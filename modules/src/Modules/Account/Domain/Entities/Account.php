<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Entities;

/**
 * @psalm-type AccountArray = array{
 *  id: int,
 *  leadId: int,
 *  login: int,
 *  groupId: int,
 *  leverage: int,
 *  balance: float,
 *  balanceSystem: float,
 *  credit: float,
 *  creditSystem: float,
 *  margin: float,
 *  marginSystem: float,
 *  enabled: bool,
 *  tradable: bool,
 *  selected: bool,
 *  isStopOutCloseEnabled: bool,
 *  group: GroupArray|null,
 * }
 */
class Account
{
    public function __construct(
        public int    $id,
        public int    $leadId,
        public int    $login,
        public int    $groupId,
        public int    $leverage,
        public float  $balance,
        public float  $balanceSystem,
        public float  $credit,
        public float  $creditSystem,
        public float  $margin,
        public float  $marginSystem,
        public bool   $enabled,
        public bool   $tradable,
        public bool   $selected,
        public bool   $marginCall,
        public bool   $stopOut,
        public bool   $isStopOutCloseEnabled,
        public ?Group $group = null,
    ) {
    }

    public function getCurrencyId() : int
    {
        return (int) $this->group?->currencyId;
    }

    public function toArray(array $with = []) : array
    {
        return array_merge($with, [
            'id'               => $this->id,
            'leadId'           => $this->leadId,
            'login'            => $this->login,
            'groupId'          => $this->groupId,
            'leverage'         => $this->leverage,
            'balance'          => $this->balance,
            'balanceSystem'    => $this->balanceSystem,
            'credit'           => $this->credit,
            'creditSystem'     => $this->creditSystem,
            'margin'           => $this->margin,
            'marginSystem'     => $this->marginSystem,
            'enabled'          => $this->enabled,
            'tradable'         => $this->tradable,
            'selected'         => $this->selected,
            'marginCall'       => $this->marginCall,
            'stopOut'          => $this->stopOut,
            'isStopOutCloseEnabled' => $this->isStopOutCloseEnabled,
            'group'            => $this->group?->toArray(),
        ]);
    }
}
