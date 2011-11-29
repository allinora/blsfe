<?php

include_once(dirname(__FILE__) . "/index.php");
class Core_Comments_AddController extends Core_Comments_Controller {

	function beforeAction(){
		parent::beforeAction();
	}

	function indexAction() {
		global $_SESSION;
		$this->render=0;
		if ($_REQUEST["object_type"] && $_REQUEST["object_id"] && $_SESSION["user"]["id"] && $_REQUEST["comment"]){
			$_params=$_REQUEST;
			$_params["user_id"]=$_SESSION["user"]["id"];
			$x=$this->model->add($_params);
			print $x;
		} else {
			print "What do you want to add?";
		}
	}
}
