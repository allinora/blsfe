<?php

class Core_Comments_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "comments");
		$this->model=new BLModel("sys/user/comment", "id");
	}
	function indexAction() {
		$comments=$this->model->getComments4Admin();
		$this->set("aComments", $comments);
	}
	
}
