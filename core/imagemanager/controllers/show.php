<?php

class Core_Imagemanager_ShowController extends Core_Controller {

	
	function listAction() {
		blsfe_load_class("blimage");
		$imageManager=new BLImage();
		$imageCategories=$imageManager->getCategories();
	}


}
