<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/****************************************************************
* Contains Common model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Model_TaximobilityCommonmodel extends Model
{
    public function __construct()
    {
        $this->session     = Session::instance();
        $this->currentdate = Commonfunction::getCurrentTimeStamp();
		
		//MongoDB Instance
        $this->mongo_db        = MangoDB::instance('default');
        $push = array('message'=>'pushmessage',"status" => 15,"display" => 1 );
		
    }
    public function insert( $table, $arr )
    {
		if(count($arr) > 0 && !isset($arr['_id'])){
			$id = Commonfunction::get_auto_id($table);
			$arr['_id'] = $id;
		}
		$result = $this->mongo_db->insertOne($table, $arr );
		return (empty($result->getwriteErrors()))?1:0;
    }
    public function update( $table, $arr, $cond1, $cond2 )
    {
		if(is_numeric($cond2)){
			$cond2 = (int)$cond2;
		}
        $result = $this->mongo_db->updateOne($table,array($cond1 => $cond2),array('$set'=> $arr),array('upsert'=>false));
        return (empty($result->getwriteErrors()))? 1: 0;
    }
	public function update_array( $table, $arr, $cond1, $cond2,$write )
    {
        $result = $this->mongo_db->updateOne($table,array($cond1 => (int)$cond2),array('$set'=> $arr),$write);
        return (empty($result->getwriteErrors()))? 1: $result->getwriteErrors();
    }
    public function delete( $table, $cond1, $cond2 )
    {
        /*$result = DB::delete( $table )->where( $cond1, '=', $cond2 )->execute();
        return $result;*/
        $result = $this->mongo_db->deleteOne($table,array($cond1 => (int)$cond2));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    public function select_site_settings($field_name,$tablename)
    {
		if(is_array($field_name)){
			//MongoDB
			
			$result = $this->mongo_db->findOne($tablename,array(),$field_name);
			if(count($field_name) > 1){
				for($i=0;$i<count($field_name); $i++){
					$index = $field_name[$i];
					$result[$index] = $result[$index];
				}	
			}else{
				$result = $result[$field_name[0]];
			}
		}elseif(empty($field_name)){
			
			//MongoDB
			$result = $this->mongo_db->findOne($tablename,array());
			$result = isset($result)?$result:"";
		}else{
			
			//MongoDB
			$result = $this->mongo_db->findOne($tablename,array(),array($field_name));
			$result = isset($result[$field_name])?$result[$field_name]:"";
		}
		//print_r($result);  exit; 
		return (!empty($result))?$result:array();
    }
	
	//By Suresh
    public function dynamic_findone($tablename,$field_name,$condition=array())
    {		
		if(is_array($field_name)){
			//MongoDB
			$result = $this->mongo_db->findOne($tablename,$condition,$field_name);
			if(count($field_name) > 1){
				for($i=0;$i<count($field_name); $i++){
					$index = $field_name[$i];
					$result[$index] = $result[$index];
				}	
			}else{
				$result = $result[$field_name[0]];
			}
		}else{
			//MongoDB
			$result = $this->mongo_db->findOne($tablename,$condition,array($field_name));
			$result = isset($result[$field_name])?$result[$field_name]:"";
		}
		return (!empty($result))?$result:'';
    }
	
    public function get_meta_settings($field="",$action){
		
		//$result = $this->mongo_db->findOne(MDB_CMS,array('menu_link'=>$action),array('meta_keyword','meta_description','meta_title'));	
		$project = explode(',',$field);
		$match = array('menu_link'=>$action, 'status_post' => 'P');
		$result = $this->mongo_db->findOne(MDB_CMS,$match,$project);
		if(!empty($result)){
			return $result;
		}else{
			return;
		}
    }
   
    function get_randorm_values( $lat, $long, $max )
    {
        $longitude = (float) $long;
        $latitude  = (float) $lat;
        $radius    = rand( 1, $max ); // in miles
        $lng_min   = $longitude - $radius / abs( cos( deg2rad( $latitude ) ) * 69 );
        $lng_max   = $longitude + $radius / abs( cos( deg2rad( $latitude ) ) * 69 );
        $lat_min   = $latitude - ( $radius / 69 );
        $lat_max   = $latitude + ( $radius / 69 );
        return $lat_max . "$" . $lng_max;
    }
    /** get location **/
    public function company_location( $cid )
    {
		$condition = ( $cid != 0 ) ? array('company_id'=>(int)$cid,'user_type'=>'C') : array('user_type'=>'A');
		$res = $this->mongo_db->findOne(MDB_PEOPLE,$condition,array('login_country', 'login_state', 'login_city'));
		$result[] = !empty($res) ? $res : array();
        return $result;
    }
    /**get state details**/
    public function gateway_details( $company_id = null, $booktype = null )
    {
		/*if ($company_id==null) {		
			$query = ($booktype == 1)?array('pay_mod_active'=>1,'_id'=>array('$ne'=>1)):array('pay_mod_active'=>1);
			$args =array(array('$match' => $query),
				array('$project' => array(
					   'pay_mod_id' => '$_id',
					   'pay_mod_name' => '$pay_mod_name',
					   'pay_mod_default' => '$pay_mod_default',
				   )
			   ),
			   array('$sort' => array('_id'=>1))
			);
			$result = $this->mongo_db->aggregate(MDB_PAYMENT_MODULES,$args);
		} else {
			$match = ($booktype == 1)? array('_id'=>(int)$company_id,'paymentmodule.pay_active'=>1,'paymentmodule.pay_mod_id'=>array('$ne'=>1)): array('_id'=>(int)$company_id,'paymentmodule.pay_active'=>1);			
			$args =array(array('$unwind' => '$paymentmodule'),
						 array('$match' => $match),
						 array('$project' => array(
							'pay_mod_id' => '$paymentmodule.pay_mod_id',
							'pay_mod_name' => '$paymentmodule.pay_mod_name',
							'pay_mod_default' => '$paymentmodule.pay_mod_default',
							)
						),
						array('$sort' => array('pay_mod_id'=>1))
					);
			$result = $this->mongo_db->aggregate(MDB_COMPANY,$args);
		}
		return (!empty($result))? $result['result'] : array();*/
		
		$query = ($booktype == 1)?array('pay_mod_active'=>1,'_id'=>array('$ne'=>1)):array('pay_mod_active'=>1);
		$args =array(array('$match' => $query),
			array('$project' => array(
				   'pay_mod_id' => '$_id',
				   'pay_mod_name' => '$pay_mod_name',
				   'pay_mod_default' => '$pay_mod_default',
			   )
		   ),
		   array('$sort' => array('_id'=>1))
		);
		$result = $this->mongo_db->aggregate(MDB_PAYMENT_MODULES,$args);
		return (!empty($result))? $result['result'] : array();
    }
    public function sms_message( $sms_id = '' )
    {		
		//MongoDB
		$result = $this->mongo_db->find(MDB_SMS_TEMPLATES,array('_id'=>(int)$sms_id));
		return (!empty($result))?$result:array();
    }
    //Get driver current Shift status from driver shift
    public function get_driver_currentstatus( $driver_id )
    {
		$result = $this->mongo_db->findOne(MDB_DRIVER_INFO,array('_id'=>(int)$driver_id),array('shift_status'));
		return (!empty($result))?$result['shift_status']:"";
    }

    public function update_commission( $pass_logid, $total_amount, $admin_commission )
    {
		$result = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array('_id'=> (int)$pass_logid),array('company_id','driver_id'));
		$company_id = (isset($result['company_id']))?$result['company_id']:"";
		$driver_id = (isset($result['driver_id']))?$result['driver_id']:"";
		$match_query = array();
		if($company_id !='' && $company_id !=0){
			$match_query['upgrade_companyid'] = (int)$company_id;
		}

        $admin_amt = 0;
		$admin_amt     = ( $total_amount * $admin_commission ) / 100; //payable to admin
		$admin_amt     = round( $admin_amt, 2);
		$total_balance = round($total_amount, 2);
		//Set Commission to Admin
		if(ADMIN_COMMISION_SETTING) {
			$update = $this->mongo_db->updateOne(MDB_PEOPLE,array('user_type' => 'A'),array('$inc' =>array('account_balance' => $admin_amt)),array('upsert' => false));
		}

        $company_amt                          = $total_amount - $admin_amt;
        $company_amt                          = round( $company_amt, 2 );

		//Set Commission to Company
		if(COMPANY_COMMISION_SETTING) {
			$update1 = $this->mongo_db->updateOne(MDB_PEOPLE,array('user_type' => 'C', 'company_id' => (int)$company_id),array('$inc' =>array('account_balance' => $company_amt)),array('upsert' => false));
		}
		$driver_commission_amt = 0;
		//Set Commission to Driver
		if(DRIVER_COMMISION_SETTING) {
			$driver_com_result = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=> (int)$company_id),array('companydetails.driver_commission'));
			$driver_commission = isset($driver_com_result['companydetails']['driver_commission']) ? $driver_com_result['companydetails']['driver_commission'] : 0;
			$driver_commission_amt = round(($company_amt*$driver_commission/100),2);
			$company_bal_amt = $company_amt-$driver_commission_amt;
			$bal_update = $this->mongo_db->updateOne(MDB_PEOPLE,array('user_type' => 'D', '_id' => (int)$driver_id),array('$inc' =>array('account_balance' => $driver_commission_amt)),array('upsert' => false));
		}

		$result_array = array();
		$result_array['admin_commission']   = $admin_amt;
		$result_array['company_commission'] = $company_amt;
		$result_array['driver_commission'] = $driver_commission_amt;
		$result_array['trans_packtype'] = 'T';
		//$result_array['trans_packtype'] = $check_package_type;
        return $result_array;
    }
    //Getting Current Time of the company Time
    public function getcompany_all_currenttimestamp($company_id)
    {
		//$update_peop_driver = $this->mongo_db->remove(MDB_PEOPLE,array('_id'=> ''));
		//$update_peop_driver1 = $this->mongo_db->remove(MDB_DRIVER_INFO,array('_id'=> ''));
		        
        if ($company_id == "" ||$company_id == 0){
			//echo TIMEZONE;
            $current_time = convert_timezone( 'now', TIMEZONE );
            $current_date = explode( ' ', $current_time );
            $start_time   = $current_date[ 0 ] . ' 00:00:01';
            $end_time     = $current_date[ 0 ] . ' 23:59:59';
            $date         = $current_date[ 0 ] . ' %';
        } else {
			$time_zone="";
			$result = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$company_id),array('companydetails.time_zone'));
			//print_r($result);
			if(!empty($result)){
				$time_zone = isset($result['companydetails'][ 'time_zone' ])?$result['companydetails'][ 'time_zone' ]:"";
			}
            $timezonefetch       = $time_zone;
            if ( $timezonefetch != '' ) {
                $current_time = convert_timezone( 'now', $time_zone );
                $current_date = explode( ' ', $current_time );
            } else {
                $current_time = convert_timezone( 'now', TIMEZONE );
                $current_date = explode( ' ', $current_time );
                $start_time   = $current_date[ 0 ] . ' 00:00:01';
                $end_time     = $current_date[ 0 ] . ' 23:59:59';
                $date         = $current_date[ 0 ] . ' %';
            }
        }
        return $current_time;
    }
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
				//~ print_r($fields);exit;
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
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PUBLIC_UPLOADS_FOLDER.'/iOS/push_notification/driverck.pem')){
					$root = $_SERVER['DOCUMENT_ROOT'].'/'.PUBLIC_UPLOADS_FOLDER.'/iOS/push_notification/driverck.pem';
				}else{
					$root = $_SERVER['DOCUMENT_ROOT'].'/application/classes/controller/driverck.pem' ;
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
				$message = $pushmessage['message'];
				$badge = isset($pushmessage['badge']) ? $pushmessage['badge']:'0';
				//$message = "Success";
				$body['aps'] = array(
					'alert' => $message,
					'trip_details' => $pushmessage,
					'sound' => 'default',
					'badge' => $badge                               
				);	
				// Encode the payload as JSON
				$payload = json_encode($body);
				// Build the binary notification
				$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
				// Send it to the server
				$result = fwrite($fp, $msg, strlen($msg));				
				fclose($fp);  
			} 
		}		
	}
    public function company_tax( $company_id = '' )
    {
		//MongoDB
		$query = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$company_id),array('companyinfo.company_tax'));
		//echo '<pre>';print_r($query);exit;
		return (!empty($query) && isset($query['companyinfo'][ 'company_tax' ]))?$query['companyinfo'][ 'company_tax' ]:0;
    }
    public function company_timezone( $company_id = '' )
    {
		//MongoDB
		$query = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$company_id),array('companydetails.time_zone'));
		//echo '<pre>';print_r($query);exit;
		return (!empty($query))?$query['companydetails'][ 'time_zone' ]:TIMEZONE;
    }
    public function getcurrencycode()
    {
		//MongoDB
		//$result = $this->mongo_db->find(MDB_CSC,array('country_status'=>'A'),array('currency_code','currency_symbol'))->sort(array('_id'=>1));
                // ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                         $options=[
                             'projection'=>[
                                 'currency_code'=>1,
                                 'currency_symbol'=>1                                                                                                                      
                             ],
                             'sort'=>[
                                 '_id'=>1
                             ]
                         ];
                         $result = $this->mongo_db->find(MDB_CSC,['country_status'=>'A'],$options);
		
		return (!empty($result))?$result:array();
    }
    public function sms_message_by_title( $sms_title = '' )
    {
		$result = array();
		$res = $this->mongo_db->findOne(MDB_TEMPLATES,array('sms_title'=> $sms_title,'status'=>'0'),array('_id','sms_title','sms_description'));
		if(!empty($res)){
			
			$temp_arr['id'] = isset($res['_id'])?$res['_id']:'';
			$temp_arr['sms_description'] = isset($res['sms_description'])?$res['sms_description']:'';
			$temp_arr['sms_title'] = isset($res['sms_title'])?$res['sms_title']:'';
			$result[] = $temp_arr;
		}		
		return $result;		
    }
    /** get location **/
    public function get_country_details( $cid )
    {
		//MongoDB
		//$match_query = array('people.company_id'=>(int)$cid,'people.user_type'=>'C');
		$match_query = array('people.company_id'=>(int)$cid);
		$arguments = array(
			array('$unwind' => '$stateinfo'),
			array('$unwind' => '$stateinfo.cityinfo'),
			array('$lookup' 		=> array(
					'from'			=>	MDB_PEOPLE,
					'localField'	=> 'stateinfo.cityinfo.city_id',
					'foreignField'	=> 'login_city',
					'as'			=> 'people'
				)
			),
			array('$unwind' => '$people'),
			array('$match'	=> $match_query),
			array(
				'$project' => array('_id'=>0,
					'country_name'=>'$country_name',
					'login_country' => '$people.login_country',
					'login_state' => '$people.login_state',
					'login_city' => '$people.login_city',
				)
			),
			array(
				'$sort' => array(
					'people.created_date' => 1
				),
			),
		);
        $result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
		//echo "<pre>"; print_r($result); exit;
		return (!empty($result['result']))?$result['result']:array();
    }
	/******************** Get default payment gateway of Specific company *********************/
	public function company_payment_details($cid)
	{
		//MongoDB
		$match_query = array('pg.company_id'=>(int)$cid,'pg.default_payment_gateway'=>1);
		$arguments = array(
			array('$lookup' 		=> array(
					'from'			=>	MDB_PAYMENT_GATEWAYS,
					'localField'	=> "_id",
					'foreignField'	=> "company_id",
					'as'			=> "pg"
				)
			),
			array('$unwind' => '$pg'),
			array('$match'	=> $match_query),
			array(
				'$project' => array('_id'=>0,
					'payment_type'=>'$pg.payment_gateway_id',
					'payment_gateway_username' => '$pg.payment_gateway_username',
					'payment_gateway_password' => '$pg.payment_gateway_password',
					'payment_gateway_key' => '$pg.payment_gateway_signature',
					//'gateway_currency_format' => '$companyinfo.company_currency_format',
					'payment_method' => '$pg.payment_method',
					'gateway_name' => '$pg.payment_gatway',
				)
			)
		);
        $result = $this->mongo_db->aggregate(MDB_COMPANY,$arguments);
		//echo "<pre>"; print_r($result); exit;
		return (!empty($result['result']))?$result['result']:array();
	}
	/******************** Get default payment gateway of Specific company *********************/
	public function payment_gateway_details()
	{
		//MongoDB
		$match_query = array('company_id'=>0,'default_payment_gateway'=>1);
		$arguments = array(
			array('$match'	=> $match_query),
			array(
				'$project' => array('_id'=>0,
					'payment_type'=>'$payment_gateway_id',
					'payment_gateway_username' => '$payment_gateway_username',
					'payment_gateway_password' => '$payment_gateway_password',
					'payment_gateway_key' => '$payment_gateway_signature',
					//'gateway_currency_format' => '$currency_code',
					'payment_method' => '$payment_method',
					'gateway_name' => '$payment_gatway',
				)
			)
		);
        $result = $this->mongo_db->aggregate(MDB_PAYMENT_GATEWAYS,$arguments);
		//echo "<pre>"; print_r($result); exit;
		return (!empty($result['result']))?$result['result']:array();
	}
	//update brain tree settlement payment status
	public function mongo_update($collection_name,$update_array,$match_condition)
	{
		$result = $this->mongo_db->updateOne($collection_name,$match_condition,array('$set'=>$update_array),array('upsert'=>false));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
	}
	
	/** Function to send sms using twilio sms gateway **/
	public function send_sms($to,$message)
	{
		require_once(DOCROOT.'application/vendor/smsgateway/Services/Twilio.php');
				
		$sms_settings=$this->get_sms_settings();                
		$sid = $sms_settings[0]['sms_account_id']; 		
		$token = $sms_settings[0]['sms_auth_token'];
		$from = '+'.$sms_settings[0]['sms_from_number'];
		try
		{
			$client = new Services_Twilio($sid, $token);
			$res = $client->account->messages->sendMessage(
				$from, 
				$to,
				$message
			); 
		}
		catch(Throwable $e)
		{
			//~ echo $e;exit;
		} 
	}
	
	public function get_auto_id($table_name)
	{
		//$result = $this->mongo_db->find($table_name,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
             // ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                         $options=[
                             'projection'=>[
                                 '_id'=>1                                 
                             ],
                             'sort'=>[
                                 '_id'=>-1
                             ],
                             'limit'=>1
                         ];
                $result = $this->mongo_db->find($table_name,[],$options);
		//$id = $result;
                $id = (!empty($result))?array($result[0]['_id']=>0):array(1);
		reset($id);
		$first_key = key($id);
		return $first_key+1;
	}
	
	public function get_card_id()
	{
		$args = array(array('$unwind' => '$creditcard_details'),
					array('$project' => array('id'=>'$creditcard_details.passenger_cardid')),
					array('$sort' => array('creditcard_details.passenger_cardid'=>-1)),
					array('$limit' => 1)
				);
		$this->mongo_db = MangoDB::instance('default');
		$rs = $this->mongo_db->aggregate(MDB_CONTACTS,$args);
		//print_r($rs);exit;
		$first_key = (!empty($rs['result'])) ? $rs['result'][0]['id'] : 0;
		$inc_id = $first_key+1;
		return $inc_id;
	}
	
	/** Common Function to display datetime format **/
	public function setDateDisplayFormat($mongodate)
	{		
		if(is_object($mongodate))
            return date('D,dM-Y h:i:s A', $mongodate->sec);
        else
            return '';
	}
	
	/**get country details**/
	public function getCountryTblFlds($field,$countryId)
	{
		$result = $this->mongo_db->findOne(MDB_CSC,array('_id' => (int)$countryId),array($field));
		
		return (!empty($result) && isset($result[$field])) ? $result[$field] : '';		
	}
	
	public function getTaxiSpeed($modelId)
	{
		$result = array();
		$res = $this->mongo_db->findOne(MDB_MOTOR_MODEL,array('_id' => (int)$modelId),array('taxi_speed','taxi_min_speed'));
		//echo '<pre>';print_r($res);exit;
		$result[0]['taxi_speed'] = isset($res['taxi_speed']) ? $res['taxi_speed'] : 0;
		$result[0]['taxi_min_speed'] = isset($res['taxi_min_speed']) ? $res['taxi_min_speed'] : 0;
		return (!empty($result)) ? $result : array();
	}
	/**
         * Get Site settings based on inputs
         * 
         * @param type $projection_fields
         * @return type
         */
	/*public function common_site_info($projection_fields=[]){            
		if(empty($projection_fields))
		{   
			$query_result = $this->mongo_db->findOne(MDB_SITEINFO, array('_id' =>1), array('app_name','email_id','app_description','notification_settings','meta_keyword','meta_description','site_favicon','pre_authorized_amount','web_google_map_key','web_google_geo_key','google_timezone_api_key','pagination_settings','price_settings','continuous_request_time','facebook_key','facebook_secretkey','site_country','site_state','site_city','admin_commission','tax','site_copyrights','facebook_share','twitter_share','google_share','linkedin_share','sms_enable','driver_tell_to_friend_message','referral_discount','show_map','taxi_charge','fare_calculation_type','driver_referral_setting','driver_referral_amount','referral_settings','default_miles','wallet_amount1','wallet_amount2','wallet_amount3','wallet_amount_range','admin_commision_setting','company_commision_setting','driver_commision_setting','user_time_zone','date_time_format','date_time_format_script','tell_to_friend_message','default_unit','skip_credit_card','cancellation_fare_setting','site_tagline','phone_number','referral_amount','ios_google_map_key','ios_google_geo_key','android_google_key','site_logo','email_site_logo','currency_format','google_business_key', 'banner_image', 'banner_content','app_content','app_bg_color', 'about_us_content', 'about_bg_color', 'contact_us_content', 'footer_bg_color', 'app_android_store_link', 'app_ios_store_link', 'facebook_follow_link', 'twitter_follow_link', 'google_follow_link','passenger_app_android_store_link','passenger_app_ios_store_link','customer_android_key','expiry_date','site_currency','package_type','mobile_header_logo','flash_screen_logo','footer_bg_color_1','app_bg_color_1','banner_image_1', 'theme_id','frontend_mobile','website_language_settings','cloud_email_verification','site_default_language','selected_language','itune_passenger','itune_driver','fb_profile'));		
			$result[] = !empty($query_result) ? $query_result : array(); 
			$result[0]['pre_authorized_amount'] = isset($result[0]['pre_authorized_amount'])?$result[0]['pre_authorized_amount']:0;
			$result[0]['app_name'] = isset($result[0]['app_name'])?$result[0]['app_name']:'';
			$result[0]['frontend_mobile'] = isset($result[0]['frontend_mobile'])?$result[0]['frontend_mobile']:'';
			$result[0]['banner_image_1'] = isset($result[0]['banner_image_1'])?$result[0]['banner_image_1']:'';
			$result[0]['theme_id'] = isset($result[0]['theme_id'])?$result[0]['theme_id']:'1';
			$result[0]['cloud_email_verification']=isset($result[0]['cloud_email_verification'])?$result[0]['cloud_email_verification']:'0';
			$result[0]['site_default_language'] = isset($result[0]['site_default_language'])?$result[0]['site_default_language']:'en';
			$result[0]['selected_language'] = isset($result[0]['selected_language'])?$result[0]['selected_language']:'en';			
			$result[0]['itune_passenger'] = isset($result[0]['itune_passenger'])?$result[0]['itune_passenger']:'';			
			$result[0]['itune_driver'] = isset($result[0]['itune_driver'])?$result[0]['itune_driver']:'';			
			$result[0]['fb_profile'] = isset($result[0]['fb_profile'])?$result[0]['fb_profile']:'';			
		}else{
			$query_result = $this->mongo_db->findOne(MDB_SITEINFO, array('_id' =>1), $projection_fields);		
			
			$result[] = !empty($query_result) ? $query_result : array(); 
			$result[0]['pre_authorized_amount'] = isset($result[0]['pre_authorized_amount'])?$result[0]['pre_authorized_amount']:0;
			$result[0]['app_name'] = isset($result[0]['app_name'])?$result[0]['app_name']:'';
			$result[0]['web_google_map_key'] = isset($result[0]['web_google_map_key'])?$result[0]['web_google_map_key']:'';
			$result[0]['frontend_mobile'] = isset($result[0]['frontend_mobile'])?$result[0]['frontend_mobile']:'';
			$result[0]['banner_image_1'] = isset($result[0]['banner_image_1'])?$result[0]['banner_image_1']:'';
			$result[0]['theme_id'] = isset($result[0]['theme_id'])?$result[0]['theme_id']:'1';
			$result[0]['cloud_email_verification']=isset($result[0]['cloud_email_verification'])?$result[0]['cloud_email_verification']:'0';
			$result[0]['site_default_language'] = isset($result[0]['site_default_language'])?$result[0]['site_default_language']:'en';
			$result[0]['selected_language'] = isset($result[0]['selected_language'])?$result[0]['selected_language']:'en';
			if(in_array('itune_passenger',$projection_fields)){
				$result[0]['itune_passenger'] = isset($result[0]['itune_passenger'])?$result[0]['itune_passenger']:'';
			}
			if(in_array('itune_driver',$projection_fields)){
				$result[0]['itune_driver'] = isset($result[0]['itune_driver'])?$result[0]['itune_driver']:'';
			}
			if(in_array('fb_profile',$projection_fields)){
				$result[0]['fb_profile'] = isset($result[0]['fb_profile'])?$result[0]['fb_profile']:'';
			}
		}		
		
		return $result;
	}*/
        
        /**
         * Get Site settings based on inputs
         * 
         * @param type $projection_fields
         * @return type
         */
	public function common_site_info($projection_fields=[]){      
            
            $theme_array = array('site_copyrights','site_logo');
            
            if(!empty($projection_fields)){ 
				$projection_field = [];
                foreach($projection_fields as $key=>$value){					
					$array_value = (in_array($value,$theme_array)) ? '$themesettings.'.$value : '$'.$value;
					$projection_field[$value] = $array_value;
				}
            }else{
                $projection_field = array(										
                        'app_name'	=>'$app_name',
                        'email_id'	=>'$email_id',
                        'app_description'	=>'$app_description',
                        'notification_settings'	=>'$notification_settings',
                        'meta_keyword'	=>'$meta_keyword',
                        'meta_description'	=>'$meta_description',
                        'pre_authorized_amount'	=>'$pre_authorized_amount',
                        'web_google_map_key'	=>'$web_google_map_key',
                        'web_google_geo_key'	=>'$web_google_geo_key',
                        'google_timezone_api_key'	=>'$google_timezone_api_key',
                        'pagination_settings'	=>'$pagination_settings',
                        'price_settings'	=>'$price_settings',
                        'continuous_request_time'	=>'$continuous_request_time',
                        'facebook_key'	=>'$facebook_key',
                        'facebook_secretkey'	=>'$facebook_secretkey',
                        'site_country'	=>'$site_country',
                        'site_state'	=>'$site_state',
                        'site_city'	=>'$site_city',
                        'admin_commission'	=>'$admin_commission',
                        'tax'	=>'$tax',
                        'facebook_share'	=>'$facebook_share',
                        'twitter_share'	=>'$twitter_share',
                        'google_share'	=>'$google_share',
                        'linkedin_share'	=>'$linkedin_share',
                        'sms_enable'	=>'$sms_enable',
                        'driver_tell_to_friend_message'	=>'$driver_tell_to_friend_message',
                        'referral_discount'	=>'$referral_discount',
                        'show_map'	=>'$show_map',
                        'taxi_charge'	=>'$taxi_charge',
                        'fare_calculation_type'	=>'$fare_calculation_type',
                        'driver_referral_setting'	=>'$driver_referral_setting',
                        'driver_referral_amount'	=>'$driver_referral_amount',
                        'referral_settings'	=>'$referral_settings',
                        'default_miles'	=>'$default_miles',
                        'wallet_amount1'	=>'$wallet_amount1',
                        'wallet_amount2'	=>'$wallet_amount2',
                        'wallet_amount3'	=>'$wallet_amount3',
                        'wallet_amount_range'	=>'$wallet_amount_range',
                        'admin_commision_setting'	=>'$admin_commision_setting',
                        'company_commision_setting'	=>'$company_commision_setting',
                        'driver_commision_setting'	=>'$driver_commision_setting',
                        'user_time_zone'	=>'$user_time_zone',
                        'date_time_format'	=>'$date_time_format',
                        'date_time_format_script'	=>'$date_time_format_script',
                        'tell_to_friend_message'	=>'$tell_to_friend_message',
                        'default_unit'	=>'$default_unit',
                        'skip_credit_card'	=>'$skip_credit_card',
                        'cancellation_fare_setting'	=>'$cancellation_fare_setting',
                        'site_tagline'	=>'$site_tagline',
                        'phone_number'	=>'$phone_number',
                        'referral_amount'	=>'$referral_amount',
                        'ios_google_map_key'	=>'$ios_google_map_key',
                        'ios_google_geo_key'	=>'$ios_google_geo_key',
                        'android_google_key'	=>'$android_google_key',
                        'site_logo'	=>'$site_logo',
                        'email_site_logo'	=>'$email_site_logo',
                        'currency_format'	=>'$currency_format',
                        'google_business_key'	=>'$google_business_key',
						 'banner_image'	=>'$banner_image',
						 'app_android_store_link'	=>'$app_android_store_link',
						 'app_ios_store_link'	=>'$app_ios_store_link',
						 'facebook_follow_link'	=>'$facebook_follow_link',
						 'twitter_follow_link'	=>'$twitter_follow_link',
						 'google_follow_link'	=>'$google_follow_link',
                        'passenger_app_android_store_link'	=>'$passenger_app_android_store_link',
                        'passenger_app_ios_store_link'	=>'$passenger_app_ios_store_link',
                        'customer_android_key'	=>'$customer_android_key',
                        'expiry_date'	=>'$expiry_date',
                        'site_currency'	=>'$site_currency',
                        'package_type'	=>'$package_type',
                        'website_language_settings'	=>'$website_language_settings',
                        'ios_driver_language_settings' => '$ios_driver_language_settings',
                        'ios_passenger_language_settings' => '$ios_passenger_language_settings',
                        'ios_driver_colorcode_settings' => '$ios_driver_colorcode_settings',
                        'ios_passenger_colorcode_settings' => '$ios_passenger_colorcode_settings',
                        'android_driver_language_settings' => '$android_driver_language_settings',
                        'android_passenger_language_settings' => '$android_passenger_language_settings',
                        'android_driver_colorcode_settings' => '$android_driver_colorcode_settings',
                        'android_passenger_colorcode_settings' => '$android_passenger_colorcode_settings',
                        'cloud_email_verification'	=>'$cloud_email_verification',
                        'site_default_language'	=>'$site_default_language',
                        'selected_language'	=>'$selected_language',
                        'itune_passenger'	=>'$itune_passenger',
                        'itune_driver'	=>'$itune_driver',
                        'fb_profile'	=>'$fb_profile',
                        'admin_header_background'	=>'$themesettings.admin_header_background',
                        'dispatch_header_background'	=>'$themesettings.dispatch_header_background',
                        'admin_footer_background'	=>'$themesettings.admin_footer_background',
                        'admin_sidebar_background'	=>'$themesettings.admin_sidebar_background',
                        'admin_sidebar_sub_tab'	=>'$themesettings.admin_sidebar_sub_tab',
                        'admin_sidebar_icon'	=>'$themesettings.admin_sidebar_icon',
                        'admin_sidebar_icon_active'	=>'$themesettings.admin_sidebar_icon_active',
                        'admin_sidebar_icon_circle'	=>'$themesettings.admin_sidebar_icon_circle',
                        'admin_sidebar_active'	=>'$themesettings.admin_sidebar_active',
                        'admin_button_background'	=>'$themesettings.admin_button_background',
                        'admin_button_hover_background'	=>'$themesettings.admin_button_hover_background',
                        'dispatch_button_background'	=>'$themesettings.dispatch_button_background',
                        'dispatch_button_hover_background'=>'$themesettings.dispatch_button_hover_background',
                        'website_header_background'	=>'$themesettings.website_header_background',
                        'website_footer_background'	=>'$themesettings.website_footer_background',
                        'website_sidebar_background'	=>'$themesettings.website_sidebar_background',
                        'website_sidebar_icon'	=>'$themesettings.website_sidebar_icon',
                        'website_sidebar_icon_active'	=>'$themesettings.website_sidebar_icon_active',
                        'website_sidebar_active'	=>'$themesettings.website_sidebar_active',
                        'website_button_background'	=>'$themesettings.website_button_background',
                        'website_button_hover_background'	=>'$themesettings.website_button_hover_background',
                        'site_copyrights'	=>'$themesettings.site_copyrights',
                        'banner_content'	=>'$themesettings.banner_content',
                        'app_content'	=>'$themesettings.app_content',
                        'app_bg_color'	=>'$themesettings.app_bg_color',
                        'about_us_content'	=>'$themesettings.about_us_content',
                        'about_bg_color'	=>'$themesettings.about_bg_color',
                        'footer_bg_color'	=>'$themesettings.footer_bg_color',
                        'contact_us_content'	=>'$themesettings.contact_us_content',
                        'footer_bg_color_1'	=>'$themesettings.footer_bg_color_1',
                        'app_bg_color_1'	=>'$themesettings.app_bg_color_1',
                        'theme_id'	=>'$themesettings.theme_id',
                        'site_favicon'	=>'$themesettings.site_favicon',
                        'banner_image'	=>'$themesettings.banner_image',
                        'mobile_header_logo'	=>'$themesettings.mobile_header_logo',
                        'flash_screen_logo'	=>'$themesettings.flash_screen_logo',
                        'banner_image_1'	=>'$themesettings.banner_image_1',
                        'frontend_mobile'	=>'$themesettings.frontend_mobile',
                        'frontend_car'	=>'$themesettings.frontend_car'
                    );
            }
            
            $args = array(array('$lookup' => 
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
            $query_result = $this->mongo_db->Aggregate(MDB_SITEINFO, $args);
           	
            $result = !empty($query_result['result']) ? $query_result['result'] : array(); 
            $result[0]['pre_authorized_amount'] = isset($result[0]['pre_authorized_amount'])?$result[0]['pre_authorized_amount']:0;
            $result[0]['app_name'] = isset($result[0]['app_name'])?$result[0]['app_name']:'';
            $result[0]['frontend_mobile'] = isset($result[0]['frontend_mobile'])?$result[0]['frontend_mobile']:'';
            $result[0]['frontend_car'] = isset($result[0]['frontend_car'])?$result[0]['frontend_car']:'';
            $result[0]['banner_image_1'] = isset($result[0]['banner_image_1'])?$result[0]['banner_image_1']:'';
            $result[0]['theme_id'] = isset($result[0]['theme_id'])?$result[0]['theme_id']:'1';
            $result[0]['cloud_email_verification']=isset($result[0]['cloud_email_verification'])?$result[0]['cloud_email_verification']:'0';
            $result[0]['site_default_language'] = isset($result[0]['site_default_language'])?$result[0]['site_default_language']:'en';
            $result[0]['selected_language'] = isset($result[0]['selected_language'])?$result[0]['selected_language']:'en';			
            $result[0]['itune_passenger'] = isset($result[0]['itune_passenger'])?$result[0]['itune_passenger']:'';			
            $result[0]['itune_driver'] = isset($result[0]['itune_driver'])?$result[0]['itune_driver']:'';			
            $result[0]['fb_profile'] = isset($result[0]['fb_profile'])?$result[0]['fb_profile']:'';
            $result[0]['expiry_date'] = isset($result[0]['expiry_date'])?$result[0]['expiry_date']:'';
            return !empty($result) ? $result : array();
	}
	public function common_findcompanyid($subdomain){

		$args = array(array('$match' => array('companyinfo.company_domain' => $subdomain)),
					array('$project' => array(
										'_id' => 0,
										'cid' => '$_id',
										'time_zone' => '$companydetails.time_zone',
										'user_time_zone' => '$companydetails.user_time_zone')),
					array('$limit' => 1));
		$result = $this->mongo_db->aggregate(MDB_COMPANY, $args);
		return !empty($result['result']) ? $result['result'] : array();
	}
	
	public function common_currency_details(){  
		$result = array();
		/*$match = array('default_payment_gateway' =>1, 'company_id' => (int)$company_id);
		$res = $this->mongo_db->findOne(MDB_PAYMENT_GATEWAYS,$match,array('currency_code','currency_symbol','payment_gateway_id'));*/
		$match = array('default' =>1, 'country_status' => 'A');
		$res = $this->mongo_db->findOne(MDB_CSC,$match,array('currency_code','currency_symbol','telephone_code'));	
	
		if(!empty($res)){
			$result[] = $res;
		}
		
		return !empty($result) ? $result : array();
	}
	
	public function common_company_details($company_id){
	
		$args = array(array('$lookup' => array('from' => 'people','localField' => '_id','foreignField' => 'company_id','as' => 'people')),
									array('$unwind' =>  array( 'path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true ) ),
									array('$match' => array('people.user_type' => 'C','_id' => (int)$company_id)),
									array('$project' => array('company_logo' => '$companyinfo.company_logo' ,  
										'company_favicon' => '$companyinfo.company_favicon', 
										'company_app_name' => '$companyinfo.company_app_name', 
										'company_api_key' => '$companyinfo.company_api_key',    
										'cancellation_fare' => '$companyinfo.cancellation_fare' , 
										'customer_app_url' => '$companyinfo.customer_app_url',
										'driver_app_url' => '$companyinfo.driver_app_url',
										'company_facebook_key' => '$companyinfo.company_facebook_key', 
										'company_facebook_secretkey' => '$companyinfo.company_facebook_secretkey',
										'company_notification_settings' => '$companyinfo.company_notification_settings',
										'company_phone_number' => '$companyinfo.company_phone_number', 
										'company_meta_title' => '$companyinfo.company_meta_title', 
										'company_meta_keyword' => '$companyinfo.company_meta_keyword', 
										'company_meta_description' => '$companyinfo.company_meta_description', 
										'company_copyrights' => '$companyinfo.company_copyrights', 
										'default_unit' => '$companyinfo.default_unit',
										'skip_credit_card' => '$companyinfo.skip_credit_card', 
										'fare_calculation_type' => '$companyinfo.fare_calculation_type', 
										'company_app_description' => '$companyinfo.company_app_description', 
										'name' => '$people.name' ,
										'lastname' => '$people.lastname' ,
										'email' => '$people.email' ,
										'address' => '$people.address' , 
										'account_balance' => '$people.account_balance' , 
										'company_facebook_share' => '$companydetails.company_facebook_share',
										'company_twitter_share' => '$companydetails.company_twitter_share', 
										'company_google_share' => '$companydetails.company_google_share',
										'company_linkedin_share' => '$companydetails.company_linkedin_share',										
										'company_name' => '$companydetails.company_name', 
										'header_bgcolor' => '$companydetails.header_bgcolor', 
										'menu_color' => '$companydetails.menu_color',
										'mouseover_color' => '$companydetails.mouseover_color',										
										'driver_commission' => '$companydetails.driver_commission')),
										array('$limit' => 1)
									);
		$result = $this->mongo_db->Aggregate(MDB_COMPANY, $args);	
		
		return !empty($result['result']) ? $result['result'] : array();
	}
	
	function findcompany_currency($company_cid)
	{		
		/*$match = array('default_payment_gateway'=>1,'company_id' =>(int)$company_cid);
		$result = $this->mongo_db->findOne(MDB_PAYMENT_GATEWAYS,$match,array('currency_code','currency_symbol'));*/
                
                $match = array('default' =>1, 'country_status' => 'A');
		$result = $this->mongo_db->findOne(MDB_CSC,$match,array('currency_code','currency_symbol'));
		
		if(!empty($result))
			$currency_arr[] = $result;
		else
			$currency_arr[] = array('currency_code' =>CURRENCY_FORMAT, 'currency_symbol' =>CURRENCY);
			
		return $currency_arr;

	}
	
	function findcompany_currencyformat($company_cid)
	{
		/*$match = array('default_payment_gateway'=>1,'company_id' =>(int)$company_cid);
		$result = $this->mongo_db->findOne(MDB_PAYMENT_GATEWAYS,$match,array('currency_code','currency_symbol'));
		
		if(!empty($result))
			$currency = $result['currency_code'];
		else*/
			$currency = CURRENCY_FORMAT;
			
		return $currency;
	}
	
	function getcompanycontent($cid)
	{
		$match = array('_id' => (int)$cid, 
					'company_cms.type' => 1);
		$args = array(array('$unwind' => '$company_cms'),
					array('$match' => $match),
					array('$project' => array('company_id' => '$company_cms.company_id',
						'menu_name' => '$company_cms.menu_name',
						 'page_url' => '$company_cms.page_url',
						 'banner_image' => '$company_cms.banner_image',
						 'type' => '$company_cms.type'
						))
				);
		$result = $this->mongo_db->Aggregate(MDB_COMPANY, $args);		
		//echo '<pre>';print_r($result);exit;
		return !empty($result['result']) ? $result['result'] : array();		
	}
	
	public function smtp_settings(){
		
		$result = $this->mongo_db->findOne(MDB_SMTP_SETTINGS, array('_id' => 1), array('smtp'));
		 
		$smtp_result[] = $result;
		return !empty($smtp_result) ? $smtp_result : array();
	}
	
	public function getcountry_currecny_details() {}
		
			/** get location **/
	public function companyLocationDetails($cid)
	{
		$result = array();
		$match = array('cmp_value' => true);
		$usertype = 'A';
		if($cid != ''){
			$match['company_id'] = (int)$cid;
			$usertype = 'C';
		}
		$match['user_type'] = $usertype;		
		$args = array(
					array('$lookup' => array('from' => MDB_CSC,'localField' => 'login_country','foreignField' => '_id','as' => 'csc')),
					array('$unwind' =>  array( 'path' =>  '$csc', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$unwind' =>  array( 'path' =>  '$csc.stateinfo', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$unwind' =>  array( 'path' =>  '$csc.stateinfo.cityinfo', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$project'  =>  array(
						'company_id' => '$company_id',
						'user_type' => '$user_type',
						'country_name' => '$csc.country_name',
						'state_name' => '$csc.stateinfo.state_name',
						'city_name' => '$csc.stateinfo.cityinfo.city_name',
					 'cmp_value' =>  array('$eq' =>  array('$login_state', '$csc.stateinfo.state_id')),
					 'cmp_value' =>  array('$eq' =>  array('$login_city', '$csc.stateinfo.cityinfo.city_id')))),
					 array('$match' => $match),
					 array('$project' => array('_id' => 0,
						 'country_name' => '$country_name',
						 'state_name' => '$state_name',
						 'city_name' => '$city_name'))
				);
		$res = $this->mongo_db->Aggregate(MDB_PEOPLE, $args);
		if(!empty($res['result'])){
			$result = $res['result'];
		}
		return $result;
	}
	
	public function getPassengerDetails($passenger_logid)
	{
		$temp_arr = $result = array();
		$match = array('_id' => (int)$passenger_logid);
		$args = array(
					array('$lookup' => array('from' => MDB_PASSENGERS, 'localField' => 'passengers_id',
												'foreignField' => '_id', 'as' => 'passengers')),
					array('$unwind' =>  array('path' =>  '$passengers', 'preserveNullAndEmptyArrays' => true)),
					array('$project' => array('passengers_log_id' => '$_id',
											'current_location' => '$current_location',
											'drop_location' => '$drop_location',
											'pickup_time' => '$pickup_time',
											'name' => '$passengers.name',
											'email' => '$passengers.email',
											'country_code' => '$passengers.country_code',
											'phone' => '$passengers.phone'
											))
				);
		$res = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			
			$temp_arr = $res['result'];
			$temp_arr[0]['current_location'] = isset($temp_arr[0]['current_location']) ? $temp_arr[0]['current_location'] : '';
			$temp_arr[0]['drop_location'] = isset($temp_arr[0]['drop_location']) ? $temp_arr[0]['drop_location'] : '';
			$temp_arr[0]['name'] = isset($temp_arr[0]['name']) ? $temp_arr[0]['name'] : '';
			$temp_arr[0]['email'] = isset($temp_arr[0]['email']) ? $temp_arr[0]['email'] : '';
			$temp_arr[0]['country_code'] = isset($temp_arr[0]['country_code']) ? $temp_arr[0]['country_code'] : '';
			$temp_arr[0]['phone'] = isset($temp_arr[0]['phone']) ? $temp_arr[0]['phone'] : '';
			$temp_arr[0]['pickup_time'] = isset($temp_arr[0]['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s', $temp_arr[0]['pickup_time']) : '';
			$result = $temp_arr;
		}		
		return $result;
	}
	
	public function promocode_used_update($promo_code,$passenger_id)
	{
		$match = array('promocode' => $promo_code,'passenger_id' => (int)$passenger_id);
		$promo_fetch = $this->mongo_db->findOne(MDB_PASSENGERS_PROMO,$match, array('_id','promo_used_details'));
		
		if(count($promo_fetch) > 0)
		{
			if(isset($promo_fetch['promo_used_details']) && $promo_fetch['promo_used_details'] != "") {
				$passenger_promoid = $promo_fetch['_id'];
				$promo_used_details = unserialize($promo_fetch['promo_used_details']);
				if(array_key_exists($passenger_id, $promo_used_details)) {
					$promo_used_details[$passenger_id]++;
				}
				$data = serialize($promo_used_details);
				$set_arr = array("promo_used_details" => $data);
				$result = $this->mongo_db->updateOne(MDB_PASSENGERS_PROMO,array('_id' => (int)$passenger_promoid),array('$set' => $set_arr),array('upsert' => false));				
			}
		}
	}
	
	public function get_company_home_data($cid){
		
		$match = array('cid' =>(int)$cid);
		$result = $this->mongo_db->findOne(MDB_COMPANY,$match,array('banner_image','banner_content','app_content', 'app_android_store_link', 'app_ios_store_link', 'app_bg_color', 'about_us_content', 'about_bg_color', 'footer_bg_color','contact_us_content', 'facebook_follow_link', 'google_follow_link', 'twitter_follow_link'));
		
		//print_r($result); exit;
	
	   return (isset($rs[0]) && count($rs[0]))?$rs[0]:array();
    }
	
	public function getUserDetailByEmail($field = "",$type,$email)
	{
		$table = ($type == 'P') ? MDB_PASSENGERS : MDB_PEOPLE;
		$res = $this->mongo_db->findOne($table,array('email' => $email),array($field));
		$result = !empty($res) ? $res[$field] : '';
		return $result;
	}
	
	public function getuserphone($type,$email)
	{
		$table = ($type == 'P') ? MDB_PASSENGERS : MDB_PEOPLE;
		$res = $this->mongo_db->findOne($table,array('email'=>$email),array('phone','country_code'));
		$country_code = isset($res['country_code']) ? $res['country_code']: '';
		$result = !empty($res) ? $country_code.$res['phone'] : '';
		return $result;
	}
	
	public function insert_promocode($data)
	{		 
		$current_time = convert_timezone('now', TIMEZONE);
		$inc_id = Commonfunction::get_auto_id(MDB_PASSENGERS_PROMO);
		$passengers = is_array($data['passenger_id']) ? $data['passenger_id'] : array($data['passenger_id']);
		$payment_insert_data = array("_id"=>$inc_id,
							"passenger_id"	=> $passengers,
							"company_id" 	=> $data['company_id'],
							"promocode" 	=> $data['promocode'],
							"promo_discount"=> $data['promo_discount'],
							"promo_used"	=> "0",
							"amount_earned"	=> "0",
							"start_date"	=> Commonfunction::MongoDate(strtotime($data['start_date'])), 
							"expire_date"	=> Commonfunction::MongoDate(strtotime($data['expire_date'])), 
							"promo_limit"	=> $data['promo_limit'],
							"createdate"	=> Commonfunction::MongoDate(strtotime($data['createdate'])), 
							"promo_used_details"	=> $data['promo_used_details'],
		);
		//echo "<pre>"; print_r($payment_insert_data); exit;
		$pay_result = $this->mongo_db->insertOne(MDB_PASSENGERS_PROMO,$payment_insert_data);
		return $pay_result;
	}
	public function driver_logout_update($update_array, $driver_id)
    {
		$result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id' => (int)$driver_id),array('$set' => $update_array),array('upsert' => false));
		return $result;	
    }
    
    public function update_driverservice($update_array="", $driver_shift_id)
    {		
		$current_time = convert_timezone('now', TIMEZONE);
		$result = $this->mongo_db->updateOne(MDB_SHIFT_HISTORY,array('_id' => (int)$driver_shift_id),array('$set' => array("shift_end" => Commonfunction::MongoDate(strtotime($current_time)))),array('upsert' => false));
		return $result;	
    }
    
    public function get_passenger_name($id)
	{
		$result = $this->mongo_db->findOne(MDB_PASSENGERS, array('_id' => (int)$id), array('name'));
		if(!empty($result)){
			return $result['name'];
		}else{
			return '';
		}
	}
	
	public static function getcompany_models($companyid)
    {
        $mongodb = MangoDB::instance('default');
        $args = array(
                array('$unwind' => '$model_fare'),
                array('$match' => array('_id'=> (int)$companyid)),
                array('$project'=>array('_id'=>0,'model_id'=>'$model_fare.model_id','model_name'=>'$model_fare.model_name')),
                array('$sort' => array('model_id' => 1))
        );
        $result = $mongodb->aggregate(MDB_COMPANY,$args);
        return (!empty($result['result']))?$result['result']:array();
    }

	# driver push notification
	public function send_drivernotification($trip_id){
		
		$arguments = array(
						array('$match' => array('_id' => (int)$trip_id)),
						array('$lookup'=>array(
							'from'=>MDB_PEOPLE,
							'localField'=>"selected_driver",
							'foreignField'=>"_id",
							'as'=>"people"        
						)),
						array('$unwind' =>  array( 'path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true)),		
						array('$project' => array(
							'device_token' => '$people.device_token',
							'device_type' => '$people.device_type',
							'device_id' => '$people.device_id',							
						))					
					);
        $res = $this->mongo_db->aggregate(MDB_REQUEST_HISTORY,$arguments);
        //~ print_r($res);exit;
        if(isset($res['result'])){
			# driver api key
			$customer_google_api    = $this->select_site_settings( 'driver_android_key', MDB_SITEINFO );
			$result = $res['result'];
			$device_token = isset($result[0]['device_token']) ? $result[0]['device_token']: '';
			$device_type = isset($result[0]['device_type']) ? $result[0]['device_type']: '';
			$device_id = isset($result[0]['device_id']) ? $result[0]['device_id']: '';
			$message = array("message" =>__('trip_cancelled_passenger'), "badge"=> "25");
			if($device_type == 1){
				$message['status'] = '25';
			}
			$this->send_pushnotification($device_token,$device_type,$message,$customer_google_api);
		}
		return 1;
	}
	
	private function get_sms_settings(){
            $sms_settings = $this->mongo_db->findOne(MDB_SMS_SETTINGS, array('_id' =>1), array('sms_account_id','sms_auth_token','sms_from_number'));
		$sms_acc = $sms_auth = $sms_fromno = '';
		if(!empty($sms_settings)){
			
			$sms_acc = isset($sms_settings['sms_account_id'])?$sms_settings['sms_account_id']:'';
			$sms_auth = isset($sms_settings['sms_auth_token'])?$sms_settings['sms_auth_token']:'';
			$sms_fromno = isset($sms_settings['sms_from_number'])?$sms_settings['sms_from_number']:'';
		}
		$result[0]['sms_account_id'] = $sms_acc;
		$result[0]['sms_auth_token'] = $sms_auth;
		$result[0]['sms_from_number'] = $sms_fromno;
                return $result;
	}
	
	/*private function get_sms_settings(){
		$project = array('sms_account_id','sms_auth_token','sms_from_number','http_link','username','password','sms_from_number1','default','default1');
		$sms_settings = $this->mongo_db->findOne(MDB_SMS_SETTINGS, array('_id' =>1), $project);
		$result = array();
		if(!empty($sms_settings)){
			
			$result['sms_acc'] = isset($sms_settings['sms_account_id'])?$sms_settings['sms_account_id']:'';
			$result['sms_auth_token'] = isset($sms_settings['sms_auth_token'])?$sms_settings['sms_auth_token']:'';
			$result['sms_from_number'] = isset($sms_settings['sms_from_number'])?$sms_settings['sms_from_number']:'';
			$result['sms_auth'] = isset($sms_settings['http_link'])?$sms_settings['http_link']:'';
			$result['username'] = isset($sms_settings['username'])?$sms_settings['username']:'';
			$result['password'] = isset($sms_settings['password'])?$sms_settings['password']:'';
			$result['sms_from_number1'] = isset($sms_settings['sms_from_number1'])?$sms_settings['sms_from_number1']:'';
			$result['default'] = isset($sms_settings['default'])?$sms_settings['default']:'';
			$result['default1'] = isset($sms_settings['default1'])?$sms_settings['default1']:'';
		}
		return $result;
	}*/
	
	public function update_force_login($update_arr, $driver_id){
		
		$match = array();
		$match['_id'] = (int)$driver_id;		
		$options=['_id',
				'device_token',
				'device_id',
				'device_type'];
				
		$res = $this->mongo_db->findOne(MDB_PEOPLE,$match,$options);
		if(!empty($res)){
			$result = $res;			
			$device_type = isset($result['device_type']) ? $result['device_type']: '';			
			$device_token = isset($result['device_token']) ? $result['device_token']: '';			
			$device_id = isset($result['device_id']) ? $result['device_id']: '';
			$message = __('already_login');
			$message = array("message" =>__('already_login1'));
			$customer_google_api='';
			if($device_type == 1){
				# driver api key
				$customer_google_api    = $this->select_site_settings( 'driver_android_key', MDB_SITEINFO );
				$message['status'] = '25';
			}
			$this->send_pushnotification($device_token,$device_type,$message,$customer_google_api);		
		}	
		
		$res = $this->mongo_db->updateOne(MDB_PEOPLE, array('_id' => (int)$driver_id), array('$set' => $update_arr), array('upsert' => false));
		return 1;
	}
}
?>
