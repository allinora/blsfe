<?php

class Core_Projectfolders_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
		//$this->set("tab", $this->tab);
		$this->model=new BLModel("sys/pm/project/folder", "id", "project_id");
	}
	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/". $this->tab. "/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/". $this->tab. "/admin");
	}




	function indexAction() {
		$res=$this->model->getAllEntries();
		$this->set("aData", $res);
	}

	function listAction($id) {
		$res=$this->model->getall($id);
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["service_id"]["helper"]="blsfe_helper_pmserviceList";
		$formatters["project_id"]["helper"]="blsfe_helper_pmprojectList";
		$formatters["user_id"]["helper"]="blsfe_helper_userList";
		$formatters["status"]["helper"]="blsfe_helper_pmstatusList";
		$formatters["start_time"]["class"]="datetimepicker";
		$formatters["end_time"]["class"]="datetimepicker";
		$formatters["description"]["css"]="width: 600px; height: 250px;";
		
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/". $this->tab. "/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/". $this->tab. "/admin");
	}
}
