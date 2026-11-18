<?php

declare(strict_types=1);

namespace App\Http\Requests\Account;

use Platform\Package\Components\Http\Request\FormValidator;
use Platform\Lead\App\Services\GetDepositLink\GetDepositLinkRequest as DomainRequest;

class DepositLinkRequest extends FormValidator
{
    public function rules() : array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
            'domain' => ['required', 'string'],
        ];
    }

    public function domainRequest(int $accountId) : DomainRequest
    {
        return new DomainRequest(
            accountId: $accountId,
            amount: $this->getFloat('amount'),
            domain: $this->getString('domain'),
        );
    }
}
