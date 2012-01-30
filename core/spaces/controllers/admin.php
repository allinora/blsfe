<?php

class Core_Spaces_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "spaces");
		$this->model=new BLModel("sys/space", "id");
	}
	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	

	function categoriesAction($id){
		$s2cModel=new BLModel("sys/space/category/link", "id", "space_id");
		
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$_REQUEST["id"]=$id;
			$x=$s2cModel->updateRelations($_REQUEST);
		}
		
		$space=$this->model->get($id);
		$this->set("space", $space);
		
		$cModel=new BLModel("sys/category", "id");
		$categories=$cModel->getall(1);
		$this->set("aCategories", $categories);

		$space2categoryLinks=$s2cModel->getall($id);
		foreach($space2categoryLinks as $s2c){
			$aSpaceCategories[$s2c["category_id"]]=1;
		}
		$this->set("aSpace2CategoryLinks", $aSpaceCategories);
	}

	function addAction() {
		parent::addAction("core/spaces/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/spaces/admin");
	}
}
