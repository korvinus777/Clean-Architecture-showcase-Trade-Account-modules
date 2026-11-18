<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services;

use Platform\Trade\Domain\Adapters\CurrencyAdapter;

class TurnoverCalculator
{
    public function __construct(
        private CurrencyAdapter $currencyAdapter,
    ) {
    }

    /**
     * Turnover = lots × openPrice × contractSize.
     * Converts: profitCurrency → system (USD), then system → account currency.
     */
    public function calculate(
        float $lots,
        float $openPrice,
        float $contractSize,
        int   $profitCurrencyId,
        int   $accountCurrencyId,
        int   $groupId,
    ) : TurnoverResult {
        $rawTurnover = $lots * $openPrice * $contractSize;

        if ($rawTurnover === 0.0) {
            return TurnoverResult::zero();
        }

        $inSystem = $profitCurrencyId
            ? $this->currencyAdapter->converseToSystem($rawTurnover, $profitCurrencyId, $groupId)
            : $rawTurnover;

        $inAccount = $this->currencyAdapter->converseFromSystem($inSystem, $accountCurrencyId, $groupId);

        return new TurnoverResult(
            inAccountCurrency: $inAccount,
            inSystemCurrency: $inSystem,
        );
    }
}
