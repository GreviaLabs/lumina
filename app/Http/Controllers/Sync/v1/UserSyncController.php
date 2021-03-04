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

class UserSyncController extends SyncController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'ms_user';
    public $primary_key = 'user_id';
    public $list_column = array('user_id', 'site_id', 'parent_user_id', 'level_id','user_code', 'firstname', 'lastname', 'quota_initial', 'quota_additional', 'quota_remaining', 'job_title', 'division','attribute','attribute_value', 'email', 'user_category', 'password', 'counter_wrong_pass', 'status_lock', 'locked_time', 'reset_by', 'reset_time', 'reset_token', 'reset_token_expired', 'status','chamber_sync_flag','last_sync', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
    public $list_required_column = array('email');
	
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

	// API for sync data to chamber
	public function get_list()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT * FROM ' . $this->table . ' WHERE 1';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = ' . replace_quote($attr['site_id']);
		}
		
		if (isset($attr['chamber_sync_flag']) && $attr['chamber_sync_flag'] != '') {
			$q.= ' AND chamber_sync_flag = '.$attr['chamber_sync_flag'];
		} else {
			$q.= ' AND chamber_sync_flag = 10';
		}
		
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
		}
        
        $result['total_rows'] = count(orm_get_list($q));
		
		// Template general 
		if (isset($attr['order'])) { 
			$q.= ' ORDER BY ' . $attr['order'];
			if (isset($attr['orderby'])) $q .= ' '.$attr['orderby']; 
		} else  {
			$q.= ' ORDER BY ';
			if (is_array($this->primary_key)) {
				foreach ($this->primary_key as $kpk => $pk) {
					$q.= $pk .' DESC';
					if ($kpk != count($this->primary_key)-1) $q.= ', ';
				}
			} 
			else 
				$q.= $this->primary_key .' DESC';
		}
		
		// set default paging
		if (! isset($attr['paging'])) {
			if (! isset($attr['offset'])) $attr['offset'] = OFFSET;
			if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
		}
		
		if (isset($attr['offset'])) { 
			$q.= ' LIMIT ' . $attr['offset'];
			
			if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
			if (isset($attr['limit'])) $attr['perpage'] = $attr['limit'];
			
			$q.= ', ' . $attr['perpage'];
		} 

		$data = orm_get_list($q);
		if (empty($data)) $data = NULL;
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
    }
    
    // API for sync data to chamber, with flag 20
	public function get_list_quota()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT user_id,quota_initial,quota_remaining FROM ' . $this->table . ' WHERE 1';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = ' . replace_quote($attr['site_id']);
		}
		
		if (isset($attr['chamber_sync_flag']) && $attr['chamber_sync_flag'] != '') {
			$q.= ' AND chamber_sync_flag = '.$attr['chamber_sync_flag'];
		} else {
			$q.= ' AND chamber_sync_flag = 20';
		}
		
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
		}
        
        $result['total_rows'] = count(orm_get_list($q));
		
		// Template general 
		if (isset($attr['order'])) { 
			$q.= ' ORDER BY ' . $attr['order'];
			if (isset($attr['orderby'])) $q .= ' '.$attr['orderby']; 
		} else  {
			$q.= ' ORDER BY ';
			if (is_array($this->primary_key)) {
				foreach ($this->primary_key as $kpk => $pk) {
					$q.= $pk .' DESC';
					if ($kpk != count($this->primary_key)-1) $q.= ', ';
				}
			} 
			else 
				$q.= $this->primary_key .' DESC';
		}
		
		// set default paging
		if (! isset($attr['paging'])) {
			if (! isset($attr['offset'])) $attr['offset'] = OFFSET;
			if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
		}
		
		if (isset($attr['offset'])) { 
			$q.= ' LIMIT ' . $attr['offset'];
			
			if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
			if (isset($attr['limit'])) $attr['perpage'] = $attr['limit'];
			
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
		// $attr = validate_column($this->list_column, $post);
		$attr = $post;
        
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
		if (! empty($_POST)) $post = $_POST;
		
		// validate_column
		// $attr = validate_column($this->list_column, $post);
		// $attr = $post;
        
        if (! empty($post)) {

			foreach ($post as $kp => $postdata) {
				$attr[] = validate_column($this->list_column,$postdata);
			}

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

	// for sync
	public function save_update_bulk()
	{
		$post = $attr = $result = NULL;
		if (! empty($_POST)) $post = $_POST;
		
		$list_field = $list_value = '';
		
		// $query = 'INSERT INTO mu_service(service_id,user_id,price,special_price,special_price_startdate,special_price_enddate,status,hour,minute) VALUES';
		$query = 'INSERT INTO ' . $this->table;
		
		$array_data = $post;

		$query .= ' (';
		foreach($array_data as $arrkey => $data) {
			if ($arrkey == 0) {
				$i = 1;
				foreach($data as $key => $val) {
					$query.= $key;
					if ($i != count($data)) 
					{
						$query.= ' ,';
					}
					$i++;
				}
				break;
			}
		}

		$query .= ') VALUES';
		// $query .= '';

		/*
		EXAMPLE RESULT
		INSERT INTO mu_service(service_id,user_id,price,status,hour,minute) VALUES('65','28','2000000','1','8','00') ,('63','28','750000','1','1','30') ,('62','28','750000','1','1','30') ,('60','28','5500000','1','3','00') ,('58','28','500000','1','1','00') ON DUPLICATE KEY UPDATE price = VALUES(price), status = VALUES(status), hour = VALUES(hour), minute = VALUES(minute)
		*/
		foreach($array_data as $arrkey => $data) {
			if ($arrkey == 0) {
				$i = 1;
				foreach($data as $key => $val) {
					$list_field.= $key;
					if ($i != count($data)) 
					{
						$list_field.= ' ,';
					}
					$i++;
				}
			}
			
			$x = 1;
			$list_value.= '(';
			foreach($data as $keyd => $valx) {
				$list_value.= replace_quote($valx);
				if ($x != count($data)) $list_value.= ',';
				
				$x++;
			}
			$list_value.= ')';
			
			// remove comma
			if ($arrkey != count($array_data)-1) {
				$list_value.= ' ,';
			}
			
		}
		
		$query.= $list_value;
		// $query.= ' ON DUPLICATE KEY UPDATE price = VALUES(price), special_price = VALUES(special_price), special_price_startdate = VALUES(special_price_startdate), special_price_enddate = VALUES(special_price_enddate), status = VALUES(status), hour = VALUES(hour), minute = VALUES(minute), editor_id = VALUES(editor_id), editor_ip = VALUES(editor_ip), editor_date = VALUES(editor_date);';
		$query.= ' ON DUPLICATE KEY UPDATE ';
		
		foreach($array_data as $arrkey => $data) {
			if ($arrkey == 0) {
				$i = 1;
				foreach($data as $key => $val) {
					$query.= $key . ' = VALUES(' . $key . ')';
					if ($i != count($data)) {
						$query.= ' ,';
					}
					$i++;
				}
			}
			break;
		}
		$update = DB::statement($query);

		if ($update) {
			$result['is_success'] = 1;
			$result['message'] = 'save update success';
		} else {
			$result['is_success'] = 0;
			$result['message'] = 'save update failed';
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
		
		// $attr = validate_column($this->list_column, $put);
		$attr = $put;
		
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

	// Receive JSON
	public function update_bulk()
	{
		$post = $attr = $result = $param_where = $post_site_id = NULL;

		// Sample data : {"site_id":"SZ24","user_id":[20,24,25]}
		if (! empty(file_get_contents('php://input'))) $post = json_decode(file_get_contents('php://input'), 1);
		
		// debug($post,1);
		// $attr = validate_column($this->list_column, $put);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($post)) $result['message'][] = 'no data';
        if (! isset($post['site_id'])) $result['message'][] .= 'site_id must be filled';
        if (empty($post['user_id'])) $result['message'][] = 'user_id must be array';
		
		// Check primary key validity
		if (! isset($post[$this->primary_key])) $result['message'][] = $this->primary_key . ' must be filled';
		if (! isset($post['site_id'])) $result['message'][] = 'site_id must be filled';

		// Print error if message exist
		if (!empty(($result['message']))) {
			$result['message'] = implode("; ",$result['message']);
			$result['is_success'] = 0;
			// if (isset($post)) $result['paramdata'] = $post;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		// remove data from pk where

		if (is_array($this->primary_key)) {
			for($i=0; $i<count($post); $i++){
				foreach ($this->primary_key as $kpk => $pk) {
					// Set hardcode
					$post[$i]['chamber_sync_flag'] = 50;
					$post[$i]['last_sync'] = get_datetime();
					unset($post[$i][$pk]);
				}
			}
			
		} else {
			for($i=0; $i<count($post); $i++){
				unset($post[$i][$this->primary_key]);
			}
		}

		$list_param = NULL;
		// $list_param['chamber_sync_flag'] = 50;
		// $list_param['last_sync'] = get_datetime();

		$list_param.= 'chamber_sync_flag = 50 ,';
		$list_param.= 'last_sync = '.replace_quote(get_datetime());

		// foreach ($list_param as $kp => $paramval) {

		// }

		$list_id = NULL;
		$list_id = implode(",",$post[$this->primary_key]);
		
		$q = "UPDATE " . $this->table;
		$q.= " SET " . $list_param;
		$q.= " WHERE " . $this->primary_key . " IN (" . $list_id . ") AND site_id = " . replace_quote($post['site_id']);
		
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

	public function update_bulk_new()
	{
		$post = $attr = $result = $param_where = $post_site_id = NULL;

		if (! empty($_POST)) $post = $_POST;
		
		// debug($post,1);
		// $attr = validate_column($this->list_column, $put);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($post)) $result['message'] = 'no data';
		
		// Check primary key validity
		if (is_array($this->primary_key)) {
			for($i=0; $i<count($post); $i++){
				foreach ($this->primary_key as $kpk => $pk) {
					if (! isset($post[$i][$pk])) $result['message'] = $pk . ' must be filled.';
					else $param_where[$i][$pk] = $post[$i][$pk];

					// Validation here
					if (! isset($post[$i])) {

					}

				}
			}
			
		} else {
			for($i=0; $i<count($post); $i++){
				if (! isset($post[$i][$this->primary_key])) $result['message'] = $this->primary_key . ' must be filled.';
				$param_where[$i][$this->primary_key] = $post[$i][$this->primary_key];
			}
		}

		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($post)) $result['paramdata'] = $post;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		// remove data from pk where

		if (is_array($this->primary_key)) {
			for($i=0; $i<count($post); $i++){
				foreach ($this->primary_key as $kpk => $pk) {
					// Set hardcode
					$post[$i]['chamber_sync_flag'] = 50;
					$post[$i]['last_sync'] = get_datetime();
					unset($post[$i][$pk]);
				}
			}
			
		} else {
			for($i=0; $i<count($post); $i++){
				unset($post[$i][$this->primary_key]);
			}
		}

		$list_param = NULL;
		$list_id = NULL;
		for($i=0; $i<count($post); $i++){
			if ($i == 0) {
				$tmppost = NULL;
				$tmppost = $post[$i];
				foreach ($tmppost as $kpost => $tpost) {
					$list_param .= $kpost . ' = ' . replace_quote($tpost);
					if ($kpost != count($tmppost)-1) $list_param .= ","; 
				}
			}
			
			$list_id[] = $param_where[$i][$this->primary_key];
		}

		$list_id = implode(",",$list_id);
		
		$q = "UPDATE " . $this->table;
		$q.= " SET " . $list_param;
		$q.= " WHERE " . $this->primary_key . " IN (" . $list_id . ") AND site_id = " . replace_quote($post_site_id);
		
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
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}