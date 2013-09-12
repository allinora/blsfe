<?php

include_once(dirname(__FILE__)  . "/bllog.class.php");
include_once(dirname(__FILE__) . "/bltransport.class.php");
class BLQuery extends BLTransport{
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

		// print "Calling $action with <pre>" . print_r($params, true) . "</pre>";
		
		
        return $this->callBusinessLogicService($this->_model . "/$action" , $params[0], $params[1] , $params[2]);
	}

    public function get($id){
		// Always uses GET method. Is Cacheable
		$key=$this->_model . DS . __CLASS__ . DS . __FUNCTION__ . DS .  "$id";
		if ($this->cache()){
			// Caching reading allowed for this model
			$result=$this->cache->read($key);
			if (!empty($result)){
				return $result;
			}
		}
        $result=$this->callBusinessLogicService($this->_model . "/get" , array($this->_idField => $id) );
		$this->cache->write($key, $result);
		return $result;
    }

    public function getall($id){
		// Always uses GET method. Is Cacheable
		$key=$this->_model . DS . __CLASS__ . DS . __FUNCTION__ . DS .  "$id";
		if ($this->cache()){
			// Caching reading allowed for this model
			$result=$this->cache->read($key);
			if (!empty($result)){
				return $result['rows'];
			}
		}
        $result=$this->callBusinessLogicService($this->_model . "/getall" , array($this->_searchField => $id) );
		$this->cache->write($key, $result);
		return $result['rows'];
    }

	public function set($params, $method="POST", $extraParams=array()){
		// No cache for set
        $result=$this->callBusinessLogicService($this->_model . "/set" , $params, "POST", $extraParams);
		return $result;
	}

	public function add($params, $method="POST", $extraParams=array()){
		// No cache for add
        $result=$this->callBusinessLogicService($this->_model . "/add" , $params, "POST", $extraParams);
		return $result;
	}

	public function delete($id){
		// No cache for delete, but must uncache the object if on localcache
		
        $result=$this->callBusinessLogicService($this->_model . "/delete" , array($this->_idField => $id));
		return $result;
	}







}