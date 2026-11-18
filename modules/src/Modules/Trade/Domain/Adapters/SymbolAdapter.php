<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Adapters;

use Platform\Trade\Domain\Dto\QuoteDto;
use Platform\Trade\Domain\Entities\Symbol;

interface SymbolAdapter
{
    public function findForAccount(int $symbolId, int $accountId) : ?Symbol;

    public function findOrCreate(string $symbol) : ?Symbol;

    public function findQuote(int $symbolId, int $accountId) : ?QuoteDto;

    public function getSymbol(int $symbolId) : Symbol;

    public function findByName(string $name) : ?Symbol;
}
