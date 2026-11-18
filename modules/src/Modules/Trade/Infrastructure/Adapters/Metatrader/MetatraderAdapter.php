<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Adapters\Metatrader;

use Platform\Metatrader\Api\Api as MtApi;
use Platform\Symbol\Api\Api as SymbolApi;
use Platform\Trade\Domain\Dto\MTOrderDto;
use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;
use Platform\Trade\Domain\Adapters\MetatraderAdapter as IMetatraderAdapter;

class MetatraderAdapter implements IMetatraderAdapter
{
    public function __construct(
        private MtApi     $mtApi,
        private Logger    $logger,
        private SymbolApi $symbolApi,
    ) {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
        ]);
    }

    public function openTrade(
        int    $tradeId,
        int    $login,
        string $symbol,
        float  $volume,
        bool   $isBuy,
        int    $digits,
        float  $stopLoss,
        float  $takeProfit,
    ) : MTOrderDto {
        $order = $this->mtApi->openPosition(
            $tradeId,
            $login,
            $symbol,
            $volume,
            $isBuy,
            $digits,
            $stopLoss,
            $takeProfit,
        );

        $resultCode = MTResultCode::tryFrom($order->resultCode);

        if ( ! $resultCode) {
            $this->logger->critical('unexpected result code', [
                'reason'  => 'when opening mt position',
                'code'    => $order->resultCode,
                'orderId' => $order->orderId,
                'dealId'  => $order->dealId,
            ]);

            $resultCode = MTResultCode::ERROR;
        }

        return new MTOrderDto(
            $resultCode,
            $order->orderId,
            $order->dealId,
            $order->price,
        );
    }

    public function closeTrade(
        int    $tradeId,
        int    $login,
        string $symbol,
        float  $lots,
        bool   $isBuy,
    ) : MTOrderDto {
        $order = $this->mtApi->closePosition(
            $tradeId,
            $login,
            $symbol,
            $lots,
            $isBuy,
        );

        $resultCode = MTResultCode::tryFrom($order->resultCode);

        if ( ! $resultCode) {
            $this->logger->critical('unexpected result code', [
                'reason'  => 'when closing mt position',
                'code'    => $order->resultCode,
                'orderId' => $order->orderId,
                'dealId'  => $order->dealId,
            ]);

            $resultCode = MTResultCode::ERROR;
        }

        return new MTOrderDto(
            $resultCode,
            $order->orderId,
            $order->dealId,
            $order->price,
        );
    }

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
    ) : MTOrderDto {
        $order = $this->mtApi->openPendingOrder(
            $orderId,
            $login,
            $symbol,
            $orderPrice,
            $askPrice,
            $bidPrice,
            $volume,
            $isBuy,
            $digits,
            $stopLoss,
            $takeProfit,
        );

        $resultCode = MTResultCode::tryFrom($order->resultCode);

        if ( ! $resultCode) {
            $this->logger->critical('unexpected result code', [
                'reason'  => 'when opening mt order',
                'code'    => $order->resultCode,
                'orderId' => $order->orderId,
                'dealId'  => $order->dealId,
            ]);

            $resultCode = MTResultCode::ERROR;
        }

        return new MTOrderDto(
            $resultCode,
            $order->orderId,
            0,
            $order->price,
        );
    }

    public function editTrade(
        int    $tradeId,
        int    $login,
        int    $symbolId,
        float  $stopLoss,
        float  $takeProfit,
    ) : MTResultCode {
        $symbol = $this->symbolApi->getSymbol($symbolId);

        $codeValue = $this->mtApi->modifyPosition(
            $tradeId,
            $login,
            $symbol->name,
            $stopLoss,
            $takeProfit,
            $symbol->precision,
        );

        $resultCode = MTResultCode::tryFrom($codeValue);

        if ( ! $resultCode) {
            $this->logger->critical('unexpected result code', [
                'reason'  => 'when modifying mt position',
                'code'    => $resultCode,
            ]);


            $resultCode = MTResultCode::ERROR;
        }

        return $resultCode;
    }

    public function closePendingOrder(
        int              $login,
        string           $symbol,
        int              $platformOrderId,
        PendingOrderType $type,
    ) : MTResultCode {
        $codeValue = $this->mtApi->closePendingOrder(
            $login,
            $symbol,
            $platformOrderId,
            $type->value,
        );

        $resultCode = MTResultCode::tryFrom($codeValue);

        if ( ! $resultCode) {
            $this->logger->alert('unexpected result code, when closing mt pending order', [
                'code'    => $codeValue,
            ]);

            $resultCode = MTResultCode::ERROR;
        }

        return $resultCode;
    }

    public function editPendingOrder(
        int              $platformOrderId,
        int              $login,
        PendingOrderType $type,
        int              $symbolId,
        ?float           $priceOrder,
        ?float           $stopLoss,
        ?float           $takeProfit,
    ) : MTResultCode {
        $symbol = $this->symbolApi->getSymbol($symbolId);

        $codeValue = $this->mtApi->modifyPendingOrder(
            $platformOrderId,
            $login,
            $type->value,
            $symbol->name,
            $symbol->precision,
            $priceOrder,
            $stopLoss,
            $takeProfit,
        );

        $resultCode = MTResultCode::tryFrom($codeValue);

        if ( ! $resultCode) {
            $this->logger->alert('unexpected result code, when edit mt pending order', [
                'code'    => $codeValue,
            ]);

            $resultCode = MTResultCode::ERROR;
        }

        return $resultCode;
    }

    public function deferredCloseTrade(
        int    $tradeId,
        int    $login,
        string $symbol,
        float  $lots,
        bool   $isBuy,
    ) : void {
        $this->mtApi->deferredClosePosition(
            $tradeId,
            $login,
            $symbol,
            $lots,
            $isBuy,
        );
    }

    public function deferredClosePendingOrder(
        int              $login,
        string           $symbol,
        int              $platformOrderId,
        PendingOrderType $type,
    ) : void {
        $this->mtApi->deferredClosePendingOrder(
            $login,
            $symbol,
            $platformOrderId,
            $type->value,
        );
    }
}
