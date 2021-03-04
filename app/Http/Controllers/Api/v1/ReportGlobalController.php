<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;

use Input;
use Validator;
use Session;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Request;

// use Request;
use DB;

use App\Models\TransactionModel;
use App\Models\UserModel;

class ReportGlobalController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
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

	public function order_transaction_per_month(){
		$attr = $result = NULL;
		$grand_total = 0;

		if(isset($_GET)) $attr = $_GET;

		$q = '
        SELECT tr.site_id, tr.transaction_id, tr.created_at, tr.article, tr.description, tr.customer_article, r.reason_value, sum(tr.qty) AS qty, tr.conversion_value ,
        (tr.price*tr.conversion_value) AS price, sum((tr.price*tr.conversion_value) * tr.qty) AS total_price, CONCAT(u.user_code," - ", u.firstname," ", u.lastname) as creator, tr.wo_wbs
		FROM tr_transaction tr
        LEFT JOIN ms_reason r ON tr.reason_id = r.reason_id
        LEFT JOIN ms_user u ON tr.user_id = u.user_id
		WHERE 1';

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

		if (isset($attr['art_source']) && $attr['art_source'] != '') {
        	if($attr['art_source'] == 'KLS'){
				$q.= ' AND tr.art_source = 1';
        	} elseif($attr['art_source'] == 'NON_KLS'){
        		$q.= ' AND tr.art_source = 2';
        	}
        }

        // start date
        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(tr.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(tr.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

		$q.= ' AND tr.status_in_out = "out" AND tr.movement_type = "201"
				GROUP BY tr.article, tr.transaction_id
				ORDER BY tr.created_at DESC';

		$data = orm_get_list($q);

		$temp_data = json_decode(json_encode($data),1);

		// get grand total price
		for($i=0; $i<count($temp_data); $i++){
			$grand_total+= $temp_data[$i]['total_price'];
		}

		$result['data'] = $data;
		$result['grand_total'] = $grand_total;

		echo json_encode($result); die;
	}

	public function article_below_safety_stock_po(){
		$attr = $result = NULL;
		$grand_total = 0;

		if(isset($_GET)) $attr = $_GET;


		$q = 'SELECT apo.site_id, apo.article, art.description, art.customer_article, art.customer_article_description,
			art.safety_stock, arts.stock_dashboard, art.price, apo.po_blanket_number, apo.po_created_date FROM ms_article_po apo
			LEFT JOIN ms_article_stock arts ON apo.article = arts.article AND apo.site_id = arts.site_id
			LEFT JOIN ms_article art ON apo.article = art.article AND apo.site_id = art.site_id
			WHERE 1';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' apo.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
			}
		}

        // start date
        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(apo.po_created_date) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(apo.po_created_date) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

		
		$q.= ' AND art.safety_stock > arts.stock_dashboard';

		$data = orm_get_list($q);
		$total_rows = count($data);

		$result['data'] = $data;
		$result['total_rows'] = $total_rows;

		echo json_encode($result); die;
	}

	public function __destruct()
	{
		// parent::__construct();
	}
	
}