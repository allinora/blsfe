<?php

class BLImage {
	
	public function _construct(){
		
	}
	
	public function resizeFile($f, $x=0, $y=0){
		
	}

	public function resizeBlob($b,  $x=0, $y=0){
		if (($x || $y) && class_exists("Imagick")) {
			$imagick = new Imagick();
			$imagick->readImageBlob($b);
			$imagick->thumbnailImage($x,$y);
			$imagick->cropImage($x,$y,0,0);
			return $imagick;
		}
	}
	
	
	public function log($str){
		if (is_string($str)){
			syslog(LOG_WARNING, $str);
		}
	}
}

?>
