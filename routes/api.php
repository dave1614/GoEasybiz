<?php

use App\Http\Controllers\ProvidusController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::group(['auth:sanctum'],function() {
    Route::get('/test_login',[TestController::class,'testLogin'])->middleware('auth:sanctum');
// });



Route::get('/test',[TestController::class,'index']);

Route::post('/setup_project',[SetupController::class,'store']);
Route::post('/get_phone_codes_for_countrs', [SetupController::class, 'setPhoneCodes']);
Route::post('/credit_all_users', [SetupController::class, 'creditAllUsers']);
Route::post('/add_withdrawal_hist', [SetupController::class, 'addWithdrawalHistory']);
Route::post('/add_transfer_hist', [SetupController::class, 'addTransferHistory']);
Route::post('/add_admin_credi_hist', [SetupController::class, 'addAdminCreditHistory']);
Route::post('/add_admin_debi_hist', [SetupController::class, 'addAdminDebitHistory']);
Route::post('/add_withdrawal_requ', [SetupController::class, 'addWithdrawalRequesttHistory']);
Route::post('/add_airtime_comb_requ', [SetupController::class, 'addairtimeComboRequests']);
Route::post('/add_data_comb_requ', [SetupController::class, 'addDataComboRequests']);
Route::post('/test_email', [SetupController::class, 'testEmail']);
Route::post('/test_reloadly', [SetupController::class, 'testReloadly']);
Route::post('/test_monnify', [SetupController::class, 'testMonnify']);












Route::any('/receive_payscribe_webhooks', [ProvidusController::class, 'recievePayscribeWebhooks']);
Route::any('/receive_providus_webhooks', [ProvidusController::class, 'recieveProvidusWebhooks']);
Route::any('/receive_monnify_webhooks', [ProvidusController::class, 'recieveMonnifyWebhooks']);
