<?php

class Core_Imagemanager_UploadController extends BaseController {

	
	function indexAction() {
		blsfe_load_class("blimage");
		$imageManager=new BLImage();
		$imageCategories=$imageManager->getCategories();
		print "<pre>" . print_r($imageCategories, true) . "</pre>";
		$this->set('title',"Lila Office");
	}


}
