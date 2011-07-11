<?php
class Cache_File extends Cache {

	function __construct(){
		print "Loading Cache_File class<br>";
	}
	function read($key) {
		return "Reading $key<br>";
	}
	
	function set($fileName,$variable) {
	}

}
