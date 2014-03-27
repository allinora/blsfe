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
		$service = "/////$service";
		$_ENV['urls']['api'] = $_ENV['urls']['api'] . "//////";
		
		$_ENV['urls']['api'] = preg_replace("@/{1,}$@", "", $_ENV['urls']['api']);
		$service = preg_replace("@^/{1,}@", "", $service);

		$action_url = $_ENV['urls']['api'] . "/" . $service;
		
		//print "Service is $service<br>";
		
		if ($_SESSION['token']){
			$request_params['token'] = $_SESSION['token'];
		}
		
		$request_params['noDataTable'] = true;
		
		$params["request_data"] = $request_params;

		if ($method == 'POST') {
			$response = $this->http_post_request($action_url, $params);
		} else {
			$response = $this->http_get_request($action_url, $params);
		}


		$response = trim($response);
		$oRet = json_decode($response, true);

		if ($oRet->error) {
			$this->sendError("danger", "API", $oRet->error_msg[0]);
		}

		// Handle integer values
		if (isset($oRet['data']['int'])){
			return $oRet['data']['int'];
		}

		return $oRet['data'];
	}
	

}