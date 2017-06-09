<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Finding the Locations details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/

class Model_TaximobilityFinds extends Model
{
	public function getlatitudelong($address) 
	{		
		$address = str_replace(' ', '+', $address);
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&sensor=false&key='.GOOGLE_GEO_API_KEY;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$geoloc = curl_exec($ch);
		
		//print_r($geoloc);
		$json = json_decode($geoloc);	
		//print_r($json);exit;
		if($json->status == 'OK')
		{				
		return array($json->results[0]->geometry->location->lat, $json->results[0]->geometry->location->lng);
		}
		else
		{
			return array(11.621354, 76.14253698);
		}
		
	}

	
	public function find_Haversine($start, $finish) 
	{	
		$theta = $start[1] - $finish[1]; 
		$distance = (sin(deg2rad($start[0])) * sin(deg2rad($finish[0]))) + (cos(deg2rad($start[0])) * cos(deg2rad($finish[0])) * cos(deg2rad($theta))); 
		$distance = acos($distance); 
		$distance = rad2deg($distance); 
		$distance = $distance * 60 * 1.1515; 
		
		return round($distance, 2);
	}	
}
