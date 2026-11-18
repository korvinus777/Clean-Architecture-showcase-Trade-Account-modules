<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

class TransactionInfoDto
{
    public function __construct(
        public int   $id,
        public int   $accountId,
        public int   $entityId,
        public int   $leadId,
        public ?int  $mt5TransactionId,
        public float $amount,
        public float $amountSystem,
        public int   $createdTimestamp,
    ) {
    }

    public function toArray() : array
    {
        return [
            'id'               => $this->id,
            'accountId'        => $this->accountId,
            'entityId'         => $this->entityId,
            'leadId'           => $this->leadId,
            'mt5TransactionId' => $this->mt5TransactionId,
            'amount'           => $this->amount,
            'amountSystem'     => $this->amountSystem,
            'createdTimestamp' => $this->createdTimestamp,
        ];
    }
}
