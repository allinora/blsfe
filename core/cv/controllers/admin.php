<?php

class Core_Cv_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "cv");
		$this->model=new BLModel("sys/user/cv/project", "id", "user_id");
	}
	
	
	function indexAction() {
		$res=$this->model->getall(1);
		//print "<pre>" . print_r($res, true) . "</pre>";
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["user_id"]["helper"]="blsfe_helper_userList";
		$formatters["category_id"]["helper"]="categoryList";
		$formatters["description"]["css"]="width: 400px; height: 100px; background-color: #d4d4d4";
		//$formatters["image"]["formtype"]="system_image";
		$formatters["image"]["hidden"]="true";

		return $formatters;
	}

	function addAction() {
		parent::addAction("core/cv/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/cv/admin");
	}
}
