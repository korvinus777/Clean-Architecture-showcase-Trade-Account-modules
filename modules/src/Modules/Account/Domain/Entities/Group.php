<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Entities;

/**
 * @psalm-type GroupArray = array{
 *  id: int,
 *  name: string,
 *  typeId: int,
 *  brandId: int,
 *  isDemo: bool,
 *  specificType: int,
 *  currencyId: int,
 *  isNoSwap: bool,
 *  typeName: string|null,
 * }
 */
class Group
{
    public const CUSTOM_TYPE  = 'custom';
    public const REGONLY_TYPE = 'regonly';

    public function __construct(
        public int               $id,
        public string            $name,
        public int               $typeId,
        public int               $brandId,
        public bool              $isDemo,
        public SpecificGroupType $specificType,
        public int               $currencyId,
        public bool              $isNoSwap,
        public int               $engineType = 0,
        public ?string           $typeName = null,
    ) {
    }

    public function toArray() : array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'typeId'       => $this->typeId,
            'brandId'      => $this->brandId,
            'isDemo'       => $this->isDemo,
            'specificType' => $this->specificType->value,
            'currencyId'   => $this->currencyId,
            'isNoSwap'     => $this->isNoSwap,
            'engineType'   => $this->engineType,
            'typeName'     => (string) $this->typeName,
        ];
    }
}
