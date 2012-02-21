<?php

class Core_Projectofferentries_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
		//$this->set("tab", $this->tab);
		$this->model=new BLModel("sys/pm/project/offer/entry", "id", "offer_id");
	}
	function indexAction($id) {
		$res=$this->model->getall($id);
		$this->set("aData", $res);
		$this->set("offer_id", $id);
	}
	
	

	function formatters(){
		$formatters=array();
		$formatters["offer_id"]["value"]=$_REQUEST["offer_id"];
		$formatters["offer_id"]["formtype"]="hidden_with_value";
		$formatters["description"]["css"]="width:500px; height: 70px;";
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/". $this->tab. "/admin/index/" . $_REQUEST["offer_id"]);
	}
	
	function editAction($id) {
		parent::editAction($id, "core/". $this->tab. "/admin/index/" . $_REQUEST["offer_id"]);
	}
}
