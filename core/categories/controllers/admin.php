<?php

class Core_Categories_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "categories");
		$this->model=new BLModel("sys/category", "id");
	}
	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	
	function formatters(){
		$formatters=array();
		$formatters["icon"]["formtype"]="system_image";
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/categories/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/categories/admin");
	}
}
