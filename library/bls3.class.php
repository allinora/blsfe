<?php
include_once(__DIR__ . "/3rdparty/S3.php");
class BLS3 extends S3 {
	
	public function __construct($accessKey = null, $secretKey = null, $useSSL = false, $endpoint = 's3.amazonaws.com'){
		if (isset($_ENV['STORAGE_SERVER_HOST']) && isset($_ENV['STORAGE_SERVER_ACCESS_KEY']) && isset($_ENV['STORAGE_SERVER_ACCESS_KEY'])){
			$endpoint = $_ENV['STORAGE_SERVER_HOST'];
			$accessKey = $_ENV['STORAGE_SERVER_ACCESS_KEY'];
			$secretKey = $_ENV['STORAGE_SERVER_SECRET_KEY'];
			return parent::__construct($accessKey, $secretKey, $useSSL, $endpoint);
		} else {
			throw new Exception("Some ENV variables are not correctly defined. Cannot use Storage");
		}

	}
}

