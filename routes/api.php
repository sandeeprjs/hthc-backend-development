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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('/test', 'Admin\UserController@test');
Route::post('/login', 'Api\UserController@login');

Route::post('/my-consignments', 'Api\ConsignmentController@index');
//Route::post('/out-for-delivery', 'Api\ConsignmentController@outForDelivery');
Route::post('/confirm-delivery', 'Api\DeliveryController@store');
Route::post('/save-runsheet', 'Api\ConsignmentController@saveRunsheet');
Route::post('/consignment-return', 'Api\DeliveryController@consignmentReturn');

// Route::post('/file-upload', 'Api\DeliveryController@fileUpload');

Route::get('/plans', 'Api\BookingController@getPlans');
Route::get('/pincodes', 'Api\BookingController@getPincodes');
Route::post('/get-customer', 'Api\BookingController@getCustomer');
Route::post('/pricing-details', 'Api\BookingController@pricingDetails');

Route::post('/booking', 'Api\BookingController@booking');

Route::post('/reasons', 'Api\DeliveryController@getReasons');
Route::get('/branch-codes', 'Api\DeliveryController@getBranchFranchisee');
Route::post('/get-emp-photo', 'Api\UserController@getEmpPhoto');

Route::post('/my-consignmentstestone', 'Api\ConsignmentController@indextestone');

Route::post('/today-consignment-count', 'Api\ConsignmentController@todayConsignmentCount');

Route::post('/scan-runsheet', 'Api\ConsignmentController@addRunsheet');