<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

use OpenApi\Attributes as OA;
use Platform\Account\Domain\Entities\Account;

#[OA\Schema(
    schema: 'AccountDto',
    properties: [
        new OA\Property('accountId', type: 'int', example: 1),
        new OA\Property('login', type: 'int', example: 2006),
        new OA\Property('balance', type: 'float', example: 1000),
        new OA\Property('balanceSystem', type: 'float', example: 800),
        new OA\Property('credit', type: 'float', example: 1000),
        new OA\Property('creditSystem', type: 'float', example: 800),
        new OA\Property('equity', type: 'float', example: 1000),
        new OA\Property('equitySystem', type: 'float', example: 800),
        new OA\Property('openPnl', type: 'float', example: 1000),
        new OA\Property('openPnlSystem', type: 'float', example: 800),
        new OA\Property('margin', type: 'float', example: 1000),
        new OA\Property('marginSystem', type: 'float', example: 800),
        new OA\Property('freeMargin', type: 'float', example: 500),
        new OA\Property('freeMarginSystem', type: 'float', example: 350),
        new OA\Property('marginLevel', type: 'int', example: 90),
        new OA\Property('demo', type: 'bool', example: false),
        new OA\Property('enabled', type: 'bool', example: true),
        new OA\Property('tradable', type: 'bool', example: true),
        new OA\Property('selected', type: 'bool', example: true),
        new OA\Property('noSwap', type: 'bool', example: true),
        new OA\Property('currency', ref: '#/components/schemas/CurrencyDto', type: 'object'),
        new OA\Property('openTurnover', description: 'Sum of turnover of open positions in account currency', type: 'float', example: 250000.0),
        new OA\Property('openTurnoverSystem', description: 'Sum of turnover of open positions in USD', type: 'float', example: 250000.0),
        new OA\Property('closedTurnover', description: 'Sum of turnover of closed trades in account currency', type: 'float', example: 1500000.0),
        new OA\Property('closedTurnoverSystem', description: 'Sum of turnover of closed trades in USD', type: 'float', example: 1500000.0),
    ],
)]
class AccountDto
{
    public function __construct(
        public int         $accountId,
        public int         $login,
        public float       $balance,
        public float       $balanceSystem,
        public float       $credit,
        public float       $creditSystem,
        public float       $equity,
        public float       $equitySystem,
        public float       $openPnl,
        public float       $openPnlSystem,
        public float       $margin,
        public float       $marginSystem,
        public float       $freeMargin,
        public float       $freeMarginSystem,
        public int         $marginLevel,
        public bool        $demo,
        public bool        $enabled,
        public bool        $tradable,
        public bool        $selected,
        public bool        $noSwap,
        public CurrencyDto $currency,
        public float       $openTurnover = 0,
        public float       $openTurnoverSystem = 0,
        public float       $closedTurnover = 0,
        public float       $closedTurnoverSystem = 0,
    ) {
    }

    public static function fromEntity(Account $account, AccountBalancesDto $balancesDto = null, CurrencyDto $currencyDto = null, AccountStatisticsDto $statisticsDto = null) : self
    {
        if ( ! $currencyDto) {
            $currencyDto = new CurrencyDto(
                0,
                '---',
                '?',
                0,
            );
        }

        return new self(
            $account->id,
            $account->login,
            $account->balance,
            $account->balanceSystem,
            $account->credit,
            $account->creditSystem,
            (float) $balancesDto?->equity,
            (float) $balancesDto?->equitySystem,
            (float) $balancesDto?->openPnl,
            (float) $balancesDto?->openPnlSystem,
            $account->margin,
            $account->marginSystem,
            (float) $balancesDto?->freeMargin,
            (float) $balancesDto?->freeMarginSystem,
            (int) $balancesDto?->marginLevel,
            $account->group?->isDemo ?? false,
            $account->enabled,
            $account->tradable,
            $account->selected,
            $account->group?->isNoSwap ?? false,
            $currencyDto,
            (float) ($statisticsDto?->openTurnover ?? 0),
            (float) ($statisticsDto?->openTurnoverSystem ?? 0),
            (float) ($statisticsDto?->closedTurnover ?? 0),
            (float) ($statisticsDto?->closedTurnoverSystem ?? 0),
        );
    }

    public function toArray() : array
    {
        return [
            'accountId'        => $this->accountId,
            'login'            => $this->login,
            'balance'          => $this->balance,
            'balanceSystem'    => $this->balanceSystem,
            'credit'           => $this->credit,
            'creditSystem'     => $this->creditSystem,
            'equity'           => $this->equity,
            'equitySystem'     => $this->equitySystem,
            'openPnl'          => $this->openPnl,
            'openPnlSystem'    => $this->openPnlSystem,
            'margin'           => $this->margin,
            'marginSystem'     => $this->marginSystem,
            'freeMargin'       => $this->freeMargin,
            'freeMarginSystem' => $this->freeMarginSystem,
            'marginLevel'      => $this->marginLevel,
            'demo'             => $this->demo,
            'enabled'          => $this->enabled,
            'tradable'         => $this->tradable,
            'selected'         => $this->selected,
            'noSwap'                => $this->noSwap,
            'currency'              => $this->currency->toArray(),
            'openTurnover'          => $this->openTurnover,
            'openTurnoverSystem'    => $this->openTurnoverSystem,
            'closedTurnover'        => $this->closedTurnover,
            'closedTurnoverSystem'  => $this->closedTurnoverSystem,
        ];
    }
}
