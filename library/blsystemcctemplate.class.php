<?php

include_once(dirname(__FILE__) . "/bltransport.class.php");
class BLSystemCCTemplate extends BLTransport{
	
	public function __construct(){
	}
	
	public function getTemplates(){
	    $templates=$this->callBusinessLogicService("/core/mailer/template/system/getall");
		return $templates;
	}


	private  function getTemplateWithTranslations($id){
		$_params["id"]=$id;
	    $result=$this->callBusinessLogicService("/core/mailer/template/system/getTemplateWithTranslations", $_params);
		return $result;
		
	}

	private function listTemplates(){
		$data="";
		$data.="<table border=1>";
		$data.="<thead><tr><th>ID</th><th>Name</th><th>
		<a href='/systemmail?op=add'>Add</a></th></tr></thead>";
		$data.="<tbody>";
		$templates=$this->getTemplates();
		foreach($templates as $t){
			$data.="<tr>";
			$data.="<td>" . $t["id"] . "</td>";
			$data.="<td>" . $t["name"] . "</td>";
			$data.="<td><a href='/systemmail?op=edit&id=" . $t["id"]. "'>Edit</a></td>";
			$data.="</tr>";
		}
		$data.="</tbody>";
		$data.="</table>";
		return $data;
		
		
	}

	private function saveText(){
		if ($_REQUEST["id"]>0){
			// Do the update
			$x=$this->callBusinessLogicService("/core/mailer/template/system/data/set", $_REQUEST);
		} else {
			// Do the insert
			$x=$this->callBusinessLogicService("/core/mailer/template/system/data/add", $_REQUEST);
		}
		//print "<pre>" . print_r($x, true) . "</pre>";
	}

	private function editTemplate($id){
		$template=$this->getTemplateWithTranslations($id);
		//print "<pre>" . print_r($template, true) . "</pre>";
		$data="<h1>Editing Template: " . $template["name"] . "</h1>";
		foreach($this->getLanguages() as $l){
			$data.="<form method='POST'>";
			$data.="<input type='hidden' name='op' value='saveText'>";
			$data.="<input type='hidden' name='template_id' value='$id'>";
			$data.="<input type='hidden' name='id' value='" .  $template["translations"][$l]["id"]  . "'>";
			$data.="<input type='hidden' name='lang' value='$l'>";
			$data.="$l<table border=1>";
			$data.="<tr>";
			$data.="<th>Sender Email</th>";
			$data.="<td><input type='text' class='sender' name='sender' value='".$template["translations"][$l]["sender"] . "'></td>";
			$data.="</tr>";
			$data.="<tr>";
			$data.="<th>Sender Name</th>";
			$data.="<td><input type='text' class='sender_name' name='sender_name' value='".$template["translations"][$l]["sender_name"] . "'></td>";
			$data.="</tr>";
			$data.="<th>Subject</th>";
			$data.="<td><input type='text' class='subject' name='subject' value='".$template["translations"][$l]["subject"] . "'></td>";
			$data.="</tr>";
			$data.="<th>Content</th>";
			$data.="<td><textarea name='body' class='body' >".$template["translations"][$l]["body"] . "</textarea></td>";
			$data.="</tr>";
			$data.="</table>";
			$data.="<input type=submit value='Save version: $l'>";
			$data.="</form>";
		}
		return $data;
	}
	private function addForm(){
		$data="<form>
		<input type=hidden name='op' value='addTemplate'>
		Template name<input type='text' name='name' value=''><input type=submit></form>";
		return $data;
		
	}
	private function addTemplate(){
		//print "<pre>" . print_r($_REQUEST ,true) . "</pre>";
		$x=$this->callBusinessLogicService("/core/mailer/template/system/add", $_REQUEST);
		return $x;
		
	}
	public function handle(){
		if ($_REQUEST["op"]=="add"){
			return $this->addForm();
		}
		if ($_REQUEST["op"]=="addTemplate"){
			$id=$this->addTemplate();
			if ($id>0){
				return $this->editTemplate($id);
			} else {
				return  "Something bad happened. Could not get ID of the new template";
			}
		}
		if ($_REQUEST["op"]=="edit"){
			return $this->editTemplate($_REQUEST["id"]);
		}
		if ($_REQUEST["op"]=="saveText"){
			$this->saveText();
			return $this->editTemplate($_REQUEST["template_id"]);
			
			
		}
		return $this->listTemplates();
	}
	
	public function getLanguages(){
		$languages = explode("|", LANGUAGES);
		return $languages;
	}
	
}
