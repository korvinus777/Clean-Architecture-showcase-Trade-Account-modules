<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Adapters\Transaction;

use Platform\Transaction\Api\Api;
use Platform\Package\Components\Time\DateTime;
use Platform\Account\Domain\Adapters\TransactionAdapter as ITransactionAdapter;

class TransactionAdapter implements ITransactionAdapter
{
    public function __construct(
        private Api $api,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getActiveAccountsAfter(DateTime $after, int $limit, int $page) : array
    {
        return $this->api->getAccountsAfterTime($after, $limit, $page);
    }
}
