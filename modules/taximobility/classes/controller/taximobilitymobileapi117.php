<?php defined('SYSPATH') or die('No direct script access.');

/****************************************************************

* Contains API details - Version 6.0.0

* @Package: Taximobility

* @Author:  NDOT Team

* @URL : http://www.ndot.in

****************************************************************/
Class Controller_TaximobilityMobileapi117 extends Controller_Mobile104
{
	public function __construct()
	{	
		$this->session = Session::instance();
		try {
			require Kohana::find_file('classes','mobile_common_config');
                        require Kohana::find_file('classes/controller', 'ndotcrypt');
                     
			$this->commonmodel=Model::factory('commonmodel');
			DEFINE("MOBILEAPI_107","mobileapi117");
			DEFINE("MOBILEAPI_107_EXTENDED","mobileapi117extended");
			DEFINE("FIND","find114");
			if((COMPANY_CID !='0'))
			{
				$this->app_name = COMPANY_SITENAME;
				$this->siteemail= COMPANY_CONTACT_EMAIL;
				$this->domain_name = SUBDOMAIN;
			}
			else
			{
				$this->siteemail= SITE_EMAIL_CONTACT;				
				$this->app_name = SITE_NAME;
				$this->app_name = preg_replace("/#?[a-z0-9]+;/i","",$this->app_name); // Remove &amp; tag from site name
				$this->domain_name='site';
			}
			
			$this->lang = I18n::lang(LANG);
			$this->app_description=APP_DESCRIPTION;	
			$this->emailtemplate=Model::factory('emailtemplate');
			$this->notification_time = ADMIN_NOTIFICATION_TIME;
			$this->customer_google_api = CUSTOMER_ANDROID_KEY; // For GCM
			$this->continuous_request_time = CONTINOUS_REQUEST_TIME;
			$this->site_currency = CURRENCY;
			//$this->currentdate=Commonfunction::getCurrentTimeStamp();
			# created date
			$this->currentdate = Commonfunction::createdateby_user_timezone();
		}
		catch (Database_Exception $e)
		{
			// Insert failed. Rolling back changes...
			$message = array("message" => __('Database Connection Failed'),"status" => 2);						
                        echo json_encode($message);
			exit;
		}
		
	}		
			
	function action_encrypt_decrypt($action, $string) {							
		$output = false;			
		$key = 'Taxi Application Project';
                
		// initialization vector 		
		$iv = md5(md5($key));		
		if( $action == 'encrypt' ) {		
		  $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, $iv);		
		  $output = base64_encode($string);		
		}		
		else if( $action == 'decrypt' ){		
		  $output = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, $iv);		
		  $output = base64_decode($string);		
		}
		return $output;		
    }
    
	public function action_index()
	{		
		
		$api = Model::factory(MOBILEAPI_107);	
		/// We are getting the date from mobile as urlencoded format in POST method
		$mobile_encodeddata = file_get_contents('php://input');
                                             
                $mobile_decryptdata='';
                $additional_param=[];
                $api_key_encrypt='';
                $company_api_key='';
                $method='';  
                
                
                if(isset($_REQUEST["type"])){
                    $method = $_REQUEST["type"];                        
		}else{
                    $message = array("message" => "Invalid Request ","status" => 2);
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				exit;	
                } 
                
                require Kohana::find_file('classes/controller', 'ndotmobilekey_validate');
 
			// Here we are decode the url encoded values and conver the values in to array
		$mobiledata =  (array)json_decode($mobile_decryptdata,true);		
			$errors = array();		
			
			
			// Check mobile input param validation
			if(empty($mobiledata) && $mobile_encodeddata!=''){
				 $message = array("message" => __('invalid_request'),"status" => -1);                       
				 $mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				 exit;
			}
                		
			
			$host  = $_SERVER['HTTP_HOST'];
			$dateStamp = $_SERVER['REQUEST_TIME'];
			
                
			
		$default_companyid='';
		$company_all_currenttimestamp = $this->commonmodel->getcompany_all_currenttimestamp($default_companyid);
		switch($method)
		{
			case 'getcoreconfig':
			$_SESSION['session_set'] = 'SITE_NAME';
			$config_array = $api->select_site_settings($default_companyid);
			$config_array['app_name'] = SITE_NAME;		
			$config_array['site_country'] = DEFAULT_COUNTRY;		
			$config_array['facebook_key'] = FB_KEY;		
			$config_array['facebook_secretkey'] = FB_SECRET_KEY;		
			$config_array['facebook_share'] = FB_SHARE;		
			$config_array['twitter_share'] = TW_SHARE;		
			$config_array['site_logo'] = SITE_LOGO;
			$utc_time = gmdate('Y-m-d H:i:s');
			$utc_time = strtotime($utc_time);
			$config_array[0]['utc_time'] = $utc_time;
			$language_color_status = array();
			if(count($config_array) > 0)
			{
				if($default_companyid == '')
				{
					$config_array[0]['noimage_base'] = URL_BASE.'/public/images/noimages109.png';
					$config_array[0]['api_base'] = URL_BASE;
					$config_array[0]['logo_base'] = URL_BASE.'/public/admin/images/';					
					$config_array[0]['aboutpage_description'] = $this->app_description;
					$config_array[0]['admin_email'] = $this->siteemail;
					$config_array[0]['tell_to_friend_subject'] = __('telltofrien_subject');
					$config_array[0]['skip_credit'] = SKIP_CREDIT_CARD;
					$config_array[0]['metric'] = UNIT_NAME;
				}
				else
				{					
					$config_array[0]['noimage_base'] = URL_BASE.'/public/images/noimages109.png';
					$config_array[0]['api_base'] = URL_BASE;
					$config_array[0]['site_country'] = "";
					$config_array[0]['logo_base'] = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/site_logo/';	
					$config_array[0]['aboutpage_description'] = $this->app_description;		
					$config_array[0]['admin_email'] = $this->siteemail;	
					$config_array[0]['tell_to_friend_subject'] = __('telltofrien_subject');
					$config_array[0]['skip_credit'] = SKIP_CREDIT_CARD;
					$config_array[0]['metric'] = UNIT_NAME;
				}
				$config_array[0]['share_content'] = __('telltofriend_content');
				$config_array[0]['referral_code_info'] = __('referral_code_info_details');
				$config_array[0]['cancellation_setting'] = CANCELLATION_FARE;
				$config_array[0]['ios_google_map_key'] = IOS_GOOGLE_MAP_API_KEY;
				$config_array[0]['ios_google_geo_key'] = IOS_GOOGLE_GEO_API_KEY;
				$config_array[0]['android_google_api_key'] = ANDROID_GOOGLE_GEO_API_KEY;
				$config_array[0]['google_business_key'] = GOOGLE_BUSINESS_KEY_USED_STATUS;
				$expiry_date = "";
				if(EXPIRY_DATE != "")
				{
					$date = Commonfunction::convertphpdate('Y-m-d H:i:s',EXPIRY_DATE);
					$expiry_date = Commonfunction::getDateTimeFormat($date,1);
				}
				$config_array[0]['domain_expiry_date'] = $expiry_date;
				$referral_settings = 0;		
				$referral_settings_message = __("referral_settings_message");
				if(REFERRAL_SETTINGS == 1) {
					$referral_settings = 1;
					$referral_settings_message = "";
				} else {
					$referral_settings = 0;
					$referral_settings_message = __("referral_settings_message");
				}
				$config_array[0]['referral_settings'] = $referral_settings;
				$config_array[0]['referral_settings_message'] = $referral_settings_message;
				
				$driverReferralSettings = 0;		
				$driverRefSettingsMsg = __("referral_settings_message");		
				if(DRIVER_REFERRAL_SETTINGS == 1) {		
					$driverReferralSettings = 1;		
					$driverRefSettingsMsg = "";		
				}		
				$config_array[0]['driver_referral_settings'] = $driverReferralSettings;		
				$config_array[0]['driver_referral_settings_message'] = $driverRefSettingsMsg;		

				/***Get Company car model details start***/
				$company_model_details = $api->company_model_details($default_companyid);
				
				if(count($company_model_details)>0) {
					foreach($company_model_details as $key => $val) {
						if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/model_image/android/'.$val["model_id"].'_focus.png')) {
							$focus_image = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/model_image/android/'.$val['model_id'].'_focus.png';
							$company_model_details[$key]["focus_image"] = $focus_image;
						}
						if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/model_image/android/'.$val["model_id"].'_unfocus.png')) {
							$unfocus_image = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/model_image/android/'.$val['model_id'].'_unfocus.png';
							$company_model_details[$key]['unfocus_image'] = $unfocus_image;
						}
						if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/model_image/ios/'.$val["model_id"].'_focus.png')) {
							$focus_image_ios = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/model_image/ios/'.$val['model_id'].'_focus.png';
							$company_model_details[$key]["focus_image_ios"] = $focus_image_ios;
						}
						if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/model_image/ios/'.$val["model_id"].'_unfocus.png')) {
							$unfocus_image_ios = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/model_image/ios/'.$val['model_id'].'_unfocus.png';
							$company_model_details[$key]["unfocus_image_ios"] = $unfocus_image_ios;
						}
						if($val["model_id"] == 10 && file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/model_image/android/10_focus.svg')) {
							$svg_focus_image = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/model_image/android/10_focus.svg';
							$company_model_details[$key]["focus_image_svg"] = $svg_focus_image;
						}
					}
					$config_array[0]['model_details'] = $company_model_details;
				}else{ 
					$config_array[0]['model_details']=__('model_details_not_found');
				}
				$gateway_details = $this->commonmodel->gateway_details($default_companyid);

				$gateway_array = array(); $passenger_payment_option = array();
				foreach($gateway_details as $valArr) {
					$gateway_array[] = $valArr;
					if($valArr["pay_mod_id"] != 3) {
						$passenger_payment_option[] = $valArr;
						if(SKIP_CREDIT_CARD == 0)
							break;
					}
				}
				$config_array[0]['app_name'] = preg_replace("/#?[a-z0-9]+;/i","",SITE_NAME);
				$config_array[0]['gateway_array'] = $gateway_array;
				$config_array[0]['passenger_payment_option'] = $passenger_payment_option;
				$config_array[0]['Itune_Passenger'] = (isset($config_array[0]['itune_passenger']) && ($config_array[0]['itune_passenger'] !='')) ? $config_array[0]['itune_passenger'] :'';
				
				$config_array[0]['Itune_Driver'] = (isset($config_array[0]['itune_driver']) && ($config_array[0]['itune_driver'] !='')) ? $config_array[0]['itune_driver'] :'';
				
				$config_array[0]['Fb_Profile'] = (isset($config_array[0]['fb_profile']) && ($config_array[0]['fb_profile'] !='')) ? $config_array[0]['ios_foursquare_api_key'] :'';				
                                
				$config_array[0]['site_currency'] = CURRENCY;
				/***Get Company car model details end***/
				$details_arr = $config_array[0];
				# iOS
				$language_color_status['ios_driver_language'] = isset($config_array[0]['ios_driver_language']) ? $config_array[0]['ios_driver_language'] :'';
				$language_color_status['ios_passenger_language'] = isset($config_array[0]['ios_passenger_language']) ? $config_array[0]['ios_passenger_language'] :'';
				$language_color_status['ios_driver_colorcode'] = isset($config_array[0]['ios_driver_colorcode']) ? $config_array[0]['ios_driver_colorcode'] :'';
				$language_color_status['ios_passenger_colorcode'] = isset($config_array[0]['ios_passenger_colorcode']) ? $config_array[0]['ios_passenger_colorcode'] :'';
				# android
				$language_color_status['android_driver_language'] = isset($config_array[0]['android_driver_language']) ? $config_array[0]['android_driver_language'] :'';
				$language_color_status['android_passenger_language'] = isset($config_array[0]['android_passenger_language']) ? $config_array[0]['android_passenger_language'] :'';
				$language_color_status['android_passenger_colorcode'] = isset($config_array[0]['android_passenger_colorcode']) ? $config_array[0]['android_passenger_colorcode'] :'';
				$language_color_status['android_driver_colorcode'] = isset($config_array[0]['android_driver_colorcode']) ? $config_array[0]['android_driver_colorcode'] :'';
				# four square settings
				$config_array[0]['android_foursquare_api_key'] = (isset($config_array[0]['android_foursquare_api_key']) && ($config_array[0]['android_foursquare_api_key'] !='')) ? $config_array[0]['android_foursquare_api_key'] :'0';
				
				$config_array[0]['android_foursquare_status'] = (isset($config_array[0]['android_foursquare_status']) && ($config_array[0]['android_foursquare_status'] !='')) ? $config_array[0]['android_foursquare_status'] :'0';
				
				$config_array[0]['ios_foursquare_api_key'] = (isset($config_array[0]['ios_foursquare_api_key']) && ($config_array[0]['ios_foursquare_api_key'] !='')) ? $config_array[0]['ios_foursquare_api_key'] :'0';				
				
				$config_array[0]['ios_foursquare_status'] = (isset($config_array[0]['ios_foursquare_status']) && ($config_array[0]['ios_foursquare_status'] !='')) ? $config_array[0]['ios_foursquare_status'] :'0';
				# unset detail array
				unset($details_arr['ios_driver_language']);
				unset($details_arr['ios_passenger_language']);
				unset($details_arr['ios_driver_colorcode']);
				unset($details_arr['ios_passenger_colorcode']);
				unset($details_arr['android_driver_language']);
				unset($details_arr['android_passenger_language']);
				unset($details_arr['android_passenger_colorcode']);
				unset($details_arr['android_driver_colorcode']);
				$message = array("message" =>__('success'),"detail" => array($details_arr),"status" => 1);
				$message['language_color_status'] = $language_color_status;
				# language color codes loading
				
				$device_arr = array(1,2); # 1 -Android, 2 - iOS
				$language_color = array();
				$type=$path_type='';
				foreach($device_arr as $d){	
					$folderPath = MOBILE_iOS_IMAGES_FILES;
					$type = 'iOS';
					$path_type = 'iOSPaths';
					if($d == 1){
						$folderPath = MOBILE_ANDROID_IMAGES_FILES;
						$type = 'android';
						$path_type = 'androidPaths';
					}
					
					$dateStamp = $_SERVER['REQUEST_TIME'];
					$iOSPathArr = array();
					# dynamic language array
					$dynamic_language_array = array('en' => 'english');
					if(defined('DYNAMIC_LANGUAGE_ARRAY'))
						$dynamic_language_array = DYNAMIC_LANGUAGE_ARRAY;
						
					$iOSPassengerLanguageDOC = DOCROOT.$folderPath."language/passenger/";
					$iOSDriverLanguageDOC = DOCROOT.$folderPath."language/driver/";
					$iOSPassengerLanguageVIEW = URL_BASE.$folderPath."language/passenger/";				
					$iOSDriverLanguageVIEW = URL_BASE.$folderPath."language/driver/";	
					$iOSColorCode = URL_BASE.$folderPath."colorcode/PassengerAppColor.xml?timeCache=".$dateStamp;
					$iOSDriverColorCode = URL_BASE.$folderPath."colorcode/DriverAppColor.xml?timeCache=".$dateStamp;	
					# android color codes
					$androidPassColorcode = URL_BASE.$folderPath."colorcode/passengerAppColors.xml?timeCache=".$dateStamp;
					$androidDriverColorcode = URL_BASE.$folderPath."colorcode/driverAppColors.xml?timeCache=".$dateStamp;
					if(defined('STATIC_LANGUAGE_ARRAY')){
						$staticLanguArr = array_flip(STATIC_LANGUAGE_ARRAY);
					}else{
						$staticLanguArr = array("english"=>"en","arabic"=>"ar");
					}
					//~ echo '<pre>';print_r($staticLanguArr);exit;
					# Passenger Language Files
					$passLangFiles  = opendir($iOSPassengerLanguageDOC);
					$passLangs = array();
					while (false !== ($filename = readdir($passLangFiles))) {
						if($filename != '.' && $filename != '..'){
							$langArr = explode('_',$filename);
							if(isset($langArr[1]))
							$langName =  ($d == 2) ? str_replace('.strings','',$langArr[1]) : str_replace('.xml','',$langArr[1]);								
							$designType = 'LTR';								
							$checkRTL = strtolower($langName);	
							if(in_array($checkRTL,$dynamic_language_array)){								
								if($checkRTL == "arabic" || $checkRTL == "urdu") {								
									$designType = 'RTL';		
								}
								$langType = isset($staticLanguArr[$checkRTL]) ? $staticLanguArr[$checkRTL]:'';		
								$fileNam = $filename."?timeCache=".$dateStamp;		
								$langFilesArr = array("language"=>$langName,"design_type"=>$designType,"language_code"=>$langType,"url"=>$iOSPassengerLanguageVIEW.$fileNam);
								$passLangs[] = $langFilesArr;
							}
						}
					}

					# Driver Language Files
					$driverLangFiles  = opendir($iOSDriverLanguageDOC);
					$driverLangs = array();
					while (false !== ($driverFilename = readdir($driverLangFiles))) {
						if($driverFilename != '.' && $driverFilename != '..'){
							$driverLangArr = explode('_',$driverFilename);
							if(isset($driverLangArr[1]))
							$driverLangName =  ($d == 2) ? str_replace('.strings','',$driverLangArr[1]) : str_replace('.xml','',$driverLangArr[1]);
							$designType = 'LTR';
							$checkRTL = strtolower($driverLangName);
							if(in_array($checkRTL,$dynamic_language_array)){		
								if($checkRTL == "arabic" || $checkRTL == "urdu") {		
									$designType = 'RTL';		
								}		
								$langType = isset($staticLanguArr[$checkRTL]) ? $staticLanguArr[$checkRTL]:'';		
								$driverFileName = $driverFilename."?timeCache=".$dateStamp;		
								$driverLangFilesArr = array("language"=>$driverLangName,"design_type"=>$designType,"language_code"=>$langType,"url"=>$iOSDriverLanguageVIEW.$driverFileName);
								$driverLangs[] = $driverLangFilesArr;
							}
						}
					}
				
					$iOSPathArr = array("driver_language"=>$driverLangs,"passenger_language"=>$passLangs,"colorcode"=>$iOSColorCode,"driverColorCode"=>$iOSDriverColorCode);
					# android color code	
					if($d == 1){				
						$iOSPathArr["colorcode"] = $androidPassColorcode;
						$iOSPathArr["driverColorCode"] = $androidDriverColorcode;
					}
					
					$language_color[$type] =  $iOSPathArr;
				}				
				$message['language_color'] = $language_color;
				
			}
			else
			{
				$message = array("message" => __('failed'),"status" => 2);
			}
			$additional_param['json_unescaped_unicode_string']=JSON_UNESCAPED_UNICODE;
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			//unset($message,$details_arr,$config_array,$company_model_details,$gateway_details);
			break;
			
			case 'check_companydomain':
                            
					$company_domain = strtolower(trim($mobiledata['company_domain']));
                                        $result = $api->check_company_domain($mobiledata);	
                                        $api_key='';
                                        
					//New version Header Authorization checking
					if(isset($headers['Authorization'])){ 					
						$api_key = isset($result[0]['mobile_api_key'])?$result[0]['mobile_api_key']:'';
						$company_api_key=$headers['Authorization'];
                             
                        
					if($company_api_key!=$api_key){
                            $message = array("message" => __('invalid_company'),"status"=>-8);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
                            exit;
                        }
						
					}
													
				if(count($result) > 0){
					
					$baseurl = URL_BASE."mobileapi117/index/";
					$folderPath = ($mobiledata['device_type'] == 2) ? MOBILE_iOS_IMAGES_FILES : MOBILE_ANDROID_IMAGES_FILES;
					$iOSImage = URL_BASE.MOBILE_iOS_IMAGES_FILES."static_image/";
				} else {
					#Local
					$baseurl = URL_BASE."mobileapi117/index/";
					$folderPath = ($mobiledata['device_type'] == 2) ? MOBILE_iOS_IMAGES_FILES : MOBILE_ANDROID_IMAGES_FILES;
					$iOSImage = URL_BASE.MOBILE_iOS_IMAGES_FILES."static_image/";
				}
				
				$folderPath = ($mobiledata['device_type'] == 2) ? MOBILE_iOS_IMAGES_FILES : MOBILE_ANDROID_IMAGES_FILES;
                              
                                
				//functionality to get imgaes, language and color code files for iOS App
				$dateStamp = $_SERVER['REQUEST_TIME'];
				$iOSPathArr = array();		
				# dynamic language array
				$dynamic_language_array = array('en' => 'english');
				if(defined('DYNAMIC_LANGUAGE_ARRAY'))
					$dynamic_language_array = DYNAMIC_LANGUAGE_ARRAY;			
				$iOSPassengerLanguageDOC = DOCROOT.$folderPath."language/passenger/";
				$iOSDriverLanguageDOC = DOCROOT.$folderPath."language/driver/";
				$iOSPassengerLanguageVIEW = URL_BASE.$folderPath."language/passenger/";				
				$iOSDriverLanguageVIEW = URL_BASE.$folderPath."language/driver/";
				if($mobiledata['device_type'] == 2){
					$iOSColorCode = URL_BASE.$folderPath."colorcode/PassengerAppColor.xml?timeCache=".$dateStamp;
					$iOSDriverColorCode = URL_BASE.$folderPath."colorcode/DriverAppColor.xml?timeCache=".$dateStamp;
				} else {
					$iOSColorCode = URL_BASE.$folderPath."colorcode/";
					$iOSDriverColorCode = "";
				}
				//~ $staticLanguArr = array("english"=>"en","turkish"=>"tr","arabic"=>"ar","german"=>"de","russian"=>"ru","spanish"=>"es","indonesian"=>"id","french"=>"fr");
				if(defined('STATIC_LANGUAGE_ARRAY')){
					$staticLanguArr = array_flip(STATIC_LANGUAGE_ARRAY);
				}else{
					$staticLanguArr = array("english"=>"en","arabic"=>"ar");
				}
				//iOS Passenger Language Files
				$passLangFiles  = opendir($iOSPassengerLanguageDOC);
				$passLangs = array();
				while (false !== ($filename = readdir($passLangFiles))) {
					if($filename != '.' && $filename != '..'){
						$langArr = explode('_',$filename);
						if(isset($langArr[1]))
							$langName =  ($mobiledata['device_type'] == 2) ? str_replace('.strings','',$langArr[1]) : str_replace('.xml','',$langArr[1]);								
						$designType = 'LTR';								
						$checkRTL = strtolower($langName);	
						if(in_array($checkRTL,$dynamic_language_array)){
							if($checkRTL == "arabic" || $checkRTL == "urdu") {								
								$designType = 'RTL';		
							}
							$langType = isset($staticLanguArr[$checkRTL]) ? $staticLanguArr[$checkRTL]:'';		
							$fileNam = $filename."?timeCache=".$dateStamp;		
							$langFilesArr = array("language"=>$langName,"design_type"=>$designType,"language_code"=>$langType,"url"=>$iOSPassengerLanguageVIEW.$fileNam);
							$passLangs[] = $langFilesArr;
						}						
					}
				}
				
				//iOS Driver Language Files
				$driverLangFiles  = opendir($iOSDriverLanguageDOC);
				$driverLangs = array();
				while (false !== ($driverFilename = readdir($driverLangFiles))) {
					if($driverFilename != '.' && $driverFilename != '..'){
						$driverLangArr = explode('_',$driverFilename);
						if(isset($driverLangArr[1]))
						$driverLangName =  ($mobiledata['device_type'] == 2) ? str_replace('.strings','',$driverLangArr[1]) : str_replace('.xml','',$driverLangArr[1]);
						$designType = 'LTR';
						$checkRTL = strtolower($driverLangName);		
						if(in_array($checkRTL,$dynamic_language_array)){
							if($checkRTL == "arabic" || $checkRTL == "urdu") {		
								$designType = 'RTL';		
							}		
							$langType = isset($staticLanguArr[$checkRTL]) ? $staticLanguArr[$checkRTL]:'';		
							$driverFileName = $driverFilename."?timeCache=".$dateStamp;		
							$driverLangFilesArr = array("language"=>$driverLangName,"design_type"=>$designType,"language_code"=>$langType,"url"=>$iOSDriverLanguageVIEW.$driverFileName);
							$driverLangs[] = $driverLangFilesArr;
						}
					}
				}
				
				$host  = (!empty($mobiledata['company_domain'])) ? $mobiledata['company_domain'].".".$mobiledata['company_main_domain'] : $_SERVER['HTTP_HOST'];
				$dateStamp = $_SERVER['REQUEST_TIME'];
				$key = $host."-".$dateStamp;
				//New version Header Authorization checking
				if(isset($headers['Authorization'])){ 
					$encode =  $mobile_data_ndot_crypt->encrypt_encode($key);
				}
				$message = array("message" =>__('success'),"baseurl" => $baseurl,"apikey" => $api_key,"status" => 1);	
				$iOSPathArr = array("static_image"=>$iOSImage,"driver_language"=>$driverLangs,"passenger_language"=>$passLangs,"colorcode"=>$iOSColorCode,"driverColorCode"=>$iOSDriverColorCode);
				if($mobiledata['device_type'] == 2) {
					$message['iOSPaths'] = $iOSPathArr;
				} else {
					$message['androidPaths'] = $iOSPathArr;
				}
				$message['encode'] = $encode;
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
                                
				//unset($message, $iOSPassengerLanguageDOC, $iOSDriverLanguageDOC,$iOSPassengerLanguageVIEW,$iOSDriverLanguageVIEW,$iOSColorCode,$iOSDriverColorCode);
			break;
			
			case 'get_authentication':
				$host  = $_SERVER['HTTP_HOST'];
				$dateStamp = $_SERVER['REQUEST_TIME'];
				$mobilehost = isset($mobiledata['mobilehost'])?strtolower($mobiledata['mobilehost']):''; 
				if(isset($mobilehost)){
					if($mobilehost == $host){
						$value = $host."-".$dateStamp;
						//New version Header Authorization checking
						if(isset($headers['Authorization'])){ 
							$encode =  $mobile_data_ndot_crypt->encrypt_encode($value);
						}
						
						$message = array("message" =>__('success'),"encode" => $encode,"status" => 1);
					} else {
						$message = array("message" => "Host does not match","status" => 2);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						break;
					}
				} else {
					$message = array("message" => " Invalid Host Request ","status" => 2);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					break; 
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message);
			break;

			case 'getmodel_fare_details':
				$company_model_details = $api->company_model_details($default_companyid);
				if(count($company_model_details) > 0) {
					foreach($company_model_details as $k => $t) {
						if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/model_image/android/'.$t["model_id"].'_focus.png')) {
							$focus_image = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/model_image/android/'.$t['model_id'].'_focus.png';
							$company_model_details[$k]['focus_image'] = $focus_image;
						}
						if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/model_image/android/'.$t["model_id"].'_unfocus.png')) {
							$unfocus_image = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/model_image/android/'.$t['model_id'].'_unfocus.png';
							$company_model_details[$k]['unfocus_image'] = $unfocus_image;
						}
						if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/model_image/ios/'.$t["model_id"].'_focus.png')) {
							$focus_image_ios = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/model_image/ios/'.$t['model_id'].'_focus.png';
							$company_model_details[$k]['focus_image_ios'] = $focus_image_ios;
						}
						if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/model_image/ios/'.$t["model_id"].'_unfocus.png')) {
							$unfocus_image_ios = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/model_image/ios/'.$t['model_id'].'_unfocus.png';
							$company_model_details[$k]['unfocus_image_ios'] = $unfocus_image_ios;
						}
						
					}
					$details = array("model_details"=>$company_model_details);
					$message = array("message" =>__('success'),"detail" => $details,"status" => 1);
				} else {
					$message = array("message" => __('model_detail_not_found'),"status" => 2);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$company_model_details);
			break;

			case 'driver_location_history':
					$location_array = $mobiledata;
					$api = Model::factory(MOBILEAPI_107);
					$extended_api = Model::factory(MOBILEAPI_107_EXTENDED);
					$message = array("message" => __('driver_history_updated'),"status" => 1);
					if(!empty($location_array))
					{
						$company_id = $default_companyid;
						$device_type = isset($location_array['device_type']) ? $location_array['device_type'] :1;
						$deviceToken = isset($location_array['device_token']) ? $location_array['device_token'] :'';
						$driverId = $location_array['driver_id'];
						$check_device = $api->check_driver_device($driverId, $device_type, $deviceToken);
						if($check_device != 1){
							
							$message = array("message" => __('already_login1'));
							$message['status'] = ($check_device == 2) ? 15 : -15;
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						}
						$company_det =$api->get_company_id($location_array['driver_id']);
						if(count($company_det)>0){
							$company_id = $company_det[0]['company_id'];
							$company_all_currenttimestamp = $this->commonmodel->getcompany_all_currenttimestamp($company_det[0]['company_id']);
						}
	
						$history_validator = $this->history_validation($location_array);
						if($history_validator->check())
						{
							$driver_status = $location_array['status'];
							$device_token = "";
							$driver_id = $location_array['driver_id'];
							$trip_id = $location_array['trip_id'];
							$coordinates = explode('|',$location_array['locations']);
							if(count($coordinates)>1){
								$last_1=array_slice($coordinates, -2, 2, true);
								$coordinates = explode(',',$last_1[count($coordinates)-2]);
							}else{
								$coordinates = explode(',',$coordinates[0]);
							}
							
							$latitude = empty($coordinates['0'])?'0.0':$coordinates['0'];		
							$longitude = empty($coordinates['1'])?'0.0':$coordinates['1'];
							if(!empty($trip_id)){
								//Passenger or Dispatcher cancel alert to driver
								$tripCancelAlert = $api->getTripCancelAlert($driver_id, $trip_id, $company_all_currenttimestamp);
								if(count($tripCancelAlert) > 0){
									$canMsg = ($tripCancelAlert[0]['trip_status'] == 4) ? __('passenger_trip_cancelled') : __('dispatcher_trip_cancelled'); 
									$message = array("message" => $canMsg,"status"=>10);
									$update_driver_request_array  = array("notification_status" => 5,'id' => $tripCancelAlert[0]['trip_id']);
									$result = $extended_api->update_driver_request($update_driver_request_array);
									$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
									//unset($message,$update_driver_request_array);
									break;
								}
								
								/** driver update from dispatcher **/
								$trip_update_status = $api->get_trip_update_status($trip_id);
								if(count($trip_update_status) > 0)
								{
									$drop_location=isset($trip_update_status[0]['drop_location']) ? urldecode($trip_update_status[0]['drop_location']) : "";
									$drop_latitude=isset($trip_update_status[0]['drop_latitude'])?$trip_update_status[0]['drop_latitude']:"";
									$drop_longitude=isset($trip_update_status[0]['drop_longitude'])?$trip_update_status[0]['drop_longitude']:"";
									$pickup_location=isset($trip_update_status[0]['current_location']) ? urldecode($trip_update_status[0]['current_location']) : "";
									$pickup_latitude=isset($trip_update_status[0]['pickup_latitude'])?$trip_update_status[0]['pickup_latitude']:"";
									$pickup_longitude=isset($trip_update_status[0]['pickup_longitude'])?$trip_update_status[0]['pickup_longitude']:"";
									$driver_notes=isset($trip_update_status[0]['notes_driver'])?$trip_update_status[0]['notes_driver']:"";
									$notification_status=isset($trip_update_status[0]['notification_status'])?$trip_update_status[0]['notification_status']:"";
									$tripUpdateMSg = ($notification_status == 6) ? __('disptcher_updated') : __('passenger_update_drop_location');
									if($device_type == 2){
										
										$message = array("message" => __('driver_history_updated'),"status" => 1);
									}else{
										
										$message = array("message" => $tripUpdateMSg,"drop_location"=>$drop_location,"drop_latitude"=>$drop_latitude,"drop_longitude"=>$drop_longitude,"pickup_location"=>$pickup_location,"pickup_latitude"=>$pickup_latitude,"pickup_longitude"=>$pickup_longitude,"driver_notes"=>$driver_notes,"status"=>11);
									}
									$update_driver_request_array  = array("notification_status" => 7,'id' => $trip_id);
									$result = $extended_api->update_driver_request($update_driver_request_array);
									$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
									//unset($message,$drop_location,$drop_latitude,$drop_longitude,$pickup_location,$pickup_latitude,$pickup_longitude,$update_driver_request_array);
									break;
								}
							}
							/** driver update from dispatcher **/
							if($driver_status == 'F')
							{
								/***** Update Driver Current Location *********************/								
								if(count($coordinates)>0)
								{	
									if(($latitude != 0) && ($longitude != 0))
									{
										if(($location_array['trip_id'] == 0) || ($location_array['trip_id'] == ""))
										{
											$update_driver_array  = array(		
																"latitude" => $latitude,
																"longitude" => $longitude,
																"status" => 'F',
																"update_date"=> $company_all_currenttimestamp,
																'driver_id'=>$driver_id
															);
										}
										else
										{
											$update_driver_array  = array(
																"latitude" => $latitude,
																"longitude" => $longitude,
																"status" => strtoupper($driver_status),
																"update_date"=> $company_all_currenttimestamp,
																'driver_id'=>$driver_id
																);
										}
										
										$update_current_result = $extended_api->update_driver_location($update_driver_array);
										$check_new_request = $api->check_new_request($driver_id,$company_all_currenttimestamp);
										if($check_new_request > 0)
										{
											$passenger_name = "";
											$get_passenger_log_details = $api->get_passenger_log_detail($check_new_request);
											
											if(count($get_passenger_log_details)>0)
											{
												foreach($get_passenger_log_details as $values)
												{														
													$p_device_type = $values->passenger_device_type;
													$p_device_token  = $values->passenger_device_token;
													/** get minimum speed **/
													$taxi_id=$values->taxi_id;
													$dr_company_id=$values->company_id;
													$get_min_speed=$api->get_minimum_speed($taxi_id,$default_companyid);
													$belowspeed_mins= isset($get_min_speed[0]['taxi_min_speed']) ? $get_min_speed[0]['taxi_min_speed'] : 0;
													/** get minimum speed **/
													$pickupplace  = urldecode($values->current_location);
													$dropplace = urldecode($values->drop_location);	
													$passenger_id = $values->passengers_id;
													$passenger_phone = $values->passenger_phone;
													$time_to_reach_passen = $values->time_to_reach_passen;
													$sub_logid = $values->sub_logid;
													$pickup_latitude = $values->pickup_latitude;
													$pickup_longitude = $values->pickup_longitude;
													$drop_latitude = $values->drop_latitude;
													$drop_longitude = $values->drop_longitude;
													$passenger_salutation = $values->passenger_salutation;
													$p_name = $values->passenger_name;
													$pickup_time = $values->pickup_time;
													$bookby = $values->bookby;
													$notes_driver = $values->notes_driver;		
												}
												$passenger_name = $passenger_salutation.' '.ucfirst($p_name);
												$notification_time = $this->notification_time;
												if($notification_time != 0 ){ $timeoutseconds = $notification_time;}else{$timeoutseconds = 15;}
												//if timeout seconds greater than 60 seconds we have to convert to mins and secs
												if($timeoutseconds > 60) {
													$notification_minutes = floor($timeoutseconds / 60);
													$notification_seconds = $timeoutseconds % 60;
													$notification_minutes = ($notification_minutes < 10) ? '0'.$notification_minutes : $notification_minutes;
												} else {
													$notification_minutes = "00";
													$notification_seconds = $timeoutseconds;
												}
												$notification_seconds = ($notification_seconds < 10) ? '0'.$notification_seconds : $notification_seconds;
												$total_timeout = $notification_minutes." : ".$notification_seconds;
												
												$trip_details = array("message" => __('api_request_confirmed_passenger'),"status" => "1","passengers_log_id" => $check_new_request,"booking_details" => array ( "pickupplace" => $pickupplace, "dropplace" => $dropplace, "pickup_time" => $pickup_time,"driver_id" => $driver_id,"passenger_id" => $passenger_id,"roundtrip" => "","passenger_phone" => $passenger_phone,"cityname" => "", "distance_away" => "","sub_logid" => $sub_logid,"drop_latitude" => $drop_latitude,"drop_longitude" => $drop_longitude, "taxi_id" => $taxi_id, "company_id" => $dr_company_id,"pickup_latitude" => $pickup_latitude, "pickup_longitude" => $pickup_longitude,"bookedby" => $bookby, "passenger_name" => $passenger_name,"profile_image" => "","drop" => $dropplace),"estimated_time" => $time_to_reach_passen ,"notification_time" => $timeoutseconds,"notification_minutes" => $notification_minutes,"notification_seconds" => $notification_seconds,"notes" =>$notes_driver,"belowspeed_mins"=>$belowspeed_mins);	
												
												$message = array("message" => __('driver_history_updated'),"trip_details"=>$trip_details,"status" => 5);	
												
												$check_another_request = $api->check_new_request_bydriver($driver_id,$company_all_currenttimestamp,$check_new_request);
												if(count($check_another_request) > 0){
													foreach($check_another_request as $cns){
														$api->change_driver_reqflow($cns['trip_id'],$cns['available_drivers'],$cns['rejected_timeout_drivers']);
													}
												}
												
												$datas  = array(
													'status' => 1,
													'trip_id'=>$check_new_request
												);
												$datas_update1 = $extended_api->update_driver_request_details($datas);
												
												$update_driver_array  = array(
													"status" => 'B',
													'driver_id'=>$driver_id
												);
												$datas_update2 = $extended_api->update_driver_location($update_driver_array);
											}	
											else
											{
												$message = array("message" => __('driver_history_updated'),"status" => 1);
											}
										}
										else
										{
											$message = array("message" => __('driver_history_updated'),"status" => 1);
										}
									}									
								}
							}
							else if($driver_status == 'A')
							{
								/***** Update Driver Current Location ******************************************/
								$update_driver_array  = array(
									'latitude' => $latitude,
									'longitude' => $longitude,
									'status' => strtoupper($driver_status),
									'update_date'=> $company_all_currenttimestamp,
									'driver_id'=>$driver_id
								);
								$update_current_result = $extended_api->update_driver_location($update_driver_array);
								/*******************************************************************************/
								$result = $api->save_driver_location_history($location_array,$default_companyid);
								$distance = isset($result[1]) ? $result[1] :'0';
								$trip_fare = 0;
								/** to get the trip fare based on the distance and minutes travelled for ongoing trip **/
								if(!empty($location_array['trip_id'])){
									$tripDets = $api->getTripCompanyModelDets($location_array['trip_id']);
											
									if($tripDets['booking_from'] == 2){
										
										$farecalculation_type = (FARE_SETTINGS != 2) ? FARE_CALCULATION_TYPE : $tripDets['fare_calculation_type'];
										$taxi_fare_details = $api->get_model_fare_details($tripDets['company_id'],$tripDets['taxi_modelid'],$tripDets['search_city'],$tripDets['brand_type']);
										$base_fare = '0';
										$min_km_range = '0';
										$min_fare = '0';
										$below_above_km_range = '0';
										$below_km = '0';
										$above_km = '0';
										$minutes_cost= '0';
										if(count($taxi_fare_details) > 0)
										{
											$base_fare = $taxi_fare_details[0]['base_fare'];
											$min_km_range = $taxi_fare_details[0]['min_km'];
											$min_fare = $taxi_fare_details[0]['min_fare'];
											$below_above_km_range = $taxi_fare_details[0]['below_above_km'];
											$below_km = $taxi_fare_details[0]['below_km'];
											$above_km = $taxi_fare_details[0]['above_km'];
											$minutes_fare = $taxi_fare_details[0]['minutes_fare'];
										}
										/********Minutes fare calculation *******/ 
									   $interval  = abs(strtotime($company_all_currenttimestamp) - strtotime($tripDets['actual_pickup_time']));
									   $minutes   = round($interval / 60);
									   /********Minutes fare calculation *******/
										$baseFare = $base_fare;
										$total_fare = $base_fare;
										if($farecalculation_type==1 || $farecalculation_type==3)
										{
											if($distance < $min_km_range)
											{
												//min fare has set as base fare if trip distance 
												$baseFare = $min_fare;
												$total_fare = $min_fare;
											}
											else if($distance <= $below_above_km_range)
											{
												$fare = $distance * $below_km;
												$total_fare  = 	$fare + $base_fare ;
											}
											else if($distance > $below_above_km_range)
											{
												$fare = $distance * $above_km;
												$total_fare  = 	$fare + $base_fare ;
											}
										}
										
										if($farecalculation_type==2 || $farecalculation_type==3)
										{
											/********** Minutes fare calculation ************/
											if($minutes_fare > 0)
											{
												$minutes_cost = $minutes * $minutes_fare;
												$total_fare  = $total_fare + $minutes_cost;
											}
											/************************************************/
										}
										$trip_fare = $total_fare;
									}
								}
								/*****/
								$distance = isset($distance) ? number_format($distance, 2, '.', '') : '0';
								$trip_fare = isset($trip_fare) ? number_format($trip_fare, 2, '.', '') : '0';
								if(isset($result[0]) && $result[0] == 1)
								{
									$message = array("message" => __('driver_history_updated'),"status" => 1,"distance"=>$distance,"trip_fare"=>$trip_fare);	
								}
								else if($result == -1)
								{
									$message = array("message" => __('driver_history_already'),"status" => -1);	
								}
								else if($result == 2)
								{
									$message = array("message" => __('invalid_user'),"status" => 2);	
								}
								else if($result == 3)
								{
									$message = array("message" => __('no_access'),"status" => 3);	
								}
								else if($result == 5)
								{
									$message = array("message" => __('driver_history_updated'),"status" => 1,"distance"=>$distance,"trip_fare"=>$trip_fare);	
								}
								else
								{
									$message = array("message" => __('invalid_user'),"status"=>-1);	
								}
							}
							elseif($driver_status == 'B')
							{
								/***** Update Driver Current Location *********************************************************/
								if(($latitude != 0) &&($longitude != 0))
								{
									$update_driver_array  = array(
										"latitude" => $latitude,
										"longitude" => $longitude,
										"status" => strtoupper($driver_status),
										"update_date"=> $company_all_currenttimestamp,
										'driver_id'=>$driver_id
									);
									$update_current_result = $extended_api->update_driver_location($update_driver_array);
								}
								/**********************************************************************************************/
								$get_passenger_log_details = $api->get_passenger_log_detail($trip_id);
								if(count($get_passenger_log_details)>0)
								{
									$driver_reply = $get_passenger_log_details[0]->driver_reply;
									$travel_status = $get_passenger_log_details[0]->travel_status;
									$location_array = array("drop_location" => $get_passenger_log_details[0]->drop_location,"drop_latitude" => $get_passenger_log_details[0]->drop_latitude,"drop_longitude" => $get_passenger_log_details[0]->drop_longitude);
									$message = array("message" => __('driver_history_updated'),"drop_location_details" => $location_array,"status" => 1);
									if(($driver_reply == 'A') && ($travel_status == 4))
									{
										$message = array("message" => __("trip_cancelled_passenger"),"detail"=>"","status"=>7);
									}
								}
								else
								{
									$message = array("message" => __('driver_history_updated'),"status" => 1);	
								}
								
								$check_new_request_trip = $api->check_new_request_bydriver($driver_id,$company_all_currenttimestamp,$trip_id);
								$check_driver_status_free=$api->check_driver_status_free($driver_id);
								if($check_driver_status_free=="B" && count($check_new_request_trip) > 0){
									foreach($check_new_request_trip as $cns){
										$api->change_driver_reqflow($cns['trip_id'],$cns['available_drivers'],$cns['rejected_timeout_drivers']);
									}
								} 
							}
							else
							{
								$message = array("message" => __('validation_error'),"detail"=>"","status"=>-3);
							}
						}
						else
						{
							$errors = $history_validator->errors('errors');	
							$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>-3);
						}
					}
					else
					{
						$message = array("message" => __('invalid_request'),"status"=>-4);
					}
					
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$get_passenger_log_details,$taxi_fare_details,$get_passenger_log_details,$trip_update_status,$check_new_request_trip,$check_driver_status_free,$tripDets,$result,$history_validator);
					break;	
			
			case 'nearestdriver_list':
					$search_array = $mobiledata;
					$validator = $this->nearestdriver_validation($search_array);			
					if($validator->check())
					{
						$passenger_id = isset($search_array['passenger_id']) ? $search_array['passenger_id']:'';
						if($search_array['latitude'] !='0' && $search_array['longitude'] !='0')
						{
							
							$skip_fav = isset($search_array['skip_fav']) ? $search_array['skip_fav']: 0;
							$favourite_places = $popular_places = array();
							if($skip_fav != 1){									
								$get_favouritepopularplaces = $api->get_favouritepopularplaces($passenger_id,1);
								if(!empty($get_favouritepopularplaces)){
									$favourite_places = $get_favouritepopularplaces;
								}
							}
							
							#popular
							$get_favouritepopularplaces = $api->get_favouritepopularplaces($passenger_id,2,$search_array['latitude'],$search_array['longitude']);									
							if(!empty($get_favouritepopularplaces)){
								$popular_places = $get_favouritepopularplaces;
							}							
							
							$passengerStatus = 3;$passengerStatusMessage = "";
							$passenger_id = $search_array['passenger_id'];
							$passenger_status_result = $api->get_passenger_status($passenger_id);
							if(count($passenger_status_result) > 0) {
								if(isset($passenger_status_result[0]["user_status"]) && $passenger_status_result[0]["user_status"] != 'A') {
									$passengerStatus = 5;
									$passengerStatusMessage = ($passenger_status_result[0]["user_status"] == "D") ? __("passenger_status_blocked_msg") : __("passenger_status_deleted_msg");
								}
							}
							$find_model = Model::factory(FIND);	
							$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);							
							$latitude = $search_array['latitude'];
							$longitude = $search_array['longitude'];
							$miles = DEFAULTMILE;//$search_array['no_of_miles'];
							$unit = UNIT; // 0 - KM, 1 - Miles
							$taxi_model = $search_array['motor_model'];
							$service_type="";
							$passengerCompany = (!empty($passenger_id)) ? $api->get_passenger_company_id($passenger_id) : 0;
							$company_id = ($passengerCompany != 0) ? $passengerCompany : $default_companyid;
							//~ $currentTime = $this->commonmodel->getcompany_all_currenttimestamp($company_id);
							$pickupTimezone = $api->getpickupTimezone($latitude,$longitude);
							$currentTime = convert_timezone('now',$pickupTimezone);
							
							$driver_details = $find_model->getNearestDrivers($taxi_model,$latitude,$longitude,$currentTime,$company_id,$miles,$unit);
							$get_modelfare_details=$api->get_modelfare_details($default_companyid,$taxi_model);
							$getFavDrivers = '';
							if(!empty($passenger_id)) { //to update passengers lat long
								$datas  = array('latitude' => $latitude, 'longitude' => $longitude); // Start to Pickup
								$uplatlong = $api_ext->update_nearpassengers($datas,$passenger_id);
								//to get favourite drivers for a passenger
								$favDrivers = $api->getFavDrivers($passenger_id);
								$getFavDrivers = (!empty($favDrivers)) ? explode(",",$favDrivers) : '';
							}
							$nearest_driver='';
							$a=1;
							$temp='10000';
							$prev_min_distance='10000~0~0~0';
							$taxi_id='';
							$temp_driver=0;
							$nearest_key=0;
							$prev_key=0;
							$total_count = count($driver_details);						
							$company_contact_no='';
							if(COMPANY_CID != 0)
							{
								$company_contact_no=COMPANY_CONTACT_PHONE_NUMBER;
							}
							$no_vehicle_msg=__('no_vehicle_msg').$company_contact_no;
							
							//Get Fare details of the Taxi model_id Start
							$fare_details=__('no_fare_details_found');
							if($get_modelfare_details!=0){
								$fare_details=$get_modelfare_details[0];
							}
							//Get Fare details of the Taxi model_id End
							$fare_details['metric'] = UNIT_NAME;
							$fare_details['fare_calculation_type']=FARE_CALCULATION_TYPE; 
							$fav_drivers_exist = 0;
							if($total_count > 0)
							{
								$driver_id = isset($driver_details[0]['driver_id'])?$driver_details[0]['driver_id']:"";
								$totalrating = 0;
								foreach($driver_details as $key => $value)
								{
									//Set nearest driver equal to 1											
									if($driver_id == $value['driver_id'])
									{
										$driver_details[$nearest_key]['nearest_driver'] ='1';
									}
									else
									{
										$driver_details[$key]['nearest_driver'] ='0';
									}
									
									if(!empty($getFavDrivers) && in_array($value['driver_id'],$getFavDrivers)) {
										$fav_drivers_exist++;
									}
									// Get last 20 coordinates of the driver Start
									$get_driver_coordinates= '0';
									$driver_details[$key]['driver_coordinates'] = $get_driver_coordinates;
									// Get last 20 coordinates of the driver End

									//Get Nearest driver Taxi speed Start										
									//FARE_CALCULATION_TYPE : 1 => Distance, 2 => Time, 3=> Distance / Time
											
									//Get Nearest driver Taxi speed Start

									$driver_details[$key]['distance_km'] = round($value['distance_km'],5);
								}
								if(count($driver_details) > 0)
									$message = array("detail" => $driver_details,"fav_drivers"=>$fav_drivers_exist,"fav_driver_message"=>__('fav_driver_book_message'),"fare_details"=>$fare_details,"driver_around_miles"=>DEFAULTMILE,"status" => 1,"message" => 'success','metric'=>UNIT_NAME);
								else
									$message = array("message" => $no_vehicle_msg,"fav_drivers"=>$fav_drivers_exist,"fav_driver_message"=>__('fav_driver_book_message'),"fare_details"=>$fare_details,"driver_around_miles"=>DEFAULTMILE,"status" => 0);	
								
								$message['favourite_places'] = $favourite_places;
								$message['popular_places'] = $popular_places;	
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								//unset($message);
								break;
						  }
						  else
						  {
							  $message = array("message" => $no_vehicle_msg,"fav_drivers"=>$fav_drivers_exist,"fav_driver_message"=>__('fav_driver_book_message'),"fare_details"=>$fare_details,"status" => $passengerStatus,"passenger_status_message" => $passengerStatusMessage);
							 
							  $message['favourite_places'] = $favourite_places;
							  $message['popular_places'] = $popular_places;							 
							  $mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							  //unset($message);
							  exit;							  
						  }
					  }
					  else
					  {
							$message = array("message" => __('lat_not_zero'),"status"=>-4);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							//unset($message);
							exit;	
					  }
					}
					else
					{
						$errors = $validator->errors('errors');	
						$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>-5);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						//unset($message,$errors);
						exit;					
					}							
					//unset($message,$datas);
					break;	
					
					
			//Passenger Signup with Referral code concept
			case 'passenger_signup_single':
			   $p_first_name = (isset($mobiledata['first_name'])) ? urldecode($mobiledata['first_name']) : '';
			   $p_last_name = (isset($mobiledata['last_name'])) ? urldecode($mobiledata['last_name']) : '';
			   $p_email = (isset($mobiledata['email'])) ? urldecode($mobiledata['email']) : '';
			   $p_phone = (isset($mobiledata['phone'])) ? urldecode($mobiledata['phone']) : '';
			   $country_code = (isset($mobiledata['country_code'])) ? $mobiledata['country_code'] : '';
			   $p_password = (isset($mobiledata['password'])) ? urldecode($mobiledata['password']) : '';
			   $p_confirm_password = (isset($mobiledata['confirm_password'])) ? urldecode($mobiledata['confirm_password']) : '';
			   $devicetoken = (isset($mobiledata['devicetoken'])) ? urldecode($mobiledata['devicetoken']) : '';
			   $device_id = (isset($mobiledata['deviceid'])) ? urldecode($mobiledata['deviceid']) : '';
			   $devicetype = (isset($mobiledata['devicetype'])) ? urldecode($mobiledata['devicetype']) : '';	
			   $accessToken = (isset($mobiledata['accesstoken'])) ? urldecode($mobiledata['accesstoken']) : '';
			   $uid = (isset($mobiledata['userid'])) ? urldecode($mobiledata['userid']) : '';				   
			   $referral_code = (isset($mobiledata['referral_code'])) ? urldecode($mobiledata['referral_code']) : '';
			   $p_acc_validator = $this->pasenger_signup_validation($mobiledata);
			   if($p_acc_validator->check())
			   {
				   $email_exist = $api->check_email_passengers($p_email,$default_companyid);
				   $phone_exist = $api->check_phone_passengers($p_phone,$default_companyid,$country_code);
				   $referralcode_exist = $api->check_referral_code_exist($referral_code,$default_companyid);
				  
				   if($email_exist > 0)
					{
						$message = array("message" => __('email_exists'),"status"=> 2);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					}
					else if($phone_exist > 0)
					{
						$message = array("message" => __('phone_exists'),"status"=> 3);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					}
					else if(!empty($referral_code) && $referralcode_exist == 0)
					{
						$message = array("message" => __('referral_code_not_exists'),"status"=> 5);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					}
					else
					{
						$image_name = '';
						if($uid != '') {
							//to get profile image from facebook and store it passenger
							$thumb_image = file_get_contents("http://graph.facebook.com/".$uid."/picture?width=".PASS_THUMBIMG_WIDTH1."&height=".PASS_THUMBIMG_HEIGHT1."");
							$thumb_image_name =  'thumb_'.$uid.'.jpg';
							$thumb_image_path = DOCROOT.PASS_IMG_IMGPATH.$thumb_image_name; 
							@chmod(DOCROOT.PASS_IMG_IMGPATH,0777);
							@chmod($thumb_image_path,0777);
							file_put_contents($thumb_image_path, $thumb_image);

							$edit_image = file_get_contents("http://graph.facebook.com/".$uid."/picture?width=".PASS_THUMBIMG_WIDTH1."&height=".PASS_THUMBIMG_HEIGHT1."");
							$edit_image_name =  'edit_'.$uid.'.jpg';
							$edit_image_path = DOCROOT.PASS_IMG_IMGPATH.$edit_image_name; 
							@chmod(DOCROOT.PASS_IMG_IMGPATH,0777);
							@chmod($edit_image_path,0777);
							file_put_contents($edit_image_path, $edit_image);

							/** Big Image **/
							$big_image = file_get_contents("http://graph.facebook.com/".$uid."/picture?width=".PASS_IMG_WIDTH."&height=".PASS_IMG_HEIGHT."");
							$image_name =  $uid.'.jpg';
							$big_image_path = DOCROOT.PASS_IMG_IMGPATH.$image_name; 
							@chmod(DOCROOT.PASS_IMG_IMGPATH,0777);
							@chmod($big_image_path,0777);
							file_put_contents($big_image_path, $big_image);


							$base_image = imagecreatefromjpeg($edit_image_path);
							$width = 100;
							$height = 19;
							$top_image = imagecreatefrompng(URL_BASE.PUBLIC_IMAGES_FOLDER."edit.png");
							$merged_image = DOCROOT.PASS_IMG_IMGPATH.'edit_'.$uid.'.jpg';
							imagesavealpha($top_image, true);
							imagealphablending($top_image, true);
							imagecopy($base_image, $top_image, 0, 83, 0, 0, $width, $height);
							imagejpeg($base_image, $merged_image);
						}
						/******/						
						$otp = text::random($type = 'numeric', $length = 4);
						$acc_details_result=$api->passenger_signup_with_referral($p_first_name, $p_last_name, $p_email, $p_phone, $country_code, $p_password, $p_confirm_password,$otp,$referral_code,$devicetoken,$device_id,$devicetype,$default_companyid,$accessToken,$uid,$image_name);							
						if($acc_details_result == 1) 
						{ 
							
								$mail="";
								$replace_variables=array(REPLACE_LOGO=>EMAILTEMPLATELOGO,REPLACE_SITENAME=>$this->app_name,REPLACE_USERNAME=>'',REPLACE_OTP=>$otp,REPLACE_SITELINK=>URL_BASE.'users/contactinfo/',REPLACE_SITEEMAIL=>$this->siteemail,REPLACE_SITEURL=>URL_BASE,REPLACE_COMPANYDOMAIN=>$this->domain_name,REPLACE_COPYRIGHTS=>SITE_COPYRIGHT,REPLACE_COPYRIGHTYEAR=>COPYRIGHT_YEAR);
								if($this->lang!='en'){
									if(file_exists(DOCROOT.TEMPLATEPATH.$this->lang.'/otp-'.$this->lang.'.html')){
										$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.$this->lang.'/otp-'.$this->lang.'.html',$replace_variables);
									}else{
										$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'otp.html',$replace_variables);
									}
								}
								else
								{
									$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'otp.html',$replace_variables);
								}
								
								$to = $p_email;
								$from = $this->siteemail;
								$subject = __('otp_subject')." - ".$this->app_name;
								$redirect = "no";
								if(SMTP == 1)
								{
									include($_SERVER['DOCUMENT_ROOT']."/modules/SMTP/smtp.php");
								}
								else
								{
									// To send HTML mail, the Content-type header must be set
									$headers  = 'MIME-Version: 1.0' . "\r\n";
									$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
									// Additional headers
									$headers .= 'From: '.$from.'' . "\r\n";
									$headers .= 'Bcc: '.$to.'' . "\r\n";
									mail($to,$subject,$message,$headers);
								} 

								//free sms url with the arguments
								if(SMS == 1)
								{
									$message_details = $this->commonmodel->sms_message_by_title('otp');
									if(count($message_details) > 0) {
										$to = $country_code.$p_phone;
										$message = $message_details[0]['sms_description'];
										$message = str_replace("##OTP##",$otp,$message);
										$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
										$this->commonmodel->send_sms($to,$message);
									}
								}
							
							$detail = array("email"=>$p_email,"phone"=>$p_phone,"skip_credit"=>SKIP_CREDIT_CARD);
							$message = array("message" =>__('account_save_otp'),"detail"=>$detail,"status"=> 1);
						}
						else
						{
							$message = array("message" => __('try_again'),"status"=> 4);
						}
					 $mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					}
			   }
			   else
			   {
					$errors = $p_acc_validator->errors('errors');
					$message = array("message"=>$errors,"status"=>-1);
					$mobile_data_ndot_crypt->encrypt_encode_json($message,$additional_param);
					exit;
				}		
			//unset($message,$result,$errors,$message_details,$acc_details_result,$thumb_image,$email_exist,$p_acc_validator,$phone_exist,$referralcode_exist);
			break;
			
			case 'otp_verify':
				$otp = isset($mobiledata['otp']) ? $mobiledata['otp'] : '';
				$email = isset($mobiledata['email']) ? $mobiledata['email'] : '';
				if(!empty($otp)) {
					$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
					$otp_verification = $api->otp_verification($otp,$email);
					if($otp_verification > 0) {
						$update_passenger_array  = array("user_status" => "A"); // activate user if the otp is valid
						$result = $api_ext->update_passengers_email($update_passenger_array,$email);
						$detail = array("email" => $email,"skip_credit" => SKIP_CREDIT_CARD);
						$message = array("message" =>__('signup_success'),"detail" => $detail,"status" => 1);
					} else {
						$message = array("message" => __('invalid_otp'),"status"=>-2);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status"=>-1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$result,$detail,$otp_verification);
				exit;
			
			
			case 'passenger_wallet':
				$passenger_id = isset($mobiledata['passenger_id']) ? $mobiledata['passenger_id'] : '';
				if(!empty($passenger_id)) {
					$passenger_wallet = $api->get_passenger_wallet_amount($passenger_id);
					$siteInfo = $api->siteinfo_details();
					$amount_details = array("wallet_amount1"=> (double)$siteInfo[0]['wallet_amount1'],
											"wallet_amount2"=> (double)$siteInfo[0]['wallet_amount2'],
											"wallet_amount3"=> (double)$siteInfo[0]['wallet_amount3'],
											"wallet_amount_range"=> $siteInfo[0]['wallet_amount_range']);
					if(count($passenger_wallet) > 0) {
						$message = array("wallet_amount" => $passenger_wallet[0]['wallet_amount'],"amount_details"=>$amount_details,"status"=>1);
					} else {
						$message = array("message" => __('invalid_user'),"status"=>-2);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status"=>-1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$passenger_wallet,$amount_details);
				exit;
			break;
			
			case 'wallet_addmoney':
				$passenger_id = isset($mobiledata['passenger_id']) ? $mobiledata['passenger_id'] : '';
				$money = isset($mobiledata['money']) ? $mobiledata['money'] : '';
				$promo_code = isset($mobiledata['promo_code']) ? urldecode($mobiledata['promo_code']) : '';
				$p_validator = $this->wallet_addmoney_validation($mobiledata);
				$promocodeAmount = 0;
			    if($p_validator->check())
			    {
				    if($promo_code != "")
					{
						$promodiscount = $api->getpromodetails($promo_code,$passenger_id);
						$promocodeAmount = ($promodiscount/100) * $money;
						$mobiledata['money'] = $money + $promocodeAmount;
					}
					
					$passenger_wallet = $this->wallet_addmoney($mobiledata,$default_companyid,$promo_code,$promocodeAmount);
					$cancelFare = $api->get_passenger_cancel_farebyid($passenger_id);
					$wallAmount = 0;
					//if($passenger_wallet == 0 && $passenger_wallet==1) {
					$passwallArr = explode("#",$passenger_wallet);
					$wallAmount = isset($passwallArr[1]) ? $passwallArr[1] : 0;
					$passenger_wallet = $passwallArr[0];
					//}
					
					
					
					if($passenger_wallet == 1) {
                                                $credit_card_sts = ($wallAmount >= $cancelFare) ? 0 : SKIP_CREDIT_CARD;
						$message = array("message" => __('amount_added_wallet'), "credit_card_status" => $credit_card_sts,"status"=>1);
					} 
					else if($passenger_wallet == 0)
					{
						$gateway_response = isset($wallAmount)?$wallAmount:'Payment Failed';
						$message = array("message" => $gateway_response, "gateway_response" =>$gateway_response,"status"=>0);		
					} else {
						$message = array("message" => __('no_payment_gateway'),"status"=>-1);
					}
				} else {
					$errors = $p_validator->errors('errors');	
					$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>-1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$p_validator,$errors,$cancelFare,$passenger_wallet,$promodiscount,$gateway_response);
			break;
			
			case 'invite_with_referral':
				$passenger_id = isset($mobiledata['passenger_id']) ? $mobiledata['passenger_id'] : '';
				if(!empty($passenger_id)) {
					$passengerReferral = $api->get_passenger_wallet_amount($passenger_id);
					if(count($passengerReferral) > 0) {
						if(REFERRAL_SETTINGS == 1) {
							$referral_settings = 1;
							$referral_settings_message = "";
						} else {
							$referral_settings = 0;
							$referral_settings_message = __("referral_settings_message");
						}
						$detail = array("referral_code" => $passengerReferral[0]['referral_code'],
										"referral_amount" => $passengerReferral[0]['referral_code_amount'],
										"referral_settings" => $referral_settings,
										"referral_settings_message" => $referral_settings_message);
						$message = array("message" => __('referral_amount'),"detail" => $detail,"status"=>1);
					} else {
						$message = array("message" => __('invalid_user'),"status"=>-2);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status"=>-1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$passengerReferral,$detail);
			break;
			
			//api to check valid promocode
			case 'check_valid_promocode':
				$passenger_id = isset($mobiledata['passenger_id']) ? $mobiledata['passenger_id'] : '';
				$promo_code = isset($mobiledata['promo_code']) ? urldecode($mobiledata['promo_code']) : '';
				if(!empty($passenger_id) && !empty($promo_code)) {
					$check_promo = $api->checkwalletpromocode($promo_code,$passenger_id,$default_companyid);
					if($check_promo == 0)
					{
						$message = array("message" => __('invalid_promocode_wallet'),"status" => 3);
					}
					else if($check_promo == 3)
					{
						$message = array("message" => __('promo_code_startdate'),"status" => 3);
					}
					else if($check_promo == 4)
					{
						$message = array("message" => __('promo_code_expired'),"status" => 3);
					}
					else if($check_promo == 2)
					{
						$message = array("message" => __('promo_code_limit_exceed'),"status" => 3);
					}
					else
					{
						$message = array("message" => __('promocode_valid'),"status" => 1);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status"=>-1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$check_promo,$promo_code);
				exit;
			break;
													
			case 'passenger_account_details':
				   $p_email = $mobiledata['email'];
				   $p_phone = $mobiledata['phone'];
				   $p_password = $mobiledata['password'];
				   $devicetoken = $mobiledata['devicetoken'];
				   $device_id = "";
				   $devicetype = $mobiledata['devicetype'];					   
				   
				   $p_acc_validator = $this->account_validation($mobiledata);
				   if($p_acc_validator->check())
				   {
					   $email_exist = $api->check_email_passengers($p_email,$default_companyid);
					   $phone_exist = $api->check_phone_passengers($p_phone,$default_companyid);
					   if($email_exist > 0)
						{
							$message = array("message" => __('email_exists'),"status"=> 2);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						}
						else if($phone_exist > 0)
						{
							$message = array("message" => __('phone_exists'),"status"=> 3);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						}
						else
						{							
							$otp = text::random($type = 'alnum', $length = 5);
							$referral_code = text::random($type = 'alnum', $length = 6);
							$acc_details_result=$api->add_p_account_details($mobiledata,$otp,$referral_code,$devicetoken,$device_id,$devicetype,$default_companyid);							
							if($acc_details_result == 1) 
							{ 
								//free sms url with the arguments
								if(SMS == 1)
								{
									$message_details = $this->commonmodel->sms_message_by_title('otp');
									if(count($message_details) > 0) {
										$to = $p_phone;
										$message = $message_details[0]['sms_description'];
										$message = str_replace("##OTP##",$otp,$message);
										$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
										$this->commonmodel->send_sms($to,$message);
									}
								} 
								$detail = array("email"=>$p_email,"skip_credit"=>SKIP_CREDIT_CARD);
								$message = array("message" =>__('account_saved'),"detail"=>$detail,"status"=> 1);
							}
							else
							{
								$message = array("message" => __('try_again'),"status"=> 4);
							}	
						 $mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						}
				   }
				   else
				   {
						$errors = $p_acc_validator->errors('errors');
						$message = array("message"=>$errors,"status"=>-1);
						$mobile_data_ndot_crypt->encrypt_encode_json($message,$additional_param);
						//unset($errors,$result);
						exit;
					}
					//unset($message,$detail,$message_details,$acc_details_result,$p_acc_validator,$email_exist,$phone_exist);
					break;
				   
				case 'resend_otp':
					$otp_array = $mobiledata;
					$email = $mobiledata['email'];
					$user_type = $otp_array['user_type'];
					if(isset($email))
					{
						$otp = text::random($type = 'numeric', $length = 4);
						$otp_result = $api->update_otp($otp_array,$otp,$default_companyid);
						if($otp_result == 1) 
						{
							$mail="";
							$replace_variables=array(REPLACE_LOGO=>EMAILTEMPLATELOGO,REPLACE_SITENAME=>$this->app_name,REPLACE_USERNAME=>'',REPLACE_OTP=>$otp,REPLACE_SITELINK=>URL_BASE.'users/contactinfo/',REPLACE_SITEEMAIL=>$this->siteemail,REPLACE_SITEURL=>URL_BASE,REPLACE_COMPANYDOMAIN=>$this->domain_name,REPLACE_COPYRIGHTS=>SITE_COPYRIGHT,REPLACE_COPYRIGHTYEAR=>COPYRIGHT_YEAR);

							/* Added for language email template */
							if($this->lang!='en')
							{
								if(file_exists(DOCROOT.TEMPLATEPATH.$this->lang.'/otp-'.$this->lang.'.html'))
								{
									$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.$this->lang.'/otp-'.$this->lang.'.html',$replace_variables);
								}
								else
								{
									$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'otp.html',$replace_variables);
								}
							}
							else
							{
								$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'otp.html',$replace_variables);
							}

							/* Added for language email template */
							$to = $email;
							$from = $this->siteemail;
							if($user_type == 'D')
							{
								$subject = __('otp_driver_subject')." - ".$this->app_name;
							}
							else
							{
								$subject = __('otp_subject')." - ".$this->app_name;	
							}
							$redirect = "no";
							if(SMTP == 1)
							{
								include($_SERVER['DOCUMENT_ROOT']."/modules/SMTP/smtp.php");
							}
							else
							{
								// To send HTML mail, the Content-type header must be set
								$headers  = 'MIME-Version: 1.0' . "\r\n";
								$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
								// Additional headers
								$headers .= 'From: '.$from.'' . "\r\n";
								$headers .= 'Bcc: '.$to.'' . "\r\n";
								mail($to,$subject,$message,$headers);
							}

							//free sms url with the arguments
							if(SMS == 1)
							{
								if($otp_array['user_type']=='P')
								{
									$phoneno = $this->commonmodel->getuserphone('P',$email);
								}
								else
								{
									$phoneno = $this->commonmodel->getuserphone('D',$email);
								}
								$message_details = $this->commonmodel->sms_message_by_title('otp');
								if(count($message_details) > 0) {
									$to = $phoneno;
									$message = $message_details[0]['sms_description'];
									$message = str_replace("##OTP##",$otp,$message);
									$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
									$this->commonmodel->send_sms($to,$message);
								}
							}
							$detail = array("email"=>$email);
							$message = array("message" => __('resend_otp'),"detail"=>$detail,"status"=> 1);
						}
						else
						{
							$message = array("message" => __('try_again'),"status"=> 4);
						}
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);   
						exit;
					}
					else
					{
						$message = array("message"=>__('invalid_email'),"status"=>-1);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);   
						//unset($result);
						exit;
					}
					//unset($message,$result,$detail,$message_details,$replace_variables,$otp_result,$phone_exist);
					break;
			
			case 'passenger_personal_details':
					$p_personal_array= $mobiledata;										
					$referred_passenger_id="";
					if(isset($p_personal_array['email']))
					{
						$validator = $this->passenger_profile_validation($p_personal_array);						
						if($validator->check())
						{
							$referral_code = $p_personal_array['referral_code'];
							if($referral_code != "")
							{					
								$validate_referral_code = $api->check_referral_code($referral_code);						
								if(is_array($validate_referral_code))
								{
									$referred_passenger_id = $validate_referral_code[0]['id'];
								}		
								else
								{
									$message = array("message" => __('invalid_referral_code'),"status"=> 3);
									$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
									exit;
								}		
							}																			
							if($p_personal_array['profile_image'] != NULL)
							{
								/* Profile Update */
								$imgdata = base64_decode($p_personal_array['profile_image']);
								$f = finfo_open();
								$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
								$mime_type = explode('/',$mime_type);
								$mime_type = $mime_type[1];
								$img = imagecreatefromstring($imgdata); 

								if($img != false)
								{                   
									$image_name = uniqid().'.'.$mime_type;
									$thumb_image_name = 'thumb_'.$image_name;
									$image_url = DOCROOT.PASS_IMG_IMGPATH.'/'.$image_name;                    								
									$image_path = DOCROOT.PASS_IMG_IMGPATH.$image_name;  
									imagejpeg($img,$image_url);
									imagedestroy($img);
									chmod($image_path,0777);
									$d_image = Image::factory($image_path);
									$path11=DOCROOT.PASS_IMG_IMGPATH;
									Commonfunction::imageoriginalsize($d_image,$path11,$image_name,90);
									$path12=$thumb_image_name;
									Commonfunction::imageoriginalsize($d_image,$path11,$thumb_image_name,90);
									$update_array = array(								
									"salutation"=>$p_personal_array['salutation'],
									"name" => $p_personal_array['firstname'],
									"lastname" => $p_personal_array['lastname'],
									"email" => $p_personal_array['email'],
									"profile_image" => $image_name,
									"user_status"=>'A',
									"activation_status"=>1);
									$message = $api->save_passenger_personaldata($update_array,$referred_passenger_id,$default_companyid);
								}
								else
								{
									$message = array("message" => __('image_not_upload'),"status"=>4);								
								}								
							}
							else
							{
									$update_array = array(
									"salutation"=>$p_personal_array['salutation'],
									"name" => $p_personal_array['firstname'],
									"lastname" => $p_personal_array['lastname'],
									"email" => $p_personal_array['email'],
									"user_status"=>'A',
									"activation_status"=>1);
									$message = $api->save_passenger_personaldata($update_array,$referred_passenger_id,$default_companyid);
							}
							/*****************************************/												
							if($message == 0)
							{
								$passenger_details = $api->passenger_detailsbyemail($p_personal_array['email'],$default_companyid);
								
										$id="";
										if(count($passenger_details) >0)
										{
											$id = $passenger_details[0]['id'];
											$email =  $passenger_details[0]['email'];
										}
										$detail = array("passenger_id"=>$id,"skip_credit"=>SKIP_CREDIT_CARD);
										$message = array("message" => __('personal_updated'),"detail"=>$detail,"status"=>1);	
							}	
							if($message == -1)
							{
								$message = array("message" => __('try_again'),"status"=>-1);	
							}	
						}
						else
						{							
							$validation_error = $validator->errors('errors');	
							$message = array("message" => $validation_error,"status"=>-3);		
						}
					}
					else
					{
						$message = array("message" => __('invalid_email'),"status"=>-4);	
					}
					
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$validation_error,$passenger_details,$update_array,$imgdata,$validator,$phone_exist,$validate_referral_code);
					break;			
			
			case 'passenger_card_details':
			$p_card_array= $mobiledata;			
			$savecard = $p_card_array['savecard'];
			$email = $p_card_array['email'];
			$config_array = $api->select_site_settings($default_companyid);
			$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
			if($savecard == 1)
			{
				$card_validation = $this->passenger_card_validation($p_card_array);			
				if($card_validation->check())
				{
					$creditcard_no = $p_card_array['creditcard_no'];
					$card_holder_name = (isset($p_card_array['card_holder_name'])) ? urldecode($p_card_array['card_holder_name']) : '';
					$creditcard_cvv = (isset($p_card_array['creditcard_cvv'])) ? urldecode($p_card_array['creditcard_cvv']) : '';
					$expdatemonth = (isset($p_card_array['expdatemonth'])) ? urldecode($p_card_array['expdatemonth']) : '';
					$expdateyear = (isset($p_card_array['expdateyear'])) ? urldecode($p_card_array['expdateyear']) : '';
					$authorize_status =$api->isVAlidCreditCard($creditcard_no,"",true);
					if($authorize_status == 0)
					{
						$message = array("message" => __('invalid_card'),"status"=> 2);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						exit;
					}
					
					$passenger_details = $api->passenger_detailsbyemail($email,$default_companyid);
					$passenger_id = (count($passenger_details) > 0) ? $passenger_details[0]['id'] : 0;
					//Credit Card Pre authorization section goes here
					//preauthorization with amount "0"(Zero)
					$preAuthorizeAmount = PRE_AUTHORIZATION_REG_AMOUNT;
					list($returncode,$paymentResult,$fcardtype,$preAuthorizeAmount) = $api->creditcardPreAuthorization($passenger_id,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$preAuthorizeAmount);
					if($returncode==0)
					{
						//preauthorization with amount "1"
						$preAuthorizeAmount = PRE_AUTHORIZATION_RETRY_REG_AMOUNT;					
						list($returncode,$paymentResult,$fcardtype,$preAuthorizeAmount)= $api->creditcardPreAuthorization($passenger_id,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$preAuthorizeAmount);
						
					}
					if($returncode != 0)
					{
						$result = $api->save_passenger_carddata($p_card_array,$default_companyid,$paymentResult,$preAuthorizeAmount,$fcardtype);
						if($result) {
							$void_transaction=$api->voidTransactionAfterPreAuthorize($result,$paymentResult);
						}
					}
					else
					{
						$message=array("message"=>__('insufficient_fund'),"status"=>3);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						//unset($message);
						exit;
					}
							
					if($result > 0)
					{
						$total_array = array();
						if(count($passenger_details) > 0)
						{
							if((!empty($passenger_details[0]['profile_image'])) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'thumb_'.$passenger_details[0]['profile_image'])){ 
								$profile_image = URL_BASE.PASS_IMG_IMGPATH.'thumb_'.$passenger_details[0]['profile_image']; 
							}
							else{ 
								$profile_image = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
							} 										
							$passenger_id= $passenger_details[0]['id'];
							$total_array['id'] = $passenger_details[0]['id'];
							$total_array['salutation'] = $passenger_details[0]['salutation'];
							$total_array['name'] = $passenger_details[0]['name'];
							$total_array['lastname'] = $passenger_details[0]['lastname'];
							$total_array['email'] = $passenger_details[0]['email'];
							$total_array['profile_image'] = $profile_image;
							$total_array['country_code'] = $passenger_details[0]['country_code'];
							$total_array['phone'] = $passenger_details[0]['phone'];
							$total_array['address'] = $passenger_details[0]['address'];
							$total_array['split_fare'] = '1';
							$referral_code = $passenger_details[0]['referral_code'];
							$total_array['referral_code'] = $referral_code;
							$total_array['referral_code_amount'] = $passenger_details[0]['referral_code_amount'];
							$ref_message = TELL_TO_FRIEND_MESSAGE.''.$referral_code;
							$ref_discount = REFERRAL_DISCOUNT;
							$telltofriend_message = TELL_TO_FRIEND_MESSAGE;
							//Newly Added-13.11.2014
							$total_array['site_currency'] = CURRENCY;
							$total_array['aboutpage_description'] = $this->app_description;
							$total_array['tell_to_friend_subject'] = __('telltofrien_subject');
							$total_array['skip_credit'] = SKIP_CREDIT_CARD;
							$total_array['metric'] = UNIT_NAME;
							//variable to know whether the passenger have credit card details
							$total_array['credit_card_status'] = 1;
							/***Get Company car model details start***/
							$company_model_details = $api->company_model_details($default_companyid);
							if(count($company_model_details)>0){
								$total_array['model_details']=$company_model_details;
							}else{
								$total_array['model_details']="model details not found";
							}
							/***Get Company car model details end***/										
							$total_array['telltofriend_message'] = $telltofriend_message;	
						}				
						$p_password = isset($passenger_details[0]['org_password'])?$passenger_details[0]['org_password']:'';
						//free sms url with the arguments
						if(SMS == 1)
						{
							$message_details = $this->commonmodel->sms_message_by_title('account_create_sms');
							if(count($message_details) > 0) {
								$to = isset($total_array['phone'])? $total_array['country_code'].$total_array['phone']:'';
								$message = $message_details[0]['sms_description'];
								$message = str_replace("##USERNAME##",$to,$message);
								$message = str_replace("##PASSWORD##",$p_password,$message);
								$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
								$this->commonmodel->send_sms($to,$message);
							}							
						}
							
						$mobile_no = isset( $passenger_details[0]['phone'])? $passenger_details[0]['country_code'].$passenger_details[0]['phone']:'';
						$username = isset( $passenger_details[0]['name'])? $passenger_details[0]['name']:'';
						$replace_variables=array(REPLACE_LOGO=>EMAILTEMPLATELOGO,REPLACE_SITENAME=>$this->app_name,REPLACE_USERNAME=>$username,REPLACE_SITELINK=>URL_BASE.'users/contactinfo/',REPLACE_MOBILE=>$mobile_no,REPLACE_PASSWORD=>$p_password,REPLACE_SITEEMAIL=>$this->siteemail,REPLACE_SITEURL=>URL_BASE,REPLACE_COMPANYDOMAIN=>$this->domain_name,REPLACE_COPYRIGHTS=>SITE_COPYRIGHT,REPLACE_COPYRIGHTYEAR=>COPYRIGHT_YEAR);
										
						/* Added for language email template */
						if($this->lang!='en')
						{				
							if(file_exists(DOCROOT.TEMPLATEPATH.$this->lang.'/driver-register-'.$this->lang.'.html'))
							{
								$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.$this->lang.'/driver-register-'.$this->lang.'.html',$replace_variables);
							}else
							{
								$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'driver-register.html',$replace_variables);
							}
						}
						else
						{
							$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'driver-register.html',$replace_variables);
						}
						/* Added for language email template */
						$to = $email;
						$from = $this->siteemail;
						$subject = __('pass_account_details')." - ".$this->app_name;	
						$redirect = "no";	
						if(SMTP == 1)
						{
							include($_SERVER['DOCUMENT_ROOT']."/modules/SMTP/smtp.php");
						}
						else
						{
							// To send HTML mail, the Content-type header must be set
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
							// Additional headers
							$headers .= 'From: '.$from.'' . "\r\n";
							$headers .= 'Bcc: '.$to.'' . "\r\n";
							mail($to,$subject,$message,$headers);	
						}
						//}
						/*** Update Pssenger password as empty ************/
						$update_passenger_array  = array("login_status" => "S","split_fare" => "1","org_password" => ""); // 
						$result = $api_ext->update_passengers($update_passenger_array,$passenger_id);
						/***************************************************/
						$message = array("message" => __('signup_success'),"detail"=>$total_array,"status"=>1);		
					}
					elseif($result == -1)
					{
						$message = array("message" => __('you_have_detail'),"status"=>3);
					}
					else
					{
						$message = array("message" => __('try_again'),"status"=>1);	
					}				
				}
				else
				{							
					$validation_error = $card_validation->errors('errors');	
					$message = array("message" => __('validation_error'),"detail"=>$validation_error,"status"=>-3);		
				}
			}
			else
			{
					$update_cred_sts  = array("skip_credit_card" => '1');
					$update_current_result = $api_ext->update_passengers_email($update_cred_sts,$email);
					$passenger_details = $api->passenger_detailsbyemail($email,$default_companyid);
					$total_array = array();
					if(count($passenger_details) > 0)
					{
						if((!empty($passenger_details[0]['profile_image'])) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'thumb_'.$passenger_details[0]['profile_image'])){ 
						$profile_image = URL_BASE.PASS_IMG_IMGPATH.'thumb_'.$passenger_details[0]['profile_image']; 
						}
						else{ 
						$profile_image = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
						} 								
						$passenger_id = $passenger_details[0]['id'];
						$total_array['id'] = $passenger_details[0]['id'];
						$total_array['salutation'] = $passenger_details[0]['salutation'];
						$total_array['name'] = $passenger_details[0]['name'];
						$total_array['lastname'] = $passenger_details[0]['lastname'];
						$total_array['email'] = $passenger_details[0]['email'];
						$total_array['profile_image'] = $profile_image;
						$total_array['phone'] = $passenger_details[0]['phone'];
						$total_array['country_code'] = $passenger_details[0]['country_code'];
						$total_array['address'] = $passenger_details[0]['address'];
						$total_array['split_fare'] = '0';
						$referral_code = $passenger_details[0]['referral_code'];
						$total_array['referral_code'] = $referral_code;
						$total_array['referral_code_amount'] = $passenger_details[0]['referral_code_amount'];
						$ref_message = TELL_TO_FRIEND_MESSAGE.''.$referral_code;
						$ref_discount = REFERRAL_DISCOUNT;
						$telltofriend_message = TELL_TO_FRIEND_MESSAGE;//str_replace("#REFDIS#",$ref_discount,$ref_message); 
						//Newly Added-13.11.2014
						$total_array['site_currency'] = CURRENCY;
						$total_array['facebook_share'] = $config_array[0]['facebook_share'];
						$total_array['twitter_share'] = $config_array[0]['twitter_share'];
						$total_array['aboutpage_description'] = $this->app_description;
						$total_array['tell_to_friend_subject'] = __('telltofrien_subject');
						$total_array['skip_credit'] = SKIP_CREDIT_CARD;
						$total_array['metric'] = UNIT_NAME;
						$total_array['credit_card_status'] = 0;
						/***Get Company car model details start***/
							$company_model_details = $api->company_model_details($default_companyid);
							if(count($company_model_details)>0){
								$total_array['model_details']=$company_model_details;
							}else{
								$total_array['model_details']="model details not found";
							}
						/***Get Company car model details end***/										
						$total_array['telltofriend_message'] = $telltofriend_message;	
					}						
				$p_password = isset($passenger_details[0]['org_password'])?$passenger_details[0]['org_password']:'';
							if(SMS == 1)
							{
								$message_details = $this->commonmodel->sms_message_by_title('account_create_sms');
								if(count($message_details) > 0) {
									$to = isset($total_array['phone'])? $total_array['country_code'].$total_array['phone']:'';
									$message = $message_details[0]['sms_description'];
									$message = str_replace("##USERNAME##",$to,$message);
									$message = str_replace("##PASSWORD##",$p_password,$message);
									$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
									$this->commonmodel->send_sms($to,$message);
								}								
							}
							
							$mobile_no = isset( $passenger_details[0]['phone'])? $passenger_details[0]['country_code'].$passenger_details[0]['phone']:'';
							$username = isset( $passenger_details[0]['name'])? $passenger_details[0]['name']:'';
							$replace_variables=array(REPLACE_LOGO=>EMAILTEMPLATELOGO,REPLACE_SITENAME=>$this->app_name,REPLACE_USERNAME=>$username,REPLACE_SITELINK=>URL_BASE.'users/contactinfo/',REPLACE_MOBILE=>$mobile_no,REPLACE_PASSWORD=>$p_password,REPLACE_SITEEMAIL=>$this->siteemail,REPLACE_SITEURL=>URL_BASE,REPLACE_COMPANYDOMAIN=>$this->domain_name,REPLACE_COPYRIGHTS=>SITE_COPYRIGHT,REPLACE_COPYRIGHTYEAR=>COPYRIGHT_YEAR);
							/* Added for language email template */
							if($this->lang!='en')
							{				
								if(file_exists(DOCROOT.TEMPLATEPATH.$this->lang.'/driver-register-'.$this->lang.'.html'))
								{
									$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.$this->lang.'/driver-register-'.$this->lang.'.html',$replace_variables);
								}else
								{
									$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'driver-register.html',$replace_variables);
								}
							}
							else
							{
								$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'driver-register.html',$replace_variables);
							}
							$to = $email;
							$from = $this->siteemail;
								$subject = __('pass_account_details')." - ".$this->app_name;	
							$redirect = "no";	
							if(SMTP == 1)
							{
								include($_SERVER['DOCUMENT_ROOT']."/modules/SMTP/smtp.php");
							}
							else
							{
								// To send HTML mail, the Content-type header must be set
								$headers  = 'MIME-Version: 1.0' . "\r\n";
								$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
								// Additional headers
								$headers .= 'From: '.$from.'' . "\r\n";
								$headers .= 'Bcc: '.$to.'' . "\r\n";
								mail($to,$subject,$message,$headers);	
							}
					/*** Update Pssenger password as empty ************/
					$update_passenger_array  = array("login_status" => "S","org_password" => ""); // 
					$result = $api_ext->update_passengers($update_passenger_array,$passenger_id);
					/***************************************************/							
				$message = array("message" => __('signup_success'),"detail"=>$total_array,"status"=>1);	
			}
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);//unset($message,$update_passenger_array,$result,$replace_variables,$message_details,$total_array,$profile_image,$passenger_details,$update_current_result,$update_passenger_array,$void_transaction,$preAuthorizeAmount,$passenger_details,$authorize_status);
			break;
			
			case 'passenger_referral_code':
				$referral_code = (isset($mobiledata['referral_code'])) ? urldecode($mobiledata['referral_code']) : '';
				$email = (isset($mobiledata['email'])) ? urldecode($mobiledata['email']) : '';
				if(!empty($referral_code)) {
					$referralcode_exist = $api->check_referral_code_exist($referral_code,$default_companyid);
					if($referralcode_exist > 0) {
						
						$passenger_details = $api->passenger_detailsbyemail($email,$default_companyid);
						if(count($passenger_details) > 0) {
							$referral_used = $api->check_referral_code_used($passenger_details[0]['id']);
							if($referral_used == 0) {
								$save_referral = $api->save_referral_code($passenger_details[0]['id'],$referral_code,$default_companyid,$passenger_details[0]['device_id'],$passenger_details[0]['device_token']);
								if($save_referral == 1) {
									$message = array("message" => __('referral_code_save_successful'),"status"=> 1);
								} else {
									$message = array("message" => __('try_again'),"status"=>-1);	
								}
							} else {
								$message = array("message" => __('referral_code_already_used'),"status"=> 4);
							}
						} else {
							$message = array("message" => __('invalid_user'),"status"=>-1);
						}
					} else {
						$message = array("message" => __('referral_code_not_exists'),"status"=> -1);
					}
				} else {
					$message = array("message" => __('referral_code_not_empty'),"status"=> -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$save_referral,$referralcode_exist,$passenger_details,$referral_used);
			break;
			
			case 'passenger_fb_connect':
					$array = $mobiledata;
					$accessToken = $array['accesstoken'];
					$uid = $array['userid'];
					$fname = $array['fname'];
					$lname = $array['lname'];
					$email = $array['fbemail'];
					$devicetoken = $array['devicetoken'];
					$device_id = $array['deviceid'];
					$devicetype = $array['devicetype'];
								/** Thumb Image ****/
								$thumb_image = @file_get_contents("https://graph.facebook.com/".$uid."/picture?width=".PASS_THUMBIMG_WIDTH1."&height=".PASS_THUMBIMG_HEIGHT1."");
								$thumb_image_name =  'thumb_'.$uid.'.jpg';
								$thumb_image_path = DOCROOT.PASS_IMG_IMGPATH.$thumb_image_name; 
								$thumb_image_file = fopen($thumb_image_path, "w") or die("Unable to open file!");
								fwrite($thumb_image_file, $thumb_image);
								fclose($thumb_image_file);

								$edit_image = @file_get_contents("https://graph.facebook.com/".$uid."/picture?width=".PASS_THUMBIMG_WIDTH1."&height=".PASS_THUMBIMG_HEIGHT1."");
								$edit_image_name =  'edit_'.$uid.'.jpg';
								$edit_image_path = DOCROOT.PASS_IMG_IMGPATH.$edit_image_name;
								$image_file = fopen($edit_image_path, "w") or die("Unable to open file!");
                               fwrite($image_file, $edit_image);
                               fclose($image_file);

								/** Big Image **/
								$big_image = @file_get_contents("https://graph.facebook.com/".$uid."/picture?width=".PASS_IMG_WIDTH."&height=".PASS_IMG_HEIGHT."");
								$image_name =  $uid.'.jpg';
								$big_image_path = DOCROOT.PASS_IMG_IMGPATH.$image_name;
								$big_image_file = fopen($big_image_path, "w") or die("Unable to open file!");
                               fwrite($big_image_file, $big_image);
                               fclose($big_image_file);
								$base_image = imagecreatefromjpeg($edit_image_path);
								$width = 100;
								$height = 19;
								$top_image = imagecreatefrompng(URL_BASE.PUBLIC_IMAGES_FOLDER."edit.png");
								$merged_image = DOCROOT.PASS_IMG_IMGPATH.'edit_'.$uid.'.jpg';
								imagesavealpha($top_image, true);
								imagealphablending($top_image, true);
								imagecopy($base_image, $top_image, 0, 83, 0, 0, $width, $height);
								imagejpeg($base_image, $merged_image);

								/*************************/	
								$otp = text::random($type = 'alnum', $length = 5);
								$referral_code = strtoupper(text::random($type = 'alnum', $length = 6));
								$status = $api->register_facebook_user($accessToken,$uid,$otp,$referral_code,$fname,$lname,$email,$image_name,$devicetoken,$device_id,$devicetype,$default_companyid);
								//~ echo $status;exit;
								if($status == -10){
									$message = array("message" => __('passenger_blocked'),"status" => $status);
									$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
									break;
								}
								$passenger_details = $api->passenger_detailsbyemail($email,$default_companyid,$uid);	
								
								if((!empty($passenger_details[0]['profile_image'])) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'thumb_'.$passenger_details[0]['profile_image'])){ 
									$profile_image = URL_BASE.PASS_IMG_IMGPATH.'thumb_'.$passenger_details[0]['profile_image']; 
								}
								else{ 
									$profile_image = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
								} 

								if(count($passenger_details) > 0){
									$passenger_details[0]['profile_image'] = $profile_image;
								}
								$config_array = $api->select_site_settings($default_companyid);								
									$total_array = array();
									$result = $passenger_details;
									$fbemail = '';
									$skip_credit_card = 2;
									if(count($result) > 0)
									{
										
										$total_array['id'] = $result[0]['id'];
										$total_array['name'] = $result[0]['name'];
										$total_array['email'] = $result[0]['email'];
										$fbemail = $total_array['email'];
										$total_array['profile_image'] = $profile_image;
										$total_array['country_code'] = $result[0]['country_code'];
										$total_array['phone'] = $result[0]['phone'];
										$total_array['address'] = $result[0]['address'];
										$total_array['user_status'] = $result[0]['user_status'];
										$total_array['login_from'] = $result[0]['login_from'];
										$total_array['referral_code'] = $result[0]['referral_code'];
										$total_array['referral_code_amount'] = $result[0]['referral_code_amount'];
										$total_array['split_fare'] = $result[0]['split_fare'];
										//to check whether the passenger gave
										$skip_credit_card = $result[0]['skip_credit_card'];
										$telltofriend_message = TELL_TO_FRIEND_MESSAGE;//str_replace("#REFDIS#",$ref_discount,$ref_message); 
										$total_array['telltofriend_message'] = $telltofriend_message;
										
										//Newly Added-13.11.2014
										$total_array['site_currency'] = CURRENCY;
										$total_array['aboutpage_description'] = $this->app_description;
										$total_array['tell_to_friend_subject'] = __('telltofrien_subject');
										$total_array['skip_credit'] = SKIP_CREDIT_CARD;
										$total_array['metric'] = UNIT_NAME;
										//variable to know whether the passenger have credit card
										$check_card_data = $api->check_passenger_card_data($result[0]['id']);
										$credit_card_sts = ($check_card_data == 0) ? 0 : SKIP_CREDIT_CARD;
										$total_array['credit_card_status'] = $credit_card_sts;
									}
							    if($status==1)
								{
									/***Get Company car model details start***/
										$company_model_details = $api->company_model_details($default_companyid);
										if(count($company_model_details)>0){
											$total_array['model_details']=$company_model_details;
										}else{
											$total_array['model_details']="model details not found";
										}
									/***Get Company car model details end***/
									$message = array("message" => __('succesful_login_flash'),"detail"=>$total_array,"status"=> 1); 
								}
								else if($status==2)
								{	
									$detail = array("email"=>$fbemail);														
									$message = array("message"=>__('account_saved_withoutmobile'),"detail"=>$detail,"status"=>2);					 
								}
								else if($status==4 || $status==3)
								{
									if(SKIP_CREDIT_CARD !=1 || $skip_credit_card != 1)
									{
										$message = array("message"=>__('p_card_data_not_filled'),"detail"=>$total_array,"status"=>4);	
									}
									else
									{
										/***Get Company car model details start***/
											$company_model_details = $api->company_model_details($default_companyid);
											if(count($company_model_details)>0){
												$total_array['model_details']=$company_model_details;
											}else{
												$total_array['model_details']="model details not found";
											}
										/***Get Company car model details end***/
										$message = array("message" => __('succesful_login_flash'),"detail"=>$total_array,"status"=> 1);
									}
								}
								else if($status==-2)
								{	
									$detail = array("email"=>$email);							 
									$message = array("message"=>__('account_not_activated'),"detail"=>$detail,"status"=>-2);													 
								}
								else if($status==10)
								{
									$message = array("message" => __('facebook_email_empty'),"status"=>10);
								}
								else
								{
									$message = array("message" => __('facebook_error'),"status"=>-1);
								}

					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$detail,$company_model_details,$total_array,$config_array,$passenger_details,$edit_image,$edit_image_path,$thumb_image,$thumb_image_path,$thumb_image_file,$image_file,$big_image,$big_image_path,$big_image_file,$base_image,$top_image,$merged_image,$status);
					break;
			
			case 'passenger_mobile_otp':

					$array = $mobiledata;
					$email = $array['fbemail'];
					$mobile = $array['mobile'];
					$country_code = isset($array['country_code']) ? $array['country_code'] : '';

					$phone_exist = $api->check_phone_bypassengers($mobile,$email,$default_companyid,$country_code);
					
					if($phone_exist != 0)
					{
						$message = array("message" => __('phone_exists'),"status"=>4);
					}
					else 
					{
						if($email != null && $mobile != null)
						{
							$status = $api->update_passenger_mobile($email,$mobile,$country_code);

							if($status == 1)
							{
								$passenger_details = $api->passenger_detailsbyemail($email,$default_companyid);
								$otp = $passenger_details[0]['otp'];
								$id = $passenger_details[0]['id'];
								$mail="";						
								$total_array = array();
								if(count($passenger_details) > 0)
								{
									$total_array['id'] = $passenger_details[0]['id'];
									$total_array['name'] = $passenger_details[0]['name'];
									$total_array['email'] = $passenger_details[0]['email'];
									$total_array['phone'] = $passenger_details[0]['phone'];
									$total_array['address'] = $passenger_details[0]['address'];
								}
								$detail = array("passenger_id"=>$id);
								$message = array("message" => __('account_saved'),"detail"=>$total_array,"status"=>1);
							}
							else
							{
								$message = array("message" => __('try_again'),"status"=>2);
							}
						}
						else
						{
							$message = array("message" => __('invalid_user'),"status"=>3);
						}	

					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$detail,$phone_exist,$status,$message,$replace_variables,$message_details,$total_array);
					break;

			case 'passenger_login':
				$p_login_array = $mobiledata;
				
				$validator = $this->passenger_login_validation($p_login_array);
					if($validator->check())
					{ 
						$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
					   $phone_exist = $api->check_phone_passengers($p_login_array['phone'],$default_companyid,$p_login_array['country_code']);
					   if($phone_exist == 0)
						{
							$message = array("message" => __('phone_not_exists'),"status"=> 2);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							//unset($message);
							break;
						} 
						else
						{
							$result = $api->passenger_login($p_login_array['phone'],md5(urldecode($p_login_array['password'])),$p_login_array['devicetoken'],$p_login_array['deviceid'],$p_login_array['devicetype'],$default_companyid,$p_login_array['country_code']); 

							if(count($result) > 0)
							{
								//Checking the User Status
								$user_status = $result[0]['user_status'];
								$passenger_email = $result[0]['email'];
								$passenger_id = $result[0]['id'];
								$device_id = $result[0]['device_token'];
								$login_status = $result[0]['login_status'];
								if($user_status == 'D' || $user_status == 'T' )
								{
									$message = array("message" => __('passenger_blocked'),"status"=> 3);
								}
								else if($user_status == 'I')
								{
									$detail = array("email"=>$passenger_email,"phone"=>$p_login_array['phone'],"passenger_id"=>$passenger_id);
									$message = array("message" => __('account_not_activated'),"detail"=>$detail,"status"=> -2);
								}
								else
								{
									$device_token=isset($p_login_array['devicetoken'])?$p_login_array['devicetoken']:'';
									$device_id;
									$update_id = $result[0]['id'];
									
									//$check_personal_date = $api->check_passenger_personal_data($update_id);
									$check_card_data = $api->check_passenger_card_data($update_id);
									//variable to know whether the passenger have credit card
									$credit_card_sts = ($check_card_data == 0) ? 0:SKIP_CREDIT_CARD;
									if(isset($result[0]['name']) && $result[0]['name'] == '')
									{ 
										$detail = array("email"=>$passenger_email,"phone"=>$p_login_array['phone'],"passenger_id"=>$passenger_id);
										$message = array("message" => __('p_personal_data_not_filled'),"status"=> -2,"detail"=>$detail);
									}
									else if($result[0]['skip_credit_card'] !=1 && $check_card_data == 0)
									{
										$detail = array("email"=>$passenger_email,"phone"=>$p_login_array['phone'],"passenger_id"=>$passenger_id);
										$message = array("message" => __('p_card_data_not_filled'),"status"=> -3,"detail"=>$detail);
									}		
									else
									{ 
										if((!empty($result[0]['profile_image'])) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'edit_'.$result[0]['profile_image'])){ 
											$edit_image = URL_BASE.PASS_IMG_IMGPATH.'edit_'.$result[0]['profile_image']; 
										}
										else{ 
											$edit_image = URL_BASE."public/images/edit_image.png";
										} 

										$result[0]['edit_image'] = $edit_image;


										if((!empty($result[0]['profile_image'])) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'thumb_'.$result[0]['profile_image'])){ 
											$profile_image = URL_BASE.PASS_IMG_IMGPATH.'thumb_'.$result[0]['profile_image']; 
										}
										else{ 
											$profile_image = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
										} 
										$total_array = array();
										if(count($result) > 0)
										{
											$total_array['id'] = $result[0]['id'];
											$total_array['name'] = $result[0]['name'];
											$total_array['email'] = $result[0]['email'];
											$total_array['profile_image'] = $profile_image;
											$total_array['country_code'] = $result[0]['country_code'];
											$total_array['phone'] = $result[0]['phone'];
											$total_array['login_from'] = $result[0]['login_from'];
											$total_array['referral_code'] = $result[0]['referral_code'];
											$total_array['referral_code_amount'] = $result[0]['referral_code_amount'];
											//this field is used to check whether the user logged in after forgot the password 0 - not forgot, 1- forgot
											$total_array['forgot_password'] = $result[0]['forgot_password'];
											$total_array['split_fare'] = $result[0]['split_fare'];
											$telltofriend_message = TELL_TO_FRIEND_MESSAGE;//str_replace("#REFDIS#",$ref_discount,$ref_message); 
											$total_array['telltofriend_message'] = $telltofriend_message;
											//Newly Added-13.11.2014
											$total_array['site_currency'] = $this->site_currency;
											$total_array['aboutpage_description'] = $this->app_description;
											$total_array['tell_to_friend_subject'] = __('telltofrien_subject');
											
											$total_array['credit_card_status'] = $credit_card_sts;
											/** function to update forgot_password status as 0 **/
											if($total_array['forgot_password'] == 1) {
												$update_pass_array  = array("forgot_password" => '0'); // Start to Pickup
												$result = $api_ext->update_passengers($update_pass_array,$passenger_id);	
											}
											/***Get Company car model details end***/
											$message = array("message"  => __('succesful_login_flash'),"detail"=>$total_array,"status"=> 1);
										}
									}											
								}
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								//unset($message,$total_array);
								break;												
							}							
							else
							{
								$message = array("message" => __('password_failed'),"status"=> 4);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								//unset($message);
								break;										
							}						
						}					
					}
					else
					{
						$validation_error = $validator->errors('errors');	
						$message = array("message" => __('validation_error'),"detail"=>$validation_error,"status"=>-5);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						//unset($message,$validation_error);
						break;				
					}	
				//unset($validator,$message,$phone_exist,$result,$detail,$profile_image,$edit_image,$check_card_data);
				break;	
			
			case 'passenger_mobile_check':
					if(!empty($mobiledata['phone'])) {
						$getArr = 2;//param to get array of passenger details
						$passDetails = $api->checkpassengerPhonewithConcat($mobiledata['phone'],$default_companyid,$getArr);
						if(count($passDetails) > 0) {
							$login_status = $passDetails[0]['login_status'];
							$creditcard_details = $passDetails[0]['creditcard_details'];							
							if($login_status == 'S') {
								if($creditcard_details == 0) {									
									$message = array("message" =>__('friend_donot_have_creditcard'),"detail"=>$passDetails,"status"=> 3);
								}else{							
									$checkPassTrip = $api->checkSecondPassengerinTrip($passDetails[0]['id']);
									if($checkPassTrip > 0){
										$message = array("message" =>__('friend_in_trip'),"detail"=>$passDetails,"status"=> 3);
									} else {
										$message = array("message" =>__('friend_contact_online'),"detail"=>$passDetails,"status"=> 1);
									}
								}								
							} else {
								$message = array("message" =>__('friend_contact_notin_online'),"detail"=>$passDetails,"status"=> 0);
							}
						} else {
							$message = array("message" =>__('friend_contact_not_found'),"detail"=>$passDetails,"status"=> 2);
							//** SMS Section Starts **//
						}
					} else {
						$message = array("message" => __('invalid_request'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$message_details,$result,$checkPassTrip,$passDetails);
					break;
			
			case 'splitfare_approval':
					$splitfare_approval = $mobiledata;
					$validator = $this->checkApprovalValidation($mobiledata);	
					if($validator->check())
					{					
						if(!empty($mobiledata['friend_id']))
						{
							//function to check a trip is completed while accept the split trip
							$checkTripStatus = $api->checkTripStatus($mobiledata['trip_id']);
							if($checkTripStatus == 1 || $checkTripStatus == 5) {
							
								$message = __('trip_already_completed');
								$message = array("message" => $message,"status"=>2);
							
							}elseif($checkTripStatus == 4){
							
								$message = __('cancelled');
								$message = array("message" => $message,"status"=>2);
								
							} else {
								
								$result = $api->setSplitfareApproval($mobiledata['trip_id'],$mobiledata['friend_id'],$mobiledata['approve_status']);
								if($result == 1)
								{	
									$message = array("message" =>__('approve_status_updated'),"trip_id" => $mobiledata['trip_id'],"status"=> 1);
								}
								else if($result == 2)
								{
									$message = array("message" =>__('approve_status_declined'),"trip_id" => $mobiledata['trip_id'],"status"=> 3);
								}
								else
								{
									$message = array("message" => __('invalid_approve'),"status"=>2);
								}
							}
						}
						else
						{
							$message = array("message" => __('invalid_user'),"status"=>-1);	
						}
					}
					else
					{
						$message = array("message" => __('validation_error'),"status"=>0);							
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$validator,$result,$checkTripStatus);
			break;
			
			case 'passenger_profile':			
					if($mobiledata['userid'] != null)
					{
						$result = $api->passenger_profile($mobiledata['userid'],'A');
						if(count($result) >0)
						{
							$passenger_image = $result[0]['profile_image'];							
							/*************************** Passenger Image ************************************/
							if((!empty($passenger_image)) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'thumb_'.$passenger_image))
							{ 
								$profile_image = URL_BASE.PASS_IMG_IMGPATH.$passenger_image; 
							}
							else
							{ 
								$profile_image = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
							}
							$result[0]['profile_image'] = 	$profile_image;
							$message = array("message" => __('success'),"detail"=>$result,"status"=>1);	
						}
						else
						{
							$message = array("message" => __('invalid_user'),"status"=>0);	
						}
						
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$validator,$result,$profile_image);
					break;			
			
			case 'edit_passenger_profile':
					$p_personal_array = $mobiledata;
					if(count($p_personal_array)>0)
					{
						if($p_personal_array['email'] != null)
						{
							$p_email = urldecode($p_personal_array['email']);
							$country_code = $p_personal_array['country_code'];
							$p_phone = urldecode($p_personal_array['phone']);
							$passenger_id = $p_personal_array['passenger_id'];
							$password = urldecode($p_personal_array['password']);
							$validator = $this->edit_passenger_profile_validation($p_personal_array);
							
							if($validator->check())
							{															
							   $email_exist = $api->edit_check_email_passengers($p_email,$passenger_id,$default_companyid);
							   $phone_exist = $api->edit_check_phone_passengers($p_phone,$passenger_id,$default_companyid,$country_code);
							   
								if($email_exist > 0)
								{
									$message = array("message" => __('email_exists'),"status"=> 3);
								}
								else if($phone_exist > 0)
								{
									$message = array("message" => __('phone_exists'),"status"=> 2);
								}
								else
								{	
									if($p_personal_array['profile_image'] != "")
									{							
										/* Profile Update */
										$imgdata = base64_decode($p_personal_array['profile_image']);
										$f = finfo_open();
										$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
										$mime_type = explode('/',$mime_type);
										$mime_type = $mime_type[1];
										$img = imagecreatefromstring($imgdata); 
										if($img != false)
										{                   
											// get prev image
											$result = $api->passenger_profile($p_personal_array['passenger_id'],'A');
											if(count($result) >0)
											{
												$profile_picture = $result[0]['profile_image'];
												if($profile_picture != "")
												{
													$main_image_path = $_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.$profile_picture;
													$thumb_image_path = $_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'thumb_'.$profile_picture;
													if(file_exists($main_image_path) &&($profile_picture != ""))
													{
													unlink($main_image_path);
													}
													if(file_exists($thumb_image_path) &&($profile_picture != ""))
													{
													unlink($thumb_image_path);
													}
												}
											}										
											$image_name = uniqid().'.'.$mime_type;
											$thumb_image_name = 'thumb_'.$image_name;
											$image_url = DOCROOT.PASS_IMG_IMGPATH.'/'.$image_name;				
											$image_path = DOCROOT.PASS_IMG_IMGPATH.$image_name;  
											imagejpeg($img,$image_url);
											imagedestroy($img);
											chmod($image_path,0777);
											$d_image = Image::factory($image_path);
											$path11=DOCROOT.PASS_IMG_IMGPATH;
											Commonfunction::imageoriginalsize($d_image,$path11,$image_name,90);
											$path12=$thumb_image_name;
											Commonfunction::imageoriginalsize($d_image,$path11,$thumb_image_name,90);
											if($password != "")
											{
												$update_array = array(								
												"salutation"=>urldecode($p_personal_array['salutation']),
												"name" => urldecode($p_personal_array['firstname']),
												"lastname" => urldecode($p_personal_array['lastname']),
												"email" => $p_email,
												"country_code" => $country_code,
												"phone" => $p_phone,
												"password" => md5($password),
												"profile_image" => $image_name);
											}
											else
											{
												$update_array = array(								
																	"salutation"=>urldecode($p_personal_array['salutation']),
																	"name" => urldecode($p_personal_array['firstname']),
																	"lastname" => urldecode($p_personal_array['lastname']),
																	"email" => $p_email,
																	"country_code" => $country_code,
																	"phone" => $p_phone,
																	"profile_image" => $image_name);										
											}
											
											$message = $api->edit_passenger_personaldata($update_array,$passenger_id,$default_companyid);
										}
										else
										{
											$message = array("message" => __('image_not_upload'),"status"=>4);								
										}
										
									}
									else
									{
										if($password != "")
										{
											$update_array = array(								
											"salutation"=>urldecode($p_personal_array['salutation']),
											"name" => urldecode($p_personal_array['firstname']),
											"lastname" => urldecode($p_personal_array['lastname']),
											"email" => $p_email,
											"country_code" => $country_code,
											"phone" => $p_phone,
											"password" => md5($password));
											
										}
										else
										{
											$update_array = array(								
															"salutation"=>urldecode($p_personal_array['salutation']),
															"name" => urldecode($p_personal_array['firstname']),
															"lastname" => urldecode($p_personal_array['lastname']),
															"email" => $p_email,
															"country_code" => $country_code,
															"phone" => $p_phone);										
										}
										$message = $api->edit_passenger_personaldata($update_array,$passenger_id,$default_companyid);
										
									}
									/*****************************************/												
									if($message == 0)
									{
										$passenger_details = $api->passenger_profile($p_personal_array['passenger_id'],'A');
										if((!empty($passenger_details[0]['profile_image'])) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'thumb_'.$passenger_details[0]['profile_image'])){ 
											$profile_image = URL_BASE.PASS_IMG_IMGPATH.'thumb_'.$passenger_details[0]['profile_image']; 
										} else { 
											$profile_image = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
										}
										$message = array("message" => __('personal_updated'),"profile_image"=>$profile_image,"status"=>1);	
									}	
									if($message == -1)
									{
										$message = array("message" => __('try_again'),"status"=>-1);	
									}											
								}
							}
							else
							{							
								$validation_error = $validator->errors('errors');	
								$message = array("message" => __('validation_error'),"detail"=>$validation_error,"status"=>-3);	
							}
						}
						else
						{
							$message = array("message" => __('invalid_email'),"status"=>-4);	
						}
					}
					else
					{
						$message = array("message" => __('try_again'),"status"=>-5);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$validation_error,$result,$profile_image,$update_array,$profile_picture,$main_image_path,$thumb_image_path,$validator,$phone_exist,$email_exist,$mime_type);
					break;

			case 'add_card_details':
			$p_card_array= $mobiledata;
			$creditcard_no = $p_card_array['creditcard_no'];
			$creditcard_cvv = $p_card_array['creditcard_cvv'];
			$expdatemonth = $p_card_array['expdatemonth'];
			$expdateyear = $p_card_array['expdateyear'];			
			$passenger_id = $p_card_array['passenger_id'];
			$default = $p_card_array['default'];
			$card_validation = $this->passenger_card_validation($p_card_array);
			if($card_validation->check())
			{			
				$authorize_status =$api->isVAlidCreditCard($creditcard_no,"",true);
				if($authorize_status == 0)
				{
					$message = array("message" => __('invalid_card'),"status"=> 2);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					exit;
				}
				$card_exist = $api->check_card_exist($creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$passenger_id);
				
				if($card_exist > 0)
				{
					$message = array("message" => __('card_exist'),"status"=> 3);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					exit;
				}
				//Credit Card Pre authorization section goes here
				//preauthorization with amount "0"(Zero)
				$preAuthorizeAmount = PRE_AUTHORIZATION_REG_AMOUNT;
				
				list($returncode,$paymentResult,$fcardtype,$preAuthorizeAmount) = $api->creditcardPreAuthorization($passenger_id,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$preAuthorizeAmount);
				
				if($returncode==0)
				{
					//preauthorization with amount "1"
					$preAuthorizeAmount = PRE_AUTHORIZATION_RETRY_REG_AMOUNT;
					list($returncode,$paymentResult,$fcardtype,$preAuthorizeAmount)= $api->creditcardPreAuthorization($passenger_id,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$preAuthorizeAmount);
				}
				
				if($returncode != 0)
				{
					$result = $api->add_passenger_carddata($p_card_array,'',$paymentResult,$preAuthorizeAmount,$fcardtype);
					if($result) {
						$void_transaction=$api->voidTransactionAfterPreAuthorize($result,$paymentResult);
					}
				}
				else
				{
					$message=array("message"=> $paymentResult,"status"=>3);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					exit;
				}
				if($result > 0)
				{
					$message = array("message" => __('card_success'),"status"=>1);		
				}
				else
				{
					$message = array("message" => __('try_again'),"status"=>-1);	
				}
			
			}
			else
			{							
				$validation_error = $card_validation->errors('errors');	
				$message = array("message" => __('validation_error'),"detail"=>$validation_error,"status"=>-3);		
			}
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			//unset($message,$validation_error,$result,$void_transaction,$card_validation,$returncode,$paymentResult,$fcardtype,$preAuthorizeAmount,$card_exist,$authorize_status,$card_exist);
			break;

			case 'edit_card_details':
			$p_card_array= $mobiledata;
			$passenger_cardid = $p_card_array['passenger_cardid'];
			$passenger_id = $p_card_array['passenger_id'];
			if($passenger_cardid != null)
			{
				$creditcard_no = $p_card_array['creditcard_no'];
				$creditcard_cvv = $p_card_array['creditcard_cvv'];
				$expdatemonth = $p_card_array['expdatemonth'];
				$expdateyear = $p_card_array['expdateyear'];
				$default = $p_card_array['default'];
				$card_validation = $this->edit_passenger_card_validation($p_card_array);
				
				if($card_validation->check())
				{
					$authorize_status =$api->isVAlidCreditCard($creditcard_no,"",true);
					if($authorize_status == 0)
					{
						$message = array("message" => __('invalid_card'),"status"=> 2);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						exit;
					}
					$card_exist = $api->edit_check_card_exist($passenger_cardid,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$passenger_id,$default);
					
					if($card_exist == 1)
					{
						$message = array("message" => __('card_exist'),"status"=> 3);
					}
					else if($card_exist == 2)
					{
						$message = array("message" => __('one_card_exist'),"status"=> 2);
					}
					else
					{
						//Credit Card Pre authorization section goes here
						//preauthorization with amount "0"(Zero)
						$preAuthorizeAmount = PRE_AUTHORIZATION_REG_AMOUNT;
						list($returncode,$paymentResult,$fcardtype,$preAuthorizeAmount) = $api->creditcardPreAuthorization($passenger_id,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$preAuthorizeAmount);
						if($returncode==0)
						{
							//preauthorization with amount "1"
							$preAuthorizeAmount = PRE_AUTHORIZATION_RETRY_REG_AMOUNT;
							list($returncode,$paymentResult,$fcardtype,$preAuthorizeAmount)= $api->creditcardPreAuthorization($passenger_id,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$preAuthorizeAmount);
						}
						
						if($returncode != 0)
						{
							$result = $api->edit_passenger_carddata($p_card_array,$paymentResult,$preAuthorizeAmount,$fcardtype);
							if($result == 0) {
								$void_transaction=$api->voidTransactionAfterPreAuthorize($passenger_cardid,$paymentResult);
							}
						}
						else
						{
							$message=array("message"=> $paymentResult,"status"=>3);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							exit;
						}
						if($result == 0)
						{
							$message = array("message" => __('edit_card_success'),"status"=>1);		
						}
						else
						{
							$message = array("message" => __('try_again'),"status"=>-1);	
						}
					}
				
				}
				else
				{							
					$validation_error = $card_validation->errors('errors');	
					$message = array("message" => __('validation_error'),"detail"=>$validation_error,"status"=>-3);		
				}
			}
			else
			{
				$message = array("message" => __('try_again'),"status"=>1);
			}
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			//unset($message,$validation_error,$result,$card_validation,$authorize_status,$card_exist,$returncode,$paymentResult,$fcardtype,$preAuthorizeAmount,$void_transaction);
			break;
			
			case 'chg_password_passenger':
			$p_chg_pass_array = $mobiledata;			
			if(!empty($p_chg_pass_array))
			{
					if($p_chg_pass_array['id'] != null)
					{
						$validator = $this->chg_password_passenger_validation($p_chg_pass_array);						
						if($validator->check())
						{
							$message = $api->chg_password_passenger($p_chg_pass_array,$default_companyid,'P');								
							switch($message){
								case -1 :
									$message = array("message" => __('confirm_new_same'),"status"=>-1);	
									break;
								case -2 :
									$message = array("message" => __('old_pass_incorrect'),"status"=>-2);
									break;
								case -3 :
									$message = array("message" => __('invalid_user'),"status"=>-3);
									break;
								case 1 :
									$message = array("message" => __('password_changed'),"status"=>1);	
									break;
								case -4 :
									$message = array("message" => __('old_new_pass_same'),"status"=>-4);	
									break;
								}
						}
						else
						{							
							$validation_error = $validator->errors('errors');	
							$message = array("message" => $validation_error,"status"=>-3);							
						}
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>0);	
					}
			}
			else
			{
					$message = array("message" => __('invalid_request'),"status"=>-6);	
			}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$validator);
					break;

			case 'add_favourite':
				$add_fav_array = $mobiledata;
				$validator = $this->favourite_validation($add_fav_array);				
				if($validator->check())
				{
					$passenger_id= $add_fav_array['passenger_id'];					
					$fav_comments = urldecode($add_fav_array['fav_comments']);
					$p_favourite_place = urldecode($add_fav_array['p_favourite_place']);
					$p_fav_latitude = $add_fav_array['p_fav_latitude'];
					$p_fav_longtitute = $add_fav_array['p_fav_longtitute'];
					$d_favourite_place = (isset($add_fav_array['d_favourite_place'])) ? urldecode($add_fav_array['d_favourite_place']) : '';
					$d_fav_latitude = (isset($add_fav_array['d_fav_latitude'])) ? $add_fav_array['d_fav_latitude'] : '';
					$d_fav_longtitute = (isset($add_fav_array['d_fav_longtitute'])) ? $add_fav_array['d_fav_longtitute'] : '';
					$p_fav_locationtype = urldecode($add_fav_array['p_fav_locationtype']);
					$notes = isset($add_fav_array['notes']) ? urldecode($add_fav_array['notes']) :"";
					
					$check_fav_place = $api->check_fav_place($passenger_id,$p_favourite_place,$d_favourite_place,$p_fav_locationtype);
					
					if($check_fav_place==0)
					{
						//Set the Favourite Trips
						$image_name = uniqid().'.png';
						$status = $api->save_favourite($passenger_id,$p_favourite_place,$p_fav_latitude,$p_fav_longtitute,$d_favourite_place,$d_fav_latitude,$d_fav_longtitute,$fav_comments,$notes,$p_fav_locationtype,$image_name);
						if($status)					
						{
							// Create directory if it does not exist							
							if(!is_dir(DOCROOT.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $passenger_id ."/")) { 
								mkdir(DOCROOT.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $passenger_id ."/",0777);
							}
							//Map image creation
							include_once MODPATH."/email/vendor/polyline_encoder/encoder.php";
							$polylineEncoder = new PolylineEncoder();
							$polylineEncoder->addPoint($p_fav_latitude,$p_fav_longtitute);
							
							$marker_end = 0;
							if($d_fav_latitude != 0 && $d_fav_longtitute != 0){
								$polylineEncoder->addPoint($d_fav_latitude,$d_fav_longtitute);
								$marker_end = $d_fav_latitude.','.$d_fav_longtitute;
							}
							$encodedString = $polylineEncoder->encodedString();
							$marker_start = $p_fav_latitude.','.$p_fav_longtitute;
							$startMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'startMarker.png';
							$endMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'endMarker.png';
							
							if($marker_end != 0) {
								$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&markers=icon:$endMarker%7C$marker_end&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
							} else {
								$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
							}
														
							if(isset($mapurl) && $mapurl != "") {
								$file_path = DOCROOT.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $passenger_id ."/".$image_name;
								file_put_contents($file_path,@file_get_contents($mapurl));
							}							
							$message = array("message" => __('mark_fav'),"detail"=>"","status"=>1);
						}
						else
						{
							$p_favourite_id = $check_fav_place['0']['p_favourite_id'];
							$message = array("message" => __('try_again'),"status"=>0);	
						}	
					}else if($check_fav_place==-1)
					{
						$message = array("message" => __('fav_already_exist_type'),"status"=>3);
					}
					else
					{
						$message = array("message" => __('fav_already_exist'),"status"=>2);
					}					
				}
				else
				{
						$validation_error = $validator->errors('errors');	
						$message = array("message" => __('validation_error'),"status"=>-3);								
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$validator,$check_fav_place,$status,$polylineEncoder,$encodedString,$marker_start,$startMarker,$endMarker,$mapurl,$file_path);
				break;
			
			case 'get_favourite_list':		
				if(count($mobiledata) > 0)
				{
					$passenger_id = $mobiledata['passenger_id'];
					$favourite_list = $api->get_favourite_list($passenger_id);
					if(count($favourite_list)>0)
					{
						foreach($favourite_list as $key=>$val){
							$mapurl = '';
							$fav_image = isset($val['fav_image']) ? $val['fav_image'] : 'fav_'.$val['p_favourite_id'].'.png';
							if(file_exists(DOCROOT.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $passenger_id ."/".$fav_image)) {
								$mapurl = URL_BASE.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $passenger_id ."/".$fav_image;
							}
							$favourite_list[$key]['map_image'] = $mapurl;
						}
						
						$message = array("message" => __('success'),"detail"=>$favourite_list,"status"=>1);	
					}
					else
					{
						$message = array("message" => __('no_favourite_trips'),"status"=>0);	
					}				
				}
				else
				{
					$message = array("message" => __('no_favourite_trips'),"status"=>-1);								
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$favourite_list,$mapurl,$passenger_id);
				break;		
			
			case 'get_favourite_details':
				$p_fav_array = $mobiledata;
				if($p_fav_array['p_favourite_id'] != null)
				{
					$favourite_details = $api->get_favourite_details($p_fav_array['p_favourite_id']);
					$message = array("message" => $favourite_details,"status"=>1);	
				}
				else
				{
					$message = array("message" => __('no_favourite'),"status"=>-1);								
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$favourite_details,$p_fav_array);
				break;
				
			case 'delete_favourite':
				$p_fav_array = $mobiledata;
				if($p_fav_array['p_favourite_id'] != null && $p_fav_array['passenger_id'] != null)
				{
					$favourite_details = $api->delete_favourite($p_fav_array['p_favourite_id'],$p_fav_array['passenger_id']);
					
					if($favourite_details) {
						if(file_exists(DOCROOT.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $p_fav_array['passenger_id'] ."/fav_".$p_fav_array['p_favourite_id'].".png")) {
							unlink(DOCROOT.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $p_fav_array['passenger_id'] ."/fav_".$p_fav_array['p_favourite_id'].".png");
						}
						$message = array("message" => __('favourite_deleted'),"status"=>1);	
					} else {
						$message = array("message" => __('no_favourite'),"status"=>-1);
					}
				}
				else
				{
					$message = array("message" => __('no_favourite'),"status"=>-1);								
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$favourite_details,$p_fav_array);
			break;	
		
			case 'edit_favourite':
				$edit_fav_array = $mobiledata;
				$validator = $this->edit_favourite_validation($edit_fav_array);
				if($validator->check())
				{
					$favourite_id= $edit_fav_array['p_favourite_id'];
					$fav_comments = $edit_fav_array['fav_comments'];
					$passenger_id  = $edit_fav_array['passenger_id'];
					$p_favourite_place = urldecode($edit_fav_array['p_favourite_place']);
					$p_fav_latitude = $edit_fav_array['p_fav_latitude'];
					$p_fav_longtitute = $edit_fav_array['p_fav_longtitute'];
					$d_favourite_place = (isset($edit_fav_array['d_favourite_place'])) ? urldecode($edit_fav_array['d_favourite_place']) : '';
					$d_fav_latitude = (isset($edit_fav_array['d_fav_latitude'])) ? $edit_fav_array['d_fav_latitude'] : '';
					$d_fav_longtitute = (isset($edit_fav_array['d_fav_longtitute'])) ? $edit_fav_array['d_fav_longtitute'] : '';
					
					$p_fav_locationtype = urldecode($edit_fav_array['p_fav_locationtype']);
					$notes = isset($edit_fav_array['notes']) ? urldecode($edit_fav_array['notes']):"";
					//Set the Favourite Trips
					$check_fav_place = $api->check_fav_editplace($passenger_id,$p_favourite_place,$d_favourite_place,$favourite_id,$p_fav_locationtype);

					if($check_fav_place==0)
					{ 
						$check_fav_place_exist = $api->check_fav_editplacecheck($passenger_id,$p_favourite_place,$d_favourite_place,$favourite_id,$p_fav_locationtype);
						if($check_fav_place_exist==0)
						{
							$status = $api->edit_favourite($favourite_id,$p_favourite_place,$p_fav_latitude,$p_fav_longtitute,$d_favourite_place,$d_fav_latitude,$d_fav_longtitute,$fav_comments,$notes,$p_fav_locationtype);
							if($status)					
							{
								$image_name = uniqid().'.png';
								// Create directory if it does not exist							
								if(!is_dir(DOCROOT.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $passenger_id ."/")) { 
									mkdir(DOCROOT.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $passenger_id ."/",0777);
								}
															
								//Map image creation
								include_once MODPATH."/email/vendor/polyline_encoder/encoder.php";
								$polylineEncoder = new PolylineEncoder();
								$polylineEncoder->addPoint($p_fav_latitude,$p_fav_longtitute);
								
								$marker_end = 0;
								if($d_fav_latitude != 0 && $d_fav_longtitute != 0){
									$polylineEncoder->addPoint($d_fav_latitude,$d_fav_longtitute);
									$marker_end = $d_fav_latitude.','.$d_fav_longtitute;
								}
								$encodedString = $polylineEncoder->encodedString();
								$marker_start = $p_fav_latitude.','.$p_fav_longtitute;
								$startMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'startMarker.png';
								$endMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'endMarker.png';
								
								if($marker_end != 0) {
									$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&markers=icon:$endMarker%7C$marker_end&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
								} else {
									$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
								}
															
								if(isset($mapurl) && $mapurl != "") {
									$file_path = DOCROOT.MOBILE_FAV_LOC_MAP_IMG_PATH."passenger_". $passenger_id .'/'.$image_name;
									file_put_contents($file_path,@file_get_contents($mapurl));
									
									$update_image = $api->update_favourite_image($favourite_id,$image_name);
								}				
								$message = array("message" => __('edit_mark_fav'),"detail"=>"","status"=>1);
							}
							else
							{
								$message = array("message" => __('no_chage_made'),"status"=>0);	
							}

						}else{
							$message = array("message" => __('fav_already_exist'),"status"=>2);

						}	
					}else if($check_fav_place==-1){
						$message = array("message" => __('no_data'),"status"=>-3);

					}
					else
					{
						$message = array("message" => __('fav_already_exist_type'),"status"=>3);
					}										
				}
				else
				{
						$validation_error = $validator->errors('errors');
						$message = array("message" => __('validation_error'),"status"=>-3);	
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$validator,$validation_error,$check_fav_place_exist,$status,$check_fav_place);
				break;
			case 'check_passenger_trip':
				$passenger_id = (isset($mobiledata['passenger_id'])) ? $mobiledata['passenger_id'] : '';
				if(!empty($passenger_id)) {
					$passengerCompany = $api->get_passenger_company_id($passenger_id);
					$company_id = ($passengerCompany != 0) ? $passengerCompany : $default_companyid;
					$passengerInTrip = $api->check_passenger_in_trip($passenger_id,$company_id);
					if($passengerInTrip > 0) {
						$message = array("message" => __('passenger_in_journey'),"status" => 1);
					} else {
						$message = array("message" => __('invalid_trip'),"status" => 2);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status"=>0);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$passengerInTrip,$company_id,$passengerCompany);
			break;
			//Append the Additional Fields while sending...
			case 'savebooking':
					$search_array = $mobiledata;
					$validator = $this->search_validation($search_array);
					$passenger_id = $search_array['passenger_id'];
					$promo_code = isset($search_array['promo_code'])?$search_array['promo_code']:'';
					$referral_code = isset($search_array['referral_code'])?$search_array['referral_code']:'';
					$passenger_payment_option = isset($search_array['passenger_payment_option'])?$search_array['passenger_payment_option']:0;
					$check_passenger = $api->check_passengerlogin($passenger_id);
					if($check_passenger == 0){
						$message = array("message" => __('passenger_blocked'),"status" => -10);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						break;
					}
					if($validator->check())
					{					
						if($promo_code != "")
						{
							$check_promo = $api->checkpromocode($promo_code,$passenger_id,$default_companyid);
							if($check_promo == 0)
							{
								$message = array("message" => __('invalid_promocode'),"status" => 3);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								//unset($message);
								break;
							}
							else if($check_promo == 3)
							{
								$message = array("message" => __('promo_code_startdate'),"status" => 3);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								//unset($message);
								break;
							}
							else if($check_promo == 4)
							{
								$message = array("message" => __('promo_code_expired'),"status" => 3);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								//unset($message);
								break;
							}
							else if($check_promo == 2)
							{
								$message = array("message" => __('promo_code_limit_exceed'),"status" => 3);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								//unset($message);
								break;
							}
							else
							{
								$formvalues['promo_code'] = $promo_code;
							}
						}
						
						if($search_array['latitude'] !='0' && $search_array['longitude'] !='0')
						{
							$add_model = Model::factory('add');
							$find_model = Model::factory(FIND);
							$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
							$latitude = $search_array['latitude'];
							$longitude = $search_array['longitude'];
							$miles = DEFAULTMILE;
							$no_passengers = "";
							$pickup_time = $search_array['pickup_time'];
							
							$pickupplace = urldecode($search_array['pickupplace']);
							$dropplace = urldecode($search_array['dropplace']);
							$drop_latitude = $search_array['drop_latitude'];
							$drop_longitude = $search_array['drop_longitude'];
							
							$taxi_fare_km = '';
							$motor_company = '1';
							$motor_model = $search_array['motor_model'];
							$maximum_luggage = "";
							$cityname = $search_array['cityname'];
							$sub_logid = $search_array['sub_logid'];
							$now_after = $search_array['now_after'];
							$passenger_id = $search_array['passenger_id'];
							$notes = isset($search_array['notes']) ? $search_array['notes'] : '';
							$passenger_app_version = isset($search_array['passenger_app_version']) ? $search_array['passenger_app_version'] : '';
							$fav_driver_booking_type = isset($search_array['fav_driver_booking_type']) ? $search_array['fav_driver_booking_type'] : 0;
							$approx_trip_fare = isset($search_array['approx_trip_fare']) ? $search_array['approx_trip_fare'] : 0;
							$unit = UNIT; // 0 - KM, 1 - Miles
							$service_type="";
							$city_id  = $api->get_city_id($cityname);	
							$passengerCompany = (!empty($passenger_id)) ? $api->get_passenger_company_id($passenger_id) : 0;
							$company_id = ($passengerCompany != 0) ? $passengerCompany : $default_companyid;
							$passengerInTrip = $api->check_passenger_in_trip($passenger_id,$company_id);
							if($passengerInTrip > 0 && $now_after != 1) {
								$errorMessage = ($passengerInTrip == 1) ? __('passenger_in_journey') : __('your_last_payment_pending');
								$message = array("message" => $errorMessage,"status" => 3);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								break;
							}
							//** Function to check passenger's credit card has expired before booking **//
							$gateway_details = $this->commonmodel->gateway_details();//function to get list of payments used ( cash, creditcard, New card )
							
							# check for credit card payment option [split fare]
							if(!empty($mobiledata['friend_id2']) || !empty($mobiledata['friend_id3']) || !empty($mobiledata['friend_id4'])){
								$credit = 0;
								foreach($gateway_details as  $arr){
									if($arr['_id'] == 2)
										$credit++;			
								}
								if($credit == 0){
									$message = array("message" => __('creditcard_disabled_admin'),"status" => -6);
									$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
									break;
								}
							}
							$payoptArr = array();
							foreach($gateway_details as $key=>$valArr){
								$payoptArr[] = $valArr['pay_mod_id'];
							}
							//function to check passenger has credit card
							$card_type = '';
							$paymentResult = '';
							$default = 'yes';
							$returncode = 1;
							$pre_authorize_amount = 0;
							$passCardDetails = $api->get_creadit_card_details($passenger_id, $card_type, $default);
							$approx_trip_fare = round($approx_trip_fare,2);
							if($approx_trip_fare != 0) {
								$check_wallet_amount = $api->checkWalletAmount($passenger_id,$approx_trip_fare);
								if($check_wallet_amount > 0) {
									$pre_authorize_amount = 0;
								} else {
									if(count($passCardDetails) > 0 && $approx_trip_fare != 0) {
										$creditcard_no = encrypt_decrypt('decrypt',$passCardDetails[0]['creditcard_no']);
										$creditcard_cvv = $passCardDetails[0]['creditcard_cvv'];
										$expdatemonth = $passCardDetails[0]['expdatemonth'];
										$expdateyear = $passCardDetails[0]['expdateyear'];
										$pre_authorize_amount = $approx_trip_fare;
										// Verify wether the card is valid or not
										list($returncode,$paymentResult,$fcardtype,$preAuthorizeAmount) = $api->creditcardPreAuthorization($passenger_id,$creditcard_no,$creditcard_cvv,$expdatemonth,$expdateyear,$pre_authorize_amount);
									}
								}
							}

							if($returncode != 0) {
								$favdriversArr = array();
								if($fav_driver_booking_type != 0) {
									$favDrivers = $api->getFavDrivers($passenger_id);
									$favdriversArr = (!empty($favDrivers)) ? explode(",",$favDrivers) : array();
								}
								
								$pickupTimezone = $api->getpickupTimezone($latitude,$longitude);
								$currentTime = convert_timezone('now',$pickupTimezone);
								$driver_details = $find_model->getNearestDrivers($motor_model,$latitude,$longitude,$currentTime,$company_id,$miles,$unit);
								$nearest_driver='';
								$a=1;
								$temp='10000';
								$prev_min_distance='10000~0';
								$taxi_id='';
								$temp_driver=0;
								$nearest_key=0;
								$prev_key=0;
								$driver_list="";
								$available_drivers ="";
								$avail_nearest_driver = array();
								$fav_driver_list = array();
								$total_count = count($driver_details);
								$company_contact_no='';
								if(COMPANY_CID != 0)
								{
									$company_contact_no=COMPANY_CONTACT_PHONE_NUMBER;
								}
								$no_vehicle_msg=__('no_vehicle_msg').$company_contact_no;
								$notification_time = $this->notification_time;	
								if($notification_time != 0 ){ $timeoutseconds = $notification_time;}else{$timeoutseconds = 15;}
								//Form Values//
								$formvalues = Arr::extract($mobiledata, array('pickupplace','dropplace','pickup_time','driver_id','passenger_id','roundtrip','passenger_phone','cityname','distance_away','sub_logid','drop_latitude','drop_longitude','promo_code','now_after','motor_model','friend_id1','friend_percentage1','friend_id2','friend_percentage2','friend_id3','friend_percentage3','friend_id4','friend_percentage4','friend_percentage_amt1','friend_percentage_amt2','friend_percentage_amt3','friend_percentage_amt4','approx_trip_fare','passenger_payment_option'));
								$credit_card_sts = SKIP_CREDIT_CARD; 
								if($total_count > 0)
								{									
										$driver_id = isset($driver_details[0]['driver_id'])?$driver_details[0]['driver_id']:"";
										$taxi_id = isset($driver_details[0]['taxi_id'])?$driver_details[0]['taxi_id']:"";
										$totalrating = 0;
										foreach($driver_details as $key => $value)
										{
											$updatetime_difference = $value['updatetime_difference'];
											//Exclude the drivers who has not logged in and not update the status last specified seconds
											if($updatetime_difference <= LOCATIONUPDATESECONDS)
											{
												if(count($favdriversArr) > 0 && in_array($value['driver_id'], $favdriversArr)) {
													$fav_driver_list[] = $value['driver_id'];
												} else {
													$avail_nearest_driver[] = $value['driver_id'];
												}
											}
										}
										
										if($fav_driver_booking_type == 1 && count($fav_driver_list) == 0) {
											$message = array("message" => __('fav_driver_not_available'),"status" => 4);
											$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);exit;
										}

										if(count($fav_driver_list) > 0) {
											$avail_nearest_driver = $fav_driver_list;
										}
	
										/*********************************************Save booking ***************************************/

										$formvalues['taxi_id']=$taxi_id;
										$formvalues['pickup_latitude']=$search_array['latitude'];
										$formvalues['pickup_longitude']=$search_array['longitude'];
										$formvalues['approx_distance']=isset($search_array['approx_distance']) ? $search_array['approx_distance'] : '';
										$formvalues['approx_duration']=isset($search_array['approx_duration']) ? $search_array['approx_duration'] : '';
										$formvalues['pickup_longitude']=$search_array['longitude'];
										$formvalues['driver_id'] =$driver_id;
										$formvalues['notes'] = $notes;
										$formvalues['approx_fare'] =$approx_trip_fare;
										$formvalues['passenger_app_version'] = $passenger_app_version;
										$formvalues['pre_transaction_id']=$paymentResult;
										$formvalues['pre_transaction_amount']=$pre_authorize_amount;
										$formvalues['passenger_payment_option'] = $passenger_payment_option;
										$formvalues['currentTime'] = $currentTime;
										$formvalues['pickupTimezone'] = $pickupTimezone;
										$result = $api->savebooking($formvalues,$company_id);
										if(count($avail_nearest_driver)>0){
											$nearest_driver=$avail_nearest_driver[0];
										}
										
										$totalNoofDrivers = (count($avail_nearest_driver) < 5) ? count($avail_nearest_driver) : 5;
										$total_request_time = ($totalNoofDrivers * $notification_time) + 20;
										$total_request_time = (count($avail_nearest_driver) < 5) ? $total_request_time : $this->continuous_request_time+20;
										//function to check whether the passenger have wallet amount by this we can give credit card status
										$total_cancelfare = $api->get_passenger_cancel_faredetail($result);
										$passenger_wallet = $api->get_passenger_wallet_amount($passenger_id);
										
										if(count($passenger_wallet) > 0 && $passenger_wallet[0]['wallet_amount'] >= $total_cancelfare) {
											$credit_card_sts = 0;
										}
										
										if(($result > 0) && ($formvalues['now_after'] == 0))
										{
												/***** Insert the druiver details to driver request table ************/
												if(!empty($nearest_driver)) {
													
													if(count($avail_nearest_driver)>0) {
														$available_drivers_Arr = array();
														# check driver limit for trip request
														$limit_driver = $this->continuous_request_time / $this->notification_time;
														$limit_driver = (int)$limit_driver;
														
														foreach($avail_nearest_driver as $key=>$driveridVal){
															
															$driver_has_request = $api->check_driver_has_trip_request($driveridVal,$company_all_currenttimestamp);
															
															# actual driver limit
															$actual_limit = count($available_drivers_Arr);
															if($driver_has_request == 0){
																$available_drivers_Arr[] = $driveridVal;
															}
														}
														
														$available_drivers =  implode(",",$available_drivers_Arr);
														$nearest_driver = (count($available_drivers_Arr) > 0) ? $available_drivers_Arr[0]: '';
													}
												}
												
												$company_det =$api->get_company_id($nearest_driver);
												$company_id = !empty($company_det) ? $company_det[0]['company_id']: 0;
												
												$datas = array();
												# check driver limit for trip request
												$driver_limit = $this->continuous_request_time / $this->notification_time;
												
												$datas['trip_id'] = $tripid = $result;
												$datas['available_drivers'] = $available_drivers;
												$datas['total_drivers'] = $available_drivers;
												$datas['selected_driver'] = $nearest_driver;
												$datas['status'] = 0;
												$datas['trip_type'] = $fav_driver_booking_type;
												$datas['rejected_timeout_drivers'] = '';								
												$datas['company_id'] = $company_id;								
												$datas['driver_limit'] = (int)$driver_limit;
												$datas['actual_limit'] = 0;
												//Inserting to Transaction Table
												$transaction = $api_ext->insert_request_details($datas);
												
												$detail = array("passenger_tripid"=>$result,"notification_time"=>$notification_time,"total_request_time"=>$total_request_time,"credit_card_status"=>$credit_card_sts);
												
												$message = array("message" => __('api_request_confirmed_passenger'),"status" => 1,"detail"=>$detail);
												$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
												exit;	
										}
										else if(($result > 0) && ($formvalues['now_after'] == 1))
										{
											
											/** Later Booking E-mail & SMS Send Start **/
											$passenger_tripid = $result;
											$datas = $this->commonmodel->getPassengerDetails($result);
											$passenger_logid = $datas[0]["passengers_log_id"];
											$pickup_location = $datas[0]["current_location"];
											$drop_location = $datas[0]["drop_location"];
											$pickup_time = $datas[0]["pickup_time"];
											$name = $datas[0]["name"];
											$email = $datas[0]["email"];
											$phone = $datas[0]["country_code"].$datas[0]["phone"];
											$message = "";
											$message .= "Thanks for booking with us, your booking was confirmed. your booking id ".$passenger_logid.". We will contact shortly.<br>";
											$message .= "Pickup Date : ".$pickup_time."<br>";
											$message .= "Pickup Location : ".$pickup_location."<br>";
											$message .= "Drop Location : ".$drop_location;
											$replace_variables=array(
												REPLACE_LOGO => EMAILTEMPLATELOGO,
												REPLACE_SITENAME => $this->app_name,
												REPLACE_USERNAME => $name,
												REPLACE_MESSAGE => $message,
												REPLACE_SITEURL => URL_BASE,
												REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
												REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR
											);
											$message = $this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'laterbooking_cofirm_message.html',$replace_variables);

											$to = $email;
											$from = $this->siteemail;
											$subject = __('later_booking_confirm_mail')." - ".$this->app_name;
											$redirect = "no";
											if(SMTP == 1)
											{
												include($_SERVER['DOCUMENT_ROOT']."/modules/SMTP/smtp.php");
											}
											else
											{
												// To send HTML mail, the Content-type header must be set
												$headers  = 'MIME-Version: 1.0' . "\r\n";
												$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
												// Additional headers
												$headers .= 'From: '.$from.'' . "\r\n";
												$headers .= 'Bcc: '.$to.'' . "\r\n";
												mail($to,$subject,$message,$headers);
											}

											if(SMS == 1)
											{
												$message_details = $this->commonmodel->sms_message_by_title('booking_confirmed_sms');
												if(count($message_details) > 0) {
													$to = $phone;
													$message = (count($message_details)) ? $message_details[0]['sms_description'] : '';
													$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
													$message = str_replace("##booking_key##",$passenger_logid,$message);
													$this->commonmodel->send_sms($to,$message);
												}
											}

											/** Later Booking E-mail & SMS Send End **/
											/***** Insert the druiver details to driver request table ************/
											$detail = array("passenger_tripid"=>$passenger_tripid,"notification_time"=>$notification_time,"total_request_time"=>$total_request_time,"credit_card_status"=>$credit_card_sts);
											$message = array("message" => __('api_request_disapatcher'),"status" => 1,"detail"=>$detail);
											$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
											//unset($message,$detail);
											exit;
										}
										else
										{
											$message = array("message" => __('try_again'),"status"=>2);	
										}
							  }
							  else
							  {
								  if($formvalues['now_after'] == 1) {	
										$formvalues['taxi_id'] = 0;
										$formvalues['pickup_latitude']=$search_array['latitude'];
										$formvalues['pickup_longitude']=$search_array['longitude'];
										$formvalues['approx_distance']=isset($search_array['approx_distance']) ? $search_array['approx_distance'] : '';
										$formvalues['approx_duration']=isset($search_array['approx_duration']) ? $search_array['approx_duration'] : '';
										$formvalues['driver_id'] = 0;
										$formvalues['notes'] =$notes;
										$formvalues['approx_fare'] =$approx_trip_fare;
										$formvalues['pre_transaction_id']=$paymentResult;
										$formvalues['passenger_app_version'] = $passenger_app_version;
										$formvalues['pre_transaction_amount']=$pre_authorize_amount;
										$formvalues['currentTime'] = $currentTime;
										$formvalues['pickupTimezone'] = $pickupTimezone;
									  $result= $api->savebooking($formvalues,$company_id);
									  $detail = array("passenger_tripid"=>$result,"notification_time"=>$notification_time,"total_request_time"=>$notification_time,"credit_card_status"=>$credit_card_sts);
									  $message = array("message" => __('api_request_disapatcher'),"status" => 1,"detail"=>$detail);
									  $mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
									  //unset($message,$detail,$formvalues);
									  exit;
								  } else {
									   if($fav_driver_booking_type == 1) {
										   $message = array("message" => __('fav_driver_not_available'),"status" => 4);
									   } else {
										   if (class_exists('Paymentgateway')) {
												 $paymentresponse = Paymentgateway::payment_gateway_connect('void',$paymentResult);
												 $payment_status=$paymentresponse['payment_status'];
											} else {
												trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
											}
										   $message = array("message" => $no_vehicle_msg,"status" => 3);
									   }
									  $mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
									  //unset($message);
									  exit;
								  }							  
							  }
						  } else {
							  $message=array("message"=>$paymentResult,"status"=>3);
							  $mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							  //unset($message);
							  exit;
						  }
					  }
					  else
					  {
							$message = array("message" => __('lat_not_zero'),"status"=>-4);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							//unset($message);
							exit;	
					  }
					}
					else
					{
						$errors = $validator->errors('errors');	
						$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>-5);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						//unset($message);
						exit;					
					}
					//unset($message,$datas);
					break;

							
					
			case 'driver_arrived':
			$array = $mobiledata;
			$trip_id = $array['trip_id'];
			if($array['trip_id'] != null)
			{
				$check_travelstatus = $api->check_travelstatus($trip_id);
				$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
				if($check_travelstatus == -1)
				{
					$message = array("message" => __('invalid_trip'),"status"=>2);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message);
					break;
				}				
				if($check_travelstatus == 4)
				{
					$message = array("message" => __('trip_cancelled_passenger'), "status"=>-1);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message);
					break;
				}
				if($check_travelstatus != 9)
				{
					$message = array("message" => __('passenger_in_journey'), "status"=>-1);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message);
					break;
				}						
				$get_passenger_log_details = $api->get_passenger_log_detail($trip_id);		
				$driver_id = $get_passenger_log_details[0]->driver_id;
				$passenger_email = $get_passenger_log_details[0]->passenger_email;
				$driver_current_location = $api->get_driver_current_status($driver_id);
				$driver_latitute = $driver_longtitute="";
				if(count($driver_current_location)>0)
				{
					$driver_latitute = $driver_current_location[0]->latitude;
					$driver_longtitute  = $driver_current_location[0]->longitude;
					$driver_status  = $driver_current_location[0]->status;					
				}

				if($driver_status == 'A')
				{
					$message = array("message" => __('already_trip'), "status"=>-1);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					break;					
				}
				/********** Update Driver Status after complete Payments *****************/
				$datas  = array("travel_status" => '3'); // Start to Pickup
				$result = $api_ext->update_passengerlogs($datas,$trip_id);
					
				/*************** Update arrival in driver request table ******************/				
				$datas  = array("status"=>'5','trip_id'=>$trip_id);
				$driver_request_result = $api_ext->update_driver_request_details($datas);		
				
				/**************************** Update status in driver table *********/
				$datas  = array("status"=>'B');
				$datas['driver_id'] = $driver_id;
				$driver_result = $api_ext->update_driver_location($datas);		
				
				/*************************************************************************/				
				/** Send Trip fare details to Passenger ***/
				$p_device_token = $get_passenger_log_details[0]->passenger_device_token;
				$device_type = $get_passenger_log_details[0]->passenger_device_type;
				$passenger_id = $get_passenger_log_details[0]->passengers_id;
				$pushmessage = array("message"=>__('passenger_on_board'),"trip_id"=>$trip_id,"driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute,"status"=>2);
				if(SMS == 1)
				{
					$message_details = $this->commonmodel->sms_message_by_title('driver_arrived');
					if(count($message_details) > 0) {
						$to = $this->commonmodel->getuserphone('P',$passenger_email);
						$message = $message_details[0]['sms_description'];
						$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
						$this->commonmodel->send_sms($to,$message);
					}
				}
				$message = array("message" => __('driver_arrival_send'),"status"=>1);					
			}
			else
			{
				$message = array("message" => __('invalid_trip'),"status"=>-1);	
			}
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);	
			//unset($message);
			break;
												
			case 'user_logout':		
			$driver_logout_array = $mobiledata;
			$driver_id = $mobiledata['driver_id'];		
			$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);	
					if($driver_id != null)
					{
						$shiftupdate_id = $driver_logout_array['shiftupdate_id'];
						$driver_model = Model::factory('driver');
						$update_id = $driver_id;							
						$check_result = $api->check_driver_companydetails($driver_id,$default_companyid);
						if($check_result == 0)	
						{
							$message = array("message" => __('invalid_user'),"status"=>-1);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							//unset($message);
							exit;
						}
					
						$driver_current_status = $api->get_driver_current_status($update_id);
						if(count($driver_current_status) > 0)
						{
								$get_driver_log_details = $api->get_driver_log_details($update_id,$default_companyid);
								$driver_trip_count = count($get_driver_log_details);//exit;
								if($driver_trip_count == 0)
								{
									$datas  = array("login_from"=>"","login_status"=>"N",
															"device_id" => "","device_token" => "",
															"device_type" => "","notification_setting"=>"0",
															"notification_status"=>"0");
									$login_status_update = $api_ext->update_driver_people($datas,$update_id);
									/*** Update in Driver table **/
									$driver_reply = $driver_model->update_driver_shift_status($update_id,'0');
									/** Update in driver shift history table **/
									$shiftupdate_arrary  = array("shift_end" => $this->currentdate);
									$shiftupdateid = $shiftupdate_id;
									if($shiftupdateid)
									{
										$companyid='';
										$transaction = $api_ext->update_drivershiftend($shiftupdateid,$companyid);
									}
									$message = array("message" => __('logout_success'),"status"=>1);
								}
								else
								{
									$message = array("message" => __('trip_in_future'),"status"=>-4);
								}
						}
						else
						{
							$message = array("message" => __('driver_in_trip'),"status"=>0);
						}
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);	
					}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);	
				//unset($message,$api_ext,$driver_model);
				break;		
			
			case 'get_trip_detail':
					$array = $mobiledata;
					$trip_id = $array['trip_id'];
					//passenger_id params come from ios passenger app only
					$passenger_id = isset($array['passenger_id']) ? $array['passenger_id'] : '';
					if($trip_id != null)
					{
						$trip_id = $trip_id;						
						$api_model = Model::factory(MOBILEAPI_107);			
						$get_passenger_log_details = $api_model->get_trip_detail($trip_id,$passenger_id);
						if(count($get_passenger_log_details)>0)
						{
							foreach($get_passenger_log_details as $journey)
							{
									$driver_id = $journey->driver_id;
									$taxi_id = $journey->taxi_id;
									$company_id = $journey->company_id;
									$driver_image_name = $journey->driver_image;
									$passenger_image = $journey->passenger_image;
									$trip_details['taxi_min_speed']=$journey->taxi_min_speed;
									$trip_details['trip_id'] = $journey->passengers_log_id;
									$trip_details['current_location'] = $journey->pickup_location;
									$trip_details['pickup_latitude'] = $journey->pickup_latitude;
									$trip_details['pickup_longitude'] = $journey->pickup_longitude;
									$trip_details['drop_location'] = $journey->drop_location;
									$trip_details['drop_latitude'] = $journey->drop_latitude;
									$trip_details['drop_longitude'] = $journey->drop_longitude;
									$trip_details['drop_time'] = ($journey->drop_time != "0000-00-00 00:00:00") ? Commonfunction::getDateTimeFormat($journey->drop_time,3) : "";
									$trip_details['pickup_date_time'] = ($journey->actual_pickup_time != "0000-00-00 00:00:00") ? Commonfunction::getDateTimeFormat($journey->actual_pickup_time,3) : Commonfunction::getDateTimeFormat($journey->pickup_time,3);
									$trip_details['pickup_time'] = ($journey->actual_pickup_time != "0000-00-00 00:00:00") ? date("H:i:s",strtotime($journey->actual_pickup_time)) : "";
									$trip_details['booking_time'] = Commonfunction::getDateTimeFormat($journey->pickup_time,3);
									$trip_details['time_to_reach_passen'] = str_replace('Min','',$journey->time_to_reach_passen);		
									$trip_details['no_passengers']= $journey->no_passengers;	
									$trip_details['rating'] = $journey->rating;
									$trip_details['notes']= $journey->notes_driver;																
									$trip_details['driver_name'] = $journey->driver_name;								
									$trip_details['driver_id'] = $journey->driver_id;							
									$trip_details['taxi_id'] = $journey->taxi_id;
									$trip_details['taxi_number'] = $journey->taxi_no;
									$trip_details['driver_phone'] = !empty($journey->driver_twilio_number) ? trim($journey->driver_twilio_number) : trim($journey->driver_phone);
									$trip_details['passenger_phone'] = !empty($journey->passenger_phone) ? $journey->passenger_phone : "";
									$trip_details['passenger_name'] = $journey->passenger_name;									
									$passengerWallAmt = $journey->wallet_amount;									
									$trip_details['travel_status'] = $journey->travel_status;	
									$driver_reply = $journey->driver_reply;	
									$trip_details['bookedby'] =  $journey->bookby;//1-passenger, 2-Dispatcher, 3-Driver
									$trip_details['street_pickup_trip'] = ($journey->bookby == 3) ? 1 : 0;
									$trip_details['waiting_time'] = $journey->waiting_time;
									$trip_details['waiting_fare'] =  ($journey->waiting_cost != "" && $journey->waiting_cost != null) ? $journey->waiting_cost : 0;
									$trip_details['distance'] =  $journey->distance;
									$trip_details['actual_distance'] =  $journey->actual_distance;
									$trip_details['metric'] = $journey->metric;
									$trip_details['amt'] = round($journey->amt,2);		
									$trip_details['actual_paid_amount'] = ($journey->is_split_trip == 1 && isset($journey->split_paid_amount)) ? round($journey->split_paid_amount,2) : round($journey->actual_paid_amount,2);		
									$trip_details['used_wallet_amount'] = ($journey->is_split_trip == 1 && isset($journey->split_wallet)) ? round($journey->split_wallet,2) : round($journey->used_wallet_amount,2);		
									$trip_details['job_ref'] = $journey->job_ref;		
									$trip_details['payment_type'] = $journey->payment_type;
									$trip_details['fare_calculation_type'] = $journey->fare_calculation_type;			
									$trip_details['payment_type_label'] = ($journey->payment_type == 1) ? __('cash'):(($journey->payment_type == 5) ? __('wallet') : __('card'));		
									$trip_details['taxi_speed'] = $journey->taxi_speed;
									$trip_details['waiting_fare_hour'] = $journey->waiting_fare_hour;
									$trip_details['fare_per_minute'] = $journey->fare_per_minute;
									$subtotal = $journey->tripfare + $trip_details['waiting_fare'] + $journey->eveningfare + $journey->nightfare;
									$trip_details['subtotal'] = $journey->tripfare;
									$subtotal = $journey->tripfare + $trip_details['waiting_fare'] + $journey->eveningfare + $journey->nightfare;
									$trip_details['subtotal'] = round($subtotal,2);
									$trip_details['minutes_fare'] = $journey->minutes_fare;
									$trip_details['distance_fare'] = round((($journey->tripfare) - ($journey->minutes_fare)),2);
									$distance_fare_metric =  ($journey->distance > 1) ? ($trip_details['distance_fare'] / $journey->distance) : $trip_details['distance_fare']; 
									$trip_details['distance_fare_metric'] = round($distance_fare_metric,2);
									$trip_details['trip_minutes'] = $journey->trip_minutes;
									$trip_details['promocode_fare'] = $journey->promocode_fare;
									$trip_details['eveningfare'] = $journey->eveningfare;
									$trip_details['nightfare'] = $journey->nightfare;
									$trip_details['tax_percentage'] = $journey->tax_percentage;
									$trip_details['tax_fare'] = $journey->tax_fare;
									$trip_details['isSplit_fare'] = (int)$journey->is_split_trip;//0-Normal trip, 1-Split Trip
									//variable to know whether the passenger have credit card
									$check_card_data = $api->check_passenger_card_data($journey->passengers_id);
									$totalCancelFare = $api->get_passenger_cancel_faredetail($trip_id);
									$passengerReferrDet = $api->check_passenger_referral_amount($passenger_id);

									$fare_setting = $api->get_cancel_setting($company_id); 
									//check unused referral amount with existing wallet amount
									$referralAmt = (isset($passengerReferrDet[0]['referral_amount'])) ? $passengerReferrDet[0]['referral_amount'] : 0;
									$reducAmt = ($referralAmt != 0) ? ($passengerWallAmt - $referralAmt) : $passengerWallAmt;

									$credit_card_sts = (($check_card_data == 0) || ($passengerWallAmt > 0 && $reducAmt >= $totalCancelFare) || ($fare_setting == 0)) ? 0 : ((FARE_SETTINGS == 1) ? SKIP_CREDIT_CARD : $fare_setting);
									
									$trip_details['credit_card_status'] = $credit_card_sts;
									//condition to check the passenger is primary or not
									$trip_details['is_primary'] = (!empty($passenger_id) && $passenger_id != $journey->passengers_id) ? false : true;
									$trip_details['trip_duration'] = "0";
									if($trip_details['drop_time'] != "") {
										//total trip duration
										$trip_seconds = strtotime($trip_details['drop_time']) - strtotime($trip_details['pickup_time']);
										$trip_days    = floor($trip_seconds / 86400);
										$trip_hours   = floor(($trip_seconds - ($trip_days * 86400)) / 3600);
										$trip_minutes = floor(($trip_seconds - ($trip_days * 86400) - ($trip_hours * 3600))/60);
										$trip_seconds = floor(($trip_seconds - ($trip_days * 86400) - ($trip_hours * 3600) - ($trip_minutes*60)));
										$trip_hours = ($trip_hours < 10) ? '0'.$trip_hours : $trip_hours;
										$trip_minutes = ($trip_minutes < 10) ? '0'.$trip_minutes : $trip_minutes;
										$trip_seconds = ($trip_seconds < 10) ? '0'.$trip_seconds : $trip_seconds;
										$trip_details['trip_duration'] = $trip_hours.":".$trip_minutes.":".$trip_seconds;
									}
									$mapurl = '';
									//map image for completed trips in trip detail page
									if($journey->travel_status == 1) {
										//print_r($journey->active_record);exit;
										if(file_exists(DOCROOT.MOBILE_TRIP_DETAIL_MAP_IMG_PATH.$trip_id.".png")) {
											$mapurl = URL_BASE.MOBILE_TRIP_DETAIL_MAP_IMG_PATH.$trip_id.".png";
										} else {
											$path = $journey->active_record;
											$path = str_replace('],[', '|', $path);
											$path = str_replace(']', '', $path);
											$path = str_replace('[', '', $path);
											$path = explode('|',$path);$path = array_unique($path);
											include_once MODPATH."/email/vendor/polyline_encoder/encoder.php";
											$polylineEncoder = new PolylineEncoder();
											if(!empty($path))
											{
												foreach($path as $values)
												{
													$values = explode(',',$values);
													$polylineEncoder->addPoint($values[0],$values[1]);
													$polylineEncoder->encodedString();
												}
											}
											$encodedString = $polylineEncoder->encodedString();
											
											$marker_end = $journey->drop_latitude.','.$journey->drop_longitude;
											$marker_start = $journey->pickup_latitude.','.$journey->pickup_longitude;
											$startMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'startMarker.png';
											$endMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'endMarker.png';
											if($marker_end != 0) {
												$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x640&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&markers=icon:$endMarker%7C$marker_end&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
											} else {
												$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x640&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
											}
											if(isset($mapurl) && $mapurl != "") {
												$file_path = DOCROOT.MOBILE_TRIP_DETAIL_MAP_IMG_PATH.$trip_id."ss.png";
												file_put_contents($file_path,@file_get_contents($mapurl));
												$mapurl = URL_BASE.MOBILE_TRIP_DETAIL_MAP_IMG_PATH.$trip_id."ss.png";
											}
										} 
									}
									$trip_details['map_image'] = $mapurl;
							}
							
							/************************************Driver Image *******************************/					
								$driver_image = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DRIVER_IMGPATH.'thumb_'.$driver_image_name;
								if(file_exists($driver_image) && ($driver_image_name !=''))
								{
								$driver_image = URL_BASE.SITE_DRIVER_IMGPATH.'thumb_'.$driver_image_name;
								}else{
								$driver_image = URL_BASE."/public/images/noimages109.png";
								}		
								$trip_details['driver_image'] = $driver_image;
							
							/*************************** Passenger Image ************************************/
							if((!empty($passenger_image)) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'thumb_'.$passenger_image))
							{ 
								 $profile_image = URL_BASE.PASS_IMG_IMGPATH.'thumb_'.$passenger_image; 
							}
							else
							{ 
								$profile_image = (isset($trip_details['bookedby']) && $trip_details['bookedby'] == 3) ? URL_BASE."public/images/streetpickup_image.png" : URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
							}	
							$trip_details['passenger_image'] = $profile_image;
							$trip_details['driver_latitute'] = $trip_details['driver_longtitute'] = '0.0';
							$current_driver_status = $api_model->get_driver_current_status($driver_id);
							
							if(count($current_driver_status)>0)
							{
								foreach($current_driver_status as $driver_details)
								{
									$trip_status = $driver_details->status;
									$trip_details['driver_latitute'] = $driver_details->latitude;
									$trip_details['driver_longtitute'] = $driver_details->longitude;									
								}
							}
							
							$trip_details['driver_status'] =  (isset($trip_status) && $trip_status != 'B') ?  $trip_status : 'F';

							$dresult = $api->driver_ratings($driver_id);
							$totalrating=0;
							if(count($dresult) > 0)
							{
								$overall_rating = 5; $i=0; $trip_total_with_rate=1;
								
								foreach($dresult as $comments)
								{
									if($comments['rating'] != 0)
									$trip_total_with_rate++;
									$rating = $comments['rating'];
									$overall_rating += $comments['rating'];
									$i++;	
								}
																		
								if($trip_total_with_rate!=0 && $overall_rating!=0){
									$totalrating = $overall_rating/$trip_total_with_rate;
								}else{
									$totalrating = 5;
								}		
																
								$totalrating = round($totalrating);											
							}
							else
							{
								$totalrating = 5;
							}				
							
							if($trip_details['travel_status'] == 1 || $trip_details['travel_status'] == 4 || ($trip_details['travel_status'] ==9 && $driver_reply == 'C')){
								$trip_details['driver_rating'] = $trip_details['rating'];
							}else{
								$trip_details['driver_rating'] = $totalrating;
							}							
							
							/** Split Fare Details **/
							$splitApproveArr = array();
							if($trip_details['isSplit_fare'] == 1){
								$splitApproveArr = $api->getSplitFareStatus($trip_id);
								if(count($splitApproveArr) > 1){
									foreach($splitApproveArr as $splkey=>$splits){
										if((!empty($splits['profile_image'])) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.$splits['profile_image'])){ 
											$profile_image = URL_BASE.PASS_IMG_IMGPATH.$splits['profile_image']; 
										}
										else{ 
											$profile_image = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
										}
										$splitApproveArr[$splkey]['profile_image'] = $profile_image;
										$splittedFare = ($trip_details['amt'] * $splits['fare_percentage']) / 100;
										$splitApproveArr[$splkey]['splitted_fare'] = round($splittedFare, 2);
									}
								}
							}
							$trip_details['splitFareDetails'] = $splitApproveArr;
							
							if(count($get_passenger_log_details) == 0)
							{
								$message = array("message" => __('try_again'),"status"=>0,"site_currency"=>$this->site_currency);	
							}
							else
							{
								$mes = __('success');
								if($trip_details['travel_status'] == 5) {
									$mes = __('trip_waiting_payment');
								} else if($trip_details['travel_status'] == 4) {
									$mes = __('cancel_by_passenger');
								}
								$message = array("message" => $mes,"detail"=>$trip_details,"status" => 1,"site_currency"=>$this->site_currency);
							}	
					}
					else
					{
						$message = array("message" => __('invalid_trip'),"status"=>-1,"site_currency"=>$this->site_currency);	
					}									
				}
				else
				{
					$message = array("message" => __('invalid_trip'),"status"=>-1,"site_currency"=>$this->site_currency);	
				}			
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);	
				//unset($message,$api_model,$profile_image,$trip_details,$get_passenger_log_details);
				break;

			case 'passenger_logout':	
			$passenger_log_array = $mobiledata;		
					if($passenger_log_array['id'] != null)
					{
						$api_model = Model::factory(MOBILEAPI_107);	
						$update_id = $passenger_log_array['id'];
						$check_result = $api->check_passenger_companydetails($passenger_log_array['id'],$default_companyid);
						if($check_result == 0)	
						{
							$message = array("message" => __('invalid_user'),"status"=>-1);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							exit;
						}

						$update_array  = array("login_from"=>"","login_status"=>"N","device_id" => "","device_token" => "","device_type" => "");
						$logout_status_update = $api_model->update_passengers($update_array,$update_id,$default_companyid);
						$delete_rejected_trips = $api_model->delete_rejected_trips($update_id,$company_all_currenttimestamp);
						$message = array("message" => __('logout_success'),"status"=>1);					
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>0);	
					}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);	
				//unset($message,$delete_rejected_trips,$logout_status_update,$check_result,$api_model);
				break;		
				
			case 'tell_to_friend_message':			
			$array = $mobiledata;
			$id = $array['id'];
			$type = $array['usertype'];
			$device_type = $array['device_type'];
			$subject = __('telltofrien_subject');
			$name="";
			$message = "";
			if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/site_logo/'.$this->domain_name.'_email_logo.png')){  $email_logo=$this->domain_name; }else{ $email_logo="demo"; }
			if($device_type == 1)
			{
				$replace_variables=array(
					REPLACE_LOGO=>EMAILTEMPLATELOGO,
					REPLACE_SITENAME=>$this->app_name,
					REPLACE_NAME=>$name,
					REPLACE_MESSAGE=>TELL_TO_FRIEND_MESSAGE,
					REPLACE_SITEEMAIL=>$this->siteemail,
					REPLACE_SITEURL=>URL_BASE,
					REPLACE_EMAIL_LOGO=>$email_logo,
					REPLACE_ANDROID_PASSENGER_APP=>ANDROID_PASSENGER_APP,
					REPLACE_IOS_PASSENGER_APP=>IOS_PASSENGER_APP,
					REPLACE_ANDROID_DRIVER_APP=>ANDROID_DRIVER_APP,
				);
			}
			else
			{
				$replace_variables=array(REPLACE_LOGO=>EMAILTEMPLATELOGO,
					REPLACE_SITENAME=>$this->app_name,
					REPLACE_NAME=>$name,
					REPLACE_SITEEMAIL=>$this->siteemail,
					REPLACE_SITEURL=>URL_BASE,
					REPLACE_MESSAGE=>TELL_TO_FRIEND_MESSAGE,
					REPLACE_ANDROID_PASSENGER_APP=>ANDROID_PASSENGER_APP,
					REPLACE_EMAIL_LOGO=>$email_logo,
					REPLACE_IOS_PASSENGER_APP=>IOS_PASSENGER_APP,
					REPLACE_ANDROID_DRIVER_APP=>ANDROID_DRIVER_APP,
					REPLACE_COPYRIGHTS=>SITE_COPYRIGHT,
					REPLACE_COPYRIGHTYEAR=>COPYRIGHT_YEAR
				);					
			}
				
			/* Added for language email template */
			if($this->lang!='en'){
			if(file_exists(DOCROOT.TEMPLATEPATH.$this->lang.'/telltofriend-'.$this->lang.'.html')){
			$message_temp=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.$this->lang.'/telltofriend-'.$this->lang.'.html',$replace_variables);
			}else{
			$message_temp=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'telltofriend.html',$replace_variables);
			}
			}else{
			$message_temp=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'telltofriend.html',$replace_variables);
			}
			/* Added for language email template */
			$message_template = htmlspecialchars($message_temp);
							
			if(($id != null) && ($type != null))
			{
				if($type == 'D')
				{
					$driver_profile = $api->driver_profile($id);	
					if(count($driver_profile)>0)
					{
						$referral_code = $driver_profile[0]['driver_referral_code'];		
						$name = $passenger_profile[0]['name'];				
						$driver_email = $passenger_profile[0]['email'];				
						$telltofriend_message = DRIVER_TELL_TO_FRIEND_MESSAGE; 
						$detail = array("tell_message" => $telltofriend_message,"message_template"=>$message_template,"subject"=>$subject);
						$message = array("detail"=>$detail,"status"=>1,"message"=>__('success'));
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>0);
					}					
				}				
				else
				{
					$passenger_profile = $api->passenger_profile($id,'A');
					if(count($passenger_profile)>0)
					{
						$referral_code = $passenger_profile[0]['referral_code'];
						$name = $passenger_profile[0]['name'];
						$phone = $passenger_profile[0]['phone'];
						$country_code = $passenger_profile[0]['country_code'];
						$ref_message = TELL_TO_FRIEND_MESSAGE.''.$referral_code;
						$ref_discount = REFERRAL_DISCOUNT;
						$telltofriend_message = TELL_TO_FRIEND_MESSAGE;//str_replace("#REFDIS#",$ref_discount,$ref_message); 						
						$detail = array("tell_message" => $telltofriend_message,"message_template"=>$message_template,"subject"=>$subject);
						$message = array("detail"=>$detail,"status"=>1,"message"=>__('success'));
						
						if(SMS == 1)
						{							
							$message_details = $this->commonmodel->sms_message_by_title('invite_friend_sms');
							if(count($message_details) > 0) {
								$to = $country_code.$phone;
								$message = $message_details[0]['sms_description'];
								$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
								$this->commonmodel->send_sms($to,$message);
							}
						}
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>0,"message"=>__('failed'));
					}
				}							
			}
			else
			{
				$message = array("message" => __('validation_error'),"status"=>-1,"message"=>__('failed'));	
			}			
									
            if($device_type == 1)
            {
				$search = array('"');
				$replace = array("'");			
				echo $str = str_ireplace($search,$replace,$message_temp);							
			}
			else
			{
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			}
			//unset($message,$passenger_profile,$driver_profile,$message_temp,$replace_variables);
			break;			

			case 'tell_to_friend':
			$tell_array = $mobiledata;
			if(!empty($tell_array))
			{
				$to = $tell_array['to'];
				$message = $tell_array['message'];
				$passenger_id = $tell_array['passenger_id'];
				$name = $email = $referral_code = "";
				$check_validation = $this->check_tell_to_friend($tell_array);
				if($check_validation->check()) 
				{
					$passenger_details = $api->passenger_profile($passenger_id,'A');
					$referral_code = "";
					if(count($passenger_details)>0)
					{
						$name = $passenger_details[0]['name'];
						$email = $passenger_details[0]['email'];
						$referral_code = $passenger_details[0]['referral_code'];
					}
					
							$friends_email = explode(',',$to);
							$rejectedemails="";
							$successemails = "";
							$mail="";
							foreach($friends_email as $femail)					
							{	
								$check_list = $api->check_email_passengers($femail);
								
								if($check_list > 0)
								{
									$rejectedemails .= $femail.',';
								}
								else
								{ 
				
								if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/site_logo/'.$this->domain_name.'_email_logo.png')){  $email_logo=$this->domain_name; }else{ $email_logo="demo"; }
								
									$subject = __('telltofrien_subject');
									$replace_variables=array(
										REPLACE_LOGO=>EMAILTEMPLATELOGO,
										REPLACE_SITENAME=>$this->app_name,
										REPLACE_NAME=>$name,
										REPLACE_EMAIL=>$email,
										REPLACE_SUBJECT=>$subject,
										REPLACE_MESSAGE=>$message,
										REPLACE_SITEEMAIL=>$this->siteemail,
										REPLACE_SITEURL=>URL_BASE,
										REPLACE_COMPANYDOMAIN=>$this->domain_name,
										REPLACE_ANDROID_PASSENGER_APP=>ANDROID_PASSENGER_APP,
										REPLACE_IOS_PASSENGER_APP=>IOS_PASSENGER_APP,
										REPLACE_EMAIL_LOGO=>$email_logo,
										REPLACE_ANDROID_DRIVER_APP=>ANDROID_DRIVER_APP,REPLACE_COPYRIGHTS=>SITE_COPYRIGHT,REPLACE_COPYRIGHTYEAR=>COPYRIGHT_YEAR);
									
			/* Added for language email template */
			if($this->lang!='en'){
			if(file_exists(DOCROOT.TEMPLATEPATH.$this->lang.'/telltofriend-'.$this->lang.'.html')){
			$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.$this->lang.'/telltofriend-'.$this->lang.'.html',$replace_variables);
			}else{
			$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'telltofriend.html',$replace_variables);
			}
			}else{
			$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'telltofriend.html',$replace_variables);
			}
			/* Added for language email template */
									
									$friend_to = $femail;
									$from = $this->siteemail;
									$successemails .= $femail.',';	
									$redirect = "no";	
									if(SMTP == 1)
									{
										include($_SERVER['DOCUMENT_ROOT']."/modules/SMTP/smtp.php");
									}
									else
									{
										// To send HTML mail, the Content-type header must be set
										$headers  = 'MIME-Version: 1.0' . "\r\n";
										$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
										// Additional headers
										$headers .= 'From: '.$from.'' . "\r\n";
										$headers .= 'Bcc: '.$to.'' . "\r\n";
										mail($to,$subject,$message,$headers);	
									}																			
								}
								if(empty($successemails))
								{
									$detail = __('already_reg');
								}
								else
								{
									$detail =  __('invitation_send');						
								}
								$message = array("detail"=>$detail,"status"=>1,"message"=>__('success'));
							}	
					}
					else
					{
						$detail = $check_validation->errors('errors');
						$message = array("detail"=>$detail,"status"=>2,"message"=>__('validation_error'));
					}
			}
			else
			{
				$message = array("message" => __('invalid_request'),"status"=> 5);
			}	
            $mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);	
            //unset($message,$email_logo,$check_list,$check_validation,$passenger_details);
			break;		
			
			case 'dynamic_page':			
			$page_array = $_GET;
			$check_validation = $this->check_dynamic_array($page_array);
			if($check_validation->check()) 
			{
					$pagename = $page_array['pagename'];
					$device_type = $page_array['device_type'];
					$pagecontent=$content="";
					if($pagename != null)
					{	
						$content_cms = $api->getcmscontent($pagename,$default_companyid);
						if(count($content_cms)>0)
						{
							foreach($content_cms as $value)
							{								
								$pagecontent = isset($value['content'])?$value['content']:'';
								$content = htmlentities($pagecontent);								
								$menu = $value['menu'];
							}
						}
						else
						{
							if($device_type == 1)
							{
								echo __('page_not_found');
								break;	
							}
							else if($device_type == 2)
							{
								$message = array("message" => __('page_not_found'),"status"=>2);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);	
								//unset($message,$json_decode);
								break;	
							}			
							else
							{
								$message = array("message" => __('page_not_found'),"status"=>2);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);	
								//unset($message,$json_decode);
								break;
							}	
	
						}							
					}
					else
					{
						$message = array("message" => __('invalid_page'),"status"=>-1);	
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						//unset($message,$json_decode);
						break;	
					}
				if($device_type == 1)
				{
					echo $pagecontent;
					//unset($pagecontent);
					break;	
				}
				else if($device_type == 2)
				{
					$result = array("content"=>$content,"title"=>$menu);
					$message = array("message"=>__('success'),"detail" => $result,"status"=>1);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);	
					//unset($message,$json_decode);
					break;	
				}			
				else
				{
					$message = array("message" => __('invalid_page'),"status"=>-1);	
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$json_decode);
					break;	
				}	 
			}
			else
			{
					$detail = $check_validation->errors('errors');
					$message = array("detail"=>$detail,"status"=>-3,"message"=>__('validation_error'));		
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);			
			}			
			//unset($message);
			break;								
			
			case 'completed_journey_datewise':
				$array = $mobiledata;				
				if($array['passenger_id'] != null)
				{
					$validator = $this->trip_history_date_wise($array);					
					if($validator->check()) 
					{					
						$userid= $array['passenger_id'];
						$start = $array['start'];
						$limit = $array['limit'];	
						$date = $array['date'];		
						$device_type = $array['device_type']; // 1 Android , 2 - IOS
						//Getting from Passenger Model Directly
						$passengers = Model::factory('passengers');
						$booktype="2";
						$fromdate = $date.' 00:00:01';
						$todate = $date.' 23:59:59';
						$arraydetails = array();
						$alldetails = array();
						if($device_type == 1)
						$pagination = 1;
						else
						$pagination = 0;
						$total_array = array();
						for($i = strtotime($fromdate); $i <= strtotime($todate); $i = strtotime('+1 Day', $i))
						{
							$cdate = date("Y-m-d",$i);						
							$passengers_all_compl = $api->get_passenger_trips_bydate($pagination,$booktype,$userid,1,'A','1',$start,$limit,$cdate);
							if(count($passengers_all_compl) > 0)
							{
								foreach($passengers_all_compl as $result)
								{
									$arraydetails['trip_id'] = $result['trip_id'];
									$arraydetails['place'] = $result['place'];
									$arraydetails['booking_time'] = $result['pickup_time'];
									$arraydetails['pickup_time'] = ($result['actual_pickup_time'] != "0000-00-00 00:00:00") ? $result['actual_pickup_time'] : $result['pickup_time'];
									$arraydetails['fare'] = $result['fare'];
									$arraydetails['pickup_latitude'] = $result['pickup_latitude'];
									$arraydetails['pickup_longitude'] = $result['pickup_longitude'];
									$arraydetails['drop_latitude'] = $result['drop_latitude'];
									$arraydetails['drop_longitude'] = $result['drop_longitude'];
									$arraydetails['notes_driver'] = $result['notes_driver'];
									$arraydetails['drivername'] = $result['drivername'];
									$arraydetails['drop_location'] = $result['drop_location'];
									$date =$result['pickup_time'];
									$alldetails[] = $arraydetails;
								}
								$total_array[] = array("trip_Date" => $cdate,"trip_details" => $alldetails);
							}							
						}						
						if(count($total_array) > 0)
						{
							$message = array("message" => __('success'),"detail"=>$total_array,"status"=>1,"site_currency"=>$this->site_currency);	
						}
						else
						{
							$message = array("message" => __('no_completed_data_date'),"status"=>0,"site_currency"=>$this->site_currency);	
						}	
					}
					else
					{
						$errors = $validator->errors('errors');	
						$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>2,"site_currency"=>$this->site_currency);
					}											
				}
				else
				{
					$message = array("message" => __('invalid_user'),"status"=>-1,"site_currency"=>$this->site_currency);	
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$total_array,$arraydetails,$passengers_all_compl);
				break;
			
			case 'completed_journey_monthwise':
				$array = $mobiledata;				
				if($array['passenger_id'] != null)
				{
					$validator = $this->trip_history_month_wise($array);
					if($validator->check()) 
					{
						$userid= $array['passenger_id'];
						$start = $array['start'];
						$limit = $array['limit'];	
						$month = $array['month'];	
						$year = $array['year'];	
						$device_type = $array['device_type']; // 1 Android , 2 - IOS
						//Getting from Passenger Model Directly
						$passengers = Model::factory('passengers');
						// Booktype 0 -> Flagger Ride, 1-> Strret Ride, 2-> All
						$booktype="2";
						// all records from 1 month	//
						$fromdate = $year.'-'.$month.'-'.'01';
						$todate = date('Y-m-t', strtotime($fromdate));
						$cmonth = $year.'-'.$month;
						$arraydetails = array();
						$alldetails = array();
						$pagination = 1;
						
							$passengers_all_compl = $api->get_passengertrips_byfrmdate($pagination,$booktype,$userid,'1',$start,$limit,$cmonth);
							
							if(count($passengers_all_compl) > 0)
							{
								foreach($passengers_all_compl as $result)
								{
									$arraydetails['trip_id'] = $result['trip_id'];
									$arraydetails['place'] = $result['place'];
									$arraydetails['booking_time'] = Commonfunction::getDateTimeFormat($result['pickup_time'],1);
									$arraydetails['pickup_time'] = ($result['actual_pickup_time'] != "0000-00-00 00:00:00" && $result['actual_pickup_time'] != "") ? Commonfunction::getDateTimeFormat($result['actual_pickup_time'],1) : Commonfunction::getDateTimeFormat($result['pickup_time'],1);
									$arraydetails['fare'] = $result['fare'];
									$arraydetails['pickup_latitude'] = $result['pickup_latitude'];
									$arraydetails['pickup_longitude'] = $result['pickup_longitude'];
									$arraydetails['drop_latitude'] = $result['drop_latitude'];
									$arraydetails['drop_longitude'] = $result['drop_longitude'];
									$arraydetails['notes_driver'] = $result['notes_driver'];
									$arraydetails['drivername'] = $result['drivername'];
									$arraydetails['drop_location'] = $result['drop_location'];
									$arraydetails['createdate'] = Commonfunction::getDateTimeFormat($result['createdate'],1);
									$arraydetails['profile_image'] = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
									if(!empty($result['profile_image']) && file_exists(DOCROOT.SITE_DRIVER_IMGPATH.$result['profile_image'])) {
										$arraydetails['profile_image'] = URL_BASE.SITE_DRIVER_IMGPATH.$result['profile_image'];
									}
									$arraydetails['taxi_no'] = $result['taxi_no'];
									$arraydetails['travel_status'] = $result['travel_status'];
									$arraydetails['payment_type'] = $result['payment_type'];
									$arraydetails['model_name'] = $result['model_name'];
									$arraydetails['driver_confirm'] = 1;
									$date = $result['pickup_time'];
									
									
									if(file_exists(DOCROOT.MOBILE_COMPLETE_TRIP_MAP_IMG_PATH.$result['trip_id'].".png")) {
										$mapurl = URL_BASE.MOBILE_COMPLETE_TRIP_MAP_IMG_PATH.$result['trip_id'].".png";
									} else {
										$path = $result['active_record'];
										$path = str_replace('],[', '|', $path);
										$path = str_replace(']', '', $path);
										$path = str_replace('[', '', $path);
										$path = explode('|',$path);$path = array_unique($path);
										include_once MODPATH."/email/vendor/polyline_encoder/encoder.php";
										$polylineEncoder = new PolylineEncoder();
										if(count(array_filter($path)) > 0)
										{
											foreach ($path as $values)
											{
											$values = explode(',',$values);
											$polylineEncoder->addPoint($values[0],$values[1]);
											$polylineEncoder->encodedString();
											}
										}
										$encodedString = $polylineEncoder->encodedString();
										
										$marker_end = $result['drop_latitude'].','.$result['drop_longitude'];
										$marker_start = $result['pickup_latitude'].','.$result['pickup_longitude'];
										$startMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'startMarker.png';
										$endMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'endMarker.png';
										
										if($marker_end != 0) {
											$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&markers=icon:$endMarker%7C$marker_end&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
										} else {
											$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
										}
										if(isset($mapurl) && $mapurl != "") {
											$file_path = DOCROOT.MOBILE_COMPLETE_TRIP_MAP_IMG_PATH.$result['trip_id'].".png";
											file_put_contents($file_path,@file_get_contents($mapurl));
											$mapurl = URL_BASE.MOBILE_COMPLETE_TRIP_MAP_IMG_PATH.$result['trip_id'].".png";
										}
									}
									$arraydetails['map_image'] = $mapurl;
									$alldetails[] = $arraydetails;
								}
							}
						
						if(count($alldetails) > 0)
						{
							$message = array("message" =>__('success'),"trip_details" => $alldetails,"status"=>1,"site_currency"=> $this->site_currency);
						}
						else
						{
							$message = array("message" => __('no_completed_data_month'),"status"=>0,"site_currency"=>$this->site_currency);	
						}						
					}
					else
					{
						$errors = $validator->errors('errors');	
						$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>2,"site_currency"=>$this->site_currency);
					}							
				}
				else
				{
					$message = array("message" => __('invalid_user'),"status"=>-1,"site_currency"=>$this->site_currency);	
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$alldetails,$arraydetails,$file_path,$mapurl,$startMarker,$endMarker,$passengers_all_compl);
				break;	
				
			case 'completed_journey':
				$array = $mobiledata;
				if($array['id'] != '')
				{
					$userid= $array['id'];
					$start = $array['start'];
					$limit = $array['limit'];

					$check_result = $api->check_passenger_companydetails($array['id'],$default_companyid);
					if($check_result == 0)	
					{
						$message = array("message" => __('invalid_user'),"status"=>-1,"site_currency"=>$this->site_currency);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						exit;;
					}
					
					//Getting from Passenger Model Directly
					$passengers = Model::factory('passengers');
					$passengers_all_compl = $api->get_passenger_log_details($userid,1,'A','1',$start,$limit,$default_companyid);
					
					if(count($passengers_all_compl) > 0)
					{
						$message = array("message" =>__('success'),"detail"=>$passengers_all_compl,"currency"=>$this->site_currency);
					}
					else
					{
						$message = array("message" => __('no_completed_data'),"status"=>0,"site_currency"=>$this->site_currency);	
					}						
						
				}
				else
				{
					$message = array("message" => __('invalid_user'),"status"=>-1,"site_currency"=>$this->site_currency);	
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$check_result,$passengers_all_compl);
				break;	

			case 'cancelled_journey':
			$cancel_array = $mobiledata;
				if($cancel_array['id'] != null)
				{
					$validator = $this->coming_cancel($array);					
					if($validator->check()) 
					{
						$userid= $cancel_array['id'];
						$start = $cancel_array['start'];
						$limit = $cancel_array['limit'];
						$device_type = $cancel_array['device_type'];

						$check_result = $api->check_passenger_companydetails($userid,$default_companyid);
						if($check_result == 0)	
						{
							$message = array("message" => __('invalid_user'),"status"=>-1);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							exit;
						}
						if($device_type == 1)
						$pagination = 1;
						else
						$pagination = 0;
						$passengers_cancel = $api->get_passenger_cancelled_trip_details($default_companyid,$pagination,$userid,'','A','',$start,$limit);
						$trip_details = array();
						$i = 0;
						$alldetails = array();
						if(count($passengers_cancel) > 0)
						{
							foreach($passengers_cancel as $journey)
							{
									$driver_id = $journey['driver_id'];
									$payment_type = $journey['payment_type'];
									$driver_image = $journey['driver_image'];
									$trip_details['trip_id'] = $journey['passengers_log_id'];
									$trip_details['pickup_location'] = $journey['pickup_location'];
									$trip_details['pickup_latitude'] = $journey['pickup_latitude'];
									$trip_details['pickup_longitude'] = $journey['pickup_longitude'];
									$trip_details['drop_location'] = $journey['drop_location'];
									$trip_details['drop_latitude'] = $journey['drop_latitude'];
									$trip_details['drop_longitude'] = $journey['drop_longitude'];
									$trip_details['pickup_time'] = $journey['pickup_time'];
									$trip_details['time_to_reach_passen'] = $journey['time_to_reach_passen'];
									$trip_details['driver_name'] = $journey['driver_name'];								
									$trip_details['driver_id'] = $journey['driver_id'];							
									$trip_details['taxi_id'] = $journey['taxi_id'];
									$trip_details['taxi_number'] = $journey['taxi_no'];
									$trip_details['driver_phone'] = $journey['driver_phone'];
									$trip_details['passenger_name'] = $journey['passenger_name'];
									$trip_details['travel_status'] = $journey['travel_status'];
									$trip_details['amt'] = $journey['amt'];		
									$trip_details['job_ref'] = $journey['job_ref'];		
							$paymentname="";
							$paymentname_sql = $api->get_payment_name($journey['payment_type']);
							if(count($paymentname_sql) > 0)
							{
								$paymentname = $paymentname_sql[0]['pay_mod_name'];
							}
							$trip_details['payment_type'] = $paymentname;		
							/************************************Driver Image *******************************/					
								$driver_image = URL_BASE.SITE_DRIVER_IMGPATH.$driver_image;
								if(file_exists($driver_image) && $driver_image !='')
								{
								$driver_image = URL_BASE.SITE_DRIVER_IMGPATH.$driver_image;
								}else{
								$driver_image = URL_BASE."/public/images/noimages109.png";
								}		
								$trip_details['driver_image'] = $driver_image;			
							$alldetails[] = $trip_details;
							$i = $i+1;			
							}
							if(SMS == 1)
							{
								$message_details = $this->commonmodel->sms_message_by_title('trip_cancel');
								if(count($message_details) > 0) {
									$to = $api->get_driver_phone_by_id($driver_id);
									$message = $message_details[0]['sms_description'];
									$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
									$this->commonmodel->send_sms($to,$message);
								}
							}																		
							$message = array("message" => __('success'),"detail"=>$alldetails,"status"=>1);
						}
						else
						{
							$message = array("message" => __('no_data'),"status"=>0);	
						}	
					}
					else
					{
							$errors = $validator->errors('errors');	
							$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>2);							
					}					
						
				}
				else
				{
					$message = array("message" => __('invalid_user'),"status"=>-1);	
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$errors,$message_details,$trip_details,$passengers_cancel,$check_result);
				break;					
				
				case 'coming_trips':
				$passenger_list_array = $mobiledata;
				//Current Journey after driver confirmation //TN1013619352
				
					if($passenger_list_array['id'] != null)
					{
						$validator = $this->coming_cancel($passenger_list_array);					
						if($validator->check()) 
						{						
							$userid= $passenger_list_array['id'];
							$start = $passenger_list_array['start'];
							$limit = $passenger_list_array['limit'];	
							$device_type = $passenger_list_array['device_type'];	
							$check_result = $api->check_passenger_companydetails($passenger_list_array['id'],$default_companyid);
							if($check_result == 0)	
							{
								$message = array("message" => __('invalid_user'),"status"=>-1);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								exit;
							}
							if($device_type == 1)
							$pagination = 1;
							else
							$pagination = 0;
							$passengers_current = $api->get_passenger_current_log_details($default_companyid,$pagination,$userid,'','A','0',$start,$limit);
							
							if(count($passengers_current) > 0)
							{
								$message = array("message" => __('success'),"detail"=>$passengers_current,"status"=>1);
							}					
							else
							{
								$message = array("message" => __('no_data'),"status"=>0);	
							}	
						}
						else
						{
							$errors = $validator->errors('errors');	
							$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>2);							
						}												
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$passengers_current,$validator,$check_result);
					break;
					
				case 'booking_list':
					//Current Journey after driver confirmation //TN1013619352
					$array = $mobiledata;
					
					if($array['id'] != null)
					{
						$validator = $this->coming_cancel($array);
						if($validator->check()) 
						{
							$userid= $array['id'];
							$start = $array['start'];
							$limit = $array['limit'];
							$device_type = $array['device_type'];
							$check_result = $api->check_passenger_companydetails($array['id'],$default_companyid);
							if($check_result == 0)
							{
								$message = array("message" => __('invalid_user'),"status"=>-1);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								exit;
							}
							if($device_type == 1)
							$pagination = 1;
							else
							$pagination = 0;
							$passengers_trips = array();
							$pending_bookings = $api->get_pending_bookings($default_companyid,$pagination,$userid,'','A','0',$start,$limit);
							foreach($pending_bookings as $key => $val)
							{
								$pending_bookings[$key]['driver_confirm']=true;
								switch($val['travel_status'])
								{
									case 1:
										$pending_bookings[$key]['travel_msg']="Fare Updated";
									break;
									case 2:
										$pending_bookings[$key]['travel_msg']="Inprogress";
									break;
									case 3:
										$pending_bookings[$key]['travel_msg']="Arrived";
									break;
									case 5:
										$pending_bookings[$key]['travel_msg']="Completed";
									break;
									case 9:
										$pending_bookings[$key]['travel_msg']="Confirmed";
									break;
									case 0:
										$pending_bookings[$key]['travel_msg']="Not Confirmed";
										$pending_bookings[$key]['driver_confirm']=false;
									break;
									default:
										$pending_bookings[$key]['travel_msg']="Cancelled";
									break;
								}
								//to get the pickup time with required date format
								$pending_bookings[$key]['pickup_time'] = Commonfunction::getDateTimeFormat($val['pickuptime'],3);
								$pending_bookings[$key]['profile_image'] = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
								if(!empty($val['profile_image']) && file_exists(DOCROOT.SITE_DRIVER_IMGPATH.$val['profile_image'])) {
									$pending_bookings[$key]['profile_image'] = URL_BASE.SITE_DRIVER_IMGPATH.$val['profile_image'];
								}
								
								$pending_bookings[$key]['waiting_fare'] = "0";
								
								if(file_exists(DOCROOT.MOBILE_PENDING_TRIP_MAP_IMG_PATH.$val['passengers_log_id'].".png")) {
									$mapurl = URL_BASE.MOBILE_PENDING_TRIP_MAP_IMG_PATH.$val['passengers_log_id'].".png";
								} else {									
									include_once MODPATH."/email/vendor/polyline_encoder/encoder.php";
									$polylineEncoder = new PolylineEncoder();
									$polylineEncoder->addPoint($val['pickup_latitude'],$val['pickup_longitude']);
									
									$marker_end = 0;
									if($val['drop_latitude'] != 0 && $val['drop_longitude'] != 0){
										$polylineEncoder->addPoint($val['drop_latitude'],$val['drop_longitude']);
										$marker_end = $val['drop_latitude'].','.$val['drop_longitude'];
									}
									$encodedString = $polylineEncoder->encodedString();
									$marker_start = $val['pickup_latitude'].','.$val['pickup_longitude'];
									$startMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'startMarker.png';
									$endMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'endMarker.png';
									if($marker_end != 0) {
										$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&markers=icon:$endMarker%7C$marker_end&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
									} else {
										$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
									}
									if(isset($mapurl) && $mapurl != "") {
										$file_path = DOCROOT.MOBILE_PENDING_TRIP_MAP_IMG_PATH.$val['passengers_log_id'].".png";
										file_put_contents($file_path,@file_get_contents($mapurl));
										$mapurl = URL_BASE.MOBILE_PENDING_TRIP_MAP_IMG_PATH.$val['passengers_log_id'].".png";
									}
								}
								$pending_bookings[$key]['map_image'] = $mapurl;
							}
							$passengers_trips['pending_bookings']=$pending_bookings;
							if(count($passengers_trips) > 0)
							{
								$message = array("message" => __('success'),"detail"=>$passengers_trips,"status"=>1);
							}					
							else
							{
								$message = array("message" => __('no_data'),"status"=>0);
							}	
						}
						else
						{
							$errors = $validator->errors('errors');
							$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>2);							
						}												
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$passengers_trips,$file_path,$mapurl,$startMarker,$endMarker,$pending_bookings,$check_result);
					break;
					
				case 'driver_booking_list':
				//Current Journey after driver confirmation //TN1013619352
				$driver_list_array = $mobiledata;
					if($driver_list_array['driver_id'] != null)
					{
						$validator = $this->driver_coming_cancel($driver_list_array);
						if($validator->check())
						{
							$driver_id= $driver_list_array['driver_id'];
							$start = $driver_list_array['start'];
							$limit = $driver_list_array['limit'];	
							$device_type = $driver_list_array['device_type'];
							$request_type = $driver_list_array['request_type'];
							$pagination = 0;
							$driver_pending_bookings = array();
							$past_booking = array();
							if($request_type == 1) {
								/***********************Driver Upcoming******************************/
								$driver_pending_bookings = $api->driver_pending_bookings($driver_id,'R','A','2',$default_companyid);
								if(count($driver_pending_bookings) > 0)
								{
									foreach($driver_pending_bookings as $key => $journey)
									{
										$passenger_photo = isset($journey['passenger_profile_image'])?$journey['passenger_profile_image']:'';
										if((!empty($passenger_photo)) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'thumb_'.$passenger_photo))
										{ 
											 $profile_image = URL_BASE.PASS_IMG_IMGPATH.'thumb_'.$passenger_photo; 
										}
										else
										{ 
											$profile_image = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
										} 
										$driver_pending_bookings[$key]['profile_image']=$profile_image;
										$payment_type=isset($journey['payment_type'])?$journey['payment_type']:'';	
										switch($payment_type)
										{
											case 1:
											$driver_pending_bookings[$key]['payment_type']="Cash";
											break;
											case 2:
											$driver_pending_bookings[$key]['payment_type']="Credit Card";
											break;
											case 3:
											$driver_pending_bookings[$key]['payment_type']="New Card";
											break;
											case 5:
											$driver_pending_bookings[$key]['payment_type']="Wallet";
											break;
											default:
											$driver_pending_bookings[$key]['payment_type']="Uncard";
											break;
										}
										//to get the pickup time with required date format
										$driver_pending_bookings[$key]['pickup_time'] = Commonfunction::getDateTimeFormat($journey['pickup_time'],1);
										//map image for upcoming trips in driver app
										if(file_exists(DOCROOT.MOBILE_PENDING_TRIP_MAP_IMG_PATH.$journey['passengers_log_id'].".png")) {
											$mapurl = URL_BASE.MOBILE_PENDING_TRIP_MAP_IMG_PATH.$journey['passengers_log_id'].".png";
										} else {									
											include_once MODPATH."/email/vendor/polyline_encoder/encoder.php";
											$polylineEncoder = new PolylineEncoder();
											$polylineEncoder->addPoint($journey['pickup_latitude'],$journey['pickup_longitude']);
											$marker_end = 0;
											if($journey['drop_latitude'] != 0 && $journey['drop_longitude'] != 0){
												$polylineEncoder->addPoint($journey['drop_latitude'],$journey['drop_longitude']);
												$marker_end = $journey['drop_latitude'].','.$journey['drop_longitude'];
											}
											$marker_start = $journey['pickup_latitude'].','.$journey['pickup_longitude'];
											$encodedString = $polylineEncoder->encodedString();
											$startMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'startMarker.png';
											$endMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'endMarker.png';
											if($marker_end != 0) {
												$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&markers=icon:$endMarker%7C$marker_end&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
											} else {
												$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
											}
											
											if(isset($mapurl) && $mapurl != "") {
												$file_path = DOCROOT.MOBILE_PENDING_TRIP_MAP_IMG_PATH.$journey['passengers_log_id'].".png";
												file_put_contents($file_path,@file_get_contents($mapurl));
												$mapurl = URL_BASE.MOBILE_PENDING_TRIP_MAP_IMG_PATH.$journey['passengers_log_id'].".png";
											}
										}
										$driver_pending_bookings[$key]['map_image'] = $mapurl;
									}
								}
							} else {
								$booktype = 1;
								$past_booking = $api->driver_past_bookings($pagination,$booktype,$driver_id,'R','A','1',$start,$limit,$default_companyid);
								foreach($past_booking as $key => $journey)
								{
									$passenger_photo = isset($journey['profile_image'])?$journey['profile_image']:'';
									if((!empty($passenger_photo)) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.'thumb_'.$passenger_photo))
									{ 
										$profile_image = URL_BASE.PASS_IMG_IMGPATH.'thumb_'.$passenger_photo;
									}
									else
									{ 
										$profile_image = ($journey['bookby'] == 3) ? URL_BASE."public/images/streetpickup_image.png" : URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
									} 
									$past_booking[$key]['profile_image']=$profile_image;
									$payment_type=isset($journey['payment_type'])?$journey['payment_type']:'';
									switch($payment_type)
									{
										case 1:
										$past_booking[$key]['payment_type']="Cash";
										break;
										case 2:
										$past_booking[$key]['payment_type']="Credit Card";
										break;
										case 3:
										$past_booking[$key]['payment_type']="Uncard";
										break;
										case 5:
										$past_booking[$key]['payment_type']="Wallet";
										break;
										default:
										$past_booking[$key]['payment_type']="Uncard";
										break;
									}
									//to get passenger Name
									$past_booking[$key]['passenger_name'] = (!empty($journey['passenger_name'])) ? ucfirst($journey['passenger_name']) : '';
									//to get the pickup time with required date format
									$past_booking[$key]['pickup_time'] = Commonfunction::getDateTimeFormat($journey['pickup_time'],1);
									$past_booking[$key]['drop_time'] = Commonfunction::getDateTimeFormat($journey['drop_time'],1);
									//Map image
									if(file_exists(DOCROOT.MOBILE_COMPLETE_TRIP_MAP_IMG_PATH.$journey['passengers_log_id'].".png")) {
										$mapurl = URL_BASE.MOBILE_COMPLETE_TRIP_MAP_IMG_PATH.$journey['passengers_log_id'].".png";
									} else {
										$path = $journey['active_record'];
										$path = str_replace('],[', '|', $path);
										$path = str_replace(']', '', $path);
										$path = str_replace('[', '', $path);
										$path = explode('|',$path);$path = array_unique($path);
										include_once MODPATH."/email/vendor/polyline_encoder/encoder.php";
										$polylineEncoder = new PolylineEncoder();
										if(count(array_filter($path)) > 0)
										{
											foreach ($path as $values)
											{
											$values = explode(',',$values);
											$polylineEncoder->addPoint($values[0],$values[1]);
											$polylineEncoder->encodedString();
											}
										}
										$encodedString = $polylineEncoder->encodedString();
										
										$marker_end = $journey['drop_latitude'].','.$journey['drop_longitude'];
										$marker_start = $journey['pickup_latitude'].','.$journey['pickup_longitude'];
										$startMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'startMarker.png';
										$endMarker = URL_BASE.PUBLIC_IMAGES_FOLDER.'endMarker.png';
										if($marker_end != 0) {
											$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&markers=icon:$endMarker%7C$marker_end&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
										} else {
											$mapurl = "https://maps.googleapis.com/maps/api/staticmap?size=640x270&zoom=13&maptype=roadmap&markers=icon:$startMarker%7C$marker_start&path=weight:3%7Ccolor:red%7Cenc:$encodedString";
										}
										if(isset($mapurl) && $mapurl != "") {
											$file_path = DOCROOT.MOBILE_COMPLETE_TRIP_MAP_IMG_PATH.$journey['passengers_log_id'].".png";
											file_put_contents($file_path,@file_get_contents($mapurl));
											$mapurl = URL_BASE.MOBILE_COMPLETE_TRIP_MAP_IMG_PATH.$journey['passengers_log_id'].".png";
										}
									}
									$past_booking[$key]['map_image'] = $mapurl;
									$past_booking[$key]['distance_fare_km'] = ($journey["distance"] > 1) ? round($journey["amt"]/$journey["distance"],2) : $journey["amt"];
                                                                        $past_booking[$key]['amt'] = (string)$journey["amt"];
									$past_booking[$key]['vehicle_distance_fare'] = ($journey["distance"] > 1) ? round($past_booking[$key]['distance_fare_km']*$journey["distance"],2) : $journey["amt"];
									$time = explode(':', str_replace("Mins","",$journey["twaiting_hour"]));
									$secs = isset($time[0]) ? $time[0] : 0;
									$mins = isset($time[1]) ? $time[1] : 0;
									$waiting_time_per_hours = ($secs*3600) + ($mins*60)/60;
									$waiting_fare_per_hour = 0;
									if($journey["waiting_fare"] > 0 && $waiting_time_per_hours > 0) {
										$waiting_fare_per_hour = ($journey["waiting_fare"]*60)/$waiting_time_per_hours;
									}
									$past_booking[$key]['waiting_fare_per_hour'] = $waiting_fare_per_hour;
									$past_booking[$key]['final_amt'] = 0;
									unset($past_booking[$key]['active_record']);
								}
								
							}
							
							$detail = array("pending_booking"=>$driver_pending_bookings,"past_booking"=>$past_booking);
							$message = array("message" => __('success'),"detail"=>$detail,"status"=>1);						
						}
						else
						{
							$errors = $validator->errors('errors');	
							$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>2);							
						}												
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$past_booking,$file_path,$mapurl,$startMarker,$endMarker,$encodedString);
					break;
					
			case 'update_ratings_comments':
			$rating_array = $mobiledata;
					if(!empty($rating_array['pass_id']))
					{
						$validator = $this->update_ratings_comments_validation($rating_array);
						
						if($validator->check()) 
						{
							$pass_id= $rating_array['pass_id'];
							$ratings = $rating_array['ratings'];
							$comments = urldecode($rating_array['comments']);						
							$fav_driver_id = $api->savecomments($pass_id,$ratings,$comments);
							$setFavDriver = ($ratings < 4) ? 2 : 1;
							$message = ($ratings < 4) ? __('rate_comment_updated') : __('rate_msg_with_set_favorite');
							$message = array("message" => $message,"set_fav_driver" => $setFavDriver,"fav_driver_id" => $fav_driver_id,"status"=>1);
						}	
						else
						{							
							
							$errors = $validator->errors('errors');		
							$message = array("message" => __('validation_error'),"detail"=>$errors,"status"=>-2);	
						}																						
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$validator,$fav_driver_id);
					break;
				
				case 'set_favourite_driver':
					if(!empty($mobiledata['passenger_id']) && !empty($mobiledata['driver_id'])) {
						$chkdriveradded = $api->chkDriverAdded($mobiledata['passenger_id'],$mobiledata['driver_id']);
						if($chkdriveradded == 0) {
							$api->saveFavouriteDriver($mobiledata['passenger_id'],$mobiledata['driver_id']);
							$message = array("message" => __('fav_driver_saved'),"status"=>1);
						} else {
							$message = array("message" => __('fav_driver_added_already'),"status"=>-1);	
						}
					}
					else
					{
						$message = array("message" => __('invalid_request'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$chkdriveradded);
				break;
					
				case 'get_credit_card_details':
					$array = $mobiledata;
					$passenger_id = $array['passenger_id'];
					$default = $array['default'];
					$card_type = strtoupper($array['card_type']);
					if($array['passenger_id'] != null)
					{												
							$result = $api->get_creadit_card_details($passenger_id,$card_type,$default);
							if(count($result)>0)
							{
								$carddetails = array();
								if($default == 'yes')
								{									
									$plain_cardno = encrypt_decrypt('decrypt',$result[0]['creditcard_no']);
									$carddetails['creditcard_no'] = $plain_cardno;
									$carddetails['masked_creditcard_no'] = repeatx($plain_cardno,'X',4);
									$carddetails['expdatemonth'] = $result[0]['expdatemonth'];
									$carddetails['expdateyear'] = $result[0]['expdateyear'];
									$carddetails['creditcard_cvv'] = "";//$result[0]['creditcard_cvv'];
									$carddetails['masked_creditcard_cvv'] = "";//repeatx($result[0]['creditcard_cvv'],'X','All');		
									$carddetails['passenger_cardid'] = $result[0]['passenger_cardid'];
									$carddetails['card_type'] = $result[0]['card_type'];									
									$message = array("message" =>__('success'),"detail"=>$carddetails,"status"=>1);					
								}
								else
								{
									$i = 0;
									$alldetails = array();
									foreach($result as $value)
									{
										$plain_cardno = encrypt_decrypt('decrypt',$value['creditcard_no']);
										$carddetails['creditcard_no'] = $plain_cardno;
										$carddetails['masked_creditcard_no'] = repeatx($plain_cardno,'X',4);
										$carddetails['expdatemonth'] = $value['expdatemonth'];
										$carddetails['expdateyear'] = $value['expdateyear'];
										$carddetails['creditcard_cvv'] = "";//$value['creditcard_cvv'];
										$carddetails['masked_creditcard_cvv'] = "";//repeatx($value['creditcard_cvv'],'X','All');		
										$carddetails['default_card'] = $value['default_card'];
										$carddetails['passenger_cardid'] = $value['passenger_cardid'];										
										$carddetails['card_type'] = $value['card_type'];
										$alldetails[] = $carddetails;
										$i = $i+1;										
									}
									$message = array("message" =>__('success'),"detail"=>$alldetails,"status"=>1);	
								}								
							}
							else
							{
								$message = array("message" =>__('no_card'),"status"=>2);
							}
					}	
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);	
					}			
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			//unset($message,$carddetails,$alldetails);
			break;
			
			case 'credit_card_delete':
				if(!empty($mobiledata['passenger_cardid']) && !empty($mobiledata['passenger_id'])) {
					$favourite_details = $api->delete_credit_card($mobiledata['passenger_cardid'], $mobiledata['passenger_id']);
					if($favourite_details) {
						$message = array("message" => __('credit_card_deleted'),"status"=>1);
					} else {
						$message = array("message" =>__('invalid_card_id'),"status"=>2);
					}
				} else {
					$message = array("message" =>__('invalid_card_id'),"status"=>2);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$favourite_details);
			break;
			
			case 'update_driver_reply':
			$update_driver_array = $mobiledata;
					if($update_driver_array['pass_id'] != null)
					{
						$pass_id= $update_driver_array['pass_id'];
						$driver_reply= $update_driver_array['driver_reply'];
						
						$update_array = array("driver_reply"=>$driver_reply);
						
						$api->update_table(MDB_PASSENGERS_LOGS,$update_array,"passengers_log_id",$pass_id);
						$message = array("message" => __('get_another_taxi'),"status"=>1);
					}
					else
					{
						$message = array("message" =>  __('invalid_user'),"status"=>-1);
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$update_driver_array,$update_array);
					break;
					
			case 'driver_profile':
				$driver_array = $mobiledata;
				if($driver_array['userid'] != null)
				{
					$check_driver_login_status = $this->is_login_status($driver_array['userid'],$default_companyid);
					if($check_driver_login_status == 1)
					{ 
						
						$result = $api->driver_profile($driver_array['userid']);
						if(count($result) >0)
						{
							$driver_pending_amount = 0; $driver_referral_wallet_pending_amount = 0;
							if(isset($result[0]['company_id'])) {
								$referral_pending_result = $api->driver_referral_pending_amount($driver_array['userid']);
								if(count($referral_pending_result) > 0) {
									$driver_referral_wallet_pending_amount = ($referral_pending_result[0]["driver_referral_wallet_pending_amount"]) ? $referral_pending_result[0]["driver_referral_wallet_pending_amount"] : 0;
								}
								$pending_result = $api->driver_withdraw_pending_amount($result[0]['company_id'],$driver_array['userid']);
								if(count($pending_result) > 0) {
									$driver_pending_amount = ($pending_result[0]["pending_amount"]) ? $pending_result[0]["pending_amount"] : 0;
								}
							}
							$country_code = (!empty($result[0]['country_code'])) ? trim($result[0]['country_code']).'-':'';
							$name = $result[0]['name'];
							$salutation = $result[0]['salutation'];
							$email = $result[0]['email'];
							$phone = $country_code.$result[0]['phone'];
							$profile_picture = $result[0]['profile_picture'];
							$address = $result[0]['address'];
							$driver_license_id = $result[0]['driver_license_id'];
							$lastname = $result[0]['lastname'];
							$bankname = $result[0]['bankname'];
							$bankaccount_no = $result[0]['bankaccount_no'];
							$taxi_no = $result[0]['taxi_no'];
							$mapping_startdate = Commonfunction::getDateTimeFormat($result[0]['mapping_startdate'],1);
							$mapping_enddate = Commonfunction::getDateTimeFormat($result[0]['mapping_enddate'],1);
							$model_name = $result[0]['model_name'];
							$driverWalletAmount = $result[0]['driver_wallet_amount'];
							$driverAvailableAmount = $result[0]['account_balance'];
							/************************************Driver Image *******************************/
							$main_image_path = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DRIVER_IMGPATH.$profile_picture;
							$thumb_image_path = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DRIVER_IMGPATH.'thumb_'.$profile_picture;
							if(file_exists($main_image_path) && ($profile_picture !='')) {
								$driver_main_image = URL_BASE.SITE_DRIVER_IMGPATH.$profile_picture;
							} else {
								$driver_main_image = URL_BASE."/public/images/noimages109.png";
							}

							if(file_exists($thumb_image_path) && ($profile_picture !='')) {
								$driver_thumb_image = URL_BASE.SITE_DRIVER_IMGPATH.'thumb_'.$profile_picture;
							} else {
								$driver_thumb_image = URL_BASE."/public/images/noimages109.png";
							}

							$dresult = $api->driver_ratings($driver_array['userid']);
							$totalrating = 5;
							if(count($dresult) > 0)
							{
								$overall_rating = 5; $i = 0; $trip_total_with_rate = 1;
								foreach($dresult as $comments)
								{
									if($comments['rating'] != 0)
									$trip_total_with_rate++;
									$rating = $comments['rating'];
									$overall_rating += $comments['rating'];
									$i++;
								}
								if($trip_total_with_rate!=0 && $overall_rating!=0){
									$totalrating = $overall_rating/$trip_total_with_rate;
								}
								$totalrating = round($totalrating);
							}
							$driverWalletAmount =  round($driverWalletAmount - $driver_referral_wallet_pending_amount);
							$result = array(
								"salutation" => $salutation,
								"name" => $name,
								"lastname"=>$lastname,
								"bankname"=>$bankname,
								"bankaccount_no"=>$bankaccount_no,
								"email"=> $email,
								"phone"=>$phone,
								"main_image_path" => $driver_main_image,
								"thumb_image_path" => $driver_thumb_image,
								"address" => $address,
								"taxi_no" => $taxi_no,
								"taxi_map_from" => $mapping_startdate,
								"taxi_map_to" => $mapping_enddate,
								"taxi_model" => $model_name,
								"driver_license_id" => $driver_license_id,
								"driver_rating" => $totalrating,
								"driver_wallet_amount"=> (string)$driverWalletAmount,
								"driver_wallet_pending_amount"=> (string)$driver_referral_wallet_pending_amount,
								"trip_amount"=> $driverAvailableAmount,
								"trip_pending_amount"=> $driver_pending_amount,
								"total_amount"=> round($driverAvailableAmount - $driver_pending_amount,2)
							);
							$message = array("message" =>__('success'),"detail"=>$result,"status"=>1);	
						}
						else
						{
							$message = array("message" => __('invalid_user_driver'),"status"=>0);	
						}
					}
					else
					{
						$message = array("message" => __('driver_not_login'),"status"=>-1);
					}
				}
				else
				{
					$message = array("message" => __('invalid_user_driver'),"status"=>-1);	
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$result,$driver_thumb_image,$driver_main_image);
				break;

			case 'driver_login':
					$array = $mobiledata;
					
					if(!empty($array))
					{
						$array['company_id'] = $default_companyid;
						$array['user_type'] = 'D';
						$validator = $this->driver_login_validation($array);
						
						if($validator->check())
						{
							$phone_exist = $api->check_phone_people(urldecode($array['phone']),'D',$default_companyid);
							if($phone_exist == 0)
							{
								$message = array("message" =>  __('phone_not_exists'),"status"=> 2);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								exit;
							}		
							else
							{
								$result = $api->driver_login(urldecode($array['phone']),urldecode($array['password']),$default_companyid);
								if(count($result) > 0)
								{
									$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
									//Checking the User Status
									$user_status = $result[0]['status'];
									$login_status = $result[0]['login_status'];
									$login_from = $result[0]['login_from'];
									$device_token = $result[0]['device_token'];
									$device_id = $result[0]['device_id'];
									$company_id = $result[0]['company_id'];
									$driver_id = $result[0]['id'];
									$driver_first_login = $result[0]['driver_first_login'];
									$driver_details = $api->driver_profile($driver_id);
									$freeStatus = isset($driver_details[0]['status'])?$driver_details[0]['status']:'';
									if($user_status == 'D' || $user_status == 'T')
									{
										$message = array("message" => __('account_deactivte'),"status"=> 0);
									}
									else if(($login_status == 'S') && ($login_from == 'D') && ($device_id != $array['device_id']))
									{				
										$deviceType = $array['device_type'];
										$deviceToken = $array['device_token'];
										$deviceId = $array['device_id'];
										# force login from another device						
										if(isset($array['force_login']) && ($array['force_login'] == true)){
											
											# If driver in trip
											if($freeStatus != 'F'){
												$message = array("message" => __('driver_in_trip'),"status"=> -1);
												$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
											}
											
											$force_login = $array['force_login'];
											$datas  = array(
															'device_token'=> $deviceToken,
															'device_id'=> $deviceId,												
															'device_type'=> $deviceType												
														);
											//~ $login_status_update = $api->update_force_login($datas, $driver_id, $this->customer_google_api);
											$login_status_update = $this->commonmodel->update_force_login($datas, $driver_id);
											
											$driver_shift = $api->get_driver_currentshift($driver_id);
											$shiftupdate_id = $taxi_id = $driver_status ="";
											if(!empty($driver_shift)){
												$shiftupdate_id = $driver_shift['shift_id'];
												$driver_status = $driver_shift['driver_status'];
											}
											
											$getTaxiforDriver = $api->getTaxiforDriver($driver_id,$company_id);
											if(count($getTaxiforDriver) > 0 )
											{
												$taxi_id = $getTaxiforDriver[0]['mapping_taxiid'];
												/***** Check whether new trips or payment waiting trips is availavble for the driver ********/
												$trip_id = $travel_status = "";
												$get_driver_trip_details = $api->get_driver_log_details($driver_id,$company_id);
												$driver_trip_count = count($get_driver_trip_details);//exit;
												if($driver_trip_count > 0)
												{												
													foreach($get_driver_trip_details as $details)
													{
														$trip_id = $details->passengers_log_id;
														$travel_status = $details->travel_status;
														$driver_status =  ($travel_status != '9') ?  'A' : $driver_status;
													}												
												}	
												/*************************************************************************************/
												$driver_details[0]["shiftupdate_id"]=$shiftupdate_id;
												$driver_details[0]["taxi_id"]=$taxi_id;
												$driver_details[0]["trip_id"]=$trip_id;
												$driver_details[0]["travel_status"]=$travel_status;
												$driver_details[0]["driver_status"]=$driver_status;
												$driver_details[0]["shift_status"]='IN';
												// Driver Statistics ********************/
												$driver_cancelled_trips = $api->get_driver_cancelled_trips($driver_id,$company_id);
												$driver_logs_rejected = $api->get_rejected_drivers($driver_id,$company_id);
												$rejected_trips = count($driver_logs_rejected);
												$driver_earnings = $api->get_driver_earnings_with_rating($driver_id,$company_id);
												$driver_tot_earnings = $api->get_driver_total_earnings($driver_id);
												$statistics = array();
												$total_trip = $trip_total_with_rate = $total_ratings = $today_earnings = $total_amount=0;
												foreach($driver_earnings as $stat){
													$total_trip++;
													$total_ratings += $stat['rating'];
													$total_amount += $stat['total_amount'];
												}
												$overall_trip = $total_trip + $rejected_trips+	$driver_cancelled_trips;
												$time_driven = $api->get_time_driven($driver_id,'R','A','1');
												$statistics = array( 
													"total_trip" => $overall_trip,
													"completed_trip" => $total_trip,
													"total_earnings" => round($driver_tot_earnings,2),
													"overall_rejected_trips" => $rejected_trips,
													"cancelled_trips" => $driver_cancelled_trips,
													"today_earnings"=>round($total_amount,2),											
													"shift_status"=>'IN',
													"time_driven"=>$time_driven,
													"status"=> 1
												  );
												$driver_details[0]["driver_first_login"]=$driver_first_login;
												if($driver_first_login == 1) {
													$datas  = array("driver_first_login"=> 2);
													$login_status_update = $api_ext->update_driver_people($datas,$driver_id);
												}
												$driver_details[0]["driver_statistics"]=$statistics;	
												$details = array("driver_details"=>$driver_details);
												/**************************************************/

												/** Last Recent 3 trip start **/
												$trip_list = array(); $trip_array["driver_id"] = $driver_id;
												$trip_list = $api->get_recent_driver_trip_list($trip_array);
												if(count($trip_list) > 0){
													foreach($trip_list as $key => $val){
														$trip_list[$key]['drop_time'] = Commonfunction::getDateTimeFormat($val['drop_time'],1);
													}
												}
												/** Last Recent 3 trip end **/
												$message = array("message" => __('login_success'),"status"=> 1,"detail"=>$details,"recent_trip_list"=>$trip_list);		
												$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
											}
											else
											{
												$message = array("message" => __('taxi_not_assigned'),"status"=>-3);													
											}											
										}else{										
											$message = array("message" => __('already_login'),"status"=> 0);
										}
									}									
									else
									{
										$driver_status = 'F';
										$taxi_id = "";
										$getTaxiforDriver = $api->getTaxiforDriver($driver_id,$company_id);
										if(count($getTaxiforDriver) > 0 )
										{
											if(($login_status != 'S') && ($login_from != 'D') && ($device_id != $array['device_id']))
											{
												$update_array  = array("notification_setting"=>"1","login_from"=>"D","login_status"=>"S","device_id" => $array['device_id'],"device_token" => $array['device_token'],"device_type" => $array['device_type'],"notification_status"=>"1");							
												// Need for update labong settings automatically
												$login_status_update = $api->update_driver_phone($update_array,$driver_id,$default_companyid);
											}
											//Enable Driver Shift status
											$driver_reply = $api->update_driver_shift_status($driver_id,'IN');
											$taxi_id = $getTaxiforDriver[0]['mapping_taxiid'];
											//$company_all_currenttimestamp = $this->commonmodel->getcompany_all_currenttimestamp($company_id);
											$datas = array("driver_id" => $driver_id,
															"company_id" => $company_id,
															"taxi_id" => $taxi_id,
															"shift_end" => null,
															"reason" => null );
											
											$transaction = $api_ext->insert_driver_shiftservice($datas);	
											$shiftupdate_id = $transaction[0];
											/***** Check whether new trips or payment waiting trips is availavble for the driver ********/
											$trip_id = $travel_status = "";
											$get_driver_trip_details = $api->get_driver_log_details($driver_id,$company_id);
											$driver_trip_count = count($get_driver_trip_details);//exit;
											if($driver_trip_count > 0)
											{												
												foreach($get_driver_trip_details as $details)
												{
													$trip_id = $details->passengers_log_id;
													$travel_status = $details->travel_status;
													$driver_status =  ($travel_status != '9') ?  'A' : $driver_status;
												}												
											}	
											/*************************************************************************************/
											$driver_details[0]["shiftupdate_id"]=$shiftupdate_id;
											$driver_details[0]["taxi_id"]=$taxi_id;
											$driver_details[0]["trip_id"]=$trip_id;
											$driver_details[0]["travel_status"]=$travel_status;
											$driver_details[0]["driver_status"]=$driver_status;
											$driver_details[0]["shift_status"]='IN';
											// Driver Statistics ********************/
											$driver_cancelled_trips = $api->get_driver_cancelled_trips($driver_id,$company_id);
											$driver_logs_rejected = $api->get_rejected_drivers($driver_id,$company_id);
											$rejected_trips = count($driver_logs_rejected);
											$driver_earnings = $api->get_driver_earnings_with_rating($driver_id,$company_id);
											$driver_tot_earnings = $api->get_driver_total_earnings($driver_id);
											$statistics = array();
											$total_trip = $trip_total_with_rate = $total_ratings = $today_earnings = $total_amount=0;
											foreach($driver_earnings as $stat){
												$total_trip++;
												$total_ratings += $stat['rating'];
												$total_amount += $stat['total_amount'];
											}
											$overall_trip = $total_trip + $rejected_trips+	$driver_cancelled_trips;
											$time_driven = $api->get_time_driven($driver_id,'R','A','1');
											$statistics = array( 
												"total_trip" => $overall_trip,
												"completed_trip" => $total_trip,
												"total_earnings" => round($driver_tot_earnings,2),
												"overall_rejected_trips" => $rejected_trips,
												"cancelled_trips" => $driver_cancelled_trips,
												"today_earnings"=>round($total_amount,2),											
												"shift_status"=>'IN',
												"time_driven"=>$time_driven,
												"status"=> 1
											  );
											$driver_details[0]["driver_first_login"]=$driver_first_login;
											if($driver_first_login == 1) {
												$datas  = array("driver_first_login"=> 2);
												$login_status_update = $api_ext->update_driver_people($datas,$driver_id);
											}
											$driver_details[0]["driver_statistics"]=$statistics;	
											$details = array("driver_details"=>$driver_details);
											/**************************************************/

											/** Last Recent 3 trip start **/
											$trip_list = array(); $trip_array["driver_id"] = $driver_id;
											$trip_list = $api->get_recent_driver_trip_list($trip_array);
											if(count($trip_list) > 0){
												foreach($trip_list as $key => $val){
													$trip_list[$key]['drop_time'] = Commonfunction::getDateTimeFormat($val['drop_time'],1);
												}
											}
											/** Last Recent 3 trip end **/
											$message = array("message" => __('login_success'),"status"=> 1,"detail"=>$details,"recent_trip_list"=>$trip_list);		
										}
										else
										{
											$message = array("message" => __('taxi_not_assigned'),"status"=>-3);
										}												
									}
								}
								else
								{
									$message = array("message" => __('password_failed'),"status"=> -1);							
								}						
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							}
						}
						else
						{
							$errors = $validator->errors('errors');
							$message = array("message" => __('validation_error'),"status"=>-5,"detail"=>$errors);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							exit;
						}
					}
					else
					{
							$message = array("message" => __('invalid_request'),"status"=>-6);	
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);						
					}						
					//unset($message,$statistics,$driver_details,$api_ext);
					break;
				
					case 'driver_earnings':
						if(!empty($mobiledata['driver_id'])){
							$company_det =$api->get_company_id($mobiledata['driver_id']);
							if(count($company_det) > 0) {
								$default_companyid = $company_det[0]['company_id'];
								$check_driver_login_status = $this->is_login_status($mobiledata['driver_id'],$default_companyid);
								if($check_driver_login_status == 1)
								{
									$get_company_time_details = $api->get_company_time_details($default_companyid);
									$start_time = $get_company_time_details['start_time']; //Start time
									$end_time = $get_company_time_details['end_time']; //end time
									$current_time = $get_company_time_details['current_time'];
									$getTodayEarnings = $api->getTodayDriverEarnings($mobiledata['driver_id'],$start_time,$end_time);
									if(isset($getTodayEarnings[0]["total_amount"]) && $getTodayEarnings[0]["total_amount"] == null) {
										//unset($getTodayEarnings[0]["total_amount"]);
										$getTodayEarnings[0]["total_amount"] = 0;
									}
									$getTodayEarnings[0]["total_amount"] = !empty($getTodayEarnings[0]["total_amount"]) ? round($getTodayEarnings[0]["total_amount"],2) : 0;
									$getWeeklyReport = $api->getWeeklyWiseEarnings($mobiledata['driver_id'],$current_time);
									$message = array("message" => __('success'),"today_earnings"=>$getTodayEarnings,"weekly_earnings"=>$getWeeklyReport,"status"=>1);
								} else {
									$message = array("message" => __('driver_not_login'),"status"=>-1);
								}
							} else {
								$message = array("message" => __('invalid_user'),"status"=>-1);
							}
						} else {
							$message = array("message" => __('invalid_request'),"status"=>-1);
						}
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						//unset($message,$getWeeklyReport);
					break;
		
		case 'edit_driver_profile':					
					$d_personal_array = $mobiledata;
					if(!empty($d_personal_array))
					{
							$driver_id = $d_personal_array['driver_id'];
							if($d_personal_array["driver_id"] != null)
							{
								$validator = $this->edit_passenger_profile_validation($d_personal_array);						
								if($validator->check())
								{
									$d_email = urldecode($d_personal_array['email']);
									$d_phone = urldecode($d_personal_array['phone']);
									$password = urldecode($d_personal_array['password']);					
									$bankname = urldecode($d_personal_array['bankname']);
									$bankaccount_no = urldecode($d_personal_array['bankaccount_no']);
									$email_exist = $api->edit_check_email_people($d_email,$driver_id);
								    $phone_exist = $api->edit_check_phone_people($d_phone,$driver_id);
									if($email_exist > 0)
									{
										$message = array("message" => __('email_exists'),"status"=> 0);
									}
									else if($phone_exist > 0)
									{
										$message = array("message" => __('phone_exists'),"status"=> 2);
									}
									else
									{			
											if($d_personal_array['profile_picture'] != NULL)
											{
												/* Profile Update */
												$imgdata = base64_decode($d_personal_array['profile_picture']);
												
												$f = finfo_open();
												$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
												$mime_type = explode('/',$mime_type);												
												$mime_type = $mime_type[1];													
												$img = imagecreatefromstring($imgdata); 
												
												if($img != false)
												{                   
													$result = $api->driver_profile($d_personal_array['driver_id'],$default_companyid);
													if(count($result) >0)
													{
														$profile_picture = $result[0]['profile_picture'];
														$thumb_image = 'thumb_'.$profile_picture;
														$main_image_path = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DRIVER_IMGPATH.$profile_picture;
														$thumb_image_path = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DRIVER_IMGPATH.'thumb_'.$profile_picture;
														if(file_exists($main_image_path) &&($profile_picture != ""))
														{
															unlink($main_image_path);
														}
														if(file_exists($thumb_image_path) && ($thumb_image != ""))
														{
															unlink($thumb_image_path);
														}
													}		
													$mime_type = str_replace(' ', '_', $mime_type);	
													$image_name = uniqid().'.'.$mime_type;
													$thumb_image_name = 'thumb_'.$image_name;
													$image_url = DOCROOT.SITE_DRIVER_IMGPATH.'/'.$image_name;                    													
													$image_path = DOCROOT.SITE_DRIVER_IMGPATH.$image_name;  
													imagejpeg($img,$image_url);
													imagedestroy($img);
													chmod($image_path,0777);
													$d_image = Image::factory($image_path);
													$path11=DOCROOT.SITE_DRIVER_IMGPATH;
													Commonfunction::imageoriginalsize($d_image,$path11,$image_name,90);
													
													$path12=$thumb_image_name;
													Commonfunction::imageresize($d_image,PASS_THUMBIMG_WIDTH, PASS_THUMBIMG_HEIGHT,$path11,$thumb_image_name,90);
													if($password != "")
													{
														$update_array = array(	
														"id"=>$d_personal_array['driver_id'],						
														"salutation"=> urldecode($d_personal_array['salutation']),
														"name" => urldecode($d_personal_array['firstname']),
														"lastname" => urldecode($d_personal_array['lastname']),
														"email" => $d_email,
														"password" => md5($password),
														"org_password" => $password,
														"profile_picture" => $image_name);
													}
													else
													{
														$update_array = array(	
														"id"=> urldecode($d_personal_array['driver_id']),						
														"salutation"=> urldecode($d_personal_array['salutation']),
														"name" => urldecode($d_personal_array['firstname']),
														"lastname" => urldecode($d_personal_array['lastname']),
														"email" => $d_email,
														"profile_picture" => $image_name);
													}
													$bank_update_array = array(
													"id"=>$d_personal_array['driver_id'],
													"bankname" => $bankname,
													"bankaccount_no" => $bankaccount_no);
													$message = $api->edit_driver_profile($update_array,$default_companyid);
													$update_bank = $api->edit_company_profile($bank_update_array);                 
												}
												else
												{
													$message = array("message" => __('image_not_upload'),"status"=>4);								
												}
												
											}
											else
											{
													if($password != "")
													{
														$update_array = array(	
														"id"=> $d_personal_array['driver_id'],						
														"salutation"=> urldecode($d_personal_array['salutation']),
														"name" => urldecode($d_personal_array['firstname']),
														"lastname" => urldecode($d_personal_array['lastname']),
														"email" => $d_email,
														"password" => md5($password),
														"org_password" => $password);
													}
													else
													{
														$update_array = array(	
														"id"=>$d_personal_array['driver_id'],						
														"salutation"=>urldecode($d_personal_array['salutation']),
														"name" => urldecode($d_personal_array['firstname']),
														"lastname" => urldecode($d_personal_array['lastname']),
														"email" => $d_email);
													}
													$bank_update_array = array(
													"id"=>$d_personal_array['driver_id'],
													"bankname" => $bankname,
													"bankaccount_no" => $bankaccount_no);
													$message = $api->edit_driver_profile($update_array,$default_companyid);
													$update_bank = $api->edit_company_profile($bank_update_array);
											}
											/*****************************************/
																			
											if($message == 0)
											{												
												$result = $api->driver_profile($d_personal_array['driver_id']);						
												if(count($result) >0)
												{
													$driver_pending_amount = 0; $driver_referral_wallet_pending_amount = 0;
													if(isset($result[0]['company_id'])) {
														$referral_pending_result = $api->driver_referral_pending_amount($d_personal_array['driver_id']);
														if(count($referral_pending_result) > 0) {
															$driver_referral_wallet_pending_amount = ($referral_pending_result[0]["driver_referral_wallet_pending_amount"]) ? $referral_pending_result[0]["driver_referral_wallet_pending_amount"] : 0;
														}
														$pending_result = $api->driver_withdraw_pending_amount($result[0]['company_id'],$d_personal_array['driver_id']);
														if(count($pending_result) > 0) {
															$driver_pending_amount = ($pending_result[0]["pending_amount"]) ? $pending_result[0]["pending_amount"] : 0;
														}
													}													
													$driverWalletAmount = $result[0]['driver_wallet_amount'];
													$driverAvailableAmount = $result[0]['account_balance'];
												}												
												$result = array(
													"driver_wallet_amount"=> $driverWalletAmount,
													"driver_wallet_pending_amount"=> $driver_referral_wallet_pending_amount,
													"trip_amount"=> $driverAvailableAmount,
													"trip_pending_amount"=> $driver_pending_amount
												);
												$message = array("message" => __('profile_updated'),"status"=>1, "detail" => $result);	
											}	
											else
											{
												$message = array("message" => __('try_again'),"status"=>1);	
											}																				
										}				
									}
								else
								{							
									$errors = $validator->errors('errors');	
									$message = array("message" => __('validation_error'),"status"=>-5,"detail"=>$errors);		
								}
							}
							else
							{
								$message = array("message" => __('invalid_user_driver'),"status"=>-1);	
							}
					}
					else
					{
						$message = array("message" => __('invalid_request'),"status"=>-1);	
					}
					
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$main_image_path,$thumb_image_path);
					break;
					

			case 'chg_password_driver':
			$driver_chg_password = $mobiledata;
					if($driver_chg_password['id'] != null)
					{
						$validator = $this->chg_password_passenger_validation($driver_chg_password);
						
						if($validator->check())
						{
							$message = $api->chg_password_passenger($driver_chg_password,$default_companyid,'D');	
							
							switch($message){
								case -1 :
									$message = array("message" => __('confirm_new_same'),"status"=>-1);	
									break;
								case -2 :
									$message = array("message" => __('old_pass_incorrect'),"status"=>-2);
									break;
								case -3 :
									$message = array("message" => __('invalid_user'),"status"=>-3);
									break;
								case 1 :
									$message = array("message" => __('password_changed'),"status"=>1);	
									break;
								case -4 :
									$message = array("message" => __('old_new_pass_same'),"status"=>-4);	
									break;
								}
							
						}
						else
						{							
							$message = $validator->errors('errors');			
						}
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>0);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$validator);
					break;
					
			case 'getdriver_update':
						ignore_user_abort(true);
						$array = $mobiledata;
						$extended_api = Model::factory(MOBILEAPI_107_EXTENDED);
						$message = array();
						$trip_id = $array["passenger_tripid"];						
						$notification_time = $this->notification_time;							
						if($notification_time != 0 ){ $timeoutseconds = $notification_time;}else{$timeoutseconds = 15;}
						$timeout = $this->continuous_request_time;//$timeoutseconds; // timeout in seconds
						$microseconds = $timeout*1000000; //Seconds to microseconds 1 second = 1000000 
						$flag = 0;
						$now = time();
						$search_flag=0;						
						if((int)$trip_id != "") 
						{											
								$i = $actual_limit = 0;		
								
								while((time() - $now) < $timeout)
								{	
									$driver_status = $api->get_request_status($trip_id);	
									$driver_status_count=count($driver_status);									
									if($driver_status_count >0)
									{										
										$req_count=$driver_status_count*$timeoutseconds;
										$driver_reply = $driver_status[0]['status'];
										$trip_type = $driver_status[0]['trip_type'];//get booking type 1-Favourite booking, 0-Normal Booking
										$selected_driver_id = $driver_status[0]['selected_driver'];
										$available_drivers = explode(',',$driver_status[0]['total_drivers']);
										$rejected_timeout_drivers = explode(',',$driver_status[0]['rejected_timeout_drivers']);	
										$comp_result = array_diff($available_drivers, $rejected_timeout_drivers);
										
										$timeperdriver = 25;
										
										$timeout=count($available_drivers)*$timeperdriver+20;
										if($timeout < $this->continuous_request_time)
										{
											$timeout=$this->continuous_request_time;
										}
										$microseconds=$timeout*1000000;
										//to get drivers company timestamp
										$company_det =$api->get_company_id($selected_driver_id);
										if(count($company_det)>0){
											$company_all_currenttimestamp = $this->commonmodel->getcompany_all_currenttimestamp($company_det[0]['company_id']);											
										}
										//condition to check driver not updated for above 30seconds if it is means we should change the request to next driver
										$driver_not_updated = $api->check_driver_not_updated($selected_driver_id,$company_all_currenttimestamp);
										
										$time_difference = strtotime($company_all_currenttimestamp) - strtotime($driver_not_updated);
										if($time_difference > $timeperdriver && count($comp_result) != 0 && $driver_reply != '4') {
											$get_request_dets=$api->check_new_request_tripid("","",$trip_id,$selected_driver_id,$company_all_currenttimestamp,"");
											$actual_limit++;
										}
										if(count($comp_result) == 0)
										{
											$driver_reply  = 5;
										}

										if(!empty($driver_reply))
										{
											
											if($driver_reply == '3') 
											{
												$message = array("message" => __("request_confirmed_passenger"),"trip_id"=>$trip_id,"status"=>1);
												$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
												exit;
											}
											elseif($driver_reply == '4')
											{
												if($trip_type == 1) {
													$message = array("message" => __("fav_driver_not_available"),"status"=>4);
												} else {
													$message = array("message" => __("driver_busy"),"status"=>2);
												}
                                                                                                // version 6.2.3 update                                                 
                                                                                                $void_transaction_trip=$api->voidTransaction_for_trip($trip_id);
												$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
												exit;
											}
											elseif($driver_reply == '5')
											{
												if($trip_type == 1) {
													$message = array("message" => __("fav_driver_not_available"),"status"=>4);
												} else {
													$message = array("message" => __("driver_busy"),"status"=>2);
												}
                                                                                                // version 6.2.3 update                                                 
                                                                                                $void_transaction_trip=$api->voidTransaction_for_trip($trip_id);
												$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
												exit;
											}
											else 
											{				
												$message = array("message" => __('try_again'),"status"=>0);
											}
										}
										usleep(5000000);												
										$i = $i+5000000;										
										if($i == $microseconds)
										{
											$update_trip_array  = array(
												'status' => 4,
												'trip_id'=>$trip_id
											);
											$result = $extended_api->update_driver_request_details($update_trip_array);
											if($trip_type == 1) {
												$message = array("message" => __("fav_driver_not_available"),"status"=>4);
											} else {
												$message = array("message" => __("driver_busy"),"status"=>2);
											}
                                                                                        // version 6.2.3 update                                                 
                                                                                        $void_transaction_trip=$api->voidTransaction_for_trip($trip_id);
											$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
											exit;
										}							
									}
									else
									{
										$message = array("message" => __('try_again'),"status"=>0);	
										exit;
									}											
								}
																													
						}
						else
						{
							$message = array("message" => __('validation_error'),"status"=>0);	
						}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message);
				break;
            			
			case 'getdriver_reply':
				$array = $mobiledata;
				$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
				if($array['passenger_tripid'] != null)
				{
					$passenger_tripid = $array["passenger_tripid"];
					$get_passenger_log_det = $api->get_trip_detail_only($passenger_tripid);
					if(count($get_passenger_log_det) > 0)															    		
					{
						$driver_reply = $get_passenger_log_det[0]->driver_reply;
						if($driver_reply == 'A')
						{
							$detail = array("trip_id"=>$passenger_tripid,"driverdetails"=>"");
							$message = array("message" => __("request_confirmed_passenger"),"detail"=>$detail,"status"=>1);
						}
						else
						{
							$change_driver_status = $api->change_driver_status($passenger_tripid,'C');
							$send_drivernotification = $this->commonmodel->send_drivernotification($passenger_tripid);
							$update_trip_array  = array("status"=>'4',"trip_id" => $passenger_tripid);
							$result = $api_ext->update_driver_request_details($update_trip_array);
							// version 6.2.3 update
							$void_transaction_trip=$api->voidTransaction_for_trip($passenger_tripid);
                                                        
							$message = array("message" => __("request_canceled_passenger"),"status"=>3);
						}
					}
					else
					{
						$message = array("message" => __('invalid_trip'),"status"=>-1);	
					}
				}
				else
				{
					$message = array("message" => __('try_again'),"status"=>0);	
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$api_ext);
				break;
			
			case 'getpassenger_update':			
				$array = $mobiledata;				
				$validator = $this->getpassenger_update_validation($array);						
				if($validator->check())
				{
					$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
					$trip_id = isset($array["trip_id"])? $array["trip_id"]:'';
					if($trip_id == ''){
						$message = array("message" => __('invalid_trip'),"status"=>-1);	
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						exit;
					}
					$passenger_id = $array["passenger_id"];
					$request_type = $array['request_type'];
					$new_drop_lat = isset($array['drop_lat']) ? $array['drop_lat'] : 0;
					$new_drop_long = isset($array['drop_long']) ? $array['drop_long'] : 0;
					$drop_location_name = isset($array['drop_location_name']) ? urldecode($array['drop_location_name']) : "";
					if($request_type == 0)
					{						
						$secondaryPassNotify = 0;
						$arrived_display = $tripstart_display = $trip_complete_display = $tripfare_update_display = $driver_cancel_display = 0;
						
						//** update the drop latitude and longitude when the passenger chenged ahile in trip **//
						if(!empty($trip_id) && !empty($new_drop_lat) && !empty($new_drop_long)) {
							
							$datas  = array("notification_status"=>'22',"drop_latitude" => $new_drop_lat,
											"drop_longitude" => $new_drop_long,"drop_location" => $drop_location_name);											
							$updateDrop = $api_ext->update_passengerlogs($datas, $trip_id);
						}
						
						$amt="";$pickup="";
						$get_passenger_log_det = $api->get_request_detail($passenger_id,$trip_id);
						//~ print_r($get_passenger_log_det);exit;
						if(count($get_passenger_log_det) > 0)
						{
							$driver_reply = $get_passenger_log_det[0]->driver_reply;
							$travel_status = $get_passenger_log_det[0]->travel_status;
							$driver_id = $get_passenger_log_det[0]->driver_id;
							$transId = $get_passenger_log_det[0]->job_ref;
							$farePercent = $get_passenger_log_det[0]->fare_percentage;
							$amt = round($get_passenger_log_det[0]->amt,2);
							$splittedAmt = ($amt * $farePercent)/100;
							$pickup_location = $get_passenger_log_det[0]->pickup_location;
							$pickup_latitude = $get_passenger_log_det[0]->pickup_latitude;
							$pickup_longitude = $get_passenger_log_det[0]->pickup_longitude;
							$actual_pickup_time = $get_passenger_log_det[0]->actual_pickup_time;
							$drop_time = $get_passenger_log_det[0]->drop_time;
							$notification_status = $get_passenger_log_det[0]->notification_status;
							$primary_passenger = $get_passenger_log_det[0]->primary_passenger;
							$tripfare = round($get_passenger_log_det[0]->tripfare,2); # no need
							$base_fare = round($get_passenger_log_det[0]->base_fare,2);
							$minutes_fare = round($get_passenger_log_det[0]->minutes_fare,2);
							$waiting_fare = round($get_passenger_log_det[0]->waiting_fare,2);
							$nightfare = round($get_passenger_log_det[0]->nightfare,2);
							$eveningfare = round($get_passenger_log_det[0]->eveningfare,2);
							$company_tax = round($get_passenger_log_det[0]->company_tax,2);
							$passenger_discount = $get_passenger_log_det[0]->passenger_discount;
							$promo_discount_fare = $get_passenger_log_det[0]->promo_discount_fare;
							$used_wallet_amount = round($get_passenger_log_det[0]->used_wallet_amount,2);
							$split_wallet_amount = round($get_passenger_log_det[0]->split_wallet_amount,2);
							$isSplit_fare = (int)$get_passenger_log_det[0]->splitTrip;//0->Normal Trip, 1->Split Trip
							if(!empty($new_drop_lat) && !empty($new_drop_long)){								
								$driverDeviceDets = $api->getDriverDeviceToken($driver_id);
								if(count($driverDeviceDets) > 0 && !empty($driverDeviceDets[0]['device_token'])){
										
										$tripUpdateMSg = __('passenger_update_drop_location');
										$pushMessage = array("message" => $tripUpdateMSg,"drop_location"=>$drop_location_name,"drop_latitude"=>$new_drop_lat,"drop_longitude"=>$new_drop_long,"pickup_location"=>$pickup_location,"pickup_latitude"=>$pickup_latitude,"pickup_longitude"=>$pickup_longitude,"driver_notes"=>'',"badge"=>5);
										$this->commonmodel->send_pushnotification($driverDeviceDets[0]['device_token'],$driverDeviceDets[0]['device_type'],$pushMessage);
										if($isSplit_fare == 1){
											$api->splitted_pushnotification($pushMessage,$trip_id,$passenger_id);
										}
								}
							}
							$payment_type = ($get_passenger_log_det[0]->payment_type == 1) ? __('cash'):(($get_passenger_log_det[0]->payment_type == 5) ? __('wallet') : __('card'));
							/** secondary passengers approval status **/
							$splitApproveArr = array();
							if($primary_passenger == $passenger_id && $isSplit_fare == 1){
								$splitApproveArr = $api->getSplitFareStatus($trip_id);
								if(count($splitApproveArr) > 1){
									foreach($splitApproveArr as $splkey=>$splits){
										if((!empty($splits['profile_image'])) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.PASS_IMG_IMGPATH.$splits['profile_image'])){ 
											$profile_image = URL_BASE.PASS_IMG_IMGPATH.$splits['profile_image']; 
										}
										else{ 
											$profile_image = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
										}
										$splitApproveArr[$splkey]['profile_image'] = $profile_image;
									}
								}
							}
							/************** Driver Location ***************************/
							$driver_latitute = $driver_longtitute = '0.0';
							$current_driver_status = $api->get_driver_current_status($driver_id);
							if(count($current_driver_status)>0)
							{
								$trip_status = $current_driver_status[0]->status;
								$driver_latitute = $current_driver_status[0]->latitude;
								$driver_longtitute = $current_driver_status[0]->longitude;
							}				
							/**********************************************************/
							
							if(($driver_reply == 'A') && ($travel_status == 9))
							{
								$detail = array("trip_id"=>$trip_id,"driverdetails"=>"");
								$message = array("message" => __("request_confirmed_passenger"),"detail"=>$detail,"isSplit_fare"=>$isSplit_fare,"splitfaredetail"=>$splitApproveArr,"driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute,"status"=>1);
							}
							elseif(($driver_reply == 'A') && ($travel_status == 8))
							{
								$dispatcher_cancel_display = ($notification_status != 8) ?  1 : 0;
								$message = array("message" => __("dispatcher_trip_cancelled"),"detail"=>"","driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute,"status"=>10,"display"=>$dispatcher_cancel_display);
								$datas  = array("notification_status"=>'8');
								$result = $api->update_split_log_table($datas, $passenger_id, $trip_id);
							}
							elseif(($driver_reply == 'C') && ($travel_status == 6))
							{
								$message = array("message" => __("trip_cancel"),"detail"=>"","driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute,"status"=>7);
							}
							elseif(($driver_reply == 'C') && ($travel_status == 9))
							{
								$driver_cancel_display = ($notification_status != 5) ?  1 : 0;
								$message = array("message" => __("driver_cancel_after_confirm"),"detail"=>"","driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute,"status"=>8,"display"=>$driver_cancel_display);
								$datas  = array("notification_status"=>'5');
								$result = $api->update_split_log_table($datas, $passenger_id, $trip_id);								
							}
							elseif(($driver_reply == 'A') && ($travel_status == 3))
							{
								$arrived_display = ($notification_status != 1) ?  1 : 0;
								$message = array("message"=>__('passenger_on_board'),"isSplit_fare"=>$isSplit_fare,"splitfaredetail"=>$splitApproveArr,"trip_id"=>$trip_id,"driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute,"status"=>2,"display"=>$arrived_display);
								$datas  = array("notification_status"=>'1');
								$result = $api->update_split_log_table($datas, $passenger_id, $trip_id);
							}
							elseif(($driver_reply == 'A') && ($travel_status == 2))
							{
								$tripstart_display = ($notification_status != 2) ?  1 : 0;
								$actual_pickup_time = $this->commonmodel->getcompany_all_currenttimestamp($default_companyid);
								$message = array("message" =>__('journey_started'),"isSplit_fare"=>$isSplit_fare,"splitfaredetail"=>$splitApproveArr,"pickup_time"=>$actual_pickup_time,"trip_id"=>$trip_id,"driver_status"=>$trip_status,"driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute,"status"=>3,"display"=>$tripstart_display);
								$datas  = array("notification_status"=>'2');
								$result = $api->update_split_log_table($datas, $passenger_id, $trip_id);
							}
							elseif(($driver_reply == 'A') && ($travel_status == 5))
							{
								$trip_complete_display = ($notification_status != 3) ?  1 : 0;
								$message = array("message"=>__('trip_completed'),"driver_status"=>$trip_status,"driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute,"status"=>4,"display"=>$trip_complete_display);
								$update_trip_array  = array("notification_status"=>'3');
								$datas  = array("notification_status"=>'3');
								$result = $api->update_split_log_table($datas, $passenger_id, $trip_id);
							}
							elseif(($driver_reply == 'A') && ($travel_status == 1) && $transId != 0)
							{
								$promotion = ($splittedAmt * $passenger_discount) / 100;
								$promotion = round($promotion,2);
								
								$interval  = abs(strtotime($drop_time) - strtotime($actual_pickup_time));
								$minutes   = round($interval / 60);
								
								$card_amt = $splittedAmt;
								# New E-receipt details
								if($isSplit_fare == 1){									
									$payment_type = __('wallet');
									$used_wallet_amount = $split_wallet_amount;
									if($splittedAmt > $split_wallet_amount){
										$card_amt = $splittedAmt - $split_wallet_amount;
										$payment_type = __('card');
									}
								}
									
								$tripfare_update_display = ($notification_status != 4) ?  1 : 0;
								$message = array("message" => __('trip_fare_updated'),"fare" => $splittedAmt,"trip_id"=>$trip_id,"pickup" => $pickup_location, "status"=>5,"display"=>$tripfare_update_display,"driver_status"=>$trip_status,"driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute,"payment_type"=>$payment_type,"base_fare"=>$base_fare,"waiting_fare"=>$waiting_fare,"nightfare"=>$nightfare,"eveningfare"=>$eveningfare,"paid_amount"=>$card_amt,
								"promotion"=>$promo_discount_fare,"tax"=>$company_tax,"used_wallet_amount"=>$used_wallet_amount,"minutes_traveled"=>$minutes,"minutes_fare"=>$minutes_fare);
								$datas  = array("notification_status"=>'4');
								//~ print_r($message);exit;
								$result = $api->update_split_log_table($datas, $passenger_id, $trip_id);
							}
							elseif(($driver_reply == 'A') && ($travel_status == 4) && $primary_passenger != $passenger_id)
							{
								$tripCancel_display = ($notification_status != 9) ?  1 : 0;
								$message = array("message" => __('trip_cancel_by_primary'),"driver_status"=>$trip_status,"driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute, "status"=>9,"display"=>$tripCancel_display);
								
								$datas  = array("notification_status"=>'9');									
								$result = $api->update_split_log_table($datas, $passenger_id, $trip_id);
							}									
							else
							{
								$message = array("message"=>__('trip_not_started'),"driver_status"=>$trip_status,"driver_latitute"=>$driver_latitute,"driver_longtitute"=>$driver_longtitute,"status"=>6);
							}
							
						}
						else
						{
							$message = array("message" => __('invalid_trip'),"status"=>-1);
						}
					}
					elseif($request_type == 1)
					{
						$get_driver_request = $api->get_driver_request($trip_id);
						if(count($get_driver_request) >0)
						{
							$driver_reply = $get_driver_request[0]['status'];
							$trip_type = $get_driver_request[0]['trip_type'];//get booking type 1-Favourite booking, 0-Normal Booking
							$available_drivers = explode(',',$get_driver_request[0]['total_drivers']);
							$rejected_timeout_drivers = explode(',',$get_driver_request[0]['rejected_timeout_drivers']);
							$comp_result = array_diff($available_drivers, $rejected_timeout_drivers);

							if(count($comp_result) == 0)
							{
								$driver_reply  = 5;
							}
							
							if($driver_reply == '3')
							{
								$detail = array("trip_id"=>$trip_id,"driverdetails"=>"");
								$message = array("message" => __("request_confirmed_passenger"),"detail"=>$detail,"status"=>1);
							}
							elseif($driver_reply == '4')
							{
								$message = array("message" => __("trip_cancel"),"detail"=>"","status"=>7);
								// version 6.2.3 update                                                 
								$void_transaction_trip=$api->voidTransaction_for_trip($trip_id);
							}
							elseif($driver_reply == '5')
							{
								if($trip_type == 1) {
									$message = array("message" => __("fav_driver_not_available"),"status"=>4);
								} else {
									$message = array("message" => __("driver_busy"),"status"=>2);
								}
								// version 6.2.3 update                                                 
								$void_transaction_trip=$api->voidTransaction_for_trip($trip_id);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								exit;
							}
							else
							{
								$message = array("message"=>__('trip_not_started'),"status"=>6);
							}
						}
						else
						{
							$message = array("message" => __('invalid_trip'),"status"=>-1);	
						}
					}
					else
					{
						$message = array("message" => __('No Trips '),"status"=>-1);
					}
				}
				else
				{
						$errors = $validator->errors('errors');	
						$message = array("message" => __('validation_error'),"status"=>-5,"detail"=>$errors);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$get_driver_request,$api_ext);
				break;

			case 'gettriprequest_status':
				$array = $mobiledata;				
				if($array['trip_id'] != null)
				{
					$trip_id = $array["trip_id"];
					$amount="";$pickup="";
					
					$get_driver_request = $api->get_driver_request($trip_id);
					if(count($get_driver_request) >0)
					{
						$driver_reply = $get_driver_request[0]->status;
						if($driver_reply == '3')
						{
							$detail = array("trip_id"=>$trip_id,"driverdetails"=>"");
							$message = array("message" => __("request_confirmed_passenger"),"detail"=>$detail,"status"=>1);
						}
						elseif($driver_reply == '4')
						{
							$message = array("message" => __("trip_cancel"),"detail"=>"","status"=>7);
						}					
						else
						{
							$message = array("message"=>__('trip_not_started'),"status"=>6);
						}
					}
					else
					{
						$message = array("message" => __('invalid_trip'),"status"=>-1);
					}
				}
				else
				{
					$message = array("message" => __('trip_id_req'),"status"=>0);	
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$get_driver_request);
				break;								
								
			case 'driver_status_update':
				$driver_status_array = $mobiledata;
				$act_pickup_location = isset($driver_status_array['actual_pickup_location']) ? 	urldecode($driver_status_array['actual_pickup_location']) : '';
				$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
					if($driver_status_array['driver_id'] != null)
					{
						$check_driver_login_status = $this->is_login_status($driver_status_array['driver_id'],$default_companyid);
						if($check_driver_login_status == 1)
						{ 
							$driver_model = Model::factory('driver');
							$current_driver_status = $driver_model->get_driver_current_status($driver_status_array['driver_id']);
							if(count($current_driver_status) > 0)
							{
										$trip_details = array();
										$passengers_log_id = $driver_status_array['trip_id'];
										$update_driver_arrary  = array(
														"latitude" => $driver_status_array['latitude'],
														"longitude" => $driver_status_array['longitude'],
														"status" => strtoupper($driver_status_array['status']));						
										if($current_driver_status[0]->status != 'A')
										{								
											if(($driver_status_array['status'] == 'A') && ($passengers_log_id != null))
											{
											$get_passenger_log_details = $api->get_passenger_log_detail($passengers_log_id);
											foreach($get_passenger_log_details as $values)
											{
													$current_location = $values->current_location;	
													$pickup_latitude = $values->pickup_latitude;
													$pickup_longitude = $values->pickup_longitude;			
													$drop_location = $values->drop_location;	
													$drop_latitude= $values->drop_latitude;
													$drop_longitude = $values->drop_longitude;
													$driver_name = $values->driver_name;															
													$p_device_type = $values->passenger_device_type;
													$p_device_token  = $values->passenger_device_token;	
													$actual_pickup_time  = $values->actual_pickup_time;
													$travel_status = $values->travel_status;
													$driver_reply = $values->driver_reply;
													$notes = $values->notes_driver;
													$default_unit = ($values->default_unit == 0) ? "KM":"MILES";
													$default_unit = (FARE_SETTINGS == 2) ? $default_unit : UNIT_NAME;
											}
											/********** Check whther the Trip is alreadt cancelled by the passenger **********/
											if(($driver_reply == 'A') && ($travel_status == 4))
											{
												$message = array("message" => __("trip_cancelled_passenger"),"detail"=>"","status"=>7);
												$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
												exit;
											}
											/*********************************************************************************/
											
												/** update journey inprogress in Passenger log table when driver start the journey**/
												$company_det =$api->get_company_id($driver_status_array['driver_id']);
												$compId = (count($company_det) > 0) ? $company_det[0]['company_id'] : $default_companyid;
												$actual_pickup_time = $this->commonmodel->getcompany_all_currenttimestamp($compId);
												$travel_status = 2;
												$act_pickup_location=$api->getaddress($driver_status_array['latitude'],$driver_status_array['longitude']);
												   if($act_pickup_location == false)
												   {
														$act_pickup_location = $current_location;
												   }
												$act_pic_lat = ($driver_status_array['latitude'] != 0) ? $driver_status_array['latitude'] : $pickup_latitude;
												$act_pic_long = ($driver_status_array['longitude'] != 0) ? $driver_status_array['longitude'] : $pickup_longitude;

												$datas = array('travel_status' => $travel_status,
																'actual_pickup_time'=>$actual_pickup_time,
																'current_location'=>$act_pickup_location,
																'pickup_latitude'=>$act_pic_lat,
																'pickup_longitude'=>$act_pic_long);

												$result = $api_ext->update_passengerlogs($datas, $passengers_log_id);
												/** Passenger log table update end **/
												/*************** Update arrival in driver request table ******************/
												$datas  = array("status"=>'6','trip_id' => $passengers_log_id);
												$result = $api_ext->update_driver_request_details($datas);
												/*************************************************************************/	
												if(($driver_status_array['latitude'] != 0) &&($driver_status_array['longitude'] != 0))
												{
													$update_driver_arrary['driver_id'] = $driver_status_array['driver_id'];
													$result = $api_ext->update_driver_location($update_driver_arrary);
												}
												$trip_details = array("pickup_latitude"=>$driver_status_array['latitude'],"pickup_longitude"=>$driver_status_array['longitude'],"pickup_location"=>$act_pickup_location,"drop_latitude"=>$drop_latitude,"drop_longitude"=>$drop_longitude,"drop_location"=>$drop_location,"notes"=>$notes,"metric"=>$default_unit);
												$message = array("message" => __('driver_location_update'),"status"=>1,"detail"=>$trip_details);
												$push_message = array("message" =>__('journey_started'),"pickup_time"=>$actual_pickup_time,"trip_id"=>$passengers_log_id,"status"=>3);
											}	
											elseif(($driver_status_array['status'] == 'A') && ($passengers_log_id == null))
											{
												$message = array("message" => __('invalid_trip_id'),"status"=>-1,"detail"=>$trip_details);
											}
											else
											{
												
												if(($driver_status_array['latitude'] != 0) &&($driver_status_array['longitude'] != 0))
												{
													$update_driver_arrary['driver_id'] = $driver_status_array['driver_id'];
													$result = $api_ext->update_driver_location($update_driver_arrary);	
												}
												$message = array("message" => __('driver_location_update'),"status"=>1);
											}
										}
										else
										{
											$update_driver_arrary  = array(
											"latitude" => $driver_status_array['latitude'],
											"longitude" => $driver_status_array['longitude'],
											"status" => strtoupper($driver_status_array['status']));	
											if(($driver_status_array['latitude'] != 0 ) &&($driver_status_array['longitude'] != 0))
											{
												$update_driver_arrary['driver_id'] = $driver_status_array['driver_id'];
												$result = $api_ext->update_driver_location($update_driver_arrary);	
											}
											$message = array("message" => __('already_trip'),"status"=>-1);
										}
								}
								else
								{									
									$insert_array = array(
											"driver_id" => $driver_status_array['driver_id'],
											"latitude"		=> $driver_status_array['latitude'],
											"longitude"		=> $driver_status_array['longitude'],
											"status"			=> 'F',
											"shift_status" => 'OUT');									
									if(($driver_status_array['latitude'] != 0) &&($driver_status_array['longitude'] != 0))
									{
										$transaction = $api_ext->insert_driverinfo($insert_array);
									}
									$message = array("message" => __('driver_location_update'),"status"=>1);
								}
						}
						else
						{
							$message = array("message" => __('driver_not_login'),"status"=>-1);	
						}													
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$transaction,$get_passenger_log_details);
					break;
					
			case 'driver_reply':
			$driver_reply_array = $mobiledata;
					if($driver_reply_array['pass_logid'] != null)
					{
						$api_model = Model::factory(MOBILEAPI_107);
						$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
						$pass_logid = $driver_reply_array['pass_logid'];
						$driver_reply = $driver_reply_array['driver_reply'];
						$driver_id = $driver_reply_array['driver_id'];
						$taxi_id = $driver_reply_array['taxi_id'];
						$company_id = $driver_reply_array['company_id'];
						$field = $driver_reply_array['field'];
						$flag = $driver_reply_array['flag'];
						if($driver_reply == 'A'){$travel_status = 9;}elseif($driver_reply == 'R'){$travel_status=10;}else{$travel_status=9;}
						$driver_statistics=array();
						$result = $api_model->update_driverreply_status($pass_logid,$driver_id,$taxi_id,$company_id,$driver_reply,$travel_status,$field,$flag,$default_companyid);
						if($result == 1)
						{
							if($driver_reply == 'A')
							{
								/********* Update the status in driver request table **************/									
								$datas  = array("status"=>'3');
								$update_result = $api_ext->update_driverrequest($datas, $pass_logid);	
								
								/********** Update the Driver table he goes Busy status ****************/
								$datas  = array("status"=>'B');
								$update_driver_result = $api_ext->update_driver_driverinfo($datas ,$driver_id);
								
								//** Split fare push notification section **//
								$spltPassDetails = $api->getSplitPassengersDetails($pass_logid);
								if(count($spltPassDetails) > 0) {
									$primary_passenger_name = '';
									$primaryPassImage = '';
									foreach($spltPassDetails as $passengerDets){
										if($primary_passenger_name == '')
											$primary_passenger_name = $passengerDets['name'];
											
										if($primaryPassImage == '') {
											if(isset($passengerDets['profile_image']) && !empty($passengerDets['profile_image']) && file_exists(URL_BASE.PASS_IMG_IMGPATH.$passengerDets['profile_image'])) {
												$primaryPassImage = URL_BASE.PASS_IMG_IMGPATH.$passengerDets['profile_image'];
											} else {
												$primaryPassImage = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
											}
										}
										
										$pushMessage  = array("message"=>"You have a split fare request","trip_id"=>$pass_logid, "pickup_location"=>$passengerDets['current_location'], "drop_location"=>$passengerDets['drop_location'], "passenger_name"=>$primary_passenger_name, "primary_passenger_profile"=>$primaryPassImage, "total_fare"=>$passengerDets['total_fare'], "split_fare"=>$passengerDets['split_fare']);
										 if($passengerDets['primary_pass_id'] != $passengerDets['passenger_id']) {
											$api->send_pushnotification($passengerDets['device_token'],$passengerDets['device_type'],$pushMessage,$this->customer_google_api);
										 }
									}
								}
								/***************** Function to send the sms ***************************/	
								$laterBookings = $api_model->get_booking_details($pass_logid);
								if(count($laterBookings) > 0) {
									//** Email Section Starts **//
									
									$subject = __('later_booking_confirm_subjest');
									$name = $laterBookings[0]['name'];
									$message = __('later_booking_confirm_message');
									$message = str_replace("##booking_key##",$pass_logid,$message);
									$replace_variables=array(REPLACE_LOGO=>EMAILTEMPLATELOGO,REPLACE_SITENAME=>$this->app_name,REPLACE_USERNAME=>$name,REPLACE_SUBJECT=>$subject,REPLACE_MESSAGE=>$message,REPLACE_SITEEMAIL=>$this->siteemail,REPLACE_COMPANYDOMAIN=>$this->domain_name,REPLACE_SITEURL=>URL_BASE,REPLACE_COPYRIGHTS=>SITE_COPYRIGHT,REPLACE_COPYRIGHTYEAR=>COPYRIGHT_YEAR);
									if($this->lang!='en'){
										if(file_exists(DOCROOT.TEMPLATEPATH.$this->lang.'/laterbooking_confirm_message-'.$this->lang.'.html')){
											$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.$this->lang.'/laterbooking_confirm_message-'.$this->lang.'.html',$replace_variables);
										}else{
											$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'laterbooking_confirm_message.html',$replace_variables);
										}
									}else{
										$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'laterbooking_confirm_message.html',$replace_variables);
									}
									$to = $laterBookings[0]['email'];
									$from = $this->siteemail;
									$redirect = "no";	
									if($to != '') {
										if(SMTP == 1)
										{
											include($_SERVER['DOCUMENT_ROOT']."/modules/SMTP/smtp.php");
										}
										else
										{
											// To send HTML mail, the Content-type header must be set
											$headers  = 'MIME-Version: 1.0' . "\r\n";
											$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
											// Additional headers
											$headers .= 'From: '.$from.'' . "\r\n";
											$headers .= 'Bcc: '.$to.'' . "\r\n";
											mail($to,$subject,$message,$headers);	
										}
									} 
									//** Email Section Ends **//
									//** SMS Section Starts **//
									if(SMS == 1)
									{
										$message_details = $this->commonmodel->sms_message_by_title('booking_confirmed_sms');
										if(count($message_details) > 0) {
											$to = $laterBookings[0]['passenger_phone'];
											$message = (count($message_details)) ? $message_details[0]['sms_description'] : '';
											$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
											$message = str_replace("##booking_key##",$pass_logid,$message);
											$sms_response = $this->commonmodel->send_sms($to,$message);
										}
									}
									//** SMS Section Ends **//
								}								
							}
							$message = __('request_confirmed');	
							$push_msg = __('driver_confirm_push');
							$push_status = 1;
							$response_status = 1;							
						}
						else if($result == 2)		
						{	
							/********** Update the Driver table he goes Busy status ****************/
							$datas  = array("status"=>'F');
							$update_driver_result = $api_ext->update_driver_people($datas, $driver_id);		
							/**************************************************************************/							
							$message = __('request_rejected');
							$push_msg = __('request_rejected_passenger');
							$push_status = 6;
							$response_status = 2;	
                                                        // version 6.2.3 update
                                                        $void_transaction_trip=$api->voidTransaction_for_trip($pass_logid);
						}else if($result == 3)		
						{								
								/***************** Function to send the sms ***************************/	
								$laterBookings = $api_model->get_booking_details($pass_logid);
								if(count($laterBookings) > 0) {
									//** Email Section Starts **//
									
									$subject = __('booking_cancel_subject');
									$name = $laterBookings[0]['name'];
									$message = __('booking_cancel_message');
									$message = str_replace("##booking_key##",$pass_logid,$message);
									$replace_variables=array(REPLACE_LOGO=>EMAILTEMPLATELOGO,REPLACE_SITENAME=>$this->app_name,REPLACE_USERNAME=>$name,REPLACE_SUBJECT=>$subject,REPLACE_MESSAGE=>$message,REPLACE_SITEEMAIL=>$this->siteemail,REPLACE_COMPANYDOMAIN=>$this->domain_name,REPLACE_SITEURL=>URL_BASE,REPLACE_COPYRIGHTS=>SITE_COPYRIGHT,REPLACE_COPYRIGHTYEAR=>COPYRIGHT_YEAR);
									if($this->lang!='en'){
									if(file_exists(DOCROOT.TEMPLATEPATH.$this->lang.'/laterbooking_confirm_message-'.$this->lang.'.html')){
										$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.$this->lang.'/laterbooking_confirm_message-'.$this->lang.'.html',$replace_variables);
									}else{
										$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'laterbooking_confirm_message.html',$replace_variables);
									}
									}else{
										$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'laterbooking_confirm_message.html',$replace_variables);
									}
									$to = $laterBookings[0]['email'];
									$from = $this->siteemail;
									$redirect = "no";
									if($to != '') {
										if(SMTP == 1)
										{
											include($_SERVER['DOCUMENT_ROOT']."/modules/SMTP/smtp.php");
										}
										else
										{
											// To send HTML mail, the Content-type header must be set
											$headers  = 'MIME-Version: 1.0' . "\r\n";
											$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
											// Additional headers
											$headers .= 'From: '.$from.'' . "\r\n";
											$headers .= 'Bcc: '.$to.'' . "\r\n";
											mail($to,$subject,$message,$headers);
										}
									} 
									//** Email Section Ends **//
									//** SMS Section Starts **//
									if(SMS == 1)
									{
										$message_details = $this->commonmodel->sms_message_by_title('booking_cancelled_sms');
										if(count($message_details) > 0) {
											$to = $laterBookings[0]['passenger_phone'];
											$message = (count($message_details)) ? $message_details[0]['sms_description'] : '';
											$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
											$message = str_replace("##booking_key##",$pass_logid,$message);
											$this->commonmodel->send_sms($to,$message);
										}
									}
									//** SMS Section Ends **//
								}
								
								// Driver Statistics ********************/
								$driver_cancelled_trips = $api->get_driver_cancelled_trips($driver_id,$company_id);
								$driver_logs_rejected = $api->get_rejected_drivers($driver_id,$company_id);
								$rejected_trips = count($driver_logs_rejected);
								$driver_earnings = $api->get_driver_earnings_with_rating($driver_id,$company_id);
								$driver_tot_earnings = $api->get_driver_total_earnings($driver_id);
								$statistics = array();
								$total_trip = $today_earnings = $total_amount=0;
																
								foreach($driver_earnings as $stat){
								$total_trip++;
								$total_amount += $stat['total_amount'];
								}
								$overall_trip = $total_trip + $rejected_trips + $driver_cancelled_trips;
								$time_driven = $api->get_time_driven($driver_id,'R','A','1');
								$driver_statistics = array(
									"total_trip" => $overall_trip,
									"completed_trip" => $total_trip,
									"total_earnings" => round($driver_tot_earnings,2),
									"overall_rejected_trips" => $rejected_trips,
									"cancelled_trips" => $driver_cancelled_trips,
									"today_earnings"=>round($total_amount,2),
									"shift_status"=>'IN',
									"time_driven"=>$time_driven,
									"status"=> 1
								  );
							//Driver Statistics Functionality End
							/********** Update the Driver table he goes Busy status ****************/
							$datas  = array("status"=>'F');
							$update_driver_result = $api_ext->update_driver_driverinfo($datas, $driver_id);			
							/*************** Update in driver request table ******************/
							
							$update_trip_array  = array("status"=>'9');
							$result = $api_ext->update_driverrequest($update_trip_array, $pass_logid);			
                                                        // version 6.2.3 update
                                                        $void_transaction_trip=$api->voidTransaction_for_trip($pass_logid);
							/*************************************************************************/
							$message = __('trip_cancelled_driver');
							$push_msg = __('driver_cancel_after_confirm');
							$push_status = 7;
							$response_status = 3;
						}else if($result == 4)
						{
						$push_msg = $message = __('trip_already_cancel_rejected');
						$push_status = 8;
						$response_status = 4;
						}
						else if($result == 5){
							$push_msg = $message = __('trip_already_confirm');
							$push_status = 9;
							$response_status = 5;
						}
						else if($result == 6){
							$push_msg = $message = __('trip_already_rejected');
							$push_status = 10;
							$response_status =6;
						}
						else if($result == 7){
							$push_msg = $message = __('trip_cancel');
							$push_status = 11;
							$response_status = 7;
							// version 6.2.3 update
							$void_transaction_trip=$api->voidTransaction_for_trip($pass_logid);
						} else if($result == 10){
							$push_msg = $message = __('trip already confirm to other driver');
							$push_status = 12;
							$response_status = 8;
							$pass_logid = "";
						}
						else {
							$message = __('trip_cancel_timeout');
							$push_msg = __('trip_cancel_timeout');
							$push_status = 12;
							$response_status = 8;
						}

							$phone_no = '';
							$device_token = '';
							$driver_name = $p_device_token = $phone_no = $driver_phone = $p_device_type="";

						    $latitude = $longitude="";
						    $taxi_details = "";

							//free sms url with the arguments
							if((SMS == 1) && ($driver_phone !=''))
							{
								$message_details = $this->commonmodel->sms_message('3');
								$to = $driver_phone;
								$message_temp = $message_details[0]['sms_description'];
								$sms_message = str_replace("##booking_key##",$pass_logid,$message_temp);
							}										
							$totalrating = "";																
							$driverdetails = array();
							$trip_detail = array();
							$driverdetails=$api->get_passenger_log_detail_reply($pass_logid);
							foreach($driverdetails as $values)
							{
								if(isset($values->profile_image) && $values->profile_image)
								{
									$img = URL_BASE.PUBLIC_UPLOADS_FOLDER.'/passenger/thumb_'.$values->profile_image;
								}else{
									$img = URL_BASE."/public/images/noimages109.png";
								} 
								$values->profile_image=$img;
							}
							if($result == '10'){
								$pass_logid = "";
							}
							$detail = array("trip_id"=>$pass_logid,"driverdetails"=>$driverdetails,"driver_statistics"=>$driver_statistics);
							if($response_status == 1)
							{
								$message = array("message" => $message,"status" => $response_status,"detail"=>$detail);	
							}
							else
							{
								$message = array("message" => $message,"status" => $response_status,"driver_statistics"=>$driver_statistics);	
							}
							if($push_status == 1 || $push_status == 6 || $push_status == 7)
							{														
								if($push_status == 1)
								{															
									$push_message = array("message"=>$push_msg,"trip_id"=>$pass_logid,"driverdetails"=>$driverdetails,"status"=>$push_status);
								}
								else
								{
									$push_message = array("message"=>$push_msg,"trip_id"=>$pass_logid,"trip_detail"=>$trip_detail,"status"=>$push_status);
								}
							}
					}
					else
					{
						$message = array("message" => __('invalid_trip'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$replace_variables);
					break;

			case 'driver_status_select':
			$driver_status_array = $mobiledata;
					$check_result = $api->check_driver_companydetails($driver_status_array['driver_id'],$default_companyid);
					if($check_result == 0)	
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						exit;;
					}

					if($driver_status_array['driver_id'] != null)
					{																						
						$result = $api->select_driverloc($driver_status_array['driver_id'],$default_companyid);	
						$driver_details=array();
						$latitude = $longitude = '0.0';
						$status = 'F';
						if(count($result)>0)
						{
							foreach($result as $details)
							{
								$driver_status = $details['status'];	
								$id = $details['id'];			//				
								$shift_status = $details['shift_status'];
								$driver_id = $details['driver_id'];
								$latitude = $details['latitude'];
								$longitude = $details['longitude'];		
								$update_date = $details['update_date'];	
							}
							$driver_details = array(
									"id"=>$id,
									"driver_id"=>$driver_id,
									"latitude"=>$latitude,
									"longitude"=>$longitude,
									"status"=>$status,
									"shift_status"=>$shift_status,
									"update_date"=>$update_date);
						}
						
						$driver_current_journey = $api->get_driver_current_journey($driver_status_array['driver_id'],$default_companyid,'0');

						$trip_details=array();
						if(count($driver_current_journey)> 0)
						{
							foreach($driver_current_journey as $values)
								{
									$current_location = $values['current_location'];				
									$drop_location = $values['drop_location'];
									$current_latitude = $values['pickup_latitude'];
									$current_longitude = $values['pickup_longitude'];
									$drop_latitude = $values['drop_latitude'];
									$drop_longitude = $values['drop_longitude'];									
								}

							$trip_details = array(
									"pickup_location"=>$current_location,
									"drop_location"=>$drop_location,
									"current_latitude"=>$current_latitude,
									"current_longitude"=>$current_longitude,
									"drop_latitude"=>$drop_latitude,
									"drop_longitude"=>$drop_longitude
									);
						}
						else
						{
							$trip_details = array('No Trip Found.');
						}
						if(count($result) > 0)				
							$message = array("current_location" => $result,"current_trip"=>$trip_details,"status"=>1);		
						else
							$message = array("message" => 'Driver Not Found or Kindly update your status',"status"=>-1);
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$trip_details,$driver_current_journey);
					break;

				
			
			case 'driver_upcoming_journey':
			$driver_upcoming_journey = $mobiledata;
					$check_result = $api->check_driver_companydetails($driver_upcoming_journey['driver_id'],$default_companyid);
					if($check_result == 0)	
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						exit;
					}

					if($array['driver_id'] != null)
					{
						$driver_id = $driver_upcoming_journey['driver_id'];		
						$driver_logs_upcoming = $api->get_driver_logs($driver_id,'R','A','9',$default_companyid);		
						$array_inc = 0;
						foreach($driver_logs_upcoming as $journey)
						{
							$upcoming_journey[] = (array) $journey;		
							$pickuptime = date('H:i:s',strtotime($journey->pickup_time));	
							$currenttime = date('H:i:s',strtotime("+10 min"));	
							if($pickuptime <= $currenttime)
							{	
								$upcoming_journey = $this->array_push_assoc($upcoming_journey,$array_inc, 'pickstatus', 'P');
							}
							else
							{
								$upcoming_journey = $this->array_push_assoc($upcoming_journey,$array_inc, 'pickstatus', 'w');	
							}  
							$array_inc++;	
						}
						if(count($driver_logs_upcoming) == 0)
						{
							$message = array("message" => __('no_data'),"status"=>0);	
						}
						else
						{
							$message = array("message" => $upcoming_journey,"status" => 1);										
						}										
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$upcoming_journey,$driver_logs_upcoming);
					break;
				
			case 'driver_invite_with_referral':
				$driverId = isset($mobiledata['driver_id']) ? $mobiledata['driver_id'] : '';
				if(!empty($driverId)) {
					$check_driver_login_status = $this->is_login_status($driverId,$default_companyid);
					if($check_driver_login_status == 1)
					{ 
						$driverReferral = $api->getDriverReferralDetails($driverId);
						if(count($driverReferral) > 0) {
							$driverImage = $api->getDriverProfileImage($driverId);
							$drProfileImg = URL_BASE.PUBLIC_IMAGES_FOLDER."no_image109.png";
							if(!empty($driverImage) && file_exists(DOCROOT.SITE_DRIVER_IMGPATH.$driverImage)) {
								$drProfileImg = URL_BASE.SITE_DRIVER_IMGPATH.$driverImage;
							}
							$detail = array("referral_code" => $driverReferral[0]['registered_driver_code'],"referral_amount" => $driverReferral[0]['registered_driver_code_amount'],"profile_image"=>$drProfileImg);
							$message = array("message" => __('referral_amount'),"detail" => $detail,"status"=>1);
						} else {
							$message = array("message" => __('invalid_user'),"status"=>-2);
						}
					} else {
						$message = array("message" => __('driver_not_login'),"status"=>-1);
					}
					
				} else {
					$message = array("message" => __('invalid_request'),"status"=>-1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$driverImage,$drProfileImg,$driverReferral);
			break;
			
			case 'check_driver_referral_code':
				$driverId = isset($mobiledata['driver_id']) ? $mobiledata['driver_id'] : '';
				$driverReferralCode = isset($mobiledata['referral_code']) ? $mobiledata['referral_code'] : '';
				$extended_api = Model::factory(MOBILEAPI_107_EXTENDED);
				if(!empty($driverReferralCode) && !empty($driverId)) {
					$driverReferral = $api->checkDriverReferralExists($driverReferralCode);
					if(count($driverReferral) > 0) {
						$driverUsedReferral = $api->checkDriverUsedReferral($driverId);
						if($driverUsedReferral > 0) {
							//updates the referred driver's id and referral status in registered users row who is using the referral code
							$referralArr = array("referred_driver_id" => $driverReferral[0]['registered_driver_id'],'registered_driver_id'=>$driverId);
							$referUpdate = $extended_api->update_driver_referral_list($referralArr);
							if($referUpdate) {
								$message = array("message" => __('referral_code_save_successful'),"status"=>1);
							} else {
								$message = array("message" => __('try_again'),"status"=>-1);
							}
						} else {
							$message = array("message" => __('referral_code_already_used'),"status"=>-1);
						}
					} else {
						$message = array("message" => __('driver_referral_code_not_exists'),"status"=>-2);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status"=>-1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$referUpdate,$driverUsedReferral,$extended_api);
			break;
			
			case 'driver_wallet_request':
				$driverId = isset($mobiledata['driver_id']) ? $mobiledata['driver_id'] : '';
				$requestedAmount = isset($mobiledata['driver_wallet_amount']) ? $mobiledata['driver_wallet_amount'] : '';
				if(!empty($requestedAmount) && !empty($driverId)) {
					$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
					
					$company_det =$api->get_company_id($driverId);
					$companyId = (count($company_det) > 0) ? $company_det[0]['company_id'] : $default_companyid;
					$currentTime = $this->commonmodel->getcompany_all_currenttimestamp($companyId);
					$driverWalletDets = $api->getDriverReferralDetails($driverId);
					$existWalletAmt = (count($driverWalletDets) > 0) ? $driverWalletDets[0]['registered_driver_wallet'] : 0;					
					$driver_referral_wallet_pending_amount = 0;
					$referral_pending_result = $api->driver_referral_pending_amount($driverId);
					if(count($referral_pending_result) > 0) {
						$driver_referral_wallet_pending_amount = ($referral_pending_result[0]["driver_referral_wallet_pending_amount"]) ? $referral_pending_result[0]["driver_referral_wallet_pending_amount"] : 0;
					}
					$existWalletAmt = $existWalletAmt - $driver_referral_wallet_pending_amount;
					if($existWalletAmt >= $requestedAmount) {
						$result = $api->saveDriverWalletRequest($driverId,$requestedAmount,$currentTime);
						if($result) {
							$driver_referral_wallet_pending_amount = 0;
							$referral_pending_result = $api->driver_referral_pending_amount($driverId);
							if(count($referral_pending_result) > 0) {
								$driver_referral_wallet_pending_amount = ($referral_pending_result[0]["driver_referral_wallet_pending_amount"]) ? $referral_pending_result[0]["driver_referral_wallet_pending_amount"] : 0;
							}
							$wallArr = array("registered_driver_wallet" => $existWalletAmt,'registered_driver_id' => $driverWalletDets[0]['registered_driver_id']);
							$existWalletAmt =  $existWalletAmt - $requestedAmount;
							$walletUpdate = $api_ext->update_driver_referral_list($wallArr);
							$message = array("message" => __('driver_wallet_request_send'),"driver_wallet_amount"=>$existWalletAmt,"driver_wallet_pending_amount" => $driver_referral_wallet_pending_amount,"status"=>1);
						} else {
							$message = array("message" => __('try_again'),"status"=>-1);
						}
					} else {
						$message = array("message" => __('dont_have_sufficient_wallet_amount'),"status"=>-1);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status"=>-1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$api_ext);
			break;
			
			case 'driver_wallet':
				$driverId = isset($mobiledata['driver_id']) ? $mobiledata['driver_id'] : '';
				if(!empty($driverId)) {
					$check_driver_login_status = $this->is_login_status($driverId,$default_companyid);
					if($check_driver_login_status == 1)
					{
						$driverWallets = $api->getDriverReferralDetails($driverId);
						$walletAmt = isset($driverWallets[0]['registered_driver_wallet']) ? $driverWallets[0]['registered_driver_wallet'] : 0;
						$requestLists = $api->getWalletAmtRequests($driverId);
						$message = array("message" => __('wallet_details'),"wallet_amount"=>$walletAmt,"request_lists"=>$requestLists,"status"=>1);
					} else {
						$message = array("message" => __('driver_not_login'),"status"=>-1);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status"=>-1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message);
			break;

			case 'tell_to_friend_by_sms':
			$tell_sms_array = $mobiledata;
				$validator = $this->tellfri_sms_validation($tell_sms_array);				
				if($validator->check())
				{
					//Set the Favourite Trips
					$driver_details = $api->driver_profile($tell_sms_array['driver_id'],$default_companyid);
					$driver_referral_code="";
					if(count($driver_details)>0)
					{
						$driver_referral_code = $driver_details[0]['driver_referral_code'];
					}
					$message_details = $this->commonmodel->sms_message('7');
					$to = $tell_sms_array['phone'];
					$message = $message_details[0]['sms_description'];
					$message = str_replace("##SITENAME##",ucfirst(COMPANY_SITENAME),$message);
					$message = str_replace("##REFERRAL_CODE##",$driver_referral_code,$message);
					$message = str_replace("##ANDROID_PASSENGER_APP##",ANDROID_PASSENGER_APP,$message);
					$message = str_replace("##IOS_PASSENGER_APP##",IOS_PASSENGER_APP,$message);
					$message = str_replace("##ANDROID_DRIVER_APP##",ANDROID_DRIVER_APP,$message);
					$result = true;
					if($result)
					{
						$message = array("message" => __('sms_invite_send'),"status"=>1);	
					}
					else
					{
						$message = array("message" => __('try_again'),"status"=>0);	
					}
					
				}
				else
				{
						$validation_error = $validator->errors('errors');	
						$message = array("message" => __('validation_error'),"status"=>-3,"detail"=>$validation_error);										
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$validator);
				break;				
			break;	

			case 'tell_to_friend_by_email':
			$tell_mail_array = $mobiledata;
			$driver_id = $tell_mail_array['driver_id'];
			$email = $tell_mail_array['email'];					
			$validator = $this->tellfri_email_validation($tell_mail_array);
			if($validator->check())
			{			
					$name = $driver_referral_code = "";
					$driver_details = $api->driver_profile($tell_mail_array['driver_id'],$default_companyid);
					$driver_referral_code="";
					if(count($driver_details)>0)
					{
						$driver_referral_code = $driver_details[0]['driver_referral_code'];
						$name = $driver_details[0]['name'];
					}
					$message = DRIVER_TELL_TO_FRIEND_MESSAGE;
					$mail="";
					$subject = __('driver_telltofriend_subject').' '.$this->app_name;
					$replace_variables=array(REPLACE_LOGO=>EMAILTEMPLATELOGO,REPLACE_SITENAME=>$this->app_name,REPLACE_NAME=>$name,REPLACE_SUBJECT=>$subject,REPLACE_MESSAGE=>$message,REPLACE_SITEEMAIL=>$this->siteemail,REPLACE_SITEURL=>URL_BASE,REPLACE_COMPANYDOMAIN=>$this->domain_name,REPLACE_COPYRIGHTS=>SITE_COPYRIGHT,REPLACE_COPYRIGHTYEAR=>COPYRIGHT_YEAR);
					
			/* Added for language email template */
			if($this->lang!='en'){
			if(file_exists(DOCROOT.TEMPLATEPATH.$this->lang.'/driver_telltofriend-'.$this->lang.'.html')){
			$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.$this->lang.'/driver_telltofriend-'.$this->lang.'.html',$replace_variables);
			}else{
			$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'driver_telltofriend.html',$replace_variables);
			}
			}else{
			$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'driver_telltofriend.html',$replace_variables);
			}
			/* Added for language email template */
				$to = $email;
				$from = $this->siteemail;
				$redirect = "no";	
				if(SMTP == 1)
				{
					include($_SERVER['DOCUMENT_ROOT']."/modules/SMTP/smtp.php");
				}
				else
				{
					// To send HTML mail, the Content-type header must be set
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					// Additional headers
					$headers .= 'From: '.$from.'' . "\r\n";
					$headers .= 'Bcc: '.$to.'' . "\r\n";
					mail($to,$subject,$message,$headers);	
				}
				$message = array("message" => __('driver_tellfri_email_success'),"status"=> 1);	
		}
			else
			{
				$validation_error = $validator->errors('errors');	
				$message = array("message" => __('validation_error'),"status"=>-3,"detail"=>$validation_error);				
			}					
            $mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);	
            //unset($message,$validator,$replace_variables,$driver_details);
			break;

			case 'driver_statistics':
					if($mobiledata['driver_id'] != null)
					{
						$company_det =$api->get_company_id($mobiledata['driver_id']);
						
						$default_companyid = ($company_det > 0) ? $company_det[0]['company_id'] : $default_companyid;
						$check_driver_login_status = $this->is_login_status($mobiledata['driver_id'],$default_companyid);
						
						if($check_driver_login_status == 1)
						{
							$driver_id = $mobiledata['driver_id'];																		
							$driver_details = $api->driver_profile($driver_id);
							$driver_logs_rejected = $api->get_rejected_drivers($driver_id,$default_companyid);	
							$rejected_trips = count($driver_logs_rejected);	
							$driver_cancelled_trips = $api->get_driver_cancelled_trips($driver_id,$default_companyid);
							$driver_tot_earnings = $api->get_driver_total_earnings($driver_id);
							$driver_comments = $api->get_driver_earnings_with_rating($driver_id,$default_companyid);
							$today_goal = $amount_left = $today_earnings = 0;
							$goal_detail = $api->get_goal_details($driver_id,'R','A','1');
							$driver_shift_status = $api->get_driver_shift($driver_id);
							if(count($goal_detail)>0)
							{
								$today_earnings = $goal_detail[0]['acheive_amt'];
							}
								$statistics = array();
								$total_trip = $trip_total_with_rate = $total_ratings = $total_amount = 0;
								
								foreach($driver_comments as $stat){
									$total_trip++;
									$total_ratings += $stat['rating'];
									$total_amount += $stat['total_amount'];
									if($stat['rating'] != 0)
										$trip_total_with_rate++;
								}
								
								$time_driven = $api->get_time_driven($driver_id,'R','A','1');
								if(count($driver_details) > 0)
								{
									$drivername = ucfirst($driver_details[0]['name']).' '.ucfirst($driver_details[0]['lastname']);
									$notification_setting = $driver_details[0]['notification_setting'];
									$overall_trip = $total_trip + $rejected_trips + $driver_cancelled_trips;
									$statistics = array( 
												"drivername"=>$drivername,
												"total_trip" => $overall_trip,
												"completed_trip" => $total_trip,
												"total_earnings" => round($driver_tot_earnings,2),
												"overall_rejected_trips" => $rejected_trips,
												"cancelled_trips" => $driver_cancelled_trips,										
												"today_earnings"=>round($total_amount,2),											
												"shift_status"=> $driver_shift_status,
												"time_driven"=>$time_driven,
												"status"=> 1
											  ); 
									$message = array("message" => __('success'),"detail"=>$statistics,"status"=>1);
								}
								else
								{
									$message = array("message" => __('invalid_driver'),"status"=>2);
								}
						}
						else
						{
							$message = array("message" => __('driver_not_login'),"status"=>-1);
						}
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=>-1);	
					}
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					//unset($message,$time_driven);
					break;					

			case 'driver_shift_status':
			$array = $mobiledata;
			$validator = $this->shift_status_validation($array);
			if($validator->check())
			{
					$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);	
					
					$driver_id = $array['driver_id'];
					$company_status = $api->api_companystatus($array['driver_id']);
					if(($company_status == 'D') || ($company_status == 'T')){
						$message = array("message" => __('company_blocked_temp'),"status"=>-7);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						exit;
					}
					if($array['driver_id'] != null)
					{
					$check_result = $api->check_driver_companydetails($array['driver_id'],$default_companyid);
					if($check_result == 0)
					{
						$message = array("message" => __('company_deactivaed_driver'),"status"=>'-1');
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						exit;
					}
						$getTaxiassignedforDriver = $api->get_assignedtaxi_list($driver_id,$default_companyid);
						$current_driver_status = $api->get_driver_current_status($array['driver_id'],$default_companyid);
						$shiftstatus = $array['shiftstatus'];
								if($array['shiftstatus'] == 'IN')
								{
									if(count($getTaxiassignedforDriver)>0)
									{	
										$taxi_id = "";
										$getTaxiforDriver = $api->getTaxiforDriver($driver_id,$default_companyid);	
										if(count($getTaxiforDriver) > 0 )
										{
										$taxi_id = $getTaxiforDriver[0]['mapping_taxiid'];
										$driver_reply = $api->update_driver_shift_status($driver_id,$array['shiftstatus']);
										$datas = array(
														"driver_id" => $driver_id,
														"taxi_id" 			=> $taxi_id,		
														"shift_end"		=> "",
														"reason"		=> $array['reason'],
														"createdate"		=> $this->currentdate,
													);
										$transaction = $api_ext->insert_drivershift($datas, $default_companyid);	
										$insert_id = $transaction[0];
										if($transaction)
										{
												$detail = array("update_id"=>$insert_id);
												$message = array("message" => __('driver_shift'),"status"=>1,"detail"=>$detail);
										}
										else
										{
											$message = array("message" => __('try_again'),"status"=>-2);
										}
									   }	
									   else
									   {
											$message = array("message" => __('taxi_not_assigned'),"status"=>-3);
									   } 	
									}
									else
									{
										$message = array("message" => __('taxi_not_assigned'),"status"=>-3);
									}						
								}
								else
								{
									if($current_driver_status[0]->status != 'A')
									{
										$get_driver_log_details = $api->get_driver_log_details($driver_id,$default_companyid);
										$driver_trip_count = count($get_driver_log_details);//exit;
										if($driver_trip_count == 0)
										{
											$update_id = $array['update_id'];
											$update_arrary  = array("shift_end" => $company_all_currenttimestamp);
											if($update_id != "")
											{
												$transaction = $api_ext->update_drivershiftend($update_id, $default_companyid);
												$driver_reply = $api->update_driver_shift_status($driver_id,'OUT');
												if($transaction)
												{
													$message = array("message" => __('driver_shift_out'),"status"=>1);
												}
												else
												{
													$message = array("message" => __('try_again'),"status"=>-2);
												}
											}
											else
											{
												$message = array("message" => __('update_id_missing'),"status"=>-5);
											}
										}
										else
										{
											$message = array("message" => __('trip_in_future'),"status"=>-4);
										}
									}
									else
									{
										$message = array("message" => __('driver_in_trip'),"status"=>-1);
									}
								}
					}
					else
					{
						$message = array("message" => __('invalid_user_driver'),"status"=>-1);
					}
				}
				else
				{
					$validation_error = $validator->errors('errors');	
					$message = array("message" => __('validation_error'),"status"=>-3,"detail"=>$validation_error);				
				}
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			//unset($message,$api_ext);
			break;
			case 'driver_shift':
			$array = $mobiledata;
			
			$validator = $this->shift_status_validation($array);
			if($validator->check())
			{			
				$check_driver_login_status = $this->is_login_status($array['driver_id'],$default_companyid);				
				if($check_driver_login_status == 1)
				{					
					$driver_id = $array['driver_id'];
					$company_status = $api->api_companystatus($array['driver_id']);	
					if(($company_status == 'D') || ($company_status == 'T')){
						$message = array("message" => __('company_blocked_temp'),"status"=>-7);
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						exit;
					}
					if($array['driver_id'] != null)
					{						
						$getTaxiassignedforDriver = $api->get_assignedtaxi_list($driver_id,$default_companyid);				
						$current_driver_status = $api->get_driver_current_status($array['driver_id'],$default_companyid);
						$shiftstatus = $array['shiftstatus'];
						if($array['shiftstatus'] == 'IN')
						{ 
							if(count($getTaxiassignedforDriver)>0)
							{	
								$taxi_id = "";
								$taxi_id = $getTaxiassignedforDriver[0]['taxi_id'];														
								$update_shift_status = $api->update_driver_shift_status($driver_id,$array['shiftstatus']);
								if($update_shift_status != 0)
								{
									$message = array("message" => __('driver_shift'),"status"=>1);
								}
								else
								{
									$message = array("message" => __('try_again'),"status"=>-2);
								}	
							 }
							 else
							 {
								 $message = array("message" => __('taxi_not_assigned'),"status"=>-3);
							 }						
						}
						else
						{
							if($current_driver_status[0]->status != 'A')
							{
								$get_driver_log_details = $api->get_driver_log_details($driver_id,$default_companyid);
								$driver_trip_count = count($get_driver_log_details);//exit;
								if($driver_trip_count == 0)
								{
									$driver_reply = $api->update_driver_shift_status($driver_id,'OUT');
									if($driver_reply)
									{
										$message = array("message" => __('driver_shift_out'),"status"=>2);
									}
									else
									{
										$message = array("message" => __('try_again'),"status"=>-2);
									}
								}
								else
								{
									$message = array("message" => __('trip_in_future'),"status"=>-4);
								}
							}
							else
							{
								$message = array("message" => __('driver_in_trip'),"status"=>-1);
							}
						}
					}
					else
					{
						$message = array("message" => __('invalid_user_driver'),"status"=>-1);
					}
					
					}
				else
				{
					$message = array("message" => __('driver_not_login'),"status"=>-1);
				}
			}
			else
			{
				$validation_error = $validator->errors('errors');	
				$message = array("message" => __('validation_error'),"status"=>-3,"detail"=>$validation_error);				
			}
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			//unset($message,$getTaxiassignedforDriver,$get_driver_log_details,$current_driver_status);
			break;
						
		case 'complete_trip':
		$array = $mobiledata;
		$extended_api = Model::factory(MOBILEAPI_107_EXTENDED);
		if(!empty($array))
		{    
			$drop_latitude = $array['drop_latitude'];
			$drop_longitude = $array['drop_longitude'];
			$drop_location = urldecode($array['drop_location']);
			$trip_id = $array['trip_id'];
			$distance = $array['distance'];
			$actual_distance = $array['actual_distance'];
			$waiting_hours = $array['waiting_hour'];
			$driver_app_version = (isset($array['driver_app_version'])) ? $array['driver_app_version'] : '';
			if(!empty($trip_id))
			{
				$gateway_details = $this->commonmodel->gateway_details($default_companyid);
				$get_passenger_log_details = $api->get_passenger_log_detail($trip_id);
				
				$p_referral_discount = 0;
				$pickupdrop = $taxi_id = $company_id = 0;
				$fare_per_hour = $waiting_per_hour = $total_fare = $nightfare = 0;
				
				if(count($get_passenger_log_details) > 0)
				{
					
					/******* Check whether the trip is completed if so we change the driver status and trip travel status and give response **********/
					if($get_passenger_log_details[0]->transaction_id != 0)
					{
						$travel_status = $get_passenger_log_details[0]->travel_status;
						$driver_id = $get_passenger_log_details[0]->driver_id;
						if(($travel_status == 1 || $travel_status == 5 || $travel_status == 2))
						{
							/********** Update Driver Status after complete Payments *****************/
							$update_driver_array  = array(
								'status' => 'F',
								'driver_id'=>$driver_id
							);
							$result = $extended_api->update_driver_location($update_driver_array);
							/************Update Driver Status ***************************************/
							$message_status = 'R';$driver_reply='A';$journey_status=1; // Waiting for Payment
							$journey = $api->update_journey_status($trip_id,$message_status,$driver_reply,$journey_status);
							/*************** Update arrival in driver request table ******************/
							$update_driver_request_details  = array(
								'status' => 7,
								'trip_id'=>$trip_id
							);
							$result = $extended_api->update_driver_request_details($update_driver_request_details);
							/*************************************************************************/	
							$resMessage = ($travel_status == 1) ?  __('trip_fare_already_updated') : __('trip_fare_and_status_updated');
							$message = array("message" => $resMessage, "status"=>-1);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							//unset($message);
							break;
						}
					}
					else
					{
						$passenger_discount = (isset($get_passenger_log_details[0]->passenger_discount))?$get_passenger_log_details[0]->passenger_discount:0; //
						$passengers_id = $get_passenger_log_details[0]->passengers_id;
						$referred_by = $get_passenger_log_details[0]->referred_by;					
						$referrer_earned = $get_passenger_log_details[0]->referrer_earned;
						$company_tax = $get_passenger_log_details[0]->company_tax;
						$company_fare_calculation_type = $get_passenger_log_details[0]->fare_calculation_type;
						$tax = (FARE_SETTINGS != 2) ? TAX : $company_tax;
						$farecalculation_type = (FARE_SETTINGS != 2) ? FARE_CALCULATION_TYPE : $company_fare_calculation_type;
						$travel_status = $get_passenger_log_details[0]->travel_status;
						$splitTrip = $get_passenger_log_details[0]->is_split_trip;//0 - Normal trip, 1 - Split trip
						$total_distance = $get_passenger_log_details[0]->distance;
						$used_wallet_amount = $get_passenger_log_details[0]->used_wallet_amount;
						$promocode = $get_passenger_log_details[0]->promocode;
						$default_unit = ($get_passenger_log_details[0]->default_unit == 0) ? "KM":"MILES";
						$default_unit = (FARE_SETTINGS == 2) ? $default_unit : UNIT_NAME;
						$p_referral_discount = 0;
						$pickupdrop = $taxi_id = $company_id = 0;
						$fare_per_hour = $waiting_per_hour = $total_fare = $nightfare = 0;
						if(($travel_status == 2) || ($travel_status == 5))
						{
							$pickup = $get_passenger_log_details[0]->current_location;
							$drop = $get_passenger_log_details[0]->drop_location;
							$pickupdrop = $get_passenger_log_details[0]->pickupdrop;
							$taxi_id = $get_passenger_log_details[0]->taxi_id;
							$pickuptime = date('H:i:s', strtotime($get_passenger_log_details[0]->pickup_time));
							$actualPickupTime = date('H:i:s', strtotime($get_passenger_log_details[0]->actual_pickup_time));
							$company_id = $get_passenger_log_details[0]->company_id;
							$driver_id = $get_passenger_log_details[0]->driver_id;
							$approx_distance = $get_passenger_log_details[0]->approx_distance;
							$approx_fare = $get_passenger_log_details[0]->approx_fare;
							$fixedprice = $get_passenger_log_details[0]->fixedprice;
							$passengers_id = $get_passenger_log_details[0]->passengers_id;
							$referred_by = $get_passenger_log_details[0]->referred_by;			
							$actual_pickup_time = $get_passenger_log_details[0]->actual_pickup_time;
							$brand_type = $get_passenger_log_details[0]->brand_type;
							$passengerMobile = $get_passenger_log_details[0]->passenger_country_code.$get_passenger_log_details[0]->passenger_phone;
							$taxi_model_id = $get_passenger_log_details[0]->taxi_modelid;
							$taxi_fare_details = $api->get_model_fare_details($company_id,$taxi_model_id,$get_passenger_log_details[0]->search_city,$brand_type);
							if($travel_status != 5) {
								$drop_time = $this->commonmodel->getcompany_all_currenttimestamp($company_id);
							} else {
								$drop_time = $get_passenger_log_details[0]->drop_time;
							}
							
							/*************** Update arrival in driver request table ******************/
							$update_trip_array  = array(
								'status' => 7,
								'trip_id'=>$trip_id
							);
							$result = $extended_api->update_driver_request_details($update_trip_array);
							/*************************************************************************/	
							/** Update Driver Status **/
							if(($array['drop_latitude'] > 0 ) && ($array['drop_longitude'] > 0)){
								$update_driver_array  = array('latitude' => $array['drop_latitude'],'longitude' => $array['drop_longitude'],'status' => 'A','driver_id'=>$driver_id);
							}else{
								$update_driver_array  = array('status' => 'A','driver_id'=>$driver_id);
							}
							
							$result = $extended_api->update_driver_location($update_driver_array);
							/*********************/
							$base_fare = '0';
							$min_km_range = '0';
							$min_fare = '0';
							$cancellation_fare = '0';
							$below_above_km_range = '0';
							$below_km = '0';
							$above_km = '0';
							$night_charge = '0';
							$night_timing_from = '0';
							$night_timing_to ='0';
							$night_fare = '0';
							$evening_charge = '0';
							$evening_timing_from = '0';
							$evening_timing_to ='0';
							$evening_fare = '0';
							$waiting_per_hour = '0';
							$minutes_cost= '0';
							$farePerMin = '0';
							
							if(count($taxi_fare_details) > 0)
							{
								$base_fare = $taxi_fare_details[0]['base_fare'];
								$min_km_range = $taxi_fare_details[0]['min_km'];
								$min_fare = $taxi_fare_details[0]['min_fare'];
								$cancellation_fare = $taxi_fare_details[0]['cancellation_fare'];
								$below_above_km_range = $taxi_fare_details[0]['below_above_km'];
								$below_km = $taxi_fare_details[0]['below_km'];
								$above_km = $taxi_fare_details[0]['above_km'];
								$night_charge = $taxi_fare_details[0]['night_charge'];
								$night_timing_from = $taxi_fare_details[0]['night_timing_from'];
								$night_timing_to = $taxi_fare_details[0]['night_timing_to'];
								$night_fare = $taxi_fare_details[0]['night_fare'];
								$evening_charge = $taxi_fare_details[0]['evening_charge'];
								$evening_timing_from = $taxi_fare_details[0]['evening_timing_from'];
								$evening_timing_to = $taxi_fare_details[0]['evening_timing_to'];
								$evening_fare = $taxi_fare_details[0]['evening_fare'];
								$waiting_per_hour = $taxi_fare_details[0]['waiting_time'];
								$minutes_fare = $taxi_fare_details[0]['minutes_fare'];
								$farePerMin = $minutes_fare;
							}
							$roundtrip="No";
							if($pickupdrop == 1)
							{
								$roundtrip = "Yes";
							}
							// Minutes travelled functionlity starts here
							/********Minutes fare calculation *******/
							$interval  = abs(strtotime($drop_time) - strtotime($actual_pickup_time));
							$minutes   = round($interval / 60);       
							/********Minutes fare calculation *******/
							$baseFare = $base_fare;		
							$total_fare = $base_fare;
							if($farecalculation_type ==1 || $farecalculation_type ==3)
							{
								$baseFare = $base_fare;
								if($total_distance < $min_km_range)
								{
									//min fare has set as base fare if trip distance 
									$baseFare = $min_fare;
									$total_fare = $min_fare;
								}
								else if($total_distance <= $below_above_km_range)
								{
									$fare = $total_distance * $below_km;
									$total_fare  = 	$fare + $base_fare ;
								}
								else if($total_distance > $below_above_km_range)
								{
									$fare = $total_distance * $above_km;
									$total_fare  = 	$fare + $base_fare ;
								}
							}
							if($farecalculation_type ==2 || $farecalculation_type ==3)
							{
								/********** Minutes fare calculation ************/
								if($minutes_fare > 0)
								{
									$minutes_cost = $minutes * $minutes_fare;
									$total_fare  = $total_fare + $minutes_cost;
								}
								/************************************************/
							}
							 
							$trip_fare = round($total_fare,2);

							// Waiting Time calculation
							$waiting_cost = $waiting_per_hour * $waiting_hours;
							$waiting_cost = round($waiting_cost,2);
							$total_fare = $waiting_cost + $total_fare;

							$parsed = date_parse($actualPickupTime);
							$pickup_seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
							
							//Night Fare Calculation
							$parsed = date_parse($night_timing_from);
							$night_from_seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];

							$parsed = date_parse($night_timing_to);
							$night_to_seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];

							$nightfare_applicable = $date_difference=0;
							if ($night_charge != 0) 
							{
								
								$night_start_date ='';
								$night_end_date ='';

								$night_start_date= date('Y-m-d')." ".$night_timing_from;
								$night_timing_to_value=$night_timing_to;
								$night_timing_from_value=$night_timing_from;
								$night_end_date= date('Y-m-d')." ".$night_timing_to;
								
								if(strtotime($night_end_date) < strtotime($night_start_date))
								{
									$night_start_date=date('Y-m-d', strtotime('-1 day'))." ".$night_timing_from_value;
								}
								else
								{
									$night_start_date= date('Y-m-d')." ".$night_timing_from_value;
								}
								$trip_time = date('Y-m-d')." ".$actualPickupTime;
								if( strtotime($trip_time) >= strtotime($night_start_date) && strtotime($trip_time) <= strtotime($night_end_date))
								{
									$nightfare_applicable = 1;
									$nightfare = ($night_fare/100)*$total_fare;//night_charge%100;                                        
									$total_fare  = $nightfare + $total_fare;
								}
							}							
							//Evening Fare Calculation
							$parsed_eve = date_parse($evening_timing_from);
							$evening_from_seconds = $parsed_eve['hour'] * 3600 + $parsed_eve['minute'] * 60 + $parsed_eve['second'];

							$parsed_eve = date_parse($evening_timing_to);
							$evening_to_seconds = $parsed_eve['hour'] * 3600 + $parsed_eve['minute'] * 60 + $parsed_eve['second'];

							$eveningfare = 0; $evefare_applicable=$date_difference=0;
							if ($evening_charge != 0) 
							{
								if( $pickup_seconds >= $evening_from_seconds && $pickup_seconds <= $evening_to_seconds)
								{
									$evefare_applicable = 1;
									$eveningfare = ($evening_fare/100)*$total_fare;//night_charge%100;
									$total_fare  = $eveningfare + $total_fare;
								}
							}
							
							// Passenger individual Discount Calculation
							$discount_fare="0";								
							// Referral Discount Claculation
							$siteinfo_details = $api->siteinfo_details();
							
							$promo_discount = $promodiscount_amount = 0;
							if($promocode != "")
							{
								$promodetails = $api->getpromodetails($promocode,$passengers_id);
								if($promodetails > 0)
								{
									$promo_discount = $promodetails;
									$calculate_amt = ($promo_discount/100)*$total_fare;
									$discount_fare = $promo_discount;
									$promodiscount_amount = round($calculate_amt,2);
									$total_fare = $total_fare-$promodiscount_amount;
								}
								else 
								{
									$promodiscount = 0;
									$promodiscount_amount = 0;
								}
							}
							$referral_discount = $siteinfo_details[0]['referral_discount'];
							$referdiscount = 0;
							// Company Tax amount Calculation
							$tax_amount = "0";
							$total_fare=round($total_fare,2);
							if($tax > 0)
							{
								$tax_amount = ($tax/100)*$total_fare;//night_charge%100;
								$tax_amount = round($tax_amount,2);
								$total_fare =  $total_fare+$tax_amount;
								$tax_amount = number_format($tax_amount, 2, '.', '');
								$tax_amount = (string)$tax_amount;
							}
							$total_fare = ($fixedprice != 0) ? $fixedprice : $total_fare;
							$trip_fare = round($trip_fare,2);
							$total_fare = round($total_fare,2);
							$subtotal_fare = $total_fare;//to display the actual total trip fare in complete trip page
							$usedWalAmount = 0;
							$totalFareAmt = 0;
							$passenger_payment_option = 0;
							
							if($travel_status != 5) {
								//condition checked to avoid amount detection while trip is in waiting for payment status
								/** Referral amount detection if the passenger have amount in their wallet **/
								$show_credit_payment = 1;
								$totalPendingPercentage = $api->getpendingFarePercentage($trip_id);
								$splitPassDets = $api->getSplitPassengersDetails($trip_id);
								if(count($splitPassDets) > 0) {
									foreach($splitPassDets as $splitPass) {
										
										$farePercentage = ($splitPass['primary_pass_id'] == $splitPass['passenger_id']) ? ($splitPass['fare_percentage'] + $totalPendingPercentage) : $splitPass['fare_percentage'];
										
										$splittedFare = ($total_fare * $farePercentage) / 100;
										$splTotfare = round($splittedFare, 2);
										
										if($splitPass["approve_status"] == "A") {
											list($actusedWalAmount,$actTotalFare) = $this->deductWalletAmount($splitPass['passenger_id'],$splitPass['wallet_amount'],$siteinfo_details[0]['referral_settings'],$splTotfare,$usedWalAmount);
										} else {
											/** Secondary passenger percent & amount to Zero **/
											if($splitPass['primary_pass_id'] != $splitPass['passenger_id']) {
												$api->updateSecondaryPercentAmtPrimary($trip_id,$splitPass['passenger_id'],$splitPass['primary_pass_id']);
											}
											$actusedWalAmount = $actTotalFare = 0;
										}
										
										#update used wallet amount in passenger split details
										$api->updateUsedWalletAmount($actusedWalAmount,$splitPass['passenger_id'],$trip_id);
										$usedWalAmount = $usedWalAmount + $actusedWalAmount;
										$totalFareAmt = $totalFareAmt + $actTotalFare;
										$passenger_payment_option = ($splitPass['primary_pass_id'] == $splitPass['passenger_id']) ? $splitPass['passenger_payment_option'] : 0;
									}
								}
								$usedAmount = $usedWalAmount;
								$total_fare = $totalFareAmt;
								//to update the used wallet amount and  for a trip in passenger log table
								$message_status = 'R';$driver_reply='A';$journey_status=5; // Waiting for Payment
								$journey = $api->update_journey_statuswith_drop($trip_id,$message_status,$driver_reply,$journey_status,$drop_latitude,$drop_longitude,$drop_location,$drop_time,$total_distance,$waiting_hours,$tax,$driver_app_version,$usedAmount,$waiting_per_hour,$farePerMin);
								/** Referral amount detection if the passenger have amount in their wallet **/
								//update the wallet amount in referred driver's row
								$referredDriver = $api->getReferredDriver($driver_id);
								if($referredDriver > 0) {
									$driverReferral = $api->getDriverReferralDetails($referredDriver);
									if(count($driverReferral) > 0){
										$wallAmount = $driverReferral[0]['registered_driver_wallet'] + $driverReferral[0]['registered_driver_code_amount'];
										$update_referral_array  = array(
											'registered_driver_wallet' => $wallAmount,
											'registered_driver_id'=>$driverReferral[0]['registered_driver_id']
										);
										$result = $extended_api->update_driver_referral_list($update_referral_array);
										//update referrer earned status in registered driver's row while he completing his first trip
										$update_referral_array  = array(
											'referral_status' => 1,
											'registered_driver_id'=>$driver_id
										);
										$result = $extended_api->update_driver_referral_list($update_referral_array);
									}
								}
							} else {
								$usedAmount = $used_wallet_amount;
								$total_fare = $total_fare - $used_wallet_amount;
							}
							$referdiscount = 0;
							$discount_fare = round($discount_fare,2);
							$nightfare = round($nightfare,2);							
							if(SMS ==1)
							{
								$message_details = $this->commonmodel->sms_message_by_title('complete_trip');
								if(count($message_details) > 0) {
									$to = $passengerMobile;
									$message = $message_details[0]['sms_description'];
									$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
									$this->commonmodel->send_sms($to,$message);
								}
							}
							/** Update Driver Status End**/
							//variable to know whether the passenger have credit card
							$check_card_data = $api->check_passenger_card_data($passengers_id);
							$credit_card_sts = ($check_card_data == 0) ? 0:SKIP_CREDIT_CARD;
							//condition checked to remove creditcard key value from array
							if($credit_card_sts == 0) {
								//condition checked to remove credit card if the passenger dont have credit card details
								$smpleArr = array();
								foreach($gateway_details as $key=>$valArr) {
									if($valArr['pay_mod_id'] != 2) {
										$smpleArr[] = $valArr;
										if(SKIP_CREDIT_CARD == 0)
											break;
									}
								}
								$gateway_details = $smpleArr;
							}
							$passenger_payment_option_array = array();
							//if the completed trip is split fare type means dont show the new card option
							if($splitTrip == 1){
								//condition checked to remove credit card if the passenger dont have credit card details
								$smpleArr = array();
								foreach($gateway_details as $key=>$valArr){
									if($valArr['pay_mod_id'] != 1 && $valArr['pay_mod_id'] != 3) {
										$smpleArr[] = $valArr;
									}
								}
								$gateway_details = $smpleArr;
							} else {
								if(count($gateway_details) > 0 && $passenger_payment_option > 0) {
									foreach($gateway_details as $valArr) {
										if($passenger_payment_option == $valArr["pay_mod_id"]) {
											$passenger_payment_option_array[] = $valArr;
										}
									}
								}
							}
							
							$gateway_details = (count($passenger_payment_option_array) > 0) ? $passenger_payment_option_array : $gateway_details;
						
							//to change the payment mode detail if trip fare is zero
							if($total_fare == 0) {
								$gateway_details = array("0"=>array("pay_mod_id"=>"5","pay_mod_name"=>"Wallet","pay_mod_default"=>"1"));
							}
							
							//the hours value has been changed to seconds
							$convertSeconds = $waiting_hours * 3600;
							$converthours = floor($convertSeconds / 3600);
							$convertmins = floor(($convertSeconds - ($converthours*3600)) / 60);
							$convertsecs = floor($convertSeconds % 60);
							$waitH = ($converthours < 10) ? '0'.$converthours : $converthours;
							$waitM = ($convertmins < 10) ? '0'.$convertmins : $convertmins;
							$waitS = ($convertsecs < 10) ? '0'.$convertsecs : $convertsecs;
							$waitingTime = ($waitH != "00") ? $waitH.':'.$waitM.':'.$waitS.' Hours' :  $waitM.':'.$waitS.' Mins';
							$total_distance = ($total_distance == '') ? '0' : $total_distance;
							
							$detail = array("trip_id" => $trip_id,"pass_id"=>$passengers_id,"distance"=> $total_distance,"trip_fare"=>$trip_fare,"referdiscount"=>$referdiscount,"promo_discount_per"=>$promo_discount,"promodiscount_amount"=>$promodiscount_amount,"passenger_discount"=>$discount_fare,"nightfare_applicable"=>$nightfare_applicable,"nightfare"=>$nightfare,"eveningfare_applicable"=>$evefare_applicable,"eveningfare"=>$eveningfare,"waiting_time"=>$waitingTime,"waiting_cost"=>$waiting_cost,"tax_amount"=>$tax_amount,"subtotal_fare"=>$subtotal_fare,"total_fare"=>$total_fare,"gateway_details"=>$gateway_details,"pickup"=>$pickup,"drop"=>$drop_location,"company_tax"=>$tax,"waiting_per_hour" => $waiting_per_hour, "roundtrip"=> $roundtrip,"minutes_traveled"=>$minutes,"minutes_fare"=>$minutes_cost,"metric"=>$default_unit,"credit_card_status"=>$credit_card_sts,"wallet_amount_used"=>$usedAmount,"base_fare"=>$baseFare,"street_pickup"=>0,"fare_calculation_type"=>$farecalculation_type);
										
							$message = array("message"=>__('trip_completed_driver'),"detail"=>$detail,"status"=>4);
							/** Send Trip fare details to Driver ***/
							$d_device_token = $get_passenger_log_details[0]->driver_device_token;
							$d_device_type = $get_passenger_log_details[0]->driver_device_type;
							/** Send Trip fare details to Passenger ***/
							$pushmessage = array("message"=>__('trip_completed'),"status"=>4);
							$p_device_token = $get_passenger_log_details[0]->passenger_device_token;
							$p_device_type = $get_passenger_log_details[0]->passenger_device_type;
						}
						else if($travel_status == 1)
						{
							$message = array("message" => __('trip_already_completed'),"status"=>-1);	
						}		
						else
						{
							$message = array("message" => __('trip_not_started'),"status"=>-1);
						}
					}
				}
				else
				{
					$message = array("message" => __('invalid_trip'),"status"=>-1);	
				}
			}
			else
			{
				$message = array("message" => __('invalid_trip'),"status"=>-1);	
			}
		}
		else
		{
			$message = array("message" => __('invalid_request'),"status"=>-1);	
		}
		$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
		//unset($message,$detail,$extended_api,$gateway_details,$get_passenger_log_details,$taxi_fare_details,$totalPendingPercentage,$splitPassDets);
		break;
			
		case 'tripfare_update':
			$array = $mobiledata;
			$api_model = Model::factory(MOBILEAPI_107);
			$extended_api = Model::factory(MOBILEAPI_107_EXTENDED);
			
			$pay_mod_id = $array['pay_mod_id'];
			if($pay_mod_id == '1' ||  $pay_mod_id == '2' ||  $pay_mod_id == '4' ||  $pay_mod_id == '5')
			{
				$validator = $this->payment_validation($array);
			}
			else
			{
				$validator = $this->payment_validationwith_card($array);
			}
			$driver_statistics=array();
			if($validator->check())
			{
				$passenger_log_id = $array['trip_id'];
				if($array['actual_distance'] == "")
					$distance = $array['distance'];
				else
				$distance = $array['actual_distance'];
				$actual_amount = $array['actual_amount'];
				$remarks = $array['remarks'];
				$minutes_traveled=$array['minutes_traveled'];
				$minutes_fare=$array['minutes_fare'];
				$base_fare=$array['base_fare'];
				$trip_fare = $array['trip_fare']; // Trip Fare without Tax,Tips and Discounts
				$fare = round($array['fare'],2); // Total Fare with Tax,Tips and Discounts can editable by driver
				$tips = round($array['tips'],2); // Tips Optional
				$nightfare_applicable = $array['nightfare_applicable'];
				$nightfare = $array['nightfare'];
				$eveningfare_applicable = $array['eveningfare_applicable'];
				$eveningfare = $array['eveningfare'];
				$tax_amount = $array['tax_amount'];
				$tax_percentage = $array['company_tax'];
				$promodiscount_amount = $array['promodiscount_amount'];
				$fare_calculation_type = isset($array['fare_calculation_type']) ? $array['fare_calculation_type'] : FARE_CALCULATION_TYPE;
				
				// Actual amount means if any deviations in trip fare driver will update it manualy but now this is not required.
				$trip_fare = round($trip_fare,2);
				$total_fare = $fare;
				$amount = round($total_fare,2); // Total amount which is used for pass to payment gateways
				$get_passenger_log_details = $api->get_passenger_log_detail($passenger_log_id);
				$pre_transaction_id = "";
				if(count($get_passenger_log_details) > 0)
				{
					$promocode = $get_passenger_log_details[0]->promocode;
					if($promocode != ''){
						$passenger_id = isset($get_passenger_log_details[0]->passengers_id) ? $get_passenger_log_details[0]->passengers_id:'';
						$this->commonmodel->promocode_used_update($promocode,$passenger_id);
					}					
						
					$pre_transaction_id = isset($get_passenger_log_details[0]->pre_transaction_id) ? $get_passenger_log_details[0]->pre_transaction_id : "";
					$default_unit = ($get_passenger_log_details[0]->default_unit == 0) ? "KM":"MILES";
					$default_unit = (FARE_SETTINGS == 2) ? $default_unit : UNIT_NAME;
					$flag = 1;
					$trans_result = $api_model->check_tranc($passenger_log_id,$flag);
					if($trans_result == 1)
					{
						/********** Update Driver Status after complete Payments *****************/
						$drivers_id = $get_passenger_log_details[0]->driver_id;
						$update_driver_array  = array(
							'status' => 'F',
							'driver_id'=>$drivers_id
							);
						$result = $extended_api->update_driver_location($update_driver_array);
						/************Update Driver Status ***************************************/
						$message_status = 'R';$driver_reply='A';$journey_status=1; // Waiting for Payment
						$journey = $api->update_journey_status($passenger_log_id,$message_status,$driver_reply,$journey_status);
						/*************** Update in driver request table ******************/
						$update_trip_array  = array(
							'status' => 8,
							'trip_id'=>$passenger_log_id
							);
						$result = $extended_api->update_driver_request_details($update_trip_array);
						/*************************************************************************/	
						if(count($get_passenger_log_details) > 0)
						{
							$default_companyid = isset($get_passenger_log_details[0]->company_id) ? $get_passenger_log_details[0]->company_id : $default_companyid;
							// Driver Statistics ********************/
							$driver_logs_rejected = $api->get_rejected_drivers($drivers_id,$default_companyid);	
							$rejected_trips = count($driver_logs_rejected);	
							$driver_cancelled_trips = $api->get_driver_cancelled_trips($drivers_id,$default_companyid);
							$driver_earnings = $api->get_driver_earnings_with_rating($drivers_id,$default_companyid);
							$driver_tot_earnings = $api->get_driver_total_earnings($drivers_id);
							$driver_statistics = array();
							$total_trip = $trip_total_with_rate = $total_ratings = $today_earnings = $total_amount=0;

							foreach($driver_earnings as $stat){
								$total_trip++;
								$total_ratings += $stat['rating'];
								$total_amount += $stat['total_amount'];
							}
							$overall_trip = $total_trip + $rejected_trips + $driver_cancelled_trips;
							$time_driven = $api->get_time_driven($drivers_id,'R','A','1');
							$driver_statistics = array( 
								"total_trip" => $overall_trip,
								"completed_trip" => $total_trip,
								"total_earnings" => round($driver_tot_earnings,2),
								"overall_rejected_trips" => $rejected_trips,
								"cancelled_trips" => $driver_cancelled_trips,
								"today_earnings"=>round($total_amount,2),
								"shift_status"=>'IN',
								"time_driven"=>$time_driven,
								"status"=> 1
							);
						}
						else
						{
							$driver_statistics=array();
						}
						//Driver Statistics Functionality End
						$message = array("message" => __('trip_fare_already_updated'), "status"=>-1);
						$message['driver_statistics']=$driver_statistics;
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						break;
					}
					if($array['pay_mod_id'] == 1 || $array['pay_mod_id'] == 5)//5 for wallet payment
					{
						/** Return payment using payment gateway **/
						if($pre_transaction_id != "") {
							                                                            
                                                            // Payment gateway void transaction
                                                             $paymentresponse =[];
                                                                $code=0;
                                                                
                                                                if (class_exists('Paymentgateway')) {
                                                                    $paymentresponse = Paymentgateway::payment_gateway_connect('void',$pre_transaction_id);
                                                                    $payment_status=$paymentresponse['payment_status'];

                                                                } else {
                                                                    trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
                                                                }
								
						}
						//Inserting to Transaction Table 
						try {
							if($array['pay_mod_id'] == 5) {
								$update_commission = $this->commonmodel->update_commission($passenger_log_id,$trip_fare,ADMIN_COMMISSON);
							} else {
								$update_commission = $this->commonmodel->update_commission($passenger_log_id,$total_fare,ADMIN_COMMISSON);
							}
							$insert_array = array(
								"passengers_log_id" => (int)$passenger_log_id,
								"distance" 			=> urldecode($array['distance']),
								"actual_distance" 	=> urldecode($array['actual_distance']),
								"distance_unit" 	=> $default_unit,
								"tripfare"			=> $trip_fare,
								"fare" 				=> $fare,
								"tips" 				=> $tips,
								"waiting_cost"		=> $array['waiting_cost'],
								"passenger_discount"=> $array['passenger_discount'],
								"promo_discount_fare"=> $promodiscount_amount,
								"tax_percentage"	=> $tax_percentage,
								"company_tax"		=> $tax_amount,
								"waiting_time"		=> urldecode($array['waiting_time']),
								"trip_minutes"		=> $minutes_traveled,
								"minutes_fare"		=> $minutes_fare,
								"base_fare"			=> $base_fare,
								"remarks"			=> $remarks,
								"payment_type"		=> $array['pay_mod_id'],
								"amt"				=> $amount,
								"nightfare_applicable" => $nightfare_applicable,
								"nightfare" 		=> $nightfare,
								"eveningfare_applicable" => $eveningfare_applicable,
								"eveningfare" 		=> $eveningfare,
								"admin_amount"		=> $update_commission['admin_commission'],
								"company_amount"	=> $update_commission['company_commission'],
								"driver_amount"		=> $update_commission['driver_commission'],
								"trans_packtype"	=> $update_commission['trans_packtype'],
								"fare_calculation_type"	=> $fare_calculation_type
							);
							$check_trans_already_exist = $api->checktrans_details($passenger_log_id);
							if(count($check_trans_already_exist)>0)
							{
								$tranaction_id = $check_trans_already_exist[0]['id'];
								$update_transaction = $extended_api->update_transaction_table($insert_array,$tranaction_id);
								$jobreferral = $tranaction_id;
							}
							else
							{
								$transaction = $extended_api->insert_transaction_table($insert_array);
								$jobreferral = $transaction;
							}
							/********** Update Driver Status after complete Payments *****************/
							$drivers_id = $get_passenger_log_details[0]->driver_id;
							$update_driver_array  = array(
								'status' => 'F',
								'driver_id'=>$drivers_id
							);
							$result = $extended_api->update_driver_location($update_driver_array);
							/************Update Driver Status ***************************************/
							/*************** Update in driver request table ******************/
							$update_driver_request_array  = array(
								'status' => 8,
								'trip_id'=>$passenger_log_id
							);
							$result = $extended_api->update_driver_request_details($update_driver_request_array);
							/*************************************************************************/
							$pickup = $get_passenger_log_details[0]->current_location;
							if(SMS == 1)
							{
								$passenger_phone_no = $get_passenger_log_details[0]->passenger_country_code.$get_passenger_log_details[0]->passenger_phone;
								$message_details = $this->commonmodel->sms_message_by_title('payment_confirmed_sms');
								if(count($message_details) > 0) {
									$to = $passenger_phone_no;
									$message = $message_details[0]['sms_description'];
									$message = str_replace("##booking_key##",$passenger_log_id,$message);
									$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
									$this->commonmodel->send_sms($to,$message);
								}
							}
							
							$detail = array("fare" => $amount,"pickup" => $pickup,"jobreferral"=>$jobreferral,"trip_id"=>$passenger_log_id);
							$message = array("message" => __('trip_fare_updated'),"detail"=>$detail,"status"=>1);
							$pushmessage = array("message" => __('trip_fare_updated'),"fare" => $amount,"trip_id"=>$passenger_log_id,"pickup" => $pickup, "status"=>5);
							$send_mail_status = $this->send_mail_passenger($passenger_log_id,1);
						}
						catch (Kohana_Exception $e) {
							$message = array("message" => __('trip_fare_already_updated'), "status"=>-1);
						}
					}
					else if($array['pay_mod_id'] == 2)
					{
						$passengers_id = $get_passenger_log_details[0]->passengers_id;
						$card_type = '';
						$default = 'yes';
						$carddetails = $api->get_creadit_card_details($passengers_id,$card_type,$default);
						
						 if(count($carddetails)>0)
						 {
							$creditcard_no = encrypt_decrypt('decrypt',$carddetails[0]['creditcard_no']);
							$creditcard_cvv = 456;
							$expmonth = $carddetails[0]['expdatemonth'];
							$expyear = $carddetails[0]['expdateyear'];
							
							if($creditcard_no != "")
							{
								$array['default_unit'] = $default_unit;
								list($payment_status,$payment_response) = $this->trippayment($array,$default_companyid);//$account_id
								if($payment_status == 0)
								{
									$gateway_response = isset($payment_response)?$payment_response:'Payment Failed';
									$message = array("message" => $gateway_response, "gateway_response" =>$gateway_response,"status"=>0);
								}
								else if($payment_status == 3)
								{
									$message = array("message" => __('gve_credit_card_details'), "status"=>-2);
								}
								else if($payment_status == 1)
								{
									$tranaction_id = "";
									$check_trans_already_exist = $api->checktrans_details($passenger_log_id);
									if(count($check_trans_already_exist)>0)
									{
										$tranaction_id = $check_trans_already_exist[0]['id'];
									}
										$jobreferral = $tranaction_id;
										$pickup = $get_passenger_log_details[0]->current_location;
										$detail = array("fare" => $amount,"pickup" => $pickup,"jobreferral"=>$jobreferral,"trip_id"=>$passenger_log_id);
										$message = array("message" => __('trip_fare_updated'), "detail" => $detail,"status"=>1);	
										$pushmessage = array("message" => __('trip_fare_updated'),"fare" => $amount,"trip_id"=>$passenger_log_id,"pickup" => $pickup, "status"=>5);
										/*************** Update in driver request table ******************/
										$update_driver_request_array  = array(
											'status' => 8,
											'trip_id'=>$passenger_log_id
										);
										$result = $extended_api->update_driver_request_details($update_driver_request_array);
								}
								else if($payment_status == -1)
								{
									$message = array("message" => __('invalid_trip'),"status"=>-1);	
								}
								else if($payment_status == 7)
								{
									$message = array("message" => __('no_payment_gateway'),"status"=>-1);	
								}
							}
							else
							{
								$message = array("message" => __('no_creditcard'),"status"=>-9);
							} 
						 }		
						 else
						 {			 								
							 $message = array("message" => __('no_card'),"status"=>-9);
						 }
					}
					else if($array['pay_mod_id'] == 3)
					{
						$creditcard_no = $array['creditcard_no'];
						$creditcard_cvv = $array['creditcard_cvv'];
						$expmonth = $array['expmonth'];
						$expyear = $array['expyear'];
						$authorize_status =$api->isVAlidCreditCard($creditcard_no,"",true);
						if($authorize_status == 1)
						{
							$array['default_unit'] = $default_unit;
							list($payment_status,$payment_response) = $this->trippayment($array,$default_companyid);//$account_id
							if($payment_status == 0)
							{
								$gateway_response = isset($payment_response)?$payment_response:'Payment Failed';
								$message = array("message" => $gateway_response, "gateway_response" =>$gateway_response,"status"=>0);		
							}				
							else if($payment_status == 3)
							{
								$message = array("message" => __('gve_credit_card_details'), "status"=>-2);
							}
							else if($payment_status == 1)
							{
								$tranaction_id = "";
								$check_trans_already_exist = $api->checktrans_details($passenger_log_id);
								if(count($check_trans_already_exist)>0)
								{
									$tranaction_id = $check_trans_already_exist[0]['id'];
								}
								
								$jobreferral = $tranaction_id;
								$pickup = $get_passenger_log_details[0]->current_location;
								$detail = array("fare" => $amount,"pickup" => $pickup,"jobreferral"=>$jobreferral,"trip_id"=>$passenger_log_id);
								$message = array("message" =>  __('trip_fare_updated'), "detail" => $detail,"status"=>1);	
								$pushmessage = array("message" => __('trip_fare_updated'),"fare" => $amount,"trip_id"=>$passenger_log_id,"pickup" => $pickup, "status"=>5);
								/*************** Update in driver request table ******************/
								$update_driver_request_array  = array(
									'status' => 8,
									'trip_id'=>$passenger_log_id
								);
								$result = $extended_api->update_driver_request_details($update_driver_request_array);
								/*************************************************************************/								
								/** Send Trip fare details to Driver ***/
								$d_device_token = $get_passenger_log_details[0]->driver_device_token;
								$d_device_type = $get_passenger_log_details[0]->driver_device_type;
								$p_device_token = $get_passenger_log_details[0]->passenger_device_token;
								$p_device_type = $get_passenger_log_details[0]->passenger_device_type;
							}
							else if($payment_status == -1)
							{
								$message = array("message" => __('invalid_trip'),"status"=>-1);	
							}
						}
						else
						{
							$message = array("message" => __('invalid_card'),"status"=>-9);
						}
					}
					
					//Driver Statistics Functionality Start
					$driver_id = $get_passenger_log_details[0]->driver_id;
					$default_companyid = isset($get_passenger_log_details[0]->company_id) ? $get_passenger_log_details[0]->company_id : $default_companyid;
					// Driver Statistics ********************/
					$driver_logs_rejected = $api->get_rejected_drivers($driver_id,$default_companyid);	
					$rejected_trips = count($driver_logs_rejected);	
					$driver_cancelled_trips = $api->get_driver_cancelled_trips($driver_id,$default_companyid);
					$driver_earnings = $api->get_driver_earnings_with_rating($driver_id,$default_companyid);
					$statistics = array();
					$total_trip = $trip_total_with_rate = $total_ratings = $today_earnings = $total_amount=0;
					foreach($driver_earnings as $stat){
							$total_trip++;
							$total_ratings += $stat['rating'];
							$total_amount += $stat['total_amount'];											
					}
					
					$overall_trip = $total_trip + $rejected_trips + $driver_cancelled_trips;													
					$time_driven = $api->get_time_driven($driver_id,'R','A','1');	
					$driver_statistics = array( 
									"total_trip" => $overall_trip,
									"completed_trip" => $total_trip,
									"total_earnings" => round($total_amount,2),
									"overall_rejected_trips" => $rejected_trips,
									"cancelled_trips" => $driver_cancelled_trips,
									"today_earnings"=>round($total_amount,2),											
									"shift_status"=>'IN',
									"time_driven"=>$time_driven,
									"status"=> 1
								);
					/**************************************************/
					
				}
				else
				{
					$message = array("message" => __('invalid_trip'),"status"=>-1);
				}
			}
			else
			{
					$validation_error = $validator->errors('errors');	
					$message = array("message" => $validation_error,"status"=>-3);						
			}	
			//Driver Statistics Functionality End
			$message['driver_statistics']=$driver_statistics;
                         //Company count updated with Crm
                        if (CRM_UPDATE_ENABLE==1 && class_exists('Thirdpartyapi')) {
                           if (method_exists('Thirdpartyapi','crm_complete_trip_count')) {                                    
                                    $thirdpartyapi= Thirdpartyapi::instance();
                                    $thirdpartyapi->crm_complete_trip_count();                
                                }
                    }
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			//unset($message,$detail,$api_model,$extended_api,$get_passenger_log_details,$driver_statistics,$insert_array,$carddetails);
			break;			

			case 'cancel_trip':			
				$driver_model = Model::factory('driver');
				$api_model = Model::factory(MOBILEAPI_107);	
				$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);	
				$cancel_trip_array = ($mobiledata) ? $mobiledata : $_POST;		
				$passenger_log_id = $cancel_trip_array['passenger_log_id'];
				$remarks = $cancel_trip_array['remarks'];

				$check_travelstatus = $api_model->check_travelstatus($passenger_log_id);
				if($check_travelstatus == -1)
				{
					$message = array("message" => __('invalid_trip'),"status"=>3);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					break;
				}
				if($check_travelstatus == 4)
				{
					$message = array("message" => __('trip_already_canceled'), "status"=>-1);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					break;
				}			
				if($check_travelstatus == 2)
				{
					$message = array("message" => __('passenger_in_journey'), "status"=>-1);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					break;
				}
				
				$flag = 1;
				$trans_result = $api_model->check_tranc($passenger_log_id,$flag);
				if($trans_result == 1)
				{
					$message = array("message" => __('trip_fare_already_updated'), "status"=>-1);
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					break;
				}

				if($cancel_trip_array['passenger_log_id'] != null)
				{
					$get_passenger_log_det = $api_model->get_passenger_log_detail($passenger_log_id);
					$driver_id = $get_passenger_log_det[0]->driver_id;
					$passenger_id = $get_passenger_log_det[0]->passengers_id;
					$passenger_name = $get_passenger_log_det[0]->passenger_name;
					$passenger_email = $get_passenger_log_det[0]->passenger_email;
					$pickup_location = $get_passenger_log_det[0]->current_location;
					$is_split_trip = $get_passenger_log_det[0]->is_split_trip;
					$wallet_amount = $get_passenger_log_det[0]->wallet_amount;
					$cancel_trip_array['company_id'] = $get_passenger_log_det[0]->company_id;
					
					$cancellation_nfree = (FARE_SETTINGS == 2) ? $get_passenger_log_det[0]->cancellation_nfree : CANCELLATION_FARE;
					$status = "F";
					if(!empty($driver_id))
						$result = $api_model->update_driver_status($status,$driver_id);
					
					if($cancellation_nfree == 0 || empty($driver_id))
					{
						if(SMS == 1 && !empty($passenger_id))
						{
							$message_details = $this->commonmodel->sms_message_by_title('trip_cancel');
							if(count($message_details) > 0) {
								$to = $this->commonmodel->getuserphone('P',$passenger_email);
								$message = $message_details[0]['sms_description'];
								$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
								$this->commonmodel->send_sms($to,$message);
							}
						}
						$payment_types=0;
						$transaction_detail=$api_model->cancel_triptransact_details($cancel_trip_array,$cancellation_nfree,$payment_types,$driver_id);
						$pushmessage = array("message"=>__('trip_cancelled_passenger'), "status"=>2);
						$d_device_token = $get_passenger_log_det[0]->driver_device_token;
						$d_device_type = $get_passenger_log_det[0]->driver_device_type;
						$message = array("message" => __('trip_cancel_passenger'),"cancellation_from"=> __('Free'),"cancellation_amount"=> 0, "status"=>2);	//with out cancellation fee
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						
					}
					else
					{
						$total = $api_model->get_passenger_cancel_faredetail($passenger_log_id);
						$passengerReferrDet = $api->check_passenger_referral_amount($passenger_id);
						$referralAmt = (isset($passengerReferrDet[0]['referral_amount'])) ? $passengerReferrDet[0]['referral_amount'] : 0;
						$reducAmt = ($referralAmt != 0) ? ($wallet_amount - $referralAmt) : $wallet_amount;
						if($cancel_trip_array['pay_mod_id'] == 3 || ($wallet_amount > 0 && $reducAmt >= $total)) // By cash
						{
							try {
									$siteinfo_details = $api_model->siteinfo_details();
									$update_commission = $this->commonmodel->update_commission($passenger_log_id,$total,$siteinfo_details[0]['admin_commission']);
									$total = (empty($total)) ? 0 : $total;
									$datas = array(
										"passengers_log_id" => $passenger_log_id,
										"remarks"		=> $remarks,
										"payment_type"		=> $cancel_trip_array['pay_mod_id'],
										"amt"			=> $total,
										"fare"			=> $total,
										"admin_amount"		=> $update_commission['admin_commission'],
										"company_amount"	=> $update_commission['company_commission'],
										"trans_packtype"	=> $update_commission['trans_packtype']
									);
									$transaction = $api_ext->insert_transactioncoll($insert_array);
									
									$datas  = array("travel_status" => '4'); // Passenger Cancelled
									$result_sts_update = $api_ext->update_passengerlogs($datas, $passenger_log_id);
									$cancel_from = __('Cash');
									//to reduce the wallet amount while cancelling the trip
									if($wallet_amount >= $total){
										$balance_wallet_amount = $wallet_amount - $total;
										$datas = array("wallet_amount" => $balance_wallet_amount);
										$wallet_update = $api_ext->update_passengers($datas, $passenger_id);
										$cancel_from = __('Wallet');
									}
									
									if(SMS == 1 && !empty($passenger_id))
									{
										$message_details = $this->commonmodel->sms_message_by_title('trip_cancel');
										if(count($message_details) > 0) {
											$to = $this->commonmodel->getuserphone('P',$passenger_email);
											$message = $message_details[0]['sms_description'];
											$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
											$this->commonmodel->send_sms($to,$message);
										}
									}
									$pushmessage = array("message"=>__('trip_cancelled_passenger'), "status"=>2);
									$d_device_token = $get_passenger_log_det[0]->driver_device_token;
									$d_device_type = $get_passenger_log_det[0]->driver_device_type;
									$message = array("message" => __('trip_cancel_passenger'),"cancellation_from"=> $cancel_from,"cancellation_amount"=> $total, "status"=>1);
								}
								catch (Kohana_Exception $e) {
									$message = array("message" => __('try_again'), "status"=>3);
								}
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						}
						else
						{
							$card_type = '';
							$default = 'yes';
							$carddetails = $api->get_creadit_card_details($passenger_id,$card_type,$default);
							$no_default_card = $api->get_creadit_card_details($passenger_id,$card_type,"");
							if(count($carddetails)>0)
							{
								$payment_status = $this->cancel_trippayment($cancel_trip_array,$cancellation_nfree,$default_companyid);
								//$cancelArr = ($payment_status != 0) ? explode("#",$payment_status):'';
                                                                $cancelArr = explode("#",$payment_status);
								$payment_status = isset($cancelArr[0]) ? $cancelArr[0] : 0;
								$cancelAmount = isset($cancelArr[1]) ? $cancelArr[1] : 0;
								if($payment_status == 0)
								{									
									$gateway_response = isset($cancelAmount)?$cancelAmount:__('cancel_payment_failed');
									$message = array("message" => __('cancel_payment_failed'), "gateway_response" =>$gateway_response,"status"=>0);
									$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
									break;
								}
								else if($payment_status == 1)
								{
                                                                    // version 6.2.3 update
                                                                    $void_transaction_trip=$api->voidTransaction_for_trip($passenger_log_id);
									if(SMS == 1 && !empty($passenger_id))
									{										
										$message_details = $this->commonmodel->sms_message_by_title('trip_cancel');
										if(count($message_details) > 0) {
											$to = $this->commonmodel->getuserphone('P',$passenger_email);
											$message = $message_details[0]['sms_description'];
											$message = str_replace("##SITE_NAME##",SITE_NAME,$message);
											$this->commonmodel->send_sms($to,$message);
										}
									}
									$message = array("message" => __('trip_cancel_passenger'),"cancellation_from"=> __('credit_card'),"cancellation_amount"=> $cancelAmount, "status"=>1);
									$pushmessage = array("message"=>__('trip_cancelled_passenger'), "status"=>2);
									$d_device_token = $get_passenger_log_det[0]->driver_device_token;
									$d_device_type = $get_passenger_log_det[0]->driver_device_type;
									$send_mail_status = $this->send_cancel_fare_mail_passenger($cancelAmount, $passenger_name, $pickup_location, $passenger_email);
									$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);				
								}
								else if($payment_status == -1)
								{
									$message = array("message" => __('invalid_trip'),"status"=>3);	
									$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
									break;
								}
							} else if (count($carddetails) == 0 && count($no_default_card) > 0) {
								$message = array("message" => __('passenger_has_no_default_creditcard'),"status"=>5);	
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);		
								break;	
									
							} else {
									$message = array("message" => __('cancel_no_creditcard'),"status"=>4);	
									$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);		
									break;				
							}
						}
					}
				}
				else
				{
					$message = array("message" => __('invalid_trip'),"status"=>3);	
					$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
					break;
				}				
				//unset($message,$detail,$api_model,$extended_api,$transaction_detail,$get_passenger_log_det);
				break;	
				
			case 'driver_document_upload':
				$p_personal_array = $mobiledata;				
					if($p_personal_array['driver_document'] != NULL)
					{						
						$dirname = $p_personal_array['driver_id'];						
						$imgdata = base64_decode($p_personal_array['driver_document']);
						$f = finfo_open();
						$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);

						$mime_type = explode('/',$mime_type);
						$mime_type = $mime_type[1];
						$img = imagecreatefromstring($imgdata); 							
							if($img != false)
							{                   
								$image_name = uniqid().'.'.$mime_type;
								$thumb_image_name = 'thumb_'.$image_name;			
								$image_path = DOCROOT.PUBLIC_UPLOADS_FOLDER.'/'.$image_name; 					
								$image_url = DOCROOT.PUBLIC_UPLOADS_FOLDER.'/'.$image_name;                    								
								//header('Content-Type: image/jpeg');					
								//$image_path = DOCROOT.PUBLIC_UPLOADS_FOLDER.'/'.$image_name; 
								//echo  $image_path;exit;
								imagejpeg($img,$image_url);
								imagedestroy($img);
								chmod($image_path,0777);
								$d_image = Image::factory($image_path);
								$foldername = DOCROOT.PUBLIC_UPLOADS_FOLDER."/driver_documents/".$dirname."/";

								if (!file_exists($foldername)) {
									mkdir(DOCROOT.PUBLIC_UPLOADS_FOLDER."/driver_documents/".$dirname, 0777);
								}
								
								//function called to unlink previous files from the folder
								$api->previous_files_unlink($foldername);
								Commonfunction::imageresize($d_image,DRIVER_DOC_IMG_WIDTH, DRIVER_DOC_IMG_HEIGHT,$foldername,$image_name,90);
								chmod($foldername,0777);
								unlink($image_path);
								$message = array("message" => __('file_upload_success'),"status"=>1);
							} else {
								$message = array("message" => __('image_not_upload'),"status"=>-1);
							}
					} else {
						$message = array("message" => __('image_not_upload'),"status"=>-1);
					} 				
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$detail,$foldername,$image_path,$image_url,$d_image);
				exit;
		
			case 'forgot_password':												
					$array_values = $mobiledata;						
					$message="";
					if($array_values['user_type'] == 'P')
					{
						$check_fb_user = $api->check_fb_user($array_values['phone_no'],$default_companyid,$array_values['country_code']);
						
						if($check_fb_user > 0){
							$message = array("message" => __('fb_user'),'status' => 3);
							$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							break;
						}
						$phone_exist = $api->check_phone_passengers($array_values['phone_no'],$default_companyid,$array_values['country_code']);
						$phone_no = $array_values['country_code'].'-'.$array_values['phone_no'];
					}
					else
					{
						$phone_exist = $api->check_phone_people($array_values['phone_no'],'D',$default_companyid);
						$phone_no = $array_values['phone_no'];
					}
					if($phone_exist > 0)
					{
						$forgot_result = $api->get_passenger_details_phone($array_values,$default_companyid);
						if(count($forgot_result) > 0) 
						{ 	
							/* Added for language email template */
							/**To generate random key if user enter email at forgot password**/
							$random_key = text::random($type = 'alnum', $length = 7);
							//function to update new password
							$newPassUpdate = $api->new_password_update($array_values,$random_key,$default_companyid);				
							$email = $forgot_result[0]['email'];
							$replace_variables=array(REPLACE_LOGO=>URL_BASE.SITE_LOGO_IMGPATH.$this->domain_name.'_email_logo.png',REPLACE_SITENAME=>$this->app_name,REPLACE_USERNAME=>ucfirst($forgot_result[0]['name']),REPLACE_MOBILE=>$phone_no,REPLACE_PASSWORD=>$random_key,REPLACE_SITELINK=>URL_BASE.'users/contactinfo/',REPLACE_SITEEMAIL=>CONTACT_EMAIL,REPLACE_SITEURL=>URL_BASE,SITE_DESCRIPTION=>$this->app_description,REPLACE_COPYRIGHTS=>SITE_COPYRIGHT,REPLACE_COPYRIGHTYEAR=>COPYRIGHT_YEAR);
							$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'user-forgotpassword.html',$replace_variables);
							if($this->lang!='en'){
								if(file_exists(DOCROOT.TEMPLATEPATH.$this->lang.'/user-forgotpassword-'.$this->lang.'.html')){
									$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.$this->lang.'/user-forgotpassword-'.$this->lang.'.html',$replace_variables);
								}else{
									$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'user-forgotpassword.html',$replace_variables);
								}
							}else{
								$message=$this->emailtemplate->emailtemplate(DOCROOT.TEMPLATEPATH.'user-forgotpassword.html',$replace_variables);
							}
							$to = $email;
							$from = $this->siteemail;
							$subject = __('forgot_password_subject')." - ".$this->app_name;	
							$redirect = "no";
							
							if(SMTP == 1)
							{
								include($_SERVER['DOCUMENT_ROOT']."/modules/SMTP/smtp.php");
							}
							else
							{
								// To send HTML mail, the Content-type header must be set
								$headers  = 'MIME-Version: 1.0' . "\r\n";
								$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
								// Additional headers
								$headers .= 'From: '.$from.'' . "\r\n";
								$headers .= 'Bcc: '.$to.'' . "\r\n";
								mail($to,$subject,$message,$headers);
							}
							
							//free sms url with the arguments
							if(SMS == 1)
							{
								if($array_values['user_type']=='P')
								{
									$phoneno = $this->commonmodel->getuserphone('P',$email);
								}
								else
								{
									$phoneno = $this->commonmodel->getuserphone('D',$email);
								}
								$message_details = $this->commonmodel->sms_message_by_title('forgot_password_sms');
								if(count($message_details) > 0) {
									$to = $phoneno;
									$message = $message_details[0]['sms_description'];
									$message = str_replace("##PASSWORD##",$random_key,$message);
									$this->commonmodel->send_sms($to,$message);
								}
							}							
							$message = array("message" => __('forgot_pass_success'),'status' => 1);
						}
						else
						{
							$message = array("message" => __('invalid_user'),'status' => 2);
						}
					}
					else
					{
						$message = array("message" => __('invalid_user'),"status"=> 2);							
					}					
							
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$replace_variables);
				break;					
					
				case 'call_response':
					require_once(DOCROOT.'application/vendor/smsgateway/Services/Twilio.php');
					$from_number=$mobiledata['From'];
					$to_passenger_number=$api->getPassengerNumber($from_number);
					$to_driver_number=$api->getDriverNumber($from_number);
					if(count($to_passenger_number)>0){
						$to=$to_passenger_number[0]["passenger_phone"];
						$driverid=$to_passenger_number[0]["driver_id"];
						$twiliocall=$api->getDriverInfo($driverid);
						$twilio_no=$twiliocall[0]["twilio_number"];
					}else if(count($to_driver_number)>0){
						$to=$to_driver_number[0]["driver_phone"];
						$driverid=$to_driver_number[0]["driver_id"];
						$twiliocall=$api->getDriverInfo($driverid);
						$twilio_no=$twiliocall[0]["twilio_number"];
					}
					else{
						$message="No records found";
						$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
						exit;
					}
					header('Content-type: text/xml');					
					echo '<Response>
					<Dial callerId="'.$twilio_no.'">'.$to.'</Dial>
					</Response>';
				break;
			
				case 'reject_trip':
					$array = $mobiledata;
					$trip_id = $array['trip_id'];
					$reject_type = $array['reject_type'];
					$driver_id = $array['driver_id'];
					$taxi_id=$array['taxi_id'];
					$company_id= $array['company_id'];
					$api_ext = Model::factory(MOBILEAPI_107_EXTENDED);
					
				if($trip_id != "")
				{			
					$passenger_log_details = $api->get_trip_detail_only($trip_id);					
					if(count($passenger_log_details) >0)
					{							
						$post=array();
						$post['driver_id']=$driver_id;
						$post['passengers_id']=$passenger_log_details[0]->passengers_id;
						$post['passengers_log_id']=$trip_id;
						$post['reason']=$array['reason'];	
						$company_all_currenttimestamp = $this->commonmodel->getcompany_all_currenttimestamp($company_id);
						$post['createdate']= $company_all_currenttimestamp;
						$operator_id = $passenger_log_details[0]->operator_id;	
                                                 
						if($reject_type == 1)
						{	
							if($passenger_log_details[0]->driver_reply == 'R')
							{
								$message=__('trip_cancel_timeout');
								$message = array("message" => $message,"status" => '8');	
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
							} 
							else if ($passenger_log_details[0]->travel_status == 6) 
							{
								$message = array("message" => __('trip_already_canceled'), "status"=>4);
								$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
								break;
							}
							else
							{
								//push message for rejected driver
								$rejected_driver=$passenger_log_details[0]->driver_id;
								$passengers_log_id=$trip_id;
								$push_msg = __('request_rejected');
								$message = array("message"=>$push_msg,"trip_id"=>$passengers_log_id,"trip_detail"=>"","status"=>6);
								
								/********** Update Trip Status *****************/
								$driver_reply = "";
								$get_driver_request = $api->get_driver_request($trip_id);
								if(count($get_driver_request) >0)
								{
									/******* Update the driver id in */
									$rejection_type = 1;
									$prev_rejected_timeout_drivers = $get_driver_request[0]['rejected_timeout_drivers'];
									$status = $get_driver_request[0]['status'];
									
									# increase actual_limit
									$actual_limit = $get_driver_request[0]['actual_limit'];
									$driver_limit = $get_driver_request[0]['driver_limit'];
									$actual_limit++;
									if($actual_limit >= $driver_limit){
										$api->update_tripdriverlimit($trip_id);
									}
									$api->update_driverlimit($trip_id,$actual_limit);
									$get_request_dets=$api->check_new_request_tripid($taxi_id,$company_id,$trip_id,$driver_id,$company_all_currenttimestamp,"",$operator_id);
									
									if($prev_rejected_timeout_drivers != "")
									{
										$rejected_timeout_drivers = $prev_rejected_timeout_drivers.','.$driver_id;
									}
									else
									{
										$rejected_timeout_drivers = $driver_id;
									}
									
									if($status != '4')
									{										
										$update_trip_array  = array("status"=>'0',"rejected_timeout_drivers" => $rejected_timeout_drivers);
									}
									$add_rejected_list = $api->add_rejected_list($post,$rejection_type);
									// Driver Statistics ********************/
									$driver_logs_rejected = $api->get_rejected_drivers($driver_id,$company_id);	
									$rejected_trips = count($driver_logs_rejected);	
									//to get cancelled trip counts from drivers
									$driver_cancelled_trips = $api->get_driver_cancelled_trips($driver_id,$company_id);
									$driver_earnings = $api->get_driver_earnings_with_rating($driver_id,$company_id);
									$driver_tot_earnings = $api->get_driver_total_earnings($driver_id);
									$statistics = array();
									$total_trip = $trip_total_with_rate = $total_ratings = $today_earnings = $total_amount=0;
																	
									foreach($driver_earnings as $stat){
									$total_trip++;
									$total_ratings += $stat['rating'];
									$total_amount += $stat['total_amount'];											
									}
									$overall_trip = $total_trip + $rejected_trips + $driver_cancelled_trips;							
									$time_driven = $api->get_time_driven($driver_id,'R','A','1');
									$statistics = array(
										"total_trip" => $overall_trip,
										"completed_trip" => $total_trip,
										"total_earnings" => round($driver_tot_earnings,2),
										"overall_rejected_trips" => $rejected_trips,
										"cancelled_trips" => $driver_cancelled_trips,
										"today_earnings"=>round($total_amount,2),											
										"shift_status"=>'IN',
										"time_driven"=>$time_driven,
										"status"=> 1
									);
									$message = array("message" => __('request_rejected'),"driver_statistics"=>$statistics,"status" => 6);
								}								
								/***********************************************************************************/							
							}
						}
						else
						{							
							$get_driver_request = $api->get_driver_request($trip_id);
							$rejection_type = 0;
							if(count($get_driver_request) >0)
							{
								/******* Update the driver id in */
								$prev_rejected_timeout_drivers = $get_driver_request[0]['rejected_timeout_drivers'];
								$status = $get_driver_request[0]['status'];
								$reject_driversArr = explode(",",$prev_rejected_timeout_drivers);
								if(!in_array($driver_id, $reject_driversArr)) 
								{
									if($prev_rejected_timeout_drivers != "")
									{
										$rejected_timeout_drivers = $prev_rejected_timeout_drivers.','.$driver_id;
									}
									else
									{
										$rejected_timeout_drivers = $driver_id;
									}
									
									# increase actual_limit
									$actual_limit = $get_driver_request[0]['actual_limit'];
									$driver_limit = $get_driver_request[0]['driver_limit'];
									$actual_limit++;
									if($actual_limit >= $driver_limit){
										$api->update_tripdriverlimit($trip_id);
									}
									$api->update_driverlimit($trip_id,$actual_limit);
									$get_request_dets=$api->check_new_request_tripid($taxi_id,$company_id,$trip_id,$driver_id,$company_all_currenttimestamp,"",$operator_id);		
									if($status != '4')
									{
										$datas  = array("status"=>'0', "rejected_timeout_drivers" => $rejected_timeout_drivers);
										$result = $api_ext->update_driverrequest($datas, $trip_id);
									}
								}
								$add_rejected_list = $api->add_rejected_list($post,$rejection_type);
								// Driver Statistics ********************/
								$driver_logs_rejected = $api->get_rejected_drivers($driver_id,$company_id);	
								$rejected_trips = count($driver_logs_rejected);	
								//to get cancelled trip counts from drivers
								$driver_cancelled_trips = $api->get_driver_cancelled_trips($driver_id,$company_id);
								$driver_earnings = $api->get_driver_earnings_with_rating($driver_id,$company_id);
								$driver_tot_earnings = $api->get_driver_total_earnings($driver_id);
								$statistics = array();
								$total_trip = $trip_total_with_rate = $total_ratings = $today_earnings = $total_amount=0;
																
								foreach($driver_earnings as $stat){
								$total_trip++;
								$total_ratings += $stat['rating'];
								$total_amount += $stat['total_amount'];											
								}
								$overall_trip = $total_trip + $rejected_trips + $driver_cancelled_trips;							
								$time_driven = $api->get_time_driven($driver_id,'R','A','1');	
								$statistics = array( 
									"total_trip" => $overall_trip,
									"completed_trip" => $total_trip,
									"total_earnings" => round($driver_tot_earnings,2),
									"overall_rejected_trips" => $rejected_trips,
									"cancelled_trips" => $driver_cancelled_trips,
									"today_earnings"=>round($total_amount,2),											
									"shift_status"=>'IN',
									"time_driven"=>$time_driven,
									"status"=> 1
								  ); 
								
								$message = array("message" => __('driver_reply_timeout'),"driver_statistics"=>$statistics,"status" => 7);
								
								# increase actual_limit
								$actual_limit = $get_driver_request[0]['actual_limit'];
								$actual_limit++;
								$api->update_driverlimit($trip_id,$actual_limit);									
							}		
						}	
					}
					else
					{
						$message = array("message" => __('invalid_trip'),"status"=>2);
					}						
			}
			else
			{
				$message =__('trip_id_req');
				$message = array("message" => $message,"status" => '-1');
			}
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			//unset($message,$api_ext,$passenger_log_details,$get_request_dets,$statistics,$get_driver_request);
			exit;

			case 'favourite_driver_list':
				$passenger_id = isset($mobiledata['passenger_id']) ? $mobiledata['passenger_id'] : '';
				if(!empty($passenger_id)) {
					$result = $api->favourite_driver_list($passenger_id);
					
					if(count($result) > 0) {
						$favourite_driver = array();
						foreach($result as $f) {
							$image = URL_BASE."public/images/noimages.jpg";
							if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.PUBLIC_UPLOADS_FOLDER.'/driver_image/'.$f['profile_picture']) && ($f['profile_picture'] != "")) {
								$image = URL_BASE.SITE_DRIVER_IMGPATH.$f['profile_picture'];
							}
							$favourite_driver[] = array (
								"driver_id" => $f["id"],
								"name" => $f["name"],
								"email" => $f["email"],
								"phone" => $f["phone"],
								"profile_image" => $image,
								"taxi_no" => $f["taxi_no"]
							);
						}
						$message = array("message" => __('favourite_driver_list'),"details" => $favourite_driver, "status" => 1);
					}
					else
					{
						$message = array("message" => __('no_data'),"status" => -1);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$image,$result,$favourite_driver);
				break;

			case 'unfavourite_driver':
				$passenger_id = isset($mobiledata['passenger_id']) ? $mobiledata['passenger_id'] : '';
				$driver_id = isset($mobiledata['driver_id']) ? $mobiledata['driver_id'] : '';
				if(!empty($passenger_id) && !empty($driver_id)) {
					$result = $api->unfavourite_driver($passenger_id, $driver_id);
					
					if($result == 1) {
						$message = array("message" => __('unfavourite_success'),"status" => 1);
					} else if($result == -1) {
						$message = array("message" => __('no_data'),"status" => -1);
					} else {
						$message = array("message" => __('invalid_user_driver'),"status" => -2);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$image,$result);
				break;

			case 'set_split_fare':
				$passenger_id = isset($mobiledata['passenger_id']) ? $mobiledata['passenger_id'] : '';
				$type = isset($mobiledata['type']) ? $mobiledata['type'] : '';
				if(!empty($passenger_id)) {
					$result = $api->set_split_fare($passenger_id,$type);
					if($result > 0) {
						if($type) {
							$message = array("message" => __('splifare_on_success_label'), "status" => 1);
						} else {
							$message = array("message" => __('splifare_off_success_label'), "status" => 0);
						}
					} else {
						$message = array("message" => __('not_eligible_for_splifare_on_label'), "status" => -1);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$result);
				break;
				
			/** Driver Withdraw List **/
			case 'driver_withdraw_list':
				$driver_id = isset($mobiledata['driver_id']) ? $mobiledata['driver_id'] : '';
				if(!empty($driver_id)) {
					$company_det = $api->get_company_id($driver_id);
					$company_id = ($company_det > 0) ? $company_det[0]['company_id'] : $default_companyid;
					$result = $api->get_withdraw_request($company_id,$driver_id);
					if(count($result) > 0) {
						$data = array();
						foreach($result as $f) {
							$status_label = __("pending");
							$status_id = 0;
							if($f["request_status"] == 1) {
								$status_label = __("approved");
								$status_id = 1;
							} else if($f["request_status"] == 2) {
								$status_label = __("rejected");
								$status_id = 2;
							}
							$data[] = array (
								"withdraw_request_id" => $f["withdraw_request_id"],
								"request_id" => "#".$f["request_id"],
								"withdraw_amount" => $this->site_currency.$f["withdraw_amount"],
								"request_date" => Commonfunction::getDateTimeFormat($f["request_date"],1),
								"brand_type" => ($f["brand_type"] == 1) ? __("Multy") : __("single"),
								"request_status" => $status_label,
								"request_status_id" => $status_id
							);
						}
						$message = array("message" => __('withdraw_req_list'),"details" => $data, "status" => 1);
					}
					else
					{
						$message = array("message" => __('no_data'),"status" => -1);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$result);
				break;
				
			/** Driver Send Withdraw Request **/
			case 'driver_send_withdraw_request':
				$driver_id = isset($mobiledata['driver_id']) ? $mobiledata['driver_id'] : '';
				$request_amount = isset($mobiledata['request_amount']) ? $mobiledata['request_amount'] : '';
				$available_amount = isset($mobiledata['available_amount']) ? $mobiledata['available_amount'] : '';
				if(!empty($driver_id) && !empty($request_amount) && !empty($available_amount)) {
					if($request_amount == "0.00" || $request_amount == "0") {
						$message = array("message" => __('invalid_amount'),"status" => -1);
					} else if($request_amount > $available_amount) {
						$message = array("message" => __('withdraw_amount_error'),"status" => -1);
					} else {
						$company_det = $api->get_company_id($driver_id);
						$company_id = ($company_det > 0) ? $company_det[0]['company_id'] : $default_companyid;
						$driver_pending_amount=0;
						$pending_result = $api->driver_withdraw_pending_amount($company_id,$driver_id);
						if(count($pending_result) > 0) {
							$driver_pending_amount = ($pending_result[0]["pending_amount"]) ? $pending_result[0]["pending_amount"] : 0;
						}
						$total_amount = round($available_amount - $driver_pending_amount,2);
						if((float)$total_amount >= (float)$request_amount){
							$result = $api->insert_withdraw_request($company_id,$driver_id,$request_amount);
							if(count($result) > 0) {
								$driver_pending_amount=0;
								$pending_result = $api->driver_withdraw_pending_amount($company_id,$driver_id);
								if(count($pending_result) > 0) {
									$driver_pending_amount = ($pending_result[0]["pending_amount"]) ? $pending_result[0]["pending_amount"] : 0;
								}
								$withdraw_request_array = array(
									'trip_amount' => round($available_amount,2),
									"trip_pending_amount"=> round($driver_pending_amount,2),
									'total_amount' => round($available_amount - $driver_pending_amount,2)
								);
								$message = array("message" => __('withdraw_req_sent_success'), "status" => 1,"details"=> $withdraw_request_array);
							}
							else
							{
								$message = array("message" => __('no_data'),"status" => -1);
							}
						}
						else{
							$message = array("message" => __('dont_have_sufficient_wallet_amount'),"status"=>-1);
						}
					}
				} else {
					$message = array("message" => __('invalid_request'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message);
				break;
				
			/** Help content list in passenger app **/
			case 'help_content':
				$helpList = $api->getHelpContents();
				if(count($helpList) > 0){
									
					$result = array_map(
								function($helpList) {
									return array(
										'help_id' => $helpList['help_id'],
										'help_content' => __('help'.$helpList['help_id'])
									);
								}, $helpList);
					$message = array("message" => __('success'),"details" => $result, "status" => 1);
				} else {
					$message = array("message" => __('no_data'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message);
			break;
			
			/** Help Comment update for a trip from passenger App **/
			case 'help_comment_update':
				$trip_id = isset($mobiledata['trip_id']) ? $mobiledata['trip_id'] : '';
				$help_id = isset($mobiledata['help_id']) ? $mobiledata['help_id'] : '';
				$help_comment = isset($mobiledata['help_comment']) ? $mobiledata['help_comment'] : '';
				if(!empty($help_id) && !empty($help_comment) && !empty($trip_id)) {
					$updateTripComment = $api->updateTripComment($trip_id,$help_id,$help_comment);
					if($updateTripComment){
						$message = array("message" => __('comment_post_success'), "status" => 1);
					} else {
						$message = array("message" => __('problem_post_comment'),"status" => -1);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message);
			break;
			
			/** Driver Withdraw List Detail Page **/
			case 'driver_withdraw_list_detail':
				$driver_id = isset($mobiledata['driver_id']) ? $mobiledata['driver_id'] : '';
				$withdraw_request_id = isset($mobiledata['withdraw_request_id']) ? $mobiledata['withdraw_request_id'] : '';
				if(!empty($driver_id) && !empty($withdraw_request_id)) {
					$result = $api->get_withdraw_deatil($driver_id,$withdraw_request_id);
					$log_result = $api->get_withdraw_log($withdraw_request_id);
					
					$activity_log = $data = array();
					if(count($result) > 0) {
						foreach($result as $f) {
							$status_label = __("not_yet_approved");
							if($f["request_status"] == 1) {
								$status_label = __("approved");
							} else if($f["request_status"] == 2) {
								$status_label = __("rejected");
							}
							$data[] = array (
								"withdraw_request_id" => $f["withdraw_request_id"],
								"request_id" => "#".$f["request_id"],
								"company_name" => $f["company_name"],
								"withdraw_amount" => $this->site_currency.$f["withdraw_amount"],
								"request_date" => date("D,dM-Y h:i:s A",strtotime($f["request_date"])),
								"brand_type" => ($f["brand_type"] == 1) ? __("Multy") : __("single"),
								"request_status" => $status_label
							);
						}
						if(count($log_result) > 0) {
							foreach($log_result as $l) {
								$attachment = $payment_mode_name = "";
								$status_label = __("not_yet_approved");
								if($l["status"] == 1) {
									$status_label = __("approved");
								} else if($l["status"] == 2) {
									$status_label = __("rejected");
								}
								$status_txt = "Status changed to ".$status_label.".";
								if($l["status"] == 1) {
									$payment_mode_name = $l["payment_mode_name"];
									$transaction_id = $l["transaction_id"];
									$comments = $l["comments"];
								} else if($l["status"] == 2) {
									$payment_mode_name = $l["payment_mode_name"];
									$transaction_id = $l["transaction_id"];
									$comments = $l["comments"];
								}
								if($l['file_name'] != "" && file_exists(DOCROOT.WITHDRAW_IMG_PATH.$l['file_name'])) {
									$attachment = URL_BASE.WITHDRAW_IMG_PATH.$l['file_name'];
								}
								$activity_log[] = array (
									"created_date" => date("D,dM-Y h:i:s A",strtotime($l["created_date"])),
									"status" => $l["status"],
									"status_txt" => $status_txt,
									"payment_mode_name" => ($payment_mode_name != null) ? $payment_mode_name : "",
									"transaction_id" => $transaction_id,
									"comments" => $comments,
									"attachment" => $attachment,
								);
							}
						}
						$message = array("message" => __('withdraw_request_details'),"details" => $data,"activity_log" => $activity_log, "status" => 1);
					}
					else
					{
						$message = array("message" => __('no_data'),"status" => -1);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$result,$log_result,$data,$attachment,$activity_log);
			break;
			
			/** Street Pickup End Trip **/
			case 'street_pickup_end_trip':
			$extended_api = Model::factory(MOBILEAPI_107_EXTENDED);
			$array = $mobiledata;
			if(!empty($array))
			{    
				$drop_latitude = $array['drop_latitude'];
				$drop_longitude = $array['drop_longitude'];
				$drop_location = urldecode($array['drop_location']);
				$trip_id = $array['trip_id'];
				$distance = $array['distance'];
				$actual_distance = $array['actual_distance'];
				$distance_time = $array['distance_time'];
				$waiting_hours = $array['waiting_hour'];
				$driver_app_version = (isset($array['driver_app_version'])) ? $array['driver_app_version'] : '';
				if(!empty($trip_id))
				{
					$gateway_details = $this->commonmodel->gateway_details($default_companyid);
					$get_passenger_log_details = $api->getPassengerLogDetail($trip_id);
					$pickupdrop = $taxi_id = $company_id = $fare_per_hour = $waiting_per_hour = $total_fare = $nightfare = 0;
					if(count($get_passenger_log_details) > 0)
					{						
						$company_tax = $get_passenger_log_details[0]->company_tax;						
						$default_metric = ($get_passenger_log_details[0]->default_unit == 0) ? "KM":"MILES";
						$default_metric = (FARE_SETTINGS == 2) ? $default_metric : UNIT_NAME;						
						$company_fare_calculation_type = $get_passenger_log_details[0]->fare_calculation_type;
						$farecalculation_type = (FARE_SETTINGS != 2) ? FARE_CALCULATION_TYPE : $company_fare_calculation_type;
						$tax = (FARE_SETTINGS != 2) ? TAX : $company_tax;
						$travel_status = $get_passenger_log_details[0]->travel_status;
						$splitTrip = $get_passenger_log_details[0]->is_split_trip; //0 - Normal trip, 1 - Split trip
						$total_distance = $get_passenger_log_details[0]->distance;
						$pickupdrop = $taxi_id = $company_id = $fare_per_hour = $waiting_per_hour = $total_fare = $nightfare = 0;
						
						if(($travel_status == 2) || ($travel_status == 5))
						{
							$pickup = $get_passenger_log_details[0]->current_location;
							$drop = $get_passenger_log_details[0]->drop_location;
							$pickupdrop = $get_passenger_log_details[0]->pickupdrop;
							$taxi_id = $get_passenger_log_details[0]->taxi_id;
							$pickuptime = date('H:i:s', strtotime($get_passenger_log_details[0]->pickup_time));
							$actualPickupTime = date('H:i:s', strtotime($get_passenger_log_details[0]->actual_pickup_time));
							$company_id = $get_passenger_log_details[0]->company_id;
							$driver_id = $get_passenger_log_details[0]->driver_id;
							$approx_distance = $get_passenger_log_details[0]->approx_distance;
							$approx_fare = $get_passenger_log_details[0]->approx_fare;
							$fixedprice = $get_passenger_log_details[0]->fixedprice;
							$actual_pickup_time = $get_passenger_log_details[0]->actual_pickup_time;

							$taxi_model_id = $get_passenger_log_details[0]->taxi_modelid;
							$brand_type = $get_passenger_log_details[0]->brand_type;

							$taxi_fare_details = $api->get_model_fare_details($company_id,$taxi_model_id,$get_passenger_log_details[0]->search_city,$brand_type);
							if($travel_status != 5) {
								$drop_time = $this->commonmodel->getcompany_all_currenttimestamp($company_id);
							} else {
								$drop_time = $get_passenger_log_details[0]->drop_time;
							}
							/*************** Update arrival in driver request table ******************/
							$update_driver_array  = array(
								'status' => 7,
								'trip_id'=>$trip_id
							);
							/*************************************************************************/
							
							/** Update Driver Status **/
							if(($array['drop_latitude'] > 0 ) && ($array['drop_longitude'] > 0))
							{
								$update_driver_array  = array(
									'latitude' => $array['drop_latitude'],
									'longitude' => $array['drop_longitude'],
									'status' => 'A',
									'driver_id'=>$driver_id
								);
							}
							else
							{
								$update_driver_array  = array(
									'status' => 'A',
									'driver_id'=>$driver_id
								);
							}
							/*********************/
							$base_fare = $min_km_range = $min_fare = $cancellation_fare = $below_above_km_range = $below_km = $above_km = $night_charge = $night_timing_from = $night_timing_to = $night_fare = $evening_charge = $evening_timing_from = $evening_timing_to = $evening_fare = $waiting_per_hour = $minutes_cost= $baseFare= 0;
							if(count($taxi_fare_details) > 0)
							{
								$base_fare = $taxi_fare_details[0]['base_fare'];
								$min_km_range = $taxi_fare_details[0]['min_km'];
								$min_fare = $taxi_fare_details[0]['min_fare'];
								$cancellation_fare = $taxi_fare_details[0]['cancellation_fare'];
								$below_above_km_range = $taxi_fare_details[0]['below_above_km'];
								$below_km = $taxi_fare_details[0]['below_km'];
								$above_km = $taxi_fare_details[0]['above_km'];
								$night_charge = $taxi_fare_details[0]['night_charge'];
								$night_timing_from = $taxi_fare_details[0]['night_timing_from'];
								$night_timing_to = $taxi_fare_details[0]['night_timing_to'];
								$night_fare = $taxi_fare_details[0]['night_fare'];
								$evening_charge = $taxi_fare_details[0]['evening_charge'];
								$evening_timing_from = $taxi_fare_details[0]['evening_timing_from'];
								$evening_timing_to = $taxi_fare_details[0]['evening_timing_to'];
								$evening_fare = $taxi_fare_details[0]['evening_fare'];
								$waiting_per_hour = $taxi_fare_details[0]['waiting_time'];
								$minutes_fare = $taxi_fare_details[0]['minutes_fare'];
								$farePerMin = $minutes_fare;
							}

							// Which is used when the driver send waiting time as minutes
							$roundtrip = "No";
							if($pickupdrop == 1)
							{
								$roundtrip = "Yes";
							}
							// Minutes travelled functionlity starts here

							/********Minutes fare calculation *******/
							$interval  = abs(strtotime($drop_time) - strtotime($actual_pickup_time));
							
							$minutes   = round($interval / 60);
							/********Minutes fare calculation *******/
							
							// Minutes travelled functionlity ends here
							if($farecalculation_type == 1 || $farecalculation_type == 3)
							{
								$baseFare = $base_fare;
								if($total_distance < $min_km_range)
								{
									//min fare has set as base fare if trip distance 
									$baseFare = $min_fare;
									$total_fare = $min_fare;
								}
								else if($total_distance <= $below_above_km_range)
								{
									$fare = $total_distance * $below_km;
									$total_fare  = 	$fare + $base_fare ;
								}
								else if($total_distance > $below_above_km_range)
								{
									$fare = $total_distance * $above_km;
									$total_fare  = 	$fare + $base_fare ;
								}
							}
							
							if($farecalculation_type == 2 || $farecalculation_type == 3)
							{
								/********** Minutes fare calculation ************/
								if($minutes_fare > 0)
								{
									$minutes_cost = $minutes * $minutes_fare;
									$total_fare  = $total_fare + $minutes_cost;
								}
								/************************************************/
							}
							$trip_fare = $total_fare;

							// Waiting Time calculation
							$waiting_cost = $waiting_per_hour * $waiting_hours;
							$total_fare = $waiting_cost + $total_fare;

							$parsed = date_parse($actualPickupTime);
							$pickup_seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];

							//Night Fare Calculation
							$parsed = date_parse($night_timing_from);
							$night_from_seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];

							$parsed = date_parse($night_timing_to);
							$night_to_seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];

							$nightfare_applicable = $date_difference=0;
							if ($night_charge != 0) 
							{
								if( ($pickup_seconds >= $night_from_seconds && $pickup_seconds <= 86399) || ($pickup_seconds >= 0 && $pickup_seconds <= $night_to_seconds) )
								{
									$nightfare_applicable = 1;
									$nightfare = ($night_fare/100)*$total_fare;
									$total_fare  = $nightfare + $total_fare;
								}
							}

							//Evening Fare Calculation
							$parsed_eve = date_parse($evening_timing_from);
							$evening_from_seconds = $parsed_eve['hour'] * 3600 + $parsed_eve['minute'] * 60 + $parsed_eve['second'];

							$parsed_eve = date_parse($evening_timing_to);
							$evening_to_seconds = $parsed_eve['hour'] * 3600 + $parsed_eve['minute'] * 60 + $parsed_eve['second'];

							$eveningfare = $evefare_applicable=$date_difference=0;
							if ($evening_charge != 0) 
							{
								if( $pickup_seconds >= $evening_from_seconds && $pickup_seconds <= $evening_to_seconds)
								{
									$evefare_applicable = 1;
									$eveningfare = ($evening_fare/100)*$total_fare;
									$total_fare  = $eveningfare + $total_fare;
								}
							}

							// Company Tax amount Calculation
							$tax_amount = "";
							if($tax > 0)
							{
								$tax_amount = ($tax/100)*$total_fare;
								$total_fare =  $total_fare+$tax_amount;
							}
							
							$total_fare = ($fixedprice != 0) ? $fixedprice : $total_fare;
							$trip_fare = round($trip_fare,2);
							$total_fare = round($total_fare,2);
							if($travel_status != 5) {
								//to update the used wallet amount and  for a trip in passenger log table
								$message_status = 'R';$driver_reply='A';$journey_status=5; // Waiting for Payment
								$journey = $api->update_journey_statuswith_drop($trip_id,$message_status,$driver_reply,$journey_status,$drop_latitude,$drop_longitude,$drop_location,$drop_time,$total_distance,$waiting_hours,$tax,$driver_app_version,0,$waiting_per_hour,$farePerMin);
								//update the wallet amount in referred driver's row
								$referredDriver = $api->getReferredDriver($driver_id);
								if($referredDriver > 0) {
									$driverReferral = $api->getDriverReferralDetails($referredDriver);
									if(count($driverReferral) > 0){
										$wallAmount = $driverReferral[0]['registered_driver_wallet'] + $driverReferral[0]['registered_driver_code_amount'];
										$update_driver_array  = array(
											'registered_driver_wallet' => $wallAmount,
											'registered_driver_id'=>$driverReferral[0]['registered_driver_id']
										);
										$update_current_result = $extended_api->update_driver_referral_list($update_driver_array);
										//update referrer earned status in registered driver's row while he completing his first trip	
										$update_driver_array  = array(
											'referral_status' => 1,
											'registered_driver_id'=>$driver_id
										);
										$update_current_result = $extended_api->update_driver_referral_list($update_driver_array);
									}
								}
							}
							$tax_amount = round($tax_amount,2);
							$nightfare = round($nightfare,2);
							$smpleArr = array();
							foreach($gateway_details as $key=>$valArr) {
								if($valArr['pay_mod_id'] == 1) {
									$smpleArr[] = $valArr;
								}
							}
							$gateway_details = $smpleArr;

							//the hours value has been changed to seconds
							$convertSeconds = $waiting_hours * 3600;
							$converthours = floor($convertSeconds / 3600);
							$convertmins = floor(($convertSeconds - ($converthours*3600)) / 60);
							$convertsecs = floor($convertSeconds % 60);
							$waitH = ($converthours < 10) ? '0'.$converthours : $converthours;
							$waitM = ($convertmins < 10) ? '0'.$convertmins : $convertmins;
							$waitS = ($convertsecs < 10) ? '0'.$convertsecs : $convertsecs;
							$waitingTime = ($waitH != "00") ? $waitH.':'.$waitM.':'.$waitS.' Hours' :  $waitM.':'.$waitS.' Mins';
														
							$detail = array("trip_id" => $trip_id,"distance" => $total_distance,"trip_fare"=>$trip_fare,"nightfare_applicable"=>$nightfare_applicable,"nightfare"=>$nightfare,"eveningfare_applicable"=>$evefare_applicable,"eveningfare"=>$eveningfare,"waiting_time"=>$waitingTime,"waiting_cost"=>$waiting_cost,"tax_amount"=>$tax_amount,"subtotal_fare"=>$total_fare,"total_fare"=>$total_fare,"gateway_details"=>$gateway_details,"pickup"=>$pickup,"drop"=>$drop_location,"company_tax"=>$tax,"waiting_per_hour" => $waiting_per_hour, "roundtrip"=> $roundtrip,"minutes_traveled"=>$minutes,"minutes_fare"=>$minutes_cost,"metric"=>$default_metric,"base_fare"=>$baseFare,"wallet_amount_used"=>0,"promo_discount_per"=>0,"pass_id"=>"","referdiscount"=>"","promo_discount_per"=>"","promodiscount_amount"=>0,"passenger_discount"=>"","credit_card_status"=>0,"street_pickup"=>1,"fare_calculation_type"=>$farecalculation_type);
							$message = array("message" => __('trip_completed_driver'),"detail" => $detail,"status" => 4);
						}
						else if($travel_status == 1)
						{
							$message = array("message" => __('trip_already_completed'),"status"=>-1);
						}
						else
						{
							$message = array("message" => __('trip_not_started'),"status"=>-1);
						}
					}
					else
					{
						$message = array("message" => __('invalid_trip'),"status"=>-1);
					}
				}
				else
				{
					$message = array("message" => __('invalid_trip'),"status"=>-1);
				}
			}
			else
			{
				$message = array("message" => __('invalid_request'),"status"=>-1);
			}
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			//unset($message,$extended_api,$get_passenger_log_details,$journey,$detail);
			break;
			
			/** Street Pickup - Driver Start Trip Process **/
			case 'driver_start_trip':
				$extended_api = Model::factory(MOBILEAPI_107_EXTENDED);
				$trip_array = $mobiledata;
				if(!empty($trip_array)) {
					$trip_array['company_id'] = $default_companyid;
					$login_status = $api->driver_logged_status($trip_array);
					if($login_status == 1){
						$trip_array['company_id'] = $api->get_driver_companyid($trip_array);
						$trip_status = $api->driver_current_trip_status($trip_array);
						if(count($trip_status) > 0){
							
							$shift_status = isset($trip_status[0]['shift_status']) ? $trip_status[0]['shift_status'] : 'OUT';
							if($shift_status == 'OUT'){
								$message = array("message" => __('driver_shift_out'),"status"=>-1);
							}
							else{								
								$trip_array['approx_trip_fare'] = isset($trip_array['approx_trip_fare'])?round($trip_array['approx_trip_fare'],2):0;
								$trip_array['taxi_id'] = isset($trip_status[0]['mapping_taxiid'])?$trip_status[0]['mapping_taxiid']:"";
								$brand_type = isset($trip_status[0]['brand_type'])?$trip_status[0]['brand_type']:"";
								$fare_details = $api->get_model_fare_details($trip_array['company_id'],$trip_status[0]['taxi_model'],'',$brand_type);
								
								$default_unit = ($trip_status[0]['default_unit'] == 0 || $trip_status[0]['default_unit']=='') ? "KM":"MILES";	
								$default_unit = (FARE_SETTINGS == 2) ? $default_unit : UNIT_NAME;							
								
								if(count($fare_details) > 0){
									$trip_array['motor_model'] = $trip_status[0]['taxi_model'];
									$details['driver_tripid'] = $api->save_street_trip($trip_array);	
									$update_driver_array  = array(
										'latitude' => $trip_array['pickup_latitude'],
										'longitude' => $trip_array['pickup_longitude'],
										'status' => 'A',
										'driver_id'=>$trip_array['driver_id']
									);
									$update_current_result = $extended_api->update_driver_location($update_driver_array);
									foreach($fare_details as $val){
										$details['base_fare'] = $val['base_fare'];
										$details['min_fare'] = $val['min_fare'];
										$details['below_km'] = $val['below_km'];
										$details['above_km'] = $val['above_km'];
										$details['below_above_km'] = $val['below_above_km'];
										$details['metric'] = $default_unit;
									}
									$message = array("message" => __('trip_confirmed'),"status" => 1,"detail"=>$details);
								}else{
									$message = array("message" => __('invalid_motor_model'),"status" => -1);
								}
							}
						}else{
							$message = array("message" => __('already_trip'),"status"=>-1);
						}
					}
					else
					{
						$message = array("message" => __('driver_not_login'),"status"=>-1);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$extended_api,$fare_details,$trip_status,$details);
			break;
		
			/** Search Driver Withdraw List **/
			case 'search_driver_withdraw_list':
				$search_array = $mobiledata;
				if(!empty($search_array['driver_id'])) {
					$company_det = $api->get_company_id($search_array['driver_id']);
					$search_array['company_id'] = ($company_det > 0) ? $company_det[0]['company_id'] : $default_companyid;
					$result = $api->search_withdraw_request($search_array);
					if(count($result) > 0) {
						$data = array();
						foreach($result as $f) {
							$status_label = __("pending");
							$status_id = 0;
							if($f["request_status"] == 1) {
								$status_label = __("approved");
								$status_id = 1;
							} else if($f["request_status"] == 2) {
								$status_label = __("rejected");
								$status_id = 2;
							}
							$data[] = array (
								"withdraw_request_id" => $f["withdraw_request_id"],
								"request_id" => "#".$f["request_id"],
								"withdraw_amount" => $this->site_currency.$f["withdraw_amount"],
								"request_date" => Commonfunction::getDateTimeFormat($f["request_date"],1),
								"request_status" => $status_label,
								"request_status_id" => $status_id
							);
						}
						$message = array("message" => __('withdraw_req_list'),"details" => $data, "status" => 1);
					}
					else
					{
						$message = array("message" => __('no_data'),"status" => -1);
					}
				} else {
					$message = array("message" => __('invalid_request'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$result,$data);
				break;
		
			/** Driver Recent Trip List **/
			case 'driver_recent_trip_list':
				$trip_array = $mobiledata;
				if(!empty($trip_array)) {
					$login_status = $api->driver_logged_status($trip_array);
					if($login_status == 1){
						$trip_list = $api->get_recent_driver_trip_list($trip_array);
						if(count($trip_list) > 0){
							foreach($trip_list as $key => $val){
								$trip_list[$key]['drop_time'] = Commonfunction::getDateTimeFormat($val['drop_time'],1);
							}
							$message = array("message" => __('drive_recent_trip_list'),"status" => 1,"trip_list"=>$trip_list);
						}else{
							$message = array("message" => __('no_data'),"status" => -1);
						}	
					}
					else
					{
						$message = array("message" => __('driver_not_login'),"status"=>-1);	
					}
				} else {
					$message = array("message" => __('invalid_request'),"status" => -1);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$login_status,$trip_list);
			break;
			
			case 'street_pickup_tripfare_update':
				$array = $mobiledata;
				$api_model = Model::factory(MOBILEAPI_107);
				$extended_api = Model::factory(MOBILEAPI_107_EXTENDED);
				$pay_mod_id = $array['pay_mod_id'];
				$validator = $this->payment_validation($array);
				$driver_statistics = array();
				if($validator->check())
				{
					$passenger_log_id = $array['trip_id'];
					if($array['actual_distance'] == "")
						$distance = $array['distance'];
					else
					$distance = $array['actual_distance'];
					$actual_amount = $array['actual_amount'];
					$remarks = $array['remarks'];
					$minutes_traveled=$array['minutes_traveled'];
					$minutes_fare=$array['minutes_fare'];
					$base_fare=$array['base_fare'];
					$trip_fare = $array['trip_fare']; //Trip Fare without Tax,Tips and Discounts
					$fare = round($array['fare'],2); //Total Fare with Tax,Tips and Discounts can editable by driver
					$tips = round($array['tips'],2); //Tips Optional
					$nightfare_applicable = $array['nightfare_applicable'];
					$nightfare = $array['nightfare'];
					$eveningfare_applicable = $array['eveningfare_applicable'];
					$eveningfare = $array['eveningfare'];
					$tax_amount = $array['tax_amount'];
					$tax_percentage = $array['company_tax'];
					$fare_calculation_type = isset($array['fare_calculation_type']) ? $array['fare_calculation_type'] : FARE_CALCULATION_TYPE;
					$trip_fare = round($trip_fare,2);
					$total_fare = $fare;
					$amount = round($total_fare,2); // Total amount which is used for pass to payment gateways
					$get_passenger_log_details = $api->getPassengerLogDetail($passenger_log_id);
					if(count($get_passenger_log_details) > 0)
					{
						if($array['pay_mod_id'] == 1)
						{
							try {
								$default_unit = ($get_passenger_log_details[0]->default_unit == 0||$get_passenger_log_details[0]->default_unit == '') ? "KM" : "MILES";
                                $default_unit = (FARE_SETTINGS == 2) ? $default_unit : UNIT_NAME;
								$update_commission = $this->commonmodel->update_commission($passenger_log_id,$total_fare,ADMIN_COMMISSON);
								$insert_array = array(
									"passengers_log_id" => $passenger_log_id,
									"distance" 			=> urldecode($array['distance']),
									"actual_distance" 	=> urldecode($array['actual_distance']),
									"distance_unit" 	=> $default_unit,
									"tripfare"			=> $trip_fare,
									"fare" 				=> $fare,
									"tips" 				=> $tips,
									"waiting_cost"		=> $array['waiting_cost'],
									"passenger_discount"=> 0,
									"promo_discount_fare"=> 0,
									"tax_percentage"	=> $tax_percentage,
									"company_tax"		=> $tax_amount,
									"waiting_time"		=> urldecode($array['waiting_time']),
									"trip_minutes"		=> $minutes_traveled,
									"minutes_fare"		=> $minutes_fare,
									"base_fare"			=> $base_fare,
									"remarks"			=> $remarks,
									"payment_type"		=> $array['pay_mod_id'],
									"amt"				=> $amount,
									"nightfare_applicable" => $nightfare_applicable,
									"nightfare" 		=> $nightfare,
									"eveningfare_applicable" => $eveningfare_applicable,
									"eveningfare" 		=> $eveningfare,
									"admin_amount"		=> $update_commission['admin_commission'],
									"company_amount"	=> $update_commission['company_commission'],
									"driver_amount"		=> $update_commission['driver_commission'],
									"trans_packtype"	=> $update_commission['trans_packtype'],
									"fare_calculation_type"	=> $fare_calculation_type
								);
								$check_trans_already_exist = $api->checktrans_details($passenger_log_id);								
								if(count($check_trans_already_exist)>0)
								{
									$tranaction_id = $check_trans_already_exist[0]['id'];
									$update_transaction = $extended_api->update_transaction_table($insert_array,$tranaction_id);
								}
								else
								{
									$transaction = $extended_api->insert_transaction_table($insert_array);
								}
								//update travel status in passengers log table
								$message_status = 'R';$driver_reply='A';$journey_status=1; // Waiting for Payment
								$journey = $api->update_journey_status($passenger_log_id,$message_status,$driver_reply,$journey_status);
								$pickup = $get_passenger_log_details[0]->current_location;
								$detail = array("fare" => $amount,"pickup" => $pickup,"trip_id"=>$passenger_log_id);
								$message = array("message" => __('trip_fare_updated'),"detail"=>$detail,"status"=>1);
							}
							catch (Kohana_Exception $e) {
								$message = array("message" => __('trip_fare_already_updated'), "status"=>-1);
							}
						}
						else
						{
							$message = array("message" => __('invalid_payment_mode'),"status"=>-1);
						}
					}
					else
					{
						$message = array("message" => __('invalid_trip'),"status"=>-1);
					}
				}
				else
				{
					$validation_error = $validator->errors('errors');
					$message = array("message" => $validation_error,"status" => -3);
				}
				$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
				//unset($message,$api_model,$extended_api,$insert_array);
				break;
				
				
			
			}
			/** Switch Case End **/
			exit;
		/*}
		else
		{
			$message = array("message" => __('invalid_company'),"status"=>-8);
			$mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
			exit;
		}*/
	}

}


?>
