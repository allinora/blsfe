<?php

// Adapted from upload.php that comes with the pluploaer

/**
 * upload.php
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

class BLUpload {
	var $target_directory = "/tmp";
	var $target_filename;
	
	public function _construct(){
		
	}
	
	function setTargetDirectory($_dir){
		$this->target_directory=$_dir;
	}
	function setFileName($n){
		$this->target_filename=$this->cleanFileName($n);
	}
	function getFileName(){
		return $this->target_filename;
	}
	
	function cleanFileName($fileName){
		$fileName = preg_replace('/[^\w\._]+/', '', $fileName);
		$fileName = preg_replace('/[^a-z0-9\._-]+/i', '', $fileName);
		return $fileName;
	}
	
	function startUpload(){
		$targetDir = $this->target_directory;
		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
		$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

		// Clean the fileName for security reasons
		$fileName = $this->cleanFileName($fileName);

		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
				$count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}

		// Create target dir
		if (!file_exists($targetDir))
			@mkdir($targetDir, 0777, true);


		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];

		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				if (!$this->target_filename) {
					// No specific name is defined
					$this->setFileName($fileName);
				};
				$outfile=$targetDir . DIRECTORY_SEPARATOR . $this->getFileName();
				$out = fopen($outfile, $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else
						return $this->returnResponse('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream at ' . __LINE__ .'"}, "id" : "id"}');
					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else
					return $this->returnResponse('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream  at ' . __LINE__ . " for " . $outfile . '"}, "id" : "id"}');
			} else
				return $this->returnResponse('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file  at ' . __LINE__ .'"}, "id" : "id"}');
		} else {
			// Open temp file
			$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					return $this->returnResponse('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream at ' . __LINE__ .'"}, "id" : "id"}');

				fclose($in);
				fclose($out);
			} else
				return $this->returnResponse('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream at ' . __LINE__ .'"}, "id" : "id"}');
		}

		// Return JSON-RPC response
		return $this->reply("success", 200, "File uploaded successfully", $outfile);
	}
	
	function returnResponse($str){
		json_decode($str);
		return $str;
	}
	
	function reply($type, $code, $message, $filepath=null){
		$res=array();
		$res["type"]=$type;
		$res["code"]=$code;
		$res["message"]=$message;
		$res["filename"]=$this->target_filename;
		$res["filepath"]=$filepath;
		$res["mime_type"]=$_FILES['file']["type"];
		return $res;
	}
	
	
}

?>
