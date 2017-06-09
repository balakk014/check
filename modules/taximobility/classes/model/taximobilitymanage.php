<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Manage Model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityManage extends Model
{
    public function __construct()
    {
        $this->session         = Session::instance();
        $this->currentdate     = Commonfunction::getCurrentTimeStamp();
		# created date
		$this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
		$this->user_createdby = $this->userid = $this->session->get("userid");
        $this->usertype       = $this->session->get('user_type');
        $this->company_id     = $this->session->get('company_id');
        $this->country_id     = $this->session->get('country_id');
        $this->state_id       = $this->session->get('state_id');
        $this->city_id        = $this->session->get('city_id');
		$this->Commonmodel = Model::factory('Commonmodel');
		//MongoDB Instance
		$this->mongo_db         = MangoDB::instance('default');
    }
    public function all_company_list($offset, $val,$find_count=false)
    {
		if($find_count) {
			$ops = array(
				array('$match' => array('user_type'=>'C',
										'status'=>array('$ne'=>'T')
				)),
				array(
						'$lookup' => array(
						'from'=>MDB_COMPANY,
						'localField'=> "company_id",
						'foreignField' => "_id",
						'as'=> "cdetails"
						)
					),
				array('$unwind' => '$cdetails'),
				array(
					'$project' => array(
					'id' => '$_id',
					)
				)
			);
			$result = $this->mongo_db->aggregate(MDB_PEOPLE,$ops);
			//echo '<pre>';print_r($result);exit;
			return (!empty($result['result']))?count($result['result']):0;
		} else {
			$ops = array(
				array('$match' => array('user_type'=>'C','status'=>array('$ne'=>'T'))),
				array(
						'$lookup' => array(
						'from'=>MDB_COMPANY,
						'localField'=> "company_id",
						'foreignField' => "_id",
						'as'=> "cdetails"
						)
					),
				array('$unwind'=>'$cdetails'),
				array(
					'$project' => array(
					'company_status' => '$cdetails.companydetails.company_status',
					'company_name' => '$cdetails.companydetails.company_name',
					'company_address' => '$cdetails.companydetails.company_address',
					'name' => '$name',
					'company_id' => '$company_id',
					'email' => '$email',
					'user_type' => '$user_type',
					'id' => '$_id',
					)
				),
				array(
					'$sort' => array(
						'company_id' => -1
					),
				),
				array('$skip' => (int)$offset),
				array('$limit' => (int)$val)
			);			
			$result = $this->mongo_db->aggregate(MDB_PEOPLE,$ops);
			
			$details = array();
			if(!empty($result['result'])){
				foreach ($result['result'] as $key => $res) {
					$details[$key]['no_of_taxi']      = $this->taxicount($res['company_id']);
					$details[$key]['no_of_driver']    = $this->drivercount($res['company_id']);
					$details[$key]['no_of_manager']   = $this->managercount($res['company_id']);
					$details[$key]['no_of_package']   = $this->packagecount($res['company_id']);
					$details[$key]['name']            = $res['name'];
					$details[$key]['email']           = $res['email'];
					$details[$key]['cid']             = $res['company_id'];
					$details[$key]['company_name']    = (isset($res['company_name']))?$res['company_name']:'';
					$details[$key]['company_address'] = (isset($res['company_address']))?$res['company_address']:'';
					$details[$key]['company_status']  = (isset($res['company_status']))?$res['company_status']:'';
					$details[$key]['id']              = $res['id'];
				}
			}
			//echo '<pre>';print_r($result['result']);exit;
			return $details;
		}
    }
    public function all_company_searchlist($keyword = "", $status = "",$company = "",$offset ="",$val ="", $find_count=false)
    {
		$keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
		$srch_query = array('user_type' => 'C' );
		//MongoDB with aggregate process only
		if((!empty($keyword)) && (!empty($status))) {
			$srch_query = array( "\$and" => array(array('user_type' => 'C' ),array('status' => $status ),array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'email' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'cdetails.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		} else if (!empty($keyword)) {
			$srch_query = array( "\$and" => array(array('user_type' => 'C' ),array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'email' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'cdetails.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		} else if (!empty($status)) {
			$srch_query = array( "\$and" => array(array('user_type' => 'C' ),array('status' => $status )));
		}
		//echo '<pre>';print_r($srch_query);//exit;
		if($find_count) {
			$ops = array(				
				array(
						'$lookup' => array(
						'from'=>MDB_COMPANY,
						'localField'=> "company_id",
						'foreignField' => "_id",
						'as'=> "cdetails"
						)
					),
				array('$unwind' => '$cdetails'),
				array('$match' => $srch_query),
				array(
					'$project' => array(
					'id' => '$_id',
					)
				),
			);
			$result = $this->mongo_db->aggregate(MDB_PEOPLE,$ops);
			//echo '<pre>';print_r($result);//exit;
			return (!empty($result['result']))?count($result['result']):0;
		} else {
			$ops = array(
				array(
						'$lookup' => array(
							'from'=>MDB_COMPANY,
							'localField'=> "company_id",
							'foreignField' => "_id",
							'as'=> "cdetails"
						)
					),
				array('$unwind' => '$cdetails'),
				array('$match' => $srch_query),
				array(
					'$project' => array(
					'company_status' => '$cdetails.companydetails.company_status',
					'company_name' => '$cdetails.companydetails.company_name',
					'company_address' => '$cdetails.companydetails.company_address',
					'name' => '$name',
					'company_id' => '$company_id',
					'email' => '$email',
					'user_type' => '$user_type',
					'id' => '$_id',
					)
				),
				array(
					'$sort' => array(
						'company_id' => -1
					),
				),
				array(
					'$skip' => (int)$offset
				),
				array(
				  '$limit' => (int)$val
				)
			);
			$result = $this->mongo_db->aggregate(MDB_PEOPLE,$ops);
			
			$details = $taxi_count = $driver_count = $manager_count = $package_count = array();			
			# taxi count
			$taxi_args = array(
							 array('$group' => array('_id' => array('taxi_company' => '$taxi_company'),
												'taxi_count' => array('$sum' => 1)))
							);
			$taxi_result = $this->mongo_db->Aggregate(MDB_TAXI, $taxi_args);			
			if(!empty($taxi_result['result'])){
				foreach($taxi_result['result'] as $t){
					$taxi_company = $t['_id']['taxi_company'];
					$taxi_count[$taxi_company] = $t['taxi_count'];
				}
			}
			
			# driver count			
			$driver_args = array(			
								array('$group' => array('_id' => array('company_id' => '$company_id',
								'user_type' => '$user_type'),
								'count' => array('$sum' => 1)))
							);
			$driver_result = $this->mongo_db->Aggregate(MDB_PEOPLE, $driver_args);		
			//echo "<pre>"; print_r($driver_result['result']); exit;
			if(!empty($driver_result['result'])){
				foreach($driver_result['result'] as $d){
					$user_type = isset($d['_id']['user_type'])?$d['_id']['user_type']:"";
					$company_id = isset($d['_id']['company_id'])?$d['_id']['company_id']:"";
					
					($user_type =='D') ? $driver_count[$company_id] = $d['count'] :'';
					($user_type =='M') ? $manager_count[$company_id] = $d['count'] :'';
				}
			}
			
			if(!empty($result['result'])){
				foreach ($result['result'] as $key => $res) {
					
					$details[$key]['no_of_taxi'] = (array_key_exists($res['company_id'],$taxi_count)) ? $taxi_count[$res['company_id']] : 0;
					$details[$key]['no_of_driver'] = (array_key_exists($res['company_id'],$driver_count)) ? $driver_count[$res['company_id']] : 0;
					$details[$key]['no_of_manager'] = (array_key_exists($res['company_id'],$manager_count)) ? $manager_count[$res['company_id']] : 0;
					$details[$key]['no_of_package'] = (array_key_exists($res['company_id'],$package_count)) ? $package_count[$res['company_id']] : 0;
					$details[$key]['name']            = $res['name'];
					$details[$key]['email']           = $res['email'];
					$details[$key]['cid']             = $res['company_id'];
					$details[$key]['company_name']    = (isset($res['company_name']))?$res['company_name']:'';
					$details[$key]['company_address'] = (isset($res['company_address']))?$res['company_address']:'';
					$details[$key]['company_status']  = (isset($res['company_status']))?$res['company_status']:'';
					$details[$key]['id']              = $res['id'];
				}
			}
			//echo '<pre>';print_r($details);exit;
			return $details;
		}
    }
	
	public function get_allcompany( $status="")
    {
		if ($status != "") {
			$arguments = array(
				array(
					'$match' => array('companydetails.company_status'=>$status)
				),
				array(
					'$project' => array(
						'cid' => '$_id',
						'company_name' => '$companydetails.company_name'
					)
				)
			);
		}else{
			$arguments = array(
				array(
					'$project' => array(
						'cid' => '$_id',
						'company_name' => '$companydetails.company_name'
					)
				)
			);
		}
		$result    = $this->mongo_db->aggregate(MDB_COMPANY, $arguments);
		//echo "<pre>"; print_r($result['result']); exit;
		return (!empty($result['result'])) ? $result['result'] : array();
    }
    
    public function count_country_list()
	{
		 
		$match_array=[];
		$offset=$val="";
		//$selected_fields = array('_id','country_name','iso_country_code','telephone_code', 'currency_code', 'currency_symbol', 'country_status', 'default');
		//$response = $this->mongo_db->find(MDB_CSC,$match_array,$selected_fields);
                ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,
                        'country_name'=>1,
                        'iso_country_code'=>1,
                        'telephone_code'=>1,
                        'currency_code'=>1,
                        'currency_symbol'=>1,
                        'country_status'=>1,
                        'default'=>1
                    ]
                ];
                $response = $this->mongo_db->find(MDB_CSC,$match_array,$options);
		
		$result   = $response;
		
        $data = array();
        if(count($result) > 0){
			foreach($result as $val){
				$arr = $val;
				$arr['country_id'] = $val['_id'];
				$data[] = $arr;
			}
		}
		//echo "<pre>"; print_r($data); exit;
       return $data; 
	}

	public function count_state_list()
	{
			$offset=$val="";
			//MongoDB with aggregate process only
			$ops = array(
				array('$unwind' => '$stateinfo'),
				array('$match' => array('stateinfo.state_status'=>array('$ne'=>'T'))),
				array('$project' => array('_id' => 0,
					'state_id' => '$stateinfo.state_id', 
					'state_name' => '$stateinfo.state_name',
					'state_countryid' => '$stateinfo.state_countryid',
					'state_status' => '$stateinfo.state_status',
					'state_default' => '$stateinfo.default',
					'country_name' => '$country_name'
					)
				),
				array(
					'$sort' => array(
						'country_name' => 1
					),
				)
				
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			//echo '<pre>';print_r($result);exit;
			return (!empty($result['result']))?$result['result']:array();
		 
		
	}
	public function company_login_update($id)
	{
		
		$result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>$id, 'user_type'=>'C'),array('$set'=>array('login_from' => 'W')),array('upsert'=>false));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
	}

    public function get_front_company_request($activeids)
    {
		$id=implode(", ",$activeids); 
       	$args = array(array('$match' => array('login_from'=>'D', 'user_type'=>'C', '_id'=>array('$in'=>array((int)$id)))),
				array('$lookup' => array('from' => MDB_COMPANY, 'localField' => 'company_id', 'foreignField' => '_id','as' => 'company')),
				array('$match'=>array('company.companydetails.company_status'=>'D')),
				array('$project' => array('name' => '$name', 'email' => '$email', 'org_password' => '$org_password'))
		);
		$res = $this->mongo_db->Aggregate(MDB_PEOPLE,$args);
		$data=array();
			
			if(count($res['result']) > 0){
				foreach($res['result'] as $val){
					$array = $val;
					$array['id'] = $val['_id'];
					$data[]=$array;
				}
			}
			
			return $data; 
 
    }
	public function unassign_taxi_request($activeids,$date)
	{
		$activeids = Commonfunction::mongo_format_array($activeids);
	
		//$response = $this->mongo_db->find(MDB_TAXI_DRIVER_MAPPING,array('_id'=>array('$in'=>$activeids)), array('mapping_driverid','mapping_companyid'));
		## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
		$options=[
			'projection'=>[
				'mapping_driverid'=>1,
				'mapping_companyid'=>1                     
			]
		];
		$response = $this->mongo_db->find(MDB_TAXI_DRIVER_MAPPING,array('_id'=>array('$in'=>$activeids)), $options);
		$result   = $response;
		
		if(count($result) > 0) {
			$driver_array['unassign_driver_list'] = $driver_array['trip_driver_list'] = array();
			foreach($result as $a) {
				
				$company_id = $a['mapping_companyid'];
				$driver_id = $a['mapping_driverid'];
				if($company_id == '' || $company_id == 0)
				{
					if(TIMEZONE)
					{
						$current_time = convert_timezone('now',TIMEZONE);
						$current_date = explode(' ',$current_time);
						$start_time = $current_date[0].' 00:00:01';
						$end_time = $current_date[0].' 23:59:59';
						$date = $current_date[0].' %';
					}
					else
					{
						$current_time =	date('Y-m-d H:i:s');
						$start_time = date('Y-m-d').' 00:00:01';
						$end_time = date('Y-m-d').' 23:59:59';
						$date = date('Y-m-d %');
					}
				}
				else
				{
					$args = array(array('$match'=>array('_id'=>(int)$company_id)),
						array('$project' => array('time_zone'=>'$companydetails.time_zone')),
						array('$limit'=> 1)
					);
					$res = $this->mongo_db->Aggregate(MDB_COMPANY,$args);
					if($res['result'][0]['time_zone'] != '')
					{
						$current_time = convert_timezone('now',$res['result'][0]['time_zone']);
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
				$company_condition = "";
				$match_query="";
				if($company_id != ""){
					
					$match_query=array('company_id'=>$company_id); 
					
				}
				//$match= ($company_id != "") ? array('company_id'=>$company_id) : "";
				$args = array(array('$match'=>array('driver_id'=>(int)$driver_id, 
									'pickup_time'=> array('$gte'=>$start_time), 
									'company_id'=>$company_id,
									'travel_status'=> array('$in'=>array(2,3,5,9)),
									'driver_reply'=>'A')),
				array('$project' => array('_id'=>0,'passengers_log_id'=>'$_id','travel_status'=>'$travel_status')));
				$unAsignedresult = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$args);

				if(count($unAsignedresult['result']) == 0) 
				{
					$this->mongo_db->updateMany(MDB_TAXI_DRIVER_MAPPING,array('_id'=>array('$in'=>$activeids)),array('$set'=>array('mapping_enddate' => Commonfunction::MongoDate(strtotime($date)),'mapping_status' => 'U')));
					$driver_array['unassign_driver_list'][] = $driver_id;
				} else {
					$driver_array['trip_driver_list'][] = $driver_id;
				}
			}
		} else {
			return array();
		}

		if(count($driver_array['trip_driver_list']) > 0) {
			$driver_array=$driver_array['trip_driver_list'];
			$args = array(array('$match'=>array('_id'=>array('$in' => $driver_array))), 
					array('$group'=>array('_id'=>array('_id'=>null), 'driver_name'=>array('$addToSet'=>'$name'))),
					array('$project' => array('_id'=>0, 'driver_name'=>'$driver_name')));
			$result = $this->mongo_db->Aggregate(MDB_PEOPLE,$args);
			if(isset($result['result'][0]['driver_name'])) {
				$driverName = implode(",",$result['result'][0]['driver_name']);
				$this->session->set('driverNames', $driverName);
			}
		}
		return (isset($driver_array['unassign_driver_list']) && count($driver_array['unassign_driver_list']) > 0) ? $driver_array['unassign_driver_list'] : array();
		
	}
	
	public function getDriverDeviceToken($deviceId)
	{		
		$match_array = array('_id'=> (int)$deviceId);
		//$selected_fields = array('device_type','device_token');
                //$response = $this->mongo_db->find(MDB_PEOPLE,$match_array,$selected_fields);
                 ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
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
	
	public function signoutDriver($driverId, $driver_shift_id, $currentDate)
	{		
		$result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>$driverId),array('$set'=>array('login_from'=>'','login_status'=>'N','device_id' => '','device_token' => '','device_type' => '','notification_setting'=>'0')),array('upsert'=>false));
		
		if($result){
				$result_driver = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id'=>$driverId), array('$set'=>array('shift_status'=>'OUT')),array('upsert'=>false));
				
				$result_shift = $this->mongo_db->updateOne(MDB_SHIFT_HISTORY,array('_id'=>$driver_shift_id), array('$set'=>array('shift_end'=>$currentDate)),array('upsert'=>false));
			
		}
		return $result;
	}
	
    public function active_company_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
        //==================================================================
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids); 
		
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids),'user_type'=>'C'),array('$set'=>array('status' => 'A')));
		$result1 = $this->mongo_db->updateMany(MDB_COMPANY,array('companydetails.userid'=>array('$in'=>$active_ids)),array('$set'=>array('companydetails.company_status' => 'A')));
		return (empty($result->getwriteErrors()) && empty($result1->getwriteErrors()))?1:$result->getwriteErrors();
    }
    public function block_company_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
        //==================================================================
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		//var_dump($active_ids);exit;
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids),'user_type'=>'C'),array('$set'=>array('status' => 'D')));
		$result1 = $this->mongo_db->updateMany(MDB_COMPANY,array('companydetails.userid'=>array('$in'=>$active_ids)),array('$set'=>array('companydetails.company_status' => 'D')));
		//echo '<pre>';print_r($result);print_r($result1);exit;
		return (empty($result->getwriteErrors()) && empty($result1->getwriteErrors()))?1:$result->getwriteErrors();
    }
	public function trash_company_request($activeids)
    {
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids),'user_type'=>'C'),array('$set'=>array('status' => 'T')));
		$result1 = $this->mongo_db->updateMany(MDB_COMPANY,array('companydetails.userid'=>array('$in'=>$active_ids)),array('$set'=>array('companydetails.company_status' => 'T')));
		return (empty($result->getwriteErrors()) && empty($result1->getwriteErrors()))?1:$result->getwriteErrors();
    }
	public function count_driver_list(){
		
		$count = $this->all_driver_list('','',true);
		return $count;
	}
	
	public function all_driver_list( $offset = "", $val = "", $find_count = FALSE)
    {
		$user_createdby                  = $this->userid;
		$usertype                        = $this->usertype;
		$company_id                      = $this->company_id;
		$country_id                      = $this->country_id;
		$state_id                        = $this->state_id;
		$city_id                         = $this->city_id;
		$match_query                     = array();
		$match_query['people.user_type'] = 'D';
		$peoples = $details = array();
		
		//$people_list = $this->mongo_db->Find(MDB_PEOPLE,array(),array('_id','name'));		
                 ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,
                        'name'=>1
                    ]
                ];
                $people_list = $this->mongo_db->find(MDB_PEOPLE,[],$options);		
		if(!empty($people_list)){
			foreach($people_list as $p){
				$peoples[$p['_id']] = $p['name'];
			}
		}
		
		if ($usertype == 'M') {
			$match_query['people.company_id']    = (int) $company_id;
			/*$match_query['_id'] = (int) $country_id;
			$match_query['stateinfo.state_id']   = (int) $state_id;
			$match_query['cityinfo.city_id']    = (int) $city_id;
			$match_query['people.login_country'] = (int) $country_id;
			$match_query['people.login_state']   = (int) $state_id;
			$match_query['people.login_city']    = (int) $city_id;*/
			
		} else if ($usertype == 'C') {
			$match_query['people.company_id'] = (int) $company_id;
		}
		//echo "<pre>"; print_r($match_query); exit;
		$common_arguments = array(
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
					'foreignField' => 'login_country',
					'foreignField' => 'login_city',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'people.company_id',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			//array('$unwind' => '$company'),
			array(
				'$lookup' => array(
					'from' => MDB_DRIVER_INFO,
					'localField' => 'people._id',
					'foreignField' => '_id',
					'as' => 'driver'
				)
			),
			//array('$unwind' => '$driver'),
			array('$match' => $match_query),
		);
		$field_arguments = array(
				
				array(
					'$project' => array(
						'id' => '$people._id',
						'user_createdby' => '$people.user_createdby',
						'name' => '$people.name',
						'username' => '$people.username',
						'email' => '$people.email',
						'company_name' => '$company.companydetails.company_name',
						'address' => '$people.address',
						'availability_status' => '$people.availability_status',
						'status' => '$people.status',
						'driver_license_id' => '$people.driver_license_id',
						'shift_status' => '$driver.shift_status',
						'phone' => '$people.phone',
						'country_name' => '$country_name',
						'state_name' => '$stateinfo.state_name',
						'city_name' => '$stateinfo.cityinfo.city_name',
						'userid' => '$company.companydetails.userid',
						'photo' => '$people.profile_picture',
						'driver_status' => '$people.status'
					)
				),
				array(
					'$sort' => array( 
						'id' => -1
					),
				)				
			);
		if($find_count != true){
			
			$field_arguments[]['$skip'] = (int)$offset;
			$field_arguments[]['$limit'] = (int)$val;
		}
		$merge_arguments = array_merge($common_arguments, $field_arguments);
		$result    = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
		
		if(!empty($result['result'])){
			foreach($result['result'] as $key => $res)		
			{					
				//$details[$key]['created_by'] = $this->userNamebyId($res['user_createdby']);			
				$details[$key]['created_by'] = (isset($res['user_createdby']) && array_key_exists($res['user_createdby'],$peoples)) ? $peoples[$res['user_createdby']] : '';
				$details[$key]['name'] = isset($res['name']) ? $res['name']: '';
				$details[$key]['username'] = isset($res['username']) ? $res['username']: '';
				$details[$key]['email'] = isset($res['email']) ? $res['email']: '';
				$details[$key]['address'] = isset($res['address']) ? $res['address']: '';	
				$details[$key]['availability_status'] = isset($res['availability_status']) ? $res['availability_status']: '';		
				$details[$key]['company_name'] = isset($res['company_name'][0]) ? $res['company_name'][0]: '';
				$details[$key]['status'] = isset($res['driver_status']) ? $res['driver_status']: '';
				$details[$key]['id'] = isset($res['id']) ? $res['id']: '';
				$details[$key]['driver_license_id'] = isset($res['driver_license_id']) ? $res['driver_license_id']: '';
				$details[$key]['shift_status'] = isset($res['shift_status'][0]) ? $res['shift_status'][0]: '';
				$details[$key]['phone'] = isset($res['phone']) ? $res['phone']: '';
				$details[$key]['country_name'] = isset($res['country_name']) ? $res['country_name']: '';
				$details[$key]['city_name'] = isset($res['city_name']) ? $res['city_name']: '';
				$details[$key]['state_name'] = isset($res['state_name']) ? $res['state_name']: '';
				$details[$key]['cid'] = isset($res['userid'][0]) ? $res['userid'][0]: '';
				$details[$key]['photo'] = isset($res['photo']) ? $res['photo']: '';
				$details[$key]['driver_status'] = isset($res['driver_status']) ? $res['driver_status']: '';
			}
		}
		//echo '<pre>';print_r($result);exit;
		return $details;		
    }
   
    public function active_driver_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
        //==================================================================
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('status' => 'A')));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function block_driver_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
        //==================================================================
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('status' => 'D')));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function count_model_list()
    {
        $count = $this->all_model_list('','',true);
        return $count;
    }
    public function all_model_list($offset, $val,$find_count=false)
    {
		$result = $temp_arr = array();
		if($find_count){
			$result = $this->mongo_db->count(MDB_MOTOR_MODEL,array('model_status'=>array('$ne'=>'T')),array('_id','model_name','model_status'));
			return $result;
		} else {
			$args = array(
				array('$match' => array('model_status'=>array('$ne'=>'T'))),
				array('$lookup' => array('from' => MDB_MOTOR_COMPANY, 'localField' => 'motor_id', 'foreignField' => '_id','as' => 'motor')),
				array('$unwind' => '$motor'),
				array('$project' => array('_id'=> 0, 'model_id' => '$_id',
										'model_name' => '$model_name',
										'model_status' => '$model_status',
										'motor_name' => '$motor.motor_name')),
				array('$sort' => array('model_name'=>-1)),
				array('$skip' => (int)$offset),
				array('$limit' => (int)$val)
			);
			$res = $this->mongo_db->Aggregate(MDB_MOTOR_MODEL,$args);
			if(!empty($res['result'])){
				$result = $res['result'];
			}
			return $result;
		}
    }
	public function count_searchmodel_list($keyword = "", $status = "")
    {
        $count = $this->get_all_model_searchlist($keyword, $status,'','',true);
        return $count;
    }
	public function get_all_model_searchlist($keyword = "", $status = "", $offset = "", $val = "",$find_count=false)
    {
        $result = $temp_arr = array();
        $keyword     = str_replace("%", "!%", $keyword);
        $keyword     = str_replace("_", "!_", $keyword);
		$srch_query = array('_id' => array('$ne' => 0));
		if((!empty($keyword)) && (!empty($status))) {
			$or = array('$or' =>array(array( "model_name" => Commonfunction::MongoRegex("/$keyword/i")),array("motor.motor_name" => Commonfunction::MongoRegex("/$keyword/i"))));
			$srch_query = array( "\$and" => array($or, array("model_status" => $status ) ) );
		} else if (!empty($keyword)) {
			$or = array('$or' =>array(array( "model_name" => Commonfunction::MongoRegex("/$keyword/i")),array("motor.motor_name" => Commonfunction::MongoRegex("/$keyword/i"))));
			$srch_query = array( "model_name" => Commonfunction::MongoRegex("/$keyword/i"));
		} else if (!empty($status)) {
			$srch_query = array("model_status" => $status );
		}
		
		if($find_count){
			$result = $this->mongo_db->count(MDB_MOTOR_MODEL,array('model_status'=>array('$ne'=>'T')),array('_id','model_name','model_status'));
			return $result;
		} else {
			$args = array(				
				array('$lookup' => array('from' => MDB_MOTOR_COMPANY, 'localField' => 'motor_id', 'foreignField' => '_id','as' => 'motor')),
				array('$unwind' => '$motor'),
				array('$match' => $srch_query),
				array('$project' => array('_id'=> 0, 'model_id' => '$_id',
										'model_name' => '$model_name',
										'model_status' => '$model_status',
										'motor_name' => '$motor.motor_name')),
				array('$sort' => array('model_name'=>-1)),
				array('$skip' => (int)$offset),
				array('$limit' => (int)$val)
			);
			$res = $this->mongo_db->Aggregate(MDB_MOTOR_MODEL,$args);
			if(!empty($res['result'])){
				$result = $res['result'];
			}
			//echo '<pre>';print_r($result);exit;
			return $result;
		}
    }
	public function block_model_request($activeids)
    {
		$actual_count = count($activeids);
		$blockedin_db = $this->mongo_db->Count(MDB_MOTOR_MODEL,array('model_status'=>'D'));
		$count = $actual_count + $blockedin_db;
		if($count < 3){
			$active_ids = Commonfunction::mongo_format_array($activeids);
			# live trips check
			$tripCount = $this->check_intrip($active_ids);
			if($tripCount == 0){
				$result = $this->mongo_db->updateMany(MDB_MOTOR_MODEL,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('model_status' => 'D')));
				$this->unassign_taxis($active_ids);				
				return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
			}
			return -2;
		}
		return -1;		
    }
    
    public function trash_model_request($activeids)
    {
		$actual_count = count($activeids);
		$blockedin_db = $this->mongo_db->Count(MDB_MOTOR_MODEL,array('model_status'=>'T'));
		$count = $actual_count + $blockedin_db;
		if($count < 3){
			$active_ids = Commonfunction::mongo_format_array($activeids);
			# live trips check
			$tripCount = $this->check_intrip($active_ids);
			if($tripCount == 0){
				$result = $this->mongo_db->updateMany(MDB_MOTOR_MODEL,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('model_status' => 'T')));
				$this->unassign_taxis($active_ids);
				return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
			}	
			return -2;		
		}
		return -1;
    }
    
    public function unassign_taxis($active_ids){		
		
		$match_query = array('taxi.taxi_model' => array('$in' => $active_ids), 
							'mapping_status' => 'A',
						);
		$args = array(
			array('$lookup' => array(
					'from' => MDB_TAXI,
					'localField' => 'mapping_taxiid',
					'foreignField' => '_id',
					'as' => 'taxi'
				)
			),			
			array('$unwind' => '$taxi'),	
			array('$match'  => $match_query),
			array('$project' => array(
					'mapping_id' => '$_id',
					'driver_id' => '$mapping_driverid',
					'company_id' => '$mapping_companyid'
				)
			)				
		);
		$res = $this->mongo_db->aggregate(MDB_TAXI_DRIVER_MAPPING,$args);
		
		if(!empty($res['result'])){
			$unassign_ids = array();
			foreach($res['result'] as $mappingid){
				$unassign_ids[] = (int)$mappingid['mapping_id'];
			}
			
			# logout drivers & send push notification
			$commMdl      = Model::factory( 'commonmodel' );
			$customer_google_api = $commMdl->select_site_settings('driver_android_key', MDB_SITEINFO);
			foreach($res['result'] as $mappingid){
				
				$driverId = $mappingid['driver_id'];	
				$companyId = $mappingid['company_id'];	
				$driverDeviceDets = $this->getDriverDeviceToken( $driverId );
				$driver_shift     = $this->get_shift_status( $driverId );
                $driverShiftId    = isset( $driver_shift[0]['driver_shift_id'] ) ? $driver_shift[0]['driver_shift_id'] : 0;
                if ( count( $driverDeviceDets ) > 0 && !empty( $driverDeviceDets[0]['device_token'] ) ) {
                    $pushMessage = array(
						"message" => __( 'taxi_unassigned_by_admin' ),
                        "status" => 15,
                        "display" => 1 
                    );
                    $commMdl->send_pushnotification( $driverDeviceDets[0]['device_token'], $driverDeviceDets[0]['device_type'], $pushMessage, $customer_google_api );
                }
                $date         = $commMdl->getcompany_all_currenttimestamp($companyId);
                $signout = $this->signoutDriver( $driverId, $driverShiftId, $date );
			}
			
			# unassign taxi's & driver's
			$this->mongo_db->updateMany(MDB_TAXI_DRIVER_MAPPING,array('_id'=>array('$in'=>$unassign_ids)),array('$set'=>array('mapping_enddate' => Commonfunction::MongoDate(strtotime($this->currentdate)),'mapping_status' => 'U')));
		}	
		return 1;
	}
	
	public function get_shift_status($id)
    {		
		## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
		$options=[
			'sort'=>[
				'_id'=>-1                        
			],
			'limit'=>1
		];
		$res = $this->mongo_db->find(MDB_SHIFT_HISTORY,array('driver_id' => (int)$id),$options);
        $result = $res;
        return (!empty($result)) ? Commonfunction::change_key($result) : array();
    }
	
    public function check_intrip($active_ids){
		
		$Intrip = array(3,2,5);
		$match_query = array('model._id' => array('$in' => $active_ids), 'travel_status' => array('$in' => $Intrip));
		$args = array(
			array('$lookup' => array(
					'from' => MDB_TAXI,
					'localField' => 'taxi_id',
					'foreignField' => "_id",
					'as' => "taxi"
				)
			),			
			array('$unwind' => '$taxi'),	
			array('$lookup' => array(
					'from' => MDB_MOTOR_MODEL,
					'localField' => 'taxi.taxi_model',
					'foreignField' => '_id',
					'as' => "model"
				)
			),	
			array('$unwind' => '$model'),
			array('$match'  => $match_query),
			array('$project' => array(
					'travel_status' => '$travel_status'
				)
			)				
		);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		$tripCount = (!empty($res['result'])) ? 1 : 0;
		return $tripCount;
	}
	
	public function active_model_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
        //==================================================================
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_MOTOR_MODEL,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('model_status' => 'A')));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
	
	public function model_motordetails($uid)
    {
		$result = $this->mongo_db->findOne(MDB_MOTOR_MODEL,array("_id"=>(int)$uid));
		//echo '<pre>';print_r($result);exit;
		return (!empty($result))?$result:array();
    }
    
	public function count_fare_list($company_id)
    {
		$count = $this->all_fare_list($company_id,'','',true);
		return $count;
    }
    
    public function all_fare_list($company_id, $offset, $val,$find_count = FALSE)
	{
		$result = array();
		if($find_count == TRUE){
			//$result = $this->mongo_db->find(MDB_COMPANY,array('_id' => (int)$company_id),array('model_fare.model_id'));
                     ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                    $options=[
                        'projection'=>[
                            'model_fare.model_id'=>1                                              
                        ]
                    ];
                    $result = $this->mongo_db->find(MDB_COMPANY,array('_id' => (int)$company_id),$options);

			$res = $result;
			return (!empty($res) && isset($res[$company_id]['model_fare']))?count($res[$company_id]['model_fare']):array();
		}else{
			//MongoDB with aggregate process only
			$ops = array(
				array('$unwind' => '$model_fare'),
				array('$lookup' => array(
						'from' => MDB_MOTOR_MODEL,
						'localField'=> 'model_fare.model_id',
						'foreignField'=> '_id',
						'as'=> 'motor_model'
					)
				),
				array('$unwind' => '$motor_model'),
				array('$match' => array('_id' => (int)$company_id, 'model_fare.fare_status'=>array('$nin'=>array('T')))),
				array('$project' => array('_id' => 0,
					'model_id' => '$model_fare.model_id',
					'model_name' => '$model_fare.model_name',
					'fare_status' => '$model_fare.fare_status',
					'model_status' => '$motor_model.model_status',
					)
				),
				array(
					'$sort' => array( 
						'model_fare.model_name' => 1
					),
				),
				array(
					'$skip' => (int)$offset
				),
				array(
				  '$limit' => (int)$val
				)
			);
			$res = $this->mongo_db->aggregate(MDB_COMPANY,$ops);
			if(!empty($res['result'])){
				$result_arr = $res['result'];
				$result = array_map(
				function($result_arr) {
					$i=0;
					return array(
						'company_model_fare_id' => $result_arr['model_id'],
						'model_id' => $result_arr['model_id'],
						'model_name' => $result_arr['model_name'],
						'fare_status' => $result_arr['fare_status'],
						'model_status' => $result_arr['model_status'],
					);
					$i++;
				}, $result_arr);
			}
			//~ echo '<pre>';print_r($result);exit;
			return $result;
		}
    }
    public function count_company_searchmodel_list($keyword = "", $status = "", $offset = "", $val = "",$find_count=false){ }
    public function get_all_company_list()
    {
		//MongoDB
		$ops = array(
			array('$match' => array('companydetails.company_status'=>'A')),
			array(
				'$project' => array('_id' => 0,
				'cid' => '$_id',
				'company_name' => '$companydetails.company_name',
				)
			),
			array(
				'$sort' => array(
					'_id' => -1
				),
			),
		);
		$result = $this->mongo_db->aggregate(MDB_COMPANY,$ops);
		//echo '<pre>';print_r($result);exit;
		return (!empty($result))?$result['result']:array();
    }
    public function get_rating_company()
    {   
          ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        'companydetails.company_name'=>1,
                        '_id'=>1,
                        'companyinfo.company_brand_type'=>1
                    ],
                    'sort'=>[
                        '_id'=>1
                    ]
                ];
        $company_res = $this->mongo_db->find(MDB_COMPANY,array('companydetails.company_status'=>'A'),$options);
		$data = array();
        if(count($company_res) > 0){
			foreach($company_res as $val){
				$arr['cid'] = $val['_id'];
				$arr['company_name'] = isset($val['companydetails']['company_name'])?$val['companydetails']['company_name']:"";
				$arr['company_brand_type'] = isset($val['companyinfo']['company_brand_type'])?$val['companyinfo']['company_brand_type']:"";
				$data[] = $arr;
			}
		}
       return $data; 
    }
    public function get_company_all_model_searchlist($keyword = "", $status = "", $offset = "", $val = "",$find_count=false)
    {
		$company_id     = $this->company_id;
        $keyword     = str_replace("%", "!%", $keyword);
        $keyword     = str_replace("_", "!_", $keyword);
		$result = array();
		//MongoDB with aggregate process only
		if($status!=""){
			$match_query = array('_id' => (int)$company_id, 'model_fare.fare_status' => $status );	
		}else{
			$match_query = array('_id' => (int)$company_id);
		}
		
		if(!empty($keyword)) {
			$srch_query = array( "\$and" => array($match_query,array("\$or"=>array(array( 'model_fare.model_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		} else {
			$srch_query = $match_query;
		}
		
		//echo '<pre>';print_r($srch_query); exit;
		if($find_count == TRUE) {
			$ops = array(
				array('$match' => $srch_query),
				array(
						'$group'=>array("_id"=>NULL,'count'=>array(
							'$sum' =>array('$size'=> '$model_fare')
						)
					)
				)
			);
			$result = $this->mongo_db->aggregate(MDB_COMPANY,$ops);
			//echo '<pre>';print_r($result);exit;
			return (!empty($result['result']) && isset($result['result'][0]['count']))?$result['result'][0]['count']:0;
		} else {
			$ops = array(
					array('$unwind' => '$model_fare'),
					array('$match' => $srch_query),
					array('$project' => array('_id' => 0,
					'model_id' => '$model_fare.model_id',
					'model_name' => '$model_fare.model_name',
					'fare_status' => '$model_fare.fare_status',
					)
				),
				array(
					'$sort' => array( 
						'model_fare.model_name' => 1
					),
				),
				array(
					'$skip' => (int)$offset
				),
				array(
				  '$limit' => (int)$val
				)
			);
			$res = $this->mongo_db->aggregate(MDB_COMPANY,$ops);
			if(!empty($res['result'])){
				$result_arr = $res['result'];
				$result = array_map(
						function($result_arr) {
							$i=0;
							return array(
								'company_model_fare_id' => $result_arr['model_id'],
								'model_id' => $result_arr['model_id'],
								'model_name' => $result_arr['model_name'],
								'fare_status' => $result_arr['fare_status'],
							);
							$i++;
						}, $result_arr);
			}
			return $result;
		}
    }
    
    public function active_fare_request($activeids)
    {
		$company_id     = (int)$this->company_id;
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$args = array(array('$unwind' => '$model_fare'),
				  array('$match' => array('_id' => (int)$company_id)),
				  array('$project' => array('model_id' => '$model_fare.model_id'))
				);
		$keys = $this->mongo_db->aggregate(MDB_COMPANY,$args);
		foreach($keys['result'] as $k => $v ){
			if(in_array($v['model_id'],$active_ids)){
				$model_fare = array("model_fare.".$k.".fare_status" => 'A');
				$res = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>$company_id),array('$set'=>$model_fare),array('upsert'=>true));
			}
		}	
		return (empty($res->getwriteErrors()))?1:0;
    }
    
    public function block_fare_request($activeids)
    {
        $company_id     = (int)$this->company_id;
        $active_ids = Commonfunction::mongo_format_array($activeids);
        $val = array();
        $args = array(array('$unwind' => '$model_fare'),
                          array('$match' => array('_id' => (int)$company_id)),
                          array('$project' => array('model_id' => '$model_fare.model_id'))
                        );
        $keys = $this->mongo_db->aggregate(MDB_COMPANY,$args);
        foreach($keys['result'] as $k => $v ){
                if(in_array($v['model_id'],$active_ids)){
                        $model_fare = array("model_fare.".$k.".fare_status" => 'D');
                        $res = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>$company_id),array('$set'=>$model_fare),array('upsert'=>true));
                }
        }	
        return (empty($res->getwriteErrors()))?1:0;
    }
    public function all_taxi_list($offset, $val,$find_count=false)
    {
        $taxi_createdby = $this->user_createdby;
        $usertype       = $this->usertype;
        $company_id     = $this->company_id;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
		//MongoDB
		if ($usertype == 'M') {
			$match_query = array(
								//~ 'taxi.taxi_country'=>(int)$country_id,
								//~ 'taxi.taxi_state'=>(int)$state_id,
								//~ 'taxi.taxi_city'=>(int)$city_id,
								'taxi.taxi_company'=>(int)$company_id
								);
		} else if ($usertype == 'C') {
			$match_query = array('taxi.taxi_company'=>(int)$company_id);
		} else {
			$match_query = array('taxi.taxi_status'=>array('$ne'=>''));
		}
		if($find_count){
			$arguments = array(
				array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$lookup' => array(
						'from' => MDB_TAXI,
						'localField'=> 'stateinfo.cityinfo.city_id',
						'foreignField'=> "taxi_country",
						'foreignField'=> "taxi_city",
						'as'=> "taxi"
					)
				),
				array('$unwind' => '$taxi'),
				array('$lookup' => array(
						'from' => MDB_COMPANY,
						'localField' => 'taxi.taxi_company',
						'foreignField' => "_id",
						'as' => "company"
					)
				),
				//array('$unwind' => '$company'),
				array('$lookup' => array(
						'from' => MDB_MOTOR_MODEL,
						'localField' => 'taxi.taxi_model',
						'foreignField' => "_id",
						'as' => "motormodel"
					)
				),
				//array('$unwind' => '$motormodel'),
				array('$match'  => $match_query),
				array('$sort' =>array('taxi.created_date' => -1) ),
				array('$project' => array('_id'=>0,
						'taxi_id' => '$taxi._id',
					)
				),array(
					'$group' => array(
						'_id' => NULL,
						'count' => array(
							'$sum' => 1
						)
					)
				)
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count']:0;
		} else {
			$result = array();
			$arguments = array(
				array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$lookup' => array(
						'from' => MDB_TAXI,
						'localField'=> 'stateinfo.cityinfo.city_id',
						'foreignField'=> "taxi_country",
						'foreignField'=> "taxi_city",
						'as'=> "taxi"
					)
				),
				array('$unwind' => '$taxi'),
				array('$lookup' => array(
						'from' => MDB_COMPANY,
						'localField' => 'taxi.taxi_company',
						'foreignField' => "_id",
						'as' => "company"
					)
				),
				//array('$unwind' => '$company'),
				array('$lookup' => array(
						'from' => MDB_MOTOR_MODEL,
						'localField' => 'taxi.taxi_model',
						'foreignField' => '_id',
						'as' => 'motormodel'
					)
				),
				array('$unwind' => '$motormodel'),
				array('$lookup' => array(
						'from' => MDB_MOTOR_COMPANY,
						'localField' => 'motormodel.motor_id',
						'foreignField' => '_id',
						'as' => 'motor'
					)
				),
				array('$unwind' => '$motor'),
				array('$match'  => $match_query),					
				array('$project' => array('_id'=>0,
						'created_by' => '$taxi.taxi_createdby',
						'taxi_id' => '$taxi._id',
						'taxi_availability' => '$taxi.taxi_availability',
						'taxi_status' => '$taxi.taxi_status',
						'company_name' => '$company.companydetails.company_name',
						'model_name' => '$motormodel.model_name',
						'motor_name' => '$motor.motor_name',
						'taxi_capacity' => '$taxi.taxi_capacity',
						'taxi_no' => '$taxi.taxi_no',
						'taxi_fare_km' => '$taxi.taxi_fare_km',
						'company_id' => '$taxi.taxi_company',
						'taxi_owner_name' => '$taxi.taxi_owner_name',
						'userid' => '$company.companydetails.userid',
					)
				),		
				array('$sort' =>array('taxi_id' => -1)),		
				array('$skip' => (int)$offset),
				array('$limit' => (int)$val)
			);
			$res = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			//echo "<pre>"; print_r($arguments); exit;
			if(!empty($res['result'])){	
				foreach($res['result'] as $r){
					
					$r['company_name'] = !empty($r['company_name']) ? $r['company_name'][0] : '';
					$r['model_name'] = isset($r['model_name']) ? $r['model_name'] : '';
					$r['motor_name'] = isset($r['motor_name']) ? $r['motor_name'] : '';
					$r['cid'] = !empty($r['userid']) ? $r['userid'][0] : '';
					$result[] = $r;
				}
			}
			//echo "<pre>"; print_r($result); exit;
			return $result;
		}
    }
    
    public function count_searchtaxi_list($keyword = "", $status = "", $company = "", $taxi_list='')
    {
		$count = $this->get_all_taxi_searchlist($keyword, $status, $company,'','',$taxi_list,true);
		return $count;
	}
	
    public function get_all_taxi_searchlist($keyword = "", $status = "", $company = "", $offset = "", $val = "",$taxi_list= '',$find_count=false)
    {
        $result = array();
		$srch_query = array('_id' => array('$ne' => 0));
		$taxilist = array();
		if($taxi_list != ''){
			$taxilist = explode(',',$taxi_list);
			$taxilist = Commonfunction::mongo_format_array($taxilist);
		}
		//print_r($taxilist); exit;
        $taxi_createdby = $this->user_createdby;
        $usertype       = $this->usertype;
        $company_id     = $this->company_id;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
		$srch_query = array();
		if(!empty($status)){
			$taxistatus = array('taxi_status' => $status);
			if($status == 'U'){
				$taxistatus = array('$and'=>array(
									array('taxi_status' => 'A'),
									array('taxi_availability' => 'A'),
									array('_id' => array('$nin' => $taxilist))));
			}
		}
		
		if((!empty($keyword)) && (!empty($status)) && (!empty($company))) {
			$srch_query = array( "\$and" => array( array('taxi_company' => (int)$company ),$taxistatus,
								array("\$or"=>array(array( 'taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , 
								array( 'taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),
								array( 'companyinfo.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) 
				));
		} else if ((!empty($keyword)) && (!empty($status))) {
			if($usertype=='A') {
				$srch_query = array( "\$and" => array($taxistatus,
								array("\$or"=>array(array( 'taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , 
								array( 'taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),
								array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) 
				));
			} else {
				$srch_query = array( "\$and" => array( array('taxi_company' => (int)$company ),$taxistatus,
								array("\$or"=>array(array( 'taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) ,
								array( 'taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),
								array( 'companyinfo.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) 
				));
			}
		} else if ((!empty($keyword)) && (!empty($company))) {
			$srch_query = array( "\$and" => array( array('taxi_company' => (int)$company ),
								array("\$or"=>array(array( 'taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , 
								array( 'taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),
								array( 'companyinfo.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) )
				));
		} else if ((!empty($status)) && (!empty($company))) {
			$srch_query = array( "\$and" => array(array('taxi_company' => (int)$company ), $taxistatus ) );
		} else if (!empty($keyword)) {
			if($usertype=='A') {
				$srch_query = array( "\$and" => array(
								array("\$or"=>array(array( 'taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , 
								array( 'taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),
								array( 'companyinfo.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) 
				));
			} else {
				$srch_query = array( "\$and" => array(  array('taxi_company' => (int)$company ),
								array("\$or"=>array(array( 'taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , 
								array( 'taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),
								array( 'companyinfo.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) )
				));
			}
		} else if (!empty($company)) {
			$srch_query = array( "\$and" => array(  array('taxi_company' => (int)$company )));
		} else if (!empty($status)) {
			if($usertype=='A'){
				$srch_query = $taxistatus;
			} else {
				$srch_query = array( "\$and" => array(  array('taxi_company' => (int)$company ),$taxistatus));
			}
		}
		else
		{
			$srch_query = array("_id" => array('$gte' => 1));
		}
		//echo '<pre>';print_r($srch_query);exit;
		$com_arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_CSC,
					'localField' => 'taxi_country',
					'foreignField' => '_id',
					'as' => 'csc'
				)
			),
			array('$unwind' => '$csc'),
			array('$unwind' => '$csc.stateinfo'),
			array('$unwind' => '$csc.stateinfo.cityinfo'),
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'taxi_company',
					'foreignField' => '_id',
					'as' => 'companyinfo'
				)
			),
			//array('$unwind' => '$company'),
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'taxi_createdby',
					'foreignField' => '_id',
					'as' => 'people'
				)
			),
			array(
				'$lookup' => array(
					'from' => MDB_MOTOR_MODEL,
					'localField' => 'taxi_model',
					'foreignField' => '_id',
					'as' => 'motor_model'
				)
			),
			array('$unwind' => '$motor_model'),
			array('$lookup' => array(
					'from' => MDB_MOTOR_COMPANY,
					'localField' => 'motor_model.motor_id',
					'foreignField' => '_id',
					'as' => 'motor'
				)
			),
			array('$unwind' => '$motor'),
			array('$match'  => $srch_query)
		);
		$project = array(array('$project' => array(
					'created_by' => '$taxi_createdby',
					'name' => '$people.name',
					'model_name' => '$motor_model.model_name',
					'motor_name' => '$motor.motor_name',
					'taxi_id' => '$_id',
					'taxi_availability' => '$taxi_availability',
					'taxi_status' => '$taxi_status',
					'company_name' => '$companyinfo.companydetails.company_name',
					'taxi_capacity' => '$taxi_capacity',
					'taxi_no' => '$taxi_no',
					'taxi_fare_km' => '$taxi_fare_km',
					'company_id' => '$taxi_company',
					'taxi_owner_name' => '$taxi_owner_name',
					'userid' => '$companyinfo.companydetails.userid',
					'country' => array('$cond' => array('if' => array('$eq' => array('$taxi_country','$csc._id')),'then'=>1,'else'=>0)),
					'state' => array('$cond' => array('if' => array('$eq' => array('$taxi_state','$csc.stateinfo.state_id')),'then'=>1,'else'=>0)),
					'city' => array('$cond' => array('if' => array('$eq' => array('$taxi_city','$csc.stateinfo.cityinfo.city_id')),'then'=>1,'else'=>0))
				)),
				array('$group'  =>  array('_id'  =>  array('_id' =>  '$_id' ),
								'details' => array('$first' => array(
									'created_by' => '$created_by',
									'name' => '$name',
									'model_name' => '$model_name',
									'motor_name' => '$motor_name',
									'taxi_id' => '$taxi_id',
									'taxi_availability' => '$taxi_availability',
									'taxi_status' => '$taxi_status',
									'company_name' => '$company_name',
									'taxi_capacity' => '$taxi_capacity',
									'taxi_no' => '$taxi_no',
									'taxi_fare_km' => '$taxi_fare_km',
									'company_id' => '$company_id',
									'taxi_owner_name' => '$taxi_owner_name',
									'userid' => '$userid',
									'country' => '$country',
									'state' => '$state',
									'city' => '$city'
								)
						))),
					array('$sort' =>array('details.taxi_id' => -1) ),
						
				);
		
		if($find_count != true){
			
			$project[]['$skip'] = (int)$offset;
			$project[]['$limit'] = (int)$val;
		}
		$arguments = array_merge($com_arguments,$project);
		$res = $this->mongo_db->aggregate(MDB_TAXI,$arguments);
		//echo '<pre>';print_r($res);exit;
		if(!empty($res['result'])){
			foreach($res['result'] as $key => $res)
			{
				//echo '<pre>';print_r($res);exit;
				$res = $res['details'];
				//$details[$key]['created_by'] = $this->userNamebyId($res['taxi_createdby']);
				$result[$key]['created_by'] = !empty($res['name']) ? $res['name'][0] : '';
				$result[$key]['taxi_id'] = $res['taxi_id'];
				$result[$key]['taxi_availability'] = $res['taxi_availability'];
				$result[$key]['taxi_status'] = $res['taxi_status'];
				$result[$key]['taxi_no'] = $res['taxi_no'];
				$result[$key]['company_name'] = !empty($res['company_name']) ? $res['company_name'][0] : '';	
				$result[$key]['motor_name'] = isset($res['motor_name']) ? $res['motor_name'] : '';
				$result[$key]['model_name'] = isset($res['model_name']) ? $res['model_name'] : '';
				$result[$key]['taxi_capacity'] = $res['taxi_capacity'];
				$result[$key]['taxi_fare_km'] = $res['taxi_fare_km'];
				$result[$key]['cid'] = !empty($res['userid']) ? $res['userid'][0] :'';
			}
		}
		//echo '<pre>';print_r($result);exit;
		return $result;
    }
    public function active_taxi_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
        //==================================================================
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_TAXI,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('taxi_status' => 'A')));
		//echo '<pre>';print_r($result);exit;
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function block_taxi_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
        //==================================================================
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_TAXI,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('taxi_status' => 'D')));
		//echo '<pre>';print_r($result);exit;
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
	public function trash_taxi_request($activeids)
    {
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_TAXI,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('taxi_status' => 'T')));
		//echo '<pre>';print_r($result);exit;
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function all_country_list($offset, $val,$find_count=false)
    {
		//MongoDB
		if($find_count){
			$res = $this->mongo_db->count(MDB_CSC,array('country_status'=>array('$ne'=>'T')));
			return $res;
		} else { 
			//$res = $this->mongo_db->find(MDB_CSC,array('country_status'=>array('$ne'=>'T')),array('_id','country_name','iso_country_code','telephone_code','currency_code','currency_symbol','country_status','default'))->sort(array('country_name'=>1))->skip($offset)->limit($val);
			## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[
                            'projection'=>[
                                '_id'=>1,
                                'country_name'=>1,
                                'iso_country_code'=>1,
                                'telephone_code'=>1,
                                'currency_code'=>1,
                                'currency_symbol'=>1,
                                'country_status'=>1,
                                'default'=>1                               
                            ],
                            'sort'=>[
                                'country_name'=>1
                                ],
                            'skip'=>(int)$offset,
                            'limit'=>(int)$val
                        ];
			$res = $this->mongo_db->find(MDB_CSC,['country_status'=>['$ne'=>'T']],$options);
			$result   = $res;
			$data = array();
			if(count($result) > 0){
				foreach($result as $val){
					$arr = $val;
					$arr['country_id'] = $val['_id'];
					$data[] = $arr;
				}
			}
		   return $data; 
		}
	
    }
    /** update default country status **/
    public function update_default_country($id)
    {
        if ($id == DEFAULT_COUNTRY) {
            return -2;
        }
		//MongoDB
		$pid = (int)$id;
		$country = $this->mongo_db->findOne(MDB_CSC,array('_id'=>$pid),array('country_status'));
		if(!empty($country['country_status']) && $country['country_status']=='A'){
			//update with site info collection
			$rs = $this->mongo_db->updateOne(MDB_SITEINFO,array('_id'=>1),array('$set'=>array('site_country' => $pid)),array('upsert'=>true));
			//update default status with 1
			$result = $this->mongo_db->updateOne(MDB_CSC,array('_id'=>$pid),array('$set'=>array('default' => 1)),array('upsert'=>true));
			//update default status with 0
			if(empty($result->getwriteErrors())){
				$result = $this->mongo_db->updateMany(MDB_CSC,array('_id'=>array('$ne'=>$pid),'default'=>1),array('$set'=>array('default' => 0)));
			}
			return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
		} else {
			return -1;
		}
    }
    /** update default state status **/
    public function update_default_state($pid)
    {
		$data = explode('_',$pid);
		if(count($pid) > 1){
			$country_id = (int)$data[0];
			$state_id = (int)$data[1];
		}else{
			$state_id = (int)$data[0];
			$countryid = $this->mongo_db->findOne(MDB_CSC,array('stateinfo.state_id'=>$state_id),array('_id'));
			$country_id = (int)$countryid['_id'];
		}
		
		//echo DEFAULT_STATE;exit;
		if ($state_id == DEFAULT_STATE) {
            return -2;
        }
		//MongoDB
		$state = $this->mongo_db->findOne(MDB_CSC,array('_id'=>$country_id,'stateinfo.state_id'=>$state_id),array('stateinfo'));
		//echo DEFAULT_COUNTRY.'<pre>';print_r($state);exit;
		if ($country_id == DEFAULT_COUNTRY) {
			if(!empty($state['stateinfo'][0]['state_status']) && $state['stateinfo'][0]['state_status']=='A'){
				//update with site info collection
				$rs = $this->mongo_db->updateOne(MDB_SITEINFO,array('_id'=>1),array('$set'=>array('site_state' => $state_id)),array('upsert'=>true));
				//update default status with 0
				$args  = array(array('$unwind' => '$stateinfo'),
							array('$match' => array('_id'=> $country_id)),
							//array('$project' => array('state_id' => '$stateinfo.state_id'))
						);
				$keys = $this->mongo_db->aggregate(MDB_CSC,$args);
				if(!empty($keys['result'])){
					$i=0;
					foreach($keys['result'] as $k => $v ){
						$update_arr["stateinfo.".$i.".default"] = 0;
						$i++;
						$update = $this->mongo_db->updateOne(MDB_CSC,array('_id'=> $country_id),array('$set'=>$update_arr),array('upsert' => true));
					}
				}
				//update default status with 1	
				$result = $this->mongo_db->updateOne(MDB_CSC,array('_id'=>$country_id,'stateinfo.state_id'=>$state_id),array('$set'=>array('stateinfo.$.default' => 1)),array('upsert'=>false));
				
				if(empty($result->getwriteErrors())){
					$result = $this->mongo_db->updateMany(MDB_CSC,array('_id'=>$country_id,'stateinfo.state_id'=>array('$ne'=>$state_id),'stateinfo.default'=>1),array('$set'=>array('stateinfo.$.default' => 0)));
				}
				return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
			} else {
				return -1;
			}
		}else{
			return 0;
		}
    }
    
    public function get_all_country_countlist($keyword = "", $status = "", $offset = "", $val = "")
    {
		
        if($offset == "" && $val == ""){
			$find_count = TRUE;
		}
		
        $keyword     = str_replace("%", "!%", $keyword);
        $keyword     = str_replace("_", "!_", $keyword);
		//MongoDB
		
		$srch_query = array();
		if((!empty($keyword)) && (!empty($status))) {
			$srch_query = array( "\$and" => array(array( "country_name" => Commonfunction::MongoRegex("/$keyword/i")) , array("country_status" => $status ) ) );
		} else if (!empty($keyword)) {
			$srch_query = array( "country_name" => Commonfunction::MongoRegex("/$keyword/i"));
		} else if (!empty($status)) {
			$srch_query = array("country_status" => $status );
		}
		//print_r($srch_query);exit;
		if($find_count) { //echo "1";
			//$res = $this->mongo_db->find(MDB_CSC,$srch_query,array('_id','country_name','iso_country_code','telephone_code','currency_code','currency_symbol','country_status','default'));
                    ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[
                            'projection'=>[
                                '_id'=>1,
                                'country_name'=>1,
                                'iso_country_code'=>1,
                                'telephone_code'=>1,
                                'currency_code'=>1,
                                'currency_symbol'=>1,
                                'country_status'=>1,
                                'default'=>1                             
                            ]
                        ];
                        $res = $this->mongo_db->find(MDB_CSC,$srch_query,$options);
			$result   = $res;
			$data = array();
			if(count($result) > 0){
				foreach($result as $val){
					$arr = $val;
					$arr['country_id'] = $val['_id'];
					$data[] = $arr;
				}
			}
			//echo "<pre>"; print_r($data); exit;
			return $data; 
		} else { //echo "2"; exit;
			//$res = $this->mongo_db->find(MDB_CSC,$srch_query,array('_id','country_name','iso_country_code','telephone_code','currency_code','currency_symbol','country_status','default'))->sort(array('country_name'=>1))->skip($offset)->limit($val);
                      ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[
                            'projection'=>[
                                '_id'=>1,
                                'country_name'=>1,
                                'iso_country_code'=>1,
                                'telephone_code'=>1,
                                'currency_code'=>1,
                                'currency_symbol'=>1,
                                'country_status'=>1,
                                'default'=>1                            
                            ],
                            'sort'=>[
                                'country_name'=>1
                            ],
                            'skip'=>(int)$offset,
                            'limit'=>(int)$val
                        ];
                        $res = $this->mongo_db->find(MDB_CSC,$srch_query,$options);
			$result   = $res;
			$data = array();
			if(count($result) > 0){
				foreach($result as $val){
					$arr = $val;
					$arr['country_id'] = $val['_id'];
					$data[] = $arr;
				}
			}
			return $data; 
			//return (!empty($res))?$res:array();
		}
    }
    
    
    
     /** update default city status **/
    public function update_default_city($pid)
    {
		$data = explode('_',$pid);
		if(count($pid) > 2){
			$country_id = (int)$data[0];
			$state_id = (int)$data[1];
		}elseif(count($pid) > 1){
			$country_id = (int)$data[0];
			$state_id = (int)$data[1];
			$city_id = (int)$data[2];
		}else{
			$city_id = (int)$data[0];
			$args1 = array(array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => array('stateinfo.cityinfo.city_id' => $city_id)),
				array('$project' => 
					array('country_id' => '$_id',
					'state_id' => '$stateinfo.state_id',
					'city_id' => '$stateinfo.cityinfo.city_id')
				)
			);
			$country_id = $state_id = 1;
			$csc_data = $this->mongo_db->aggregate(MDB_CSC,$args1);
			if(isset($csc_data['result']) && !empty($csc_data['result'])){
				$country_id = (int)$csc_data['result'][0]['country_id'];
				$state_id = (int)$csc_data['result'][0]['state_id'];
			}
		}
		/*$country_id = (int)$data[0];
		$state_id = (int)$data[1];
		$city_id = (int)$data[2];*/
                
		if ($city_id == DEFAULT_CITY) {
            return -2;
        }		
		//MongoDB
		$city = $this->mongo_db->findOne(MDB_CSC,array('_id'=>$country_id,'stateinfo.state_id'=>$state_id,'stateinfo.cityinfo.city_id'=>$city_id),array('stateinfo.cityinfo.city_status'));
		//Use recursive function search & get value for specific city details with array
		//$resultset = $this->recursive_array_search($city,'city_id',$city_id);
		
		if ($country_id == DEFAULT_COUNTRY && $state_id == DEFAULT_STATE) {
			if(!empty($city['stateinfo'][0]['cityinfo'][0]['city_status']) && $city['stateinfo'][0]['cityinfo'][0]['city_status']=='A'){
				//update with site info collection
				$rs = $this->mongo_db->updateOne(MDB_SITEINFO,array('_id'=>1),array('$set'=>array('site_city' => $city_id)),array('upsert'=>true));
				
				//update default status with 0
				$update_arr = array();$update_false = array();
				$args = array(array('$unwind' => '$stateinfo'),
						array('$unwind' => '$stateinfo.cityinfo'),
						array('$match' => array('_id' => $country_id, 'stateinfo.cityinfo.default' => 1)),
						array('$project' => array('state_id' => '$stateinfo.state_id'))
				);
				$state_false = $this->mongo_db->aggregate(MDB_CSC,$args);				
				if(!empty($state_false['result'])){
					//echo '<pre>';
					foreach($state_false as $s){
						if(is_array($s)){
							foreach($s as $state){
								//echo '<pre>';
								echo $state['state_id'];
								$z=0;
								$args1 = array(array('$unwind' => '$stateinfo'),
										array('$unwind' => '$stateinfo.cityinfo'),
										array('$match' => array('_id' => $country_id, 'stateinfo.state_id' => (int)$state['state_id'])),
										array('$project' => array('city_id' => '$stateinfo.cityinfo.city_id'))
								);
								$city_false = $this->mongo_db->aggregate(MDB_CSC,$args1);
								if(!empty($city_false['result'])){									
									foreach($city_false as $c => $d){
										if(is_array($d)){ //print_r($d);
											foreach($d as $city){
												$update_false['stateinfo.$.cityinfo.'.$z.'.default'] = 0;
												$result_false = $this->mongo_db->updateOne(MDB_CSC,array('_id'=>$country_id,'stateinfo.state_id'=>(int)$state['state_id'],'stateinfo.cityinfo.city_id'=> (int)$city['city_id']),array('$set'=>$update_false),array('upsert'=>true));
												$z++;
											}								
										}													
									}
								}								
							}										
						}
					}
					//exit;
				}
			//	exit;
				//update default status with 1
				$args = array(array('$unwind' => '$stateinfo'),
						array('$unwind' => '$stateinfo.cityinfo'),
						array('$match' => array('_id' => $country_id, 'stateinfo.state_id' => $state_id)),
						array('$project' => array('city_id' => '$stateinfo.cityinfo.city_id'))
				);
				$city_result = $this->mongo_db->aggregate(MDB_CSC,$args);
				$update_arr = array();$update_false = array();
				if(!empty($city_result['result'])){
					$z=0;
					foreach($city_result as $c => $d){
						if(is_array($d)){
							foreach($d as $city){
								if($city_id == $city['city_id']){
									$update_arr['stateinfo.$.cityinfo.'.$z.'.default'] = 1;
								}
								$z++;
							}										
						}				
					}
				}
				$result = $this->mongo_db->updateOne(MDB_CSC,array('_id'=>$country_id,'stateinfo.state_id'=>$state_id,'stateinfo.cityinfo.city_id'=>$city_id),array('$set'=>$update_arr),array('upsert'=>true));
				return (empty($result->getwriteErrors())) ? 1 : 0;				
			} else {
				return -1;
			}
		} else {
			return 0;
		}
    }
	//MongoDB Embedded document search value with assosciative array
    function recursive_array_search($array, $key, $value)
	{
		$results = array();
		if (is_array($array)) {
			$arrval = (isset($array[$key])) ? trim(strtolower($array[$key])) : '';
			if($value) $searchval = trim(strtolower($value));
			//search other than department and role
			if ( !empty($arrval) && (($arrval == $searchval) || (is_array($value) && in_array($arrval,$value))) ) {
				$results[] = $array;
			}
			
			foreach ($array as $subarray) {
				$results = array_merge($results, $this->recursive_array_search($subarray, $key, $value));
			}
		}
		return $results;
	}
    public function active_country_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_CSC,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('country_status' => 'A')));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function block_country_request($activeids)
    {
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		//var_dump($active_ids);
		//Checking people & taxi table whether data's are existing with country
		$people_count = $this->mongo_db->count(MDB_PEOPLE,array('login_country'=>array('$in'=>$active_ids)),	array('login_country'));
		$taxi_count = $this->mongo_db->count(MDB_TAXI,array('taxi_country'=>array('$in'=>$active_ids)),array('taxi_country'));
		if($people_count==0 && $taxi_count==0){
			$result = $this->mongo_db->updateMany(MDB_CSC,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('country_status' => 'D')));
			return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
		} else {
			return 0;
		}
    }
	public function trash_country_request($activeids)
    {
		//Here changing array values with string to integers values
		$active_ids = array();
		foreach($activeids as $each_id):
			$active_ids[] = (int)$each_id;
		endforeach;
		//var_dump($active_ids);
		//Checking people & taxi table whether data's are existing with country
		$people_count = $this->mongo_db->count(MDB_PEOPLE,array('login_country'=>array('$in'=>$active_ids)),	array('login_country'));
		$taxi_count = $this->mongo_db->count(MDB_TAXI,array('taxi_country'=>array('$in'=>$active_ids)),array('taxi_country'));
		if($people_count==0 && $taxi_count==0){
			$result = $this->mongo_db->updateMany(MDB_CSC,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('country_status' => 'T')));
			return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
		} else {
			return 0;
		}
    }
    public function block_gateway($activeids)
    {
        $company_id = $this->company_id;
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		if(!empty($active_ids)){
			$result = $this->mongo_db->updateMany(MDB_PAYMENT_GATEWAYS,array('_id'=>array('$in'=>$active_ids),'company_id'=>(int)$company_id),array('$set'=>array('payment_status' => 'D')));
			return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
		} else {
			return 0;
		}
    }
    public function trash_gateway($activeids)
    {
        $company_id = $this->company_id;
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		//var_dump($active_ids);
		if(!empty($active_ids)){
			$result = $this->mongo_db->updateMany(MDB_PAYMENT_GATEWAYS,array('_id'=>array('$in'=>$active_ids),'company_id'=>(int)$company_id),array('$set'=>array('payment_status' => 'T')));
			return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
		} else {
			return 0;
		}
    }
    public function active_gateway($activeids)
    {
        $company_id = $this->company_id;
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		//var_dump($active_ids);
		if(!empty($active_ids)){
			$result = $this->mongo_db->updateMany(MDB_PAYMENT_GATEWAYS,array('_id'=>array('$in'=>$active_ids),'company_id'=>(int)$company_id),array('$set'=>array('payment_status' => 'A')));
			return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
		} else {
			return 0;
		}
    }
    public function get_all_country_searchlist($keyword = "", $status = "", $offset = "", $val = "",$find_count=false)
    {
		if($offset == "" && $val == ""){
			$find_count = TRUE;
		}
        $keyword     = str_replace("%", "!%", $keyword);
        $keyword     = str_replace("_", "!_", $keyword);
        $srch_query = array();
		//MongoDB
		if((!empty($keyword)) && (!empty($status))) {
			$srch_query = array( "\$and" => array(array( "country_name" => Commonfunction::MongoRegex("/$keyword/i")) , array("country_status" => $status ) ) );
		} else if (!empty($keyword)) {
			$srch_query = array( "country_name" => Commonfunction::MongoRegex("/$keyword/i"));
		} else if (!empty($status)) {
			$srch_query = array("country_status" => $status );
		}
		//print_r($srch_query);exit;
		if($find_count) {
			$res = $this->mongo_db->count(MDB_CSC,$srch_query);
			return $res;
		} else {
			//$res = $this->mongo_db->find(MDB_CSC,$srch_query,array('_id','country_name','iso_country_code','telephone_code','currency_code','currency_symbol','country_status','default'))->sort(array('country_name'=>1))->skip($offset)->limit($val);
                    ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[
                            'projection'=>[
                                '_id'=>1,
                                'country_name'=>1,
                                'iso_country_code'=>1,
                                'telephone_code'=>1,
                                'currency_code'=>1,
                                'currency_symbol'=>1,
                                'country_status'=>1,
                                'default'=>1                            
                            ],
                            'sort'=>[
                                'country_name'=>1
                            ],
                            'skip'=>(int)$offset,
                            'limit'=>(int)$val
                        ];
                        $res = $this->mongo_db->find(MDB_CSC,$srch_query,$options);
			
			$result   = $res;
			$data = array();
			if(count($result) > 0){
				foreach($result as $val){
					$arr = $val;
					$arr['country_id'] = $val['_id'];
					$data[] = $arr;
				}
			}
			return $data; 
		}
    }
    
    public function count_city_list(){
		$offset = $value = "";
		$this->all_city_list($offset,$value,TRUE);
	}
    
    public function all_city_list($offset, $val,$find_count=false)
    {
		if($find_count){
			//MongoDB with aggregate process only
			$ops = array(
				array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => array('stateinfo.cityinfo.city_status'=>array('$nin'=>array('T')))),
				array('$group'=>array('_id'=>array('_id'=>null),'total_city_count'=>array('$sum'=>1)))
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			return (!empty($result['result']))?$result['result'][0]['total_city_count']:0;
		} else {
			//MongoDB with aggregate process only
			$ops = array(
				array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => array('stateinfo.cityinfo.city_status'=>array('$ne'=>'T'))),
				array('$project' => array('_id' => 0,
					'city_id' => '$stateinfo.cityinfo.city_id', 
					'city_name' => '$stateinfo.cityinfo.city_name',
					'city_stateid' => '$stateinfo.cityinfo.city_stateid',
					'city_countryid' => '$stateinfo.cityinfo.city_countryid',
					'city_status' => '$stateinfo.cityinfo.city_status',
					'city_model_fare' => '$stateinfo.cityinfo.city_model_fare',
					'city_default' => '$stateinfo.cityinfo.default',
					'state_name' => '$stateinfo.state_name',
					'country_name' => '$country_name'
					)
				),
				array(
					'$sort' => array(
						'country_name' => 1
					),
				),
				array(
					'$skip' => (int)$offset
				),
				array(
				  '$limit' => (int)$val
				)
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			return (!empty($result['result']))?$result['result']:array();
		}
    }
    public function active_city_request($activeids)
    {
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$count = count($active_ids);
		$cnt=0;
		foreach($activeids as $val){
			
			$city_id = (int)$val;
			$args1 = array(array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => array('stateinfo.cityinfo.city_id' => $city_id)),
				array('$project' => 
					array('country_id' => '$_id',
					'state_id' => '$stateinfo.state_id',
					'city_id' => '$stateinfo.cityinfo.city_id')
				)
			);
			$country_id = $state_id = 1;
			$csc_data = $this->mongo_db->aggregate(MDB_CSC,$args1);
			if(isset($csc_data['result']) && !empty($csc_data['result'])){
				$country_id = (int)$csc_data['result'][0]['country_id'];
				$state_id = (int)$csc_data['result'][0]['state_id'];
			}
				
			//Here we getting nested dcouemnts index keys while updating data with city embedded documents
			$state_index = commonfunction::get_collection_index($country_id,$state_id,$city_id,'state');
			$cityindex = commonfunction::get_collection_index($country_id,$state_id,$city_id,'city',true);
			$city_index = $cityindex['city_index'];
			$index_key = "stateinfo.".$state_index.".cityinfo.".$city_index;
			$data_set = array('city_id'=>(int)$city_id,
					'city_stateid' => (int)$state_id,
					'city_countryid' => (int)$country_id,
					'default' => $cityindex['city_default'],
					'city_name' => $cityindex['city_name'],
					'zipcode' => $cityindex['zipcode'],
					'city_model_fare' => $cityindex['city_model_fare'],
					'city_status'=>'A'
				);
			$data = array($index_key=>$data_set);
			$res = $this->mongo_db->updateOne(MDB_CSC,array('_id'=>(int)$country_id,'stateinfo.state_id'=>(int)$state_id,'stateinfo.cityinfo.city_id'=>(int)$city_id),array('$set'=>$data),array('upsert'=>true));
			
			$cnt+=(empty($res->getwriteErrors())) ? 1 : $res->getwriteErrors();
		}
		return ($count==$cnt)?1:0;
    }
    public function block_city_request($activeids)
    {
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$count = count($active_ids);
		$cnt=0;
		foreach($activeids as $val){
			
			$city_id = (int)$val;
			$args1 = array(array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => array('stateinfo.cityinfo.city_id' => $city_id)),
				array('$project' => 
					array('country_id' => '$_id',
					'state_id' => '$stateinfo.state_id',
					'city_id' => '$stateinfo.cityinfo.city_id')
				)
			);
			$country_id = $state_id = 1;
			$csc_data = $this->mongo_db->aggregate(MDB_CSC,$args1);
			if(isset($csc_data['result']) && !empty($csc_data['result'])){
				$country_id = (int)$csc_data['result'][0]['country_id'];
				$state_id = (int)$csc_data['result'][0]['state_id'];
			}
			
			$city_count = $this->mongo_db->count(MDB_SITEINFO,array('site_city'=>(int)$city_id),array('site_city'));
			
			# Here we getting nested dcouemnts index keys while updating data with city embedded documents
			$state_index = commonfunction::get_collection_index($country_id,$state_id,$city_id,'state');
			$cityindex = commonfunction::get_collection_index($country_id,$state_id,$city_id,'city',true);
			$city_index = $cityindex['city_index'];
			$index_key = "stateinfo.".$state_index.".cityinfo.".$city_index;
			$data_set = array('city_id'=>(int)$city_id,
					'city_stateid' => (int)$state_id,
					'city_countryid' => (int)$country_id,
					'default' => $cityindex['city_default'],
					'city_name' => $cityindex['city_name'],
					'zipcode' => $cityindex['zipcode'],
					'city_model_fare' => $cityindex['city_model_fare'],
					'city_status'=>'D'
				);
			$data = array($index_key=>$data_set);
			if($city_count==0){
				$res = $this->mongo_db->updateOne(MDB_CSC,array('_id'=>(int)$country_id,'stateinfo.state_id'=>(int)$state_id,'stateinfo.cityinfo.city_id'=>(int)$city_id),array('$set'=>$data),array('upsert'=>true));
				$cnt+=(empty($res->getwriteErrors())) ? 1 : $res->getwriteErrors();
			}
		}
		return ($count==$cnt)?1:0;
    }
    public function trash_city_request($activeids)
    {
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$count = count($active_ids);
		$cnt=0;
		foreach($activeids as $val){
			
			$city_id = (int)$val;
			$args1 = array(array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => array('stateinfo.cityinfo.city_id' => $city_id)),
				array('$project' => 
					array('country_id' => '$_id',
					'state_id' => '$stateinfo.state_id',
					'city_id' => '$stateinfo.cityinfo.city_id')
				)
			);
			$country_id = $state_id = 1;
			$csc_data = $this->mongo_db->aggregate(MDB_CSC,$args1);
			if(isset($csc_data['result']) && !empty($csc_data['result'])){
				$country_id = (int)$csc_data['result'][0]['country_id'];
				$state_id = (int)$csc_data['result'][0]['state_id'];
			}

			$city_count = $this->mongo_db->count(MDB_SITEINFO,array('site_city'=>(int)$city_id),array('site_city'));
			
			# Here we getting nested dcouemnts index keys while updating data with city embedded documents
			$state_index = commonfunction::get_collection_index($country_id,$state_id,$city_id,'state');
			$cityindex = commonfunction::get_collection_index($country_id,$state_id,$city_id,'city',true);
			$city_index = $cityindex['city_index'];
			$index_key = "stateinfo.".$state_index.".cityinfo.".$city_index;
			$data_set = array('city_id'=>(int)$city_id,
					'city_stateid' => (int)$state_id,
					'city_countryid' => (int)$country_id,
					'default' => $cityindex['city_default'],
					'city_name' => $cityindex['city_name'],
					'zipcode' => $cityindex['zipcode'],
					'city_model_fare' => $cityindex['city_model_fare'],
					'city_status'=>'T'
				);
			$data = array($index_key=>$data_set);
			if($city_count==0){
				$res = $this->mongo_db->updateOne(MDB_CSC,array('_id'=>(int)$country_id,'stateinfo.state_id'=>(int)$state_id,'stateinfo.cityinfo.city_id'=>(int)$city_id),array('$set'=>$data),array('upsert'=>true));
				$cnt+=(empty($res->getwriteErrors())) ? 1 : $res->getwriteErrors();
			}
		}
		return ($count==$cnt)?1:0;
    }
    public function all_state_list($offset, $val,$find_count=false)
    {
		if($find_count){
			$ops = array(
				array('$unwind'=> '$stateinfo'),
				array('$match' => array('stateinfo.state_status'=>array('$nin'=>array('T')))),				
				array('$group'=>array('_id'=>array('_id'=>null),'total_state_count'=>array('$sum'=>1)))
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			//echo "<pre>";print_r($result);exit;
			return (!empty($result['result']))?$result['result'][0]['total_state_count']:0;
		} else {
			//MongoDB with aggregate process only
			$ops = array(
				array('$unwind' => '$stateinfo'),
				array('$match' => array('stateinfo.state_status'=>array('$ne'=>'T'))),
				array('$project' => array('_id' => 0,
					'state_id' => '$stateinfo.state_id', 
					'state_name' => '$stateinfo.state_name',
					'state_countryid' => '$stateinfo.state_countryid',
					'state_status' => '$stateinfo.state_status',
					'state_default' => '$stateinfo.default',
					'country_name' => '$country_name'
					)
				),
				array(
					'$sort' => array(
						'country_name' => 1
					),
				),
				array(
					'$skip' => (int)$offset
				),
				array(
				  '$limit' => (int)$val
				)
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			//echo '<pre>';print_r($result);exit;
			return (!empty($result['result']))?$result['result']:array();
		}
    }
    public function active_state_request($activeids)
    {
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$count = count($active_ids);
		$cnt=0;
		foreach($activeids as $val){
			
			$state_id = (int)$val;
			$countryid = $this->mongo_db->findOne(MDB_CSC,array('stateinfo.state_id'=>$state_id),array('_id'));
			$country_id = (int)$countryid['_id'];
			
			$match = array('_id'=>(int)$country_id,'stateinfo.state_countryid'=>(int)$country_id,'stateinfo.state_id'=>(int)$state_id);
			$res = $this->mongo_db->updateOne(MDB_CSC, $match, array('$set'=>array('stateinfo.$.state_status' => 'A')),array('upsert'=>true));
			$cnt+=(empty($res->getwriteErrors())) ? 1 : $res->getwriteErrors();
		}
		return ($count==$cnt)?1:0;
    }
    
    public function block_state_request($activeids)
    {
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$count = count($active_ids);
		$cnt=0;
		foreach($activeids as $val){
			
			$state_id = (int)$val;
			$countryid = $this->mongo_db->findOne(MDB_CSC,array('stateinfo.state_id'=>$state_id),array('_id'));
			$country_id = (int)$countryid['_id'];
				
			# Checking site info collection whether data's are existing with country state
			$state_count = $this->mongo_db->count(MDB_SITEINFO,array('site_state'=>(int)$state_id),	array('site_state'));
			if($state_count==0){
				
				$match = array('_id'=>(int)$country_id,'stateinfo.state_countryid'=>(int)$country_id,'stateinfo.state_id'=>(int)$state_id);
				$res = $this->mongo_db->updateOne(MDB_CSC,$match,array('$set'=>array('stateinfo.$.state_status' => 'D')),array('upsert'=>false));
				$cnt+=(empty($res->getwriteErrors())) ? 1 : $res->getwriteErrors();
			}
		}
		return ($count==$cnt)?1:0;
    }
    
    public function trash_state_request($activeids)
    {
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$count = count($active_ids);
		$cnt=0;
		foreach($activeids as $val){
			
			$state_id = (int)$val;
			$countryid = $this->mongo_db->findOne(MDB_CSC,array('stateinfo.state_id'=>$state_id),array('_id'));
			$country_id = (int)$countryid['_id'];
			
			$state_count = $this->mongo_db->count(MDB_SITEINFO,array('site_state'=>(int)$state_id),	array('site_state'));
			if($state_count==0){
				
				$match = array('_id'=>(int)$country_id,'stateinfo.state_countryid'=>(int)$country_id,'stateinfo.state_id'=>(int)$state_id);
				$res = $this->mongo_db->updateOne(MDB_CSC, $match, array('$set'=>array('stateinfo.$.state_status' => 'T')),array('upsert'=>true));
				$cnt+=(empty($res->getwriteErrors())) ? 1 : $res->getwriteErrors();
			}
		}
		return ($count==$cnt)?1:0;
    }
    public function count_searchstate_list($keyword = "", $status = "", $offset = "", $val = "",$find_count = FALSE)
    {
		if($offset == "" && $val == ""){
			$find_count = TRUE;
		}
        $keyword     = str_replace("%", "!%", $keyword);
        $keyword     = str_replace("_", "!_", $keyword);
		//MongoDB with aggregate process only
		if((!empty($keyword)) && (!empty($status))) {
			$srch_query = array( "\$and" => array(array('stateinfo.state_status' => $status ),array("\$or"=>array(array( 'country_name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'stateinfo.state_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		} else if (!empty($keyword)) {
			$srch_query = array( "\$or" => array(array( 'country_name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'stateinfo.state_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) );
		} else if (!empty($status)) {
			$srch_query = array("stateinfo.state_status" => $status );
		}else{
			$srch_query = array("stateinfo.state_id" => array('$gt' => 1) );
		}
		//print_r($srch_query); exit;
		if($find_count) {
		
			$ops = array(
				array('$unwind'=> '$stateinfo'),
				array('$match' => $srch_query),				
				array('$group'=>array('_id'=>array('_id'=>null),'total_state_count'=>array('$sum'=>1)))
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			return (!empty($result['result']))?$result['result'][0]['total_state_count']:0;
		} else {
			$ops = array(
					array('$unwind' => '$stateinfo'),
					array('$match' => $srch_query),
					array('$project' => array('_id' => 0,
						'state_id' => '$stateinfo.state_id', 
						'state_name' => '$stateinfo.state_name',
						'state_countryid' => '$stateinfo.state_countryid',
						'state_status' => '$stateinfo.state_status',
						'state_default' => '$stateinfo.default',
						'country_name' => '$country_name'
					)
				),
				array(
					'$sort' => array(
						'country_name' => 1
					),
				),
				array(
					'$skip' => (int)$offset
				),
				array(
				  '$limit' => (int)$val
				)
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			//echo '<pre>else';print_r($result);//exit;
			return (!empty($result['result']))?$result['result']:array();
		}
    }
    
    public function get_all_state_searchlist($keyword = "", $status = "", $offset = "", $val = "", $find_count = FALSE)
    {
		
        if($offset == "" && $val == ""){
			$find_count = TRUE;
		}
        $keyword     = str_replace("%", "!%", $keyword);
        $keyword     = str_replace("_", "!_", $keyword);
		//MongoDB with aggregate process only
		if((!empty($keyword)) && (!empty($status))) {
			$srch_query = array( "\$and" => array(array('stateinfo.state_status' => $status ),array("\$or"=>array(array( 'country_name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'stateinfo.state_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		} else if (!empty($keyword)) {
			$srch_query = array( "\$or" => array(array( 'country_name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'stateinfo.state_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) );
		} else if (!empty($status)) {
			$srch_query = array("stateinfo.state_status" => $status );
		}else{
			$srch_query = array("stateinfo.state_id" => array('$gt' => 1));
		}
		//echo "<pre>"; print_r($srch_query); exit; 
		if($find_count) {
		
			$ops = array(
				array('$unwind'=> '$stateinfo'),
				array('$match' => $srch_query),				
				array('$group'=>array('_id'=>array('_id'=>null),'total_state_count'=>array('$sum'=>1)))
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			//echo "<pre>";print_r($result);exit;
			return (!empty($result['result']))?$result['result'][0]['total_state_count']:0;
		} else {
			$ops = array(
					array('$unwind' => '$stateinfo'),
					array('$match' => $srch_query),
					array('$project' => array('_id' => 0,
						'state_id' => '$stateinfo.state_id', 
						'state_name' => '$stateinfo.state_name',
						'state_countryid' => '$stateinfo.state_countryid',
						'state_status' => '$stateinfo.state_status',
						'state_default' => '$stateinfo.default',
						'country_name' => '$country_name'
					)
				),
				array(
					'$sort' => array(
						'country_name' => 1
					)
				),
				array(
					'$skip' => (int)$offset
				),
				array(
				  '$limit' => (int)$val
				)
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
		
			return (!empty($result['result']))?$result['result']:array();
		}
    }
    public function count_searchcity_list($keyword = "", $status = "", $offset = "", $val = "",$find_count=false)
    {
        if($offset == "" && $val == ""){
			$find_count = TRUE;
		}
        $keyword     = str_replace("%", "!%", $keyword);
        $keyword     = str_replace("_", "!_", $keyword);
		//MongoDB with aggregate process only
		$srch_query =array();
		if((!empty($keyword)) && (!empty($status))) {
			$srch_query = array( "\$and" => array(array('stateinfo.cityinfo.city_status' => $status ),array("\$or"=>array(array( 'country_name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'stateinfo.state_name' => Commonfunction::MongoRegex("/$keyword/i") ) , array( 'stateinfo.cityinfo.city_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		} else if (!empty($keyword)) {
			$srch_query = array( "\$or" => array(array( 'country_name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'stateinfo.state_name' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'stateinfo.cityinfo.city_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) );
		} else if (!empty($status)) {
			$srch_query = array("stateinfo.cityinfo.city_status" => $status );
		}
		else{
			$srch_query = array("stateinfo.cityinfo.city_id" => array('$gt' => 1));
		}
		//echo "<pre>"; print_r($srch_query); exit; 
		if($find_count) {
			$ops = array(
				array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => $srch_query),
				array('$group'=>array('_id'=>array('_id'=>null),'total_city_count'=>array('$sum'=>1)))
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			//echo '<pre>if';print_r($result);exit;
			return (!empty($result['result']))? $result['result'][0]['total_city_count'] :0;
		} else {
			$ops = array(
				array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => $srch_query),
				array('$project' => array('_id' => 0,
					'city_id' => '$stateinfo.cityinfo.city_id', 
					'city_name' => '$stateinfo.cityinfo.city_name',
					'city_stateid' => '$stateinfo.cityinfo.city_stateid',
					'city_countryid' => '$stateinfo.cityinfo.city_countryid',
					'city_status' => '$stateinfo.cityinfo.city_status',
					'city_model_fare' => '$stateinfo.cityinfo.city_model_fare',
					'city_default' => '$stateinfo.cityinfo.default',
					'state_name' => '$stateinfo.state_name',
					'country_name' => '$country_name'
					)
				),
				array(
					'$sort' => array(
						'country_name' => 1
					),
				),
				array(
					'$skip' => (int)$offset
				),
				array(
				  '$limit' => (int)$val
				)
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			return (!empty($result['result']))?$result['result']:array();
		}
    }
    
    public function get_all_city_searchlist($keyword = "", $status = "", $offset = "", $val = "",$find_count=false)
    {
        $keyword     = str_replace("%", "!%", $keyword);
        $keyword     = str_replace("_", "!_", $keyword);
        $srch_query =array();
		//MongoDB with aggregate process only
		if((!empty($keyword)) && (!empty($status))) {
			$srch_query = array( "\$and" => array(array('stateinfo.cityinfo.city_status' => $status ),array("\$or"=>array(array( 'country_name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'stateinfo.state_name' => Commonfunction::MongoRegex("/$keyword/i") ) , array( 'stateinfo.cityinfo.city_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		} else if (!empty($keyword)) {
			$srch_query = array( "\$or" => array(array( 'country_name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'stateinfo.state_name' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'stateinfo.cityinfo.city_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) );
		} else if (!empty($status)) {
			$srch_query = array("stateinfo.cityinfo.city_status" => $status );
		}
		else{
			$srch_query = array("stateinfo.cityinfo.city_id" => array('$gt' => 1));
		}
		if($find_count) {
			$ops = array(
				array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => $srch_query),
				array('$group'=>array('_id'=>array('_id'=>null),'total_city_count'=>array('$sum'=>1)))
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			//echo '<pre>if';print_r($result);exit;
			return (!empty($result['result']))? $result['result'][0]['total_city_count'] :0;
		} else {
			$ops = array(
				array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => $srch_query),
				array('$project' => array('_id' => 0,
					'city_id' => '$stateinfo.cityinfo.city_id', 
					'city_name' => '$stateinfo.cityinfo.city_name',
					'city_stateid' => '$stateinfo.cityinfo.city_stateid',
					'city_countryid' => '$stateinfo.cityinfo.city_countryid',
					'city_status' => '$stateinfo.cityinfo.city_status',
					'city_model_fare' => '$stateinfo.cityinfo.city_model_fare',
					'city_default' => '$stateinfo.cityinfo.default',
					'state_name' => '$stateinfo.state_name',
					'country_name' => '$country_name'
					)
				),
				array(
					'$sort' => array(
						'country_name' => 1
					),
				),
				array(
					'$skip' => (int)$offset
				),
				array(
				  '$limit' => (int)$val
				)
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$ops);
			return (!empty($result['result']))?$result['result']:array();
		}
    }
    public function count_searchmanager_list($keyword = "", $status = "", $company = "", $offset = "", $val = "", $find = true)
    {
        $count = $this->all_manager_searchlist($keyword, $status, $company, $offset, $val, $find);
        return $count;
    }
	
	public function all_manager_searchlist($keyword = "", $status = "", $company = "", $offset = "", $val = "", $find_count = FALSE)
    {
		$result = $temp_arr = array();
        $user_createdby = $this->user_createdby;
        $usertype       = $this->usertype;
        $company_id     = (int)$this->company_id;
        
        $keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
		$status = ($status!="")?$status:array('$ne' => 'T');
		$c_id = ($company!="")?$company:$company_id;
		if ($usertype == 'M') {
			$match_query = array('user_type'=>'M', 'login_country'=>(int) $country_id,'login_state' =>(int) $state_id,'login_city'=>(int) $city_id,'company_id' => (int)$c_id,'status' => $status);
		} else if ($usertype == 'C' && $c_id!="") {
			$match_query = array('user_type'=>'M', 'company_id' => (int)$c_id,'status' => $status );
		}elseif($c_id!="" || $c_id!=0) {
			$match_query = array('user_type'=>'M', 'company_id' => (int)$c_id,'status' => $status );
		}else{
			$match_query = array('user_type'=>'M', 'status' => $status );
		}
		if(!empty($keyword)) {
			$srch_query = array( "\$and" => array($match_query,array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i") ), array( 'email' => Commonfunction::MongoRegex("/$keyword/i") ), array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'phone' => Commonfunction::MongoRegex("/$keyword/i")) ) ) ) );
		}else{
			$srch_query = $match_query;
		}
		//echo "<pre>"; print_r($srch_query); exit;
		$common_arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_CSC,
					'localField' => 'login_country',
					'foreignField' => '_id',
					'as' => 'csc'
				)
			),
			array('$unwind' => '$csc'),
			array('$unwind' => '$csc.stateinfo'),
			array('$unwind' => '$csc.stateinfo.cityinfo'),
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'company_id',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			//array('$unwind' => '$company'),
			array(
				'$match' => $srch_query
			),
		);
		
		$field_arguments = array(
				array(
					'$sort' => array( 
						'created_date' => -1
					),
				),
				array(
					'$project' => array(
						'pid' => '$_id',
						'created_by' => '$user_createdby',
						'name' => '$name',
						'username' => '$username',
						'email' => '$email',
						'company_name' => '$company.companydetails.company_name',
						'address' => '$address',
						'availability_status' => '$availability_status',
						'status' => '$status',
						'driver_license_id' => '$driver_license_id',
						'shift_status' => '$driver.shift_status',
						'phone' => '$phone',
						'country_name' => '$csc.country_name',
						'state_name' => '$csc.stateinfo.state_name',
						'city_name' => '$csc.stateinfo.cityinfo.city_name',
						'cid' => '$company.companydetails.userid',
						'photo' => '$profile_picture',
						'driver_status' => '$status',
						'country' => array('$cond' => array('if' => array('$eq' => array('$login_country','$csc._id')),'then'=>1,'else'=>0)),
						'state' => array('$cond' => array('if' => array('$eq' => array('$login_state','$csc.stateinfo.state_id')),'then'=>1,'else'=>0)),
						'city' => array('$cond' => array('if' => array('$eq' => array('$login_city','$csc.stateinfo.cityinfo.city_id')),'then'=>1,'else'=>0)),	
					),					
				),
				array('$match' => array('country' => 1,'state' => 1,'city' => 1))
			);
			if ($find_count != TRUE) {
				$field_arguments[]['$skip'] = (int)$offset;
				$field_arguments[]['$limit'] = (int)$val;
			}
			
			$merge_arguments = array_merge($common_arguments, $field_arguments);
			//echo '<pre>';print_r($merge_arguments);exit;
			$res = $this->mongo_db->aggregate(MDB_PEOPLE, $merge_arguments);
			if(!empty($res['result'])){
				foreach($res['result'] as $r){
					$temp_arr['name'] = $r['name'];
					$temp_arr['company_name'] = !empty($r['company_name']) ? $r['company_name'][0] :'';
					$temp_arr['email'] = $r['email'];
					$temp_arr['address'] = $r['address'];					
					$temp_arr['country_name'] = $r['country_name'];
					$temp_arr['state_name'] = $r['state_name'];
					$temp_arr['city_name'] = $r['city_name'];
					$temp_arr['status'] = $r['status'];
					$temp_arr['id'] = $r['_id'];
					$temp_arr['userid'] = !empty($r['cid']) ? $r['cid'][0] :'';					
					$result[] = $temp_arr;
				}
			}
			return $result;
    }
	
	public function all_admin_searchlist($keyword = "", $status = "", $offset = "", $val = "",$find_count=false)
	{
		if($offset == "" && $val==""){
			$find_count = TRUE;
		}
		$srch_query=array();
		//MongoDB with aggregate process only
		if((!empty($keyword)) && (!empty($status))) {
			$srch_query = array( "\$and" => array(array('status' => $status ),array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'email' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		} else if (!empty($keyword)) {
			$srch_query = array( "\$or" => array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'email' => Commonfunction::MongoRegex("/$keyword/i") ) ) );
			
		} else if (!empty($status)) {
			$srch_query = array( "\$and" => array(array("status" => $status )));
		}
		else {
			$srch_query = array("_id" => array('$gt' => 1));
		}
		if($find_count) {
			$ops = array(
				array(
					  '$match' => $srch_query,
				),
				array(
					'$lookup' => array(
					'from'=>MDB_CSC,
					'localField'=> "login_country",
					'foreignField' => "_id",
					'as'=> "countrydetails"
					)
				),
				array('$unwind'=>'$countrydetails'),
				array(
						'$group'=>array('_id' => null,'count'=>array(
							'$sum' =>1
						)
					)
				)
			);
			$result = $this->mongo_db->aggregate(MDB_PEOPLE,$ops);
			return (!empty($result['result']))?count($result['result']):0;
		} else {
			$ops = array(
				array('$match' => $srch_query),
				array(
						'$lookup' => array(
						'from'=>MDB_CSC,
						'localField'=> "login_country",
						'foreignField' => "_id",
						'as'=> "countrydetails"
						)
					),
				array('$unwind' => array('path' =>'$countrydetails', 'preserveNullAndEmptyArrays'=>true)),
				array(
					'$project' => array(
					'country_name' => '$countrydetails.country_name',
					'name' => '$name',
					'email' => '$email',
					'address' => '$address',
					'status' => '$status',
					'lastname' => '$lastname',
					'login_country' => '$login_country',
					'phone' => '$phone',
					'user_type' => '$user_type'
					)
				),
				array(
					'$sort' => array(
						'_id' => 1
					),
				),
				array('$match'=>array('user_type'=>'S')),
				array(
					'$skip' => (int)$offset
				),
				array(
				  '$limit' => (int)$val
				)
			);
			$result = $this->mongo_db->aggregate(MDB_PEOPLE,$ops);
			$data = array();
			if(count($result['result']) > 0){
				foreach($result['result'] as $val){
					$arr = $val;
					$arr['id'] = $val['_id'];
					$data[] = $arr;
				}
			}
			return $data; 
 
		}
    }
    
    public function count_searchdriver_list($keyword = "", $status = "", $company = "",$peoples,$driver_list){
		
		$count = $this->get_all_driver_searchlist($keyword, $status, $company, '','',true,$peoples,$driver_list);
		return $count;
	}
	
	public function get_all_driver_searchlist($keyword = "", $status = "", $company = "", $offset = "", $val = "", $find_count = FALSE,$peoples,$driver_list)
    {
		$user_createdby                  = $this->userid;
		$usertype                        = $this->usertype;
		$company_id                      = $this->company_id;
		$country_id                      = $this->country_id;
		$state_id                        = $this->state_id;
		$city_id                         = $this->city_id;
		$keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
		$status = ($status!="")?$status:array('$ne' => 'T');
		$c_id = ($company!="")?$company:$company_id;
		$match_query = $details = array();
		
		/*$people_list = $this->mongo_db->Find(MDB_PEOPLE,array(),array('_id','name'));
		$people_list = iterator_to_array($people_list);
		if(!empty($people_list)){
			foreach($people_list as $p){
				$peoples[$p['_id']] = $p['name'];
			}
		}
		*/
		$match_query['user_type'] = 'D';
		//$match_query['status'] = $status;
		if($usertype == 'C' || ($c_id != '' && $c_id != 0)){
			$match_query['company_id'] = (int)$c_id;
		}
		if ($usertype == 'M') {
			/*$match_query['login_country'] = (int)$country_id;
			$match_query['login_state'] = (int)$state_id;
			$match_query['login_city'] = (int)$city_id;*/
		}
		$driverlist = array();
		if($driver_list != ''){
			$driverlist = explode(',',$driver_list);
			$driverlist = Commonfunction::mongo_format_array($driverlist);
		}
		if(!empty($status)){
			if($status == 'U'){
				$match_query['_id'] = array('$nin' => $driverlist);
			}else{
				$match_query['status'] = $status;
			}
		}
		if(!empty($keyword)) {
			$srch_query = array( "\$and" => array($match_query,array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i") ), array( 'email' => Commonfunction::MongoRegex("/$keyword/i") ), array( 'phone' => Commonfunction::MongoRegex("/$keyword/i")) ) ) ) );
		}else{
			$srch_query = $match_query;
		}
		//print_r($srch_query); exit;
		$common_arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_CSC,
					'localField' => 'login_country',
					'foreignField' => '_id',
					'as' => 'csc'
				)
			),
			array('$unwind' => '$csc'),
			array('$unwind' => '$csc.stateinfo'),
			array('$unwind' => '$csc.stateinfo.cityinfo'),
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'company_id',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			//array('$unwind' => '$company'),
			array(
				'$lookup' => array(
					'from' => MDB_DRIVER_INFO,
					'localField' => '_id',
					'foreignField' => '_id',
					'as' => 'driver'
				)
			),
			array('$match' => $srch_query),
		);
		$field_arguments = array(				
				array(
					'$project' => array(
						'id' => '$_id',
						'user_createdby' => '$user_createdby',
						'name' => '$name',
						'username' => '$username',
						'email' => '$email',
						'company_name' => '$company.companydetails.company_name',
						'userid' => '$company.companydetails.userid',
						'address' => '$address',
						'availability_status' => '$availability_status',
						'status' => '$status',
						'driver_license_id' => '$driver_license_id',
						'shift_status' => '$driver.shift_status',
						'phone' => '$phone',
						'country_name' => '$csc.country_name',
						'state_name' => '$csc.stateinfo.state_name',
						'city_name' => '$csc.stateinfo.cityinfo.city_name',
						'cid' => '$company.companydetails.userid',
						'photo' => '$profile_picture',
						'driver_status' => '$status',
						'country' => array('$cond' => array('if' => array('$eq' => array('$login_country','$csc._id')),'then'=>1,'else'=>0)),
						'state' => array('$cond' => array('if' => array('$eq' => array('$login_state','$csc.stateinfo.state_id')),'then'=>1,'else'=>0)),
						'city' => array('$cond' => array('if' => array('$eq' => array('$login_city','$csc.stateinfo.cityinfo.city_id')),'then'=>1,'else'=>0)),	
					),					
				),
				array('$match' => array('country' => 1,'state' => 1,'city' => 1)),
				array(
					'$sort' => array( 
						'id' => -1
					)
				)				
			);
		
		if($find_count == false){
			
			$field_arguments[]['$skip'] = (int)$offset;
			$field_arguments[]['$limit'] = (int)$val;
		}
		$merge_arguments = array_merge($common_arguments, $field_arguments);
		$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $merge_arguments);
		//~ echo '<pre>';print_r($merge_arguments); exit;
		if(!empty($result['result'])){
			foreach($result['result'] as $key => $res)
			{
				$details[$key]['created_by'] = (isset($res['user_createdby']) && array_key_exists($res['user_createdby'],$peoples)) ? $peoples[$res['user_createdby']] : '';
				$details[$key]['name'] = isset($res['name']) ? $res['name']: '';
				$details[$key]['username'] = isset($res['username']) ? $res['username']: '';
				$details[$key]['email'] = isset($res['email']) ? $res['email']: '';
				$details[$key]['address'] = isset($res['address']) ? $res['address']: '';	
				$details[$key]['availability_status'] = isset($res['availability_status']) ? $res['availability_status']: '';			
				$details[$key]['company_name'] = !empty($res['company_name']) ? $res['company_name'][0]: '';
				$details[$key]['status'] = isset($res['driver_status']) ? $res['driver_status']: '';
				$details[$key]['id'] = isset($res['id']) ? $res['id']: '';
				$details[$key]['driver_license_id'] = isset($res['driver_license_id']) ? $res['driver_license_id']: '';
				$details[$key]['shift_status'] = !empty($res['shift_status']) ? $res['shift_status'][0]: '';
				$details[$key]['phone'] = isset($res['phone']) ? $res['phone']: '';
				$details[$key]['country_name'] = isset($res['country_name']) ? $res['country_name']: '';
				$details[$key]['city_name'] = isset($res['city_name']) ? $res['city_name']: '';
				$details[$key]['state_name'] = isset($res['state_name']) ? $res['state_name']: '';
				$details[$key]['cid'] = !empty($res['userid']) ? $res['userid'][0]: '';
				$details[$key]['photo'] = isset($res['profile_picture']) ? $res['profile_picture']: '';
				$details[$key]['driver_status'] = isset($res['driver_status']) ? $res['driver_status']: '';
			}
		}
		//echo '<pre>';print_r($details);exit;
		return $details;
    }
	
	/*public function get_all_driver_searchlist($keyword = "", $status = "", $company = "", $offset = "", $val = "", $find_count = FALSE)
    {
		$user_createdby                  = $this->userid;
		$usertype                        = $this->usertype;
		$company_id                      = $this->company_id;
		$country_id                      = $this->country_id;
		$state_id                        = $this->state_id;
		$city_id                         = $this->city_id;
		$keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
		$status = ($status!="")?$status:array('$ne' => 'T');
		$c_id = ($company!="")?$company:$company_id;
		if ($usertype == 'M') {
			$match_query = array('people.user_type'=>'D', 'people.login_country'=>(int) $country_id,'people.login_state' =>(int) $state_id,'people.login_city'=>(int) $city_id,'people.company_id' => (int)$c_id,'people.status' => $status);
		} else if ($usertype == 'C') {
			$match_query = array('people.user_type'=>'D', 'people.company_id' => (int)$c_id,'people.status' => $status );
		}elseif((int)$c_id!="" || (int)$c_id!=0) {
			$match_query = array('people.user_type'=>'D', 'people.company_id' => (int)$c_id,'people.status' => $status );
		}else{
			$match_query = array('people.user_type'=>'D', 'people.status' => $status );
		}
		if(!empty($keyword)) {
			$srch_query = array( "\$and" => array($match_query,array("\$or"=>array(array( 'people.name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'people.lastname' => Commonfunction::MongoRegex("/$keyword/i") ), array( 'people.email' => Commonfunction::MongoRegex("/$keyword/i") ), array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'people.phone' => Commonfunction::MongoRegex("/$keyword/i")) ) ) ) );
		}else{
			$srch_query = $match_query;
		}
		//echo "<pre>"; print_r($srch_query); exit;
		$common_arguments = array(
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
					'foreignField' => 'login_country',
					'foreignField' => 'login_city',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'people.company_id',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			array(
				'$unwind' => '$company'
			),
			array(
				'$lookup' => array(
					'from' => MDB_DRIVER_INFO,
					'localField' => 'people._id',
					'foreignField' => '_id',
					'as' => 'driver'
				)
			),
			array(
				'$unwind' => '$driver'
			),
			array(
				'$match' => $srch_query
			),
		);
		
		if ($find_count == TRUE) {
			$count_arguments = array(
				array(
					'$project' => array(
						'result' => '$people._id'
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
			$merge_arguments = array_merge($common_arguments, $count_arguments);
			$result          = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
		//echo "<pre>";print_r($result['result']);exit;
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
		} else {
			$field_arguments = array(
				array(
					'$sort' => array( 
						'people.created_date' => -1
					),
				),
				array(
					'$project' => array(
						'id' => '$people._id',
						'created_by' => '$people.user_createdby',
						'name' => '$people.name',
						'username' => '$people.username',
						'email' => '$people.email',
						'company_name' => '$company.companydetails.company_name',
						'address' => '$people.address',
						'availability_status' => '$people.availability_status',
						'status' => '$people.status',
						'driver_license_id' => '$people.driver_license_id',
						'shift_status' => '$driver.shift_status',
						'phone' => '$people.phone',
						'country_name' => '$country_name',
						'state_name' => '$stateinfo.state_name',
						'city_name' => '$stateinfo.cityinfo.city_name',
						'cid' => '$company.companydetails.userid',
						'photo' => '$people.profile_picture',
						'driver_status' => '$people.status',
					)
				),
				array('$skip'	=> (int)$offset ),
				array('$limit'	=> (int)$val )
			);
			$merge_arguments = array_merge($common_arguments, $field_arguments);
			$result    = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
			return (!empty($result['result'])) ? $result['result'] : array();
		}
    }*/
    
    public function get_all_undriver_searchlist($keyword = "", $status = "", $company = "", $offset = "", $val = "", $find_count=false)
    {
        $user_createdby                  = $this->userid;
		$usertype                        = $this->usertype;
		$company_id                      = $this->company_id;
		$country_id                      = $this->country_id;
		$state_id                        = $this->state_id;
		$city_id                         = $this->city_id;
		$keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
		$status = ($status!="")?$status:array('$ne' => 'T');
		$c_id = ($company!="")?$company:$company_id;
		if ($usertype == 'M') {
			$match_query = array('people.user_type'=>'D', 'people.login_country'=>(int) $country_id,'people.login_state' =>(int) $state_id,'people.login_city'=>(int) $city_id,'people.company_id' => (int)$c_id,'people.availability_status' => $status);
		} else if ($usertype == 'C') {
			$match_query = array('people.user_type'=>'D', 'people.company_id' => (int)$c_id,'people.availability_status' => $status );
		}elseif((int)$c_id!="" || (int)$c_id!=0) {
			$match_query = array('people.user_type'=>'D', 'people.company_id' => (int)$c_id,'people.availability_status' => $status );
		}else{
			$match_query = array('people.user_type'=>'D', 'people.availability_status' => $status );
		}
		if(!empty($keyword)) {
			$srch_query = array( "\$and" => array($match_query,array("\$or"=>array(array( 'people.name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'people.lastname' => Commonfunction::MongoRegex("/$keyword/i") ), array( 'people.email' => Commonfunction::MongoRegex("/$keyword/i") ), array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		}else{
			$srch_query = $match_query;
		}
		$common_arguments = array(
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
					'foreignField' => 'login_country',
					'foreignField' => 'login_city',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'people.company_id',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			//array('$unwind' => '$company'),
			array(
				'$lookup' => array(
					'from' => MDB_DRIVER_INFO,
					'localField' => 'people._id',
					'foreignField' => '_id',
					'as' => 'driver'
				)
			),
			//array('$unwind' => '$driver'),
			array(
				'$match' => $srch_query
			),
		);
		
		if ($find_count == TRUE) {
			$count_arguments = array(
				array(
					'$project' => array(
						'result' => '$people._id'
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
			$merge_arguments = array_merge($common_arguments, $count_arguments);
			$result          = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
		} else {
			$field_arguments = array(
				array(
					'$sort' => array( 
						'people.created_date' => -1
					),
				),
				array(
					'$project' => array(
						'id' => '$people._id',
						'created_by' => '$people.user_createdby',
						'name' => '$people.name',
						'username' => '$people.username',
						'email' => '$people.email',
						'company_name' => '$company.companydetails.company_name',
						'address' => '$people.address',
						'availability_status' => '$people.availability_status',
						'status' => '$people.status',
						'driver_license_id' => '$people.driver_license_id',
						'shift_status' => '$driver.shift_status',
						'phone' => '$people.phone',
						'country_name' => '$country_name',
						'state_name' => '$stateinfo.state_name',
						'city_name' => '$stateinfo.cityinfo.city_name',
						'cid' => '$company.companydetails.userid',
						'photo' => '$people.profile_picture',
						'driver_status' => '$people.status',
					)
				),
				array('$skip'	=> (int)$offset ),
				array('$limit'	=> (int)$val )
			);
			$merge_arguments = array_merge($common_arguments, $field_arguments);
			$result    = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
			return (!empty($result['result'])) ? $result['result'] : array();
		}
    }

    
    public function get_all_untaxi_searchlist($keyword = "", $status = "", $company = "", $offset = "", $val = "",$find_count=false)
    {
        $taxi_createdby = $this->user_createdby;
        $usertype       = $this->usertype;
        $company_id     = $this->company_id;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
		$keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
		if($usertype == 'M'){
			$company = $company_id;
        }
		//MongoDB with aggregate process only
		if((!empty($keyword)) && (!empty($status)) && (!empty($company))) {
			$srch_query = array( "\$and" => array( /*array('taxi.taxi_country' => (int)$country_id ),array('taxi.taxi_state' => (int)$state_id ),array('taxi.taxi_city' => (int)$city_id ),*/array('taxi.taxi_company' => (int)$company ),array('taxi.taxi_availability' => $status ),array("\$or"=>array(array( 'taxi.taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'taxi.taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		} else if ((!empty($keyword)) && (!empty($status))){
			if($usertype=='A'){
				$srch_query = array( "\$and" => array(array('taxi.taxi_availability' => $status ),array("\$or"=>array(array( 'taxi.taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'taxi.taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
			} else {
				$srch_query = array( "\$and" => array( /*array('taxi.taxi_country' => (int)$country_id ),array('taxi.taxi_state' => (int)$state_id ),array('taxi.taxi_city' => (int)$city_id ),*/array('taxi.taxi_company' => (int)$company ),array('taxi.taxi_availability' => $status ),array("\$or"=>array(array( 'taxi.taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'taxi.taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
			}
		} else if ((!empty($keyword)) && (!empty($company))){
			$srch_query = array( "\$and" => array( /*array('taxi.taxi_country' => (int)$country_id ),array('taxi.taxi_state' => (int)$state_id ),array('taxi.taxi_city' => (int)$city_id ),*/array('taxi.taxi_company' => (int)$company ),array("\$or"=>array(array( 'taxi.taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'taxi.taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		} else if ((!empty($status)) && (!empty($company))){
			$srch_query = array( "\$and" => array( /*array('taxi.taxi_country' => (int)$country_id ),array('taxi.taxi_state' => (int)$state_id ),array('taxi.taxi_city' => (int)$city_id ),*/array('taxi.taxi_company' => (int)$company ),array('taxi.taxi_availability' => $status ) ) );
		} else if (!empty($keyword)) {
			if($usertype=='A'){
				$srch_query = array( "\$and" => array(array("\$or"=>array(array( 'taxi.taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'taxi.taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
			} else {
				$srch_query = array( "\$and" => array( /*array('taxi.taxi_country' => (int)$country_id ),array('taxi.taxi_state' => (int)$state_id ),array('taxi.taxi_city' => (int)$city_id ),*/array('taxi.taxi_company' => (int)$company ),array("\$or"=>array(array( 'taxi.taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'taxi.taxi_type' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
			}
		} else if (!empty($company)) {
				$srch_query = array( "\$and" => array( /*array('taxi.taxi_country' => (int)$country_id ),array('taxi.taxi_state' => (int)$state_id ),array('taxi.taxi_city' => (int)$city_id ),*/array('taxi.taxi_company' => (int)$company )));
		} else if (!empty($status)) {
			if($usertype=='A'){
				$srch_query = array( "\$and" => array(array('taxi.taxi_availability' => $status )));
			} else {
				$srch_query = array( "\$and" => array( /*array('taxi.taxi_country' => (int)$country_id ),array('taxi.taxi_state' => (int)$state_id ),array('taxi.taxi_city' => (int)$city_id ),*/array('taxi.taxi_company' => (int)$company ),array('taxi.taxi_availability' => $status )));
			}
		}
		//echo '<pre>';print_r($srch_query);//exit;
		if($find_count){
			$arguments = array(
					array('$unwind' => '$stateinfo'),
					array('$unwind' => '$stateinfo.cityinfo'),
					array('$lookup' => array(
							'from' => MDB_TAXI,
							'localField'=> 'stateinfo.cityinfo.city_id',
							'foreignField'=> "taxi_country",
							'foreignField'=> "taxi_state",
                            'foreignField'=> "taxi_city",
							'as'=> "taxi"
						)
					),
					array('$unwind' => '$taxi'),
					array('$lookup' => array(
							'from' => MDB_COMPANY,
							'localField' => 'taxi.taxi_company',
							'foreignField' => "_id",
							'as' => "company"
						)
					),
					//array('$unwind' => '$company'),
					array('$lookup' => array(
							'from' => MDB_MOTOR_MODEL,
							'localField' => 'taxi.taxi_model',
							'foreignField' => "_id",
							'as' => "motormodel"
						)
					),
					//array('$unwind' => '$motormodel'),
					array('$match'  => $srch_query),
					array('$project' => array('_id'=>0,
							'taxi_id' => '$taxi._id',
						)
					),
				array('$sort' =>array('taxi.created_date' => -1) ),
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result']))?count($result['result']):0;
		} else {
			$arguments = array(
					array('$unwind' => '$stateinfo'),
					array('$unwind' => '$stateinfo.cityinfo'),
					array('$lookup' => array(
							'from' => MDB_TAXI,
							'localField'=> 'stateinfo.cityinfo.city_id',
							'foreignField'=> "taxi_country",
							'foreignField'=> "taxi_state",
                            'foreignField'=> "taxi_city",
							'as'=> "taxi"
						)
					),
					array('$unwind' => '$taxi'),
					array('$lookup' => array(
							'from' => MDB_COMPANY,
							'localField' => 'taxi.taxi_company',
							'foreignField' => "_id",
							'as' => "company"
						)
					),
					//array('$unwind' => '$company'),
					array('$lookup' => array(
							'from' => MDB_MOTOR_MODEL,
							'localField' => 'taxi.taxi_model',
							'foreignField' => "_id",
							'as' => "motormodel"
						)
					),
					//array('$unwind' => '$motormodel'),
					array('$match'  => $srch_query),
					array('$project' => array('_id'=>0,
							'created_by' => '$taxi.taxi_createdby',
							'taxi_id' => '$taxi._id',
							'taxi_availability' => '$taxi.taxi_availability',
							'taxi_status' => '$taxi.taxi_status',
							'company_name' => '$company.companydetails.company_name',
							'model_name' => '$motormodel.model_name',
							'taxi_capacity' => '$taxi.taxi_capacity',
							'taxi_no' => '$taxi.taxi_no',
							'taxi_fare_km' => '$taxi.taxi_fare_km',
							'company_id' => '$taxi.taxi_company',
							'taxi_owner_name' => '$taxi.taxi_owner_name',
							'company_userid' => '$company.companydetails.userid',
						)
					),
				array('$sort' =>array('taxi.created_date' => -1) ),
				array('$skip' => (int)$offset ),
				array('$limit' => (int)$val )
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result']))?$result['result']:array();
		}
    }
    public function all_manager_list($offset, $val,$find_count=FALSE)
    {
		$result = $temp_arr = array();
        $user_createdby = $this->user_createdby;
        $usertype       = $this->usertype;
        $company_id     = $this->company_id;
		$match_query = array( 'user_type' => 'M');
		if ($usertype == 'M') {
			$match_query['company_id'] = (int)$company_id;
			$match_query['user_createdby'] = (int)$this->user_createdby;
		} else if ($usertype == 'C') {
			$match_query['company_id'] = (int)$company_id;
		} 
		$common_arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_CSC,
					'localField' => 'login_country',
					'foreignField' => '_id',
					'as' => 'csc'
				)
			),
			array('$unwind' => '$csc'),
			array('$unwind' => '$csc.stateinfo'),
			array('$unwind' => '$csc.stateinfo.cityinfo'),
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'company_id',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			//array('$unwind' => '$company'),
			array(
				'$match' => $match_query
			),
		);
		
		if ($find_count == TRUE) {
			$count_arguments = array(
				array(
					'$project' => array(
						'result' => '$_id',
						'country' => array('$cond' => array('if' => array('$eq' => array('$login_country','$csc._id')),'then'=>1,'else'=>0)),
						'state' => array('$cond' => array('if' => array('$eq' => array('$login_state','$csc.stateinfo.state_id')),'then'=>1,'else'=>0)),
						'city' => array('$cond' => array('if' => array('$eq' => array('$login_city','$csc.stateinfo.cityinfo.city_id')),'then'=>1,'else'=>0))
					)
				),
				array('$match' => array('country' => 1,'state' => 1,'city' => 1)),
				array(
					'$group' => array(
						'_id' => NULL,
						'count' => array(
							'$sum' => 1
						)
					)
				)
			);
			$merge_arguments = array_merge($common_arguments, $count_arguments);
			$result          = $this->mongo_db->aggregate(MDB_PEOPLE, $merge_arguments);
			//echo "<pre>";print_r($result);exit;
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
		} else {
			$field_arguments = array(
				
				array(
					'$project' => array(
						'pid' => '$_id',
						'created_by' => '$user_createdby',
						'name' => '$name',
						'username' => '$username',
						'email' => '$email',
						'company_name' => '$company.companydetails.company_name',
						'address' => '$address',
						'availability_status' => '$availability_status',
						'status' => '$status',
						'driver_license_id' => '$driver_license_id',
						'shift_status' => '$driver.shift_status',
						'phone' => '$phone',
						'country_name' => '$csc.country_name',
						'state_name' => '$csc.stateinfo.state_name',
						'city_name' => '$csc.stateinfo.cityinfo.city_name',
						'cid' => '$company.companydetails.userid',
						'photo' => '$profile_picture',
						'driver_status' => '$status',
						'country' => array('$cond' => array('if' => array('$eq' => array('$login_country','$csc._id')),'then'=>1,'else'=>0)),
						'state' => array('$cond' => array('if' => array('$eq' => array('$login_state','$csc.stateinfo.state_id')),'then'=>1,'else'=>0)),
						'city' => array('$cond' => array('if' => array('$eq' => array('$login_city','$csc.stateinfo.cityinfo.city_id')),'then'=>1,'else'=>0)),	
					),					
				),
				
				array(
					'$group' => array(
						'_id' => array('_id' => '$pid'),
						'details' => array('$first' => array(
								'pid' => '$pid',
								'created_by' => '$created_by',
								'name' => '$name',
								'username' => '$username',
								'email' => '$email',
								'company_name' => '$company_name',
								'address' => '$address',
								'availability_status' => '$availability_status',
								'status' => '$status',
								'driver_license_id' => '$driver_license_id',
								'shift_status' => '$shift_status',
								'phone' => '$phone',
								'country_name' => '$country_name',
								'state_name' => '$state_name',
								'city_name' => '$city_name',
								'cid' => '$cid',
								'photo' => '$photo',
								'driver_status' => '$driver_status',
								'country' => '$country',
								'state' => '$state',
								'city' => '$city',
							)
						),
						
					),					
				),
				array(
					'$sort' => array( 
						'details.pid' => -1
					),
				),
				array('$skip'	=> (int)$offset ),
				array('$limit'	=> (int)$val )
			);
			$merge_arguments = array_merge($common_arguments, $field_arguments);
			$res = $this->mongo_db->aggregate(MDB_PEOPLE, $merge_arguments);
			if(!empty($res['result'])){
				foreach($res['result'] as $r){
					$r = $r['details'];
					$temp_arr = $r;
					//echo '<pre>';print_r($temp_arr);exit;
					$temp_arr['id'] = $r['pid'];
					$temp_arr['userid'] = !empty($r['cid']) ? $r['cid'][0] :'';
					$temp_arr['company_name'] = !empty($r['company_name']) ? $r['company_name'][0] :'';
					$result[] = $temp_arr;
				}
			}
			//echo '<pre>';print_r($result);exit;
			return $result;
		}
    }
    
	public function count_admin_list(){
	   $offset = $val = "";
	   $this->all_admin_list($offset,$val,TRUE);
	}
    public function all_admin_list($offset, $val,$find_count=false)
    {
		if($offset=="" &&  $val=="")
		{
			$find_count=true;
		}
		//MongoDB
		if($find_count){
			$result = $this->mongo_db->count(MDB_PEOPLE,array('user_type'=>'S','status'=>array('$ne'=>'T')),array('_id'));
			return $result;
		} else {
			$ops = array(
				array(
					'$lookup' => array(
					'from'=>MDB_CSC,
					'localField'=> "login_country",
					'foreignField' => "_id",
					'as'=> "countrydetails"
					)
				),
				array('$unwind' => array('path' =>'$countrydetails', 'preserveNullAndEmptyArrays'=>true)),
				array(
					'$project' => array(
					'country_name' => '$countrydetails.country_name',
					'name' => '$name',
					'lastname' => '$lastname',
					'email' => '$email',
					'address' => '$address',
					'status' => '$status',
					'login_country' => '$login_country',
					'user_type' => '$user_type',
					)
				),
				array('$match'=>array('user_type'=>'S','status'=>array('$ne'=>'T'))),
			);
			$results = $this->mongo_db->aggregate(MDB_PEOPLE,$ops);
			//echo "<pre>"; print_r($results); exit;
			$data = array();
			if(count($results['result']) > 0){
				foreach($results['result'] as $val){
					$arr = $val;
					$arr['id'] = $val['_id'];
					$data[] = $arr;
				}
			}
			return $data; 
 
		}
    }
	
    public function active_manager_request($activeids)
    {	
        $ids = Commonfunction::mongo_format_array($activeids);
        $set_array = array('status' => 'A');
        $result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$ids),'user_type'=>'M'),array('$set'=>$set_array));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    
    public function block_manager_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
        //==================================================================
		//MongoDB
		$ids = Commonfunction::mongo_format_array($activeids);
	
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$ids),'user_type'=>'M'),array('$set'=>array('status' => 'D')));
		
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
	public function trash_manager_request($activeids)
    {
		//MongoDB
		$ids = Commonfunction::mongo_format_array($activeids);
		$set_array = array('status' => 'T');
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$ids),'user_type'=>'M'),array('$set'=>$set_array));
		//print_r($result); exit;
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
	public function active_admin_request($activeids)
    {
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		//var_dump($active_ids);
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids),'user_type'=>'S'),array('$set'=>array('status' => 'A')));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function block_admin_request($activeids)
    {
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		//var_dump($active_ids);
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids),'user_type'=>'S'),array('$set'=>array('status' => 'D')));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
	public function trash_admin_request($activeids)
    {
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($activeids);
		//var_dump($active_ids);
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids),'user_type'=>'S'),array('$set'=>array('status' => 'T')));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function count_assigntaxi_list(){	
        $count = $this->all_assigntaxi_list('','','',TRUE);
        return $count;
    }
	
    public function all_assigntaxi_list( $offset, $val,$peoples,$find_count=false)
    {
		$user_createdby = $this->user_createdby;
        $usertype       = $this->usertype;
        $company_id     = $this->company_id;
		$details = $match_query = array();
		$match_query['mapping.mapping_status'] = array('$ne' => 'T');
		if (($usertype == 'M' || $usertype == 'C')  && $company_id!=0) {
			$match_query['company._id'] = (int)$company_id;
        }
		
		//echo "<pre>"; print_r($match_query); exit;
		$common_arguments = array(
			array(
				'$unwind' => '$stateinfo'
			),
			array(
				'$unwind' => '$stateinfo.cityinfo'
			),
			array(
				'$lookup' => array(
					'from' => MDB_TAXI_DRIVER_MAPPING,
					'localField' => '_id',
					'foreignField' => 'mapping_countryid',
					'localField' => 'stateinfo.state_id',
					'foreignField' => 'mapping_stateid',
					'localField' => 'stateinfo.cityinfo.city_id',
					'foreignField' => 'mapping_cityid',
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
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'mapping.mapping_companyid',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			array(
				'$unwind' => '$company'
			),
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'mapping.mapping_driverid',
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
		);
		
		$field_arguments = array(
			array(
				'$sort' => array( 
					'mapping._id' => -1
				),
			),
			array(
				'$project' => array(
					'mapping_id' => '$mapping._id',
					'id' => '$people._id',
					'name' => '$people.name',
					'taxi_id' => '$taxi._id',
					'taxi_no' => '$taxi.taxi_no',
					'cid' => '$company.companydetails.userid',
					'company_name' => '$company.companydetails.company_name',
					'country_name'=>'$country_name',
					'state_name'=>'$stateinfo.state_name',
					'city_name'=>'$stateinfo.cityinfo.city_name',
					'mapping_status' => '$mapping.mapping_status',
					'user_createdby' => '$mapping.mapping_createdby',
					'mapping_startdate' => '$mapping.mapping_startdate',
					'mapping_enddate' => '$mapping.mapping_enddate',
					'mapping_companyid' => '$mapping.mapping_companyid',
				)
			),
		);
		if($find_count == false){
			$field_arguments[]['$skip'] = (int)$offset;
			$field_arguments[]['$limit'] = (int)$val;
		}
		$merge_arguments = array_merge($common_arguments, $field_arguments);
		$result   = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
		
		if(!empty($result['result'])){
			foreach($result['result'] as $key => $res){
				if($find_count == false){
					$details[$key]['created_by'] = (array_key_exists($res['user_createdby'],$peoples)) ? $peoples[$res['user_createdby']] : '';
					$details[$key]['mapping_id'] = $res['mapping_id'];
					$details[$key]['mapping_status'] = $res['mapping_status'];					
					$details[$key]['company_name'] = $res['company_name'];	
					$details[$key]['taxi_no'] = $res['taxi_no'];	
					$details[$key]['country_name'] = $res['country_name'];
					$details[$key]['state_name'] = $res['state_name'];	
					$details[$key]['city_name'] = $res['city_name'];
					$details[$key]['mapping_startdate'] = isset($res['mapping_startdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['mapping_startdate']) : '';
					$details[$key]['mapping_enddate'] = isset($res['mapping_enddate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['mapping_enddate']) : '';
					$details[$key]['id'] = $res['id'];
					$details[$key]['cid'] = $res['cid'];
					$details[$key]['taxi_id'] = $res['taxi_id'];
					$details[$key]['mapping_companyid'] = $res['mapping_companyid'];
				}		
				$details[$key]['name'] = $res['name'];		
			}
		}
		return $details;
    }
    
    public function count_assigntaxisearch_list($keyword = "", $status = "", $company = ""){
		
		$count = $this->get_all_assigntaxi_searchlist($keyword, $status, $company,'','','',TRUE);
		return $count;
	}
	
	public function get_all_assigntaxi_searchlist( $keyword = "", $status = "", $company = "", $offset = "", $val = "",$peoples, $find_count = FALSE)
    {
		$user_createdby = $this->user_createdby;
        $usertype       = $this->usertype;
        $company_id     = $this->company_id;
		$keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
		$details = $match_query = array();
		$match_query['_id'] = array('$gt' => 0);
		if (($usertype == 'M' || $usertype == 'C')  && $company_id!=0) {
			$match_query['mapping.mapping_companyid'] = (int)$company_id;
        }
		if ($company!="" && $company!=0) {
			$match_query['company._id'] = (int)$company;
        }
		if ($status!="") {
			$match_query['mapping.mapping_status'] = $status;
        }
		$matchquery = $match_query;
		if ($keyword!="") {
			$srch_query = array("\$or"=>array(
							array('people.name'=>Commonfunction::MongoRegex("/$keyword/i")),
							array('people.lastname'=>Commonfunction::MongoRegex("/$keyword/i")),
							array('people.email'=>Commonfunction::MongoRegex("/$keyword/i")),
							array('people.username'=>Commonfunction::MongoRegex("/$keyword/i"))));
			/*$and = array();
			if(!empty($match_query)){
				foreach($match_query as $key => $val){
					$and[] = array($key => $val);
				}
				$and[] = $srch_query;
				$matchquery = array('$and' => $and);
			}else{
				$matchquery = array_merge($match_query,$srch_query);
			}*/
			$matchquery = array_merge($match_query,$srch_query);
		}
		//echo $company."<pre>"; print_r($matchquery); exit;
		$common_arguments = array(
			array(
				'$unwind' => '$stateinfo'
			),
			array(
				'$unwind' => '$stateinfo.cityinfo'
			),
			array(
				'$lookup' => array(
					'from' => MDB_TAXI_DRIVER_MAPPING,
					'localField' => 'stateinfo.cityinfo.city_id',
					'foreignField' => 'mapping_countryid',
					'foreignField' => 'mapping_cityid',
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
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'mapping.mapping_companyid',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			array(
				'$unwind' => '$company'
			),
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'mapping.mapping_driverid',
					'foreignField' => '_id',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
			array(
				'$match' => $matchquery
			),
		);
		$field_arguments = array(
			array(
				'$sort' => array( 
					'mapping._id' => -1
				),
			),
			array(
				'$project' => array(
					'mapping_id' => '$mapping._id',
					'id' => '$people._id',
					'name' => '$people.name',
					'taxi_id' => '$taxi._id',
					'taxi_no' => '$taxi.taxi_no',
					'cid' => '$company.companydetails.userid',
					'company_name' => '$company.companydetails.company_name',
					'country_name'=>'$country_name',
					'state_name'=>'$stateinfo.state_name',
					'city_name'=>'$stateinfo.cityinfo.city_name',
					'mapping_status' => '$mapping.mapping_status',
					'user_createdby' => '$mapping.mapping_createdby',
					'mapping_startdate' => '$mapping.mapping_startdate',
					'mapping_enddate' => '$mapping.mapping_enddate',
					'mapping_companyid' => '$mapping.mapping_companyid',
				)
			),
		);
		if($find_count == false){
			$field_arguments[]['$skip'] = (int)$offset;
			$field_arguments[]['$limit'] = (int)$val;
		}
		$merge_arguments = array_merge($common_arguments, $field_arguments);
		$result   = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);	
		//echo '<pre>';print_r($merge_arguments);exit;	
		if(!empty($result['result'])){
			foreach($result['result'] as $key => $res){
				if($find_count == false){
					$details[$key]['created_by'] = (array_key_exists($res['user_createdby'],$peoples)) ? $peoples[$res['user_createdby']] : '';
				}	
				$details[$key]['mapping_id'] = $res['mapping_id'];
				$details[$key]['mapping_status'] = $res['mapping_status'];					
				$details[$key]['company_name'] = $res['company_name'];	
				$details[$key]['taxi_no'] = $res['taxi_no'];	
				$details[$key]['country_name'] = $res['country_name'];
				$details[$key]['state_name'] = $res['state_name'];	
				$details[$key]['city_name'] = $res['city_name'];
				$details[$key]['mapping_startdate'] = isset($res['mapping_startdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['mapping_startdate']) : '';
				$details[$key]['mapping_enddate'] = isset($res['mapping_enddate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['mapping_enddate']) : '';
				$details[$key]['id'] = $res['id'];
				$details[$key]['cid'] = $res['cid'];
				$details[$key]['taxi_id'] = $res['taxi_id'];
				$details[$key]['mapping_companyid'] = $res['mapping_companyid'];	
				$details[$key]['name'] = $res['name'];		
			}
		}
		return $details;
    }
	
    public function active_assigntaxi_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
        //==================================================================
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_TAXI_DRIVER_MAPPING,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('mapping_status' => 'A')));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function block_assigntaxi_request($activeids)
    {
        $active_ids = Commonfunction::mongo_format_array($activeids);
        $result = $this->mongo_db->updateMany(MDB_TAXI_DRIVER_MAPPING,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('mapping_status' => 'D')));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function trash_assigntaxi_request($activeids)
    {
        //check whether id is exist in checkbox or single active request
        //==================================================================	
		$active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_TAXI_DRIVER_MAPPING,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('mapping_status' => 'T')));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    
    public function countall_rating_drivers()
    {
        $count = $this->all_rating_drivers('','',true);
        return $count;
    }
    
	public function all_rating_drivers($offset, $val,$find_count=false)
    {
        $driver_createdby = $this->user_createdby;
        $usertype         = $this->usertype;
        $company_id       = $this->company_id;
        $country_id       = $this->country_id;
        $state_id         = $this->state_id;
        $city_id          = $this->city_id;
        
		$lookup_arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'driver_id',
					'foreignField' => '_id',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
		);
		if($usertype == 'C'){
			$srch_query = array('people.company_id' => (int)$company_id,'travel_status'=>1,'rating'=>array('$gt'=>0),'people.user_type'=>'D');
            $common_arguments = array(
                array(
                    '$match' => $srch_query
                ),
            );
        } else if($usertype=='M'){
			$srch_query = array('people.company_id' => (int)$company_id,'travel_status'=>1,'rating'=>array('$gt'=>0),'people.user_type'=>'D','people.login_country'=>(int)$country_id,'people.login_state'=>(int)$state_id,'people.login_city'=>(int)$city_id);
			$lookup_argument = array(
				array(
					'$lookup' => array(
						'from' => MDB_CSC,
						'localField'=> 'stateinfo.cityinfo.city_id',
						'foreignField'=> "people.login_city",
						'as' => 'csc'
					)
				),
				array(
					'$unwind' => '$csc'
				),
			);
			$common_arguments = array(
				array(
                    '$match' => $srch_query
                ),
			);
			$lookup_arguments = array_merge($lookup_arguments,$lookup_argument);
		} else {
			$srch_query = array('travel_status'=>1,'rating'=>array('$gt'=>0),'people.user_type'=>'D');
			$common_arguments = array(
                array(
                    '$match' => $srch_query
                ),
            );
		}
		if($find_count){
			$common_argument = array_merge($lookup_arguments,$common_arguments);
			$count_arguments = array(
				array(
					'$group' => array(
						'_id' => array('driver_id'=>'$driver_id'),
						'count' => array(
							'$sum' => 1
						)
					)
				)
			);
			$merge_arguments = array_merge($common_argument, $count_arguments);
			$result          = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);
			//echo "<pre>if";print_r(count($result['result']));//exit;
			return (!empty($result['result']) && isset($result['result'])) ? count($result['result']) : 0;
		} else {
			$common_argument = array_merge($lookup_arguments,$common_arguments);
			$field_arguments = array(
				array(
					'$sort' => array( 
						'createdate' => -1
					),
				),
				array('$group' => array("_id" => array('driver_id'=>'$driver_id','name'=>'$people.name'),
						"total_ratings" => array( '$sum' => '$rating' ),
						"count" => array( '$sum' => 1 ),
					)
				),
				array( '$project' =>array('_id' => 0,
						'driver_id' => '$_id.driver_id',
						'name' => '$_id.name',
						'total_posts' => '$total_ratings',
						'co_nt' => '$count',
					)
				),
				array('$skip'	=> (int)$offset ),
				array('$limit'	=> (int)$val )
			);
			$merge_arguments = array_merge($common_argument, $field_arguments);
			$result    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);
			//echo "<pre>else";print_r($merge_arguments); exit;
			return (!empty($result['result'])) ? $result['result'] : array();
		}
    }
	public function count_rating_drivers_list($keyword = "", $comp_id = "")
    {
        $count = $this->get_all_ratingdrivers_searchlist($keyword, $comp_id,'','',true);
        //echo '<pre>';print_r($count);exit;
        return $count;
    }
    
    public function get_all_ratingdrivers_searchlist($keyword = "", $comp_id = "",$offset = "", $val = "", $find_count=false)
    {
		$result = array();
        $keyword          = str_replace("%", "!%", $keyword);
        $keyword          = str_replace("_", "!_", $keyword);
        $usertype         = $this->usertype;
        $driver_createdby = $this->user_createdby;
        $company_id       = $this->company_id;
        $country_id       = $this->country_id;
        $state_id         = $this->state_id;
        $city_id          = $this->city_id;
        $srch_query = array('_id' => array('$gt' =>0),'rating'=>array('$gt'=>0));
		$lookup_arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'driver_id',
					'foreignField' => '_id',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
		);
		if($usertype == 'C'){
			$srch_query = array('people.company_id' => (int)$company_id,'travel_status'=>1,'people.user_type'=>'D');
			if (!empty($keyword)) {				
				$srch_query = array("people.name" => Commonfunction::MongoRegex("/$keyword/i"),'people.company_id' => (int)$company_id,'travel_status'=>1,'rating'=>array('$ne'=>0),'people.user_type'=>'D');
			}
            $common_arguments = array(
                array(
                    '$match' => $srch_query
                ),
            );
        } else if($usertype=='M'){
			$srch_query = array('people.company_id' => (int)$company_id,'travel_status'=>1,'rating'=>array('$gt'=>0),'people.user_type'=>'D','people.login_country'=>(int)$country_id,'people.login_state'=>(int)$state_id,'people.login_city'=>(int)$city_id);
			if (!empty($keyword)) {
				$srch_query = array("people.name" => Commonfunction::MongoRegex("/$keyword/i"),'people.company_id' => (int)$company_id,'travel_status'=>1,'rating'=>array('$ne'=>0),'people.user_type'=>'D','people.login_country'=>(int)$country_id,'people.login_state'=>(int)$state_id,'people.login_city'=>(int)$city_id);
			}
			$lookup_argument = array(
				array(
					'$lookup' => array(
						'from' => MDB_CSC,
						'localField'=> 'stateinfo.cityinfo.city_id',
						'foreignField'=> "people.login_city",
						'as' => 'csc'
					)
				),
				array(
					'$unwind' => '$csc'
				),
			);
			$common_arguments = array(
				array(
                    '$match' => $srch_query
                ),
			);
			$lookup_arguments = array_merge($lookup_arguments,$lookup_argument);
		} else {
			if (!empty($keyword) && !empty($comp_id)) {
				$srch_query = array("people.name" => Commonfunction::MongoRegex("/$keyword/i"),'people.company_id' => (int)$comp_id,'travel_status'=>1,'rating'=>array('$gt'=>0),'people.user_type'=>'D');
			} else if (!empty($keyword)) {
				$srch_query = array("people.name" => Commonfunction::MongoRegex("/$keyword/i"),'travel_status'=>1,'rating'=>array('$gt'=>0),'people.user_type'=>'D');
			} else if (!empty($comp_id)) {
				$srch_query = array('people.company_id' => (int)$comp_id,'travel_status'=>1,'rating'=>array('$gt'=>0),'people.user_type'=>'D');
			}
			$common_arguments = array(
                array(
                    '$match' => $srch_query
                ),
            );
		}
		$common_argument = array_merge($lookup_arguments,$common_arguments);
		$field_arguments = array(
			array(
				'$sort' => array( 
					'createdate' => -1
				),
			),
			array('$group' => array("_id" => array('driver_id'=>'$driver_id','name'=>'$people.name'),
					"total_ratings" => array( '$sum' => '$rating' ),
					"count" => array( '$sum' => 1 ),
				)
			),
			array( '$project' =>array('_id' => 0,
					'driver_id' => '$_id.driver_id',
					'name' => '$_id.name',
					'total_posts' => '$total_ratings',
					'co_nt' => '$count',
				)
			),
		);
		if($find_count == false){
			
			$field_arguments[]['$skip'] = (int)$offset;
			$field_arguments[]['$limit'] = (int)$val;
		}
		$merge_arguments = array_merge($common_argument, $field_arguments);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);
		//echo '<pre>';print_r($merge_arguments);exit;
		if(!empty($res['result'])){
			$result = $res['result'];
		}
		return ($find_count == true) ? count($result) : $result;
    }
    public function userNamebyId($id)
    {
        $result = $this->mongo_db->findOne(MDB_PEOPLE,array('_id' => $id),array('name'));
        if (count($result) > 0) {
            return $result['name'];
        } else {
            return '';
        }
    }
    
    public function userCompanyDetails($id)
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_PEOPLE,array('company_id'=>(int)$id,'user_type'=>'C'),array('name','email'));
        return (!empty($result))?$result:array();
    }
  
    public function trash_driver_request($activeids)
    {
        $active_ids = Commonfunction::mongo_format_array($activeids);
        $result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('status' => 'T')));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function get_all_contacts_searchlist_count($keyword = "", $cid = '')
    {
        $keyword    = str_replace("%", "!%", $keyword);
        $keyword    = str_replace("_", "!_", $keyword);
        //MongoDB
        $srch_query=array();
        if($keyword){
                $srch_query = array( "\$or" => array(array( "subject" => Commonfunction::MongoRegex("/$keyword/i")) , array("name" => Commonfunction::MongoRegex("/$keyword/i") ) ) );
        }
        if(!empty($cid)){
                $srch_query = array(array("\$and"=>array("contact_cid"=>$cid)),"\$or"=>array(array('name'=>Commonfunction::MongoRegex("/$keyword/i")),array('subject'=>Commonfunction::MongoRegex("/$keyword/i"))));
        }
        //print_r($srch_query);exit;
        $res = $this->mongo_db->count(MDB_CONTACTS,$srch_query);
        return $res;
    }
    public function get_all_contacts_searchlist($keyword = "",$cid='')
    {
        $keyword    = str_replace("%", "!%", $keyword);
        $keyword    = str_replace("_", "!_", $keyword);
        $srch_query=array();
        //MongoDB
        if($keyword){
                $srch_query = array( "\$or" => array(array( "subject" => Commonfunction::MongoRegex("/$keyword/i")) , array("first_name" => Commonfunction::MongoRegex("/$keyword/i") ) ) );
        }
        if(!empty($cid)){
                $srch_query = array(array("\$and"=>array("contact_cid"=>$cid)),"\$or"=>array(array('first_name'=>Commonfunction::MongoRegex("/$keyword/i")),array('subject'=>Commonfunction::MongoRegex("/$keyword/i"))));
        }
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'sort'=>[
                'sent_date'=>-1
            ],
            'skip'=>0,
            'limit'=>10
        ];
        $res = $this->mongo_db->find(MDB_CONTACTS,$srch_query,$options);	
        return $res;
    }
    public function delete_contacts($id)
    {
        //MongoDB
        $result = $this->mongo_db->deleteOne(MDB_CONTACTS,array('_id'=>(int)$id));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    
    public function count_contacts_list($cid = '')
    {
        if ($cid == '') {		
            //MongoDB
            $rs = $this->mongo_db->count(MDB_CONTACTS);
        } else {
            //MongoDB
            $rs = $this->mongo_db->count(MDB_CONTACTS,array('contact_cid'=>$cid));
        }
        return $rs;
    }
    public function all_contacts_list($offset, $val, $cid = '')
    {
        if ($cid == '') {
                         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[                            
                            'sort'=>[
                                'sent_date'=>-1
                            ],
                            'skip'=>(int)$offset,
                            'limit'=>(int)$val
                        ];
                        $res = $this->mongo_db->find(MDB_CONTACTS,[],$options);
			
        } else {
             ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[                            
                            'sort'=>[
                                'sent_date'=>-1
                            ],
                            'skip'=>(int)$offset,
                            'limit'=>(int)$val
                        ];
                        $res = $this->mongo_db->find(MDB_CONTACTS,array('contact_cid'=>$cid),$options);
        }
		
		$rs = $res;
        return $rs;
    }
    public function count_content_list()
    {
        $result = $this->mongo_db->count(MDB_CMS,array('content_status'=>(int)1));
        return $result;
    }
    public function all_content_list($offset = '', $val = '')
    {
        $ops = array(array('$match'=>array('content_status'=>array('$eq'=>(int)1))),
						array('$project'=>array(
							'id'=>'$_id',
							'menu_name'=>'$menu_name',
							'status_post'=>'$status_post',
						)),
						array('$sort'=>array('_id'=>-1)),
						array('$skip'=>(int)$offset),
						array('$limit'=>(int)$val),												
					);
		$result = $this->mongo_db->aggregate(MDB_CMS,$ops);
		return (!empty($result['result']))?$result['result']:array();
        
    }
    public function contacts_list_view($id)
    {
		$result = array();
		$project = array('_id','first_name','email','subject','message','phone','sent_date','cid','contact_cid','country','last_name','no_of_employees','budget','product','revenue','industry','attachment_file');
		$res = $this->mongo_db->findOne(MDB_CONTACTS,array('_id'=>(int)$id),$project);		
		if(!empty($res)){
			
			$temp_arr['id'] = isset($res['_id'])?$res['_id']:'';
			$temp_arr['first_name'] = isset($res['first_name'])?$res['first_name']:'';
			$temp_arr['email'] = isset($res['email'])?$res['email']:'';
			$temp_arr['subject'] = isset($res['subject'])?$res['subject']:'';
			$temp_arr['message'] = isset($res['message'])?$res['message']:'';
			$temp_arr['phone'] = isset($res['phone'])?$res['phone']:'';
			$temp_arr['cid'] = isset($res['cid'])?$res['cid']:'';
			$temp_arr['contact_cid'] = isset($res['contact_cid'])?$res['contact_cid']:'';
			$temp_arr['country'] = isset($res['country'])?$res['country']:'';
			$temp_arr['last_name'] = isset($res['last_name'])?$res['last_name']:'';
			$temp_arr['no_of_employees'] = isset($res['no_of_employees'])?$res['no_of_employees']:'';
			$temp_arr['budget'] = isset($res['budget'])?$res['budget']:'';
			$temp_arr['product'] = isset($res['product'])?$res['product']:'';
			$temp_arr['revenue'] = isset($res['revenue'])?$res['revenue']:'';
			$temp_arr['industry'] = isset($res['industry'])?$res['industry']:'';
			$temp_arr['attachment_file'] = isset($res['attachment_file'])?$res['attachment_file']:'';
			$temp_arr['sent_date'] = isset($res['sent_date'])?$res['sent_date']:'';
			
			$result[] = $temp_arr;
		}
		return $result;
    }
    public function taxicount($cid)
    {
        //MongoDB
        $result = $this->mongo_db->count(MDB_TAXI,array('taxi_company'=>(int)$cid));
        return (!empty($result))?$result:0;
    }
    public function drivercount($cid)
    {
        //MongoDB
        $result = $this->mongo_db->count(MDB_PEOPLE,array('company_id'=>(int)$cid,'user_type'=>'D'));
        return (!empty($result))?$result:0;
    }
    public function managercount($cid)
    {
        //MongoDB
        $result = $this->mongo_db->count(MDB_PEOPLE,array('company_id'=>(int)$cid,'user_type'=>'M'));
        return (!empty($result))?$result:0;
    }
    public function details_taxiinfo($id)
    {
		$result = array();
		$match_query = array('taxi._id'=>(int)$id);
		$arguments = array(
			array('$unwind' => '$stateinfo'),
			array('$unwind' => '$stateinfo.cityinfo'),
			array('$lookup' => array(
					'from' => MDB_TAXI,
					'localField'=> 'stateinfo.cityinfo.city_id',
					'foreignField'=> "taxi_country",
					'foreignField'=> "taxi_state",
					'foreignField'=> "taxi_city",
					'as'=> "taxi"
				)
			),
			array('$unwind' => '$taxi'),
			array('$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'taxi.taxi_company',
					'foreignField' => "_id",
					'as' => "company"
				)
			),
			array('$unwind' => '$company'),
			array('$lookup' => array(
					'from' => MDB_MOTOR_MODEL,
					'localField' => 'taxi.taxi_model',
					'foreignField' => "_id",
					'as' => "motormodel"
				)
			),
			array('$unwind' => '$motormodel'),
			array('$lookup' => array(
					'from' => MDB_MOTOR_COMPANY,
					'localField' => 'motormodel.motor_id',
					'foreignField' => "_id",
					'as' => "motor"
				)
			),
			array('$unwind' => '$motor'),
			array('$match'  => $match_query),
			array('$project' => array('_id'=>0,
					'taxi_id' => '$taxi._id',
					'motor_name' => '$motor.motor_name',
					'model_name' => '$motormodel.model_name',
					'taxi_no' => '$taxi.taxi_no',
					'taxi_company' => '$taxi.taxi_company',
					'max_luggage' => '$taxi.max_luggage',
					'taxi_sliderimage' => '$taxi.taxi_sliderimage',
					'taxi_speed' => '$taxi.taxi_speed',
					'taxi_image' => '$taxi.taxi_image',	
					'taxi_serializeimage' => '$taxi.taxi_serializeimage',
					/*'created_by' => '$taxi.taxi_createdby',					
					'taxi_availability' => '$taxi.taxi_availability',
					'taxi_status' => '$taxi.taxi_status',
					'company_name' => '$company.companydetails.company_name',
					'taxi_capacity' => '$taxi.taxi_capacity',					
					'taxi_fare_km' => '$taxi.taxi_fare_km',
					'company_id' => '$taxi.taxi_company',					
					'taxi_owner_name' => '$taxi.taxi_owner_name',
					'taxi_min_speed' => '$taxi.taxi_min_speed',					
					'taxi_manufacturer' => '$taxi.taxi_manufacturer',
					'taxi_colour' => '$taxi.taxi_colour',
					'taxi_motor_expire_date' => '$taxi.taxi_motor_expire_date',
					'taxi_pco_licence_number' => '$taxi.taxi_pco_licence_number',
					'taxi_pco_licence_expire_date' => '$taxi.taxi_pco_licence_expire_date',
					'taxi_insurance_number' => '$taxi.taxi_insurance_number',
					'taxi_insurance_expire_date_time' => '$taxi.taxi_insurance_expire_date_time',*/
					
				)
			),
		);
		$res = $this->mongo_db->aggregate(MDB_CSC,$arguments);
		if(!empty($res['result'])){
			$result = $res['result'][0];
			$result['taxi_serializeimage'] = isset($result['taxi_serializeimage']) ? $result['taxi_serializeimage'] : '';
			$result[] = $result;
		}
		//echo '<pre>';print_r($result);exit;
		return $result;
    }
    public function details_userinfo($id, $driver = 0)
    {
		$common_args = array( array('$lookup' => array('from' => MDB_COMPANY,'localField' => 'company_id','foreignField' => '_id','as' => 'company')),
						array('$unwind' =>  array( 'path' =>  '$company', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$lookup' => array('from' => MDB_CSC,'localField' => 'login_country','foreignField' => '_id','as' => 'csc')),
						array('$unwind' =>  array( 'path' =>  '$csc', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$unwind' =>  array( 'path' =>  '$csc.stateinfo', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$unwind' =>  array( 'path' =>  '$csc.stateinfo.cityinfo', 'preserveNullAndEmptyArrays' => true)));
		if($driver == 0){
			$driver_args = array(
						array('$project'  =>  array(
							'user_createdby' => '$user_createdby','user_type' => '$user_type',
							'company_id' => '$company_id','login_country' => '$login_country',
							'login_state' => '$login_state','login_city' => '$login_city',
							'name' => '$name','username' => '$username',
							'lastname' => '$lastname' ,'email' => '$email' , 'phone' => '$phone' , 
							'address' => '$address' ,'driver_license_id' => '$driver_license_id' , 
							'dob' => '$dob' , 'account_balance' => '$account_balance' , 
							'booking_limit' => '$booking_limit' ,'login_status' => '$login_status',
							'login_state' => '$login_state','login_city' => '$login_city',
							'company' => '$company',
							'csc' => '$csc',
						 'cmp_value' =>  array('$eq' =>  array('$login_state', '$csc.stateinfo.state_id')),
						 'cmp_value' =>  array('$eq' =>  array('$login_city', '$csc.stateinfo.cityinfo.city_id')))),
						array('$match' => array('cmp_value' => true,'_id' => (int)$id)),
						array('$project' => array('user_createdby' => '$user_createdby','user_type' => '$user_type',
							'company_id' => '$company_id','login_country' => '$login_country',
							'login_state' => '$login_state','login_city' => '$login_city',
							'name' => '$name','username' => '$username',
							'lastname' => '$lastname' ,'email' => '$email' , 'phone' => '$phone' , 
							'address' => '$address' ,'driver_license_id' => '$driver_license_id' , 
							'dob' => '$dob' , 'account_balance' => '$account_balance' , 
							'booking_limit' => '$booking_limit' ,'login_status' => '$login_status',
							'login_state' => '$login_state','login_city' => '$login_city',
							'company_name' => '$company.companydetails.company_name',
							'company_address' => '$company.companydetails.company_address',
							'country_name' => '$csc.country_name',
							'state_name' => '$csc.stateinfo.state_name',
							'cityname' => '$csc.stateinfo.cityinfo.city_name'
							))
				);
			$args = array_merge($common_args, $driver_args);
			$res	= $this->mongo_db->aggregate(MDB_PEOPLE, $args);			
			$result	=  (!empty($res['result'])) ? $res['result'] :array();
		}else{
			
			$match = array('_id' => (int)$id,'user_type'  =>  'D','cmp_value' => true);
			$driver_args = array(
							array('$lookup' => array('from' => MDB_DRIVER_INFO,'localField' => '_id','foreignField' => '_id','as' => 'driver')),
							array('$unwind' =>  array( 'path' =>  '$driver', 'preserveNullAndEmptyArrays' =>  true ) ),
							array('$lookup' => array('from' => MDB_DRIVER_REF,'localField' => '_id','foreignField' => 'registered_driver_id','as' => 'driver_referral_list')),
							//array('$unwind' =>  array( 'path' =>  '$driver_referral_list', 'preserveNullAndEmptyArrays' =>  true ) ),
							array('$project' => array('company_name' => '$company.companydetails.company_name',
								'company_address' => '$company.companydetails.company_address',
								'user_createdby' => '$user_createdby',
								'user_type' => '$user_type',
								'company_id' => '$company_id',
								'login_country' => '$login_country',
								'login_state' => '$login_state',
								'login_city' => '$login_city',
								'name' => '$name',
								'lastname' => '$lastname',   
								'address' => '$address',
								'email' => '$email',
								'phone' => '$phone',
								'driver_license_id' => '$driver_license_id',
								'dob' => '$dob',
								'booking_limit' => '$booking_limit',
								'login_status' => '$login_status',
								'gender' => '$gender',
								'driver_license_expire_date' => '$driver.driverinfo.driver_license_expire_date',
								'driver_pco_license_number' => '$driver.driverinfo.driver_pco_license_number',
								'driver_pco_license_expire_date' => '$driver.driverinfo.driver_pco_license_expire_date',
								'driver_insurance_number' => '$driver.driverinfo.driver_insurance_number',
								'driver_insurance_expire_date' => '$driver.driverinfo.driver_insurance_expire_date',
								'driver_national_insurance_number' => '$driver.driverinfo.driver_national_insurance_number',
								'driver_national_insurance_expire_date' => '$driver.driverinfo.driver_national_insurance_expire_date',
								'registered_driver_code' => '$driver_referral_list.registered_driver_code',
								'country_name' => '$csc.country_name',
								'state_name' => '$csc.stateinfo.state_name',
								'cityname' => '$csc.stateinfo.cityinfo.city_name',
								'cmp_value' =>  array('$eq' =>  array('$login_state', '$csc.stateinfo.state_id')),
								'cmp_value' =>  array('$eq' =>  array('$login_city', '$csc.stateinfo.cityinfo.city_id'))
							)),
							array('$match' => $match)
						);
						
			$args = array_merge($common_args, $driver_args);
			$res	= $this->mongo_db->aggregate(MDB_PEOPLE, $args);
			$result	=  (!empty($res['result'])) ? $res['result'] :array();
		}
        $details = array();				
		foreach($result as $key => $res)
		{
			
			$details[$key]['created_by'] = $this->userNamebyId($res['user_createdby']);
			$details[$key]['name'] = isset($res['name']) ? $res['name']: '';
			$details[$key]['username'] = isset($res['username']) ? $res['username']: '';
			$details[$key]['lastname'] = isset($res['lastname']) ? $res['lastname']: '';
			$details[$key]['email'] = isset($res['email']) ? $res['email']: '';
			$details[$key]['phone'] = isset($res['phone']) ? $res['phone']: '';
			$details[$key]['address'] = isset($res['address']) ? $res['address']: '';
			$details[$key]['user_type'] = isset($res['user_type']) ? $res['user_type']: '';
			$details[$key]['driver_license_id'] = isset($res['driver_license_id']) ? $res['driver_license_id']: '';
			$details[$key]['dob'] = isset($res['dob']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['dob']):'';
			$details[$key]['id'] = isset($res['_id']) ? $res['_id'] : '';
			$details[$key]['account_balance'] = isset($res['account_balance']) ? $res['account_balance'] : '';
			$details[$key]['booking_limit'] = isset($res['booking_limit']) ? $res['booking_limit'] : '';
			$details[$key]['login_status'] = isset($res['login_status']) ? $res['login_status'] : '';
			$details[$key]['login_country'] = isset($res['login_country']) ? $res['login_country']: '';
			$details[$key]['login_state'] = isset($res['login_state']) ? $res['login_state']: '';
			$details[$key]['login_city'] = isset($res['login_city']) ? $res['login_city']: '';
			$details[$key]['country_name'] = isset($res['country_name']) ?  $res['country_name'] :'';
			$details[$key]['country_code'] = isset($res['country_code']) ?  $res['country_code'] :'';
			$details[$key]['state_name'] = isset($res['state_name']) ?  $res['state_name'] :'';
			$details[$key]['city_name'] = isset($res['cityname']) ?  $res['cityname'] :'';
			$details[$key]['company_id'] = isset($res['company_id']) ? $res['company_id'] : '';
			$details[$key]['registered_driver_code'] = isset($res['registered_driver_code']) ? $res['registered_driver_code'] : '';
			if(($res['user_type'] != 'N' && $res['user_type'] !='S') && $res['company_id'] !='')
			{
				$details[$key]['company_id'] = isset($res['company_id']) ? $res['company_id'] : '';
				$details[$key]['company_name'] = isset($res['company_name']) ?  $res['company_name'] :'';
				$details[$key]['company_address'] = isset($res['company_address']) ?  $res['company_address'] :'';				
				if($driver == 1) {
					$details[$key]['gender'] = $res['gender'];
					$details[$key]['driver_license_expire_date'] = !empty($res['driver_license_expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['driver_license_expire_date'][0]):'';
					
					$details[$key]['driver_pco_license_number'] = !empty($res['driver_pco_license_number']) ? $res['driver_pco_license_number'][0]:'';
					$details[$key]['driver_pco_license_expire_date'] = !empty($res['driver_pco_license_expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['driver_pco_license_expire_date'][0]):'';
					
					$details[$key]['driver_insurance_number'] = !empty($res['driver_insurance_number']) ? $res['driver_insurance_number'][0]:'';
					$details[$key]['driver_insurance_expire_date'] = !empty($res['driver_insurance_expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['driver_insurance_expire_date'][0]):'';
					
					$details[$key]['driver_national_insurance_number'] = !empty($res['driver_national_insurance_number']) ? $res['driver_national_insurance_number'][0]:'';
					$details[$key]['driver_national_insurance_expire_date'] = !empty($res['driver_national_insurance_expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['driver_national_insurance_expire_date'][0]) :'';
					
					$details[$key]['registered_driver_code'] = isset($res['registered_driver_code'][0]) ? $res['registered_driver_code'][0]:'';
				}
			}
		}
		//echo"<pre>"; print_r($details); exit; 
	 	return $details;
    }
	
	/** passenger info **/
    public function details_passengerinfo($id)
    {
        //$result  = $this->mongo_db->find(MDB_PASSENGERS,array('_id' => (int)$id),array('_id','name', 'email', 'phone', 'address', 'user_status', 'discount', 'referral_code', 'referral_code_amount', 'wallet_amount','country_code'));
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[
                            'projection'=>[
                                '_id'=>1,
                                'name'=>1,
                                'email'=>1,
                                'phone'=>1,
                                'address'=>1,
                                'user_status'=>1,
                                'discount'=>1,
                                'referral_code'=>1,
                                'referral_code_amount'=>1,
                                'wallet_amount'=>1,
                                'country_code'=>1
                            ]                            
                        ];
                        $result  = $this->mongo_db->find(MDB_PASSENGERS,array('_id' => (int)$id),$options);
		$details = array();
		$key = 0;
		//print_r($result);exit;
        foreach ($result as $main_doc) {
			$details[$key]['id']                   = $main_doc['_id'];
			$details[$key]['user_status']          = (isset($main_doc['user_status'])) ? $main_doc['user_status'] : '';
			$details[$key]['name']                 = (isset($main_doc['name'])) ? $main_doc['name'] : '';
			$details[$key]['email']                = (isset($main_doc['email'])) ? $main_doc['email'] : '';
			$details[$key]['phone']                = (isset($main_doc['phone'])) ? $main_doc['phone'] : '';
			$details[$key]['country_code']         = (isset($main_doc['country_code'])) ? $main_doc['country_code'] : '';
			$details[$key]['address']              = (isset($main_doc['address'])) ? $main_doc['address'] : '';
			$details[$key]['discount']             = (isset($main_doc['discount'])) ? $main_doc['discount'] : '';
			$details[$key]['referral_code']        = (isset($main_doc['referral_code'])) ? $main_doc['referral_code'] : '';
			$details[$key]['referral_code_amount'] = (isset($main_doc['referral_code_amount'])) ? $main_doc['referral_code_amount'] : '';
			$details[$key]['wallet_amount']        = (isset($main_doc['wallet_amount'])) ? $main_doc['wallet_amount'] : '';
			$refer_name = $this->get_reference_details($main_doc['_id']);
			$details[$key]['referred_by']        = $refer_name;
			$key++;
		}
		//echo "<pre>"; print_r($refer_name); exit;
		return $details;
    }
	
	public function get_reference_details($id)
	{
		$arguments = array(
			array('$unwind' => '$_id'),
			array(
				'$lookup' => array(
							'from'=>MDB_PASSENGER_REFERRAL,
							'localField'=> '_id',
							'foreignField' => "referred_by",
							'as'=> "referral"
						)
			),
			array('$match'=> array('referral.passenger_id' => (int)$id)),
			array(
				'$project' => array(
					'name' => '$name',
				)
			),
		);
	
		$referred_by = $this->mongo_db->aggregate(MDB_PASSENGERS,$arguments);
		return (!empty($referred_by) && isset($referred_by['result'][0]['name']))?$referred_by['result'][0]['name']:"-";
	}
   
    /** comapny details **/
    public function companydetails($id)
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_COMPANY,array('_id'=>(int)$id),array('companydetails.company_name','companydetails.company_address'));
        //echo '<pre>';print_r($result);exit;
        return (!empty($result) && count($result)>0)?$result:array();
    }
    public function countrydetails($id)
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_CSC,array('_id'=>(int)$id),array('country_name'));
        return (!empty($result) && count($result)>0)?$result['country_name']:'-';
    }
    public function statedetails($id,$cid)
    {
		//MongoDB
		$country_id = (int)$cid;
		$state_id = (int)$id;
                 ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        'stateinfo.$'=>1,                       
                    ],
                    'sort'=>[
                        'stateinfo.state_id'=>-1
                    ]
                ];
                $rs = $this->mongo_db->find(MDB_CSC,array('stateinfo.state_id'=>$state_id,'_id'=>$country_id),$options);
		
		$result = (!empty($rs))?$rs:array();
		if (!empty($result)){
			$result = (count($result[0]['stateinfo']) > 0) ? array_reverse((array)$result[0]['stateinfo']) : array();
			$state_name = $result[0]['state_name'];
		} else {
			$state_name = '';
		}
		return $state_name;
    }
    public function citydetails($id,$sid,$cid)
    {
		//echo $id.'=>'.$sid.'=>'.$cid;
		$country_id = (int)$cid;
		$state_id = (int)$sid;
		$city_id = (int)$id;
		//$rs = $this->mongo_db->find(MDB_CSC,array('_id'=>$country_id,'stateinfo.state_id'=>$state_id,'stateinfo.cityinfo.city_id'=>$city_id),array('stateinfo.cityinfo.$'=>1))->sort(array('stateinfo.cityinfo.city_id'=>-1));
                  ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        'stateinfo.cityinfo.$'=>1,                       
                    ],
                    'sort'=>[
                        'stateinfo.cityinfo.city_id'=>-1
                    ]
                ];
		$rs = $this->mongo_db->find(MDB_CSC,array('_id'=>$country_id,'stateinfo.state_id'=>$state_id,'stateinfo.cityinfo.city_id'=>$city_id),$options);
		$result = (!empty($rs))?$rs:array();
		if (!empty($result)){
			//print_r($result);
			$city_detail = $result[0]['stateinfo'][0]['cityinfo'];
			//Use recursive function search & get value for specific city details with array
			$resultset = $this->recursive_array_search((array)$city_detail,'city_id',$city_id);
			//print_r($resultset);exit;
			$city_name = $resultset[0]['city_name'];
		} else {
			$city_name = '';
		}
		return $city_name;
    }
    //for manage content view 
    public function content_list_view($id = '')
    {
        $ops = array(array('$match'=>array('_id'=>array('$eq'=>(int)$id))),
						array('$project'=>array(
							'menu_id'=>'$_id',
							'menu_name'=>'$menu_name',
							'menu_link'=>'$menu_link',
							'status_post'=>'$status_post',
							'menu'=>'$menu',
							'meta_title'=>'$meta_title',
							'meta_keyword'=>'$meta_keyword',
							'meta_description'=>'$meta_description',
							'content'=>'$content',
						)),											
				);
        $result = $this->mongo_db->aggregate(MDB_CMS,$ops);
        //print_r($result);
        return !empty($result['result'])?$result['result']:array();
        
    }
    //for deleting contents
    public function delete_content($id)
    {
       // $result = $this->mongo_db->deleteOne(MDB_CMS,array('_id'=>(int)$id));
         $data_set=[
            "menu" => "",
            "meta_title"=> "",
            "meta_keyword"=>"",
            "meta_description"=>"",
            "content"=>"",
            "content_status"=>""
            ];        
        $result = $this->mongo_db->updateOne(MDB_CMS,array('_id'=>(int)$id),array('$unset'=>$data_set),array('upsert'=>false));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
	/** Validating for edit view content **/
	public function validate_editview($arr) 
	{
		
		return Validation::factory($arr)
		->rule('meta_title', 'not_empty')
		->rule('meta_keyword', 'not_empty')
		->rule('meta_description', 'not_empty')
		->rule('menu_name', 'not_empty');		

	}
    /** Validating for edit view content **/
    public function validate_companyeditview($arr, $cid, $id)
    {
        return Validation::factory($arr)->rule('page_title', 'not_empty')->rule('page_title', 'max_length', array(
            ':value',
            '50'
        ))->rule('menu_name', 'not_empty')->rule('menu_name', 'max_length', array(
            ':value',
            '20'
        ))->rule('page_url', 'not_empty')->rule('page_url', 'max_length', array(
            ':value',
            '20'
        ))
            ->rule('page_url', 'Model_Manage::checkpageurl', array(
            ':value',
            $cid,
            $id
        ));
    }
    /** Updating content view while editing **/
    public function update_editview_content($post, $id)
    {
        $ops = array(array('$match'=>array('_id'=>array('$eq'=>(int)$post['menu_name']))),
					array('$project'=>array('menu_name'=>'$menu_name')));
        $result = $this->mongo_db->aggregate(MDB_CMS,$ops);
        $menu_name = (!empty($result['result']))?$result['result'][0]['menu_name']:"";        
        $data_set = array('menu'=>$menu_name,
				'meta_title'=>$post['meta_title'],
				'meta_keyword'=>$post['meta_keyword'],
				'meta_description'=>$post['meta_description'],
				'content'=>$post['content']
				);        
        $update = $this->mongo_db->updateOne(MDB_CMS,array('_id'=>(int)$post['menu_name']),array('$set'=>$data_set),array('upsert'=>false));
        return (count($update))?1:0;
    }
    //Check the menu already exists
    public function menu_name_exits($post, $id)
    {
        $result=0;
        if($post['menu_name'] != $id){
			$ops = array(array('$match'=>array('_id'=>array('$eq'=>(int)$post['menu_name']))),
			array('$project'=>array('menu'=>'$menu')));
			$result = $this->mongo_db->aggregate(MDB_CMS,$ops);
			$result = (isset($result['result'][0]['menu']))?1:0;		
		}        
        return $result;
    }
    public function get_companymanagerlist($id, $offset = '', $val = '',$find_count=false)
    {
		//MongoDB
		$match_query = array('company._id'=>(int)$id,'people.user_type'=>'M');
		if($find_count){
			$arguments = array(
					array('$unwind' =>  array( 'path' =>  '$stateinfo', 'preserveNullAndEmptyArrays' => true)),
					array('$unwind' =>  array( 'path' =>  '$stateinfo.cityinfo', 'preserveNullAndEmptyArrays' => true)),
					array('$lookup' => array(
							'from' =>        MDB_PEOPLE,
							'localField'=> 'stateinfo.cityinfo.city_id',
							'foreignField'=> "login_country",
							'foreignField'=> "login_city",
							'as'=> "people"
						)
					),
					array('$unwind' =>  array( 'path' =>  '$people', 'preserveNullAndEmptyArrays' => true)),
					array('$lookup' => array(
							'from'   => MDB_COMPANY,
							'localField' => 'people.company_id',
							'foreignField' => "_id",
							'as' => "company"
						)
					),
					array('$unwind' =>  array( 'path' =>  '$company', 'preserveNullAndEmptyArrays' => true)),
					array('$match'  => $match_query),
					array('$project' => array(
							'company_id' => '$company._id',
						)
					),
				array('$sort' =>array('people.created_date' => -1) ),
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result']))?count($result['result']):0;
		} else {
			$arguments = array(
					array('$unwind' => '$stateinfo'),
					array('$unwind' => '$stateinfo.cityinfo'),
					array('$lookup' => array(
							'from' => MDB_PEOPLE,
							'localField'=> 'stateinfo.cityinfo.city_id',
							'foreignField'=> "login_country",
							'foreignField'=> "login_city",
							'as'=> "people"
						)
					),
					array('$unwind' => '$people'),
					array('$lookup' => array(
							'from' => MDB_COMPANY,
							'localField' => 'people.company_id',
							'foreignField' => "_id",
							'as' => "company"
						)
					),
					array('$unwind' => '$company'),
					array('$match'  => $match_query),
					array('$project' => array('_id'=>0,
							'country_name' => '$country_name',
							'state_name' => '$stateinfo.state_name',
							'city_name' => '$stateinfo.cityinfo.city_name',
							'company_id' => '$company._id',
							'company_name' => '$company.companydetails.company_name',
							'created_date' => '$people.created_date',
							'status' => '$people.status',
							'name' => '$people.name',
							'email' => '$people.email',
							'address'=>'$people.address',
							'id' => '$people._id',
						)
					),
				array('$sort' =>array('people.created_date' => -1) ),
				array('$skip' => (int)$offset ),
				array('$limit' => (int)$val )
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result']))?$result['result']:array();
		}
    }
    //selected manus 
    public function get_menus()
    {
        $ops = array(
					array(
						'$project' => array(
						'menu_id' => '$_id',
						'menu_name' => '$menu_name',
						'status_post' => '$status_post',
						)
					),
					array(
						'$sort' => array("_id"=>-1)
					),
			);
		$result = $this->mongo_db->aggregate(MDB_CMS,$ops);
		return  (!empty($result['result']))?$result['result']:array();       
    }
    public function get_companydriverlist($id, $offset = '', $val = '',$find_count=false)
    {
		//MongoDB
		$match_query = array('company._id'=>(int)$id,'people.user_type'=>'D');
		if($find_count){
			$arguments = array(
					array('$unwind' => '$stateinfo'),
					array('$unwind' => '$stateinfo.cityinfo'),
					array('$lookup' => array(
							'from' => MDB_PEOPLE,
							'localField'=> 'stateinfo.cityinfo.city_id',
							'foreignField'=> "login_country",
							'foreignField'=> "login_city",
							'as'=> "people"
						)
					),
					array('$unwind' => '$people'),
					array('$lookup' => array(
							'from' => MDB_COMPANY,
							'localField' => 'people.company_id',
							'foreignField' => "_id",
							'as' => "company"
						)
					),
					array('$unwind' => '$company'),
					array('$match'  => $match_query),
					array('$project' => array(
							'company_id' => '$company._id',
						)
					),
				array('$sort' =>array('people.created_date' => -1) ),
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result']))?count($result['result']):0;
		} else {
			$arguments = array(
					array('$unwind' => '$stateinfo'),
					array('$unwind' => '$stateinfo.cityinfo'),
					array('$lookup' => array(
							'from' => MDB_PEOPLE,
							'localField'=> 'stateinfo.cityinfo.city_id',
							'foreignField'=> "login_country",
							'foreignField'=> "login_city",
							'as'=> "people"
						)
					),
					array('$unwind' => '$people'),
					array('$lookup' => array(
							'from' => MDB_COMPANY,
							'localField' => 'people.company_id',
							'foreignField' => "_id",
							'as' => "company"
						)
					),
					array('$unwind' => '$company'),
					array('$match'  => $match_query),
					array('$project' => array('_id'=>0,
							'country_name' => '$country_name',
							'state_name' => '$stateinfo.state_name',
							'city_name' => '$stateinfo.cityinfo.city_name',
							'company_id' => '$company._id',
							'company_name' => '$company.companydetails.company_name',
							'created_date' => '$people.created_date',
							'status' => '$people.status',
							'name' => '$people.name',
							'email' => '$people.email',
							'address'=>'$people.address',
							'id' => '$people._id',
						)
					),
				array('$sort' =>array('people.created_date' => -1) ),
				array('$skip' => (int)$offset ),
				array('$limit' => (int)$val )
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result']))?$result['result']:array();
		}
    }
    public function get_companytaxilist($id, $offset = '', $val = '',$find_count=false)
    {
        //MongoDB
		$match_query = array('taxi.taxi_company'=>(int)$id);
		if($find_count){
			$arguments = array(
					array('$unwind' => '$stateinfo'),
					array('$unwind' => '$stateinfo.cityinfo'),
					array('$lookup' => array(
							'from' => MDB_TAXI,
							'localField'=> 'stateinfo.cityinfo.city_id',
							'foreignField'=> "taxi_country",
							'foreignField'=> "taxi_state",
                            'foreignField'=> "taxi_city",
							'as'=> "taxi"
						)
					),
					array('$unwind' => '$taxi'),
					array('$lookup' => array(
							'from' => MDB_COMPANY,
							'localField' => 'taxi.taxi_company',
							'foreignField' => "_id",
							'as' => "company"
						)
					),
					array('$unwind' => '$company'),
					array('$match'  => $match_query),
					array('$project' => array(
							'taxi_id' => '$taxi._id',
						)
					),
				array('$sort' =>array('taxi.created_date' => -1) ),
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result']))?count($result['result']):0;
		} else {
			$arguments = array(
					array('$unwind' => '$stateinfo'),
					array('$unwind' => '$stateinfo.cityinfo'),
					array('$lookup' => array(
							'from' => MDB_TAXI,
							'localField'=> 'stateinfo.cityinfo.city_id',
							'foreignField'=> "taxi_country",
							'foreignField'=> "taxi_state",
                            'foreignField'=> "taxi_city",
							'as'=> "taxi"
						)
					),
					array('$unwind' => '$taxi'),
					array('$lookup' => array(
							'from' => MDB_COMPANY,
							'localField' => 'taxi.taxi_company',
							'foreignField' => "_id",
							'as' => "company"
						)
					),
					array('$unwind' => '$company'),
					array('$match'  => $match_query),
					array('$project' => array(
							'country_name' => '$country_name',
							'state_name' => '$stateinfo.state_name',
							'city_name' => '$stateinfo.cityinfo.city_name',
							'taxi_id' => '$taxi._id',
							'company_name' => '$company.companydetails.company_name',
							'created_date' => '$taxi.created_date',
							'taxi_status' => '$taxi.taxi_status',
							'taxi_no' => '$taxi.taxi_no',
						)
					),
				array('$sort' =>array('taxi.created_date' => -1) ),
				array('$skip' => (int)$offset ),
				array('$limit' => (int)$val )
			);
			$result = $this->mongo_db->aggregate(MDB_CSC,$arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result']))?$result['result']:array();
		}
    }
    /** getting driver rating given by users**/
    public function getdriverratinglist($id)
    {
		$args = array(array('$lookup' => array(
							'from' => MDB_PASSENGERS,
							'localField' => 'passengers_id',
							'foreignField' => "_id",
							'as' => "passenger")),
					array('$unwind' => array('path' =>  '$passenger', 'preserveNullAndEmptyArrays' =>  true)),
					array('$match' => array('driver_id' => (int)$id)),
					array('$group' => array(
								'_id' => NULL,
								'count' => array('$sum' => 1)))
				);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		//echo "<pre>"; print_r($result); exit;
		return (!empty($result['result'])) ? $result['result'][0]['count'] :0;
    }
    /** getting driver rating given by users**/
    public function get_driverratinglist($id, $offset = '', $val = '')
    {
		$result = $temp_arr = array();
		$args = array(array('$lookup' => array(
							'from' => MDB_PASSENGERS,
							'localField' => 'passengers_id',
							'foreignField' => "_id",
							'as' => "passenger")),
					array('$unwind' => array('path' =>  '$passenger', 'preserveNullAndEmptyArrays' =>  true)),
					array('$match' => array('driver_id' => (int)$id)),
					array('$project' => array(
										'rating' => '$rating',
										'comments' => '$comments',
										'name' => '$passenger.name',
										'current_location' => '$current_location',
										'drop_location' => '$drop_location',
										'no_passengers' => '$no_passengers',
										'pickup_time' => '$pickup_time',
										'waitingtime' => '$waitingtime')),
					array('$sort' => array('_id' => 1)),
					array('$skip' => (int)$offset),
					array('$limit' => (int)$val),
				);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr['rating'] = isset($r['rating']) ? $r['rating']: '';
				$temp_arr['comments'] = isset($r['comments']) ? $r['comments']: '';
				$temp_arr['name'] = isset($r['name']) ? $r['name']: '';
				$temp_arr['current_location'] = isset($r['current_location']) ? $r['current_location']: '';
				$temp_arr['drop_location'] = isset($r['drop_location']) ? $r['drop_location']: '';
				$temp_arr['no_passengers'] = isset($r['no_passengers']) ? $r['no_passengers']: '';
				$temp_arr['pickup_time'] = isset($r['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']): '';
				$temp_arr['waitingtime'] = isset($r['waitingtime']) ? $r['waitingtime']: '';
				$result[] = $temp_arr;
			}			
		}
		//echo '<pre>';print_r($result);exit;
		return $result;
    }
    public function getmanagerdriverlist($id)
    {
        $count = $this->get_managerdriverlist($id,'','',$find=true);
        return $count;
    }
    /** getting data for manager driver list **/
    public function get_managerdriverlist($id,$offset,$val,$find_count=false)
    {
		$result = array();
		$check_query =  array('company_id'=>(int)$id,'user_type' => 'D');
		$check_args = array(
			array('$match'  => $check_query),
			array('$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'company._id',
					'foreignField' => "company_id",
					'as' => "company"
				)
			),
			array('$project' => array('_id'=>0,
					'id'=>'$_id',
					'company_id' => '$company_id',
					'login_city' => '$login_city',
					'login_state' => '$login_state',
					'login_country' => '$login_country',
				)
			),
		);
		$res = $this->mongo_db->aggregate(MDB_PEOPLE,$check_args);
		//echo "<pre>"; print_r($res); exit;
		if(count($res['result'])==0){
			return ($find_count)?0:array();
		}
		$company_id    = $res['result'][0]['company_id'];
		$login_city    = $res['result'][0]['login_city'];
		$login_country = $res['result'][0]['login_country'];
		$login_state   = $res['result'][0]['login_state'];
		
		$match_query = array('company._id'=>(int)$company_id,
							'people.user_type'=>'D',
							'people.login_country'=>(int)$login_country,
							'people.login_state'=>(int)$login_state,
							'people.login_city'=>(int)$login_city
							);
		$common_args = array(
					array('$unwind' => '$stateinfo'),
					array('$unwind' => '$stateinfo.cityinfo'),
					array('$lookup' => array(
							'from' => MDB_PEOPLE,
							'localField'=> 'stateinfo.cityinfo.city_id',
							'foreignField'=> "login_country",
							'foreignField'=> "login_state",
							'foreignField'=> "login_city",
							'as'=> "people"
						)
					),
					array('$unwind' => '$people'),
					array('$lookup' => array(
							'from' => MDB_COMPANY,
							'localField' => 'people.company_id',
							'foreignField' => "_id",
							'as' => "company"
						)
					),
					array('$unwind' => '$company')
				);
		if($find_count){
			$arguments = array(					
					array('$match'  => $match_query),
					array('$project' => array(
							'company_id' => '$company._id',
						)
					),
				array('$sort' =>array('people.created_date' => -1) ),
			);
			$args = array_merge($common_args, $arguments);
			$result = $this->mongo_db->aggregate(MDB_CSC,$args);
			//echo "<pre>if"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result']))?count($result['result']):0;
		} else {
			
			$arguments = array(
					array('$project' => array(
							'id' => '$people._id',
							'country_name' => '$country_name',
							'state_name' => '$stateinfo.state_name',
							'city_name' => '$stateinfo.cityinfo.city_name',
							'cid' => '$company._id',
							'company_name' => '$company.companydetails.company_name',
							//'created_date' => '$people.created_date',
							'status' => '$people.status',
							'name' => '$people.name',
							'user_type' => '$people.user_type',
							//'email' => '$people.email',
							//'address'=>'$people.address'
						)
					),
				array('$match'  => array('cid'=>(int)$id,'user_type' => 'D')),
				array('$sort' =>array('people.created_date' => -1) ),
				array('$skip' => (int)$offset ),
				array('$limit' => (int)$val )
			);
			$args = array_merge($common_args, $arguments);
			$res = $this->mongo_db->aggregate(MDB_CSC,$args);
			//echo "<pre>else"; print_r($res); exit;
			if(!empty($res['result'])){
				$i=0;
				foreach($res['result'] as $r){
					$result[$i] = $r;
					$i++;
				}
			}
			//echo "<pre>else"; print_r($result); exit;
			return $result;
		}
    }
    
    public function getmanagertaxilist($id=''){
		
		$count = $this->get_managertaxilist($id,'','',true);
		return $count;
	}
    
    /** getting data for manager taxi list **/
    public function get_managertaxilist($id, $offset = '', $val = '',$find_count=false)
    {        
		$result = array();
		$check_query =  array('_id'=>(int)$id,'user_type'=>'M');
		$check_args = array(
			array('$match'  => $check_query),
			array('$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'company._id',
					'foreignField' => "company_id",
					'as' => "company"
				)
			),
			array('$project' => array('_id'=>0,
					'id'=>'$_id',
					'company_id' => '$company_id',
					'login_city' => '$login_city',
					'login_state' => '$login_state',
					'login_country' => '$login_country',
				)
			),
		);
		$res = $this->mongo_db->aggregate(MDB_PEOPLE,$check_args);
		echo "<pre>"; print_r($res); exit;
		if( count($res['result'])==0){
			return ($find_count)?0:array();
		}
		$company_id    = $res['result'][0]['company_id'];
        $login_city    = $res['result'][0]['login_city'];
        $login_country = $res['result'][0]['login_country'];
        $login_state   = $res['result'][0]['login_state'];
		$match_query = array('company._id'=>(int)$company_id,
							'taxi.taxi_country'=>(int)$login_country,
							'taxi.taxi_state'=>(int)$login_state,
							'taxi.taxi_city'=>(int)$login_city
							);
		
		$common_arguments = array(
						array('$unwind' => '$stateinfo'),
						array('$unwind' => '$stateinfo.cityinfo'),
						array('$lookup' => array(
								'from' => MDB_TAXI,
								'localField'=> 'stateinfo.cityinfo.city_id',
								'foreignField'=> "taxi_country",
								'foreignField'=> "taxi_state",
								'foreignField'=> "taxi_city",
								'as'=> "taxi"
							)
						),
						array('$unwind' => '$taxi'),
						array('$lookup' => array(
								'from' => MDB_COMPANY,
								'localField' => 'taxi.taxi_company',
								'foreignField' => "_id",
								'as' => "company"
							)
						),
						array('$unwind' => '$company'),
						array('$match'  => $match_query),
				);
		if($find_count){
			$arguments = array(
					array('$project' => array(
							'taxi_id' => '$taxi._id',
						)
					),
				array('$sort' =>array('taxi.created_date' => -1) ),
			);
			$args = array_merge($common_arguments, $arguments);
			$result = $this->mongo_db->aggregate(MDB_CSC,$args);
			echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result']))?count($result['result']):0;
		} else {
			$arguments = array(
					array('$project' => array(
							'country_name' => '$country_name',
							'state_name' => '$stateinfo.state_name',
							'city_name' => '$stateinfo.cityinfo.city_name',
							'taxi_id' => '$taxi._id',
							'company_name' => '$company.companydetails.company_name',
							'created_date' => '$taxi.created_date',
							'taxi_status' => '$taxi.taxi_status',
							'taxi_no' => '$taxi.taxi_no',
							'cid' => '$taxi.taxi_company',
						)
					),
				array('$sort' =>array('taxi.created_date' => -1) ),
				array('$skip' => (int)$offset ),
				array('$limit' => (int)$val )
			);
			$args = array_merge($common_arguments, $arguments);
			$res = $this->mongo_db->aggregate(MDB_CSC,$args);
			//echo "<pre>"; print_r($args); exit;
			if(!empty($res['result'])){
				$i=0;
				foreach($res['result'] as $r){
					$result[$i] = $r;
					$i++;
				}				
			}
			//echo "<pre>"; print_r($res); exit;
			return $result;
		}
    }
    public function validate_unavailabledriver($arr)
    {
        return Validation::factory($arr)->rule('reason', 'not_empty')->rule('startdate', 'not_empty')->rule('enddate', 'not_empty')->rule('enddate', 'Model_Manage::date_diff', array(
            'value',
            $arr['startdate']
        ))->rule('enddate', 'Model_Manage::checkunavailable', array(
            ':value',
            $arr
        ));
    }
    public static function date_diff($enddate, $startdate)
    {
        if ($startdate > $enddate) {
            return 1;
        } else {
            return 0;
        }
    }
    public function check_peoplecompanyid($id)
    {
		$result = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=>(int)$id));
		return (!empty($result))?$result:array();
	}
    public function check_taxicompanyid($id)
    {
		$result = array();
		$res = $this->mongo_db->findOne(MDB_TAXI,array('_id'=>(int)$id),
										array('taxi_company','taxi_country','taxi_state','taxi_city'));
		if(!empty($res)){
			$result[] = $res;
		}
		return $result;
    }
    public function check_companyid($id)
    {
		$result = array();
		$res = $this->mongo_db->findOne(MDB_COMPANY,array('companydetails.userid'=>(int)$id),array('_id'));
		$result= array();
		
		if(!empty($res)){
			$result[]['cid'] = $res['_id'];
		}
		return $result;
    }
    
    public function active_availabilitytaxi_request($activeids)
    {
        //MongoDB
        //Here changing array values with string to integers values
        $active_ids = Commonfunction::mongo_format_array($activeids);
        $result = $this->mongo_db->updateMany(MDB_TAXI,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('taxi_availability' => 'A')));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function block_availabilitytaxi_request($activeids)
    {
        //MongoDB
        //Here changing array values with string to integers values
        $active_ids = Commonfunction::mongo_format_array($activeids);
        $result = $this->mongo_db->updateMany(MDB_TAXI,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('taxi_availability' => 'D')));
        //echo '<pre>';print_r($result);exit;
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function active_availabilitydriver_request($activeids)
    {
        $active_ids = Commonfunction::mongo_format_array($activeids);
        $result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('availability_status' => 'A')));
        
        return count($result);
        
    }
    public function block_availabilitydriver_request($activeids)
    {
        $active_ids = Commonfunction::mongo_format_array($activeids);
		$result = $this->mongo_db->updateMany(MDB_PEOPLE,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('availability_status' => 'D')));
        return count($result);
    }
    // get menu list	
    public function count_menu_list()
    {
        $result = $this->mongo_db->count(MDB_CMS,array('menu_name'=>array('$ne'=>null)));
        return $result;
    }
    public function all_menu_list($offset, $val)
    {
		$ops = array(
					array('$match' => array('menu_name'=>array('$ne'=>null))),
					array(
						'$project' => array(
						'menu_id' => '$_id',
						'menu_name' => '$menu_name',
						'status_post' => '$status_post',
						)
					),
					array(
						'$sort' => array("_id"=>-1)
					),
					array(
						'$skip' => (int)$offset
					),
					array(
						'$limit' => (int)$val
					)
			);
		$result = $this->mongo_db->aggregate(MDB_CMS,$ops);
		//echo '<pre>';print_r($result);exit;
		return (!empty($result['result']))?$result['result']:array();
    } 
    //For deleting menu
    public function delete_menu($id)
    {
        $result = $this->mongo_db->deleteOne(MDB_CMS,array('_id'=>(int)$id));
		return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    public function details_taxi_driver($id)
    {
        $now   = date('Y-m-d H:i:s');
		 $result = $this->mongo_db->findOne(MDB_TAXI_DRIVER_MAPPING,array('mapping_taxiid'=>(int)$id,"mapping_startdate"=>array('$lte'=>Commonfunction::MongoDate(strtotime($now))),"mapping_enddate"=>array('$gte'=>Commonfunction::MongoDate(strtotime($now)))),array('mapping_driverid'));
		return (!empty($result))?$result:array();
    }
    public function update_comments($passengers_log_id)
    {
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS, array('_id' => (int)$passengers_log_id), array('$set' => array('comments' => '')), array('upsert' => false));
        return (empty($result->getwriteErrors())) ? 1: 0;
    }
    public function get_unassign_taxi_searchlist($find_count=false, $keyword = "",$offset = "", $val = "")
    {              
        $usertype       = $this->usertype;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
        $company_id     = $this->company_id;
		$match_query = $taxi_list = array();
		$match_query['taxi_status'] = 'A';
		$match_query['taxi_availability'] = 'A';
		$booked_driver = $this->free_availabletaxi_list();
        if (count($booked_driver) > 0) {
            foreach ($booked_driver as $key => $value) {
                $taxi_list[] = (int)$value['id'];
            }
			$match_query['_id'] = array('$nin' => $taxi_list);
        }
        //echo '--'.$company_id;
        if (!empty($company_id) && $company_id!=0) {
			$match_query['taxi_company'] = (int)$company_id;
        }
        if ($usertype == 'M') {
			$match_query['taxi_country'] = (int)$country_id;
			$match_query['taxi_state'] = (int)$state_id;
			$match_query['taxi_city'] = (int)$city_id;
        }
        
        $keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
        if ($keyword) {
			$search = array("\$or"=>array(array( 'taxi_no' => Commonfunction::MongoRegex("/$keyword/i")) ,
										array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") )
										 ) );
			
			$match_query = array("\$and" => array($match_query, $search));
        }
		//echo "<pre>"; print_r($match_query); exit;
		$common_arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'taxi_company',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			array(
				'$unwind' => '$company'
			),
			array(
				'$match' => $match_query
			),
		);
		if($find_count == TRUE){
			$count_arguments = array(
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
			$arguments = array_merge($common_arguments,$count_arguments);
			$result    = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
		}else{
			$field_arguments = array(
				array(
					'$project' => array(
						'taxi_id' => '$_id',
						'taxi_no' => '$taxi_no',
						'cid' => '$company._id',
						'company_name' => '$company.companydetails.company_name'						
					)
				),
				array(
					'$sort' => array('_id' => 1)
				),
				array(
					'$skip' => $offset
				),
				array(
					'$limit' => (int)$val
				),
			);
			$arguments = array_merge($common_arguments,$field_arguments);
			$result    = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
			//echo "<pre>"; print_r($arguments); exit;
			return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		}
    }
    public function free_availabletaxi_list()
    {
        $currentdate = date('Y-m-d H:i:s');
        $enddate     = date('Y-m-d') . ' 23:59:59';
        $match1 = array('taximapping.mapping_status'=>"A" ,
						'taximapping.mapping_startdate' => array('$gte' => Commonfunction::MongoDate(strtotime($currentdate))),
						'taximapping.mapping_startdate' => array('$lte' => Commonfunction::MongoDate(strtotime($enddate))));
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
							 'as'=>"taximapping"        
						)),
						array('$unwind'=>'$taximapping'),
						array('$match'=> $match1),
						array('$lookup'=>array(
							'from'=>MDB_PEOPLE,
							'localField'=>"taximapping.mapping_driverid",
							'foreignField'=>"_id",
							 'as'=>"people"        
						)),
						array('$match'=>array( 'people.status'=> 'A' )),
						array('$project' => array(
							'id' => '$people._id',
							'taxi_id' => '$_id',
						)),
						//array('$limit' => 10)
					);
        $result = $this->mongo_db->aggregate(MDB_TAXI,$arguments);
        return (isset($result['result']) ? $result['result']: array());
    }
	
	public function all_freetaxi_list($offset, $val, $cid = 0, $find_count = FALSE)
    {
		$currentdate = date('Y-m-d H:i:s');
        $enddate     = date('Y-m-d') . ' 23:59:59';
		$match_query                     = array();
		$match_query['mapping.mapping_status'] = 'A';
		$match_query['driver.status'] = 'A';
		$match_query['company.companydetails.company_status'] = 'A';
		$match_query['country_status'] = 'A';
		$match_query['stateinfo.state_status'] = 'A';
		$match_query['stateinfo.cityinfo.city_status'] = 'A';
		$match_query['taxi.taxi_status'] = 'A';
		$match_query['taxi.taxi_availability'] = 'A';
		$match_query['people.status'] = 'A';
		$match_query['people.availability_status'] = 'A';
		$match_query['people.user_type'] = 'D';
		
		if ($cid!="" && $cid!=0) {
			$match_query['taxi.taxi_company'] = (int)$cid;
		}
		if ($currentdate!="" && $enddate!="") {
			$match_query['mapping.mapping_startdate'] = array('$gte' => $currentdate);
			$match_query['mapping.mapping_enddate'] = array('$lte' => $enddate);
		}
		//echo "<pre>"; print_r($match_query); exit;
		$common_arguments = array(
			array(
				'$unwind' => '$stateinfo'
			),
			array(
				'$unwind' => '$stateinfo.cityinfo'
			),
			array(
				'$lookup' => array(
					'from' => MDB_TAXI_DRIVER_MAPPING,
					'localField' => 'stateinfo.cityinfo.city_id',
					'foreignField' => 'mapping_countryid',
					'foreignField' => 'mapping_cityid',
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
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'mapping.mapping_companyid',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			array(
				'$unwind' => '$company'
			),
			
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'mapping.mapping_driverid',
					'foreignField' => '_id',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
			array(
				'$lookup' => array(
					'from' => MDB_DRIVER_INFO,
					'localField' => 'mapping.mapping_driverid',
					'foreignField' => '_id',
					'as' => 'driver'
				)
			),
			array(
				'$unwind' => '$driver'
			),
			array(
				'$match' => $match_query
			),
		);
		
		if ($find_count == TRUE) {
			$count_arguments = array(
				array(
					'$project' => array(
						'result' => '$people._id'
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
			$merge_arguments = array_merge($common_arguments, $count_arguments);
			$result          = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
			//echo "<pre>";print_r($result['result']);exit;
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
		} else {
			$field_arguments = array(
				array(
					'$sort' => array( 
						'people.created_date' => -1
					),
				),
				array(
					'$project' => array(
						'id' => '$people._id',
						'name' => '$people.name',
						'taxi_id' => '$taxi._id',
						'taxi_no' => '$taxi.taxi_no',
						'cid' => '$company._id',
						'company_name' => '$company.companydetails.company_name',
						'phone' => '$people.phone'
					)
				),
				array('$skip'	=> (int)$offset ),
				array('$limit'	=> (int)$val )
			);
			$merge_arguments = array_merge($common_arguments, $field_arguments);
			$result    = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
			//echo "<pre>";print_r($result['result']); exit;
			return (!empty($result['result'])) ? $result['result'] : array();
		}
    }
    public function get_all_freetaxi_searchlist($find_count = false,$keyword = "", $offset = "", $val = "")
    {
        $currentdate = date('Y-m-d H:i:s');
        $enddate     = date('Y-m-d') . ' 23:59:59';
		$match_query                     = array();
		$match_query['mapping.mapping_status'] = 'A';
		$match_query['driver.status'] = 'A';
		$match_query['company.companydetails.company_status'] = 'A';
		$match_query['country_status'] = 'A';
		$match_query['stateinfo.state_status'] = 'A';
		$match_query['stateinfo.cityinfo.city_status'] = 'A';
		$match_query['taxi.taxi_status'] = 'A';
		$match_query['taxi.taxi_availability'] = 'A';
		$match_query['people.status'] = 'A';
		$match_query['people.availability_status'] = 'A';
		$match_query['people.user_type'] = 'D';
		$keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
		if ($keyword) {
			$search = array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) ,
										array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") )
										 ) );
			
			$match_query = array("\$and" => array($match_query, $search));
        }
        $company_id     = $this->company_id;
		if ($company_id!="" && $company_id!=0) {
			$match_query['taxi.taxi_company'] = (int)$company_id;
		}
		if ($currentdate!="" && $enddate!="") {
			$match_query['mapping.mapping_startdate'] = array('$gte' => $currentdate);
			$match_query['mapping.mapping_enddate'] = array('$lte' => $enddate);
		}
		//echo "<pre>"; print_r($match_query); exit;
		$common_arguments = array(
			array(
				'$unwind' => '$stateinfo'
			),
			array(
				'$unwind' => '$stateinfo.cityinfo'
			),
			array(
				'$lookup' => array(
					'from' => MDB_TAXI_DRIVER_MAPPING,
					'localField' => 'stateinfo.cityinfo.city_id',
					'foreignField' => 'mapping_countryid',
					'foreignField' => 'mapping_cityid',
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
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'mapping.mapping_companyid',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			array(
				'$unwind' => '$company'
			),
			
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'mapping.mapping_driverid',
					'foreignField' => '_id',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
			array(
				'$lookup' => array(
					'from' => MDB_DRIVER_INFO,
					'localField' => 'mapping.mapping_driverid',
					'foreignField' => '_id',
					'as' => 'driver'
				)
			),
			array(
				'$unwind' => '$driver'
			),
			array(
				'$match' => $match_query
			),
		);
		
		if ($find_count == TRUE) {
			$count_arguments = array(
				array(
					'$project' => array(
						'result' => '$people._id'
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
			$merge_arguments = array_merge($common_arguments, $count_arguments);
			$result          = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
			//echo "<pre>";print_r($result['result']);exit;
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
		} else {
			$field_arguments = array(
				array(
					'$sort' => array( 
						'people.created_date' => -1
					),
				),
				array(
					'$project' => array(
						'id' => '$people._id',
						'name' => '$people.name',
						'taxi_id' => '$taxi._id',
						'taxi_no' => '$taxi.taxi_no',
						'cid' => '$company._id',
						'company_name' => '$company.companydetails.company_name',
						'phone' => '$people.phone'
					)
				),
				array('$skip'	=> (int)$offset ),
				array('$limit'	=> (int)$val )
			);
			$merge_arguments = array_merge($common_arguments, $field_arguments);
			$result    = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
			//echo "<pre>";print_r($result['result']); exit;
			return (!empty($result['result'])) ? $result['result'] : array();
		}
    }  
	public function all_free_driver_list($offset, $val, $cid = 0,$find_count=false)
    {
        $usertype       = $this->usertype;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
		$match_query = $taxi_list = array();
		$match_query['user_type'] = 'D';
		$match_query['status'] = 'A';
		$match_query['availability_status'] = 'A';
		$booked_driver = $this->free_availabletaxi_list();
        if (count($booked_driver) > 0) {
            foreach ($booked_driver as $key => $value) {
                $taxi_list[] = (int)$value['id'];
            }
			$match_query['_id'] = array('$nin' => $taxi_list);
        }
        if (!empty($cid) && $cid!=0) {
			$match_query['company_id'] = (int)$cid;
        }
        if ($usertype == 'M') {
			$match_query['login_country'] = (int)$country_id;
			$match_query['login_state'] = (int)$state_id;
			$match_query['login_city'] = (int)$city_id;
        }
		//echo "<pre>"; print_r($match_query); exit;
		$common_arguments = array(
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
			array(
				'$match' => $match_query
			),
		);
		if($find_count == TRUE){
			$count_arguments = array(
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
			$arguments = array_merge($common_arguments,$count_arguments);
			$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
		}else{
			$field_arguments = array(
				array(
					'$project' => array(
						'id'=>'$_id',
						'user_createdby' => '$user_createdby',
						'name' => '$name',
						'username' => '$username',
						'email' => '$email',
						'address'=>'$address',
						'availability_status'=>'$availability_status',
						'company_name' => '$company.companydetails.company_name',
						'status'=>'$status',
						'driver_license_id'=>'$driver_license_id',
						'phone'=>'$phone',
						'cid'=>'$company.companydetails.userid',	
					)
				),
				array(
					'$sort' => array('_id' => 1)
				),
				array(
					'$skip' => $offset
				),
				array(
					'$limit' => (int)$val
				),
			);
			$arguments = array_merge($common_arguments,$field_arguments);
			$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
			for($i=0;$i<count($result['result']);$i++){				
				$result['result'][$i]['user_createdby'] = '--'/*isset($result['result'][$i]['user_createdby'])? $this->userNamebyId($result['result'][$i]['user_createdby']):"" */;
			}
			return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		}
	}
    public function get_unassign_driver_searchlist($find_count=false, $keyword = "", $offset = "", $val = "")
    {
        $usertype       = $this->usertype;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
		$match_query = $taxi_list = array();
		$match_query['user_type'] = 'D';
		$match_query['status'] = 'A';
		$match_query['availability_status'] = 'A';
		$keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
         if ($keyword) {
			$search = array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) ,
										array( 'company.companydetails.company_name' => Commonfunction::MongoRegex("/$keyword/i") )
										 ) );
			
			$match_query = array("\$and" => array($match_query, $search));
        }
		$booked_driver = $this->free_availabletaxi_list();
        if (count($booked_driver) > 0) {
            foreach ($booked_driver as $key => $value) {
                $taxi_list[] = (int)$value['id'];
            }
			$match_query['_id'] = array('$nin' => $taxi_list);
        }
        if (!empty($cid) && $cid!=0) {
			$match_query['company_id'] = (int)$cid;
        }
        if ($usertype == 'M') {
			$match_query['login_country'] = (int)$country_id;
			$match_query['login_state'] = (int)$state_id;
			$match_query['login_city'] = (int)$city_id;
        }
		//echo "<pre>"; print_r($match_query); exit;
		$common_arguments = array(
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
			array(
				'$match' => $match_query
			),
		);
		if($find_count == TRUE){
			$count_arguments = array(
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
			$arguments = array_merge($common_arguments,$count_arguments);
			$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
		}else{
			$field_arguments = array(
				array(
					'$project' => array(
						'id'=>'$_id',
						'user_createdby' => '$user_createdby',
						'name' => '$name',
						'username' => '$username',
						'email' => '$email',
						'address'=>'$address',
						'availability_status'=>'$availability_status',
						'company_name' => '$company.companydetails.company_name',
						'status'=>'$status',
						'driver_license_id'=>'$driver_license_id',
						'phone'=>'$phone',
						'cid'=>'$company.companydetails.userid',	
					)
				),
				array(
					'$sort' => array('_id' => 1)
				),
				array(
					'$skip' => $offset
				),
				array(
					'$limit' => (int)$val
				),
			);
			$arguments = array_merge($common_arguments,$field_arguments);
			$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
			for($i=0;$i<count($result['result']);$i++){				
				$result['result'][$i]['user_createdby'] = '--'/*isset($result['result'][$i]['user_createdby'])? $this->userNamebyId($result['result'][$i]['user_createdby']):"" */;
			}
			return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		}
    }
	public function current_package_details($cid)
    {
		$result = array();
		$current_time = Commonfunction::MongoDate(strtotime($this->currentdate));
		//$match = array('user_type'  => 'C','company_id' => (int)$cid, 'package_report.check_package_type' => 'T');
		$match = array('$and' => array(array('company_id'  => (int)$cid),array('user_type'  => 'C'),
		array('$or' => array(array('package_report.check_package_type' => 'T'),array('upgrade_expirydate' =>array('$gt' => $current_time))))));
		$args = array(
					array('$lookup' => array('from' => MDB_COMPANY,'localField' => 'company_id','foreignField' => '_id','as' => 'company')),
					array('$unwind' =>  array( 'path' =>  '$company', 'preserveNullAndEmptyArrays' =>  true ) ),
					/*array('$lookup' => array('from' => MDB_PACKAGE_REPORT,'localField' => 'company_id','foreignField' => 'upgrade_companyid','as' => 'package_report')),
					array('$unwind' =>  array( 'path' =>  '$package_report', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$lookup' => array('from' => MDB_PACKAGE,'localField' => 'package_report.upgrade_packageid',
					'foreignField' => '_id','as' => 'package')),
					array('$unwind' =>  array( 'path' =>  '$package', 'preserveNullAndEmptyArrays' =>  true ) ),*/
					array('$match' => $match),
					array('$project' => array(
									'package_id' => '$package_report.package_id',
									'upgrade_packageid' => '$package_report.upgrade_packageid', 
									/*'check_package_type' => '$package_report.check_package_type',
									'upgrade_expirydate' => '$package_report.upgrade_expirydate',*/
									'total_taxi' => '$package_report.upgrade_no_taxi', 
									'total_driver' => '$package_report.upgrade_no_driver',
									'package_name' => '$package.package_name', 
									'package_type' => '$package.package_type', 
									'total_taxi' => '$package_report.upgrade_no_taxi',
									'total_driver' => '$package_report.upgrade_no_driver', 
									'company_currency' => '$company.companyinfo.company_currency',
									'company_domain' => '$company.companyinfo.company_domain'))
				);
		$res = $this->mongo_db->aggregate(MDB_PEOPLE, $args);
		$result = array();
		if(!empty($res['result'])){
			foreach($res['result'] as $val){
				$arr = $val;
				$arr['company_domain'] = isset($res['result'][0]['company_domain'])?$res['result'][0]['company_domain']:"";
				//$arr['upgrade_expirydate'] = commonfunction::convertphpdate('Y-m-d H:i:s',$res['result'][0]['upgrade_expirydate']);
				$result[] = $arr;
			}
			
		}
		//echo '<pre>';print_r($result);exit;
		return $result;
    }
    public function company_info($cid)
	{
        $arguments = array(
            array('$match'	=> array('_id' => $cid)),
            array('$unwind'=>'$companyinfo'),
            array(
                '$project' => array(
					'company_domain'=>'$companyinfo.company_domain',
					'company_currency'=>'$companyinfo.company_currency_format',
                )
            )
        );
        $result = $this->mongo_db->aggregate(MDB_COMPANY,$arguments);
		//echo "<pre>"; print_r($result['result']); exit;
        return (!empty($result['result']))?$result['result']:array();
    }
    /*********************************************************************************************/
    //Function used to get all driver logs with transactions
    public function get_driver_completed_transaction($id, $msg_status, $driver_reply = null, $travel_status = null, $start = null, $limit = null, $current_date, $fromdate, $todate)
    {
		$result = $temp_arr = array();
        if ($current_date == 1) {
            $start_time =  date('Y-m-d') . ' 00:00:01';//'2014-05-06 00:00:01';
            $end_time   = date('Y-m-d') . ' 23:59:59';
        } else {
            $start_time = $fromdate;//'2014-05-06 00:00:01';
            $end_time   = $todate;
        }
		$match_query = array("\$and" => array(array("pickup_time"=>array('$gte' => Commonfunction::MongoDate(strtotime($start_time)),'$lte'=> Commonfunction::MongoDate(strtotime($end_time)))),array('driver_id' => (int)$id),array('travel_status' => (int)$travel_status),array('msg_status' => $msg_status),array('driver_reply' => $driver_reply) ));
		//print_r($match_query);//exit;
        $arguments = array(
            array('$match'	=> $match_query),
			array('$lookup' 		=> array(
                    'from'			=>	MDB_PASSENGERS,
                    'localField'	=> 'passengers_id',
                    'foreignField'	=> "_id",
                    'as'			=> "passengers"
                )
            ),
            //array('$unwind'=>'$passengers'),
            array('$unwind'=> array( 'path'=> '$passengers', 'preserveNullAndEmptyArrays'=> true ) ),
            array('$lookup' 		=> array(
                    'from'			=>	MDB_TRANSACTION,
                    'localField'	=> '_id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "trans"
                )
            ),
            //array('$unwind'=>'$trans'),
            array('$unwind'=> array( 'path'=> '$trans', 'preserveNullAndEmptyArrays'=> true ) ),
            array(
                '$project' => array(
					'passengers_log_id' => '$_id',
					'passengers_id'=>'$passengers_id',
					'driver_id'=>'$driver_id',
					'taxi_id'=>'$taxi_id',
					'company_id'=>'$company_id',
					'current_location'=>'$current_location',
					'pickup_latitude'=>'$pickup_latitude',
					'pickup_longitude'=>'$pickup_longitude',
					'drop_location'=>'$drop_location',
					'drop_latitude'=>'$drop_latitude',
					'drop_longitude'=>'$drop_longitude',
					'distance'=>'$distance',
					'approx_duration'=>'$approx_duration',
					'approx_fare'=>'$approx_fare',
					'pickup_time'=>'$pickup_time',
					'travel_status'=>'$travel_status',
					'driver_reply'=>'$driver_reply',
					'driver_comments'=>'$driver_comments',
					'fixedprice'=>'$fixedprice',
					'company_tax'=>'$company_tax',
					'faretype'=>'$faretype',
					'bookingtype'=>'$bookingtype',
					'luggage'=>'$luggage',
					'bookby'=>'$bookby',
					'operator_id'=>'$operator_id',
					'distance'=>'$trans.distance',
					'actual_distance'=>'$trans.actual_distance',
                    'fare' => '$trans.fare',
					'remarks' => '$trans.remarks',
					'payment_type' => '$trans.payment_type',
					'amt' => '$trans.amt',
					'distance_unit' => '$trans.distance_unit',
					'payment_status'=>'$trans.payment_status',
					'Taxamt'=>'$trans.company_tax',
					'name'=>'$passengers.name',					
                )
            ),
            array(
                '$skip' => 0
            ),
            array(
              '$limit' => (int)$start
            )
        );
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr['passengers_log_id'] = $r['_id'];
				$temp_arr['passengers_id'] = isset($r['passengers_id'])?$r['passengers_id']:'';
				$temp_arr['driver_id'] = $r['driver_id'];
				$temp_arr['taxi_id'] = $r['taxi_id'];
				$temp_arr['company_id'] = $r['company_id'];
				$temp_arr['current_location'] = $r['current_location'];
				$temp_arr['pickup_latitude'] = $r['pickup_latitude'];
				$temp_arr['pickup_longitude'] = $r['pickup_longitude'];
				$temp_arr['drop_location'] = $r['drop_location'];
				$temp_arr['drop_latitude'] = $r['drop_latitude'];
				$temp_arr['drop_longitude'] = $r['drop_longitude'];
				$temp_arr['distance'] = isset($r['distance']) ? $r['distance']:'';
				$temp_arr['approx_duration'] = isset($r['approx_duration']) ? $r['approx_duration']:'';
				$temp_arr['approx_fare'] = $r['approx_fare'];
				$temp_arr['pickup_time'] = isset($r['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
				$temp_arr['travel_status'] = $r['travel_status'];
				$temp_arr['driver_reply'] = $r['driver_reply'];
				$temp_arr['driver_comments'] = isset($r['driver_comments']) ? $r['driver_comments']:'';
				$temp_arr['fixedprice'] = isset($r['fixedprice'])?$r['fixedprice']:'';
				$temp_arr['company_tax'] = $r['company_tax'];
				$temp_arr['faretype'] = isset($r['faretype']) ? $r['faretype']:'';
				$temp_arr['bookingtype'] = $r['bookingtype'];
				$temp_arr['luggage'] = isset($r['luggage']) ? $r['luggage']:'';
				$temp_arr['bookby'] = $r['bookby'];
				$temp_arr['operator_id'] = isset($r['operator_id']) ? $r['operator_id']:'';
				$temp_arr['actual_distance'] = isset($r['actual_distance']) ? $r['actual_distance']:''; 
				$temp_arr['fare'] = isset($r['fare'])?$r['fare']:'';
				$temp_arr['remarks'] =isset($r['remarks']) ? $r['remarks']:''; 
				$temp_arr['payment_type'] = isset($r['payment_type'])?$r['payment_type']:'';
				$temp_arr['amt'] = isset($r['amt'])?$r['amt']:'';
				$temp_arr['distance_unit'] = isset($r['distance_unit']) ? $r['distance_unit']:'';
				$temp_arr['payment_status'] = isset($r['payment_status']) ? $r['payment_status']:'';
				$temp_arr['name'] = isset($r['name'])?$r['name']:'--';
				$temp_arr['Taxamt'] = isset($r['Taxamt'])?$r['Taxamt']:'';
				$result[] = (object)$temp_arr;
			}
		}
        //echo "<pre>"; print_r($result); exit;
        return $result;
    }
    /*********************************************************************************************/
    //Function used to Passenegr completed logs with transactions
    public function get_passenger_completed_transaction($id, $msg_status, $driver_reply = null, $travel_status = null, $limit = null, $offset = null, $current_date, $fromdate, $todate)
    {
		$result = $temp_arr = array();
        if($current_date != 1){
			$match_query =  array('passengers_id' => (int)$id,'msg_status' => $msg_status,'driver_reply'=>$driver_reply,'travel_status' => (int)$travel_status,'$and' => array(array('pickup_time' =>  array('$gte'=>Commonfunction::MongoDate(strtotime($fromdate))) ),array('pickup_time' =>  array('$lte'=>Commonfunction::MongoDate(strtotime($todate))) ) ) );
		}else{
			$match_query =  array('passengers_id' => (int)$id,'msg_status' => $msg_status,'driver_reply'=>$driver_reply,'travel_status' => (int)$travel_status);
		}
		//print_r($match_query); exit;
		$arguments = array(
			array('$lookup' 		=> array(
					'from'			=>	MDB_PASSENGERS,
					'localField'	=> 'passengers_id',
					'foreignField'	=> '_id',
					'as'			=> 'passenger'
				)
			),
			array('$unwind' => '$passenger'),
			array('$lookup' 		=> array(
					'from'			=>	MDB_TRANSACTION,
					'localField'	=> '_id',
					'foreignField'	=> 'passengers_log_id',
					'as'			=> 'transaction'
				)
			),
			array('$unwind' => '$transaction'),
			array('$match'	=> $match_query),
			array(
				'$project' => array(
					'passengers_log_id' => '$_id',
					'passenger_id' => '$passenger_id',
					'driver_id' => '$driver_id',
					'taxi_id' => '$taxi_id',
					'company_id' => '$company_id',
					'current_location'=>'$current_location',
					'pickup_latitude'=>'$pickup_latitude',
					'pickup_longitude'=>'$pickup_longitude',
					'drop_location'=>'$drop_location',
					'drop_latitude'=>'$drop_latitude',
					'drop_longitude'=>'$drop_longitude',
					'approx_distance'=>'$approx_distance',
					'approx_duration'=>'$approx_duration',
					'approx_fare'=>'$approx_fare',
					'pickup_time'=>'$pickup_time',
					'travel_status'=>'$travel_status',
					'driver_reply'=>'$driver_reply',
					'driver_comments'=>'$driver_comments',
					'fixedprice'=>'$fixedprice',
					'company_tax'=>'$company_tax',
					'faretype'=>'$faretype',
					'bookingtype'=>'$bookingtype',
					'luggage'=>'$luggage',
					'bookby'=>'$bookby',
					'operator_id'=>'$operator_id',
					'distance'=>'$transaction.distance',
					'distance_unit'=>'$transaction.distance_unit',
					'actual_distance'=>'$transaction.actual_distance',
					'fare'=>'$transaction.fare',
					'remarks'=>'$transaction.remarks',
					'payment_type'=>'$transaction.payment_type',
					'amt'=>'$transaction.amt',
					'payment_status'=>'$transaction.payment_status',
					'Taxamt'=>'$transaction.company_tax',
					'name' => '$passenger.name'
				)
			),
			array(
				'$sort' => array(
					'_id' => 1
				)
			),
			array('$skip' => (int)$offset),
			array('$limit' => (int)$limit)
		);
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		if(!empty($res['result'])){
			$i=0;
			foreach($res['result'] as $r){
				$temp_arr = $r;
				$temp_arr['pickup_time'] = commonfunction::convertphpdate('Y-m-d H:i:s', $r['pickup_time']);
				$temp_arr['distance_unit'] = isset($r['distance_unit']) ? $r['distance_unit'] : ''; 
				$result[$i] = (object)$temp_arr;
				$i++;
			}			
		}
		//echo '<pre>';print_r((object)$result);exit;
		return $result;
    }
	

    /*** Common Function for generating PDF *************/
    public function generate_pdf($html, $filename)
    {
        require_once(APPPATH . 'vendor/pdf/config/lang/eng.php');
        require_once(APPPATH . 'vendor/pdf/tcpdf.php');
        // create new PDF document
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        // set header and footer fonts
        $pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //set some language-dependent strings
        $pdf->setLanguageArray($l);
        // ---------------------------------------------------------
        // set font
        //$pdf->SetFont('helvetica', '', 10);        
        $pdf->AddPage();        
        //$pdf->SetFont('helvetica', '', 8);
        
        # To support arabic fonts
        $pdf->SetFont('ufontscom_aealarabiya', '', 10);
        
        $pdf->writeHTML($html, true, false, false, false, '');
        // add a page
        // output the HTML content
        // reset pointer to the last page
        //Close and output PDF document
        ob_end_clean();
        $pdf->Output($filename . '.pdf', 'D');
        exit;
    }
    /*** Common Function for Send PDF *************/
    public function send_pdf($html, $driver_name, $driver_email, $filepath)
    {
        require_once(APPPATH . 'vendor/pdf/config/lang/eng.php');
        require_once(APPPATH . 'vendor/pdf/tcpdf.php');
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        // set header and footer fonts
        $pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //set some language-dependent strings
        $pdf->setLanguageArray($l);
        // ---------------------------------------------------------
        // set font
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 8);
        $pdf->writeHTML($html, true, false, false, false, '');
        // add a page
        // output the HTML content
        // reset pointer to the last page
        //Close and output PDF document
        ob_end_clean();
        $pdf->Output($filepath . '.pdf', 'F');
        if (file_exists($filepath . '.pdf')) {
            return 1;
        } else {
            return 0;
        }
    }
    public function model_faredetails($model_id)
    {
        $company_id = $this->company_id;
		//$result = $this->mongo_db->find(MDB_COMPANY,array('_id'=>(int)$company_id, 'model_fare.model_id'=>(int)$model_id),array('model_fare.$.id'));
          ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        'model_fare.$.id'=>1,                       
                    ]
                ];
                $result = $this->mongo_db->find(MDB_COMPANY,array('_id'=>(int)$company_id, 'model_fare.model_id'=>(int)$model_id),$options);
		$res = $result;
		return (!empty($res) && isset($res[0]['model_fare']))?$res[0]['model_fare']:array();
			
    }
    public function count_faq_list()
    {
        $offset = $val = "";
        $this->all_faq_list($offset,$val,TRUE);
    }
    public function all_faq_list($offset, $val,$find_count=FALSE)
    {
		$match = array('status' => array('$ne'=>'N'));
		if($find_count == TRUE){
			$result = $this->mongo_db->count(MDB_PASSENGERS_FAQ,$match);
			return ($result > 0) ? $result : 0;
		}else{
			$args = array(array('$match' => $match),
						  array('$project' => array('faq_id'=>'$_id',
													'faq_title'=>'$faq_title',
													'faq_details'=>'$faq_details',
													'status'=>'$status')),
						  array('$sort' => array('_id'=>1)),
						  array('$skip' => (int)$offset),
						  array('$limit' => (int)$val)						  
						);
			$result = $this->mongo_db->Aggregate(MDB_PASSENGERS_FAQ,$args);
			return (isset($result['result'])) ? $result['result'] : array();
		}        
    }
    public function update_faq_status($status ='',$activeids)
    {
		if(count($activeids) >0){
			foreach($activeids as $a){
				$update = array('status' => $status);
				$result = $this->mongo_db->updateOne(MDB_PASSENGERS_FAQ,array('_id'=>(int)$a),array('$set'=>$update),array('upsert'=>true));		
			}
		}
    }
    
    public function count_searchfaq_list($keyword = "", $status = "", $offset = "", $val = "",$find_count=false)
    {
		$result = $this->get_all_faq_searchlist($keyword, $status, $offset, $val,$find_count=true);
		return $result;
        
    }
    public function get_all_faq_searchlist($keyword = "", $status = "", $offset = "", $val = "",$find_count=false)
    {
		if($offset == "" && $val == ""){
			$find_count = TRUE;
		}
        $keyword     = str_replace("%", "!%", $keyword);
        $keyword     = str_replace("_", "!_", $keyword);
        $or = array();
        $match =array('_id' => array('$ne' => 0)); 
        $match_query = array('$and' => array($match));
        if($status != '') {			
			$status_arr = array('status' => $status);
			$match_query['$and'][] = $status_arr;
		}
		if($keyword != '') { 
			$or = array("\$or" => array(
                            array(
                                'faq_title' => Commonfunction::MongoRegex("/$keyword/i")
                            ),
                            array(
                                'faq_details' => Commonfunction::MongoRegex("/$keyword/i")
                            )
                        )
					);
			$match_query['$and'][] = $or;
		}
		
		if($find_count){			
			$result = $this->mongo_db->count(MDB_PASSENGERS_FAQ,$match_query);
			//echo '<pre>';print_r($match_query);exit;
			return ($result>0) ? $result : 0;
 		}else{ 
			$args = array(array('$match' => $match_query),
						  array('$project' => array('faq_id'=>'$_id',
													'faq_title'=>'$faq_title',
													'faq_details'=>'$faq_details',
													'status'=>'$status')),
						  array('$sort' => array('faq_id'=>1)),
						  array('$skip' => (int)$offset),
						  array('$limit' => (int)$val)
					);
			$result = $this->mongo_db->Aggregate(MDB_PASSENGERS_FAQ,$args);
			
			return (isset($result['result'])) ? $result['result'] : array();
		}
    }
    
    public function active_faq_request($activeids)
    {
       $active_ids = Commonfunction::mongo_format_array($activeids);
 		$result = $this->mongo_db->updateMany(MDB_PASSENGERS_FAQ,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('status' => 'A')));
    }
    public function block_faq_request($activeids)
    {
        $active_ids = Commonfunction::mongo_format_array($activeids);
 		$result = $this->mongo_db->updateMany(MDB_PASSENGERS_FAQ,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('status' => 'D')));
    }
	public function trash_faq_request($activeids)
    {
		$active_ids = Commonfunction::mongo_format_array($activeids);
 		$result = $this->mongo_db->updateMany(MDB_PASSENGERS_FAQ,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('status' => 'T')));
 	
    }
    
    public function getpromocode()
    { 
            $promo_code = Commonfunction::randomkey_generator(6);
                ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,                       
                    ]
                ];
                $query = $this->mongo_db->find(MDB_PASSENGERS_PROMO,array('promocode' => $promo_code), $options);
		$promocode_result = $query;
        if (count($promocode_result) > 0) {
			$promo_code = Commonfunction::randomkey_generator(6);
        }
        return $promo_code;
    }
	
    public function getuserslist($company_id = 0)
    {
		
		if($_SESSION['user_type']!="C" && $_SESSION['user_type']!="M")
		{
			$demo_company = ""; //1-Demo. For testing purpose only
			$demo_cond    = "";
			if ($demo_company != "") {  $demo_cond = $demo_company;  }        
			if ($company_id != "") {  $demo_cond = $company_id;     }
			$match = array();
			$match['user_status'] = 'A';
			$match['activation_status'] = 1;
			if($demo_cond !='' && $demo_cond != 0){
				$match['passenger_cid'] = (int)$demo_cond;
			}
			$ops = array(
					array('$match' => $match),
					array(
						'$project' => array(
						'id' => '$_id',
						'name' => '$name',
						'salutation' => '$salutation',
						'email' => '$email'
						)
					)
				);
			$result = $this->mongo_db->aggregate(MDB_PASSENGERS,$ops);
			$list   = "";$emails = "";
			$count_result = count($result['result']);
			if( $count_result > 0){
				for($i=0;$i< $count_result;$i++){			
					$id         = $result['result'][$i]['id'];
					$name       = $result['result'][$i]['name'];
					$salutation = empty($result['result'][$i]['salutation']) ? '' : $result['result'][$i]['salutation'] . ' ';
					$email      = $result['result'][$i]['email'];
					$pname      = $salutation . $name;
					$emails .= '<option value="' . $id . '~' . $email . '~' . $pname . '">' . $email . '(' . $pname . ')' . '</option>';
				}
				$list = '<div class="new_input_field"><select name="to_user[]" id="to_user" class="required" multiple>'.$emails.'</select></div> ';
			} else {
				$list = __('No Passenger') . '<br><input type="hidden" id="to_user" class="promo_send_user" name="to_user" value="">';
			}
			//echo "<pre>";print_r($list);exit;
			return $list;     
		}
		else
		{	
			$arguments = array(
				array('$match'	=> array('company_id' => (int)$company_id)),
				array('$lookup' 		=> array(
						'from'			=>	MDB_PASSENGERS,
						'localField'	=> 'passengers_id',
						'foreignField'	=> '_id',
						'as'			=> 'passengers'
				)),
				array('$unwind'=>'$passengers'),
				array(
					'$project' => array(
						'id' => '$passengers._id',
						'name' => '$passengers.name',
						'email' => '$passengers.email',
						'phone' => '$passengers.phone',
						'address' => '$passengers.address',
						'created_date' => '$passengers.created_date',
						'user_status' => '$passengers.user_status'                    
				)),
				array('$group' => array('_id' => array('id' => '$id',
														'name' => '$name',
														'salutation' => '$salutation',
														'email' => '$email'
														)
					))
			);
			$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS ,$arguments);
			//echo "<pre>";print_r($result);exit;
			$list   = "";$emails = "";
			$count_result = count($result['result']);
			if( $count_result > 0){
				for($i=0;$i< $count_result;$i++){			
					$id         = $result['result'][$i]['_id']['id'];
					$name       = $result['result'][$i]['_id']['name'];
					$salutation = empty($result['result'][$i]['_id']['salutation']) ? '' : $result['result'][$i]['_id']['salutation'] . ' ';
					$email      = $result['result'][$i]['_id']['email'];
					$pname      = $salutation . $name;
					$emails .= '<option value="' . $id . '~' . $email . '~' . $pname . '">' . $email . '(' . $pname . ')' . '</option>';
				}
				$list = '<div class="new_input_field"><select name="to_user[]" id="to_user" class="required" multiple>'.$emails.'</select></div> ';
			} else {
				$list = __('No Passenger') . '<br><input type="hidden" id="to_user" class="promo_send_user" name="to_user" value="">';
			}
			//echo "<pre>";print_r($list);exit;
			return $list;     
		}   
    } 
    
    public function getpassengerscount($company_id = 0)
    {		
		$match_query = array();
		$match_query['user_status'] = "A";
		$match_query['activation_status'] = 1;
		if($company_id != 0){
			$match_query['passenger_cid'] = (int)$company_id;
		}
		//echo "<pre>"; print_r($match_query); exit;
		$arguments = array(array('$match'=> $match_query),
					//array('$unwind' => '$passengerdetails'),
					array('$project' => array('pid' => '$_id')),
					array('$group' =>array('_id' => NULL,
										   'count' => array('$sum' => 1))),
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS,$arguments);
		//echo "<pre>"; print_r($result['result']); exit;
		return (!empty($result['result']) && isset($result['result'][0]['count']))?$result['result'][0]['count']:0;
	
    }
    public function check_promo_exit($promo_code = "", $company_id = 0)
    {
		$match_query = array();
		$match_query['promocode'] = $promo_code;
		if($company_id != 0){
			$match_query['company_id'] = (int)$company_id;
		}
		$arguments = array(
			array('$match'=> $match_query),
			array('$project' => array('promocode' 	=> '$promocode', 'promo_discount' => '$promo_discount','promo_used' => '$promo_used')),
			array('$group' =>array('_id' => NULL,'count' => array('$sum' => 1))),
		);
		//echo "<pre>"; print_r($arguments); exit;	
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_PROMO,$arguments);
		//echo "<pre>"; print_r($result['result']); exit;
        return (isset($result['result'][0]['count']))?$result['result'][0]['count']:0;
    }
   
	public function count_promocode_list($search, $company_id = 0)
    {
		$count = $this->promocode_list('','',$search,$company_id,TRUE);
		return $count;
	}
	
	public function promocode_list($offset, $val, $search, $company_id = 0, $find_count = FALSE)
    {
		//echo $company_id;exit; 
		
		$match_query = $result = $temp_arr = array();
		$match_query['_id'] = array('$ne' => '');
		if($company_id != 0) {
			$match_query['company_id'] = (int)$company_id;
			$match_query['company.companydetails.company_status'] = 'A';
		}
		if(!empty($search))
		{
			$keyword       = str_replace("%", "!%", $search["keyword"]);
			$keyword       = str_replace("_", "!_", $search["keyword"]);
			if($search['keyword'] != "") {
				$match_query['promocode'] = Commonfunction::MongoRegex("/$keyword/i");
			}
			if (!empty($search['startdate']) && !empty($search['enddate'])) {
				$match_query['start_date'] = array('$gte' => Commonfunction::MongoDate(strtotime($search["startdate"])), '$lte' => Commonfunction::MongoDate(strtotime($search["enddate"])));
            }elseif(!empty($search['startdate'])) {
				$match_query['start_date'] = array('$gte' => Commonfunction::MongoDate(strtotime($search["startdate"])));
            }elseif(!empty($search['enddate'])) {
				$match_query['start_date'] = array('$lte' => Commonfunction::MongoDate(strtotime($search["enddate"])));
            }
			
			if (!empty($search['e_startdate']) && !empty($search['e_enddate'])) {
				$match_query['expire_date'] = array('$gte' => Commonfunction::MongoDate(strtotime($search["e_startdate"])), '$lte' => Commonfunction::MongoDate(strtotime($search["e_enddate"])));
            }elseif(!empty($search['e_startdate'])) {
				$match_query['expire_date'] = array('$gte' => Commonfunction::MongoDate(strtotime($search["e_startdate"])));
            }elseif(!empty($search['e_enddate'])) {
				$match_query['expire_date'] = array('$lte' => Commonfunction::MongoDate(strtotime($search["e_enddate"])));
            }
			
            if (!empty($search['company'])) {
				$match_query['company_id'] = (int)$search["company"];
            }elseif($company_id != 0){
				$match_query['company_id'] = (int)$company_id;
			}
		}else{
			if($company_id != 0){
				$match_query['company_id'] = (int)$company_id;
			}
		}
		$arguments = array(
					array(
						'$lookup' => array(
							'from'=>MDB_COMPANY,
							'localField'=> 'company_id',
							'foreignField' => "_id",
							'as'=> "company"
						)
					),
					//array('$unwind' => '$company'),
					array('$match'=> $match_query),
					array('$unwind' => '$_id'),
					array('$project' => array('promocode' 	=> 1,'promo_discount'=> 1,'passenger_id'	=> 1,'start_date'	=> 1,'expire_date'	=> 1,'promo_limit'	=> 1,'company_name'	=> '$company.companydetails.company_name')),
					
					array('$group' =>array('_id' => array('passenger_promoid'=> '$_id','promocode'=> '$promocode','promo_discount'=>'$promo_discount','passenger_id' => '$passenger_id','start_date'=>'$start_date','expire_date'=>'$expire_date','promo_limit'=>'$promo_limit','company_name'=>'$company_name'),'passenger_count' => array('$sum' => 1))),
					array( '$group' =>array('_id' => '$_id.promocode',
					'promodetails' =>array( '$first' =>array('passenger_id'=> '$_id.passenger_id','passenger_promoid'=> '$_id.passenger_promoid','promocode'=> '$_id.promocode','promo_discount'=>'$_id.promo_discount','start_date'=>'$_id.start_date','expire_date'=>'$_id.expire_date','promo_limit'=>'$_id.promo_limit','company_name'=>'$_id.company_name')),'count' => array('$sum' => '$passenger_count'))),
					array('$sort' => array('promodetails.passenger_promoid' => -1)),
				);
		if($find_count != true){
			$arguments[]['$skip'] = (int)$offset;
			$arguments[]['$limit'] = (int)$val;
			
		}
		$res = $this->mongo_db->aggregate(MDB_PASSENGERS_PROMO,$arguments);
		if(!empty($res['result'])){
			$i=0;
			foreach($res['result'] as $r){
				$temp_arr = $r['promodetails'];
				$temp_arr['passenger_id'] = (isset($temp_arr['passenger_id']))?implode(',',$temp_arr['passenger_id']):"";
				$temp_arr['start_date'] = commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['start_date']);			
				$temp_arr['expire_date'] = commonfunction::convertphpdate('Y-m-d H:i:s',$temp_arr['expire_date']);				
				$result[$i] = $temp_arr;
				$i++;
			}
		}
		//echo '<pre>';print_r($res);exit;
		return $result;
    }
	
  
   /* public function getactive_users($companyId = "")
    {	
		$match_query = array();
		$match_query['user_status'] = "A";
		$match_query['activation_status'] = 1;
		if($companyId != 0){
			$match_query['passenger_cid'] = (int)$companyId;
		}
		$arguments = array(
			array('$match'=> $match_query),
			array('$project' => array('salutation' 	=> '$salutation','name'=> '$name','email'	=> '$email')),
			array('$sort' => array('created_date' => -1)),
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS,$arguments);
		echo "<pre>"; print_r($companyId); exit;
		$all_plist = array();
        foreach ($result['result'] as $value) {
            $id          = $value['_id'];
            $name        = (isset($value['name']) && $value['name']!="")?$value['name']:"";
            $salutation  = (isset($value['salutation']) && $value['salutation']!="") ? $value['salutation'] : ' ';
            $email       = (isset($value['email']) && $value['email']!="")?$value['email']:"";
            $pname       = $salutation . $name;
            $list        = $id . '~' . $email . '~' . $pname;
            $all_plist[] = $list;
        }
        return $all_plist;
    }*/
	public function getactive_users($companyId = "")
    {
		$match_query = array();
		$match_query['passengers.user_status'] = "A";
		$match_query['passengers.activation_status'] = 1;
		if($companyId != 0){
			$match_query['company_id'] = (int)$companyId;
		}
		$arguments = array(
			array('$lookup' 		=> array(
					'from'			=>	MDB_PASSENGERS,
					'localField'	=> 'passengers_id',
					'foreignField'	=> '_id',
					'as'			=> 'passengers'
			)),
			array('$unwind'=>'$passengers'),
			array('$match'	=> $match_query),
			array('$project' => array(
					'id' => '$passengers._id',
					'name' => '$passengers.name',
					'email' => '$passengers.email'
			)),
			array('$group' => array('_id' => array('id' => '$id',
													'name' => '$name',
													'salutation' => '$salutation',
													'email' => '$email'
													)
				))
		);
		$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS ,$arguments);
		$all_plist = array();
        foreach ($result['result'] as $value) {
            $value          = $value['_id'];
            $id          = $value['id'];
            $name        = (isset($value['name']) && $value['name']!="")?$value['name']:"";
            $salutation  = (isset($value['salutation']) && $value['salutation']!="") ? $value['salutation'] : ' ';
            $email       = (isset($value['email']) && $value['email']!="")?$value['email']:"";
            $pname       = $salutation . $name;
            $list        = $id . '~' . $email . '~' . $pname;
            $all_plist[] = $list;
        }
        return $all_plist;
	}
	
    /**function to get company name **/
    public function getcompanydomainName($cid)
    {
		//$query = $this->mongo_db->find(MDB_COMPANY,array('_id' => (int)$cid),array('companyinfo.company_domain'));
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        'companyinfo.company_domain'=>1,                       
                    ]
                ];
                $query = $this->mongo_db->find(MDB_COMPANY,array('_id' => (int)$cid),$options);
		$result = $query;
		return (isset($result[0]['companyinfo']))?$result[0]['companyinfo']:"-";
    }
    /** Function to get driver's current status **/
    public function get_driver_current_status($driver_id, $company_id = '')
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
		$match = array();
		$match['driver_id'] = (int)$driver_id;
		$match['driver_reply'] = "A";
		$match['pickup_time'] = array('$gte'=> Commonfunction::MongoDate(strtotime($start_time)));
		$match['travel_status'] = array('$in' => array(9,5,3,2));
		if ($company_id != "" && $company_id != 0) {
			$match['company_id'] = (int)$company_id;
		}
		//$result = $this->mongo_db->find(MDB_PASSENGERS_LOGS,$match,array('_id','travel_status'));
                ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1, 
                        'travel_status'=>1
                    ]
                ];
                $result = $this->mongo_db->find(MDB_PASSENGERS_LOGS,$match,$options);
		$res = $result;
        return (!empty($res)?$res:array());
    }
    //** to check whether the taxi is assigned **//
    public function istaxiassigned($taxiIds)
    {
		//MongoDB
		//Here changing array values with string to integers values
		$active_ids = Commonfunction::mongo_format_array($taxiIds);
		$match_query = array('mapping.mapping_taxiid'=>array('$in'=>$active_ids),'mapping.mapping_status'=>'A');
		$ops = array(
			array(
					'$lookup' => array(
					'from'=>MDB_TAXI_DRIVER_MAPPING,
					'localField'=> "_id",
					'foreignField' => "mapping_taxiid",
					'as'=> "mapping"
					)
				),
			array('$unwind'=>'$mapping'),
			array('$match' => $match_query),
			array(
				'$project' => array('_id' => '$_id',
				)
			),
		);
		$result = $this->mongo_db->aggregate(MDB_TAXI,$ops);
		//echo '<pre>';print_r($result);exit;
		return (!empty($result['result']))? $result['result'] : array();
    }
    //** to check whether the taxi is assigned **//
    public function isdriverassigned($driverIds)
    {
		$driverIds = Commonfunction::mongo_format_array($driverIds);
		$match_query = array('mapping_driverid'=>array('$in'=>$driverIds),'mapping_status'=>'A');
		$ops = array(
			array(
					'$lookup' => array(
					'from'=>MDB_PEOPLE,
					'localField'=> "mapping_driverid",
					'foreignField' => "_id",
					'as'=> "people"
					)
				),
			array('$unwind'=>'$people'),
			array('$match' => $match_query),
			array('$project' => array('_id' => '$_id')),
		);
		$result = $this->mongo_db->aggregate(MDB_TAXI_DRIVER_MAPPING,$ops);
		//echo '<pre>'; print_r($result);exit;
		return !empty($result['result']) ? $result['result'] :array();
    }
    //** to get the assigned taxi details **//
    public function get_assigned_details($assignId)
    {
		//MongoDB
		$match_query = array('_id'=>(int)$assignId);
		$ops = array(
			array('$match' => $match_query),
			array('$project' => array('mapping_driverid' => '$mapping_driverid','mapping_taxiid' => '$mapping_taxiid','mapping_startdate' => '$mapping_startdate', 'mapping_enddate' => '$mapping_enddate')),
		);
		$result = $this->mongo_db->aggregate(MDB_TAXI_DRIVER_MAPPING,$ops);
		//echo '<pre>'; print_r($result);exit;
		return (!empty($result['result']))?$result['result']:0;
    }
	
	public function check_already_assigned($driver_id, $taxi_id, $startdate, $enddate)
    {
		$match_query = array();
		$match_query['mapping.mapping_status'] = 'A';
		if ($driver_id!="") {
			$match_query['mapping.mapping_driverid'] = (int)$driver_id;
		}
		if ($taxi_id!="") {
			$match_query['mapping.mapping_taxiid'] = (int)$taxi_id;
		}
		if ($startdate!="" && $enddate!="") {
			$match_query['mapping.mapping_startdate'] = array('$gte' => $startdate);
			$match_query['mapping.mapping_enddate'] = array('$lte' => $enddate);
		}else{
			if ($startdate!="") {
				$match_query['mapping.mapping_startdate'] = array('$gte' => $startdate);
				$match_query['mapping.mapping_enddate'] = array('$lte' => $startdate);
			}
			if ($enddate!="") {
				$match_query['mapping.mapping_startdate'] = array('$gte' => $enddate);
				$match_query['mapping.mapping_enddate'] = array('$lte' => $enddate);
			}
		}	
		//echo "<pre>"; print_r($match_query); exit;
		$arguments = array(
			array(
				'$unwind' => '$stateinfo'
			),
			array(
				'$unwind' => '$stateinfo.cityinfo'
			),
			array(
				'$lookup' => array(
					'from' => MDB_TAXI_DRIVER_MAPPING,
					'localField' => 'stateinfo.cityinfo.city_id',
					'foreignField' => 'mapping_countryid',
					'foreignField' => 'mapping_cityid',
					'as' => 'mapping'
				)
			),
			array(
				'$unwind' => '$mapping'
			),
		
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'mapping.mapping_companyid',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			array(
				'$unwind' => '$company'
			),
			array(
				'$match' => $match_query
			),
			array(
				'$project' => array(
					'result' => '$mapping._id'
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
		$result    = $this->mongo_db->aggregate(MDB_CSC, $arguments);
		return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
    }
	
	public function check_driver_have_taxi($mapId, $driver_id, $startdate, $enddate)
    {
		$match_query = array();
		$match_query['mapping._id'] = array('$ne' => $mapId);
		if ($driver_id!="") {
			$match_query['mapping.mapping_driverid'] = (int)$driver_id;
		}
		if ($startdate!="" && $enddate!="") {
			$match_query['mapping.mapping_startdate'] = array('$gte' => $startdate);
			$match_query['mapping.mapping_enddate'] = array('$lte' => $enddate);
		}else{
			if ($startdate!="") {
				$match_query['mapping.mapping_startdate'] = array('$gte' => $startdate);
				$match_query['mapping.mapping_enddate'] = array('$lte' => $startdate);
			}
			if ($enddate!="") {
				$match_query['mapping.mapping_startdate'] = array('$gte' => $enddate);
				$match_query['mapping.mapping_enddate'] = array('$lte' => $enddate);
			}
		}	
		//echo "<pre>"; print_r($match_query); exit;
		$arguments = array(
			array(
				'$unwind' => '$stateinfo'
			),
			array(
				'$unwind' => '$stateinfo.cityinfo'
			),
			array(
				'$lookup' => array(
					'from' => MDB_TAXI_DRIVER_MAPPING,
					'localField' => 'stateinfo.cityinfo.city_id',
					'foreignField' => 'mapping_countryid',
					'foreignField' => 'mapping_cityid',
					'as' => 'mapping'
				)
			),
			array(
				'$unwind' => '$mapping'
			),
		
			array(
				'$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => 'mapping.mapping_companyid',
					'foreignField' => '_id',
					'as' => 'company'
				)
			),
			array(
				'$unwind' => '$company'
			),
			array(
				'$match' => $match_query
			),
			array(
				'$project' => array(
					'result' => '$mapping._id'
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
		$result    = $this->mongo_db->aggregate(MDB_CSC, $arguments);
		return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
    }
    /************* Dashboard All Driver status***************/
	/** get wallet requested list **/
	public function getWalletAmtRequests($searchArr, $companyId, $getCnt = 0, $offset = '', $limit = '')
	{
		$match = $result = $temp_arr=array();
		$keyword = (isset($searchArr['keyword'])) ? $searchArr['keyword'] : '';
		$company = (isset($searchArr['company'])) ? $searchArr['company'] : '';
		$startdate = (isset($searchArr['startdate'])) ? $searchArr['startdate'] : '';
		$enddate = (isset($searchArr['enddate'])) ? $searchArr['enddate'] : '';
		if(!empty($company)){
			$match['people.company_id'] = (int)$company;
		}else{
			$match['_id'] = array('$gt' => 0);
		}
		
		if(!empty($startdate) && !empty($enddate)){
			$match['wallet_request_date'] = array('$gte' => Commonfunction::MongoDate(strtotime($startdate)),
												  '$lte' => Commonfunction::MongoDate(strtotime($enddate)));
		} else if(!empty($startdate)){
			$match['wallet_request_date'] = array('$gte' => Commonfunction::MongoDate(strtotime($startdate)));
		} else if(!empty($enddate)) {
			$match['wallet_request_date'] = array('$lte' => Commonfunction::MongoDate(strtotime($enddate)));
		}
		if(!empty($keyword)){
			$search = array('$or' => array(array( 'people.name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'people.lastname' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'people.phone' => Commonfunction::MongoRegex("/$keyword/i") )));
			$match1 = array();
			foreach($match as $k => $v){
				$match1[] = array($k => $v);
			}
			$and_arr = array_merge($match1,array($search));
			$match_arr = array('$and' => $and_arr);
		}else{
			$match_arr = $match;
		}
		//echo "<pre>"; print_r($match_arr); exit;
		$project = array('wallet_request_id' => '$_id',
						 'wallet_request_amount' => '$wallet_request_amount',
						 'request_status' => '$wallet_request_status',
						 'wallet_request_status' => '$wallet_request_status',
						 'reqstatuslbl' => array('$concat' =>array(
								array('$cond' => array(array('$eq' => array('$wallet_request_status',2)),'Approved','')),
								array('$cond' => array(array('$eq' => array('$wallet_request_status',3)),'Reject','')),
								array('$cond' => array(array('$lt' => array('$wallet_request_status',2)),'Pending','')),
							)),
						 'wallet_request_date' => '$wallet_request_date',
						 'approved_date' => '$wallet_request_approved_date',
						 //'wallet_request_date' => array('$dateToString' => array('format' => '%Y-%m-%d %H:%M:%S','date' => '$wallet_request_date')),
						 //'approved_date' => array('$dateToString' => array('format' => '%Y-%m-%d %H:%M:%S','date' => '$wallet_request_approved_date')),
						 'driverName' =>'$people.name',
						 'driver_id' => '$wallet_request_driver',
						 //'driverPhone' => array('$concat' =>array('$people.country_code','$people.phone')),
						 'driverPhone' =>  '$people.phone',
						 'company_name' => '$company.companydetails.company_name'
						);		
					$args = array(array('$lookup' => array('from'=>MDB_PEOPLE,
											  'localField'=> "wallet_request_driver",
											  'foreignField' => "_id",
											  'as'=> "people")),
					  array('$unwind' => '$people'),
					  array('$lookup' => array('from'=>MDB_COMPANY,
											  'localField'=> 'people.company_id',
											  'foreignField' => '_id',
											  'as'=> 'company')),
					  array('$unwind' => '$company'),
					  array('$unwind' => '$company.companydetails'),
					  array('$match' => $match_arr),
					  array('$project' => $project)
				  );
		$page_arr = array();
		if($getCnt == 0){
			$page_arr = array(array('$skip'=> (int)$offset),
						  array('$limit'=> (int)$limit));			
		}
		$arguments = array_merge($args,$page_arr);
		$res = $this->mongo_db->aggregate(MDB_DRIVER_WALLET_REQ,$arguments);
		//echo '<pre>';print_r($res);exit;
                if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr['wallet_request_id'] = $r['wallet_request_id'];
				$temp_arr['wallet_request_amount'] = $r['wallet_request_amount'];
				$temp_arr['reqstatuslbl'] = $r['reqstatuslbl'];
				$temp_arr['wallet_request_date'] = isset($r['wallet_request_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['wallet_request_date']):'';
				$temp_arr['approved_date'] = isset($r['approved_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['approved_date']):'';
				$temp_arr['wallet_request_status'] = $r['wallet_request_status'];
                                $temp_arr['driverName'] =   $r['driverName'];
				$temp_arr['driver_id'] =    $r['driver_id'];						
				$temp_arr['driverPhone'] =  $r['driverPhone'];
                                $temp_arr['company_name']= $r['company_name'];
				$result[] = $temp_arr;
			}
		}
		/*if(!empty($res['result'])){
			$result = $res['result'];
		}*/
		return $result;
	}
	/** update the wallet request status **/
	/*public function updateRequestStatus($activeids,$reqSts,$userId)
	{
		
		if(count($activeids)>0){
			foreach($activeids as $a){
				$match = array('_id' => (int)$a);
				$update_arr = array('wallet_request_status' => (int)$reqSts,
									'wallet_request_approved_date' => Commonfunction::MongoDate(strtotime($this->currentdate)),
									'wallet_request_approved_by' => (int)$userId);
				$result = $this->mongo_db->updateOne(MDB_DRIVER_WALLET_REQ,$match,array('$set' => $update_arr),array('upsert'=>true));	
			}
		}
	} */
	
	/** update the wallet request status **/
	public function updateRequestStatus($activeids,$reqSts,$userId)
	{
		if($reqSts == 3) {
			if(count($activeids)>0){
				foreach($activeids as $a){
					$match = array('_id' => (int)$a);
					$update_arr = array('wallet_request_status' => (int)$reqSts);
					$result = $this->mongo_db->updateOne(MDB_DRIVER_WALLET_REQ,$match,array('$set' => $update_arr),array('upsert'=>true));	
				}
			}		
			//Here changing array values with string to integers values
			$active_ids = Commonfunction::mongo_format_array($activeids);
			//$result = $this->mongo_db->find(MDB_DRIVER_WALLET_REQ,array('_id'=>array('$in'=>$active_ids)),array('wallet_request_driver','wallet_request_amount'));
                        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[
                            'projection'=>[
                                'wallet_request_driver'=>1,
                                'wallet_request_amount'=>1
                            ]
                        ];
                        $result = $this->mongo_db->find(MDB_DRIVER_WALLET_REQ,array('_id'=>array('$in'=>$active_ids)),$options);
			$result = (!empty($result))?$result:array();
			if(count($result)>0){
				foreach($result as $r) {
					$wallet_request_amount = $r['wallet_request_amount'];
					//$reff_result = $this->mongo_db->find(MDB_DRIVER_REF,array('registered_driver_id'=>(int)$r['wallet_request_driver']),array('_id','registered_driver_wallet'));
					 ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
					$options=[
						'projection'=>[
							'_id'=>1,
							'registered_driver_wallet'=>1
						]
					];
					$reff_result = $this->mongo_db->find(MDB_DRIVER_REF,array('registered_driver_id'=>(int)$r['wallet_request_driver']),$options);
					$reff_result = (!empty($reff_result))?$reff_result:array();
					if(count($reff_result)>1){
						foreach($reff_result as $rr) {
							//echo "<pre>"; print_r($rr); exit;
							$wallet_request_amount = $rr['registered_driver_wallet']+$wallet_request_amount;
							$match = array('_id' => (int)$rr['_id']);
							$update_arr = array('registered_driver_wallet' => $wallet_request_amount);
							$result = $this->mongo_db->updateOne(MDB_DRIVER_REF,$match,array('$set' => $update_arr),array('upsert'=>true));
						}
					}
				}
			}
		} else {
			if(count($activeids)>0){
				foreach($activeids as $a){
					$match = array('_id' => (int)$a);
					$update_arr = array('wallet_request_status' => (int)$reqSts,
										'wallet_request_approved_date' => Commonfunction::MongoDate(strtotime($this->currentdate)),
										'wallet_request_approved_by' => (int)$userId);
					$result = $this->mongo_db->updateOne(MDB_DRIVER_WALLET_REQ,$match,array('$set' => $update_arr),array('upsert'=>false));	
					
					$match1 = array('registered_driver_id' => (int)$a);
					$update_arr1 = array('registered_driver_wallet' => 0);
					$result = $this->mongo_db->updateOne(MDB_DRIVER_REF,$match1,array('$set' => $update_arr1),array('upsert'=>false));
				}
				
				# update amount to 0
				$active_ids = Commonfunction::mongo_format_array($activeids);
				$options=[
					'projection'=>[
						'wallet_request_driver'=>1
					]
				];
				$result = $this->mongo_db->find(MDB_DRIVER_WALLET_REQ,array('_id'=>array('$in'=> $active_ids)),$options);
				$result = (!empty($result))?$result:array();
				if(count($result)>0){
					foreach($result as $r) {
						$match1 = array('registered_driver_id' => (int)$r['wallet_request_driver']);
						$update_arr1 = array('registered_driver_wallet' => 0);
						$result = $this->mongo_db->updateOne(MDB_DRIVER_REF,$match1,array('$set' => $update_arr1),array('upsert'=>false));
					}
				}
			}
		}
		return $result;
	}
	
	/** get wallet requests for a particular driver **/
	public function getDriverWithdrawRequests($driverId)
	{
		$result = $temp_arr = array();
		$args = array(
					array('$match' => array('wallet_request_driver' => (int)$driverId)),
					array('$project' =>
						array('wallet_request_id' => '$_id',
							   'wallet_request_amount' => '$wallet_request_amount',
							   'statuslbl' => array('$concat' =>array(
									  array('$cond' => array(array('$eq' => array('$wallet_request_status',2)),'Approved','')),
									  array('$cond' => array(array('$eq' => array('$wallet_request_status',3)),'Reject','')),
									  array('$cond' => array(array('$lt' => array('$wallet_request_status',2)),'Pending','')),
								  )),
							   'request_date' => '$wallet_request_date',
							   'approved_date' => '$wallet_request_approved_date',
							   'request_status' =>'$wallet_request_status'))
				   );
		$res = $this->mongo_db->Aggregate(MDB_DRIVER_WALLET_REQ,$args);
		//echo '<pre>';print_r($res);exit;
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr['wallet_request_id'] = $r['wallet_request_id'];
				$temp_arr['wallet_request_amount'] = $r['wallet_request_amount'];
				$temp_arr['statuslbl'] = $r['statuslbl'];
				$temp_arr['request_date'] = isset($r['request_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['request_date']):'';
				$temp_arr['approved_date'] = isset($r['approved_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['approved_date']):'';
				$temp_arr['request_status'] = $r['request_status'];
				$result[] = $temp_arr;
			}
		}
		//echo '<pre>';print_r($result);exit;
		return $result;
	}
	
	public function getWithdrawRequests($searchArr, $companyId, $getCnt = 0, $driver_type, $offset = '', $limit = '')
	{
		$company_id = $_SESSION['company_id'];
		$user_type = $_SESSION['user_type'];
		$keyword = (isset($searchArr['keyword'])) ? $searchArr['keyword'] : '';
		$company = (isset($searchArr['company'])) ? $searchArr['company'] : '';
		$brand_type = (isset($searchArr['brand_type'])) ? $searchArr['brand_type'] : '';
		$status = (isset($searchArr['status'])) ? $searchArr['status'] : '';
		$startdate = (isset($searchArr['startdate']) && $searchArr['startdate'] != "") ? Commonfunction::ensureDatabaseFormat($searchArr['startdate'],1) : date('Y-m-d 00:00:00', strtotime('-7 days'));
		$enddate = (isset($searchArr['enddate']) && $searchArr['enddate'] != "") ? Commonfunction::ensureDatabaseFormat($searchArr['enddate'],2) : convert_timezone('now',$_SESSION['timezone']);

		if($brand_type != ""){
			$match['brand_type'] = (int)$brand_type;
		}

		$match['type'] = ($driver_type == 1) ? 0 : 1;
		//$match['type'] = array('$in' => array(0,1));

		if($user_type == 'C') {
			$match['company_id'] = (int)$company_id;
		}

		if(!empty($startdate) && !empty($enddate)) {
			$match['request_date'] = array('$gte' => Commonfunction::MongoDate(strtotime($startdate)),'$lte' => Commonfunction::MongoDate(strtotime($enddate)));
		} else if(!empty($startdate)){
			$match['request_date'] = array('$gte' => Commonfunction::MongoDate(strtotime($startdate)));
		} else if(!empty($enddate)) {
			$match['request_date'] = array('$lte' => Commonfunction::MongoDate(strtotime($enddate)));
		}
		
		if($status != "") {
			$match['request_status'] = (int)$status;
		}
		
		$result = $keyword_match = array();
		if(!empty($keyword)) {
			$keyword_match = array( '$or' =>  array(
								array('people.name' =>  array('$regex' => $keyword,'$options' => '$i')),
								array('people.lastname' =>  array('$regex' => $keyword,'$options' => '$i')),
								array('people.phone' =>  array('$regex' => $keyword,'$options' => '$i')),  
								array('request_id' =>  array('$regex' => $keyword,'$options' => '$i')),
								array('pass.phone' =>  array('$regex' => $keyword,'$options' => '$i')),  
								array('_id' =>  array('$regex' => $keyword,'$options' => '$i')),  
						));
		}
		
		if(!empty($keyword_match)) {
			$match_args = array('$and' => array($match, $keyword_match));
		}else{
			$match_args = $match;
		}
		//echo "<pre>"; print_r($match_args); exit;
		$args = array(array('$lookup' => array('from' => MDB_PEOPLE,
									'localField' => 'requester_id',
									'foreignField' => '_id',
									'as' => 'people')),
					array('$unwind'=> array( 'path'=> '$people', 'preserveNullAndEmptyArrays'=> true ) ),
					array('$match' => $match_args),
					array('$project' => array('_id' => 0,'withdraw_request_id' => '$_id',
							'request_id' => '$request_id','brand_type' => '$brand_type',
							'withdraw_amount' => '$withdraw_amount','request_date' => '$request_date',
							'request_status' => '$request_status',
							'name' => '$people.name',
							'lastname' => '$people.lastname'
							)),
						array('$sort' => array('withdraw_request_id' => -1))
					);
		
		if($getCnt != 1) {
			$args[]['$skip'] = (int)$offset;
			$args[]['$limit'] = (int)$limit;
		}			
		
		$res = $this->mongo_db->aggregate(MDB_WITHDRAW_REQUEST,$args);	
		if(!empty($res['result'])){			
			$i=0;
			foreach($res['result'] as $r){					
				
				$result[] = $r;				
				$result[$i]['request_date'] = commonfunction::convertphpdate('Y-m-d h:i:s',$r['request_date']);
				$result[$i]['request_status'] = isset($r['request_status'])?$r['request_status']:'';
				$result[$i]['name'] = isset($r['name'])?$r['name']:'';
				$result[$i]['lastname'] = isset($r['lastname'])?$r['lastname']:'';
				$i++;
			}
		}
		//~ echo '<pre>';print_r($result);exit;
		return (!empty($result)) ? $result : array();
	}
	
	public function getDashboardWithdrawRequests($companyId,$driver_type)
	{
		$result = array();
		$match['type'] = ($driver_type) ? 0 : 1;
		if($companyId > 0) {
			$match['company_id'] = (int)$companyId;
		}
		$args = array(	
			array('$match' => $match),
			array('$project' => array(
				'request_status' => '$request_status',
				'withdraw_amount' => '$withdraw_amount',
				'approved_withdraw_request_count' => array('$cond' => array('if' => array('$eq' => array('$request_status', 1)),'then' =>  1, 'else' => null)),
				'deny_withdraw_request_count' => array('$cond' => array('if' => array('$eq' => array('$request_status', 2)),'then' =>  1, 'else' => null)),
				'single_brand_type' => array('$cond' => array('if' => array('$eq' => array('$brand_type', 2)),'then' =>  '$withdraw_amount', 'else' => null)),
				'multy_brand_type' => array('$cond' => array('if' => array('$eq' => array('$brand_type', 1)),'then' =>  '$withdraw_amount', 'else' => null)),
				'payment_transaction_deneid' => array('$cond' =>  array( 'if' =>  array('$eq' =>  array( '$request_status', 2)), 'then' =>  '$withdraw_amount', 'else' => null)),
				'payment_transaction_pending' => array('$cond' =>  array( 'if' =>  array('$eq' =>  array( '$request_status', 0)), 'then' =>  '$withdraw_amount', 'else' => null)) 
			)),
			array('$group' => array('_id' => null,
						'total_withdraw_request_count' => array('$sum' => 1),
						'approved_withdraw_request_count' =>  array( '$sum' =>  '$approved_withdraw_request_count' ), 
						'deny_withdraw_request_count' =>  array( '$sum' =>  '$deny_withdraw_request_count' ), 
						'payment_transaction' =>  array( '$sum' =>  '$withdraw_amount' ), 
						'payment_transaction_single' =>  array('$sum' => '$single_brand_type'),
						'payment_transaction_multy' => array('$sum' => '$multy_brand_type'),
						'payment_transaction_deneid' => array('$sum' => '$payment_transaction_deneid'),
						'payment_transaction_pending' => array('$sum' => '$payment_transaction_pending')
			))
		);
		$decimal = array('payment_transaction','payment_transaction_single','payment_transaction_multy','payment_transaction_deneid','payment_transaction_pending', 'total_withdraw_request_count');
		$res = $this->mongo_db->aggregate(MDB_WITHDRAW_REQUEST,$args);
		//echo "<pre>"; print_r($res); exit; 
		if(!empty($res['result'])){
			$result = $res['result'];	
			$result[0]['payment_transaction'] = number_format($result[0]['payment_transaction'], 2, '.', '');
			$result[0]['payment_transaction_single'] = number_format($result[0]['payment_transaction_single'], 2, '.', '');
			$result[0]['payment_transaction_multy'] = number_format($result[0]['payment_transaction_multy'], 2, '.', '');
			$result[0]['payment_transaction_deneid'] = number_format($result[0]['payment_transaction_deneid'], 2, '.', '');
			$result[0]['payment_transaction_pending'] = number_format($result[0]['payment_transaction_pending'], 2, '.', '');
			$result[0]['total_withdraw_request_count'] = $result[0]['total_withdraw_request_count'];
			$result[0]['approved_withdraw_request_count'] = $result[0]['approved_withdraw_request_count'];
			$result[0]['deny_withdraw_request_count'] = $result[0]['deny_withdraw_request_count'];
			
		}
		else
		{
			$result[0]['payment_transaction']= "";
			$result[0]['payment_transaction_single']= "";
			$result[0]['payment_transaction_multy']= "";
			$result[0]['payment_transaction_deneid']= "";
			$result[0]['payment_transaction_pending']= "";
			$result[0]['total_withdraw_request_count']= "";
			$result[0]['approved_withdraw_request_count']= "";
			$result[0]['deny_withdraw_request_count']= "";
		}
		
		return $result;		
	}
	
	public function get_withdraw_payment_mode()
	{
		$args = array(
					array('$match' => array('payment_mode_status'=>'A')),
					array('$project' =>array('_id'=>0,'withdraw_payment_mode_id' => '$_id',
											'payment_mode_name' => '$payment_mode_name')),
					array('$sort'=>array('withdraw_payment_mode_id'=>-1))
				);
		$res = $this->mongo_db->Aggregate(MDB_WITHDRAW_PAYMENT_MODE,$args);
		//echo '<pre>';print_r($res);exit;
		return !empty($res['result']) ? $res['result']: array();
	}
	
	public function get_withdraw_deatil($id)
	{
		$result = array();
		$args = array(					
					array('$lookup' => array('from' => MDB_PEOPLE,'localField' => 'requester_id','foreignField' => '_id','as' => 'people')),
					array('$unwind' =>  array( 'path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$match' => array('_id'  => (int)$id)),
					array('$project' => array(
						'_id' => 0,
						'withdraw_request_id' => '$_id',
						'request_id' => '$request_id',
						'brand_type' => '$brand_type',
						'withdraw_amount' => '$withdraw_amount',
						'request_date' => '$request_date',
						'request_status' => '$request_status',
						'name' => '$people.name',
						'lastname' => '$people.lastname'
					))
				);
		$res = $this->mongo_db->Aggregate(MDB_WITHDRAW_REQUEST,$args);
		if(!empty($res['result'])){			
			foreach($res['result'] as $r){					
				$result[] = $r;
				$result[0]['request_date'] = commonfunction::convertphpdate('Y-m-d h:i:s',$r['request_date']);
				$result[0]['name'] = isset($r['name']) ? $r['name'] :'' ;
				$result[0]['lastname'] = isset($r['lastname']) ? $r['lastname'] :'' ;
			}
		}
		//echo '<pre>';print_r($result);exit;
		return $result;
	}
	
	public function get_withdraw_log($id)
	{
		$result = array();
		$args = array(					
					array('$match' => array('_id'  => (int)$id)),
					array('$sort' => array('_id' => -1)),
					array('$project' => array(
						'log_id' => '$_id',
						'payment_mode' => '$withdraw_request_log.payment_mode',
						'transaction_id' => '$withdraw_request_log.transaction_id',
						'comments' => '$withdraw_request_log.comments',
						'file_name' => '$withdraw_request_log.file_name',
						'created_date' => '$withdraw_request_log.createdate',
						'status' => '$withdraw_request_log.request_status',
						'payment_mode_name' => '$withdraw_request_log.payment_mode'
						))
				);
		$res = $this->mongo_db->Aggregate(MDB_WITHDRAW_REQUEST,$args);
		//echo '<pre>';print_r($res['result']);exit;
		$data=array();
		if(!empty($res['result']) && !empty($res['result'][0]['log_id'])){
			$i=0;			
			foreach($res['result'] as $r){	//echo "<pre>"; print_r($r); exit;	
				$result = $r;			
				$result['log_id'] = !empty($r['log_id'][0]) ? $r['log_id'][0] : '';
				$result['payment_mode'] = !empty($r['payment_mode'][0]) ? $r['payment_mode'][0] : '';
				$result['transaction_id'] = !empty($r['transaction_id'][0]) ? $r['transaction_id'][0] : '';
				$result['comments'] = !empty($r['comments'][0]) ? $r['comments'][0] : '';
				$result['status'] = !empty($r['status'][0]) ? $r['status'][0] : '';
				$result['file_name'] = !empty($r['file_name'][0]) ? $r['file_name'][0] : '';
				$result['created_date'] = !empty($r['created_date'][0]) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['created_date'][0]) : '';
				$result['payment_mode_name'] = !empty($r['payment_mode_name'][0]) ? $r['payment_mode_name'][0] : '';
				$data[] = $result;
			}
		}
		//echo '<pre>';print_r($data);exit;
		return $data;
	}
        
	public function get_people_list(){
		
		$peoples =array();
		//$people_list = $this->mongo_db->Find(MDB_PEOPLE,array(),array('_id','name'));
                ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,
                        'name'=>1                       
                    ]
                ];
                $people_list = $this->mongo_db->find(MDB_PEOPLE,[],$options);
		if(!empty($people_list)){
			foreach($people_list as $p){
				if(isset($p['name'])) { 
					$peoples[$p['_id']] = $p['name'];
				}
			}
		}
		return $peoples;
	}
	
	public function findcompany_currency($company_cid)
	{
		$currency_arr[] = array('currency_code' =>CURRENCY_FORMAT, 'currency_symbol' =>CURRENCY);
		
		/*$rs = $this->mongo_db->findOne(MDB_PAYMENT_GATEWAYS,array('default_payment_gateway'=>1,'company_id'=>(int)$company_cid),array('currency_code','currency_symbol'));
		if(!empty($rs)){
			unset($currency_arr);
			$currency_arr[] = $rs;
		}*/
		return $currency_arr;
	}
	
	public function count_searchcompany_list($keyword = "", $status = "",$company = ""){
		
		$keyword = str_replace("%","!%",$keyword);
		$keyword = str_replace("_","!_",$keyword);
		
		$match = array('people.user_type' => 'C');
		$and = $or = array();
		if($company != ''){
			$and['_id'] = (int)$company;
		}		
		if($status != ''){
			$and['companydetails.company_status'] = $status;
		}
		$match = !empty($and) ? array('$and' => array($match, $and)) : $match;
		if($keyword != ''){
			$or = array('$or' => array(array('people.name' => array('$regex' => $keyword,'$options' => '$i')),
										array('people.lastname' => array('$regex' => $keyword,'$options' => '$i')),
										array('people.email' => array('$regex' => $keyword,'$options' => '$i')),
										array('companydetails.company_name' => array('$regex' => $keyword,'$options' => '$i')),
										array('people.name' => array('$regex' => $keyword,'$options' => '$i'))));
			$match = array('$and' => array($match, $or));
		}
		$args = array(				
					array('$lookup' => array('from' => MDB_PEOPLE,'localField' => 'companydetails.userid','foreignField' => '_id','as' => 'people')),
					array('$unwind' =>  array('path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true)),
					array('$match' => $match),
					array('$sort' => array('people.created_date' => -1)),
					array('$group' =>  array('_id' => null, 'count' => array('$sum' =>  1 ) ) )
				);	
		//echo '<pre>';print_r($args);exit;
		$result = $this->mongo_db->Aggregate(MDB_COMPANY, $args);
		return !empty($result['result']) ? $result['result']: array();
	}
	
	public function count_manager_list(){
		
		$user_createdby = $_SESSION['userid'];
		$usertype = $_SESSION['user_type'];
		$company_id = $_SESSION['company_id'];	
		$match = array('user_type' => 'M');
		if($usertype =='M' || $usertype =='C'){
			$match['company_id'] = (int)$company_id;
			if($usertype =='M'){
				$match['user_createdby'] = (int)$user_createdby;
			}
		}
		$args = array(
					array('$lookup' => array('from' => MDB_COMPANY,'localField' => 'company_id','foreignField' => '_id','as' => 'company')),
					array('$unwind' =>  array( 'path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$match' => $match),
					array('$sort' => array('created_date' => -1)),
					array( '$group' =>  array( '_id' =>  null, 'count' =>  array( '$sum' =>  1 ) ) )
				);
		$result = $this->mongo_db->Aggregate(MDB_PEOPLE, $args);
		return !empty($result['result']) ? $result['result'][0]['count'] : 0;
	}
	
	public function getcompanymanagerlist($id)
	{
		$count = $this->get_companymanagerlist($id, '','' ,true);
		return $count;		
	}
	
	public function getcompanydriverlist($id)
	{
		$count = $this->get_companydriverlist($id, '','' ,true);
		return $count;		
	}
	
	public function getcompanytaxilist($id)
	{
		$count = $this->get_companytaxilist($id, '','' ,true);
		return $count;		
	}
	
	public function passengerWalletLogs($searchArr, $setPagination=0 , $offset = '', $limit = '')
	{
		$keyword = (isset($searchArr['keyword'])) ? trim(Html::chars($searchArr['keyword'])) : '';
		$startdate = (isset($searchArr['startdate']) && $searchArr['startdate']!="") ? Commonfunction::ensureDatabaseFormat($searchArr['startdate'],1) : '';
		$enddate = (isset($searchArr['enddate']) && $searchArr['enddate']!="") ? Commonfunction::ensureDatabaseFormat($searchArr['enddate'],2) : '';
		$limitCnd = "";
		$keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
		
		$date = $result = array();
		$match = array('_id' => array('$ne' => 0));
		($startdate != '') ? $date['$gte'] = Commonfunction::MongoDate(strtotime($startdate)) : '' ;
		($enddate != '') ? $date['$lte'] = Commonfunction::MongoDate(strtotime($enddate)) : '' ;
		if(!empty($date)){		
			$match['createdate'] = $date;
		}
		
		/*if(!empty($keyword)){
			$or = array('$or' => array(array('name' =>  array('$regex' => $keyword,'$options' => '$i')),
					array('pass.lastname' => array('$regex' => $keyword,'$options' => '$i')),
					array('pass.phone' => array('$regex' => $keyword,'$options' => '$i'))));
			$match = array('$and' => array(array('createdate' =>$date), $or));
		}		 */
		
		
		if(!empty($keyword) && (!empty($startdate) || !empty($enddate)))
		{
			$match = array( "\$and" => array(array('createdate' => $date),array("\$or"=>array(array('pass.name' => Commonfunction::MongoRegex("/$keyword/i")) , array('pass.lastname' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'pass.phone' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
		}else if(!empty($keyword)){
			$match = array( "\$or"=>array(array('pass.name' => Commonfunction::MongoRegex("/$keyword/i")) , 
										array('pass.lastname' => Commonfunction::MongoRegex("/$keyword/i") ),
										array( 'pass.phone' => Commonfunction::MongoRegex("/$keyword/i") ) )   );
		}
		$args = array(					
					array('$lookup' => array('from' => MDB_PASSENGERS,'localField' => 'passenger_id','foreignField' => '_id','as' => 'pass')),
					array('$unwind' => '$pass'),
					array('$match' => $match),
					array('$project' => array(
						'name' => '$pass.name',
						'lastname' => '$pass.lastname',
						'creditcard_no' => '$creditcard_no',
						'card_holder_name' => '$card_holder_name',
						'amount' => '$amount',
						'payment_type' => array('$cond' =>  array('if' =>  array('$eq' =>  array('$payment_type', 1)), 
						'then' =>  'Paypal', 'else' =>  'Braintree' )),
						'transaction_id' => '$transaction_id',         
						'promocode' => '$promocode',
						'promocode_amount' => '$promocode_amount',
						'createdate' => '$createdate',
						'mobile_number' =>  array('$concat' =>  array( '$pass.country_code', '$pass.phone')))),
					array('$sort' => array('_id' => -1))
				);
		if($setPagination == 1){
			$args[]['$skip'] = (int)$offset;
			$args[]['$limit'] = (int)$limit;
		}
	
		$res = $this->mongo_db->Aggregate(MDB_PASSENGER_WALLET_LOG, $args);
		//~ echo '<pre>';print_r($match);exit;
		if(!empty($res['result'])){
			$i=0;
			foreach($res['result'] as $r){
				$r['createdate'] = isset($r['createdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']) : '';
				$result[$i] = $r;
				$i++;
			}
		}		
		return $result;
	}
	
	public function details_promoinfo($id, $company_id = 0)
	{		
		$result = $temp_arr = array();
		$match = array('_id' => (int)$id);
		if($company_id != 0) {			
			$match['company_id'] = (int)$company_id;
			$match['comp.companydetails.company_status'] = 'A';
		}
		$args = array(						
						array('$lookup' => array('from' => MDB_COMPANY,'localField' => 'company_id','foreignField' => '_id','as' => 'comp')),
						array('$unwind' =>  array( 'path' =>  '$comp', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$lookup' => array('from' => MDB_PASSENGERS,'localField' => 'passenger_id','foreignField' => '_id','as' => 'pass')),
						array('$unwind' =>  array( 'path' =>  '$pass', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$match' => $match),
						array('$group' => array('_id' => array('promocode' => '$promocode',
						'promo_used_details' => '$promo_used_details',
						'promocode' => '$promocode',
						'promo_discount' => '$promo_discount',
						'start_date' => '$start_date',
						'expire_date' => '$expire_date',
						'promo_limit' => '$promo_limit',
						'passenger_id' => '$passenger_id',
						'user_name' =>  array( '$concat' =>  array( '$pass.name', '$promocode' ) ) 
						),
						'passenger' =>  array( '$sum' =>  '$passenger_id' ),
						'passenger_count' => array('$sum' => 1)
						))
					);
		$res = $this->mongo_db->Aggregate(MDB_PASSENGERS_PROMO, $args);
		//echo '<pre>';print_r($res);exit;
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				$temp_arr['promocode'] = $r['_id']['promocode'];
				$temp_arr['promo_discount'] = $r['_id']['promo_discount'];
				$temp_arr['start_date'] = isset($r['_id']['start_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['_id']['start_date']) : '';
				$temp_arr['expire_date'] = isset($r['_id']['expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['_id']['expire_date']) : '';
				$temp_arr['promo_limit'] = $r['_id']['promo_limit'];
				$temp_arr['passenger_id'] = $r['_id']['passenger_id'];
				$temp_arr['passenger_count'] = isset($r['passenger_count']) ? $r['passenger_count']: 0;
				$temp_arr['user_name'] = $r['_id']['user_name'];
				$temp_arr['promo_used_details'] = $r['_id']['promo_used_details'];
				$result[] = $temp_arr;
			}
		}
		//echo '<pre>';print_r($result);exit;
		return $result;
	}
	
	public function unassign_taxi_list($company = '')
	{
		$currentdate = $this->Commonmodel->getcompany_all_currenttimestamp($company);
		//$date = explode(" ",$currentdate);
		//$enddate = Commonfunction::MongoDate(strtotime($currentdate));
		$currentdate = Commonfunction::MongoDate(strtotime($currentdate));
		$result = array();
		$match = array('$and' =>  array(array('people.status' => 'A'), 
										array('taximapping.mapping_status' => 'A'), 
										array( '$or' =>  array(
											array('taximapping.mapping_startdate' => array('$lte' => $currentdate)),
											array('taximapping.mapping_enddate' => array('$gte' => $currentdate))
										))
					));
		$args = array(
					array('$lookup' => array('from' => MDB_COMPANY,'localField' => 'taxi_company','foreignField' => '_id','as' => 'comp')),
					array('$unwind' => '$comp'),
					array('$lookup' => array('from' => MDB_TAXI_DRIVER_MAPPING,'localField' => '_id','foreignField' => 'mapping_taxiid','as' => 'taximapping')),
					array('$unwind' => '$taximapping'),
					array('$lookup' => array('from' => MDB_PEOPLE,'localField' => 'taximapping.mapping_driverid','foreignField' => '_id','as' => 'people')),
					array('$unwind' => '$people'),
					array( '$match' =>  $match),
					array('$project' => array('_id' => 0,'id' => '$people._id',
					'taxi_id' => '$_id')),
					array('$group'  =>  array('_id'  =>  array('_id' =>  '$id'),
											'details'  =>  array('$first' => array('taxi_id' =>  '$taxi_id','id' =>  '$id')),
											'count' => array('$sum' => 1)))
				
							
				);
		$res = $this->mongo_db->Aggregate(MDB_TAXI, $args);	
		$result = array();
		if(!empty($res['result'])){
			foreach($res['result'] as $key => $val){
				$arr['taxi_id'] = isset($val['details']['taxi_id'])?$val['details']['taxi_id']:'';
				$arr['id'] = isset($val['details']['id'])?$val['details']['id']:'';
				$result[] = $arr;
			}
			//$result = $res['result']; 
		}
		//echo "<pre>"; print_r($result); exit; 
		return $result;
	}
	
	public function mapping_driver_details($id){
		
		$result = array();
		$now = date('Y-m-d H:i:s');
		$date = Commonfunction::MongoDate(strtotime($now));
		$match = array('mapping_taxiid' => (int)$id, 
						'mapping_startdate' => array('$lte' => $date),
						'mapping_enddate' => array('$gte' => $date));
						
		$args = array(array('$lookup' => array('from' => MDB_PEOPLE,'localField' => 'mapping_driverid','foreignField' => '_id','as' => 'people')),
		array('$unwind' =>  array('path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true ) ),
		array('$match' => $match),
		array('$project' => array('driverid' => '$mapping_driverid','id' => '$people._id')));
		
		$res = $this->mongo_db->Aggregate(MDB_TAXI_DRIVER_MAPPING, $args);	
		//echo '<pre>';print_r($res);exit;
		if(!empty($res['result'])){
			
			$result = $res['result']; 
		}
		return $result;
	}
	public function block_unblock_withdraw_payment($activeids,$status)
	{
		if($status == 'D'){
			$active = array();
			$options=['projection'=>['_id'=>1]];
			$active_records = $this->mongo_db->Find(MDB_WITHDRAW_PAYMENT_MODE,array('payment_mode_status'=>'A'),$options);
			if(!empty($active_records)){
				foreach($active_records as $a){
					$active[]=$a['_id'];
				}
			}
			$array_diff = array_diff($active,$activeids);
			if(empty($array_diff))
				return -1;
		}
		
		$active_ids = Commonfunction::mongo_format_array($activeids);
 		$result = $this->mongo_db->updateMany(MDB_WITHDRAW_PAYMENT_MODE,array('_id'=>array('$in'=>$active_ids)),array('$set'=>array('payment_mode_status' => $status)));
 		return 1;
	}
	
	public function insert_withdraw_status_log($post,$log_id)
	{
		$date = $this->currentdate_bytimezone;
		$transaction_id = $post['transaction_id'];
		$payment_mode = $post['payment_mode'];
		if($post["status"] == 2) {
			$transaction_id = "";
			$payment_mode = 0;
		}
		
		$new_logid=1; $company_request_amount = $requester_id = 0;
		$match = array('_id'=>(int)$log_id);
		//$withdraw_logs = $this->mongo_db->find(MDB_WITHDRAW_REQUEST,$match,array('withdraw_request_log.log_id'))->sort(array('withdraw_request_log.log_id'=>-1))->limit(1);					
                 ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                                        $options=[
                                            'projection'=>[
                                               'withdraw_request_log.log_id'=>1
                                            ],
                                            'sort'=>[
                                              'withdraw_request_log.log_id'  =>-1
                                            ],
                                            'limit'=>1
                                        ];
                                        $withdraw_logs = $this->mongo_db->find(MDB_WITHDRAW_REQUEST,$match,$options);
		$logs = $withdraw_logs;
		$logs = reset($logs);
		if(!empty($logs['withdraw_request_log'])){
			$end = end($logs['withdraw_request_log']);
			$new_logid = $end['log_id'] +1;
		}

		$set_array = array('withdraw_request_log'=>array(
								'log_id'  =>  (int)$new_logid,
								'withdraw_request_id'  =>  (int)$log_id,
								'payment_mode'  => (int)$payment_mode,
								'transaction_id'  => $transaction_id,
								'comments'  => $post['comments'],
								'request_status'  => (int)$post['status'],
								'file_name'  =>  '',
								"createdate" => Commonfunction::MongoDate(strtotime($date))));
		$insert_log = $this->mongo_db->updateOne(MDB_WITHDRAW_REQUEST,$match,array('$push'=>$set_array),array('upsert'=>false));
		
		$update1 = array('request_status' => (int)$post["status"]);
		$update = $this->mongo_db->updateOne(MDB_WITHDRAW_REQUEST,$match,array('$set' => $update1),array('upsert'=>false));
		
		$amount_result = $this->mongo_db->findOne(MDB_WITHDRAW_REQUEST,$match,array('withdraw_amount','requester_id'));					
		if(count($amount_result) > 0) {
			$company_request_amount = $amount_result["withdraw_amount"];
			$requester_id = $amount_result["requester_id"];
		}
		if($post['status'] == 1) {
			
				$acc_arr = array('account_balance' => -$company_request_amount);
				$update = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id' => (int)$requester_id),array('$inc' => $acc_arr),array('upsert'=>false));
		} else if($post['status'] == 2) {			
			if($requester_id > 0) {
				$update_arr = array('_id' => (int)$requester_id);
				$update_arr['user_type'] = ($post['type'] == 0) ? 'C' : 'D';
				$acc_arr = array('account_balance' => $company_request_amount);
				//$update = $this->mongo_db->updateOne(MDB_PEOPLE,$update_arr,array('$inc' => $acc_arr),array('upsert'=>false));
			}			
		}
		return $log_id.'_'.$new_logid;
	}

	/** 
	 * Update Withdraw Request Attachment Name
	 **/

	public function update_withdraw_file_name($log_ids,$attachment_name)
	{
		if(!empty($log_ids)){
			foreach($log_ids as $id){
				$ids = explode('_', $id);
				$requestid = $ids[0];
				$logid = $ids[1];
				
				$match = array('_id'=>(int)$requestid);
				$args = array(array('$unwind' => '$withdraw_request_log'),
						  array('$match' => $match),
						  array('$project' => array('log_id' => '$withdraw_request_log.log_id','withdraw_request_id' => '$withdraw_request_log.withdraw_request_id','payment_mode' => '$withdraw_request_log.payment_mode','transaction_id' => '$withdraw_request_log.transaction_id','comments' => '$withdraw_request_log.comments','request_status' => '$withdraw_request_log.request_status','createdate' => '$withdraw_request_log.createdate'))
						);
				$keys = $this->mongo_db->aggregate(MDB_WITHDRAW_REQUEST,$args);
				$val = array();
				foreach($keys['result'] as $k => $v ) {
					$t = $keys['result'][0];
					$val = array('withdraw_request_log' => 
						array(array(
							'log_id' => (int)$t['log_id'],
							'withdraw_request_id' => (int)$t['withdraw_request_id'],
							'payment_mode' => (int)$t['payment_mode'],
							'transaction_id' => $t['transaction_id'],
							'comments' => $t['comments'],
							'request_status' => (int)$t['request_status'],
							'createdate' => Commonfunction::MongoDate(strtotime(commonfunction::convertphpdate('Y-m-d H:i:s',$t['createdate']))),
							'file_name' => $attachment_name
						)));
				}
				$result = $this->mongo_db->updateOne(MDB_WITHDRAW_REQUEST,$match,array('$set' => $val),array('upsert'=>false));
				return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
			}
		}
	}
	
	public function insert_company_withdraw_request($post,$company_id,$user_id)
	{
		$uniqueId = commonfunction::checkRequestID(); 
		$date = commonfunction::getCurrentTimeStamp();
		$company_request_amount = $post['company_request_amount'];
		
		$inc_id = $this->get_insert_id(MDB_WITHDRAW_REQUEST);
		$insert_arr = array(
							'_id' => (int)$inc_id,
							'company_id' => (int)$company_id,
							'request_id' => $uniqueId,
							'brand_type' => 1,
							'requester_id' => (int)$user_id,
							'withdraw_amount' => (double)$company_request_amount,
							'request_status' => 0,
							'type' => 1,
							'request_date' => Commonfunction::MongoDate(strtotime($date))
						);
		$insert_log = $this->mongo_db->insertOne(MDB_WITHDRAW_REQUEST,$insert_arr);
		return $inc_id;
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
	
	public function details_moderatorinfo($id)
	{
		$result = array();
		$ops = array(
			array('$match'=>array('user_type'=>'S','status'=>array('$ne'=>'T'),"_id" => (int)$id)),
			array(
				'$lookup' => array(
				'from'=>MDB_CSC,
				'localField'=> "login_country",
				'foreignField' => "_id",
				'as'=> "countrydetails"
				)
			),
			array('$unwind' => array('path' =>'$countrydetails', 'preserveNullAndEmptyArrays'=>true)),
			array(
				'$project' => array(
				'country_name' => '$countrydetails.country_name',
				'name' => '$name',
				'lastname' => '$lastname',
				'email' => '$email',
				'address' => '$address',
				'country_code' => '$country_code',
				'phone' => '$phone',
				'status' => '$status',
				'login_country' => '$login_country',
				'user_type' => '$user_type',
				)
			),
		);
		$res = $this->mongo_db->aggregate(MDB_PEOPLE,$ops);
		if(!empty($res['result'])) {
			foreach($res['result'] as $r) {
				$temp_arr['name'] = isset($r['name']) ? $r['name']: '';
				$temp_arr['address'] = isset($r['address']) ? $r['address']: '';
				$temp_arr['lastname'] = isset($r['lastname']) ? $r['lastname']: '';
				$temp_arr['email'] = isset($r['email']) ? $r['email']: '';
				$temp_arr['country_code'] = isset($r['phone']) ? $r['country_code']: '';
				$temp_arr['phone'] = isset($r['phone']) ? $r['phone']: '';
				$temp_arr['user_type'] = isset($r['user_type']) ? $r['user_type']: '';
				$temp_arr['status'] = isset($r['status']) ? $r['status']: '';
				$temp_arr['country_name'] = isset($r['country_name']) ? $r['country_name']: '';
				$result[] = $temp_arr;
			}
		}
		return $result;
	}
	
	public function viewtransaction_details($transaction_id)
    {        
        $result = array();
        $match = array(
            'transaction_id' => $transaction_id
        );
        $args = array(					
					array('$lookup' => array('from' => MDB_PASSENGERS,'localField' => 'passenger_id','foreignField' => '_id','as' => 'pass')),
					array('$unwind' => '$pass'),
					array('$match' => $match),
					array('$project' => array(
						'name' => '$pass.name',
						'lastname' => '$pass.lastname',
						'amount' => '$amount',
						'payment_type' => array('$cond' =>  array('if' =>  array('$eq' =>  array('$payment_type', 1)), 
						'then' =>  'Paypal', 'else' =>  'Braintree' )),
						'payment_status' => array('$cond' =>  array('if' =>  array('$eq' =>  array('$payment_status', 1)), 
						'then' =>  'Success', 'else' =>  'Pending' )),
						'transaction_id' => '$transaction_id',
						'createdate' => '$createdate',
						'mobile_number' =>  array('$concat' =>  array( '$pass.country_code', '$pass.phone'))))
				);
		$res = $this->mongo_db->Aggregate(MDB_PASSENGER_WALLET_LOG, $args);
		if(!empty($res['result'])){
			$i=0;
			foreach($res['result'] as $r){
				$r['createdate'] = isset($r['createdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['createdate']) : '';
				$result[$i] = $r;
				$i++;
			}
		}		
		return $result;
    }
	
	public function get_popularplaces($count,$offset,$limit,$keyword=''){
		
		$result = $temp_arr = $match = array();
		#keyword search
		if($keyword != ''){
			$or = array('$or'=>array(
							array( 'city_name' => Commonfunction::MongoRegex("/$keyword/i")) , 
							array( 'label_name' => Commonfunction::MongoRegex("/$keyword/i") ))
						);
			$match[] = array('$match' => $or);
		}
		#common arguements
		$args = array(
					array('$project' => array('city_id' => '$city_id','city_name'=>'$city_name','label_name'=>'$label_name')),
					array('$group'=>array('_id'=>array('city_id'=>'$city_id','city_name'=>'$city_name'),
										'count'=>array('$sum'=>1),		
									)),
					array('$sort' => array('_id.city_id' => 1))
				);
		if($limit != ''){
			$args[]['$skip'] = (int)$offset;
			$args[]['$limit'] = (int)$limit;
		}
		#insert match array
		if(!empty($match)){
			array_splice( $args, 0, 0, $match );
		}
		$res = $this->mongo_db->aggregate(MDB_POPULAR_PLACES, $args);		
		if(!empty($res['result'])){
			foreach($res['result'] as $r){					
				$temp_arr['city_name'] = isset($r['_id']['city_name']) ? $r['_id']['city_name']: '';
				$temp_arr['city_id'] = isset($r['_id']['city_id']) ? $r['_id']['city_id']: '';
				$temp_arr['count'] = isset($r['count']) ? $r['count']: '';							
				$result[] = $temp_arr;
			}				
		}
		if($count == true){
			return count($result);
		}
		return $result;	
	}
	
	public function getcompany_emails($activeids){
		
		$list = array();
		$active_ids = Commonfunction::mongo_format_array($activeids);			
		$match_query = array('_id' => array('$in' => $active_ids), 'people.user_type' => 'C');
		$args = array(
			array('$lookup' => array(
					'from' => MDB_COMPANY,
					'localField' => '_id',
					'foreignField' => "model_fare.model_id",
					'as' => "company"
				)
			),			
			array('$unwind' => '$company'),	
			array('$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => 'company._id',
					'foreignField' => 'company_id',
					'as' => "people"
				)
			),	
			array('$unwind' => '$people'),
			array('$match'  => $match_query),
			array('$project' => array(
					'_id'=>0,
					'company_id' => '$company._id',
					'company_email' => '$people.email',
				)
			),
			array('$group'  =>  array('_id'  =>  array('_id' =>  '$company_id' ),
							'details' => array('$first' => array(
								'company_email' => '$company_email',									
							)
					)))
			
		);
		$res = $this->mongo_db->aggregate(MDB_MOTOR_MODEL,$args);
		if(!empty($res['result'])){
			foreach($res['result'] as $r){
				isset($r['details']['company_email']) ? $list[] = $r['details']['company_email'] : '';
			}
		}
		return $list;
	}
	
}
