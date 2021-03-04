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

class RfidArticleSyncController extends SyncController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'ms_rfid_article';
    public $primary_key = 'rfid_article_id';
    public $list_column = array('rfid_article_id', 'site_id', 'outbound_delivery', 'article','description', 'rfid', 'picktime', 'user_id', 'status', 'status_message','chamber_sync_flag','last_sync', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
    // public $list_required_column = array('email');
	
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

    // @list_rfid => array rfid
	public function get_list_rfid()
	{		
		$attr = $flag = $result = NULL;		
        // debug($attr,1);        
        if (! empty(file_get_contents('php://input'))) $attr = json_decode(file_get_contents('php://input'), 1);        
        if (! isset($attr['site_id'])) {
            echo json_encode(array('message' => 'site_id must be filled'));die;
        }
        if (empty($attr['list_rfid']) || ! is_array($attr['list_rfid'])) {
			echo json_encode(array('message' => 'list_rfid must be filled'));die;
		}
		$flag = 1;
			start:
			$q = "
					SELECT 
						a.site_id site_chamber_gr, a.outbound_delivery, a.article
						, a.description rfid_article_description, a.rfid, b.article_id
						, b.customer_article, b.description article_description, b.customer_article_description
						, b.uom, b.conversion_value, b.safety_stock
						, d.art_column, d.art_rack, d.art_row
						, b.price, c.article_stock_id, b.conversion_value stock_qty 
						, c.stock_cc, c.stock_damaged
					FROM ms_rfid_article a
					LEFT JOIN ms_article b ON 1=1 
						AND b.article = a.article 
						AND b.site_id = ".replace_quote($attr['site_id'])."
					LEFT JOIN ms_article_stock c ON 1=1 
						AND c.article_id = b.article_id
					LEFT JOIN ms_article_place d ON 1=1 
						AND d.article_id = b.article_id
					WHERE 1=1 
						AND LOWER(status_message) = 'open'";
	
			if (! empty($attr['list_rfid']) && is_array($attr['list_rfid'])) {
				$list_rfid = implode('","',$attr['list_rfid']);
				$q.= ' AND rfid IN ("' . $list_rfid . '")';
			}		
			if($flag == 0)
			{
				$q.= '
				AND b.article_id IS NOT NULL AND c.article_stock_id IS NOT NULL ';
			}
			
			$data = orm_get_list($q);			
			$result['is_success'] = 1;
			if (empty($data)) {
				$data = NULL;
				$result['is_success'] = 0;
			}
			else
			{
				
				if($flag == 1)
				{
					$article = $article_stock = '';
					for($i=0; $i<count($data); $i++)
					{
						if($data[$i]->article_id == NULL)
						{
							if($article != '')
								$article.=",";
							$article.= "'".$data[$i]->article.$data[$i]->site_chamber_gr."'";
						}	
						if($data[$i]->article_stock_id == NULL)
						{						
							if($article_stock != '')
								$article_stock.=",";
							$article_stock.= "'".$data[$i]->article.$data[$i]->site_chamber_gr."'";
						}
					}				
					
					if($article != '' || $article_stock != '')
					{
						if($article != '')
						{
							$insert_article = "
							INSERT ms_article 
								(site_id, article, customer_article, description, 
								uom, conversion_value, safety_stock, price, `status`,
								chamber_sync_flag, created_at, created_by, created_ip)
							SELECT 
								".replace_quote($attr['site_id']).", article,customer_article, description,
								uom,conversion_value,safety_stock,price,`status`,
								chamber_sync_flag, created_at, created_by, created_ip 
							FROM ms_article 
							WHERE 1=1 
								AND CONCAT(article,site_id) IN(".$article.")";
								
							DB::statement($insert_article);
						}
						if($article_stock != '')
						{
							$insert_article_stock = "
							INSERT ms_article_stock
								(article_id, site_id, article, customer_article, 
								description, stock_qty, stock_cc, stock_damaged, STATUS,
								chamber_sync_flag, created_at, created_by, created_ip)
							SELECT 
								b.article_id, b.site_id, a.article, a.customer_article, 
								a.description, 0, 0, 0, a.status,
								a.chamber_sync_flag, a.created_at, a.created_by, a.created_ip 
							FROM ms_article_stock a
							JOIN ms_article b ON 1=1 
								AND  b.article = a.article 
								AND b.site_id = ".replace_quote($attr['site_id'])." 
							WHERE 1=1 
								AND	CONCAT(a.article,a.site_id) IN(".$article_stock.")";
								
							DB::statement($insert_article_stock);
						}
	
						$flag = 0;
						goto start;
					}
				}
			}
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

	// Receive JSON
	public function update_bulk()
	{
		$post = $attr = $result = $param_where = $post_site_id = NULL;

		// Sample data : {"site_id":"SZ24","key_id":[{"outbound_delivery":"9460001375","article":"MT0000100","rfid":"0CE2003412012A1700045C933F"},{"outbound_delivery":"9460001375","article":"MT0000100","rfid":"0CE2003412012F1700045C91EC"}]}
		if (! empty(file_get_contents('php://input'))) $post = json_decode(file_get_contents('php://input'), 1);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;

        if (empty($post)) $result['message'][] = 'no data';
        if (empty($post['key_id'])) $result['message'][] = 'key_id must be array';
        if (! isset($post['site_id'])) $result['message'][] = 'site_id must be filled';

		// Check primary key validity
		for($i=0; $i<count($post['key_id']); $i++){
			if (! isset($post['key_id'][$i]['outbound_delivery'])) $result['message'][] = 'outbound_delivery must be filled';
			if (! isset($post['key_id'][$i]['article'])) $result['message'][] = 'article must be filled';
			if (! isset($post['key_id'][$i]['rfid'])) $result['message'][] = 'rfid must be filled';
		}

		// Print error if message exist
		if (!empty(($result['message']))) {
			$result['message'] = implode("; ",$result['message']);
			$result['is_success'] = 0;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/

		$list_param = NULL;

		$list_param.= 'chamber_sync_flag = 50 ,';
		$list_param.= 'last_sync = '.replace_quote(get_datetime());

		$q = "UPDATE " . $this->table;
		$q.= " SET " . $list_param;
		$q.= " WHERE 1 AND site_id = ".replace_quote($post['site_id'])." AND ";
		foreach ($post['key_id'] as $key => $val) {
			// $q.= "(".$val." = ".replace_quote($val);
			// $q.= " AND ";
			$i=0;
			$q.= "( ";
			foreach ($val as $keys => $vals) {
				$q.= $keys . " = " . replace_quote($vals);
				if($i != count($val)-1){
					$q.= " AND ";
				}
				$i++;
			}

			if($key != count($post['key_id'])-1){
				$q.= " ) OR ";
			} else $q.= " )";
		}
		// Example output
		// <pre>UPDATE ms_rfid_article SET chamber_sync_flag = 50 ,field_sync = NULL ,last_sync = '2019-06-17 16:19:04' WHERE 1 AND site_id = 'SZ24' AND ( outbound_delivery = '9460001375' AND article = 'MT0000100' AND rfid = '0CE2003412012A1700045C933F' ) OR ( outbound_delivery = '9460001375' AND article = 'MT0000100' AND rfid = '0CE2003412012F1700045C91EC' )</pre>
		
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