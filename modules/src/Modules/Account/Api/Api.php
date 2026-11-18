<?php

declare(strict_types=1);

namespace Platform\Account\Api;

use Platform\Account\Api\Dto\GroupDto;
use Platform\Account\Api\Dto\AccountDto;
use Platform\Account\Api\Dto\ShortAccountDto;
use Platform\Account\Domain\Dto\AccountBalancesDto;

interface Api
{
    public function findGroupIdByName(string $group) : ?int;

    public function addAccountGroup(
        string  $name,
        ?bool   $isDemo = null,
        ?string $currency = null,
        ?int    $currencyPrecision = null,
    ) : ?GroupDto;

    public function getById(int $id) : ?AccountDto;

    public function updateBalance(int $accountId, float $amount, float $amountSystem) : bool;

    public function updateCredit(int $accountId, float $amount, float $amountSystem) : bool;

    public function getGroupOfAccount(int $accountId) : ?GroupDto;

    public function getDefaultGroup() : GroupDto;

    public function getAccountLeverage(int $accountId) : int;

    public function getDefaultLeverage() : int;

    public function create(
        int    $leadHashId,
        string $name,
        string $type,
        int    $specificType,
        string $currency,
        bool   $isDemo,
        string $mainPassword,
        string $investorPassword,
        bool   $enabled,
        bool   $tradable,
        bool   $isStopOutCloseEnabled,
    ) : ?AccountDto;

    public function findGroup(int $groupId) : ?GroupDto;

    public function findByLogin(int $login) : ?AccountDto;

    public function getGroupIds() : array;

    public function updateMtAccount(
        int     $login,
        ?string $group = null,
        ?int    $leverage = null,
        ?bool   $isEnabled = null,
        ?bool   $isTradable = null,
        ?float  $margin = null,
    ) : bool;

    public function delete(int $accountId, bool $isFromMt = false) : bool;

    public function getSelectedAccountId(int $leadId) : int;

    /**
     * @param int[] $logins
     * @return array<int, AccountDto>
     */
    public function getAccountsByLogin(array $logins) : array;

    /**
     * @param int $login
     * @return bool
     */
    public function marginCallEnter(int $login) : bool;

    /**
     * @param int $login
     * @return bool
     */
    public function marginCallLeave(int $login) : bool;

    /**
     * @param int $login
     * @return bool
     */
    public function stopOutEnter(int $login) : bool;

    /**
     * @param int $login
     * @return bool
     */
    public function stopOutLeave(int $login) : bool;

    public function sumSystemBalanceOfLeads(array $leadIds) : float;

    public function findAccountBalances(int $accountId) : ?AccountBalancesDto;
    /**
     * @param int $leadId
     * @return ShortAccountDto[]
     */
    public function getAccountsOfLead(int $leadId) : array;

    /**
     * @param int $login
     * @param float $balance
     * @param float $credit
     * @return bool
     */
    public function updateAccountBalanceByLogin(int $login, float $balance, float $credit) : bool;

    /**
     * @param int $login
     * @param float $margin
     * @param bool $isFromMt
     * @return bool
     */
    public function updateMargin(int $login, float $margin, bool $isFromMt) : bool;

    /**
     * @param int[] $leadIds
     * @return array<int, \Platform\Account\Domain\Dto\AccountStatisticsDto> keyed by leadId
     */
    public function sumTurnoverOfLeads(array $leadIds) : array;

    public function addOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool;

    public function moveOpenToClosedTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool;

    public function moveClosedToOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool;
}
