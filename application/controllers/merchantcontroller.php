<?php

class MerchantController extends BLController {

	function beforeAction () {
        	$this->model= new blmodel("/crm/merchant","id", null);
	}

	
	function index($q) {
		print "<pre>" . print_r($q, true) . "</pre>";
        	$x=$this->model->get(1);
		$this->set('string',"Hello World");
		$this->set("merchant", $x);
	}

	function afterAction() {

	}


}
