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

use App\Models\ArticleModel;

class MovementArticleController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'tr_movement_article';
    public $primary_key = 'movement_article_id';
	public $list_column = array('movement_article_id', 'receiving_site_id', 'article', 'description','qty','balance_qty','movement_type', 'is_chamber', 'status', 'reference', 'reference_type', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
	
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
		
		if (isset($attr['movement_article_id']) && $attr['movement_article_id'] != '') {
			$q.= ' AND movement_article_id = '.$attr['movement_article_id'];
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
			
		$q = 'SELECT mv_art.movement_article_id, mv_art.receiving_site_id, mv_art.article, mv_art.description, mv_art.qty, mv_art.movement_type, mv_art.created_at, mv_art.status, mv_art.reference,';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE mv_art.created_by = u.user_id), mv_art.created_by) as creator,';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE mv_art.updated_by = u.user_id), mv_art.updated_by) as editor';
		$q.= ' FROM ' . $this->table . ' mv_art WHERE 1';
		$q.= ' HAVING 1';
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' receiving_site_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR reference LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
		
		if (isset($attr['movement_article_id']) && $attr['movement_article_id'] != '') {
			$q.= ' AND movement_article_id = '.$attr['movement_article_id'];
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

	public function get_list_combine_report(){
		$get = NULL;
		if(isset($_GET)) $get = $_GET;
		$empty = "-";
		$q = "SELECT mvd.movement_article_id,mvd.created_at AS movement_date, mvd.receiving_site_id AS site, mvd.reference AS document, mvd.article, mvd.movement_type, mvd.reference_type,
			CASE
				WHEN (mvd.is_chamber = 0 AND mvd.reference_type = 'od' AND mvd.movement_type = 101) THEN mvd.qty ELSE ".replace_quote($empty)."
			END AS gr_dashboard,
			CASE
				WHEN (mvd.is_chamber = 0 AND (mvd.reference_type = 'tr' OR mvd.reference_type = 'cc' OR mvc.reference_type = 'od') AND (mvd.movement_type = 201 OR mvd.movement_type = 311 OR mvd.movement_type = 551 OR mvd.movement_type = 641)) THEN mvd.qty ELSE ".replace_quote($empty)."
			END AS order_dashboard,
			CASE
				WHEN (mvd.is_chamber = 0 AND mvd.reference_type != 'kitting') THEN mvd.balance_qty ELSE ".replace_quote($empty)."
			END AS balance_qty_dashboard,
			CASE
				WHEN (mvd.is_chamber = 1 AND (mvd.reference_type = 'tr' OR mvc.reference_type = 'od') AND mvd.movement_type = 101 OR mvd.reference_type = 'kitting') THEN mvc.qty ELSE ".replace_quote($empty)."
			END AS gr_chamber,
			CASE
				WHEN (mvd.is_chamber = 1 AND (mvd.reference_type = 'tr' OR mvd.reference_type = 'cc') AND (mvd.movement_type = 201 OR mvd.movement_type = 311)) THEN mvc.qty ELSE ".replace_quote($empty)."
			END AS order_chamber,
			CASE
				WHEN (mvd.is_chamber = 1 OR mvd.reference_type = 'kitting') THEN mvc.balance_qty ELSE ".replace_quote($empty)."
			END AS balance_qty_chamber
			FROM tr_movement_article mvd LEFT JOIN tr_movement_article mvc USING(movement_article_id)";
		if (isset($get['keyword']) && $get['keyword'] != '') {
			$q.= ' WHERE ( ';
			$q.= ' mvd.receiving_site_id LIKE '.replace_quote($get['keyword'],'like');
			$q.= ' OR mvd.article LIKE '.replace_quote($get['keyword'],'like');
			$q.= ' OR mvd.reference LIKE '.replace_quote($get['keyword'],'like');
			$q.= ')';
        }
		$q .= " ORDER BY movement_date ASC";
		$data = orm_get_list($q);
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	public function get_list_basic(){
		$empty = "-";
		$get = $result = NULL;
		
		if(isset($_GET)) $get = $_GET;

		$q = "SELECT mvd.movement_article_id,mvd.created_at AS movement_date, mvd.receiving_site_id AS site, mvd.reference AS document, mvd.article, mvd.movement_type, mvd.reference_type,";
		if($get['is_chamber'] == 0){
			$q .= "CASE
				WHEN (mvd.is_chamber = 0 AND mvd.reference_type = 'od' AND mvd.movement_type = 101) THEN mvd.qty ELSE ".replace_quote($empty)."
			END AS gr_dashboard,
			CASE
				WHEN (mvd.is_chamber = 0 AND (mvd.reference_type = 'tr' OR mvd.reference_type = 'cc' OR mvc.reference_type = 'od') AND (mvd.movement_type = 201 OR mvd.movement_type = 311 OR mvd.movement_type = 551 OR mvd.movement_type = 641)) THEN mvd.qty ELSE ".replace_quote($empty)."
			END AS order_dashboard,
			CASE
				WHEN (mvd.is_chamber = 0 AND mvd.reference_type != 'kitting') THEN mvd.balance_qty ELSE ".replace_quote($empty)."
			END AS balance_qty_dashboard ";
		} else{
			$q .= "CASE
				WHEN (mvd.is_chamber = 1 AND (mvd.reference_type = 'tr' OR mvc.reference_type = 'od') AND mvd.movement_type = 101 OR mvd.reference_type = 'kitting') THEN mvc.qty ELSE ".replace_quote($empty)."
			END AS gr_chamber,
			CASE
				WHEN (mvd.is_chamber = 1 AND (mvd.reference_type = 'tr' OR mvd.reference_type = 'cc') AND (mvd.movement_type = 201 OR mvd.movement_type = 311)) THEN mvc.qty ELSE ".replace_quote($empty)."
			END AS order_chamber,
			CASE
				WHEN (mvd.is_chamber = 1 OR mvd.reference_type = 'kitting') THEN mvc.balance_qty ELSE ".replace_quote($empty)."
			END AS balance_qty_chamber ";
		}
		$q .= "FROM tr_movement_article mvd LEFT JOIN tr_movement_article mvc USING(movement_article_id)";
		$q .= " WHERE 1 ";
		if(isset($get['is_chamber'])){
			if($get['is_chamber'] == 0){
				$q .= " AND mvd.is_chamber = 0";
			}else{
				$q .= " AND mvd.is_chamber = 1";
			}
		} 
		if (isset($get['keyword']) && $get['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' mvd.receiving_site_id LIKE '.replace_quote($get['keyword'],'like');
			$q.= ' OR mvd.article LIKE '.replace_quote($get['keyword'],'like');
			$q.= ' OR mvd.reference LIKE '.replace_quote($get['keyword'],'like');
			$q.= ')';
        }
		$q .= " ORDER BY movement_date ASC";
		
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
		if (! empty($_POST)) $post = $_POST;
		if($post['token']) unset($post['token']);

		if($post['data']){
			for($i=0; $i<count($post['data']); $i++){
				$post['data'][$i]['qty'] = $post['data'][$i]['stock_qty'];
				$post['data'][$i]['receiving_site_id'] = $post['data'][$i]['site_id'];
				$post['data'][$i]['reference'] = $post['data'][$i]['cc_id'];
				unset($post['data'][$i]['stock_qty']);
				unset($post['data'][$i]['site_id']);
				unset($post['data'][$i]['cc_id']);
				$attr[] = validate_column($this->list_column, $post['data'][$i]);
			}
		}

        if (! empty($attr)) {
        	for($i=0; $i<count($attr); $i++){
            	$save = DB::table($this->table)->insert($attr[$i]);
        	}
            
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