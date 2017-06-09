<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
* Contains Taxidispatch details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************/
class Controller_TaximobilityTaxidispatch extends Controller_Dispatchadmin
{
    public function __construct( Request $request, Response $response )
    {
        parent::__construct( $request, $response );
        if ( !isset( $_SESSION['company_id'] ) ) {
            $this->urlredirect->redirect( 'company/login' );
        }
        DEFINE( "DISPATCH_100", "taxidispatch" );
        $this->taxi_dispatch_model          = Model::factory( DISPATCH_100 );
        $company_id                         = ( isset( $_SESSION['company_id'] ) ) ? $_SESSION['company_id'] : 0;
        $this->company_id                   = $company_id;
        $commonmodel                        = Model::factory( 'commonmodel' );
        $this->company_all_currenttimestamp = $commonmodel->getcompany_all_currenttimestamp( $company_id );
        View::bind_global( 'company_all_currenttimestamp', $this->company_all_currenttimestamp );
        $cdate = date( 'd/m H:i:s', strtotime( $this->company_all_currenttimestamp ) );
        View::bind_global( 'cdate', $cdate );
    }
    public function action_dashboard()
    {
        $this->is_login();
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'S' ) {
            $this->request->redirect( "admin/login" );
        }
        $add_model         = Model::factory( 'add' );
        $common_model      = Model::factory( 'commonmodel' );
        // Get Pickup & Drop location Lat & Long using Google API 
        $locationArr       = $this->commonmodel->companyLocationDetails( $this->company_id );
        $countryName       = ( isset( $locationArr[0]['country_name'] ) ) ? $locationArr[0]['country_name'] : '';
        $stateName         = ( isset( $locationArr[0]['state_name'] ) ) ? $locationArr[0]['state_name'] : '';
        $cityName          = ( isset( $locationArr[0]['city_name'] ) ) ? $locationArr[0]['city_name'] : '';
        $company_location  = $cityName . ',' . $stateName . ',' . $countryName;
        //$company_location = "Coimbatore, Tamil Nadu, India";
        $current_location  = $this->getLatLong( $company_location );
        $current_latitude  = $current_location[0];
        $current_longitude = $current_location[1];
        $company_id        = $_SESSION['company_id'];
        $company_tax       = ( FARE_SETTINGS == 2 && !empty( $company_id ) ) ? $common_model->company_tax( $company_id ) : TAX;
        $company_timezone  = $common_model->company_timezone( $company_id );
        /**To get the form submit button name**/
        $create_submit     = arr::get( $_REQUEST, 'create' );
        $dispatch_submit   = arr::get( $_REQUEST, 'dispatch' );
        $update_submit     = arr::get( $_REQUEST, 'update' );
        $update_dispatch   = arr::get( $_REQUEST, 'update_dispatch' );
        $dispatch_type     = arr::get( $_REQUEST, 'dispatch_type' );
        $model_details     = $this->taxi_dispatch_model->model_details();
        $errors            = array();
        $post_values       = array();
        if ( ( $create_submit || $dispatch_submit ) ) {
            $post_values = $_POST;
            $post        = Arr::map( 'trim', $this->request->post() );
            $validator   = $this->taxi_dispatch_model->validate_dispatchbooking( arr::extract( $post, array(
                 'firstname',
                'email',
                'country_code',
                'phone',
                'current_location',
                'pickup_lat',
                'pickup_lng',
                'drop_location',
                'drop_lat',
                'drop_lng',
                'pickup_date',
                'pickup_time',
                'taxi_model',
                'recurrent',
                'lablename',
                'frmdate',
                'todate',
                'total_fare',
                'notes' 
            ) ) );
            if ( $validator->check() ) {
                $random_key      = text::random( $type = 'alnum', $length = 10 );
                $password        = text::random( $type = 'alnum', $length = 6 );
                $req_result      = $this->taxi_dispatch_model->addbooking( $post, $random_key, $password, $company_tax );
                $send_mail       = $req_result['send_mail'];
                $passenger_logid = $req_result['pass_logid'];
                $recurrent_id    = $req_result['recurrent_id'];
                $insert_booking  = $req_result['insert_booking'];
                if ( $passenger_logid != '' ) {
                    /* Create Log */
                    $company_id  = $_SESSION['company_id'];
                    $log_message = __( 'log_message_added' );
                    $log_message = str_replace( "PASS_LOG_ID", $passenger_logid, $log_message );
                    $log_booking = __( 'log_booking_added' );
                    $log_booking = str_replace( "PASS_LOG_ID", $passenger_logid, $log_booking );
                    $log_status  = $this->taxi_dispatch_model->create_logs( $passenger_logid, $company_id, $user_createdby, $log_message, $log_booking );
                    /* Create Log */
                } else {
                    $company_id  = $_SESSION['company_id'];
                    $log_message = __( 'log_message_recurrent_added' );
                    $log_message = str_replace( "RECURRENT_ID", $recurrent_id, $log_message );
                    $log_status  = $this->taxi_dispatch_model->create_logs( $recurrent_id, $company_id, $user_createdby, $log_message );
                }
                if ( ( $passenger_logid ) ) {
                    if ( $send_mail == 'S' ) {
                        /** Mail to new User Start **/
                        $mail              = "";
                        $pass_phNo         = $post['country_code'] . ' - ' . $post['phone'];
                        $replace_variables = array(
                             REPLACE_LOGO => EMAILTEMPLATELOGO,
                            REPLACE_SITENAME => $this->app_name,
                            REPLACE_USERNAME => $post['firstname'],
                            REPLACE_MOBILE => $pass_phNo,
                            REPLACE_PASSWORD => $password,
                            REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                            REPLACE_SITEEMAIL => $this->siteemail,
                            REPLACE_SITEURL => URL_BASE,
                            REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                            REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
                        );
                        $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'passenger-register.html', $replace_variables );
                        $to                = $_POST['email'];
                        $from              = $this->siteemail;
                        $subject           = __( 'pass_account_details' ) . " - " . $this->app_name;
                        $redirect          = "taxidispatch/dashboard";
                        $mail_model        = Model::factory( 'add' );
                        $smtp_result       = $mail_model->smtp_settings();
                        if ( !empty( $smtp_result ) && ( $smtp_result[0]['smtp'] == 1 ) ) {
                            include( $_SERVER['DOCUMENT_ROOT'] . "/modules/SMTP/smtp.php" );
                        } else {
                            // To send HTML mail, the Content-type header must be set
                            $headers = 'MIME-Version: 1.0' . "\r\n";
                            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                            // Additional headers
                            $headers .= 'From: ' . $from . '' . "\r\n";
                            $headers .= 'Bcc: ' . $to . '' . "\r\n";
                            mail( $to, $subject, $message, $headers );
                        }
                        //free sms url with the arguments
                        if ( SMS == 1 ) {
                            $common_model    = Model::factory( 'commonmodel' );
                            $message_details = $common_model->sms_message( '1' );
                            if ( count( $message_details ) > 0 ) {
                                $to      = $pass_phNo;
                                $message = $message_details[0]['sms_description'];
                                $message = str_replace( "##SITE_NAME##", SITE_NAME, $message );
                                $message = str_replace( "##PHONE_NO##", $pass_phNo, $message );
                                $message = str_replace( "##PASSWORD##", $password, $message );
                            }
                        }
                        /** Mail to new User End **/
                    } else {
                        /** Later Booking E-mail & SMS Send Start **/
                        $message = "";
                        $message .= "Thanks for booking with us, your booking was confirmed. your booking id " . $passenger_logid . ". We will contact shortly.<br>";
                        $message .= "Pickup Date : " . $post["pickup_date"] . "<br>";
                        $message .= "Pickup Location : " . $post["current_location"] . "<br>";
                        $message .= "Drop Location : " . $post["drop_location"];
                        $replace_variables = array(
                             REPLACE_LOGO => EMAILTEMPLATELOGO,
                            REPLACE_SITENAME => $this->app_name,
                            REPLACE_USERNAME => $post['firstname'],
                            REPLACE_MESSAGE => $message,
                            REPLACE_SITEURL => URL_BASE,
                            REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                            REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
                        );
                        $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'laterbooking_cofirm_message.html', $replace_variables );
                        $to                = $_POST['email'];
                        $from              = $this->siteemail;
                        $subject           = __( 'later_booking_confirm_mail' ) . " - " . $this->app_name;
                        $redirect          = "taxidispatch/dashboard";
                        $mail_model        = Model::factory( 'add' );
                        $smtp_result       = $mail_model->smtp_settings();
                        if ( !empty( $smtp_result ) && ( $smtp_result[0]['smtp'] == 1 ) ) {
                            include( $_SERVER['DOCUMENT_ROOT'] . "/modules/SMTP/smtp.php" );
                        } else {
                            // To send HTML mail, the Content-type header must be set
                            $headers = 'MIME-Version: 1.0' . "\r\n";
                            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                            // Additional headers
                            $headers .= 'From: ' . $from . '' . "\r\n";
                            $headers .= 'Bcc: ' . $to . '' . "\r\n";
                            mail( $to, $subject, $message, $headers );
                        }
                        if ( SMS == 1 ) {
                            $message_details = $this->commonmodel->sms_message_by_title( 'booking_confirmed_sms' );
                            if ( count( $message_details ) > 0 ) {
                                $to      = $post["country_code"] . $post["phone"];
                                $message = ( count( $message_details ) ) ? $message_details[0]['sms_description'] : '';
                                $message = str_replace( "##SITE_NAME##", SITE_NAME, $message );
                                $message = str_replace( "##booking_key##", $passenger_logid, $message );
                                $this->commonmodel->send_sms( $to, $message );
                            }
                        }
                        /** Later Booking E-mail & SMS Send End **/
                    }
                    /** Dispatch **/
                    $company_id       = $_SESSION['company_id'];
                    $company_dispatch = $this->taxi_dispatch_model->company_dispatch( $company_id );
                    $tdispatch_type   = '';
                    if ( count( $company_dispatch ) > 0 ) {
                        $tdispatch_type = $company_dispatch[0]['labelname'];
                    }
                    if ( $dispatch_submit && $tdispatch_type != 1 && $passenger_logid != '' && $recurrent_id == "" ) {
?>
                       <form action="<?php
                        echo URL_BASE;
?>taxidispatch/dashboard" method="post" id="form_showpopup">
                        <input type="hidden" name="show_pass_logid" value="<?php
                        echo $passenger_logid;
?>" id="show_pass_logid" />
                        <input type="hidden" name="pickup_latitude" value="<?php
                        echo $post['pickup_lat'];
?>" id="pickup_latitude" />
                        <input type="hidden" name="pickup_longitude" value="<?php
                        echo $post['pickup_lng'];
?>" id="pickup_longitude" />
                        <input type="hidden" name="drop_latitude" value="<?php
                        echo $post['drop_lat'];
?>" id="drop_longitude" />
                        <input type="hidden" name="drop_longitude" value="<?php
                        echo $post['drop_lng'];
?>" id="drop_latitude" />
                        <input type="hidden" name="no_passengers" value="<?php
                        echo $post['no_passengers'];
?>" id="no_passengers" />
                        </form>
                        <script>    
                        document.getElementById('form_showpopup').submit();
                        </script>
                    <?php
                    } else {
                        Message::success( __( 'booking_added' ) );
                        $this->request->redirect( "taxidispatch/dashboard" );
                    }
                } else {
                    Message::success( __( 'booking_added' ) );
                    $this->request->redirect( "taxidispatch/dashboard" );
                }
            } else {
                $errors = $validator->errors( 'errors' );
            }
        } elseif ( ( $update_submit || $update_dispatch || ( $dispatch_type == 1 ) || $_POST ) && Validation::factory( $_POST ) ) {
            $post_values = $_POST;
            $post        = Arr::map( 'trim', $this->request->post() );
            $validator   = $this->taxi_dispatch_model->validate_dispatchbooking_edit( arr::extract( $post, array(
                 'edit_firstname',
                'edit_email',
                'edit_country_code',
                'edit_phone',
                'edit_current_location',
                'edit_pickup_lat',
                'edit_pickup_lng',
                'edit_drop_location',
                'edit_drop_lat',
                'edit_drop_lng',
                'edit_pickup_date',
                'edit_pickup_time',
                'edit_luggage',
                'edit_no_passengers',
                'edit_taxi_model',
                'edit_recurrent',
                'lablename',
                'frmdate',
                'todate',
                'total_fare' 
            ) ) );
            if ( $validator->check() || ( $dispatch_type == 1 ) ) {
                $random_key = text::random( $type = 'alnum', $length = 10 );
                $password   = text::random( $type = 'alnum', $length = 6 );
                if ( $dispatch_type == 1 ) {
                    $req_result = $this->taxi_dispatch_model->directdispatch( $_REQUEST['splid'] );
                } else {
                    $req_result = $this->taxi_dispatch_model->updatebooking( $post, $random_key, $password );
                }
                $send_mail       = $req_result['send_mail'];
                $passenger_logid = $req_result['pass_logid'];
                /* Create Log */
                $company_id      = $_SESSION['company_id'];
                $log_message     = __( 'log_message_updated' );
                $log_message     = str_replace( "PASS_LOG_ID", $passenger_logid, $log_message );
                $log_booking     = __( 'log_booking_updated' );
                $log_booking     = str_replace( "PASS_LOG_ID", $passenger_logid, $log_booking );
                $log_status      = $this->taxi_dispatch_model->create_logs( $passenger_logid, $company_id, $user_createdby, $log_message, $log_booking );
                /* Create Log */
                if ( $passenger_logid ) {
                    if ( $send_mail == 'S' ) {
                        /** Mail to new User **/
                        $mail              = "";
                        $pass_phNo         = $post['edit_country_code'] . ' - ' . $post['edit_phone'];
                        $replace_variables = array(
                             REPLACE_LOGO => EMAILTEMPLATELOGO,
                            REPLACE_SITENAME => $this->app_name,
                            REPLACE_USERNAME => $post['edit_firstname'],
                            REPLACE_MOBILE => $pass_phNo,
                            REPLACE_PASSWORD => $password,
                            REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                            REPLACE_SITEEMAIL => $this->siteemail,
                            REPLACE_SITEURL => URL_BASE,
                            REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                            REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
                        );
                        $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'passenger-register.html', $replace_variables );
                        $to                = $_POST['edit_email'];
                        $from              = $this->siteemail;
                        $subject           = __( 'pass_account_details' ) . " - " . $this->app_name;
                        $redirect          = "taxidispatch/dashboard";
                        $mail_model        = Model::factory( 'add' );
                        $smtp_result       = $mail_model->smtp_settings();
                        if ( !empty( $smtp_result ) && ( $smtp_result[0]['smtp'] == 1 ) ) {
                            include( $_SERVER['DOCUMENT_ROOT'] . "/modules/SMTP/smtp.php" );
                        } else {
                            // To send HTML mail, the Content-type header must be set
                            $headers = 'MIME-Version: 1.0' . "\r\n";
                            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                            // Additional headers
                            $headers .= 'From: ' . $from . '' . "\r\n";
                            $headers .= 'Bcc: ' . $to . '' . "\r\n";
                            mail( $to, $subject, $message, $headers );
                        }
                        //free sms url with the arguments
                        if ( SMS == 1 ) {
                            $common_model    = Model::factory( 'commonmodel' );
                            $message_details = $common_model->sms_message( '1' );
                            $to              = $pass_phNo;
                            $message         = $message_details[0]['sms_description'];
                            $message         = str_replace( "##SITE_NAME##", SITE_NAME, $message );
                        }
                        /** Mail to new User **/
                    }
                    /** Dispatch **/
                    $company_id       = $_SESSION['company_id'];
                    $company_dispatch = $this->taxi_dispatch_model->company_dispatch( $company_id );
                    $tdispatch_type   = '';
                    if ( count( $company_dispatch ) > 0 ) {
                        $tdispatch_type = $company_dispatch[0]['labelname'];
                    }
                    if ( $update_dispatch && $tdispatch_type != 1 && $passenger_logid != '' ) {
?>
                       <form action="<?php
                        echo URL_BASE;
?>taxidispatch/dashboard" method="post" id="form_showpopup">
                        <input type="hidden" name="show_pass_logid" value="<?php
                        echo $passenger_logid;
?>" id="show_pass_logid" />
                        <input type="hidden" name="pickup_latitude" value="<?php
                        echo $post['edit_pickup_lat'];
?>" id="pickup_latitude" />
                        <input type="hidden" name="pickup_longitude" value="<?php
                        echo $post['edit_pickup_lng'];
?>" id="pickup_longitude" />
                        <input type="hidden" name="drop_latitude" value="<?php
                        echo $post['edit_drop_lat'];
?>" id="drop_longitude" />
                        <input type="hidden" name="drop_longitude" value="<?php
                        echo $post['edit_drop_lng'];
?>" id="drop_latitude" />
                        <input type="hidden" name="no_passengers" value="<?php
                        echo $post['edit_no_passengers'];
?>" id="no_passengers" />
                        </form>
                        <script>    
                        document.getElementById('form_showpopup').submit();
                        </script>
                    <?php
                    } else {
                        Message::success( __( 'booking_added' ) );
                        $this->request->redirect( "taxidispatch/dashboard" );
                    }
                }
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $all_company_map_list       = $this->taxi_dispatch_model->all_driver_map_list( "" );
        $get_active_company_details = $this->taxi_dispatch_model->get_active_company_details();        $view                       = View::factory( TAXI_DISPATCH . 'dashboard' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'company_tax', $company_tax )->bind( 'company_timezone', $company_timezone )->bind( 'model_details', $model_details )->bind( 'postvalue', $post_values )->bind( 'all_company_map_list', $all_company_map_list )->bind( 'show_popup', $_REQUEST )->bind( 'get_taxi_model', $get_taxi_model )->bind( 'current_latitude', $current_latitude )->bind( 'current_longitude', $current_longitude )->bind( 'companyId', $this->company_id )->bind( 'get_active_company_details', $get_active_company_details );
        $this->template->title      = SITENAME . " | " . __( 'dashboard' );
        $this->template->page_title = __( 'dashboard' );
        $this->template->content    = $view;
        //unset($default);
    }
    public function action_sample()
    {
        $view                       = View::factory( TAXI_DISPATCH . 'sample' );
        $this->template->title      = SITENAME . " | " . __( 'Sample' );
        $this->template->page_title = __( 'Sample' );
        $this->template->content    = $view;
    }
    /**
     * ****action_is_login()****
     * @return check user logged or not
     */
    public function is_login()
    {
        $session = Session::instance();
        //get current url and set it into session
        //========================================            
        /**To check Whether the user is logged in or not**/
        if ( !isset( $this->session ) || ( !$this->session->get( 'userid' ) ) )
            {
            Message::error( __( 'login_access' ) );
            $request->redirect( "/company/login/" );
        }
        return;
    }
    public function action_edit_booking()
    {
        $pass_log_id         = $_REQUEST['passenger_logid'];
        $edit_bookingdetails = $this->taxi_dispatch_model->edit_bookingdetails( $pass_log_id );
        echo json_encode( $edit_bookingdetails );
        exit;
    }
    public function action_firstname_load_new()
    {
        $output           = '';
        $name             = array();
        $like_q           = arr::get( $_REQUEST, 'query' );
        $like_q           = urlencode( $like_q );
        $getmodel_details = $this->taxi_dispatch_model->getuser_details( $like_q, '1' );
        foreach ( $getmodel_details as $details ) {
            $name[] = $details['name'] . ' - (' . $details['phone'] . ')';
        }
        echo json_encode( $name );
        exit;
    }
    public function action_email_load_new()
    {
        $output           = '';
        $like_q           = arr::get( $_REQUEST, 'q' );
        $like_q           = urlencode( $like_q );
        $getmodel_details = $this->taxi_dispatch_model->getuser_details( $like_q, '2' );
        foreach ( $getmodel_details as $details ) {
            $name[] = $details['email'];
        }
        echo json_encode( $name );
        exit;
    }
    public function action_phone_load_new()
    {
        $output           = '';
        $like_q           = arr::get( $_REQUEST, 'q' );
        $like_q           = urlencode( $like_q );
        $getmodel_details = $this->taxi_dispatch_model->getuser_details( $like_q, '3' );
        foreach ( $getmodel_details as $details ) {
            $name[] = $details['phone'];
        }
        echo json_encode( $name );
        exit;
    }
    public function action_check_pass_phone_email_exist()
    {
        if ( $_GET['pass_id'] != '' ) {
            $result = $this->taxi_dispatch_model->check_passenger_email_phone_exist( $_GET['pass_id'], $_GET['pass_email'], $_GET['pass_phone'] );
            echo $result;
        }
        exit;
    }
    public function action_get_passengerDetails_new()
    {
        $output           = '';
        $name             = arr::get( $_REQUEST, 'field_name' );
        $getmodel_details = $this->taxi_dispatch_model->getpassenger_Detailinfo_new( $_REQUEST );
        if ( is_array( $getmodel_details ) && count( $getmodel_details ) > 0 ) {
            echo $getmodel_details[0]['id'] . ',' . $getmodel_details[0]['name'] . ',' . $getmodel_details[0]['email'] . ',' . $getmodel_details[0]['phone'] . ',' . $getmodel_details[0]['country_code'];
        } else {
            echo 0;
        }
        exit;
    }
    public function action_driver_status_details_search_new()
    {
        $post_values          = $_POST;
        $driver_status        = $_REQUEST['driver_status'];
        $taxi_company         = $_REQUEST['taxi_company'];
        $send_array           = array(
             'driver_status' => $driver_status,
            'taxi_company' => $taxi_company 
        );
        $all_company_map_list = $this->taxi_dispatch_model->driver_status_details( $_REQUEST );
        $markers              = array();
        $tmarkers             = array();
        $book_now             = "";
        foreach ( $all_company_map_list as $key => $val ) {
            if ( $val['driver_status'] == "F" && $val['shift_status'] == "IN" ) {
                $driver_info = '<span style="color:green">' . __( 'free_in' ) . '</span>';
                $book_now    = '<button type="button" class="btn btn-outline btn-primary btn-xs" name="bookingnow" onclick="bookingnow_click(this.id);" id="driverid_' . $val['driver_id'] . '" >' . __( 'booknow' ) . '</button>';
            } elseif ( $val['driver_status'] == "F" && $val['shift_status'] == "OUT" ) {
                $driver_info = '<span style="color:blue">' . __( 'free_out' ) . '</span>';
            } elseif ( $val['driver_status'] == "B" ) {
                $driver_info = '<span style="color:red">' . __( 'trip_assigned' ) . '</span>';
            } elseif ( $val['driver_status'] == "A" ) {
                $driver_info = '<span style="color:#07841E">' . __( 'hired' ) . '</span>';
            }
            $update_date = $val['update_date'];
            $drv_info    = '<span class="info-content">' . ucfirst( $val['name'] ) . '</span>';
            $drv_info .= '</br>';
            $drv_info .= '<span class="info-content">' . $driver_info . '</span>';
            $drv_info .= '</br>';
            $drv_info .= '<span class="info-content">' . $update_date . '</span>';
            if ( $book_now != "" ) {
                $drv_info .= '</br>';
            }
            $markers[$key]['info']         = $drv_info;
            $markers[$key]['lat']          = $val['latitude'];
            $markers[$key]['lng']          = $val['longitude'];
            $markers[$key]['status']       = $val['driver_status'];
            $markers[$key]['shift_status'] = $val['shift_status'];
            $book_now                      = ""; //For clear existing value
        }
        echo json_encode( $markers );
        exit;
    }
    public function action_driver_status_search_details()
    {
        $driver_status = $_REQUEST['driver_status'];
        $taxi_model    = $_REQUEST['taxi_model'];
        if ( $taxi_model != "" ) {
            $all_company_map_list = $this->taxi_dispatch_model->driver_status_details_model( $_REQUEST );
        } else {
            $all_company_map_list = $this->taxi_dispatch_model->driver_status_details( $_REQUEST );
        }
        $i               = 0;
        $driver_active   = 0;
        $driver_free_in  = 0;
        $driver_free_out = 0;
        $driver_busy     = 0;
        if ( count( $all_company_map_list ) > 0 ) {
            $output = "<h4>" . __( 'driver_details' ) . "<span id='driver_dets_count'></span> </h4>";
            $output .= "<ul class='driver_details'>";
            foreach ( $all_company_map_list as $key => $val ) {
                if ( $val['driver_status'] == "A" ) {
                    $status     = "Active";
                    $span_class = "driver_status_active";
                    $driver_active++;
                } elseif ( $val['driver_status'] == "F" && $val['shift_status'] == "IN" ) {
                    $status     = "Free In";
                    $span_class = "driver_status_in";
                    $driver_free_in++;
                } elseif ( $val['driver_status'] == "F" && $val['shift_status'] == "OUT" ) {
                    $status     = "Free Out";
                    $span_class = "driver_status_out";
                    $driver_free_out++;
                } elseif ( $val['driver_status'] == "B" ) {
                    $status     = "Busy";
                    $span_class = "driver_status_busy";
                    $driver_busy++;
                }
                $i++;
                if ( $i % 2 ) {
                    $class = "driver_status_li_one";
                } else {
                    $class = "driver_status_li_two";
                }
                $driver_page = URL_BASE . "manage/driverinfo/" . $val['driver_id'];
                $output .= "<li class=" . $class . ">";
                $output .= "<span><a href='" . $driver_page . "' target='_blank' title='Goto'>" . ucfirst( $val['name'] ) . "</a></span>";
                $output .= "<span class=" . $span_class . ">" . $status . "</span>";
                $output .= "</li>";
            }
            $output .= "</ul>";
        } else {
            $no_driver_class = "no_driver";
            $output          = "<ul class='driver_details'>";
            $output .= "<h4>Driver Details</h4>";
            $output .= "<li class='" . $no_driver_class . "'>";
            $output .= "No Driver Found";
            $output .= "</li>";
        }
        $driver_count_dets = "(A - " . $driver_active . ", In - " . $driver_free_in . ", Out - " . $driver_free_out . ", B - " . $driver_busy . ")";
        echo $output . "#" . $driver_count_dets;
        exit;
    }
    /** This function is used to get driver list as well as icons in the map in dispatcher dashboard **/
    public function action_driver_list_with_status()
    {
        $all_company_map_list = $this->taxi_dispatch_model->get_driver_list_with_status( $_REQUEST );
        $i                    = 0;
        $driver_active        = 0;
        $driver_free_in       = 0;
        $driver_free_out      = 0;
        $driver_busy          = 0;
        $markers              = array();
        $book_now             = "";
        if ( count( $all_company_map_list ) > 0 ) {
            $order           = array(
                "FIN",
                "B",
                "A",
                "FOUT" 
            );
            $new_order_array = array();
            foreach ( $all_company_map_list as $key => $val ) {
                $con               = ( $val['driver_status'] == "F" ) ? $val['driver_status'] . $val['shift_status'] : $val['driver_status'];
                $new_order_array[] = array(
                     'id' => $con,
                    'driver_id' => $val["driver_id"],
                    'name' => $val['name'],
                    'driver_status' => $val['driver_status'],
                    'update_date' => $val['update_date'],
                    'latitude' => $val['latitude'],
                    'longitude' => $val['longitude'],
                    'shift_status' => $val['shift_status'],
                    'updatetime_difference' => $val['updatetime_difference'],
                    'model_name' => $val['model_name'] 
                );
            }
            usort( $new_order_array, function( $a, $b ) use ($order)
            {
                $pos_a = array_search( $a['id'], $order );
                $pos_b = array_search( $b['id'], $order );
                return $pos_a - $pos_b;
            } );
            $output = "<h4>Driver Details <span id='driver_dets_count'></span> </h4>";
            $output .= "<ul class='driver_details'>";
            foreach ( $new_order_array as $key => $val ) {
                if ( $val['driver_status'] == "A" ) {
                    $status      = "Active";
                    $span_class  = "driver_status_active";
                    $driver_info = '<span style="color:orange">' . __( 'hired' ) . '</span>';
                    $driver_active++;
                } elseif ( $val['driver_status'] == "F" && $val['shift_status'] == "IN" ) {
                    $status      = "Free In";
                    $span_class  = "driver_status_in";
                    $driver_info = '<span style="color:green">' . __( 'free_in' ) . '</span>';
                    $book_now    = '<button type="button" class="btn btn-outline btn-primary btn-xs" name="bookingnow" onclick="bookingnow_click(this.id);" id="driverid_' . $val['driver_id'] . '" >' . __( 'booknow' ) . '</button>';
                    $driver_free_in++;
                } elseif ( $val['driver_status'] == "F" && $val['shift_status'] == "OUT" ) {
                    $status      = "Free Out";
                    $span_class  = "driver_status_out";
                    $driver_info = '<span style="color:blue">' . __( 'free_out' ) . '</span>';
                    $driver_free_out++;
                } elseif ( $val['driver_status'] == "B" ) {
                    $status      = "Busy";
                    $span_class  = "driver_status_busy";
                    $driver_info = '<span style="color:red">' . __( 'trip_assigned' ) . '</span>';
                    $driver_busy++;
                }
                $i++;
                if ( $i % 2 ) {
                    $class = "driver_status_li_one";
                } else {
                    $class = "driver_status_li_two";
                }
                $driver_page = URL_BASE . "manage/driverinfo/" . $val['driver_id'];
                $output .= "<li class=" . $class . ">";
                $output .= "<span><a href='" . $driver_page . "' target='_blank' title='Goto'>" . ucfirst( $val['name'] . " - (" . $val['model_name'] . ")" ) . "</a></span>";
                $output .= "<span class=" . $span_class . ">" . $status . "</span>";
                $output .= "</li>";
                //driver display in map side
                $drv_info = '<div class="info_drivercontent">';
                $drv_info .= '<span class="info-content">' . ucfirst( $val['name'] . " - " . $val['model_name'] ) . '</span>';
                $drv_info .= '</br>';
                $drv_info .= '<span class="info-content">' . $driver_info . '</span>';
                $drv_info .= '</br>';
                $drv_info .= '<span class="info-content">' . $val['update_date'] . '</span>';
                if ( $book_now != "" ) {
                    $drv_info .= '</br>';
                }
                $drv_info .= '</div>';
                $markers[$key]['info']         = $drv_info;
                $markers[$key]['lat']          = $val['latitude'];
                $markers[$key]['lng']          = $val['longitude'];
                $markers[$key]['status']       = $val['driver_status'];
                $markers[$key]['shift_status'] = $val['shift_status'];
            }
            $output .= "</ul>";
        } else {
            $no_driver_class = "no_driver";
            $output          = "<ul class='driver_details'>";
            $output .= "<h4>Driver Details</h4>";
            $output .= "<li class='" . $no_driver_class . "'>";
            $output .= "No Driver Found";
            $output .= "</li>";
        }
        $driver_count_dets = "(A - " . $driver_active . ", In - " . $driver_free_in . ", Out - " . $driver_free_out . ", B - " . $driver_busy . ")";
        echo json_encode( $markers ) . "#" . $output . "#" . $driver_count_dets;
        exit;
    }
    public function action_get_recent_activity()
    {
        $output         = '';
        $get_logcontent = $this->taxi_dispatch_model->load_logcontent();
        $i              = 0;
        if ( count( $get_logcontent ) > 0 ) {
            foreach ( $get_logcontent as $details ) {
                $i++;
                if ( $i % 2 ) {
                    $class = "show_logs_li_one";
                } else {
                    $class = "show_logs_li_two";
                }
                $badge_class = "badge";
                $time_now    = $this->time_since( $details['log_createdate'] );
                $output .= "<li class=" . $class . ">";
                $output .= "<span>" . $details['log_message'] . "</span>";
                $output .= "<span class=" . $badge_class . ">" . $time_now . "</span>";
                $output .= "</li>";
            }
        } else {
            $output .= "<li>";
            $output .= "<span>" . __( 'no_data' ) . "</span>";
            $output .= "</li>";
        }
        echo $output;
        exit;
    }
    public function time_since( $date )
    {
        $time_ago     = strtotime( $date );
        $cur_time     = $this->company_all_currenttimestamp;
        $cur_time     = strtotime( $cur_time );
        $time_elapsed = $cur_time - $time_ago;
        $seconds      = $time_elapsed;
        $minutes      = round( $time_elapsed / 60 );
        $hours        = round( $time_elapsed / 3600 );
        $days         = round( $time_elapsed / 86400 );
        $weeks        = round( $time_elapsed / 604800 );
        $months       = round( $time_elapsed / 2600640 );
        $years        = round( $time_elapsed / 31207680 );
        // Seconds
        if ( $seconds <= 60 ) {
            return $timen = "just now";
        }
        //Minutes
        else if ( $minutes <= 60 ) {
            if ( $minutes == 1 ) {
                return $timen = "one min ago";
            } else {
                return $timen = "$minutes mins ago";
            }
        }
        //Hours
        else if ( $hours <= 24 ) {
            if ( $hours == 1 ) {
                return $timen = "an hour ago";
            } else {
                return $timen = "$hours hrs ago";
            }
        }
        //Days
        else if ( $days <= 7 ) {
            if ( $days == 1 ) {
                return $timen = "yesterday";
            } else {
                return $timen = "$days days ago";
            }
        }
        //Weeks
        else if ( $weeks <= 4.3 ) {
            if ( $weeks == 1 ) {
                return $timen = "a week ago";
            } else {
                return $timen = "$weeks weeks ago";
            }
        }
        //Months
        else if ( $months <= 12 ) {
            if ( $months == 1 ) {
                return $timen = "a month ago";
            } else {
                return $timen = "$months months ago";
            }
        }
        //Years
        else {
            if ( $years == 1 ) {
                return $timen = "one year ago";
            } else {
                return $timen = "$years years ago";
            }
        }
    }
    public function action_all_booking_list_manage()
    {
        /** get available drivers */
        $output_driver        = "";
        $all_company_map_list = $this->taxi_dispatch_model->get_driver_list_with_status( $_REQUEST );
        //echo '<pre>';print_r($all_company_map_list);exit;
        $i                    = 0;
        $driver_active        = 0;
        $driver_free_in       = 0;
        $driver_free_out      = 0;
        $driver_busy          = 0;
        $markers              = array();
        $book_now             = "";
        if ( count( $all_company_map_list ) > 0 ) {
            $order           = array(
				"FIN",
                "B",
                "A",
                "FOUT" 
            );
            $new_order_array = array();
            foreach ( $all_company_map_list as $key => $val ) {
                $con               = ( $val['driver_status'] == "F" ) ? $val['driver_status'] . $val['shift_status'] : $val['driver_status'];
                $new_order_array[] = array(
                     'id' => $con,
                    'driver_id' => $val["driver_id"],
                    'name' => $val['name'],
                    'driver_status' => $val['driver_status'],
                    'update_date' => $val['update_date'],
                    'latitude' => $val['latitude'],
                    'longitude' => $val['longitude'],
                    'shift_status' => $val['shift_status'],
                    'updatetime_difference' => $val['updatetime_difference'],
                    'model_name' => $val['model_name'] 
                );
            }
            usort( $new_order_array, function( $a, $b ) use ($order)
            {
                $pos_a = array_search( $a['id'], $order );
                $pos_b = array_search( $b['id'], $order );
                return $pos_a - $pos_b;
            } );
            $output_driver = "<h4>Driver Details <span id='driver_dets_count'></span> </h4>";
            $output_driver .= "<ul class='driver_details'>";
            foreach ( $new_order_array as $key => $val ) {
                if ( $val['driver_status'] == "A" ) {
                    $status      = "Active";
                    $span_class  = "driver_status_active";
                    $driver_info = '<span style="color:orange">' . __( 'hired' ) . '</span>';
                    $driver_active++;
                } elseif ( $val['driver_status'] == "F" && $val['shift_status'] == "IN" ) {
                    $status      = "Free In";
                    $span_class  = "driver_status_in";
                    $driver_info = '<span style="color:green">' . __( 'free_in' ) . '</span>';
                    $book_now    = '<button type="button" class="btn btn-outline btn-primary btn-xs" name="bookingnow" onclick="bookingnow_click(this.id);" id="driverid_' . $val['driver_id'] . '" >' . __( 'booknow' ) . '</button>';
                    $driver_free_in++;
                } elseif ( $val['driver_status'] == "F" && $val['shift_status'] == "OUT" ) {
                    $status      = "Free Out";
                    $span_class  = "driver_status_out";
                    $driver_info = '<span style="color:blue">' . __( 'free_out' ) . '</span>';
                    $driver_free_out++;
                } elseif ( $val['driver_status'] == "B" ) {
                    $status      = "Busy";
                    $span_class  = "driver_status_busy";
                    $driver_info = '<span style="color:red">' . __( 'trip_assigned' ) . '</span>';
                    $driver_busy++;
                }
                $i++;
                if ( $i % 2 ) {
                    $class = "driver_status_li_one";
                } else {
                    $class = "driver_status_li_two";
                }
                $driver_page = URL_BASE . "manage/driverinfo/" . $val['driver_id'];
                $output_driver .= "<li class=" . $class . ">";
                $output_driver .= "<span><a href='" . $driver_page . "' target='_blank' title='Goto'>" . ucfirst( $val['name'] . " - (" . $val['model_name'] . ")" ) . "</a></span>";
                $output_driver .= "<span class=" . $span_class . ">" . $status . "</span>";
                $output_driver .= "</li>";
                //driver display in map side
                $drv_info = '<div class="info_drivercontent">';
                $drv_info .= '<span class="info-content">' . ucfirst( $val['name'] . " - " . $val['model_name'] ) . '</span>';
                $drv_info .= '</br>';
                $drv_info .= '<span class="info-content">' . $driver_info . '</span>';
                $drv_info .= '</br>';
                $drv_info .= '<span class="info-content">' . $val['update_date'] . '</span>';
                if ( $book_now != "" ) {
                    $drv_info .= '</br>';
                }
                $drv_info .= '</div>';
                $markers[$key]['info']         = $drv_info;
                $markers[$key]['lat']          = $val['latitude'];
                $markers[$key]['lng']          = $val['longitude'];
                $markers[$key]['status']       = $val['driver_status'];
                $markers[$key]['shift_status'] = $val['shift_status'];
            }
            $output_driver .= "</ul>";
        } else {
            $no_driver_class = "no_driver";
            $output_driver   = "<ul class='driver_details'>";
            $output_driver .= "<h4>Driver Details</h4>";
            $output_driver .= "<li class='" . $no_driver_class . "'>";
            $output_driver .= "No Driver Found";
            $output_driver .= "</li>";
        }
        $driver_count_dets    = "(A - " . $driver_active . ", In - " . $driver_free_in . ", Out - " . $driver_free_out . ", B - " . $driver_busy . ")";
        // get all the booking list 
        $output               = '';
        $current_time         = $this->company_all_currenttimestamp;
        $travel_status        = isset( $_GET['travel_status'] ) ? $_GET['travel_status'] : "";
        $driver_reply_cancel  = isset( $_GET['status_cancel'] ) ? $_GET['status_cancel'] : "";
        $manage_status        = isset( $_GET['manage_status'] ) ? $_GET['manage_status'] : 0;
        $taxi_company         = isset( $_GET['taxi_company'] ) ? $_GET['taxi_company'] : 0;
        $search_txt           = isset( $_GET['search_txt'] ) ? $_GET['search_txt'] : "";
        $search_location      = isset( $_GET['search_location'] ) ? $_GET['search_location'] : "";
        $filter_date          = isset( $_GET['filter_date'] ) ? $_GET['filter_date'] : "";
        $to_date              = isset( $_GET['to_date'] ) ? $_GET['to_date'] : "";
        $booking_filter       = isset( $_GET['booking_filter'] ) ? $_GET['booking_filter'] : "";
        $taxi_model           = isset( $_GET['taxi_model'] ) ? $_GET['taxi_model'] : "";
        $send_array           = array(
             "current_time" => $current_time,
            "travel_status" => $travel_status,
            "driver_reply_cancel" => $driver_reply_cancel,
            "manage_status" => $manage_status,
            "search_txt" => $search_txt,
            "search_location" => $search_location,
            "filter_date" => $filter_date,
            "to_date" => $to_date,
            "booking_filter" => $booking_filter,
            "taxi_company" => $taxi_company,
            "taxi_model" => $taxi_model 
        );
        $get_all_booking_list = $this->taxi_dispatch_model->dispatcher_booking_list( $send_array );
        $i                    = 0;
        $sno                  = 0;
        $status_button        = "";
        $edit                 = "";
        $name_color           = "";
        $op                   = array();
        if ( count( $get_all_booking_list ) > 0 ) {
            foreach ( $get_all_booking_list as $listings ) {
                $trcolor  = 'oddtr';
                $bookType = $listings['bookingtype'];
                $bookBy   = $listings['bookby'];
                if ( $bookType == 2 && $bookBy == 2 ) {
                    $bookType = "Dispatcher";
                } else if ( $bookType == 2 && $bookBy == 1 ) {
                    $bookType = "Mobile";
                } else {
                    $bookType = "---";
                }
                $i++;
                if ( $i % 2 ) {
                    $trcolor_class = "show_tr_one";
                } else {
                    $trcolor_class = "show_tr_two";
                }
                $pass_logid     = $listings['pass_logid'];
                $pickup_time    = $listings['pickup_time'];
                $createdate     = $listings['createdate'];
                $act_pickuptime = ( $listings['act_pickuptime'] != "-" ) ? date( 'Y-m-d H:i:s', strtotime( $listings['act_pickuptime'] ) ) : $listings['act_pickuptime'];
                if ( strlen( $listings['passenger_name'] ) > 15 ) {
                    $passenger_name = ucfirst( $listings['passenger_name'] );
                    $passenger_name = mb_substr( $passenger_name, 0, 15 ) . '...';
                } else {
                    $passenger_name = $listings['passenger_name'];
                }
                $passenger_id = $listings['passenger_id'];
                $driver_name     = $listings['driver_name'];
                $driver_id       = $listings['driver_id'];
                $passengerInfo = URL_BASE.'manage/passengerinfo/'.$passenger_id;
                $driverInfo = URL_BASE.'manage/driverinfo/'.$driver_id;
                $company_name    = ( isset( $listings['company_name'] ) && $listings['company_name'] != '' ) ? $listings['company_name'] : "---";
                $telephone_code  = ( $listings['passenger_country_code'] = '' ) ? $listings['passenger_country_code'] : '+91';
                $passenger_phone = ( isset( $listings['passenger_phone'] ) && $listings['passenger_phone'] != '' ) ? $telephone_code . ' - ' . $listings['passenger_phone'] : "---";
                $model_name      = ( isset( $listings['model_name'] ) && $listings['model_name'] != '' ) ? $listings['model_name'] : "---";
                $taxicompany_id  = $listings['company_id'];
                $fixedprice      = ( !empty( $listings['fare'] ) ) ? $listings['fare'] : 0;
                $approx_distance = ( !empty( $listings['distance'] ) ) ? $listings['distance'] : 0;
                if ( $listings['travel_status'] == 1 ) {
                    $trans_details = $this->taxi_dispatch_model->dispatcher_booking_transaction( $pass_logid );
                    if ( count( $trans_details ) > 0 ) {
                        $fixedprice      = $trans_details[0]['fare'];
                        $approx_distance = $trans_details[0]['distance'];
                    }
                }
                $approx_distance = ( UNIT_NAME == "MILES" ) ? ( $approx_distance * 0.621371 ) : $approx_distance;
                $approx_distance = round( $approx_distance, 2 );
                $pickup_lat      = $listings['pickup_latitude'];
                $pickup_lng      = $listings['pickup_longitude'];
                $drop_lat        = $listings['drop_latitude'];
                $drop_lng        = $listings['drop_longitude'];
                $no_passengers   = $listings['no_passengers'];
                if ( strlen( $listings['current_location'] ) > 5 ) {
                    $current_location = substr( ucfirst( $listings['current_location'] ), 0, 15 ) . "..";
                } else {
                    $current_location = $listings['current_location'];
                }
                if ( strlen( $listings['drop_location'] ) > 5 ) {
                    $drop_location = substr( ucfirst( $listings['drop_location'] ), 0, 15 ) . "..";
                } else {
                    $drop_location = $listings['drop_location'];
                }
                $dispatch_time = strtotime( $listings['dispatch_time'] );
                $total_drivers = isset( $listings['total_drivers'] ) ? $listings['total_drivers'] : "";
                $crntTimestamp = strtotime( $current_time );
                $count_sec     = date( "Y-m-d H:i:s", $crntTimestamp + 35 ); // wait for 25 sec 
                if ( $total_drivers != NULL && $total_drivers != "" ) {
                    if ( $total_drivers == 2 ) {
                        $count_sec = date( "Y-m-d H:i:s", $crntTimestamp + 15 ); // wait for 45 sec
                    } elseif ( $total_drivers == 3 ) {
                        $count_sec = date( 'Y-m-d H:i:s', $crntTimestamp - 10 );
                    } elseif ( $total_drivers > 3 ) {
                        $count_sec = date( 'Y-m-d H:i:s', $crntTimestamp - 45 );
                    } else {
                        $count_sec = date( "Y-m-d H:i:s", $crntTimestamp + 35 ); // wait for 25 sec(20+5) for first Notification
                    }
                }
                $edit_tab_display = "";
                $cancel           = "";
                if ( $listings['travel_status'] == 0 ) {
                    $status_color  = "#63c9fb";
                    $tr_icon_class = "assign_icon";
                    $tr_icon_title = __( 'assign' );
                    $travel_status = '<div style="color:red;">' . __( 'assign' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs update_dispatch" name="update_dispatch" id="update_dispatch_' . $pass_logid . '_' . $taxicompany_id . '_' . date( 'Y-m-d', strtotime( $pickup_time ) ) . '" value="' . __( 'dispatch' ) . '" >' . __( 'dispatch' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                } elseif ( $listings['travel_status'] == 1 ) {
                    $status_color  = "#bad356";
                    $tr_icon_class = "complete_icon";
                    $tr_icon_title = __( 'completed' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'completed' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs completed" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'completedd' ) . '</button>';
                } elseif ( $listings['travel_status'] == 2 ) {
                    $status_color     = "#efbb69";
                    $tr_icon_class    = "inprogress_icon";
                    $tr_icon_title    = __( 'inprogress' );
                    $travel_status    = '<div style="color:' . $status_color . ';">' . __( 'inprogress' ) . '</div>';
                    $status_button    = '<button type="submit" class="btn btn-outline btn-primary btn-xs inprogress" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'inprogress' ) . '</button>';
                    $edit_tab_display = "<script>$('.edit_booking_" . $pass_logid . "').hide();$('#add_booking_tab').html('Add Booking');</script>";
                } elseif ( $listings['travel_status'] == 3 ) {
                    $status_color  = "#f46dd0";
                    $tr_icon_class = "start_to_pickup_icon";
                    $tr_icon_title = __( 'start_to_pickup' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'start_to_pickup' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs inprogress" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'start_to_pickup' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                } elseif ( $listings['travel_status'] == 4 ) {
                    $status_color  = "#ea6e64";
                    $tr_icon_class = "cancel_icon";
                    $tr_icon_title = __( 'cancel_by_passenger' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'cancel_by_passenger' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs cancelled" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'cancelledd' ) . '</button>';
                } elseif ( $listings['travel_status'] == 5 ) {
                    $status_color  = "#78b2ca";
                    $tr_icon_class = "waiting_payment_icon";
                    $tr_icon_title = __( 'waiting_payment' );
                    $travel_status = '<div style="color:' . $status_color . '">' . __( 'waiting_payment' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs cancelled" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'waiting_payment' ) . '</button>';
                } elseif ( $listings['travel_status'] == 6 ) {
                    $status_color  = "#66dde3";
                    $tr_icon_class = "reassign_icon";
                    $tr_icon_title = __( 'reassign' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'reassign' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs update_dispatch" name="update_dispatch" id="update_dispatch_' . $pass_logid . '_' . $taxicompany_id . '_' . date( 'Y-m-d', strtotime( $pickup_time ) ) . '" value="' . __( 'dispatch' ) . '" >' . __( 'dispatch' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                } elseif ( $listings['travel_status'] == 7 && $listings['dispatch_time'] >= $count_sec ) {
                    $driver_not_updated = $this->taxi_dispatch_model->check_driver_not_updated( $driver_id );
                    $time_difference    = $crntTimestamp - strtotime( $driver_not_updated );
                    if ( $time_difference > 25 ) {
                        $get_request_dets = $this->taxi_dispatch_model->check_new_request_tripid( "", "", $pass_logid, $driver_id, $current_time, "" );
                    }
                    $status_color  = "#eb13f9";
                    $name_color    = "#eb13f9";
                    $tr_icon_class = "waiting_response_icon";
                    $tr_icon_title = __( 'dispatched' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'dispatched' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs waiting_response" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatched' ) . '" >' . __( 'dispatched' ) . '</button>';
                } elseif ( $listings['travel_status'] == 7 && ( $listings['driver_reply'] == 'C' || $listings['driver_reply'] == 'R' ) ) {
                    $status_color  = "#ea6e64";
                    $tr_icon_class = "cancel_icon";
                    $tr_icon_title = __( 'cancelled' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'cancelled' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs cancelled" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'cancelledd' ) . '</button>';
                } elseif ( $listings['travel_status'] == 7 ) {
                    $status_color  = "#1339f9";
                    $tr_icon_class = "reassign_icon";
                    $tr_icon_title = __( 'reassign' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'reassign' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs update_dispatch" name="update_dispatch" id="update_dispatch_' . $pass_logid . '_' . $taxicompany_id . '_' . date( 'Y-m-d', strtotime( $pickup_time ) ) . '" value="' . __( 'dispatch' ) . '" >' . __( 'dispatch' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                } elseif ( $listings['travel_status'] == 8 ) {
                    $status_color  = "#ea6e64";
                    $tr_icon_class = "cancel_icon";
                    $tr_icon_title = __( 'cancelled' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'cancelled' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs cancelled" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'cancelledd' ) . '</button>';
                } elseif ( $listings['travel_status'] == 9 && $listings['driver_reply'] == 'A' ) {
                    $status_color  = "#ad72fc";
                    $tr_icon_class = "confirm_icon";
                    $tr_icon_title = __( 'confirmed' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'confirmed' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs inprogress" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'confirmed' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                } elseif ( $listings['travel_status'] == 9 && ( $listings['driver_reply'] == 'C' || $listings['driver_reply'] == 'R' ) ) {
                    $status_color  = "#ea6e64";
                    $tr_icon_class = "cancel_icon";
                    $tr_icon_title = __( 'cancelled' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'cancelled' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs cancelled" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'cancelledd' ) . '</button>';
                    $edit          = '<button type="submit" class="btn btn-outline btn-primary btn-xs update_dispatch" name="update_dispatch" id="update_dispatch_' . $pass_logid . '_' . $_SESSION['company_id'] .'_'.date( 'Y-m-d H:i:s', strtotime( $pickup_time ) ). '" value="' . __( 'dispatch' ) . '" >' . __( 'reassign' ) . '</button>';
                } elseif ( $listings['travel_status'] == 10 ) {
                    $status_color  = "#1339f9";
                    $tr_icon_class = "reassign_icon";
                    $tr_icon_title = __( 'reassign' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'reassign' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs update_dispatch" name="update_dispatch" id="update_dispatch_' . $pass_logid . '_' . $taxicompany_id . '_' . date( 'Y-m-d', strtotime( $pickup_time ) ) . '" value="' . __( 'dispatch' ) . '" >' . __( 'dispatch' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                }
                if ( $driver_name != "" ) {
                    $driver_name  = $driver_name;
                    $driver_phone = "-(" . $listings['driver_phone'] . ")";
                } else {
                    $driver_name  = "<span style='color:red;'>No Driver</span>";
                    $driver_phone = "";
                }
                $notes_img = '-';
                if ( !empty( $listings['notes'] ) ) {
                    $notes_img = '<a href="javascript:void(0)" style="color:#000;text-decoration:none;cursor:default;" title="' . $listings['notes'] . '"><img src="' . URL_BASE . 'public/common/images/notes.jpg"></a>';
                }
                $op[] .= '<div class="tbl_row ' . $trcolor_class . ' ' . $tr_icon_class . '" id="addtr_' . $pass_logid . '" style="border-left:5px solid ' . $status_color . ';">';
                $op[] .= '<div class="tbl_book_time"><span>' . date( 'Y-m-d H:i:s', strtotime( $createdate ) ) . '</span></div>';
                $op[] .= '<div class="tbl_pickup_time"><span>' . date( 'Y-m-d H:i:s', strtotime( $pickup_time ) ) . '</span></div>';
                $op[] .= '<div class="tbl_act_pickup_time"><span>' . $act_pickuptime . '</span></div>';
                $op[] .= '<div class="tbl_book_type"><span>' . $bookType . '</span></div>';
                if ( $listings['travel_status'] == 1 ) {
                    $op[] .= '<div class="tbl_trip_id" style="color:blue;text-decoration:none;"><a style="text-decoration:none;" href="' . URL_BASE . 'transaction/transaction_details/' . $pass_logid . '">' . $pass_logid . '</a></div>';
                } else {
                    $op[] .= '<div class="tbl_trip_id" style="color:blue;"><span>' . $pass_logid . '</span></div>';
                }
                $op[] .= '<div class="tbl_passenger"><span><a target="_blank" href="'.$passengerInfo.'">' . ucfirst( $passenger_name ) . '</a><br>' . $passenger_phone . '</span></div>';
                if ( $_SESSION['user_type'] == "A" ) {
                    $op[] .= '<div class="tbl_company_name"><span>' . ucfirst( $company_name ) . '</span></div>';
                }
                $op[] .= '<div class="tbl_driver"><span><a target="_blank" href="'.$driverInfo.'">' . ucfirst( $driver_name ) . '</a>' . $driver_phone . '</span></div>';
                $op[] .= '<div class="tbl_vehicle"><span>' . ucfirst( $model_name ) . '</span></div>';
                $op[] .= '<div class="tbl_cur_Loc"><span>' . $current_location . '</span></div>';
                $op[] .= '<div class="tbl_drop_Loc"><span>' . $drop_location . '</span></div>';
                $op[] .= '<div class="tbl_distance"><span>' . $approx_distance . '</span></div>';
                $op[] .= '<div class="tbl_fare"><span>' . $fixedprice . '</span></div>';
                $op[] .= '<div class="tbl_status"><a href="javascript:;" style="cursor:default;" title="' . $tr_icon_title . '">' . $travel_status . '</a></div>';
                $op[] .= '<div class="tbl_action">' . $edit . $cancel . '<span>' . $status_button . '</span></div>';
                $op[] .= $edit_tab_display;
                $op[] .= '</div>';
                $status_button = "";
                $edit          = "";
            }
            if ( $op != NULL ) {
                $output = implode( " ", $op );
            }
        } else {
            $output .= '<div class="nodata">';
            $output .= '<p>' . __( 'no_data' ) . '</p>';
            $output .= '</div>';
        }
        echo json_encode( $markers ) . "@" . $output_driver . "@" . $driver_count_dets . "@" . count( $get_all_booking_list ) . "@" . $output;
        exit;
    }
    public function action_all_booking_list_manage_all()
    {
        $output               = '';
        $current_time         = $this->company_all_currenttimestamp;
        $travel_status        = isset( $_GET['travel_status'] ) ? $_GET['travel_status'] : "";
        $driver_reply_cancel  = isset( $_GET['status_cancel'] ) ? $_GET['status_cancel'] : "";
        $manage_status        = isset( $_GET['manage_status'] ) ? $_GET['manage_status'] : 0;
        $search_txt           = isset( $_GET['search_txt'] ) ? $_GET['search_txt'] : "";
        $search_location      = isset( $_GET['search_location'] ) ? $_GET['search_location'] : "";
        $filter_date          = ( isset( $_GET['filter_date'] ) && $_GET['filter_date'] != "" ) ? Commonfunction::ensureDatabaseFormat( $_GET['filter_date'], 1 ) : "";
        $to_date              = ( isset( $_GET['to_date'] ) && $_GET['to_date'] != "" ) ? Commonfunction::ensureDatabaseFormat( $_GET['to_date'], 2 ) : "";
        $booking_filter       = isset( $_GET['booking_filter'] ) ? $_GET['booking_filter'] : "";
        $company_id           = isset( $_GET['company'] ) ? $_GET['company'] : "";
        $select_taxi_model    = isset( $_GET['select_taxi_model'] ) ? $_GET['select_taxi_model'] : "";
        $send_array           = array(
             "current_time" => $current_time,
            "travel_status" => $travel_status,
            "driver_reply_cancel" => $driver_reply_cancel,
            "manage_status" => $manage_status,
            "search_txt" => $search_txt,
            "search_location" => $search_location,
            "filter_date" => $filter_date,
            "to_date" => $to_date,
            "booking_filter" => $booking_filter,
            "company_id" => $company_id,
            "select_taxi_model" => $select_taxi_model 
        );
        $get_all_booking_list = $this->taxi_dispatch_model->get_all_booking_list_all( $send_array );
        $i                    = 0;
        $sno                  = 0;
        $status_button        = "";
        $edit                 = "";
        $name_color           = "";
        $op                   = array();
        $taxicompany_id       = 0;
        if ( count( $get_all_booking_list ) > 0 ) {
            foreach ( $get_all_booking_list as $listings ) {
                $trcolor  = 'oddtr';
                $bookType = $listings['bookingtype'];
                $bookBy   = $listings['bookby'];
                if ( $bookType == 2 && $bookBy == 2 ) {
                    $bookType = "Dispatcher";
                } else if ( $bookType == 2 && $bookBy == 1 ) {
                    $bookType = "Mobile";
                } else {
                    $bookType = "---";
                }
                $i++;
                if ( $i % 2 ) {
                    $trcolor_class = "show_tr_one";
                } else {
                    $trcolor_class = "show_tr_two";
                }
                $pass_logid       = $listings['pass_logid'];
                $pickup_time      = $listings['pickup_time'];
                $createdate       = $listings['createdate'];
                $act_pickuptime   = ( $listings['act_pickuptime'] != "-" ) ? date( 'Y-m-d H:i:s', strtotime( $listings['act_pickuptime'] ) ) : $listings['act_pickuptime'];
                $passenger_name   = $listings['passenger_name'];
                $passenger_phone  = $listings['passenger_phone'];
                $driver_name      = $listings['driver_name'];
                $driver_id        = $listings['driver_id'];
                $reachable_mobile = isset( $listings['driver_phone'] ) ? $listings['driver_phone'] : "";
                $passenger_phone  = ( isset( $listings['passenger_phone'] ) && $listings['passenger_phone'] != '' ) ? $listings['passenger_country_code'] . ' - ' . $listings['passenger_phone'] : "---";
                $company_name     = isset( $listings['company_name'] ) ? $listings['company_name'] : "---";
                $model_name       = isset( $listings['model_name'] ) ? $listings['model_name'] : "---";
                $taxicompany_id   = $listings['company_id'];
                if ( $reachable_mobile != "" ) {
                    $reachable_mobile = $reachable_mobile;
                } else {
                    $reachable_mobile = "---";
                }
                $fare     = $listings['fare'];
                $distance = $listings['distance'];
                if ( $fare != NULL && $distance != NULL ) {
                    $approx_distance = $distance;
                    $fixedprice      = $fare;
                } else {
                    $approx_distance = $listings['approx_distance'];
                    $fixedprice      = $listings['approx_fare'];
                }
                $approx_distance = ( UNIT_NAME == "MILES" ) ? ( $approx_distance * 0.621371 ) : $approx_distance;
                $approx_distance = round( $approx_distance, 2 );
                $pickup_lat      = $listings['pickup_latitude'];
                $pickup_lng      = $listings['pickup_longitude'];
                $drop_lat        = $listings['drop_latitude'];
                $drop_lng        = $listings['drop_longitude'];
                $no_passengers   = $listings['no_passengers'];
                if ( strlen( $listings['current_location'] ) > 25 ) {
                    $current_location = substr( ucfirst( $listings['current_location'] ), 0, 25 ) . "..";
                } else {
                    $current_location = $listings['current_location'];
                }
                if ( strlen( $listings['drop_location'] ) > 25 ) {
                    $drop_location = substr( ucfirst( $listings['drop_location'] ), 0, 25 ) . "..";
                } else {
                    $drop_location = $listings['drop_location'];
                }
                $dispatch_time = strtotime( $listings['dispatch_time'] );
                $crntTimestamp = strtotime( $current_time );
                $total_drivers = "";
                $count_sec     = date( "Y-m-d H:i:s", $crntTimestamp + 35 ); // wait for 25 sec
                if ( $total_drivers != NULL && $total_drivers != "" ) {
                    $drivers_count = count( explode( ',', $total_drivers ) );
                    if ( $drivers_count == 2 ) {
                        $count_sec = date( "Y-m-d H:i:s", $crntTimestamp + 15 ); // wait for 45 sec
                    } elseif ( $drivers_count == 3 ) {
                        $count_sec = date( 'Y-m-d H:i:s', $crntTimestamp - 10 ); // wait for 70 sec
                    } elseif ( $drivers_count > 3 ) {
                        $count_sec = date( 'Y-m-d H:i:s', $crntTimestamp - 45 ); // wait for 90 sec
                    } else {
                        $count_sec = date( "Y-m-d H:i:s", $crntTimestamp + 35 ); // wait for 25 sec(20+5) for first Notification
                    }
                }
                $edit_tab_display = "";
                $cancel           = "";
                if ( $listings['travel_status'] == 0 ) {
                    $status_color  = "#63c9fb";
                    $tr_icon_class = "assign_icon";
                    $tr_icon_title = __( 'assign' );
                    $travel_status = '<div style="color:red;">' . __( 'assign' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs update_dispatch" name="update_dispatch" id="update_dispatch_' . $pass_logid . '_' . $taxicompany_id . '_' . date( 'Y-m-d', strtotime( $pickup_time ) ) . '" value="' . __( 'dispatch' ) . '" >' . __( 'dispatch' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                } elseif ( $listings['travel_status'] == 1 ) {
                    $status_color  = "#bad356";
                    $tr_icon_class = "complete_icon";
                    $tr_icon_title = __( 'completed' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'completed' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs completed" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'completedd' ) . '</button>';
                } elseif ( $listings['travel_status'] == 2 ) {
                    $status_color     = "#efbb69";
                    $tr_icon_class    = "inprogress_icon";
                    $tr_icon_title    = __( 'inprogress' );
                    $travel_status    = '<div style="color:' . $status_color . ';">' . __( 'inprogress' ) . '</div>';
                    $status_button    = '<button type="submit" class="btn btn-outline btn-primary btn-xs inprogress" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'inprogress' ) . '</button>';
                    $edit_tab_display = "<script>$('.edit_booking" . $pass_logid . "').hide();$('#add_booking_tab').html('Add Booking');</script>";
                } elseif ( $listings['travel_status'] == 3 ) {
                    $status_color  = "#f46dd0";
                    $tr_icon_class = "start_to_pickup_icon";
                    $tr_icon_title = __( 'start_to_pickup' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'start_to_pickup' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs inprogress" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'start_to_pickup' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                } elseif ( $listings['travel_status'] == 4 ) {
                    $status_color  = "#ea6e64";
                    $tr_icon_class = "cancel_icon";
                    $tr_icon_title = __( 'cancel_by_passenger' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'cancel_by_passenger' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs cancelled" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'cancelledd' ) . '</button>';
                } elseif ( $listings['travel_status'] == 5 ) {
                    $status_color  = "#78b2ca";
                    $tr_icon_class = "waiting_payment_icon";
                    $tr_icon_title = __( 'waiting_payment' );
                    $travel_status = '<div style="color:' . $status_color . '">' . __( 'waiting_payment' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs cancelled" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'waiting_payment' ) . '</button>';
                } elseif ( $listings['travel_status'] == 6 ) {
                    $status_color  = "#66dde3";
                    $tr_icon_class = "reassign_icon";
                    $tr_icon_title = __( 'reassign' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'reassign' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs update_dispatch" name="update_dispatch" id="update_dispatch_' . $pass_logid . '_' . $taxicompany_id . '_' . date( 'Y-m-d', strtotime( $pickup_time ) ) . '" value="' . __( 'dispatch' ) . '" >' . __( 'dispatch' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                } elseif ( $listings['travel_status'] == 7 && $listings['dispatch_time'] >= $count_sec ) {
                    $status_color  = "#eb13f9";
                    $name_color    = "#eb13f9";
                    $tr_icon_class = "waiting_response_icon";
                    $tr_icon_title = __( 'dispatched' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'dispatched' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs waiting_response" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatched' ) . '" >' . __( 'dispatched' ) . '</button>';
                } elseif ( $listings['travel_status'] == 7 && ( $listings['driver_reply'] == 'C' || $listings['driver_reply'] == 'R' ) ) {
                    $status_color  = "#ea6e64";
                    $tr_icon_class = "cancel_icon";
                    $tr_icon_title = __( 'cancelled' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'cancelled' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs cancelled" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'cancelledd' ) . '</button>';
                } elseif ( $listings['travel_status'] == 7 ) {
                    $status_color  = "#1339f9";
                    $tr_icon_class = "reassign_icon";
                    $tr_icon_title = __( 'reassign' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'reassign' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs update_dispatch" name="update_dispatch" id="update_dispatch_' . $pass_logid . '_' . $taxicompany_id . '_' . date( 'Y-m-d', strtotime( $pickup_time ) ) . '" value="' . __( 'dispatch' ) . '" >' . __( 'dispatch' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                } elseif ( $listings['travel_status'] == 8 ) {
                    $status_color  = "#ea6e64";
                    $tr_icon_class = "cancel_icon";
                    $tr_icon_title = __( 'cancelled' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'cancelled' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs cancelled" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'cancelledd' ) . '</button>';
                } elseif ( $listings['travel_status'] == 9 && $listings['driver_reply'] == 'A' ) {
                    $status_color  = "#ad72fc";
                    $tr_icon_class = "confirm_icon";
                    $tr_icon_title = __( 'confirmed' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'confirmed' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs inprogress" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'confirmed' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                } elseif ( $listings['travel_status'] == 9 && ( $listings['driver_reply'] == 'C' || $listings['driver_reply'] == 'R' ) ) {
                    $status_color  = "#ea6e64";
                    $tr_icon_class = "cancel_icon";
                    $tr_icon_title = __( 'cancelled' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'cancelled' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs cancelled" name="update_dispatch" id="update_dispatch_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" >' . __( 'cancelledd' ) . '</button>';
                    $edit          = '<button type="submit" class="btn btn-outline btn-primary btn-xs update_dispatch" name="update_dispatch" id="update_dispatch_' . $pass_logid . '_' . $_SESSION['company_id'] . '" value="' . __( 'dispatch' ) . '" >' . __( 'reassign' ) . '</button>';
                } elseif ( $listings['travel_status'] == 10 ) {
                    $status_color  = "#1339f9";
                    $tr_icon_class = "reassign_icon";
                    $tr_icon_title = __( 'reassign' );
                    $travel_status = '<div style="color:' . $status_color . ';">' . __( 'reassign' ) . '</div>';
                    $status_button = '<button type="submit" class="btn btn-outline btn-primary btn-xs update_dispatch" name="update_dispatch" id="update_dispatch_' . $pass_logid . '_' . $taxicompany_id . '_' . date( 'Y-m-d', strtotime( $pickup_time ) ) . '" value="' . __( 'dispatch' ) . '" >' . __( 'dispatch' ) . '</button>';
                    $edit          = '<a href="javascript:;" class="edit-ico status ' . $trcolor . '" name="edit" id="addtr_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-edit">&nbsp;</i></a>';
                    $cancel        = '<a href="javascript:;" class="remove-ico status cancelBtn" name="cancel" id="cancel_' . $pass_logid . '" value="' . __( 'dispatch' ) . '" ><i class="glyphicon glyphicon-remove">&nbsp;</i></a>';
                }
                if ( $driver_name != "" ) {
                    $driver_name  = $driver_name;
                    $driver_phone = "-(" . $listings['driver_phone'] . ")";
                } else {
                    $driver_name  = "<span style='color:red;'>No Driver</span>";
                    $driver_phone = "";
                }
                $notes_img = '-';
                if ( !empty( $listings['notes'] ) ) {
                    $notes_img = '<a href="javascript:void(0)" style="color:#000;text-decoration:none;cursor:default;" title="' . $listings['notes'] . '"><img src="' . URL_BASE . 'public/common/images/notes.jpg"></a>';
                }
                $op[] .= '<div class="tbl_row ' . $trcolor_class . ' ' . $tr_icon_class . '" id="addtr_' . $pass_logid . '" style="border-left:5px solid ' . $status_color . ';">';
                $op[] .= '<div class="tbl_book_time"><span>' . date( 'Y-m-d H:i:s', strtotime( $createdate ) ) . '</span></div>';
                $op[] .= '<div class="tbl_pickup_time"><span>' . date( 'Y-m-d H:i:s', strtotime( $pickup_time ) ) . '</span></div>';
                $op[] .= '<div class="tbl_act_pickup_time"><span>' . $act_pickuptime . '</span></div>';
                $op[] .= '<div class="tbl_book_type"><span>' . $bookType . '</span></div>';
                if ( $listings['travel_status'] == 1 ) {
                    $op[] .= '<div class="tbl_trip_id"  style="color:blue;text-decoration:none;"><a style="text-decoration:none;" href="' . URL_BASE . 'transaction/transaction_details/' . $pass_logid . '">' . $pass_logid . '</a></div>';
                } else {
                    $op[] .= '<div class="tbl_trip_id" style="color:blue;"><span>' . $pass_logid . '</span></div>';
                }
                $op[] .= '<div class="tbl_passenger"><span>' . ucfirst( $passenger_name ) . '<br>' . $passenger_phone . '</span></div>';
                if ( $_SESSION['user_type'] == "A" ) {
                    $op[] .= '<div class="tbl_company_name"><span>' . ucfirst( $company_name ) . '</span></div>';
                }
                $op[] .= '<div class="tbl_driver"><span>' . ucfirst( $driver_name ) . '' . $driver_phone . '</span></div>';
                $op[] .= '<div class="tbl_vehicle"><span>' . $model_name . '</span></div>';
                $op[] .= '<div class="tbl_cur_Loc"><span>' . $current_location . '</span></div>';
                $op[] .= '<div class="tbl_drop_Loc"><span>' . $drop_location . '</span></div>';
                $op[] .= '<div class="tbl_distance"><span>' . $approx_distance . '</span></div>';
                $op[] .= '<div class="tbl_fare"><span>' . $fixedprice . '</span></div>';
                $op[] .= '<div class="tbl_status" title="' . $tr_icon_title . '"><span>' . $travel_status . '</span></div>';
                $op[] .= '<div class="tbl_action">' . $edit . $cancel . '<span>' . $status_button . '</span></div>';
                $op[] .= $edit_tab_display;
                $op[] .= '</div>';
                $status_button = "";
                $edit          = "";
            }
            if ( $op != NULL ) {
                $output = implode( " ", $op );
            }
        } else {
            $output = '<div class="nodata"><p>' . __( 'no_data' ) . '</p></div>';
        }
        echo $output;
        exit;
    }
    public function action_view_all_driverss()
    {
        $taxi_model = $_REQUEST['taxi_model'];
        if ( $taxi_model != "" ) {
            $all_company_map_list = $this->taxi_dispatch_model->all_driver_map_list_model( $_REQUEST );
        } else {
            $all_company_map_list = $this->taxi_dispatch_model->all_driver_map_list( $_REQUEST );
        }
        $markers  = array();
        $tmarkers = array();
        foreach ( $all_company_map_list as $key => $val ) {
            $book_now = "";
            if ( $val['driver_status'] == "F" && $val['shift_status'] == "IN" ) {
                $driver_info = '<span style="color:green">' . __( 'free_in' ) . '</span>';
                $book_now    = '<button type="button" class="btn btn-outline btn-primary btn-xs" name="bookingnow" onclick="bookingnow_click(this.id);" id="driverid_' . $val['driver_id'] . '" >' . __( 'booknow' ) . '</button>';
            } elseif ( $val['driver_status'] == "F" && $val['shift_status'] == "OUT" ) {
                $driver_info = '<span style="color:blue">' . __( 'free_out' ) . '</span>';
            } elseif ( $val['driver_status'] == "B" ) {
                $driver_info = '<span style="color:red">' . __( 'trip_assigned' ) . '</span>';
            } elseif ( $val['driver_status'] == "A" ) {
                $driver_info = '<span style="color:orange">' . __( 'hired' ) . '</span>';
            }
            $update_date = $val['update_date'];
            $drv_info    = '<div class="info_drivercontent">';
            $drv_info .= '<span class="info-content">' . ucfirst( $val['name'] ) . '</span>';
            $drv_info .= '</br>';
            $drv_info .= '<span class="info-content">' . $driver_info . '</span>';
            $drv_info .= '</br>';
            $drv_info .= '<span class="info-content">' . $update_date . '</span>';
            if ( $book_now != "" ) {
                $drv_info .= '</br>';
            }
            $drv_info .= '</div>';
            $markers[$key]['info']         = $drv_info;
            $markers[$key]['lat']          = $val['latitude'];
            $markers[$key]['lng']          = $val['longitude'];
            $markers[$key]['status']       = $val['driver_status'];
            $markers[$key]['shift_status'] = $val['shift_status'];
        }
        echo json_encode( $markers );
        exit;
    }
    public function action_search_driver_location()
    {
        if ( isset( $_REQUEST["pass_logid"] ) ) {
            $admin_companyid = isset( $_REQUEST["admin_companyid"] ) ? $_REQUEST["admin_companyid"] : 0;
            $tdispatch_model = Model::factory( 'taxidispatch' );
            $booking_details = $this->taxi_dispatch_model->get_bookingdetails( $_REQUEST['pass_logid'], $admin_companyid );
            $latitude        = $booking_details[0]["pickup_latitude"];
            $longitude       = $booking_details[0]["pickup_longitude"];
            $miles           = '';
            $no_passengers   = $booking_details[0]["no_passengers"];
            $taxi_fare_km    = $booking_details[0]["min_fare"];
            $taxi_model      = $booking_details[0]["taxi_modelid"];
            $taxi_type       = '';
            $maximum_luggage = $booking_details[0]["luggage"];
            $pass_logid      = $_REQUEST["pass_logid"];
            if ( isset( $_SESSION['search_city'] ) ) {
                $cityname = $_SESSION['search_city'];
            } else {
                $cityname = '';
            }
            $search_driver  = $_REQUEST['search_driver'];
            $driver_details = $this->taxi_dispatch_model->search_driver_location( $latitude, $longitude, $miles, $no_passengers, $_REQUEST, $taxi_fare_km, $taxi_model, $taxi_type, $maximum_luggage, $cityname, $pass_logid, $admin_companyid, $search_driver );
            $count          = count( $driver_details );
            $output         = '';
            $sno            = 0;
            if ( $count > 0 ) {
                foreach ( $driver_details as $listings ) {
                    $sno++;
                    $miles_to_km     = round( ( $listings['distance_miles'] * 1.609344 ), 2 );
                    $distance_miles  = ( ceil( $miles_to_km * 100 ) / 100 );
                    $color           = ( $sno % 2 == 0 ) ? 'whitecp' : 'colorcp';
                    $driver_has_trip = $this->taxi_dispatch_model->check_driver_has_trip_request( $listings['driver_id'] );
                    $current_request = $this->taxi_dispatch_model->currently_driver_has_trip_request( $listings['driver_id'] );
                    if ( $driver_has_trip == 0 && $current_request == 0 ) {
                        $output .= '<p class=' . $color . ' id="' . $listings['driver_id'] . '_' . $listings['taxi_id'] . '_' . round( $distance_miles, 2 ) . '_' . $listings['company_id'] . '"><a  href="javascript:;">' . $listings['name'] . ' (' . __( 'miles' ) . ' ' . $distance_miles . ')</a></p>';
                    }
                }
            } else {
                $output .= __( 'no_driver_available' );
            }
?>
                   <script>    
                    $('#driver_details p').click(function() {
                            var detailsid = this.id;
                            var findimg = detailsid.split('_');

                            var pass_logid = $('#passenger_log_id').val();    
                            
                            var dataS = "pass_logid="+pass_logid+"&driver_id="+findimg[0]+"&taxi_id="+findimg[1]+"&driver_away_in_km="+findimg[2]+"&company_id="+findimg[3];    
                            
                            $("#show_process").html('<img src="<?php
            echo IMGPATH;
?>loader.gif">');
                            $.ajax
                            ({             
                                type: "GET",
                                url: "<?php
            echo URL_BASE;
?>taxidispatch/updatebooking", 
                                data: dataS, 
                                cache: false, 
                                dataType: 'html',
                                success: function(response) 
                                {         
                                    $("#show_process").html('');
                                    //console.log(response);
                                    //document.location.href="<?php
            echo URL_BASE;
?>tdispatch/managebooking/#stuff";
                                     window.location="<?php
            echo URL_BASE;
?>taxidispatch/dashboard";
                                } 
                                 
                            });    
                        });    
                    </script>
                    
                <?php
            echo ( !empty( $output ) ) ? $output : __( 'no_driver_available' );
            exit;
        }
    }
    public function action_updatebooking()
    {
        $user_createdby               = $_SESSION['userid'];
        $company_id                   = $_SESSION['company_id'];
        $tdispatch_model              = Model::factory( 'taxidispatch' );
        $api                          = Model::factory( 'api' );
        $commonmodel                  = Model::factory( 'commonmodel' );
        $company_all_currenttimestamp = $commonmodel->getcompany_all_currenttimestamp( $company_id );
        if ( isset( $_REQUEST['driver_id'] ) ) {
            $update_booking = $tdispatch_model->updatebooking_logid( $_REQUEST );
        } else {
            Message::error( __( 'no_driver_available' ) );
            $this->request->redirect( "tdispatch/managebooking" );
        }
        $passenger_logid   = $_REQUEST['pass_logid'];
        $passenger_details = $tdispatch_model->get_bookingdetails( $_REQUEST['pass_logid'], $company_id );
        $driver_details    = $tdispatch_model->get_driver_profile_details( $_REQUEST['driver_id'] );
        /* Create Log */
        $company_id        = $_SESSION['company_id'];
        $log_message       = __( 'log_message_dispatched' );
        $log_message       = str_replace( "PASS_LOG_ID", $passenger_logid, $log_message );
        $log_booking       = __( 'log_booking_dispatched' );
        $log_booking       = str_replace( "DRIVERNAME", $driver_details[0]['name'], $log_booking );
        $log_status        = $this->taxi_dispatch_model->create_logs( $passenger_logid, $company_id, $user_createdby, $log_message, $log_booking );
?>
       <script type="text/javascript">load_logcontent();</script>
        <?php
        /* Create Log */
        if ( $update_booking ) {
            /** Dispatch algorithm **/
            $company_dispatch  = $this->taxi_dispatch_model->company_dispatch( $company_id );
            $tdispatch_type    = '';
            $hide_customer     = '';
            $hide_droplocation = '';
            if ( count( $company_dispatch ) > 0 ) {
                $tdispatch_type    = $company_dispatch[0]['labelname'];
                $hide_customer     = $company_dispatch[0]['hide_customer'];
                $hide_droplocation = $company_dispatch[0]['hide_droplocation'];
            }
            /** Dispatch algorithm **/
            $cityname          = "";
            $driver_away_in_km = $_REQUEST['driver_away_in_km'];
            $exist_request     = $this->taxi_dispatch_model->exist_request( $passenger_logid );
            if ( $exist_request == 1 ) {
                $delete_exist_request = $this->taxi_dispatch_model->remove_request_details( $passenger_logid );
            }
            /***** Insert the druiver details to driver request table ************/
            $insert_array = array(
                 "trip_id" => $passenger_logid,
                "available_drivers" => $_REQUEST['driver_id'],
                "total_drivers" => $_REQUEST['driver_id'],
                "selected_driver" => $_REQUEST['driver_id'],
                "status" => 0,
                "rejected_timeout_drivers" => "",
                "createdate" => $company_all_currenttimestamp 
            );
            //Inserting to Transaction Table 
            $transaction  = $this->taxi_dispatch_model->insert_request_details( $insert_array );
            $detail       = array(
                 "passenger_tripid" => $passenger_logid,
                "notification_time" => "" 
            );
            $msg          = array(
                 "message" => __( 'api_request_confirmed_passenger' ),
                "status" => 1,
                "detail" => $detail 
            );
            exit;
        }
    }
    public function action_driver_sequence_list()
    {
        $trip_id                  = $_REQUEST['trip_id'];
        $get_driver_sequence_list = $this->taxi_dispatch_model->get_driver_sequence_list( $trip_id );
        $markers                  = array();
        if ( count( $get_driver_sequence_list ) > 0 ) {
            foreach ( $get_driver_sequence_list as $key => $val ) {
                $markers[$key]['trip_id']       = $val['trip_id'];
                $markers[$key]['total_drivers'] = $this->taxi_dispatch_model->get_selected_driver_sequence_list( $val['total_drivers'], $val['trip_id'] );
            }
        }
        echo json_encode( $markers );
        exit;
    }
    public function action_manage_bookinga()
    {
        $view                       = View::factory( TAXI_DISPATCH . 'manage_booking' );
        $this->template->title      = SITENAME . " | " . __( 'manage_booking' );
        $this->template->page_title = __( 'manage_booking' );
        $this->template->content    = $view;
    }
    public function action_manage_booking()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'A' && $usertype == 'S' ) {
            $this->request->redirect( "admin/login" );
        }
        $add_model                  = Model::factory( 'add' );
        $tdispatch_model            = Model::factory( 'taxidispatch' );
        $common_model               = Model::factory( 'commonmodel' );
        $company_id                 = $_SESSION['company_id'];
        $company_tax                = $common_model->company_tax( $company_id );
        $company_timezone           = $common_model->company_timezone( $company_id );
        /**To get the form submit button name**/
        $create_submit              = arr::get( $_REQUEST, 'create' );
        $dispatch_submit            = arr::get( $_REQUEST, 'dispatch' );
        $model_details              = $tdispatch_model->model_details();
        $errors                     = array();
        $post_values                = array();
        $view                       = View::factory( TAXI_DISPATCH . 'manage_booking' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'company_tax', $company_tax )->bind( 'company_timezone', $company_timezone )->bind( 'model_details', $model_details )->bind( 'companyId', $this->company_id )->bind( 'postvalue', $post_values );
        $this->template->title      = SITENAME . " | " . __( 'manage_booking' );
        $this->template->page_title = __( 'manage_booking' );
        $this->template->content    = $view;
    }
    public function action_cancel_booking()
    {
        $passenger_logid = $_REQUEST['pass_logid'];
        $user_createdby  = $_SESSION['userid'];
        $company_id      = $_SESSION['company_id'];
        $tdispatch_model = Model::factory( 'taxidispatch' );
        $update_booking  = $this->taxi_dispatch_model->cancelbooking_logid( $_REQUEST );
        /* Create Log */
        $log_message     = __( 'log_message_cancelled' );
        $log_message     = str_replace( "PASS_LOG_ID", $passenger_logid, $log_message );
        $log_booking     = __( 'log_booking_cancelled' );
        $log_booking     = str_replace( "PASS_LOG_ID", $passenger_logid, $log_booking );
        $log_status      = $this->taxi_dispatch_model->create_logs( $passenger_logid, $company_id, $user_createdby, $log_message, $log_booking );
?>
       <script type="text/javascript">load_logcontent();</script>
        <?php
        if ( $update_booking == '1' ) {
            Message::success( __( 'successfully_cancel_booking' ) );
            $this->request->redirect( "taxidispatch/dashboard" );
        } else {
            Message::error( __( 'Trip in Progress' ) );
            $this->request->redirect( "taxidispatch/dashboard" );
        }
    }
    //Getting the latitude and Longitude    
    public function getLatLong( $address )
    {
        $address = str_replace( ' ', '+', $address );
        $url     = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key=' . GOOGLE_GEO_API_KEY;
        $ch      = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $geoloc = curl_exec( $ch );
        $json   = json_decode( $geoloc );
        if ( isset( $json ) && $json->status == 'OK' ) {
            return array(
                 $json->results[0]->geometry->location->lat,
                $json->results[0]->geometry->location->lng 
            );
        } else {
            return array(
                 11.621354,
                76.14253698 
            );
        }
    }
    //function to get tax value for a particular company/admin
    public function action_gettaxval()
    {
        $common_model = Model::factory( 'commonmodel' );
        echo $company_tax = ( FARE_SETTINGS == 2 && !empty( $_REQUEST['company'] ) ) ? $common_model->company_tax( $_REQUEST['company'] ) : TAX;
        exit;
    }
    //function to get taxi dispatch settings
    public function action_checkdispatchsettings()
    {
        $dispatchMdl = Model::factory( 'taxidispatch' );
        if ( isset( $this->company_id ) ) {
            $dispatch_settings = $dispatchMdl->dispatch_settings( $this->company_id );
            echo $dispatch_settings;
        }
        exit;
    }
    /**
     * Check passenger trip status
     * return int
     **/
    public function action_checkPassengerStatus()
    {
        $result      = 0;
        $dispatchMdl = Model::factory( 'taxidispatch' );
        if ( $_POST ) {
            if ( isset( $this->company_id ) ) {
                $result = $dispatchMdl->checkPassengerStatus( $_POST["trip_id"], $_POST["company_id"] );
            }
        }
        echo $result;
        exit;
    }
}
?>
