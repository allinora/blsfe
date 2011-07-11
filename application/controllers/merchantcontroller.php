<?php

class MerchantController extends BLController {

	function beforeAction () {
        	$this->model= new blmodel("/crm/merchant","id", null);
	}

	
	function index($q) {
        $x=$this->model->get($q);
		$this->set("merchant", $x);
	}

	function afterAction() {

	}


}
