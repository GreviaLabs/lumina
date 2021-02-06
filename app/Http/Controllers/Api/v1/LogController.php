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

class ArticleController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'grv_log';
    public $primary_key = 'log_id';
	public $list_column = array('log_id','source', 'subject', 'content' ,'notes','creator_date');
	
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
		
		if (isset($attr['log_id']) && $attr['log_id'] != '') {
			$q.= ' AND log_id = '.replace_quote($attr['log_id']);
		}

		if (isset($attr['article']) && $attr['article'] != '') {
			$q.= ' AND article = '.replace_quote($attr['article']);
        }

		if (isset($attr['customer_article']) && $attr['customer_article'] != '') {
			$q.= ' AND customer_article = '.replace_quote($attr['customer_article']);
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

	public function get_ajax()
	{
		$attr = NULL;
        if (! empty($_GET)) $attr = $_GET;
        
        if(!isset($attr['customer_article']) && $attr['customer_article'] = '' || !isset($attr['site_id']) && $attr['site_id'] = ''){
        	// debug($attr,1);
        	return false;
        }
			
		$q = 'SELECT * FROM ' . $this->table . ' WHERE 1';
		$q.= ' AND customer_article = '. replace_quote($attr['customer_article']);
		$q.= ' AND site_id = '. replace_quote($attr['site_id']);
        
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
		}
		
		$data = orm_get($q);
		echo json_encode($data);
		die;
	}
	
	public function get_list_with_stock()
	{
		$attr = $result = $filter = NULL;
        if (! empty($_GET)) $attr = $_GET;
            
		$q = '
        SELECT ar.article_stock_id, a.log_id, a.site_id, a.article ,a.customer_article, a.description, a.customer_article_description, a.uom, a.conversion_value, a.safety_stock, a.price, a.price_mf,
        ar.stock_dashboard,ar.stock_qty, ar.stock_cc, ar.stock_disc, ar.stock_damaged, 
        a.status, a.status as art_status, ar.status as art_stock_status , a.art_source,
        IFNULL(created.user_code,"-") as created_code,
        IFNULL(ar.created_at,"") as created_at,
        IFNULL(ar.created_ip,"-") as created_ip,
        IFNULL(updated.user_code,"-") as updated_code,
        IFNULL(ar.updated_at,"-") as updated_at,
        IFNULL(ar.updated_ip,"-") as updated_ip';

        if (isset($attr['export_column'])) {
            $q = '
            SELECT ar.article_stock_id, a.log_id, a.site_id, a.article ,a.customer_article, a.description, a.customer_article_description, a.uom, a.conversion_value, a.safety_stock, 
            ar.stock_dashboard,ar.stock_qty as stock_chamber, ar.stock_cc, ar.stock_disc, ar.stock_damaged, 
            a.status, a.status as art_status, ar.status as art_stock_status, a.art_source,
            IFNULL(created.user_code,"-") as created_code,
            IFNULL(ar.created_at,"") as created_at,
            IFNULL(ar.created_ip,"-") as created_ip,
            IFNULL(updated.user_code,"-") as updated_code,
            IFNULL(ar.updated_at,"-") as updated_at,
            IFNULL(ar.updated_ip,"-") as updated_ip';
        }

        $q.= '
        FROM ms_article a 
        LEFT JOIN ms_user created ON created.user_id = a.created_by
        LEFT JOIN ms_user updated ON updated.user_id = a.updated_by
        LEFT JOIN ms_article_stock ar USING(log_id)
        WHERE 1 
        HAVING 1';
		
		if (isset($attr['filter']) && $attr['filter'] != '') 
		{
			// validate_column
			// $filter = validate_column($this->list_column, $attr);

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
		else 
		{
			if (isset($attr['log_id']) && $attr['log_id'] != '') {
				$q.= ' AND log_id = '.replace_quote($attr['log_id']);
			}
	
			if (isset($attr['article']) && $attr['article'] != '') {
				$q.= ' AND article = '.replace_quote($attr['article']);
			}
	
			if (isset($attr['description']) && $attr['description'] != '') {
				$q.= ' AND description = '.replace_quote($attr['description']);
			}
		}
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR customer_article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR customer_article_description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }

		if (isset($attr['stock_status']) && $attr['stock_status'] != '') {
            
            $stock_status = NULL;
            if ($attr['stock_status'] == 'all') {
                // $stock_status = NULL;
            } else if ($attr['stock_status'] == 'in_stock') {
                $q.= ' AND stock_dashboard >= 1';
            } else if ($attr['stock_status'] == 'out_of_stock') {
                $q.= ' AND stock_dashboard <= 0';
            } else if ($attr['stock_status'] == 'below_safety_stock') {
                $q.= ' AND safety_stock IS NOT NULL AND stock_dashboard <= safety_stock';
            }
        }

		if (isset($attr['list_article']) && $attr['list_article'] != '' ) {
            $list_article = NULL;
            $list_article = explode(',',$attr['list_article']);
            $q.= " AND (";
            if (! empty($list_article)) 
            {
                foreach ($list_article as $key => $art)
                {
                    $q.= " article = " . replace_quote($art);
                    if ($key != count($list_article)-1) $q.= " OR "; 
                }
            }
            $q.= " ) ";
        }

		if (isset($attr['status'])) {
            
            if ($attr['status'] == 'all') {

            } else if ($attr['status'] == 'active') {
                $q.= ' AND art_status = 1 AND art_stock_status = 1';
            } if ($attr['status'] == 'inactive') {
                $q.= ' AND art_status = 0';
            } else if (in_array($attr['status'],array('-1','0','1'))) {
                $q.= ' AND art_status = ' . $attr['status'] .' AND art_stock_status = ' . $attr['status'];
            }

        } else {
			$q.= ' AND art_status != -1 AND art_stock_status != -1';
        }

        // debug($attr);
        // debug($q,1);

		if(isset($attr['data']) && $attr['data'] != '' && count($attr['data'])>0 ){
			for($i=0; $i<count($attr['data']); $i++){
				unset($attr['data'][$i]['po_blanket_number']);
				unset($attr['data'][$i]['po_blanket_qty']);
				unset($attr['data'][$i]['po_created_date']);
			}

			$q.= " AND ";
			foreach ($attr['data'] as $key => $val) {
				$i=0;
				$q.= "( ";
				foreach ($val as $keys => $vals) {
					$q.= $keys . " = " . replace_quote($vals);
					if($i != count($val)-1){
						$q.= " AND ";
					}
					$i++;
				}
				if($key != count($attr['data'])-1){
					$q.= " ) OR ";
				} else $q.= " )";
			}
		}

        $result['total_rows'] = count(orm_get_list($q));
        // debug($attr);
        // debug($q,1);
		
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
    
    public function get_list()
	{
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = '
        SELECT a.log_id, a.site_id, a.article, a.customer_article, a.description, a.customer_article_description, uom, conversion_value, safety_stock, max_stock, price, a.status, a.chamber_sync_flag,
        s.stock_dashboard, a.art_source
        FROM ' . $this->table . ' a
        LEFT JOIN ms_article_stock s ON s.site_id = a.site_id AND s.article = a.article
        WHERE 1
        HAVING 1
        ';
		
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
		else 
		{
			if (isset($attr['log_id']) && $attr['log_id'] != '') {
				$q.= ' AND log_id = '.replace_quote($attr['log_id']);
			}
	
			if (isset($attr['article']) && $attr['article'] != '') {
				$q.= ' AND article = '.replace_quote($attr['article']);
			}
	
			if (isset($attr['description']) && $attr['description'] != '') {
				$q.= ' AND description = '.replace_quote($attr['description']);
			}
		}
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR customer_article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR customer_article_description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id']) && ! empty($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}
        }

        if (isset($attr['list_article']) && $attr['list_article'] != '' ) {
            $list_article = NULL;
            $list_article = explode(',',$attr['list_article']);
            $q.= " AND (";
            if (! empty($list_article)) 
            {
                foreach ($list_article as $key => $art)
                {
                    $q.= " article = " . replace_quote($art);
                    if ($key != count($list_article)-1) $q.= " OR "; 
                }
            }
            $q.= " ) ";
        }
        
        if (isset($attr['stock_status']) && $attr['stock_status'] != '') {
            
            $stock_status = NULL;
            if ($attr['stock_status'] == 'all') {
                // $stock_status = NULL;
            } else if ($attr['stock_status'] == 'in_stock') {
                $q.= ' AND stock_dashboard >= 1';
            } else if ($attr['stock_status'] == 'out_of_stock') {
                $q.= ' AND stock_dashboard <= 0';
            } else if ($attr['stock_status'] == 'below_safety_stock') {
                $q.= ' AND safety_stock IS NOT NULL AND stock_dashboard <= safety_stock';
            }
        }

        if (isset($attr['art_source']) && $attr['art_source'] != '') {
        	if($attr['art_source'] == 'KLS'){
				$q.= ' AND art_source = 1';
        	} elseif($attr['art_source'] == 'NON_KLS'){
        		$q.= ' AND art_source = 2';
        	}
        }

		if (isset($attr['status'])) {
            if ($attr['status'] == 'active') {
                $q.= ' AND a.status = 1';
            } else if ($attr['status'] == 'inactive') {
                $q.= ' AND a.status = 0';
            } else if (in_array($attr['status'],array('-1','0','1'))) {
                $q.= ' AND a.status = '.$attr['status'];
            }
        } else {
			$q.= ' AND a.status != -1';
		}

		if(isset($attr['data']) && $attr['data'] != '' && count($attr['data'])>0 ){
			for($i=0; $i<count($attr['data']); $i++){
				unset($attr['data'][$i]['po_blanket_number']);
				unset($attr['data'][$i]['po_blanket_qty']);
				unset($attr['data'][$i]['po_created_date']);
			}

			$q.= " AND ";
			foreach ($attr['data'] as $key => $val) {
				$i=0;
				$q.= "( ";
				foreach ($val as $keys => $vals) {
					$q.= $keys . " = " . replace_quote($vals);
					if($i != count($val)-1){
						$q.= " AND ";
					}
					$i++;
				}
				if($key != count($attr['data'])-1){
					$q.= " ) OR ";
				} else $q.= " )";
			}
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
        
        // debug($q,1);

		$data = orm_get_list($q);
		if (empty($data)) $data = NULL;
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	public function get_list_export()
	{
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT art.site_id, art.article, art.description, art.customer_article, art.customer_article_description,';
		$q.= ' artp.art_rack as article_rack, artp.art_column as article_column, artp.art_row as article_row ';
		$q.= ' FROM ' . $this->table . ' art ';
		$q.= ' LEFT JOIN ms_article_place artp ON art.log_id = artp.log_id';
		$q.= ' WHERE 1';
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' art.article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR art.customer_article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR art.description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR art.description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR art.customer_article_description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' art.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND art.site_id = '.replace_quote($attr['site_id']);
			}
		}
        $q.= ' AND art.status = 1';

        $result['total_rows'] = count(orm_get_list($q));
		
		$q.= ' ORDER BY site_id ASC, article ASC';

		$data = orm_get_list($q);
		if (empty($data)) $data = NULL;
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	public function get_qty_value(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;
		
		$q = 'SELECT sum(arts.stock_dashboard) as qty, sum(a.price * arts.stock_dashboard) as value FROM '.$this->table;
		$q.= ' a LEFT JOIN ms_article_stock arts ON a.article = arts.article';
		$q.= ' WHERE 1';
		
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND a.site_id = '.replace_quote($attr['site_id']);
        }
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND a.status = '.$attr['status'];
        } else {
			$q.= ' AND a.status != -1';
		}

		$data = orm_get($q);

		if (empty($data)) $data = NULL;
		
        $result['data'] = $data;
        $result['qty'] = $data->qty;
        $result['value'] = $data->value;

        echo json_encode($result); 
		die;
	}


	public function get_list_dropdown()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT log_id, article, site_id FROM ' . $this->table . ' 
		WHERE 1=1 AND status = 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}

		$data = orm_get_list($q);
		if (empty($data)) $data = NULL;
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	public function get_list_dropdown_by_site()
	{
		$attr = $result = $data = NULL;
		if (! empty($_GET)) $attr = $_GET;
		
		if(isset($attr['site_id'])){
			$q = 'SELECT log_id, article, site_id FROM ' . $this->table . ' 
				WHERE 1=1 AND status = 1';
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0; $i<count($attr['site_id']); $i++){
					$q.= 'site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.=' OR ';
					}
				}
				$q.= ')';
			}elseif($attr['site_id'] != ''){
				$q.= ' AND site_id = '.replace_quote($attr['site_id']);
			}

			if(isset($attr['article']) && $attr['article'] != ''){
				$q.= ' AND article LIKE "%'.$attr['article'].'%"';
			}

			$q.= ' ORDER BY site_id ASC';

			if(isset($attr['paging']) && $attr['paging'] != ''){
				$q.= ' LIMIT '.$attr['paging'];
			}
			$data = orm_get_list($q);
		}

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

	public function save_bulk_art_stk(){
		$post = $data2 = $attr = $result = NULL;
		if (! empty($_POST)) $post = $_POST;

		$list_field = $list_value = $list_values = '';

		$data2 = $post;
		
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

		for($i=0; $i<count($post); $i++){
			unset($post[$i]['article']);
			unset($post[$i]['site_id']);
		}
		
		foreach($post as $arrkey => $data) {
			if ($arrkey == 0) {
				$i = 1;
				foreach($data as $key => $val) {
					$query.= $key . ' = VALUES(' . $key . ')';
					if ($i != count($data)) {
						$query.= ', ';
					}
					$i++;
				}
			}
			break;
		}
		$update = DB::statement($query);

		// comment dulu, nanti digunain sepertinya 18 Juli 2019 - Harvei

		// for($i=0; $i<count($data2); $i++){
		// 	unset($data2[$i]['uom']);
		// 	unset($data2[$i]['price']);
		// 	$data2[$i]['stock_qty'] = 0;
		// 	$data2[$i]['stock_cc'] = 0;
		// 	$data2[$i]['stock_damaged'] = 0;
		// }

		// $q = 'INSERT IGNORE INTO ms_article_stock';
		
		// $q .= ' (';
		// foreach($data2 as $arrkey => $data) {
		// 	if ($arrkey == 0) {
		// 		$i = 1;
		// 		foreach($data as $key => $val) {
		// 			$q.= $key;
		// 			if ($i != count($data)) 
		// 			{
		// 				$q.= ' ,';
		// 			}
		// 			$i++;
		// 		}
		// 		break;
		// 	}
		// }

		// $q .= ') VALUES';
		// foreach($data2 as $arrkey => $data) {
		// 	if ($arrkey == 0) {
		// 		$i = 1;
		// 		foreach($data as $key => $val) {
		// 			$list_field.= $key;
		// 			if ($i != count($data)) 
		// 			{
		// 				$list_field.= ' ,';
		// 			}
		// 			$i++;
		// 		}
		// 	}
			
		// 	$x = 1;
		// 	$list_values.= '(';
		// 	foreach($data as $keyd => $valx) {
		// 		$list_values.= replace_quote($valx);
		// 		if ($x != count($data)) $list_values.= ',';
				
		// 		$x++;
		// 	}
		// 	$list_values.= ')';
			
		// 	// remove comma
		// 	if ($arrkey != count($data2)-1) {
		// 		$list_values.= ' ,';
		// 	}
			
		// }
		// $list_values.=';';
		// $q.= $list_values;
		// $update_stock = DB::statement($q);

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