<?php

class Core_Zlmactions_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "zlmactions");
		$this->model=new BLModel("zolomo/action", "id", "category_id");
	}
	function indexAction() {
		$res=$this->model->getAllActions();
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["category_id"]["helper"]="blsfe_helper_systemCategoryList";
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/zlmactions/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/zlmactions/admin");
	}
}
