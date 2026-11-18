<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int    $id
 * @property string $name
 * @property string $value
 */
class AccountSettingModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = DB::MAIN;

    protected $table = 'accountsSettings';

    protected $guarded = ['id'];

    protected static function newFactory() : Factories\AccountSettingFactory
    {
        return Factories\AccountSettingFactory::new();
    }
}
