<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Platform\Package\Components\Time\DateTime;

/**
 * @property int $id
 * @property int $accountId
 * @property array $diffs
 * @property bool $isFromMt
 * @property DateTime $createdAt
 */
class ChangeModel extends Model
{
    public $timestamps = false;

    protected $connection = DB::MAIN;

    protected $table = 'accountsChanges';

    protected $guarded = ['id'];

    protected $casts = [
        'diffs'     => 'array',
        'isFromMt'  => 'bool',
        'createdAt' => DateTime::class,
    ];
}
