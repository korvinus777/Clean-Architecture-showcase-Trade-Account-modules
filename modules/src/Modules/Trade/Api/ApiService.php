<?php

declare(strict_types=1);

namespace Platform\Trade\Api;

use Platform\Trade\Api\Dto\OrderDto;
use Platform\Trade\Api\Dto\TradeDto;
use Platform\Trade\Api\Dto\IdResponseDto;
use Platform\Trade\Api\Dto\ClosedTradeDto;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\Direction;
use Platform\Trade\Domain\ValueObjects\CloseReason;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;
use Platform\Trade\App\Services\EditMtTrade\EditMtTradeInput;
use Platform\Trade\App\Services\OpenMtTrade\OpenMtTradeInput;
use Platform\Trade\Domain\Repositories\ClosedTradeRepository;
use Platform\Package\Components\Container\Contracts\Container;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Trade\App\Services\CloseMtTrade\CloseMtTradeInput;
use Platform\Trade\App\Services\EditMtTrade\EditMtTradeRequest;
use Platform\Trade\App\Services\OpenMtTrade\OpenMtTradeRequest;
use Platform\Trade\App\Services\CloseMtTrade\CloseMtTradeRequest;
use Platform\Trade\App\Services\ReopenMtTrade\ReopenMtTradeInput;
use Platform\Trade\App\Services\ReopenMtTrade\ReopenMtTradeRequest;
use Platform\Trade\App\Services\ActivateMtOrder\ActivateMtOrderInput;
use Platform\Trade\App\Services\ActivateMtOrder\ActivateMtOrderRequest;
use Platform\Trade\App\Services\CloseNativeTrade\CloseNativeTradeInput;
use Platform\Trade\App\Services\CloseNativeTrade\CloseNativeTradeRequest;
use Platform\Trade\App\Services\DeleteOpenedTrade\DeleteOpenedTradeInput;
use Platform\Trade\App\Services\DeleteOpenedTrade\DeleteOpenedTradeRequest;
use Platform\Trade\App\Services\EditMtPendingOrder\EditMtPendingOrderInput;
use Platform\Trade\App\Services\OpenMtPendingOrder\OpenMtPendingOrderInput;
use Platform\Trade\App\Services\CloseMtPendingOrder\CloseMtPendingOrderInput;
use Platform\Trade\App\Services\EditMtPendingOrder\EditMtPendingOrderRequest;
use Platform\Trade\App\Services\OpenMtPendingOrder\OpenMtPendingOrderRequest;
use Platform\Trade\App\Services\CloseMtPendingOrder\CloseMtPendingOrderRequest;

class ApiService implements Api
{
    public function __construct(
        private Container              $container,
        private TradeRepository        $tradeRepository,
        private PendingOrderRepository $orderRepository,
        private ClosedTradeRepository  $closedTradeRepository,
    ) {
    }

    public function getNewTradeId() : int
    {
        return $this->tradeRepository->getNewId();
    }

    public function getNewPendingOrderId() : int
    {
        return $this->orderRepository->getNewId();
    }

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
    ) : IdResponseDto {
        $result = $this->container->make(OpenMtTradeInput::class)->open(new OpenMtTradeRequest(
            $tradeId,
            $symbol,
            $login,
            $isBuy ? Direction::BUY : Direction::SELL,
            $volume,
            $price,
            $time,
            $stopLoss,
            $takeProfit,
            $swap,
            $commission,
            $mt5TransactionId,
            $mt5TransactionTime,
        ));

        return new IdResponseDto(
            id: $result->trade?->id,
            alreadyProcessed: $result->alreadyProcessed,
        );
    }

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
    ) : IdResponseDto {
        $result = $this->container->make(CloseMtTradeInput::class)->close(new CloseMtTradeRequest(
            $tradeId,
            $login,
            $lots,
            $price,
            $time,
            $swap,
            $commission,
            $pnl,
            CloseReason::fromDealReason($closeReason),
            $mt5TransactionId,
            $mtPositionId,
        ));

        return new IdResponseDto(
            id: $result->closedTrade?->id,
            alreadyProcessed: $result->alreadyProcessed,
        );
    }

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
    ) : IdResponseDto {
        $result = $this->container->make(CloseNativeTradeInput::class)->close(new CloseNativeTradeRequest(
            $tradeId,
            $login,
            $lots,
            $price,
            $time,
            $swap,
            $commission,
            $pnl,
            CloseReason::fromNativeEngineReason($closeReason),
            $nativeTransactionId,
            $nativePositionId,
            $margin,
        ));

        return new IdResponseDto(
            id: $result->closedTrade?->id,
            alreadyProcessed: $result->alreadyProcessed,
        );
    }

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
    ) : bool {
        $result = $this->container->singleton(EditMtTradeInput::class)->edit(new EditMtTradeRequest(
            $tradeId,
            $symbol,
            $contractSize,
            $isBuy,
            $lots,
            $lotsLeft,
            $openPrice,
            $openTime,
            $swap,
            $stopLoss,
            $takeProfit,
        ));

        return $result->trade !== null;
    }

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
    ) : ?int {
        $result = $this->container->make(OpenMtPendingOrderInput::class)
            ->open(
                new OpenMtPendingOrderRequest(
                    $orderId,
                    $symbol,
                    $login,
                    $lots,
                    $price,
                    $stopLimitPrice,
                    $stopLoss,
                    $takeProfit,
                    PendingOrderType::from($type),
                    $time,
                ),
            );

        return $result->order?->id;
    }

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
    ) : IdResponseDto {
        $result = $this->container->make(ActivateMtOrderInput::class)->activate(new ActivateMtOrderRequest(
            $orderId,
            $symbol,
            $isBuy ? Direction::BUY : Direction::SELL,
            $lots,
            $price,
            $time,
            $stopLoss,
            $takeProfit,
            $swap,
            $commission,
            $mt5TransactionId,
        ));

        return new IdResponseDto(
            id: $result->trade?->id,
            alreadyProcessed: $result->alreadyProcessed,
        );
    }

    public function closePendingOrderFromMt(int $orderId, DateTime $closeTime) : bool
    {
        $result = $this->container->make(CloseMtPendingOrderInput::class)->close(
            new CloseMtPendingOrderRequest($orderId, $closeTime),
        );

        return $result->order !== null;
    }

    public function editPendingOrderFromMt(
        int      $orderId,
        int      $type,
        float    $lots,
        DateTime $time,
        float    $price,
        float    $stopLimitPrice,
        float    $stopLoss,
        float    $takeProfit,
    ) : bool {
        $result = $this->container->make(EditMtPendingOrderInput::class)->edit(
            new EditMtPendingOrderRequest(
                $orderId,
                $type,
                $lots,
                $time,
                $price,
                $stopLimitPrice,
                $stopLoss,
                $takeProfit,
            ),
        );

        return $result->order !== null;
    }

    public function find(int $tradeId) : ?TradeDto
    {
        $trade = $this->tradeRepository->find($tradeId);

        return $trade ? TradeDto::fromEntity($trade) : null;
    }

    public function findClosedTrade(int $tradeId, int $mt5TransactionId) : ?ClosedTradeDto
    {
        $closedTrade = $this->closedTradeRepository->findByTradeAndMtDeal($tradeId, $mt5TransactionId);

        return $closedTrade ? ClosedTradeDto::fromEntity($closedTrade) : null;
    }

    public function reopenByMtDeal(int $mt5TransactionId) : bool
    {
        return $this->container->make(ReopenMtTradeInput::class)->reopen(new ReopenMtTradeRequest($mt5TransactionId));
    }

    public function deleteOpened(int $tradeId) : bool
    {
        $result = $this->container->make(DeleteOpenedTradeInput::class)
            ->delete(new DeleteOpenedTradeRequest($tradeId));

        return $result->deleted;
    }

    public function findOrder(int $orderId) : ?OrderDto
    {
        $order = $this->orderRepository->find($orderId);

        return $order ? OrderDto::fromEntity($order) : null;
    }

    /** @inheritDoc */
    public function getOpenTrades(int $page, int $limit) : array
    {
        $trades = $this->tradeRepository->getOpened($page, $limit);

        return array_map([TradeDto::class, 'fromEntity'], $trades);
    }

    public function isPendingOrderType(int $type) : bool
    {
        return (bool) PendingOrderType::tryFrom($type);
    }
}
