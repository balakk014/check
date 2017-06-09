<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Mobileapi Extended Model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityMobileapi117extended extends Model
{
    public function __construct()
    {
        /*$this->session = Session::instance();*/
        $this->currentdate = Commonfunction::getCurrentTimeStamp();
        # created date
		$this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
		//MongoDB Instance
		$this->mongo_db = MangoDB::instance('default');
    }
	
	//Update driver request
    public function update_driver_request($data)
    {
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id' => (int)$data['id']),array('$set'=>array("notification_status" => (int)$data['notification_status'])),array('upsert'=>false));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
	
	//update driver location
    public function update_driver_location($data)
    {
		$set_array = array();
		//Set Array
		if(isset($data['longitude']) && isset($data['latitude'])) {
				$set_array['loc'] = array("type" => "Point",
										"coordinates" => array((double)$data['longitude'],(double)$data['latitude'])); 
		}
		if(isset($data['status'])) { $set_array['status'] = $data['status']; }
		if(isset($data['update_date'])) { $set_array['update_date'] = Commonfunction::MongoDate(strtotime($data['update_date'])); }
		
		$result = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id' => (int)$data['driver_id']),array('$set'=>$set_array),array('upsert'=>false));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
	
	
	//update driver request details
    public function update_driver_request_details($data)
    {
		$set_array = array();		
		isset($data['status']) ? $set_array['status'] = (int)$data['status']:'';
		$result = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY,array('_id' => (int)$data['trip_id']),array('$set'=>$set_array),array('upsert'=>false));
        return 1;
    }
    
    public function get_insert_id($collection = ""){
		 $options=[
			'projection'=>[
				'_id'=>1
			],
			'sort'=>[
				'_id'=>-1
			],
			'limit'=>1
		];
        $result = $this->mongo_db->find($collection,[],$options);
		$id = (!empty($result))?array($result[0]['_id']=>0):array(1);
		reset($id);
		$first_key = key($id);
		return $first_key+1;
		/*$rs = $this->mongo_db->find($collection,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
		$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
		reset($res);
		$first_key = key($res);
		$inc_id = $first_key+1;
		return $inc_id;*/
	}
	
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

	public function update_nearpassengers($datas, $passenger_id){
		
		$update_array = array('loc' => array('type' => 'Point',
											'coordinates' => array((double)$datas['longitude'],(double)$datas['latitude'])));
		$match = array('_id' => (int)$passenger_id);
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set' => $update_array), array('upsert' => false));
		
		//return !empty($result['err']) ? 0 :1;
		return (empty($result->getwriteErrors())) ? 1 : 0;
	}
	
	public function insert_request_details($datas){
		
		$company_all_currenttimestamp = $this->getcompany_all_currenttimestamp($datas['company_id']);
		
		$insert_array = array(
			"_id" => (int)$datas['trip_id'],
			"available_drivers" => $datas['available_drivers'],
			"total_drivers" => $datas['total_drivers'],
			"selected_driver" => (int)$datas['selected_driver'],
			"status" => (int)$datas['status'],
			"trip_type" => (int)$datas['trip_type'],
			"rejected_timeout_drivers" => $datas['rejected_timeout_drivers'],
			"createdate" => Commonfunction::MongoDate(strtotime($company_all_currenttimestamp))
		);
		
		isset($datas['driver_limit']) ? $insert_array['driver_limit'] = $datas['driver_limit'] : '';
		isset($datas['actual_limit']) ? $insert_array['actual_limit'] = $datas['actual_limit'] : '';
		
		$result  = $this->mongo_db->insertOne(MDB_REQUEST_HISTORY, $insert_array);
	}
	
	public function insert_driver_shiftservice($datas){
		
		$company_all_currenttimestamp = $this->getcompany_all_currenttimestamp($datas['company_id']);		
		$insert_id = $this->get_insert_id(MDB_SHIFT_HISTORY);
		
		$insert_array = array(
							'_id' => (int)$insert_id,
							'driver_id' => (int)$datas['driver_id'],
							'taxi_id' => (int)$datas['taxi_id'],
							'shift_start' => Commonfunction::MongoDate(strtotime($company_all_currenttimestamp)),
							'shift_end' => $datas['shift_end'],
							'reason' => $datas['reason'],
							'createdate' => Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone))
						);
		
		$res = $this->mongo_db->insertOne(MDB_SHIFT_HISTORY, $insert_array);
		$result = array($insert_id);
		return $result;
	}
	
	public function update_driver_people($datas, $driver_id){
		
		$update_arr = array();
		isset($datas['driver_first_login']) ? $update_arr['driver_first_login'] = (int)$datas['driver_first_login'] : '';
		isset($datas['status']) ? $update_arr['status'] = $datas['status'] : '';
		isset($datas['login_from']) ? $update_arr['login_from'] = $datas['login_from'] : '';
		isset($datas['login_status']) ? $update_arr['login_status'] = $datas['login_status'] : '';
		isset($datas['device_id']) ? $update_arr['device_id'] = $datas['device_id'] : '';
		isset($datas['device_token']) ? $update_arr['device_token'] = $datas['device_token'] : '';
		isset($datas['device_type']) ? $update_arr['device_type'] = (int)$datas['device_type'] : '';
		isset($datas['notification_setting']) ? $update_arr['notification_setting'] = $datas['notification_setting'] : '';
		isset($datas['notification_status']) ? $update_arr['notification_status'] = (int)$datas['notification_status'] : '';
		
		if(!empty($update_arr)){
			$res = $this->mongo_db->updateOne(MDB_PEOPLE, array('_id' => (int)$driver_id), array('$set' => $update_arr), array('upsert' => false));
		}
		return 1;		
	}
	
	public function update_driver_driverinfo($datas, $driver_id){
		
		$update_arr = array();
		isset($datas['status']) ? $update_arr['status'] = $datas['status'] : '';
		
		if(!empty($update_arr)){
			$res = $this->mongo_db->updateOne(MDB_DRIVER_INFO, array('_id' => (int)$driver_id), array('$set' => $update_arr), array('upsert' => false));
		}
		return 1;		
	}
	
	public function update_driverrequest($datas, $trip_id){
		
		$update_arr = array();
		isset($datas['rejected_timeout_drivers']) ? $update_arr["rejected_timeout_drivers"] = $datas['rejected_timeout_drivers'] : '';
		isset($datas['status']) ? $update_arr['status'] = (int)$datas['status'] : '';
		
		if(!empty($update_arr)){
			$res = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY, array('_id' => (int)$trip_id), array('$set' => $update_arr), array('upsert' => false));
		}
		return 1;		
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
	
	//update_driver_referral_list
    public function update_driver_referral_list($data)
    {
		$set_array = array();
		//Set Array
		if(isset($data['registered_driver_wallet'])) { $set_array['registered_driver_wallet'] = (double)$data['registered_driver_wallet']; }
		if(isset($data['referral_status'])) { $set_array['referral_status'] = (int)$data['referral_status']; }
		if(isset($data['referred_driver_id'])) { $set_array['referred_driver_id'] = (int)$data['referred_driver_id']; }
		
		$result = $this->mongo_db->updateOne(MDB_DRIVER_REF,array('registered_driver_id' => (int)$data['registered_driver_id']),array('$set'=>$set_array),array('upsert'=>false));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    
    public function update_passengers($data, $passenger_id)
    {
		$set_array = array();
		if(isset($data['login_status'])) { $set_array['login_status'] = $data['login_status']; }
		if(isset($data['wallet_amount'])) { $set_array['wallet_amount'] = (double)$data['wallet_amount']; }
		if(isset($data['user_status'])) { $set_array['user_status'] = $data['user_status']; }
		if(isset($data['forgot_password'])) { $set_array['forgot_password'] = (int)$data['forgot_password']; }
		if(isset($data['org_password'])) { $set_array['org_password'] = $data['org_password']; }
		if(isset($data['split_fare'])) { $set_array['split_fare'] = $data['split_fare']; }
		
		if(!empty($set_array)){
			$result = $this->mongo_db->updateOne(MDB_PASSENGERS, array('_id' => (int)$passenger_id),array('$set'=>$set_array),array('upsert'=>false));
		}
        return 1;
    }
    
    public function update_passengers_email($data, $email)
    {
		$set_array = array();
		if(isset($data['skip_credit_card'])) { $set_array['skip_credit_card'] = (int)$data['skip_credit_card']; }
		if(isset($data['user_status'])) { $set_array['user_status'] = $data['user_status']; }
		
		if(!empty($set_array)){
			$result = $this->mongo_db->updateOne(MDB_PASSENGERS, array('email' => $email),array('$set'=>$set_array),array('upsert'=>false));
		}
        return 1;
    }
	
	public function update_transaction_table($set_array, $tranaction_id){	
			
		$result = $this->mongo_db->updateOne(MDB_TRANSACTION,array('_id' => (int)$tranaction_id),array('$set'=>$set_array),array('upsert'=>false));
        return (empty($result->getwriteErrors())) ? 1 : 0;
	}
	
	public function insert_transaction_table($insert_array){
		
		$inc_id = Commonfunction::get_auto_id(MDB_TRANSACTION);
		$insert_array = array(
			"_id" => (int)$inc_id,
			"passengers_log_id" => (int)$insert_array['passengers_log_id'],
			"tripfare"			=> (float)$insert_array['tripfare'],
			"fare" 				=> (float)$insert_array['fare'],
			"tips" 				=> (float)$insert_array['tips'],
			"waiting_cost"		=> (float)$insert_array['waiting_cost'],
			"tax_percentage"	=> (float)$insert_array['tax_percentage'],
			"company_tax"		=> (float)$insert_array['company_tax'],
			"minutes_fare"		=> (float)$insert_array['minutes_fare'],
			"base_fare"			=> (float)$insert_array['base_fare'],
			"payment_type"		=> (int)$insert_array['payment_type'],
			"amt"				=> (float)$insert_array['amt'],
			"nightfare_applicable" => (int)$insert_array['nightfare_applicable'],
			"nightfare" 		=> (float)$insert_array['nightfare'],
			"eveningfare_applicable" => (int)$insert_array['eveningfare_applicable'],
			"eveningfare" 		=> (float)$insert_array['eveningfare'],
			"admin_amount"		=> (float)$insert_array['admin_amount'],
			"company_amount"	=> (float)$insert_array['company_amount'],
			"driver_amount"		=> (float)$insert_array['driver_amount'],
			"promo_discount_fare" => (double)$insert_array['promo_discount_fare'],
			"distance"		=> $insert_array['distance'],
			"actual_distance"		=> $insert_array['actual_distance'],
			"distance_unit"		=> $insert_array['distance_unit'],
			"passenger_discount" => $insert_array['passenger_discount'],
			"waiting_time" => $insert_array['waiting_time'],
			"trip_minutes" => $insert_array['trip_minutes'],
			"remarks" => $insert_array['remarks'],
			"trans_packtype" => $insert_array['trans_packtype'],
			"fare_calculation_type" => $insert_array['fare_calculation_type'],
			'current_date' => Commonfunction::MongoDate(strtotime($this->currentdate))
		);
		$result  = $this->mongo_db->insertOne(MDB_TRANSACTION, $insert_array);
        return $inc_id;
	}
	
	//update passenger wallet amount
    public function update_passenger_wallet_array($data)
    {
		$update_array = array();
		if(isset($data['wallet_amount'])){ $update_array['wallet_amount'] = (double)$data['wallet_amount']; }
		$match = array('_id' => (int)$data['id']);
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set' => $update_array), array('upsert' => false));
		return !empty($result->getwriteErrors()) ? 0 :1;
    }
	
	//update passenger used referral
    public function update_passenger_used_referral($data)
    {
		$update_array = array();
		if(isset($data['referral_amount_used'])){ $update_array['referral_amount_used'] = (int)$data['referral_amount_used']; }
		$match = array('passenger_id' => (int)$data['passenger_id']);
		$result = $this->mongo_db->updateOne(MDB_PASSENGER_REFERRAL,$match,array('$set' => $update_array), array('upsert' => false));
		return !empty($result->getwriteErrors()) ? 0 :1;
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
		
	public function insert_drivershift($datas, $company_id=''){
               
		   $insert_id = $this->get_insert_id(MDB_SHIFT_HISTORY);        
		   $shift_start = $this->getcompany_all_currenttimestamp($company_id);
		   $insert_array = array(
												   '_id' => (int)$insert_id,
												   "driver_id" => $datas['driver_id'],
												   "taxi_id"        => $datas['taxi_id'],
												   "shift_start" => Commonfunction::MongoDate(strtotime($shift_start)),
												   "shift_end"        => $datas['shift_end'],
												   "reason"        => $datas['reason'],
												   "createdate" => Commonfunction::MongoDate(strtotime($datas['createdate']))
										   );
		   
		   $res = $this->mongo_db->insertOne(MDB_SHIFT_HISTORY, $insert_array);
		   return array((string)$insert_id);
   }
   
	public function update_drivershiftend($id, $company_id=''){
	   
		if($company_id != ''){
			$shift_end = $this->getcompany_all_currenttimestamp($company_id);
		}else{
			$shift_end = Commonfunction::getCurrentTimeStamp();
		}
		$update_arr = array('shift_end' => Commonfunction::MongoDate(strtotime($shift_end)));
		$res = $this->mongo_db->updateOne(MDB_SHIFT_HISTORY, array('_id' => (int)$id), array('$set' => $update_arr), array('upsert' => false));
		return 1;                
	}
	
	//update driver location
    public function insert_driverinfo($data)
    {
		$insert_array = array();
		//Set Array
		if(isset($data['longitude']) && isset($data['latitude'])) { 
			
			$insert_array['loc'] = array("type" => "Point","coordinates" => array((double)$data['longitude'],(double)$data['latitude']));
		}
		if(isset($data['driver_id'])) { $insert_array['-id'] = $data['driver_id']; }
		if(isset($data['status'])) { $insert_array['status'] = $data['status']; }
		if(isset($data['shift_status'])) { $insert_array['shift_status'] = $data['shift_status']; }
		
		if(!empty($insert_array)){
			  $res = $this->mongo_db->insertOne(MDB_DRIVER_INFO, $insert_array);
		}		
        return 1;
    }
    
    public function get_tripid(){
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
		return $inc_id;
	}
	
}

