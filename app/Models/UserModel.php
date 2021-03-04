<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ms_user';

    protected $fillables;

    // ======================================================================================================
    // Decrease Quota Child Start By Harvei
	public $who = 'System';
	public $ip = "System";

    public static function deduct_child_quota($trx){
		$modelUser = new UserModel();
		$q = 'SELECT u.user_id, u.parent_user_id, u.quota_remaining, u.quota_additional FROM ms_user u';
		$q.= ' WHERE u.user_id = '.$trx->user_id.' AND LOWER(u.user_category) = "user" AND u.status = 1';
		$data = orm_get($q);

		// $q = 'UPDATE ms_user SET quota_remaining = quota_remaining - '.$trx->total;
		// $q.= ', chamber_sync_flag = 20 WHERE user_id = '.$trx->user_id;
		// DB::statement($q);

		// update user_quota
		// if quota user enough
		if($trx->total <= $data->quota_remaining){
			$modelUser->update_user_quota($trx->total, $trx->user_id);
			// insert movement_quota
			$modelUser->insert_movement_quota($trx, $trx->total, $trx->user_id, $data->quota_remaining);
			// deduct child
			$child = $modelUser->deduct_quota_child($data, $trx->total, NULL, $trx);
			// deduct parent
			$parent = $modelUser->deduct_parent_quota($trx, $trx->total);
		} else{
			// if quota not enough
			$sisa = ((int)$trx->total - (int)$data->quota_remaining);
			// decrease remaining with user's current remaining quota
			$modelUser->update_user_quota($data->quota_remaining, $trx->user_id);
			// and decrease user's additional quota
			$type = 'additional';
			$modelUser->update_user_quota($sisa, $trx->user_id, $type);
			// insert movement_quota
			$modelUser->insert_movement_quota($trx, $trx->total, $trx->user_id, $data->quota_remaining, $data, $sisa);
			// deduct child
			$child = $modelUser->deduct_quota_child($data, $data->quota_remaining, NULL, $trx);
			// deduct parent
			$parent = $modelUser->deduct_parent_quota($trx, $data->quota_remaining);
		}
		return $child;
    }

    public function deduct_quota_child($data,$qty, $qtyb=NULL, $trx){
    	$return['message'] = 'Init';
		$return['is_success'] = 0;
		$temp_qty = $deduct_temp = 0;
		$arr_user = array();
		$modelUser = new UserModel();
		// get user child
		if(isset($data)){
			$q = 'SELECT u.user_id, u.site_id, u.parent_user_id, u.quota_remaining FROM ms_user u';
			$q.= ' WHERE u.parent_user_id = '.$data->user_id.' AND LOWER(u.user_category) = "user" AND u.status = 1 AND u.quota_remaining > 0';
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
					// insert movement_quota
					$modelUser->insert_movement_quota($trx, $child[$i]->quota_remaining, $child[$i]->user_id, $child[$i]->quota_remaining);

					$deduct_temp = $deduct_temp + $temp_qty - ($child[$i]->quota_remaining);
				} else{
					// jika quota user cukup
					$qtyb[$child[$i]->user_id] = $temp_qty + $qtyb[$child[$i]->user_id];

					// $qu = 'UPDATE ms_user SET quota_remaining = quota_remaining - '.$temp_qty;
					// $qu.= ', chamber_sync_flag = 20 WHERE user_id = '.$child[$i]->user_id;
					// DB::statement($qu);

					// update quota user
					$modelUser->update_user_quota($temp_qty, $child[$i]->user_id);
					// insert movement_quota
					$modelUser->insert_movement_quota($trx, $temp_qty, $child[$i]->user_id, $child[$i]->quota_remaining);

					// $this->deduct_quota_child($child[$i], $temp_qty);
				}
				// untuk akomodir jika ada sisa potong yang ditampung
				if($i==$total_child-1){
					if($deduct_temp>0){
						$modelUser->deduct_quota_child($data, $deduct_temp, $qtyb, $trx);
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
						$modelUser->deduct_quota_child($key,$value,NULL,$trx);
					}
				}
			}
			
			$return['message'] = 'Child Quota Deducted!';
			$return['is_success'] = 1;
		}
		return $return;
    }

    public function insert_movement_quota($data, $qty, $user, $remaining, $data_user=NULL, $sisa=0){
    	// insert to movement_quota
		$q = "INSERT INTO tr_movement_quota_level(user_id, site_id, transaction_id, qty, value, addt, balance_qty, 
				balance_value, balance_addt, created_at, created_by, created_ip)";
		if($sisa==0){
			$q.= " VALUES(".replace_quote($user).",".replace_quote($data->site_id).",".replace_quote($data->transaction_id).",".(int)$qty.",".(int)$data->value.",NULL";
			if(strtolower($data->flag_qty_value) == "value"){
				$q.= ",NULL,".((int)$data->quota_remaining-(int)$data->value).",NULL";
			} elseif(strtolower($data->flag_qty_value) == "qty"){
				$q.= ",".((int)$remaining-(int)$qty).",NULL,NULL";
			}
		}
		// if quota_remaining user not enough 
		elseif($sisa>0){
			$q.= " VALUES(".replace_quote($user).",".replace_quote($data->site_id).",".replace_quote($data->transaction_id).",";
			$q.= (int)$remaining.",".(int)$data->value.",".(int)$sisa;
			if(strtolower($data->flag_qty_value) == "value"){
				$q.= ",NULL,".((int)$remaining).",".((int)$data_user->quota_additional-(int)$sisa);
			} elseif(strtolower($data->flag_qty_value) == "qty"){
				$q.= ",".((int)$remaining).",NULL,".((int)$data_user->quota_additional-(int)$sisa);
			}
		}
		$q.= ",".replace_quote(date("Y-m-d H:i:s")).",".replace_quote($this->who).",".replace_quote($this->ip).")";
		DB::statement($q);
    }

    public function update_user_quota($qty, $user, $type = 'remaining'){
    	if($type == 'additional'){
			$q = 'UPDATE ms_user SET quota_additional = quota_additional - '.$qty;
			$q.= ', chamber_sync_flag = 20 WHERE user_id = '.$user;
			DB::statement($q);
    	} elseif($type == 'remaining'){
	    	$q = 'UPDATE ms_user SET quota_remaining = quota_remaining - '.$qty;
			$q.= ', chamber_sync_flag = 20 WHERE user_id = '.$user;
			DB::statement($q);
    	}
    }

    // Decrease Quota Child End 
    // ======================================================================================================

    public function deduct_parent_quota($trx, $qty){
		$modelUser = new UserModel();
		$q = 'SELECT u.user_id, u.parent_user_id, u.quota_remaining FROM ms_user u';
		$q.= ' WHERE u.user_id = '.$trx->user_id.' AND LOWER(u.user_category) = "user" AND u.status = 1';
		$data = orm_get($q);

		// deduct current user quota
		// depracated on 25 july 2019 (karena sudah d potong function child)
		// $qu = 'UPDATE ms_user SET quota_remaining = quota_remaining - '.$trx->total;
		// $qu.= ', chamber_sync_flag = 20 WHERE user_id = '.$data->user_id;
		// DB::statement($qu);
		if(isset($data)) $parent = $modelUser->deduct_quota_parent($data, $qty, $trx);

		return $parent;
	}

	public function deduct_quota_parent($data, $qty, $trx){
		$modelUser = new UserModel();
		$return['message'] = 'Init';
		$return['is_success'] = 0;
		// get user parent
		if(isset($data)){
			$q = 'SELECT u.user_id, u.site_id, u.parent_user_id, u.quota_remaining FROM ms_user u';
			$q.= ' WHERE u.user_id = '.$data->parent_user_id.' AND LOWER(u.user_category) = "user" AND u.status = 1';
			$parent = orm_get($q);

			// $qu = 'UPDATE ms_user SET quota_remaining = quota_remaining - '.$qty;
			// $qu.= ', chamber_sync_flag = 20 WHERE user_id = '.$parent->user_id;
			// DB::statement($qu);

			// update quota user
			$modelUser->update_user_quota($qty, $parent->user_id);
			// insert movement_quota
			$modelUser->insert_movement_quota($trx, $qty, $parent->user_id, $parent->quota_remaining);
			if(isset($parent)){
				if(isset($parent->parent_user_id)){
					$modelUser->deduct_quota_parent($parent, $qty, $trx);
				}
			}
			$return['message'] = 'Parent Quota Deducted!';
			$return['is_success'] = 1;
		}

		return $return;
    }
    // Decrease Quota Parent End 
    // ======================================================================================================

}