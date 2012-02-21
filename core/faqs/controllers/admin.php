<?php

class Core_Faqs_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "faqs");
		$this->model=new BLModel("sys/faq", "id");
	}
	function indexAction() {
		$faqs=$this->model->getall(1);
		$this->set("aData", $faqs);
	}

	function addAction() {
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$this->model->add($_REQUEST);
			$this->redirect("core", "faqs/admin/");
		}
	}
	
	function editAction($id) {
		$this->addPackage("ckeditor"); // Load the ckeditor libraries
		$faqData=$this->model->getFaqWithTranslations(array("id"=>$id));
		$faq=array();
		$faq["id"]=$faqData["id"];
		$faq["name"]=$faqData["name"];
		foreach($this->backend_languages as $key=>$l){
			if ($faqData["translations"][$key]){
				$faq["translations"][$key]=$faqData["translations"][$key];
			} else {
				$faq["translations"][$key]=array(
					"faq_id"=>$faqData["id"],
					"lang"=>$key
				);
			}
			
		}
		$this->set("aData", $faq);
	}
	function saveAction(){
		$this->render=0;
		$faqTextModel=new BLModel("sys/faq/text", "id", "faq_id");
		if ($_REQUEST["id"]>0){
			$x=$faqTextModel->set($_REQUEST);
		} else {
			$x=$faqTextModel->add($_REQUEST);
		}
		$this->redirect("core", "faqs/admin/edit/" . $_REQUEST["faq_id"]);
	}
}
