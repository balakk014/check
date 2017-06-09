<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Edit Model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityEdit extends Model
{
    public function __construct()
    {
        $this->session         = Session::instance();
        $this->user_admin_type = $this->session->get("user_type");
        $this->company_id     = $this->session->get('company_id');
        $this->currentdate     = Commonfunction::getCurrentTimeStamp();
        # created date
		$this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
        //MongoDB Instance
        $this->mongo_db        = MangoDB::instance('default');
    }
    /**Validating for Add company**/
    public function validate_editcompany($arr,$uid)
    {
		//print_r($arr);exit;
        $validation = Validation::factory($arr)
            ->rule('email', 'not_empty')
            ->rule('email', 'email')
            ->rule('email', 'max_length', array(':value', '50'))
            ->rule('email', 'Model_Edit::checkemail', array(':value',$uid))

                ->rule('firstname', 'not_empty')
                //->rule('firstname', 'alpha_dash')
            ->rule('firstname', 'min_length', array(':value', '4'))
            ->rule('firstname', 'max_length', array(':value', '30'))
            ->rule('lastname', 'not_empty')
                    //->rule('lastname', 'alpha_dash')
            //->rule('lastname', 'min_length', array(':value', '4'))
            //->rule('lastname', 'max_length', array(':value', '30'))

            ->rule('phone', 'not_empty')
            //->rule('phone', 'numeric')
            ->rule('phone', 'min_length', array(':value', '7'))
            ->rule('phone', 'max_length', array(':value', '20'))
            //->rule('phone', 'phone', array(':value'))
            ->rule('phone', 'contact_phone', array(':value'))
            ->rule('phone', 'Model_Edit::checkphone', array(':value',$uid))

            ->rule('company_name', 'not_empty')
            ->rule('company_name', 'min_length', array(':value', '4'))
            ->rule('company_name', 'max_length', array(':value', '30'))
            //->rule('company_name', 'Model_Edit::checkcompany', array(':value',$arr['country'],$arr['state'],$arr['city'],$uid))

            //->rule('payment_gateway_username','not_empty')
            //->rule('payment_gateway_password','not_empty')
            //->rule('payment_gateway_signature','not_empty')
            //->rule('payment_method','not_empty')

            ->rule('address', 'not_empty')
            ->rule('country', 'not_empty')
            ->rule('state', 'not_empty')
            ->rule('company_image','Upload::size', array($arr['company_image'], IMG_MAX_SIZE))
            ->rule('city', 'not_empty');
			//->rule('time_zone', 'not_empty');
            //->rule('company_address', 'not_empty')
            /*->rule('currency_code', 'not_empty')
            ->rule('currency_symbol', 'not_empty')
            ->rule('currency_symbol', 'Model_Edit::checksite_currency', array(':value',$arr['currency_code'])) */
            
        /*  if($this->user_admin_type=='A' || $this->user_admin_type=='DA')
            {
                        $validation->rule('paymodstatus', 'not_empty');

            }*/

        return $validation;
    }
    /**Validating for Add Motor**/
    public function validate_editmotor($arr, $uid)
    {
        return Validation::factory($arr)->rule('companyname', 'not_empty')
        //->rule('companyname', 'alpha_dash')
            ->rule('companyname', 'min_length', array(
            ':value',
            '2'
        ))->rule('companyname', 'max_length', array(
            ':value',
            '30'
        ))->rule('companyname', 'Model_Edit::checkmotor', array(
            ':value',
            $uid
        ));
    }
    /**Validating for Add company**/
    public function validate_editdriver($arr, $uid)
    {
		//print_r($arr);exit;
        return Validation::factory($arr)->rule('firstname', 'not_empty')
        //->rule('username', 'alpha_dash')
            ->rule('firstname', 'min_length', array(
            ':value',
            '4'
        ))->rule('firstname', 'max_length', array(
            ':value',
            '30'
        ))->rule('lastname', 'not_empty')
        //->rule('username', 'alpha_dash')
        //->rule('lastname', 'min_length', array(':value', '4'))
        //->rule('lastname', 'max_length', array(':value', '30'))
            ->rule('dob', 'not_empty')->rule('phone', 'not_empty')
        //->rule('phone','Model_Add::check_valid_phone_number',array(':value','/^[0-9()-+]*$/u'))
        //->rule('phone', 'alpha_numeric')
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
        ))->rule('phone', 'regex', array(
            ':value',
            '/^[0-9]++$/'
        ))->rule('phone', 'Model_Edit::checkphone', array(
            ':value',
            $uid
        ))->rule('email', 'not_empty')->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('email', 'Model_Edit::checkemail', array(
            ':value',
            $uid
        ))
        /*->rule('password', 'not_empty')->rule('password', 'min_length', array(
            ':value',
            '6'
        ))->rule('password', 'max_length', array(
            ':value',
            '20'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('repassword', 'not_empty')->rule('repassword', 'min_length', array(
            ':value',
            '6'
        ))->rule('repassword', 'max_length', array(
            ':value',
            '20'
        ))->rule('repassword', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('repassword', 'matches', array(
            ':validation',
            'password',
            'repassword'
        )) */
        ->rule('driver_license_id', 'not_empty')->rule('driver_license_id', 'max_length', array(
            ':value',
            '30'
        ))->rule('driver_license_id', 'Model_Edit::checklicenceId', array(
            ':value',
            $uid
        ))->rule('driver_license_expire_date', 'not_empty')->rule('driver_pco_license_number', 'not_empty')->rule('driver_pco_license_number', 'max_length', array(
            ':value',
            '30'
        ))->rule('driver_pco_license_number', 'Model_Edit::checkpcolicenceNo', array(
            ':value',
            $uid
        ))->rule('driver_pco_license_expire_date', 'not_empty')->rule('driver_insurance_number', 'not_empty')->rule('driver_insurance_number', 'max_length', array(
            ':value',
            '30'
        ))->rule('driver_insurance_number', 'Model_Edit::checkinsuranceNo', array(
            ':value',
            $uid
        ))->rule('driver_insurance_expire_date', 'not_empty')->rule('driver_national_insurance_number', 'not_empty')->rule('driver_national_insurance_number', 'max_length', array(
            ':value',
            '30'
        ))->rule('driver_national_insurance_number', 'Model_Edit::checkNationalinsuranceNo', array(
            ':value',
            $uid
        ))->rule('driver_national_insurance_expire_date', 'not_empty')->rule('address', 'not_empty')->rule('country', 'not_empty')->rule('state', 'not_empty')->rule('company_name', 'not_empty')->rule('booking_limit', 'not_empty')->rule('booking_limit', 'numeric')->rule('booking_limit', 'Model_Add::check_booking_limit', array(
            ':value',
            $arr['booking_limit']
        ))->rule('city', 'not_empty')
        ->rule('profile_picture', 'Upload::size', array($arr['profile_picture'],IMG_MAX_SIZE));
    }
    public function validate_editmodel($arr, $uid)
    {
        $validation = Validation::factory($arr)->rule('model_name', 'not_empty')
            ->rule('model_name', 'min_length', array(
            ':value',
            '2'
        ))->rule('model_name', 'max_length', array(
            ':value',
            '30'
        ))->rule('model_name', 'Model_Edit::checkmodelname', array(
            ':value',
            $arr['companyname'],
            $uid
        ))->rule('model_size', 'not_empty')->rule('model_size', 'Model_Edit::check_fare_zero', array(
            ':value',
            $arr['model_size']
        ))->rule('companyname', 'not_empty')->rule('waiting_time', 'not_empty')->rule('waiting_time', 'Model_Edit::check_waiting_time', array(
            ':value',
            $arr['waiting_time']
        ))->rule('base_fare', 'not_empty')->rule('base_fare', 'Model_Edit::check_base_fare', array(
            ':value',
            $arr['base_fare']
        ))->rule('min_km', 'not_empty')->rule('min_km', 'Model_Add::check_min_km', array(
            ':value',
            $arr['min_km']
        ))->rule('min_fare', 'not_empty')->rule('min_fare', 'Model_Edit::check_min_fare', array(
            ':value',
            $arr['min_fare']
        ))->rule('cancellation_fare', 'not_empty')->rule('cancellation_fare', 'Model_Edit::check_cancellation_fare', array(
            ':value',
            $arr['cancellation_fare']
        ))->rule('below_and_above_km', 'not_empty')->rule('below_and_above_km', 'Model_Add::check_below_and_above_km', array(
            ':value',
            $arr['min_km']
        ))->rule('below_km', 'not_empty')->rule('below_km', 'Model_Edit::check_below_km', array(
            ':value',
            $arr['below_km']
        ))->rule('above_km', 'not_empty')->rule('above_km', 'Model_Edit::check_above_km', array(
            ':value',
            $arr['above_km']
        ))->rule('night_charge', 'not_empty')->rule('minutes_fare', 'not_empty')->rule('minutes_fare', 'Model_Edit::check_minute_fare', array(
            ':value',
            $arr['minutes_fare']
        ));
        
		$validation->rule('description', 'not_empty');
		
        if (Arr::get($arr, 'night_charge') == 1) {
            //echo "dsf";exit;
            $validation->rule('night_timing_from', 'not_empty')->rule('night_timing_to', 'not_empty')->rule('night_fare', 'not_empty')->rule('night_fare', 'Model_Edit::check_night_fare', array(
                ':value',
                $arr['night_fare']
            ));
        }
        return $validation;
    }
    public function validate_editfare($arr)
    {
        $validation = Validation::factory($arr)->rule('base_fare', 'not_empty')->rule('base_fare', 'Model_Edit::check_base_fare', array(
            ':value',
            $arr['base_fare']
        ))->rule('base_fare', 'Model_Edit::check_fare_zero', array(
            ':value',
            $arr['base_fare']
        ))->rule('model_name', 'not_empty')->rule('model_size', 'not_empty')->rule('model_size', 'Model_Edit::check_fare_zero', array(
            ':value',
            $arr['model_size']
        ))->rule('min_km', 'not_empty')->rule('min_km', 'Model_Add::check_min_km', array(
            ':value',
            $arr['min_km']
        ))->rule('min_fare', 'not_empty')->rule('min_fare', 'Model_Edit::check_min_fare', array(
            ':value',
            $arr['min_fare']
        ))->rule('cancellation_fare', 'not_empty')->rule('cancellation_fare', 'Model_Edit::check_cancellation_fare', array(
            ':value',
            $arr['cancellation_fare']
        ))->rule('below_and_above_km', 'not_empty')->rule('below_and_above_km', 'Model_Add::check_below_and_above_km', array(
            ':value',
            $arr['min_km']
        ))->rule('below_km', 'not_empty')->rule('below_km', 'Model_Edit::check_below_km', array(
            ':value',
            $arr['below_km']
        ))->rule('below_km', 'Model_Edit::check_fare_zero', array(
            ':value',
            $arr['below_km']
        ))->rule('above_km', 'not_empty')->rule('above_km', 'Model_Edit::check_above_km', array(
            ':value',
            $arr['above_km']
        ))->rule('above_km', 'Model_Edit::check_fare_zero', array(
            ':value',
            $arr['above_km']
        ))->rule('minutes_fare', 'not_empty')->rule('minutes_fare', 'Model_Edit::check_minute_fare', array(
            ':value',
            $arr['minutes_fare']
        ))->rule('night_charge', 'not_empty')->rule('evening_charge', 'not_empty');
        if (Arr::get($arr, 'night_charge') == 1) {
            //echo "dsf";exit;
            $validation->rule('night_timing_from', 'not_empty')->rule('night_timing_to', 'not_empty')->rule('night_fare', 'not_empty')->rule('night_fare', 'Model_Edit::check_night_fare', array(
                ':value',
                $arr['night_fare']
            ))->rule('night_fare', 'Model_Admin::check_percentage', array(
                ':value'
            ));
        }
        if (Arr::get($arr, 'evening_charge') == 1) {
            //echo "dsf";exit;
            $validation->rule('evening_timing_from', 'not_empty')->rule('evening_timing_to', 'not_empty')->rule('evening_fare', 'not_empty')->rule('evening_fare', 'Model_Add::check_evening_fare', array(
                ':value',
                $arr['evening_fare']
            ))->rule('evening_fare', 'Model_Admin::check_percentage', array(
                ':value'
            ));
        }
        return $validation;
    }

    /**Validating for Add Taxi**/
    public function validate_edittaxi($arr,$form_values,$uid)
    {

        $rule = Validation::factory($arr)
        ->rule('taxi_no', 'not_empty')
        ->rule('taxi_no', 'min_length', array(':value', '4'))
        ->rule('taxi_no', 'max_length', array(':value', '30'))
        //->rule('taxi_no', 'alpha_numeric', array(':value','/^[0-9]{1,}/'))
        ->rule('taxi_no', 'regex', array(':value','/^[a-z0-9A-Z -]++$/iD'))
        ->rule('taxi_no', 'Model_Edit::check_taxino', array(':value',$uid))

        //->rule('taxi_type', 'not_empty')
        ->rule('taxi_model', 'not_empty')
        ->rule('taxi_min_speed', 'not_empty')
        ->rule('taxi_owner_name', 'min_length', array(':value', '4'))
        ->rule('taxi_owner_name', 'max_length', array(':value', '30'))
        ->rule('taxi_owner_name', 'not_empty')
        ->rule('taxi_manufacturer', 'min_length', array(':value', '4'))
        ->rule('taxi_manufacturer', 'max_length', array(':value', '30'))
        ->rule('taxi_manufacturer', 'not_empty')
        ->rule('taxi_colour', 'min_length', array(':value', '3'))
        ->rule('taxi_colour', 'max_length', array(':value', '10'))
        ->rule('taxi_colour', 'not_empty')
        ->rule('taxi_motor_expire_date', 'not_empty')
        ->rule('taxi_insurance_number', 'not_empty')
        ->rule('taxi_insurance_number', 'Model_Edit::check_taxinsurance_number', array(':value',$uid))
        ->rule('taxi_insurance_expire_date', 'not_empty')
        ->rule('taxi_pco_licence_number', 'not_empty')
        ->rule('taxi_pco_licence_number', 'Model_Edit::check_taxipco_number', array(':value',$uid))
        ->rule('taxi_pco_licence_expire_date', 'not_empty')
        ->rule('country', 'not_empty')
        ->rule('state', 'not_empty')
        ->rule('city', 'not_empty')
        ->rule('company_name', 'not_empty')

    /*  ->rule('taxi_capacity', 'not_empty')
        ->rule('taxi_capacity', 'min_length', array(':value', '1'))
        ->rule('taxi_capacity', 'max_length', array(':value', '20'))
        ->rule('taxi_capacity', 'digit', array(':value','/^[0-9]{1,}/'))*/

        /*->rule('taxi_fare_km', 'not_empty')
        ->rule('taxi_fare_km', 'min_length', array(':value', '1'))
        ->rule('taxi_fare_km', 'max_length', array(':value', '20'))
        ->rule('taxi_fare_km', 'digit', array(':value','/^[0-9]{1,}/'))*/
		->rule('taxi_image','Upload::size', array($arr['taxi_image'], IMG_MAX_SIZE))
        ->rule('company_name', 'Model_Edit::checkassignedtaxi',array($arr['city'],$arr['country'],$arr['state'],$arr['company_name'],$uid));

        foreach($arr as $key => $value)
        {   if(in_array($value,$form_values))
            {
                $rule = $rule->rule($value, 'not_empty');
            }

        }

        return $rule;

    }

    public static function check_taxi_speed_val($value)
    {
        // Check if the username already exists in the database
        if ($value <= 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function checkassignedtaxi($city, $country, $state, $company_name, $uid)
    {
        $mongodb        = MangoDB::instance('default');
        $current_time = Commonfunction::getCurrentTimeStamp();
        $result = $mongodb->findOne(MDB_TAXI_DRIVER_MAPPING,array('mapping_taxiid'=>(int)$uid,"\$and"=>array(array("\or"=>array("mapping_startdate"=>array(array('$gte'=>$current_time),array('$lte'=>$current_time)))),array("\or"=>array(array('mapping_companyid'=>array('$ne'=>(int)$company_name)),array('mapping_countryid'=>array('$ne'=>(int)$country)),array('mapping_stateid'=>array('$ne'=>(int)$state)),array('mapping_cityid'=>array('$ne'=>(int)$city)))))),array('_id'));
        //echo '<pre>';print_r($result);exit;
        return ($result>0)?false:true;
    }
    /**Validating for Add Motor**/
    public function validate_editpackage($arr, $uid)
    {
        return Validation::factory($arr)->rule('package_name', 'not_empty')->rule('package_name', 'min_length', array(
            ':value',
            '4'
        ))->rule('package_name', 'max_length', array(
            ':value',
            '100'
        ))->rule('package_name', 'Model_Edit::checkpackagename', array(
            ':value',
            $uid
        ))->rule('package_description', 'not_empty')->rule('package_description', 'min_length', array(
            ':value',
            '20'
        ))->rule('no_of_taxi', 'not_empty')->rule('no_of_taxi', 'Model_Edit::check_fare_zero', array(
            ':value'
        ))->rule('no_of_taxi', 'digit')->rule('no_of_driver', 'not_empty')->rule('no_of_driver', 'Model_Edit::check_fare_zero', array(
            ':value'
        ))->rule('no_of_driver', 'digit')->rule('days_expire', 'not_empty')->rule('days_expire', 'Model_Edit::check_fare_zero', array(
            ':value'
        ))->rule('days_expire', 'digit')->rule('package_price', 'not_empty')->rule('package_price', 'numeric');
    }
    public function validate_editcompanypayment($arr, $uid)
    {
        return Validation::factory($arr)
        ->rule('description', 'not_empty')
        ->rule('currency_code', 'not_empty')
        ->rule('currency_code', 'max_length', array(
            ':value',
            '3'
        ))->rule('currency_symbol', 'not_empty')
        ->rule('currency_symbol', 'Model_Admin::checksite_currency', array(
            ':value',
            $arr['currency_code']
        ))->rule('payment_method', 'not_empty')
        ->rule('payment_gateway_username', 'not_empty')
        ->rule('payment_gateway_password', 'not_empty')
        ->rule('payment_gateway_signature', 'not_empty');
    }
    //To update company Functionalities
    public function editcompany($uid, $post, $files)
    {
        //echo '<pre>';print_r($post);exit;
        $company_cid = $post['company_id'];
        if (isset($files['taxi_image']['name']) && $files['taxi_image']['name'] != '') {
            $image_name = $uid;
            $filename   = Upload::save($files['taxi_image'], $image_name, DOCROOT . COMPANY_IMG_IMGPATH);
            $logo_image = Image::factory($filename);
            $path1      = DOCROOT . COMPANY_IMG_IMGPATH;
            $path       = $image_name;
            Commonfunction::multipleimageresize($logo_image, COMPANY_IMG_WIDTH, COMPANY_IMG_HEIGHT, $path1, $image_name, 90);
            $check = 1;
        }
        $people_data      = array(
            'name' => $post['firstname'],
            'lastname' => $post['lastname'],
            'phone' => $post['phone'],
            'address' => $post['address'],
            'email' => $post['email'],
            'login_country' => (int)$post['country'],
            'login_state' => (int)$post['state'],
            'login_city' => (int)$post['city']
        );
        $people_result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$uid),array('$set'=>$people_data),array('upsert'=>false));
        $company_data     = array(
            'companydetails.company_name' => $post['company_name'],
            //'companydetails.company_address' => $post['address'],
            'companydetails.company_country' => (int)$post['country'],
            'companydetails.company_state' => (int)$post['state'],
            'companydetails.company_city' => (int)$post['city'],
            //'companydetails.time_zone' => $post['time_zone']
        );
        //echo '<pre>';print_r($company_cid);exit;
        $company_result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>(int)$company_cid),array('$set'=>$company_data),array('upsert'=>false));

        /** Company package expiry date upgrade functionality if it is changed **/
        if(!empty($post['prev_expiry_date']) && $post['prev_expiry_date'] != $post['expire_date']) {
            //print_r($post['prev_expiry_date'] ."!=". $post['expire_date'].'=='.$company_cid); exit;

            //taxi driver mapping date extended for that particular company
            $update = array('mapping_enddate' => Commonfunction::MongoDate(strtotime($post['expire_date'])));
            $mappingupdate = $this->mongo_db->updateOne(MDB_TAXI_DRIVER_MAPPING,array('mapping_companyid'=>(int)$company_cid),array('$set' => $update),array('upsert' => false));

            //status update in people table
            $update = array('status'=>'A');
            $stsupdate = $this->mongo_db->updateOne(MDB_PEOPLE,array('company_id'=>(int)$company_cid),array('$set' => $update),array('upsert' => false));
        }

        //Company payment settings Update
       /* if (isset($post['payid'])) {
            foreach ($post['payid'] as $k => $id) {
                //print_r($id);exit;
                $default = ($id == $post['default'][0])?1:0;
                $paystatus = (in_array($id, $post['paymodstatus']))?1:0;
                $payment_modules_data = array(
                    "paymentmodule.$k.pay_active" => (int)$paystatus,
                    "paymentmodule.$k.pay_mod_default" => (int)$default
                );
                $pay_result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>(int)$company_cid),array('$set'=>$payment_modules_data),array('upsert'=>true));
            }
        }
        if ($uid != 0) {
            //Company payment module settings Update
            if (isset($post['payid_add'])) {
                $payment_module_data = array();
                foreach ($post['payid_add'] as $k => $id) {
                    $default = ($id == $post['default'][0])?1:0;
                    $paystatus = (in_array($id, $post['paymodstatus']))?1:0;
                    $payment_module_data[] = array(
                        "paymentmodule.$k.pay_mod_id" => (int)$post['payid_add'][$k],
                        "paymentmodule.$k.pay_mod_name" => $post['paymodname'][$k],
                        "paymentmodule.$k.pay_mod_image" => $post['paymodimage'][$k],
                        "paymentmodule.$k.pay_active" => (int)$paystatus,
                        "paymentmodule.$k.pay_mod_default" => (int)$default
                    );
                }
                $pay_result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>(int)$company_cid),array('$set'=>array('paymentmodule'=>$payment_module_data)),array('upsert'=>true));
            }
        }*/
        return 1;
    }
    public function editcompanypayment($uid, $post)
    {
        $company_id = $this->company_id;
        if($post['payment_method'] == "T") {
            $payment_gateway_username_field = "payment_gateway_username";
            $payment_gateway_password_field = "payment_gateway_password";
            $payment_gateway_signature_field = "payment_gateway_signature";
            $payment_gateway_username = $post['payment_gateway_username'];
            $payment_gateway_password = $post['payment_gateway_password'];
            $payment_gateway_signature = $post['payment_gateway_signature'];
        } else {
            $payment_gateway_username_field = "live_payment_gateway_username";
            $payment_gateway_password_field = "live_payment_gateway_password";
            $payment_gateway_signature_field = "live_payment_gateway_signature";
            $payment_gateway_username = $post['live_payment_gateway_username'];
            $payment_gateway_password = $post['live_payment_gateway_password'];
            $payment_gateway_signature = $post['live_payment_gateway_signature'];
        }
        $query = array(
            'description' => $post['description'],
           /* 'currency_code' => $post['currency_code'],
            'currency_symbol' => $post['currency_symbol'],*/
            'payment_method' => $post['payment_method'],
            $payment_gateway_username_field => $payment_gateway_username,
            $payment_gateway_password_field => $payment_gateway_password,
            $payment_gateway_signature_field => $payment_gateway_signature
        );
        //MongoDB
        $result = $this->mongo_db->updateOne(MDB_PAYMENT_GATEWAYS,array('_id'=>(int)$uid,'company_id'=>(int)$company_id),array('$set'=>$query),array('upsert'=>true));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    //To update company Functionalities
    public function editmodel($uid, $post)
    {
        $query = array(
            'model_name' => $post['model_name'],
            'model_size' => (int)$post['model_size'],
            'base_fare' =>(float) $post['base_fare'],
            'min_fare' => (float)$post['min_fare'],
            'cancellation_fare' => (float)$post['cancellation_fare'],
            'below_km' => (float)$post['below_km'],
            'above_km' => (float)$post['above_km'],
            'night_charge' => (float)$post['night_charge'],
            'night_timing_from' => $post['night_timing_from'],
            'night_timing_to' => $post['night_timing_to'],
            'night_fare' => (float)$post['night_fare'],
            'evening_charge' => (float)$post['evening_charge'],
            'evening_timing_from' => $post['evening_timing_from'],
            'evening_timing_to' => $post['evening_timing_to'],
            'evening_fare' => (float)$post['evening_fare'],
            'waiting_time' => (float)$post['waiting_time'],
            'min_km' => (float)$post['min_km'],
            'taxi_speed' => (float)$post['taxi_speed'],
            'below_above_km' => (float)$post['below_and_above_km'],
            'minutes_fare' => (float)$post['minutes_fare'],
            'description' => $post['description'],
        );
		
        //MongoDB
        $result = $this->mongo_db->updateOne(MDB_MOTOR_MODEL,array('_id'=>(int)$uid),array('$set'=>$query),array('upsert'=>true));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    //To update company Functionalities
    public function editfare($post)
    {
            $company_id = $this->company_id;
            $model_fare_array = array(
                'model_fare.$.model_name' => $post['model_name'],
                'model_fare.$.model_size' => (int)$post['model_size'],
                'model_fare.$.base_fare' => (float)$post['base_fare'],
                'model_fare.$.min_fare' => (float)$post['min_fare'],
                'model_fare.$.cancellation_fare' => (float)$post['cancellation_fare'],
                'model_fare.$.below_km' => (float)$post['below_km'],
                'model_fare.$.above_km' => (float)$post['above_km'],
                'model_fare.$.night_charge' => (float)$post['night_charge'],
                'model_fare.$.night_timing_from' => $post['night_timing_from'],
                'model_fare.$.night_timing_to' => $post['night_timing_to'],
                'model_fare.$.night_fare' => (float)$post['night_fare'],
                'model_fare.$.evening_charge' => (float)$post['evening_charge'],
                'model_fare.$.evening_timing_from' => $post['evening_timing_from'],
                'model_fare.$.evening_timing_to' => $post['evening_timing_to'],
                'model_fare.$.evening_fare' => (float)$post['evening_fare'],
                'model_fare.$.min_km' => (float)$post['min_km'],
                'model_fare.$.waiting_time' => $post['waiting_time'],
                'model_fare.$.below_above_km' => (float)$post['below_and_above_km'],
                'model_fare.$.minutes_fare' => (float)$post['minutes_fare']
            );
            $model_id = (int)$post['company_model_fare_id'];
            $result = $this->mongo_db->updateMany(MDB_COMPANY,array('_id'=>(int)$company_id, 'model_fare.model_id'=>(int)$model_id),array('$set'=>$model_fare_array));
            return (empty($result->getwriteErrors())) ? 1 : 0;
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }
    public function get_payment_details($uid)
    {
        $company_id = $this->company_id;
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_PAYMENT_GATEWAYS,array('_id'=>(int)$uid,'company_id'=>(int)$company_id));
        $data = array();
        if(count($result) > 0){
            $data[] = $result;
        }
        return $data;
    }
    // Check Whether Email is Already Exist or Not
    public static function checkemail($email = "", $uid)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PEOPLE,array('email'=>$email,'_id'=>array('$ne'=>(int)$uid)));
        return ($result > 0)?false:true;
    }
    // Check Whether Email is Already Exist or Not
    public static function checkphone($phone = "", $uid)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PEOPLE,array('phone'=>$phone,'_id'=>array('$ne'=>(int)$uid)));
        return ($result > 0)?false:true;
    }
    // Check Whether Email is Already Exist or Not
    public static function check_passengeremail($email = "", $uid)
    {
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PASSENGERS,array('email'=>$email,'_id'=>array('$ne'=>(int)$uid)));
        return ($result > 0)?false:true;
    }
    // Check Whether Company details is Already Exist or Not
    public static function company_details($uid)
    {
        $mongodb = MangoDB::instance('default');
        $result = array();
        $ops = array(
            array(
                    '$lookup' => array(
                        'from'=>MDB_PEOPLE,
                        'localField'=> "_id",
                        'foreignField' => "company_id",
                        'as'=> "people"
                    )
                ),
            array('$unwind' => '$people'),
            array('$match' => array('people.user_type'=>'C','people._id'=>(int)$uid)),
            array(
                    '$project' => array(
                        'company_status' => '$companydetails.company_status',
                        'company_name' => '$companydetails.company_name',
                        'company_address' => '$companydetails.company_address',
                        'company_country' => '$companydetails.company_country',
                        'company_state' => '$companydetails.company_state',
                        'company_city' => '$companydetails.company_city',
                        'company_domain' => '$companyinfo.company_domain',
                        'time_zone' => '$companydetails.time_zone',
                        'userid' => '$companydetails.userid',
                        'upgrade_expirydate' => '$package.upgrade_expirydate',
                        'name' => '$people.name',
                        'lastname' => '$people.lastname',
                        'phone' => '$people.phone',
                        'address' => '$people.address',
                        'company_id' => '$people.company_id',
                        'email' => '$people.email',
                        'user_type' => '$people.user_type',
                        'id' => '$people._id',
                        'cid' => '$_id',
                        )
            ),
        );
        $res = $mongodb->aggregate(MDB_COMPANY,$ops);
        if(!empty($res['result'])){
            $result = $res['result'];
        }
        return $result;
    }
    public function company_details_new($uid)
    {
        $result = array();
        $ops = array(
            array(
                    '$lookup' => array(
                        'from'=>MDB_PEOPLE,
                        'localField'=> "_id",
                        'foreignField' => "company_id",
                        'as'=> "people"
                    )
                ),
            array('$unwind' => '$people'),
            array('$match' => array('people.user_type'=>'C','people._id'=>(int)$uid)),
            array(
                '$project' => array(
                    'company_status' => '$companydetails.company_status',
                    'company_name' => '$companydetails.company_name',
                    'company_address' => '$companydetails.company_address',
                    'company_country' => '$companydetails.company_country',
                    'company_state' => '$companydetails.company_state',
                    'company_city' => '$companydetails.company_city',
                    'company_domain' => '$companyinfo.company_domain',
                    'time_zone' => '$companydetails.time_zone',
                    'userid' => '$companydetails.userid',
                    'upgrade_expirydate' => '$package.upgrade_expirydate',
                    'name' => '$people.name',
                    'lastname' => '$people.lastname',
                    'phone' => '$people.phone',
                    'mobile_code' => '$people.country_code',
                    'address' => '$people.address',
                    'company_id' => '$people.company_id',
                    'email' => '$people.email',
                    'user_type' => '$people.user_type',
                    'id' => '$people._id',
                    'cid' => '$_id',
                )
            ),
        );
        $res = $this->mongo_db->aggregate(MDB_COMPANY,$ops);
        if(!empty($res['result'])){
            $result = $res['result'];
        }
        return $result;
    }
    public function model_motordetails($uid)
    {
        $result = array();
        $args = array(
                array('$match' => array('_id' => (int)$uid)),
                array('$lookup' => array('from' => MDB_MOTOR_COMPANY, 'localField' => 'motor_id', 'foreignField' => '_id','as' => 'motor')),
                array('$unwind' => '$motor'),
                array('$project' => array('_id'=> 0, 'model_id' => '$_id',
                                        'model_name' => '$model_name','motor_name' => '$motor.motor_name',
                                        'model_status' => '$model_status','motor_id' => '$motor._id',
                                        'base_fare' => '$base_fare','min_km' => '$min_km',
                                        'min_fare' => '$min_fare','cancellation_fare' => '$cancellation_fare',
                                        'below_above_km' => '$below_above_km','below_km' => '$below_km',
                                        'above_km' => '$above_km','waiting_time' => '$waiting_time',
                                        'minutes_fare' => '$minutes_fare','taxi_min_speed' => '$taxi_min_speed',
                                        'night_charge' => '$night_charge','night_timing_from' => '$night_timing_from',
                                        'night_timing_to' => '$night_timing_to','night_fare' => '$night_fare',
                                        'evening_charge' => '$evening_charge','evening_timing_from' => '$evening_timing_from',
                                        'evening_timing_to' => '$evening_timing_to','evening_fare' => '$evening_fare',
                                        'model_size' => '$model_size','taxi_speed' => '$taxi_speed',
                                        'description' => '$description'))
            );
        $res = $this->mongo_db->Aggregate(MDB_MOTOR_MODEL,$args);
        if(!empty($res['result'])){
            $result = $res['result'];
            $result[0]['description'] = isset($result[0]['description']) ? $result[0]['description'] : '';
        }
        return $result;
    }
    // Check Whether Motor details is Already Exist or Not
    public function motordetails()
    {
        $result = $temp_arr = array();
        //$res = $this->mongo_db->find(MDB_MOTOR_COMPANY,array("motor_status"=>"A"),array('_id','motor_name'))->sort(array('motor_name'=>1));
                ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,
                        'motor_name'=>1
                    ],
                    'sort'=>[
                        'motor_name'=>-1
                        ]
                ];
        $res = $this->mongo_db->find(MDB_MOTOR_COMPANY,["motor_status"=>"A"],$options);
        if(!empty($res)){
            foreach($res as $r){
                $temp_arr = array('motor_id' => $r['_id'], 'motor_name' => $r['motor_name']);
                $result[] = $temp_arr;
            }
        }
        return $result;
    }
    public function model_faredetails($uid)
    {
        $result = array();
        $company_id = $this->company_id;
        $ops =  array(
                    array('$unwind' => '$model_fare'),
                    array('$match'=>array('_id' => (int)$company_id,'model_fare.model_id' => (int)$uid)),
                    array(
                        '$project' => array(
                            'model_fare' => 1,
                        )
                    ),
                );
        $res = $this->mongo_db->aggregate(MDB_COMPANY,$ops);
        $result[] = !empty($res['result'][0]['model_fare']) ? $res['result'][0]['model_fare']: array();
        $result[0]['company_model_fare_id'] = !empty($result[0]) ? $result[0]['model_id']:'';
        return $result;
    }
    // Check Whether Manage Field details is Already Exist or Not
    public function managetaxi_details($uid)
    {
        $result = array();
        $res = $this->mongo_db->findOne(MDB_TAXI,array('_id'=>(int)$uid),array('taxi_no','taxi_company','taxi_model','taxi_owner_name','taxi_manufacturer','taxi_colour','taxi_motor_expire_date','taxi_insurance_number','taxi_insurance_expire_date_time','taxi_pco_licence_number','taxi_pco_licence_expire_date','taxi_speed','taxi_min_speed','max_luggage','taxi_fare_km','taxi_country','taxi_state','taxi_city','taxi_image',MDB_TAXI.'.taxi_id','taxi_sliderimage','taxi_serializeimage'));
        if(!empty($res)){

            $res['taxi_id'] = $res['_id'];
            $res['taxi_motor_expire_date'] = !empty($res['taxi_motor_expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['taxi_motor_expire_date']) : '';
            $res['taxi_insurance_expire_date_time'] = !empty($res['taxi_insurance_expire_date_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['taxi_insurance_expire_date_time']) : '';
            $res['taxi_pco_licence_expire_date'] = !empty($res['taxi_pco_licence_expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$res['taxi_pco_licence_expire_date']) : '';
            $res['taxi_serializeimage'] = isset($res['taxi_serializeimage']) ? $res['taxi_serializeimage'] : '';

            $result[] = $res;
        }
        //echo '<pre>';print_r($result);exit;
        return $result;
    }

    /** for updating taxi image **/
    public function edittaxi_image($image, $uid)
    {
        $result = $this->mongo_db->findOne(MDB_TAXI,array('_id'=>(int)$uid),array('taxi_image'));
        if(!empty($result)) {
            $id1 = $result['taxi_image'];
            if (file_exists($id1)) {
                unlink($id1);
            }
        }
        $res = $this->mongo_db->updateOne(MDB_TAXI,array('_id'=>(int)$uid),array('$set'=>array('taxi_image'=>$image)),array('upsert'=>true));
        return (empty($res->getwriteErrors())) ? 1 : $res->getwriteErrors();
    }

    //To Edit Taxi Functionalities
    public function edittaxi($post, $form_array, $uid, $files)
    {
        //print_r($uid  ); exit;
        $nexti            = '';
        $add_count        = $post['add_count'];
        if(isset($files['updateimage']['name']) && $files['updateimage']['type'] !='')
        {
            foreach($files['updateimage']['name'] as $key => $value)
            {
                $nexti = $key;
                $file_array = array();
                if(isset($files['updateimage']['name'][$key]) && $files['updateimage']['name'][$key]!='')
                {
                    $file_array['name'] =  $files['updateimage']['name'][$key];
                    $file_array['type'] = $files['updateimage']['type'][$key];
                    $file_array['tmp_name'] = $files['updateimage']['tmp_name'][$key];
                    $file_array['error'] = $files['updateimage']['error'][$key];
                    $image_name = $uid.'_'.$key;
                    $filename = Upload::save($file_array,$image_name,DOCROOT.TAXI_IMG_IMGPATH);
                    $logo_image = Image::factory($filename);
                    $path1=DOCROOT.TAXI_IMG_IMGPATH;
                    $path=$image_name;
                    Commonfunction::multipleimageresize($logo_image,TAXI_IMG_WIDTH, TAXI_IMG_HEIGHT,$path1,$image_name,90);
                }
            }
        }

        $taxi_arrcount = array();
        if(isset($files['size']['name']) && $files['size']['type'] !='')
        {
            $count = count($files['size']['name']);  $z = 0;
            for($j=0;$j<$count;$j++)
            {
                $file_array = array();
                if($files['size']['name'][$j]!='')
                {
                    $z++;
                    if($nexti ==''){
                        $nexti = $add_count;
                    }
                    else{
                        $nexti++;
                    }
                    $file_array['name'] =  $files['size']['name'][$j];
                    $file_array['type'] = $files['size']['type'][$j];
                    $file_array['tmp_name'] = $files['size']['tmp_name'][$j];
                    $file_array['error'] = $files['size']['error'][$j];
                    $image_name = $uid.'_'.$nexti;
                    $taxi_arrcount[] = $nexti;
                    $filename = Upload::save($file_array,$image_name,DOCROOT.TAXI_IMG_IMGPATH);
                    $logo_image = Image::factory($filename);
                    $path1=DOCROOT.TAXI_IMG_IMGPATH;
                    $path=$image_name;
                    Commonfunction::multipleimageresize($logo_image,TAXI_IMG_WIDTH, TAXI_IMG_HEIGHT,$path1,$image_name,90);
                }
            }

            $image_serialize = array();
            $updateresult = $this->mongo_db->updateOne(MDB_TAXI,array('_id'=>(int)$uid),array('$inc'=>array('taxi_sliderimage'=>(int)$z)),array('upsert'=>false));

            $array_result = $this->mongo_db->findOne(MDB_TAXI,array('_id' => (int)$uid),array('taxi_serializeimage'));
            $image_serialize = !empty($array_result) ? unserialize($array_result['taxi_serializeimage']) : '';

            if(is_array($image_serialize)){
                $update_array = array_merge($image_serialize, $taxi_arrcount);
            }
            else{
                $update_array = $taxi_arrcount;
            }
            $update_arrimage = serialize($update_array);
            $updateresult = $this->mongo_db->updateOne(MDB_TAXI,array('_id'=>(int)$uid),array('$set'=>array('taxi_serializeimage'=>$update_arrimage)),array('upsert'=>false));
        }

        $post['taxi_type'] = 1;
        $query = array(
            'taxi_no' => $post['taxi_no'],
            'taxi_type' => (int)$post['taxi_type'],
            'taxi_model' => (int)$post['taxi_model'],
            'taxi_country' => (int)$post['country'],
            'taxi_state' => (int)$post['state'],
            'taxi_city' => (int)$post['city'],
            'taxi_capacity' => 0,
            'taxi_speed' => (float)$post['taxi_speed'],
            'max_luggage' => (float)$post['minimum_luggage'],
            'taxi_fare_km' => (float)$post['taxi_fare_km'],
            'taxi_company' => (int)$post['company_name'],
            'taxi_owner_name' => $post['taxi_owner_name'],
            'taxi_manufacturer' => $post['taxi_manufacturer'],
            'taxi_colour' => $post['taxi_colour'],
            'taxi_motor_expire_date' => Commonfunction::MongoDate(strtotime($post['taxi_motor_expire_date'])),
            'taxi_pco_licence_number' => $post['taxi_pco_licence_number'],
            'taxi_pco_licence_expire_date' => Commonfunction::MongoDate(strtotime($post['taxi_pco_licence_expire_date'])),
            'taxi_insurance_number' => $post['taxi_insurance_number'],
            'taxi_insurance_expire_date_time' => Commonfunction::MongoDate(strtotime($post['taxi_insurance_expire_date'])),
            'taxi_min_speed' => (float)$post['taxi_min_speed']
        );
        $result = $this->mongo_db->updateOne(MDB_TAXI,array('_id'=>(int)$uid),array('$set'=>$query),array('upsert'=>true));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    public function check_taxicompanyid($id)
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_TAXI,array('_id'=>(int)$id),array('_id','taxi_company','taxi_state','taxi_city','taxi_country'));
        return (!empty($result))?$result:array();
    }
    public static function check_taxino($name, $uid)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_TAXI,array('taxi_no'=>$name,'_id'=>array('$ne'=>(int)$uid)),array('taxi_no'));
        return ($result>0)?false:true;
    }
    // To Check Motorname is Already Available or Not
    public static function checkmodelname($name, $motorid, $uid)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_MOTOR_MODEL,array("model_name" => Commonfunction::MongoRegex("/$name/i"),"_id"=>array('$ne'=>(int)$uid)));
        return ($result>0)?false:true;
    }
    public function driver_details($uid)
    {
        $arguments = array(
            array('$match'  => array('_id' => (int)$uid, 'user_type' => 'D')),
            array(
                '$project' => array(
                    'id' => '$_id',
                    'name'=>'$name',
                    'address'=>'$address',
                    'lastname'=>'$lastname',
                    'gender'=>'$gender',
                    'dob'=>'$dob',
                    'email'=>'$email',
                    'phone'=>'$phone',
                    'org_password'=>'$org_password',
                    'user_type' => '$user_type',
                    'status' => '$status',
                    'login_country' => '$login_country',
                    'login_state' => '$login_state',
                    'login_city' => '$login_city',
                    'company_id' => '$company_id',
                    'driver_license_id' => '$driver_license_id',
                    'booking_limit' => '$booking_limit',
                    'profile_picture' => '$profile_picture'
                )
            ),
        );
        $result = $this->mongo_db->aggregate(MDB_PEOPLE,$arguments);
        //echo "<pre>"; print_r($result['result']); exit;
        return (!empty($result['result']))?$result['result']:array();
    }
    public function manager_details($uid)
    {
        $res = $this->mongo_db->findOne(MDB_PEOPLE,array('_id' => (int)$uid, 'user_type' => 'M'),array('name','lastname','email','phone','address','login_country','login_state','login_city','company_id'));
        (!empty($res)) ? $result[] = $res: $result =array();
        return $result;
    }
    public function taxicompany_details()
    {
             ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,
                        'companydetails'=>1
                    ],
                    'sort'=>[
                        'companydetails.company_name'=>1
                        ]
                ];
        $result = $this->mongo_db->find(MDB_COMPANY,['companydetails.company_status' => 'A'],$options);
        return (!empty($result))?$result:array();
    }
    public function peoplecompany_details($uid)
    {
        $result = $this->mongo_db->find(MDB_PEOPLE,array('_id' => (int)$uid));
                $res = (!empty($result))?$result:array();
        return $res;
    }

    public function moderator_details($uid)
    {
        //MongoDB
        $ops = array(
            array('$match'=>array('user_type'=>'S','_id'=>(int)$uid)),
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
                '$project' => array(
                'country_name' => '$countrydetails.country_name',
                'name' => '$name',
                'email' => '$email',
                'address' => '$address',
                'status' => '$status',
                'lastname' => '$lastname',
                'login_country' => '$login_country',
                'phone' => '$phone'
                )
            )
        );
        $results = $this->mongo_db->aggregate(MDB_PEOPLE,$ops);
        //echo '<pre>';print_r($results);exit;
        return (!empty($results['result']))?$results['result']:array();

    }
    //to get driver's licence and insurance details from driver info table
    public function driver_info_details($driver_id)
    {
        //MongoDB
        $arguments = array(
            array('$match'=>array('_id'=>(int)$driver_id)),
            array('$unwind'=>'$driverinfo'),
            array(
                '$project' => array(
                    'driver_license_expire_date' => '$driverinfo.driver_license_expire_date',
                    'driver_pco_license_number' => '$driverinfo.driver_pco_license_number',
                    'driver_pco_license_expire_date' => '$driverinfo.driver_pco_license_expire_date',
                    'driver_insurance_number' => '$driverinfo.driver_insurance_number',
                    'driver_insurance_expire_date' => '$driverinfo.driver_insurance_expire_date',
                    'driver_national_insurance_number' => '$driverinfo.driver_national_insurance_number',
                    'driver_national_insurance_expire_date' => '$driverinfo.driver_national_insurance_expire_date'
                )
            )
        );
        $result = $this->mongo_db->aggregate(MDB_DRIVER_INFO,$arguments);
        //echo '<pre>';print_r($result['result']);exit;
        return (!empty($result['result']))?$result['result']:array();
    }
    //To update Edit Driver Functionalities
    public function edit_driver($post, $uid)
    {
        //$password = Html::chars(md5($post['password']));
        if (COMPANY_CID == 1 || SUBDOMAIN == 'demo') {
            $param = array(
                'name' => $post['firstname'],
                'address' => $post['address'],
                'login_country' => (int)$post['country'],
                'login_state' => (int)$post['state'],
                'login_city' => (int)$post['city'],
                'lastname' => $post['lastname'],
                'gender' => $post['gender'],
                'dob' => Commonfunction::MongoDate(strtotime($post['dob'])),
                'email' => $post['email'],
                'driver_license_id' => $post['driver_license_id'],
                'phone' => $post['phone'],
                'company_id' => (int)$post['company_name'],
                'booking_limit' => $post['booking_limit']
            );
            //MongoDB
            $result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$uid),array('$set'=>$param),array('upsert'=>false));
        } else {
            $param = array(
                'name' => $post['firstname'],
                'address' => $post['address'],
                'login_country' => (int)$post['country'],
                'login_state' => (int)$post['state'],
                'login_city' => (int)$post['city'],
                'lastname' => $post['lastname'],
                'gender' => $post['gender'],
                'dob' => Commonfunction::MongoDate(strtotime($post['dob'])),
                'email' => $post['email'],
                /*'password' => $password,
                'org_password' => $post['password'],*/
                'driver_license_id' => $post['driver_license_id'],
                'phone' => $post['phone'],
                'company_id' => (int)$post['company_name'],
                'booking_limit' => (int)$post['booking_limit']
            );
            //MongoDB
            $result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$uid),array('$set'=>$param),array('upsert'=>false));
        }
        $arguments = array(array('$unwind' => '$stateinfo'),array('$unwind' => '$stateinfo.cityinfo'),
            array('$match' => array('stateinfo.cityinfo.city_id'=> (int)$post['city'],'stateinfo.cityinfo.city_status' => 'A')),
            array('$project' => array('_id' => 0,'city_name' => '$stateinfo.cityinfo.city_name',)),
            array('$sort' => array('stateinfo.cityinfo.city_name' => 1),)
        );
        $cityresult = $this->mongo_db->aggregate(MDB_CSC,$arguments);
        $cityresult = $cityresult['result'];
        $address    = $cityresult[0]['city_name'];
        $address    = str_replace(' ', '+', $address);
        $url        = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key=' . GOOGLE_GEO_API_KEY;
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $geoloc = curl_exec($ch);
        //print_r($geoloc);
        $json   = json_decode($geoloc);
        if ($json->status == 'OK') {
            $latitude  = $json->results[0]->geometry->location->lat;
            $longitude = $json->results[0]->geometry->location->lng;
        } else {
            $latitude  = (double)LOCATION_LATI;
            $longitude = (double)LOCATION_LONG;
        }
        $driver_data = array(
            'loc'=>array('type' => 'Point',
            'coordinates'=>array($longitude,$latitude)),
        );
        //echo '<pre>';print_r($driver_data);exit;
        $result = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id'=>(int)$uid),array('$set'=>$driver_data),array('upsert'=>false));
        //Driver Info
        $driver_info_data = array(
            'driverinfo' => array(
                array(
                    'driver_license_expire_date' => Commonfunction::MongoDate(strtotime($post['driver_license_expire_date'])),
                    'driver_pco_license_number' => $post['driver_pco_license_number'],
                    'driver_pco_license_expire_date'=> Commonfunction::MongoDate(strtotime($post['driver_pco_license_expire_date'])),
                    'driver_insurance_number'=> $post['driver_insurance_number'],
                    'driver_insurance_expire_date'=> Commonfunction::MongoDate(strtotime($post['driver_insurance_expire_date'])),
                    'driver_national_insurance_number'=>$post['driver_national_insurance_number'],
                    'driver_national_insurance_expire_date'=> Commonfunction::MongoDate(strtotime($post['driver_national_insurance_expire_date']))
                )
            )
        );
        $driver_info = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id'=>(int)$uid),array('$set'=>$driver_info_data),array('upsert'=>false));
        //the condition to get the response 1 if driver or driver info detials changed
        if ($result || $driver_info) {
            return 1;
        } else {
            return 0;
        }
    }
    // Check Whether Country details is Already Exist or Not
    public function country_detail($uid)
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_CSC,array('_id'=>(int)$uid),array('_id','country_name','iso_country_code','telephone_code','currency_code','currency_symbol','country_status','default'));
        $data = array();
        if(count($result) > 0){
            $arr = $result;
            $arr['sms_id'] = $result['_id'];
            $data[] = $arr;
        }
        return $data;
    }
    public function country_details($uid)
    {
        //MongoDB
        $result = array();
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
                        ]
                ];
                $res = $this->mongo_db->find(MDB_CSC,['_id'=>(int)$uid],$options);
        $res_arr = $res;
        $result = array();
        if(!empty($res_arr)){
            foreach($res_arr as $r){
                $result[] = array('country_id' => $r['_id'], 'country_name' =>
                $r['country_name'], 'iso_country_code' => $r['iso_country_code'], 'telephone_code' => $r['telephone_code'], 'currency_code' => $r['currency_code'], 'currency_symbol' => $r['currency_symbol'], 'country_status' => $r['default']);
            }
        }
        return $result;
    }
    public function validate_editcountry($arr, $uid)
    {
        return Validation::factory($arr)->rule('country_name', 'not_empty')
        //->rule('country_name', 'alpha_dash')
            ->rule('country_name', 'Model_Edit::check_reg_countryname', array(
            ':value'
        ))->rule('country_name', 'min_length', array(
            ':value',
            '2'
        ))->rule('country_name', 'max_length', array(
            ':value',
            '30'
        ))->rule('country_name', 'Model_Edit::checkcountryname', array(
            ':value',
            $uid
        ))->rule('iso_country_code', 'not_empty')->rule('iso_country_code', 'min_length', array(
            ':value',
            '2'
        ))->rule('iso_country_code', 'max_length', array(
            ':value',
            '5'
        ))->rule('iso_country_code', 'Model_Edit::checkisocountrycode', array(
            ':value',
            $uid
        ))->rule('telephone_code', 'not_empty')->rule('telephone_code', 'min_length', array(
            ':value',
            '2'
        ))->rule('telephone_code', 'max_length', array(
            ':value',
            '5'
        ))->rule('currency_code', 'not_empty')->rule('currency_code', 'min_length', array(
            ':value',
            '2'
        ))->rule('currency_code', 'max_length', array(
            ':value',
            '5'
        ))->rule('currency_symbol', 'not_empty')->rule('currency_symbol', 'max_length', array(
            ':value',
            '10'
        ));
    }
    public function validate_edit_template($arr, $uid)
    {
        return Validation::factory($arr)->rule('sms_description', 'not_empty');
    }
    public static function checkcountryname($name, $uid)
    {
        $cid = (int)$uid;
        $mongodb = MangoDB::instance('default');
        $res = $mongodb->count(MDB_CSC,array('country_name'=>Commonfunction::MongoRegex("/^$name/i"),'_id'=>array('$ne'=>$cid)),array('country_name'));
        return ($res > 0)?false:true;
    }
    public static function checkfaqtitle($faq, $fid)
    {
        $mongo_db = MangoDB::instance('default');
        $result = $mongo_db->count(MDB_PASSENGERS_FAQ,array('faq_title'=>$faq,'_id'=>array('$ne'=>(int)$fid)));
        return ($result > 0)?false:true;
    }
    public static function checkisocountrycode($iso_country_code, $uid)
    {
        $cid = (int)$uid;
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $res = $mongodb->count(MDB_CSC,array('iso_country_code'=>Commonfunction::MongoRegex("/^$iso_country_code/i"),'_id'=>array('$ne'=>$cid)),array('iso_country_code'));
        return ($res > 0)?false:true;
    }
    public function editcountry($uid, $post)
    {
		$symbol = mb_convert_encoding($post['currency_symbol'], 'UTF-8', 'HTML-ENTITIES');
        $query = array(
            'iso_country_code' => $post['iso_country_code'],
            'telephone_code' => $post['telephone_code'],
            'currency_code' => $post['currency_code'],
            'currency_symbol' => $symbol
        );
        //MongoDB
        $result = $this->mongo_db->updateOne(MDB_CSC,array('_id'=>(int)$uid),array('$set'=>$query),array('upsert'=>true));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    public function edittemplate($uid, $post)
    {
        $query = array('sms_description' => $post['sms_description']);
        $result = $this->mongo_db->updateOne(MDB_SMS_TEMPLATES,array('_id'=>(int)$uid),array('$set'=>$query),array('upsert'=>true));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }

    public function sms_template($id)
    {
		$result = array();
        $res = $this->mongo_db->findOne(MDB_SMS_TEMPLATES, array('_id' => (int)$id),array("_id","sms_title", "sms_info","sms_description","status"));
		if(!empty($res)){
            $temp_arr['sms_id'] = isset($res['_id'])?$res['_id']:'';
            $temp_arr['sms_title'] = isset($res['sms_title'])?$res['sms_title']:'';
            $temp_arr['sms_info'] = isset($res['sms_info'])?$res['sms_info']:'';
            $temp_arr['sms_description'] = isset($res['sms_description'])?$res['sms_description']:'';
            $temp_arr['status'] = isset($res['status'])?$res['status']:'';

            $result[] = $temp_arr;
        }
        return $result;
    }

    public function countrydetails()
    {

        //$result = $this->mongo_db->find(MDB_CSC, array('country_status' => 'A'),array("_id","country_name"));
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,
                        'country_name'=>1
                    ]
                ];
        $result = $this->mongo_db->find(MDB_CSC, ['country_status' => 'A'],$options);
        $res = $result;
        $data = array();
        if(count($res) > 0){
            foreach($res as $val){
                $arr = $val;
                $arr['country_id'] = $val['_id'];
                $data[] = $arr;
            }
        }
        return $data;


    }

    public function city_details()
    {
        //MongoDB
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$unwind' => '$stateinfo.cityinfo'),
            /*array('$match' => array('stateinfo.cityinfo.city_status'=>'A','stateinfo.state_id'=>(int)DEFAULT_STATE,'_id'=>(int)DEFAULT_COUNTRY)),*/
            array('$match' => array('stateinfo.cityinfo.city_status'=>'A')),
            array('$project' => array('_id' => 0,
                'city_id' => '$stateinfo.cityinfo.city_id',
                'city_name' => '$stateinfo.cityinfo.city_name',
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
    public function state_details()
    {

        //MongoDB
        $ops = array(
            array('$unwind' => '$stateinfo'),
            /*array('$match' => array('stateinfo.state_status'=>'A','_id'=>(int)DEFAULT_COUNTRY)),*/
            array('$match' => array('stateinfo.state_status'=>'A')),
            array('$project' => array('_id' => 0,
                'state_id' => '$stateinfo.state_id',
                'state_name' => '$stateinfo.state_name',
                )
            )
        );
        $result = $this->mongo_db->aggregate(MDB_CSC,$ops);
        //print_r($result); exit;
        $data = array();
        if(count($result['result']) > 0){
            foreach($result['result'] as $val){
                $arr = $val;
                $data[] = $arr;
            }
        }

       return $data;


    }
    public function country_details_new()
    {
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>[
                        '_id'=>1,
                        'country_name'=>1
                    ]
                ];
                $res = $this->mongo_db->find(MDB_CSC,['country_status'=>'A'],$options);
        $result= $res;

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
    public function city_countrydetails($cityid)
    {
        //MongoDB
        //$country_id = (int)$countryid;
        //$state_id = (int)$stateid;
        $city_id = (int)$cityid;

        //MongoDB with aggregate process only
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$unwind' => '$stateinfo.cityinfo'),
            array('$match' => array('stateinfo.cityinfo.city_id'=>$city_id)),
            array('$project' => array('_id' => 0,
                'city_id' => '$stateinfo.cityinfo.city_id',
                'city_name' => '$stateinfo.cityinfo.city_name',
                'country_name' => '$country_name',
                'city_countryid' => '$_id',
                'country_id' => '$stateinfo.cityinfo.city_countryid',
                'city_stateid' => '$stateinfo.cityinfo.city_stateid',
                'zipcode' => '$stateinfo.cityinfo.zipcode',
                'city_model_fare' => '$stateinfo.cityinfo.city_model_fare',
            ))
        );
        $result = $this->mongo_db->aggregate(MDB_CSC,$ops);
        return (!empty($result['result']))?$result['result']:array();
    }

    public function validate_editcity($arr, $uid)
    {
        return Validation::factory($arr)->rule('city_name', 'not_empty')
        //->rule('city_name', 'alpha_dash')
            ->rule('city_name', 'Model_Edit::check_reg_city_name', array(
            ':value'
        ))->rule('city_name', 'min_length', array(
            ':value',
            '2'
        ))->rule('city_name', 'max_length', array(
            ':value',
            '30'
        ))->rule('city_name', 'Model_Edit::checkcityname', array(
            ':value',
            $arr['state_name'],
            $arr['country_name'],
            $uid
        ))->rule('zipcode', 'not_empty')
        ->rule('state_name', 'not_empty')
        ->rule('country_name', 'not_empty')
        ->rule('city_model_fare', 'not_empty')
        ->rule('city_model_fare', 'numeric')
        /*->rule('city_model_fare', 'decimal', array(
            ':value',
            '2'
        ))*/
        ->rule('city_model_fare', 'Model_Edit::checkmodelfare', array(':value'));
    }
    // To Check Motorname is Already Available or Not
    public static function checkcityname($name, $stateid, $countryid, $uid)
    {
        //Check if the username already exists in the database
        //MongoDB
        $city_id = (int)$uid;
        $country_id = (int)$countryid;
        $state_id = (int)$stateid;
        $mongodb = MangoDB::instance('default');
        $match = array('_id' => $country_id, 'stateinfo.state_id' => $state_id, 'stateinfo.cityinfo.city_id' => array('$ne'=>$city_id));
        $args = array(array('$unwind' => '$stateinfo'),
                      array('$unwind' => '$stateinfo.cityinfo'),
                      array('$match' => $match),
                      array('$project' => array('city_name'=>'$stateinfo.cityinfo.city_name')),
                      //array('$limit' => 1)
                  );
        $res = $mongodb->aggregate(MDB_CSC,$args);
        $match=0;
        if(!empty($res['result'])){
			foreach($res['result'] as $c){
				if(strtolower($c['city_name']) == strtolower($name))
					$match=1;
			}				
		}
        return ($match == 1)?false:true;
    }
     // To Check Model fare
    public static function checkmodelfare($value)
    {
        return  ((!preg_match('/^[0-9]+(\.[0-9]{1,2})?/', $value)) || $value > '100')?false:true;
    }

    public function editcity($cityid, $post)
    {
        $new_country = $post['country_name'];
        $old_country = $post['city_countryid'];
        $new_state = $post['state_name'];
        $old_state = $post['state_id'];

        $match = array('_id' => (int)$old_country, 'stateinfo.state_id' => (int)$old_state, 'stateinfo.cityinfo.city_id' => (int)$cityid);
        $args = array(array('$unwind' => '$stateinfo'),
                      array('$unwind' => '$stateinfo.cityinfo'),
                      array('$match' => $match),
                      array('$project' => array('city_details'=>'$stateinfo.cityinfo')),
                      array('$limit' => 1)
                  );
        $res = $this->mongo_db->aggregate(MDB_CSC,$args);
        $city_details = isset($res['result'][0]['city_details']) ? $res['result'][0]['city_details']: array();
        if($new_state == $old_state){
            $state_index = commonfunction::get_collection_index($old_country,$old_state,$cityid,'state');
            $cityindex = commonfunction::get_collection_index($old_country,$old_state,$cityid,'city');
            $city_index = $cityindex['city_index'];
            $city_details['city_name'] = $post['city_name'];
            $city_details['zipcode'] = $post['zipcode'];
            $city_details['city_model_fare'] = (float)$post['city_model_fare'];

            $index_key = "stateinfo.".$state_index.".cityinfo.".$city_index;
            $city_array = array($index_key => $city_details);
            $result = $this->mongo_db->updateOne(MDB_CSC,$match,array('$set'=>$city_array),array('upsert'=>true));
        }else{
            /*$args = array(array('$unwind' => '$stateinfo'),
                    array('$unwind' => '$stateinfo.cityinfo'),
                    array('$sort' => array('stateinfo.cityinfo.city_id' => -1)),
                    array('$project' => array('id'=>'$stateinfo.cityinfo.city_id')),
                    array('$limit' => 1)
                );
            $rs = $this->mongo_db->aggregate(MDB_CSC,$args);
            $first_key = !empty($rs['result']) ? $rs['result'][0]['id'] : 0;
            $inc_id = $first_key+1;*/

            $city_details['city_id'] = (int)$cityid;
            $city_details['city_name'] = $post['city_name'];
            $city_details['zipcode'] = $post['zipcode'];
            $city_details['city_countryid'] = (int)$post['country_name'];
            $city_details['city_stateid'] = (int)$post['state_name'];
            $city_details['city_model_fare'] = (float)$post['city_model_fare'];
            $city_details['city_status'] = 'A';
            $city_details['default'] = 0;

            $state_index = $state_index = commonfunction::get_collection_index($new_country,$new_state,0,'state');
            $index_key = "stateinfo.".$state_index.".cityinfo";
            $city_array = array($index_key => $city_details );
            $mresult = $this->mongo_db->updateOne(MDB_CSC,array('_id'=> (int)$new_country,'stateinfo.state_id'=> (int)$new_state),array('$push'=>$city_array),array('upsert'=>true));

            $pull_city = $this->mongo_db->updateOne(MDB_CSC,array('_id' => (int)$old_country,'stateinfo.cityinfo.city_id' => (int)$cityid),
                                    array('$pull'  =>  array( 'stateinfo.$.cityinfo' =>  array('city_id' => (int)$cityid))));
        }
        return 1;
    }
    public function state_countrydetails($stateid)
    {
        //$country_id = (int)$countryid;
        $state_id = (int)$stateid;
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$match' => array('stateinfo.state_id'=> $state_id)),
            array('$project' => array('_id' => 0,
                'country_id' => '$_id',
                'country_name' => '$country_name',
                'state_id' => '$stateinfo.state_id',
                'country_name' => '$country_name',
                'state_name' => '$stateinfo.state_name',
                'state_countryid' => '$stateinfo.state_countryid',
            ))
        );
        $result = $this->mongo_db->aggregate(MDB_CSC,$ops);

        $data = array();
        if(count($result['result']) > 0){
            foreach($result['result'] as $val){

                $data[] = $val;
            }
        }

        return $data;

    }
    public function validate_editstate($arr, $uid)
    {
        //Array ( [country_name] => 3 [state_name] => stateabcd [state_countryid] => 3 )

        return Validation::factory($arr)->rule('state_name', 'not_empty')
        //->rule('state_name', 'alpha_dash')
            ->rule('state_name', 'Model_Edit::check_reg_state_name', array(
            ':value'
        ))->rule('state_name', 'min_length', array(
            ':value',
            '2'
        ))->rule('state_name', 'max_length', array(
            ':value',
            '30'
        ))->rule('state_name', 'Model_Edit::checkstatename', array(
            ':value',
            $arr['country_name'],
            $uid
        ))->rule('country_name', 'Model_Edit::checkstatecity', array(
            ':value',$arr['state_countryid'],$uid
        ))->rule('country_name', 'not_empty');
    }
    // Check if the state_name already exists in the database
    public static function checkstatename($name, $countryid, $uid)
    {
        $cid = (int)$uid;
        $sid = (int)$countryid;
        $mongodb = MangoDB::instance('default');
		// ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
		$options=[
			'projection'=>[
				'stateinfo.state_name'=>1,
				'stateinfo.state_id'=>1
				],
			'sort'=>[
				'stateinfo.state_id'=>-1
				 ]
			];
		$match = array( "\$and" => array( array('_id'=>array('$eq'=>$sid)),array('stateinfo.$.state_id'=>array('$ne'=>$cid))));
		$res = $mongodb->find(MDB_CSC,$match,$options);
		$statelist = $res[0]['stateinfo'];
		$match = 0;
		if(!empty($statelist)){
			foreach($statelist as $s){
				if((strtolower($name) == strtolower($s['state_name'])) && ($cid != $s['state_id']))
					$match = 1;
			}
		}
        return ($match == 1)?false:true;
    }
    public function editstate($stateid, $post)
    {
        //echo '<pre>';print_r($post);exit;
        $match = array('_id'=>(int)$post['state_countryid']);
        $args  = array(array('$unwind' => '$stateinfo'),
                    array('$match' => $match),
                    array('$project' => array('state_id' => '$stateinfo.state_id'))
                );
        $keys = $this->mongo_db->aggregate(MDB_CSC,$args);
        $update_arr = array();
        if($post['state_countryid'] != $post['country_name']){
            if(!empty($keys['result'])){
                $i=0;
                $delete = $this->mongo_db->updateOne(MDB_CSC,array( '_id' =>  (int)$post['state_countryid']),
                                            array('$pull' => array('stateinfo' => array('state_id' => (int)$stateid))));

                $args = array(array('$unwind' => '$stateinfo'),
                              array('$sort' => array('stateinfo.state_id' => -1)),
                              array('$project' => array('id'=>'$stateinfo.state_id')),
                              array('$limit' => 1)
                            );
                $rs = $this->mongo_db->aggregate(MDB_CSC,$args);
                $first_key = !empty($rs['result']) ? $rs['result'][0]['id'] : 0;
                $inc_id = $first_key+1;
                $match1 = array('_id'=>(int)$post['country_name']);

                $new_state = array("stateinfo"=>array('state_id'  => (int)$inc_id,
                                    'state_name'  => $post['state_name'],
                                    'state_countryid'  => (int)$post['country_name'],
                                    'state_status'  =>  'A',
                                    'default'  =>  0));
                $update = $this->mongo_db->updateOne(MDB_CSC,$match1,array('$push'=>$new_state),array('upsert' => true));
            }
        }else{
            if(!empty($keys['result'])){
                $i=0;
                foreach($keys['result'] as $k => $v ){
                    if($stateid == $v['state_id']){
                        $update_arr["stateinfo.".$i.".state_name"] = $post['state_name'];
                    }
                    $i++;
                }
                //echo '<pre>';print_r($update_arr);exit;
                $update = $this->mongo_db->updateOne(MDB_CSC,$match,array('$set'=>$update_arr),array('upsert' => true));
            }
        }
        return (empty($update->getwriteErrors())) ? 1 : $update->getwriteErrors();
    }
    public function validate_editmanager($arr, $uid)
    {
        return Validation::factory($arr)
        ->rule('firstname', 'not_empty')
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
        ->rule('email', 'not_empty')->rule('email', 'email')
        ->rule('email', 'max_length', array(
            ':value',
            '50'
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
        ))->rule('company_name', 'not_empty')
        //->rule('company_name', 'alpha_dash')
        //->rule('company_name', 'Model_Edit::checkmanagercompany', array(':value',$arr['city'],$arr['state'],$arr['country'],$uid))
        ->rule('address', 'not_empty')
        ->rule('country', 'not_empty')
        ->rule('state', 'not_empty')
        ->rule('city', 'not_empty');
    }
    public function validate_editadmin($arr, $uid)
    {
        return Validation::factory($arr)->rule('firstname', 'not_empty')->rule('firstname', 'min_length', array(
            ':value',
            '4'
        ))->rule('firstname', 'max_length', array(
            ':value',
            '30'
        ))->rule('lastname', 'not_empty')->rule('email', 'not_empty')->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '50'
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
        ))->rule('address', 'not_empty')->rule('country', 'not_empty');
    }
    //To update Edit Manager Functionalities
    public function edit_manager($post, $uid)
    {
        //MongoDB
        $array = array(
            'name' => $post['firstname'],
            'address' => $post['address'],
            'login_country' => (int)$post['country'],
            'login_state' => (int)$post['state'],
            'login_city' => (int)$post['city'],
            'lastname' => $post['lastname'],
            'email' => $post['email'],
            'phone' => $post['phone'],
            'company_id' => (int)$post['company_name']
        );
        $result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$uid),array('$set'=>$array),array('upsert'=>true));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    public function edit_admin($post, $uid)
    {
        //MongoDB
        $data = array(
            'name' => $post['firstname'],
            'address' => $post['address'],
            'login_country' => (int)$post['country'],
            'lastname' => $post['lastname'],
            'email' => $post['email'],
            'phone' => (int)$post['phone'],
            'updated_date' => $this->currentdate,
        );
        $result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$uid),array('$set'=>$data),array('upsert'=>true));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }

    public function validate_editassigntaxi($arr, $uid)
    {
        return Validation::factory($arr)->rule('company_name', 'not_empty')->rule('country', 'not_empty')
								->rule('state', 'not_empty')->rule('city', 'not_empty')
								->rule('enddate', 'not_empty')
								->rule('driver', 'not_empty')->rule('startdate', 'not_empty')
								->rule('driver', 'Model_Edit::checkassigntaxi', array(
									'driver',
									':value',
									$arr,
									$uid
								))->rule('taxi', 'not_empty')
								->rule('taxi', 'Model_Edit::checkassigntaxi', array(
									'taxi',
									':value',
									$arr,
									$uid
								));
    }
    
    public static function checkassigntaxi($driver_taxi, $driver_taxi_id, $post, $uid)
    {
        $country_id      = $post['country'];
        $state_id        = $post['state'];
        $city_id         = $post['city'];
        $company_name    = $post['company_name'];
        $driver_id       = $post['driver'];
        $taxi_id         = $post['taxi'];
        $startdate       = $post['startdate'];
        $enddate         = $post['enddate'];
        $match_query                     = array();
        $match_query['mapping.mapping_status'] = 'A';
        $match_query['mapping._id'] = array('$ne' =>(int)$uid);
        if ($driver_taxi == 'driver') {
            $match_query['mapping.mapping_driverid'] = (int)$driver_taxi_id;
        }
        if ($driver_taxi == 'taxi') {
            $match_query['mapping.mapping_taxiid'] = (int)$driver_taxi_id;
        }

        if ($startdate && $enddate) {
			
			$or = array('$or' => array(
					array('mapping.mapping_startdate' => 
						array('$gte' => Commonfunction::MongoDate(strtotime($startdate)), '$lte' => Commonfunction::MongoDate(strtotime($enddate)))),
					
					array('mapping.mapping_enddate' => 
						array('$gte' => Commonfunction::MongoDate(strtotime($startdate)), '$lte' => Commonfunction::MongoDate(strtotime($enddate)))),
						
					array('$and' => array(
									array('mapping.mapping_startdate' => array('$lte' => Commonfunction::MongoDate(strtotime($startdate)))),
									array('mapping.mapping_enddate' => array('$gte' => Commonfunction::MongoDate(strtotime($enddate))))
						)),						
					));
			$match = array('$and' => array($match_query, $or));
        }else{
			$match = $match_query;
        }
        //~ echo "<pre>"; print_r($match); exit;
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
                '$match' => $match
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
        $mongo_db        = MangoDB::instance('default');
        $result          = $mongo_db->aggregate(MDB_CSC, $arguments);        
        $count = (isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
        $response = ($count > 0) ? false : true;
        return $response;
    }

    public function assigntaxi_details($uid)
    {
        $result = $temp_arr = $match_query = array();
        $match_query['mapping._id'] = (int)$uid;
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
                    'mapping_companyid' => '$mapping.mapping_companyid',
                    'mapping_countryid' => '$mapping.mapping_countryid',
                    'mapping_stateid' => '$mapping.mapping_stateid',
                    'mapping_cityid' => '$mapping.mapping_cityid',
                    'mapping_driverid' => '$mapping.mapping_driverid',
                    'mapping_taxiid' => '$mapping.mapping_taxiid',
                    'mapping_startdate' => '$mapping.mapping_startdate',
                    'mapping_enddate' => '$mapping.mapping_enddate',
                )
            ),
        );
        $res    = $this->mongo_db->aggregate(MDB_CSC, $arguments);
        //echo "<pre>";print_r($res['result']); exit;
        if(!empty($res['result'])){
            $r = $res['result'][0];
            $temp_arr['mapping_companyid'] = $r['mapping_companyid'];
            $temp_arr['mapping_countryid'] = $r['mapping_countryid'];
            $temp_arr['mapping_stateid'] = $r['mapping_stateid'];
            $temp_arr['mapping_cityid'] = $r['mapping_cityid'];
            $temp_arr['mapping_driverid'] = $r['mapping_driverid'];
            $temp_arr['mapping_taxiid'] = $r['mapping_taxiid'];
            $temp_arr['mapping_startdate'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['mapping_startdate']);
            $temp_arr['mapping_enddate'] = commonfunction::convertphpdate('Y-m-d H:i:s',$r['mapping_enddate']);
            $result[] = $temp_arr;
        }
        return $result;
    }
    public function edit_assigntaxi($post, $uid)
    {
        $data      = array(
            'mapping_driverid' => (int)$post['driver'],
            'mapping_taxiid' => (int)$post['taxi'],
            'mapping_companyid' => (int)$post['company_name'],
            'mapping_countryid' => (int)$post['country'],
            'mapping_stateid' => (int)$post['state'],
            'mapping_cityid' => (int)$post['city'],
            'mapping_startdate' => Commonfunction::MongoDate(strtotime($post['startdate'])),
            'mapping_enddate' => Commonfunction::MongoDate(strtotime($post['enddate']))
        );
        $result = $this->mongo_db->updateOne(MDB_TAXI_DRIVER_MAPPING,array('_id'=>(int)$uid),array('$set'=>$data),array('upsert'=>false));
        if ($result) {
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_TAXI,
                        'localField' => 'mapping_taxiid',
                        'foreignField' => '_id',
                        'as' => 'taxi'
                    )
                ),
                array(
                    '$unwind' => '$taxi'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_MOTOR_MODEL,
                        'localField' => 'taxi.taxi_model',
                        'foreignField' => '_id',
                        'as' => 'motor_model'
                    )
                ),
                array(
                    '$unwind' => '$motor_model'
                ),
                array(
                    '$lookup' => array(
                        'from' => MDB_PEOPLE,
                        'localField' => 'mapping_driverid',
                        'foreignField' => '_id',
                        'as' => 'people'
                    )
                ),
                array(
                    '$unwind' => '$people'
                ),
                array(
                    '$match' => array('people._id' => (int)$post['driver'] )
                ),
                array(
                    '$project' => array(
                        'taxi_no' => '$taxi.taxi_no',
                        'name' => '$people.name',
                        'email' => '$people.email',
                        'model_name' => '$motor_model.model_name',
                        'taxi_speed' => '$taxi.taxi_speed',
                        'max_luggage' => '$taxi.max_luggage'
                    )
                ),
            );
            $result          = $this->mongo_db->aggregate(MDB_TAXI_DRIVER_MAPPING, $arguments);
            $resultquery[] = (!empty($result['result']) && isset($result['result'])) ? $result['result'][0]: 0;
            return $resultquery;
        } else {
            return 0;
        }
    }
    public function validate_unavailabledriver($arr, $uid)
    {
        return Validation::factory($arr)->rule('reason', 'not_empty')->rule('startdate', 'not_empty')->rule('enddate', 'not_empty')->rule('enddate', 'Model_Manage::date_diff', array(
            'value',
            $arr['startdate']
        ))->rule('enddate', 'Model_Edit::checkunavailable', array(
            ':value',
            $arr,
            $uid
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
    //Validate the edit menu
    public function validate_editmenu($arr,$mid)
    {
        return Validation::factory($arr)
            ->rule('menu_name', 'not_empty')
            ->rule('menu_name', 'min_length', array(':value', '2'))
            ->rule('menu_name', 'max_length', array(':value', '30'))
            ->rule('menu_name', 'Model_Edit::menu_name_exits', array(
						$mid,$arr['slug']
					))
            ->rule('slug','not_empty');
    }
    //selected manu
    public function get_menu($mid)
    {
        $ops = array(
                    array('$match'=>array('_id'=>(int)$mid)),
                    array(
                        '$project' => array(
                        '_id' => '$_id',
                        'menu_name' => '$menu_name',
                        'menu_link' => '$menu_link',
                        'status_post' => '$status_post',
                        )
                    ),
                    array(
                        '$sort' => array("_id"=>-1)
                    ),
            );
        $result = $this->mongo_db->aggregate(MDB_CMS,$ops);
        //print_r(($result));exit;
        return $result['result'];
    }
    public function update_menu($mid, $post)
    {
        $status = $post['status_posts'];
        if ($status == 'Publish') {
            $status = 'P';
        } else if ($status == 'Unpublish') {
            $status = 'U';
        }
        $cms_data      = array(
            'menu_name' => $post['menu_name'],
            'menu_link' => trim($post['slug'],'-'),
            'status_post' => $status,
        );
        $cms_result = $this->mongo_db->updateOne(MDB_CMS,array('_id'=>(int)$mid),array('$set'=>$cms_data),array('upsert'=>false));
        return (count($cms_result))?1:0;
    }
    //Check the menu already exists- done
    public static function menu_name_exits($mid, $slug)
    {
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_CMS,array('_id'=>array('$ne'=>(int)$mid), "menu_link"=> trim($slug,'-')));
        
        return ($result>0)?false:true;
    }
    //String Convert the URL format
    function string_convertoUrl($str_val)
    {
        // small fonts
        $strurl = strtolower($str_val);
        // change spaces to -
        $strurl = str_replace(' ', '-', $strurl);
        // delete all other characters to -
        $strurl = preg_replace('|[^0-9a-z\-\/+]|', '', $strurl);
        // delete too much - if near
        $strurl = preg_replace('/[\-]+/', '-', $strurl);
        // trim -
        $strurl = trim($strurl, '-');
        return $strurl;
    }
    //Validate the edit menu
    public function validate_editmile($arr, $mid)
    {
        return Validation::factory($arr)->rule('mile', 'not_empty')->rule('mile', 'digit')->rule('mile', 'max_length', array(
            ':value',
            '30'
        ));
    }
    public static function check_reg_countryname($name)
    {
        if (preg_match('/^[A-Za-z ]+$/', $name)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_reg_state_name($name)
    {
        if (preg_match('/^[A-Za-z ]+$/', $name)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_reg_city_name($name)
    {
        if (preg_match('/^[A-Za-z ]+$/', $name)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_base_fare($base_fare)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $base_fare)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_fare_zero($base_fare)
    {
        if ($base_fare == 0) {
            return false;
        } else {
            return true;
        }
    }
    public static function check_min_km($min_km)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $min_km)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_below_and_above_km($below_and_above_km, $min_km)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $below_and_above_km) && $below_and_above_km > $min_km) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_min_fare($min_fare)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $min_fare)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_cancellation_fare($cancellation_fare)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $cancellation_fare)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_minute_fare($minute_fare)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $minute_fare)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_below_km($below_km)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $below_km)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_above_km($above_km)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $above_km)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_waiting_time($waiting_time)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $waiting_time)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_night_fare($night_fare)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $night_fare)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_booking_limit($night_fare)
    {
        if (preg_match('/^\d+(\\d+)*$/', $night_fare)) {
            return true;
        } else {
            return false;
        }
    }
    public function get_faqdetails($fid)
    {
        $match = array('_id' => (int)$fid);
        $args = array(array('$match' => $match),
                        array('$project' => array('faq_id'=>'$_id',
                                                  'faq_title'=>'$faq_title',
                                                  'faq_details'=>'$faq_details'
                                                  ))
                  );
        $result = $this->mongo_db->Aggregate(MDB_PASSENGERS_FAQ,$args);
        return (isset($result['result'])) ? $result['result'] : array();
    }
    public function validate_editfaq($arr, $fid)
    {
        return Validation::factory($arr)->rule('faq_title', 'not_empty')->rule('faq_title', 'Model_Edit::checkfaqtitle', array(
            ':value',
            $fid
        ))->rule('faq_details', 'not_empty');
    }
    public function editfaq($post, $fid)
    {
        $update_arr = array(
                            'faq_title' => $post['faq_title'],
                            'faq_details' => $post['faq_details']
                        );
        $result = $this->mongo_db->updateOne(MDB_PASSENGERS_FAQ,array('_id' => (int)$fid),array('$set'=>$update_arr),array('upsert'=>true));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    // To Check Currency code is equal to Currency symbol
    public static function checksite_currency($currencysymbol, $currencycode)
    {
        // To Check Currency code is equal to Currency symbol
        $mongo_db = MangoDB::instance('default');
        $match = array('currency_code' => $currencycode,'currency_symbol' => $currencysymbol);
        $result = $mongo_db->findOne(MDB_CSC,$match,array('country_id'));
        return (count($result)>0) ? true : false ;
    }
    public function get_company_payment_settings($company_user_id){ }
    public function get_promocodedetails($id)
    {
        $result = array();
        $query = $this->mongo_db->findOne(MDB_PASSENGERS_PROMO,array('_id' => (int)$id),array('_id', 'promocode', 'promo_discount', 'start_date', 'expire_date', 'promo_limit','promo_used_details'));
        if(!empty($query)){
            $arr = $query;
            $arr['start_date'] = isset($query['start_date'])?commonfunction::convertphpdate('Y-m-d H:i:s',$query['start_date']):"";
            $arr['expire_date'] = isset($query['expire_date'])?commonfunction::convertphpdate('Y-m-d H:i:s',$query['expire_date']):"";
            $result[] = $arr;
        }
        //rint_r($result); exit;

        return $result;
    }
    public function validate_editpromocode($arr, $id)
    {
        $validation = Validation::factory($arr)->rule('promo_limit', 'not_empty')
        //->rule('model_name', 'alpha_dash')
            ->rule('promo_limit', 'numeric');
        return $validation;
    }
    public function get_promocode_users($promocode)
    {
        $arguments = array(
            array('$lookup' => array(
                'from'=>MDB_PASSENGERS,
                'localField'=> "passenger_id",
                'foreignField' => "_id",
                'as'=> "passenger"
                )
            ),
            array('$match'=>array('passenger.user_status'=>"A", 'promocode'=>$promocode)),
            array('$unwind'=>'$passenger'),
            array('$project' => array(
                    'promocode' => '$promocode',
                    'name'=>'$passenger.name',
                    'email'=>'$passenger.email',
                )
            )
        );
        $results = $this->mongo_db->aggregate(MDB_PASSENGERS_PROMO,$arguments);
        return (!empty($results['result']))?$results['result']:array();
    }
    public function editpromocode($post, $promocode)
    {
        $data     = array(
            'start_date' => Commonfunction::MongoDate(strtotime($post['start_date'])),
            'expire_date' => Commonfunction::MongoDate(strtotime($post['expire_date'])),
            'promo_limit' => $post['promo_limit']
        );
        //print_r($promocode);exit;
        $result = $this->mongo_db->updateMany(MDB_PASSENGERS_PROMO,array('promocode'=>$promocode),array('$set'=>$data));
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    // Check driver licence Id is Already Exist or Not
    public static function checklicenceId($value, $uid)
    {
        $mongodb        = MangoDB::instance('default');
        $arguments = array(
            array('$match'=>array('_id'=>array('$ne' => (int)$uid), 'driver_license_id'=> $value)),
            array('$project' => array('pid' => '$_id')),
            array('$group'=>array('_id' => NULL,'count' => array('$sum' => 1 ))),
        );
        $results = $mongodb->aggregate(MDB_PEOPLE,$arguments);
        //print_r($results['result'][0]['count']);exit;
        return (isset($results['result'][0]['count']) && $results['result'][0]['count'] > 0)?false:true;

    }
    //pco licence number already exist
    public static function checkpcolicenceNo($value, $uid)
    {
        $mongodb        = MangoDB::instance('default');
        $arguments = array(
            array('$lookup' => array(
                'from'=>MDB_DRIVER_INFO,
                'localField'=> "_id",
                'foreignField' => "_id",
                'as'=> "driver_info"
                )
            ),
            array('$match'=>array('_id'=>array('$ne' => (int)$uid), 'driver_info.driverinfo.driver_pco_license_number'=> $value)),
            array('$project' => array('pid' => '$_id')),
            array('$group'=>array('_id' => NULL,'count' => array('$sum' => 1 ))),
        );
        $results = $mongodb->aggregate(MDB_PEOPLE,$arguments);
        //print_r($results['result']);exit;
        return (isset($results['result'][0]['count']) && $results['result'][0]['count'] > 0)?false:true;
    }
    //insurance number already exist
    public static function checkinsuranceNo($value, $uid)
    {
        $mongodb        = MangoDB::instance('default');
        //MongoDB
        $arguments = array(
            array('$lookup' => array(
                'from'=>MDB_DRIVER_INFO,
                'localField'=> "_id",
                'foreignField' => "_id",
                'as'=> "driver_info"
                )
            ),
            array('$match'=>array('_id'=>array('$ne' => (int)$uid), 'driver_info.driverinfo.driver_insurance_number'=> $value)),
            array('$project' => array('pid' => '$_id')),
            array('$group'=>array('_id' => NULL,'count' => array('$sum' => 1 ))),
        );
        $results = $mongodb->aggregate(MDB_PEOPLE,$arguments);
        //print_r($results['result']);exit;
        return (isset($results['result'][0]['count']) && $results['result'][0]['count'] > 0)?false:true;
    }
    //national insurance number already exist
    public static function checkNationalinsuranceNo($value, $uid)
    {
        $mongodb        = MangoDB::instance('default');
        $arguments = array(
            array('$lookup' => array(
                'from'=>MDB_DRIVER_INFO,
                'localField'=> "_id",
                'foreignField' => "_id",
                'as'=> "driver_info"
                )
            ),
            array('$match'=>array('_id'=>array('$ne' => (int)$uid), 'driver_info.driverinfo.driver_national_insurance_number'=> $value)),
            array('$project' => array('pid' => '$_id')),
            array('$group'=>array('_id' => NULL,'count' => array('$sum' => 1 ))),
        );
        $results = $mongodb->aggregate(MDB_PEOPLE,$arguments);
        //print_r($results['result']);exit;
        return (isset($results['result'][0]['count']) && $results['result'][0]['count'] > 0)?false:true;
    }
    // To Check taxi insurance number is Already Available or Not
    public static function check_taxinsurance_number($number, $uid)
    {
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_TAXI,array('taxi_insurance_number'=>$number,'_id'=>array('$ne'=>(int)$uid)),array('taxi_insurance_number'));
        return ($result>0)?false:true;
    }
    // To Check taxi pco licence number is Already Available or Not
    public static function check_taxipco_number($number, $uid)
    {
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_TAXI,array('taxi_pco_licence_number'=>$number,'_id'=>array('$ne'=>(int)$uid)),array('taxi_pco_licence_number'));
        return ($result>0)?false:true;
    }
    //To Get all payment modules
    public function payment_modules()
    {
        //$result = $this->mongo_db->find(MDB_PAYMENT_MODULES,array(),array());
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[];
                $result = $this->mongo_db->find(MDB_PAYMENT_MODULES,[],$options);
        return (!empty($result))?$result:array();
    }

    public function check_peoplecompanyid($id)
    {
        $result = $this->mongo_db->find(MDB_PEOPLE,array('_id'=>(int)$id));
        return (!empty($result))?$result:0;
    }

    // Check Whether Phone is Already Exist or Not
    public static function check_passengerphone($phone = "", $uid)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PASSENGERS,array('phone'=>$phone,'_id'=>array('$ne'=>(int)$uid)));
        //print_r($result);exit;
        return ($result > 0)?false:true;
    }

    public function driver_details_edit($id){

        $result = $temp_arr = array();
        $args = array(array('$lookup' => array(
                                        'from' => MDB_DRIVER_INFO,
                                        'localField' => '_id',
                                        'foreignField' => '_id',
                                        'as' => 'driver')),
                    array('$unwind' => array('path' =>  '$driver', 'preserveNullAndEmptyArrays' =>  true)),
                    array('$lookup' => array(
                                        'from' => MDB_COMPANY,
                                        'localField' => 'company_id',
                                        'foreignField' => '_id',
                                        'as' => 'company')),
                    array('$unwind' => array('path' =>  '$company', 'preserveNullAndEmptyArrays' =>  true)),
                    array('$match' => array('_id' => (int)$id,'user_type' => 'D')),
                    array('$project' => array(
                                        'id' => '$_id','rating' => '$rating','name' => '$name','lastname' => '$lastname',
                                        'email' => '$email','password' => '$password','org_password' => '$org_password',
                                        'gender' => '$gender','dob' => '$dob','driver_license_id' => '$driver_license_id',
                                        'phone' => '$phone','login_country' => '$login_country','login_state' => '$login_state',
                                        'login_city' => '$login_city','company_id' => '$company_id','booking_limit' => '$booking_limit',
                                        'address' => '$address','profile_picture' => '$profile_picture','user_type' => '$user_type',
                                        'company_brand_type' => '$company.companyinfo.company_brand_type',
                                        'driver_license_expire_date' => '$driver.driverinfo.driver_license_expire_date',
                                        'driver_pco_license_number' => '$driver.driverinfo.driver_pco_license_number',
                                        'driver_pco_license_expire_date' => '$driver.driverinfo.driver_pco_license_expire_date',
                                        'driver_insurance_number' => '$driver.driverinfo.driver_insurance_number',
                                        'driver_insurance_expire_date' => '$driver.driverinfo.driver_insurance_expire_date',
                                        'driver_pco_license_expire_date' => '$driver.driverinfo.driver_pco_license_expire_date',
                                        'driver_national_insurance_number' => '$driver.driverinfo.driver_national_insurance_number',
                                        'driver_national_insurance_expire_date' => '$driver.driverinfo.driver_national_insurance_expire_date'))
                );
        $res = $this->mongo_db->aggregate(MDB_PEOPLE,$args);
        //echo "<pre>"; print_r($res['result']); exit;

        if(!empty($res['result'])){

            $r = $res['result'][0];
            //echo '<pre>';print_r($r);exit;
            $temp_arr['id'] = isset($r['id']) ? $r['id']: '';
            $temp_arr['name'] = isset($r['name']) ? $r['name']: '';
            $temp_arr['email'] = isset($r['email']) ? $r['email']: '';
            $temp_arr['lastname'] = isset($r['lastname']) ? $r['lastname']: '';
            $temp_arr['password'] = isset($r['password']) ? $r['password']: '';
            $temp_arr['user_type'] = isset($r['user_type']) ? $r['user_type']: '';
            $temp_arr['org_password'] = isset($r['org_password']) ? $r['org_password']: '';
            $temp_arr['gender'] = isset($r['gender']) ? $r['gender']: '';
            $temp_arr['phone'] = isset($r['phone']) ? $r['phone']: '';
            $temp_arr['login_country'] = isset($r['login_country']) ? $r['login_country']: '';
            $temp_arr['login_state'] = isset($r['login_state']) ? $r['login_state']: '';
            $temp_arr['login_city'] = isset($r['login_city']) ? $r['login_city']: '';
            $temp_arr['company_id'] = isset($r['company_id']) ? $r['company_id']: '';
            $temp_arr['booking_limit'] = isset($r['booking_limit']) ? $r['booking_limit']: '';
            $temp_arr['address'] = isset($r['address']) ? $r['address']: '';
            $temp_arr['company_brand_type'] = isset($r['company_brand_type']) ? $r['company_brand_type']: '';
            $temp_arr['profile_picture'] = isset($r['profile_picture']) ? $r['profile_picture']: '';
            $temp_arr['driver_license_id'] = isset($r['driver_license_id']) ? $r['driver_license_id']: '';
            $temp_arr['current_location'] = isset($r['current_location']) ? $r['current_location']: '';
            $temp_arr['drop_location'] = isset($r['drop_location']) ? $r['drop_location']: '';
            $temp_arr['no_passengers'] = isset($r['no_passengers']) ? $r['no_passengers']: '';
            $temp_arr['dob'] = isset($r['dob']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['dob']): '';
            $temp_arr['pickup_time'] = isset($r['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']): '';
            $temp_arr['waitingtime'] = isset($r['waitingtime']) ? $r['waitingtime']: '';
            $temp_arr['driver_pco_license_number'] = isset($r['driver_pco_license_number']) ? $r['driver_pco_license_number'][0]: '';
            $temp_arr['driver_insurance_number'] = isset($r['driver_insurance_number']) ? $r['driver_insurance_number'][0]: '';
            $temp_arr['driver_national_insurance_number'] = isset($r['driver_national_insurance_number']) ? $r['driver_national_insurance_number'][0]: '';

            $temp_arr['driver_license_expire_date'] = isset($r['driver_license_expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['driver_license_expire_date'][0]): '';
            $temp_arr['driver_pco_license_expire_date'] = isset($r['driver_pco_license_expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['driver_pco_license_expire_date'][0]): '';
            $temp_arr['driver_insurance_expire_date'] = isset($r['driver_insurance_expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['driver_insurance_expire_date'][0]): '';
            $temp_arr['driver_national_insurance_expire_date'] = isset($r['driver_national_insurance_expire_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['driver_national_insurance_expire_date'][0]): '';
            //echo '<pre>';print_r($temp_arr);exit;
            $result[] = $temp_arr;
        }
        return $result;
    }

    public static function checkstatecity($new_country, $old_country, $uid)
    {
        $mongodb = MangoDB::instance('default');
        $return = true;
        if($new_country != $old_country){

            $match = array('_id' => (int)$old_country,'stateinfo.state_id' => (int)$uid);
            $args = array(  array('$unwind' => '$stateinfo'),
                            array('$match' => $match),
                            array('$project' => array('cityinfo' => '$stateinfo.cityinfo'))
                        );
            $res = $mongodb->Aggregate(MDB_CSC, $args);
            $cityinfo = isset($res['result'][0]['cityinfo']) ? $res['result'][0]['cityinfo'] : array();
            $return = !empty($cityinfo) ? false : true;
        }
        return $return;
    }
    
    public function popularplace_details($city_id){
		
		$result = $temp_arr = array();
		$args = array(
					array('$match' => array('city_id' => (int)$city_id)),
					array('$project' => array('city_id' => '$city_id','city_name'=>'$city_name','label_name'=>'$label_name',
											'location_icon'=>'$location_icon','location_name'=>'$location_name','loc'=>'$loc.coordinates')),
					array('$sort' => array('_id.city_id' => 1))
				);
		$res = $this->mongo_db->aggregate(MDB_POPULAR_PLACES, $args);
		
		if(!empty($res['result'])){
			foreach($res['result'] as $r){					
				$temp_arr['location_name'] = isset($r['location_name']) ? $r['location_name']: '';							
				$temp_arr['location_icon'] = isset($r['location_icon']) ? $r['location_icon']: '';							
				$temp_arr['label_name'] = isset($r['label_name']) ? $r['label_name']: '';							
				$temp_arr['latitude'] = isset($r['loc'][1]) ? $r['loc'][1]: '';							
				$temp_arr['longitude'] = isset($r['loc'][0]) ? $r['loc'][0]: '';							
				$temp_arr['_id'] = isset($r['_id']) ? $r['_id']: '';							
				$result[] = $temp_arr;
			}				
		}
		return $result;	
	}
	
	public function validate_editpopularplace($arr)
    {
        return Validation::factory($arr)
			->rule('city_name', 'not_empty')
			->rule('label_name', 'not_empty')
			->rule('location_name', 'not_empty');		
    }
    
	public function editpopularplace($id, $post)
    {		
		$result = 0;
		//~ echo '<pre>';print_r($post);exit;
		$city = explode('|',$post['city_name']);
		$city_id = $city[0];
		$city_name = $city[1];
		if(isset($post['edit_popular'])){
			foreach($post['edit_popular'] as $p){				
				$edit = explode('|',$p);
				$arr_id = $edit[0];
				$edit_id = $edit[1];
				$update_arr = array(
					'label_name' => $post['label_name'][$arr_id],
					'location_name' => $post['location_name'][$arr_id],
					'location_icon' => $post['location_icon'][$arr_id],
					'loc'=>array('type' => 'Point', 'coordinates'=>array((double)$post['longitude'][$arr_id],
																		(double)$post['latitude'][$arr_id]))
				);
				$res = $this->mongo_db->updateOne(MDB_POPULAR_PLACES,array('_id'=>(int)$edit_id),array('$set'=>$update_arr),array('upsert'=>false));
				$result=1;
			}			
		}
		
		$count = $post['count'];
		$old_count = $post['old_count'];
		$old_city = $post['old_city'];
		if($city_id != $old_city){
			$remove = $this->mongo_db->deleteMany(MDB_POPULAR_PLACES,array('city_id' => (int)$old_city));
		}
		
		# new locations insert		
		if($count > $old_count){
			for($i = $old_count;$i <= $count;$i++){
				if(isset($post['label_name'][$i]) && $post['label_name'][$i] != ''){
					$options=['projection'=>[ '_id'=>1 ],
						'sort'=>[ '_id'=>-1 ],
						'limit'=>1 ];
					$rs = $this->mongo_db->find(MDB_POPULAR_PLACES,[],$options);
					$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
					reset($res);
					$first_key = key($res);
					$inc_id = $first_key+1;	
					$insert_data = array(
						'_id' => (int)$inc_id,
						'city_id'=> (int)$city_id,
						'city_name' => $city_name,
						'label_name' => $post['label_name'][$i],
						'location_icon' => $post['location_icon'][$i],
						'location_name' => $post['location_name'][$i],
						'loc'=>array('type' => 'Point', 'coordinates'=>array((double)$post['longitude'][$i],
																			(double)$post['latitude'][$i]))
					);
					$result = $this->mongo_db->insertOne(MDB_POPULAR_PLACES,$insert_data);
					$result=1;
				}
			}
		}
        return $result;	
    }
    
    public function remove_popularplace($remove_id,$cityid=''){
		
		if($remove_id != 'all'){
			$remove = $this->mongo_db->deleteMany(MDB_POPULAR_PLACES,array('_id' => (int)$remove_id));
		}else{
			$remove = $this->mongo_db->deleteMany(MDB_POPULAR_PLACES,array('city_id' => (int)$cityid));
		}
		return 1;
	}
	
	public function email_template($id)
    {
		$result = array();
        $res = $this->mongo_db->findOne(MDB_EMAIL_TEMPLATES, array('_id' => (int)$id),array("_id","email_title", "subject","description","language","status"));
		if(!empty($res)){
            $temp_arr['email_id'] = isset($res['_id'])?$res['_id']:'';
            $temp_arr['email_title'] = isset($res['sms_title'])?$res['email_title']:'';
            $temp_arr['subject'] = isset($res['subject'])?$res['subject']:'';
            $temp_arr['description'] = isset($res['description'])?$res['description']:'';
            $temp_arr['language'] = isset($res['language'])?$res['language']:'';
            $temp_arr['status'] = isset($res['status'])?$res['status']:'';

            $result[] = $temp_arr;
        }
        return $result;
    }
}
