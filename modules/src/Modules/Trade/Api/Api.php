<?php

declare(strict_types=1);

namespace Platform\Trade\Api;

use Platform\Trade\Api\Dto\OrderDto;
use Platform\Trade\Api\Dto\TradeDto;
use Platform\Trade\Api\Dto\IdResponseDto;
use Platform\Trade\Api\Dto\ClosedTradeDto;
use Platform\Package\Components\Time\DateTime;

interface Api
{
    public function getNewTradeId() : int;

    public function getNewPendingOrderId() : int;

    public function openTradeFromMt(
        int      $tradeId,
        string   $symbol,
        int      $login,
        bool     $isBuy,
        float    $volume,
        float    $price,
        DateTime $time,
        float    $stopLoss,
        float    $takeProfit,
        float    $swap,
        float    $commission,
        int      $mt5TransactionId,
        DateTime $mt5TransactionTime,
    ) : IdResponseDto;

    public function closeTradeFromMt(
        int      $tradeId,
        int      $mtPositionId,
        int      $login,
        float    $lots,
        float    $price,
        DateTime $time,
        float    $swap,
        float    $commission,
        float    $pnl,
        int      $mt5TransactionId,
        int      $closeReason,
    ) : IdResponseDto;

    public function closeTradeFromNative(
        int      $tradeId,
        int      $nativePositionId,
        int      $login,
        float    $lots,
        float    $price,
        DateTime $time,
        float    $swap,
        float    $commission,
        float    $pnl,
        int      $nativeTransactionId,
        int      $closeReason,
        float    $margin,
    ) : IdResponseDto;

    public function editTradeFromMt(
        int       $tradeId,
        ?string   $symbol,
        ?float    $contractSize,
        ?bool     $isBuy,
        ?float    $lots,
        ?float    $lotsLeft,
        ?float    $openPrice,
        ?DateTime $openTime,
        ?float    $swap,
        ?float    $stopLoss,
        ?float    $takeProfit,
    ) : bool;

    public function openPendingOrderFromMt(
        int      $orderId,
        string   $symbol,
        int      $login,
        float    $lots,
        float    $price,
        float    $stopLimitPrice,
        float    $stopLoss,
        float    $takeProfit,
        int      $type,
        DateTime $time,
    ) : ?int;

    public function activateOrderFromMt(
        int      $orderId,
        string   $symbol,
        bool     $isBuy,
        float    $lots,
        float    $price,
        DateTime $time,
        float    $stopLoss,
        float    $takeProfit,
        float    $swap,
        float    $commission,
        int      $mt5TransactionId,
    ) : IdResponseDto;

    public function closePendingOrderFromMt(int $orderId, DateTime $closeTime) : bool;

    public function editPendingOrderFromMt(
        int      $orderId,
        int      $type,
        float    $lots,
        DateTime $time,
        float    $price,
        float    $stopLimitPrice,
        float    $stopLoss,
        float    $takeProfit,
    ) : bool;

    public function find(int $tradeId) : ?TradeDto;

    public function findClosedTrade(int $tradeId, int $mt5TransactionId) : ?ClosedTradeDto;

    public function reopenByMtDeal(int $mt5TransactionId) : bool;

    public function deleteOpened(int $tradeId) : bool;

    public function findOrder(int $orderId) : ?OrderDto;

    /**
     * @param int $page
     * @param int $limit
     * @return TradeDto[]
     */
    public function getOpenTrades(int $page, int $limit) : array;

    public function isPendingOrderType(int $type) : bool;
}
