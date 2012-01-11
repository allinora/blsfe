<?php

include_once(dirname(_FILE_) . "/index.php");
class Core_Docs_AdminController extends Core_Docs_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("categories", $this->categories);
	}

	function indexAction() {
		$documents=$this->model->getall(1);
		//print "<pre>" . print_r($documents, true) . "</pre>";
		$this->set("aData", $documents);
	}

	function addAction() {
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$this->model->add($_REQUEST);
			$this->redirect("core", "docs/admin/");
		}
	}
	
	function editAction($id) {
		$document=$this->model->get($id);
		$this->set("aData", $document);
		//print "<pre>" . print_r($document, true) . "</pre>";
	}

	function showAction($id) {
        $this->doNotRenderHeader=1;
		$document=$this->model->get($id);
		$this->set("aData", $document);
	}
	
	
	
	function autosaveAction(){
		$this->render=0;
		if ($this->model->set($_REQUEST)==2){
			print "{status: ok, message: 'yippie'}";
		} else {
			print "{status: error, message: 'Something messed up'}";
		}
		$this->redirect("core/docs/admin/show/1");
	}
	function saveAction(){
		$this->render=0;
		print "<pre>" . print_r($_REQUEST, true) . "</pre>";
		return;
		$pageTextModel=new BLModel("sys/page/text", "id", "page_id");
		if ($_REQUEST["id"]>0){
			$x=$pageTextModel->set($_REQUEST);
		} else {
			$x=$pageTextModel->add($_REQUEST);
		}
		$this->redirect("core", "pages/admin/edit/" . $_REQUEST["page_id"]);
	}
}
