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

use App\Models\RoleCapabilityModel;

class RoleCapabilityController extends ApiController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
	| controller as you wish. It is just here to get your app started!
	|
    */
    public $table = 'ms_role_capability';
    public $primary_key = 'role_capability_id';
    public $list_column = array('role_capability_id','role_id','capability_id','create','read','update','delete','status', 'created_at', 'created_by','created_ip','updated_at','updated_by','updated_ip');
	
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
		// $log = ArticleModel::where('capability_id',3)
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
		
		if (isset($attr['role_capability_id']) && $attr['role_capability_id'] != '') {
			$q.= ' AND role_capability_id = '.$attr['role_capability_id'];
		}
		
		if (isset($attr['capability_id']) && $attr['capability_id'] != '') {
			$q.= ' AND capability_id = '.$attr['capability_id'];
		}
		
		if (isset($attr['role_id']) && $attr['role_id'] != '') {
			$q.= ' AND role_id = '.$attr['role_id'];
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
		SELECT rc.role_capability_id,rc.capability_id, r.role_name, rc.`create`, rc.`read`, rc.`update`, rc.`delete`, rc.status, rc.created_at, rc.created_by, rc.updated_at, rc.updated_by
		FROM ' . $this->table . ' rc
		LEFT JOIN ms_role r USING(role_id)
		WHERE 1';
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' rc.role_capability_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR rc.role_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR rc.capability_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
		
		if (isset($attr['capability_id']) && $attr['capability_id'] != '') {
			$q.= ' AND rc.capability_id = '.$attr['capability_id'];
        }
		
		if (isset($attr['role_capability_id']) && $attr['role_capability_id'] != '') {
			$q.= ' AND rc.role_capability_id = '.$attr['role_capability_id'];
        }
		
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND rc.status = '.$attr['status'];
        } else {
			$q.= ' AND rc.status != -1';
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

	/*
	* Siap2 pake query ini untuk get list role capability dengan priority 1
	SELECT rc.role_capability_id, MAX(rc.create) `create`, MAX(rc.read) `read`, MAX(rc.update) `update`, MAX(rc.delete) `delete`
	FROM ms_role r 
	LEFT JOIN ms_role_capability rc USING(role_id)
	WHERE 1 AND rc.role_capability_id IS NOT NULL
	GROUP BY rc.capability_id
	*/
	public function get_list_detail_buggy()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = '
		SELECT rc.role_capability_id, c.capability_id as capability_id, c.capability as capability, r.role_name, MAX(rc.`create`) `create`, MAX(rc.`read`) `read`, MAX(rc.`update`) `update` ,MAX(rc.`delete`) `delete`, ur.role_id as role_id, ur.user_id as user_id, ur.status as ur_status, rc.status as rc_status
		FROM ' . $this->table .' rc 
		LEFT JOIN ms_role r USING(role_id)
        LEFT JOIN ms_capability c USING(capability_id)
        LEFT JOIN ms_user_role ur ON ur.role_id = r.role_id
        WHERE 1';

        $q.= ' GROUP BY capability_id, role_id ';
        $q.= ' HAVING 1';
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' role_name LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR c.capability LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR capability_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
		
		// no use harusnya
		// if (isset($attr['capability_id']) && $attr['capability_id'] != '') {
		// 	$q.= ' AND capability_id = '.$attr['capability_id'];
		// }
        
        if (isset($attr['user_id']) && $attr['user_id'] != '') {
			$q.= ' AND user_id = '.$attr['user_id'];
        }
        

        // deprecated due to invalid expected result on 21 oct 2019
		if (isset($attr['role_id']) && $attr['role_id'] != '') {
			$q.= ' AND role_id = '.$attr['role_id'];
        }
		
		// no use harusnya
		// if (isset($attr['role_capability_id']) && $attr['role_capability_id'] != '') {
		// 	$q.= ' AND role_capability_id = '.$attr['role_capability_id'];
		// }
		// get list list_unavail_menu
		if (isset($attr['list_unavail_menu']) && $attr['list_unavail_menu'] != '') {
			$q.= ' AND (( `read` = 0 ) AND ( `create` = 0  AND `update` = 0 AND `delete` = 0 ))';
        }
		
		if (isset($attr['list_avail_menu']) && $attr['list_avail_menu'] != '') {
			$q.= ' AND (( `read` = 1 ) OR ( `create` = 1 OR `update` = 1 OR `delete` = 1 ))';
        }
        
        // get active user_role status due user can have multiple user_role
        $q.= ' AND ur_status != -1';

		if (isset($attr['status']) && in_array($attr['status'],array('-1','0','1'))) {
			$q.= ' AND rc_status = '.$attr['status'];
        } else {
			$q.= ' AND rc_status != -1';
        }
        
        
        $result['total_rows'] = count(orm_get_list($q));
        // debug($q,1);
        
		
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
    
    public function get_list_detail()
	{
		$attr = $result = NULL;
		if (! empty($_GET)) $attr = $_GET;
			
		$q = '
		SELECT rc.role_capability_id, c.capability_id, c.capability as capability, r.role_name, rc.`create`, rc.`read`, rc.`update` ,rc.`delete`
		FROM ' . $this->table .' rc 
		LEFT JOIN ms_role r USING(role_id)
		LEFT JOIN ms_capability c USING(capability_id)
		WHERE 1';
		
		if (isset($attr['keyword']) && $attr['keyword'] != '') {
			$q.= ' AND ( ';
			$q.= ' role_name LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ' OR c.capability LIKE '.replace_quote($attr['keyword'],'like');
			// $q.= ' OR capability_id LIKE '.replace_quote($attr['keyword'],'like');
			$q.= ')';
        }
		
		// no use harusnya
		// if (isset($attr['capability_id']) && $attr['capability_id'] != '') {
		// 	$q.= ' AND capability_id = '.$attr['capability_id'];
		// }
		
		if (isset($attr['role_id']) && $attr['role_id'] != '') {
			$q.= ' AND role_id = '.$attr['role_id'];
        }
		
		// no use harusnya
		// if (isset($attr['role_capability_id']) && $attr['role_capability_id'] != '') {
		// 	$q.= ' AND role_capability_id = '.$attr['role_capability_id'];
		// }
		// get list list_unavail_menu
		if (isset($attr['list_unavail_menu']) && $attr['list_unavail_menu'] != '') {
			$q.= ' AND (( `read` = 0 ) AND ( `create` = 0  AND `update` = 0 AND `delete` = 0 ))';
        }
		
		if (isset($attr['list_avail_menu']) && $attr['list_avail_menu'] != '') {
			$q.= ' AND (( `read` = 1 ) OR ( `create` = 1 OR `update` = 1 OR `delete` = 1 ))';
        }
		
		if (isset($attr['status']) && in_array(array(-1,0,1),$attr['status'])) {
			$q.= ' AND rc.status = '.$attr['status'];
        } else {
			$q.= ' AND rc.status != -1';
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

	// Insert all role capability
	public function cron_insert_role()
	{
		// debug($_GET,1);
		$get = $attr = $result = NULL;
		if (! empty($_GET)) $get = $_GET;

		if (! isset($get['role_id'])) {
			$message = 'role_id harus diisi';
			$result['message'] = $message;
			echo json_encode($result);
			die;
		}
		// $attr = NULL;
		
		$result['is_success'] = 'on progress';
		$result['message'] = 'under maintenance';
		
		$q = '
		INSERT IGNORE INTO ms_role_capability(role_id,capability_id,created_at) SELECT "' . $get['role_id'] . '", c.capability_id, now()
		FROM ms_capability c 
		WHERE 1 AND c.`status` = 1
		';

		$save = DB::insert($q);
		// $q = '
		// INSERT INTO ms_role_capability(role_id,capability_id,`create`,`read`,`update`,`delete`) 
		// VALUES(1,4,0,0,1,1) ON DUPLICATE KEY UPDATE `create` = VALUES(`create`), `update` = VALUES(`update`)
		// ';

        echo json_encode($save); 
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
    
    // Insert all role capability
	public function update_bulk()
	{
		// debug($_GET,1);
		$post = $attr = $result = NULL;
        if (! empty($_POST)) $post = $_POST;
        
        if (! isset($post['rcid'])) {
			$message = 'data harus diisi';
			$result['message'] = $message;
			echo json_encode($result);
			die;
		}

		if (! isset($post['role_id'])) {
			$message = 'role_id harus diisi';
			$result['message'] = $message;
			echo json_encode($result);
			die;
        }
        
        // if (! isset($post['role_capability_id'])) {
		// 	$message = 'role_capability_id harus diisi';
		// 	$result['message'] = $message;
		// 	echo json_encode($result);
		// 	die;
		// }
		// $attr = NULL;
		
		// $result['is_success'] = 'on progress';
        // $result['message'] = 'under maintenance';
        /*
        SAMPLE

        SELECT * FROM ms_role_capability r WHERE r.role_capability_id IN(66,67);

        INSERT INTO ms_role_capability(role_capability_id,`create`,`read`,`update`,`delete`) 
        VALUES
        (66,1,0,0,0), 
        (67,1,1,1,0)
        ON DUPLICATE KEY UPDATE updated_at = now(), 
        `create`  = VALUES(`create`), `read`  = VALUES(`read`), `update`  = VALUES(`update`), `delete`  = VALUES(`delete`), updated_at = now()
        */
		
		$q = "
        INSERT IGNORE INTO ms_role_capability(role_id,capability_id,`create`,`read`,`update`,`delete`) VALUES";

        if (!empty($post['rcid'])) {
            $i = 1;
            foreach ($post['rcid'] as $key => $rs) {
                $create = $read = $edit = $delete = $capability_id = 0;
                
                // debug($rs['edit'],1);
                if (isset($post['role_id'])) $role_id = $post['role_id'];
                if (isset($rs['capability_id'])) $capability_id = $rs['capability_id'];
                if (isset($rs['create'])) $create = $rs['create'];
                if (isset($rs['read'])) $read = $rs['read'];
                if (isset($rs['edit'])) $edit = $rs['edit'];
                if (isset($rs['delete'])) $delete = $rs['delete'];

                $q .= " (" . $role_id . "," . $capability_id . "," . $create . "," . $read . "," . $edit . "," . $delete . ")";

                if ($i != count($post['rcid'])) $q .= ",";
                $i++;
            }
        }

        $q .= " ON DUPLICATE KEY UPDATE `create`  = VALUES(`create`), `read`  = VALUES(`read`), `update`  = VALUES(`update`), `delete`  = VALUES(`delete`), updated_at = now();
        ";
        
        // debug($q,1);

		$save = DB::insert($q);
		// $q = '
		// INSERT INTO ms_role_capability(role_id,capability_id,`create`,`read`,`update`,`delete`) 
		// VALUES(1,4,0,0,1,1) ON DUPLICATE KEY UPDATE `create` = VALUES(`create`), `update` = VALUES(`update`)
        // ';
        if ($save) {
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
	
	public function __destruct()
	{
		// parent::__construct();
	}
	
}