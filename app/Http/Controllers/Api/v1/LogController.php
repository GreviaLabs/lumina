<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;

use Input;
use Validator;
use Session;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Request;

use Request;
use DB;

// namespace App;

use App\Models\LogModel;

class LogController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'tr_log';
    public $primary_key = 'level_id';
    public $list_column = array('log_id','name','url','method','data','response','created_at','created_by','created_ip');
	
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

	// with eloqueen
	public function get_list_status()
	{
		// $log = LogModel::all();
		// $log = LogModel::where('log_id',3)
        // $log = LogModel::whereName('mantap')
        $log = $data = NULL;
		$log['rowcount'] = 9;
		$log['data'] = LogModel::whereStatus('1')
						->get()
						->all();
						// ->toSql()->get();
		// $data = 

						// $log = $logModel->toSql();
		echo json_encode($log);

		// $log = $this->sql_debug($log);
		// debug($log);
		die;
		// debug($log);
		// debug('mantap from model<hr/>');
		// debug($log,1);
		// echo $log->
	}

	public function sql_debug($sql_string, array $params = null) {
		if (!empty($params)) {
			$indexed = $params == array_values($params);
			foreach($params as $k=>$v) {
				if (is_object($v)) {
					if ($v instanceof \DateTime) $v = $v->format('Y-m-d H:i:s');
					else continue;
				}
				elseif (is_string($v)) $v="'$v'";
				elseif ($v === null) $v='NULL';
				elseif (is_array($v)) $v = implode(',', $v);
	
				if ($indexed) {
					$sql_string = preg_replace('/\?/', $v, $sql_string, 1);
				}
				else {
					if ($k[0] != ':') $k = ':'.$k; //add leading colon if it was left out
					$sql_string = str_replace($k,$v,$sql_string);
				}
			}
		}
		return $sql_string;
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
		
		if (isset($attr['log_id']) && $attr['log_id'] != '') 
		{
			$q.= ' AND log_id = '.$attr['log_id'];
		}
		
		$data = orm_get($q,NULL,'json');
		debug($data);
		die;
	}
	
	public function get_list()
	{
		
		// dd($response->headers); 
		// $header = $secretkey = NULL;
		// if (Request::header('secretkey')) {
		// 	$header = Request::header();
		// 	$secretkey = Request::header('secretkey');
		// }

		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT * FROM ' . $this->table . ' WHERE 1';
		
		if (isset($attr['log_id']) && $attr['log_id'] != '') {
			$q.= ' AND log_id = '.$attr['log_id'];
        }
		
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
		}

        if (isset($attr['order'])) {
            $q.= ' ORDER BY ' . $attr['order'];
        }
        
        // $result['']
		
        // debug($data);
        
        $result['totaldata'] = count(orm_get_list($q));

        if (isset($attr['paging']) && $attr['paging'] != '') 
		{
            $q.= ' AND log_id = '.$attr['log_id'];
            
            if (isset($attr['offset'])) $q.= ' LIMIT ' . $attr['offset'];
            if (isset($attr['limit'])) $q.= ', ' . $attr['limit'];

            
        }

		$data = orm_get_list($q);
        $result['rowdata'] =  $data;
        
        $result = json_encode($result);
        echo $result;
        // debug($result);
		// die;
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
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}