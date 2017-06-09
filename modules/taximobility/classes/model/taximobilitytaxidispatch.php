<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Taxidispatch Model details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Model_TaximobilityTaxidispatch extends Model
{
    public function __construct()
    {
        $this->session     = Session::instance();
        $this->usertype    = $this->session->get("user_type");
        $this->userid     =  $this->user_id     = $this->session->get('userid');
        $this->company_id  = $this->session->get('company_id');
        $this->country_id  = $this->session->get('country_id');
        $this->state_id    = $this->session->get('state_id');
        $this->city_id     = $this->session->get('city_id');
        $this->currentdate = Commonfunction::getCurrentTimeStamp();
        $this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
        $this->commonmodel = Model::factory('commonmodel');
        $this->company_current_time = $this->commonmodel->getcompany_all_currenttimestamp($this->company_id);
        $this->api_model   = Model::factory('api');
        $this->tdispatch_model = Model::factory('tdispatch');
        //MongoDB Instance
        $this->mongo_db    = MangoDB::instance('default');
        
    }
    public function getpassenger_Detailinfo_new($data)
    {
        $company_id      = $this->company_id;
        $date_field_name = $data['field_name'];
        $split_value     = explode('-', $date_field_name);
        
        if($company_id!=0){
            if ($data['field_value'] == 'firstname') {
                if (isset($split_value[1])) {
                    $phone_split = substr(trim($split_value[1]), 0, -1);
                    $phone_no    = substr(trim($phone_split), 1);
                    $phone_no    = trim($phone_no);
                    $condition =array('phone' => $phone_no,'user_status'=>'A',"\$or"=>array(array('passenger_cid'=>(int)$company_id),array('logs.company_id'=>(int)$company_id)));
                } else {
                    $condition =array('name' => $data['field_name'],'user_status'=>'A',"\$or"=>array(array('logs.company_id'=>(int)$company_id)));
                }
            } elseif ($data['field_value'] == 'email') {
                $condition =array('email' => $data['field_name'],'user_status'=>'A',"\$or"=>array(array('passenger_cid'=>(int)$company_id),array('logs.company_id'=>(int)$company_id)));
            } elseif ($data['field_value'] == 'phone') {
                 $condition =array('phone' => $data['field_name'],'user_status'=>'A',"\$or"=>array(array('passenger_cid'=>(int)$company_id),array('logs.company_id'=>(int)$company_id)));
            }
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_PASSENGERS_LOGS,
                        'localField' => '_id',
                        'foreignField' => 'passengers_id',
                        'as' => 'logs'
                    )
                ),
               /* array(
                    '$unwind' => '$logs'
                ), */
                array('$unwind' => array('path' => '$logs','preserveNullAndEmptyArrays' => true)),
                array(
                    '$match' => $condition
                ),
                array(
                    '$project' => array('_id' => 0,
                        'id' => '$_id',
                        'name' => '$name',
                        'email' => '$email',
                        'phone' => '$phone',
                        'country_code' => '$country_code',
                    )
                ),
                array(
					'$group' => array(
						'_id' => array('_id' => '$_id'),
						'details' => array('$first' => 
							array('id' => '$_id',
								'name' => '$name',
								'email' => '$email',
								'phone' => '$phone',
								'country_code' => '$country_code'
							)
						)
					)
				)
                //array('$limit'=>1)
            );
            $result          = $this->mongo_db->aggregate(MDB_PASSENGERS, $arguments);
            $results = array();
            if(!empty($result['result'])){
				foreach($result['result'] as $v){
					$v = $v['details'];
					$arr['id'] = isset($v['id'])?$v['id']:"";
					$arr['name'] = isset($v['name'])?$v['name']:"";
					$arr['email'] = isset($v['email'])?$v['email']:"";
					$arr['country_code'] = isset($v['country_code'])?$v['country_code']:"";
					$arr['phone'] = isset($v['phone'])?$v['phone']:"";
					$results[] = $arr;
				}
			}
            
			//echo "<pre>if";print_r($result['result']); exit;
            return $results;
        } else {
            if ($data['field_value'] == 'firstname') {
                if (isset($split_value[1])) {
                    $phone_split = substr(trim($split_value[1]), 0, -1);
                    $phone_no    = substr(trim($phone_split), 1);
                    $phone_no    = trim($phone_no);
                    $condition =array('phone' => $phone_no,'user_status'=>'A');
                } else {
                    $condition =array('name' => $data['field_name'],'user_status'=>'A');
                }
            } elseif ($data['field_value'] == 'email') {
                $condition =array('email' => $data['field_name'],'user_status'=>'A');
            } elseif ($data['field_value'] == 'phone') {
                $condition =array('phone' => $data['field_name'],'user_status'=>'A');
            }
            //echo '<pre>';print_r($condition);exit;
            $results = $this->mongo_db->findOne(MDB_PASSENGERS,$condition,array('_id','name','phone','email','country_code'));
            //echo "<pre>else";print_r($results);exit;
			$data = array();
			if(count($results) > 0){
				$arr = $results;
				$arr['id'] = $results['_id'];
				$data[] = $arr;	
			}
            return $data;
        }
    }
    public function getuser_details($like, $type)
    {
        $company_id  = $this->company_id;
        //MongoDB
        if($company_id!=0){
            if($type==1){
                $split_value = explode('-', urldecode($like));
                if (count($split_value) > 1) {
                    $phone_split = substr(trim($split_value[1]), 0, -1);
                    $phone_no    = substr(trim($phone_split), 1);
                    $phone_no    = trim($phone_no);
                    $condition =array('phone' => Commonfunction::MongoRegex("/$phone_no/i"),'user_status'=>'A',"\$or"=>array(array('passenger_cid'=>(int)$company_id),array('logs.company_id'=>(int)$company_id)));
                } else {
                    $condition =array('name' => Commonfunction::MongoRegex("/$like/i"),'user_status'=>'A',"\$or"=>array(array('logs.company_id'=>(int)$company_id)));
                }
            } else if($type==2){
                $condition =array('email' => Commonfunction::MongoRegex("/$like/i"),'user_status'=>'A',"\$or"=>array(array('passenger_cid'=>(int)$company_id),array('logs.company_id'=>(int)$company_id)));
            } else if($type==3){
               $condition =array('phone' => Commonfunction::MongoRegex("/$like/i"),'user_status'=>'A',"\$or"=>array(array('passenger_cid'=>(int)$company_id),array('logs.company_id'=>(int)$company_id)));
            }
            //echo "<pre>"; print_r($condition); exit;
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_PASSENGERS_LOGS,
                        'localField' => '_id',
                        'foreignField' => 'passengers_id',
                        'as' => 'logs'
                    )
                ),
               /* array(
                    '$unwind' => '$logs'
                ), */
                array('$unwind' => array('path' => '$logs','preserveNullAndEmptyArrays' => true)),
                array('$match' => $condition),
                array(
                    '$project' => array(
                        'name' => '$name',
                        'email' => '$email',
                        'phone' => '$phone',
                    )
                ),
                array(
					'$group' => array(
						'_id' => array('_id' => '$_id'),
						'details' => array('$first' => 
							array('name' => '$name',
								'email' => '$email',
								'phone' => '$phone'
							)
						)
					)
				)
            );
            $result          = $this->mongo_db->aggregate(MDB_PASSENGERS, $arguments);
            $results = array();
            if(!empty($result['result'])){
				foreach($result['result'] as $v){
					$v = $v['details'];
					$arr['name'] = isset($v['name'])?$v['name']:"";
					$arr['email'] = isset($v['email'])?$v['email']:"";
					$arr['phone'] = isset($v['phone'])?$v['phone']:"";
					$results[] = $arr;
				}
			}
            return $results;
        } else {
            if($type==1){
                $split_value = explode('-', urldecode($like));
                if (count($split_value) > 1) {
                    $phone_split = substr(trim($split_value[1]), 0, -1);
                    $phone_no    = substr(trim($phone_split), 1);
                    $phone_no    = trim($phone_no);
                    $condition =array('phone' => Commonfunction::MongoRegex("/$phone_no/i"),'user_status'=>'A');
                } else {
                    $condition =array('name' => Commonfunction::MongoRegex("/$like/i"),'user_status'=>'A');
                }
            } else if($type==2){
                $condition =array('email' => Commonfunction::MongoRegex("/$like/i"),'user_status'=>'A');
            } else if($type==3){
               $condition =array('phone' => Commonfunction::MongoRegex("/$like/i"),'user_status'=>'A');
            }
            //$results = $this->mongo_db->find(MDB_PASSENGERS,$condition,array('_id','name','phone','email'))->sort(array('name'=>1));
             ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                      '_id'=>1,
                      'name'=>1,
                      'phone'=>1,
                      'email'=>1
                  ],
                'sort'=>[
                    'name'=>1
                    ]
                ];
            $results = $this->mongo_db->find(MDB_PASSENGERS,$condition,$options);
            return (!empty($results))?$results:array();
        }
    }
    /*************************Dashboard Driver status ***********************************/
    public function driver_status_details( $array )
    {
        $user_createdby = $this->user_id;
        $usertype       = $this->usertype;
        $company_id     = $this->company_id;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
        $driver_status = isset( $array[ 'driver_status' ] ) ? $array[ 'driver_status' ] : "";
        $taxi_company  = isset( $array[ 'taxi_company' ] ) ? $array[ 'taxi_company' ] : "";
        $match_query = $result = array();
        $match_query['people.user_type'] = 'D';
        $match_query['people.status'] = 'A';
        if ($usertype == 'C' || $usertype == 'M') {
            if($company_id!="" && $company_id!=0){
                $match_query['people.company_id'] = $company_id;
            }
        } else {
            if ($taxi_company != "" && $taxi_company != 0) {
                $match_query['people.company_id'] = $taxi_company;
            }
        }
        if ( $driver_status == 'A' || $driver_status == 'F' || $driver_status == 'B') {
            $match_query['status'] = $driver_status;
            $match_query['shift_status'] = 'IN';
        } elseif ( $driver_status == 'OUT' ) {
            $match_query['status'] = 'F';
            $match_query['shift_status'] = $driver_status;
        }
			
        $match2 = array('updatetime_difference' => array('$lte' => LOCATIONUPDATESECONDS));
        $company_current_time = $this->company_current_time;
        //$company_current_time = '2016-08-23 00:00:00';
		$arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => '_id',
					'foreignField' => '_id',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
			array(
				'$match' => $match_query
			),
            /*array(
                '$project' => array(
                    'driver_id' => '$people._id',
                    'name' => '$people.name',
                    'driver_status' => '$status',
                    'shift_status' => '$shift_status',
                    //'update_date' => '$update_date',
                    'loc' => '$loc.coordinates',
                    //'updatetime_difference' => array('$subtract' =>array(Commonfunction::MongoDate(strtotime($company_current_time)),'$update_date')),
                     'updatetime_difference' => array('$multiply' => array(array('$subtract' => array(Commonfunction::MongoDate(strtotime($company_current_time)),'$update_date')),0.0001))
                )
            ), */
            
            array('$group' => array('_id' => array(
					'driver_id' => '$_id',
					'name' => '$people.name',
					'driver_status' => '$status',
					'loc' => '$loc.coordinates',
					'shift_status' => '$shift_status',
					'updatetime_difference' => array('$multiply' => array(array('$subtract' => array(Commonfunction::MongoDate(strtotime($company_current_time)),'$update_date')),0.0001))
				))),
			array('$match' => array('_id.updatetime_difference'=>array('$lte'=> (int)LOCATIONUPDATESECONDS ))),
            //array('$match' => $match2),
            array(
                '$sort' => array( 
                    'people.created_date' => -1
                ),
            ),
		);
		
        $res = $this->mongo_db->aggregate(MDB_DRIVER_INFO, $arguments);
        if(!empty($res['result'])){
			$i=0;
			foreach($res['result'] as $r){
				$r =$r['_id'];
				$result[$i]['driver_id'] = $r['driver_id'];
				$result[$i]['name'] = isset($r['name']) ? $r['name']:'';
				$result[$i]['driver_status'] = isset($r['driver_status'])?$r['driver_status']:'';
				$result[$i]['shift_status'] = isset($r['shift_status'])?$r['shift_status']:'';
				$result[$i]['updatetime_difference'] = isset($r['updatetime_difference'])?$r['updatetime_difference']:'';
				$result[$i]['latitude'] = isset($r['loc'][1]) ? $r['loc'][1]:'';
				$result[$i]['longitude'] = isset($r['loc'][0]) ? $r['loc'][0] : '';
				$i++;
			}
		}
		//echo '<pre>';print_r($res);exit;
        return $result;
    }

    public function driver_status_details_model( $array )
    {
        //print_r($array);exit;
        $user_createdby       = $this->user_id;
        $usertype             = $this->usertype;
        $company_id           = $this->company_id;
        $country_id           = $this->country_id;
        $state_id             = $this->state_id;
        $city_id              = $this->city_id;
        $company_current_time = $this->company_current_time;
        $driver_status = isset($array['driver_status']) ? $array['driver_status'] : "";
        $taxi_company  = isset($array['taxi_company']) ? $array['taxi_company'] : "";
        $taxi_model    = isset($array['taxi_model']) ? $array['taxi_model'] : "";
        $match_query = array();
        $match_query['user_type'] = 'D';
        $match_query['status'] = 'A';
        if ($usertype == 'C' || $usertype == 'M') {
            if($company_id!="" && $company_id!=0){
                $match_query['company_id'] = $company_id;
            }
        } else {
            if ($taxi_company != "" && $taxi_company != 0) {
                $match_query['company_id'] = $taxi_company;
            }
        }
        
        if ( $driver_status == 'A' || $driver_status == 'F' ) {
            $match_query['driver.status'] = $driver_status;
            $match_query['driver.shift_status'] = 'IN';
        } elseif ( $driver_status == 'OUT' ) {
            $match_query['driver.status'] = 'F';
            $match_query['driver.shift_status'] = $driver_status;
        }
        $match_query['taxi.taxi_model'] = (int)$taxi_model;
       
        $arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_DRIVER_INFO,
					'localField' => '_id',
					'foreignField' => '_id',
					'as' => 'driver'
				)
			),
			array(
				'$unwind' => '$driver'
			),
            array(
				'$lookup' => array(
					'from' => MDB_TAXI_DRIVER_MAPPING,
					'localField' => '_id',
					'foreignField' => 'mapping_driverid',
					'as' => 'mapping'
				)
			),
			array(
				'$unwind' => '$mapping'
			),
            array(
				'$lookup' => array(
					'from' => MDB_TAXI,
					'localField' => 'mapping.mapping_taxiid',
					'foreignField' => '_id',
					'as' => 'taxi'
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
                    '$_id' => 0,
                    'driver_id' => '$_id',
                    'name' => '$name',
                    'driver_status' => '$status',
                    'driver.shift_status' => '$shift_status',
                    '$update_date' => '$driver.update_date',
                    'updatetime_difference' => array('$subtract' =>array(Commonfunction::MongoDate(strtotime($company_current_time)),'$driver.update_date')),
                )
            ),
            array('$match' => array('updatetime_difference'=>array('$lte'=>(int)LOCATIONUPDATESECONDS ))),
            array(
                '$sort' => array( 
                    'created_date' => -1
                ),
            ),
		);
        $result          = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
        //echo "<pre>"; print_r($result); exit;
        return (!empty($result['result'])) ? $result['result'] : array();
    }
    public function all_driver_map_list($array)
    {
        $taxi_company         = isset($array['taxi_company']) ? $array['taxi_company'] : 0;        
        $date = $this->company_current_time;
                
       if($this->usertype=='C' || $this->usertype=='M'){
            $company_condition = array('company_id' => (int)$this->company_id,'user_type'=>'D','status'=>'A','driver.shift_status'=>'IN');
        } else {
            $company_condition = ($taxi_company!=0) ? array('company_id' => (int)$taxi_company,'user_type'=>'D','status'=>'A','driver.shift_status'=>'IN') : array('user_type'=>'D','status'=>'A','driver.shift_status'=>'IN');
        }
        $arguments = array(
            array('$lookup' => array(
                    'from' => MDB_DRIVER_INFO,
                    'localField' => '_id',
                    'foreignField' => "_id",
                    'as' => "driver"
                )
            ),
            array('$unwind' => '$driver'),
            array('$match' => $company_condition),
            array('$project' => array(
                "_id" => 0,
                "loc" => '$driver.loc.coordinates',
                "driver_id" => '$driver._id',
                "driver_status" => '$driver.status',
                "shift_status" => '$driver.shift_status',
                "update_date" => '$driver.update_date',
                "name" => '$name',
                "user_type" => '$user_type',
                "status" => '$status',
                'updatetime_difference' => array('$subtract' =>array(Commonfunction::MongoDate(strtotime($date)),'$driver.update_date')),
                )
            ),
            array('$match' => array('updatetime_difference'=>array('$lte'=>(int)LOCATIONUPDATESECONDS))),
        );
		$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
		$results = array();
		if(!empty($result['result'])){
			foreach($result['result'] as $datas){
				$temp_arr['name'] = $datas['name'];
				$temp_arr['user_type'] = $datas['user_type'];
				$temp_arr['latitude'] = isset($datas['loc'][1])?$datas['loc'][1]:"";
				$temp_arr['longitude'] = isset($datas['loc'][0])?$datas['loc'][0]:"";
				$temp_arr['status'] = $datas['status'];
				$temp_arr['driver_id'] = $datas['driver_id'];
				$temp_arr['driver_status'] = $datas['driver_status'];
				$temp_arr['shift_status'] = $datas['shift_status'];
				$temp_arr['update_date'] = isset($r['update_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['update_date']):'';
				$temp_arr['updatetime_difference'] = $datas['updatetime_difference'];				
				$results[] = $temp_arr;
			}
		}
		return $results;
    }
	
    public function all_driver_map_list_model($array)
    {
        $taxi_model           = isset($array['taxi_model']) ? $array['taxi_model'] : 0;
        $taxi_company         = isset($array['taxi_company']) ? $array['taxi_company'] : 0;
        $user_createdby       = $this->user_id;
        $usertype             = $this->usertype;
        $company_id           = $this->company_id;
        $country_id           = $this->country_id;
        $state_id             = $this->state_id;
        $city_id              = $this->city_id;
        $company_current_time = $this->company_current_time;        
        $match_query = array();
        $match_query['user_type'] = 'D';
        $match_query['status'] = 'A';
        if ($usertype == 'C' || $usertype == 'M') {
            if($company_id!="" && $company_id!=0){
                $match_query['company_id'] = $company_id;
            }
        } else {
            if ($taxi_company != "" && $taxi_company != 0) {
                $match_query['company_id'] = $taxi_company;
            }
        }
        $match_query['driver.shift_status'] = 'IN';
        $match_query['taxi.taxi_model'] = (int)$taxi_model;
       
        $arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_DRIVER_INFO,
					'localField' => '_id',
					'foreignField' => '_id',
					'as' => 'driver'
				)
			),
			array(
				'$unwind' => '$driver'
			),
            array(
				'$lookup' => array(
					'from' => MDB_TAXI_DRIVER_MAPPING,
					'localField' => '_id',
					'foreignField' => 'mapping_driverid',
					'as' => 'mapping'
				)
			),
			array(
				'$unwind' => '$mapping'
			),
            array(
				'$lookup' => array(
					'from' => MDB_TAXI,
					'localField' => 'mapping.mapping_taxiid',
					'foreignField' => '_id',
					'as' => 'taxi'
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
                    "_id" => 0,
                    "loc" => '$driver.loc.coordinates',
                    'driver_id' => '$_id',
                    'update_date' => '$driver.update_date',
                    'name' => '$name',
                    'driver_status' => '$status',
                    'driver.shift_status' => '$shift_status',
                    'updatetime_difference' => array('$subtract' =>array(Commonfunction::MongoDate(strtotime($company_current_time)),'$driver.update_date')),
                )
            ),
            array('$match' => array('updatetime_difference'=>array('$lte'=>(int)LOCATIONUPDATESECONDS ))),
            array(
                '$sort' => array( 
                    'created_date' => -1
                ),
            ),
		);
        $result          = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
        //echo "<pre>"; print_r($result); exit;
        return (!empty($result['result'])) ? $result['result'] : array();
    }
    public function validate_dispatchbooking($arr)
    {
        return Validation::factory($arr)
        ->rule('firstname', 'not_empty')
        //->rule('firstname', 'min_length', array(':value', '3'))
        //->rule('firstname', 'max_length', array(':value', '32'))
        //->rule('email', 'not_empty')
        ->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '100'
        ))
        ->rule('country_code', 'not_empty')
        ->rule('phone', 'not_empty')
        ->rule('current_location', 'not_empty')
        ->rule('pickup_lat', 'not_empty')
        ->rule('pickup_lng', 'not_empty')
        ->rule('pickup_date', 'not_empty')
        ->rule('pickup_time', 'not_empty');
    }
    public function addbooking($post, $random_key, $password, $company_tax)
    {
		
        $firstname        = Html::chars($post['firstname']);
        $pass_logid       = '';
        $recurrent_id     = '';
        $send_mail        = 'N';
        $insert_booking   = 'N';
        $passenger_id     = $post['passenger_id'];
        $admin_company_id = isset($post['admin_company_id']) ? $post['admin_company_id'] : "";
        if ($admin_company_id != "") {
            $company_id = $admin_company_id;
        } else {
            $company_id = $this->company_id;
        }
        $search_city  = trim($post['cityname']);
        $search_cityid = trim($post['city_id']);
        if ($search_city != '') {
			$condition = array("stateinfo.cityinfo.city_name"=> Commonfunction::MongoRegex("/$search_city/i"));
		} elseif ($search_cityid != '') {
			$condition = array("stateinfo.cityinfo.city_id"=> (int)$search_cityid);
		} else {
			$condition = array("stateinfo.cityinfo.default"=> 1);
		}
		$arguments = array(
			array('$unwind' =>'$stateinfo'),
			array('$unwind' =>'$stateinfo.cityinfo'),
			array('$match' => $condition),
			array('$project' =>array(
					'_id' => 0,
					'city_id' => '$stateinfo.cityinfo.city_id',
					'city_model_fare' => '$stateinfo.cityinfo.city_model_fare',
				)
			),
			array('$limit' => 1)
		);
		$city_result = $this->mongo_db->aggregate(MDB_CSC, $arguments);
		//echo "<pre>if";print_r($city_result['result']); exit;
		$city_id = 0;
		if(!empty($city_result['result']) && count($city_result['result'][0])>0){
			$city_id = $city_result['result'][0]['city_id'];
		}
        $current_datetime        = $this->company_current_time;
        $current_datesplit       = explode(' ', $current_datetime);
        $pickup_date             = $post['pickup_date'];
        $pickup_time             = $post['pickup_time'];
        if ($pickup_date == '' || $pickup_date == 'Date') {
            $pickup_date = $current_datesplit[0];
        }
        if ($pickup_time == '' || $pickup_time == 'Now') {
            $pickup_time = $current_datesplit[1];
        }
        //$pickup_datetime = $pickup_date.' '.$pickup_time;
        $pickup_datetime = $pickup_date;
        $userid          = $this->user_id;
        if (isset($post['dispatch'])) {
            $booktype = 1;
        } else {
            $booktype = 2;
        }
        $pass_condition = (!empty($post['email'])) ? array('email'=>$post['email'],'phone'=>$post['phone']) : array('phone'=>$post['phone']);
        $passenger_exist = $this->mongo_db->findOne(MDB_PASSENGERS,$pass_condition,array('_id'));
        if ($post['passenger_id'] == '' && count($passenger_exist) == 0) {
            //Get the last object id
			//$pass_rs = $this->mongo_db->find(MDB_PASSENGERS,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
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
            $pass_rs = $this->mongo_db->find(MDB_PASSENGERS,[],$options);
			$pass_res = (!empty($pass_rs))?array($pass_rs[0]['_id']=>0):array(1);
			reset($pass_res);
			$pass_first_key = key($pass_res);
			$passengers_id = $pass_first_key+1;
            $pass_data = array('_id' => (int)$passengers_id,
                'name' => $firstname,
                'email' => $post['email'],
                'country_code' => $post['country_code'],
                'phone' => $post['phone'],
                'password' => md5($password),
                //'org_password' => $password,
                'created_date' => $current_datetime,
                'activation_key' => $random_key,
                'user_status' => ACTIVE,
                'passenger_cid' => (int)$company_id,
                'activation_status' => 1
            );
            $pass_result = $this->mongo_db->insertOne(MDB_PASSENGERS,$pass_data);
            $send_mail        = 'S';
            $passenger_id     = $passengers_id;
        } else {
            $passenger_id           = $passenger_exist['_id'];
            $name                   = explode('- (', $firstname);
            $firstname              = isset($name[0]) ? $name[0] : $firstname;
            $update_passenger_array = array(
                'name' => $firstname,
                'email' => $post['email'],
                'country_code' => $post['country_code']
            );
            $updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array('$set'=>$update_passenger_array),array('upsert'=>true));
        }
        
        $user_createdby = $this->user_id;
        /** if single booking **/
        if ($post['recurrent'] == 1) {
            $booking_key  = commonfunction::randomkey_generator();
            //Get the last object id
			//$rs = $this->mongo_db->find(MDB_PASSENGERS_LOGS,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
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
            $rs = $this->mongo_db->find(MDB_PASSENGERS_LOGS,[],$options);
			$res =  (!empty($rs))?array($rs[0]['_id']=>0):array(1);
			reset($res);
			$first_key = key($res);
			$log_id = $first_key+1;
            
            $insert_array   = array('_id' => (int)$log_id,
                'booking_key' => $booking_key,
                'passengers_id' => (int)$passenger_id,
				'driver_id' => 0,//empty insert purpose
                'taxi_id' => 0,//empty insert purpose
                'company_id' => (int)$company_id,
                'current_location' => $post['current_location'],
                'pickup_latitude' => $post['pickup_lat'],
                'pickup_longitude' => $post['pickup_lng'],
                'drop_location' => $post['drop_location'],
                'drop_latitude' => $post['drop_lat'],
                'drop_longitude' => $post['drop_lng'],
                'pickup_time' => Commonfunction::MongoDate(strtotime($pickup_datetime)),
                'no_passengers' => $post['no_passengers'],
                'approx_distance' => (float)$post['distance_km'],
                'approx_duration' => $post['total_duration'],
                'approx_fare' => (float)$post['total_fare'],
                'search_city' => (int)$city_id,
                'notes_driver' => $post['notes'],
                'faretype' => (int)$post['payment_type'],
                'fixedprice' => (float)$post['fixedprice'],
                'bookingtype' => (int)$booktype,
                'luggage' => (int)$post['luggage'],
                'bookby' => 2,
                'operator_id' => (int)$userid,
                'travel_status' => 0,
                'rating' => 0,
                'taxi_modelid' => (int)$post['taxi_model'],
                'recurrent_type' => (int)$post['recurrent'],
                'company_tax' => (int)$post['company_tax'],
                'account_id' => 0,
                'accgroup_id' => 0,
                'createdate' => Commonfunction::MongoDate(strtotime($current_datetime)),
                'logs' => array(),
			);
            $today_result   = $this->mongo_db->insertOne(MDB_PASSENGERS_LOGS, $insert_array);
            $trip_id        = $log_id;
            $pass_logid     = $log_id;
            $insert_booking = 'S';
            if(empty($today_result->getwriteErrors())){
				$split_id=0;
				$args  = array(array('$unwind' => '$split_details'),
					array('$project' => array('split_id' => '$split_details.split_id')),
					array('$sort' => array('split_details.split_id' => -1))
				);
				$get_id = $this->mongo_db->aggregate(MDB_CSC,$args);	
				$split_id = (!empty($get_id['result'])) ? $get_id['result'][0]['split_id'] : 0;
				$split_id +=1;					
				$insert_split = array('split_details' => array('split_id' => (int)$split_id,
									'friends_p_id' => (int)$passenger_id,
									'fare_percentage' => 100,
									'createdate' => Commonfunction::MongoDate(strtotime($current_datetime)),
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
				$this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$trip_id),array('$push'=>$insert_split),array('upsert' => true));
				
				/*$insert_array = array(
                            "_id" => $log_id,
                            "available_drivers" => 0,
                            "total_drivers" => 0,
                            "selected_driver" => 0,
                            "status" => 0,
                            "rejected_timeout_drivers" => "",
                            "createdate" => Commonfunction::MongoDate(strtotime('2015-07-10'))
                        );
				//Inserting to Transaction Table 
				$transaction        = $this->mongo_db->insertOne(MDB_REQUEST_HISTORY, $insert_array);*/
                        
			}          
        }
        /** if single booking end**/
        /** if recurrent booking **/
        if ($post['dispatch'] != '') {
           // $dispatch_data = $this->mongo_db->find(MDB_COMPANY,array('_id'=>(int)$company_id),array('dispatch_algorithm'=>1))->sort(array('dispatch_algorithm.aid'=>-1))->limit(1);
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                    'dispatch_algorithm'=>1                     
                  ],
                'sort'=>[
                    'dispatch_algorithm.aid'=>-1
                    ],
                'limit'=>1
                ];
            $dispatch_data = $this->mongo_db->find(MDB_COMPANY,['_id'=>(int)$company_id],$options);
            $companydispatch = (!empty($dispatch_data))?$dispatch_data:array();
            if (count($companydispatch) > 0 || $company_id ==0) {                
                if(count($companydispatch) > 0 ){
					//$company_dispatch  = $companydispatch[$company_id]['dispatch_algorithm'];
                    $company_dispatch  = $companydispatch[0]['dispatch_algorithm'];
                    $tdispatch_type    = isset($company_dispatch[0]['labelname']) ? $company_dispatch[0]['labelname'] : '';
					$hide_customer     = isset($company_dispatch[0]['hide_customer']) ? $company_dispatch[0]['hide_customer'] : '';
					$hide_droplocation = isset($company_dispatch[0]['hide_droplocation']) ? $company_dispatch[0]['hide_droplocation'] : '';
                } else {
                    //$data = array_reverse($company_dispatch);
                    $tdispatch_type    = 1;
                }
                $pass_logid = ($trip_id != "")?$this->get_autodispatch($trip_id):'';
                if ($tdispatch_type == 1 && $pass_logid != '') {
                    $booking_details   = $this->get_bookingdetails($pass_logid, $company_id);
                    $latitude          = isset($booking_details[0]["pickup_latitude"])?$booking_details[0]["pickup_latitude"]:"";
                    $longitude         = isset($booking_details[0]["pickup_longitude"])?$booking_details[0]["pickup_longitude"]:"";
                    $miles             = '';
                    $no_passengers     = isset($booking_details[0]["no_passengers"])?$booking_details[0]["no_passengers"]:0;
                    $taxi_fare_km      = isset($booking_details[0]["min_fare"])?$booking_details[0]["min_fare"]:0;
                    $taxi_model        = isset($booking_details[0]["taxi_modelid"])?$booking_details[0]["taxi_modelid"]:0;
                    $taxi_type         = '';
                    $maximum_luggage   = isset($booking_details[0]["luggage"])?$booking_details[0]["luggage"]:"";
                    $company_id        = isset($booking_details[0]["company_id"])?$booking_details[0]["company_id"]:0;
                    $cityname          = '';
                    $search_driver     = '';
                    $driver_details    = $this->search_driver_location($latitude, $longitude, $miles, $no_passengers, $_REQUEST, $taxi_fare_km, $taxi_model, $taxi_type, $maximum_luggage, $cityname, $pass_logid, $company_id, $search_driver);
                    //print_r($driver_details);exit;
                    $nearest_driver    = '';
                    $a                 = 1;
                    $temp              = '10000';
                    $prev_min_distance = '10000~0';
                    $taxi_id           = '';
                    $temp_driver       = 0;
                    $nearest_key       = 0;
                    $prev_key          = 0;
                    $driver_list       = "";
                    $available_drivers = "";
                    $nearest_driver_id = $nearest_taxi_id = "";
                    $total_count       = count($driver_details);
                    //exit;
                    if ($total_count > 0) {
                        /*Nearest driver calculation */
                        $nearest_driver_ids = array();
                        $nearest_count      = 1;
                        //print_r($driver_details);
                        foreach ($driver_details as $key => $value) {
                            $prev_min_distance = explode('~', $prev_min_distance);
                            $prev_key          = $prev_min_distance[1];
                            $prev_min_distance = $prev_min_distance[0];
                            //to check the driver has trip already
                            $driver_has_trip   = $this->check_driver_has_trip_request($value['driver_id']);
                            $current_request   = $this->currently_driver_has_trip_request($value['driver_id']);
                            if ($driver_has_trip == 0 && $current_request == 0) {
                                $nearest_driver_ids[] = $value['driver_id'];
                                if ($nearest_count == 1) {
                                    /*$nearest_driver_id = isset($driver_details[0]['_id']['driver_id']) ? $driver_details[0]['_id']['driver_id'] : 0;
                                    $nearest_taxi_id   = isset($driver_details[0]['_id']['taxi_id']) ? $driver_details[0]['_id']['taxi_id'] : 0;*/
                                    $nearest_driver_id = isset($driver_details[0]['driver_id']) ? $driver_details[0]['driver_id'] : 0;
                                    $nearest_taxi_id   = isset($driver_details[0]['taxi_id']) ? $driver_details[0]['taxi_id'] : 0;
                                }
                                $nearest_count++;
                            }
                            //checking with previous minimum 
                            if ($value['distance'] < $prev_min_distance) {
                                //new minimum distance
                                $nearest_key       = $key;
                                $prev_min_distance = $value['distance'] . '~' . $key;
                            } else {
                                //previous minimum
                                $nearest_key       = $prev_key;
                                $prev_min_distance = $prev_min_distance . '~' . $prev_key;
                            }
                        }
                        /*echo '<pre>';print_r($driver_details);
                        echo $nearest_driver_id;exit;*/
                        $drivers_count = count($nearest_driver_ids);
                        if ($nearest_driver_ids != NULL) {
                            $nearest_driver_ids = implode(",", $nearest_driver_ids);
                        }
                        /*Nearest driver calculation End*/
                        $miles_or_km            = round(($prev_min_distance), 2);
                        $driver_away_in_km      = (ceil($miles_or_km * 100) / 100);
                        $duration               = '+1 minutes';
                        $current_datetime       = date('Y-m-d H:i:s', strtotime($duration, strtotime($current_datetime)));
                        /****** Estimated Arival *************/
                        $taxi_speed             = $this->api_model->get_taxi_speed($nearest_taxi_id);
                        $estimated_time         = $this->api_model->estimated_time($driver_away_in_km, $taxi_speed);
                        /**************************************/
                        //to get nearest driver's company id
                        $driver_company_details = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=>(int)$nearest_driver_id),array('company_id','name','phone'));
                        $companyid              = isset($driver_company_details['company_id']) ? $driver_company_details['company_id'] :0;
                        $companyName            = $this->get_company_name($companyid);
                        $driver_name            = (isset($driver_company_details['name'])) ? $driver_company_details['name'] : "";
                        $driver_phone           = (isset($driver_company_details['phone'])) ? $driver_company_details['phone'] : "";
                        $driver_reachable_no    = (isset($driver_company_details['phone'])) ? $driver_company_details['phone'] : "";
                        //condition checked to update the company id and name only in admin side
                       if ($this->usertype == 'A') {
                            $updatequery = array(
                                'driver_id'=>(int)$nearest_driver_id,
                                'taxi_id'=>(int) $nearest_taxi_id,
                                'company_id'=>(int)$companyid,
                                'travel_status'=>7,
                                'driver_reply'=>'',
                                'msg_status'=>'U',
                                'dispatch_time'=> Commonfunction::MongoDate(strtotime($current_datetime))
                            );
                        } else {
                            $updatequery = array(
                                'driver_id'=>(int)$nearest_driver_id,
                                'taxi_id'=>(int)$nearest_taxi_id,
                                'travel_status'=>7,
                                'driver_reply'=>'',
                                'msg_status'=>'U',
                                'dispatch_time'=> Commonfunction::MongoDate(strtotime($current_datetime))
                            );
                        }
                        $updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$pass_logid),array('$set'=>$updatequery),array('upsert'=>true));
                        /* Create Log */
                        $company_id   = $this->company_id;
                        $userid       = $this->user_id;
                        $driver_data    = $this->get_driver_profile_details($nearest_driver_id);
                        $log_message  = __('log_message_dispatched');
                        $log_message  = str_replace("PASS_LOG_ID", $pass_logid, $log_message);
                        $log_booking  = __('log_booking_dispatched');
                        $log_booking  = isset($driver_data['name']) ? str_replace("DRIVERNAME", $driver_data['name'], $log_booking) : '';
                        $log_status   = $this->create_logs($pass_logid, $company_id, $userid, $log_message, $log_booking);
                        /*
                    ?>
						<script type="text/javascript">load_logcontent();</script>
					<?php */
                        /***** Insert the druiver details to driver request table ************/
                        $insert_array = array(
                            "_id" => $pass_logid,
                            "available_drivers" => $nearest_driver_ids,
                            "total_drivers" => $nearest_driver_ids,
                            "selected_driver" => (int)$nearest_driver_id,
                            "status" => 0,
                            "rejected_timeout_drivers" => "",
                            "createdate" => Commonfunction::MongoDate(strtotime($current_datetime))
                        );
                        //Inserting to Transaction Table 
                        $transaction        = $this->mongo_db->insertOne(MDB_REQUEST_HISTORY, $insert_array);
                        //print_r($insert_array);
                        $detail       = array(
                            "passenger_tripid" => $pass_logid,
                            "notification_time" => ""
                        );
                        $msg          = array(
                            "message" => __('api_request_confirmed_passenger'),
                            "status" => 1,
                            "detail" => $detail
                        );
                    }
                   // exit;
                }
                /** Auto Dispatch **/
            }
        }
        $req_result['send_mail']      = $send_mail;
        $req_result['pass_logid']     = $pass_logid;
        $req_result['recurrent_id']   = $recurrent_id;
        $req_result['insert_booking'] = $insert_booking;
        return $req_result;
    }
    public function get_autodispatch($pass_logid)
    {
        $company_id       = $this->company_id;
        $current_datetime = $this->company_current_time;
        //MongoDB
        $match_array['_id'] = (int) $pass_logid;
        $match_array['driver_id'] = 0;
        //echo "<pre>";  print_r($match_array); exit;
        $arguments = array(
            array(
                '$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => 'company_id',
                    'foreignField' => "_id",
                    'as' => "company"
                )
            ),
            array(
                '$match' => $match_array
            ),
            array(
                '$project' => array(
                    '_id' => '$_id'
                )
            ),
        );
        $result    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
        return (!empty($result['result'])) ? $result['result'][0]['_id'] : array();
    }    
    public function get_all_booking_list_all($array)
    {
		$result = array();        
		$travel_status       = isset($array['travel_status']) ? $array['travel_status'] : '';
        $driver_reply_cancel = isset($array['driver_reply_cancel']) ? $array['driver_reply_cancel'] : '';
        $manage_status       = isset($array['manage_status']) ? $array['manage_status'] : '';
        $search_txt          = isset($array['search_txt']) ? $array['search_txt'] : '';
        $search_location     = isset($array['search_location']) ? $array['search_location'] : '';
        $filter_date         = isset($array['filter_date']) ? $array['filter_date'] : '';
        $to_date             = isset($array['to_date']) ? $array['to_date'] : '';
        $booking_filter      = isset($array['booking_filter']) ? $array['booking_filter'] : '';
        $company_id          = isset($array['company_id']) ? $array['company_id'] : $_SESSION['company_id'];
        $taxi_model_id       = (isset($array['select_taxi_model']) && $array['select_taxi_model'] > 0) ? $array['select_taxi_model'] : '';
        $fromdate            = ($filter_date!="")?$filter_date:"";
        $todate              = ($to_date!="") ?$to_date:"";       
        $date                       = date('Y-m-d', strtotime($array['current_time']));
        $currentdate                = $date . ' 00:00:00';
        $enddate                    = $date . ' 23:59:59';
        $company_id                 = $this->company_id;
        $travel_status              = Commonfunction::mongo_format_array(explode(",", $travel_status));
        $two_days_before            = date( 'Y-m-d 00:00:00', strtotime( $date . ' 0 day' ) );
        $match_query = array();
        $match_query['bookby']        = 2;
        $match_query['travel_status'] = array('$in' => $travel_status);
		if ($company_id!="" && $company_id!=0) {
			$match_query['company._id'] = (int)$company_id;
        }
        if ($driver_reply_cancel == "") {
            $match_query['driver_reply'] = array('$nin' => array('C','R'));
            $key = array_search('8', $travel_status);
            if (false !== $key) {
                unset($travel_status[$key]);
            }
            $match_query['travel_status'] = array('$in' => $travel_status);
        }
        if ($fromdate != '' && $todate != '') {
            $srch_query2 = array(
					array('$or' =>array(
					array('$and' => array(array('pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($fromdate)))),
						array('pickup_time' => array('$lte' => Commonfunction::MongoDate(strtotime($todate)))))),
					array('$and' => array(array('actual_pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($fromdate)))),
						array('actual_pickup_time' => array('$lte' => Commonfunction::MongoDate(strtotime($todate))))))
			)));
        }elseif($fromdate != '' || $todate != ''){
            $datesearch = ($to_date != '') ? $to_date : $filter_date;
            $dateArr    = explode(" ", $datesearch);
            $staDate    = $dateArr[0] . ' 00:00:01';
            $endDate    = $dateArr[0] . ' 23:59:59';
            $srch_query2 = array(
					array('$or' =>array(
					array('$and' => array(array('pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($staDate)))),
						array('pickup_time' => array('$lte' => Commonfunction::MongoDate(strtotime($endDate)))))),
					array('$and' => array(array('actual_pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($staDate)))),
						array('actual_pickup_time' => array('$lte' => Commonfunction::MongoDate(strtotime($endDate))))))
			)));
        }else{
            $match_query['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($two_days_before)));
        }
        if ($manage_status == 0) {
            $match_query['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($two_days_before)));
        }
		$matchquery = $match_query;
		if($search_txt!=""){
			$srch_query = array(
				array("\$or"=>array(array( 'passengers.name' => Commonfunction::MongoRegex("/$search_txt/i")) , 
				array( 'people.name' => Commonfunction::MongoRegex("/$search_txt/i") ),
				array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$search_txt/i") ),
				array( 'passengers.phone' => Commonfunction::MongoRegex("/$search_txt/i") ),
				array( '_id' => (int)$search_txt ),
				array( 'people.phone' => Commonfunction::MongoRegex("/$search_txt/i") ) ) ) 
			);
		}
		//print_r($matchquery); exit;
		if($search_location != '') {
			$srch_query1 = array(
				array("\$or"=>array(array( 'current_location' => Commonfunction::MongoRegex("/$search_location/i")) , 
				array( 'drop_location' => Commonfunction::MongoRegex("/$search_location/i") ) )  
			) );
		}
		$srch_query = (isset($srch_query))?$srch_query:array();
		$srch_query1 = (isset($srch_query1))?$srch_query1:array();
		$srch_query2 = (isset($srch_query2))?$srch_query2:array();
		$arr_merge = array_merge($srch_query,$srch_query1);
		$arr_merge = array_merge($arr_merge,$srch_query2);
		$and_arr = (!empty($arr_merge))?array( "\$and" => $arr_merge):array();
		$matchquery = array_merge($matchquery,$and_arr);
		//echo "<pre>"; print_r($matchquery); exit;
        $arguments = array(
           array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'driver_id',
                    'foreignField' => "_id",
                    'as' => "people"
                )
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => 'company_id',
                    'foreignField' => "_id",
                    'as' => "company"
                )
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => 'passengers_id',
                    'foreignField' => "_id",
                    'as' => "passengers"
                )
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_MOTOR_MODEL,
                    'localField' => 'taxi_modelid',
                    'foreignField' => "_id",
                    'as' => "motormodel"
                )
            ),
            array(
                  '$lookup' => array(
                    'from' => MDB_REQUEST_HISTORY,
                    'localField' => '_id',
                    'foreignField' => "_id",
                    'as' => "driver_request"
                )
            ),
            array(
                  '$lookup' => array(
                    'from' => MDB_TRANSACTION,
                    'localField' => '_id',
                    'foreignField' => "passengers_log_id",
                    'as' => "trans"
                )
            ),
            array(
                '$match' => $matchquery
            ),
            array(
                '$project' => array(
                    '_id'=>0,
                    'company_id'=>'$company._id',
                    'notes'=>'$notes_driver',
                    'pickup_time'=>'$pickup_time',
                    'act_pickuptime' => array('$sum' => array('$cond' => array(array('$eq' => array('$actual_pickup_time',Commonfunction::MongoDate('0000-00-00 00:00:00'))),'$pickup_time','$actual_pickup_time'))),//Don't use strtotime for 0000-00-00 00:00:00
                    'pickup_latitude'=>'$pickup_latitude',
                    'pickup_longitude'=>'$pickup_longitude',
                    'drop_latitude'=>'$drop_latitude',
                    'drop_longitude'=>'$drop_longitude',
                    'no_passengers'=>'$no_passengers',
                    'current_location'=>'$current_location',
                    'drop_location'=>'$drop_location',
                    'dispatch_time'=>'$dispatch_time',
                    'travel_status'=>'$travel_status',
                    'driver_reply'=>'$driver_reply',
                    'approx_distance'=>'$approx_distance',
                    'approx_fare'=>'$approx_fare',
                    'company_name'=>'$company.companydetails.company_name',
                    'pass_logid'=>'$_id',
                    'passenger_name'=>'$passengers.name',
                    'passenger_phone'=>'$passengers.phone',
                    'passenger_country_code'=>'$passengers.country_code',
                    'driver_name'=>'$people.name',
                    'driver_phone'=>'$people.phone',
                    'driver_id'=>'$people._id',
                    'model_name'=>'$motormodel.model_name',
                    'total_drivers'=>'$driver_request.total_drivers',
                    'fare'=>'$trans.fare',
                    'distance'=>'$trans.distance',
                    'createdate' => '$createdate',
                    'bookingtype' => '$bookingtype',
                    'dispatch_time' => '$dispatch_time',
                    'driver_reply' => '$driver_reply'
                )
            ),
            array(
                '$sort' => array(
                    '_id' => -1
                )
            )
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
        //echo '<pre>';print_r($arguments);exit;
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
			
				$temp_arr['company_id'] = !empty($r['company_id']) ? $r['company_id'][0]:'';
				$temp_arr['company_name'] = !empty($r['company_name']) ? $r['company_name'][0]:'';
				$temp_arr['passenger_name'] = !empty($r['passenger_name']) ? $r['passenger_name'][0]:'';
				$temp_arr['passenger_phone'] = !empty($r['passenger_phone']) ? $r['passenger_phone'][0]:'';
				$temp_arr['passenger_country_code'] = !empty($r['passenger_country_code']) ? $r['passenger_country_code'][0]:'';
				$temp_arr['driver_name'] = !empty($r['driver_name']) ? $r['driver_name'][0]:'';
				$temp_arr['driver_phone'] = !empty($r['driver_phone']) ? $r['driver_phone'][0]:'';
				$temp_arr['driver_id'] = !empty($r['driver_id']) ? $r['driver_id'][0]:'';
				$temp_arr['model_name'] = !empty($r['model_name']) ? $r['model_name'][0]:'';
				$temp_arr['total_drivers'] = !empty($r['total_drivers']) ? $r['total_drivers'][0]:'';
				$temp_arr['distance'] = !empty($r['distance']) ? $r['distance'][0]:'';
				$temp_arr['fare'] = !empty($r['fare']) ? $r['fare'][0]:'';
				$temp_arr['pass_logid'] = isset($r['pass_logid']) ? $r['pass_logid']:'';
				$temp_arr['notes'] = isset($r['notes']) ? $r['notes']:'';
				$temp_arr['createdate'] = isset($r['createdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']):'';
				$temp_arr['bookingtype'] = isset($r['bookingtype']) ? $r['bookingtype']:'';
				$temp_arr['bookby'] = isset($r['bookby']) ? $r['bookby']:'';
				$temp_arr['pickup_latitude'] = isset($r['pickup_latitude']) ? $r['pickup_latitude']:'';
				$temp_arr['pickup_longitude'] = isset($r['pickup_longitude']) ? $r['pickup_longitude']:'';
				$temp_arr['drop_latitude'] = isset($r['drop_latitude']) ? $r['drop_latitude']:'';
				$temp_arr['drop_longitude'] = isset($r['drop_longitude']) ? $r['drop_longitude']:'';
				$temp_arr['no_passengers'] = isset($r['no_passengers']) ? $r['no_passengers']:'';
				$temp_arr['current_location'] = isset($r['current_location']) ? $r['current_location']:'';
				$temp_arr['drop_location'] = isset($r['drop_location']) ? $r['drop_location']:'';
				$temp_arr['travel_status'] = isset($r['travel_status']) ? $r['travel_status']:'';
				$temp_arr['driver_reply'] = isset($r['driver_reply']) ? $r['driver_reply']:'';
				$temp_arr['approx_distance'] = isset($r['approx_distance']) ? $r['approx_distance']:'';
				$temp_arr['approx_fare'] = isset($r['approx_fare']) ? $r['approx_fare']:'';
				$temp_arr['dispatch_time'] = isset($r['dispatch_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['dispatch_time']):'';
				$temp_arr['pickup_time'] = isset($r['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
				$temp_arr['act_pickuptime'] = isset($r['act_pickuptime']) ? $r['act_pickuptime']:'';
				
				$result[] = $temp_arr;
			}
		}
		return $result;
    }
	
	/*public function get_tripid(){  
		$rs = $this->mongo_db->find(MDB_PASSENGERS_LOGS, array(),array('_id'))>sort(array('_id'=>-1))>limit(1);
		$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
		reset($res);
		$first_key = key($res);
		$inc_id = $first_key;
		return $inc_id;
    } */
   
    public function load_logcontent()
    {
        $company_id       = $this->company_id;
        $user_createdby   = $this->user_id;
        $current_datetime = $this->company_current_time;
        $currentdate      = date('Y-m-d', strtotime($current_datetime));
        $sdate            = $currentdate . ' 00:00:00';
		//$result = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array());
		$args = array(array('$unwind' => '$logs'),
						array('$match' => array('log_userid' => (int)$user_createdby,
												'log_createdate' => array('$gte' => Commonfunction::MongoDate(strtotime($sdate))))),
						array('$sort' => array('_id' => -1)),
						array('$limit' => 50)
					);
		$result = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$args);
    }
    public function edit_bookingdetails($pass_logid = '')
    {
        $result = $temp_arr = array();
        $company_id    = $this->company_id;
        if ($company_id != "" && $company_id != 0) {
            //$match_array['company_id'] = (int) $company_id;
        }
        $match_array['_id'] = (int) $pass_logid;
        //echo "<pre>";  print_r($match_array); exit;
        if (FARE_SETTINGS == 2 && !empty($company_id)) {
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => 'company_id',
                        'foreignField' => "_id",
                        'as' => "company"
                    )
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_PASSENGERS,
                        'localField' => 'passengers_id',
                        'foreignField' => "_id",
                        'as' => "passengers"
                    )
                ),
                array(
                    '$unwind' => '$passengers'
                ),
                array(
                    '$match' => $match_array
                ),
                array(
                    '$project' => array(
                        'pass_logid' => '$_id',
                        'passenger_name' => '$passengers.name',
                        'passengers_id' => '$passengers._id',
                        'passenger_email' => '$passengers.email',
                        'passenger_phone' => '$passengers.phone',
                        'pickup_latitude' => '$pickup_latitude',
                        'pickup_longitude' => '$pickup_longitude',
                        'no_passengers' => '$no_passengers',
                        'luggage' => '$luggage',
                        'company_id' => '$company_id',
                        'approx_fare' => '$approx_fare',
                        'approx_distance' => '$approx_distance',
                        'country_code' => '$passengers.country_code',
                        'current_location' => '$current_location',
                        'drop_location' => '$drop_location',
                        'drop_latitude' => '$drop_latitude',
                        'drop_longitude' => '$drop_longitude',
                        'pickup_time' => '$pickup_time',
                        'notes_driver' => '$notes_driver',
                        'taxi_modelid' => '$taxi_modelid',
                        'search_city' => '$search_city',
                        'travel_status' => '$travel_status',
                    )
                ),
            );
            $res    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
            
        } else {
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => 'company_id',
                        'foreignField' => "_id",
                        'as' => "company"
                    )
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_PASSENGERS,
                        'localField' => 'passengers_id',
                        'foreignField' => "_id",
                        'as' => "passengers"
                    )
                ),
                array(
                    '$unwind' => '$passengers'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_MOTOR_MODEL,
                        'localField' => 'taxi_modelid',
                        'foreignField' => "_id",
                        'as' => "motormodel"
                    )
                ),
                array(
                    '$unwind' => '$motormodel'
                ),
                array(
                    '$match' => $match_array
                ),
                array(
                    '$project' => array(
                        'pass_logid' => '$_id',
                        'passengers_id' => '$passengers._id',
                        'passenger_name' => '$passengers.name',
                        'passenger_email' => '$passengers.email',
                        'passenger_phone' => '$passengers.phone',
                        'min_fare' => '$motormodel.min_fare',
                        'pickup_latitude' => '$pickup_latitude',
                        'pickup_longitude' => '$pickup_longitude',
                        'no_passengers' => '$no_passengers',
                        'taxi_modelid' => '$motormodel._id',
                        'luggage' => '$luggage',
                        'company_id' => '$company_id',
                        'approx_fare' => '$approx_fare',
                        'approx_distance' => '$approx_distance',
                        'approx_duration' => '$approx_duration',
                        'country_code' => '$passengers.country_code',
                        'current_location' => '$current_location',
                        'drop_location' => '$drop_location',
                        'drop_latitude' => '$drop_latitude',
                        'drop_longitude' => '$drop_longitude',
                        'pickup_time' => '$pickup_time',
                        'notes_driver' => '$notes_driver',
                        'search_city' => '$search_city',
                        'travel_status' => '$travel_status',
                    )
                ),
            );
            $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
        }
        //echo '<pre>';print_r($res);exit;
        if(!empty($res['result'])){
			$r = $res['result'][0];
			$temp_arr['passengers_log_id'] = isset($r['pass_logid']) ? $r['pass_logid']:'';
			$temp_arr['pass_logid'] = isset($r['pass_logid']) ? $r['pass_logid']:'';
			$temp_arr['passengers_id'] = isset($r['passengers_id']) ? $r['passengers_id']:'';
			$temp_arr['passenger_name'] = isset($r['passenger_name']) ? $r['passenger_name']:'';
			$temp_arr['passenger_phone'] = isset($r['passenger_phone']) ? $r['passenger_phone']:'';
			$temp_arr['passenger_email'] = isset($r['passenger_email']) ? $r['passenger_email']:'';
			$temp_arr['country_code'] = isset($r['country_code']) ? $r['country_code']:'';
			$temp_arr['pickup_latitude'] = isset($r['pickup_latitude']) ? $r['pickup_latitude']:'';
			$temp_arr['pickup_longitude'] = isset($r['pickup_longitude']) ? $r['pickup_longitude']:'';
			$temp_arr['drop_latitude'] = isset($r['drop_latitude']) ? $r['drop_latitude']:'';
			$temp_arr['drop_longitude'] = isset($r['drop_longitude']) ? $r['drop_longitude']:'';
			$temp_arr['pickup_time'] = isset($r['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
			$temp_arr['travel_status'] = isset($r['travel_status']) ? $r['travel_status']:'';
			$temp_arr['search_city'] = isset($r['search_city']) ? $r['search_city']:'';
			$temp_arr['notes_driver'] = isset($r['notes_driver']) ? $r['notes_driver']:'';
			$temp_arr['current_location'] = isset($r['current_location']) ? $r['current_location']:'';
			$temp_arr['approx_distance'] = isset($r['approx_distance']) ? $r['approx_distance']:'';
			$temp_arr['approx_fare'] = isset($r['approx_fare']) ? $r['approx_fare']:'';
			$temp_arr['approx_duration'] = isset($r['approx_duration']) ? $r['approx_duration']:'';
			$temp_arr['min_fare'] = isset($r['min_fare']) ? $r['min_fare']:'';
			$temp_arr['no_passengers'] = isset($r['no_passengers']) ? $r['no_passengers']:'';
			$temp_arr['company_id'] = isset($r['company_id']) ? $r['company_id']:'';
			$temp_arr['drop_location'] = isset($r['drop_location']) ? $r['drop_location']:'';
			$temp_arr['taxi_modelid'] = isset($r['taxi_modelid']) ? $r['taxi_modelid']:'';
			
			$result[] = $temp_arr;
		}
		return $result;
    }
    
    public function validate_dispatchbooking_edit($arr)
    {
        return Validation::factory($arr)
        ->rule('edit_firstname', 'not_empty')
        //->rule('edit_firstname', 'min_length', array(':value', '3'))            
        //->rule('firstname', 'max_length', array(':value', '32'))            
        //->rule('edit_email', 'not_empty')
        ->rule('edit_email', 'email')->rule('edit_email', 'max_length', array(
            ':value',
            '50'
        ))->rule('edit_country_code', 'not_empty')
        ->rule('edit_phone', 'not_empty')
        ->rule('edit_current_location', 'not_empty')
        ->rule('edit_pickup_lat', 'not_empty')
        ->rule('edit_pickup_lng', 'not_empty')
        /*->rule('drop_location', 'not_empty')
        ->rule('drop_lat', 'not_empty')
        ->rule('drop_lng', 'not_empty')*/ 
        //->rule('luggage', 'numeric')            
        //->rule('no_passengers', 'numeric')            
        //->rule('edit_pickup_time', 'not_empty')
        ->rule('edit_pickup_date', 'not_empty');
    }
    public function updatebooking($post, $random_key, $password)
    {
        $firstname   = Html::chars($post['edit_firstname']);
        $send_mail   = 'N';
        
        $search_cityid     = $post['edit_city_id'];
        $search_city = trim($post['edit_cityname']);
        if ($search_city != '') {
			$condition = array("stateinfo.cityinfo.city_name"=> Commonfunction::MongoRegex("/$search_city/i"));
		} elseif ($search_cityid != '') {
			$condition = array("stateinfo.cityinfo.city_id"=> (int)$search_cityid);
		} else {
			$condition = array("stateinfo.cityinfo.default"=> 1);
		}
		$arguments = array(
			array('$unwind' =>'$stateinfo'),
			array('$unwind' =>'$stateinfo.cityinfo'),
			array('$match' => $condition),
			array('$project' =>array(
					'_id' => 0,
					'city_id' => '$stateinfo.cityinfo.city_id',
					'city_model_fare' => '$stateinfo.cityinfo.city_model_fare',
				)
			),
			array('$limit' => 1)
		);
		$city_result = $this->mongo_db->aggregate(MDB_CSC, $arguments);
		//echo "<pre>if";print_r($city_result['result']); exit;
		$city_id = 0;
		if(!empty($city_result['result']) && count($city_result['result'][0])>0){
			$city_id = $city_result['result'][0]['city_id'];
		}
        $passenger_id     = $post['edit_passenger_id'];
        $admin_company_id = isset($post['edit_admin_company_id']) ? $post['edit_admin_company_id'] : "";
        $company_id = ($admin_company_id != "") ?$admin_company_id:$this->company_id;
        $current_datetime  = $this->company_current_time;
        $current_datesplit = explode(' ', $current_datetime);
        $pickup_datetime   = $post['edit_pickup_date'];
        $user_createdby    = $userid = $this->user_id;
        $booktype = (isset($post['dispatch'])) ? 1 : 2;
        $pass_condition = (!empty($post['edit_email'])) ? array('email'=>$post['edit_email'],'phone'=>$post['edit_phone']) : array('phone'=>$post['edit_phone']);
        $passenger_exist = $this->mongo_db->findOne(MDB_PASSENGERS,$pass_condition,array('_id'));
        
        if (count($passenger_exist) == 0) {
            //Get the last object id
			//$pass_rs = $this->mongo_db->find(MDB_PASSENGERS,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
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
            $pass_rs = $this->mongo_db->find(MDB_PASSENGERS,[],$options);
			$pass_res =  (!empty($pass_rs))?array($pass_rs[0]['_id']=>0):array(1);
			reset($pass_res);
			$pass_first_key = key($pass_res);
			$passengers_id = $pass_first_key+1;
            $insert_array     = array('_id' => (int)$passengers_id,
                'name' => $firstname,
                'email' => $post['edit_email'],
                'phone' => $post['edit_phone'],
                'country_code' => $post['edit_country_code'],
                'password' => md5($password),
                //'org_password' => $password,
                'created_date' => $current_datetime,
                'activation_key' => $random_key,
                'user_status' => ACTIVE,
                'passenger_cid' => (int)$company_id,
                'activation_status' => 1
            );
            //Inserting to PASSENGERS Table 
            $insert_passenger = $this->mongo_db->insertOne(MDB_PASSENGERS, $insert_array);
            $send_mail        = 'S';
            $passenger_id     = $passengers_id;
        } else {
            $passenger_id           = $passenger_exist['_id'];
            $name                   = explode('- (', $firstname);
            $firstname              = isset($name[0]) ? $name[0] : $firstname;
            $update_passenger_array = array(
                'name' => $firstname,
                'email' => $post['edit_email']
            );
            $updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array('$set'=>$update_passenger_array),array('upsert'=>true));
        }
        $companyName  = $this->get_company_name($company_id);
        $update_array = array(
            "passengers_id" => (int)$passenger_id,
            "company_id" => (int)$company_id,
            "current_location" => $post['edit_current_location'],
            "pickup_latitude" => $post['edit_pickup_lat'],
            "pickup_longitude" => $post['edit_pickup_lng'],
            "drop_location" => $post['edit_drop_location'],
            "drop_latitude" => $post['edit_drop_lat'],
            "drop_longitude" => $post['edit_drop_lng'],
            "pickup_time" => Commonfunction::MongoDate(strtotime($pickup_datetime)),
            "no_passengers" => (int)$post['edit_no_passengers'],
            "approx_distance" => (float)$post['edit_distance_km'],
            "approx_duration" => $post['edit_total_duration'],
            "approx_fare" => (float)$post['edit_total_fare'],
            "search_city" => (int)$city_id,
            "notes_driver" => $post['edit_notes'],
            "faretype" => (int)$post['edit_payment_type'],
            "fixedprice" => (float)$post['edit_fixedprice'],
            "bookingtype" => (int)$booktype,
            "luggage" => (int)$post['edit_luggage'],
            "bookby" => 2,
            "operator_id" => (int)$userid,
            "taxi_modelid" => (int)$post['edit_taxi_model'],
            "company_tax" => (float)$post['edit_company_tax'],
            "notification_status" => 6
        );
        //echo '<pre>';print_r($update_array);exit;
        if ($company_id == 0) {
            unset($update_array['company_id']);
            unset($update_array['company_name']);
        }
        //print_r($update_array);exit;
        $updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS, array('_id'=>(int)$post['edit_pass_logid']),array('$set'=>$update_array),array( 'upsert' => false));

        if ($post['update_dispatch'] != '') {
            //echo "1";exit;
            $trip_id          = $post['edit_pass_logid'];
            $company_id       = $this->company_id;            
            //$dispatch_data = $this->mongo_db->find(MDB_COMPANY,array('_id'=>(int)$company_id),array('dispatch_algorithm'=>1))->sort(array('dispatch_algorithm.aid'=>-1))->limit(1);
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                    'dispatch_algorithm'=>1
                ],
                'sort'=>[
                    'dispatch_algorithm.aid'=>-1
                    ],
                'limit'=>1
                ];
            $dispatch_data = $this->mongo_db->find(MDB_COMPANY,['_id'=>(int)$company_id],$options);
            $companydispatch = (!empty($dispatch_data))?$dispatch_data:array();
            if (count($companydispatch) > 0 || $company_id ==0) {                
                if(count($companydispatch) > 0 ){
                    
                    #Lamp 7.0 and Mongo Db 3.4 conversion
					$company_dispatch  = $companydispatch[0]['dispatch_algorithm'];
                    $tdispatch_type    = isset($company_dispatch[0]['labelname']) ? $company_dispatch[0]['labelname'] : '';
					$hide_customer     = isset($company_dispatch[0]['hide_customer']) ? $company_dispatch[0]['hide_customer'] : '';
					$hide_droplocation = isset($company_dispatch[0]['hide_droplocation']) ? $company_dispatch[0]['hide_droplocation'] : '';
                } else {
                    //$data = array_reverse($company_dispatch);
                    $tdispatch_type    = 1;
                }
                $pass_logid = ($trip_id != "")? $trip_id:0;
				//echo $pass_logid.'--'.$trip_id;exit;
			if(!isset($post['update'])) {
                if ($tdispatch_type == 1 && $pass_logid != '') {
                    $booking_details   = $this->get_bookingdetails($pass_logid, $company_id);
                    $latitude          = $booking_details[0]["pickup_latitude"];
                    $longitude         = $booking_details[0]["pickup_longitude"];
                    $miles             = '';
                    $no_passengers     = $booking_details[0]["no_passengers"];
                    $taxi_fare_km      = $booking_details[0]["min_fare"];
                    $taxi_model        = $booking_details[0]["taxi_modelid"];
                    $taxi_type         = '';
                    $maximum_luggage   = $booking_details[0]["luggage"];
                    $company_id        = $booking_details[0]["company_id"];
                    $cityname          = '';
                    $search_driver     = '';
                    $driver_details    = $this->search_driver_location($latitude, $longitude, $miles, $no_passengers, $_REQUEST, $taxi_fare_km, $taxi_model, $taxi_type, $maximum_luggage, $cityname, $pass_logid, $company_id, $search_driver);
                    //print_r($driver_details);exit;
                    $nearest_driver    = '';
                    $a                 = 1;
                    $temp              = '10000';
                    $prev_min_distance = '10000~0';
                    $taxi_id           = '';
                    $temp_driver       = 0;
                    $nearest_key       = 0;
                    $prev_key          = 0;
                    $driver_list       = "";
                    $available_drivers = "";
                    $nearest_driver_id = $nearest_taxi_id = "";
                    $total_count       = count($driver_details);
                    //exit;
                    if (count($driver_details) > 0) {
                        $nearest_count      = 1;
                        /*Nearest driver calculation */
                        $nearest_driver_ids = array();
                        foreach ($driver_details as $key => $value) {
                            $prev_min_distance = explode('~', $prev_min_distance);
                            $prev_key          = $prev_min_distance[1];
                            $prev_min_distance = $prev_min_distance[0];
                            //to check the driver has trip already
                            $driver_has_trip   = $this->check_driver_has_trip_request($value['driver_id']);
                            $current_request   = $this->currently_driver_has_trip_request($value['driver_id']);
                            if ($driver_has_trip == 0 && $current_request == 0) {
                                $nearest_driver_ids[] = $value['driver_id'];
                                if ($nearest_count == 1) {
                                    $nearest_driver_id = isset($driver_details[0]['driver_id']) ? $driver_details[0]['driver_id'] : 0;
                                    $nearest_taxi_id   = isset($driver_details[0]['taxi_id']) ? $driver_details[0]['taxi_id'] : 0;
                                }
                                $nearest_count++;
                            }
                            //echo 'near'.$nearest_driver_id;exit;
                            //checking with previous minimum 
                            if ($value['distance'] < $prev_min_distance) {
                                //new minimum distance
                                $nearest_key       = $key;
                                $prev_min_distance = $value['distance'] . '~' . $key;
                            } else {
                                //previous minimum
                                $nearest_key       = $prev_key;
                                $prev_min_distance = $prev_min_distance . '~' . $prev_key;
                            }
                        } 
                        $drivers_count = count($nearest_driver_ids);
                        if ($nearest_driver_ids != NULL) {
                            $nearest_driver_ids = implode(",", $nearest_driver_ids);
                        }
                        /*Nearest driver calculation End*/
                        $miles_or_km       = round(($prev_min_distance), 2);
                        $driver_away_in_km = (ceil($miles_or_km * 100) / 100);
                        $company_id        = $this->company_id;
                        $duration          = '+1 minutes';
                        $current_datetime  = date('Y-m-d H:i:s', strtotime($duration, strtotime($current_datetime)));
                        /****** Estimated Arival *************/
                        $taxi_speed        = $this->api_model->get_taxi_speed($nearest_taxi_id);
                        $estimated_time    = $this->api_model->estimated_time($driver_away_in_km, $taxi_speed);
                        /**************************************/
                        //to get nearest driver's company id
                        if (!empty($nearest_driver_id)) {
                            $driver_company_details = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=>(int)$nearest_driver_id),array('company_id','name','phone'));
                        }
                        $companyName         = (isset($driver_company_details[0]['name'])) ? $this->get_company_name($driver_company_details[0]['company_id']) : "";
                        $companyid           = (isset($driver_company_details[0]['company_id'])) ? $driver_company_details[0]['company_id'] : 0;
                        $driver_name         = (isset($driver_company_details[0]['name'])) ? $driver_company_details[0]['name'] : "";
                        $driver_phone        = (isset($driver_company_details[0]['phone'])) ? $driver_company_details[0]['phone'] : "";
                        $driver_reachable_no = (isset($driver_company_details[0]['phone'])) ? $driver_company_details[0]['phone'] : "";
                        //condition checked to update the company id and name only in admin side
                        if ($this->usertype == 'A') {
                            $updatequery = array('driver_id'=>(int)$nearest_driver_id,
                                'taxi_id'=>(int)$nearest_taxi_id,
                                'company_id'=>(int)$companyid,
                                'travel_status'=>7,
                                'driver_reply'=>'',
                                'msg_status'=>'U',
                                'dispatch_time'=> Commonfunction::MongoDate(strtotime($current_datetime))
                            );
                        } else {
                            $updatequery = array('driver_id'=>(int)$nearest_driver_id,
                                'taxi_id'=>(int)$nearest_taxi_id,
                                'travel_status'=>7,
                                'driver_reply'=>'',
                                'msg_status'=>'U',
                                'dispatch_time'=> Commonfunction::MongoDate(strtotime($current_datetime))
                            );
                        }
                        $updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$pass_logid),array('$set'=>$updatequery),array('upsert'=>true));
                        /* Create Log */
                        $company_id   = $this->company_id;
                        $userid       = $this->user_id;
                        $driver_data    = $this->get_driver_profile_details($nearest_driver_id);
                        $log_message  = __('log_message_dispatched');
                        $log_message  = str_replace("PASS_LOG_ID", $pass_logid, $log_message);
                        $log_booking  = __('log_booking_dispatched');
                        $log_booking  = isset($driver_data[0]['name']) ? str_replace("DRIVERNAME", $driver_data[0]['name'], $log_booking) : '';
                        $log_status   = $this->create_logs($pass_logid, $company_id, $userid, $log_message, $log_booking);
?>
						<script type="text/javascript">load_logcontent();</script>
						<?php
                        $exist_request = $this->exist_request($pass_logid);
                        if ($exist_request == 1) {
                             $delete_exist_request = $this->mongo_db->deleteOne(MDB_REQUEST_HISTORY,array('_id'=>(int)$pass_logid));
                        }
                        // Insert the driver details to driver request table /
                        $nearest_driver_ids = (!empty($nearest_driver_ids)) ? $nearest_driver_ids : '';
                        $insert_array       = array(
                            "_id" => (int)$pass_logid,
                            "available_drivers" => $nearest_driver_ids,
                            "total_drivers" => $nearest_driver_ids,
                            "selected_driver" => (int)$nearest_driver_id,
                            "status" => 0,
                            "rejected_timeout_drivers" => "",
                            "createdate" => Commonfunction::MongoDate(strtotime($current_datetime))
                        );
                        //print_r($insert_array);exit;
                        //Inserting to Driver request table Table 
                        $driver_request        = $this->mongo_db->insertOne(MDB_REQUEST_HISTORY, $insert_array);
                        $detail             = array(
                            "passenger_tripid" => $pass_logid,
                            "notification_time" => ""
                        );
                        $msg                = array(
                            "message" => __('api_request_confirmed_passenger'),
                            "status" => 1,
                            "detail" => $detail
                        );
                    }
                }
			}
                /** Auto Dispatch **/
            }
        }
        $req_result['send_mail']  = $send_mail;
        $req_result['pass_logid'] = $post['edit_pass_logid'];
        return $req_result;
    }
    public function exist_request($pass_logid)
    {
        //MongoDB
        $result = $this->mongo_db->count(MDB_REQUEST_HISTORY,array('_id'=>(int)$pass_logid),array('_id'));
        return ($result>0)?1:0;
    }
    public function get_bookingdetails($pass_logid, $company_id)
    {
        $result = array();
        if ($company_id != "" && $company_id != 0) {
            $match_array['company_id'] = (int) $company_id;
        }
        $match_array['_id'] = (int) $pass_logid;
        //echo "<pre>";  print_r($match_array); exit;
        $arguments = array(
            array(
                '$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => 'company_id',
                    'foreignField' => "_id",
                    'as' => "company"
                )
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => 'passengers_id',
                    'foreignField' => "_id",
                    'as' => "passengers"
                )
            ),
            array(
                '$unwind' => '$passengers'
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_MOTOR_MODEL,
                    'localField' => 'taxi_modelid',
                    'foreignField' => "_id",
                    'as' => "motormodel"
                )
            ),
            array(
                '$unwind' => '$motormodel'
            ),
            array('$match' => $match_array ),
            array(
                '$project' => array(
                    'pass_logid' => '$_id',
                    'passenger_name' => '$passengers.name',
                    'passenger_email' => '$passengers.email',
                    'passenger_phone' => '$passengers.phone',
                    'min_fare' => '$motormodel.min_fare',
                    'pickup_latitude' => '$pickup_latitude',
                    'pickup_longitude' => '$pickup_longitude',
                    'no_passengers' => '$no_passengers',
                    'company_name' => '$company.companydetails.company_name',
                    'taxi_modelid' => '$motormodel._id',
                    'luggage' => '$luggage',
                    'company_id' => '$company_id',
                )
            ),
        );
        $res    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				
				$temp_arr = $r;
				$temp_arr['company_name'] = !empty($temp_arr['company_name']) ? $temp_arr['company_name'][0] :'';
				$temp_arr['luggage'] = isset($temp_arr['luggage']) ? $temp_arr['luggage'] :0;
				$result[] = $temp_arr;
			}			
		}
        //echo "<pre>"; print_r($res['result']); exit;
        return $result;
    }
    public function search_driver_location($lat, $long, $distance = NULL, $no_passengers, $request, $taxi_fare_km, $taxi_model, $taxi_type, $maximum_luggage, $city_name, $sub_log_id, $company_id, $search_driver)
    {
        $assigned_driver    = $this->free_availabletaxisearch_list($taxi_type, $taxi_model, $company_id);
        $result = $driver_list       = array();
        $driver_count      = '';
        $driver_list_array = array();
        foreach ($assigned_driver as $key => $value) {
                $driver_list_array[] = (int)$value['_id']['id'];
        }
        $match_query = array();
        if (count($driver_list_array) > 0) {
            $driver_list = commonfunction::mongo_format_array($driver_list_array);
        }
        //echo '<pre>';print_r($driver_list_array);print_r($driver_list);exit;
        if ($search_driver) {
            $match_query = array('people.name'=>Commonfunction::MongoRegex("/$search_driver/i"));
        }
        if ($taxi_model) {
            $match_query = array('taxi.taxi_model' => (int)$taxi_model);
        }
        if ($taxi_type) {
            $match_query = array('taxi.taxi_type' => (int)$taxi_type);
        }
        if ($maximum_luggage) {
            $match_query = array('taxi.max_luggage' => array('$gte'=>(int)$maximum_luggage));
        }
        
        $current_datetime = $this->commonmodel->company_timezone($company_id);
        $current_time     = convert_timezone('now', $current_datetime);
        $current_date     = explode(' ', $current_time);
        $start_time       = $current_date[0] . ' 00:00:01';
        $end_time         = $current_date[0] . ' 23:59:59';
        $latitude = (float)$lat;
        $longitude = (float)$long;
        if (UNIT == 0) {
            //Get result In kilo meters
            $geonear = array('$geoNear'=> array('near' => array(
                    'type' => "Point",
                    'coordinates' => array( $longitude , $latitude )
                    ),
                    'distanceField' => "distance",
                    'spherical' => true,
                    'distanceMultiplier' => 0.001,
                    'num' => 1000000
                )
            );
        } else {
            //Get the result In Miles
            $geonear = array('$geoNear'=> array('near' => array(
                    'type' => "Point",
                    'coordinates' => array( $longitude , $latitude )
                    ),
                    'distanceField' => "distance",
                    'spherical' => true,
                    'distanceMultiplier' => 0.000621371192237,
                    'num' => 1000000
                )
            );
        }
        $match1 = array(
					"distance" => array('$lte' => (int)DEFAULTMILE),
					"people.login_status" => 'S',
					"shift_status" => "IN",
					"status" => "F"
				);
		if(!empty($driver_list))
			$match1['_id'] = array('$in'=>$driver_list);
		
        $arguments = array(
            $geonear,
            array('$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => "_id",
                    'foreignField' => "_id",
                    'as' => "people"
                )
            ),
            array('$unwind' => array('path' => '$people','preserveNullAndEmptyArrays' => true)),
            array('$match' => $match1),
            array('$sort' => array("distance" => 1)),
            array('$lookup' => array(
                    'from' => MDB_TAXI_DRIVER_MAPPING,
                    'localField' => "_id",
                    'foreignField' => "mapping_driverid",
                    'as' => "tmap"
                )
            ),
            array('$unwind' => array('path' => '$tmap','preserveNullAndEmptyArrays' => true)),
            array('$lookup' => array(
                    'from' => MDB_TAXI,
                    'localField' => "tmap.mapping_taxiid",
                    'foreignField' => "_id",
                    'as' => "taxi"
                 )
            ),
            array('$unwind' => array('path' => '$taxi','preserveNullAndEmptyArrays' => true)),
            array('$lookup' => array(
                    'from' => MDB_MOTOR_MODEL,
                    'localField' => "taxi.taxi_model",
                    'foreignField' => "_id",
                    'as' => "model"
                )
            ),
            array('$unwind' => array('path' => '$model','preserveNullAndEmptyArrays' => true)),
            array('$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => "tmap.mapping_companyid",
                    'foreignField' => "_id",
                    'as' => "comp"
                )
            ),
            array('$unwind' => array('path' => '$comp','preserveNullAndEmptyArrays' => true)),
            array('$match' => array(
                    "tmap.mapping_startdate" => array('$lte' => Commonfunction::MongoDate(strtotime($start_time))),
                    "tmap.mapping_enddate" => array('$gte' => Commonfunction::MongoDate(strtotime($end_time))),
                    "tmap.mapping_status" => 'A',
                    "taxi.taxi_model" => (int)$taxi_model
                )
            ),
            array('$group' => array("_id" => array(
                        "id" => '$_id',
                        "distance" => '$distance',
                        "distance_miles" => '$distance',
                        "update_date" => '$update_date',
                        "shift_status" => '$shift_status',
                        "status" => '$status',
                        "name" => '$people.name',
                        "driver_id" => '$people._id',
                        "phone" => '$people.phone',
                        "updatetime_difference" => '$updatetime_difference',
                        "d_photo" => '$people.profile_picture',
                        "location" => '$location',
                        "company_name" => '$comp.companydetails.company_name',
                        "company_id" => '$comp._id',
                        "taxi_no" => '$taxi.taxi_no',
                        "taxi_image" => '$taxi.taxi_image',
                        "taxi_capacity" => '$taxi.taxi_capacity',
                        "taxi_id" => '$taxi._id',
                        'updatetime_difference' => array('$multiply' => array(array('$subtract' => array(Commonfunction::MongoDate(strtotime($current_time)),'$update_date')),0.0001))
                    )
                )
            ),
            array('$match' => array("_id.updatetime_difference" => array('$lte' => (int)LOCATIONUPDATESECONDS))),
            array('$limit'=>10)
        );
        
        $res = $this->mongo_db->aggregate(MDB_DRIVER_INFO,$arguments);
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$datas = $r['_id'];
				$temp_arr['_id'] = $datas['id'];
				$temp_arr['distance'] = $datas['distance'];
				$temp_arr['distance_miles'] = $datas['distance_miles'];
				$temp_arr['update_date'] = commonfunction::convertphpdate('Y-m-d H:i:s',$datas['update_date']);
				$temp_arr['shift_status'] = $datas['shift_status'];
				$temp_arr['status'] = $datas['status'];
				$temp_arr['name'] = $datas['name'];
				$temp_arr['driver_id'] = $datas['driver_id'];
				$temp_arr['phone'] = $datas['phone'];
				$temp_arr['updatetime_difference'] = $datas['updatetime_difference'];
				$temp_arr['d_photo'] = $datas['d_photo'];
				$temp_arr['company_name'] = $datas['company_name'];
				$temp_arr['company_id'] = $datas['company_id'];
				$temp_arr['taxi_no'] = $datas['taxi_no'];
				$temp_arr['taxi_image'] = $datas['taxi_image'];
				$temp_arr['taxi_capacity'] = $datas['taxi_capacity'];
				$temp_arr['taxi_id'] = $datas['taxi_id'];
				
				$result[] = $temp_arr;
			}
		}        
        return $result;
    }
    public function check_driver_booking_limit($driver_id,$book_limit)
    {
        $current_time      = convert_timezone('now', TIMEZONE);
        $current_date      = explode(' ', $current_time);
        $start_time        = $current_date[0] . ' 00:00:01';
        $end_time          = $current_date[0] . ' 23:59:59';
        $arguments = array(
            array(
					'$lookup' => array(
					'from'=>MDB_PEOPLE,
					'localField'=> "driver_id",
					'foreignField' => "_id",
					'as'=> "people"
				)
			),
            array('$unwind' => '$people'),
            array('$match' => array(
                'createdate' => array('$gte' => '2015-04-21 00:00:01'), //$start_time
                'travel_status' => 1,
                'booking_from' => array('$ne'=>2),
                'driver_id' => (int)$driver_id)
            ),
            array('$group' => array('_id'=>0,'count' => array('$sum'=>1)))
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        //echo '<pre>';print_r($result);exit;
        $booking_limit = (!empty($result['result'][0]))?$result['result'][0]['count']:0;
        return ($booking_limit!=0) ? ($book_limit > $booking_limit) ? 1 : 0 : 0;
    }
    public function free_availabletaxisearch_list($motor_company = '', $motor_model = '', $company_id = '')
    {
        $current_time      = convert_timezone('now', TIMEZONE);
        $current_date      = explode(' ', $current_time);
        $start_time        = $current_date[0] . ' 00:00:01';
        $end_time          = $current_date[0] . ' 23:59:59';
        $match_query = array();
        if (isset($motor_company) && $motor_company != '') {
            $match_query['taxi_type'] = 1;
        }
        if (isset($motor_model) && ($motor_model != '')) {
            $match_query['taxi_model'] =(int)$motor_model;
        }        
        if ($company_id != "" && $company_id != 0) {
            $match_query['mapping.mapping_companyid'] = (int)$company_id;
            $match_query['people.company_id'] = (int)$company_id;
            $match_query['taxi_company'] = (int)$company_id;
        }
        $match_query['taxi_status'] = 'A';
        $match_query['taxi_availability'] = 'A';
        $match_query['people.status'] = 'A';
        $match_query['people.availability_status'] = 'A';
        $match_query['mapping.mapping_status'] = 'A';
        $match_query['mapping.mapping_startdate'] = array('$lte'=> Commonfunction::MongoDate(strtotime($current_time)));
        $match_query['mapping.mapping_enddate'] = array('$gte'=> Commonfunction::MongoDate(strtotime($current_time)));
        $match_query['company.companydetails.company_status'] = 'A';
        $match_query['report.check_package_type'] = 'T';
        $match_query['report.upgrade_expirydate'] = array('$gte'=>Commonfunction::MongoDate(strtotime($current_time)));
        $match_query['people.booking_limit'] = array('$gt' => $this->mongo_db->count(MDB_PASSENGERS_LOGS,array('createdate'=>array('$gte'=>$start_time),'driver_id'=>'people._id','travel_status'=>1,'booking_from' => array('$ne'=>2))));
        //echo '<pre>';print_r($match_query);exit;
        $ops = array(
            array(
                '$lookup' => array(
                    'from'=>MDB_COMPANY,
                    'localField'=> "taxi_company",
                    'foreignField' => "_id",
                    'as'=> "company"
                )
            ),
            array('$unwind' => '$company'),
            array(
                '$lookup' => array(
                    'from'=>MDB_TAXI_DRIVER_MAPPING,
                    'localField'=> "_id",
                    'foreignField' => "mapping_taxiid",
                    'as'=> "mapping"
                )
            ),
            array('$unwind' => '$mapping'),
            array(
                '$lookup' => array(
                    'from'=>MDB_PEOPLE,
                    'localField'=> "mapping.mapping_driverid",
                    'foreignField' => "_id",
                    'as'=> "people"
                )
            ),
            array('$project' => array(
                'taxi_status' => 1,
                'taxi_availability' => 1,
                'taxi_company' => 1,
                'taxi_model' => 1,
                'taxi_type' => 1,
                'driver_id' => '$mapping.mapping_driverid',
                'company' => 1,
                'mapping' => 1,
                'report' => 1,
                'people' => 1,
                'people' => array('$cond' => array(array('$eq'=>array(array('$size'=>'$people'),0)),null,'$people'))
                )
            ),
            array('$unwind'=>'$people'),
            array('$match' => $match_query),
            array('$group'=>array("_id"=>array("taxi_id"=>'$_id',
                        "id"=>'$people._id',
                        "booking_limit" => '$people.booking_limit'
                    ),
                )
            ),
            array('$sort'=>array('_id.id'=>1)),
        );
        $result = $this->mongo_db->aggregate(MDB_TAXI,$ops);
        //echo '<pre>';print_r($result);exit;
        return (!empty($result))?$result['result']:array();
    }
    public function create_logs($booking_logid = '', $company_id = '', $log_userid = '', $log_message = '', $log_booking = '')
    {
        $current_time = $this->company_current_time;        
        //MongoDB
        $log_data = array(
            'log_userid' => (int)$log_userid,
            'log_message' => $log_message,
            'log_booking' => $log_booking,
            'log_createdate' => Commonfunction::MongoDate(strtotime($current_time))
        );
        $log_array = array("logs" =>$log_data );
        $result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$booking_logid),array('$push'=>$log_array),array('upsert'=>true));
		//echo '<pre>';print_r($result);exit;
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function get_active_company_details()
    {
        //MongoDB
         $arguments = array(
            array(
                '$match' => array('companydetails.company_status'=>'A')
            ),
            array(
                '$project' => array('cid'=>'$_id','company_name'=>'$companydetails.company_name') 
            ),
            array('$sort'=>array('companydetails.company_name'=>1))
        );
        $result          = $this->mongo_db->aggregate(MDB_COMPANY, $arguments);
        //echo "<pre>";print_r($result['result']);exit;
        return (!empty($result['result']))?$result['result']:array();
    }
    /** to get company name from company id **/
    public function get_company_name($cid)
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$cid),array('companydetails.company_name'));
        return (!empty($result))?$result['companydetails']['company_name']:"";
    }
    public function cancelbooking_logid($data)
    {
        $log_id = trim($data['pass_logid']);
        $selectresult = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$log_id),array('travel_status'));
        if (!empty($selectresult) && count($selectresult)>0) {
            if ($selectresult['travel_status'] != '5' || $selectresult['travel_status'] != '2') {
                $updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$log_id),array('$set'=>array('travel_status'=>8)),array('upsert'=>true));
                if (SMS == 1) {
                    $get_passenger_log_det = $this->get_passenger_log_detail($log_id);
                    $passenger_phone       = isset($get_passenger_log_det[0]['passenger_phone']) ? $get_passenger_log_det[0]->passenger_phone : "";
                    if ($passenger_phone != "") {
                        $to              = $passenger_phone;
                        $message_details = $this->commonmodel->sms_message('6');
                        $message_temp    = $message_details[0]['sms_description'];
                        $sms_message     = str_replace(array(
                            "##SITE_NAME##"
                        ), array(
                            SITE_NAME
                        ), $message_temp);
                        $this->send_sms($to, $sms_message);
                    }
                }
                return 1;
            }
            return 0;
        }
        return 0;
    }
    public function get_passenger_log_detail($passengerlog_id = "")
    {        
        $ops = array(
            array('$match' => array('_id'=>(int)$passengerlog_id)),
            array(
                '$lookup' => array(
                'from'=>MDB_PASSENGERS,
                'localField'=> "_id",
                'foreignField' => "_id",
                'as'=> "passengers"
                )
            ),
            array('$unwind'=>'$passengers'),
            array(
                '$project' => array(
                    '_id' => 0,
                    'passenger_phone' => '$passengers.passenger_phone',
                )
            )
        );
        $results = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$ops);
        return (!empty($results['result']))?$results['result']:array();
    }
    public function check_driver_has_trip_request($driver_id)
    {
        $two_days_before = date('Y-m-d 00:00:01', strtotime("-2 days"));     
        //MongoDB
        $srch_query = array( "\$and" => array(array('driver_reply'=>'A','driver_id'=>(int)$driver_id,'dispatch_time'=>array('$gte'=>$two_days_before)),array("\$or"=>array(array( 'travel_status' => 2, 'travel_status' => 3, 'travel_status' => 5) ) ) ) );
        $result = $this->mongo_db->count(MDB_PASSENGERS_LOGS,$srch_query);
        return $result;
    }
    public function currently_driver_has_trip_request($driver_id)
    {
        $two_minutes_before = date('Y-m-d H:i:s', strtotime("-2 minutes"));
        //MongoDB
        $srch_query = array('status'=>1,'selected_driver'=>(int)$driver_id,'createdate'=>array('$gte'=>$two_minutes_before));
        $result = $this->mongo_db->count(MDB_REQUEST_HISTORY,$srch_query);
        return $result;
    }

    public function get_driver_list_with_status($array)
    {
        $company_id = '';
        $result= $temp_arr =array();        
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
            $current_time = convert_timezone('now', TIMEZONE);
            $current_date = explode(' ', $current_time);
            $start_time   = $current_date[0] . ' 00:00:01';
            $end_time     = $current_date[0] . ' 23:59:59';
        } else {
            $result          = $this->mongo_db->findOne(MDB_COMPANY, array(
                '_id' => (int) $company_id
            ), array(
                'companydetails.time_zone'
            ));
            $timezone_fetch = isset($result['companydetails']['time_zone']) ? $result['companydetails']['time_zone'] : "";
            if ($timezone_fetch != '') {
                $current_time = convert_timezone('now', $timezone_fetch);
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
		$usertype             = $_SESSION['user_type'];
        $company_id           = $_SESSION['company_id'];
        $driver_status                   = isset($array['driver_status']) ? $array['driver_status'] : "";
        $taxi_company                    = isset($array['taxi_company']) ? $array['taxi_company'] : "";
        $taxi_model                      = isset($array['taxi_model']) ? $array['taxi_model'] : "";
        $match_query                     = array();
        $match_query['people.user_type'] = 'D';
        $match_query['people.status']    = 'A';
        $match_query['taxi_driver_mapping.mapping_status'] = 'A';
        $match_query['taxi_driver_mapping.mapping_startdate'] = array('$lte' => Commonfunction::MongoDate(strtotime($current_time)));
        $match_query['taxi_driver_mapping.mapping_enddate'] = array('$gte' => Commonfunction::MongoDate(strtotime($current_time)));        
        if ($driver_status == 'A' || $driver_status == 'F') {
            $match_query['status']       = $driver_status;
            $match_query['shift_status'] = 'IN';
        } elseif ($driver_status == 'OUT') {
            $match_query['status']       = 'F';
            $match_query['shift_status'] = $driver_status;
        }
        $company_current_time = $this->company_current_time;
        if ($usertype != 'A') {
            $match_query['people.company_id'] = (int) $company_id;
        } else if ($usertype == 'A' && $taxi_company != "" && $taxi_company != 0) {
            $match_query['people.company_id'] = (int) $taxi_company;
        }
        if ($taxi_model != '') {
            $match_query['taxi.taxi_model']                     = (int) $taxi_model;
            $match_query['taxi_driver_mapping.mapping_enddate'] = $current_time;            
        }
        # company status
		$match_query['comp.companydetails.company_status'] = 'A';
        $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_PEOPLE,
                        'localField' => '_id',
                        'foreignField' => "_id",
                        'as' => "people"
                    )
                ),
                array('$unwind' => '$people'),
                array(
                    '$lookup' => array(
                        'from' => MDB_TAXI_DRIVER_MAPPING,
                        'localField' => '_id',
                        'foreignField' => "mapping_driverid",
                        'as' => "taxi_driver_mapping"
                    )
                ),
                array('$unwind' => '$taxi_driver_mapping'),
                array('$lookup' => array(
						'from' => MDB_COMPANY,
						'localField' => 'taxi_driver_mapping.mapping_companyid',
						'foreignField' => '_id',
						'as' => 'comp')),
				array('$unwind' =>  '$comp'),		
                array(
                    '$lookup' => array(
                        'from' => MDB_TAXI,
                        'localField' => 'taxi_driver_mapping.mapping_taxiid',
                        'foreignField' => '_id',
                        'as' => "taxi"
                    )
                ),
                array('$unwind' => '$taxi'),
                array(
                    '$lookup' => array(
                        'from' => MDB_MOTOR_MODEL,
                        'localField' => 'taxi.taxi_model',
                        'foreignField' => "_id",
                        'as' => "motor_model"
                    )
                ),
                array('$unwind' => '$motor_model'),
                array('$match' => $match_query),
                array('$group' => array('_id' => array(
                                'driver_id' => '$_id',
                                'taxi_id' => '$taxi_driver_mapping.mapping_taxiid',
                                'name' => '$people.name',
                                'driver_status' => '$status',
                                'update_date' => '$update_date',
                                'loc' => '$loc.coordinates',
                                'shift_status' => '$shift_status',
                                'model_name' => '$motor_model.model_name',
                                'update_date' => '$update_date',
                                'updatetime_difference' => array('$multiply' => array(array('$subtract' => array(Commonfunction::MongoDate(strtotime($current_time)),'$update_date')),0.0001))
                            ))),
                array('$match' => array('_id.updatetime_difference'=>array('$lte'=> (int)LOCATIONUPDATESECONDS )))
            );
        $res = $this->mongo_db->aggregate(MDB_DRIVER_INFO, $arguments);
        //echo '<pre>';print_r($arguments);exit;
		$exixts_id = array();
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$datas = $r['_id'];
				if(!in_array($datas['driver_id'],$exixts_id)){
					$temp_arr['driver_id'] = $datas['driver_id'];
					$temp_arr['taxi_id'] = $datas['taxi_id'];
					$temp_arr['name'] = $datas['name'];
					$temp_arr['driver_status'] = $datas['driver_status'];
					$temp_arr['update_date'] = isset($datas['update_date'])? commonfunction::convertphpdate('Y-m-d H:i:s',$datas['update_date']):'';
					$temp_arr['shift_status'] = $datas['shift_status'];
					$temp_arr['model_name'] = $datas['model_name'];
					$temp_arr['updatetime_difference'] = $datas['updatetime_difference'];
					$coordinates = $datas['loc'];
					$temp_arr['latitude'] = $coordinates[1];
					$temp_arr['longitude'] = $coordinates[0];
					$exixts_id[] = $datas['driver_id'];
					$result[] = $temp_arr;
				}
			}
		}
        return $result;
    }
    
    public function dispatcher_booking_list($array)
    {
		$result = array();
        $travel_status                = $array['travel_status'];
        $driver_reply_cancel          = $array['driver_reply_cancel'];
        $manage_status                = $array['manage_status'];
        $taxi_company                 = $array['taxi_company'];
		$taxi_model                   = $array['taxi_model'];
		$search_txt					  =	$array['search_txt'];
		$search_location			  = $array['search_location'];
		$filter_date				  = $array['filter_date'];
		$to_date					  = $array['to_date'];
		$fromdate 					  = $filter_date.':00';
		$todate 					  = $to_date.':00';
		
        $date                         = date('Y-m-d', strtotime($array['current_time']));
        $currentdate                  = $date . ' 00:00:00';
        $enddate                      = $date . ' 23:59:59';
        $status_query                 = "";
        $travel_status                = Commonfunction::mongo_format_array(explode(",", $travel_status));
        $two_days_before              = date( 'Y-m-d 00:00:00', strtotime( $date . ' 0 day' ) );
        $match_array                  = array();
        $match_array['bookby']        = 2;
        $match_array['travel_status'] = array( '$in' => $travel_status );
        
        if ($taxi_company != "" && $taxi_company != 0) {
            $match_array['company_id'] = (int) $taxi_company;
        }
		if ($taxi_model != "" && $taxi_model != 0) {
            $match_array['motormodel._id'] = (int) $taxi_model;
        }
        if ($driver_reply_cancel == "") {
            $match_array['driver_reply'] = array(
                '$nin' => array('C','R')
            );
            $key                         = array_search('8', $travel_status);
            if (false !== $key) {
                unset($travel_status[$key]);
            }
            $match_array['travel_status'] = array(
                '$in' => $travel_status
            );
        }
        if ($manage_status == 0) {
            //$match_array['pickup_time'] = array( '$gte' => Commonfunction::MongoDate(strtotime($two_days_before)));
        }
        
		if($search_txt!=""){
			$srch_query = array(
				array("\$or"=>array(array( 'passengers.name' => Commonfunction::MongoRegex("/$search_txt/i")) , 
				array( 'people.name' => Commonfunction::MongoRegex("/$search_txt/i") ),
				array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$search_txt/i") ),
				array( 'passengers.phone' => Commonfunction::MongoRegex("/$search_txt/i") ),
				array( '_id' => (int)$search_txt),
				array( 'people.phone' => Commonfunction::MongoRegex("/$search_txt/i") ) ) ) 
			);
		}
		if($search_location != '') {
			$srch_query1 =array(
				array("\$or"=>array(array( 'current_location' => Commonfunction::MongoRegex("/$search_location/i")) , 
				array( 'drop_location' => Commonfunction::MongoRegex("/$search_location/i") ) ) ) 
			);
		}
		//print_r($match_array); exit;
		if($filter_date !='' && $to_date !='')	
		{	
			$srch_query2 = array(
									array('$or' =>array(
									array('$and' => array(array('pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($fromdate)))),
													array('pickup_time' => array('$lte' => Commonfunction::MongoDate(strtotime($todate)))))),
									array('$and' => array(array('actual_pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($fromdate)))),
													array('actual_pickup_time' => array('$lte' => Commonfunction::MongoDate(strtotime($todate))))))
							)));			
		} else if($filter_date !='' || $to_date !='') {
			$datesearch = ($to_date != '') ? $to_date : $filter_date ;
			$dateArr = explode(" ",$datesearch);
			$staDate = $dateArr[0].' 00:00:01';
			$endDate = $dateArr[0].' 23:59:59';
			
			$srch_query2 = array(
									array('$or' =>array(
									array('$and' => array(array('pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($staDate)))),
													array('pickup_time' => array('$lte' => Commonfunction::MongoDate(strtotime($endDate)))))),
									array('$and' => array(array('actual_pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($staDate)))),
													array('actual_pickup_time' => array('$lte' => Commonfunction::MongoDate(strtotime($endDate))))))
							)));
		} else {
			$staDate = date('Y-m-d 00:00:00', strtotime($date .' 0 day'));
			$endDate = date('Y-m-d 23:59:59', strtotime($date .' 0 day'));
			
			$srch_query2 = array(
									array('$or' =>array(
									array('$and' => array(array('pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($staDate)))),
													array('pickup_time' => array('$lte' => Commonfunction::MongoDate(strtotime($endDate)))))),
									array('$and' => array(array('actual_pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($staDate)))),
													array('actual_pickup_time' => array('$lte' => Commonfunction::MongoDate(strtotime($endDate))))))
							)));
		}
		$srch_query = (isset($srch_query))?$srch_query:array();
		$srch_query1 = (isset($srch_query1))?$srch_query1:array();
		$srch_query2 = (isset($srch_query2))?$srch_query2:array();
		$arr_merge = array_merge($srch_query,$srch_query1);
		$arr_merge = array_merge($arr_merge,$srch_query2);
		$and_arr = (!empty($arr_merge))?array( "\$and" => $arr_merge):array();
		$match_array = array_merge($match_array,$and_arr);
		//echo "<pre>";  print_r($match_array); exit;
        $arguments = array(
            array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'driver_id',
                    'foreignField' => "_id",
                    'as' => "people"
                )
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => 'company_id',
                    'foreignField' => "_id",
                    'as' => "company"
                )
            ),
            array(
                '$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => 'passengers_id',
                    'foreignField' => "_id",
                    'as' => "passengers"
                )
            ),
            array( '$unwind' => '$passengers'),
            array(
                '$lookup' => array(
                    'from' => MDB_MOTOR_MODEL,
                    'localField' => 'taxi_modelid',
                    'foreignField' => "_id",
                    'as' => "motormodel"
                )
            ),
            array( '$unwind' => '$motormodel' ),
			array( '$match' => $match_array  ),
            array(
                '$project' => array(
                    'pass_logid' => '$_id',
                    'company_id' => '$company_id',
                    'notes' => '$notes_driver',
                    'total_drivers' => '$driver_id',
                    'fare' => '$approx_fare',
                    'distance' => '$approx_distance',
                    'pickup_time' => '$pickup_time',
                    'act_pickuptime' => '$actual_pickup_time',
                    'driver_id' => '$driver_id',
                    'pickup_latitude' => '$pickup_latitude',
                    'pickup_longitude' => '$pickup_longitude',
                    'drop_latitude' => '$drop_latitude',
                    'drop_longitude' => '$drop_longitude',
                    'no_passengers' => '$no_passengers',
                    'current_location' => '$current_location',
                    'drop_location' => '$drop_location',
                    'dispatch_time' => '$dispatch_time',
                    'travel_status' => '$travel_status',
                    'driver_reply' => '$driver_reply',
                    'driver_name' => '$people.name',
                    'reachable_mobile' => '$people.phone',
                    'passenger_id' => '$passengers_id',
                    'passenger_name' => '$passengers.name',
                    'passenger_country_code' => '$passengers.country_code',
                    'passenger_phone' => '$passengers.phone',
                    'model_name' => '$motormodel.model_name',
                    'company_name' => '$company.companydetails.company_name',
                    'createdate' => '$createdate',
                )
            ),
            array(
                '$sort' => array(
                    '_id' => -1
                )
            )
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
       // echo '<pre>';print_r($res['result']);exit;
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
			
				$temp_arr['company_id'] = !empty($r['company_id']) ? $r['company_id'][0]:'';
				$temp_arr['company_name'] = !empty($r['company_name']) ? $r['company_name'][0]:'';
				$temp_arr['passenger_id'] = isset($r['passenger_id']) ? $r['passenger_id']:'';
				$temp_arr['passenger_name'] = isset($r['passenger_name']) ? $r['passenger_name']:'';
				$temp_arr['passenger_phone'] = isset($r['passenger_phone']) ? $r['passenger_phone']:'';
				$temp_arr['passenger_country_code'] = isset($r['passenger_country_code']) ? $r['passenger_country_code']:'';
				$temp_arr['driver_name'] = !empty($r['driver_name']) ? $r['driver_name'][0]:'';
				$temp_arr['driver_phone'] = !empty($r['reachable_mobile']) ? $r['reachable_mobile'][0]:'';
				$temp_arr['driver_id'] = isset($r['driver_id']) ? $r['driver_id']:'';
				$temp_arr['model_name'] = !empty($r['model_name']) ? $r['model_name']:'';
				$temp_arr['total_drivers'] = !empty($r['total_drivers']) ? $r['total_drivers'][0]:'';
				$temp_arr['distance'] = isset($r['distance']) ? $r['distance']:'';
				$temp_arr['fare'] = isset($r['fare']) ? $r['fare']:'';
				$temp_arr['pass_logid'] = isset($r['pass_logid']) ? $r['pass_logid']:'';
				$temp_arr['notes'] = isset($r['notes']) ? $r['notes']:'';
				$temp_arr['createdate'] = isset($r['createdate']) ? Commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']):'';                                
				$temp_arr['bookingtype'] = isset($r['bookingtype']) ? $r['bookingtype']:'';
				$temp_arr['bookby'] = isset($r['bookby']) ? $r['bookby']:'';
				$temp_arr['pickup_latitude'] = isset($r['pickup_latitude']) ? $r['pickup_latitude']:'';
				$temp_arr['pickup_longitude'] = isset($r['pickup_longitude']) ? $r['pickup_longitude']:'';
				$temp_arr['drop_latitude'] = isset($r['drop_latitude']) ? $r['drop_latitude']:'';
				$temp_arr['drop_longitude'] = isset($r['drop_longitude']) ? $r['drop_longitude']:'';
				$temp_arr['no_passengers'] = isset($r['no_passengers']) ? $r['no_passengers']:'';
				$temp_arr['current_location'] = isset($r['current_location']) ? $r['current_location']:'';
				$temp_arr['drop_location'] = isset($r['drop_location']) ? $r['drop_location']:'';
				$temp_arr['travel_status'] = isset($r['travel_status']) ? $r['travel_status']:'';
				$temp_arr['driver_reply'] = isset($r['driver_reply']) ? $r['driver_reply']:'';
				$temp_arr['approx_distance'] = isset($r['approx_distance']) ? $r['approx_distance']:'';
				$temp_arr['approx_fare'] = isset($r['approx_fare']) ? $r['approx_fare']:'';
				$temp_arr['dispatch_time'] = isset($r['dispatch_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['dispatch_time']):'';
				$temp_arr['pickup_time'] = isset($r['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
				$temp_arr['act_pickuptime'] = isset($r['act_pickuptime']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['act_pickuptime']):'-';
				
				$result[] = $temp_arr;
			}
        }
		//echo "<pre>";print_r($result);exit;
		return $result;
    }
   
    public function dispatcher_booking_transaction($trip_id)
    {
		$result = array();
        //$query  = $this->mongo_db->find(MDB_TRANSACTION, array('passengers_log_id' => (int) $trip_id), array('distance','fare'));
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                    'distance'=>1,
                    'fare'=>1
                    ]                
                ];
        $query  = $this->mongo_db->find(MDB_TRANSACTION, ['passengers_log_id' => (int) $trip_id], $options);
        $res = $query;
        $res = commonfunction::change_key($res);
        
        if(!empty($res)){
			$result = array_map(
						function($res) {
							return array(
								'fare' => $res['fare'],
								'distance' => isset($res['distance'])?$res['distance']:0
							);
					}, $res);
		}
        return $result;
    }
    public function check_driver_not_updated($driver_id)
    { 
        $query  = $this->mongo_db->findOne(MDB_DRIVER_INFO, array('_id' => (int)$driver_id), array('update_date'));
        $result = (!empty($query)) ? Commonfunction::convertphpdate('Y-m-d H:i:s',$query['update_date']) : 0;
        return $result;
    }
   
    public function check_new_request_tripid($taxi_id = null, $company_id = null, $trip_id, $driver_id, $company_all_currenttimestamp, $driver_reply, $operator_id = 0)
    {
        $datetime    = explode(' ', $company_all_currenttimestamp);
        $currentdate = $datetime[0] . ' 00:00:01';     
        $match = array('_id'=>(int)$trip_id,
					   'selected_driver'=> (int)$driver_id,
					   'status'=>array('$ne'=> 4),
					   'createdate'=>array('$gte'=> Commonfunction::MongoDate(strtotime($currentdate)))
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
                $available_drivers = $result['available_drivers'];
                $exp_drivers       = explode(',', $available_drivers);
                $s_array           = array();
                $first_driver      = isset($exp_drivers[0]) ? $exp_drivers[0] : 0;
				//$temp_driver=$first_driver;
				//print_r($result); exit;
                for ($i = 1; $i < count($exp_drivers); $i++) {
                    $s_array[]   = $exp_drivers[$i];
                    $temp_driver = isset($exp_drivers[1]) ? $exp_drivers[1] : $exp_drivers[0];
                }
                
				$temp_driver = is_array($exp_drivers) ? $temp_driver : $exp_drivers[0];
                if (count($s_array)>0) {
                    $s_driver = implode(',', $s_array);
                }
                $prev_rejected_timeout_drivers = isset($result['rejected_timeout_drivers']) ? $result['rejected_timeout_drivers'] : "";
                //echo $prev_rejected_timeout_drivers;
                if ($prev_rejected_timeout_drivers != "") {
                    $rejected_timeout_drivers = $prev_rejected_timeout_drivers . ',' . $driver_id;
                } else {
                    $rejected_timeout_drivers = $driver_id;
                }
                //to get the usertypes
                if ($operator_id != 0) {
					$user_type_dets = $this->mongo_db->findOne(MDB_PEOPLE,array('_id' => (int)$operator_id),array('user_type'));
                }
                
                $temp_driver       = isset($temp_driver) ? $temp_driver : "";
                $update_trip_array = array(
                    "available_drivers" => $s_driver,
                    "selected_driver" => (int)$temp_driver,
                    "status" => 0,
                    "rejected_timeout_drivers" => $rejected_timeout_drivers
                );
                //print_r($update_trip_array);exit;
                $update_result = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY,array('_id'=>(int)$trip_id),array('$set'=>$update_trip_array),array('upsert'=>false));
        
                //to update driver request and passenger log if selected driver is empty
				
                if ($temp_driver == '') {
                    $update_trip_array_one = array(
                        "status" => 4
                    );
                    $update_result = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY,array('_id'=>(int)$trip_id),array('$set'=>$update_trip_array_one),array('upsert'=>false));
                    //condition checked to null the company id and name only in admin side
                    if ($operator_id != 0 && (isset($user_type_dets['user_type']) && $user_type_dets['user_type'] == 'A')) {
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
                    $update_result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$trip_id),array('$set'=>$update_log_array_driver),array('upsert'=>false));
                }
                $driver_details         = $this->get_driver_taxi($temp_driver);
                $drivertaxi             = isset($driver_details[0]['mapping_taxiid']) ? $driver_details[0]['mapping_taxiid'] : $taxi_id;
                $drivercompany          = isset($driver_details[0]['mapping_companyid']) ? $driver_details[0]['mapping_companyid'] : $company_id;
                $driver_profile_details = array();
                if ($temp_driver != '') {
                    //to get the driver profile details and company name
                    $driver_profile_details = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=> (int)$temp_driver),array('name','phone'));
                }
                
                $driver_name         = (isset($driver_profile_details['name'])) ? $driver_profile_details['name'] : "";
                $driver_phone        = (isset($driver_profile_details['phone'])) ? $driver_profile_details['phone'] : "";
                $driver_reachable_no = (isset($driver_profile_details['phone'])) ? $driver_profile_details['phone'] : "";
               
                //company Name
                $companyDets         = array();
                if ($drivercompany != '') {
                    $companyDets = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=> (int)$drivercompany),array('companydetails.company_name'));
                }
                
                $companyName = (count($companyDets) > 0 && isset($companyDets['companydetails']['company_name'])) ? $companyDets['companydetails']['company_name'] : "";
                //condition checked to update the company id and name only in admin side
                if ($operator_id != 0 && (isset($user_type_dets['user_type']) && $user_type_dets['user_type'] == 'A')) {
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
                //}
                $pass_log_update = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$trip_id),array('$set'=>$update_log_array),array('upsert'=>false));
                $update_driver_array      = array(
                    "status" => 'B'
                );
                $driver_tbl_update = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id'=>(int)$driver_id),array('$set'=>$update_driver_array),array('upsert'=>false));
                //$driver_status = $this->get_request_status($trip_id);
                $available_drivers        = explode(',', $result['total_drivers']);
                $rejected_timeout_drivers = explode(',', $rejected_timeout_drivers);
                $comp_result              = array_diff($available_drivers, $rejected_timeout_drivers);
				
                if (count($comp_result) == 0) {
                    $update_trip_array_one = array(
                        "status" => 4
                    );
                    $update_result = $this->mongo_db->updateOne(MDB_REQUEST_HISTORY,array('_id'=>(int)$trip_id),array('$set'=>$update_trip_array_one),array('upsert'=>false));
                    //condition checked to null the company id and name only in admin side
                    if ($operator_id != 0 && (isset($user_type_dets['user_type']) && $user_type_dets['user_type'] == 'A')) {
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
                    $pass_log_update = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$trip_id),array('$set'=>$update_log_array_driver),array('upsert'=>false));
                }
            } else {
                //echo "2";exit;
                $drivertaxi    = $taxi_id; //isset($driver_details[0]['mapping_taxiid'])?$driver_details[0]['mapping_taxiid']:"";
                $drivercompany = $company_id; //isset($driver_details[0]['mapping_companyid'])?$driver_details[0]['mapping_companyid']:"";
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
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                    'mapping_taxiid'=>1,
                    'mapping_companyid'=>1
                    ]                
                ];
        $result = $this->mongo_db->find(MDB_TAXI_DRIVER_MAPPING,['mapping_driverid' => (int)$driver_id, 'mapping_status' => 'A'],$options);
        $resultset = Commonfunction::change_key($result);
		return (!empty($resultset))? $resultset: array();
    }
    public function model_details()
    {
        $company_id = $this->company_id;        
        //MongoDB
        if (FARE_SETTINGS == 2 && !empty($company_id)) {
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => '_id',
                        'foreignField' => 'model_fare.model_id',
                        'as' => 'company'
                    )
                ),
                array(
                    '$unwind' => '$company'
                ),
                array(
                    '$match' => array("\$and"=>array(array('company._id'=>(int)$company_id),array('company.model_fare.fare_status'=>'A'),array('model_status'=>'A')))
                ),
                array(
                    '$project' => array('_id'=>'$_id','model_id'=>'$_id','model_name'=>'$model_name') 
                )
            );
            $result          = $this->mongo_db->aggregate(MDB_MOTOR_MODEL, $arguments);
			//echo "<pre>if";print_r($result['result']);exit;
            return (!empty($result['result']))?$result['result']:array();
        } else {
            $arguments = array(
                array(
                    '$match' => array('model_status'=>'A')
                ),
                array(
                    '$project' => array('model_id'=>'$_id','model_name'=>'$model_name') 
                )
            );
            $result          = $this->mongo_db->aggregate(MDB_MOTOR_MODEL, $arguments);
			//echo "<pre>else";print_r($result['result']);exit;
            return (!empty($result['result']))?$result['result']:array();
        }
    }
    public function updatebooking_logid($data)
    {
        $company_id       = $this->company_id;
        $current_datetime = $this->company_current_time;
        $duration         = '+1 minutes';
        $current_datetime = date('Y-m-d H:i:s', strtotime($duration, strtotime($current_datetime)));
        
        //MongoDB
        $updatequery = array(
            'company_id' => (int)  $data['company_id'],
            'driver_id'=>(int)$data['driver_id'],
            'taxi_id'=>(int)$data['taxi_id'],
            'travel_status'=>7,
            'driver_reply'=>'',
            'msg_status'=>'U',
            'comments' => '',
            'dispatch_time'=> Commonfunction::MongoDate(strtotime($current_datetime))
        );
        $updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=>(int)$data['pass_logid']),array('$set'=>$updatequery),array('upsert'=>true));
        return (empty($updateresult->getwriteErrors()))?1:$updateresult->getwriteErrors();
    }
    public function get_driver_profile_details($id = "")
    {
        $result = array();
        $res = $this->mongo_db->findOne(MDB_PEOPLE,array('_id' => (int)$id),array('name'));
        if(!empty($res)){
			$result[] = $res;
		}
        return $result;
    }
    //function to get dispatch settings
    public function dispatch_settings($company_id)
    {
		## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
		$dispatch = '2';
		if($company_id != '' && $company_id != 0){
			$options=[
				'projection'=>[
					'dispatch_algorithm'=>1,
					'_id' => 0
					],
				'limit'=>1
				];            
			$dispatch_data = $this->mongo_db->find(MDB_COMPANY,['_id'=>(int)$company_id],$options);                
			$dispatch = (isset($dispatch_settings[0]['dispatch_algorithm'][0]['labelname'])) ? $dispatch_settings[0]['dispatch_algorithm'][0]['labelname']:'2';
		}else{
			$options=[
				'projection'=>[
					'labelname'=>1,
					'_id' => 0
					],
					'limit'=>1
				];            
			$dispatch_data = $this->mongo_db->find(MDB_SITEINFO, ['_id'=>1],$options);                
			$dispatch = (isset($dispatch_data[0]['labelname'])) ? $dispatch_data[0]['labelname']:'2';
		}
        return $dispatch;
    }
     //function to get admin dispatch settings
    public function admin_dispatch_settings()
    {
        $dispatch_data = $this->mongo_db->findOne(MDB_SITEINFO,array('_id'=>1),array('labelname'));
        //echo '<pre>';print_r($dispatch_data);exit;
        $companydispatch = (!empty($dispatch_data))?$dispatch_data['labelname']:'';
        return $companydispatch;
    }
    public function directdispatch($pass_logid)
    {
        $company_id = $this->company_id;
        /** Auto Dispatch **/
        if ($pass_logid != '') {
            $booking_details   = $this->get_bookingdetails($pass_logid, $company_id);
            $latitude          = $booking_details[0]["pickup_latitude"];
            $longitude         = $booking_details[0]["pickup_longitude"];
            $miles             = '';
            $no_passengers     = $booking_details[0]["no_passengers"];
            $taxi_fare_km      = $booking_details[0]["min_fare"];
            $taxi_model        = $booking_details[0]["taxi_modelid"];
            $taxi_type         = '';
            $maximum_luggage   = isset($booking_details[0]["luggage"]) ? $booking_details[0]["luggage"]:0;
            $company_id        = $booking_details[0]["company_id"];
            $cityname          = '';
            $search_driver     = '';
            $driver_details    = $this->search_driver_location($latitude, $longitude, $miles, $no_passengers, $_REQUEST, $taxi_fare_km, $taxi_model, $taxi_type, $maximum_luggage, $cityname, $pass_logid, $company_id, $search_driver);
            $nearest_driver    = '';
            $a                 = 1;
            $temp              = '10000';
            $prev_min_distance = '10000~0';
            $taxi_id           = '';
            $temp_driver       = 0;
            $nearest_key       = 0;
            $prev_key          = 0;
            $driver_list       = "";
            $available_drivers = "";
            $nearest_driver_id = $nearest_taxi_id = "";
            $total_count       = count($driver_details);
            if ($total_count > 0) {
                $nearest_count      = 1;
                /*Nearest driver calculation */
                $nearest_driver_ids = array();                
                foreach ($driver_details as $key => $values) {
					$value = $values['_id'];
                    $prev_min_distance = explode('~', $prev_min_distance);
                    $prev_key          = $prev_min_distance[1];
                    $prev_min_distance = $prev_min_distance[0];
                    //to check the driver has trip already
                    $driver_has_trip   = $this->check_driver_has_trip_request($value['driver_id']);
                    $current_request   = $this->currently_driver_has_trip_request($value['driver_id']);
                    if ($driver_has_trip == 0 && $current_request == 0) {
                        $nearest_driver_ids[] = $value['driver_id'];
                        if ($nearest_count == 1) {
                            /*$nearest_driver_id = isset($driver_details[$key]['_id']['driver_id']) ? $driver_details[$key]['_id']['driver_id'] : 0;
                            $nearest_taxi_id   = isset($driver_details[$key]['_id']['taxi_id']) ? $driver_details[$key]['_id']['taxi_id'] : 0;*/
                            $nearest_driver_id = isset($driver_details[$key]['driver_id']) ? $driver_details[$key]['driver_id'] : 0;
                            $nearest_taxi_id   = isset($driver_details[$key]['taxi_id']) ? $driver_details[$key]['taxi_id'] : 0;
                        }
                        $nearest_count++;
                    }
                    //checking with previous minimum
                    if ($value['distance'] < $prev_min_distance) {
                        //new minimum distance
                        $nearest_key       = $key;
                        $prev_min_distance = $value['distance'] . '~' . $key;
                    } else {
                        //previous minimum
                        $nearest_key       = $prev_key;
                        $prev_min_distance = $prev_min_distance . '~' . $prev_key;
                    }
                }
                $drivers_count = count($nearest_driver_ids);
                if ($nearest_driver_ids != NULL) {
                    $nearest_driver_ids = implode(",", $nearest_driver_ids);
                }
                $miles_or_km       = round(($prev_min_distance), 2);
                $driver_away_in_km = (ceil($miles_or_km * 100) / 100);
                $current_datetime  = date('Y-m-d H:i:s');
                $duration          = '+1 minutes';
                $current_datetime  = date('Y-m-d H:i:s', strtotime($duration, strtotime($current_datetime)));
                /****** Estimated Arival *************/
                $taxi_speed        = $this->api_model->get_taxi_speed($nearest_taxi_id);
                $estimated_time    = $this->api_model->estimated_time($driver_away_in_km, $taxi_speed);
                /**************************************/
                //to get nearest driver's company id
                if (!empty($nearest_driver_id)) {
                    $driver_company_details = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=>(int)$nearest_driver_id),array('company_id','name','phone'));
                }
                $companyName         = (isset($driver_company_details[0]['name'])) ? $this->get_company_name($driver_company_details[0]['company_id']) : "";
                $companyid           = (isset($driver_company_details[0]['company_id'])) ? $driver_company_details[0]['company_id'] : "";
                $driver_name         = (isset($driver_company_details[0]['name'])) ? $driver_company_details[0]['name'] : "";
                $driver_phone        = (isset($driver_company_details[0]['phone'])) ? $driver_company_details[0]['phone'] : "";
                $driver_reachable_no = (isset($driver_company_details[0]['phone'])) ? $driver_company_details[0]['phone'] : "";
                //condition checked to update the company id and name only in admin side
                if ($this->usertype == 'A') {                    
                    $updatequery = array('driver_id'=>(int)$nearest_driver_id,
                        'taxi_id'=>(int) $nearest_taxi_id,
                        'company_id'=>(int)$companyid,
                        'travel_status'=>7,
                        'driver_reply'=>'',
                        'msg_status'=>'U',
                        'dispatch_time'=> Commonfunction::MongoDate(strtotime($current_datetime))
                    );
                } else {
                    $updatequery = array('driver_id'=>(int)$nearest_driver_id,
                        'taxi_id'=>(int)$nearest_taxi_id,
                        'travel_status'=>7,
                        'driver_reply'=>'',
                        'msg_status'=>'U',
                        'dispatch_time'=> Commonfunction::MongoDate(strtotime($current_datetime))
                    );
                }
                $updateresult = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,array('_id'=> (int)$pass_logid),array('$set'=>$updatequery),array('upsert'=>false));
                /* Create Log */
                $company_id   = $this->company_id;
                $userid       = $this->user_id;
                $log_message  = __('log_message_dispatched');
                $log_message  = str_replace("PASS_LOG_ID", $pass_logid, $log_message);
                $log_booking  = __('log_booking_dispatched');
                //$log_booking  = str_replace("DRIVERNAME", $driver_details[0]['_id']['name'], $log_booking);
                $log_booking  = str_replace("DRIVERNAME", $driver_details[0]['name'], $log_booking);
                $log_status   = $this->create_logs($pass_logid, $company_id, $userid, $log_message, $log_booking);
            ?>
				<script type="text/javascript">load_logcontent();</script>
			<?php
                $exist_request = $this->exist_request($pass_logid);
                if ($exist_request == 1) {
                    $delete_exist_request = $this->mongo_db->deleteMany(MDB_REQUEST_HISTORY,array('_id'=>(int)$pass_logid));
                }
                /***** Insert the druiver details to driver request table ************/
                $nearest_driver_ids = (!empty($nearest_driver_ids)) ? $nearest_driver_ids : '';
                $insert_array       = array(
                    "_id" => (int)$pass_logid,
                    "available_drivers" => $nearest_driver_ids,
                    "total_drivers" => $nearest_driver_ids,
                    "selected_driver" => (int)$nearest_driver_id,
                    "status" => 0,
                    "rejected_timeout_drivers" => null,
                    "createdate" => Commonfunction::MongoDate(strtotime($current_datetime))
                );
                //print_r($insert_array);exit;
                //Inserting to Driver Request Table
                $driver_request        = $this->mongo_db->insertOne(MDB_REQUEST_HISTORY, $insert_array);
                $detail             = array(
                    "passenger_tripid" => $pass_logid,
                    "notification_time" => ""
                );
                $msg                = array(
                    "message" => __('api_request_confirmed_passenger'),
                    "status" => 1,
                    "detail" => $detail
                );
            }
        }
        /** Auto Dispatch **/
        $req_result['send_mail']  = 'N';
        $req_result['pass_logid'] = $pass_logid;
        return $req_result;
    }
    
	public function company_dispatch($id = "")
    {
        $result = array();
        $match = array('_id' => (int)$id);
        $args = array(array('$match' => $match),
					array('$unwind' => '$dispatch_algorithm'),
					array('$project' => array('labelname' => '$dispatch_algorithm.labelname',
											'hide_customer' => '$dispatch_algorithm.hide_customer',
											'hide_droplocation' => '$dispatch_algorithm.hide_droplocation')));
        $res = $this->mongo_db->Aggregate(MDB_COMPANY,$args);
		//print_r($res); exit;
        if(!empty($res['result']) && $id!=0){
			$rr = $res['result'][0];
			$data['labelname']    = isset($rr['labelname']) ? $rr['labelname'] : '';
			$data['hide_customer']    = isset($rr['hide_customer']) ? $rr['hide_customer'] : '';
			$data['hide_droplocation'] = isset($rr['hide_droplocation']) ? $rr['hide_droplocation'] : '';
			$result[] = $data;
		}elseif($id==0){
			$data['labelname']    = '1,2';
			$data['hide_customer']    = '';
			$data['hide_droplocation'] = '';
			$result[] = $data;
		}
        return $result;
    }
	
	
	public function checkPassengerStatus($trip_id,$company_id)
	{
		$pending_trip_count = $in_trip = 0;
        $args = array(array('$match' => array('_id' => (int)$trip_id,'travel_status' => 5,'driver_reply' => 'A')),
				array('$project' => array('passengers_log_id' => '$_id','passengers_id' => '$passengers_id')),
				array('$group' => array("_id" => NULL,
					"details" => array('$first' => array('passengers_id' => '$passengers_id')),
					"total" => array( '$sum' => 1 ),
					)
				)
			);
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		$result = array();
        if(!empty($res['result'][0])){
			$result = $res['result'][0];
		}
		if(count($result) > 0) {
			$passengers_id = (isset($result['details']["passengers_id"]))?$result['details']["passengers_id"]:"";		
			if($passengers_id == "") {
				$passenger_result = $this->mongo_db->findOne(MDB_PASSENGERS_LOGS,array('_id' => (int)$trip_id),array('passengers_id'));
				$passengers_id = $passenger_result['passengers_id'];
			}
			$pending_trip_count = isset($result["total"])?$result["total"]:0;
			$common_model = Model::factory('commonmodel');
			$start_time = $common_model->getcompany_all_currenttimestamp($company_id);
			$match['passengers_id'] = (int)$passengers_id;
			$match['driver_reply'] = 'A';
			$match['travel_status'] = array('$in' => array(9,3,2));
			$match['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($start_time)));
			$arguments = array(
				array('$match' => $match),
				array('$project' => array('_id' => 0,'passengers_log_id' => '$_id')),
				array('$group' =>array('_id' => null,'total' => array('$sum' => 1))),
			);
			$result_trip = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
			if(isset($result_trip[0]["total"])) {
				$in_trip = $result_trip[0]["total"];
			}
		}
		return json_encode(array("pending_payment_count" => $pending_trip_count,"in_trip" => $in_trip));
	}
	
	public function remove_request_details($trip_id){
		//$delete_exist_request = $this->mongo_db->remove(MDB_REQUEST_HISTORY,array('_id'=>(int)$trip_id));
            $delete_exist_request = $this->mongo_db->deleteOne(MDB_REQUEST_HISTORY,array('_id'=>(int)$trip_id));
	}
	
	public function insert_request_details($data){
		$inc_id = Commonfunction::get_auto_id(MDB_REQUEST_HISTORY);
		$insert_array = array(
			"_id" => (int)$data['trip_id'],
			//"trip_id" => (int)$data['trip_id'],
			"available_drivers" => $data['available_drivers'],
			"total_drivers" 	=> $data['total_drivers'],
			"selected_driver" 	=> (int)$data['selected_driver'],
			"status" => (int)$data['status'],
			"rejected_timeout_drivers" => $data['rejected_timeout_drivers'],
			"trip_type" => 0,
			"createdate"		=> Commonfunction::MongoDate(strtotime($data['createdate'])),
		);
		$this->mongo_db->insertOne(MDB_REQUEST_HISTORY,$insert_array);
		return $inc_id;
	}
	
	public function getpickupTimezone($lat,$lng)
	{
		try{
			$url = 'https://maps.googleapis.com/maps/api/timezone/json?location='.trim($lat).','.trim($lng).'&timestamp=1&key='.GOOGLE_TIMEZONE_API_KEY;  
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
	
	
}
?>
