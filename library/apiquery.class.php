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
	 	print "Calling $action with <pre>" . print_r($params, true) . "</pre>";
		if (!isset($params[0])){
			$params[0] = null;
		}
		if (!isset($params[1])){
			$params[1] = null;
		}
		if (!isset($params[2])){
			$params[2] = null;
		}
        return $this->callApiService($this->_model . "/$action" , $params[0], $params[1] , $params[2]);
	}

}