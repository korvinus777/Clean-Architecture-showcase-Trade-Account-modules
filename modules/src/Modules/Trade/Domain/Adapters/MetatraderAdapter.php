<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Adapters;

use Platform\Trade\Domain\Dto\MTOrderDto;
use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;

interface MetatraderAdapter
{
    public function openTrade(
        int    $tradeId,
        int    $login,
        string $symbol,
        float  $volume,
        bool   $isBuy,
        int    $digits,
        float  $stopLoss,
        float  $takeProfit,
    ) : MTOrderDto;

    public function closeTrade(
        int    $tradeId,
        int    $login,
        string $symbol,
        float  $lots,
        bool   $isBuy,
    ) : MTOrderDto;

    public function openPendingOrder(
        int    $orderId,
        int    $login,
        string $symbol,
        float  $orderPrice,
        float  $askPrice,
        float  $bidPrice,
        float  $volume,
        bool   $isBuy,
        int    $digits,
        float  $stopLoss,
        float  $takeProfit,
    ) : MTOrderDto;

    public function editTrade(
        int    $tradeId,
        int    $login,
        int    $symbolId,
        float  $stopLoss,
        float  $takeProfit,
    ) : MTResultCode;

    public function closePendingOrder(
        int              $login,
        string           $symbol,
        int              $platformOrderId,
        PendingOrderType $type,
    ) : MTResultCode;

    public function editPendingOrder(
        int              $platformOrderId,
        int              $login,
        PendingOrderType $type,
        int              $symbolId,
        ?float           $priceOrder,
        ?float           $stopLoss,
        ?float           $takeProfit,
    ) : MTResultCode;

    public function deferredCloseTrade(
        int    $tradeId,
        int    $login,
        string $symbol,
        float  $lots,
        bool   $isBuy,
    ) : void;

    public function deferredClosePendingOrder(
        int              $login,
        string           $symbol,
        int              $platformOrderId,
        PendingOrderType $type,
    ) : void;
}
