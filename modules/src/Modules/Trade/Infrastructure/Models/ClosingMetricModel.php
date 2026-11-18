<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $tradeId
 * @property int $mt5TransactionId
 * @property int $requestedFromClient
 * @property int $acceptedOnServer
 * @property int $sentOnMt
 * @property int $answeredFromMt
 * @property int $closedOnMt
 * @property int $acceptedEventOnServer
 * @property int $closedOnServer
 */
class ClosingMetricModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = DB::MAIN;

    protected $table = 'tradesClosingMetrics';

    protected $guarded = ['id'];

    protected $attributes = [
        'requestedFromClient'   => 0,
        'acceptedOnServer'      => 0,
        'sentOnMt'              => 0,
        'answeredFromMt'        => 0,
        'closedOnMt'            => 0,
        'acceptedEventOnServer' => 0,
        'closedOnServer'        => 0,
    ];

    protected static function newFactory() : Factories\ClosingMetricFactory
    {
        return Factories\ClosingMetricFactory::new();
    }
}
