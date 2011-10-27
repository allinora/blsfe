<?php

include_once(dirname(__FILE__) . "/bltransport.class.php");
class BLTranslate extends BLTransport{
	var $project;
	var $lang;
	
	public function _construct($lang="00", $project=""){
		$this->lang=$lang;
		$this->poject=$project;
	}
	
	public function translate($string){
	    $translation=$this->callBusinessLogicService("/core/po/string/getTranslation", array("string"=>trim($string[1]), "language"=>$lang, "project"=>$project), "GET");
		//print "<pre>" . print_r($translation, true) . "</pre>";exit;
	    if ($translation) {
			return $translation["msgstr"];
	    }
	    return "X:" . $string[1];
	}
}
