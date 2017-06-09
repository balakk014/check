<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Add model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityAdd extends Model
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
        $this->username = $this->session->get("username");
        $this->admin_username = $this->session->get("username");
        $this->admin_userid = $this->session->get("userid");
        //MongoDB Instance
        $this->mongo_db        = MangoDB::instance('default');
    }
    /**Validating for Add company**/
    public function validate_addcompany($arr,$files_value_array)
    {
        return Validation::factory($arr)
            ->rule('firstname', 'not_empty')
            ->rule('firstname', 'min_length', array(':value', '4'))
            ->rule('firstname', 'max_length', array(':value', '30'))
            ->rule('lastname', 'not_empty')
            ->rule('lastname', 'min_length', array(':value', '1'))
            ->rule('email', 'not_empty')
            ->rule('email', 'email')
            ->rule('email', 'max_length', array(':value', '50'))
            ->rule('email', 'Model_Add::checkemail', array(':value'))
            ->rule('password', 'not_empty')
            ->rule('password', 'min_length', array(':value', '4'))
            ->rule('password', 'max_length', array(':value', '20'))
            ->rule('password','valid_password',array(':value','/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'))
            ->rule('repassword', 'not_empty')
            ->rule('repassword', 'min_length', array(':value', '4'))
            ->rule('repassword', 'max_length', array(':value', '20'))
            ->rule('repassword','valid_password',array(':value','/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'))
            ->rule('repassword',  'matches', array(':validation', 'password', 'repassword'))
            ->rule('phone', 'not_empty')
            ->rule('phone', 'min_length', array(':value', '7'))
            ->rule('phone', 'max_length', array(':value', '20'))
            ->rule('phone', 'contact_phone', array(':value'))
            ->rule('phone', 'Model_Add::checkphone', array(':value'))
            ->rule('company_name', 'not_empty')
            ->rule('company_name', 'min_length', array(':value', '4'))
            ->rule('company_name', 'max_length', array(':value', '30'))
            ->rule('company_name', 'Model_Add::checkcompany', array(':value',$arr['country'],$arr['state'],$arr['city']))
            ->rule('address', 'not_empty')
            ->rule('country', 'not_empty')
            ->rule('state', 'not_empty')
            ->rule('city', 'not_empty')
            ->rule('currency_code', 'not_empty')
            ->rule('currency_symbol', 'not_empty')
            ->rule('company_image', 'Upload::not_empty',array($files_value_array['company_image']))
            ->rule('company_image', 'Upload::valid',array($files_value_array['company_image']))
            ->rule('company_image', 'Upload::size', array($arr['company_image'],IMG_MAX_SIZE))
            ->rule('time_zone', 'not_empty');
    }
    /**Validating for Add Taxi**/
    public function validate_addtaxi($arr, $files_value_array = "")
    {
        $rule = Validation::factory($arr)
        ->rule('taxi_no', 'not_empty')
        ->rule('taxi_no', 'min_length', array(
            ':value',
            '4'
        ))->rule('taxi_no', 'max_length', array(
            ':value',
            '30'
        ))
        ->rule('taxi_no', 'regex', array(
            ':value',
            '/^[a-z0-9A-Z -]++$/iD'
        ))->rule('taxi_no', 'Model_Add::check_taxino', array(
            ':value'
        ))->rule('taxi_type', 'not_empty')
            ->rule('taxi_model', 'not_empty')
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
            ->rule('taxi_insurance_number', 'Model_Add::check_taxinsurance_number', array(
            ':value'
        ))->rule('taxi_insurance_expire_date', 'not_empty')
            ->rule('taxi_pco_licence_number', 'not_empty')
            ->rule('taxi_pco_licence_number', 'Model_Add::check_taxipco_number', array(
            ':value'
        ))->rule('taxi_pco_licence_expire_date', 'not_empty')
            ->rule('country', 'not_empty')
            ->rule('state', 'not_empty')->rule('city', 'not_empty')
            ->rule('company_name', 'not_empty')
            ->rule('taxi_min_speed', 'not_empty')
            ->rule('taxi_speed', 'not_empty')
            ->rule('taxi_speed', 'numeric')
            ->rule('taxi_speed', 'max_length', array(':value', '6'))
            ->rule('taxi_speed', 'Model_Add::check_taxi_speed_val', array(':value'))
            ->rule('taxi_fare_km', 'not_empty')
            ->rule('taxi_fare_km', 'min_length', array(
            ':value',
            '1'
        ))->rule('taxi_fare_km', 'max_length', array(
            ':value',
            '20'
        ))
        ->rule('taxi_image', 'Upload::size', array($arr['taxi_image'],IMG_MAX_SIZE))
        ->rule('taxi_fare_km', 'digit', array(
            ':value',
            '/^[0-9]{1,}/'
        ));
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
 
    /**Validating for Add Motor**/
    public function validate_addmodel($arr)
    {
        $validation = Validation::factory($arr)->rule('model_name', 'not_empty')
            ->rule('model_name', 'min_length', array(
            ':value',
            '2'
        ))->rule('model_name', 'max_length', array(
            ':value',
            '30'
        ))->rule('model_name', 'Model_Add::checkmodelname', array(
            ':value',
            $arr['companyname']
        ))->rule('model_size', 'not_empty')->rule('model_size', 'Model_Edit::check_fare_zero', array(
            ':value',
            $arr['model_size']
        ))->rule('companyname', 'not_empty')->rule('waiting_time', 'not_empty')->rule('waiting_time', 'Model_Add::check_waiting_time', array(
            ':value',
            $arr['waiting_time']
        ))->rule('base_fare', 'not_empty')->rule('base_fare', 'Model_Add::check_base_fare', array(
            ':value',
            $arr['base_fare']
        ))->rule('min_km', 'not_empty')->rule('min_km', 'Model_Add::check_min_km', array(
            ':value',
            $arr['min_km']
        ))->rule('min_fare', 'not_empty')->rule('min_fare', 'Model_Add::check_min_fare', array(
            ':value',
            $arr['min_fare']
        ))->rule('cancellation_fare', 'not_empty')->rule('cancellation_fare', 'Model_Add::check_cancellation_fare', array(
            ':value',
            $arr['cancellation_fare']
        ))->rule('minutes_fare', 'not_empty')->rule('minutes_fare', 'Model_Edit::check_minute_fare', array(
            ':value',
            $arr['minutes_fare']
        ))->rule('below_and_above_km', 'not_empty')->rule('below_and_above_km', 'Model_Add::check_below_and_above_km', array(
            ':value',
            $arr['min_km']
        ))->rule('below_km', 'not_empty')->rule('below_km', 'Model_Add::check_below_km', array(
            ':value',
            $arr['below_km']
        ))->rule('above_km', 'not_empty')->rule('above_km', 'Model_Add::check_above_km', array(
            ':value',
            $arr['above_km']
        ))->rule('night_charge', 'not_empty');
        if (Arr::get($arr, 'night_charge') == 1) {
            //echo "dsf";exit;
            $validation->rule('night_timing_from', 'not_empty')->rule('night_timing_to', 'not_empty')->rule('night_fare', 'not_empty')->rule('night_fare', 'Model_Add::check_night_fare', array(
                ':value',
                $arr['night_fare']
            ));
        }
        return $validation;
    }
    /**Validating for Add Motor**/
    public function validate_addfare($arr)
    {
        $validation = Validation::factory($arr)->rule('model_name', 'not_empty')->rule('model_size', 'not_empty')->rule('model_size', 'Model_Edit::check_fare_zero', array(
            ':value',
            $arr['model_size']
        ))->rule('waiting_time', 'not_empty')->rule('waiting_time', 'Model_Add::check_waiting_time', array(
            ':value',
            $arr['waiting_time']
        ))->rule('base_fare', 'not_empty')->rule('base_fare', 'Model_Add::check_base_fare', array(
            ':value',
            $arr['base_fare']
        ))->rule('min_km', 'not_empty')->rule('min_km', 'Model_Add::check_min_km', array(
            ':value',
            $arr['min_km']
        ))->rule('min_fare', 'not_empty')->rule('min_fare', 'Model_Add::check_min_fare', array(
            ':value',
            $arr['min_fare']
        ))->rule('cancellation_fare', 'not_empty')->rule('cancellation_fare', 'Model_Add::check_cancellation_fare', array(
            ':value',
            $arr['cancellation_fare']
        ))->rule('below_and_above_km', 'not_empty')->rule('below_and_above_km', 'Model_Add::check_below_and_above_km', array(
            ':value',
            $arr['min_km']
        ))->rule('below_and_above_km', 'Model_Add::check_value_zero', array(
            ':value',
            $arr['below_and_above_km']
        ))->rule('below_km', 'not_empty')->rule('below_km', 'Model_Add::check_below_km', array(
            ':value',
            $arr['below_km']
        ))->rule('above_km', 'not_empty')->rule('above_km', 'Model_Add::check_above_km', array(
            ':value',
            $arr['above_km']
        ))->rule('minutes_fare', 'not_empty')->rule('minutes_fare', 'Model_Add::check_minute_fare', array(
            ':value',
            $arr['minutes_fare']
        ))->rule('night_charge', 'not_empty')->rule('evening_charge', 'not_empty');
        if (Arr::get($arr, 'night_charge') == 1) {
            $validation->rule('night_timing_from', 'not_empty')->rule('night_timing_to', 'not_empty')->rule('night_fare', 'not_empty')->rule('night_fare', 'Model_Add::check_night_fare', array(
                ':value',
                $arr['night_fare']
            ))->rule('night_fare', 'Model_Admin::check_percentage', array(
                ':value'
            ));
        }
        if (Arr::get($arr, 'evening_charge') == 1) {
            $validation->rule('evening_timing_from', 'not_empty')->rule('evening_timing_to', 'not_empty')->rule('evening_fare', 'not_empty')->rule('evening_fare', 'Model_Add::check_evening_fare', array(
                ':value',
                $arr['evening_fare']
            ))->rule('evening_fare', 'Model_Admin::check_percentage', array(
                ':value'
            ));
        }
        return $validation;
    }
    /**Validating for Add company**/
    public function validate_adddriver($arr)
    {
        $validate = Validation::factory($arr)
            ->rule('firstname', 'not_empty')
            ->rule('firstname', 'min_length', array(':value', '4'))
            ->rule('firstname', 'max_length', array(':value', '30'))
            ->rule('lastname', 'not_empty')
            ->rule('dob', 'not_empty')
            ->rule('phone', 'not_empty')
            ->rule('phone', 'contact_phone', array(':value'))
            ->rule('phone', 'Model_Add::checkphone', array(':value'))
            ->rule('email', 'not_empty')
            ->rule('email', 'email')
            ->rule('email', 'max_length', array(':value', '50'))
            ->rule('email', 'Model_Add::checkemail', array(':value'))
            ->rule('password', 'not_empty')
            ->rule('password', 'min_length', array(':value', '6'))
            ->rule('password', 'max_length', array(':value', '20'))
            ->rule('password','valid_password',array(':value','/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'))
            ->rule('repassword', 'not_empty')
            ->rule('repassword', 'min_length', array(':value', '6'))
            ->rule('repassword', 'max_length', array(':value', '20'))
            ->rule('repassword','valid_password',array(':value','/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'))
            ->rule('repassword',  'matches', array(':validation', 'password', 'repassword'))
            ->rule('driver_license_id', 'not_empty')
            ->rule('driver_license_id', 'max_length', array(':value', '30'))
            ->rule('driver_license_id', 'Model_Add::checklicenceId', array(':value'))
            ->rule('driver_license_expire_date', 'not_empty')
            ->rule('driver_pco_license_number', 'not_empty')
            ->rule('driver_pco_license_number', 'max_length', array(':value', '30'))
            ->rule('driver_pco_license_number', 'Model_Add::checkpcolicenceNo', array(':value'))
            ->rule('driver_pco_license_expire_date', 'not_empty')
            ->rule('driver_insurance_number', 'not_empty')
            ->rule('driver_insurance_number', 'max_length', array(':value', '30'))
            ->rule('driver_insurance_number', 'Model_Add::checkinsuranceNo', array(':value'))
            ->rule('driver_insurance_expire_date', 'not_empty')
            ->rule('driver_national_insurance_number', 'not_empty')
            ->rule('driver_national_insurance_number', 'max_length', array(':value', '30'))
            ->rule('driver_national_insurance_number', 'Model_Add::checkNationalinsuranceNo', array(':value'))
            ->rule('driver_national_insurance_expire_date', 'not_empty')
            ->rule('address', 'not_empty')
            ->rule('country', 'not_empty')
            ->rule('state', 'not_empty');
            if($arr['brand_type'] == "M") {
                $validate->rule('company_name', 'not_empty');
            }
            $validate->rule('booking_limit', 'not_empty')
            ->rule('booking_limit', 'numeric')
            ->rule('booking_limit', 'Model_Add::check_booking_limit', array(':value',$arr['booking_limit']))
            ->rule('city', 'not_empty')
            ->rule('photo', 'Upload::not_empty',array(':value'))
            ->rule('photo', 'Upload::size', array($arr['photo'],IMG_MAX_SIZE))
            ->rule('photo', 'Upload::type', array(':value', array('jpeg','jpg','png','gif')));
        return $validate;
    }
    //To Add company Functionalities
    public function addcompany($post, $path)
    {
        // MongoDB Query Starts here
        $image_data = getimagesize($path['company_image']['tmp_name']);
        if (isset($image_data['mime'])) {
            $password        = Html::chars(md5($post['password']));
            $upgrade_package = "D";
            $upgrade_packid  = "";
            $current_date    = convert_timezone('now', $post['time_zone']);
            $ses_userId = $this->admin_userid;

            //people collection data and inserts hee
            //Get the last object id
            $people_first_key=0;
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
               'projection'=>[ '_id'=>1 ],
               'sort'=>['_id'=>-1 ],
               'limit'=>1
            ];
            $people_rs = $this->mongo_db->find(MDB_PEOPLE,[],$options);
            $people_res = (!empty($people_rs))?array($people_rs[0]['_id']=>0):array(1);
            reset($people_res);
            $people_first_key = !empty($people_res) ? key($people_res) : 0;
            $company_userid = $people_first_key+1;
            $people_data = array('_id'=>$company_userid,
                'name' => $post['firstname'],
                'address' => $post['address'],
                'lastname' => $post['lastname'],
                'email' => $post['email'],
                'paypal_account' => '',
                'country_code' => $post['telephone_code'],
                'phone' => $post['phone'],
                'password' => $password,
                'login_country' => (int)$post['country'],
                'login_state' => (int)$post['state'],
                'login_city' => (int)$post['city'],
                'created_date' => Commonfunction::MongoDate(strtotime($current_date)),
                'user_type' => 'C',
                'status' => ACTIVE,
                'user_createdby' => (int)$ses_userId
            );
            $people_result = $this->mongo_db->insertOne(MDB_PEOPLE,$people_data);
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            $options=[
                'projection'=>[ '_id'=>1 ],
                'sort'=>[ '_id'=>-1 ],
                'limit'=>1
            ];
            $company_rs = $this->mongo_db->find(MDB_COMPANY,[],$options);
            $company_res = (!empty($company_rs))?array($company_rs[0]['_id']=>0):array(1);
            reset($company_res);
            $company_first_key = (!empty($company_res)) ? key($company_res):0;
            $reg_companyid = $company_first_key+1;
            $company_data = array(
                'company_name' => $post['company_name'],
                'company_address' => $post['address'],
                'company_country' => (int)$post['country'],
                'company_state' => (int)$post['state'],
                'company_city' => (int)$post['city'],
                'userid' => (int)$company_userid,
                'time_zone' => $post['time_zone'],
                'user_time_zone' => '',
                'header_bgcolor' => '#FFFFFF',
                'menu_color' => '#000000',
                'mouseover_color' => '#FFD800',
                'company_status' => ACTIVE
            );
            //Company info data
            $key      = "";
            $charset  = "abcdefghijklmnopqrstuvwxyz";
            $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $charset .= "0123456789";
            $length = mt_rand(30, 35);
            for ($i = 0; $i < $length; $i++):
                $key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];
            endfor;
            $company_info_data = array(
                'company_app_name' => $post['company_name'],
                'company_currency' => $post['currency_symbol'],
                'company_currency_format' => $post['currency_code'],
                'company_paypal_username' => '',
                'company_paypal_password' => '',
                'company_paypal_signature' => '',
                'payment_method' => '',
                'company_notification_settings' => 60,
                'company_brand_type' => 'M',
                'company_api_key' => $key,
                'cancellation_fare' => '',
                'fare_calculation_type' => '3',
                'company_tax' => '',
                'company_copyrights' => '',
                'company_logo' => '',
                'company_favicon' => '',
                'default_unit' => '',
                'skip_credit_card' => '',
            );
            //Dispatch Algorithm with company collection
            $dispatch_algorithm_data = array();
            $dispatch_algorithm_data[] = array(
                'labelname' => 1,
                'alg_created_by' => (int)$company_userid,
                'alg_company_id' => $reg_companyid,
                'active' => 1,
                'hide_customer' => 0,
                'hide_droplocation' => 0,
                'hide_fare' => 0
            );
            //Company CMS Data And inset with company collection
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
                if($i==0)
                {
                    //~ $srcfile=DOCROOT.PUBLIC_IMAGES_FOLDER.'header_banner_bg.jpg';
                    //~ $dstfile=DOCROOT.PUBLIC_UPLOAD_BANNER_FOLDER.$reg_companyid.'_header_banner_bg.jpg';
                    //~ copy($srcfile, $dstfile);
                    $image_name=$reg_companyid.'_header_banner_bg.jpg';
                    $image_name='';
                    $values = array(
                        'company_id' => (int)$reg_companyid,
                        'image_tag' => 'image1',
                        'alt_tags' => 'image1',
                        'banner_image' => $image_name,
                        'type'=>2
                    );
                    $cms_data[] = $values;
                }else{
                    $cms_data[] = array(
                        'menu_name' => $pages[$i],
                        'title' => $pages[$i],
                        'content' => $pages[$i],
                        'page_url' => $page_url[$i],
                        'type'=>1
                    );
                }
            }
            $company_insert = array('_id' => $reg_companyid,
                'companydetails'=> $company_data,
                'companyinfo' => $company_info_data,
                'dispatch_algorithm'=>$dispatch_algorithm_data,
                'company_cms' => $cms_data,
                'paymentmodule'=>array(), //$payment_module_data,
                'model_fare' => array()
            );
            $company_result = $this->mongo_db->insertOne(MDB_COMPANY,$company_insert);
            /** ADD for Default Payment gateway **/
            ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
            /*$options=[
                'projection'=>['_id'=>1,'payment_gateway_name'=>1]
            ];
            $payment = $this->mongo_db->find(PAYMENT_GATEWAYS_TYPES,array('payment_gateway_status' => 1),$options);
            $i=1;
            foreach($payment as $gateway){
                $default=($i==1)?1:0;
                $payment_user= ($i==1)?'nandhu_1337947987_biz_api1.gmail.com':'';
                $payment_pass=($i==1)?'1337948040':'';
                $payment_sign=($i==1)?'A0YqGlJEML24al4qg2LnV2U.g2ThAfXD37NEiWIVcgjl1pxlygg-XaVs':'';
                $inc_id = Commonfunction::get_auto_id(MDB_PAYMENT_GATEWAYS);
                $payment_insert_data = array('_id'=>(int)$inc_id,
                    'company_id' => (int)$reg_companyid,
                    'payment_gateway_id' => (int)$gateway['_id'],
                    'payment_gatway' => $gateway['payment_gateway_name'],
                    'payment_gateway_username' => $payment_user,
                    'payment_gateway_password' => $payment_pass,
                    'payment_gateway_signature' => $payment_sign,
                    'payment_status' => 'A',
                    'default_payment_gateway' => (int)$default,
                    'currency_code' => 'USD',
                    'currency_symbol' => '$',
                    'payment_method' => 'T'
                );
                $pay_result = $this->mongo_db->insertOne(MDB_PAYMENT_GATEWAYS,$payment_insert_data);
                $i++;
            }*/
            // Company Image
            $image_name = $company_userid;
            $filename   = Upload::save($path['company_image'], $image_name, DOCROOT . COMPANY_IMG_IMGPATH);
            $logo_image = Image::factory($filename);
            $path1      = DOCROOT . COMPANY_IMG_IMGPATH;
            $path       = $image_name;
            $image_data = Commonfunction::multipleimageresize($logo_image, COMPANY_IMG_WIDTH, COMPANY_IMG_HEIGHT, $path1, $image_name, 90);
            // End Company Image
            //update company id with People Collection
             $update_people = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>$company_userid),array('$set'=>(array('company_id' => $reg_companyid))),array('upsert'=>true));

            return 1;
        } else {
            return 2;
        }
        // MongoDB Query Ends here
    }
    public function check_array($value = "")
    {
        if (!empty($value)) {
            return true;
        } else {
            return false;
        }
    }
    //To Add Taxi Functionalities
    public function addtaxi($post,$form_array, $image, $files)
    {
        $taxi_createdby   = $this->user_createdby;
        $cid         = $post['company_name'];
        //Get count of uploaded with taxi multiple images
        if (isset($files['size']['name'])) {
            $count = count($files['size']['name']);
        } else {
            $count = 0;
        }
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[ '_id'=>1 ],
            'sort'=>[ '_id'=>-1 ],
            'limit'=>1
        ];
        $rs = $this->mongo_db->find(MDB_TAXI,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $inc_id = $taxi_id = $first_key+1;
        $taxi_data = array('_id' => (int)$inc_id,
            'taxi_no' => $post['taxi_no'],
            'taxi_type' => (int)$post['taxi_type'],
            'taxi_model' => (int)$post['taxi_model'],
            'taxi_company' => (int)$post['company_name'],
            'taxi_owner_name' => $post['taxi_owner_name'],
            'taxi_manufacturer' => $post['taxi_manufacturer'],
            'taxi_colour' => $post['taxi_colour'],
            'taxi_motor_expire_date' => Commonfunction::MongoDate(strtotime($post['taxi_motor_expire_date'])),
            'taxi_insurance_number' => $post['taxi_insurance_number'],
            'taxi_insurance_expire_date_time' => Commonfunction::MongoDate(strtotime($post['taxi_insurance_expire_date'])),
            'taxi_pco_licence_number' => $post['taxi_pco_licence_number'],
            'taxi_pco_licence_expire_date' => Commonfunction::MongoDate(strtotime($post['taxi_pco_licence_expire_date'])),
            'taxi_country' => (int)$post['country'],
            'taxi_state' => (int)$post['state'],
            'taxi_city' => (int)$post['city'],
            'taxi_capacity' => 0,
            'taxi_speed' => (float)$post['taxi_speed'],
            'taxi_min_speed' => (float)$post['taxi_min_speed'],
            'max_luggage' => (int)$post['minimum_luggage'],
            'taxi_fare_km' => (float)$post['taxi_fare_km'],
            'taxi_createdby' => (int)$taxi_createdby,
            'taxi_status' => ACTIVE,
            'taxi_availability' => ACTIVE,
            'taxi_image' => $image,
            'taxi_sliderimage' => (int)$count,
        );
        $result = $this->mongo_db->insertOne(MDB_TAXI,$taxi_data);
        # image upload
        $taxi_arrcount = array();
        for($i=0;$i<$count;$i++){
            $file_array = array();
            $file_array['name'] =  $files['size']['name'][$i];
            $file_array['type'] = $files['size']['type'][$i];
            $file_array['tmp_name'] = $files['size']['tmp_name'][$i];
            $file_array['error'] = $files['size']['error'][$i];
            $image_name = $taxi_id.'_'.$i;
            $taxi_arrcount[] = $i;
            $filename = Upload::save($file_array,$image_name,DOCROOT.TAXI_IMG_IMGPATH);
            $logo_image = Image::factory($filename);
            $path1=DOCROOT.TAXI_IMG_IMGPATH;
            $path=$image_name;
            Commonfunction::multipleimageresize($logo_image,TAXI_IMG_WIDTH, TAXI_IMG_HEIGHT,$path1,$image_name,90);
        }
        $update_arrimage = serialize($taxi_arrcount);
        $updateresult = $this->mongo_db->updateOne(MDB_TAXI,array('_id'=>(int)$inc_id),array('$set'=>array('taxi_serializeimage'=>$update_arrimage)),array('upsert'=>false));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    //To check the fare exist for the model
    public function check_fare_exist($comny_id, $modelid)
    {
        $result = $this->mongo_db->count(MDB_COMPANY,array('_id'=>(int)$comny_id,'model_fare.model_id'=>(int)$modelid),array('_id','model_fare.model_id'));
        return $result;
    }

    //To Add Fare Functionalities
    public function addfare($post)
    {
        $company_id = $this->company_id;
        //MongoDB
        //Get the last object id
        $model_id = $post['model_name'];
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[ 'model_name'=>1 ]
        ];
        $rs = $this->mongo_db->find(MDB_MOTOR_MODEL,array('model_status'=>array('$eq'=>'A'), "_id"=>(int)$model_id),$options);
        $result = $rs;
        if (!empty($result)) {
            $query = array(
                'model_id' => (int)$model_id,
                'model_name' => $result[0]['model_name'],
                'fare_status' => "A",
                'company_cid' => (int)$company_id,
                'motor_mid' => (int)$post['companyname'],
                'base_fare' => (float)$post['base_fare'],
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
                'min_km' => (float)$post['min_km'],
                'below_above_km' => (float)$post['below_and_above_km'],
                'minutes_fare' => (float)$post['minutes_fare'],
                'model_size' => (int)$post['model_size'],
                'waiting_time' => (int)$post['waiting_time']
            );
            $model_fare_array = array("model_fare" =>$query );
            $result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>(int)$company_id),array('$push'=>$model_fare_array),array('upsert'=>false));
            return (empty($result->getwriteErrors())) ? 1 : 0;
        } else {
            return 0;
        }
    }
    //To Add Fare Functionalities
    public function exist_models($model_id)
    {
        $id = $this->company_id;
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
           'projection'=>[ 'model_fare'=>1 ]
        ];
        $result = $this->mongo_db->find(MDB_COMPANY,array("_id"=>(int)$id, "model_fare.model_id"=>(int)$model_id ),$options);
        $res = $result;
        return (!empty($res))?$res:array();
    }
    //To Add company Functionalities
    public function add_driver($post, $filename)
    {
        $password       = Html::chars(md5($post['password']));
        $user_createdby = $this->user_createdby;
        $cid            = $post['company_name'];
        $current_date   = $this->currentdate_bytimezone;
        //Get the last object id
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[ '_id'=>1 ],
            'sort'=>[ '_id'=>-1 ],
            'limit'=>1
        ];
        $rs = $this->mongo_db->find(MDB_PEOPLE,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $inc_id = $first_key+1;
        $param = array(
            '_id' => $inc_id,
            'name'=>$post['firstname'],
            'address'=>$post['address'],
            'lastname'=>$post['lastname'],
            'gender'=>$post['gender'],
            'dob'=> Commonfunction::MongoDate(strtotime($post['dob'])),
            'email'=>$post['email'],
            'phone'=>$post['phone'],
            'password'=>$password,
            'created_date' => Commonfunction::MongoDate(strtotime($current_date)),
            'user_type' => 'D',
            'status' => ACTIVE,
            'user_createdby' => (int)$user_createdby,
            'login_country' => (int)$post['country'],
            'login_state' => (int)$post['state'],
            'login_city' => (int)$post['city'],
            'company_id' => (int)$post['company_name'],
            'driver_license_id' => $post['driver_license_id'],
            'booking_limit' => (int)$post['booking_limit'],
            'profile_picture' => $filename,
            'notification_setting' => 0,
            'driver_first_login' => 1,
            'login_status' => 'N',
            'availability_status' => 'A' // for checking package purpose
        );
        $result = $this->mongo_db->insertOne(MDB_PEOPLE,$param);
        $driver_id      = $inc_id;
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[ '_id'=>1 ]
        ];
        $query = $this->mongo_db->find(MDB_DRIVER_INFO,array('_id'=> (int)$driver_id),$options);
        $result = $query;
        if (count($result) == 0) {
            $arguments = array(array('$unwind' => '$stateinfo'),array('$unwind' => '$stateinfo.cityinfo'),
                array('$match' => array('_id'=> (int)$post['country'],'stateinfo.state_id'=> (int)$post['state'],'stateinfo.cityinfo.city_id'=> (int)$post['city'],'stateinfo.cityinfo.city_status' => 'A')),
                array('$project' => array('_id' => 0,'city_name' => '$stateinfo.cityinfo.city_name',)),
                array('$sort' => array('stateinfo.cityinfo.city_name' => 1),)
            );
            $cityresult = $this->mongo_db->aggregate(MDB_CSC,$arguments);
            $cityresult = $cityresult['result'];
            $address    = isset($cityresult[0]['city_name'])?$cityresult[0]['city_name']:'';
            $address    = str_replace(' ', '+', $address);
            $url        = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key=' . GOOGLE_GEO_API_KEY;
            $ch         = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $geoloc = curl_exec($ch);
            $json   = json_decode($geoloc);
            if (isset($json->status) == 'OK' && isset($json->results[0]->geometry->location->lat)) {
                $latitude  = $json->results[0]->geometry->location->lat;
                $longitude = $json->results[0]->geometry->location->lng;
            } else {
                $latitude  = (double)LOCATION_LATI;
                $longitude = (double)LOCATION_LONG;
            }
            $dt = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('UTC'));
            $ts = $dt->getTimestamp();
            $today = Commonfunction::MongoDate($ts);
            $insert_data = array(
                '_id' => $driver_id,
                'status'=>'F',
                'shift_status' => 'OUT',
                'update_date' => $today,
                'loc'=>array('type' => 'Point', 'coordinates'=>array($longitude,$latitude)),
            );
            $result = $this->mongo_db->insertOne(MDB_DRIVER_INFO,$insert_data);
        }
        if ($result) {
            $driver_insert_data = array(
                        'driverinfo' =>array(array(
                                'driver_license_expire_date' => Commonfunction::MongoDate(strtotime($post['driver_license_expire_date'])),
                                'driver_pco_license_number' => $post['driver_pco_license_number'],
                                'driver_pco_license_expire_date'=> Commonfunction::MongoDate(strtotime($post['driver_pco_license_expire_date'])),
                                'driver_insurance_number'=> $post['driver_insurance_number'],
                                'driver_insurance_expire_date'=> Commonfunction::MongoDate(strtotime($post['driver_insurance_expire_date'])),
                                'driver_national_insurance_number'=>$post['driver_national_insurance_number'],
                                'driver_national_insurance_expire_date'=> Commonfunction::MongoDate(strtotime($post['driver_national_insurance_expire_date'])))));
            $result = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id'=>(int)$driver_id),array('$set'=>$driver_insert_data),array('upsert'=>false));
            //driver referral insert
            $driverReferralCode = text::random($type = 'alnum', $length = 6);
            $inc_id = Commonfunction::get_auto_id(MDB_DRIVER_REF);
            $registered_driver_wallet=0;
            $insert_ref = array('_id' => (int)$inc_id,
                                'referred_driver_id' => 0,
                                'registered_driver_id' => (int)$driver_id,
                                'registered_driver_code' => $driverReferralCode,
                                'registered_driver_code_amount' => DRIVER_REF_AMOUNT,
                                'registered_driver_wallet' => (double)$registered_driver_wallet,
                                'referral_status' => 0,
                                'createdate' => Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone))
                                );
            $res_ref = $this->mongo_db->insertOne(MDB_DRIVER_REF,$insert_ref);
            /** For Single brand type drivers, add their details to company and company_info tables ( only for Single brand type ) **/
            if($post['brand_type'] == "S") {
                ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>['_id'=>1],
                    'sort'=>['_id'=>-1],
                    'limit'=>1
                    ];
                $rs = $this->mongo_db->find(MDB_COMPANY,[],$options);
                $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
                reset($res);
                $first_key = key($res);
                $first_cmy_inc_id = $first_key+1;
                $insert_cmy =  array('_id' => (int)$first_cmy_inc_id,
                    'companydetails' =>array(
                        'company_name' => $post['firstname'],
                        'company_address' => $post['address'],
                        'company_country' => $post['country'],
                        'company_state' => $post['state'],
                        'company_city' => $post['city'],
                        'userid' => (int)$driver_id,
                        'time_zone' => $_SESSION['timezone'],
                        'company_status' => 'A',
                        'header_bgcolor' => '#FFFFFF',
                        'menu_color' => '#000000',
                        'mouseover_color' => '#FFD800'
                    )
                );
                $res_ref = $this->mongo_db->insertOne(MDB_COMPANY,$insert_cmy);
                $companyId = $first_cmy_inc_id;
                $key="";
                $charset = "abcdefghijklmnopqrstuvwxyz";
                $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $charset .= "0123456789";
                $length = mt_rand (30, 35);
                for ($i=0; $i<$length; $i++)
                $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
                //company info table
                $update_cmy_info =  array(
                    'companyinfo' =>array(
                        'company_cid' => $companyId,
                        'company_brand_type' => $post['brand_type'],
                        'company_api_key' => $key
                    )
                );
                $result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>(int)$companyId),array('$set'=>$update_cmy_info),array('upsert'=>false));
                //update company id in people table
                $result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$driver_id),array('$set'=>array('company_id' => $companyId)),array('upsert'=>false));
                $add_company = Model::factory('add');
            }else{
                $result = $this->mongo_db->findOne(MDB_COMPANY,array("_id"=>(int)$post['company_name']));
                $company_info_data = array(
                    'company_app_name' => $result['companyinfo']['company_app_name'],
                    'company_currency' => $result['companyinfo']['company_currency'],
                    'company_currency_format' => $result['companyinfo']['company_currency_format'],
                    'company_paypal_username' => $result['companyinfo']['company_paypal_username'],
                    'company_paypal_password' => $result['companyinfo']['company_paypal_password'],
                    'company_paypal_signature' => $result['companyinfo']['company_paypal_signature'],
                    'payment_method' => $result['companyinfo']['payment_method'],
                    'company_notification_settings' => $result['companyinfo']['company_notification_settings'],
                    'company_api_key' => $result['companyinfo']['company_api_key'],
                    'cancellation_fare' => $result['companyinfo']['cancellation_fare'],
                    'fare_calculation_type' => $result['companyinfo']['fare_calculation_type'],
                    'company_tax' => $result['companyinfo']['company_tax'],
                    'company_copyrights' => $result['companyinfo']['company_copyrights'],
                    'company_logo' => $result['companyinfo']['company_logo'],
                    'company_favicon' => $result['companyinfo']['company_favicon'],
                    'default_unit' => $result['companyinfo']['default_unit'],
                    'skip_credit_card' => $result['companyinfo']['skip_credit_card'],
                    'company_brand_type' => 'M'
                );
                $update_cmy_info =  array('companyinfo' =>$company_info_data);
                $result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>(int)$post['company_name']),array('$set'=>$update_cmy_info),array('upsert'=>false));
            }
            return $driver_id;
        } else {
            return 0;
        }
    }
    // To Check User Name is Already Available or Not
    public static function checkusername($name)
    {
        // Check if the username already exists in the database
        $result = DB::select('username')->from(PEOPLE)->where('username', '=', $name)->execute()->as_array();
        return (count($result) > 0)?false:true;
    }
    // To Check User Name is Already Available or Not
    public static function check_taxino($name)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_TAXI,array('taxi_no'=>$name),array('taxi_no'));
        return ($result>0)?false:true;
    }

    // To Check Company Name is Already Available or Not
    public static function checkcompany($companyname, $country, $state, $city)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_COMPANY,array('company_name'=>$companyname,'company_country'=>(int)$country,'company_state'=>(int)$state,'company_city'=>(int)$city));
        return ($result>0)?false:true;
    }
    // To Check Company Domain is Already Available or Not
    public static function checkdomain($domainname)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_COMPANY,array('company_domain'=>$domainname));
        return ($result>0)?false:true;
    }
    // To Check Company Domain is Already Available or Not
    public static function checkcompanydomain($domainname)
    {
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_COMPANY,array('companyinfo.company_domain'=>$domainname));
        return ($result > 0)?1:0;
    }
    // Check Whether Email is Already Exist or Not
    public static function checkemail($email = "")
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PEOPLE,array('email'=>$email));
        return ($result > 0)?false:true;
    }
    // Check Whether Phone is Already Exist or Not
    public static function checkphone($phone = "",$id="")
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $match = array('phone'=>$phone);
        $result = $mongodb->count(MDB_PEOPLE,$match);
        return ($result > 0)?false:true;
    }
    public static function checkphone_autocreate($phone = "")
    {
        $phone  = $phone . '1';
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PEOPLE,array('phone'=> $phone));
        return ($result > 0)?false:true;
    }
    // Check Whether Motor details is Already Exist or Not
    public function motor_details()
    {
        $result = array();
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>['_id'=>1],
            'sort'=>['motor_name'=>-1]
        ];
        $res = $this->mongo_db->find(MDB_MOTOR_COMPANY,array('motor_status'=>'A'),$options);
        $res = commonfunction::change_key($res);
        if(!empty($res)){
            $result = array_map(
                        function($res) {
                            return array('motor_id' => $res['_id']);
                        }, $res);
        }
        return $result;
    }

    // Check Whether Motor details is Already Exist or Not
    public function modeldetails()
    {
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>['_id'=>1,'model_name'=>1],
            'sort'=>['model_name'=>1]
        ];
        $result = $this->mongo_db->find(MDB_MOTOR_MODEL,["model_status"=>['$eq'=> "A"]],$options);
        $res = $result;
        return $res;
    }
    // Get the Additional Field for Taxi
    public static function taxi_additionalfields()
    {
        return array();
    }
    /**Validating for Add Country**/
    public function validate_addcountry($arr)
    {
        return Validation::factory($arr)->rule('country_name', 'not_empty')
            ->rule('country_name', 'Model_Add::check_reg_countryname', array(
            ':value'
        ))->rule('country_name', 'min_length', array(
            ':value',
            '2'
        ))->rule('country_name', 'max_length', array(
            ':value',
            '30'
        ))->rule('country_name', 'Model_Add::checkcountryname', array(
            ':value'
        ))->rule('iso_country_code', 'not_empty')->rule('iso_country_code', 'min_length', array(
            ':value',
            '2'
        ))->rule('iso_country_code', 'max_length', array(
            ':value',
            '5'
        ))->rule('iso_country_code', 'Model_Add::checkisocountrycode', array(
            ':value'
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
            '5'
        ));
    }
    // To Check Countryname is Already Available or Not
    public static function checkcountryname($name)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $res = $mongodb->count(MDB_CSC,array( "country_name" => Commonfunction::MongoRegex("/^$name/i")),array('country_name'));
        return ($res > 0)?false:true;
    }
    // To Check Countryname is Already Available or Not
    public static function checkfaqtitle($faq)
    {
        $mongo_db = MangoDB::instance('default');
        $result = $mongo_db->count(MDB_PASSENGERS_FAQ,array('faq_title'=>$faq));
        return ($result > 0)?false:true;
    }
    // To Check Countryname is Already Available or Not
    public static function checkisocountrycode($iso_country_code)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $res = $mongodb->count(MDB_CSC,array('iso_country_code'=>Commonfunction::MongoRegex("/^$iso_country_code/i")),array('iso_country_code'));
        return ($res > 0)?false:true;
    }
    //To Add Country Functionalities
    public function addcountry($post)
    {
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                '_id'=>1,
                ],
            'sort'=>[
                '_id'=>-1
                 ],
            'limit'=>1
            ];
        $rs = $this->mongo_db->find(MDB_CSC,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $inc_id = $first_key+1;
        $query = array(
            '_id' => $inc_id,
            'country_name' => $post['country_name'],
            'iso_country_code' => $post['iso_country_code'],
            'telephone_code' => $post['telephone_code'],
            'currency_code' => $post['currency_code'],
            'currency_symbol' => $post['currency_symbol'],
            'country_status' => ACTIVE,
            'default' => 0,
            'stateinfo' => array()
        );
        $result = $this->mongo_db->insertOne(MDB_CSC,$query);
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    public function country_detail()
    {
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                '_id'=>1,
                'country_name'=>1
                ],
            'sort'=>[
                'country_name'=>1
                 ]
            ];
        $res = $this->mongo_db->find(MDB_CSC,['country_status'=>'A'],$options);
        return (!empty($res))?$res:array();
    }
    public function country_details_new()
    {
        $result = $temp_arr = array();
        $args = array(
                       array('$unwind' =>  array( 'path' =>  '$stateinfo', 'preserveNullAndEmptyArrays' =>  true )),
                       array('$unwind' =>  array( 'path' =>  '$stateinfo.cityinfo', 'preserveNullAndEmptyArrays' =>  true )),
                       array('$match' => array('country_status' => 'A',
                           'stateinfo.state_status' => 'A')),
                       array('$group' => array('_id' => array('country_id' => '$_id','country_name' => '$country_name')))
                   );
        $res = $this->mongo_db->Aggregate(MDB_CSC,$args);
        if(!empty($res['result'])){
            foreach($res['result'] as $r){

                $temp_arr['country_id'] = $r['_id']['country_id'];
                $temp_arr['country_name'] = $r['_id']['country_name'];
                $result[] = $temp_arr;
            }
        }
        return $result;
    }
    //MongoDB Embedded document search value with assosciative array
    public function form_array($array, $key)
    {
        $results = array();
        if (is_array($array)) {
            $arrval = (isset($array[$key])) ? trim(strtolower($array[$key])) : '';
            //search other than department and role
            if ( !empty($arrval) ) {
                $results[] = $array;
            }
            foreach ($array as $subarray) {
                $results = array_merge($results, $this->form_array($subarray, $key));
            }
        }
        return $results;
    }
    public function country_details()
    {
        $result = array();
        $options=[
            'projection'=>[
                '_id'=>1,
                'country_name'=>1
                ],
            'sort'=>[
                'country_name'=>1
                 ]
            ];
        $res = $this->mongo_db->find(MDB_CSC,['country_status'=>'A'],$options);
        $res_arr = $res;
        $result = array();
        if(!empty($res_arr)){
            foreach($res_arr as $r){
                $result[] = array('country_id' => $r['_id'], 'country_name' => $r['country_name']);
            }
        }
        return $result;
    }
    public function city_details()
    {
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$unwind' => '$stateinfo.cityinfo'),
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
        return (!empty($result['result']))?$result['result']:array();
    }
    public function state_details()
    {
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$match' => array('stateinfo.state_status'=>'A')),
            array('$project' => array('_id' => 0,
                'state_id' => '$stateinfo.state_id',
                'state_name' => '$stateinfo.state_name',
                )
            ),
            array(
                '$sort' => array(
                    'country_name' => 1
                ),
            )
        );
        $result = $this->mongo_db->aggregate(MDB_CSC,$ops);
        return (!empty($result['result']))?$result['result']:array();
    }
    public function get_city_state_details($countryid)
    {
        if ($countryid) {
            $state_countryid = $countryid;
        } else {
            $state_countryid = DEFAULT_COUNTRY;
        }
        //MongoDB with aggregate process only
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$match' => array('stateinfo.state_status'=>'A','stateinfo.state_countryid'=>(int)$state_countryid)),
            array('$project' => array('_id' => 0,
                'state_id' => '$stateinfo.state_id',
                'state_name' => '$stateinfo.state_name',
                'state_default' => '$stateinfo.default',
                )
            ),
            array(
                '$sort' => array(
                    'country_name' => 1
                ),
            )
        );
        $result = $this->mongo_db->aggregate(MDB_CSC,$ops);
        return (!empty($result['result']))?$result['result']:array();
    }
    public static function getstate_details($country_id)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$match' => array('stateinfo.state_status'=>'A','stateinfo.state_countryid'=>(int)$country_id)),
            array('$project' => array('_id' => 0,
                'state_id' => '$stateinfo.state_id',
                'state_name' => '$stateinfo.state_name',
                'state_default' => '$stateinfo.default',
                )
            ),
            array(
                '$sort' => array(
                    'country_name' => 1
                ),
            )
        );
        $result = $mongodb->aggregate(MDB_CSC,$ops);
        return (!empty($result['result']))?$result['result']:array();
    }
    public static function getcity_details($country_id, $state_id)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$unwind' => '$stateinfo.cityinfo'),
            array('$match' => array('stateinfo.cityinfo.city_status'=>'A','stateinfo.state_id'=>(int)$state_id,'_id'=>(int)$country_id)),
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
        $result = $mongodb->aggregate(MDB_CSC,$ops);
        return (!empty($result['result']))?$result['result']:array();
    }
    public function validate_addcity($arr)
    {
        return Validation::factory($arr)->rule('city_name', 'not_empty')
            ->rule('city_name', 'Model_Add::check_reg_city_name', array(
            ':value'
        ))->rule('city_name', 'min_length', array(
            ':value',
            '2'
        ))->rule('city_name', 'max_length', array(
            ':value',
            '30'
        ))->rule('city_name', 'Model_Add::checkcityname', array(
            ':value',
            $arr['state_name'],
            $arr['country_name']
        ))->rule('state_name', 'not_empty')->rule('country_name', 'not_empty')->rule('zipcode', 'not_empty')->rule('city_model_fare', 'not_empty')->rule('city_model_fare', 'numeric')
        ->rule('city_model_fare', 'Model_Edit::checkmodelfare', array(':value'));
    }
    public function validate_addstate($arr)
    {
        return Validation::factory($arr)->rule('state_name', 'not_empty')
            ->rule('state_name', 'Model_Add::check_reg_state_name', array(
            ':value'
        ))->rule('state_name', 'min_length', array(
            ':value',
            '2'
        ))->rule('state_name', 'max_length', array(
            ':value',
            '30'
        ))->rule('state_name', 'Model_Add::checkstatename', array(
            ':value',
            $arr['country_name']
        ))->rule('country_name', 'not_empty');
    }
    public static function checkcityname($name, $stateid, $id)
    {
        //MongoDB
        $country_id = (int)$id;
        $state_id = (int)$stateid;
        $mongodb = MangoDB::instance('default');
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'stateinfo.cityinfo.city_name'=>1
            ],
            'sort'=>[
                'stateinfo.cityinfo.city_id'=>-1
            ]
        ];
        $res = $mongodb->find(MDB_CSC,array( "\$and" => array(array( "stateinfo.cityinfo.city_name" => Commonfunction::MongoRegex("/^$name/i")) , array('_id'=>array('$eq'=>$country_id)),array('stateinfo.state_id'=>array('$eq'=>$state_id)))),$options);
        $result = (!empty($res))?$res:array();
        return (count($result) > 0)?false:true;
    }
    public static function checkstatename($name, $id)
    {
        $cid = (int)$id;
        $mongodb = MangoDB::instance('default');
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'stateinfo.state_name'=>1
            ],
            'sort'=>[
                'stateinfo.state_id'=>-1
            ]
        ];
        $match = array( "\$and" => array(array( "stateinfo.state_name" => Commonfunction::MongoRegex("/^$name/i")) , 
										array('_id'=>array('$eq'=>$cid))));
        $res = $mongodb->find(MDB_CSC,$match,$options);
        $result = (!empty($res))?$res:array();
        return (count($result) > 0)?false:true;
    }
    public function addcity($post)
    {
        $country_id = (int)$post['country_name'];
        $state_id = (int)$post['state_name'];
        //Get the last object id
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'stateinfo.cityinfo.$'=>1,
                ],
            'sort'=>[
                'stateinfo.cityinfo.city_id'=>-1
                 ],
            'limit'=>1
            ];
        $rs = $this->mongo_db->find(MDB_CSC,['_id'=>$country_id,'stateinfo.state_id'=>$state_id],$options);
        $result = (!empty($rs))?$rs:array();
        if (!empty($result)){
            $res = (count($result[0]['stateinfo'][0]['cityinfo']) > 0) ? array_reverse((array)$result[0]['stateinfo'][0]['cityinfo']) : 0;
        } else {
            $res = 0;
        }
        $args = array(array('$unwind' => '$stateinfo'),
                      array('$unwind' => '$stateinfo.cityinfo'),
                      array('$sort' => array('stateinfo.cityinfo.city_id' => -1)),
                      array('$project' => array('id'=>'$stateinfo.cityinfo.city_id')),
                      array('$limit' => 1)
                      );
        $rs = $this->mongo_db->aggregate(MDB_CSC,$args);
        $first_key = !empty($rs['result']) ? $rs['result'][0]['id'] : 0;
        $inc_id = $first_key+1;
        $query = array('city_id' => (int)$inc_id,
            'city_name' =>  $post['city_name'],
            'zipcode' => $post['zipcode'],
            'city_status' => ACTIVE,
            'city_countryid' => $country_id,
            'city_stateid' => $state_id,
            'city_model_fare' => (float)$post['city_model_fare'],
            'default' => 0
        );
        $state_index = $state_index = commonfunction::get_collection_index($country_id,$state_id,0,'state');
        $index_key = "stateinfo.".$state_index.".cityinfo";
        $city_array = array($index_key => $query );
        $mresult = $this->mongo_db->updateOne(MDB_CSC,array('_id'=>$country_id,'stateinfo.state_id'=>$state_id),array('$push'=>$city_array),array('upsert'=>true));
        return (empty($mresult->getwriteErrors()))?1:$mresult->getwriteErrors();
    }
    public function addstate($post)
    {
        $country_id = (int)$post['country_name'];
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'stateinfo.state_id'=>1,
                ],
            'sort'=>[
                'stateinfo.state_id'=>-1
                 ],
            'limit'=>1
            ];
        $rs = $this->mongo_db->find(MDB_CSC,['_id'=>$country_id],$options);
        $result = (!empty($rs))?$rs:array();
        if (!empty($result)){
            $res = (isset($result[0]['stateinfo'])) ? array_reverse((array)$result[0]['stateinfo']) : 0;
        } else {
            $res = 0;
        }
        $args = array(array('$unwind' => '$stateinfo'),
                      array('$sort' => array('stateinfo.state_id' => -1)),
                      array('$project' => array('id'=>'$stateinfo.state_id')),
                      array('$limit' => 1)
                  );
        $rs = $this->mongo_db->aggregate(MDB_CSC,$args);
        $first_key = !empty($rs['result']) ? $rs['result'][0]['id'] : 0;
        $inc_id = $first_key+1;
        $query = array("state_id" =>(int)$inc_id,
            "state_name"=>$post['state_name'],
            "state_countryid"=> (int)$country_id,
            "state_status"=>ACTIVE,
            "default"=>0,
            "cityinfo"=>array()
        );
        $state_array = array("stateinfo" =>$query );
        $mresult = $this->mongo_db->updateOne(MDB_CSC,array('_id'=>$country_id),array('$push'=>$state_array),array('upsert'=>true));
        return (empty($mresult->getwriteErrors()))?1:$mresult->getwriteErrors();
    }
    public static function model_details()
    {
        $mongo_db = MangoDB::instance('default');
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                '_id'=>1,
                'model_name'=>1
            ],
            'sort'=>[
                'model_name'=>1
                ]
        ];
        $res = $mongo_db->find(MDB_MOTOR_MODEL,array('model_status'=>'A'),$options);
        $res = commonfunction::change_key($res);
        if(!empty($res)){
            $result = array_map(
                        function($res) {
                            return array(
                                'model_id' => $res['_id'],
                                'model_name' => $res['model_name']
                            );
                        }, $res);
        }
        return $result;
    }
    public function model_details_new($company_id='')
    {
        $data = array();
        if($company_id != 0 && $company_id != '')
        {
			$match = array('cdetails._id'=>(int)$company_id, 'model_status' => 'A');
			$arguments = array(
                array('$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => '_id',
                    'foreignField' => 'model_fare.model_id',
                    'as'=> "cdetails"
                )),
                array('$unwind'=>'$cdetails'),
                array('$match'=>$match),
                array('$project'=>array('_id'=>'$_id','model_name'=>'$model_name'))
            );
            $result = $this->mongo_db->aggregate(MDB_MOTOR_MODEL,$arguments);
            $res = $result['result'];
            if(!empty($res)){
                foreach($res as $r){
                    $temp_arr = array('model_id' => $r['_id'],'model_name' => $r['model_name']);
                    $data[] = $temp_arr;
                }
            }
            return $data;
        } else {
            $options=[
                'projection'=>[
                    '_id'=>1,
                    'model_name'=>1
                    ],
                'sort'=>[
                    'model_name'=>-1
                     ]
                ];
            $res = $this->mongo_db->find(MDB_MOTOR_MODEL,['model_status'=>'A'],$options);
            $res = $res;
            if(!empty($res)){
                foreach($res as $r){
                    $temp_arr = array('model_id' => $r['_id'],'model_name' => $r['model_name']);
                    $data[] = $temp_arr;
                }
            }
            return $data;
        }
    }
    public static function getmodel_details($motorid)
    {
        $mongodb = MangoDB::instance('default');
        $args = array(
                array('$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => '_id',
                    'foreignField' => 'model_fare.model_id',
                    'as'=> "cdetails"
                )),
                array('$unwind'=>'$cdetails'),

                array('$match' => array('_id'=> (int)$motorid)),
                array('$project'=>array('model_id'=>'$_id','model_name'=>'$model_name')),
                array('$sort' => array('model_name' => 1))
        );
        $result = $mongodb->aggregate(MDB_MOTOR_MODEL,$args);
        return (!empty($result['result']))?$result['result']:array();
    }

    public function taxicompany_details()
    {
        $ops = array(
            array('$unwind' => '$companydetails'),
            array('$match' => array('companydetails.company_status' => 'A')),
            array('$project' => array('cid' => '$_id','company_name' => '$companydetails.company_name', 'company_brand_type' => '$companyinfo.company_brand_type')),
            array(
                '$sort' => array(
                    'companydetails.country_name' => 1
                ),
            )
        );
        $result = $this->mongo_db->aggregate(MDB_COMPANY,$ops);
        return (!empty($result['result']))?$result['result']:array();
    }
    public function getcompany_details($country_id, $state_id, $city_id)
    {
        $ops = array(
            array('$unwind' => '$companydetails'),
            array('$match' => array('companydetails.company_status' => 'A','companydetails.company_country' => (int)$country_id, 'companydetails.company_state' => (int)$state_id, 'companydetails.company_city' => (int)$city_id)),
            array('$project' => array('cid' => '$_id','company_name' => '$companydetails.company_name')
            ),
            array(
                '$sort' => array(
                    'companydetails.country_name' => 1
                ),
            )
        );
        $result = $this->mongo_db->aggregate(MDB_COMPANY,$ops);
        return (!empty($result['result']))?$result['result']:array();
    }

    public function validate_addmanager($arr)
    {
        return Validation::factory($arr)->rule('firstname', 'not_empty')
            ->rule('firstname', 'min_length', array(
            ':value',
            '4'
        ))->rule('firstname', 'max_length', array(
            ':value',
            '30'
        ))->rule('lastname', 'not_empty')
        ->rule('email', 'not_empty')->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('email', 'Model_Add::checkemail', array(
            ':value'
        ))->rule('phone', 'not_empty')
        ->rule('phone', 'min_length', array(
            ':value',
            '7'
        ))->rule('phone', 'max_length', array(
            ':value',
            '20'
        ))
        ->rule('phone', 'Model_Add::checkphone', array(
            ':value'
        ))->rule('password', 'not_empty')->rule('password', 'min_length', array(
            ':value',
            '4'
        ))->rule('password', 'max_length', array(
            ':value',
            '20'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('repassword', 'not_empty')->rule('repassword', 'min_length', array(
            ':value',
            '4'
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
        ))->rule('company_name', 'not_empty')
        ->rule('address', 'not_empty')->rule('country', 'not_empty')->rule('state', 'not_empty')->rule('city', 'not_empty');
    }
    public function validate_addadmin($arr)
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
        ))->rule('email', 'Model_Add::checkemail', array(
            ':value'
        ))->rule('phone', 'not_empty')
            ->rule('phone', 'min_length', array(
            ':value',
            '7'
        ))->rule('phone', 'max_length', array(
            ':value',
            '20'
        ))->rule('phone', 'contact_phone', array(
            ':value'
        ))->rule('phone', 'Model_Add::checkphone', array(
            ':value'
        ))->rule('password', 'not_empty')->rule('password', 'min_length', array(
            ':value',
            '4'
        ))->rule('password', 'max_length', array(
            ':value',
            '20'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('repassword', 'not_empty')->rule('repassword', 'min_length', array(
            ':value',
            '4'
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
        ))->rule('address', 'not_empty')->rule('country', 'not_empty');
    }
    public static function checkmanagercompany($companyname, $cityid, $stateid, $countryid)
    {
        $result = DB::select()->from(PEOPLE)->where('company_id', '=', $companyname)->where('login_country', '=', $countryid)->where('login_state', '=', $stateid)->where('login_city', '=', $cityid)->where('user_type', '=', 'M')->execute()->as_array();
        if (count($result) > 0) {
            return false;
        } else {
            return true;
        }
    }
    public function addmanager($post)
    {
        $password          = Html::chars(md5($post['password']));
        $manager_createdby = $this->user_createdby;
        $current_date      = $this->currentdate_bytimezone;
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
        $rs = $this->mongo_db->find(MDB_PEOPLE,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $inc_id = $first_key+1;
        $query = array(
            '_id' => (int)$inc_id,
            'name' => $post['firstname'],
            'address' =>  $post['address'],
            'lastname' =>  $post['lastname'],
            'email' => $post['email'],
            'phone' => $post['phone'],
            'password' => $password,
            'created_date' => $current_date,
            'user_type' => 'M',
            'status' => ACTIVE,
            'login_country' => (int)$post['country'],
            'login_state' => (int)$post['state'],
            'login_city' => (int)$post['city'],
            'company_id' => (int)$post['company_name'],
            'user_createdby' => (int)$manager_createdby
        );
        $result = $this->mongo_db->insertOne(MDB_PEOPLE,$query);
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }

    public function insert_promocode($insert_array)
    {
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
        $rs = $this->mongo_db->find(MDB_PASSENGERS_PROMO,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $inc_id = $first_key+1;
        $query = array(
            '_id' => (int)$inc_id,
            'passenger_id' => (int)$insert_array['passenger_id'],
            'company_id' =>  (int)$insert_array['company_id'],
            'promocode' =>  $insert_array['promocode'],
            'promo_discount' => $insert_array['promo_discount'],
            'promo_used' => (int)$insert_array['promo_used'],
            'amount_earned' => (int)$insert_array['amount_earned'],
            'start_date' => Commonfunction::MongoDate(strtotime($insert_array['start_date'])),
            'expire_date' => Commonfunction::MongoDate(strtotime($insert_array['expire_date'])),
            'promo_limit' => (int)$insert_array['promo_limit'],
            'createdate' => Commonfunction::MongoDate(strtotime($insert_array['createdate']))
        );
        $result = $this->mongo_db->insertOne(MDB_PASSENGERS_PROMO,$query);
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }

    public function addadmin($post)
    {
        $password          = Html::chars(md5($post['password']));
        $createdby         = $this->user_createdby;
        $current_date      = $this->currentdate_bytimezone;
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
        $rs = $this->mongo_db->find(MDB_PEOPLE,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $inc_id = $first_key+1;
        $data = array(
            '_id' => $inc_id,
            'name' => $post['firstname'],
            'address' => $post['address'],
            'lastname' => $post['lastname'],
            'email' => $post['email'],
            'phone' => $post['phone'],
            'password' => $password,
            'created_date' => $current_date,
            'user_type' => 'S',
            'account_type' => 0,
            'status' => ACTIVE,
            'login_country' => (int)$post['country'],
            'country_code' => $post['telephone_code'],
            'login_state' => 0,
            'login_city' => 0,
            'user_createdby' => $createdby,
        );
        $result = $this->mongo_db->insertOne(MDB_PEOPLE,$data);
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    public function driver_details( )
    {
        $usertype                        = $this->usertype;
        $company_id                      = $this->company_id;
        $country_id                      = $this->country_id;
        $state_id                        = $this->state_id;
        $city_id                         = $this->city_id;
        $match_query                     = array();
        $match_query['user_type'] = 'D';
        $match_query['status']    = 'A';
        $match_query['availability_status']    = 'A';
        if ($usertype == 'M') {
            $match_query['login_country'] = (int)$country_id;
            $match_query['login_state']   = (int)$state_id;
            $match_query['login_city']    = (int)$city_id;
            $match_query['company_id']    = (int)$company_id;
        } else if ($usertype == 'C') {
            $match_query['company_id'] = (int) $company_id;
        }
        $arguments = array(
            array(
                '$match' => $match_query
            ),
            array(
                '$sort' => array(
                    'created_date' => -1
                ),
            ),
            array(
                '$project' => array(
                    'id' => '$_id',
                    'name' => '$name'
                )
            )
        );
        $result    = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
        return (!empty($result['result'])) ? $result['result'] : array();
    }

    public function taxi_details( )
    {
        $usertype                        = $this->usertype;
        $company_id                      = $this->company_id;
        $country_id                      = $this->country_id;
        $state_id                        = $this->state_id;
        $city_id                         = $this->city_id;

        $match_query                     = array();
        $match_query['taxi_status'] = 'A';
        $match_query['taxi_availability']    = 'A';
        if ($usertype == 'M') {
            $match_query['taxi_country'] = (int)$country_id;
            $match_query['taxi_state']   = (int)$state_id;
            $match_query['taxi_city']    = (int)$city_id;
            $match_query['taxi_company']    = (int)$company_id;
        } else if ($usertype == 'C') {
            $match_query['taxi_company'] = (int) $company_id;
        }
        $arguments = array(
            array(
                '$match' => $match_query
            ),
            array(
                '$sort' => array(
                    '_id' => -1
                ),
            ),
            array(
                '$project' => array(
                    'taxi_id' => '$_id',
                    'taxi_no' => '$taxi_no'
                )
            )
        );
        $result    = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
        return (!empty($result['result'])) ? $result['result'] : array();
    }

    public function getassignedcount($country_id = '', $state_id = '', $city_id = '', $company_name = '', $driver_id = '', $taxi_id = '', $startdate = '', $enddate = '')
    {
        $count = $this->getassignedlist($country_id,$state_id,$city_id,$company_name,$driver_id,$taxi_id,$startdate,$enddate, '', '', TRUE);
        return $count;
    }

    public function getassignedlist($country_id = '', $state_id = '', $city_id = '', $company_name = '', $driver_id = '', $taxi_id = '', $startdate = '', $enddate = '', $offset = '', $val = '', $find_count = FALSE)
    {
        $user_createdby = $this->user_createdby;
        $usertype       = $this->usertype;
        $company_id     = ($company_name!="")?$company_name:$this->company_id;
        $country_id     = ($country_id!="")?$country_id:$this->country_id;
        $state_id       = ($state_id!="")?$state_id:$this->state_id;
        $city_id        = ($city_id!="")?$state_id:$this->city_id;
        $match_query = array();
        $match_query['mapping._id'] = array('$gte' => 0);
        if ($usertype == 'C' && $company_id!=0) {
            $match_query['mapping.mapping_companyid'] = (int)$company_id;
        }
        if ($usertype == 'M' && $company_id!=0) {
            $match_query['mapping.mapping_companyid'] = (int)$company_id;
            $match_query['mapping.mapping_countryid'] = (int)$country_id;
            $match_query['mapping.mapping_stateid'] = (int)$state_id;
            $match_query['mapping.mapping_cityid'] = (int)$city_id;
        }
        if ($driver_id) {
            $match_query['mapping.mapping_driverid'] = (int)$driver_id;
        }
        if ($taxi_id) {
            $match_query['mapping.mapping_taxiid'] = (int)$taxi_id;
        }
        if ($startdate && $enddate) {
            $match_query['mapping.mapping_startdate'] = array('$gte' => $startdate);
            $match_query['mapping.mapping_enddate'] = array('$lt' => $enddate);
        }else{
            if ($startdate) {
                $match_query['mapping.mapping_startdate'] = array('$gte' => $startdate);
                $match_query['mapping.mapping_enddate'] = array('$lt' => $startdate);
            }
            if ($enddate) {
                $match_query['mapping.mapping_startdate'] = array('$gte' => $enddate);
                $match_query['mapping.mapping_enddate'] = array('$lt' => $enddate);
            }
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
                '$match' => $match_query
            ),
        );
        if($find_count==TRUE){
            $count_arguments = array(
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
            $merge_arguments = array_merge($common_arguments, $count_arguments);
            $result          = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
            return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
        }else{
            $field_arguments = array(
                array(
                    '$sort' => array(
                        'mapping.mapping_createdate' => -1
                    ),
                ),
                array(
                    '$project' => array(
                        'id' => '$mapping._id',
                        'name' => '$people.name',
                        'taxi_no' => '$taxi.taxi_no',
                        'company_name' => '$company.companydetails.company_name',
                        'country_name'=>'$country_name',
                        'city_name'=>'$stateinfo.cityinfo.city_name',
                        'mapping_status' => '$mapping.mapping_status',
                        'mapping_startdate' => '$mapping.mapping_startdate',
                        'mapping_enddate' => '$mapping.mapping_enddate',

                    )
                ),
                array('$skip'   => (int)$offset ),
                array('$limit'  => (int)$val )
            );
            $merge_arguments = array_merge($common_arguments, $field_arguments);
            $res    = $this->mongo_db->aggregate(MDB_CSC, $merge_arguments);
            $result = array();
            if(!empty($res['result'])){
                foreach($res['result'] as $r){
                    $temp_arr['id'] = isset($r['_id'])?$r['_id']:"";
                    $temp_arr['name'] = isset($r['name'])?$r['name']:"";
                    $temp_arr['taxi_no'] = isset($r['taxi_no'])?$r['taxi_no']:"";
                    $temp_arr['company_name'] = isset($r['company_name'])?$r['company_name']:"";
                    $temp_arr['country_name'] = isset($r['country_name'])?$r['country_name']:"";
                    $temp_arr['city_name'] = isset($r['city_name'])?$r['city_name']:"";
                    $temp_arr['mapping_status'] = isset($r['mapping_status'])?$r['mapping_status']:"";
                    $temp_arr['mapping_startdate'] = isset($r['mapping_startdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['mapping_startdate']) : '';
                    $temp_arr['mapping_enddate'] = isset($r['mapping_enddate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['mapping_enddate']) : '';
                    $result[] = $temp_arr;
                }
            }
            return $result;
        }

    }
    /*public function validate_addassigntaxi($arr)
    {
        return Validation::factory($arr)->rule('company_name', 'not_empty')
			->rule('country', 'not_empty')->rule('state', 'not_empty')->rule('city', 'not_empty')
			->rule('driver', 'not_empty')
			->rule('startdate', 'not_empty')->rule('enddate', 'not_empty')
			->rule('enddate', 'Model_Add::checkassigntaxi', array(
            ':value',
            $arr
			))->rule('taxi', 'not_empty');
    }*/
    public function validate_addassigntaxi($arr)
    {
        return Validation::factory($arr)->rule('company_name', 'not_empty')
			->rule('country', 'not_empty')->rule('state', 'not_empty')->rule('city', 'not_empty')
			->rule('startdate', 'not_empty')->rule('enddate', 'not_empty')
			->rule('driver', 'not_empty')
			->rule('driver', 'Model_Add::checkassigntaxi', array(
            'driver',':value',
            $arr))			
			->rule('taxi', 'not_empty')
			->rule('taxi', 'Model_Add::checkassigntaxi', array(
            'taxi',':value',
            $arr));
    }
   
    public static function checkassigntaxi($driver_taxi, $driver_taxi_id, $post)
    {
        $mongo_db        = MangoDB::instance('default');
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
		if ($driver_taxi == 'driver') {
            
            $match_query['mapping.mapping_driverid'] = (int)$driver_taxi_id;
        }else{
            
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
        $result          = $mongo_db->aggregate(MDB_CSC, $arguments);
        $count = (isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
        $response = ($count > 0) ? false : true;
        return $response;
    }

    public function addassigntaxi($post)
    {
        $mapping_createdby = $this->user_createdby;
        //people collection data and inserts hee
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
        $rs = $this->mongo_db->find(MDB_TAXI_DRIVER_MAPPING,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $_id = $first_key+1;
        $insert_data = array('_id'=>$_id,
            'mapping_driverid' => (int)$post['driver'],
            'mapping_taxiid' =>(int)$post['taxi'],
            'mapping_companyid' =>(int)$post['company_name'],
            'mapping_countryid' =>(int)$post['country'],
            'mapping_stateid' => (int)$post['state'],
            'mapping_cityid' =>(int)$post['city'],
            'mapping_startdate' => Commonfunction::MongoDate(strtotime($post['startdate'])),
            'mapping_enddate' => Commonfunction::MongoDate(strtotime($post['enddate'])),
            'mapping_status' =>ACTIVE,
            'mapping_createdby' =>$mapping_createdby
        );
        $result = $this->mongo_db->insertOne(MDB_TAXI_DRIVER_MAPPING,$insert_data);
        if ($_id) {
            $arguments = array(
                array(
                    '$lookup' => array(
                        'from' => MDB_TAXI,
                        'localField' => 'mapping_taxiid',
                        'foreignField' => '_id',
                        'as' => 'taxi'
                    )
                ),
                array('$unwind' => '$taxi'),
                array(
                    '$lookup' => array(
                        'from' => MDB_MOTOR_MODEL,
                        'localField' => 'taxi.taxi_model',
                        'foreignField' => '_id',
                        'as' => 'motor_model'
                    )
                ),
                array('$unwind' => '$motor_model'),
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
                        'model_name' => '$motor_model.model_name',
                        'taxi_speed' => '$taxi.taxi_speed',
                        'max_luggage' => '$taxi.max_luggage',
                        'name' => '$people.name',
                        'email' => '$people.email',
                    )
                ),
            );
            $result          = $this->mongo_db->aggregate(MDB_TAXI_DRIVER_MAPPING, $arguments);
            $resultquery = (!empty($result['result']) && isset($result['result'])) ? $result['result']: 0;
            return $resultquery;
        } else {
            return 0;
        }
    }

    public function getdriverdetails($company_id, $country_id, $state_id, $city_id, $usertype)
    {
        $match_query = array("user_type" => 'D',"status" => 'A',
						"availability_status" => 'A',"login_country" => (int)$country_id,
						"login_state" => (int)$state_id,"login_city" => (int)$city_id);
        if ($usertype == 'C' || ($company_id != '' && $company_id != 0)) {
            $match_query['company_id'] = (int) $company_id;
        } else if($company_id != '' && $company_id != 0) {
            $match_query['company_id'] = (int) $company_id;
        }
        $arguments = array(
            array(
                '$lookup' => array(
                    'from' => MDB_TAXI_DRIVER_MAPPING,
                    'localField' => '_id',
                    'foreignField' => 'mapping_driverid',
                    'as' => 'taximap'
                )
            ),
            array('$unwind' =>  array( 'path' =>  '$taximap', 'preserveNullAndEmptyArrays' =>  true )),
            array(
                '$match' => $match_query
            ),
            array(
                '$project' => array(
                    "id" => '$_id',
                    "mapping_id" => '$taximap._id',
                    "name" => '$name',
                    "mapping_startdate" => '$taximap.mapping_startdate',
                    "mapping_enddate" => '$taximap.mapping_enddate',
                    "case" => array('$cond' => array(
                        'if' => array('$ne' => array('$taximap._id', null)),
                            'then' => '$taximap.mapping_startdate',
                            'else' => '$taximap.mapping_enddate'
                        )
                    )
                )
            ),
            array(
                '$sort' => array(
                    'mapping_id' => -1
                ),
            ),
            array('$group' => array(
                '_id' => array('id' => '$id'),
                'details' => array('$first' => array('id' => '$id','name' => '$name','mapping_startdate' => '$mapping_startdate','mapping_enddate' => '$mapping_enddate'))))
        );
        $result = $this->mongo_db->aggregate(MDB_PEOPLE, $arguments);
        if(!empty($result['result'])){
            foreach($result['result'] as $k => $t) {
                $t = $t['details'];
                $arr[$k]["_id"] = $t['id'];
                $arr[$k]["id"] = $t['id'];
                $arr[$k]["name"] = $t['name'];
                $arr[$k]["mapping_startdate"] = isset($t['mapping_startdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$t['mapping_startdate']) : "";
                $arr[$k]["mapping_enddate"] = isset($t['mapping_enddate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$t['mapping_enddate']) : "";
            }
        }
        return (!empty($result['result'])) ? $arr : array();
    }

    public function gettaxidetails($company_id, $country_id, $state_id, $city_id, $usertype)
    {
        $match_query = array("taxi_status" => 'A',"taxi_availability" => 'A',
							"taxi_country" => (int)$country_id,"taxi_state" => (int)$state_id,
							"taxi_city" => (int)$city_id,
							"model.model_status" => "A"
						);
        if ($usertype == 'C' || ($company_id != '' && $company_id != 0)) {
            $match_query['taxi_company'] = (int) $company_id;
        } else if($company_id != '' && $company_id != 0) {
            $match_query['taxi_company'] = (int) $company_id;
        }
        $arguments = array(
			array(
                '$lookup' => array(
                    'from' => MDB_MOTOR_MODEL,
                    'localField' => 'taxi_model',
                    'foreignField' => '_id',
                    'as' => 'model'
                )
            ),
            array('$unwind' =>  array( 'path' =>  '$model', 'preserveNullAndEmptyArrays' =>  true )),
            array(
                '$lookup' => array(
                    'from' => MDB_TAXI_DRIVER_MAPPING,
                    'localField' => '_id',
                    'foreignField' => 'mapping_taxiid',
                    'as' => 'taximap'
                )
            ),
            array('$unwind' =>  array( 'path' =>  '$taximap', 'preserveNullAndEmptyArrays' =>  true )),
            array('$match' => $match_query),
            array(
                '$project' => array(
                    "taxi_id" => '$_id',
                    "taxi_no" => '$taxi_no',
                    "mapping_id" => '$taximap._id',
                    "mapping_startdate" => '$taximap.mapping_startdate',
                    "mapping_enddate" => '$taximap.mapping_enddate',
                    "case" => array('$cond' => array(
                        'if' => array('$ne' => array('$taximap._id', null)),
                            'then' => '$taximap.mapping_startdate',
                            'else' => '$taximap.mapping_enddate'
                        )
                    )
                )
            ),
            array('$sort' => array('mapping_id' => -1)),
            array('$group' => array(
                '_id' => array('id' => '$taxi_id'),
                'details' => array('$first' => array('taxi_id' => '$taxi_id',
										'taxi_no' => '$taxi_no',
										'mapping_startdate' => '$mapping_startdate',
										'mapping_enddate' => '$mapping_enddate'))))
        );
        $result = $this->mongo_db->aggregate(MDB_TAXI, $arguments);
        if(!empty($result['result'])){
            foreach($result['result'] as $k => $t) {
                $t = $t['details'];
                $arr[$k]["taxi_id"] = $t['taxi_id'];
                $arr[$k]["taxi_no"] = isset($t['taxi_no']) ? $t['taxi_no'] : '';
                $arr[$k]["mapping_startdate"] = isset($t['mapping_startdate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$t['mapping_startdate']) : "";
                $arr[$k]["mapping_enddate"] = isset($t['mapping_enddate']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$t['mapping_enddate']) : "";
            }
        }
        return (!empty($result['result'])) ? $arr : array();
    }

    /** validate add contents **/
    public function validate_addcontents($arr)
    {
        return Validation::factory($arr)
        ->rule('meta_title', 'not_empty')
        ->rule('meta_keyword', 'not_empty')
        ->rule('meta_description', 'not_empty')
        ->rule('menu_name', 'not_empty');
    }
    /** inserting the contents **/
    public function addcontents($post)
    {
        $ops = array(array('$match'=>array('_id'=>array('$eq'=>(int)$post['menu_name']))),
                    array('$project'=>array('menu_name'=>'$menu_name')));
        $result = $this->mongo_db->aggregate(MDB_CMS,$ops);
        $menu_name = (!empty($result['result']))?$result['result'][0]['menu_name']:"";
        $data_set = array('menu'=>$menu_name,
                'meta_title'=>$post['meta_title'],
                'meta_keyword'=>$post['meta_keyword'],
                'meta_description'=>$post['meta_description'],
                'content'=>$post['content'],
                'content_status'=>1
                );
        $update = $this->mongo_db->updateOne(MDB_CMS,array('_id'=>(int)$post['menu_name']),array('$set'=>$data_set),array('upsert'=>false));
        return (count($update))?1:0;
    }
    //Check the menu name already exists while adding a content
    public static function menu_content_exits($post)
    {
        $mongodb = MangoDB::instance('default');
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'menu'=>1
            ]
        ];
        $result = $mongodb->find(MDB_CMS,array('_id'=>(int)$post['menu_name']),$options);
        if(isset($result[0]['menu'])){
            return true;
        }
        return false;
    }
    //selected manus
    public function get_menus()
    {
        $result = array();
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                '_id'=>1,
                'menu_name'=>1,
                'status_post'=>1
            ],
            'sort'=>[
                '_id'=>-1
                 ]
            ];
        $res = $this->mongo_db->find(MDB_CMS,['menu_name'=>['$ne'=>null]],$options);
        $res = $res;
        $res = commonfunction::change_key($res);
        if(!empty($res)){
            $result = array_map(
                    function($res) {
                        return array(
                            'menu_id' => $res['_id'],
                            'menu_name' => $res['menu_name'],
                            'status_post' => $res['status_post']
                        );
                    }, $res);
        }
        return $result;
    }
    public function check_companyid($cid)
    {
        $res = $this->mongo_db->count(MDB_COMPANY,array('_id'=>(int)$cid,'companydetails.company_status'=>'A'),array('_id'));
        return $res;
    }
    public function change_imagecount($taxi_id = '', $image_id = '')
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_TAXI,array('_id'=>(int)$taxi_id),array('taxi_sliderimage'));
        $count_image = (!empty($result))?$result['taxi_sliderimage']:0;
        if($count_image > 0) {
            $count_image = $count_image-1 ;
            $res = $this->mongo_db->updateOne(MDB_TAXI,array('_id'=>($taxi_id)),array('$set'=>array('taxi_sliderimage'=>(int)$count_image)),array('upsert'=>true));
        }
        return $count_image;
    }
    /**Validating for Add Menu**/
    public function validate_addmenu($arr)
    {
      return Validation::factory($arr)

            ->rule('menu_name', 'not_empty')
            ->rule('menu_name', 'min_length', array(':value', '2'))
            ->rule('menu_name', 'max_length', array(':value', '30'))
			->rule('menu_name', 'Model_Add::menu_name_exits', array(
						$arr['slug']
					))
            ->rule('slug','not_empty');
    }
    //To Add Menu Functionalities
    public function addmenu($post)
    {
        $status = $post['status_posts'];
        if ($status == 'Publish') {
            $status = 'P';
        } else if ($status == 'Unpublish') {
            $status = 'U';
        }
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
        $cms_rs = $this->mongo_db->find(MDB_CMS,[],$options);
        $cms_rs = (!empty($cms_rs))?array($cms_rs[0]['_id']=>0):array(1);
        reset($cms_rs);
        $cms_first_key = key($cms_rs);
        $cms_id = $cms_first_key + 1;
        $cms_menu = array('_id'=>$cms_id,
            'menu_name' => $post['menu_name'],
            'menu_link' => trim($post['slug'],'-'),
            'status_post' => $status,
            'order_status' => (int)0,
        );
        $cms_result = $this->mongo_db->insertOne(MDB_CMS,$cms_menu);
        return (empty($cms_result->getwriteErrors()))?1:0;
    }
    //Check the menu already exists
    public static function menu_name_exits($slug)
    {
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->Count(MDB_CMS,array("menu_link" => trim($slug,'-')));
        //~ print_r($result);exit;
        return ($result > 0)?false:true;
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

    /**Validating for Add Mile**/
    public function validate_addmile($arr)
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
    public static function check_evening_fare($night_fare)
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
    public static function check_valid_phone_number($phone, $regex)
    {
        if (preg_match($regex, $phone)) {
            return false;
        } else {
            return true;
        }
    }
    /** validating the banners images **/
    public function validate_addbanner($arr = "", $files_value_array = "")
    {
        return Validation::factory($arr)->rule('tags', 'not_empty')->rule('image_tag', 'not_empty')->rule('file', 'Upload::not_empty', array(
            $files_value_array['banner_image']
        ))->rule('file', 'Upload::type', array(
            $files_value_array['banner_image'],
            array(
                'jpg',
                'jpeg',
                'png',
                'gif'
            )
        ))->rule('file', 'Upload::size', array(
            $files_value_array['banner_image'],
            '2M'
        ));
    }

    /** Validating Faq **/
    public function validate_addfaq($arr)
    {
        $validation = Validation::factory($arr)->rule('faq_title', 'not_empty')
        ->rule('faq_title', 'Model_Add::checkfaqtitle', array(
            ':value'
        ))->rule('faq_details', 'not_empty');
        return $validation;
    }
    public static function addfaq($post)
    {
        $mongo_db = MangoDB::instance('default');
        $inc_id = Commonfunction::get_auto_id(MDB_PASSENGERS_FAQ);
        $insert_arr = array(
            '_id' => (int)$inc_id,
            'faq_title' => $post['faq_title'],
            'faq_details' => $post['faq_details'],
            'status' => 'A'
        );
        $result = $mongo_db->insertOne(MDB_PASSENGERS_FAQ,$insert_arr);
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    public static function check_minute_fare($minute_fare)
    {
        if (preg_match('/^\d+(\.\d+)*$/', $minute_fare)) {
            return true;
        } else {
            return false;
        }
    }
    public static function check_value_zero($value)
    {
        if ($value == "0") {
            return false;
        } else {
            return true;
        }
    }
    /**Validating for Create Login**/
    public function validate_createlogin($arr)
    {
        return Validation::factory($arr)->rule('firstname', 'not_empty')->rule('firstname', 'Model_Add::checkname', array(
            ':value'
        ))->rule('firstname', 'min_length', array(
            ':value',
            '4'
        ))->rule('firstname', 'max_length', array(
            ':value',
            '30'
        ))->rule('lastname', 'not_empty')->rule('no_of_login', 'not_empty')->rule('phone', 'numeric')->rule('phone', 'not_empty')->rule('phone', 'contact_phone', array(
            ':value'
        ))->rule('phone', 'Model_Add::checkphone_autocreate', array(
            ':value'
        ));
    }
    public static function checkname($firstname = "")
    {
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PEOPLE,array('name'=>$firstname."1"));
        return (($result >0) ? false : true );
    }
    public function create_login($post)
    {
        ///print_r($post);
        $no_of_login = $post['no_of_login'];
        for ($i = 1; $i <= $no_of_login; $i++) {
            $password          = Html::chars(md5($post['password']));
            $user_createdby    = $this->user_createdby;
            $mapping_createdby = $this->user_createdby;
            $cid               = $post['company_name'];
            $email             = $post['firstname'] . $i . "@taximobility.com";
            $name              = $post['firstname'] . $i;
            $phone             = $post['phone'] . $i;
            $taxi_no           = "TX" . $post['phone'] . $i;
            $current_date      = $this->currentdate_bytimezone;
            $current_date = Commonfunction::MongoDate(strtotime($current_date));
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
            $rs = $this->mongo_db->find(MDB_PEOPLE,[],$options);
            $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
            reset($res);
            $first_key = key($res);
            $driver_id = $first_key+1;
            $insert_people = array(
                '_id' => (int)$driver_id,
                'salutation' => $post['salutation'],
                'name' => $name,
                'address' => 'USA',
                'lastname' => $post['lastname'],
                'gender' => 'Male',
                'dob' => Commonfunction::MongoDate(strtotime('1990-05-05')),
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'created_date' => $current_date,
                'user_type' => 'D',
                'status' => ACTIVE,
                'user_createdby' => (int)$user_createdby,
                'login_country' => (int)$post['country'],
                'login_state' => (int)$post['state'],
                'login_city' => (int)$post['city'],
                'company_id' =>  (int)$post['company_id'],
                'driver_license_id' => $phone,
                'booking_limit' => (int)$post['booking_limit']
            );
            $result_people = $this->mongo_db->insertOne(MDB_PEOPLE,$insert_people);
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
            $rs = $this->mongo_db->find(MDB_DRIVER_INFO,[],$options);
            $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
            reset($res);
            $first_key = key($res);
            $inc_id = $first_key+1;
            $latitude          = "34.0500";
            $longitude         = "-118.2500";
            $driverinfo = array();
            $driverinfo[] = array('driver_id' => (int)$driver_id);
            $insert_data = array(
                '_id' => (int)$inc_id,
                'status'=>'F',
                'shift_status' => 'OUT',
                'update_date' => $current_date,
                'loc'=>array('type' => 'Point', 'coordinates'=>array((double)$longitude,(double)$latitude)),
                'driverinfo' => $driverinfo
            );
            $result = $this->mongo_db->insertOne(MDB_DRIVER_INFO,$insert_data);
            /** Taxi added**/
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
            $rs = $this->mongo_db->find(MDB_TAXI,[],$options);
            $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
            reset($res);
            $first_key = key($res);
            $taxi_id = $first_key+1;
            $insert_taxi = array(
                '_id' => (int)$taxi_id,
                'taxi_no' => $taxi_no,
                'taxi_type' => 1,
                'taxi_model' => (int)$post['taxi_model'],
                'taxi_company' => (int)$post['company_id'],
                'taxi_owner_name' => 'Ndot',
                'taxi_manufacturer' => 'Ndot',
                'taxi_colour' => 'red',
                'taxi_motor_expire_date' => Commonfunction::MongoDate(strtotime('2018-05-05 00:00:00')),
                'taxi_insurance_number' => $phone,
                'taxi_insurance_expire_date_time' => Commonfunction::MongoDate(strtotime('2018-05-05 00:00:00')),
                'taxi_pco_licence_number' => '2018-05-05',
                'taxi_pco_licence_expire_date' => Commonfunction::MongoDate(strtotime('2018-05-05 00:00:00')),
                'taxi_country' => (int)$post['country'],
                'taxi_state' => (int)$post['state'],
                'taxi_city' => (int)$post['city'],
                'taxi_capacity' => 5,
                'taxi_speed' => 80,
                'max_luggage' => 8,
                'taxi_fare_km' => 80,
                'taxi_createdby' => $current_date,
                'taxi_status' => ACTIVE
            );
            $result = $this->mongo_db->insertOne(MDB_TAXI,$insert_taxi);
            /** Taxi Mapping**/
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
            $rs = $this->mongo_db->find(MDB_TAXI_DRIVER_MAPPING,[],$options);
            $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
            reset($res);
            $first_key = key($res);
            $map_id = $first_key+1;
            $insert_mapping = array(
                '_id' => (int)$map_id,
                'mapping_driverid' => (int)$driver_id,
                'mapping_taxiid' => (int)$taxi_id,
                'mapping_companyid' => 1,
                'mapping_countryid' => (int)$post['country'],
                'mapping_stateid' => (int)$post['state'],
                'mapping_cityid' => (int)$post['city'],
                'mapping_startdate' => Commonfunction::MongoDate(strtotime($post['start_booking_date'])),
                'mapping_enddate' => Commonfunction::MongoDate(strtotime($post['end_booking_date'])),
                'mapping_status' => ACTIVE,
                'mapping_createdby' => (int)$mapping_createdby
            );
            $result = $this->mongo_db->insertOne(MDB_TAXI_DRIVER_MAPPING,$insert_mapping);
            /** Add Passenger**/
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
            $rs = $this->mongo_db->find(MDB_PASSENGERS,[],$options);
            $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
            reset($res);
            $first_key = key($res);
            $passenger_id = $first_key+1;
            $credit = array();
            $credit[] = array(
                'passenger_id' => (int)$passenger_id,
                'passenger_email' => $email,
                'card_type' => 'P',
                'creditcard_no' => 'bmRvdGVuY3JpcHRfNDExMTExMTExMTExMTExMQ==',
                'creditcard_cvv' => '567',
                'expdatemonth' => '5',
                'expdateyear' => '2020',
                'default_card' => '1',
                'createdate' => $current_date
            );
            $insert_pass = array(
                '_id' => (int)$passenger_id,
                'salutation' => $post['salutation'],
                'name' => $name,
                'lastname' => $post['lastname'],
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'creditcard_no' => 'bmRvdGVuY3JpcHRfNDU1NjM2ODM3MDg4NDQ3MQ==',
                'creditcard_cvv' => '567',
                'expdatemonth' => '01',
                'expdateyear' => '2020',
                'activation_status' => 1,
                'login_status' => 'N',
                'user_status' => 'A',
                'creditcard_details' => $credit
            );
            $result = $this->mongo_db->insertOne(MDB_PASSENGERS,$insert_pass);
        }
        return 1;
    }
    public static function view_login($post)
    {
        $mongodb = MangoDB::instance('default');
        $no_of_login    = $post['no_of_login'];
        $args = array(array('$project' => array('name' => '$name','phone' => '$phone')),
                            array('$sort' => array('_id'=>-1)),
                            array('$limit' => (int)$no_of_login)
                      );
        $result = $mongodb->aggregate(MDB_PEOPLE,$args);
        return (!empty($result['result'])?$result['result']:array());
    }
    public static function view_passengerlogin($post)
    {
        $mongodb = MangoDB::instance('default');
        $no_of_login    = $post['no_of_login'];
        $args = array(array('$project' => array('name' => '$name','phone' => '$phone')),
                            array('$sort' => array('_id'=>-1)),
                            array('$limit' => (int)$no_of_login)
                      );
        $result = $mongodb->aggregate(MDB_PASSENGERS,$args);
        return (!empty($result['result'])?$result['result']:array());
    }
    // Check driver licence Id is Already Exist or Not
    public static function checklicenceId($value)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_PEOPLE,array('driver_license_id'=>$value));
        return ($result > 0)?false:true;
    }

    //pco licence number already exist
    public static function checkpcolicenceNo($value)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_DRIVER_INFO,array('driverinfo.driver_pco_license_number'=>$value));
        return ($result > 0)?false:true;
    }
    //insurance number already exist
    public static function checkinsuranceNo($value)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_DRIVER_INFO,array('driverinfo.driver_insurance_number'=>$value));
        return ($result > 0)?false:true;
    }
    //national insurance number already exist
    public static function checkNationalinsuranceNo($value)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_DRIVER_INFO,array('driverinfo.driver_national_insurance_number'=>$value));
        return ($result > 0)?false:true;
    }
    // To Check taxi insurance number is Already Available or Not
    public static function check_taxinsurance_number($number)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_TAXI,array('taxi_insurance_number'=>$number),array('taxi_insurance_number'));
        return ($result>0)?false:true;
    }
    // To Check taxi pco licence number is Already Available or Not
    public static function check_taxipco_number($number)
    {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $result = $mongodb->count(MDB_TAXI,array('taxi_pco_licence_number'=>$number),array('taxi_pco_licence_number'));
        return ($result>0)?false:true;
    }
    //To Get all payment modules
    public function payment_modules()
    {
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[];
        $result = $this->mongo_db->find(MDB_PAYMENT_MODULES,[],$options);
        return (!empty($result))?$result:array();
    }

    public function smtp_settings(){
        $result = $this->mongo_db->findOne(MDB_SMTP_SETTINGS, array('_id' => 1), array('smtp'));
        $smtp_result[] = $result;
        return !empty($smtp_result) ? $smtp_result : array();
    }

    /**
    * It is used To get Company Brand Type
    * @method  getCompanyBrand
    * @return  $companyBrand
    */
    public function getCompanyBrand($companyId)
    {
        $companyBrand = $this->mongo_db->findOne(MDB_COMPANY,array("_id"=>(int)$companyId), array("companyinfo.company_brand_type"));
        return isset($companyBrand['companyinfo']['company_brand_type']) ? $companyBrand['companyinfo']['company_brand_type'] : "";
    } 
    
    public function validate_addpopularplace($arr)
    {
        return Validation::factory($arr)
			->rule('city_name', 'not_empty')
			->rule('label_name', 'not_empty')
			->rule('location_icon', 'not_empty')
			->rule('location_name', 'not_empty');		
    }
    
    public function add_popularplace($post_values){

		$city = explode('|',$post_values['city_name']);
		$city_id = $city[0];
		$city_name = $city[1];
		//echo '<pre>';print_r($post_values);exit;
		$count = count($post_values['location_name']);
		for($i=0;$i<$count;$i++){
			if(isset($post_values['label_name'][$i]) && $post_values['label_name'][$i] != ''){
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
					'label_name' => $post_values['label_name'][$i],
					'location_icon' => $post_values['location_icon'][$i],
					'location_name' => $post_values['location_name'][$i],
					'loc'=>array('type' => 'Point', 'coordinates'=>array((double)$post_values['longitude'][$i],
																		(double)$post_values['latitude'][$i]))
				);
				$result = $this->mongo_db->insertOne(MDB_POPULAR_PLACES,$insert_data);
			}
		}
		return 1;
	}
	
    public function validate_places($post)
    {
		$validation = array();
		$editpage = (isset($post['editpage'])) ? 1 : 0;
		
		$city = explode('|',$post['city_name']);
		$old_city = $city_id = $city[0];
		$city_name = $city[1];
		if($editpage == 1){
			$old_city = $post['old_city'];
		}
		if($old_city != $city_id){
			$match1 = ['city_id'=>(int)$city_id];
			$res = $this->mongo_db->findOne(MDB_POPULAR_PLACES,$match1);
			(!empty($res)) ? $validation['city'] = __('cityplaces_exists') : '';
		}		
		
		$i= ($editpage == 1) ? 0 : 1;
		$label_error = '';
		foreach($post['label_name'] as $l){
			$match2 = array('label_name'=> $l);
			($editpage == 1) ? $match2['city_id'] = array('$ne' => (int)$old_city) : '';
			$res = $this->mongo_db->findOne(MDB_POPULAR_PLACES,$match2);
			(!empty($res)) ? $label_error .= $i.'|'.__('labelname_exists').',' : '';		
			$i++;
		}
		($label_error != '') ? $validation['label_name'] = $label_error : '';		
		
		$i= ($editpage == 1) ? 0 : 1;
		$location_error = '';
		foreach($post['location_name'] as $l){			
			$match3 = array('location_name'=> $l);
			($editpage == 1) ? $match3['city_id'] = array('$ne' => (int)$old_city) : '';
			$res = $this->mongo_db->findOne(MDB_POPULAR_PLACES,$match3);
			(!empty($res)) ? $location_error .= $i.'|'.__('locationname_exists').',' : '';		
			$i++;
		}
		($location_error != '') ? $validation['location_name'] = $location_error : '';		
		return $validation;		
    }
    
    public function add_driver_import($post, $filename='')
    {
        $password       = Html::chars(md5($post['password']));
        $user_createdby = $this->user_createdby;
        $cid            = isset($post['company_name'])?$post['company_name']:1;
        $current_date   = $this->currentdate_bytimezone;
        //Get the last object id
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[ '_id'=>1 ],
            'sort'=>[ '_id'=>-1 ],
            'limit'=>1
        ];
        $rs = $this->mongo_db->find(MDB_PEOPLE,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $inc_id = $first_key+1;
        $param = array(
            '_id' => $inc_id,
            'name'=>$post['firstname'],
            'address'=>$post['address'],
            'lastname'=>$post['lastname'],
            'gender'=>$post['gender'],
            'dob'=> isset($post['dob'])?Commonfunction::MongoDate(strtotime($post['dob'])):'',
            'email'=>$post['email'],
            'phone'=>$post['phone'],
            'password'=>$password,
            'created_date' => Commonfunction::MongoDate(strtotime($current_date)),
            'user_type' => 'D',
            'status' => ACTIVE,
            'user_createdby' => (int)$user_createdby,
            'login_country' => (int)isset($post['country'])?$post['country']:DEFAULT_COUNTRY,
            'login_state' => (int)isset($post['state'])?$post['state']:DEFAULT_STATE,
            'login_city' => (int)isset($post['city'])?$post['city']:DEFAULT_CITY,
            'company_id' => (int) $cid,
            'driver_license_id' => $post['driver_license_id'],
            'booking_limit' => (int)20,
            'profile_picture' => $filename,
            'notification_setting' => 0,
            'driver_first_login' => 1,
            'login_status' => 'N',
            'availability_status' => 'A' // for checking package purpose
        );
        $result = $this->mongo_db->insertOne(MDB_PEOPLE,$param);
        $driver_id      = $inc_id;
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[ '_id'=>1 ]
        ];
        $query = $this->mongo_db->find(MDB_DRIVER_INFO,array('_id'=> (int)$driver_id),$options);
        $result = $query;
        if (count($result) == 0) {
            $arguments = array(array('$unwind' => '$stateinfo'),array('$unwind' => '$stateinfo.cityinfo'),
                array('$match' => array('_id'=> (int)$param['login_country'],'stateinfo.state_id'=> (int)$param['login_state'],'stateinfo.cityinfo.city_id'=> (int)$param['login_city'],'stateinfo.cityinfo.city_status' => 'A')),
                array('$project' => array('_id' => 0,'city_name' => '$stateinfo.cityinfo.city_name',)),
                array('$sort' => array('stateinfo.cityinfo.city_name' => 1),)
            );
            $cityresult = $this->mongo_db->aggregate(MDB_CSC,$arguments);
            $cityresult = $cityresult['result'];
            $address    = isset($cityresult[0]['city_name'])?$cityresult[0]['city_name']:'';
            $address    = str_replace(' ', '+', $address);
            $url        = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key=' . GOOGLE_GEO_API_KEY;
            $ch         = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $geoloc = curl_exec($ch);
            $json   = json_decode($geoloc);
            if (isset($json->status) == 'OK' && isset($json->results[0]->geometry->location->lat)) {
                $latitude  = $json->results[0]->geometry->location->lat;
                $longitude = $json->results[0]->geometry->location->lng;
            } else {
                $latitude  = (double)LOCATION_LATI;
                $longitude = (double)LOCATION_LONG;
            }
            $dt = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('UTC'));
            $ts = $dt->getTimestamp();
            $today = Commonfunction::MongoDate($ts);
            $insert_data = array(
                '_id' => $driver_id,
                'status'=>'F',
                'shift_status' => 'OUT',
                'update_date' => $today,
                'loc'=>array('type' => 'Point', 'coordinates'=>array($longitude,$latitude)),
            );
            $result = $this->mongo_db->insertOne(MDB_DRIVER_INFO,$insert_data);
        }
        /*if ($result) {
            $driver_insert_data = array(
                        'driverinfo' =>array(array(
                                'driver_license_expire_date' => Commonfunction::MongoDate(strtotime($post['driver_license_expire_date'])),
                                'driver_pco_license_number' => $post['driver_pco_license_number'],
                                'driver_pco_license_expire_date'=> Commonfunction::MongoDate(strtotime($post['driver_pco_license_expire_date'])),
                                'driver_insurance_number'=> $post['driver_insurance_number'],
                                'driver_insurance_expire_date'=> Commonfunction::MongoDate(strtotime($post['driver_insurance_expire_date'])),
                                'driver_national_insurance_number'=>$post['driver_national_insurance_number'],
                                'driver_national_insurance_expire_date'=> Commonfunction::MongoDate(strtotime($post['driver_national_insurance_expire_date'])))));
            $result = $this->mongo_db->updateOne(MDB_DRIVER_INFO,array('_id'=>(int)$driver_id),array('$set'=>$driver_insert_data),array('upsert'=>false));
            //driver referral insert
            $driverReferralCode = text::random($type = 'alnum', $length = 6);
            $inc_id = Commonfunction::get_auto_id(MDB_DRIVER_REF);
            $registered_driver_wallet=0;
            $insert_ref = array('_id' => (int)$inc_id,
                                'referred_driver_id' => 0,
                                'registered_driver_id' => (int)$driver_id,
                                'registered_driver_code' => $driverReferralCode,
                                'registered_driver_code_amount' => DRIVER_REF_AMOUNT,
                                'registered_driver_wallet' => (double)$registered_driver_wallet,
                                'referral_status' => 0,
                                'createdate' => Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone))
                                );
            $res_ref = $this->mongo_db->insertOne(MDB_DRIVER_REF,$insert_ref);
            /** For Single brand type drivers, add their details to company and company_info tables ( only for Single brand type ) **/
            /*if($post['brand_type'] == "S") {
                ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
                $options=[
                    'projection'=>['_id'=>1],
                    'sort'=>['_id'=>-1],
                    'limit'=>1
                    ];
                $rs = $this->mongo_db->find(MDB_COMPANY,[],$options);
                $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
                reset($res);
                $first_key = key($res);
                $first_cmy_inc_id = $first_key+1;
                $insert_cmy =  array('_id' => (int)$first_cmy_inc_id,
                    'companydetails' =>array(
                        'company_name' => $post['firstname'],
                        'company_address' => $post['address'],
                        'company_country' => $post['country'],
                        'company_state' => $post['state'],
                        'company_city' => $post['city'],
                        'userid' => (int)$driver_id,
                        'time_zone' => $_SESSION['timezone'],
                        'company_status' => 'A',
                        'header_bgcolor' => '#FFFFFF',
                        'menu_color' => '#000000',
                        'mouseover_color' => '#FFD800'
                    )
                );
                $res_ref = $this->mongo_db->insertOne(MDB_COMPANY,$insert_cmy);
                $companyId = $first_cmy_inc_id;
                $key="";
                $charset = "abcdefghijklmnopqrstuvwxyz";
                $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $charset .= "0123456789";
                $length = mt_rand (30, 35);
                for ($i=0; $i<$length; $i++)
                $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
                //company info table
                $update_cmy_info =  array(
                    'companyinfo' =>array(
                        'company_cid' => $companyId,
                        'company_brand_type' => $post['brand_type'],
                        'company_api_key' => $key
                    )
                );
                $result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>(int)$companyId),array('$set'=>$update_cmy_info),array('upsert'=>false));
                //update company id in people table
                $result = $this->mongo_db->updateOne(MDB_PEOPLE,array('_id'=>(int)$driver_id),array('$set'=>array('company_id' => $companyId)),array('upsert'=>false));
                $add_company = Model::factory('add');
            }else{
                $result = $this->mongo_db->findOne(MDB_COMPANY,array("_id"=>(int)$post['company_name']));
                $company_info_data = array(
                    'company_app_name' => $result['companyinfo']['company_app_name'],
                    'company_currency' => $result['companyinfo']['company_currency'],
                    'company_currency_format' => $result['companyinfo']['company_currency_format'],
                    'company_paypal_username' => $result['companyinfo']['company_paypal_username'],
                    'company_paypal_password' => $result['companyinfo']['company_paypal_password'],
                    'company_paypal_signature' => $result['companyinfo']['company_paypal_signature'],
                    'payment_method' => $result['companyinfo']['payment_method'],
                    'company_notification_settings' => $result['companyinfo']['company_notification_settings'],
                    'company_api_key' => $result['companyinfo']['company_api_key'],
                    'cancellation_fare' => $result['companyinfo']['cancellation_fare'],
                    'fare_calculation_type' => $result['companyinfo']['fare_calculation_type'],
                    'company_tax' => $result['companyinfo']['company_tax'],
                    'company_copyrights' => $result['companyinfo']['company_copyrights'],
                    'company_logo' => $result['companyinfo']['company_logo'],
                    'company_favicon' => $result['companyinfo']['company_favicon'],
                    'default_unit' => $result['companyinfo']['default_unit'],
                    'skip_credit_card' => $result['companyinfo']['skip_credit_card'],
                    'company_brand_type' => 'M'
                );
                $update_cmy_info =  array('companyinfo' =>$company_info_data);
                $result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>(int)$post['company_name']),array('$set'=>$update_cmy_info),array('upsert'=>false));
            }*/
            return $driver_id;
        /*} else {
            return 0;
        }*/
    }
    
     public function validate_adddriverimport($arr)
    {
        $validate = Validation::factory($arr)
            ->rule('firstname', 'not_empty')
            ->rule('firstname', 'min_length', array(':value', '4'))
            ->rule('firstname', 'max_length', array(':value', '30'))
            ->rule('lastname', 'not_empty')
            //->rule('dob', 'not_empty')
            ->rule('phone', 'not_empty')
            ->rule('phone', 'contact_phone', array(':value'))
            ->rule('phone', 'Model_Add::checkphone', array(':value'))
            ->rule('email', 'not_empty')
            ->rule('email', 'email')
            ->rule('email', 'max_length', array(':value', '50'))
            ->rule('email', 'Model_Add::checkemail', array(':value'))
            ->rule('password', 'not_empty')
            ->rule('password', 'min_length', array(':value', '6'))
            ->rule('password', 'max_length', array(':value', '20'))
            ->rule('password','valid_password',array(':value','/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'))
          
            ->rule('driver_license_id', 'not_empty')
            ->rule('driver_license_id', 'max_length', array(':value', '30'))
            ->rule('driver_license_id', 'Model_Add::checklicenceId', array(':value'))
            
            ->rule('address', 'not_empty');
  
        return $validate;
    }
    
    public function validate_addtaxiimport($arr)
    {
        $rule = Validation::factory($arr)
        ->rule('taxi_no', 'not_empty')
        ->rule('taxi_no', 'min_length', array(
            ':value',
            '4'
        ))->rule('taxi_no', 'max_length', array(
            ':value',
            '30'
        ))
        ->rule('taxi_no', 'regex', array(
            ':value',
            '/^[a-z0-9A-Z -]++$/iD'
        ))->rule('taxi_no', 'Model_Add::check_taxino', array(
            ':value'
        ))
        //->rule('taxi_type', 'not_empty')
            ->rule('taxi_model', 'not_empty')
            ->rule('taxi_owner_name', 'min_length', array(':value', '4'))
            ->rule('taxi_owner_name', 'max_length', array(':value', '30'))
            ->rule('taxi_owner_name', 'not_empty')
            ->rule('taxi_manufacturer', 'min_length', array(':value', '4'))
            ->rule('taxi_manufacturer', 'max_length', array(':value', '30'))
            ->rule('taxi_manufacturer', 'not_empty')
            ->rule('taxi_insurance_number', 'not_empty')
            ->rule('taxi_insurance_number', 'Model_Add::check_taxinsurance_number', array(
            ':value'
        ))->rule('taxi_insurance_expire_date', 'not_empty')
            ->rule('taxi_pco_licence_number', 'not_empty')
            ->rule('taxi_pco_licence_number', 'Model_Add::check_taxipco_number', array(
            ':value'
        ))->rule('taxi_pco_licence_expire_date', 'not_empty');
            //->rule('company_name', 'not_empty')
            //->rule('taxi_speed', 'not_empty')
            //->rule('taxi_speed', 'numeric')
            //->rule('taxi_speed', 'max_length', array(':value', '6'))
            //->rule('taxi_speed', 'Model_Add::check_taxi_speed_val', array(':value'))
        return $rule;
    }
    
      public function addtaxi_import($post)
    {
        $taxi_createdby   = $_SESSION['userid'];
        $cid         = isset($post['company_name'])?$post['company_name']:'1';
        //Get count of uploaded with taxi multiple images
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[ '_id'=>1 ],
            'sort'=>[ '_id'=>-1 ],
            'limit'=>1
        ];
        $rs = $this->mongo_db->find(MDB_TAXI,[],$options);
        $res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
        reset($res);
        $first_key = key($res);
        $inc_id = $taxi_id = $first_key+1;
        $taxi_model_id = $this->get_model_id($post['taxi_model']);
        $company_id = isset($post['company_name'])?$post['company_name']:'1';
        $taxi_data = array('_id' => (int)$inc_id,
            'taxi_no' => $post['taxi_no'],
            'taxi_type' => (int)isset($post['taxi_type'])?$post['taxi_type']:'1',
            'taxi_model' => (int)$taxi_model_id,
            'taxi_company' => (int)$company_id,
            'taxi_owner_name' => $post['taxi_owner_name'],
            'taxi_manufacturer' => $post['taxi_manufacturer'],
            'taxi_colour' => isset($post['taxi_colour'])?$post['taxi_colour']:'',
            'taxi_motor_expire_date' => Commonfunction::MongoDate(strtotime($post['taxi_motor_expire_date'])),
            'taxi_insurance_number' => $post['taxi_insurance_number'],
            'taxi_insurance_expire_date_time' => Commonfunction::MongoDate(strtotime($post['taxi_insurance_expire_date'])),
            'taxi_pco_licence_number' => $post['taxi_pco_licence_number'],
            'taxi_pco_licence_expire_date' => Commonfunction::MongoDate(strtotime($post['taxi_pco_licence_expire_date'])),
            'taxi_country' => (int)isset($post['country'])?$post['country']:DEFAULT_COUNTRY, 
            'taxi_state' => (int)isset($post['state'])?$post['state']:DEFAULT_STATE,
            'taxi_city' => (int)isset($post['city'])?$post['city']:DEFAULT_CITY,
            'taxi_capacity' => 0,
            'taxi_speed' => (float)isset($post['taxi_speed'])?$post['taxi_speed']:'50',
            'taxi_min_speed' => (float)isset($post['taxi_min_speed'])?$post['taxi_min_speed']:'10',
            'max_luggage' => (int)$post['minimum_luggage'],
            'taxi_fare_km' => (float)isset($post['taxi_fare_km'])?$post['taxi_fare_km']:'0',
            'taxi_createdby' => (int)$taxi_createdby,
            'taxi_status' => ACTIVE,
            'taxi_availability' => ACTIVE,
            'taxi_image' => '',
            'taxi_sliderimage' => (int)'0',
        );
        $result = $this->mongo_db->insertOne(MDB_TAXI,$taxi_data);
        # image upload
        $taxi_arrcount = array();
        /*for($i=0;$i<$count;$i++){
            $file_array = array();
            $file_array['name'] =  $files['size']['name'][$i];
            $file_array['type'] = $files['size']['type'][$i];
            $file_array['tmp_name'] = $files['size']['tmp_name'][$i];
            $file_array['error'] = $files['size']['error'][$i];
            $image_name = $taxi_id.'_'.$i;
            $taxi_arrcount[] = $i;
            $filename = Upload::save($file_array,$image_name,DOCROOT.TAXI_IMG_IMGPATH);
            $logo_image = Image::factory($filename);
            $path1=DOCROOT.TAXI_IMG_IMGPATH;
            $path=$image_name;
            Commonfunction::multipleimageresize($logo_image,TAXI_IMG_WIDTH, TAXI_IMG_HEIGHT,$path1,$image_name,90);
        }*/
        $update_arrimage = serialize($taxi_arrcount);
        $updateresult = $this->mongo_db->updateOne(MDB_TAXI,array('_id'=>(int)$inc_id),array('$set'=>array('taxi_serializeimage'=>$update_arrimage)),array('upsert'=>false));
        return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    
    public function get_model_id($model_name)
    {
		$options=['_id'];                 
        $result = $this->mongo_db->findOne(MDB_MOTOR_MODEL,['model_name'=> $model_name],$options);   
        
        if(isset($result['_id']))
        {
			return  $result['_id'];
		}
		else
		{
			return 0;
		}
    }
}
