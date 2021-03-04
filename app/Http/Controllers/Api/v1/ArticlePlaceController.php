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

use App\Models\ArticlePlaceModel;

class ArticlePlaceController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'ms_article_place';
    public $primary_key = 'article_place_id';
	public $list_column = array('article_place_id','article_id','art_column', 'art_row', 'art_rack', 'status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
	
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
		
		if (isset($attr['article_place_id']) && $attr['article_place_id'] != '') {
			$q.= ' AND article_place_id = '.replace_quote($attr['article_place_id']);
		}

		if (isset($attr['article_id']) && $attr['article_id'] != '') {
			$q.= ' AND article_id = '.replace_quote($attr['article_id']);
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
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' art_column LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR art_row LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR art_rack LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }

		if (isset($attr['article_id']) && $attr['article_id'] != '') {
			$q.= ' AND article_id = '.replace_quote($attr['article_id']);
		}

		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status = 1';
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

	public function get_list_ajax()
	{
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT a.article, a.site_id, ap.article_place_id, ap.art_column, ap.art_row, ap.art_rack FROM ' . $this->table . ' ap ';
		$q.= ' LEFT JOIN ms_article a ON ap.article_id = a.article_id';
		$q.= ' WHERE 1';

		if (isset($attr['article_id']) && $attr['article_id'] != '') {
			$q.= ' AND ap.article_id = '.replace_quote($attr['article_id']);
		}

		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND ap.status = '.$attr['status'];
        } else {
			$q.= ' AND ap.status = 1';
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
		if (! empty($_POST)) $post = $_POST;
		$attr = array();
		// validate_column
		if($post){
			for($i=0;$i<count($post);$i++){
				$attr[] = validate_column($this->list_column, $post[$i]);
			}
		}

        if (! empty($attr)) {
        	for($i=0; $i<count($attr); $i++){
        		$save = DB::table($this->table)->insert($attr[$i]);
	            if ($save) {
	                $result['last_insert_id'] = DB::getPdo()->lastInsertId();
					$result['is_success'] = 1;
	                $result['message'] = 'save success';
	            } else {
					$result['is_success'] = 0;
	                $result['message'] = 'save failed';
	            }
        	}	
        }

        echo json_encode($result);
        die;
	}

	public function save_update_bulk(){
		$post = $attr = $result = NULL;
		if (! empty($_POST)) $post = $_POST;

		$list_field = $list_value = '';
		
		$query = 'INSERT INTO ' . $this->table;
		
		$query .= ' (';
		foreach($post as $arrkey => $data) {
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
		foreach($post as $arrkey => $data) {
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
			if ($arrkey != count($post)-1) {
				$list_value.= ' ,';
			}
			
		}
		
		$query.= $list_value;

		$query.= ' ON DUPLICATE KEY UPDATE ';
		
		foreach($post as $arrkey => $data) {
			if ($arrkey == 0) {
				$i = 1;
				foreach($data as $key => $val) {
					if($key != "article" && $key != "site_id"){
						$query.= $key . ' = VALUES(' . $key . ')';
						if ($i != count($data)) {
							$query.= ', ';
						}
						$i++;
					}
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
				else $param_where[$pk] = $delete[$pk];
			}
		} else {
			if (! isset($attr[$this->primary_key])) $result['message'] = $this->primary_key . ' must be filled.';
			$param_where[$this->primary_key] = $delete[$this->primary_key];
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