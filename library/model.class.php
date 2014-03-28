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
			// print "Getting models from $_url<br>";
			$aResult = file_get_contents($_url);
			$aModels = json_decode($aResult, true);
			$this->cacheSet("models.php", $aModels);
		}
		//print "<pre>Models" . print_r($aModels, true) . "</pre>";exit;
		
		$this->aModels = $aModels;
		
		if (!isset($aModels[$model])){
			throw new Exception("Could not find this model: <b>$model</b>");
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
			// print "<pre>" . print_r($aModels[$model], true)  . "</pre>";
			// $this->model = new BLModel($aModels[$model]['backend'][0], $aModels[$model]['backend'][1], $aModels[$model]['backend'][2]);
			$this->model = new BLModel($aModels[$model]['backend']);
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
		$_action = $this->model() . '/' . $action;
		
		if (MODEL_TYPE == 'API'){
			$action = $this->model() . '/' . $action;
		}
 		// print "Model is " . $this->model() . "<br>";
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
		
		
		// Pass some valuable data
		$this->autoSetValues($params);
		
		// print "Params<pre>" . print_r($params, true) . "</pre>";
        return $this->model->$action($params[0], $params[1] , $params[2]);
	}
	
	
	function cacheGet($key){
		return FALSE;
		$cache_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $key;
		if (file_exists($cache_file)){
			return unserialize(file_get_contents($cache_file));
		}
		return FALSE;
	}
	
	function cacheSet($key, $data){
		return FALSE;
		$cache_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $key;
		file_put_contents($cache_file, serialize($data));
		
	}
	
	function autoSetValue($params){
		
		if (isset($params["hmm_you_seem_to_have_been_here_before"] )){
			throw new Excetion("Euuuu. You seem to have been here before");
		}
		$params["hmm_you_seem_to_have_been_here_before"] = 1;
		
		if (isset($_SESSION["data"]["userData"]["user_id"])) {
			if (!isset($params[0]["client_id"])){
				$params[0]["client_id"] = $_SESSION["data"]["userData"]["user_id"];
				$params[0]["sender_id"] = $_SESSION["data"]["userData"]["user_id"];
			}
		}

		if (isset($_SESSION["data"]["profileData"])) {
			$params[0]["sender_name"] = $_SESSION["data"]["profileData"]["firstname"] . ' ' . $_SESSION["data"]["profileData"]["lastname"];
		}
		
		if ($_SESSION["token"]) {
			$params[0]["token"] = $_SESSION["token"];
		}
		
		if (defined('LANG')){
			$params[0]["lang"] = LANG;
		}
		
	return $params;
		
	}
}
