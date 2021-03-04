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

use App\Models\RfidArticleModel;

class RfidArticleController extends ApiController {

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
    public $list_column = array('rfid_article_id','site_id', 'outbound_delivery', 'article', 'description', 'rfid', 'picktime','user_id','site_chamber_gr','chamber_sync_flag','status_message','status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
	
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
		// $log = ArticleModel::where('rfid_article_id',3)
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
		
		if (isset($attr['rfid_article_id']) && $attr['rfid_article_id'] != '') {
			$q.= ' AND rfid_article_id = '.$attr['rfid_article_id'];
		}
		
		if (isset($attr['rfid']) && $attr['rfid'] != '') {
			$q.= ' AND rfid = '.$attr['rfid'];
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
			
		$q = 'SELECT rfid.rfid_article_id, rfid.site_id, rfid.outbound_delivery, rfid.article, rfid.description, rfid.rfid, rfid.picktime, rfid.user_id, rfid.status, rfid.created_at, rfid.updated_at,';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE rfid.user_id = u.user_id), rfid.created_by) as user_id,';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE rfid.created_by = u.user_id), rfid.created_by) as creator,';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE rfid.updated_by = u.user_id), rfid.updated_by) as editor';
		$q.= ' FROM ' . $this->table . ' rfid WHERE 1';
		$q.= ' HAVING 1';
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' site_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR outbound_delivery LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR rfid LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR picktime LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR user_id LIKE '.replace_quote($attr['keyword'],'like');
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
		
		// if (isset($attr['rfid_article_id']) && $attr['rfid_article_id'] != '') {
		// 	$q.= ' AND rfid_article_id = '.$attr['rfid_article_id'];
  //       }
		// debug($attr,1);
		if (isset($attr['status']) && in_array($attr['status'],array(-1,0,1))) {
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
	
	public function get_list_rfid()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;			
		
		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($attr)) $result['message'] = 'no data';
        
		if (! isset($attr['rfid'])) $result['message'] = 'rfid must be filled.';
		
		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

		$q = "SELECT * FROM ms_rfid_article WHERE 1 = 1 AND rfid in(".$attr['rfid'].")";
		
		if (isset($attr['status_message']) && $attr['status_message'] != '') {
			$q.= " AND status_message = '".$attr['status_message']."'";
        }
		if (isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != '') {
			$q.= " AND outbound_delivery = '".$attr['outbound_delivery']."'";
        }
		if (isset($attr['status']) && in_array($attr['status'],array(-1,0,1))) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
		}
		//  debug($q,1);	
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

	// reporting
	public function get_rfid_report(){
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT SUM((alsd.qty_receive*alsd.conversion_value)+alsd.conversion_diff) AS total_rfid
				FROM tr_article_logistic_site als
				LEFT JOIN tr_article_logistic_site_detail alsd ON 1=1 AND alsd.outbound_delivery = als.outbound_delivery
				WHERE 1 AND als.status_message != "new"';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' als.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND als.site_id = '.replace_quote($attr['site_id']);
			}
		}

		$total_rfid = orm_get($q);

		$q = 'SELECT CAST(SUM(combine_qty + conversion_diff) AS DECIMAL) as total_prepack_rfid FROM tr_prepack_bundling_header';

		$q.= ' WHERE 1 ';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
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

		$total_rfid_kitting = orm_get($q);

		$q = 'SELECT sum(art.conversion_value) AS mapping_rfid FROM ms_rfid_article rfid
				LEFT JOIN ms_article art ON rfid.article = art.article AND rfid.site_id = art.site_id';

		$q.= ' WHERE 1';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' rfid.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND rfid.site_id = '.replace_quote($attr['site_id']);
			}
		}

		$mapping_rfid = orm_get($q);

		$total_rfid = json_decode(json_encode($total_rfid),1);
		$total_rfid_kitting = json_decode(json_encode($total_rfid_kitting),1);
		$total_rfid['total_rfid'] = $total_rfid_kitting['total_prepack_rfid'] + $total_rfid['total_rfid'];
		$mapping_rfid = json_decode(json_encode($mapping_rfid),1);
		$unmapping_rfid['unmapping_rfid'] = (int)$total_rfid['total_rfid']-(int)$mapping_rfid['mapping_rfid'];
		
		$data = ['mapping_rfid' => $mapping_rfid['mapping_rfid'], 'unmapping_rfid' => $unmapping_rfid['unmapping_rfid']];

        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	public function list_unmapping_rfid_article(){
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT a.outbound_delivery, a.site_id, b.article, b.description, 
				IFNULL((b.qty_receive * b.conversion_value) + b.conversion_diff,0) - IFNULL(COUNT(rfid) * b.conversion_value,0) count_rfid, b.conversion_value, a.created_at 
				FROM tr_article_logistic_site a
				LEFT JOIN tr_article_logistic_site_detail b ON 1=1 AND b.outbound_delivery = a.outbound_delivery
				LEFT JOIN ms_rfid_article c ON 1=1 AND c.outbound_delivery = b.outbound_delivery AND c.article = b.article AND c.site_id = a.site_id';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' a.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND a.site_id = '.replace_quote($attr['site_id']);
			}
		}

		if(isset($attr['article']) && $attr['article'] != ''){
				$q.= ' AND b.article LIKE '.replace_quote($attr['article'], 'LIKE');
		}

		if(isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != ''){
				$q.= ' AND a.outbound_delivery LIKE '.replace_quote($attr['outbound_delivery'], 'LIKE');
		}


		$q.= ' GROUP BY a.outbound_delivery, a.site_id, b.article, b.description, b.conversion_diff, b.qty_receive, 
					b.conversion_value, a.created_at
				HAVING count_rfid > 0';

		$q.= '	UNION ALL';

		$q.= '	SELECT pbh.outbound_delivery, pbh.site_id, pbd.article, "" description, 
				IFNULL(pbh.combine_qty + pbh.conversion_diff,0) - IFNULL(COUNT(DISTINCT CONCAT(c.rfid+c.outbound_delivery)) * pbh.conversion_value,0) count_rfid, 
					pbh.conversion_value, pbh.created_at 
				FROM tr_prepack_bundling_header pbh
				LEFT JOIN tr_prepack_bundling_detail pbd ON 1=1 AND pbd.outbound_delivery = pbh.outbound_delivery
				LEFT JOIN ms_rfid_article c ON 1=1 AND c.outbound_delivery = pbd.outbound_delivery AND c.article = pbd.article AND c.site_id = pbh.site_id';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' pbh.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND pbh.site_id = '.replace_quote($attr['site_id']);
			}
		}

		if(isset($attr['article']) && $attr['article'] != ''){
				$q.= ' AND pbd.article LIKE '.replace_quote($attr['article'], 'LIKE');
		}

		if(isset($attr['outbound_delivery']) && $attr['outbound_delivery'] != ''){
				$q.= ' AND pbh.outbound_delivery LIKE '.replace_quote($attr['outbound_delivery'], 'LIKE');
		}

		$q.= '	GROUP BY pbh.outbound_delivery, pbh.site_id, pbd.article, pbh.combine_qty, pbh.conversion_value, 
				pbh.conversion_diff, pbh.created_at
				HAVING count_rfid > 0';

		$data = orm_get_list($q);

        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
	}

	public function update_status_rfid(){
		$attr = $result = NULL;
		if (! empty($_POST)) $attr = $_POST;
		$attr = validate_column($this->list_column, $attr);

		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($attr)) $result['message'] = 'no data';

        // Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			return json_encode($result);
		}

		/************ Start operation ************/

		$q = 'UPDATE ms_rfid_article SET status = 0, status_message = "Rejected", chamber_sync_flag = 10';
		$q.= ' WHERE 1 AND outbound_delivery = '.replace_quote($attr['outbound_delivery']);
		$q.= ' AND article = '.replace_quote($attr['article']);
		$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		$q.= ' AND rfid = '.replace_quote($attr['rfid']);
		$q.= ' AND status = '.replace_quote($attr['status']);
		$q.= ' AND status_message = '.replace_quote($attr['status_message']);

		$update = DB::statement($q);
		if ($update) {
			$result['is_success'] = 1;
			$result['message'] = 'update success';
		} else {
			$result['is_success'] = 0;
			$result['message'] = 'update failed';
			// $result['query'] = $update->toSql();
		}

		return json_encode($result);
	}
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}