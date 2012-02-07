<?php

class Core_%name%_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", $this->tab . "/admin");
	}
	
}
