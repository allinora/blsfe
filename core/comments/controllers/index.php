<?php

class Core_Comments_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "comments");
		$this->model=new BLModel("sys/user/comment");
	}
	function indexAction() {
		$this->render=0;
		print "What are you looking for?";
	}
}
