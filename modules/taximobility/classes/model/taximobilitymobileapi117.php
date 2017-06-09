<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Mobileapi Model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_Taximobilitymobileapi117 extends Model
{
    public function __construct()
    {
        /*$this->session = Session::instance();	
        $this->name = $this->session->get("name");
        $this->admin_userid = $this->session->get("passenger_id");
        $this->admin_email = $this->session->get("email");
        $this->user_admin_type = $this->session->get("user_type");*/
        $this->currentdate = Commonfunction::getCurrentTimeStamp();
		# created date
		$this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
		//MongoDB Instance
		$this->mongo_db = MangoDB::instance('default');
		
    }
    public function search($search)
    {
        echo "API Model";
    }
    //Get Common config
    public function select_site_settings($company_id)
    {
        if ($company_id != '') {
			$arguments = array(array('$unwind'=>'$companyinfo'),
						array('$match'=>array('_id'=>(int)$company_id)),
						array('$project' => array(		
						    '_id' =>0,											
							'site_currency' => '$companyinfo.company_currency',
							'facebook_key' => '$companyinfo.company_facebook_key',
							'facebook_secretkey' => '$companyinfo.company_facebook_secretkey',
							'facebook_share' => '$companyinfo.company_facebook_share',
							'twitter_share' => '$companyinfo.company_twitter_share',
							'cancellation_fare_setting' => '$companyinfo.cancellation_fare',
							'site_logo' => '$companyinfo.company_logo',
							'google_business_key' => '$companyinfo.google_business_key',			
						))					
					);
			$result = $this->mongo_db->aggregate(MDB_COMPANY,$arguments);
        } else {
			
			$projection_field = array(		
							'_id' =>0,											
							'app_name' => '$app_name',
							'site_country' => '$site_country',
							'site_currency' => '$site_currency',
							'facebook_key' => '$facebook_key',
							'facebook_secretkey' => '$facebook_secretkey',
							'facebook_share' => '$facebook_share',
							'twitter_share' => '$twitter_share',
							'google_business_key' => '$google_business_key',
							'ios_google_map_key' => '$ios_google_map_key',
							'ios_google_geo_key' => '$ios_google_geo_key',
							'android_google_api_key' => '$android_google_key',	
							'cancellation_fare_setting'	=> '$cancellation_fare_setting',
							'ios_driver_language' => '$ios_driver_language_status',
							'ios_passenger_language' => '$ios_passenger_language_status',
							'ios_driver_colorcode' => '$ios_driver_colorcode_status',
							'ios_passenger_colorcode' => '$ios_passenger_colorcode_status',
							'android_driver_language' => '$android_driver_language_status',
							'android_passenger_language' => '$android_passenger_language_status',
							'android_passenger_colorcode' => '$android_passenger_colorcode_status',
							'android_driver_colorcode' => '$android_driver_colorcode_status',
							'android_foursquare_api_key' => '$android_foursquare_api_key',
							'ios_foursquare_api_key' => '$ios_foursquare_api_key',
							'android_foursquare_status' => '$android_foursquare_status',
							'ios_foursquare_status' => '$ios_foursquare_status',
							'itune_passenger' => '$itune_passenger',
							'itune_driver' => '$itune_driver',
							'fb_profile' => '$fb_profile',
							'site_logo' => '$themesettings.site_logo'
						);
			$args = array(
					array('$lookup' => 
							array('from' => MDB_THEME_SETTINGS,
								'localField' => '_id',
								'foreignField' => '_id',
								'as' => 'themesettings'
							)
						),
                array('$unwind' =>  array( 'path' =>  '$themesettings', 'preserveNullAndEmptyArrays' =>  true ) ),
                array('$match' => array('_id' =>1)),
                array('$project' => $projection_field),
                array('$limit' => 1)
            );
			$result = $this->mongo_db->aggregate(MDB_SITEINFO,$args);
        }
        //~ print_r($result);exit;
        return (!empty($result['result'])) ? $result['result']: array();
    }
    //Passenger Login
    public function passenger_login($phone, $pwd, $devicetoken = "", $deviceid = "", $devicetype = "", $company_id = "", $country_code = "")
    {
		$project = array('_id','name','email','profile_image','phone','country_code','address','referral_code','referral_code_amount','fb_user_id','split_fare','fb_access_token','user_status','login_from','device_id','device_token','login_status','skip_credit_card','forgot_password');
		$match = $result = array();
		$match['phone'] = $phone;
		$match['country_code'] = $country_code;
		$match['password'] = $pwd;
		if ($company_id != '' && $company_id != 0){
			$match['passenger_cid'] = (int)$company_id;
        }
		$res = $this->mongo_db->findOne(MDB_PASSENGERS,$match,$project);
		//print_r($res);exit;
		 if(!empty($res)){
			
			$temp_arr['id'] = isset($res['_id'])?$res['_id']:'';
			$temp_arr['email'] = isset($res['email'])?$res['email']:'';
			$temp_arr['profile_image'] = isset($res['profile_image'])?$res['profile_image']:'';
			$temp_arr['salutation'] = isset($res['salutation'])?$res['salutation']:'';
			$temp_arr['name'] = isset($res['name'])?$res['name']:'';
			$temp_arr['lastname'] = isset($res['lastname'])?$res['lastname']:'';
			$temp_arr['country_code'] = isset($res['country_code'])?$res['country_code']:'';
			$temp_arr['phone'] = isset($res['phone'])?$res['phone']:'';
			$temp_arr['org_password'] = isset($res['org_password'])?$res['org_password']:'';
			$temp_arr['address'] = isset($res['address'])?$res['address']:'';
			$temp_arr['referral_code'] = isset($res['referral_code'])?$res['referral_code']:'';
			$temp_arr['referral_code_amount'] = isset($res['referral_code_amount'])?$res['referral_code_amount']:'';
			$temp_arr['split_fare'] = isset($res['split_fare'])?$res['split_fare']:'';
			$temp_arr['otp'] = isset($res['otp'])?$res['otp']:'';
			$temp_arr['fb_user_id'] = isset($res['fb_user_id'])?$res['fb_user_id']:'';
			$temp_arr['fb_access_token'] = isset($res['fb_access_token'])?$res['fb_access_token']:'';
			$temp_arr['user_status'] = isset($res['user_status'])?$res['user_status']:'';
			$temp_arr['login_from'] = isset($res['login_from'])?$res['login_from']:'';
			$temp_arr['login_status'] = isset($res['login_status'])?$res['login_status']:'';
			$temp_arr['device_id'] = isset($res['device_id'])?$res['device_id']:'';
			$temp_arr['device_token'] = isset($res['device_token'])?$res['device_token']:'';
			$temp_arr['skip_credit_card'] = isset($res['skip_credit_card'])?$res['skip_credit_card']:'';
			$temp_arr['forgot_password'] = isset($res['forgot_password'])?$res['forgot_password']:'';
			$result[] = $temp_arr;
		}
		
        if (count($result) > 0) {
            if ($deviceid != "") {
                $update_array = array(
                    "device_token" => $devicetoken,
                    "device_id" => $deviceid,
                    "device_type" => (int)$devicetype,
                    "login_status" => "S"
                );
				$match['phone'] = $phone;
				$update_device_token_result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$update_array),array('upsert'=>false));
            }
            return (!empty($update_device_token_result->getwriteErrors()) ? array() : $result);
        } else {
            return array();
        }
    }
    // Check Whether Passenger Email is Already Exist or Not
    public function check_email_passengers($email = "", $company_id = "")
    {
		$match_query=array();
		$match_query['email'] = $email;
        if ($company_id != '' & $company_id!=0) {
           $match_query['passenger_cid'] = (int)$company_id;
        }
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match_query);
		
        return ($result>0) ? $result : 0 ;
    }
    // Check Whether Passenger phone is Already Exist or Not
    public function check_phone_passengers($phone = "", $company_id = "", $country_code = "")
    {
		
		$match_query=array();
		$match_query['phone'] = $phone;
		if($country_code!=""){
			$match_query['country_code'] = $country_code;
		}
        if ($company_id != '' & $company_id!=0) {
			$match_query['passenger_cid'] = (int)$company_id;
        }
        
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match_query);
		return ($result>0) ? $result : 0 ;
    }
    // Check Whether Passenger phone is Already Exist or Not	
	public function check_phone_bypassengers($phone = "", $email = '', $company_id = '', $country_code = '')
    {
		$match_query=array();
		$match_query['phone'] = $phone;
		$match_query['country_code'] = $country_code;
		if($company_id !=''){
			$match_query['passenger_cid'] = (int)$company_id;
		}
        $result = $this->mongo_db->count(MDB_PASSENGERS,$match_query,array('phone'));
        if ($result > 0) {
            return 1;
        } else {
            return 0;
        }
    }
	
    // Check Whether People phone is Already Exist or Not
    // Check with all placed before going to edit this function
    public function check_phone_people($phone = "", $user_type = "", $company_id)
    {
		$match_query['phone'] = trim($phone);
		$match_query['user_type'] = $user_type;
		if($company_id != '' && $company_id != 0) {
			$match_query['company_id'] = (int)$company_id;
		}
		
		$result = $this->mongo_db->count(MDB_PEOPLE,$match_query);
		//echo $result;exit;
		return $result;
    }
    /** REGISTER FACEBOOK USERS **/
    /** REGISTER FACEBOOK USERS **/
    public function register_facebook_user($accessToken = "", $uid = "", $otp = "", $referral_code = "", $fname = "", $lname = "", $email = "", $image_name = "", $devicetoken = "", $device_id = "", $devicetype = "", $company_id = "")
    {        
		if($email == '') {
			$checkmail = $this->mongo_db->findOne(MDB_PASSENGERS,array('fb_user_id' => $uid),array('email'));
			$email = (!empty($checkmail) && isset($checkmail['email'])) ? $checkmail['email'] : '';
			if($email == ''){
				return 10;
			}
		}
        /** Referral key generator **/
		$match_query = array();
		$match_query['email'] = $email;
		if ($company_id != '' & $company_id!=0) {
           $match_query['passenger_cid'] = (int)$company_id;
        }
        $options=[
				'projection'=>['_id' =>1,'user_status' => 1]
			];
		$res = $this->mongo_db->find(MDB_PASSENGERS, $match_query, $options);
		$result1 = $res;
        if (count($result1) == 0) {
            /* below script hided becos in new version During signUp there is OTP verification and referral code So the user has been redirected to register page*/
            $password       = text::random($type = 'alnum', $length = 6);
            $activation_key = Commonfunction::admin_random_user_password_generator(); //
            $common_model   = Model::factory('commonmodel');
            $current_time = $this->currentdate_bytimezone;
			$current_time = Commonfunction::MongoDate(strtotime($current_time));
			/*$rs = $this->mongo_db->find(MDB_PASSENGERS,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
			$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);*/
			$options=[
				'projection'=>[
				   '_id'=>1,                               
					],
				'sort'=>[
					'_id'=>-1
					],
				'limit'=>1
			];
			$res = $this->mongo_db->find(MDB_PASSENGERS,[],$options);
			$res = (!empty($res))?array($res[0]['_id']=>0):array(1);
			reset($res);
			$first_key = key($res);
			$inc_id = $first_key+1;
			
            $insert_array = array(
				'_id' => (int)$inc_id,
                'name' => $fname,
                'lastname' => $lname,
                'email' => $email,
                'profile_image' => $image_name,
                'password' => md5($password),
                'org_password' => $password,
                'otp' => $otp,
                'phone' => $uid,
                'address' => '',
                'country_code' =>'',
                'referral_code_amount' =>'',
                'split_fare' =>'',
                'skip_credit_card' =>'',
                'forgot_password' =>'',
                'referral_code' => $referral_code,
                'fb_user_id' => $uid,
                'fb_access_token' => $accessToken,
                'activation_key' => $activation_key,
                'activation_status' => 1,
                'login_from' => 3,
                'user_status' => 'A',
                'created_date' => $current_time,
                'updated_date' => $current_time,
                'passenger_cid' => (int)$company_id
            );
			//print_r($insert_array);exit;
			$result = $this->mongo_db->insertOne(MDB_PASSENGERS,$insert_array);
            if ($devicetoken != "") {
                $update_array               = array(
                    "device_token" => $devicetoken,
                    "device_id" => $device_id,
                    "device_type" => (int)$devicetype
                );
				$update_device_token_result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$inc_id),array('$set'=>$update_array),array('upsert'=>false));                
            }
            return 2;
        } else if (count($result1) == 1) {
			$user_status = isset($result1[0]['user_status']) ? $result1[0]['user_status'] : 'D';
			if($user_status == 'D'){
				return -10;
			}
			$match_query = array();
			$match_query['email'] = $email;
			$match_query['fb_user_id'] = array('$ne'=>'');
			$match_query['fb_access_token'] = array('$ne'=>'');
			if ($company_id != '' & $company_id!=0) {
			   $match_query['passenger_cid'] = (int)$company_id;
			}
			$ress = $this->mongo_db->findOne(MDB_PASSENGERS,$match_query,array('_id'));
            $counts = (isset($ress['_id'])?count($ress['_id']):0);
            //~ print_r($ress);exit;
            if ($counts != 0) {
                $passenger_id = (isset($ress['_id'])?$ress['_id']:'');
                /************* Check whether Mobile Details Filled ******************/
				$match_query = array();
				$match_query['email'] = $email;
				$match_query['phone'] = $uid;
				if ($company_id != '' & $company_id!=0) {
				   $match_query['passenger_cid'] = (int)$company_id;
				}               
				//print_r($match_query);
				$mobileresult = $this->mongo_db->count(MDB_PASSENGERS,$match_query);
                $mobile_count = (isset($mobileresult)) ? $mobileresult:0;
                
				/************* Check whether Personal Details Filled ******************/
                if ($company_id != '' && $company_id != 0) {
					 $match = array( "\$and" => array(array('passenger_cid' => (int)$company_id ),
													  array('email' => $email ),
													  array("\$or"=>array(array( 'name' => "") , array( 'lastname' => "" ) ) ) ) );
                } else {
					 $match = array( "\$and" => array(array('email' => $email ),
													  array("\$or"=>array(array( 'name' => "") ,
																		  array( 'lastname' => "" ) ) ) ) );                  
                }
				$result         = $this->mongo_db->count(MDB_PASSENGERS,$match);               
                $personal_count = (isset($result)?$result:0);
				$arguments = array(array('$unwind'=>'$creditcard_details'),
								   array('$match'=>array('_id'=>(int)$passenger_id)));
				$cardresult = $this->mongo_db->aggregate(MDB_PASSENGERS,$arguments);
				$card_count = (isset($cardresult['result'])?count($cardresult['result']):0);				
                $update_array = array("login_from" => 3,
									 "login_status" => 'S',
									 "device_token" => $devicetoken,
									 "device_id" => $device_id,
									 "device_type" => $devicetype);
                /*$update_array = array(
                    "login_from" => 3
                );*/
				$match_query = array();
				$match_query['email'] = $email;
				if ($company_id != '' & $company_id!=0) {
				   $match_query['passenger_cid'] = (int)$company_id;
				} 
				$update_device_token_result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match_query,array('$set'=>$update_array),array('upsert'=>false));
				//echo $counts."-->".$personal_count."-->".$mobile_count."-->".$card_count;exit;
				
                if (($counts == 1) && ($personal_count == 0) && ($mobile_count == 0) && ($card_count > 0)) {
                    return 1;
                } else if ((($counts == 1) && ($mobile_count == 1) && ($personal_count == 1) && ($card_count == 0)) || (($counts == 1) && ($mobile_count == 1) && ($personal_count == 0) && ($card_count == 0))) {
                    return 2;
                } else if (($counts == 1) && ($mobile_count == 0) && ($personal_count == 1) && ($card_count == 0)) {
                    return 3;
                } else if (($counts == 1) && ($mobile_count == 0) && ($personal_count == 0) && ($card_count == 0)) {
                    return 4;
                } else if (($counts == 0) && ($personal_count == 0) && ($mobile_count == 0)) {
                    return -2;
                }
            } else {
                return -2;
            }
        } else {
            return -2;
        }
    }
    // Passenger Mobile Number 

	
	public function update_passenger_mobile($email = "", $mobile = "",$country_code = "")
    {
        try {
            $update_array = array("phone" => $mobile,"country_code" => $country_code);
			$match_query = array();
			$match_query['email'] = $email;
			$result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match_query,array('$set'=>$update_array),array('upsert'=>false));
			return (empty($result->getwriteErrors())) ? 1 : 0;
        }
        catch (Kohana_Exception $e) {
            return -1;
        }
    }
    //Passenger Profile
    public function passenger_profile($userid,$status='')
    {	
		$match = array('_id' => (int)$userid);
		if($status != ''){
			$match['user_status'] = $status;
		}
		$project = array('name','lastname','org_password','password','salutation','email','referral_code','profile_image','country_code','phone','discount','user_status','login_from');
		$res = $this->mongo_db->findOne(MDB_PASSENGERS,$match,$project);
		
		 if(!empty($res)){
			$temp_arr['email'] = isset($res['email'])?$res['email']:'';
			$temp_arr['profile_image'] = isset($res['profile_image'])?$res['profile_image']:'';
			$temp_arr['salutation'] = isset($res['salutation'])?$res['salutation']:'';
			$temp_arr['name'] = isset($res['name'])?$res['name']:'';
			$temp_arr['lastname'] = isset($res['lastname'])?$res['lastname']:'';
			$temp_arr['country_code'] = isset($res['country_code'])?$res['country_code']:'';
			$temp_arr['phone'] = isset($res['phone'])?$res['phone']:'';
			$temp_arr['org_password'] = isset($res['org_password'])?$res['org_password']:'';
			$temp_arr['referral_code'] = isset($res['referral_code'])?$res['referral_code']:'';
			$temp_arr['password'] = isset($res['password'])?$res['password']:'';
			$temp_arr['user_status'] = isset($res['user_status'])?$res['user_status']:'';
			$temp_arr['login_from'] = isset($res['login_from'])?$res['login_from']:'';
			$temp_arr['discount'] = isset($res['discount'])?$res['discount']:'';
			$result[] = $temp_arr;
		}
		
		return (!empty($result)) ? $result : array();
    }
    /** Save Customer Booking **/
    public function savebooking($val, $company_id)
    {		
		//print_r($val);exit;
		$pickup_time = urldecode($val['pickup_time']);
		$roundtrip = $val['roundtrip'];
		$cityname = $val['cityname'];
		
		$approx_distance = $val['approx_distance'];
		$approx_duration = $val['approx_duration'];
		$approx_fare = "";//commonfunction::real_escape_string($val['approx_fare']);
		$pickup_latitude = $val['pickup_latitude'];
		$pickup_longitude = $val['pickup_longitude'];
		$drop_latitude = $val['drop_latitude'];
		$drop_longitude = $val['drop_longitude'];
		$notes_driver = $val['notes'];
		$passenger_app_version = $val['passenger_app_version'];
		$distance_away = (isset($val['distance_away'])) ? $val['distance_away'] : '';
		$promo_code = $val['promo_code'];
		$now_after = $val['now_after'];
		$pre_transaction_id = $val['pre_transaction_id'];
		$pre_transaction_amount = $val['pre_transaction_amount'];

		$sub_logid = $this->get_sublogid($val['sub_logid']);
		$city_id="";
		$pass_company = (!empty($company_id)) ? $company_id : 0;		
		$city_name = Commonfunction::getCityName($pickup_latitude,$pickup_longitude);
								
        $company_tax      = ""; //$get_taxi_fare_based_model[0]['company_tax'];			
        if ($roundtrip == 'true') {
            $pickupdrop = 1;
        } else {
            $pickupdrop = 0;
        }
        $waitingtime = '';
        $company_id  = $this->get_company_id($val['driver_id']);
        if (count($company_id) > 0) {
            $company_id = $company_id[0]['company_id'];
        } else {
            $company_id = 0;
        }
		
		//split fare values
		$totalsplit = 0;
		$spliFareArr = array();
		$friend_id1 = (isset($val['friend_id1'])) ? $val['friend_id1'] : '';
		$friend_percentage1 = (isset($val['friend_percentage1'])) ? $val['friend_percentage1'] : '';
		$friend_percentage_amt1 = (isset($val['friend_percentage_amt1'])) ? $val['friend_percentage_amt1'] : '';
		if(!empty($friend_id1)){
			$spliFareArr[$friend_id1] = $friend_percentage1."_" .$friend_percentage_amt1;
			$totalsplit++;
		}
		
		$friend_id2 = (isset($val['friend_id2'])) ? $val['friend_id2'] : '';
		$friend_percentage2 = (isset($val['friend_percentage2'])) ? $val['friend_percentage2'] : '';
		$friend_percentage_amt2 = (isset($val['friend_percentage_amt2'])) ? $val['friend_percentage_amt2'] : '';
		if(!empty($friend_id2)){
			$spliFareArr[$friend_id2] = $friend_percentage2."_" .$friend_percentage_amt2;
			$totalsplit++;
		}
			
		$friend_id3 = (isset($val['friend_id3'])) ? $val['friend_id3'] : '';
		$friend_percentage3 = (isset($val['friend_percentage3'])) ? $val['friend_percentage3'] : '';
		$friend_percentage_amt3 = (isset($val['friend_percentage_amt3'])) ? $val['friend_percentage_amt3'] : '';
		if(!empty($friend_id3)){
			$spliFareArr[$friend_id3] = $friend_percentage3."_" .$friend_percentage_amt3;
			$totalsplit++;
		}
		
		$friend_id4 = (isset($val['friend_id4'])) ? $val['friend_id4'] : '';
		$friend_percentage4 = (isset($val['friend_percentage4'])) ? $val['friend_percentage4'] : '';
		$friend_percentage_amt4 = (isset($val['friend_percentage_amt4'])) ? $val['friend_percentage_amt4'] : '';
		if(!empty($friend_id4)){
			$spliFareArr[$friend_id4] = $friend_percentage4."_" .$friend_percentage_amt4;
			$totalsplit++;
		}
		
		# Setting static fare percenetage for 3 passengers
		if($totalsplit == 3){
			$spliFareArr[$friend_id1] = "34_".$friend_percentage_amt1;
			$spliFareArr[$friend_id2] = "33_".$friend_percentage_amt2;
			$spliFareArr[$friend_id3] = "33_".$friend_percentage_amt3;
		}
			
        
        $pickupTimezone = $this->getpickupTimezone($pickup_latitude,$pickup_longitude);
		$update_time = convert_timezone('now',$pickupTimezone);
        $booking_key                = text::random($type = 'alnum', $length = 10);	
        $isSplit = (count($spliFareArr) > 1) ? 1:0;
        $driver_availability_result = $this->get_driver_availability($val['driver_id'], $update_time);
        
        # created date
		$createdate = Commonfunction::createdateby_user_timezone();
			
        if (count($driver_availability_result) == 0) {
			/*$rs = $this->mongo_db->find(MDB_PASSENGERS_LOGS,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
			$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);*/
			$options=[
				'projection'=>[
				   '_id'=>1,                               
					],
				'sort'=>[
					'_id'=>-1
					 ],
				'limit'=>1
			];
			$res = $this->mongo_db->find(MDB_PASSENGERS_LOGS,[],$options);
			$res = (!empty($res))?array($res[0]['_id']=>0):array(1);
			reset($res);
			$first_key = key($res);
			$inc_id = $first_key+1;
            if ($now_after == 0) {
				$insert_array = array(
					'_id' => (int)$inc_id,
					'passengers_id' => (int)$val['passenger_id'],
					'driver_id' => (int)$val['driver_id'],
					'company_id' => (int)$company_id,
					'current_location' => (urldecode($val['pickupplace'])),
					'pickup_latitude' => $pickup_latitude,
					'pickup_longitude' => $pickup_longitude,
					'drop_location' => (urldecode($val['dropplace'])),
					'drop_latitude' => $drop_latitude,
					'drop_longitude' => $drop_longitude,
					'no_passengers' => 1,
					'approx_distance' => $approx_distance,
					'approx_fare' => (double)$approx_fare,
					'time_to_reach_passen' => $distance_away,
					'pickup_time' => Commonfunction::MongoDate(strtotime($update_time)),
					'pickupdrop' => $pickupdrop,
					'waitingtime' => $waitingtime,
					'createdate' => Commonfunction::MongoDate(strtotime($createdate)),
					'taxi_id' => (int)$val['taxi_id'],
					'booking_from' => 1,
					'search_city' => (int)$city_id,
					'sub_logid' => (int)$sub_logid,
					'notes_driver' => $notes_driver,
					'booking_from_cid' => (int)$company_id,
					'company_tax' => (float)$company_tax,
					'bookingtype' => 1,
					'bookby' => 1,
					'promocode' => $promo_code,
					'now_after' => 0,
					'pre_transaction_id' => $pre_transaction_id,
					'pre_transaction_amount' => (double)$pre_transaction_amount,
					'city_name' => $city_name,
					'passenger_app_version' => $passenger_app_version,
					'is_split_trip' => $isSplit,
					'trip_timezone' => $pickupTimezone,
					'notification_status' => 0,
					'taxi_modelid' => (int)$val['motor_model']
				);
                $result = $this->mongo_db->insertOne(MDB_PASSENGERS_LOGS,$insert_array);
            } else {
                $pickup_time = date("Y-m-d H:i:s",strtotime($pickup_time));
				$insert_array = array(
					'_id' => (int)$inc_id,
					'booking_key' => $booking_key,
                    'passengers_id' => (int)$val['passenger_id'],
                    'company_id' => 0,
                    'current_location' => urldecode($val['pickupplace']),
                    'pickup_latitude' => $pickup_latitude,
                    'pickup_longitude' => $pickup_longitude,
                    'drop_location' => urldecode($val['dropplace']),
                    'drop_latitude' => $drop_latitude,
                    'drop_longitude' => $drop_longitude,
                    'pickup_time' => Commonfunction::MongoDate(strtotime($pickup_time)),
                    //'pickup_time' => $pickup_time,
                    'no_passengers' => 0,
                    'approx_distance' => (double)$approx_distance,
                    'approx_duration' => $distance_away,
                    'approx_fare' => $approx_fare,
                    'search_city' => (int)$city_id,
                    'notes_driver' => $notes_driver,
                    'faretype' => 0,
                    'fixedprice' => 0,
                    'bookingtype' => 2,
                    'luggage' => 0,
                    'bookby' => 2,
                    'operator_id' => 0,
                    'travel_status' => 0,
                    'taxi_modelid' => (int)$val['motor_model'],
                    'recurrent_type' => 0,
                    'company_tax' => (float)$company_tax,
                    'promocode' => $promo_code,               
                    'createdate' => Commonfunction::MongoDate(strtotime($createdate)),
                    'now_after' => 1,
					'city_name' => $city_name,
					'passenger_app_version' => $passenger_app_version,
					'is_split_trip' => $isSplit,
					'trip_timezone' => $pickupTimezone,
					'pre_transaction_id' => $pre_transaction_id,
					'notification_status' => 0,
					'pre_transaction_amount' => (double)$pre_transaction_amount
				);
                $result = $this->mongo_db->insertOne(MDB_PASSENGERS_LOGS,$insert_array);                
            }
            if ($sub_logid == '' || $sub_logid == '0') {
				$update = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS ,array('_id'=>(int)$inc_id),array('$set'=>array('sub_log_id'=>(int)$inc_id)),array('upsert'=>false));
            }
		
			if(count($spliFareArr) > 0) {
				foreach($spliFareArr as $key => $values)
				{
					$approve_status = ($val['passenger_id']==$key) ? 'A' : 'I';
					$perctvalue=explode('_',$values);
					$friend_percent=isset($perctvalue[0])?$perctvalue[0]:"";
					$friend_amnt=isset($perctvalue[1])?$perctvalue[1]:"";
					//$split_id = Commonfunction::get_auto_id(MDB_SPLIT_LOG);
					$split_id=0;
					$args  = array(array('$unwind' => '$split_details'),
						array('$project' => array('split_id' => '$split_details.split_id')),
						array('$sort' => array('split_details.split_id' => -1))
					);
					$get_id = $this->mongo_db->aggregate(MDB_CSC,$args);
					$split_id = (!empty($get_id['result'])) ? $get_id['result'][0]['split_id'] : 0;
					$split_id +=1;
					$insert_arr = array('split_details' => array('split_id' => (int)$split_id,
										'trip_id' => (int)$inc_id,
										'friends_p_id' => (int)$key,
										'fare_percentage' => (double)$friend_percent,
										'createdate' => Commonfunction::MongoDate(strtotime($update_time)),
										'approve_status' => $approve_status,
										'appx_amount' => (double)$friend_amnt,
										'passenger_payment_option' => $val["passenger_payment_option"]
										));
					if($key != 0){
						$this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$inc_id),
												array('$push'=>$insert_arr),array('upsert' => false));
					}
				}
			}else{
				//$now_after=1;		
				if($now_after != 0) {
					$split_id=0;
					$args  = array(array('$unwind' => '$split_details'),
						array('$project' => array('split_id' => '$split_details.split_id')),
						array('$sort' => array('split_details.split_id' => -1))
					);
					$get_id = $this->mongo_db->aggregate(MDB_CSC,$args);	
					$split_id = (!empty($get_id['result'])) ? $get_id['result'][0]['split_id'] : 0;
					$split_id +=1;
					$insert_arr = array('split_details' => array('split_id' => (int)$split_id,
											'friends_p_id' => (int)$val['passenger_id'],
											'fare_percentage' => 100,
											'createdate' => Commonfunction::MongoDate(strtotime($update_time)),
											'approve_status' => 'A',
											'appx_amount' => (double)$approx_fare,
											'passenger_payment_option' => $val["passenger_payment_option"]
											));
					$this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$inc_id),
													array('$push'=>$insert_arr),array('upsert' => false));
				}
			}			
			return (!empty($result->getwriteErrors()) ? 0 : $inc_id);
        } else {
            return 'F'; //Driver already booed for this time
        }
    }
    /** Get Company id for the Driver **/
    public function get_company_id($driver_id)
    {		
		$result = array();
		if($driver_id!=0){				
			$res = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=> (int)$driver_id),array('company_id'));
			if(!empty($res)){
				$result[] = $res;
			}
		}
		return $result;
    }
    /** Driver availability **/
    public function get_driver_availability($driver_id, $pickup_time)
    {
		$match = array('pickup_time' => Commonfunction::MongoDate(strtotime($pickup_time)),
					   'driver_id' => (int)$driver_id,
					   'driver_reply' => 'A',
					   'travel_status' => 9 );
	   $options=[
				'projection'=>[
				   '_id'=>1,                               
				]
			];
		$res = $this->mongo_db->find(MDB_PASSENGERS_LOGS,$match,$options);
		$result = $res;
		return (!empty($result)?$result:array());
    }
    //Change Password for Both Driver and Passenger
    public function chg_password_passenger($array, $company_id = '', $type = '')
    {
        //Checking the Confirmation of Password
        
        if ($array['new_password'] == $array['confirm_password']) { 
            if ($type == 'D'){
			   $profile = $this->driver_profile($array['id']);
			   $table= MDB_PEOPLE;
            }
            else {
                $profile = $this->passenger_profile($array['id']); 
                $table= MDB_PASSENGERS;
            }
            if (count($profile) > 0) {
				if ($type == 'D')
					$profile_password = $profile[0]['password'];
				else
					$profile_password = $profile[0]['password'];						

                if ($array['old_password'] == $array['new_password']) {
                    return -4;
                } else if ($profile_password == $array['old_password']) {
					
					$update_array = array('password' => md5($array['new_password']),
											//'org_password' => $array['new_password']
											);
					$match = array();
					$match['_id'] = (int)$array['id'];		
                    if ($type == 'D') {
						$result = $this->mongo_db->updateOne($table,$match,array('$set'=>$update_array),array('upsert'=>false));
                    } else {						
                        if ($company_id != '' && $company_id != 0) {							
							$match['passenger_cid'] = (int)$company_id;
                        }
						$result = $this->mongo_db->updateOne($table,$match,array('$set'=>$update_array),array('upsert'=>false));
                    }
                    return 1;
                } else
                    return -2;
            } else
                return -3;
        } else {
            return -1;
        }
    }
    //Save Comments and Ratings from Passenger
    public function savecomments($log_id = "", $ratings = "", $comments = "")
    {
		$update = array('comments' => $comments,'rating' => (int)$ratings);
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$log_id),
										  array('$set'=>$update),array('upsert'=>false));
	    
		$result = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$log_id),array('driver_id'));
		return (isset($result['driver_id']) ? $result['driver_id'] : 0);		
    }
    //Common Function for updation
    public function update_table($table, $arr, $cond1, $cond2)
    {
		//echo $table;
		if(is_numeric($cond2)){
			$cond2 = (int)$cond2;
		}		
        $result = $this->mongo_db->updateOne($table,array($cond1 => $cond2),array( '$set'=>$arr),array('upsert'=>false));		
        //return (empty($result->getwriteErrors()) ? 1:$result['errmsg']);
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }     
    public function update_split_fare($arr, $passenger_log_id, $passengers_id)
    {
        $result = $this->mongo_db->updateOne(MDB_PASSENGERS, array('_id' => (int)$passenger_log_id, "friends_p_id" => $passengers_id),array('$set'=>$arr),array('upsert'=>false));		
        
        $match = array('_id'=>(int)$passenger_log_id);
		$args = array(array('$unwind' => '$split_details'),
				  array('$match' => $match),
				  array('$project' => array('pass_id' => '$split_details.friends_p_id'))
				);
		$keys = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);			
		$i =0;$val = array();
		foreach($keys['result'] as $k => $v ){
			if($passengers_id == $k['pass_id']){
					
				$update_array = array("split_details.$i.fare_percentage" => $arr['fare_percentage'],
							"split_details.$i.appx_amount" => (double)$arr['appx_amount'] );
			}
			$i++;
		}		
		if(!empty($update_array)){
			$result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$passenger_log_id),
										array('$set'=>$update_array),
										array('upsert' => false));
		}
		return 1;
    }
    //Driver Profile
    public function driver_profile($userid)
    {   
		$result = $temp_arr = array();
		$arguments = array(
					array('$lookup'=>array(
							'from'=>MDB_DRIVER_INFO,
							'localField'=>"_id",
							'foreignField'=>"_id",
							'as'=>"driverinfo"        
						)),
						array('$unwind' =>  array( 'path' =>  '$driverinfo', 'preserveNullAndEmptyArrays' =>  true)),		
						array('$lookup'=>array(
							'from'=>MDB_COMPANY,
							'localField'=>"company_id",
							'foreignField'=>"_id",
							'as'=>"company"        
						)),
						array('$unwind' =>  array( 'path' =>  '$company', 'preserveNullAndEmptyArrays' =>  true)),		
						array('$lookup'=>array(
							'from'=>MDB_TAXI_DRIVER_MAPPING,
							'localField'=>"_id",
							'foreignField'=>"mapping_driverid",
							 'as'=>"taxi_driver_mapping"        
						)),
						array('$unwind' =>  array( 'path' =>  '$taxi_driver_mapping', 'preserveNullAndEmptyArrays' =>  true)),		
						array('$lookup'=>array(
							'from'=>MDB_TAXI,
							'localField'=>"taxi_driver_mapping.mapping_taxiid",
							'foreignField'=>"_id",
							 'as'=>"taxi"        
						)),
						array('$unwind' =>  array( 'path' =>  '$taxi', 'preserveNullAndEmptyArrays' =>  true)),		
						array('$lookup'=>array(
							'from'=>MDB_MOTOR_MODEL,
							'localField'=>"taxi.taxi_model",
							'foreignField'=>"_id",
							 'as'=>"motor_model"        
						)),
						array('$unwind' =>  array( 'path' =>  '$motor_model', 'preserveNullAndEmptyArrays' =>  true)),		
						array('$lookup'=>array(
							'from'=>MDB_DRIVER_REF,
							'localField'=>"_id",
							'foreignField'=>"registered_driver_id",
							 'as'=>"driver_ref"        
						)),
						array('$unwind' =>  array( 'path' =>  '$driver_ref', 'preserveNullAndEmptyArrays' =>  true)),		
						array('$match'=>array(
							'_id'=>(int)$userid,
							'user_type'=>"D",
							"taxi_driver_mapping.mapping_status"=>"A"  ,
							"taxi_driver_mapping.mapping_startdate" => array('$lte' => Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone))),
							"taxi_driver_mapping.mapping_enddate" => array('$gte' => Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone))),
						)),
						array('$project' => array(
							'salutation' => '$salutation',
							'name' => '$name',
							'company_address' => '$company.companydetails.company_address',
							'name' => '$name',
							'lastname' => '$lastname',
							'email' => '$email',
							'phone' => '$phone',
							'userid' => '$_id',
							'address' => '$address',
							'password' => '$password',
							'otp' => '$otp',
							'photo' => '$photo',
							'device_type' => '$device_type',
							'device_token' => '$device_token',
							'login_status' => '$login_status',
							'user_type' => '$user_type',
							'account_balance' => '$account_balance',
							'driver_referral_code' => '$driver_referral_code',
							'notification_setting' => '$notification_setting',
							'company_id' => '$company_id',
							'driver_license_id' => '$driver_license_id',
							'profile_picture'=>'$profile_picture',
							'bankname'=>'$company.companydetails.bankname',
							'bankaccount_no'=>'$company.companydetails.bankaccount_no',
							'company_ownerid'=>'$company.companydetails.userid',
							'taxi_no'=>'$taxi.taxi_no',
							'mapping_startdate'=>'$taxi_driver_mapping.mapping_startdate',
							'mapping_enddate'=>'$taxi_driver_mapping.mapping_enddate',
							'model_name'=>'$motor_model.model_name',						
							'driver_wallet'=>'$driver_ref.registered_driver_wallet'	,
							'shift_status'=>'$driverinfo.shift_status',					
							'status'=>'$driverinfo.status'					
						))					
					);
        $res = $this->mongo_db->aggregate(MDB_PEOPLE,$arguments);
        //print_r($arguments);exit;
        if(!empty($res['result'])){
			
			$r = $res['result'][0];
			
			$temp_arr['salutation'] = isset($r['salutation'])?$r['salutation']:'';
			$temp_arr['name'] = isset($r['name'])?$r['name']:'';
			$temp_arr['company_address'] = isset($r['company_address'])?$r['company_address']:'';
			$temp_arr['name'] = isset($r['name'])?$r['name']:'';
			$temp_arr['lastname'] = isset($r['lastname'])?$r['lastname']:'';
			$temp_arr['email'] = isset($r['email'])?$r['email']:'';
			$temp_arr['phone'] = isset($r['phone'])?$r['phone']:'';
			$temp_arr['userid'] = isset($r['userid'])?$r['userid']:'';
			$temp_arr['address'] = isset($r['address'])?$r['address']:'';
			$temp_arr['password'] = isset($r['password'])?$r['password']:'';
			$temp_arr['otp'] = isset($r['otp'])?$r['otp']:'';
			$temp_arr['photo'] = isset($r['photo'])?$r['photo']:'';
			$temp_arr['device_type'] = isset($r['device_type'])?$r['device_type']:'';
			$temp_arr['device_token'] = isset($r['device_token'])?$r['device_token']:'';
			$temp_arr['login_status'] = isset($r['login_status'])?$r['login_status']:'';
			$temp_arr['user_type'] = isset($r['user_type'])?$r['user_type']:'';
			$temp_arr['driver_referral_code'] = isset($r['driver_referral_code'])?$r['driver_referral_code']:'';
			$temp_arr['driver_wallet_amount'] = isset($r['driver_wallet'])?(string)$r['driver_wallet']:'0';
			$temp_arr['notification_setting'] = isset($r['notification_setting'])?$r['notification_setting']:'';
			$temp_arr['company_id'] = isset($r['company_id'])?$r['company_id']:'';
			$temp_arr['driver_license_id'] = isset($r['driver_license_id'])?$r['driver_license_id']:'';
			$temp_arr['profile_picture'] = isset($r['profile_picture'])?$r['profile_picture']:'';
			$temp_arr['bankname'] = isset($r['bankname'])?$r['bankname']:'';
			$temp_arr['bankaccount_no'] = isset($r['bankaccount_no'])?$r['bankaccount_no']:'';
			$temp_arr['company_ownerid'] = isset($r['company_ownerid'])?$r['company_ownerid']:'';
			$temp_arr['taxi_no'] = isset($r['taxi_no'])?$r['taxi_no']:'';
			$temp_arr['account_balance'] = isset($r['account_balance'])?(string)$r['account_balance']:'0';
			
			$temp_arr['mapping_startdate'] = isset($r['mapping_startdate'])? commonfunction::convertphpdate('Y-m-d H:i:s',$r['mapping_startdate']):'';
			$temp_arr['mapping_enddate'] = isset($r['mapping_enddate'])? commonfunction::convertphpdate('Y-m-d H:i:s',$r['mapping_enddate']):'';
			$temp_arr['model_name'] = isset($r['model_name'])?$r['model_name']:'';						
			$temp_arr['driver_wallet'] = isset($r['driver_wallet'])?$r['driver_wallet']:'';
			$temp_arr['shift_status'] = isset($r['shift_status'])?$r['shift_status']:'';
			$temp_arr['status'] = isset($r['status'])?$r['status']:'';
			
			$result[] = $temp_arr;
		}
        return $result;
    }
    //Driver Login
    public function driver_login($phone, $pwd, $company_id)
    {    
		$result = $temp_arr = array(); 
		$match = array(
					"phone" => $phone,
					"password" => $pwd,
					"user_type" => 'D'
				);   
        if($company_id != '' && $company_id != 0){
			$match['company_id'] = (int)$company_id;
		}
		$project = array("status", "login_status",
						"login_from", "device_token",
						"device_id", "company_id",
						"driver_first_login", "_id", "salutation");
		$res = $this->mongo_db->findOne(MDB_PEOPLE,$match,$project);
		if(!empty($res)){
			$temp_arr['status'] = isset($res['status']) ? $res['status'] :'';
			$temp_arr['login_status'] = isset($res['login_status']) ? $res['login_status'] :'';
			$temp_arr['login_from'] = isset($res['login_from']) ? $res['login_from'] :'';
			$temp_arr['device_token'] = isset($res['device_token']) ? $res['device_token'] :'';
			$temp_arr['device_id'] = isset($res['device_id']) ? $res['device_id'] :'';
			$temp_arr['company_id'] = isset($res['company_id']) ? $res['company_id'] :'';
			$temp_arr['driver_first_login'] = isset($res['driver_first_login']) ? $res['driver_first_login'] :0;
			$temp_arr['id'] = isset($res['_id']) ? $res['_id'] :'';
			$temp_arr['salutation'] = isset($res['salutation']) ? $res['salutation'] :'';
			$result[] = $temp_arr;
		}
		return $result;
    }
    public function check_driver_companydetails($driver_id, $company_id)
    {
        $match = array('_id'=>(int)$driver_id, 'user_type' => 'D');
        if($company_id != '' && $company_id != 0){
			
			$match['company_id'] = (int)$company_id;
		}
        $result = $this->mongo_db->count(MDB_PEOPLE,$match,array('_id'));
        return (!empty($result)) ? $result :0;
    }
    public function check_passenger_companydetails($id, $company_id)
    {
        $match = array();
		$match['_id'] = (int)$id;
		if ($company_id != '' && $company_id !=0) {
			$match['passenger_cid'] = (int)$company_id;
		}
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match);
		return isset($result)?$result:0;
    }
    // Driver Login Status
    public function logged_user_status($driver_id, $company_id)
    {
        if ($company_id != '' && $company_id !=0) {
            $result = $this->mongo_db->findOne(MDB_PEOPLE,array('company_id'=>(int)$company_id,'_id'=>(int)$driver_id),
												array('login_status'));
        } else {
			$result = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=>(int)$driver_id),array('login_status'));
        }
        //print_r($result); exit;
        return ((count($result) >= 1 && (isset($result['login_status']) && $result['login_status'] == 'S')) ? 1 : 0);
    }
    //Update Driver Status
    public function update_driverreply_status($id, $driver_id, $taxi_id, $company_id, $status, $travel_status, $field, $flag, $default_companyid)
    {
        $driver_reply = '';
        $data = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$id),array('driver_reply'));
        if(!empty($data)){
				$driver_reply = isset($data['driver_reply']) ? $data['driver_reply'] : '';
		}		
        //Acceptred Status
        if ($status == 'A') {
            $update_query = array(
                'travel_status' => (int)$travel_status,
                'driver_reply' => $status,
                'driver_id' => (int)$driver_id,
                'taxi_id' => (int)$taxi_id,
                'company_id' => (int)$company_id,
                //'time_to_reach_passen' => $field,
                'msg_status' => 'R'
            );
        }else {  //Rejected Status and Adding the Driver Comments 
            $update_query = array(
                'travel_status' => (int)$travel_status,
                'driver_reply' => $status,
                'driver_id' => (int)$driver_id,
                'taxi_id' => (int)$taxi_id,
                'company_id' => (int)$company_id,
                'driver_comments' => $field,
                'msg_status' => 'R'
            );
        }
        if ($driver_reply == '') {
            $update = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$id),array('$set'=>$update_query),array('upsert'=>false));
            $update_result = (empty($update->getwriteErrors())) ? 1: 0;
            if ($update_result > 0) {
                if ($status == 'A')
                    return 1;
                else if ($status == 'R')
                    return 2;
                else if ($status == 'C')
                    return 3;
            } else {
                return 4;
            }
        } else {
            // Driver cancel the trip when pick up
            if ($flag == 1) {
                $update_query     = array(
                    'travel_status' => 9,
                    'driver_reply' => $status,
                    'driver_comments' => $field,
                    'msg_status' => 'R'
                );
                $update = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$id),array('$set'=>$update_query),array('upsert'=>false));
				$update_result = (empty($update->getwriteErrors()))?1:0;
                if ($update_result > 0) {
                    if ($status == 'R')
                        return 2;
                    else if ($status == 'C')
                        return 3;
                } else {
                    return 4; //
                }
            } else {
                if ($driver_reply == 'A') {
                    return 5; // driver already confirmed
                } else if ($driver_reply == 'R') {
                    return 6; // driver already rejected
                } else if ($driver_reply == 'C') {
                    return 7; // driver already cancelled
                } else {
                    return 0; // Time out or some technical issues
                }
            }
        }
    }
    //Driver Profile Edit
    public function edit_driver_profile($array, $default_companyid)
    {
        try {
            $chk_driver = $this->driver_profile($array['id']);
            if (count($chk_driver) > 0) {
				$match = array('_id' => (int)$array['id'],'user_type' => 'D');
				$result = $this->mongo_db->updateOne(MDB_PEOPLE,$match,array('$set'=>$array),array('upsert'=>false));
				return 0;
            } else {
                return -2;
            }
        }
        catch (Kohana_Exception $e) {
            return 1;		
        }
    }
    //Company Profile Edit
    public function edit_company_profile($array)
    {
        try {
            $chk_driver = $this->driver_profile($array['id']);		
            if (count($chk_driver) > 0) {
                $company_id = $chk_driver[0]['company_id'];
				$result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>(int)$company_id),
												  array('$set' => $array),array('upsert'=>false));
                return 0;
            } else {
                return -2;
            }
        }
        catch (Kohana_Exception $e) {
            return 1;
        }
    }
    public function getLatLong($address)
    {
        $address = str_replace(' ', '+', $address);
        $url     = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key=' . GOOGLE_GEO_API_KEY;
        $ch      = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $geoloc = curl_exec($ch);
        //print_r($geoloc);
        $json   = json_decode($geoloc);
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
    //Applying the Haversine Function to get the Distance
    function Haversine($start, $finish)
    {
        $theta    = $start[1] - $finish[1];
        $distance = (sin(deg2rad($start[0])) * sin(deg2rad($finish[0]))) + (cos(deg2rad($start[0])) * cos(deg2rad($finish[0])) * cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        return round($distance, 2);
    }
    function Tripdistance_Haversine($start, $finish)
    {
        if ((count($start) > 0) && (count($finish) > 0)) {
            $theta        = $start[1] - $finish[1];
            // modifiedon 04-05-2014
            $distance     = (sin(deg2rad($start[0])) * sin(deg2rad($finish[0]))) + (cos(deg2rad($start[0])) * cos(deg2rad($finish[0])) * cos(deg2rad($theta)));
            //echo '<br>';
            $cal_distance = acos($distance);
            //var_dump($cal_distance);
            if (is_nan($cal_distance)) {
                $distance = '0';
                return $distance;
            } else {
                //echo 'cos_distance'.$cal_distance;
                $red_deg_distance = rad2deg($cal_distance);
                //echo '<br>';
                //echo 'distance'.$red_deg_distance;
                $final_distance   = $red_deg_distance * 60 * 1.1515;
                //echo '<br>';
                //echo 'miles'.$final_distance;
                $after_round      = round($final_distance, 4);
                //echo '<br>'.'After Round'.$after_round;
                return $after_round;
            }
        } else {
            $distance = 0;
            return $distance;
        }
    }
    public function passenger_detailsbyemail($email, $company_id,$uid="")
    {
		$result = $temp_arr = array();
		$match_query = array();
		if($email!=""){
			$match_query['email'] = $email;
		}elseif($uid!=""){
			$match_query['fb_user_id'] = $uid;
		}else{
			$match_query = array();
		}
		if ($company_id != '' & $company_id!=0) {
		   $match_query['passenger_cid'] = (int)$company_id;
		}
        $res = $this->mongo_db->findOne(MDB_PASSENGERS,$match_query,array('_id','email','profile_image','profile_image','salutation','name','lastname','country_code','phone','org_password','address','referral_code','referral_code_amount', 'split_fare', 'otp','fb_user_id','fb_access_token','user_status','login_from','device_id','device_token','skip_credit_card'));
       
        if(!empty($res)){
			
			$temp_arr['id'] = isset($res['_id'])?$res['_id']:'';
			$temp_arr['email'] = isset($res['email'])?$res['email']:'';
			$temp_arr['profile_image'] = isset($res['profile_image'])?$res['profile_image']:'';
			$temp_arr['salutation'] = isset($res['salutation'])?$res['salutation']:'';
			$temp_arr['name'] = isset($res['name'])?$res['name']:'';
			$temp_arr['lastname'] = isset($res['lastname'])?$res['lastname']:'';
			$temp_arr['country_code'] = isset($res['country_code'])?$res['country_code']:'';
			$temp_arr['phone'] = isset($res['phone'])?$res['phone']:'';
			$temp_arr['org_password'] = isset($res['org_password'])?$res['org_password']:'';
			$temp_arr['address'] = isset($res['address'])?$res['address']:'';
			$temp_arr['referral_code'] = isset($res['referral_code'])?$res['referral_code']:'';
			$temp_arr['referral_code_amount'] = isset($res['referral_code_amount'])?$res['referral_code_amount']:'';
			$temp_arr['split_fare'] = (isset($res['split_fare']) && $res['split_fare']!='') ?$res['split_fare']:'0';
			$temp_arr['otp'] = isset($res['otp'])?$res['otp']:'';
			$temp_arr['fb_user_id'] = isset($res['fb_user_id'])?$res['fb_user_id']:'';
			$temp_arr['fb_access_token'] = isset($res['fb_access_token'])?$res['fb_access_token']:'';
			$temp_arr['user_status'] = isset($res['user_status'])?$res['user_status']:'';
			$temp_arr['login_from'] = isset($res['login_from'])?$res['login_from']:'';
			$temp_arr['device_id'] = isset($res['device_id'])?$res['device_id']:'';
			$temp_arr['device_token'] = isset($res['device_token'])?$res['device_token']:'';
			$temp_arr['skip_credit_card'] = isset($res['skip_credit_card'])?$res['skip_credit_card']:'';
			$result[] = $temp_arr;
		}
		
        return $result;
     
    }
    /*** Get Taxi Model***/
    public function get_taxi_model_details($taxi_id = "")
    {
        $result = $this->mongo_db->findOne(MDB_TAXI,array('_id'=>(int)$taxi_id),array("taxi_model"));
        return (isset($result)? $result:array());
    }
    /*** Get Taxi fare per KM & Waiting charge of the company based Company***/
    public function get_model_fare_details($company_id, $model_id = "", $search_city = "",$brand_type="")
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
        $city_model_fare = (!empty($result_fare[0]['city_model_fare']) ? $result_fare[0]['city_model_fare'] : 0);
        if (FARE_SETTINGS == 2 && $brand_type == "M" ) {
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
					//"cancellation_fare"=> array('$add' => array('$cancellation_fare',array('$multiply'=>array('$cancellation_fare',array('$divide'=>array(2,100)))))),
                                        "cancellation_fare"=>'$cancellation_fare',
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
			//$temp_arr['active_record'] = $coordinates;
			$result[] = $temp_arr;
		}
		return $result;
    }
    public function passenger_transdetails($log_id)
    {		
		$result = $temp_arr = array();
		$args = array(
			array('$lookup' => array(
									'from' => MDB_PASSENGERS,
									'localField' => 'passengers_id',
									'foreignField' => '_id',
									'as' => 'passengers',
								)),
			array('$unwind' => '$passengers'),
			array('$lookup' => array(
									'from' => MDB_TRANSACTION,
									'localField' => '_id',
									'foreignField' => 'passengers_log_id',
									'as' => 'trans',
								)),
			array('$unwind' => '$trans'),
			array('$lookup' => array(
									'from' => MDB_PEOPLE,
									'localField' => 'driver_id',
									'foreignField' => '_id',
									'as' => 'people',
								)),
			array('$unwind' => '$people'),
			array('$match' => array('_id' => (int)$log_id)),
			array('$project' =>
				array('passengers_log_id' => '$_id',
					'booking_key' => '$booking_key',
					'passengers_id' => '$passengers_id',
					'driver_id' => '$driver_id',
					'taxi_id' => '$taxi_id',
					'company_id' => '$company_id',
					'current_location' => '$current_location',
					'pickup_latitude' => '$pickup_latitude',
					'pickup_longitude' => '$pickup_longitude',
					'drop_location' => '$drop_location',
					 'drop_latitude' => '$drop_latitude',
					'drop_longitude' => '$drop_longitude',
					'no_passengers' => '$no_passengers',
					'approx_distance' => '$approx_distance',
					'approx_duration' => '$approx_duration',
					'approx_fare' => '$approx_fare',
					'time_to_reach_passen' => '$time_to_reach_passen',
					'pickup_time' => '$pickup_time',
					'dispatch_time' => '$dispatch_time',
					'pickupdrop' => '$pickupdrop',
					'rating' => '$rating',
					'comments' => '$comments',
					'travel_status' => '$travel_status',
					'driver_reply' => '$driver_reply',
					'createdate' => '$createdate',
					'booking_from' => '$booking_from',
					'company_tax' => '$company_tax',
					'faretype' => '$faretype',
					'bookingtype' => '$bookingtype',
					'driver_comments' => '$driver_comments',
					'travel_time' => array('$subtract' => array('$drop_time','$actual_pickup_time')),
					'used_wallet_amount' => '$used_wallet_amount',
					'job_referral' => '$trans._id',
					'distance' => '$trans.distance',
					'actual_distance' => '$trans.actual_distance',
					'tripfare' => '$trans.tripfare',
					'fare' => '$trans.fare',
					'tips' => '$trans.tips',
					'waiting_time' => '$trans.waiting_time',
					'waiting_cost' => '$trans.waiting_cost',
					'tax_amount' => '$trans.company_tax',
					'amt' => '$trans.amt',
					'passenger_discount' => '$trans.passenger_discount',
					'account_discount' => '$trans.account_discount',
					'credits_used' => '$trans.credits_used',
					'transaction_id' => '$trans.transaction_id',
					'payment_type' => '$trans.payment_type',
					'payment_status' => '$trans.payment_status',
					'admin_amount' => '$trans.admin_amount',
					'company_amount' => '$trans.company_amount',
					'nightfare_applicable' => '$trans.nightfare_applicable',
					'nightfare' => '$trans.nightfare',
					'eveningfare_applicable' => '$trans.eveningfare_applicable',
					'eveningfare' => '$trans.eveningfare',
					'trans_packtype' => '$trans.trans_packtype',
					'waiting_time' => '$trans.waiting_time',
					'minutes_fare' => '$trans.minutes_fare',
					'trip_minutes' => '$trans.trip_minutes',
					'passenger_name' => '$passengers.name',
					'passenger_email' => '$passengers.email',
					'driver_name' => '$people.name',
					'driver_email' => '$people.email'  ))
		);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			
			$temp_arr = $res['result'][0];
			$temp_arr['trans_amt'] = isset($temp_arr['trans_amt'][0]) ? $temp_arr['trans_amt'][0]: '0';
			$temp_arr['amt'] = $temp_arr['used_wallet_amount'] + $temp_arr['trans_amt'];
			$temp_arr['distance'] = isset($temp_arr['distance']) ? $temp_arr['distance'] : 0;
			$temp_arr['trip_minutes'] = isset($temp_arr['trip_minutes']) ? $temp_arr['trip_minutes'] : '';
			$temp_arr['pickup_time'] = isset($temp_arr['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['pickup_time']) : '';
			$temp_arr['dispatch_time'] = isset($temp_arr['dispatch_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s', $temp_arr['dispatch_time']) : '';
			$temp_arr['waiting_time'] = isset($temp_arr['waiting_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s', $temp_arr['waiting_time']) : '';
			$result[] = $temp_arr;
		}		
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
    public function triptransact_details($details, $payment_types, $driver_id=0)
    {		
		
		$admin_amt     = ($details['fare'] * $details[0]['admin_commission']) / 100; //payable to admin
		$admin_amt     = round($admin_amt, 2);
		$total_balance = round($details['fare'], 2);
		//Set Commission to Admin
		if(ADMIN_COMMISION_SETTING) {
			$update = $this->mongo_db->updateOne(MDB_PEOPLE,array('user_type' => 'A'),
										  array('$inc' => array('account_balance' => $admin_amt )),
										  array('upsert' => false));			
		}            
       
        $company_amt              = $details['fare'] - $admin_amt;
        $company_amt              = round($company_amt, 2);
		
		//Set Commission to Admin
        if(COMPANY_COMMISION_SETTING) {
			$update = $this->mongo_db->updateOne(MDB_PEOPLE,array('user_type' => 'C','company_id' => (int)$details['company_id']),
										  array('$inc' => array('account_balance' => $company_amt )));		
		}
		$driver_commission_amt = 0;
		
		//Set Commission to Driver
		if(DRIVER_COMMISION_SETTING && $driver_id > 0) {
			
			$driver_com_result = $this->mongo_db->findOne(MDB_COMPANY,array('_id' => (int)$details['company_id']),array('companydetails.driver_commission'));
			$driver_commission = isset($driver_com_result['companydetails']['driver_commission']) ? $driver_com_result['companydetails']['driver_commission'] : 0;
			$driver_commission_amt = round(($company_amt*$driver_commission/100),2);
			$company_bal_amt = $company_amt-$driver_commission_amt;
			$update = $this->mongo_db->updateOne(MDB_PEOPLE,array('user_type' => 'D','_id' => (int)$driver_id),
										  array('$inc' => array('account_balance' => $driver_commission_amt )));		
		}
        
        $current_time             = date('Y-m-d H:i:s');
        $details['CORRELATIONID'] = isset($details['CORRELATIONID']) ? $details['CORRELATIONID'] : '';
        $details['ACK']           = isset($details['ACK']) ? $details['ACK'] : '1';
        $details['CURRENCYCODE']  = isset($details['CURRENCYCODE']) ? $details['CURRENCYCODE'] : '';
		$inc_id = $this->get_insert_id(MDB_TRANSACTION);
		$check_package_type='';
        $insert_array = array(
			'_id' => (int)$inc_id,
			'passengers_log_id' => (int)$details['passengers_log_id'],
			'distance' => $details['distance'],
			'actual_distance' => $details['actual_distance'],
			'distance_unit' => $details['distance_unit'],			
			'tripfare' => (double)$details['tripfare'],
			'fare' => (double)$details['fare'],
			'base_fare' => (double)$details['base_fare'],
			'tips' => (double)$details['tips'],
			'waiting_cost' => (double)$details['waiting_cost'],
			'waiting_time' => $details['waiting_time'],
			'company_tax' => (double)$details['company_tax'],
			'trip_minutes' => $details['trip_minutes'],
			'minutes_fare' => (double)$details['minutes_fare'],
			'passenger_discount' => (double)$details['passenger_discount'],
			'account_discount' => (double)$details['account_discount'],
			'credits_used' => $details['credits_used'],
			'remarks' => $details['remarks'],
			'correlation_id' => $details['CORRELATIONID'],
			'ack' => $details['ACK'],
			'transaction_id' => $details['TRANSACTIONID'],
			'payment_type' => (int)$details['payment_type'],
			'payment_method' => $details['payment_method'],
			'order_time' => Commonfunction::MongoDate(strtotime($current_time)),
			'amt' => (float)$details['amt'],
			'currency_code' => $details['CURRENCYCODE'],
			'payment_status' => $details['ACK'],
			'captured' => 1,
			'admin_amount' => (double)$admin_amt,
			'company_amount' => (double)$company_amt,
			'driver_amount' => (double)$driver_commission_amt,
			'trans_packtype' => $check_package_type,
			'nightfare_applicable' => $details['nightfare_applicable'],
			'nightfare' => (double)$details['nightfare'],
			'payment_gateway_id' => (int)$payment_types,
			'fare_calculation_type' => $details['fare_calculation_type'],
			'tax_percentage' => (double)$details['tax_percentage'],
			'eveningfare' => (double)$details['eveningfare'],
			'eveningfare_applicable' => $details['eveningfare_applicable'],
			'current_date' => Commonfunction::MongoDate(strtotime($this->currentdate)),
			'promo_discount_fare' => (double)$details['promo_discount_fare']
		);
		$result = $this->mongo_db->insertOne(MDB_TRANSACTION,$insert_array);
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    public function cancel_triptransact_details($details, $cancellation_nfree, $payment_types,$driver_id = 0)
    {
		if($cancellation_nfree != 0 && !empty($driver_id))
		{			
			$admin_amt = ($details['total_fare'] * $details[0]['admin_commission'])/100; //payable to admin
			$admin_amt = round($admin_amt, 2);
			$total_balance = round($details['total_fare'],2);
	
			//Set Commission to Admin
			if(ADMIN_COMMISION_SETTING) {					
				$updateresult = $this->mongo_db->updateOne(MDB_PEOPLE,array('user_type'=>'A'),
												array('$inc' => array('account_balance' => (double)$admin_amt )),
												array('upsert'=>false));			
			}

			//$company_amt = $details['total_fare'] - $admin_amt; 	
			$company_amt = $details['total_fare'];
			$company_amt = round($company_amt, 2);	

			//Set Commission to Company
			if(COMPANY_COMMISION_SETTING) {
				
				$updateresult = $this->mongo_db->updateOne(MDB_PEOPLE,
												array('user_type'=>'C', 'company_id' => (int)$details['company_id']),
												array('$inc' => array('account_balance' => (double)$company_amt )),
												array('upsert'=>false));
			}
			
			//Set Commission to Driver
			if(DRIVER_COMMISION_SETTING && $driver_id > 0) {
				
				$driver_com_result = $this->mongo_db->findOne(MDB_COMPANY, 
													array('_id'=>(int)$details['company_id']), array('companydetails.driver_commission'));
				
				$driver_commission = isset($driver_com_result['companydetails']['driver_commission']) ? $driver_com_result['companydetails']['driver_commission'] : 0;
				$driver_commission_amt = round(($company_amt*$driver_commission/100),2);
				$company_bal_amt = $company_amt-$driver_commission_amt;
				
				$updateresult = $this->mongo_db->updateOne(MDB_PEOPLE,
												array('user_type'=>'D', '_id' => (int)$driver_id),
												array('$inc' => array('account_balance' => (double)$driver_commission_amt)),
												array('upsert'=>false));
			}

			if($details['travel_status'] == 4)
			{
				$update_arr = array('comments' => $details['remarks'], 'travel_status' => (int)$details['travel_status'],
									'actual_pickup_time' => Commonfunction::MongoDate(strtotime($this->currentdate)));
				$updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,
												array('_id' => (int)$details['passenger_log_id']),
												array('$set' => $update_arr),
												array('upsert'=>false));
			}
			$current_time = date('Y-m-d H:i:s');
			$details['CORRELATIONID']=isset($details['CORRELATIONID'])?$details['CORRELATIONID']:'';
			$details['ACK']=isset($details['ACK'])?$details['ACK']:'1';
			$details['CURRENCYCODE']=isset($details['CURRENCYCODE'])?$details['CURRENCYCODE']:'';
			$check_package_type='';
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
			//echo '<pre>';print_r($insert_arr);exit;
			$result = $this->mongo_db->insertOne(MDB_TRANSACTION, $insert_arr);
			return $inc_id;
		}
		else
		{	
			$update_arr = array('comments' => $details['remarks'], 'travel_status' => 4, 
								'actual_pickup_time' => Commonfunction::MongoDate(strtotime($this->currentdate)));		
			$updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,
												array('_id' => (int)$details['passenger_log_id']),
												array('$set' => $update_arr),
												array('upsert'=>false));
			return 1;
		}
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
    public function check_travelstatus($log_id)
    {
		$result = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$log_id),
											array("travel_status","driver_reply"));
        return isset($result['travel_status'])?$result['travel_status']: -1 ;
    }
    public function save_driver_location_history($location_array, $default_companyid)
    {
        $get_company_time_details = $this->get_company_time_details($default_companyid);
        $start_time               = $get_company_time_details['start_time']; //Start time
        $end_time                 = $get_company_time_details['end_time']; //end time
        $current_time             = $get_company_time_details['current_time']; // Current Time
        $driver_id                = $location_array['driver_id'];
        $trip_id                  = $location_array['trip_id'];
        $status                   = $location_array['status'];
		
        $location_record_array = explode('|',$location_array['locations']);		
		$location_record = '';	
		$loc_array = array();
		foreach($location_record_array as $key=>$value)
		{
			if($value !=""){
				//$location_record .='['.$value.']'.',';
				$lat_long = explode(',',$value);
				$temp = $lat_long[0];
				$lat_long[0] = (double)$lat_long[1];
				$lat_long[1] = (double)$temp;
				$loc_array[] = $lat_long;
			}
		}		
        if ($driver_id != "") {
            if ($trip_id != '') {
				$trip_query = $this->mongo_db->findOne(MDB_LOCATION_HISTORY,array('trip_id'=>(int)$trip_id),array('loc.coordinates','distance','_id'));
				//print_r($trip_query);exit;
				$trip_check = (!empty($trip_query))?$trip_query:array();
                if (count($trip_check) == 0) {
                    if ($trip_id != 0) {
						/*$rs = $this->mongo_db->find(MDB_LOCATION_HISTORY,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
						$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);*/
						$options=[
							'projection'=>[
							   '_id'=>1,                               
								],
							'sort'=>[
								'_id'=>-1
								 ],
							'limit'=>1
						];
						$res = $this->mongo_db->find(MDB_LOCATION_HISTORY,[],$options);
						$res = (!empty($res))?array($res[0]['_id']=>0):array(1);
						reset($res);
						$first_key = key($res);
						$inc_id = $first_key+1;
						$loc_data = array('_id' => $inc_id,
							'driver_id' => (int)$driver_id,
							'trip_id' => (int)$trip_id,
							'status' => $status,
							'distance' => (float)0,
							'createdate' => Commonfunction::MongoDate(strtotime($current_time)),
							'loc' => array("type"=>"MultiPoint","coordinates"=> $loc_array));
							//print_r($loc_data);exit;
						$loc_result = $this->mongo_db->insertOne(MDB_LOCATION_HISTORY,$loc_data);
						return (!empty($loc_result) && empty($loc_result->getwriteErrors()))? 1:0;
                    } else {
                        return 5; // If there is no trip id means update only driver current location. This is done at controller it self
                    }
                } else {
					$pickup=array();
					$drop=array();					
					if (!empty($trip_check['loc']['coordinates'])) {					
						$pickup_location = (!empty($trip_check['loc']['coordinates'])) ? end($trip_check['loc']['coordinates']) : array();
						$temp = $pickup_location[0];
						$pickup_location[0] = $pickup_location[1];
						$pickup_location[1] = $temp;
						$coordinates=array();$lat_long=array();
						$c=0;
						$explode_location=explode('|',$location_array['locations']);
						for($count=0;$count<count($explode_location);$count++){
							if($explode_location[$count] !=""){
								if($count!=0){
									$pickup_location=explode(',',$explode_location[$c]);
									if($c < count($explode_location)-1){
										$drop_location=explode(',',$explode_location[$c+1]);
									}
									$c++;
								}else{
									$drop_location = explode(',',$explode_location[$count]);
								}
								//$coordinates[] = '['.$explode_location[$count].']';
								$lat_long = explode(',',$explode_location[$count]);
								$temp = $lat_long[0];
								$lat_long[0] = (double)$lat_long[1];
								$lat_long[1] = (double)$temp;
								$coordinates[] = $lat_long;
				
								$distance=$this->Tripdistance_Haversine($pickup_location, $drop_location);
								$current_distance = 0;
								if($distance > 0)
								{
									if(UNIT_NAME != "KM") { //to get distance in miles
										$current_distance = round($distance,4);
									} else { //to get distance in km
										$current_distance = round($distance * 1.609344,4);
									}
								}
								if(isset($total_distance)){
									$prev_distance = $total_distance;
									$total_distance = $prev_distance+$current_distance;
								}else{
									$prev_distance = $trip_check['distance'];
									$total_distance = $prev_distance+$current_distance;
								}
							}								
						}
						
						if ($trip_id != 0) {
							//print_r($coordinates);exit;
							foreach($coordinates as $latlong){
								$result = $this->mongo_db->updateOne(MDB_LOCATION_HISTORY,array('trip_id'=>(int)$trip_id),array('$push'=>array('loc.coordinates'=> $latlong)),array('upsert'=>false));
							}							
							
							$result1 = $this->mongo_db->updateOne(MDB_LOCATION_HISTORY,array('trip_id'=>(int)$trip_id),array('$set'=>array('status'=>$status,'driver_id'=>(int)$driver_id,'distance'=>(float)$total_distance)),array('upsert'=>false));
							
							$result2 = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$trip_id),array('$set'=>array('distance'=>(double)$total_distance)),array('upsert'=>false));
							$result_arr = array(); 
							
							if(empty($result->getwriteErrors()) && empty($result1->getwriteErrors())){
								$result_arr[] = 1;
                                $result_arr[] = $total_distance;
                                return $result_arr;
							} else {
								return 0;
							}
                        } else {
                            return 5;
                        }
                    } else {
                        return 3;
                    }
                }
            } else {
                return 5;
            }
        } else {
            return 2;
        }
    }

    /*public function save_driver_location_history_free($location_array, $default_companyid)
    {
        $get_company_time_details = $this->get_company_time_details($default_companyid);
        $start_time               = $get_company_time_details['start_time']; //Start time
        $end_time                 = $get_company_time_details['end_time']; //end time
        $current_time             = $get_company_time_details['current_time']; // Current Time
        $check_package_type="";
        $driver_id                = $location_array['driver_id'];
        $trip_id                  = $location_array['trip_id'];
        $status                   = $location_array['status'];
		$people_result = $this->mongo_db->findOne(MDB_PEOPLE,array('_id' => (int)$driver_id,'user_type' => 'D'),array('company_id'));
        if (count($people_result) > 0) {
			$arguments = array(
				array('$lookup'=>array(
					'from'=>MDB_PACKAGE,
					'localField'=>"upgrade_packageid",
					'foreignField'=>"_id",
					 'as'=>"package"
				)),
				array('$unwind'=>'$package'),
				array('$match'=> array('upgrade_companyid' =>(int)$people_result['company_id'])),
				array('$project' => array('driver_tracking' => '$driver_tracking'))
			);
			$result = $this->mongo_db->aggregate(MDB_PACKAGE_REPORT,$arguments);
			$first_results = (!empty($result['result']) ? $result['result']: array());
            if (count($first_results) > 0) {
                $check_package_type = (isset($first_results[0]['driver_tracking'])) ? $first_results[0]['driver_tracking'] :'';
            }
            if ($check_package_type != 'S') {
                return 3;
            }
        } else {
            return 3;
        }
		$coordinates = explode('|', $location_array['locations']);
		if (count($coordinates) > 1) {
			$last_1      = array_slice($coordinates, -2, 2, true);
			$coordinates = explode(',', $last_1[count($coordinates) - 2]);
		} else {
			$coordinates = explode(',', $coordinates[0]);
		}
		$latitude  = empty($coordinates['0']) ? '0.0' : $coordinates['0'];
		$longitude = empty($coordinates['1']) ? '0.0' : $coordinates['1'];
        if ($driver_id != "") {
            if (($trip_id == '') || (($trip_id == 0))) {
				$find_query = $this->mongo_db->findOne(MDB_LOCATION_HISTORY,array('driver_id'=>(int)$driver_id,'status'=>'F'),array('_id'));
				//print_r($find_query);exit;
				$find_result = (!empty($find_query))?$find_query:array();				
                if (count($find_result) == 0) {
					//Get the last object id
					$options=[
							'projection'=>[
							   '_id'=>1,                               
								],
							'sort'=>[
								'_id'=>-1
								 ],
							'limit'=>1
						];
					$res = $this->mongo_db->find(MDB_LOCATION_HISTORY,[],$options);
                    $res = (!empty($res))?array($res[0]['_id']=>0):array(1);    
					reset($res);
					$first_key = key($res);
					$inc_id = $first_key+1;
					$loc_data = array('_id' => (int)$inc_id,
						'driver_id' => (int)$driver_id,
						'distance' => (float)0,
						'status' => $status,
						'createdate' => Commonfunction::MongoDate(strtotime($current_time)),
						'loc' => array("type"=>"MultiPoint","coordinates"=>array(array((double)$longitude,(double)$latitude)))
					);					
					$loc_result = $this->mongo_db->insertOne(MDB_LOCATION_HISTORY,$loc_data);
					return (empty($loc_result->getwriteErrors()))?1:0;
                } else {
					$location_hid = $find_result['_id'];
					$result = $this->mongo_db->updateOne(MDB_LOCATION_HISTORY,array('_id'=>(int)$location_hid),array('$push'=>
					array('loc.coordinates'=>array((double)$longitude,(double)$latitude))),array('upsert'=>false));
					return (empty($result->getwriteErrors())) ? 1 : 0;
                }
            }
        } else {
            return 2;
        }
    }*/

    //Update the Journey Status with drop location
    public function update_journey_statuswith_drop($id, $msg_status, $driver_reply, $travel_status, $drop_latitude, $drop_longitude, $drop_location, $drop_time, $total_distance, $waiting_hours, $tax,$driver_app_version,$usedAmount,$waiting_fare_hour,$fare_per_minute)
    {
        $set_query = array(
            'msg_status' => $msg_status,
            'driver_reply' => $driver_reply,
            'travel_status' => (int)$travel_status,
            'drop_latitude' => (double)$drop_latitude,
            'drop_longitude' => (double)$drop_longitude,
            'drop_location' => $drop_location,
            'drop_time' => Commonfunction::MongoDate(strtotime($drop_time)),
            'waitingtime' => $waiting_hours,
			'driver_app_version'=>$driver_app_version,
			'used_wallet_amount' => (double)$usedAmount,
			'waiting_fare_hour' => $waiting_fare_hour,
			'fare_per_minute' => $fare_per_minute,
            'company_tax' => $tax
        );
		
        $result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id' => (int)$id),array( '$set'=> $set_query ),array('upsert'=>false));
        //return (empty($res['err']))?1:0;
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    //Update the Journey Status with out drop location
    public function update_journey_status($id, $msg_status, $driver_reply, $travel_status)
    {
        $set_query = array(
            'msg_status' => $msg_status,
            'driver_reply' => $driver_reply,
            'travel_status' => (int)$travel_status
        );
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id' => (int)$id),array( '$set'=> $set_query ),array('upsert'=>false));
		return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    public function get_trip_detail($passengerlog_id = "", $passenger_id = "")
    {
		$match_query = $result = array();
		$match_query['_id'] = (int)$passengerlog_id;
		$unwind_split = array();
		if($passenger_id!=""){
			$match_query['split_details.friends_p_id'] = (int)$passenger_id;
			$unwind_split = array(array('$unwind' => '$split_details'));
		}
			
		$args1 = array(	
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'company_id',
					'foreignField'=>'_id',
					'as'=>'company'
				)
			),
			array('$unwind' =>  array( 'path' =>  '$company', 'preserveNullAndEmptyArrays' =>  true)),		
			array(
				'$lookup' => array(
					'from' => MDB_PASSENGERS,
					'localField' => 'passengers_id',
					'foreignField'=>'_id',
					'as'=>'passengers'
				)
			),
			array('$unwind' =>  array( 'path' =>  '$passengers', 'preserveNullAndEmptyArrays' =>  true)),
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'driver_id',
					'foreignField'=>'_id',
					'as'=>'people'
				)
			),
			array('$unwind' =>  array( 'path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true)),
			array(
				'$lookup' => array(
					'from' => MDB_DRIVER_INFO,
					'localField' => 'driver_id',
					'foreignField'=>'_id',
					'as'=>'ddinfo'
				)
			),
			array('$unwind' =>  array( 'path' =>  '$ddinfo', 'preserveNullAndEmptyArrays' =>  true)),
			array(
				'$lookup' => array(
					'from' => MDB_TAXI,
					'localField' => 'taxi_id',
					'foreignField'=>'_id',
					'as'=>'taxi'
				)
			),
			array('$unwind' =>  array( 'path' =>  '$taxi', 'preserveNullAndEmptyArrays' =>  true)),
			array(
				'$lookup' => array(
					'from' => MDB_MOTOR_MODEL,
					'localField' => 'taxi.taxi_model',
					'foreignField'=>'_id',
					'as'=>'mmodel'
				)
			),
			array('$unwind' =>  array( 'path' =>  '$mmodel', 'preserveNullAndEmptyArrays' =>  true)));
		//$args1 = array_merge($args1, $modelfare_arr);	
		$args2 = array(
			array(
				'$lookup' => array(
					'from' => MDB_LOCATION_HISTORY,
					'localField' => '_id',
					'foreignField'=>'trip_id',
					'as'=>'dlh'
				)
			),
			array('$unwind' =>  array( 'path' =>  '$dlh', 'preserveNullAndEmptyArrays' =>  true)),
			array(
				'$lookup' => array(
					'from' => MDB_TRANSACTION,
					'localField' => '_id',
					'foreignField'=>'passengers_log_id',
					'as'=>'trans'
				)
			),
			array('$unwind' =>  array( 'path' =>  '$trans', 'preserveNullAndEmptyArrays' =>  true)),
			array('$match' => $match_query),						
			array('$project' => array(
					'passengers_log_id' => '$_id',
					'company_id' => '$company_id',
					'passengers_id' => '$passengers_id',
					'current_location' => '$current_location',
					'drop_location' =>  array( '$ifNull' =>  array('$drop_location',0)),
					'no_passengers' => '$no_passengers',
					'pickup_time' => '$pickup_time',
					'actual_pickup_time' => '$actual_pickup_time',
					'drop_time' => '$drop_time',
					'rating' => '$rating',
					'comments' => '$comments',
					'no_passengers' => '$no_passengers',
					'notes_driver' => '$notes_driver',
					'is_split_trip' => '$is_split_trip',
					'driver_name' => '$people.name',
					'driver_image' => '$people.profile_picture',
					'driver_id' => '$people._id',
					'taxi_no' => '$taxi.taxi_no',
					'taxi_speed' => '$mmodel.taxi_speed',
					'taxi_min_speed' => '$mmodel.taxi_min_speed',
					'taxi_min_fare' => '$mmodel.min_fare',
					//'waiting_fare_hour' => '$waiting_time',
					//'fare_per_minute' => '$minutes_fare',
					'waiting_fare_hour' => '$waiting_fare_hour',
					'fare_per_minute' => '$fare_per_minute',
					'taxi_id' => '$taxi._id',
					'travel_status' => '$travel_status',
					'driver_reply' =>  '$driver_reply',
					'search_city' =>  '$city_id' ,
					'pickup_location' => '$current_location',
					'pickup_latitude' => '$pickup_latitude',
					'pickup_longitude' => '$pickup_longitude',
					'drop_location' => '$drop_location',
					'drop_latitude' => '$drop_latitude',
					'drop_longitude' => '$drop_longitude',
					'time_to_reach_passen' => '$time_to_reach_passen',
					'notification_status' => '$notification_status',
					'used_wallet_amount' => '$used_wallet_amount',
					'bookby' => '$bookby',
					'driver_twilio_number' =>  array( '$ifNull' =>  array('$ddinfo.driver_twilio_number','')),
					'driver_phone' =>  array( '$concat' =>  array( '$people.country_code', '$people.phone' ) ),
					'passenger_name' =>  array( '$ifNull' =>  array('$passengers.name','')),
					'passenger_phone' =>  array( '$concat' =>  array( '$passengers.country_code', '$passengers.phone' ) ),
					'passenger_image' => '$passengers.profile_image',
					'wallet_amount' => '$passengers.wallet_amount',
					'job_ref' =>  array( '$ifNull' =>  array('$trans._id',0)),
					'payment_type' =>  array( '$ifNull' =>  array('$trans.payment_type',0)),
					'actual_paid_amount' =>  array( '$ifNull' =>  array('$trans.amt',0)),
					'actual_distance' =>  array( '$ifNull' =>  array('$trans.distance',0)),
					'amt'  =>  array('$ifNull'  =>  array(array( '$sum'  => array('$trans.amt', '$used_wallet_amount') ), 0)),
					'trans_waiting_time' => '$trans.waiting_time', 
					'waiting_time' => '$waitingtime', 
					'distance' => '$distance',
					'metric' =>  array( '$ifNull' =>  array('$trans.distance_unit','0')),
					'company_metric' =>  array( '$ifNull' =>  array('$company.companyinfo.default_unit','0')),
					'waiting_cost' =>  array( '$ifNull' =>  array('$trans.waiting_cost',0)),
					'tripfare' =>  array( '$ifNull' =>  array('$trans.tripfare',0)),
					'minutes_fare' =>  array( '$ifNull' =>  array('$trans.minutes_fare',0)),
					'trip_minutes' =>  array( '$ifNull' =>  array('$trans.trip_minutes',0)),
					'promocode_fare' =>  array( '$ifNull' =>  array('$trans.promo_discount_fare',0)),
					'tax_percentage' =>  array( '$ifNull' =>  array('$trans.tax_percentage',0)),
					'tax_fare' =>  array( '$ifNull' =>  array('$trans.company_tax',0)),
					'eveningfare' =>  array( '$ifNull' =>  array('$trans.eveningfare',0)),
					'nightfare' =>  array( '$ifNull' =>  array('$trans.nightfare',0)),
					'fare_calculation_type' =>  array( '$ifNull' =>  array('$trans.fare_calculation_type',3)),
					'active_record' =>  array( '$ifNull' =>  array('$dlh.loc',0)),
					'distance_fare'  =>  array('$ifNull'  => array(array('$subtract'  => array('$trans.tripfare', '$trans.minutes_fare') ), 0)),
				)
			)			
		);
		$arguments = array_merge($args1, $args2);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		if(!empty($unwind_split)){
			$arguments = array_merge( $unwind_split,$arguments );
		}

		if(!empty($res['result'])){
			
			$temp_arr = $res['result'][0];
			//print_r($res);exit;
			$temp_arr['taxi_min_speed'] = isset($temp_arr['taxi_min_speed']) ? $temp_arr['taxi_min_speed']:0;
			$temp_arr['min_fare'] = isset($temp_arr['taxi_min_fare']) ? $temp_arr['taxi_min_fare']:0;
			$temp_arr['no_passengers'] = isset($temp_arr['no_passengers']) ? $temp_arr['no_passengers']:0;
			$temp_arr['notes_driver'] = isset($temp_arr['notes_driver']) ? $temp_arr['notes_driver']:'';
			$temp_arr['passengers_id'] = isset($temp_arr['passengers_id']) ? $temp_arr['passengers_id']:'0';
			$temp_arr['taxi_no'] = isset($temp_arr['taxi_no']) ? $temp_arr['taxi_no']:'';
			$temp_arr['is_split_trip'] = isset($temp_arr['is_split_trip']) ? $temp_arr['is_split_trip']:0;
			$temp_arr['travel_status'] = isset($temp_arr['travel_status']) ? $temp_arr['travel_status']:0;
			$temp_arr['driver_reply'] = isset($temp_arr['driver_reply']) ? $temp_arr['driver_reply']:0;
			$temp_arr['time_to_reach_passen'] = isset($temp_arr['time_to_reach_passen']) ? $temp_arr['time_to_reach_passen']:0;
			$temp_arr['passengers_log_id'] = isset($temp_arr['passengers_log_id']) ? (string)$temp_arr['passengers_log_id']:'0';
			$temp_arr['rating'] = isset($temp_arr['rating']) ? $temp_arr['rating']:'0';
			$temp_arr['distance'] = isset($temp_arr['distance']) ? number_format($temp_arr['distance'], 2, '.', '') :'0';
			$temp_arr['distance_fare'] = isset($temp_arr['distance_fare']) ? $temp_arr['distance_fare']:0;
			$temp_arr['driver_name'] = isset($temp_arr['driver_name']) ? $temp_arr['driver_name']:'';
			$temp_arr['driver_image'] = isset($temp_arr['driver_image']) ? $temp_arr['driver_image']:'';
			$temp_arr['used_wallet_amount'] = isset($temp_arr['used_wallet_amount']) ? $temp_arr['used_wallet_amount']:0;
			$temp_arr['taxi_id'] = isset($temp_arr['taxi_id']) ? $temp_arr['taxi_id']:'';
			$temp_arr['wallet_amount'] = !empty($temp_arr['wallet_amount']) ? $temp_arr['wallet_amount'][0]:'';
			$temp_arr['waiting_time'] = isset($temp_arr['waiting_time']) ? $temp_arr['waiting_time']:'';
			if($temp_arr['travel_status'] == 1){
				$temp_arr['waiting_time'] = isset($temp_arr['trans_waiting_time']) ? $temp_arr['trans_waiting_time']:'';
			}
			$temp_arr['taxi_speed'] = isset($temp_arr['taxi_speed']) ? $temp_arr['taxi_speed']:0;		
			
			/*$temp_arr['company_metric'] = isset($temp_arr['company_metric']) ? $temp_arr['company_metric']:0;
			$temp_arr['metric'] = isset($temp_arr['metric']) ? $temp_arr['metric']:0;*/
			# metric functions
			$temp_arr['company_metric'] = isset($temp_arr['company_metric']) ? $temp_arr['company_metric']:0;
			$temp_arr['company_metric'] = ($temp_arr['company_metric'] == 0) ? 'KM' : 'MILES';
			$temp_arr['metric'] = isset($temp_arr['metric']) ? $temp_arr['metric']:0;
			if($temp_arr['metric'] == 0 || $temp_arr['metric'] == ''){
				
				$temp_arr['metric'] = (FARE_SETTINGS == 1) ? UNIT_NAME : $temp_arr['company_metric'];
			} 
			
			$temp_arr['waiting_fare_hour'] = isset($temp_arr['waiting_fare_hour']) ? $temp_arr['waiting_fare_hour']:0;
			$temp_arr['fare_per_minute'] = isset($temp_arr['fare_per_minute']) ? $temp_arr['fare_per_minute']:0;
			$temp_arr['passenger_image'] = isset($temp_arr['passenger_image']) ? $temp_arr['passenger_image']:0;
			$temp_arr['promocode_fare'] = isset($temp_arr['promocode_fare']) ? (string)$temp_arr['promocode_fare']:0;
			# Active record
			$active_record = !empty($temp_arr['active_record']) ? $temp_arr['active_record']['coordinates']:array();				
			$coordinates='';
			if(!empty($active_record)){
				foreach($active_record as $a){
					$lat = '['.$a[1].',';
					$long = $a[0].'],';
					$coordinates .= $lat.$long;
				}
				($coordinates !='') ? $temp_arr['active_record'] = $coordinates : '';
			}	
			$temp_arr['active_record'] = $coordinates;
			$temp_arr['fare'] = isset($temp_arr['fare']) ? $temp_arr['fare']:'0';
			$temp_arr['model_name'] = isset($temp_arr['model_name']) ? $temp_arr['model_name']:'';
			$temp_arr['taxi_no'] = isset($temp_arr['taxi_no']) ? $temp_arr['taxi_no']:'';
			$temp_arr['driver_id'] = isset($temp_arr['driver_id']) ? $temp_arr['driver_id']:'';
			
			$temp_arr['pickup_time'] = isset($temp_arr['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['pickup_time']) : '0000-00-00 00:00:00';
			$temp_arr['drop_time'] = isset($temp_arr['drop_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['drop_time']) : '0000-00-00 00:00:00';
			$temp_arr['actual_pickup_time'] = isset($temp_arr['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['actual_pickup_time']) : '0000-00-00 00:00:00';
			$temp_arr['driver_image'] = isset($temp_arr['driver_image']) ? $temp_arr['driver_image']:'';
			
			$result[] = (object)$temp_arr;
		}
		//print_r($res);exit;
		return $result;
    }
	
    public function get_minimum_speed($taxi_id, $default_companyid)
    {
        $company_id = $default_companyid;
		if (FARE_SETTINGS == 2 && $company_id != "") {			
			$arguments = array(
				array('$unwind' => '$model_fare'),
				array('$lookup' =>
					array(
						'from' => MDB_TAXI,
						'localField' => 'model_fare.model_id',
						'foreignField' => 'taxi_model',
						'as' => 'taxi',
					)	
				),
				array('$unwind' => '$taxi'),
				array('$match' => array('_id' => $taxi_id,'model_fare.fare_status' => $taxi_id,'taxi._id' => $taxi_id)),
				array('$project' => array('taxi_min_speed' => '$model_fare.taxi_min_speed')),
			);
			$result = $this->mongo_db->aggregate(MDB_COMPANY,$arguments);
			return (!empty($result['result'])?$result['result']:array());					
		} else {
			$arguments = array(
				array('$lookup' =>
					array(
						'from' => MDB_TAXI,
						'localField' => '_id',
						'foreignField' => 'taxi_model',
						'as' => 'taxi',
					)	
				),
				array('$unwind' => '$taxi'),
				array('$match' => array('taxi._id' => $taxi_id)),
				array('$project' => array('taxi_min_speed' => '$taxi_min_speed')),
			);
			$result = $this->mongo_db->aggregate(MDB_MOTOR_MODEL,$arguments);
			return (!empty($result['result'])?$result['result']:array());
		}	
    }
	
	/*** Get Passenger get_trip_detail passenger log id ***/
    public function get_request_detail($passenger_id = "",$trip_id="")
    {
		$localtime = gmdate("Y-m-d");
		//$localtime = date("Y-m-d");
		//echo $localtime;exit;		
		$match_array = $result = $temp_arr = array();
		$match_array['travel_status'] = array('$in' => array(2,3,5,6,9,8,1,4));
		$match_array['notification_status'] = array('$ne' => 4);
		//~ $match_array['yearMonthDay'] = $localtime;
		$match_array['friends_p_id'] = (int)$passenger_id;
		$match_array['_id'] = (int)$trip_id;
        $arguments = array(
			array('$unwind' => '$split_details'),
			array('$lookup'=>array(
				'from'=>MDB_PASSENGERS,
				'localField'=>"passengers_id",
				'foreignField'=>"_id",
				 'as'=>"passengers"        
			)),
			array('$unwind'=>'$passengers'),
			array('$lookup'=>array(
				'from'=>MDB_TAXI,
				'localField'=>"taxi_id",
				'foreignField'=>"_id",
				 'as'=>"taxi"
			)),
			array('$unwind'=>'$taxi'),
			array('$lookup'=>array(
				'from'=>MDB_PEOPLE,
				'localField'=>"driver_id",
				'foreignField'=>"_id",
				 'as'=>"people"
			)),
			array('$unwind'=>'$people'),
			array('$lookup'=>array(
				'from'=>MDB_TRANSACTION,
				'localField'=>"_id",
				'foreignField'=>"passengers_log_id",
				'as'=>"trans"
			)),
			//array('$unwind' => array('path' => '$trans', 'preserveNullAndEmptyArrays' => true )),
			array('$project' =>
				array(
					'yearMonthDay' => array('$dateToString' => array('format' => '%Y-%m-%d','date' => '$createdate')),
					'trip_id'=>'$passengers_log_id',
					'primary_passenger'=>'$passengers_id',
					'current_location'=>'$current_location',
					'drop_location'=>'$drop_location',
					'no_passengers'=>'$no_passengers',
					'pickup_time'=>'$pickup_time',
					'actual_pickup_time'=>'$actual_pickup_time',
					'drop_time'=>'$drop_time',
					'rating'=>'$rating',
					'no_passengers'=>'$no_passengers',
					'notes_driver'=>'$notes_driver', 
					'driver_name'=>'$people.name',
					'driver_image'=>'$people.profile_picture',
					'driver_id'=>'$people._id',
					'taxi_no'=>'$taxi.taxi_no',
					'taxi_id'=>'$taxi._id',
					'travel_status'=>'$travel_status',
					'driver_reply'=>'$driver_reply',
					'city_id'=>'$search_city' ,
					'pickup_location'=>'$current_location',
					'pickup_latitude'=>'$pickup_latitude',
					'pickup_longitude'=>'$pickup_longitude',
					'drop_location'=>'$drop_location',
					'drop_latitude'=>'$drop_latitude',
					'drop_longitude'=>'$drop_longitude',
					'time_to_reach_passen'=>'$time_to_reach_passen',
					'notification_status'=>'$notification_status',
					'bookby'=>'$bookby',
					'driver_phone'=>'$phone', 
					'passenger_name'=>'$name', 
					'used_wallet_amount'=>'$used_wallet_amount',
					'passenger_phone'=>'$phone',
					'passenger_image'=>'$profile_image',
					'job_ref' => array('$cond' => array(array('$eq' => array('$trans._id',null)),0,'$trans._id')),
					'payment_type' => array('$cond' => array(array('$eq' => array('$trans.payment_type',null)),0,'$trans.payment_type')),
					'amt' => array('$cond' => array(array('$eq' => array('$trans.amt',null)),0,'$trans.amt')),
					'trans_amt' => array('$cond' => array(array('$eq' => array('$trans.amt',null)),0,'$trans.amt')),
					'friends_p_id'=> '$split_details.friends_p_id',
					'notification_status'=> '$split_details.notification_status',
					'fare_percentage'=> '$split_details.fare_percentage',
					'split_wallet_amount'=> '$split_details.used_wallet_amount',
					'splitTrip'=> '$is_split_trip',
					"tripfare"=> array('$ifNull'=> array('$trans.tripfare',0)),
					"base_fare"=> array('$ifNull'=> array('$trans.base_fare',0)),
					"minutes_fare"=> array('$ifNull'=> array('$trans.minutes_fare',0)),
					"waiting_fare"=> array('$ifNull'=> array('$trans.waiting_cost',0)),
					"nightfare"=> array('$ifNull'=> array('$trans.nightfare',0)),
					"eveningfare"=> array('$ifNull'=> array('$trans.eveningfare',0)),
					"passenger_discount"=> array('$ifNull'=> array('$trans.passenger_discount',0)),
					"promo_discount_fare"=> array('$ifNull'=> array('$trans.promo_discount_fare',0)),
					"company_tax"=> array('$ifNull'=> array('$trans.company_tax',0)),
				)
			),
			array('$match'=>$match_array),
			array('$sort'=>array('_id' => -1)),
			array('$skip'=>0),
			array('$limit'=>1),
		);
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        //~ echo '<pre>';print_r($res);exit;
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr = $r;
				$temp_arr['splitTrip'] = isset($temp_arr['splitTrip']) ? $temp_arr['splitTrip']: '';
				$temp_arr['used_wallet_amount'] = isset($temp_arr['used_wallet_amount']) ? $temp_arr['used_wallet_amount']: '0';
				$temp_arr['pickup_time'] = isset($temp_arr['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['pickup_time']): '';
				$temp_arr['actual_pickup_time'] = isset($temp_arr['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['actual_pickup_time']): '';
				$temp_arr['drop_time'] = isset($temp_arr['drop_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['drop_time']): '';
				$temp_arr['fare_percentage'] = isset($temp_arr['fare_percentage']) ? $temp_arr['fare_percentage']: '';
				$temp_arr['notification_status'] = isset($temp_arr['notification_status']) ? $temp_arr['notification_status']: '';
				$temp_arr['friends_p_id'] = isset($temp_arr['friends_p_id']) ? $temp_arr['friends_p_id']: '';
				$temp_arr['job_ref'] = !empty($temp_arr['job_ref']) ? $temp_arr['job_ref'][0]: '';
				$temp_arr['payment_type'] = !empty($temp_arr['payment_type']) ? $temp_arr['payment_type'][0]: '';
				//$temp_arr['amt'] = isset($temp_arr['amt'][0]) ? $temp_arr['amt'][0]: '';
				$temp_arr['trans_amt'] = isset($temp_arr['trans_amt'][0]) ? $temp_arr['trans_amt'][0]: '0';
				$temp_arr['amt'] = $temp_arr['used_wallet_amount'] + $temp_arr['trans_amt'];
				$temp_arr['tripfare'] = isset($temp_arr['tripfare'][0]) ? $temp_arr['tripfare'][0]: '';
				$temp_arr['base_fare'] = isset($temp_arr['base_fare'][0]) ? $temp_arr['base_fare'][0]: '';
				$temp_arr['minutes_fare'] = isset($temp_arr['minutes_fare'][0]) ? $temp_arr['minutes_fare'][0]: '';
				$temp_arr['waiting_fare'] = isset($temp_arr['waiting_fare'][0]) ? $temp_arr['waiting_fare'][0]: '';
				$temp_arr['nightfare'] = isset($temp_arr['nightfare'][0]) ? $temp_arr['nightfare'][0]: '';
				$temp_arr['eveningfare'] = isset($temp_arr['eveningfare'][0]) ? $temp_arr['eveningfare'][0]: '';
				$temp_arr['company_tax'] = isset($temp_arr['company_tax'][0]) ? $temp_arr['company_tax'][0]: '';
				$temp_arr['passenger_discount'] = isset($temp_arr['passenger_discount'][0]) ? $temp_arr['passenger_discount'][0]: '0';
				$temp_arr['promo_discount_fare'] = isset($temp_arr['promo_discount_fare'][0]) ? $temp_arr['promo_discount_fare'][0]: '0';
				$temp_arr['split_wallet_amount'] = isset($temp_arr['split_wallet_amount']) ? $temp_arr['split_wallet_amount']: '0';
				
				$result[] = (object)$temp_arr;
			}
		}
		
        return $result;
    }
	
	// Get Passenger trips by Fromdate anda to date
	public function get_passenger_current_log_details($company_id, $pagination, $userid = "", $travelstatus = "", $driver_reply = "", $createdate = "", $start = null, $limit = null)
    {
		 if ($company_id == '') {
            if (TIMEZONE) {
                $current_time = convert_timezone('now', TIMEZONE);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
                $date         = $current_date[0] . ' %';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:01';
                $end_time     = date('Y-m-d') . ' 23:59:59';
                $date         = date('Y-m-d %');
            }
        } else {
			$time_arguments = array(array('$match'=>array('_id'=>(int)$company_id)),array('$unwind'=>'$companydetails'),array('$project'=>array('time_zone'=>'$companydetails.time_zone')));            
            $time = $this->mongo_db->aggregate(MDB_COMPANY,$time_arguments);
			$timezone_fetch = $time['result'];
			 if ($timezone_fetch[0]['time_zone'] != '') {
                $current_time = convert_timezone('now', $timezone_fetch[0]['time_zone']);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:00';
                $end_time     = $current_date[0] . ' 23:59:59';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:00';
                $end_time     = date('Y-m-d') . ' 23:59:59';
            }
        }
		
		$match_array = array();
		$match_array['split_details.friends_p_id'] = (int)$userid;
		$match_array['travel_status'] = array('$in' => array(9,2,3));
		$match_array['driver_reply'] = $driver_reply;
		$match_array['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($start_time)));
        $arguments = array(
			array('$unwind' => '$split_details'),
			array('$lookup'=>array(
				'from'=>MDB_PASSENGERS,
				'localField'=>"passengers_id",
				'foreignField'=>"_id",
				 'as'=>"passengers"        
			)),
			array('$unwind'=>'$passengers'),
			array('$lookup'=>array(
				'from'=>MDB_TAXI,
				'localField'=>"taxi_id",
				'foreignField'=>"_id",
				 'as'=>"taxi"        
			)),
			array('$unwind'=>'$taxi'),
			array('$lookup'=>array(
				'from'=>MDB_PEOPLE,
				'localField'=>"driver_id",
				'foreignField'=>"_id",
				 'as'=>"people"        
			)),
			array('$unwind'=>'$people'),
			/*array('$lookup'=>array(
				'from'=>MDB_SPLIT_LOG,
				'localField'=>"_id",
				'foreignField'=>"trip_id",
				 'as'=>"split_log"        
			)),*/
			array('$match'=>$match_array),
			array('$project' =>
				array(
					'passengers_log_id'=>'$_id',
					'pickup_location'=>'$current_location',
					'drop_location'=>'$drop_location',
					'no_passengers'=>'$no_passengers',
					'pickuptime'=>'$pickup_time',
					'rating'=>'$rating',
					'driver_name'=>'$people.name',
					'driver_lastname'=>'$people.lastname',
					'driver_image'=>'$people.photo',
					'driver_id'=>'$people._id',
					'taxi_no'=>'$taxi.taxi_no',
					'taxi_id'=>'$taxi._id',
					'travel_status'=>'$travel_status',
					'city_id'=>'$search_city' ,
					'pickup_location'=>'$current_location',
					'pickup_latitude'=>'$pickup_latitude',
					'pickup_longitude'=>'$pickup_longitude',
					'drop_location'=>'$drop_location',
					'drop_latitude'=>'$drop_latitude',
					'drop_longitude'=>'$drop_longitude',
					'time_to_reach_passen'=>'$time_to_reach_passen',
					'driver_phone'=>'$people.phone',
					'passenger_name'=>'$passengers.name',
					'passenger_lastname'=>'$passengers.lastname'
				)
			),
			array('$sort' => array('passengers_log_id' => -1)),
		);
		
        if ($pagination == 1) {
			$page_field = array(array('$skip' => (int)$start),array('$limit' => (int)$limit));
			$arguments 	=  array_merge($arguments,$page_field);
        }
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        return (!empty($result['result'])?$result['result']:array()); 
    }
    /** Get passengers Current booked details for passenger with Upcoming and ongoing trip details**/	
	public function get_driver_current_log_details($company_id, $pagination, $userid = "", $travelstatus = "", $driver_reply = "", $createdate = "", $start = null, $limit = null)
    {
    if ($company_id == '') {
            if (TIMEZONE) {
                $current_time = convert_timezone('now', TIMEZONE);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
                $date         = $current_date[0] . ' %';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:01';
                $end_time     = date('Y-m-d') . ' 23:59:59';
                $date         = date('Y-m-d %');
            }
        } else {
			$time_arguments = array(array('$match'=>array('_id'=>(int)$company_id)),array('$unwind'=>'$companydetails'),array('$project'=>array('time_zone'=>'$companydetails.time_zone')));            
            $time = $this->mongo_db->aggregate(MDB_COMPANY,$time_arguments);
			$timezone_fetch = $time['result'];
			 if ($timezone_fetch[0]['time_zone'] != '') {
                $current_time = convert_timezone('now', $timezone_fetch[0]['time_zone']);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:00';
                $end_time     = $current_date[0] . ' 23:59:59';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:00';
                $end_time     = date('Y-m-d') . ' 23:59:59';
            }
        }

		$match_array = array();
		$match_array['driver_id'] = (int)$userid;
		$match_array['bookby'] = BOOK_BY_CONTROLLER;
		$match_array['travel_status'] = array('$in' => array(9,3));
		$match_array['driver_reply'] = '';		
		$match_array['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($start_time)));

        $arguments = array(
			array('$lookup'=>array(
				'from'=>MDB_PASSENGERS,
				'localField'=>"passengers_id",
				'foreignField'=>"_id",
				 'as'=>"passengers"
			)),
			array('$unwind'=>'$passengers'),
			array('$lookup'=>array(
				'from'=>MDB_PEOPLE,
				'localField'=>"driver_id",
				'foreignField'=>"_id",
				 'as'=>"people"
			)),
			array('$unwind'=>'$people'),
			array('$lookup'=>array(
				'from'=>MDB_TAXI,
				'localField'=>"taxi_id",
				'foreignField'=>"_id",
				 'as'=>"taxi"
			)),
			array('$match'=>$match_array),
			array('$project' =>
				array(
					'passengers_log_id'=>'$_id',
					'pickup_location'=>'$current_location',
					'drop_location'=>'$drop_location',
					'no_passengers'=>'$no_passengers',
					'pickup_time' => array('$cond' => array(array('$eq' => array('$actual_pickup_time',Commonfunction::MongoDate(strtotime('1969-12-31 00:00:00')))),'$pickup_time','$actual_pickup_time')),
					'rating'=>'$rating', 
					'driver_name'=>'$people.name',
					'driver_lastname'=>'$people.lastname',
					'driver_image'=>'$people.photo',
					'driver_id'=>'$people._id',
					'taxi_no'=>'$taxi.taxi_no',
					'taxi_id'=>'$taxi._id',
					'travel_status'=>'$travel_status',
					'city_id'=>'$search_city',
					'pickup_location'=>'$current_location',
					'pickup_latitude'=>'$pickup_latitude',
					'pickup_longitude'=>'$pickup_longitude',
					'drop_location'=>'$drop_location',
					'drop_latitude'=>'$drop_latitude',
					'drop_longitude'=>'$drop_longitude',
					'time_to_reach_passen'=>'$time_to_reach_passen',
					'driver_phone'=>'$people.phone',
					'passenger_name'=>'$passengers.name',
					'passenger_phone'=>'$passengers.phone',
					'passenger_lastname'=>'$passengers.lastname'
				)
			),
			array('$sort' => array('pickup_time' => -1)),
		);
		
        if ($pagination == 1) {
			$page_field = array(array('$skip' => (int)$start),array('$limit' => (int)$limit));
			$arguments 	=  array_merge($arguments,$page_field);
        }
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        return (!empty($result['result'])?$result['result']:array());
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
                                                        'pre_transaction_id'=>'$pre_transaction_id',
                                                        'pre_transaction_amount'=>'$pre_transaction_amount',
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
							"passenger_country_code"=>'$passengers.country_code',
							"cancellation_nfree"=>'$company.companyinfo.cancellation_fare',
							"fare_calculation_type"=>'$company.companyinfo.fare_calculation_type',
							"company_tax"=>'$company.companyinfo.company_tax',
							'drop_location' => array('$ifNull'=>array('$drop_location',0)),
							'droplocation' => array('$ifNull'=>array('$drop_location',0)),
							'transaction_id' => array('$ifNull'=>array('$transaction._id',0)),
							'brand_type' => '$company.companyinfo.company_brand_type',
							'default_unit' => '$company.companyinfo.default_unit'
						)),
						array('$match'=>array(
							'_id'=>(int)$passengerlog_id
						)),
					);
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
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
			$temp_arr['company_tax'] = (isset($temp_arr['company_tax']) && !empty($temp_arr['company_tax']))?$temp_arr['company_tax']:0;
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
			$temp_arr['default_unit'] = isset($temp_arr['default_unit']) ? $temp_arr['default_unit'] : DEFAULT_UNIT;		
			$temp_arr['fare_calculation_type'] = isset($temp_arr['fare_calculation_type'])?$temp_arr['fare_calculation_type']:FARE_CALCULATION_TYPE;
                        $temp_arr['pre_transaction_id'] = isset($temp_arr['pre_transaction_id'])?$temp_arr['pre_transaction_id']:'';
                        $temp_arr['pre_transaction_amount'] = isset($temp_arr['pre_transaction_amount'])?$temp_arr['pre_transaction_amount']:'';
			
			
			$result[] = (object)$temp_arr;
		}
        return $result;
    }
	
    public function get_passenger_log_detail_reply($passengerlog_id = "")
    {       
		$result = $temp_arr = array();
        $arguments = array(array('$lookup'=>array(
							'from'=> MDB_COMPANY,
							'localField'=>"company_id",
							'foreignField'=>"_id",
							 'as'=>"company"        
						)),
						array('$unwind'=>'$company'),
						array('$lookup'=>array(
							'from'=> MDB_PASSENGERS,
							'localField'=>"passengers_id",
							'foreignField'=>"_id",
							 'as'=>"passengers"        
						)),
						array('$unwind'=>'$passengers'),
						array('$lookup'=>array(
							'from'=> MDB_PEOPLE,
							'localField'=>"driver_id",
							'foreignField'=>"_id",
							 'as'=>"people"        
						)),
						array('$unwind'=>'$people'),
						array('$match'=>array(
							'_id'=> (int)$passengerlog_id
						)),
						array('$project' => array(
							'trip_id' => '$_id',
							'phone' => '$people.phone',
							'booking_key'  =>  '$booking_key',
							'passengers_id'  =>  '$passengers_id',
							'taxi_id'  =>  '$taxi_id',
							'driver_id'  =>  '$driver_id',
							'company_id'  =>  '$company_id',
							'current_location'  =>  '$current_location',
							'pickup_latitude'  =>  '$pickup_latitude',
							'pickup_longitude'  =>  '$pickup_longitude',
							'drop_location'  =>  '$drop_location',
							'drop_latitude'  =>  '$drop_latitude',
							'drop_longitude'  =>  '$drop_longitude',
							'no_passengers'  =>  '$no_passengers',
							'approx_distance'  =>  '$approx_distance',
							'approx_duration'  =>  '$approx_duration',
							'approx_fare'  =>  '$approx_fare',
							'time_to_reach_passen'  =>  '$time_to_reach_passen',
							'pickup_time'  =>  '$pickup_time',
							'actual_pickup_time'  =>  '$actual_pickup_time',
							'drop_time'  =>  '$drop_time',
							'account_id'  =>  '$account_id',
							'accgroup_id'  =>  '$accgroup_id',
							'pickupdrop'  =>  '$pickupdrop',
							'rating'  =>  '$rating',
							'comments'  =>  '$comments',
							'travel_status'  =>  '$travel_status',
							'driver_reply'  =>  '$driver_reply',
							'msg_status'  =>  '$msg_status',
							'createdate'  =>  '$createdate',
							'booking_from'  =>  '$booking_from',
							'search_city'  =>  '$search_city',
							'sub_logid'  =>  '$sub_logid',
							'passengers_log_id'  =>  '$_id',
							'bookby'  =>  '$bookby',
							'booking_from_cid'  =>  '$booking_from_cid',
							'company_tax'  =>  '$company_tax',
							'distance'  =>  '$distance',
							'driver_name'  =>  '$people.name',
							'discount'  =>  '$passengers.passenger_discount',
							'profile_image'  =>  '$passengers.profile_image',
							'notes'  =>  '$notes_driver',
							'driver_phone'  =>  '$people.phone',
							'driver_photo'  =>  '$people.profile_picture',
							'driver_device_id'  =>  '$people.device_id',
							'driver_device_token'  =>  '$people.device_token',
							'driver_device_type'  =>  '$people.device_type',
							'passenger_discount'  =>  '$passengers.discount ',
							'passenger_device_id'  =>  '$passengers.device_id',
							'passenger_device_token'  =>  '$passengers.device_token',
							'referred_by'  =>  '$passengers.referred_by',
							'referrer_earned'  =>  '$passengers.referrer_earned',
							'passenger_device_type'  =>  '$passengers.device_type',
							'passenger_salutation'  =>  '$passengers.salutation', 
							'passenger_name'  =>  '$passengers.name',
							'passenger_lastname'  =>  '$passenger_lastname',
							'passenger_email'  =>  '$passengers.email',
							'passenger_phone'  =>  '$passengers.phone'											
						))					
					);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		if(!empty($res['result'])){
			$temp_arr = $res['result'][0];
			
			$temp_arr['pickup_time'] = isset($temp_arr['pickup_time'])?Commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['pickup_time']):"";
			$temp_arr['createdate'] = isset($temp_arr['createdate'])?Commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['createdate']):"";
			
			$result[] = (object)$temp_arr;
		}
		
        return $result;
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
			$city_model_fare = isset($city1_result[0]['city_model_fare']) ? $city1_result[0]['city_model_fare']:0;
			
			/*$city1_result = (isset($city1['result'])?$city1['result']:'');
            if (count($city1_result) > 0) {
                $city_model_fare = isset($city1_result[0]['city_model_fare']) ? $city1_result[0]['city_model_fare']:0;
            } */
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
    /*** Get Passenger Profile details using passenger log id ***/
    public function get_passenger_cancel_farebyid($passenger_id = "")
    {
		$match = array('passengers_id' => (int)$passenger_id,
					   'travel_status' => array('$in' => array(2,3))
					   );
		$search  = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,$match,array('search_city'));
		$search_city = (!empty($search)) ? $search['search_city'] : 0;
		if ($search_city != 0) {
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
        //$city_model_fare=1;
        $model_base_query = $this->mongo_db->aggregate(MDB_CSC,$city_arg);
        $result_fare = (!empty($model_base_query['result'])?$model_base_query['result']:array());        
        $city_model_fare = (!empty($result_fare[0]['city_model_fare']) ? $result_fare[0]['city_model_fare'] : 0);
		$args = array(
					array('$lookup' =>array('from' => MDB_TAXI,
										  'localField' => 'taxi_id',
										  'foreignField' => '_id',
										  'as' => 'taxi')),
					array('$unwind' => '$taxi'),
					array('$lookup' =>array('from' => MDB_MOTOR_MODEL,
										  'localField' => 'taxi.taxi_model',
										  'foreignField' => '_id',
										  'as' => 'model')),
					array('$unwind' => '$model'),
					array('$match' => $match),
					array('$project' => array('cancellation_fare' =>array('$add' => array('$model.cancellation_fare',
																	array('$divide' => array($city_model_fare,100)))))),
					array('$sort' => array('_id' => -1))					  
				);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		return (!empty($result['result']) ? $result['result'][0]['cancellation_fare'] : 0);
    }
    /*** Get Passenger Profile details using Driver id ***/
    public function get_driver_log_details($driver_id = "", $company_id)
    {
        if ($company_id == '') {
            if (TIMEZONE) {
                $current_time = convert_timezone('now', TIMEZONE);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
                $date         = $current_date[0] . ' %';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:01';
                $end_time     = date('Y-m-d') . ' 23:59:59';
                $date         = date('Y-m-d %');
            }
        } else {
            $result = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$company_id),array('companydetails.time_zone'));
            if (!empty($result)) {
				$time_zone = (isset($result['companydetails']['time_zone'])?$result['companydetails']['time_zone']:"");
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
		$match = $result = array();
		$match['driver_id'] = (int)$driver_id;
		$match['driver_reply'] = "A";
		$match['pickup_time'] = array('$gte'=> Commonfunction::MongoDate(strtotime($start_time)));
		$match['travel_status'] = array('$in' => array(9,5,3,2));
		if ($company_id != "" && $company_id != 0) {
			$match['company_id'] = (int)$company_id;
		}		
		$options=[
				'projection'=>[
				   '_id'=>1,                               
				   'travel_status'=>1                            
					]
				];
		$res = $this->mongo_db->find(MDB_PASSENGERS_LOGS,$match,array('_id','travel_status'));
		//print_r($res); exit;
		$res = $res;
		$res = commonfunction::change_key($res);
		if(!empty($res)){
			
			$temp_arr['passengers_log_id'] = (string)$res[0]['_id'];
			$temp_arr['travel_status'] = $res[0]['travel_status'];
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
    // Update Driver Shift Status
    public function update_driver_shift_status($id, $shift_status, $stat = null)
    {
        $set_query    = array(
            'shift_status' => $shift_status,
            'status' => 'F'
        );
        $result = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id' => (int)$id),array('$set' => $set_query),array('upsert'=>false));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }

    public function get_city_id($cityname)
    {
		$city_id     = "";
		$arguments = array(array('$unwind'=>'$stateinfo'),
							array('$unwind'=>'$stateinfo.cityinfo'),
							array('$match'=>array('stateinfo.cityinfo.city_name' =>  Commonfunction::MongoRegex("/$cityname/i"))),
							array('$project'=>array('city_id'=>'$stateinfo.cityinfo.city_id')),
							array('$limit' => 1)
					);		
		$city_query = $this->mongo_db->aggregate(MDB_CSC,$arguments);
		$city = (!empty($city_query['result']))?$city_query['result']:array();
        if (count($city) > 0) {
            $city_id = $city[0]['city_id'];
        } else {
            $arguments = array(array('$unwind'=>'$stateinfo'),
							   array('$unwind'=>'$stateinfo.cityinfo'),
								array('$match'=>array(
													  'default'=>1,
													  'stateinfo.default'=>1,
													  'stateinfo.cityinfo.default'=>1
												 )),
								 array('$project'=>array('city_id'=>'$stateinfo.cityinfo.city_id'))
						);
			$city_query = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			$city = (!empty($city_query['result'])?$city_query['result']:array());
			$city_id = $city[0]['city_id'];
        }
        return $city_id;
    }
   
	
	public function get_passenger_details_phone($array, $company_id)
    {
		$match_query = array();
		$match_query['phone'] = $array['phone_no'];
		$options = array('projection' => 
							array(
								'email' =>1,
								'name' =>1
							));
		if ($array['user_type'] == 'P') {
			if($company_id!="" && $company_id !=0){
				$match_query['passenger_cid'] = (int)$company_id;
			}
			//$project = array('email','name','activation_key','phone');
			$options['projection']['activation_key'] =1;
			$options['projection']['phone'] =1;
			$table = MDB_PASSENGERS;
		}else{
			//$project = array('email','name');
			$table = MDB_PEOPLE;
		}
		$result = $this->mongo_db->find($table,$match_query,$options);
		//print_r($result);exit;
		$res = Commonfunction::change_key($result);
		return (!empty($res)) ? $res:array();
    }
	
    public function get_sublogid($log_id)
    {
		$res = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$log_id),array('sub_logid'));
        $result = (!empty($res)?$res:array());
		return (isset($result['sub_logid'])?$result['sub_logid']:0);
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
    
    public function update_driver_phone($update_array, $id, $default_companyid)
    {
        $update = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id' => (int)$id),array('$set'=>$update_array),array('upsert'=>false));
        return (empty($update->getwriteErrors())) ? 1 : 0;
    }
    //Get Driver Current Status if he is break,Avtive,Free
    public function get_driver_current_status($id, $company_id = '')
    {
		$result = $temp_arr = array();
		$lat = $long = '';
		$res = $this->mongo_db->findOne(MDB_DRIVER_INFO,array('_id'=>(int)$id),array('status', 'loc.coordinates'));
		//print_r($res);exit;
		if(!empty($res)){
			
			$temp_arr['status'] = $res['status'];
			$coordinates = isset($res['loc']['coordinates']) ? $res['loc']['coordinates']: array();
			if(!empty($coordinates)){
				$lat = $coordinates[1];
				$long = $coordinates[0];
			}		
			$temp_arr['latitude'] = $lat;
			$temp_arr['longitude'] = $long;	
			$result[] = (object)$temp_arr;
		}
		return $result;
    }
    public function getTaxiforDriver($id, $company_id = '')
    {
		$result = array();
        if ($company_id == '') {
            if (TIMEZONE) {
                $current_time = convert_timezone('now', TIMEZONE);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
                $date         = $current_date[0] . ' %';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:01';
                $end_time     = date('Y-m-d') . ' 23:59:59';
                $date         = date('Y-m-d %');
            }
        } else {
            $result = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$company_id),array('companydetails.time_zone'));
            if (!empty($result)) {
				$time_zone = (isset($result['companydetails']['time_zone'])?$result['companydetails']['time_zone']:"");
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
		//$end_time = '2015-05-30 00:00:00';
        $match_query = array();
        if ($company_id != '') {
			$match_query['mapping_companyid'] = (int)$company_id;
        }
        $match_query["mapping_driverid"] = (int)$id;
		$match_query['mapping_startdate'] = array('$lte'=> Commonfunction::MongoDate(strtotime($current_time)));
		$match_query['mapping_enddate'] = array('$gte'=> Commonfunction::MongoDate(strtotime($end_time)));
		$match_query["mapping_status"] = 'A';
		$result = array();
		
        $res = $this->mongo_db->findOne(MDB_TAXI_DRIVER_MAPPING,$match_query,array('mapping_taxiid'));
		
		if(!empty($res)){
			$result[] = $res;
		}
        return $result;
    }
    public function get_assignedtaxi_list($driver_id = '', $company_id = '')
    {
        if ($company_id == '') {
            if (TIMEZONE) {
                $current_time = convert_timezone('now', TIMEZONE);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
                $date         = $current_date[0] . ' %';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:01';
                $end_time     = date('Y-m-d') . ' 23:59:59';
                $date         = date('Y-m-d %');
            }
        } else {            
            $res = $this->mongo_db->findOne(MDB_COMPANY, array('_id' => (int)$company_id), array('companydetails.time_zone'));
            if (isset($timezone_fetch['companydetails']['time_zone'])) {
                $current_time = convert_timezone('now', $timezone_fetch[0]['time_zone']);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:01';
                $end_time     = date('Y-m-d') . ' 23:59:59';
            }
        }
		//$start_time   = '2015-04-20 00:00:01';
		//$end_time     = '2015-04-20 23:59:59';
		$match = array('mapping_driverid'=>(int)$driver_id,
						'mapping_startdate'=>array('$lte' => Commonfunction::MongoDate(strtotime($current_time))),
						'mapping_enddate'=>array('$gte' => Commonfunction::MongoDate(strtotime($current_time))));
		if ($company_id != '' && $company_id != 0) {
            $match['mapping_companyid'] = (int)$company_id;
        }
		$args = array(
					array('$lookup' => array('from' => MDB_TAXI,
											'localField' => 'mapping_taxiid',
											'foreignField' => '_id',
											'as' => 'taxi')),
					array('$lookup' => array('from' => MDB_COMPANY,
											'localField' => 'mapping_companyid',
											'foreignField' => '_id',
											'as' => 'companyinfo')),
					array('$lookup' => array('from' => MDB_PEOPLE,
											'localField' => 'mapping_driverid',
											'foreignField' => '_id',
											'as' => 'people')),
					array('$lookup' => array('from' => MDB_CSC,
											'localField' => 'mapping_countryid',
											'foreignField' => '_id',
											'localField' => 'mapping_stateid',
											'foreignField' => 'stateinfo.state_id',
											'localField' => 'mapping_cityid',
											'foreignField' => 'stateinfo.cityinfo.city_id',
											'as' => 'csc')),
					array('$sort'=>array('mapping_startdate'=>1)),
					array('$match'=>$match),
					array('$project' => array('taxi_id' => '$_id'))
				);
		$result = $this->mongo_db->aggregate(MDB_TAXI_DRIVER_MAPPING,$args);
		//print_r($result);exit;
		return (!empty($result['result'])) ? $result['result'] : array(); 
    }
    //Function used to get total driven details
    public function get_time_driven($id, $msg_status, $driver_reply = null, $travel_status = null)
    {
        $date         = date("Y-m-d");
        $current_time = convert_timezone('now', TIMEZONE);
        $current_date = explode(' ', $current_time);
        $start_time   = $current_date[0] . ' 00:00:01';
        $end_time     = $current_date[0] . ' 23:59:59';
        //$start_time = '2014-01-09 00:00:01';
        //$end_time = '2014-01-09 23:59:59';
		$arguments = array(
				array('$lookup'=>array(
					'from'=>MDB_PASSENGERS,
					'localField'=>"passengers_id",
					'foreignField'=>"_id",
					'as'=>"passengers")),
				array('$unwind'=>'$passengers'),
				array('$match'=>array(
					'createdate'=>array('$gte'=> Commonfunction::MongoDate(strtotime($start_time)),
										'$lte'=> Commonfunction::MongoDate(strtotime($end_time))),
					'driver_id'=>(int)$id,
					'msg_status'=> $msg_status,
					'driver_reply'=> $driver_reply,
					"travel_status"=> (int)$travel_status
				)),
				array('$sort' => array("_id"=>-1)),
				array('$project'=>array(
					'actual_pickup_time'=>'$actual_pickup_time',
					'drop_time'=>'$drop_time',
				))
		);
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        $result = $res['result'];
        if (count($result) > 0) {
            $actual_pickup_time = '';
            $drop_time          = '';
            $hours              = '';
            $minutes            = '';
            $seconds            = '';
            $date_difference    = "";
            $total_differnce    = "";
            foreach ($result as $get_details) {
                $actual_pickup_time = strtotime((isset($get_details[0]['actual_pickup_time'])?$get_details[0]['actual_pickup_time']:''));
                $drop_time          = strtotime((isset($get_details[0]['drop_time'])?$get_details[0]['drop_time']:''));
                $date_difference    = abs($drop_time - $actual_pickup_time);
                $total_differnce += $date_difference;
            }
            //$date_difference = $drop_time - $actual_pickup_time;
            $hours += floor((($total_differnce % 604800) % 86400) / 3600);
            $minutes += floor(((($total_differnce % 604800) % 86400) % 3600) / 60);
            $seconds += floor((((($total_differnce % 604800) % 86400) % 3600) % 60));
            $time_result = $minutes . ':' . $seconds;
        } else {
            $time_result = "00:00";
        }
        return $time_result;
    }
    public function api_companystatus($user_id)
    {
		$args = array(array('$lookup' => array('from' => MDB_COMPANY,
											   'localField' => 'company_id',
											   'foreignField' => '_id',
											   'as' => 'company')),
					  array('$unwind' => '$company'),
					  array('$unwind' => '$company.companydetails'),
					  array('$match' => array('_id' => (int)$user_id)),
					  array('$project' => array('company_status' => '$company.companydetails.company_status'))
					);
		$check = $this->mongo_db->aggregate(MDB_PEOPLE,$args);
		$result = (!empty($check['result'])) ? $check['result'] : array();
		if(count($result)>0){
			return $result[0]['company_status'];
		}else{
			return 'A';
		}
    }
    /************************ TDispatch **************************/

   
    public function check_used_limit($passengers_id)
    {
		$args = array(
			array('$lookup'=>array(
				'from'=>MDB_PASSENGERS,
				'localField'=>"passengers_id",
				'foreignField'=>"_id",
				 'as'=>"passengers"        
			)),
			array('$unwind' => array('path' => '$passengers', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup'=>array(
				'from'=>MDB_TRANSACTION,
				'localField'=>"_id",
				'foreignField'=>"passengers_log_id",
				 'as'=>"trans"
			)),
			array('$unwind' => array('path' => '$trans', 'preserveNullAndEmptyArrays' => true )),
			array('$match' => array('passengers_id' => (int)$passengers_id) ),
			array('$group' =>array('_id' => null,
					'total_used_limit' => array('$sum'=>'$trans.credits_used')
				)
			),
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		
		return (!empty($result['result']) ? $result['result']: array());
    }
    /******************** Get default payment gateway of Specific company *********************/
    public function company_payment_details($cid)
    {
		$arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'company_id',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			array(
				'$unwind' => '$company'
			),
			array('$match' =>
				array(
					'company_id' => (int)$cid,
					'default_payment_gateway' => 1,
				)
			),
			array('$project' =>
				array('_id'=>0,
					'payment_type'=>'$payment_gateway_id',
					'payment_gateway_username'=>'$payment_gateway_username',
					'payment_gateway_password'=>'$payment_gateway_password',
					'payment_gateway_key'=>'$payment_gateway_signature',
                                        'live_payment_gateway_username'=>'$live_payment_gateway_username',
					'live_payment_gateway_password'=>'$live_payment_gateway_password',
					'live_payment_gateway_key'=>'$live_payment_gateway_signature',
					//'gateway_currency_format'=>'$company.companyinfo.company_currency_format',
					'payment_method'=>'$payment_method'
				)
			)
		);
		$result = $this->mongo_db->aggregate(MDB_PAYMENT_GATEWAYS,$arguments);
		return (!empty($result['result']) && isset($result['result']))?$result['result']:array();
    }
    /******************** Get default payment gateway of Specific company *********************/
    public function payment_gateway_details()
    {	
		$result = array();
		$arguments = array(
			array('$match' =>
				array(
					'company_id' => 0,
					'default_payment_gateway' => 1,
				)
			),
			array('$project' =>
				array(
					'payment_type' => '$payment_gateway_id',
					'payment_gateway_username' => '$payment_gateway_username',
					'payment_gateway_password' => '$payment_gateway_password',
					'payment_gateway_key' => '$payment_gateway_signature',
					//'gateway_currency_format' => '$currency_code',
					'payment_method' => '$payment_method',
				)
			)
		);
	
		$res = $this->mongo_db->aggregate(MDB_PAYMENT_GATEWAYS,$arguments);
		if(!empty($res['result'])){
			
			$result = $res['result'];
		}
		return $result;
    }
    /** Get Payment gateway details by payment type **/
    public function payment_gateway_bytype($paymentType = "")
    {
		$result = array();
		$match = array('payment_gateway_id' => (int)$paymentType, 'company_id' => 0);
		$project = array('payment_type' => '$payment_gateway_id',
						 'payment_gateway_username' => '$payment_gateway_username',
						 'payment_gateway_password' => '$payment_gateway_password',
						 'payment_gateway_key' => '$payment_gateway_signature',
                                                 'live_payment_gateway_username' => '$live_payment_gateway_username',
						 'live_payment_gateway_password' => '$live_payment_gateway_password',
						 'live_payment_gateway_key' => '$live_payment_gateway_signature',
						 //'gateway_currency_format' => '$currency_code',
						 'payment_method' => '$payment_method');
		$args = array(array('$match' => $match),
					array('$project' => $project));
		$res = $this->mongo_db->aggregate(MDB_PAYMENT_GATEWAYS,$args);
		$result = !empty($res['result']) ? $res['result'] : array();
        
        return $result;
    }
    /**************************** Customer enhancement - Edited Senthil *************************/
    /**Passenger Signup**/
    public function add_p_account_details($val, $otp = null, $referral_code = "", $devicetoken = "", $deviceid = "", $devicetype = "", $company_id = "")
    {
        //$username = Html::chars($val['name']);
        $password       = text::random($type = 'alnum', $length = 6);       
        $common_model   = Model::factory('commonmodel');
        $activation_key = Commonfunction::admin_random_user_password_generator();
        $company_id=1;
        if ($company_id != '') {
            $current_time = $common_model->getcompany_all_currenttimestamp($company_id);
        } else {
            $current_time = $this->currentdate_bytimezone;
        }
        /** Referrral key generator **/
		$ref_code = commonfunction::randomkey_generator('6');
		$referralcode_check = $this->mongo_db->findOne(MDB_PASSENGERS,array('referral_code'=>$ref_code),array('referral_code'));
		$count_referral = (!empty($referralcode_check)?$referralcode_check:0);
        if ($count_referral > 0) {
            $referral_code = (isset($referralcode_check['referral_code'])?$referralcode_check['referral_code']:'');
        } else {
            $referral_code       = $ref_code;
        }
		/*$rs = $this->mongo_db->find(MDB_PASSENGERS,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
		$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);*/
		$options=[
				'projection'=>[
				   '_id'=>1,                               
					],
				'sort'=>[
					'_id'=>-1
					 ],
				'limit'=>1
			];
		$res = $this->mongo_db->find(MDB_PASSENGERS,[],$options);
		$res = (!empty($res))?array($res[0]['_id']=>0):array(1);
		reset($res);
		$first_key = key($res);
		$inc_id = $first_key+1;
        $fieldname_array = array(
			'_id' => (int)$inc_id,
            'name'=>null,
            'email'=>$val['email'],
            'password'=>md5($val['password']),
            //'org_password'=>$val['password'],
            'otp'=>$otp,
            'phone'=>$val['phone'],
            'address'=>null,
            'referral_code'=>$referral_code,
            'activation_key'=>null,
            'activation_status'=>0,
            'user_status'=>'I',
            'created_date'=> Commonfunction::MongoDate(strtotime($current_time)),
            'updated_date'=> Commonfunction::MongoDate(strtotime($current_time)),
            'passenger_cid'=> (int)$company_id
        );
		$result = $this->mongo_db->insertOne(MDB_PASSENGERS,$fieldname_array);
		$result1 = empty($result->getwriteErrors())?1:0;
        if ($result1 == 1) {
            if ($devicetoken != "") {
                $update_array = array(
                    "device_token" => $devicetoken,
                    "device_id" => $deviceid,
                    "device_type" => (int)$devicetype
                );
				$update_device_token_result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('email'=>$val['email']),array('$set'=>$update_array), array('upsert' => false));
            }
			return 1;
        } else {
            return 0;
        }
    }
    /** Resend OTP **/
    public function update_otp($otp_array, $otp, $company_id = '')
    {
        if ($company_id != '' && $company_id != 0) {
            $common_model     = Model::factory('commonmodel');
            $current_datetime = $common_model->getcompany_all_currenttimestamp($company_id);
        } else {
            $current_datetime = date('Y-m-d H:i:s');
        }
        
        $update_arr = array('otp'=>$otp,'updated_date'=> Commonfunction::MongoDate(strtotime($current_datetime)));
        if ($otp_array['user_type'] == 'P') {
			
			$match = array('email'=>$otp_array['email']);
            if ($company_id != '') {
				$match['passenger_cid'] = (int)$company_id;
            }            
            $update_otp = $this->mongo_db->updateOne(MDB_PASSENGERS, $match, array('$set'=>$update_arr),array('upsert'=>false));
        } else if ($otp_array['user_type'] == 'D') {
			
			$update_otp = $this->mongo_db->updateOne(MDB_PEOPLE,array('email'=>$otp_array['email']),array('$set'=>$update_arr),array('upsert'=>false));
        } else {
            $update_otp = false;
        }
        return ($update_otp == false) ? 0 :1;
    }
    //Check Referral code
    public function check_referral_code($referral_code = null, $company_id = '')
    {
        try {            
			$match = array();
			$match['referral_code'] = $referral_code;
			if ($company_id != '' && $company_id !=0) {
				$match['passenger_cid'] = (int)$company_id;
			}
			$res = $this->mongo_db->findOne(MDB_PASSENGERS,$match,array('_id'));
			
			$result="";
			if(!empty($res)){
				$temp_arr['id'] = isset($res['_id']) ? $res['_id'] : '';
				$result[] = $temp_arr;
			}
			
			return (isset($result)?$result:array()); 
			//return (isset($res)?$res:array()); 
        }
        catch (Kohana_Exception $e) {
            return array();
        }
    }
    //Save Passenger Personal Profile Data
    public function save_passenger_personaldata($array, $referred_passenger_id, $company_id = '')
    {
        try {
            $p_email = $array['email'];
			$match = array();
			$match['email'] = $p_email;
			if ($company_id != '' && $company_id !=0) {
				$match['passenger_cid'] = (int)$company_id;
			}
			$result = $this->mongo_db->findOne(MDB_PASSENGERS,$match,array('_id'));
			$passenger_id = (isset($result['_id'])?$result['_id']:0);
            if ($passenger_id != 0) {
				$match['_id'] = (int)$passenger_id;
				$result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$array),array('upsert'=>false));
                return (empty($result->getwriteErrors())?0:-1);
            } else {
                return 1;
            }
        }
        catch (Kohana_Exception $e) {
            return 1;
        }
    }
    //Save Passenger Card Data
    public function save_passenger_carddata($array, $default_companyid)
    {
        try {
            $p_email = $array['email'];
			$match = array('email'=> $p_email);
            if ($default_companyid != "" && $default_companyid != 0 ) { 		
				$match = array('email'=> $p_email,'passenger_cid'=> (int)$default_companyid);
            } 
			$result = $this->mongo_db->findOne(MDB_PASSENGERS,$match,array('_id'));
			$data =  (!empty($result)) ? $result: array();
            if (count($data) > 0) {
                $passenger_id = $data['_id'];
            } else {
                $passenger_id = '';
            }
            if ($passenger_id != '') {
                $card_holder_name = isset($array['card_holder_name']) ? $array['card_holder_name'] : '';
                $creditcard_no    = $array['creditcard_no'];
                $creditcard_no    = encrypt_decrypt('encrypt', $creditcard_no);
                $creditcard_cvv   = $array['creditcard_cvv'];
                $expdatemonth     = $array['expdatemonth'];
                $expdateyear      = $array['expdateyear'];
                $email = $array['email'];  
                $args = array(array('$unwind' => '$creditcard_details'),
								array('$match' => array('_id'=> (int)$passenger_id,'creditcard_no' => $creditcard_no)));              
				$result = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);	
                if(!empty($result['result'])) {
                    return -1;
                } else {
					/*$passenger_rs = $this->mongo_db->find(MDB_PASSENGERS,array(),array('creditcard_details.passenger_cardid'))
									->sort(array('creditcard_details.passenger_cardid'=>-1))->limit(1);	*/
					$options=[
							'projection'=>[
							   'creditcard_details.passenger_cardid'=>1,                               
								],
							'sort'=>[
								'creditcard_details.passenger_cardid'=>-1
								],
							'limit'=>1
						];
					$passenger_rs = $this->mongo_db->find(MDB_PASSENGERS,[],$options);
					
					$passenger_rs1 = reset($passenger_rs);
					$passenger_first_key = isset($passenger_rs1['creditcard_details'][0]['passenger_cardid']) ? $passenger_rs1['creditcard_details'][0]['passenger_cardid'] : 0;
					$passenger_cardid = $passenger_first_key+1;
					$set_array = array("creditcard_details"=>array(
								"passenger_cardid" => (int)$passenger_cardid,
								"passenger_email" => $email,
								"card_type" => "P",
								"creditcard_no" => $creditcard_no,
								"creditcard_cvv" => $creditcard_cvv,
								"card_holder_name" => $card_holder_name,
								"expdatemonth" => (int)$expdatemonth,
								"expdateyear" => (int)$expdateyear,
								"status" => 1,
								"default_card" =>1,
								"createdate" => Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone))));
					$insert = $this->mongo_db->updateMany(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array('$push'=>$set_array));
                    return $passenger_cardid;
                }
            } else {
                return 0;
            }
        }
        catch (Kohana_Exception $e) {
            return 0;
        }
    }
    // Check Whether Passenger Personal Data is Already Exist or Not //check_passenger_card_data
    public function check_passenger_personal_data($userid = "")
    {
		$match = array('\$and'=>array(array('_id'=>(int)$userid),array('\$or'=>array(array('name'=>array('$eq'=>'')),array('lastname'=>array('$eq'=>''))))));
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match);
		return (isset($result))? $result: 0;
    }
    //Check Passenger Card Details
    public function check_passenger_card_data($userid = "")
    {
		$arguments = array(array('$unwind'=>'$creditcard_details'),
			array('$match'=>array('_id'=>(int)$userid)),
			array('$project'=>array('_id'=>'$_id')),
			array('$group' => array('_id' => NULL,'count' => array('$sum' => 1)))
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS, $arguments);
        return (isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
    }
    // edit_check_email_passengers
    public function edit_check_email_passengers($email = "", $passenger_id = "", $company_id = '')
    {
		$match = array();
		$match['email'] =  $email;
		$match['_id'] =  array('$ne' => (int)$passenger_id);
		if($company_id != '' && $company_id != 0)  {
			$match['passenger_cid'] =  (int)$company_id;
		}
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match);
		return (!empty($result)) ? $result : 0 ;
    }
    // edit_check_email_passengers
    public function edit_check_email_people($email = "", $driver_id = "")
    {
		$match = array('email' => $email, 'user_type' => 'D', '_id' => array('$ne'=> (int)$driver_id));
		$result = $this->mongo_db->count(MDB_PEOPLE,$match);
		return (!empty($result)) ? $result : 0 ;
    }
    //edit_check_phone_passengers
    public function edit_check_phone_passengers($phone = "", $passenger_id = '', $company_id = '', $country_code = '')
    {
		$match = array();
		$match['phone'] =  $phone;
		$match['country_code'] =  array('$in' =>array($country_code,''));
		$match['_id'] =  array('$ne' => (int)$passenger_id);
		if($company_id != '' && $company_id != 0)  {
			$match['passenger_cid'] =  (int)$company_id;
		}
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match);
		return (!empty($result)) ? $result : 0 ;
    }
    //edit_check_phone_passengers
    public function edit_check_phone_people($phone = "", $driver_id = '')
    {
		$match = array('phone' => $phone, 'user_type' => 'D', '_id' => array('$ne'=> (int)$driver_id));
		$result = $this->mongo_db->count(MDB_PEOPLE,$match);
		return (!empty($result)) ? $result : 0 ;
    }
    //Edit Passenger Personal Profile Data
    public function edit_passenger_personaldata($array, $passenger_id, $company_id = '')
    {
        try {
			$match = array('_id' => (int)$passenger_id);
            if ($company_id != '' && $company_id != 0) {
				$match['passenger_cid'] = (int)$company_id;
            }
			$result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$array),array('upsert'=>false));
            return 0;
        }
        catch (Kohana_Exception $e) {
            return -1;
        }
    }
    //Check card exist for passenger
    public function check_card_exist($creditcard_no = "", $creditcard_cvv, $expdatemonth, $expdateyear, $passenger_id = "")
    {
        $creditcard_no = encrypt_decrypt('encrypt', $creditcard_no);
		$match = array('_id'=>(int)$passenger_id, 'creditcard_details.creditcard_no' => $creditcard_no, 'creditcard_details.status'=>1);
		$args = array(array('$unwind' => '$creditcard_details'),
					  array('$match' => $match)				  
					);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
		return (!empty($result['result'])) ? count($result['result']) : 0;
    }
    //Check card exist for passenger
    public function edit_check_card_exist($passenger_cardid, $creditcard_no = "", $creditcard_cvv, $expdatemonth, $expdateyear, $passenger_id = "", $default)
    {
		$creditcard_no = encrypt_decrypt('encrypt', $creditcard_no);
		$match = array('_id'=>(int)$passenger_id,
					   'creditcard_details.creditcard_no' => $creditcard_no,
					   'creditcard_details.passenger_cardid' => array('$ne'=>(int)$passenger_cardid),
					   'creditcard_details.status' =>1
					   );
		$args = array(array('$unwind' => '$creditcard_details'),
						array('$project' => array('passenger_cardid' => '$creditcard_details.passenger_cardid',
												'default_card' => '$creditcard_details.default_card')),
						array('$match' => $match)				  
					);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
		$result =  (!empty($res['result'])) ? $res['result'] : array();
		if(count($result) > 0){
			$default_card = $result[0]['default_card'];
            if ($default_card == $default) {
                return 2;
            } else {
                return 1;
            }			
		}else {
            return 0;
        }
    }
    //Check Favourite Place exist for passenger
    public function check_fav_place($passenger_id = "", $favourite_place = "", $d_favourite_place = "", $p_fav_locationtype = "")
    {
		$match = array('passenger_id' => (int)$passenger_id,'fav_loction_type' => $p_fav_locationtype);
		$fav_check = $this->mongo_db->count(MDB_PASSENGERS_FAVOURITES,$match);
		//print_r($fav_check);exit;
		
		if($fav_check == 0){
			$favourite_place   = $favourite_place;
            $d_favourite_place = $d_favourite_place;
			$match1 = array('passenger_id' => (int)$passenger_id,'p_favourite_place' => Commonfunction::MongoRegex("/$favourite_place/i"));
			if ($d_favourite_place != '') {
				$match1['d_favourite_place'] = Commonfunction::MongoRegex("/$d_favourite_place/i") ;
			}
			$fav_count = $this->mongo_db->count(MDB_PASSENGERS_FAVOURITES,$match1);
			return $fav_count;
		}else{
            return -1;
        }
    }
    public function check_fav_editplace($passenger_id = "", $favourite_place = "", $d_favourite_place = "", $favourite_id = "", $p_fav_locationtype = "")
    {
		$match = array('passenger_id' => (int)$passenger_id,'_id' => (int)$favourite_id);
		$fav_check = $this->mongo_db->findOne(MDB_PASSENGERS_FAVOURITES,$match);		
		
		if(count($fav_check) > 0){
			if ($fav_check['fav_loction_type'] == $p_fav_locationtype){
				return 0;
			}else{
				$match1 =array();
				$match1['p_favourite_id'] = array('$ne'=> (int)$favourite_id);
				$match1['fav_loction_type'] = array('$eq'=> $p_fav_locationtype);
				$type_check = $this->mongo_db->findOne(MDB_PASSENGERS_FAVOURITES,$match1);
				return (isset($type_check)) ? $type_check : 0;
			}			
		}else{
            return -1;
        }
    }
    public function check_fav_editplacecheck($passenger_id = "", $favourite_place = "", $d_favourite_place = "", $favourite_id = "", $p_fav_locationtype = "")
    {
		$favourite_place   = $favourite_place;//$this->mysql->escape($favourite_place);
		$d_favourite_place = $d_favourite_place;//$this->mysql->escape($d_favourite_place);
		$match1 = array('_id'=>array('$ne'=>(int)$favourite_id),
						'passenger_id' => (int)$passenger_id,
						'p_favourite_place' => Commonfunction::MongoRegex("/$favourite_place/i"));
		if ($d_favourite_place != '') {
			$match1['d_favourite_place'] = Commonfunction::MongoRegex("/$d_favourite_place/i");
		}
		$fav_count = $this->mongo_db->count(MDB_PASSENGERS_FAVOURITES,$match1);
		return $fav_count;
    }
    //Add Passenger Card Data
    public function add_passenger_carddata($array, $referred_passenger_id = null,$preTransactId = '', $preTransactAmount = '', $cardTypeDesc = '')
    {
        try {
            $p_email        = $array['email'];
            $passenger_id   = $array['passenger_id'];
            $creditcard_no  = $array['creditcard_no'];
            $creditcard_no  = encrypt_decrypt('encrypt', $creditcard_no);
            $creditcard_cvv = $array['creditcard_cvv'];
            $expdatemonth   = $array['expdatemonth'];
            $expdateyear    = $array['expdateyear'];
            $card_type      = $array['card_type'];
            $default        = $array['default'];
			
			$args = array(array('$unwind' => '$creditcard_details'),
						  array('$sort' => array('creditcard_details.passenger_cardid' => -1)),
						  array('$project' => array('card_id' => '$creditcard_details.passenger_cardid')),
						  array('$limit' => 1)
						  );
			$get_id = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
			$inc_id = (isset($get_id['result'][0]['card_id']) && !empty($get_id['result'][0]['card_id'])) ? $get_id['result'][0]['card_id'] : 0;
			$inc_id +=1;			
			$update_array = array("creditcard_details"=>array(
								'passenger_cardid' => (int)$inc_id,
								'passenger_id' => (int)$passenger_id,
								'passenger_email' => $p_email,
								'card_type' => $card_type,
								'creditcard_no' => $creditcard_no,
								'expdatemonth' => $expdatemonth,
								'expdateyear' => $expdateyear,
								'default_card' => (int)$default,
								'creditcard_cvv' => $creditcard_cvv,
								'status' => 1,
								"createdate" => Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone)),
								'pre_transaction_id' => $preTransactId,
								'pre_transaction_amount' => $preTransactAmount,
								'card_type_description' => $cardTypeDesc,
								'status' =>1,																
								'void_status' => 0 ));
            if ($default == 1) {
				$match = array('_id'=>(int)$passenger_id);
				$args = array(array('$unwind' => '$creditcard_details'),
						  array('$match' => array('_id' => (int)$passenger_id)),
						  array('$project' => array('card_id' => '$creditcard_details.passenger_cardid'))
						);
				$keys = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
				$val = array();
				if(!empty($keys['result'])){
					$i=0;
					foreach($keys['result'] as $k => $v ){
						$val["creditcard_details.".$i.".default_card"] = 0;
						$i++;
					}
					$def_update          = $val;
					$match['creditcard_details.passenger_id'] = (int)$passenger_id;
					$update = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$def_update),array('upsert' => false));
				}				
            }
			$result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),
											  array('$push'=>$update_array),
											  array('upsert' => false));
            return $inc_id;
        }
        catch (Kohana_Exception $e) {
            return 0;
        }
    }
    //edit Passenger Card Data
    public function edit_passenger_carddata($array,$preTransactId = '', $preTransactAmount = '', $cardTypeDesc = '')
    {
        try {	
            $passenger_cardid = $array['passenger_cardid'];
            $passenger_id     = $array['passenger_id'];
            $creditcard_no    = $array['creditcard_no'];
            $creditcard_no    = encrypt_decrypt('encrypt', $creditcard_no);
            $creditcard_cvv   = $array['creditcard_cvv'];
            $expdatemonth     = $array['expdatemonth'];
            $expdateyear      = $array['expdateyear'];
            $card_type        = $array['card_type'];
            $default          = $array['default'];
			$match = array('_id'=>(int)$passenger_id);
			$args = array(array('$unwind' => '$creditcard_details'),
					  array('$match' => array('_id' => (int)$passenger_id)),
					  array('$project' => array('card_id' => '$creditcard_details.passenger_cardid'))
					);
			$keys = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);			
			$i =0;$j =0;$val = array();
			foreach($keys['result'] as $k => $v ){
				if($v['card_id'] == $array['passenger_cardid']){
					$j = $i;
				}
				$val["creditcard_details.".$i.".default_card"] = 0;
				$i++;
			}
			$update_array = array(
								"creditcard_details.$j.card_type" => $card_type,
								"creditcard_details.$j.creditcard_no" => $creditcard_no,
								"creditcard_details.$j.creditcard_cvv" => $creditcard_cvv,
								"creditcard_details.$j.expdatemonth" => $expdatemonth,
								"creditcard_details.$j.expdateyear" => $expdateyear,
								"creditcard_details.$j.default_card" => (int)$default,
								"creditcard_details.$j.pre_transaction_id"=>$preTransactId,
								"creditcard_details.$j.pre_transaction_amount"=>$preTransactAmount,
								"creditcard_details.$j.card_type_description"=>$cardTypeDesc
							);
            if ($default == 1) {
				//$def_update          = $val;
				//$val["creditcard_details.$.default_card"] = 0;
				$def_update = $val;
				//print_r($def_update);exit;
				$match['creditcard_details.passenger_id'] = (int)$passenger_id;
				$update = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$def_update),array('upsert' => false));
            }
			$result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),
											array('$set'=>$update_array),
											array('upsert' => false));
			return 0;	
        }
        catch (Kohana_Exception $e) {
            return 1;			
        }
    }
    /** Save favourite Trip**/
    public function save_favourite($passenger_id = null, $p_favourite_place = null, $p_fav_latitude = null, $p_fav_longtitute = null, $d_favourite_place = null, $d_fav_latitude = null, $d_fav_longtitute = null, $fav_comments = null, $notes = null, $p_fav_locationtype = null,$image_name=null)
    {
		$inc_id = $this->get_insert_id(MDB_PASSENGERS_FAVOURITES);
		$insert_array = array(
			'_id' => (int)$inc_id,
			'passenger_id' => (int)$passenger_id,
			'p_favourite_place' => $p_favourite_place,
			'p_fav_latitude' => $p_fav_latitude,
			'p_fav_longtitute' => $p_fav_longtitute,
			'd_favourite_place' => $d_favourite_place,
			'd_fav_latitude' => $d_fav_latitude,
			'd_fav_longtitute' => $d_fav_longtitute,
			'fav_comments' => $fav_comments,
			'status' => 'A',
			'notes' => $notes,
			'fav_loction_type' => $p_fav_locationtype,
			'fav_image' => $image_name
			
		);
		$result = $this->mongo_db->insertOne(MDB_PASSENGERS_FAVOURITES,$insert_array);
        return $inc_id;
    }
    /** Get Favourite list **/
    public function get_favourite_list($passenger_id="")
    {	
		$match = array('passenger_id' => (int)$passenger_id,'status' => 'A');
		$project = array('p_favourite_id' => '$_id',
						 'passenger_id' => '$passenger_id',
						 'p_favourite_place' => '$p_favourite_place',
						 'p_fav_latitude' => '$p_fav_latitude',
						 'p_fav_longtitute' => '$p_fav_longtitute',
						 'd_favourite_place' => '$d_favourite_place',
						 'd_fav_latitude' => '$d_fav_latitude',
						 'd_fav_longtitute' => '$d_fav_longtitute',
						 'fav_comments' => '$fav_comments',
						 'notes' => '$notes',
						 'fav_loction_type' => '$fav_loction_type',
						 'fav_image' => '$fav_image'
						 );		
		$args = array(array('$match' => $match),
					  array('$project' => $project)
					  );
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_FAVOURITES,$args);
		return (isset($res['result'])) ? $res['result'] : array();
    }
    //$favourite_id,$p_favourite_place,$p_fav_latitude,$p_fav_longtitute,$d_favourite_place,$d_fav_latitude,$d_fav_longtitute,$fav_comments
    public function edit_favourite($favourite_id = null, $p_favourite_place = null, $p_fav_latitude = null, $p_fav_longtitute = null, $d_favourite_place = null, $d_fav_latitude = null, $d_fav_longtitute = null, $fav_comments = null, $notes = null, $p_fav_locationtype = null)
    {
		$update_array = array(
            'p_favourite_place' => $p_favourite_place,
            'p_fav_latitude' => $p_fav_latitude,
            'p_fav_longtitute' => $p_fav_longtitute,
            'd_favourite_place' => $d_favourite_place,
            'd_fav_latitude' => $d_fav_latitude,
            'd_fav_longtitute' => $d_fav_longtitute,
            'fav_comments' => $fav_comments,
            'notes' => $notes,
            'fav_loction_type' => $p_fav_locationtype
        );
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS_FAVOURITES,array('_id'=>(int)$favourite_id),array('$set'=>$update_array),array('upsert'=>false));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    /** Delete Favourite **/
    public function delete_favourite($favourite_id, $passenger_id)
    {
		$result = $this->mongo_db->deleteMany(MDB_PASSENGERS_FAVOURITES,array('_id'=> (int)$favourite_id));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    //Passenger Completed Trips by Date wise
    public function get_passenger_trips_bydate($pagination, $booktype, $userid = "", $status = "", $driver_reply = "", $createdate = "", $start = null, $limit = null, $date)
    {
        $start_time   = $date . ' 00:00:01';
        $end_time     = $date . ' 23:59:59';
        $result = $temp_arr = $pagination_arr = array();
		$match = array('createdate'=>array('$gte'=> Commonfunction::MongoDate(strtotime($start_time)),
										   '$lte'=> Commonfunction::MongoDate(strtotime($end_time))),
						'passengers_id'=> (int)$userid,
						'travel_status'=> (int)$status,
						'driver_reply'=> $driver_reply
						);
		if($booktype != 2){
			$match['bookingtype'] = $booktype;
		}
		$args = array(
					array('$match' => $match),
					array('$lookup' => array('from' => MDB_TRANSACTION,
										   'localField' => '_id',
										   'foreignField' => 'passengers_log_id',
										   'as' => 'transaction')),
					  array('$unwind' => '$transaction'),
					  array('$lookup' => array('from' => MDB_PEOPLE,
											   'localField' => 'driver_id',
											   'foreignField' => '_id',
											   'as' => 'people')),
					  array('$unwind' => '$people'),
					  array('$lookup' => array('from' => MDB_TAXI,
											   'localField' => 'taxi_id',
											   'foreignField' => '_id',
											   'as' => 'taxi')),
					  array('$unwind' => '$taxi'),
					  array('$lookup' => array('from' => MDB_MOTOR_MODEL,
											   'localField' => 'taxi.taxi_model',
											   'foreignField' => '_id',
											   'as' => 'motor_model')),
					  array('$unwind' => '$motor_model'),
					  array('$project'=>array('place'=>'$current_location',
											'pickup_time'=>'$pickup_time',
											'actual_pickup_time'=>'$actual_pickup_time',
											'drop_location'=>'$drop_location',
											'pickup_latitude'=>'$pickup_latitude',
											'pickup_longitude'=>'$pickup_longitude',
											'drop_latitude'=>'$drop_latitude',
											'drop_longitude'=>'$drop_longitude',
											'notes_driver'=>'$notes_driver',
											'fare'=>'$transaction.fare',
											'drivername'=> array('$concat'=>array('$people.name',' ','$people.lastname')),
											'driverimage'=>'$people.photo',
											'taxi_no'=>'$taxi.taxi_no',
											'payment_name'=>'$payment_modules.pay_mod_name',
											'trip_id'=>'$_id',
											'jobreferral'=>'$transaction._id',
											'createdate'=>'$createdate',
											'passengers_id'=>'$passengers_id',
											'travel_status'=>'$travel_status',
											'driver_reply'=>'$driver_reply',
											'msg_status'=>'$msg_status',
											'profile_image' => '$people.photo',
											'model_name' => '$motor_model.model_name'
										)),					 
					  array('$sort' => array('_id'=>-1))			  
					);
		$pagination_arr = array();
		if ($pagination == 1) {
			$pagination_arr = array(array('$skip' => (int)$start),array('$limit' => (int)$limit));
        }
		$arguments = array_merge($args,$pagination_arr);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr = $r;
				$temp_arr['booking_time'] = isset($temp_arr['booking_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['booking_time']):'';
				$temp_arr['actual_pickup_time'] = isset($temp_arr['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['actual_pickup_time']):'';
				$temp_arr['pickup_time'] = isset($temp_arr['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['pickup_time']):'';
				$result[] = $temp_arr;
			}			
		}
		return $result;
    }    
    
	
    // Get Passenger trips by Fromdate anda to date
	public function get_passengertrips_byfrmdate($pagination,$booktype,$userid="",$createdate="",$start=null,$limit=null,$date)
	{
        $current_time = convert_timezone('now', TIMEZONE);
        $current_date = explode(' ', $current_time);
        $start_time   = $date . ' 00:00:01';
        $end_time     = $date . ' 23:59:59';
		$result = $temp_arr = array();
		
		$match_query[]['split_details.friends_p_id'] = (int)$userid;
		$match_query[]['split_details.approve_status'] = 'A';
		if ($booktype != 2) {
			$match_query[]['bookingtype'] = (int)$booktype;
        }        
        
		$and = array('$or' => array(
								array('$and'=>array(array('travel_status'=> 1), array('driver_reply'=> 'A'))),
								array('travel_status' => array('$in' => array(4,8))),
								array('$and'=>array(array('travel_status'=> 9), array('driver_reply'=> 'C')))
							)
						);
		$match = array('$and' => array_merge($match_query, array($and)));
		
        $arguments = array(
					array('$unwind' =>  array( 'path' =>  '$split_details', 'preserveNullAndEmptyArrays' =>  true )),
					array('$lookup'=>array(
						'from'=>MDB_TRANSACTION,
						'localField'=>"_id",
						'foreignField'=>"passengers_log_id",
						 'as'=>"transaction"        
					)),
					array('$unwind' =>  array( 'path' =>  '$transaction', 'preserveNullAndEmptyArrays' =>  true )),
					array('$lookup'=>array(
						'from'=>MDB_PEOPLE,
						'localField'=>"driver_id",
						'foreignField'=>"_id",
						 'as'=>"people"        
					)),
					array('$unwind' =>  array( 'path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true )),
					array('$lookup'=>array(
						'from'=>MDB_TAXI,
						'localField'=>"taxi_id",
						'foreignField'=>"_id",
						 'as'=>"taxi"        
					)),
					array('$unwind' =>  array( 'path' =>  '$taxi', 'preserveNullAndEmptyArrays' =>  true )),
					array('$match'=> $match),
					array('$lookup'=>array(
						'from'=>MDB_MOTOR_MODEL,
						'localField'=>"taxi.taxi_model",
						'foreignField'=>"_id",
						 'as'=>"motor_model"        
					)),
					array('$unwind' =>  array( 'path' =>  '$motor_model', 'preserveNullAndEmptyArrays' =>  true )),
					array('$lookup'=>array(
						'from'=>MDB_LOCATION_HISTORY,
						'localField'=>"_id",
						'foreignField'=>"trip_id",
						 'as'=>"location_history"        
					)),
					array('$unwind' =>  array( 'path' =>  '$location_history', 'preserveNullAndEmptyArrays' =>  true )),
					array('$project' => array(
						'place' => '$current_location',
						'pickup_time' => '$pickup_time',
						'actual_pickup_time' => '$actual_pickup_time',
						'drop_location' => '$drop_location',
						'pickup_latitude' => '$pickup_latitude',
						'pickup_longitude' => '$pickup_longitude',
						'drop_latitude' => '$drop_latitude',
						'drop_longitude' => '$drop_longitude',
						'notes_driver' => '$notes_driver',
						//'fare' => '$transaction.fare',
						'fare' => array('$ifNull'  =>  array(array( '$sum'  => array('$transaction.amt', '$used_wallet_amount') ), 0)),
						'drivername' => array('$concat'=>array('$people.name',' ', '$people.lastname')),
						'profile_image' => '$people.profile_picture',
						'taxi_no' => '$taxi.taxi_no',
						'payment_name' => '$payment_modules.pay_mod_name',
						'trip_id' => '$_id',
						'jobreferral' => '$transaction._id',							
						'createdate' => '$createdate',
						'passengers_id' => '$passengers_id',
						'travel_status' => '$travel_status',
						'driver_reply' => '$driver_reply',
						'msg_status' => '$msg_status',
						'model_name' => '$motor_model.model_name',
						'distance' => '$distance',
						"payment_type"=> array('$ifNull'=> array('$transaction.payment_type',0)),
						"active_record"=> array('$ifNull'=> array('$location_history.loc',0))
					))
				);		
		
		if ($pagination == 1) {
			$page_arguments = array(
				array('$sort' => array('_id' => -1)),
				array('$skip' => (int)$start),
				array('$limit' => (int)$limit)
			);
        } else {
			$page_arguments = array(array('$sort' => array('travel_status' => -1)));
        }
        
        $args = array_merge($arguments, $page_arguments);
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
        if(!empty($res['result'])){
			$coordinates='';
			foreach($res['result'] as $r){
				
				$temp_arr = $r;
				$temp_arr['pickup_time'] = isset($temp_arr['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['pickup_time']): '';
				$temp_arr['createdate'] = isset($temp_arr['createdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['createdate']): '';
				$temp_arr['actual_pickup_time'] = isset($temp_arr['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['actual_pickup_time']): '';
				
				# Active record
				$active_record = $r['active_record']['coordinates'];				
				if(!empty($active_record)){
					foreach($active_record as $a){
						$lat = '['.$a[1].',';
						$long = $a[0].'],';
						$coordinates .= $lat.$long;
					}
					($coordinates !='') ? $temp_arr['active_record'] = $coordinates : '';
				}	
				$temp_arr['active_record'] = $coordinates;
				$temp_arr['fare'] = isset($temp_arr['fare']) ? $temp_arr['fare'] : 0;
				$temp_arr['taxi_no'] = isset($temp_arr['taxi_no']) ? $temp_arr['taxi_no'] : '';
				$temp_arr['model_name'] = isset($temp_arr['model_name']) ? $temp_arr['model_name'] : '';
				$result[] = $temp_arr;
			}
		}
		//print_r($res);exit;
        return $result;
    }
   
    // Get today Goal Details //	
	public function get_goal_details($id, $msg_status, $driver_reply = null, $travel_status = null)
    {
        $current_time = convert_timezone('now', TIMEZONE);
        $current_date = explode(' ', $current_time);
        $start_time   = $current_date[0] . ' 00:00:01';
        $end_time     = $current_date[0] . ' 23:59:59';
		$match_query = array();
		$match_query['driver_id'] = (int)$id;
		$match_query['msg_status'] = $msg_status;
		$match_query['driver_reply'] = $driver_reply;
		$match_query['travel_status'] = (int)$travel_status;
		if ($start_time != "" && $end_time!="") {
			$match_query['createdate'] = array('$gte' => Commonfunction::MongoDate(strtotime($start_time)),'$lte' => Commonfunction::MongoDate(strtotime($end_time)));
        }
		$arguments = array(
			array('$lookup'  		=> array(
                    'from'			=>	MDB_TRANSACTION,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "trans"
                )
            ),
			array('$match' => $match_query),
			array('$project' =>array('amt' =>array('$sum'=>'$trans.amt'))),
			array('$group' =>array('_id' =>array('_id' => null),'acheive_amt' => array('$sum'=>'$amt'))),
			array('$project' =>array('_id' =>0,'acheive_amt' => '$acheive_amt')),
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		return (!empty($result['result'])?$result['result']:array());
    }
	

    // Check Whether People phone is Already Exist or Not //
    /*********************************************************************************************/
    /*********** Function Used for validate credit cards ***************/
    function _checkSum($ccnum)
    {
        $checksum = 0;
        for ($i = (2 - (strlen($ccnum) % 2)); $i <= strlen($ccnum); $i += 2) {
            $checksum += (int) ($ccnum{$i - 1});
        }
        // Analyze odd digits in even length strings or even digits in odd length strings.
        for ($i = (strlen($ccnum) % 2) + 1; $i < strlen($ccnum); $i += 2) {
            $digit = (int) ($ccnum{$i - 1}) * 2;
            if ($digit < 10) {
                $checksum += $digit;
            } else {
                $checksum += ($digit - 9);
            }
        }
        if (($checksum % 10) == 0)
            return true;
        else
            return false;
    }
    function isVAlidCreditCard($ccnum, $type = "", $returnobj = false)
    {
        $creditcard = array(
            "visa" => "/^4\d{3}-?\d{4}-?\d{4}-?\d{4}$/",
            "mastercard" => "/^5[1-5]\d{2}-?\d{4}-?\d{4}-?\d{4}$/",
            "discover" => "/^6011-?\d{4}-?\d{4}-?\d{4}$/",
            "amex" => "/^3[4,7]\d{13}$/",
            "diners" => "/^3[0,6,8]\d{12}$/",
            "bankcard" => "/^5610-?\d{4}-?\d{4}-?\d{4}$/",
            "jcb" => "/^[3088|3096|3112|3158|3337|3528|3530]\d{12}$/",
            "enroute" => "/^[2014|2149]\d{11}$/",
            "switch" => "/^[4903|4911|4936|5641|6333|6759|6334|6767]\d{12}$/"
        );
        if (empty($type)) {
            $match = false;
            foreach ($creditcard as $type => $pattern)
                if (preg_match($pattern, $ccnum) == 1) {
                    $match = true;
                    break;
                }
            if (!$match)
                return 0;
            else {
                if ($returnobj) {
                    $return        = new stdclass;
                    $return->valid = $this->_checkSum($ccnum);
                    $return->ccnum = $ccnum;
                    $return->type  = $type;
                    return 1;
                } else
                    return 0;
            }
        } else {
            if (@preg_match($creditcard[strtolower(trim($type))], $ccnum) == 0) {
                return false;
            } else {
                if ($returnobj) {
                    //print_r($returnobj);
                    $return        = new stdclass;
                    $return->valid = $this->_checkSum($ccnum);
                    $return->ccnum = $ccnum;
                    $return->type  = $type;
                    return 1;
                } else
                    return 1;
            }
        }
    }
    /*************************************************************************************/
    //Mobile Driver Push Notification Sending
    public function send_driver_mobile_pushnotification($d_device_token = "", $device_type = "", $pushmessage = null, $android_api = "", $book_request = "")
    {
        //---------------------------------- ANDROID ----------------------------------//                            
        if ($device_type == 1) {
            $apiKey          = $android_api;
            //echo $d_device_token;
            $registrationIDs = array(
                $d_device_token
            );
            // Message to be sent                                    
            if (!empty($registrationIDs)) {
                // Set POST variables
                $url     = 'https://android.googleapis.com/gcm/send';
                //print_r($registrationIDs);exit;
                $fields  = array(
                    'registration_ids' => $registrationIDs,
                    'data' => array(
                        "message" => $pushmessage
                    )
                );
                $headers = array(
                    'Authorization: key=' . $apiKey,
                    'Content-Type: application/json'
                );
                // print_r($pushmessage);exit;
                // Open connection
                $ch      = curl_init();
                // Set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                // Execute post
                $result = curl_exec($ch);
                //echo $result;
                // Close connection
                curl_close($ch);
                //echo $result;                            
            }
            //exit;  
        } else if ($device_type == 2) {
            //---------------------------------- IPHONE ----------------------------------//  
            //print_r($contact_iphone);exit;
            $deviceToken = $d_device_token;
            $deviceToken = trim($deviceToken);
            if (!empty($deviceToken)) {
                //print_r($deviceToken);exit;
                // Put your private key's passphrase here:
                $passphrase = '1234';
                // Put your alert message here:
                //$message = $message = "A new business ".$business_name." is added in Yiper";
                //$message = $deal_id.".".ucfirst($merchant_name)." has a new deal for you. View now...";
                $badge      = 0;
                ////////////////////////////////////////////////////////////////////////////////
                $root       = $_SERVER['DOCUMENT_ROOT'] . '/application/classes/controller/ck.pem';
                // echo  $root;
                $ctx        = stream_context_create();
                stream_context_set_option($ctx, 'ssl', 'local_cert', $root);
                stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                // Open a connection to the APNS server
                $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                if (!$fp)
                    exit("Failed to connect: $err $errstr" . PHP_EOL);
                //echo 'Connected to APNS' . PHP_EOL;
                // Create the payload body
                $status = $pushmessage['status'];
                if ($status == 1) {
                    $pickup       = $pushmessage['pickup'];
                    $passenger_id = $pushmessage['passenger_id'];
                    $taxi_id      = $pushmessage['taxi_id'];
                    $company_id   = $pushmessage['company_id'];
                    $distance     = $pushmessage['distance'];
                    $trip_details = $passenger_id . '-' . $taxi_id . '-' . $company_id . '-' . $distance;
                    $body['aps']  = array(
                        'alert' => 'You have new booking request',
                        'trip_details' => $trip_details,
                        'sound' => 'default'
                    );
                } else if ($status == 3) {
                    $pickup            = $pushmessage['pickup'];
                    $fare              = $pushmessage['fare'];
                    $referral_discount = $pushmessage['referral_discount'];
                    $message           = $pushmessage['result'];
                    $fare_details      = $pickup . '-' . $fare . '-' . $referral_discount;
                    $body['aps']       = array(
                        'alert' => $message,
                        'fare_details' => $fare_details,
                        'sound' => 'default'
                    );
                    $body['aps']       = array(
                        'alert' => $pushmessage,
                        'badge' => $badge,
                        'sound' => 'default'
                    );
                }
                // print_r($body);
                // exit;
                // Encode the payload as JSON
                $payload = json_encode($body);
                // Build the binary notification
                $msg     = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
                // Send it to the server
                $result  = fwrite($fp, $msg, strlen($msg));
                //if (!$result)
                //  echo 'Message not delivered' . PHP_EOL;
                //else
                //echo 'Message successfully delivered' . PHP_EOL;
                // Close the connection to the server
                fclose($fp);
            }
        } else {
        }
    }
    //Mobile Passenger Push Notification Sending
    public function send_passenger_mobile_pushnotification($d_device_token = "", $device_type = "", $pushmessage = null, $android_api = "")
    {
        //echo 'asd';
        // print_r($pushmessage);exit;
        //echo 'as'.$device_type;
        //---------------------------------- ANDROID ----------------------------------//                            
        if ($device_type == 1) {
            $apiKey          = $android_api;
            $registrationIDs = array(
                $d_device_token
            );
            //echo $d_device_token;
            // Message to be sent                                    
            if (!empty($registrationIDs)) {
                // Set POST variables
                $url         = 'https://android.googleapis.com/gcm/send';
                //print_r($registrationIDs);exit;
                $pushmessage = json_encode($pushmessage);
                //print_r($pushmessage);exit;
                $fields      = array(
                    'registration_ids' => $registrationIDs,
                    'data' => array(
                        "message" => $pushmessage
                    )
                );
                $headers     = array(
                    'Authorization: key=' . $apiKey,
                    'Content-Type: application/json'
                );
                // Open connection
                $ch          = curl_init();
                // Set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                // Execute post
                $result = curl_exec($ch);
                //echo $result;
                // Close connection
                curl_close($ch);
                //echo $result;                            
            }
            //exit;  
        } elseif ($device_type == 2) {
            //---------------------------------- IPHONE ----------------------------------//  
            //print_r($contact_iphone);exit;
            // print_r($pushmessage);
            $deviceToken = $d_device_token;
            $deviceToken = trim($deviceToken);
            // echo $deviceToken;exit;                                                                                      
            if (!empty($deviceToken)) {
                //print_r($deviceToken);exit;
                // Put your private key's passphrase here:
                $passphrase = '1234';
                // Put your alert message here:
                //$message = $message = "A new business ".$business_name." is added in Yiper";
                //$message = $deal_id.".".ucfirst($merchant_name)." has a new deal for you. View now...";                                    
                $badge      = 0;
                ////////////////////////////////////////////////////////////////////////////////
                $root       = $_SERVER['DOCUMENT_ROOT'] . '/application/classes/controller/ck.pem';
                $ctx        = stream_context_create();
                stream_context_set_option($ctx, 'ssl', 'local_cert', $root);
                stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                // Open a connection to the APNS server
                $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                if (!$fp)
                    exit("Failed to connect: $err $errstr" . PHP_EOL);
                //echo 'Connected to APNS' . PHP_EOL; 
                // Create the payload body
                $message = $pushmessage['message'];
                $status  = $pushmessage['status'];
                if ($status == 1) {
                    $trip_id     = $pushmessage['trip_id'];
                    $body['aps'] = array(
                        'alert' => $message,
                        'trip_id' => $trip_id,
                        'status' => $status,
                        'sound' => 'default'
                    );
                } else if ($status == 2) {
                    $trip_id     = $pushmessage['trip_id'];
                    $body['aps'] = array(
                        'alert' => $message,
                        'trip_id' => $trip_id,
                        'status' => $status,
                        'sound' => 'default'
                    );
                } else if ($status == 3) {
                    $trip_id     = $pushmessage['trip_id'];
                    $body['aps'] = array(
                        'alert' => $message,
                        'trip_id' => $trip_id,
                        'status' => $status,
                        'sound' => 'default'
                    );
                } else if ($status == 4) {
                    $body['aps'] = array(
                        'alert' => $message,
                        'status' => $status,
                        'sound' => 'default'
                    );
                } else if ($status == 5) {
                    $fare        = $pushmessage['fare'];
                    $pickup      = $pushmessage['pickup'];
                    $body['aps'] = array(
                        'alert' => $message,
                        'fare' => $fare,
                        'pickup' => $pickup,
                        'status' => $status,
                        'sound' => 'default'
                    );
                } else if ($status == 6) {
                    $trip_id     = $pushmessage['trip_id'];
                    $body['aps'] = array(
                        'alert' => $message,
                        'trip_id' => $trip_id,
                        'status' => $status,
                        'sound' => 'default'
                    );
                } else if ($status == 7) {
                    $trip_id     = $pushmessage['trip_id'];
                    $body['aps'] = array(
                        'alert' => $message,
                        'trip_id' => $trip_id,
                        'status' => $status,
                        'sound' => 'default'
                    );
                } else {
                    $body['aps'] = array(
                        'alert' => $message,
                        'status' => $status,
                        'sound' => 'default'
                    );
                }
                //print_r($body);		
                // Encode the payload as JSON
                $payload = json_encode($body);
                //print_r($payload);//exit;
                //
                // Build the binary notification
                $msg     = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
                // Send it to the server
                $result  = fwrite($fp, $msg, strlen($msg));
                /*if (!$result)
                echo 'Message not delivered' . PHP_EOL;
                else
                echo 'Message successfully 
                * delivered' . PHP_EOL;*/
                // Close the connection to the server
                fclose($fp);
            }
        } else {
        }
    }
    /******** reg_passenger_first_trip ***********/
    public function reg_passenger_first_trip($passengers_id)
    {        
         $arguments = array(array('$lookup'=>array(
							'from'=>MDB_TRANSACTION,
							'localField'=>"_id",
							'foreignField'=>"passengers_log_id",
							 'as'=>"transaction"        
						)),
						array('$unwind'=>'$transaction'),
						array('$match'=> array(
							'passengers_id'=>(int)$passengers_id,
							'travel_status'=> 1,
							'driver_reply'=>"A",
							'msg_status'=>"R"     
						)),
						array('$project' => array(
							'passengers_log_id' => '$transaction.passengers_log_id'			
						))					
					);
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        return (count($result['result'])>0) ? 1 : 0; 
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
    /** Credit card delete function **/
    public function delete_credit_card($passenger_cardid, $passenger_id)
    {		 
		$delete = $this->mongo_db->updateOne(MDB_PASSENGERS,array( '_id' =>  (int)$passenger_id),
											array('$pull' => array('creditcard_details' => array('passenger_cardid' => (int)$passenger_cardid))));
		return (empty($delete->getwriteErrors())) ? 1 : 0;
    }
    
	public function driver_ratings($driver_id)
    {
		$result = array();
		$arguments = array(
			array('$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'driver_id',
					'foreignField' => '_id',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
			array('$match' => array(
					'driver_id' => (int)$driver_id,
					'travel_status' => 1,
					'driver_reply' => 'A'
				)
			),
			array(
				'$project' => array(
					'passengers_log_id' => '$_id',
					'passengers_id' => '$passengers_id',
					'driver_id' => '$driver_id',
					'taxi_id' => '$taxi_id',
					'company_id' => '$company_id',
					'current_location' => '$current_location',
					'pickup_latitude' => '$pickup_latitude',
					'pickup_longitude' => '$pickup_longitude',
					'drop_location' => '$drop_location',
					'drop_latitude' => '$drop_latitude',
					'drop_longitude' => '$drop_longitude',
					'rating' => '$rating',
					'comments' => '$comments',
					'driver_comments' => '$driver_comments',
					'salutation' => '$people.salutation',
					'name' => '$people.name',
					'lastname' => '$people.lastname',
					'email' => '$people.email',
					'photo' => '$people.photo',
					'device_token' => '$people.device_token',
					'device_type' => '$people.device_type',
				)
			),
			array(
				'$sort' => array('createdate' => -1)
			),
			array(
				'$skip' => 0
			),
			array(
				'$limit' => 5
			)
		);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr['id'] = isset($re['passengers_log_id'])?$re['passengers_log_id']:'';
				$temp_arr['passengers_id'] = isset($re['passengers_id'])?$re['passengers_id']:'';
				$temp_arr['driver_id'] = isset($re['driver_id'])?$re['driver_id']:'';
				$temp_arr['taxi_id'] = isset($re['taxi_id'])?$re['taxi_id']:'';
				$temp_arr['company_id'] = isset($re['company_id'])?$re['company_id']:'';
				$temp_arr['lastname'] = isset($re['lastname'])?$re['lastname']:'';
				$temp_arr['current_location'] = isset($re['current_location'])?$re['current_location']:'';
				$temp_arr['pickup_latitude'] = isset($re['pickup_latitude'])?$re['pickup_latitude']:'';
				$temp_arr['pickup_longitude'] = isset($re['pickup_longitude'])?$re['pickup_longitude']:'';
				$temp_arr['drop_location'] = isset($re['drop_location'])?$re['drop_location']:'';
				$temp_arr['drop_latitude'] = isset($re['drop_latitude'])?$re['drop_latitude']:'';
				$temp_arr['drop_longitude'] = isset($re['drop_longitude'])?$re['drop_longitude']:'';
				$temp_arr['rating'] = isset($res['rating'])?$re['rating']:'0';
				$temp_arr['comments'] = isset($re['comments'])?$re['comments']:'';
				$temp_arr['driver_comments'] = isset($re['driver_comments'])?$re['driver_comments']:'';
				$temp_arr['salutation'] = isset($re['salutation'])?$re['salutation']:'';
				$temp_arr['name'] = isset($re['name'])?$re['name']:'';
				$temp_arr['lastname'] = isset($re['lastname'])?$re['lastname']:'';
				$temp_arr['email'] = isset($re['email'])?$re['email']:'';
				$temp_arr['photo'] = isset($re['photo'])?$re['photo']:'';
				$temp_arr['device_token'] = isset($res['device_token'])?$re['device_token']:'';
				$temp_arr['device_type'] = isset($re['device_type'])?$re['device_type']:'';
				
				$result[] = $temp_arr;
			}			
		}
		//echo '<pre>';print_r($result);exit;
        return $result;
    }
    // Get Payment type name
    public function get_payment_name($payment_id)
    {
		$result = $this->mongo_db->findOne(MDB_PAYMENT_MODULES,array('_id' => (int)$payment_id), array('pay_mod_name','pay_mod_image') );
		return (!empty($result))?$result:array();
    }
    public function checktrans_details($log_id)
    {
		$result = $this->mongo_db->findOne(MDB_TRANSACTION,array('passengers_log_id' => (int)$log_id),array('_id'));
		$results = array();
		if(count($result) > 0){
			$results[]['id'] = $result['_id'];
		}
		return (!empty($results) ? $results : array());
    }
    //Function used to get the get_driver ongoign trips   
	public function get_driver_current_ongoigtrips($id, $msg_status, $driver_reply = null, $travel_status = null, $company_id, $start = null, $limit = null)
    {
		  if ($company_id == '') {
            if (TIMEZONE) {
                $current_time = convert_timezone('now', TIMEZONE);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:00';
                $end_time     = $current_date[0] . ' 23:59:59';
                $date         = $current_date[0] . ' %';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:00';
                $end_time     = date('Y-m-d') . ' 23:59:59';
                $date         = date('Y-m-d %');
            }
        } else {
			$time_arguments = array(array('$match'=>array('_id'=>(int)$company_id)),array('$unwind'=>'$companydetails'),array('$project'=>array('time_zone'=>'$companydetails.time_zone')));            
            $time = $this->mongo_db->aggregate(MDB_COMPANY,$time_arguments); 
			$timezone_fetch = $time['result'];
			 if ($timezone_fetch[0]['time_zone'] != '') {
                $current_time = convert_timezone('now', $timezone_fetch[0]['time_zone']);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:00';
                $end_time     = $current_date[0] . ' 23:59:59';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:00';
                $end_time     = date('Y-m-d') . ' 23:59:59';
            }
        }		
		$match_array = array();
		$match_array['driver_id'] = (int)$id;
		$match_array['msg_status'] = $msg_status;
		$match_array['driver_reply'] = $driver_reply;
		$match_array['travel_status'] = array('$in' => array(2,3,5,9));
		$match_array['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($start_time)));
        $arguments = array(
			array('$lookup'=>array(
				'from'=>MDB_PASSENGERS,
				'localField'=>"passengers_id",
				'foreignField'=>"_id",
				 'as'=>"passengers"        
			)),
			array('$unwind'=>'$passengers'),
			array('$lookup'=>array(
				'from'=>MDB_TRANSACTION,
				'localField'=>"_id",
				'foreignField'=>"passengers_log_id",
				 'as'=>"trans"
			)),
			array('$match'=>$match_array),
			array('$project' =>
				array(
					'_id' => 0,
					'passenger_name'=>'$pass.name',
					'passenger_phone'=>'$passengers.phone',
					'profile_image'=>'$passengers.profile_image',
					'passengers_log_id'=>'$_id',
					'pickup_location'=>'$current_location',
					'drop_location'=>'$drop_location',
					'pickup_longitude'=>'$pickup_longitude',
					'pickup_latitude'=>'$pickup_latitude',
					'drop_latitude'=>'$drop_latitude',
					'drop_longitude'=>'$drop_longitude',
					'travel_status'=>'$travel_status',
					'notes'=>'$notes_driver',
					'distance'=>'$distance',
					'waiting_hour'=>'$waitingtime',
					'bookby'=>'$bookby'
				)
			),
			array('$sort' => array('pickup_time' => 1)),
		);
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        return (!empty($result['result'])?$result['result']:array()); 
    }
    /***************** Calculate ETA Time *********************/
    public function estimated_time($distance, $taxi_speed)
    {
        $ttime = "";
        if ($distance != 0 && $taxi_speed != 0) {
            $time = $distance / $taxi_speed;
            //Titanium.API.info("Response ETA" + distance + "-" + taxi_speed);					                                                                          
            $time = $time * 3600; // time duration in seconds
            $days = floor($time / (60 * 60 * 24));
            $time -= $days * (60 * 60 * 24);
            $hours = floor($time / (60 * 60));
            $time -= $hours * (60 * 60);
            $minutes = floor($time / 60);
            $time -= $minutes * 60;
            $seconds = floor($time);
            $time -= $seconds;
            if ($minutes > 0) {
                $ttime .= $minutes . __('Min') + ":";
            }
            if ($seconds > 0) {
                $ttime .= $seconds . __('Sec');
            }
        } else {
            $ttime = 1;
        }
        return $ttime;
    }
    public function add_rejected_list($post, $rejection_type)
    {
		$id = Commonfunction::get_auto_id(MDB_REJECTION_HISTORY);
		$insert_data = array('_id'=>$id,
			'driver_id' => (int)$post['driver_id'],
            'passengers_log_id' => (int)$post['passengers_log_id'],
            'passengers_id' => (int)$post['passengers_id'],
            'reason' => $post['reason'],
            'rejection_type' => (int)$rejection_type,
            'createdate' => Commonfunction::MongoDate(strtotime($post['createdate']))
		);
		$result = $this->mongo_db->insertOne(MDB_REJECTION_HISTORY,$insert_data);
    }
    public function get_passenger_phone_by_id($id)
    {         
		$result = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$id),array('phone'=>'phone'));
        return (isset($result)? $result['phone'] : "");
    }
    public function get_driver_phone_by_id($id)
    {
		$arguments = array(
			array('$lookup' 		=> array(
					'from'			=>	MDB_PEOPLE,
					'localField'	=> '_id',
					'foreignField'	=> "login_country",
					'as'			=> "people"
				)
			),
			array('$unwind' => '$people'),
			array('$match'	=> array('people._id'=>(int)$id)),
			array(
				'$project' => array('_id'=>0,
					'telephone_code'=>'$telephone_code',
					'phone' => '$people.phone',
				)
			)
		);
        $result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
		$telephone_code = (!empty($result['result'][0]['telephone_code']))?$result['result'][0]
		['telephone_code']:"";
		$phone = (isset($result['result'][0]['phone']))?$result['result'][0]['phone']:"";
		return $telephone_code.$phone;
    }
    
    public function send_sms($number, $msg)
    {
		$result = $this->mongo_db->findOne(MDB_CSC,array('default' => 1),array('telephone_code'));
        $to          = str_replace('+', '', $result['telephone_code'] . $number);
        require_once(DOCROOT . 'application/vendor/mobility_sms/includeSettings.php');
        $userAccount = '';
        $passAccount = '';
        $timeSend    = time();
        $dateSend    = 0;
        $deleteKey   = 0;
        $viewResult  = 1;
        $sender      = "Taximobility";
        $sms_send    = sendSMS($userAccount, $passAccount, $to, $sender, $msg, $timeSend, $dateSend, $deleteKey, $viewResult);
        return $sms_send;
    }
    /** Get driver job status **/
    public function get_request_status($trip_id = "")
    {        
		$result = $temp_arr = array();
        $res = $this->mongo_db->findOne(MDB_REQUEST_HISTORY,array('_id'=> (int)$trip_id),
					array('available_drivers','rejected_timeout_drivers','total_drivers',
						'selected_driver','status','trip_type'));
		if(!empty($res)){
			$temp_arr['available_drivers'] =  $res['available_drivers'];
			$temp_arr['rejected_timeout_drivers'] =  $res['rejected_timeout_drivers'];
			$temp_arr['total_drivers'] =  $res['total_drivers'];
			$temp_arr['selected_driver'] =  $res['selected_driver'];
			$temp_arr['status'] =  $res['status'];
			$temp_arr['trip_type'] =  isset($res['trip_type'])?$res['trip_type']:'';
			$result[] = $temp_arr;
		}
        return $result;
    }
    public function get_location_details($trip_id)
    {
		$args = array(array('$match' => array('_id' => (int)$trip_id)),
					  array('$lookup' => array(
											   'from' => MDB_LOCATION_HISTORY,
											   'localField' => '_id',
											   'foreignField' => 'trip_id',
											   'as' => 'location')),
					  array('$unwind' => '$loc'),
					  array('$project' => array(
											   'current_location' => '$current_location',
											   'drop_location' => '$drop_location',
											   'active_record' => '$loc.coordinates',
											   'drop_latitude' => '$drop_latitude',
											   'drop_longitude' => '$drop_longitude',
											   'pickup_latitude' => '$pickup_latitude',
											   'pickup_longitude' => '$pickup_longitude'
											))					  
					);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		return (!empty($result['result']) ? $result['result'] : array());
    }
    public function get_rejected_drivers($driver_id, $company_id)
    {
        $get_company_time_details = $this->get_company_time_details($company_id);
        $start_time               = $get_company_time_details['start_time']; 
        $end_time                 = $get_company_time_details['end_time']; 
        //echo  $driver_id.'//'.$end_time;exit;
        $match = array('driver_id'=> (int)$driver_id,
						'createdate'=>array('$gte' => Commonfunction::MongoDate(strtotime($start_time)),'$lte' => Commonfunction::MongoDate(strtotime($end_time)))
						);
        $res = $this->mongo_db->count(MDB_REJECTION_HISTORY,$match);
        return (isset($res)) ? $res : 0 ;		
    }
    /********************* Check any new job request for the driver ***********************/
    public function check_new_request($driver_id, $company_all_currenttimestamp)
    {
        $datetime    = explode(' ', $company_all_currenttimestamp);
        $currentdate = $datetime[0] . ' 00:00:01';
        //echo $currentdate;exit;
		$arguments = array(array('$match' => array('status' => 0,
												   'selected_driver' => (int)$driver_id,
												   'createdate' => array('$gte' => Commonfunction::MongoDate(strtotime($currentdate)))
											   )),
							array('$project' => array('trip_id' => '$_id','available_drivers' => '$available_drivers')),
							array('$sort'=>array('_id'=>-1)),
							array('$limit' => 1)
						);
		$result = $this->mongo_db->aggregate(MDB_REQUEST_HISTORY,$arguments);
		//print_r($driver_id); exit;
		$res = !empty($result['result']) ? $result['result'] : array();
        if (!empty($res)) {
            $trip_id = $res[0]['trip_id'];            
        } else {
            $trip_id = '0';
        }
        return $trip_id;
    }
    /****************** Select driver request ******************/
    public function get_driver_request($trip_id)
    {
		$result = array();
		$res = $this->mongo_db->findOne(MDB_REQUEST_HISTORY,array('_id' => (int)$trip_id ),array('available_drivers', 'total_drivers', 'rejected_timeout_drivers', 'status','trip_type','driver_limit','actual_limit'));
		if(!empty($res)){
			$res['trip_type'] = isset($res['trip_type']) ? $res['trip_type']:0;
			$res['driver_limit'] = isset($res['driver_limit']) ? $res['driver_limit']:0;
			$res['actual_limit'] = isset($res['actual_limit']) ? $res['actual_limit']:0;
			$result[] = $res;
		}
		return $result;
    }
    public static function company_model_details($default_companyid)
    {
		$mongo_db = MangoDB::instance('default');
		$result = $temp_arr = array();
		$company_id = $default_companyid;
        if (FARE_SETTINGS == 2 && $company_id != "") {
			$arguments = array(array('$unwind'=>array('$model_fare')),
							   array($lookup =>
									array('from' => MDB_MOTOR_MODEL,
											'localField' => 'model_fare.model_id',
											'foreignField' => '_id',
											'as' => 'motor_model')
									),
							   array('$unwind'=>array('$motor_model')),
							   array('$match'=>array('_id'=> 1,'model_fare.fare_status'=>'A')),
							   array('$group'=>array(
									'_id'=>array('model_id'=>'$motor_model._id',
									'model_name' => '$model_fare.model_name',
									'model_size' => '$model_fare.model_size',
									'base_fare' => '$model_fare.base_fare',
									'min_fare' => '$model_fare.min_fare',
									'min_km' => '$model_fare.min_km',
									'below_above_km' => '$model_fare.below_above_km',
									'below_km' => '$model_fare.below_km',
									'above_km' => '$model_fare.above_km',
									'cancellation_fare' => '$model_fare.cancellation_fare',
									'night_charge' => '$model_fare.night_charge',
									'night_timing_from' => '$model_fare.night_timing_from',
									'night_timing_to' => '$model_fare.night_timing_to',
									'night_fare' => '$model_fare.night_fare',
									'evening_charge' => '$model_fare.evening_charge',
									'evening_timing_from' => '$model_fare.evening_timing_from',
									'evening_timing_to' => '$model_fare.evening_timing_to',
									'evening_fare' => '$model_fare.evening_fare',
									'evening_fare' => '$model_fare.waiting_time',
							   ))),
							   array('$sort'=>array('_id.model_id'=>1))
							);            
			$res = $mongo_db->aggregate(MDB_COMPANY,$arguments);
        } else {
			$arguments = array(array('$match'=>array('model_status'=> 'A')),
							   array('$project'=>array(
									'model_id' => '$_id',
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
									'night_timing_from' => '$night_timing_from',
									'night_timing_to' => '$night_timing_to',
									'night_fare' => '$night_fare',
									'evening_charge' => '$evening_charge',
									'evening_timing_from' => '$evening_timing_from',
									'night_fare' => '$night_fare',
									'evening_charge' => '$evening_charge',
									'evening_timing_from' => '$evening_timing_from',
									'evening_timing_to' => '$evening_timing_to',
									'evening_fare' => '$evening_fare',
									'waiting_fare' => '$waiting_time'
							   ))							   
							);            
			$res = $mongo_db->aggregate(MDB_MOTOR_MODEL,$arguments);
        }
        
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr = $r;
				$temp_arr['waiting_fare'] = isset($temp_arr['waiting_fare']) ? $temp_arr['waiting_fare'] : 0;
				$result[] = $temp_arr;
			}
		}
		
		return $result;
    }
    public function get_modelfare_details($default_companyid, $taxi_model)
    {
        //$default_companyid=1;
        if ($default_companyid != "") {
             $arguments = array(
						array('$unwind'=>'$model_fare'),
						array('$match'=>array('_id'=>(int)$default_companyid,
								'model_fare.fare_status'=>'A',
								'model_fare.model_id'=>(int)$taxi_model)),
						array('$project' => array(
							'model_id' => '$model_fare.model_id',
							'model_name' => '$model_fare.model_name',
							'model_size' => '$model_fare.model_size',
							'motor_mid' => '$model_fare.motor_mid',
							'base_fare' => '$model_fare.base_fare',
							'min_km' => '$model_fare.min_km',
							'min_fare' => '$model_fare.min_fare',
							'cancellation_fare' => '$model_fare.cancellation_fare',
							'below_above_km' => '$model_fare.below_above_km',
							'below_km' => '$model_fare.below_km',
							'above_km' => '$model_fare.above_km',
							'night_fare' => '$model_fare.night_fare',
							'waiting_time' => '$model_fare.waiting_time',
							'minutes_fare' => '$model_fare.minutes_fare'
						))
					);
				$res = $this->mongo_db->aggregate(MDB_COMPANY,$arguments);
				$result = !empty($res['result']) ? $res['result'] : array();
        } else {       
            			$project = array(
							'model_name',
							'model_size',
							'motor_mid',
							'base_fare',
							'min_km',
							'min_fare',
							'cancellation_fare',
							'below_above_km',
							'below_km',
							'above_km',
							'night_fare',
							'waiting_time',
							'taxi_speed',
							'minutes_fare',
							'description');
							
            $res = $this->mongo_db->findOne(MDB_MOTOR_MODEL,array('_id'=> (int)$taxi_model),$project);            
			if(!empty($res)){
				$res['model_id'] = $res['_id'];
				$res['description'] = isset($res['description'])?$res['description']:'';
			}
			$result[] = !empty($res) ? $res : array();
        }
        return $result;
    }
    public function get_driver_taxi_speed($taxi_id)
    {
		/*$result = $this->mongo_db->find(MDB_TAXI,array('_id' => (int)$taxi_id,'taxi_status' => 'A'),array('taxi_speed'))->sort(array('_id' => -1))->skip(0)->limit(1);*/
		$options=[
				'projection'=>[
				   'taxi_speed'=>1,                               
					],
				'sort'=>[
					'_id'=>-1
					 ],
				'skip' =>0,
				'limit'=>1
			];
		$result = $this->mongo_db->find(MDB_TAXI,array('_id' => (int)$taxi_id,'taxi_status' => 'A'),$options);
		
        if (count($result) > 0) {
			$result = Commonfunction::change_key($result);
            return $result[0]['taxi_speed'];
        } else {
            return 0;
        }
    }
    public function getpromodetails($promo_code = "", $passenger_id = "")
    {        
        $promo_fetch = $this->mongo_db->findOne(MDB_PASSENGERS_PROMO,array('promocode'=>$promo_code,'passenger_id'=>(int)$passenger_id),array("promocode","promo_discount","promo_used","promo_limit"));
        $promo_trip_count=$promo_user_count=$promo_wallet_count=0;
        if (count($promo_fetch) > 0) {
            $promocode        = $promo_fetch['promocode'];
            $promo_discount   = $promo_fetch['promo_discount'];
            $promo_used       = $promo_fetch['promo_used'];
            $promo_limit      = $promo_fetch['promo_limit'];          
            //$promo_user_count = $this->mongo_db->count(MDB_PASSENGERS_LOGS,array('promocode'=>$promo_code,'passenger_id'=>(int)$passenger_id));
            
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
                return -1;
            } else {
                return $promo_discount;
            }
        } else {
            return 0;
        }
    }
    public function update_promo_discount($passenger_log_id, $promocode, $referral_discount){ }
   
    /********************** Check Promo Code ***************/
    public function checkpromocode($promo_code = "", $passenger_id = "", $company_id = "")
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
            if ($company_id == '') {
                if (TIMEZONE) {
                    $current_time = convert_timezone('now', TIMEZONE);
                } else {
                    $current_time = date('Y-m-d H:i:s');
                }
            } else {
				$timezone_query = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$company_id),array('companydetails.time_zone'));
				$timezone = (isset($timezone_query) ? $timezone_query :array());
                if (isset($timezone['companydetails']['time_zone'])) {
                    $current_time = convert_timezone('now', $timezone['companydetails']['time_zone']);
                } else {
                    $current_time = date('Y-m-d H:i:s');
                }
            }
			
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
	
    //To get the passenger cancel request data
    public function get_passenger_cancel_request_data($driver_id = "", $company_id = "")
    {
		$get_company_time_details = $this->get_company_time_details($company_id);
        $current_time             = $get_company_time_details['current_time']; // Current Time
		$match_array = array(
						'driver_id' => (int)$driver_id,
						'travel_status' => 4,
						'notification_status' => array('$nin' => array(4,5)),
						'createdate' => Commonfunction::MongoDate(strtotime($current_time))
					);
		$arguments = array(
			array('$match'=>$match_array),
			array('$group' => array(
				'_id' => NULL,
				'total_amount' => array('$sum' => '$transaction.fare')
			)),
			array('$project'=>array(
				'trip_id'=>'$_id',
				'status'=>'$travel_status',
				'notification_status'=>'$notification_status',
			))
		);
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		//print_r($result);exit;
        $result = (!empty($result['result']) ? $result['result']:array());
        return $result;
    }
 
	public function get_past_bookings($userid = "", $status = "", $driver_reply = "", $createdate = "", $start = null, $limit = null, $company_id)
    {
		$get_company_time_details = $this->get_company_time_details($company_id);
        $start_time  = $get_company_time_details['start_time']; //Start time
		$match_query = array();
		$match_query['split_details.friends_p_id'] = (int)$userid;
		$match_query['driver_reply'] = $driver_reply;
		$match_query['travel_status'] = (int)$status;
		if ($createdate == 0) {
			$match_query['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($start_time)));
        }
		 if ($company_id != "" && $company_id != 0 ) {
			$match_query['company_id'] = (int)$company_id;
        }
		$arguments = array(
			array('$unwind' => '$split_details'),
			array('$lookup' 		=> array(
                    'from'			=>	MDB_TRANSACTION,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "trans"
                )
            ),
			array('$lookup' =>
				array(
					'from'=>MDB_PASSENGERS,
					'localField'=> "passengers_id",
					'foreignField' => "_id",
					'as'=> "passenger"
				)
            ),
			array('$lookup' 		=> array(
                    'from'			=>	MDB_TAXI,
                    'localField'	=> "taxi_id",
                    'foreignField'	=> "_id",
                    'as'			=> "taxi"
                )
            ),
			array('$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'driver_id',
					'foreignField' => "_id",
					'as' => "people"
				)
			),
			array('$unwind' => '$people'),
			/*array('$lookup' => array(
					'from' => MDB_SPLIT_LOG,
					'localField' => '_id',
					'foreignField' => 'trip_id',
					'as' => 'split_log'
				)
			),*/
			array('$match' => $match_query),
			array('$project' =>
				array('_id' => 0,
					'passengers_log_id'=>'$_id',
					'pickup_location'=>'$current_location',
					'drop_location'=>'$drop_location',
					'pickup_longitude'=>'$pickup_longitude',
					'pickup_latitude'=>'$pickup_latitude',
					'drop_longitude'=>'$drop_longitude',
					'drop_latitude'=>'$drop_latitude',
					'pickup_time' => '$actual_pickup_time',
					'travel_status'=>'$travel_status',
					'pickup_location'=>'$current_location',
					'drop_location'=>'$drop_location',
					'passenger_name'=>'$passenger.name',
					'passengers_log_id'=>'$_id',
					'driver_id'=>'$people._id',
					'notes_driver'=>'$passenger.notes_driver',
					 'name'=>'$people.name',
					'lastname'=>'$people.lastname',
					'drop_time'=>'$passenger.drop_time',
					'drivername' => array('$concat' => array('$people.name', ' ', '$people.lastname')),
					'trip_duration' => array('$subtract' => array(array('$cond' => array(array('$eq' => array('$actual_pickup_time',Commonfunction::MongoDate(strtotime('1969-12-31 00:00:00')))),'$pickup_time','$actual_pickup_time')),'$drop_time')),
				)
			),
			array('$sort' => array('_id' => -1)),
			array('$skip' => (int)$start),
			array('$limit' => (int)$limit)
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		return (!empty($result['result'])?$result['result']:array());
    }
	
	public function get_pending_bookings($company_id, $pagination, $userid = "", $travelstatus = "", $driver_reply = "", $createdate = "", $start = null, $limit = null)
    {
		$result = array();
        $get_company_time_details = $this->get_company_time_details($company_id);
        $start_time = $get_company_time_details['start_time']; //Start time
		$end_time = $get_company_time_details['end_time']; //end time
		$current_time = $get_company_time_details['current_time']; // Current Time
			
		$match_query = array();
		$match_query[]['split_details.friends_p_id'] = (int)$userid;
		$match_query[]['split_details.approve_status'] = 'A';
		$or1 = array('$or' => array(array('driver_reply' => $driver_reply), 
									array('driver_reply' => array('$eq'=>'')),
									array('driver_reply' =>  array( '$exists' =>  false))
					));
		
		if ($createdate == 0) {
			$yesterday = date('Y-m-d H:i:s',strtotime($start_time . "-1 days"));
			$match_query[]['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($yesterday)));
        }
        
		$and = array('$or' => array(
								array('$and'=>array(array('travel_status'=> 0), array('bookingtype'=> 2))),
								array('travel_status' => array('$in' => array(9,2,3,5))))
					);
		$match = array('$and' => array_merge($match_query, array($or1), array($and)));
		//print_r($match);exit;
		if ($pagination == 1) {
			$field_arguments = array(
				array('$sort' => array('pickup_time' => 1)),
				array('$skip' => (int)$start),
				array('$limit' => (int)$limit)
			);
        } else {
			$field_arguments = array(array('$sort' => array('travel_status' => -1)));
        }
		$common_arguments = array(
			array('$unwind' => '$split_details'),
			array('$lookup' =>
				array(
					'from'=>MDB_PASSENGERS,
					'localField'=> "passengers_id",
					'foreignField' => "_id",
					'as'=> "passengers"
				)
            ),
            array('$unwind'=>'$passengers'),
			array('$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'driver_id',
					'foreignField' => "_id",
					'as' => "people"
				)
			),
			array('$unwind' =>  array( 'path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true )),
			array('$lookup' 		=> array(
                    'from'			=>	MDB_TAXI,
                    'localField'	=> "taxi_id",
                    'foreignField'	=> "_id",
                    'as'			=> "taxi"
                )
            ),
            array('$unwind' =>  array( 'path' =>  '$taxi', 'preserveNullAndEmptyArrays' =>  true )),
			array('$lookup'=>array(
				'from'=>MDB_MOTOR_MODEL,
				'localField'=>"taxi.taxi_model",
				'foreignField'=>"_id",
				 'as'=>"motor_model"        
			)),
			array('$unwind' =>  array( 'path' =>  '$motor_model', 'preserveNullAndEmptyArrays' =>  true )),
			array('$match' => $match),
			array('$project' =>
				array(
					'passengers_log_id' => '$_id',
					'pickup_location'=>'$current_location',
					'drop_location'=>'$drop_location',
					'pickup_latitude'=>'$pickup_latitude',
					'pickup_longitude'=>'$pickup_longitude',
					'drop_latitude'=>'$drop_latitude',
					'drop_longitude'=>'$drop_longitude',
					'travel_status'=>'$travel_status',
					'pickup_location'=>'$current_location',
					'notes_driver'=>'$notes_driver',
					'waitingtime'=>'$waitingtime',
					'distance'=>'$distance',
					'pickuptime' => array('$ifNull' => array('$actual_pickup_time',array('$pickup_time','$actual_pickup_time'))),
					//'pickup_time' => '$pickup_time',
					'drivername'=>'$people.name',
					'driver_id'=>'$people._id',
					'passenger_name'=>'$pass.name',
					'model_name' => '$motor_model.model_name',
					'taxi_no' => '$taxi.taxi_no',
					'profile_image' => '$people.profile_picture'
				)
			),
			array('$sort' => array('pickup_time' => 1))							
		);
		$arguments = array_merge($common_arguments,$field_arguments);
		
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		//~ print_r($res);exit;
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr = $r;
				$temp_arr['travel_status'] = isset($temp_arr['travel_status']) ? $temp_arr['travel_status'] :'';
				$temp_arr['model_name'] = isset($temp_arr['model_name']) ? $temp_arr['model_name'] :'';
				if(count($temp_arr['pickuptime'])>1){
					if(!empty($temp_arr['pickuptime'])){
						$pickuptime = (!empty($temp_arr['pickuptime'][0])) ? $temp_arr['pickuptime'][0] : ((!empty($temp_arr['pickuptime'][1])) ? $temp_arr['pickuptime'][1] : '');
						$pickuptime = (!empty($pickuptime)) ? Commonfunction::convertphpdate('Y-m-d h:i:s',$pickuptime) : '';
					}
				}else{
					$pickuptime = !empty($temp_arr['pickuptime']) ? Commonfunction::convertphpdate('Y-m-d h:i:s',$temp_arr['pickuptime']) : '';
				}
				$temp_arr['pickuptime'] = $pickuptime;
									
				//$temp_arr['pickuptime'] = isset($temp_arr['pickuptime']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['pickuptime']) : '';
				$result[] = $temp_arr;
			}
		}
		//~ print_r($result);exit;
		return $result;
    }
   
	public function driver_past_bookings($pagination, $booktype, $id, $msg_status, $driver_reply = null, $travel_status = null, $start = null, $limit = null, $default_companyid = null)
    {
        $get_company_time_details = $this->get_company_time_details($default_companyid);
        $start_time               = $get_company_time_details['start_time']; //Start time
        $end_time                 = $get_company_time_details['end_time']; //end time
        $current_time             = $get_company_time_details['current_time']; // Current Time
		$match_query = $result = array();
		$match_query['driver_id'] = (int)$id;
		$match_query['msg_status'] = $msg_status;
		$match_query['driver_reply'] = $driver_reply;
		$match_query['travel_status'] = (int)$travel_status;
		if ($booktype == 2) {
			$match_query['booking_from'] = (int)$booktype;
        } else {
			//$match_query['booking_from'] = array('$ne' => 2);
        }
		if ($pagination == 1) {
			$custom_arguments = array(
				array(
					'$sort' => array(
						'pickup_time' => -1
					)
				),
				array( '$skip' => (int)$start),
				array( '$limit' => (int)$limit)
			);
        } else {
			$custom_arguments = array(
				array(
					'$sort' => array(
						'pickup_time' => -1
					)
				),
			);
        }
        $common_arguments = array(        
            array('$match' => $match_query),	
			array(
                '$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => 'passengers_id',
                    'foreignField' => "_id",
                    'as' => "passengers"
                )
            ),
            array('$unwind' =>  array( 'path' =>  '$passengers', 'preserveNullAndEmptyArrays' =>  true )),
            array(
                  '$lookup' => array(
                    'from' => MDB_TRANSACTION,
                    'localField' => '_id',
                    'foreignField' => "passengers_log_id",
                    'as' => "trans"
                )
            ),
            array('$unwind' =>  array( 'path' =>  '$trans', 'preserveNullAndEmptyArrays' =>  true )),
			array(
                '$lookup' => array(
                    'from' => MDB_LOCATION_HISTORY,
                    'localField' => 'driver_id',
                    'foreignField' => "_id",
                    'as' => "dloc"
                )
            ),	
            array('$unwind' =>  array( 'path' =>  '$dloc', 'preserveNullAndEmptyArrays' =>  true )),
			array('$project' => 
				array('actual_pickup_time' => array('$cond' =>  
				array('if' =>  array('$eq' =>  array( '$actual_pickup_time', Commonfunction::MongoDate('0000-00-00 00:00:00'))), //Don't use strtotime for 0000-00-00 00:00:00
					'then' =>  '$pickup_time', 'else' => '$actual_pickup_time' )), 
				'passengers_log_id' => '$_id',
				'pickup_location' => '$current_location',
				'tax_percentage' => '$company_tax',
				'tax' =>  array( '$ifNull' =>  array('$trans.company_tax',0)),
				'sub_total' =>  array( '$ifNull' =>  array('$trans.tripfare',0)),
				'total' =>  array( '$ifNull' =>  array('$trans.amt',0)),
				'promocode' =>  array( '$ifNull' =>  array('$trans.promo_discount_fare',0)),
				'passenger_name' => '$pass.name',
				'profile_image' => '$pass.profile_image',
				'drop_location' =>  array( '$ifNull' =>  array('$drop_location',0)),
				'drop_location' =>  array( '$ifNull' =>  array('$drop_location',0)),
				'travel_status' => '$travel_status',
				'wallet' => '$used_wallet_amount',
				'payment_type' => '$trans.payment_type',
				'bookby' => '$bookby',
				'pickup_longitude' => '$pickup_longitude', 
				'pickup_latitude' => '$pickup_latitude',
				'drop_latitude' => '$drop_latitude', 
				'drop_longitude' => '$drop_longitude', 
				'travel_status' => '$travel_status', 
				'notes' => '$notes_driver', 
				'distance' => '$distance',
				'drop_time' => '$drop_time',
				'trip_duration' => array('$cond' => array('if' =>  
													array('$eq' => array('$actual_pickup_time', Commonfunction::MongoDate('0000-00-00 00:00:00')) ),//Don't use strtotime for 0000-00-00 00:00:00
										'then' =>  '$pickup_time', 'else' => '$actual_pickup_time' )), 
				'metric' =>  array( '$ifNull' =>  array('$trans.distance_unit',0)),
				'waiting_fare' =>  array( '$ifNull' =>  array('$trans.waiting_cost',0)),
				'waitingtime' => '$waitingtime',
				'twaiting_hour' =>  array( '$ifNull' =>  array('$trans.waiting_time',0)),
				'active_record' =>  array( '$ifNull' =>  array('$dloc.loc',0)),
				'distance_fare'  =>  array('$ifNull'  => array(array('$subtract'  => array('$trans.tripfare', '$trans.minutes_fare') ), 0)),
				'amt'  =>  array('$ifNull'  =>  array(array( '$sum'  => array('$trans.amt', '$used_wallet_amount') ), 0))
				)),
				array('$sort' => array('passengers_log_id' => -1))
        );
		$arguments = array_merge($common_arguments,$custom_arguments);
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
        
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr = $r;
				
				$temp_arr['pickup_time'] = isset($temp_arr['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['actual_pickup_time']) : '';
				$temp_arr['drop_time'] = isset($temp_arr['drop_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['drop_time']) : '';
				$temp_arr['trip_duration'] = isset($temp_arr['trip_duration']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['trip_duration']) : '';
				$temp_arr['distance_fare'] = isset($temp_arr['distance_fare']) ? $temp_arr['distance_fare']: 0;
				$temp_arr['distance'] = isset($temp_arr['distance']) ? $temp_arr['distance']: '';
				# Active record
				$active_record = isset($temp_arr['active_record']['coordinates']) ? $temp_arr['active_record']['coordinates']:array();				
				$coordinates='';
				if(!empty($active_record)){
					foreach($active_record as $a){
						$lat = '['.$a[1].',';
						$long = $a[0].'],';
						$coordinates .= $lat.$long;
					}
					($coordinates !='') ? $temp_arr['active_record'] = $coordinates : '';
				}	
				$temp_arr['active_record'] = $coordinates;
				$result[] = $temp_arr;
			}
		}
		return $result;
    }
	
    //Function used to get the get_driver ongoign trips
    public function driver_pending_bookings($id, $msg_status, $driver_reply = null, $travel_status = null, $company_id, $start = null, $limit = null)
    {
		
	    $get_company_time_details = $this->get_company_time_details($company_id);
        $start_time               = $get_company_time_details['start_time']; //Start time
        $end_time                 = $get_company_time_details['end_time']; //end time
        $current_time             = $get_company_time_details['current_time']; // Current Time
		$match_query = $result = array();
		$match_query['driver_id'] = (int)$id;
		$match_query['msg_status'] = $msg_status;
		$match_query['driver_reply'] = $driver_reply;
		$match_query['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($start_time)));
		$match_query['travel_status'] = array('$in' => array(2,5,3,9));

        $arguments = array(
			array(
                '$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => 'passengers_id',
                    'foreignField' => "_id",
                    'as' => "passengers"
                )
            ),
			array('$unwind' => array('path' => '$passengers', 'preserveNullAndEmptyArrays' => true )),
            array(
                  '$lookup' => array(
                    'from' => MDB_TRANSACTION,
                    'localField' => '_id',
                    'foreignField' => "passengers_log_id",
                    'as' => "trans"
                )
            ),
            array('$unwind' => array('path' => '$trans', 'preserveNullAndEmptyArrays' => true )),
			array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'driver_id',
                    'foreignField' => "_id",
                    'as' => "people"
                )
            ),
            array('$unwind' => array('path' => '$people', 'preserveNullAndEmptyArrays' => true )),
            array('$match' => $match_query),
			array(
                '$project' => array(
                    'pickup_time' => '$pickup_time',
					'pickup_longitude' => '$pickup_longitude',
					'pickup_latitude' => '$pickup_latitude',
					'drop_latitude' => '$drop_latitude',
					'drop_longitude' => '$drop_longitude',
					'travel_status' => '$travel_status',
					'notes' => '$notes_driver',
					'distance' => '$distance',
					'waiting_hour' => '$waitingtime',
					'bookby' => '$bookby',
					'drivername' => '$people.name',
					'passenger_name' => '$passengers.name',
					'passenger_profile_image' => '$passengers.profile_image',
					'passengers_log_id' => '$_id',
					'pickup_location' => '$current_location',
					'drop_location' => array('$ifNull' => array( '$drop_location', 0 ) ),
					'travel_status' => '$travel_status'
                )
            ),
            array(
                '$sort' => array(
                    'passengers_log_id' => -1
                )
            )
        );
        $res    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr = $r;
				$temp_arr['passengers_log_id'] = isset($temp_arr['passengers_log_id']) ? (string)$temp_arr['passengers_log_id']:'';
				$temp_arr['driver_name'] = isset($temp_arr['driver_name']) ? $temp_arr['driver_name']:'';
				$temp_arr['pickup_time'] = isset($temp_arr['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['pickup_time']) : '';
				$result[] = $temp_arr;
			}
		}
		//print_r($res);exit;
        return $result;
    }
	
    public function check_new_request_bydriver($driver_id, $company_all_currenttimestamp, $trip_id)
    {
		$result = $temp_arr = array();
		$datetime    = explode(' ', $company_all_currenttimestamp);
        $currentdate = $datetime[0] . ' 00:00:01';
		/*$res = $this->mongo_db->find(MDB_REQUEST_HISTORY,
										array('status' => 0, 'selected_driver' => (int)$driver_id,
											  '_id' => array('$ne' => (int)$trip_id),
											  'createdate' => array('$gte' => Commonfunction::MongoDate(strtotime($currentdate)))),
										array('_id','available_drivers','rejected_timeout_drivers'))
								->sort(array('_id' => -1));*/
								
		$options=[
				'projection'=>[
					'_id' =>1,
					'available_drivers' =>1,
					'rejected_timeout_drivers' =>1   
					],
				'sort'=>[
					'_id'=>-1
					]
			];
			
		$match = array('status' => 0, 'selected_driver' => (int)$driver_id, '_id' => array('$ne' => (int)$trip_id),
					  'createdate' => array('$gte' => Commonfunction::MongoDate(strtotime($currentdate)))
					  );
		$result = $this->mongo_db->find(MDB_REQUEST_HISTORY,$match,$options);
		$res = Commonfunction::change_key($result);
		$result = array();
		if(!empty($res)){
			foreach($res as $r){				
				$temp_arr['trip_id'] = isset($r['_id'])?$r['_id']:'';
				$temp_arr['available_drivers'] = isset($r['available_drivers'])?$r['available_drivers']:'';
				$temp_arr['rejected_timeout_drivers'] = isset($r['rejected_timeout_drivers']) ? $r['rejected_timeout_drivers']:'';
				$result[] = $temp_arr;
			}
		}
		return $result;
    }
    /********************* Check any new job request for the driver ***********************/
    public function check_new_request_tripid($taxi_id = null, $company_id = null, $trip_id, $driver_id, $company_all_currenttimestamp, $driver_reply, $operator_id = 0)
    {
        $datetime     = explode(' ', $company_all_currenttimestamp);
        $current_date = $datetime[0] . ' 00:00:01';
        $createdate   = isset($current_date) ? $current_date : $datetime;
		$createdate = Commonfunction::MongoDate(strtotime($createdate));
		$match = array('_id'=>(int)$trip_id,
						'selected_driver'=> (int)$driver_id,
						'status'=>array('$ne'=>4),
						'createdate'=>array('$gte'=> $createdate)
					  );
		$project = array('_id',
						 'available_drivers',
						 'total_drivers',
						 'rejected_timeout_drivers',
						 'status');
		$s_driver = '';
		$result = $this->mongo_db->findOne(MDB_REQUEST_HISTORY,$match,$project);
        if (count($result) > 0) {
            if ($driver_reply != 'C') {
                $available_drivers = (isset($result['available_drivers'])?$result['available_drivers']:'');
                $exp_drivers       = explode(',', $available_drivers);
                $s_array           = array();
                $first_driver      = isset($exp_drivers[0]) ? $exp_drivers[0] : 0;
                for ($i = 1; $i < count($exp_drivers); $i++) {
                    $s_array[]   = $exp_drivers[$i];
                    $temp_driver = isset($exp_drivers[1]) ? $exp_drivers[1] : $exp_drivers[0];
                }
                if (count($s_array) >0) {
                    $s_driver = implode(',', $s_array);
                }
                $prev_rejected_timeout_drivers = isset($result['rejected_timeout_drivers']) ? $result['rejected_timeout_drivers'] : "";
                if ($prev_rejected_timeout_drivers != "") {
                    $rejected_timeout_drivers = $prev_rejected_timeout_drivers . ',' . $driver_id;
                } else {
                    $rejected_timeout_drivers = $driver_id;
                }
                //to get the usertypes
                if ($operator_id != 0) {
					$user_type_detail = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=>(int)$operator_id),array('user_type'));
					$user_type_dets = (isset($user_type_detail)) ? $user_type_detail : array();
                }
                $temp_driver       = isset($temp_driver) ? $temp_driver : "";
                $update_trip_array = array(
                    "available_drivers" => $s_driver,
                    "selected_driver" => (int)$temp_driver,
                    "status" => 0,
                    "rejected_timeout_drivers" => $rejected_timeout_drivers
                );
                
                $update_result     = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY,array('_id'=>(int)$trip_id),array('$set'=>$update_trip_array),array('upsert'=>false));
                
                //to update driver request and passenger log if selected driver is empty
                if ($temp_driver == '') {
                    $update_trip_array_one = array(
                        "status" => 4
                    );
                    $update_result         = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY, array('_id'=>(int)$trip_id),array('$set'=>$update_trip_array_one),array('upsert'=>false));
                    if ($operator_id != 0 && $user_type_dets['user_type'] == 'A') {
                        $update_log_array_driver = array(
                            "driver_id" => 0,
                            "taxi_id" => 0,
                            "company_id" => 0
                        );
                    } else {
                        $update_log_array_driver = array(
                            "driver_id" => 0,
                            "taxi_id" => 0
                        );
                    }
                    $results = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$trip_id),array('$set'=>$update_log_array_driver),array('upsert'=>false));
                }
                $driver_details = $this->get_driver_taxi($temp_driver);
				$driver_details = reset($driver_details);
                $drivertaxi     = isset($driver_details['mapping_taxiid']) ? $driver_details['mapping_taxiid'] : $taxi_id;
                $drivercompany  = isset($driver_details['mapping_companyid']) ? $driver_details['mapping_companyid'] : $company_id;
                if ($operator_id != 0 && $user_type_dets['user_type'] == 'A') {
                    $update_log_array = array(
                        "driver_id" => (int)$temp_driver,
                        "taxi_id" => (int)$drivertaxi,
                        "company_id" => (int)$drivercompany
                    );
                } else {
                    $update_log_array = array(
                        "driver_id" => (int)$temp_driver,
                        "taxi_id" => (int)$drivertaxi
                    );
                }
                //print_r($update_log_array);exit;
                $pass_log_update          = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$trip_id),array('$set'=> $update_log_array),array('upsert'=>false));
                $update_driver_array      = array(
                    "status" => 'B'
                );
                $driver_tbl_update        = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id'=>(int)$driver_id),array('$set'=>$update_driver_array),array('upsert'=>false));
                $available_drivers        = explode(',', $result['total_drivers']);
                $rejected_timeout_drivers = explode(',', $rejected_timeout_drivers);
                $comp_result              = array_diff($available_drivers, $rejected_timeout_drivers);
                if (count($comp_result) == 0) {
                    $update_trip_array_one = array(
                        "status" => 4
                    );
                    $update_result         = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY, array('_id'=>(int)$trip_id),array('$set'=>$update_trip_array_one),array('$upsert'=>true));
                    if ($operator_id != 0 && $user_type_dets['user_type'] == 'A') {
                        $update_log_array_driver = array(
                            "driver_id" => 0,
                            "taxi_id" => 0,
                            "company_id" => 0
                        );
                    } else {
                        $update_log_array_driver = array(
                            "driver_id" => 0,
                            "taxi_id" => 0
                        );
                    }
                    $result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$trip_id),array('$set'=>$update_log_array_driver),array('$upsert'=>true));
                }
            } else {
                $drivertaxi    = $taxi_id; 
                $drivercompany = $company_id; 
                if ($driver_reply == "C") {
                    $update_log_array = array(
                        "driver_id" => $temp_driver,
                        "taxi_id" => $drivertaxi,
                        "driver_reply" => "C"
                    );
                } else {
                    $update_log_array = array(
                        "driver_id" => $temp_driver,
                        "taxi_id" => $drivertaxi
                    );
                }
            }
        } else {
            $trip_id = 0;
        }
        return "";
    }
    public function get_driver_taxi($driver_id = "")
    {
		$match = array('mapping_driverid' => (int)$driver_id, 'mapping_status' => 'A');
		//$project = array('mapping_taxiid' , 'mapping_companyid');
		$options=[
				'projection'=>[
				   'mapping_taxiid'=>1,                               
				   'mapping_companyid'=>1                           
					]
				];
		$result = $this->mongo_db->find(MDB_TAXI_DRIVER_MAPPING,$match,$options);
		$res = $result;
        return isset($res) ? $res : array();
    }
    public function previous_files_unlink($dir)
    {
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                $images = array();
                while (($file = readdir($dh)) !== false) {
                    if (!is_dir($dir . $file)) {
                        //$images[$listings['id']] = $file;
                        unlink($dir . $file);
                    }
                }
                closedir($dh);
            }
        }
    }
    public function check_driver_has_trip_request($driver_id, $company_all_currenttimestamp)
    {
        $datetime     = explode(' ', $company_all_currenttimestamp);
        $current_date = $datetime[0] . ' 00:00:01';
        $createdate   = isset($current_date) ? $current_date : $datetime;
		$match = array('status' => 1,
					   'selected_driver' => (int)$driver_id,
					   'createdate' => array('$gte'=> Commonfunction::MongoDate(strtotime($createdate))),
					   );
		$result = $this->mongo_db->count(MDB_REQUEST_HISTORY,$match);
		return isset($result) ? $result:0;
    }
    /*** Get Passenger Profile details using passenger log id  ***/
    public function get_trip_detail_only($passengerlog_id = "")
    {
		$result = array();
        $match = array('_id'=>(int)$passengerlog_id);
        $project = array('passengers_id','driver_id','taxi_id','operator_id','travel_status','driver_reply');
        $res = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,$match,$project);
        
        if(!empty($res)){
			
				$r = $res;
				$temp_arr['passengers_id'] = isset($r['passengers_id'])? $r['passengers_id']: '';
				$temp_arr['driver_id'] = isset($r['driver_id'])?$r['driver_id']:'';
				$temp_arr['taxi_id'] = isset($r['taxi_id'])?$r['taxi_id']:'';
				$temp_arr['operator_id'] = isset($r['operator_id'])?$r['operator_id']:'';
				$temp_arr['travel_status'] = isset($r['travel_status'])?$r['travel_status']:'';
				$temp_arr['driver_reply'] = isset($r['driver_reply'])?$r['driver_reply']:'';
				
				$result[] = (object)$temp_arr;
		}
        return $result;
    }
    /** Change Driver Status **/
    public function change_driver_status($passenger_log_id = "", $status = "")
    {
        if ($status == 'A') {
            $changearr = array(
                "driver_reply" => $status,
                "msg_status" => 'R',
                "travel_status" => 9,
                "driver_comments" => __('confirmed')
            );
        } elseif ($status == 'R') {
            $changearr = array(
                "driver_reply" => $status,
                "msg_status" => 'R',
                "travel_status" => 10,
                "driver_comments" => __('missed')
            );
        } else {
            $changearr = array(
                "driver_reply" => $status,
                "msg_status" => 'R',
                "travel_status" => 6,
                "driver_comments" => ""
            );
        }
		//$result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$passenger_log_id),$changearr,array('upsert'=>false));
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$passenger_log_id),array('$set'=>$changearr),array('upsert'=>false));
		return (empty($result->getwriteErrors())) ? 1 : 0;
		
    }
    /*** Notification to driver for Dispatcher cancelled the trip *********************/
    public function get_dispatcher_cancel_data($driver_id = "", $company_id)
    {	
		$match_array = array();
		$date = date('Y-m-d');		
		$match_array['driver_id'] = (int)$driver_id;
		$match_array['travel_status'] = 8;
		$match_array['notification_status'] = array('$ne' => 5);
		$match_array['bookby'] = 2;
		$match_array['yearMonthDay'] = $date;
		$arguments = array(
			array('$project' =>
				array(
					'yearMonthDay' => array('$dateToString' => array('format' => '%Y-%m-%d','date' => '$createdate')),
					'trip_id'=>'$passengers_log_id',
					'status'=>'$travel_status'
				)
			),
			array('$match'=>$match_array)
		);		
		//print_r($arguments);exit;
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		//print_r($result);exit;
        return (!empty($result['result'])?$result['result']:array());
    }
	
    /************************************************************************************/
    public function get_driver_earnings_with_rating($driver_id, $company_id)
    {
        $get_company_time_details = $this->get_company_time_details($company_id);
        $start_time               = $get_company_time_details['start_time']; //Start time
        $end_time                 = $get_company_time_details['end_time']; //end time
        $current_time             = $get_company_time_details['current_time']; // Current Time
        $arguments = array(
				array('$lookup'=>array(
					'from'=>MDB_TRANSACTION,
					'localField'=>"_id",
					'foreignField'=>"passengers_log_id",
					'as'=>"transaction")),
				array('$unwind'=>'$transaction'),
				array('$match'=>array(
					'createdate'=>array('$gte'=>Commonfunction::MongoDate(strtotime($start_time)),
										'$lte'=>Commonfunction::MongoDate(strtotime($end_time))),
					'driver_id'=>(int)$driver_id,
					'travel_status'=>1
				)),
				array('$project'=>array(
					'rating'=>'$rating',
					'total_amount'=>'$transaction.fare',
				)),
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        
        $result = array();
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr['rating'] = isset($r['rating'])?$r['rating']:0;
				$temp_arr['total_amount'] = isset($r['total_amount'])?$r['total_amount']:0;
				$result[] = $temp_arr;
			}
		}
        return $result;
    }
    public function get_driver_total_earnings($driver_id)
    {
         $arguments = array(
				array('$lookup'=>array(
					'from'=>MDB_TRANSACTION,
					'localField'=>"_id",
					'foreignField'=>"passengers_log_id",
					'as'=>"transaction")),
				array('$unwind'=>'$transaction'),
				array('$match'=>array(
							'driver_id'=>(int)$driver_id,
							'travel_status'=>1
						)),
				array('$group' => array(
					'_id' => NULL,
					'total_amount' => array('$sum' => '$transaction.fare')
				)),
				array('$project'=>array(
					'rating'=>'$rating',
					'total_amount'=>'$total_amount',
				))
			);
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        return (!empty($result['result'])) ? $result['result'][0]['total_amount'] : 0;
    }
    /************************************************************************************/
    public function get_company_time_details($companyid)
    {
        $timezone_details = array();
        /*** Start ***/
        if ($companyid == '') {
            if (TIMEZONE) {
                $current_time = convert_timezone('now', TIMEZONE);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
                $date         = $current_date[0] . ' %';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:01';
                $end_time     = date('Y-m-d') . ' 23:59:59';
                $date         = date('Y-m-d %');
            }
        } else {
            $result = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$companyid),array('companydetails.time_zone'));
            if (!empty($result)) {
				$timezone = (isset($result['companydetails']['time_zone'])?$result['companydetails']['time_zone']:"");
                $current_time = convert_timezone('now', $timezone);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
                $date         = $current_date[0] . ' %';
            } else {
                $current_time = date('Y-m-d H:i:s');
                $start_time   = date('Y-m-d') . ' 00:00:01';
                $end_time     = date('Y-m-d') . ' 23:59:59';
                $date         = date('Y-m-d %');
            }
        }
        $timezone_details['current_time'] = $current_time;
        $timezone_details['start_time']   = $start_time;
        $timezone_details['end_time']     = $end_time;
        $timezone_details['date']         = $date;
		//print_r( $timezone_details);exit;
        return $timezone_details;
        /*** End ***/
    }
    /** to check driver not updated for a particular period **/
    public function check_driver_not_updated($driver_id, $company_timestamp)
    {        
		$result = $this->mongo_db->findOne(MDB_DRIVER_INFO,array('_id'=> (int)$driver_id),array('update_date'));
        $update_date = (isset($result['update_date'])) ? commonfunction::convertphpdate('Y-m-d H:i:s',$result['update_date']) :'';
        return $update_date;
    }
	
    /** Change driver request flow **/
    public function change_driver_reqflow($trip_id, $available_drivers, $rejected_timeout_drivers)
    {
        $availDriversArr = explode(",", $available_drivers);
        if (count($availDriversArr) > 1) {
            $temp                = $availDriversArr[0];
            $availDriversArr[0]  = $availDriversArr[1];
            $availDriversArr[1]  = $temp;
            $driver_avail        = implode(",", $availDriversArr);
            $temp_driver         = isset($availDriversArr[0]) ? $availDriversArr[0] : 0;
            $update_trip_array   = array(
                "available_drivers" => (int)$driver_avail,
                "selected_driver" => (int)$temp_driver,
                "status" => 0
            );
            $update_result       = $this->update_table(MDB_REQUEST_HISTORY, $update_trip_array, '_id', (int)$trip_id);
            $driver_details      = $this->get_driver_taxi($temp_driver);
            $drivertaxi          = isset($driver_details[0]['mapping_taxiid']) ? $driver_details[0]['mapping_taxiid'] : 0;
            $drivercompany       = isset($driver_details[0]['mapping_companyid']) ? $driver_details[0]['mapping_companyid'] : 0;
            $update_log_array    = array(
                "driver_id" => (int)$temp_driver,
                "taxi_id" => (int)$drivertaxi,
                "company_id" => (int)$drivercompany
            );
            $pass_log_update     = $this->update_table(MDB_PASSENGERS_LOGS, $update_log_array, '_id', (int)$trip_id);
            $update_driver_array = array(
                "status" => 'B'
            );
            $driver_tbl_update   = $this->update_table(MDB_DRIVER_INFO, $update_driver_array, '_id', (int)$temp_driver);
        } else {
            $reject_drivers    = ($rejected_timeout_drivers != '') ? $rejected_timeout_drivers . ',' . $available_drivers : $available_drivers;
            $update_trip_array = array(
                "available_drivers" => null,
                "selected_driver" => null,
                "rejected_timeout_drivers" => $reject_drivers,
                "status" => 4
            );
            $update_result     = $this->update_table(MDB_REQUEST_HISTORY, $update_trip_array, '_id', (int)$trip_id);
            $update_log_array  = array(
                "driver_id" => 0,
                "taxi_id" => 0,
                "company_id" => 0
            );
            $pass_log_update   = $this->update_table(MDB_PASSENGERS_LOGS, $update_log_array, '_id', (int)$trip_id);
        }
    }
    /** to get the updated trip details ( updated from dispatcher ) **/
    public function get_trip_update_status($trip_id = "")
    {
		$result = array();
		
		$match = array('_id' => (int)$trip_id,'notification_status' =>array('$in' => array(6,22)));
		$project = array('drop_location', 'current_location', 'pickup_latitude', 'pickup_longitude', 'drop_latitude', 'drop_longitude','notes_driver','notification_status');
		$res = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,$match,$project);           
		
		if(!empty($res)){
			$result[] = $res;
		}
		return $result;
    }
    public function passenger_signup_with_referral($p_first_name, $p_last_name, $p_email, $p_phone, $country_code, $p_password, $p_confirm_password,
    $otp=null,$referral_code="",$devicetoken="",$deviceid="",$devicetype="",$company_id="",$accessToken="",$uid="",$image_name="")
    {
	
        $common_model = Model::factory('commonmodel');
        if ($company_id != '') {
            $current_time = $common_model->getcompany_all_currenttimestamp($company_id);
        } else {
            $current_time = $this->currentdate_bytimezone;
        }
        /** Referrral key generator **/
		$auto_referral_code = commonfunction::randomkey_generator('6');
        /** Referrral key generator **/
        /** to get referral setting and amount from siteinfo table **/
        $siteInfo            = $this->siteinfo_details();
        $referralAmount      = (isset($siteInfo[0]['referral_amount'])?$siteInfo[0]['referral_amount']:0);
		$referral_settings      = (isset($siteInfo[0]['referral_settings'])?$siteInfo[0]['referral_settings']:'');
        if ($referral_settings == 2) {
            $auto_referral_code = '';
            $referralAmount     = '';
        }
        /** Insert in passenger table **/
		/*$rs = $this->mongo_db->find(MDB_PASSENGERS,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
		$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);*/
		$options=[
				'projection'=>[
				   '_id'=>1,                               
					],
				'sort'=>[
					'_id'=>-1
					 ],
				'limit'=>1
			];
		$res = $this->mongo_db->find(MDB_PASSENGERS,[],$options);
		$res = (!empty($res))?array($res[0]['_id']=>0):array(1);
		reset($res);
		$first_key = key($res);
		$inc_id = $first_key+1;
		$md_pwd = md5($p_confirm_password);
		$val['first_name'] = (isset($p_first_name)) ? urldecode($p_first_name) : '';
		$val['last_name'] = (isset($p_last_name)) ? urldecode($p_last_name) : '';
		$val['email'] = (isset($p_email)) ? urldecode($p_email) : '';
		//$val['phone'] = (isset($val['phone'])) ? urldecode($val['phone']) : '';
		//$val['country_code'] = (isset($val['country_code'])) ? urldecode($val['country_code']) : '';
		//$val['password'] = (isset($val['password'])) ? urldecode($val['password']) : '';
		//$val['confirm_password'] = (isset($val['confirm_password'])) ? urldecode($val['confirm_password']) : '';
		$devicetoken = (isset($devicetoken)) ? urldecode($devicetoken) : '';
		$deviceid = (isset($deviceid)) ? urldecode($deviceid) : '';
		$devicetype = (isset($devicetype)) ? urldecode($devicetype) : '';
		$uid = (isset($uid)) ? urldecode($uid) : '';
		$accessToken = (isset($accessToken)) ? urldecode($accessToken) : '';
	
		$fieldname_array = array(
			'_id' => (int)$inc_id,
			'name' => $val['first_name'],
			'lastname' => $val['last_name'],
			'email' => $val['email'],
			'password' => $md_pwd,
			'org_password'=> $p_confirm_password, 
			'otp' => $otp,
			'country_code' => $country_code,
			'split_fare' =>'',
            'skip_credit_card' =>'',
            'forgot_password' =>'',
			'phone' => $p_phone,
			'address' => null,
			'referral_code' => $auto_referral_code,
			'referral_code_amount' => (float)$referralAmount,
			'referral_code_limit' => 1,
			'activation_key' => null,
			'activation_status' => 1,
			'user_status' => 'I',
			'created_date' => Commonfunction::MongoDate(strtotime($current_time)),
			'updated_date' => Commonfunction::MongoDate(strtotime($current_time)),
			'passenger_cid' => (int)$company_id,
			'device_token'=> $devicetoken,
			'device_id' => $deviceid,
			'device_type' => (int)$devicetype,
			'fb_user_id' => $accessToken,
			'fb_access_token' => $uid,
			'discount' => (float)0,
			'salutation' => '',
			'profile_image' => $image_name
		);	
		$insert      = $this->mongo_db->insertOne(MDB_PASSENGERS,$fieldname_array);
		//$passresult = isset($insert['err']) ? 0 : 1;
		$passresult = (empty($insert->getwriteErrors())) ? 1 : 0;
		
        if ($passresult) {
            if (isset($referral_code)) {
				//echo $referral_code;
                //to get the referral amount and referral limit from the referral code
				$referral_check = $this->mongo_db->findOne(MDB_PASSENGERS,array('referral_code' =>$referral_code),array('_id','referral_code_amount','referral_code_limit'));
				$refer_dets = isset($referral_check) ? $referral_check : array();
				//print_r($refer_dets);exit;
                if (count($refer_dets) > 0) {
					$referral_code_amount = (isset($refer_dets['referral_code_amount']) ? $refer_dets['referral_code_amount'] : '' );
					$referral_code_limit = (isset($refer_dets['referral_code_limit']) ? $refer_dets['referral_code_limit'] : '' );
					$referred_id = (isset($refer_dets['_id']) ? $refer_dets['_id'] : '' );
				
					/*$rs = $this->mongo_db->find(MDB_PASSENGER_REFERRAL,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
					$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);*/
					$options=[
							'projection'=>[
							   '_id'=>1,                               
								],
							'sort'=>[
								'_id'=>-1
								],
							'limit'=>1
						];
					$res = $this->mongo_db->find(MDB_PASSENGER_REFERRAL,[],$options);
					$res = (!empty($res))?array($res[0]['_id']=>0):array(1);
					reset($res);
					$first_key = key($res);
					$inc_ref_id = $first_key+1;
					$ref_fieldArr = array(
						'_id' => (int)$inc_ref_id,
                        'passenger_id' => (int)$inc_id,
                        'referral_code' => $referral_code,
                        'referral_amount' => (float)$referral_code_amount,
                        'referral_limit' => $referral_code_limit,
                        'device_id' => $deviceid,
                        'device_token' => $devicetoken,
                        'referred_by' => (int)$referred_id,
                        'referral_amount_used' => 0,
                        'createdate' => Commonfunction::MongoDate(strtotime($current_time))
                    );
					$passRef = $this->mongo_db->insertOne(MDB_PASSENGER_REFERRAL,$ref_fieldArr);
                    //to update the referral amount into the wallet column in passenger table
                    $update_array         = array(
                        'wallet_amount' => (float)$referral_code_amount
                    );
					$update_wallet_amount = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$inc_id),array('$set'=>$update_array),array('upsert'=>false));
                }
            }
            return 1;
        } else {
            return 0;
        }
    }
    public function save_referral_code($passenger_id = "", $referral_code = "", $company_id = "", $deviceid = "", $devicetoken = "")
    {
        $common_model = Model::factory('commonmodel');
        $current_time = $this->currentdate_bytimezone;
        if ($company_id != '') {
            $current_time = $common_model->getcompany_all_currenttimestamp($company_id);
        } 
        //to get the referral amount and referral limit from the referral code		
        $options=[
				'projection'=>[
					   '_id'=>1,                               
					   'referral_code_amount'=>1,                               
					   'referral_code_limit'=>1
					]
				];
		$referral_query = $this->mongo_db->find(MDB_PASSENGERS,array('referral_code'=>$referral_code),$options);
		$referral_res = $referral_query;
        $refer_dets = (isset($referral_res)?$referral_res:array());
		$ref_id = (isset($refer_dets[0]['_id'])?$refer_dets[0]['_id']:0);
		$ref_amount = (isset($refer_dets[0]['referral_code_amount'])?$refer_dets[0]['referral_code_amount']:0);
        $ref_code = (isset($refer_dets[0]['referral_code_limit'])?$refer_dets[0]['referral_code_limit']:'');        
		if (count($refer_dets) > 0) {
			/*$rs = $this->mongo_db->find(MDB_PASSENGER_REFERRAL,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
			$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);*/
			
			$options=[
				'projection'=>[
				   '_id'=>1,                               
					],
				'sort'=>[
					'_id'=>-1
					 ],
				'limit'=>1
			];
			$res = $this->mongo_db->find(MDB_PASSENGER_REFERRAL,[],$options);
			$res = (!empty($res))?array($res[0]['_id']=>0):array(1);
			reset($res);
			$first_key = key($res);
			$inc_id = $first_key+1;
            $ref_fieldArr         = array(
				'_id' => (int)$inc_id,
                'passenger_id' => (int)$passenger_id,
                'referral_code' => $referral_code,
                'referral_amount' => (double)$ref_amount,
                'referral_limit' => $ref_code,
                'device_id' => $deviceid,
                'device_token' => $devicetoken,
                'referred_by' => (int)$ref_id,
                'createdate' =>  Commonfunction::MongoDate(strtotime($current_time))
            );
			$passRef = $this->mongo_db->insertOne(MDB_PASSENGER_REFERRAL,$ref_fieldArr);
            //to update the referral amount into the wallet column in passenger table
            $update_array         = array(
                'wallet_amount' => (double)$ref_amount
            );
			$update_wallet_amount = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array('$set'=>$update_array),array('upsert'=>false));
            return 1;
        } else {
            return 0;
        }
    }
    //check passenger already used referral code
    public function check_referral_code_used($passenger_id){
		
		$result = $this->mongo_db->count(MDB_PASSENGER_REFERRAL,array('passenger_id'=>(int)$passenger_id));
        return !empty($result) ? $result:0;
    }
    //check otp exist for a passenger
    public function otp_verification($otp = "", $email = "")
    {
		$result = $this->mongo_db->count(MDB_PASSENGERS,array('email'=> $email,'otp' => $otp));
        return (isset($result)) ? $result: 0;
    }
    // Check Whether Passenger phone is Already Exist or Not
    public function check_referral_code_exist($referral_code = "", $company_id = "")
    {
		$match = array('referral_code'=>$referral_code);
        if ($company_id != '' && $company_id != 0) {
			$match['passenger_cid'] = (int)$company_id;
        }
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match);
		return (!empty($result)?1:0);
    }
    //to check the passenger have wallet amount to use
    public function get_passenger_wallet_amount($passenger_id)
    {      
		$result = array();  
		$res = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array("wallet_amount","name","lastname","email","phone","referral_code_amount","referral_code"));
		if(!empty($res)){
			
			$temp_arr['wallet_amount'] = isset($res['wallet_amount'])?$res['wallet_amount']:0;
			$temp_arr['name'] = isset($res['name'])?$res['name']:'';
			$temp_arr['lastname'] = isset($res['lastname'])?$res['lastname']:'';
			$temp_arr['email'] = isset($res['email'])?$res['email']:'';
			$temp_arr['phone'] = isset($res['phone'])?$res['phone']:'';
			$temp_arr['referral_code_amount'] = (isset($res['referral_code_amount']) && $res['referral_code_amount']!=null)?$res['referral_code_amount']:'0';
			$temp_arr['referral_code'] = isset($res['referral_code'])?$res['referral_code']:'';
			$result[] = $temp_arr;
		}		
        return $result;
    }
    //function to get passenger details by referral code
    public function passenger_detailsbyreferralcode($referral_code)
    {
        $res = $this->mongo_db->findOne(MDB_PASSENGERS,array('referral_code'=> $referral_code),array('_id','wallet_amount'));
        $result = array();
        if(!empty($res)){
			$temp_arr['id'] = isset($res['_id'])?$res['_id']:'';
			$temp_arr['wallet_amount'] = isset($res['wallet_amount'])?$res['wallet_amount']:0;
			$result[] = $temp_arr;
		}		
        return $result;
    }
    //to check the passenger have referral amount to use
    public function check_passenger_referral_amount($passenger_id)
    {       
		$options=[
				'projection'=>[
				   'referral_amount'=> 1,                               
				   'referral_code'=>1                         
					]
				];
        $res = $this->mongo_db->find(MDB_PASSENGER_REFERRAL,array('passenger_id'=>(int)$passenger_id,'referral_amount_used'=>0),array("referral_amount","referral_code"));
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
    //insert into wallet log table
    public function add_wallet_log($fieldname_array, $values_array)
    {
		/*$rs = $this->mongo_db->find(MDB_PASSENGER_WALLET_LOG,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
		$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);*/
		
		$options=[
				'projection'=>[
				   '_id'=>1,                               
					],
				'sort'=>[
					'_id'=>-1
					 ],
				'limit'=>1
			];
		$res = $this->mongo_db->find(MDB_PASSENGER_WALLET_LOG,[],$options);
		$res = (!empty($res))?array($res[0]['_id']=>0):array(1);
		reset($res);
		$first_key = key($res);
		$inc_id = $first_key+1;
		$insert_arr = array_combine($fieldname_array, $values_array);
		$insert_arr['passenger_id'] = isset($insert_arr['passenger_id']) ? (int)$insert_arr['passenger_id']:0;
		$insert_arr['amount'] = isset($insert_arr['amount']) ? (double)$insert_arr['amount']:0;
		$insert_arr['payment_status'] = isset($insert_arr['payment_status']) ? (int)$insert_arr['payment_status']:0;
		$insert_arr['payment_type'] = isset($insert_arr['payment_type']) ? (int)$insert_arr['payment_type']:0;
		$insert_arr['promocode_amount'] = isset($insert_arr['promocode_amount']) ? (double)$insert_arr['promocode_amount']:0;
		$insert_arr['createdate'] = Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone));
		//print_r($insert_arr);exit;
		$insert_arr['_id'] = (int)$inc_id;
		$result = $this->mongo_db->insertOne(MDB_PASSENGER_WALLET_LOG,$insert_arr);
		return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    //insert credit card details if savecard is 1
    public function add_credit_card_details($card_fieldArr, $card_valueArr)
    {
		$args = array(array('$unwind' => '$creditcard_details'),
									array('$project' => array('id'=>'$creditcard_details.passenger_cardid')),
									array('$sort' => array('creditcard_details.passenger_cardid'=>-1)),
									array('$limit' => 1)
								);
		$rs = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
		$first_key = (isset($rs['result'])) ? $rs['result'][0]['id'] : 0;
		$inc_id = $first_key+1;
							
		$update_array = array_combine($card_fieldArr, $card_valueArr);
        $update_array['passenger_cardid'] = (int)$inc_id;
        $passenger_id = $update_array['passenger_id'];
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id' => (int)$passenger_id),array('$push'=>$update_arr),array('upsert'=>false));
		return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    /** check promocode used limit for wallet **/
    public function checkwalletpromocode($promo_code = "", $passenger_id = "", $company_id = "")
    {
		$match = array('promocode'=>$promo_code,'passenger_id'=>(int)$passenger_id);
		$project = array('promocode',
						 'promo_discount',
						 'promo_used',
						 'start_date',
						 'expire_date',
						 'promo_limit');
		$promo_fetch = $this->mongo_db->findOne(MDB_PASSENGERS_PROMO,$match,$project);
        if (count($promo_fetch) > 0) {
			$promocode      = (isset($promo_fetch['promocode'])?$promo_fetch['promocode']:'');
            $promo_discount = (isset($promo_fetch['promo_discount'])?$promo_fetch['promo_discount']:'');
            $promo_used     = (isset($promo_fetch['promo_used'])?$promo_fetch['promo_used']:'');
            $promo_start_date    = (isset($promo_fetch['start_date'])?$promo_fetch['start_date']:'');
            $promo_expire_date   = (isset($promo_fetch['expire_date'])?$promo_fetch['expire_date']:'');
            $promo_limit    = (isset($promo_fetch['promo_limit'])?$promo_fetch['promo_limit']:'');
			$promo_start = Commonfunction::convertphpdate('Y-m-d H:i:s',$promo_start_date);
			$promo_expire = Commonfunction::convertphpdate('Y-m-d H:i:s',$promo_expire_date);
            if ($company_id == '' || $company_id == 0) {
                if (TIMEZONE) {
                    $current_time = convert_timezone('now', TIMEZONE);
                } else {
                    $current_time = date('Y-m-d H:i:s');
                }
            } else {
				$model_base_query = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$company_id),array('companydetails.time_zone'));
				$model_res = (isset($model_base_query)?$model_base_query:array());
				if(!empty($model_res)){
					$timezone = (isset($model_res['companydetails']['time_zone'])?$model_res['companydetails']['time_zone']:'');
					$current_time = convert_timezone('now', $timezone);
				}else {
                    $current_time = date('Y-m-d H:i:s');
                }
            }
            if (strtotime($promo_start) > strtotime($current_time)) {
                return 3;
            } else if (strtotime($promo_expire) < strtotime($current_time)) {
                return 4;
            } else {
				$promo_user_count = $promo_trip_count = $promo_wallet_count = 0;
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
    /** to get location from latittude and longitude **/
    public function getaddress($lat, $lng)
    {
        try {
            $url    = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&sensor=false&key=' . GOOGLE_GEO_API_KEY;
            $json   = @file_get_contents($url);
            $data   = json_decode($json);
            $status = ($data) ? $data->status : 0;
            if ($status == "OK")
                return $data->results[0]->formatted_address;
            else
                return false;
        }
        catch (Kohana_Exception $e) {
            return false;
        }
    }
    /*Get the CMS Content*/
    public function getcmscontent($content, $default_companyid = "")
    {
        $default_companyid = COMPANY_CID;
        if ($default_companyid != 0) {
			$arguments = array(
						array('$match'=>array('_id'=> (int)$default_companyid)),
						array('$unwind' => '$company_cms'),
						array('$match'=>array('company_cms.page_url'=> $content)),
						array('$project' => array(													
							'content' => '$company_cms.content',
							'menu' => '$company_cms.menu_name',
							'type' => 1
						))					
					);
			$result = $this->mongo_db->aggregate(MDB_COMPANY,$arguments);
        } else {
			$arguments = array(
						array('$match'=>array('_id'=> (int)$content)),
						array('$project' => array(													
							'content' => '$content',
							'meta_keyword' => '$meta_keyword',
							'meta_title' => '$menu_name',
							'meta_description' => '$meta_description',
							'menu' => '$menu',
							'type' => 1
						))					
					);
			$result = $this->mongo_db->aggregate(MDB_CMS,$arguments);
        }
        //echo $content;print_r($result['result']);exit;
        return (!empty($result['result'])) ? $result['result'] : array();
    }
    public function get_driver_cancelled_trips($driver_id, $company_id)
    {
        $get_company_time_details = $this->get_company_time_details($company_id);
        $start_time               = $get_company_time_details['start_time']; //Start time
        $end_time                 = $get_company_time_details['end_time']; //end time
        $result = $this->mongo_db->count(MDB_PASSENGERS_LOGS,array('travel_status'=>9,'driver_reply'=>"C",'createdate'=>array('$gte'=>Commonfunction::MongoDate(strtotime($start_time)),'$lte'=>Commonfunction::MongoDate(strtotime($end_time)))));
        return (isset($result))?$result:0;
    }
    public function logged_user_status_web($driver_id, $company_id)
    {
        $company_id = "";
		if ($company_id == 0) {
            if (TIMEZONE) {
                $current_time = convert_timezone('now', TIMEZONE);
                $current_date = explode(' ', $current_time);
                $start_time   = $current_date[0] . ' 00:00:01';
                $end_time     = $current_date[0] . ' 23:59:59';
                $date         = $current_date[0] . ' %';
            } else {
                $time        = date('H:i:s', strtotime($pickup_time));
                $update_time = date('Y-m-d') . ' ' . $time;
            }
        } else {
			$result = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$company_id),array('companydetails.time_zone'));
            if (!empty($result)) {
				$time_zone = (isset($result['companydetails']['time_zone'])?$result['companydetails']['time_zone']:"");
                $time                    = date('H:i:s', strtotime($pickup_time));
                $current_datetime        = convert_timezone('now', $time_zone);
                $curretnt_datetime_split = explode(' ', $current_datetime);
                $update_time             = $curretnt_datetime_split[0] . ' ' . $time;
            } else {
				$time        = date('H:i:s', strtotime($pickup_time));
                $update_time = date('Y-m-d') . ' ' . $time;
            }
        }
		$match_array = array();
		//echo $driver_id;exit;
		$match_array['_id']=(int)$driver_id;
		if($company_id!="" && $company_id!=0){
			$match_array['company_id']=(int)$company_id;
		}
		$match_array['taxi_driver_mapping.mapping_enddate']= array('$gte'=> Commonfunction::MongoDate(strtotime($current_time)));
		$arguments = array(
						array('$lookup'=>array(
							'from'=>MDB_TAXI_DRIVER_MAPPING,
							'localField'=>"_id",
							'foreignField'=>"mapping_driverid",
							 'as'=>"taxi_driver_mapping"
						)),
						array('$unwind'=>'$taxi_driver_mapping'),
						array('$match'=> $match_array),
						array('$project' => array(
							'login_status' => '$login_status',
							'notification_status' => '$notification_status'
						))
					);
        $result = $this->mongo_db->aggregate(MDB_PEOPLE,$arguments);
        $result = (isset($result['result']) ? $result['result']: array());
        if (count($result) == 0) {
            $result[0]['login_status']        = 'N';
            $result[0]['notification_status'] = '0';
            $result[0]['admin_logout']        = '1';
			$match_array = array();
			$match_array['driver_id']=(int)$driver_id;
			$match_array['travel_status']=array('$in' => array(2,3,5,9));
			$match_array['driver_reply'] = 'A';
			if($company_id!="" && $company_id!=0){
				$match_array['company_id']=(int)$company_id;
			}
			$match_array['pickup_time']= array('$gte'=> Commonfunction::MongoDate(strtotime($start_time)));
			
			//$result1 = $this->mongo_db->find(MDB_PASSENGERS_LOGS,$match_array,array('_id','travel_status'))->sort(array('_id' => -1))->skip(0)->limit(1);
			
			$options=[
				'projection'=>[
				   '_id'=>1,    
				   'travel_status'=>1                           
				],
				'sort'=>[
					'_id'=>-1
				],
				'skip' =>0,
				'limit'=>1
			];
			$res = $this->mongo_db->find(MDB_PASSENGERS_LOGS,$match_array,$options);
		
			$get_driver_log_details = $result1;
            if (count($get_driver_log_details) == 0) {
                $update_array        = array(
                    "login_from" => "",
                    "login_status" => "N",
                    "device_id" => "",
                    "device_token" => "",
                    "device_type" => "",
                    "notification_setting" => 0
                );
                $login_status_update = $this->update_table(MDB_PEOPLE, $update_array, '_id', $driver_id);
                if ($login_status_update) {					
                    $result[0]['login_status']        = 'N';
                    $result[0]['notification_status'] = '1';
                    $result[0]['admin_logout']        = '1';
                    $update_driverArr                 = array(
                        "shift_status" => "OUT"
                    );
					$dr_status_update                 = $this->update_table(MDB_DRIVER_INFO, $update_driverArr, '_id', $driver_id);
                }
                /** GET Shift ID **/
                $driver_shift = $this->get_driver_shift_log($driver_id);
                if (count($driver_shift) > 0) {
                    $this->currentdate  = Commonfunction::getCurrentTimeStamp();
                    $shiftupdate_arrary = array(
                        "shift_end" => Commonfunction::MongoDate(strtotime($this->currentdate))
                    );
                    $driver_shift_id    = isset($driver_shift[0]['_id']) ? $driver_shift[0]['_id'] : '';
					if($driver_shift_id != ''){
						$transaction        = $this->update_table(MDB_SHIFT_HISTORY, $shiftupdate_arrary, '_id', $driver_shift_id);	
					}                    
                }
            }
            return $result;
        } else {
            if ($result[0]['login_status'] == 'S' && $result[0]['notification_status'] == 1) {
                return 1;
            }
            if ($result[0]['login_status'] == 'N') {
                return $result;
            } else {
                return 1;
            }
        }
    }
	 public function get_driver_shift($id)
    {
		$options=[
				'projection'=>[
						'shift_status'=>1,                               
					]
				];
		$result = $this->mongo_db->find(MDB_DRIVER_INFO,array('_id' => (int)$id),$options);
		$res = $result;
		$res = commonfunction::change_key($res);		
		return (!empty($res[0]['shift_status']) && $res[0]['shift_status'])?$res[0]['shift_status']:array();
    }
	
    public function get_driver_shift_log($id)
    {
		$options=[
				'projection'=>[
						'_id'=>1,                               
					]
				];
		$result = $this->mongo_db->find(MDB_SHIFT_HISTORY,array('driver_id' => (int)$id),$options);
		$res = $result;
		return (!empty($res) && $res)?Commonfunction::change_key($res):array();
    }
    public function get_passenger_company_id($id)
    {		
		$result1 = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=> (int)$id),array('passenger_cid'));
		$company_id = (!empty($result1) && isset($result1['passenger_cid'])) ? $result1['passenger_cid']: 0;
        return $company_id;
    }
    //** to check the passenger in trip or not **//
    public function check_passenger_in_trip($passengerId, $company_id)
    {
        $get_company_time_details = $this->get_company_time_details($company_id);
        $st_time               = $get_company_time_details['start_time'];
		$start_time = Commonfunction::convertphpdate('Y-m-d H:i:s',$st_time);
		$match = array('pickup_time' => array('$gte'=> Commonfunction::MongoDate(strtotime($start_time))),
						'passengers_id' => (int)$passengerId,
						'driver_reply' => 'A',
						'travel_status'=>array('$in'=>array(9,2,3,5))	
					);
		$count = $this->mongo_db->count(MDB_PASSENGERS_LOGS,$match);
		return !empty($count)?$count:0;
    }   
   // Check Whether Passenger is fb user or normal
    public function check_fb_user($phone = "", $company_id = "", $country_code = "")
    {
		$match_query = array();
		$match_query['phone'] = $phone;
		$match_query['country_code'] = $country_code;
		$match_query['fb_user_id'] = array('$ne' => "");
		$match_query['fb_access_token'] = array('$ne' => "");
		if($company_id!="" && $company_id!=0){
			$match_query['passenger_cid'] = (int)$company_id;
		}
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match_query,array('_id'));
		return isset($result)? $result:0;
    }
	
	public function get_insert_id($collection = ""){
		
		/*$rs = $this->mongo_db->find($collection,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
		$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);*/
		
		$options=[
				'projection'=>[
				   '_id'=>1,                               
					],
				'sort'=>[
					'_id'=>-1
					 ],
				'limit'=>1
			];
		$res = $this->mongo_db->find($collection,[],$options);
		$res = (!empty($res))?array($res[0]['_id']=>0):array(1);
		reset($res);
		$first_key = key($res);
		$inc_id = $first_key+1;
		return $inc_id;
	}
	
	public function update_driverinfo($array="", $id=""){
		$result = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id'=>(int)$id),array('$set'=>$array),array('upsert'=>false));
		return 1;
	}
	
	/**  **/
	public function checkpassengerPhonewithConcat($phone="",$company_id="")
	{
		$match = array();
		$match['phone'] = $phone;
		if($company_id != '' && $company_id != 0){
			$match['passenger_cid'] = (int)$company_id;
		}
		$res = $this->mongo_db->findOne(MDB_PASSENGERS,$match,array('phone','_id','name','login_status','creditcard_details'));
		if(!empty($res)){
			$temp_arr['id'] = isset($res['_id'])?$res['_id']:'';
			$temp_arr['name'] = isset($res['name'])?$res['name']:'';
			$temp_arr['phone'] = isset($res['phone'])?$res['phone']:'';
			$temp_arr['creditcard_details'] = isset($res['creditcard_details'])? 1 :0;
			$temp_arr['login_status'] = isset($res['login_status'])?$res['login_status']:'';
			$result[] = $temp_arr;
		}		
		return (!empty($result)) ? $result : array();			
	}
	
	/** check secondary passenger in trip **/
	public function checkSecondPassengerinTrip($passengerId)
	{
		$count=0;
		$currentDate = date('Y-m-d',strtotime($this->currentdate));
		$and1 = array('$and' => array(
						array('travel_status' => 9), 
						array('approve_status' => 'A')));
						
		$match = array('$and'=> array(
						array('pickup_time' =>  Commonfunction::MongoDate(strtotime($currentDate))),
						array('friends_p_id' => (int)$passengerId),
						array('driver_reply' => 'C'),
						array('$or' => array(array('travel_status'=>array('$in'=>array(2,3,5))),$and1))
					));
		$args = array(array('$unwind' => array('path' => '$split_details', 'preserveNullAndEmptyArrays' => true )),				  
					array('$project' => 
						array('_id' => 0, 
							'passengers_log_id'=>'$_id',
							'pickup_time' => array('$dateToString'=> array('format'=> '%Y-%m-%d', 'date'=>'$pickup_time')), 
							'friends_p_id'=> '$split_details.friends_p_id',
							'approve_status' =>'$split_details.approve_status',
							'travel_status' => '$travel_status',
							'driver_reply' => '$driver_reply'
						)
					),
				  array('$match' => $match),
				  array('$group'=>array('_id'=>array('passengers_log_id'=>'$passengers_log_id', 'count'=>array('$sum'=>1)))
				));
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			
			$count = isset($res['result'][0]['_id']['count']) ? $res['result'][0]['_id']['count']: 0;
		}		
		return $count;
	}
	
	//**Function to set approval status from secondary passenger for split fare**//
	public function setSplitfareApproval($tripId, $friendId, $approvalStatus)
	{
		$match = array('_id'=>(int)$tripId);
		$args = array(array('$unwind' => '$split_details'),
					array('$match' => $match),
					array('$project' => array('friend_id' => '$split_details.friends_p_id'))
				);
		$keys = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);			
		$i =0;$val = array();
		foreach($keys['result'] as $k => $v ){
			if($v['friend_id'] == $friendId){
				$val["split_details.$i.approve_status"] = $approvalStatus;
			}
			$i++;
		}		
		$match['split_details.friends_p_id'] = (int)$friendId;
		
		$update = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,$match,array('$set'=>$val),array('upsert' => false));
		return (empty($update->getwriteErrors())) ? (($approvalStatus == 'A') ? 1 : 2) : 0;
	}
	/** function to update split fare payment status **/
	public function updateSplitTransaction($transactionId, $amount, $paymentSts, $gatewaySts, $settlementSts, $tripId, $friendId)
	{
		$match = array('_id'=>(int)$tripId);
		$args = array(array('$unwind' => '$split_details'),
				  array('$match' => $match),
				  array('$project' => array('friend_id' => '$split_details.friends_p_id'))
				);
		$keys = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);			
		$i =0;$val = array();
		foreach($keys['result'] as $k => $v ){
			if($v['friend_id'] == $friendId){
				//$i = $k;
				$val["split_details.$i.transaction_id"] = $transactionId;
				$val["split_details.$i.paid_amount"] = (double)$amount;
				$val["split_details.$i.payment_status"] = (int)$paymentSts;
				$val["split_details.$i.braintree_payment_status"] = $gatewaySts;
				$val["split_details.$i.settlement_status"] = (int)$settlementSts;
			}
			$i++;			
		}
		//$match['split_details.trip_id'] = (int)$tripId;
		$match['split_details.friends_p_id'] = (int)$friendId;
		$update = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,$match,array('$set'=>$val),array('upsert'=>false));
		return (empty($update->getwriteErrors())) ? 1 : 0;
		/*$update_array = array("transaction_id" => $transactionId,
							  "paid_amount" => (double)$amount,
							  "payment_status" => (int)$paymentSts,
							  "braintree_payment_status" => $gatewaySts,
							  "settlement_status" => (int)$settlementSts);*/
		
	}	
	//credit card validation for braintree
	public function creditcardPreAuthorization($passenger_id,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$preauthorize_amount)
	{	
                    $passenger_profile=$this->passenger_profile($passenger_id,'A');
                    if(count($passenger_profile)>0)
		{
		
                    // Payment gateway transaction mandatory parameters
                    $transaction_amount = $preauthorize_amount;
                    
                    $card_info['card_number'] = $creditcard_no;
                    $card_info['expirationMonth'] = $expdatemonth;
                    $card_info['expirationYear'] = $expdateyear;
                    $card_info['cvv'] = $creditcard_cvv;
                    $shipping_info['firstName'] = $passenger_profile[0]['name'];
                    
                    // Payment gateway transaction non-mandatory parameters
                    $shipping_info['lastName'] = isset($passenger_profile[0]['lastname'])?$passenger_profile[0]['name']:'';
                    $shipping_info['email'] = '';
                    $shipping_info['company'] = '';
                    $shipping_info['phone'] = '';
                    $shipping_info['fax'] = '';
                    $shipping_info['website'] = '';
                    $shipping_info['company'] = '';
                    $shipping_info['street'] = '';
                    $shipping_info['state'] = '';
                    $shipping_info['country_code'] = '';
                    $shipping_info['zip_code'] = '';

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
                                $fresult=$paymentresponse['payment_response'];
                                $fcardtype='';
                                $preauthorize_amount= isset($paymentresponse['preauthorize_amount'])?$paymentresponse['preauthorize_amount']:$transaction_amount;
				/*foreach (($result->errors->deepAll()) as $error) {
					$fresult .= $error->message;
				}*/
			}
                }    else
		{
			$fresult='Passenger Data Not Available';
			$code=0;
			$fcardtype='';
			
		}
                        return array($code,$fresult,$fcardtype,$preauthorize_amount);		
		
	}
	
	
	//** function to check whether the booking is later or not **//
	public function get_booking_details($tripID)
	{
		$result = $temp_arr = array();
		$args = array(array('$lookup' => array('from' => MDB_PASSENGERS,
											   'localField' => 'passengers_id',
											   'foreignField' => '_id',
											   'as' => 'passenger')),
					  array('$unwind' => '$passenger'),
					  array('$lookup' => array('from' => MDB_PEOPLE,
											   'localField' => 'driver_id',
											   'foreignField' => '_id',
											   'as' => 'people')),
					  array('$unwind' => '$people'),
					  array('$match' => array('_id' => (int)$tripID)),
					  array('$project' => array('passenger_phone'=>array('$concat'=>array('$passenger.country_code','$passenger.phone')),
												'name' => '$passenger.name',
												'email' => '$passenger.email',
												'driver_phone' => '$people.phone',
												'driver_id' => '$driver_id',
												'passengers_id' => '$passengers_id'))					  
					);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				 
				 $temp_arr['passenger_phone'] = $r['passenger_phone'];
				 $temp_arr['name'] = $r['name'];
				 $temp_arr['email'] = $r['email'];
				 $temp_arr['driver_id'] = $r['driver_id'];
				 $temp_arr['driver_phone'] = $r['driver_phone'];
				 $temp_arr['passengers_id'] = $r['passengers_id'];
				 
				 $result[] = $temp_arr;
			}
		}
		return $result;
	}
	
	/** Function to get splitted Passenger details to send push notification to them **/
	public function getSplitPassengersDetails($trip_id)
	{
		$result = $temp_arr = array();
		$args = array(array('$unwind' => '$split_details'),
					  array('$lookup' => array('from' => MDB_PASSENGERS,
											   'localField' => 'split_details.friends_p_id',
											   'foreignField' => '_id',
											   'as' => 'passenger')),
					  array('$unwind' => '$passenger'),
					  array('$match' => array('_id' => (int)$trip_id)),
					  array('$project' => array('passenger_id' => '$passenger._id',
												'phone' => '$passenger.phone',
												'wallet_amount' => array('$ifNull' => array('$passenger.wallet_amount', 0 )),
												'name' => '$passenger.name',
												'profile_image' => '$passenger.profile_image',
												'device_token' => '$passenger.device_token',
												'device_id' => '$passenger.device_id',
												'device_type' => '$passenger.device_type',
												'primary_pass_id' => '$passengers_id',
												'current_location' => '$current_location',
												'drop_location' => '$drop_location',
												'total_fare' => '$approx_fare',
												'split_fare' => '$split_details.appx_amount',
												'fare_percentage' => '$split_details.fare_percentage',
												'approve_status' => '$split_details.approve_status',
												'passenger_payment_option' => '$split_details.passenger_payment_option'))
					);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr['passenger_id'] = $r['passenger_id'];
				$temp_arr['phone'] = $r['phone'];
				$temp_arr['wallet_amount'] = $r['wallet_amount'];
				$temp_arr['name'] = $r['name'];
				$temp_arr['profile_image'] = (isset($r['profile_image']))?$r['profile_image']:"";
				$temp_arr['device_token'] = (isset($r['device_token']))?$r['device_token']:"";
				$temp_arr['device_id'] = isset($r['device_id'])?$r['device_id']:"";
				$temp_arr['device_type'] = isset($r['device_type'])?$r['device_type']:"";
				$temp_arr['primary_pass_id'] = isset($r['primary_pass_id'])?$r['primary_pass_id']:'';
				$temp_arr['current_location'] = isset($r['current_location'])?$r['current_location']:'';
				$temp_arr['drop_location'] = isset($r['drop_location'])? $r['drop_location']:'';
				$temp_arr['total_fare'] = isset($r['total_fare'])?$r['total_fare']:'';
				$temp_arr['split_fare'] = isset($r['split_fare'])?$r['split_fare']:'';
				$temp_arr['fare_percentage'] = isset($r['fare_percentage'])?$r['fare_percentage']:'';
				$temp_arr['approve_status'] = isset($r['approve_status'])?$r['approve_status']:'I';
				$temp_arr['passenger_payment_option'] = isset($r['passenger_payment_option'])?$r['passenger_payment_option']:'';
				
				$result[] = $temp_arr;
			}
		}
		return $result;
	}
	
	/** Function to send push notification to passenger **/
    public function send_pushnotification($d_device_token="",$device_type="",$pushmessage=null,$android_api="")
    { 
	   if($device_type == 1)
	   {
		   //---------------------------------- ANDROID ----------------------------------//		  
			$apiKey = $android_api;
			$registrationIDs = array($d_device_token);
			// Message to be sent
			if(!empty($registrationIDs))
			{
				// Set POST variables
				$url = 'https://android.googleapis.com/gcm/send';
				$pushmessage = json_encode($pushmessage);
				$fields = array('registration_ids' => $registrationIDs,'data' => array( "message" => $pushmessage));
				$headers = array('Authorization: key=' . $apiKey, 'Content-Type: application/json');
				// Open connection
				$ch = curl_init();
				// Set the url, number of POST vars, POST data
				curl_setopt( $ch, CURLOPT_URL, $url );
				curl_setopt( $ch, CURLOPT_POST, true );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
				// Execute post
				$result = curl_exec($ch);
				// Close connection
				curl_close($ch);
				//echo $result;                            
			}
		}
		elseif($device_type == 2)
		{                          
			//---------------------------------- IPHONE ----------------------------------// 
			$deviceToken = trim($d_device_token);                                                                                      
			if(!empty($deviceToken))
			{
				// Put your private key's passphrase here:
				$passphrase = '1234';
				// Put your alert message here:
				//$message = $message = "A new business ".$business_name." is added in Yiper";
				//$message = $deal_id.".".ucfirst($merchant_name)." has a new deal for you. View now...";                                    
				$badge = 0;
				////////////////////////////////////////////////////////////////////////////////
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PUBLIC_UPLOADS_FOLDER.'/iOS/push_notification/ck.pem')){
					$root = $_SERVER['DOCUMENT_ROOT'].'/'.PUBLIC_UPLOADS_FOLDER.'/iOS/push_notification/ck.pem';
				}else{
					$root = $_SERVER['DOCUMENT_ROOT'].'/application/classes/controller/ck.pem' ;
				}				
				$ctx = stream_context_create();
				stream_context_set_option($ctx, 'ssl', 'local_cert',$root );
				stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

				// Open a connection to the APNS server
				$fp = stream_socket_client(
					'ssl://gateway.push.apple.com:2195', $err,
					$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
				if (!$fp)
					exit("Failed to connect: $err $errstr" . PHP_EOL);
				//echo 'Connected to APNS' . PHP_EOL; 
				// Create the payload body
				//$message=$pushmessage['message'];
				$message = "Success";
				$badge = isset($pushmessage['badge']) ? $pushmessage['badge']:'0';
				$body['aps'] = array(
					'alert' => $message,
					'trip_details' => $pushmessage,
					'sound' => 'default',
					'badge' => $badge
					);	
				// Encode the payload as JSON
				$payload = json_encode($body);
				//print_r($payload);exit;
				// Build the binary notification
				$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
				// Send it to the server
				$result = fwrite($fp, $msg, strlen($msg));
				fclose($fp);  
			} 
		}		
	}
	/** Function to get total pending fare percentage for a particular trip **/
	public function getpendingFarePercentage($trip_id)
	{
		$match =  array('_id' => (int)$trip_id,
						'split_details.approve_status' => array('$ne'=>'A')
						);
		$args = array(array('$unwind' => '$split_details'),
					  array('$lookup' => array('from' => MDB_PASSENGERS,
											   'localField' => 'split_details.friends_p_id',
											   'foreignField' => '_id',
											   'as' => 'passenger')),
					  array('$unwind' => '$passenger'),
					  array('$match' => $match),
					  array('$project' => array('fare_percentage' => '$split_details.fare_percentage' )),
					  array('$group'=>array('_id'=>null,
										 'total'=> array('$sum'=>'$fare_percentage')))				  
					);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		//print_r($result);exit;
		return isset($result['result'][0]['total']) ? $result['result'][0]['total'] : 0;
	}
	/** Function to get split fare details for a particular trip **/
	public function getTripSplitFareDets($tripid,$approve_status = '')
	{		
		$result = array();
		$match =  array('_id' => (int)$tripid);
		$match['passenger.creditcard_details.default_card'] = 1;
		if($approve_status != ''){
			$match['split_details.approve_status'] = $approve_status;
		}
		$args = array(array('$unwind' => '$split_details'),
					  array('$lookup' => array('from' => MDB_PASSENGERS,
											   'localField' => 'split_details.friends_p_id',
											   'foreignField' => '_id',
											   'as' => 'passenger')),
					  array('$unwind' => '$passenger'),
					  array('$unwind' => '$passenger.creditcard_details'),
					  array('$match' => $match),
					  array('$project' => array('friends_p_id' => '$split_details.friends_p_id',
												'fare_percentage' => '$split_details.fare_percentage',
												'approve_status' => '$split_details.approve_status',
												'used_wallet_amount' => '$split_details.used_wallet_amount',
												'card_type' => '$passenger.creditcard_details.card_type',
												'creditcard_no' => '$passenger.creditcard_details.creditcard_no',
												'creditcard_cvv' => '$passenger.creditcard_details.creditcard_cvv',
												'expdatemonth' => '$passenger.creditcard_details.expdatemonth',
												'expdateyear' => '$passenger.creditcard_details.expdateyear',
												'card_holder_name' => '$passenger.creditcard_details.card_holder_name',
												'firstname' => '$passenger.name',
												'lastname' => '$passenger.lastname',
												'email' => '$passenger.email',
												'phone' => array('$concat'=>array('$passenger.country_code','$passenger.phone'))
												)),
					  array('$sort'=>array('_id'=>-1))
					);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr = $r;
				$temp_arr['card_holder_name'] = isset($r['card_holder_name']) ? $r['card_holder_name']:'';				
				$result[] = $temp_arr;
			}			
		}
		return $result;
	}
	
    /** function to insert failure transaction **/
	public function insertFailureTransact($tripId, $passengerId, $failureAmount, $failureDate)
	{
		$inc_id = Commonfunction::get_auto_id(MDB_FAILED_TRANSACTION);
		$insert_arr = array('_id' => (int)$inc_id,
							'trip_id' => (int)$tripId,
							'passenger_id' => (int)$passengerId,
							'failure_amount' => (double)$failureAmount,
							'failure_date' => Commonfunction::MongoDate(strtotime($failureDate)),
						);
		$result = $this->mongo_db->insertOne(MDB_FAILED_TRANSACTION,$insert_arr);
		return 1;
	}
	/** Function to check the entered referral code of a particular driver is exist or not **/
	public function checkDriverReferralExists($driverReferralCode)
	{
		$project = array('registered_driver_id',
						 'registered_driver_code_amount',
						 'registered_driver_wallet');
		$match = array('registered_driver_code' => $driverReferralCode);
		$result = $this->mongo_db->findOne(MDB_DRIVER_REF,$match,$project);
		return (!empty($result)) ? array($result): array(); 
	}
	/** Function to check the driver has already used the referral code or not **/
	public function checkDriverUsedReferral($driverId)
	{
		$match = array('registered_driver_id' => (int)$driverId, 'referred_driver_id' => 0);		
		$result = $this->mongo_db->findOne(MDB_DRIVER_REF,$match,array('_id'));
		//print_r($result);exit;
		return (!empty($result)) ? 1 : 0;
	}
	//** Function to check the passenger's credit card has expired  **//
	public function check_credit_card_expire($passenger_id, $company_id)
	{
		$get_company_time_details = $this->get_company_time_details($company_id);
		$current_time = $get_company_time_details['current_time'];
		$currentmonth = date("m", strtotime($current_time));
		$currentyear = date("Y", strtotime($current_time));
		
		$match_query = array( "\$and" => array(array('creditcard_details.passenger_id' => (int)$passenger_id ),array('creditcard_details.default_card' => '1' ),array("\$or"=>array(array( 'creditcard_details.expdatemonth' => array('$gt' =>$currentmonth)) , array( 'creditcard_details.expdateyear' => $currentyear)),array( 'expdateyear' =>  array('$gt' =>$currentyear) ) ) ) );
		$arguments = array(
			array(
				'$unwind' => '$creditcard_details'
			),
			array(
				'$match' => $match_query
			),
			 array(
				'$project' => array(
					'result' => '$_id'
				)
			),
			array(
				'$group' => array(
					'_id' => NULL,
					'count' => array(
						'$sum' => 1
					)
				)
			)
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS, $arguments);
        //echo "<pre>if";print_r($result['result']);exit;
        return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
	}
	
	public function driver_referral_pending_amount($driver_id)
	{
		
     $args = array(array('$match' => array('wallet_request_driver' => (int)$driver_id, 'wallet_request_status' => 1)),
				   array('$project' => array('driver_referral_wallet_pending_amount' => array('$cond'=> array('if'=>
				   array('$eq'=>array('$wallet_request_status', 1 )), 'then'=> '$wallet_request_amount', 'else'=> null )))),
				   array('$group'=>array('_id'=>null,
										 'driver_referral_wallet_pending_amount'=> array('$sum'=>'$driver_referral_wallet_pending_amount'))
						)
				);
		$res = $this->mongo_db->Aggregate(MDB_DRIVER_WALLET_REQ,$args);
		return (!empty($res['result'])) ? $res['result'] : array(); 
     
	}
	
	public function driver_withdraw_pending_amount($company_id,$driver_id)
	{
		  $args = array(array('$match' => array('requester_id' => (int)$driver_id, 'request_status' => 0)),
				   array('$project' => array('pending_amount' => array('$cond'=> array('if'=>
				   array('$eq'=>array('$request_status', 0 )), 'then'=> '$withdraw_amount', 'else'=> null )))),
				   array('$group'=>array('_id'=>null,
										 'pending_amount'=> array('$sum'=>'$pending_amount'))
						)
				);
		$res = $this->mongo_db->Aggregate(MDB_WITHDRAW_REQUEST,$args);
		//print_r($res );exit;
		return (!empty($res['result'])) ? $res['result'] : array(); 
		
	}
	
	
	//credit card validation for braintree
	public function voidTransactionAfterPreAuthorize($cardId, $preTransactId)
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
		/*if($payment_status==1){
			$args = array(array('$match' => array('creditcard_details.passenger_cardid' => (int)$cardId)),
					array('$unwind' => '$creditcard_details'),					
					array('$project' => array('card_id' => '$creditcard_details.passenger_cardid'))
		);
		$keys = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
		//echo $cardId;print_r($keys);exit;
		$i =0;$val = array();
		foreach($keys['result'] as $k => $v ){
			if(isset($v['card_id']) && $v['card_id'] == $cardId){
				$val["creditcard_details.$i.void_status"] = 1;                                                
			}
			$i++;
		}
		$match = array('creditcard_details.passenger_cardid' => (int)$cardId);
		if(!empty($val)){
			//$result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$val),array('upsert' => false));
		}		
			
		}*/			
		return $payment_status;		
	}

/** Function to get driver's referral code and amount to invite **/
	public function getDriverReferralDetails($driverId)
	{
		$result = array();
		$options=[
				'projection'=>[
						'registered_driver_id'=>1,                               
						'registered_driver_code'=>1,                               
						'registered_driver_code_amount'=>1,                               
						'registered_driver_wallet'=>1                              
					]
				];
						 
		$match = array('registered_driver_id' => (int)$driverId);
		$res = $this->mongo_db->find(MDB_DRIVER_REF,$match,$options);
		$res = $res;
		$res = commonfunction::change_key($res);
		if(!empty($res)){
			foreach($res as $r){
				
				$temp_arr = $r;
				$temp_arr['registered_driver_id'] = isset($temp_arr['registered_driver_id'])?$temp_arr['registered_driver_id']:'';
				$temp_arr['registered_driver_code'] = isset($temp_arr['registered_driver_code'])?$temp_arr['registered_driver_code']:'';
				$temp_arr['registered_driver_code_amount'] = isset($temp_arr['registered_driver_code_amount'])?$temp_arr['registered_driver_code_amount']:'';
				$temp_arr['registered_driver_wallet'] = isset($temp_arr['registered_driver_wallet'])?$temp_arr['registered_driver_wallet']:'';
				$result[] = $temp_arr;
			}			
		}
		//print_r($res);exit;
		return $result;
	}
	
	/** Save driver wallet requests **/
	public function saveDriverWalletRequest($driverId,$driverWalletAmount,$currentTime)
	{
		$requestStatus = '1';
		$inc_id = Commonfunction::get_auto_id(MDB_DRIVER_WALLET_REQ);
		$insert_arr = array('_id' => (int)$inc_id,
							'wallet_request_driver' => (int)$driverId,
							'wallet_request_amount' => (double)$driverWalletAmount,
							'wallet_request_date' => Commonfunction::MongoDate(strtotime($currentTime)),
							'wallet_request_approveed_date' => Commonfunction::MongoDate(strtotime('1969-01-01')),
							'wallet_request_approved_by' => 0,
							'wallet_request_status' => (int)$requestStatus);
		$result = $this->mongo_db->insertOne(MDB_DRIVER_WALLET_REQ,$insert_arr);
		return (empty($result->getwriteErrors())) ? 1 :0;
	}
	
	/** get wallet requested list **/
	public function getWalletAmtRequests($driverId)
	{
		$result = array();
		$args = array(array('$match' => array('wallet_request_driver' =>(int)$driverId)),
					  array('$project' => array(
							'wallet_request_id' => '$_id',
							'wallet_request_amount' => '$wallet_request_amount',
							'status' => array('$concat' =>array(
								array('$cond' => array(array('$eq' => array('$wallet_request_status',2)),'Approved','')),
								array('$cond' => array(array('$eq' => array('$wallet_request_status',3)),'Reject','')),
								array('$cond' => array(array('$lt' => array('$wallet_request_status',2)),'Pending','')),
							)),
							'wallet_request_date' => '$wallet_request_date'))
					  );
		$res = $this->mongo_db->aggregate(MDB_DRIVER_WALLET_REQ,$args);
		
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr['wallet_request_id'] = isset($r['wallet_request_id']) ? $r['wallet_request_id'] : '';
				$temp_arr['wallet_request_amount'] = isset($r['wallet_request_amount']) ? $r['wallet_request_amount'] : '';
				$temp_arr['wallet_request_date'] = isset($r['wallet_request_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['wallet_request_date']) : '';
				$temp_arr['status'] = isset($r['status']) ? $r['status'] : '0';
				
				$result[] = $temp_arr;
			}
		}
		return $result;
	}
	
	public function updateUsedWalletAmount($actusedWalAmount,$passengerId,$tripId)
	{
		$match = array('_id'=>(int)$tripId);
		$args = array(array('$unwind' => '$split_details'),
				  array('$match' => $match),
				  array('$project' => array('friend_id' => '$split_details.friends_p_id'))
				);
		$keys = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		$i =0;$val = array();
		foreach($keys['result'] as $k => $v ){
			if($v['friend_id'] == $passengerId){
				$val["split_details.$i.used_wallet_amount"] = (double)$actusedWalAmount;
			}
			$i++;
		}
		
		$match['split_details.friends_p_id'] = (int)$passengerId;
		$update = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,$match,array('$set'=>$val),array('upsert' => false));
		return (empty($update->getwriteErrors())) ? 1 : 0;
	}
	
	/** Function to get the referred driver id **/
	public function getReferredDriver($driverId)
	{
		$match = array('registered_driver_id' => (int)$driverId,'referral_status' =>0);
		$result = $this->mongo_db->findOne(MDB_DRIVER_REF,$match,array('referred_driver_id'));
		return (isset($result)) ? $result['referred_driver_id'] : array(); 
	}
	
	public function update_split_log_table($datas ='',$friend_id='',$trip_id=''){
	
		$match = array('_id'=>(int)$trip_id);
		$args = array(array('$unwind' => '$split_details'),
				  array('$match' => $match),
				  array('$project' => array('friend_id' => '$split_details.friends_p_id'))
				);
		$keys = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);			
		$i =0;$val = array();
		foreach($keys['result'] as $k => $v ){
			if($v['friend_id'] == $friend_id){
				
				$val["split_details.$i.notification_status"] = (int)$datas['notification_status'];
			}
			$i++;
		}
		//$match['split_details.trip_id'] = (int)$trip_id;
		$match['split_details.friends_p_id'] = (int)$friend_id;
		$update = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,$match,array('$set'=>$val),array('upsert' => false));
	}
	
	/** Function to get driver's Profile image **/
	public function getDriverProfileImage($driverId)
	{
		$result = $this->mongo_db->findOne(MDB_PEOPLE,array('_id' =>(int)$driverId,'user_type' => 'D'),array('profile_picture'));
		return (isset($result['profile_picture'])) ? $result['profile_picture'] : '';
	}
	
	/** Function to get split fare details for a particular trip **/
	public function getSplitFareStatus($tripId)
	{
		$result = $temp_arr = array();
		$args = array(
					array('$match' => array('_id' => (int)$tripId)),
					array('$unwind' => '$split_details'),
					array('$lookup' => array('from' => MDB_PASSENGERS,
							'localField' => 'split_details.friends_p_id',
							'foreignField' => '_id',
							'as' => 'passenger')),
					array('$unwind' => '$passenger'),
					array('$project' => array(
											'_id' => 0,
											'firstname' => '$passenger.name',
											'profile_image' => '$passenger.profile_image',
											'approve_status' => '$split_details.approve_status',
											'fare_percentage' => '$split_details.fare_percentage',
											'used_wallet_amount' => '$split_details.used_wallet_amount'
						))
				);
		$res = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr['firstname'] = isset($r['firstname']) ? $r['firstname']: '';
				$temp_arr['profile_image'] = isset($r['profile_image']) ? $r['profile_image']: '';
				$temp_arr['approve_status'] = isset($r['approve_status']) ? $r['approve_status']: '';
				$temp_arr['fare_percentage'] = isset($r['fare_percentage']) ? $r['fare_percentage']: '';
				$temp_arr['used_wallet_amount'] = isset($r['used_wallet_amount']) ? $r['used_wallet_amount']: '';
				
				$result[] = $temp_arr;
			}
		}
        return $result;
	}	
	
	/** function to get favourite driver for a passenger **/
	public function getFavDrivers($passengerId)
	{
		$result = '';
		$res = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$passengerId),array('favourite_drivers'));
		
		$favourite_drivers = isset($res['favourite_drivers']) ? (array)$res['favourite_drivers'] : array();
		$result = !empty($favourite_drivers) ? implode(',',$favourite_drivers) : '';
		return $result;
	}
	/**  **/
	
	public function setdrivercheck($array = ''){
		$this->mongo_db->insertOne('driver_check', $array);		
	}
	
	/**  **/
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
	
	// Driver Login Status
	public function driver_logged_status($data)
	{
		if(isset($data['driver_id']) && !empty($data['driver_id'])){			
			$condition_arr = array('_id' => (int)$data['driver_id'],'user_type'=>'D');
			if(isset($data['company_id']) && !empty($data['company_id'])){
				$condition_arr = array('_id' => (int)$data['driver_id'],'user_type'=>'D','company_id' => (int)$data['company_id']);
			}
			$result = $this->mongo_db->findOne(MDB_PEOPLE,$condition_arr,array('login_status'));
			return (count($result) > 0 && $result['login_status'] == 'S')?1:0;
		}else{
			return 0;
		}
		
	}
	
	/** Get Company id for the Driver **/
	public function get_driver_companyid($data)
	{
		$company = (isset($data['company_id']))?$data['company_id']:'';
		if(isset($data['driver_id']) && !empty($data['driver_id'])){
			$result = $this->mongo_db->findOne(MDB_PEOPLE,array('_id' => (int)$data['driver_id']),array('company_id'));
			$company = (count($result) > 0 && $result['company_id']!="")?$result['company_id']:$company;
		}
		return $company;
	}
		
	
	public function driver_current_trip_status($data)
	{
		$result = array();
		$company = (isset($data['company_id']))?$data['company_id']:'';
		$get_company_time_details = $this->get_company_time_details($company);
		$current_time = Commonfunction::MongoDate(strtotime($get_company_time_details['current_time']));
		$match_query = array('_id' => (int)$data['driver_id'],
							'status'=>'F',
							//'shift_status'=>'IN',
							'driver_mapping.mapping_enddate'=>array('$gt'=>$current_time));
		$arguments = array(
			array('$lookup'=>array(
				'from'=>MDB_TAXI_DRIVER_MAPPING,
				'localField'=>"_id",
				'foreignField'=>"mapping_driverid",
				'as'=>"driver_mapping"
			)),
			array('$unwind' => array('path' => '$driver_mapping', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup'=>array(
				'from'=>MDB_COMPANY,
				'localField'=>"driver_mapping.mapping_companyid",
				'foreignField'=>"_id",
				'as'=>"company"
			)),
			array('$unwind' => array('path' => '$company', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup'=>array(
				'from'=>MDB_TAXI,
				'localField'=>"driver_mapping.mapping_taxiid",
				'foreignField'=>"_id",
				 'as'=>"taxi"
			)),
			array('$unwind' => array('path' => '$taxi', 'preserveNullAndEmptyArrays' => true )),
			array('$match' => $match_query),
			array(
				'$project' => array(
					'mapping_taxiid' => '$driver_mapping.mapping_taxiid',
					'status' => '$status',
					'shift_status' => '$shift_status',
					'taxi_model' => '$taxi.taxi_model',
					'brand_type' => '$company.companyinfo.company_brand_type',
					'default_unit' => '$company.companyinfo.default_unit',
				)
			)
		);
		$res = $this->mongo_db->aggregate(MDB_DRIVER_INFO,$arguments);
		if(!empty($res['result'])){
			
			$res_array = $res['result'];
			
            $result[0]['mapping_taxiid'] = isset($res_array[0]['mapping_taxiid']) ? $res_array[0]['mapping_taxiid']:'';
            $result[0]['status'] = isset($res_array[0]['status']) ? $res_array[0]['status']:'';
            $result[0]['shift_status'] = isset($res_array[0]['shift_status']) ? $res_array[0]['shift_status']:'';
            $result[0]['taxi_model'] = isset($res_array[0]['taxi_model']) ? $res_array[0]['taxi_model']:'';
            $result[0]['brand_type'] = isset($res_array[0]['brand_type']) ? $res_array[0]['brand_type']:'';
            $result[0]['default_unit'] = isset($res_array[0]['default_unit']) ? $res_array[0]['default_unit']:'';
		}
        //print_r($result);exit;
        return (!empty($result) ? $result: array());
	}
	
	/** Save Driver street trip **/
	public function save_street_trip($data)
	{
		//$date = Commonfunction::MongoDate(strtotime($this->currentdate));
		$approx_fare = "";//commonfunction::real_escape_string($val['approx_fare']);
		$city_name = Commonfunction::getCityName($data['pickup_latitude'],$data['pickup_longitude']);
		$pickupTimezone = $this->getpickupTimezone($data['pickup_latitude'],$data['pickup_longitude']);
		$update_time = convert_timezone('now',$pickupTimezone);
		$date = Commonfunction::MongoDate(strtotime($update_time));
		$inc_id = Commonfunction::get_auto_id(MDB_PASSENGERS_LOGS);
		$insert_array = array(
			'_id' => (int)$inc_id,
			'driver_id' => (int)$data['driver_id'],
			'taxi_id' => (int)$data['taxi_id'],
			'company_id' => (int)$data['company_id'],
			'current_location' => urldecode($data['pickup_location']),
			'pickup_latitude' => (double)$data['pickup_latitude'],
			'pickup_longitude' => (double)$data['pickup_longitude'],
			'city_name' => $city_name,
			'drop_location' => urldecode($data['drop_location']),
			'drop_latitude' => (double)$data['drop_latitude'],
			'drop_longitude' => (double)$data['drop_longitude'],
			'approx_distance' => $data['approx_distance'],
			'approx_fare' => (double)$data['approx_fare'],
			'time_to_reach_passen' => $data['time_to_reach_passen'],
			'pickup_time' => $date,
			'actual_pickup_time' => $date,
			'dispatch_time' => null,
			'travel_status' => 2,
			'driver_reply' => 'A',
			'notification_status' => 2,
			'createdate' => $date,
			'booking_from' => 2,
			'bookingtype' => 1,
			'bookby' => 3,
			'taxi_modelid' => (int)$data['motor_model'],
			'is_split_trip' => 0,
			'driver_app_version' => $data['driver_app_version'],
			'distance' => 0,
			'pickupdrop'=>0,
			'fixedprice'=>0,
			'search_city'=>'',
			'logs' => array(),
			'split_details' => array()
		);
		$result = $this->mongo_db->insertOne(MDB_PASSENGERS_LOGS,$insert_array);
		return (string)$inc_id;
	}
	
	public function insert_withdraw_request($company_id,$driver_id,$request_amount)
	{
		$uniqueId = Commonfunction::checkRequestID();
		$date = Commonfunction::MongoDate(strtotime($this->currentdate));
		$inc_id = Commonfunction::get_auto_id(MDB_WITHDRAW_REQUEST);
		$insert_array = array(
			'_id' => (int)$inc_id,
			'company_id' => (int)$company_id,
			'request_id' => $uniqueId,
			'brand_type' => 1,
			'requester_id' => (int)$driver_id,
			'withdraw_amount' => (float)$request_amount,
			'request_date' => $date,
			'request_status' => 0,
			'type' => 0,
			'withdraw_request_log' => array()
		);
		$result = $this->mongo_db->insertOne(MDB_WITHDRAW_REQUEST,$insert_array);
		return $inc_id;
	}
	
	//Get Driver Recent Trip List	
	public function get_recent_driver_trip_list($data)
	{
		$result = array();
		$match_query = array('driver_id' => (int)$data['driver_id'],'travel_status'=>1,'drop_time'=>array('$ne'=>null));
		$arguments = array(
			array('$lookup'=>array(
				'from'=>MDB_MOTOR_MODEL,
				'localField'=>"taxi_modelid",
				'foreignField'=>"_id",
				'as'=>"motor_model"
			)),
			array('$unwind' => array('path' => '$motor_model', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup'=>array(
				'from'=>MDB_TRANSACTION,
				'localField'=>"_id",
				'foreignField'=>"passengers_log_id",
				 'as'=>"transaction"
			)),
			array('$unwind' => array('path' => '$transaction', 'preserveNullAndEmptyArrays' => true )),
			array('$match' => $match_query),
			array(
				'$project' => array(
					'drop_time' => '$drop_time',
					//'fare' => '$transaction.tripfare',
					'fare'  =>  array('$ifNull'  =>  array(array( '$sum'  => array('$transaction.amt', '$used_wallet_amount') ), 0)),
					'model_name' => '$motor_model.model_name',
				)
			),
			array(
				'$sort' => array('drop_time' => -1)
			),
			array('$limit' => 3)
		);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		$data = array();
		if(!empty($res['result'])){
			foreach($res['result'] as $val){
				$data['drop_time'] = Commonfunction::convertphpdate('Y-m-d H:i:s',$val['drop_time']);
				$data['fare'] = isset($val['fare'])?$val['fare']:0;
				$data['model_name'] = (isset($val['model_name']))?$val['model_name']:"";
				$result[] = $data;
			}
		}
        return $result;
	}
	
	public function search_withdraw_request($data)
	{
		$match_query['company_id'] = (int)$data['company_id'];
		$match_query['requester_id'] = (int)$data['driver_id'];
		$match_query['type'] = 0;
		if(isset($data['status']) && $data['status'] !=''){
			$match_query['request_status'] = (int)$data['status'];
		}		
		if(isset($data['from']) && isset($data['to'])){
			
			$data['from'] = urldecode($data['from']);
			$data['to'] = urldecode($data['to']);			
			if($data['from']!="" && $data['to']!=""){
				$match_query['request_date'] = array('$gte' => Commonfunction::MongoDate(strtotime($data['from'])), '$lte' => Commonfunction::MongoDate(strtotime($data['to'])));
			}elseif($data['from']!=""){
				$match_query['request_date'] = array('$gte' => Commonfunction::MongoDate(strtotime($data['from'])));
			}
			elseif($data['to']!=""){
				$match_query['request_date'] = array('$lte' => Commonfunction::MongoDate(strtotime($data['to'])));
			}
		}
		//print_r($match_query);exit;
		//$project = array('_id','request_id','withdraw_amount','request_date','request_status');
		$options=[
				'projection'=>[
				   '_id'=>1,                               
				   'request_id'=>1,                               
				   'withdraw_amount'=>1,                               
				   'request_date'=>1,                               
				   'request_status'=>1
					]
				];
		$result = $this->mongo_db->Find(MDB_WITHDRAW_REQUEST,$match_query,$options);
		$res = $result;
		$data = $final_array = array();
		if(isset($res) && count($res) > 0){
			$i=0;
			foreach($res as $val) {
				$data['withdraw_request_id'] = $val['_id'];
				$data['request_id'] = $val['request_id'];
				$data['withdraw_amount'] = $val['withdraw_amount'];
				$data['request_date'] = Commonfunction::convertphpdate('Y-m-d H:i:s',$val['request_date']);
				$data['request_status'] = isset($val['request_status']) ? $val['request_status']:'0';
				$final_array[$i] = $data;
				$i++;
			}	
		}
		return $final_array;
	}
	
	/*** Street Pickup Passenger Details ***/
	public function getPassengerLogDetail($trip_id = "")
	{	
		$args = array(
			array('$lookup' => array(
				'from' => MDB_PASSENGERS,
				'localField' => 'passengers_id',
				'foreignField' => '_id',
				'as' => 'pass',
			)),
			array('$unwind' => array('path' => '$pass', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup' => array(
				'from' => MDB_TRANSACTION,
				'localField' => '_id',
				'foreignField' => 'passengers_log_id',
				'as' => 'trans',
			)),
			array('$unwind' => array('path' => '$trans', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup' => array(
				'from' => MDB_COMPANY,
				'localField' => 'company_id',
				'foreignField' => '_id',
				'as' => 'comp',
			)),
			array('$unwind' => array('path' => '$comp', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup' => array(
				'from' => MDB_PEOPLE,
				'localField' => 'driver_id',
				'foreignField' => '_id',
				'as' => 'people',
			)),
			array('$unwind' => array('path' => '$people', 'preserveNullAndEmptyArrays' => true )),
			array('$match' => array('_id' => (int)$trip_id)),
			array('$project' =>array('_id' => 0,
					'phone'=>'$people.phone',
					'promocode'=>'$promocode',
					'is_split_trip'=>'$is_split_trip',
					'booking_key'=>'$booking_key',
					'passengers_id'=>'$passengers_id',
					'driver_id'=>'$driver_id',
					'taxi_id'=>'$taxi_id',
					'taxi_modelid'=>'$taxi_modelid',
					'company_id'=>'$company_id',
					'current_location'=>'$current_location',
					'pickup_latitude'=>'$pickup_latitude', 
					'pickup_longitude'=>'$pickup_longitude',
					'drop_latitude'=>'$drop_latitude',
					'drop_longitude'=>'$drop_longitude',
					'no_passengers'=>'$no_passengers',
					'approx_distance'=>'$approx_distance',
					'approx_duration'=>'$approx_duration',
					'approx_fare'=>'$approx_fare',
					'fixedprice'=>'$fixedprice',
					'time_to_reach_passen'=>'$time_to_reach_passen',
					'pickup_time'=>'$pickup_time',
					'actual_pickup_time'=>'$actual_pickup_time',
					'drop_time'=>'$drop_time',
					'account_id'=>'$account_id',
					'accgroup_id'=>'$accgroup_id',
					'pickupdrop'=>'$pickupdrop',
					'rating'=>'$rating',
					'comments'=>'$comments',
					'travel_status'=>'$travel_status',
					'driver_reply'=>'$driver_reply',
					'msg_status'=>'$msg_status',
					'createdate'=>'$createdate',
					'booking_from'=>'$booking_from',
					'search_city'=>'$search_city',
					'sub_logid'=>'$sub_logid',
					'bookby'=>'$bookby',
					'booking_from_cid'=>'$booking_from_cid',
					'distance'=>'$distance',
					'notes_driver'=>'$notes_driver',
					'used_wallet_amount'=>'$used_wallet_amount',
					'driver_name'=>'$people.name',
					'passenger_discount'=>'$pass.discount',
					'driver_phone'=>'$people.phone',
					'driver_photo'=>'$people.profile_picture',
					'driver_device_id'=>'$people.device_id',
					'driver_device_token'=>'$people.device_token',
					'driver_device_type'=>'$people.device_type',
					'passenger_discount'=>'$pass.discount',
					'passenger_device_id'=>'$pass.device_id',
					'passenger_device_token'=>'$pass.device_token',
					'referred_by'=>'$pass.referred_by',
					'referrer_earned'=>'$pass.referrer_earned',
					'wallet_amount'=>'$pass.wallet_amount',
					'passenger_device_type'=>'$pass.device_type',
					'passenger_salutation'=>'$pass.salutation',
					'passenger_name'=>'$pass.name',
					'passenger_lastname'=>'$pass.lastname',
					'passenger_email'=>'$pass.email',
					'passenger_phone'=>'$phone',
					'passenger_country_code'=>'$pass.country_code',
					'cancellation_nfree'=>'$comp.companyinfo.cancellation_fare',
					'default_unit'=>'$comp.companyinfo.default_unit',
					'brand_type'=>'$comp.companyinfo.company_brand_type',
					'fare_calculation_type'=>'$comp.companyinfo.fare_calculation_type',
					'company_tax'=>'$comp.companyinfo.company_tax',
					'drop_location' =>  array('$ifNull' => array('$drop_location','null')),
					'transaction_id' => array('$ifNull' => array('$trans._id','null'))
				)
			)
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		$data = array();
		if(isset($result['result']) && count($result['result']) > 0){
			foreach($result['result'] as $key => $val) {
				$data = $val;
				$data['distance'] = (isset($val['distance']))?$val['distance']:0;
				$data['default_unit'] = (isset($val['default_unit']))?$val['default_unit']:0;
				$data['pickupdrop'] = (isset($val['pickupdrop']))?$val['pickupdrop']:0;
				$data['fixedprice'] = (isset($val['fixedprice']))?$val['fixedprice']:0;
				$data['search_city'] = (isset($val['search_city']))?$val['search_city']:'';
				$data['brand_type'] = (isset($val['brand_type']))?$val['brand_type']:'M';
				$data['fare_calculation_type'] = isset($val['fare_calculation_type'])?$val['fare_calculation_type']:FARE_CALCULATION_TYPE;
				$data['pickup_time'] = (isset($val['pickup_time']))?Commonfunction::convertphpdate('Y-m-d H:i:s',$val['pickup_time']):"";
				$data['createdate'] = (isset($val['createdate']))?Commonfunction::convertphpdate('Y-m-d H:i:s',$val['createdate']):"";
				$data['actual_pickup_time'] = (isset($val['actual_pickup_time']))?Commonfunction::convertphpdate('Y-m-d H:i:s',$val['actual_pickup_time']):"";
				$data['drop_time'] = (isset($val['drop_time']))?Commonfunction::convertphpdate('Y-m-d H:i:s',$val['drop_time']):"";
				$result['result'][$key] = (object)$data;
			}	
		}
		
		return (!empty($result['result']) ? $result['result'] : array());
	}
	
	/**
	 * Get Withdraw Requested for Detail Page
	 * return array
	 **/
	public function get_withdraw_deatil($driver_id,$withdraw_request_id)
	{	
		$match_query = array('_id' => (int)$withdraw_request_id,'requester_id'=>(int)$driver_id);
		$arguments = array(
			array('$lookup' => array(
				'from' => MDB_COMPANY,
				'localField' => 'company_id',
				'foreignField' => '_id',
				'as' => 'comp',
			)),
			array('$unwind' => array('path' => '$comp', 'preserveNullAndEmptyArrays' => true )),
			array('$match' => $match_query),
			array(
				'$project' => array(
					'withdraw_request_id' => '$_id',
					'request_id' => '$request_id',
					'withdraw_amount' => '$withdraw_amount',
					'request_date' => '$request_date',
					'request_status' => '$request_status',
					'brand_type' => '$brand_type',
					'company_name' => '$comp.companydetails.company_name',
				)
			)
		);
		$result = $this->mongo_db->aggregate(MDB_WITHDRAW_REQUEST,$arguments);
		//echo '<pre>';print_r($result);
		//print_r($arguments);
		//exit;
		$data = $final_array = array();
		if(isset($result['result']) && count($result['result']) > 0){
			$i=0;
			foreach($result['result'] as $val) {
				$data = $val;
				$data['request_date'] = Commonfunction::convertphpdate('Y-m-d H:i:s',$val['request_date']);
				$data['company_name'] = isset($val['company_name'])?$val['company_name']:"";
				$data['request_status'] = isset($val['request_status'])?$val['request_status']:"0";
				$final_array[$i] = $data;
				$i++;
			}	
		}
        return $final_array;
	}
	
	/** 
	 * Get Withdraw Requested Log
	 * return array
	 **/
	public function get_withdraw_log($withdraw_request_id)
	{
		$match_query = array('_id' => (int)$withdraw_request_id);
		$arguments = array(
			array('$unwind' => array('path' => '$withdraw_request_log', 'preserveNullAndEmptyArrays' => true )),
			array('$lookup' => array(
				'from' => MDB_WITHDRAW_PAYMENT_MODE,
				'localField' => 'withdraw_request_log.payment_mode',
				'foreignField' => '_id',
				'as' => 'withdraw_payment_mode',
			)),
			array('$unwind' => array('path' => '$withdraw_payment_mode', 'preserveNullAndEmptyArrays' => true )),
			array('$match' => $match_query),
			array(
				'$project' => array(
					'withdraw_request_id' => '$_id',
					'logs' => '$withdraw_request_log',
					'payment_mode' => '$withdraw_request_log.payment_mode',
					'transaction_id' => '$withdraw_request_log.transaction_id',
					'comments' => '$withdraw_request_log.comments',
					'status' => '$withdraw_request_log.request_status',
					'file_name' => '$withdraw_request_log.file_name',
					'createdate' => '$withdraw_request_log.createdate',
					'payment_mode_name'=>'$withdraw_payment_mode.payment_mode_name'
				)
			),
			array(
				'$sort' => array('_id' => -1)
			)
		);
		$result = $this->mongo_db->aggregate(MDB_WITHDRAW_REQUEST,$arguments);
		$data = $final_array = array();
		if(!empty($result['result'])){
			$i=0;
			if(isset($result['result'][0]['logs'])){
				foreach($result['result'] as $val) {
					$data = $val;
					$data['withdraw_request_id'] = $val['withdraw_request_id'];
					$data['payment_mode'] = isset($val['payment_mode'])?$val['payment_mode']:"";
					$data['transaction_id'] = isset($val['transaction_id'])?$val['transaction_id']:"";
					$data['comments'] = isset($val['comments'])?$val['comments']:"";
					$data['status'] = isset($val['status'])?$val['status']:"";
					$data['file_name'] = isset($val['file_name'])?$val['file_name']:"";
					$data['created_date'] = isset($val['createdate']) ? Commonfunction::convertphpdate('Y-m-d H:i:s',$val['createdate']):'';
					$data['payment_mode_name'] = isset($val['payment_mode_name'])?$val['payment_mode_name']:"";
					$final_array[$i] = $data;
					$i++;
				}	
			}
		}
		//print_r($result);exit;
        return $final_array;
	}
	
	/**
	 * Get Driver Withdraw Request
	 * return array
	 **/

	public function get_withdraw_request($company_id,$driver_id)
	{
		$match = array('company_id' => (int)$company_id,'requester_id' => (int)$driver_id,'type' => 0 );
		$options=[
				'projection'=>[
					   '_id'=>1,                               
					   'request_id'=>1,                               
					   'withdraw_amount'=>1,                               
					   'request_date'=>1,                               
					   'request_status'=>1,                               
					   'brand_type'=>1                               
					]
				];
		$res = $this->mongo_db->find(MDB_WITHDRAW_REQUEST,$match,$options);
		//print_r($res);exit;
		$result = Commonfunction::change_key($res);
		if(count($result) > 0){
			foreach($result as $key => $val) {
				$result[$key]['withdraw_request_id'] = $val['_id'];
				$result[$key]['request_date'] = Commonfunction::convertphpdate('Y-m-d H:i:s',$val['request_date']);
				$result[$key]['request_status'] = isset($val['request_status']) ? $val['request_status']: '0';
			}	
		}
		//print_r($match);exit;
		return (!empty($result)?$result:array());
	}
	
	public function get_passenger_status($passenger_id)
	{
		$res = $this->mongo_db->findOne(MDB_PASSENGERS, array('_id' => (int)$passenger_id),array('user_status'));
		$result[] = !empty($res) ? $res : array();
		return $result;
	}
	
	public function checkWalletAmount($passenger_id,$approx_trip_fare)
	{
		$count=0;
		$args = array(
					array('$match' => array('_id' => (int)$passenger_id)),
					array('$project' => array('count' => array('$cond' => array('if' => array('$gt' => array('$wallet_amount', $approx_trip_fare) ), 
											'then' =>  1, 'else' => 0))))
				);		
		$res = $this->mongo_db->Aggregate(MDB_PASSENGERS, $args);
		if(!empty($res['result'])){
			$count = $res['result'][0]['count'];
		}
		return $count;
	}

	public function new_password_update($array,$random_key,$company_id)
	{
		$password = md5($random_key);
		$update_array = array('password' => $password,
							//'org_password' => $random_key
							);
		$match['phone'] = $array['phone_no'];
		if($array['user_type'] == 'P') {
			if($company_id != '') {
				$match['passenger_cid'] = $company_id;
				$result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$update_array),array('upsert'=>false));
			} else {
				$result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$update_array),array('upsert'=>false));
			}
		} else {
			$result = $this->mongo_db->updateOne(MDB_PEOPLE,$match,array('$set'=>$update_array),array('upsert'=>false));
		}
		return (empty($result->getwriteErrors())) ? 1 : 0;
	}


	
	//get Driver Earnings for a particular day
	public function getTodayDriverEarnings($driver_id,$start_time,$end_time)
	{
		$match_query['driver_id'] = (int)$driver_id;
		$match_query['travel_status'] = 1;
		$match_query['createdate'] = array('$gte' => Commonfunction::MongoDate(strtotime($start_time)),'$lte'=>Commonfunction::MongoDate(strtotime($end_time)));
		$args = array(
			array('$lookup' => array(
				'from' => MDB_TRANSACTION,
				'localField' => '_id',
				'foreignField' => 'passengers_log_id',
				'as' => 'trans',
			)),
			array('$unwind' => array('path' => '$trans', 'preserveNullAndEmptyArrays' => true )),
			array('$match' => $match_query ),
			array('$group' =>array('_id' => null,
					'total_amount' => array('$sum'=>'$trans.fare'),
					'total_trips' => array('$sum'=>1)
				)
			),
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		if(isset($result['result']) && count($result['result']) > 0){
			$array_data['total_amount'] = isset($result['result'][0]['total_amount'])?$result['result'][0]['total_amount']:0;
			$array_data['total_trips'] = isset($result['result'][0]['total_trips'])?$result['result'][0]['total_trips']:0;
			$final_array['result'][] = $array_data;
		}else{
			$array_data['total_amount'] = 0;
			$array_data['total_trips'] = 0;
			$final_array['result'][] = $array_data;		
		}
		return $final_array['result'];
		
	}
	
	//get Driver Earnings for a particular week
	public function getWeeklyWiseEarnings($driver_id,$current_time)
	{	
		$previous_month = date('Y-m-01 00:00:00', strtotime(date('Y-m-d',strtotime($current_time))." -1 month"));
		$current_date = date('Y-m-d 23:59:59',strtotime($current_time));
		$match_query['driver_id'] = (int)$driver_id;
		$match_query['travel_status'] = 1;
		$match_query['createdate'] = array('$gte' => Commonfunction::MongoDate(strtotime($previous_month)),'$lte'=>Commonfunction::MongoDate(strtotime($current_date)));
		$args = array(
			array('$lookup' => array(
				'from' => MDB_TRANSACTION,
				'localField' => '_id',
				'foreignField' => 'passengers_log_id',
				'as' => 'trans',
			)),
			array('$unwind' => array('path' => '$trans', 'preserveNullAndEmptyArrays' => true )),
			array('$match' => $match_query ),
			array('$project' =>array('total_amount' => array('$cond' => array('if'=> array('$gte' => array('$trans.fare', 0 ) ), 'then' =>  '$trans.fare', 'else' => 0 )),
					//'created_date' => '$createdate',
					'created_date' => array('$dateToString' => array('format' => '%Y-%m-%d','date' => '$createdate')),
				)
			),
			array('$group' =>array('_id' => array('created_date' => '$created_date'),
					//'created_date' => '$created_date',
					'total_amount' => array('$sum'=>'$total_amount'),					
					
				)
			)
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		//print_r($result);
		$result = (!empty($result['result']) ? $result['result']: array());
		$data = $date_array = $final_array = array();
		$current_week_amount = 0;
		if(count($result) > 0){
			foreach($result as $val){
				$val1 = (isset($val['_id']))?$val['_id']:array();
				if(!empty($val1)){
					$array['total_amount'] = $val['total_amount'];
					//$date = Commonfunction::convertphpdate('Y-m-d',$val['created_date']);
					$date = $val1['created_date'];
					$array['day'] = date('d D', strtotime($date));
					$data[$date] = $array;
				}
			}
		}
		//print_r($data);
		$days_count_via_month = cal_days_in_month(CAL_GREGORIAN, date('m',strtotime("-30 days")),date('Y'));
		$month_array = array(date('m', strtotime('last month'))=>$days_count_via_month,date('m')=>date('j'));
			$current_date = date('d');
			foreach($month_array as $month => $day_count){
				for($i=1;$i<=$day_count;$i++){
					$day = (strlen($i) > 1)?$i:'0'.$i;
					$date = date('Y').'-'.$month.'-'.$day;
					$month_text = date('M', strtotime($date));
					if (array_key_exists($date, $data)) {
						$arr['trip_amount'][]=$data[$date]['total_amount'];
						$arr['day_list'][]=$data[$date]['day'];
					}else{
						$arr['trip_amount'][]=0;
						$arr['day_list'][]=date('d D', strtotime($date));
					}
					
					if($i<=7){
						$date_array[]= $day;
						if($i==7 || ($i==$current_date && $month == date('m'))){
							$arr['date_text'] = $month_text." 01-07";
							$arr['this_week_earnings'] = array_sum($arr['trip_amount']);
							$final_array[] = $arr;
							$arr=$date_array=array();
						}
					}
					elseif($i>=8 && $i<=14){
						$date_array[]= $day;
						if($i==14 || ($i==$current_date && $month == date('m'))){
							$arr['date_text'] = $month_text." 08-14";
							$arr['this_week_earnings'] = array_sum($arr['trip_amount']);
							$final_array[] = $arr;
							$arr=$date_array=array();
						}
					}
					elseif($i>=15 && $i<=21){
						$date_array[]= $day;
						if($i==21 || ($i==$current_date && $month == date('m'))){
							$arr['date_text'] = $month_text." 15-21";
							$arr['this_week_earnings'] = array_sum($arr['trip_amount']);
							$final_array[] = $arr;
							$arr=$date_array=array();
						}
					}
					elseif($i>=22 && $i<=28){
						$date_array[]= $day;
						if($i==28 || ($i==$current_date && $month == date('m'))){
							$arr['date_text'] = $month_text." 22-28";
							$arr['this_week_earnings'] = array_sum($arr['trip_amount']);
							$final_array[] = $arr;
							$arr=$date_array=array();
						}
					}
					elseif($i>=29 && $i<=$days_count_via_month){
						$date_array[]= $day;
						if($i==$days_count_via_month || ($i==$current_date && $month == date('m'))){
							$arr['date_text'] = $month_text." 29-".$days_count_via_month;
							$arr['this_week_earnings'] = array_sum($arr['trip_amount']);
							$final_array[] = $arr;
							$arr=$date_array=array();
						}
					}	
				}				
			}
			//exit;
		return array_reverse($final_array);
	}
	
	# cancel setting
	public function get_cancel_setting($company_id)
	{
		$brand_type= 'M';
		$comp_result = $this->mongo_db->findOne(MDB_COMPANY,array('_id' => (int)$company_id),array('companyinfo.cancellation_fare','companyinfo.skip_credit_card'));
		if(count($comp_result) > 0) {
			
			$brand_type = isset($comp_result['companyinfo']["company_brand_type"]) ? $comp_result['companyinfo']["company_brand_type"] : 'M';
		}

		if(FARE_SETTINGS == 1 || $brand_type == 'S') {	
			
			$result = $this->mongo_db->findOne(MDB_SITEINFO, array(),array('cancellation_fare_setting','skip_credit_card'));
			if(count($result) > 0) {
				if($result["cancellation_fare_setting"] == 1) {
					if($result["skip_credit_card"] == 0) {
						return 0;
					} else {
						return 1;
					}
				} else {
					return 0;
				}
			}
		} else {
			if(count($comp_result) > 0) {
				
			$cancellation_fare = isset($comp_result['companyinfo']["cancellation_fare"]) ? $comp_result['companyinfo']["cancellation_fare"]:0;
			$skip_credit_card = isset($comp_result['companyinfo']["skip_credit_card"]) ? $comp_result['companyinfo']["skip_credit_card"]:0;
				if($cancellation_fare == 1) {
					if($skip_credit_card == 1) {
						return 0;
					} else {
						return 1;
					}
				} else {
					return 0;
				}
			}
		}
	}
/** 
	 * Update Secondary percent and amount to Primary passenger
	 * return
	 **/

	public function updateSecondaryPercentAmtPrimary($trip_id,$secondary_passenger_id,$primary_passenger_id)
	{		
		$args = array(array('$unwind' => array('path' => '$split_details', 'preserveNullAndEmptyArrays' => true )),
						array('$match' => array('_id' => (int)$trip_id, 
											'split_details.friends_p_id' => (int)$secondary_passenger_id)),
						array('$project' => array('fare_percentage'=> '$split_details.fare_percentage',
											'appx_amount' =>'$split_details.appx_amount',
											'friends_p_id' =>'$split_details.friends_p_id')
						),
					);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		$result = isset($res['result'])?$res['result']:array();
		if(count($result) > 0) {
			$percent = $result[0]["fare_percentage"];
			$appx_amount = $result[0]["appx_amount"];
			$args = array(array('$match' => array('split_details.friends_p_id' => (int)$primary_passenger_id)),
							array('$project' => array('split_id'=> '$split_details.split_id',
													'friends_p_id'=> '$split_details.friends_p_id'))
						);
			$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
			$result = isset($res['result'])?$res['result']:array();
			$val = array();
			foreach($result as $k => $v ){
				$j=0;
				foreach($v['friends_p_id'] as $value){
					if($value == $primary_passenger_id){
						$val["split_details.$j.fare_percentage"] = $percent;
						$val["split_details.$j.appx_amount"] = $appx_amount;
					}
					$j++;
				}
				if($j==count($v['friends_p_id'])){
					$match['_id'] = (int)$v['_id'];
					$match['split_details.friends_p_id'] = (int)$primary_passenger_id;
					$update = $this->mongo_db->updateMany(MDB_PASSENGERS_LOGS,$match,array('$inc'=>$val));
					$val = array();
				}
			}
		}
		
		# deduct percent & amount from secondary passenger
		$args = array(array('$unwind' => array('path' => '$split_details', 'preserveNullAndEmptyArrays' => true )),
					array('$match' => array('_id' => (int)$trip_id)),
					array('$project' => array('split_id'=> '$split_details.split_id',
									  'friends_p_id' =>'$split_details.friends_p_id',
									  'approve_status' =>'$split_details.approve_status'))
				);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		$result = isset($res['result'])?$res['result']:array();
		$i =0;$val = array();
		foreach($result as $k => $v ){
			if($v['friends_p_id'] == $secondary_passenger_id && $v['approve_status'] != 'A'){
				$val["split_details.$i.fare_percentage"] = 0;
				$val["split_details.$i.appx_amount"] = 0;
			}
			$i++;
		}
		$match['_id'] = (int)$trip_id;		
		$match['split_details.friends_p_id'] = (int)$secondary_passenger_id;		
		//$match['split_details.approve_status'] = 'I';		
		if(!empty($val)){			
			$update = $this->mongo_db->updateMany(MDB_PASSENGERS_LOGS,$match,array('$set'=>$val));
		}		
		
		return true;
	}
	
	public function updatePassengerPercentAmt($passenger_log_id,$passengers_id,$type)
	{
		$args = array(array('$unwind' => array('path' => '$split_details', 'preserveNullAndEmptyArrays' => true )),
			array('$match' => array('_id' => (int)$passenger_log_id)),
			array('$project' => array('split_id'=> '$split_details.split_id',
									  'friends_p_id' =>'$split_details.friends_p_id'))
		);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		$result = isset($res['result'])?$res['result']:array();
		$i =0;$val = array();
		foreach($result as $k => $v ){
			if($v['friends_p_id'] != $passengers_id){
				$val["split_details.$i.fare_percentage"] = 0;
				$val["split_details.$i.appx_amount"] = 0;
			}
			$i++;
		}
		$match['_id'] = (int)$passenger_log_id;		
		$match['split_details.approve_status'] = ($type == 1) ? 'I' : 'A' ;
		
		if(!empty($val)){			
			$update = $this->mongo_db->updateMany(MDB_PASSENGERS_LOGS,$match,array('$set'=>$val));
		}
		return true;
	}
	
	# Set split fare
	public function set_split_fare($passenger_id, $type)
	{
		$match = array('_id'=>(int)$passenger_id);
		$args = array(array('$match' => $match), array('$unwind'=> '$creditcard_details'));
		$res = $this->mongo_db->Aggregate(MDB_PASSENGERS, $args);
		if(!empty($res['result'])){
			$update = $this->mongo_db->updateOne(MDB_PASSENGERS,$match, array('$set' => array('split_fare'=> (int)$type)),array('upsert' =>false));
			return 1;
		}		
		return 0;
	}

	
	/** function to get trip cancel alert to driver ( either from passenger or dispatcher ) **/
	public function getTripCancelAlert($driver_id="", $trip_id, $current_time)
	{
		
		$result = $temp_arr = array();		
		$current_date = explode(' ',$current_time);
		$start_time = $current_date[0]." 00:00:01";
		$end_time = $current_date[0]." 23:59:59";
		$match = array('_id' => (int)$trip_id,
						'driver_id' => (int)$driver_id,
						'travel_status' => array('$in' => array(4,8)),
						'notification_status' => array('$ne' =>5),
						'createdate'=>array('$gte'=> Commonfunction::MongoDate(strtotime($start_time)),
										'$lte'=> Commonfunction::MongoDate(strtotime($end_time))),
					);
		$project = array('_id','travel_status','notification_status');
		$res = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,$match,$project);
		if(!empty($res)){
			$temp_arr['trip_id'] = $res['_id'];
			$temp_arr['trip_status'] = isset($res['travel_status'])?$res['travel_status']:0;
			$temp_arr['notification_status'] = isset($res['notification_status'])?$res['notification_status']:0;
			$result[] = $temp_arr;
		}
		return $result;
	}
	
	/** function to update favourite driver id **/
	public function chkDriverAdded($passengerId, $driverId)
	{
		$result = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$passengerId),array('favourite_drivers'));
		$return = 0;
		if(!empty($result)){
			//$fav_drivers = explode(',',$result['favourite_drivers']);
			$fav_drivers = isset($result['favourite_drivers']) ? (array)$result['favourite_drivers'] : array();
			if(!empty($fav_drivers)){
				$return = in_array($driverId,$fav_drivers) ? 1:0;
			}
		}	
		return $return;	
	}
	
	/** function to update favourite driver id **/
	public function saveFavouriteDriver($passengerId, $driverId)
	{
		$result = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$passengerId),array('favourite_drivers'));
		$drivers = array((int)$driverId);
		if(!empty($result)){
			
			//$fav_drivers = explode(',',$result['favourite_drivers']);
			$fav_drivers = isset($result['favourite_drivers']) ? (array)$result['favourite_drivers'] : array();
			if(!empty($fav_drivers)){
				
				$drivers = (array)$result['favourite_drivers'];
				if(!in_array($driverId,$fav_drivers)){
					//$drivers = $drivers.','.$driverId;				
					array_push($drivers, (int)$driverId);
				}				
			}				
		}
		$res = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passengerId),array('$set' => array('favourite_drivers' => $drivers)),array('upsert' => false));	
		return 1;
	}

	public function favourite_driver_list($passenger_id)
	{
		$result = $temp_arr = array();
		$args = array(
					array('$unwind' => '$favourite_drivers'),
					array('$lookup' => array('from' => 'people','localField' => 'favourite_drivers',
											'foreignField' => '_id','as' => 'people')),
					array('$unwind' =>  array( 'path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$lookup' => array('from' => 'taxi_driver_mapping','localField' => 'people._id',
											'foreignField' => 'mapping_driverid','as' => 'tdm')),
					array('$unwind' =>  array( 'path' =>  '$tdm', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$lookup' => array('from' => 'taxi','localField' => 'tdm.mapping_taxiid',
											'foreignField' => '_id','as' => 'taxi')),
					array('$unwind' =>  array( 'path' =>  '$taxi', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$match' => array('_id' => (int)$passenger_id)),
					array('$project' => array('taxi_no' => '$taxi.taxi_no',
										   'id' => '$people._id',
										   'name' => '$people.name',
										   'email' => '$people.email',
										   'phone' => '$people.phone',
										   'profile_picture' => '$people.profile_picture')),
					array('$group' => array('_id' => '$_id',
											'id' =>  array( '$push' =>  '$id' ),
											'taxi_no' =>  array( '$push' =>  '$taxi_no' ),
											'name' =>  array( '$push' =>  '$name' ),
											'email' =>  array( '$push' =>  '$email' ),
											'phone' =>  array( '$push' =>  '$phone' ),
											'profile_picture' =>  array( '$push' =>  '$profile_picture'),
					))
				);
		$res = $this->mongo_db->Aggregate(MDB_PASSENGERS,$args);
		//print_r($res);exit;
		if(!empty($res['result'])){
			$result_arr = $res['result'][0];
			$count = count($result_arr['taxi_no']);
			for($i=0;$i<$count;$i++){
				
				$driverid = isset($result_arr['id'][$i]) ? $result_arr['id'][$i]:'';
				if(!in_array($driverid,$temp_arr)){				
					$temp_arr['id'] = $driverid;
					$temp_arr['taxi_no'] = isset($result_arr['taxi_no'][$i]) ? $result_arr['taxi_no'][$i]:'';
					$temp_arr['name'] = isset($result_arr['name'][$i]) ? $result_arr['name'][$i]:'';
					$temp_arr['email'] = isset($result_arr['email'][$i]) ? $result_arr['email'][$i]:'';
					$temp_arr['phone'] = isset($result_arr['phone'][$i]) ? $result_arr['phone'][$i]:'';
					$temp_arr['profile_picture'] = isset($result_arr['profile_picture'][$i]) ? $result_arr['profile_picture'][$i]:'';
					$result[] = $temp_arr;
				}
			}
		} 	
		//print_r($result);exit;
		return $result;	
	}	

	public function unfavourite_driver($passenger_id, $driver_id)
	{
		$match = array('_id' => (int)$passenger_id);
		$options=[
				'projection'=>[
				   'favourite_drivers'=>1,                               
					]
				];
		$result = $this->mongo_db->find(MDB_PASSENGERS,$match,$options);
		$passenger_result = $result;
		$passenger_result = commonfunction::change_key($passenger_result);
		if(count($passenger_result) > 0) {
			 
			if(isset($passenger_result[0]["favourite_drivers"]) && !empty($passenger_result[0]["favourite_drivers"])) {
				//$driver_array = explode(",",$passenger_result[0]["favourite_drivers"]);
				$driver_array = (array)$passenger_result[0]["favourite_drivers"];
				if(count($driver_array) > 0) {
					if(in_array($driver_id,$driver_array)) {
						$list = array_diff($driver_array, array($driver_id));
						//$driver_list = implode(",",$list);
						$driver_list = $list;
						$update_array = ($driver_list != "") ? array("favourite_drivers" => $driver_list) : array("favourite_drivers" => array());
						$match = array('_id' => (int)$passenger_id);
						//print_r($list); exit;
						$result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$update_array),array('upsert' => false));

						return 1;
					} else {
						return -1;
					}
				} else {
					return -1;
				}
			} else {
				return -1;
			}
		} else {
			return 0;
		}
	}
	
	public function check_company_domain($data)
	{
		$company_domain = (isset($data['company_domain']))?strtolower(trim($data['company_domain'])):"";
		$result = array();
		$res = $this->mongo_db->findOne(MDB_COMPANY_DOMAIN, array('company_domain'=>$company_domain), array('_id','DBtype','live_domain','mobile_api_key'));
		if(!empty($res)){
			$result[0]['domain_id'] = $res['_id'];
			$result[0]['DBtype'] = isset($res['DBtype'])?$res['DBtype']:2;
                        $result[0]['mobile_api_key']=isset($res['mobile_api_key'])?$res['mobile_api_key']:'';
                        $result[0]['live_domain']=isset($res['live_domain'])?$res['live_domain']:'';
                        /*if($mobile_api_key=='')
                        {
                            $update_array=['api_key'=>$api_key];
                            $match['_id'] = isset($data['domain_id'])?$data['domain_id']:1;
                            $this->mongo_db->updateOne(MDB_COMPANY_DOMAIN,$match,array('$set'=>$update_array),array('upsert'=>false));
                        }*/
		}
		return $result;
	}
	
	public function update_used_status($data){
		$update_array = array("used_status" => 1,"used_date" => Commonfunction::MongoDate(strtotime($this->currentdate)));
		$match['_id'] = $data['domain_id'];
		$result = $this->mongo_db->updateOne(MDB_COMPANY_DOMAIN,$match,array('$set'=>$update_array),array('upsert'=>false));
		//return (isset($result['err']) ? array() : $result);
		return (empty($result->getwriteErrors())) ? 1 : 0;
	}
	
	public function get_company_expiry_date($auth_key)
	{
		$result = array();
		$res = $this->mongo_db->findOne(MDB_SITEINFO, array('_id'=>1), array('expiry_date'));
		if(!empty($res)){
			$result[0]['expiry_date'] = $res['expiry_date']->sec;
		}
		return $result;
	}

	/** get company, model and city details for a trip **/
	public function getTripCompanyModelDets($tripId)
	{
		$result = array();
		$args = array(
					array('$match' => array('_id' => (int)$tripId)),
					array('$lookup' => array('from' => MDB_COMPANY,
											'localField' => 'company_id',
											'foreignField' => '_id',
											'as' => 'company')),
					array('$unwind' => '$company'),
					array('$project' => array('company_id' => '$company_id',
												'search_city' => '$search_city',
												'taxi_modelid' => '$taxi_modelid',
												'booking_from' => '$booking_from',
												'actual_pickup_time' => '$actual_pickup_time',
												'brand_type' => '$company.companyinfo.company_brand_type',
												'fare_calculation_type' => '$company.companyinfo.fare_calculation_type'))
				);
		
		$res1 = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS, $args);
		if(!empty($res1['result'])){
			
			$res = $res1['result'][0];
			$result['company_id'] = isset($res['company_id'])?$res['company_id']:'';
			$result['search_city'] = isset($res['search_city'])?$res['search_city']:'';
			$result['taxi_modelid'] = isset($res['taxi_modelid'])?$res['taxi_modelid']:'';
			$result['booking_from'] = isset($res['booking_from'])?$res['booking_from']:'';
			$result['brand_type'] = isset($res['brand_type'])?$res['brand_type']:'M';
			$result['fare_calculation_type'] = isset($res['fare_calculation_type'])?$res['fare_calculation_type']:FARE_CALCULATION_TYPE;
			$result['actual_pickup_time'] = isset($res['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['actual_pickup_time']) : '';
		}
		return $result;
	}
	
	/** get Help contents to show in passnger app **/
	public function getHelpContents()
	{
		$result = array();
		$options=[
				'projection'=>[
					'_id'=>1,                               
					'help_content'=>1                               
					]
				];
		$res = $this->mongo_db->Find(MDB_HELP,array(),$options);
		$res = $res;
		$res = commonfunction::change_key($res);
		if(!empty($res)){
			
			$result = array_map(
					function($res) {
						return array(
							'help_id' => $res['_id'],
							'help_content' => $res['help_content']
						);
				}, $res);
		}
		return $result;
	}	
	
	public function get_companydet($company_api_key = ''){
		
		$result = $temp_arr = array();
		$match = array('companyinfo.company_api_key' => $company_api_key,'companydetails.company_status'=>'A');
		$apikey_query = $this->mongo_db->findOne(MDB_COMPANY, $match, array('_id','companyinfo.company_currency','companyinfo.company_app_description'));
		
		if(!empty($apikey_query)){
			$temp_arr['company_cid'] = $apikey_query['_id'];
			$temp_arr['company_currency'] = isset($apikey_query['companyinfo']['company_currency'])?$apikey_query['companyinfo']['company_currency']:'';
			$temp_arr['company_app_description'] = isset($apikey_query['companyinfo']['company_app_description'])?$apikey_query['companyinfo']['company_app_description']:'';
			$result[] = $temp_arr;
		}
		return $result;
	}
	
	public function checkTripStatus($tripId)
	{
		$res = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$tripId),array('travel_status'));
		$travel_status = isset($res['travel_status']) ? $res['travel_status'] : '';
		return $travel_status;		
	}

	public function updateTripComment($trip_id,$help_id,$help_comment)
	{
		$commentedDate = date('Y-m-d H:i:s');
		$update_array = array("help_id" => (int)$help_id,
							"help_comment" => urldecode($help_comment),
							"help_comment_date" => Commonfunction::MongoDate(strtotime($commentedDate)));		
		$update = $this->mongo_db->updateOne(MDB_TRANSACTION,array('passengers_log_id'=>(int)$trip_id),array('$set' => $update_array),array('upsert'=>false));
		return 1;
	}
	
	public function getDriverDeviceToken($driver_id)
	{		
		$match_array = array('_id'=> (int)$driver_id);
		$options=[
			'projection'=>[
				'device_type'=>1,
				'device_token'=>1                     
			]
		];
		$response = $this->mongo_db->find(MDB_PEOPLE,$match_array,$options);
		$result   = $response;
		$data = array();
		if(count($result) > 0){
			foreach($result as $val){
				$arr = $val;				
				$data[] = $arr;
			}
		}
		return $data;	
	}
	
	public function update_driverlimit($trip_id,$actual_limit,$driver_limit=0)
	{		
		$update_array = array("actual_limit" => $actual_limit);						
		$update = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY,array('_id'=>(int)$trip_id),array('$set' => $update_array),array('upsert'=>false));
		return 1;
	}
	
	public function update_tripdriverlimit($trip_id)
	{		
		$match = array('_id'=>(int)$trip_id);
		$project = array('_id',
						 'total_drivers');
		$result = $this->mongo_db->findOne(MDB_REQUEST_HISTORY,$match,$project);
		if(!empty($result)){			
			$total_drivers = $result['total_drivers'];
			$update_array = array(
									"available_drivers" => '',
									"rejected_timeout_drivers" => $total_drivers,
									'status'=>0
								);						
			$update = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY,array('_id'=>(int)$trip_id),array('$set' => $update_array),array('upsert'=>false));
		}
		return 1;
	}
	
	public function check_passengerlogin($passenger_id){
	
		$match = array('_id' =>(int)$passenger_id, 'user_status' => 'A');
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match);
		return $result;
	}	
	
	public function delete_rejected_trips($passenger_id, $company_all_currenttimestamp)
	{
		$datetime     = explode(' ', $company_all_currenttimestamp);
		$currentdate  = $datetime[0] . ' 00:00:01';
		$match = array('passengers_id'=>(int)$passenger_id,'createdate'=>array('$gte'=> Commonfunction::MongoDate(strtotime($currentdate))));
		$result = $this->mongo_db->deleteMany(MDB_REJECTION_HISTORY,$match);
		return 1;
	}
	
	public function check_driver_status_free($driver_id = "")
	{
		$result = $this->mongo_db->findOne(MDB_DRIVER_INFO,array('_id'=>(int)$driver_id),array('status'));
		return (isset($result)) ? $result['status'] : ''; 
	}
	
	# Taximobility-6.2.3 functionalities
	public function get_favouritepopularplaces($passenger_id,$type,$lat='', $long='')
	{
		$result = $temp_arr = array();
		$fav_labelname = array('1'=>'home','2'=>'office','3'=>'airport');
		if($type == 1){
			$match = array('passenger_id' => (int)$passenger_id,'status' => 'A');
			$project = array(
							 'p_location_name' => '$p_favourite_place',
							 'd_location_name' => '$d_favourite_place',
							 'latitude' => '$d_fav_latitude',
							 'longtitute' => '$d_fav_longtitute',
							 'p_latitude' => '$p_fav_latitude',
							 'p_longtitute' => '$p_fav_longtitute',
							 'loction_type' => '$fav_loction_type');		
			$args = array(array('$match' => $match),
						  array('$project' => $project),
						  array('$sort' => array('_id' => 1)),
						  array('$limit' => 5)
						);
			$res = $this->mongo_db->aggregate(MDB_PASSENGERS_FAVOURITES,$args);
			if(!empty($res['result'])){
				foreach($res['result'] as $r){
					$temp_arr['location_name'] = (isset($r['d_location_name']) && ($r['d_location_name'] !='')) ? $r['d_location_name']: $r['p_location_name'];
					$temp_arr['latitude'] = ($r['latitude'] != '') ? $r['latitude']: $r['p_latitude'];
					$temp_arr['longtitute'] = ($r['longtitute'] != '') ? $r['longtitute']: $r['p_longtitute'];
					$temp_arr['label_name'] = $r['loction_type'];
					$imagename = 'OTH';
					if(array_key_exists($r['loction_type'],$fav_labelname)){
						$temp_arr['label_name'] = isset($r['loction_type']) ? ucfirst($fav_labelname[$r['loction_type']]): '';
						$imagename = strtoupper($temp_arr['label_name']);
						$imagename = ($imagename == 'AIRPORT') ? 'AIR' : $imagename;
					}
					
					# images
					$android_image_path = URL_BASE.MOBILE_ANDROID_IMAGES_FILES."static_image/favourite_popular/";
					$ios_image_path = URL_BASE.MOBILE_iOS_IMAGES_FILES."static_image/favourite_popular/";					
					$temp_arr['android_icon'] = $android_image_path.$imagename.'.png';
					$temp_arr['ios_icon'] = $ios_image_path.$imagename.'.png';
					$result[] = $temp_arr;
				}
			}
		}else{		
			$args =  array('loc' => array('$near'  => 
								  array(
									'$geometry' => array('type'=> 'Point','coordinates'=> array((double)$long,(double)$lat)),
									'$maxDistance' => 30000
								  )
							));
			$options=array('projection'=>array('city_name' =>1,'location_name' => 1,
												'label_name' => 1,'loc.coordinates'=>1,'location_icon'=>1),
												'limit' =>5);
			$res = $this->mongo_db->Find(MDB_POPULAR_PLACES, $args, $options);			
			if(!empty($res)){
				foreach($res as $r){					
					$temp_arr['location_name'] = isset($r['location_name']) ? $r['location_name']: '';
					$temp_arr['label_name'] = isset($r['label_name']) ? $r['label_name']: '';
					$temp_arr['location_icon'] = isset($r['location_icon']) ? $r['location_icon']: '';
					$temp_arr['latitude'] = isset($r['loc']['coordinates'][1]) ? $r['loc']['coordinates'][1]: '';
					$temp_arr['longtitute'] = isset($r['loc']['coordinates'][0]) ? $r['loc']['coordinates'][0]: '';		
					# images
					$android_image_path = URL_BASE.MOBILE_ANDROID_IMAGES_FILES."static_image/favourite_popular/";
					$ios_image_path = URL_BASE.MOBILE_iOS_IMAGES_FILES."static_image/favourite_popular/";
					$imagename = $temp_arr['location_icon'];
					$temp_arr['android_icon'] = $android_image_path.$imagename.'.png';
					$temp_arr['ios_icon'] = $ios_image_path.$imagename.'.png';
					$result[] = $temp_arr;
				}				
			}		
		}
		return $result;		
	}
	
	//Version 6.2.3 update - Void transaction  for cancel & reject trips
	public function voidTransaction_for_trip($trip_id)
	{
		$project=['pre_transaction_id','pre_transaction_amount'];
		$match=['_id'=>(int)$trip_id];
		$result = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,$match,$project);
		$payment_status = '';                    
		$paymentresponse =[];
		$code=0;
		
		if(!empty($result)){       
				$preTransactId= isset($result['pre_transaction_id'])?$result['pre_transaction_id']:'';
				$preTransactAmount=isset($result['pre_transaction_amount'])?$result['pre_transaction_amount']:0;
				if($preTransactId!='' && $preTransactAmount!=0){
				// Payment gateway void transaction
				if (class_exists('Paymentgateway')) {
					$paymentresponse = Paymentgateway::payment_gateway_connect('void',$preTransactId);
					$payment_status=$paymentresponse['payment_status'];
				   
				} else {
					trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
				}
				// Payment gateway success response
				if($payment_status==1){                   
			
					$match = ['_id' => (int)$trip_id];
					$val=['void_status'=>1];
					$result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,$match,array('$set'=>$val),array('upsert' => false));
			
				}
			}
		}
		return $payment_status;
	}
	
	public function get_driver_currentshift($id){
		
		$result = array();
		$options=[
					'projection'=>[
						'_id'=>1,                               
					],
					'sort' => ['_id' => -1],
					'limit' => 1
				];
		
		$args = array(
					array('$match' => array('driver_id' => (int)$id)),
					array(
						'$lookup' => array(
							'from' => MDB_DRIVER_INFO,
							'localField' => 'driver_id',
							'foreignField'=>'_id',
							'as'=>'driver_info'
						)
					),
					array('$unwind' =>  array( 'path' =>  '$company', 'preserveNullAndEmptyArrays' =>  true)),
					array('$project' => array('shift_id' => '$shift_id',
											'driver_status' => '$driver_info.status')),
					array('$sort' => array('_id' => -1)),
					array('$limit' => 1),
					
				);
		$res = $this->mongo_db->Aggregate(MDB_SHIFT_HISTORY,$args);
		if(!empty($res['result'])){
			$r = $res['result'];
			$result['shift_id'] = isset($r[0]['_id']) ? $r[0]['_id']:'';
			$result['driver_status'] = isset($r[0]['driver_status'][0]) ? $r[0]['driver_status'][0]:'';
		}
		return $result;
	}
	
	public function check_driver_device($driverId, $device_type, $deviceId){
		
		$match = array();
		$match['_id'] = (int)$driverId;
		//~ $match['device_id'] = $deviceId;
		$options=['_id','device_id'];
		$res = $this->mongo_db->findOne(MDB_PEOPLE,$match,$options);
		$result = 0;
		if(isset($res['device_id']) && $res['device_id'] != ''){
			$result = ($res['device_id'] == $deviceId) ? 1 : 2;
		}
		return $result;
	}
	
	public function update_favourite_image($favourite_id, $image_name)
    {
		$match = array('_id' => (int)$favourite_id);
		$options=['fav_image' =>1];
		$res = $this->mongo_db->findOne(MDB_PASSENGERS_FAVOURITES,$match,$options);
		if(!empty($res) && isset($res['fav_image'])){
			$image_link = DOCROOT.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $passenger_id .'/'.$res['fav_image'];
			if(file_exists($image_link)){
				unlink($image_link);
			}
		}
		$update_array = array(
            'fav_image' => $image_name
        );
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS_FAVOURITES,array('_id'=>(int)$favourite_id),array('$set'=>$update_array),array('upsert'=>false));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }	
    
    public function get_mobile_api_key(){
        $options=['mobile_api_key'];
                    
        $result = $this->mongo_db->findOne(MDB_COMPANY_DOMAIN,['_id'=>1],$options);        
        return $result;
    }
    
    public function splitted_pushnotification($pushMessage,$trip_id,$passenger_id){
        $result = $temp_arr = array();
		$args = array(
					array('$match' => array('_id' => (int)$trip_id)),
					array('$unwind' => '$split_details'),
					array('$lookup' => array('from' => MDB_PASSENGERS,
							'localField' => 'split_details.friends_p_id',
							'foreignField' => '_id',
							'as' => 'passenger')),
					array('$unwind' => '$passenger'),
					array('$project' => array(
											'_id' => 0,
											'approve_status' => '$split_details.approve_status',
											'device_token' => '$passenger.device_token',
											'device_type' => '$passenger.device_type',
											'passenger_id' => '$passenger._id',
						))
				);
		$res = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			foreach($res['result'] as $r){

				$approve_status = isset($r['approve_status']) ? $r['approve_status']: '';
				$passengerId = isset($r['passenger_id']) ? $r['passenger_id']: '';
				if($approve_status == 'A' && $passengerId != $passenger_id){
					$device_token = isset($r['device_token']) ? $r['device_token']: '';
					$device_type = isset($r['device_type']) ? $r['device_type']: '';
					$pushMessage['badge'] = 1;
					//~ echo $passengerId.'='.$passenger_id.'='.$device_token.'='.$device_type.'='.CUSTOMER_ANDROID_KEY;exit;
					$this->send_pushnotification($device_token,$device_type,$pushMessage,CUSTOMER_ANDROID_KEY);
				}
			}
		}
        return 1;
    }
    
   
}
