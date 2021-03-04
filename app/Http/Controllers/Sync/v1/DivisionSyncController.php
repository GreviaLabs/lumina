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

// use App\Models\CompanyModel;

class DivisionSyncController extends SyncController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'ms_division';
    public $primary_key = 'division_id';
    public $list_column = array('division_name', 'status', 'chamber_sync_flag', 'last_sync', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
	
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
	
	public function get_list()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT * FROM ' . $this->table . ' WHERE 1';
		
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

	// Receive JSON
	public function update_bulk()
	{
		$post = $attr = $result = $param_where = $post_division_id = NULL;

		// Sample data : {"site_id":"SZ24","user_id":[20,24,25]}
		if (! empty(file_get_contents('php://input'))) $post = json_decode(file_get_contents('php://input'), 1);
		
		// debug($post,1);
        // $attr = validate_column($this->list_column, $put);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        // if (empty($post)) $result['message'][] = 'no data';
        // if (! isset($post['site_id'])) $result['message'][] .= 'site_id must be filled';
        // if (empty($post['division_id'])) $result['message'][] = 'division_id must be array';
		
		// Check primary key validity
		if (! isset($post[$this->primary_key])) $result['message'][] = $this->primary_key . ' must be filled';
		if (! isset($post['division_id'])) $result['message'][] = 'division_id must be filled';

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

		$list_id = NULL;
		$list_id = implode(",",$post[$this->primary_key]);
		
		$q = "UPDATE " . $this->table;
		$q.= " SET " . $list_param;
		$q.= " WHERE " . $this->primary_key . " IN (" . $list_id . ") AND division_id = " . replace_quote($post['division_id']);
		
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