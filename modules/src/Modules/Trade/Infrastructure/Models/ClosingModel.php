<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\Database\DB;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\CloseReason;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Properties:
 * @property int $id
 * @property int $tradeId
 * @property int $mt5TransactionId
 * @property float $lots
 * @property float $amount
 * @property float $closePrice
 * @property CloseReason $closeReason
 * @property DateTime $closeTime
 * @property float $commission
 * @property float $pnl
 * @property float $pnlSystem
 * @property float $swap
 * @property float $swapSystem
 * @property float $turnover
 * @property float $turnoverSystem
 * @property DateTime $createdAt
 * @property DateTime $updatedAt
 *
 * Relations:
 * {@see ClosingModel::trade()}
 * @property TradeModel $trade
 */
class ClosingModel extends Model
{
    use HasFactory;

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $connection = DB::MAIN;

    protected $table = 'tradesClosing';

    protected $guarded = ['id'];

    protected $casts = [
        'lots'        => 'float',
        'amount'      => 'float',
        'closePrice'  => 'float',
        'commission'  => 'float',
        'pnl'         => 'float',
        'pnlSystem'   => 'float',
        'swap'           => 'float',
        'swapSystem'     => 'float',
        'turnover'       => 'float',
        'turnoverSystem' => 'float',
        'closeReason'    => CloseReason::class,
        'closeTime'   => DateTime::class,
        'createdAt'   => DateTime::class,
        'updatedAt'   => DateTime::class,
    ];

    protected static function newFactory() : Factories\ClosingFactory
    {
        return Factories\ClosingFactory::new();
    }

    public function trade() : BelongsTo
    {
        return $this->belongsTo(TradeModel::class, 'tradeId');
    }
}
