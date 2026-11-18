<?php

declare(strict_types=1);

namespace Platform\Trade\Api\Dto;

class IdResponseDto
{
    public function __construct(
        public ?int $id,
        public bool $alreadyProcessed,
    ) {
    }
}
