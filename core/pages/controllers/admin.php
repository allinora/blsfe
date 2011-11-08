<?php

class Core_Pages_AdminController extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "pages");
	}
	function indexAction() {
		blsfe_load_class("BLPage");
		$oPage=new BLPage();
		$pages=$oPage->getPages();
		$this->set("aData", $pages);
	}
	
	function editAction($id) {
		blsfe_load_class("BLPage");
		$oPage=new BLPage();
		$pageData=$oPage->getPageWithTranslations($id);
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
			// Do the update
			$x=$pageTextModel->set($_REQUEST);
		} else {
			// Do the insert
			$x=$pageTextModel->add($_REQUEST);
		}
		
		//print "<pre>" . print_r($x, true) . "</pre>";
		$this->redirect("core", "pages/admin/edit/" . $_REQUEST["page_id"]);
	}
}
