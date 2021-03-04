<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    // return $router->app->version();

    $return['message'] = 'Hello world from API Liquid :D';
    return json_encode($return);
});

$router->group(['prefix' => 'report/v1'], function() use($router){
    
    $router->group(['prefix' => 'report_general'], function() use($router){
		$router->get('/total_so_jobs_invalid', 'Report\v1\ReportGeneralController@total_so_jobs_invalid');
		$router->get('/total_so_value', 'Report\v1\ReportGeneralController@total_so_value');
        $router->get('/total_remaining_quota_division', 'Report\v1\ReportGeneralController@total_remaining_quota_division');
        $router->get('/total_remaining_quota_site', 'Report\v1\ReportGeneralController@total_remaining_quota_site');
		$router->get('/total_remaining_quota_user', 'Report\v1\ReportGeneralController@total_remaining_quota_user');
		$router->get('/total_order_value_mf_nonmf', 'Report\v1\ReportGeneralController@total_order_value_mf_nonmf');
		$router->get('/testing', 'Report\v1\ReportGeneralController@testing');
		$router->get('/sum_site', 'Report\v1\ReportGeneralController@sum_site');
		$router->get('/get_list_article', 'Report\v1\ReportGeneralController@get_list_article');
		$router->get('/get_qty_value_article', 'Report\v1\ReportGeneralController@get_qty_value_article');
		$router->get('/get_sum_company', 'Report\v1\ReportGeneralController@get_sum_company');
		$router->get('/get_sum_user', 'Report\v1\ReportGeneralController@get_sum_user');
		$router->get('/get_discrepancy_cc', 'Report\v1\ReportGeneralController@get_discrepancy_cc');
		$router->get('/get_fulfillment_po', 'Report\v1\ReportGeneralController@get_fulfillment_po');
		$router->get('/get_list_article_need_create_po', 'Report\v1\ReportGeneralController@get_list_article_need_create_po');
		$router->get('/get_sum_active_article_po', 'Report\v1\ReportGeneralController@get_sum_active_article_po');
		$router->get('/get_out_of_stock_article', 'Report\v1\ReportGeneralController@get_out_of_stock_article');
		$router->get('/get_outstanding_od_for_gr', 'Report\v1\ReportGeneralController@get_outstanding_od_for_gr');
		$router->get('/get_user_transaction', 'Report\v1\ReportGeneralController@get_user_transaction');
    });
    
		
	$router->group(['prefix' => 'report_purchasing'], function() use($router){
    	$router->get('/get_list', 'Report\v1\ReportPurchasingController@get_list');
    	$router->get('/article_qty_below_safety_stock', 'Report\v1\ReportPurchasingController@article_qty_below_safety_stock');
    	$router->get('/article_value_below_safety_stock', 'Report\v1\ReportPurchasingController@article_value_below_safety_stock');
    	$router->get('/number_of_po', 'Report\v1\ReportPurchasingController@number_of_po');
    	$router->get('/fulfillment_po', 'Report\v1\ReportPurchasingController@fulfillment_po');
    	$router->get('/fulfillment_qty_po', 'Report\v1\ReportPurchasingController@fulfillment_qty_po');
    	$router->get('/article_need_create_po', 'Report\v1\ReportPurchasingController@article_need_create_po');
    	$router->get('/report_po_fulfill_unfulfill', 'Report\v1\ReportPurchasingController@report_po_fulfill_unfulfill');
    	$router->get('/report_total_below_po', 'Report\v1\ReportPurchasingController@report_total_below_po');
    	$router->get('/report_article_value_qty_kls_nonkls', 'Report\v1\ReportPurchasingController@report_article_value_qty_kls_nonkls');
	});

	$router->group(['prefix' => 'report_logistic'], function() use($router){
    	$router->get('/list_of_article', 'Report\v1\ReportLogisticController@list_of_article');
    	$router->get('/value_of_article', 'Report\v1\ReportLogisticController@value_of_article');
		$router->get('/qty_of_article', 'Report\v1\ReportLogisticController@qty_of_article');
		$router->get('/number_of_sto', 'Report\v1\ReportLogisticController@number_of_sto');
		$router->get('/number_of_sto_in', 'Report\v1\ReportLogisticController@number_of_sto_in');
		$router->get('/number_of_sto_out', 'Report\v1\ReportLogisticController@number_of_sto_out');
		$router->get('/discrepancy_article_gr', 'Report\v1\ReportLogisticController@discrepancy_article_gr');
        $router->get('/transaction_kitting', 'Report\v1\ReportLogisticController@transaction_kitting');
        
		// $router->get('/transaction_kitting', 'Report\v1\ReportLogisticController@transaction_kitting');
	});

	$router->group(['prefix' => 'report_sales'], function() use($router){
		$router->get('/sales_order_per_site', 'Report\v1\ReportSalesController@sales_order_per_site');
    	$router->get('/sales_order_per_site_division_maintenance', 'Report\v1\ReportSalesController@sales_order_per_site_division_maintenance');
    	$router->get('/sales_order_per_site_division_production', 'Report\v1\ReportSalesController@sales_order_per_site_division_production');
		$router->get('/sales_order_per_site_division_ga', 'Report\v1\ReportSalesController@sales_order_per_site_division_ga');
		$router->get('/order_transaction_month', 'Report\v1\ReportSalesController@order_transaction_month');
		$router->get('/quota_site', 'Report\v1\ReportSalesController@quota_site');
		$router->get('/quota_user', 'Report\v1\ReportSalesController@quota_user');
	});

	$router->group(['prefix' => 'report_admin'], function() use($router){
		$router->get('/list_company', 'Report\v1\ReportAdminController@list_company');
    	$router->get('/role_maintenance', 'Report\v1\ReportAdminController@role_maintenance');
    	$router->get('/manage_level', 'Report\v1\ReportAdminController@manage_level');
		$router->get('/manage_reason', 'Report\v1\ReportAdminController@manage_reason');
		$router->get('/created_site', 'Report\v1\ReportAdminController@created_site');
		$router->get('/list_of_user', 'Report\v1\ReportAdminController@list_of_user');
		$router->get('/manage_quota', 'Report\v1\ReportAdminController@manage_quota');
		$router->get('/manage_reason_type', 'Report\v1\ReportAdminController@manage_reason_type');
	});

	$router->group(['prefix' => 'report_replenishment'], function() use($router){
		$router->get('/mapping_rfid', 'Report\v1\ReportReplenishmentController@mapping_rfid');
        $router->get('/discrepancy_of_cycle_counting', 'Report\v1\ReportReplenishmentController@discrepancy_of_cycle_counting');
        $router->get('/disc_cc_value_qty', 'Report\v1\ReportReplenishmentController@disc_cc_value_qty');
        
        // article done mapping and unmapping - sum only
        $router->get('/list_unmapping_rfid', 'Report\v1\ReportReplenishmentController@list_unmapping_rfid');
        

	});

	
	
});

$router->group(['prefix' => 'api/v1', ], function () use ($router)
{		
    // $router->group(['prefix' => 'api/v1', ], function () use ($router)
    // {		
    
    // },

    $router->group(['prefix' => 'cron', ], function () use ($router)
    {
        $router->get('/', 'Api\v1\CronController@index');
        $router->get('/index', 'Api\v1\CronController@index');
        $router->get('/update_article_in_article_po', 'Api\v1\CronController@update_article_in_article_po');
        // $router->get('/', 'Api\v1\CronController@');
        $router->get('/insert_ignore_article_stock_empty', 'Api\v1\CronController@insert_ignore_article_stock_empty');
        $router->get('/update_logistic_site_detail_description', 'Api\v1\CronController@update_logistic_site_detail_description');
        $router->get('/update_article_source_trx', 'Api\v1\CronController@update_article_source_trx');
        $router->get('/update_article_source_gr', 'Api\v1\CronController@update_article_source_gr');
        $router->get('/update_article_source_kitting', 'Api\v1\CronController@update_article_source_kitting');
        $router->get('/update_order_from_sc', 'Api\v1\CronController@update_order_from_sc');
        $router->get('/update_remaining_qty_po', 'Api\v1\CronController@update_remaining_qty_po');
        // new update sc_order_id and so_sap to tr_transaction
        $router->get('/update_sc_so_to_trx', 'Api\v1\CronController@update_sc_so_to_trx');
        $router->get('/update_trx_so_sap', 'Api\v1\CronController@update_trx_so_sap');

        // api for email template
        $router->get('/trigger_safety_stock', 'Api\v1\CronController@trigger_safety_stock');
        $router->get('/report_articlepo_job_error', 'Api\v1\CronController@report_articlepo_job_error');
    });

    $router->group(['prefix' => 'report_global', ], function () use ($router)
    {
        $router->get('/order_transaction_per_month', 'Api\v1\ReportGlobalController@order_transaction_per_month');
        $router->get('/article_below_safety_stock_po', 'Api\v1\ReportGlobalController@article_below_safety_stock_po');
    });

    $router->get('/loging', 'Api\v1\LogController@get_list_status');
    $router->get('/log', 'Api\v1\LogController@get_list');
    $router->get('/log/get_list_status', 'Api\v1\LogController@get_list_status');
    $router->get('/log/get', 'Api\v1\LogController@get');
    $router->post('/log', 'Api\v1\LogController@save');
    $router->put('/log', 'Api\v1\LogController@update');
    $router->delete('/log', 'Api\v1\LogController@delete');

    // $router->get('/log/list', 'Api\v1\LogController@get_list');
	
	$router->get('/article', 'Api\v1\ArticleController@get_list');
	$router->get('/article/get_list', 'Api\v1\ArticleController@get_list');
	$router->get('/article/get_list_export', 'Api\v1\ArticleController@get_list_export');
	$router->get('/article/get_list_with_stock', 'Api\v1\ArticleController@get_list_with_stock');
	$router->get('/article/get_ajax', 'Api\v1\ArticleController@get_ajax');
	$router->get('/article/get_list_dropdown', 'Api\v1\ArticleController@get_list_dropdown');
	$router->get('/article/get_list_dropdown_by_site', 'Api\v1\ArticleController@get_list_dropdown_by_site');
	$router->get('/article/get', 'Api\v1\ArticleController@get');
	$router->post('/article', 'Api\v1\ArticleController@save');
	$router->post('/article/save_bulk', 'Api\v1\ArticleController@save_bulk');
	$router->post('/article/save_update_bulk', 'Api\v1\ArticleController@save_update_bulk');
	$router->post('/article/save_bulk_art_stk', 'Api\v1\ArticleController@save_bulk_art_stk');
	$router->put('/article', 'Api\v1\ArticleController@update');
    $router->delete('/article', 'Api\v1\ArticleController@delete');
    $router->get('/article/get_qty_value', 'Api\v1\ArticleController@get_qty_value');

	$router->get('/article_place', 'Api\v1\ArticlePlaceController@get');
	$router->get('/article_place/get_list', 'Api\v1\ArticlePlaceController@get_list');
	$router->get('/article_place/get_list_ajax', 'Api\v1\ArticlePlaceController@get_list_ajax');
	$router->post('/article_place/save_bulk', 'Api\v1\ArticlePlaceController@save_bulk');
	$router->post('/article_place/save_update_bulk', 'Api\v1\ArticlePlaceController@save_update_bulk');
	$router->post('/article_place', 'Api\v1\ArticlePlaceController@save');
	$router->put('/article_place', 'Api\v1\ArticlePlaceController@update');
    $router->delete('/article_place', 'Api\v1\ArticlePlaceController@delete');
	
	$router->get('/company', 'Api\v1\CompanyController@get_list');
	$router->get('/company/get', 'Api\v1\CompanyController@get');
	$router->get('/company/get_by_site_id', 'Api\v1\CompanyController@get_by_site_id');
	$router->get('/company/get_list', 'Api\v1\CompanyController@get_list');
	$router->get('/company/get_list_dropdown', 'Api\v1\CompanyController@get_list_dropdown');
	$router->post('/company', 'Api\v1\CompanyController@save');
	$router->put('/company', 'Api\v1\CompanyController@update');
    $router->delete('/company', 'Api\v1\CompanyController@delete');


	$router->group(['prefix' => '/user', ], function () use ($router)
    {		
		$router->get('', 'Api\v1\UserController@get');
		$router->post('', 'Api\v1\UserController@save');
		$router->put('', 'Api\v1\UserController@update');
		$router->delete('', 'Api\v1\UserController@delete');		
		$router->get('get', 'Api\v1\UserController@get');
		$router->get('get_list', 'Api\v1\UserController@get_list');
		$router->get('get_list_dropdown', 'Api\v1\UserController@get_list_dropdown');
		$router->get('get_list_bottom_level_child', 'Api\v1\UserController@get_list_bottom_level_child');
		$router->get('insert_quota_by_site_id', 'Api\v1\UserController@insert_quota_by_site_id');
		$router->get('get_user', 'Api\v1\UserController@get_user');
    });

    // $router->get('/user', 'Api\v1\UserController@get_list');
    // $router->get('/user/get_list_dropdown', 'Api\v1\UserController@get_list_dropdown');
	// $router->get('/user/get_list', 'Api\v1\UserController@get_list');
	// // $router->get('/user/get_level_by_user', 'Api\v1\UserController@get_level_by_user');
	// $router->get('/user/insert_quota_by_site_id', 'Api\v1\UserController@insert_quota_by_site_id');
	// $router->get('/user/get', 'Api\v1\UserController@get');
	// $router->post('/user', 'Api\v1\UserController@save');
	// $router->put('/user', 'Api\v1\UserController@update');
	// $router->delete('/user', 'Api\v1\UserController@delete');

	$router->get('/user/get', 'Api\v1\UserController@get');
	$router->get('/user/get_parent', 'Api\v1\UserController@get_parent');
	$router->get('/user/get_list', 'Api\v1\UserController@get_list');
	$router->get('/user/get_list_sync', 'Api\v1\UserController@get_list_sync');
	$router->get('/user/get_list_dropdown', 'Api\v1\UserController@get_list_dropdown');
	$router->get('/user/get_list_bottom_level_child', 'Api\v1\UserController@get_list_bottom_level_child');
	$router->get('/user/insert_quota_by_site_id', 'Api\v1\UserController@insert_quota_by_site_id');
	$router->get('/user/user_bottom_up', 'Api\v1\UserController@user_bottom_up');
	$router->get('/user', 'Api\v1\UserController@get');
	$router->post('/user', 'Api\v1\UserController@save');
	$router->put('/user', 'Api\v1\UserController@update');
	$router->delete('/user', 'Api\v1\UserController@delete');	
	$router->get('/user/get_site_quota_remaining', 'Api\v1\UserController@get_site_quota_remaining');
	$router->get('/user/report_quota_site_remaining', 'Api\v1\UserController@report_quota_site_remaining');
	$router->get('/user/report_quota_user_site_remaining', 'Api\v1\UserController@report_quota_user_site_remaining');
	
    $router->get('/user_attribute_value', 'Api\v1\UserAttributeValueController@get_list');
	$router->get('/user_attribute_value/get', 'Api\v1\UserAttributeValueController@get');
	$router->get('/user_attribute_value/get_list', 'Api\v1\UserAttributeValueController@get_list');
	$router->post('/user_attribute_value', 'Api\v1\UserAttributeValueController@save');
	$router->put('/user_attribute_value', 'Api\v1\UserAttributeValueController@update');
    $router->delete('/user_attribute_value', 'Api\v1\UserAttributeValueController@delete');

    $router->get('/tm_custart', 'Api\v1\TmCustArtController@get_list');
	$router->get('/tm_custart/get', 'Api\v1\TmCustArtController@get');
	$router->get('/tm_custart/get_list', 'Api\v1\TmCustArtController@get_list');
	$router->post('/tm_custart', 'Api\v1\TmCustArtController@save');
	$router->put('/tm_custart', 'Api\v1\TmCustArtController@update');
    $router->delete('/tm_custart', 'Api\v1\TmCustArtController@delete');
	
	// $router->get('/site/{id:[A-Za-z0-9]+}', 'Api\v1\SiteController@get');
	$router->get('/site', 'Api\v1\SiteController@get_list');
	$router->get('/site/get', 'Api\v1\SiteController@get');
	$router->get('/site/get_reset_day', 'Api\v1\SiteController@get_reset_day');
	$router->get('/site/get_list', 'Api\v1\SiteController@get_list');
	$router->get('/site/get_list_dropdown', 'Api\v1\SiteController@get_list_dropdown');
	$router->post('/site', 'Api\v1\SiteController@save');
	$router->put('/site', 'Api\v1\SiteController@update');
    $router->delete('/site', 'Api\v1\SiteController@delete');
    $router->get('/site/get_sales_order_per_site', 'Api\v1\SiteController@get_sales_order_per_site');
	
	$router->get('/pic', 'Api\v1\PicController@get_list');
	$router->get('/pic/get', 'Api\v1\PicController@get');
	$router->get('/pic/get_list', 'Api\v1\PicController@get_list');
	$router->post('/pic', 'Api\v1\PicController@save');
	$router->put('/pic', 'Api\v1\PicController@update');
    $router->delete('/pic', 'Api\v1\PicController@delete');
	
	$router->get('/level', 'Api\v1\LevelController@get_list');
	$router->get('/level/get', 'Api\v1\LevelController@get');
	$router->get('/level/get_list', 'Api\v1\LevelController@get_list');
	$router->get('/level/get_list_dropdown', 'Api\v1\LevelController@get_list_dropdown');
	$router->get('/level/get_list_sync', 'Api\v1\LevelController@get_list_sync');
	$router->post('/level', 'Api\v1\LevelController@save');
	$router->put('/level', 'Api\v1\LevelController@update');
    $router->delete('/level', 'Api\v1\LevelController@delete');
	
	$router->get('/reason', 'Api\v1\ReasonController@get_list');
	$router->get('/reason/get', 'Api\v1\ReasonController@get');
	$router->get('/reason/get_list', 'Api\v1\ReasonController@get_list');
	$router->get('/reason/get_list_dropdown', 'Api\v1\ReasonController@get_list_dropdown');
	$router->post('/reason', 'Api\v1\ReasonController@save');
	$router->put('/reason', 'Api\v1\ReasonController@update');
    $router->delete('/reason', 'Api\v1\ReasonController@delete');
	
	$router->get('/transaction', 'Api\v1\TransactionController@get_list');
	$router->get('/transaction/get_list_export', 'Api\v1\TransactionController@get_list_export');
	$router->get('/transaction/get', 'Api\v1\TransactionController@get');
	$router->get('/transaction/get_list', 'Api\v1\TransactionController@get_list');
	$router->get('/transaction/get_list_transaction_cc', 'Api\v1\TransactionController@get_list_transaction_cc');
	$router->get('/transaction/get_total_price_order', 'Api\v1\TransactionController@get_total_price_order');
	$router->get('/transaction/trigger_status_and_value', 'Api\v1\TransactionController@trigger_status_and_value');
	$router->post('/transaction', 'Api\v1\TransactionController@save');
	$router->get('/transaction/trans_out_order', 'Api\v1\TransactionController@trans_out_order');
	$router->get('/transaction/trans_out_artpo', 'Api\v1\TransactionController@trans_out_artpo');
	$router->get('/transaction/trans_in', 'Api\v1\TransactionController@trans_in');
	$router->put('/transaction', 'Api\v1\TransactionController@update');
    $router->delete('/transaction', 'Api\v1\TransactionController@delete');
    // report
	$router->get('/transaction/sales_order_per_site', 'Api\v1\TransactionController@sales_order_per_site');
	$router->get('/transaction/sales_order_qty_per_site', 'Api\v1\TransactionController@sales_order_qty_per_site');
	$router->get('/transaction/sales_order_value_per_site', 'Api\v1\TransactionController@sales_order_value_per_site');
	$router->get('/transaction/report_order_per_month', 'Api\v1\TransactionController@report_order_per_month');
	$router->get('/transaction/report_top_order', 'Api\v1\TransactionController@report_top_order');
	$router->get('/transaction/report_total_trx_user', 'Api\v1\TransactionController@report_total_trx_user');

    $router->put('/transaction_cc/write_off','Api\v1\TransactionCCController@write_off');
    $router->get('/transaction_cc/get_list','Api\v1\TransactionCCController@get_list');
    $router->get('/transaction_cc/get_list_group','Api\v1\TransactionCCController@get_list_group');
    $router->get('/transaction_cc/get_group','Api\v1\TransactionCCController@get_group');
    $router->get('/transaction_cc/get_list_cc','Api\v1\TransactionCCController@get_list_cc');
    $router->get('/transaction_cc/cycle_count_from_chamber', 'Api\v1\TransactionCCController@cycle_count_from_chamber');

    $router->get('/cc_job/get','Api\v1\CCJobController@get');
    $router->get('/cc_job/get_list','Api\v1\CCJobController@get_list');
    $router->post('/cc_job','Api\v1\CCJobController@save');
    $router->post('/cc_job/save_bulk','Api\v1\CCJobController@save_bulk');
    $router->put('/cc_job','Api\v1\CCJobController@update');
    $router->delete('/cc_job','Api\v1\CCJobController@delete');
    $router->post('/cc_job/damage_goods','Api\v1\CCJobController@damage_goods');
    $router->post('/cc_job/discrepancy','Api\v1\CCJobController@discrepancy');
    $router->post('/cc_job/write_off','Api\v1\CCJobController@write_off');
    $router->post('/cc_job/cycle_count','Api\v1\CCJobController@cycle_count');

	$router->get('/scorder/get_list_artpo_history', 'Api\v1\SCOrderController@get_list_artpo_history');
    $router->get('/scorder/get_new_so_id', 'Api\v1\SCOrderController@get_new_so_id');
    $router->get('/scorder/get_list_no_sosap', 'Api\v1\SCOrderController@get_list_no_sosap');
    $router->get('/scorder/get_list_not_sent_so', 'Api\v1\SCOrderController@get_list_not_sent_so');
	$router->post('/scorder/insert_detail_sc', 'Api\v1\SCOrderController@insert_detail_sc');
	$router->post('/scorder/insert_sc', 'Api\v1\SCOrderController@insert_sc');
	$router->get('/scorder/get_list_sc', 'Api\v1\SCOrderController@get_list_sc');
	$router->put('/scorder/update_flag', 'Api\v1\SCOrderController@update_flag');
	$router->put('/scorder/update', 'Api\v1\SCOrderController@update');
	$router->put('/scorder/update_artpo_history', 'Api\v1\SCOrderController@update_artpo_history');

	$router->get('/article_logistic_site', 'Api\v1\ArticleLogisticSiteController@get_list');
	$router->get('/article_logistic_site/get', 'Api\v1\ArticleLogisticSiteController@get');
	$router->get('/article_logistic_site/get_new_id', 'Api\v1\ArticleLogisticSiteController@get_new_id');
	$router->get('/article_logistic_site/cron_update_header_status', 'Api\v1\ArticleLogisticSiteController@cron_update_header_status');
	$router->get('/article_logistic_site/get_list', 'Api\v1\ArticleLogisticSiteController@get_list');
	$router->get('/article_logistic_site/get_list_detail', 'Api\v1\ArticleLogisticSiteController@get_list_detail');
	$router->get('/article_logistic_site/get_list_export', 'Api\v1\ArticleLogisticSiteController@get_list_export');
	$router->post('/article_logistic_site', 'Api\v1\ArticleLogisticSiteController@save');
	$router->put('/article_logistic_site', 'Api\v1\ArticleLogisticSiteController@update');
	$router->put('/article_logistic_site/update_bulk', 'Api\v1\ArticleLogisticSiteController@update_bulk');
    $router->delete('/article_logistic_site', 'Api\v1\ArticleLogisticSiteController@delete');
    $router->post('/article_logistic_site/good_issue_stock', 'Api\v1\ArticleLogisticSiteController@good_issue_stock');
	
	$router->get('/article_logistic_site_detail', 'Api\v1\ArticleLogisticSiteDetailController@get_list');
	$router->get('/article_logistic_site_detail/get', 'Api\v1\ArticleLogisticSiteDetailController@get');
	$router->get('/article_logistic_site_detail/get_list', 'Api\v1\ArticleLogisticSiteDetailController@get_list');
	$router->get('/article_logistic_site_detail/get_list_replenish', 'Api\v1\ArticleLogisticSiteDetailController@get_list_replenish');

	// report gr
	$router->get('/article_logistic_site_detail/get_discrepancy_gr', 'Api\v1\ArticleLogisticSiteDetailController@get_discrepancy_gr');
	$router->get('/article_logistic_site_detail/get_number_of_od', 'Api\v1\ArticleLogisticSiteDetailController@get_number_of_od');


	$router->get('/article_logistic_site_detail/get_list_kitting', 'Api\v1\ArticleLogisticSiteDetailController@get_list_kitting');

	$router->post('/article_logistic_site_detail', 'Api\v1\ArticleLogisticSiteDetailController@save');
	$router->post('/article_logistic_site_detail/save_bulk', 'Api\v1\ArticleLogisticSiteDetailController@save_bulk');
	$router->post('/article_logistic_site_detail/save_bulk_detail', 'Api\v1\ArticleLogisticSiteDetailController@save_bulk_detail');
	$router->put('/article_logistic_site_detail', 'Api\v1\ArticleLogisticSiteDetailController@update');
	$router->put('/article_logistic_site_detail/update_bulk', 'Api\v1\ArticleLogisticSiteDetailController@update_bulk');
    $router->delete('/article_logistic_site_detail', 'Api\v1\ArticleLogisticSiteDetailController@delete');
	
	$router->get('/role', 'Api\v1\RoleController@get_list');
	$router->get('/role/get', 'Api\v1\RoleController@get');
	$router->get('/role/get_list', 'Api\v1\RoleController@get_list');
	$router->get('/role/get_list_dropdown', 'Api\v1\RoleController@get_list_dropdown');
	$router->post('/role', 'Api\v1\RoleController@save');
	$router->put('/role', 'Api\v1\RoleController@update');
    $router->delete('/role', 'Api\v1\RoleController@delete');
	
	$router->get('/capability', 'Api\v1\CapabilityController@get_list');
	$router->get('/capability/get_list', 'Api\v1\CapabilityController@get_list');
	$router->get('/capability/get', 'Api\v1\CapabilityController@get');
	$router->post('/capability', 'Api\v1\CapabilityController@save');
	$router->put('/capability', 'Api\v1\CapabilityController@update');
    $router->delete('/capability', 'Api\v1\CapabilityController@delete');
	
	$router->get('/role_capability/cron_insert_role', 'Api\v1\RoleCapabilityController@cron_insert_role');
	$router->get('/role_capability/get', 'Api\v1\RoleCapabilityController@get');
	$router->get('/role_capability/get_list', 'Api\v1\RoleCapabilityController@get_list');
	$router->get('/role_capability/get_list_detail', 'Api\v1\RoleCapabilityController@get_list_detail');
	$router->get('/role_capability', 'Api\v1\RoleCapabilityController@get_list');
	$router->post('/role_capability/update_bulk', 'Api\v1\RoleCapabilityController@update_bulk');
	$router->post('/role_capability', 'Api\v1\RoleCapabilityController@save');
	$router->put('/role_capability', 'Api\v1\RoleCapabilityController@update');
    $router->delete('/role_capability', 'Api\v1\RoleCapabilityController@delete');
	
	$router->get('/reason_type', 'Api\v1\ReasonTypeController@get_list');
	$router->get('/reason_type/get', 'Api\v1\ReasonTypeController@get');
	$router->get('/reason_type/get_list', 'Api\v1\ReasonTypeController@get_list');
	$router->get('/reason_type/get_list_dropdown', 'Api\v1\ReasonTypeController@get_list_dropdown');
	$router->post('/reason_type', 'Api\v1\ReasonTypeController@save');
	$router->put('/reason_type', 'Api\v1\ReasonTypeController@update');
    $router->delete('/reason_type', 'Api\v1\ReasonTypeController@delete');
	
	$router->get('/reason_type_mapping', 'Api\v1\ReasonTypeMappingController@get_list');
	$router->get('/reason_type_mapping/get_validate_unique_reason', 'Api\v1\ReasonTypeMappingController@get_validate_unique_reason');
	$router->get('/reason_type_mapping/get', 'Api\v1\ReasonTypeMappingController@get');
	$router->get('/reason_type_mapping/get_list', 'Api\v1\ReasonTypeMappingController@get_list');
	$router->post('/reason_type_mapping', 'Api\v1\ReasonTypeMappingController@save');
	$router->put('/reason_type_mapping', 'Api\v1\ReasonTypeMappingController@update');
	$router->put('/reason_type_mapping/update_bulk_chamber', 'Api\v1\ReasonTypeMappingController@update_bulk_chamber');
    $router->delete('/reason_type_mapping', 'Api\v1\ReasonTypeMappingController@delete');
	$router->put('/reason_type_mapping/update_chamber_sync_flag', 'Api\v1\ReasonTypeMappingController@update_chamber_sync_flag');
	
	$router->get('/attribute', 'Api\v1\AttributeController@get_list');
	$router->get('/attribute/get', 'Api\v1\AttributeController@get');
	$router->get('/attribute/get_list', 'Api\v1\AttributeController@get_list');
	$router->get('/attribute/get_list_dropdown', 'Api\v1\AttributeController@get_list_dropdown');
	$router->post('/attribute', 'Api\v1\AttributeController@save');
	$router->put('/attribute', 'Api\v1\AttributeController@update');
    $router->delete('/attribute', 'Api\v1\AttributeController@delete');
	
	$router->get('/article_attribute_value', 'Api\v1\ArticleAttributeValueController@get_list');
	$router->get('/article_attribute_value/get_list', 'Api\v1\ArticleAttributeValueController@get_list');
	$router->get('/article_attribute_value/get', 'Api\v1\ArticleAttributeValueController@get');
	$router->post('/article_attribute_value', 'Api\v1\ArticleAttributeValueController@save');
	$router->put('/article_attribute_value', 'Api\v1\ArticleAttributeValueController@update');
    $router->delete('/article_attribute_value', 'Api\v1\ArticleAttributeValueController@delete');
    $router->get('/article_attribute_value/get_list_dropdown', 'Api\v1\ArticleAttributeValueController@get_list_dropdown');
   	$router->get('/article_attribute_value/get_ajax', 'Api\v1\ArticleAttributeValueController@get_ajax');
	
	$router->get('/article_stock', 'Api\v1\ArticleStockController@get_list');
	$router->get('/article_stock/get', 'Api\v1\ArticleStockController@get');
	$router->get('/article_stock/get_list', 'Api\v1\ArticleStockController@get_list');
	$router->post('/article_stock', 'Api\v1\ArticleStockController@save');
	$router->put('/article_stock', 'Api\v1\ArticleStockController@update');
	$router->put('/article_stock/update_stock', 'Api\v1\ArticleStockController@update_stock');
	$router->put('/article_stock/update_dashboard_stk', 'Api\v1\ArticleStockController@update_dashboard_stk');
    $router->delete('/article_stock', 'Api\v1\ArticleStockController@delete');
    $router->get('/article_stock/get_value_of_stocks', 'Api\v1\ArticleStockController@get_value_of_stocks');
	
	$router->get('/article_po', 'Api\v1\ArticlePoController@get_list');
	$router->get('/article_po/get', 'Api\v1\ArticlePoController@get');
	$router->get('/article_po/get_list', 'Api\v1\ArticlePoController@get_list');
	$router->post('/article_po', 'Api\v1\ArticlePoController@save');
	$router->post('/article_po/save_bulk', 'Api\v1\ArticlePoController@save_bulk');
	$router->put('/article_po/update_in', 'Api\v1\ArticlePoController@update_in');
	$router->put('/article_po', 'Api\v1\ArticlePoController@update');
    $router->delete('/article_po', 'Api\v1\ArticlePoController@delete');
    $router->get('/article_po/update_artpo', 'Api\v1\ArticlePoController@update_artpo_qty');
    $router->get('/article_po/update_remaining_qty_artpo', 'Api\v1\ArticlePoController@update_remaining_qty_artpo');
	
	$router->get('/rfid_article', 'Api\v1\RfidArticleController@get_list');
	$router->get('/rfid_article/get', 'Api\v1\RfidArticleController@get');
	$router->get('/rfid_article/get_rfid_report', 'Api\v1\RfidArticleController@get_rfid_report');
	$router->get('/rfid_article/get_list_rfid', 'Api\v1\RfidArticleController@get_list_rfid');
	$router->get('/rfid_article/get_list', 'Api\v1\RfidArticleController@get_list');
	$router->post('/rfid_article', 'Api\v1\RfidArticleController@save');
	$router->put('/rfid_article', 'Api\v1\RfidArticleController@update');
	$router->post('/rfid_article/update_status_rfid', 'Api\v1\RfidArticleController@update_status_rfid');
    $router->delete('/rfid_article', 'Api\v1\RfidArticleController@delete');
    $router->get('/rfid_article/list_unmapping_rfid_article', 'Api\v1\RfidArticleController@list_unmapping_rfid_article');
	
	$router->get('/config', 'Api\v1\ConfigController@get_list');
	$router->get('/config/get', 'Api\v1\ConfigController@get');
	$router->get('/config/get_list', 'Api\v1\ConfigController@get_list');
	$router->post('/config', 'Api\v1\ConfigController@save');
	$router->put('/config/update_duplicate_single', 'Api\v1\ConfigController@update_duplicate_single');
	$router->put('/config', 'Api\v1\ConfigController@update');
	$router->delete('/config', 'Api\v1\ConfigController@delete');

	$router->get('/movement_article', 'Api\v1\MovementArticleController@get_list');
	$router->get('/movement_article/get_list_combine_report', 'Api\v1\MovementArticleController@get_list_combine_report');
	$router->get('/movement_article/get_list', 'Api\v1\MovementArticleController@get_list');
	$router->get('/movement_article/get_list_basic', 'Api\v1\MovementArticleController@get_list_basic');
	$router->get('/movement_article/get', 'Api\v1\MovementArticleController@get');
	$router->post('/movement_article', 'Api\v1\MovementArticleController@save');
	$router->post('/movement_article/save_bulk', 'Api\v1\MovementArticleController@save_bulk');
	$router->put('/movement_article', 'Api\v1\MovementArticleController@update');
	$router->delete('/movement_article', 'Api\v1\MovementArticleController@delete');

	$router->get('/movement_quota_level', 'Api\v1\MovementQuotaLevelController@get_list');
	$router->get('/movement_quota_level/get', 'Api\v1\MovementQuotaLevelController@get');
	$router->get('/movement_quota_level/get_list', 'Api\v1\MovementQuotaLevelController@get_list');
	$router->post('/movement_quota_level', 'Api\v1\MovementQuotaLevelController@save');
	$router->put('/movement_quota_level', 'Api\v1\MovementQuotaLevelController@update');
	$router->delete('/movement_quota_level', 'Api\v1\MovementQuotaLevelController@delete');
	
	$router->get('/movement_type', 'Api\v1\MovementTypeController@get_list');
	$router->get('/movement_type/get', 'Api\v1\MovementTypeController@get');
	$router->get('/movement_type/get_list', 'Api\v1\MovementTypeController@get_list');
	$router->post('/movement_type', 'Api\v1\MovementTypeController@save');
	$router->put('/movement_type', 'Api\v1\MovementTypeController@update');
	$router->delete('/movement_type', 'Api\v1\MovementTypeController@delete');
	
	$router->get('/prepack_bundling_header', 'Api\v1\PrepackBundlingHeaderController@get_list');
	$router->get('/prepack_bundling_header/get_new_id', 'Api\v1\PrepackBundlingHeaderController@get_new_id');
	$router->get('/prepack_bundling_header/get', 'Api\v1\PrepackBundlingHeaderController@get');
	$router->get('/prepack_bundling_header/get_list', 'Api\v1\PrepackBundlingHeaderController@get_list');
	$router->post('/prepack_bundling_header', 'Api\v1\PrepackBundlingHeaderController@save');
	$router->put('/prepack_bundling_header', 'Api\v1\PrepackBundlingHeaderController@update');
	$router->delete('/prepack_bundling_header', 'Api\v1\PrepackBundlingHeaderController@delete');
	
	$router->get('/prepack_bundling_detail', 'Api\v1\PrepackBundlingDetailController@get_list');
	$router->get('/prepack_bundling_detail/get', 'Api\v1\PrepackBundlingDetailController@get');
	$router->get('/prepack_bundling_detail/get_list', 'Api\v1\PrepackBundlingDetailController@get_list');
	$router->post('/prepack_bundling_detail', 'Api\v1\PrepackBundlingDetailController@save');
	$router->post('/prepack_bundling_detail/save_bulk', 'Api\v1\PrepackBundlingDetailController@save_bulk');
	$router->put('/prepack_bundling_detail', 'Api\v1\PrepackBundlingDetailController@update');
	$router->delete('/prepack_bundling_detail', 'Api\v1\PrepackBundlingDetailController@delete');
	
	$router->get('/article_po_history', 'Api\v1\ArticlePoHistoryController@get_list');
	$router->get('/article_po_history/get', 'Api\v1\ArticlePoHistoryController@get');
	$router->get('/article_po_history/get_list', 'Api\v1\ArticlePoHistoryController@get_list');
	$router->post('/article_po_history', 'Api\v1\ArticlePoHistoryController@save');
	$router->put('/article_po_history', 'Api\v1\ArticlePoHistoryController@update');
	$router->delete('/article_po_history', 'Api\v1\ArticlePoHistoryController@delete');

	$router->get('/user_role', 'Api\v1\UserRoleController@get_list');
	$router->get('/user_role/get', 'Api\v1\UserRoleController@get');
	$router->get('/user_role/get_list', 'Api\v1\UserRoleController@get_list');
	$router->get('/user_role/get_list_user', 'Api\v1\UserRoleController@get_list_user');
	$router->post('/user_role', 'Api\v1\UserRoleController@save');
	$router->put('/user_role', 'Api\v1\UserRoleController@update');
	$router->delete('/user_role', 'Api\v1\UserRoleController@delete');
		
	$router->get('/log_activity_chamber', 'Api\v1\LogActivityChamberController@get_list');
	$router->get('/log_activity_chamber/get', 'Api\v1\LogActivityChamberController@get');
	$router->get('/log_activity_chamber/get_list', 'Api\v1\LogActivityChamberController@get_list');
	$router->post('/log_activity_chamber', 'Api\v1\LogActivityChamberController@save');
	$router->put('/log_activity_chamber', 'Api\v1\LogActivityChamberController@update');
	$router->delete('/log_activity_chamber', 'Api\v1\LogActivityChamberController@delete');
	
	$router->get('/emergency_log', 'Api\v1\EmergencyLogController@get_list');
	$router->get('/emergency_log/get', 'Api\v1\EmergencyLogController@get');
	$router->get('/emergency_log/get_list', 'Api\v1\EmergencyLogController@get_list');
	$router->post('/emergency_log', 'Api\v1\EmergencyLogController@save');
	$router->put('/emergency_log', 'Api\v1\EmergencyLogController@update');
    $router->delete('/emergency_log', 'Api\v1\EmergencyLogController@delete');

	$router->get('/power_log', 'Api\v1\PowerLogController@get_list');
	$router->get('/power_log/get', 'Api\v1\PowerLogController@get');
	$router->get('/power_log/get_list', 'Api\v1\PowerLogController@get_list');
	$router->post('/power_log', 'Api\v1\PowerLogController@save');
	$router->put('/power_log', 'Api\v1\PowerLogController@update');
	$router->put('/power_log/insert_bulk', 'Api\v1\PowerLogController@insert_bulk');
	$router->delete('/power_log', 'Api\v1\PowerLogController@delete');
		
	$router->get('/division', 'Api\v1\DivisionController@get_list');
	$router->get('/division/get', 'Api\v1\DivisionController@get');
	$router->get('/division/get_list', 'Api\v1\DivisionController@get_list');
	$router->get('/division/get_list_dropdown', 'Api\v1\DivisionController@get_list_dropdown');
	$router->get('/division/get_list_sync', 'Api\v1\DivisionController@get_list_sync');
	$router->post('/division', 'Api\v1\DivisionController@save');
	$router->put('/division', 'Api\v1\DivisionController@update');
    $router->delete('/division', 'Api\v1\DivisionController@delete');
	
    $router->get('/video', 'Api\v1\VideoController@get_list');
	$router->get('/video/get', 'Api\v1\Controller@get');
	$router->get('/video/get_list', 'Api\v1\Controller@get_list');
	
	// $router->get('/', 'Api\v1\Controller@get_list');
	// $router->get('/get_list', 'Api\v1\Controller@get_list');
	// $router->get('/get', 'Api\v1\Controller@get');
	// $router->post('/', 'Api\v1\Controller@save');
	// $router->put('/', 'Api\v1\Controller@update');
    // $router->delete('/', 'Api\v1\Controller@delete');
});

	$router->get('api/v1/cron/update_quota_additional', 'Api\v1\CronController@update_quota_additional');

$router->group(['prefix' => 'cron/v1', ], function () use ($router)
{
	$router->get('/user/deduct_parent_quota', 'Cron\v1\UserCronController@deduct_parent_quota');
	$router->get('/user/remove_quota_child1', 'Cron\v1\UserCronController@remove_quota_child1');
	$router->get('/user/deduct_child_quota', 'Cron\v1\UserCronController@deduct_child_quota');

});