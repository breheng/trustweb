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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/


$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
  $api->get('/',            ['as' => 'api.home',   'uses' => 'App\Api\V1\Controllers\Controller@home']);
  $api->get('/pg',            ['as' => 'api.pg',   'uses' => 'App\Api\V1\Controllers\Controller@playground']);

  // inventory management
  $api->post('/buildingCreate',  ['as' => 'build.c', 'uses' => 'App\Api\V1\Controllers\InfraController@buildingCreate']);
  $api->post('/buildingSearch',  ['as' => 'build.r', 'uses' => 'App\Api\V1\Controllers\InfraController@buildingSearch']);
  $api->post('/buildingEdit',    ['as' => 'build.u', 'uses' => 'App\Api\V1\Controllers\InfraController@buildingEdit']);
  $api->post('/buildingDelete',  ['as' => 'build.d', 'uses' => 'App\Api\V1\Controllers\InfraController@buildingDelete']);

  $api->post('/seatCreate',  ['as' => 'seat.c', 'uses' => 'App\Api\V1\Controllers\InfraController@seatCreate']);
  $api->post('/seatSearch',  ['as' => 'seat.r', 'uses' => 'App\Api\V1\Controllers\InfraController@seatSearch']);
  $api->post('/seatEdit',    ['as' => 'seat.u', 'uses' => 'App\Api\V1\Controllers\InfraController@seatEdit']);
  $api->post('/seatDelete',  ['as' => 'seat.d', 'uses' => 'App\Api\V1\Controllers\InfraController@seatDelete']);

  $api->get('/massKickOut',  ['as' => 'check.kick.all', 'uses' => 'App\Api\V1\Controllers\InfraController@massKickOut']);
  $api->get('/reserveExpired',  ['as' => 'reserve.expired', 'uses' => 'App\Api\V1\Controllers\InfraController@reserveExpired']);

  // admin->staff kind of thingy
  $api->post('/AdminAddStaff',  ['as' => 'admin.as', 'uses' => 'App\Api\V1\Controllers\AdminController@adminAddStaff']);
  $api->post('/AdminUpdateStaff',  ['as' => 'admin.ups', 'uses' => 'App\Api\V1\Controllers\AdminController@AdminUpdateStaff']);
  // $api->post('/UserLogin',  ['as' => 'user.login', 'uses' => 'App\Api\V1\Controllers\AdminController@doLogin']);

  // LOV management
  $api->post('/LovTaskTypeAdd',  ['as' => 'lov.tt.c', 'uses' => 'App\Api\V1\Controllers\LovController@ttCreate']);
  $api->post('/LovTaskTypeSearch',  ['as' => 'lov.tt.r', 'uses' => 'App\Api\V1\Controllers\LovController@ttSearch']);
  $api->post('/LovTaskTypeEdit',  ['as' => 'lov.tt.u', 'uses' => 'App\Api\V1\Controllers\LovController@ttEdit']);
  $api->post('/LovTaskTypeDelete',  ['as' => 'lov.tt.d', 'uses' => 'App\Api\V1\Controllers\LovController@ttDelete']);

  $api->post('/LovActTypeAdd',  ['as' => 'lov.at.c', 'uses' => 'App\Api\V1\Controllers\LovController@atCreate']);
  $api->post('/LovActTypeSearch',  ['as' => 'lov.at.r', 'uses' => 'App\Api\V1\Controllers\LovController@atSearch']);
  $api->post('/LovActTypeEdit',  ['as' => 'lov.at.u', 'uses' => 'App\Api\V1\Controllers\LovController@atEdit']);
  $api->post('/LovActTypeDelete',  ['as' => 'lov.at.d', 'uses' => 'App\Api\V1\Controllers\LovController@atDelete']);

  $api->get('/CCGet',  ['as' => 'lov.cc.get', 'uses' => 'App\Api\V1\Controllers\LovController@ccGet']);

  // activities
  $api->post('/UserLogin',  ['as' => 'user.login', 'uses' => 'App\Api\V1\Controllers\LoginController@doLogin']);
  $api->post('/UserJustLogin',  ['as' => 'user.j.login', 'uses' => 'App\Api\V1\Controllers\LoginController@justLogin']);

  $api->post('/ReserveSeat',  ['as' => 'user.reserve', 'uses' => 'App\Api\V1\Controllers\UserController@ReserveSeat']);
  $api->post('/ReserveCancel',  ['as' => 'user.reservec', 'uses' => 'App\Api\V1\Controllers\UserController@ReserveCancel']);
  $api->post('/CheckinFromReserve',  ['as' => 'user.checkin.res', 'uses' => 'App\Api\V1\Controllers\UserController@CheckinFromReserve']);
  $api->post('/CheckinDirect',  ['as' => 'user.checkin.dir', 'uses' => 'App\Api\V1\Controllers\UserController@CheckinDirect']);
  $api->post('/CheckOut',  ['as' => 'user.checkout', 'uses' => 'App\Api\V1\Controllers\UserController@CheckOut']);

  $api->post('/seatScanQR',  ['as' => 'seat.qr', 'uses' => 'App\Api\V1\Controllers\InfraController@seatScanQR']);


  // get infos
  $api->post('/UserGetInfo',  ['as' => 'user.info', 'uses' => 'App\Api\V1\Controllers\UserController@getCustInfo']);
  $api->post('/buildingGetSummary',  ['as' => 'build.summary', 'uses' => 'App\Api\V1\Controllers\InfraController@buildingGetSummary']);
  $api->get('/buildingAllSummary',  ['as' => 'build.allsummary', 'uses' => 'App\Api\V1\Controllers\InfraController@buildingAllSummary']);
  $api->post('/buildingListSeats',  ['as' => 'build.listseat', 'uses' => 'App\Api\V1\Controllers\InfraController@buildingListSeats']);
  $api->post('/UserListBuilding',  ['as' => 'user.listbuild', 'uses' => 'App\Api\V1\Controllers\UserController@ListAllowedBuilding']);
  $api->post('/UserFind',  ['as' => 'user.finda', 'uses' => 'App\Api\V1\Controllers\UserController@Find']);

  $api->post('/GiveFeedback',  ['as' => 'api.fb.submit', 'uses' => 'App\Api\V1\Controllers\MiscController@sendFeedback']);

  // GWD stuffs
  $api->post('/gwd/add',  ['as' => 'api.gwd.add', 'uses' => 'App\Api\V1\Controllers\MiscController@GwdAddActivity']);
  $api->post('/gwd/summary',  ['as' => 'api.gwd.sum', 'uses' => 'App\Api\V1\Controllers\MiscController@GwdGetSummary']);
  $api->get('/gwd/acttype',  ['as' => 'api.gwd.type', 'uses' => 'App\Api\V1\Controllers\MiscController@GwdGetActType']);
});
