<?php

class Core_Projectexpenses_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
		//$this->set("tab", $this->tab);
		$this->model=new BLModel("sys/pm/project/expense", "id", "project_id");
	}
	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["project_id"]["helper"]="blsfe_helper_pmprojectList";
		$formatters["user_id"]["helper"]="blsfe_helper_userList";
		
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/". $this->tab. "/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/". $this->tab. "/admin");
	}
}
