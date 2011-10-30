<?php

include_once(dirname(__FILE__) . "/bltransport.class.php");
class BLPage extends BLTransport{
	
	public function __construct(){
	}
	
	public function getPages(){
	    $pages=$this->callBusinessLogicService("/core/page/getall");
		return $pages;
	}


	private  function getPageWithTranslations($id){
		$_params["id"]=$id;
	    $result=$this->callBusinessLogicService("/core/page/getPageWithTranslations", $_params);
		return $result;
		
	}

	private function listPages(){
		$data="";
		$data.="<table border=1>";
		$data.="<thead><tr><th>ID</th><th>Name</th><th>
		<a href='/pages?op=add'>Add</a></th></tr></thead>";
		$data.="<tbody>";
		$pages=$this->getPages();
		foreach($pages as $p){
			$data.="<tr>";
			$data.="<td>" . $p["id"] . "</td>";
			$data.="<td>" . $p["name"] . "</td>";
			$data.="<td><a href='/pages?op=edit&id=" . $p["id"]. "'>Edit</a></td>";
			$data.="</tr>";
			
		}
		$data.="</tbody>";
		$data.="</table>";
		return $data;
		
		
	}

	private function saveText(){
		//print "<pre>" . print_r($_REQUEST, true) . "</pre>";
		if ($_REQUEST["id"]>0){
			// Do the update
			$x=$this->callBusinessLogicService("/core/page/text/set", $_REQUEST);
		} else {
			// Do the insert
			$x=$this->callBusinessLogicService("/core/page/text/add", $_REQUEST);
		}
		//print "<pre>" . print_r($x, true) . "</pre>";
		
	}
	private function editPage($id){
		$page=$this->getPageWithTranslations($id);
		//print "<pre>" . print_r($page, true) . "</pre>";
		$data="<h1>Editing Page: " . $page["name"] . "</h1>";
		foreach($this->getLanguages() as $l){
			$data.="<form method='POST'>";
			$data.="<input type='hidden' name='op' value='saveText'>";
			$data.="<input type='hidden' name='page_id' value='$id'>";
			$data.="<input type='hidden' name='id' value='" .  $page["translations"][$l]["id"]  . "'>";
			$data.="<input type='hidden' name='lang' value='$l'>";
			$data.="$l<table border=1>";
			$data.="<tr>";
			$data.="<th>Title</th>";
			$data.="<td><input type='text' class='title' name='title' value='".$page["translations"][$l]["title"] . "'></td>";
			$data.="</tr>";
			$data.="<tr>";
			$data.="<th>summary</th>";
			$data.="<td><textarea class='description'  name='description'>".$page["translations"][$l]["description"] . "</textarea></td>";
			$data.="</tr>";
			$data.="<tr>";
			$data.="<th>Content</th>";
			$data.="<td><textarea name='data' class='data' >".$page["translations"][$l]["data"] . "</textarea></td>";
			$data.="</tr>";
			$data.="</table>";
			$data.="<input type=submit value='Save version: $l'>";
			$data.="</form>";
		}
		return $data;
	}
	private function addForm(){
		$data="<form>
		<input type=hidden name='op' value='addPage'>
		Page name<input type='text' name='name' value=''><input type=submit></form>";
		return $data;
		
	}
	private function addPage(){
		//print "<pre>" . print_r($_REQUEST ,true) . "</pre>";
		$x=$this->callBusinessLogicService("/core/page/add", $_REQUEST);
		return $x;
		
	}
	public function handle(){
		if ($_REQUEST["op"]=="add"){
			return $this->addForm();
		}
		if ($_REQUEST["op"]=="addPage"){
			$id=$this->addPage();
			if ($id>0){
				return $this->editPage($id);
			} else {
				return  "Something bad happened. Could not get ID of the new page";
			}
		}
		if ($_REQUEST["op"]=="edit"){
			return $this->editPage($_REQUEST["id"]);
		}
		if ($_REQUEST["op"]=="saveText"){
			$this->saveText();
			return $this->editPage($_REQUEST["page_id"]);
			
			
		}
		return $this->listPages();
	}
	
	public function getLanguages(){
		$languages = explode("|", LANGUAGES);
		return $languages;
	}
	
}
