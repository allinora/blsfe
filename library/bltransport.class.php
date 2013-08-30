<?php

include_once(dirname(__FILE__)  . "/bllog.class.php");
class BLTransport{

	function __construct(){
	}

	protected function callBusinessLogicService($service, $request_params = array(), $method = "GET", $params = array()){
		if (defined("BLSEBE_TRANSPORT")){
			if (BLSEBE_TRANSPORT == "local"){
				return $this->callBusinessLogicServiceLocal($service, $request_params, $method, $params);
			} else {
				return $this->callBusinessLogicServiceHttp($service, $request_params, $method, $params);
			}
		} else {
			return $this->callBusinessLogicServiceHttp($service, $request_params, $method, $params);
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
		syslog(LOG_DEBUG, "callBusinessLogicService: ERROR: Could not unserialize response: ".$response);
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

		// Http stream options
		// See http://www.php.net/manual/en/context.http.php
	    $options = array( 
	          'http' => array( 
	            'method' => $method, 
	            'header' => "Accept-language: en\r\n"
             ) 
       ); 

		if (isset($http_params["timeout"])){
			// This can be used to set a long timeout when called from the CLI based daemon
			$options["http"]["timeout"] = $http_params["timeout"];
		}

		$url.='?';
		foreach($params as $var => $val){
			$url .= $var . '=' . urlencode($val) . '&';
		}
	    $context = stream_context_create($options); 
	    $response = file_get_contents($url, false, $context);
		if (!$response) {
			return false;
		}
		
		$result = trim($response);
		return $result;
	}


   
	/*
	public function getURL($url, $params=array()){
		//print "Calling $url<br> with <br><pre>" . print_r($params, true) . "</pre>";
       //syslog(LOG_DEBUG, "getURL: $url");
       require_once("HTTP/Request.php");
       $req =new HTTP_Request($url);
       $req->setMethod(HTTP_REQUEST_METHOD_GET);
       if (isset($params["auth_data"]) && is_array($params["auth_data"])) {
           $req->setBasicAuth($params["auth_data"]["login"], $params["auth_data"]["pass"]);
       }
       if (is_array($params["request_data"])) {
           foreach ($params["request_data"] as $var=>$val){
               $req->addQueryString($var, $val);
           }
       }

       if (isset($params["headers"]) && is_array($params["headers"])) {
           foreach ($params["headers"] as $var=>$val){
               $req->addHeader($var, $val);
           }
       }


       if (!PEAR::isError($req->sendRequest())) {
           return $req->getResponseBody();
       }
   }

   private function postURL($url, $params=array()){
           require_once("HTTP/Request.php");
           $req = new HTTP_Request($url);
           $req->setMethod(HTTP_REQUEST_METHOD_POST);
   
           if (!$params["options"]["no-multipart"]) {
               $req->addHeader("Content-type","multipart/form-data");
           }
           if (preg_match("/http:\/\/([^\/]*)/", $url, $match)){
               $host=$match[1];
               $req->addHeader("Host",$host);
           }
   
   
           if (is_array($params["headers"])) {
               foreach ($params["headers"] as $var=>$val){
                   $req->addHeader($var, $val);
               }
           }
   
           if (is_array($params["request_data"])) {
   
               foreach ($params["request_data"] as $var=>$val){
                   $req->addPostData($var, $val);
               }
           }
   
           $req->addPostData("Sender", "postURL");
   
   
           if (!PEAR::isError($req->sendRequest())) {
               return $req->getResponseBody();
           }
   }
	*/

}