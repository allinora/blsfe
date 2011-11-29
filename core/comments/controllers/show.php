<?php

include_once(dirname(__FILE__) . "/index.php");
class Core_Comments_ShowController extends Core_Comments_Controller {

	function beforeAction(){
		parent::beforeAction();
	}

	function indexAction() {
		$this->render=0;
		if ($_REQUEST["object_type"] && $_REQUEST["object_id"]){
			$x=$this->model->getCommentsArray($_REQUEST);
			print $x;
		} else {
			print "What do you want to see?";
			
		}
	}
	function jsonAction() {
		$this->render=0;
		if ($_REQUEST["object_type"] && $_REQUEST["object_id"]){
			$x=$this->model->getCommentsArray($_REQUEST);
			$y=json_encode($x, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
			print $y;
		} else {
			print "What do you want to see?";
			
		}
	}
	function htmlAction() {
		$this->render=1;
		$this->doNotRenderHeader=1;
		if ($_REQUEST["object_type"] && $_REQUEST["object_id"]){
			$x=$this->model->getCommentsArray($_REQUEST);
			$this->set("comments", $x);
		}
	}
}
