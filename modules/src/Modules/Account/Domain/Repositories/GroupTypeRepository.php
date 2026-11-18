<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Repositories;

interface GroupTypeRepository
{
    public function findNameById(int $id) : ?string;

    public function findIdByName(string $name) : ?int;

    public function getCustomId() : int;
}
