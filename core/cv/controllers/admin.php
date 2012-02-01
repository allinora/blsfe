<?php

class Core_Cv_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "cv");
		$this->model=new BLModel("sys/user/cv/project", "id", "user_id");
	}
	
	
	function indexAction() {
		$res=$this->model->getall(1);
		print "<pre>" . print_r($res, true) . "</pre>";
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["category_id"]["helper"]="categoryList";
		$formatters["image"]["formtype"]="system_image";

		$formatters["icon"]["hidden"]="true";
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/subcategories/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/subcategories/admin");
	}
}
