<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Adapters\NativeTradeEngine;

use Platform\NativeTradeEngine\Api\Api;
use Platform\Symbol\Api\Api as SymbolApi;
use Platform\Trade\Domain\Dto\NTOrderDto;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Trade\Domain\Dto\NTPositionDto;
use Platform\Trade\Domain\Dto\NTCloseTransactionDto;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Adapters\NativeTradeEngineAdapter as INativeTradeEngineAdapter;

class NativeTradeEngineAdapter implements INativeTradeEngineAdapter
{
    public function __construct(
        private Api       $api,
        private Logger    $logger,
        private SymbolApi $symbolApi,
    ) {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
        ]);
    }

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
    ) : NTOrderDto {
        $order = $this->api->openPendingOrder(
            $orderId,
            $login,
            $symbolId,
            $price,
            $lots,
            $digits,
            $direction,
            $stopLoss,
            $takeProfit,
            $timestampMs,
        );

        $resultCode = NTResultCode::tryFrom($order->resultCode);

        if ( ! $resultCode) {
            $this->logger->critical('unexpected result code', [
                'reason'  => 'when opening nt order',
                'code'    => $order->resultCode,
                'orderId' => $order->orderId,
                'login'   => $order->login,
            ]);

            $resultCode = NTResultCode::ERROR;
        }

        return new NTOrderDto(
            $resultCode,
            $order->orderId,
            $order->orderId,
            $order->symbol,
            $order->volumeInitialExt,
            $order->type,
            $order->orderPrice,
            $order->orderTrigger,
            $order->priceSl,
            $order->priceTp,
            $order->timestampMs,
        );
    }

    public function modifyPendingOrder(
        int    $orderId,
        int    $login,
        int    $symbolId,
        ?float $price,
        ?float $stopLoss,
        ?float $takeProfit,
    ) : NTOrderDto {
        $order = $this->api->modifyPendingOrder(
            $orderId,
            $login,
            $symbolId,
            $price,
            $stopLoss,
            $takeProfit,
        );

        $resultCode = NTResultCode::tryFrom($order->resultCode);

        if ( ! $resultCode) {
            $this->logger->alert('unexpected result code, when modifying nt pending order', [
                'code' => $order->resultCode,
            ]);

            $resultCode = NTResultCode::ERROR;
        }

        return new NTOrderDto(
            $resultCode,
            $order->orderId,
            $order->orderId,
            $order->symbol,
            $order->volumeInitialExt,
            $order->type,
            $order->orderPrice,
            $order->orderTrigger,
            $order->priceSl,
            $order->priceTp,
            $order->timestampMs,
        );
    }

    public function closePendingOrder(int $orderId, int $login) : NTOrderDto
    {
        $order = $this->api->closePendingOrder($orderId, $login);

        $resultCode = NTResultCode::tryFrom($order->resultCode);

        if ( ! $resultCode) {
            $this->logger->alert('unexpected result code, when closing mt pending order', [
                'code' => $order->resultCode,
            ]);

            $resultCode = NTResultCode::ERROR;
        }

        return new NTOrderDto(
            $resultCode,
            $order->orderId,
            $order->orderId,
            $order->symbol,
            $order->volumeInitialExt,
            $order->type,
            $order->orderPrice,
            $order->orderTrigger,
            $order->priceSl,
            $order->priceTp,
            $order->timestampMs,
        );
    }

    public function openTrade(
        int $tradeId,
        int $login,
        int $symbolId,
        float $volume,
        bool $isBuy,
        int $digits,
        float $stopLoss,
        float $takeProfit,
        int   $timestampMs = 0,
    ) : NTPositionDto {
        $position = $this->api->openPosition(
            $tradeId,
            $login,
            $symbolId,
            $volume,
            $isBuy,
            $digits,
            $stopLoss,
            $takeProfit,
            $timestampMs,
        );

        $resultCode = NTResultCode::tryFrom($position->resultCode);

        if ( ! $resultCode) {
            $this->logger->critical('unexpected result code', [
                'reason'  => 'when opening nt position',
                'code'    => $position->resultCode,
                'tradeId' => $tradeId,
                'login' => $login,
                'symbolId' => $symbolId,
                'volume' => $volume,
                'isBuy' => $isBuy,
                'digits' => $digits,
                'stopLoss' => $stopLoss,
                'takeProfit' => $takeProfit,
            ]);

            $resultCode = NTResultCode::ERROR;
        }

        return new NTPositionDto(
            $resultCode,
            $position->login,
            $position->symbolId,
            $position->volume,
            $position->transactionId,
            $position->positionId,
            $position->price,
            $position->stopLoss,
            $position->takeProfit,
            $position->time,
            $position->transactionTime,
            $position->commission,
            $position->swap,
            $position->accountMargin,
        );
    }

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
    ) : NTPositionDto {
        $symbol = $this->symbolApi->getSymbol($symbolId);

        $position = $this->api->modifyPosition(
            $tradeId,
            $login,
            $symbolId,
            $isBuy,
            $openDate,
            $openPrice,
            $volume,
            $swap,
            $stopLoss,
            $takeProfit,
            $symbol->precision,
        );

        $resultCode = NTResultCode::tryFrom($position->resultCode);

        if ( ! $resultCode) {
            $this->logger->critical('unexpected result code', [
                'reason'  => 'when modifying nt position',
                'code'    => $resultCode,
                'tradeId' => $tradeId,
                'login'   => $login,
            ]);


            $resultCode = NTResultCode::ERROR;
        }

        return new NTPositionDto(
            $resultCode,
            $position->login,
            $position->symbolId,
            $position->volume,
            $position->transactionId,
            $position->positionId,
            $position->price,
            $position->stopLoss,
            $position->takeProfit,
            $position->time,
            $position->transactionTime,
            $position->commission,
            $position->swap,
            $position->accountMargin,
        );
    }

    public function closeTrade(int $tradeId, int $login) : NTCloseTransactionDto
    {
        $position = $this->api->closePosition($tradeId, $login);

        $resultCode = NTResultCode::tryFrom($position->resultCode);

        if ( ! $resultCode) {
            $this->logger->critical('unexpected result code', [
                'reason'  => 'when closing nt position',
                'code'    => $position->resultCode,
                'tradeId' => $tradeId,
                'login'   => $login,
            ]);

            $resultCode = NTResultCode::ERROR;
        }

        return new NTCloseTransactionDto(
            $resultCode,
            $position->login,
            $position->positionId,
            $position->volume,
            $position->price,
            $position->transactionId,
            $position->time,
            $position->transactionTime,
            $position->pnl,
            $position->swap,
            $position->commission,
            $position->accountMargin,
            $position->reason,
        );
    }

    public function reopenTrade(int $tradeId, int $login) : NTPositionDto
    {
        $position = $this->api->reopenPosition($tradeId, $login);

        $resultCode = NTResultCode::tryFrom($position->resultCode);

        if ( ! $resultCode) {
            $this->logger->critical('unexpected result code', [
                'reason'  => 'when reopening nt position',
                'code'    => $position->resultCode,
                'tradeId' => $tradeId,
                'login'   => $login,
            ]);

            $resultCode = NTResultCode::ERROR;
        }

        return new NTPositionDto(
            $resultCode,
            $position->login,
            $position->symbolId,
            $position->volume,
            $position->transactionId,
            $position->positionId,
            $position->price,
            $position->stopLoss,
            $position->takeProfit,
            $position->time,
            $position->transactionTime,
            $position->commission,
            $position->swap,
            $position->accountMargin,
        );
    }
}
