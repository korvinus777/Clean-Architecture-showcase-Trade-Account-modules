<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\CrmReopenTrade;

use Throwable;
use Platform\Package\Models\SettingModel;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Package\Components\Http\Response\Code;
use Platform\Package\Components\Http\Request\Device;
use Platform\Package\Components\Http\Request\Context;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Adapters\NativeTradeEngineAdapter;
use Platform\Trade\Domain\Repositories\ClosedTradeRepository;
use Platform\Package\Components\Database\Contracts\Transaction;
use Platform\Package\Components\System\ValueObjects\EngineType;
use Platform\Trade\Domain\Services\ReopenTrade\ReopenTradeInput;
use Platform\Trade\Domain\Services\ReopenTrade\ReopenTradeResult;
use Platform\Trade\Domain\Services\ReopenTrade\ReopenTradeRequest;
use Platform\Package\Components\System\Repositories\SettingRepository;
use Platform\Package\Components\System\Services\SettingResolver\Params;

class CrmReopenTradeService implements CrmReopenTradeInput
{
    /**
     * @param CrmReopenTradeOutput $output
     * @param Logger $logger
     * @param Transaction $transaction
     * @param SymbolAdapter $symbolAdapter
     * @param AccountAdapter $accountAdapter
     * @param TradeRepository $tradeRepository
     * @param SettingRepository $settingRepository
     * @param ClosedTradeRepository $closedTradeRepository
     * @param NativeTradeEngineAdapter $nativeTradeEngineAdapter
     * @param ReopenTradeInput $reopenTradeService
     */
    public function __construct(
        private CrmReopenTradeOutput $output,
        private Logger $logger,
        private Transaction $transaction,
        private SymbolAdapter $symbolAdapter,
        private AccountAdapter $accountAdapter,
        private TradeRepository $tradeRepository,
        private SettingRepository $settingRepository,
        private ClosedTradeRepository $closedTradeRepository,
        private NativeTradeEngineAdapter $nativeTradeEngineAdapter,
        private ReopenTradeInput $reopenTradeService,
    ) {

    }

    /**
     * @param CrmReopenTradeRequest $request
     * @return CrmReopenTradeResult
     */
    public function reopen(CrmReopenTradeRequest $request) : CrmReopenTradeResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'tradeId'  => $request->tradeId,
        ]);

        $account = $this->accountAdapter->findAccount($request->accountId);

        if ( ! $account) {
            return $this->output->accountNotFound();
        }

        if ( ! ($account->isEnabled && $account->isTradable)) {
            return $this->output->accountNotTradable();
        }

        $trade = $this->tradeRepository->find($request->tradeId);
        $closedTrade = $this->closedTradeRepository->findByTrade($trade->id);

        if ( ! $trade || $trade->leadId !== $account->leadId || ! $trade->isClosed || ! $closedTrade) {
            return $this->output->tradeNotFound();
        }

        $symbol = $this->symbolAdapter->findForAccount($trade->symbolId, $account->id);

        if ( ! $symbol) {
            return $this->output->symbolUnavailable();
        }

        if ($symbol->isDisabled()) {
            return $this->output->symbolDisabled();
        }

        $engineType = $this->settingRepository->findOne('Engine Type', new Params());

        if ( ! $engineType) {
            return $this->output->engineTypeNotFound(Code::TRADE_ENGINE_TYPE_NOT_FOUND);
        }

        return $this->processByEngine($engineType, $account, $trade, $closedTrade);
    }

    /**
     * @param SettingModel $engineType
     * @param AccountDto $account
     * @param Trade $trade
     * @param ClosedTrade $closedTrade
     * @return CrmReopenTradeResult
     */
    protected function processByEngine(SettingModel $engineType, AccountDto $account, Trade $trade, ClosedTrade $closedTrade) : CrmReopenTradeResult
    {
        return match ($engineType->value()) {
            EngineType::MT->value                   => $this->reopenOnMt($account, $trade, $closedTrade),
            EngineType::NATIVE_TRADE_ENGINE->value  => $this->reopenOnNt($account, $trade, $closedTrade),
            default                                 => $this->output->engineTypeNotFound(Code::TRADE_ENGINE_TYPE_NOT_FOUND),
        };
    }

    /**
     * @param AccountDto $account
     * @param Trade $trade
     * @param ClosedTrade $closedTrade
     * @return CrmReopenTradeResult
     */
    private function reopenOnMt(AccountDto $account, Trade $trade, ClosedTrade $closedTrade) : CrmReopenTradeResult
    {
        return $this->output->cannotReopenOnMt(MTResultCode::ERROR);
    }

    /**
     * @param AccountDto $account
     * @param Trade $trade
     * @param ClosedTrade $closedTrade
     * @return CrmReopenTradeResult
     */
    private function reopenOnNt(AccountDto $account, Trade $trade, ClosedTrade $closedTrade) : CrmReopenTradeResult
    {
        try {
            return $this->transaction->forMain(
                function () use ($account, $trade, $closedTrade) : CrmReopenTradeResult {
                    $result = $this->nativeTradeEngineAdapter->reopenTrade(
                        $trade->id,
                        $account->login,
                    );

                    if ($result->resultCode->isError()) {
                        return $this->output->cannotReopenOnNt($result->resultCode);
                    }

                    if ($result->accountMargin !== null) {
                        $this->accountAdapter->updateMargin($account->login, $result->accountMargin);
                    }

                    $response = $this->processTradeReopen($closedTrade);

                    if ( ! $response->reopened) {
                        return $this->output->cannotProcessReopen();
                    }

                    return $this->output->reopened($trade->id);
                },
            );
        } catch (Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'request' => ['tradeId' => $trade->id],
                'error'   => Arr::formatThrowable($throwable),
            ]);

            return $this->output->error();
        }
    }

    /**
     * @param ClosedTrade $closedTrade
     * @return ReopenTradeResult
     */
    protected function processTradeReopen(ClosedTrade $closedTrade) : ReopenTradeResult
    {
        return $this->reopenTradeService->reopen(
            new ReopenTradeRequest(
                $closedTrade,
                new Context(Device::CRM_AGENT, '', '', ''),
            ),
        );
    }
}
