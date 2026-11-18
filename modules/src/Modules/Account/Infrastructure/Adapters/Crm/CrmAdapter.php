<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Adapters\Crm;

use Platform\Crm\Api\Api;
use Platform\Account\Domain\Dto\PaymentMethodDto;
use Platform\Account\Domain\Dto\WithdrawalInfoDto;
use Platform\Account\Domain\Adapters\CrmAdapter as ICrmAdapter;
use Platform\Crm\Domain\Dto\PaymentMethodDto as ApiPaymentMethodDto;

class CrmAdapter implements ICrmAdapter
{
    public function __construct(private Api $api)
    {
    }

    public function getWithdrawalInfo(int $accountId) : ?WithdrawalInfoDto
    {
        $wdDto = $this->api->getWithdrawalInfo($accountId);

        if ( ! $wdDto) {
            return null;
        }

        return new WithdrawalInfoDto(
            amountMax: $wdDto->amountMax,
            amountMin: $wdDto->amountMin,
            countLeft:  $wdDto->countLeft,
            bankCountries: $wdDto->bankCountries,
            walletChains: $wdDto->walletChains,
            paymentMethods: array_map(function (ApiPaymentMethodDto $dto) : PaymentMethodDto {
                return new PaymentMethodDto(
                    id: $dto->id,
                    title: $dto->title,
                    systemName: $dto->systemName,
                    isActive: $dto->isActive,
                    fields: $dto->fields,
                );
            }, $wdDto->paymentMethods),
        );
    }
}
