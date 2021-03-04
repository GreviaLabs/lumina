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

class ReportLogisticController extends ReportController {

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

	// report for list of article
	public function list_of_article(){
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

    //repot for value of article
    public function value_of_article(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT SUM(b.stock_dashboard * a.price) AS total, s.site_id, s.company_id
				FROM ms_article a
				LEFT JOIN ms_article_stock b USING(article_id)
				LEFT JOIN ms_site s ON a.site_id = s.site_id
                WHERE 1
                AND a.status = 1';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' a.site_id = '.replace_quote($attr['site_id']);
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

        $data = orm_get($q);

        // get data except current site
        $q = 'SELECT SUM(b.stock_qty * a.price) AS total, s.site_id, s.company_id
				FROM ms_article a
				LEFT JOIN ms_article_stock b USING(article_id)
				LEFT JOIN ms_site s ON a.site_id = s.site_id
                WHERE 1
                AND a.status = 1';

        if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' a.site_id != '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND a.site_id != '.replace_quote($attr['site_id']);
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

        $q.= ' AND s.company_id = '.replace_quote($data->company_id);

        $except = orm_get_list($q);

		if (empty($data)) $data = NULL;
		if (empty($except)) $except = NULL;

		// mapping data key => value (sz24=>10000)
		$temp[][$data->site_id] = $data->total;
		foreach($except as $key => $val){
			$temp[][$val->site_id] = $val->total;
		}

		$result['data'] = $temp;

		echo json_encode($result);
		die;
    }
    
    //repot for qty of article
    public function qty_of_article(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT SUM(stock_qty) AS total
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

    //repot for Number Of STO
    public function number_of_sto(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

        // 1. 
		$q = '
        SELECT COUNT(a.outbound_delivery) AS total
        FROM tr_article_logistic_site a
        WHERE 1';

        if (isset($attr['status_in_out']) && $attr['status_in_out'] != '') {
			$q.= ' AND status_in_out = '.replace_quote($attr['status_in_out']);
        }

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        
        $data = orm_get($q,'total','array');

		if (empty($data)) $data = NULL;

		$result['total'] = $data;
		// $result['data'] = $data;

		echo json_encode($result);
		die;
    }

    //repot for Number Of STO
    public function number_of_sto_in(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

        // 1. 
		$q = '
        SELECT COUNT(a.outbound_delivery) AS total
        FROM tr_article_logistic_site a
        WHERE 1';

        $q.= " AND a.status_in_out = 'in'";

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        
        $data = orm_get($q,'total','array');

		if (empty($data) || ! isset($data)) $data = 0;

		$result['total'] = $data;
		// $result['data'] = $data;

		echo json_encode($result);
		die;
    }

    //repot for Number Of STO
    public function number_of_sto_out(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

        // 1. 
		$q = '
        SELECT COUNT(a.outbound_delivery) AS total
        FROM tr_article_logistic_site a
        WHERE 1';

        $q.= " AND a.status_in_out = 'out'";

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        
        $data = orm_get($q,'total','array');

		if (empty($data) || ! isset($data)) $data = 0;

		$result['total'] = $data;
		// $result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    //repot for Discrepancy Article GR
    public function discrepancy_article_gr(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT SUM(disc_plus) AS total
			  FROM tr_article_logistic_site a
			  LEFT JOIN tr_article_logistic_site_detail b USING(outbound_delivery)
              WHERE 1';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }
        
        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }

    //repot for Transaction Kitting
    public function transaction_kitting(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT count(outbound_delivery) AS total
			  FROM tr_prepack_bundling_header a
			  -- LEFT JOIN tr_prepack_bundling_detail b USING(outbound_delivery)
              WHERE 1';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' a.site_id = '.replace_quote($attr['site_id']);
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
        
        $data = orm_get($q);

		if (empty($data)) $data = NULL;

		$result['total'] = $data->total;

		echo json_encode($result);
		die;
    }

	public function __destruct()
	{
		// parent::__construct();
	}
	
}