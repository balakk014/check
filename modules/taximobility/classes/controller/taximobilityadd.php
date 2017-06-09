<?php defined('SYSPATH') or die('No direct script access.');
/****************************************************************
* Contains User Management(Users)details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Controller_TaximobilityAdd extends Controller_Siteadmin
{
    /**
     ****__construct()****
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->is_login();
    }
    public function is_login()
    {
        $session = Session::instance();
        //get current url and set it into session
        //========================================
        $this->session->set('requested_url', Request::detect_uri());
        /**To check Whether the user is logged in or not**/
        if (!isset($this->session) || (!$this->session->get('userid')) && !$this->session->get('id')) {
            Message::error(__('login_access'));
            $this->request->redirect("/admin/login/");
        }
        return;
    }
   
    public function action_fare()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ($usertype != 'C') {
            $this->request->redirect("admin/login");
        }
        $add_fare      = Model::factory('add');
        /**To get the form submit button name**/
        $signup_submit = arr::get($_REQUEST, 'submit_addfare');
        $errors        = array();
        $post_values   = array();
        $motor_details = $add_fare->motor_details();
        $model_details = $add_fare->model_details();
        if ($signup_submit && Validation::factory($_POST)) {
            $post_values  = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
            $exist_models = $add_fare->exist_models($post_values['model_name']);
            if (count($exist_models) == 1) {
                Message::error(__('fare_added_already'));
                $this->request->redirect("manage/fare");
            }
            $validator = $add_fare->validate_addfare(arr::extract($post_values, array(
                'model_name',
                'model_size',
                'waiting_time',
                'base_fare',
                'min_fare',
                'cancellation_fare',
                'below_km',
                'above_km',
                'night_charge',
                'night_timing_from',
                'night_timing_to',
                'night_fare',
                'min_km',
                'below_and_above_km',
                'minutes_fare',
                'evening_charge',
                'evening_timing_from',
                'evening_timing_to',
                'evening_fare'
            )));
            if ($validator->check()) {
                $signup_id = $add_fare->addfare($post_values);
                if ($signup_id == 1) {
                    Message::success(__('sucessfull_added_fare_company'));
                    $this->request->redirect("manage/fare");
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/add_fare')->bind('validator', $validator)->bind('errors', $errors)->bind('postvalue', $post_values)->bind('motor_details', $motor_details)->bind('model_details', $model_details);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_fare');
        $this->template->page_title = __('add_fare');
        $this->template->content    = $view;
    }

    public function action_company()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ($usertype != 'A' && $usertype != 'S' && $usertype != 'DA') {
            $this->request->redirect("admin/login");
        }
        $add_model       = Model::factory('add');
        /**To get the form submit button name**/
        $signup_submit   = arr::get($_REQUEST, 'submit_addcompany');
        $country_details = $add_model->country_details();
        $city_details    = $add_model->city_details();
        $state_details   = $add_model->state_details();
        $package_details = array();
        $currencysymbol  = $this->currencysymbol;
        $currencycode    = $this->all_currency_code;
        $errors          = array();
        $post_values     = array();
        if ($signup_submit && Validation::factory($_POST, $_FILES)) {
            $post_values                  = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
            $post_values['company_image'] = $_FILES['company_image'];
            $validator                    = $add_model->validate_addcompany(arr::extract($post_values, array(
                'firstname',
                'lastname',
                'email',
                'password',
                'repassword',
                'phone',
                'address',
                'company_name',
                'domain_name',
                'company_address',
                'country',
                'state',
                'city',
                'currency_code',
                'currency_symbol',
                'time_zone',
                'company_image'
            )), $_FILES);
            if ($validator->check())
                {
                $signup_id = $add_model->addcompany($post_values, $_FILES);
                if ($signup_id == 1) {
                    $mail              = "";
                    $replace_variables = array(
                        REPLACE_LOGO => EMAILTEMPLATELOGO,
                        REPLACE_SITENAME => COMPANY_SITENAME,
                        REPLACE_DOMAINNAME => $post_values['domain_name'],
                        REPLACE_USERNAME => $post_values['firstname'],
                        REPLACE_EMAIL => $post_values['email'],
                        REPLACE_PASSWORD => $post_values['password'],
                        REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                        REPLACE_SITEEMAIL => CONTACT_EMAIL,
                        REPLACE_SITEURL => URL_BASE,
                        REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                        REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR
                    );
                    $message           = $this->emailtemplate->emailtemplate(DOCROOT . TEMPLATEPATH . 'registertemp.html', $replace_variables);
                    $to                = $_POST['email'];
                    $from              = CONTACT_EMAIL;
                    $subject           = __('registration_success');
                    $redirect          = "manage/company";
                    $smtp_result       = $add_model->smtp_settings();
                    if (!empty($smtp_result) && ($smtp_result[0]['smtp'] == 1)) {
                        include($_SERVER['DOCUMENT_ROOT'] . "/modules/SMTP/smtp.php");
                    } else {
                        // To send HTML mail, the Content-type header must be set
                        $headers = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        // Additional headers
                        $headers .= 'From: ' . $from . '' . "\r\n";
                        $headers .= 'Bcc: ' . $to . '' . "\r\n";
                        mail($to, $subject, $message, $headers);
                    }
                    
                    //Company count updated with Crm
                    if (CRM_UPDATE_ENABLE==1 && class_exists('Thirdpartyapi')) {
                        if (method_exists('Thirdpartyapi','crm_add_company_count')) {                                    
                                    $thirdpartyapi= Thirdpartyapi::instance();
                                    $thirdpartyapi->crm_add_company_count();                
                                }
                    }
                    Message::success(__('sucessfull_added_company'));
                    $this->request->redirect("manage/company");
                } else if ($signup_id == 2) {
                    Message::success(__('invalid_image_uploaded'));
                    $this->request->redirect("add/company");
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/add_company')->bind('validator', $validator)->bind('errors', $errors)->bind('country_details', $country_details)->bind('city_details', $city_details)->bind('state_details', $state_details)->bind('package_details', $package_details)->bind('postvalue', $post_values)->bind('currency_symbol', $currencysymbol)->bind('currency_code', $currencycode);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_company');
        $this->template->page_title = __('add_company');
        $this->template->content    = $view;
    }
    
    public function action_create_login()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $add_model      = Model::factory('add');
        $cid            = $this->request->param('id');
        if ($cid != '') {
            $check_cid = $add_model->check_companyid($cid);
            if ($check_cid == 0) {
                Message::success(__('invalid_companyid'));
                $this->request->redirect("manage/company");
            }
        }
        $usertype = $_SESSION['user_type'];
        if ($cid == '') {
            $cid = $_SESSION['company_id'];
        }
        if ($usertype != 'A') {
            $check_result = $add_model->validate_packagedriver($cid);
            if ($check_result < 0) {
                if ($usertype == 'C') {
                    $this->request->redirect("manage/availabilitydriver");
                }
                if ($usertype == 'M') {
                    $this->request->redirect("manage/availabilitydriver");
                }
            }
            if ($check_result == 0) {
                if ($usertype == 'C') {
                    Message::success(__('please_upgrade_package'));
                    $this->request->redirect("company/dashboard");
                }
                if ($usertype == 'M') {
                    Message::success(__('check_company_owner'));
                    $this->request->redirect("manager/dashboard");
                }
            }
        }
        $add_model           = Model::factory('add');
        $taxicompany_details = $add_model->taxicompany_details();
        /**To get the form submit button name**/
        $signup_submit       = arr::get($_REQUEST, 'submit_addcompany');
        $errors              = array();
        $post_values         = array();
        if ($signup_submit && Validation::factory($_POST)) {
            $post_values = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
            $validator   = $add_model->validate_createlogin(arr::extract($post_values, array(
                'firstname',
                'lastname',
                'phone',
                'no_of_login',
                'company_id'
            )));
            if ($validator->check()) {
                $signup_id         = $add_model->create_login($post_values);
                $driver_details    = $add_model->view_login($post_values);
                $passenger_details = $add_model->view_passengerlogin($post_values);
                if ($signup_id == 1) {
                    Message::success(__('sucessfull_login_created'));
                    $this->request->redirect("add/create_login");
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/create_login')->bind('validator', $validator)->bind('errors', $errors)->bind('errors', $errors)->bind('driver_details', $driver_details)->bind('passenger_details', $passenger_details)->bind('taxicompany_details', $taxicompany_details)->bind('postvalue', $post_values);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('create_login');
        $this->template->page_title = __('create_login');
        $this->template->content    = $view;
    }
    public function action_taxi()
    {                           
        $add_model = Model::factory('add');
        $cid       = $this->request->param('id');
        if ($cid != '') {
            $check_cid = $add_model->check_companyid($cid);
            if ($check_cid == 0) {
                Message::success(__('invalid_companyid'));
                $this->request->redirect("manage/company");
            }
        }
        $taxi_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ($cid == '') {
            $cid = $_SESSION['company_id'];
        }
        if ($usertype != 'A') {
            //$check_result = $add_model->validate_packagetaxi($cid);
            $check_result = 1;
            if ($check_result < 0) {
                if ($usertype == 'C') {
                    $this->request->redirect("manage/availabilitytaxi");
                }
                if ($usertype == 'M') {
                    $this->request->redirect("manage/availabilitytaxi");
                }
            }
            if ($check_result == 0) {
                if ($usertype == 'C') {
                    Message::success(__('please_upgrade_package'));
                    $this->request->redirect("company/dashboard");
                }
                if ($usertype == 'M') {
                    Message::success(__('check_company_owner'));
                    $this->request->redirect("manager/dashboard");
                }
            }
            //$check_result = $add_model->validate_package_assigntaxi($cid);
            $check_result = 1;
            if ($check_result == 0) {
                if ($usertype == 'C') {
                    Message::success(__('please_upgrade_package'));
                    $this->request->redirect("add/upgradepackage");
                }
                if ($usertype == 'M') {
                    Message::success(__('check_company_owner'));
                    $this->request->redirect("manager/dashboard");
                }
            }
        }
        /**To get the form submit button name**/
        $signup_submit       = arr::get($_REQUEST, 'submit_addtaxi');
        $errors              = array();
        $post_values         = array();
        $form_array          = '';
        //$motor_details = $add_model->motor_details();
        //$model_details = $add_model->model_details();
        $model_details_new   = $add_model->model_details_new($cid);
        //~ echo $cid;
        //~ echo '<pre>';print_r($model_details_new);exit;
        $country_details     = $add_model->country_details();
        $state_details       = $add_model->state_details();
        $city_details        = $add_model->city_details();
        $taxicompany_details = $add_model->taxicompany_details();
        $additional_fields   = $add_model->taxi_additionalfields();
        $array_count         = count($additional_fields);
        if ($array_count > 0) {
            for ($i = 0; $i < $array_count; $i++) {
                if ($i > 0) {
                    $form_array .= "," . $additional_fields[$i]['field_name'];
                } else {
                    $form_array .= $additional_fields[$i]['field_name'];
                }
            }
            $form_array = explode(',', $form_array);
        } else {
            $form_array = array();
        }
        $post_values = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
        $values      = Arr::merge($post_values, $form_array);
        if ($signup_submit && Validation::factory($values, $_FILES)) {
            $values['taxi_image'] = $_FILES['taxi_image'];
            $validator            = $add_model->validate_addtaxi($values, $_FILES, $array_count);
            if ($validator->check()) {
                $comny_id         = $post_values['company_name'];
                $modelid          = $post_values['taxi_model'];
                //get company brand type for the selected company
                $brandType        = $add_model->getCompanyBrand($comny_id);
                $check_fare_exist = 1;
                //exit($brandType);
                if ($brandType == "M") {
                    $check_fare_exist = $add_model->check_fare_exist($comny_id, $modelid);
                }
                if ($check_fare_exist == 0) {
                    Message::error(__('fare_not_avaliable'));
                    if ($usertype == 'C') {
                        $this->request->redirect("manage/fare");
                    } else {
                        $this->request->redirect("manage/model");
                    }
                } else {
                    if (!empty($_FILES['taxi_image']['name'])) {
                        /* image */
                        $image_name = uniqid() . $_FILES['taxi_image']['name'];
                        $image_type = explode('.', $image_name);
                        $image_type = end($image_type);
                        //$image_name=url::title($image_name).'.'.$image_type;
                        $filename   = Upload::save($_FILES['taxi_image'], $image_name, DOCROOT . TAXI_IMG_IMGPATH);
                        //chmod($filename,'0777');
                        //Image resize and crop for thumb image
                        $logo_image = Image::factory($filename);
                        $path1      = DOCROOT . TAXI_IMG_IMGPATH;
                        $path       = $image_name;
                        Commonfunction::taxiimageresize($logo_image, TAXI_IMG_WIDTH, TAXI_IMG_HEIGHT, $path1, $image_name, 90);
                        /**** Taxi APP THU100 ***/
                        $tmb100_image_name = 'tmb100_' . $image_name;
                        Commonfunction::taxiimageresize($logo_image, TAXI_APP_THMB100_IMG_WIDTH, TAXI_APP_THMB100_IMG_HEIGHT, $path1, $tmb100_image_name, 90);
                        /**** TAxi APP THUM50 ***/
                        $tmb32_image_name = 'tmb32_' . $image_name;
                        Commonfunction::taxiimageresize($logo_image, TAXI_APP_THMB32_IMG_WIDTH, TAXI_APP_THMB32_IMG_HEIGHT, $path1, $tmb32_image_name, 90);
                        if ($image_type == 'jpeg' || $image_type == 'jpg') {
                            $base_image   = imagecreatefromjpeg($path1 . $tmb32_image_name);
                            $width        = 32;
                            $height       = 12;
                            $top_image    = imagecreatefrompng(URL_BASE . "public/common/images/view.png");
                            $merged_image = $path1 . $tmb32_image_name;
                            imagesavealpha($top_image, true);
                            imagealphablending($top_image, true);
                            imagecopy($base_image, $top_image, 0, 23, 0, 0, $width, $height);
                            imagejpeg($base_image, $merged_image);
                        }
                        if ($image_type == 'png') {
                            $base_image   = imagecreatefrompng($path1 . $tmb32_image_name);
                            $width        = 32;
                            $height       = 12;
                            $top_image    = imagecreatefrompng(URL_BASE . "public/common/images/view.png");
                            $merged_image = $path1 . $tmb32_image_name;
                            imagesavealpha($top_image, true);
                            imagealphablending($top_image, true);
                            imagecopy($base_image, $top_image, 0, 23, 0, 0, $width, $height);
                            imagepng($base_image, $merged_image);
                        }
                        $signup_id = $add_model->addtaxi($post_values, $form_array, $path, $_FILES, $array_count);
                        if ($signup_id == 1) {
                            $mail = "";
                            //Taxi count updated with Crm
                            if (CRM_UPDATE_ENABLE==1 && class_exists('Thirdpartyapi')) {
                                if (method_exists('Thirdpartyapi','crm_add_taxi_count')) {                                    
                                    $thirdpartyapi= Thirdpartyapi::instance();
                                    $thirdpartyapi->crm_add_taxi_count();                
                                }
                            }
                            Message::success(__('sucessfull_added_taxi'));
                            $this->request->redirect("manage/taxi");
                        }
                    }
                }
            } else {
                $errors = $validator->errors('errors');
                //print_r($state_details);exit;
            }
        }
        $view                       = View::factory('admin/add_taxi')->bind('validator', $validator)->bind('errors', $errors)->bind('additional_fields', $additional_fields)->bind('country_details', $country_details)->bind('city_details', $city_details)->bind('state_details', $state_details)
        //->bind('motor_details',$motor_details)
            
        //->bind('model_details',$model_details)
            ->bind('model_details_new', $model_details_new)->bind('taxicompany_details', $taxicompany_details)->bind('cid', $cid)->bind('postvalue', $post_values);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_taxi');
        $this->template->page_title = __('add_taxi');
        $this->template->content    = $view;
    }
    public function action_driver()
    {
        /********** Package Based Adding Drivers Limit Here ***********/
        if (PACKAGE_TYPE == 1 || PACKAGE_TYPE == 2 || PACKAGE_TYPE == 0) {
            $package              = Model::factory('package');
            $drivercount          = $package->checkdrivercount();
            $drivercount          = $drivercount[0]['driver_count'];
            $current_driver_count = $package->check_current_driver_count();
            $current_driver_count = count($current_driver_count);
            if ($current_driver_count >= $drivercount) {
                $str_message = str_replace('##CLOUD_SITENAME##', CLOUD_SITENAME, __('upgrade_package_content'));
                Message::error($str_message);
                $this->request->redirect("package/account_plan");
            }
        }
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $add_model      = Model::factory('add');
        $cid            = $this->request->param('id');
        if ($cid != '') {
            $check_cid = $add_model->check_companyid($cid);
            if ($check_cid == 0) {
                Message::success(__('invalid_companyid'));
                $this->request->redirect("manage/company");
            }
        }
        $usertype = $_SESSION['user_type'];
        if ($cid == '') {
            $cid = $_SESSION['company_id'];
        }
        /**To get the form submit button name**/
        $signup_submit       = arr::get($_REQUEST, 'submit_driver');
        $country_details     = $add_model->country_details();
        $state_details       = $add_model->state_details();
        $city_details        = $add_model->city_details();
        $taxicompany_details = $add_model->taxicompany_details();
        $errors              = array();
        $post_values         = array();
        if ($signup_submit && Validation::factory($_POST)) {
            $post_values = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
            $driver      = Model::factory('driver');
            $form_values = Arr::extract($post_values, array(
                'firstname',
                'lastname',
                'dob',
                'email',
                'password',
                'repassword',
                'driver_license_id',
                'driver_license_expire_date',
                'driver_pco_license_number',
                'driver_pco_license_expire_date',
                'driver_insurance_number',
                'driver_insurance_expire_date',
                'driver_national_insurance_number',
                'driver_national_insurance_expire_date',
                'phone',
                'address',
                'country',
                'state',
                'city',
                'company_name',
                'booking_limit',
                'brand_type'
            ));
            $file_values = Arr::extract($_FILES, array(
                'photo'
            ));
            $values      = Arr::merge($form_values, $file_values);
            $validator   = $add_model->validate_adddriver($values);
            //print_r($validator); exit;
            if ($validator->check()) {
                $filename = "";
                $path1    = "";
                if (isset($_FILES['photo']['name']) && $_FILES['photo']['name'] != '') {
                    $image_name       = uniqid() . $_FILES['photo']['name'];
                    $thumb_image_name = 'thumb_' . $image_name;
                    $image_type       = explode('.', $image_name);
                    $image_type       = end($image_type);
                    //$image_name=url::title($image_name).'.'.$image_type;
                    $filename         = Upload::save($_FILES['photo'], $image_name, DOCROOT . SITE_DRIVER_IMGPATH);
                    $logo_image       = Image::factory($filename);
                    $path11           = DOCROOT . SITE_DRIVER_IMGPATH;
                    $path1            = $image_name;
                    Commonfunction::imageresize($logo_image, PASS_IMG_WIDTH, PASS_IMG_HEIGHT, $path11, $image_name, 90);
                    $path12 = $thumb_image_name;
                    Commonfunction::imageresize($logo_image, PASS_THUMBIMG_WIDTH, PASS_THUMBIMG_HEIGHT, $path11, $thumb_image_name, 90);
                }
                $signup_id = $add_model->add_driver($_POST, $filename);
                $status    = $driver->update_driverimage($path1, $signup_id);
                if ($signup_id != 0) {
                    $signup_id = 1;
                } else {
                    $signup_id = 0;
                }
                if ($signup_id == 1) {
                    $mail              = "";
                    $replace_variables = array(
                        REPLACE_LOGO => EMAILTEMPLATELOGO,
                        REPLACE_SITENAME => COMPANY_SITENAME,
                        REPLACE_USERNAME => $post_values['firstname'],
                        REPLACE_MOBILE => $post_values['phone'],
                        REPLACE_PASSWORD => $post_values['password'],
                        REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                        REPLACE_SITEEMAIL => CONTACT_EMAIL,
                        REPLACE_SITEURL => URL_BASE,
                        REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                        REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR
                    );
                    $message           = $this->emailtemplate->emailtemplate(DOCROOT . TEMPLATEPATH . 'driver-register.html', $replace_variables);
                    $to                = $post_values['email'];
                    $from              = CONTACT_EMAIL;
                    $subject           = __('driver_registration_success');
                    $redirect          = "manage/driver";
                    $smtp_result       = $add_model->smtp_settings();
                    if (!empty($smtp_result) && ($smtp_result[0]['smtp'] == 1)) {
                        include($_SERVER['DOCUMENT_ROOT'] . "/modules/SMTP/smtp.php");
                    } else {
                        // To send HTML mail, the Content-type header must be set
                        $headers = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        // Additional headers
                        $headers .= 'From: ' . $from . '' . "\r\n";
                        $headers .= 'Bcc: ' . $to . '' . "\r\n";
                        mail($to, $subject, $message, $headers);
                    }
                    //free sms url with the arguments
                    if (SMS == 1) {
                        $common_model    = Model::factory('commonmodel');
                        $message_details = $common_model->sms_message('1');
                        $to              = $_POST['phone'];
                        $message         = isset($message_details[0]['sms_description']) ? $message_details[0]['sms_description'] : "";
                        $message         = str_replace("##SITE_NAME##", SITE_NAME, $message);                    
                    }
                    Message::success(__('sucessfull_added_driver'));
                    $this->request->redirect("manage/driver");
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/add_driver')->bind('validator', $validator)->bind('errors', $errors)->bind('country_details', $country_details)->bind('state_details', $state_details)->bind('city_details', $city_details)->bind('taxicompany_details', $taxicompany_details)->bind('cid', $cid)->bind('postvalue', $post_values);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_driver');
        $this->template->page_title = __('add_driver');
        $this->template->content    = $view;
    }
   /* public function action_country()
    {
        Message::error(__('invalid_access'));
        $this->request->redirect("manage/country");
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ($usertype != 'A' && $usertype != 'S') {
            $this->request->redirect("admin/login");
        }
        $add_model     = Model::factory('add');
        //To get the form submit button name
        $signup_submit = arr::get($_REQUEST, 'submit_addcountry');
        $errors        = array();
        $post_values   = array();
        if ($signup_submit && Validation::factory($_POST)) {
            $post      = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
            $validator = $add_model->validate_addcountry(arr::extract($post, array(
                'country_name',
                'iso_country_code',
                'telephone_code',
                'currency_code',
                'currency_symbol'
            )));
            if ($validator->check()) {
                $signup_id = $add_model->addcountry($post);
                if ($signup_id == 1) {
                    $mail = "";
                    Message::success(__('sucessfull_added_country'));
                    $this->request->redirect("manage/country");
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/add_country')->bind('validator', $validator)->bind('errors', $errors)->bind('postvalue', $post);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_country');
        $this->template->page_title = __('add_country');
        $this->template->content    = $view;
    } */
    public function action_city()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ($usertype != 'A' && $usertype != 'S') {
            $this->request->redirect("admin/login");
        }
        $add_model     = Model::factory('add');
        /**To get the form submit button name**/
        $signup_submit = arr::get($_REQUEST, 'submit_addcity');
        $post_values   = $errors = array();
        $motor_details = $add_model->country_details_new();
        $state_details = $add_model->get_city_state_details($countryid = '');
        if ($signup_submit && Validation::factory($_POST)) {
            $post_values = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
            if ($post_values['country_name']) {
                $state_details = $add_model->get_city_state_details($post_values['country_name']);
            }
            $validator = $add_model->validate_addcity(arr::extract($post_values, array(
                'country_name',
                'state_name',
                'city_name',
                'zipcode',
                'city_model_fare'
            )));
            if ($validator->check()) {
                $signup_id = $add_model->addcity($post_values);
                if ($signup_id == 1) {
                    Message::success(__('sucessfull_added_city'));
                    $this->request->redirect("manage/city");
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/add_city')->bind('validator', $validator)->bind('errors', $errors)->bind('postvalue', $post_values)->bind('state_details', $state_details)->bind('motor_details', $motor_details);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_city');
        $this->template->page_title = __('add_city');
        $this->template->content    = $view;
    }
    public function action_state()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ($usertype != 'A' && $usertype != 'S') {
            $this->request->redirect("admin/login");
        }
        $add_model     = Model::factory('add');
        /**To get the form submit button name**/
        $signup_submit = arr::get($_REQUEST, 'submit_addstate');
        $errors        = array();
        $post_values   = array();
        $state_details = $add_model->country_details();
        if ($signup_submit && Validation::factory($_POST)) {
            $post      = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
            $validator = $add_model->validate_addstate(arr::extract($post, array(
                'country_name',
                'state_name'
            )));
            if ($validator->check()) {
                $signup_id = $add_model->addstate($post);
                if ($signup_id == 1) {
                    Message::success(__('sucessfull_added_state'));
                    $this->request->redirect("manage/state");
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/add_state')->bind('validator', $validator)->bind('errors', $errors)->bind('postvalue', $post)->bind('state_details', $state_details);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_state');
        $this->template->page_title = __('add_state');
        $this->template->content    = $view;
    }
    public function action_getmodellist()
    {
        $add_model        = Model::factory('add');
        $output           = '';
        $motorid          = arr::get($_REQUEST, 'motor_id');
        $modelid          = arr::get($_REQUEST, 'model_id');
        $getmodel_details = $add_model->getmodel_details($motorid);
        if (isset($motorid)) {
            $count = count($getmodel_details);
            if ($count > 0) {
                $output .= '<select name="taxi_model" id="taxi_model" class="required" title="' . __('select_the_taximodel') . '" >
                       <option value="">--Select--</option>';
                foreach ($getmodel_details as $modellist) {
                    $output .= '<option value="' . $modellist["model_id"] . '"';
                    if ($modelid == $modellist["model_id"]) {
                        $output .= 'selected=selected';
                    }
                    $output .= '>' . $modellist["model_name"] . '</option>';
                }
                $output .= '</select>';
            } else {
                $output .= '<select name="taxi_model" id="taxi_model" class="required" title="' . __('select_the_taximodel') . '">
                <option value="">--Select--</option></select>';
            }
        }
        echo $output;
        exit;
    }
    public function action_getcitylist()
    {
        $add_model        = Model::factory('add');
        $output           = '';
        $country_id       = arr::get($_REQUEST, 'country_id');
        $state_id         = arr::get($_REQUEST, 'state_id');
        $city_id          = arr::get($_REQUEST, 'city_id');
        $getmodel_details = $add_model->getcity_details($country_id, $state_id);
        if (isset($country_id)) {
            $count = count($getmodel_details);
            if ($count > 0) {
                $output .= '<select name="city" id="city" --onchange="change_company();" class="required" title="' . __('select_the_city') . '" >
                       <option value="">--Select--</option>';
                foreach ($getmodel_details as $modellist) {
                    $output .= '<option value="' . $modellist["city_id"] . '"';
                    if ($city_id == $modellist["city_id"]) {
                        $output .= 'selected=selected';
                    }
                    $output .= '>' . $modellist["city_name"] . '</option>';
                }
                $output .= '</select>';
            } else {
                $output .= '<select name="city" id="city" class="required" title="' . __('select_the_city') . '">
                   <option value="">--Select--</option></select>';
            }
        }
        echo $output;
        exit;
    }
    public function action_getlist_state()
    {
        $add_model        = Model::factory('add');
        $output           = '';
        $country_id       = arr::get($_REQUEST, 'country_id');
        $state_id         = arr::get($_REQUEST, 'state_id');
        $getmodel_details = $add_model->getstate_details($country_id);
        if (isset($country_id)) {
            $count = count($getmodel_details);
            if ($count > 0) {
                $output .= '<select name="state" id="state" onchange=change_city_drop("","","") class="required" title="' . __('select_the_state') . '">
                       <option value="">--Select--</option>';
                foreach ($getmodel_details as $modellist) {
                    $output .= '<option value="' . $modellist["state_id"] . '"';
                    if ($state_id == $modellist["state_id"]) {
                        $output .= 'selected=selected';
                    }
                    $output .= '>' . $modellist["state_name"] . '</option>';
                }
                $output .= '</select>';
            } else {
                $output .= '<select name="state" id="state" class="required" title="' . __('select_the_state') . '" >
                   <option value="">--Select--</option></select>';
            }
        }
        echo $output;
        exit;
    }
    public function action_getstatelist()
    {
        $add_model        = Model::factory('add');
        $output           = '';
        $country_id       = arr::get($_REQUEST, 'country_id');
        $state_id         = arr::get($_REQUEST, 'state_id');
        $getmodel_details = $add_model->getstate_details($country_id);
        if (isset($country_id)) {
            $count = count($getmodel_details);
            if ($count > 0) {
                $output .= '<select name="state_name" id="state_name" >';
                foreach ($getmodel_details as $modellist) {
                    $output .= '<option value="' . $modellist["state_id"] . '"';
                    if ($state_id == $modellist["state_id"]) {
                        $output .= 'selected=selected';
                    }
                    $output .= '>' . $modellist["state_name"] . '</option>';
                }
                $output .= '</select>';
            } else {
                $output .= '<select name="state_name" id="state_name">
                   <option value="">--Select--</option></select>';
            }
        }
        echo $output;
        exit;
    }
    public function action_getassigntaxilist()
    {
        $add_model        = Model::factory('add');
        $output           = '';
        $country_id       = arr::get($_REQUEST, 'country_id');
        $state_id         = arr::get($_REQUEST, 'state_id');
        $city_id          = arr::get($_REQUEST, 'city_id');
        $assigntaxi       = arr::get($_REQUEST, 'assigntaxi');
        $getmodel_details = $add_model->getcity_details($country_id, $state_id);
        if (isset($country_id)) {
            $count = count($getmodel_details);
            if ($count > 0) {
                if (isset($assigntaxi) && $assigntaxi == 1) {
                    $output .= '<select name="city" id="city" class="required" title=" ' . __('select_the_city') . '" onchange="change_info();">
                       <option value="">--Select--</option>';
                } else {
                    $output .= '<select name="city" id="city" class="required" title=" ' . __('select_the_city') . '">
                       <option value="">--Select--</option>';
                }
                foreach ($getmodel_details as $modellist) {
                    $output .= '<option value="' . $modellist["city_id"] . '"';
                    if ($city_id == $modellist["city_id"]) {
                        $output .= 'selected=selected';
                    }
                    $output .= '>' . $modellist["city_name"] . '</option>';
                }
                $output .= '</select>';
            } else {
                $output .= '<select name="city" id="city" title=" ' . __('select_the_city') . '" class="required">
                   <option value="">--Select--</option></select>';
            }
        }
        echo $output;
        exit;
    }
    public function action_getassignstatelist()
    {
        $add_model        = Model::factory('add');
        $output           = '';
        $country_id       = arr::get($_REQUEST, 'country_id');
        $state_id         = arr::get($_REQUEST, 'state_id');
        $getmodel_details = $add_model->getstate_details($country_id);
        if (isset($country_id)) {
            $count = count($getmodel_details);
            if ($count > 0) {
                $output .= '<select name="state" id="state" onchange="change_city_drop(\'\',\'\',\'\'); change_info(\'\',\'\',\'\',\'\');" class="required" title="' . __('select_the_state') . '">
                       <option value="">--Select--</option>';
                foreach ($getmodel_details as $modellist) {
                    $output .= '<option value="' . $modellist["state_id"] . '"';
                    if ($state_id == $modellist["state_id"]) {
                        $output .= 'selected=selected';
                    }
                    $output .= '>' . $modellist["state_name"] . '</option>';
                }
                $output .= '</select>';
            } else {
                $output .= '<select name="state" id="state" onchange="change_city_drop(); change_info();" class="required" title="' . __('select_the_state') . '" >
                   <option value="">--Select--</option></select>';
            }
        }
        echo $output;
        exit;
    }
    public function action_manager()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ($usertype != 'A' && $usertype != 'C' && $usertype != 'S' && $usertype != 'DA') {
            $this->request->redirect("admin/login");
        }
        $add_model     = Model::factory('add');
        $company_model = Model::factory('company');
        $cid           = $this->request->param('id');
        if ($cid != '') {
            $check_cid = $add_model->check_companyid($cid);
            if ($check_cid == 0) {
                Message::success(__('invalid_companyid'));
                $this->request->redirect("manage/company");
            }
        }
        /**To get the form submit button name**/
        $signup_submit       = arr::get($_REQUEST, 'submit_addmanager');
        $country_details     = $add_model->country_details();
        $city_details        = $add_model->city_details();
        $taxicompany_details = $add_model->taxicompany_details();
        $state_details       = $add_model->state_details();
        $errors              = array();
        $post_values         = array();
        if ($signup_submit && Validation::factory($_POST)) {
            $post_values = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
            $validator   = $add_model->validate_addmanager(arr::extract($post_values, array(
                'firstname',
                'lastname',
                'email',
                'password',
                'repassword',
                'phone',
                'address',
                'country',
                'state',
                'city',
                'company_name'
            )));
            if ($validator->check()) {
                $signup_id = $add_model->addmanager($post_values);
                if ($signup_id == 1) {
                    if (isset($post_values['add_as_executive']) && $post_values['add_as_executive'] == 1) {
                        $admins_model     = Model::factory('admin');
                        $insert_executive = $admins_model->add_executive($post_values);
                    }
                    $mail              = "";
                    //function to get company domain name
                    $company_dets      = $company_model->get_company_info($post_values['company_name']);
                    $companyDomain     = isset($company_dets['company_domain']) ? $company_dets['company_domain'] : '';
                    $contact_mail      = isset($company_dets['company_mail']) ? $company_dets['company_mail'] : '';
                    //if there is no domain name comes go to product url
                    $replace_variables = array(
                        REPLACE_LOGO => EMAILTEMPLATELOGO,
                        REPLACE_SITENAME => COMPANY_SITENAME,
                        REPLACE_DOMAINNAME => $companyDomain,
                        REPLACE_USERNAME => $post_values['firstname'],
                        REPLACE_EMAIL => $post_values['email'],
                        REPLACE_PASSWORD => $post_values['password'],
                        REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                        REPLACE_SITEEMAIL => $contact_mail,
                        REPLACE_SITEURL => URL_BASE,
                        REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                        REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR
                    );
                    $message           = $this->emailtemplate->emailtemplate(DOCROOT . TEMPLATEPATH . 'register_dispatcher.html', $replace_variables);
                    $to                = $_POST['email'];
                    $from              = CONTACT_EMAIL;
                    $subject           = __('registration_success');
                    $redirect          = "manage/manager";
                    $smtp_result       = $add_model->smtp_settings();
                    if (!empty($smtp_result) && ($smtp_result[0]['smtp'] == 1)) {
                        include($_SERVER['DOCUMENT_ROOT'] . "/modules/SMTP/smtp.php");
                    } else {
                        // To send HTML mail, the Content-type header must be set
                        $headers = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        // Additional headers
                        $headers .= 'From: ' . $from . '' . "\r\n";
                        $headers .= 'Bcc: ' . $to . '' . "\r\n";
                        mail($to, $subject, $message, $headers);
                    }
                    Message::success(__('sucessfull_added_manager'));
                    $this->request->redirect("manage/manager");
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/add_manager')->bind('validator', $validator)->bind('errors', $errors)->bind('country_details', $country_details)->bind('city_details', $city_details)->bind('taxicompany_details', $taxicompany_details)->bind('state_details', $state_details)->bind('cid', $cid)->bind('postvalue', $post_values);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_manager');
        $this->template->page_title = __('add_manager');
        $this->template->content    = $view;
    }
    public function action_getcompanylist()
    {
        $add_model        = Model::factory('add');
        $output           = '';
        $country_id       = arr::get($_REQUEST, 'country_id');
        $state_id         = arr::get($_REQUEST, 'state_id');
        $city_id          = arr::get($_REQUEST, 'city_id');
        $company_name     = arr::get($_REQUEST, 'company_name');
        $user_type        = $_SESSION['user_type'];
        $company_id       = $_SESSION['company_id'];
        $getmodel_details = $add_model->getcompany_details($country_id, $state_id, $city_id);
        if (isset($country_id)) {
            $count = count($getmodel_details);
            if ($count > 0) {
                $output .= '<select name="company_name" id="company_name" class="required">
                       <option value="">--Select--</option>';
                foreach ($getmodel_details as $modellist) {
                    $output .= '<option value="' . $modellist["cid"] . '"';
                    if ($company_name == $modellist["cid"]) {
                        $output .= 'selected=selected';
                    }
                    $output .= '>' . $modellist["company_name"] . '</option>';
                }
                $output .= '</select>';
            } else {
                $output .= '<select name="company_name" id="company_name">
                   <option value="">--Select--</option></select>';
            }
        }
        echo $output;
        exit;
    }
    public function action_assigntaxi()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $add_model      = Model::factory('add');
        $usertype       = $_SESSION['user_type'];
        $company_id     = $_SESSION['company_id'];
        $cid            = $_SESSION['company_id'];
        /**To get the form submit button name**/
        $signup_submit  = arr::get($_REQUEST, 'submit_addassigntaxi');
        if ($usertype != "M") {
            $country_details     = $add_model->country_details();
            $state_details       = $add_model->state_details();
            $city_details        = $add_model->city_details();
            $taxicompany_details = $add_model->taxicompany_details();
        }
        $driver_details = $add_model->driver_details();
        $taxi_details   = $add_model->taxi_details();
        $errors         = array();
        $post_values    = array();
        if ($signup_submit && Validation::factory($_POST)) {
            $post_values = $_POST;
            $post        = Arr::map('trim', $this->request->post());
            $validator   = $add_model->validate_addassigntaxi(arr::extract($post, array(
                'company_name',
                'country',
                'state',
                'city',
                'driver',
                'taxi',
                'startdate',
                'enddate'
            )));
            if ($validator->check()) {
                $update = $add_model->addassigntaxi($post);
                if (is_array($update) && count($update) > 0) {
                    $mail              = "";
                    $replace_variables = array(
                        REPLACE_LOGO => EMAILTEMPLATELOGO,
                        REPLACE_SITENAME => COMPANY_SITENAME,
                        REPLACE_USERNAME => $update[0]['name'],
                        REPLACE_TAXINO => $update[0]['taxi_no'],
                        REPLACE_STARTDATE => $post['startdate'],
                        REPLACE_ENDDATE => $post['enddate'],
                        REPLACE_TAXIMODEL => $update[0]['model_name'],
                        REPLACE_TAXISPEED => $update[0]['taxi_speed'],
                        REPLACE_MAXIMUMLUGGAGE => $update[0]['max_luggage'],
                        REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                        REPLACE_SITEEMAIL => CONTACT_EMAIL,
                        REPLACE_SITEURL => URL_BASE,
                        REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                        REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR
                    );
                    $message           = $this->emailtemplate->emailtemplate(DOCROOT . TEMPLATEPATH . 'assign_taxi.html', $replace_variables);
                    $to                = $update[0]['email'];
                    $from              = CONTACT_EMAIL;
                    $subject           = __('taxi_assigned_you');
                    $redirect          = "manage/assigntaxi";
                    $smtp_result       = $add_model->smtp_settings();
                    if (!empty($smtp_result) && ($smtp_result[0]['smtp'] == 1)) {
                        include($_SERVER['DOCUMENT_ROOT'] . "/modules/SMTP/smtp.php");
                    } else {
                        // To send HTML mail, the Content-type header must be set
                        $headers = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        // Additional headers
                        $headers .= 'From: ' . $from . '' . "\r\n";
                        $headers .= 'Bcc: ' . $to . '' . "\r\n";
                        mail($to, $subject, $message, $headers);
                    }
                }
                Message::success(__('sucessfull_assign_taxi'));
                $this->request->redirect("manage/assigntaxi");
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/add_assigntaxi')->bind('validator', $validator)->bind('errors', $errors)->bind('country_details', $country_details)->bind('state_details', $state_details)->bind('city_details', $city_details)->bind('taxicompany_details', $taxicompany_details)->bind('driver_details', $driver_details)->bind('taxi_details', $taxi_details)->bind('postvalue', $post_values);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('assign_taxi');
        $this->template->page_title = __('assign_taxi');
        $this->template->content    = $view;
    }
    public function action_getassignedlist()
    {
        $add_model     = Model::factory('add');
        $output        = '';
        $country_id    = arr::get($_REQUEST, 'country_id');
        $state_id      = arr::get($_REQUEST, 'state_id');
        $city_id       = arr::get($_REQUEST, 'city_id');
        $company_name  = arr::get($_REQUEST, 'company_name');
        $driver_id     = arr::get($_REQUEST, 'driver_id');
        $taxi_id       = arr::get($_REQUEST, 'taxi_no');
        $startdate     = arr::get($_REQUEST, 'startdate');
        $enddate       = arr::get($_REQUEST, 'enddate');
        $user_type     = $_SESSION['user_type'];
        $company_id    = $_SESSION['company_id'];
        $page_title    = __('assign_taxi');
        $page_no       = arr::get($_REQUEST, 'page');
        $count_details = $add_model->getassignedcount($country_id, $state_id, $city_id, $company_name, $driver_id, $taxi_id, $startdate, $enddate);
        if ($page_no)
            $offset = REC_PER_PAGE * ($page_no - 1);
        $pag_data         = Pagination::factory(array(
            'current_page' => array(
                'source' => 'query_string',
                'key' => 'page'
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/punajax'
        ));
        $getmodel_details = $add_model->getassignedlist($country_id, $state_id, $city_id, $company_name, $driver_id, $taxi_id, $startdate, $enddate, $offset, REC_PER_PAGE);
        $count            = count($getmodel_details);
        $output .= '<div class="widget">
                <div class="title"><h6>' . $page_title . '</h6>
                <div style="width:auto; float:right; margin: 4px 3px;">
                <div class="button greyishB"></div>                       
                </div>
                </div>';
        if ($count > 0) {
            $output .= '<div class= "overflow-block">';
        }
        $output .= '<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">';
        if ($count > 0) {
            $output .= '<thead>
                <tr>
                <td align="left" width="5%" style="min-width: 22px !important;" >Status</td>
                <td align="left" width="5%">' . __('sno_label') . '</td>
                <td align="left" style="text-align:left;" width="8%">' . ucfirst(__('driver_name')) . '</td>
                <td align="left" style="text-align:left;" width="10%">' . __('taxi_no') . '</td>
                <td align="left" style="text-align:left;" width="8%">' . __('companyname') . '</td>
                <td align="left" style="text-align:left;" width="10%">' . __('country_label') . '</td>
                <td align="left" style="text-align:left;" width="8%">' . __('city_label') . '</td>
                <td align="left" style="text-align:left;" width="10%">' . __('from_date') . '</td>
                <td align="left" style="text-align:left;" width="10%">' . __('end_date') . '</td>
                </tr>
                </thead>
                <tbody>    ';
            $sno = $offset;
            /* For Serial No */
            foreach ($getmodel_details as $listings) {
                //S.No Increment
                //==============
                $sno++;
                //For Odd / Even Rows
                //===================
                $trcolor = ($sno % 2 == 0) ? 'oddtr' : 'eventr';
                $output .= '<tr class="' . $trcolor . '">
                <td align="center">';
                if ($listings['mapping_status'] == 'A') {
                    $txt   = "Active";
                    $class = "unsuspendicon";
                } elseif ($listings['mapping_status'] == 'T') {
                    $txt   = "Trash";
                    $class = "trashicon";
                } else {
                    $txt   = "Deactive";
                    $class = "blockicon";
                }
                $output .= '<a href="javascript:void(0);" title =' . $txt . ' class=' . $class . '></a>';
                $output .= '</td> 
                <td align="center">' . $sno . '</td>
                <td align="left">' . wordwrap(ucfirst($listings['name']), 30, '<br/>', 1) . '</td>
                <td>' . wordwrap(ucfirst($listings['taxi_no']), 30, '<br/>', 1) . '</td>
                <td align="left">' . wordwrap(ucfirst($listings['company_name']), 30, '<br/>', 1) . '</td>
                <td align="left">' . wordwrap($listings['country_name'], 25, '<br />', 1) . '</td>                        
                <td>' . wordwrap($listings['city_name'], 25, '<br />', 1) . '</td>
                <td>' . wordwrap(Commonfunction::getDateTimeFormat($listings['mapping_startdate'], 1), 25, '<br />', 1) . '</td>
                <td>' . wordwrap(Commonfunction::getDateTimeFormat($listings['mapping_enddate'], 1), 25, '<br />', 1) . '</td>
                </tr>';
            }
        }
        //For No Records
        //==============
        else {
            $output .= '<tr>
                <td class="nodata">' . __('no_data') . '</td>
                </tr>';
        }
        $output .= '</tbody>
                </table>';
        if ($count > 0) {
            $output .= '</div>';
        }
        $output .= '</div>';
        $output .= '<div class="bottom_contenttot"><div class="pagination">';
        if ($count > 0) {
            $output .= '' . $pag_data->render() . '';
        }
        $output .= '</div></div>';
        echo $output;
        exit;
    }
    public function action_getdriverlist()
    {
        $add_model        = Model::factory('add');
        $output           = '';
        $country_id       = arr::get($_REQUEST, 'country_id');
        $state_id         = arr::get($_REQUEST, 'state_id');
        $city_id          = arr::get($_REQUEST, 'city_id');
        $company_id       = arr::get($_REQUEST, 'company_name');
        $user_type        = $_SESSION['user_type'];
        $driver_id        = arr::get($_REQUEST, 'driver_id');
        $type             = arr::get($_REQUEST, 'type');
        $getmodel_details = $add_model->getdriverdetails($company_id, $country_id, $state_id, $city_id, $user_type);
        $commMdl          = Model::factory('commonmodel');
        $date             = $commMdl->getcompany_all_currenttimestamp($company_id);
        if (isset($country_id)) {
            $count = count($getmodel_details);
            if ($count > 0) {
                $output .= '<select name="driver" id="driver" onchange="change_info("","","");" size=5>
                        <option value="">--Select--</option>';
                foreach ($getmodel_details as $modellist) {
                    if ($type == 1) {
                        if ($date < $modellist['mapping_startdate'] || $date > $modellist['mapping_enddate'] || empty($modellist['mapping_enddate'])) {
                            $output .= '<option value="' . $modellist["id"] . '"';
                            if ($driver_id == $modellist["id"]) {
                                $output .= 'selected=selected class="selected_active"';
                            }
                            $output .= '>' . ucfirst($modellist["name"]) . '</option>';
                        }
                    } else {
                        $output .= '<option value="' . $modellist["id"] . '"';
                        if ($driver_id == $modellist["id"]) {
                            $output .= 'selected=selected class="selected_active"';
                        }
                        $output .= '>' . ucfirst($modellist["name"]) . '</option>';
                    }
                }
                $output .= '</select>';
            } else {
                $output .= '<select name="driver" id="driver" onchange="change_info("","","");" size=5>
                    <option value="">--Select--</option></select>';
            }
        }
        echo $output;
        exit;
    }
    public function action_gettaxilist()
    {
        $add_model        = Model::factory('add');
        $output           = '';
        $country_id       = arr::get($_REQUEST, 'country_id');
        $state_id         = arr::get($_REQUEST, 'state_id');
        $city_id          = arr::get($_REQUEST, 'city_id');
        $company_id       = arr::get($_REQUEST, 'company_name');
        $user_type        = $_SESSION['user_type'];
        $taxi_id          = arr::get($_REQUEST, 'taxi_id');
        $type             = arr::get($_REQUEST, 'type');
        $commMdl          = Model::factory('commonmodel');
        $date             = $commMdl->getcompany_all_currenttimestamp($company_id);
        $getmodel_details = $add_model->gettaxidetails($company_id, $country_id, $state_id, $city_id, $user_type);
        if (isset($country_id)) {
            $count = count($getmodel_details);
            if ($count > 0) {
                $output .= '<select name="taxi" id="taxi" onchange="change_info("","","");" size=5>
                        <option value="">--Select--</option>';
                foreach ($getmodel_details as $modellist) {
                    if ($type == 1) {
                        if ($date < $modellist['mapping_startdate'] || $date > $modellist['mapping_enddate'] || empty($modellist['mapping_enddate'])) {
                            $output .= '<option value="' . $modellist["taxi_id"] . '"';
                            if ($taxi_id == $modellist["taxi_id"]) {
                                $output .= 'selected=selected class="selected_active"';
                            }
                            $output .= '>' . $modellist["taxi_no"] . '</option>';
                        }
                    } else {
                        $output .= '<option value="' . $modellist["taxi_id"] . '"';
                        if ($taxi_id == $modellist["taxi_id"]) {
                            $output .= 'selected=selected class="selected_active"';
                        }
                        $output .= '>' . $modellist["taxi_no"] . '</option>';
                    }
                }
                $output .= '</select>';
            } else {
                $output .= '<select name="taxi" id="taxi" onchange="change_info("","","");" size=5>
                        <option value="">--Select--</option></select>';
            }
        }
        echo $output;
        exit;
    }
    /** for adding contents **/
    public function action_contents()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ($usertype == 'C') {
            $this->request->redirect("company/login");
        }
        if ($usertype == 'M') {
            $this->request->redirect("manager/login");
        }
        $add_model     = Model::factory('add');
        /** Select menus **/
        $menu_details  = $add_model->get_menus();
        /**To get the form submit button name**/
        $signup_submit = arr::get($_REQUEST, 'submit_addmanager');
        $errors        = array();
        $post_values   = array();
        if ($signup_submit && Validation::factory($_POST)) {
            $post_values = Arr::map('trim', $this->request->post());
            $validator   = $add_model->validate_addcontents(arr::extract($post_values, array(
                'menu_name',
                'meta_title',
                'meta_keyword',
                'meta_description'
            )));
            if ($validator->check()) {
                $menu_name_exits = $add_model->menu_content_exits($post_values);
                if ($menu_name_exits == 1) {
                    Message::error(__('content_already_exits'));
                    $this->request->redirect("manage/contents");
                }
                $signup_id = $add_model->addcontents($post_values);
                if ($signup_id == 1) {
                    Message::success(__('sucessfull_added_contents'));
                    $this->request->redirect("manage/contents");
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/add_contents')->bind('validator', $validator)->bind('errors', $errors)->bind('postvalue', $post_values)->bind('menu_details', $menu_details);
        $this->template->title      = SITENAME . " | " . __('add_content');
        $this->template->page_title = __('add_content');
        $this->template->content    = $view;
    }
    public function action_delete_image()
    {
        $file_path = $_REQUEST['sPath'];
        $filepath  = $_SERVER["DOCUMENT_ROOT"] . '/public/' . UPLOADS . '/taxi_image/';
        $taxi_id   = $_REQUEST['taxi_id'];
        $imageid   = $_REQUEST['image_id'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $add_model = Model::factory('add');
        $count     = $add_model->change_imagecount($taxi_id, $imageid);
        $output    = '';
        $j         = 0;
        if (is_array($count)) {
            foreach ($count as $value) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/public/' . UPLOADS . '/taxi_image/' . $taxi_id . '_' . $value . '.png')) {
                    $output .= '<tr>
            <td width="20%"></td>
        
            <td valign="top" width="20%">  
            
                <input type="file" class="text" name="updateimage[' . $value . ']" id="cpicture' . $value . '" value="" ><br>
                <span id="error' . $value . '" class="err_count" style="display:none;color:red;font-size:11px;">*Only jpeg, jpg or png images</span>
            </td>
            <td valign="top">

                <img style="margin-left:10px;" width="75" height="75" src="' . URL_BASE . 'public/' . UPLOADS . '/taxi_image/' . $taxi_id . '_' . $value . '.png"  width="300" alt="Slider Image"/>
            <br />
            
                <a href="javascript:;" onclick="remove_image(\'' . $filepath . $taxi_id . '_' . $value . '.png\',\'' . $value . '\')" class="ml10" title="Delete">Delete</a>

            </td>
            </tr>';
                } else {
                    $output .= '<tr style="display:none;">
            <td width="20%"></td>
            <td valign="top" width="20%"><br>
            <span id="error<?php echo $value; ?>" class="err_count" style="display:none;color:red;font-size:11px;">*Only jpeg, jpg or png images</span>
            </td>
            </tr>';
                }
            }
        }
        echo $output;
        exit;
    }
    // Add menu function
    public function action_menu()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ($usertype != 'A' && $usertype != 'S') {
            $this->request->redirect("admin/login");
        }
        $add_model     = Model::factory('add');
        /**To get the form submit button name**/
        $signup_submit = arr::get($_REQUEST, 'submit_menu');
        $errors        = array();
        $post_values   = array();
        if ($signup_submit && Validation::factory($_POST)) {
            $post_values = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
            $validator   = $add_model->validate_addmenu(arr::extract($post_values, array(
                'menu_name',
                'slug'
            )));
            if ($validator->check()) {
                $status = $add_model->addmenu($post_values);
                if ($status == 1) {
                    Message::success(__('sucessfull_added_menu'));
                    $this->request->redirect("manage/menu");
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view                       = View::factory('admin/add_menu')->bind('validator', $validator)->bind('errors', $errors)->bind('postvalue', $post_values);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_menu');
        $this->template->page_title = __('add_menu');
        $this->template->content    = $view;
    }    
    public function action_getcountry()
    {
        $output     = '';
        $company_id = arr::get($_REQUEST, 'company_id');
        $state_id   = '';
        $city_id    = '';
        if (isset($company_id)) {
            $location = $this->commonmodel->get_country_details($company_id);
            if (count($location) > 0) {
                $output .= '<option value="' . $location[0]["login_country"] . '">' . $location[0]["country_name"] . '</option>';
                $state_id = $location[0]["login_state"];
                $city_id  = $location[0]["login_city"];
            } else {
                $output .= '<option value="">--Select--</option>';
            }
        }
        echo $output . "~" . $state_id . "~" . $city_id;
        exit;
    }
    public function action_getTelephoneCode()
    {
        $post           = $this->request->post();
        $telephone_code = '';
        if ($post['country_id'] != '') {
            $telephone_code = $this->commonmodel->getCountryTblFlds('telephone_code', $post['country_id']);
        }
        echo $telephone_code;
        exit;
    }
    /** Function to get taxi min speed and speed from the selected model **/
    public function action_getTaxiSpeed()
    {
        $post   = $this->request->post();
        $resArr = array();
        if (!empty($post['modelId'])) {
            $resArr = $this->commonmodel->getTaxiSpeed($post['modelId']);
        }
        echo json_encode($resArr);
        exit;
    }
    public function action_getmodelfare()
    {
        $company_id = arr::get($_REQUEST, 'company_id');
        if (isset($company_id)) {
            $models = $this->commonmodel->getcompany_models($company_id);
            if (!empty($models)) {
                $field_type = '';
?>
                    <select name="taxi_model" id="taxi_model" onchange="getTaxiSpeed(this.value)" class="required" title="<?php echo __('select_the_taximodel'); ?>" >
                            <option value="">--Select--</option>
                            <?php foreach ($models as $list) { ?>
                                    <option value="<?php echo $list['model_id']; ?>" <?php if ($field_type == $list['model_id']) { echo 'selected=selected'; } ?> >
                                            <?php echo ucfirst($list['model_name']); ?>
                                    </option>
                            <?php } ?>
                    </select>
                    <?php } else { ?>
                    <select name="taxi_model" id="taxi_model" onchange="getTaxiSpeed(this.value)" class="required" title="<?php echo __('select_the_taximodel'); ?>" >
                            <option value="">
                                    <?php echo __('nomodels_found'); ?>
                            </option>
                    </select>
            <?php
            }
        }
        exit;
    }
    
    public function action_popularplace()
    {		
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ($usertype != 'A' && $usertype != 'S') {
            $this->request->redirect("admin/login");
        }
        $add_model     = Model::factory('add');
        /**To get the form submit button name**/
        $signup_submit = arr::get($_POST, 'submit_addpopularplace');
        $post_values   = $errors = array();
        $city_details = $add_model->city_details();
        if ($signup_submit && Validation::factory($_POST)) {
            //$post_values = Securityvalid::sanitize_inputs(Arr::map('trim', $this->request->post()));
            $post_values = $this->request->post();
            $validator = $add_model->validate_addpopularplace(arr::extract($post_values, array(
                'city_name',
                'label_name',
                'location_name',
                'location_icon'
            )));
            if ($validator->check()) {
                $add_popular = $add_model->add_popularplace($post_values);
                if ($add_popular == 1) {
                    Message::success(__('sucessfull_added_popular'));
                    $this->request->redirect("manage/popularplace");
                }
            } else {
                $errors = $validator->errors('errors');
            }
		}
		$location_icon = (defined('POPULAR_ICONS')) ? POPULAR_ICONS : array();
		$view = View::factory('admin/add_popularplace')
			->bind('validator', $validator)
			->bind('errors', $errors)
			->bind('postvalue', $post_values)
			->bind('location_icon', $location_icon)
			->bind('city_details', $city_details);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_popular');
        $this->template->page_title = __('add_popular');
        $this->template->content    = $view;
    }
    
    public function action_check_locationdetails(){
		
		$post_values = $_POST;
		$validate = array();
		if(!empty($post_values)){
			$add_model     = Model::factory('add');
			$validate = $add_model->validate_places($post_values);
		}
		echo json_encode($validate);
		exit;
	}
        
   public function action_driver_import()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $add_model      = Model::factory('add');
        $cid            = $this->request->param('id');
        $usertype = $_SESSION['user_type'];
        if ($cid == '') {
            $cid = $_SESSION['company_id'];
        }
        /**To get the form submit button name**/
        $signup_submit       = arr::get($_REQUEST, 'submit_driver');
       // $country_details     = $add_model->country_details();
       // $state_details       = $add_model->state_details();
        //$city_details        = $add_model->city_details();
       // $taxicompany_details = $add_model->taxicompany_details();
        $errors              = array();
        $post_values         = array();
        $success='';
        $add_model      = Model::factory('add');
        if ($signup_submit && Validation::factory($_POST)) {
            if (!empty($_FILES['excel_file'])){
				
				$file = $_FILES['excel_file'];
                                $unique_id = uniqid();
				$image_name = $unique_id .$_FILES['excel_file']['name']; 
                                
				$filename =Upload::save($_FILES['excel_file'],$image_name,DOCROOT.'public/uploads/user_excel/', '0777');
				
				require_once DOCROOT . 'modules/PHPExcel/Classes/PHPExcel/IOFactory.php';
				require_once DOCROOT . 'modules/PHPExcel/Classes/PHPExcel.php';
                                                
						$path = DOCROOT . 'public/uploads/user_excel/' . $unique_id .$file["name"];
                                                @chmod($path,'777');
                                                 
						//move_uploaded_file($file["tmp_name"], $path);
						if (file_exists($path)) {
                            // try {

							$pathInfo     = pathinfo($file["name"]);
							$type         = $pathInfo['extension'] == 'xlsx' ? 'Excel2007' : 'Excel5';
							$objReader    = PHPExcel_IOFactory::createReader($type);
							$objPHPExcel  = $objReader->load($path);
							foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
								$worksheets[$worksheet->getTitle()] = $worksheet->toArray();
							}
							$xls_content = $worksheets['Sheet1'];
                                                        
                                                        //print_r($xls_content);exit;
							
							
							foreach($xls_content as $key => $val)
							{
								//try
								//{
                                                                if($key != '0')
                                                                {
                                                                        array_splice($val, 9);
                               
                                                                        $field_array = array('id','firstname','lastname','phone','email','password','gender','address','driver_license_id');
                                                                        $value_array = array_combine($field_array,$val);


                                                                        $validator = $add_model->validate_adddriverimport($value_array);

                                                                        if($validator->check())
                                                                        {
                                                                               $add_model->add_driver_import($value_array);

                                                                                // $db->commit();
                                                                                $success = 1;
                                                                        }
                                                                        else
                                                                        {
                                                                                $errors[$key] = $validator->errors('errors');
                                                                                //print_r($errors);exit;
                                                                        }	
                                                                }	
                                                        /*}
								catch (Database_Exception $e)
								{
									$errors[0]='Error Uploading Files';
									 // $db->rollback();
								}	*/		
							}
							/*if (!empty($worksheets['banksexcel']) && is_array($worksheets['banksexcel'])) {
								if ( file_exists( $path ) ) {
									unlink( $path );
								}
								return self::processdata($worksheets['banksexcel']);
							} else {
								return NOT_FOUND;
							}*/
                                                     /* } catch (Exception $ex) {
                                                        $errors[0]='Invalid file uploaded';     
                                                    }*/
                                            } else {
							$errors[0]='Error Uploading Files';
						}
                                                                                                        

						/*$image_name = uniqid().$_FILES['excel_file']['name']; 
						$filename =Upload::save($_FILES['excel_file'],$image_name,DOCROOT.USER_IMGPATH, '0777');
						$status = $this->admin->add_users($validator,$_POST,$image_name,$activation_key);

							//Flash message 
							Message::success(__('user_add_flash'));
							
							/*Dashboard Page Redirection*/
							//$this->request->redirect("manageusers/index");
			}else{
				//validation error msg hits here
				$errors[0]='Please Upload files to import';;
			}
		}
		if(count($errors) == 0 && $success == 1)
		{
			//$db->commit();
			Message::success(__('driver_imported_success'));
			$this->request->redirect("manage/driver");
		}
        $view = View::factory('admin/driver_import')
                ->bind('validator', $validator)
                ->bind('errors', $errors)
                ->bind('postvalue', $post_values);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_driver_import');
        $this->template->page_title = __('add_driver_import');
        $this->template->content    = $view;
    }
    
       public function action_taxi_import()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $add_model      = Model::factory('add');
        $cid            = $this->request->param('id');
        $usertype = $_SESSION['user_type'];
        if ($cid == '') {
            $cid = $_SESSION['company_id'];
        }
        /**To get the form submit button name**/
        $signup_submit       = arr::get($_REQUEST, 'submit_driver');
       // $country_details     = $add_model->country_details();
       // $state_details       = $add_model->state_details();
        //$city_details        = $add_model->city_details();
       // $taxicompany_details = $add_model->taxicompany_details();
        $errors              = array();
        $post_values         = array();
        $success='';
        $add_model      = Model::factory('add');
        if ($signup_submit && Validation::factory($_POST)) {
            if (!empty($_FILES['excel_file'])){
				
				$file = $_FILES['excel_file'];
                                $unique_id = uniqid();
				$image_name = $unique_id.'taxi'.$_FILES['excel_file']['name']; 
                                
				$filename =Upload::save($_FILES['excel_file'],$image_name,DOCROOT.'public/uploads/user_excel/', '0777');
				
				require_once DOCROOT . 'modules/PHPExcel/Classes/PHPExcel/IOFactory.php';
				require_once DOCROOT . 'modules/PHPExcel/Classes/PHPExcel.php';
                                                
						$path = DOCROOT . 'public/uploads/user_excel/' . $unique_id.'taxi'.$file["name"];
                        @chmod($path,'777');
                                                 
						//move_uploaded_file($file["tmp_name"], $path);
						if (file_exists($path)) {
                            // try {

							$pathInfo     = pathinfo($file["name"]);
							$type         = $pathInfo['extension'] == 'xlsx' ? 'Excel2007' : 'Excel5';
							$objReader    = PHPExcel_IOFactory::createReader($type);
							$objPHPExcel  = $objReader->load($path);
							foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
								$worksheets[$worksheet->getTitle()] = $worksheet->toArray();
							}
							$xls_content = $worksheets['Sheet1'];
				
							foreach($xls_content as $key => $val)
							{
								//try
								//{
									if($key != '0')
									{  
											array_splice($val, 12);
											$field_array = 
											array('taxi_id','taxi_no','taxi_model','taxi_owner_name','taxi_manufacturer','taxi_motor_expire_date','taxi_insurance_number','taxi_insurance_expire_date','taxi_pco_licence_number','taxi_pco_licence_expire_date','taxi_colour','minimum_luggage');
											$value_array = array_combine($field_array,$val);

											$validator = $add_model->validate_addtaxiimport($value_array);

											if($validator->check())
											{
												   $add_model->addtaxi_import($value_array);

													// $db->commit();
													$success = 1;
											}
											else
											{
													$errors[$key] = $validator->errors('errors');
													//print_r($errors);exit;
											}	
									}	
								/*}
								catch (Database_Exception $e)
								{
									$errors[0]='Error Uploading Files';
									 // $db->rollback();
								}	*/		
							}
							/*if (!empty($worksheets['banksexcel']) && is_array($worksheets['banksexcel'])) {
								if ( file_exists( $path ) ) {
									unlink( $path );
								}
								return self::processdata($worksheets['banksexcel']);
							} else {
								return NOT_FOUND;
							}*/
                                                     /* } catch (Exception $ex) {
                                                        $errors[0]='Invalid file uploaded';     
                                                    }*/
                                            } else {
							$errors[0]='Error Uploading Files';
						}
                                                                                                        

						/*$image_name = uniqid().$_FILES['excel_file']['name']; 
						$filename =Upload::save($_FILES['excel_file'],$image_name,DOCROOT.USER_IMGPATH, '0777');
						$status = $this->admin->add_users($validator,$_POST,$image_name,$activation_key);

							//Flash message 
							Message::success(__('user_add_flash'));
							
							/*Dashboard Page Redirection*/
							//$this->request->redirect("manageusers/index");
			}else{
				//validation error msg hits here
				$errors[0]='Please Upload files to import';;
			}
		}
		if(count($errors) == 0 && $success == 1)
		{
			//$db->commit();
			Message::success(__('taxi_imported_success'));
			$this->request->redirect("manage/taxi");
		}
        $view = View::factory('admin/taxi_import')
                ->bind('validator', $validator)
                ->bind('errors', $errors)
                ->bind('postvalue', $post_values);
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __('add_taxi_import');
        $this->template->page_title = __('add_taxi_import');
        $this->template->content    = $view;
    }
} // End Welcome
?>
