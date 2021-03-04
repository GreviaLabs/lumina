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

use App\Models\CompanyModel;
use App\Models\UserModel;

class UserController extends ApiController {

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
	'firstname', 'lastname', 'quota_initial', 'quota_additional', 'quota_remaining', 'budget_quota', 'job_title', 
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

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function get()
	{
		$attr = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		// $q = 'SELECT * FROM ' . $this->table . ' WHERE 1';
		$q = '
		SELECT user_id, firstname, lastname, CONCAT(firstname," ",lastname) as fullname, job_title, division_id, 
		email, user_code, `password`, user_category,u.chamber_sync_flag, role_name, level_id, level_name, site_id, ur.role_id,
		parent_user_id, quota_initial, quota_additional, quota_remaining, email, budget_quota, counter_wrong_pass, 
		status_lock, locked_time, reset_by, reset_time, reset_token, reset_token_expired, u.status, u.created_at, u.created_by, u.created_ip, 
		u.updated_at, u.updated_by, u.updated_ip,
		u.attribute, u.attribute_value
		FROM '	. $this->table . ' u
		LEFT JOIN ms_level l USING(level_id)
		LEFT JOIN ms_user_role ur USING(user_id)
		LEFT JOIN ms_role r USING(role_id)
		WHERE 1';
		
		if (isset($attr['user_id']) && $attr['user_id'] != '') {
			$q.= ' AND u.user_id = '.$attr['user_id'];
		}

		if (isset($attr['user_code']) && $attr['user_code'] != '') {
			$q.= ' AND u.user_code = '.replace_quote($attr['user_code']);
		}

		if (isset($attr['email']) && $attr['email'] != '') {
			$q.= ' AND u.email = '.replace_quote($attr['email']);
        }
        
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND u.site_id = '.replace_quote($attr['site_id']);
        }

		// debug($q,1);
		
		$data = orm_get($q);
		echo json_encode($data);
		die;
	}
	
	public function get_list()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = '
		SELECT user_id, firstname, lastname, job_title, division_id, email, user_category,u.chamber_sync_flag, role_name, level_id, level_name, site_id, ur.role_id, u.status, u.quota_initial, u.quota_additional, u.quota_remaining, u.budget_quota, u.user_code
		FROM '	. $this->table . ' u
		LEFT JOIN ms_level l USING(level_id)
		LEFT JOIN ms_user_role ur USING(user_id)
		LEFT JOIN ms_role r USING(role_id)
		WHERE 1';
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			
			// array('user_id', 'site_id', 'parent_user_id', 'level_id','user_code', 'firstname', 'lastname', 'quota_initial', 'quota_additional', 'quota_remaining', 'job_title', 'division_id', 'email', 'user_category', 'password', 'counter_wrong_pass', 'status_lock', 'locked_time', 'reset_by', 'reset_time',  'status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
			
			$q.= ' AND ( ';
			$q.= ' user_code LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR CONCAT(firstname, " ", lastname) LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR lastname LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR job_title LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR division_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR email LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR user_category LIKE '.replace_quote($attr['keyword'],'like');
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
		} else {
			if (isset($attr['user_id']) && $attr['user_id'] != '') {
				if(!is_array($attr['user_id'])) $user_id = json_decode($attr['user_id']);
				else $user_id = $attr['user_id'];
				if(count($user_id)>1){
					$user_id = implode(",", $user_id);
					$q.= " AND user_id IN (".$user_id.")";
				}
				else{
					$q.= " AND user_id = ".$user_id;
				}
			}
			
			if (isset($attr['user_code']) && $attr['user_code'] != '') {
				$q.= ' AND u.user_code = '.replace_quote($attr['user_code']);
            }
            
			if (isset($attr['site_id']) && $attr['site_id'] != '') {
				$q.= ' AND u.site_id = '.replace_quote($attr['site_id']);
			}

			if (isset($attr['email']) && $attr['email'] != '') {
				$q.= ' AND u.email = '.replace_quote($attr['email']);
			}
		}
		
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND u.status = ' . $attr['status'] . ' AND ur.status = ' . $attr['status'] . ' AND r.status = ' . $attr['status'];
        } else {
			$q.= ' AND u.status != -1 AND ur.`status` = 1 AND r.`status` = 1';
		}
        
		$q.= ' GROUP BY user_id DESC';
		
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

	// function get child recursive
	function user_list(array $elements, $parentId = 0) {
		$branch = array();
		foreach ($elements as $element){
		    if ($element->parent_user_id == $parentId){
		        $children = $this->user_list($elements, $element->user_id);
		        if ($children){
		            $element->child = $children;
		        }
		    $branch[] = $element;
		    }
		}
		return $branch;
	}

	// get chiild user
	public function get_user()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		if (isset($attr['user_id']) && $attr['user_id'] != '') {
			$user_id = $attr['user_id'];
		}

		$q = '
		SELECT u.user_id, u.site_id, u.parent_user_id, u.user_code, u.firstname, u.lastname, u.job_title, u.email, u.user_category
		FROM '	. $this->table . ' u
		WHERE 1';

		$result = orm_get_list($q);

		$list_user = $this->user_list($result,$user_id);
		debug($list_user,1);
        echo json_encode($list_user); 
		die;
	}

	public function get_site_quota_remaining(){
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
		$q = "SELECT site_id, SUM(quota_initial) AS quota_init, SUM(quota_additional) AS quota_add, SUM(quota_remaining) AS quota_remain FROM ms_user";
		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q .= " WHERE site_id = '".$attr['site_id']."'";
		}
		$q .= " GROUP BY site_id";
		$data = orm_get($q);
		$result = $data;

		echo json_encode($result); 
		die;
	}

	public function get_reset_day($date,$reset_days)
	{
		$attr = $result = $return = NULL;
		// if (! empty($_GET)) 
		// 	$attr = $_GET;

		$date = $date;		
		$now = get_datetime();	

		$date1=date_create($date);
		$date2=date_create($now);
		$diff=date_diff($date1,$date2);

		$diff1 = $diff->format("%R");
		$x = $diff->format("%a");
		$y = $reset_days;
		$result = fmod($x,$y);

		if($result == 0 && $diff1 == '+')
		{
			// echo 'OK'.' '.$date.' '.$now.' '.$x;
			$return['message'] = 'Reset Days';
			$return['is_success'] = 1;
		}
		else
		{
			// echo 'sisa '.$result.' '.$date.' '.$now.' '.$x;	
			$return['message'] = 'Not Reset Days. Will Reset in '.$result.' day(s)';
			$return['is_success'] = 0;	
		}		

		return $return;
	}

	// TOPDOWN QC : DONE by rusdi thursday 21 mar 2019
	// BOTTOM UP QC : not yet
	public function insert_quota_by_site_id()
	{
		$model = new UserModel();
		 
		
		
		// debug('jalan nih',1);
		$error = $return = $attr = NULL;
		if (! empty($_GET)) $attr = $_GET;
		
		if (! $attr['site_id']) $error = 'Site_id harus diisi';

		if ($error) {
			$return['error'] = $error;
			echo json_encode($return);
			die;
		}

		$q = 'SELECT * FROM ms_site s WHERE s.site_id = ' . replace_quote($attr['site_id']);
		$obj_site = orm_get($q,NULL,'json');
		$obj_site = json_decode($obj_site,1);

		$flag_qty_value = $site_qty_value = $list_user = NULL;
		$return['data'] = NULL;
		if (isset($obj_site['site_id'])) 
		{

			$flag_qty_value = $obj_site['flag_qty_value']; // qty
			$site_qty_value = $obj_site['site_qty_value']; // 200
			$start = $obj_site['start_date_counting'];
			$res = $obj_site['reset_days'];
			// Detect is_reset_days
			$reset_days = $this->get_reset_day($start,$res);
			// debug($reset_days,1);
			if($reset_days['is_success'] == 0){
				return $reset_days['message'];
				die;
			}
			// Detect type method_calc
			if ($obj_site['method_calc'] == 'top_down') 
			{
				
				// Start top_down logic
				// $list_user = $this->get_list_parent_by_user(array(
                //     'site_id' => $obj_site['site_id'],
				// 	'flag_qty_value' => $obj_site['flag_qty_value'],
				// 	'site_qty_value' => $obj_site['site_qty_value'],
				// ));
                $list_user = $this->get_list_parent_by_user($obj_site);
				$list_user = json_decode($list_user,1);
				// debug($list_user,1);


				$return['data'] = $list_user;
				// debug($list_user);
				// die;

				// End top_down logic

			} 
			else if ($obj_site['method_calc'] == 'bottom_up') 
			{
                // Start bottom_up logic
                $bottom_up = $this->user_bottom_up($obj_site);
                // return $bottom_up;
                $return['data'] = $bottom_up;
				// End bottom_up logic
			}
		}
		else
		{
			$return['message'] = 'data not found / error occurred';
		}

		// $list_user = $this->sortme($list_user);
		debug($return,1);
		// echo json_encode($return);
		die;
		// debug($list_user,1);
		
		
		// debug($list_user,1);
	}

	
	// ================================================================================
	// Mode Topdown - Return all user with quota - using recursive method
	// QC : checked by rusdi - thursday 21 mar 2019
	private function get_list_parent_by_user($attr = NULL)
	{
		$result = $error = $quota = NULL;
		$userorder = 1; // user hierarchy
        // if (! empty($_GET)) $attr = $_GET;
		// if (! empty($attr)) $attr = $_GET;
		
		// debug($attr);
		
		if (! isset($attr['site_id'])) $error = 'Site_id harus diisi';
		if (! isset($attr['flag_qty_value'])) $error = 'flag_qty_value harus diisi';
		if (! isset($attr['site_qty_value'])) $error = 'site_qty_value harus diisi';
		
		if ($error) {
			$return['error'] = $error;
			echo json_encode($return);
			die;
		}

		$quota = $attr['site_qty_value'];

        // Get all parent with child
        $q = '
		SELECT user_id,user_code, l.level_name, l.level_hierarchy, ' . $userorder . ' as userorder, u.parent_user_id, u.site_id,
        (
            SELECT COUNT(user_id)
            FROM ms_user mu 
            WHERE mu.parent_user_id = u.user_id AND mu.status = 1 AND u.user_category != "replenish"
        ) as totalchild
        FROM ms_user u 
        LEFT JOIN ms_level l USING (level_id)
        WHERE 1 AND u.parent_user_id IS NULL AND site_id = ' . replace_quote($attr['site_id']) . ' AND u.status = 1 AND u.user_category != "replenish"
        HAVING totalchild >= 0 
        ';

        $data = orm_get_list($q,'json');
        $data = json_decode($data,1);

        $result = array();
        if (! empty($data)) {
			$tmpquota = $total_person = 0;
			$total_person = count($data);

            foreach ($data as $key => $rs) {
				
				// Count quota here
				$tmpquota = $quota / $total_person;
				if (strpos($tmpquota,'.')) {
					
					// round up on last round
					if ($key == ($total_person - 1)) {
						$tmpquota = ceil($tmpquota);
					} else {
						$tmpquota = floor($tmpquota);
					}
				} 

				$result[$key] = $rs;
				
				// identifier top global parent
				$result[$key]['is_top_parent'] = TRUE;
				$result[$key]['quota'] = $tmpquota;

				// update quota to user
				$save = DB::table($this->table)->where($this->primary_key,$rs['user_id'])->update(array(
				'quota_remaining' => $tmpquota, 
				'quota_initial' => $tmpquota,
				'chamber_sync_flag' => '20'
				));
				
				// ------------------------------
				// Save to movement_article
				// $data = new stdClass;
				// $data->site_id = $rs['site_id'];
				// $data->transaction_id = NULL;
				// $data->value = NULL;
				// $data->flag_qty_value = $attr['flag_qty_value'];
				// $data->quota_remaining = $tmpquota;
				
				// $model = new UserModel();
				// $model->insert_movement_quota($data,$tmpquota,$rs['user_id'],$tmpquota);
				
				// public function insert_movement_quota($data, $qty, $user, $remaining){
				// site_id, transaction_id, value, flag_qty_value, quota_remaining
				// ------------------------------

                $listchild = NULL;
                $listchild = $this->get_list_child($rs['user_id'], $tmpquota, ($userorder + 1), $rs['site_id']);
				
				
				
				$result[$key]['listchild'] = $listchild;
				
				// $userorder++;
            }
        } else {
			$result['message'] = 'data not found / error occurred';
		}
        
        return json_encode($result); 
		die;
    }
    
	// Recursive function get all child below level
	// userorder = user hierarchy level
    private function get_list_child($user_id, $quota, $userorder, $site_id)
    {
        $return = $listchild = NULL;

        if (! isset($user_id)) return $return;
        if (! isset($quota)) return $return;
        if (! isset($userorder)) $userorder = 1;
        if (! isset($site_id)) return $return;

        $q = '
        SELECT user_id,user_code, l.level_name, l.level_hierarchy, u.parent_user_id, ' . $userorder . ' as userorder, u.site_id,
        (
            SELECT COUNT(user_id)
            FROM ms_user mu 
            WHERE mu.parent_user_id = u.user_id AND mu.site_id = u.site_id AND mu.status = 1 AND mu.user_category != "replenish"
        ) as totalchild
        FROM ms_user u 
        LEFT JOIN ms_level l USING (level_id)
        WHERE 1 AND u.parent_user_id = ' . $user_id . ' AND u.site_id = '. replace_quote($site_id) . ' AND u.status = 1 AND u.user_category != "replenish"';
        $listchild = orm_get_list($q,'json');
		$listchild = json_decode($listchild,1);
		// debug($listchild,1);

        if (! empty($listchild)) {
			$temp = NULL;
			$total_person = count($listchild);
            foreach ($listchild as $x => $rc) {
				
				// Count quota here
				$tmpquota = $quota / $total_person;
				if (strpos($tmpquota,'.')) {
					
					// round up on last round
					if ($x == ($total_person - 1)) {
						$tmpquota = ceil($tmpquota);
					} else {
						$tmpquota = floor($tmpquota);
					}
				}

				$rc['quota'] = $tmpquota;
				
				$rc['userorder'] = $userorder;

				// Update quota to user
				$save = DB::table($this->table)->where($this->primary_key,$rc['user_id'])->update(array(
				'quota_remaining' => $tmpquota, 
				'quota_initial' => $tmpquota,
				'chamber_sync_flag' => '20'
				));

				$temp[] = $rc;
				
				// Still have child
                if ($rc['totalchild'] > 0) {
					
					$tempchild = NULL;
                    $tempchild = $this->get_list_child($rc['user_id'],$tmpquota,($userorder + 1), $rc['site_id']);
					$temp[$x]['listchild'] = $tempchild;
					$temp[$x]['userorder'] = $userorder;
                } else {
					// No chilld
					// $userorder--;
					// $quota = $quota;
                    $temp[$x]['listchild'] = array();
                    $temp[$x]['is_bottom_child'] = '1';
					$temp[$x]['userorder'] = $userorder;
				}
				// $userorder++;

			}
			
            $return = $temp;
        }

        return $return;
	}
	// TOPDOWN END
	// ================================================================================
	
	// ================================================================================
	// BOTTOM UP START
	// QC : notyet
	// Get list bottom level child, sort by level_hierarchy
	public function get_list_bottom_level_child()
	{
		$attr = NULL;

		if ($_GET) $attr = $_GET;
		if (! isset($attr['site_id'])) return ' site_id harus diisi';

		$q = '
		SELECT u.user_id, u.parent_user_id, u.user_code, l.level_id , l.level_hierarchy, l.level_name, u.quota_initial, u.quota_additional, u.quota_remaining
		FROM ms_user u 
		LEFT JOIN ms_level l USING(level_id)
		WHERE u.user_id NOT IN (
			SELECT mu.parent_user_id 
			FROM ms_user mu
			WHERE mu.parent_user_id IS NOT NULL
		) AND u.parent_user_id IS NOT NULL AND u.site_id = ' . replace_quote($attr['site_id']) . ' 
		ORDER BY l.level_hierarchy DESC, l.level_id ASC
		';
		// debug($q,1);

		$listchild = orm_get_list($q,'json');
		$listchild = json_decode($listchild,1);

		$return = $userorder = NULL;
		$quota = 0;
		if (! empty($listchild)) {
			foreach ($listchild as $key => $rs)
			{
				// $rs;
				$userorder = 1;
				$quota = $rs['quota_initial'];
				if (! isset($quota)) $quota = 30;

				$return[] = $rs;
				if (isset($rs['parent_user_id'])) {
					$return[$key]['parent'] = $this->get_list_parent($rs['parent_user_id'], $quota, $userorder);
				} else {

				}
			}
		}

		// debug($return,1);
		// debug($listchild,1);
	}

	// Recursive function get all child below level
	// userorder = user hierarchy level
    private function get_list_parent($parent_user_id, $quota, $userorder)
    {
        $return = $listparent = NULL;

        if (! isset($parent_user_id)) return $return;
        if (! isset($quota)) return $return;
        if (! isset($userorder)) $userorder = 1;

        $q = '
        SELECT user_id, user_code, u.quota_initial, u.quota_additional, u.quota_remaining, l.level_name, l.level_hierarchy, u.parent_user_id, ' . $userorder . ' as userorder, 
        (
            SELECT COUNT(user_id)
            FROM ms_user mu 
            WHERE mu.parent_user_id = u.user_id
        ) as totalparent
        FROM ms_user u 
        LEFT JOIN ms_level l USING (level_id)
        WHERE 1 AND u.user_id = ' . $parent_user_id;
        $listparent = orm_get_list($q,'json');
		$listparent = json_decode($listparent,1);
		// debug($q,1);
		// debug($listparent,1);

        if (! empty($listparent)) {
			$temp = NULL;
			$total_person = count($listparent);
            foreach ($listparent as $x => $rc) {
				
				// Increment quota user when not null
				if (isset($rc['quota_initial'])) $quota += $rc['quota_initial'];

				$tmpquota = $quota;

				// No need to sum up or down
				// if (strpos($tmpquota,'.')) {
					
				// 	// round up on last round
				// 	if ($x == ($total_person - 1)) {
				// 		$tmpquota = ceil($tmpquota);
				// 	} else {
				// 		$tmpquota = floor($tmpquota);
				// 	}
				// }

				$rc['quota'] = $tmpquota;
				
				$rc['userorder'] = $userorder;
				$temp[] = $rc;
				
				// Parent level
                if ($rc['totalparent'] > 0) {
					
					$tempparent = NULL;
                    $tempparent = $this->get_list_parent($rc['parent_user_id'],$tmpquota,($userorder + 1));
					$temp[$x]['listparent'] = $tempparent;
					$temp[$x]['userorder'] = $userorder;
                } else {
					// No chilld
					// $userorder--;
					// $quota = $quota;
                    $temp[$x]['listparent'] = array();
                    $temp[$x]['is_bottom_child'] = '1';
					$temp[$x]['userorder'] = $userorder;
				}
				// $userorder++;

			}
			
            $return = $temp;
        }

        return $return;
	}
	// BOTTOM UP END
	// ================================================================================
	

	public function get_list_dropdown()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = 'SELECT user_id, CONCAT(firstname," ",lastname) as fullname FROM ' . $this->table . ' WHERE 1';

		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND status = '.$attr['status'];
        } else {
			$q.= ' AND status != -1';
		}
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			
			// array('user_id', 'site_id', 'parent_user_id', 'level_id','user_code', 'firstname', 'lastname', 'quota_initial', 'quota_additional', 'quota_remaining', 'job_title', 'division_id', 'email', 'user_category', 'password', 'counter_wrong_pass', 'status_lock', 'locked_time', 'reset_by', 'reset_time',  'status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
			
			$q.= ' AND ( ';
			$q.= ' user_code LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR CONCAT(firstname, " ", lastname) LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR lastname LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR job_title LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR division_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR email LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR user_category LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
		
		if (isset($attr['user_id']) && $attr['user_id'] != '') {
			$q.= ' AND user_id = '.$attr['user_id'];
        }
		
		if (isset($attr['order'])) { 
			$q.= ' ORDER BY ' . $attr['order'];
			if (isset($attr['orderby'])) $q .= ' '.$attr['orderby']; 
		} else  {
			$q.= ' ORDER BY '. $this->primary_key .' DESC';
		}
		
		// set paging
		if (isset($attr['paging'])) {
			if (! isset($attr['offset'])) $attr['offset'] = OFFSET;
			if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
		}
		
		if (isset($attr['offset'])) { 
			$q.= ' LIMIT ' . $attr['offset'];
			
			if (! isset($attr['perpage'])) $attr['perpage'] = PERPAGE;
			
			$q.= ', ' . $attr['perpage'];
		}

		$result['data'] = orm_get_list($q);
        
        echo json_encode($result); 
		die;
	}

    // Logic quota user_bottom_up by ali
	// public function user_bottom_up($attr)
	// {
	// 	$result = NULL;
        
 //        if (! isset($attr['site_id'])) {
 //            echo "site_id must filled";
 //            die;
 //        }
		
	// 	$q = 'SELECT user_id FROM ms_user WHERE parent_user_id IS NULL AND site_id = ' . replace_quote($attr['site_id']);
				
	// 	$list_data = orm_get_list($q);	
		
	// 	$update1 = 'update ms_user set quota_remaining = quota_initial WHERE site_id = ' . replace_quote($attr['site_id']);
	// 	DB::statement($update1);

	// 	if (! empty($list_data)) 
	// 	{	
	// 		try 
	// 		{
	// 			for($i=0; $i<count($list_data); $i++)
	// 			{
	// 				$strid = $list_data[$i]->user_id;					
	// 				$strid2 = $strid;
	// 				$strid3 = '';

	// 				back:
					
	// 				$s = 'SELECT user_id, parent_user_id, quota_initial FROM ms_user WHERE parent_user_id IN('.$strid2.') AND site_id = ' . replace_quote($attr['site_id']);
	// 				$list_user = orm_get_list($s);
	// 				// debug($list_user,1);
	// 				$strid2='';
					
	// 				if (! empty($list_user))
	// 				{
	// 					for($a=0; $a<count($list_user); $a++)
	// 					{
	// 						if ($strid2 != '')
	// 							$strid2 .= ",";
	// 						$strid2 .= $list_user[$a]->user_id;

	// 						if ($strid3 != '')
	// 							$strid3 .= ",";
	// 						$strid3 .= $list_user[$a]->user_id."_".$list_user[$a]->parent_user_id;							
	// 					}
	// 					goto back;
	// 				}
	// 				else
	// 				{						
	// 					$strSplit = explode(",", $strid3);
	// 					$z = count($strSplit);
	// 					for($a=0; $a<count($strSplit); $a++)
	// 					{
	// 						$strSplit2 = explode("_", $strSplit[$z - ($a + 1)]);
	// 						$update2 = "UPDATE ms_user a LEFT JOIN ms_user b ON 1=1 AND b.user_id = ".$strSplit2[0]." AND b.parent_user_id = ".$strSplit2[1]." SET a.quota_remaining = a.quota_remaining + b.quota_remaining WHERE 1=1 AND a.user_id =".$strSplit2[1]." AND site_id = ".replace_quote($attr['site_id']);
	// 						DB::statement($update2);                        
	// 					}
	// 				}
	// 			}
	// 		}
	// 		catch(\Throwable $e)
	// 		{
	// 			$result['error'] = 0;
	// 		}
	// 	}
	// }

	// public function hierarchy(array $elements, $parentId = 0) {
	//     $branch = array();

	//     foreach ($elements as $element) {
	//         if ($element->parent_user_id == $parentId) {
	//             $children = $this->hierarchy($elements, $element->user_id);
	//             if ($children) {
	//                 $element->children = $children;
	//             }
	//             $branch[] = $element;
	//         }
	//     }

	//     return $branch;
	// }

	// Logic quota user_bottom_up by Harvei
	private function user_bottom_up($attr)
	{
		$result = NULL;

		if (! isset($attr['site_id'])) {
            echo "site_id must filled";
            die;
        }
       
		// get parent user
		$q = '
        SELECT u.user_id, u.parent_user_id, u.quota_initial, u.site_id 
        FROM ms_user u
		LEFT JOIN ms_user msu ON msu.user_id = u.user_id
		WHERE EXISTS(
            SELECT user_id 
            FROM ms_user mu
			WHERE mu.parent_user_id = u.user_id AND mu.status = 1 AND LOWER(mu.user_category) != "replenish"
        )  AND u.site_id = ' . replace_quote($attr['site_id']) . '
        AND LOWER(u.user_category) != "replenish" AND u.status = 1
        ORDER BY user_id DESC';
				
        $list_data_parent = orm_get_list($q);
        // debug($list_data_parent);

		// update parent user quota_initial to 0
		if(! empty($list_data_parent) && count($list_data_parent)>0){
			for($i=0; $i<count($list_data_parent); $i++){
				$update1 = 'UPDATE ms_user SET quota_initial = 0, quota_remaining = 0 WHERE site_id = ' . replace_quote($attr['site_id']). ' AND user_id = '.$list_data_parent[$i]->user_id;
				DB::statement($update1);
			}
		}

		// get child
		$q = '
        SELECT u.user_id, u.parent_user_id, u.quota_initial 
        FROM ms_user u
		LEFT JOIN ms_user msu ON msu.user_id = u.user_id
		WHERE NOT EXISTS(
            SELECT user_id
            FROM ms_user mu
			WHERE mu.parent_user_id = u.user_id AND mu.status = 1 AND LOWER(mu.user_category) != "replenish"
        )  AND u.site_id = ' . replace_quote($attr['site_id']) . ' 
        AND LOWER(u.user_category) != "replenish" AND u.status = 1
        ORDER BY user_id DESC';
				
		$list_data_child = orm_get_list($q);

		// update first parent user quota_initial to child quota_initial
		if(! empty($list_data_child) && count($list_data_child)>0){
			for($j=0; $j<count($list_data_child); $j++){
				$update1 = 'UPDATE ms_user SET quota_initial = quota_initial + '.$list_data_child[$j]->quota_initial;
				$update1.= ',quota_remaining = quota_initial, chamber_sync_flag = 20';
				$update1.= ' WHERE site_id = ' . replace_quote($attr['site_id']). ' AND user_id = '.$list_data_child[$j]->parent_user_id;
                DB::statement($update1);
                
				$update2 = 'UPDATE ms_user SET quota_remaining = quota_initial, chamber_sync_flag = 20';
				$update2.= ' WHERE site_id = ' . replace_quote($attr['site_id']). ' AND user_id = '.$list_data_child[$j]->user_id;
				DB::statement($update2);
			}
		}

		if(! empty($list_data_parent) && count($list_data_parent)>0){
			$update = $this->update_quota_parent($list_data_parent);
		}
		if(!empty($update)){
			$data['message'] = $update['message'];
			$data['is_success'] = $update['is_success'];
			return $data;
		}
	}

	public function update_quota_parent($data, $temp_quota = 0){
		$a = array();
		$is_success = 0;
		$message = '';
		for($i=0; $i<count($data); $i++){
			if(!empty($data[$i+1])){
                $q = 'SELECT user_id, parent_user_id, quota_initial FROM ms_user WHERE user_id = '.$data[$i]->user_id . ' AND LOWER(user_category) != "replenish" AND status = 1';
                
                // debug($q);
                $select = orm_get($q);
                
                // 
				if($data[$i]->parent_user_id != $data[$i+1]->parent_user_id && $data[$i]->parent_user_id != ""){
					$q = 'UPDATE ms_user SET quota_initial = quota_initial + '. $temp_quota . ' + '. $select->quota_initial;
					$q.= ', quota_remaining = quota_initial, chamber_sync_flag = 20';
					$q.= ' WHERE site_id = '. replace_quote($data[$i]->site_id) .' AND user_id = '.$data[$i]->parent_user_id;
                    DB::statement($q);
                    // debug($q.'<hr/>');
					$message = "Success to Update";
					$is_success = 1;
				} else{
					array_push($a,$data[$i+1]);
					$temp_quota = $select->quota_initial;
					$this->update_quota_parent($a,$temp_quota);
				}
			} 
		}
		$data['is_success'] = $is_success;
		$data['message'] = $message;
		return $data;
	}

	// get list user for reporting dashboard
	public function get_parent(){
		$attr = NULL;

		if(! empty($_GET)) $attr = $_GET;
		$q = 'SELECT user_id, user_code, site_id, parent_user_id FROM '.$this->table;
		$q.= ' WHERE 1';
		if(isset($attr['user_id']) && $attr['user_id'] != ''){
			$q.= ' AND user_id = '.$attr['user_id'];
		}
		$user = orm_get($q);

		$qt = 'CREATE TEMPORARY TABLE temp_child_table (';
		$qt.= '`user_id` int NOT NULL, `user_code` varchar(100), `site_id` varchar(10), `firstname` varchar(100), 
				`lastname` varchar(100), `parent_user_id` int, `division_id` int, `division_name` varchar(100),
				 PRIMARY KEY (user_id))';
		DB::statement($qt);
		
		$children = $this->get_children($user);

		$q = 'SELECT * FROM temp_child_table';
		$child = orm_get_list($q);

		$qdt = 'DROP TEMPORARY TABLE temp_child_table';
		DB::statement($qdt); 

		return $child;
	}

	public function get_children($data){

		$qi = 'INSERT INTO temp_child_table(`user_id`, `user_code`, `site_id`, `firstname`, 
				`lastname`, `parent_user_id`, `division_id`, `division_name`)
				SELECT u.user_id, u.user_code, u.site_id, u.firstname, u.lastname, u.parent_user_id, u.division_id, d.division_name
				FROM '.$this->table.' u LEFT JOIN ms_division d ON u.division_id = d.division_id WHERE 1 AND u.parent_user_id = '.$data->user_id;
		DB::statement($qi);

		$q = 'SELECT u.user_id, u.user_code, u.site_id, u.firstname, u.lastname, u.parent_user_id, 
			(SELECT count(user_id) FROM ms_user mu WHERE mu.parent_user_id = u.user_id AND mu.site_id = u.site_id) as total_child
			FROM '.$this->table.' u WHERE 1 AND u.parent_user_id = '.$data->user_id;
		$child = orm_get_list($q);
		if(count($child)==0){
            $return = array();
		} else{
			// $temp = NULL;
			foreach($child as $key => $val){
				$temp[] = $val;
				if($val->total_child>0){
					$tempchild = $this->get_children($val);
					$temp[] = $tempchild;
				} else{
					// $temp[$key]->list_child = array();
					// $temp[$key]->last_child = 1;
				}
			}
			$return = $temp;
		}
		return $return;
	}

	public function save()
	{
        $post = $attr = $result = NULL;
		if (! empty($_POST)) $post = $_POST;
		
		// validate_required_column
		$attr_required = validate_required_column($this->list_required_column, $post);

		// validate_column
		$attr = validate_column($this->list_column, $post);

		$result['is_success'] = 1;
		$result['message'] = NULL;
        
        if (empty($attr)) $result['message'] = 'no data';
		if (isset($attr_required)) $result['message'] = $attr_required.' column is required';
		
		// Print error if message exist
		if (isset($result['message'])) {
			$result['is_success'] = 0;
			if (isset($attr)) $result['paramdata'] = $attr;
			echo json_encode($result);
			die;
		}

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
	* Report Quota
	*/

	public function report_quota_site_remaining(){
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;

		// check site using what quota
		// if using top_down, get site_qty_value else get quota initial big boss
		$q = 'SELECT * FROM ms_site WHERE 1';
		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}
		$site = orm_get($q);
		$site = json_decode(json_encode($site),1);

		$q = 'SELECT * FROM ms_user WHERE 1';
		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}
		$q.= ' AND parent_user_id IS NULL';
		$q.= ' AND user_category != "admin"';
		$user = orm_get($q);
		$user = json_decode(json_encode($user),1);

		if($site['method_calc'] == 'bottom_up'){
			$total_quota = $user['quota_initial'];
			$quota_remaining = $user['quota_remaining'];
		} elseif($site['method_calc'] == 'top_down'){
			$total_quota = $site['site_qty_value'];
			$quota_remaining = $user['quota_remaining'];
		}
		
		$result['total_quota'] = $total_quota;
		$result['quota_remaining'] = $quota_remaining;
		
		echo json_encode($result); die;
	}

	public function report_quota_user_site_remaining(){
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;

		// check site using what quota
		// if using top_down, get site_qty_value else get quota initial big boss
		$q = 'SELECT * FROM ms_site WHERE 1';
		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}
		$site = orm_get($q);
		$site = json_decode(json_encode($site),1);

		$q = 'SELECT * FROM ms_user WHERE 1';
		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}
		$q.= ' AND parent_user_id IS NULL';
		$q.= ' AND user_category != "admin"';
		$parent = orm_get($q);
		$parent = json_decode(json_encode($parent),1);

		$q = 'SELECT * FROM ms_user WHERE 1';
		if(isset($attr['site_id']) && $attr['site_id'] != ''){
			$q.= ' AND site_id = '.replace_quote($attr['site_id']);
		}
		if(isset($attr['user_id']) && $attr['user_id'] != ''){
			$q.= ' AND user_id = '.replace_quote($attr['user_id']);
		}
		$user = orm_get($q);
		$user = json_decode(json_encode($user),1);

		// report quota user and site
		if($site['method_calc'] == 'bottom_up'){
			$total_quota = $parent['quota_initial'];
			$quota_remaining = $user['quota_remaining'];
		} elseif($site['method_calc'] == 'top_down'){
			$total_quota = $site['site_qty_value'];
			$quota_remaining = $user['quota_remaining'];
		}
		
		$result['total_quota'] = $total_quota;
		$result['quota_remaining'] = $quota_remaining;
		
		echo json_encode($result); die;
	}
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}