<?php
/**
* Method to run a Backend webservice
*/
function implode_get($array) {
    $first = true;
    $output = '';
    foreach($array as $key => $value) {
        if (is_array($value)) {
            $value=join(',',$value);
        }
        if ($first) {
            $output = '?'.$key.'='.$value;
            $first = false;
        } else {
            $output .= '&'.$key.'='.$value;   
        }
    }
    return $output;
}

function _microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function callBusinessLogicService($service, $request_params=array(), $method="GET", $params=array()){
    global $_SESSION;
    $request_params["lang"]=$_SESSION["lang"];
    $request_params["country"]=$_SESSION["country"];

    $xparams=implode_get($request_params);
    $static_key="$service$xparams";                   
    $static_key_text=$static_key;

    $static_key=rawurlencode($static_key);

    if ($method=="GET" && $params["nocache"]!=1) {
        if ($cache=readCache($static_key)) {
            return $cache;
        }
    }

    // $method can be GET/POST


    $action_url=BL_URL . "/" . $service;
    // print "Action: $action_url";
    $log_message="callBL:$method:LIVE:$static_key_text";
    $time_start = _microtime_float();
	//print "Action url is $action_url<br>";


    //load_constant("MY_IP_ADDRESS");

    $params["request_data"]=$request_params;

    $params["headers"]["X-SHARED-KEY"]=BLS_SHARED_KEY;


    if ($method=="POST") {
        $response=tipi_call_function("postURL", $action_url, $params);
    } else {
        $response=tipi_call_function("getURL", $action_url, $params);
    }
    $time_end = _microtime_float();
    $time_taken = number_format($time_end - $time_start, 4);
    syslog(LOG_DEBUG, "callBL:$time_taken:$suid:$method:LIVE:$static_key_text");

    if ($params["no_unserialize"]) {
        return $response;
    }

    $response = trim($response);
    $aRet=unserialize($response);
    	 
    if( ($aRet === false) && ($response !== serialize(false)) ) {
    	syslog(LOG_DEBUG, "callBusinessLogicService: ERROR: Could not unserialize response: ".$response);
    	return false;
    }
    
    if ($method=="GET") {
        writeCache($static_key, $aRet);
    }
    return $aRet;
}

function _recursive_url_encode($array) {
	if (!is_array($array)) {
		return $array;
	}
	$ret = array();
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			$ret[$key] = _recursive_url_encode($value);
		} else {
			$ret[$key] = urlencode($value);
		}
	}
	
	return $ret;
}

