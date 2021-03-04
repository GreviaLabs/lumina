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

use App\Models\TransactionModel;
use App\Models\UserModel;

class TransactionController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'tr_transaction';
    public $primary_key = 'transaction_id';
    public $list_column = array('transaction_id','site_id','user_id','article','movement_type','customer_article','description','conversion_value','qty','value','status_in_out','reason_id','wo_wbs','remark','ref_cc','outbound_delivery','rfid','picktime','flag_used','price','site_chamber_gr', 'status', 'status_message','is_job_order', 'is_job_artpo','chamber_sync_flag','dashboard_sync_flag','last_sync', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip', 'start_date', 'end_date', 'status_in_out', 'ref_so_sap', 'ref_sc_order_id');
	// protected $date = new DateTime();
	// public $datetime = $date->format("Y-m-d h:i:s");
	public $who = 'System';
	public $ip = "System";

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
		// $log = ArticleModel::where('transaction_id',3)
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
		
		if (isset($attr['transaction_id']) && $attr['transaction_id'] != '') {
			$q.= ' AND transaction_id = ' . replace_quote($attr['transaction_id']);
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
	
	// get list order 201
	public function get_list()
	{
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT tr.transaction_id, tr.site_id, tr.qty, tr.price, art.price_mf, tr.value, tr.conversion_value, tr.status_in_out, tr.wo_wbs, reason_value, tr.article, tr.description, tr.customer_article, tr.ref_so_sap, tr.ref_sc_order_id, tr.created_at, tr.updated_at, tr.status, tr.user_id, tr.is_job_artpo, movement_type, ';
		$q.= ' COALESCE((SELECT CONCAT(u.user_code ," - ", u.firstname , " ", u.lastname) FROM ms_user u WHERE tr.created_by = u.user_id), tr.created_by) as creator,';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE tr.updated_by = u.user_id), tr.updated_by) as editor';
		$q.= ' FROM ' . $this->table . ' tr';
		$q.= ' LEFT JOIN ms_reason r ON r.reason_id = tr.reason_id';
		$q.= ' LEFT JOIN ms_article art ON tr.article = art.article AND tr.site_id = art.site_id';
		$q.= ' WHERE 1';
		$q.= ' HAVING 1';
		
		if (isset($attr['filter']) && $attr['filter'] != '') 
		{
			// validate_column
			// $filter = validate_column($this->list_column, $attr['filter']);
			// $filter = $attr['filter'];

			if (! empty($filter) && isset($filter) && $filter != '') 
			{
                
                $i = 0;
				$q.= ' AND (';
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
		// else{

		// 	if (isset($attr['transaction_id']) && $attr['transaction_id'] != '') {
		// 		$q.= ' AND transaction_id = ' . replace_quote($attr['transaction_id']);
	 //        }

	 //        if (isset($attr['article']) && $attr['article'] > 0) {
		// 		$q.= ' AND article =  '.$attr['article'];
	 //        }
		// }

		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			// array('site_id','transaction_id','site_name','site_address','site_qty_value','flag_qty_value','method_calc','start_date_counting', 'reset_days', 'logo_file_name', 'chamber_sync_flag', 'field_sync', 'status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
			
			$q.= ' AND ( ';
			$q.= ' transaction_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR customer_article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR description LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR site_address LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR method_calc LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR logo_file_name LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }

        if (isset($attr['transaction_id']) && $attr['transaction_id'] != '') {
			$q.= ' AND transaction_id LIKE '.replace_quote($attr['transaction_id'],'like');
        }

        if (isset($attr['article']) && $attr['article'] != '') {
			$q.= ' AND article LIKE '.replace_quote($attr['article'],'like');
        }

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			$attr['start_date'] = date('Y-m-d',strtotime($attr['start_date']));
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$attr['end_date'] = date('Y-m-d',strtotime($attr['end_date']));
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }
        
		if(isset($attr['status_in_out']) && $attr['status_in_out'] != ''){
			if($attr['status_in_out'] != 'all')	$q.= ' AND status_in_out = '.replace_quote($attr['status_in_out']);
			else $q.= ' AND status_in_out IN ("in", "out")';
		}
		
		if (isset($attr['user_id']) && $attr['user_id'] > 0) {
			$q.= ' AND user_id =  '.$attr['user_id'];
        }

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id =  '.replace_quote($attr['site_id']);
        }

		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND tr.status = '.$attr['status'];
        } else {
			$q.= ' AND tr.status != -1';
		}

		if (isset($attr['movement_type']) && $attr['movement_type'] != '') {
			$q.= ' AND movement_type = '.replace_quote($attr['movement_type']);
        }

        if (isset($attr['not_movement_type']) && $attr['not_movement_type'] != '') {
			$q.= ' AND movement_type != '.replace_quote($attr['not_movement_type']);
        }

        // not tested yet
        if (isset($attr['job_order_status']) && $attr['job_order_status'] != '') {
            if ($attr['job_order_status'] == 'pending') {
                $q.= ' AND is_job_order != 0';
            } else if ($attr['job_order_status'] == 'completed') {               
                $q.= ' AND is_job_order = 1';
            }
        }

        // not tested yet
        if (isset($attr['job_artpo_status']) && $attr['job_artpo_status'] != '') {
            if ($attr['job_artpo_status'] == 'pending') {
                $q.= ' AND is_job_artpo != 0';
            } else if ($attr['job_artpo_status'] == 'completed') {               
                $q.= ' AND is_job_artpo = 1';
            }
        }

        if (isset($attr['art_source']) && $attr['art_source'] != '') {
        	if($attr['art_source'] == 'KLS'){
				$q.= ' AND art_source = 1';
        	} elseif($attr['art_source'] == 'NON_KLS'){
        		$q.= ' AND art_source = 2';
        	}
        }

        if (isset($attr['opt_so_sap']) && $attr['opt_so_sap'] != '') {
        	if($attr['opt_so_sap'] == 'exists'){
				$q.= ' AND ref_so_sap IS NOT NULL';
        	} elseif($attr['opt_so_sap'] == 'not_exists'){
        		$q.= ' AND ref_so_sap IS NULL';
        	}
        }

        if (isset($attr['opt_sc_order_id']) && $attr['opt_sc_order_id'] != '') {
        	if($attr['opt_sc_order_id'] == 'exists'){
				$q.= ' AND ref_sc_order_id IS NOT NULL';
        	} elseif($attr['opt_sc_order_id'] == 'not_exists'){
        		$q.= ' AND ref_sc_order_id IS NULL';
        	}
        }

        if(isset($attr['ref_so_sap']) && $attr['ref_so_sap'] != ''){
			$q.= ' AND ref_so_sap LIKE '.replace_quote($attr['ref_so_sap'],'like');
        }

        if(isset($attr['ref_sc_order_id']) && $attr['ref_sc_order_id'] != ''){
			$q.= ' AND ref_sc_order_id LIKE '.replace_quote($attr['ref_sc_order_id'],'like');
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

	public function get_list_export()
	{
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT tr.transaction_id, tr.site_id as site, tr.qty as qty, tr.value as price, tr.conversion_value as conversion_uom, tr.status_in_out as status, tr.wo_wbs, tr.article, tr.description, tr.customer_article, tr.ref_so_sap, tr.ref_sc_order_id, tr.is_job_artpo, ';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE tr.user_id = u.user_id), tr.user_id) as user,';
		$q.= ' tr.created_at as transaction_date, ';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE tr.created_by = u.user_id), tr.created_by) as creator';
		$q.= ' FROM ' . $this->table . ' tr WHERE 1';

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
    		$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}

		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' transaction_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR customer_article LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR description LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
        	if(isset($attr['end_date']) && $attr['end_date'] != ''){
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
        	} else{
        		$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND NOW()';
        	}
        }

		if(isset($attr['status_in_out']) && $attr['status_in_out'] != ''){
			if($attr['status_in_out'] != 'all')	$q.= ' AND status_in_out = '.replace_quote($attr['status_in_out']);
			else $q.= ' AND status_in_out IN ("in", "out")';
		}

        if (isset($attr['opt_so_sap']) && $attr['opt_so_sap'] != '') {
        	if($attr['opt_so_sap'] == 'exists'){
				$q.= ' AND ref_so_sap IS NOT NULL';
        	} elseif($attr['opt_so_sap'] == 'not_exists'){
        		$q.= ' AND ref_so_sap IS NULL';
        	}
        }

        if (isset($attr['opt_sc_order_id']) && $attr['opt_sc_order_id'] != '') {
        	if($attr['opt_sc_order_id'] == 'exists'){
				$q.= ' AND ref_sc_order_id IS NOT NULL';
        	} elseif($attr['opt_sc_order_id'] == 'not_exists'){
        		$q.= ' AND ref_sc_order_id IS NULL';
        	}
        }

        if(isset($attr['ref_so_sap']) && $attr['ref_so_sap'] != ''){
			$q.= ' AND ref_so_sap LIKE '.replace_quote($attr['ref_so_sap'],'like');
        }
        
        if(isset($attr['ref_sc_order_id']) && $attr['ref_sc_order_id'] != ''){
			$q.= ' AND ref_sc_order_id LIKE '.replace_quote($attr['ref_sc_order_id'],'like');
        }

		if (isset($attr['movement_type']) && $attr['movement_type'] != '') {
			$q.= ' AND movement_type = '.replace_quote($attr['movement_type']);
        }

        if (isset($attr['not_movement_type']) && $attr['not_movement_type'] != '') {
			$q.= ' AND movement_type != '.replace_quote($attr['not_movement_type']);
        }
        $result['total_rows'] = count(orm_get_list($q));
        $data = orm_get_list($q,'array');
        $result['data'] = $data;
        return json_encode($result); 
	}

	public function get_list_transaction_cc(){
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;
		
		$q = 'SELECT transaction_id, site_id, user_id, article, movement_type, customer_article, description, conversion_value, qty, value, status_in_out, ref_cc, outbound_delivery, rfid, price, site_chamber_gr, created_at, status_message FROM '.$this->table;
		$q.= ' WHERE 1 ';

		if(isset($attr['cc_id']) && $attr['cc_id'] != ''){
			$q.= 'AND ref_cc = '.replace_quote($attr['cc_id']);
		}

		if(isset($attr['article']) && $attr['article'] != ''){
			$q.= ' AND article = '.replace_quote($attr['article']);
		}

		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= 'AND site_id = '.replace_quote($attr['site_id']);
		}

		$q.= ' ORDER BY created_at ASC';

		$data = orm_get_list($q);
		echo json_encode($data);
		die;
	}

	/** 
	 * 
	 * 1. Decrease ms_user -> update quota remaining
	 * 2. Decrease ms_article_stock -> update stock
	 * 3. Insert table tr_movement_article -> insert
	 * 4. Update table ms_rfid_article set status to 0
	 * 5. Update table tr_transaction set is_job_order to 1
	*/
	public function trans_out_order()
	{
		$attr = $result = NULL;
		$print = array();
		$stage = 0;
		$message = 'init';
		$sisa = 0;

		$result['is_success'] = 1;
		$result['message'] = NULL;
		$list_data = $this->get_list_order_trans_out();
		// debug($list_data,1);
		if(count($list_data) > 0) $list_data = $list_data;

		if (empty($list_data)) $result['message'] = 'no data';

		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			echo json_encode($result);
			die;
		}
		/************ Start operation ************/
		for($i=0; $i<count($list_data); $i++){
			DB::beginTransaction();

			// 1 (decreasing user quota)
			try {
                // tidak perlu cek kuota user mencukupi atau tidak, karena sudah dicek oleh chamber
                // ignore user category as discussed with aan 18 june 2019 by rusdi (deprecated on 25 July 2019)
				if(strtolower($list_data[$i]->user_category) == 'user' && $list_data[$i]->movement_type == '201'){
					$q = "SELECT site_id, config_name, config_value FROM ms_config 
							WHERE config_name = 'is_quota' AND status = 1 
							AND site_id =".replace_quote($list_data[$i]->site_id);
					$config = orm_get($q);

					// get user for a real time quota
					$user = $this->get_user($list_data[$i]->user_id);
					$list_data[$i]->quota_remaining = $user->quota_remaining;
					$list_data[$i]->quota_additional = $user->quota_additional;
					// if config use decrease quota user itself
					if($config->config_value == 1){
						// check if user quota is enough
						if($list_data[$i]->total <= $list_data[$i]->quota_remaining){
							$q = "UPDATE ms_user SET quota_remaining = quota_remaining - ".((int)$list_data[$i]->total);
						} else{
							$sisa = ((int)$list_data[$i]->total - (int)$list_data[$i]->quota_remaining);
							$q = "UPDATE ms_user SET quota_remaining = quota_remaining - ".((int)$list_data[$i]->quota_remaining);
							$q.= ", quota_additional = quota_additional - ".((int)$sisa);
						}
						$q.= ",	updated_at = " . replace_quote(get_datetime()) . " 
								WHERE user_id = ".replace_quote($list_data[$i]->user_id)." 
								AND site_id = ".replace_quote($list_data[$i]->site_id);
						DB::statement($q);
						// insert to tr_movement_quota_level
						$q = "INSERT INTO tr_movement_quota_level(user_id, site_id, transaction_id, qty, value, addt, balance_qty, 
								balance_value, balance_addt, created_at, created_by, created_ip)";
						// check if user quota is enough
						if($list_data[$i]->total <= $list_data[$i]->quota_remaining){
							$q.= " VALUES(".replace_quote($list_data[$i]->user_id).","
								.replace_quote($list_data[$i]->site_id).","
								.replace_quote($list_data[$i]->transaction_id).","
								.(int)$list_data[$i]->total.","
								.(int)$list_data[$i]->value.",NULL";
							if(strtolower($list_data[$i]->flag_qty_value) == "value"){
								$q.= ",NULL,"
									.((int)$list_data[$i]->quota_remaining-(int)$list_data[$i]->value)
									.',NULL';
							} elseif(strtolower($list_data[$i]->flag_qty_value) == "qty"){
								$q.= ","
									.((int)$list_data[$i]->quota_remaining-(int)$list_data[$i]->total)
									.",NULL,NULL";
							}
						} else{
							$sisa = ((int)$list_data[$i]->total-(int)$list_data[$i]->quota_remaining);
							$q.= " VALUES(".replace_quote($list_data[$i]->user_id).","
								.replace_quote($list_data[$i]->site_id).","
								.replace_quote($list_data[$i]->transaction_id).","
								// insert quota_remaining (quota_remaining user not enough)
								.((int)$list_data[$i]->quota_remaining).","
								.(int)$list_data[$i]->value.","
								// insert remaining qty (use additional)
								.$sisa;
							if(strtolower($list_data[$i]->flag_qty_value) == "value"){
								$q.= ",NULL,"
									.((int)$list_data[$i]->quota_remaining)
									.','
									.((int)$list_data[$i]->quota_additional-(int)$list_data[$i]->value);
							} elseif(strtolower($list_data[$i]->flag_qty_value) == "qty"){
								$q.= ","
									.((int)$list_data[$i]->quota_remaining)
									.",NULL,"
									.((int)$list_data[$i]->quota_additional-(int)$sisa);
							}
						}
						$q.= ",".replace_quote(date("Y-m-d H:i:s")).",".replace_quote($this->who).",".replace_quote($this->ip).")";
						DB::statement($q);
					} elseif($config->config_value == 2){
						// if config use decrease quota recursive
						UserModel::deduct_child_quota($list_data[$i]);
					}
					$message = 'success with commit';
				} elseif(strtolower($list_data[$i]->user_category) != 'user' && $list_data[$i]->movement_type == '205'){
					$message = 'not decrease quota user';
				}
				$stage++;
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}

			// 2 (decreasing article stock_qty (chamber) and stock_dashboard)
			try {
				// if movement_type 201 (trans_out normal) decrease stock_dashboard
				// else (movement_type 205) just decrease stock_qty (chamber)
				$q = "UPDATE ms_article_stock SET stock_qty = stock_qty - ".((int)$list_data[$i]->total);
				if($list_data[$i]->movement_type == '201'){
					$q.= ", stock_dashboard = stock_dashboard - ".((int)$list_data[$i]->total);
				}
				$q.= ", updated_at = " . replace_quote(get_datetime()) . " 
						 WHERE site_id = ".replace_quote($list_data[$i]->site_id)." 
						 AND article = ".replace_quote($list_data[$i]->article);
				DB::statement($q);
				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}

			// 3 (insert new movement article)
			try {
                $q = "SELECT stock_qty, stock_dashboard FROM ms_article_stock a 
                		WHERE a.article = ".replace_quote($list_data[$i]->article) . " 
                		AND site_id = ". replace_quote($list_data[$i]->site_id);
                $stock = orm_get($q);

				// if movement_type 201 insert movement article for dashboard
				// if movement_type 205 just insert movement article for chamber
                $q = "INSERT INTO tr_movement_article(receiving_site_id,article,description,qty,movement_type,status,
                		reference,balance_qty,created_at,created_by,created_ip,is_chamber,reference_type)
                		VALUES(".replace_quote($list_data[$i]->site_id).",".replace_quote($list_data[$i]->article).",".
                			replace_quote($list_data[$i]->description).",".$list_data[$i]->total.",".
                			replace_quote($list_data[$i]->movement_type).",1,".replace_quote($list_data[$i]->transaction_id).
                			",".replace_quote($stock->stock_qty).",".replace_quote(date("Y-m-d H:i:s")).",".
                			replace_quote($this->who).",".replace_quote($this->ip).",1,'tr')";
				DB::statement($q);
				
				if($list_data[$i]->movement_type == '201'){
					$q = "INSERT INTO tr_movement_article(receiving_site_id,article,description,qty,movement_type,status,
							reference,balance_qty,created_at,created_by,created_ip,is_chamber,reference_type)
							VALUES(".replace_quote($list_data[$i]->site_id).",".replace_quote($list_data[$i]->article).",".
								replace_quote($list_data[$i]->description).",".$list_data[$i]->total.",".
								replace_quote($list_data[$i]->movement_type).",1,".replace_quote($list_data[$i]->transaction_id).
								",".replace_quote($stock->stock_dashboard).",".replace_quote(date("Y-m-d H:i:s")).",".
								replace_quote($this->who).",".replace_quote($this->ip).",0,'tr')";
					DB::statement($q);
				}
				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}

			// 4 (update status rfid_article to 0)
			try {
				$q = "UPDATE ms_rfid_article SET status = 0, status_message = 'Closed', updated_at = " . replace_quote(get_datetime())." 
						WHERE rfid IN (SELECT rfid FROM ".$this->table." a 
						WHERE 1 AND a.transaction_id = ".replace_quote($list_data[$i]->transaction_id)." 
						AND a.movement_type like '2%' AND a.is_job_order = 0)";
				DB::statement($q);
				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				$message = 'fail with rollback';
				throw $e;
			}

			// 5 (update is_job_order to 1)
			try {
				$q = "UPDATE ".$this->table." SET is_job_order = 1, updated_at = " . replace_quote(get_datetime()) . " 
						WHERE ".$this->primary_key." = ".replace_quote($list_data[$i]->transaction_id)." 
						AND article = ".replace_quote($list_data[$i]->article);
				DB::statement($q);
				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}

			DB::commit();

		}

		// // Update flag data to 1
		// $q = "INSERT INTO tr_article_logistic_site(site_id,outbound_delivery) VALUES('xx','update')";
		// DB::statement($q);
		
		$print['message'] = $message;
		$print['stage'] = $stage;
		echo json_encode($print);
		die;
	}

	/** 
	 * 
	 * 1. Decreasing article po qty
	 * 2. Update is_job_artpo to 1
	*/
	public function trans_out_artpo(){

		$attr = $result = NULL;
		$print = array();
		$stage = 0;
		$message = 'There is no article_po data.';
		$is_success = 0;
		$sisa = 0;

		$result['is_success'] = 1;
		$result['message'] = NULL;
		$list_data = $this->get_list_arttpo_trans_out();
		// debug($list_data,1);
		if(count($list_data) > 0) $list_data = $list_data;

		if (empty($list_data)) $result['message'] = 'no data';

		// for($i=0; $i<count($list_data); $i++){
		// 	if (! isset($list_data[$i]->transaction_id)) $result['message'] = $this->primary_key . ' must be filled.';
		// }

		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			// if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}
		/************ Start operation ************/
		for($i=0; $i<count($list_data); $i++){
			DB::beginTransaction();

			// 1 (decreasing article po qty)
			try {
				$list_po = $this->get_list_article_po($list_data[$i]);
				$minus = $list_data[$i]->total;
				$k = 0;
				if(!empty($list_po)){
					while($minus > 0){
						if(!empty($list_po[$k])){
							if($list_po[$k]->remaining_qty < $minus){
								$q = "UPDATE ms_article_po SET remaining_qty = 0, 
										issue_qty = issue_qty + ".((int)$list_po[$k]->remaining_qty).", 
										updated_at = " . replace_quote(get_datetime()) . " 
										WHERE article_po_id = ".replace_quote($list_po[$k]->article_po_id)." 
										AND site_id = ".replace_quote($list_po[$k]->site_id)." 
										AND article = ".replace_quote($list_po[$k]->article);
								DB::statement($q);
								$q = "INSERT INTO tr_article_po_history(article_po_id, po_usage_qty, po_remaining_qty, po_created_date, reference, status_in_out, status, created_at, created_by, created_ip)
									VALUES(".replace_quote($list_po[$k]->article_po_id).",".(int)$list_po[$k]->remaining_qty.",0,".replace_quote($list_po[$k]->po_created_date).",".replace_quote($list_data[$i]->transaction_id).",'out',0,".replace_quote(date("Y-m-d H:i:s")).",".replace_quote($this->who).",".replace_quote($this->ip).")";
								DB::statement($q);
								$minus = ((int)$minus-(int)$list_po[$k]->remaining_qty);
								$msg = 'Success with commit';
							}
							else{
								$q = "UPDATE ms_article_po SET remaining_qty = remaining_qty - ".((int)$minus).", 
										issue_qty = issue_qty + ".((int)$minus).", 
										updated_at = " . replace_quote(get_datetime()) . " 
										WHERE article_po_id = ".replace_quote($list_po[$k]->article_po_id)." 
										AND site_id = ".replace_quote($list_po[$k]->site_id)." 
										AND article = ".replace_quote($list_po[$k]->article);
								DB::statement($q);
								$q = "INSERT INTO tr_article_po_history(article_po_id, po_usage_qty, po_remaining_qty, po_created_date, reference, status_in_out, status, created_at, created_by, created_ip)
									VALUES(".replace_quote($list_po[$k]->article_po_id).",".(int)$minus.",".((int)$list_po[$k]->remaining_qty-(int)$minus).",".replace_quote($list_po[$k]->po_created_date).",".replace_quote($list_data[$i]->transaction_id).",'out',1,".replace_quote(date("Y-m-d H:i:s")).",".replace_quote($this->who).",".replace_quote($this->ip).")";
								DB::statement($q);
								$minus = 0;
								$msg = 'Success with commit.';
								$is_success = 1;
								// break;
							}
							$k++;
						}
						else{
							DB::rollback();
							$msg = 'There is no data left. Fail with rollback';
							break;
						}
					}
				} else{
					DB::rollback();
					$msg = 'There is no article_po data. Fail with rollback';
					continue;
				}
				$stage++;
				$message = $msg;
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}

			// 2 (Update is_job_artpo to 1)
			try {
				if($is_success == 1){
					$q = "UPDATE ".$this->table." SET is_job_artpo = 1,
							 updated_at = " . replace_quote(get_datetime()) . " 
							 WHERE ".$this->primary_key." = ".replace_quote($list_data[$i]->transaction_id)." 
							 AND article = ".replace_quote($list_data[$i]->article);
					DB::statement($q);
					$stage++;
					$message = 'success with commit';
				}
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}

			DB::commit();
		}
		

		$print['message'] = $message;
		$print['stage'] = $stage;
		echo json_encode($print);
		die;
	}

	/** 
	 * 
	 * 1. Increase ms_article_stock -> update stock
	 * 2. Insert table tr_movement_article -> insert
	 * 3. Update rfid_article set status_message to status_message from table transaction
	 * 4. Update transaction set is_job_order to 1
	*/
	public function trans_in()
	{
		$attr = $result = NULL;
		$print = array();
		$stage = 0;
		$message = 'init';
		$sisa = 0;

		$result['is_success'] = 1;
		$result['message'] = NULL;
		$list_data = $this->get_list_order_trans_in();
		if(count($list_data) > 0) $list_data = $list_data;

		if (empty($list_data)) $result['message'] = 'no data';

		// for($i=0; $i<count($list_data); $i++){
		// 	if (! isset($list_data[$i]->transaction_id)) $result['message'] = $this->primary_key . ' must be filled.';
		// }

		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			// if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}
		/************ Start operation ************/
		for($i=0; $i<count($list_data); $i++){
			DB::beginTransaction();

			// 1 (increasing article stock_qty (chamber))
			try {
				$q = "UPDATE ms_article_stock SET stock_qty = stock_qty + ".((int)$list_data[$i]->total).", 
						updated_at = " . replace_quote(get_datetime()) . " 
						WHERE site_id = '".$list_data[$i]->site_id."' 
						AND article = '".$list_data[$i]->article."'";
				DB::statement($q);

				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}

			// 2 (insert new movement article)
			try {
                $q = "SELECT stock_qty FROM ms_article_stock a 
                		WHERE a.article = ".replace_quote($list_data[$i]->article) . " 
                		AND site_id = ". replace_quote($list_data[$i]->site_id);
                $stock_qty = orm_get($q,'stock_qty');

                $q = "INSERT INTO tr_movement_article(receiving_site_id,article,description,qty,movement_type,status,
                		reference,balance_qty,created_at,created_by,created_ip,is_chamber,reference_type)
						VALUES('".$list_data[$i]->site_id."','".$list_data[$i]->article."','".$list_data[$i]->description."',".$list_data[$i]->total.",".$list_data[$i]->movement_type.",1,'".$list_data[$i]->transaction_id."',".$stock_qty.",'".date("Y-m-d H:i:s")."','".$this->who."','".$this->ip."',1,'tr')";
				DB::statement($q);
				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
			}

			// 3 (update rfid_article set status_message to status_message from table transaction.)
			try {
				// depracated by Harvei on 22 July 2019
				// $q = "UPDATE ms_rfid_article SET status_message = ".replace_quote($list_data[$i]->status_message)
				// 	." WHERE rfid IN (SELECT rfid FROM ".$this->table." a"
				// 	." WHERE 1 AND a.transaction_id = ".replace_quote($list_data[$i]->transaction_id)
				// 	." AND a.movement_type like '1%' AND a.is_job_order = 0 AND a.status = 1)";
				$q = "UPDATE ms_rfid_article ra
						LEFT JOIN ".$this->table." tr ON ra.rfid = tr.rfid
						SET ra.status_message = ".replace_quote($list_data[$i]->status_message)."
						WHERE 1 AND tr.transaction_id = ".replace_quote($list_data[$i]->transaction_id)."
						 AND tr.movement_type like '1%' AND tr.is_job_order = 0 AND tr.status = 1 AND tr.site_id = ".replace_quote($list_data[$i]->site_id);
				DB::statement($q);

				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				$message = 'fail with rollback';
				throw $e;
			}

			// 4 (update transaction set is_job_order to 1)
			try {
				$q = "UPDATE ".$this->table." SET is_job_order = 1 
						WHERE ".$this->primary_key." = ".replace_quote($list_data[$i]->transaction_id)." 
						AND article = ".replace_quote($list_data[$i]->article);
				DB::statement($q);
				$stage++;
				$message = 'success with commit';
			} catch (\Throwable $e) {
				DB::rollback();
				$message = 'fail with rollback';
				throw $e;
            }

			DB::commit();
		}
		
		$print['message'] = $message;
		$print['stage'] = $stage;
		echo json_encode($print);
		die;
	}

	public function get_list_order_trans_out(){
		$q = "SELECT a.transaction_id, a.site_id, a.user_id, a.movement_type, a.article, a.customer_article,
			 a.description, sum(a.conversion_value) as total, a.qty, sum(a.value) as value, a.status_in_out, a.reason_id, 
			 a.is_job_order, a.is_job_artpo, b.user_category, b.quota_remaining, b.quota_additional, c.flag_qty_value FROM " . $this->table . "
			  a LEFT JOIN ms_user b ON a.user_id = b.user_id 
			  LEFT JOIN ms_site c ON a.site_id = c.site_id 
			  WHERE 1 AND a.status = 1 AND a.is_job_order = 0 
			  AND (a.movement_type like '2%' OR a.movement_type like '3%')
			  GROUP BY transaction_id, site_id, article LIMIT 5";
		$list_trans = orm_get_list($q);
		return $list_trans;
	}

	public function get_user($user_id){
		$q = "SELECT user_id, site_id, user_code, quota_additional, quota_remaining, user_category";
		$q.= " FROM ms_user WHERE 1";
		$q.= " AND user_id = ".replace_quote($user_id);
		$q.= " AND status = 1";
		$user = orm_get($q);
		return $user;
	}

	public function get_list_arttpo_trans_out(){
		// movement type ubah jadi 201 (sementara 25 July 2019)
		$q = "SELECT a.transaction_id, a.site_id, a.user_id, a.movement_type, a.article, a.customer_article, 
				a.description, sum(a.conversion_value) as total, a.qty, a.value, a.status_in_out, a.reason_id, 
				a.is_job_order, a.is_job_artpo, b.user_category, b.quota_remaining FROM " . $this->table . " a 
				LEFT JOIN ms_user b ON a.user_id = b.user_id 
				WHERE 1 AND a.status = 1 AND a.is_job_order = 1 
				AND a.is_job_artpo = 0 AND a.movement_type = '201' 
				GROUP BY transaction_id, site_id, article LIMIT 75";
		$list_trans = orm_get_list($q);
		return $list_trans;
	}

	public function get_list_order_trans_in(){
		$q = "SELECT a.transaction_id, a.site_id, a.user_id, a.movement_type, a.article, a.customer_article, 
				a.description, sum(a.conversion_value) as total, a.qty, a.value, a.status_in_out, a.reason_id, 
				a.is_job_order, a.is_job_artpo, a.status_message, b.user_category, b.quota_remaining 
				FROM " . $this->table . " a 
				LEFT JOIN ms_user b ON a.user_id = b.user_id 
				WHERE 1 AND a.status = 1 AND a.movement_type like '1%' 
				AND a.is_job_order = 0 
				GROUP BY transaction_id, site_id, article LIMIT 5";
		$list_trans = orm_get_list($q);
		return $list_trans;
	}

	public function get_list_article_po($data){
		$q = "SELECT * FROM ms_article_po 
				WHERE status=1 AND site_id = '".$data->site_id."' 
				AND article = '".$data->article."' 
				AND remaining_qty > 0 
				ORDER BY po_created_date ASC";
		$list_po = orm_get_list($q);
		return $list_po;
	}

	public function get_total_price_order(){
		$get = $attr = $result = NULL;
		if (! empty($_GET)) $get = $_GET;
		
		$q = "SELECT SUM(t.price) as total_price FROM tr_transaction t WHERE 1 AND t.movement_type LIKE '2%'";

		if (isset($get['month']) && $get['month'] != '') {
			$q.= " AND MONTH(created_at) = ".$get['month'];
		}

		if (isset($get['year']) && $get['year'] != '') {
			$q.= " AND YEAR(created_at) = ".$get['year'];
		}

		if(isset($get['user_id']) && $get['user_id'] != ''){
			$user_id = json_decode($get['user_id']);
			if(count($user_id)>1){
				$user_id = implode(",", $user_id);
				$q.= " AND user_id IN (".$user_id.")";
			}
			else{
				$q.= " AND user_id = ".$user_id;
			}
		}

		if (isset($get['site_id']) && $get['site_id'] != '') {
			$q.= " AND site_id = ".$get['site_id'];
		}

		$data = orm_get($q,'total_price');
		$result['total_price'] = $data;
		echo json_encode($result);
		die;
    }
    
    // Fill all records empty status_in_out & value
	public function trigger_status_and_value()
	{
        $get = $attr = $result = NULL;
		if (! empty($_GET)) $get = $_GET;
        
        $update = "
        UPDATE tr_transaction tra
        SET tra.status_in_out = IF(tra.transaction_id LIKE '%.SO.%','out',IF(tra.transaction_id LIKE '%.RI.%','in',IF(tra.transaction_id LIKE '%.CC.%','cc',NULL)))
        WHERE tra.status_in_out IS NULL;";
        DB::statement($update);
        
        $update = "
        UPDATE tr_transaction tra
        SET tra.value = tra.qty * tra.conversion_value * tra.price
        WHERE `value` IS NULL;";
        DB::statement($update);
        
        if ($update) {
            $result['is_success'] = 1;
            $result['message'] = 'update transaction col status_in_out & value success';
        } else {
            $result['is_success'] = 0;
            $result['message'] = 'update failed';
        }

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

	/*
	*	Report Api For Transaction
	*/

	public function sales_order_per_site(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;
		// total order kls
		$q = 'SELECT COUNT(tr.transaction_id) AS total_order_kls
				FROM tr_transaction tr';
		$q.= ' WHERE 1 ';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

		$q.= ' AND tr.movement_type = "201" AND tr.art_source = 1';

		$total_order_kls = orm_get($q);

		// total order non kls
		$q = 'SELECT COUNT(tr.transaction_id) AS total_order_non_kls
				FROM tr_transaction tr';
		$q.= ' WHERE 1 ';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

		$q.= ' AND tr.movement_type = "201" AND tr.art_source = 2';

		$total_order_non_kls = orm_get($q);


		$total_order_kls = json_decode(json_encode($total_order_kls),1);
		$total_order_non_kls = json_decode(json_encode($total_order_non_kls),1);

		$result['data'] = ['total_order_kls' => $total_order_kls['total_order_kls'], 'total_order_non_kls' => $total_order_non_kls['total_order_non_kls']];

		echo json_encode($result); 
		die;
	}

	public function sales_order_qty_per_site(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		// total qty order kls
		$q = 'SELECT SUM(tr.conversion_value) AS total_qty_order_kls
				FROM tr_transaction tr';
		$q.= ' WHERE 1 ';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

		$q.= ' AND tr.movement_type = "201" AND tr.art_source = 1';

		$total_qty_order_kls = orm_get($q);

		// total qty order non kls
		$q = 'SELECT SUM(tr.conversion_value) AS total_qty_order_non_kls
				FROM tr_transaction tr';
		$q.= ' WHERE 1 ';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

		$q.= ' AND tr.movement_type = "201" AND tr.art_source = 2';

		$total_qty_order_non_kls = orm_get($q);

		$total_qty_order_kls = json_decode(json_encode($total_qty_order_kls),1);
		$total_qty_order_non_kls = json_decode(json_encode($total_qty_order_non_kls),1);

		$result['data'] = ['total_qty_order_kls' => $total_qty_order_kls['total_qty_order_kls'], 'total_qty_order_non_kls' => $total_qty_order_non_kls['total_qty_order_non_kls']];

		echo json_encode($result); 
		die;
	}

	public function sales_order_value_per_site(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		// total value order non kls
		$q = 'SELECT SUM(tr.qty*tr.price) AS total_value_order_kls
				FROM tr_transaction tr';
		$q.= ' WHERE 1 ';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

		$q.= ' AND tr.movement_type = "201" AND tr.art_source = 1';

		$total_value_order_kls = orm_get($q);

		// total value order non kls
		$q = 'SELECT SUM(tr.qty*tr.price) AS total_value_order_non_kls
				FROM tr_transaction tr';
		$q.= ' WHERE 1 ';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

		$q.= ' AND tr.movement_type = "201" AND tr.art_source = 2';

		$total_value_order_non_kls = orm_get($q);

		$total_value_order_kls = json_decode(json_encode($total_value_order_kls),1);
		$total_value_order_non_kls = json_decode(json_encode($total_value_order_non_kls),1);

		$result['data'] = ['total_value_order_kls' => $total_value_order_kls['total_value_order_kls'], 'total_value_order_non_kls' => $total_value_order_non_kls['total_value_order_non_kls']];

		echo json_encode($result); 
		die;
	}

	public function report_order_per_month(){
		$attr = $result = NULL;
		if(isset($_GET)) $attr = $_GET;
		$res = array();
		$temp_kls = array();
		$temp_nonkls = array();
		$res = array();
		// array month
		$arr_month = array(1,2,3,4,5,6,7,8,9,10,11,12);
		$q = '
			SELECT MONTH(tr.created_at) AS month, YEAR(tr.created_at) as year, sum(tr.price) AS total_order FROM tr_transaction tr
				WHERE tr.movement_type = "201"
				AND tr.art_source = 1';
		if(isset($attr['year']) && $attr['year'] != ''){
			$q.= ' AND YEAR(tr.created_at) = '.$attr['year'];
		} else {
			$q.= ' AND YEAR(tr.created_at) = YEAR(NOW())';
		}
		$q.= 'GROUP BY MONTH(tr.created_at), YEAR(NOW())';
		$kls = orm_get_list($q);

		$q = '
			SELECT MONTH(tr.created_at) AS month,  YEAR(tr.created_at) as year, sum(tr.price) AS total_order FROM tr_transaction tr
				WHERE tr.movement_type = "201"
				AND tr.art_source = 2';
		if(isset($attr['year']) && $attr['year'] != ''){
			$q.= ' AND YEAR(tr.created_at) = '.$attr['year'];
		} else {
			$q.= ' AND YEAR(tr.created_at) = YEAR(NOW())';
		}
		$q.= ' GROUP BY MONTH(tr.created_at), YEAR(NOW())';
		$nonkls = orm_get_list($q);

		$kls = json_decode(json_encode($kls),1);
		$nonkls = json_decode(json_encode($nonkls),1);

		if(is_array($kls) && count($kls) > 0){
			for($i=0; $i<count($kls); $i++){
				$temp_kls[$kls[$i]['month']] = $kls[$i]['total_order'];
			}
		}
		if(is_array($nonkls) && count($nonkls) > 0){
			for($i=0; $i<count($nonkls); $i++){
				$temp_nonkls[$nonkls[$i]['month']] = $nonkls[$i]['total_order'];
			}
		}

		// loop and check trx month
		for($i=1; $i<=count($arr_month); $i++){
			$monthName = date('M', mktime(0, 0, 0, $arr_month[$i-1], 10));
			if(isset($temp_kls[$i])){
				$res[$monthName.'_kls'] =  $temp_kls[$i];	
			} else {
				$res[$monthName.'_kls'] =  0;
			}
			if(isset($temp_nonkls[$i])){
				$res[$monthName.'_non_kls'] =  $temp_nonkls[$i];	
			} else {
				$res[$monthName.'_non_kls'] =  0;
			}
		}

		$result['data'] = $res;
		echo json_encode($result); 
		die;
	}

	public function report_top_order(){
		$attr = $result = $res = NULL;
		if(isset($_GET)) $attr = $_GET;

		$q = 'SELECT tr.article, count(tr.article) AS total_order
				FROM tr_transaction tr
				WHERE 1';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

		$q.= ' GROUP BY tr.article
				ORDER BY total_order DESC
				LIMIT 5';

		$data = orm_get_list($q);
		$data = json_decode(json_encode($data),1);

		// change data to [article => total_order] ex: ['10004896' => 1000]
		for($i=0; $i<count($data); $i++){
			$res[$data[$i]['article']] = $data[$i]['total_order'];
		}

		$result['data'] = $res;
		echo json_encode($result); die;
	}
	
	public function report_total_trx_user(){
		$attr = $result = $res = NULL;
		if(isset($_GET)) $attr = $_GET;

		$q = 'SELECT count(tr.transaction_id) as total_order
				FROM tr_transaction tr
				WHERE 1';

		if(isset($attr['site_id'])){
			if(is_array($attr['site_id'])){
				$q.= ' AND (';
				for($i=0;$i<count($attr['site_id']); $i++){
					$q.= ' tr.site_id = '.replace_quote($attr['site_id'][$i]);
					if($i != count($attr['site_id'])-1){
						$q.= ' OR ';
					}
				}
				$q.= ')';
			} else{
				$q.= ' AND tr.site_id = '.replace_quote($attr['site_id']);
			}
		}

		if(isset($attr['user_id']) && $attr['user_id'] != ''){
			$q.= ' AND tr.user_id = '.$attr['user_id'];
		}

		$data = orm_get($q);
		$data = json_decode(json_encode($data),1);

		$result['data'] = $data;
		echo json_encode($result); die;
	}

	public function __destruct()
	{
		// parent::__construct();
	}
	
}