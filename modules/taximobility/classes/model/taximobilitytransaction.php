<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Transaction Model details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityTransaction extends Model
{
    /**
     ****__construct()**
     *** Common Function in this model
     */
    public function __construct()
    {
        //** Session variables initialization goes here **//
        $this->session         = Session::instance();
        $this->username        = $this->session->get("username");
        $this->admin_username  = $this->session->get("username");
        $this->admin_userid    = $this->session->get("id");
        $this->admin_email     = $this->session->get("email");
        $this->user_admin_type = $this->session->get("user_type");
        $this->userid          = $this->session->get('userid');
        $this->country_id      = $this->session->get('country_id');
        $this->state_id        = $this->session->get('state_id');
        $this->city_id         = $this->session->get('city_id');
        $this->currentdate     = Commonfunction::getCurrentTimeStamp();
        # created date
		$this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
        //MongoDB Instance
        $this->mongo_db        = MangoDB::instance('default');
        $this->common_model      = Model::factory('commonmodel');
    }
    /**
     ****Company list function**
     *** Returns overall list of the company based on status
     */
    public function get_allcompany_tranaction($status = "")
    {
        $result = array();
        $condition = (!empty($status))?array('companydetails.company_status'=>$status):array();
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                    '_id'=>1,
                    'companydetails.company_name'=>1
                    ],
                'sort'=>[
                    'companydetails.company_name'=>1                    
                ]
            ];
            $res = $this->mongo_db->find(MDB_COMPANY,$condition,$options);
		$res = $res;
		if(!empty($res)){
			$res = commonfunction::change_key($res);
			foreach($res as $r){
				$result[] = array('cid' => $r['_id'], 'company_name' => $r['companydetails']['company_name']);
			}
		}
		//echo '<pre>';print_r($result);exit;
        return $result;
    }
    
    public function count_admintransaction_list($list,$company,$manager_id,$taxiid,$driverid,$passengerid,$startdate,$enddate,$transaction_id,$payment_type, $payment_mode='', $passenger_ids='', $taxi_ids = "",$driver_ids = "",$trip_id = "")
    {
        $count = $this->transaction_details($list, $company, $manager_id, $taxiid, $driverid, $passengerid, $startdate, $enddate,'','', $transaction_id, $payment_type, $payment_mode="", $passenger_ids='',$taxi_ids = "",$driver_ids = "",$trip_id="");
        //echo $count;exit;
        return $count;
    }
    /**
     ****Transaction list function**
     *** Returns overall transaction list && Count also
     */
    public function transaction_details($list,$company,$manager_id,$taxiid,$driverid,$passengerid,$startdate,$enddate,$offset='',$val='',$transaction_id,$payment_type, $payment_mode="", $passenger_ids='',$taxi_ids = "",$driver_ids = "",$trip_id="")
    {
        $usertype = $this->user_admin_type;
        $result = $temp_arr = array();
        // Condition to search based on taxi and driver for user type "Managers" //
        if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
            if ($taxi_ids !='') {                
                $tid = explode(',',$taxi_ids);
                $taxi_ids = Commonfunction::mongo_format_array($tid);
            } else {
                $taxi_ids = array();
            }
            
            if ($driver_ids !='') {
                $cdriver_id = explode(',',$driver_ids);
                $driver_ids = Commonfunction::mongo_format_array($cdriver_id);
            } else {
                $driver_ids = array();
            }
        }
        
        if ($passenger_ids !='') {
            
			$cpassenger_id = explode(',',$passenger_ids);
			$passenger_ids = Commonfunction::mongo_format_array($cpassenger_id);
        } else {
            $passenger_ids = array();
        }
        
        $date_condition = array();
        if ($startdate != "") {
            $date_condition = array(
                'createdate' => array(
                    '$gte' => Commonfunction::MongoDate(strtotime($startdate)),
                    '$lte' => Commonfunction::MongoDate(strtotime($enddate))
                )
            );
        }
        
        // Condition to search based on transaction id //
        $trans_condition = array();
        if ($transaction_id != '') {
            $trans_condition = array('trans.transaction_id' => $transaction_id);
        }
        // Condition to search based on status //
        $condition = array();
        if ($list == 'all') {
            $condition = array();
        } else if ($list == 'success') {
            $condition = array(
                'travel_status' => 1,
                'driver_reply' => 'A'
            );
        } else if ($list == 'cancelled') {
            $condition = array(
                "\$or" => array(
                    array(
                        'travel_status' => 4,
                        'driver_reply' => 'A'
                    ),
                    array(
                        'travel_status' => 8
                    ), 
                    array(
                        'travel_status' => 9,
                        'driver_reply' => 'C'
                    )
                )
            );
        } else if ($list == 'rejected') {
            $condition = array(
                //'travel_status' => 0,
                'reject.rejection_type' => 1
            );
        } else if ($list == 'pendingpayment') {
            $condition = array(
                'travel_status' => 5,
                'driver_reply' => 'A'
            );
        }
        
        $pay_condition = array();
        // Condition to search based on payment type //
        if ($payment_type != 'All' && $payment_type != '') {
            if ($list != 'rejected') {
                $pay_condition = array(
                    'trans.payment_type' => (int)$payment_type
                );
            }
        }
        // Condition to search based on company //
        $company_condition = array();
        if (($company != "") && ($company != "All")) {
            $company_condition = array(
                'company_id' => (int)$company,
                //'passengers.passenger_cid' => (int)$company
            );
        }
        // Condition to search based on taxi id //
        $taxi_condition = array();
        if (($taxiid != "All") && !empty($taxiid) ) {
            $taxi_condition = array(
                'taxi_id' => (int) $taxiid
            );
        } else {
            if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
                if (!empty($taxi_ids)) {
                    $taxi_condition = array(
                        'taxi_id' => array(
                            '$in' => $taxi_ids
                        )
                    );
                }
            }
        }
        // Condition to search based on driver id //
        $driver_condition = array();
        if (($driverid != "All") && !empty($driverid)) {
            if($list != 'rejected'){
				$driver_condition = array(
					"driver_id" => (int) $driverid
				);
			}else{
				$driver_condition = array(
					"reject.driver_id" => (int) $driverid
				);
			}
        } else {
            if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
                if (!empty($driver_ids)) {
                    $driver_condition = array(
                        "driver_id" => array(
                            '$in' => $driver_ids
                        )
                    );
                }
            }
        }
        // Condition to search based on passenger id //
        $passengers_condition = array();
        if (($passengerid != "") && ($passengerid != "All")) {
            $passengers_condition = array(
                "passengers_id" => (int) $passengerid
            );
        }
		$trip_condition = array();
        if($trip_id !='')
		{
			$trip_condition = array('_id' => (int)$trip_id);
		}   
		 
        if ($list == 'rejected') {
			//$reject_condition = array('reject.rejection_type' => 1);
            $match_query = array_merge($date_condition, $passengers_condition, $company_condition, $taxi_condition, $driver_condition, $condition, $pay_condition,$trip_condition);
            //print_r($match_query);exit;
            $common_arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => "company_id",
                        'foreignField' => "_id",
                        'as' => "company"
                    )
                ),
                array('$unwind' => array('path' => '$company', 'preserveNullAndEmptyArrays' => true )),
                //array('$unwind' => '$company'),
                array(
                    '$lookup' => array(
                        'from' => MDB_REJECTION_HISTORY,
                        'localField' => "_id",
                        'foreignField' => "passengers_log_id",
                        'as' => "reject"
                    )
                ),
                array('$unwind' => array('path' => '$reject', 'preserveNullAndEmptyArrays' => true )),
                array(
                    '$lookup' => array(
                        'from' => MDB_PEOPLE,
                        'localField' => "reject.driver_id",
                        'foreignField' => "_id",
                        'as' => "people"
                    )
                ),
                array('$unwind' => array('path' => '$people', 'preserveNullAndEmptyArrays' => true )),
                array(
                    '$lookup' => array(
                        'from' => MDB_PASSENGERS,
                        'localField' => "passengers_id",
                        'foreignField' => "_id",
                        'as' => "passengers"
                    )
                ),
                array('$unwind' => array('path' => '$passengers', 'preserveNullAndEmptyArrays' => true )),
				array('$match' => $match_query)
            );
            if (empty($offset) && empty($val)) {
                $arguments = array(
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'id' => '$_id'
                        )
                    ),
                    array(
                        '$group' => array(
							//'_id' => NULL,
							'_id' => '$id',
                            'count' => array(
                                '$sum' => 1
                            )
                        )
                    ),
                    array(
                        '$sort' => array(
                            '_id' => -1
                        )
                    )
                );
                $merge_arguments = array_merge($common_arguments,$arguments);
                //echo '<pre>';print_r($merge_arguments);exit;
                $result    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);
                //echo '<pre>';print_r($result);exit;
                return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
            } else {
                $arguments = array(
                    array(
                        '$project' => array(
                            'passengers_log_id' => '$_id',
                            'driver_id' => '$people._id',
                            'driver_name' => '$people.name',
                            'driver_phone' => '$people.phone',
                            'passenger_name' => '$passengers.name',
                            'passenger_email' => '$passengers.email',
                            'passenger_phone' => '$passengers.phone',
                            'company_id' => '$company_id',
                            'company_name' => '$company.companydetails.company_name',
                            'createdate' => '$createdate',
                            'current_location' => '$current_location',
                            'drop_location' => '$drop_location',
                            'distance' => '$distance',
							'used_wallet_amount' => '$used_wallet_amount',
                            'travel_status' => '$travel_status',
                            'driver_reply' => '$driver_reply',
                            'driver_comments' => '$driver_comments',
                            'userid' => '$company.companydetails.userid',
							'actual_pickup_time' => '$actual_pickup_time',
                            'pickup_time' => '$pickup_time',
                            'no_passengers' => '$no_passengers',
                        )
                    ),
                    array(
                        '$sort' => array(
                            'id' => -1
                        )
                    ),
                    array(
                        '$skip' => (int) $offset
                    ),
                    array(
                        '$limit' => (int) $val
                    )
                );
                $merge_arguments = array_merge($common_arguments,$arguments);
                $res= $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);
                //echo '<pre>';print_r($res);exit;
                if(!empty($res['result'])){
					foreach($res['result'] as $r){
						
						$temp_arr['driver_id'] = isset($r['driver_id'])?$r['driver_id']:'';
						$temp_arr['driver_name'] = isset($r['driver_name'])?$r['driver_name']:'';
						$temp_arr['driver_phone'] = isset($r['driver_phone'])?$r['driver_phone']:'';
						$temp_arr['passenger_name'] = isset($r['passenger_name'])?$r['passenger_name']:'';
						$temp_arr['passenger_email'] = isset($r['passenger_email'])?$r['passenger_email']:'';
						$temp_arr['passenger_phone'] = isset($r['passenger_phone'])?$r['passenger_phone']:'';
						$temp_arr['company_id'] = $r['company_id'];
						$temp_arr['company_name'] = isset($r['company_name']) ? $r['company_name']:'';
						$temp_arr['userid'] = isset($r['userid'])?$r['userid']:'';
						$temp_arr['passengers_log_id'] = $r['passengers_log_id'];
						$temp_arr['createdate'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']);
						$temp_arr['current_location'] = $r['current_location'];
						$temp_arr['drop_location'] = $r['drop_location'];
						$temp_arr['driver_reply'] = isset($r['driver_reply'])?$r['driver_reply']:'';
						$temp_arr['travel_status'] = isset($r['travel_status'])?$r['travel_status']:'';
						$temp_arr['actual_pickup_time'] = isset($r['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['actual_pickup_time']) :'';
						$temp_arr['pickup_time'] = isset($r['pickup_time'])?commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
						
						$result[] = $temp_arr;
					}
				}
				//echo "<pre>"; 	print_r($result);exit;
				return $result;
            }
        } else {
            $match_query = array_merge($date_condition, $passengers_condition, $company_condition, $taxi_condition, $driver_condition, $condition, $pay_condition, $trans_condition,$trip_condition);
            $common_arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => "company_id",
                        'foreignField' => "_id",
                        'as' => "company"
                    )
                ),
                array('$unwind' => array('path' => '$company','preserveNullAndEmptyArrays' => true)),
                array(
                    '$lookup' => array(
                        'from' => MDB_PEOPLE,
                        'localField' => "driver_id",
                        'foreignField' => "_id",
                        'as' => "people"
                    )
                ),
                array('$unwind' => array('path' => '$people','preserveNullAndEmptyArrays' => true)),
                array(
                    '$lookup' => array(
                        'from' => MDB_PASSENGERS,
                        'localField' => "passengers_id",
                        'foreignField' => "_id",
                        'as' => "passengers"
                    )
                ),
                array('$unwind' => array('path' => '$passengers','preserveNullAndEmptyArrays' => true)),
                array(
                    '$lookup' => array(
                        'from' => MDB_TRANSACTION,
                        'localField' => "_id",
                        'foreignField' => "passengers_log_id",
                        'as' => "trans"
                    )
                ),
				array( '$match' => $match_query ),
            );
            if (empty($offset) && empty($val)) {
               $arguments = array(
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'id' => '$_id'
                        )
                    ),
                    array(
                        '$group' => array(
                            '_id' => NULL,
                            'count' => array(
                                '$sum' => 1
                            )
                        )
                    ),
                    array(
                        '$sort' => array(
                            'id' => -1
                        )
                    )
                );
                $merge_arguments = array_merge($common_arguments,$arguments);
                $result    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);
                //echo '<pre>count-';print_r($merge_arguments);exit;
                return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
            } else {
                $arguments = array(                    
                    array(
                        '$project' => array(
                            '_id' => 0,
                            'id' => '$_id',
                            'driver_id' => '$people._id',
                            'driver_name' => '$people.name',
                            'driver_phone' => '$people.phone',
                            'passenger_name' => '$passengers.name',
                            'passenger_email' => '$passengers.email',
                            'passenger_phone' => '$passengers.phone',
                            'company_id' => '$company_id',
                            'company_name' => '$company.companydetails.company_name',
                            'userid' => '$company.companydetails.userid',
                            'admin_amount' => '$trans.admin_amount',
                            'company_amount' => '$trans.company_amount',
                            'transaction_id' => '$trans.transaction_id',
                            'passengers_log_id' => '$_id',
                            'payment_type' => '$trans.payment_type',
                            'createdate' => '$createdate',
                            'current_location' => '$current_location',
                            'drop_location' => '$drop_location',
                            'distance' => '$distance',
                            'nightfare' => '$trans.nightfare',
                            'eveningfare' => '$trans.eveningfare',
                            'used_wallet_amount' => '$used_wallet_amount',
                            'fare' => '$trans.fare',
                            'travel_status' => '$travel_status',
                            'driver_reply' => '$driver_reply',
                            'driver_comments' => '$driver_comments',
                            'distance_unit' => '$trans.distance_unit',
                            'payment_method' => '$trans.payment_method',
                            'no_passengers' => '$no_passengers',
                            'payment_gateway_name' => '$payment_gateway_name',
                            'actual_pickup_time' => '$actual_pickup_time',
                            'pickup_time' => '$pickup_time',
                            'rating' => '$rating',
                            'comments' => '$comments'
                            
                        )
                    ),
                    array(
                        '$sort' => array(
                            'id' => -1
                        )
                    ),
                    array(
                        '$skip' => (int) $offset
                    ),
                    array(
                        '$limit' => (int) $val
                    )
                );
                $merge_arguments = array_merge($common_arguments,$arguments);
                $res    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);
                //~ echo '<pre>if';print_r($merge_arguments);exit;
                if(!empty($res['result'])){
					foreach($res['result'] as $r){
						$temp_arr['id'] = $r['id'];
						$temp_arr['driver_id'] = isset($r['driver_id'])?$r['driver_id']:"";
						$temp_arr['driver_name'] = isset($r['driver_name'])?$r['driver_name']:"-";
						$temp_arr['driver_phone'] = isset($r['driver_phone'])?$r['driver_phone']:"";
						$temp_arr['passenger_name'] = isset($r['passenger_name'])?$r['passenger_name']:"-";
						$temp_arr['passenger_email'] = isset($r['passenger_email'])?$r['passenger_email']:"";
						$temp_arr['passenger_phone'] = isset($r['passenger_phone'])?$r['passenger_phone']:"";
						$temp_arr['company_id'] = isset($r['company_id'])?$r['company_id']:"";
						$temp_arr['company_name'] = isset($r['company_name'])?$r['company_name']:"-";
						$temp_arr['userid'] = isset($r['userid'])?$r['userid']:"";
						$temp_arr['admin_amount'] = !empty($r['admin_amount']) ? $r['admin_amount'][0]:'';
						$temp_arr['company_amount'] = !empty($r['company_amount']) ? $r['company_amount'][0]:'';
						$temp_arr['transaction_id'] = !empty($r['transaction_id']) ? $r['transaction_id'][0]:'';
						$temp_arr['passengers_log_id'] = $r['passengers_log_id'];
						$temp_arr['payment_type'] = !empty($r['payment_type']) ? $r['payment_type'][0]:'';
						$temp_arr['createdate'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']);
						$temp_arr['current_location'] = isset($r['current_location'])?$r['current_location']:"";
						$temp_arr['drop_location'] = isset($r['drop_location'])?$r['drop_location']:"";
						$temp_arr['distance'] = isset($r['distance']) ? $r['distance']:'0';
						$temp_arr['nightfare'] = !empty($r['nightfare']) ?$r['nightfare'][0]:'';
						$temp_arr['eveningfare'] = !empty($r['eveningfare']) ? $r['eveningfare'][0]:'';
						$temp_arr['used_wallet_amount'] = isset($r['used_wallet_amount'])?$r['used_wallet_amount']:'';
						$temp_arr['fare'] = !empty($r['fare']) ? $r['fare'][0] :'';
						$temp_arr['travel_status'] = !empty($r['travel_status']) ? $r['travel_status'] :'';
						$temp_arr['driver_reply'] = !empty($r['driver_reply']) ? $r['driver_reply'] :'';
						$temp_arr['driver_comments'] = isset($r['driver_comments'])?$r['driver_comments']:'';
						$temp_arr['distance_unit'] = !empty($r['distance_unit'])?$r['distance_unit'][0]:'';
						$temp_arr['payment_method'] = !empty($r['payment_method']) ? $r['payment_method']:'';
						$temp_arr['actual_pickup_time'] = isset($r['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['actual_pickup_time']) :'';
						$temp_arr['pickup_time'] = isset($r['pickup_time'])?commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
						$temp_arr['no_passengers'] = isset($r['no_passengers'])?$r['no_passengers']:'';
						$temp_arr['rating'] = isset($r['rating'])?$r['rating']:'';
						$temp_arr['comments'] = isset($r['comments'])?$r['comments']:'';
						$temp_arr['payment_gateway_name'] = isset($r['payment_gateway_name']) ? $r['payment_gateway_name']:'';
						$result[] = $temp_arr;
					}
				}
                return $result;
            }
        }
    }
    /************* Get Taxi List ******************/
    public function gettaxidetails($company_id, $manager_id)
    {
		$result = array();
        $usertype = $this->user_admin_type;
        if (($manager_id != "") && ($manager_id != "All") || ($usertype == 'M')) {
            if ($usertype == 'M') {
                $manager_id      = $this->userid;
                // function to get manager details ( city, state, country and company ) //
                $manager_details = $this->manager_details($manager_id);
                $country_id      = $manager_details['login_country'];
                $state_id        = $manager_details['login_state'];
                $city_id         = $manager_details['login_city'];
                $company_id      = $manager_details['company_id'];
            } else {
                $country_id = $this->country_id;
                $state_id   = $this->state_id;
                $city_id    = $this->city_id;
            }
            $match_query = array(
                'taxi.taxi_country' => (int) $country_id,
                'taxi.taxi_state' => (int) $state_id,
                'taxi.taxi_city' => (int) $city_id
            );
            
            if (($company_id != "") && ($company_id != "All")) {
                $match_query = array(
                    'taxi.taxi_company' => (int) $company_id,
                    'taxi.taxi_country' => (int) $country_id,
                    'taxi.taxi_state' => (int) $state_id,
                    //'taxi.taxi_city' => (int) $city_id
                );
            }
            $arguments = array(
                array(
                    '$unwind' => '$stateinfo'
                ),
                array(
                    '$unwind' => '$stateinfo.cityinfo'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_TAXI,
                        'localField' => 'stateinfo.cityinfo.city_id',
                        'foreignField' => "taxi_country",
                        'foreignField' => "taxi_state",
                        'foreignField' => "taxi_city",
                        'as' => "taxi"
                    )
                ),
                array(
                    '$unwind' => '$taxi'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => 'taxi.taxi_company',
                        'foreignField' => "_id",
                        'as' => "company"
						)
					),
				array('$unwind' =>  array( 'path' =>  '$company', 'preserveNullAndEmptyArrays' =>  true ) ),
                array(
                    '$match' => $match_query
                ),
                array(
                    '$project' => array(
                        '_id' => 0,
                        'taxi_id' => '$taxi._id',
                        'taxi_no' => '$taxi.taxi_no',
						'time_zone' => '$company.companydetails.time_zone'
                    )
                ),
                array(
                    '$sort' => array(
                        'taxi.created_date' => -1
                    )
                )
            );
            $result    = $this->mongo_db->aggregate(MDB_CSC, $arguments);
           //decho "<pre>"; print_r($result);exit;
            return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
        } else {
            // Condition to search based on company id //
            $match_query = array('_id' => array('$ne' =>0));
            if (($company_id != "") && ($company_id != "All")) {
                $match_query = array(
                    'taxi_company' => (int)$company_id,
                );
			}
			$arguments   = array(
				array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'taxi_company',
					'foreignField' => "_id",
					'as' => "company"
					)
				),
				array('$unwind' =>  array( 'path' =>  '$company', 'preserveNullAndEmptyArrays' =>  true ) ),
				array(
					'$match' => $match_query
				),
				array(
					'$project' => array(
						'_id' => 0,
						'taxi_id' => '$_id',
						'taxi_no' => '$taxi_no',
						'time_zone' => '$company.companydetails.time_zone'
					)
				),
				array(
					'$sort' => array(
						'taxi_country' => -1
					)
				)
			);
			$res      = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
			//echo "<pre>"; print_r($result);exit;
			if(!empty($res['result'])){
				$result = $res['result'];
			}
			return $result;
            
        }
    }
    /************* Get Driver List ******************/
    public function getdriverdetails($company_id, $manager_id)
    {
        $result = array();
        $usertype = $this->user_admin_type;
        if (($manager_id != "") && ($manager_id != "All") || ($usertype == 'M')) {
            // function to get manager details ( city, state, country and company ) //
            $manager_details = $this->manager_details($manager_id);
            $country_id      = $manager_details['login_country'];
            $state_id        = $manager_details['login_state'];
            $city_id         = $manager_details['login_city'];
            $company_id      = $manager_details['company_id'];
        } else {
            $country_id = $this->country_id;
            $state_id   = $this->state_id;
            $city_id    = $this->city_id;
        }
        if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
            $match_query = array(
                'people.user_type' => 'D',
                'people.company_id' => (int) $company_id,
                //~ 'people.login_country' => (int) $country_id,
                //~ 'people.login_state' => (int) $state_id,
                //~ 'people.login_city' => (int) $city_id
            );
            $arguments   = array(
                array(
                    '$unwind' => '$stateinfo'
                ),
                array(
                    '$unwind' => '$stateinfo.cityinfo'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_PEOPLE,
                        'localField' => 'stateinfo.cityinfo.city_id',
						'foreignField' => "login_country",
                        'foreignField' => "login_state",
                        'foreignField' => "login_city",
                        'as' => "people"
                    )
                ),
                array(
                    '$unwind' => '$people'
                ),
                array(
                    '$match' => $match_query
                ),
                array(
                    '$project' => array(
                        '_id' => 0,
                        'id' => '$people._id',
                        'name' => '$people.name',
                        'lastname' => '$people.lastname'
                    )
                ),
                array(
                    '$sort' => array(
                        'created_date' => -1
                    )
                )
            );
            $res      = $this->mongo_db->aggregate(MDB_CSC, $arguments);
            if(!empty($res['result'])){
				$result = $res['result'];
			}
			//echo '<pre>';print_r($res);exit;
			return $result;
        }
        
        // Condition to search based on company id //
        $match_query = array('user_type' => 'D');
        if (($company_id != "") && ($company_id != "All")) {
            $match_query['company_id'] = (int)$company_id;
		}
		
		$arguments   = array(
			 array(
				'$match' => $match_query
			),
			array(
				'$project' => array(
					'_id' => 0,
					'id' => '$_id',
					'name' => '$name',
					'lastname' => '$lastname'
				)
			),
			array(
				'$sort' => array(
					'created_date' => -1
				)
			)
		);
		$res      = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
		
		if(!empty($res['result'])){
			$result = $res['result'];
		}
		return $result;
        
    }
    /************* Function to get Manager Details ******************/
    public function manager_details($manager_id)
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_PEOPLE, array(
            'user_type' => 'M',
            '_id' => (int) $manager_id
        ), array(
            'login_country',
            'login_state',
            'login_city',
            'company_id'
        ));
        return (!empty($result)) ? $result : array();
    }
    /************* Get Taxi List ******************/
    public function getmanager_taxidetails($company_id, $manager_id)
    {
        //** function to get manager details ( city, state, country and company ) **//
		$cid = (($company_id != "") && ($company_id != "All"))?$company_id:$manager_cmid;
        $match_query = array('taxi.taxi_company' => (int) $cid);
        
        if(($manager_id != "") && ($manager_id != "All")){
			
			$manager_details = $this->manager_details($manager_id);			
			$country_id      = $manager_details['login_country'];
			$state_id        = $manager_details['login_state'];
			$city_id         = $manager_details['login_city'];
			$manager_cmid    = $manager_details['company_id'];
			
			$match_query['taxi.taxi_country'] = (int)$country_id;
            $match_query['taxi.taxi_state'] = (int)$state_id;
            $match_query['taxi.taxi_city'] = (int)$city_id;
		}		
       
        $arguments = array(
            array(
                '$unwind' => '$stateinfo'
            ),
            array(
                '$unwind' => '$stateinfo.cityinfo'
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_TAXI,
                    'localField' => 'stateinfo.cityinfo.city_id',
                    'foreignField' => "taxi_city",
                    'as' => "taxi"
                )
            ),
            array(
                '$unwind' => '$taxi'
            ),
            array(
                '$match' => $match_query
            ),
            array(
                '$project' => array(
                    '_id' => 0,
                    'taxi_id' => '$taxi._id',
                    'taxi_no' => '$taxi.taxi_no'
                )
            ),
            array(
                '$sort' => array(
                    '_id' => -1
                )
            )
        );
        $result    = $this->mongo_db->aggregate(MDB_CSC, $arguments);
        //echo "<pre>"; print_r($result['result']);exit;
        return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
        
    }
    /************* Get Driver List ******************/
    public function getmanager_driverdetails($company_id, $manager_id)
    {
		$manager_cmid = '';
		if(($manager_id != "") && ($manager_id != "All")){
			$manager_details = $this->manager_details($manager_id);	
			$country_id = $manager_details[0]['login_country'];
			$state_id = $manager_details[0]['login_state'];
			$city_id = $manager_details[0]['login_city'];
			$manager_cmid = $manager_details[0]['company_id'];
		}
	   	
        //** function to get manager details ( city, state, country and company ) **//
        $cid = (($company_id != "") && ($company_id != "All"))?$company_id:$manager_cmid;
		
		$match_query = array('people.user_type' => 'D');
        if($cid != ''){
			$match_query['people.company_id'] = (int)$cid;
		}
        
        if(($manager_id != "") && ($manager_id != "All")){
			
			$manager_details = $this->manager_details($manager_id);			
			$country_id      = $manager_details['login_country'];
			$state_id        = $manager_details['login_state'];
			$city_id         = $manager_details['login_city'];
			$manager_cmid    = $manager_details['company_id'];
			
			$match_query['people.login_country'] = (int)$country_id;
            $match_query['people.login_state'] = (int)$state_id;
            $match_query['people.login_city'] = (int)$city_id;
		}
        
       
        $arguments   = array(
            array(
                '$unwind' => '$stateinfo'
            ),
            array(
                '$unwind' => '$stateinfo.cityinfo'
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'stateinfo.cityinfo.city_id',
                    'foreignField' => "login_city",
                    'as' => "people"
                )
            ),
            array(
                '$unwind' => '$people'
            ),
            array(
                '$match' => $match_query
            ),
            array(
                '$project' => array(
                    '_id' => 0,
                    'id' => '$people._id',
                    'name' => '$people.name',
                    'lastname' => '$people.lastname'
                )
            ),
            array(
                '$sort' => array(
                    '_id' => -1
                )
            )
        );
        
        $result      = $this->mongo_db->aggregate(MDB_CSC, $arguments);
        //echo "<pre>if"; print_r($result['result']);exit;
        return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
    }
    /************* Get Manager List ******************/
    public function getmanagerdetails($company_id)
    {
		$result = array();
        $country_id = $this->country_id;
        $state_id   = $this->state_id;
        $city_id    = $this->city_id;
        $usertype   = $this->user_admin_type;
        
        $match_query = array('user_type' => 'M');
        if ($usertype == 'M') {
            
			$match_query['people.user_type'] = 'M';
			$match_query['people.login_country'] = (int)$country_id;
			$match_query['people.login_state'] = (int)$state_id;
			$match_query['people.login_city'] = (int)$city_id;
            
            $arguments   = array(
                array(
                    '$unwind' => '$stateinfo'
                ),
                array(
                    '$unwind' => '$stateinfo.cityinfo'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_PEOPLE,
                        'localField' => 'stateinfo.cityinfo.city_id',
                        'foreignField' => "login_city",
                        'as' => "people"
                    )
                ),
                array(
                    '$unwind' => '$people'
                ),
                array(
                    '$match' => $match_query
                ),
                array(
                    '$project' => array(
                        '_id' => 0,
                        'id' => '$people._id',
                        'name' => '$people.name',
                        'lastname' => '$people.lastname'
                    )
                ),
                array(
                    '$sort' => array(
                        'people.created_date' => -1
                    )
                )
            );
            $res      = $this->mongo_db->aggregate(MDB_CSC, $arguments);
			if(!empty($res['result'])){
				$result = $res['result'];
			}
			return $result;
        }
        
        // Condition to search based on company id //
        if (($company_id != "") && ($company_id != "All")) {
            
            $match_query['company_id'] = (int)$company_id;
		}
		$arguments   = array(
			 array(
				'$match' => $match_query
			),
			array(
				'$project' => array(
					'_id' => 0,
					'id' => '$_id',
					'name' => '$name',
					'lastname' => '$lastname'
				)
			),
			array(
				'$sort' => array(
					'created_date' => -1
				)
			)
		);
		$res      = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
		if(!empty($res['result'])){
			$result = $res['result'];
		}
		return $result;
    }
   
    /** Get Passengers List **************/
    public function getpassengerdetails($company_id, $manager_id)
    {
		$result = $temp_arr = array();
        $usertype    = $this->user_admin_type;  
        $match = array('_id' => array('$ne' => 0));
		if (($company_id != "") && ($company_id != "All")) {
			$match['company_id'] = (int)$company_id;
        }
        $arguments = array(
					array(
						'$lookup' => array(
							'from' => MDB_PASSENGERS,
							'localField' => "passengers_id",
							'foreignField' => "_id",
							'as' => "passengers"
						)
					),
					array('$unwind' =>  '$passengers'),
					array(
						'$lookup' => array(
							'from' => MDB_COMPANY,
							'localField' => "company_id",
							'foreignField' => "_id",
							'as' => "company"
						)
					),
					array('$unwind' =>  '$company'),
					array('$match' => $match),
					array(					
						'$project' => array(
							'_id' => 0,
							'id' => '$passengers._id',
							'name' => '$passengers.name',
							'company_name' => '$company.companydetails.company_name'
						)
					),
					array('$group' => array('_id' => array('id' => '$id',
													'name' => '$name',
													'company_name' => '$company_name'
													)
					)),
					array(
						'$sort' => array('created_date' => -1)
					)
				);	     
       
		$res  = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
		
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr = $r['_id'];
				//~ $temp_arr['id'] = $r['id'];
				//~ $temp_arr['name'] = $r['name'];
				//~ $temp_arr['company_name'] = isset($r['company_name']) ? $r['company_name'] : '';
				$result[] = $temp_arr;
			}			
		}
		//echo '<pre>';print_r($result);exit;
		return $result;           
    }
    /**
     * ****export_data()****
     *@export user listings
     */
    public function export_data($list, $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id, $payment_type)
    {
		$temp_arr = $results = array();
		//echo $list.'--'. $company.'--'. $manager_id.'--'. $taxiid.'--'. $driver_id.'--'. $passengerid.'--'. $startdate.'--'. $enddate.'--'. $transaction_id.'--'. $payment_type;exit;
        $usertype        = $this->user_admin_type;
        //echo '<pre>';
        // Condition to search based on taxi and driver for user type "Managers" //
        if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
            // Function to get taxi details //
            $taxilist = $this->gettaxidetails($company, $manager_id);
            if (count($taxilist) > 0) {
                //$taxi_id  = "";
                foreach ($taxilist as $key => $taxis) {
                    $tid[] = isset($taxis["taxi_id"]) ? $taxis["taxi_id"] : $taxis["_id"];
                    //$tid[] = isset($taxis["taxi_id"])?$taxis["taxi_id"]:$taxis["_id"];
                    //$taxi_id .= $tid . ',';
                }
                //$taxi_ids = substr($taxi_id, 0, strlen($taxi_id) - 1);
                $taxi_ids = Commonfunction::mongo_format_array($tid);
            } else {
                //$taxi_ids = "";
                $taxi_ids = array();
            }
            //echo 'taxilist=>';print_r($taxi_ids);echo '<br>';
            // Function to get driver details //
            $driverlist = $this->getdriverdetails($company, $manager_id);
            if (count($driverlist) > 0) {
                //$cdriver_id = "";
                foreach ($driverlist as $key => $drivers) {
                    $cdriver_id[] = $drivers["id"];
                    //$cdriver_id .= $drivers["id"] . ',';
                }
                //$driver_ids = substr($cdriver_id, 0, strlen($cdriver_id) - 1);
                $driver_ids = Commonfunction::mongo_format_array($cdriver_id);
            } else {
                //$driver_ids = "";
                $driver_ids = array();
            }
            //echo 'driverlist=>';print_r($driver_ids);echo '<br>';
        }
        // Function to get passenger details //
        $passengerlist = $this->getpassengerdetails($company, $manager_id);
        // Condition to search based on passengers //
        if (count($passengerlist) > 0) {
            //$cpassenger_id = "";
            foreach ($passengerlist as $key => $passengers) {
                $cpassenger_id[] = $passengers["id"];
                //$cpassenger_id .= $passengers["id"] . ',';
            }
            //$passenger_ids = substr($cpassenger_id, 0, strlen($cpassenger_id) - 1);
            $passenger_ids = Commonfunction::mongo_format_array($cpassenger_id);
        } else {
            //$passenger_ids = "";
            $passenger_ids = array();
        }
        //echo 'passengerlist=>';print_r($passenger_ids);echo '<br>';//exit;
        $date_condition = array();
        if ($startdate != "") {
            $date_condition = array(
                'createdate' => array(
                    '$gte' => Commonfunction::MongoDate(strtotime($startdate)),
                    '$lte' => Commonfunction::MongoDate(strtotime($enddate))
                )
            );
        }
        // Condition to search based on transaction id //
        $trans_condition = array();
        if ($transaction_id != '') {
            $trans_condition = array(
                'trans.transaction_id' => $transaction_id
            );
        }
        // Condition to search based on status //
        $condition = array();
        if ($list == 'all') {
            $condition = array();
        } else if ($list == 'success') {
            $condition = array(
                'travel_status' => 1,
                'driver_reply' => 'A'
            );
        } else if ($list == 'cancelled') {
            $condition = array(
                "\$or" => array(
                    array(
                        'travel_status' => 4,
                        'driver_reply' => 'A'
                    ),
                    array(
                        'travel_status' => 9,
                        'driver_reply' => 'C'
                    )
                )
            );
        } else if ($list == 'rejected') {
            $condition = array(
                'reject.rejection_type' => 1
            );
        }
        $pay_condition = array();
        // Condition to search based on payment type //
        if ($payment_type != 'All' && $payment_type != '') {
            if ($list != 'rejected') {
                $pay_condition = array(
                    'trans.payment_type' => (int) $payment_type
                );
            }
        }
        // Condition to search based on company //
        $company_condition = array();
        if (($company != "") && ($company != "All")) {
            $company_condition = array(
                'company_id' => (int) $company
            );
        }
        // Condition to search based on taxi id //
        $taxi_condition = array();
        if (($taxiid != "All") && !empty($taxiid)) {
            $taxi_condition = array(
                'taxi_id' => (int) $taxiid
            );
        } else {
            if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
                if (count($taxilist) > 0) {
                    $taxi_condition = array(
                        'taxi_id' => array(
                            '$in' => $taxi_ids
                        )
                    );
                }
            }
        }
        // Condition to search based on driver id //
        $driver_condition = array();
        if (($driver_id != "All") && !empty($driver_id)) {
            $driver_condition = array(
                "driver_id" => (int) $driver_id
            );
        } else {
            if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
                if (count($driverlist) > 0) {
                    $driver_condition = array(
                        "driver_id" => array(
                            '$in' => $driver_ids
                        )
                    );
                }
            }
        }
        // Condition to search based on passenger id //
        $passengers_condition = array();
        if (($passengerid != "") && ($passengerid != "All")) {
            $passengers_condition = array(
                "passengers_id" => (int) $passengerid
            );
        }
        if ($list == 'rejected') {
            $match_query = array_merge($date_condition, $passengers_condition, $company_condition, $taxi_condition, $driver_condition, $condition, $pay_condition);
            //print_r($match_query);//exit;
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => "company_id",
                        'foreignField' => "_id",
                        'as' => "company"
                    )
                ),
                //array('$unwind' => '$company'),
                array(
                    '$lookup' => array(
                        'from' => MDB_PEOPLE,
                        'localField' => "driver_id",
                        'foreignField' => "_id",
                        'as' => "people"
                    )
                ),
                //array('$unwind' => '$people'),
                array(
                    '$lookup' => array(
                        'from' => MDB_PASSENGERS,
                        'localField' => "passengers_id",
                        'foreignField' => "_id",
                        'as' => "passengers"
                    )
                ),
                //array('$unwind' => '$passengers'),
                 array(
                    '$lookup' => array(
                        'from' => MDB_REJECTION_HISTORY,
                        'localField' => "_id",
                        'foreignField' => "passengers_log_id",
                        'as' => "reject"
                    )
                ),
                array('$match' => $match_query),
                array(
                    '$project' => array(
                        '_id' => 0,
                        'id' => '$_id',
                        'driver_id' => '$people._id',
                        'driver_name' => '$people.name',
                        'driver_phone' => '$people.phone',
                        'passenger_name' => '$passengers.name',
                        'email' => '$passengers.email',
                        'passenger_phone' => '$passengers.phone',
                        'company_id' => '$company_id',
                        'company_name' => '$company.companydetails.company_name',
                        'userid' => '$company.companydetails.userid',
                        'admin_amount' => '$trans.admin_amount',
                        'company_amount' => '$trans.company_amount',
                        'transaction_id' => '$trans.transaction_id',
                        'passengers_log_id' => '$trans.passengers_log_id',
                        'payment_type' => '$trans.payment_type',
                        'createdate' => '$createdate',
                        'current_location' => '$current_location',
                        'drop_location' => '$drop_location',
                        'distance' => '$distance',
                        'nightfare' => '$trans.nightfare',
                        'eveningfare' => '$trans.eveningfare',
                        'used_wallet_amount' => '$used_wallet_amount',
                        'fare' => '$trans.fare',
                        'travel_status' => '$travel_status',
                        'driver_reply' => '$driver_reply',
                        'driver_comments' => '$driver_comments',
                        'distance_unit' => '$trans.distance_unit',
                        'rating' => '$rating',
                    )
                ),
                array(
                    '$sort' => array(
                        'id' => -1
                    )
                ),
            );
            $res    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
            if(!empty($res['result'])){
				foreach($res['result'] as $r){
					
					$temp_arr['driver_id'] = !empty($r['driver_id'])?$r['driver_id'][0]:'';
						$temp_arr['driver_name'] = !empty($r['driver_name'])?$r['driver_name'][0]:'';
						$temp_arr['driver_phone'] = !empty($r['driver_phone'])?$r['driver_phone'][0]:'';
						$temp_arr['passenger_name'] = !empty($r['passenger_name'])?$r['passenger_name'][0]:'';
						$temp_arr['email'] = !empty($r['passenger_email'])?$r['passenger_email'][0]:'';
						$temp_arr['passenger_phone'] = !empty($r['passenger_phone'])?$r['passenger_phone'][0]:'';
						$temp_arr['company_id'] = $r['company_id'];
						$temp_arr['company_name'] = !empty($r['company_name']) ? $r['company_name'][0]:'';
						$temp_arr['userid'] = !empty($r['userid'])?$r['userid'][0]:'';
						$temp_arr['admin_amount'] = !empty($r['admin_amount']) ? $r['admin_amount'][0]:'';
						$temp_arr['company_amount'] = !empty($r['company_amount']) ? $r['company_amount'][0]:'';
						$temp_arr['transaction_id'] = !empty($r['transaction_id']) ? $r['transaction_id'][0]:'';
						$temp_arr['passengers_log_id'] = isset($r['passengers_log_id'])?$r['passengers_log_id']:''	;
						$temp_arr['payment_type'] = !empty($r['payment_type']) ? $r['payment_type'][0]:'';
						$temp_arr['createdate'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']);
						$temp_arr['current_location'] = $r['current_location'];
						$temp_arr['drop_location'] = $r['drop_location'];
						$temp_arr['driver_comments'] = isset($r['driver_comments'])?$r['driver_comments']:'';
						$temp_arr['rating'] = isset($r['rating'])?$r['rating']:'';
						$temp_arr['driver_reply'] = $r['driver_reply'];
						$temp_arr['actual_pickup_time'] = isset($r['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['actual_pickup_time']) :'';
						$temp_arr['pickup_time'] = isset($r['pickup_time'])?commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
						$results[] = $temp_arr;
				}
			}
        } else {
            $match_query = array_merge($date_condition, $passengers_condition, $company_condition, $taxi_condition, $driver_condition, $condition, $pay_condition, $trans_condition);
            //print_r($match_query);exit;
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => "company_id",
                        'foreignField' => "_id",
                        'as' => "company"
                    )
                ),
                array(
                    '$unwind' => '$company'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_PEOPLE,
                        'localField' => "driver_id",
                        'foreignField' => "_id",
                        'as' => "people"
                    )
                ),
                array(
                    '$unwind' => '$people'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_PASSENGERS,
                        'localField' => "passengers_id",
                        'foreignField' => "_id",
                        'as' => "passengers"
                    )
                ),
                array(
                    '$unwind' => '$passengers'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_TRANSACTION,
                        'localField' => "_id",
                        'foreignField' => "passengers_log_id",
                        'as' => "trans"
                    )
                ),
                //array('$unwind' => '$trans'),
                array('$match' => $match_query),
                array(
                    '$project' => array(
                        '_id' => 0,
                        'id' => '$_id',
                        'driver_id' => '$people._id',
                        'driver_name' => '$people.name',
                        'driver_phone' => '$people.phone',
                        'passenger_name' => '$passengers.name',
                        'passenger_email' => '$passengers.email',
                        'passenger_phone' => '$passengers.phone',
                        'company_id' => '$company_id',
                        'company_name' => '$company.companydetails.company_name',
                        'userid' => '$company.companydetails.userid',
                        'admin_amount' => '$trans.admin_amount',
                        'company_amount' => '$trans.company_amount',
                        'transaction_id' => '$trans.transaction_id',
                        'passengers_log_id' => '$_id',
                        'payment_type' => '$trans.payment_type',
                        'createdate' => '$createdate',
                        'current_location' => '$current_location',
                        'drop_location' => '$drop_location',
                        'distance' => '$distance',
                        'nightfare' => '$trans.nightfare',
                        'eveningfare' => '$trans.eveningfare',
                        'used_wallet_amount' => '$used_wallet_amount',
                        'fare' => '$trans.fare',
                        'travel_status' => '$travel_status',
                        'driver_reply' => '$driver_reply',
                        'driver_comments' => '$driver_comments',
                        'distance_unit' => '$trans.distance_unit',
                        'comments' => '$comments',
                        'rating' => '$rating',
                        'company_tax' => '$company_tax',
                        'no_passengers' => '$no_passengers',
                        'trans_packtype' => '$trans.trans_packtype'
                    )
                ),
                array(
                    '$sort' => array(
                        'id' => -1
                    )
                ),
            );
            $res    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
            if(!empty($res['result'])){
				foreach($res['result'] as $r){
					$temp_arr['driver_id'] = $r['driver_id'];
					$temp_arr['driver_name'] = $r['driver_name'];
					$temp_arr['driver_phone'] = $r['driver_phone'];
					$temp_arr['passenger_name'] = $r['passenger_name'];
					$temp_arr['email'] = $r['passenger_email'];
					$temp_arr['passenger_phone'] = $r['passenger_phone'];
					$temp_arr['company_id'] = $r['company_id'];
					$temp_arr['company_name'] = $r['company_name'];
					$temp_arr['userid'] = $r['userid'];
					$temp_arr['admin_amount'] = !empty($r['admin_amount']) ? $r['admin_amount'][0]:'';
					$temp_arr['trans_packtype'] = !empty($r['trans_packtype']) ? $r['trans_packtype'][0]:'';
					$temp_arr['company_amount'] = !empty($r['company_amount']) ? $r['company_amount'][0]:'';
					$temp_arr['transaction_id'] = !empty($r['transaction_id']) ? $r['transaction_id'][0]:'';
					$temp_arr['passengers_log_id'] = !empty($r['passengers_log_id'])?$r['passengers_log_id'][0]:'';
					$temp_arr['payment_type'] = !empty($r['payment_type']) ? $r['payment_type'][0]:'';
					$temp_arr['createdate'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']);
					$temp_arr['current_location'] = $r['current_location'];
					$temp_arr['drop_location'] = $r['drop_location'];
					$temp_arr['distance'] = isset($r['distance']) ? $r['distance']:'';
					$temp_arr['nightfare'] = !empty($r['nightfare']) ?$r['nightfare'][0]:'';
					$temp_arr['eveningfare'] = !empty($r['eveningfare']) ? $r['eveningfare'][0]:'';
					$temp_arr['used_wallet_amount'] = isset($r['used_wallet_amount'])?$r['used_wallet_amount']:'';
					$temp_arr['fare'] = !empty($r['fare']) ? $r['fare'][0] :'';
					$temp_arr['travel_status'] = isset($r['travel_status'])?$r['travel_status']:'';
					$temp_arr['driver_reply'] = $r['driver_reply'];
					$temp_arr['driver_comments'] = isset($r['driver_comments'])?$r['driver_comments']:'';
					$temp_arr['distance_unit'] = !empty($r['distance_unit'])?$r['distance_unit'][0]:'';
					$temp_arr['payment_method'] = !empty($r['payment_method']) ? $r['payment_method']:'';
					$temp_arr['actual_pickup_time'] = isset($r['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['actual_pickup_time']) :'';
					$temp_arr['pickup_time'] = isset($r['pickup_time'])?commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
					$temp_arr['no_passengers'] = $r['no_passengers'];
					$temp_arr['rating'] = isset($r['rating'])?$r['rating']:'';
					$temp_arr['comments'] = isset($r['comments'])?$r['comments']:'';
					$temp_arr['company_tax'] = isset($r['company_tax'])?$r['company_tax']:'';
					$temp_arr['payment_gateway_name'] = isset($r['payment_gateway_name']) ? $r['payment_gateway_name']:'';
					$results[] = $temp_arr;
				}
			}			
        }
        $xls_output = "<table border='1' cellspacing='0' cellpadding='5'>";
		if($list != 'rejected') {
			$xls_output .= "<th>".__('sno')."</th>";
			$xls_output .= "<th>".__('cctransaction_id')."</th>";
			$xls_output .= "<th>".__('payment_type')."</th>";
		} 
		$xls_output .= "<th>".__('trip_id')."</th>";
		$xls_output .= "<th>".__('passenger_name')."</th>";
		$xls_output .= "<th>".ucfirst(__('driver_name'))."</th>";
		$xls_output .= "<th>".__('journey_date')."</th>";
		$xls_output .= "<th>".__('passenger_email')."</th>";
		$xls_output .= "<th>".__('Current_Location')."</th>";
		$xls_output .= "<th>".__('Drop_Location')."</th>";
		$xls_output .= "<th>".__('companyname')."</th>";
		if($list != 'rejected') { 
			$xls_output .= "<th>".__('admin_commision')."</th>";
			$xls_output .= "<th>".__('company_commision')."</th>";
		} 

		if($list != 'rejected' && $list != 'cancelled' ) { 
			$xls_output .= "<th>".__('waiting_time_with_format')."</th>";
			$xls_output .= "<th>".__('distance_km')."</th>";
			$xls_output .= "<th>".__('trip_total_fare')."</th>";
			//$xls_output .= "<th>".__('equivalent_to_usd').CURRENCY_FORMAT."</th>";
			$xls_output .= "<th>".__('nightfare')."</th>";
			$xls_output .= "<th>".__('eveningfare')."</th>";
		} 
		elseif($list == 'cancelled') { 
			$xls_output .= "<th>".__('cancel_fare').'('.CURRENCY.')'."</th>";
		} 
		else {	
			$xls_output .= "<th>".__('travel_status')."</th>";
			//$xls_output .= "<th>".__('reason')."</th>";
		} 
		$file = 'Export';
        $sno=0;
		$total_fare = 0;
        //echo '<pre>';print_r($results);exit;        
        if(!empty($results)){
			foreach($results as $result)
			{
				if($list != 'rejected') { 
					$paymentMod = ($result['payment_method'] == 'L') ? 'Live' : 'Test';
					if($result['distance'] != 0) { $distance = round($result['distance'],2); } else { $distance =  '--';  }
					if($result['fare'] != 0) { $fare =  round($result['fare'],2); } else { $fare =  '--'; }
					if($result['comments'] != '') { $comments =  $result['comments']; } else { $comments =  'No Comments'; }

				}
				else
				{
					if($result['driver_reply'] == 'C') { $driver_reply =  __('cancelled_by_driver'); } else { $driver_reply = __('rejected_by_driver'); }
					if($result['driver_comments'] == '') { $driver_comments = ''; } else { $driver_comments = $result['driver_comments']; }
				}	
				if(isset($result['company_amount'])){if($result['company_amount'] <= 0) { $company_amount =  '0'; } else { $company_amount = round($result['distance'],2);  } }
				if($result['rating'] == 0) { $ratings =  '-'; } else { $ratings =  $result['rating']; }
				
				$xls_output .= "<tr>"; 
				if($list != 'rejected') { 
					$xls_output .= "<td>".++$sno."</td>"; 
					$xls_output .= "<td>".ucfirst($result['transaction_id'])."</td>"; 
					 
					if($result['payment_type'] == 2 )
					{
						$xls_output .= "<td>Credit Card Using ".$result['payment_gateway_name']." ( ".$paymentMod." )</td>";
					}
					else if($result['payment_type'] == 3 )
					{
						$xls_output .= "<td> ".__('new_credit_card')." ( ".$paymentMod." )</td>";
					}
					else if($result['payment_type'] == 4) 
					{
						$xls_output .= "<td> ".__('account')." </td>";
					}
					else
					{
						$xls_output .= "<td>Cash</td>"; 	
					}
					
				}
				$xls_output .= "<td>".$result['passengers_log_id']."</td>";
				$journeyDate = ($result['actual_pickup_time'] != '0000-00-00 00:00:00') ? Commonfunction::getDateTimeFormat($result['actual_pickup_time'],1) : Commonfunction::getDateTimeFormat($result['pickup_time'],1);
				$xls_output .= "<td>".ucfirst($result['passenger_name'])."</td>"; 
				$xls_output .= "<td>".wordwrap(ucfirst($result['driver_name']),30,'<br/>',1)."</td>"; 
				$xls_output .= "<td>".$journeyDate."</td>"; 
				$xls_output .= "<td>".wordwrap($result['email'],50,'<br />',1)."</td>"; 
				$xls_output .= "<td>".strip_tags(htmlentities($result['current_location']))."</td>"; 
				$xls_output .= "<td>".strip_tags(htmlentities($result['drop_location']))."</td>"; 
				$xls_output .= "<td>".wordwrap($result['company_name'],25,'<br />',1)."</td>"; 
				if($list != 'rejected') { 
				$xls_output .= "<td>".$result['admin_amount']."</td>"; 
				$xls_output .= "<td>".$result['company_amount']."</td>"; 
				} 
				if($list != 'rejected' && $list != 'cancelled') { 
				//$waitingTime = (!empty($result['waitingtime'])) ? $result['waitingtime'].' Mins': '--';
				$waitingTime = '--';
				if(!empty($result['waiting_time'])) {
					$waitingTimeArr = explode(" ",$result['waiting_time']);
					$waitingTimeFormat = explode(":",$waitingTimeArr[0]);
					$waitingTime = (!isset($waitingTimeFormat[2])) ? '00:'.$waitingTimeArr[0] : $waitingTimeArr[0];
				}
				$xls_output .= "<td>".$waitingTime."</td>"; 
				$xls_output .= "<td>".$distance."</td>"; 
				$company_currency = $result['company_id'];
				//function to get company currency
				//$ccur = findcompany_currencyformat($company_currency);
				$ccur = CURRENCY_FORMAT;
				$xls_output .= "<td>".$ccur.' '.$fare."</td>";
				//function to convert currency
				$convet_amt = currency_conversion($ccur,$fare);
				$con_amt = round($convet_amt,2);
				//$xls_output .= "<td>".$con_amt."</td>";
				$nightfare = (!empty($result['nightfare'])) ? $ccur.' '.$result['nightfare']: '--';
				$eveningfare = (!empty($result['nightfare'])) ? $ccur.' '.$result['eveningfare']: '--';
				$xls_output .= "<td>".$nightfare."</td>";
				$xls_output .= "<td>".$eveningfare."</td>";
				}
				elseif($list == 'cancelled') { 
					$company_currency = $result['company_id'];
					//function to get company currency
					$ccur = findcompany_currencyformat($company_currency);
					$xls_output .= "<td>".$ccur.' '.$fare."</td>"; 
					//function to convert currency
					$convet_amt = currency_conversion($ccur,$fare);
				}
				else
				{
				$xls_output .= "<td>".$driver_reply."</td>"; 
				//$xls_output .= "<td>".$driver_comments."</td>"; 	
				} 
				$xls_output .= "</tr>"; 
				if($list != 'rejected') {
					$total_fare +=$convet_amt;
				}
			}
			
			if($list != 'rejected' && count($results) > 0) {
				$colspan = ($list == 'cancelled') ? '12' : '15';
				$xls_output .= "<tr><td colspan='$colspan' align='right'>".__('trip_total_fare')."</td><td>".CURRENCY_FORMAT." $total_fare</td></tr>";
			}
			$xls_output .= "</table>";

			$filename = $file."_".date("Y-m-d_H-i",time());
			header("Content-Disposition: attachment; filename=".$filename.".xls");
			echo $xls_output; 
			exit;
		}
    }
    /**
     * ****export_data()****
     *@export user listings as pdf
     */
    public function export_data_pdf($list, $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id, $payment_type)
    {
		//echo $list.'--'. $company.'--'. $manager_id.'--'. $taxiid.'--'. $driver_id.'--'. $passengerid.'--'. $startdate.'--'. $enddate.'--'. $transaction_id.'--'. $payment_type;exit;
        $usertype        = $this->user_admin_type;
        //echo '<pre>';
        // Condition to search based on taxi and driver for user type "Managers" //
        if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
            // Function to get taxi details //
            $taxilist = $this->gettaxidetails($company, $manager_id);
            if (count($taxilist) > 0) {
                //$taxi_id  = "";
                foreach ($taxilist as $key => $taxis) {
                    $tid[] = isset($taxis["taxi_id"]) ? $taxis["taxi_id"] : $taxis["_id"];
                    //$tid[] = isset($taxis["taxi_id"])?$taxis["taxi_id"]:$taxis["_id"];
                    //$taxi_id .= $tid . ',';
                }
                //$taxi_ids = substr($taxi_id, 0, strlen($taxi_id) - 1);
                $taxi_ids = Commonfunction::mongo_format_array($tid);
            } else {
                //$taxi_ids = "";
                $taxi_ids = array();
            }
            //echo 'taxilist=>';print_r($taxi_ids);echo '<br>';
            // Function to get driver details //
            $driverlist = $this->getdriverdetails($company, $manager_id);
            if (count($driverlist) > 0) {
                //$cdriver_id = "";
                foreach ($driverlist as $key => $drivers) {
                    $cdriver_id[] = $drivers["id"];
                    //$cdriver_id .= $drivers["id"] . ',';
                }
                //$driver_ids = substr($cdriver_id, 0, strlen($cdriver_id) - 1);
                $driver_ids = Commonfunction::mongo_format_array($cdriver_id);
            } else {
                //$driver_ids = "";
                $driver_ids = array();
            }
            //echo 'driverlist=>';print_r($driver_ids);echo '<br>';
        }
        // Function to get passenger details //
        $passengerlist = $this->getpassengerdetails($company, $manager_id);
        // Condition to search based on passengers //
        if (count($passengerlist) > 0) {
            //$cpassenger_id = "";
            foreach ($passengerlist as $key => $passengers) {
                $cpassenger_id[] = $passengers["id"];
                //$cpassenger_id .= $passengers["id"] . ',';
            }
            //$passenger_ids = substr($cpassenger_id, 0, strlen($cpassenger_id) - 1);
            $passenger_ids = Commonfunction::mongo_format_array($cpassenger_id);
        } else {
            //$passenger_ids = "";
            $passenger_ids = array();
        }
        //echo 'passengerlist=>';print_r($passenger_ids);echo '<br>';//exit;
        $date_condition = array();
        if ($startdate != "") {
            $date_condition = array(
                'createdate' => array(
                    '$gte' => Commonfunction::MongoDate(strtotime($startdate)),
                    '$lte' => Commonfunction::MongoDate(strtotime($enddate))
                )
            );
        }
        // Condition to search based on transaction id //
        $trans_condition = array();
        if ($transaction_id != '') {
            $trans_condition = array(
                'trans.transaction_id' => $transaction_id
            );
        }
        // Condition to search based on status //
        $condition = array();
        if ($list == 'all') {
            $condition = array();
        } else if ($list == 'success') {
            $condition = array(
                'travel_status' => 1,
                'driver_reply' => 'A'
            );
        } else if ($list == 'cancelled') {
            $condition = array(
                "\$or" => array(
                    array(
                        'travel_status' => 4,
                        'driver_reply' => 'A'
                    ),
                    array(
                        'travel_status' => 9,
                        'driver_reply' => 'C'
                    )
                )
            );
        } else if ($list == 'rejected') {
            $condition = array(
                //'driver_reply' => 'R'
                'reject.rejection_type' => 1
            );
        }
        $pay_condition = array();
        // Condition to search based on payment type //
        if ($payment_type != 'All' && $payment_type != '') {
            if ($list != 'rejected') {
                $pay_condition = array(
                    'trans.payment_type' => (int) $payment_type
                );
            }
        }
        // Condition to search based on company //
        $company_condition = array();
        if (($company != "") && ($company != "All")) {
            $company_condition = array(
                'company_id' => (int) $company
            );
        }
        // Condition to search based on taxi id //
        $taxi_condition = array();
        if (($taxiid != "All") && !empty($taxiid)) {
            $taxi_condition = array(
                'taxi_id' => (int) $taxiid
            );
        } else {
            if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
                if (count($taxilist) > 0) {
                    $taxi_condition = array(
                        'taxi_id' => array(
                            '$in' => $taxi_ids
                        )
                    );
                }
            }
        }
        // Condition to search based on driver id //
        $driver_condition = array();
        if (($driver_id != "All") && !empty($driver_id)) {
            $driver_condition = array(
                "driver_id" => (int) $driver_id
            );
        } else {
            if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
                if (count($driverlist) > 0) {
                    $driver_condition = array(
                        "driver_id" => array(
                            '$in' => $driver_ids
                        )
                    );
                }
            }
        }
        // Condition to search based on passenger id //
        $passengers_condition = array();
        if (($passengerid != "") && ($passengerid != "All")) {
            $passengers_condition = array(
                "passengers_id" => (int) $passengerid
            );
        }
        if ($list == 'rejected') {
            $match_query = array_merge($date_condition, $passengers_condition, $company_condition, $taxi_condition, $driver_condition, $condition, $pay_condition);
            //print_r($match_query);exit;
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => "company_id",
                        'foreignField' => "_id",
                        'as' => "company"
                    )
                ),
                //array('$unwind' => '$company'),
                array(
                    '$lookup' => array(
                        'from' => MDB_PEOPLE,
                        'localField' => "driver_id",
                        'foreignField' => "_id",
                        'as' => "people"
                    )
                ),
                //array('$unwind' => '$people'),
                array(
                    '$lookup' => array(
                        'from' => MDB_PASSENGERS,
                        'localField' => "passengers_id",
                        'foreignField' => "_id",
                        'as' => "passengers"
                    )
                ),
                //array('$unwind' => '$passengers'),
                array(
                    '$lookup' => array(
                        'from' => MDB_REJECTION_HISTORY,
                        'localField' => "_id",
                        'foreignField' => "passengers_log_id",
                        'as' => "reject"
                    )
                ),
                array('$match' => $match_query),
                array(
                    '$project' => array(
                        '_id' => 0,
                        'id' => '$_id',
                        'driver_id' => '$people._id',
                        'driver_name' => '$people.name',
                        'driver_phone' => '$people.phone',
                        'passenger_name' => '$passengers.name',
                        'email' => '$passengers.email',
                        'passenger_phone' => '$passengers.phone',
                        'company_id' => '$company_id',
                        'company_name' => '$company.companydetails.company_name',
                        'userid' => '$company.companydetails.userid',
                        'admin_amount' => '$trans.admin_amount',
                        'company_amount' => '$trans.company_amount',
                        'transaction_id' => '$trans.transaction_id',
                        'passengers_log_id' => '$trans.passengers_log_id',
                        'payment_type' => '$trans.payment_type',
                        'createdate' => '$createdate',
                        'current_location' => '$current_location',
                        'drop_location' => '$drop_location',
                        'distance' => '$distance',
                        'nightfare' => '$trans.nightfare',
                        'eveningfare' => '$trans.eveningfare',
                        'used_wallet_amount' => '$used_wallet_amount',
                        'fare' => '$trans.fare',
                        'travel_status' => '$travel_status',
                        'driver_reply' => '$driver_reply',
                        'driver_comments' => '$driver_comments',
                        'distance_unit' => '$trans.distance_unit',
                        'rating' => '$rating',
                    )
                ),
                array(
                    '$sort' => array(
                        'id' => -1
                    )
                ),
            );
            $res  = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
            if(!empty($res['result'])){
					foreach($res['result'] as $r){
						
						$temp_arr['driver_id'] = !empty($r['driver_id'])?$r['driver_id'][0]:'';
						$temp_arr['driver_name'] = !empty($r['driver_name'])?$r['driver_name'][0]:'';
						$temp_arr['driver_phone'] = !empty($r['driver_phone'])?$r['driver_phone'][0]:'';
						$temp_arr['passenger_name'] = !empty($r['passenger_name'])?$r['passenger_name'][0]:'';
						$temp_arr['passenger_email'] = !empty($r['passenger_email'])?$r['passenger_email'][0]:'';
						$temp_arr['passenger_phone'] = !empty($r['passenger_phone'])?$r['passenger_phone'][0]:'';
						$temp_arr['company_id'] = $r['company_id'];
						$temp_arr['company_name'] = !empty($r['company_name']) ? $r['company_name'][0]:'';
						$temp_arr['userid'] = !empty($r['userid'])?$r['userid'][0]:'';
						$temp_arr['admin_amount'] = !empty($r['admin_amount']) ? $r['admin_amount'][0]:'';
						$temp_arr['company_amount'] = !empty($r['company_amount']) ? $r['company_amount'][0]:'';
						$temp_arr['transaction_id'] = !empty($r['transaction_id']) ? $r['transaction_id'][0]:'';
						$temp_arr['passengers_log_id'] = isset($r['passengers_log_id'])?$r['passengers_log_id']:'';
						$temp_arr['payment_type'] = !empty($r['payment_type']) ? $r['payment_type'][0]:'';
						$temp_arr['createdate'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']);
						$temp_arr['current_location'] = $r['current_location'];
						$temp_arr['drop_location'] = $r['drop_location'];
						$temp_arr['driver_comments'] = isset($r['driver_comments'])?$r['driver_comments']:'';
						$temp_arr['rating'] = isset($r['rating'])?$r['rating']:'';
						$temp_arr['driver_reply'] = $r['driver_reply'];
						$temp_arr['actual_pickup_time'] = isset($r['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['actual_pickup_time']) :'';
						$temp_arr['pickup_time'] = isset($r['pickup_time'])?commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
						$result[] = $temp_arr;
					}
				}
				//echo "<pre>"; 	print_r($result);exit;
				return $result;
        } else {
            $match_query = array_merge($date_condition, $passengers_condition, $company_condition, $taxi_condition, $driver_condition, $condition, $pay_condition, $trans_condition);
            //print_r($match_query);exit;
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => "company_id",
                        'foreignField' => "_id",
                        'as' => "company"
                    )
                ),
                array(
                    '$unwind' => '$company'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_PEOPLE,
                        'localField' => "driver_id",
                        'foreignField' => "_id",
                        'as' => "people"
                    )
                ),
                array(
                    '$unwind' => '$people'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_PASSENGERS,
                        'localField' => "passengers_id",
                        'foreignField' => "_id",
                        'as' => "passengers"
                    )
                ),
                array(
                    '$unwind' => '$passengers'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_TRANSACTION,
                        'localField' => "_id",
                        'foreignField' => "passengers_log_id",
                        'as' => "trans"
                    )
                ),
                //array('$unwind' => '$trans'),
                array(
                    '$match' => $match_query
                ),
                array(
                    '$project' => array(
                        
                        'passengers_log_id' => '$_id',
                        'driver_id' => '$people._id',
                        'driver_name' => '$people.name',
                        'driver_phone' => '$people.phone',
                        'passenger_name' => '$passengers.name',
                        'passenger_email' => '$passengers.email',
                        'passenger_phone' => '$passengers.phone',
                        'company_id' => '$company_id',
                        'company_name' => '$company.companydetails.company_name',
                        'userid' => '$company.companydetails.userid',
                        'admin_amount' => '$trans.admin_amount',
                        'company_amount' => '$trans.company_amount',
                        'transaction_id' => '$trans.transaction_id',
                        'passengers_log_id' => '$trans.passengers_log_id',
                        'payment_type' => '$trans.payment_type',
                        'createdate' => '$createdate',
                        'current_location' => '$current_location',
                        'drop_location' => '$drop_location',
                        'distance' => '$distance',
                        'nightfare' => '$trans.nightfare',
                        'eveningfare' => '$trans.eveningfare',
                        'used_wallet_amount' => '$used_wallet_amount',
                        'fare' => '$trans.fare',
                        'travel_status' => '$travel_status',
                        'driver_reply' => '$driver_reply',
                        'driver_comments' => '$driver_comments',
                        'distance_unit' => '$trans.distance_unit',
                        'comments' => '$comments',
                        'rating' => '$rating',
                        'company_tax' => '$company_tax',
                        'no_passengers' => '$no_passengers',
                        'trans_packtype' => '$trans.trans_packtype'
                    )
                ),
                array(
                    '$sort' => array(
                        'id' => -1
                    )
                ),
            );
            $res   = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
            //echo '<pre>else';print_r($res);exit;
            if(!empty($res['result'])){
				foreach($res['result'] as $r){
					
					$temp_arr['driver_id'] = $r['driver_id'];
					$temp_arr['driver_name'] = $r['driver_name'];
					$temp_arr['driver_phone'] = $r['driver_phone'];
					$temp_arr['passenger_name'] = $r['passenger_name'];
					$temp_arr['passenger_email'] = $r['passenger_email'];
					$temp_arr['passenger_phone'] = $r['passenger_phone'];
					$temp_arr['company_id'] = $r['company_id'];
					$temp_arr['company_name'] = $r['company_name'];
					$temp_arr['userid'] = $r['userid'];
					$temp_arr['admin_amount'] = !empty($r['admin_amount']) ? $r['admin_amount'][0]:'';
					$temp_arr['trans_packtype'] = !empty($r['trans_packtype']) ? $r['trans_packtype'][0]:'';
					$temp_arr['company_amount'] = !empty($r['company_amount']) ? $r['company_amount'][0]:'';
					$temp_arr['transaction_id'] = !empty($r['transaction_id']) ? $r['transaction_id'][0]:'';
					$temp_arr['passengers_log_id'] = !empty($r['passengers_log_id'])?$r['passengers_log_id'][0]:'';
					$temp_arr['payment_type'] = !empty($r['payment_type']) ? $r['payment_type'][0]:'';
					$temp_arr['createdate'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']);
					$temp_arr['current_location'] = $r['current_location'];
					$temp_arr['drop_location'] = $r['drop_location'];
					$temp_arr['distance'] = isset($r['distance']) ? $r['distance']:'';
					$temp_arr['nightfare'] = !empty($r['nightfare']) ?$r['nightfare'][0]:'';
					$temp_arr['eveningfare'] = !empty($r['eveningfare']) ? $r['eveningfare'][0]:'';
					$temp_arr['used_wallet_amount'] = isset($r['used_wallet_amount'])?$r['used_wallet_amount']:'';
					$temp_arr['fare'] = !empty($r['fare']) ? $r['fare'][0] :'';
					$temp_arr['travel_status'] = isset($r['travel_status'])?$r['travel_status']:'';
					$temp_arr['driver_reply'] = $r['driver_reply'];
					$temp_arr['driver_comments'] = isset($r['driver_comments'])?$r['driver_comments']:'';
					$temp_arr['distance_unit'] = !empty($r['distance_unit'])?$r['distance_unit'][0]:'';
					$temp_arr['payment_method'] = !empty($r['payment_method']) ? $r['payment_method']:'';
					$temp_arr['actual_pickup_time'] = isset($r['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['actual_pickup_time']) :'';
					$temp_arr['pickup_time'] = isset($r['pickup_time'])?commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
					$temp_arr['no_passengers'] = $r['no_passengers'];
					$temp_arr['rating'] = isset($r['rating'])?$r['rating']:'';
					$temp_arr['comments'] = isset($r['comments'])?$r['comments']:'';
					$temp_arr['company_tax'] = isset($r['company_tax'])?$r['company_tax']:'';
					$temp_arr['payment_gateway_name'] = isset($r['payment_gateway_name']) ? $r['payment_gateway_name']:'';
					$result[] = $temp_arr;
				}
			}
			//echo '<pre>else';print_r($result);exit;                
			return $result;
        }
    }
    /********* Graph ****************/
    public function getgraphvalues($list,$company,$manager_id,$taxiid,$driver_id,$passengerid,$startdate,$enddate,$transaction_id,$payment_type,$payment_mode='',$passenger_ids='',$taxi_ids = "",$driver_ids = "")
    {
		$result = $temp_arr = array();
        $usertype = $this->user_admin_type;
        // Condition to search based on taxi and driver for user type "Managers" //
        if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
            
            if ($taxi_ids !='') {
                $tid = explode(',',$taxi_ids);                
                $taxi_ids = Commonfunction::mongo_format_array($tid);
            } else {
                
                $taxi_ids = array();
            }
            
            if ($driver_ids !='') {
                $cdriver_id = explode(',',$driver_ids);                
                $driver_ids = Commonfunction::mongo_format_array($cdriver_id);
            } else {
                $driver_ids = array();
            }
        }
        
        // Condition to search based on passengers //
        if ($passenger_ids !='') {
			$cpassenger_id = explode(',',$passenger_ids);                              
            $passenger_ids = Commonfunction::mongo_format_array($cpassenger_id);
        } else {
            $passenger_ids = array();
        }
        
        $date_condition = array();
        if ($startdate != "") {
            $date_condition = array(
                'createdate' => array(
                    '$gte' => Commonfunction::MongoDate(strtotime($startdate)),
                    '$lte' => Commonfunction::MongoDate(strtotime($enddate))
                )
            );
        }
        // Condition to search based on transaction id //
        $trans_condition = array();
        if ($transaction_id != '') {
            $trans_condition = array(
                'trans.transaction_id' => $transaction_id
            );
        }
        // Condition to search based on status //
        $condition = array();
        if ($list == 'all') {
            $condition = array();
        } else if ($list == 'success') {
            $condition = array(
                'travel_status' => 1,
                'driver_reply' => 'A'
            );
        } else if ($list == 'cancelled') {
            $condition = array(
                "\$or" => array(
                    array(
                        'travel_status' => 4,
                        'driver_reply' => 'A'
                    ),
                    array(
						'travel_status' => 8
                    ),
                    array(
                        'travel_status' => 9,
                        'driver_reply' => 'C'
                    )
                )
            );
        } else if ($list == 'rejected') {
            $condition = array(
                'driver_reply' => 'R'
            );
        }else if ($list == 'pendingpayment') {
            $condition = array(
                'travel_status' => 5,
                'driver_reply' => 'A'
            );
        }
        $pay_condition = array();
        // Condition to search based on payment type //
        if ($payment_type != 'All' && $payment_type != '') {
            if ($list != 'rejected') {
                $pay_condition = array(
                    'trans.payment_type' => (int) $payment_type
                );
            }
        }
        // Condition to search based on company //
        $company_condition = array();
        if (($company != "") && ($company != "All")) {
            $company_condition = array(
                'company_id' => (int) $company
            );
        }
        // Condition to search based on taxi id //
        $taxi_condition = array();
        if (($taxiid != "All") && !empty($taxiid)) {
            $taxi_condition = array(
                'taxi_id' => (int) $taxiid
            );
        } else {
            if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
                if (!empty($taxi_ids)) {
                    $taxi_condition = array(
                        'taxi_id' => array(
                            '$in' => $taxi_ids
                        )
                    );
                }
            }
        }
        // Condition to search based on driver id //
        $driver_condition = array();
        if (($driver_id != "All") && !empty($driver_id)) {
            $driver_condition = array(
                "driver_id" => (int) $driver_id
            );
        } else {
            if ((($manager_id != '') && ($manager_id != 'All')) || ($usertype == 'M')) {
                if (!empty($driver_ids)) {
                    $driver_condition = array(
                        "driver_id" => array(
                            '$in' => $driver_ids
                        )
                    );
                }
            }
        }
        // Condition to search based on passenger id //
        $passengers_condition = array();
        if (($passengerid != "") && ($passengerid != "All")) {
            $passengers_condition = array(
                "passengers_id" => (int) $passengerid
            );
        }
        $match_query = array_merge($date_condition, $passengers_condition, $company_condition, $taxi_condition, $driver_condition, $condition, $pay_condition, $trans_condition);
        //print_r($match_query);exit;        
        $arguments   = array(
            array(
                '$lookup' => array(
                    'from' => MDB_TRANSACTION,
                    'localField' => "_id",
                    'foreignField' => "passengers_log_id",
                    'as' => "trans"
                )
            ),
            array('$unwind' => array('path' => '$trans','preserveNullAndEmptyArrays' => true)),
            array(
                '$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => "company_id",
                    'foreignField' => "_id",
                    'as' => "company"
                )
            ),
            array('$unwind' => array('path' => '$company','preserveNullAndEmptyArrays' => true)),
            array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => "driver_id",
                    'foreignField' => "_id",
                    'as' => "people"
                )
            ),
            array('$unwind' => array('path' => '$people','preserveNullAndEmptyArrays' => true)),
            array(
                '$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => "passengers_id",
                    'foreignField' => "_id",
                    'as' => "passengers"
                )
            ),
            array('$unwind' => array('path' => '$passengers','preserveNullAndEmptyArrays' => true)),
            array('$match' => $match_query),
            array('$project' => array(
                    'createdate'=>array('$dateToString'=>array('format' => '%Y-%m-%d', 'date'=> '$createdate')),
                    'pickup_time'=>array('$dateToString'=>array('format' => '%Y-%m-%d %H:%M:%S', 'date'=> '$pickup_time')),
                    'travel_status'=>array('$cond'=>array('if'=>array('$eq'=>array('$travel_status',4)),
                                                          'then'=>'$trans.fare','else'=>0)),
                    'travel_status1'=>array('$cond'=>array('if'=>array('$ne'=>array('$travel_status',4)),
                                                          'then'=>'$trans.fare','else'=>0)),
                    'fare' => '$trans.fare')),
            array('$group' =>array('_id' =>array('createdate' => '$createdate'),
                                   'amount' => array('$sum' => '$fare'),
                                   'cancelled_amount' => array('$sum' => '$travel_status'),
                                   'completed_amount' => array('$sum' => '$travel_status1'),
                                   'pickup_time' => array('$addToSet' => '$pickup_time')
                                   )),
            array('$sort' => array('_id' => 1))
        );
        $res      = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
        //echo '<pre>';print_r($res);exit;
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr['createdate'] = $r['_id']['createdate'];
				$temp_arr['amount'] = $r['amount'];
				$temp_arr['cancelled_amount'] = $r['cancelled_amount'];
				$temp_arr['completed_amount'] = $r['completed_amount'];
				$temp_arr['pending_amount'] = $r['completed_amount'];
				$temp_arr['pickup_time'] = !empty($r['pickup_time'])?$r['pickup_time'][0]:'';
				$result[] = $temp_arr;
			}
		}
		//echo '<pre>';print_r($result);exit;
        return $result;
    }
    public function viewtransaction_details($log_id)
    {
		$result = array();
        $match_query = array(
            '_id' => (int)$log_id
        );
        //print_r($match_query);exit;
        $arguments   = array(
            array(
                '$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => "company_id",
                    'foreignField' => "_id",
                    'as' => "company"
                )
            ),
            array('$unwind' => array('path' => '$company','preserveNullAndEmptyArrays' => true)),
            array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => "driver_id",
                    'foreignField' => "_id",
                    'as' => "people"
                )
            ),
            array('$unwind' => array('path' => '$people','preserveNullAndEmptyArrays' => true)),
            array(
                '$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => "passengers_id",
                    'foreignField' => "_id",
                    'as' => "passengers"
                )
            ),
            array('$unwind' => array('path' => '$passengers','preserveNullAndEmptyArrays' => true)),
            array(
                '$lookup' => array(
                    'from' => MDB_LOCATION_HISTORY,
                    'localField' => "_id",
                    'foreignField' => "trip_id",
                    'as' => "driver_location"
                )
            ),            
            array(
                '$lookup' => array(
                    'from' => MDB_TRANSACTION,
                    'localField' => "_id",
                    'foreignField' => "passengers_log_id",
                    'as' => "trans"
                )
            ),
            //array('$unwind' => '$trans'),
			array('$unwind' => array('path' => '$trans','preserveNullAndEmptyArrays' => true)),
			array(
                '$lookup' => array(
                    'from' => MDB_HELP,
                    'localField' => "trans.help_id",
                    'foreignField' => "_id",
                    'as' => "help"
                )
            ),
            array('$unwind' => array('path' => '$help','preserveNullAndEmptyArrays' => true)),
            array(
                '$match' => $match_query
            ),
            array(
                '$project' => array(
                    '_id' => 0,
                    'id' => '$_id',
                    'driver_id' => '$people._id',
                    'driver_name' => '$people.name',
                    'driver_phone' => '$people.phone',
                    'passenger_name' => '$passengers.name',
                    'passenger_email' => '$passengers.email',
                    'passenger_phone' => '$passengers.phone',
                    'company_id' => '$company_id',
                    'company_name' => '$company.companydetails.company_name',
                    'userid' => '$company.companydetails.userid',
                    'createdate' => '$createdate',
                    'current_location' => '$current_location',
                    'drop_location' => '$drop_location',
                    'distance' => '$distance',
                    'used_wallet_amount' => '$used_wallet_amount',
                    'fare' => '$trans.fare',
                    'travel_status' => '$travel_status',
                    'driver_reply' => '$driver_reply',
                    'driver_comments' => '$driver_comments',
                    'comments' => '$comments',
                    'rating' => '$rating',
                    'org_tax' => '$company_tax',
                    'actual_pickup_time' => '$actual_pickup_time',
                    'drop_time' => '$drop_time',
                    'drop_latitude' => '$drop_latitude',
                    'drop_longitude' => '$drop_longitude',
					'company_tax' => '$trans.company_tax',
					'admin_amount' => '$trans.admin_amount',
                    'company_amount' => '$trans.company_amount',
                    'transaction_id' => '$trans.transaction_id',
                    'passengers_log_id' => '$trans.passengers_log_id',
                    'payment_type' => '$trans.payment_type',
                    'nightfare' => '$trans.nightfare',
                    'eveningfare' => '$trans.eveningfare',
                    'distance_unit' => '$trans.distance_unit',
                    'tripfare' => '$trans.tripfare',
                    'minutes_fare' => '$trans.minutes_fare',
                    'trip_minutes' => '$trans.trip_minutes',
                    'taxi_waiting_time' => '$trans.waiting_time',
                    'taxi_waiting_cost' => '$trans.waiting_cost',
                    'nightfare_applicable' => '$trans.nightfare_applicable',
                    'eveningfare_applicable' => '$trans.eveningfare_applicable',
                    'amt' => '$trans.amt',
                    'passenger_app_version' => '$passenger_app_version',
                    'driver_app_version' => '$driver_app_version',
                    'active_record' => '$driver_location.loc.coordinates',
                    'help_content' => '$help.help_content',
                    'help_comment' => '$trans.help_comment',
                    'help_comment_date' => '$trans.help_comment_date',
                )
            ),
            array(
                '$sort' => array(
                    '_id' => 1
                )
            )
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);

        if(!empty($res['result'])){
			
			$r = $res['result'][0];
			
			$temp_arr['id'] = $r['id'];
			$temp_arr['driver_id'] = isset($r['driver_id']) ? $r['driver_id'] : 0;
			$temp_arr['driver_name'] = isset($r['driver_name']) ? $r['driver_name'] : "";
			$temp_arr['driver_phone'] = isset($r['driver_phone']) ? $r['driver_phone'] : "";
			$temp_arr['passenger_name'] = isset($r['passenger_name']) ? $r['passenger_name'] : "";
			$temp_arr['passenger_email'] = isset($r['passenger_email']) ? $r['passenger_email'] : "";
			$temp_arr['passenger_phone'] = isset($r['passenger_phone']) ? $r['passenger_phone'] : "";
			$temp_arr['company_id'] = isset($r['company_id']) ? $r['company_id'] : "";
			$temp_arr['company_name'] = isset($r['company_name']) ? $r['company_name'] : "";
			$temp_arr['userid'] = isset($r['userid']) ? $r['userid'] : "";
			$temp_arr['current_location'] = isset($r['current_location']) ? $r['current_location'] : "";
			$temp_arr['drop_location'] = isset($r['drop_location']) ? $r['drop_location'] : "";
			$temp_arr['distance'] = isset($r['distance']) ? $r['distance']:'-';
			$temp_arr['createdate'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']);
			$temp_arr['used_wallet_amount'] = isset($r['used_wallet_amount'])?$r['used_wallet_amount']:'';
			$temp_arr['payment_status'] = $temp_arr['travel_status'] = isset($r['travel_status']) ? $r['travel_status'] : "";
			$temp_arr['driver_reply'] = isset($r['driver_reply']) ? $r['driver_reply'] : "";
			$temp_arr['driver_comments'] = isset($r['driver_comments'])?urldecode($r['driver_comments']):'';
			$temp_arr['comments'] = isset($r['comments'])?$r['comments']:'';
			$temp_arr['rating'] = isset($r['rating'])?$r['rating']:'';
			$temp_arr['org_tax'] = isset($r['org_tax'])?$r['org_tax']:0;
			$temp_arr['company_tax'] = isset($r['company_tax'])?$r['company_tax']:0;
			$temp_arr['actual_pickup_time'] = isset($r['actual_pickup_time'])?commonfunction::convertphpdate('Y-m-d H:i:s',$r['actual_pickup_time']):'';
			$temp_arr['drop_time'] = isset($r['drop_time'])?commonfunction::convertphpdate('Y-m-d H:i:s',$r['drop_time']):'';
			$temp_arr['drop_latitude'] = isset($r['drop_latitude']) ? $r['drop_latitude'] : "";
			$temp_arr['drop_longitude'] = isset($r['drop_longitude']) ? $r['drop_longitude'] : "";
			$temp_arr['tripfare'] = isset($r['tripfare'])?$r['tripfare']:'';
			$temp_arr['minutes_fare'] = isset($r['minutes_fare'])?$r['minutes_fare']:'';
			$temp_arr['trip_minutes'] = isset($r['trip_minutes'])?$r['trip_minutes']:'';
			$temp_arr['taxi_waiting_time'] = isset($r['taxi_waiting_time'])?$r['taxi_waiting_time']:'';
			$temp_arr['taxi_waiting_cost'] = isset($r['taxi_waiting_cost'])?$r['taxi_waiting_cost']:'';
			$temp_arr['passenger_app_version'] = isset($r['passenger_app_version'])?$r['passenger_app_version']:'';
			$temp_arr['driver_app_version'] = isset($r['driver_app_version'])?$r['driver_app_version']:'';			
			$active_record = isset($r['active_record'][0])?$r['active_record'][0]:array();
			
			$temp_arr['admin_amount'] = isset($r['admin_amount'])?$r['admin_amount']:'';
			$temp_arr['company_amount'] = isset($r['company_amount'])?$r['company_amount']:'';
			$temp_arr['transaction_id'] = isset($r['transaction_id']) ?$r['transaction_id']:'';
			$temp_arr['passengers_log_id'] = isset($r['passengers_log_id'])?$r['passengers_log_id']:'';
			$temp_arr['payment_type'] = isset($r['payment_type'])?$r['payment_type']:'';
			$temp_arr['nightfare'] = isset($r['nightfare'])?$r['nightfare']:'';
			$temp_arr['eveningfare'] = isset($r['eveningfare'])?$r['eveningfare']:'';			
			$temp_arr['fare'] = isset($r['fare'])?$r['fare']:'';			
			$temp_arr['distance_unit'] = isset($r['distance_unit'])?$r['distance_unit']:'';			
			$temp_arr['nightfare_applicable'] = isset($r['nightfare_applicable'])?$r['nightfare_applicable']:'';
			$temp_arr['eveningfare_applicable'] = isset($r['eveningfare_applicable'])?$r['eveningfare_applicable']:'';
			$temp_arr['amt'] = isset($r['amt'])?$r['amt']:'';	
			$temp_arr['help_content'] = isset($r['help_content'])?$r['help_content']:'';
			$temp_arr['help_comment'] = isset($r['help_comment'])?$r['help_comment']:'';
			$temp_arr['help_comment_date'] = isset($r['help_comment_date'])?commonfunction::convertphpdate('Y-m-d H:i:s',$r['help_comment_date']):'';					
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
		//echo '<pre>';print_r($res);exit;
        return $result;
    }
    public function viewdriver_tracking($trip_id)
    {
        //MongoDB
        $arguments = array(
            array('$match'	=> array("trip_id" => (int)$trip_id)),
            array(
                '$project' => array('_id'=>0,
                    'id' => '$_id',
                    'active_record' => '$loc.coordinates',
                )
            )
        );
        $result = $this->mongo_db->aggregate(MDB_LOCATION_HISTORY,$arguments);
        //echo "<pre>"; print_r($result); exit;
        return (!empty($result['result']))?$result['result']:array();
    }
    
    public function accountreport_details($list, $company, $startdate, $enddate, $payment_type)
    {
        $result = $temp_arr = $match = array();
        $usertype        = $this->user_admin_type;
		$match['travel_status'] = 1;
        $match['driver_reply'] = 'A';
        if (($company != "") && ($company != "All") && $company != 0) {
            $match['company_id'] = (int)$company;
        }
       
        //$startdate = '2015-05-01 00:00:00';
        //$enddate = '2015-05-31 12:59:59';
        if ($startdate != "") {
			 $match['actual_pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($startdate)),
                                        '$lte' => Commonfunction::MongoDate(strtotime($enddate)));
        }
        if ($payment_type != 'All' && $payment_type != '') {
            $match['trans.payment_type'] = (int)$payment_type;
        }
        $args = array(
                array('$lookup' => array(
                    'from' => MDB_TRANSACTION,
                    'localField' => '_id',
                    'foreignField' => 'passengers_log_id',
                    'as' => 'trans',
                )),
                //array('$unwind' => '$trans'),
                array('$unwind' => array('path' => '$trans','preserveNullAndEmptyArrays' => true)),
                array('$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => 'company_id',
                    'foreignField' => '_id',
                    'as' => 'company',
                )),
                //array('$unwind' => '$company'),
                /*array('$unwind' => array('path' => '$company','preserveNullAndEmptyArrays' => true)),
                array('$lookup' => array(
                    'from' => MDB_PAYMENT_GATEWAYS,
                    'localField' => 'company._id',
                    'foreignField' => 'company_id',
                    'as' => 'payment',
                )),*/
                array('$unwind' => array('path' => '$payment','preserveNullAndEmptyArrays' => true)),
                array('$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'driver_id',
                    'foreignField' => '_id',
                    'as' => 'people',
                )),
                //array('$unwind' => '$people'),                
                array('$unwind' => array('path' => '$people','preserveNullAndEmptyArrays' => true)),
                array('$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => 'passengers_id',
                    'foreignField' => '_id',
                    'as' => 'passengers',
                )),
                //array('$unwind' => '$passengers'),
                array('$unwind' => array('path' => '$passengers','preserveNullAndEmptyArrays' => true)),
                array('$match' => $match),
                array('$project' => array(
                    'passengers_log_id' => '$_id',
                    'driver_name' => '$people.name',
                    'driver_phone' => '$people.phone',
                    'passenger_name' => '$passengers.name',
                    'passenger_email' => '$passengers.email',
                    'passenger_phone' => '$passengers.phone',
                    'total_amount' => array('$add' =>  array( '$trans.fare', '$used_wallet_amount')),
                    'fare' => '$trans.fare',
                    'used_wallet_amount' => '$used_wallet_amount',
                    //'cash_payment' =>  array('$add' =>  array( '$transfare', '$pass_logs.used_wallet_amount')
                    'cid' => '$company_id',
                    /*'currency_code' => '$payment.currency_code',
                    'currency_symbol' => '$payment.currency_symbol'*/
                )),
				array('$group' => array(
                    //'_id' => array('passengers_log_id' => '$passengers_log_id','day' => '$day','month' => '$month','year' => '$year'),
                    '_id' => array('passengers_log_id' => '$passengers_log_id'),
                    'details' => array('$first' => array(
						'passengers_log_id' => '$passengers_log_id',
						'driver_name' => '$driver_name',
						'driver_phone' => '$driver_phone',
						'passenger_name' => '$passenger_name',
						'passenger_email' => '$passenger_email',
						'passenger_phone' => '$passenger_phone',
						'fare' => '$fare',
						'used_wallet_amount' => '$used_wallet_amount',
						'cid' => '$cid',
						/*'currency_code' => '$currency_code',
						'currency_symbol' => '$currency_symbol',*/
						'total'=> '$total_amount'
                    ))
                )),
                array('$sort' => array('_id.passengers_log_id' => -1)),
            );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args); 
        
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$r = $r['details'];
				$temp_arr['cid'] = $r['cid'];
				$temp_arr['passenger_name'] = isset($r['passenger_name'])?$r['passenger_name']:"";
				$temp_arr['passenger_email'] = isset($r['passenger_email'])?$r['passenger_email']:"";
				$temp_arr['passenger_phone'] = isset($r['passenger_phone'])?$r['passenger_phone']:"";
				$temp_arr['driver_name'] = isset($r['driver_name'])?$r['driver_name']:"";
				$temp_arr['driver_phone'] = isset($r['driver_phone'])?$r['driver_phone']:"";
				$temp_arr['fare'] = isset($r['fare'])?$r['fare']:0;
				$temp_arr['used_wallet_amount'] = isset($r['used_wallet_amount'])?$r['used_wallet_amount']:0;
				//$temp_arr['currency_code'] = !empty($r['currency_code'])?$r['currency_code'][0]:'';
				//$temp_arr['currency_symbol'] = !empty($r['currency_symbol'])?$r['currency_symbol'][0]:'';
				$temp_arr['total'] = !empty($r['total'])?$r['total'][0]:'';
				$result[] = $temp_arr;
			}
		}
                
        return $result;
    } 
    
     public function accountreport_details_payment($company_id = 0,$start_date,$end_date,$payment_type)
	{	
		# Total Amount	
		$total_amount = 0;
		$date_match = array('$gte' => Commonfunction::MongoDate(strtotime($start_date)), '$lte' => Commonfunction::MongoDate(strtotime($end_date)));
		$total_match = array('current_date' => $date_match);
		if(!empty($payment_type) && $payment_type != 0) {
			$total_match['payment_type'] = (int)$payment_type;
		}
		if (($company_id != "") && ($company_id != "All") && $company_id != 0) {
            $total_match['pass_logs.company_id'] = (int)$company_id;
        }
		$args = array(
					array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,
					'localField' => 'passengers_log_id','foreignField' => '_id','as' => 'pass_logs')),
					array('$unwind' =>  array('path' =>  '$pass_logs', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$match' => $total_match),
					array('$project' =>  array('total' =>  array('$add' => array( '$fare', '$pass_logs.used_wallet_amount')))),
					array('$group' =>  array('_id' => null,
					'total_amount' =>  array('$sum' => '$total')))
				);
		$total_amount_sql = $this->mongo_db->Aggregate(MDB_TRANSACTION,$args);
		if(!empty($total_amount_sql['result'])){			
			$total_amount = $total_amount_sql['result'][0]['total_amount'];
		}
		return $total_amount;
	}
	
	public function get_company_commission_amount($company_id = 0,$start_date,$end_date,$payment_type)
	{
		# Commision Amount
		$commision_amount = 0;
		$date_match = array('$gte' => Commonfunction::MongoDate(strtotime($start_date)), '$lte' => Commonfunction::MongoDate(strtotime($end_date)));
		$commission_match = array('current_date' => $date_match);
		if(!empty($payment_type) && $payment_type != 0) {
			$commission_match['payment_type'] = (int)$payment_type;
		}
		if (($company_id != "") && ($company_id != "All") && $company_id != 0) {
			$commission_match['pass_log.company_id'] = (int)$company_id;
			$commission_args = array(
								array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,'localField' => 'passengers_log_id',
													'foreignField' => '_id','as' => 'pass_log')),
								array('$match' => $commission_match),
								array('$group' => array('_id' => null,
								'commision_amount' => array('$sum' => '$company_amount')
								))
							);	
		} else {			
			$commission_args = array(
						array('$match' => $commission_match),
						array('$group' => array('_id' => null,'commision_amount' => array('$sum' => '$admin_amount')))
					);				
		}
		//echo "<pre>"; print_r($commission_match); exit;
		$commision_sql = $this->mongo_db->Aggregate(MDB_TRANSACTION,$commission_args);
		if(!empty($commision_sql['result'])){
			$commision_amount = round($commision_sql['result'][0]['commision_amount'],2);
		}
		return $commision_amount;
	}
	
	public function get_company_revenue_amount($company_id = 0,$start_date,$end_date,$payment_type)
	{
		# Total Amount	
		$commision_amount = 0;
		$date_match = array('$gte' => Commonfunction::MongoDate(strtotime($start_date)), '$lte' => Commonfunction::MongoDate(strtotime($end_date)));		
		$total_match = array('current_date' => $date_match);
		if($company_id > 0) {
			$total_match['pass_logs.company_id'] = (int)$company_id;
		}
		if(!empty($payment_type) && $payment_type != 0) {
			$total_match['payment_type'] = (int)$payment_type;
		}
		$args = array(
			array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,
			'localField' => 'passengers_log_id','foreignField' => '_id','as' => 'pass_logs')),
			array('$unwind' =>  array('path' =>  '$pass_logs', 'preserveNullAndEmptyArrays' =>  true ) ),
			array('$match' => $total_match),
			array('$project' =>  array('total' =>  array('$add' => array( '$fare', '$pass_logs.used_wallet_amount')))),
			array('$group' =>  array('_id' => null,
			'total_amount' =>  array('$sum' => '$total')))
		);
		$total_amount_sql = $this->mongo_db->Aggregate(MDB_TRANSACTION,$args);
		if(!empty($total_amount_sql['result'])){			
			$total_amount = $total_amount_sql['result'][0]['total_amount'];
		}
		return $result["total_amount"] = $total_amount;		
	}
	
    //public function getaccountreportvalues($list,$company,$manager_id,$taxiid,$driver_id,$passengerid,$startdate,$enddate,$transaction_id,$payment_type)
    public function getaccountreportvalues($list, $company, $startdate, $enddate, $payment_type)
    {
        $result = $temp_arr = $match = array();
        $usertype        = $this->user_admin_type;
        $match['travel_status'] = 1;
        $match['driver_reply'] = 'A';
        if (($company != "") && ($company != "All") && $company != 0) {
            $match['company_id'] = (int)$company;
        }
        //$startdate = '2015-03-01 00:00:00';
        //$enddate = '2015-03-31 00:00:00';
        if ($startdate != "") {
           $match['actual_pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($startdate)),
                                        '$lte' => Commonfunction::MongoDate(strtotime($enddate)));
        }
        if ($payment_type != 'All' && $payment_type != '') {
            $match['trans.payment_type'] = (int)$payment_type;
        }
        $args = array(
                array('$lookup' => array(
                    'from' => MDB_TRANSACTION,
                    'localField' => '_id',
                    'foreignField' => 'passengers_log_id',
                    'as' => 'trans',
                )),
				array('$unwind' => array('path' => '$trans','preserveNullAndEmptyArrays' => true)),
				array('$match' => $match),
                array('$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => 'company_id',
                    'foreignField' => '_id',
                    'as' => 'company',
                )),
                array('$unwind' => array('path' => '$company','preserveNullAndEmptyArrays' => true)),
                array('$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'driver_id',
                    'foreignField' => '_id',
                    'as' => 'people',
                )),       
                array('$unwind' => array('path' => '$people','preserveNullAndEmptyArrays' => true)),
                array('$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => 'passengers_id',
                    'foreignField' => '_id',
                    'as' => 'passengers',
                )),
                array('$unwind' => array('path' => '$passengers','preserveNullAndEmptyArrays' => true)),
                //array('$match' => $match),
                array('$project' => array(
					'passengers_log_id' => '$_id',
                    'fare' => '$trans.fare',
                    'actual_pickup_time'=>array('$dateToString'=>array('format' => '%Y-%m-%d', 'date'=> '$actual_pickup_time')),
                    'year' => array('$year' => '$actual_pickup_time'),
                    'month' => array('$month' => '$actual_pickup_time'),
                    'day' => array('$dayOfMonth' => '$actual_pickup_time')
                )),
                array('$group' => array(
                    '_id' => array('day' => '$day','month' => '$month','year' => '$year'),
                    'amount' => array('$sum' => '$fare'),
                    'trips' =>  array('$sum' => 1),
                    'createdate' =>  array('$addToSet' => '$actual_pickup_time')
                )),
                array('$sort' => array('createdate' => -1)),
            );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr['amount'] = $r['amount'];
				$temp_arr['trips'] = $r['trips'];
				$temp_arr['createdate'] = $r['createdate'][0];
				$result[] = $temp_arr;
			}
		}
        //echo '<pre>';print_r($result);exit;
        return $result;
    }
    
    public function close_mysql_connection($instance)
    {
        $db = Database::$instances[$instance];
        //print_r($db);
        $db->disconnect();
    }
    // PHP strtotime compatible strings
    public function dateDiff($time1, $time2, $precision = 6)
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
    
    public function total_braintree_transaction_details($keyword = "", $start_date = "", $end_date = "", $company_id = "", $filter_company = ""){
		
		$count = $this->braintree_transaction_details($keyword, $start_date, $end_date, $company_id, $filter_company,'','',true);
		return $count;
	}
    public function braintree_transaction_details($keyword = "", $start_date = "", $end_date = "", $company_id = "", $filter_company = "", $val = "", $offset = "",$find_count=false)
    {
        $date_condition = array();
        if ($start_date != "") {
            $date_condition = array(
                'createdate' => array(
                    '$gte' => Commonfunction::MongoDate(strtotime($start_date)),
                    '$lte' => Commonfunction::MongoDate(strtotime($end_date))
                )
            );
        }
        $company_condition = array();
        if ($filter_company != '' && $filter_company != 'All') {
            $company_condition = array(
                'company_id' => (int)$filter_company
            );
        }
        if($company_id!=''){
			$company_condition = array(
                'company_id' => (int)$company_id
            );
        }
        $keyword_condition = array();
        if($keyword!=''){
            $keyword_condition = array("\$or"=>array(array( 'trans.transaction_id' => (string)$keyword) , array( 'trans.passengers_log_id' => (int)$keyword )));
        }
        $search_query = array(
            'trans.payment_gateway_id' => 2,
            'trans.payment_type'=>array('$in'=>array(2,3))
        );
        $match_query = array_merge($search_query,$company_condition,$date_condition,$keyword_condition);
        //echo '<pre>';print_r($match_query);exit;
        $common_arguments   = array(
            array(
                '$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => "company_id",
                    'foreignField' => "_id",
                    'as' => "company"
                )
            ),
            array(
                '$unwind' => '$company'
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => "driver_id",
                    'foreignField' => "_id",
                    'as' => "people"
                )
            ),
            array(
                '$unwind' => '$people'
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => "passengers_id",
                    'foreignField' => "_id",
                    'as' => "passengers"
                )
            ),
            array(
                '$unwind' => '$passengers'
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_TRANSACTION,
                    'localField' => "_id",
                    'foreignField' => "passengers_log_id",
                    'as' => "trans"
                )
            ),
            array('$unwind' => '$trans'),
            array( '$match' => $match_query )
        );
        if($find_count){
            $arguments = array(
                array(
                    '$project' => array(
                        '_id' => 0,
                        'id' => '$_id'
                    )
                ),
                array(
                    '$group' => array(
                        '_id' => 0,
                        'count' => array(
                            '$sum' => 1
                        )
                    )
                ),
                array(
                    '$sort' => array(
                        '_id' => 1
                    )
                )
            );
            $merge_arguments = array_merge($common_arguments,$arguments);
            //echo '<pre>';print_r($merge_arguments);//exit;
            $result      = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);
            //echo '<pre>';print_r($result);exit;
            return (!empty($result['result'][0])) ? $result['result'][0]['count'] : 0;
        } else{
            $arguments = array(
                array(
                '$unwind' => '$split_details'
            ),
                array(
                    '$project' => array(
                        '_id' => 0,
                        'id' => '$_id',
                        'company_name' => '$company.companydetails.company_name',
                        'transaction_id'=>'$trans.transaction_id',
                        //'payment_status' => '$trans.payment_status',
                        'payment_status' => '$split_details.settlement_status',
                        'amt' => '$trans.amt',
                        'trip_id' => '$trans.passengers_log_id',
                        'createdate' => '$createdate',
                        'company_id' => '$company_id',
                    )
                ),
                array(
                    '$sort' => array(
                        '_id' => 1
                    )
                ),
                array(
                    '$skip' => (int)$offset
                ),
                array(
                    '$limit' => (int)$val
                )                
            );
            $merge_arguments = array_merge($common_arguments,$arguments);
            $result      = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);
            //echo '<pre>';print_r($merge_arguments);exit;
            return (!empty($result['result'])) ? $result['result'] : array();
        }
    }
    public function update_settlement_status($transaction_array = array(), $company_id = "")
    {
       /* if ($company_id > 0) {
            $paypal_details           = $this->common_model->company_payment_details($company_id);
            $payment_gateway_username = isset($paypal_details[0]['payment_gateway_username']) ? $paypal_details[0]['payment_gateway_username'] : "";
            $payment_gateway_password = isset($paypal_details[0]['payment_gateway_password']) ? $paypal_details[0]['payment_gateway_password'] : "";
            $payment_gateway_key      = isset($paypal_details[0]['payment_gateway_key']) ? $paypal_details[0]['payment_gateway_key'] : "";
            $currency_format          = isset($paypal_details[0]['gateway_currency_format']) ? $paypal_details[0]['gateway_currency_format'] : "";
            $payment_method           = isset($paypal_details[0]['payment_method']) ? $paypal_details[0]['payment_method'] : "";
            $payment_types            = isset($paypal_details[0]['payment_type']) ? $paypal_details[0]['payment_type'] : "";
            $street                   = COMPANY_STREET_ADDR;
            $city                     = COMPANY_LOGIN_CITY_NAME;
            $state                    = COMPANY_LOGIN_STATE_NAME;
            $country_code             = COMPANY_LOGIN_ISO_COUNTRYCODE;
            $currency_code            = COMPANY_CURRENCY_FORMAT;
            $zipcode                  = COMPANY_ZIPCODE;
        } else {*/
            /*$paypal_details           = $this->common_model->payment_gateway_details();
            $payment_gateway_username = isset($paypal_details[0]['payment_gateway_username']) ? $paypal_details[0]['payment_gateway_username'] : "";
            $payment_gateway_password = isset($paypal_details[0]['payment_gateway_password']) ? $paypal_details[0]['payment_gateway_password'] : "";
            $payment_gateway_key      = isset($paypal_details[0]['payment_gateway_key']) ? $paypal_details[0]['payment_gateway_key'] : "";
            $currency_format          = isset($paypal_details[0]['gateway_currency_format']) ? $paypal_details[0]['gateway_currency_format'] : "";
            $payment_method           = isset($paypal_details[0]['payment_method']) ? $paypal_details[0]['payment_method'] : "";
            $payment_types            = isset($paypal_details[0]['payment_type']) ? $paypal_details[0]['payment_type'] : "";
            $payment_gateway            = isset($paypal_details[0]['gateway_name']) ? $paypal_details[0]['gateway_name'] : "";
        /*}*/
        /*if(!empty($payment_gateway) && ($payment_gateway=='Brain tree' || $payment_gateway=='Braintree')){
            /** Brain Tree payment gateway **/
           /* $product_title  = Html::chars('Complete Trip');
            $payment_action = 'sale';
            require_once(APPPATH . 'vendor/braintree-payment/lib/Braintree.php');
            $pay_type = ($payment_method == "L") ? "live" : "sandbox";
            if ($pay_type == "live") {
                Braintree_Configuration::environment('production');
            } else {
                Braintree_Configuration::environment('sandbox');
            }
            Braintree_Configuration::merchantId($payment_gateway_username); //your_merchant_id
            Braintree_Configuration::publicKey($payment_gateway_password); //your_public_key
            Braintree_Configuration::privateKey($payment_gateway_key); //your_private_key
            //~ foreach ($transaction_array as $key => $val) {
                //~ $trans          = explode(":", $val);
                //~ $transaction_id = $trans[0];
                //~ $trip_id        = $trans[1];
                //~ $transaction    = Braintree_Transaction::find($transaction_id);
                //~ //echo json_encode($transaction);echo "<br/>";
                //~ if (isset($transaction->_attributes)) {
                    //~ $result = $transaction->_attributes;
                    //~ /*$this->common_model->update_table(TRANS, array(
                        //~ 'payment_status' => str_replace('_', ' ', $result['status'])
                    //~ ), 'passengers_log_id', $trip_id);*/
                    //~ 
                    //~ $update_array = array('payment_status' => str_replace('_', ' ', $result['status']));
                    //~ $result = $this->mongo_db->updateOne(MDB_TRANSACTION,array('passengers_log_id'=>(int)$trip_id),array('$set'=>$update_array),array('upsert'=>false));
                //~ }
            //~ }
        
         // Payment gateway settlement transaction
        
                   /* if (class_exists('Paymentgateway')) {
                        $paymentresponse = Paymentgateway::payment_gateway_connect('settlement', $preTransactId,$preTransactAmount);
                        $payment_status = $paymentresponse['payment_status'];
                        $transaction_status='';
                        $transaction_id='';
                        if($payment_status==1)
                        {
                            $transaction_status = $paymentresponse['transaction_status'];
                            $transaction_id = $paymentresponse['TRANSACTIONID'];
                        }
                    } else {
                        trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
                    }
            return 1;*/
            return 0;
        }
        
    
    public function getaddress($lat, $lng)
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
    
    public function export_transaction_details($list,$company,$manager_id,$taxiid,$driverid,$passengerid,$startdate,$enddate,$offset='',$val='',$transaction_id,$payment_type, $payment_mode="", $passenger_ids='',$taxi_ids = "",$driver_ids = "",$trip_id="")
   	{
		$result = $this->transaction_details($list,$company,$manager_id,$taxiid,$driverid,$passengerid,$startdate,$enddate,$offset,$val,$transaction_id,$payment_type, $payment_mode="", $passenger_ids='',$taxi_ids = "",$driver_ids = "",$trip_id="");
		
		return $result;
	}
	
	
	public function update_transaction($tran_array, $trip_id)
	{
		$stsupdate = $this->mongo_db->updateOne(MDB_TRANSACTION,array('passengers_log_id'=>(int)$trip_id),array('$set' => $tran_array),array('upsert' => false));
		return $stsupdate;
	}
}
