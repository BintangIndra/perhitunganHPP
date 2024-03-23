<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransaksiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login', [AuthController::class,'login']);

Route::prefix('transaksi')->group(function () {
    Route::post('/', [TransaksiController::class, 'store']);
    Route::put('/{id}', [TransaksiController::class, 'update']);
    Route::delete('/{id}', [TransaksiController::class, 'destroy']);
    Route::get('/', [TransaksiController::class, 'index']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
