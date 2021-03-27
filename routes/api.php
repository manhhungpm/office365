<?php

use Illuminate\Http\Request;

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

Route::post('auth/login', 'AuthController@login')->name('login')->middleware('throttle:30,1');
Route::post('/student-code/check', 'StudentCodeController@check')->name('studentCode.check')->middleware('throttle:30,1');
Route::post('/ms-user/guest-store', 'MSUserController@guestStore')->name('msUser.guest-create')->middleware('throttle:10,1');
Route::post('/ms-user/guest-store-api', function (Request $request) {
    return guestStoreApi($request->only('code', 'displayName', 'userPrincipalName',
        'surname', 'givenName', 'password', 'domain_id', 'accountEnabled', 'username'));
});
Route::post('/student-code/check-api', function (Request $request) {
    return studentCodeCheckApi($request->only('code'));
});
//hung them

Route::group([
    'middleware' => ['api', 'auth:api', 'check_status']
], function () {

    includeRouteFiles(__DIR__ . '/Auth/');

    Route::group(['namespace' => 'Admin', 'prefix' => '/admin', 'as' => 'admin.', 'middleware' => []], function () {
        includeRouteFiles(__DIR__ . '/Admin/');
    });

    Route::group(['middleware' => []], function () {
        includeRouteFiles(__DIR__ . '/Common/');
    });
});
