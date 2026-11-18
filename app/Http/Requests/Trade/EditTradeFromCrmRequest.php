<?php

declare(strict_types=1);

namespace App\Http\Requests\Trade;

use Illuminate\Validation\Rule;
use Platform\Trade\Domain\ValueObjects\Direction;
use Platform\Package\Components\Http\Request\Context;
use Platform\Package\Components\Http\Request\FormValidator;
use Platform\Trade\App\Services\EditTrade\EditTradeRequest;

class EditTradeFromCrmRequest extends FormValidator
{
    public function rules() : array
    {
        return [
            'accountId' => ['required', 'numeric', 'min:1'],
            'tradeId' => ['required', 'numeric', 'min:1'],
            'stop_loss' => ['numeric', 'min:0'],
            'take_profit' => ['numeric', 'min:0'],
            'direction' => [Rule::in(array_map(fn (Direction $dir) => $dir->value, Direction::cases()))],
            'open_date' => [Rule::date()->format('Y-m-d H:i:s')],
            'open_price' => ['numeric'],
            'volume' => ['numeric'],
            'swap' => ['numeric'],
         ];
    }

    public function domainRequest() : EditTradeRequest
    {
        return new EditTradeRequest(
            tradeId: $this->getInt('tradeId'),
            accountId: $this->getInt('accountId'),
            context: Context::fromCrmRequest(),
            stopLoss: $this->getNullableFloat('stop_loss'),
            takeProfit: $this->getNullableFloat('take_profit'),
            direction: $this->getNullableInt('direction'),
            openDate: $this->getNullableString('open_date'),
            openPrice: $this->getNullableFloat('open_price'),
            volume: $this->getNullableFloat('volume'),
            swap: $this->getNullableFloat('swap'),
        );
    }
}
