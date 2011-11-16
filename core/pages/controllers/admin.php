<?php

class Core_Pages_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "pages");
		$this->model=new BLModel("sys/page", "id");
	}
	function indexAction() {
		$pages=$this->model->getall(1);
		$this->set("aData", $pages);
	}

	function addAction() {
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$this->model->add($_REQUEST);
			$this->redirect("core", "pages/admin/");
		}
	}
	
	function editAction($id) {
		$pageData=$this->model->getPageWithTranslations(array("id"=>$id));
		$page=array();
		$page["id"]=$pageData["id"];
		$page["name"]=$pageData["name"];
		foreach($this->backend_languages as $key=>$l){
			if ($pageData["translations"][$key]){
				$page["translations"][$key]=$pageData["translations"][$key];
			} else {
				$page["translations"][$key]=array(
					"page_id"=>$pageData["id"],
					"lang"=>$key
				);
			}
			
		}
		$this->set("aData", $page);
		//print "<pre>" . print_r($page, true) . "</pre>";
	}
	function saveAction(){
		$this->render=0;
		$pageTextModel=new BLModel("sys/page/text", "id", "page_id");
		if ($_REQUEST["id"]>0){
			$x=$pageTextModel->set($_REQUEST);
		} else {
			$x=$pageTextModel->add($_REQUEST);
		}
		$this->redirect("core", "pages/admin/edit/" . $_REQUEST["page_id"]);
	}
}
