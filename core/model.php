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
		$_SESSION = array();
		$authModel = new Model('login');
		if ($authModel->doLogin($login, $password)){
			$this->set("login", "success");
		} else {
			$this->set("login", "failed");
		}
	}

	function authenticateAction(){
		if ($this->getParam("login") && $this->getParam("passwd")){
			return $this->doLogin($this->getParam("login"), $this->getParam("passwd"));
		}
	}

	protected function authenticateToken($token){
		$sessionModel = new Model("login");
		if ($sessionModel->authenticateToken($token)){
			$this->set("login", "success");
		} else {
			$this->set("login", "failed");
		}
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
