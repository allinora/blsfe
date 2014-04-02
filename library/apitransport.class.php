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
		
		$_ENV['urls']['api'] = preg_replace("@/{1,}$@", "", $_ENV['urls']['api']);
		$service = preg_replace("@^/{1,}@", "", $service);
		$action_url = $_ENV['urls']['api'] . "/" . $service;
		
		$request_params['noDataTable'] = true;
		
		$params["request_data"] = $request_params;

		// print "<pre>" . print_r($params, true) . "</pre>";
		if ($method == 'POST') {
			$response = $this->http_post_request($action_url, $params);
		} else {
			$response = $this->http_get_request($action_url, $params);
		}


		$response = trim($response);
		$aRet = json_decode($response, true);
		//print "<pre>" . print_r($aRet, true) . "<pre>";	

			if ($aRet['error'] > 0 ) {
				if ($service != "login/login"){
					$this->sendError("danger", "API", join("<br>", $aRet['error_msg']));
				} else {
					return 0;
				}
			}

		// Handle integer values
		if (isset($aRet['data']['int'])){
			return $aRet['data']['int'];
		}

		return $aRet['data'];
	}
	

}