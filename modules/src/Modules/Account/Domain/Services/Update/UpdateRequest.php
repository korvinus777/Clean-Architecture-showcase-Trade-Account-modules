<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Update;

use Platform\Account\Domain\Entities\SpecificGroupType;

class UpdateRequest
{
    public function __construct(
        public int                $accountId,
        public ?string            $name              = null,
        public ?string            $type              = null,
        public ?SpecificGroupType $specificGroupType = null,
        public ?int               $leverage          = null,
        public ?string            $mainPassword      = null,
        public ?string            $investorPassword  = null,
        public ?bool              $enabled           = null,
        public ?bool              $tradable          = null,
        public ?float             $margin            = null,
        public bool               $isFromMt          = false,
        public ?bool              $isStopOutCloseEnabled = null,
    ) {
    }

    private function payloadToArray() : array
    {
        return [
            'name'              => $this->name,
            'type'              => $this->type,
            'specificGroupType' => $this->specificGroupType,
            'leverage'          => $this->leverage,
            'mainPassword'      => $this->mainPassword,
            'investorPassword'  => $this->investorPassword,
            'enabled'           => $this->enabled,
            'tradable'          => $this->tradable,
            'margin'            => $this->margin,
            'isStopOutCloseEnabled' => $this->isStopOutCloseEnabled,
        ];
    }

    public function isEmpty() : bool
    {
        foreach ($this->payloadToArray() as $value) {
            if ($value !== null) {
                return false;
            }
        }

        return true;
    }
}
