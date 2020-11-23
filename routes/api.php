<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaFiscalController;

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

Route::get('/', function () {
    return json_encode(['Fernando Bonfim']);
});
Route::get('sincronizarArquivei', [NotaFiscalController::class, 'sync']);
Route::get('notaFiscal/{danfe?}', [NotaFiscalController::class, 'show']);

