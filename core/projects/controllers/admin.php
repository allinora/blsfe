<?php

class Core_Projects_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
		//$this->set("tab", $this->tab);
		$this->model=new BLModel("sys/pm/project", "id", "company_id");
	}
	function indexAction() {
		$res=$this->model->getAllProjects();
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["company_id"]["helper"]="blsfe_helper_companyList";
		$formatters["description"]["css"]="width:600px; height: 400px;";
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/". $this->tab. "/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/". $this->tab. "/admin");
	}
}
