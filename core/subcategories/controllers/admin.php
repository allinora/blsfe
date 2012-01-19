<?php

class Core_Subcategories_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "subcategories");
		$this->model=new BLModel("sys/category/sub", "id", "category_id");
	}
	function indexAction() {
		$res=$this->model->getAllSubcategories();
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["category_id"]["helper"]="categoryList";
		//$formatters["icon"]["formtype"]="system_image";
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
