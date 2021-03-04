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

class ReportGeneralController extends ReportController {

	/*
	|--------------------------------------------------------------------------
	| Report Logistic Controller
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

	// report value all order (by site or by division name)
	public function total_so_value(){
        // echo "hello world";die;
        $attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

        // Example
        /*
        SELECT tr.site_id, SUM(tr.value) total_value, d.division_name
        FROM tr_transaction tr
        LEFT JOIN ms_user u ON tr.user_id = u.user_id
        LEFT JOIN ms_division d ON u.division_id = d.division_id
        WHERE 1 AND tr.movement_type LIKE '%2%' AND tr.site_id = 'SZ24'
        */

        $q = '
        SELECT tr.site_id, SUM(tr.value) total_value, d.division_name
        FROM tr_transaction tr
        LEFT JOIN ms_user u ON tr.user_id = u.user_id
        LEFT JOIN ms_division d ON u.division_id = d.division_id
        WHERE 1 AND tr.movement_type = "201"';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

		if(isset($attr['user_id'])){
			if(is_array($attr['user_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['user_id']); $i++){
					$q.= ' tr.user_id = '.replace_quote($attr['user_id'][$i]);
					if($i != count($attr['user_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.user_id = '.replace_quote($attr['user_id']);
			}
		}

        if (isset($attr['division_id']) && $attr['division_id'] != '') {
			$q.= ' AND u.division_id = '.replace_quote($attr['division_id']);
        }
        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }

    // report order with jobs not working
	public function total_so_jobs_invalid(){
        // echo "hello world";die;
        $attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

        // Example
        /*
        SELECT tr.site_id, tr.article, tr.customer_article, tr.is_job_order, tr.is_job_artpo, tr.is_job_gr
        FROM tr_transaction tr
        WHERE 1 AND tr.is_job_order = 0 OR tr.is_job_artpo = 0 AND tr.site_id = 'SZ24'
        */

        $q = '
        SELECT tr.site_id, tr.article, tr.customer_article, tr.is_job_order, tr.is_job_artpo, tr.is_job_gr
        FROM tr_transaction tr
        WHERE 1 AND tr.is_job_order = 0 OR tr.is_job_artpo = 0;
        ';

        // site id
		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

		// if(isset($attr['user_id'])){
		// 	if(is_array($attr['user_id'])){
		// 		$q.= ' AND (';
		// 		for($i=0;$i<count($attr['user_id']); $i++){
		// 			$q.= ' tr.user_id = '.replace_quote($attr['user_id']);
		// 			if($i != count($attr['user_id'])-1){
		// 				$q.= ' OR ';
		// 			}
		// 		}
		// 		$q.= ')';
		// 	} else{
		// 		$q.= ' AND tr.user_id = '.replace_quote($attr['user_id']);
		// 	}
		// }

        // start date

        // created by
        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }

    // 
	public function total_remaining_quota_division(){

        $attr = $result = $temp = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

        // Example
        /*
        SELECT u.site_id, d.division_name, SUM(u.quota_initial) AS total_quota_initial , SUM(u.quota_additional) AS total_quota_additional, SUM(u.quota_remaining) AS total_quota_remaining
        FROM ms_user u 
        LEFT JOIN ms_division d ON u.division_id = d.division_id
        WHERE 1 AND u.site_id = 'SZ24'
        GROUP BY u.division_id 

        */

        // $q = 'SELECT u.site_id, d.division_name, SUM(u.quota_initial) AS total_quota_initial , SUM(u.quota_additional) AS total_quota_additional, SUM(u.quota_remaining) AS total_quota_remaining';
        $q = '
        SELECT d.division_name, SUM(u.quota_remaining) AS total_quota_remaining
        FROM ms_user u 
        LEFT JOIN ms_division d ON u.division_id = d.division_id
        WHERE 1
        ';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' u.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND u.site_id = '.replace_quote($attr['site_id']);
			}
		}

		if(isset($attr['user_id'])){
			if(is_array($attr['user_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['user_id']); $i++){
					$q.= ' u.user_id = '.replace_quote($attr['user_id'][$i]);
					if($i != count($attr['user_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND u.user_id = '.replace_quote($attr['user_id']);
			}
		}


        // start date

        // created by
        

        // $data = orm_get_list($q);
        $data = orm_get($q,NULL,'array');

		if (empty($data)) $data = NULL;

		$result['division_name'] = $data['division_name'];
		$result['total_remaining_quota_division'] = $data['total_quota_remaining'];

		echo json_encode($result);
		die;
    }

    // 
	public function total_remaining_quota_site(){

        $attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

        // Example
        /*
        SELECT u.site_id, d.division_name, SUM(u.quota_initial) AS total_quota_initial , SUM(u.quota_additional) AS total_quota_additional, SUM(u.quota_remaining) AS total_quota_remaining
        FROM ms_user u 
        LEFT JOIN ms_division d ON u.division_id = d.division_id
        WHERE 1 AND u.site_id = 'SZ24'
        GROUP BY u.division_id 

        */

        // $q = '';
        $q = '
        SELECT SUM(u.quota_remaining) AS total_quota_remaining
        FROM ms_user u 
        WHERE 1
        ';

        // site id
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND u.site_id = '.replace_quote($attr['site_id']);
        }

		// if (isset($attr['user_id']) && $attr['user_id'] != '') {
		// 	$q.= ' AND u.user_id = '.replace_quote($attr['user_id']);
        // }


        // start date

        // created by

        $data = orm_get($q,NULL,'array');

		if (empty($data)) $data = NULL;

        $result['total_remaining_quota'] = NULL;
        if (isset($data['total_quota_remaining'])) $result['total_remaining_quota'] = $data['total_quota_remaining'];

		echo json_encode($result);
		die;
    }

    public function total_remaining_quota_user(){

        $attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

        // Example
        /*
        SELECT u.site_id, d.division_name, SUM(u.quota_initial) AS total_quota_initial , SUM(u.quota_additional) AS total_quota_additional, SUM(u.quota_remaining) AS total_quota_remaining
        FROM ms_user u 
        LEFT JOIN ms_division d ON u.division_id = d.division_id
        WHERE 1 AND u.site_id = 'SZ24'
        GROUP BY u.division_id 

        */

        $q = '
        SELECT u.site_id, SUM(u.quota_remaining) AS total_quota_remaining
        FROM ms_user u 
        WHERE 1 AND u.status = 1
        ';

        // site id
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND u.site_id = '.replace_quote($attr['site_id']);
        }

        if (isset($attr['user_id']) && $attr['user_id'] != '') {
			$q.= ' AND u.user_id = '.replace_quote($attr['user_id']);
        }

        // start date
        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

        // created by
        if (isset($attr['created_by']) && $attr['created_by'] != '') {
			$q.= ' AND u.created_by = '.replace_quote($attr['created_by']);
        }

		$data = orm_get($q,NULL,'array');

        if (empty($data)) $data = NULL;
        // debug($q,1);

        $result['total_remaining_quota'] = NULL;
        if (isset($data['total_quota_remaining'])) $result['total_remaining_quota'] = $data['total_quota_remaining'];

		echo json_encode($result);
		die;
    }

    // 
	public function total_order_value_mf_nonmf(){

        $attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

        // Example
        /*
        SELECT u.site_id, d.division_name, SUM(u.quota_initial) AS total_quota_initial , SUM(u.quota_additional) AS total_quota_additional, SUM(u.quota_remaining) AS total_quota_remaining
        FROM ms_user u 
        LEFT JOIN ms_division d ON u.division_id = d.division_id
        WHERE 1 AND u.site_id = 'SZ24'
        GROUP BY u.division_id 

		*/
		
		$total_value_mf = $total_value_nonmf = 0;

        $q = "
        SELECT IFNULL(SUM(t.value),0) AS total_value_mf
		FROM tr_transaction t
		WHERE 1 AND t.movement_type = 201 AND t.article LIKE '%7%'";

		// site id
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND t.site_id = '.replace_quote($attr['site_id']);
		}
		
		// start date
        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

		$total_value_mf = orm_get($q,'total_value_mf');

		$q = "
		SELECT IFNULL(SUM(t.value),0) AS total_value_nonmf
		FROM tr_transaction t
		WHERE 1 AND t.movement_type = 201 AND t.article NOT LIKE '%7%'";

        // site id
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND u.site_id = '.replace_quote($attr['site_id']);
        }

        // start date
        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

		$total_value_nonmf = orm_get($q,'total_value_nonmf');

		$result['total_value_mf'] = $total_value_mf;
		$result['total_value_nonmf'] = $total_value_nonmf;
		$result['total_value_global'] = $total_value_mf + $total_value_nonmf;

		$result['total_value_mf_percent'] = ($total_value_mf / $result['total_value_global']) * 100;
		$result['total_value_nonmf_percent'] = ($total_value_nonmf / $result['total_value_global']) * 100;
		$result['total_value_global_percent'] = 100;

		echo json_encode($result);
		die;
    }

    public function total_order(){

        $attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

        // Example
        /*
        SELECT u.site_id, d.division_name, SUM(u.quota_initial) AS total_quota_initial , SUM(u.quota_additional) AS total_quota_additional, SUM(u.quota_remaining) AS total_quota_remaining
        FROM ms_user u 
        LEFT JOIN ms_division d ON u.division_id = d.division_id
        WHERE 1 AND u.site_id = 'SZ24'
        GROUP BY u.division_id 

		*/
		
		$total_value_mf = $total_value_nonmf = 0;

        $q = "
        SELECT IFNULL(SUM(t.value),0) AS total_value_mf
		FROM tr_transaction t
		WHERE 1 AND t.movement_type = 201 AND t.article LIKE '%7%'";

		// site id
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND t.site_id = '.replace_quote($attr['site_id']);
		}
		
		// start date
        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

		$total_value_mf = orm_get($q,'total_value_mf');

		$q = "
		SELECT IFNULL(SUM(t.value),0) AS total_value_nonmf
		FROM tr_transaction t
		WHERE 1 AND t.movement_type = 201 AND t.article NOT LIKE '%7%'";

        // site id
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND u.site_id = '.replace_quote($attr['site_id']);
        }

        // start date
        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

		$total_value_nonmf = orm_get($q,'total_value_nonmf');

		$result['total_value_mf'] = $total_value_mf;
		$result['total_value_nonmf'] = $total_value_nonmf;
		$result['total_value_global'] = $total_value_mf + $total_value_nonmf;

		$result['total_value_mf_percent'] = ($total_value_mf / $result['total_value_global']) * 100;
		$result['total_value_nonmf_percent'] = ($total_value_nonmf / $result['total_value_global']) * 100;
		$result['total_value_global_percent'] = 100;

		echo json_encode($result);
		die;
    }
    
	// report for list of article
	public function testing(){
        echo "hello world";die;
        $attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT COUNT(*) AS total
				FROM ms_article_stock
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

	// active sites
	public function sum_site(){
        $attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT count(site_id) AS total
				FROM ms_site
				WHERE 1 ';

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

		$q.= ' AND status = 1';

        $data = orm_get($q);

		if (empty($data)) $data = NULL;

		$result['total'] = $data->total;

		echo json_encode($result);
		die;
	}

	// total articles starts
	// total article
	public function get_list_article(){
        $attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT * FROM ms_article
				WHERE 1';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}
		}

		$q.= ' AND status = 1';

        $data = orm_get_list($q);
        $total_rows = count($data);

		if (empty($data)) $data = NULL;

		$result['total_rows'] = $total_rows;
		$result['data'] = $data;

		echo json_encode($result);
		die;
	}

	// total value and qty article
	public function get_qty_value_article(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;
		
		$q = 'SELECT sum(arts.stock_dashboard) as qty, sum(a.price * arts.stock_dashboard) as value FROM ms_article';
		$q.= ' a LEFT JOIN ms_article_stock arts ON a.article = arts.article';
		$q.= ' WHERE 1';
		
		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' a.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND a.site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(a.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(a.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$q.= ' AND a.status = 1';

		$data = orm_get($q);

		if (empty($data)) $data = NULL;
		
        $result['qty'] = $data->qty;
        $result['value'] = $data->value;

        echo json_encode($result); 
		die;
	}
	// end total articles

	// total companies
	public function get_sum_company(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT count(*) as total FROM ms_company';
		$q.= ' WHERE 1';

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$q.= ' AND status = 1';

		$data = orm_get($q);

		if (empty($data)) $data = NULL;
		
        $result['total'] = $data->total;

        echo json_encode($result); 
		die;
	}

	// total users
	public function get_sum_user(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT count(user_id) as total FROM ms_user';
		$q.= ' WHERE 1';
		
		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$q.= ' AND status = 1';

		$data = orm_get($q);

		if (empty($data)) $data = NULL;
		
        $result['total'] = $data->total;

        echo json_encode($result); 
		die;
	}

	// discrepancy of cycle counting
	public function get_discrepancy_cc(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		// get total cycle_count
		$q = 'SELECT SUM(cc.qty) AS cycle_count FROM tr_cc_job cc
				WHERE 1 AND cc.movement_type = "903"';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$cc = orm_get($q);
		// end get total cycle_count

		// get total damage_goods
		$q = 'SELECT SUM(cc.qty) AS damage_goods FROM tr_cc_job cc
				WHERE 1 AND cc.movement_type = "311"';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$damaged = orm_get($q);
		// end get total damage_goods

		// get total write_off
		$q = 'SELECT SUM(cc.qty) AS write_off FROM tr_cc_job cc
				WHERE 1 AND cc.movement_type = "551"';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$write_off = orm_get($q);
		// end get total write_off
		
		if (empty($cc)) $cc = NULL;
		if (empty($damaged)) $damaged = NULL;
		if (empty($write_off)) $write_off = NULL;


        $temp['cycle_count'] = $cc->cycle_count;
        $temp['damaged'] = $damaged->damage_goods;
        $temp['write_off'] = $write_off->write_off;

        $result['data'] = $temp;
        echo json_encode($result); 
		die;
	}


	// pending
	// get fulfillment PO by Article
	public function get_fulfillment_po(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT * FROM ms_article_po';
		$q.= ' WHERE 1';

		$q.= ' AND open_qty = 0 AND remaining_qty = 0';
		
		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$q.= ' AND status = 1';

		$data = orm_get_list($q);

		if (empty($data)) $data = NULL;
		
        $result['data'] = $data;

        echo json_encode($result); 
		die;
	}

	// get article need to create po
	public function get_list_article_need_create_po(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT * FROM ms_article_po';
		$q.= ' WHERE 1';

		$q.= ' AND open_qty = 0 AND remaining_qty = 0';
		
		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$q.= ' AND status = 1';

		$data = orm_get_list($q);

		if (empty($data)) $data = NULL;
		
        $result['data'] = $data;

        echo json_encode($result); 
		die;
	}

	// get number of active article po
	public function get_sum_active_article_po(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT count(po_blanket_number) as total FROM ms_article_po';
		$q.= ' WHERE 1';

		$q.= ' AND remaining_qty > 0';
		
		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$q.= ' AND status = 1';

		$data = orm_get($q);

		if (empty($data)) $data = NULL;
		
        $result['total'] = $data->total;

        echo json_encode($result); 
		die;
	}

	// get list out of stock article
	public function get_out_of_stock_article(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT * FROM ms_article_stock';
		$q.= ' WHERE 1';

		$q.= ' AND stock_dashboard = 0';
		
		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$q.= ' AND status = 1';

		$data = orm_get_list($q);

		if (empty($data)) $data = NULL;
		
        $result['data'] = $data;

        echo json_encode($result); 
		die;
	}

	// get list outstanding od for gr dashboard
	public function get_outstanding_od_for_gr(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT * FROM tr_article_logistic_site als';
		$q.= ' LEFT JOIN tr_article_logistic_site_detail alsd ON als.outbound_delivery = alsd.outbound_delivery';
		$q.= ' WHERE 1';

		$q.= ' AND alsd.status_message = "new"';
		
		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' als.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(als.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(als.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
		
		$q.= ' AND als.status_in_out = "in" AND als.status = 1 AND alsd.status = 1';

		$data = orm_get_list($q);

		if (empty($data)) $data = NULL;
		
        $result['data'] = $data;

        echo json_encode($result); 
		die;
	}

	// for get data user compare with transaction (quota_budget user and total qty transaction for user)
	public function get_user_transaction(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT u.user_code, u.firstname, u.lastname, u.site_id, u.budget_quota, sum(tr.conversion_value) AS qty, 
				SUM(tr.value) AS price, s.flag_qty_value AS flag FROM tr_transaction tr
				LEFT JOIN ms_user u ON tr.user_id = u.user_id
				LEFT JOIN ms_site s ON tr.site_id = s.site_id
				WHERE 1';

		$q.= ' AND tr.movement_type = "201" AND u.budget_quota != ""';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(tr.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(tr.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

		$q.= ' GROUP BY tr.user_id';

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