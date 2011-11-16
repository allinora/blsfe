<?php

class Core_Translations_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "translations");
	}
	function indexAction() {
        blsfe_load_class("BLTranslate");
        $po=new BLTranslate();
        $data=$po->handle();
        $this->set("po", $data);
		
	}
	
}
