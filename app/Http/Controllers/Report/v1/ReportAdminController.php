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

class ReportAdminController extends ReportController {

	/*
	|--------------------------------------------------------------------------
	| Report Admin Controller
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

    // report for list of Company
    public function list_company(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM ms_company
                WHERE 1
                ";
                
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }

    // report for role maintenance
    public function role_maintenance(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM ms_role
                WHERE 1
                ";
                
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }

    // report for manage level
    public function manage_level(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM ms_level
                WHERE 1
                ";
                
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    // report for manage reason
    public function manage_reason(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM ms_reason
                WHERE 1
                ";
                
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    // report for reated site
    public function created_site(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM ms_site
                WHERE 1
                ";
                
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    // report for list of user
    public function list_of_user(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM ms_user
                WHERE 1
                ";
                
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    // report for manage quota
    public function manage_quota(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT SUM(quota_initial) AS total
				FROM ms_user
                WHERE 1
                ";
                
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }
    
    // report for manage reason type
    public function manage_reason_type(){
		$attr = $result = $filter = NULL;

		if (! empty($_GET)) $attr = $_GET;

		$q = "SELECT COUNT(*) AS total
				FROM ms_reason_type
                WHERE 1
                ";
                
        if (isset($attr['site_id']) && $attr['site_id'] != '') {
            $q.= ' AND site_id = '.replace_quote($attr['site_id']);
        }        

        $data = orm_get_list($q);

		if (empty($data)) $data = NULL;

		$result['data'] = $data;

		echo json_encode($result);
		die;
    }

    public function __destruct()
	{
		// parent::__construct();
	}
	
}