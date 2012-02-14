<?php

include_once(dirname(__FILE__) . "/blfileinfo.class.php");
class BLImage {
	
	public function _construct(){
		global $cache;
		// Set the reference to cache
		$this->cache=$cache;
	}
	
	public function resizeFile($f, $x=0, $y=0){
		$x=round($x);
		$y=round($y);
		$caching=0;
		
		
		
		if (defined("CMS_CACHE_DIRECTORY")){
			if (in_array(substr($_SERVER["REQUEST_URI"], -3), array("png","jpg","gif"))){
				$caching=1;
				$cache_file=CMS_CACHE_DIRECTORY . $_SERVER["REQUEST_URI"];
				$cache_dir=dirname($cache_file);
			}
			
		}
		
		//print "$cache_file"; 

		if (($x || $y) && class_exists("Imagick")) {
			$imagick = new Imagick($f);
			$imagick->setCompressionQuality(90);
			$imagick->thumbnailImage($x,$y);
			$imagick->cropImage($x,$y,0,0);
			$data=$imagick->getimageblob();
		} else {
			$data=file_get_contents($f);
		}
		
		if ($caching){
			if(!is_dir($cache_dir)){
				mkdir(dirname($cache_file) , 0777, true);
			}
			file_put_contents($cache_file, $data);
		}
		return $data;
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
	
	public function displayImage($f, $x=0, $y=0){
		$fileInfo=new BLFileinfo();
		$mimetype=$fileInfo->ext2mimetype($f);
		$data = $this->resizeFile($f, $x, $y);
		header("Content-type: $mimetype");
		echo $data;
	}
	
	public function log($str){
		if (is_string($str)){
			syslog(LOG_WARNING, $str);
		}
	}
}

?>
