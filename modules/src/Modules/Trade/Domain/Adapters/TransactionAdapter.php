<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Adapters;

use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Trade\Domain\Dto\TransactionDto;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Trade\Domain\Dto\TransactionInfoDto;
use Platform\Trade\Domain\Dto\TransactionResponseDto;

interface TransactionAdapter
{
    public function openTrade(TransactionDto $transaction) : TransactionResponseDto;

    public function closeTrade(
        Trade          $trade,
        ClosedTrade    $closedTrade,
        TransactionDto $transaction,
        Symbol         $symbol,
    ) : TransactionResponseDto;

    public function openTradeCommission(
        int       $accountId,
        int       $tradeId,
        int       $leadId,
        int       $currencyId,
        int       $groupId,
        float     $amount,
        ?DateTime $createdAt = null,
    ) : bool;

    public function closeTradeCommission(
        int       $accountId,
        int       $tradeId,
        int       $closeId,
        int       $leadId,
        int       $currencyId,
        int       $groupId,
        float     $amount,
        ?DateTime $createdAt = null,
    ) : bool;

    public function cancelCloseCommission(int $tradeId, int $closeId) : void;

    public function cancelOpenCommission(int $tradeId) : void;

    public function cancelClose(int $closeId) : bool;

    /**
     * @param int $tradeId
     * @return TransactionInfoDto
     * @throws \Exception
     */
    public function cancelOpen(int $tradeId) : TransactionInfoDto;

    public function findClosedTrade(int $closeId) : ?TransactionInfoDto;
}
