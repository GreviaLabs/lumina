<?php

namespace App\Http\Controllers\Report\v1;

use App\Http\Controllers\Controller;

use Input;
use Validator;
use Session;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Request;

// use Request;
use DB;

class ReportPurchasingController extends ReportController {

	/*
	|--------------------------------------------------------------------------
	| Report Purchasing Controller
	|--------------------------------------------------------------------------
	|
	| Api Report for controller handler created by Harvei on Tuesday 06 August 2019 13:13
	| controller as you wish. It is just here to get your app started!
	|
    */
	
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

	// report for articleBelowSafetyStock
	public function article_qty_below_safety_stock(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT a.site_id, COUNT(a.article) AS total
				FROM ms_article a
				LEFT JOIN ms_article_stock ars USING(article_id)
				WHERE 1';
		$q.= ' AND a.safety_stock > ars.stock_dashboard';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND a.site_id = '.replace_quote($attr['site_id']);
        }
        
        $q.= ' GROUP BY site_id';

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
	// report for articleBelowSafetyStock
	public function article_value_below_safety_stock(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT a.site_id, SUM(a.price) AS total
				FROM ms_article a
				LEFT JOIN ms_article_stock ars USING(article_id)
				WHERE 1';
		$q.= ' AND a.safety_stock > ars.stock_dashboard';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND a.site_id = '.replace_quote($attr['site_id']);
        }
        
        $q.= ' GROUP BY site_id';

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
	}

    // harusnya gapake lagi
	// report for numberOfPO
	public function number_of_po()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT * FROM ms_article_po WHERE 1';

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

		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
		}
        
        $result['total_rows'] = count(orm_get_list($q));

		$data = orm_get_list($q);
        $result['data'] = $data;
        
        echo json_encode($result); 
		die;
    }
    
    // report for numberOfPO
	public function report_po_fulfill_unfulfill()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
        // $q = 'SELECT * FROM ms_article_po WHERE 1';
        
        $result['total_all_po'] = $result['total_active_po'] = $result['total_inactive_po'] = $result['total_fulfill_po'] = $result['total_unfulfill_po'] = 0;

        // ----------------------------------------------------------------------------------
        // -- all po
        $q = '
        SELECT COUNT(apo.article_po_id) AS total_all_po
        FROM ms_article_po apo
        WHERE 1';

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
        
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
        }

		if (isset($attr['status']) && in_array($attr['status'],array(-1,0,1))) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
        }
        // debug($q,1);

        $result['total_all_po'] = orm_get($q,'total_all_po','array');
        // debug($result,1);

        $q = NULL;

        // ----------------------------------------------------------------------------------
        // -- active po
        $q = '
        SELECT COUNT(apo.article_po_id) AS total_active_po
        FROM ms_article_po apo
        WHERE 1 AND remaining_qty > 0';

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
        
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
        }

		if (isset($attr['status']) && in_array($attr['status'],array(-1,0,1))) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
        }
        // debug($q,1);

        $result['total_active_po'] = orm_get($q,'total_active_po','array');
        // debug($result,1);

        $q = NULL;

        // ----------------------------------------------------------------------------------
        // -- inactive po
        $q = '
        SELECT COUNT(apo.article_po_id) AS total_inactive_po
        FROM ms_article_po apo
        WHERE 1 AND open_qty = 0 AND remaining_qty = 0';

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
        
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
        }

		if (isset($attr['status']) && in_array($attr['status'],array(-1,0,1))) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
        }
        // debug($q,1);

        $result['total_inactive_po'] = orm_get($q,'total_inactive_po','array');
        // debug($result,1);

        $q = NULL;

        // ----------------------------------------------------------------------------------
        // -- fulfill po
        $q = '
        SELECT COUNT(apo.article_po_id) AS total_fulfill_po
        FROM ms_article_po apo
        WHERE apo.open_qty = 0 AND apo.remaining_qty = 0';

		if(isset($attr['start_date']) && $attr['start_date'] != ''){
			if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
			} else $q.= ' AND DATE(created_at) like '.replace_quote($attr['start_date']);
		} elseif(isset($attr['end_date']) && $attr['end_date'] != ''){
			$q.= ' AND DATE(created_at) like '.replace_quote($attr['end_date']);
        }
        
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
        }

		if (isset($attr['status']) && in_array($attr['status'],array(-1,0,1))) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
        }
        // debug($q,1);
        
        
        $result['total_fulfill_po'] = orm_get($q,'total_fulfill_po','array');
        // debug($result,1);
        if (isset($result['total_all_po']) && isset($result['total_fulfill_po'])) $result['total_unfulfill_po'] = $result['total_all_po'] - $result['total_fulfill_po'];
        
        echo json_encode($result); 
		die;
    }
    
    public function report_total_below_po() {
        $q = "
        SELECT COUNT(a.article_id) AS total_article_below_po, SUM(s.stock_dashboard) AS total_qty_below_po, SUM(s.stock_dashboard * a.price) AS total_value_below_po
        FROM ms_article a
        LEFT JOIN ms_article_stock s ON s.site_id = a.site_id AND s.article = a.article
        LEFT JOIN ms_article_po po ON po.site_id = a.site_id AND (po.article = a.article OR po.customer_article = a.customer_article)
        WHERE s.stock_dashboard < a.safety_stock
        ";

        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
			} else $q.= ' AND DATE(created_at) like '.replace_quote($attr['start_date']);
		} elseif(isset($attr['end_date']) && $attr['end_date'] != ''){
			$q.= ' AND DATE(created_at) like '.replace_quote($attr['end_date']);
        }
        
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
        }

		if (isset($attr['status']) && in_array($attr['status'],array(-1,0,1))) {
			$q.= ' AND a.status = '.$attr['status'];
        } else {
			$q.= ' AND a.status != -1';
        }

        $result = orm_get($q,NULL,'array');
        
        echo json_encode($result); 
		die;
    }

    public function report_article_value_qty_kls_nonkls() {
        
        $qkls = $qnonkls = $q = NULL;
        $qkls = "
        SELECT SUM(s.stock_dashboard) total_qty, SUM(a.price) total_value
        FROM ms_article a
        LEFT JOIN ms_article_stock s ON s.site_id = a.site_id AND s.article = a.article
        WHERE a.art_source = 1";

        $qnonkls = "
        SELECT IFNULL(SUM(s.stock_dashboard),0) total_qty, IFNULL(SUM(a.price),0) total_value
        FROM ms_article a
        LEFT JOIN ms_article_stock s ON s.site_id = a.site_id AND s.article = a.article
        WHERE a.art_source = 2";

        $q = "";
        if(isset($attr['start_date']) && $attr['start_date'] != ''){
			if(isset($attr['end_date']) && $attr['end_date'] != ''){
				$q.= ' AND DATE(created_at) BETWEEN '.replace_quote($attr['start_date']).' AND '.replace_quote($attr['end_date']);
			} else $q.= ' AND DATE(created_at) like '.replace_quote($attr['start_date']);
		} elseif(isset($attr['end_date']) && $attr['end_date'] != ''){
			$q.= ' AND DATE(created_at) like '.replace_quote($attr['end_date']);
        }
        
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND a.site_id = '.replace_quote($attr['site_id']);
        }

		if (isset($attr['status']) && in_array($attr['status'],array(-1,0,1))) {
			$q.= ' AND a.status = '.$attr['status'];
        } else {
			$q.= ' AND a.status != -1';
        }

        $qkls .= $q;
        $qnonkls .= $q;
        
        $reskls = orm_get($qkls,NULL,'array');
        $resnonkls = orm_get($qnonkls,NULL,'array');

        $result['qty_kls'] = $reskls['total_qty'];
        $result['value_kls'] = $reskls['total_value'];

        $result['qty_nonkls'] = $resnonkls['total_qty'];
        $result['value_nonkls'] = $resnonkls['total_value'];
        
        echo json_encode($result); 
		die;
    }

	public function get_list()
	{
		$attr = $result = $filter = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT tr.transaction_id, tr.site_id, tr.qty, tr.value, tr.status_in_out, tr.wo_wbs, tr.article, tr.customer_article, tr.created_at, tr.updated_at, tr.status, tr.user_id,';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE tr.created_by = u.user_id), tr.created_by) as creator,';
		$q.= ' COALESCE((SELECT u.user_code FROM ms_user u WHERE tr.updated_by = u.user_id), tr.updated_by) as editor';
		$q.= ' FROM tr_transaction tr WHERE 1';
		$q.= ' HAVING 1';
		
		if (isset($attr['filter']) && $attr['filter'] != '') 
		{
			// validate_column
			$filter = validate_column($this->list_column, $attr);
			// $filter = $attr['filter'];

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
			// $q.= ' OR site_address LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR method_calc LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR logo_file_name LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
		
		if (isset($attr['user_id']) && $attr['user_id'] > 0) {
			$q.= ' AND user_id =  '.$attr['user_id'];
        }

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND site_id =  '.replace_quote($attr['site_id']);
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
			$q.= ' ORDER BY transaction_id DESC';
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

	public function fulfillment_po()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT site_id, COUNT(*) as total FROM ms_article_po apo
				WHERE 1
				AND apo.open_qty = 0
				AND apo.remaining_qty = 0';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
        }
        
        $q.= ' GROUP BY site_id';

        $article_fulfilled = orm_get($q);

        $q = 'SELECT site_id, COUNT(*) as total FROM ms_article_po apo
				WHERE 1';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
        }
        
        $q.= ' GROUP BY site_id';

        $total_article = orm_get($q);

        $result['article_fulfilled'] = $result['total_article'] = "0";

        if (isset($article_fulfilled->total)) $result['article_fulfilled'] = $article_fulfilled->total;
        if (isset($total_article->total)) $result['total_article'] = $total_article->total;

        echo json_encode($result);
        die;

	}

	public function fulfillment_qty_po()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;

		$q = 'SELECT site_id, sum(apo.po_blanket_qty) as total FROM ms_article_po apo
				WHERE 1
				AND apo.open_qty = 0
				AND apo.remaining_qty = 0';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
        }
        
        $q.= ' GROUP BY site_id';

        $qty_fulfilled = orm_get($q,NULL,'array');

        $q = 'SELECT site_id, sum(apo.po_blanket_qty) as total FROM ms_article_po apo
				WHERE 1';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
        }
        
        $q.= ' GROUP BY site_id';

        $total_qty = orm_get($q,NULL,'array');

        if (isset($qty_fulfilled['total'])) $result['qty_fulfilled'] = $qty_fulfilled['total'];
        if (isset($total_qty['total'])) $result['total_qty'] = $total_qty['total'];

        echo json_encode($result);
        die;

	}

	public function article_need_create_po()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;

		$q = '
		SELECT COUNT(apo.article_po_id) 
		FROM ms_article_po apo
		LEFT JOIN ms_article ar ON apo.site_id = ar.site_id AND ar.article = apo.article
		WHERE 1 AND apo.open_qty = 0 AND apo.remaining_qty = 0';

		if (isset($attr['site_id']) && $attr['site_id'] != '') {
			$q.= ' AND apo.site_id = '.replace_quote($attr['site_id']);
        }
		
		if (isset($attr['start_date']) && $attr['start_date'] != '' && isset($attr['end_date']) && $attr['end_date'] != '') {
			$q.= ' AND apo.created_at BETWEEN '.replace_quote(date('Y-m-d H:i:s',strtotime($attr['start_date']))) . ' AND ' . replace_quote(date('Y-m-d H:i:s',strtotime($attr['end_date'])));
        }
        
        $total_qty = orm_get($q);

        $result['qty_fulfilled'] = $qty_fulfilled->total;
        $result['total_qty'] = $total_qty->total;

        echo json_encode($result);
        die;

	}
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}