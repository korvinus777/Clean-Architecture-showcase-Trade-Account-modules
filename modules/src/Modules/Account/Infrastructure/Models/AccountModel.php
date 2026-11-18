<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Time\DateTime;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Platform\Account\Infrastructure\Mappers\AccountMapper;
use Platform\Account\Infrastructure\Models\Factories\AccountFactory;

/**
 * Properties
 * @property int      $id
 * @property int      $login
 * @property int      $leadId
 * @property int      $groupId
 * @property int      $leverage
 * @property float    $balance
 * @property float    $balanceSystem
 * @property float    $credit
 * @property float    $creditSystem
 * @property float    $margin
 * @property float    $marginSystem
 * @property bool     $isEnabled
 * @property bool     $isTradable
 * @property bool     $isSelected
 * @property bool     $isMarginCall
 * @property bool     $isStopOut
 * @property bool     $isStopOutCloseEnabled
 * @property DateTime $createdAt
 * @property DateTime $updatedAt
 * @property DateTime $deletedAt
 *
 * @property AccountGroupModel|null $group
 * @property AccountBalanceModel|null $balances
 *
 * @method static AccountFactory factory($count = null, $state = [])
 */
class AccountModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';
    public const DELETED_AT = 'deletedAt';

    protected $connection = DB::MAIN;

    protected $table = 'accounts';

    protected $guarded = ['id'];

    protected $casts = [
        'balance'          => 'float',
        'balanceSystem'    => 'float',
        'credit'           => 'float',
        'creditSystem'     => 'float',
        'margin'           => 'float',
        'marginSystem'     => 'float',
        'isEnabled'        => 'boolean',
        'isTradable'       => 'boolean',
        'isSelected'       => 'boolean',
        'isMarginCall'     => 'boolean',
        'isStopOut'        => 'boolean',
        'isStopOutCloseEnabled' => 'boolean',
        'createdAt'        => DateTime::class,
        'updatedAt'        => DateTime::class,
        'deletedAt'        => DateTime::class,
    ];

    protected static function newFactory() : AccountFactory
    {
        return AccountFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function group() : BelongsTo
    {
        return $this->belongsTo(AccountGroupModel::class, 'groupId');
    }

    /**
     * @return HasOne
     */
    public function balances() : HasOne
    {
        return $this->hasOne(AccountBalanceModel::class, 'accountId');
    }

    public function toEntity() : Account
    {
        return AccountMapper::entityFromModel($this);
    }
}
