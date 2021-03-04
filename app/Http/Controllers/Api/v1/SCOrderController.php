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

class SCOrderController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'tr_sc_order';
    public $list_column = array('sc_order_id','po_number','site_id','issue_date','is_created_csv','is_sent','remark','so_sap','log_fr_sap','log_staging','log_sap','sc_filename');
    public $primary_key = ['sc_order_id'];
    public $pkey = 'sc_order_id';
	// protected $date = new DateTime();
	// public $datetime = $date->format("Y-m-d h:i:s");
	public $who = 'System';
	public $ip = "System";

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

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */

	public function generate_id($str = 0, $prefix = "D",$digitno = 5)
	{
		$date_month = date("Ymd", time() );
		$newLen = $str + 1;
		$strdigit = $val = "";
		$tmpzero = $tmpstr = NULL;
		
		$len = strlen($newLen);
		$tmpzero = $digitno - strlen($newLen);
		
		$tmpstr = strlen($newLen) - $tmpzero;
		if ($tmpstr <= 0) $tmpstr = 1;

		for ($i = 1; $i <= $tmpzero; $i++) {
			$strdigit .= '0'; 
		}
		
		for ($i = 1; $i <= $tmpstr; $i++) {
			$val = $prefix.$date_month.$strdigit.$newLen;
		}
		return $val;
	}

	public function get_new_so_id($prefix = "D")
	{
		$date_month = date("Ymd", time() );
		$col = "sc_order_id";
		$sql = "
		SELECT " . $col . " 
		FROM tr_sc_order 
		WHERE " . $col . " LIKE '%".$prefix.$date_month."_%' 
		ORDER BY " . $col . " DESC 
		LIMIT 1;";
		
		$last_id = orm_get($sql, $col);
		
		$new_id = 0;
		if(isset($last_id)) $new_id = get_right($last_id,5);
		// debug($new_id,1);
		$val = $this->generate_id($new_id);
		$response['new_id'] = $val;
		echo json_encode($response);
		die;
	}

	// 04 Juli 2019 edit select to get data from article_po_history
	public function get_list_artpo_history(){
		$get = $result = NULL;
		if (! empty($_GET)) $get = $_GET;
		//$q = "SELECT GROUP_CONCAT(apoh.article_po_history_id) AS group_apo_id, apo.article, apo.site_id, apo.customer_article, apo.po_blanket_number, DATE(apoh.created_at) AS issue_date, SUM(tr.conversion_value) AS issue_qty, tr.price FROM tr_article_po_history apoh
				// LEFT JOIN tr_transaction tr ON apoh.reference = tr.transaction_id
				// LEFT JOIN ms_article_po apo ON apoh.article_po_id = apo.article_po_id
				// LEFT JOIN ms_article art ON apo.article = art.article AND apo.site_id = art.site_id
				// WHERE 1 ";
		$q = "SELECT GROUP_CONCAT(apoh.article_po_history_id) AS group_apo_id, apo.article, apo.customer_article, apo.site_id, apo.po_blanket_number, SUM(tr.conversion_value) AS issue_qty, (sum(tr.conversion_value)*tr.price) as price, DATE(apoh.created_at) AS issue_date
			FROM tr_article_po_history apoh
			LEFT JOIN ms_article_po apo ON apoh.article_po_id = apo.article_po_id
			LEFT JOIN ms_article art ON apo.article = art.article AND apo.site_id = art.site_id
			LEFT JOIN tr_transaction tr ON apoh.reference = tr.transaction_id 
				AND art.article = tr.article AND art.site_id = tr.site_id
			WHERE 1 ";
		if (isset($get['is_created_sc_order']) && $get['is_created_sc_order'] != '') {
			$q.= " AND apoh.is_created_sc_order = ".$get['is_created_sc_order'];
		}
		$q.= " AND apoh.status_in_out = 'out' AND tr.status_in_out = 'out' AND apo.`status` = 1";
		$q.= " GROUP BY apo.po_blanket_number, apo.article, apo.site_id, DATE(apo.created_at)";
		$q.= " ORDER BY DATE(apoh.created_at) ASC, apo.po_blanket_number DESC";
		$result = orm_get_list($q);
		$prev_num = '';
		$idx = 0;
		if(!empty($result)){
			foreach ($result as $key => $value) {
				if($value->po_blanket_number == $prev_num){
					$idx++;
				}else{
					$idx = 0;
				}
				$data[$value->po_blanket_number][$idx] = $value;
				$prev_num = $value->po_blanket_number;
			}
		}
		echo json_encode($data);
		die;
    }
    
    // get list all no so sap to be hit to poseidon from front end
	public function get_list_no_sosap(){
		$get = $result = NULL;
		if (! empty($_GET)) $get = $_GET;
        // -- SELECT sc_order_id, po_number, site_id, issue_date
		$q = "
        SELECT GROUP_CONCAT(sc_order_id) as list_sc_order_id
        FROM tr_sc_order o
        WHERE 1 ";

        $q.= " AND o.is_csv_created = 1 ";
        $q.= " AND (o.so_sap IS NULL OR o.so_sap != '')";

        if (isset($get['site_id']) && $get['site_id'] != '') {
			// $q.= " AND o.site_id = " . replace_quote($get['site_id']);
		}

		// if (isset($get['is_created_sc_order']) && $get['is_created_sc_order'] != '') {
		// 	$q.= " AND apoh.is_created_sc_order = ".$get['is_created_sc_order'];
		// }
		// $q.= " AND apoh.status_in_out = 'out' AND tr.status_in_out = 'out' AND apo.`status` = 1";
		// $q.= " GROUP BY apo.po_blanket_number, apo.article, apo.site_id, DATE(apo.created_at)";
        // $q.= " ORDER BY DATE(apoh.created_at) ASC, apo.po_blanket_number DESC";
        
        $result = orm_get($q,'list_sc_order_id','array');
        $response = explode(',',$result);

        // debug($result,1);
        // $response = array_merge(',',$result);
        // debug($response,1);
        // $result = 

        $data['data'] = $response;
        
		echo json_encode($data);
		die;
	}

	// get list all sc_order is_sent = 0
	public function get_list_not_sent_so(){
		$get = $result = NULL;
		if (! empty($_GET)) $get = $_GET;

		$q = 'SELECT sc.sc_order_id, sc.sc_filename FROM tr_sc_order sc';
		$q.= ' WHERE 1 AND sc.is_csv_created = 1';

		if (isset($get['is_sent']) && $get['is_sent'] != '') {
			$q.= " AND sc.is_sent = " . replace_quote($get['is_sent']);
		}

		$data = orm_get_list($q);

		$result['data'] = $data;

		echo json_encode($result);
		die;
	}

	public function insert_sc(){
		$post = $result = NULL;
		if (! empty($_POST)) $post = $_POST;
		if (! empty($post)) {
            $save = DB::table($this->table)->insert($post);
            
            if ($save) {
				$result['is_success'] = 1;
                $result['message'] = 'save success';
            } else {
				$result['is_success'] = 0;
                $result['message'] = 'save failed';
            }
        }

		echo json_encode($result);
		die;
	}

	public function insert_detail_sc(){
		$post = $result = NULL;
		if (! empty($_POST)) $post = $_POST;
		if (! empty($post)) {
			for($i=0; $i<count($post); $i++){
            	$save = DB::table('tr_sc_order_detail')->insert($post[$i]);
			}
            
            if ($save) {
				$result['is_success'] = 1;
                $result['message'] = 'save detail success';
            } else {
				$result['is_success'] = 0;
                $result['message'] = 'save detail failed';
            }
        }

		echo json_encode($result);
		die;
	}

	// update article_po.is_created_sc_order to 1
	public function update_artpo_history(){
		$put = $attr = $result = $param_where = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') parse_str(file_get_contents("php://input"), $_PUT);

		$put = $_PUT;
		// $attr = validate_column($this->list_column, $put);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($put)) $result['message'] = 'no data';

		$q = "UPDATE tr_article_po_history SET is_created_sc_order = ".$put['is_created_sc_order'];
		$q.= ", updated_at = ".replace_quote($put['updated_at']);
		$q.= ", updated_by = ".replace_quote($put['updated_by']);
		$q.= ", updated_ip = ".replace_quote($put['updated_ip']);
		$q.= " WHERE article_po_history_id IN( ".$put['article_po_history_id']." )";
		$update = DB::statement($q);
		if ($update) {
			$result['is_success'] = 1;
			$result['message'] = 'update success';
		} else {
			$result['is_success'] = 0;
			$result['message'] = 'update failed';
		}
		
		echo json_encode($result);
		die;
	}
	
	public function get_list_sc(){
		$get = $result = NULL;

		if(!empty($_GET)) $get = $_GET;
		// add (d.price) later
		$q = "SELECT d.article as sku, d.issue_qty as qty, h.site_id as site, h.po_number as cust_po_no, h.sc_order_id as so_no_dashboard, DATE(h.issue_date) as order_date_dashboard FROM ".$this->table." h join tr_sc_order_detail d on h.sc_order_id = d.sc_order_id WHERE 1";

		if (isset($get['is_csv_created']) && $get['is_csv_created'] != '') {
			$q.= " AND h.is_csv_created = ".$get['is_csv_created']."";
		}

		$result = orm_get_list($q);

		$prev_num = '';
		$idx = 0;
		foreach ($result as $key => $value) {
			if($value->so_no_dashboard == $prev_num){
				$idx++;
			}else{
				$idx = 0;
			}
			$data[$value->so_no_dashboard][$idx] = $value;
			$prev_num = $value->so_no_dashboard;
		}

		echo json_encode($data);
		die;
	}

	public function update_flag(){
		$put = $attr = $result = $param_where = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') parse_str(file_get_contents("php://input"), $_PUT);

		$put = $_PUT;
		
		// $attr = validate_column($this->list_column, $put);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($put)) $result['message'] = 'no data';
		
		// Check primary key validity
		if (is_array($this->primary_key)) {
			for($i=0; $i<count($put); $i++){
				foreach ($this->primary_key as $kpk => $pk) {
					if (! isset($put[$i][$pk])) $result['message'] = $pk . ' must be filled.';
					else $param_where[$i][$pk] = $put[$i][$pk];
				}
			}
			
		} else {
			for($i=0; $i<count($put); $i++){
				if (! isset($put[$i][$this->primary_key])) $result['message'] = $this->primary_key . ' must be filled.';
			}
		}

		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($put)) $result['paramdata'] = $put;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		// remove data from pk where
		if (is_array($this->primary_key)) {
			for($i=0; $i<count($put); $i++){
				foreach ($this->primary_key as $kpk => $pk) {
					unset($put[$i][$pk]);
				}
			}
			
		} else {
			for($i=0; $i<count($put); $i++){
				unset($put[$i][$this->primary_key]);
			}
		}
		for($i=0; $i<count($put); $i++){
			// update sc_order
			$update = DB::table($this->table)
				->where($param_where[$i])
				->update($put[$i]);
		}
		if ($update) {
			$result['is_success'] = 1;
			$result['message'] = 'update success';
		} else {
			$result['is_success'] = 0;
			$result['message'] = 'update failed';
			// $result['query'] = $update->toSql();
		}

        echo json_encode($result);
        die;
	}

	public function update()
	{
		$put = $attr = $result = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') parse_str(file_get_contents("php://input"), $_PUT);

        $put = $_PUT;

		$attr = validate_column($this->list_column, $put);

		$result['is_success'] = 1;
		$result['message'] = NULL;

        if (empty($attr)) $result['message'] = 'no data';
        
		if (! isset($attr[$this->pkey])) $result['message'] = $this->pkey . ' must be filled.';
		
		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		$param_where = $attr[$this->pkey];
		unset($attr[$this->pkey]);

		$update = DB::table($this->table)
			->where($this->pkey, $param_where)
			->update($attr);
			
		if ($update) {
			$result['is_success'] = 1;
			$result['message'] = 'update success';
		} else {
			$result['is_success'] = 0;
			$result['message'] = 'update failed';
			// $result['query'] = $update->toSql();
		}

        echo json_encode($result);
        die;
	}

	public function __destruct()
	{
		// parent::__construct();
	}
	
}