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
	    $translation=$this->callBusinessLogicService("/sys/po/string/getTranslation", array("string"=>trim($string[1]), "language"=>$this->lang, "project"=>$this->project), "GET");
	    if (is_array($translation) && isset($translation["msgstr"])) {
			return $translation["msgstr"];
	    }
	    return "X:" . $string[1];
	}
	
	public function getProjects(){
	    $projects=$this->callBusinessLogicService("/sys/po/string/getProjects");
		return $projects;
	}


	private  function getStringWithTranslations($id){
		$_params["id"]=$id;
	    $result=$this->callBusinessLogicService("/sys/po/string/getStringWithTranslations", $_params);
		return $result;
		
	}

	public function handle(){
		$data="";
		
		if ($_REQUEST["project"]){
			$_SESSION["PO_project"]=$_REQUEST["project"];
		}
		if ($_REQUEST["language"]){
			$_SESSION["PO_language"]=$_REQUEST["language"];
		}
		if ($_REQUEST["q"]){
			$_SESSION["PO_q"]=$_REQUEST["q"];
		}
		
		$data.=$this->searchForm($_SESSION["PO_project"],$_SESSION["PO_language"],$_SESSION["PO_q"]);
		if ($_REQUEST["op"]=="search"){
			$data.=$this->searchResult($_REQUEST["q"], $_REQUEST["project"], $_REQUEST["language"]);
		}
		if ($_REQUEST["op"]=="edit"){
			$data.=$this->editForm($_REQUEST["id"]);
		}
		if ($_REQUEST["op"]=="update"){
			$this->updatePO($_REQUEST["id"], $_REQUEST["po"]);
		}
		
		return $data;
	}
	
	private function updatePO($id, $po){
		print "Updating $id";
		$_params["id"]=$id;
		$_params["po"]=$po;
		print "<pre>" . print_r($_params, true) . "</pre>";
		
	    $result=$this->callBusinessLogicService("/sys/po/string/updateTranslations", $_params);
		print "<pre>" . print_r($result, true) . "</pre>";
		
		
	}
	
	private function editForm($id){
		$r=$this->getStringWithTranslations($id);
		//print "<pre>xx" . print_r($r, true) . "</pre>";
		$data.="\n<form>";
		$data.="\n<input type='hidden' name='op' value='update'>";
		$data.="\n<input type='hidden' name='id' value='" . $id. "'>";
		$data.="\n<table border=1>";
		$data.="\n<tr><th>Source</th><td>"  . htmlentities($r["msgid"]) . "</td></tr>";
		foreach($this->getLanguages() as $l){
			$data.="\n<tr><th>$l</th><td><textarea class='po_textarea' name='po[$l]'>"  . $r["translations"][$l]["msgstr"] . "</textarea></td></tr>";
		}
		$data.="\n</table>";
		$data.="\n<input type='submit' value='update' onclick='this.form.submit()'>";
		$data.="\n</form>";
		
		return $data;
		
	}
	
	
	public function searchResult($string, $project="", $lang=""){
		$result=$this->search($string, $project, $lang);
		if (!is_array($result)){
			$data="No result found";
			return $data;
		}

		$data="<div style='max-width: 600px;'>";
		$languages=$this->getLanguages();
		foreach($result as $r){
			$data.="\n<form>";
			$data.="\n<input type='hidden' name='op' value='edit'>";
			$data.="\n<input type='hidden' name='id' value='" . $r["id"]. "'>";
			$data.="\n<table border=1>";
			$data.="\n<tr><th>Project</th><td>"  . $r["project"] . "</td></tr>";
			$data.="\n<tr><th>Source</th><td>"  . htmlentities($r["msgid"]) . "</td></tr>";
			foreach($languages as $l){
				$data.="\n<tr><th>$l</th><td>"  . htmlentities($r["translations"][$l]["msgstr"]) . "</td></tr>";
			}
			$data.="\n</table>";
			$data.="\n<input type='submit' value='edit' onclick='this.form.submit()'>";
			$data.="\n</form>";
			$data.="<hr />";
		}
		$data.="</div>";
		return $data;
		print "<pre>" . print_r($result, true) . "</pre>";
	}
	
	private  function search($string, $project="", $lang=""){
		$_params["q"]=$string;
		$_params["project"]=$project;
		$_params["language"]=$lang;
	    $result=$this->callBusinessLogicService("/sys/po/string/search", $_params);
		return $result;
		
	}
	
	public function getLanguages(){
		$languages = explode("|", LANGUAGES);
		return $languages;
	}
	
	public function searchForm($project=null,$language=null,$string=null){
		$projects=$this->getProjects();
		$data="\n<form>";
		$data.="\n<input type=hidden name='op' value='search'>";
		$data.="\nProject";
		$data.=$this->projectList( $projects, $project);
		$data.="\nLanguage";
		$data.=$this->languageList( $this->getLanguages(), $language);
		$data.="\nText";
		$data.=$this->searchBox($string);
		$data.="\n<input type='submit'>";
		$data.="\n</form>\n\n";
		return $data;
	}
	private function searchBox($string){
		$data="<input type=text name='q' value='$string'>";
		return $data;
	}
	
	private function projectList($options, $selected){
		$data="<select id='project' name='project'>";
		$data.="<option value=''>Any</option>";
		foreach($options as $o){
			$data.="<option value='$o'";
			if ($o==$selected){
				$data.=" selected ";
			}
			$data.=">$o</option>";
		}
		$data.="</select>";
		return $data;
	}
	
	private function languageList($options, $selected){
		$data="<select id='language' name='language'>";
		$data.="<option value=''>Any</option>";
		$data.="<option value='00'>Source</option>";
		foreach($options as $o){
			$data.="<option value='$o'";
			if ($o==$selected){
				$data.=" selected ";
			}
			$data.=">$o</option>";
		}
		$data.="</select>";
		return $data;
	}
	
	
	
}
