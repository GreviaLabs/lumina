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

use App\Models\ArticleLogisticSiteDetailModel;

class ArticleLogisticSiteDetailController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'tr_article_logistic_site_detail';
    public $primary_key = ['outbound_delivery','article'];
    public $list_column = array('outbound_delivery', 'article', 'customer_article','description','transaction_id','qty_od_sap','conversion_value','qty_receive_actual','qty_receive','disc_minus','disc_plus','conversion_diff','balance_qty_for_po','dashboard_received_date','qty_chamber','chamber_disc_minus','chamber_disc_plus','chamber_sync_flag', 'sloc', 'actual_receive_date','field_sync','status', 'status_message','created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip','po_blanket_number');
	
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
		
		if (isset($attr['article_logistic_site_detail_id']) && $attr['article_logistic_site_detail_id'] != '') 
		{
			$q.= ' AND article_logistic_site_detail_id = '.$attr['article_logistic_site_detail_id'];
		}
		
		if (isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != '') 
		{
			$q.= ' AND outbound_delivery = '.$attr['outbound_delivery'];
		}
		
		if (isset($attr['article']) && $attr['article'] != '') 
		{
			$q.= ' AND article = '.$attr['article'];
		}
		
		if (isset($attr['customer_article']) && $attr['customer_article'] != '') 
		{
			$q.= ' AND customer_article = '.$attr['customer_article'];
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
		
		// if (isset($attr['keyword']) && $attr['keyword'] != '') {
		// 	$q.= ' AND ( ';
		// 	$q.= ' company_name LIKE '.replace_quote($attr['keyword'],'like');
		// 	$q.= ' OR company_address LIKE '.replace_quote($attr['keyword'],'like');
		// 	$q.= ' OR company_phone LIKE '.replace_quote($attr['keyword'],'like');
		// 	$q.= ' OR company_pic LIKE '.replace_quote($attr['keyword'],'like');
		// 	$q.= ')';
        // }
		
		if (isset($attr['article_logistic_site_detail_id']) && $attr['article_logistic_site_detail_id'] != '') 
		{
			$q.= ' AND article_logistic_site_detail_id = '.$attr['article_logistic_site_detail_id'];
		}
		
		if (isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != '') 
		{
			$q.= ' AND outbound_delivery = '.$attr['outbound_delivery'];
		}
		
		if (isset($attr['article']) && $attr['article'] != '') 
		{
			$q.= ' AND article = '.$attr['article'];
		}
		
		if (isset($attr['customer_article']) && $attr['customer_article'] != '') 
		{
			$q.= ' AND customer_article = '.$attr['customer_article'];
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

	// For dashboard get kitting process: article_logistic_site & prepack_bundling
	public function get_list_kitting()
	{
		$get = $result = NULL;
		if (! empty($_GET)) $get = $_GET;
		$paramtemp = NULL;
		$paramtemp['keyword'] = '';
		$paramtemp['order'] = '';
		$paramtemp['filter'] ='';

		if(isset($get['keyword']) && $get['keyword'] != ''){
        	$paramtemp['keyword'].= " 
			AND (b.outbound_delivery like '%".$get['keyword']."%' 
			OR b.article like '%".$get['keyword']."%') 
			";
		}
		
		if (isset($get['order'])) { 
			$paramtemp['order'].= ' ORDER BY ' . $get['order'];
			if (isset($get['orderby'])) $paramtemp['order'] .= ' '.$get['orderby']; 
		} else  {
			$paramtemp['order'].= ' ORDER BY ';
			if (is_array($this->primary_key)) {
				foreach ($this->primary_key as $kpk => $pk) {
					$paramtemp['order'].= $pk .' DESC';
					if ($kpk != count($this->primary_key)-1) $paramtemp['order'].= ', ';
				}
			} 
			else 
				$paramtemp['order'].= $this->primary_key .' DESC';
		}

		if(isset($get['filter'])){
			$paramtemp['filter'].= ' AND (b.';

			$i = 0;
				foreach ($get['filter'] as $akey => $aval) {
					if (isset($aval) && $aval != '') {
						if ($i > 0) $paramtemp['filter'] .= ' AND b.';
						$paramtemp['filter'].= ' '.$akey. ' LIKE ' . replace_quote($aval,'like');
						$i++;
					}
				}
				$paramtemp['filter'].= ' ) ';
		}

		$q = "
			(SELECT 
				b.outbound_delivery
				, b.article
				, b.description
				, b.conversion_value
				, b.conversion_diff
				, a.site_id
				, b.article_logistic_site_detail_id
				, b.status_message
				, b.created_at
				, COALESCE((SELECT u.user_code FROM ms_user u WHERE a.created_by = u.user_id), a.created_by) as creator
			FROM tr_article_logistic_site a
			LEFT JOIN tr_article_logistic_site_detail b ON 1=1 			
				AND b.outbound_delivery = a.outbound_delivery 
			WHERE 1 = 1 				
				AND b.status = 1
				AND b.conversion_diff > 0
				AND b.status_message != 'new'
				AND a.site_id = ".replace_quote($get['site_id'])."
				" . $paramtemp['keyword'] . " ".$paramtemp['filter']."
			GROUP BY 
				b.outbound_delivery
				, b.article
				, b.description
				, b.conversion_value 
				, a.site_id
			HAVING 1
			". $paramtemp['order'] . ")
				
			UNION

			(SELECT 
				b.outbound_delivery
				, b.article
				, c.description
				, a.conversion_value
				, a.conversion_diff
				, a.site_id
				, '' article_logistic_site_detail_id
				, a.status_message
				, b.created_at
				, COALESCE((SELECT u.user_code FROM ms_user u WHERE a.created_by = u.user_id), a.created_by) as creator
			FROM tr_prepack_bundling_header a
			LEFT JOIN tr_prepack_bundling_detail b ON 1=1 
				AND b.outbound_delivery = a.outbound_delivery
			LEFT JOIN ms_article c ON 1=1
				AND b.article = c.article
				AND a.site_id = c.site_id
			WHERE 1 = 1 				
				AND b.status = 1
				AND a.site_id = ".replace_quote($get['site_id'])."
				AND a.conversion_diff > 0
				" . $paramtemp['keyword'] . " ".$paramtemp['filter']."
			HAVING 1
			". $paramtemp['order'] .")";
        $result['total_rows'] = count(orm_get_list($q));
		// Template general 	
		
		// set default paging
		// if (! isset($attr['paging'])) {
		// 	if (! isset($attr['offset'])) $attr['offset'] = OFFSET;
		// 	if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
		// }
		
		// if (isset($attr['offset'])) { 
		// 	$q.= ' LIMIT ' . $attr['offset'];
			
		// 	if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
			
		// 	$q.= ', ' . $attr['perpage'];
		// }

		$data = orm_get_list($q);
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	// For mapping program .net
	public function get_list_replenish()
	{		
		$attr = $result = $message = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		if (! isset($attr['outbound_delivery'])) {
			$message['message'] = 'outbound_delivery must be filled';
			echo json_encode($message);
			die;
		}

		$q = "
			SELECT 
				b.outbound_delivery
				, b.article
				, b.description
				, b.qty_receive - COUNT(c.article) conversion_value
				, a.site_id 			
			FROM tr_article_logistic_site a
			LEFT JOIN tr_article_logistic_site_detail b ON 1=1 			
				AND b.outbound_delivery = a.outbound_delivery 
			LEFT JOIN ms_rfid_article c ON 1=1 
				AND c.outbound_delivery = b.outbound_delivery 
				AND c.article = b.article
				AND c.site_id = a.site_id
			WHERE 1 = 1 				
				AND a.outbound_delivery = ".replace_quote($attr['outbound_delivery'])."
				AND b.status = 1
				AND b.status_message != 'new'
			GROUP BY 
				b.outbound_delivery
				, b.article
				, b.description
				, b.qty_receive 
				, a.site_id 
			HAVING  
				b.qty_receive - COUNT(c.article) > 0

			UNION

			SELECT 
				b.outbound_delivery
				, b.article
				, d.description
				, (a.combine_qty/a.conversion_value) - COUNT(c.article) conversion_value
				, a.site_id 
			FROM tr_prepack_bundling_header a
			LEFT JOIN tr_prepack_bundling_detail b ON 1=1 
				AND b.outbound_delivery = a.outbound_delivery 
			LEFT JOIN ms_rfid_article c ON 1=1 
				AND c.outbound_delivery = b.outbound_delivery 
				AND c.article = b.article
				AND c.site_id = a.site_id
			LEFT JOIN ms_article d ON 1=1
				AND d.article = b.article
				AND d.site_id = a.site_id
			WHERE 1 = 1 				
				AND a.outbound_delivery = ".replace_quote($attr['outbound_delivery'])."
				AND b.status = 1
				AND a.status_message = 'received'
			GROUP BY 
				b.outbound_delivery
				, b.article
				, a.conversion_value 
				, a.combine_qty
				, a.site_id
				, d.description 
			HAVING  
				(a.combine_qty/a.conversion_value) - COUNT(c.article) > 0
				
			";
			
		$data = orm_get_list($q);
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	public function update_status_header()
	{
		// -- all header new, update to partial complete
		// SELECT sd.outbound_delivery, GROUP_CONCAT(sd.status_message) as latest
		// FROM tr_article_logistic_site_detail sd 
		// LEFT JOIN tr_article_logistic_site sh USING(outbound_delivery)
		// WHERE 1 AND sh.status_message = 'new'
		// GROUP BY sd.outbound_delivery
		// HAVING latest LIKE '%new%' AND latest like '%received%'

		// --- all header new or partial, update to complete
		// SELECT sd.outbound_delivery
		// FROM tr_article_logistic_site_detail sd 
		// LEFT JOIN tr_article_logistic_site sh USING(outbound_delivery)
		// WHERE 1 AND sh.status_message IN ('new','partially_completed')
		// GROUP BY sd.outbound_delivery
		// HAVING GROUP_CONCAT(sd.status_message) LIKE '%received%' AND 
		// GROUP_CONCAT(sd.status_message) NOT LIKE '%new%'

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
		// validate_column
		// $attr = validate_column($this->list_column, $post);
        
        if (! empty($post)) {
            for($i=0; $i<count($post); $i++){
				$post[$i] = validate_column($this->list_column, $post[$i]);
				$save = DB::table($this->table)->insert($post[$i]);
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

	public function save_bulk_detail()
	{
        $post = $attr = $result = NULL;
        $list_field = $list_value = '';
		if (! empty($_POST)) $post = $_POST;
		// validate_column
		// $attr = validate_column($this->list_column, $post);
        
        if (! empty($post)) {
        	$q = 'INSERT INTO '. $this->table;
        	$q.= ' (';
        	foreach ($post as $arrKey => $data) {
        		if ($arrKey == 0) {
					$i = 1;
					foreach($data as $key => $val) {
						$q.= $key;
						if ($i != count($data)) 
						{
							$q.= ', ';
						}
						$i++;
					}
					break;
				}
        	}

        	$q.= ") VALUES";

        	foreach($post as $arrKey => $data) {
				if ($arrKey == 0) {
					$i = 1;
					foreach($data as $key => $val) {
						$list_field.= $key;
						if ($i != count($data)) 
						{
							$list_field.= ', ';
						}
						$i++;
					}
				}
				
				$x = 1;
				$list_value.= '(';
				foreach($data as $keyd => $valx) {
					$list_value.= replace_quote($valx);
					if ($x != count($data)) $list_value.= ', ';
					
					$x++;
				}
				$list_value.= ')';
				
				// remove comma
				if ($arrKey != count($post)-1) {
					$list_value.= ', ';
				}
				
			}
			$q.= $list_value;
			$save = DB::statement($q);
            if ($save) {
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

	/*
	* Report Api For GR
	*/

	// discrepancy article gr
	public function get_discrepancy_gr(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$res = ['total_article_gr_kls' => 0, 
				'total_article_gr_nonkls' => 0,
				'total_article_gr_no_disc_kls' => 0,
				'total_article_gr_no_disc_nonkls' => 0,
				'total_article_gr_disc_plus_kls' => 0,
				'total_article_gr_disc_plus_nonkls' => 0,
				'total_article_gr_disc_minus_kls' => 0,
				'total_article_gr_disc_minus_nonkls' => 0,
				'total_article_gr_conv_diff_kls' => 0,
				'total_article_gr_conv_diff_nonkls' => 0,
				'total_kitting_article_kls' => 0,
				'total_kitting_article_nonkls' => 0];

		// All article GR KLS and Non KLS
		$q = 'SELECT alsd.art_source, COUNT(alsd.article) AS total_article FROM tr_article_logistic_site_detail alsd
				LEFT JOIN tr_article_logistic_site als ON als.outbound_delivery = alsd.outbound_delivery
				WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
		}
		$q.= ' AND als.status_in_out = "in"
				GROUP BY alsd.art_source';

		$gr_all_article = orm_get_list($q);
		$gr_all_article = json_decode(json_encode($gr_all_article),1);

		for($i=0; $i<count($gr_all_article); $i++){
			if($gr_all_article[$i]['art_source'] == 1){
				$res['total_article_gr_kls'] = $gr_all_article[$i]['total_article'];
			} else if($gr_all_article[$i]['art_source'] == 2){
				$res['total_article_gr_nonkls'] = $gr_all_article[$i]['total_article'];
			}
		}

		// All article GR with no disc
		$q = 'SELECT alsd.art_source, COUNT(alsd.article) AS total_article FROM tr_article_logistic_site_detail alsd
				LEFT JOIN tr_article_logistic_site als ON als.outbound_delivery = alsd.outbound_delivery
				WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
		}
		$q.= ' AND als.status_in_out = "in" AND alsd.disc_minus = 0 AND alsd.disc_plus = 0
				GROUP BY alsd.art_source';

		$gr_all_article_no_disc = orm_get_list($q);
		$gr_all_article_no_disc = json_decode(json_encode($gr_all_article_no_disc),1);

		for($i=0; $i<count($gr_all_article_no_disc); $i++){
			if($gr_all_article_no_disc[$i]['art_source'] == 1){
				$res['total_article_gr_no_disc_kls'] = $gr_all_article_no_disc[$i]['total_article'];
			} else if($gr_all_article_no_disc[$i]['art_source'] == 2){
				$res['total_article_gr_no_disc_nonkls'] = $gr_all_article_no_disc[$i]['total_article'];
			}
		}

		// All article GR with disc plus
		$q = 'SELECT alsd.art_source, COUNT(alsd.article) AS total_article FROM tr_article_logistic_site_detail alsd
				LEFT JOIN tr_article_logistic_site als ON als.outbound_delivery = alsd.outbound_delivery
				WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
		}
		$q.= ' AND als.status_in_out = "in" AND alsd.disc_plus > 0
				GROUP BY alsd.art_source';

		$gr_all_article_disc_plus = orm_get_list($q);
		$gr_all_article_disc_plus = json_decode(json_encode($gr_all_article_disc_plus),1);

		for($i=0; $i<count($gr_all_article_disc_plus); $i++){
			if($gr_all_article_disc_plus[$i]['art_source'] == 1){
				$res['total_article_gr_disc_plus_kls'] = $gr_all_article_disc_plus[$i]['total_article'];
			} else if($gr_all_article_disc_plus[$i]['art_source'] == 2){
				$res['total_article_gr_disc_plus_nonkls'] = $gr_all_article_disc_plus[$i]['total_article'];
			}
		}

		// All article GR with disc minus
		$q = 'SELECT alsd.art_source, COUNT(alsd.article) AS total_article FROM tr_article_logistic_site_detail alsd
				LEFT JOIN tr_article_logistic_site als ON als.outbound_delivery = alsd.outbound_delivery
				WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
		}
		$q.= ' AND als.status_in_out = "in" AND alsd.disc_minus > 0
				GROUP BY alsd.art_source';

		$gr_all_article_disc_minus = orm_get_list($q);
		$gr_all_article_disc_minus = json_decode(json_encode($gr_all_article_disc_minus),1);

		for($i=0; $i<count($gr_all_article_disc_minus); $i++){
			if($gr_all_article_disc_minus[$i]['art_source'] == 1){
				$res['total_article_gr_disc_minus_kls'] = $gr_all_article_disc_minus[$i]['total_article'];
			} else if($gr_all_article_disc_minus[$i]['art_source'] == 2){
				$res['total_article_gr_disc_minus_nonkls'] = $gr_all_article_disc_minus[$i]['total_article'];
			}
		}

		// All article GR with conversion diff
		$q = 'SELECT alsd.art_source, COUNT(alsd.article) AS total_article FROM tr_article_logistic_site_detail alsd
				LEFT JOIN tr_article_logistic_site als ON als.outbound_delivery = alsd.outbound_delivery
				WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
		}
		$q.= ' AND als.status_in_out = "in" AND alsd.conversion_diff > 0
				GROUP BY alsd.art_source';

		$gr_all_article_conversion_diff = orm_get_list($q);
		$gr_all_article_conversion_diff = json_decode(json_encode($gr_all_article_conversion_diff),1);

		for($i=0; $i<count($gr_all_article_conversion_diff); $i++){
			if($gr_all_article_conversion_diff[$i]['art_source'] == 1){
				$res['total_article_gr_conv_diff_kls'] = $gr_all_article_conversion_diff[$i]['total_article'];
			} else if($gr_all_article_conversion_diff[$i]['art_source'] == 2){
				$res['total_article_gr_conv_diff_nonkls'] = $gr_all_article_conversion_diff[$i]['total_article'];
			}
		}

		// All article kitting KLS
		$q = 'SELECT pbdh.outbound_delivery AS total_kitting
				FROM tr_prepack_bundling_header pbdh
				LEFT JOIN tr_prepack_bundling_detail pbd ON pbdh.outbound_delivery = pbd.outbound_delivery
				WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND pbdh.site_id = '.replace_quote($attr['site_id']);
		}
		$q.= '  AND pbd.art_source = 1
				GROUP BY pbdh.outbound_delivery';

		$kitting_all_article_kls = orm_get_list($q);
		$kitting_all_article_kls = json_decode(json_encode($kitting_all_article_kls),1);

		if(isset($kitting_all_article_kls)){
			$res['total_kitting_article_kls'] = count($kitting_all_article_kls);
		}

		// All article kitting nonKLS
		$q = 'SELECT pbdh.outbound_delivery AS total_kitting
				FROM tr_prepack_bundling_header pbdh
				LEFT JOIN tr_prepack_bundling_detail pbd ON pbdh.outbound_delivery = pbd.outbound_delivery
				WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND pbdh.site_id = '.replace_quote($attr['site_id']);
		}
		$q.= '  AND pbd.art_source = 2
				GROUP BY pbdh.outbound_delivery';

		$kitting_all_article_nonkls = orm_get($q);
		$kitting_all_article_nonkls = json_decode(json_encode($kitting_all_article_nonkls),1);

		if(isset($kitting_all_article_nonkls)){
			$res['kitting_all_article_nonkls'] = count($kitting_all_article_nonkls);
		}

		$result['data'] = $res;

        echo json_encode($result); 
		die;
	}

	public function get_number_of_od(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$res = ['total_od_gr' => 0, 
				'total_od_new' => 0,
				'total_od_completed' => 0];

		// get od gr all status
		$q = 'SELECT count(als.outbound_delivery) AS total_od
			FROM tr_article_logistic_site als
			WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
		}

		$q.= ' AND als.status_in_out = "in"';

		$all_gr = orm_get($q);
		$all_gr = json_decode(json_encode($all_gr),1);

		$res['total_od_gr'] = $all_gr['total_od'];

		// get od gr new status
		$q = 'SELECT count(als.outbound_delivery) AS total_od
			FROM tr_article_logistic_site als
			WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
		}

		$q.= ' AND als.status_in_out = "in" AND als.status_message = "new"';

		$gr_new = orm_get($q);
		$gr_new = json_decode(json_encode($gr_new),1);

		$res['total_od_new'] = $gr_new['total_od'];

		// get od gr completed status
		$q = 'SELECT als.outbound_delivery
				FROM tr_article_logistic_site als
				LEFT JOIN tr_article_logistic_site_detail alsd ON als.outbound_delivery = alsd.outbound_delivery
				WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
		}

		$q.= ' AND als.status_in_out = "in" AND als.status_message = "completed" 
				AND alsd.status_message != "new"
				GROUP BY als.outbound_delivery';

		$gr_completed = orm_get_list($q);
		$gr_completed = json_decode(json_encode($gr_completed),1);

		$res['total_od_completed'] = count($gr_completed);

		$result['data'] = $res;
		echo json_encode($result); die;
	}
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}