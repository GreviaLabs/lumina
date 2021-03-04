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

use App\Models\ArticleModel;

class ArticleSyncController extends SyncController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'ms_article';
    public $primary_key = 'article_id';
	public $list_column = array('article_id','site_id', 'article', 'customer_article' ,'description','uom','conversion_value','safety_stock','column','rack','row','price','chamber_sync_flag','status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
	
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
		
		if (isset($attr['article_id']) && $attr['article_id'] != '') {
			$q.= ' AND article_id = '.replace_quote($attr['article_id']);
		}

		if (isset($attr['article']) && $attr['article'] != '') {
			$q.= ' AND article = '.replace_quote($attr['article']);
        }

		if (isset($attr['site_id']) && $attr['site_id'] != '' && isset($attr['article']) && $attr['article'] != '') {
			$q.= ' AND site_id = '. replace_quote($attr['site_id']);
			$q.= ' AND article = '. replace_quote($attr['article']);
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
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
        $q = 'SELECT * FROM ' . $this->table . ' WHERE 1';
        
        if (isset($attr['chamber_sync_flag']) && $attr['chamber_sync_flag'] != '') {
			$q.= ' AND chamber_sync_flag = '.$attr['chamber_sync_flag'];
		} else {
			$q.= ' AND chamber_sync_flag = 10';
		}
		
		if (isset($attr['article_id']) && $attr['article_id'] != '') {
			$q.= ' AND article_id = '.replace_quote($attr['article_id']);
		}

		if (isset($attr['article']) && $attr['article'] != '') {
			$q.= ' AND article = '.replace_quote($attr['article']);
		}
		
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR customer_article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
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
			
			$q.= ', ' . $attr['perpage'];
		}

		$data = orm_get_list($q);
		if (empty($data)) $data = NULL;
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	// API for sync data to chamber
	// public function get_list_sync()
	// {
	// 	$attr = $result = NULL;
	// 	if (! empty($_GET)) $attr = $_GET;
			
	// 	$q = 'SELECT * FROM ' . $this->table . ' WHERE 1';
		
	// 	if (isset($attr['chamber_sync_flag']) && $attr['chamber_sync_flag'] != '') {
	// 		$q.= ' AND chamber_sync_flag = '.$attr['chamber_sync_flag'];
	// 	}
		
	// 	if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
	// 		$q.= ' AND status = '.$attr['status'];
    //     } else {
	// 		$q.= ' AND status != -1';
	// 	}
        
    //     $result['total_rows'] = count(orm_get_list($q));
		
	// 	// Template general 
	// 	if (isset($attr['order'])) { 
	// 		$q.= ' ORDER BY ' . $attr['order'];
	// 		if (isset($attr['orderby'])) $q .= ' '.$attr['orderby']; 
	// 	} else  {
	// 		$q.= ' ORDER BY ';
	// 		if (is_array($this->primary_key)) {
	// 			foreach ($this->primary_key as $kpk => $pk) {
	// 				$q.= $pk .' DESC';
	// 				if ($kpk != count($this->primary_key)-1) $q.= ', ';
	// 			}
	// 		} 
	// 		else 
	// 			$q.= $this->primary_key .' DESC';
	// 	}
		
	// 	// set default paging
	// 	if (! isset($attr['paging'])) {
	// 		if (! isset($attr['offset'])) $attr['offset'] = OFFSET;
	// 		if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
	// 	}
		
	// 	if (isset($attr['offset'])) { 
	// 		$q.= ' LIMIT ' . $attr['offset'];
			
	// 		if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
			
	// 		$q.= ', ' . $attr['perpage'];
	// 	}

	// 	$data = orm_get_list($q);
	// 	if (empty($data)) $data = NULL;
    //     $result['data'] = $data;
        
    //     echo json_encode($result); 
	// 	die;
	// }

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
		$put = $attr = $result = $param_where = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') parse_str(file_get_contents("php://input"), $_PUT);

		$put = $_PUT;
		$attr = validate_column($this->list_column, $put);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($attr)) $result['message'] = 'no data';
		
		// Check primary key validity
		if (is_array($this->primary_key)) {
			foreach ($this->primary_key as $kpk => $pk) {
				if (! isset($attr[$pk])) $result['message'] = $pk . ' must be filled.';
				else $param_where[$pk] = $put[$pk];
			}
		} else {
			if (! isset($attr[$this->primary_key])) $result['message'] = $this->primary_key . ' must be filled.';
			$param_where[$this->primary_key] = $put[$this->primary_key];
		}
		
		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		
		// remove data from pk where
		if (is_array($this->primary_key)) {
			foreach ($this->primary_key as $kpk => $pk) {
				unset($attr[$pk]);
			}
		} else {
			unset($attr[$this->primary_key]);
		}

		$update = DB::table($this->table)
			->where($param_where)
			->update($attr);
			
		if ($update) {
			$result['is_success'] = 1;
			$result['message'] = 'update success';
		} else {
			$result['is_success'] = 0;
			$result['message'] = 'update failed';
			// $result['querymessage'] = debug($update);
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
        if (empty($post['article_id'])) $result['message'][] = 'article_id must be array';
		
		// Check primary key validity
		if (! isset($post[$this->primary_key])) $result['message'][] = $this->primary_key . ' must be filled';
		if (! isset($post['article_id'])) $result['message'][] = 'article_id must be filled';

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
		// $list_param['field_sync'] = NULL; //reset data
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
	
	public function delete()
	{
		$delete = $attr = $result = $param_where = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') parse_str(file_get_contents("php://input"), $_DELETE);

        $delete = $_DELETE;
		
		$attr = validate_column($this->list_column, $delete);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($attr)) $result['message'] = 'no data';
        
		// Check primary key validity
		if (is_array($this->primary_key)) {
			foreach ($this->primary_key as $kpk => $pk) {
				if (! isset($attr[$pk])) $result['message'] = $pk . ' must be filled.';
				else $param_where[$pk] = $put[$pk];
			}
		} else {
			if (! isset($attr[$this->primary_key])) $result['message'] = $this->primary_key . ' must be filled.';
			$param_where[$this->primary_key] = $put[$this->primary_key];
		}
		
		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		$attr['status'] = '-1';

		$update = DB::table($this->table)
			->where($param_where)
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