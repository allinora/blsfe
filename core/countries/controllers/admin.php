<?php

class Core_Countries_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "countries");
		$this->model=new BLModel("sys/country", "code");
	}
	function indexAction() {
		$aData = $this->model->getall(1);
		$this->set("aData", $aData);
	}
}
