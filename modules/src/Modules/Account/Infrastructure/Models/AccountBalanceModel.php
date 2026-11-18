<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Platform\Package\Components\Time\DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Platform\Account\Infrastructure\Models\Factories\AccountBalanceFactory;

/**
 * Properties
 * @property int      $id
 * @property int      $accountId
 * @property float    $equity
 * @property float    $equitySystem
 * @property float    $openPnl
 * @property float    $openPnlSystem
 * @property float    $freeMargin
 * @property float    $freeMarginSystem
 * @property int      $marginLevel
 * @property float    $availableWithdrawal
 * @property float    $volume
 * @property float    $swap
 * @property float    $closePnl
 * @property int      $openTradesCount
 * @property int      $closedTradesCount
 * @property DateTime $createdAt
 * @property DateTime $updatedAt
 */

class AccountBalanceModel extends Model
{
    use HasFactory;

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $connection = DB::MAIN;

    protected $table = 'accountsBalances';

    protected $guarded = ['id'];

    protected $casts = [
        'equity'              => 'float',
        'equitySystem'        => 'float',
        'openPnl'             => 'float',
        'openPnlSystem'       => 'float',
        'freeMargin'          => 'float',
        'freeMarginSystem'    => 'float',
        'availableWithdrawal' => 'float',
        'volume'              => 'float',
        'swap'                => 'float',
        'closePnl'            => 'float',
        'createdAt'           => DateTime::class,
        'updatedAt'           => DateTime::class,
    ];

    protected static function newFactory() : AccountBalanceFactory
    {
        return AccountBalanceFactory::new();
    }
}
