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

use App\Models\ArticleLogisticSiteModel;

class ArticleLogisticSiteController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'tr_article_logistic_site';
    public $primary_key = ['site_id','outbound_delivery'];
    public $list_column = array('site_id', 'outbound_delivery', 'status', 'status_in_out','status_message', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
	
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

	public function generate_id($str = 0, $prefix = "ODM",$digitno = 4)
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
	
	public function get_new_id($prefix = "ODM")
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
		// $log = ArticleModel::where('company_id',3)
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
		
		if (isset($attr['article_logistic_site_id']) && $attr['article_logistic_site_id'] != '') 
		{
			$q.= ' AND article_logistic_site_id = '.$attr['article_logistic_site_id'];
		}

		if (isset($attr['site_id']) && $attr['site_id'] != '') 
		{
			$q.= ' AND site_id = ' . replace_quote($attr['site_id']);
		}

		if (isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != '') 
		{
			$q.= ' AND outbound_delivery = ' . replace_quote($attr['outbound_delivery']);
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
			
		$q = "
        SELECT ar.article_logistic_site_id article_logistic_site_id, ar.site_id site_id, ar.outbound_delivery outbound_delivery, 
        ar.status status, ar.status_in_out status_in_out, ar.status_message status_message,
        IFNULL(created.user_code,'-') as created_code,
        IFNULL(ar.created_at,'') as created_at,
        IFNULL(ar.created_ip,'-') as created_ip,
        IFNULL(updated.user_code,'-') as updated_code,
        IFNULL(ar.updated_at,'-') as updated_at,
        IFNULL(ar.updated_ip,'-') as updated_ip
        FROM " . $this->table . " ar 
        LEFT JOIN ms_user created ON created.user_id = ar.created_by
        LEFT JOIN ms_user updated ON updated.user_id = ar.updated_by
        LEFT JOIN tr_article_logistic_site_detail alsd ON ar.outbound_delivery = alsd.outbound_delivery
        WHERE 1
        ";
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' ar.outbound_delivery LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR ar.site_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
        
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND ar.site_id = '.replace_quote($attr['site_id']);
        }
		
		if (isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != '') {
			$q.= ' AND ar.outbound_delivery = '.replace_quote($attr['outbound_delivery']);
        }

		if (isset($attr['article_logistic_site_id']) && $attr['article_logistic_site_id'] != '') {
			$q.= ' AND article_logistic_site_id = '.$attr['article_logistic_site_id'];
        }

        if (isset($attr['status_in_out']) && $attr['status_in_out'] != ''){
            $q.= ' AND ar.status_in_out = '.replace_quote($attr['status_in_out']);
        }

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(ar.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(ar.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

		if (isset($attr['status_message']) && $attr['status_message'] != '') {
			if($attr['status_message'] == 'new'){
				$q.= ' AND ar.status_message = "new"';
			} elseif($attr['status_message'] == 'completed'){
				$q.= ' AND ar.status_message = "completed"
						AND alsd.status_message != "new"';
			} elseif($attr['status_message'] == 'partially_completed'){
				$q.= ' AND ar.status_message = "completed" 
					AND alsd.status_message = "new"';
			}
        }
		
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND ar.status = '.$attr['status'];
        } else {
			$q.= ' AND ar.status != -1';
		}

		$q.= ' GROUP BY outbound_delivery';

		$q.= ' HAVING 1';
        $result['total_rows'] = count(orm_get_list($q));
		
		// Template general 
		if (isset($attr['order'])) { 
			$q.= ' ORDER BY ' . $attr['order'];
			if (isset($attr['orderby'])) $q .= ' '.$attr['orderby']; 
		} else  {
			$q.= ' ORDER BY ';
			if (is_array($this->primary_key)) 
			{
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
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	// Return all list detail data
	public function get_list_detail()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET; 

		if (! isset($attr['site_id']) || ! isset($attr['outbound_delivery'])) 
		{
			$message['message'] = "site_id or outbound_delivery must be filled";
			echo json_encode($message);die;
		}
			
		// $q = 'SELECT * FROM ' . $this->table . ' WHERE 1';
		$q = '
		SELECT alsd.*, a.conversion_value as conversion_value_article, GROUP_CONCAT(rtm.reason_type_mapping_id SEPARATOR ", ") as reason_type_mapping_id
		FROM tr_article_logistic_site als
        LEFT JOIN tr_article_logistic_site_detail alsd USING(outbound_delivery)
        LEFT JOIN ms_article a ON a.article = alsd.article AND a.site_id = als.site_id
        LEFT JOIN ms_reason_type rt ON alsd.article = rt.article AND als.site_id = rt.site_id
        LEFT JOIN ms_reason_type_mapping rtm ON rtm.reason_type_id = rt.reason_type_id
		WHERE 1';
		
		
		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND als.site_id = ' . replace_quote($attr['site_id']);
		}

		if (isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != '') {
			$q.= ' AND als.outbound_delivery = ' . replace_quote($attr['outbound_delivery']);
		}
		// if (isset($attr['keyword']) && $attr['keyword'] != '') {
		// 	$q.= ' AND ( ';
		// 	$q.= ' company_name LIKE '.replace_quote($attr['keyword'],'like');
		// 	$q.= ' OR company_address LIKE '.replace_quote($attr['keyword'],'like');
		// 	$q.= ' OR company_phone LIKE '.replace_quote($attr['keyword'],'like');
		// 	$q.= ' OR company_pic LIKE '.replace_quote($attr['keyword'],'like');
		// 	$q.= ')';
        // }
		
		// if (isset($attr['article_logistic_site_id']) && $attr['article_logistic_site_id'] != '') {
		// 	$q.= ' AND article_logistic_site_id = '.$attr['article_logistic_site_id'];
        // }
		
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND als.status = '.$attr['status'];
        } else {
			$q.= ' AND als.status != -1';
		}

		$q.= ' GROUP BY alsd.article';
        
        $result['total_rows'] = count(orm_get_list($q));
		
		// Template general 
		// if (isset($attr['order'])) { 
		// 	$q.= ' ORDER BY ' . $attr['order'];
		// 	if (isset($attr['orderby'])) $q .= ' '.$attr['orderby']; 
		// } else  {
		// 	$q.= ' ORDER BY ';
		// 	if (is_array($this->primary_key)) {
		// 		foreach ($this->primary_key as $kpk => $pk) {
		// 			$q.= $pk .' DESC';
		// 			if ($kpk != count($this->primary_key)-1) $q.= ', ';
		// 		}
		// 	} 
		// 	else 
		// 		$q.= $this->primary_key .' DESC';
		// }
		
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
		// debug($data,1);

		$data = orm_get_list($q);
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	public function get_list_export(){
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT alsd.outbound_delivery, als.site_id, alsd.article, alsd.qty_od_sap, alsd.qty_receive_actual, alsd.conversion_value, artp.art_rack, artp.art_column, artp.art_row FROM tr_article_logistic_site_detail alsd
				LEFT JOIN tr_article_logistic_site als ON als.outbound_delivery = alsd.outbound_delivery
				LEFT JOIN ms_article art ON art.article = alsd.article AND art.site_id = als.site_id
				LEFT JOIN ms_article_place artp ON art.article_id = artp.article_id';
		$q.= ' WHERE 1';

		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' alsd.outbound_delivery LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR als.site_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
        
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
        }
		
		if (isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != '') {
			$q.= ' AND alsd.outbound_delivery = '.replace_quote($attr['outbound_delivery']);
        }

        if (isset($attr['status_message']) && $attr['status_message'] != '') {
			if($attr['status_message'] == 'new'){
				$q.= ' AND als.status_message = "new"';
			} elseif($attr['status_message'] == 'completed'){
				$q.= ' AND als.status_message = "completed"
						AND alsd.status_message != "new"';
			}
        }

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(alsd.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(alsd.created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        } elseif(!isset($attr['start_date']) && $attr['start_date'] == ''){
			if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(alsd.created_at) BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(alsd.created_at) BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()';
        	}
        }

        $q.= ' ORDER BY outbound_delivery, article';

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
		// debug($put,1);
		
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

	public function update_bulk()
	{
		$put = $attr = $result = $param_where = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') parse_str(file_get_contents("php://input"), $_PUT);

		$put = $_PUT;
		
		// $attr = validate_column($this->list_column, $put);
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($put)) $result['message'] = 'no data';
		
		// Check primary key validity
		if (is_array($this->primary_key)) {
			for($i=0; $i<count($put); $i++){
				foreach ($this->primary_key as $kpk => $pk) {
					if (! isset($put[$i][$pk])) $result['message'] = $pk . ' must be filled.';
					else $param_where[$i][$pk] = $put[$i][$pk];
				}
			}
			
		} else {
			for($i=0; $i<count($put); $i++){
				if (! isset($put[$i][$this->primary_key])) $result['message'] = $this->primary_key . ' must be filled.';
			}
		}

		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($put)) $result['paramdata'] = $put;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		// remove data from pk where

		if (is_array($this->primary_key)) {
			for($i=0; $i<count($put); $i++){
				foreach ($this->primary_key as $kpk => $pk) {
					unset($put[$i][$pk]);
				}
			}
			
		} else {
			for($i=0; $i<count($put); $i++){
				unset($put[$i][$this->primary_key]);
			}
		}
		for($i=0; $i<count($put); $i++){
			$update = DB::table($this->table)
				->where($param_where[$i])
				->update($put[$i]);
		}
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
	
	public function cron_update_header_status()
	{
		$listdata = $q = $table = NULL;

		$table = 'tr_article_logistic_site';
		$q = "
		SELECT sd.outbound_delivery, GROUP_CONCAT(sd.status_message) as latest_status
		FROM tr_article_logistic_site_detail sd 
		LEFT JOIN tr_article_logistic_site sh USING(outbound_delivery)
		WHERE 1 AND sh.status_message = 'new'
		GROUP BY sd.outbound_delivery
		HAVING latest_status LIKE '%new%' AND latest_status like '%received%'
		";
		// $listdata = DB::statement($q);
		$listdata = orm_get_list($q);

		if (! empty($listdata)) {
			foreach ($listdata as $data) {
				// $update = DB::statement($q);
				$update = $attr = $param_where = NULL;
				$attr['status_message'] = 'partially_completed';
				$attr['updated_at'] = get_datetime();
				$attr['updated_by'] = 'cron';
				$attr['updated_ip'] = 'cron';
				$param_where['outbound_delivery'] = $data->outbound_delivery;
				$update = DB::table($this->table)
						  ->where($param_where)
						  ->update($attr);
			}
		}

		$listdata = NULL;
		$q = "
		SELECT sd.outbound_delivery, GROUP_CONCAT(sd.status_message) as latest_status
		FROM tr_article_logistic_site_detail sd 
		LEFT JOIN tr_article_logistic_site sh USING(outbound_delivery)
		WHERE 1 AND sh.status_message IN ('new','partially_completed')
		GROUP BY sd.outbound_delivery
		HAVING (latest_status LIKE '%received%' OR latest_status LIKE '%received_with_disc%') AND 
		latest_status NOT LIKE '%new%'
		";
		// $listdata = DB::statement($q);
		$listdata = orm_get_list($q);

		if (! empty($listdata)) {
			foreach ($listdata as $data) {
				// $update = DB::statement($q);
				$update = $attr = $param_where = NULL;
				$attr['status_message'] = 'completed';
				$attr['updated_at'] = get_datetime();
				$attr['updated_by'] = 'cron';
				$attr['updated_ip'] = 'cron';
				$param_where['outbound_delivery'] = $data->outbound_delivery;
				$update = DB::table($this->table)
						  ->where($param_where)
						  ->update($attr);
				$row_success++;
			}
		}

	}

	// get list join detail and header for good_issue
	public function get_list_join($outbound_delivery, $site){
		$q = 'SELECT alsd.outbound_delivery as outbound_delivery, als.site_id as site_id, alsd.article as article, 
				alsd.description as description, alsd.qty_od_sap as qty, alsd.sloc 
				FROM tr_article_logistic_site_detail alsd
				LEFT JOIN tr_article_logistic_site als ON alsd.outbound_delivery = als.outbound_delivery
				WHERE als.site_id = '.replace_quote($site).' 
				AND alsd.outbound_delivery = '.replace_quote($outbound_delivery).' 
				AND als.status_message = "new"';
		$data = orm_get_list($q);
		$result['data'] = $data;
		$result = json_encode($result);
		return $result;
	}

	public function update_stock_issue($article, $site_id, $qty, $sloc, $user, $ip){
		$result = NULL;
		$sloc = json_decode($sloc,1);
		$q = 'UPDATE ms_article_stock SET ';
		// mapping sloc
		if(!empty($sloc['1000'])){
			$q.= $sloc['1000'].' = '.$sloc['1000'].' - '.(int)$qty;
		} elseif(!empty($sloc['1001'])){
			$q.= $sloc['1001'].' = '.$sloc['1001'].' - '.(int)$qty;
		} elseif(!empty($sloc['1009'])){
			$q.= $sloc['1009'].' = '.$sloc['1009'].' - '.(int)$qty;
		}

		$q.= ', updated_at = '.replace_quote(get_datetime()).', 
		 		updated_by = '.replace_quote($user).', 
		 		updated_ip = '.replace_quote($ip).' WHERE 1 
		 		AND	article = '.replace_quote($article).' 
		 		AND site_id = '.replace_quote($site_id);
		// $q = 'UPDATE ms_article_stock SET stock_dashboard = stock_dashboard - '.$qty.', 
		// 		updated_at = '.replace_quote(get_datetime()).', 
		// 		updated_by = '.replace_quote($user).', 
		// 		updated_ip = '.replace_quote($ip).' WHERE 1 
		// 		AND	article = '.replace_quote($article).' 
		// 		AND site_id = '.replace_quote($site_id);
		$update = DB::statement($q);

		if(isset($update)){
			$result['is_success'] = 1;
			$result['message'] = 'Update Success!';
		} else{
			$result['is_success'] = 0;
			$result['message'] = 'Update Failed!';
		}
		return $result;
	}

	public function update_status_message_detail($outbound_delivery, $article, $user, $ip){
		$result = NULL;
		
		$q = 'UPDATE tr_article_logistic_site_detail SET status_message = "received", 
				updated_at = '.replace_quote(get_datetime()).', 
				updated_by = '.replace_quote($user).', 
				updated_ip = '.replace_quote($ip).' WHERE 1 
				AND outbound_delivery = '.replace_quote($outbound_delivery).' 
				AND article = '.replace_quote($article);
		$update = DB::statement($q);

		if(isset($update)){
			$result['is_success'] = 1;
			$result['message'] = 'Update Success!';
		} else{
			$result['is_success'] = 0;
			$result['message'] = 'Update Failed!';
		}
		return $result;
	}

	public function update_status_message_header($outbound_delivery, $site_id, $user, $ip){
		$result = NULL;
		
		$q = 'UPDATE tr_article_logistic_site SET status_message = "completed", 
				updated_at = '.replace_quote(get_datetime()).', 
				updated_by = '.replace_quote($user).', 
				updated_ip = '.replace_quote($ip).' WHERE 1 
				AND outbound_delivery = '.replace_quote($outbound_delivery).' 
				AND site_id = '.replace_quote($site_id);
		$update = DB::statement($q);

		if(isset($update)){
			$result['is_success'] = 1;
			$result['message'] = 'Update Success!';
		} else{
			$result['is_success'] = 0;
			$result['message'] = 'Update Failed!';
		}
		return $result;
	}

	public function insert_movement_article($outbound_delivery, $article, $description, $site_id, $qty, $user, $ip){
		$result = NULL;
		$movement_type = '641';

		// select article_stock
		$q = 'SELECT article, site_id, stock_dashboard FROM ms_article_stock WHERE article = '.replace_quote($article).' 
				AND site_id = '.replace_quote($site_id);
		$stock = orm_get($q);

		$q = 'INSERT INTO tr_movement_article(receiving_site_id,article,description,qty,movement_type,status,
                		reference,balance_qty,created_at,created_by,created_ip,is_chamber,reference_type)
                		VALUES('.replace_quote($site_id).', '.replace_quote($article).', '.replace_quote($description).', '.$qty.', 
                		'.replace_quote($movement_type).', 1, '.replace_quote($outbound_delivery).', '.$stock->stock_dashboard.', '
                		.replace_quote(get_datetime()).', '.replace_quote($user).', '.replace_quote($ip).', 0, "od")';
		$insert = DB::statement($q);

		if(isset($insert)){
			$result['is_success'] = 1;
			$result['message'] = 'Update Success!';
		} else{
			$result['is_success'] = 0;
			$result['message'] = 'Update Failed!';
		}
		return $result;
	}

	public function good_issue_stock(){
		$post = $result = NULL;
		if(isset($_POST)) $post = $_POST;

		// validation here
		if(! isset($post['outbound_delivery']) || ! isset($post['site_id'])){
			$message = 'There is no outbound_delivery OR site_id!';
			$result['message'] = $message;
			$result['is_success'] = 0;
			return $result;
		}
		// end validation

		// Operation Start

		// get list detail
		$list_od = $this->get_list_join($post['outbound_delivery'], $post['site_id']);
		$list_od = json_decode($list_od);
		$list_od = $list_od->data;

		// update stock, update status_message, insert movement_article
		for($i=0; $i<count($list_od); $i++){
			// update stock
			$update_stock = $this->update_stock_issue($list_od[$i]->article, $list_od[$i]->site_id, $list_od[$i]->qty, $list_od[$i]->sloc, $post['created_by'], $post['created_ip']);
			// if update_stock success
			if(isset($update_stock) && $update_stock['is_success'] == 1){
				// update od detail status_message
				$update_status_message_detail = $this->update_status_message_detail($list_od[$i]->outbound_delivery, $list_od[$i]->article, $post['created_by'], $post['created_ip']);

				// insert movement_article
				$insert_movement_article = $this->insert_movement_article($list_od[$i]->outbound_delivery, $list_od[$i]->article,$list_od[$i]->description, $list_od[$i]->site_id, $list_od[$i]->qty, $post['created_by'], $post['created_ip']);
			}
		}

		// update od header status_message
		$update_status_message_header = $this->update_status_message_header($post['outbound_delivery'], $post['site_id'], $post['created_by'], $post['created_ip']);

		// End Operation

		if(isset($update_stock) && $update_stock['is_success'] == 1){
			$result['is_success'] == 1;
			$result['message'] = "Good Issue Done Successfuly!";
		} else{
			$result['is_success'] == 0;
			$result['message'] = "Good Issue Failed!";
		}
		$result = json_encode($result);

		return $result;
	}

	public function __destruct()
	{
		// parent::__construct();
	}
	
}