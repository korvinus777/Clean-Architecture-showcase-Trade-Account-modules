<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Account\CreateController;
use App\Http\Controllers\Account\DeleteController;
use App\Http\Controllers\Account\SelectController;
use App\Http\Controllers\Account\UpdateController;
use App\Http\Controllers\Account\DepositController;
use App\Http\Controllers\Account\DetailsController;
use App\Http\Controllers\Account\WithdrawalController;
use App\Http\Controllers\Account\LeadAccountsController;

Route::pattern('id', '[0-9]+');
Route::pattern('accountId', '[0-9]+');

Route::get('/{id}', DetailsController::class)->name('details');
Route::post('/{accountId}/select', SelectController::class)
    ->name('select')
    ->middleware('auth');
Route::get('/lead', LeadAccountsController::class)->name('lead');
Route::post('/', CreateController::class)->name('create');
Route::put('/{accountId}', UpdateController::class)->name('update');
Route::delete('/{accountId}', DeleteController::class)->name('delete');
Route::get('/withdrawal-info/{accountId}', [WithdrawalController::class, 'info'])
    ->name('withdrawal-info')
    ->middleware('auth');
Route::get('/{accountId}/deposit-link', [DepositController::class, 'link'])
    ->name('deposit-link')
    ->middleware('auth');
