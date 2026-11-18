<?php

declare(strict_types=1);

namespace App\Http\Requests\Account;

use OpenApi\Attributes as OA;
use Illuminate\Validation\Rule;
use Platform\Account\Domain\Entities\SpecificGroupType;
use Platform\Package\Components\Http\Request\FormValidator;
use Platform\Account\Domain\Services\Create\CreateRequest as DomainCreateRequest;

#[OA\RequestBody(
    request: 'CreateAccount',
    required: true,
    content: new OA\JsonContent(
        required: [
            'leadHashId',
            'name',
            'type',
            'specificType',
            'currency',
            'isDemo',
            'mainPassword',
            'investorPassword',
        ],
        properties: [
            new OA\Property(property: 'leadHashId', type: 'int', example: 143),
            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
            new OA\Property(property: 'type', type: 'string', example: 'gold'),
            new OA\Property(property: 'specificType', description: 'Specific type of account: 1 - regular, 2 - custom, 3 - mam', type: 'int', example: 1),
            new OA\Property(property: 'currency', type: 'string', example: 'usd'),
            new OA\Property(property: 'isDemo', type: 'bool', example: true),
            new OA\Property(property: 'mainPassword', type: 'string', example: 'aB123456'),
            new OA\Property(property: 'investorPassword', type: 'string', example: 'aB123456'),
            new OA\Property(property: 'isEnabled', description: 'Account enabled', type: 'bool', example: true),
            new OA\Property(property: 'isTradable', description: 'Trading allowed', type: 'bool', example: true),
            new OA\Property(property: 'isStopOutEnabled', description: 'Closing trades in stopout allowed', type: 'bool', example: true),
        ],
    ),
)]
class CreateRequest extends FormValidator
{
    public function rules() : array
    {
        return [
            'leadHashId'       => ['required', 'integer'],
            'name'             => ['required', 'string', 'max:256'],
            'type'             => ['required', 'string'],
            'specificType'     => [
                'required',
                'integer',
                Rule::in(array_map(fn (SpecificGroupType $type) : int => $type->value, SpecificGroupType::cases())),
            ],
            'currency'         => ['required', 'string'],
            'isDemo'           => ['required', 'boolean'],
            'mainPassword'     => ['required', 'string'],
            'investorPassword' => ['required', 'string'],
            'isEnabled'        => ['boolean'],
            'isTradable'       => ['boolean'],
            'isStopOutEnabled' => ['boolean'],
        ];
    }

    public function domainRequest() : DomainCreateRequest
    {
        return new DomainCreateRequest(
            $this->getInt('leadHashId'),
            $this->getString('name'),
            $this->getString('type'),
            SpecificGroupType::from($this->getInt('specificType')),
            $this->getString('currency'),
            $this->getBool('isDemo'),
            $this->getString('mainPassword'),
            $this->getString('investorPassword'),
            $this->getBool('isEnabled', true),
            $this->getBool('isTradable', true),
            $this->getBool('isStopOutEnabled', true),
        );
    }
}
