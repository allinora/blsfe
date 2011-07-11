<?php

class BLQuery {
    protected $_result;
	protected $_orderBy;
	protected $_order;
	protected $_page;
	protected $_limit;


	function __construct(){
		global $cache;
		$this->cache=$cache;
	}

    public function call($action, $params=array(), $method="GET", $extraParams=array()){
        return $this->callBusinessLogicService($this->_model . "/$action" , $params );
    }

    public function get($id){
		// Always uses GET method. Is Cacheable
		$key=$this->_model . DS . __CLASS__ . DS . __FUNCTION__ . DS .  "$id";
		$result=$this->cache->read($key);
		if (!empty($result)){
			return $result;
		}
        $result=$this->callBusinessLogicService($this->_model . "/get" , array($this->_idField => $id) );
		$this->cache->write($key, $result);
		return $result;
    }

    public function getall($id){
		// Always uses GET method. Is Cacheable
		$key=$this->_model . DS . __CLASS__ . DS . __FUNCTION__ . DS .  "$id";
		$result=$this->cache->read($key);
		if (!empty($result)){
			return $result;
		}
        $result=$this->callBusinessLogicService($this->_model . "/getall" , array($this->_searchField => $id) );
		$this->cache->write($key, $result);
		return $result;
    }

	public function set($params, $method="POST", $extraParams){
		// No cache for set
        $result=$this->callBusinessLogicService($this->_model . "/set" , $params, "POST", $extraParams);
		return $result;
	}

	public function add($params, $method="POST", $extraParams){
		// No cache for add
        $result=$this->callBusinessLogicService($this->_model . "/add" , $params, "POST", $extraParams);
		return $result;
	}

	public function delete($id){
		// No cache for delete, but must uncache the object if on localcache
		
        $result=$this->callBusinessLogicService($this->_model . "/set" , array($this->_idField => $id)");
		return $result;
	}






    // The underlying functions...




    private function getURL($url, $params=array()){
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
    
            //print "<pre>" . print_r($params, true) . "</pre>";
    
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

    private function callBusinessLogicService($service, $request_params=array(), $method="GET", $params=array()){
        global $_SESSION;
        $request_params["lang"]=$_SESSION["lang"];
   
    
        $action_url=BLSERVER_URL . "/" . $service;
        $params["request_data"]=$request_params;
        $params["headers"]["X-SHARED-KEY"]=BLSERVER_SHARED_KEY;
    
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


}