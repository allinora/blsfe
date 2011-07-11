<?php
class BLModel extends BLQuery {
	protected $_model;
    protected $_idField;
    protected $_searchField;

	function __construct($endpoint, $_idF="id", $_searchF) {
		parent::__construct();
		$this->_model = $endpoint;
        $this->_idField=$_idF;
        $this->_searchField=$_searchF;
	}


	function __destruct() {
	}
}
