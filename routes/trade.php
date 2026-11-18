<?php

declare(strict_types=1);

use App\Http\Controllers\Trade\EditTradeFromCrmController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Trade\EditTradeController;
use App\Http\Controllers\Trade\OpenTradeController;
use App\Http\Controllers\Trade\CloseTradeController;
use App\Http\Controllers\Trade\ReopenTradeController;
use App\Http\Controllers\Trade\EditPendingOrderController;
use App\Http\Controllers\Trade\OpenPendingOrderController;
use App\Http\Controllers\Trade\ClosePendingOrderController;
use App\Http\Controllers\Trade\CloseAccountTradesController;
use App\Http\Controllers\Trade\CloseAccountPendingOrdersController;

Route::pattern('tradeId', '[0-9]+');

Route::post('/', OpenTradeController::class)->name('open.trade')->middleware(['auth', 'trade.notify.status']);
Route::delete('/pending-order', CloseAccountPendingOrdersController::class)->name('pending-order.close.all')->middleware('auth');
Route::delete('/', CloseAccountTradesController::class)->name('close.all')->middleware('auth');
Route::delete('/{tradeId}', CloseTradeController::class)->name('close.trade')->middleware('auth');
Route::put('/{tradeId}', EditTradeController::class)->name('edit.trade')->middleware('auth');
Route::put('/reopen', ReopenTradeController::class)->name('reopen.trade');
Route::put('/edit', EditTradeFromCrmController::class)->name('edit.trade.from.crm');
Route::post('/order', OpenPendingOrderController::class)->name('open.pending.order')->middleware(['auth', 'trade.notify.status']);
Route::delete('/order/{pendingOrderId}', ClosePendingOrderController::class)
    ->name('close.pending.order')->middleware('auth');
Route::put('/order/{orderId}', EditPendingOrderController::class)->name('edit.pending.order')
    ->middleware('auth');
