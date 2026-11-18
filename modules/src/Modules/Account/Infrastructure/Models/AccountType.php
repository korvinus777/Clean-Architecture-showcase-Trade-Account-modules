<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Platform\Account\Infrastructure\Models\Factories\AccountTypeFactory;

/**
 * @property int    $id
 * @property string $name
 *
 * @method static AccountTypeFactory factory($count = null, $state = [])
 */
class AccountType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = DB::MAIN;

    protected $table = 'accountsTypes';

    protected $guarded = ['id'];

    protected static function newFactory() : AccountTypeFactory
    {
        return AccountTypeFactory::new();
    }
}
