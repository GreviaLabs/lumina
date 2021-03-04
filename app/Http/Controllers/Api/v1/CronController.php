<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Input;
use Validator;
use Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;

use DB;
use Request;

class CronController extends Controller {
	
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// parent::__construct();
		// $this->authToken();
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		echo "helloworld from CRON";
		die;
	}

	// Reset quota by user
	public function reset_quota_by_days()
	{
		// When reset days exceeded then reset quota each user
		// - table user: initial_quota will be restore by top_down / bottom_up

		echo "helloworld logdata from API";
		die;
	}

	// Reset quota by user
	public function update_quota_additional()
	{
		// $q = "SELECT * FROM ms_config";
		$get_site_id = "";
		
		$q = '
		SELECT * 
		FROM ms_user u
		WHERE u.chamber_sync_flag = "50" AND u.quota_additional > 0 AND LOWER(u.user_category) != "replenish" AND u.status = 1';
		$data = orm_get_list($q);
		
		if (! empty($data)) {
			foreach ($data as $rs) {
				$quota_remaining = $rs->quota_remaining;
				$q = "
				UPDATE ms_user u 
				SET u.quota_remaining = " . ($quota_remaining + $rs->quota_additional) . " , u.quota_additional = 0
				WHERE u.user_id = " . $rs->user_id;
				
				// insert log movement quota here
				$update = DB::statement($q);
			}
		}		
		
		echo "berhasil";
		die;
	}
	
	public function send_csv_poblanket_to_oms()
	{
		echo "helloworld logdata from API";
		die;
	}

	public function send_csv_order_to_magento()
	{
		echo "helloworld logdata from API";
		die;
    }
    
    /* 
     * JOBS
     * -----------------------
     * This will find all record in table article_po with no article , 
     * by lookup from table ms_article using identifier customer_article , 
     * and update column article
     */
	public function update_article_in_article_po()
	{
        $response = $q = $cron = NULL;

        // update article
        $q = "
        UPDATE ms_article_po po
        LEFT JOIN ms_article ar ON po.customer_article = ar.customer_article AND po.site_id = ar.site_id
        SET po.article = ar.article
        WHERE (po.article IS NULL OR po.article = '');
        ";
        $cron = DB::statement($q);

        // update customer_article
        $q = "
        UPDATE ms_article_po po
        LEFT JOIN ms_article ar ON po.article = ar.article AND po.site_id = ar.site_id
        SET po.customer_article = ar.customer_article
        WHERE (po.customer_article IS NULL OR po.customer_article = '');
        ";
        $cron = DB::statement($q);

        // update customer_article
        $q = "
        UPDATE ms_article_po po
        LEFT JOIN ms_article a ON po.article = a.article AND po.site_id = a.site_id
        SET po.customer_article = a.customer_article
        WHERE po.customer_article IS NULL AND a.customer_article IS NOT NULL;
        ";
        $cron = DB::statement($q);

        // update description
        $q = "
        UPDATE ms_article_po po
        LEFT JOIN ms_article a ON po.article = a.article AND po.site_id = a.site_id
        SET po.description = a.description
        WHERE po.description IS NULL AND a.description IS NOT NULL;
        ";
        $cron = DB::statement($q);

        // update customer_article_description
        $q = "
        UPDATE ms_article_po po
        LEFT JOIN ms_article a ON po.article = a.article AND po.site_id = a.site_id
        SET po.customer_article_description = a.customer_article_description
        WHERE po.customer_article_description IS NULL AND a.customer_article_description IS NOT NULL;
        ";
        $cron = DB::statement($q);
        
        if ($cron) 
            $response['message'] = 'success';
        else 
            $response['message'] = 'failed';

        echo json_encode($response);
		die;
	}

	public function log()
	{
        $listdata = $response_nodata = NULL;

        $response_nodata = array('message' => 'no data pending');
        $response_nodata = json_encode($response_nodata);

        // 1. check if any article logistic site pending data
        $q = "
        SELECT als.site_id, alsd.article, alsd.qty_receive_actual, alsd.actual_receive_quantity_for_art_po
        FROM tr_article_logistic_site als
        LEFT JOIN tr_article_logistic_site_detail alsd ON alsd.outbound_delivery = als.outbound_delivery 
        WHERE actual_receive_quantity_for_art_po != 0
        ";

        $listdata = DB::statement($q);

        if (empty($listdata)) {
            echo $response_nodata;die;
        }

        
        

    }

    // -- jobs insert tr_article_stock where ms_article exist
    public function insert_ignore_article_stock_empty() {
        $q = "
        INSERT IGNORE INTO ms_article_stock (article_id,site_id,article,customer_article,description,customer_article_description, chamber_sync_flag,created_at,created_by)
        SELECT a.article_id,a.site_id,a.article,a.customer_article,a.description,a.customer_article_description,10,now(),'cron'
        FROM ms_article a
        LEFT JOIN ms_article_stock ar USING(site_id,article)
        WHERE 1 AND a.`status` = 1
        ";

        $update = DB::statement($q);
        $str['message'] = 'query failed';
        if ($update) {
            $str['message'] = 'query success';
        }
        echo json_encode($str);die;
    }

    // -- jobs update col description table tr_article_logistic_site_detail from table article
    public function update_logistic_site_detail_description() {
        $q = "
        UPDATE tr_article_logistic_site_detail alsd
        LEFT JOIN tr_article_logistic_site als ON als.outbound_delivery 
        LEFT JOIN ms_article a ON a.article = alsd.article AND a.site_id = als.site_id
        SET alsd.customer_article = a.customer_article, alsd.description = a.description
        WHERE 1 AND a.`status` = 1 AND (alsd.description IS NULL OR alsd.customer_article IS NULL)";

        $update = DB::statement($q);
        $str['message'] = 'query failed';
        if ($update) {
            $str['message'] = 'query success';
        }
        echo json_encode($str);die;
    }
	
	public function trigger_safety_stock()
	{
        $get = $attr = NULL;

        if ($_GET) $get = $_GET; 

        $q = "SELECT a.site_id, a.article, a.description , a.safety_stock, s.stock_qty, s.stock_dashboard, s.stock_damaged, s.stock_disc, s.stock_cc, a.price
		FROM ms_article a
		LEFT JOIN ms_article_stock s USING(article_id)
		WHERE 1 AND a.`status` = 1
		-- AND 
        HAVING safety_stock > stock_dashboard";
        
        if (isset($attr['site_id']) && $attrp['site_id'] != '') {
            $q.= " AND site_id = " .replace_quote($attr['site_id']);
        }
		
		$listdata = orm_get_list($q,'array');
        if (empty($listdata)) $listdata = NULL; 
        echo json_encode($listdata);
        die;
    }
    
	public function report_articlepo_job_error()
	{
        $q = $listdata = NULL;

        $q = "
        SELECT * 
		FROM tr_transaction t
		WHERE t.is_job_artpo = 0
		AND (t.movement_type = 201)
        ";
		
		$listdata = orm_get_list($q,'array');
        if (empty($listdata)) $listdata = NULL; 
        echo json_encode($listdata);
        die;
	}

	public function update_article_source_trx(){
		$q = $result = NULL;
		$q = "
			UPDATE tr_transaction tr
			LEFT JOIN ms_article art ON art.article = tr.article AND art.site_id = tr.site_id
			SET tr.art_source = art.art_source
			WHERE tr.art_source IS NULL";

		$update = DB::statement($q);

		$result['message'] = 'Update Article Source Transaction Failed!';
		if($update){
			$result['message'] = 'Update Article Source Transaction Success!';
		}
		echo json_encode($result); die;
	}

	public function update_article_source_gr(){
		$q = $result = NULL;
		$q = "
			UPDATE tr_article_logistic_site_detail alsd
			LEFT JOIN ms_article art ON art.article = alsd.article
			LEFT JOIN tr_article_logistic_site als ON art.site_id = als.site_id
			SET alsd.art_source = art.art_source
			WHERE alsd.art_source IS NULL";

		$update = DB::statement($q);

		$result['message'] = 'Update Article Source GR Failed!';
		if($update){
			$result['message'] = 'Update Article Source GR Success!';
		}
		echo json_encode($result); die;
	}

	public function update_article_source_kitting(){
		$q = $result = NULL;
		$q = "
			UPDATE tr_prepack_bundling_detail pbd
			LEFT JOIN ms_article art ON art.article = pbd.article
			LEFT JOIN tr_prepack_bundling_header pbh ON art.site_id = pbh.site_id
			SET pbd.art_source = art.art_source
			WHERE pbd.art_source IS NULL";

		$update = DB::statement($q);

		$result['message'] = 'Update Article Source Kitting Failed!';
		if($update){
			$result['message'] = 'Update Article Source Kitting Success!';
		}
		echo json_encode($result); die;
	}

	// update all transaction order with no ref_sc_order_id or ref_so_sap
	// get data from sc_order
	// Deprecated due to 
	// public function update_order_from_sc(){
		// $q = $result = NULL;
		// $q = '
			// UPDATE tr_transaction tr
				// LEFT JOIN (
					// SELECT tr.transaction_id, sc.sc_order_id, sc.so_sap FROM tr_transaction tr
						// LEFT JOIN tr_article_po_history apoh ON tr.transaction_id = apoh.reference
						// LEFT JOIN ms_article_po apo ON apoh.article_po_id = apo.article_po_id 
							// AND tr.article = apo.article 
							// AND tr.site_id = apo.site_id
						// LEFT JOIN tr_sc_order sc ON apo.po_blanket_number = sc.po_number 
							// AND tr.site_id = sc.site_id
						// LEFT JOIN tr_sc_order_detail scd ON sc.sc_order_id = scd.sc_order_id 
							// AND tr.article = scd.article
						// WHERE tr.movement_type = "201"
							// AND tr.status_in_out = "out"
							// AND (tr.ref_sc_order_id IS NULL
								// OR tr.ref_so_sap IS NULL)
						// GROUP BY tr.transaction_id, tr.article, tr.site_id
				// ) tmp ON tr.transaction_id = tmp.transaction_id
				// SET tr.ref_sc_order_id = tmp.sc_order_id, tr.ref_so_sap = tmp.so_sap';

		// $update = DB::statement($q);

		// $result['message'] = 'Update Order SC Order And SO SAP Failed!';
		// if($update){
			// $result['message'] = 'Update Order SC Order And SO SAP Success!';
		// }
		// echo json_encode($result); die;
	// }

	// update remaining_qty po
	// get data from GR
	// due to changing flow 04/10/2019
	// update po from GR dashboard
	public function update_remaining_qty_po(){
		$message = $q = $result = NULL;
		// get data gr
		$list_gr = $this->get_list_gr();
		$list_gr = json_decode($list_gr,1);

		// start execution
		if(count($list_gr) > 0){
			for($i=0; $i<count($list_gr); $i++){
				// get data po
				$list_po = $this->get_list_po($list_gr[$i]['article'], $list_gr[$i]['site_id']);
				$list_po = json_decode($list_po,1);

				// set var for gr balance_qty_for_po
				$balance = $list_gr[$i]['balance_qty_for_po'];
				if(count($list_po) == 0){
					continue;
				} else{
					$j = 0;
					while($j < count($list_po) && $balance > 0){
						if($balance < $list_po[$j]['open_qty']){
							$q = 'UPDATE ms_article_po SET open_qty = open_qty - '.(int)$balance.', remaining_qty = remaining_qty + '.(int)$balance.' WHERE article_po_id = '.$list_po[$j]['article_po_id'];
							DB::statement($q);
							$qgr = 'UPDATE tr_article_logistic_site_detail SET balance_qty_for_po = 0 WHERE article_logistic_site_detail_id = '.$list_gr[$i]['article_logistic_site_detail_id'];
							DB::statement($qgr);
							$balance = 0;
						} else{
							$q = 'UPDATE ms_article_po SET remaining_qty = remaining_qty + open_qty, open_qty = 0 WHERE article_po_id = '.$list_po[$j]['article_po_id'];
							DB::statement($q);
							$balance = (int)$balance - (int)$list_po[$j]['open_qty'];
							$qgr = 'UPDATE tr_article_logistic_site_detail SET balance_qty_for_po = balance_qty_for_po - '.(int)$list_po[$j]['open_qty'].' WHERE article_logistic_site_detail_id = '.$list_gr[$i]['article_logistic_site_detail_id'];
							DB::statement($qgr);
						}
						$qpo = 'INSERT INTO tr_article_po_history(article_po_id, po_usage_qty, po_remaining_qty, po_created_date, status_in_out, reference, created_at, created_by, created_ip)';
						if($balance > 0){
							$qpo.= ' VALUES('.replace_quote($list_po[$j]['article_po_id']).','.$list_po[$j]['open_qty'].','.((int)$list_po[$j]['open_qty'] + (int)$list_po[$j]['remaining_qty']).','.replace_quote($list_po[$j]['po_created_date']).',"in",'.replace_quote($list_gr[$i]['article_logistic_site_detail_id']).',NOW(),"System","System")';
						} else{
							$qpo.= ' VALUES('.replace_quote($list_po[$j]['article_po_id']).','.$list_gr[$i]['balance_qty_for_po'].','.((int)$list_gr[$i]['balance_qty_for_po'] + (int)$list_po[$j]['remaining_qty']).','.replace_quote($list_po[$j]['po_created_date']).',"in",'.replace_quote($list_gr[$i]['article_logistic_site_detail_id']).',NOW(),"System","System")';
						}
						DB::statement($qpo);
						$message = 'Success';
						$j++;
					}
				}
			}
		} else{
			$message = 'There is No Pending PO Qty From GR';
		}
		$result['message'] = $message;
		return json_encode($result);
	}

	// get list gr
	public function get_list_gr(){
		$q = $result = NULL;
		$q = 'SELECT alsd.article_logistic_site_detail_id, alsd.outbound_delivery, alsd.article, alsd.customer_article, als.site_id, alsd.balance_qty_for_po
				 FROM tr_article_logistic_site_detail alsd
				 LEFT JOIN tr_article_logistic_site als ON alsd.outbound_delivery = als.outbound_delivery
				 WHERE alsd.balance_qty_for_po > 0 AND alsd.status_message != "new" AND als.status_message != "new"
				 	AND als.status_in_out = "in"';
		$q.= ' ORDER BY als.created_at ASC, alsd.article_logistic_site_detail_id ASC';
		$result = orm_get_list($q);

		return json_encode($result);
	}

	// get list po
	public function get_list_po($article, $site_id){
		$q = $result = NULL;
		$q = 'SELECT apo.article_po_id, apo.article, apo.site_id, apo.open_qty, apo.remaining_qty, apo.po_created_date
				 FROM ms_article_po apo';
		$q.= ' WHERE 1 ';
		$q.= ' AND apo.open_qty > 0';
		$q.= ' AND apo.article = '.replace_quote($article);
		$q.= ' AND apo.site_id = '.replace_quote($site_id);
		$q.= ' ORDER BY apo.po_created_date ASC';
		$result = orm_get_list($q);

		return json_encode($result);
	}

	// new update sc_order_id to tr_transaction
	public function update_sc_so_to_trx(){
		$result['is_success'] = 1;
		$result['message'] = NULL;
		// get list sc_order
		$sc_order_data = $this->get_list_sc();
		$sc_order_data = json_decode($sc_order_data,1);

		if(empty($sc_order_data)){
			$result['message'] = "There's No SC Order Data";
		}

		if(isset($result['message'])){
			$result['is_success'] = 0;
		}

		/*Start Operation*/
		// update sc_order
		for($i=0; $i<count($sc_order_data); $i++){
			// get list trx (article,site_id,po,is_sc_order,is_so_sap)
			// is_sc_order = 1 (get sc_order IS NULL)
			// is_so_sap = 1 (get sc_order IS NULL)
			$list_trx = $this->get_list_trx($sc_order_data[$i]['article'],$sc_order_data[$i]['site_id'],$sc_order_data[$i]['po_number'],1,NULL);
			$list_trx = json_decode($list_trx,1);
			
			if(empty($list_trx)) continue;
			$k = 0;
			// balance_issue_qty
			$issue_qty = $sc_order_data[$i]['balance_issue_qty'];
			while($issue_qty > 0){
				if(empty($list_trx[$k])) break;
				if($list_trx[$k]['conversion_value'] <= $issue_qty){
					$qt = 'UPDATE tr_transaction tr SET tr.ref_sc_order_id = '.replace_quote($sc_order_data[$i]['sc_order_id']);
					$qt.= ' WHERE 1';
					$qt.= ' AND tr.transaction_id = '.replace_quote($list_trx[$k]['transaction_id']);
					$qt.= ' AND tr.article = '.replace_quote($list_trx[$k]['article']);
					$qt.= ' AND tr.site_id = '.replace_quote($list_trx[$k]['site_id']);
					DB::statement($qt);
					
					$qs = 'UPDATE tr_sc_order_detail scd SET scd.balance_issue_qty = scd.balance_issue_qty - '.$list_trx[$k]['conversion_value'];
					$qs.= ' WHERE 1';
					$qs.= ' AND scd.sc_order_id = '.replace_quote($sc_order_data[$i]['sc_order_id']);
					$qs.= ' AND scd.article = '.replace_quote($sc_order_data[$i]['article']);
					DB::statement($qs);
					
					$issue_qty = (int)$issue_qty - (int)$list_trx[$k]['conversion_value'];
					
					$result['is_success'] = 1;
					$result['message'] = "Update Success!";
				}
				$k++;
			}
		}

		return json_encode($result);
	}

	public function get_list_sc(){
		$q = 'SELECT sc.sc_order_id, sc.po_number, sc.site_id, scd.article, scd.issue_qty, scd.balance_issue_qty FROM tr_sc_order_detail scd
				LEFT JOIN tr_sc_order sc ON scd.sc_order_id = sc.sc_order_id
				LEFT JOIN ms_article_po apo ON sc.po_number = apo.po_blanket_number AND scd.article = apo.article AND sc.site_id = apo.site_id
				WHERE scd.balance_issue_qty > 0
				ORDER BY scd.sc_order_id, apo.po_created_date ASC';
		$data = orm_get_list($q);
		return json_encode($data);
	}

	public function get_list_sc_so_sap(){
		$q = 'SELECT sc.sc_order_id, sc.po_number, sc.site_id, sc.so_sap FROM tr_sc_order sc
				WHERE sc.so_sap IS NOT NULL
				ORDER BY sc.sc_order_id ASC';
		$data = orm_get_list($q);
		return json_encode($data);
	}

	public function get_list_trx($article, $site_id, $po_number, $is_sc_order, $is_so_sap=NULL){
		$q = 'SELECT tr.transaction_id, apo.po_blanket_number, tr.site_id, tr.article, tr.qty, tr.conversion_value, tr.is_job_artpo
				FROM tr_article_po_history apoh
				LEFT JOIN tr_transaction tr ON apoh.reference = tr.transaction_id
				LEFT JOIN ms_article_po apo ON apoh.article_po_id = apo.article_po_id
				WHERE tr.status_in_out = "out" AND tr.is_job_artpo = 1';
		$q.= ' AND tr.article = '.replace_quote($article);
		$q.= ' AND tr.site_id = '.replace_quote($site_id);
		$q.= ' AND apo.po_blanket_number = '.replace_quote($po_number);
		if($is_sc_order == 1){
			$q.= ' AND tr.ref_sc_order_id IS NULL';
		}
		// $is_so_sap depracated cause: wrong query, just need 1 column
		if($is_so_sap == 1){
			$q.= ' AND tr.ref_so_sap IS NULL';
		}
		$q.= ' ORDER BY tr.transaction_id, apo.po_created_date, apoh.created_at ASC';
		$data = orm_get_list($q);
		return json_encode($data);
	}

	// new update so_sap to tr_transaction
	public function update_trx_so_sap(){
		$update = $result = NULL;

		$result['message'] = NULL;
		$result['is_success'] = 1;

		$q = 'UPDATE tr_transaction tr
				LEFT JOIN tr_sc_order sc ON sc.sc_order_id = tr.ref_sc_order_id
				SET tr.ref_so_sap = sc.so_sap
				WHERE tr.is_job_artpo = 1 
					AND tr.ref_sc_order_id IS NOT NULL
					AND sc.so_sap IS NOT NULL 
					AND tr.ref_so_sap IS NULL 
					AND tr.status_in_out = "out"';
		$update = DB::statement($q);

		if($update){
			$result['message'] = 'SO SAP Successfuly Updated!';
		} else{
			$result['is_success'] = 0;
			$result['message'] = 'SO SAP Failed to Update!';
		}
		
		return json_encode($result);
	}
    
    // public function log()
	// {
		// echo "helloworld logdata from API";
		// die;
	// }

	// public function log()
	// {
		// echo "helloworld logdata from API";
		// die;
	// }
	
	// public function log()
	// {
		// echo "helloworld logdata from API";
		// die;
	// }
	
	// public function log()
	// {
		// echo "helloworld logdata from API";
		// die;
	// }
	
}