<?php

class BLLog {
	
	public function _construct(){
		
	}
	
	public function log($str){
		if (is_string($str)){
			syslog(LOG_WARNING, $str);
		}
	}
}
