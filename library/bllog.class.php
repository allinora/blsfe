<?php

class BLLog {
	
	public function _construct(){
		
	}
	
	public function log($str){
		syslog(LOG_WARNING, $str);

	}
}

?>
