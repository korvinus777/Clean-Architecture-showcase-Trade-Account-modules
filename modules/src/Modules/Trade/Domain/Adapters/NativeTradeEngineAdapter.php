<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Adapters;

use Platform\Trade\Domain\Dto\NTOrderDto;
use Platform\Trade\Domain\Dto\NTPositionDto;
use Platform\Trade\Domain\Dto\NTCloseTransactionDto;

interface NativeTradeEngineAdapter
{
    public function openPendingOrder(
        int   $orderId,
        int   $login,
        int   $symbolId,
        float $price,
        float $lots,
        int   $digits,
        int   $direction,
        float $stopLoss,
        float $takeProfit,
        int   $timestampMs,
    ) : NTOrderDto;

    public function modifyPendingOrder(
        int    $orderId,
        int    $login,
        int    $symbolId,
        ?float $price,
        ?float $stopLoss,
        ?float $takeProfit,
    ) : NTOrderDto;

    public function closePendingOrder(
        int    $orderId,
        int    $login,
    ) : NTOrderDto;

    public function openTrade(
        int    $tradeId,
        int    $login,
        int    $symbolId,
        float  $volume,
        bool   $isBuy,
        int    $digits,
        float  $stopLoss,
        float  $takeProfit,
        int    $timestampMs = 0,
    ) : NTPositionDto;

    public function editTrade(
        int    $tradeId,
        int    $login,
        int    $symbolId,
        bool   $isBuy,
        int    $openDate,
        float  $openPrice,
        float  $volume,
        float  $swap,
        float  $stopLoss,
        float  $takeProfit,
    ) : NTPositionDto;

    public function closeTrade(
        int    $tradeId,
        int    $login,
    ) : NTCloseTransactionDto;

    public function reopenTrade(
        int    $tradeId,
        int    $login,
    ) : NTPositionDto;
}
