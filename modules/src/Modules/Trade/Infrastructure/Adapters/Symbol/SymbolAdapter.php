<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Adapters\Symbol;

use Platform\Symbol\Api\Dto\SymbolDto;
use Platform\Trade\Domain\Dto\QuoteDto;
use Platform\Symbol\Api\Api as SymbolApi;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Account\Api\Api as AccountApi;
use Platform\Currency\Api\Api as CurrencyApi;
use Platform\Trade\Domain\Adapters\SymbolAdapter as ISymbolAdapter;

class SymbolAdapter implements ISymbolAdapter
{
    public function __construct(
        private SymbolApi   $symbolApi,
        private AccountApi  $accountApi,
        private CurrencyApi $currencyApi,
    ) {
    }

    public function findForAccount(int $symbolId, int $accountId) : ?Symbol
    {
        $group = $this->accountApi->getGroupOfAccount($accountId);

        if ( ! $group) {
            return null;
        }

        $symbol = $this->symbolApi->findForGroup($symbolId, $group->id, true);

        if ( ! $symbol) {
            return null;
        }

        return $this->entityFromDto($symbol);
    }

    public function findOrCreate(string $symbol) : ?Symbol
    {
        $symbol = $this->symbolApi->findOrCreate($symbol);

        return $symbol ? $this->entityFromDto($symbol) : null;
    }

    private function entityFromDto(SymbolDto $symbol) : Symbol
    {
        return new Symbol(
            $symbol->id,
            $symbol->name,
            $symbol->displayName,
            $symbol->precision,
            $symbol->volumeMin,
            $symbol->volumeMax,
            $symbol->volumeStep,
            $symbol->tradeMode,
            $symbol->isTradeble,
            $symbol->contractSize,
            $this->resolveCurrencyId($symbol->profitCurrency),
        );
    }

    private function resolveCurrencyId(string $currencyName) : int
    {
        if ( ! $currencyName) {
            return 0;
        }

        return $this->currencyApi->findByName($currencyName)?->id ?? 0;
    }

    public function findQuote(int $symbolId, int $accountId) : ?QuoteDto
    {
        $group = $this->accountApi->getGroupOfAccount($accountId);

        if ( ! $group) {
            return null;
        }

        if ($quoteDto = $this->symbolApi->findQuote($symbolId, $group->id)) {
            return new QuoteDto(
                $quoteDto->symbolId,
                $quoteDto->ask,
                $quoteDto->bid,
                $quoteDto->middle,
                $quoteDto->spread,
                $quoteDto->createdTimestamp,
            );
        }

        return null;
    }

    public function getSymbol(int $symbolId) : Symbol
    {
        $symbol = $this->symbolApi->getSymbol($symbolId);

        return $this->entityFromDto($symbol);
    }

    public function findByName(string $name) : ?Symbol
    {
        $symbol = $this->symbolApi->findByName($name);

        return $symbol ? $this->entityFromDto($symbol) : null;
    }
}
