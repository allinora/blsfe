<?php

include_once(dirname(__FILE__)  . "/bllog.class.php");
class BLTransport{

	function __construct(){
	}

    protected function callBusinessLogicService($service, $request_params=array(), $method="GET", $params=array()){
	//protected function callBusinessLogicService($service, $arguments){
        global $_SESSION;
		if (isset($_SESSION["lang"])){
        	$request_params["lang"]=$_SESSION["lang"]; // Multilanguage stuff if available
		}
   
        $action_url=BLSERVER_URL . "/" . $service;
        $params["request_data"]=$request_params;
		if (defined("BLSERVER_SHARED_KEY")){
	        $params["headers"]["X-SHARED-KEY"]=BLSERVER_SHARED_KEY;
		}
		//print "<p>Calling $action_url</p><pre>" . print_r($request_params, true) . "</pre>";
    
        if ($method=="POST") {
            $response=$this->postURL($action_url, $params);
        } else {
            $response=$this->getURL($action_url, $params);
        }
    
        if (isset($params["no_unserialize"]) && $params["no_unserialize"]) {
            return $response;
        }
    
        $response = trim($response);
        $aRet=unserialize($response);
        	 
        if( ($aRet === false) && ($response !== serialize(false)) ) {
        	syslog(LOG_DEBUG, "callBusinessLogicService: ERROR: Could not unserialize response: ".$response);
        	return false;
        }

        return $aRet;
    }


    public function getURL($url, $params=array()){
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

        if (is_array($params["headers"])) {
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


}
