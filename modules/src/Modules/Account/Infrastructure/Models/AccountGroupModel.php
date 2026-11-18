<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Platform\Package\Components\Database\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Platform\Account\Domain\Entities\SpecificGroupType;

/**
 * @property int               $id
 * @property string            $name
 * @property int               $accountTypeId
 * @property int               $brandId
 * @property int               $currencyId
 * @property bool              $isDemo
 * @property SpecificGroupType $specificTypeId
 * @property bool              $isDefault
 * @property bool              $isNoSwap
 * @property int               $engineType
 *
 * Scopes:
 * @see AccountGroupModel::scopeValid()
 * @method static Builder valid()
 *
 * @see AccountGroupModel::scopeDefault()
 * @method static Builder default()
 *
 * Relations
 * @property AccountType|null $type {@uses AccountGroupModel::type()}
 * @property \Illuminate\Database\Eloquent\Collection|AccountModel[] $accounts {@uses AccountGroupModel::accounts()}
 */
class AccountGroupModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = DB::MAIN;

    protected $table = 'accountsGroups';

    protected $guarded = ['id'];

    protected $casts = [
        'isDemo'         => 'boolean',
        'isDefault'      => 'boolean',
        'isNoSwap'       => 'boolean',
        'specificTypeId' => SpecificGroupType::class,
    ];

    public function scopeValid(Builder $builder) : void
    {
        $builder->whereNot('currencyId', 0)
            ->whereNot('brandId', 0)
            ->whereNot('accountTypeId', 0);
    }

    public function scopeDefault(Builder $builder) : void
    {
        $builder->where('isDefault', 1);
    }

    /**
     * @return BelongsTo
     */
    public function type() : BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'accountTypeId');
    }

    /**
     * @return HasMany
     */
    public function accounts() : HasMany
    {
        return $this->hasMany(AccountModel::class, 'groupId');
    }

    protected static function newFactory() : Factories\AccountGroupFactory
    {
        return Factories\AccountGroupFactory::new();
    }
}
