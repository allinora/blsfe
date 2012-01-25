<?php

class BLGeo {
	
	public function __construct(){
	}

	function address2coords($address){
	    $url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false';
	    $json = file_get_contents($url);
	    $data = json_decode($json);

	    $location = $data->results[0]->geometry->location;
		return $location;
	}
	function coords2address($coordinates){
	    $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.urlencode($coordinates).'&sensor=false';
	    $json = file_get_contents($url);
	    $data = json_decode($json);
	    return $data->results[0]->formatted_address;
	}
}
