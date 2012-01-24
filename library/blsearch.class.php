<?php

require_once dirname(__FILE__). '/3rdparty/sphinx/sphinxapi.php';
class BLSearch extends SphinxClient {
	
	public function __construct(){
		parent::__construct();
		if (defined('SPHINX_SERVER_HOST') && defined('SPHINX_SERVER_PORT')){
			$this->SetServer(SPHINX_SERVER_HOST, (int) SPHINX_SERVER_PORT);
		}
		
		// Common options
		$this->SetMatchMode(SPH_MATCH_ALL);
		$this->SetArrayResult(true);
		$this->SetLimits(0, 100);
		
		
	}
	
}

?>
