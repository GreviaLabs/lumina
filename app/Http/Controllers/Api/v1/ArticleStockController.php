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

use App\Models\ArticleStockTypeModel;

class ArticleStockController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'ms_article_stock';
    public $primary_key = 'article_stock_id';
    public $list_column = array('site_id','article','customer_article','description','customer_article_description','stock_qty','stock_dashboard','stock_disc','stock_cc','stock_damaged','status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
	
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
		// $log = ArticleModel::where('article_stock_id',3)
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
		
		if (isset($attr['article_stock_id']) && $attr['article_stock_id'] != '') {
			$q.= ' AND article_stock_id = '.$attr['article_stock_id'];
		}
		
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}
		
		if (isset($attr['article']) && $attr['article'] != '') {
			$q.= ' AND article = '.replace_quote($attr['article']);
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
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR customer_article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR customer_article_description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR stock_qty LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }

        if (isset($attr['filter']) && $attr['filter'] != '') 
		{
			// validate_column
			$filter = validate_column($this->list_column, $attr);

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
		}
		
		// if (isset($attr['article_stock_id']) && $attr['article_stock_id'] != '') {
		// 	$q.= ' AND article_stock_id = '.$attr['article_stock_id'];
  //       }
			
		// if (isset($attr['site_id']) && $attr['site_id'] != '') {
		// 	$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		// }
		
		// if (isset($attr['article']) && $attr['article'] != '') {
		// 	$q.= ' AND article = '.$attr['article'];
		// }
	
		if (isset($attr['status']) && in_array($attr['status'],array('-1','0','1'))) {
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

	// update stock_dashboard
	public function update_dashboard_stk()
	{
		$put = $attr = $result = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') parse_str(file_get_contents("php://input"), $_PUT);

        $put = $_PUT;
		$attr = validate_column($this->list_column, $put);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($attr)) $result['message'] = 'no data';
        
		if (! isset($attr['article']) || ! isset($attr['site_id'])) $result['message'] = 'article or site_id must be filled.';
		
		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		$q = 'UPDATE ms_article_stock SET stock_dashboard = stock_dashboard + '.((int)$attr['stock_dashboard']);
		$q.= ' WHERE article = '.replace_quote($attr['article']).' AND site_id = '.replace_quote($attr['site_id']);
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


	// not used yet, maybe will be deleted
	public function update_stock(){
		$put = $attr = $result = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') parse_str(file_get_contents("php://input"), $_PUT);

        $put = $_PUT;
        $attr = array();
        if($put['token']) unset($put['token']);
        if($put['data']){
        	if(is_array($put['data'])){
        		for($i=0; $i<count($put['data']); $i++){
        			$attr[] = validate_column($this->list_column, $put['data'][$i]);
        		}
        	}
        }

        $result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($attr)) $result['message'] = 'no data';

        // Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		$q = "SELECT * FROM ".$this->table." WHERE status = 1";
		$list_art_stock = orm_get_list($q);
		$j=0;
		for($i=0; $i<count($attr); $i++){
			while($list_art_stock[$j]){
				if($attr[$i]['article'] == $list_art_stock[$j]->article && $attr[$i]['site_id'] == $list_art_stock[$j]->site_id){
					$q = "UPDATE ".$this->table." SET stock_qty = ";
					//logic pengurangan;
					$q .= ((int)$list_art_stock[$j]->stock_qty - (int)$attr[$i]['stock_qty']);
					$q .= ", stock_cc = ";
					$q .= ((int)$list_art_stock[$j]->stock_qty - (int)$attr[$i]['stock_qty']);
					$q .= " WHERE article = ".replace_quote($attr[$i]['article']);
					$q .= " AND site_id = ".replace_quote($attr[$i]['site_id']);
					// $update = DB::statement($q);
					debug($q,1);
				}
				$j++;
			}
		}

		if ($update) {
			$result['is_success'] = 1;
			$result['message'] = 'update success';
		} else {
			$result['is_success'] = 0;
			$result['message'] = 'update failed';
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

	// reporting
	public function get_value_of_stocks(){
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;

		$data = array();
		$data = ['kls' => 0, 'non_kls' => 0];

		// get value of stock KLS
		$q = 'SELECT SUM(b.stock_dashboard * a.price) AS total, a.art_source
				FROM ms_article a
				LEFT JOIN ms_article_stock b USING(article_id)
				LEFT JOIN ms_site s ON a.site_id = s.site_id
                WHERE 1
                AND a.status = 1 AND a.art_source = 1';

        if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' a.site_id = '.replace_quote($attr['site_id']);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND a.site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(b.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(b.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

        $kls = orm_get($q);

        // get value of stock NonKLS
		$q = 'SELECT SUM(b.stock_dashboard * a.price) AS total, a.art_source
				FROM ms_article a
				LEFT JOIN ms_article_stock b USING(article_id)
				LEFT JOIN ms_site s ON a.site_id = s.site_id
                WHERE 1
                AND a.status = 1 AND a.art_source = 2';

        if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' a.site_id = '.replace_quote($attr['site_id']);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND a.site_id = '.replace_quote($attr['site_id']);
			}
		}

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(b.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(b.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

        $nonkls = orm_get($q);

        $kls = json_decode(json_encode($kls),1);
        $nonkls = json_decode(json_encode($nonkls),1);

        if(isset($kls['total'])) $data['kls'] = $kls['total'];
        if(isset($nonkls['total'])) $data['non_kls'] = $nonkls['total'];
      
        $result['data'] = $data;

        echo json_encode($result); die;
	}
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}