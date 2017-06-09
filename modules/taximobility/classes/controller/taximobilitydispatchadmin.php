<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
* Contains dispatch admin details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************/
abstract class Controller_TaximobilityDispatchadmin extends Controller_Template
{
    //Default variables
    public $template = "admin/taxi_dispatch/template";
    public $selected_page_title;
    public $page_title;
    public $filter;
    public $status;
    public $footer_contents;
    /**
     ****__construct()****
     */
    public function __construct( Request $request, Response $response )
    {
        $controller = $request->controller();
        $action     = $request->action();
        //Session instance
        $this->session       = Session::instance();
        $this->urlredirect   = Request::current();
        $this->emailtemplate = Model::factory( 'emailtemplate' );
        $commonmodel         = $this->commonmodel = Model::factory( 'commonmodel' );
        if ( file_exists( DOCROOT . 'application/classes/common_config.php' ) ) {
            require Kohana::find_file( 'classes', 'common_config' );
        }
        $company_id = isset( $_SESSION['company_id'] ) ? $_SESSION['company_id'] : "";
        $this->lang = $this->session->get( 'lang' );
        /*if ( $this->lang != "" ) {
            $lang = $this->lang;
        } else {
            $lang = "en";
        }*/
        if ( $this->lang != "" ) {
            $lang = $this->lang;
            if(LANG_INFO==1 && $lang=='en'){
                $lang='endef';
            }
        } else {
            if(LANG_INFO==1){
                $lang='endef';
            }else{
                $lang = "en";
            }
        }
        $this->currlang            = I18n::lang( $lang );
        $this->javascript_language = "";
        View::bind_global( 'js_language', $this->javascript_language );
        /** get location **/
        $location = $this->commonmodel->company_location( $company_id );
        if ( count( $location ) > 0 ) {
            $this->country_company = $location[0]['login_country'];
            $this->state_company   = $location[0]['login_state'];
            $this->city_company    = $location[0]['login_city'];
        }
        $this->usertype  = $this->session->get( 'user_type' );
        $this->username  = $this->session->get( 'username' );
        $this->firstname = $this->session->get( 'first_name' );
        $this->adminid   = $this->session->get( 'userid' );
        //Filter type    
        //Get cookie values if cookie is set and apply to session variables
        Cookie::$salt    = 'userid';
        $cookie          = Cookie::get( 'userid' );
        if ( $cookie ) {
            $this->session->set( "userid", $cookie );
            $user_details = $this->authorize->select_user_details_by_id( $cookie );
            if ( count( $user_details ) > 0 ) {
                $this->session->set( "user_type", $user_details[0]['user_type'] );
                $this->session->set( "email", $user_details[0]['email'] );
            }
        }
        $this->userid = $this->session->get( "userid" );
        //Css & Script include for admin
        /**To Define path for selected theme**/
        define( "ADMINIMGPATH", URL_BASE . 'public/admin/images/' );
        define( "CSSADMIN", URL_BASE . 'public/admin/' );
        define( "ADMINCSSPATH", CSSADMIN . 'css/' );
        $adminstyles  = array(
             ADMINCSSPATH . 'admin_style.css' => 'screen',
            ADMINCSSPATH . 'jquery-ui-1.8.11.custom.css' => 'screen' 
        );
        $adminscripts = array(
             SCRIPTPATH . 'jquery-1.4.3.min.js' 
        );
        View::bind_global( 'adminstyles', $adminstyles );
        View::bind_global( 'adminscripts', $adminscripts );
        //Users Themes
        define( "THEME", "default/" );
        define( "USERVIEW", "themes/" . THEME );
        define( "CSSPATH", "public/" . THEME . "css/" );
        $userstyles  = array(
             CSSPATH . 'layout.css' => 'screen' 
        );
        $userscripts = array(
             SCRIPTPATH . 'jquery.jcarousel.pack.js',
            SCRIPTPATH . 'jquery-1.4.2.min.js' 
        );
        View::bind_global( 'styles', $userstyles );
        View::bind_global( 'scripts', $userscripts );
        $this->app_name          = $this->commonmodel->select_site_settings( 'app_name', SITEINFO );
        $this->siteemail         = $this->commonmodel->select_site_settings( 'email_id', SITEINFO );
        $this->all_currency_code = "";
        $this->currencysymbol    = "";
        //binding variables to views    
        View::bind_global( 'app_name', $this->app_name );
        View::bind_global( 'currencysymbol', $this->currencysymbol );
        View::bind_global( 'siteemail', $this->siteemail );
        View::bind_global( 'usertype', $this->usertype );
        View::bind_global( 'username', $this->username );
        View::bind_global( 'first_name', $this->firstname );
        View::bind_global( 'adminid', $this->adminid );
        $company_currency = findcompany_currency( $company_id );
        View::bind_global( 'currency_code', $this->all_currency_code );
        View::bind_global( 'company_currency', $company_currency );
        View::bind_global( 'action', $action );
        View::bind_global( 'controller', $controller );
        View::bind_global( 'selected_page_title', $this->selected_page_title );
        //status to all views
        View::bind_global( 'status', $this->allstatus );
        $this->meta_description = '';
        $this->meta_keywords    = '';
        $this->title            = '';
        View::bind_global( 'meta_description', $this->meta_description );
        View::bind_global( 'page_title', $this->page_title );
        View::bind_global( 'meta_keywords', $this->meta_keywords );
        /** for footer info **/
        $footer_contents = "";
        View::bind_global( 'footer_contents', $footer_contents );
        $userid         = $this->session->get( 'userid' );
        $usrid          = $userid;
        //Assign the request to the controller
        $this->request  = $request;
        //Assign a response to the controller
        $this->response = $response;
        /*********** Force profile Update first time login *********** */
        if ( PACKAGE_TYPE == 0 ) {
            $package           = Model::factory( 'package' );
            $chk_admin_profile = $package->get_site_info();
            $current_url       = Request::$current->uri();
            $userid            = isset( $_SESSION["userid"] ) ? $_SESSION["userid"] : '';
            if ( isset( $chk_admin_profile[0]['profile_status'] ) ) {
            } else {
                $chk_admin_profile[0]['profile_status'] = 0;
            }
            if ( ( $chk_admin_profile[0]['profile_status'] == 0 ) && ( $userid != '' && $current_url != 'package/editprofile_user/1' && $current_url != 'add/getTelephoneCode' && $current_url != 'add/getlist_state' && $current_url != 'add/getcitylist' && $current_url != 'admin/logout' && $current_url != 'add/getassigntaxilist' ) ) {
                Message::error( __( 'update_all_profile_details' ) );
                Request::$current->redirect( "package/editprofile_user/1" );
            }
        }
    } //End of construct method
    /**
     *****encode()****
     * @return encoded API Result
     */
    public function encode( $result )
    {
        return json_encode( $result );
    }
    /**
     *****decode()****
     * @return decoded API Result
     */
    public function decode( $result )
    {
        $result  = json_decode( $result );
        $version = explode( ".", phpversion() );
        if ( $version[0] > 5 ) {
            switch ( json_last_error() ) {
                case JSON_ERROR_NONE:
                    $error = "";
                    break;
                case JSON_ERROR_DEPTH:
                    $error = "Maximum stack depth exceeded";
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error = "Underflow or the modes mismatch";
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $error = "Unexpected control character found";
                    break;
                case JSON_ERROR_SYNTAX:
                    $error = "Syntax error, malformed JSON";
                    break;
                case JSON_ERROR_UTF8:
                    $error = "Malformed UTF-8 characters, possibly incorrectly encoded";
                    break;
                default:
                    $error = "";
                    break;
            }
            if ( !empty( $error ) ) {
                throw new exception( "JSON Error: " . $error );
            }
        }
        return $result;
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
                $headers .= 'Content-type:text/html;charset=iso-8859-1' . "\r\n";
                $headers .= 'From: ' . $from . "\r\n";
                switch ( $type ) {
                    case "smtp":
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
                        $smtp_config1 = array(
                             'driver' => 'smtp',
                            'options' => array(
                                 'hostname' => 'smtp.gmail.com',
                                'username' => 'johnjoeshep@gmail.com',
                                'password' => 'test@123',
                                'port' => '465',
                                'encryption' => 'ssl' 
                            ) 
                        );
                        //mail sending option here
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
    /**
     * ****DisplayDateTimeFormat()****
     *
     * @param $input_date_time string
     * @param 
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
} // End Welcome