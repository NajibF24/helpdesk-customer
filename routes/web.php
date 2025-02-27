<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes(['register' => false]);

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    // return what you want
    echo "cache clear";
});
Route::get('/config-clear', function() {
    $exitCode = Artisan::call('config:clear');
    // return what you want
    echo "config clear";
});

Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    // return what you want
    echo "optimize";
});
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    // return what you want
    echo "route clear";
});
Route::get('/', function () {
    return redirect()->route('home');
});

Route::post('/login', 'App\Http\Controllers\LoginController@authenticate');
Route::get('/logout', 'App\Http\Controllers\LoginController@logout')->name('logout');
Route::post('/logout', 'App\Http\Controllers\LoginController@logout')->name('post-logout');
Route::get('/onetime_login_token/{token}', 'App\Http\Controllers\LoginController@onetime_login_token');

Route::get('/tesemail/{email}/{title}/{message}','App\Http\Controllers\CrudController@tesemail');
Route::get('email_approve/{token}','App\Http\Controllers\ApproveRequestController@email_approve');
Route::get('email_approve_inventory/{token}','App\Http\Controllers\ApproveRequestController@email_approve_inventory');

Route::get('ticket_detail/{token}','App\Http\Controllers\ApproveRequestController@ticket_detail');

Route::group(['middleware' => ['auth']], function() {

	Route::get('logcheck647', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

	Route::get('/openApps/{apps}', 'App\Http\Controllers\HomeController@openApps');

	Route::get('activity_stream', [App\Http\Controllers\HomeController::class, 'activity_stream']);
    Route::get('notification', [App\Http\Controllers\HomeController::class, 'notification']);
	Route::get('messages', [App\Http\Controllers\HomeController::class, 'messages']);
	Route::post('set-read-notification', [App\Http\Controllers\HomeController::class, 'set_read_notification']);
	Route::get('dashboard_created_resolved_reload', [App\Http\Controllers\HomeController::class, 'dashboard_created_resolved_reload']);

	Route::get('change_password', 'App\Http\Controllers\UsersController@change_password');
	Route::post('change_password_update', 'App\Http\Controllers\UsersController@change_password_update')->name('change_password_update');

	Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
	Route::get('/home/material_list_report', [App\Http\Controllers\HomeController::class, 'getMaterialListReport'])->name('home.material_list_report');
	Route::get('view_notif/{id}', 'App\Http\Controllers\NotifController@view_notif');
	//Route::get('/home2', [App\Http\Controllers\HomeController::class, 'index2'])->name('home2');
	Route::get('/getlog', [App\Http\Controllers\HomeController::class, 'getlog'])->name('getlog');
	Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'dashboard'])->name('dashboard');

    Route::resource('service-catalog','App\Http\Controllers\ServiceCatalogController');
    Route::get('request-service', 'App\Http\Controllers\ServiceCatalogController@request_service');
    Route::get('request-service/service-catalog/{id}', 'App\Http\Controllers\ServiceCatalogController@service_catalog');

    Route::get('request-incident/incident-catalog/{id}', 'App\Http\Controllers\ServiceCatalogController@incident_catalog');

    Route::get('service-catalog/create/{id}', 'App\Http\Controllers\ServiceCatalogController@create_service');

	Route::get('/article', 'App\Http\Controllers\ArticleController@index')->name('article-index');
	Route::get('/ajax_category', 'App\Http\Controllers\ArticleController@ajax_category')->name('ajax_category');
	Route::get('/get_faq_list', 'App\Http\Controllers\ArticleController@get_faq_list')->name('get_faq_list');
	Route::get('/get_faq', 'App\Http\Controllers\ArticleController@get_faq')->name('get_faq');
	Route::get('/faq', 'App\Http\Controllers\ArticleController@index')->name('faq');

	Route::get('list/{type}','App\Http\Controllers\CrudController@listItem')->name('list');
	Route::get('list/{type}/{param1}','App\Http\Controllers\CrudController@listItem')->name('list_disposal');
	Route::get('listServer/{type}', 'App\Http\Controllers\CrudController@listServer');
	Route::get('listServer/{type}/{param1}', 'App\Http\Controllers\CrudController@listServer');
	Route::get('create/{type}','App\Http\Controllers\CrudController@create')->name('create');
	Route::get('create/{type}/{modal}/{select_target}','App\Http\Controllers\CrudController@create')->name('create_modal');
	Route::get('create/{type}/{modal}/{select_target}/{param1}','App\Http\Controllers\CrudController@create')->name('create_modal_2');
	//Route::post('create/{type}','App\Http\Controllers\CrudController@create')->name('create');
	Route::post('store/{type}','App\Http\Controllers\CrudController@store')->name('store');
	Route::post('store/{type}/{modal}','App\Http\Controllers\CrudController@store')->name('store_modal');
	Route::get('edit/{id}/{type}','App\Http\Controllers\CrudController@edit')->name('edit');
	//Route::post('edit/{id}/{type}','App\Http\Controllers\CrudController@edit')->name('edit');
	Route::get('edit/{id}/{type}/{param1}','App\Http\Controllers\CrudController@edit');
	Route::post('edit/{id}/{type}/{param1}','App\Http\Controllers\CrudController@edit');
	Route::post('update/{id}/{type}','App\Http\Controllers\CrudController@update')->name('update');
	Route::get('delete/{id}/{type}','App\Http\Controllers\CrudController@delete')->name('delete');
	Route::get('show/{id}/{type}','App\Http\Controllers\CrudController@show')->name('show');

	Route::get('/select2list/{object}','App\Http\Controllers\CrudController@select2list')->name('select2list');

	Route::get('choose_incident','App\Http\Controllers\CrudController@choose_incident')->name('choose_incident');
	Route::get('choose_incid','App\Http\Controllers\CrudController@choose_incident')->name('choose_service');

	Route::resource('ticket-monitoring', 'App\Http\Controllers\TicketMonitoringController');
	Route::get('myDraft','App\Http\Controllers\TicketMonitoringController@myDraft')->name('myDraft');
	Route::get('myServices','App\Http\Controllers\TicketMonitoringController@myServices')->name('myServices');
	Route::get('myIncidents','App\Http\Controllers\TicketMonitoringController@myIncidents')->name('myIncidents');
	Route::get('myAssignments','App\Http\Controllers\TicketMonitoringController@myAssignments')->name('myAssignments');
	Route::post('replyComment','App\Http\Controllers\TicketMonitoringController@replyComment')->name('replyComment');


	Route::resource('approve-request', 'App\Http\Controllers\ApproveRequestController');



	Route::get('/sendNotifEmail','App\Http\Controllers\CrudController@sendNotifEmail');
	Route::post('ticketAction','App\Http\Controllers\CrudController@ticketAction');
	Route::post('approve-request/ticketAction','App\Http\Controllers\ApproveRequestController@ticketAction');
	Route::post('approve-request/ticketActionInventory','App\Http\Controllers\ApproveRequestController@ticketActionInventory');
	Route::post('approve-request/ticket_reject','App\Http\Controllers\ApproveRequestController@ticket_reject');
	Route::post('approve-request/ticket_delete','App\Http\Controllers\ApproveRequestController@ticket_delete');
	Route::post('approve-request/{id}/reject', 'App\Http\Controllers\ApproveRequestController@rejectGoodsIssue')->name('approve_request.reject_inventory');
	Route::post('approve-request/{id}/reject-goods-receive', 'App\Http\Controllers\ApproveRequestController@rejectGoodsReceive')->name('approve_request.reject_goods_receive');

	Route::post('ckeditor/upload', 'App\Http\Controllers\CKEditorController@upload')->name('ckeditor.image-upload');
	Route::post('send_rating','App\Http\Controllers\CrudController@send_rating');

	Route::post('summer_upload', 'App\Http\Controllers\CKEditorController@summer_upload')->name('ckeditor.summer_upload');
	Route::get('download_faq_pdf/{id}', 'App\Http\Controllers\ArticleController@download_pdf_faq');
	Route::resource('search', 'App\Http\Controllers\SearchController');

    Route::get('dashboard_reload', [App\Http\Controllers\HomeController::class, 'dashboard_reload']);
	Route::get('dashboard_chart_reload', [App\Http\Controllers\HomeController::class, 'dashboard_chart_reload']);

	Route::resource('report', 'App\Http\Controllers\ReportController');

	Route::get('material/material_code/detail/{id}', 'App\Http\Controllers\MaterialController@getMaterialCodeDetail');
	Route::get('material/detail/{id}', 'App\Http\Controllers\MaterialController@getMaterialDetail');
	Route::get('material/store_location_list/{warehouseId}', 'App\Http\Controllers\MaterialController@getStoreLocationList')->name('material.store-location-list');

	Route::get('goods_issue/catalog', 'App\Http\Controllers\GoodsIssueController@catalog');
	Route::get('goods_issue/material_code_list', 'App\Http\Controllers\GoodsIssueController@getMaterialCodeList')->name('goods_issue.material-code-list');
	Route::get('goods_issue/material_code/{id}/material_list', 'App\Http\Controllers\GoodsIssueController@getMaterialList')->name('goods_issue.material-list');
	Route::post('goods_issue/assign_material/{materialId}/{goodsIssueDetailId}', 'App\Http\Controllers\GoodsIssueController@assignMaterial')->name('goods_issue.assign_material');
	Route::resource('goods_issue', 'App\Http\Controllers\GoodsIssueController');
	Route::post('inventory/comment', 'App\Http\Controllers\GoodsIssueController@replyComment');
	Route::get('goods_issue/check_material_code/{materialCodeId}', 'App\Http\Controllers\GoodsIssueController@checkMaterialCode');

	Route::get('goods_receive/catalog', 'App\Http\Controllers\GoodsReceiveController@catalog');
	Route::get('goods_receive/material_code_list', 'App\Http\Controllers\GoodsReceiveController@getMaterialCodeList')->name('goods_receive.material-code-list');
	Route::get('goods_receive/material_code/{id}/material_list', 'App\Http\Controllers\GoodsReceiveController@getMaterialList')->name('goods_receive.material-list');
	Route::post('goods_receive/assign_material/{materialId}/{goodsIssueDetailId}', 'App\Http\Controllers\GoodsReceiveController@assignMaterial')->name('goods_receive.assign_material');
	Route::resource('goods_receive', 'App\Http\Controllers\GoodsReceiveController');
	Route::get('goods_receive/get-issue-data/{id}', 'App\Http\Controllers\GoodsReceiveController@getIssueData')->name('goods_receive.get-issue-data');


	// Route::get('inventory_list', 'App\Http\Controllers\InventoryListController@index')->name('inventory_list.index');
	Route::get('inventory_list/{id}/edit', 'App\Http\Controllers\InventoryListController@edit')->name('inventory_list.edit');
	Route::put('inventory_list/{id}/update', 'App\Http\Controllers\InventoryListController@update')->name('inventory_list.update');

	// Route::get('inventory_report', 'App\Http\Controllers\InventoryReportController@inventoryReport')->name('inventory_report.index');
	Route::get('inventory_report/export', 'App\Http\Controllers\InventoryReportController@inventoryReportExport')->name('inventory_report.export');
	Route::get('inventory_transaction_report/export', 'App\Http\Controllers\InventoryReportController@inventoryTransactionReportExport')->name('inventory_transaction_report.export');
	// Route::get('inventory_transaction_report', 'App\Http\Controllers\InventoryReportController@inventoryTransactionReport')->name('inventory_transaction_report.index');
	Route::post('/update_next_approver/{ticketId}','App\Http\Controllers\CrudController@updateNextApprover');


  // Route::get('/files/{filename}', function ($filename) {
  //     $path = public_path("uploads/{$filename}");
  //     if (!file_exists($path)) abort(404);

  //     return response()->download($path, $filename, [
  //       'Content-Disposition' => 'attachment',
  //   ]);
  // });
});
