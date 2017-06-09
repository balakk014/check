<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Company Model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityCompany extends Model
{
    public function __construct()
    {
        $this->session     = Session::instance();
        //$this->username = $this->session->get("user_name");
        $this->currentdate = Commonfunction::getCurrentTimeStamp();
        # created date
		$this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
        $this->mongo_db    = MangoDB::instance('default');
        $this->company_id     = $this->session->get('company_id');
    }
    /**Validating User SignUP details**/
    public function validate_company_signup($arr)
    {
        return Validation::factory($arr)->rule('firstname', 'not_empty')->rule('firstname', 'min_length', array(
            ':value',
            '4'
        ))->rule('firstname', 'max_length', array(
            ':value',
            '32'
        ))->rule('lastname', 'not_empty')->rule('lastname', 'max_length', array(
            ':value',
            '32'
        ))->rule('email', 'not_empty')->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '100'
        ))->rule('companyname', 'not_empty')->rule('companyname', 'alpha_dash')->rule('companyname', 'min_length', array(
            ':value',
            '4'
        ))->rule('companyname', 'max_length', array(
            ':value',
            '30'
        ))->rule('country', 'not_empty')->rule('city', 'not_empty')->rule('state', 'not_empty')->rule('address', 'not_empty')->rule('companyaddress', 'not_empty')->rule('mobile', 'not_empty')->rule('mobile', 'min_length', array(
            ':value',
            '4'
        ))->rule('mobile', 'max_length', array(
            ':value',
            '36'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('password', 'not_empty')->rule('password', 'min_length', array(
            ':value',
            '4'
        ))->rule('password', 'max_length', array(
            ':value',
            '36'
        ))->rule('confirm_password', 'not_empty')->rule('confirm_password', 'min_length', array(
            ':value',
            '4'
        ))->rule('confirm_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('confirm_password', 'matches', array(
            ':validation',
            'password',
            'confirm_password'
        ))->rule('confirm_password', 'max_length', array(
            ':value',
            '36'
        ));
        //->rule('terms', 'not_empty');
    }
    public function validate_company_login($arr)
    {
        return Validation::factory($arr)->rule('company_email', 'not_empty')->rule('company_email', 'email')->rule('company_email', 'max_length', array(
            ':value',
            '100'
        ))->rule('company_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('company_password', 'not_empty')->rule('company_password', 'min_length', array(
            ':value',
            '4'
        ))->rule('company_password', 'max_length', array(
            ':value',
            '36'
        ));
    }
    public function get_availabletaxi_list($find_count = false)
    {
        $company_id  = (int)$this->company_id;
		$currentdate = date('Y-m-d H:i:s');
        $enddate     = date('Y-m-d') . ' 23:59:59';
		$match_query = array();
        $match_query['people.status'] = 'A';
        $match_query['driver.status'] = 'A';
		$match_query['mapping.mapping_status'] = 'A';
        $match_query['taxi_status'] = 'A';
        $match_query['taxi_availability'] = 'A';
        $match_query['people.availability_status'] = 'A';
        $match_query['people.user_type'] = 'D';
        if ($company_id!="" && $company_id!=0) {
			$match_query['company._id'] = $company_id;
		}
		if ($currentdate!="" && $enddate!="") {
			$match_query['mapping.mapping_startdate'] = array('$gte' => $currentdate);
			$match_query['mapping.mapping_enddate'] = array('$lt' => $enddate);
		}
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
        );
        if($find_count == TRUE){
			$count_arguments = array( 
                    array( '$project' => array(
                        'taxi_id' => '$_id',
                        'taxi_no'=>'$taxi_no',
                        'userid'=>'$companydetails.userid',
                        'company_name'=>'$companydetails.company_name',
                        'driver_id'=>'$driver._id',
                        'name'=>'$people.name',
                        'phone'=>'$people.phone',
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
            $result          = $this->mongo_db->aggregate(MDB_CSC, $arguments);
            //echo "<pre>";print_r($result['result']); exit;
            return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
        }else{
            $count_arguments = array( 
                    array( '$project' => array(
                        'taxi_id' => '$_id',
                        'taxi_no'=>'$taxi_no',
                        'userid'=>'$companydetails.userid',
                        'company_name'=>'$companydetails.company_name',
                        'driver_id'=>'$driver._id',
                        'name'=>'$people.name',
                        'phone'=>'$people.phone',
                    )
                ),
                array('$skip' => 0),
                array('$limit' => 10)
            );
            $arguments = array_merge($common_arguments,$count_arguments);
            $result          = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
            //echo "<pre>";print_r($result['result']); exit;
            return (!empty($result['result']) && isset($result['result'])) ? $result['result']:array();
        }
    }
    
    public function get_manager_list()
    {
        $company_id  = (int)$this->company_id;
		$match_query = array();
		$match_query['people.user_type'] = 'M';

        if ($company_id!="" && $company_id!=0) {
			$match_query['people.company_id'] = $company_id;
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
				'$match' => $match_query
			),
			array(
				'$project' => array(
					'id' => '$people._id',
                    'name'=>'$people.name',
                    'phone'=>'$people.phone',
                    'userid'=>'$company.companydetails.userid',
                    'company_name'=>'$company.companydetails.company_name',
                    'country_name'=>'$country_name',
                    'state_name'=>'$stateinfo.state_name',
                    'city_name'=>'$stateinfo.cityinfo.city_name',
				)
			),
		);
		$result          = $this->mongo_db->aggregate(MDB_CSC, $arguments);
		//echo "<pre>";print_r($result['result']); exit;
		return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
    }    
    public function free_driver_list( $find_count=false)
    {
        $company_id  = $this->company_id;
		$assigned_driver = $this->free_availabletaxi_list();
		$match_query = $driver_list = array();
		$match_query['company_id'] = (int)$company_id;
        $match_query['user_type'] = 'D';
		$match_query['status'] = 'A';
		$match_query['availability_status'] = 'A';
        if (count($assigned_driver) > 0) {
            foreach ($assigned_driver as $key => $value) {
                $driver_list[] = (int)$value['id'];
            }
			$match_query['_id'] = array('$nin' => $driver_list);
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
        if($find_count==TRUE){
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
                        'id' => '$_id',
                        'name' => '$name',
                        'userid' => '$company.companydetails.userid',
                        'company_name' => '$company.companydetails.company_name',
                    )
                ),
                array(
                    '$sort' => array('_id' => 1)
                ),
                array(
                    '$skip'=>0
                ),
                array(
                    '$limit' => 10
                )
            );
            $arguments = array_merge($common_arguments,$field_arguments);
            $result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
            //echo "<pre>"; print_r($common_arguments); exit;
            return (!empty($result['result']) && isset($result['result'])) ? $result['result']:array();
        }   
    }    
    public function free_taxi_list($find_count=false)
    {        
        $company_id = (int)$this->company_id;
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
        if (!empty($company_id) && $company_id!=0) {
			$match_query['taxi_company'] = $company_id;
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
        if($find_count==TRUE){
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
                        'userid' => '$company.companydetails.userid',
                        'company_name' => '$company.companydetails.company_name',
                    )
                ),
                array(
                    '$sort' => array('_id' => 1)
                ),
                array(
                    '$skip'=>0
                ),
                array(
                    '$limit' => 10
                )
            );
            $arguments = array_merge($common_arguments,$field_arguments);
            $result    = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
            //echo "<pre>"; print_r($result); exit;
            return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
        }
	}
    
    public function free_availabletaxi_list()
    {
		$company_id  = (int)$this->company_id;
		$cuurentdate = date('Y-m-d H:i:s');
        $enddate     = date('Y-m-d') . ' 23:59:59';
		$match_query = array();
		$match_query['people.status'] = 'A';
		$match_query['mapping.mapping_status'] = 'A';
		if ($company_id!="" && $company_id!=0) {
			$match_query['taxi_company'] = $company_id;
		}
		if ($cuurentdate!="" && $enddate!="") {
			$match_query['mapping.mapping_startdate'] = array('$gte' => $cuurentdate);
			$match_query['mapping.mapping_enddate'] = array('$lt' => $enddate);
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
    
    public function dashboard_driverdetails($cid = '')
    {
		$match = array();
        $match['user_type'] = 'D';
        $match['status'] = 'A'; 
        if($cid != '' && $cid != 0){
            $match['company_id'] = (int)$cid; 
        }
        $args = array(array('$lookup' => array('from' => MDB_COMPANY,
                                               'localField' => '_id',
                                               'foreignField' => 'company.userid',
                                               'as' => 'comapany')),
                        array('$match' => $match),
                        array('$group' => array('_id' => null, 'count' => array(
							'$sum' => 1
						)))
                      );
        $result = $this->mongo_db->aggregate(MDB_PEOPLE,$args);
        //echo '<pre>';print_r($result);exit;
        $res = (!empty($result['result'])) ? "['Driver',".$result['result'][0]['count']."]" : "";
        return $res;
    }    
    public function changedashboard_driverdetails($cid = '', $startdate = '', $enddate = '')
    {
        $match_query = array();
        $match_query['user_type'] = 'M';
        if($cid!="" && $cid!=0){
            $match_query['company_id'] = (int)$cid;    
        }
        if ($startdate!="" && $enddate!="") {
			$match_query['created_date'] = array('$gte' => $startdate);
			$match_query['created_date'] = array('$lte' => $enddate);
		}
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
					'result' => '$_id',
				)
			),
			array(
                  '$group' =>array('_id' => NULL,'count' => array('$sum' => 1),)
			),
		);
		$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
		$result = (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		$result_val   = "";
		if(count( $result ) > 0){
			foreach ($result as $res) {
				 $result_val .= "['Driver', " . $res["count"] . "" . "],";
			}	
		}
		$result = rtrim($result_val, ",");
		//echo "<pre>"; print_r($result); exit;
        return $result;
    }
    
    public function dashboard_managerdetails($cid = '')
    {
		$match = array();
        $match['user_type'] = 'M';
        $match['status'] = 'A'; 
        if($cid != '' && $cid != 0){
            $match['company_id'] = (int)$cid; 
        }
        $args = array(array('$lookup' => array('from' => MDB_COMPANY,
                                               'localField' => '_id',
                                               'foreignField' => 'company.userid',
                                               'as' => 'comapany')),
                        array('$match' => $match),
                        array('$group' => array('_id' => null, 'count' => array(
							'$sum' => 1
						)))
                      );
        $result = $this->mongo_db->aggregate(MDB_PEOPLE,$args);
        $res = (!empty($result['result'])) ? "['Manager',".$result['result'][0]['count']."]" : "";
        return $res;
    }
    
    public function changedashboard_managerdetails($cid = '', $startdate = '', $enddate = '')
    {
        $match_query = array();
        $match_query['user_type'] = 'M';
        if($cid!="" && $cid!=0){
            $match_query['company_id'] = (int)$cid;    
        }
        if ($startdate!="" && $enddate!="") {
			$match_query['created_date'] = array('$gte' => $startdate);
			$match_query['created_date'] = array('$lte' => $enddate);
		}
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
					'result' => '$_id',
				)
			),
			array(
                  '$group' =>array('_id' => NULL,'count' => array('$sum' => 1),)
			),
		);
		$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
		$result = (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		$result_val   = "";
		if(count( $result ) > 0){
			foreach ($result as $res) {
				 $result_val .= "['Manager', " . $res["count"] . "" . "],";
			}	
		}
		$result = rtrim($result_val, ",");
		//echo "<pre>"; print_r($result); exit;
        return $result;
    }
    
    public function dashboard_taxidetails($cid = '')
    {
		$match = array();
        $match['taxi_status'] = 'A'; 
        if($cid != '' && $cid != 0){
            $match['taxi_company'] = (int)$cid; 
        }
        $result = $this->mongo_db->count(MDB_TAXI,$match);
        $res = (!empty($result)) ? '[Taxi,'.$result.']' : '';
        return $res;
    }
    
    public function changedashboard_taxidetails($cid = '', $startdate = '', $enddate = '')
    {
        $match_query = array();
        $match_query['_id'] = array('$gt' => 0);
        if($cid!="" && $cid!=0){
            $match_query['taxi_company'] = (int)$cid;    
        }
        if ($startdate!="" && $enddate!="") {
			$match_query['taxi_createdate'] = array('$gte' => $startdate);
			$match_query['taxi_createdate'] = array('$lte' => $enddate);
		}
		$arguments = array(
			array(
				'$match' => $match_query
			),
			array('$project' => array('result' => '$_id')),
			array('$group' =>array('_id' => NULL,'count' => array('$sum' => 1))),
		);
		$result    = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
		$result = (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		$result_val   = "";
		if(count( $result ) > 0){
			foreach ($result as $res) {
				 $result_val .= "['Taxi', " . $res["count"] . "" . "],";
			}	
		}
		$result = rtrim($result_val, ",");
		//echo "<pre>"; print_r($result); exit;
        return $result;
    }
  
    public function transactionbycompanydriver($cid = '')
    {
        $match_query = array();
        $match_query['people.user_type'] = 'D';
        if($cid!="" && $cid!=0){
            $match_query['people.company_id'] = (int)$cid;    
        }
		$arguments = array(
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
				'$project' => array(
                    'driver_id' => '$driver_id',
                    'name' => '$people.name',
				)
			),
			array(
                  '$group' =>array('_id' => array('driver_id' => '$driver_id','name' => '$name'),'count' => array('$sum' => 1))
			),
		);
		$result    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $arguments);
		$result = (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		$result_val   = "";
		if(count( $result ) > 0){
			foreach ($result as $res) {   
				$result_val .= "['" . $res['_id']["name"] . "', " . $res["count"] . "" . "],";
			}	
		}
		$result = rtrim($result_val, ",");
		//echo "<pre>"; print_r($result); exit;
        return $result;
    }
    
    /**Validating Fund Request Form**/
    public function validate_fundrequest_amount($arr)
    {
        $total_pending_request = $this->fund_req_calc();
        return Validation::factory($arr)->rule('amount', 'not_empty')->rule('amount', 'max_length', array(
            ':value',
            '3'
        ))->rule('amount', 'numeric')->rule('amount', 'must_greaterthan', array(
            $arr['amount'],
            MIN_FUND
        ))->rule('amount', 'must_lessertotal', array(
            $arr['amount'],
            $total_pending_request
        ))->rule('amount', 'must_lesserthan', array(
            $arr['amount'],
            MAX_FUND
        ));
    }
   
    //company total current balance
    public function company_tot_current_bal($user_id)
    {
       // ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'account_balance'=>1                                 
            ],
            'sort'=>[
                '_id'=>-1
            ]            
        ];
        $result = $this->mongo_db->find(MDB_PEOPLE,['_id'=>(int)$user_id,'user_type' => 'C'],$options);
        return (!empty($result))?$result:array();
    }
    public function admin_tot_current_bal()
    {
        // ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'account_balance'=>1                                 
            ],
            'sort'=>[
                '_id'=>1
            ]            
        ];
        $result = $this->mongo_db->find(MDB_PEOPLE,['status'=>'A','user_type' => 'A'],$options);
        return (!empty($result))?$result:array();
    }
	
	public function validate_updatesiteinfo($arr="")
	{
		//echo '<pre>';print_r($arr);exit;
					return Validation::factory($arr)     
					->rule('company_tax', 'not_empty')
					->rule('company_tax', 'numeric')
					->rule('company_tax', 'Model_Company::check_percentage', array(':value'))
					->rule('default_unit','not_empty')
					->rule('skip_credit_card','not_empty')
					->rule('fare_calculation','not_empty')
					->rule('cancellation_fare','not_empty')
					->rule('company_copyrights','not_empty')
					->rule('user_time_zone','not_empty')
					->rule('date_time_format','not_empty')
					->rule('company_logo', 'Upload::size',array($arr['company_logo'],IMG_MAX_SIZE))
					->rule('company_logo', 'Upload::type', array(':value', array('jpeg','jpg','png','gif')))
					->rule('email_site_logo', 'Upload::size',array($arr['email_site_logo'],IMG_MAX_SIZE))
					->rule('email_site_logo', 'Upload::type', array(':value', array('jpeg','jpg','png','gif')))
					->rule('company_favicon', 'Upload::size',array($arr['company_favicon'],IMG_MAX_SIZE))
					->rule('company_favicon', 'Upload::type', array(':value', array('jpeg','jpg','png','gif')))
					->rule('driver_commission','not_empty')
					->rule('driver_commission','numeric');
					/*->rule('customer_app_android', 'not_empty')
					->rule('customer_app_android', 'max_length', array(':value', '450'))
					->rule('customer_app_android', 'url')
					
					->rule('customer_app_ios', 'not_empty')
					->rule('customer_app_ios', 'max_length', array(':value', '450'))
					->rule('customer_app_ios', 'url')
		
					
					
					->rule('driver_app_android', 'not_empty')
					->rule('driver_app_android', 'max_length', array(':value', '450'))
					->rule('driver_app_android', 'url')
					
					->rule('app_name', 'not_empty')
					->rule('app_name', 'max_length', array(':value', '250'))
					
					->rule('company_tagline', 'not_empty')
					->rule('company_tagline', 'max_length', array(':value', '50'))
					
					->rule('app_description', 'not_empty')
					
					->rule('contact_email','not_empty')
					->rule('contact_email', 'max_length', array(':value', '30'))
					->rule('contact_email', 'email')     
					
					->rule('phone_number','not_empty')
					->rule('phone_number', 'max_length', array(':value', '30'))
					
					//->rule('meta_keyword','not_empty')
					
					//->rule('meta_description','not_empty')
					
					//->rule('site_country','not_empty')
					
					->rule('notification_settings','not_empty')
					->rule('notification_settings','numeric')


					/*->rule('site_city','not_empty')
					
					->rule('file', 'Upload::not_empty',array($files_value_array['site_logo']))
		->rule('file', 'Upload::type', array($files_value_array['site_logo'], array('jpg','jpeg', 'png', 'gif')))
		->rule('file', 'Upload::size', array($files_value_array['site_logo'],'2M'))
					
					
					
					//->rule('company_currency','not_empty')
					->rule('sms_enable','not_empty')
					
					->rule('passenger_setting','not_empty')
					->rule('home_page_title','not_empty')
					->rule('home_page_title', 'max_length', array(':value', '80'))
					->rule('home_page_content','not_empty')
					->rule('default_unit','not_empty')
					->rule('fare_calculation','not_empty')
					->rule('skip_credit_card','not_empty');*/

	}
    /** site logo **/
    public function updatesiteinfo_image($image, $cid)
    {
        //$result = $this->mongo_db->find(MDB_COMPANY, array("_id" => (int) $cid), array("companyinfo.company_logo"));
         // ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'companyinfo.company_logo'=>1                                 
            ]       
        ];
        $result = $this->mongo_db->find(MDB_COMPANY, ["_id" => (int) $cid],$options);
        $res    = $result;
        if (!empty($res[$cid]['companyinfo']['company_logo'])) {
            $id1 = $res[$cid]['companyinfo']['company_logo'];
            if (file_exists($id1)) {
                unlink($id1);
            }
        }
        $query  = array(
            'companyinfo.company_logo' => $image
        );
     
        $result = $this->mongo_db->updateOne(MDB_COMPANY, array(
            '_id' => (int) $cid
        ), array(
            '$set' => $query
        ), array(
            'upsert' => false
        ));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    /** site favicon image **/
    /** site logo **/
    public function updatesiteinfo_faviconimage($image, $cid)
    {
        //$result = $this->mongo_db->find(MDB_COMPANY,array("_id" => (int) $cid), array("companyinfo.company_favicon"));
          // ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'companyinfo.company_favicon'=>1                                 
            ]       
        ];
        $result = $this->mongo_db->find(MDB_COMPANY,["_id" => (int) $cid],$options);
        $res    = $result;
        if (!empty($res[$cid]['companyinfo']['company_favicon'])) {
            $id1 = $res[$cid]['companyinfo']['company_favicon'];
            if (file_exists($id1)) {
                unlink($id1);
            }
        }
        $query  = array(
            'companyinfo.company_favicon' => $image
        );
        //MongoDB
        $result = $this->mongo_db->updateOne(MDB_COMPANY, array(
            '_id' => (int) $cid
        ), array(
            '$set' => $query
        ), array(
            'upsert' => false
        ));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
   
    public function updatesiteinfo($post_value_array, $cid)
    {
		//print_r($post_value_array); exit; 
		$date_time_format_script = "yy-mm-dd hh:mm:ss";
        $query  = array(
            'companyinfo.company_tax' => (double)$post_value_array['company_tax'],
            'companyinfo.default_unit' => (int)$post_value_array['default_unit'],
            'companyinfo.fare_calculation_type' => $post_value_array['fare_calculation'],
            'companyinfo.cancellation_fare' => (double)$post_value_array['cancellation_fare'],
            'companyinfo.company_copyrights' => $post_value_array['company_copyrights'],
            'companyinfo.skip_credit_card' => (int)$post_value_array['skip_credit_card'],            
            'companydetails.date_time_format' => $post_value_array['date_time_format'],
            'companydetails.user_time_zone' => $post_value_array['user_time_zone'],
            'companydetails.date_time_format_script' => $date_time_format_script,
            'companydetails.driver_commission' => (double)$post_value_array['driver_commission'],
        );
		//echo '<pre>';print_r($post_value_array);exit;
        $result = $this->mongo_db->updateOne(MDB_COMPANY, array(
            '_id' => (int) $cid
        ), array(
            '$set' => $query
        ), array(
            'upsert' => false
        ));
        //echo '<pre>';print_r($cid);exit;
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    
    public function site_settings($id = "")
	{
		$temp_arr = $result = array();
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'companydetails.time_zone'=>1,
                'companydetails.date_time_format'=>1,
                'companydetails.date_time_format_script'=>1,
                'companydetails.driver_commission'=>1,
                'companyinfo.cancellation_fare'=>1,
                'companyinfo.fare_calculation_type'=>1,
                'companyinfo.company_tax'=>1, 
                'companyinfo.company_copyrights'=>1,
                'companyinfo.company_logo'=>1,
                'companyinfo.company_favicon'=>1,
                'companyinfo.default_unit'=>1,
                'companyinfo.skip_credit_card'=>1,
                'companyinfo.cancellation_fare'=>1                              
            ]       
        ];
        $results = $this->mongo_db->find(MDB_COMPANY,["_id" => (int)$id],$options);
        $res1     = $results;
        $res1    = commonfunction::change_key($res1);
        if(!empty($res1)){
			$res = $res1[0];
			$temp_arr['user_time_zone'] = isset($res['companydetails']['time_zone']) ? $res['companydetails']['time_zone']:'';
			$temp_arr['date_time_format'] = isset($res['companydetails']['date_time_format']) ? $res['companydetails']['date_time_format']:'';
			$temp_arr['date_time_format_script'] = isset($res['companydetails']['date_time_format_script']) ? $res['companydetails']['date_time_format_script']:'';
			$temp_arr['driver_commission'] = isset($res['companydetails']['driver_commission']) ? $res['companydetails']['driver_commission']:'';
			$temp_arr['cancellation_fare'] = isset($res['companyinfo']['cancellation_fare']) ? $res['companyinfo']['cancellation_fare']:'';
			$temp_arr['company_cancellation'] = isset($res['companyinfo']['cancellation_fare']) ? $res['companyinfo']['cancellation_fare']:'';
			$temp_arr['fare_calculation_type'] = isset($res['companyinfo']['fare_calculation_type']) ? $res['companyinfo']['fare_calculation_type']:'';
			$temp_arr['company_tax'] = isset($res['companyinfo']['company_tax']) ? $res['companyinfo']['company_tax']:'';
			$temp_arr['company_copyrights'] = isset($res['companyinfo']['company_copyrights']) ? $res['companyinfo']['company_copyrights']:'';
			$temp_arr['company_logo'] = isset($res['companyinfo']['company_logo']) ? $res['companyinfo']['company_logo']:'';
			$temp_arr['company_favicon'] = isset($res['companyinfo']['company_favicon']) ? $res['companyinfo']['company_favicon']:'';
			$temp_arr['default_unit'] = ($res['companyinfo']['default_unit']==1) ? $res['companyinfo']['default_unit']:0;
			$temp_arr['skip_credit_card'] = isset($res['companyinfo']['skip_credit_card']) ? $res['companyinfo']['skip_credit_card']:0;
			$temp_arr['cancellation_fare'] = isset($res['companyinfo']['cancellation_fare']) ? $res['companyinfo']['cancellation_fare']:0;
			$result[] = $temp_arr;
		}
		//echo '<pre>';print_r($results);exit;        
        return $result;
    }
    
    public function validate_update_socialinfo($arr = "")
    {
        return Validation::factory($arr)->rule('facebook_key', 'not_empty')->rule('facebook_secretkey', 'not_empty')->rule('facebook_share', 'not_empty')->rule('twitter_share', 'not_empty')->rule('google_share', 'not_empty')->rule('linkedin_share', 'not_empty');
    }
    public function validate_update_layoutinfo($arr = "")
    {
        return Validation::factory($arr)->rule('header_bg', 'not_empty')->rule('menu_bg', 'not_empty')->rule('mover_bg', 'not_empty');
    }
    
    /** validating the banners images **/
    public function validate_update_module($arr = "", $files_value_array = "")
    {
        return Validation::factory($arr) /*->rule('member', 'not_empty')
        ->rule('member', 'max_length', array(':value', '2'))*/ ->rule('file', 'Upload::not_empty', array(
            $files_value_array['banner_image1']
        ))->rule('file', 'Upload::type', array(
            $files_value_array['banner_image1'],
            array(
                'jpg',
                'jpeg',
                'png',
                'gif'
            )
        ))->rule('file', 'Upload::size', array(
            $files_value_array['banner_image1'],
            '2M'
        ))->rule('file', 'Upload::not_empty', array(
            $files_value_array['banner_image2']
        ))->rule('file', 'Upload::type', array(
            $files_value_array['banner_image2'],
            array(
                'jpg',
                'jpeg',
                'png',
                'gif'
            )
        ))->rule('file', 'Upload::size', array(
            $files_value_array['banner_image2'],
            '2M'
        ))->rule('file', 'Upload::not_empty', array(
            $files_value_array['banner_image3']
        ))->rule('file', 'Upload::type', array(
            $files_value_array['banner_image3'],
            array(
                'jpg',
                'jpeg',
                'png',
                'gif'
            )
        ))->rule('file', 'Upload::size', array(
            $files_value_array['banner_image3'],
            '2M'
        ))->rule('file', 'Upload::not_empty', array(
            $files_value_array['banner_image4']
        ))->rule('file', 'Upload::type', array(
            $files_value_array['banner_image4'],
            array(
                'jpg',
                'jpeg',
                'png',
                'gif'
            )
        ))->rule('file', 'Upload::size', array(
            $files_value_array['banner_image4'],
            '2M'
        ))->rule('file', 'Upload::not_empty', array(
            $files_value_array['banner_image5']
        ))->rule('file', 'Upload::type', array(
            $files_value_array['banner_image5'],
            array(
                'jpg',
                'jpeg',
                'png',
                'gif'
            )
        ))->rule('file', 'Upload::size', array(
            $files_value_array['banner_image5'],
            '2M'
        ));
    }
    public function get_company_info($cid)
    {
        $result = array();        
        $args = array(
					array('$match' => array('_id' => (int)$cid)),
					array('$lookup' 		=> array(
							'from'			=>	MDB_PEOPLE,
							'localField'	=> 'companydetails.userid',
							'foreignField'	=> '_id',
							'as'			=> 'people')),
					array('$unwind' => array('path' => '$people', 'preserveNullAndEmptyArrays' => true )),
					array('$project' => array('company_mail' => '$people.email',
											'company_domain' => '$companyinfo.company_domain'))			
				);        
        $res = $this->mongo_db->Aggregate(MDB_COMPANY, $args);
        //echo '<pre>';print_r($res);exit;
        if(!empty($res['result'])){
			
			$response = $res['result'][0];
			$result['company_domain'] = isset($response['company_domain'])?$response['company_domain']:'';
			$result['company_mail'] = isset($response['company_mail'])?$response['company_mail']:'';
		}
		return $result;
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        /*$options=[
            'projection'=>[
                'companyinfo.company_domain'=>1,                                             
            ]       
        ];        
        $res = $this->mongo_db->find(MDB_COMPANY, array('_id' => (int)$cid), $options);
        $res = commonfunction::change_key($res);
        if(!empty($res)){
			$result[]['company_domain'] = isset($res[0]['companyinfo']['company_domain'])?$res[0]['companyinfo']['company_domain']:'';
		}
		return $result;*/
    }
    // List Company Users
    public function count_passenger_list_history($cid)
    {
        $offset = '';
        $val = '';
        $find_count = true;
        $result = $this->all_passenger_list_history($cid,$offset, $val, $find_count);
        return $result;
    }
    /** passenger list **/
    public function all_passenger_list_history($cid, $offset, $val, $find_count = FALSE)
    {
		
		$result = array();$count=0;
		//echo $offset ."-". $val;exit;
		$arguments = array(
            array('$match'	=> array('company_id' => (int)$cid)),
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
			array('$group' => array('_id' => array('pass_id' => '$id',
													'name' => '$name',
													'email' => '$email',
													'phone' => '$phone',
													'address' => '$address',
													'created_date' => '$created_date',
													'user_status' => '$user_status'
													)
				))
        );
        
        
        if ($find_count == FALSE) {
			$arguments[]['$skip'] = (int)$offset;
			$arguments[]['$limit'] = (int)$val;
        } 
        
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS ,$arguments);
		//echo '<pre>';print_r($arguments);exit;	
            if(!empty($res['result'])){
				foreach($res['result'] as $k => $v){
					$datas = $v['_id'];
					$result[$k]['id'] = isset($datas['pass_id'])?$datas['pass_id']:"";
					$result[$k]['name'] = isset($datas['name'])?$datas['name']:"";
					$result[$k]['email'] = isset($datas['email'])?$datas['email']:"";
					$result[$k]['phone'] = isset($datas['phone'])?$datas['phone']:"";
					$result[$k]['address'] = isset($datas['address'])?$datas['address']:'';
					$result[$k]['user_status'] = isset($datas['user_status'])?$datas['user_status']:"";
					$result[$k]['created_date'] = isset($datas['created_date'])?commonfunction::convertphpdate('Y-m-d H:i:s',$datas['created_date']):"";
				}				
			}
			//print_r($_SESSION); exit; 
			
            return $result;
    }
    
    public function count_passengersearch_list( $keyword = "", $status = "", $company = "" )
    {
		$offset = $val = "";
		$count = $this->get_all_searchpassenger_list($keyword, $status, $company, $offset,$val,true);
		return $count;
    }
    /** for getting passenger listing search **/
    public function get_all_searchpassenger_list($keyword = "", $status = "", $company = "", $offset = "", $val = "", $find_count = FALSE)
    {
        $keyword    = str_replace("%", "!%", $keyword);
        $keyword    = str_replace("_", "!_", $keyword);
        $result = array();
        $srch_query = array('company_id' => (int)$company);
        if (!empty($keyword)){
			$srch_query['$or'] = array(
										array('passengers.name' => Commonfunction::MongoRegex("/$keyword/i")),
										array('passengers.email' => Commonfunction::MongoRegex("/$keyword/i"))
									);                    
		}
        if(!empty($status)){
            $srch_query['passengers.user_status'] = $status;
        }
		
		$result = array();$count=0;
		$arguments = array(            
            array('$lookup' 		=> array(
                    'from'			=>	MDB_PASSENGERS,
                    'localField'	=> 'passengers_id',
                    'foreignField'	=> '_id',
                    'as'			=> 'passengers'
			)),
            array('$unwind'=>'$passengers'),
            array('$match'	=> $srch_query),
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
			array('$group' => array('_id' => array('pass_id' => '$id',
													'name' => '$name',
													'email' => '$email',
													'phone' => '$phone',
													'address' => '$address',
													'created_date' => '$created_date',
													'user_status' => '$user_status'
													)
				))
        );        
        
        if ($find_count != TRUE) {
			$arguments[]['$skip'] = (int)$offset;
			$arguments[]['$limit'] = (int)$val;
        }
        $res = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS ,$arguments);
        if(!empty($res['result'])){
			foreach($res['result'] as $k => $v){
				$datas = $v['_id'];
				$result[$k]['id'] = $datas['pass_id'];
				$result[$k]['name'] = $datas['name'];
				$result[$k]['email'] = $datas['email'];
				$result[$k]['phone'] = $datas['phone'];
				$result[$k]['address'] = isset($datas['address'])?$datas['address']:'';
				$result[$k]['user_status'] = $datas['user_status'];
				$result[$k]['created_date'] = isset($datas['created_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$datas['created_date']) : '';
			}				
		}
		//echo '<pre>';print_r($arguments);exit;
		return $result;
        
    }
    public function get_country(){ }
    
    /*public function save_moderator_trial($post)
    {
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                '_id'=>1,                                             
            ],
            'sort'=>[
                '_id'=>-1,                                             
            ],
            'limit'=>1
        ];
        $rs = $this->mongo_db->find(MDB_GET_FREE_QUOTES,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $inc_id = $first_key+1;
        $message = " (FREE TRAIL REQUEST)   " . ucfirst($post['message']);
        $insert_array = array(
            '_id' => (int)$inc_id,
            'name' => $post['name'],
            'email' => trim($post['email']),
            'mobile_no' => $post['phone'],
            'company_name' => $post['company_name'],
            'no_of_taxi' => (int)$post['no_of_taxi'],
            'country_name' => $post['country'],
            'city_name' => $post['city'],
            'message' => $message,
            'createdate' => Commonfunction::MongoDate(strtotime($post['createdate']))
        );
        $result = $this->mongo_db->insertOne(MDB_GET_FREE_QUOTES,$insert_array);
        return (empty($result->getwriteErrors())? 1 : 0);
    }
    */
    public function validate_update_smssettings($arr = "")
    {
        return Validation::factory($arr)->rule('sms_account_id', 'not_empty')->rule('sms_auth_token', 'not_empty')->rule('sms_from_number', 'not_empty');
    }
    public function get_admin_dashboard_data($company_id)
    {
        $result["general_users"] = $this->mongo_db->count(MDB_PASSENGERS,array('user_status' => 'A'),array('_id'));        
        $result["driver"] = $this->mongo_db->count(MDB_PEOPLE,array('user_type' => 'D','status' => 'A','company_id' => (int)$company_id),array('_id'));        
        $result["manager"] = $this->mongo_db->count(MDB_PEOPLE,array('company_id' => (int)$company_id,'user_type' => 'M','status' => 'A'),array('_id'));
        $result["taxi"] = $this->mongo_db->count(MDB_TAXI,array('taxi_status' => 'A','taxi_company' => (int)$company_id),array('_id'));
        $result["country"] = $this->mongo_db->count(MDB_CSC,array('country_status' => 'A'),array('_id'));
        $arguments = array(array('$unwind' => '$stateinfo'),
                array('$match'=> array('stateinfo.state_status' => 'A')),
                array('$project' => array('id' 	=> '$stateinfo.state_id')),
                array('$group' =>array('_id' => NULL,'count' => array('$sum' => 1))),
        );
        $state_count = $this->mongo_db->aggregate(MDB_CSC,$arguments);
        $result["state"] = (isset($state_count['result'][0]['count']))?$state_count['result'][0]['count']:0;
		$arguments = array(array('$unwind' => '$stateinfo'),array('$unwind' => '$stateinfo.cityinfo'),
			array('$match'=> array('stateinfo.cityinfo.city_status' => 'A')),
			array('$project' => array('id' 	=> '$stateinfo.cityinfo.city_id')),
			array('$group' =>array('_id' => NULL,'count' => array('$sum' => 1))),
		);
		$city_count = $this->mongo_db->aggregate(MDB_CSC,$arguments);
        $result["city"] = (isset($city_count['result'][0]['count']))?$city_count['result'][0]['count']:0;
        return $result;
    }
    //dashboard active users count
    public function get_activeusers_list_count($company_id)
    {
        $results = $this->mongo_db->count(MDB_PASSENGERS,array('login_status' => 'A','passenger_cid'=> $company_id),array('_id'));
        return $results;
    }
    public static function addcompanydetails($post)
    {
        $mongo_db        = MangoDB::instance('default');
        $password        = Html::chars(md5('qwerty'));
        $upgrade_package = 'N';
        $upgrade_packid  = 5;
        $taxi_type       = 1;
        $taxi_model      = 1;
        //print_r($post);
        //exit;
        if ($post['time_zone']) {
            $current_time = convert_timezone('now', $post['time_zone']);
            $current_date = explode(' ', $current_time);
            $start_time   = $current_date[0] . ' 00:00:01';
            $end_time     = $current_date[0] . ' 23:59:59';
        } else {
            $current_time = $this->currentdate_bytimezone;
            $start_time   = date('Y-m-d') . ' 00:00:01';
            $end_time     = date('Y-m-d') . ' 23:59:59';
        }
        $add_company  = Model::factory('add');
        $current_date = convert_timezone('now', $post['time_zone']);
        $current_date = Commonfunction::MongoDate(strtotime($current_date));
        
        $current_time = Commonfunction::MongoDate(strtotime($current_time));
        $start_time = Commonfunction::MongoDate(strtotime($start_time));
        $end_time = Commonfunction::MongoDate(strtotime($end_time));
        $country_id   = DEFAULT_COUNTRY; //$post['country'];//DEFAULT_COUNTRY
        $state_id     = DEFAULT_STATE; //$post['state'];//DEFAULT_STATE
        $city_id      = DEFAULT_CITY; //$post['city'];//DEFAULT_CITY
        if ($post['type'] == 2) {
            $post['g_name']  = ($post['name']) ? $post['name'] : $post['g_name'];
            $post['g_email'] = ($post['email']) ? $post['email'] : $post['g_email'];
            $post['g_phone'] = ($post['phone']) ? $post['phone'] : $post['g_phone'];
        }
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                '_id'=>1,                                             
            ],
            'sort'=>[
                '_id'=>-1,                                             
            ],
            'limit'=>1
        ];
        $rs = $mongo_db->find(MDB_PEOPLE,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $user_id = $first_key+1;
        $insert_people = array(
            '_id' => (int)$user_id,
            'name' => $post['g_name'],
            'address' => null,
            'lastname' => null,
            'email' => trim($post['g_email']),
            'paypal_account' => null,
            'phone' => $post['g_phone'],
            'password' => $password,
            //'org_password' => 'qwerty',
            'login_country' => (int)$country_id,
            'login_state' => (int)$state_id,
            'login_city' => (int)$city_id,
            'created_date' => $current_date,
            'user_type' => 'C',
            'status' => 'A'
        );
        $result = $mongo_db->insertOne(MDB_PEOPLE,$insert_people);
        
        //$rs = $mongo_db->find(MDB_COMPANY,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                '_id'=>1,                                             
            ],
            'sort'=>[
                '_id'=>-1,                                             
            ],
            'limit'=>1
        ];
        $rs = $mongo_db->find(MDB_COMPANY,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $company_id = $first_key+1;
        $company_userid            = $user_id;
        $reg_companyid             = $company_id;
        $company_data = array(
            '_id' => (int)$company_id,
            'company_name' => $post['company_name'],
            'company_address' => null,
            'company_country' => (int)$country_id,
            'company_state' => (int)$state_id,
            'company_city' => (int)$city_id,
            'userid' => (int)$company_userid,
            'time_zone' => $post['time_zone'],
            'header_bgcolor' => '#FFFFFF',
            'menu_color' => '#000000',
            'mouseover_color' => '#FFD800'
        );		
        $key                       = "";
        $charset                   = "abcdefghijklmnopqrstuvwxyz";
        $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charset .= "0123456789";
        $length = mt_rand(30, 35);
        for ($i = 0; $i < $length; $i++)
            $key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];
            
        $company_info_data = array(
            'company_cid' => (int)$reg_companyid,
            'company_domain' => trim($post['domain_name']),
            'company_phone_number' => '9000000000',
            'company_app_name' => $post['company_name'],
            'company_currency' => '$',
            'company_currency_format' => 'USD',
            'payment_method' => 'T',
            'company_paypal_username' => 'nandhu_1337947987_biz_api1.gmail.com',
            'company_paypal_password' => '1337948040',
            'company_paypal_signature' => 'A0YqGlJEML24al4qg2LnV2U.g2ThAfXD37NEiWIVcgjl1pxlygg-XaVs',
            'company_notification_settings' => '20',
            'company_api_key' => $key
        );
        
        //$rs = $mongo_db->find(MDB_COMPANY,array(),array('dispatch_algorithm.aid'))->sort(array('dispatch_algorithm.aid'=>-1))->limit(1);
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'dispatch_algorithm.aid'=>1,                                             
            ],
            'sort'=>[
                'dispatch_algorithm.aid'=>-1,                                             
            ],
            'limit'=>1
        ];
        $rs = $mongo_db->find(MDB_COMPANY,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        $res_id = (isset($res)?reset($res):array());
        $first_key = (!empty($res_id) ? (isset($res_id['dispatch_algorithm'][0]['aid'])?$res_id['dispatch_algorithm'][0]['aid']:0) : 0);
        $a_id = $first_key+1;
        $dispatch_algorithm_data = array();
        $dispatch_algorithm_data[] = array(
            'aid' => (int)$a_id,
            'labelname' => 2,
            'alg_created_by' => (int)$company_userid,
            'alg_company_id' => (int)$reg_companyid,
            'active' => 1,
            'hide_customer' => 0,
            'hide_droplocation' => 0,
            'hide_fare' => 0
        );        
        $pages    = array(
                'About us',
                'Privacy policy',
                'Servicing for Excellence',
                'Terms and Conditions',
                'Help'
            );
            $page_url = array(
                'aboutus',
                'privacypolicy',
                'service-area',
                'termsconditions',
                'help'
            );
        $cms_data = array();
           for ($i = 0; $i < 5; $i++) {
               $cms_data[] = array(
                   'menu_name' => $pages[$i],
                   'title' => $pages[$i],
                   'content' => $pages[$i],
                   'page_url' => $page_url[$i],
                   'type'=>1
               );
           }
        
        $adminmodeldata = $mongo_db->findOne(MDB_MOTOR_MODEL,array('_id'=>(int)$taxi_model));
        if (count($adminmodeldata) > 0) {
            foreach ($adminmodeldata as $values) {
                $model_id          = (isset($values['model_id'])?$values['model_id']:'');
                $model_name        = (isset($values['model_name'])?$values['model_name']:'');
                $motor_mid         = (isset($values['motor_mid'])?$values['motor_mid']:'');
                $base_fare         = (isset($values['base_fare'])?$values['base_fare']:'');
                $min_km            = (isset($values['min_km'])?$values['min_km']:'');
                $min_fare          = (isset($values['min_fare'])?$values['min_fare']:'');
                $cancellation_fare = (isset($values['cancellation_fare'])?$values['cancellation_fare']:'');
                $below_above_km    = (isset($values['below_above_km'])?$values['below_above_km']:'');
                $below_km          = (isset($values['below_km'])?$values['below_km']:'');
                $above_km          = (isset($values['above_km'])?$values['above_km']:'');
                $night_charge      = (isset($values['night_charge'])?$values['night_charge']:'');
                $night_timing_from = (isset($values['night_timing_from'])?$values['night_timing_from']:'');
                $night_timing_to   = (isset($values['night_timing_to'])?$values['night_timing_to']:'');
                $night_fare        = (isset($values['night_fare'])?$values['night_fare']:'');
                $waiting_time      = (isset($values['waiting_time'])?$values['waiting_time']:'');
            }            
        }
        $company_model = array(
             'model_id' => (int)$model_id,
             'company_cid' => (int)$reg_companyid,
             'motor_mid' => (int)$motor_mid,
             'base_fare' => (float)$base_fare,
             'min_fare' => (float)$min_fare,
             'cancellation_fare' =>  (float)$cancellation_fare,
             'below_km' => (float)$below_km,
             'above_km' => (float)$above_km,
             'night_charge' => $night_charge,
             'night_timing_from' => $night_timing_from,
             'night_timing_to' => $night_timing_to,
             'night_fare' => $night_fare,
             'min_km' => $min_km,
             'below_above_km' => $below_above_km,
             'waiting_time' => $waiting_time,
             'model_name'  => $model_name                   
         );
        $payment_module_data = array();
        $company_insert = array('_id' => (int)$reg_companyid,
                        'companydetails'=> $company_data,
                        'companyinfo' => $company_info_data,
                        'dispatch_algorithm'=> $dispatch_algorithm_data,
                        'company_cms' => $cms_data,
                        'paymentmodule'=> $payment_module_data,
                        'model_fare' => $company_model
                    );
        $result_comp = $mongo_db->insertOne(MDB_COMPANY,$company_insert);
        
        $update_people = $mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$company_userid),array('$set'=>array('company_id'=>(int)$reg_companyid)));       
        // Convert Time
        $domain        = trim($post['domain_name']);
        $referral_code = "";
        /** Referral key generator  $country_id,$state_id,$city_id **/
        $length        = ($post['no_of_taxi']) ? $post['no_of_taxi'] : 3;
        for ($i = 1; $i <= $length; $i++) {
            /***** Driver login creation ************/
            $driver_email    = $domain . $i . '@' . $domain . '.com';
            $driver_name     = $domain . $i;
            $driver_mobile   = $reg_companyid . date('ymd') . $i;
            
            //$rs = $mongo_db->find(MDB_PEOPLE,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
              ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,                                             
                    ],
                    'sort'=>[
                        '_id'=>-1,                                             
                    ],
                    'limit'=>1
                ];
            $rs = $mongo_db->find(MDB_PEOPLE,[],$options);
            $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
            reset($res);
            $first_key = key($res);
            $inc_id = $first_key+1;
            $in_people = array(
                '_id' => (int)$inc_id,
                'name' => $driver_name,
                'email' => $driver_email,
                'password' => md5("qwerty"),
                //'org_password' => 'qwerty',
                'otp' => null,
                'phone' => $driver_mobile,
                'driver_referral_code' => $referral_code,
                'user_type' => 'D',
                'status' => 'A',
                'login_city' => (int)$city_id,
                'login_state' => (int)$state_id,
                'login_country' => (int)$country_id,
                'created_date' => $current_time,
                'updated_date' => $current_time,
                'company_id' => (int)$reg_companyid,
                'booking_limit' => 100
            );
            $result_people = $mongo_db->insertOne(MDB_PEOPLE,$in_people);
            $driver_id       = $inc_id;
            
            /***** Passenger login creation ************/
            //$rs = $mongo_db->find(MDB_PASSENGERS,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,                                             
                    ],
                    'sort'=>[
                        '_id'=>-1,                                             
                    ],
                    'limit'=>1
                ];
                $rs = $mongo_db->find(MDB_PASSENGERS,[],$options);
            $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
            reset($res);
            $first_key = key($res);
            $inc_id = $first_key+1;
            $insert_pass = array(
                '_id' => (int)$inc_id,
                'name' => $driver_name,
                'salutation' => 'Mr',
                'lastname' => $driver_name,
                'email' => $driver_email,
                'password' => md5("qwerty"),
                //'org_password' => 'qwerty',
                'phone' => $driver_mobile,
                'activation_status' => 1,
                'user_status' => 'A',
                'created_date' => $current_time,
                'updated_date' => $current_time,
                'passenger_cid' => (int)$reg_companyid,
                'skip_credit_card' => 1
            );
            $result_pass = $mongo_db->insertOne(MDB_PASSENGERS,$insert_pass);
            /***** Taxi  creation ************/
            //$rs = $mongo_db->find(MDB_TAXI,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,                                             
                    ],
                    'sort'=>[
                        '_id'=>-1,                                             
                    ],
                    'limit'=>1
                ];
                $rs = $mongo_db->find(MDB_TAXI,[],$options);
            $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
            reset($res);
            $first_key = key($res);
            $taxi_id = $first_key+1;
            $taxi_image             = 'taxi_' . $i . '.png';
            $insert_taxi = array(
                '_id' => (int)$taxi_id,
                'taxi_no' => $domain . $i,
                'taxi_type' => (int)$taxi_type,
                'taxi_model' => (int)1,
                'taxi_company' => (int)$reg_companyid,
                'taxi_country' => (int)$country_id,
                'taxi_image' => $taxi_image,
                'taxi_state' => (int)$state_id,
                'taxi_city' => (int)$city_id,
                'taxi_capacity' => 5,
                'taxi_speed' => 100
            );
            $result_taxi = $mongo_db->insertOne(MDB_TAXI,$insert_taxi);
            /***** Driver location creation ************/
            //$rs = $mongo_db->find(MDB_DRIVER_INFO,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                    '_id'=>1,                                             
                ],
                'sort'=>[
                    '_id'=>-1,                                             
                ],
                'limit'=>1
            ];
            $rs = $mongo_db->find(MDB_DRIVER_INFO,[],$options);
            $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
            reset($res);
            $first_key = key($res);
            $inc_id = $first_key+1;          
            $driverinfo = array();
            $driverinfo[] = array('driver_id' => (int)$driver_id);            
            $dt = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('UTC'));
			$ts = $dt->getTimestamp();
			$today = Commonfunction::MongoDate($ts);
			$insert_data = array(
				'_id' => (int)$inc_id,
				'status'=>'F',
				'shift_status' => 'OUT',
				'update_date' => $today,
				'loc'=>array('type' => 'Point', 'coordinates'=>array((double)LOCATION_LONG,(double)LOCATION_LATI)),
                'driverinfo' => $driverinfo
			);
			$result = $mongo_db->insertOne(MDB_DRIVER_INFO,$insert_data);
            /** Insert Taxi Mappning Details *******/
            //$rs = $mongo_db->find(MDB_TAXI_DRIVER_MAPPING,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                    '_id'=>1,                                             
                ],
                'sort'=>[
                    '_id'=>-1,                                             
                ],
                'limit'=>1
            ];
            $rs = $mongo_db->find(MDB_TAXI_DRIVER_MAPPING,[],$options);
            $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
            reset($res);
            $first_key = key($res);
            $inc_id = $first_key+1;
            $insert_mapping = array(
                '_id' => (int)$inc_id,
                'mapping_driverid' => (int)$driver_id,
                'mapping_taxiid' => (int)$taxi_id,
                'mapping_companyid' => (int)$reg_companyid,
                'mapping_countryid' => (int)$country_id,
                'mapping_stateid' => (int)$state_id,
                'mapping_cityid' => (int)$city_id,
                'mapping_startdate' => $start_time,
                'mapping_enddate' => $expirydate,
                'mapping_createdby' => 1
            );
            $result_mapping = $mongo_db->insertOne(MDB_TAXI_DRIVER_MAPPING,$insert_mapping);
        }       
        return (empty($result_comp->getwriteErrors()))? $reg_companyid:0;
    }
    public function total_trip_details($start = '', $end = '')
    {       
       //MongoDB
        $company_id = $this->company_id;
        $match_query = array();
        $match_query['travel_status'] = 1;
        $match_query['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($start)),'$lt'=> Commonfunction::MongoDate(strtotime($end)));
        if($company_id!="" && $company_id!=0){
            $match_query['company_id'] = (int)$company_id;
        }
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
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        return (!empty($result['result']))?$result['result']:array();
    }
    
    /**************Dashboard Trip Details Chart***************/
    public function validate_payment_settings($arr, $uid)
    {
        return Validation::factory($arr)->rule('paymodstatus', 'not_empty');
    }
    public function check_array($value = "")
    {
        if (!empty($value)) {
            return true;
        } else {
            return false;
        }
    }
    //** Function to check whether the given value is below 100 **//
    public static function check_percentage($percentage)
    {
        if ($percentage > 100) {
            return false;
        } else {
            return true;
        }
    }
}
