<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Properties:
 * @property int $id
 * @property int $orderId
 * @property int $tradeId
 * @property int $activatedOnMt
 * @property int $acceptedEventOnServer
 * @property int $activatedOnServer
 */
class ActivatingOrderMetricModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = DB::MAIN;

    protected $table = 'tradesActivatingOrderMetrics';

    protected $guarded = ['id'];

    protected static function newFactory() : Factories\ActivatingOrderMetricFactory
    {
        return Factories\ActivatingOrderMetricFactory::new();
    }
}
