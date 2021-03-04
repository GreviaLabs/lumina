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

class TransactionCCController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'tr_transaction_cc';
    public $primary_key = 'cc_id';
    public $list_column = array('cc_id','ref_no','site_id','rfid','article','description','movement_type','stock_qty','stock_cc','status_message','status','dashboard_sync_flag', 'last_sync', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
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
		
		if (isset($attr['cc_id']) && $attr['cc_id'] != '') {
			$q.= ' AND cc_id = ' . replace_quote($attr['cc_id']);
		}
		
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
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

			if (isset($attr['cc_id']) && $attr['cc_id'] != '') {
				$q.= ' AND cc_id = ' . replace_quote($attr['cc_id']);
	        }

	        if (isset($attr['article']) && $attr['article'] > 0) {
				$q.= ' AND article =  '.$attr['article'];
	        }
		}

		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			// array('site_id','transaction_id','site_name','site_address','site_qty_value','flag_qty_value','method_calc','start_date_counting', 'reset_days', 'logo_file_name', 'chamber_sync_flag', 'field_sync', 'status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
			
			$q.= ' AND ( ';
			$q.= ' cc_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR article LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR site_address LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR method_calc LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR logo_file_name LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id =  '.replace_quote($attr['site_id']);
        }

		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
		}
        
        $result['total_rows'] = count(orm_get_list($q));
		
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

	public function get_list_group(){
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;
		if($attr['token']) unset($attr['token']);

		$q = 'SELECT cc_id, site_id, article, movement_type, SUM(stock_qty) AS stock_qty, SUM(stock_cc) AS stock_cc FROM '. $this->table;
		$q.= ' WHERE 1 ';

		if(isset($attr['cc_id']) && $attr['cc_id'] != ''){
			$q.= 'AND cc_id = '.replace_quote($attr['cc_id']);
		}

		if(isset($attr['article']) && $attr['article'] != ''){
			$q.= 'AND article = '.replace_quote($attr['article']);
		}

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= 'AND site_id = '.replace_quote($attr['site_id']);
		}

		$q.= ' GROUP BY cc_id, site_id, article';

		$data = orm_get_list($q);
		echo json_encode($data);
	}

	public function get_group(){
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;
		if($attr['token']) unset($attr['token']);

		$q = 'SELECT cc_id, site_id, article, movement_type, SUM(stock_qty) AS stock_qty, SUM(stock_cc) AS stock_cc FROM '. $this->table;
		$q.= ' WHERE 1 ';

		if(isset($attr['cc_id']) && $attr['cc_id'] != ''){
			$q.= 'AND cc_id = '.replace_quote($attr['cc_id']);
		}

		if(isset($attr['article']) && $attr['article'] != ''){
			$q.= 'AND article = '.replace_quote($attr['article']);
		}

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= 'AND site_id = '.replace_quote($attr['site_id']);
		}

		$q.= ' GROUP BY cc_id, site_id, article';

		$data = orm_get($q);
		echo json_encode($data);
	}

	public function get_list_cc()
	{
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;
		if($attr['token']) unset($attr['token']);
		// if($attr['group_by']) $attr['group_by'] = implode(', ', $attr['group_by']);
		
		// if($attr['group_by']){
		// 	$q = 'SELECT cc_id, article, site_id, sum(stock_cc) as stock_cc, movement_type FROM ' . $this->table . ' WHERE 1';
		// }else{
		$q = 'SELECT cc_id, article, site_id, stock_cc, movement_type FROM ' . $this->table . ' WHERE 1';
		// }

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id =  '.replace_quote($attr['site_id']);
        }

        if (isset($attr['flag'])){
        	$q.= ' AND flag = '.$attr['flag'];
        }

        if(isset($attr['group_by'])){
        	$q.= ' GROUP BY '.$attr['group_by'];
        }

		$data = orm_get_list($q);
        // $result['data'] = $data;
        $result = json_encode($data);
        return $result;
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

	public function write_off(){
		$put = $attr = $result = NULL;

		if ($_SERVER['REQUEST_METHOD'] == 'PUT') parse_str(file_get_contents("php://input"), $_PUT);

        $put = $_PUT;
		
		$attr = validate_column($this->list_column, $put);

       	$q = "UPDATE tr_transaction_cc SET stock_cc = 0, stock_damage = 0 WHERE cc_id = '".$attr['cc_id']."' AND site_id = '".$attr['site_id']."' AND article = '".$attr['article']."'";
       	debug($q,1);
       	// $wo = DB::statement($q);
       	if ($wo) {
			$result['is_success'] = 1;
            $result['message'] = 'update success';
        } else {
			$result['is_success'] = 0;
            $result['message'] = 'update failed';
        }

        echo json_encode($result);
        die;
	}

	// cycle count from table trx_cc
	/* Operation:
	** 1. Update article_stock.stock_cc or stock_damaged
	** 2. Update article_stock.stock_qty (check if stock_cc or stock_damaged available then operate the stock_qty)
	** 3. Insert tr_movement_article based on tr_transaction_cc
	** 4. Insert tr_log_master (next step)
	*/
	public function cycle_count_from_chamber(){
		$attr = $result = NULL;
		$print = array();
		$stage = 0;
		$message = 'init';

		$result['is_success'] = 1;
		$result['message'] = NULL;

		// Select tr_transction_cc
		$list_data = $this->get_list_cc();
		$list_data = json_decode($list_data);

		if(count($list_data) > 0) $list_data = $list_data;

		if (empty($list_data)) $result['message'] = 'no data';

		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			// if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}
		/************ Start operation ************/
		for($i=0; $i<count($list_data); $i++){
			DB::beginTransaction();
			// 1 (Update article_stock.stock_cc or stock_damaged)
			try {
				$data_art_stock = $this->get_article_stock($list_data[$i]);
				if($list_data[$i]->movement_type == '301'){ //nanti ganti jadi mappingan
					// if movement_type stock_cc
					$q = "UPDATE ms_article_stock SET stock_cc = ".((int)$data_art_stock->stock_cc + (int)$list_data[$i]->stock_cc)." WHERE article = ".replace_quote($data_art_stock->article)." AND site_id = ".replace_quote($data_art_stock->site_id);
				} elseif($list_data[$i]->movement_type == '302'){ //nanti ganti jadi mappingan
					// if movement_type stock_damaged
					$q = "UPDATE ms_article_stock SET stock_damaged = ".((int)$data_art_stock->stock_damaged + (int)$list_data[$i]->stock_damaged)." WHERE article = ".replace_quote($data_art_stock->article)." AND site_id = ".replace_quote($data_art_stock->site_id);
				}
				debug($q,1);
				DB::statement($q);
				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}

			// 2 (Update article_stock.stock_qty (check if stock_cc or stock_damaged available then operate the stock_qty))
			try {
				$data_art_stock = $this->get_article_stock($list_data[$i]);
				if($data_art_stock->stock_cc != 0){
					$q = "UPDATE ms_article_stock SET stock_qty = ".((int)$data_art_stock->stock_qty - (int)$data_art_stock->stock_cc)." WHERE article = ".replace_quote($data_art_stock->article)." AND site_id = ".replace_quote($data_art_stock->site_id);
				} elseif ($data_art_stock->stock_damaged != 0) {
					$q = "UPDATE ms_article_stock SET stock_damaged = ".((int)$data_art_stock->stock_qty - (int)$data_art_stock->stock_damaged)." WHERE article = ".replace_quote($data_art_stock->article)." AND site_id = ".replace_quote($data_art_stock->site_id);
				}
				debug($q,1);
				DB::statement($q);
				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}

			// 3 (Insert tr_movement_article based on tr_transaction_cc)
			try {
				$qty = (! empty($list_data[$i]->stock_cc) ? $list_data[$i]->stock_cc : $list_data[$i]->stock_damaged);
				$q = "INSERT INTO tr_movement_article(receiving_site_id,article,qty,movement_type,status,reference,created_at,created_by,created_ip)
				VALUES(".replace_quote($list_data[$i]->site_id).",".replace_quote($list_data[$i]->article).",".$qty.",".$list_data[$i]->movement_type.",1".$list_data[$i]->cc_id.",".replace_quote(date("Y-m-d H:i:s")).",".replace_quote($who).",".replace_quote($ip).")";
				debug($q,1);
				DB::statement($q);
				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}
			DB::commit();
		}
		
		$print['message'] = $message;
		$print['stage'] = $stage;
		echo json_encode($print);
		die;
	}

	public function get_article_stock($data){
		$q = "SELECT article, site_id, stock_qty, stock_cc, stock_damaged FROM ms_article_stock WHERE article = ".replace_quote($data->article)." AND site_id = ".replace_quote($data->site_id);
		$select = orm_get($q);
		return $select;
	}
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}