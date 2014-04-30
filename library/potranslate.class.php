<?php

class POTranslate {
	var $project;
	var $lang;
	var $seen;
	
	public function __construct(){
		$directory = ROOT . '/locale';
		$domain = 'messages';
		$path_tokens = explode('/', ROOT);
		$project = array_pop($path_tokens);
		bindtextdomain($domain, $directory);
		textdomain($domain);
		bind_textdomain_codeset($domain, 'UTF-8');
		
		$locale = $this->getLanguageLocale(LANG);
		putenv("LANG=" . $locale);
		setlocale(LC_MESSAGES, $locale);
	}
	
	
	function getLanguageLocale($lang){
		$languages = array();

		// Define various languages short and charset where they are different

		$languages["en"] = "en_US";
		$languages["cs"] = "cs_CZ";
		$languages["sr"] = "sr_RS";
		
		if (isset($languages[$lang])){
			$code = $languages[$lang];
		} else {
			$code = strtolower($lang) . '_' . strtoupper($lang);
		}
		$code .= '.utf-8';
		return $code;
		
	}

	public function translate($str){
		$source = trim($str[1]);
		if (empty($source)){
			return $source;
		}

		$translated = _($source);
		if ($translated == $source) {
			// Add this string via the API
		}
		return $translated;
		
	}
	
}
