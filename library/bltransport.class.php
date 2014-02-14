<?php

include_once(dirname(__FILE__)  . "/bllog.class.php");
class BLTransport{

	function __construct(){
	}

	protected function callBusinessLogicService($service, $request_params = array(), $method = "GET", $params = array()){
		if (defined("BLSEBE_TRANSPORT")){
			$res = null;
			if (BLSEBE_TRANSPORT == "local"){
				$res = $this->callBusinessLogicServiceLocal($service, $request_params, $method, $params);
			} else {
				$res = $this->callBusinessLogicServiceHttp($service, $request_params, $method, $params);
			}
			if (!is_array($res) && is_string($res)){
				//print "<pre>";  var_dump($res) ; print  "</pre>";
				//print "<pre>" . print_r($res, true) . "</pre>";
				if (substr($res, 0, 9) == "Exception"){
					// print "<pre>" . print_r($res, true) . "</pre>"; exit;
				}
			}
			return $res;
		} else {
			throw new Exception ("BLSEBE_TRANSPORT is not defined.");
		}
	}
	
	private function callBusinessLogicServiceLocal($service, $request_params=array(), $method="GET", $params=array()){
		global $_SESSION;
		if (isset($_SESSION["lang"])){
			$request_params["lang"] = $_SESSION["lang"]; // Multilanguage stuff if available
		}

		$_ENV["PROJECT_HOME"] = BLSBE_PROJECT_HOME;
		$_blsbe_bootstrap_file = BLSBE_BOOTSTRAP_FILE;
		if (file_exists($_blsbe_bootstrap_file)){
			require_once($_blsbe_bootstrap_file);
		}
		$context = array();
		$context["env"]["REQUEST_METHOD"] = "GET";
		$context["env"]["REQUEST_URI"] = $service;
		$context["env"]["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];

		if ($_SESSION["user"]){
			$context["env"]["HTTP_X_CALLER_UNAME"] = "U:" . $_SESSION["user"]["email"];
			$context["env"]["HTTP_X_CALLER_UID"] = "U:" . $_SESSION["user"]["user_id"];
		} else {
			if ($_SESSION["companyData"]){
				$context["env"]["HTTP_X_CALLER_UNAME"] = "C:" . $_SESSION["companyData"]["email"];
				$context["env"]["HTTP_X_CALLER_UID"] = "C:" . $_SESSION["companyData"]["id"];
			}
		}

		$context["_GET"] = $request_params;
		$app = new BLS_Router();
		$res = $app($context);
		if ($res[0] == 200){
			return unserialize($res[2]);
		}
	}

	private function callBusinessLogicServiceHttp($service, $request_params=array(), $method="GET", $params=array()){
		//protected function callBusinessLogicService($service, $arguments){
		global $_SESSION;
		if (isset($_SESSION["lang"])){
			$request_params["lang"] = $_SESSION["lang"]; // Multilanguage stuff if available
		}

		$action_url = BLSERVER_URL . "/" . $service;
		$params["request_data"] = $request_params;

		if (defined("BLSERVER_SHARED_KEY")){
			$params["headers"]["X-SHARED-KEY"] = BLSERVER_SHARED_KEY;
		}

		if ($_SESSION["user"]){
			if (isset($_SESSION["user"]["email"])){
				$params["headers"]["X-CALLER-UNAME"] = "U:" . $_SESSION["user"]["email"];
			}
			if(isset($_SESSION["user"]["user_id"])){
				$params["headers"]["X-CALLER-UID"] = "U:" . $_SESSION["user"]["user_id"];
			}
		} else {
			if ($_SESSION["companyData"]){ // Extranet
				$params["headers"]["X-CALLER-UNAME"] = "C:" . $_SESSION["companyData"]["email"];
				$params["headers"]["X-CALLER-UID"]   = "C:" . $_SESSION["companyData"]["id"];
			}
		}
		//print "<p>Calling $action_url</p><pre>" . print_r($request_params, true) . "</pre>";

		if ($method == 'POST') {
			$response = $this->http_post_request($action_url, $params);
		} else {
			$response = $this->http_get_request($action_url, $params);
		}

		if (isset($params["no_unserialize"]) && $params["no_unserialize"]) {
			return $response;
		}

		$response = trim($response);
		$aRet = unserialize($response);

		if( ($aRet === false) && ($response !== serialize(false)) ) {
			syslog(LOG_DEBUG, "callBusinessLogicService: ERROR: Could not unserialize response: " . $response);
			return false;
		}

		return $aRet;
	}

	function http_post_request($url, $params, $http_params = array()) {
		return $this->http_request($url,'POST', $params, $http_params);
	}

	function http_get_request($url, $params, $http_params = array()) {
		return $this->http_request($url,'GET', $params, $http_params);
	}
	

	function http_request($url, $method, $params, $http_params=array()) {
		
		if (defined('ALWAYS_REQUIRE_COMPANY_ID')){
			if (!isset($params['request_data']['company_id'])){
				if (isset($_SESSION['company_id'])){
					$params['request_data']['company_id'] = $_SESSION['company_id'];
				} else {
					$params['request_data']['company_id'] = 0;
					// throw new Exception("Cannot call backend without the company_id in $url<pre>" . print_r($params, true) . "</pre>");
				}
			} else {
				$_SESSION['company_id'] = $params['request_data']['company_id'] ;
			}
			//print "<pre>$url" . print_r($params, true) . "</pre>";
		}
		// Http stream options
		// See http://www.php.net/manual/en/context.http.php
		$headers = array();
		$headers['Accept-language'] = 'en';
		
		if (isset($params["headers"])) {
			foreach($params["headers"] as $key => $val){
				$headers[$key] = $val;
			}
		}
		
		$header_str = "";
		
		foreach($headers as $key => $val){
			$header_str .= $key . ": " . $val . "\r\n";
		}

	    $options = array( 
	          'http' => array( 
	            'method' => $method, 
	            'header' => $header_str
             ) 
       ); 

		if (isset($http_params["timeout"])){
			// This can be used to set a long timeout when called from the CLI based daemon
			$options["http"]["timeout"] = $http_params["timeout"];
		}

		if (isset($params['request_data'])){
			$url .= '?';
			
			foreach($params['request_data'] as $var => $val){
				if (empty($var)) {
					continue;
				}
				if (is_array($val)) {
					// print "$var: <pre>" . print_r($val, true) . "</pre>";
					foreach($val as $_array_key => $_array_value ) {
						 // print "Setting $var  [$_array_key] to $_array_value<br>";
						$url .= $var . '[' . $_array_key .  ']=' . urlencode($_array_value) . '&';
					}

				} else {
					// print "Setting $var to $val<br>";
					$url .= $var . '=' . urlencode($val) . '&';

				}
			}
		}
		// print "url is $url<br>";
	    $context = stream_context_create($options); 
	    $response = file_get_contents($url, false, $context);
		if (!$response) {
			return false;
		}
		
		$result = trim($response);
		return $result;
	}


}