<?php

class Core_Controller extends BLController {

	function beforeAction(){
		global $default;
		
		print "<pre>" . print_r($default, true) . "</pre>";
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
		
		$this->set("coremodules", $default["modules"]["sys"]);
		$this->set("appmodules", $default["modules"]["app"]);
	}
	
	function afterAction(){
	}
	


}
