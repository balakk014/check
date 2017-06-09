<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Finding the Locations details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Model_TaximobilityFind extends Model
{
	public function __construct(){
		$this->mongo_db = MangoDB::instance('default');
		$this->currentdate = Commonfunction::getCurrentTimeStamp();
		# created date
		$this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
	}	
	public function getmodel_details($motorid)
	{
            $arguments = array(
            array('$match' => array('model_status' => 'A')),
            array(
                '$project' => array(
                    '_id' => 0,
                    'model_id' => '$_id',
                    'model_name' => '$model_name'
                )
            ),
            array('$sort' => array('model_id' => 1)),
        );
        $results = $this->mongo_db->aggregate(MDB_MOTOR_MODEL,$arguments);
        return (!empty($results['result']) && isset($results['result']))?$results['result']:array();
	}

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
	
	//to get model fare details
	public function modelDets($modelId)
	{
		$companyId = COMPANY_CID;
		if(FARE_SETTINGS == 2 && !empty($companyId))
		{
			$match_query['_id'] = 13;
			$match_query['model_fare.model_id'] = 1;
			$match_query['model_fare.fare_status'] = 'A';
			$arguments = array(
				array(
					'$unwind' => '$model_fare'
				),
				array(
					'$lookup' => array(
						'from' => MDB_MOTOR_MODEL,
						'localField' => 'model_fare.model_id',
						'foreignField' => '_id',
						'as' => 'motor_model'
					)
				),
				array(
					'$unwind' => '$motor_model'
				),
				array(
					'$match' => $match_query
				),
				array(
					'$project' => array(
						'model_id' => '$motor_model.model_id',
						'model_name' => '$motor_model.model_name',
						'model_size' => '$motor_model.model_size',
						'base_fare' => '$model_fare.base_fare',
						'min_fare' => '$model_fare.min_fare',
						'cancellation_fare' => '$model_fare.cancellation_fare',
						'min_km' => '$model_fare.min_km',
						'below_above_km' => '$model_fare.below_above_km',					
						'below_km' => '$model_fare.below_km',
						'above_km' => '$model_fare.above_km',
						'night_charge' => '$model_fare.night_charge',
						'night_timing_from' => array('$substr' => array('$model_fare.night_timing_from', 0, 5)),
						'night_timing_to' => array('$substr' => array('$model_fare.night_timing_to', 0, 5)),
						'night_fare' => '$model_fare.night_fare',
						'evening_charge' => '$model_fare.evening_charge',
						'evening_timing_from' => array('$substr' => array('$model_fare.evening_timing_from', 0, 5)),
						'evening_timing_to' => array('$substr' => array('$model_fare.evening_timing_to', 0, 5)),
						'evening_fare' => '$model_fare.evening_fare'
					)
				),
				array('$sort' => array('model_id' => 1))
			);
			$result = $this->mongo_db->aggregate(MDB_COMPANY, $arguments);
		}
		else
		{
			$match_query = array();
			$match_query['model_status'] = 'A';
			$match_query['_id'] = (int)$modelId;
			$arguments = array(
				array(
					'$match' => $match_query
				),
				array(
					'$project' => array(
						'model_id' => '$model_id',
						'model_name' => '$model_name',
						'model_size' => '$model_size',
						'base_fare' => '$base_fare',
						'min_fare' => '$min_fare',
						'cancellation_fare' => '$cancellation_fare',
						'min_km' => '$min_km',
						'below_above_km' => '$below_above_km',					
						'below_km' => '$below_km',
						'above_km' => '$above_km',
						'night_charge' => '$night_charge',
						'night_timing_from' => array('$substr' => array('$night_timing_from', 0, 5)),
						'night_timing_to' => array('$substr' => array('$night_timing_to', 0, 5)),
						'night_fare' => '$night_fare',
						'evening_charge' => '$evening_charge',
						'evening_timing_from' => array('$substr' => array('$evening_timing_from', 0, 5)),
						'evening_timing_to' => array('$substr' => array('$evening_timing_to', 0, 5)),
						'evening_fare' => '$evening_fare'
					)
				),
				array('$sort' => array('model_id' => 1))
			);
			$result          = $this->mongo_db->aggregate(MDB_MOTOR_MODEL, $arguments);
		}
		$symbol_array = $new_result = array();
		if(count($result['result']) > 0 ){
			foreach($result['result'] as $key => $val){
				$symbol_array[$key]['base_fare'] = CURRENCY.$val['base_fare'];
				$symbol_array[$key]['min_fare'] = CURRENCY.$val['min_fare'];
				$symbol_array[$key]['cancellation_fare'] = CURRENCY.$val['cancellation_fare'];
				$symbol_array[$key]['min_km'] = '( '.$val['min_km'].' '.UNIT_NAME.' )';
				$symbol_array[$key]['below_above_km'] = '( '.$val['below_above_km'].' '.UNIT_NAME.' )';
				$symbol_array[$key]['below_km'] = CURRENCY.$val['below_km'];
				$symbol_array[$key]['above_km'] = CURRENCY.$val['above_km'];
				$new_result['result'][$key] = array_merge($result['result'][$key],$symbol_array[$key]);
			}
			$result = $new_result;
		}
		$resultset = (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		return $resultset;
	}
	
	public function getFreeDriverList($modelId,$lat,$long,$current_time)
    {
		$company_id = 0;
        $commonmodel = Model::factory('commonmodel');
        $current_time = $commonmodel->getcompany_all_currenttimestamp($company_id);
        $unit_conversion = (UNIT == 0) ? '1.609344' : '' ;// 0 - KM, 1 - MILES
		$latitude = (float)$lat;
        $longitude = (float)$long;
		
		$match_query_1 = $match_query_2 = array();
		$match_query_1['people.login_status'] = 'S';
		$match_query_1['people.booking_limit'] = array('$gt' => $this->mongo_db->count(MDB_PASSENGERS_LOGS,array('createdate' => array('$gte' => Commonfunction::MongoDate(strtotime($current_time))),'driver_id' => 'people._id', 'travel_status' => 1, 'booking_from' => array('$ne' => 2))));
		$match_query_1['status'] = 'F';
		$match_query_1['shift_status'] = 'IN';
		
		$match_query_2['tmap.mapping_startdate'] = array('$lte'=> Commonfunction::MongoDate(strtotime($current_time)));
		$match_query_2['tmap.mapping_enddate'] = array('$gte'=>Commonfunction::MongoDate(strtotime($current_time)));
		$match_query_2['tmap.mapping_status'] = 'A';
		$match_query_2['taxi.taxi_model'] = (int)$modelId;
		# company status
		$match2['comp.companydetails.company_status'] = 'A';

		 if (UNIT == '0') {
			$geonear = array('near'=>array('type'=>'Point','coordinates'=>array($longitude,$latitude)),
				'distanceField'=>"distance",
				//'maxDistance' => $unit_conversion*1000,
				'spherical'=>true,
				'distanceMultiplier'=>0.001,
				'num'=>1000000
			);
        }else {
            //Get the result In Miles
            $geonear = array('near' => array( 'type' => "Point", 'coordinates' => array( $longitude , $latitude )),
					'distanceField' => "distance",
					//'maxDistance' => 50*1000,
					'spherical' => true,
					'distanceMultiplier' => 0.000621371192237,
					'num' => 1000000
				);
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
			array('$match'=>$match_query_1),	
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
			array('$match'=>$match_query_2),
			array('$group'=>array('_id'=>array(
					'driver_id'=>'$_id',
					'driver_name'=>'$people.name',
					'loc' => '$loc',
					'status' => '$status',
					'distance_km' => '$distance',
					'taxi_id' => '$taxi._id',
					'taxi_speed' => '$taxi.taxi_speed',
					'cid' => '$comp._id',
					'updatetime_difference' => array('$multiply' => array(array('$subtract' => array(Commonfunction::MongoDate(strtotime($current_time)),'$update_date')),0.0001))
			))),
			array('$match' => array('_id.updatetime_difference'=>array('$lte'=> (int)LOCATIONUPDATESECONDS ))),
			array('$sort'=>array('distance_km'=>1)),
			array('$skip'=>0),
			array('$limit'=>50),
		);
		$result = $this->mongo_db->aggregate(MDB_DRIVER_INFO,$arguments);
		//echo '<pre>';print_r($result);exit;
		$results = array();
		if(!empty($result['result'])){
			foreach($result['result'] as $r){
				$datas = $r['_id'];
				$temp_arr['driver_id'] = $datas['driver_id'];
				$temp_arr['driver_name'] = $datas['driver_name'];
				$temp_arr['latitude'] = $datas['loc']['coordinates'][1];
				$temp_arr['longitude'] = $datas['loc']['coordinates'][0];
				$temp_arr['status'] = $datas['status'];
				$temp_arr['taxi_id'] = $datas['taxi_id'];
				$temp_arr['taxi_speed'] = $datas['taxi_speed'];
				$temp_arr['cid'] = $datas['cid'];
				$temp_arr['updatetime_difference'] = $datas['updatetime_difference'];				
				$results[] = $temp_arr;
			}
		}
        return $results;
    }
	
    /** function for booking form validation **/
    public function validate_bookingForm($arr)
    {
		/*return Validation::factory($arr)
           		->rule('name', 'not_empty')
           		->rule('country_code', 'not_empty')
           		->rule('mobile_number', 'not_empty')
           		->rule('email', 'not_empty')
           		->rule('pickup_location', 'not_empty');
           		->rule('pickup_time', 'not_empty');*/
				
		$validation = Validation::factory($arr)
           		->rule('name', 'not_empty')
           		->rule('country_code', 'not_empty')
           		->rule('mobile_number', 'not_empty')
           		->rule('email', 'not_empty')
           		->rule('pickup_location', 'not_empty')
           		->rule('promocode', 'min_length', array(':value', '3'))
				->rule('promocode', 'max_length', array(':value', '6'));
           if(Arr::get($arr,'bookType')==1)
			{
				$validation->rule('pickup_time', 'not_empty');
				$validation->rule('pickup_time', 'Model_Find::checklaterTime', array(':value',$arr['currentTime']));
			}
		return $validation;
	}
	
	/** function to check later pickuptime is greater than 1 hour **/
	public static function checklaterTime($pickupTime, $currentTime)
	{
		$diffSecs = strtotime($pickupTime) - strtotime($currentTime);
		$diffHrs = round($diffSecs/3600);
		if($diffHrs < 1) {
			return false;
		} else {
			return true;
		}
	}
	/** function for save booking **/
	public function saveBooking($post)
	{
		$freeDrivers = $this->getFreeDriverList($post['model'],$post['pass_latitude'],$post['pass_longitude'],$post['currentTime']);
		//echo '<pre>';print_r($post);
		$drArr = array();
		$tripId = 0;
		$passengerId = $post['passengerId'];//
		$driverCnt = count($freeDrivers);
		$pickupdrop = 0;
		$waitingtime =   $city_id = $sub_logid = $notes_driver     = $company_tax = $promo_code = '';
		$pickupTime = ($post['bookType'] == 0) ? $post['currentTime'] : $post['pickup_time'];
		$approx_distance = (isset($post['appx_distance'])) ? $post['appx_distance'] : 0;
		$promo_code = (isset($post['promocode'])) ? $post['promocode'] : '';
		$approx_fare = (isset($post['appx_amount'])) ? $post['appx_amount'] : 0;
		# created date
		$createdate = Commonfunction::createdateby_user_timezone();
		
		if($driverCnt > 0 && $post['bookType'] != 1){
			$nearestDriver = $freeDrivers[0]['driver_id'];			
			$companyId = isset($freeDrivers[0]['cid']) ? $freeDrivers[0]['cid'] : '';
			$taxiId = $freeDrivers[0]['taxi_id'];
			foreach($freeDrivers as $drivers) {
				$drArr[] = $drivers['driver_id'];
			}
			$driverStr = implode(",",$drArr);
			$commonmodel = Model::factory('commonmodel');
			$createdTime = $commonmodel->getcompany_all_currenttimestamp($companyId);			
			$bookBy =  ($post['bookType'] == 0) ?  1 : 2; //1-passenger, 2-dispatcher
			//echo $bookBy.'//'.$post['bookType'];exit;
			$tripId = Commonfunction::get_auto_id(MDB_PASSENGERS_LOGS);
			$insert_array = array(
				'_id' => (int)$tripId,
				'passengers_id' => (int)$passengerId,
				'driver_id' => (int)$nearestDriver,
				'taxi_id' => (int)$taxiId,
				'company_id' => (int)$companyId,
				'current_location' => $post['pickup_location'],
				'pickup_latitude' => $post['pickup_latitude'],
				'pickup_longitude' => $post['pickup_longitude'],
				'drop_location' => $post['drop_location'],
				'drop_latitude' => $post['drop_latitude'],
				'drop_longitude' => $post['drop_longitude'],
				'pickup_time' => Commonfunction::MongoDate(strtotime($pickupTime)),
				'bookby' => (int)$bookBy,
				'now_after' => (int)$post['bookType'],
				'no_passengers' => 1,
				'approx_distance' => $approx_distance,
				'approx_fare' => (double)$approx_fare,
				'time_to_reach_passen' => "",
				'pickupdrop' => $pickupdrop,
				'waitingtime' => $waitingtime,
				'createdate' => Commonfunction::MongoDate(strtotime($createdate)),
				'booking_from' => 1,
				'search_city' => (int)$city_id,
				'sub_logid' => (int)$sub_logid,
				'notes_driver' => $notes_driver,
				'booking_from_cid' => (int)$companyId,
				'company_tax' => (float)$company_tax,
				'bookingtype' => 1,
				'bookby' => 1,
				'promocode' => $promo_code,
				'now_after' => 0,
				'driver_reply' => '',
				'msg_status' => 'U',
				'favourite_trip' => 'N',
				'recurrent_type' => 0,
				'alert_notification' => 0,
				'rating' => 0,
				'taxi_modelid' => (int)$post['model'],
				'travel_status' => 0
			);
			//echo '<pre>';print_r($insert_array);exit;
			$result = $this->mongo_db->insertOne(MDB_PASSENGERS_LOGS,$insert_array);
			$split_id=0;
			$args  = array(array('$unwind' => '$split_details'),
				array('$project' => array('split_id' => '$split_details.split_id')),
				array('$sort' => array('split_details.split_id' => -1))
			);
			$get_id = $this->mongo_db->aggregate(MDB_CSC,$args);	
			$split_id = (!empty($get_id['result'])) ? $get_id['result'][0]['split_id'] : 0;
			$split_id +=1;					
			$insert_split = array('split_details' => array('split_id' => (int)$split_id,
								//'trip_id' => (int)$inc_id,
								'friends_p_id' => (int)$passengerId,
								'fare_percentage' => 100,
								'createdate' => Commonfunction::MongoDate(strtotime($createdate)),
								'approve_status' => 'A',
								'appx_amount' => 0,
								'transaction_id' => null,
								'sub_total' => 0,
								'tax' => 0,
								'paid_amount' => 0,
								'payment_status' => 0,
								'braintree_payment_status' => 1,
								'settlement_status' => 1,
								'notification_status' => 0));
			$this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$tripId),array('$push'=>$insert_split),array('upsert' => true));
			if($result && $post['bookType'] == 0) {		
				$history_id = Commonfunction::get_auto_id(MDB_REQUEST_HISTORY);
				$driver_insert_array = array(
					'_id' => (int)$tripId,
					'trip_id' => (int)$tripId,
					'trip_type' => 0,
					'available_drivers' => $driverStr,
					'total_drivers' => $driverStr,
					'selected_driver' => (int)$nearestDriver,
					'status' => 0,
					'rejected_timeout_drivers' => '',
					'createdate' => Commonfunction::MongoDate(strtotime($createdate)),
				);
				//echo '<pre>';print_r($driver_insert_array);exit;
				$result = $this->mongo_db->insertOne(MDB_REQUEST_HISTORY,$driver_insert_array);
			}
			if($post['bookType'] == 1){
				$this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$tripId),array('$set'=>array('bookby' => 2)),array('upsert' => false));
			}
			$responseCode = ($post['bookType'] == 0) ?  1 : 2;
		} else {
			$nearestDriver = $taxiId = $companyId = 0;
			if($post['bookType'] == 1) {
				$tripId = Commonfunction::get_auto_id(MDB_PASSENGERS_LOGS);
				$insert_array = array(
					'_id' => (int)$tripId,
					'passengers_id' => (int)$passengerId,
					'driver_id' => 0,
					'taxi_id' => 0,
					'company_id' => 0,
					'current_location' => $post['pickup_location'],
					'pickup_latitude' => $post['pickup_latitude'],
					'pickup_longitude' => $post['pickup_longitude'],
					'drop_location' => $post['drop_location'],
					'drop_latitude' => $post['drop_latitude'],
					'drop_longitude' => $post['drop_longitude'],
					'pickup_time' => Commonfunction::MongoDate(strtotime($pickupTime)),
					'bookby' => 2,
					'now_after' => (int)$post['bookType'],
					'no_passengers' => 1,
					'approx_distance' => $approx_distance,
					'approx_fare' => (double)$approx_fare,
					'time_to_reach_passen' => "",
					'pickupdrop' => $pickupdrop,
					'waitingtime' => $waitingtime,
					'createdate' => Commonfunction::MongoDate(strtotime($createdate)),
					'booking_from' => 1,
					'search_city' => (int)$city_id,
					'notes_driver' => $notes_driver,
					'booking_from_cid' => (int)$companyId,
					'company_tax' => (float)$company_tax,
					'bookingtype' => 2,
					'promocode' => $promo_code,
					'driver_reply' => '',
					'msg_status' => 'U',
					'favourite_trip' => 'N',
					'recurrent_type' => 0,
					'alert_notification' => 0,
					'taxi_modelid' => (int)$post['model'],
					'travel_status' => 0
				);
				$result = $this->mongo_db->insertOne(MDB_PASSENGERS_LOGS,$insert_array);
				
				$split_id=1;
				//$split_rs = $this->mongo_db->find(MDB_PASSENGERS_LOGS,array(),array('split_details.split_id'))->sort(array('split_details.split_id'=>-1))->limit(1);					
                                 ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                                $options=[
                                    'projection'=>[
                                        'split_details.split_id'=>1
                                    ],
                                    'sort'=>[
                                        'split_details.split_id'=>-1
                                        ],
                                    'limit'=>1
                                ];
                                $split_rs = $this->mongo_db->find(MDB_PASSENGERS_LOGS,[],$options);
				$split_rs = reset($split_rs);
				//print_r($split_rs);exit;
				if(isset($split_rs['split_details']) && count($split_rs['split_details'])>0){
					$split_rs1 = reset($split_rs);
					$split_key = $split_rs1['split_details'][0]['split_id'];
					$split_id = $split_key+1;
				}					
				$insert_split = array('split_details' => array('split_id' => (int)$split_id,
									//'trip_id' => (int)$inc_id,
									'friends_p_id' => (int)$passengerId,
									'fare_percentage' => 100,
									'createdate' => Commonfunction::MongoDate(strtotime($createdate)),
									'approve_status' => 'A',
									'appx_amount' => 0,
									'transaction_id' => null,
									'sub_total' => 0,
									'tax' => 0,
									'paid_amount' => 0,
									'payment_status' => 0,
									'braintree_payment_status' => 1,
									'settlement_status' => 1,
									'notification_status' => 0));
				$this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$tripId),array('$push'=>$insert_split),array('upsert' => true));
				
				$responseCode = 2;
			} else {
				$responseCode = 3;
			}
		}
		return array($responseCode,$driverCnt,$tripId);
	}
	/** function for signup form validation **/
	public function validate_signupForm($arr)
    {
		$validation = Validation::factory($arr)
			->rule('email', 'not_empty')
			->rule('email', 'email')
			->rule('email', 'min_length', array(':value', '8')) 
			->rule('email', 'max_length', array(':value', '50'))
			->rule('email', 'Model_Find::signupEmailExist', array(':value'))
			->rule('password', 'not_empty')
			->rule('password', 'min_length', array(':value', '6')) 
			->rule('password', 'max_length', array(':value', '24'))
			->rule('confirm_password', 'not_empty')
			->rule('confirm_password', 'min_length', array(':value', '6')) 
			->rule('confirm_password', 'max_length', array(':value', '24'))
			->rule('confirm_password', 'Model_Find::checkConfirmPassword', array(':value', $arr['password']))
			->rule('country_code', 'not_empty')
			->rule('country_code', 'min_length', array(':value', '2'))
			->rule('country_code', 'max_length', array(':value', '5'))
			->rule('mobile_number', 'not_empty')
			->rule('mobile_number', 'phone', array(':value'))
			->rule('mobile_number', 'min_length', array(':value', '7'))
			->rule('mobile_number', 'max_length', array(':value', '20'))
			->rule('mobile_number', 'Model_Find::signupPhoneExist', array(':value', $arr['country_code']));
		if(REFERRAL_SETTINGS == 1) {
			$validation->rule('referral_code', 'Model_Find::checkRefCodeExist', array(':value'));
		}
		$validation->rule('salutation', 'not_empty')
			->rule('first_name', 'not_empty')
			->rule('first_name', 'min_length', array(':value', '4')) 
			->rule('first_name', 'max_length', array(':value', '35'))
			->rule('last_name', 'not_empty')
			->rule('last_name', 'min_length', array(':value', '4')) 
			->rule('last_name', 'max_length', array(':value', '35'));
		if(DEFAULT_SKIP_CREDIT_CARD) {
			$validation->rule('credit_card_number', 'not_empty');
			$validation->rule('credit_card_number', 'Model_Find::checkValidCard', array(':value'));
			$validation->rule('month', 'not_empty');
			$validation->rule('year', 'not_empty');
			$validation->rule('year', 'Model_Find::checkMonthYear', array(':value', $arr['month']));
			$validation->rule('cvv', 'not_empty');
			$validation->rule('cvv', 'min_length', array(':value', '3'));
			$validation->rule('cvv', 'max_length', array(':value', '4'));
		}
		return $validation;
	}
	/** function to check email id exist **/
	public static function signupEmailExist($email)
	{
            $mongo_db = MangoDB::instance('default'); 
            $result = $mongo_db->count(MDB_PASSENGERS,array('email' => $email));
            return (isset($result) && $result > 0) ? false : true;
	}
	/** function to check email id exist **/
	public static function signupPhoneExist($mobileno,$mobilecode)
	{
		$mongo_db = MangoDB::instance('default'); 
		$result = $mongo_db->count(MDB_PASSENGERS,array('country_code'=>$mobilecode, 'phone' => $mobileno));
		return (isset($result) && $result > 0) ? false : true;
	}
	/** function to check password and confirm password are same **/
	public static function checkConfirmPassword($confPass, $pass)
	{
		if($confPass != $pass) {
			return false;
		} else {
			return true;
		}
	}
	/** function to check the credit card number is valid or not **/
	public static function checkValidCard($creditcard)
	{
		$siteusers = Model::factory('siteusers');
		$checkSts = $siteusers->isVAlidCreditCard($creditcard,"",true);
		if($checkSts == 0){
			return false;
		} else {
			return true;
		}
	}

	/** function for passenger signup **/
	public function passengerSignup($post,$void_status=0)
	{		
		$this->session = Session::instance();
		$auto_referral_code = Commonfunction::randomkey_generator('6');
		$siteInfo = $this->siteinfodetails();
		$referralAmount = isset($siteInfo['referral_amount']) ? $siteInfo['referral_amount'] : '';
		$currentTime = $this->currentdate_bytimezone;
		# checking for FB user
		$fb_user_id = ($this->session->get('fb_id')) ? $this->session->get('fb_id') : '';
		$fb_access_token = ($this->session->get('fb_access_token')) ? $this->session->get('fb_access_token') : '';
		$this->session->delete('fb_id');
		$this->session->delete('fb_access_token');
		
		/** Insert in passenger table **/
		$inc_id = Commonfunction::get_auto_id(MDB_PASSENGERS);
		$insert_array = array(
						'_id' => (int)$inc_id,
						'name' => $post['first_name'],
						'lastname' => $post['last_name'],
						'email' => $post['email'],
						'password' => md5($post['password']),
						//'org_password' => $post['password'],
						'country_code' => $post['country_code'],
						'phone' => $post['mobile_number'],
						'referral_code' => $auto_referral_code,
						'referral_code_amount' => (double)$referralAmount,
						'referral_code_limit' => 1,
						'activation_status' => 1,
						'user_status' => 'A',
						'created_date' => Commonfunction::MongoDate(strtotime($currentTime)),
						'updated_date' => Commonfunction::MongoDate(strtotime($currentTime)),
						'device_type' => 3,
						'login_status' => 'S',
						'login_from' => 4,
						'skip_credit_card' => 2,
						'passenger_cid' => 0,
						'wallet_amount' => 0,
						'fb_user_id' => $fb_user_id,
						'fb_access_token' => $fb_access_token
					);
		$insert = $this->mongo_db->insertOne(MDB_PASSENGERS,$insert_array);
		/** Referral code check **/
		if(empty($insert->getwriteErrors())){
			$referral_code = (!empty($post['referral_code'])) ? $post['referral_code'] : '';
			if(!empty($referral_code)) {
				$project = array('_id','referral_code_amount','referral_code_limit');
				$referral = $this->mongo_db->findOne(MDB_PASSENGERS,array('referral_code'=>$referral_code),$project);
				$ref_result = isset($referral) ? $referral : array();
				if(count($ref_result) > 0) {
					$ref_id = Commonfunction::get_auto_id(MDB_PASSENGER_REFERRAL);
					$referral_code_amount = (isset($ref_result['referral_code_amount']))?$ref_result['referral_code_amount']:0;
					$referral_code_limit = (isset($ref_result['referral_code_limit']))?$ref_result['referral_code_limit']:0;
					$pass_id = (isset($ref_result['_id']))?$ref_result['_id']:'';
					$referral_array = array(
										'_id' => (int)$ref_id,
										'passenger_id' => (int)$inc_id,
										'referral_code' => $referral_code,
										'referral_amount' => (double)$referral_code_amount,
										'referral_limit' => (int)$referral_code_limit,
										'referred_by' => (int)$pass_id,
										'referral_amount_used' => 0,
										'createdate' => Commonfunction::MongoDate(strtotime($currentTime)));
					$insert = $this->mongo_db->insertOne(MDB_PASSENGER_REFERRAL,$referral_array);
					//to update the referral amount into the wallet column in passenger table
					$update_array = array('wallet_amount'=> (double)$referral_code_amount);
					$insert = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=> (int)$inc_id),array('$set'=>$update_array),array('upsert'=>true));
				}
			}
			//save credit card details
			if(DEFAULT_SKIP_CREDIT_CARD) {
				$this->saveCreditCard($inc_id,$post['first_name'],$post['email'],$post['credit_card_number'],$post['month'],$post['year'],$post['cvv'],$post['paymentResult'],$post['preAuthorizeAmount'],$post['fcardtype'],$void_status);
				
				$update_split = array('split_fare'=> '1');
				$insert = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=> (int)$inc_id),array('$set'=>$update_split),array('upsert'=>false));
			}
			$this->session->set("passenger_name",$post['first_name']);
			$this->session->set("id",$inc_id);
			$this->session->set("passenger_email",$post['email']);
			$this->session->set("passenger_phone",$post['mobile_number']);
			$this->session->set("passenger_phone_code",$post['country_code']);
			return 1;
		} else {
			return 0;
		}
	}
	/** function to save credit card details after pre authorization **/
	public function saveCreditCard($passId,$passName,$passEmail,$cardNo,$expMnt,$expYear,$cvv,$paymentResult,$preAuthorizeAmount,$fcardtype,$void_status=0)
	{
		
			$cardNo = encrypt_decrypt('encrypt',$cardNo);
			$args = array(array('$unwind' => '$creditcard_details'),
						  array('$sort' => array('creditcard_details.passenger_cardid' => -1)),
						  array('$project' => array('card_id' => '$creditcard_details.passenger_cardid')),
						  array('$limit' => 1)
						  );
			$get_id = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
			$inc_id = (!empty($get_id['result']) && (isset($get_id['result'][0]['card_id']) && !empty($get_id['result'][0]['card_id']))) ? $get_id['result'][0]['card_id'] : 0;
			$inc_id +=1;
			$update_array = array("creditcard_details"=>array(
								'passenger_cardid' => (int)$inc_id,
								'passenger_id' => (int)$passId,
								'passenger_email' => $passEmail,
								'card_type' => 'P',
								'card_holder_name' => $passName,
								'creditcard_cvv' => $cvv,
								'creditcard_no' => $cardNo,
								'expdatemonth' => (int)$expMnt,
								'expdateyear' => (int)$expYear,
								'default_card' => 1,
								'pre_transaction_id' => $paymentResult ,
								'pre_transaction_amount' => $preAuthorizeAmount,
								'card_type_description' => $fcardtype,
								'status' => 1,
                                                                'void_status'=>(int)$void_status,
								"createdate" => Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone))));	
			$result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passId),array('$push'=>$update_array),array('upsert' => true));
			
	}
	//credit card validation for braintree
	public function creditcardPreAuthorization($passengerName,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$preauthorize_amount)
	{
            
                    $transaction_amount = $preauthorize_amount;

                    $card_info['card_number'] = $creditcard_no;
                    $card_info['expirationMonth'] = $expdatemonth;
                    $card_info['expirationYear'] = $expdateyear;
                    $card_info['cvv'] = $creditcard_cvv;
                    $shipping_info['firstName'] = $passengerName;
                                    
                    
                   

                    // Payment gateway additional parameters
                    $additional_parameters = [];
                    $payment_status = '';                    
                    $paymentresponse =[];
                    
                    // Payment gateway preauthorization transaction
                    if (class_exists('Paymentgateway')) {
                        $paymentresponse = Paymentgateway::payment_gateway_connect('preauthorization',$transaction_amount,$card_info,$shipping_info,$additional_parameters);
                        $payment_status=$paymentresponse['payment_status'];
                    } else {
                        trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
                    }
                    
                    $code=0;
                    $fresult='';
                    $fcardtype='';
                    // Payment gateway success response
                    if($payment_status==1)
                    {
			$fresult=$paymentresponse['TRANSACTIONID'];
			$fcardtype =$paymentresponse['cardType'];
			$code=1;
                        $preauthorize_amount= isset($paymentresponse['preauthorize_amount'])?$paymentresponse['preauthorize_amount']:$transaction_amount;
                    }
                    // Payment gateway failure response
                    else {
				$code = 0;
				$fresult = "";
                                $fresult=$paymentresponse['payment_response'];
                                $preauthorize_amount= isset($paymentresponse['preauthorize_amount'])?$paymentresponse['preauthorize_amount']:$transaction_amount;
				/*foreach (($result->errors->deepAll()) as $error) {
					$fresult .= $error->message;
				}*/
			}
                        
                        return array($code,$fresult,$fcardtype,$preauthorize_amount);
		
	}
	/******************** Get default payment gateway of Specific company *********************/
	public function payment_gateway_details()
	{
		$args = array(
					array('$match' => array('default_payment_gateway'=>1,'company_id'=>0)),
					array('$project' => array(
										'payment_type' => '$payment_gateway_id',
										'payment_gateway_username' => '$payment_gateway_username',
										'payment_gateway_password' => '$payment_gateway_password',
										'payment_gateway_key' => '$payment_gateway_signature',
										//'gateway_currency_format' => '$currency_code',
										'payment_method' => '$payment_method'))
				);
		$result = $this->mongo_db->aggregate(MDB_PAYMENT_GATEWAYS,$args);
		return (isset($result['result'])) ? $result['result'] : array();
	}
	//Refun the amount after the preauthorization completed
	public function voidTransactionAfterPreAuthorize($pass_id,$preTransactId)
	{
		$commonmodel = Model::factory('commonmodel');                  
                    
                    $payment_status = '';                    
                    $paymentresponse =[];
                    $code=0;
                    // Payment gateway void transaction
                    if (class_exists('Paymentgateway')) {
                        $paymentresponse = Paymentgateway::payment_gateway_connect('void',$preTransactId);
                        $payment_status=$paymentresponse['payment_status'];
                       
                    } else {
                        trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
                    }
                    
                    // Payment gateway success response
               /* if($payment_status==1){
                     $val["creditcard_details.0.void_status"] = 1;
                     $update = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$pass_id),array('$set'=>$val),array('upsert' => true));	                     
                }*/			
                return $payment_status;		
	}
	/** get common info from siteinfo table **/
	public function siteinfodetails()	
	{
            $result = $this->mongo_db->findOne(MDB_SITEINFO,array(),array('referral_amount'));
            return (isset($result)) ? $result : array();
	}
	/** Get driver job status **/
	public function get_request_status($passenger_log_id="")
	{
		$resultset = $this->mongo_db->findOne(MDB_REQUEST_HISTORY,array('_id' => (int)$passenger_log_id),array('available_drivers','rejected_timeout_drivers','total_drivers','selected_driver','status','trip_type'));
		//$resultset = Commonfunction::change_key(iterator_to_array($resultset));
		$result = array();
		if(!empty($resultset)){
				$result[] = $resultset;
		}
		return $result;
	}
	/** Get Company id for the Driver **/
	public function get_company_id($driver_id)
	{
		$result = $this->mongo_db->findOne(MDB_PEOPLE,array('_id' => (int)$driver_id ),array('company_id'));
		return (!empty($result) && isset($result['company_id']))?$result['company_id']:0;
	}
	/** Get passenger's company id **/
	public function get_passenger_company_id($id) 
	{
		$result = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id' => (int)$id ),array('passenger_cid'));
		return (!empty($result) && isset($result['passenger_cid']))?$result['passenger_cid']:0;
		
	}
	//** to check the passenger in trip or not **//
	public function check_passenger_in_trip($passengerId, $currentTime)
	{	
		$result = $this->mongo_db->count(MDB_PASSENGERS,array('pickup_time'=>array('$gte' => Commonfunction::MongoDate(strtotime($currentTime))), 'passengers_id' => (int)$passengerId, 'driver_reply' => 'A', 'travel_status' => array('$in' => array(9,2,3,5))), array('_id') );
		return (isset($result) && $result > 0) ? $result : 0;

	}
	
	/** to get current time of a location(country) from latittude and longitude **/
	public function getpickupTimezone($lat,$lng)
	{	
		try{
		  $url = 'https://maps.googleapis.com/maps/api/timezone/json?location='.trim($lat).','.trim($lng).'&timestamp=1&key='.GOOGLE_TIMEZONE_API_KEY;  
		  //echo $url;exit;    
		  $json = @file_get_contents($url);
		  $data=json_decode($json);
		  $status = ($data) ? $data->status : 0;
		  if($status=="OK")
			return ($data) ? $data->timeZoneId : TIMEZONE;
		  else
			return TIMEZONE;
		} catch (Kohana_Exception $e) {
			return TIMEZONE;
		} 
	  
	}
	
	/** Function to check given referral code exist or not **/
	public static function checkRefCodeExist($referralCode)
	{
		$mongo_db = MangoDB::instance('default');
		$result = $mongo_db->Count(MDB_PASSENGERS,array('referral_code' => $referralCode));
		return (!empty($result)) ? true : false;
	}
	
	/** function to check the expiration month and year is valid or not **/
	public static function checkMonthYear($year,$month)
	{
		if(($year < date('Y')) || ($year == date('Y') && $month < date('m')) || ($month < 1 || $month > 12)) {
			return false;
		} else {
			return true;
		}
	}
	
	//update driver request details
    public function update_driver_request_details($data)
    {
		$set_array = array();
		isset($data['status']) ? $set_array['status'] = (int)$data['status']:'';
		$result = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY,array('_id' => (int)$data['trip_id']),array('$set'=>$set_array),array('upsert'=>false));
        return 1;
    }
    
    public function check_travelstatus($log_id)
    {
		$result = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$log_id),array("travel_status","driver_reply"));
        return isset($result['travel_status'])?$result['travel_status']: -1 ;
    }
    
	public function check_tranc($log_id, $flag)
    {
        $match = array('transaction.passengers_log_id'=> (int)$log_id);
        if($flag == 1){
			$match['travel_status'] = 1;
		}
		$arguments = array(array('$lookup' => array(
					'from'=>MDB_TRANSACTION,
					'localField'=> "_id",
					'foreignField' => "passengers_log_id",
					'as'=> "transaction")),
			array('$unwind' => '$transaction'),
			array('$match' => $match),
			array('$project' => array(
					'travel_status'=>'$travel_status',
					'driver_id' =>'$driver_id',
					'id' => '$transaction._id',
					'passengers_id' => '$passengers_id'))							
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		if ($flag == 1) {
            return (!empty($result['result']) ? count($result['result']) : array());
        } else {
            return (!empty($result['result']) ? $result['result'] : array());
        }		
    }
    
	/*** Get Passenger Profile details using passenger log id ***/
    public function get_passenger_log_detail($passengerlog_id = "")
    {
		$result = $temp_arr = array();
        $arguments = array(array('$lookup'=>array(
				'from'=>MDB_PASSENGERS,
				'localField'=>"passengers_id",
				'foreignField'=>"_id",
				 'as'=>"passengers"
			)),
			array('$unwind' => array('path' => '$passengers', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup'=>array(
				'from'=>MDB_PEOPLE,
				'localField'=>"driver_id",
				'foreignField'=>"_id",
				 'as'=>"people"
			)),
			array('$unwind' => array('path' => '$people', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup'=>array(
				'from'=>MDB_COMPANY,
				'localField'=>"company_id",
				'foreignField'=>"_id",
				 'as'=>"company"
			)),
			array('$unwind' => array('path' => '$company', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup'=>array(
				'from'=>MDB_TRANSACTION,
				'localField'=>"_id",
				'foreignField'=>"passengers_log_id",
				 'as'=>"transaction"        
			)),
			array('$unwind' => array('path' => '$transaction', 'preserveNullAndEmptyArrays' => true )),
			array('$project' => array(
				'phone' => '$people.phone',
				'promocode' => '$promocode',
				'booking_key' => '$booking_key',
				'driver_id' => '$driver_id',
				 'taxi_id' => '$taxi_id',
				'wallet_amount' => '$wallet_amount',
				'taxi_modelid' => '$taxi_modelid',
				'company_id' => '$company_id',
				'current_location' => '$current_location',
				'pickup_latitude' => '$pickup_latitude',
				'pickup_longitude' => '$pickup_longitude',
				'drop_latitude' => '$drop_latitude',
				'drop_longitude' => '$drop_longitude',
				'no_passengers' => '$no_passengers',
				'approx_distance' => '$approx_distance',
				'approx_duration' => '$approx_duration',
				'is_split_trip' => '$is_split_trip',
				'approx_fare' => '$approx_fare',
				'fixedprice' => '$fixedprice',
				'time_to_reach_passen' => '$time_to_reach_passen',
				'pickup_time' => '$pickup_time',
				'actual_pickup_time' => '$actual_pickup_time',
				'drop_time'=>'$drop_time',
				'account_id'=>'$account_id',
				'accgroup_id'=>'$accgroup_id',
				'pickupdrop'=>'$pickupdrop',
				'rating'=>'$rating',
				'comments'=>'$comments',
				'travel_status'=>'$travel_status',
				'driver_reply'=>'$driver_reply',
				'msg_status' => '$msg_status',
				'createdate' => '$createdate',
				'booking_from' => '$booking_from',
				'search_city' => '$search_city',
				'sub_logid'=>'$sub_logid',
				'bookby'=>'$bookby',
				'booking_from_cid'=>'$booking_from_cid',
				'distance'=>'$distance',
				'notes_driver'=>'$notes_driver',
				'used_wallet_amount'=>'$used_wallet_amount',
				'driver_phone'=>'$people.phone',							
				"driver_name" => '$people.name',
				"driver_photo" => '$people.profile_picture',
				"driver_device_id" => '$people.device_id',
				"driver_device_token"  => '$people.device_token',
				"driver_device_type" => '$people.device_type',
				'passengers_id' => '$passengers_id',
				"passenger_discount" => '$passengers.discount',
				"passenger_device_id"=>'$passengers.device_id',
				"passenger_device_token"=>'$passengers.device_token',
				"referred_by"=>'$passengers.referred_by',
				"referrer_earned"=>'$passengers.referrer_earned',
				"passenger_device_type"=>'$passengers.device_type',
				"passenger_salutation"=>'$passengers.salutation',
				"passenger_name"=>'$passengers.name',
				"passenger_lastname"=>'$passengers.lastname',
				"passenger_email"=>'$passengers.email',
				"passenger_phone"=>'$passengers.phone',
				"cancellation_nfree"=>'$company.companyinfo.cancellation_fare',
				"company_tax"=>'$company.companyinfo.company_tax',
				'drop_location' => array('$ifNull'=>array('$drop_location',0)),
				'droplocation' => array('$ifNull'=>array('$drop_location',0)),
				'transaction_id' => array('$ifNull'=>array('$transaction._id',0)),
				'brand_type' => '$company.companyinfo.company_brand_type'
			)),
			array('$match'=>array(
				'_id'=>(int)$passengerlog_id
			)),
		);
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        //print_r($res);exit;
        if(!empty($res['result'])){
			$temp_arr = $res['result'][0];
			$temp_arr['passenger_country_code'] = isset($temp_arr['passenger_country_code'])?$temp_arr['passenger_country_code']:'';
			$temp_arr['referred_by'] = isset($temp_arr['referred_by'])?$temp_arr['referred_by']:'';
			$temp_arr['passenger_salutation'] = isset($temp_arr['passenger_salutation'])?$temp_arr['passenger_salutation']:'';
			$temp_arr['referrer_earned'] = isset($temp_arr['referrer_earned'])?$temp_arr['referrer_earned']:'';
			$temp_arr['driver_id'] = isset($temp_arr['driver_id'])?$temp_arr['driver_id']:'';
			$temp_arr['is_split_trip'] = isset($temp_arr['is_split_trip'])?$temp_arr['is_split_trip']:'';
			$temp_arr['used_wallet_amount'] = isset($temp_arr['used_wallet_amount'])?$temp_arr['used_wallet_amount']:'';
			$temp_arr['sub_logid'] = isset($temp_arr['sub_logid'])?$temp_arr['sub_logid']:'';
			$temp_arr['taxi_id'] = isset($temp_arr['taxi_id'])?$temp_arr['taxi_id']:'';
			$temp_arr['driver_device_token'] = isset($temp_arr['driver_device_token'])?$temp_arr['driver_device_token']:'';
			$temp_arr['driver_device_type'] = isset($temp_arr['driver_device_type'])?$temp_arr['driver_device_type']:'';
			$temp_arr['wallet_amount'] = isset($temp_arr['wallet_amount'])?$temp_arr['wallet_amount']:'';
			$temp_arr['fixedprice'] = isset($temp_arr['fixedprice'])?$temp_arr['fixedprice']:'';
			$temp_arr['pickupdrop'] = isset($temp_arr['pickupdrop'])?$temp_arr['pickupdrop']:'';
			$temp_arr['distance'] = isset($temp_arr['distance'])?$temp_arr['distance']:'';
			$temp_arr['used_wallet_amount'] = isset($temp_arr['used_wallet_amount'])?$temp_arr['used_wallet_amount']:'';
			$temp_arr['promocode'] = isset($temp_arr['promocode'])?$temp_arr['promocode']:'';
			$temp_arr['passenger_device_token'] = isset($temp_arr['passenger_device_token'])?$temp_arr['passenger_device_token']:'';
			$temp_arr['passenger_device_type'] = isset($temp_arr['passenger_device_type'])?$temp_arr['passenger_device_type']:'';
			$temp_arr['booking_key'] = isset($temp_arr['booking_key'])?$temp_arr['booking_key']:'';
			$temp_arr['company_tax'] = isset($temp_arr['company_tax'])?$temp_arr['company_tax']:0;
			$temp_arr['time_to_reach_passen'] = isset($temp_arr['time_to_reach_passen'])?$temp_arr['time_to_reach_passen']:'';
			$temp_arr['account_id'] = isset($temp_arr['account_id'])?$temp_arr['account_id']:'';
			$temp_arr['transaction_id'] = isset($temp_arr['transaction_id'])?$temp_arr['transaction_id']:0;
			$temp_arr['cancellation_nfree'] = isset($temp_arr['cancellation_nfree'])?$temp_arr['cancellation_nfree']:0;
			$temp_arr['pickup_time'] = isset($temp_arr['pickup_time'])?Commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['pickup_time']):"";
			$temp_arr['createdate'] = isset($temp_arr['createdate'])?Commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['createdate']):"";
			$temp_arr['actual_pickup_time'] = isset($temp_arr['actual_pickup_time'])?Commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['actual_pickup_time']):"";
			$temp_arr['drop_time'] = (isset($temp_arr['drop_time']))?Commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['drop_time']):"";
			
			$temp_arr['passengers_id'] = isset($temp_arr['passengers_id']) ? $temp_arr['passengers_id'] : '';
			$temp_arr['passenger_discount'] = isset($temp_arr['passenger_discount']) ? $temp_arr['passenger_discount'] : '';
			$temp_arr['passenger_device_id'] = isset($temp_arr['passenger_device_id']) ? $temp_arr['passenger_device_id'] : '';
			$temp_arr['passenger_device_token'] = isset($temp_arr['passenger_device_token']) ? $temp_arr['passenger_device_token'] : '';
			$temp_arr['referred_by'] = isset($temp_arr['referred_by']) ? $temp_arr['referred_by'] : '';
			$temp_arr['referrer_earned'] = isset($temp_arr['referrer_earned']) ? $temp_arr['referrer_earned'] : '';
			$temp_arr['passenger_device_type'] = isset($temp_arr['passenger_device_type']) ? $temp_arr['passenger_device_type'] : '';
			$temp_arr['passenger_salutation'] = isset($temp_arr['passenger_salutation']) ? $temp_arr['passenger_salutation'] : '';
			$temp_arr['passenger_name'] = isset($temp_arr['passenger_name']) ? $temp_arr['passenger_name'] : '';
			$temp_arr['passenger_lastname'] = isset($temp_arr['passenger_lastname']) ? $temp_arr['passenger_lastname'] : '';
			$temp_arr['passenger_email'] = isset($temp_arr['passenger_email']) ? $temp_arr['passenger_email'] : '';
			$temp_arr['passenger_phone'] = isset($temp_arr['passenger_phone']) ? $temp_arr['passenger_phone'] : '';		
			$temp_arr['brand_type'] = isset($temp_arr['brand_type'])?$temp_arr['brand_type']:0;
			$result[] = (object)$temp_arr;
		}
        return $result;
    }
    
    public function update_driver_status($status, $driverid)
    {
        $update_array = array("status" => $status);		
		$result = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id'=>(int)$driverid),array('$set'=>$update_array),array('upsert'=>false));
		return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    
    public function get_passenger_phone_by_id($id)
    {         
		$result = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$id),array('phone'=>'phone'));
        return (isset($result)? $result['phone'] : "");
    }
    
    public function cancel_triptransact_details($details, $cancellation_nfree, $payment_types,$driver_id = 0)
    {
		$check_package_type='';
		if($cancellation_nfree != 0 && !empty($driver_id))
		{
			
			$admin_amt = ($details['total_fare'] * $details[0]['admin_commission'])/100; //payable to admin
			$admin_amt = round($admin_amt, 2);
			$total_balance = round($details['total_fare'],2);
			//Set Commission to Admin
			if(ADMIN_COMMISION_SETTING) {					
				$updateresult = $this->mongo_db->updateOne(MDB_PEOPLE,array('user_type'=>'A'),
					array('$inc' => array('account_balance' => (double)$admin_amt )),
					array('upsert'=>false)
				);
			}
		
			$company_amt = $details['total_fare'];
			$company_amt = round($company_amt, 2);
			//Set Commission to Company
			if(COMPANY_COMMISION_SETTING) {
				$updateresult = $this->mongo_db->updateOne(MDB_PEOPLE,array('user_type'=>'C', 'company_id' => (int)$details['company_id']),array('$inc' => array('account_balance' => (double)$company_amt )),array('upsert'=>false));
			}
			//Set Commission to Driver
			if(DRIVER_COMMISION_SETTING && $driver_id > 0){
				$driver_com_result = $this->mongo_db->findOne(MDB_COMPANY, array('_id'=>(int)$details['company_id']), array('companydetails.driver_commission'));
				$driver_commission = isset($driver_com_result['companydetails']['driver_commission']) ? $driver_com_result['companydetails']['driver_commission'] : 0;
				$driver_commission_amt = round(($company_amt*$driver_commission/100),2);
				$company_bal_amt = $company_amt-$driver_commission_amt;
				$updateresult = $this->mongo_db->updateOne(MDB_PEOPLE,array('user_type'=>'D', '_id' => (int)$driver_id),array('$inc' => array('account_balance' => (double)$driver_commission_amt)),array('upsert'=>false));
			}
			if($details['travel_status'] == 4)
			{
				$update_arr = array('comments' => $details['remarks'], 'travel_status' => (int)$details['travel_status'],'actual_pickup_time' => Commonfunction::MongoDate(strtotime($this->currentdate)));
				$updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id' => (int)$details['passenger_log_id']),array('$set' => $update_arr),array('upsert'=>false));
			}
			$current_time = date('Y-m-d H:i:s');
			$details['CORRELATIONID']=isset($details['CORRELATIONID'])?$details['CORRELATIONID']:'';
			$details['ACK']=isset($details['ACK'])?$details['ACK']:'1';
			$details['CURRENCYCODE']=isset($details['CURRENCYCODE'])?$details['CURRENCYCODE']:'';
			$inc_id = $this->get_insert_id(MDB_TRANSACTION);
			$insert_arr = array(
				'_id' => (int)$inc_id,
				'passengers_log_id' => (int)$details['passenger_log_id'],
				'fare' => (double)$details['total_fare'],
				'remarks' => (double)$details['remarks'],
				'correlation_id' => (double)$details['CORRELATIONID'],
				'ack' => $details['ACK'],
				'transaction_id' => $details['TRANSACTIONID'],
				'payment_type' => (int)$details['pay_mod_id'],
				'order_time' => Commonfunction::MongoDate(strtotime($current_time)),
				'amt' => (double)$details['total_fare'],
				'currency_code' => $details['CURRENCYCODE'],
				'payment_status' => $details['ACK'],
				'captured' => 1,
				'admin_amount' => (double)$admin_amt,
				'company_amount' => (double)$company_amt,
				'trans_packtype' => $check_package_type,
				'payment_gateway_id' => (int)$payment_types,
				'current_date' => Commonfunction::MongoDate(strtotime($this->currentdate))
			);
			$result = $this->mongo_db->insertOne(MDB_TRANSACTION, $insert_arr);
			return $inc_id;
		}
		else
		{	
			$update_arr = array('comments' => $details['remarks'], 'travel_status' => 4, 'actual_pickup_time' => Commonfunction::MongoDate(strtotime($this->currentdate)));		
			$updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id' => (int)$details['passenger_log_id']),array('$set' => $update_arr),array('upsert'=>false));
			return 1;
		}
    }
    
    public function get_insert_id($collection = ""){
		//$rs = $this->mongo_db->find($collection,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
                ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                                $options=[
                                    'projection'=>[
                                        '_id'=>1
                                    ],
                                    'sort'=>[
                                        '_id'=>-1
                                        ],
                                    'limit'=>1
                                ];
                                $rs = $this->mongo_db->find($collection,[],$options);
		$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
		reset($res);
		$first_key = key($res);
		$inc_id = $first_key+1;
		return $inc_id;
	}
	
	/*** Get Passenger Profile details using passenger log id ***/
    public function get_passenger_cancel_faredetail($passengerlog_id = "")
    {
		$find = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$passengerlog_id),array('search_city'));
		$find_result = (!empty($find)?$find:array());
		$city_model_fare=0;
        if (count($find_result) > 0) {
            $city_id           = $find_result['search_city'];
			$args = array(array('$unwind'=>'$stateinfo'),
						  array('$unwind'=>'$stateinfo.cityinfo'),
						  array('$match'=>array('stateinfo.cityinfo.city_id'=>(int)$city_id)),
						  array('$project'=>array('city_model_fare' => '$stateinfo.cityinfo.city_model_fare'))
						);
			$city1 = $this->mongo_db->aggregate(MDB_CSC,$args);
			$city1_result = (isset($city1['result'])?$city1['result']:'');
            if (count($city1_result) > 0) {
                $city_model_fare = $city1_result[0]['city_model_fare'];
            } 
        } else {
			//default
            $city_id = $find_result['search_city'];
			$args = array(array('$unwind'=>'$stateinfo'),
						  array('$unwind'=>'$stateinfo.cityinfo'),
						  array('$match'=>
								array('default'=> 1,
									  'stateinfo.default'=> 1,
									  'stateinfo.cityinfo.default'=> 1)),
						  array('$project'=>array('city_model_fare' => '$stateinfo.cityinfo.city_model_fare'))
						);
			$city1 = $this->mongo_db->aggregate(MDB_CSC,$args);
			$city1_result = (isset($city1['result'])?$city1['result']:'');
            if (count($city1_result) > 0) {
                $city_model_fare = $city1_result['city_model_fare'];
            }
        }
		
		$args = array(
			array('$lookup'=>array('from' => MDB_TAXI,
				  'localField' => 'taxi_id',
				  'foreignField' => '_id',
				  'as' => 'taxi')),
			array('$unwind' => '$taxi'),
			array('$lookup'=>array('from' => MDB_MOTOR_MODEL,
				  'localField' => 'taxi.taxi_model',
				  'foreignField' => '_id',
				  'as' => 'mm')),
			array('$unwind' => '$mm'),
			array('$match' => array('_id' => (int)$passengerlog_id)),
			array('$project'=>
				array('Passenger_log_id' => '$_id','cancellation_fare' =>
				array('$add'=>array('$mm.cancellation_fare',array('$multiply'=>
				array('$mm.cancellation_fare',array('$divide' => array((int)$city_model_fare,100))))))	
			))					  
		);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		return (isset($result[0]['cancellation_fare'])?$result[0]['cancellation_fare']:0);
    }
     //to check the passenger have referral amount to use
    public function check_passenger_referral_amount($passenger_id)
    {       
        //$res = $this->mongo_db->find(MDB_PASSENGER_REFERRAL,array('passenger_id'=>(int)$passenger_id,'referral_amount_used'=>0),array("referral_amount","referral_code"));
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'referral_amount'=>1,
                'referral_code'=>1
            ]
        ];
        $res = $this->mongo_db->find(MDB_PASSENGER_REFERRAL,['passenger_id'=>(int)$passenger_id,'referral_amount_used'=>0],$options);
        $res = $res;
        $res = Commonfunction::change_key($res);
        $result = array_map(
					function($res) {
							return array(
								'referral_amount' => $res['referral_amount'],
								'referral_code' => $res['referral_code']
							);
					}, $res);
        return $result;
    }
    
    public function siteinfo_details()
    {        
        $result = array();
        $res = $this->mongo_db->findOne(MDB_SITEINFO,array(),array("admin_commission","referral_discount","currency_format","referral_amount","referral_settings","wallet_amount1","wallet_amount2","wallet_amount3","wallet_amount_range"));
        if(!empty($res)){
			$result[] = $res;
		}
        return $result;
    }
    
    public function insert_transactioncoll($datas){
               
			$insert_id = $this->get_insert_id(MDB_TRANSACTION);                
			$insert_array = array(
								   '_id' => (int)$insert_id,
								   "passengers_log_id" => (int)$datas['passenger_log_id'],
								   "remarks"           => $datas['remarks'],
								   "payment_type"      => $datas['payment_type'],
								   "amt"               => (double)$datas['amt'],
								   "fare"              => (double)$datas['fare'],
								   "admin_amount"      => (double)$datas['admin_amount'],
								   "company_amount" => (double)$datas['company_amount'],
								   "trans_packtype" => $datas['trans_packtype'],
								   'current_date' => Commonfunction::MongoDate(strtotime($this->currentdate))
						   );
		   
		   $res = $this->mongo_db->insertOne(MDB_TRANSACTION, $insert_array);
		   return $insert_id;
   }
   
   public function update_passengerlogs($datas, $id){
		
		$update_arr = array();
		isset($datas['notification_status']) ? $update_arr["notification_status"] = (int)$datas['notification_status'] : '';
		isset($datas['drop_latitude']) ? $update_arr['drop_latitude'] = (double)$datas['drop_latitude'] : '';
		isset($datas['drop_longitude']) ? $update_arr['drop_longitude'] = (double)$datas['drop_longitude'] : '';
		isset($datas['drop_location']) ? $update_arr['drop_location'] = $datas['drop_location'] : '';
		isset($datas['pickup_latitude']) ? $update_arr['pickup_latitude'] = (double)$datas['pickup_latitude'] : '';
		isset($datas['pickup_longitude']) ? $update_arr['pickup_longitude'] = (double)$datas['pickup_longitude'] : '';
		isset($datas['current_location']) ? $update_arr['current_location'] = $datas['current_location'] : '';
		isset($datas['travel_status']) ? $update_arr['travel_status'] = (int)$datas['travel_status'] : '';
		isset($datas['actual_pickup_time']) ? $update_arr['actual_pickup_time'] = Commonfunction::MongoDate(strtotime($datas['actual_pickup_time'])) : '';
		
		isset($datas['split_transaction_status']) ? $update_arr['split_transaction_status'] = $datas['split_transaction_status'] : '';
		isset($datas['void_status']) ? $update_arr['void_status'] = $datas['void_status'] : '';
		
		if(!empty($update_arr)){
			$res = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS, array('_id' => (int)$id), array('$set' => $update_arr), array('upsert' => false));
		}
		return 1;		
	}
	
	public function update_passengers($update_array, $update_id, $default_companyid)
    {
		$match = array();
		$match['_id'] = (int)$update_id; 
        if ($default_companyid != '') {
			$match['passenger_cid'] = (int)$default_companyid;
        } 
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$update_array),array('upsert'=>false));
        return 1;
    }
    
    // Get Passenger credit card details
    public function get_creadit_card_details($passenger_id = "", $card_type = "", $default = "")
    {
        $match = array();
		$match['_id'] = (int)$passenger_id;
		if (($card_type == "P") || ($card_type == "B")) {
			$match['creditcard_details.card_type'] = $card_type;
        }
        if ($default == 'yes') {
			$match['creditcard_details.default_card'] = 1;
        }
		$args = array(
			//array('$unwind' => '$creditcard_details'),
			array('$unwind' =>  array( 'path' =>  '$creditcard_details', 'preserveNullAndEmptyArrays' =>  true )),
			array('$match' => $match),
			array('$project' =>
				array(
					'_id'=>0,
					'passenger_cardid' => '$creditcard_details.passenger_cardid',
					'passenger_id' => '$creditcard_details.passenger_id',
					'card_type' => '$creditcard_details.card_type',
					'expdatemonth' => '$creditcard_details.expdatemonth',
					'default_card' => '$creditcard_details.default_card',
					'expdateyear' => '$creditcard_details.expdateyear',
					'creditcard_no' => '$creditcard_details.creditcard_no',
					'creditcard_cvv' => '$creditcard_details.creditcard_cvv'
				)
			)
		);
		
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
		//echo '<pre>';print_r($result);exit;
		$card_details = !empty($result['result'][0]) ? $result['result'] : array();
		return $card_details;
    }
    
    public function passengerlogid_details($log_id)
    {
		$result = $temp_arr = array();
		$args = array(
			array('$lookup' => array(
				'from' => MDB_PASSENGERS,
				'localField' => 'passengers_id',
				'foreignField' => '_id',
				'as' => 'pass',
			)),
			array('$unwind' => '$pass'),
			array('$lookup' => array(
				'from' => MDB_PEOPLE,
				'localField' => 'driver_id',
				'foreignField' => '_id',
				'as' => 'people',
			)),
			array('$unwind' => '$people'),
			array('$lookup' => array(
				'from' => MDB_LOCATION_HISTORY,
				'localField' => '_id',
				'foreignField' => 'trip_id',
				'as' => 'driver_location',
			)),
			//array('$unwind' => '$driver_location'),
			array('$unwind' =>  array( 'path' =>  '$driver_location', 'preserveNullAndEmptyArrays' =>  true)),
			array('$match' => array('_id' => (int)$log_id)),
			array('$project' =>
				array('_id' => 0,
					'passengers_id'=>'$passengers_id',
					'driver_id'=>'$driver_id',
					'company_id'=>'$company_id',
					'passenger_name'=>'$pass.name',
					'passenger_lastname'=>'$pass.lastname',
					'passenger_email'=>'$pass.email',
					'passenger_phone'=>'$pass.phone',
					'name'=>'$people.name',
					'email'=>'$people.email',
					'phone'=>'$people.phone',
					'driver_name'=>'$people.name',
					'driver_email'=>'$people.email',
					'driver_phone'=>'$people.phone',
					'passenger_devicetoken'=>'$pass.device_token',
					'driver_devicetoken'=>'$people.device_token',
					'search_city'=>'$search_city',
					'taxi_id'=>'$taxi_id',
					'pickup_time'=>'$pickup_time',
					'pickupLocation' => '$current_location',
					'dropLocation' => '$drop_location',
					'pickup_latitude' => '$pickup_latitude',
					'pickup_longitude' => '$pickup_longitude',
					'drop_latitude' => '$drop_latitude',
					'drop_longitude' => '$drop_longitude',
					'pre_transaction_id' => '$pre_transaction_id',
					'pre_transaction_amount' => '$pre_transaction_amount',
					'used_wallet_amount' => '$used_wallet_amount',				
					'active_record' => '$driver_location.loc.coordinates',
					'taxi_modelid' => '$taxi_modelid'
				)
			)
		);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			$temp_arr = $res['result'][0];
			$temp_arr['pickup_time'] = commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['pickup_time']);
			$active_record = isset($temp_arr['active_record'])?$temp_arr['active_record']:array();
			$coordinates='';
			if(!empty($active_record)){
				foreach($active_record as $a){
					$lat = '['.$a[1].',';
					$long = $a[0].'],';
					$coordinates .= $lat.$long;
				}
				($coordinates !='') ? $temp_arr['active_record'] = $coordinates : '';
			}
			$result[] = $temp_arr;
		}
		return $result;
    }
    
    /*** Get Taxi fare per KM & Waiting charge of the company based Company***/
    public function get_model_fare_details($company_id, $model_id = "", $search_city = "")
    {
		$res = array();
        if ($search_city != '') {
			$city_arg = array(array('$unwind'=>'$stateinfo'),
						array('$unwind'=>'$stateinfo.cityinfo'),
						array('$match'=>array(
							'stateinfo.cityinfo.city_id' => (int)$search_city
						)),
						array('$project'=>array(
							'city_model_fare' => '$stateinfo.cityinfo.city_model_fare',
						))
					);
        }else{
			$city_arg = array(array('$unwind'=>'$stateinfo'),
						array('$unwind'=>'$stateinfo.cityinfo'),
						array('$match'=>array(
							'stateinfo.cityinfo.default' => 1
						)),
						array('$project'=>array(
							'city_model_fare' => '$stateinfo.cityinfo.city_model_fare',
						))
					);
        }  
        $city_model_fare=1;
        $model_base_query = $this->mongo_db->aggregate(MDB_CSC,$city_arg);
        $result_fare = (!empty($model_base_query['result'])?$model_base_query['result']:array());        
        $city_model_fare = (!empty($result_fare[0]['city_model_fare']) ? $result_fare[0]['city_model_fare'] : 1);
        if (FARE_SETTINGS == 2) {
			$arguments = array(array('$unwind'=>'$model_fare'),
				array('$project' => array(
					"model_id"=>'$model_fare.model_id',
					"base_fare"=> array('$add' => array('$model_fare.base_fare',array('$multiply'=>array('$model_fare.base_fare',array('$divide'=>array($city_model_fare,100)))))),						
					"min_fare"=> array('$add' => array('$model_fare.min_fare',array('$multiply'=>array('$model_fare.min_fare',array('$divide'=>array($city_model_fare,100)))))),						
					"cancellation_fare"=> array('$add' => array('$model_fare.cancellation_fare',array('$multiply'=>array('$model_fare.cancellation_fare',array('$divide'=>array($city_model_fare,100)))))),						
					"below_km"=> array('$add' => array('$model_fare.below_km',array('$multiply'=>array('$model_fare.below_km',array('$divide'=>array($city_model_fare,100)))))),						
					"above_km"=> array('$add' => array('$model_fare.above_km',array('$multiply'=>array('$model_fare.above_km',array('$divide'=>array($city_model_fare,100)))))),						
					"minutes_fare"=> array('$add' => array('$model_fare.minutes_fare',array('$multiply'=>array('$model_fare.minutes_fare',array('$divide'=>array($city_model_fare,100)))))),						
					"night_charge"=> '$model_fare.night_charge',
					"night_timing_from" => '$model_fare.night_timing_from',
					"night_timing_to" => '$model_fare.night_timing_to',						
					"night_fare"=> array('$add' => array('$model_fare.night_fare',array('$multiply'=>array('$model_fare.night_fare',array('$divide'=>array($city_model_fare,100)))))),
					"evening_charge" => '$model_fare.evening_charge',
					"evening_timing_from" => '$model_fare.evening_timing_from',
					"evening_timing_to" => '$model_fare.evening_timing_to',						
					"evening_fare"=> array('$add' => array('$model_fare.evening_fare',array('$multiply'=>array('$model_fare.evening_fare',array('$divide'=>array($city_model_fare,100)))))),
					"waiting_time" => '$model_fare.waiting_time',
					"min_km" => '$model_fare.min_km',
					"below_above_km" => '$model_fare.below_above_km',
				)),
				array('$match' => array(
					"_id"=> (int)$company_id,
					"model_id" => (int)$model_id
				))					
			);
			$result = $this->mongo_db->aggregate(MDB_COMPANY,$arguments);
		}else{
			$arguments = array(
				array('$project' => array(
					"base_fare"=> array('$add' => array('$base_fare',array('$multiply'=>array('$base_fare',array('$divide'=>array($city_model_fare,100)))))),
					"min_fare"=> array('$add' => array('$min_fare',array('$multiply'=>array('$min_fare',array('$divide'=>array($city_model_fare,100)))))),
					"cancellation_fare"=> array('$add' => array('$cancellation_fare',array('$multiply'=>array('$cancellation_fare',array('$divide'=>array(2,100)))))),						
					"below_km"=> array('$add' => array('$below_km',array('$multiply'=>array('$below_km',array('$divide'=>array($city_model_fare,100)))))),
					"above_km"=> array('$add' => array('$above_km',array('$multiply'=>array('$above_km',array('$divide'=>array($city_model_fare,100)))))),
					"minutes_fare"=> array('$add' => array('$minutes_fare',array('$multiply'=>array('$minutes_fare',array('$divide'=>array($city_model_fare,100)))))),						
					"night_charge"=> '$night_charge',
					"night_timing_from" => '$night_timing_from',
					"night_timing_to" => '$night_timing_to',						
					"night_fare"=> array('$add' => array('$night_fare',array('$multiply'=>array('$night_fare',array('$divide'=>array($city_model_fare,100)))))),
					"evening_charge" => '$evening_charge',
					"evening_timing_from" => '$evening_timing_from',
					"evening_timing_to" => '$evening_timing_to',						
					"evening_fare"=> array('$add' => array('$evening_fare',array('$multiply'=>array('$evening_fare',array('$divide'=>array($city_model_fare,100)))))),
					"waiting_time" => '$waiting_time',
					"min_km" => '$min_km',
					"below_above_km" => '$below_above_km',
				)),
				array('$match' => array(
					"_id"=> (int)$model_id
				))					
			);
			$result = $this->mongo_db->aggregate(MDB_MOTOR_MODEL,$arguments);
		}
		
		if(!empty($result['result'])){
			
			$res = $result['result'];
			$res[0]['cancellation_fare'] = isset($res[0]['cancellation_fare']) ? number_format($res[0]['cancellation_fare'], 2, '.', ' '): 0;
		}
		//print_r($res);exit;
        return $res;
    }
    
    public function checkpromocode($promo_code = "", $passenger_id = "", $current_time = "")
    {
		$match = array('promocode' => $promo_code,'passenger_id' => (int)$passenger_id);
		$project = array('promocode','promo_discount','promo_used','start_date','expire_date','promo_limit');		
		$promo = $this->mongo_db->findOne(MDB_PASSENGERS_PROMO,$match,$project);
		$promo_fetch = (isset($promo)?$promo:array());
        if (count($promo_fetch) > 0) {
            $promocode      = (isset($promo_fetch['promocode'])?$promo_fetch['promocode']:'');
            $promo_discount = (isset($promo_fetch['promo_discount'])?$promo_fetch['promo_discount']:'');
            $promo_used     = (isset($promo_fetch['promo_used'])?$promo_fetch['promo_used']:'');
            $promo_start_date    = (isset($promo_fetch['start_date'])?$promo_fetch['start_date']:'');
            $promo_expire_date   = (isset($promo_fetch['expire_date'])?$promo_fetch['expire_date']:'');
            $promo_limit    = (isset($promo_fetch['promo_limit'])?$promo_fetch['promo_limit']:'');
			$promo_start = Commonfunction::convertphpdate('Y-m-d H:i:s',$promo_start_date);
			$promo_expire = Commonfunction::convertphpdate('Y-m-d H:i:s',$promo_expire_date);
			
            if (strtotime($promo_start) > strtotime($current_time)) {
                return 3;
            } else if (strtotime($promo_expire) < strtotime($current_time)) {
                return 4;
            } else {
				$promo_trip_count=$promo_user_count=$promo_wallet_count=0;
				#trip count
				$match = array('promocode'=>$promo_code,
							   'passengers_id'=>(int)$passenger_id,
							   'travel_status'=> 1,
							   'driver_reply' => 'A'
							   );
				$promo_trip_count = $this->mongo_db->count(MDB_PASSENGERS_LOGS,$match);
				
				#wallet count
				$match1 = array('promocode'=>$promo_code,'passenger_id'=>(int)$passenger_id);
				$promo_wallet_count = $this->mongo_db->count(MDB_PASSENGER_WALLET_LOG,$match1);
				$promo_user_count = $promo_trip_count + $promo_wallet_count;
				
				if ($promo_user_count > 0 && $promo_user_count >= $promo_limit) {
					return 2;
				} else {
					return 1;
				}
            }
        } else {
            return 0;
        }
    }
    
}

