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

// use App\Models\ArticleModel;

class PrepackBundlingHeaderController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'tr_prepack_bundling_header';
    public $primary_key = ['site_id','outbound_delivery'];
    public $list_column = array('outbound_delivery','site_id','conversion_value','combine_qty','conversion_diff','user_id','status','status_message','created_at','created_by','created_ip','updated_at','updated_by','updated_ip');
	
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

	public function generate_id($str = 0, $prefix = "PO",$digitno = 4)
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
	
	public function get_new_id($prefix = "PO")
	{
		$date_month = date("Ymd", time() );
		$col = "outbound_delivery";
		$sql = "
		SELECT " . $col . " 
		FROM " . $this->table . " 
		WHERE " . $col . " LIKE '%".$prefix.$date_month."_%' 
		ORDER BY " . $col . " DESC 
		LIMIT 1;";
		
		$last_id = orm_get($sql, $col);
		
		$new_id = 0;
		if(isset($last_id)) $new_id = get_right($last_id,4);
		// debug($new_id,1);
		$val = $this->generate_id($new_id);
		$response['new_id'] = $val;
		echo json_encode($response);
		die;
	}

	// with eloqueen
	public function get_list_status()
	{
		// $log = ArticleModel::all();
		// $log = ArticleModel::where('article_id',3)
		// $log = ArticleModel::whereName('mantap')
		$log = ArticleModel::whereStatus('1')
						->get()
						->all();
						// ->toSql()->get();
		// $data = 

						// $log = $logModel->toSql();
		echo json_encode($log);

		die;
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
		
		if (isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != '') {
			$q.= ' AND outbound_delivery = '.replace_quote($attr['outbound_delivery']);
		}

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
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
			
		$q = 'SELECT prepack_header.outbound_delivery, prepack_header.site_id, prepack_header.status_message, prepack_header.conversion_value, prepack_header.combine_qty, prepack_header.status, prepack_header.created_at,';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE prepack_header.created_by = u.user_id), prepack_header.created_by) as creator,';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE prepack_header.user_id = u.user_id), prepack_header.user_id) as user_id';
		$q.= ' FROM ' . $this->table . ' prepack_header WHERE 1';
		$q.= ' HAVING 1';
		
		if (isset($attr['prepack_id']) && $attr['prepack_id'] != '') {
			$q.= ' AND prepack_id = '.$attr['prepack_id'];
		}
		if (isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != '') {
			$q.= ' AND outbound_delivery = '.$attr['outbound_delivery'];
        }

        if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' outbound_delivery LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR site_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
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
			$q.= ' ORDER BY outbound_delivery DESC';
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
		foreach ($this->primary_key as $kpk => $pk) {
			unset($attr[$pk]);
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