<?php

class Core_Login_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->model=new BLModel("sys/auth/user", "id");
		
	}
	function indexAction() {
		$this->set("noMenu", 1);
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$x=$this->model->login(array("email"=>$_REQUEST["email"], "passwd"=>$_REQUEST["passwd"]));
			if ($x["user_id"]>0){
				$_SESSION["user"]=$x;
				$this->redirect("core", "admin");
			}
		}
		
	}
}
