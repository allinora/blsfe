<?php

class Core_Controller extends BLController {

	function beforeAction(){
		$_wrapper_directory=BLSFE_ROOT . "/core/admin/views";
		$this->setWrapperDir($_wrapper_directory);
		$this->set("blsfe_template_dir", $_wrapper_directory);
		
		$language_model=new BLModel("sys/language", "id");
		$_languages=$language_model->getall(1);
		$this->backend_languages=array();
		foreach($_languages as $l){
			$this->backend_languages[$l["lang"]]=$l;	
		}
		$this->set("languages", $this->backend_languages);
	}
	
	function afterAction(){
	}
	


}
