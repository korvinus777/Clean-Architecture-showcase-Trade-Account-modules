<?php

declare(strict_types=1);

namespace App\Http\Requests\Account;

use OpenApi\Attributes as OA;
use Illuminate\Validation\Rule;
use Platform\Account\Domain\Entities\SpecificGroupType;
use Platform\Package\Components\Http\Request\FormValidator;
use Platform\Account\Domain\Services\Update\UpdateRequest as UpdateRequestModel;

#[OA\RequestBody(
    request: 'UpdateAccount',
    required: true,
    content: new OA\JsonContent(
        required: [
            'type',
            'specificType',
        ],
        properties: [
            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
            new OA\Property(property: 'type', type: 'string', example: 'gold'),
            new OA\Property(property: 'specificType', description: 'Specific type of account: 1 - regular, 2 - custom, 3 - mam', type: 'int', example: 1),
            new OA\Property(property: 'currency', type: 'string', example: 'usd'),
            new OA\Property(property: 'isDemo', type: 'bool', example: true),
            new OA\Property(property: 'mainPassword', type: 'string', example: 'aB123456'),
            new OA\Property(property: 'investorPassword', type: 'string', example: 'aB123456'),
            new OA\Property(property: 'isEnabled', description: 'Account enabled', type: 'bool', example: true),
            new OA\Property(property: 'isTradable', description: 'Trading allowed', type: 'bool', example: true),
        ],
    ),
)]
class UpdateRequest extends FormValidator
{
    public function rules() : array
    {
        return [
            'name'             => ['string', 'max:256'],
            'type'             => ['required_with:specificType', 'string'],
            'specificType'     => [
                'required_with:type',
                'integer',
                Rule::in(array_map(fn (SpecificGroupType $type) : int => $type->value, SpecificGroupType::cases())),
            ],
            'leverage'         => ['integer'],
            'mainPassword'     => ['string'],
            'investorPassword' => ['string'],
            'isEnabled'        => ['boolean'],
            'isTradable'       => ['boolean'],
        ];
    }

    public function domainRequest(int $accountId) : UpdateRequestModel
    {
        $specificType = $this->getNullableInt('specificType');

        $specificGroupType = $specificType !== null ? SpecificGroupType::from($specificType) : null;

        return new UpdateRequestModel(
            accountId: $accountId,
            name: $this->getNullableString('name'),
            type: $this->getNullableString('type'),
            specificGroupType: $specificGroupType,
            leverage: $this->getNullableInt('leverage'),
            mainPassword: $this->getNullableString('mainPassword'),
            investorPassword: $this->getNullableString('investorPassword'),
            enabled: $this->getNullableBool('isEnabled'),
            tradable: $this->getNullableBool('isTradable'),
            isStopOutCloseEnabled: $this->getNullableBool('isStopOutEnabled'),
        );
    }
}
