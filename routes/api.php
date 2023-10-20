<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController; 
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\ChatController;
use App\Http\Controllers\api\PaymentController;

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



Route::post('hostRegister', [AuthController::class, 'hostRegister']);
Route::post('hostLogin', [AuthController::class, 'hostLogin']);
Route::post('hostEdit', [AuthController::class, 'hostEdit']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify_otp', [AuthController::class, 'verify_otp']);
Route::post('user_login_log', [AuthController::class, 'user_login_log']);
Route::post('getUsers',[UserController::class,'getUsers']);
Route::get('getPrice',[UserController::class,'getPrice']);
Route::get('getAgency',[UserController::class,'getAgency']);
Route::post('update_token',[AuthController::class,'update_token']);
Route::post('on_off_status',[UserController::class,'onOffStatus']);
Route::post('coin_update',[UserController::class,'coinUpdate']);
Route::post('update-calling-history',[UserController::class,'updateCallingHistory']);
Route::get('get-calling-history', [UserController::class, 'getCallingHistory']);
Route::post('getAllUser',[UserController::class,'getAllUser']);

Route::post('update_subscription',[UserController::class,'update_subscription']);
Route::post('purchase_coin',[UserController::class,'purchase_coin']);
Route::get('agency-coin-history',[UserController::class,'AgencyCoinHistory']);
Route::get('user-coin-history',[UserController::class,'UserCoinHistory']);

Route::get('SendCallNotification',[ChatController::class,'SendCallNotification']);
Route::post('AutoSendMessage',[ChatController::class,'AutoSendMessage']);
Route::post('create-chat', [ChatController::class, 'CreateChat']);
Route::get('get-all-chat/{id}/{limit}', [ChatController::class, 'GetAllChat']);
Route::post('imageUpload', [ChatController::class, 'imageUpload']);
Route::get('personal-chat/{user_id}/{receiver_id}', [ChatController::class, 'PersonalChat']);
Route::get('unread-msg-count', [ChatController::class, 'UnreadMessageCount']);
Route::get('get_all_unread_msg_count', [ChatController::class, 'GetAllUnreadMessageCount']);

Route::post('create-payment-information', [PaymentController::class, 'createPaymentInformation']);
Route::get('get-payment-information', [PaymentController::class, 'getPaymentInformation']);

Route::post('gameupi', [PaymentController::class, 'gameupi']);
Route::post('create-gu-payment-information', [PaymentController::class, 'createGuPaymentInformation']);
Route::get('get-gu-payment-information', [PaymentController::class, 'getGuPaymentInformation']);

Route::post('create-stp-payment-information', [PaymentController::class, 'createStpPaymentInformation']);
Route::get('get-stp-payment-information', [PaymentController::class, 'getStpPaymentInformation']);

Route::get('payment-history', [PaymentController::class, 'paymentHistory']);

Route::group(['middleware' => 'auth:api'], function () {

});
