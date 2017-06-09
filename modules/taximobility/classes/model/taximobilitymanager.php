<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Manager Model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityManager extends Model
{
	public function __construct()
	{	
		$this->session = Session::instance();	
		//$this->username = $this->session->get("user_name");
		$this->currentdate = Commonfunction::getCurrentTimeStamp();
		$this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
		$this->userid 			= $this->session->get("userid");
		$this->company_id     	= $this->session->get('company_id');
        $this->usertype 		= $this->session->get("user_type");
        $this->country_id      	= $this->session->get('country_id');
        $this->state_id        	= $this->session->get('state_id');
        $this->city_id         	= $this->session->get('city_id');
		//MongoDB Instance
        $this->mongo_db         = MangoDB::instance('default');
	}
	
	public function get_availabletaxi_list($find_count = false)
    {
        $company_id  = (int)$this->company_id;
		$usertype = $this->usertype;	
	   	$country_id = $this->country_id;
	   	$state_id = $this->state_id;
	   	$city_id = $this->city_id;
		
		$currentdate = date('Y-m-d H:i:s');
        $enddate     = date('Y-m-d') . ' 23:59:59';
		$match_query = array();
        $match_query['driver.status'] = 'F';
        $match_query['driver.shift_status'] = 'IN';
		$match_query['mapping.mapping_status'] = 'A';
		$match_query['people.status'] = 'A';
		$match_query['mapping.mapping_cityid'] = (int)$city_id;
		$match_query['taxi_country'] = (int)$country_id;
		$match_query['taxi_state'] = (int)$state_id;
		$match_query['taxi_city'] = (int)$city_id;
		$match_query['people.login_country'] = (int)$country_id;
		$match_query['people.login_state'] = (int)$state_id;
		$match_query['people.login_city'] = (int)$city_id;

        if ($company_id!="" && $company_id!=0) {
			$match_query['company._id'] = $company_id;
		}
		if ($currentdate!="" && $enddate!="") {
			$match_query['mapping.mapping_startdate'] = array('$lt' => Commonfunction::MongoDate(strtotime($currentdate)));
			$match_query['mapping.mapping_enddate'] = array('$gt' => Commonfunction::MongoDate(strtotime($enddate)));
		}
		$arguments = array(
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
				'$lookup' => array(
					'from' => MDB_TAXI_DRIVER_MAPPING,
					'localField' => '_id',
					'foreignField' => 'mapping_taxiid',
					'as' => 'mapping'
				)
			),
			array(
				'$unwind' => '$mapping'
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
			array( '$project' =>
				array(
					'taxi_id' => '$_id',
					'taxi_no'=>'$taxi_no',
					'userid'=>'$company.companydetails.userid',
					'company_name'=>'$company.companydetails.company_name',
					'driver_id'=>'$driver._id',
					'name'=>'$people.name',
					'phone'=>'$people.phone',
				)
			),
			array('$skip' => 0),
			array('$limit' => 10)
		);
		$result          = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
		//echo "<pre>";print_r($result['result']); exit;
		return (!empty($result['result']) && isset($result['result'])) ? $result['result']:array();
    }
	
	public function free_driver_list()
    {
		$usertype = $this->usertype;
		$company_id  = (int)$this->company_id;
	   	$country_id = $this->country_id;
	   	$state_id = $this->state_id;
	   	$city_id = $this->city_id;
		$assigned_driver = $this->free_availabletaxi_list();
		$match_query = $driver_list = array();
		$match_query['user_type'] = 'D';
		$match_query['status'] = 'A';
		$match_query['availability_status'] = 'A';
		$match_query['login_country'] = (int)$country_id;
		$match_query['login_state'] = (int)$state_id;
		$match_query['login_city'] = (int)$city_id;
        if ($company_id!="" && $company_id!=0) {
			$match_query['company_id'] = $company_id;
		}
        if (count($assigned_driver) > 0) {
            foreach ($assigned_driver as $key => $value) {
                $driver_list[] = (int)$value['id'];
            }
			$match_query['_id'] = array('$nin' => $driver_list);
        }
		//echo "<pre>"; print_r($match_query); exit;
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
			array(
				'$match' => $match_query
			),
			array(
				'$project' => array(
					'id' => '$_id',
					'name' => '$name',
					'userid' => '$company.companydetails.userid',
					'company_name' => '$company.companydetails.company_name',
				)
			),
			array(
				'$sort' => array('_id' => 1)
			)
		);
		$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
		//echo "<pre>"; print_r($result); exit;
		return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
    }
	
	public function free_taxi_list($find_count = false, $cid = 0)
    {
        $usertype       = $this->usertype;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
		$company_id  	= (int)$this->company_id;
		$match_query = $taxi_list = array();
		$match_query['taxi_status'] = 'A';
		$match_query['taxi_availability'] = 'A';
		$match_query['taxi_country'] = (int)$country_id;
		$match_query['taxi_state'] = (int)$state_id;
		$match_query['taxi_city'] = (int)$city_id;
		$booked_driver = $this->free_availabletaxi_list();
        //echo '<pre>';print_r($company_id);exit;
        if (count($booked_driver) > 0) {
            foreach ($booked_driver as $key => $value) {
                $taxi_list[] = (int)$value['taxi_id'];
            }
			$match_query['_id'] = array('$nin' => $taxi_list);
        }
        if ($company_id !='' && $company_id != 0) {
			$match_query['taxi_company'] = (int)$company_id;
        }
		$arguments = array(
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
			array(
				'$project' => array(
					'taxi_id' => '$_id',
					'taxi_no' => '$taxi_no',
					'cid' => '$company._id',
					'company_name' => '$company.companydetails.company_name',
					'userid' => '$company.companydetails.userid'
				)
			),
			array(
				'$sort' => array('_id' => 1)
			),
		);
		$result    = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
		//echo "<pre>"; print_r($result); exit;
		return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
	}	
	
	public function free_availabletaxi_list()
    {
		$usertype = $this->usertype;
		$company_id  = (int)$this->company_id;
	   	$country_id = $this->country_id;
	   	$state_id = $this->state_id;
	   	$city_id = $this->city_id;
		
		$currentdate = date('Y-m-d H:i:s');
        $enddate     = date('Y-m-d') . ' 23:59:59';
		$match_query = array();			
		$match_query['people.status'] = 'A';
		$match_query['mapping.mapping_status'] = 'A';
		$match_query['mapping.mapping_cityid'] = (int)$city_id;
		$match_query['taxi_country'] = (int)$country_id;
		$match_query['taxi_state'] = (int)$state_id;
		$match_query['taxi_city'] = (int)$city_id;
		$match_query['people.login_country'] = (int)$country_id;
		$match_query['people.login_state'] = (int)$state_id;
		$match_query['people.login_city'] = (int)$city_id;
		if ($company_id!="" && $company_id!=0) {
			$match_query['company._id'] = $company_id;
		}
		if ($currentdate!="" && $enddate!="") {
			$match_query['mapping.mapping_startdate'] = array('$lt' => Commonfunction::MongoDate(strtotime($currentdate)));
			$match_query['mapping.mapping_enddate'] = array('$gt' => Commonfunction::MongoDate(strtotime($enddate)));
		}
		//echo "<pre>"; print_r($match_query); exit;
		$arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_TAXI_DRIVER_MAPPING,
					'localField' => '_id',
					'foreignField' => 'mapping_taxiid',
					'as' => 'mapping'
				)
			),
			array(
				'$unwind' => '$mapping'
			),
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
			array(
				'$project' => array(
					'id' => '$people._id',
					'taxi_id' => '$_id'
				)
			),
			array(
				'$skip' => 0
			),
			array(
				'$limit' => 10
			),
		);
		$result    = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
		//echo "<pre>"; print_r($result['result']); exit;
		return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
    }
	
	
	public function gettransaction($id)
	{
		$match_query = $result = $id_arr = array();
		
		if($id !=''){
			
			$id_arr = explode(',', $id);
			$ids = Commonfunction::mongo_format_array($id_arr);
			$match_query['driver_id'] = array('$in' => $ids);
			$match_query['travel_status'] = 1;
			$arguments=array(
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
				array(
					'$match' => $match_query
				),
				array(
					'$group' => array(
						'_id' => array('driver_id' => '$driver_id','name' => '$people.name'),
						'count' => array('$sum' => 1)
					) 
				),
				array(
					'$project' => array(
						'_id' => 0,
						'driver_name' => '$_id.name',
						'count' => '$count'
					)
				),
			);
			$res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
			if(!empty($res['result'])){
				$result = $res['result'];
			}
		}
		return $result;
	}
	
	public function changegettransaction($id = '',$startdate = '',$enddate = '')
	{
		$match_query = $result = $id_arr = array();
		
		if($id !=''){
				
			$id_arr = explode(',', $id);
			$ids = Commonfunction::mongo_format_array($id_arr);
			$match_query['driver_id'] = array('$in' => $ids);
			$match_query['travel_status'] = 1;
			
			if($startdate!="" && $enddate!=""){
				$match_query['createdate'] = array('$gte'=>Commonfunction::MongoDate(strtotime($startdate)),'$lte'=>Commonfunction::MongoDate(strtotime($enddate)));
			}
			$match_query['travel_status'] = 1;
			$arguments=array(
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
				array(
					'$match' => $match_query
				),
				array(
					'$group' => array(
						'_id' => array('driver_id' => '$driver_id','name' => '$people.name'),
						'count' => array('$sum' => 1)
					) 
				),
				array(
					'$project' => array(
						'_id' => 0,
						'driver_name' => '$_id.name',
						'count' => '$count'
					)
				),
			);
			$result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		}
		return (!empty($result) && isset($result['result'])) ? $result['result'] : array();
	}
	
	public function get_admin_dashboard_data($company_id)
    {		
		# Active Passenger Count 
		$active_passenger_count = 0;
		$and1 = array('$and' => array(array( 'travel_status' => 1),array('driver_reply' => 'A')));
		$and2 = array('$and' => array(array( 'travel_status' => 9),array('driver_reply' => 'C')));
		$or2 = array('$or' => array($and2,array('travel_status' => array('$in' => array(4,8)))));
		$or1 = array('$or' => array($and1,$or2));
		
		$passenger_match = array('$and' =>  array(array('company_id' => (int)$company_id),$or1));
		
		$passenger_args = array(
								array('$lookup' => array('from' => 'passengers','localField' => 'passengers_id',
													'foreignField' => '_id','as' => 'pass')),
								array('$unwind' =>  array('path' =>  '$pass', 'preserveNullAndEmptyArrays' =>  true ) ),
								array('$match' => $passenger_match),
								array('$group' => array('_id' => array('_id' => null),
								'active_passenger_count' => array('$sum' => 1)))
							);
		$cancel_trips_sql = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$passenger_args);
		if(!empty($cancel_trips_sql['result'])){
			$active_passenger_count = $cancel_trips_sql['result'][0]['active_passenger_count'];
		}
		$result["general_users"] = $active_passenger_count;
			
		$result["driver"] = $this->mongo_db->count(MDB_PEOPLE,array('user_type' => 'D','status' => 'A','company_id' => (int)$company_id),array('_id'));
		
		$result["country"] = $this->mongo_db->count(MDB_CSC,array('country_status' => 'A'),array('_id'));
		$arguments = array(array('$unwind' => '$stateinfo'),array('$match'=> array('stateinfo.state_status' => 'A')),array('$project' => array('id' 	=> '$stateinfo.state_id')),array('$group' =>array('_id' => NULL,'count' => array('$sum' => 1))));
		
		$state_count = $this->mongo_db->aggregate(MDB_CSC,$arguments);
        
        $result["state"] = (isset($state_count['result'][0]['count']))?$state_count['result'][0]['count']:0;
		$arguments = array(array('$unwind' => '$stateinfo'),array('$unwind' => '$stateinfo.cityinfo'),array('$match'=> array('stateinfo.cityinfo.city_status' => 'A')),array('$project' => array('id' 	=> '$stateinfo.cityinfo.city_id')),array('$group' =>array('_id' => NULL,'count' => array('$sum' => 1))));
		
		$city_count = $this->mongo_db->aggregate(MDB_CSC,$arguments);
        $result["city"] = (isset($city_count['result'][0]['count']))?$city_count['result'][0]['count']:0;
		$result["taxi"] = $this->mongo_db->count(MDB_TAXI,array('taxi_company' => (int)$company_id, 'taxi_status' => 'A'),array('_id'));
        //echo '<pre>';print_r($result);exit;
        return $result;
    }
	
	
	public function get_activeusers_list($company_id)
    {
		$arguments = array(
			array(
				'$match' => array('login_status' => 'A', 'passenger_cid' => (int)$company_id)
			),
			array(
				'$project' => array(
					'name' => '$name',
					'last_login' => '$last_login',
					'phone' => '$phone',
					'address' => '$address'
				)
			),
			array('$sort' => array('last_login'=>-1)),
			array('$skip' => 0),
			array('$limit' => 10)
		);
		$result          = $this->mongo_db->aggregate(MDB_PASSENGERS, $arguments);
		//echo "<pre>";print_r($result['result']); exit;
		return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
    }
		//dashboard active users count
	public function get_activeusers_list_count($company_id)
	{
		$match = array('login_status' => 'A','passenger_cid' => (int)$company_id,'login_status' => 'A');
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match);
		return $result;
	}

	/***********Dashboard Trip details chart************/
	public function get_company_trip_count($month, $year)
	{
		$user_type=$_SESSION['user_type'];
		$dispatcher_id=$_SESSION['userid'];
		$company_id=$_SESSION['company_id'];
		$count=0;
		if($user_type == 'M'){
			
			$match = array( 'month' => (int)$month, 
							'year' => (int)$year,
							'travel_status' => 1, 'driver_reply' => 'A',
							'cid' => (int)$company_id, 'id' => (int)$dispatcher_id
							);
			$args = array(
						array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,'localField' => '_id','foreignField' => 'company_id','as' => 'pass_log')),
						array('$unwind' => '$pass_log'),
						array('$lookup' => array('from' => MDB_TRANSACTION,'localField' => 'pass_log._id','foreignField' => 'passengers_log_id','as' => 'trans')),
						array('$unwind' => '$trans'),
						array('$lookup' => array('from' => MDB_PEOPLE,'localField' => 'companydetails.userid','foreignField' => 'user_createdby','as' => 'people')),
						array('$unwind' => '$people'),
						array('$project' => array(
												'_id' => 0,
												'month' =>  array( '$month' =>  '$trans.current_date' ),
												'year' =>  array( '$year' =>  '$trans.current_date' ),
												'travel_status' => '$pass_log.travel_status',
												'driver_reply' => '$pass_log.driver_reply',
												'cid' => '$_id',
												'id' => '$people._id')),
						array('$match' => $match),
						array('$group'  =>  array('_id'  =>  array('month' =>  array( 'month' =>  '$month' )),
											'count' => array('$sum' => 1)))
					);
			$res = $this->mongo_db->Aggregate(MDB_COMPANY,$args);
			if(!empty($res['result'])){
				
				$result = $res['result'][0];
				$count = $result['count'];
			}			
		}
		return $count;
	}

	public function get_company_trip_revenues($month, $year)
	{
		$user_type=$_SESSION['user_type'];
		$dispatcher_id=$_SESSION['userid'];
		$company_id=$_SESSION['company_id'];
		$revenue=0;
		if($user_type == 'M'){
			
			$match = array('month' => (int)$month, 
							'year' => (int)$year,
							'travel_status' => 1, 
							'cid' => (int)$company_id, 'id' => (int)$dispatcher_id
							);
			$args = array(
						array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,'localField' => '_id','foreignField' => 'company_id','as' => 'pass_log')),
						array('$unwind' => '$pass_log'),
						array('$lookup' => array('from' => MDB_TRANSACTION,'localField' => 'pass_log._id','foreignField' => 'passengers_log_id','as' => 'trans')),
						array('$unwind' => '$trans'),
						array('$lookup' => array('from' => MDB_PEOPLE,'localField' => 'companydetails.userid','foreignField' => 'user_createdby','as' => 'people')),
						array('$unwind' => '$people'),
						array('$project' => array(
										'_id' => 0,
										'month' =>  array( '$month' =>  '$trans.current_date' ),
										'year' =>  array( '$year' =>  '$trans.current_date' ),
										'travel_status' => '$pass_log.travel_status',
										'driver_reply' => '$pass_log.driver_reply',
										'cid' => '$_id',
										'id' => '$people._id',
										'fare' => '$trans.fare')),
						array('$match' => $match),
						array('$group'  =>  array('_id'  =>  array( 'month' =>  array( 'month' =>  '$month' )),
								   'revenues' => array('$sum' => '$fare')))
			   );
			$res = $this->mongo_db->Aggregate(MDB_COMPANY,$args);
			//echo '<pre>';print_r($res);exit;
			if(!empty($res['result'])){
				
				$result = $res['result'][0];
				$count = $result['revenues'];
			}
		}
		return $revenue;
	}
	
	
	/********* Total Trip and Revenue details *********************/
	public function total_trip_details($start='',$end='')
	{
		$dispatcher_id	= $this->userid;
		$company_id		= $this->company_id;
		$match_query = array();
		//$match_query['plog.travel_status'] = 1;
		if($start!=''&& $end!=''){
			$match_query['plog.pickup_time'] = array('$gte'=>Commonfunction::MongoDate(strtotime($start)),'$lte'=>Commonfunction::MongoDate(strtotime($end)));
		}
		if($company_id != 0 && $company_id!=""){
			$match_query['plog.company_id'] = (int)$company_id;
		}
		if($dispatcher_id!=""){
			//$match_query['people.id'] = (int)$dispatcher_id;
		}
        $arguments = array(
			array('$unwind' => '$companydetails'),		
			array('$lookup' 		=> array(
                    'from'			=>	MDB_PASSENGERS_LOGS,
                    'localField'	=> '_id',
                    'foreignField'	=> "company_id",
                    'as'			=> "plog"
                )
            ),
            //array('$unwind' => '$plog'),	
            array('$unwind' =>  array('path' =>  '$plog', 'preserveNullAndEmptyArrays' =>  true ) ),
            array('$match'	=> $match_query),
			array('$lookup' 		=> array(
                    'from'			=>	MDB_TRANSACTION,
                    'localField'	=> 'plog._id',
                    'foreignField'	=> "passengers_log_id",
                    'as'			=> "trans"
                )
            ),
            //array('$unwind' => '$trans'),
            array('$unwind' =>  array('path' =>  '$trans', 'preserveNullAndEmptyArrays' =>  true ) ),
			//~ array('$lookup' 		=> array(
                    //~ 'from'			=>	MDB_PEOPLE,
                    //~ 'localField'	=> 'companydetails.userid',
                    //~ 'foreignField'	=> 'user_createdby',
                    //~ 'as'			=> 'people'
                //~ )
            //~ ),
            //~ array('$unwind' => '$people'),
            //array('$match'	=> $match_query),
			array(
                '$project' => array(
					'year' => array( '$substr' => array( '$plog.pickup_time', 0, 4 ) ),
                    'month' => array( '$substr' => array( '$plog.pickup_time', 5, 2 ) ),
                    'day' => array( '$substr'=> array( '$plog.pickup_time', 8, 2 ) ),
					'fare' => '$trans.fare',
                )
            ),
            array('$group' => array('_id' => array( 'date' => '$day','month' => '$month'),
                'fare' => array('$sum' => '$fare'),
                'trips' => array( '$sum' => 1 ),
                )
            ),
            array('$sort' => array('_id.month' => 1)),
            array('$sort' => array('_id.date' => 1))
        );
         
        $result = $this->mongo_db->aggregate(MDB_COMPANY,$arguments);
        //echo '<pre>';print_r($result);exit;
		return (!empty($result) && $result['result'])?$result['result']:array();
	}
	/***********Dashboard Trip details chart************/
	public function get_company_timezone($company_id)
	{		
		$result = $this->mongo_db->findOne(MDB_COMPANY,array('_id' => (int)$company_id),array('companydetails.user_time_zone'));
		(isset($result['user_time_zone']) && $result['user_time_zone'] != '') ? $this->session->set("timezone",$result['user_time_zone']) : "";
	}
		
}
