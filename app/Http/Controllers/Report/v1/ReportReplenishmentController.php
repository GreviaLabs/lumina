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

class ReportReplenishmentController extends ReportController {

	/*
	|--------------------------------------------------------------------------
	| Report Replenishment Controller
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

	// report for mapping rfid
	public function mapping_rfid(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT COUNT(*) AS total
				FROM ms_rfid_article
                WHERE 1
                AND status = 1';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
	}

	// report for unmapping rfid
	public function list_unmapping_rfid(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "
        SELECT A.outbound_delivery,A.article, IFNULL(A.qty_receive,0) qty_receive_dashboard, IFNULL(B.description,'') description, COUNT(B.rfid) rep_mapping_chamber 
        FROM tr_article_logistic_site_detail A
        LEFT JOIN ms_rfid_article B ON 1=1 AND CONCAT(B.outbound_delivery,B.site_id,B.article) = CONCAT(A.outbound_delivery,B.site_id,A.article)
        WHERE 1";

        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND B.site_id = ' . replace_quote($attr['site_id']);
        }

		$q.= ' GROUP BY A.outbound_delivery,A.article, A.qty_receive';        
		
		// debug($q,1);

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
	}

	// report for discrepancy of cycle counting
	public function discrepancy_of_cycle_counting(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM tr_transaction_cc
                WHERE 1
                AND status_message = 'Closed' and stock_cc > 0";

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
	}

	// report for discrepancy of cycle counting by value and qty
	public function disc_cc_value_qty(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		// mungkin ini
		/*
				SELECT j.site_id, j.article, j.description, SUM(j.qty) AS total_qty, j.created_at, j.updated_at
		FROM tr_cc_job j
		WHERE j.site_id = 'SZ24'
		GROUP BY site_id, article
		*/ 
        $q = "
        SELECT c.site_id, DATE(c.updated_at) AS last_update, SUM(c.qty) AS total_qty,SUM(c.qty * a.price) AS total_value, is_progress
        FROM tr_cc_job c
        LEFT JOIN ms_article a ON a.article = c.article AND a.site_id = c.site_id
        WHERE 1";

        $q.= ' AND c.is_progress > 1';
        
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND c.site_id = '.replace_quote($attr['site_id']);
        }
        
        $q.= ' GROUP BY last_update';

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