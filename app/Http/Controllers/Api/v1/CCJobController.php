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

class CCJobController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'tr_cc_job';
    public $primary_key = 'cc_job_id';
    public $list_column = array('cc_job_id','reference_doc','site_id','article','description','customer_article','customer_article_description','movement_type','category','qty','json','is_progress', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
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
	public function get()
	{
		$attr = NULL;
		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT * FROM ' . $this->table . ' WHERE 1';
		
		if (isset($attr['cc_job_id']) && $attr['cc_job_id'] != '') {
			$q.= ' AND cc_job_id = ' . replace_quote($attr['cc_job_id']);
		}

		if (isset($attr['reference_doc']) && $attr['reference_doc'] != '') {
			$q.= ' AND reference_doc = ' . replace_quote($attr['reference_doc']);
		}
		
		if (isset($attr['is_progress']) && in_array(array(-1,0,1,10),$attr['is_progress'])) {
			$q.= ' AND is_progress = '.$attr['is_progress'];
        } else {
			$q.= ' AND is_progress != -1';
		}
		
		$data = orm_get($q);
		echo json_encode($data);
		die;
	}
	
	public function get_list()
	{
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT * FROM ' . $this->table . ' WHERE 1';
		
		if (isset($attr['filter']) && $attr['filter'] != '') 
		{
			// validate_column
			// $filter = validate_column($this->list_column, $attr);
			$filter = $attr['filter'];

			if (! empty($filter)) 
			{
				$q.= ' AND (';
				
				$i = 0;
				foreach ($filter as $akey => $aval) {
					if (isset($aval) && $aval != '') {
						if ($i > 0) $q .= ' AND ';
						$q.= ' '.$akey. ' LIKE ' . replace_quote($aval,'like');
						$i++;
					}
				}
				$q.= ' ) ';
			}
		} else{

			if (isset($attr['cc_job_id']) && $attr['cc_job_id'] != '') {
				$q.= ' AND cc_job_id = ' . replace_quote($attr['cc_job_id']);
	        }

	        if (isset($attr['article']) && $attr['article'] > 0) {
				$q.= ' AND article =  '.$attr['article'];
	        }
		}

		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			// array('site_id','transaction_id','site_name','site_address','site_qty_value','flag_qty_value','method_calc','start_date_counting', 'reset_days', 'logo_file_name', 'chamber_sync_flag', 'field_sync', 'status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
			
			$q.= ' AND ( ';
			$q.= ' cc_job_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR article LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR site_address LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR method_calc LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR logo_file_name LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id =  '.replace_quote($attr['site_id']);
        }

        if (isset($attr['movement_type']) && $attr['movement_type'] != '') {
			$q.= ' AND movement_type =  '.replace_quote($attr['movement_type']);
        }

        if (isset($attr['not_movement_type']) && $attr['not_movement_type'] != '') {
			$q.= ' AND movement_type !=  '.replace_quote($attr['not_movement_type']);
        }

        if (isset($attr['reference_doc']) && $attr['reference_doc'] != '') {
			$q.= ' AND reference_doc =  '.replace_quote($attr['reference_doc']);
        }

		if (isset($attr['is_progress']) && in_array($attr['is_progress'],array(-1,0,1,10))) {
			$q.= ' AND is_progress = '.$attr['is_progress'];
        } else {
			$q.= ' AND is_progress != -1';
		}
        
        $result['total_rows'] = count(orm_get_list($q));
		
        if (isset($attr['group_by']) && $attr['group_by'] != '') {
			$q.= ' GROUP BY '.$attr['group_by'];
        }

		if (isset($attr['order'])) { 
			$q.= ' ORDER BY ' . $attr['order'];
			if (isset($attr['orderby'])) $q .= ' '.$attr['orderby']; 
		} else  {
			$q.= ' ORDER BY '. $this->primary_key .' DESC';
		}
		
		// set default paging
		if (! isset($attr['paging'])) {
			if (! isset($attr['offset'])) $attr['offset'] = OFFSET;
			if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
		}
		
		if (isset($attr['offset'])) { 
			$q.= ' LIMIT ' . $attr['offset'];
			
			if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
			
			$q.= ', ' . $attr['perpage'];
		}

		$data = orm_get_list($q);
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	public function save()
	{
        $post = $attr = $result = NULL;
		if (! empty($_POST)) $post = $_POST;
		
		// validate_column
		$attr = validate_column($this->list_column, $post);
        
        if (! empty($attr)) {
            $save = DB::table($this->table)->insert($attr);
            
            if ($save) {
                $result['last_insert_id'] = DB::getPdo()->lastInsertId();
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

	public function save_bulk()
	{
        $post = $attr = $result = NULL;
        $list_field = $list_value = '';
		if (! empty($_POST)) $post = $_POST;
		// validate_column
		// $attr = validate_column($this->list_column, $post);
        // debug($post,1);
        if (! empty($post['list_data'])) {
        	$q = 'INSERT INTO '. $this->table;
        	$q.= ' (';

        	foreach ($post['list_data'] as $arrKey => $data) {
        		if ($arrKey) {
					$i = 1;
					foreach($data as $key => $val) {
						$q.= $key;
						if ($i != count($data)) 
						{
							$q.= ', ';
						}
						$i++;
					}
					break;
				}
        	}

        	$q.= ") VALUES";
        	$z = 0;
        	foreach($post['list_data'] as $arrKey => $data) {
				if ($arrKey) {
					$i = 1;
					foreach($data as $key => $val) {
						$list_field.= $key;
						if ($i != count($data)) 
						{
							$list_field.= ', ';
						}
						$i++;
					}
				}
				
				$x = 1;
				$list_value.= '(';
				foreach($data as $keyd => $valx) {
					$list_value.= replace_quote($valx);
					if ($x != count($data)) $list_value.= ', ';
					if($keyd )
					$x++;
				}
				$list_value.= ')';
				// remove comma
				if ($z != count($post['list_data'])-1) {
					$list_value.= ', ';
				}
				$z++;
			}
			$q.= $list_value;
			$save = DB::statement($q);
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
	
	public function update()
	{
		$put = $attr = $result = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') parse_str(file_get_contents("php://input"), $_PUT);

        $put = $_PUT;
		
		$attr = validate_column($this->list_column, $put);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($attr)) $result['message'] = 'no data';
        
		if (! isset($attr[$this->primary_key])) $result['message'] = $this->primary_key . ' must be filled.';
		
		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		$param_where = $attr[$this->primary_key];
		unset($attr[$this->primary_key]);

		$update = DB::table($this->table)
			->where($this->primary_key, $param_where)
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
	
	public function delete()
	{
		$delete = $attr = $result = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') parse_str(file_get_contents("php://input"), $_DELETE);

        $delete = $_DELETE;
		
		$attr = validate_column($this->list_column, $delete);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($attr)) $result['message'] = 'no data';
        
		if (! isset($attr[$this->primary_key])) $result['message'] = $this->primary_key . ' must be filled.';
		
		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		$param_where = $attr[$this->primary_key];
		// unset($attr[$this->primary_key]);
		$attr['status'] = '-1';

		$update = DB::table($this->table)
			->where($this->primary_key, $param_where)
			->update($attr);
			
		if ($update) {
			$result['is_success'] = 1;
			$result['message'] = 'delete success';
		} else {
			$result['is_success'] = 0;
			$result['message'] = 'delete failed';
			// $result['query'] = $update->toSql();
		}

        echo json_encode($result);
        die;
	}

	// cycle count damage goods
	// decrease and stock_dashboard and increase stock_damage
	public function damage_goods(){
		$post = $result = NULL;

		if(!empty($_POST)) $post = $_POST;

		// Operation Start
		if(isset($post['update']) && !empty($post['update']) && !empty($post['qty'])){
			// update stock_dashboard and stock_damaged
			$q = 'UPDATE ms_article_stock SET ';
			for($i=0; $i<count($post['update']); $i++){
				if($post['update'][$i] == 'stock_damaged'){
					$q.= $post['update'][$i].' = '.$post['update'][$i].' + '.$post['qty'].', ';
				} elseif($post['update'][$i] == 'stock_dashboard'){
					$q.= $post['update'][$i].' = '.$post['update'][$i].' - '.$post['qty'].', ';
				}
			}
			$q.= ' updated_at = '.replace_quote($post['updated_at']).', 
					updated_by = '.replace_quote($post['updated_by']).', 
					updated_ip = '.replace_quote($post['updated_ip']).' 
					WHERE article = '.replace_quote($post['article']).' 
					AND site_id = '.replace_quote($post['site_id']);
			$update_stock = DB::statement($q);

			if ($update_stock) {
				$result['is_success'] = 1;
				$result['message'] = 'Cycle Count Damage Goods Success!';
			} else {
				$result['is_success'] = 0;
				$result['message'] = 'Cycle Count Damage Goods Failed';
				// $result['query'] = $update->toSql();
			}
	        echo json_encode($result);
	        die;
		}
	}

	// cycle count discrepancy
	// decrease stock_dashboard and increase stock_cc
	// public function discrepancy_minus(){
	// 	$post = $result = NULL;

	// 	if(!empty($_POST)) $post = $_POST;

	// 	// Operation Start
	// 	if(isset($post['update']) && !empty($post['update']) && !empty($post['qty'])){
	// 		// update stock_dashboard and stock_damaged
	// 		$q = 'UPDATE ms_article_stock SET ';
	// 		for($i=0; $i<count($post['update']); $i++){
	// 			if($post['update'][$i] == 'stock_dashboard'){
	// 				$q.= $post['update'][$i].' = '.$post['update'][$i].' + '.$post['qty'].', ';
	// 			} elseif($post['update'][$i] == 'stock_disc'){
	// 				$q.= $post['update'][$i].' = '.$post['update'][$i].' + '.$post['qty'].', ';
	// 			}
	// 		}
	// 		$q.= ' updated_at = '.replace_quote($post['updated_at']).', 
	// 				updated_by = '.replace_quote($post['updated_by']).', 
	// 				updated_ip = '.replace_quote($post['updated_ip']).' 
	// 				WHERE article = '.replace_quote($post['article']).' 
	// 				AND site_id = '.replace_quote($post['site_id']);
	// 		$update_stock = DB::statement($q);

	// 		if ($update_stock) {
	// 			$result['is_success'] = 1;
	// 			$result['message'] = 'Cycle Count Damage Goods Success!';
	// 		} else {
	// 			$result['is_success'] = 0;
	// 			$result['message'] = 'Cycle Count Damage Goods Failed';
	// 			// $result['query'] = $update->toSql();
	// 		}
	//         echo json_encode($result);
	//         die;
	// 	}
	// }

	// cycle count write_off
	// just decrease stock_dashboard, cause stock_qty decreased when chamber replenish out
	public function write_off(){
		$post = $result = NULL;

		if(!empty($_POST)) $post = $_POST;
		
		if(isset($post['qty'])){
			$q = 'UPDATE ms_article_stock SET ';

			for($i=0; $i<count($post['update']); $i++){
				if($post['update'][$i] == 'stock_dashboard'){
					$q.= $post['update'][$i].' = '.$post['update'][$i].' - '.$post['qty'].', ';
				}  elseif($post['update'][$i] == 'stock_cc'){
					$q.= $post['update'][$i].' = '.$post['update'][$i].' - '.$post['qty'].', ';
				} elseif($post['update'][$i] == 'stock_damaged'){
					$q.= $post['update'][$i].' = '.$post['update'][$i].' - '.$post['qty'].', ';
				}
				// will be use later
				// elseif($post['update'][$i] == 'stock_disc'){
				// 	$q.= $post['update'][$i].' = '.$post['update'][$i].' - '.$post['qty'].', ';
				// }
			}

			$q.= '	updated_at = '.replace_quote($post['updated_at']).', 
					updated_by = '.replace_quote($post['updated_by']).', 
					updated_ip = '.replace_quote($post['updated_ip']).' 
					WHERE article = '.replace_quote($post['article']).' 
					AND site_id = '.replace_quote($post['site_id']);

			$update_stock = DB::statement($q);

			if ($update_stock) {
				$result['is_success'] = 1;
				$result['message'] = 'Cycle Count Write Off Success!';
			} else {
				$result['is_success'] = 0;
				$result['message'] = 'Cycle Count Write Off Failed';
				// $result['query'] = $update->toSql();
			}
	        echo json_encode($result);
	        die;
		}
	}

	// cycle count
	// decrease stock_dashboard and increase stock_cc
	public function cycle_count(){
		$post = $result = NULL;

		if(!empty($_POST)) $post = $_POST;

		// Operation Start
		if(isset($post['update']) && !empty($post['update']) && !empty($post['qty'])){
			// update stock_dashboard and stock_damaged
			$q = 'UPDATE ms_article_stock SET ';
			for($i=0; $i<count($post['update']); $i++){
				if($post['update'][$i] == 'stock_dashboard'){
					$q.= $post['update'][$i].' = '.$post['update'][$i].' - '.$post['qty'].', ';
				} elseif($post['update'][$i] == 'stock_cc'){
					$q.= $post['update'][$i].' = '.$post['update'][$i].' + '.$post['qty'].', ';
				}
			}
			$q.= ' updated_at = '.replace_quote($post['updated_at']).', 
					updated_by = '.replace_quote($post['updated_by']).', 
					updated_ip = '.replace_quote($post['updated_ip']).' 
					WHERE article = '.replace_quote($post['article']).' 
					AND site_id = '.replace_quote($post['site_id']);
			$update_stock = DB::statement($q);

			if ($update_stock) {
				$result['is_success'] = 1;
				$result['message'] = 'Cycle Count Success!';
			} else {
				$result['is_success'] = 0;
				$result['message'] = 'Cycle Count Failed';
				// $result['query'] = $update->toSql();
			}
		} else {
			$result['is_success'] = 0;
			$result['message'] = 'No data processed';
		}
		
		echo json_encode($result);
		die;
	}


	public function __destruct()
	{
		// parent::__construct();
	}
	
}