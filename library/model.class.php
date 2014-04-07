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
		
		$this->aModels = $aModels;
		
		if (!isset($aModels[$model])){
			throw new Exception("Could not find this model: <b>$model</b>");
		}
		if (MODEL_TYPE == 'API'){
			$this->model = new ApiModel($model, $_idF, $_searchF);
		}
		if (MODEL_TYPE == 'BACKEND'){
			if (!isset($aModels[$model]['backend'][1])){
				$aModels[$model]['backend'][1] = null;
			}
			if (!isset($aModels[$model]['backend'][2])){
				$aModels[$model]['backend'][2] = null;
			}
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
		if (in_array($action, array("get", "getall", "delete")) && isset($params[0]) && is_numeric($params[0])){
			$params[0] = ['id'=> $params[0]];
		}
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
	
	function doLogin($login, $password) {
		if (MODEL_TYPE == "API"){
			$authData = $this->login(array('username' => $login, 'password' => $password));
		} else {
			$authData = $this->login(array('login' => $login, 'passwd' => $password));
		}
		if ($authData["token"]) {
			return $this->authenticateToken($authData["token"]);
		} else {
			return false;
		}
	}
	
	function authenticateToken($token){
		$data = $this->getTokenData(array('token' => $token));
		if (isset($data["userData"])){
			$this->setSession('token', $token);
			$this->setSession("data", $data);
			$this->setSession("company_id", $data['userData']['company_id']);
			return true;
		} else {
			return false;
		}
	}
	
	private function setSession($key, $value){
		$_SESSION[$key] = $value;
	}
	
	function autoSetValues(&$params){
		//  print "<pre>" . print_r($params, true) . "</pre>";
		if (!is_array($params[0])){
			throw new Exception("params[0] is not array. This should never happen.");
		}
		if (isset($_SESSION["data"]["userData"]["user_id"])) {
			if (!isset($params[0]["client_id"])){
				$params[0]["client_id"] = $_SESSION["data"]["userData"]["user_id"];
				$params[0]["sender_id"] = $_SESSION["data"]["userData"]["user_id"];
			}
		}

		if (isset($_SESSION["data"]["profileData"])) {
			$params[0]["sender_name"] = $_SESSION["data"]["profileData"]["firstname"] . ' ' . $_SESSION["data"]["profileData"]["lastname"];
		}
		
		if (isset($_SESSION["token"])) {
			$params[0]["token"] = $_SESSION["token"];
		}
		
		if (defined('LANG')){
			$params[0]["lang"] = LANG;
		}
	}
}
