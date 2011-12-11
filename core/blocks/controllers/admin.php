<?php

class Core_Blocks_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "blocks");
		$this->model=new BLModel("sys/block", "id");
	}
	function indexAction() {
		$blocks=$this->model->getall(1);
		$this->set("aData", $blocks);
	}

	function addAction() {
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$this->model->add($_REQUEST);
			$this->redirect("core", "blocks/admin/");
		}
	}
	
	function editAction($id) {
		$blockData=$this->model->getBlockWithTranslations(array("id"=>$id));
		$block=array();
		$block["id"]=$blockData["id"];
		$block["name"]=$blockData["name"];
		foreach($this->backend_languages as $key=>$l){
			if ($blockData["translations"][$key]){
				$block["translations"][$key]=$blockData["translations"][$key];
			} else {
				$block["translations"][$key]=array(
					"block_id"=>$blockData["id"],
					"lang"=>$key
				);
			}
			
		}
		$this->set("aData", $block);
		print "<pre>" . print_r($block, true) . "</pre>";
	}
	function saveAction(){
		$this->render=0;
		$blockTextModel=new BLModel("sys/block/text", "id", "block_id");
		if ($_REQUEST["id"]>0){
			$x=$blockTextModel->set($_REQUEST);
		} else {
			$x=$blockTextModel->add($_REQUEST);
		}
		$this->redirect("core", "blocks/admin/edit/" . $_REQUEST["block_id"]);
	}
}
