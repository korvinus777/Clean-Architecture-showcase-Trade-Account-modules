<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Platform\Package\Components\Time\DateTime;

/**
 * @property int      $id
 * @property int      $accountId
 * @property float    $volume
 * @property float    $swap
 * @property float    $closePnl
 * @property int      $openTradesCount
 * @property int      $closedTradesCount
 * @property float    $openTurnover
 * @property float    $openTurnoverSystem
 * @property float    $closedTurnover
 * @property float    $closedTurnoverSystem
 * @property DateTime $createdAt
 * @property DateTime $updatedAt
 */
class AccountStatisticsModel extends Model
{
    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $connection = DB::MAIN;

    protected $table = 'accountsStatistics';

    protected $guarded = ['id'];

    protected $casts = [
        'volume'               => 'float',
        'swap'                 => 'float',
        'closePnl'             => 'float',
        'openTurnover'         => 'float',
        'openTurnoverSystem'   => 'float',
        'closedTurnover'       => 'float',
        'closedTurnoverSystem' => 'float',
        'createdAt'            => DateTime::class,
        'updatedAt'            => DateTime::class,
    ];
}
