<?php

declare(strict_types=1);

namespace Platform\Account\Api\Dto;

use Platform\Account\Domain\Dto\AccountDto as DomainAccountDto;

class ShortAccountDto
{
    public function __construct(
        public int    $accountId,
        public int    $accountLogin,
        public int    $currencyId,
        public int    $currencyPrecision,
        public string $currencyName,
        public string $currencySign,
    ) {
    }

    public static function fromDto(DomainAccountDto $dto) : self
    {
        return new self(
            $dto->accountId,
            $dto->login,
            $dto->currency->id,
            $dto->currency->precision,
            $dto->currency->name,
            $dto->currency->sign,
        );
    }
}
