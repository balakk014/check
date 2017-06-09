<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
* website controller - Contains abstract class of front end 
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************/
abstract class Controller_TaximobilityWebsite extends Controller_Template
{
    //Default variables
    public $template = "themes/template";
    public $alllanguage;
    public $success_msg;
    public $failure_msg;
    public $script;
    public $style;
    public $curr_lang;
    public $session_instance;
    public $userid;
    public $user_name;
    public $user_email;
    public $user_type;
    public $user_paypal_account;
    public $all_countries;
    public $user_shipping;
    public $other_shipping;
    public $gig_alt_name;
    public $replace_variables;
    public $site_settings;
    public $job_settings;
    public $selected_theme;
    public $page_title;
    public $miles;
    /**
     ****__construct()****
     */
    public function __construct( Request $request, Response $response )
    {
        $controller        = $request->controller();
        $action            = $request->action();
        // Assign the request to the controller
        $this->request     = $request;
        // Assign a response to the controller
        $this->response    = $response;
        //Session instance
        $this->session     = Session::instance();
        $this->urlredirect = Request::current();
        //Models declaration
        $commonmodel       = Model::factory( 'commonmodel' );
        if ( file_exists( DOCROOT . 'application/classes/common_config.php' ) ) {
            require Kohana::find_file( 'classes', 'common_config' );
        }
        $siteusers           = Model::factory( 'siteusers' );
        $this->findMdl       = Model::factory( 'find' );
        $this->authorize     = Model::factory( 'authorize' );
        $this->managemodel   = Model::factory( 'managemodel' );
        $this->site          = Model::factory( 'site' );
        $this->emailtemplate = Model::factory( 'emailtemplate' );
        # language
        $domain = defined('SUB_DOMAIN_TEMP') ? SUB_DOMAIN_TEMP : UPLOADS;
        $this->lang          = $this->session->get( $domain.'lang' );
        //~ echo $this->lang;exit;
        if ( $this->lang != "" ) {
            $allLang = array_keys(DYNAMIC_LANGUAGE_ARRAY);
            if(in_array($this->lang,$allLang)){
                $lang = $this->lang;
                if(LANG_INFO==1){
                    $lang=$lang.'def';
                }
            }else{
                $this->session->set( $domain.'lang',SELECTED_LANGUAGE);
                $this->lang          = $this->session->get( $domain.'lang' );
                //Message::error(__('failed_toload_previous_lang'));
                Message::error('Failed to load previous selected language, Kindly contact administrator.');
                $lang= SELECTED_LANGUAGE.'def';
            }
        } else {
            if(LANG_INFO==1){
                $lang=SELECTED_LANGUAGE.'def';
            }else{
                $lang = SELECTED_LANGUAGE;
            }
            $this->session->set( $domain.'lang',SELECTED_LANGUAGE);
			$this->lang          = $this->session->get( $domain.'lang' );
        }
        $this->currlang            = I18n::lang( $lang );
        $this->javascript_language = json_encode( I18n::load( $this->currlang   ) );
        $this->usertype            = $this->session->get( 'user_type' );
        $this->username            = $this->session->get( 'username' );
        $this->name                = $this->session->get( 'name' );
        $this->user_name           = $this->session->get( 'user_name' );
        $this->firstname           = $this->session->get( 'first_name' );
        View::bind_global( 'usertype', $this->usertype );
        View::bind_global( 'username', $this->username );
        View::bind_global( 'name', $this->name );
        View::bind_global( 'user_name', $this->user_name );
        View::bind_global( 'first_name', $this->firstname );
        View::bind_global( 'js_language', $this->javascript_language );
        View::bind_global( 'language', $this->lang );

        if(defined('DYNAMIC_LANGUAGE_ARRAY')){
			$langArr = DYNAMIC_LANGUAGE_ARRAY;
		}else{
			$langArr = array(
				"en" => "English",
				"ar" => "Arabic",
				"fr" => "French",
				"tr" => "Turkish",
				"de" => "Germen",
				"ru" => "Russian" 
			);
		}
		array_walk($langArr, function(&$a) { $a = ucfirst($a); });
		View::bind_global( 'langArr', $langArr );
        //passengers model
        $id          = $this->session->get( 'id' );
        $field_array = array(
             'banner_image',
            'banner_content',
            'app_content',
            'passenger_app_android_store_link',
            'passenger_app_ios_store_link',
            'app_android_store_link',
            'app_ios_store_link',
            'app_bg_color',
            'about_us_content',
            'about_bg_color',
            'footer_bg_color',
            'contact_us_content',
            'facebook_follow_link',
            'google_follow_link',
            'twitter_follow_link',
            'banner_image_1',
            'app_bg_color_1',
            'about_bg_color_1',
            'footer_bg_color_1',
            'frontend_mobile', 
            'frontend_car', 
        );
        $home_array  = array();
        if ( count( $this->siteinfo ) > 0 ) {
            $siteinfo = $this->siteinfo[0];
            foreach ( $field_array as $field ) {
                $home_array[$field] = ( isset( $siteinfo[$field] ) && $siteinfo[$field] != "" ) ? $siteinfo[$field] : "";
            }
        }
        View::bind_global( 'home_array', $home_array );
        if ( $id != '' ) {
            $upcomming_trips = $siteusers->get_upcomming_trips( false, $id );
            $data            = json_decode( $upcomming_trips );
            define( 'ALERT', $upcomming_trips );
            $details = Commonfunction::get_user_wallet_amount();
            define( 'PASSENGER_WALLET', isset( $details['wallet_amount'] ) ? $details['wallet_amount'] : 0 );
            define( 'PASSENGER_REFERRALCODE', isset( $details['referral_code'] ) ? $details['referral_code'] : "" );
        }
        $this->userid = '';
        //Css & Script include for admin
        /**To Define path for selected theme**/
        define( "ADMINIMGPATH", URL_BASE . 'public/admin/images/' );
        define( "CSSADMIN", URL_BASE . 'public/admin/' );
        define( "ADMINCSSPATH", CSSADMIN . 'css/' );
        //Users Themes
        define( "THEME", "default/" );
        define( "USERVIEW", "themes/" . THEME );
        define( "CSSPATH", "public/common/css/" );
        $this->template = USERVIEW . 'template';
        $id             = $this->session->get( 'id' );
        $usertype       = $this->session->get( 'usertype' );
        $usrid          = $id;
        $menuorder      = array();
        View::bind_global( 'menuorder', $menuorder );
        /** for footer info **/
        $footer_contents[0]['site_favicon'] = SITE_FAVICON;
        $footer_contents['site_copyrights'] = SITE_COPYRIGHT;
        View::bind_global( 'footer_contents', $footer_contents );
        View::bind_global( 'usrid', $usrid );
        View::bind_global( 'usertype', $usertype );
        View::bind_global( 'global_session', $this->session );
        $companyId = $this->session->get( 'company_id' );
        if ( $companyId > 0 ) {
            $company_content = $commonmodel->getcompanycontent( $companyId );
            View::bind_global( 'company_content', $company_content );
            foreach ( $company_content as $cc ) {
                Route::set( $cc['page_url'], $cc['page_url'] . '.html' )->defaults( array(
                     'controller' => 'page',
                    'action' => 'companycms' 
                ) );
            }
        }
        if ( !isset( $usrid ) && $action != 'signup' && $action != 'forgotpassword' && $action != 'foursquare_connect' && $action != 'twittersignin' && $action != 'linkdin_signin' ) {
            $userstyles = array(
                 CSSPATH . 'layout_home.css' => 'screen',
                CSSPATH . 'mobile_slider/skin.css' => 'screen' 
            );
        } else {
            $userstyles = array(
                 CSSPATH . 'layout.css' => 'screen',
                CSSPATH . 'mobile_slider/skin.css' => 'screen' 
            );
        }
        $userscripts             = array(
             SCRIPTPATH . 'jquery-1.4.2.min.js',
            SCRIPTPATH . 'lightbox-form.js',
            SCRIPTPATH . 'rating.min.js',
            SCRIPTPATH . 'text_sahdow.js' 
        );
        $this->app_name          = SITENAME;
        $this->siteemail         = SITE_EMAILID;
        $this->app_description   = SITE_APP_DESCRIPTION;
        $this->notification_time = SITE_NOTIFICATION_SETTING;
        if ( $action ) {
            $meta_data              = $commonmodel->get_meta_settings( 'meta_keyword,meta_description,meta_title', $action );
            $this->meta_title       = $meta_data[0]["meta_title"];
            $this->meta_description = $meta_data[0]["meta_description"];
            $this->meta_keyword     = $meta_data[0]["meta_keyword"];
        }
        ( $this->meta_keyword == '' ) ? $this->meta_keyword = SITE_METAKEYWORD : "";
        ( $this->meta_description == '' ) ? $this->meta_description = SITE_METADESCRIPTION : "";
        ( $this->meta_title == '' ) ? $this->meta_title = $this->app_name : "";
        $waitingtime = array(
             "15" => "15 Mins",
            "30" => "30 Mins",
            "45" => "45 Mins",
            "60" => "60 Mins" 
        );
        $this->session->set( "miles", 5 );
        if ( $companyId == 0 ) {
            $this->title            = $this->app_name; //
            $this->meta_keywords    = $this->meta_keyword; //
            $this->meta_description = $this->meta_description; //
        } else {
            $this->title            = COMPANY_APP_NAME; //
            $this->meta_title       = COMPANY_META_TITLE;
            $this->meta_keywords    = COMPANY_META_KEYWORD; //
            $this->meta_description = COMPANY_META_DESCRIPTION; //                
        }
        View::bind_global( 'meta_description', $this->meta_description );
        View::bind_global( 'page_title', $this->title );
        View::bind_global( 'meta_keywords', $this->meta_keywords );
        View::bind_global( 'meta_title', $this->meta_title );
        View::bind_global( 'miles', $miles );
        View::bind_global( 'waitingtime', $waitingtime ); //
        View::bind_global( 'notification_time', $notification_time );
        View::bind_global( 'currency_code', $this->all_currency_code );
        View::bind_global( 'success_msg', $this->success_msg );
        View::bind_global( 'failure_msg', $this->failure_msg );
        View::bind_global( 'app_name', $this->app_name );
        View::bind_global( 'siteemail', $this->siteemail );
        View::bind_global( 'styles', $userstyles );
        View::bind_global( 'scripts', $userscripts );
        View::bind_global( 'action', $action );
        View::bind_global( 'controller', $controller );
        $this->currenttimestamp = $this->currenttimestamp();
        View::bind_global( 'data', $_POST );
        $ip = $_SERVER['REMOTE_ADDR'];
        $ip = IPADDRESS;
        
    }
    /**
     * ****action_email_send()****
     * @E-Mail function calls from here
     */
    public function email_send( array $mail, $type = 'smtp', $htmlneed = true )
    {
        if ( is_array( $mail ) ) {
            if ( $this->array_keys_exists( $mail, array(
                 'to',
                'from',
                'subject',
                'message' 
            ) ) ) {
                $to      = $mail['to'];
                $from    = $mail['from'];
                $subject = $mail['subject'];
                $message = $mail['message'];
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= "Content-language: he" . "\r\n";
                $headers .= 'Content-type:text/html;charset=iso-8859-1' . "\r\n";
                $headers .= 'From: ' . $from . "\r\n";
                switch ( $type ) {
                    case "smtp":
                        //mail send thru smtp
                        $this->siteusers = Model::factory( 'siteusers' );
                        $smtp_detail     = $this->siteusers->get_smtpdetails();
                        $smtp_config     = "";
                        if ( isset( $smtp_detail[0] ) ) {
                            $host        = $smtp_detail[0]['smtp_host'];
                            $username    = $smtp_detail[0]['smtp_username'];
                            $password    = $smtp_detail[0]['smtp_password'];
                            $port        = $smtp_detail[0]['smtp_port'];
                            $smtp_config = array(
                                 'driver' => 'smtp',
                                'options' => array(
                                     'hostname' => $host,
                                    'username' => $username,
                                    'password' => $password,
                                    'port' => $port,
                                    'encryption' => 'ssl' 
                                ) 
                            );
                        }
                        $smtp_config1   = array(
                             'driver' => 'smtp',
                            'options' => array(
                                 'hostname' => 'smtp-mail.outlook.com',
                                'username' => 'info@taximobility.com',
                                'password' => 'ndotadmin',
                                'port' => '587',
                                'encryption' => 'tls' 
                            ) 
                        );
                        //mail sending option here
                        $connect_result = Email::connect( $smtp_config1 );
                        try {
                            if ( Email::connect( $smtp_config1 ) ) {
                                if ( Email::send( $to, $from, $subject, $message, $html = $htmlneed ) == 0 ) {
                                    return 1;
                                }
                                return 0;
                            }
                        }
                        catch ( Throwable $e ) {
                            try {
                                if ( mail( $to, $subject, $message, $headers ) ) {
                                    return 1;
                                }
                            }
                            catch ( Throwable $e ) {
                                return 0;
                            }
                        }
                        break;
                    default:
                        if ( mail( $to, $subject, $message, $headers ) ) {
                            return 1;
                        }
                        break;
                }
            } else {
                return 2;
            }
        }
    }
    /**
     * ***action_array_keys_exists()****
     * ** User Defined Function **
     * @return check array exist otr not
     */
    public function array_keys_exists( $array, $keys )
    {
        foreach ( $keys as $k ) {
            if ( !isset( $array[$k] ) ) {
                return false;
            }
        }
        return true;
    }
    public function action_index()
    {
        $view                    = View::factory( USERVIEW . 'home' );
        $this->template->content = $view;
    }
    /**
     *****action_network_activity()****
     *@purpose of linkdin curl function
     */
    /** SEND GRID FUNCTION **/
    public function sendgrid( $host = array(), $from = "", $receiver = array(), $subject = "", $message = "" )
    {
        include MODPATH . "/email/swift/lib/swift_required.php";
        include_once MODPATH . "/email/swift/SmtpApiHeader.php";
        $hdr   = new SmtpApiHeader();
        $times = array();
        $names = array();
        $hdr->addFilterSetting( 'subscriptiontrack', 'enable', 1 );
        $hdr->addFilterSetting( 'twitter', 'enable', 1 );
        $hdr->addTo( $receiver );
        $hdr->addSubVal( '-time-', $times );
        $hdr->addSubVal( '-name-', $names );
        $hdr->setUniqueArgs( array ());
        $sitename = "Sayboard";
        if ( !$sitename ) {
            $sitename = $_SERVER['HTTP_HOST'];
        }
        $fromEmail = $from;
        if ( !$fromEmail ) {
            $fromEmail = "noreply@" . $_SERVER['HTTP_HOST'];
        }
        $from      = array(
             $fromEmail => $sitename 
        );
        $to        = array(
             'defaultdestination@example.com' => 'Personal Name Of Recipient' 
        );
        $text      = "test text..";
        $html      = $message;
        $transport = Swift_SmtpTransport::newInstance( $host['host'], $host['port'] );
        $transport->setUsername( $host['uname'] );
        $transport->setPassword( $host['password'] );
        $swift   = Swift_Mailer::newInstance( $transport );
        $message = new Swift_Message( $subject );
        $headers = $message->getHeaders();
        $headers->addTextHeader( 'X-SMTPAPI', $hdr->asJSON() );
        $message->setFrom( $from );
        $message->setBody( $html, 'text/html' );
        $message->setTo( $to );
        $message->addPart( $text, 'text/plain' );
        return;
    }
    public function network_activity( $oauth_token, $oauth_token_secret, $endpoint )
    {
        $req_token   = new OAuthConsumer( $oauth_token, $oauth_token_secret, 1 );
        $profile_req = OAuthRequest::from_consumer_and_token( $this->test_consumer, $req_token, "GET", $endpoint, array ());
        $profile_req->sign_request( $this->sig_method, $this->test_consumer, $req_token );
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
             $profile_req->to_header() 
        ) );
        curl_setopt( $ch, CURLOPT_URL, $endpoint );
        $output = curl_exec( $ch );
        if ( curl_errno( $ch ) ) {
            echo 'Curl error 2: ' . curl_error( $ch );
        }
        curl_close( $ch );
        return $output;
    }
    /**
     * ****action_currenttimestamp()****
     * @return time format
     */
    public function currenttimestamp()
    {
        return date( "Y:m:d H:i:s", time() );
    }
    /**
     * ****DisplayDateTimeFormat()****
     * @param $input_date_time string
     * @return  time format
     */
    public function DisplayDateTimeFormat( $input_date_time )
    {
        //getting input data from last login db field
        //===========================================
        $input_date_split        = explode( "-", $input_date_time );
        //splitting year and time in two arrays
        //=====================================
        $input_date_explode      = explode( ' ', $input_date_split[2] );
        $input_date_explode1     = explode( ':', $input_date_explode[1] );
        //getting to display datetime format
        //==================================
        $display_datetime_format = date( 'j M Y h:i:s A', mktime( $input_date_explode1[0], $input_date_explode1[1], $input_date_explode1[2], $input_date_split[1], $input_date_explode[0], $input_date_split[0] ) );
        return $display_datetime_format;
    }
    /**
     * ****get_bitly_short_url()****
     *
     * @param url format
     * @param 
     * @return  the shortened bitly url
     */
    public function get_bitly_short_url( $url, $login, $appkey, $format = 'txt' )
    {
        $connectURL = 'http://api.bit.ly/v3/shorten?login=' . $login . '&apiKey=' . $appkey . '&uri=' . urlencode( $url ) . '&format=' . $format;
        return $this->curl_get_result( $connectURL );
    }
    public function curl_get_result( $url )
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        $data = curl_exec( $ch );
        curl_close( $ch );
        return $data;
    }
} // End Website
