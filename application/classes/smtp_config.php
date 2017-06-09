<?php

defined('SYSPATH') or die("No direct script access.");

# Mongo
$smtp_result = array();
$mongo_db        = MangoDB::instance('default');
$result = $mongo_db->findOne(MDB_SMTP_SETTINGS,array('_id'=>1),array('smtp_username','smtp_password','smtp_host','transport_layer_security','smtp_port','smtp'));

$smtp_result[] = $result;

/* Mysql
$smtp_result = DB::select(SMTP_SETTINGS.'.smtp_username',SMTP_SETTINGS.'.smtp_password',SMTP_SETTINGS.'.smtp_host',SMTP_SETTINGS.'.transport_layer_security',SMTP_SETTINGS.'.smtp_port',SMTP_SETTINGS.'.smtp')->from(SMTP_SETTINGS)
		->where('id', '=', '1')
		->execute()
		->as_array();
*/		

define("SMTP_USERNAME",$smtp_result[0]["smtp_username"]);
define("SMTP_PASSWORD",$smtp_result[0]["smtp_password"]);
define("SMTP_HOST",$smtp_result[0]["smtp_host"]);
define("SMTP_TRANSPORT_LAYER_SECURITY",$smtp_result[0]["transport_layer_security"]);
define("SMTP_PORT",$smtp_result[0]["smtp_port"]); 
//define("SMTP",$smtp_result[0]["smtp"]); 		
		
?>
