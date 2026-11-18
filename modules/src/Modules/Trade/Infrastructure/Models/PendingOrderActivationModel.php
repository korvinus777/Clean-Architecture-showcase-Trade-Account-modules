<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Platform\Package\Components\Time\DateTime;

/**
 * Properties:
 * @property int $id
 * @property int $orderId
 * @property int $tradeId
 * @property DateTime $activateTime
 */
class PendingOrderActivationModel extends Model
{
    public $timestamps = false;

    protected $connection = DB::MAIN;

    protected $table = 'tradesPendingOrderActivations';

    protected $guarded = ['id'];

    protected $casts = [
        'activateTime' => DateTime::class,
    ];
}
