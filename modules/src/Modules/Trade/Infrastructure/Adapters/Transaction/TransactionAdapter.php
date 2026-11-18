<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Adapters\Transaction;

use Platform\Transaction\Api\Api;
use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Trade\Domain\Dto\TransactionDto;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Trade\Domain\Dto\TransactionInfoDto;
use Platform\Trade\Domain\Dto\TransactionResponseDto;
use Platform\Transaction\Api\Dto\TransactionDto as ExternalTransactionDto;
use Platform\Trade\Domain\Adapters\TransactionAdapter as ITransactionAdapter;

class TransactionAdapter implements ITransactionAdapter
{
    public function __construct(
        private Api $api,
    ) {
    }

    public function openTrade(TransactionDto $transaction) : TransactionResponseDto
    {
        $idDto = $this->api->openTrade(
            $transaction->accountId,
            $transaction->entityId,
            $transaction->leadId,
            $transaction->currencyId,
            $transaction->amount,
            $transaction->groupId,
            $transaction->mt5TransactionId,
            $transaction->mt5TransactionTime,
        );

        return new TransactionResponseDto(
            success: $idDto->success,
            id: $idDto->id,
            isDuplicate: $idDto->isDuplicate,
        );
    }

    public function closeTrade(
        Trade          $trade,
        ClosedTrade    $closedTrade,
        TransactionDto $transaction,
        Symbol         $symbol,
    ) : TransactionResponseDto {
        $idDto = $this->api->closeTrade(
            tradeId: $trade->id,
            closeId: $closedTrade->id,
            accountId: $trade->accountId,
            leadId: $trade->leadId,
            symbolId: $symbol->id,
            currencyId: $transaction->currencyId,
            groupId: $transaction->groupId,
            mt5PositionId: $transaction->mtPositionId,
            mt5TransactionId: $transaction->mt5TransactionId,
            mt5TransactionTime: $transaction->mt5TransactionTime,
            isBuy: $trade->direction->isBuy(),
            lots: $closedTrade->lots,
            openPrice: $trade->openPrice,
            closePrice: $closedTrade->price,
            stopLoss: $trade->stopLoss,
            takeProfit: $trade->takeProfit,
            swap: $closedTrade->swap,
            grossPnl: $closedTrade->pnl,
            symbolName: $symbol->displayName(),
            openTime: $trade->openTime,
            isPartial: ! $trade->isClosed,
            closeReason: $closedTrade->reason->value,
            turnover: $closedTrade->turnover,
            turnoverSystem: $closedTrade->turnoverSystem,
        );

        return new TransactionResponseDto(
            success: $idDto->success,
            id: $idDto->id,
            isDuplicate: $idDto->isDuplicate,
        );
    }

    public function openTradeCommission(
        int       $accountId,
        int       $tradeId,
        int       $leadId,
        int       $currencyId,
        int       $groupId,
        float     $amount,
        ?DateTime $createdAt = null,
    ) : bool {
        $id = $this->api->openTradeCommission(
            $accountId,
            $tradeId,
            $leadId,
            $currencyId,
            $groupId,
            $amount,
            $createdAt,
        );

        return $id !== null;
    }

    public function closeTradeCommission(
        int       $accountId,
        int       $tradeId,
        int       $closeId,
        int       $leadId,
        int       $currencyId,
        int       $groupId,
        float     $amount,
        ?DateTime $createdAt = null,
    ) : bool {
        $id = $this->api->closeTradeCommission(
            $accountId,
            $tradeId,
            $closeId,
            $leadId,
            $currencyId,
            $groupId,
            $amount,
            $createdAt,
        );

        return $id !== null;
    }

    public function cancelCloseCommission(int $tradeId, int $closeId) : void
    {
        $this->api->cancelCloseCommission($tradeId, $closeId);
    }

    public function cancelOpenCommission(int $tradeId) : void
    {
        $this->api->cancelOpenCommission($tradeId);
    }

    public function cancelClose(int $closeId) : bool
    {
        return $this->api->cancelClosing($closeId);
    }

    public function cancelOpen(int $tradeId) : TransactionInfoDto
    {
        $transaction = $this->api->cancelOpening($tradeId);

        return $this->makeTransactionInfoDto($transaction);
    }

    public function findClosedTrade(int $closeId) : ?TransactionInfoDto
    {
        if ($transactionDto = $this->api->findClosedTrade($closeId)) {
            return $this->makeTransactionInfoDto($transactionDto);
        }

        return null;
    }

    private function makeTransactionInfoDto(ExternalTransactionDto $transactionDto) : TransactionInfoDto
    {
        return new TransactionInfoDto(
            id: $transactionDto->transactionId,
            accountId: $transactionDto->accountId,
            entityId: $transactionDto->entityId,
            leadId: $transactionDto->leadId,
            mt5TransactionId: $transactionDto->mt5TransactionId,
            amount: $transactionDto->amount,
            amountSystem: $transactionDto->amountSystem,
            createdTimestamp: $transactionDto->createdTimestamp,
        );
    }
}
