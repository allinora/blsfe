<?php

class Core_Languages_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "languages");
		$this->model=new BLModel("sys/language", "lang");
	}
	function indexAction() {
		$languages=$this->model->getall(1);
		$this->set("aData", $languages);
	}
}
