<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\Direction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Platform\Trade\Infrastructure\Models\Factories\TradeFactory;

/**
 * @property int $id
 * @property int $accountId
 * @property int $leadId
 * @property int $symbolId
 * @property float $contractSize
 * @property float $lots
 * @property float $lotsLeft
 * @property float $amount
 * @property float $amountLeft
 * @property Direction $direction
 * @property float $openPrice
 * @property DateTime $openTime
 * @property float $stopLoss
 * @property float $takeProfit
 * @property float $swap
 * @property float $swapSystem
 * @property float $turnover
 * @property float $turnoverSystem
 * @property bool $isClosed
 * @property bool $isMigrated
 * @property DateTime $createdAt
 * @property DateTime $updatedAt
 *
 * @method static TradeFactory factory($count = null, $state = [])
 */
class TradeModel extends Model
{
    use HasFactory;

    public const UPDATED_AT = 'updatedAt';
    public const CREATED_AT = 'createdAt';

    protected $connection = DB::MAIN;

    protected $table = 'trades';

    protected $guarded = ['id'];

    protected $casts = [
        'contractSize' => 'float',
        'lots'         => 'float',
        'lotsLeft'     => 'float',
        'amount'       => 'float',
        'amountLeft'   => 'float',
        'direction'    => Direction::class,
        'openPrice'    => 'float',
        'openTime'     => DateTime::class,
        'stopLoss'     => 'float',
        'takeProfit'   => 'float',
        'swap'           => 'float',
        'swapSystem'     => 'float',
        'turnover'       => 'float',
        'turnoverSystem' => 'float',
        'isClosed'       => 'bool',
        'isMigrated'   => 'bool',
        'createdAt'    => DateTime::class,
        'updatedAt'    => DateTime::class,
    ];

    public static function defaultAttributes() : array
    {
        return [
            'accountId'    => 0,
            'leadId'       => 0,
            'symbolId'     => 0,
            'contractSize' => 0,
            'lots'         => 0,
            'lotsLeft'     => 0,
            'amount'       => 0,
            'amountLeft'   => 0,
            'direction'    => Direction::BUY->value,
            'openPrice'    => 0,
            'openTime'     => DateTime::now(),
            'takeProfit'   => 0,
            'stopLoss'     => 0,
            'swap'           => 0,
            'swapSystem'     => 0,
            'turnover'       => 0,
            'turnoverSystem' => 0,
            'isClosed'       => false,
        ];
    }

    protected static function newFactory() : TradeFactory
    {
        return TradeFactory::new();
    }
}
