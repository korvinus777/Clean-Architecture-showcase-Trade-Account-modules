<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditMtTrade;

use Platform\Trade\Domain\Entities\Symbol;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\ValueObjects\Direction;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Package\Components\Http\Request\Device;
use Platform\Package\Components\Http\Request\Context;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Services\ProcessTradeEditing\ProcessTradeEditingInput;
use Platform\Trade\Domain\Services\ProcessTradeEditing\ProcessTradeEditingRequest;

class EditMtTradeService implements EditMtTradeInput
{
    public function __construct(
        private EditMtTradeOutput        $output,
        private Logger                   $logger,
        private SymbolAdapter            $symbolAdapter,
        private AccountAdapter           $accountAdapter,
        private TradeRepository          $tradeRepository,
        private ProcessTradeEditingInput $processTradeEditingService,
    ) {
    }

    public function edit(EditMtTradeRequest $request) : EditMtTradeResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'tradeId'  => $request->tradeId,
        ]);

        $trade = $this->tradeRepository->find($request->tradeId);

        if ( ! $trade) {
            $this->logger->critical('trade not found', ['reason' => 'finding by requested id']);

            return $this->output->failed();
        }

        $account = $this->accountAdapter->findAccount($trade->accountId);

        if ( ! $account) {
            $this->logger->error('account not found', ['reason' => 'finding by trade account id']);

            return $this->output->failed();
        }

        $symbol = $request->symbol
            ? $this->symbolAdapter->findByName($request->symbol)
            : $this->symbolAdapter->getSymbol($trade->symbolId);

        if ( ! $symbol) {
            $this->logger->critical('symbol not found', [
                'symbolName' => $request->symbol,
                'symbolId'   => $trade->symbolId,
                'reason'     => 'finding by name when passed or trade symbol id',
            ]);

            return $this->output->failed();
        }

        $lots = $lotsLeft = $amount = $amountLeft = null;

        $contractSize = $request->contractSize ?? $trade->contractSize;

        // lots changed
        if ($request->lots) {
            [$lots, $amount] = $this->lots($request->lots, $symbol, $contractSize);
        }

        // lots left changed
        if ($request->lotsLeft) {
            [$lotsLeft, $amountLeft] = $this->lots($request->lotsLeft, $symbol, $contractSize);
        }

        // contract size changed
        if ($request->contractSize) {
            [$lots, $amount] = $this->lots($request->lots ?? $trade->lots, $symbol, $contractSize);
            [$lotsLeft, $amountLeft] = $this->lots($request->lotsLeft ?? $trade->lotsLeft, $symbol, $contractSize);
        }

        $context = new Context(Device::MT_DESKTOP, '', '', '');

        $result = $this->processTradeEditingService->process(new ProcessTradeEditingRequest(
            $trade,
            $account,
            $context,
            $symbol->id,
            $request->contractSize,
            $this->direction($request->isBuy),
            $lots,
            $amount,
            $lotsLeft,
            $amountLeft,
            $request->openPrice,
            $request->openTime,
            $request->swap,
            $request->stopLoss,
            $request->takeProfit,
            $symbol->profitCurrencyId,
        ));

        return $result->trade ? $this->output->edited($result->trade) : $this->output->failed();
    }

    private function direction(?bool $isBuy) : ?Direction
    {
        if ($isBuy === null) {
            return null;
        }

        return $isBuy ? Direction::BUY : Direction::SELL;
    }

    /**
     * @param float $lots
     * @param Symbol $symbol
     * @param float $contractSize
     * @return array{float, float}
     */
    private function lots(float $lots, Symbol $symbol, float $contractSize) : array
    {
        return [$lots, $symbol->amountFromLots($lots, $contractSize)];
    }
}
