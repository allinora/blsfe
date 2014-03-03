<?php

include_once(dirname(__FILE__)  . "/httptransport.class.php");
include_once(dirname(__FILE__)  . "/bllog.class.php");
class BLTransport extends HttpTransport{

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
		
		//print "<pre>" . print_r($res, true) . "</pre>";
		if ($res[0] == 200){
			if (substr($res[2], 0, 30) == "SCGIServer Uncaught Exception:") {
				$this->sendError("danger", "Backend", $res[2]);
			}
			$retData = unserialize($res[2]);
			if (!empty($retData)  && is_string($retData) && substr($retData, 0, 10) == "Exception:") {
				$this->sendError("danger", "Backend", $retData);
			}
			
			return $retData;
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

		if (isset($_SESSION["user"])){
			if (isset($_SESSION["user"]["email"])){
				$params["headers"]["X-CALLER-UNAME"] = "U:" . $_SESSION["user"]["email"];
			}
			if(isset($_SESSION["user"]["user_id"])){
				$params["headers"]["X-CALLER-UID"] = "U:" . $_SESSION["user"]["user_id"];
			}
		} else {
			if (isset($_SESSION["companyData"])){ // Extranet
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

}