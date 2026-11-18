<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

class TransactionResponseDto
{
    public function __construct(
        public bool $success,
        public ?int $id = null,
        public bool $isDuplicate = false,
    ) {
    }
}
