<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'WithdrawalInfoDto',
    properties: [
        new OA\Property('amountMin', type: 'float', example: 50.00),
        new OA\Property('amountMax', type: 'float', example: 1000.00),
        new OA\Property('countLeft', type: 'int', example: 3),
        new OA\Property('bankCountries', type: 'array', items: new OA\Items(), example: [
            'AF' => 'Afghanistan',
            'UA' => 'Ukraine',
            'ZW' => 'Zimbabwe',
        ]),
        new OA\Property('walletChains', type: 'array', items: new OA\Items(), example: [
            1 => 'BTC',
            2 => 'ETH',
            3 => 'USDT (TRC20)',
            4 => 'USDT (ERC20)',
        ]),
        new OA\Property('paymentMethods', type: 'array', items: new OA\Items('#/components/schemas/PaymentMethodDto')),
    ],
)]
class WithdrawalInfoDto
{
    /**
     * @param float $amountMax
     * @param float $amountMin
     * @param int $countLeft
     * @param array $bankCountries
     * @param array $walletChains
     * @param PaymentMethodDto[] $paymentMethods
     */
    public function __construct(
        public float $amountMax,
        public float $amountMin,
        public int   $countLeft,
        public array $bankCountries,
        public array $walletChains,
        public array $paymentMethods,
    ) {
    }

    public function toArray() : array
    {
        return [
            'amountMax'      => $this->amountMax,
            'amountMin'      => $this->amountMin,
            'countLeft'      => $this->countLeft,
            'bankCountries'  => $this->bankCountries,
            'walletChains'   => $this->walletChains,
            'paymentMethods' => array_map(function (PaymentMethodDto $dto) : array {
                return $dto->toArray();
            }, $this->paymentMethods),
        ];
    }
}
