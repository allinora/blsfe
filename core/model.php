<?php
include_once(__DIR__ . "/app.php");
class Model_Controller extends App_Controller {

	function beforeAction(){
		parent::beforeAction();
	}
	
	function afterAction(){
		parent::afterAction();
	}
	
	function logoutAction(){
		$_SESSION = array();
		unset($_SESSION);
		$this->redirect("access", "login");
	}
	
	protected function doLogin($login, $password) {
		$authModel = new Model("login");
		if (MODEL_TYPE == "API"){
			$authData = $authModel->login(array('username' => $login, 'password' => $password));
		} else {
			$authData = $authModel->login(array('login' => $login, 'passwd' => $password));
		}
		if ($authData["token"]) {
			$this->authenticateToken($authData["token"]);
		} else {
			return false;
			$this->sendError("danger", "Error", "Login Failed");
		}
		$this->set("login", "success");
		return true;
	}

	function authenticateAction(){
		if ($this->getParam("login") && $this->getParam("passwd")){
			return $this->doLogin($this->getParam("login"), $this->getParam("passwd"));
		}
	}

	protected function authenticateToken($token){
		$sessionModel = new Model("login");
		$data = $sessionModel->getTokenData(array('token' => $token));
		if (isset($data["userData"])){
			$this->setSession('token', $token);
			$this->setSession("data", $data);
			$this->setSession("company_id", $data['userData']['company_id']);
		} else {
			$this->sendError("danger", "Error", "Login Failed");
		}
		$this->set("login", "success");
	}
	
	function getRemoteAddr(){
		return  (isset($_SERVER["HTTP_X_REAL_IP"])) ? $_SERVER["HTTP_X_REAL_IP"] : $_SERVER["REMOTE_ADDR"];
	}

	function getTrxId(){
		return $this->getRemoteAddr() . '-' .   date('Y.m.d.H.i.s') . '-' . rand();
	}
	
	
	protected function isLoggedIn() {
		return ($this->getSession("token")) ? TRUE : FALSE;
	}
	
	
	
}
