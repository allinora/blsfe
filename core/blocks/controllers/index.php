<?php

class Core_Blocks_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "blocks");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "blocks/admin");
	}
}
