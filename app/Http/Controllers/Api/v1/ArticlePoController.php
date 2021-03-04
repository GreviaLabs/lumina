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

use App\Models\ArticlePoModel;

class ArticlePoController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'ms_article_po';
    public $primary_key = 'article_po_id';
    public $list_column = array('article_po_id','site_id', 'article', 'customer_article','description','customer_article_description','po_blanket_number', 'po_blanket_qty','open_qty','remaining_qty', 'actual_receive_quantity_for_art_po', 'po_created_date','line_id','status','status_message', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
    public $who = 'System';
    public $ip = 'System';
	
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
		// $log = ArticleModel::where('article_po_id',3)
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
		
		if (isset($attr['article_po_id']) && $attr['article_po_id'] != '') {
			$q.= ' AND article_po_id = '.$attr['article_po_id'];
		}

		if (isset($attr['article']) && $attr['article'] != '') {
			$q.= ' AND article = '.replace_quote($attr['article']);
		}

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}

		if (isset($attr['po_blanket_number']) && $attr['po_blanket_number'] != '') {
			$q.= ' AND po_blanket_number = '.replace_quote($attr['po_blanket_number']);
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
			
		$q = '
        SELECT *
        FROM ' . $this->table . '
        WHERE 1';
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' site_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR customer_article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR po_blanket_number LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR po_blanket_qty LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR po_created_date LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
		
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
		
		// if (isset($attr['article_po_id']) && $attr['article_po_id'] != '') {
		// 	$q.= ' AND article_po_id = '.$attr['article_po_id'];
  //       }

		if(isset($attr['created_by']) && $attr['created_by'] != ''){
			$q.= ' AND (';
			for($i=0; $i<count($attr['created_by']); $i++){
				$q.= 'created_by = '.$attr['created_by'][$i];
				if($i < count($attr['created_by'])-1){
					$q.= ' OR ';
				} else $q.= ')';
			}
		}

		if(isset($attr['start_date']) && $attr['start_date'] != ''){
			if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
			} else $q.= ' AND DATE(created_at) like '.replace_quote($attr['start_date']);
		} elseif(isset($attr['end_date']) && $attr['end_date'] != ''){
			$q.= ' AND DATE(created_at) like '.replace_quote($attr['end_date']);
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

		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
        }

		if (isset($attr['po_status']) && $attr['po_status']!= '') {
            if ($attr['po_status'] == 'active') {
                $q.= ' AND open_qty = 0 AND remaining_qty = 0';
            } elseif ($attr['po_status'] == 'fulfilled') {
                $q.= ' AND (open_qty = 0 AND remaining_qty = 0)';
            } elseif ($attr['po_status'] == 'not_fulfilled') {
                $q.= ' AND NOT (open_qty = 0 AND remaining_qty = 0)';
            }
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
        
        // debug($q,1);

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
    
    // Update all article_po where customer_article empty when update menu article
	public function update_in()
	{
		$put = $attr = $result = NULL;

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') parse_str(file_get_contents("php://input"), $_PUT);

        $put = $_PUT;
		
        $attr = validate_column($this->list_column, $put);
        
        $result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($attr)) $result['message'] = 'no data';
		
		if (! isset($attr['site_id'])) $result['message'] = 'Site_id must be filled.';
		if (! isset($attr['article'])) $result['message'] = 'Article must be filled.';
		
		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

        $q = "UPDATE ms_article_po apo
        LEFT JOIN ms_article pos ON pos.article = apo.article AND pos.site_id = apo.site_id
        SET apo.customer_article = pos.customer_article
        WHERE (apo.customer_article IS NULL OR apo.customer_article = '' ) AND pos.article = " . replace_quote($attr['article']) . " AND pos.site_id = " . replace_quote($attr['site_id']);
        
		/************ Start operation ************/
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

	/** 
	 * deprecated on 04/10/2019 by Harvei
	 * due to changing flow from transaction_in to GR
	 * 
	 * Update remaining_qty article_po
	*/
	public function update_remaining_qty_artpo(){

		$attr = $result = NULL;
		$message = 'There is no article_po data.';
		$is_success = 0;
		$sisa = 0;

		$result['is_success'] = 1;
		$result['message'] = NULL;
		$list_trx = $this->get_list_trx_in();

		if(count($list_trx) > 0) $list_trx = $list_trx;

		if (empty($list_trx)) $result['message'] = 'no data';
		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			// if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}
		/************ Start operation ************/
		// working
		for($i=0; $i<count($list_trx); $i++){
			$list_artpo = $this->get_list_article_po($list_trx[$i]);
			$actual_qty = $list_trx[$i]->total;
			$j=0;
			$param = $q2 = $qty = NULL;
			if(!empty($list_artpo))
			{
				while($actual_qty > 0)
				{
					if(!empty($list_artpo[$j]))
					{
						if($list_artpo[$j]->open_qty < $actual_qty)
						{
							break;
						} else
						{
							// if($list_artpo[$j]->open_qty > $actual_qty){
								$q = "UPDATE ms_article_po SET remaining_qty = remaining_qty + ".(int)$actual_qty.", open_qty = open_qty - ".$actual_qty.", updated_at = " . replace_quote(get_datetime()) . " WHERE article_po_id = ".replace_quote($list_artpo[$j]->article_po_id)." AND site_id = ".replace_quote($list_artpo[$j]->site_id)." AND article = ".replace_quote($list_artpo[$j]->article);
								DB::statement($q);
								$qty = $actual_qty;
								$actual_qty = 0;
								$qa = "UPDATE tr_transaction SET is_job_artpo = 1 WHERE transaction_id = ".replace_quote($list_trx[$i]->transaction_id)." AND article = ".replace_quote($list_trx[$i]->article)." AND site_id = ".replace_quote($list_trx[$i]->site_id)." AND rfid = ".replace_quote($list_trx[$i]->rfid);
								DB::statement($qa);
							// }
						}
						$q = "INSERT INTO tr_article_po_history(article_po_id, po_usage_qty, po_remaining_qty, po_created_date, reference, status_in_out, status, created_at, created_by, created_ip)";
						$q.= "VALUES(".replace_quote($list_artpo[$j]->article_po_id).",".(int)$qty.",".((int)$list_artpo[$j]->remaining_qty+(int)$qty).",".replace_quote($list_artpo[$j]->po_created_date).",".replace_quote($list_trx[$i]->transaction_id).",'in',1,".replace_quote(date("Y-m-d H:i:s")).",".replace_quote($this->who).",".replace_quote($this->ip).")";
						DB::statement($q);
						$message = 'Success with commit';
						$j++;
					} else
					{
						break;
					}
				}
			} else{
				$message = 'There is no Article PO Data left.';
			}	
		}

		$print['message'] = $message;
		echo json_encode($print);
		die;
	}

	public function get_list_article_logistic(){
		$q = 'SELECT als.outbound_delivery, alsd.article_logistic_site_detail_id, als.site_id, alsd.article, alsd.qty_receive_actual, alsd.actual_receive_quantity_for_art_po FROM tr_article_logistic_site als
			LEFT JOIN tr_article_logistic_site_detail alsd 
			ON alsd.outbound_delivery = als.outbound_delivery 
			WHERE actual_receive_quantity_for_art_po != 0';
		$list_article = orm_get_list($q);
		return $list_article;
	}

	public function get_list_trx_in(){
		$q = 'SELECT transaction_id, site_id, article, movement_type, conversion_value AS total, rfid
			FROM tr_transaction
			WHERE movement_type = "101" AND status = 1 AND is_job_artpo = 0';
			// GROUP BY transaction_id, site_id, article';
		$list_trx = orm_get_list($q);
		return $list_trx;
	}

	public function get_list_article_po($data){
		$q = 'SELECT po.article_po_id, po.site_id, po.article, po.customer_article, po.open_qty, po.remaining_qty, po.issue_qty, po.po_created_date
			FROM ms_article_po po
			WHERE po.remaining_qty >= 0 AND po.open_qty > 0 AND po.site_id = '.replace_quote($data->site_id).' AND article = '.replace_quote($data->article).'
			ORDER BY po.po_created_date ASC';
		$list_po = orm_get_list($q);
		return $list_po;
	}

	// global select
	public function get_list_article_po_a(){
		$q = 'SELECT po.article_po_id, po.site_id, po.article, po.customer_article, po.po_blanket_qty, po.remaining_qty, po.issue_qty
			FROM ms_article_po po
			WHERE po.remaining_qty = 0 AND po.issue_qty = 0
			ORDER BY po.po_created_date ASC';
		$list_po = orm_get_list($q);
		return $list_po;
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

	public function update_artpo_qty(){
		$attr = $result = NULL;
		$print = array();
		$message = 'No Article PO Updated';

		if (! empty($_GET)) $attr = $_GET;

		$result['is_success'] = 1;
		$result['message'] = NULL;
		// $list_site_detail = $this->get_list_site_detail($attr);
		// $list_artpo = $this->get_list_artpo($attr);
		$list_site_detail = $this->get_list_site_detail($attr['site_id']);
		if(count($list_site_detail) > 0) $list_site_detail = $list_site_detail;

		if (empty($list_site_detail)) $result['message_site_detail'] = 'no data site';

		// Print error if message exist
		if (isset($result['message_site_detail']) || isset($result['message_artpo'])) {
			$result['is_success'] = 0;
			// if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

		/************ Start operation ************/
		
		// Look up site detail to art po
		for($i=0; $i<count($list_site_detail); $i++){
			$remain = $list_site_detail[$i]->actual_receive_quantity_for_art_po;
			$j = 0;
			$list_artpo = $this->get_list_artpo($list_site_detail[$i]);
			while($remain > 0){
				if($j < count($list_artpo)){
					if($remain <= $list_artpo[$j]->po_blanket_qty){
						$q = "UPDATE ms_article_po SET remaining_qty = ".(int)$remain." WHERE article_po_id = '".$list_artpo[$j]->article_po_id."'";
						DB::statement($q);
						$remain = 0;
						$message = 'Succeed to update data';
					}
					else{
						$remaining_qty = (int)$list_artpo[$j]->po_blanket_qty;
						$q = "UPDATE ms_article_po SET remaining_qty = ".$remaining_qty." WHERE article_po_id = '".$list_artpo[$j]->article_po_id."'";
						DB::statement($q);
						$remain = ((int)$remain - (int)$list_artpo[$j]->po_blanket_qty);
						$message = 'Succeed to update data';
					}
					$qu = "UPDATE tr_article_logistic_site_detail SET actual_receive_quantity_for_art_po = ".$remain." WHERE outbound_delivery = '".$list_site_detail[$i]->outbound_delivery."' AND article = '".$list_site_detail[$i]->article."'";
					DB::statement($qu);
					$j++;
				}
				else{
					break;
				}
			}
		}
		$print['message'] = $message;
		echo json_encode($print);
		die;
	}

	public function get_list_site_detail($site){
		$q = "SELECT d.outbound_delivery, h.site_id, d.article, d.actual_receive_quantity_for_art_po 
				FROM tr_article_logistic_site_detail d 
				LEFT JOIN tr_article_logistic_site h 
				ON h.outbound_delivery = d.outbound_delivery 
				WHERE h.site_id = '".$site."' AND d.actual_receive_quantity_for_art_po != 0";
		$list_data = orm_get_list($q);
		return $list_data;
	}

	public function get_list_artpo($data){
		$q = "SELECT article_po_id, site_id, article, po_blanket_qty, remaining_qty, issue_qty
				FROM ms_article_po
				WHERE remaining_qty = 0 AND issue_qty = 0 AND site_id = ".replace_quote($data->site_id)." AND article = ".replace_quote($data->article)."
				ORDER BY po_created_date ASC";
		$list_data = orm_get_list($q);
		return $list_data;
	}
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}