<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Finding the Locations details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Model_TaximobilityFind114 extends Model
{
	public function __construct()
    {
        //$this->currentdate = Commonfunction::getCurrentTimeStamp();
		$this->mongo_db = MangoDB::instance('default');
    }
    
    /** get nearest driver list - optimized query 22oct16 **/
	public function getNearestDrivers($modelId,$lat,$long,$current_time,$companyId,$distance,$unit)
	{
		$temp_arr = $result = array();
		$current_date      = explode(' ', $current_time);
        $start_time        = $current_date[0].' 00:00:01';
		$match1 = array('people.status' => 'A','status' => 'F','shift_status' => 'IN');
		if($distance !=''){
			$match1['distance'] = array('$lte' => (double)$distance);
		}

        $match2 = array( 'updatetime_difference' => array('$lte' => (int)LOCATIONUPDATESECONDS),
						 'tmap.mapping_startdate' => array('$lte' => Commonfunction::MongoDate(strtotime($current_time))),
						 'tmap.mapping_enddate' => array('$gte' => Commonfunction::MongoDate(strtotime($current_time))),
						 'tmap.mapping_status' => 'A'
					 );
		# company status
		$match2['comp.companydetails.company_status'] = 'A';
		
		if ($companyId !=''){
			$match2['tmap.mapping_companyid'] =  (int)$companyId;
			$match2['taxi.taxi_company'] =  (int)$companyId;
        }
        
        if ($modelId != ''){
			$match2['taxi.taxi_model'] =  (int)$modelId;   
        }       
        
		$multiplier = (UNIT == '0') ? 0.001 : 0.000621371192237 ;  
		$geonear = array('near' => array( 'type' => "Point", 'coordinates' => array( (double)$long , (double)$lat )),
						'distanceField' => "distance",
						'spherical' => true,
						'distanceMultiplier' => (double)$multiplier,
						'num' => 1000000
					); 
		$args = array(		
					array('$geoNear' => $geonear),
					array('$lookup' => array('from' => MDB_PEOPLE,'localField' => '_id','foreignField' => '_id','as' => 'people')),
					//array('$unwind' => '$people'),
					array('$unwind' =>  array( 'path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true)),		
					array('$match' => $match1),
					array('$project' => array('_id' => '$_id',
							   'distance' => '$distance',
							   'shift_status' => '$shift_status',
							   'status' => '$status',
							   'loc' => '$loc.coordinates',
							   'people' => 1,
								'updatetime_difference' => array('$multiply' => array(array('$subtract' => array(Commonfunction::MongoDate(strtotime($current_time)),'$update_date')),0.0001))
						)),
					array('$lookup' => array('from' => MDB_TAXI_DRIVER_MAPPING,'localField' => '_id','foreignField' => 'mapping_driverid',
					'as' => 'tmap')),
					array('$unwind' =>  array( 'path' =>  '$tmap', 'preserveNullAndEmptyArrays' =>  true)),		
					array('$lookup' => array('from' => MDB_TAXI,'localField' => 'tmap.mapping_taxiid','foreignField' => '_id','as' => 'taxi')),
					array('$unwind' =>  array( 'path' =>  '$taxi', 'preserveNullAndEmptyArrays' =>  true)),		
					array('$lookup' => array('from' => MDB_COMPANY,'localField' => 'tmap.mapping_companyid','foreignField' => '_id','as' => 'comp')),
					array('$unwind' =>  array( 'path' =>  '$comp', 'preserveNullAndEmptyArrays' =>  true)),		
					array('$match' => $match2),
					array('$group' => array('_id' => array('driver_id' => '$_id'),
										'loc' => array('$addToSet' => '$loc'),
										'distance' => array('$addToSet' => '$distance'),
										'taxi_speed' => array('$addToSet' => '$taxi.taxi_speed'),
										'booking_limit' => array('$addToSet' => '$people.booking_limit'),
										'updatetime_difference' => array('$addToSet' => '$updatetime_difference'),
										))
			);
		$res = $this->mongo_db->Aggregate(MDB_DRIVER_INFO, $args);
		if(!empty($res['result'])){
			foreach($res['result'] as $datas){
						
				$lat = $long = '';	
				$temp_arr['driver_id'] = $datas['_id']['driver_id']; 
				$temp_arr['distance_km'] = !empty($datas['distance']) ? $datas['distance'][0]:0; 
				$temp_arr['taxi_speed'] = !empty($datas['taxi_speed']) ? $datas['taxi_speed'][0]:0; 
				$temp_arr['booking_limit'] = isset($datas['booking_limit'][0]) ? $datas['booking_limit'][0]:0; 
				$temp_arr['updatetime_difference'] = !empty($datas['updatetime_difference'])?$datas['updatetime_difference'][0]:''; 				
				$loc = !empty($datas['loc']) ? $datas['loc'][0] :array();
				if(!empty($loc)){
					$lat = $loc[1];
					$long = $loc[0];
				}
				$temp_arr['latitude'] = $lat;
				$temp_arr['longitude'] = $long;
				# check driver booking limit
				$buk_limit = $this->mongo_db->count(MDB_PASSENGERS_LOGS,
														array('createdate'=>array('$gte'=> Commonfunction::MongoDate(strtotime($start_time))),
															'driver_id'=> (int)$temp_arr['driver_id'],
															'travel_status'=>1,
															'booking_from' => array('$ne'=>2)));
				
				if($buk_limit < $temp_arr['booking_limit']){
					$result[] = $temp_arr;
				}			
			}
		}
		//print_r($res);exit;
		return $result;
	}
	public function free_availabletaxisearch_list($motor_company = '', $motor_model = '', $company_id = '')
    {
		$current_time      = convert_timezone('now', TIMEZONE);
        $current_date      = explode(' ', $current_time);
        $start_time        = $current_date[0] . ' 00:00:01';
        $end_time          = $current_date[0] . ' 23:59:59';
		//$start_time = '2015-04-21 00:00:01';
		//$end_time = '2015-04-21 01:58:53';
		$match = array(
				'people.status'=>"A",
				'people.booking_limit'=> array('$gt' => $this->mongo_db->count(MDB_PASSENGERS_LOGS,array('createdate'=>array(
									'$gte'=> Commonfunction::MongoDate(strtotime($start_time))),
									'driver_id'=>'people._id',
									'travel_status'=>1,
									'booking_from' => array('$ne'=>2)))),
				'taxi_status' => 'A',
				'taxi_availability' => 'A',
				'people.availability_status' => 'A',
				'taxi_mapping.mapping_status' => 'A',				
				'company.companydetails.company_status' => 'A',      
				'taxi_mapping.mapping_startdate' => array('$lte' => Commonfunction::MongoDate(strtotime($start_time))),
				'taxi_mapping.mapping_enddate' => array('$gte' => Commonfunction::MongoDate(strtotime($end_time))),
				'package_report.check_package_type' => 'T',
				'package_report.upgrade_expirydate' => array('$gte' => Commonfunction::MongoDate(strtotime($end_time)))
			);
        if ($company_id != "") {
            $match['taxi_mapping.mapping_companyid'] = (int)$company_id;
			$match['people.company_id']	 = (int)$company_id;
			$match['taxi_company'] = (int)$company_id;
        }
		if (isset($motor_company) && $motor_company != '') {
			$match['taxi_type'] = 1;
        }
        if (isset($motor_model) && ($motor_model != '')) {
			$match['taxi_model'] = (int)$motor_model;
        }
		$arguments = array(array('$lookup'=>array(
					'from'=>MDB_COMPANY,
					'localField'=>"taxi_company",
					'foreignField'=>"_id",
					 'as'=>"company"        
				)),
				array('$unwind'=>'$company'),
				array('$lookup'=>array(
					'from'=>MDB_TAXI_DRIVER_MAPPING,
					'localField'=>"_id",
					'foreignField'=>"mapping_taxiid",
					 'as'=>"taxi_mapping"        
				)),
				array('$unwind'=>'$taxi_mapping'),
				/*array('$lookup'=>array(
					'from'=>MDB_PACKAGE_REPORT,
					'localField'=>"taxi_company",
					'foreignField'=>"upgrade_companyid",
					 'as'=>"package_report"        
				)),
				array('$unwind'=>'$package_report'),*/
				array('$lookup'=>array(
					'from'=>MDB_PEOPLE,
					'localField'=>"taxi_mapping.mapping_driverid",
					'foreignField'=>"_id",
					 'as'=>"people"        
				)),
				array('$unwind'=>'$people'),
				array('$match'=>$match),
				array('$group' => array('_id'=>array('taxi_id'=>'$_id',
							'id'=>'$people._id',
							/*'check_package_type'=>'$package_report.check_package_type',
							'upgrade_expirydate'=>'$package_report.upgrade_expirydate',*/
							'booking_limit'=>'$people.booking_limit'))),
				array('$sort'=>array('_id.id'=>1))
			);		
        $result = $this->mongo_db->aggregate(MDB_TAXI,$arguments);
        //print_r($result);exit;
        return (isset($result['result']) ? $result['result']: array()); 		
    }
	
    public function search_driver_mobileapp($params)
    {
        $flag            = '';
        $unit_conversion = "";
        $assigned_driver = $this->free_availabletaxisearch_list($params['motor_company'],$params['motor_model'],$params['company_id']);
        //print_r($assigned_driver);exit;
        $driver_list_array = array();
        foreach ($assigned_driver as $key => $value) {
            $driver_list_array[] = $value['_id']['id'];
        }
		$company_id = $params['company_id'];
		// City model fare getting here, But not used in any query
		$city_model_fare = '';
		$cityname = $params['cityname'];
		if($cityname != ''){
			$arguments = array(array('$unwind'=>'$stateinfo'),
							array('$unwind'=>'$stateinfo.cityinfo'),
							array('$match'=>array(
								'stateinfo.cityinfo.city_name' =>  Commonfunction::MongoRegex("/$cityname/i"))),
							array('$project'=>array('city_model_fare'=>'$stateinfo.cityinfo.city_model_fare')),
							array('$limit' => 1)
					);			
		}else{
			$arguments = array(array('$unwind'=>'$stateinfo'),
							   array('$unwind'=>'$stateinfo.cityinfo'),
								array('$match'=>array(
													  'default'=>1,
													  'stateinfo.default'=>1,
													  'stateinfo.cityinfo.default'=>1
												 )),
								 array('$project'=>array('city_model_fare'=>'$stateinfo.cityinfo.city_model_fare'))
						);
		}			
		$city_query = $this->mongo_db->aggregate(MDB_CSC,$arguments);
		$city = (!empty($city_query['result'])?$city_query['result']:array());
		//print_r($cityname);exit;
        if (count($city) > 0) {
            $city_model_fare = $city[0]['city_model_fare'];
        }       
        
		if ($company_id == '') {
            $current_time = convert_timezone('now', TIMEZONE);
            $current_date = explode(' ', $current_time);
            $start_time   = $current_date[0] . ' 00:00:01';
            $end_time     = $current_date[0] . ' 23:59:59';
        } else {
            //$model_base_query = "select time_zone from  company where cid='$company_id' ";
            $result = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$company_id),array('companydetails.time_zone'));
			if(!empty($result)){
				$time_zone = isset($result['companydetails'][ 'time_zone' ])?$result['companydetails'][ 'time_zone' ]:"";
			}
            if ($time_zone != '') {
                $current_time = convert_timezone('now', $time_zone);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:01';
                $end_time     = date('Y-m-d') . ' 23:59:59';
            }
        }        
		//$current_time = '2015-04-22 17:42:51';
        //$start_time = '2015-12-21 01:57:29';
        //$end_time = '2015-12-21 01:57:29';
		//print_r($driver_list_array);exit;
        $latitude = (double)$params['latitude'];
        $longitude = (double)$params['longitude'];		
		$match1   = array(  'people.login_status' => 'S',
							'status' => 'F',
							'shift_status' => 'IN',
							'_id'=>array('$in'=>$driver_list_array)
						);
		
		if (isset($params['distance'])) {
            $match1['distance'] =  array('$lte'=> $params['distance']);
        }else{
			$match1['distance'] = array('$lte' => DEFAULTMILE);
		}								
						
        $match2   = array(  'tmap.mapping_startdate' => array('$lte'=> Commonfunction::MongoDate(strtotime($start_time))),
							'tmap.mapping_enddate' => array('$gte'=>Commonfunction::MongoDate(strtotime($end_time))),
							'tmap.mapping_status' => 'A',
						);
        
		
		if ($params['taxi_fare_km'] != '') {
            $match2['model.min_fare'] = array('$lte' => $params['taxi_fare_km']);
        }
		if ($company_id) {
			$match2['tmap.mapping_companyid'] =  (int)$company_id;
			$match2['taxi.taxi_company'] =  (int)$company_id;
        }
		if ($params['motor_company']) {
			$match2['taxi.taxi_type'] =  (int)$params['motor_company'];   
        }
        if (($params['motor_model'] != 0) && ($params['motor_model'] != null)) {
			$match2['taxi.taxi_model'] =  (int)$params['motor_model'];   
        }
        //echo '<pre>';print_r($params);exit;
	    if (UNIT == '0') {
			$geonear = array('near'=>array('type'=>'Point','coordinates'=>array($longitude,$latitude)),
							'distanceField'=>"distance",
							'maxDistance' => $params['miles']*1000,
							'spherical'=>true,
							'distanceMultiplier'=>0.001,
							 'num'=>1000000        
						);
        }else {
            //Get the result In Miles
            $geonear = array('near' => array( 'type' => "Point", 'coordinates' => array( $longitude , $latitude )),
						'distanceField' => "distance",
						'maxDistance' => $params['miles']*1000,
						'spherical' => true,
						'distanceMultiplier' => 0.000621371192237,
						'num' => 1000000
					);
        }
		$arguments = array(
			array('$geoNear'=>$geonear ),						
			array('$lookup' => array(
				'from' => MDB_PEOPLE,
				'localField' => '_id',
				'foreignField' => '_id',
				'as' => 'people'									
			)),
			array('$unwind'=>'$people'),			
			array('$match'=>$match1),	
			array('$sort'=>array('distance'=>1)),
			array('$project' => array(
							'_id' => 1,
							'distance' => '$distance',
							'shift_status' => '$shift_status',
							'status' => '$status',
							'loc' => '$loc.coordinates',
							'people' => 1,	
							'updatetime_difference' => array('$subtract'=>
								array(Commonfunction::MongoDate(strtotime($current_time)),'$update_date'))
						)),
			array('$lookup'=>array(
							'from' => MDB_TAXI_DRIVER_MAPPING,
							'localField' => '_id',
							'foreignField' => 'mapping_driverid',
							'as' => 'tmap'
						)),
			array('$unwind'=>'$tmap'),
			array('$lookup'=>array(
				'from' => MDB_TAXI,
				'localField' => 'tmap.mapping_taxiid',
				'foreignField' => '_id',
				'as' => 'taxi'
			)),
			array('$unwind'=>'$taxi'),
			array('$lookup'=>array(
				'from' => MDB_MOTOR_MODEL,
				'localField' => 'taxi.taxi_model',
				'foreignField' => '_id',
				'as' => 'model'
			)),
			array('$unwind'=>'$model'),
			array('$lookup'=>array(
				'from' => MDB_COMPANY,
				'localField' => 'tmap.mapping_companyid',
				'foreignField' => '_id',
				'as' => 'comp'
			)),
			array('$unwind'=>'$comp'),
			array('$match'=>$match2),
			array('$group'=>array('_id'=>array('driver_id'=>'$_id',
					'name' => '$people.name',
					'model_name' => '$model.model_name',
					'phone' => '$people.phone',
					'd_photo' => '$people.profile_picture',
					'id' => '$people._id',
					'loc' => '$loc',
					'status' => '$status',
					'distance' => '$distance', 
					'distance_miles' => '$distance',
					'updatetime_difference' => '$updatetime_difference',
					'company_name' => '$comp.company_name',
					'get_companyid' => '$comp._id',
					'cancellation_nfree' => '$comp.companyinfo.cancellation_fare',
					'company_tax' => '$comp.companyinfo.company_tax',
					'taxi_no' => '$taxi.taxi_no',
					'taxi_image' => '$taxi.taxi_image',
					'taxi_capacity' => '$taxi.taxi_capacity',
					'taxi_id' => '$taxi._id',
					'taxi_speed' => '$taxi.taxi_speed'
				)))
		);
		$result = $this->mongo_db->aggregate(MDB_DRIVER_INFO,$arguments);
		return (isset($result['result'])) ? $result['result'] : array();
    }
    public function getlatitudelong($address)
    {
        $address = str_replace(' ', '+', $address);
        $url     = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key=' . GOOGLE_GEO_API_KEY;
        $ch      = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $geoloc = curl_exec($ch);
        //print_r($geoloc);
        $json   = json_decode($geoloc);
        //print_r($json);exit;
        if ($json->status == 'OK') {
            return array(
                $json->results[0]->geometry->location->lat,
                $json->results[0]->geometry->location->lng
            );
        } else {
            return array(
                11.621354,
                76.14253698
            );
        }
    }
    public function find_Haversine($start, $finish)
    {
        $theta    = $start[1] - $finish[1];
        $distance = (sin(deg2rad($start[0])) * sin(deg2rad($finish[0]))) + (cos(deg2rad($start[0])) * cos(deg2rad($finish[0])) * cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        return round($distance, 2);
    }
    // search nearest drivers around passengers pickup location
    public function nearestdrivers($lat, $long, $taxi_model, $passenger_id, $distance = NULL, $company_id, $unit, $service_type)
    {
        $free_driver       = $this->availabledrivers($passenger_id, $company_id);
		//print_r($free_driver);exit;
        $match1            = array();
        $match2            = array();
        $driver_list_array = array();        
        if ($free_driver > 0) {
            foreach ($free_driver as $key => $value) {
                $driver_list_array[] = $value['_id']['id'];
            }
        } else {
            $driver_list_array = array();
        }
        // Find already rejected and timeout drivers in the current trip
        
        if ($company_id == '') {
            $current_time = convert_timezone('now', TIMEZONE);
            $current_date = explode(' ', $current_time);
            $start_time   = $current_date[0] . ' 00:00:01';
            $end_time     = $current_date[0] . ' 23:59:59';
            //echo '1-'.TIMEZONE;
        } else {
            //$query = $this->mongo_db->find(MDB_COMPANY,array('_id'=>(int)$company_id),array('companydetails.time_zone'));
             ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                    'companydetails.time_zone'=>1
                ]
            ];
            $query = $this->mongo_db->find(MDB_COMPANY,['_id'=>(int)$company_id],$options);
            $result = $query;
			if(!empty($result)){
				$time_zone = isset($result[ $company_id ]['companydetails'][ 'time_zone' ])?$result[ $company_id ]['companydetails'][ 'time_zone' ]:"";
			}
            if ($time_zone != '') {
                $current_time = convert_timezone('now', $time_zone);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:01';
                $end_time     = date('Y-m-d') . ' 23:59:59';
            }
            echo $current_time;
        }
        //exit;
        $latitude = (float)$lat;
        $longitude = (float)$long;
        $flag                   = 1;  
        $service_types = "";
        if ($service_type) {
            $match1['taxi_service_type'] =  array('$in'=> $service_type);
        }
        if ($unit == '0') {
             //Get the result In KM
            $geonear = array('near'=>array('type'=>'Point','coordinates'=>array($longitude,$latitude)),
							'distanceField'=>"distance",
							'maxDistance' => $distance*1000,
							'spherical'=>true,
							'distanceMultiplier'=> 0.001,
							 'num'=>1000000        
						);
        }else {
            //Get the result In Miles
            $geonear = array('near' => array( 'type' => "Point", 'coordinates' => array( $longitude , $latitude )),
						'distanceField' => "distance",
						'maxDistance' => $distance*1000,
						'spherical' => true,
						'distanceMultiplier' => 0.000621371192237,
						'num' => 1000000
					);
        }        
		//print_r($taxi_model);//exit;
        $match1   = array(  'people.login_status' => 'S',
							'status' => 'F',
							'shift_status' => 'IN',
							'_id'=>array('$in'=>$driver_list_array)
						);
        $match2   = array(  'updatetime_difference' => array('$lte'=>LOCATIONUPDATESECONDS),
							'tmap.mapping_startdate' => array('$lte'=> Commonfunction::MongoDate(strtotime($start_time))),
							'tmap.mapping_enddate' => array('$gte'=> Commonfunction::MongoDate(strtotime($end_time))),
							'tmap.mapping_status' => 'A',
						);
        if ($company_id != "") {
            $match2['tmap.mapping_companyid'] =  (int)$company_id;
            $match2['tmap.taxi_company'] =  (int)$company_id;
        }
        if ($taxi_model != 'All' && $taxi_model != null) {
           $match2['taxi.taxi_model'] =  (int)$taxi_model;
        }
        $arguments = array(
						array('$geoNear'=>$geonear),						
						array('$lookup' => array(
							'from' => MDB_PEOPLE,
							'localField' => '_id',
							'foreignField' => '_id',
							'as' => 'people'									
						)),
						array('$unwind'=>'$people'),
						array('$project' => array(
							'_id' => 1,
							'distance' => '$distance',
							'shift_status' => '$shift_status',
							'status' => '$status',
							'loc' => '$loc.coordinates',
							'people' => 1,	
							//'updatetime_difference' => array('$subtract'=>array(Commonfunction::MongoDate(strtotime($current_time)),'$update_date'))
							'updatetime_difference' => array('$multiply' => array(array('$subtract' => array(Commonfunction::MongoDate(strtotime($current_time)),'$update_date')),0.0001))
						)),
						array('$match'=>$match1),				
						array('$sort'=>array('distance'=>1)),
						array('$lookup'=>array(
							'from' => MDB_TAXI_DRIVER_MAPPING,
							'localField' => '_id',
							'foreignField' => 'mapping_driverid',
							'as' => 'tmap'
						)),
						array('$unwind'=>'$tmap'),
						array('$lookup'=>array(
							'from' => MDB_TAXI,
							'localField' => 'tmap.mapping_taxiid',
							'foreignField' => '_id',
							'as' => 'taxi'
						)),
						array('$unwind'=>'$taxi'),
						array('$lookup'=>array(
							'from' => MDB_COMPANY,
							'localField' => 'tmap.mapping_companyid',
							'foreignField' => '_id',
							'as' => 'comp'
						)),
						array('$unwind'=>'$comp'),
						array('$match'=>$match2),
						array('$group'=>array('_id'=>array('driver_id'=>'$_id',
							'loc' => '$loc',
							'distance_km' => '$distance',
							'taxi_speed' => '$taxi.taxi_speed'
						)))
					);
        $result = $this->mongo_db->aggregate(MDB_DRIVER_INFO,$arguments);
		//echo $current_time;exit;
        //return (isset($result['result']) ? (isset($result['result'][0]) ? $result['result'][0] : array()) : array());
        return (!empty($result['result'])) ? $result['result'] : array();
    }
    public function availabledrivers($availabledrivers, $company_id = '')
    {
        $current_time      = convert_timezone('now', TIMEZONE);
        $current_date      = explode(' ', $current_time);
        $start_time        = $current_date[0] . ' 00:00:01';
        $end_time          = $current_date[0] . ' 23:59:59';
        $company_condition = "";
        $match = array(
				'people.status'=>"A",
				'people.booking_limit'=> array('$gt' => $this->mongo_db->count(MDB_PASSENGERS_LOGS,array('createdate'=>array(
									'$gte'=> Commonfunction::MongoDate(strtotime($start_time))),
									'driver_id'=>'people._id',
									'travel_status'=>1,
									'booking_from' => array('$ne'=>2)))),
				'taxi_status' => 'A',
				'taxi_availability' => 'A',
				'people.availability_status' => 'A',
				'taxi_mapping.mapping_status' => 'A',				
				'company.companydetails.company_status' => 'A',      
				'taxi_mapping.mapping_startdate' => array('$lte' => Commonfunction::MongoDate(strtotime($start_time))),
				'taxi_mapping.mapping_enddate' => array('$gte' => Commonfunction::MongoDate(strtotime($end_time))),
				'package_report.check_package_type' => 'T',
				'package_report.upgrade_expirydate' => array('$gte' => Commonfunction::MongoDate(strtotime($start_time)))
			);
        if ($company_id != "" && $company_id != 0) {
            $match['taxi_mapping.mapping_companyid'] = (int)$company_id;
			$match['people.company_id']	 = (int)$company_id;
			$match['taxi_company'] = (int)$company_id;
        }		
		$arguments = array(array('$lookup'=>array(
					'from'=>MDB_COMPANY,
					'localField'=>"taxi_company",
					'foreignField'=>"_id",
					 'as'=>"company"        
				)),
				array('$unwind'=>'$company'),
				array('$lookup'=>array(
					'from'=>MDB_TAXI_DRIVER_MAPPING,
					'localField'=>"_id",
					'foreignField'=>"mapping_taxiid",
					 'as'=>"taxi_mapping"        
				)),
				array('$unwind'=>'$taxi_mapping'),
				/*array('$lookup'=>array(
					'from'=>MDB_PACKAGE_REPORT,
					'localField'=>"taxi_company",
					'foreignField'=>"upgrade_companyid",
					 'as'=>"package_report"        
				)),
				array('$unwind'=>'$package_report'),*/
				array('$lookup'=>array(
					'from'=>MDB_PEOPLE,
					'localField'=>"taxi_mapping.mapping_driverid",
					'foreignField'=>"_id",
					 'as'=>"people"        
				)),
				array('$unwind'=>'$people'),
				array('$project' => array(
					'taxi_status' => 1,
					'taxi_availability' => 1,
					'taxi_company' => 1,
					'company.companydetails' => 1,
					'taxi_mapping' => 1,
					'package_report' => 1,
					'people' => 1,	
					//'people' => array('$cond' => array(array('$eq'=>array(array('$size'=>'$people'),0)),null,'$people'))
				)),
				array('$match'=>$match),
				array('$group' => array('_id'=>array('taxi_id'=>'$_id',
							'id'=>'$people._id',
							/*'check_package_type'=>'$package_report.check_package_type',
							'upgrade_expirydate'=>'$package_report.upgrade_expirydate',*/
							'booking_limit'=>'$people.booking_limit'))),
				array('$sort'=>array('_id.id'=>1))
			);	         
        $result = $this->mongo_db->aggregate(MDB_TAXI,$arguments);
		//print_r($result);exit;
        return (isset($result['result']) ? $result['result']: array()); 
    }
}
