<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
* Contains User Management(Users)details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************/
class Controller_TaximobilityEdit extends Controller_Siteadmin
{
    /**
     ****__construct()****
     */
    public function __construct( Request $request, Response $response )
    {
        parent::__construct( $request, $response );
        $this->is_login();
    }
    public function is_login()
    {
        $session = Session::instance();
        //get current url and set it into session
        //========================================
        $this->session->set( 'requested_url', Request::detect_uri() );
        /**To check Whether the user is logged in or not**/
        if ( !isset( $this->session ) || ( !$this->session->get( 'userid' ) ) && !$this->session->get( 'id' ) ) {
            Message::error( __( 'login_access' ) );
            $this->request->redirect( "/admin/login/" );
        }
        return;
    }
    public function action_company()
    {
        $usertype       = $_SESSION['user_type'];
        $company_userid = $_SESSION['userid'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/login" );
        }
        $edit_model      = Model::factory( 'edit' );
        $add_model       = Model::factory( 'add' );
        $uid             = $this->request->param( 'id' );
        $Company_details = $edit_model->company_details_new( $uid );
        //if invalid id is given redirect to manage page
        if ( count( $Company_details ) == 0 ) {
            $this->request->redirect( "manage/company" );
        }
        $country_details              = $add_model->country_details();
        $city_details                 = $add_model->city_details();
        $state_details                = $add_model->state_details();
        $currencysymbol               = $this->currencysymbol;
        $currencycode                 = $this->all_currency_code;
        $get_company_payment_settings = $edit_model->get_company_payment_settings( $uid );
        $signup_submit                = arr::get( $_REQUEST, 'submit_addcompany' );
        $errors                       = array();
        $post_values                  = array();
        if ( ( $usertype == 'C' ) ) {
            if ( ( $company_userid != $uid ) ) {
                $this->urlredirect->redirect( 'company/dashboard' );
            }
        }
        if ( ( $usertype == 'M' ) ) {
            $this->urlredirect->redirect( 'company/dashboard' );
        }
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values                  = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $post_values['company_image'] = $_FILES['company_image'];
            $validator                    = $edit_model->validate_editcompany( arr::extract( $post_values, array(
                 'firstname',
                'lastname',
                'email',
                'phone',
                'address',
                'company_name',
                'company_address',
                'country',
                'state',
                'city',
                'currency_code',
                'currency_symbol',
                'time_zone',
                'company_image' 
            ) ), $uid );
            if ( $validator->check() )
                {
                $status = $edit_model->editcompany( $uid, $post_values, $_FILES );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_company' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/company" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_company' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'country_details', $country_details )->bind( 'city_details', $city_details )->bind( 'state_details', $state_details )->bind( 'company_details', $Company_details )->bind( 'currency_symbol', $currencysymbol )->bind( 'currency_code', $currencycode )->bind( 'get_company_payment_settings', $get_company_payment_settings );
        $this->template->title      = SITENAME . " | " . __( 'edit_company' );
        $this->template->page_title = __( 'edit_company' );
        $this->template->content    = $view;
    }
    public function action_motor()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $edit_model      = Model::factory( 'edit' );
        $uid             = $this->request->param( 'id' );
        $Company_details = $edit_model->motor_details( $uid );
        $signup_submit   = arr::get( $_REQUEST, 'submit_editmotor' );
        $errors          = array();
        $post_values     = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = $_POST;
            $post        = Arr::map( 'trim', $this->request->post() );
            $validator   = $edit_model->validate_editmotor( arr::extract( $post, array(
                 'companyname' 
            ) ), $uid );
            if ( $validator->check() ) {
                $status = $edit_model->editmotor( $uid, $post );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_motor_company' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/motor" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_motor' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'company_details', $Company_details );
        $this->template->title      = SITENAME . " | " . __( 'manage_motor_company' );
        $this->template->page_title = SITENAME . " | " . __( 'manage_motor_company' );
        $this->template->content    = $view;
    }
    public function action_paymentgateway()
    {
        $usertype         = $_SESSION['user_type'];
        $edit_model       = Model::factory( 'edit' );
        $uid              = $this->request->param( 'id' );
        $payment_settings = $edit_model->get_payment_details( $uid );
        $signup_submit    = arr::get( $_REQUEST, 'submit_editpayment' );
        $errors           = array();
        $post_values      = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = $_POST;
            $post        = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $validator   = $edit_model->validate_editcompanypayment( arr::extract( $post, array(
                 'description',
                'currency_code',
                'currency_symbol',
                'payment_method',
                'payment_gateway_username',
                'payment_gateway_password',
                'payment_gateway_signature',
                'live_payment_gateway_username',
                'live_payment_gateway_password',
                'live_payment_gateway_signature' 
            ) ), $uid );
            if ( $validator->check() ) {
                $status = $edit_model->editcompanypayment( $uid, $post );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_payment_gateway' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                if ( $usertype == "C" ) {
                    $this->request->redirect( "company/payment_gateway_module" );
                } else {
                    $this->request->redirect( "admin/payment_gateway_module" );
                }
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $currencysymbol             = $this->currencysymbol;
        $currencycode               = $this->all_currency_code;
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_company_paymentgateway' )->bind( 'errors', $errors )->bind( 'validator', $validator )->bind( 'postvalue', $post_values )->bind( 'currency_symbol', $currencysymbol )->bind( 'currency_code', $currencycode )->bind( 'payment_settings', $payment_settings );
        $this->template->title      = SITENAME . " | " . __( 'edit_payment_gateway' );
        $this->template->page_title = SITENAME . " | " . __( 'edit_payment_gateway' );
        $this->template->content    = $view;
    }
    public function action_model()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $edit_model    = Model::factory( 'edit' );
        $uid           = $this->request->param( 'id' );
        $motor_details = $edit_model->motordetails();
        $model_details = $edit_model->model_motordetails( $uid );
        //if invalid id is given redirect to manage page
        if ( count( $model_details ) == 0 ) {
            $this->request->redirect( "manage/model" );
        }
        $signup_submit = arr::get( $_REQUEST, 'submit_editmodel' );
        $errors        = array();
        $post_values   = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $formValues  = Arr::extract( $post_values, array(
                 'companyname',
                'model_name',
                'model_size',
                'motor_mid',
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
                'evening_charge',
                'evening_timing_from',
                'evening_timing_to',
                'evening_fare',
                'below_and_above_km',
                'min_km',
                'minutes_fare',
                'description'
            ) );
			
            $file_values = Arr::extract( $_FILES, array(
                 'model_thumb_image',
                'model_thumb_act_image',
                'model_image',
                'android_focus_model_image',
                'android_unfocus_model_image',
                'ios_focus_model_image',
                'ios_unfocus_model_image' 
            ) );
            $values      = Arr::merge( $formValues, $file_values );
            $validator   = $edit_model->validate_editmodel( $values, $uid );
            if ( $validator->check() ) {
                $imageUpload = 0;
                //thumb image
                $path11      = DOCROOT . MODEL_IMGPATH;
                if ( $_FILES['model_thumb_image']['name'] != '' ) {
                    $image_name        = $uid . '.png';
                    $thumb_image_name  = 'thumb_' . $image_name;
                    $thumb_filename    = Upload::save( $_FILES['model_thumb_image'], $thumb_image_name, $path11 );
                    $model_thumb_image = Image::factory( $thumb_filename );
                    Commonfunction::imageresize( $model_thumb_image, '64', '23', $path11, $thumb_image_name, 90 );
                    $imageUpload = 1;
                }
                //thumb active image
                if ( $_FILES['model_thumb_act_image']['name'] != '' ) {
                    $actimage_name         = $uid . '.png';
                    $thumb_act_image_name  = 'thumb_act_' . $actimage_name;
                    $thumb_act_filename    = Upload::save( $_FILES['model_thumb_act_image'], $thumb_act_image_name, $path11 );
                    $model_thumb_act_image = Image::factory( $thumb_act_filename );
                    Commonfunction::imageresize( $model_thumb_act_image, '64', '23', $path11, $thumb_act_image_name, 90 );
                    $imageUpload = 1;
                }
                //large image
                if ( $_FILES['model_image']['name'] != '' ) {
                    $largeimage_name = $uid . '.png';
                    $mdlimage_name   = 'large_' . $largeimage_name;
                    $model_filename  = Upload::save( $_FILES['model_image'], $mdlimage_name, $path11 );
                    $model_image     = Image::factory( $model_filename );
                    Commonfunction::imageresize( $model_image, '512', '176', $path11, $mdlimage_name, 90 );
                    $imageUpload = 1;
                }
                //Android focus image
                if ( $_FILES['android_focus_model_image']['name'] != '' ) {
                    $android_path     = DOCROOT . MODEL_IMGPATH . "android/";
                    $focus_image_name = $uid . '_focus.png';
                    $model_filename   = Upload::save( $_FILES['android_focus_model_image'], $focus_image_name, $android_path );
                    $model_image      = Image::factory( $model_filename );
                    Commonfunction::imageresize( $model_image, '92', '32', $android_path, $focus_image_name, 90 );
                    $imageUpload = 1;
                }
                //Android unfocus image
                if ( $_FILES['android_unfocus_model_image']['name'] != '' ) {
                    $android_path       = DOCROOT . MODEL_IMGPATH . "android/";
                    $unfocus_image_name = $uid . '_unfocus.png';
                    $model_filename     = Upload::save( $_FILES['android_unfocus_model_image'], $unfocus_image_name, $android_path );
                    $model_image        = Image::factory( $model_filename );
                    Commonfunction::imageresize( $model_image, '92', '32', $android_path, $unfocus_image_name, 90 );
                    $imageUpload = 1;
                }
                //iOS focus image
                if ( $_FILES['ios_focus_model_image']['name'] != '' ) {
                    $android_path     = DOCROOT . MODEL_IMGPATH . "ios/";
                    $focus_image_name = $uid . '_focus.png';
                    $model_filename   = Upload::save( $_FILES['ios_focus_model_image'], $focus_image_name, $android_path );
                    $model_image      = Image::factory( $model_filename );
                    Commonfunction::imageresize( $model_image, '164', '164', $android_path, $focus_image_name, 90 );
                    $imageUpload = 1;
                }
                //iOS unfocus image
                if ( $_FILES['ios_unfocus_model_image']['name'] != '' ) {
                    $android_path       = DOCROOT . MODEL_IMGPATH . "ios/";
                    $unfocus_image_name = $uid . '_unfocus.png';
                    $model_filename     = Upload::save( $_FILES['ios_unfocus_model_image'], $unfocus_image_name, $android_path );
                    $model_image        = Image::factory( $model_filename );
                    Commonfunction::imageresize( $model_image, '164', '164', $android_path, $unfocus_image_name, 90 );
                    $imageUpload = 1;
                }
                $status = $edit_model->editmodel( $uid, $post_values );
                if ( $status == 1 || $imageUpload == 1 ) {
                    Message::success( __( 'sucessfull_updated_model_company' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/model" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_model' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'motor_details', $motor_details )->bind( 'model_details', $model_details );
        $this->template->title      = SITENAME . " | " . __( 'edit_model' );
        $this->template->page_title = __( 'edit_model' );
        $this->template->content    = $view;
    }
    public function action_fare()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "company/login" );
        }
        $edit_model    = Model::factory( 'edit' );
        $uid           = $this->request->param( 'id' );
        $motor_details = $edit_model->motordetails();
        $model_details = $edit_model->model_faredetails( $uid );
        if ( count( $model_details ) > 0 ) {
            $modelid    = $model_details[0]['model_id'];
            $model_name = $edit_model->model_motordetails( $modelid );
        } else {
            $this->request->redirect( "manage/fare" );
        }
        $signup_submit = arr::get( $_REQUEST, 'submit_editmodel' );
        $errors        = array();
        $post_values   = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $validator   = $edit_model->validate_editfare( arr::extract( $post_values, array(
                 'company_model_fare_id',
                'model_name',
                'model_size',
                'base_fare',
                'min_fare',
                'cancellation_fare',
                'below_km',
                'above_km',
                'night_charge',
                'night_timing_from',
                'night_timing_to',
                'night_fare',
                'waiting_time',
                'min_km',
                'below_and_above_km',
                'minutes_fare',
                'evening_charge',
                'evening_timing_from',
                'evening_timing_to',
                'evening_fare' 
            ) ) );
            if ( $validator->check() ) {
                $status = $edit_model->editfare( $post_values );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_fare_company' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/fare" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_fare' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'motor_details', $motor_details )->bind( 'model_details', $model_details )->bind( 'model_name', $model_name );
        $this->template->title      = __( 'edit_fare' );
        $this->template->page_title = __( 'edit_fare' );
        $this->template->content    = $view;
    }
    public function action_driver()
    {
        $user_createdby      = $_SESSION['userid'];
        $usertype            = $_SESSION['user_type'];
        $company_id          = $_SESSION['company_id'];
        $country_id          = $_SESSION['country_id'];
        $state_id            = $_SESSION['state_id'];
        $city_id             = $_SESSION['city_id'];
        $edit_model          = Model::factory( 'edit' );
        $add_model           = Model::factory( 'add' );
        /**To get the form submit button name**/
        $signup_submit       = arr::get( $_REQUEST, 'submit_driver' );
        $errors              = array();
        $post_values         = array();
        $uid                 = $this->request->param( 'id' );
        $view_model          = Model::factory( 'manage' );
        $driver_info_details = $info_arr = array();
        $Company_details     = $edit_model->driver_details_edit( $uid );
        if ( !empty( $Company_details ) ) {
            foreach ( $Company_details as $c ) {
                $info_arr['driver_license_expire_date']            = isset( $c['driver_license_expire_date'] ) ? $c['driver_license_expire_date'] : '';
                $info_arr['driver_pco_license_number']             = isset( $c['driver_pco_license_number'] ) ? $c['driver_pco_license_number'] : '';
                $info_arr['driver_pco_license_expire_date']        = isset( $c['driver_pco_license_expire_date'] ) ? $c['driver_pco_license_expire_date'] : '';
                $info_arr['driver_insurance_number']               = isset( $c['driver_insurance_number'] ) ? $c['driver_insurance_number'] : '';
                $info_arr['driver_insurance_expire_date']          = isset( $c['driver_insurance_expire_date'] ) ? $c['driver_insurance_expire_date'] : '';
                $info_arr['driver_national_insurance_number']      = isset( $c['driver_national_insurance_number'] ) ? $c['driver_national_insurance_number'] : '';
                $info_arr['driver_national_insurance_expire_date'] = isset( $c['driver_national_insurance_expire_date'] ) ? $c['driver_national_insurance_expire_date'] : '';
            }
            $driver_info_details[] = $info_arr;
        }
        if ( ( $usertype == 'C' ) ) {
            if ( ( $company_id != $Company_details[0]['company_id'] ) || ( $Company_details[0]['user_type'] != 'D' ) ) {
                $this->urlredirect->redirect( 'company/dashboard' );
            }
        } else if ( ( $usertype == 'M' ) ) {
            if ( ( $company_id != $Company_details[0]['company_id'] ) || ( $state_id != $Company_details[0]['login_state'] ) || ( $city_id != $Company_details[0]['login_city'] ) || ( $country_id != $Company_details[0]['login_country'] ) || ( $Company_details[0]['user_type'] != 'D' ) ) {
                $this->urlredirect->redirect( 'manager/dashboard' );
            }
        }
        if ( count( $Company_details ) == 0 ) {
            $this->urlredirect->redirect( 'manage/driver' );
        }
        $country_details     = $add_model->country_details();
        $city_details        = $add_model->city_details();
        $state_details       = $add_model->state_details();
        $taxicompany_details = $add_model->taxicompany_details();
        $driver              = Model::factory( 'driver' );
        if ( $signup_submit && Validation::factory( $_POST, $_FILES ) ) {
            $post_values                    = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $post_values['profile_picture'] = $_FILES['profile_picture'];
            $form_values                    = Arr::extract( $post_values, array(
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
                'brand_type',
                'profile_picture' 
            ) );
            $file_values                    = Arr::extract( $_FILES, array(
                 'profile_picture' 
            ) );
            $values                         = Arr::merge( $form_values, $file_values );
            $validator                      = $edit_model->validate_editdriver( $values, $uid );
            if ( $validator->check() ) {
                $imgstatus = 0;
                if ( !empty( $_FILES['profile_picture']['name'] ) ) {
                    $image_name       = uniqid() . $_FILES['profile_picture']['name'];
                    $thumb_image_name = 'thumb_' . $image_name;
                    $image_type       = explode( '.', $image_name );
                    $image_type       = end( $image_type );
                    $filename         = Upload::save( $_FILES['profile_picture'], $image_name, DOCROOT . SITE_DRIVER_IMGPATH );
                    //Image resize and crop for thumb image
                    $logo_image       = Image::factory( $filename );
                    $path11           = DOCROOT . SITE_DRIVER_IMGPATH;
                    $path1            = $image_name;
                    Commonfunction::imageresize( $logo_image, PASS_IMG_WIDTH, PASS_IMG_HEIGHT, $path11, $image_name, 90 );
                    $path12 = $thumb_image_name;
                    Commonfunction::imageresize( $logo_image, PASS_THUMBIMG_WIDTH, PASS_THUMBIMG_HEIGHT, $path11, $thumb_image_name, 90 );
                    $imgstatus = $driver->update_driverimage( $path1, $uid );
                }
                $status = $edit_model->edit_driver( $post_values, $uid );
                if ( $status == 1 || $imgstatus == 1 ) {
                    Message::success( __( 'sucessfull_updated_driver' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/driver" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                       = View::factory( 'admin/edit_driver' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'country_details', $country_details )->bind( 'state_details', $state_details )->bind( 'city_details', $city_details )->bind( 'taxicompany_details', $taxicompany_details )->bind( 'driver_info_details', $driver_info_details )->bind( 'company_details', $Company_details );
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __( 'edit_driver' );
        $this->template->page_title = __( 'edit_driver' );
        $this->template->content    = $view;
    }
    
    public function action_taxi()
    {
        $model_company   = '';
        $user_createdby  = $_SESSION['userid'];
        $usertype        = $_SESSION['user_type'];
        $company_id      = $_SESSION['company_id'];
        $country_id      = $_SESSION['country_id'];
        $state_id        = $_SESSION['state_id'];
        $city_id         = $_SESSION['city_id'];
        $add_model       = Model::factory( 'add' );
        $edit_model      = Model::factory( 'edit' );
        $uid             = $this->request->param( 'id' );
        $id              = $this->request->param( 'id' );
        $view_controller = Model::factory( 'manage' );
        $Company_details = $edit_model->managetaxi_details( $uid );
        if ( count( $Company_details ) == 0 ) {
            $this->request->redirect( "manage/taxi" );
        }
        $model_company = isset( $Company_details[0]['taxi_company'] ) ? $Company_details[0]['taxi_company'] : '';
        $tmid          = $Company_details;
        if ( ( $usertype == 'C' ) ) {
            if ( $company_id != $tmid[0]['taxi_company'] ) {
                $this->urlredirect->redirect( 'company/dashboard' );
            }
        } else if ( ( $usertype == 'M' ) ) {
            if ( ( $company_id != $tmid[0]['taxi_company'] ) || ( $state_id != $tmid[0]['taxi_state'] ) || ( $city_id != $tmid[0]['taxi_city'] ) || ( $country_id != $tmid[0]['taxi_country'] ) ) {
                $this->urlredirect->redirect( 'manager/dashboard' );
            }
        }
        $signup_submit       = arr::get( $_REQUEST, 'submit_edittaxi' );
        $errors              = array();
        $post_values         = array();
        $form_array          = '';
        $model_details       = $add_model->model_details_new( $model_company );
        $country_details     = $add_model->country_details();
        $state_details       = $add_model->state_details();
        $city_details        = $add_model->city_details();
        $additional_fields   = $add_model->taxi_additionalfields();
        $taxicompany_details = $add_model->taxicompany_details();
        $array_count         = count( $additional_fields );
        if ( $array_count > 0 ) {
            for ( $i = 0; $i < $array_count; $i++ ) {
                if ( $i > 0 ) {
                    $form_array .= "," . $additional_fields[$i]['field_name'];
                } else {
                    $form_array .= $additional_fields[$i]['field_name'];
                }
            }
            $form_array = explode( ',', $form_array );
        } else {
            $form_array = array();
        }
        $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
        $values      = Arr::merge( $post_values, $form_array );
        if ( $signup_submit && Validation::factory( $values, $_FILES ) ) {
            $comny_id         = $post_values['company_name'];
            $modelid          = $post_values['taxi_model'];
            $check_fare_exist = $add_model->check_fare_exist( $comny_id, $modelid );
            if ( $check_fare_exist == 0 ) {
                Message::error( __( 'fare_not_avaliable' ) );
                if ( $usertype == 'C' ) {
                    $this->request->redirect( "manage/fare" );
                } else {
                    $this->request->redirect( "manage/model" );
                }
            }
            $taxi_old_img         = $post_values['taxi_old_img'];
            $values['taxi_image'] = $_FILES['taxi_image'];
            $validator            = $edit_model->validate_edittaxi( $values, $form_array, $uid );
            if ( $validator->check() ) {
                if ( !empty( $_FILES['taxi_image']['name'] ) ) {
                    /* image */
                    if ( file_exists( DOCROOT . TAXI_IMG_IMGPATH . $taxi_old_img ) ) {
                        unlink( DOCROOT . TAXI_IMG_IMGPATH . $taxi_old_img );
                    }
                    if ( file_exists( DOCROOT . TAXI_IMG_IMGPATH . "tmb32_" . $taxi_old_img ) ) {
                        unlink( DOCROOT . TAXI_IMG_IMGPATH . "tmb32_" . $taxi_old_img );
                    }
                    if ( file_exists( DOCROOT . TAXI_IMG_IMGPATH . "tmb100_" . $taxi_old_img ) ) {
                        unlink( DOCROOT . TAXI_IMG_IMGPATH . "tmb100_" . $taxi_old_img );
                    }
                    $image_name = uniqid() . $_FILES['taxi_image']['name'];
                    $image_type = explode( '.', $image_name );
                    $image_type = end( $image_type );
                    $image_name = url::title( $image_name ) . '.' . $image_type;
                    $filename   = Upload::save( $_FILES['taxi_image'], $image_name, DOCROOT . TAXI_IMG_IMGPATH );
                    //Image resize and crop for thumb image
                    $logo_image = Image::factory( $filename );
                    $path1      = DOCROOT . TAXI_IMG_IMGPATH;
                    $path       = $image_name;
                    Commonfunction::taxiimageresize( $logo_image, TAXI_IMG_WIDTH, TAXI_IMG_HEIGHT, $path1, $image_name, 90 );
                    /**** Taxi APP THU100 ***/
                    $tmb100_image_name = 'tmb100_' . $image_name;
                    Commonfunction::taxiimageresize( $logo_image, TAXI_APP_THMB100_IMG_WIDTH, TAXI_APP_THMB100_IMG_HEIGHT, $path1, $tmb100_image_name, 90 );
                    /**** TAxi APP THUM50 ***/
                    $tmb32_image_name = 'tmb32_' . $image_name;
                    Commonfunction::taxiimageresize( $logo_image, TAXI_APP_THMB32_IMG_WIDTH, TAXI_APP_THMB32_IMG_HEIGHT, $path1, $tmb32_image_name, 90 );
                    if ( $image_type == 'jpeg' || $image_type == 'jpg' ) {
                        $base_image   = imagecreatefromjpeg( $path1 . $tmb32_image_name );
                        $width        = 32;
                        $height       = 12;
                        $top_image    = imagecreatefrompng( URL_BASE . "public/common/images/view.png" );
                        $merged_image = $path1 . $tmb32_image_name;
                        imagesavealpha( $top_image, true );
                        imagealphablending( $top_image, true );
                        imagecopy( $base_image, $top_image, 0, 23, 0, 0, $width, $height );
                        imagejpeg( $base_image, $merged_image );
                    }
                    if ( $image_type == 'png' ) {
                        $base_image   = imagecreatefrompng( $path1 . $tmb32_image_name );
                        $width        = 32;
                        $height       = 12;
                        $top_image    = imagecreatefrompng( URL_BASE . "public/common/images/view.png" );
                        $merged_image = $path1 . $tmb32_image_name;
                        imagesavealpha( $top_image, true );
                        imagealphablending( $top_image, true );
                        imagecopy( $base_image, $top_image, 0, 23, 0, 0, $width, $height );
                        imagepng( $base_image, $merged_image );
                    }
                    $status = $edit_model->edittaxi_image( $path, $uid );
                }
                $status = $edit_model->edittaxi( $post_values, $form_array, $uid, $_FILES );
                Message::success( __( 'sucessfull_updated_taxi' ) );
                $this->request->redirect( "manage/taxi" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                       = View::factory( 'admin/edit_taxi' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'additional_fields', $additional_fields )->bind( 'company_details', $Company_details )->bind( 'city_details', $city_details )->bind( 'state_details', $state_details )->bind( 'model_details', $model_details )->bind( 'country_details', $country_details )->bind( 'taxicompany_details', $taxicompany_details )->bind( 'postvalue', $post_values );
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __( 'edit_taxi' );
        $this->template->page_title = __( 'edit_taxi' );
        $this->template->content    = $view;
    }
    public function action_country()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $edit_model      = Model::factory( 'edit' );
        $uid             = $this->request->param( 'id' );
        $Company_details = $edit_model->country_details( $uid );
        $signup_submit   = arr::get( $_REQUEST, 'submit_editcountry' );
        $errors          = array();
        $post_values     = array();
        if ( empty( $Company_details ) ) {
            $this->request->redirect( "manage/country" );
        }
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $validator   = $edit_model->validate_editcountry( arr::extract( $post_values, array(
                 'country_name',
                'iso_country_code',
                'telephone_code',
                'currency_code',
                'currency_symbol' 
            ) ), $uid );
            if ( $validator->check() ) {
                $status = $edit_model->editcountry( $uid, $post_values );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_country' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/country" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_country' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'company_details', $Company_details );
        $this->template->title      = SITENAME . " | " . __( 'edit_country' );
        $this->template->page_title = __( 'edit_country' );
        $this->template->content    = $view;
    }
    public function action_city()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $edit_model    = Model::factory( 'edit' );
        $uid           = $this->request->param( 'id' );
        $motor_details = $edit_model->country_details_new();
        $state_details = $edit_model->state_details();
        $model_details = $edit_model->city_countrydetails( $uid );
        if ( empty( $model_details ) ) {
            $this->request->redirect( "manage/city" );
        }
        $signup_submit = arr::get( $_REQUEST, 'submit_editcity' );
        $errors        = array();
        $post_values   = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $validator   = $edit_model->validate_editcity( arr::extract( $post_values, array(
                 'country_name',
                'state_name',
                'city_name',
                'zipcode',
                'city_countryid',
                'city_model_fare' 
            ) ), $uid );
            if ( $validator->check() ) {
                $status = $edit_model->editcity( $uid, $post_values );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_city' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/city" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_city' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'motor_details', $motor_details )->bind( 'state_details', $state_details )->bind( 'model_details', $model_details );
        $this->template->title      = SITENAME . " | " . __( 'edit_city' );
        $this->template->page_title = __( 'edit_city' );
        $this->template->content    = $view;
    }
    public function action_state()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $edit_model      = Model::factory( 'edit' );
        $uid             = $this->request->param( 'id' );
        $country_details = $edit_model->countrydetails();
        $state_details   = $edit_model->state_countrydetails( $uid );
        $signup_submit   = arr::get( $_REQUEST, 'submit_editstate' );
        $errors          = array();
        $post_values     = array();
        if ( empty( $state_details ) ) {
            $this->request->redirect( "manage/state" );
        }
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $validator   = $edit_model->validate_editstate( arr::extract( $post_values, array(
                 'country_name',
                'state_name',
                'state_countryid' 
            ) ), $uid );
            if ( $validator->check() ) {
                $status = $edit_model->editstate( $uid, $post_values );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_state' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/state" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_state' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'country_details', $country_details )->bind( 'state_details', $state_details );
        $this->template->title      = SITENAME . " | " . __( 'edit_state' );
        $this->template->page_title = __( 'edit_state' );
        $this->template->content    = $view;
    }
    public function action_manager()
    {
        $usertype   = $_SESSION['user_type'];
        $company_id = $_SESSION['company_id'];
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $edit_model      = Model::factory( 'edit' );
        $add_model       = Model::factory( 'add' );
        $view_model      = Model::factory( 'manage' );
        /**To get the form submit button name**/
        $signup_submit   = arr::get( $_REQUEST, 'submit_editmanager' );
        $errors          = array();
        $post_values     = array();
        $uid             = $this->request->param( 'id' );
        $Company_details = $edit_model->manager_details( $uid );
        if ( $usertype == 'C' ) {
            if ( empty( $Company_details ) ) {
                $this->urlredirect->redirect( 'company/dashboard' );
            } else {
                if ( $company_id != $Company_details[0]['company_id'] ) {
                    $this->urlredirect->redirect( 'company/dashboard' );
                }
            }
        }
        $taxicompany_details = $add_model->taxicompany_details();
        $country_details     = $add_model->country_details_new();
        $state_details       = $add_model->state_details();
        $city_details        = $add_model->city_details();
        $is_executive_id     = 0;
        $is_executive        = 0;
        if ( ( $_SESSION['user_type'] != 'S' ) && ( TDISPATCH_VIEW == 1 ) && isset( $_SESSION['vbx_show'] ) && $_SESSION['vbx_show'] == 1 ) {
            $admins_model     = Model::factory( 'admin' );
            $insert_executive = $admins_model->get_database_exists( 'text', $Company_details[0]['email'] );
            $is_executive_id  = $insert_executive;
            if ( $insert_executive > 0 ) {
                $is_executive = 1;
            }
        }
        if ( count( $Company_details ) == 0 ) {
            $this->request->redirect( "manage/manager" );
        }
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $form_values = Arr::extract( $post_values, array(
                 'firstname',
                'lastname',
                'email',
                'phone',
                'address',
                'country',
                'state',
                'city',
                'company_name' 
            ) );
            $validator   = $edit_model->validate_editmanager( $form_values, $uid );
            if ( $validator->check() ) {
                $status = $edit_model->edit_manager( $post_values, $uid );
                if ( isset( $post_values['add_as_executive'] ) && $post_values['add_as_executive'] == 1 ) {
                    $admins_model = Model::factory( 'admin' );
                    if ( isset( $post_values['callcenter_id'] ) && $post_values['callcenter_id'] > 0 ) {
                        $insert_executive = $admins_model->update_executive( $post_values );
                    } else {
                        $insert_executive = $admins_model->add_executive( $post_values );
                        $status           = 1;
                    }
                } elseif ( isset( $post_values['callcenter_id'] ) && $post_values['callcenter_id'] > 0 ) {
                    $admins_model     = Model::factory( 'admin' );
                    $insert_executive = $admins_model->delete_executive( $post_values['callcenter_id'] );
                    $status           = 1;
                }
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_manager' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/manager" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                       = View::factory( 'admin/edit_manager' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'country_details', $country_details )->bind( 'city_details', $city_details )->bind( 'taxicompany_details', $taxicompany_details )->bind( 'state_details', $state_details )->bind( 'is_executive_id', $is_executive_id )->bind( 'is_executive', $is_executive )->bind( 'company_details', $Company_details );
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __( 'edit_manager' );
        $this->template->page_title = __( 'edit_manager' );
        $this->template->content    = $view;
    }
    public function action_admin()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "manager/login" );
        }
        $edit_model    = Model::factory( 'edit' );
        $add_model     = Model::factory( 'add' );
        $view_model    = Model::factory( 'manage' );
        /**To get the form submit button name**/
        $signup_submit = arr::get( $_REQUEST, 'submit_editadmin' );
        $errors        = array();
        $post_values   = array();
        $uid           = $this->request->param( 'id' );
        $user_details  = $edit_model->moderator_details( $uid );
        if ( count( $user_details ) == 0 ) {
            $this->request->redirect( "manage/admin" );
        }
        $country_details = $add_model->country_details();
        $state_details   = $add_model->state_details();
        $city_details    = $add_model->city_details();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $form_values = Arr::extract( $post_values, array(
                'firstname',
                'lastname',
                'email',
                'phone',
                'address',
                'country',
                'company_name' 
            ) );
            $validator   = $edit_model->validate_editadmin( $form_values, $uid );
            if ( $validator->check() ) {
                $status = $edit_model->edit_admin( $post_values, $uid );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_superadmin' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/admin" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                       = View::factory( 'admin/edit_admin' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'country_details', $country_details )->bind( 'city_details', $city_details )->bind( 'state_details', $state_details )->bind( 'user_details', $user_details );
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __( 'edit_superadmin' );
        $this->template->page_title = __( 'edit_superadmin' );
        $this->template->content    = $view;
    }
    public function action_assigntaxi()
    {
        $usertype        = $_SESSION['user_type'];
        $edit_model      = Model::factory( 'edit' );
        $add_model       = Model::factory( 'add' );
        /**To get the form submit button name**/
        $signup_submit   = arr::get( $_REQUEST, 'submit_editassigntaxi' );
        $errors          = array();
        $post_values     = array();
        $uid             = $this->request->param( 'id' );
        $company_details = $edit_model->assigntaxi_details( $uid );
        //redirect to list page if the assigned details not there
        if ( count( $company_details ) == 0 ) {
            $this->request->redirect( "manage/assigntaxi" );
        }
        $country_details     = $add_model->country_details();
        $state_details       = $add_model->state_details();
        $city_details        = $add_model->city_details();
        $taxicompany_details = $add_model->taxicompany_details();
        $driver_details      = $add_model->driver_details();
        $taxi_details        = $add_model->taxi_details();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = $_POST;
            $post        = Arr::map( 'trim', $this->request->post() );
            $form_values = Arr::extract( $post, array(
                 'company_name',
                'country',
                'state',
                'city',
                'driver',
                'taxi',
                'startdate',
                'enddate' 
            ) );
            $validator   = $edit_model->validate_editassigntaxi( $form_values, $uid );
            if ( $validator->check() ) {
                $update = $edit_model->edit_assigntaxi( $post, $uid );
                if ( $update != 0 ) {
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
                    $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'assign_taxi.html', $replace_variables );
                    $to                = $update[0]['email'];
                    $from              = $this->siteemail;
                    $subject           = __( 'taxi_assigned_you' );
                    $redirect          = "manage/assigntaxi";
                    $smtp_result       = $add_model->smtp_settings();
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
                    Message::success( __( 'sucessfull_assign_taxi' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/assigntaxi" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                       = View::factory( 'admin/edit_assigntaxi' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'country_details', $country_details )->bind( 'city_details', $city_details )->bind( 'state_details', $state_details )->bind( 'taxicompany_details', $taxicompany_details )->bind( 'driver_details', $driver_details )->bind( 'taxi_details', $taxi_details )->bind( 'postvalue', $post_values )->bind( 'company_details', $company_details );
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __( 'edit_assigned_taxi' );
        $this->template->page_title = __( 'edit_assigned_taxi' );
        $this->template->content    = $view;
    }
    public function action_unavailability()
    {
        $user_createdby      = $_SESSION['userid'];
        $usertype            = $_SESSION['user_type'];
        $uid                 = $this->request->param( 'id' );
        $view_controller     = Model::factory( 'edit' );
        $post_values         = array();
        $errors              = array();
        $unavailable_details = $view_controller->unavailability_details( $uid );
        $signup_submit       = arr::get( $_REQUEST, 'submit_addleave' );
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = $_POST;
            $post        = Arr::map( 'trim', $this->request->post() );
            $validator   = $view_controller->validate_unavailabledriver( arr::extract( $post, array(
                 'reason',
                'startdate',
                'enddate',
                'driver_id',
                'taxi_id' 
            ) ), $uid );
            if ( $validator->check() ) {
                $update = $view_controller->edit_unavailabledriver( $post, $uid );
                Message::success( __( 'profile_updated_successfully' ) );
                $this->request->redirect( "manage/unavailability" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                       = View::factory( 'admin/edit_unavailability' )->bind( 'pag_data', $pag_data )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'unavailable_details', $unavailable_details )->bind( 'Offset', $offset )->bind( 'postvalue', $post_values );
        $this->page_title           = __( 'mark_unavailable' );
        $this->template->title      = SITENAME . " | " . __( 'mark_unavailable' );
        $this->template->page_title = __( 'mark_unavailable' );
        $this->template->content    = $view;
    }
    public function action_getunavilabledriverlist()
    {
        $manage_model  = Model::factory( 'edit' );
        $output        = '';
        $driver_id     = arr::get( $_REQUEST, 'driver_id' );
        $taxi_id       = arr::get( $_REQUEST, 'taxi_id' );
        $page_title    = __( 'unavailability' );
        $page_no       = arr::get( $_REQUEST, 'page' );
        $count_details = $manage_model->getunavailabledriverlist( $driver_id, $taxi_id );
        if ( $page_no )
            $offset = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data          = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/punceal' 
        ) );
        $getdriver_details = $manage_model->get_unavailabledriverlist( $driver_id, $taxi_id, $offset, REC_PER_PAGE );
        $count             = count( $getdriver_details );
        $output .= '<div class="widget">
                <div class="title"><img src="' . IMGPATH . 'icons/dark/frames.png" alt="" class="titleIcon" /><h6>' . $page_title . '</h6>
                <div style="width:auto; float:right; margin: 4px 3px;">';
        if ( $count > 0 ) {
            $output .= '<div class="button greyish"></div>                       
                </div>';
        }
        $output .= '</div>';
        if ( $count > 0 ) {
            $output .= '<div class= "overflow-block">';
        }
        $output .= '<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">';
        if ( $count > 0 ) {
            $output .= '<thead>
                <tr>
                <td align="left" width="5%">' . __( 'sno_label' ) . '</td>
                <td align="left" width="10%">' . ucfirst( __( 'driver_name' ) ) . '</td>
                <td align="left" width="10%">' . __( 'email_label' ) . '</td>
                <td align="left" width="10%">' . __( 'reason' ) . '</td>
                <td align="left" width="10%">' . __( 'from_date' ) . '</td>
                <td align="left" width="10%">' . __( 'end_date' ) . '</td>
                </tr>
                </thead>
                <tbody>    ';
            /* For Serial No */
            $sno = $offset;
            foreach ( $getdriver_details as $listings ) {
                //S.No Increment
                //==============
                $sno++;
                //For Odd / Even Rows
                //===================
                $trcolor = ( $sno % 2 == 0 ) ? 'oddtr' : 'eventr';
                $output .= '<tr class="' . $trcolor . '">';
                $output .= '<td>' . $sno . '</td>
                <td><a href=' . URL_BASE . 'manage/driverinfo/' . $listings['id'] . '>' . wordwrap( $listings['name'], 30, '<br/>', 1 ) . '</a></td>
                <td>' . wordwrap( $listings['email'], 25, '<br />', 1 ) . '</td>
                <td>' . $listings['u_reason'] . '</td>
                <td>' . $listings['u_startdate'] . '</td>
                <td>' . $listings['u_enddate'] . '</td></tr>';
            }
        }
        //For No Records
        //==============
        else {
            $output .= '<tr>
                <td class="nodata">' . __( 'no_data' ) . '</td>
                </tr>';
        }
        $output .= '</tbody>
                </table>';
        if ( $count > 0 ) {
            $output .= '</div>';
        }
        $output .= '</div><div class="clr">&nbsp;</div>';
        $output .= '<div class="pagination">';
        if ( $count > 0 ) {
            $output .= '<p>' . $pag_data->render() . '</p>';
        }
        $output .= '</div><div class="clr">&nbsp;</div>';
        echo $output;
        exit;
    }
    // Edit manu details
    public function action_menu()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/login" );
        }
        $edit_model    = Model::factory( 'edit' );
        $mid           = $this->request->param( 'id' );
        $model_details = $edit_model->get_menu( $mid );
        if ( count( $model_details ) == 0 ) {
            $this->request->redirect( "manage/menu" );
        }
        $signup_submit = arr::get( $_REQUEST, 'submit_editmenu' );
        $errors        = array();
        $post_values   = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $validator   = $edit_model->validate_editmenu( arr::extract( $post_values, array(
                 'menu_name',
                'slug' 
            ) ), $mid );
            if ( $validator->check() ) {
                //~ $menu_name_exits = $edit_model->menu_name_exits( $mid, $post_values );
                //~ if ( $menu_name_exits == 1 ) {
                    //~ Message::error( __( 'menu_name_exits' ) );
                    //~ $this->request->redirect( "manage/menu" );
                //~ }
                $status = $edit_model->update_menu( $mid, $post_values );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_menu' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/menu" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( 'admin/edit_menu' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'model_details', $model_details );
        $this->template->title      = SITENAME . " | " . __( 'edit_menu' );
        $this->template->page_title = __( 'edit_menu' );
        $this->template->content    = $view;
    }
    // Edit mile details
    public function action_mile()
    {
        $usertype = $_SESSION['user_type'];
        if ( ( $usertype != 'A' ) && ( $usertype != 'S' ) ) {
            $this->request->redirect( "admin/login" );
        }
        $edit_model    = Model::factory( 'edit' );
        $mid           = $this->request->param( 'id' );
        $model_details = $edit_model->get_mile( $mid );
        $signup_submit = arr::get( $_REQUEST, 'submit_editmile' );
        $errors        = array();
        $post_values   = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = $_POST;
            $post        = Arr::map( 'trim', $this->request->post() );
            $validator   = $edit_model->validate_editmile( arr::extract( $post, array(
                 'mile' 
            ) ), $mid );
            if ( $validator->check() ) {
                $mile_name_exits = $edit_model->mile_name_exits( $mid, $_POST );
                if ( $mile_name_exits == 1 ) {
                    Message::error( __( 'mile_name_exits' ) );
                    $this->request->redirect( "manage/mile" );
                }
                $status = $edit_model->update_mile( $mid, $_POST );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_mile' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/mile" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( 'admin/edit_mile' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'model_details', $model_details );
        $this->template->title      = SITENAME . " | " . __( 'edit_mile' );
        $this->template->page_title = SITENAME . " | " . __( 'edit_mile' );
        $this->template->content    = $view;
    }
    public function action_sms_templates()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/login" );
        }
        $edit_model   = Model::factory( 'edit' );
        $check_id     = explode( '/', $_SERVER['REQUEST_URI'] );
        $sms_id       = $check_id[3];
        $sms_template = $edit_model->sms_template( $sms_id );
        if ( empty( $sms_template ) ) {
            $this->request->redirect( "admin/sms_template" );
        }
        $signup_submit = arr::get( $_REQUEST, 'submit_edit_template' );
        $errors        = array();
        $post_values   = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = $_POST;
            $post        = Arr::map( 'trim', $this->request->post() );
            $validator   = $edit_model->validate_edit_template( arr::extract( $post, array(
                 'sms_description' 
            ) ), $sms_id );
            if ( $validator->check() ) {
                $status = $edit_model->edittemplate( $sms_id, $post );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_modified_sms_message' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "admin/sms_template" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_smstemplate' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'sms_template', $sms_template );
        $this->template->title      = __( 'edit_sms_template' );
        $this->template->page_title = __( 'edit_sms_template' );
        $this->template->content    = $view;
    }
    public function action_banner()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "company/login" );
        }
        $edit_model     = Model::factory( 'edit' );
        $uid            = $this->request->param( 'id' );
        $banner_details = $edit_model->bannerdetails( $uid );
        $signup_submit  = arr::get( $_REQUEST, 'submit_banner' );
        $errors         = array();
        $post_values    = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = $_POST;
            $post        = Arr::map( 'trim', $this->request->post() );
            $add         = Model::factory( 'add' );
            $validator   = $add->validate_addbanner( arr::extract( $post, array(
                 'banner_image',
                'tags',
                'image_tag' 
            ) ), $_FILES );
            if ( $validator->check() ) {
                $image_updated_status = '';
                $image_id             = $_POST['image_id'];
                if ( !empty( $_FILES['banner_image']['name'] ) ) {
                    /* image1 */
                    $image_name1 = uniqid() . $_FILES['banner_image']['name'];
                    $image_type  = explode( '.', $image_name1 );
                    $image_type  = end( $image_type );
                    $filename    = Upload::save( $_FILES['banner_image'], $image_name1, DOCROOT . BANNER_IMGPATH );
                    //Image resize and crop for thumb image
                    $logo_image1 = Image::factory( $filename );
                    $path11      = DOCROOT . BANNER_IMGPATH;
                    $path1       = $image_name1;
                    Commonfunction::imageresize( $logo_image1, BANNER_SLIDER_WIDTH, BANNER_SLIDER_HEIGHT, $path11, $image_name1, 90 );
                    $image_updated_status = $edit_model->update_banner_image( $path1, $image_id );
                }
                $tags                 = $_POST['tags'];
                $image_tag            = $_POST['image_tag'];
                $image_updated_status = $edit_model->update_banner_details( $tags, $image_tag, $image_id );
                if ( $image_updated_status == 0 ) {
                    Message::success( __( 'banner_updated_successfully' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/banner" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_banner' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'banner_details', $banner_details );
        $this->template->title      = SITENAME . " | " . __( 'manage_banner' );
        $this->template->page_title = SITENAME . " | " . __( 'manage_banner' );
        $this->template->content    = $view;
    }
    public function action_faq()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/login" );
        }
        $edit_model    = Model::factory( 'edit' );
        /**To get the form submit button name**/
        $signup_submit = arr::get( $_REQUEST, 'submit_editfaq' );
        $errors        = array();
        $post_values   = array();
        $fid           = $this->request->param( 'id' );
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $validator   = $edit_model->validate_editfaq( arr::extract( $post_values, array(
                 'faq_title',
                'faq_details' 
            ) ), $fid );
            if ( $validator->check() ) {
                $signup_id = $edit_model->editfaq( $post_values, $fid );
                if ( $signup_id == 1 ) {
                    Message::success( __( 'sucessfull_added_faq' ) );
                    $this->request->redirect( "manage/faq" );
                } else {
                    Message::error( __( 'not_updated' ) );
                    $this->request->redirect( "manage/faq" );
                }
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $faq_details                = $edit_model->get_faqdetails( $fid );
        $view                       = View::factory( 'admin/edit_faq' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'faq_details', $faq_details );
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __( 'edit_faq' );
        $this->template->page_title = __( 'edit_faq' );
        $this->template->content    = $view;
    }
    public function action_promocode()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'C' ) {
            $this->request->redirect( "admin/login" );
        }
        $id                = $this->request->param( 'id' );
        $edit_model        = Model::factory( 'edit' );
        $manage            = Model::factory( 'manage' );
        /**To get the form submit button name**/
        $signup_submit     = arr::get( $_REQUEST, 'submit_addmodel' );
        $errors            = $post_values = array();
        $promocode_details = $edit_model->get_promocodedetails( $id );
        if ( count( $promocode_details ) == 0 ) {
            $this->request->redirect( "manage/promocode" );
        }
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values    = $_POST;
            $post           = Arr::map( 'trim', $this->request->post() );
            $validator      = $edit_model->validate_editpromocode( arr::extract( $post, array(
                 'promo_limit' 
            ) ), $id );
            $promo_discount = $post['promo_discount'];
            $promo_code     = $post['promocode'];
            $start_date     = $post['start_date'];
            $expire_date    = $post['expire_date'];
            $promo_limit    = $post['promo_limit'];
            if ( $validator->check() ) {
                if ( isset( $_POST['resend'] ) ) {
                    $promocode_users = $edit_model->get_promocode_users( $promo_code );
                    foreach ( $promocode_users as $val ) {
                        $content           = __( 'promocode_details_changed' );
                        $subjects          = 'Promo code';
                        $name              = $val['name'];
                        $email             = $val['email'];
                        $promocode_msg     = __( 'promocode_msg' );
                        $code              = str_replace( '##DISCOUNT##', $promo_discount, $promocode_msg );
                        $code              = str_replace( '##PROMOCODE##', $promo_code, $code );
                        $replace_variables = array(
                             REPLACE_LOGO => EMAILTEMPLATELOGO,
                            REPLACE_SITENAME => $this->app_name,
                            REPLACE_USERNAME => $name,
                            REPLACE_MESSAGE => str_replace( '\n', '', $content ),
                            REPLACE_STARTDATE => $start_date,
                            REPLACE_EXPIREDATE => $expire_date,
                            REPLACE_USAGELIMIT => $promo_limit,
                            REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                            REPLACE_PROMOCODE => $code,
                            REPLACE_SITEEMAIL => $this->siteemail,
                            REPLACE_SITEURL => URL_BASE,
                            REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                            REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
                        );
                        $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'promocode_message.html', $replace_variables );
                        $to                = $email;
                        $from              = $this->siteemail;
                        $subject           = $subjects . " - " . $this->app_name;
                        $redirect          = "";
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
                    }
                }
                $signup_id = $edit_model->editpromocode( $post, $promo_code );
                if ( $signup_id == 1 ) {
                    Message::success( __( 'sucessfull_promocode_update' ) );
                    $this->request->redirect( "manage/promocode" );
                }
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                       = View::factory( 'admin/edit_promocode' )->bind( 'errors', $errors )->bind( 'promocode_details', $promocode_details )->bind( 'postvalue', $post_values );
        $this->template->content    = $view;
        $this->template->title      = SITENAME . " | " . __( 'edit_promocode' );
        $this->template->page_title = __( 'edit_promocode' );
        $this->template->content    = $view;
    }
    
    public function action_popularplace()
    {
        $usertype = $_SESSION['user_type'];
        if ($usertype != 'A' && $usertype != 'S') {
            $this->request->redirect("admin/login");
        }
        $edit_model      = Model::factory( 'edit' );
        $uid             = $this->request->param( 'id' );
        $city_details = $edit_model->city_details();		
        $place_details = $edit_model->popularplace_details($uid);
        $count = count($place_details);
        $signup_submit   = arr::get( $_POST, 'submit_editplace' );
        $errors          = $post_values     = array();
        if ( empty( $place_details ) ) {
            $this->request->redirect( "manage/popularplace" );
        }
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = $this->request->post();
            $validator   = $edit_model->validate_editpopularplace( arr::extract( $post_values, array(
				'city_name',
                'label_name',
                'location_name'               
            ) ), $uid );
            if ( $validator->check() ) {
                $status = $edit_model->editpopularplace( $uid, $post_values );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_popular' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manage/popularplace" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $location_icon = (defined('POPULAR_ICONS')) ? POPULAR_ICONS : array();
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'edit_popularplace' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'city_details', $city_details )->bind( 'place_details', $place_details )->bind('count',$count)->bind('cityid',$uid)->bind('location_icon', $location_icon);
        $this->template->title      = SITENAME . " | " . __( 'edit_popular' );
        $this->template->page_title = __( 'edit_popular' );
        $this->template->content    = $view;
    }
    
    public function action_removepopularplace()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $edit_model      = Model::factory( 'edit' );
        $cityid             = $this->request->param( 'id' );	
        $remove_id = arr::get( $_REQUEST, 'id' );	
        if($remove_id !=''){		
			$remove_id = base64_decode($remove_id);
			$status = $edit_model->remove_popularplace($remove_id,$cityid);
			if ( $status == 1 ) {
				Message::success( __( 'sucessfull_removed_popular' ) );
			} else {
				Message::error( __( 'not_updated' ) );
			}
		}else{
			Message::error( __( 'notfound_popular' ) );
		}
		$this->request->redirect( "edit/popularplace/".$cityid );          
    }
} // End Welcome
?>
