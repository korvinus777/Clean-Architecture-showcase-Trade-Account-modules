<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Repositories;

use Platform\Account\Domain\Entities\Account;
use Platform\Account\Domain\Dto\AccountBalancesDto;
use Platform\Account\Domain\ValueObjects\AccountValueObject;

interface AccountRepository
{
    public function create(AccountValueObject $accountValueObject) : Account;

    public function find(int $id, bool $withGroup = false) : ?Account;

    public function get(int $id, bool $withGroup = false) : Account;

    /**
     * @param int $leadId
     * @param bool $withGroup
     * @return Account[]
     */
    public function findByLead(int $leadId, bool $withGroup = false) : array;

    public function update(Account $account) : void;

    public function updateBalance(int $accountId, float $amount, float $amountSystem) : bool;

    public function updateCredit(int $accountId, float $amount, float $amountSystem) : bool;

    public function delete(int $id) : void;

    public function findByLogin(int $login, bool $withGroup = false) : ?Account;

    public function findByLeadAndLogin(int $leadId, int $login, bool $withGroup = false) : ?Account;

    public function updateMargin(int $id, float $margin, float $marginSystem) : bool;

    public function saveChanges(int $accountId, array $diffs, bool $isFromMt) : int;

    public function unselectedAll(int $leadId) : void;

    public function select(int $accountId, int $leadId) : bool;

    public function getSelectedAccountId(int $leadId) : int;

    /**
     * @param int[] $logins
     * @return array<int, Account>
     */
    public function findByLogins(array $logins) : array;

    /**
     * @param int $accountId
     * @param bool $isEnter
     * @return bool
     */
    public function setMarginCall(int $accountId, bool $isEnter) : bool;

    /**
     * @param int $accountId
     * @param bool $isEnter
     * @return bool
     */
    public function setStopOut(int $accountId, bool $isEnter) : bool;

    public function sumSystemBalanceOfLeads(array $leadIds) : float;

    /**
     * @param int $lastLogin
     * @param int $limit
     * @return array<int, Account>
     */
    public function getLastAccounts(int $lastLogin, int $limit) : array;

    /**
     * @param int $accountId
     * @return AccountBalancesDto|null
     */
    public function findBalances(int $accountId) : ?AccountBalancesDto;

    /**
     * @param int[] $ids
     * @return array<int, Account>
     */
    public function findByIds(array $ids) : array;
}
