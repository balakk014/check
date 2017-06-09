<?php defined('SYSPATH') or die('No direct script access.');
/******************************************
* Contains Admin(Login,Logout,Forgot Password,etc,...)details
* @Package: Taximobility
* @Author: Taxi Team
* @URL : taximobility.com
********************************************/
class Model_TaximobilityAdmin extends Model
{
    /**
     * ****__construct()****
     * setting up session variables
     */
    public function __construct()
    {
        $this->session          = Session::instance();
        $this->username         = $this->session->get("username");
        $this->admin_session_id = $this->session->get("id");
        $this->user_createdby = $this->userid = $this->session->get("userid");
        $this->usertype       = $this->session->get('user_type');
        $this->company_id     = $this->session->get('company_id');
        $this->country_id     = $this->session->get('country_id');
        $this->state_id       = $this->session->get('state_id');
        $this->city_id        = $this->session->get('city_id');
        $this->mdate            = commonfunction::getCurrentTimeStamp();
        # created date
		$this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
        //$this->DisplayDateTimeFormat = commonfunction::DisplayDateTimeFormat();
        //MongoDB Instance
        $this->mongo_db         = MangoDB::instance('default');
    }
    public function all_user_list($offset = '', $val = '',$find_count = false)
    {
		// MongoDB
		if($find_count){
			$result = $this->mongo_db->count(MDB_PEOPLE,array('user_type'=>array('$ne'=>'A'),'status'=>array('$ne'=>'T')));
			return $result;
		} else {
                        // ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[
                            'projection'=>[
                                '_id'=>1,
                                'name'=>1,
                                'lastname'=>1,
                                'email'=>1,
                                'phone'=>1,
                                'address'=>1,
                                'created_date'=>1,
                                'user_type'=>1,
                                'status'=>1
                                ],
                            'sort'=>[
                                'created_date'=>-1
                                 ],
                            'skip'=>(int)$offset,
                            'limit'=>(int)$val
                            ];
                        $result = $this->mongo_db->find(MDB_PEOPLE,array('user_type'=>array('$ne'=>'A'),'status'=>array('$ne'=>'T')),$options);
            
			return (!empty($result))?$result:array();
		}
    }
    
    public function count_usersearch_list($keyword = "", $user_type = "", $status = "", $offset = "", $val = "",$find_count = true){
		
		$count = $this->get_all_search_list($keyword, $user_type, $status, $offset, $val,$find_count);
		return $count;
	}
    
    public function get_all_search_list($keyword = "", $user_type = "", $status = "", $offset = "", $val = "",$find_count = false)
    {
        $keyword        = str_replace("%", "!%", $keyword);
        $keyword        = str_replace("_", "!_", $keyword);
        $srch_query = array('user_type' => array('$ne'=>'A'));
        if((!empty($keyword)) && (!empty($status)) && (!empty($user_type))) {
            $srch_query = array( "\$and" => array(array('user_type' => array('$ne'=>'A')),array('user_type' => $user_type ),array('status' => $status ),array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'email' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
        } else if (!empty($keyword) && (!empty($user_type))) {
            $srch_query = array( "\$and" => array(array('user_type' => array('$ne'=>'A')),array('user_type' => $user_type ),array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'email' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
        } else if (!empty($keyword) && (!empty($status))) {
            $srch_query = array( "\$and" => array(array('user_type' => array('$ne'=>'A')),array('status' => $status ),array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'email' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
        } else if(!empty($status) && (!empty($user_type))){
            $srch_query = array( "\$and" => array(array('user_type' => array('$ne'=>'A')),array('user_type' => $user_type ),array('status' => $status ) ) );
        } else if(!empty($keyword)){
            $srch_query = array( "\$and" => array(array('user_type' => array('$ne'=>'A')),array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) , array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i") ),array( 'email' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
        } else if(!empty($user_type)){
            $srch_query = array( "\$and" => array(array('user_type' => array('$ne'=>'A')),array('user_type' => $user_type )));
        } else if (!empty($status)) {
            $srch_query = array( "\$and" => array(array('user_type' => array('$ne'=>'A')),array('status' => $status )));
        }
        //echo '<pre>';print_r($srch_query);//exit;
        if($user_type == 'C'){
            $common_arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_COMPANY,
                        'localField' => '_id',
                        'foreignField' => 'companydetails.userid',
                        'as' => 'company'
                    )
                ),
                array(
                    '$unwind' => '$company'
                ),
                array(
                    '$match' => $srch_query
                ),
            );
            if($find_count){
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
                $merge_arguments = array_merge($common_arguments, $count_arguments);
                $result          = $this->mongo_db->aggregate(MDB_PEOPLE, $merge_arguments);
                //echo "<pre>if";print_r($result['result']);exit;
                return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
            } else {
                $field_arguments = array(
                   
                    array(
                        '$project' => array('_id'=>0,
                            '_id' => '$_id',
                            'created_date' => '$created_date',
                            'name' => '$name',
                            'lastname' => '$lastname',
                            'email' => '$email',
                            'address' => '$address',
                            'status' => '$status',
                            'phone' => '$phone',
                            'user_type' => '$user_type',
                        )
                    ),
                     array(
                        '$sort' => array( 
                            '_id' => -1
                        ),
                    ),
                    array('$skip'	=> (int)$offset ),
                    array('$limit'	=> (int)$val )
                );
                $merge_arguments = array_merge($common_arguments, $field_arguments);
                $result    = $this->mongo_db->aggregate(MDB_PEOPLE, $merge_arguments);
                $data = array();
                if(isset($result['result']) && !empty($result['result'])){
					foreach($result['result'] as $val){
						//$arr = $val;
						$arr['id'] = $val['_id'];
                                                $arr['name']=$val['name'];
                                                $arr['lastname']=$val['lastname'];
                                                $arr['email']=$val['email'];
                                                $arr['address']=$val['address'];
                                                $arr['status']=$val['status'];
                                                $arr['phone']=$val['phone'];
                                                $arr['user_type']=$val['user_type'];
                                                $arr['created_date']= Commonfunction::convertphpdate('Y-m-d H:i:s',$val['created_date']);
						$data[] = $arr;
					}
				}
                //echo "<pre>else";print_r($data); exit;
                return $data;
            }
        } else {
            if($find_count){
                $result = $this->mongo_db->count(MDB_PEOPLE,$srch_query);
                return $result;
            } else {
                    $result = array();
                     // ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[
                            'projection'=>[
                                'name'=>1,
                                'lastname'=>1,
                                'email'=>1,
                                'phone'=>1,
                                'address'=>1,
                                'created_date'=>1,
                                'user_type'=>1,
                                'status'=>1
                                ],
                            'sort'=>[
                                '_id'=>-1
                                 ],
                            'skip'=>(int)$offset,
                            'limit'=>(int)$val
                            ];
                        $res = $this->mongo_db->find(MDB_PEOPLE,$srch_query,$options);
                if(!empty($res)){
					$i=0;
					foreach($res as $r){
						$result[$i] = $r;
						$result[$i]['id'] = $r['_id'];
						$result[$i]['created_date'] = commonfunction::convertphpdate('Y-m-d H:i:s', $r['created_date']);
						$i++;
					}
				}
				//echo "<pre>"; print_r($result); exit;
				return $result;
            }
        }
    }
    /**
     * ****add_user_form()****
     *@param $arr validation array
     *@validation check
     */
    public function validate_user_form($arr)
    {
        $arr['firstname'] = trim($arr['firstname']);
        $arr['email']     = trim($arr['email']);
        //updated for trim of username while posting and not proper validation
        $arr['username']  = trim($arr['username']);
        return Validation::factory($arr)->rule('firstname', 'not_empty')
            ->rule('lastname', 'min_length', array(
            ':value',
            '1'
        ))->rule('firstname', 'min_length', array(
            ':value',
            '4'
        ))->rule('firstname', 'max_length', array(
            ':value',
            '32'
        ))->rule('file', 'Upload::type', array(
            $files_value_array['photo'],
            array(
                'jpg',
                'jpeg',
                'png',
                'gif'
            )
        ))->rule('file', 'Upload::size', array(
            $files_value_array['photo'],
            '2M'
        ))->rule('email', 'not_empty')->rule('email', 'email')->rule('country_id', 'not_empty')->rule('paypal_account', 'email')->rule('username', 'not_empty')->rule('username', 'min_length', array(
            ':value',
            '4'
        ))->rule('username', 'max_length', array(
            ':value',
            '30'
        ));
    }
    /**
     * ****validate_login()****
     *@param $arr validation array
     *@validation check
     */
    public function validate_login($arr)
    {
        return Validation::factory($arr)
            ->rule('email', 'not_empty')->rule('email', 'email')->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('password', 'not_empty');
    }
	/** passenger list **/
    public function all_passenger_list($offset, $val, $find_count = FALSE)
    {
        if ($find_count == TRUE) {
            $arguments = array(
               
                array(
                    '$project' => array(
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
                )
            );
            $result    = $this->mongo_db->aggregate(MDB_PASSENGERS, $arguments);
            return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
        } else {
            $arguments = array(
               
                array(
                    '$project' => array(
						'_id' => '$_id',
						'country_code' => '$country_code',
						'phone' => '$phone',
						'profile_image' => '$profile_image',
						'name' => '$name',
						'email' => '$email',
						'address' => '$address',
						'referral_code' => '$referral_code',
						'wallet_amount' => '$wallet_amount',
						'created_date' => '$created_date',
						'user_status' => '$user_status',
                    )
                ),
                array(
                    '$sort' => array(
                        'created_date' => -1
                    )
                ),
                array(
                    '$skip' => (int) $offset
                ),
                array(
                    '$limit' => (int) $val
                )
            );
            $result    = $this->mongo_db->aggregate(MDB_PASSENGERS, $arguments);
            //echo "<pre>"; print_r($result['result']); exit;
            return (!empty($result['result'])) ? $result['result'] : array();
        }
    }
    
    /** for getting passenger listing search **/
    public function get_all_searchpassenger_list($keyword = "", $status = "", $company = "", $offset = "", $val = "", $find_count = false)
    {
		if($offset =="" && $val ==""){
			$find_count = TRUE;
		}
        $keyword     = str_replace("%", "!%", $keyword);
        $keyword     = str_replace("_", "!_", $keyword);		
		$common_arguments = $result = array();
		
        if((!empty($keyword)) && (!empty($status)) && (!empty($company))) {
            $srch_query = array( "\$and" => array(
												array('passengers_id' => array('$ne'=>null )),
												array('company_id' => (int)$company ),
												array('pass.user_status' => $status ),
							array("\$or"=>array(array( 'pass.name' => Commonfunction::MongoRegex("/$keyword/i")) , 
												array( 'pass.lastname' => Commonfunction::MongoRegex("/$keyword/i") ),
												array( 'pass.email' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
        } else if (!empty($keyword) && (!empty($company))) {
            $srch_query = array( "\$and" => array(
												array('passengers_id' => array('$ne'=>null )),
												array('passenger_cid' => (int)$company ),
							array("\$or"=>array(array( 'pass.name' => Commonfunction::MongoRegex("/$keyword/i")) , 
												array( 'pass.lastname' => Commonfunction::MongoRegex("/$keyword/i") ),
												array( 'pass.email' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
        } else if (!empty($keyword) && (!empty($status))) {
            $srch_query = array( "\$and" => array(
												array('passengers_id' => array('$ne'=>null )),
												array('pass.user_status' => $status ),
							array("\$or"=>array(array( 'pass.name' => Commonfunction::MongoRegex("/$keyword/i")) , 
												array( 'pass.lastname' => Commonfunction::MongoRegex("/$keyword/i") ),
												array( 'pass.email' => Commonfunction::MongoRegex("/$keyword/i") ) ) ) ) );
        } else if(!empty($status) && (!empty($company))){
            $srch_query = array( "\$and" => array(
												array('passengers_id' => array('$ne'=>null )),
												array('passenger_cid' => (int)$company ),
												array('user_status' => $status ) ) );
			} else if(!empty($keyword)){
            $srch_query = array( "\$and" => array(array('passengers_id' => array('$ne'=>null )),
										array("\$or"=>array(array( 'pass.name' => Commonfunction::MongoRegex("/$keyword/i") ),
												array( 'pass.lastname' => Commonfunction::MongoRegex("/$keyword/i") ),
												array( 'pass.email' => Commonfunction::MongoRegex("/$keyword/i") ) )
											),
										)
									);
        } else if(!empty($company)){
            $srch_query = array( "\$and" => array(array('company_id' => (int)$company ),array('passengers_id' => array('$ne'=>null )),));
        } else if (!empty($status)) {
            $srch_query = array( "\$and" => array(array('pass.user_status' => $status ),array('passengers_id' => array('$ne'=>null )),));
        }else{
			$srch_query = array('passengers_id' => array('$ne'=>null ));
		}
		if(!empty($srch_query)){			
			$common_arguments = array('$match' => $srch_query);
		}
		$offset = ($offset!="")?$offset:0;
		if($find_count == TRUE){
			 $field_arguments = array(
				array('$lookup' => array('from' => MDB_PASSENGERS,'localField' => 'passengers_id',
													'foreignField' => '_id','as' => 'pass')),
				array('$unwind' =>  array('path' =>  '$pass', 'preserveNullAndEmptyArrays' =>  true ) ),
				$common_arguments,
                array(
                    '$project' => array(
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
                )
            );
            //$merge_arguments = array_merge($common_arguments, $field_arguments);
            $merge_arguments = $field_arguments;
			$result    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);
			
            return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
		}else{
			$field_arguments = array(
				array('$lookup' => array('from' => MDB_PASSENGERS,'localField' => 'passengers_id',
													'foreignField' => '_id','as' => 'pass')),
				array('$unwind' =>  array('path' =>  '$pass', 'preserveNullAndEmptyArrays' =>  true ) ),
				$common_arguments,
				//array('$project' => array('pass_id' => '$pass._id')),
				array(
					'$group' => array(
						'_id'=>array('id' => '$pass._id',
						'country_code' => '$pass.country_code',
						'phone' => '$pass.phone',
						'profile_image' => '$pass.profile_image',
						'name' => '$pass.name',
						'email' => '$pass.email',
						'address' => '$pass.address',
						'referral_code' => '$pass.referral_code',
						'wallet_amount' => '$pass.wallet_amount',
						'created_date' => '$pass.created_date',
						'user_status' => '$pass.user_status',
						)
					)
				),
				array(
					'$sort' => array('created_date' => -1)
				),
				array(
					'$skip' => $offset
				),
				array(
					'$limit' => (int)$val
				)
			);
		}
		
		$merge_arguments = $field_arguments;
		$res    = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS, $merge_arguments);			
		//echo '<pre>';print_r($res);exit;
		if(!empty($res['result'])){
			$i=0;
			foreach($res['result'] as $rr){
				$r = $rr['_id'];
				if(!empty($r)){
					$result[$i]['id'] = $r['id'];
					$result[$i]['name'] = $r['name'];
					$result[$i]['email'] = $r['email'];
					$result[$i]['phone'] = $r['phone'];
					$result[$i]['address'] = isset($r['address']) ? $r['address'] : '--';
					$result[$i]['referral_code'] = isset($r['referral_code'])?$r['referral_code']:'';
					$result[$i]['user_status'] = isset($r['user_status'])?$r['user_status']:'';
					$result[$i]['wallet_amount'] = isset($r['wallet_amount']) ? $r['wallet_amount'] : '';
					$result[$i]['created_date'] = isset($r['created_date'])?commonfunction::convertphpdate('Y-m-d H:i:s', $r['created_date']):'';
					$i++;
				}
			}			
		}		
		
		return $result;	
    }
    
    /**Validating Change Password Details**/
    public function validate_changepwd($arr)
    {
        return Validation::factory($arr)->rule('old_password', 'not_empty')
        //->rule('old_password','alpha_dash')
            ->rule('old_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('old_password', 'max_length', array(
            ':value',
            '16'
        ))->rule('new_password', 'not_empty')
            ->rule('new_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('new_password', 'max_length', array(
            ':value',
            '16'
        ))->rule('confirm_password', 'not_empty')->rule('confirm_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))
            ->rule('confirm_password', 'matches', array(
            ':validation',
            'new_password',
            'confirm_password'
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
            ->rule('conf_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))
        //->rule('conf_password', array(':equals','new_password'))
            
        //->rule('conf_password', 'matches')
            ->rule('conf_password', 'max_length', array(
            ':value',
            '16'
        ));
    }
    /**Validating Forgot Password Details**/
    public function validate_forgotpwd($arr)
    {
        return Validation::factory($arr)->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('email', 'not_empty');
    }
    /** validating the site info settings **/
	public function validate_updatesiteinfo($arr="",$files_value_array="")
	{
					$validator = Validation::factory($arr)     
											   
					->rule('app_name', 'not_empty')
					->rule('app_name', 'max_length', array(':value', '250'))					
					->rule('site_tagline', 'not_empty')
					->rule('site_tagline', 'max_length', array(':value', '50'))
					->rule('app_description', 'not_empty')					
					->rule('contact_email','not_empty')
					->rule('contact_email', 'max_length', array(':value', '30'))
					->rule('contact_email', 'email')     					
					->rule('phone_number','not_empty')
					->rule('phone_number', 'max_length', array(':value', '30'))
					->rule('tell_to_friend_message', 'not_empty')
					->rule('meta_keyword','not_empty')					
					->rule('tax','not_empty')
					->rule('tax','numeric')
					->rule('tax', 'Model_Admin::check_percentage', array(':value'))
					->rule('notification_settings','not_empty')
					->rule('notification_settings','numeric')
					->rule('admin_commission','not_empty')
					->rule('admin_commission','numeric')                       
					->rule('continuous_request_time','not_empty')
					->rule('continuous_request_time','numeric')
					->rule('fare_calculation','not_empty')
					->rule('price_settings','not_empty')
					->rule('default_unit','not_empty')
					->rule('skip_credit_card','not_empty')
					->rule('cancellation_fare','not_empty')
					->rule('referral_settings','not_empty')
					->rule('referral_amount','not_empty')
					 ->rule('referral_amount','numeric')
					->rule('wallet_amount1','not_empty')
					->rule('wallet_amount1','numeric')
					->rule('wallet_amount2','not_empty')
					->rule('wallet_amount2','numeric')
					->rule('wallet_amount3','not_empty')
					->rule('wallet_amount3','numeric')
					->rule('wallet_amount1', 'Model_Admin::compare_wallet_amount1', array(':value',$arr['wallet_amount2'],$arr['wallet_amount3']))
					->rule('wallet_amount2', 'Model_Admin::compare_wallet_amount2', array(':value',$arr['wallet_amount3']))
					->rule('wallet_amount_range','not_empty')
					->rule('driver_referral_setting','not_empty')
					->rule('driver_referral_amount','not_empty')
					 ->rule('driver_referral_amount','numeric')
					->rule('ios_google_map_key','not_empty')
					->rule('ios_google_geo_key','not_empty')
					->rule('web_google_map_key','not_empty')
					->rule('web_google_geo_key','not_empty')
					->rule('google_timezone_api_key','not_empty')
					->rule('android_google_api_key','not_empty')
					->rule('show_map','not_empty')
					->rule('pagination_settings','not_empty')
					->rule('default_miles','not_empty')
					->rule('default_miles','numeric')
					->rule('user_time_zone','not_empty')
					->rule('date_time_format','not_empty')
					->rule('site_language','not_empty')
					->rule('passenger_app_android_store_link','not_empty')
					->rule('passenger_app_ios_store_link','not_empty')
					->rule('app_android_store_link','not_empty')
					->rule('app_ios_store_link','not_empty');
		return $validator;

	}
        
    /** validating the banners images **/
    public function validate_update_module($arr = "", $files_value_array = "")
    {
        return Validation::factory($arr)
		->rule('file', 'Upload::not_empty', array(
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
    /** validating the module settings **/
    public function validate_update_module1($arr = "")
    {
        return Validation::factory($arr)->rule('member', 'not_empty')->rule('member', 'max_length', array(
            ':value',
            '2'
        ));
    }
    /** Updating the banner images **/
    public function update_module_settings_images1($image, $id)
    {
       // echo $image .",". $id;
        $res = $this->mongo_db->findOne(MDB_CMS,array('_id'=>(int)$id),
										array('banner_image1'));
	
		if(!empty($res)){
			$result[] = $res;
		}
        	//echo "<pre>"; print_r($result); exit;
        if (!empty($results[0]['banner_image1'])) { 
            $id1 = DOCROOT . BANNER_IMGPATH . $results[0]['banner_image1'];
            if (file_exists($id1)) {
                $id1 = BANNER_IMGPATH . $results[0]['banner_image1'];
                unlink($id1);
            }
        }
        if ($id > 0) {
            if(isset($image)) { 
                $query = array(
                    'banner_image1' => $image,
                    'type' => '2'
                );
            }
            $result = $this->mongo_db->updateOne(MDB_CMS,array('_id'=>(int)$id),array('$set'=>$query),array('upsert'=>false));
          
        } else {
            
            $query = array(
                    'banner_image1' => $image,
                    'type' => '2',
                    'status' => '1'
                );
            $company_result = $this->mongo_db->insertOne(MDB_CMS,$query);
           // $id = $result;
        }
        return $id;
    }
    public function update_module_settings_images2($image, $id)
    {
       $res = $this->mongo_db->findOne(MDB_CMS,array('_id'=>(int)$id),
										array('banner_image2'));
		if(!empty($res)){
			$result[] = $res;
		}
        
        if (!empty($results[0]['banner_image2'])) {
            $id1 = DOCROOT . BANNER_IMGPATH . $results[0]['banner_image2'];
            if (file_exists($id1)) {
                $id1 = BANNER_IMGPATH . $results[0]['banner_image2'];
                unlink($id1);
            }
        }
        if ($id > 0) {
            if (isset($image)) {
                $query = array(
                    'banner_image2' => $image,
                    'type' => '2'
                );
            }
            $result = $this->mongo_db->updateOne(MDB_CMS,array('_id'=>(int)$id),array('$set'=>$query),array('upsert'=>false));
        } else {
            
            $query = array(
                    'banner_image2' => $image,
                    'type' => '2',
                    'status' => '1'
                );
            $company_result = $this->mongo_db->insertOne(MDB_CMS,$query);
           // $id     = $result[0];
        }
        return $id;
    }
    public function update_module_settings_images3($image, $id)
    {
        $res = $this->mongo_db->findOne(MDB_CMS,array('_id'=>(int)$id),
										array('banner_image3'));
		if(!empty($res)){
			$result[] = $res;
		}
        
        if (!empty($results[0]['banner_image3'])) {
            $id1 = DOCROOT . BANNER_IMGPATH . $results[0]['banner_image3'];
            if (file_exists($id1)) {
                $id1 = BANNER_IMGPATH . $results[0]['banner_image3'];
                unlink($id1);
            }
        }
        if ($id > 0) {
            if (isset($image)) {
                $query = array(
                    'banner_image3' => $image,
                    'type' => '2'
                );
            }
            $result = $this->mongo_db->updateOne(MDB_CMS,array('_id'=>(int)$id),array('$set'=>$query),array('upsert'=>false));
        } else {
            
            $query = array(
                    'banner_image3' => $image,
                    'type' => '2',
                    'status' => '1'
                );
            $company_result = $this->mongo_db->insertOne(MDB_CMS,$query);
            //$id     = $result[0];
        }
        return $id;
    }
    public function update_module_settings_images4($image, $id)
    {
          $res = $this->mongo_db->findOne(MDB_CMS,array('_id'=>(int)$id),
										array('banner_image4'));
		if(!empty($res)){
			$result[] = $res;
		}
        
        if (!empty($results[0]['banner_image4'])) {
            $id1 = DOCROOT . BANNER_IMGPATH . $results[0]['banner_image4'];
            if (file_exists($id1)) {
                $id1 = BANNER_IMGPATH . $results[0]['banner_image4'];
                unlink($id1);
            }
        }
        if ($id > 0) {
            if (isset($image)) {
                $query = array(
                    'banner_image4' => $image,
                    'type' => '2'
                );
            }
            $result = $this->mongo_db->updateOne(MDB_CMS,array('_id'=>(int)$id),array('$set'=>$query),array('upsert'=>false));
        } else {
            
            $query = array(
                    'banner_image4' => $image,
                    'type' => '2',
                    'status' => '1'
                );
            $company_result = $this->mongo_db->insertOne(MDB_CMS,$query);
           // $id     = $result[0];
        }
        return $id;
    }
    public function update_module_settings_images5($image, $id)
    {
        $res = $this->mongo_db->findOne(MDB_CMS,array('_id'=>(int)$id),
										array('banner_image5'));
		if(!empty($res)){
			$result[] = $res;
		}
        
        if (!empty($results[0]['banner_image5'])) {
            $id1 = DOCROOT . BANNER_IMGPATH . $results[0]['banner_image5'];
            if (file_exists($id1)) {
                $id1 = BANNER_IMGPATH . $results[0]['banner_image5'];
                unlink($id1);
            }
        }
        if ($id > 0) {
            if (isset($image)) {
                $query = array(
                    'banner_image5' => $image,
                    'type' => '2'
                );
            }
            $result = $this->mongo_db->updateOne(MDB_CMS,array('_id'=>(int)$id),array('$set'=>$query),array('upsert'=>false));
        } else {
            
            $query = array(
                    'banner_image5' => $image,
                    'type' => '2',
                    'status' => '1'
                );
            $company_result = $this->mongo_db->insertOne(MDB_CMS,$query);
            //$id     = $result[0];
        }
        return $id;
    }
    /** Updating the module settings **/
    public function update_module_settings($post, $count)
    {
        if ($count <= 5) {
			
			$res = $this->mongo_db->findOne(MDB_CMS,array('order'=>array( '$ne'=>0 )),
										array('_id'));
			$count1 = count($res);
			if (isset($post['member0'])) {
                if ($count1 > 0) {
                    $query = array(
                        'content' => $post['member0'],
                        'alt_tags' => $post['tags1'],
                        'type' => '3',
                        'status' => '1',
                        'order' => '1'
                    );
                    $result = $this->mongo_db->updateOne(MDB_CMS,array('order'=>1),array('$set'=>$query),array('upsert'=>false));
                    
                    // Checked the updated query status isset
                    if ($result == 1) {
                        $update_sstatus = $result;
                    }
                } else {
					 $query = array(
                    'content' => $post['member0'],
                    'alt_tags' => $post['tags1'],
                    'type' => '3',
                    'status' => '1',
                    'order' => '1'
                    
					);
					$company_result = $this->mongo_db->insertOne(MDB_CMS,$query);
					if ($rs == 1) {
						$update_sstatus = $company_result;
					}
               
                }
            }
            if (isset($post['member1'])) {
                if ($count1 > 0) {
                    $query = array(
                        'content' => $post['member1'],
                        'alt_tags' => $post['tags2'],
                        'type' => '3',
                        'status' => '1',
                        'order' => '2'
                    );
                    $result = $this->mongo_db->updateOne(MDB_CMS,array('order'=>2),array('$set'=>$query),array('upsert'=>false));
                    // Checked the updated query status isset
                    if ($rs == 1) {
                        $update_sstatus = $rs;
                    }
                } else {
                    $query = array(
                    'content' => $post['member1'],
                    'alt_tags' => $post['tags2'],
                    'type' => '3',
                    'status' => '1',
                    'order' => '2'
                    
					);
					$company_result = $this->mongo_db->insertOne(MDB_CMS,$query);
					if ($rs == 1) {
						$update_sstatus = $company_result;
					}
				}
            }
            if (isset($post['member2'])) {
                if ($count1 > 0) {
                    $query = array(
                        'content' => $post['member2'],
                        'alt_tags' => $post['tags3'],
                        'type' => '3',
                        'status' => '1',
                        'order' => '3'
                    );
                    $result = $this->mongo_db->updateOne(MDB_CMS,array('order'=>3),array('$set'=>$query),array('upsert'=>false));
                    // Checked the updated query status isset
                    if ($rs == 1) {
                        $update_sstatus = $rs;
                    }
                } else {
                    $query = array(
                    'content' => $post['member2'],
                    'alt_tags' => $post['tags3'],
                    'type' => '3',
                    'status' => '1',
                    'order' => '3'
                    
					);
					$company_result = $this->mongo_db->insertOne(MDB_CMS,$query);
					if ($rs == 1) {
						$update_sstatus = $company_result;
					}
				}
            }
            if (isset($post['member3'])) {
                if ($count1 > 0) {
                    $query = array(
                        'content' => $post['member3'],
                        'alt_tags' => $post['tags4'],
                        'type' => '3',
                        'status' => '1',
                        'order' => '4'
                    );
                    $result = $this->mongo_db->updateOne(MDB_CMS,array('order'=>4),array('$set'=>$query),array('upsert'=>false));
                    // Checked the updated query status isset
                    if ($rs == 1) {
                        $update_sstatus = $rs;
                    }
                } else {
                    $query = array(
                    'content' => $post['member3'],
                    'alt_tags' => $post['tags4'],
                    'type' => '3',
                    'status' => '1',
                    'order' => '4'
                    
					);
					$company_result = $this->mongo_db->insertOne(MDB_CMS,$query);
					if ($rs == 1) {
						$update_sstatus = $company_result;
					}
				}
            }
            
           
            if (isset($post['member4'])) {
                if ($count1 > 0) {
                    $query = array(
                        'content' => $post['member4'],
                        'alt_tags' => $post['tags5'],
                        'type' => '3',
                        'status' => '1',
                        'order' => '5'
                    );
                    $result = $this->mongo_db->updateOne(MDB_CMS,array('order'=>5),array('$set'=>$query),array('upsert'=>false));
                    // Checked the updated query status isset
                    if ($rs == 1) {
                        $update_sstatus = $rs;
                    }
                } else {
                    $query = array(
                    'content' => $post['member4'],
                    'alt_tags' => $post['tags5'],
                    'type' => '3',
                    'status' => '1',
                    'order' => '5'
                    
					);
					$company_result = $this->mongo_db->insertOne(MDB_CMS,$query);
					if ($rs == 1) {
						$update_sstatus = $company_result;
					}
				}
            }
            if (isset($update_sstatus)) {
                return $update_sstatus;
            } else {
                return 0;
            }
            
        } 
										
      
    }
    /** site logo **/
    public function updatesiteinfo_image($image)
    {
        $query  = array(
            'site_logo' => $image
        );
        //MongoDB
        $result = $this->mongo_db->updateOne(MDB_THEME_SETTINGS, array(
            '_id' => 1
        ), array(
            '$set' => $query
        ), array(
            'upsert' => true
        ));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    /** site email logo **/
    public function updatesite_email_einfo_image($image)
    {
        $query  = array(
            'email_site_logo' => $image
        );
        //MongoDB
        $result = $this->mongo_db->updateOne(MDB_THEME_SETTINGS, array(
            '_id' => 1
        ), array(
            '$set' => $query
        ), array(
            'upsert' => true
        ));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    /** site favicon image **/
    public function updatesiteinfo_faviconimage($image)
    {
        $results = $this->mongo_db->findOne(MDB_THEME_SETTINGS, array(
            '_id' => 1
        ), array(
            'site_favicon'
        ));
        if (!empty($results['site_favicon'])) {
            $id1 = DOCROOT . SITE_FAVICON_IMGPATH . $results['site_favicon'];
            if (file_exists($id1)) {
                unlink($id1);
            }
        }
        $query  = array(
            'site_favicon' => $image
        );
       
        $result = $this->mongo_db->updateOne(MDB_THEME_SETTINGS, array(
            '_id' => 1
        ), array(
            '$set' => $query
        ), array(
            'upsert' => true
        ));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
     /** site banner image **/
    public function updatesiteinfo_bannerimage($image,$field="banner_image")
    {
        $results = $this->mongo_db->findOne(MDB_THEME_SETTINGS, array(
            '_id' => 1
        ), array(
            $field
        ));
        if (!empty($results[$field])) {
            $id1 = DOCROOT . PUBLIC_UPLOADS_LANDING_FOLDER . $results[$field];
            if (file_exists($id1)) {
                unlink($id1);
            }
        }
        $query  = array(
            $field => $image
        );
       
        $result = $this->mongo_db->updateOne(MDB_THEME_SETTINGS, array(
            '_id' => 1
        ), array(
            '$set' => $query
        ), array(
            'upsert' => false
        ));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
	/** frontend mobile image **/
    public function updatesiteinfo_frontimage($image, $field)
    {
        $results = $this->mongo_db->findOne(MDB_THEME_SETTINGS, array(
            '_id' => 1
        ), array(
            $field
        ));
        if (!empty($results[$field])) {
            $id1 = DOCROOT . PUBLIC_UPLOADS_LANDING_FOLDER . $results[$field];
            if (file_exists($id1)) {
                unlink($id1);
            }
        }
        $query  = array(
            $field => $image
        );
       
        $result = $this->mongo_db->updateOne(MDB_THEME_SETTINGS, array(
            '_id' => 1
        ), array(
            '$set' => $query
        ), array(
            'upsert' => false
        ));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function updatesiteinfo($post_value_array)
    {
        $admin_commision_setting = isset($post_value_array['admin_commision_setting']) ? $post_value_array['admin_commision_setting'] : 0;
        $company_commision_setting = isset($post_value_array['company_commision_setting']) ? $post_value_array['company_commision_setting'] : 0;
        $driver_commision_setting = isset($post_value_array['driver_commision_setting']) ? $post_value_array['driver_commision_setting'] : 0;
        $date_time_format_script = "yy-mm-dd hh:mm:ss";
        $wallet_amount_range = $post_value_array['wallet_amount1'].'-'.$post_value_array['wallet_amount3'];
        $site_language_arr = $post_value_array['site_language'];
        $post_site_language = $post_value_array['site_language'];
        $site_language = implode(',', $post_site_language);
        $selected_language = SELECTED_LANGUAGE;
        $post_lang = array_flip($post_site_language);
        $web_lang_array = WEB_DB_LANGUAGE;
        $ios_db_driver_lang = IOS_DRIVER_LANG;
        $ios_db_passenger_lang = IOS_PASSENGER_LANG;
        $ios_db_driver_colorcode = IOS_DRIVER_COLORCODE;
        $ios_db_passenger_colorcode = IOS_PASSENGER_COLORCODE;
        $android_db_driver_lang = ANDROID_DRIVER_LANG;
        $android_db_passenger_lang = ANDROID_PASSENGER_LANG;
        $android_db_driver_colorcode = ANDROID_DRIVER_COLORCODE;
        $android_db_passenger_colorcode = ANDROID_PASSENGER_COLORCODE;
        $web_new_lang_array = $ios_new_driver_lang = $ios_new_passenger_lang = $ios_new_driver_colorcode = $ios_new_passenger_colorcode = $android_new_driver_lang = $android_new_passenger_lang = $android_new_driver_colorcode = $android_new_passenger_colorcode = array();
        foreach($post_lang as $k => $v){ 
            if(array_key_exists($k,$web_lang_array)){ $web_new_lang_array[$k] = $web_lang_array[$k]; } else{ $web_new_lang_array[$k]=1; }
            if(array_key_exists($k,$ios_db_driver_lang)){ $ios_new_driver_lang[$k] = $ios_db_driver_lang[$k]; } else{ $ios_new_driver_lang[$k]=1; }
            if(array_key_exists($k,$ios_db_passenger_lang)){ $ios_new_passenger_lang[$k] = $ios_db_passenger_lang[$k]; } else{ $ios_new_passenger_lang[$k]=1; }
            if(array_key_exists($k,$ios_db_driver_colorcode)){ $ios_new_driver_colorcode[$k] = $ios_db_driver_colorcode[$k]; } else{ $ios_new_driver_colorcode[$k]=1; }
            if(array_key_exists($k,$ios_db_passenger_colorcode)){ $ios_new_passenger_colorcode[$k] = $ios_db_passenger_colorcode[$k]; } else{ $ios_new_passenger_colorcode[$k]=1; }
            if(array_key_exists($k,$android_db_driver_lang)){ $android_new_driver_lang[$k] = $android_db_driver_lang[$k]; } else{ $android_new_driver_lang[$k]=1; }
            if(array_key_exists($k,$android_db_passenger_lang)){ $android_new_passenger_lang[$k] = $android_db_passenger_lang[$k]; } else{ $android_new_passenger_lang[$k]=1; }
            if(array_key_exists($k,$android_db_driver_colorcode)){ $android_new_driver_colorcode[$k] = $android_db_driver_colorcode[$k]; } else{ $android_new_driver_colorcode[$k]=1; }
            if(array_key_exists($k,$android_db_passenger_colorcode)){ $android_new_passenger_colorcode[$k] = $android_db_passenger_colorcode[$k]; } else{ $android_new_passenger_colorcode[$k]=1; }
        }
        $query  = array(
            'app_name' => $post_value_array['app_name'],
            'app_description' => $post_value_array['app_description'],
            'email_id' => $post_value_array['contact_email'],
            'phone_number' => $post_value_array['phone_number'],
            'meta_keyword' => $post_value_array['meta_keyword'],
            'meta_description' => $post_value_array['meta_description'],
            'show_map' => $post_value_array['show_map'],
            'site_tagline' => $post_value_array['site_tagline'],
            'notification_settings' => $post_value_array['notification_settings'],
            'pagination_settings' => $post_value_array['pagination_settings'],
            'tell_to_friend_message' => $post_value_array['tell_to_friend_message'],
            'admin_commission' => $post_value_array['admin_commission'],
            'tax' => $post_value_array['tax'],
            'sms_enable' => $post_value_array['sms_enable'],
            'default_unit' => $post_value_array['default_unit'],
            'skip_credit_card' => $post_value_array['skip_credit_card'],
            'cancellation_fare_setting' => $post_value_array['cancellation_fare'],
            'fare_calculation_type' => $post_value_array['fare_calculation'],
            'price_settings' => $post_value_array['price_settings'],
            'referral_settings' => $post_value_array['referral_settings'],
            'referral_amount' => $post_value_array['referral_amount'],
            'wallet_amount1' => $post_value_array['wallet_amount1'],
            'wallet_amount2' => $post_value_array['wallet_amount2'],
            'wallet_amount3' => $post_value_array['wallet_amount3'],
            'wallet_amount_range' => $wallet_amount_range,
            'continuous_request_time' => $post_value_array['continuous_request_time'],
			'driver_referral_setting'=>$post_value_array['driver_referral_setting'],
			'driver_referral_amount'=>$post_value_array['driver_referral_amount'],
			'ios_google_map_key'=>$post_value_array['ios_google_map_key'],
			'ios_google_geo_key'=>$post_value_array['ios_google_geo_key'],
			'web_google_map_key'=>$post_value_array['web_google_map_key'],
			'web_google_geo_key'=>$post_value_array['web_google_geo_key'],
			'google_timezone_api_key'=>$post_value_array['google_timezone_api_key'],
			'android_google_key'=>$post_value_array['android_google_api_key'],
			'android_google_key'=>$post_value_array['android_google_api_key'],
			'default_miles'=>$post_value_array['default_miles'],
			'date_time_format'=>$post_value_array['date_time_format'],
			'user_time_zone' => $post_value_array['user_time_zone'],
			'date_time_format_script' => $date_time_format_script,
			'admin_commision_setting' => $admin_commision_setting,
			'company_commision_setting' => $company_commision_setting,
			'driver_commision_setting' => $driver_commision_setting,
			'passenger_app_android_store_link' =>  $post_value_array['passenger_app_android_store_link'],
			'passenger_app_ios_store_link' =>  $post_value_array['passenger_app_ios_store_link'],
			'app_android_store_link' =>  $post_value_array['app_android_store_link'],
			'app_ios_store_link' =>  $post_value_array['app_ios_store_link'],
			'site_default_language' =>  $site_language,
                        'website_language_settings'	=>$web_new_lang_array,
                        'ios_driver_language_settings' => $ios_new_driver_lang,
                        'ios_passenger_language_settings' => $ios_new_passenger_lang,
                        'ios_driver_colorcode_settings' => $ios_new_driver_colorcode,
                        'ios_passenger_colorcode_settings' => $ios_new_passenger_colorcode,
                        'android_driver_language_settings' => $android_new_driver_lang,
                        'android_passenger_language_settings' => $android_new_passenger_lang,
                        'android_driver_colorcode_settings' => $android_new_driver_colorcode,
                        'android_passenger_colorcode_settings' => $android_new_passenger_colorcode,
                        
        );
        if(!in_array($selected_language, $site_language_arr) && isset($site_language_arr[0])){
            $query['selected_language'] = $site_language_arr[0];
        }
        $query['footer_bg_color_1'] =  isset($post_value_array['footer_bg_color_1']) ? $post_value_array['footer_bg_color_1'] : '';
        $query['app_bg_color_1'] =  isset($post_value_array['app_bg_color_1']) ? $post_value_array['app_bg_color_1'] : '';
        $query['theme_id'] =  isset($post_value_array['theme_id'])?$post_value_array['theme_id']:1;
		
		# language updation to mobile apps
		$language_keys = array_keys(DYNAMIC_LANGUAGE_ARRAY);
        $intersect = array_intersect($site_language_arr,$language_keys);
        $mobile_lang_status = 0;
        if(count($language_keys) != count($site_language_arr)){
			$mobile_lang_status = 1;
		}else if(count($intersect) != count($site_language_arr)){
			$mobile_lang_status = 1;
		}
		
		if($mobile_lang_status == 1){
			$time = date('YmdHis');
			$query['ios_driver_language_status'] = $time;
			$query['ios_passenger_language_status'] = $time;
			$query['android_driver_language_status'] = $time;
			$query['android_passenger_language_status'] = $time;	
		}
		# language updation to mobile apps END
		
        //echo '<pre>';print_r($post_value_array);exit;
        $result = $this->mongo_db->updateOne(MDB_SITEINFO, array(
            '_id' => 1
        ), array(
            '$set' => $query
        ), array(
            "upsert" => true
        ));
        
        if((empty($result->getwriteErrors())) && $post_value_array['prev_referral_amount'] != $post_value_array['referral_amount']) {
			
			//all passengers referral code amount updated here
			$update_arr1 = array('referral_code_amount' => (double)$post_value_array['referral_amount']);
			$result = $this->mongo_db->updateMany(MDB_PASSENGERS, array('_id' => array('$gt'=>0)), array('$set' => $update_arr1));
		}
		
		//update referral amount of driver
		if((empty($result->getwriteErrors())) && $post_value_array['prev_driver_referral_amount'] != $post_value_array['driver_referral_amount']) {
			
			//all drivers referral code amount updated here
			$update_arr2 = array('registered_driver_code_amount' => (double)$post_value_array['driver_referral_amount']);
			$result = $this->mongo_db->updateMany(MDB_DRIVER_REF, array('_id' => array('$gt'=>0)), array('$set' => $update_arr2));
		}
        
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function validate_update_socialinfo($arr = "")
    {
        return Validation::factory($arr)->rule('facebook_key', 'not_empty')
						->rule('facebook_secretkey', 'not_empty')
						->rule('facebook_share', 'not_empty')
						->rule('twitter_share', 'not_empty')
						->rule('google_share', 'not_empty')
						->rule('linkedin_share', 'not_empty')
						->rule('facebook_follow_link','not_empty')
						->rule('google_follow_link','not_empty')
						->rule('twitter_follow_link','not_empty')
						->rule('itune_passenger', 'not_empty')
						->rule('itune_driver','not_empty')
						->rule('fb_profile','not_empty');
    }
    public function update_socialinfo($post_value_array)
    {
        $query  = array(
            'facebook_key' => $post_value_array['facebook_key'],
            'facebook_secretkey' => $post_value_array['facebook_secretkey'],
            'facebook_share' => $post_value_array['facebook_share'],
            'twitter_share' => $post_value_array['twitter_share'],
            'google_share' => $post_value_array['google_share'],
            'linkedin_share' => $post_value_array['linkedin_share'],
            'facebook_follow_link' =>  $post_value_array['facebook_follow_link'],
			'google_follow_link' =>  $post_value_array['google_follow_link'],
			'twitter_follow_link' =>  $post_value_array['twitter_follow_link'],
			'itune_passenger' =>  $post_value_array['itune_passenger'],
			'itune_driver' =>  $post_value_array['itune_driver'],
			'fb_profile' =>  $post_value_array['fb_profile']
        );
        //MongoDB
        $result = $this->mongo_db->updateOne(MDB_SITEINFO, array(
            '_id' => 1
        ), array(
            '$set' => $query
        ), array(
            "upsert" => true
        ));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    public function validate_update_payment_submit($arr = "")
    {
        return Validation::factory($arr)
		->rule('payment_gatway_name', 'not_empty')
		->rule('description', 'not_empty')
		->rule('currency_code', 'not_empty')
		->rule('currency_code', 'max_length', array(
            ':value',
            '3'
        ))->rule('currency_symbol', 'not_empty')->rule('currency_symbol', 'max_length', array(
            ':value',
            '1'
        ))->rule('payment_method', 'not_empty')
		->rule('payment_gateway_username', 'not_empty')
		->rule('payment_gateway_password', 'not_empty')
		->rule('payment_gateway_signature', 'not_empty');
    }
    public function check_array($value = "")
    {
        if (!empty($value)) {
            return true;
        } else {
            return false;
        }
    }
    public function update_payment_submit($post_value_array)
    {		
        $update = 0;
        foreach ($post_value_array['payid'] as $k => $id) {
            if ($id == $post_value_array['default'][0]) {
                $default = 1;
            } else {
                $default = 0;
            }
            if (in_array($id, $post_value_array['paymodstatus'])) {
                $paystatus = 1;
            } else {
                $paystatus = 0;
            }
			$payment_modules_data = array(
                'pay_mod_active' => $paystatus,
                'pay_mod_default' => $default
            );
			$pay_result = $this->mongo_db->updateOne(MDB_PAYMENT_MODULES,array('_id'=>(int)$id),array('$set'=>$payment_modules_data),array('upsert'=>true));            
        }
        return 1;// (!isset($pay_result['err'])) ? 1 : 0;
    }
    public function site_payment_gateways($id)
    {	
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_PAYMENT_GATEWAYS,array('_id'=>(int)$id));
        return (!empty($result))?$result:array();
    }
	//To Get all payment modules
	public function payment_modules()
	{
                ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[                            
                            'sort'=>[
                                '_id'=>1
                                 ]                            
                            ];
                        $result = $this->mongo_db->find(MDB_PAYMENT_MODULES,[],$options);
		
		return (!empty($result))?$result:array();
	}
    public function get_payment_gateways($offset, $val,$find_count=false)
    {		
				
		if($find_count){ 
			$result = $this->mongo_db->count(MDB_PAYMENT_GATEWAYS,array('payment_status'=>array('$ne'=>'T'),'company_id'=>0),array('_id','company_id','payment_gateway_id','payment_gatway', 'description', 'payment_method', 'payment_gateway_username', 'payment_gateway_password', 'payment_gateway_signature','payment_gateway_id', 'payment_status', 'default_payment_gateway', 'live_payment_gateway_username', 'live_payment_gateway_password', 'live_payment_gateway_signature'));
			
			return $result;
		} else {
			
			//$result = $this->mongo_db->find(MDB_PAYMENT_GATEWAYS,array('payment_status'=>array('$ne'=>'T'),'company_id'=>0),array('_id','company_id','payment_gateway_id','payment_gatway', 'description', 'currency_code', 'currency_symbol', 'payment_method', 'payment_gateway_username', 'payment_gateway_password', 'payment_gateway_signature','payment_gateway_id', 'payment_status', 'default_payment_gateway', 'live_payment_gateway_username', 'live_payment_gateway_password', 'live_payment_gateway_signature'))->skip($offset)->limit($val);
                      ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                        $options=[
                            'projection'=>[
                                '_id'=>1,
                                'company_id'=>1,
                                'payment_gateway_id'=>1,
                                'payment_gatway'=>1,
                                'description'=>1,                               
                                'payment_method'=>1,
                                'payment_gateway_username'=>1,
                                'payment_gateway_password'=>1,
                                'payment_gateway_signature'=>1,
                                'payment_gateway_id'=>1,
                                'payment_status'=>1,
                                'default_payment_gateway'=>1,
                                'live_payment_gateway_username'=>1,
                                'live_payment_gateway_password'=>1,
                                'live_payment_gateway_signature'=>1
                            ],                            
                            'skip'=>(int)$offset,
                            'limit'=>(int)$val    
                         ];
                        $result = $this->mongo_db->find(MDB_PAYMENT_GATEWAYS,array('payment_status'=>array('$ne'=>'T'),'company_id'=>0),$options);
			$result   = $result;
			$data = array();
			if(count($result) > 0){
				foreach($result as $val){
					$arr = $val;
					$arr['id'] = $val['_id'];
					$data[] = $arr;
				}
			}
			return $data; 
		}
    }
	public function count_withdraw_payment_mode(){
		$offset = $val = '';
		$this->get_withdraw_payment_mode($offset, $val,TRUE);
	}
	
	public function get_withdraw_payment_mode($offset, $val,$find_count=false)
    {
		if($offset =="" && $val=="")
		{
			$find_count=TRUE;
		}
		//MongoDB
		if($find_count){
			$result = $this->mongo_db->count(MDB_WITHDRAW_PAYMENT_MODE,array());
			return $result;
		} else {
			//$project = array('_id','payment_mode_name','payment_mode_status','payment_mode_createdate');
			//$response = $this->mongo_db->find(MDB_WITHDRAW_PAYMENT_MODE,array(), $project)->skip($offset)->limit($val);
                    ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                         $options=[
                            'projection'=>[
                               '_id'=>1,
                                'payment_mode_name'=>1,
                                'payment_mode_status'=>1,
                                'payment_mode_createdate'=>1
                                ],
                            'skip'=>(int)$offset,
                            'limit'=>(int)$val    
                         ];
                         $response = $this->mongo_db->find(MDB_WITHDRAW_PAYMENT_MODE,[],$options);
			$result   = $response;
			//print_r($result); exit;
			if(count($result)>0)
			{
				foreach($result as $val){
					$arr=$val;
					$arr['withdraw_payment_mode_id']=$val['_id'];
					$data[] = $arr;
				}
			}
			
			return $data;
			
		}
    }
    /** update default country status **/
    public function update_default_payment($id)
    {
        //MongoDB
        $pid = (int)$id;
        $gateway = $this->mongo_db->findOne(MDB_PAYMENT_GATEWAYS,array('_id'=>$pid),array('payment_status'));
        if(!empty($gateway['payment_status']) && $gateway['payment_status']=='A'){
            //update default status with 1
            $res = $this->mongo_db->updateOne(MDB_PAYMENT_GATEWAYS,array('_id'=>$pid),array('$set'=>array('default_payment_gateway' => 1)),array('upsert'=>true));
            //update default status with 0
            if(empty($res->getwriteErrors())){
                    $result = $this->mongo_db->updateMany(MDB_PAYMENT_GATEWAYS,array('_id'=>array('$ne'=>$pid),'company_id'=>0,'default_payment_gateway'=>1),array('$set'=>array('default_payment_gateway' => 0)));
            }
            return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
        } else {
            return -1;
        }
    }
    public function mail_settings()
    {
        //MongoDB
        $res    = $this->mongo_db->findOne(MDB_SMTP_SETTINGS, array(
            '_id' => 1
        ));
        $result = array();
        foreach ($res as $keys => $values) {
            $result[0][$keys] = $values;
        }
        return $result;
    }
    public function sms_template()
    {
		## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
		$options=[
		'projection'=>[
		   '_id'=>1,
			'sms_title'=>1,
			'sms_info'=>1,
			'sms_description'=>1,
			'status'=>1
			],
		 'sort'=>[
		   '_id'=>1
		 ]                                
		];
		$response = $this->mongo_db->find(MDB_SMS_TEMPLATES,[],$options);
        $result   = $response;
        $data = array();
        $removed_sms = array('booking_failed_sms','invite_friend_sms','driver_tell_to_friend');
        if(count($result) > 0){
			foreach($result as $val){
				$arr = $val;
				if(!in_array($val['sms_title'],$removed_sms)){					
					$arr['sms_id'] = $val['_id'];
					$data[] = $arr;
				}
			}
		}
        return $data;
    }
	public function payment_settings()
	{
            //$response = $this->mongo_db->find(MDB_PAYMENT_MODULES)->sort(array('_id' => 1));
                ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                         $options=[                            
                             'sort'=>[
                               '_id'=>1
                             ]                                
                         ];
                         $response = $this->mongo_db->find(MDB_PAYMENT_MODULES,[],$options);
        $result   = $response;
        return $result;
	}
    public function validate_mailsettings($arr = "")
    {
        return Validation::factory($arr)->rule('smtp_host', 'not_empty')->rule('smtp_host', 'max_length', array(
            ':value',
            '50'
        ))->rule('smtp_port', 'not_empty')->rule('smtp_port', 'max_length', array(
            ':value',
            '4'
        ))->rule('smtp_username', 'not_empty')->rule('smtp_username', 'max_length', array(
            ':value',
            '50'
        ))->rule('smtp_password', 'not_empty')->rule('smtp_password', 'max_length', array(
            ':value',
            '50'
        ))->rule('transport_layer_security', 'not_empty')->rule('smtp', 'not_empty');
    }
    public function updatemailsetting($post_value_array)
    {
        $query  = array(
            'smtp_host' => $post_value_array['smtp_host'],
            'smtp_port' => $post_value_array['smtp_port'],
            'smtp_username' => $post_value_array['smtp_username'],
            'smtp_password' => $post_value_array['smtp_password'],
            'transport_layer_security' => $post_value_array['transport_layer_security'],
            'smtp' => $post_value_array['smtp']
        );
        //MongoDB
        $result = $this->mongo_db->updateOne(MDB_SMTP_SETTINGS, array(
            '_id' => 1
        ), array(
            '$set' => $query
        ), array(
            'upsert' => true
        ));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    public function get_activeusers_list()
    {
		$arguments = array(
			array(
				'$match' => array('login_status' => 'A')
			),
			array(
				'$project' => array(
					'name' => '$name',
					'last_login' => '$last_login',
					'phone' => '$phone',
					'address' => '$address'
				)
			),
			array('$skip' => 0),
			array('$limit' => 10)
		);
		$result          = $this->mongo_db->aggregate(MDB_PASSENGERS, $arguments);
		//echo "<pre>";print_r($result['result']); exit;
		return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
    }
    //get all active users list
    public function get_all_activeusers_list($cid = 0)
    {		
		$match_array = array();
		$match_array['login_status']= 'A';
		if (!empty($cid) && $cid!=0) {
			$match_array['passenger_cid']= (int)$cid;
		}
                //$results = $this->mongo_db->find(MDB_PASSENGERS,$match_array)->sort(array('last_login'=>-1));
                 ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                         $options=[                            
                             'sort'=>[
                               'last_login'=>-1
                             ]                                
                         ];
		$results = $this->mongo_db->find(MDB_PASSENGERS,$match_array,$options);
		return (!empty($results))?$results:array();
    }
	
    //dashboard active users count
    public function get_activeusers_list_count($cid = 0)
    {
		if (!empty($cid)) {
			$condition = array('login_status' => 'A','passenger_cid' => (int)$cid);
        }else{
			$condition = array('login_status' => 'A');
		}
		$result = $this->mongo_db->count(MDB_PASSENGERS,$condition,array('_id'));
		return $result;
    }
	
    //get all active user list
    public function all_users_list($offset = '', $val = '', $cid = 0,$find_count=false)
    {
		$match_array = array();
		$match_array['login_status']= 'A';
		if (!empty($cid) && $cid!=0) {
			$match_array['passenger_cid']= (int)$cid;
		}
		if($find_count==TRUE){
			$result = $this->mongo_db->count(MDB_PASSENGERS,$match_array,array('_id'));
			//echo $result; exit;
			return $result;
		}else{
                         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                         $options=[
                             'projection'=>[
                                 '_id'=>1,
                                 'name'=>1,
                                 'last_login'=>1,
                                 'phone'=>1,
                                 'address'=>1
                             ],
                             'sort'=>[
                               'last_login'=>-1
                             ],
                             'skip'=>(int)$offset,
                             'limit'=>(int)$val
                         ];
                         $results = $this->mongo_db->find(MDB_PASSENGERS,$match_array,$options);
			return (!empty($results))?$results:array();	
		}
    }
	
   
    public function get_availabletaxi_list_count()
    {
		$currentdate = date('Y-m-d H:i:s');
        $enddate     = date('Y-m-d') . ' 23:59:59';
		$match_query = array();
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
		if ($currentdate!="" && $enddate!="") {
			$match_query['mapping.mapping_startdate'] = array('$gte' => $currentdate);
			$match_query['mapping.mapping_enddate'] = array('$lt' => $enddate);
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
		$result          = $this->mongo_db->aggregate(MDB_CSC, $arguments);
		return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
    }
    
    public function free_taxi_list($find_count = false, $cid = 0)
    {
        $usertype       = $this->usertype;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
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
        if ($cid != 0) {
			$match_query['taxi_company'] = (int)$cid;
        }
        if ($usertype == 'M') {
			$match_query['taxi_country'] = (int)$country_id;
			$match_query['taxi_state'] = (int)$state_id;
			$match_query['taxi_city'] = (int)$city_id;
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
			);
			$arguments = array_merge($common_arguments,$field_arguments);
			$result    = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
			//echo "<pre>"; print_r($result); exit;
			return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		}
	}	
	
	public function free_taxi_list_all_pag($offset, $val, $cid = 0,$find_count=false)
    {
        $usertype       = $this->usertype;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
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
        if (!empty($cid) && $cid!=0) {
			$match_query['taxi_company'] = (int)$cid;
        }
        if ($usertype == 'M') {
			$match_query['taxi_country'] = (int)$country_id;
			$match_query['taxi_state'] = (int)$state_id;
			$match_query['taxi_city'] = (int)$city_id;
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
    public function free_driver_list_count()
    {
		$assigned_driver = $this->free_availabletaxi_list();
		$match_query = $driver_list = array();
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
		$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
		//echo "<pre>"; print_r($result); exit;
		return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
    }
	
    public function free_availabletaxi_list()
    {
		$company_id  = $this->session->get('company_id');
		$cuurentdate = date('Y-m-d H:i:s');
        $enddate     = date('Y-m-d') . ' 23:59:59';
		$match_query = array();
		$match_query['people.status'] = 'A';
		$match_query['mapping.mapping_status'] = 'A';
		if ($company_id!="" && $company_id!=0) {
			$match_query['taxi_company'] = (int)$company_id;
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
    /** selecting the banner image for module settings **/
    public function site_module_settings()
    {
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                    '_id'=>1,
                    'banner_image1'=>1,
                    'banner_image2'=>1,
                    'banner_image3'=>1,
                    'banner_image4'=>1,
                    'banner_image5'=>1                                
                ]                             
            ];
            $response = $this->mongo_db->find(MDB_CMS,['type' => 2,'status_post'=> 'P'],$options);	
            $result   = $response;
        $data = array();
       // print_r($result); exit;
        if(count($result) > 0){
			
			foreach($result as $val){
				
				//$arr = $val;
				$arr['id'] = (isset($val['_id'])?$val['_id']:"");
				$arr['banner_image1'] = (isset($val['banner_image1'])?$val['banner_image1']:"");
				$arr['banner_image2'] = (isset($val['banner_image2'])?$val['banner_image2']:"");
				$arr['banner_image3'] = (isset($val['banner_image3'])?$val['banner_image3']:"");
				$arr['banner_image4'] = (isset($val['banner_image4'])?$val['banner_image4']:"");
				$arr['banner_image5'] = (isset($val['banner_image5'])?$val['banner_image5']:"");
				$data[] = $arr;
			}
		}
       
		
		return (!empty($data))?Commonfunction::change_key($data):array();
    }
    /** selecting the tag descriptions for module settings **/
    public function site_info_settings()
    {
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                         $options=[
                             'projection'=>[
                                 'content'=>1,
                                 'alt_tags'=>1                                                                
                             ],
                             'sort'=>[
                                 'order_status'=>1
                             ]                             
                         ];
                         $result = $this->mongo_db->find(MDB_CMS,['type' => 3,'status_post'=> 'P'],$options);		
		return (!empty($result))?Commonfunction::change_key($result):array();
    }
    /** validate the menu settings  **/
    public function validate_update_menusettings($arr = "")
    {
        return Validation::factory($arr)->rule('menu_name', 'not_empty')->rule('menu_link', 'not_empty');
        //->rule('status_post','not_empty');
    }
    /** validate the menu settings  **/
    public function validate_update_menusettings1($arr = "")
    {
        return Validation::factory($arr)->rule('menu_name1', 'not_empty')->rule('menu_link1', 'not_empty');
        //->rule('status_post','not_empty');
    }
    public function get_admin_dashboard_data()
    {
		$result["general_users"] = $this->mongo_db->count(MDB_PASSENGERS,array('user_status' => 'A'),array('_id'));
		$result["driver"] = $this->mongo_db->count(MDB_PEOPLE,array('user_type' => 'D','status' => 'A'),array('_id'));
		$arguments = array(array('$lookup' => array('from'=>MDB_PEOPLE,
													'localField'=> '_id',
													'foreignField' => "company_id",
													'as'=> "people")),
			array('$unwind' => '$people'),
			array('$match'=> array('people.user_type'=>'C','people.status' => 'A')),
			array('$project' => array('id' 	=> '$_id')),
			array('$group' =>array('_id' => NULL,'count' => array('$sum' => 1))),
		);
		$company_count = $this->mongo_db->aggregate(MDB_COMPANY,$arguments);
		//print_r($company_count); exit;
        $result["company"] = (isset($company_count['result'][0]['count']))?$company_count['result'][0]['count']:0;
		$result["manager"] = $this->mongo_db->count(MDB_PEOPLE,array('user_type' => 'M','status' => 'A'),array('_id'));
		$result["taxi"] = $this->mongo_db->count(MDB_TAXI,array('taxi_status' => 'A'),array('_id'));
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
	
	public function getUserbyCompany()
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
			array(
				'$match' => array('user_type' => 'D')
			),
			array(
				'$project' => array(
					'company_id' => '$company_id',
					'company_name' => '$company.companydetails.company_name'
				)
			),
			
			array(
				'$group' =>array(
					'_id' => array(
						'company_id'=>'$company_id',
						'company_name'=>'$company_name',
					),
					'count' => array(
						'$sum' => 1
					),
				)
			),
		);
		$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
		$result = (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		$result_val   = "";
		if(count( $result ) > 0){
			foreach ($result as $res) {
				$result_val .= "['" . $res['_id']['company_name'] . "', " . $res["count"] . "" . "],";
			}	
		}
		$result = rtrim($result_val, ",");
		//echo "<pre>"; print_r($result); exit;
        return $result;
    } 

    public function transactionbyCompany()
    {
		$arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_PASSENGERS_LOGS,
					'localField' => '_id',
					'foreignField' => 'company_id',
					'as' => 'passengerlog'
				)
			),
			array(
				'$unwind' => '$passengerlog'
			),
			array(
				'$lookup' => array(
					'from' => MDB_TRANSACTION,
					'localField' => 'passengerlog._id',
					'foreignField' => 'passengers_log_id',
					'as' => 'trans'
				)
			),
			array(
				'$unwind' => '$trans'
			),
			array(
				'$project' => array(
					'company_id' => '$company_id',
					'fare' => '$trans.fare',
					'company_name' => '$companydetails.company_name'
				)
			),
			array(
				'$group' =>array(
					'_id' => array(
						'company_id'=>'$company_id',
						'company_name'=>'$company_name',
					),
					'fare' => array(
						'$sum' => '$fare'
					),
				)
			),
		);
		$result    = $this->mongo_db->aggregate(MDB_COMPANY, $arguments);
		$result = (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
		$result_val   = "";
		if(count( $result ) > 0){
			foreach ($result as $res) {
				$result_val .= "['" . $res['_id']['company_name'] . "', " . $res["fare"] . "" . "],";
			}	
		}
		$result = rtrim($result_val, ",");
		//echo "<pre>"; print_r($result); exit;
        return $result;
    } 
    
    public function company_accountbalance()
    {
		$arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => '_id',
					'foreignField' => 'company_id',
					'as' => 'people'
				)
			),
			array(
				'$unwind' => '$people'
			),
			array(
				'$match' => array('people.user_type' => 'C')
			),
			array(
				'$project' => array(
					'account_balance' => '$people.account_balance',
					'company_name' => '$companydetails.company_name'
				)
			),
			array(
				'$sort' => array(
					'people._id' => 1
				)
			),
		);
		$result    = $this->mongo_db->aggregate(MDB_COMPANY, $arguments);
		$queryval = (isset($result['result']))?$result['result']:array();
		if(count($queryval) > 0) {
			$result   = "";
			foreach ($queryval as $res) {
				$company_name = (isset($res["company_name"]))?$res["company_name"]:"-";
				$account_balance = (isset($res["account_balance"]))?$res["account_balance"]:"-";
				$result .= "['" . $company_name . "', " . $account_balance . "" . "],";
			}
			$result = rtrim($result, ",");
		}
		//echo "<pre>"; print_r($result); exit;
		return $result;
    }
    //live users search
    public function live_usersearch_list($count = FALSE,$keyword = "",$offset='',$limit='')
    {
        $company_id         = $this->session->get('company_id');
		$keyword    = str_replace("%", "!%", $keyword);
        $keyword    = str_replace("_", "!_", $keyword);
		$match = array();
		$match['login_status'] = 'A';
		if(!empty($company_id)){
			$match['passenger_cid'] = (int)$company_id;
		}
		if($keyword){
			$srch_array = array('$and' => array($match , array('$or' => array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")),
																			  array( 'email' => Commonfunction::MongoRegex("/$keyword/i"))))));
		}else{
			$srch_array = $match;
		}
		$args = array(array('$match' => $srch_array),
					  array('$sort' => array('created_date' => -1)));
		if($count == true){
			$result = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
			return (!empty($result['result'])) ? count($result['result']) : array();
		}
		else{
			$page = array(array('$skip' => (int)$offset),array('$limit' => (int)$limit));
			$arguments = array_merge($args,$page);
			$result = $this->mongo_db->aggregate(MDB_PASSENGERS,$arguments);
			return (!empty($result['result'])) ? $result['result'] : array();
		}
    }
    public function get_all_live_search_list($find_count = false, $keyword = "",$cid='' , $offset = "", $val = "")
    {
        $match_array = array();
		$match_array['login_status']= 'A';
		if (!empty($cid) && $cid!=0) {
			$match_array['passenger_cid']= (int)$cid;
		}
		$keyword       = str_replace("%", "!%", $keyword);
        $keyword       = str_replace("_", "!_", $keyword);
		if ($keyword) {
			$search = array("\$or"=>array(array( 'name' => Commonfunction::MongoRegex("/$keyword/i")) ,
										array( 'lastname' => Commonfunction::MongoRegex("/$keyword/i")) ,
										array( 'email' => Commonfunction::MongoRegex("/$keyword/i") )
										 ) );
			
			$match_array = array("\$and" => array($match_array, $search));
        }
		if($find_count==TRUE){
			$result = $this->mongo_db->count(MDB_PASSENGERS,$match_array,array('_id'));
			//echo $result; exit;
			return $result;
		}else{
			  ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                         $options=[
                             'projection'=>[
                                 '_id'=>1,
                                 'name'=>1,
                                 'last_login'=>1,
                                 'phone'=>1,
                                 'address'=>1                                                               
                             ],
                             'sort'=>[
                                 'last_login'=>-1
                             ],
                             'skip'=>(int)$offset,
                             'limit'=>(int)$val
                         ];
                         $results = $this->mongo_db->find(MDB_PASSENGERS,$match_array,$options);
			return (!empty($results))?$results:array();	
		}
    }
    /**
     * ****export_data()****
     *@export user listings
     */
    // To Check Currency code is equal to Currency symbol
    public static function checksite_currency($currencysymbol, $currencycode)
    {
        
		//MongoDB
		$mongodb = MangoDB::instance('default');
		$result = $mongodb->count(MDB_TAXI,array('currency_code'=>$currencycode,'currency_symbol'=>$currencysymbol),array('country_id'));
		return ($result>0)?false:true;
    }
	
	public function get_company_details()
    {
		$match_query = $data = array();
		$match_query['people.user_type'] = 'C';
		$match_query['people.status'] = 'A';
		//echo "<pre>"; print_r($match_query); exit;
		$arguments = array(
			array(
				'$lookup' => array(
					'from' => MDB_PEOPLE,
					'localField' => '_id',
					'foreignField' => 'company_id',
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
					'cid' => '$_id',
					'company_name' => '$companydetails.company_name'
				)
			),
		);
		$result    = $this->mongo_db->aggregate(MDB_COMPANY, $arguments);
		//echo "<pre>"; print_r($result); exit;
		if(count($result['result']) > 0){
			foreach($result['result'] as $val){  
				
				$arr['cid'] = $val['cid'];
				$arr['company_name'] = isset($val['companydetails']['company_name'])?$val['companydetails']['company_name']:"";
				$arr['company_brand_type'] = isset($val['companyinfo']['company_brand_type'])?$val['companyinfo']['company_brand_type']:"";
				$data[] = $arr;
			}
		}
		return $data; 
    }
    public function all_driver_map_list()
    {		
		$user_createdby = $this->user_createdby;
        $usertype       = $this->usertype;
        $company_id     = (int)$this->company_id;
        $country_id     = $this->country_id;
        $state_id       = $this->state_id;
        $city_id        = $this->city_id;
		$match_query =array();
		$match_query['user_type'] = 'D';
		$match_query['status'] = 'A';
		if (($company_id!="" && $company_id!=0) && ($usertype == 'M') || ($usertype == 'C')) {
			$match_query['company_id'] = $company_id;
        }
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
				'$match' => $match_query
			),
			array(
				'$project' => array(
					'name'=>'$name',
					'driver_status' => '$driver.status',
					'shift_status' => '$driver.shift_status',
					'location' => '$driver.loc.coordinates'
				)
			)
		);
		$result = $this->mongo_db->aggregate(MDB_PEOPLE,$arguments);
		//echo "<pre>"; print_r($result); exit;
		return (!empty($result) && isset($result['result']))?$result['result']:array();
    }
    //function to get passenger list who have referral
    public function passenger_list_referralcode()
    {
		$result = array();
		$match = array('referral_code'=>array('$ne'=>null), 'user_status'=>'A');
		/*$project = array('_id','wallet_amount');
		$res = $this->mongo_db->Find(MDB_PASSENGERS,$match,$project);*/
                   ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1, 
                        'wallet_amount'=>1
                    ]                                        
                ];
                $res = $this->mongo_db->find(MDB_PASSENGERS,$match,$options);
		
		$res = Commonfunction::change_key($res);
		if(!empty($res)){
			foreach($res as $r){
				
				$temp_arr['id'] = $r['_id'];
				$temp_arr['wallet_amount'] = isset($r['wallet_amount'])?$r['wallet_amount']:0;
				$result[] = $temp_arr;
			}
		}
        return $result;
    }
    //function to update wallet
    public function update_wallet($passengeridArr, $referral_amount)
    {
        $update_arr = array(
            "referral_code_amount" => (double)$referral_amount
        );
		//print_r($passengeridArr);exit;
		if(count($passengeridArr) >0){
			foreach($passengeridArr as $p){
				$result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$p),array('$set'=>$update_arr),array('upsert'=>true));
			}
		}		
    }
    public function siteinfo_details()
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_SITEINFO, array(
            '_id' => 1
        ), array(
            'admin_commission',
            'referral_discount',
            'currency_format',
            'referral_amount',
            'referral_settings'
        ));
        $res    = array();
        foreach ($result as $keys => $values) {
            $res[0][$keys] = $values;
        }
        return $res;
    }
    public static function check_wallet_amount_range($base_fare)
    {
        if (preg_match('/^\d+(\-\d+)*$/', $base_fare)) {
            return true;
        } else {
            return false;
        }
    }
    /** Fuction to check wallet amount1 is greater than wallet amount2 or wallet amount3 **/
    public static function compare_wallet_amount1($amount1, $amount2, $amount3)
    {
        if ($amount1 > $amount2 || $amount1 > $amount3) {
            return false;
        } else {
            return true;
        }
    }
    /** Fuction to check wallet amount1 is greater than wallet amount2 or wallet amount3 **/
    public static function compare_wallet_amount2($amount1, $amount2)
    {
        if ($amount1 > $amount2) {
            return false;
        } else {
            return true;
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
	
	//** Function to get all logged in passengers list **//	
	public function logged_in_passengers()
	{	
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[
                    'name'=>1,
                    'loc.coordinates'=>1                                                                                              
                ]                             
            ];
            $result = $this->mongo_db->find(MDB_PASSENGERS,['user_status' => 'A','login_status' => 'S'],$options);
            $res = $result;
            return (!empty($result))?Commonfunction::change_key($res):array();
	}
	public function get_driver_list($company_id = 0)
	{
		$args = array();
		if($company_id > 0) {
			
			$args[]['$match'] = array('company_id' => (int)$company_id);
		}
		$args[]['$project'] = array('_id'=>0,
                            'id' => '$_id',
                            'name' => '$name');
		$result    = $this->mongo_db->aggregate(MDB_PEOPLE, $args);
		return !empty($result['result']) ? $result['result']: array();
	}
	
	
	public function count_paymentgateway_list(){
            $result = $this->mongo_db->count(MDB_PAYMENT_GATEWAYS,array('payment_status'=>array('$ne'=>'T')),array('_id'));
            return $result;
	}
	
	//To Get all payment modules
	public function get_payment_gateway_list()
	{
		//MongoDB
		//$result = $this->mongo_db->find(MDB_PAYMENT_MODULES,array(),array('_id','pay_mod_name','pay_mod_image','pay_mod_default','pay_mod_active','pay_mod_cd'))->sort(array('_id'=>1));
                 ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                         $options=[
                             'projection'=>[
                                 '_id'=>1,
                                 'pay_mod_name'=>1,
                                 'pay_mod_image'=>1,
                                 'pay_mod_default'=>1,
                                 'pay_mod_active'=>1,
                                 'pay_mod_cd'=>1                                                                                      
                             ],
                             'sort'=>[
                                 '_id'=>1
                             ]
                         ];
		$result = $this->mongo_db->find(MDB_PAYMENT_MODULES,[],$options);
		$data = array();
        if(count($result) > 0){
			foreach($result as $val){
				$arr = $val;
				$arr['pay_mod_id'] = $val['_id'];
				$data[] = $arr;
			}
		}
		
       return $data; 
		
	}
	
	/** Update mobile image **/
	public function update_mobile_logo_image($field,$image)
	{
		$results = $this->mongo_db->findOne(MDB_THEME_SETTINGS, array('_id' => 1), array($field));
		
        if(!empty($results[$field])){
			$id1 = $results[$field];
			if(file_exists($id1)){
				unlink($id1);
			}
		}
        $query = array($field => $image);
        $result = $this->mongo_db->updateOne(MDB_THEME_SETTINGS, array(
            '_id' => 1
        ), array(
            '$set' => $query
        ), array(
            'upsert' => true
        ));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
	}
	
	public function get_single_multy_company()
	{
		$options=[
				'projection'=>[
				   '_id'=>1,                               
				   'companydetails.company_name'=>1,                               
				   'companyinfo.company_brand_type'=>1,                               
					],
				'sort'=>[
					'_id'=>-1
					]
			];
		$company_res = 	$this->mongo_db->find(MDB_COMPANY,array('companydetails.company_status'=>'A'),$options);
		/*$result = $this->mongo_db->find(MDB_COMPANY,array('companydetails.company_status'=>'A'),array('companydetails.company_name','_id','companyinfo.company_brand_type'))->sort(array('_id'=>1));
		$company_res = iterator_to_array($result);*/
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
	
   /**
               * It is used to enable_template
               * @method  enable_template
               * @return  count($result);
               */
       public function enable_template($activeids,$status)
       {
           $active_ids = Commonfunction::mongo_format_array($activeids);
           $filter=  [
                   '_id'=>[
                       '$in'=>$active_ids
                       ]
                    ];
           $query=[
               '$set'=>[
                   'status'=>$status
                   ]
                ];           
               $result = $this->mongo_db->updateMany(MDB_SMS_TEMPLATES,$filter,$query,['upsert'=>false]);
               return count($result);
       }
       
    public function getthemesettings($data_fields){
        $result = $this->mongo_db->find(MDB_THEME_SETTINGS,array('_id'=>1),$data_fields);
        return $result;
    }
       
    /** validating the admin theme settings **/
    public function validate_admin_theme_settings($arr)
    {
        $validator = Validation::factory($arr)					   
            ->rule('header_background', 'not_empty')
            ->rule('dispatch_header_background', 'not_empty')    
            ->rule('footer_background', 'not_empty')
            ->rule('sidebar_background', 'not_empty')
            ->rule('sidebar_sub_tab', 'not_empty')
            ->rule('sidebar_icon', 'not_empty')
            ->rule('sidebar_icon_circle', 'not_empty')
            ->rule('sidebar_active', 'not_empty')
            ->rule('button_background', 'not_empty')
            ->rule('button_hover_background', 'not_empty');
        return $validator;
    }
    
    /** validating the website theme settings **/
    public function validate_website_theme_settings($arr)
    {
        $validator = Validation::factory($arr)					   
            ->rule('header_background', 'not_empty')
            ->rule('footer_background', 'not_empty')
            ->rule('sidebar_background', 'not_empty')
            ->rule('sidebar_icon', 'not_empty')
            ->rule('sidebar_icon_active', 'not_empty')
            ->rule('sidebar_active', 'not_empty')
            ->rule('button_background', 'not_empty')
            ->rule('button_hover_background', 'not_empty');
        return $validator;
    }
        
    public function updatethemesettings($data_array)
    {
        $result = $this->mongo_db->updateOne(MDB_THEME_SETTINGS, array('_id' => 1), array('$set' => $data_array), array("upsert" => true));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
       
	public function validate_templatesinfo($arr="",$files_value_array="")
	{
		$validator = Validation::factory($arr)     
				->rule('site_logo','Upload::size', array($arr['site_logo'],IMG_MAX_SIZE))
				->rule('email_site_logo','Upload::size', array($arr['email_site_logo'],IMG_MAX_SIZE))
				->rule('banner_image','Upload::size', array($arr['banner_image'],IMG_MAX_SIZE))
				->rule('banner_content','not_empty')
				->rule('app_content','not_empty')
				->rule('site_copyrights','not_empty')
				->rule('app_bg_color','not_empty')
				->rule('about_bg_color','not_empty')				
				->rule('footer_bg_color','not_empty')
				->rule('mobile_header_logo','Upload::size', array($arr['mobile_header_logo'],IMG_MAX_SIZE))
				->rule('flash_screen_logo','Upload::size', array($arr['flash_screen_logo'],IMG_MAX_SIZE))
				->rule('banner_content','not_empty')		
				->rule('about_us_content','not_empty')
				->rule('site_favicon','Upload::size', array($arr['site_favicon'],IMG_MAX_SIZE))
				->rule('contact_us_content','not_empty');
		return $validator;
	}
	
	public function update_templatesettings($post_value_array)
    {
        $query  = array(
            'site_copyrights' => $post_value_array['site_copyrights'],
			'banner_content' =>  $post_value_array['banner_content'],
			'app_content' =>  $post_value_array['app_content'],
			'app_bg_color' =>  $post_value_array['app_bg_color'],
			'about_us_content' =>  $post_value_array['about_us_content'],
			'about_bg_color' =>  $post_value_array['about_bg_color'],
			'footer_bg_color' =>  $post_value_array['footer_bg_color'],
			'contact_us_content' =>  $post_value_array['contact_us_content']
        );      
        $query['footer_bg_color_1'] =  isset($post_value_array['footer_bg_color_1']) ? $post_value_array['footer_bg_color_1'] : '';
        $query['app_bg_color_1'] =  isset($post_value_array['app_bg_color_1']) ? $post_value_array['app_bg_color_1'] : '';
        $query['theme_id'] =  isset($post_value_array['theme_id'])?$post_value_array['theme_id']:1;
        $result = $this->mongo_db->updateOne(MDB_THEME_SETTINGS, array(
            '_id' => 1
        ), array(
            '$set' => $query
        ), array(
            "upsert" => false
        ));      
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
}
