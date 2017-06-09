<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
* Contains Finding By IP details
* @Package: Taximobility
* @Author: Taxi Team
* @URL : taximobility.com
********************************************/
class Controller_TaximobilityFind extends Controller_Website
{
    /**
     ****__construct()****
     */
    public function __construct( Request $request, Response $response )
    {
        parent::__construct( $request, $response );
        $this->taxidispatchMdl = Model::factory( 'taxidispatch' );
        $this->domain_name     = ( COMPANY_CID != 0 ) ? SUBDOMAIN : 'site';
    }
    public function action_index()
    {
        $view                    = View::factory( USERVIEW . 'find' );
        $this->template->content = $view;
        $this->template->title   = $this->title;
    }  
    public function action_get_motor_model()
    {
        if ( isset( $_POST['motor_type'] ) ) {
            $common_model = Model::factory( 'Commonmodel' );
            $motor_model  = $common_model->get_motor_model( $_POST['motor_type'] );
            $html         = "";
            $html .= '<label>' . __( "taxi_model" ) . '</label>
                    <select name="taxi_model" id="taxi_model">
                        <option value="">' . __( "select_label" ) . '</option>';
            foreach ( $motor_model as $list ) {
                $html .= '<option value="' . $list["model_id"] . '" >' . $list["model_name"] . '</option>';
            }
            $html .= '</select>';
            echo $html;
            exit;
        }
    }
    /** function for web booking **/
    public function action_webBooking()
    {
        /**To check Whether the user is logged in or not**/
        if ( !isset( $this->session ) || !$this->session->get( 'id' ) ) {
            Message::error( __( 'login_access' ) );
            $this->request->redirect( "/users/login/" );
        }
        $motorid                 = 1;
        /**function to get the selected model fare details**/
        $getModelDetails         = $this->findMdl->getmodel_details( $motorid );
        $passengerId             = $this->session->get( 'id' );
        $passengerName           = $this->session->get( 'passenger_name' );
        $passengerEmail          = $this->session->get( 'passenger_email' );
        $passengerPhone          = $this->session->get( 'passenger_phone' );
        $passengerPhCode         = $this->session->get( 'passenger_phone_code' );
        $val                     = array();
        $val['driver_status']    = 'F';
        $driverList              = $this->taxidispatchMdl->driver_status_details( $val );
        $this->template->content = View::factory( USERVIEW . 'web_booking' )->bind( 'getmodel_details', $getModelDetails )->bind( 'driverList', $driverList )->bind( 'passengerId', $passengerId )->bind( 'passengerName', $passengerName )->bind( 'passengerEmail', $passengerEmail )->bind( 'passengerPhone', $passengerPhone )->bind( 'passengerPhCode', $passengerPhCode );
    }
    /** Function to get model fare details **/
    public function action_getModelFareDets()
    {
        if ( isset( $_POST['modelID'] ) ) {
            $modelFares     = $this->findMdl->modelDets( $_POST['modelID'] );
            $markers        = array();
            $pickupTimezone = $this->findMdl->getpickupTimezone( $_POST['passlat'], $_POST['passlong'] );
            $currentTime    = convert_timezone( 'now', $pickupTimezone );
            $driverList     = $this->findMdl->getFreeDriverList( $_POST['modelID'], $_POST['passlat'], $_POST['passlong'], $currentTime );
            $a              = 0;
            if ( count( $driverList ) > 0 ) {
                foreach ( $driverList as $v ) {
                    for ( $b = 0; $b < 4; $b++ ) {
                        if ( $b == 0 ) {
                            $markers[$a][$b] = $v['latitude'];
                        }
                        if ( $b == 1 ) {
                            $markers[$a][$b] = $v['longitude'];
                        }
                        if ( $b == 2 ) {
                            $markers[$a][$b] = '<div class="info_content"><b>' . __( 'driver_name' ) . '</b> : ' . $v['driver_name'] . '</div>';
                        }
                        if ( $b == 3 ) {
                            $markers[$a][$b] = PUBLIC_IMGPATH . '/taxi_location.png';
                        }
                    }
                    $a++;
                }
            }
            $modelFares['driver_list'][] = $markers;
            $modelFares['drivers_count'] = count( $driverList );
            echo json_encode( $modelFares );
        }
        exit;
    }
    /** Booking save function **/
    public function action_bookingSubmit()
    {
        $post     = Arr::map( 'trim', $this->request->post() );
        $response = array();
        if ( $post ) {
            $pickupTimezone      = $this->findMdl->getpickupTimezone( $post['pass_latitude'], $post['pass_longitude'] );
            $currentTime         = convert_timezone( 'now', $pickupTimezone );
            $post['currentTime'] = $currentTime;
            if ( !empty( $post['pickup_time'] ) ) {
                $post['pickup_time'] = date( "Y-m-d H:i:s", strtotime( $post['pickup_time'] ) );
            }
            $validator = $this->findMdl->validate_bookingForm( $post );
            if ( $validator->check() ) {
                $this->commonmodel = Model::factory( 'commonmodel' );
                $promoValid        = ( isset( $post['promocode'] ) && $post['promocode'] != '' ) ? $this->findMdl->checkPromocode( $post['promocode'], $post['passengerId'], $currentTime ) : 1;
                if ( $promoValid == 1 ) {
                    $passengerInTrip = $this->findMdl->check_passenger_in_trip( $post['passengerId'], $currentTime );
                    if ( $passengerInTrip > 0 ) {
                        Message::error( __( 'passenger_in_journey' ) );
                        $response['redirect'] = URL_BASE . "booking.html";
                    } else {
                        list( $result, $driverCnt, $tripId ) = $this->findMdl->saveBooking( $post );
                        if ( $result != 1 ) {
                            if ( $result == 2 ) {
                                Message::success( __( 'api_request_disapatcher' ) );
                            } else if ( $result == 3 ) {
                                Message::error( __( 'no_drivers_found' ) );
                            }
                            $response['redirect'] = URL_BASE . "booking.html";
                        } else {
                            //1000 is multiplied to change the seconds to ms(millisecond)
                            if ( $driverCnt < 4 ) {
                                $timeout = ( ( ADMIN_NOTIFICATION_TIME * $driverCnt ) + 25 ) * 1000;
                            } else {
                                $timeout = CONTINOUS_REQUEST_TIME;
                            }
                            $notification_time = ADMIN_NOTIFICATION_TIME;
                            if ( $notification_time != 0 ) {
                                $timeoutseconds = $notification_time;
                            } else {
                                $timeoutseconds = 15;
                            }
                            $microseconds = $timeout * 1000000; //Seconds to microseconds 1 second = 1000000 
                            $now          = time();
                            $i            = 0;
                            if ( !empty( $tripId ) ) {
                                while ( ( time() - $now ) < $timeout ) {
                                    $driver_status       = $this->findMdl->get_request_status( $tripId );
                                    $driver_status_count = count( $driver_status );
                                    if ( $driver_status_count > 0 ) {
                                        $req_count                = $driver_status_count * $timeoutseconds;
                                        $driver_reply             = $driver_status[0]['status'];
                                        $trip_type                = $driver_status[0]['trip_type']; //get booking type 1-Favourite booking, 0-Normal Booking
                                        $selected_driver_id       = $driver_status[0]['selected_driver'];
                                        $available_drivers        = explode( ',', $driver_status[0]['total_drivers'] );
                                        $rejected_timeout_drivers = explode( ',', $driver_status[0]['rejected_timeout_drivers'] );
                                        $comp_result              = array_diff( $available_drivers, $rejected_timeout_drivers );
                                        $timeout                  = count( $available_drivers ) * 25 + 20;
                                        if ( $timeout < CONTINOUS_REQUEST_TIME ) {
                                            $timeout = CONTINOUS_REQUEST_TIME;
                                        }
                                        $microseconds = $timeout * 1000000;
                                        //to get drivers company timestamp
                                        $company_det  = $this->findMdl->get_company_id( $selected_driver_id );
                                        if ( count( $company_det ) > 0 ) {
                                            $company_all_currenttimestamp = $this->commonmodel->getcompany_all_currenttimestamp( $company_det[0]['company_id'] );
                                        }
                                        //condition to check driver not updated for above 30seconds if it is means we should change the request to next driver
                                        $driver_not_updated = $this->taxidispatchMdl->check_driver_not_updated( $selected_driver_id, $company_all_currenttimestamp );
                                        $time_difference    = strtotime( $company_all_currenttimestamp ) - strtotime( $driver_not_updated );
                                        if ( $time_difference > 25 && count( $comp_result ) != 0 && $driver_reply != '4' ) {
                                            $get_request_dets = $this->taxidispatchMdl->check_new_request_tripid( "", "", $tripId, $selected_driver_id, $company_all_currenttimestamp, "" );
                                        }
                                        if ( count( $comp_result ) == 0 ) {
                                            $driver_reply = 5;
                                        }
                                        if ( !empty( $driver_reply ) ) {
                                            if ( $driver_reply == '3' ) {
                                                Message::success( __( 'request_confirmed_passenger' ) );
                                                $response['redirect'] = URL_BASE . "booking.html";
                                                break;
                                            } elseif ( $driver_reply == '4' ) {
                                                Message::success( __( 'driver_busy' ) );
                                                $response['redirect'] = URL_BASE . "booking.html";
                                                break;
                                            } elseif ( $driver_reply == '5' ) {
                                                Message::success( __( 'driver_busy' ) );
                                                $response['redirect'] = URL_BASE . "booking.html";
                                                break;
                                            }
                                        }
                                        usleep( 5000000 );
                                        $i = $i + 5000000;
                                        if ( $i == $microseconds ) {
                                            $update_trip_array = array(
                                                 "status" => 4,
                                                'trip_id' => $tripId 
                                            );
                                            $result            = $this->taxidispatchMdl->update_driver_request_details( $update_trip_array );
                                            Message::success( __( 'driver_busy' ) );
                                            $response['redirect'] = URL_BASE . "booking.html";
                                            break;
                                        }
                                    } else {
                                        Message::success( __( 'try_again' ) );
                                        $response['redirect'] = URL_BASE . "booking.html";
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $promoInvalidMsg = array();
                    if ( $promoValid == 0 ) {
                        $promoInvalidMsg['promocode'] = __( 'invalid_promocode' );
                    } else if ( $promoValid == 2 ) {
                        $promoInvalidMsg['promocode'] = __( 'promo_code_limit_exceed' );
                    } else if ( $promoValid == 3 ) {
                        $promoInvalidMsg['promocode'] = __( 'promo_code_startdate' );
                    } else if ( $promoValid == 4 ) {
                        $promoInvalidMsg['promocode'] = __( 'promo_code_expired' );
                    }
                    $response['error'] = $promoInvalidMsg;
                }
            } else {
                $response['error'] = $validator->errors( 'errors' );
            }
        }
        echo json_encode( $response );
        exit;
    }
    /** Passenger Signup **/
    public function action_signup()
    {
        $post       = Arr::map( 'trim', $this->request->post() );
        $response   = array();
        $returncode = 1;
        if ( $post ) {
            if ( DEFAULT_SKIP_CREDIT_CARD ) {
                $post['credit_card_number'] = preg_replace( '/\s+/', '', $post['credit_card_number'] );
            }
            $validator = $this->findMdl->validate_signupForm( $post );
            if ( $validator->check() ) {
                if ( DEFAULT_SKIP_CREDIT_CARD ) {
                    $preAuthorizeAmount = PRE_AUTHORIZATION_REG_AMOUNT;
                    list( $returncode, $paymentResult, $fcardtype,$preAuthorizeAmount) = $this->findMdl->creditcardPreAuthorization( $post['first_name'], $post['credit_card_number'], $post['cvv'], $post['month'], $post['year'], $preAuthorizeAmount );
                    if ( $returncode == 0 ) {
                        //preauthorization with amount "1"
                        $preAuthorizeAmount = PRE_AUTHORIZATION_RETRY_REG_AMOUNT;
                        list( $returncode, $paymentResult, $fcardtype,$preAuthorizeAmount) = $this->findMdl->creditcardPreAuthorization( $post['first_name'], $post['credit_card_number'], $post['cvv'], $post['month'], $post['year'], $preAuthorizeAmount );
                    }
                }
                if ( $returncode != 0 ) {
                    if ( DEFAULT_SKIP_CREDIT_CARD ) {
                        $post['paymentResult']      = $paymentResult;
                        $post['preAuthorizeAmount'] = $preAuthorizeAmount;
                        $post['fcardtype']          = $fcardtype;
                    } else {
                        $post['credit_card_number'] = "";
                        $post['month']              = "";
                        $post['year']               = "";
                        $post['cvv']                = "";
                        $post['paymentResult']      = "";
                        $post['preAuthorizeAmount'] = 0;
                        $post['fcardtype']          = "";
                    }
                    $void_transaction =0;
					if ( DEFAULT_SKIP_CREDIT_CARD ) {
						$passId=$this->session->get('id');
						$void_transaction=$this->findMdl->voidTransactionAfterPreAuthorize($passId,$paymentResult);
                    }
                    $result      = $this->findMdl->passengerSignup( $post,$void_transaction);                   
                    $commonmodel = Model::factory( 'commonmodel' );
                    if ( $result == 1 ) {
                        $mobileNo   = $post['country_code'] . $post['mobile_number'];
                        $p_password = $post['password'];
                        $passName   = $post['first_name'];
                        //sending sms to passenger
                        if ( SMS == 1 ) {
                            $message_details = $commonmodel->sms_message_by_title( 'account_create_sms' );
                            if ( count( $message_details ) > 0 ) {
                                $to      = $mobileNo;
                                $message = $message_details[0]['sms_description'];
                                $message = str_replace( "##USERNAME##", $to, $message );
                                $message = str_replace( "##PASSWORD##", $p_password, $message );
                                $message = str_replace( "##SITE_NAME##", SITE_NAME, $message );
                                $commonmodel->send_sms( $to, $message );
                            }
                        }
                        //sending mail to passenger
                        $replace_variables = array(
                             REPLACE_LOGO => URL_BASE . SITE_LOGO_IMGPATH . $this->domain_name . '_email_logo.png',
                            REPLACE_SITENAME => $this->app_name,
                            REPLACE_USERNAME => $passName,
                            REPLACE_MOBILE => $mobileNo,
                            REPLACE_PASSWORD => $p_password,
                            REPLACE_SITEEMAIL => $this->siteemail,
                            REPLACE_COPYRIGHTS => SITE_COPYRIGHT 
                        );
                        if ( $this->lang != 'en' ) {
                            if ( file_exists( DOCROOT . TEMPLATEPATH . $this->lang . '/driver-register-' . $this->lang . '.html' ) ) {
                                $message = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . $this->lang . '/driver-register-' . $this->lang . '.html', $replace_variables );
                            } else {
                                $message = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'driver-register.html', $replace_variables );
                            }
                        } else {
                            $message = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'driver-register.html', $replace_variables );
                        }
                        /* Added for language email template */
                        $to          = $post['email'];
                        $from        = $this->siteemail;
                        $subject     = __( 'pass_account_details' ) . " - " . $this->app_name;
                        $redirect    = "no";
                        $smtp_result = $commonmodel->smtp_settings();
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
                        Message::success( __( 'signup_success' ) );
                    } else {
                        Message::error( __( 'try_again' ) );
                    }
                    $response['redirect'] = URL_BASE . "dashboard.html";
                } else {
                    if ( DEFAULT_SKIP_CREDIT_CARD ) {
                        $errors['credit_card_number'] = $paymentResult;
                    }
                    $response['error'] = $errors;
                }
            } else {
                $errors = $validator->errors( 'errors' );
                if ( isset( $errors['country_code'] ) ) {
                    unset( $errors['mobile_number'] );
                }
                $response['error'] = $errors;
            }
        }
        echo json_encode( $response );
        exit;
    }
} // End Controller_Find class
