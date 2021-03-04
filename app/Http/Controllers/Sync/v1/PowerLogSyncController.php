<?php

namespace App\Http\Controllers\Sync\v1;

use App\Http\Controllers\Controller;

use Input;
use Validator;
use Session;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Request;

// use Request;
use DB;

class PowerLogController extends SyncController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'ms_power_log';
    public $primary_key = 'power_log_id';
    public $list_column = array('site_id', 'pin_ups', 'field_sync',);
    // public $list_column = array('site_id','pin_ups','pressed', 'status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
	
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
			
		$q = '
		SELECT *
		FROM ' . $this->table . '
		WHERE 1';
		
		if (isset($attr['power_log_id']) && $attr['power_log_id'] != '') {
			$q.= ' AND power_log_id = '.$attr['power_log_id'];
		}
		
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}
		
		// if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
		// 	$q.= ' AND status = '.$attr['status'];
        // } else {
		// 	$q.= ' AND status != -1';
		// }

		$data = orm_get($q);
		if (empty($data)) $data['data'] = NULL;
		echo json_encode($data);
		die;
	}
	
	public function get_list()
	{
		//coba disini
		// $query = "INSERT INTO ms_power_log(site_id,pin_ups) VALUES('sz24','1'), ('sz25','22'), ('sz26','32'), ('sz30','testing');";
		// $run = DB::statement($query);
		// debug($run,1);

		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		// $q = '
		// SELECT * 
		// FROM ' . $this->table . ' 
		// WHERE 1';
		
		$q = '
        SELECT *
        FROM ' . $this->table . '
        WHERE 1';
        
        if (isset($attr['dashboard_sync_flag']) && $attr['dashboard_sync_flag'] != '') {
			$q.= ' AND dashboard_sync_flag = '.$attr['dashboard_sync_flag'];
		} else {
			$q.= ' AND dashboard_sync_flag = 10';
		}
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {			
			$q.= ' AND ( ';
			$q.= ' site_id LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR site_name LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR site_address LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR method_calc LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR logo_file_name LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
		
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = ' . replace_quote($attr['site_id']);
        }
		
		// if (isset($attr['company_id']) && $attr['company_id'] != '') {
		// 	$q.= ' AND s.company_id = '.$attr['company_id'];
        // }
		
		// if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
		// 	$q.= ' AND status = '.$attr['status'];
        // } else {
		// 	$q.= ' AND status != -1';
		// }
        
        $result['total_rows'] = count(orm_get_list($q));
		
		if (isset($attr['order'])) { 
			
			// extra order table company
			// $extra_order = array('company_name');			
			// if (in_array($attr['order'],$extra_order)) $attr['order'] = 'c.'.$attr['order'];

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
		if (empty($data)) $data = NULL;
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
	
	//save bulk json
	// Receive JSON
	public function save_bulk()
	{
		$post = $attr = $result = $param_where = $post_site_id = NULL;

		// Sample data : [{"transaction_id":"SZ20.20190426.0001","site_id":"SZ20","outbound_delivery":"","article":"","rfid":"","description":"","conversion_value":0,"qty":0,"picktime":"","user_id":9,"sync_date":null,"chamber_sync_flag":30,"field_sync":0,"last_sync":"\/Date(1560397283653)\/","flag_used":1,"price":0,"movement_type":101,"site_chamber_gr":"","status_message":"","status":1,"created_at":"4/26/2019 11:10:32 AM","created_by":"USER_TEST","created_ip":"127.0.0.1","updated_at":"","updated_by":"","updated_ip":""},{"transaction_id":"SZ20.20190426.0002","site_id":"SZ20","outbound_delivery":"","article":"","rfid":"","description":"","conversion_value":0,"qty":0,"picktime":"","user_id":9,"sync_date":null,"chamber_sync_flag":30,"field_sync":0,"last_sync":"\/Date(1560397283656)\/","flag_used":1,"price":0,"movement_type":101,"site_chamber_gr":"","status_message":"","status":1,"created_at":"4/29/2019 1:51:30 PM","created_by":"USER_TEST","created_ip":"127.0.0.1","updated_at":"","updated_by":"","updated_ip":""}]
		if (! empty(file_get_contents('php://input'))) {
            $post = json_decode(file_get_contents('php://input'), 1);

            $jsondata = file_get_contents('php://input');
            $param['name'] = 'sync post';
            $param['url'] = 'transaction/save_bulk';
            $param['data'] = $jsondata;
            $save = save_logapi($param);
        }
		
		// debug($post,1);
		// $attr = validate_column($this->list_column, $put);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($post)) $result['message'][] = 'no data';
        // if (! isset($post['site_id'])) $result['message'][] .= 'site_id must be filled';
        // if (empty($post[$this->primary_key])) $result['message'][] = $this->primary_key . ' must be array';
		
		// Check primary key validity
		// if (! isset($post['site_id'])) $result['message'][] = 'site_id must be filled';
		// if (! isset($post[$this->primary_key])) $result['message'][] = $this->primary_key . ' must be filled';

		// Print error if message exist
		if (!empty(($result['message']))) {
			$result['message'] = implode("; ",$result['message']);
			$result['is_success'] = 0;
			// if (isset($post)) $result['paramdata'] = $post;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/

		$list_param = $list_value = NULL;
		$strvalues = $strparam = NULL;
		// debug($post[0],1);

		// remove data from pk where
		if (! empty($post)) {
			for($i=0; $i<count($post); $i++){
				
				$post[$i]['dashboard_sync_flag'] = '50';
				$post[$i]['last_sync'] = get_datetime();

				if ($i == 0) {
					// Inject header only to array
					foreach ($post[$i] as $header => $val) {
						if ($header == 'sync_date') {
							continue;
						}
						$list_param[] = $header;
					}
					
					// unset($list_param[$i]['last_sync']);
					

					$strparam .= '( ' . implode(',',$list_param) . ' )';
				}

				$tmp_value = NULL;

				// Inject value only to array
				unset($post[$i]['sync_date']);
				$list_values = array_values($post[$i]);
				// unset($post[$i]['last_sync']);

				// $tmpval = array_values($post[$i]);
				// $tmp = array_map(function($value) {
				// 	return $value === "" ? "NULL" : replace_quote($value);
				//  }, $tmpval); 
				// $list_values = $tmpval;
				$x = 0;
				foreach ($list_values as $tv) {
					$val = "NULL";
					if (isset($tv) && $tv != '') {
						$val = replace_quote($tv);
						// debug('bangst',1);
					}
					$list_values[$x] = $val;
					$x++;
				}
				// debug($list_values,1);

				$strvalues .= '( ' . implode(',',$list_values) . ' )';
				if ($i != (count($post) - 1)) {
					$strvalues .= ", ";
				}

				// foreach ($this->primary_key as $kpk => $pk) {
				// 	// Set hardcode
				// 	$post[$i]['chamber_sync_flag'] = 50;
				// 	$post[$i]['field_sync'] = NULL; //reset data
				// 	$post[$i]['last_sync'] = get_datetime();
				// }
			}
			
		} else {
			for($i=0; $i<count($post); $i++){
				unset($post[$i][$this->primary_key]);
			}
		}
		// debug($list_param,1);
		// debug($list_values,1);

		// $array2 = array_map(function($value) {
		// 	return $value === "" ? NULL : $value;
		//  }, $array); 

		// debug($list_param);
		// echo "<br/>";
		// debug($list_value,1);

		// for ()
		
		// $list_param = NULL;
		// $list_param['chamber_sync_flag'] = 50;
		// $list_param['field_sync'] = NULL; //reset data
		// $list_param['last_sync'] = get_datetime();
		
		// $list_param.= 'chamber_sync_flag = 50 ,';
		// $list_param.= 'field_sync = NULL ,';
		// $list_param.= 'last_sync = '.replace_quote(get_datetime());
		
		
		
		$q = "INSERT IGNORE INTO " . $this->table;
		$q.= $strparam . ' VALUES ' . $strvalues;
		// debug('jalanax<br/>');
		
		// debug($q,1);
		// Example output
		// <pre>UPDATE ms_user SET chamber_sync_flag = '50' WHERE user_id IN (23,24,25)</pre>
		
		$update = DB::statement($q);

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
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}