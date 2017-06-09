<?php defined('SYSPATH') or die('No direct access allowed.');
/****************************************************************
* Contains Common Function Details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Kohana_Commonfunction
{
    /**
     * @var array configuration settings
     */
    protected $_config = array();
    /**
     * Class Main Constructor Method
     * This method is executed every time your module class is instantiated.
     */
    public function __construct() { }
    /**
     * ****Image resize ****
     * @return Image listings  */
    static function logoresize($image_factory, $width, $height, $path, $image_name, $quality = 90)
    {
        $image_name = $image_name . '.png';
        $image_factory->resize($width, $height, Image::NONE);
        $image_factory->crop($width, $height);
        $image = $image_factory->save($path . $image_name, 90);
        return $image;
    }
    static function imageresize($image_factory, $width, $height, $path, $image_name, $quality = 90)
    {
        if ($image_factory->height < $height || $image_factory->width < $width) {
            $image = $image_factory->save($path . $image_name, 90);
            return $image;
        } else {
            $image_factory->resize($width, $height, Image::NONE);
            $image_factory->crop($width, $height);
            $image = $image_factory->save($path . $image_name, 90);
            return $image;
        }
    }
    static function taxiimageresize($image_factory, $width, $height, $path, $image_name, $quality = 90)
    {
        $image_factory->resize($width, $height, Image::NONE);
        $image_factory->crop($width, $height);
        $image = $image_factory->save($path . $image_name, 90);
        return $image;
    }
    static function multipleimageresize($image_factory, $width, $height, $path, $image_name, $quality = 90)
    {
        $image_name = $image_name . '.png';
        $image_factory->resize($width, $height, Image::NONE);
        $image_factory->crop($width, $height);
        $image = $image_factory->save($path . $image_name, 90);
        return $image;
    }
    /**To Get Current TimeStamp**/
    static function getCurrentTimeStamp()
    {
        return date('Y-m-d H:i:s', time());
    }
    //function for generating random key
    //=================================	 
    static function admin_random_user_password_generator()
    {
        $string = Text::random('hexdec', RANDOM_KEY_LENGTH);
        return $string;
    }
    //function for generating random key
    //=================================	 
    static function randomkey_generator($length = 0)
    {
        $length = ($length == 0) ? RANDOM_KEY_LENGTH : $length;
        $string = Text::random('hexdec', $length);
        return strtoupper($string);
    }
    //Save original image size
    static function imageoriginalsize($image_factory, $path, $image_name, $quality = 90)
    {
        $image = $image_factory->save($path . $image_name, $quality);
        return $image;
    }
    //MongoDB Bulk datas changing the datatypes
    static function mongo_format_array($values)
    {
            $active_ids = array();
            foreach($values as $each_id):
                    $active_ids[] = (int)$each_id;
            endforeach;
            return $active_ids;
    }
    //function for php 5.5 mysqli_real_escape_string function expects paramerter to be database connection link. So here this function will solve the issue with old mysql_real_escape_string
    static function real_escape_string($data)
    {
        $result = Database::instance()->escape($data);
        return $result;
    }
    //For City && State &  Country Based data update query filter data
    static function get_collection_index($countryid,$stateid,$cityid,$field,$type=false)
    {
        $country_id = (int)$countryid;
        $state_id = (int)$stateid;
        $city_id = (int)$cityid;
        $mongodb = MangoDB::instance('default');
                         $options=[
                             'projection'=>[],
                             'limit'=>1                                            
                         ];
                $rs1 = $mongodb->find(MDB_CSC,['_id'=>$country_id],$options);
		$result1 = (!empty($rs1))?$rs1:array();
        if($field=='state'){
            foreach($result1[0]['stateinfo'] as $key => $val){
                if($val['state_id'] == $state_id){
                    $state_index = $key;
                    break;
                }
            }
            return $state_index;
        } else {
            foreach($result1[0]['stateinfo'] as $key => $val){
                if($val['state_id'] == $state_id){
                    $state_index = $key;
                    break;
                }
            }
            $city_index=0;$city_status='D';$city_default=0;
            if(isset($result1[0]['stateinfo'][$state_index]['cityinfo'])){
				foreach($result1[0]['stateinfo'][$state_index]['cityinfo'] as $key => $val){
					if($val['city_id'] == $city_id){
						$city_index = $key;
						$city_status = $val['city_status'];
						$city_default = $val['default'];
						$city_name = $val['city_name'];
						$city_model_fare = $val['city_model_fare'];
						$zipcode = isset($val['zipcode'])?$val['zipcode']:'';
						break;
					}
				}
			}
            if($type){
                return array('city_index'=>$city_index,'city_status'=>$city_status,'city_default'=>$city_default,'zipcode'=>$zipcode,'city_model_fare'=>$city_model_fare,'city_name' => $city_name);
            } else {
                return array('city_index'=>$city_index,'city_status'=>$city_status,'city_default'=>$city_default);
            }
        }
    }
    //Get address details based on latitude,longitude passed agruments
    static function getaddress($lat, $lng)
    {
        $url    = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&sensor=false&key=' . GOOGLE_GEO_API_KEY;
        $json   = @file_get_contents($url);
        $data   = json_decode($json);
        $status = $data->status;
        if ($status == "OK")
            return $data->results[0]->formatted_address;
        else
            return false;
    }
    // PHP strtotime compatible strings
    static function dateDiff($time1, $time2, $precision = 6)
    {
        // If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }
        // Set up intervals and diffs arrays
        $intervals = array(
            'year',
            'month',
            'day',
            'hour',
            'minute',
            'second'
        );
        $diffs     = array();
        // Loop thru all intervals
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime  = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add    = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }
            $time1            = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }
        $count = 0;
        $times = array();
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval 
            // if value is bigger than 0
            if ($value > 0) {
                // Add s if value is not 1
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }
        // Return string with times
        return implode(", ", $times);
    }
    
    //To convert the mongo date into php date foramte
    static function convertphpdate($dateformate,$mongodate)
    {
        $elsparam = "";
        if(is_object($mongodate)){
            # seconds object change to milliseconds string added to support LAMP 7.0 and its mongo version
            if($dateformate!=""){
                $sec=((string) $mongodate)/1000;
                $date = new DateTime('@'.$sec, new DateTimeZone(USER_SELECTED_TIMEZONE));
                $date->setTimezone(new DateTimeZone(USER_SELECTED_TIMEZONE));
		return $value = $date->format($dateformate);
            }else{
                $sec=((string) $mongodate)/1000;
                $date = new DateTime('@'.$sec, new DateTimeZone(TIMEZONE));
                $date->setTimezone(new DateTimeZone(USER_SELECTED_TIMEZONE));
		return $value = $date->format("Y-m-d H:i:s");
            }
        }else{
            if(is_array($mongodate) && $mongodate['$date']!=""){
                if($dateformate!=""){
                    $sec=($mongodate['$date'])/1000;
                    $date = new DateTime('@'.$sec, new DateTimeZone(TIMEZONE));
                    $date->setTimezone(new DateTimeZone(USER_SELECTED_TIMEZONE));
                    return $value = $date->format($dateformate);
                }else{
                    $sec=($mongodate['$date'])/1000;
                    $date = new DateTime('@'.$sec, new DateTimeZone(TIMEZONE));
                    $date->setTimezone(new DateTimeZone(USER_SELECTED_TIMEZONE));
                    return $value = $date->format("Y-m-d H:i:s");
                }
            }else{
                return $elsparam;
            }
        }
    }

    //To get last id 
    static function get_auto_id($table_name)
    {
        $mongodb = MangoDB::instance('default');
        $options=[
            'projection'=>[
                    '_id'=>1,                               
            ],
            'sort'=>[
                    '_id'=>-1
             ],
            'limit'=>1
        ];
        $result = $mongodb->find($table_name,[],$options);
        $id = (!empty($result))?array($result[0]['_id']=>0):array(1);
        reset($id);
        $first_key = key($id);
        return $first_key+1;
    }
    
    //To change the array key value
    static function change_key( $array ) {
        $new_index = array();
        if(!empty($array) && count($array) > 0){
            for($i=0;$i<count($array);$i++){
                $new_index[$i] = $i;
            }
            return array_combine( $new_index, $array );
        }else{
            return array();
        }
    }
    
    
    static public function get_user_wallet_amount()
    {
        $session = Session::instance();
        $response = array();
        $result["wallet_amount"] = 0; $result["referral_code"] = "";
        $mongo_db = MangoDB::instance('default');
        $passid = $session->get("id");
        $project = array('wallet_amount','referral_code');
        $result = $mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$passid),$project);
        if(count($result)>0){
            $response["wallet_amount"] = isset($result["wallet_amount"]) ? $result["wallet_amount"] :0;
            $response["referral_code"] = isset($result["referral_code"]) ? $result["referral_code"] :'';
        }
        return $response;
    }
	
    static function getDateTimeFormat($value,$type)
    {   
        if($value != "0000-00-00 00:00:00" && $value != "0000-00-00" && $value!='') {
            if(($type == 1) || ($type == 2) ||  ($type== 4)) {
                if($type == 1) {
                    $value = str_replace('/', '-', $value);
                    $timestamp = strtotime($value);
                    return date(DEFAULT_DATE_TIME_FORMAT, $timestamp);
                } else if($type == 2) {
                    $timestamp = strtotime($value);
                    $format = explode(" ",DEFAULT_DATE_TIME_FORMAT);
                    $format = isset($format[0]) ? $format[0] : DEFAULT_DATE_TIME_FORMAT;
                    return date($format, $timestamp);
                } else if($type == 4) {
                    $timestamp = strtotime($value);
                    $format = explode(" ",DEFAULT_DATE_TIME_FORMAT);                                        
                    $format = isset($format[1]) ? $format[1] : 'h:i:s A';
                        return date($format, $timestamp);
                } 
            } else {
                $timestamp = strtotime($value);
                return date(DEFAULT_DATE_TIME_FORMAT, $timestamp);
            }
        } else {
                return $value;
        }
    }
    static function ensureDatabaseFormat($date,$type)
    {
        $date = str_replace('/', '-', $date);
        $date = explode(" ",$date);
        if(isset($date[1])) {
                $start_con = $end_con = $date[1];
        } else {
                $start_con = "00:00:00";
                $end_con = "23:59:59";
        }
        if($type == 1) {
                $date = $date[0]." ".$start_con;
        } else if($type == 2) {
                $date = $date[0]." ".$end_con;
        } else if($type == 3) {
                return date("Y-m-d", strtotime($date[0]));
        }
        $timestamp = strtotime($date);
        return date("Y-m-d H:i:s", $timestamp);
    }
	
    static function getCityName($latitude,$longitude)
    {
        $city = "";
        if($latitude != "" && $longitude != "") {
            $geocode = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&sensor=false');
            $output = json_decode($geocode);
            for($j=0;$j<count($output->results[0]->address_components);$j++) {
                $cn = array($output->results[0]->address_components[$j]->types[0]);
                if(in_array("locality", $cn))
                {
                    $city = strtolower($output->results[0]->address_components[$j]->long_name);
                }
            }
        }
        return $city;
    }
	
    static function checkRequestID()
    {
        $mongodb = MangoDB::instance('default');
        $string = Text::random('hexdec', 10);
        $result = $mongodb->count(MDB_WITHDRAW_REQUEST,array('_id'=>$string),array('_id'));
        if($result == 0) {
            return $string;
        } else {
            commonfunction::checkRequestID();
        }
    }
    /***** Set Mongo Date UTC Date Time Format ******/
    static function MongoDate($currentdatetime)
    {              
        if($currentdatetime!='0000-00-00 00:00:00')
        {        
            $server_date=date('Y/m/d H:i:s', $currentdatetime);
            $timezonedate = new DateTime($server_date, new DateTimeZone(USER_SELECTED_TIMEZONE));
            $timestamp = $timezonedate->format('U');
            $timestamp = $timestamp*1000;
            $utcdatetime = new MongoDB\BSON\UTCDateTime($timestamp);           
            return $utcdatetime;
        }
        else
        {
            $timezonedate = new DateTime($currentdatetime, new DateTimeZone(USER_SELECTED_TIMEZONE));
            $timestamp = $timezonedate->format('U'); 
            $timestamp = $timestamp*1000;
            $utcdatetime = new MongoDB\BSON\UTCDateTime($timestamp);                          
            return $utcdatetime;
        }
    }
    /***** Set Mongo Regex ******/
    static function MongoRegex($keyword,$flags='')
    {   
        if($flags!='')
        {
            $mongoregex = new MongoDB\BSON\Regex($keyword,$flags);
            $mongoregex_array['$regex']=$mongoregex->getPattern();
            $mongoregex_array['$options']=$mongoregex->getFlags();
            return $mongoregex_array;
        }else{
            $split_record=explode('/',$keyword);                
            $keyword=$split_record[1];
            $flags=$split_record[2];
            $mongoregex = new MongoDB\BSON\Regex($keyword,$flags); 
            $mongoregex_array['$regex']=$mongoregex->getPattern();
            $mongoregex_array['$options']=$mongoregex->getFlags();
            return $mongoregex_array;
        }
    }
    
    # created date by user time zone
    static function createdateby_user_timezone()
    {   
        $currenttime_timezone = new DateTime('now', new DateTimeZone(USER_SELECTED_TIMEZONE));
		$createdate = $currenttime_timezone->format('Y-m-d H:i:s');	
		return $createdate;
    }
    
}
