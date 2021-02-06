<?php 

/*
 * -------------------------------------------------------------------------------------------
 * ORM HELPER
 * -------------------------------------------------------------------------------------------
*/
// Return single data record
function orm_get($query, $column = NULL, $format = NULL)
{
	if (!isset($query)) 
		return 'orm_get error empty string';
	
	$data = NULL;
	$data = DB::select($query);
	if ($data) {
		$data = $data[0];
		if (! empty($column)) $data = $data->$column; 
	} else {
		// debug('Data query error');
		// debug($query,1);
		$data = NULL;
	}
	// if set return single column else return array of columns
	// $data = json_decode(json_encode($data),1);
	if (isset($format) && $format == 'json') {
		$data = json_encode($data);
	} else if ($format== 'array') {
		$data = json_decode(json_encode($data),1);
	}
	
	return $data;
}

// Return list data record
function orm_get_list($query, $format = NULL)
{
	if (!isset($query)) 
		return 'orm_get_list error empty string';
	
	$data = NULL;
	$data = DB::select($query);
	
	// $data = json_decode(json_encode($data),1);

	if (isset($format) && $format == 'json') {
		$data = json_encode($data);
	} else if ($format== 'array') {
		$data = json_decode(json_encode($data),1);
	}
		
	return $data;
}

// Return save data record
function orm_save($table, $param) {
	$last_id = DB::table($table)->insertGetId($param);	
	return $last_id;
}

// Return save data record
function save_logapi($param) {
	$last_id = orm_save('tr_log',$param);
	return $last_id;
}

function debug($arr,$is_die = false)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    if ($is_die) die;
}

/*
 *  returns SQL with values in it
 */
function getSql($model)
{
    $replace = function ($sql, $bindings)
    {
        $needle = '?';
        foreach ($bindings as $replace){
            $pos = strpos($sql, $needle);
            if ($pos !== false) {
                if (gettype($replace) === "string") {
                     $replace = ' "'.addslashes($replace).'" ';
                }
                $sql = substr_replace($sql, $replace, $pos, strlen($needle));
            }
        }
        return $sql;
    };
    $sql = $replace($model->toSql(), $model->getBindings());

    return $sql;
}

function replace_quote($str, $type='str')
{
	if ($type == 'like') {
		$str = "'%" . $str . "%'";
	} else {
		
		$str = "'" . $str . "'";
	}
	
	return $str;
}

/*
 * Remove all column not registered in $arrsource
 * --------------------------------------
 * return: array - array of invalid parameter submitted by user. ex: array()
 * --------------------------------------
 * @arrsource : array of string. 
 * @arrtarget : array of string. 
*/
function validate_column($arrsource,$arrtarget) {
	
	if (empty($arrsource) || empty($arrtarget)) {
		return 'helper error: validate_column error parameter';
	}	
	
	$temp = NULL;
	foreach ($arrsource as $rs) {
		if (isset($arrtarget[$rs])) $temp[$rs] = $arrtarget[$rs];
	}
	
	return $temp;
}

/*
 * Check all column required whether fulfilled or not.(must fill all required_column)
 * --------------------------------------
 * return: string - string of all invalid parameter submitted by user, ex: user,role
 * --------------------------------------
 * @arrsource : array of string . 
 * @arrtarget : array of string postdata. 
*/
function validate_required_column($arrsource,$arrtarget) {
	
	if (empty($arrsource) || empty($arrtarget)) {
		return 'helper error: validate_required_column error parameter';
	}	
	
	$temp = NULL;
	foreach ($arrsource as $rs) {
		if (! isset($arrtarget[$rs]) || $arrtarget[$rs] == '') {
			$temp[] = $rs;
		}
	}

	// merge list of array into string
	if (! empty($temp)) $temp = implode(',',$temp);

	return $temp;
}

function curl_api_liquid($url, $method = 'get', $attr = NULL, $data = NULL)
{
	$httpheader = $param = array();
	// $httpheader[] = 'Token: macbook';
	
	// Get all API_KEY from config .env files
	if (env('API_KEY')) {
		$httpheader[] = 'Token: '.env('API_KEY');
	} else {
		if (isset($attr['token'])) $httpheader[] = 'Token: '.$attr['token'];
	}
	// $httpheader[] = 'Secretkey: grevia';
	
	
	// Activate debug
	if (isset($attr['debug'])) $param['debug'] = $attr['debug'];
	// if (isset($attr['method'])) $param['method'] = $attr['method'];

	$param['httpheader'] = $httpheader;
	$param['useragent'] = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36';
	
	// debug($url);
	// debug($method);
	// debug($param);
	// debug($data);
	
	$return = curl($url, $method, $param, $data);
	// debug($return,1);
	
	// if (isset($attr['debug'])) debug($return,1);
	
	return $return;
}


function curl($url, $method = 'get', $attr = NULL, $data = NULL)
{
	$ch = curl_init();
	
	// curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
	
	if (isset($attr['useragent'])) {
		curl_setopt($ch, CURLOPT_USERAGENT,$attr['useragent']);
	}
	
	if (isset($attr['httpheader'])) {
		curl_setopt($ch, CURLOPT_HTTPHEADER,$attr['httpheader']);
	}
	// curl_setopt($ch, CURLOPTGET, true);	
	//curl_setopt($ch, CURLOPT_POST, true);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, "username=XXXXX&password=XXXXX");
	
	// // Using proxy
	// $arr_proxy = array(
		// // '187.44.1.167:8080', // BZ
		// // '123.205.131.69:21776', // taiwan
		// // '139.59.207.66:8080', // SG
		// // '163.47.11.113:3128', // SG
		
		// '36.67.85.3:8080', // all local indo proxy
		// '150.107.251.26:8080',
		// '182.30.225.36:8080',
		// '182.253.130.178:8080',
		// // '',
	// );
	// curl_setopt($ch, CURLOPT_PROXY , $arr_proxy[mt_rand(0,count($arr_proxy)-1)]);     // return web page
	
	// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);     // return web page
	curl_setopt($ch, CURLOPT_HEADER         , false);    // don't return headers
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);     // follow redirects
	curl_setopt($ch, CURLOPT_ENCODING       , "");       // handle all encodings
	curl_setopt($ch, CURLOPT_USERAGENT      , "spider"); // who am i
	curl_setopt($ch, CURLOPT_AUTOREFERER    , true);     // set referer on redirect
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 120);      // timeout on connect
	curl_setopt($ch, CURLOPT_TIMEOUT        , 120);      // timeout on response
	curl_setopt($ch, CURLOPT_MAXREDIRS      , 10);       // stop after 10 redirects
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);    // Disabled SSL Cert checks
	curl_setopt($ch, CURLOPT_VERBOSE 		, true);     // Show info
	
	
	// get, post, put, delete
	if (isset($method)) {		
		
		$method = strtolower($method);
		
		$post_arr = array('post','put','delete');
		
		if (in_array($method,$post_arr)) {
			
			if ($method == 'post') curl_setopt($ch, CURLOPT_POST, true);
			else if ($method == 'put') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			else if ($method == 'delete') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			
			if (! empty($data)) curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
			
		} else if ($method == 'get') {
			
			if (! empty($data)) $url .= '?'.http_build_query($data);
		}
	} else {
		// method get
		if (! empty($data)) $url .= '?'.http_build_query($data);
	}
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	if (isset($attr['debug'])) {
		// debugging process
		// $resp['parameter'] = $data;
		// $resp['output'] = curl_exec($ch);
		$resp['output'] = curl_exec($ch);
		$resp['info'] = curl_getinfo($ch);
		$resp['error'] = curl_error($ch);
		$resp['info']['passing_header'] = $attr['httpheader'];
		$resp['info']['passing_data'] = $data;
		debug($resp,1);
	} else {
		
		// Normal process
		$resp = curl_exec($ch);
	}
	
	if (curl_error($ch)) {
		echo curl_error($ch);
	}
	curl_close($ch);
	
	if (! isset($resp)) $resp = NULL;
	
	return $resp;
}

function get_datetime(){
	date_default_timezone_set("Asia/Bangkok");
	return date("Y-m-d H:i:s", time());
}

function get_right($string,$chars){
	$str = substr($string, strlen($string)-$chars,$chars);
	return $str;  
}
function get_left($string,$chars){
	$str = substr($string,0,$chars);
	return $str;
}


?>