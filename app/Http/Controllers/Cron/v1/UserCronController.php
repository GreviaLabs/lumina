<?php

namespace App\Http\Controllers\Cron\v1;

use App\Http\Controllers\Controller;

use Input;
use Validator;
use Session;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Request;

// use Request;
use DB;

use App\Models\CompanyModel;

class UserCronController extends CronController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'ms_user';
    public $primary_key = 'user_id';
	public $list_column = array('user_id', 'site_id', 'parent_user_id', 'level_id','user_code', 
	'firstname', 'lastname', 'quota_initial', 'quota_additional', 'quota_remaining', 'job_title', 
	'division_id','attribute','attribute_value', 'email', 'user_category', 'password', 
	'counter_wrong_pass', 'status_lock', 'locked_time', 'reset_by', 'reset_time', 'reset_token', 
	'reset_token_expired', 'chamber_sync_flag', 'status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
    public $list_required_column = array('email');
	
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

    // ======================================================================================================
    // Decrease Quota Parent Start
    // by harvei on 28 jun 2019
	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function deduct_parent_quota(){
		$attr = $result = NULL;
		if(isset($_GET) && !empty($_GET)) $attr = $_GET;

		$q = 'SELECT u.user_id, u.parent_user_id, u.quota_remaining FROM ms_user u';
		$q.= ' WHERE u.user_id = '.$attr['user_id'].' AND LOWER(u.user_category) != "replenish" AND u.status = 1';
		$data = orm_get($q);

		// deduct current user quota
		$qu = 'UPDATE ms_user SET quota_remaining = quota_remaining - '.$attr['qty'];
		$qu.= ', chamber_sync_flag = 20 WHERE user_id = '.$data->user_id;
		DB::statement($qu);

		$parent = $this->deduct_quota($data, $attr['qty']);

		return $parent;
	}

	public function deduct_quota($data, $qty){
		$return['message'] = 'Init';
		$return['is_success'] = 0;
		// get user parent
		if(isset($data) && count($data)>0){
			$q = 'SELECT u.user_id, u.site_id, u.parent_user_id, u.quota_remaining FROM ms_user u';
			$q.= ' WHERE u.user_id = '.$data->parent_user_id.' AND LOWER(u.user_category) != "replenish" AND u.status = 1';
			$parent = orm_get($q);

			$qu = 'UPDATE ms_user SET quota_remaining = quota_remaining - '.$qty;
			$qu.= ', chamber_sync_flag = 20 WHERE user_id = '.$parent->user_id;
			DB::statement($qu);
			if(isset($parent) && count($parent)>0){
				if(isset($parent->parent_user_id)){
					$this->deduct_quota($parent, $qty);
				}
			}
			$return['message'] = 'Parent Quota Deducted!';
			$return['is_success'] = 1;
		}

		return $return;
    }
    // Decrease Quota Parent End 
    // ======================================================================================================
    
    // remove parent
    // ======================================================================================================
    // Decrease Quota Child Start By Harvei

    public function deduct_child_quota(){
		$attr = $result = NULL;
		if(isset($_GET) && !empty($_GET)) $attr = $_GET;

		$q = 'SELECT u.user_id, u.parent_user_id, u.quota_remaining FROM ms_user u';
		$q.= ' WHERE u.user_id = '.$attr['user_id'].' AND LOWER(u.user_category) != "replenish" AND u.status = 1';
		$data = orm_get($q);

		$q = 'UPDATE ms_user SET quota_remaining = quota_remaining - '.$attr['qty'];
		$q.= ', chamber_sync_flag = 20 WHERE user_id = '.$attr['user_id'];
		DB::statement($q);
		$child = $this->deduct_quota_child($data->user_id, $attr['qty']);
		return $child;
    }

    public function deduct_quota_child($data,$qty, $qtyb=NULL){
    	$return['message'] = 'Init';
		$return['is_success'] = 0;
		$temp_qty = $deduct_temp = 0;
		$arr_user = array();
		// get user child
		if(isset($data)){
			$q = 'SELECT u.user_id, u.site_id, u.parent_user_id, u.quota_remaining FROM ms_user u';
			$q.= ' WHERE u.parent_user_id = '.$data.' AND LOWER(u.user_category) != "replenish" AND u.status = 1 AND u.quota_remaining > 0';
			$child = orm_get_list($q);
			if(empty($child) || count($child) == 0) {
				return $return;
			}
			$total_child = count($child);
			// echo "<hr/>total child: ".$total_child." ";die;
			for($i=0; $i<$total_child; $i++){
				$temp_qty = $qty / $total_child;
				if (strpos($temp_qty,'.')) {
					// round up on last round
					if ($i == ($total_child - 1)) {
						$temp_qty = ceil($temp_qty);
					} else {
						$temp_qty = floor($temp_qty);
					}
				}
				if(!isset($qtyb[$child[$i]->user_id])) $qtyb[$child[$i]->user_id] = 0;
				if($child[$i]->quota_remaining < $temp_qty){
					// if quota user tidak cukup then set quota user to 0 and tampung sisa potongnya
					$qtyb[$child[$i]->user_id] += ($child[$i]->quota_remaining);
					$qu = 'UPDATE ms_user SET quota_remaining = 0';
					$qu.= ', chamber_sync_flag = 20 WHERE user_id = '.$child[$i]->user_id;
					DB::statement($qu);
					$deduct_temp = $deduct_temp + $temp_qty - ($child[$i]->quota_remaining);
				} else{
					// jika quota user cukup
					$qtyb[$child[$i]->user_id] = $temp_qty + $qtyb[$child[$i]->user_id];
					$qu = 'UPDATE ms_user SET quota_remaining = quota_remaining - '.$temp_qty;
					$qu.= ', chamber_sync_flag = 20 WHERE user_id = '.$child[$i]->user_id;
					DB::statement($qu);
					// $this->deduct_quota_child($child[$i], $temp_qty);
				}
				// untuk akomodir jika ada sisa potong yang ditampung
				if($i==$total_child-1){
					if($deduct_temp>0){
						$this->deduct_quota_child($data, $deduct_temp, $qtyb);
						$qtyb = NULL;
					} else 
					break;
				}
			}
			if(isset($qtyb)){
				foreach ($qtyb as $key => $value) {
					$q = 'SELECT * FROM ms_user WHERE parent_user_id = '.$key;
					$dt = orm_get($q);
					if(!empty($dt)){
						$this->deduct_quota_child($key,$value,NULL);
					}
				}
			}
			
			$return['message'] = 'Child Quota Deducted!';
			$return['is_success'] = 1;
		}
		return $return;
    }

    // Decrease Quota Child End 
    // ======================================================================================================
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}