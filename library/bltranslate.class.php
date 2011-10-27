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
	    if (is_array($translation) && isset($translation["msgstr"])) {
			return $translation["msgstr"];
	    }
	    return "X:" . $string[1];
	}
	
	public function getProjects(){
	    $projects=$this->callBusinessLogicService("/core/po/string/getProjects");
		return $projects;
	}
	
	public function search($string, $project="", $lang=""){
		$_params["q"]=$string;
		$_params["project"]=$project;
		$_params["language"]=$lang;
	    $result=$this->callBusinessLogicService("/core/po/string/search", $_params);
		return $result;
		
	}
	
	public function getLanguages(){
		$languages = explode("|", LANGUAGES);
		return $languages;
	}
	
	public function searchForm($project=null,$language=null,$string=null){
		$projects=$this->getProjects();
		$data=$this->projectList( $projects, $project);
		print $data;
		$data=$this->languageList( $this->getLanguages(), $language);
		print $data;
	}
	
	private function projectList($options, $selected){
		$data="<select id='project' name='project'>";
		$data.="<option value=''>Any</option>";
		foreach($options as $o){
			$data.="<option value='$o'>$o</option>";
		}
		$data.="</select>";
		return $data;
	}
	
	private function languageList($options, $selected){
		$data="<select id='language' name='language'>";
		$data.="<option value=''>Any</option>";
		$data.="<option value='00'>Source</option>";
		foreach($options as $o){
			$data.="<option value='$o'>$o</option>";
		}
		$data.="</select>";
		return $data;
	}
	
	
	
}
