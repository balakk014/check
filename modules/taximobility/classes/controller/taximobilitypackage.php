<?php

defined('SYSPATH') or die('No direct script access.');
/* * ****************************************
 * Package controller
 * @Package: Taximobility
 * @Author: Taxi Team
 * @URL : taximobility.com
 * ****************************************** */

class Controller_TaximobilityPackage extends Controller_Siteadmin {

    /**
     * __construct()
     */
    public $template = 'admin/packagetemplate';

    public function __construct(Request $request, Response $response) {
        
        parent::__construct($request, $response);
        $this->session = Session::instance();
        $user_type = $this->session->get('user_type');
        if ($user_type == 'C') {
            Message::error(__('invalid_access'));
            $this->request->redirect(URL_BASE . "company/dashboard");
        } else if ($user_type == 'M') {
            Message::error(__('invalid_access'));
            $this->request->redirect(URL_BASE . "manager/dashboard");
        }
        $this->is_login();
    }

    public function action_index() {
        $this->request->redirect(URL_BASE . "package/home");
    }

    public function is_login() {
        $session = Session::instance();
        //get current url and set it into session
        //========================================
        $request_url=Request::detect_uri();
        $this->session->set('requested_url', Request::detect_uri());
        /** To check Whether the user is logged in or not* */
        if($request_url!='package/subscription_extend'){
        if (!isset($this->session) || (!$this->session->get('userid')) && !$this->session->get('id')) {
            Message::error(__('login_access'));
            $this->request->redirect("/admin/login/");
        }
        }
        return;
        
    }

    /**
     * Force Profile Update
     * @return admin edit profile
     */
    public function action_editprofile_user() {
        $this->is_login();
        $usertype = $_SESSION['user_type'];
        if ($usertype == 'C') {
            $this->request->redirect("company/login");
        }
        $config_country['taxicountry'] = $this->country_info();
        if ($usertype == 'M') {
            $this->request->redirect("manager/login");
        }
        //get current page segment id 
        $usrid = $this->request->param('userid');
        $id = $this->request->param('id');
        $userid = isset($usrid) ? $usrid : $id;
        if ($_SESSION['userid'] != $userid) {
            Message::error(__('invalid_access'));
            $this->request->redirect("admin/dashboard");
        }
        //check current action
        $action = $this->request->action();
        $action .= "/" . $userid;
        $postvalue = Arr::map('trim', $this->request->post());

        $add_company = Model::factory('add');
        $package = Model::factory('package');

        $country_details = $package->country_details();
        $city_details = $add_company->city_details();
        $state_details = $add_company->state_details();
        $getadmin_profile_info = $package->getadmin_profile_info();
        $get_site_info = $package->get_site_info();
        
        if (isset($get_site_info[0]['profile_status'])) {
            if ($get_site_info[0]['profile_status'] != 0) {
                Message::error(__('invalid_access_profile'));
                $this->request->redirect("admin/dashboard");
            }
        }
        if (isset($get_site_info[0]['domain_name'])) {
            $business_name = $get_site_info[0]['domain_name'];
        } else {
            $business_name = '';
        }
        $user_timezone = $get_site_info[0]['user_time_zone'];
        //getting request for form submit
        $editprofile = arr::get($_REQUEST, 'submit_editprofile');
        $errors = array();
        if (isset($editprofile) && Validation::factory($_POST)) {
            $post_values = Securityvalid::sanitize_inputs($postvalue);
            $validator = $package->editprofile_validate(arr::extract($post_values, array('firstname', 'lastname', 'email', 'phone', 'address', 'country', 'state', 'city', 'user_time_zone', 'iso_country_code', 'telephone_code', 'currency_code', 'currency_symbol', 'postal_code')), $userid);
            if ($validator->check()) {
                if(PACKAGE_TYPE==3){
                    require Kohana::find_file('classes/controller', 'ndotmobile_crypt');
                    $mobile_data_ndot_crypt=new Mobile_key_crypt();
                    $post_values['mobile_api_key']=$mobile_data_ndot_crypt->mobile_encrypt_encode();
                }
                
                $status = $package->edit_people($userid, $post_values);

                if ($status == 1) {
                    //Profile data updated with Crm
                    if (CRM_UPDATE_ENABLE == 1 && class_exists('Thirdpartyapi')) {
                        if (method_exists('Thirdpartyapi', 'crm_update_profile')) {
                            $thirdpartyapi = Thirdpartyapi::instance();
                            $thirdpartyapi->crm_update_profile($post_values);
                        }
                    }
                    Message::success(__('profile_updated_successfully'));
                } else {
                    Message::error(__('not_updated'));
                }
                $this->request->redirect("package/home");
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $login_details = $package->login_details_byid($userid);
        $email = $_SESSION['email'];
        
        $get_login_info='';
         if (CRM_UPDATE_ENABLE == 1 && class_exists('Thirdpartyapi')) {
                        if (method_exists('Thirdpartyapi', 'crm_get_app_login_info')) {
                            $thirdpartyapi = Thirdpartyapi::instance();
                           $get_login_info=$thirdpartyapi->crm_get_app_login_info();
                        }
                    }
                  
        $view = View::factory(ADMINVIEW . 'package_plan/editprofile_trialuser')
                ->bind('errors', $errors)
                ->bind('action', $action)
                ->bind('validate', $validate)
                ->bind('user_exists', $user_exists)
                ->bind('postvalue', $postvalue)
                ->bind('country_details', $country_details)
                ->bind('all_country_list', $config_country['taxicountry'])
                ->bind('city_details', $city_details)
                ->bind('state_details', $state_details)
                ->bind('login_detail', $login_details)
                ->bind('user_timezone', $user_timezone)
                ->bind('business_name', $business_name)
                ->bind('email', $email)
                ->bind('login_credentials', $get_login_info);
        $this->template->content = $view;
        $this->template->meta_description = CLOUD_SITENAME . " | Admin ";
        $this->template->meta_keywords = CLOUD_SITENAME . "  | Admin ";
        $this->template->title = "Edit Profile";
        $this->template->page_title = "Edit Profile";
    }

    
    
  
    /**
     * Payment gateway related - create hashing for payment gateway
     * @param double $chargetotal
     * @param int $Currency
     * @param int $storeId
     * @param varchar $sharedSecret
     * @return type
     */
    public function createHash($chargetotal, $Currency, $storeId, $sharedSecret) {
        // Please change the store Id to your individual Store ID
        // NOTE: Please DO NOT hardcode the secret in that script. For example read it from a database.
        $stringToHash = $storeId . $this->getDateTime() . $chargetotal . $Currency . $sharedSecret;
        $ascii = bin2hex($stringToHash);
        return sha1($ascii);
    }

    /**
     * Payment Gateway Related Functionalities - Get current date time    
     * @return currentdatetime
     */
    public function getDateTime() {
        date_default_timezone_set('Asia/Kolkata');
        $dateTime = date('Y:m:d-H:i:s');
        return $dateTime;
    }

   

    /**
     * Cloud Account information
     */
    public function action_account() {
        $package = Model::factory('package');
        $get_admin_info = $package->getadmin_profile_info();
        $get_site_info = $package->get_site_info();
        $get_paid_info = $package->get_package_paid_info();

        $check_current_driver_count = $package->check_current_driver_count();

        if (isset($check_current_driver_count[0]['_id'])) {
            $total_driver_count = count($check_current_driver_count);
        } else {
            $total_driver_count = 0;
        }

        $this->template->title = CLOUD_SITENAME . ' | ' . _('account_info');
        $this->selected_page_title = __("account_info");
        $this->template->page_title = __('account_info');
        $this->meta_description = "";
        $this->template->content = View::factory("admin/package_plan/account")
                ->bind('admin_info', $get_admin_info)
                ->bind('get_site_info', $get_site_info)
                ->bind('total_driver_count', $total_driver_count)
                ->bind('get_paid_info', $get_paid_info);
    }

    /**
     * Add credit card information 
     */
    public function action_addcreditcard() {
        $package = Model::factory('package');
        $btn_card_confirm = arr::get($_REQUEST, 'btn_card_confirm');
        $config_country['taxicountry'] = $this->country_info();
        $this->session = Session::instance();
        $errors = array();
        $userid = $this->session->get('userid');
        $postvalue = Arr::map('trim', $this->request->post());

        $getadmin_profile_info = $package->getadmin_profile_info();


        $post_values = Securityvalid::sanitize_inputs($postvalue);
        $billing_card_info_details = $package->billing_card_info_details();
        if (empty($billing_card_info_details)) {
            $billing_info_id = '';
        } else {
            $billing_info_id = $billing_card_info_details[0]['_id'];
        }
        if (isset($btn_card_confirm) && Validation::factory($_POST)) {
            $validator = $package->upgrade_plan_validate(arr::extract($post_values, array('cardnumber', 'cvv', 'expirydate', 'firstName', 'lastname', 'address', 'postal_code', 'terms', 'country', 'state', 'city')), $userid);
            if ($validator->check()) {
                $cardnumber = $postvalue['cardnumber'];
                $cvv = $postvalue['cvv'];
                $expirydate = explode('/', $postvalue['expirydate']);
                $firstname = $postvalue['firstName'];
                $lastname = $postvalue['lastname'];
                $address = $postvalue['address'];
                $city = $postvalue['city'];
                $country = $postvalue['country'];
                $state = $postvalue['state'];
                $postal_code = $postvalue['postal_code'];
                $package_upgrade_time = PACKAGE_UPGRADE_TIME;
                $cardnumber = preg_replace('/\s+/', '', $cardnumber);
                
                $this->billing_info['card_number'] = $cardnumber;
                $this->billing_info['cvv'] = $cvv;
                $this->billing_info['expirationMonth'] = $expirydate[0];
                $this->billing_info['expirationYear'] = $expirydate[1];
                $this->billing_info['firstName'] = $firstname;
                $this->billing_info['lastname'] = $lastname;
                $this->billing_info['address'] = $address;
                $this->billing_info['city'] = $city;
                $this->billing_info['country'] = $country;
                $this->billing_info['state'] = $state;
                $this->billing_info['postal_code'] = $postal_code;
                $this->billing_info['createdate'] = $package_upgrade_time;                
                $this->billing_info['currency']=CLOUD_CURRENCY_FORMAT;             
                $billing_info_reg = $package->billing_registration($this->billing_info, $billing_info_id);
                if ($billing_info_reg) {
                    Message::success(__('billing_updated_sucessfully'));
                }
            } else {
                $errors = $validator->errors('errors');
            }
        }
        $view = View::factory(ADMINVIEW . 'package_plan/addcreditcard')
                ->bind('postedvalues', $this->userPost)
                ->bind('errors', $errors)
                ->bind('getadmin_profile_info', $getadmin_profile_info)
                ->bind('subscription_cost_month', $subscription_cost_month)
                ->bind('billing_card_info_details', $billing_card_info_details)
                ->bind('all_country_list', $config_country['taxicountry'])
                ->bind('setup_cost', $setup_cost)
                ->bind('postvalue', $post_values);
        $this->template->title = CLOUD_SITENAME . " | " . __('add_credit_card');
        $this->template->page_title = __('add_credit_card');
        $this->template->content = $view;
    }

    /**
     *  Completed account transaction amount details
     */
    public function action_account_summary() {
        $package = Model::factory('package');
        $paid_amount = $package->total_paid_amount();        
        $this->template->title = CLOUD_SITENAME . ' | Account';        
        $this->template->page_title = __('account_info');
        $this->meta_description = "";
        $this->template->content = View::factory("admin/package_plan/account_summary")
                ->bind('paid_amount', $paid_amount);
    }

    /**
     * All transaction summary details
     */
    public function action_account_summary_details() {
        $user_createdby = $_SESSION['userid'];
        $usertype = $_SESSION['user_type'];
        if ($usertype != 'A' && $usertype != 'S') {
            $this->request->redirect("admin/dashboard");
        }
        //Page Title
        $this->page_title = __('account_summary_details');
        $this->selected_page_title = __('account_summary_details');
        $package = Model::factory('package');
        $count_package_info = $package->count_package_info();
        //pagination loads here
        //-------------------------
        $page_no = isset($_GET['page']) ? $_GET['page'] : 0;

        if ($page_no == 0 || $page_no == 'index')
            $page_no = PAGE_NO;
        $offset = REC_PER_PAGE * ($page_no - 1);

        $pag_data = Pagination::factory(array(
                    'current_page' => array('source' => 'query_string', 'key' => 'page'),
                    'items_per_page' => REC_PER_PAGE,
                    'total_items' => $count_package_info,
                    'view' => 'pagination/punbb',
        ));
        $all_package_info_list = $package->all_package_info_list($offset, REC_PER_PAGE);
        //****pagination ends here***//
        //Find page action in view
        $action = $this->request->action();
        //send data to view file 
        $view = View::factory(ADMINVIEW . 'package_plan/account_summary_details')
                ->bind('all_package_info_list', $all_package_info_list)
                ->bind('pag_data', $pag_data)
                ->bind('srch', $_REQUEST)
                ->bind('Offset', $offset);
        $this->template->title = CLOUD_SITENAME . " | " . __('account_summary_details');
        $this->template->page_title = __('account_summary_details');
        $this->template->content = $view;
    }

    /**
     *  Completed account per transaction details
     */
    public function action_account_transaction() {
        $package = Model::factory('package');
        $invoice_id = explode('/', $_SERVER['REQUEST_URI']);
        $transaction_info = $package->account_transaction($invoice_id[3]);
        $this->template->title = CLOUD_SITENAME . ' | Account';
        $this->template->page_title = __('account_transaction_details');
        $this->meta_description = "";
        $this->template->content = View::factory("admin/package_plan/account_transaction")
                ->bind('transaction_info', $transaction_info);
    }

    
    /**
     * Custom and default Language and color code files upload page
     */
    public function action_preferences() {
        $package = Model::factory('package');
        $get_langcolor_info = $package->get_langcolor_info();
        $this->template->meta_description = CLOUD_SITENAME . " | Preferences ";
        $this->template->meta_keywords = CLOUD_SITENAME . " | Preferences ";
        $this->template->title = CLOUD_SITENAME . " | " . __('Preferences');
        $this->template->page_title = __('Preferences');
        $this->template->content = View::factory("admin/package_plan/preferences")->bind('langcolor_info', $get_langcolor_info);
    }

    /**
     * Account home page
     */
    public function action_home() {
        $package = Model::factory('package');
        $get_admin_info = $package->getadmin_profile_info();
        $get_site_info = $package->get_site_info();
        $get_payment_gateway_info = $package->get_payment_gateway_info();
        if (isset($get_payment_gateway_info['payment_gatway'])) {
            $payment_gateway = $get_payment_gateway_info['payment_gatway'];
        } else {
            $payment_gateway = '';
        }

        $this->template->title = CLOUD_SITENAME . " | " . __('account_breadcrumb');
        $this->template->page_title = __('account_breadcrumb');
        $this->meta_description = "";
        $this->template->content = View::factory("admin/package_plan/home")
                ->bind('admin_info', $get_admin_info)
                ->bind('get_site_info', $get_site_info)
                ->bind('get_payment_gateway_info', $payment_gateway);
    }

    /**
     * Custom web language file upload
     */
    public function action_web_language() {
        $package = Model::factory('package');
        $errors = array();
        $postvalue = $this->request->post();
        $action = $this->request->action();
        if(isset($postvalue['dynamic_lang']) && $postvalue['dynamic_lang']!=""){
            $dynamic_lang = $postvalue['dynamic_lang'];
        }
        $language_setting_array = WEB_DB_LANGUAGE;
        if (isset($postvalue['web_lang_radio']) && $postvalue['web_lang_radio'] == 2 && Validation::factory(array_merge($_FILES,$postvalue))) {
            $validator = $package->validate_web_language(array_merge($_FILES,$postvalue));
            if ($validator->check()) {
                $get_site_info=$package->get_site_info();
                $domain_name=$get_site_info[0]['domain_name'];
                if (!empty($_FILES['web_language_file']['name'])) {
                    $image_type = explode('.', $_FILES['web_language_file']['name']);
                    $image_type = end($image_type);
                    $image_name = $dynamic_lang.'_customize.' . $image_type;
                    $fileName = $dynamic_lang.'_customize.xml';
                    
                    $target_path=CUSTOMLANGPATH.'i18n/';
                 if (!is_dir($target_path) ) {
                      Message::error(__('mentioned directory not availabe'));
                    $this->request->redirect('/package/preferences');
                 }
                    
                    $illegal_words = array('unlink', 'unset', 'exit;', 'break;');
                    $file_handle = fopen($_FILES['web_language_file']['tmp_name'], "r");
                    while (!feof($file_handle)) {
                        $line_of_text = fgets($file_handle);
                        if ($this->match($illegal_words, strtolower($line_of_text))) {
                            Message::error(__('faile_upload_changes_made_info'));
                            $this->request->redirect('/package/preferences');
                        }
                    }
                    fclose($file_handle);
                    
                   

                   /* if (file_exists($target_path . $image_name) && file_exists($target_path . $fileName)) {
                        rename($target_path . $image_name, $target_path . 'en_customize.php');
                    } elseif (file_exists($target_path . $image_name)) {
                        rename($target_path . $image_name, $target_path . $fileName);
                    }*/
                    if (file_exists($target_path . $image_name)) {
                        unlink($target_path . $image_name);
                    }
                    
                    move_uploaded_file($_FILES['web_language_file']['tmp_name'], $target_path . $image_name);
                    //rename($target_path . $image_name, $target_path . $fileName);
                    chmod($target_path . $image_name, 0777);
                   try {
                   
                    //print_r($target_path.$image_name); exit;
                     //print_r($target_path.$image_name); exit;
					$fileContents = file_get_contents($target_path.$image_name);                    
					$fileContents=preg_replace('/[\x00-\x1f]/','',htmlspecialchars($fileContents));					
                    $xml_system = simplexml_load_string($fileContents) or die("Error: Cannot create object");
                    
                    } catch (Exception $ex) {
                       throw new Exception($ex);
                       // Message::error(__('fail_upload_checkfile_error_info'));
                       // $this->request->redirect('/package/preferences');
                    }
                    
                     $child_array='';
                     
                     foreach ($xml_system->children()->string as $value) {           
                        $name=(string)$value['name'];                        
                        $value_string=(string) ($value);
                        $value_string=htmlentities($value_string);
                        $value_string= str_replace('"', "'", $value_string);
                        $child_array.='"'.$name.'"'.'=>'.'"'. $value_string.'"'.',';
                     }      
                     if (file_exists($target_path .$dynamic_lang.'.php')) {
                        unlink($target_path.$dynamic_lang.'.php');
                    }
                     
                    $string="<?php defined('SYSPATH') or die('No direct script access.');"
                            . "return ";
                    
                    $fp = fopen($target_path.$dynamic_lang.'.php', 'w');
                    chmod($target_path . $dynamic_lang.'.php', 0777);
                    fwrite($fp, print_r($string, TRUE));
                    fwrite($fp, print_r('['.$child_array.']', TRUE));
                    fwrite($fp, print_r(';', TRUE));
                    fclose($fp);
                    
                    $language_setting_array[$dynamic_lang] = 2;
                    $data = array('website_language_settings', $language_setting_array);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                } else {
                    Message::error(__('fail_upload_error_info'));
                    $this->request->redirect('/package/preferences');
                }
            } else {
                $errors = $validator->errors('errors');
                Message::error(__('file_upload_warning_info'));
            }
        } else {
            if (isset($postvalue['web_lang_radio']) && $postvalue['web_lang_radio'] == 1) {
                $validator = $package->validate_web_language($postvalue);
                if ($validator->check()) {
                    $language_setting_array[$dynamic_lang] = 1;
                    $data = array('website_language_settings', $language_setting_array);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_default_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                } else {
                    $errors = $validator->errors('errors');
                    Message::error(__('fail_upload_default_info'));
                }
            } else {
                Message::error(__('fail_upload_default_info'));
                $this->request->redirect('/package/preferences');
            }
        }
        $this->template->meta_description = CLOUD_SITENAME . " | Preferences ";
        $this->template->meta_keywords = CLOUD_SITENAME . " | Preferences ";
        $this->template->title = CLOUD_SITENAME . " | " . __('Preferences');
        $this->template->page_title = __('Preferences');
        $this->template->content = View::factory("admin/package_plan/preferences")->bind('action', $action)->bind('postvalue', $postvalue)->bind('errors', $errors);
    }

    public function match($needles, $haystack) {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Custom IOS language & color code upload
     */
    public function action_ios_language_colorcode() {
        $package = Model::factory('package');
        $errors = array();
        $postvalue = $this->request->post();
        $action = $this->request->action();
        if(isset($postvalue['dynamic_lang']) && $postvalue['dynamic_lang']!=""){
            $dynamic_lang = $postvalue['dynamic_lang'];
            $dynamic_lang_name = ucfirst(DYNAMIC_LANGUAGE_ARRAY[$dynamic_lang]);
        }
        $ios_db_driver_lang = IOS_DRIVER_LANG;
        $ios_db_passenger_lang = IOS_PASSENGER_LANG;
        $ios_db_driver_colorcode = IOS_DRIVER_COLORCODE;
        $ios_db_passenger_colorcode = IOS_PASSENGER_COLORCODE;
        if (isset($postvalue['ios_driver_lang_radio']) && $postvalue['ios_driver_lang_radio'] == 2 && Validation::factory(array_merge($_FILES,$postvalue))) {
            $validator = $package->validate_ios_driver_language(array_merge($_FILES,$postvalue));
            if ($validator->check()) {
                if (!empty($_FILES['ios_driver_language_file']['name'])) {
                    $image_type = explode('.', $_FILES['ios_driver_language_file']['name']);
                    $image_type = end($image_type);
                    $image_name = 'Localizable_'.$dynamic_lang_name.'.' . $image_type;
                    $fileName = 'Localizable_'.$dynamic_lang_name.'_default.strings';
                    $org_target_path = DOCROOT . SAMPLE_IOS_LANG_FILES . 'driver/';
                    $default_target_path = DOCROOT . IOS_DEFAULT_CUSTOMIZE_FILES . 'driver/';
                    if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . $fileName)) {
                        rename($org_target_path . $image_name, $default_target_path . 'Localizable_'.$dynamic_lang_name.'_customize.strings');
                    } elseif (file_exists($org_target_path . $image_name)) {
                        rename($org_target_path . $image_name, $default_target_path . $fileName);
                    }
                    move_uploaded_file($_FILES['ios_driver_language_file']['tmp_name'], $org_target_path . $image_name);
                    chmod($org_target_path . $image_name, 0777);
                    $ios_db_driver_lang[$dynamic_lang] = 2;
                    $data = array('ios_driver_language_settings', $ios_db_driver_lang);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                } else {
                    Message::error(__('fail_upload_error_info'));
                    //$this->request->redirect('/package/preferences');
                }
            } else {
                $errors = $validator->errors('errors');
                Message::error(__('file_upload_warning_info'));
            }
        } elseif (isset($postvalue['ios_driver_lang_radio']) && $postvalue['ios_driver_lang_radio'] == 1) {
             $validator = $package->validate_web_language($postvalue);
                if ($validator->check()) {
                    $image_name = 'Localizable_'.$dynamic_lang_name.'.strings';
                    $org_target_path = DOCROOT . SAMPLE_IOS_LANG_FILES . 'driver/';
                    $default_target_path = DOCROOT . IOS_DEFAULT_CUSTOMIZE_FILES . 'driver/';
                    if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . 'Localizable_'.$dynamic_lang_name.'_default.strings')) {
                        rename($org_target_path . $image_name, $default_target_path . 'Localizable_'.$dynamic_lang_name.'_customize.strings');
                        rename($default_target_path . 'Localizable_'.$dynamic_lang_name.'_default.strings', $org_target_path . $image_name);
                    }
                    $ios_db_driver_lang[$dynamic_lang] = 1;
                    $data = array('ios_driver_language_settings', $ios_db_driver_lang);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_default_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                }else {
                    $errors = $validator->errors('errors');
                    Message::error(__('fail_upload_default_info'));
                }
        } elseif (isset($postvalue['ios_passenger_lang_radio']) && $postvalue['ios_passenger_lang_radio'] == 2 && Validation::factory(array_merge($_FILES,$postvalue))) {
            $validator = $package->validate_ios_driver_language(array_merge($_FILES,$postvalue));
            if ($validator->check()) {
                if (!empty($_FILES['ios_passenger_language_file']['name'])) {
                    $image_type = explode('.', $_FILES['ios_passenger_language_file']['name']);
                    $image_type = end($image_type);
                    $image_name = 'Localizable_'.$dynamic_lang_name.'.' . $image_type;
                    $fileName = 'Localizable_'.$dynamic_lang_name.'_default.strings';
                    $org_target_path = DOCROOT . SAMPLE_IOS_LANG_FILES . 'passenger/';
                    $default_target_path = DOCROOT . IOS_DEFAULT_CUSTOMIZE_FILES . 'passenger/';
                    if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . $fileName)) {
                        rename($org_target_path . $image_name, $default_target_path . 'Localizable_'.$dynamic_lang_name.'_customize.strings');
                    } elseif (file_exists($org_target_path . $image_name)) {
                        rename($org_target_path . $image_name, $default_target_path . $fileName);
                    }
                    move_uploaded_file($_FILES['ios_passenger_language_file']['tmp_name'], $org_target_path . $image_name);
                    chmod($org_target_path . $image_name, 0777);
                    $ios_db_passenger_lang[$dynamic_lang] = 2;
                    $data = array('ios_passenger_language_settings', $ios_db_passenger_lang);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                } else {
                    Message::error(__('fail_upload_error_info'));
                    //$this->request->redirect('/package/preferences');
                }
            } else {
                $errors = $validator->errors('errors');
                Message::error(__('file_upload_warning_info'));
            }
        } elseif (isset($postvalue['ios_passenger_lang_radio']) && $postvalue['ios_passenger_lang_radio'] == 1) {
            $image_name = 'Localizable_'.$dynamic_lang_name.'.strings';
            $org_target_path = DOCROOT . SAMPLE_IOS_LANG_FILES . 'passenger/';
            $default_target_path = DOCROOT . IOS_DEFAULT_CUSTOMIZE_FILES . 'passenger/';
            if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . 'Localizable_'.$dynamic_lang_name.'_default.strings')) {
                rename($org_target_path . $image_name, $default_target_path . 'Localizable_'.$dynamic_lang_name.'_customize.strings');
                rename($default_target_path . 'Localizable_'.$dynamic_lang_name.'_default.strings', $org_target_path . $image_name);
            }
            $ios_db_passenger_lang[$dynamic_lang] = 1;
            $data = array('ios_passenger_language_settings', $ios_db_passenger_lang);
            $status = $package->update_language_colorcode($data);
            Message::success(__('file_default_upload_succ_info'));
            $this->request->redirect('/package/preferences');
        } elseif (isset($postvalue['ios_driver_colorcode_radio']) && $postvalue['ios_driver_colorcode_radio'] == 2 && Validation::factory(array_merge($_FILES,$postvalue))) {
            $validator = $package->validate_ios_driver_language(array_merge($_FILES,$postvalue));
            if ($validator->check()) {
                if (!empty($_FILES['ios_driver_colorcode_file']['name'])) {
                    $image_type = explode('.', $_FILES['ios_driver_colorcode_file']['name']);
                    $image_type = end($image_type);
                    $image_name = 'DriverAppColor.' . $image_type;
                    $fileName = 'DriverAppColor_default.xml';
                    $org_target_path = DOCROOT . SAMPLE_IOS_COLORCODE_FILES;
                    $default_target_path = DOCROOT . IOS_DEFAULT_CUSTOMIZE_FILES;
                    if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . $fileName)) {
                        rename($org_target_path . $image_name, $default_target_path . 'DriverAppColor_customize.xml');
                    } elseif (file_exists($org_target_path . $image_name)) {
                        rename($org_target_path . $image_name, $default_target_path . $fileName);
                    }
                    move_uploaded_file($_FILES['ios_driver_colorcode_file']['tmp_name'], $org_target_path . $image_name);
                    chmod($org_target_path . $image_name, 0777);
                    $ios_db_driver_colorcode[$dynamic_lang] = 2;
                    $data = array('ios_driver_colorcode_settings', $ios_db_driver_colorcode);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                } else {
                    Message::error(__('fail_upload_error_info'));
                    //$this->request->redirect('/package/preferences');
                }
            } else {
                $errors = $validator->errors('errors');
                Message::error(__('file_upload_warning_info'));
            }
        } elseif (isset($postvalue['ios_driver_colorcode_radio']) && $postvalue['ios_driver_colorcode_radio'] == 1) {
            $image_name = 'DriverAppColor.xml';
            $org_target_path = DOCROOT . SAMPLE_IOS_COLORCODE_FILES;
            $default_target_path = DOCROOT . IOS_DEFAULT_CUSTOMIZE_FILES;
            if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . 'DriverAppColor_default.xml')) {
                rename($org_target_path . $image_name, $default_target_path . 'DriverAppColor_customize.xml');
                rename($default_target_path . 'DriverAppColor_default.xml', $org_target_path . $image_name);
            }
            $ios_db_driver_colorcode[$dynamic_lang] = 1;
            $data = array('ios_driver_colorcode_settings', $ios_db_driver_colorcode);
            $status = $package->update_language_colorcode($data);
            Message::success(__('file_default_upload_succ_info'));
            $this->request->redirect('/package/preferences');
        } elseif (isset($postvalue['ios_passenger_colorcode_radio']) && $postvalue['ios_passenger_colorcode_radio'] == 2 && Validation::factory(array_merge($_FILES,$postvalue))) {
            $validator = $package->validate_ios_driver_language(array_merge($_FILES,$postvalue));
            if ($validator->check()) {
                if (!empty($_FILES['ios_passenger_colorcode_file']['name'])) {
                    $image_type = explode('.', $_FILES['ios_passenger_colorcode_file']['name']);
                    $image_type = end($image_type);
                    $image_name = 'PassengerAppColor.' . $image_type;
                    $fileName = 'PassengerAppColor_default.xml';
                    $org_target_path = DOCROOT . SAMPLE_IOS_COLORCODE_FILES;
                    $default_target_path = DOCROOT . IOS_DEFAULT_CUSTOMIZE_FILES;
                    if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . $fileName)) {
                        rename($org_target_path . $image_name, $default_target_path . 'PassengerAppColor_customize.xml');
                    } elseif (file_exists($org_target_path . $image_name)) {
                        rename($org_target_path . $image_name, $default_target_path . $fileName);
                    }
                    move_uploaded_file($_FILES['ios_passenger_colorcode_file']['tmp_name'], $org_target_path . $image_name);
                    chmod($org_target_path . $image_name, 0777);
                    $ios_db_passenger_colorcode[$dynamic_lang] = 2;
                    $data = array('ios_passenger_colorcode_settings', $ios_db_passenger_colorcode);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                } else {
                    Message::error(__('fail_upload_error_info'));
                    //$this->request->redirect('/package/preferences');
                }
            } else {
                $errors = $validator->errors('errors');
                Message::error(__('file_upload_warning_info'));
            }
        } elseif (isset($postvalue['ios_passenger_colorcode_radio']) && $postvalue['ios_passenger_colorcode_radio'] == 1) {
            $image_name = 'PassengerAppColor.xml';
            $org_target_path = DOCROOT . SAMPLE_IOS_COLORCODE_FILES;
            $default_target_path = DOCROOT . IOS_DEFAULT_CUSTOMIZE_FILES;
            if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . 'PassengerAppColor_default.xml')) {
                rename($org_target_path . $image_name, $default_target_path . 'PassengerAppColor_customize.xml');
                rename($default_target_path . 'PassengerAppColor_default.xml', $org_target_path . $image_name);
            }
            $ios_db_passenger_colorcode[$dynamic_lang] = 1;
            $data = array('ios_passenger_colorcode_settings', $ios_db_passenger_colorcode);
            $status = $package->update_language_colorcode($data);
            Message::success(__('file_default_upload_succ_info'));
            $this->request->redirect('/package/preferences');
        } else {
            Message::error(__('fail_upload_error_info'));
            // $this->request->redirect('/package/preferences');
        }

        $this->template->meta_description = CLOUD_SITENAME . " | Preferences ";
        $this->template->meta_keywords = CLOUD_SITENAME . " | Preferences ";
        $this->template->title = CLOUD_SITENAME . " | " . __('Preferences');
        $this->template->page_title = __('Preferences');
        $this->template->content = View::factory("admin/package_plan/preferences")->bind('action', $action)->bind('postvalue', $postvalue)->bind('errors', $errors);
    }

    /**
     * Custom Setup with Android Language & color code file upload
     */
    public function action_android_language_colorcode() {
        $package = Model::factory('package');
        $errors = array();
        $postvalue = $this->request->post();
        $action = $this->request->action();
        //echo '<pre>';print_r($postvalue);exit;
        if(isset($postvalue['dynamic_lang']) && $postvalue['dynamic_lang']!=""){
            $dynamic_lang = $postvalue['dynamic_lang'];
            $dynamic_lang_name = ucfirst(DYNAMIC_LANGUAGE_ARRAY[$dynamic_lang]);
        }
        $android_db_driver_lang = ANDROID_DRIVER_LANG;
        $android_db_passenger_lang = ANDROID_PASSENGER_LANG;
        $android_db_driver_colorcode = ANDROID_DRIVER_COLORCODE;
        $android_db_passenger_colorcode = ANDROID_PASSENGER_COLORCODE;
        if (isset($postvalue['android_driver_lang_radio']) && $postvalue['android_driver_lang_radio'] == 2 && Validation::factory(array_merge($_FILES,$postvalue))) {
            # customized language - driver
            $validator = $package->validate_android_driver_language(array_merge($_FILES,$postvalue));
            if ($validator->check()) {
                if (!empty($_FILES['android_driver_language_file']['name'])) {
                    $image_type = explode('.', $_FILES['android_driver_language_file']['name']);
                    $image_type = end($image_type);
                    $image_name = 'strings_'.$dynamic_lang_name.'.' . $image_type;
                    $fileName = 'strings_'.$dynamic_lang_name.'_default.xml';
                    $org_target_path = DOCROOT . SAMPLE_ANDROID_LANG_FILES . 'driver/';
                    $default_target_path = DOCROOT . ANDROID_DEFAULT_CUSTOMIZE_FILES . 'driver/';
                    if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . $fileName)) {
                        rename($org_target_path . $image_name, $default_target_path . 'strings_'.$dynamic_lang_name.'_customize.xml');
                    } elseif (file_exists($org_target_path . $image_name)) {
                        rename($org_target_path . $image_name, $default_target_path . $fileName);
                    }
                    move_uploaded_file($_FILES['android_driver_language_file']['tmp_name'], $org_target_path . $image_name);
                    chmod($org_target_path . $image_name, 0777);
                    $android_db_driver_lang[$dynamic_lang] = 2;
                    $data = array('android_driver_language_settings', $android_db_driver_lang);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                } else {
                    Message::error(__('fail_upload_error_info'));
                    //$this->request->redirect('/package/preferences');
                }
            } else {
                $errors = $validator->errors('errors');
                Message::error(__('file_upload_warning_info'));
            }
        } elseif (isset($postvalue['android_driver_lang_radio']) && $postvalue['android_driver_lang_radio'] == 1) {
            # default language - driver
            $image_name = 'strings_'.$dynamic_lang_name.'.xml';
            $org_target_path = DOCROOT . SAMPLE_ANDROID_LANG_FILES . 'driver/';
            $default_target_path = DOCROOT . ANDROID_DEFAULT_CUSTOMIZE_FILES . 'driver/';
            if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . 'strings_'.$dynamic_lang_name.'_default.xml')) {
                rename($org_target_path . $image_name, $default_target_path . 'strings_'.$dynamic_lang_name.'_customize.xml');
                rename($default_target_path . 'strings_'.$dynamic_lang_name.'_default.xml', $org_target_path . $image_name);
            }
            $android_db_driver_lang[$dynamic_lang] = 1;
            $data = array('android_driver_language_settings', $android_db_driver_lang);
            $status = $package->update_language_colorcode($data);
            Message::success(__('file_default_upload_succ_info'));
            $this->request->redirect('/package/preferences');
        } elseif (isset($postvalue['android_passenger_lang_radio']) && $postvalue['android_passenger_lang_radio'] == 2 && Validation::factory(array_merge($_FILES,$postvalue))) {
            # customized language - passenger
            $validator = $package->validate_android_driver_language(array_merge($_FILES,$postvalue));
            if ($validator->check()) {
                if (!empty($_FILES['android_passenger_language_file']['name'])) {
                    $image_type = explode('.', $_FILES['android_passenger_language_file']['name']);
                    $image_type = end($image_type);
                    $image_name = 'strings_'.$dynamic_lang_name.'.' . $image_type;
                    $fileName = 'strings_'.$dynamic_lang_name.'_default.xml';
                    $org_target_path = DOCROOT . SAMPLE_ANDROID_LANG_FILES . 'passenger/';
                    $default_target_path = DOCROOT . ANDROID_DEFAULT_CUSTOMIZE_FILES . 'passenger/';
                    if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . $fileName)) {
                        rename($org_target_path . $image_name, $default_target_path . 'strings_'.$dynamic_lang_name.'_customize.xml');
                    } elseif (file_exists($org_target_path . $image_name)) {
                        rename($org_target_path . $image_name, $default_target_path . $fileName);
                    }
                    move_uploaded_file($_FILES['android_passenger_language_file']['tmp_name'], $org_target_path . $image_name);
                    chmod($org_target_path . $image_name, 0777);
                    $android_db_passenger_lang[$dynamic_lang] = 2;
                    $data = array('android_passenger_language_settings', $android_db_passenger_lang);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                } else {
                    Message::error(__('fail_upload_error_info'));
                    //$this->request->redirect('/package/preferences');
                }
            } else {
                $errors = $validator->errors('errors');
                Message::error(__('file_upload_warning_info'));
            }
        } elseif (isset($postvalue['android_passenger_lang_radio']) && $postvalue['android_passenger_lang_radio'] == 1) {
            # default language - passenger
            $image_name = 'strings_'.$dynamic_lang_name.'.xml';
            $org_target_path = DOCROOT . SAMPLE_ANDROID_LANG_FILES . 'passenger/';
            $default_target_path = DOCROOT . ANDROID_DEFAULT_CUSTOMIZE_FILES . 'passenger/';
            if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . 'strings_'.$dynamic_lang_name.'_default.xml')) {
                rename($org_target_path . $image_name, $default_target_path . 'strings_'.$dynamic_lang_name.'_customize.xml');
                rename($default_target_path . 'strings_'.$dynamic_lang_name.'_default.xml', $org_target_path . $image_name);
            }
            $android_db_passenger_lang[$dynamic_lang] = 1;
            $data = array('android_passenger_language_settings', $android_db_passenger_lang);
            $status = $package->update_language_colorcode($data);
            Message::success(__('file_default_upload_succ_info'));
            $this->request->redirect('/package/preferences');
        } elseif (isset($postvalue['android_driver_colorcode_radio']) && $postvalue['android_driver_colorcode_radio'] == 2 && Validation::factory(array_merge($_FILES,$postvalue))) {
            # customized colorcode - driver
            $validator = $package->validate_android_driver_language(array_merge($_FILES,$postvalue));
            if ($validator->check()) {
                if (!empty($_FILES['android_driver_colorcode_file']['name'])) {
                    $image_type = explode('.', $_FILES['android_driver_colorcode_file']['name']);
                    $image_type = end($image_type);
                    $image_name = 'driverAppColors.' . $image_type;
                    $fileName = 'driverAppColors_default.xml';
                    $org_target_path = DOCROOT . SAMPLE_ANDROID_COLORCODE_FILES;
                    $default_target_path = DOCROOT . ANDROID_DEFAULT_CUSTOMIZE_FILES;
                    if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . $fileName)) {
                        rename($org_target_path . $image_name, $default_target_path . 'driverAppColors_customize.xml');
                    } elseif (file_exists($org_target_path . $image_name)) {
                        rename($org_target_path . $image_name, $default_target_path . $fileName);
                    }
                    move_uploaded_file($_FILES['android_driver_colorcode_file']['tmp_name'], $org_target_path . $image_name);
                    chmod($org_target_path . $image_name, 0777);
                    $android_db_driver_colorcode[$dynamic_lang] = 2;
                    $data = array('android_driver_colorcode_settings', $android_db_driver_colorcode);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                } else {
                    Message::error(__('fail_upload_error_info'));
                    //$this->request->redirect('/package/preferences');
                }
            } else {
                $errors = $validator->errors('errors');
                Message::error(__('file_upload_warning_info'));
            }
        } elseif (isset($postvalue['android_driver_colorcode_radio']) && $postvalue['android_driver_colorcode_radio'] == 1) {
            # default colorcode - driver
            $image_name = 'driverAppColors.xml';
            $org_target_path = DOCROOT . SAMPLE_ANDROID_COLORCODE_FILES;
            $default_target_path = DOCROOT . ANDROID_DEFAULT_CUSTOMIZE_FILES;
            if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . 'driverAppColors_default.xml')) {
                rename($org_target_path . $image_name, $default_target_path . 'driverAppColors_customize.xml');
                rename($default_target_path . 'driverAppColors_default.xml', $org_target_path . $image_name);
            }
            $android_db_driver_colorcode[$dynamic_lang] = 1;
            $data = array('android_driver_colorcode_settings', $android_db_driver_colorcode);
            $status = $package->update_language_colorcode($data);
            Message::success(__('file_default_upload_succ_info'));
            $this->request->redirect('/package/preferences');
        } elseif (isset($postvalue['android_passenger_colorcode_radio']) && $postvalue['android_passenger_colorcode_radio'] == 2 && Validation::factory(array_merge($_FILES,$postvalue))) {
            # customized colorcode - passenger
            $validator = $package->validate_android_driver_language(array_merge($_FILES,$postvalue));
            if ($validator->check()) {
                if (!empty($_FILES['android_passenger_colorcode_file']['name'])) {
                    $image_type = explode('.', $_FILES['android_passenger_colorcode_file']['name']);
                    $image_type = end($image_type);
                    $image_name = 'passengerAppColors.' . $image_type;
                    $fileName = 'passengerAppColors_default.xml';
                    $org_target_path = DOCROOT . SAMPLE_ANDROID_COLORCODE_FILES;
                    $default_target_path = DOCROOT . ANDROID_DEFAULT_CUSTOMIZE_FILES;
                    if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . $fileName)) {
                        rename($org_target_path . $image_name, $default_target_path . 'passengerAppColors_customize.xml');
                    } elseif (file_exists($org_target_path . $image_name)) {
                        rename($org_target_path . $image_name, $default_target_path . $fileName);
                    }
                    move_uploaded_file($_FILES['android_passenger_colorcode_file']['tmp_name'], $org_target_path . $image_name);
                    chmod($org_target_path . $image_name, 0777);
                    $android_db_passenger_colorcode[$dynamic_lang] = 2;
                    $data = array('android_passenger_colorcode_settings', $android_db_passenger_colorcode);
                    $status = $package->update_language_colorcode($data);
                    Message::success(__('file_upload_succ_info'));
                    $this->request->redirect('/package/preferences');
                } else {
                    Message::error(__('fail_upload_error_info'));
                    //$this->request->redirect('/package/preferences');
                }
            } else {
                $errors = $validator->errors('errors');
                Message::error(__('file_upload_warning_info'));
            }
        } elseif (isset($postvalue['android_passenger_colorcode_radio']) && $postvalue['android_passenger_colorcode_radio'] == 1) {
            # default colorcode - passenger
            $image_name = 'passengerAppColors.xml';
            $org_target_path = DOCROOT . SAMPLE_ANDROID_COLORCODE_FILES;
            $default_target_path = DOCROOT . ANDROID_DEFAULT_CUSTOMIZE_FILES;
            if (file_exists($org_target_path . $image_name) && file_exists($default_target_path . 'passengerAppColors_default.xml')) {
                rename($org_target_path . $image_name, $default_target_path . 'passengerAppColors_customize.xml');
                rename($default_target_path . 'passengerAppColors_default.xml', $org_target_path . $image_name);
            }
            $android_db_passenger_colorcode[$dynamic_lang] = 1;
            $data = array('android_passenger_colorcode_settings', $android_db_passenger_colorcode);
            $status = $package->update_language_colorcode($data);
            Message::success(__('file_default_upload_succ_info'));
            $this->request->redirect('/package/preferences');
        } else {
            Message::error(__('fail_upload_error_info'));
            //$this->request->redirect('/package/preferences');
        }
        $this->template->meta_description = CLOUD_SITENAME . " | Preferences ";
        $this->template->meta_keywords = CLOUD_SITENAME . " | Preferences ";
        $this->template->title = CLOUD_SITENAME . " | " . __('Preferences');
        $this->template->page_title = __('Preferences');
        $this->template->content = View::factory("admin/package_plan/preferences")->bind('action', $action)->bind('postvalue', $postvalue)->bind('errors', $errors);
    }

    /**
     * Setup with application payment gateway settings
     */
    public function action_payments() {
        $package = Model::factory('package');
        $postvalue = $errors = array();
        $postvalue = $this->request->post();
        $payment_gateway_id= isset($postvalue['payment_gateway_type'])?$postvalue['payment_gateway_type']:0;
        
        $payment_settings = $package->get_payment_details($payment_gateway_id);
        $paypal_payment_settings=$package->get_paypal_payment_details();
        //echo "<pre>"; print_r($payment_settings); exit;
        $this->template->meta_description = CLOUD_SITENAME . " | Payments ";
        $this->template->meta_keywords = CLOUD_SITENAME . " | Payments ";
        $this->template->title = CLOUD_SITENAME . " | " . __('Payments');
        $this->template->page_title = __('Payments');
        
        if (class_exists('Paymentgateway')) {                
                $payment_gateway_list = Paymentgateway::payment_auth_credentials_view();
              
            } else {
                trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
            }
            
            $form_top_fields= isset($payment_gateway_list[1])?$payment_gateway_list[1]:[];
            $form_fields= isset($payment_gateway_list[2])?$payment_gateway_list[2]:[];
            $form_live_fields= isset($payment_gateway_list[3])?$payment_gateway_list[3]:[];
            $form_bottom_fields= isset($payment_gateway_list[4])?$payment_gateway_list[4]:[];

        $this->template->content = View::factory("admin/package_plan/payments")
                ->bind('payment_settings', $payment_settings)
                ->bind('paypal_payment_settings',$paypal_payment_settings)
                ->bind('form_top_fields', $form_top_fields)
                ->bind('form_fields', $form_fields)
                ->bind('form_live_fields', $form_live_fields)
                ->bind('form_bottom_fields', $form_bottom_fields)
                ->bind('payment_gateway_list',$payment_gateway_list[0])
                ->bind('postvalue', $postvalue)
                ->bind('errors', $errors);
    }

    public function action_direct_payment_gateway() {
        $package = Model::factory('package');
        $payment_settings = $package->get_payment_details();
        $signup_submit = arr::get($_REQUEST, 'submit_editpayment');
        $errors = $postvalue = array();   
        
        
        
          if (class_exists('Paymentgateway')) {                
                $payment_gateway_list = Paymentgateway::payment_auth_credentials_view();
              
            } else {
                trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
            }
            
            $form_top_fields= [];
            $form_fields= [];
            $form_live_fields= [];
            $form_bottom_fields= [];
        if ($signup_submit && Validation::factory($_POST)) {
            $postvalue = Arr::map('trim', $this->request->post());
            $validator = $package->validate_editcompanypayment(arr::extract($postvalue, array('payment_gateway_type', 'payment_gateway_provider_id', 'description', 'currency_code', 'currency_symbol', 'payment_method', 'payment_gateway_username', 'payment_gateway_password', 'payment_gateway_signature', 'live_payment_gateway_username', 'live_payment_gateway_password', 'live_payment_gateway_signature')));
            if ($validator->check()) {
                $status = $package->editcompanypayment($postvalue);
                if ($status == 1) {
                    Message::success(__('sucessfull_updated_payment_gateway'));
                } elseif ($status == 2) {
                    Message::error(__('payment_status_error_info'));
                } else {
                    Message::error(__('not_updated'));
                }
                $this->request->redirect("package/payments");
            } else {
                $errors = $validator->errors('errors');
            }
        }
        //send data to view file 
        $this->template->meta_description = SITENAME . " | Payments ";
        $this->template->meta_keywords = SITENAME . " | Payments ";
        $this->template->title = SITENAME . " | " . __('Payments');
        $this->template->page_title = __('Payments');
        $this->template->content = View::factory("admin/package_plan/payments")
                ->bind('payment_settings', $payment_settings)
                ->bind('postvalue', $postvalue)
                 ->bind('form_top_fields', $form_top_fields)
                ->bind('form_fields', $form_fields)
                ->bind('form_live_fields', $form_live_fields)
                ->bind('form_bottom_fields', $form_bottom_fields)
                ->bind('payment_gateway_list',$payment_gateway_list[0])
                ->bind('errors', $errors);
    }

    public function action_alternative_gateways_details() {
        $package = Model::factory('package');        
        $payment_gateway_type= isset($_POST['payment_gateway_id'])?$_POST['payment_gateway_id']:'';
        
        $paypal_payment_settings=$package->get_paypal_payment_details();
        if($payment_gateway_type!=""){
        $payment_settings = $package->get_payment_details($payment_gateway_type);
        
        
          if (class_exists('Paymentgateway')) {                
                $payment_gateway_list = Paymentgateway::payment_auth_credentials_view();
              
            } else {
                trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
            }
            
            $form_top_fields= isset($payment_gateway_list[1])?$payment_gateway_list[1]:[];
            $form_fields= isset($payment_gateway_list[2])?$payment_gateway_list[2]:[];
            $form_live_fields= isset($payment_gateway_list[3])?$payment_gateway_list[3]:[];
            $form_bottom_fields= isset($payment_gateway_list[4])?$payment_gateway_list[4]:[];
        }
        
        $signup_submit = arr::get($_REQUEST, 'submit_edit_alternate_payment');
        
        $errors = $postvalue = array();
        if ($signup_submit && Validation::factory($_POST)) {
            $postvalue = Arr::map('trim', $this->request->post());            
            $field_list_array= Paymentgateway::get_payment_gateway_required_fields();           
            
            if(!empty($field_list_array)){
            //$validator = $package->validate_editcompanypayment(arr::extract($postvalue, array('payment_gateway_type', 'payment_gateway_provider_id', 'description', 'currency_code', 'currency_symbol', 'payment_method', 'payment_gateway_username', 'payment_gateway_password', 'payment_gateway_signature', 'live_payment_gateway_username', 'live_payment_gateway_password', 'live_payment_gateway_signature')));
                $validator = $package->validate_editcompanypayment(arr::extract($postvalue, $field_list_array));
            }else{
                throw new Exception('check payment gateway fields xml');
            }
            
            //$validator=$package->validate_editcompanypayment($postvalue);
            if ($validator->check()) {
                
                $status = $package->editcompanypayment($postvalue);
                if ($status == 1) {
                    Message::success(__('sucessfull_updated_payment_gateway'));
                } elseif ($status == 2) {
                    Message::error(__('payment_status_error_info'));
                } else {
                    Message::error(__('not_updated'));
                }
                $this->request->redirect("package/payments#top");
            } else {
                if (class_exists('Paymentgateway')) {                
                $payment_gateway_list = Paymentgateway::payment_auth_credentials_view();
              
            } else {
                trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
            }
            
            $form_top_fields= isset($payment_gateway_list[1])?$payment_gateway_list[1]:[];
            $form_fields= isset($payment_gateway_list[2])?$payment_gateway_list[2]:[];
            $form_live_fields= isset($payment_gateway_list[3])?$payment_gateway_list[3]:[];
            $form_bottom_fields= isset($payment_gateway_list[4])?$payment_gateway_list[4]:[];
                $errors = $validator->errors('errors');                      
            }
        }else if($payment_gateway_type==''){
            $this->request->redirect("package/payments");
        }
        //send data to view file 
        $this->template->meta_description = SITENAME . " | Payments ";
        $this->template->meta_keywords = SITENAME . " | Payments ";
        $this->template->title = SITENAME . " | " . __('Payments');
        $this->template->page_title = __('Payments');
        $this->template->content = View::factory("admin/package_plan/payments")
                ->bind('payment_settings', $payment_settings)
                ->bind('paypal_payment_settings',$paypal_payment_settings)
                ->bind('postvalue', $postvalue)
                ->bind('form_top_fields', $form_top_fields)
                ->bind('form_fields', $form_fields)
                ->bind('form_live_fields', $form_live_fields)
                ->bind('form_bottom_fields', $form_bottom_fields)
                ->bind('payment_gateway_list',$payment_gateway_list[0])
                ->bind('errors', $errors);
    }


    
    #  Setup with SMS settings 
    public function action_cloud_sms_settings() {

        $usertype = $_SESSION['user_type'];

        $package = Model::factory('package');
        $errors = array();
        $smssettings_submit = arr::get($_REQUEST, 'btn_sms_activate');

        $post_values = array();

        $sms_id = '';
        $company_id = 1;
        $smssettings = $package->sms_settings();
        if (empty($smssettings)) {

            $smssettings[0]['sms_account_id'] = '';
            $smssettings[0]['sms_auth_token'] = '';
            $smssettings[0]['sms_from_number'] = '';
            $smssettings[0]['sms_id'] = '';
            $sms_id = $smssettings[0]['sms_id'];
        } else {
            $sms_id = 1;
        }

        if ($smssettings_submit && Validation::factory($_POST)) {
            $post_values = $_POST;

            $validator = $package->validate_update_smssettings(arr::extract($_POST, array('sms_account_id', 'sms_auth_token', 'sms_from_number')));
            //'site_city';
            if ($validator->check()) {
                $status = $package->update_sms_settings($_POST, $company_id, $sms_id);

                if ($status == 1) {
                    Message::success(__('sucessful_settings_update'));
                } else {
                    Message::error(__('not_updated'));
                }

                $this->request->redirect("package/cloud_sms_settings");
            } else {
                $errors = $validator->errors('errors');
            }
        }
        //$id = $this->request->param('id');
        // $id=1;

        $this->selected_page_title = __("sms_settings");
        $view = View::factory(ADMINVIEW . 'package_plan/sms_settings')
                ->bind('validator', $validator)
                ->bind('errors', $errors)
                ->bind('postvalue', $post_values)
                ->bind('smssettings', $smssettings);

        $this->template->title = CLOUD_SITENAME . " | " . __('sms_settings');
        $this->template->page_title = __('sms_settings');
        $this->template->content = $view;
    }

    /**
     * Setup with google map key settings
     */
    public function action_google_settings() {

        $package = Model::factory('package');
        $errors = array();
        $google_settings_submit = arr::get($_REQUEST, 'btn_google');

        $post_values = array();
        $google_settings = $package->google_settings();

        $google_id = 1;

        if ($google_settings_submit && Validation::factory($_POST)) {
            $post_values = $_POST;

            $validator = $package->validate_update_google_settings(arr::extract($_POST, array('ios_google_map_key', 'ios_google_geo_key', 'web_google_map_key', 'google_timezone_api_key', 'web_google_geo_key', 'android_google_api_key','web_foursquare_api_key','android_foursquare_api_key','ios_foursquare_api_key')));

            if ($validator->check()) {
                $status = $package->update_google_settings($_POST, $google_id);

                if ($status == 1) {
                    Message::success(__('sucessful_settings_update'));
                } else {
                    Message::error(__('not_updated'));
                }

                $this->request->redirect("package/google_settings");
            } else {
                $errors = $validator->errors('errors');
            }
        }

        $this->template->title = CLOUD_SITENAME . ' | ' . __('google_settings');
        $this->template->page_title = __('google_settings');
        $this->meta_description = "";
        $this->template->content = View::factory("admin/package_plan/google_settings")
                ->bind('errors', $errors)
                ->bind('google_settings', $google_settings)
                ->bind('postvalue', $post_values);
    }

    /**
     * Get the state information data   
     *    
     */
    public function action_getlist_state() {
        $package = Model::factory('package');
        $output = '';
        $country_id = arr::get($_REQUEST, 'country_id');
        $state_id = arr::get($_REQUEST, 'state_id');

        $getmodel_details = $package->getstate_details($country_id);

        if (isset($country_id)) {

            $count = count($getmodel_details);
            if ($count > 0) {

                /* $output .='<select name="state" id="state" onchange=change_city_drop("","","") class="form_control required" title="'.__('select_the_state').'">
                  <option value="">--Select--</option>'; */

                $output .= '<label>State</label><select class="form_control" name="state" id="state" onchange="change_city_drop();"><option>Select state</option>';

                foreach ($getmodel_details as $modellist) {
                    $output .= '<option value="' . $modellist["state_id"] . '"';
                    if ($state_id == $modellist["state_id"]) {
                        $output .= 'selected=selected';
                    }
                    $output .= '>' . $modellist["state_name"] . '</option>';
                }

                $output .= '</select>';
            } else {
                $output .= '<label>State</label><select class="form_control" name="state" id="state" onchange="change_city_drop();"><option>Select state</option>';
            }
        }
        echo $output;
        exit;
    }

    /**
     *  Get the city information data 
     * 
     */
    public function action_getcitylist() {
        $package = Model::factory('package');
        $output = '';
        $country_id = arr::get($_REQUEST, 'country_id');
        $state_id = arr::get($_REQUEST, 'state_id');
        $city_id = arr::get($_REQUEST, 'city_id');


        $getmodel_details = $package->getcity_details($country_id, $state_id);

        if (isset($country_id)) {

            $count = count($getmodel_details);
            if ($count > 0) {

                $output .= '<label>City</label><select class="form_control" name="city" id="city" onchange="change_info();"><option>Select city</option>';


                foreach ($getmodel_details as $modellist) {
                    $output .= '<option value="' . $modellist["city_id"] . '"';
                    if ($city_id == $modellist["city_id"]) {
                        $output .= 'selected=selected';
                    }
                    $output .= '>' . $modellist["city_name"] . '</option>';
                }
                $output .= '</select>';
            } else {
                /* $output .='<select name="city" id="city" title=" '.__('select_the_city').'" class="required">
                  <option value="">--Select--</option></select>'; */
                $output .= '<label>City</label><select class="form_control" name="city" id="city" onchange="change_info();"><option>Select city</option>';
            }
        }
        echo $output;
        exit;
    }

    
    
    // Generate PDF *******************/
    public function action_genpdf() {
        $post = Request::current()->post();

        $package = Model::factory('package');
        $id = $post['pdf-id'];

        $paid_package_info = $package->get_package_invoice_info($id);

        $city = '';
        $state = '';
        $country = '';
        $address = '';
        $get_package_business = $package->get_site_info();
        if (isset($get_package_business[0]['domain_name'])) {
            $business_name = $get_package_business[0]['domain_name'];
        } else {
            $business_name = '';
        }
        if (isset($paid_package_info[0]['city'])) {
            $city = $paid_package_info[0]['city'];
        }
        if (isset($paid_package_info[0]['state'])) {
            $state = $paid_package_info[0]['state'];
        }
        if (isset($paid_package_info[0]['country'])) {
            $country = $paid_package_info[0]['country'];
        }

        if (isset($paid_package_info[0]['address'])) {
            $address = $paid_package_info[0]['address'];
        }
        $payment_terms = '-';
        if (isset($paid_package_info[0]['payment_terms'])) {
            if ($paid_package_info[0]['payment_terms'] == 1) {
                $payment_terms = '30 days';
            } else if ($paid_package_info[0]['payment_terms'] == 2) {
                $payment_terms = '1 year';
            } else if ($paid_package_info[0]['payment_terms'] == 3) {
                $payment_terms = '2 years';
            } else if ($paid_package_info[0]['payment_terms'] == 4) {
                $payment_terms = '3 years';
            }
        }

        $Middle_html = "";
        $Endhtml = "";
        $Tophtml = '<style>
	h1 {
		color: navy;
		font-family: times;
		font-size: 24pt;
	}
	p.first {
		color: #003300;
		font-family: helvetica;
		font-size: 12pt;
	}
	p.first span {
		color: #006600;
		font-style: italic;
	}
	p#second {
		color: rgb(00,63,127);
		font-family: times;
		font-size: 12pt;
		text-align: justify;
	}
	
	p#second > span {
		
	}
	table.first {
		color: #003300;
		font-family: helvetica;
		font-size: 8pt;
		background-color:#FFF; 
		border:10px solid #236B8D;
		
		
	}
	td {
		font-weight:bold;
		font:bold 12pt arial; color:#000000;
		
	}
	.invoice_head{text-align: right;color:#000000;}
	.head_border{border-bottom:1px solid #2c2c2c;}
	.totalstyle{font-weight:bold; font:bold 12pt arial; color:#ffffff; background-color:#2c2c2c; text-align:left; width:auto}
	.taxstyle{font-weight:bold; font:bold 12pt arial; color:#000000;  text-align:left;}
        .office_addr,.invoice_sender{width:100%;float:left;}
        .office_addr h1{font:bold 16px arial;color:#000;padding-bottom:15px;width:100%;margin:0;}
        .office_addr p{font:normal 12px arial;color:#000;width:100%;margin:0;line-height:20px;}
        .invoice_sender h2{font:30px arial;color:#5292BC;width:100%;padding:10px 0 20px;margin:0;}
        .invoice_sender h3{font:bold 14px arial;color:#000;width:100%;line-height:20px;margin:0;}
        .invoice_sender p{font:12px arial;color:#000;width:100%;margin:0;}
        .invoice_det p{width:100%;font:13px arial;color:#000;margin:0;float:left;}
        .invoice_det p label{width:85px;font:bold 13px arial;color:#000;float:left;text-align:right;padding-right:10px;}
        .border{width:100%;height:1px;background:#8BB5D2;margin:20px 0 40px;}
        .pur_det thead tr th{font:14px arial;color:#5895BE;background:#DCE9F1;padding:5px 10px;}
        .pur_det tr td{font:14px arial;color:#000;padding:5px 10px;}
        .pur_det tr td p{line-height:20px;}
        .border_dot{width:100%;border-top:1px dashed #8BB5D2;margin:20px 0 10px;}
        .bal_due{font:16px arial;color:#000;margin:0;}
        .tot_amt{font:bold 18px arial;color:#000;margin: 0;}

	</style>        
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background:#fff;padding:50pt;">
            <tr>
                <td width="50%">
                        <h1 style="font-weight:bold;font-size:16pt;font-family:helvetica,sans serif;color:#000;padding-bottom:15pt;width:100%;margin:0;">NDOT Technologies Pvt Ltd</h1>
                        <p style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;width:100%;margin:0;">+91-422-2970042</p>
                        <p style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;width:100%;margin:0;">accounts@ndot.in</p>
                        <p style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;width:100%;margin:0;">http://www.ndottech.com</p>
                </td>
                <td width="50%"></td>
            </tr>
            <tr>
                <td>
                        <h2 style="font-family:helvetica,sans serif;font-size:20pt;color:#5292BC;width:100%;padding:10pt 0 20pt;margin:0;">INVOICE</h2>
                        <h3 style="font-family:helvetica,sans serif;font-size:13pt;color:#000;width:100%;margin:0;">INVOICE TO</h3>
                        <p style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;width:100%;margin:0;">' . $business_name . '</p>
                        <p style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;width:100%;margin:0;">' . $paid_package_info[0]['name'] . '</p>
                        <p style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;width:100%;margin:0;">' . $address . '</p>
                        <p style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;width:100%;margin:0;">' . $city . '</p>
                        <p style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;width:100%;margin:0;">' . $state . '</p>
                        <p style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;width:100%;margin:0;">' . $country . '</p>
                </td>
                <td>
                    <table width="100%" cellpadding="5" cellspacing="0">
                    <tr><td><label style="width:85pt;font-family:helvetica,sans serif;font-weight:bold;font-size:11pt;color:#000;float:left;text-align:right;padding-right:10pt;line-height:1pt;">INVOICE NO.</label></td><td><p style="width:100%;font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;margin:0;float:left;line-height:1pt;">' . $paid_package_info[0]['purchase_inv_id'] . '</p></td></tr>
                    <tr><td><label style="width:85pt;font-family:helvetica,sans serif;font-weight:bold;font-size:11pt;color:#000;float:left;text-align:right;padding-right:10pt;line-height:1pt;">DATE</label></td><td><p style="width:100%;font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;margin:0;float:left;line-height:1pt;">' . Commonfunction::convertphpdate('d-m-Y', $paid_package_info[0]['createddate']) . '</p></td></tr>
                    <tr><td><label style="width:85pt;font-family:helvetica,sans serif;font-weight:bold;font-size:11pt;color:#000;float:left;text-align:right;padding-right:10pt;line-height:1pt;">TERMS</label></td><td><p style="width:100%;font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;margin:0;float:left;line-height:1pt;">Net ' . $payment_terms . '</p></td></tr>
                    </table>
                   
                </td>
            </tr>
            <tr><td colspan="2"><div style="width:100%;border-top:1pt solid #8BB5D2;"></div></td></tr>
            <tr><td colspan="2"><table class="pur_det" border="0" cellpadding="5" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th bgcolor="#DCE9F1" align="center" style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#A8C8DD;">NO</th>
                <th bgcolor="#DCE9F1" align="left" colspan="2" style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#A8C8DD;">ACTIVITY</th>
                <th bgcolor="#DCE9F1" align="right" style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#A8C8DD;">QTY</th>
                <th bgcolor="#DCE9F1" align="right" style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#A8C8DD;">RATE</th>
                <th bgcolor="#DCE9F1" align="right" style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#A8C8DD;">AMOUNT</th>
            </tr> 
            </thead>
            <tr><td colspan="6" style="height:3pt;"></td></tr>
            <tr>
                <td align="center" style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;">1</td>
                <td align="left" colspan="2" style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;">Taxi - Product:Taxi Mobility Custom Branding on Mobile + Before Uploading default application in Client server
                </td>
                <td align="right" style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;">1</td>
                <td align="right" style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;">' . number_format($paid_package_info[0]['amount'], 2) . '</td>
                <td align="right" style="font-family:helvetica,sans serif;font-weight:normal;font-size:11pt;color:#000;">' . number_format($paid_package_info[0]['amount'], 2) . '</td>
            </tr>
            <tr><td colspan="6"><div style="height:5pt;width:100%:"></div></td></tr>
            <tr><td colspan="6"><div style="width:100%;border-top:1pt dashed #8BB5D2;"></div></td></tr>
            <tr>
                <td align="right" colspan="3"><p class="bal_due" style="font-family:helvetica,sans serif;font-weight:normal;font-size:13pt;color:#000;">Total Amount</p></td>
                <td colspan="3" align="right"><p class="tot_amt" style="font-family:helvetica,sans serif;font-size:13pt;color:#000;">USD ' . number_format($paid_package_info[0]['amount'], 2) . '</p></td>
            </tr>
            </table>
            </td></tr> </table>';

        $html = $Tophtml . $Middle_html . $Endhtml;
        ob_clean();
        $filename = __('INVOICE') . '-' . date('m-d-y-s');

        $generate_pdf = $package->generate_pdf($html, $filename);
        exit;
    }
    
    private function country_info(){
        return array(
            "1" => "India",
            "2" => "German",
            "3" => "China",
            "4" => "America",
            "5" => "Russia",
            "6" => "Mexico",
            "7" => "Australia",
            "8" => "Schweiz",
            "9" => "United States",
            "10" => "Argentina",
            "11" => "Peru",
            "12" => "South Africa",
            "13" => "Hungary",
            "14" => "Malaysia",
            "15" => "KENYA",
            "16" => "New Zealand",
            "17" => "Russian Federation",
            "18" => "Brasil",
            "19" => "Portugal",
            "20" => "Canada",
            "21" => "United Kingdom",
            "22" => "Iran",
            "23" => "Ethiopia",
            "24" => "testing",
            "25" => "Kazakhstan",
            "26" => "Afghanistan",
            "27" => "Albania",
            "28" => "Algeria",
            "29" => "American Samoa",
            "30" => "Andorra",
            "31" => "Anguilla",
            "32" => "Antarctica",
            "33" => "Antigua and Barbuda",
            "34" => "Armenia",
            "35" => "Aruba",
            "36" => "Austria",
            "37" => "Azerbaijan",
            "38" => "Bahamas",
            "39" => "Bahrain",
            "40" => "Barbados",
            "41" => "Belarus",
            "42" => "Belgium",
            "43" => "Belize",
            "44" => "Benin",
            "45" => "Bermuda",
            "46" => "Bhutan",
            "47" => "Bolivia",
            "48" => "Bosnia and Herzegovina",
            "49" => "Botswana",
            "50" => "Bouvet Island",
            "51" => "British Indian Ocean Territory",
            "52" => "British Virgin Islands",
            "53" => "Brunei",
            "54" => "Bulgaria",
            "55" => "Burkina Faso",
            "56" => "Burundi",
            "57" => "Cambodia",
            "58" => "Cameroon",
            "59" => "Cape Verde",
            "60" => "Cayman Islands",
            "61" => "Central African Republic",
            "62" => "Chad",
            "63" => "Chile",
            "64" => "China",
            "65" => "Christmas Island",
            "66" => "Cocos Islands",
            "67" => "Colombia",
            "68" => "Comoros",
            "69" => "Cook Islands",
            "70" => "Costa Rica",
            "71" => "Croatia",
            "72" => "Cuba",
            "73" => "Cyprus",
            "74" => "Czech Republic",
            "75" => "Democratic Republic of the Congo",
            "76" => "Djibouti",
            "77" => "Dominica",
            "78" => "Dominican Republic",
            "79" => "East Timor",
            "80" => "Ecuador",
            "81" => "Egypt",
            "82" => "El Salvador",
            "83" => "Equatorial Guinea",
            "84" => "Eritrea",
            "85" => "Estonia",
            "86" => "Falkland Islands",
            "87" => "Faroe Islands",
            "88" => "Fiji",
            "89" => "Finland",
            "90" => "France",
            "91" => "French Guiana",
            "92" => "French Polynesia",
            "93" => "French Southern Territories",
            "94" => "Gabon",
            "95" => "Gambia",
            "96" => "Georgia",
            "97" => "Germany",
            "98" => "Ghana",
            "99" => "Gibraltar",
            "100" => "Greece",
            "101" => "Greenland",
            "102" => "Grenada",
            "103" => "Guadeloupe",
            "104" => "Guam",
            "105" => "Guatemala",
            "106" => "Guinea",
            "107" => "Guinea-Bissau",
            "108" => "Guyana",
            "109" => "Haiti",
            "110" => "Heard Island and McDonald Islands",
            "111" => "Honduras",
            "112" => "Hong Kong",
            "113" => "Iceland",
            "114" => "Iraq",
            "115" => "Ireland",
            "116" => "Israel",
            "117" => "Italy",
            "118" => "Ivory Coast",
            "119" => "Jamaica",
            "120" => "Japan",
            "121" => "Jordan",
            "122" => "Kiribati",
            "123" => "Kyrgyzstan",
            "124" => "Laos",
            "125" => "Latvia",
            "126" => "Lesotho",
            "127" => "Liberia",
            "128" => "Libya",
            "129" => "Liechtenstein",
            "130" => "Lithuania",
            "131" => "Luxembourg",
            "132" => "Macao",
            "133" => "Macedonia",
            "134" => "Madagascar",
            "135" => "Malawi",
            "136" => "Maldives",
            "137" => "Mali",
            "138" => "Malta",
            "139" => "Marshall Islands",
            "140" => "Martinique",
            "141" => "Mauritania",
            "142" => "Mauritius",
            "143" => "Mayotte",
            "144" => "Mexico",
            "145" => "Micronesia",
            "146" => "Moldova",
            "147" => "Monaco",
            "148" => "Mongolia",
            "149" => "Montserrat",
            "150" => "Morocco",
            "151" => "Mozambique",
            "152" => "Namibia",
            "153" => "Nauru",
            "154" => "Nepal",
            "155" => "Netherlands",
            "156" => "Netherlands Antilles",
            "157" => "New Caledonia",
            "158" => "Nicaragua",
            "159" => "Niger",
            "160" => "Nigeria",
            "161" => "Niue",
            "162" => "Norfolk Island",
            "163" => "North Korea",
            "164" => "Northern Mariana Islands",
            "165" => "Norway",
            "166" => "Oman",
            "167" => "Pakistan",
            "168" => "Palau",
            "169" => "Palestinian Territory",
            "170" => "Panama",
            "171" => "Papua New Guinea",
            "172" => "Paraguay",
            "173" => "Philippines",
            "174" => "Pitcairn",
            "175" => "Poland",
            "176" => "Puerto Rico",
            "177" => "Qatar",
            "178" => "Republic of the Congo",
            "179" => "Reunion",
            "180" => "Romania",
            "181" => "Rwanda",
            "182" => "Saint Helena",
            "183" => "Saint Kitts and Nevis",
            "184" => "Saint Lucia",
            "185" => "Saint Pierre and Miquelon",
            "186" => "Saint Vincent and the Grenadines",
            "187" => "Samoa",
            "188" => "San Marino",
            "189" => "Sao Tome and Principe",
            "190" => "Senegal",
            "191" => "Serbia and Montenegro",
            "192" => "Seychelles",
            "193" => "Sierra Leone",
            "194" => "Singapore",
            "195" => "Slovakia",
            "196" => "Slovenia",
            "197" => "Solomon Islands",
            "198" => "Somalia",
            "199" => "South Georgia and the South Sandwich Islands",
            "200" => "South Korea",
            "201" => "Spain",
            "202" => "Sri Lanka",
            "203" => "Sudan",
            "204" => "Suriname",
            "205" => "Svalbard and Jan Mayen",
            "206" => "Swaziland",
            "207" => "Sweden",
            "208" => "Switzerland",
            "209" => "Syria",
            "210" => "Taiwan",
            "211" => "Tajikistan",
            "212" => "Tanzania",
            "213" => "Togo",
            "214" => "Tokelau",
            "215" => "Tonga",
            "216" => "Trinidad and Tobago",
            "217" => "Tunisia",
            "218" => "Turkey",
            "219" => "Turkmenistan",
            "220" => "Turks and Caicos Islands",
            "221" => "Tuvalu",
            "222" => "U.S. Virgin Islands",
            "223" => "Uganda",
            "224" => "Ukraine",
            "225" => "United States Minor Outlying Islands",
            "226" => "Uruguay",
            "227" => "Uzbekistan",
            "228" => "Vanuatu",
            "229" => "Vatican",
            "230" => "Venezuela",
            "231" => "Vietnam",
            "232" => "Wallis and Futuna",
            "233" => "Western Sahara",
            "234" => "Yemen",
            "235" => "Zambia",
            "236" => "Zimbabwe",
            "237" => "Mourisious",
            "238" => "Dubai",
            "239" => "testcountry",
            "240" => "testing service",
            "241" => "testcountr",
            "242" => "test",
            "243" => "United Kingdoms",
            "244" => "Saudi Arabia",
            "0" => "Others",
        );
    }
    
    public function action_resendmail(){
        $email=$_POST['email'];
        if($email==''){            
            return 0;
        }
         if (CRM_UPDATE_ENABLE == 1 && class_exists('Thirdpartyapi')) {
                        if (method_exists('Thirdpartyapi', 'crm_resend_email')) {
                            $thirdpartyapi = Thirdpartyapi::instance();                            
                            echo $thirdpartyapi->crm_resend_email($email);
                            exit;
                            
                        }
                    }
        exit;
    }
    
   
    
     public function xml_entity_decode($s) {
    // here an illustration (by user-defined function) 
    // about how the hypothetical PHP-build-in-function MUST work
    static $XENTITIES = array('&amp;','&gt;','&lt;');
    static $XSAFENTITIES = array('#_x_amp#;','#_x_gt#;','#_x_lt#;');
    $s = str_replace($XENTITIES,$XSAFENTITIES,$s); 

    //$s = html_entity_decode($s, ENT_NOQUOTES, 'UTF-8'); // any php version
    $s = html_entity_decode($s, ENT_HTML5|ENT_NOQUOTES, 'UTF-8'); // PHP 5.3+

    $s = str_replace($XSAFENTITIES,$XENTITIES,$s);
    return $s;
  } 
}
  
// End Welcome
?>
