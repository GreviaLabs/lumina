<?php

namespace App\Http\Controllers\Report\v1;

use App\Http\Controllers\Controller;

use Input;
use Validator;
use Session;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Request;

// use Request;
use DB;

class ReportSalesController extends ReportController {

	/*
	|--------------------------------------------------------------------------
	| Report Sales Controller
	|--------------------------------------------------------------------------
	|
	| Api Report for controller handler created by Harvei on Tuesday 06 August 2019 13:13
	| controller as you wish. It is just here to get your app started!
	|
    */
	
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{		
		// $this->middleware('guest');

		// auth from apicontroller
		parent::__construct();

	}

	// report for Sales Order Per Site
	public function sales_order_per_site(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM tr_transaction
                WHERE 1
                AND movement_type = '201' ";

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
	}

    // report for Sales Order Per Site Divisoin Production
	public function sales_order_per_site_division_production(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM tr_transaction
                LEFT JOIN ms_user b USING(user_id)
                LEFT JOIN ms_division c USING(division_id)
                WHERE 1 AND movement_type = '201' AND division_name = 'Production'
                ";

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    // report for Sales Order Per Site Divisoin Maintenance
	public function sales_order_per_site_division_maintenance(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM tr_transaction
                LEFT JOIN ms_user b USING(user_id)
                LEFT JOIN ms_division c USING(division_id)
                WHERE 1 AND movement_type = '201' AND division_name = 'Maintenance'
                ";

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    // report for Sales Order Per Site Divisoin GA
	public function sales_order_per_site_division_ga(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM tr_transaction
                LEFT JOIN ms_user b USING(user_id)
                LEFT JOIN ms_division c USING(division_id)
                WHERE 1 AND movement_type = '201' AND division_name = 'General Affair'
                ";

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    // report for Sales Order transaction per month
    public function order_transaction_month(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM tr_transaction
                WHERE 1
                AND movement_type = '201' ";

        if (isset($attr['month']) && $attr['month'] != '') {
            $q.= ' AND SUBSTRING(created_at,6,2) = '.replace_quote($attr['month']);
        }
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    // report for quota/site
    public function quota_site(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT SUM(site_qty_value) AS total
				FROM ms_site
                WHERE 1
                ";
                
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    // report for quota/user
    public function quota_user(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT SUM(quota_remaining) AS total
				FROM ms_user
                WHERE 1
                ";
                
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
	public function __destruct()
	{
		// parent::__construct();
	}
	
}