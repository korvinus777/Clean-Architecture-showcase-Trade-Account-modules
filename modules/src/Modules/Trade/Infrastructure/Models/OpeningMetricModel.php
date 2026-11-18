<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $tradeId
 * @property int $requestedFromClient
 * @property int $acceptedOnServer
 * @property int $sentOnMt
 * @property int $answeredFromMt
 * @property int $openedOnMt
 * @property int $acceptedEventOnServer
 * @property int $openedOnServer
 */
class OpeningMetricModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = DB::MAIN;

    protected $table = 'tradesOpeningMetrics';

    protected $guarded = ['id'];

    protected $attributes = [
        'requestedFromClient'   => 0,
        'acceptedOnServer'      => 0,
        'sentOnMt'              => 0,
        'answeredFromMt'        => 0,
        'openedOnMt'            => 0,
        'acceptedEventOnServer' => 0,
        'openedOnServer'        => 0,
    ];

    protected static function newFactory() : Factories\OpeningMetricFactory
    {
        return Factories\OpeningMetricFactory::new();
    }
}
