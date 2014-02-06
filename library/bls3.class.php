<?php
include_once(__DIR__ . "/../3rdparty/S3.php");
class BLS3 extends S3 {
	
	public function __construct($accessKey = null, $secretKey = null, $useSSL = false, $endpoint = 's3.amazonaws.com'){
		if (defined('STORAGE_SERVER_HOST') && defined('STORAGE_SERVER_ACCESS_KEY') && defined('STORAGE_SERVER_SECRET_KEY')){
			
			$endpoint = STORAGE_SERVER_HOST;
			$accessKey = STORAGE_SERVER_ACCESS_KEY;
			$secretKey = STORAGE_SERVER_SECRET_KEY;
			return parent::__construct($accessKey, $secretKey, $useSSL, $endpoint);
		} else {
			throw new Exception("Some constants are not correctly defined. Cannot use Storage");
		}

	}

}

