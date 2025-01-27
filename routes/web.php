<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;


Route::get('/',  'HomeController@welcome');
Route::get('/sp/{eCode}', 'BookingController@acknowledgement');
Auth::routes();

Route::get('/admin/home', 'HomeController@index')->name('home');
Route::get('/admin/overview', 'Admin\DashboardController@index')->name('overview');




//Franchisee
Route::post('/admin/franchisee/search', 'Admin\FranchiseeController@search')->name('franchisee.search');
Route::get('/admin/franchisees/{id}/view', 'Admin\FranchiseeController@view')->name('franchisee.view');
Route::get('/admin/booking-partner', 'Admin\FranchiseeController@bookingPartner');
Route::get('/admin/delivery-partner', 'Admin\FranchiseeController@deliveryPartner');
Route::resource('/admin/master/franchisees', 'Admin\FranchiseeController');

//Branch
Route::get('/admin/branch/find', 'Admin\BranchController@find');
Route::resource('/admin/master/branches','Admin\BranchController');
Route::get('/admin/serviceable-branches', 'Admin\BranchController@serviceableBranches');
Route::get('admin/office-list', 'Admin\BranchController@officeList');

//Employee
Route::get('/admin/employee/selectedBranch', 'Admin\EmployeeController@selectedBranch');
Route::get('/admin/employee/findBranch', 'Admin\EmployeeController@findBranch');
Route::get('/admin/employee/{id}/view', 'Admin\EmployeeController@view')->name('employee.view');
Route::resource('/admin/master/employees','Admin\EmployeeController');

//Subscription
Route::resource('/admin/master/plans','Admin\SubscriptionController');

//Pricing
Route::resource('/admin/master/pricings','Admin\PricingController');
Route::get('/admin/pricing-details', 'Admin\PricingController@pricingDetails')->name('pricing-details');

//Customer
Route::resource('/admin/master/customers','Admin\CustomerController');
Route::get('/admin/customer-search', 'Admin\CustomerController@search');
Route::get('/admin/customer/search', 'Admin\CustomerController@customerSearch')->name('customer_search');
Route::get('/admin/customer-details', 'Admin\CustomerController@customerDetails');
Route::resource('/admin/master/pricing','Admin\PricingController');

//Pincodes
Route::resource('/admin/master/pincodes', 'Admin\PincodeController');
Route::get('/admin/pincode-details', 'Admin\PincodeController@pincodeDetails')->name('pincode_details');
Route::get('/admin/pincodes/findPincode', 'Admin\PincodeController@findPincode');

//Subscriptions
Route::resource('/admin/master/subscriptions','Admin\SubscriptionController');
Route::get('/admin/subscription-list', 'Admin\SubscriptionController@subscriptionList');

//Bookings
Route::get('/admin/booking/calculate-volumetric-weight', 'Admin\BookingController@calculateVolumetricWeight')->name('calculate-weight');
Route::get('/admin/bookings/bulk', 'Admin\BookingController@bulkBooking')->name('bookings.bulk');
Route::post('/admin/bookings/import', 'Admin\BookingController@import')->name('bookings.import');
Route::get('/admin/bookings/validate/{batchId}', 'Admin\BookingController@validateExcel')->name('bookings.validate');
Route::get('/admin/bookings/bulk-booking-details', 'Admin\BookingController@bulkBookingDetails');
Route::put('/admin/bookings/sheet-update', 'Admin\BookingController@sheetUpdate')->name('bookings.row-update');
Route::delete('/admin/bookings/row-delete', 'Admin\BookingController@rowDelete')->name('bookings.row-delete');
Route::get('/admin/testEmail', 'Admin\BookingController@testMail');
Route::get('/admin/bookings/bulk-create/{batchId}', 'Admin\BookingController@bulkCreate')->name('bulk-booking.create');
Route::resource('/admin/bookings', 'Admin\BookingController');
Route::get('/admin/booking/sms', 'Admin\BookingController@sendSMSTest');
Route::get('/admin/emailtest', 'Admin\BookingController@emailTest');

// Route::get('/admin/booking/email-test', 'Admin\BookingController@emailTest');
Route::get('/admin/booking/download-bb-sample', 'Admin\BookingController@getBulkBookingSample')->name('download-bb-sample');

Route::get('/admin/bookings/{id}/view', 'Admin\BookingController@view')->name('bookings.view');

//Manifest
Route::get('/admin/booking-details', 'Admin\ManifestController@bookingDetails');
Route::get('/admin/branch-franchisee', 'Admin\ManifestController@branchFranchisee');
Route::get('/admin/booking/download-manifest-sample', 'Admin\Import\ManifestController@getManifestSample')->name('download-manifest-sample');

//Manifest Incoming
Route::get('/admin/manifests/incoming', 'Admin\ManifestController@incoming')->name('manifests.incoming');
Route::get('/admin/manifests/incoming/create', 'Admin\ManifestController@incomingCreate')->name('manifests.incoming.create');
Route::get('/admin/manifests/incoming/{id}/edit', 'Admin\ManifestController@edit')->name('manifests.incoming_edit');
Route::put('/admin/manifests/incoming/{id}', 'Admin\ManifestController@update')->name('manifests.incoming_update');
Route::delete('/admin/manifests/incoming/{id}/delete', 'Admin\ManifestController@destroy')->name('manifests.incoming_delete');
Route::post('/admin/manifests/incoming/store', 'Admin\ManifestController@store')->name('manifests.incoming_store');
Route::view('/admin/manifests/incoming/import', 'manifests.import-incoming')->name('in-manifest-import')->middleware('auth');
Route::post('/admin/manifests/incoming/import/create', 'Admin\Import\ManifestController@incomingImport')->name('manifests.import.incoming.create');

//Manifest Outgoing
Route::get('/admin/manifests/outgoing', 'Admin\ManifestController@outgoing')->name('manifests.outgoing');
Route::get('/admin/manifests/outgoing/create', 'Admin\ManifestController@outgoingCreate')->name('manifests.outgoing.create');
Route::get('/admin/manifests/outgoing/{id}/edit', 'Admin\ManifestController@edit')->name('manifests.outgoing_edit');
Route::put('/admin/manifests/outgoing/{id}', 'Admin\ManifestController@update')->name('manifests.outgoing_update');
Route::delete('/admin/manifests/outgoing/{id}/delete', 'Admin\ManifestController@destroy')->name('manifests.outgoing_delete');
Route::post('/admin/manifests/outgoing/store', 'Admin\ManifestController@store')->name('manifests.outgoing_store');
Route::view('/admin/manifests/outgoing/import', 'manifests.import-outgoing')->name('out-manifest-import')->middleware('auth');
Route::post('/admin/manifests/outgoing/import/create', 'Admin\Import\ManifestController@outgoingImport')->name('manifests.import.outgoing.create');
//Route::resource('/admin/manifests', 'Admin\ManifestController');

//Return
Route::get('/admin/booking-details-for-returns', 'Admin\ManifestController@bookingDetailsForReturns');
Route::get('/admin/return-branch-partner', 'Admin\ReturnsController@branchPartners');
Route::get('/admin/returns/incoming/create', 'Admin\ReturnsController@incomingCreate')->name('returns.incoming.create');
Route::post('/admin/returns/incoming/store', 'Admin\ReturnsController@store')->name('returns.store');
Route::get('/admin/returns/outgoing/create', 'Admin\ReturnsController@outgoingCreate')->name('returns.outgoing.create');
//Route::resource('/admin/master/consignments', 'Admin\ConsignmentController');

//Dispatch
Route::resource('/admin/dispatches','Admin\DispatchController');

//mode
Route::resource('/admin/modes','Admin\ModeController');

//Reasons
Route::resource('/admin/master/reasons', 'Admin\ReasonController');

//Consignments
Route::get('/admin/consignments', 'Admin\ConsignmentController@index')->name('consignments.index');
Route::get('/admin/consignments/create', 'Admin\ConsignmentController@create')->name('consignments.create');
Route::get('/admin/consignments/reprint/{batchId}', 'Admin\ConsignmentController@reprint')->name('consignments.reprint');
Route::post('/admin/consignments/print', 'Admin\ConsignmentController@print')->name('consignments.print');
Route::get('/admin/officeSearch', 'Admin\ConsignmentController@searchOfficeId')->name('office.search');
Route::get('/admin/consignments/export/{batchId}', 'Admin\ConsignmentController@consignmentExport')->name('consignments.export');

//acknowledgement
Route::get('/booking/acknowledgement/{eCode}', 'BookingController@acknowledgement')->name('acknowledgement');

//tracking
Route::resource('/admin/tracking', 'Admin\TrackingController');
Route::get('/booking/{batchId}', 'BookingController@index')->name('bookings.track');
Route::get('/track', 'BookingController@tracking')->name('public.tracking');

//Roles
Route::resource('/admin/master/roles', 'Admin\RoleController');

//Permissions
Route::resource('/admin/master/permissions', 'Admin\PermissionController');

//Module
Route::get('/admin/enabled-modules', 'Admin\ModuleController@index')->name('enabled_modules');

//Runsheet
Route::get('/admin/runsheet', 'Admin\RunsheetController@create')->name('runsheet.add');
Route::get('/admin/runsheet-validate', 'Admin\RunsheetController@runsheetValidation')->name('runsheet.validate');
Route::post('/admin/runsheet/create', 'Admin\RunsheetController@createRunsheet')->name('runsheet.create');

//Reports
Route::get('/admin/reports/shipment', 'Admin\ReportsController@shipmentReport')->name('shipment.report');
Route::get('/admin/reports/sales-by-partner', 'Admin\ReportsController@salesByPartnerReport')->name('salesByPartner.report');
//Route::get('/admin/bookings/report', 'Admin\BookingController\report')->name('booking.report');
