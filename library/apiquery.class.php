<?php

include_once(dirname(__FILE__)  . "/bllog.class.php");
include_once(dirname(__FILE__) . "/apitransport.class.php");
class ApiQuery extends ApiTransport{
    protected $_result;
	protected $_orderBy;
	protected $_order;
	protected $_page;
	protected $_limit;


	function __construct(){
		global $cache;
		$this->cache=$cache;
	}

	public function __call($action, $params){
		if (!isset($params[0])){
			$params[0] = null;
		}

		if (is_numeric($params[0])){
			if (substr($action, -3) == "get") { // handle special case for get
				$params[0] = array($this->_idField => $params[0]);
			}
			if (substr($action, -6) == "getall") { // handle special case for getall
				$params[0] = array($this->_searchField => $params[0]);
			}
		}
		
		if (!isset($params[1])){
			$params[1] = null;
		}
		if (!isset($params[2])){
			$params[2] = null;
		}
        return $this->callApiService($this->_model . "/$action" , $params[0], $params[1] , $params[2]);
	}


    public function xxget($id, $cache = true){
        $result = $this->callApiService($this->_model . "/get" , array($this->_idField => $id) );
		return $result;
    }

    public function getall($id, $cache = true){
        $result = $this->callApiService($this->_model . "/getall" , array($this->_searchField => $id) );
		return $result['rows'];
    }

	public function set($params, $method="POST", $extraParams=array()){
        $result=$this->callApiService($this->_model . "/set" , $params, "POST", $extraParams);
		return $result;
	}

	public function add($params, $method="POST", $extraParams=array()){
        $result=$this->callApiService($this->_model . "/add" , $params, "POST", $extraParams);
		return $result;
	}

	public function delete($id){
        $result=$this->callApiService($this->_model . "/delete" , array($this->_idField => $id));
		return $result;
	}








}