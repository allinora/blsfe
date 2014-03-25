<?php
include_once(dirname(__FILE__) . "/apiquery.class.php");


class ApiModel extends ApiQuery {
	protected $_model;
	protected $_idField;
	protected $_searchField;
	protected $_cached=true;

	public $debug = FALSE;


	function __construct($model, $_idF="id", $_searchF=null) {
		parent::__construct();
		$this->_model = $model;
		$this->_idField = $_idF;
		$this->_searchField = $_searchF;
	}

	function cache($x=null){
		if (isset($x)){
			$this->_cached=$x;
		} else {
			return $this->_cached;
		}
		
	}
	function model(){
		return $this->_model;
	}

	function idField(){
		return $this->_idField;
	}
	function searchField(){
		return $this->_searchField;
	}
	
	function __destruct() {
	}
}
