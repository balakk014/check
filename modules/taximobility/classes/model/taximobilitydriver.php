<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Driver module details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityDriver extends Model
{
    /**
     ****__construct()**
     *** Common Function in this model
     */
    public function __construct()
    {
		
        $this->session = Session::instance();	
		$this->name = $this->session->get("name");
		$this->admin_userid = $this->session->get("passenger_id");
		$this->admin_email = $this->session->get("email");
		$this->user_admin_type = $this->session->get("user_type");
		$this->Commonmodel = $Commonmodel = Model::factory('Commonmodel');
		
		//$this->currentdate=Commonfunction::getCurrentTimeStamp();

        $this->lat='';
        $this->lon='';
        if( isset($_SESSION['id']) && ($_SESSION['id']!='') )
        { 
                $this->lat=isset($_SESSION['ip_lati'])?$_SESSION['ip_lati']:LOCATION_LATI;  
                $this->lon=isset($_SESSION['ip_lng'])?$_SESSION['ip_lng']:LOCATION_LONG;                 
        }
        else{ 
                $this->lat=isset($_COOKIE['c_lati'])?$_COOKIE['c_lati']:LOCATION_LATI;    
                $this->lon=isset($_COOKIE['c_lng'])?$_COOKIE['c_lng']:LOCATION_LONG;    
         }
        //MongoDB Instance
        $this->mongo_db = MangoDB::instance('default');
    }
    /**Validating Login Datas**/
    public function validate_login($arr)
    {
        if ($arr['password'] == 'Password') {
            $arr['password'] = "";
        }
        return Validation::factory($arr)->rule('email', 'not_empty')->rule('email', 'email')->rule('password', 'not_empty')->rule('password', 'min_length', array(
            ':value',
            '5'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ));
    }
    /** Validate Profile Settings **/
    public function validate_driver_profilesettings($arr)
    {
        return Validation::factory($arr)->rule('name', 'not_empty')
        //->rule('name','illegal_chars',array(':value','/^[\p{L}-.,_; \'0-9]*$/u'))
            ->rule('name', 'min_length', array(
            ':value',
            '4'
        ))->rule('name', 'max_length', array(
            ':value',
            '32'
        ))->rule('phone', 'not_empty')->rule('phone', 'numeric') //num
            ->rule('address', 'not_empty');
        //->rule('description','illegal_chars',array(':value','/^[\p{L}-.,_; \'0-9]*$/u'));
    }
	// Updating User Details
	public function update_driverimage($image,$userid)
	{
		$this->currentdate=Commonfunction::getCurrentTimeStamp();
		//$query = $this->mongo_db->find(MDB_PEOPLE,array('_id' => (int)$userid),array('profile_picture'));
		## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
		$options=[
			'projection'=>[
				'profile_picture'=>1              
			]
		];
		$query = $this->mongo_db->find(MDB_PEOPLE,array('_id' => (int)$userid),$options);
		$result = $query;
		
		if(isset($result[0]['profile_picture']) && $result[0]['profile_picture']!=""){
			$id1 = SITE_DRIVER_IMGPATH.$result[0]['profile_picture'];
			$id2 = SITE_DRIVER_IMGPATH.'thumb_'.$result[0]['profile_picture'];
			if(file_exists($id1) && file_exists($id2)){
				unlink($id1);
				unlink($id2);
			}
		}
		$mdate = $this->currentdate;
		$query = array();
		$query['updated_date'] = $mdate;
		if(isset($image)){
			$query[ 'profile_picture' ]=$image;
		}
		$result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$userid),array('$set'=>$query),array('upsert'=>true));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
	}
    public function get_driver_profile_details($id = "")
    {
        $result = $this->mongo_db->findOne(MDB_PEOPLE, array(
            '_id' => (int) $id
        ));
        return (!empty($result)) ? $result : array();
    }
    // Validating Forgot Password Details
    public function validate_forgotpwd($arr)
    {
        return Validation::factory($arr)->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '100'
        ))->rule('email', 'not_empty');
    }
    public function check_phone_passengers($phone = "")
    {
        $match_query=array();
		$match_query['phone'] = $phone;
        if (COMPANY_CID != 0) {
			$match_query['company_id'] = (int)COMPANY_CID;
        }
		$result = $this->mongo_db->count(MDB_PEOPLE,$match_query);
        return (!empty($result)) ? $result : 0 ;
    }
    public function forgot_password_phone($array_data, $value, $random_key)
    {
        $mdate  = Commonfunction::MongoDate(strtotime($this->currentdate));
        $pass   = md5($random_key);
        $pwd_arr = array('password' => $pass, 
						//'org_password' => $random_key,
						'updated_date'=>$mdate);
        $update = $this->mongo_db->updateOne(MDB_PEOPLE,array('phone'=>$value['phone_no']),
                                          array('$set'=>$pwd_arr),array('upsert'=>true));
        $result = (empty($update->getwriteErrors())) ? 1 :0;
        if ($result) {
            $project = array('name', 'email', 'password', 'phone');
            $res = $this->mongo_db->findOne(MDB_PEOPLE,array('phone'=>$value['phone_no'],'status'=>'A'),$project);
            return (isset($res)) ? $res : array();
        } else {
            return 0;
        }
    }
    
    
    // Validating Change Password Details
    public function validate_changepwd($arr)
    {
        return Validation::factory($arr)->rule('old_password', 'not_empty')->rule('old_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('old_password', 'max_length', array(
            ':value',
            '16'
        ))->rule('new_password', 'not_empty')->rule('new_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('new_password', 'min_length', array(
            ':value',
            '5'
        ))->rule('new_password', 'max_length', array(
            ':value',
            '16'
        ))->rule('confirm_password', 'not_empty')->rule('confirm_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('confirm_password', 'matches', array(
            ':validation',
            'new_password',
            'confirm_password'
        ))->rule('confirm_password', 'min_length', array(
            ':value',
            '5'
        ))->rule('confirm_password', 'max_length', array(
            ':value',
            '16'
        ));
    }
    /**Validating Reset Password Details **/
    public function validate_resetpwd($arr)
    {
        return Validation::factory($arr)->rule('new_password', 'not_empty')
        //->rule('new_password','alpha_dash')
            ->rule('new_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('new_password', 'max_length', array(
            ':value',
            '16'
        ))->rule('conf_password', 'not_empty')
        //->rule('conf_password','alpha_dash')
            
        //->rule('conf_password', array(':equals','new_password'))
            ->rule('conf_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('conf_password', 'max_length', array(
            ':value',
            '16'
        ));
    }
    /**
     * Validation rule for fields in email
     */
    public function validate_email($arr)
    {
        return Validation::factory($arr)->rule('email', 'not_empty')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('email', 'Model_Authorize::check_label_not_empty', array(
            ":value",
            __('enter_email')
        ))->rule('email', 'email_domain')->rule('email', 'Model_Authorize::unique_email');
    }
    public function get_my_profile_details($id)
    {
        $result = array();
        $res = $this->mongo_db->findOne(MDB_PEOPLE,
            array(
                '_id' => (int)$id
            ),
            array(
                'name',
                'notification_setting',
                'company_id'
            )
        );
        (!empty($res)) ? $result[] = $res : '';
        return $result;
    }
    
    public function get_my_trips($id)
    {
		$result = $temp_arr = array();
        $args = array(
					array('$lookup' => array('from' => MDB_LOCATION_HISTORY,'localField' => 'driver_id',
						'foreignField' => 'driver_id','as' => 'drloc')),
					array('$unwind' =>  array( 'path' =>  '$drloc', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$project' => array('driver_id' => '$driver_id',
										'trip_id' => '$drloc.trip_id',
										'status' => '$drloc.status',
										'travel_status' => '$travel_status',
										'active_record' => '$drloc.loc.coordinates',
										'current_location' => '$current_location',
										'drop_location' => '$drop_location',
										'cmp_value' =>  array('$eq' =>  array('$_id', '$drloc.trip_id')))),
					array('$match' => array('cmp_value' => true,'driver_id' => (int)$id,'status' => 'A','travel_status' => 1)),
					array('$project' => array('_id' => 0,'passengers_log_id' => '$_id','active_record' => '$active_record',
						'current_location' => '$current_location','drop_location' => '$drop_location'))
				);
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
        
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr['current_location'] = $r['current_location'];
				$temp_arr['drop_location'] = $r['drop_location'];
				$active_record = $r['active_record'];
				$coordinates='';
				if(!empty($active_record)){
					foreach($active_record as $a){
						$lat = '['.$a[1].',';
						$long = $a[0].'],';
						$coordinates .= $lat.$long;
					}
				}
				$temp_arr['active_record'] = $coordinates;
				$result[] = $temp_arr;
			}
			//echo '<pre>';print_r($result);exit;
		}
        return $result;
    }
    
    //Function used to get the get_driver_logs $driver_id,'R','A','1',COMPANY_CID);
    public function get_driver_logs($id, $msg_status, $driver_reply = null, $travel_status = null, $company_id)
    {
        if ($company_id == '') {
            $current_time = date('Y-m-d H:i:s');
            $start_time   = date('Y-m-d') . ' 00:00:01';
            $end_time     = date('Y-m-d') . ' 23:59:59';
        } else {
            $time_arguments = array(array('$match'=>array('_id'=>(int)$company_id)),
									array('$unwind'=>'$companydetails'),
									array('$project'=>array('time_zone'=>'$companydetails.time_zone'))
								);            
            $time = $this->mongo_db->aggregate(MDB_COMPANY,$time_arguments); 
			$timezone_fetch = $time['result'];
            if ($timezone_fetch[0]['time_zone'] != '') {
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
        $match_query = array("\$and" => array(array("pickup_time"=>array('$gte' => Commonfunction::MongoDate(strtotime($start_time)))),array('driver_id' => (int)$id),array('travel_status' => (int)$travel_status),array('msg_status' => $msg_status),array('driver_reply' => $driver_reply) ));
        //print_r($match_query);//exit;
        $arguments = array(
            array('$match'	=> $match_query),
            array('$lookup' 		=> array(
                    'from'			=>	MDB_PASSENGERS,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "passengers"
                )
            ),
            array('$unwind'=>'$passengers'),
            array(
                '$project' => array(
                    'name' => '$passengers.name',
                    'current_location' => '$current_location',
                    'drop_location' => '$drop_location',
                    'no_passengers' => '$no_passengers',
                    'pickup_time' => '$pickup_time',
                )
            ),
            array(
                '$sort' => array('_id' => -1 )
            )
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        //echo "<pre>"; print_r($result); exit;
        return (!empty($result['result']))?$result['result']:array();
    }
    //Function used to find the completed logs
    public function get_driver_logs_completed($id, $msg_status, $driver_reply = null, $travel_status = null, $start = null, $limit = null)
    {
        $match_query = array('driver_id' => (int)$id, 'travel_status' => (int)$travel_status, 'msg_status' => $msg_status, 'driver_reply' => $driver_reply);
        //print_r($match_query);//exit;
        $arguments = array(
            array('$match'	=> $match_query),
            array('$lookup' 		=> array(
                    'from'			=>	MDB_PASSENGERS,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "passengers"
                )
            ),
            array('$unwind'=>'$passengers'),
            array(
                '$project' => array(
                    'name' => '$passengers.name',
                    'current_location' => '$current_location',
                    'drop_location' => '$drop_location',
                    'no_passengers' => '$no_passengers',
                    'pickup_time' => '$pickup_time',
                )
            ),
            array(
                '$sort' => array('_id' => -1 )
            ),
            array(
                '$skip' => (int)$limit
            ),
            array(
              '$limit' => (int)$start
            )
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        //echo "<pre>"; print_r($result); exit;
        return (!empty($result['result']))?$result['result']:array();
    }
    //Function used to get all driver logs with transactions
    public function get_taxi_logs_completed_transaction($id, $msg_status, $driver_reply = null, $travel_status = null, $limit = null, $offset = null)
    {
		$result = $temp_arr = array();
        $match_query = array('taxi_id' => (int)$id, 'msg_status' => $msg_status, 'driver_reply' => $driver_reply, 'travel_status' => (int)$travel_status );
        $arguments = array(
            array('$lookup' 		=> array(
                    'from'			=>	MDB_PASSENGERS,
                    'localField'	=> 'passengers_id',
                    'foreignField'	=> "_id",
                    'as'			=> "passengers"
                )
            ),
            array('$unwind' => '$passengers'),
            array('$lookup' 		=> array(
                    'from'			=>	MDB_TRANSACTION,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "trans"
                )
            ),
            array('$unwind' => '$trans'),
            array('$match'	=> $match_query),
            array(
                '$project' => array('_id'=>0,
                    'id' => '$_id',
                    'distance' => '$distance',
                    'distance_unit'=>'$trans.distance_unit',
                    'fare'=>'$trans.fare',
                    'name'=>'$passengers.name',
                    'current_location'=>'$current_location',
                    'drop_location'=>'$drop_location',
                    'no_passengers'=>'$no_passengers',
                    'pickup_time' => '$pickup_time',
                )
            ),
            array(
                '$sort' => array(
                    '_id' => -1
                ),
            )            
        );
        if($limit != null){
			$arguments[]['$skip'] = (int)$offset;
			$arguments[]['$limit'] = (int)$limit;
		}
        
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr['distance'] = isset($r['distance']) ? $r['distance']:'';
				$temp_arr['distance_unit'] = isset($r['distance_unit']) ? $r['distance_unit']:'';
				$temp_arr['fare'] = $r['fare'];
				$temp_arr['name'] = $r['name'];
				$temp_arr['current_location'] = $r['current_location'];
				$temp_arr['drop_location'] = $r['drop_location'];
				$temp_arr['no_passengers'] = $r['no_passengers'];
				$temp_arr['pickup_time'] = !empty($r['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'' ;
				$result[] = (object)$temp_arr;
			}
		}
		//echo "<pre>"; print_r($result); exit;
        return $result;
    }
    //Function used to get the get_driver_logs
    public function get_driver_logs1($id, $msg_status, $driver_reply = null, $travel_status = null, $limit, $start,$find_count=false)
    {
		$result = array();
        $match_query = array('driver_id' => (int)$id, 'msg_status' => $msg_status, 'driver_reply' => $driver_reply, 'travel_status' => (int)$travel_status );
        $arguments = array(
            array('$match'	=> $match_query),
            array('$lookup' 		=> array(
                    'from'			=>	MDB_PASSENGERS,
                    'localField'	=> 'passengers_id',
                    'foreignField'	=> "_id",
                    'as'			=> "passengers"
                )
            ),
            array('$unwind' => '$passengers'),
            array(
                '$sort' => array(
                    '_id' => -1
                ),
            ),
        );
        
		$carguments = array(
			//array('$match' => array('rating' => array('$gt'=>0))),
			array('$match' => array('comments' => array('$ne'=>null))),
			array(
				'$project' => array('_id'=>0,
					'id' => '$_id',
					'rating' => '$rating',
					'comments' => '$comments',
					'profile_image' => '$passengers.profile_image',
					'name'=>'$passengers.name',
					'createdate'=>'$createdate',
				)
			)
		);
		if($find_count == false){
			$carguments[]['$skip'] = (int)$start;
			$carguments[]['$limit'] = (int)$limit;
		}
		$merge_arguments = array_merge($arguments,$carguments);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$merge_arguments);
		//echo '<pre>';print_r($res);exit;
		if(!empty($res['result'])){
			foreach($res['result'] as $r) {
				//$temp_arr['id'] = isset($r['id']) ? $r['id']:'';
                                $temp_arr['id'] = $id;
				$temp_arr['passengers_log_id'] = isset($r['id']) ? $r['id']:'';
				$temp_arr['rating'] = isset($r['rating']) ? $r['rating']:0;
				$temp_arr['comments'] = isset($r['comments']) ? $r['comments']:'';
				$temp_arr['profile_image'] = isset($r['profile_image']) ? $r['profile_image']:'';
				$temp_arr['name'] = isset($r['name']) ? $r['name']:'';
				$temp_arr['createdate'] = isset($r['createdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s', $r['createdate']):'';
				$result[] = (object)$temp_arr;
			};
		}
		//echo '<pre>';print_r($result);exit;
		return $result;
    }
    public function get_passenger_log_details($passengerlog_id = "")
    {
		$arguments = array(
			array('$lookup' =>
				array(
                    'from' => MDB_COMPANY,
                    'localField' => "company_id",
                    'foreignField' => "_id",
                    'as' => "company"
                )
            ),
            array('$unwind' => '$company'),
			
			array(
                '$lookup' =>
				array(
					'from'=>MDB_PASSENGERS,
					'localField'=> "passengers_id",
					'foreignField' => "_id",
					'as'=> "passengers"
				)
            ),
            array('$unwind'=>'$passengers'),
			
			array(
                '$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'driver_id',
					'foreignField' => "_id",
					'as' => "people"
				)
			),
			array('$unwind' => '$people'),
            
			array('$match' => array('_id' => (int)$passengerlog_id)),
			array('$project' =>
				array(
					'passengers_log_id' => '$_id',
                    'get_companyid' => '$company._id',
					'driver_name' => '$people.name',
                    'driver_phone' => '$people.phone',
                    'passenger_discount' => '$passengers.discount',
                    'passenger_name' => '$passengers.name',
                    'passenger_email' => '$passengers.email',
                    'passenger_creditcard_no' => '$passengers.creditcard_details.creditcard_no',
                    'passenger_phone' => '$passengers.phone',
				)
			)							
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		return (!empty($result['result'])?$result['result']:array());
    }
    //Get Driver Current Status if he is break,Avtive,Free
    public function get_driver_current_status($id)
    {
		$result = $temp_arr = array();
		$res = $this->mongo_db->findOne(MDB_DRIVER_INFO,array('_id'=>(int)$id),array('shift_status','update_date','loc.coordinates','status'));
		if(!empty($res)){
			$temp_arr['shift_status'] = $res['shift_status'];
			$temp_arr['update_date'] = isset($res['update_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['update_date']):'';
			$temp_arr['status'] = $res['status'];
			$coordinates = isset($res['loc']['coordinates']) ? $res['loc']['coordinates'] : array();
			$latitude = $longitude = '';
			if(!empty($coordinates)){
				/*foreach($coordinates as $c){
					$latitude[] = $c[1];
					$longitude[] = $c[0];
				}*/
				$latitude = $coordinates[1];
				$longitude = $coordinates[0];
			}
			$temp_arr['latitude'] = $latitude;
			$temp_arr['longitude'] = $longitude;
			$result[] = (object)$temp_arr;
		}
		return $result;
    }
    public function get_shift_status($id)
    {
		//$res = $this->mongo_db->Find(MDB_SHIFT_HISTORY,array('driver_id' => (int)$id))->sort(array('_id'=>-1))->limit(1);
           ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'sort'=>[
                        '_id'=>-1                        
                    ],
                    'limit'=>1
                ];
                $res = $this->mongo_db->find(MDB_SHIFT_HISTORY,array('driver_id' => (int)$id),$options);
        $result = $res;
        //echo '<pre>';print_r($result);exit;
        return (!empty($result)) ? Commonfunction::change_key($result) : array();
    }
    /** Driver Current Travel Availability **/
    public function check_driver_travel_availability($driver_id, $pickup_time)
    {       	
		$result = array();	
        $start_date = date('Y-m-d 00:00:01');
        //$res = $this->mongo_db->find(MDB_PASSENGERS_LOGS,array('driver_id'=>(int)$driver_id,'driver_reply'=>'A','travel_status'=>1,'pickup_time'=>array('$gte'=>$start_date,'$lt'=>$pickup_time)),array('driver_id','current_location','drop_location',	'_id','msg_status','driver_reply'))->sort(array('_id'=>-1))->limit(1);
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        'driver_id'=>1,
                        'current_location'=>1,
                        'drop_location'=>1,
                        '_id'=>1,
                        'msg_status'=>1,
                        'driver_reply'=>1
                    ],
                    'sort'=>[
                        '_id'=>-1
                        ],
                    'limit'=>1
                ];
        $res = $this->mongo_db->find(MDB_PASSENGERS_LOGS,array('driver_id'=>(int)$driver_id,'driver_reply'=>'A','travel_status'=>1,'pickup_time'=>array('$gte'=>$start_date,'$lt'=>$pickup_time)),$options);
        $result = $res;
        if(!empty($result)){
			$result = Commonfunction::change_key($result);
		}
        return $result;
    }
    //Update Driver Shift Status
    public function update_driver_shift_status($id, $status, $stat = null)
    {
        $sql_query          = array(
            'shift_status' => 'OUT'
        );
        $notification_query = array(
            'notification_setting' => 0
        );
        if ($status == 1) {
            $shift_status = 'IN';
            $sql_query          = array(
                'shift_status' => 'IN'
            );
            $notification_query = array(
                'notification_setting' => 1
            );
        }
		$result = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id' => (int)$id),
                                          array('$set'=>$sql_query),array('upsert'=>false));
        
        $result1 = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id' => (int)$id),
                                           array('$set'=>$notification_query),array('upsert'=>false));
    }
    public function get_trans_of_driver($id, $limit, $days_ago = '', $cur_day = '')
    {
		$result = $temp_arr = array();
        $d_day_ago = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
        $d_cur_day = (date('Y-m-d'));
        if (($days_ago == '') && ($cur_day == '')) {
            $start = $d_day_ago;
            $end   = $d_cur_day;
        } else {
            $start = $days_ago;
            $end   = $cur_day;
        }
        
        $match_query = array("\$and" => array(array("pickup_time"=>array('$gte' => Commonfunction::MongoDate(strtotime($start)),'$lt'=> Commonfunction::MongoDate(strtotime($end)))),array('driver_id' => (int)$id),array('travel_status' => 1)));
       
        $arguments = array(
            array('$match'	=> $match_query),
            array('$lookup' 		=> array(
                    'from'			=>	MDB_TRANSACTION,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "trans"
                )
            ),
            array('$unwind'=>'$trans'),
            array(
                '$project' => array(
                    'fare' => '$trans.fare',
                    'month' => array( '$substr' => array( '$pickup_time', 5, 2 ) ),
                    'day' => array( '$substr'=> array( '$pickup_time', 8, 2 ) ),
                )
            ),
            array('$group' => array('_id' => array('date' => '$day','month' => '$month'),
                    'fare' => array( '$sum' => '$fare' ),
                    'date' => array( '$first' => '$day' ),
                    'month' => array( '$first' => '$month' ),
                    //'trips' => array( '$sum' => 1 ),
                    )
            ),
            array(
                '$skip' => 0
            ),
            array(
              '$limit' => (int)$limit
            )
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr['fare'] = $r['fare'];
				$temp_arr['date'] = $r['date'];
				$temp_arr['month'] = $r['month'];
				$result[] = $temp_arr;
			}			
		}
        return $result;
    }
    public function get_trans_of_taxi($id, $limit, $days_ago = '', $cur_day = '')
    {
		$result = $temp_arr = array();
        $d_day_ago = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
        $d_cur_day = (date('Y-m-d'));
        if (($days_ago == '') && ($cur_day == '')) {
            $start = $d_day_ago;
            $end   = $d_cur_day;
        } else {
            $start = $days_ago;
            $end   = $cur_day;
		}
       $match_query = array("\$and" => array(array("pickup_time"=>array('$gte' => Commonfunction::MongoDate(strtotime($start)),'$lt'=> Commonfunction::MongoDate(strtotime($end)))),array('taxi_id' => (int)$id),array('travel_status' => 1)));
       
       $arguments = array(
            array('$match'	=> $match_query),
            array('$lookup' 		=> array(
                    'from'			=>	MDB_TRANSACTION,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "trans"
                )
            ),
            array('$unwind'=>'$trans'),
            array(
                '$project' => array(
                    'fare' => '$trans.fare',
                    "month" => array( '$substr' => array( '$pickup_time', 5, 2 ) ),
                    "day" => array( '$substr'=> array( '$pickup_time', 8, 2 ) ),
                )
            ),
            array('$group' => array("_id" => array("date" => '$day',"month" => '$month'),
                    "fare" => array( '$sum' => '$fare' ),
                    "trips" => array( '$sum' => 1 ),
                    )
            ),
            array(
                '$skip' => 0
            ),
            array(
              '$limit' => (int)$limit
            )
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr['date'] = $r['_id']['date'];
				$temp_arr['month'] = $r['_id']['month'];
				$temp_arr['fare'] = $r['fare'];
				$temp_arr['trips'] = $r['trips'];
				$result[] = $temp_arr;
			}
		}
        //echo "<pre>"; print_r($result); exit;
        return $result;
    }
    //** function to get total transaction count of a taxi **//
    public function get_total_trans_taxi($id)
    {
       $match_query = array("\$and" => array(array('taxi_id' => (int)$id),array('travel_status' => 1)));
       $arguments = array(
            array('$match'	=> $match_query),
            array('$lookup' 		=> array(
                    'from'			=>	MDB_TRANSACTION,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "trans"
                )
            ),
            array('$unwind'=>'$trans'),
            array('$group' => array("_id" => 0,
                    "count" => array( '$sum' => 1 ),
                    )
            ),
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        //echo "<pre>"; print_r($result); exit;
        return (!empty($result['result']))?$result['result'][0]['count']:0;
    }
    public function get_taxi_trips($id)
    {
		$result = $temp_arr = array();
        $match_query = array("\$and" => array(array('taxi_id' => (int)$id),array('travel_status' => 1),array('dlh.status' => 'A')));
        $arguments = array(
            array('$lookup' 		=> array(
                    'from'			=>	MDB_LOCATION_HISTORY,
                    'localField'	=> "driver_id",
                    'foreignField'	=> "driver_id",
                    'localField'	=> "_id",
                    'foreignField'	=> "trip_id",
                    'as'			=> "dlh"
                )
            ),
            array('$unwind'=>'$dlh'),
            array('$match'	=> $match_query),
            array(
                '$project' => array('_id'=>0,
                    'passengers_log_id' => '$_id',
                    'active_record' => '$dlh.loc',
                    'current_location' => '$current_location',
                    'drop_location' => '$drop_location',
                )
            ),
            array(
                '$skip' => 0
            ),
            array(
              '$limit' => 3
            )
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr['current_location'] = $r['current_location'];
				$temp_arr['drop_location'] = $r['drop_location'];
				$temp_arr['passengers_log_id'] = $r['passengers_log_id'];
				$coordinates = isset($r['active_record']['coordinates']) ? $r['active_record']['coordinates']: array();
				$active_record = '';
				if(!empty($coordinates)){
					foreach($coordinates as $c){
						$latitude = '['.$c[1];
						$longitude = ','.$c[0].'],';
						$active_record .= $latitude.$longitude;
					}
				}
				$temp_arr['active_record'] = $active_record;
				$result[] = $temp_arr;
			}
		}   
		//echo "<pre>"; print_r($result); exit;     
        return $result;
    }
    //Function used to get all Shift logs
    public function count_get_driver_shift_logs($id)
    {
        $result = $this->mongo_db->count(MDB_SHIFT_HISTORY,array('driver_id' => (int)$id));
        return (isset($result)) ? $result : 0;
    }
    public function get_driver_shift_logs($id, $start, $limit,$count=false)
    {    
		$temp_arr = $result = array();
        $match_query = array('driver_id' => (int)$id);
        $arguments = array(
            array('$lookup' 		=> array(
                    'from'			=>	MDB_TAXI,
                    'localField'	=> "taxi_id",
                    'foreignField'	=> "_id",
                    'as'			=> "taxi"
                )
            ),
            array('$unwind'=>'$taxi'),
            array('$match'	=> $match_query),
            array(
                '$project' => array('_id'=>0,
                    'driver_id' => '$driver_id',
                    'reason' => '$reason',
                    'shift_start' => '$shift_start',
                    'shift_end' => '$shift_end',
                    'taxi_no' => '$taxi.taxi_no',
                    'taxi_id' => '$taxi._id',
                )
            ),
            array(
                '$sort' => array('_id' => -1)
            ),
            array(
                '$skip' => (int)$limit
            ),
            array(
              '$limit' => (int)$start
            )
        );
        $res = $this->mongo_db->aggregate(MDB_SHIFT_HISTORY,$arguments);
       
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr['driver_id'] = $r['driver_id'];
				$temp_arr['reason'] = $r['reason'];
				$temp_arr['shift_start'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['shift_start']);
				$temp_arr['shift_end'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['shift_end']);
				$temp_arr['taxi_no'] = $r['taxi_no'];
				$temp_arr['taxi_id'] = $r['taxi_id'];
				
				$result[] = (object)$temp_arr;
			}
		}
        return $result;
    }
    public function get_trip_statitics($driver_id)
    {
        $start_date      = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"))).' 00:00:01'; //'2015-03-06 00:00:01';
        $end_date        = (date('Y-m-d')).' 23:59:59';
        $temp_arr = $rejected_trips = $completed_trips = $cancelled_trips = $cum_arr = array();
        
        # Rejection query starts
        $match = array("\$and" => array(array("createdate"=>array('$gte'=> Commonfunction::MongoDate(strtotime($start_date)),
        '$lt'=> Commonfunction::MongoDate(strtotime($end_date)))),array('driver_id' => (int)$driver_id)));
        
        $rejected_arguments = array(
            array('$match'	=> $match),
            array(
                '$project' => array(
                    'createdate' => '$createdate',
                    'year' => array( '$substr' => array( '$createdate', 0, 4 ) ),
                    'month' => array( '$substr' => array( '$createdate', 5, 2 ) ),
                    'day' => array( '$substr'=> array( '$createdate', 8, 2 ) ),
                )
            ),
            array('$group' => array('_id' => array('year' => '$year','date' => '$day','month' => '$month'),
                'createdate' => array('$first' => '$createdate'),
                'rejected_count' => array( '$sum' => 1 ),
                )
            )
        );
        $rejected = $this->mongo_db->aggregate(MDB_REJECTION_HISTORY,$rejected_arguments);
        if(!empty($rejected['result'])){
			foreach($rejected['result'] as $r){
				
				$temp_arr['createdate'] = isset($r['createdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']) :'';
				$temp_arr['rejected_count'] = isset($r['rejected_count']) ? $r['rejected_count'] :'';
				$rejected_trips[] = $temp_arr;
			}			
		}        
        #Rejection query End        
        $args = array(
						array('$match' => $match),
						array('$project' => array('date' =>  array( '$dateToString' =>  array( 'format' =>  '%Y-%m-%d', 'date' =>  '$createdate' ) ),
											'travel_status' => '$travel_status',
											'createdate' => '$createdate',
											'driver_reply' => '$driver_reply'
											)),
						array('$group' => array('_id' => array('driver_reply' => array('driver_reply' => '$driver_reply'),
											'travel_status' => array('travel_status' => '$travel_status'),
											'date' => array('date' => '$date')),
											'count' => array('$sum' => 1)))
					);
		$trips = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);	
	
		if(!empty($trips['result'])){
			foreach($trips['result'] as $t){		
				//echo "<pre>"; print_r($t); exit;
				$driver_reply = isset($t['_id']['driver_reply']['driver_reply'])? $t['_id']['driver_reply']['driver_reply'] :'';
				$travel_status = isset($t['_id']['travel_status']['travel_status'])?$t['_id']['travel_status']['travel_status']:'';	
				$date = isset($t['_id']['date']['date']) ? $t['_id']['date']['date'] :'';	
				if($driver_reply == 'C'){				
					$cancelled_trips[] = array('createdate' => $date, 'cancelled_count' => $t['count']);
				}
				if($driver_reply == 'A' && $travel_status == 1){
					$completed_trips[] = array('createdate' => $date, 'completed_count' => $t['count']);
				}
			}		
		}
		$result=array('completed_trips'=>$completed_trips,'rejected_trips'=>$rejected_trips,'cancelled_trips'=>$cancelled_trips);
		return $result;       
    }  
    public function get_current_trip_logs($id)
    {
		$result = $temp_arr = array();
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
        $match_query = array('driver_id' => (int)$id,'driver_reply' => 'A',
							'pickup_time'=>array('$gte'=>$start_time,'$lte'=>$end_time), 
							"travel_status"=> array('$in'=>array(5,2)));
        $arguments = array(
            array('$lookup' 		=> array(
                    'from'			=>	MDB_PASSENGERS,
                    'localField'	=> 'driver_id',
                    'foreignField'	=> "_id",
                    'as'			=> "people"
                )
            ),
            array('$unwind' => '$people'),
            array('$match'	=> $match_query),
            array(
                '$project' => array('_id'=>0,
                    'id' => '$_id',
                    'approx_distance' => '$approx_distance',
                    'approx_fare'=>'$approx_fare',
                    'distance'=>'$distance',
                    'company_id'=>'$company_id',
                    'current_location'=>'$current_location',
                    'drop_location'=>'$drop_location',
                    'travel_status' => '$travel_status',
                )
            ),
            array(
                '$sort' => array(
                    '_id' => -1
                ),
            ),
            array(
              '$limit' => (int)1
            )
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr['approx_distance'] = $r['approx_distance'];
				$temp_arr['approx_fare'] = $r['approx_fare'];
				$temp_arr['distance'] = $r['distance'];
				$temp_arr['company_id'] = $r['company_id'];
				$temp_arr['current_location'] = $r['current_location'];
				$temp_arr['drop_location'] = $r['drop_location'];
				$temp_arr['travel_status'] = $r['travel_status'];
				$result[] = $temp_arr;
			}
		}
        return $result;
    }
    
    //** function to get total transaction count of a driver **//
    public function get_total_trans_driver($id)
    {
       $match_query = array("\$and" => array(array('driver_id' => (int)$id),array('travel_status' => 1)));
       $arguments = array(
            array('$match'	=> $match_query),
            array('$lookup' 		=> array(
                    'from'			=>	MDB_TRANSACTION,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "trans"
                )
            ),
            array('$unwind'=>'$trans'),
            array('$group' => array("_id" => 0,
                    "count" => array( '$sum' => 1 ),
                    )
            ),
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        $result = (!empty($res['result']) && isset($res['result'][0]['count'])) ? $res['result'][0]['count']:0;
        return $result;
    }
        
    //** function to get total ratings of a driver **//
    public function get_total_ratings_driver($id)
    {
		$result = array();
        $match_query = array('driver_id' => (int)$id, 'travel_status' => 1, 'rating' => array('$gt' => 0));
        $arguments = array(
            array('$match'	=> $match_query),
            array('$lookup' 		=> array(
                    'from'			=>	MDB_TRANSACTION,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "trans"
                )
            ),
            array('$unwind'=>'$trans'),
            array(
                '$project' => array(
                    'rating' => '$rating',
                    'driver_id' => '$driver_id',
                    'month' => array( '$substr' => array( '$pickup_time', 5, 2 ) ),
                    'day' => array( '$substr'=> array( '$pickup_time', 8, 2 ) ),
                )
            ),
            array('$group' => array(
					//'_id' => array('date' => '$day','month' => '$month'),
					"_id" => array('driver_id'=>'$driver_id'),
                    'total_ratings' => array( '$sum' => '$rating' ),
                    'trip_cnt' => array( '$sum' => 1 ),
                    )
            )
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr['total_ratings'] = $r['total_ratings'];
				$temp_arr['trip_cnt'] = $r['trip_cnt'];
				$result[] = $temp_arr;
			}			
		}
        //echo '<pre>';print_r($result);exit;
        return $result;
    }

    
    public function driver_logs_progress_upcoming($id,$msg_status,$driver_reply=null,$company_id)
	{	
		$result =array();		
		if($company_id == ''){
				$current_time =	date('Y-m-d H:i:s');
				$start_time = date('Y-m-d').' 00:00:01';
				$end_time = date('Y-m-d').' 23:59:59';
		}	
		else{
			$timezone_fetch = $this->mongo_db->findOne(MDB_COMPANY,array('_id' => (int)$company_id),array('companydetails.time_zone'));
			if(isset($timezone_fetch['companydetails']['time_zone']))
			{
				$current_time = convert_timezone('now',$timezone_fetch['companydetails']['time_zone']);
				$current_date = explode(' ',$current_time);
				$start_time = $current_date[0].' 00:00:01';
				$end_time = $current_date[0].' 23:59:59';
			}
			else
			{
				$current_time =	date('Y-m-d H:i:s');
				$start_time = date('Y-m-d').' 00:00:01';
				$end_time = date('Y-m-d').' 23:59:59';
			}
		}
		
		$match = array('driver_id' => (int)$id,
					'msg_status' => $msg_status,
					'driver_reply' => $driver_reply,
					'pickup_time' => array('$gte' =>  Commonfunction::MongoDate(strtotime($start_time))));
		
		$arguments = array(
						array('$lookup' => array(
												'from' => MDB_PASSENGERS,
												'localField' => 'passengers_id',
												'foreignField' => "_id",
												'as' => "passenger")),
						array('$unwind' => '$passenger'),
						array('$match'  => $match),				
						array('$project' => array('_id'=>0,
												'taxi_id' => '$taxi._id',
												'name' => '$passenger.name',
												'current_location' => '$current_location',
												'drop_location' => '$drop_location',
												'no_passengers' => '$no_passengers',
												'travel_status' => '$travel_status',
												'pickup_time' => '$pickup_time')),
						array('$group' => array('_id' => array('travel_status' => '$travel_status',
											'name' => '$name',
											'current_location' => '$current_location',
											'drop_location' => '$drop_location',
											'no_passengers' => '$no_passengers',
											'pickup_time' => '$pickup_time')
										)),
						array('$sort' =>array('_id' => -1))
					);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		$result = array();
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
                            $temp_arr['travel_status'] = $r['_id']['travel_status'];
                            $temp_arr['name'] = $r['_id']['name'];                            
                            $temp_arr['current_location'] = $r['_id']['current_location'];
                            $temp_arr['drop_location'] = $r['_id']['drop_location'];
                            $temp_arr['no_passengers'] = $r['_id']['no_passengers'];
                            $temp_arr['pickup_time'] = isset($r['_id']['pickup_time'])?Commonfunction::convertphpdate("", $r['_id']['pickup_time']): '';
                            $result[] = (object)$temp_arr;
			}
		}
		//echo "<pre>"; print_r($result); exit;
		return $result;
	}
	
	public function get_driver_referral_list($id,$record,$start,$con)
	{
		$result = $temp_arr = array();
		$args = array(
						array('$lookup' => array('from' => MDB_DRIVER_REF,
												'localField' => '_id',
												'foreignField' => "registered_driver_id",
												'as' => "driver_referral")),
						array('$unwind' => '$driver_referral'),
						array('$match'  => array('driver_referral.referred_driver_id' => (int)$id)),				
						array('$project' => array('_id'=>0,
												'name' => '$name',
												'registered_driver_code' => '$driver_referral.registered_driver_code',
												'referred_driver_id' => '$driver_referral.referred_driver_id',
												'registered_driver_id' => '$driver_referral.registered_driver_id',
												'registered_driver_wallet' => '$driver_referral.registered_driver_wallet',
												'referral_status' => '$driver_referral.referral_status',
												'createdate' => '$driver_referral.createdate')),
						array('$sort' => array('createdate' => -1))
					);		
		if($con){
			$args[]['$skip'] = (int)$start;
			$args[]['$limit'] = (int)$record;
		}
		$res = $this->mongo_db->aggregate(MDB_PEOPLE,$args);			
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr = $r;
				$temp_arr['createdate'] = isset($temp_arr['createdate']) ? commonfunction::convertphpdate('Y-m-d h:i:s',$temp_arr['createdate']) : '';
				$result[] = (object)$temp_arr;
			}			
		}
		return $result;
	}
	
	//** function to get overall trip statistics count **//
	public function getoverall_trip_statitics_count($driver_id)
	{
		$match = array('driver_id' => (int)$driver_id);
		$args = array(		
					array('$match' => $match),
					array('$project' => array('createdates' =>  array('$dateToString' => array('format' =>  '%Y-%m-%d', 'date' => '$createdate')))),
					array('$group' => array('_id' => '$createdates',
					'count' => array('$sum' => 1)))
				);		
		$rejected = $this->mongo_db->Aggregate(MDB_REJECTION_HISTORY,$args);
		$rejected_trips = !empty($rejected['result']) ? $rejected['result'][0]['count'] : 0;
		
		$args = array(
					array('$match' => $match),
					array('$project' => array(
										'driver_reply' => '$driver_reply',
										'travel_status' => '$travel_status',
										'createdates' =>  array( '$dateToString' =>  array( 'format' =>  '%Y-%m-%d', 'date' =>  '$createdate' ) ))),
					array('$group' => array('_id' => array('driver_reply' => array('driver_reply' => '$driver_reply'),
										'travel_status' => array('travel_status' => '$travel_status'),
										'date' => array('createdates' => '$createdates')),
										'count' => array('$sum' => 1))),
					array('$sort' => array('_id' => -1))
				);
		$trips = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$args);
		$cancelled_trips = $completed_trips =0;
		
		if(!empty($trips['result'])){
			foreach($trips['result'] as $t){
				$driver_reply = isset($t['_id']['driver_reply']['driver_reply'])?$t['_id']['driver_reply']['driver_reply']:'';
				$travel_status = isset($t['_id']['travel_status']['travel_status'])?$t['_id']['travel_status']['travel_status']:'';
				if($driver_reply == 'C'){				
					$cancelled_trips++;
				}
				if($driver_reply == 'A' && $travel_status == 1){
					$completed_trips++;
				}
			}
		}
		
		if($rejected_trips == 0 && $cancelled_trips == 0 && $completed_trips == 0) {
			return 0;
		} else {
			return 1;
		}
	}
}
