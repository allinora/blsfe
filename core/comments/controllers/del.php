<?php

include_once(dirname(__FILE__) . "/index.php");
class Core_Comments_DelController extends Core_Comments_Controller {

	function beforeAction(){
		parent::beforeAction();
	}

	function indexAction() {
		global $_SESSION;
		
		$this->render=0;
		if ($_REQUEST["id"] &&  $_SESSION["user"]["user_id"] ){
			$_params=$_REQUEST;
			$_params["user_id"]=$_SESSION["user"]["user_id"];
			//print "<pre>"  . print_r($_params, true) . "</pre>";
			$x=$this->model->deactivateComment($_params);
			print $x;
		} else {
			print "What do you want to add?";
		}
	}
}
