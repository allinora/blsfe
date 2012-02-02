<?php

class Core_Zlmlistings_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "zlmlistings");
		$this->model=new BLModel("zolomo/listing", "id", "category_id");
	}
	function indexAction() {
		$res=$this->model->getAllListings();
		//print "<pre>"  . print_r($res, true) . "</pre>";
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["user_id"]["helper"]="blsfe_helper_userList";
		$formatters["active"]["formtype"]="yesno_radio";
		$formatters["description"]["css"]="width:300px; height: 200px;";
		$formatters["space_id"]["helper"]="blsfe_helper_spaceList";
		$formatters["action_id"]["helper"]="blsfe_helper_actionList";
		$formatters["sub_category_id"]["helper"]="blsfe_helper_systemCategorySubCategoryList";
		
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/zlmlistings/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/zlmlistings/admin");
	}
}
