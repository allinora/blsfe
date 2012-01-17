<?php

class Core_Login_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->model=new BLModel("sys/auth/user", "id");
		
	}
	function indexAction() {
		$this->set("noMenu", 1);
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$x=$this->model->login(array("email"=>$_REQUEST["login"], "passwd"=>$_REQUEST["passwd"]));
			//print "<pre>r" . print_r($_REQUEST, true) . "</pre>";
			//print "<pre>x" . print_r($x, true) . "</pre>";
			if ($x["user_id"]>0){
				$_SESSION["authdata"]=$x;
				$this->redirect("core", "admin");
			}
		}
		
	}
}
