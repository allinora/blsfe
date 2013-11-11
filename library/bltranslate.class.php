<?php

include_once(dirname(__FILE__) . "/bltransport.class.php");
class BLTranslate extends BLTransport{
	var $project;
	var $lang;
	var $seen;
	
	public function __construct($lang="00", $project=""){
		global $cache;
		$this->lang=$lang;
		$this->project=$project;
		$this->cache = $cache;
		
	}
	
	public function translate($string){
		global $cache;
		$this->cache = $cache;
		
		$str = trim($string[1]);
		
		if ($this->seen[$str]){
			return $this->seen[$str];
		}
		$cache_key = "translate.$str";
		if ($this->cache){
			if ($result = $this->cache->read($cache_key)){
				return $result;
			}
		}
		
	    $translation=$this->callBusinessLogicService("/sys/po/string/getTranslation", array("string"=>trim($string[1]), "language"=>$this->lang, "project"=>$this->project), "GET");
	    if (is_array($translation) && isset($translation["msgstr"])) {
			$this->seen[$str] = $translation["msgstr"];
			if ($this->cache){
				$this->cache->write($cache_key, $translation["msgstr"]);
				
			}
	    } else {
			$this->seen[$str] = $string[1];
		}
		return $this->seen[$str];
	}
	public function debugTranslate($string){
		$translation = $this->translate($string);
		
	    if ($translation == trim($string[1])) {
			return "X:" . $translation;
		}
		
		return $translation;
		
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
		//print "Updating $id";
		$_params["id"]=$id;
		$_params["po"]=$po;
		//print "<pre>" . print_r($_params, true) . "</pre>";
	    $result=$this->callBusinessLogicService("/sys/po/string/updateTranslations", $_params);
	
		if (isset($_SESSION["translation_last_search"])){
			$_url = "/core/translations/admin/?op=search&project=" . $_SESSION["translation_last_search"]["project"] . "&language=" . $_SESSION["translation_last_search"]["language"] . "&q=" . $_SESSION["translation_last_search"]["string"] . "&scrollto=$id";
			header("Location: $_url");
		}
	}
	
	private function editForm($id){
		$r=$this->getStringWithTranslations($id);
		//print "<pre>xx" . print_r($r, true) . "</pre>";
		
		$_SESSION["translation_last_edit"] = $id;
		// print "<pre>" . print_r($_SESSION, true) . "</pre>";
		
		$data.="\n<br><br><form>";
		$data.="\n<input type='hidden' name='op' value='update'>";
		$data.="\n<input type='hidden' name='id' value='" . $id. "'>";
		$data.="\n<table class='potable table table-striped table-bordered table-condensed'>";
		
		
		$data.="\n<tr><th>Source</th><td>"  . htmlentities($r["msgid"]) . "</td></tr>";
		foreach($this->getLanguages() as $l){
			$data.="\n<tr><th>$l</th><td><textarea class='po_textarea form-control' name='po[$l]'>"  . $r["translations"][$l]["msgstr"] . "</textarea></td></tr>";
		}
		$data.="\n</table>";
		$data.="\n<input type='submit' class='btn pull-right' value='update' onclick='this.form.submit()'>";
		$data.="\n</form>";
		
		return $data;
		
	}
	
	
	public function searchResult($string, $project="", $lang=""){
		$result=$this->search($string, $project, $lang);
		if (!is_array($result)){
			$data="No result found";
			return $data;
		}

		$_SESSION["translation_last_search"]["string"] = $string;
		$_SESSION["translation_last_search"]["project"] = $project;
		$_SESSION["translation_last_search"]["lang"] = $lang;

		
		$data="<div style='max-width: 600px;' >";
		$languages=$this->getLanguages();
		foreach($result as $r){
			$data.="\n<form>";
			$data.="\n<input type='hidden' name='op' value='edit'>";
			$data.="\n<input type='hidden' name='id' value='" . $r["id"]. "'>";
			$data.="\n<table class='potable table table-striped table-bordered table-condensed' id='translation-" . $r["id"] . "'>";
			$data.="\n<tr class='odd' ><th>Project</th><td><i>"  . $r["project"] . "</i></td></tr>";
			$data.="\n<tr class='even' ><th>Source</th><td><b>"  . htmlentities($r["msgid"]) . "</b></td></tr>";
			$class="odd";
			foreach($languages as $l){
				$trclass=($r["translations"][$l]["msgstr"])?'filled':'empty';
				$data.="\n<tr class='$class'><th>Translation :: $l</th><td class='$trclass'>"  . htmlentities($r["translations"][$l]["msgstr"]) . "</td></tr>";
				if ($class=="odd"){
					$class="even";
				} else {
					$class="odd";
				}
			}
			$data.="\n<tr><td colspan=2 align=right><div align=right><input type='submit' class='potable-edit'  value='edit this string' onclick='this.form.submit()'></div></td></tr>";
			$data.="\n</table>";
			$data.="\n</form>";
			$data.="<hr class='potable-spacer'/>";
		}
		$data.="</div>";
		// print "<pre>" . print_r($result, true) . "</pre>";
		return $data;
	}
	
	private  function search($string, $project="", $lang=""){
		$_params["q"]=$string;
		$_params["project"]=$project;
		$_params["language"]=$lang;
	    $result=$this->callBusinessLogicService("/sys/po/string/search", $_params);
		return $result;
		
	}
	
	public function getLanguages(){
		if (defined('TRANSLATION_LANGUAGES')){
			$languages = explode("|", TRANSLATION_LANGUAGES);
		} else {
			$languages = explode("|", LANGUAGES);
		}
		return $languages;
	}
	
	public function searchForm($project=null,$language=null,$string=null){
		$projects=$this->getProjects();
		$data = "\n<form>";
		$data .= "\n<table style='width: auto' class='table table-striped table-bordered table-condensed'>";
		$data .= "\n<input type=hidden name='op' value='search'>";
		$data .= "\n<th>Project</th>";
		$data .= "<td>" . $this->projectList( $projects, $project) . "</td>";
		$data .= "\n<th>Language</th>";
		$data .= "<td>" . $this->languageList( $this->getLanguages(), $language) . "</td>";
		$data .= "\n<th>Text</th>";
		$data .= "<td>" . $this->searchBox($string) . "</td>";
		$data .= "\n<th><input type='submit' value='search'></th>";
		$data .= "\n</table></form>\n\n";
		return $data;
	}
	private function searchBox($string){
		$data="<input type=text name='q' value='$string'>";
		return $data;
	}
	
	private function projectList($options, $selected){
		$data="<select id='project' name='project'>";
		$data.="<option value=''>Any</option>";
		foreach((array)$options as $o){
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
