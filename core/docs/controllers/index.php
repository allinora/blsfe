<?php

class Core_Docs_Controller extends Docs_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "docs");
		$this->set("tab", "docs");
		$this->model=new BLModel("sys/document", "id", "category_id");
		$this->categoriesModel=new BLModel("sys/document/category", "id");
		$this->categories=$this->categoriesModel->getall(1);
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "docs/admin");
	}
}
