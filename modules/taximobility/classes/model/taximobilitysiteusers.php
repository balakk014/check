<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains site users model details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilitySiteusers extends Model
{
    public function __construct()
    {
        $this->session         = Session::instance();
        $this->username        = $this->session->get("username");
        $this->admin_username  = $this->session->get("username");
        $this->admin_userid    = $this->session->get("id");
        $this->admin_email     = $this->session->get("email");
        $this->user_admin_type = $this->session->get("user_type");
        $this->currentdate     = Commonfunction::getCurrentTimeStamp();
        # created date
		$this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
        $this->mongo_db = MangoDB::instance('default');
        $this->lat             = '';
        $this->lon             = '';
        if (isset($_SESSION['id']) && ($_SESSION['id'] != '')) {
            $this->lat = isset($_SESSION['ip_lati']) ? $_SESSION['ip_lati'] : LOCATION_LATI;
            $this->lon = isset($_SESSION['ip_lng']) ? $_SESSION['ip_lng'] : LOCATION_LONG;
        } else {
            $this->lat = isset($_COOKIE['c_lati']) ? $_COOKIE['c_lati'] : LOCATION_LATI;
            $this->lon = isset($_COOKIE['c_lng']) ? $_COOKIE['c_lng'] : LOCATION_LONG;
        }
    }
    /**Validating User SignUP details**/
    public function validate_signup($arr)
    {
        return Validation::factory($arr)->rule('name', 'not_empty')->rule('name', 'min_length', array(
            ':value',
            '4'
        ))->rule('name', 'max_length', array(
            ':value',
            '32'
        ))->rule('lastname', 'not_empty')->rule('lastname', 'min_length', array(
            ':value',
            '1'
        ))->rule('lastname', 'max_length', array(
            ':value',
            '32'
        ))->rule('email', 'not_empty')->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('password', 'not_empty')->rule('password', 'min_length', array(
            ':value',
            '5'
        ))->rule('password', 'max_length', array(
            ':value',
            '50'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        )) /*->rule('repassword', 'not_empty')
        ->rule('repassword', 'min_length', array(':value', '5'))
        ->rule('repassword', 'max_length', array(':value', '50'))
        ->rule('repassword',  'matches', array(':validation', 'password', 'repassword'))*/ ;
    }
    public function validate_twittersignup($arr)
    {
        return Validation::factory($arr)->rule('name', 'not_empty')->rule('name', 'min_length', array(
            ':value',
            '4'
        ))->rule('name', 'max_length', array(
            ':value',
            '32'
        ))->rule('lastname', 'not_empty')->rule('lastname', 'min_length', array(
            ':value',
            '1'
        ))->rule('lastname', 'max_length', array(
            ':value',
            '32'
        ))->rule('email', 'not_empty')->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('password', 'not_empty')->rule('password', 'min_length', array(
            ':value',
            '5'
        ))->rule('password', 'max_length', array(
            ':value',
            '50'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('account_type', 'not_empty');
    }
    /** get site information**/
    public function get_site_info()
    {        
        $result =$this->mongo_db->findOne(MDB_SITEINFO,array(),array('app_description','site_tagline','site_copyrights','site_logo'));
        //echo "<pre>";print_r($result);exit;
        return (isset($result)) ? $result : array();
    }
    /** get site information**/
    public function get_sitecms_info()
    {
        //$res =$this->mongo_db->find(MDB_CMS,array('type'=>1),array('app_description','site_tagline','site_copyrights','site_logo'))->sort(array('order_status'=>1))->limit(5);
          ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'app_description'=>1,
                'site_tagline'=>1,
                'site_copyrights'=>1,
                'site_logo'=>1
            ],
            'sort'=>[
                'order_status'=>1
                ],
            'limit'=>5
        ];
        $res =$this->mongo_db->find(MDB_CMS,['type'=>1],$options);
        $result = $res;
        //echo "<pre>";print_r($result);exit;
        return (isset($result)) ? Commonfunction::change_key($result) : array();
    }
    /** get banner images **/
    public function get_banner_images()
    {
       // $res = $this->mongo_db->find(MDB_CMS,array('type'=>2),array('banner_image1','banner_image2','banner_image3','banner_image4','banner_image5'));
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[
                'banner_image1'=>1,
                'banner_image2'=>1,
                'banner_image3'=>1,
                'banner_image4'=>1,
                'banner_image5'=>1               
            ]
        ];
        $res = $this->mongo_db->find(MDB_CMS,['type'=>2],$options);
        $result = $res;
        
        return (isset($result)) ? Commonfunction::change_key($result) : array();
    }
    public function get_company_images($cid)
    {
        $args = array(array('$unwind' => '$company_cms'),
                      array('$match' => array('_id'=>(int)$cid,
                                              'type'=>2)),
                      array('$project' => array('banner_image' => '$company_cms.banner_image',
                                                'image_tag' => '$company_cms.image_tag'))
                    );
        $result = $this->mongo_db->aggregate(MDB_COMPANY,$args);
        //echo "<pre>";print_r($result['result']);exit;
        return (isset($result['result'])) ? $result['result'] : array();
    }
    public function get_company_cms($cid)
    {
        $args = array(array('$unwind' => '$company_cms'),
                      array('$match' => array('_id'=>(int)$cid)),
                      array('$project' => array('page_url' => '$company_cms.page_url',
                                                'content' => '$company_cms.content',
                                                'menu_name' => '$company_cms.menu_name'))
                    );
        $result = $this->mongo_db->aggregate(MDB_COMPANY,$args);
        //echo "<pre>";print_r($result['result']);exit;
        return (isset($result['result'])) ? $result['result'] : array();
    }
    public function get_company_cms_page($cid)
    {
        $args = array(array('$unwind' => '$company_cms'),
                      array('$match' => array('_id'=>(int)$cid)),
                      array('$project' => array('page_url' => '$company_cms.page_url',
                                                'content' => '$company_cms.content',
                                                'menu_name' => '$company_cms.menu_name'))
                    );
        $result = $this->mongo_db->aggregate(MDB_COMPANY,$args);
        //echo "<pre>";print_r($result['result']);exit;
        return (isset($result['result'])) ? $result['result'] : array();
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
    // Validating User Details while Updating User Details
    public function validate_user_settings($arr, $files_value_array)
    {
        return Validation::factory($arr, $files_value_array)->rule('file', 'Upload::type', array(
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
        ));
    }
    // Validating User Details while Updating User Details
    public function validate_carepicture_settings($arr, $files_value_array)
    {
        return Validation::factory($arr, $files_value_array)->rule('file', 'Upload::type', array(
            $files_value_array['image'],
            array(
                'jpg',
                'jpeg',
                'png',
                'gif'
            )
        ))->rule('file', 'Upload::size', array(
            $files_value_array['image'],
            '2M'
        ));
    }
    public function validate_user_profilesettings($arr)
    {
        return Validation::factory($arr)->rule('name', 'not_empty')->rule('name', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('name', 'min_length', array(
            ':value',
            '4'
        ))->rule('name', 'max_length', array(
            ':value',
            '32'
        ))->rule('lastname', 'not_empty')->rule('lastname', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('lastname', 'min_length', array(
            ':value',
            '1'
        ))->rule('lastname', 'max_length', array(
            ':value',
            '32'
        ))->rule('email', 'not_empty')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('email', 'email_domain')->rule('description', 'not_empty')->rule('description', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('description', 'min_length', array(
            ':value',
            '5'
        ))->rule('school', 'not_empty')->rule('education', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('education', 'not_empty')->rule('education', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ));
    }
    public function validate_user_profilesettings_optional($arr)
    {
        return Validation::factory($arr)->rule('phone', 'phone')->rule('dob', 'date')->rule('organisation', 'alpha_space')->rule('organisation', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('organisation', 'not_numeric')->rule('work', 'alpha_space')->rule('work', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('work', 'not_numeric')->rule('website', 'url')->rule('user_paypal_account', 'max_length', array(
            ':value',
            '60'
        ))->rule('user_paypal_account', 'email')->rule('user_paypal_account', 'Model_Authorize::unique_email')->rule('account_balance_amt', 'numeric')->rule('group', 'alpha_space')->rule('group', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('group', 'not_numeric');
    }
    // Validating Forgot Password Details
    public function validate_forgotpwd($arr)
    {
        return Validation::factory($arr)->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '100'
        ))->rule('email', 'not_empty');
    }
   // Validating Change Password Details
	public function validate_changepwd($arr) 
	{ 
		return Validation::factory($arr)       
			->rule('old_password', 'not_empty')
			->rule('old_password','valid_password',array(':value','/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'))
			->rule('new_password', 'min_length', array(':value', '6'))
			->rule('old_password', 'max_length', array(':value', '24'))
			->rule('new_password', 'not_empty')
			->rule('new_password','valid_password',array(':value','/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'))
			->rule('new_password', 'min_length', array(':value', '6'))
			->rule('new_password', 'max_length', array(':value', '24'))
			->rule('confirm_password', 'not_empty')
			->rule('confirm_password','valid_password',array(':value','/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'))
			->rule('confirm_password',  'matches', array(':validation', 'new_password', 'confirm_password'))
			->rule('confirm_password', 'min_length', array(':value', '6'))
			->rule('confirm_password', 'max_length', array(':value', '24'));
	}

	/**Validating Reset Password Details **/

	public function validate_resetpwd($arr) 
	{
		return Validation::factory($arr)       
			->rule('new_password', 'not_empty')
			//->rule('new_password','alpha_dash')
			->rule('new_password','valid_password',array(':value','/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'))
			->rule('new_password', 'max_length', array(':value', '16'))
			->rule('conf_password', 'not_empty')
			//->rule('conf_password','alpha_dash')
			//->rule('conf_password', array(':equals','new_password'))
			->rule('conf_password','valid_password',array(':value','/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'))
			->rule('conf_password', 'max_length', array(':value', '16'));
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
    
    public function validate_contact($arr)
    {
        $arr['email'] = trim($arr['email']);
        return Validation::factory($arr)->rule('name1', 'not_empty')->rule('name1', 'not_numeric')->rule('name1', 'min_length', array(
            ':value',
            '3'
        ))->rule('email', 'not_empty')->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('phone', 'numeric') //num
            ->rule('type', 'not_empty')->rule('subject', 'not_empty')->rule('message', 'not_empty');
    }
    public function get_logged_user_details($userid)
    {
        $result = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=>(int)$userid, 'user_type'=>'N'),array());
        //echo "<pre>";print_r($result);exit;
        return (isset($result)) ? $result : array();
    }
    /**Validating User conatct details**/
    public function validate_contact_form($arr)
    {
        $arr['email1'] = trim($arr['email1']);
        return Validation::factory($arr)->rule('name', 'not_empty')->rule('name', 'not_numeric')
        //->rule('name','alpha')  
            ->rule('name', 'min_length', array(
            ':value',
            '3'
        ))->rule('email1', 'not_empty')->rule('email1', 'email')->rule('email1', 'max_length', array(
            ':value',
            '50'
        ))->rule('message', 'not_empty')->rule('message', 'min_length', array(
            ':value',
            '10'
        ));
    }
    /**Validating company Signup details**/
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
        ))->rule('companyname', 'not_empty')->rule('companyname', 'min_length', array(
            ':value',
            '4'
        ))->rule('companyname', 'max_length', array(
            ':value',
            '30'
        ))->rule('company_name', 'Model_Siteusers::checkcompany', array(
            ':value',
            $arr['country'],
            $arr['state'],
            $arr['city']
        ))->rule('paypal_account', 'not_empty')->rule('paypal_account', 'email')->rule('paypal_account', 'max_length', array(
            ':value',
            '150'
        ))->rule('country', 'not_empty')->rule('city', 'not_empty')->rule('state', 'not_empty')->rule('address', 'not_empty')->rule('companyaddress', 'not_empty')->rule('mobile', 'not_empty')->rule('mobile', 'phone')->rule('mobile', 'min_length', array(
            ':value',
            '4'
        ))->rule('mobile', 'max_length', array(
            ':value',
            '36'
        ))->rule('mobile', 'Model_Siteusers::checkphone', array(
            ':value'
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
        ))->rule('domain_name', 'not_empty')->rule('domain_name', 'min_length', array(
            ':value',
            '4'
        ))->rule('domain_name', 'max_length', array(
            ':value',
            '10'
        ))->rule('domain_name', 'alpha_numeric', array(
            ':value',
            '/^[0-9]{1,}/'
        ))->rule('domain_name', 'Model_Add::checkdomain', array(
            ':value'
        ))->rule('time_zone', 'not_empty');
    }
    /** for getting city details **/
    public static function getcity_details($country_id, $state_id)
    {
        $mongo_db = MangoDB::instance('default');
        $ops = array(
				array('$unwind' => '$stateinfo'),
				array('$unwind' => '$stateinfo.cityinfo'),
				array('$match' => array('_id'=> (int)$country_id, 'stateinfo.state_id'=> (int)$state_id)),
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
						'stateinfo.cityinfo.city_name' => 1
					),
				)
			);
			$result = $mongo_db->aggregate(MDB_CSC,$ops);
			return (!empty($result['result']))?$result['result']:array();
    }
    /** for getting state details **/
    public function getstate_details($country_id)
    {
        $ops = array(
				array('$unwind' => '$stateinfo'),
				array('$match' => array('_id'=>(int)$country_id)),
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
    /** for vlaidate company login details **/
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
    /** menu listing in header pages **/
    public function menu_listingorder()
    {
        //$res = $this->mongo_db->find(MDB_CMS,array('status_post'=>'P','menu_name'=>array('$ne'=>null)),array())->sort(array('order_status'=>1));
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[
            'projection'=>[],
            'sort'=>[
                'order_status'=>1
            ]
        ];
        $res = $this->mongo_db->find(MDB_CMS,array('status_post'=>'P','menu_name'=>array('$ne'=>null)),$options);
        $result = $res;
        return (isset($result)) ? $result :array();
        
    }
    /** contact us validation**/
    public function validate_contactus($arr = "")
    {
        return Validation::factory($arr)->rule('name', 'not_empty')->rule('name', 'Model_Siteusers::checkurlgiven', array(
            ':value'
        )) //to avoid injection
            ->rule('email', 'not_empty')->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '100'
        ))->rule('email', 'Model_Siteusers::checkurlgiven', array(
            ':value'
        )) //to avoid injection
            ->rule('phone', 'phone', array(
            ':value'
        ))->rule('phone', 'Model_Siteusers::checkurlgiven', array(
            ':value'
        )) //to avoid injection
            ->rule('subject', 'not_empty')->rule('subject', 'Model_Siteusers::checkurlgiven', array(
            ':value'
        )) //to avoid injection
            ->rule('security_code', 'not_empty')->rule('security_code', 'Model_Siteusers::checkurlgiven', array(
            ':value'
        )) //to avoid injection
            ->rule('message', 'not_empty');
    }
    /** inserting a contacus info in table **/
    public function contactus_add($sign, $cid, $file_name, $file, $country_name="")
    {
        $message = ucfirst($sign['message']);
        if (COMPANY_CID == 0) {
            $inc_id = Commonfunction::get_auto_id(MDB_CONTACTS);
            if($file_name != "" )
			{
				$random_key = 'ATTACHMENT_'.$inc_id;
				$extension_arr = explode( '.', $file_name);                                                                
				$extension = end($extension_arr );
				$file_name = $random_key.".".$extension;
				$extension_arr = explode( '.', $file_name);                                                                
				$extension = end($extension_arr );				
				$file_name = $random_key.".".$extension;				
				if($file != '')
				{ 
					$file_path = $_SERVER['DOCUMENT_ROOT'].'/public/uploads/attachments/';
					$dirname = 'attachments';
					echo 'file_path = '.$file_path;
					if (!file_exists($file_path))
					{
						mkdir($file_path, 0777);
						chmod($file_path,0777);
					}
					$value = Upload::save($file,$file_name,$file_path);
					chmod($file_path.$file_name,0777);
				}
			}
            $insert_arr = array('_id' => (int)$inc_id,
                                'cid' => (int)$cid,                
                                'first_name' => $sign['first_name'],
                                'last_name'=>null,
                                'email' => $sign['email'],
                                'message' => $message,
                                'phone' => $sign['phone'],
                                'country' => $country_name,
                                'industry' => 0,
                                'no_of_employees' => (int)$sign['no_of_employees'],
                                'budget' => 0,
                                'product' => (int)$sign['product'],
                                'revenue' => (int)$sign['revenue'],
                                'attachment_file' => $file_name,
                                'sent_date' => Commonfunction::MongoDate(strtotime($this->currentdate)));					
            $result = $this->mongo_db->insertOne(MDB_CONTACTS,$insert_arr);
            return (empty($result->getwriteErrors())?$inc_id:0);       
			/*$insert_arr = array(
                '_id' => (int)$inc_id,
                'name' => $sign['name'],
                'email' => $sign['email'],
                'subject' => $sign['subject'],
                'message' => $message,
                'phone' => $sign['phone'],
                'sent_date' => Commonfunction::MongoDate(strtotime($this->currentdate))
            );*/			
        } else {
            /* Create Log */
            $ins_logid      = 0;
            $company_id     = $cid;
            $user_createdby = "";
            $log_message    = __('You have enquiry from ') . "," . __('name_label') . ":" . $sign['name'] . "," . __('message') . ":" . $sign['message'] . "," . __('phone_number') . ":" . $sign['phone'] . "," . __('Current_Location') . ":" . $sign['clocation'] . "," . __('Drop_Location') . ":" . $sign['droplocation'];
            $log_booking    = __('You have enquiry from ') . "," . __('name_label') . ":" . $sign['name'] . "," . __('message') . ":" . $sign['message'] . "," . __('phone_number') . ":" . $sign['phone'] . "," . __('Current_Location') . ":" . $sign['clocation'] . "," . __('Drop_Location') . ":" . $sign['droplocation'];
            $log_status     = $this->create_logs($ins_logid, COMPANY_CID, $user_createdby, $log_message, $log_booking);
            return $log_status;
            /* Create Log */
        }
    }

    //===============================================================================================================		
    public static function create_logs($booking_logid = '', $company_id = '', $log_userid = '', $log_message = '', $log_booking = '')
    {
		$Commonmodel  = Model::factory('Commonmodel');
        $current_time = $Commonmodel->getcompany_all_currenttimestamp($company_id);
        
		//$rs = $this->mongo_db->find(MDB_CONTACTS,array(),array('_id'))->sort(array('_id'=>-1))->limit(1);
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
                $rs = $this->mongo_db->find(MDB_CONTACTS,[],$options);
		$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
		reset($res);
		$first_key = key($res);
		$inc_id = $first_key+1;	
		$insert_arr = array(
			'_id' => (int)$inc_id,
			'booking_logid' => (int)$booking_logid,
            'log_userid' => (int)$company_id,
            'log_message' => $log_message,
            'log_booking' => $log_booking,
            'log_createdate' => $current_time
		);
		$result = $this->mongo_db->insertOne(MDB_CONTACTS,$insert_arr);
		return (empty($result->getwriteErrors())?$inc_id:0);
    }
    public static function get_company_taxi_image($company_id)
    {
        $mongo_db = MangoDB::instance('default');
        $args = array(array('$lookup' => array('from' => MDB_TAXI_DRIVER_MAPPING,
                                               'localField' => '_id',
                                               'foreignField' => 'mapping_taxiid',
                                               'as' => 'taxi_map')),
                      array('$unwind' => '$taxi_map'),
                      array('$match' => array('taxi_company'=>(int)$company_id)),
                      array('$project' => array('taxi_image' => '$taxi_image'))
                    );
        $result = $mongo_db->aggregate(MDB_TAXI,$args);
        //echo "<pre>";print_r($result['result']);exit;
        return (isset($result['result'])) ? $result['result'] : array();
    }
    public static function get_company_info($company_id)
    {
	    $mongo_db = MangoDB::instance('default');
        $args = array(array('$lookup' => array('from' => MDB_PEOPLE,
                                               'localField' => '_id',
                                               'foreignField' => 'company_id',
                                               'as' => 'people')),
                      array('$unwind' => '$people'),
                      array('$unwind' => '$companyinfo'),
                      array('$unwind' => '$companydetails'),
                      array('$match' => array('_id'=>(int)$company_id)),
                      array('$project' => array('company_phone_number' => '$companyinfo.company_phone_number',
                      'company_tagline' => '$companyinfo.company_tagline',
                      'company_name' => '$companydetails.company_name',
                      'company_address' => '$companydetails.company_address',
                      'header_bgcolor' => '$companydetails.header_bgcolor',
                      'menu_color' => '$companydetails.menu_color',
                      'mouseover_color' => '$companydetails.mouseover_color'))
                    );
        $result = $mongo_db->aggregate(MDB_COMPANY,$args);
        //echo "<pre>";print_r($result['result']);exit;
        return (isset($result['result'])) ? $result['result'] : array();
    }

    public function all_driver_map_list($company_id)
    {
		$result_arr = $temp_arr = array();
        $args = array(array('$lookup' => array('from' => MDB_DRIVER_INFO,
                                               'localField' => '_id',
                                               'foreignField' => '_id',
                                               'as' => 'driver')),
                      array('$unwind' => '$driver'),
                      array('$match' => array('user_type'=>'D',
                                              'company_id'=>(int)$company_id,
                                              'driver.status' => 'F',
                                              'driver.shift_status' => 'IN',
                                              'status' => 'A',
                                              'login_status' => 'S'
                                              )),
                      array('$project' => array('status' => '$driver.status',
                                                'driver_status' => '$driver.status',
                                                'name' => '$name',
                                                'profile_picture' => '$profile_picture',
                                                'shift_status' => '$driver.shift_status',
                                                'loc' => '$driver.loc.coordinates'
                                            ))
                    );
        $result = $this->mongo_db->Aggregate(MDB_PEOPLE,$args);
        //echo "<pre>";print_r($result['result']);exit;
        if(!empty($result['result'])){			
			foreach($result['result'] as $r){
				$temp_arr['driver_status'] = $r['driver_status'];
				$temp_arr['name'] = $r['name'];
				$temp_arr['profile_picture'] = $r['profile_picture'];
				$temp_arr['shift_status'] = $r['shift_status'];
				$temp_arr['latitude'] = $r['loc'][1];
				$temp_arr['longitude'] = $r['loc'][0];
				$result_arr[] = $temp_arr;
			}
		}        
        return $result_arr;
    }
    
    public static function checkurlgiven($value)
    {
        if (preg_match("/http/i", $value)) {
            return false;
        } else {
            return true;
        }
    }
    
    public function get_company_information($company_id=""){
        $args = array(array('$unwind' => '$company_cms'),
                array('$unwind' => '$companyinfo'),
                array('$unwind' => '$companydetails'),
                array('$match' => array('_id'=>(int)$company_id)),
                array('$project' => array('banner_image' => '$company_cms.banner_image',
                                        'image_tag' => '$company_cms.image_tag',
                                        'page_url' => '$company_cms.page_url',
                                        'content' => '$company_cms.content',
                                        'menu_name' => '$company_cms.menu_name',
                                        'company_phone_number' => '$companyinfo.company_phone_number',
                                        'company_tagline' => '$companyinfo.company_tagline',
                                        'company_name' => '$companydetails.company_name',
                                        'company_address' => '$companydetails.company_address',
                                        'header_bgcolor' => '$companydetails.header_bgcolor',
                                        'menu_color' => '$companydetails.menu_color',
                                        'mouseover_color' => '$companydetails.mouseover_color'
                                    ))
            );
        $result = $this->mongo_db->aggregate(MDB_COMPANY,$args);
        //echo "<pre>";print_r($result);exit;
        return (isset($result['result'])) ? $result['result'] : array();
    }
    
    /** Validate signin form fields **/
	
	public function validate_signin_form($post) 
	{
		return Validation::factory($post)
			->rule('country_code', 'not_empty')
			->rule('country_code', 'min_length', array(':value', '2'))
			->rule('country_code', 'max_length', array(':value', '5'))
			->rule('mobile_number', 'not_empty')
			->rule('mobile_number', 'min_length', array(':value', '7'))
			->rule('mobile_number', 'max_length', array(':value', '15'))
			->rule('password', 'not_empty')
			->rule('password', 'min_length', array(':value', '6')) 
			->rule('password', 'max_length', array(':value', '24'));
	}
	
	/** Validate passenger details **/

	public function validate_passenger_details($post) 
	{		
		$country_code = $post['country_code'];
		$phone = Html::chars($post['mobile_number']);
		$password = Html::chars(md5($post['password']));
        $fb_id = isset($post['fb_id']) ? $post['fb_id'] : "";
		$fb_access_token = isset($post['fb_access_token']) ? $post['fb_access_token'] : "";
        //print_r($post); exit;
		$project = array('_id','name','email','user_status','phone','country_code','profile_image');
		$match = array('country_code' => $country_code, 'phone' => $phone, 'password' => $password);
		$result = $this->mongo_db->findOne(MDB_PASSENGERS,$match,$project);
		if(count($result) > 0 && $result['user_status'] == 'A') {
			//Whenever user logged into the application, Add their IP and other details..
			$login_time = $this->currentdate;
			$update_array = array('last_login' => Commonfunction::MongoDate(strtotime($login_time)),'fb_user_id' => $fb_id,'fb_access_token' => $fb_access_token);
			$update = $this->mongo_db->updateOne(MDB_PASSENGERS,array('phone' => $phone),array('$set' => $update_array),array('upsert'=>true));
			
			$profile_img = (isset($result["profile_image"])?$result["profile_image"]:'');
			$this->session->set("passenger_name",$result["name"]);
			$this->session->set("id",$result["_id"]);
			$this->session->set("profile_image",$profile_img);
			$this->session->set("passenger_email",$result["email"]);
			$this->session->set("passenger_phone",$result["phone"]);
			$this->session->set("passenger_phone_code",$result["country_code"]);
            
            $this->session->delete("fb_name");
			$this->session->delete("fb_email");
			$this->session->delete("fb_id");
			$this->session->delete("fb_access_token");
            if(isset($post['remember_me'])) {
				$this->session->set("remember_me",1);
				$this->session->set("passenger_password",$post['password']);
			}
			return 1;
		} elseif(count($result) > 0 && $result['user_status'] == 'I') {
			return -1;
		} else {
			return 0;
		}
	}
	/** Validate forgot password form fields **/
	public function validate_forgot_password_form($post) 
	{
		return Validation::factory($post)
			->rule('country_code', 'not_empty')
			->rule('country_code', 'min_length', array(':value', '2'))
			->rule('country_code', 'max_length', array(':value', '5'))
			->rule('mobile_number', 'not_empty')
			->rule('mobile_number', 'phone', array(':value'))
			->rule('mobile_number', 'min_length', array(':value', '7'))
			->rule('mobile_number', 'max_length', array(':value', '20'));
	}
	
	/** Update forgot password to passenger  **/

	public function validate_forgot_password_details($post,$password) 
	{
		$country_code = $post['country_code'];
		$phone = Html::chars($post['mobile_number']);
		$result = $this->mongo_db->findOne(MDB_PASSENGERS,array('country_code' =>$country_code,'phone' =>$phone),array('_id','name','email'));
		if(count($result) >0) {
			$update_arr = array('password' => md5($password), 
								//'org_password' =>$password
								);
			$update = $this->mongo_db->updateOne(MDB_PASSENGERS,array('phone'=>$phone),array('$set'=>$update_arr),array('upsert'=>true));
			$result[] = $result;
			return $result;
		} else {
			return array();
		}
	}
	
	/** Validate card -- return array() **/

	function isVAlidCreditCard($ccnum,$type="",$returnobj=false)
	{
		$creditcard = array(
						"visa"=>"/^4\d{3}-?\d{4}-?\d{4}-?\d{4}$/",
						"mastercard"=>"/^5[1-5]\d{2}-?\d{4}-?\d{4}-?\d{4}$/",
						"discover"=>"/^6011-?\d{4}-?\d{4}-?\d{4}$/",
						"amex"=>"/^3[4,7]\d{13}$/",
						"diners"=>"/^3[0,6,8]\d{12}$/",
						"bankcard"=>"/^5610-?\d{4}-?\d{4}-?\d{4}$/",
						"jcb"=>"/^[3088|3096|3112|3158|3337|3528|3530]\d{12}$/",
						"enroute"=>"/^[2014|2149]\d{11}$/",
						"switch"=>"/^[4903|4911|4936|5641|6333|6759|6334|6767]\d{12}$/"
					);
		if(empty($type)) {
			$match = false;
			foreach($creditcard as $type=>$pattern) {
				if(preg_match($pattern,$ccnum) == 1) {
					$match = true;
					break;
				}
			}
			if(!$match) { 
				return 0; 
			} else {
				if($returnobj) {
					$return=new stdclass;
					$return->valid=$this->_checkSum($ccnum);
					$return->ccnum=$ccnum;
					$return->type=$type;
					return 1;
				} else {
					return 0;
				}
			}
		} else {
			if(@preg_match($creditcard[strtolower(trim($type))],$ccnum)==0) {
				return false;
			} else {
				if($returnobj) {
					$return = new stdclass;
					$return->valid = $this->_checkSum($ccnum);
					$return->ccnum = $ccnum;
					$return->type = $type;
					return 1;
				} else {
					return 1;
				}
			}
		}
	}
	
	/**  Function Used for validate credit cards  **/

	function _checkSum($ccnum)
	{
		$checksum = 0;
		for ($i=(2-(strlen($ccnum) % 2)); $i<=strlen($ccnum); $i+=2) {
			$checksum += (int)($ccnum{$i-1});
		}
		// Analyze odd digits in even length strings or even digits in odd length strings.
		for ($i=(strlen($ccnum)% 2) + 1; $i<strlen($ccnum); $i+=2) {
			$digit = (int)($ccnum{$i-1}) * 2;
			if ($digit < 10) { 
				$checksum += $digit;
			} else {
				$checksum += ($digit-9);
			}
		}
		if (($checksum % 10) == 0) 
			return true; 
		else 
			return false;
	}
    
    //Newly added For version5.0 upgrade
    
    /**
	 * Get recent trip details
	 * return array()
	 **/

	public function get_recent_trips($userid)
	{
		$result = array();
        $arguments = array(
            array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'driver_id',
                    'foreignField' => '_id',
                    'as' => 'people'
                )
            ),
            //array('$unwind' => '$people'),
            array('$unwind' => array('path' => '$people','preserveNullAndEmptyArrays' => true)),
            array('$match' => array('travel_status' => 1, 'passengers_id' => (int)$userid)),
            array(
                '$project' => array(
                    '_id' => 0,
                    'passengers_log_id' => '$_id',
                    'current_location' => '$current_location',
                    'drop_location' => '$drop_location',
                    'pickup_time' => '$pickup_time',
                    'driver_id' => '$driver_id',
                    'name' => '$people.name',
                    'lastname' => '$people.lastname',
                    'country_code' => array('$ifNull' => array('','$people.country_code')),
                    'phone' => '$people.phone',
                    'email' => '$people.email',
                    'profile_picture' => '$people.profile_picture'
                )
            ),
            array('$group' =>
				array('_id' => array('passengers_log_id'=> '$passengers_log_id'),
					'details' => array(
						'$first' => array(
							'passengers_log_id'=> '$passengers_log_id',
							'current_location' => '$current_location',
							'drop_location' => '$drop_location',
							'pickup_time' => '$pickup_time',
							'driver_id' => '$driver_id',
							'name' => '$name',
							'lastname' => '$lastname',
							'country_code' => '$country_code',
							'phone' => '$phone',
							'email' => '$email',
							'profile_picture' => '$profile_picture',
						)
					)
				)
			),
            array('$sort' => array('details.passengers_log_id' => -1)),
            array('$skip' => 0),
            array('$limit' => 3),
        );
        $results = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        
		$data = array();
		if($results['result']){
			foreach($results['result'] as $values){
				$values = $values['details'];
				
				$arr['pickup_time'] = isset($values['pickup_time'])?Commonfunction::convertphpdate("Y-m-d H:i:s",$values['pickup_time']):"";
				$arr['passengers_log_id'] = isset($values['passengers_log_id'])? $values['passengers_log_id']:0;
				$arr['current_location'] = isset($values['current_location'])? $values['current_location']:"";
				$arr['drop_location'] = isset($values['drop_location'])? $values['drop_location']:"";
				$arr['name'] = isset($values['name'])? $values['name']:"";
				$arr['lastname'] = isset($values['lastname'])? $values['lastname']:"";
				$arr['phone'] = isset($values['phone'])? $values['phone']:"";
				$arr['driver_id'] = isset($values['driver_id'])? $values['driver_id']:"";
				$arr['country_code'] = isset($values['country_code'])? $values['country_code']:"";
				$arr['email'] = isset($values['email'])? $values['email']:"";
				$arr['profile_picture'] = isset($values['profile_picture'])? $values['profile_picture']:"";
				$data[] = $arr;
			}
		}
		return $data;
	}
    
    /**
	 * Get upcomming trip details
	 * return array()
	 **/

	public function get_upcomming_trips($condition = false,$userid)
	{
		$match['passengers_id'] = (int)$userid;
		if($condition == false){
			$match['alert_notification'] = 0;
		}
		$query = array(
			array('$or' =>
				array(
					array('$and' => array(array('travel_status' => array('$in' => array(9,5,3,2))),array('driver_reply' => 'A') )),
					array('$and' => array(array('travel_status' => 0), array( 'now_after' => 1 ))),
				)
			)
		);
		$and_arr = array( "\$and" => $query);
		$matchquery = array_merge($match,$and_arr);
		//echo "<pre>"; print_r($matchquery); exit;
        $common_arguments = array(
			array('$match' => $matchquery),
            array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'driver_id',
                    'foreignField' => '_id',
                    'as' => 'people'
                )
            ),
            //array('$unwind' => '$people'),
            array('$unwind' => array('path' => '$people','preserveNullAndEmptyArrays' => true)),
			array(
                '$lookup' => array(
                    'from' => MDB_PASSENGERS,
                    'localField' => 'passengers_id',
                    'foreignField' => '_id',
                    'as' => 'passenger'
                )
            ),
            //array('$unwind' => '$passenger'),
            array('$unwind' => array('path' => '$passenger','preserveNullAndEmptyArrays' => true)),
			array(
                '$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => 'company_id',
                    'foreignField' => '_id',
                    'as' => 'company'
                )
            ),
           array('$unwind' => array('path' => '$company','preserveNullAndEmptyArrays' => true)),
        );
		
		$field_arguments = array(
			array(
				'$project' => array(
					'_id' => 0,
					'passengers_log_id' => '$_id',
					'current_location' => '$current_location',
					'drop_location' => '$drop_location',
					'pickup_time' => '$pickup_time',
					'driver_id' => '$driver_id',
					'name' => '$people.name',
					'lastname' => '$people.lastname',
					'country_code' => array('$ifNull' =>array('','$people.country_code')),
					'alert_notification' => array('$ifNull' =>array('','$alert_notification')),
					'phone' => '$people.phone',
					'email' => '$people.email',
					'profile_picture' => '$people.profile_picture',
					'alert_notification' => '$alert_notification',
					'travel_status' => '$travel_status', 
					'driver_reply' => '$driver_reply',
					'cardid' => '$passenger.creditcard_details.passenger_id',
					'company_id' => '$company._id',
					'cancellation_fare' => '$company.companyinfo.cancellation_fare',
				)
			),
			
			/*array('$skip' => 0),
			array('$limit' => 1),*/
			//array('$unwind' => '$cardid'),
			array('$group' =>
				array('_id' => array('passengers_log_id'=> '$passengers_log_id'),
					'details' => array(
						'$first' => array(
							'passengers_log_id'=> '$passengers_log_id',
							'current_location' => '$current_location',
							'drop_location' => '$drop_location',
							'pickup_time' => '$pickup_time',
							'driver_id' => '$driver_id',
							'name' => '$name',
							'lastname' => '$lastname',
							'country_code' => '$country_code',
							'alert_notification' => '$alert_notification',
							'phone' => '$phone',
							'email' => '$email',
							'profile_picture' => '$profile_picture',
							'alert_notification' => '$alert_notification',
							'travel_status' => '$travel_status', 
							'driver_reply' => '$driver_reply',
							'cardid' => '$cardid',
							'cancellation_fare' => '$cancellation_fare',
						)
					),
					'count' => array('$sum' => 1)
				)
			),	
			array('$sort' => array('details.passengers_log_id' => -1)),
		);
		$arguments = array_merge($common_arguments,$field_arguments);
		$results = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
		if($condition == false){
			$count = (!empty($results['result'])) ? count($results['result']) : 0;
			$passengers_log_id = (isset($results['result'][0]['details']['passengers_log_id'])) ? $results['result'][0]['details']['passengers_log_id'] : 0;
			$array = array("count" => $count,"trip_id" => $passengers_log_id);
			return  json_encode($array);
		}
		
		$data = array();
		
		if($results['result']){
			foreach($results['result'] as $values){
				$val = $values['details'];
				$arr = $val;
				$arr['pickup_time'] = isset($val['pickup_time'])?Commonfunction::convertphpdate("Y-m-d H:i:s",$val['pickup_time']):"";
				$arr['cancellation_fare'] = isset($val['cancellation_fare'])? $val['cancellation_fare']:"";
				$arr['creditcardCnt'] = (isset($val['cardid']) && count($val['cardid']) > 0 )? count($values['count']):0;
				$arr['name'] = isset($val['name'])? $val['name']:"";
				$arr['phone'] = isset($val['phone'])? $val['phone']:"";
				$arr['driver_id'] = isset($val['driver_id'])? $val['driver_id']:"";
				$arr['driver_reply'] = isset($val['driver_reply'])? $val['driver_reply']:"";
				$arr['travel_status'] = isset($val['travel_status'])? $val['travel_status']:"";
				$arr['profile_picture'] = isset($val['profile_picture'])? $val['profile_picture']:"";
				$data[] = $arr;
			}
		}
		//echo "<pre>"; print_r($data); exit;
		return $data;
	}
    
    /**
	 * Get passenger completed Trips
	 * return array()
	 **/
	public function get_passenger_completed_trips($find_count = false,$userid,$offset,$record)
	{
        $common_arguments = array(
            array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'driver_id',
                    'foreignField' => '_id',
                    'as' => 'people'
                )
            ),
            array('$unwind' => '$people'),
            array(
                '$lookup' => array(
                    'from' => MDB_TAXI,
                    'localField' => 'taxi_id',
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
                    'from' => MDB_TRANSACTION,
                    'localField' => '_id',
                    'foreignField' => 'passengers_log_id',
                    'as' => 'trans'
                )
            ),
            array('$unwind' => '$trans'),
            array('$match' => array('passengers_id' => (int)$userid, 'travel_status' => 1)),            
        );
        
        if($find_count==false){
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
            $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
            return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
        }else{
            $field_arguments = array(
                array(
                    '$project' => array(
                        '_id' => 0,
                        'passengers_log_id' => '$_id',
                        'used_wallet_amount' => '$used_wallet_amount',
                        'pickup_time' => '$pickup_time',
                        'actual_pickup_time' => '$actual_pickup_time',
                        'driver_id' => '$driver_id',
                        'distance' => '$trans.distance',
                        'name' => '$people.name',
                        'lastname' => '$people.lastname',
                        'payment_type' => '$trans.payment_type',
                        'fare' => '$trans.fare',
                        'model_name' => '$motor_model.model_name',
                        'distance_unit' => '$trans.distance_unit'    
                    )
                ),
                array('$sort' => array('passengers_log_id' => -1)),
                array('$skip' => (int)$offset),
                array('$limit' => (int)$record),
            );
            $arguments = array_merge($common_arguments,$field_arguments);
            $results = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
			//echo "<pre>"; print_r($results['result']); exit;
			$data = array();
			if($results['result']){
				foreach($results['result'] as $val){
					$arr = $val;
					$arr['pickup_time'] = isset($val['pickup_time'])?Commonfunction::convertphpdate("Y-m-d H:i:s",$val['pickup_time']):"";
					$arr['distance'] = isset($val['distance'])? $val['distance']: "";
					$arr['actual_pickup_time'] = isset($val['actual_pickup_time'])? Commonfunction::convertphpdate("Y-m-d H:i:s",$val['actual_pickup_time']): "";
					
					$data[] = $arr;
				}
			}
			return $data;
            return (!empty($results['result']) && isset($results['result']))?$results['result']:array();
        }
        
	}
	public function getaddress($lat,$lng)
	{ 
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false&key='.GOOGLE_GEO_API_KEY;	
		$json = @file_get_contents($url);
		$data=json_decode($json);
		$status = isset($data->status) ? $data->status : "";
		if($status=="OK")
		return $data->results[0]->formatted_address;
		else
		return false;
	}
	
    
    /**
	 * Get passenger Cancelled Trips
	 * return array()
	 **/

	public function get_passenger_cancelled_trips($find_count = false,$userid,$offset,$record)
	{
		/*$condition = array( "\$and" => array(array('passengers_id' => (int)$userid),
											 array("\$or"=>array(array( 'travel_status' => 9) , array( 'travel_status' => 4 ) ) ) ) );*/
		
		/*$and1 = array('$and' => array(array('travel_status' => 9),array('driver_reply' => 'C')));
		$condition = array('$and' => array(
										array('passengers_id' => (int)$userid),
										array('$or' => array(array('travel_status' => 4),$and1))
							));*/
		$match['passengers_id'] = (int)$userid;
		$query = array(
			array('$or' =>
				array(
					array('$and' => array(array('travel_status' => 4) )),
					array('$and' => array(array('travel_status' => 9), array('driver_reply' => 'C') )),
				)
			)
		);
		$and_arr = array( "\$and" => $query);
		$matchquery = array_merge($match,$and_arr);
		//echo "<pre>"; print_r($matchquery); exit;
        $common_arguments = array(
            array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'driver_id',
                    'foreignField' => '_id',
                    'as' => 'people'
                )
            ),
            array('$unwind' => array('path' => '$people','preserveNullAndEmptyArrays' => true)),
            array(
                '$lookup' => array(
                    'from' => MDB_TAXI,
                    'localField' => 'taxi_id',
                    'foreignField' => '_id',
                    'as' => 'taxi'
                )
            ),
            array('$unwind' => array('path' => '$taxi','preserveNullAndEmptyArrays' => true)),
            array(
                '$lookup' => array(
                    'from' => MDB_MOTOR_MODEL,
                    'localField' => 'taxi_modelid',
                    'foreignField' => '_id',
                    'as' => 'motor_model'
                )
            ),
            array('$unwind' => array('path' => '$motor_model','preserveNullAndEmptyArrays' => true)),
            array(
                '$lookup' => array(
                    'from' => MDB_TRANSACTION,
                    'localField' => '_id',
                    'foreignField' => 'passengers_log_id',
                    'as' => 'trans'
                )
            ),
            //array('$unwind' => '$trans'),            
            array('$match' => $matchquery),
            array('$sort' => array('_id' => -1)),
        );
        if($find_count==false){
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
            $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
            return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
        }else{
            $field_arguments = array(
                array(
                    '$project' => array(
                        '_id' => 1,
                        'passengers_log_id' => '$_id',
                        'used_wallet_amount' => '$used_wallet_amount',
                        'pickup_time' => '$pickup_time',
                        'actual_pickup_time' => '$actual_pickup_time',
                        'driver_id' => '$driver_id',
                        'distance' => '$trans.distance',
                        'name' => '$people.name',
                        'lastname' => '$people.lastname',
                        'payment_type' => '$trans.payment_type',
                        'fare' => '$trans.fare',
                        'model_name' => '$motor_model.model_name',
                        'distance_unit' => '$trans.distance_unit',
                        'used_wallet_amount' => '$used_wallet_amount'
                    )
                ),
                array('$skip' => (int)$offset),
                array('$limit' => (int)$record),
            );
            $arguments = array_merge($common_arguments,$field_arguments);
            $results = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
			$result = array();
			 if(!empty($results['result'])){
				foreach($results['result'] as $r){
					$temp_arr['tripid'] = !empty($r['_id']) ? $r['_id']:'';
					$temp_arr['driver_id'] = !empty($r['driver_id']) ? $r['driver_id']:'';
					$temp_arr['pickup_time'] = isset($r['pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['pickup_time']):'';
					$temp_arr['actual_pickup_time'] = isset($r['actual_pickup_time']) ? commonfunction::convertphpdate('Y-m-d H:i:s',$r['actual_pickup_time']):'';
					$temp_arr['passengers_log_id'] = isset($r['passengers_log_id']) ? $r['passengers_log_id']:'';
					$temp_arr['distance'] = (isset($r['distance']) && !empty($r['distance']))?$r['distance']:0;
					$temp_arr['name'] = isset($r['name']) ? $r['name']:'';
					$temp_arr['lastname'] = isset($r['lastname']) ? $r['lastname']:'';
					$temp_arr['payment_type'] = isset($r['payment_type'][0]) ? $r['payment_type'][0] : "";
					$temp_arr['fare'] = isset($r['fare'][0]) ? $r['fare'][0]:0;
					$temp_arr['model_name'] = isset($r['model_name']) ? $r['model_name']:'';
					$temp_arr['distance_unit'] = (isset($r['distance_unit']) && !empty($r['distance_unit']))?$r['distance_unit']:'';
					$temp_arr['used_wallet_amount'] = (isset($r['used_wallet_amount']) && !empty($r['used_wallet_amount']))?$r['used_wallet_amount']:0;
					$result[] = $temp_arr;
				}
			}
			//echo '<pre>';print_r($result);exit;
			return $result;
        }
	}
    
    public function get_driver_rating($driver_id = "")
	{
		$rating = 0;
        $arguments = array(           
            array('$match' => array('rating' => array('$gt' => 0), 'driver_id' => (int)$driver_id)),
            array(
                '$project' => array(
                    '_id' => 0,
                    'rating' => '$rating',
                )
            ),
            array('$group' => array("_id" => NULL,
                    "rating" => array( '$sum' => '$rating' ),
                    "count" => array( '$sum' => 1 ),
                )
            ),
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        $results = (!empty($result['result']) && isset($result['result'][0])) ? $result['result'][0] : array();
    
        if(count($results) > 0) {
			if(isset($results["rating"]) && $results["rating"] > 0 && isset($results["count"]) && $results["count"] > 0) {
				$rating = round($results["rating"] / $results["count"]);
			}
		}
        return $rating;
	}
    
    /** Check Image Exist or Not while Updating passenger Details **/ 

	public function check_passenger_photo($userid = "")
	{
        $result = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$userid),array('photo'));
        return (isset($result['photo'])) ? $result['photo'] : '';
	}
    
    /** Validate passenger form data **/

	/*public function validate_passenger_profile($posted_value) 
	{
		return Validation::factory($posted_value)
		->rule('name', 'not_empty')
		->rule('name', 'min_length', array(':value', '4'))
		->rule('name', 'max_length', array(':value', '32'))
		->rule('lastname', 'not_empty')
		->rule('lastname', 'min_length', array(':value', '1'))
		->rule('lastname', 'max_length', array(':value', '32'))
		->rule('email','not_empty')
		->rule('email','max_length',array(':value','50'))
		->rule('email','email_domain')
		->rule('phone', 'not_empty')
		->rule('profile_picture', 'Upload::type', array(':value', array('jpg','png','gif')));;
	}*/
    
    public function validate_passenger_profile($posted_value,$userid) 
	{
		return Validation::factory($posted_value)
		->rule('name', 'not_empty')
		->rule('name', 'min_length', array(':value', '4'))
		->rule('name', 'max_length', array(':value', '32'))
		->rule('lastname', 'not_empty')
		->rule('lastname', 'min_length', array(':value', '1'))
		->rule('lastname', 'max_length', array(':value', '32'))
		->rule('email','not_empty')
		->rule('email', 'min_length', array(':value', '8')) 
		->rule('email','max_length',array(':value','50'))
		->rule('email','email')
		->rule('email', 'Model_Siteusers::check_passenger_email', array(':value',$userid))
		->rule('country_code', 'not_empty')
		->rule('country_code', 'min_length', array(':value', '2'))
		->rule('country_code', 'max_length', array(':value', '5'))
		->rule('phone', 'not_empty')
		->rule('phone', 'Model_Siteusers::check_passenger_phone', array(':value',$userid,$posted_value['country_code']))
		->rule('profile_picture', 'Upload::type', array(':value', array('jpg','png','gif')));;
	}
    
     // Check Whether Passenger Email is Already Exist or Not
    public static function check_passenger_email($email = "", $userid = "")
    {
        $mongo_db = MangoDB::instance('default');
		$match_query=array('email' => $email,'_id'=>array('$ne'=>(int)$userid));
		$res = $mongo_db->count(MDB_PASSENGERS,$match_query);
        return ($res >0) ? false : true ;
    }
    // Check Whether Passenger phone is Already Exist or Not
    public static function check_passenger_phone($phone = "", $userid = "")
    {
        $mongo_db = MangoDB::instance('default');
		$match_query=array('phone' => $phone,'_id'=>array('$ne'=>(int)$userid));
		$res = $mongo_db->count(MDB_PASSENGERS,$match_query);
        return ($res>0) ? false : true ;
    }
    
    /** Update passenger details **/
	 
	public function update_passenger_details($post,$userid,$photo)
	{
		$mdate = $this->currentdate; 
		if(!empty($photo)) {
        $update_arr = array('name'=> $post['name'],
                            'lastname'=> $post['lastname'],
                            'email'=> $post['email'],
                            'phone'=> $post['phone'],
                            'address'=> $post['address'],
                            'country_code'=> $post['country_code'],
                            'updated_date' => Commonfunction::MongoDate(strtotime($mdate)),
                            'profile_image' => $photo
                            );
		} else {
			$update_arr = array('name'=> $post['name'],
                            'lastname'=> $post['lastname'],
                            'email'=> $post['email'],
                            'phone'=> $post['phone'],
                            'address'=> $post['address'],
                            'country_code'=> $post['country_code'],
                            'updated_date' => Commonfunction::MongoDate(strtotime($mdate))
                            );
		}
        $result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$userid),array('$set'=>$update_arr),array('upsert'=>true));
        if(empty($result->getwriteErrors())){
            $res = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$userid),array('country_code'));
            $phoneNumber = (isset($res)) ? $res["country_code"].$post['phone'] : $post['phone'];
            $this->session->set("passenger_name",$post['name']);
            $this->session->set("profile_image",$photo);
            $this->session->set("passenger_email",$post['lastname']);
            $this->session->set("passenger_phone",$phoneNumber);
            return 1;    
        }
		return 0;    
	}
    
   /** Get User Details at passenger Profile Page **/

	public function get_passenger_details($userid = null)
	{
        $args = array(array('$match' => array('_id' => (int)$userid)),
                      array('$project' => array('id'=>'$_id',
                                              'name'=>'$name',
                                              'lastname'=>'$lastname',
                                              'email'=>'$email',
                                              'phone'=>'$phone',
                                              'address'=>'$address',
                                              'profile_image'=>'$profile_image',
                                              'country_code'=>'$country_code'))
                      );
        $result = $this->mongo_db->Aggregate(MDB_PASSENGERS,$args);
        $data = array();
			if(count($result['result']) > 0){
				
					$arr = $result['result'][0];
					$arr['address'] = isset($result['result'][0]['address'])?$result['result'][0]['address']:"";
					$data[] = $arr;
				
			}
			
       // echo "<pre>"; print_r($data); exit; 
		return (isset($data)) ? $data : array();
	}
    
    /** Check Whether the Eneterd Password is Correct While passenger Change Password **/

	public function check_change_password($pass="",$userid="")
	{
		$userid = (isset($_SESSION['id']) ? $_SESSION['id'] : "");
        $result = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>$userid),array('password'));
		$password = (isset($result)) ? $result['password'] : '';
		$pass = md5($pass);
		return ($password == $pass) ? 1 : 0;
	}
    
    /** Change passenger password **/
	public function passenger_change_password($array_data,$post_value_array,$userid)
	{
		$datas = $this->mongo_db->findOne(MDB_PASSENGERS, array('_id' => (int)$userid),array('name','password','email'));
		$result[] = !empty($datas) ? $datas : array();		
		$password = !empty($result) ? $result[0]["password"]: '';
		if(md5($array_data['old_password']) == $password){
			$mdate = $this->currentdate;
			$password = md5($array_data['confirm_password']);
			$update_arr = array('password' => $password,
								//'org_password' =>$array_data['confirm_password'],
								'updated_date' => Commonfunction::MongoDate(strtotime($mdate)));
			$update = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id' => (int)$userid),array('$set'=>$update_arr),array('upsert'=>true));
			return $result;
		}else{
			return -1;
		}
	}
    
    public function viewtransaction_details($log_id = "")
	{
        $arguments = array(
            array(
                '$lookup' => array(
                    'from' => MDB_TRANSACTION,
                    'localField' => '_id',
                    'foreignField' => 'passengers_log_id',
                    'as' => 'trans'
                )
            ),
            array('$unwind' => '$trans'),
            array(
                '$lookup' => array(
                    'from' => MDB_COMPANY,
                    'localField' => 'company_id',
                    'foreignField' => '_id',
                    'as' => 'company'
                )
            ),
            array('$unwind' => '$company'),
            array(
                '$lookup' => array(
                    'from' => MDB_PEOPLE,
                    'localField' => 'driver_id',
                    'foreignField' => '_id',
                    'as' => 'people'
                )
            ),
            array('$unwind' => '$people'),
            array(
                '$lookup' => array(
                    'from' => MDB_TAXI,
                    'localField' => 'taxi_id',
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
                    'from' => MDB_PASSENGERS,
                    'localField' => 'passengers_id',
                    'foreignField' => '_id',
                    'as' => 'pass'
                )
            ),
            array('$unwind' => '$pass'),
            array('$match' => array('_id' => (int)$log_id)),
            array('$project' => array(
					'actual_pickup_time' => '$actual_pickup_time',
					'passengers_log_id' => '$_id',
					'driver_id' => '$driver_id',
					'current_location' => '$current_location',
					'drop_location' => '$drop_location',
					'drop_latitude' => '$drop_latitude',
					'drop_longitude' => '$drop_longitude',
					'pickup_latitude' => '$pickup_latitude',
					'pickup_longitude' => '$pickup_longitude',
					'distance' => '$trans.distance',
					'drop_time' => '$drop_time',
					'tripfare' => '$trans.tripfare',
					'minutes_fare' => '$trans.minutes_fare',
					'company_tax' => '$trans.company_tax',
					'used_wallet_amount' => '$used_wallet_amount',
					'fare' => '$trans.fare',
					'profile_picture' => '$people.profile_picture',
					'driver_name' => '$people.name',
					'driver_phone' => '$people.phone',
					'passenger_name' => '$pass.name',
					'org_tax' => '$company_tax',
					'passenger_email' => '$email',
					'passenger_phone' => '$pass.phone',
					'taxi_waiting_time' => '$trans.waiting_time',
					'taxi_waiting_cost' => '$trans.waiting_cost',
					'model_id' => '$motor_model._id',
					'model_name' => '$motor_model.model_name',
					'taxi_no' => '$taxi.taxi_no',
					'payment_type' => '$trans.payment_type',
					'actual_distance'=> '$trans.actual_distance',
					'trip_minutes' => '$trans.trip_minutes',
					'waiting_time' => '$trans.waiting_time',
					'distance_unit' => '$trans.distance_unit',
					'current_date' => '$trans.current_date',
					'nightfare' => '$trans.nightfare',
					'eveningfare' => '$trans.eveningfare',
					'discount_fare' => '$trans.promo_discount_fare',
					'passenger_discount' => '$trans.passenger_discount',
					'nightfare_applicable' => '$trans.nightfare_applicable',
					'eveningfare_applicable' => '$trans.eveningfare_applicable',
                )
            )
        );
        $results = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        //echo "<pre>"; print_r($results); exit; 
     
        $result_arr=array();
         if(!empty($results['result'])){		
			 foreach($results['result'] as $r){	
				$temp_arr=$results['result'][0];
				$temp_arr['actual_pickup_time'] = isset($r['actual_pickup_time'])? commonfunction::convertphpdate('Y-m-d H:i:s',$r['actual_pickup_time']):"";
				$temp_arr['passengers_log_id'] = isset($r['passengers_log_id'])?$r['passengers_log_id']:"";
				$temp_arr['driver_id'] = isset($r['driver_id'])?$r['driver_id']:"";
				$temp_arr['current_location'] = isset($r['current_location'])?$r['current_location']:"";
				$temp_arr['drop_location'] = isset($r['drop_location'])?$r['drop_location']:"";
				$temp_arr['drop_latitude'] = isset($r['drop_latitude'])?$r['drop_latitude']:"";
				$temp_arr['drop_longitude'] = isset($r['drop_longitude'])?$r['drop_longitude']:"";
				$temp_arr['pickup_latitude'] = isset($r['pickup_latitude'])?$r['pickup_latitude']:"";
				$temp_arr['pickup_longitude'] = isset($r['pickup_longitude'])?$r['pickup_longitude']:"";
				$temp_arr['distance'] = isset($r['distance'])?$r['distance']:"";
				$temp_arr['drop_time'] = isset($r['drop_time'])? commonfunction::convertphpdate('Y-m-d H:i:s',$r['drop_time']):"";
				$temp_arr['tripfare'] = isset($r['tripfare'])?$r['tripfare']:0;
				$temp_arr['minutes_fare'] = isset($r['minutes_fare'])?$r['minutes_fare']:0;
				$temp_arr['company_tax'] = isset($r['company_tax'])?$r['company_tax']:0;
				$temp_arr['used_wallet_amount'] = isset($r['used_wallet_amount'])?$r['used_wallet_amount']:0;
				$temp_arr['fare'] = isset($r['fare'])?$r['fare']:0;
				$temp_arr['profile_picture'] = isset($r['profile_picture'])?$r['profile_picture']:"";
				$temp_arr['driver_name'] = isset($r['driver_name'])?$r['driver_name']:"";
				$temp_arr['driver_phone'] = isset($r['driver_phone'])?$r['driver_phone']:"";
				$temp_arr['passenger_name'] = isset($r['passenger_name'])?$r['passenger_name']:"";
				$temp_arr['org_tax'] = isset($r['org_tax'])?$r['org_tax']:0;
				$temp_arr['passenger_email'] = isset($r['passenger_email'])?$r['passenger_email']:"";
				$temp_arr['passenger_phone'] = isset($r['passenger_phone'])?$r['passenger_phone']:"";
				$temp_arr['taxi_waiting_time'] = isset($r['taxi_waiting_time'])?$r['taxi_waiting_time']:"";
				$temp_arr['taxi_waiting_cost'] = isset($r['taxi_waiting_cost'])?$r['taxi_waiting_cost']:0;
				$temp_arr['model_id'] = isset($r['model_id'])?$r['model_id']:"";
				$temp_arr['model_name'] = isset($r['model_name'])?$r['model_name']:"";
				$temp_arr['taxi_no'] = isset($r['taxi_no'])?$r['taxi_no']:"";
				$temp_arr['payment_type'] = isset($r['payment_type'])?$r['payment_type']:"";
				$temp_arr['actual_distance'] = isset($r['actual_distance'])?$r['actual_distance']:"";
				$temp_arr['trip_minutes'] = isset($r['trip_minutes'])?$r['trip_minutes']:"";
				$temp_arr['waiting_time'] = isset($r['waiting_time'])?$r['waiting_time']:"";
				$temp_arr['distance_unit'] = isset($r['distance_unit'])?$r['distance_unit']:"";
				$temp_arr['current_date'] = isset($r['current_date'])?commonfunction::convertphpdate('Y-m-d H:i:s',$r['current_date']):"";
				$temp_arr['nightfare'] = isset($r['nightfare'])?$r['nightfare']:0;
				$temp_arr['eveningfare'] = isset($r['eveningfare'])?$r['eveningfare']:0;
				$temp_arr['passenger_discount'] = isset($r['passenger_discount'])?$r['passenger_discount']:0;
				$temp_arr['discount_fare'] = isset($r['discount_fare']) ? $r['discount_fare']:0;
				
				$temp_arr['eveningfare_applicable'] = isset($r['eveningfare_applicable'])?$r['eveningfare_applicable']:"";
				$temp_arr['nightfare_applicable'] = isset($r['nightfare_applicable'])?$r['nightfare_applicable']:"";
				$result_arr[] = $temp_arr;
			}			
		}        
		//echo '<pre>';print_r($result_arr); exit;
        return $result_arr;
    }
    
    public function get_location_details($trip_id)
	{
		$latlong = $res_arr = array();
        $arguments = array(
            array(
                '$lookup' => array(
                    'from' => MDB_LOCATION_HISTORY,
                    'localField' => '_id',
                    'foreignField' => 'trip_id',
                    'as' => 'loc_history'
                )
            ),
            //array('$unwind' => '$loc_history'),
            array('$unwind' => array('path' => '$loc_history','preserveNullAndEmptyArrays' => true)),
            array('$match' => array('_id' => (int)$trip_id)),
            array('$project' => array(
                'active_record' => '$loc_history.loc',
                'drop_latitude' => '$drop_latitude',
                'drop_longitude' => '$drop_longitude',
                'pickup_latitude' => '$pickup_latitude',
                'pickup_longitude' => '$pickup_longitude',
                'current_location' => '$current_location',
                'drop_location' => '$drop_location'
                )
            )
        );
        $results = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        
        $res_arr = !empty($results['result']) ? $results['result']:array();
        
        # Active record
		$active_record = isset($res_arr[0]['active_record']['coordinates']) ? $res_arr[0]['active_record']['coordinates']:array();				
		$coordinates='';
		if(!empty($active_record)){
			foreach($active_record as $a){
				$lat = '['.$a[1].',';
				$long = $a[0].'],';
				$coordinates .= $lat.$long;
			}
			($coordinates !='') ? $res_arr[0]['active_record'] = $coordinates : '';
		}	
        return $res_arr;
	}
    
    /**
	 * Get Passenger credit card details
	 * return array()
	 **/

	public function get_all_saved_card_details($passenger_id = "")
	{    
        $arguments = array(
            array('$unwind' => '$creditcard_details'),
            array('$match' => array('_id' => (int)$passenger_id)),
            array('$project' => array(
                'passenger_cardid' => '$creditcard_details.passenger_cardid',
                'passenger_id' => '$creditcard_details.passenger_id',
                'card_type' => '$creditcard_details.card_type',
                'expdatemonth' => '$creditcard_details.expdatemonth',
                'default_card' => '$creditcard_details.default_card',
                'expdateyear' => '$creditcard_details.expdateyear',
                'creditcard_no' => '$creditcard_details.creditcard_no',
                'creditcard_cvv' => '$creditcard_details.creditcard_cvv'
                )
            )
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS,$arguments);
        return(!empty($result) && isset($result['result']))?$result['result']:array();
	}
    
    /**
	 * Check card exist for passenger
	 **/
	public function check_card_exist($creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$passenger_id)
	{
		$creditcard_no = encrypt_decrypt('encrypt',$creditcard_no);
        $arguments = array(
            array('$unwind' => '$creditcard_details'),
            array('$match' => array('_id' => (int)$passenger_id, 'creditcard_details.creditcard_no' => $creditcard_no)),
            array('$project' => array('passenger_cardid' => '$creditcard_details.passenger_cardid')),
            array('$group' => array('_id' => NULL,'count' => array('$sum' => 1))),
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS,$arguments);
        return(!empty($result) && isset($result['result'][0]['count']))?$result['result'][0]['count']:0;
	}
    
    /**
	 * Add Passenger New Card Data
	 **/

	public function add_passenger_carddata($creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$passenger_id,$default,$card_type,$email,$preAuthorizeAmount=0,$pre_transaction_id='',$void_status=0,$fcardtype='')
	{   
		try {
			$email = urldecode($email);
			$creditcard_no = encrypt_decrypt('encrypt',$creditcard_no);
            //~ $args = array(array('$unwind' => '$creditcard_details'),
						  //~ array('$match' => array('_id' => (int)$passenger_id)),
						  //~ array('$project' => array('card_id' => '$creditcard_details.passenger_cardid'))
						//~ );
            //~ $keys = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
            //~ $cardid = (isset($keys['result'][0]['card_id']))?$keys['result'][0]['card_id']+1:1;
            $options=[
				'projection'=>[
				   'creditcard_details.passenger_cardid'=>1,                               
					],
				'sort'=>[
					'creditcard_details.passenger_cardid'=>-1
					],
				'limit'=>1
			];
			$passenger_rs = $this->mongo_db->find(MDB_PASSENGERS,[],$options);		
			$passenger_rs1 = reset($passenger_rs);
			$passenger_first_key = isset($passenger_rs1['creditcard_details'][0]['passenger_cardid']) ? $passenger_rs1['creditcard_details'][0]['passenger_cardid'] : 0;
			$cardid = $passenger_first_key+1;
			
			if($default == 1) {
				$val = array();
				if(!empty($keys['result'])){
					foreach($keys['result'] as $k => $v ){
						$val["creditcard_details.".$k.".default_card"] = 0;
					}
					$def_update          = $val;
					$update = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array('$set'=>$def_update),array('upsert' => true));
				}
                $update_array  = array("creditcard_details"=>array(
                    'passenger_cardid' => (int)$cardid,
                    'passenger_id' => (int)$passenger_id,
                    'passenger_email' => $email,
                    'card_type' => $card_type,
                    'creditcard_no' => $creditcard_no,
                    'creditcard_cvv' => $creditcard_cvv,
                    'card_holder_name' => '',
                    'expdatemonth' => $expdatemonth,
                    'expdateyear' => $expdateyear,
                    'default_card' => 1,
                    'createdate' => Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone)),
                    'pre_transaction_id' => $pre_transaction_id,
                    'pre_transaction_amount' => (double)$preAuthorizeAmount,
                    'card_type_description' => $fcardtype,
                    'void_status' => (int)$void_status
                    
                ));
                $card_result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array('$push'=>$update_array),array('upsert' => true));
			} else {                            
                $update_array  = array("creditcard_details"=>array(
                    'passenger_cardid' => (int)$cardid,
                    'passenger_id' => (int)$passenger_id,
                    'passenger_email' => $email,
                    'card_type' => $card_type,
                    'creditcard_no' => $creditcard_no,
                    'creditcard_cvv' => $creditcard_cvv,
                    'card_holder_name' => '',
                    'expdatemonth' => $expdatemonth,
                    'expdateyear' => $expdateyear,
                    'default_card' => 0,
                    'createdate' => Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone)),
                    'pre_transaction_id' => $pre_transaction_id,
                    'pre_transaction_amount' => (double)$preAuthorizeAmount,
                    'card_type_description' => $fcardtype,
                    'void_status' => (int)$void_status                    
                ));
                $card_result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array('$push'=>$update_array),array('upsert' => false));
			}
			return 0;
		} catch (Kohana_Exception $e) {
            echo "catch"; exit;
			return 1;
		}
	}
    
    /**
	 * Get Passenger credit card details
	 * return array()
	 **/

	public function get_single_saved_card_details($card_id = "",$userid = "")
	{
        $arguments = array(
            array('$unwind' => '$creditcard_details'),
            array('$match' => array('_id' => (int)$userid,'creditcard_details.passenger_cardid' => (int)$card_id)),
            array('$project' => array(
                'passenger_cardid' => '$creditcard_details.passenger_cardid',
                'passenger_id' => '$creditcard_details.passenger_id',
                'card_type' => '$creditcard_details.card_type',
                'expdatemonth' => '$creditcard_details.expdatemonth',
                'default_card' => '$creditcard_details.default_card',
                'expdateyear' => '$creditcard_details.expdateyear',
                'creditcard_no' => '$creditcard_details.creditcard_no',
                'creditcard_cvv' => array('$ifNull' => array('','$creditcard_details.creditcard_cvv'))
                )
            )
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS,$arguments);
        return(!empty($result) && isset($result['result']))?$result['result']:array();
	}
    
    /**
	 * Check card exist for passenger
	 **/
	public function edit_check_card_exist($passenger_cardid,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$passenger_id,$default)
	{
        $creditcard_no = encrypt_decrypt('encrypt',$creditcard_no);
        $arguments = array(
            array('$unwind' => '$creditcard_details'),
            array('$match' => array('_id' => (int)$passenger_id, 'creditcard_details.creditcard_no' => $creditcard_no, 'creditcard_details.passenger_cardid' => array('$ne' => (int)$passenger_cardid))),
            array('$project' => array('passenger_cardid' => '$creditcard_details.passenger_cardid','default_card' => '$creditcard_details.default_card')),
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS,$arguments);
        $result = (!empty($result) && isset($result['result']))?$result['result']:0;
		if(count($result) > 0) { 
			$default_card = $result[0]['default_card'];
			if($default_card == $default) {
				return 2;
			} else {
				return 1;
			}
		} else {
			return 0;
		}
	}
    
    /**
	 * Edit Passenger Card Data
	 **/
	public function edit_passenger_carddata($passenger_cardid,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$passenger_id,$default,$card_type,$preAuthorizeAmount=0,$pre_transaction_id='',$void_status=0,$fcardtype='')
	{
		try {
			$creditcard_no = encrypt_decrypt('encrypt',$creditcard_no);
            $args = array(
                array('$unwind' => '$creditcard_details'),
                array('$match' => array('_id' => (int)$passenger_id)),
                array('$project' => array('card_id' => '$creditcard_details.passenger_cardid'))
            );
            $keys = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
            $i =0;$default_array = array();
            if(!empty($keys['result'])){
                foreach($keys['result'] as $k => $v ){
                    if($v['card_id'] == $passenger_cardid){
                        $i = $k;
                    }
                    $default_array["creditcard_details.$k.default_card"] = 0;
                }
            }
			if($default == 1) {
                $update = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array('$set'=>$default_array),array('upsert' => true));
                $update_array = array(
                    "creditcard_details.$i.card_type" => $card_type,
                    "creditcard_details.$i.creditcard_no" => $creditcard_no,
                    "creditcard_details.$i.creditcard_cvv" => $creditcard_cvv,
                    "creditcard_details.$i.expdatemonth" => $expdatemonth,
                    "creditcard_details.$i.expdateyear" => $expdateyear,
                    "creditcard_details.$i.default_card" => 1,
                    "creditcard_details.$i.pre_transaction_id" => $pre_transaction_id,
                    "creditcard_details.$i.pre_transaction_amount" =>(double) $preAuthorizeAmount,
                    "creditcard_details.$i.card_type_description"=>$fcardtype,
                    "creditcard_details.$i.void_status" => (int)$void_status,
                );
                $result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id,'creditcard_details.passenger_cardid'=>(int)$passenger_cardid),array('$set'=>$update_array),array('upsert' => true));
			} else {
                $update_array = array(
                    "creditcard_details.$i.card_type" => $card_type,
                    "creditcard_details.$i.creditcard_no" => $creditcard_no,
                    "creditcard_details.$i.creditcard_cvv" => $creditcard_cvv,
                    "creditcard_details.$i.expdatemonth" => $expdatemonth,
                    "creditcard_details.$i.expdateyear" => $expdateyear,
                    "creditcard_details.$i.pre_transaction_id" => $pre_transaction_id,
                    "creditcard_details.$i.pre_transaction_amount" =>(double) $preAuthorizeAmount,
                    "creditcard_details.$i.card_type_description"=>$fcardtype,
                    "creditcard_details.$i.void_status" => (int)$void_status
                );
                $result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id,'creditcard_details.passenger_cardid'=>(int)$passenger_cardid),array('$set'=>$update_array),array('upsert' => true));
			}
			return 0;
		} catch (Kohana_Exception $e) {
			return 1;
		}
	}
    
    /** 
	 * Credit card delete function 
	 **/
	public function delete_card_details($passenger_cardid,$passenger_id)
	{
        $update_array = array('creditcard_details' => array('passenger_cardid' => (int)$passenger_cardid));
        $result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array('$pull'=>$update_array),array('upsert' => true));
        return (empty($result->getwriteErrors())) ? 1 : 0;
	}
    
    /**
	 * Get upcoming alert below 30 min trip
	 **/
	public function get_upcoming_trips_alert($passenger_id,$time='')
	{    
        $arguments = array(
            array('$match' => array('passengers_id' => (int)$passenger_id,
								'pickup_time' => array('$gte' => Commonfunction::MongoDate(strtotime($time))),
								'travel_status' => 9, 'driver_reply' => 'A' )),
            array('$project' => array('pickup_time' => '$pickup_time')),
            array('$sort' => array('_id' => -1)),
            array('$skip' => 0),
            array('$limit' => 1),
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        
        $results = (!empty($result) && isset($result['result']))?$result['result']:array();
        if(count($results) > 0) {
			$db_date = $results[0]["pickup_time"]->sec;
			//echo commonfunction::convertphpdate('Y-m-d H:i:s',$results[0]["pickup_time"]);exit;
			$to_time = strtotime($db_date);
			$from_time = strtotime($time);
			return round(abs($to_time - $from_time) / 60,2);
		}
        return 0;
	}
    
    // PHP strtotime compatible strings
	public function dateDiff($time1, $time2, $precision = 6)
	{
		// If not numeric then convert texts to unix timestamps
		if (!is_int($time1)) {
			$time1 = strtotime($time1);
		}
		if (!is_int($time2)) {
			$time2 = strtotime($time2);
		}
		// If time1 is bigger than time2
		// Then swap time1 and time2
		if ($time1 > $time2) {
			$ttime = $time1;
			$time1 = $time2;
			$time2 = $ttime;
		}
		// Set up intervals and diffs arrays
		$intervals = array('year','month','day','hour','minute','second');
		$diffs = array();
		// Loop thru all intervals
		foreach ($intervals as $interval) {
			// Create temp time from time1 and interval
			$ttime = strtotime('+1 ' . $interval, $time1);
			// Set initial values
			$add = 1;
			$looped = 0;
			// Loop until temp time is smaller than time2
			while ($time2 >= $ttime) {
				// Create new temp time from time1 and interval
				$add++;
				$ttime = strtotime("+" . $add . " " . $interval, $time1);
				$looped++;
			}
			$time1 = strtotime("+" . $looped . " " . $interval, $time1);
			$diffs[$interval] = $looped;
		}

		$count = 0;
		$times = array();
		// Loop thru all diffs
		foreach ($diffs as $interval => $value) {
			// Break if we have needed precission
			if ($count >= $precision) {
				break;
			}
			// Add value and interval 
			// if value is bigger than 0
			if ($value > 0) {
				// Add s if value is not 1
				/* if ($value != 1) {
					$interval .= "s";
				} */
				// Add value and interval to times array
				//$times[] = $value . " " . $interval;
				$times[] = $value;
				$count++;
			}
		}
		if(count($times) == 1) {
			$data = implode(":", $times);
			$times = explode(":","00:".$data);
		}
		if(count($times) == 2) {
			$data = implode(":", $times);
			$times = explode(":","00:".$data);
		}
		if(count($times) == 3) {
			$hours = (isset($times[0]) && strlen($times[0]) == 1) ? "0".$times[0] : $times[0];
			$minutes = (isset($times[1]) && strlen($times[1]) == 1) ? "0".$times[1] : $times[1];
			$seconds = (isset($times[2]) && strlen($times[2]) == 1) ? "0".$times[2] : $times[2];
			$times = explode(":",$hours.":".$minutes.":".$seconds);
		}
		return implode(":", $times);
	}
    
    /** check promocode used limit for wallet **/
   /* public function checkwalletpromocode($promo_code = "", $passenger_id = "", $company_id = "")
    {
		$match = array('promocode'=>$promo_code,'passenger_id'=>array('$in'=>array((int)$passenger_id)));
		$project = array('promocode', 'promo_discount', 'promo_used', 'start_date', 'expire_date', 'promo_limit');
		$promo_fetch = $this->mongo_db->findOne(MDB_PASSENGERS_PROMO,$match,$project);
        if (count($promo_fetch) > 0) {
			$promocode      = (isset($promo_fetch['promocode'])?$promo_fetch['promocode']:'');
            $promo_discount = (isset($promo_fetch['promo_discount'])?$promo_fetch['promo_discount']:'');
            $promo_used     = (isset($promo_fetch['promo_used'])?$promo_fetch['promo_used']:'');
            $promo_start_date    = (isset($promo_fetch['start_date'])?$promo_fetch['start_date']:'');
            $promo_expire_date   = (isset($promo_fetch['expire_date'])?$promo_fetch['expire_date']:'');
            $promo_limit    = (isset($promo_fetch['promo_limit'])?$promo_fetch['promo_limit']:'');
			$promo_start = Commonfunction::convertphpdate('Y-m-d H:i:s',$promo_start_date);
			$promo_expire = Commonfunction::convertphpdate('Y-m-d H:i:s',$promo_expire_date);
            $current_time = convert_timezone('now',TIMEZONE);           
            if (strtotime($promo_start) > strtotime($current_time)) {
                return 3;
            } else if (strtotime($promo_expire) < strtotime($current_time)) {
                return 4;
            } else {
				$promo_use_query = $this->mongo_db->count(MDB_PASSENGER_WALLET_LOG,array('promocode' => '$promo_code','passenger_id' => (int)'$passenger_id'));
				$promo_user_count = (!empty($promo_use_query)?$promo_use_query:0);
                if ($promo_user_count > 0 && $promo_user_count >= $promo_limit) {
                    return 2;
                } else {
                    return 1;
                }
            }
        } else {
            return 0;
        }
    }*/
    
    public function checkwalletpromocode($promo_code = "", $passenger_id = "")
    {
		$match = array('promocode' => $promo_code,'passenger_id' => (int)$passenger_id);
		$project = array('promocode','promo_discount','promo_used','start_date','expire_date','promo_limit');		
		$promo = $this->mongo_db->findOne(MDB_PASSENGERS_PROMO,$match,$project);
		$promo_fetch = (isset($promo)?$promo:array());
        if (count($promo_fetch) > 0) {
            $promocode      = (isset($promo_fetch['promocode'])?$promo_fetch['promocode']:'');
            $promo_discount = (isset($promo_fetch['promo_discount'])?$promo_fetch['promo_discount']:'');
            $promo_used     = (isset($promo_fetch['promo_used'])?$promo_fetch['promo_used']:'');
            $promo_start_date    = (isset($promo_fetch['start_date'])?$promo_fetch['start_date']:'');
            $promo_expire_date   = (isset($promo_fetch['expire_date'])?$promo_fetch['expire_date']:'');
            $promo_limit    = (isset($promo_fetch['promo_limit'])?$promo_fetch['promo_limit']:'');
			$promo_start = Commonfunction::convertphpdate('Y-m-d H:i:s',$promo_start_date);
			$promo_expire = Commonfunction::convertphpdate('Y-m-d H:i:s',$promo_expire_date);
			$current_time = convert_timezone('now',TIMEZONE);           
            if (strtotime($promo_start) > strtotime($current_time)) {
                return 3;
            } else if (strtotime($promo_expire) < strtotime($current_time)) {
                return 4;
            } else {
				$promo_trip_count=$promo_user_count=$promo_wallet_count=0;
				#trip count
				$match = array('promocode'=>$promo_code,
							   'passengers_id'=>(int)$passenger_id,
							   'travel_status'=> 1,
							   'driver_reply' => 'A'
							   );
				$promo_trip_count = $this->mongo_db->count(MDB_PASSENGERS_LOGS,$match);
				
				#wallet count
				$match1 = array('promocode'=>$promo_code,'passenger_id'=>(int)$passenger_id);
				$promo_wallet_count = $this->mongo_db->count(MDB_PASSENGER_WALLET_LOG,$match1);
				$promo_user_count = $promo_trip_count + $promo_wallet_count;
				
				if ($promo_user_count > 0 && $promo_user_count >= $promo_limit) {
					return 2;
				} else {
					return 1;
				}
            }
        } else {
            return 0;
        }
    }
    
    //to check the passenger have wallet amount to use
    public function get_passenger_wallet_amount($passenger_id)
    {
		$data = array();      
		$result = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$passenger_id),array("wallet_amount","name","lastname","email","phone","referral_code_amount","referral_code"));
		if(count($result) > 0) {
			$data[] = $result;
		}
		return (!empty($data) ? $data :array());
    }
    
    public function getpromodetails($promo_code = "", $passenger_id = "")
    {        
        $promo_fetch = $this->mongo_db->findOne(MDB_PASSENGERS_PROMO,array('promocode'=>$promo_code,'passenger_id'=>(int)$passenger_id),array("promocode","promo_discount","promo_used","promo_limit"));
        if (count($promo_fetch) > 0) {
            $promocode        = isset($promo_fetch['promocode']) ? $promo_fetch['promocode']: '';
            $promo_discount   = isset($promo_fetch['promo_discount']) ? $promo_fetch['promo_discount'] : '';
            $promo_used       = isset($promo_fetch['promo_used']) ? $promo_fetch['promo_used'] : '';
            $promo_limit      = isset($promo_fetch['promo_limit']) ? $promo_fetch['promo_limit'] :'';
            $promo_trip_count=$promo_user_count=$promo_wallet_count=0;
            
            #trip count
			$match = array('promocode'=>$promo_code,
						   'passengers_id'=>(int)$passenger_id,
						   'travel_status'=> 1,
						   'driver_reply' => 'A'
						   );
			$promo_trip_count = $this->mongo_db->count(MDB_PASSENGERS_LOGS,$match);
			
			#wallet count
			$match1 = array('promocode'=>$promo_code,'passenger_id'=>(int)$passenger_id);
			$promo_wallet_count = $this->mongo_db->count(MDB_PASSENGER_WALLET_LOG,$match1);
			$promo_user_count = $promo_trip_count + $promo_wallet_count;
			
            if ($promo_user_count > 0 && $promo_user_count >= $promo_limit) {
                return -1;
            } else {
                return $promo_discount;
            }
        } else {
            return 0;
        }
    }
    
    /** Get Payment gateway details by payment type **/
    public function payment_gateway_bytype($paymentType = "")
    {
		$match = array('payment_gateway_id' => (int)$paymentType, 'company_id' => 0);
		$project = array('payment_type' => '$payment_gateway_id',
						 'payment_gateway_username' => '$payment_gateway_username',
						 'payment_gateway_password' => '$payment_gateway_password',
						 'payment_gateway_key' => '$payment_gateway_signature',
                                                 'live_payment_gateway_username' => '$live_payment_gateway_username',
						 'live_payment_gateway_password' => '$live_payment_gateway_password',
						 'live_payment_gateway_key' => '$live_payment_gateway_signature',
						 //'gateway_currency_format' => '$currency_code',
						 'payment_method' => '$payment_method');
		$args = array(array('$match' => $match),
					array('$project' => $project));
		$res = $this->mongo_db->Aggregate(MDB_PAYMENT_GATEWAYS,$args);
        return (!empty($res) ? $res['result'] : array());
    }
    
    //insert into wallet log table
    public function add_wallet_log($fieldname_array, $values_array)
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
		$rs = $this->mongo_db->find(MDB_PASSENGER_WALLET_LOG,[],$options);
		$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
		reset($res);
		$first_key = key($res);
		$inc_id = $first_key+1;
		$insert_arr = array_combine($fieldname_array, $values_array);
		$insert_arr['passenger_id'] = isset($insert_arr['passenger_id']) ? (int)$insert_arr['passenger_id']:0;
		$insert_arr['amount'] = isset($insert_arr['amount']) ? (double)$insert_arr['amount']:0;
		$insert_arr['payment_status'] = isset($insert_arr['payment_status']) ? (int)$insert_arr['payment_status']:0;
		$insert_arr['payment_type'] = isset($insert_arr['payment_type']) ? (int)$insert_arr['payment_type']:0;
		$insert_arr['promocode_amount'] = isset($insert_arr['promocode_amount']) ? (double)$insert_arr['promocode_amount']:0;
		$insert_arr['createdate'] = Commonfunction::MongoDate(strtotime($this->currentdate));
		//print_r($insert_arr);exit;
		$insert_arr['_id'] = (int)$inc_id;
		
		$result = $this->mongo_db->insertOne(MDB_PASSENGER_WALLET_LOG,$insert_arr);
		return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    
   //insert credit card details if savecard is 1
    public function add_credit_card_details($update_array, $passenger_id ="")
    {
		$options=[
				'projection'=>[
				   'creditcard_details.passenger_cardid'=>1,                               
					],
				'sort'=>[
					'creditcard_details.passenger_cardid'=>-1
					],
				'limit'=>1
			];
		$passenger_rs = $this->mongo_db->find(MDB_PASSENGERS,[],$options);		
		$passenger_rs1 = reset($passenger_rs);
		$passenger_first_key = isset($passenger_rs1['creditcard_details'][0]['passenger_cardid']) ? $passenger_rs1['creditcard_details'][0]['passenger_cardid'] : 0;
		$passenger_cardid = $passenger_first_key+1;
		$update_array['passenger_cardid'] = (int)$passenger_cardid;
		$update_array['default_card'] = 0;
		$update_array['card_type'] = 'P';
		$update_array['status'] = 1;
		$update_array['createdate'] = $this->currentdate_bytimezone;
		$update_arr = array('creditcard_details' => $update_array);
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id' => (int)$passenger_id),array('$push'=>$update_arr),array('upsert'=>true));
		return (empty($result->getwriteErrors())) ? 1 : 0;
    }
    
    /** 
	 * Check user email already exists 
	 **/

	public function check_user_exists($email = "",$fb_user_id,$fb_access_token)
	{
		if($fb_user_id != "") {
            $project = array('_id','user_status','name','email','phone','country_code');
            $result = $this->mongo_db->findOne(MDB_PASSENGERS,array('fb_user_id' => $fb_user_id),$project);
			if(count($result) > 0 && $result['user_status'] == "A") {
				//~ if($fb_user_id != "" && $fb_access_token != "") {
					//~ $update_arr = array('fb_user_id' => $fb_user_id,"fb_access_token" => $fb_access_token);
                    //~ $update = $this->mongo_db->updateOne(MDB_PASSENGERS,array('email' => $email),
                                                      //~ array('$set' => $update_arr),array('upsert'=>true));
				//~ }
				$this->session->set("passenger_name",$result["name"]);
				$this->session->set("id",$result["_id"]);
				$this->session->set("passenger_email",$result["email"]);
				$this->session->set("passenger_phone",$result["phone"]);
				$this->session->set("passenger_phone_code",$result["country_code"]);
				return 1;
			} elseif(count($result) == 1 && $result['user_status'] == 'D') {
				return -1;
			} else {
				return 0;
			}
		}
		return 0;
	}
    
    /**
	 * Get passenger wallet amount
	 * return amount (int)
	 **/

	public function get_wallet_amount($userID)
	{
		$wallet_amount = 0;
        $result = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$userID),array('wallet_amount'));
		if(count($result) > 0 && isset($result['wallet_amount'])) {
			$wallet_amount = $result['wallet_amount'];
		}
		return $wallet_amount;
	}
	
	/**
	 * Update Passenger Card Data default
	 **/

	public function change_default_card($passenger_id,$passenger_cardid)
	{
		$match = array('_id'=>(int)$passenger_id);
		$args = array(array('$unwind' => '$creditcard_details'),
				  array('$match' => array('_id' => (int)$passenger_id)),
				  array('$project' => array('card_id' => '$creditcard_details.passenger_cardid'))
				);
		$keys = $this->mongo_db->aggregate(MDB_PASSENGERS,$args);
		if(!empty($keys['result'])){
			$def_zero = array();$def_one = array();$i=0;
			foreach($keys['result'] as $k => $v ){
				$def_zero["creditcard_details.".$i.".default_card"] = 0;
				if($passenger_cardid == $v['card_id']){
					$def_one["creditcard_details.".$i.".default_card"] = 1;
				}
				$i++;
			}
			$update = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$def_zero),array('upsert' => true));
			$update = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$def_one),array('upsert' => true));
		}
	}
	
	/**
	 * Update upcoming alert count
	 **/

	public function change_upcoming_alert_count($passenger_id,$trip_id)
	{
		$match = array('passengers_id' => (int)$passenger_id,'_id'=> (int)$trip_id);
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS_LOGS,$match,array('$set' => array('alert_notification' => 1)),array('upsert'=>true));
		return (empty($result->getwriteErrors())) ? 1 :0;
	}
	
	//Passenger Profile
    public function passenger_profile($userid)
    {	
		$match = array('_id' => (int)$userid);
		$project = array('name','lastname','org_password','password','salutation','email','referral_code','profile_image','country_code','phone','discount','user_status','login_from');
		$result = $this->mongo_db->findOne(MDB_PASSENGERS,$match,$project);
		return (!empty($result)) ? $result : array();
    }
    
    public function get_companycms_details($cid)
	{
		 $result = $temp = array();
		 $args = array(
						array('$unwind' => '$company_cms'),
						array('$project' => array('company_id' => '$company_cms.company_id',
							 'menu_name' => '$company_cms.menu_name',
							 'page_url' => '$company_cms.page_url',
							 'banner_image' => '$company_cms.banner_image',
							 'type' => '$company_cms.type'
							))		 
					);
		$res = $this->mongo_db->Aggregate(MDB_COMPANY,$args);
		if(!empty($res['result'])){
			$i=1;
			foreach($res['result'] as $r){
				
				$temp['id'] = $i;
				$temp['company_id'] = isset($r['company_id']) ? $r['company_id']:'';
				$temp['menu_name'] = isset($r['menu_name']) ? $r['menu_name'] :'';
				$temp['page_url'] = isset($r['page_url']) ? $r['page_url']:''; 
				$temp['banner_image'] = isset($r['banner_image']) ? $r['banner_image']:''; 
				$temp['type'] = isset($r['type']) ? $r['type']:''; 
				$result[] = $temp;
				$i++;
			}
		}
		return $result;
	} 
	
	public function get_alldriver_rating()
	{
		$rating = 0;
		$driver_ratings = array();
        $arguments = array(           
            array('$match' => array('rating' => array('$gt' => 0))),
            array(
                '$project' => array(
                    '_id' => 0,
                    'rating' => '$rating',
                )
            ),
            array('$group' => array("_id" => NULL,
                    "rating" => array( '$sum' => '$rating' ),
                    "count" => array( '$sum' => 1 ),
                )
            )
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS_LOGS,$arguments);
        $results = (!empty($result['result']) && isset($result['result'][0])) ? $result['result'][0] : array();
   
        if(count($results) > 0) {
			foreach($results as $r){
				if(isset($r["rating"]) && $r["rating"] > 0 && isset($r["count"]) && $r["count"] > 0) {
					$rating = round($r["rating"] / $r["count"]);
				}
				$driver_ratings[$r["driver_id"]] = $rating;
			}
			//~ if(isset($results["rating"]) && $results["rating"] > 0 && isset($results["count"]) && $results["count"] > 0) {
				//~ $rating = round($results["rating"] / $results["count"]);
			//~ }
		}
		//echo '<pre>';print_r($driver_ratings);exit;

        return $driver_ratings;
	}
	
	public function validate_wallet_amount($posted_value) 
	{
		return Validation::factory($posted_value)
		->rule('wallet_amount', 'not_empty')
		->rule('wallet_amount', 'Model_Siteusers::validate_wallet_amount_btn', array(':value'));
	}
	
	public static function validate_wallet_amount_btn($wallAmount)
	{
		if(isset($wallAmount) && $wallAmount != "")
		{
			if(($wallAmount < WALLET_AMOUNT_1) || ($wallAmount > WALLET_AMOUNT_3)) {
				return false;
			}
			return true;
		}
		return false;
	}
	public function validate_company_form($arr="")
	{
		return Validation::factory($arr)
			->rule('name', 'not_empty')
			->rule('email', 'not_empty')
			->rule('message', 'not_empty');
	}
	
	public function add_contact_us($post,$subject)
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
                $rs = $this->mongo_db->find(MDB_CONTACTS,[],$options);
		$res = (!empty($rs))?array($rs[0]['_id']=>0):array(1);
		reset($res);
		$first_key = key($res);
		$inc_id = $first_key+1;
		
		$insert_array=array('_id' => $inc_id,
						'first_name' => $post['name'],
						'email' => $post['email'],
						'subject' => $subject,
						'message'=> $post['message'],
						'phone'=> $post['phone'], 
						'sent_date' => Commonfunction::MongoDate(strtotime($this->currentdate)),
						'cid' => $inc_id,
						'contact_cid' => "",
						'country' => "",
						'last_name' => "",
						'country' => "",
						'no_of_employees' => "",
						'budget' => "",
						'product' => "",
						'revenue' => "",
						'industry' => "",
						'attachment_file' => "");
		
		$result = $this->mongo_db->insertOne(MDB_CONTACTS,$insert_array);
		return (empty($result->getwriteErrors())) ? 1 : 0;
		
	}
	
	/**
	 * Check credit card exists
	 **/

	public function check_creditcard_exist($passenger_id,$creditcard_number)
	{
		$card = preg_replace('/\s+/', '', $creditcard_number);
		$creditcard_number = encrypt_decrypt('encrypt',$card);
		$arguments = array(
			array('$unwind' => '$creditcard_details'),
            array('$match' => array('_id' => (int)$passenger_id,'creditcard_no' => $creditcard_number)),
            array('$project' => array(
					'_id' => 0,
					'passenger_cardid' => '$creditcard_details.passenger_cardid',
                )
            ),
			array('$group' => array("_id" => NULL,
                    "count" => array( '$sum' => 1 ),
                )
            )
        );
        $result = $this->mongo_db->aggregate(MDB_PASSENGERS,$arguments);
        return (!empty($result['result']) && isset($result['result'][0]['count'])) ? $result['result'][0]['count'] : 0;
	}
	
	//insert credit card details if savecard is 1
    public function update_passenger_amount($data)
    {
		$update_arr = array('wallet_amount' => $data['wallet_amount']);
		$result = $this->mongo_db->updateOne(MDB_PASSENGERS,array('_id' => (int)$data['id']),array('$set'=>$update_arr),array('upsert'=>false));		
		return (empty($result->getwriteErrors())) ? 1 : 0;
    }
        
    public function getLaterRequestData(){
		$getDateArr = explode(" ",$this->currentdate);
		$getDateVal = $getDateArr[0];
		$start_time = $getDateArr[0] . ' 00:00:00';
		$end_time   = $getDateArr[0]. ' 23:59:59';
		$match_array['travel_status'] = array('$in'=>array(0,7));
		$match_array['driver_reply'] = "";
		$match_array['bookingtype'] = 2;
		$match_array['pickup_time'] = array('$gte' => Commonfunction::MongoDate(strtotime($start_time)),'$lte'=> Commonfunction::MongoDate(strtotime($end_time)));
		$options=[
			'projection'=>[
				'_id'=>1,
				'company_id'=>1,
				'pickup_time'=>1,
				'auto_send_request'=>1,
				'trip_timezone'=>1
			],
			'sort'=>[
			  '_id'=>1
			]
			
		];
		$result = $this->mongo_db->find(MDB_PASSENGERS_LOGS,$match_array,$options);
		$data = array();
		if(count($result) > 0){
			foreach($result as $val){
				$arr = $val;
				$arr['passengers_log_id'] = $arr['_id'];
				$arr['auto_send_request'] = isset($arr['auto_send_request'])?$arr['auto_send_request']:0;
				$arr['trip_timezone'] = isset($arr['trip_timezone'])?$arr['trip_timezone']:'';
				$arr['pickup_time'] = isset($arr['pickup_time'])?Commonfunction::convertphpdate("Y-m-d H:i:s",$arr['pickup_time']):"";
				$arr['company_id'] = isset($arr['company_id'])?$arr['company_id']:0;
				$data[] = $arr;
				
			}
		}
		return $data;
	}
	
    
}


