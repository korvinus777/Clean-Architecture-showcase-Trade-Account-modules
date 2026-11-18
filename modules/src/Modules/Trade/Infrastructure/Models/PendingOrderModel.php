<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Platform\Package\Components\Time\DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;
use Platform\Trade\Domain\ValueObjects\PendingOrderStatus;

/**
 * @property int                $id
 * @property int                $accountId
 * @property int                $symbolId
 * @property float              $lots
 * @property float              $price
 * @property float              $stopLimitPrice
 * @property float              $stopLoss
 * @property float              $takeProfit
 * @property PendingOrderType   $type
 * @property PendingOrderStatus $status
 * @property DateTime           $openTime
 * @property DateTime|null      $closeTime
 * @property DateTime           $createdAt
 * @property DateTime           $updatedAt
 */
class PendingOrderModel extends Model
{
    use HasFactory;

    public const UPDATED_AT = 'updatedAt';
    public const CREATED_AT = 'updatedAt';

    protected $connection = DB::MAIN;

    protected $table = 'tradesPendingOrders';

    protected $guarded = ['id'];

    protected $casts = [
        'createdAt'      => DateTime::class,
        'updatedAt'      => DateTime::class,
        'openTime'       => DateTime::class,
        'closeTime'      => DateTime::class,
        'type'           => PendingOrderType::class,
        'status'         => PendingOrderStatus::class,
        'lots'           => 'float',
        'price'          => 'float',
        'stopLimitPrice' => 'float',
        'stopLoss'       => 'float',
        'takeProfit'     => 'float',
    ];

    public static function defaultAttributes() : array
    {
        return [
            'accountId'      => 0,
            'symbolId'       => 0,
            'lots'           => 0,
            'price'          => 0,
            'stopLimitPrice' => 0,
            'stopLoss'       => 0,
            'takeProfit'     => 0,
            'type'           => PendingOrderType::BUY_LIMIT,
            'status'         => PendingOrderStatus::PENDING,
            'openTime'       => DateTime::now(),
            'closeTime'      => DateTime::now(),
        ];
    }

    protected static function newFactory() : Factories\PendingOrderFactory
    {
        return Factories\PendingOrderFactory::new();
    }
}
