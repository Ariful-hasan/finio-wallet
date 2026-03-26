<?php

use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post  ('wallets',                    [WalletController::class, 'open']);
    Route::get   ('wallets/{walletId}',         [WalletController::class, 'show']);
    Route::post  ('wallets/{walletId}/deposit', [WalletController::class, 'deposit']);
    Route::post  ('wallets/{walletId}/transfer',[WalletController::class, 'transfer']);
});
