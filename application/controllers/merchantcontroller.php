<?php

class MerchantController extends BLController {

	function beforeAction () {
        	$this->model= new blmodel("/crm/merchant","id", null);
	}

	
	function index($q) {
        $x=$this->model->get(1);
		$this->set("merchant", $x);
	}

	function afterAction() {

	}


}
