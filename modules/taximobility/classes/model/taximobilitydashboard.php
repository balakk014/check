<?php defined('SYSPATH') or die('No direct script access.');
/****************************************************************
* Contains Dashboard(Site Statistics - Count) details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Model_TaximobilityDashboard extends Model
{
	/**
	 * ****__construct()****
	 *
	 * setting up session variables
	 */
	public function __construct()
	{	
		$this->session = Session::instance();	
		$this->username = $this->session->get("username");
		$this->admin_session_id = $this->session->get("id");
		
		$this->user_createdby = $this->userid = $this->session->get("userid");
        $this->usertype       = $this->session->get('user_type');
        $this->company_id     = $this->session->get('company_id');
        $this->country_id     = $this->session->get('country_id');
        $this->state_id       = $this->session->get('state_id');
        $this->city_id        = $this->session->get('city_id');
		
		//MongoDB Instance
		$this->mongo_db         = MangoDB::instance('default');
	}
	
	/********* Total Trip and Revenue details *********************/
	public function total_trip_details($company_id,$start='',$end='')
	{
		$match_query['travel_status'] = 1;
		$match_query['pickup_time'] = array('$gte'=> Commonfunction::MongoDate(strtotime($start)),
											'$lte'=> Commonfunction::MongoDate(strtotime($end)));
		if($company_id != 0 && $company_id!=""){
			$match_query['company_id'] = (int)$company_id;
		}
		//echo "<pre>"; print_r($match_query); exit;
        $arguments = array(
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
                '$project' => array(
					'year' => array( '$substr' => array( '$pickup_time', 0, 4 ) ),
                    'month' => array( '$substr' => array( '$pickup_time', 5, 2 ) ),
                    'day' => array( '$substr'=> array( '$pickup_time', 8, 2 ) ),
					'fare' => '$trans.fare',
                )
            ),
            array('$group' => array('_id' => array( 'date' => '$day','month' => '$month'),
                'fare' => array('$sum' => '$fare'),
                'trips' => array( '$sum' => 1 ),
                )
            )
        );
         
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		return (!empty($result) && $result['result'])?$result['result']:array();
	}
	
	
	/*************************Dashboard Driver status ***********************************/
	public function driver_status_details($driver_status)
	{
		$usertype = $_SESSION['user_type'];
		$company_id = $_SESSION['company_id'];			
		$match = array();
		$match['user_type'] = 'D';
		if($driver_status=='A' || $driver_status=='F'){
			$match['$driver.status'] = $driver_status;
			$match['$driver.shift_status'] = 'IN';
		}elseif($driver_status=='OUT'){
			$match['$driver.status'] = 'F';
			$match['$driver.shift_status'] = $driver_status;
		}		
		if($usertype  == 'C' || $usertype  == 'M'){
			$match['company_id'] = (int)$company_id;		
		}
		$args = array(array('$lookup' => array('from' => MDB_DRIVER_INFO,
											   'localField' => '_id',
											   'foreignField' => '_id',
											   'as' => 'driver',
											   )),
					  array('$unwind' => '$driver'),
					  array('$match' => $match),
					  array('$project' => array('driver_status' => '$driver.status',
												'shift_status' => '$driver.shift_status',
												'longitude' => '$driver.longitude',
												'latitude' => '$driver.latitude',
												'name' => '$driver.name'))
				);
		$result = $this->mongo_db->aggregate(MDB_PEOPLE,$args);
		print_r($args);exit;
		return (isset($result['result'])) ? $result['result'] : array();
	}
	/*************************Dashboard Driver status ***********************************/
	/** to get company details and Driver, Taxi and passengers details for that particular company **/
	public function getCompanyUsersTaxi($company_id)
	{	
		$match_query = array();
		$match_query['user_type'] = 'D';
		$match_query['status'] = 'A';
		if($company_id!="" && $company_id!=0){
			$match_query['company_id'] = (int)$company_id;	
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
				'$group' => array(
					'_id' => array('company_id' => '$company_id','company_name' => '$company.companydetails.company_name'),
					'totaldrivers' => array('$sum' => 1)
				)
			),
			array(
				'$lookup' => array(
					'from' => MDB_TAXI,
					'localField' => '_id.company_id',
					'foreignField' => 'taxi_company',
					'as' => 'taxi'
				)
			),
			array(
				'$unwind' => '$taxi'
			),
			array(
				'$group' => array(
					'_id' => array('taxi' => '_id','company_id' => '$_id.company_id','company_name' => '$_id.company_name','totaldrivers' => '$totaldrivers'),
					'totaltaxis' => array('$sum' => 1)
				)
			),
			array(
				'$lookup' => array(
					'from' => MDB_PASSENGERS,
					'localField' => '_id.company_id',
					'foreignField' => 'passenger_cid',
					'as' => 'passenger'
				)
			),
			array(
				'$unwind' => '$passenger'
			),
			array(
				'$group' => array(
					'_id' => array('taxi' => '_id','company_name' => '$_id.company_name','totaldrivers' => '$_id.totaldrivers','totaltaxis' => '$totaltaxis'),
					'totalpassengers' => array('$sum' => 1)
				)
			),
			array(
				'$group' => array(
					'_id' => array('company_name' => '$_id.company_name','totaldrivers' => '$_id.totaldrivers','totaltaxis' => '$_id.totaltaxis','totalpassengers' => '$totalpassengers')
				)
			),
		);
		$result = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
		//echo "<pre>";print_r($result['result']);exit;
		return (!empty($result['result']) && isset($result['result'])) ? $result['result']:array();
	}
		
	public function appwise_trips($month, $year)
	{
		$arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_PASSENGERS_LOGS,
					'localField' => '_id',
					'foreignField' => 'company_id',
					'as' => 'passenger'
				)
			),
			array(
				'$unwind' => '$passenger'
			),
			array(
				'$lookup' => array(
					'from' => MDB_TRANSACTION,
					'localField' => 'passenger._id',
					'foreignField' => 'passengers_log_id',
					'as' => 'trans'
				)
			),
			array(
				'$unwind' => '$trans'
			),			
			array(
				'$project' => array(
					'month' => array( '$substr' => array( '$trans.current_date', 5, 2 ) ),
					'year' => array( '$substr' => array( '$trans.current_date', 0, 4 ) ),
					'travel_status'=>'$passenger.travel_status',
					'fare'=>'$trans.fare',
					'trips'=>'$_id',
					'admin_amount'=>'$trans.admin_amount',
					'bookby'=>'$passenger.bookby',
				)
			),
			array(
				'$match' => array('month' =>(string)$month,'year'=>(string)$year,'travel_status'=>1)
			),
			array(
				'$group' => array(
					'_id' => array('month' => '$month','year'=>'$year'),
					'total_trips' => array('$sum' => 1),
					'revenues' => array('$sum' => '$fare'),
					'admincommission' => array('$sum' => '$admin_amount'),
					'webtrips' => array('$sum' => array('$cond' => array(array('$eq' => array('$bookby',2)),1,0))),
					'mobiletrips' => array('$sum' =>array('$cond' => array(array('$eq' => array('$bookby',1)),1,0)))
				)
			),
		);
		$result = $this->mongo_db->aggregate(MDB_COMPANY, $arguments);
		return (!empty($result['result']) && isset($result['result'])) ? $result['result']:array();
	}	
	
	public function getDashboardData($company_id = 0,$start_date,$end_date)
	{	
		# Total Amount	
		$total_amount = $driver_commission = $commision_amount = $cash_payment = $company_cash_amt = $card_payment = $company_card_amt = $passenger_count = $trips_count = $cancel_trips_count = $active_passenger_count = 0;
		$date_match = array('$gte' => Commonfunction::MongoDate(strtotime($start_date)), '$lte' => Commonfunction::MongoDate(strtotime($end_date)));
		
		$total_match = array('current_date' => $date_match);
		if($company_id > 0) {
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
		//print_r($total_amount_sql); exit;
		if(!empty($total_amount_sql['result'])){			
			$total_amount = $total_amount_sql['result'][0]['total_amount'];
		}
		$result["total_amount"] = $total_amount;
		
		# Commision Amount
		$commission_match = array('current_date' => $date_match);
		if($company_id > 0) {	
			$commission_match['pass_log.company_id'] = (int)$company_id;
			$commission_args = array(
								array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,'localField' => 'passengers_log_id',
													'foreignField' => '_id','as' => 'pass_log')),
								array('$match' => $commission_match),
								array('$group' => array('_id' => null,
								'commision_amount' => array('$sum' => '$company_amount'),
								'driver_amount' => array('$sum' => '$driver_amount')
								))
							);
		} else {			
			$commission_args = array(
						array('$match' => $commission_match),
						array('$group' => array('_id' => null,'commision_amount' => array('$sum' => '$admin_amount')))
					);				
		}
		$commision_sql = $this->mongo_db->Aggregate(MDB_TRANSACTION,$commission_args);
		
		if(!empty($commision_sql['result'])){
			
			$driver_commission = ($company_id > 0) ? $commision_sql['result'][0]['driver_amount'] : 0;
			$commision_amount = round($commision_sql['result'][0]['commision_amount'],2);
		}
		$result['commision_amount'] = $commision_amount;
		$result['driver_commision'] = $driver_commission;
		
 		# Company Amount
		$result["company_amount"] = $result["total_amount"]-$result["commision_amount"];
		
		# Cash Payment
		$cash_match = array('current_date' => $date_match, 'payment_type' => 1);	
		if($company_id > 0) {
			$cash_match['pass_logs.company_id'] = (int)$company_id;
		}	
		$cash_args = array(
						array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,'localField' => 'passengers_log_id',
												'foreignField' => '_id','as' => 'pass_logs')),
						array('$unwind' =>  array('path' =>  '$pass_logs', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$match' => $cash_match),
						array('$project' =>  array(
												'company_amount' => '$company_amount',
												'cash_payment' =>  array('$add' =>  array( '$fare', '$pass_logs.used_wallet_amount')))),
						array('$group' =>  array('_id' => null,
											'company_cash_amt' => array('$sum' => '$company_amount'),
											'cash_payment' =>  array('$sum' => '$cash_payment')))
					);
		$cash_payment_sql = $this->mongo_db->Aggregate(MDB_TRANSACTION,$cash_args);
		if(!empty($cash_payment_sql['result'])){
			
			$cash_payment = round($cash_payment_sql['result'][0]['cash_payment'],2);
			$company_cash_amt = round($cash_payment_sql['result'][0]['company_cash_amt'],2);
		}
		$result["cash_payment"] = $cash_payment;
		$result["company_cash_payment"] = $company_cash_amt;
			
		# Card Payment
		$card_match = array('$and' =>  array(
								array('current_date' => $date_match),
								array( '$or' =>  array(array('payment_type' =>  2),
								array('payment_type' =>  3)))));
		if($company_id > 0) {
			$card_match['$and'][] = array('pass_logs.company_id' => (int)$company_id);
		}
		//echo $company_id; exit;
		$card_args = array(		
						array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,'localField' => 'passengers_log_id',
												'foreignField' => '_id','as' => 'pass_logs')),
						array('$unwind' =>  array('path' =>  '$pass_logs', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$match' => $card_match),
						array('$project' => array(
										'company_card_amt' => '$company_amount',
										'card_payment' =>  array('$add' =>  array('$fare', '$pass_logs.used_wallet_amount')))),
						array('$group' =>  array('_id' => null,
										'company_card_amt' => array('$sum' => '$company_card_amt'),
										'card_payment' =>  array('$sum' => '$card_payment' )))
					);
		$card_payment_sql = $this->mongo_db->Aggregate(MDB_TRANSACTION,$card_args);
		//print_r($card_payment_sql); exit;
		if(!empty($card_payment_sql['result'])){
			
			$card_payment = round($card_payment_sql['result'][0]['card_payment'],2);
			$company_card_amt = round($card_payment_sql['result'][0]['company_card_amt'],2);
		}
		$result["card_payment"] = $card_payment;
		$result["company_card_payment"] = $company_card_amt;
		
		# New Users Count
		/*$new_match = array('created_date' => $date_match);
		$pass_args = array(
						array('$match' => $new_match),
						array('$group' => array('_id' => array('_id' => null),'passenger_count' => array('$sum' => 1)))
					);
		$passenger_result = $this->mongo_db->Aggregate(MDB_TRANSACTION,$pass_args);	
		if(!empty($passenger_result['result'])){			
			$passenger_count = $passenger_result['result'][0]['passenger_count'];
		}
		$result["passenger_count"] = $passenger_count; */
		
		# New Users Count
		if($company_id > 0) {
			$new_match = array('passengers.created_date' => $date_match);
			$new_match['company_id'] = (int)$company_id;
			$pass_args = array(
				array(
					'$lookup' => array(
						'from' => MDB_PASSENGERS,
						'localField' => "passengers_id",
						'foreignField' => "_id",
						'as' => "passengers"
					)
				),
				array('$unwind' =>  '$passengers'),
				array('$match' => $new_match),
				array('$group' => array('_id' => array('passengers_id' => '$passengers_id'))),
				array('$group' => array('_id' => array('_id' => null),'passenger_count' => array('$sum' => 1))),
			);
			$passenger_result = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS, $pass_args);
			if(!empty($passenger_result['result'])){
				$passenger_count = $passenger_result['result'][0]['passenger_count'];
			}
			$result["passenger_count"] = $passenger_count;
		} else {
			$new_match = array('created_date' => $date_match);
			if($company_id > 0) {
				$new_match['passenger_cid'] = (int)$company_id;
			}	
			$pass_args = array(
							array('$match' => $new_match),
							array('$group' => array('_id' => array('_id' => null),'passenger_count' => array('$sum' => 1)))
						);
			$passenger_result = $this->mongo_db->Aggregate(MDB_PASSENGERS,$pass_args);
			if(!empty($passenger_result['result'])) {
				$passenger_count = $passenger_result['result'][0]['passenger_count'];
			}
			$result["passenger_count"] = $passenger_count;
		}

		# Total Trips Count
		$trips_match = array('trans.current_date' => $date_match);
		$trips_match['travel_status'] = 1;
		$trips_match['driver_reply'] = 'A';
		if($company_id > 0) {
			$trips_match['company_id'] = (int)$company_id;
		}	
		$trips_args = array(
							array('$lookup' => array('from' => MDB_TRANSACTION,
							'localField' => '_id','foreignField' => 'passengers_log_id','as' => 'trans')),
							array('$unwind' =>  array('path' =>  '$trans', 'preserveNullAndEmptyArrays' =>  true ) ),
							array('$match' => $trips_match),
							array('$group' => array('_id' => array('_id' => null),
													'trips_count' => array('$sum' => 1)))
						);
		$trips_sql = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$trips_args);
		if(!empty($trips_sql['result'])){
			$trips_count = $trips_sql['result'][0]['trips_count'];
		}
		$result["trips_count"] = $trips_count;
		
		# Active Passenger Count 
		$and1 = array('$and' => array(array( 'travel_status' => 1),array('driver_reply' => 'A')));
		$and2 = array('$and' => array(array( 'travel_status' => 9),array('driver_reply' => 'C')));
		$or2 = array('$or' => array($and2,array('travel_status' => array('$in' => array(4,8)))));
		$or1 = array('$or' => array($and1,$or2));
		
		$passenger_match = array('$and' =>  array(array('actual_pickup_time' => $date_match),$or1));
		if($company_id > 0) {
			$passenger_match['$and'][] = array('pass.company_id' => (int)$company_id);
		}					
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
		$result["active_passenger_count"] = $active_passenger_count;
		
		/** Cancel Trips Count **/
		//array('$match' => array('travel_status' => array('$in' => array(4,9,8)), 'driver_reply' => 'C','actual_pickup_time' => $date_match)),
		$cancel_match = array('$and' => array(array('actual_pickup_time' => $date_match), $or2));
		if($company_id > 0) {
			$cancel_match['$and'][] = array('company_id' => (int)$company_id);
		}	
		$cancel_args = array(
							array('$match' => $cancel_match),
							array('$group' => array('_id' => array('_id' => null),
								'cancel_trips_count' => array('$sum' => 1)))
						);
		$cancel_trips_sql = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$cancel_args);	
		if(!empty($cancel_trips_sql['result'])){
			$cancel_trips_count = $cancel_trips_sql['result'][0]['cancel_trips_count'];
		}
		$result["cancel_trips_count"] = $cancel_trips_count;
		return $result;
	}
	
	public function getDashboardassignUnassignData($company_id = 0,$start_date,$end_date)
	{
		$condition = $mapping_condition = $people_condition = $taxi_result = $total_driver_result = $result = array();
		if($company_id > 0) {
			# Assigned taxi count
			$taxi_match = array('$or' =>  array(
								array('mapping_startdate' => array('$gte' => Commonfunction::MongoDate(strtotime($start_date)),
																	'$lte' => Commonfunction::MongoDate(strtotime($end_date)))),   
								array('mapping_enddate' => array('$gte' => Commonfunction::MongoDate(strtotime($start_date)),
																	'$lte' => Commonfunction::MongoDate(strtotime($start_date))))
								));
			if($company_id > 0) {
				//$taxi_match = array('$and' => array($taxi_match,$mapping_condition));
			}
			$taxi_args = array(	array('$match' => $taxi_match),
								array('$group' => array('_id' => array('company_id' => '$mapping_companyid'),
								'assigned_taxi_count' =>  array( '$sum' => 1),
								'assigned_driver_count'  => array('$sum' => 1)
								))
							);
			$taxi_sql = $this->mongo_db->Aggregate(MDB_TAXI_DRIVER_MAPPING,$taxi_args);	
			//print_r($taxi_sql); exit;		
			if(!empty($taxi_sql['result'])){
				foreach($taxi_sql['result'] as $t){
					$taxi_result[] = array('company_id' => $t['_id']['company_id'],
										'assigned_taxi_count'=>$t['assigned_taxi_count'], 
										'assigned_driver_count'=>$t['assigned_driver_count']);
				}			
			}
			
			# Total driver count
			$driver_match = array('user_type' => 'D');
			if($company_id > 0) {
				$driver_match['company_id'] = (int)$company_id;
			}
			$driver_args = array(
								array('$match' => $driver_match),
								array('$lookup' => array('from' => MDB_COMPANY,'localField' => 'company_id',
													'foreignField' => '_id','as' => 'company')),
								array('$group' => array('_id' => array('company_id' => '$company_id'),
								'total_driver_count' => array('$sum' => 1)))
							);
			$total_driver_sql = $this->mongo_db->Aggregate(MDB_TAXI_DRIVER_MAPPING,$driver_args);	
			if(!empty($total_driver_sql['result'])){
				foreach($total_driver_sql['result'] as $t){
					$total_driver_result[] = array('company_id' => $t['_id']['company_id'],
										'total_driver_count'=>$t['total_driver_count']);
				}
			}
			
			# Total driver count
			$condition = array('taxi_company' => (int)$company_id);
			$sql_args[]['$lookup'] = array('from' => MDB_TAXI,'localField' => '_id',
											'foreignField' => 'taxi_company','as' => 'taxi');
			if($company_id > 0) {
				$sql_args[]['$match'] = array('taxi.taxi_company' => (int)$company_id);
			}
			else
			{
				$sql_args[]['$match'] = array('companydetails.company_status' => 'A');
			}
			$sql_args[]['$project'] = array('_id' => 0,
											'cid' => '$_id',
											'company_name' => '$companydetails.company_name',
											'company_status' => '$companydetails.company_status',
											'taxi_count' =>  array('$size' =>  '$taxi'));
				//echo "<pre>"; print_r($sql_args); exit;
			$sql = $this->mongo_db->Aggregate(MDB_COMPANY,$sql_args);	
			if(!empty($sql['result'])){
				$result = $sql['result'];
			}	
			
			if(count($result) > 0) {
				foreach($result as $k => $v) {
					$result[$k]["assigned_driver_count"] = 0;
					$result[$k]["assigned_taxi_count"] = 0;
					$result[$k]["total_driver_count"] = 0;
					if(count($taxi_result) > 0) {
						foreach($taxi_result as $t) {
							if($v["cid"] == $t["company_id"]) {
								$result[$k]["assigned_taxi_count"] = $t["assigned_taxi_count"];
								$result[$k]["assigned_driver_count"] = $t["assigned_driver_count"];
							}
						}
					}
					/*if(count($driver_result) > 0) {
						foreach($driver_result as $d) {
							if($v["cid"] == $d["company_id"]) {
								$result[$k]["assigned_driver_count"] = $d["assigned_driver_count"];
							}
						}
					}
					if(count($taxi_result) > 0) {
						foreach($taxi_result as $t) {
							if($v["cid"] == $t["company_id"]) {
								$result[$k]["assigned_taxi_count"] = $t["assigned_taxi_count"];
							}
						}
					}*/
					if(count($total_driver_result) > 0) {
						foreach($total_driver_result as $d) {
							if($v["cid"] == $d["company_id"]) {
								$result[$k]["total_driver_count"] = $d["total_driver_count"];
							}
						}
					}
				}
			}
		} else {
			$result = array();
		}
		//	print_r($result); exit;
		return $result;
	}
	
	public function getPaymentByCompany($company_id = 0,$start_date,$end_date)
	{
		$promotional_amount = $promotional_amount_count = 0;
		$all_result = $result = array();
		$all_result[] =  array('commision_amount' => 0,
							'driver_commision' => 0,
							'total_amount' => 0,
							'total_amount_count' => 0,
							'cash_payment' => 0,
							'company_cash_amt' => 0,
							'cash_payment_count' => 0,
							'card_payment' => 0,
							'company_card_payment' => 0,
							'card_payment_count' => 0,
							'referral_payment' => 0,
							'referral_payment_count' => 0,
							'promotional_amount' => 0,
							'promotional_amount_count' => 0);
		$date = array('$gte' => Commonfunction::MongoDate(strtotime($start_date)),'$lte' => Commonfunction::MongoDate(strtotime($end_date)));
		$sql_match = array('current_date' => $date);
		if($company_id > 0) {
			$sql_match['pass_logs.company_id'] = (int)$company_id;
		}	
		$sql_args = array(
			array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,'localField' => 'passengers_log_id','foreignField' => '_id','as' => 'pass_logs')),
			array('$unwind' => array('path' => '$pass_logs', 'preserveNullAndEmptyArrays' =>  true)),
			array('$match' => $sql_match),
			array('$project' =>  array(
				'company_amount' => '$company_amount',
				'driver_amount' => '$driver_amount',
				'total_amount' =>  array( '$add' =>  array( '$fare', '$pass_logs.used_wallet_amount')),
				//array('$eq' =>  array('$pass_logs.promocode',''))
				'cash_payment' => array('$cond' =>  array( 'if' =>  array('$and'  =>  array( array( '$eq' =>  array( '$payment_type', 1) ),
										)), 
										'then' =>  array( '$add' =>  array( '$fare', '$pass_logs.used_wallet_amount')),
										'else' =>  0 )),
				'company_cash_amt' => array('$cond' => array('if' => array('$eq' => array('$payment_type', 1)),
										'then' => '$company_amount',
										'else' =>  0 )),                                 
				'cash_payment_count' => array('$cond' =>  array( 'if' =>  array( '$eq' =>  array( '$payment_type', 1 ) ),
										'then' =>  1, 'else' =>  0 )),
				//array( '$eq' =>  array('$pass_logs.promocode','') ),
				'card_payment' => array('$cond' =>  array( 'if' =>  array('$and'  =>  array( 
										array('$or' => array(array( '$eq' =>  array( '$payment_type',2)),array( '$eq' =>  array( '$payment_type',3)))))), 
										'then' =>  array( '$add' =>  array( '$fare', '$pass_logs.used_wallet_amount')),
										'else' =>  0 )),
				'company_card_payment' => array('$cond' => array('if' => array('$and' => array(array('$eq' =>  array('$pass_logs.promocode','')),
										array('$or' => array(array('$eq' => array( '$payment_type',2)),array('$eq' => array('$payment_type',3)))))), 
										'then' => '$company_amount',
										'else' => 0)),
				//array( '$eq' =>  array('$pass_logs.promocode','') ),
				'card_payment_count' => array('$cond' =>  array('if' => array('$and'  =>  array( 
										array('$or' => array(array('$eq' => array('$payment_type',2)),array('$eq' => array('$payment_type',3)))))), 
										'then' =>  1,
										'else' =>  0 )),
				'referral_payment' => array('$cond' =>  array( 'if' =>  array('$and'  =>  array( array( '$eq' =>  array( '$payment_type', 5) ),
										array( '$eq' =>  array( '$pass_logs.promocode','')))), 
										'then' =>  '$pass_logs.used_wallet_amount',
										'else' =>  0 )), 
				'referral_payment_count' => array('$cond' =>  array( 'if' =>  array('$and'  =>  array( array( '$eq' =>  array( '$payment_type', 5) ),
										array( '$eq' =>  array( '$pass_logs.promocode','')))), 
										'then' =>  1,
										'else' =>  0 ))                                 
										)),   
			array('$group' => array('_id' => null,
				'total_amount_count' => array('$sum' => 1),
				'commision_amount' => array('$sum' => '$company_amount'),
				'driver_commision' => array('$sum' => '$driver_amount'),
				'total_amount' => array('$sum' => '$total_amount'),
				'cash_payment' => array('$sum' => '$cash_payment'),
				'company_cash_amt' => array('$sum' => '$company_cash_amt'),
				'cash_payment_count' => array('$sum' => '$cash_payment_count'),
				'card_payment_count' => array('$sum' => '$card_payment_count'),
				'card_payment' => array('$sum' => '$card_payment'),
				'company_card_payment' => array('$sum' => '$company_card_payment'),
				'referral_payment' => array('$sum' => '$referral_payment'),
				'referral_payment_count' => array('$sum' => '$referral_payment_count')))
		
		);
		$sql_result = $this->mongo_db->Aggregate(MDB_TRANSACTION,$sql_args);	
		if(!empty($sql_result['result'])){
			$all_result = $sql_result['result'];
		}
		
		$query_args = array(		
				array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,'localField' => 'passengers_log_id',
										'foreignField' => '_id','as' => 'pass_logs')),
				array('$unwind' =>  array( 'path' =>  '$pass_logs', 'preserveNullAndEmptyArrays' =>  true ) ),
				array('$match' => array('current_date' => $date, 'pass_logs.promocode' =>  array('$ne' =>  ''))),
				array('$project' => array('_id' => 0,'fare' => '$fare','promocode' => '$pass_logs.promocode'))
			);
		$query_result = $this->mongo_db->Aggregate(MDB_TRANSACTION,$query_args);	
		if(!empty($query_result['result'])){
			$result = $query_result['result'];
		}
		
		if(count($result) > 0) {
			foreach($result as $p) {
				if(isset($p["promocode"]) && ($p["promocode"] != "")) {
					$promo_code = $p["promocode"];
					$fare = $p["fare"];
					$promo_result = $this->mongo_db->findOne(MDB_PASSENGERS_PROMO,array('promocode' => $promo_code),array('promo_discount'));
					if(count($promo_result) > 0) {
						$promo_discount = isset($promo_result['promo_discount']) ? $promo_result['promo_discount']: 0;
						$promotional_amount += ($promo_discount/100)*$fare;
						$promotional_amount_count = $promotional_amount_count+1;
					}
				}
			}
		}
		$all_result[0]["promotional_amount"] = $promotional_amount;
		$all_result[0]["company_cash_amt"] = isset($all_result[0]["company_cash_amt"]) ? $all_result[0]["company_cash_amt"]-$promotional_amount : 0;
		$all_result[0]["promotional_amount_count"] = $promotional_amount_count;	
		//echo '<pre>';print_r($all_result);exit;	
		return $all_result;
	}
	
	public function getCityByCountChart($company_id = 0,$start_date,$end_date)
	{
		$result = array();		
		$match = array('pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($start_date)),
												'$lte' => Commonfunction::MongoDate(strtotime($end_date))),
						'city_name' =>  array('$ne' =>"")
						);
		if($company_id > 0) {
			$match['company_id'] = (int)$company_id;
		}
		$args = array(
					array('$match' => $match),
					array('$group' => array('_id' => array('city_name' => '$city_name'),
					'trip_top_cities_count' => array('$sum' => 1)))
				);
		$res = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($res['result'])){
			$i=0;
			foreach($res['result'] as $r){
				if(isset($r['_id']['city_name']) && $r['_id']['city_name']!=""){						
					$result[$i]['city_name'] = $r['_id']['city_name'];
					$result[$i]['trip_top_cities_count'] = $r['trip_top_cities_count'];
					$i++;
				}
			}
		}
		return $result;
	}
	
	public function getCompanyRevenueChart($company_id = 0,$start_date,$end_date)
	{
		$result = $user_result = array();
		$date = array('$gte' => Commonfunction::MongoDate(strtotime($start_date)),
									'$lte' => Commonfunction::MongoDate(strtotime($end_date)));
		$match['createdate'] = $date;
		if($company_id > 0) {
			$match['company_id'] = (int)$company_id;
		}
		$query_args = array(
						array('$lookup' => array('from' => MDB_TRANSACTION,'localField' => '_id',
												'foreignField' => 'passengers_log_id','as' => 'trans')),
						//array('$unwind' => '$trans'),
						array('$unwind' =>  array('path' =>  '$trans', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$lookup' => array('from' => MDB_COMPANY,'localField' => 'company_id','foreignField' => '_id','as' => 'company')),
						//array('$unwind' => '$company'),
						array('$unwind' =>  array('path' =>  '$company', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$lookup' => array('from' => MDB_PEOPLE,'localField' => 'driver_id','foreignField' => '_id','as' => 'people')),
						//array('$unwind' => '$people'),
						array('$unwind' =>  array('path' =>  '$people', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$lookup' => array('from' => MDB_PASSENGERS,'localField' => 'passengers_id','foreignField' => '_id','as' => 'pass')),
						//array('$unwind' => '$pass'),
						array('$unwind' =>  array('path' =>  '$pass', 'preserveNullAndEmptyArrays' =>  true ) ),
						array('$match' => $match),
						array('$project' =>  array(
									'createdate' => '$createdate',
									'amount' => '$trans.fare',
									'trips' => '$trans.fare')),
						array('$group' =>  array(
									'_id'  =>  array(
													'month' => array('$month' =>  '$createdate' ),
													'day' => array('$dayOfMonth' => '$createdate'), 
													'year' =>  array('$year' =>  '$createdate')),
									'amount' =>  array( '$sum' =>  '$amount' ),
									'trips' =>  array( '$sum' => 1 )))
					);
		$query = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$query_args);
		//echo '<pre>';print_r($query);exit;
		if(!empty($query['result'])){
			$i=0;
			foreach($query['result'] as $r){
				$created_date = $r['_id']['year'].'-'.$r['_id']['month'].'-'.$r['_id']['day'];
				$result[$i]['createdate'] = $created_date;
				$result[$i]['amount'] = $r['amount'];
				$result[$i]['trips'] = $r['trips'];
				$i++;
			}
		}
		
		$user_match['pass.created_date'] = $date;
		if($company_id > 0) {
			$user_match['pass.company_id'] = (int)$company_id;
		}
		$user_args = array(
						array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,'localField' => '_id','foreignField' => 'passengers_id','as' => 'pass')),
						array('$unwind' => '$pass'),
						array('$match' => $user_match),
						array('$group' =>  array(
						'_id'  =>  array('month' => array('$month' => '$created_date'),'day' => array('$dayOfMonth' => '$created_date'), 
						'year' =>  array('$year' =>  '$created_date')),
						'user_count' =>  array('$sum' => 1)))
					);
		$user_query = $this->mongo_db->Aggregate(MDB_PASSENGERS,$user_args);
		if(!empty($user_query['result'])){
			$i=0;
			foreach($user_query['result'] as $q){
				$created_date = $r['_id']['year'].'-'.$r['_id']['month'].'-'.$r['_id']['date'];
				$user_result[$i]['created_date'] = $created_date;
				$user_result[$i]['user_count'] = $r['trips'];
				$i++;
			}
		}

		$user_date_array = $trans_array = $final_array = $stored_arr = $data1 = $data2 = $full_arr = array();
		if(count($result) > 0) {
			foreach($result as $res) {
				$full_arr[] = $date = date("Y-m-d",strtotime($res["createdate"]));
				$trans_array[$date] = $res;
			}
		}
		
		if(count($user_result) > 0) {
			foreach($user_result as $d) {
				$full_arr[] = $date = date("Y-m-d",strtotime($d["created_date"]));
				$user_date_array[$date] = $d;
			}
		}
		function cmp($a, $b)
		{
			if ($a == $b) {
				return 0;
			}
			return ($a < $b) ? -1 : 1;
		}
		$full_arr = array_values(array_unique($full_arr));
		usort($full_arr, "cmp");
		foreach($full_arr as $val){
			$data1['createdate'] = $val;
			$data1['amount'] = 0;
			$data1['trips'] = 0;
			$data2['user_count'] = 0;
			$data2['created_date'] = "";
			if(isset($trans_array[$val])) { $data1 = $trans_array[$val]; }
			if(isset($user_date_array[$val])) { $data2 = $user_date_array[$val]; }
			$final_array[] = array_merge($data1,$data2);
		}
		//echo '<pre>';print_r($final_array);exit;
		return $final_array;
	}
	
	public function getDriverRevenueChart($driver_id = 0,$start_date,$end_date)
	{
		$result = array();
		$match = array('travel_status' => 1,
					'driver_reply' => 'A',
					'trans.current_date' => array('$gte' => Commonfunction::MongoDate(strtotime($start_date)),'$lte' => Commonfunction::MongoDate(strtotime($end_date))));
		if($driver_id > 0) {
			$match['driver_id'] = (int)$driver_id;
		}
		$args = array(		
					array('$lookup' => array('from' => MDB_TRANSACTION,'localField' => '_id','foreignField' => 'passengers_log_id','as' => 'trans')),
					array('$unwind' => '$trans'),
					array('$match' => $match),
					array('$project' =>  array(
								'current_date' => '$trans.current_date',
								'amount' => '$trans.driver_amount',
								'trips' => '$_id')),
					array('$group' =>  array(
								'_id'  =>  array('month' =>  array('$month' =>  '$current_date'),
								'day' => array('$dayOfMonth' =>  '$current_date' ), 
								'year' =>  array('$year' =>  '$current_date' ) ),
								'amount' =>  array( '$sum' =>  '$amount' ),
								'trip_count' =>  array( '$sum' => '$_id' )))
				);
		$query = $this->mongo_db->Aggregate(MDB_PASSENGERS_LOGS,$args);
		if(!empty($query['result'])){
			$i=0;
			foreach($query['result'] as $r){				
				$created_date = $r['_id']['year'].'-'.$r['_id']['month'].'-'.$r['_id']['day'];
				$result[$i]['current_date'] = $created_date;
				$result[$i]['amount'] = $r['amount'];
				$result[$i]['trip_count'] = $r['trip_count'];
				$i++;
			}
		}
		return $result;
	}
	
	public function getCompanyWiseTrip($company_id = 0,$start_date,$end_date)
	{
		$result = array();
		$match['pass_log.pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($start_date)), '$lte' => Commonfunction::MongoDate(strtotime($end_date)));
		if($company_id > 0) {
			$match['_id'] = (int)$company_id;
		}
		$args = array(				
					array('$lookup' => array('from' => MDB_PASSENGERS_LOGS,'localField' => '_id','foreignField' => 'company_id','as' => 'pass_log')),
					array('$unwind' =>  array( 'path' =>  '$pass_log', 'preserveNullAndEmptyArrays' =>  true ) ),
					array('$match' => $match),
					array('$project' => array(
								'company_name' => '$companydetails.company_name',
								'trip_completed' => array('$cond' =>  array( 'if' =>  array('$and'  =>  array( array( '$eq' =>  array( '$pass_log.travel_status', 9) ),
					array( '$eq' =>  array( '$pass_log.driver_reply','C') ))), 'then' =>  '$pass_log._id', 'else' =>  null )),
								'trip_inprogress' => array('$cond' =>  array( 'if' =>  array('$and'  =>  array( array( '$eq' =>  array( '$pass_log.travel_status', 2 ) ),
					array( '$eq' =>  array( '$pass_log.driver_reply','A') ))),'then' =>  '$pass_log._id','else' =>  null )),                
								'trip_cancelled' => array('$cond' =>  array( 'if' =>  array('$and'  =>  array( array( '$eq' =>  array( '$pass_log.travel_status', 4 ) ),
					array( '$eq' =>  array( '$pass_log.driver_reply','A') ),
								array('$or' => array(
											array( '$eq' =>  array( '$pass_log.travel_status',8) ),
											array( '$eq' =>  array( '$pass_log.travel_status',9) ),
											array( '$eq' =>  array( '$pass_log.driver_reply','C') )
								)))), 'then' =>  '$pass_log._id','else' =>  null ))                
					)),
					array('$group' => array('_id' => null,
								'trip_completed' =>  array(
								'$sum' =>  array( '$cond' =>  array(array( '$ne' =>  array( '$trip_completed',null) ),1,0))),
								'trip_inprogress' =>  array(
								'$sum' =>  array( '$cond' =>  array(array( '$ne' =>  array( '$trip_inprogress',null) ),1,0))),
								'trip_cancelled' =>  array(
								'$sum' =>  array( '$cond' =>  array(array( '$ne' =>  array( '$trip_cancelled',null) ),1,0)))  
								,'company_name' => array('$addToSet' => '$company_name')      
					))
				);
		$query = $this->mongo_db->Aggregate(MDB_COMPANY,$args);
		if(!empty($query['result'])){
			$result = $query['result'];
			$result[0]['company_name'] = !empty($result[0]['company_name']) ? $result[0]['company_name'][0]: '';
		}
		return $result;
		//echo '<pre>';print_r($result);exit;
	}
	
}
?>
