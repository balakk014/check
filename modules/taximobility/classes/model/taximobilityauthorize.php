<?php defined('SYSPATH') or die('No direct script access.');
/****************************************************************
* Contains Authorization model for admin 
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Model_TaximobilityAuthorize extends Model
{
    public function __construct()
    {
        $this->session  = Session::instance();
        $this->userid   = $this->session->get("userid");
        $this->mdate    = commonfunction::getCurrentTimeStamp();
        //MongoDB Instance
        $this->mongo_db = MangoDB::instance('default');
    }
    public function login_validate($arr)
    {
        return Validation::factory($arr)
        ->rule('email', 'not_empty', array(  ':value', 'Email' ))
        ->rule('email', 'email', array(':value','Email' ))
        ->rule('password', 'not_empty', array(':value','Password'));
    }
    public function adminlogin_details($email, $password, $need_count = FALSE, $status = ACTIVE)
    {
        $condition = array('email'=>$email,'password'=>$password,'status'=>ACTIVE,"\$or"=>array(array('user_type'=>'A'),array('user_type'=>'S')));
        if($need_count){
            $result = $this->mongo_db->count(MDB_PEOPLE,$condition);
            return $result;
        } else {
            $res = $this->mongo_db->findOne(MDB_PEOPLE,$condition,array('_id','user_type','name','username','email','company_id','login_city','login_state','login_country'));            
            if(!empty($res))
            {
				$temp_arr['id'] = isset($res['_id'])?$res['_id']:'';
				
				$loginIp = $_SERVER['REMOTE_ADDR'];
				$lastLogin = date("Y-m-d H:i:s");
				$lastLogin = Commonfunction::MongoDate(strtotime($lastLogin));
				$update_arr = array("last_login"=>$lastLogin,"login_ip"=>$loginIp);
				$update = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$temp_arr['id']),array('$set'=>$update_arr),array('upsert'=>false));
				
				$temp_arr['user_type'] = isset($res['user_type'])?$res['user_type']:'';
				$temp_arr['name'] = isset($res['name'])?$res['name']:'';
				$temp_arr['username'] = isset($res['username'])?$res['username']:'';
				$temp_arr['email'] = isset($res['email'])?$res['email']:'';
				$temp_arr['company_id'] = isset($res['company_id'])?$res['company_id']:'';
				$temp_arr['login_city'] = isset($res['login_city'])?$res['login_city']:'';
				$temp_arr['login_state'] = isset($res['login_state'])?$res['login_city']:'';
				$temp_arr['login_country'] = isset($res['login_country'])?$res['login_country']:'';
				$result[] = $temp_arr;
			}
			else
			{
				$result=array();
			}            
            return $result;
        }
    }
    public function set_session_data($id,$pass) 
	{
		$project = array('phone','country_code');
		$match = array('_id' => (int)$id);
		$result = $this->mongo_db->findOne(MDB_PASSENGERS,$match,$project);
		if(count($result) > 0) {
			$this->session->set("remember_me",1);
			$this->session->set("passenger_phone",$result["phone"]);
			$this->session->set("passenger_phone_code",$result["country_code"]);
			$this->session->set("passenger_password",$pass);
		}
		return true;
	}
    public function companylogin_details($email, $password, $need_count = FALSE, $status = ACTIVE)
    {
		$result = array();
		$match_query = array('email'=>$email,
							 'password'=>$password,
							 'status'=>ACTIVE,
							 'user_type'=>'C',
							
							);
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
				'$match' => $match_query
			)
		);
		//echo $need_count."<pre>else";print_r($common_arguments);exit;
        if($need_count){
            $count_arguments = array(
                array(
					'$sort' => array( 
						'package_report._id' => -1
					),
				),
               array('$limit'	=> 1 ),
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
			//echo "<pre>if";print_r($result);exit;
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
        } else {
            $count_arguments = array(
                array(
					'$sort' => array( 
						'package_report._id' => -1
					),
				),
                array('$limit'	=> 1 ),
                array(
                    '$project' => array('_id'=>0,
                        'id' => '$_id',
                        'user_type' => '$user_type',
                        'name' => '$name',
                        'username' => '$username',
                        'email' => '$email',
                        'company_id' => '$company._id',
                        'login_city' => '$login_city',
                        'login_state' => '$login_state',
                        'login_country' => '$login_country',
						'time_zone'=>'$company.companydetails.time_zone',
                        'user_time_zone'=>'$company.companydetails.user_time_zone',
                        'company_status' => '$company.companydetails.company_status',
                        'upgrade_packageid' => '$package_report.upgrade_packageid',
                        'upgrade_expirydate' => '$package_report.upgrade_expirydate',
                    )
                )
            );
			$merge_arguments = array_merge($common_arguments, $count_arguments);
			$res          = $this->mongo_db->aggregate(MDB_PEOPLE, $merge_arguments);
			//echo "<pre>";print_r($res);exit;
			if(!empty($res['result'])){
				
				$r = $res['result'][0];
				$temp_arr['id'] = $r['id'];
				$temp_arr['user_type'] = $r['user_type'];
				$temp_arr['name'] = $r['name'];
				$temp_arr['username'] = isset($r['username'])?$r['username']:'';
				$temp_arr['email'] = $r['email'];
				$temp_arr['company_id'] = $r['company_id'];
				$temp_arr['login_city'] = $r['login_city'];
				$temp_arr['login_state'] = $r['login_state'];
				$temp_arr['login_country'] = $r['login_country'];
				$temp_arr['company_status'] = $r['company_status'];
				$temp_arr['time_zone'] = $r['time_zone'];
				$temp_arr['user_time_zone'] = $r['user_time_zone'];
				$temp_arr['upgrade_packageid'] = isset($r['upgrade_packageid'][0])?$r['upgrade_packageid'][0]:"";
				$temp_arr['upgrade_expirydate'] = (isset($r['upgrade_expirydate'][0]) && !empty($r['upgrade_expirydate'][0]))?commonfunction::convertphpdate('Y-m-d H:i:s',$r['upgrade_expirydate'][0]):'';	
				$result[] = $temp_arr;			
			}
            //echo "<pre>else";print_r($common_arguments);exit;
            return $result;
        }
    }
    public function managerlogin_details($email, $password, $need_count = FALSE, $status = ACTIVE)
    {
        $match_query = array('email'=>$email,'password'=>$password,'status'=>ACTIVE,'user_type'=>'M');
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
			)
		);
        if($need_count == TRUE){
            $count_arguments = array(
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
			//echo "<pre>if";print_r($result);exit;
			return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
        } else {
            $count_arguments = array(
                array(
                    '$project' => array('_id'=>0,
                        'id' => '$_id',
                        'user_type' => '$user_type',
                        'name' => '$name',
                        'username' => '$username',
                        'email' => '$email',
                        'company_id' => '$company_id',
                        'login_city' => '$login_city',
                        'login_state' => '$login_state',
                        'login_country' => '$login_country',
						'time_zone'=>'$company.companydetails.time_zone',
                        'user_time_zone'=>'$company.companydetails.user_time_zone',
                        'company_status' => '$company.companydetails.company_status',
                    )
                )
            );
			$merge_arguments = array_merge($common_arguments, $count_arguments);
			$res          = $this->mongo_db->aggregate(MDB_PEOPLE, $merge_arguments);
			$result = array();
			//echo "<pre>else";print_r($res['result']);exit;            
            if(!empty($res['result'])){
				foreach($res['result'] as $key => $val){	
					$arr = $val;
					$arr['username'] = isset($val['$username'])?$val['$username']:"";
					$result[] = $arr;
				}
			}
			return $result;
            //echo "<pre>else";print_r($result);exit;
        }
    }
   
    public static function forgotpassword_emailcheck($email)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PEOPLE,array('email'=>$email,'user_type'=>ADMIN));
        return ($result>0)?TRUE:FALSE;
    }
    public static function forgotpassword_emailcompanycheck($email)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PEOPLE,array('email'=>$email,'user_type'=>'C'));
        return ($result>0)?TRUE:FALSE;
    }
    public static function forgotpassword_emailmanagercheck($email)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PEOPLE,array('email'=>$email,'user_type'=>'M'));
        return ($result>0)?TRUE:FALSE;
    }
    public function forgotpassword_validate($arr)
    {
        $validate = Validation::factory($arr)->rule('email', 'not_empty', array(
            ':value',
            'Email'
        ))->rule('email', 'email_domain', array(
            ':value',
            'Email'
        ))->rule('email', 'Model_Authorize::forgotpassword_emailcheck', array(
            ':value',
            'Email'
        ));
        return $validate;
    }
    public function forgotpassword_companyvalidate($arr)
    {
        $validate = Validation::factory($arr)->rule('email', 'not_empty', array(
            ':value',
            'Email'
        ))->rule('email', 'email_domain', array(
            ':value',
            'Email'
        ))->rule('email', 'Model_Authorize::forgotpassword_emailcompanycheck', array(
            ':value',
            'Email'
        ));
        return $validate;
    }
    public function forgotpassword_managervalidate($arr)
    {
        $validate = Validation::factory($arr)->rule('email', 'not_empty', array(
            ':value',
            'Email'
        ))->rule('email', 'email_domain', array(
            ':value',
            'Email'
        ))->rule('email', 'Model_Authorize::forgotpassword_emailmanagercheck', array(
            ':value',
            'Email'
        ));
        return $validate;
    }
   
    public function editprofile_validate($arr, $uid)
    {
        $validation = Validation::factory($arr)->rule('firstname', 'not_empty')
            //->rule('firstname', 'alpha_dash')
            ->rule('firstname', 'min_length', array(
            ':value',
            '4'
        ))->rule('firstname', 'max_length', array(
            ':value',
            '30'
        ))->rule('lastname', 'not_empty')
        //->rule('lastname', 'alpha_dash')            
        //->rule('lastname', 'min_length', array(':value', '4'))            
        //->rule('lastname', 'max_length', array(':value', '30'))
            ->rule('email', 'not_empty')
            ->rule('email', 'email')
            ->rule('email', 'max_length', array(
            ':value',
            '100'
        ))->rule('email', 'Model_Edit::checkemail', array(
            ':value',
            $uid
        ))->rule('phone', 'not_empty')
        //->rule('phone', 'numeric')
            ->rule('phone', 'min_length', array(
            ':value',
            '7'
        ))->rule('phone', 'max_length', array(
            ':value',
            '20'
        ))
        //->rule('phone', 'phone', array(':value'))
            ->rule('phone', 'contact_phone', array(
            ':value'
        ))->rule('phone', 'Model_Edit::checkphone', array(
            ':value',
            $uid
        ))->rule('address', 'not_empty')
        ->rule('country', 'not_empty')
        ->rule('state', 'not_empty')
        ->rule('city', 'not_empty');
        if(in_array('image',$arr)){
			$validation = $validation->rule('image','Upload::size', array($arr['image'], IMG_MAX_SIZE));
		}
		return $validation;
    }
    /** for passengers list **/
    public function editpassenger_validate($arr, $uid)
    {
        return Validation::factory($arr)
        ->rule('name', 'not_empty')
        //->rule('name', 'alpha_dash')
        ->rule('name', 'min_length', array(
            ':value',
            '4'
        ))->rule('name', 'max_length', array(
            ':value',
            '30'
        ))
        ->rule('email', 'not_empty')
        ->rule('email', 'email')
        ->rule('email', 'max_length', array(
            ':value',
            '100'
        ))->rule('email', 'Model_Edit::check_passengeremail', array(
            ':value',
            $uid
        ))->rule('phone', 'not_empty')
        ->rule('phone', 'Model_Edit::check_passengerphone', array(
            ':value',
            $uid
        ))
        //->rule('phone', 'numeric')            
        //->rule('phone','Model_Add::check_valid_phone_number',array(':value','/^[0-9()-+]*$/u'))
        ->rule('phone', 'min_length', array(
            ':value',
            '7'
        ))->rule('phone', 'max_length', array(
            ':value',
            '20'
        ))->rule('phone', 'phone', array(
            ':value'
        ));
        //->rule('address', 'not_empty');
    }
    public function changepassword_validate($arr, $id)
    {
        return Validation::factory($arr)
        ->rule('oldpassword', 'not_empty')
        ->rule('oldpassword', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('oldpassword', 'max_length', array(
            ':value',
            '16'
        ))->rule('oldpassword', 'Model_Authorize::check_pass', array(
            ':value',
            $id
        ))->rule('password', 'not_empty')->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('password', 'max_length', array(
            ':value',
            '16'
        ))->rule('repassword', 'not_empty')->rule('repassword', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('repassword', 'matches', array(
            ':validation',
            'password',
            'repassword'
        ))->rule('repassword', 'max_length', array(
            ':value',
            '16'
        ));
    }
    public function changepassword($password, $userid)
    {
        $org_password = $password;
        $password     = md5($password);
        $set_array = array(
            "password" => $password,
            //"org_password" => $org_password
        );
        //MongoDB
        $result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$userid),array('$set'=>$set_array),array('upsert'=>false));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    public function select_users_byemail($email)
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_PEOPLE,array('email'=>$email),array('_id','name','email'));
		$data = array();
		if(count($result) > 0){
			$arr['id'] = $result['_id'];
			$arr['name'] = $result['name'];
			$arr['email'] = $result['email'];
			$data[]=$arr;
		}
        return $data;
    }
    public function edit_people($uid, $post, $files)
    {
        $image_name = "";
		if(isset($files['image']['name']) && $files['image']['name']!=''){
			
			$image_name = $uid; 
			$filename   = Upload::save($files['image'], $image_name, DOCROOT . COMPANY_IMG_IMGPATH);
            $logo_image = Image::factory($filename);
            $path1      = DOCROOT . COMPANY_IMG_IMGPATH;
            $path       = $image_name;
            Commonfunction::multipleimageresize($logo_image, TAXI_IMG_WIDTH, TAXI_IMG_HEIGHT, $path1, $image_name, 90);
		}
        
        $data_set = array(
            'name' => $post['firstname'],
            'address' => $post['address'],
            'login_country' => (int)$post['country'],
            'login_state' => (int)$post['state'],
            'login_city' => (int)$post['city'],
            'lastname' => $post['lastname'],
            'email' => $post['email'],
            'phone' => $post['phone'],
            'profile_picture' => $image_name
        );
        $result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$uid),array('$set'=>$data_set),array('upsert'=>true));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    /** edit passengers details**/
    public function edit_passenger($uid, $post)
    {
        $country_code = '';
        $phone        = $post['phone'];
        if (strpos($post['phone'], '-') !== false) {
            $phoneArr     = explode('-', $post['phone']);
            $country_code = $phoneArr[0];
            $phone        = $phoneArr[1];
        }
        //MongoDB
        $data = array(
            'name' => $post['name'],
            'address' => $post['address'],
            'email' => $post['email'],
            'country_code' => $country_code,
            'phone' => $phone,
            'discount' => (int)$post['discount'],
            'passenger_cid'=>(int)$post['company_id']
        );
        $result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$uid),array('$set'=>$data),array('upsert'=>true));
        //print_r($result);exit;
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }   
    public function login_details_byid($userid)
    {
		$result = array();
        $res = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=>(int)$userid),array('_id','name','lastname','email','address','country_code','login_country','login_state','login_city','status','phone','profile_picture'));
		if(!empty($res)){
			$result[] = $res;
		}
        return $result;
    }
    /** get passenger id */
    public function login_details_by_passengerid($userid)
    {
		$result = array();
        $res = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$userid),
											array('_id','name','lastname','email','address','country_code','phone','discount','passenger_cid'));
        if(!empty($res)){
            $res['address'] = isset($res['address']) ? $res['address']: '';
            $res['discount'] = isset($res['discount']) ? $res['discount']: 0;
            $res['passenger_cid'] = isset($res['passenger_cid']) ? $res['passenger_cid']: 0;
            $result[] = $res;
        }
        return $result;
    }
    public function select_user_details_by_id($userid)
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=>(int)$userid,'status'=>ACTIVE),array('_id','user_type','email','name'));
        return (!empty($result)) ? $result : array();
    }
    public function select_alluser_details($id)
    {
        //MongoDB
        $result = $this->mongo_db->find(MDB_PEOPLE,array('_id'=>array('$ne'=>(int)$id)));
        return (!empty($result)) ? $result : array();
    }
    /**
     * Check inline textbox label not empty for javscript on focus and on blur
     **/
    public static function check_label_not_empty($fieldname, $value)
    {
        return ($fieldname == $value) ? FALSE : TRUE;
    }
    public function user_list()
    {
		// MongoDB
		//$result = $this->mongo_db->find(MDB_PEOPLE,array('user_type'=>array('$ne'=>'N'),'user_type'=>array('$ne'=>'A'),'status'=>array('$ne'=>'T')))->sort(array('created_date'=>-1));
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                         $options=[
                             'sort'=>[
                                 'created_date'=>-1
                             ]
                         ];
                         $result = $this->mongo_db->find(MDB_PEOPLE,array('user_type'=>array('$ne'=>'N'),'user_type'=>array('$ne'=>'A'),'status'=>array('$ne'=>'T')),$options);
		return (!empty($result))?$result:array();
    }
   
    public function count_user_list()
    {
		$count = $this->all_user_list('','',true);
		return $count;		
    }
    public function all_user_list($offset = '', $val = '',$find_count = false)
    {
		$result = array();
		$match = array('user_type'=>array('$ne'=>'A'),'status'=>array('$ne'=>'T'));
		if($find_count){
			$result = $this->mongo_db->count(MDB_PEOPLE,$match);
			return $result;
		} else {
			//$res = $this->mongo_db->find(MDB_PEOPLE, $match,array('_id','name','lastname','email','phone','address','created_date','user_type','status'))->sort(array('created_date'=>-1))->skip($offset)->limit($val);
                        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
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
                                 '_id'=>-1
                             ],
                             'skip'=>(int)$offset,
                             'limit'=>(int)$val
                         ];
                         $res = $this->mongo_db->find(MDB_PEOPLE, $match,$options);
						
			if(!empty($res)){
				$i=0;
				foreach($res as $r){
					$result[$i] = $r;
					$result[$i]['id'] = $r['_id'];
					$result[$i]['name'] = isset($r['name'])?$r['name']:"";
					$result[$i]['lastname'] = isset($r['lastname'])?$r['lastname']:"";
					$result[$i]['email'] = isset($r['email'])?$r['email']:"";
					$result[$i]['phone'] = isset($r['phone'])?$r['phone']:"";
					$result[$i]['address'] = isset($r['address'])?$r['address']:"";
					$result[$i]['user_type'] = isset($r['user_type'])?$r['user_type']:"";
					$result[$i]['status'] = isset($r['status'])?$r['status']:"";
					$result[$i]['created_date'] = commonfunction::convertphpdate('Y-m-d H:i:s', $r['created_date']);
					$i++;
				}
			}
			//echo '<pre>';print_r($result);exit;
			return $result;
		}
    }
    public function validate_user_form($arr)
    {
        $arr['name']  = trim($arr['name']);
        $arr['email'] = trim($arr['email']);
        return Validation::factory($arr)->rule('name', 'not_empty')->rule('name', 'min_length', array(
            ':value',
            '5'
        ))->rule('name', 'min_length', array(
            ':value',
            '32'
        ))->rule('password', 'not_empty')->rule('password', 'min_length', array(
            ':value',
            '5'
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
    // Check Whether the Eneterd Password is Correct While User Change Password
    public static function check_pass($pass = "", $userid = "")
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $pass     = md5($pass);
        $condition = array('_id'=>(int)$userid);
        $result = $mongodb->findOne(MDB_PEOPLE,$condition,array('password'));
        $password = $result["password"];
        return ($password == $pass)?true:false;
    }
    public function count_passenger_list_history()
    {
        $count = $this->all_passenger_list_history('','',true);
        //echo '<pre>';print_r($count);exit;
        return $count;
    }
    /** passenger list **/
    public function all_passenger_list_history($offset, $val, $find_count = FALSE)
    {
		$result = array();
		$arguments = array(
				//array('$match' => array('user_status' => array('$ne' => 'T'))),
                array(
                    '$project' => array(
                        'id' => '$_id',
                        'phone' => '$phone',
                        'name' => '$name',
                        'email' => '$email',
                        'address' => '$address',
                        'created_date' => '$created_date',
                        'wallet_amount' => '$wallet_amount',
                        'referral_code' => '$referral_code',
                        'user_status' => '$user_status'
                    )
                ),
                array(
                    '$sort' => array(
                        'created_date' => -1
                    )
                )
            );
		if($find_count != true){
            $arguments[]['$skip'] = (int) $offset;
            $arguments[]['$limit'] = (int) $val;  
		}          
		
		$res    = $this->mongo_db->aggregate(MDB_PASSENGERS, $arguments);	
		if(!empty($res['result'])){
			$i=0;
			foreach($res['result'] as $r){
				$result[$i]['id'] = $r['id'];
				$result[$i]['name'] = $r['name'];
				$result[$i]['email'] = $r['email'];
				$result[$i]['phone'] = $r['phone'];
				$result[$i]['address'] = isset($r['address']) ? $r['address'] : '--';
				$result[$i]['referral_code'] = isset($r['referral_code']) ? $r['referral_code'] : ''; 
				$result[$i]['user_status'] = isset($r['user_status']) ? $r['user_status'] : '';
				$result[$i]['wallet_amount'] = isset($r['wallet_amount']) ? $r['wallet_amount'] : '';
				$result[$i]['created_date'] = isset($r['created_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s', $r['created_date']):'';
				$i++;
			}			
		}		
		//echo '<pre>';print_r($arguments);exit;
		return $result;	
    }
}
