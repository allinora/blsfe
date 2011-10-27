<?php

include_once(dirname(__FILE__) . "/bltransport.class.php");
class BLTranslate extends BLTransport{
	var $project;
	var $lang;
	
	public function __construct($lang="00", $project=""){
		$this->lang=$lang;
		$this->project=$project;
	}
	
	public function translate($string){
	    $translation=$this->callBusinessLogicService("/core/po/string/getTranslation", array("string"=>trim($string[1]), "language"=>$this->lang, "project"=>$this->project), "GET");
		//print "<pre>" . print_r($translation, true) . "</pre>";exit;
	    if ($translation) {
			return $translation["msgstr"];
	    }
	    return "X:" . $string[1];
	}
}
