<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Platform\Package\Components\Time\DateTime;
use Platform\Package\Components\Http\Request\Context;

/**
 * @property int $id
 * @property int $tradeId
 * @property array $diffs
 * @property Context $context
 * @property DateTime $createdAt
 */
class ChangeModel extends Model
{
    public $timestamps = false;

    protected $connection = DB::MAIN;

    protected $table = 'tradesChanges';

    protected $guarded = ['id'];

    protected $casts = [
        'diffs'     => 'array',
        'context'   => Context::class,
        'createdAt' => DateTime::class,
    ];
}
