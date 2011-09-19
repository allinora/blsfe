<?php
include_once(dirname(__FILE__) . "/dbquery.class.php");


class DBModel extends DBQuery {

	function __construct() {
		// Get the database connection from the config file.
		// This should be only used for stuff that requires a database connection and is not provided by the BLS
		// Such as session, or even when using database for caching bls results... etc
		if (!defined("DB_HOST")|| !defined("DB_USER")||!defined("DB_NAME")||!defined("DB_PASS")){	
			die("Database config is not defined");
		}

		
		$host=DB_HOST;
		$user=DB_USER;
		$pass=DB_PASS;
		$db=DB_NAME;
		parent::__construct($host, $user, $pass, $db);
	}

	function __destruct() {
	}
}
