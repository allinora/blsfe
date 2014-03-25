<?php

include_once(dirname(__FILE__)  . "/bllog.class.php");
include_once(dirname(__FILE__)  . "/httptransport.class.php");
class ApiTransport extends HttpTransport{

	function __construct(){
	}

	protected function callApiService($service, $request_params=array(), $method="GET", $params=array()){
		//protected function callBusinessLogicService($service, $arguments){
		global $_SESSION;
		if (isset($_SESSION["lang"])){
			$request_params["lang"] = $_SESSION["lang"]; // Multilanguage stuff if available
		}

		if (isset($_ENV['API_SHARED_KEY'])){
			$request_params["sharedKey"] = $_ENV['API_SHARED_KEY'];
		}
		if (!isset($_ENV['urls']['api'])){
			$this->sendError("danger", "Local", 'api url is not defined');
			
		}
		
		$_ENV['urls']['api'] = preg_replace("@/$@", "", $_ENV['urls']['api']);
		$action_url = $_ENV['urls']['api'] . "/" . $service;
		
		$params["request_data"] = $request_params;

		if ($method == 'POST') {
			$response = $this->http_post_request($action_url, $params);
		} else {
			$response = $this->http_get_request($action_url, $params);
		}


		$response = trim($response);
		$oRet = json_decode($response);

		if ($oRet->error) {
			$this->sendError("danger", "API", $oRet->error_msg[0]);
		}

		return $oRet->data;
	}

}