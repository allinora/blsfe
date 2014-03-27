<?php

class Model {
	protected $_model;
	protected $_idField;
	protected $_searchField;
	protected $_cached=true;

	public $debug = FALSE;

	function __construct($model, $_idF="id", $_searchF=null) {
		if (!defined('MODEL_TYPE')){
			throw new Exception("MODEL_TYPE is not defined");
		}
		$this->_model = $model;

		/*
		$modelsMapFile = ROOT . "/config/models.php";
		if (!file_exists($modelsMapFile)){
			throw new Exception("File not found: " . $modelsMapFile);
		}
		*/
		
		if (!isset($_ENV['urls']['api'])){
			throw new Exception("URL to the API is not defined");
		}
		
		
		if (!$aModels = $this->cacheGet("models.php")){
			
			$_url = $_ENV['urls']['api'];
			$_url = preg_replace("@/$@", "", $_url);
			$_url .= "/api/models";
			
			$aResult = file_get_contents($_url);
			$aModels = json_decode($aResult, true);
			$this->cacheSet("models.php", $aModels);
		}
		// print "<pre>Models" . print_r($aModels, true) . "</pre>";exit;
		
		$this->aModels = $aModels;
		
		if (!isset($aModels[$model])){
			throw new Exception("Could not find this model");
		}
		// print "<pre>" . print_r($aModels, true) . "</pre>";
		if (MODEL_TYPE == 'API'){
			$this->model = new ApiModel($aModels[$model]['api'], $_idF, $_searchF);
		}
		if (MODEL_TYPE == 'BACKEND'){
			if (!isset($aModels[$model]['backend'][1])){
				$aModels[$model]['backend'][1] = null;
			}
			if (!isset($aModels[$model]['backend'][2])){
				$aModels[$model]['backend'][2] = null;
			}
			$this->model = new BLModel($aModels[$model]['backend'][0], $aModels[$model]['backend'][1], $aModels[$model]['backend'][2]);
		}
	}

	function cache($x=null){
		if (isset($x)){
			$this->_cached=$x;
		} else {
			return $this->_cached;
		}
		
	}
	function model(){
		return $this->_model;
	}

	function idField(){
		return $this->_idField;
	}
	
	function __destruct() {
	}


	public function __call($action, $params){
		// print "Action<pre>" . print_r($action, true) . "</pre>";
		// print "Params<pre>" . print_r($params, true) . "</pre>";
		if (!isset($params[0])){
			$params[0] = null;
		}
		if (!isset($params[1])){
			$params[1] = null;
		}
		if (!isset($params[2])){
			$params[2] = null;
		}
		
		if (!isset($this->aModels[$this->_model]['methods'][$action])){
			throw new Exception("Dont know how to do $action");
		}
		if (MODEL_TYPE == 'API'){
			$_action = (isset($this->aModels[$this->_model]['methods'][$action]['api'])) ? $this->aModels[$this->_model]['methods'][$action]['api'] : $action;
		}
		if (MODEL_TYPE == 'BACKEND'){
			$_action = (isset($this->aModels[$this->_model]['methods'][$action]['backend'])) ? $this->aModels[$this->_model]['methods'][$action]['backend'] : $action;
		}
        return $this->model->$_action($params[0], $params[1] , $params[2]);
	}
	
	
	function cacheGet($key){
		$cache_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $key;
		if (file_exists($cache_file)){
			return unserialize(file_get_contents($cache_file));
		}
		return FALSE;
	}
	
	function cacheSet($key, $data){
		$cache_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $key;
		file_put_contents($cache_file, serialize($data));
		
	}
}
