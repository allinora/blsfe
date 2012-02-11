<?php

class BLImage {
	
	public function _construct(){
		
	}
	
	public function resizeFile($f, $x=0, $y=0){
		$x=round($x);
		$y=round($y);
		if (($x || $y) && class_exists("Imagick")) {
			$imagick = new Imagick($f);
			$imagick->thumbnailImage($x,$y);
			$imagick->cropImage($x,$y,0,0);
			return $imagick;
		} else {
			return file_get_contents($f);
		}
	}

	public function resizeBlob($b,  $x=0, $y=0){
		$x=round($x);
		$y=round($y);
		if (($x || $y) && class_exists("Imagick")) {
			$imagick = new Imagick();
			$imagick->readImageBlob($b);
			$imagick->thumbnailImage($x,$y);
			$imagick->cropImage($x,$y,0,0);
			return $imagick;
		} else {
			return $b;
		}
	}
	
	
	public function log($str){
		if (is_string($str)){
			syslog(LOG_WARNING, $str);
		}
	}
}

?>
