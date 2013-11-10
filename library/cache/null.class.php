<?php
class Cache_Null extends Cache {

	function __construct(){
	}
	
	function read($key, $force = 0) {
		return false;
	}
	
	function write($key, $value){
	}
}
