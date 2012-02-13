<?php

class Core_Projectoffers_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
		//$this->set("tab", $this->tab);
		$this->model=new BLModel("sys/pm/project/offer", "id", "company_id");
	}
	function indexAction() {
		$res=$this->model->getAllOffers();
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["company_id"]["helper"]="blsfe_helper_companyList";
		$formatters["description"]["css"]="width:600px; height: 400px;";
		$formatters["conditions"]["css"]="width:600px; height: 100px;";
		$formatters["footnotes"]["css"]="width:600px; height: 100px;";
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/". $this->tab. "/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/". $this->tab. "/admin");
	}
}
