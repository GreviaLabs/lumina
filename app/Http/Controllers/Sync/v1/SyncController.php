<?php

namespace App\Http\Controllers\Sync\v1;

use App\Http\Controllers\Controller;
use Input;
use Validator;
use Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;

use Request;

class SyncController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Api for controller handler created by rusdi on monday 3 september 2018 14:16
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
		// parent::__construct();
		
		// $this->middleware('guest');
		
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Credentials: *');
		header('Access-Control-Max-Age: 86400');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
		header("Access-Control-Allow-Headers: X-Requested-With");
		
		$this->authToken();
		// echo "yamete blokir";
		// die;
		}
		
		public function authToken() 
		{
			$is_valid = FALSE;
			$header = $token = NULL;
			
			if (Request::header()) $header = Request::header();

			if (isset($header['token'][0])) {
				$token = $header['token'][0];
			}

			if (isset($_GET['token']) && ! isset($token)) {
				$token = $_GET['token'];
			}
			
			if (isset($_POST['token']) && ! isset($token)) {
				$token = $_POST['token'];
			}

			// Disable when production
			// $token = 'macbook';
			if ($token) {
				// $header = Request::header();
				// $token = Request::header('token');
				// debug('mantapjiwa ada token',1);

				if ($token == 'macbook') {
                    $is_valid = TRUE;
                    
                    // save to log every post
                    if (! empty(file_get_contents('php://input'))) {
                        $post = json_decode(file_get_contents('php://input'), 1);
            
                        $jsondata = file_get_contents('php://input');
                        $param['name'] = 'sync post';
                        $param['url'] = Request::segment(1).'/'.Request::segment(2).'/'.Request::segment(3).'/'.Request::segment(4);
                        $param['method'] = 'post';
                        $param['data'] = $jsondata;
                        $param['created_at'] = get_datetime();
                        $save = save_logapi($param);
                    }

				} else {
					// Check token exist and valid
					$q = 'SELECT * FROM ms_token WHERE token = "'.$token.'"';
					$check = orm_get($q);

					// Check token registered and not expired then valid 
					if ($check) {
						$is_valid = TRUE;
					} else {
						$message['message'] = ERROR_SECRETKEY_INVALID;
					}
				}

				// debug($check,1);
				// debug('<hr/>tutup',1);
				
			} else {
				$message['message'] = ERROR_SECRETKEY_NO_EXIST;
			}

			// debug('gokil',1);
			if (! $is_valid) {

				echo json_encode($message);
				die;
			}
			
			
		}
}