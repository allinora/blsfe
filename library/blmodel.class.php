<?php
include_once(dirname(__FILE__) . "/blquery.class.php");


class BLModel extends BLQuery {
	protected $_model;
	protected $_idField;
	protected $_searchField;

	function __construct($model, $_idF="id", $_searchF=null) {
		parent::__construct();
		$this->_model = $model;
		$this->_idField=$_idF;
		$this->_searchField=$_searchF;
	}

	function model(){
		return $this->_model;
	}

	function idField(){
		return $this->_idField;
	}
	
	function __destruct() {
	}
}
