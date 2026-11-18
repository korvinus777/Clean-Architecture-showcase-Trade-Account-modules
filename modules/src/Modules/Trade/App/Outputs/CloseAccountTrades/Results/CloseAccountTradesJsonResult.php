<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\CloseAccountTrades\Results;

use OpenApi\Attributes as OA;
use Platform\Package\Components\Http\Response\Code;
use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;
use Platform\Trade\App\Outputs\CloseTrade\Results\CloseTradeJsonResult;
use Platform\Trade\App\Services\CloseAccountTrades\CloseAccountTradesResult;

class CloseAccountTradesJsonResult extends CloseAccountTradesResult
{
    use HttpJsonResult;

    #[OA\Schema(
        schema: 'ErrorMassiveCloseTrade',
        properties: [
            new OA\Property('error', type: 'string', example: 'TRADE_MARKET_CLOSED'),
            new OA\Property('code', type: 'int', example: 16003),
            new OA\Property('message', type: 'int', example: 'Market of {symbol} is closed'),
            new OA\Property('messageBindings', type: 'object', example: ['symbol' => 'EURUSD']),
        ],
    )]
    public function getData() : array
    {
        $results = [];

        /**
         * @var int $tradeId
         * @var CloseTradeJsonResult $result
         */
        foreach ($this->results as $tradeId => $result) {
            if ( ! $this->isErrorCode($result->code)) {
                continue;
            }

            $results[$tradeId] = [
                'error'           => $result->code->name,
                'code'            => $result->code->value,
                'message'         => $result->message ?: $result->code->message(),
                'messageBindings' => $result->bindings,
            ];
        }

        return $results;
    }

    private function isErrorCode(Code $code) : bool
    {
        return ! $code->isOk() && $code !== Code::TRADE_SENT_ON_MT_ASYNC;
    }
}
