<?php defined( 'SYSPATH' ) OR die( 'No Direct Script Access' );
/******************************************
* Contains Users Module details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************/
class Controller_TaximobilityCompany extends Controller_Siteadmin
{
    public $balance_amount;
    public function action_index()
    {
        $this->urlredirect->redirect( 'company/login' );
        $this->template->meta_desc     = $this->metadescription;
        $this->template->meta_keywords = $this->metakeywords;
    }
    public function action_login()
    {
        if ( $this->userid ) {
            $this->urlredirect->redirect( 'company/dashboard' );
        }
        $userid      = "";
        $success_msg = "";
        $error_msg   = "";
        //condition checked to show package period expire message
        if ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] == "expire" ) {
            Message::error( __( 'please_upgrade_package' ) );
            $this->urlredirect->redirect( 'company/login' );
        }
        $submit      = $this->request->post( 'admin_login' );
        $form_values = Arr::extract( $_REQUEST, array(
			'email',
            'password',
            'remember_me'
        ) );
        $validate    = $this->authorize->login_validate( $form_values );
        if ( isset( $submit ) ) {
            if ( $validate->check() ) {
                if ( ( $this->authorize->companylogin_details( $form_values['email'], md5( $form_values['password'] ), TRUE, "" ) ) > 0 ) //,""
                    {
                    $select_result = $this->authorize->companylogin_details( $form_values['email'], md5( $form_values['password'] ), FALSE );
                    if ( TIMEZONE ) {
                        $current_time = convert_timezone( 'now', TIMEZONE );
                        $current_date = explode( ' ', $current_time );
                        $date         = $current_date[0];
                    } else {
                        $current_time = date( 'Y-m-d H:i:s' );
                        $date         = date( 'Y-m-d' );
                    }
                    if ( ( $select_result[0]['company_status'] == 'D' ) || ( $select_result[0]['company_status'] == 'T' ) ) {
                        Message::error( __( 'login_deactived' ) );
                    } else {
                        $tdispatch_model    = Model::factory( 'tdispatch' );
                        $userid             = $select_result[0]['id'];
                        $selected_time_zone = ( $select_result[0]['user_time_zone'] != "" ) ? $select_result[0]['user_time_zone'] : $select_result[0]['time_zone'];
                        $this->session->set( "userid", $select_result[0]['id'] );
                        $this->session->set( "user_type", $select_result[0]['user_type'] );
                        $this->session->set( "name", $select_result[0]['name'] );
                        $this->session->set( "username", $select_result[0]['username'] );
                        $this->session->set( "email", $select_result[0]['email'] );
                        $this->session->set( "company_id", $select_result[0]['company_id'] );
                        $this->session->set( "city_id", $select_result[0]['login_city'] );
                        $this->session->set( "state_id", $select_result[0]['login_state'] );
                        $this->session->set( "country_id", $select_result[0]['login_country'] );
                        $this->session->set( "timezone", $selected_time_zone );
                        $usrid = $this->session->get( 'userid' );
                        $id    = $usrid;
                        if(isset($form_values['remember_me'])) {
							setcookie( "company_email",$select_result[0]['email'],time() + (86400 * 30) );
							setcookie( "company_password",$form_values['password'],time() + (86400 * 30) );
						}
                        Message::success( __( 'succesful_login_flash' ) );
                        $this->urlredirect->redirect( 'company/login' );
                    }
                } else {
                    Message::error( __( 'login_failure' ) );
                }
            } else {
                $errors = $validate->errors( 'errors' );
            }
        }
        $this->template->page_title = __( 'comapny_login_title' );
        $view                       = View::factory( COMPANYVIEW . 'login' )->bind( 'validate', $validate )->bind( 'form_values', $form_values )->bind( 'errors', $errors );
        $this->template->content    = $view;
    }
    public function action_dashboard()
    {
        $this->is_login();
        if ( isset( $_SESSION['user_type'] ) ) {
            $usertype = $_SESSION['user_type'];
            if ( $usertype == 'A' ) {
                $this->urlredirect->redirect( 'admin/dashboard' );
            }
            if ( $usertype == 'M' ) {
                $this->urlredirect->redirect( 'manager/dashboard' );
            }
        } else {
            $this->urlredirect->redirect( 'company/login' );
        }
        $post_values                = array();
        $company_id                 = $_SESSION["company_id"];
        $admin                      = Model::factory( 'admin' );
        $get_all_company            = $admin->get_company_details();
        $driver_list                = $admin->get_driver_list( $company_id );
        $this->selected_page_title  = __( "dashboard" );
        $view                       = View::factory( 'admin/dashboard_new' )->bind( "driver_list", $driver_list )->bind( "getAllCompany", $get_all_company );
        $this->template->title      = SITENAME . " | " . __( 'dashboard' );
        $this->template->page_title = __( 'dashboard' );
        $this->template->content    = $view;
    }
    /**
     *****action_editprofile()****
     * @return admin edit profile
     */
    public function action_editprofile()
    {
        $this->is_login();
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'A' ) {
            $this->urlredirect->redirect( 'admin/dashboard' );
        }
        if ( $usertype == 'M' ) {
            $this->urlredirect->redirect( 'manager/dashboard' );
        }
        $userid = $_SESSION['userid'];
        $uid    = $this->request->param( 'id' );
        if ( $uid != $userid ) {
            Message::error( __( 'invalid_access' ) );
            $this->urlredirect->redirect( 'company/dashboard' );
        }
        $edit_model      = Model::factory( 'edit' );
        $add_model       = Model::factory( 'add' );
        $Company_details = $edit_model->company_details( $uid );
        $country_details = $add_model->country_details();
        $city_details    = $add_model->city_details();
        $state_details   = $add_model->state_details();
        $currencysymbol  = $this->currencysymbol;
        $signup_submit   = arr::get( $_REQUEST, 'submit_addcompany' );
        $errors          = array();
        $post_values     = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $post_values['company_image'] = $_FILES['company_image'];
            $validator   = $edit_model->validate_editcompany( arr::extract( $post_values, array(
                 'firstname',
                'lastname',
                'email',
                'phone',
                'address',
                'company_name',
                'company_address',
                'waiting_time',
                'country',
                'state',
                'city',
                'currency_code',
                'currency_symbol',
                'time_zone' ,
                'company_image'
            ) ), $uid );
            if ( $validator->check() ) {
                $status = $edit_model->editcompany( $uid, $post_values, $_FILES );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_company' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "company/dashboard" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $get_company_payment_settings = $edit_model->get_company_payment_settings( $uid );
        //send data to view file 
        $view                         = View::factory( ADMINVIEW . 'edit_company' )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'country_details', $country_details )->bind( 'city_details', $city_details )->bind( 'state_details', $state_details )->bind( 'currency_symbol', $currencysymbol )->bind( 'company_details', $Company_details )->bind( 'get_company_payment_settings', $get_company_payment_settings );
        $this->template->title        = __( 'Edit Profile' );
        $this->template->page_title   = __( 'Edit Profile' );
        $this->template->content      = $view;
    }
    /**
     *****action_changepassword()****
     * @return admin change password
     */
    public function action_changepassword()
    {
        $this->is_login();
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'A' ) {
            $this->urlredirect->redirect( 'admin/dashboard' );
        }
        if ( $usertype == 'M' ) {
            $this->urlredirect->redirect( 'manager/dashboard' );
        }
        $errors         = array();
        $changepassword = arr::get( $_REQUEST, 'submit_changepassword' );
        /**To get current logged user id from session**/
        $usrid          = $this->session->get( 'userid' );
        $id             = $usrid;
        if ( isset( $changepassword ) && Validation::factory( $_POST ) ) {
            $postvalue = $_POST;
            $post      = Arr::map( 'trim', $this->request->post() );
            $validator = $this->authorize->changepassword_validate( arr::extract( $post, array(
                 'oldpassword',
                'password',
                'repassword' 
            ) ), $id );
            if ( $validator->check() ) {
                $update            = $this->authorize->changepassword( $post['password'], $this->userid );
                $mail              = "";
                $replace_variables = array(
                    REPLACE_LOGO => EMAILTEMPLATELOGO,
                    REPLACE_SITENAME => $this->app_name,
                    REPLACE_USERNAME => ucfirst( $update[0]['name'] ),
                    REPLACE_EMAIL => $update[0]['email'],
                    REPLACE_PASSWORD => $post['password'],
                    REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                    REPLACE_SITEEMAIL => $this->siteemail,
                    REPLACE_SITEURL => URL_BASE,
                    REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                    REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
                );
                $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'changepassword.html', $replace_variables );
                $to                = $update[0]['email'];
                $from              = $this->siteemail;
                $subject           = __( 'reset_password_label' ) . " - " . $this->app_name;
                $redirect          = "company/changepassword";
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
                Message::success( __( 'sucessful_change_password' ) );
                $this->request->redirect( "company/changepassword" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                             = View::factory( ADMINVIEW . 'authorize/changepassword' )->bind( 'errors', $errors )->bind( 'postvalue', $postvalue );
        $this->template->content          = $view;
        $this->template->meta_description = SITENAME . " | Admin ";
        $this->template->meta_keywords    = SITENAME . " | Admin ";
        $this->template->title            = SITENAME . " | " . __( 'changepassword_label' );
        $this->template->page_title       = __( 'changepassword_label' );
    }
    /**
     *****action_logout()****
     * @return admin logout from site
     */
    public function action_logout()
    {
        $this->is_login();
        $tdispatch_model = Model::factory( 'tdispatch' );
        if ( isset( $_SESSION['company_id'] ) && isset( $_SESSION['userid'] ) ) {
            $company_id     = $_SESSION['company_id'];
            $user_createdby = $_SESSION['userid'];
            $name           = $_SESSION['name'];
        }
        $this->session->destroy();
        Cookie::delete( 'userid' );
        $this->request->redirect( "/company/login" );
    }
    public function action_forgot_password()
    {
        $errors         = array();
        $forgotpassword = arr::get( $_REQUEST, 'submit_forgot_password_admin' );
        if ( isset( $forgotpassword ) && Validation::factory( $_POST ) ) {
            $postvalue = $_POST;
            $post      = Arr::map( 'trim', $this->request->post() );
            $validator = $this->authorize->forgotpassword_companyvalidate( arr::extract( $post, array(
                 'email' 
            ) ) );
            if ( $validator->check() ) {
                $user_detail = $this->authorize->select_users_byemail( $post['email'], "" );
                $password    = Text::random();
                $this->authorize->changepassword( $password, $user_detail[0]['id'] );
                $mail              = "";
                $replace_variables = array(
                     REPLACE_LOGO => EMAILTEMPLATELOGO,
                    REPLACE_SITENAME => $this->app_name,
                    REPLACE_USERNAME => ucfirst( $user_detail[0]['name'] ),
                    REPLACE_EMAIL => $post['email'],
                    REPLACE_PASSWORD => $password,
                    REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                    REPLACE_SITEEMAIL => $this->siteemail,
                    REPLACE_SITEURL => URL_BASE,
                    REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                    REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
                );
                $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'forgotpassword.html', $replace_variables );
                $to                = $post['email'];
                $from              = $this->siteemail;
                $subject           = __( 'forgot_password_label' ) . " - " . $this->app_name;
                $redirect          = "company/login";
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
                Message::success( __( 'sucessful_forgot_password' ) );
                $this->request->redirect( "company/login" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                             = View::factory( COMPANYVIEW . 'forgot_password' )->bind( 'errors', $errors )->bind( 'postvalue', $postvalue );
        $this->template->content          = $view;
        $this->template->meta_description = SITENAME . " | Admin ";
        $this->template->meta_keywords    = SITENAME . " | Admin ";
        $this->template->title            = SITENAME . " | " . __( 'forgot_password' );
        $this->template->page_title       = __( 'forgot_password' );
    }
    /** Manage Site Settings **/
    public function action_company_setting()
    {
        $this->is_login();
        $manage_model = Model::factory( 'manage' );
        $usertype     = $_SESSION['user_type'];
        if ( $usertype == 'A' || $usertype == 'C' ) {
        } else {
            $this->request->redirect( "company/login" );
        }
        $cid           = $_SESSION['company_id'];
        $settings      = Model::factory( 'company' );
        $errors        = array();
        $signup_submit = arr::get( $_REQUEST, 'editsettings_submit' );
        $errors        = array();
        $post_values   = array();
        if ( $signup_submit && Validation::factory( $_POST, $_FILES ) ) {
            $post_values = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->post() ) );
            $form_values = Arr::extract( $post_values, array(
				'company_copyrights',
                'fare_calculation',
                'default_unit',
                'skip_credit_card',
                'cancellation_fare',
                'company_tax',
                'date_time_format',
                'user_time_zone',
                'driver_commission' 
            ) );
            $file_values = Arr::extract( $_FILES, array(
                 'company_logo',
                'email_site_logo',
                'company_favicon' 
            ) );
            $values      = Arr::merge( $form_values, $file_values );
            $validator   = $settings->validate_updatesiteinfo( $values );
            if ( $validator->check() ) {
                $status = $settings->updatesiteinfo( $post_values, $cid );
                $this->session->set( "timezone", $post_values['user_time_zone'] );
                $res_com_info   = $settings->get_company_info( $cid );
                $company_domain = isset( $res_com_info[0]['company_domain'] ) ? $res_com_info[0]['company_domain'] : '';
                if ( !empty( $_FILES['company_logo']['name'] ) ) {
                    $image_name = $cid . '_logo.png';
                    $image_type = explode( '.', $image_name );
                    $image_type = end( $image_type );
                    $filename   = Upload::save( $_FILES['company_logo'], $image_name, DOCROOT . SITE_LOGO_IMGPATH );
                    //Image resize and crop for thumb image
                    $logo_image = Image::factory( $filename );
                    $path11     = DOCROOT . SITE_LOGO_IMGPATH;
                    $path1      = $image_name;
                    Commonfunction::logoresize( $logo_image, SITE_LOGO_WIDTH, SITE_LOGO_HEIGHT, $path11, $image_name, 90 );
                    $status = $settings->updatesiteinfo_image( $path1, $cid );
                }
                if ( !empty( $_FILES['email_site_logo']['name'] ) ) {
                    $image_name = $cid . '_email_logo.png';
                    $image_type = explode( '.', $image_name );
                    $image_type = end( $image_type );
                    $filename   = Upload::save( $_FILES['email_site_logo'], $image_name, DOCROOT . SITE_LOGO_IMGPATH );
                    //Image resize and crop for thumb image
                    $logo_image = Image::factory( $filename );
                    $path11     = DOCROOT . SITE_LOGO_IMGPATH;
                    $path1      = $image_name;
                    Commonfunction::imageresize( $logo_image, 175, 35, $path11, $image_name, 90 );
                }
                if ( !empty( $_FILES['company_favicon']['name'] ) ) {
                    if ( !empty( $favicon_old ) && file_exists( DOCROOT . SITE_FAVICON_IMGPATH . $favicon_old ) ) {
                        unlink( DOCROOT . SITE_FAVICON_IMGPATH . $favicon_old );
                    }
                    $image_name = 'fav_' . $company_domain;
                    $image_type = explode( '.', $image_name );
                    $image_type = end( $image_type );
                    $filename   = Upload::save( $_FILES['company_favicon'], $image_name, DOCROOT . SITE_FAVICON_IMGPATH );
                    //Image resize and crop for thumb image
                    $logo_image = Image::factory( $filename );
                    $path11     = DOCROOT . SITE_FAVICON_IMGPATH;
                    $path1      = $image_name;
                    Commonfunction::imageresize( $logo_image, FAVICON_WIDTH, FAVICON_HEIGHT, $path11, $image_name, 90 );
                    $status = $settings->updatesiteinfo_faviconimage( $path1, $cid );
                }
                Message::success( __( 'sucessful_settings_update' ) );
                $this->request->redirect( "company/company_setting" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $currencysymbol             = $this->currencysymbol;
        $site_settings              = $settings->site_settings( $cid );
        //~ echo '<pre>';print_r($site_settings);exit;
        # set 0 for null values
        $site_settings[0]['company_tax'] = (isset($site_settings[0]['company_tax']) && (trim($site_settings[0]['company_tax']) != null)) ? $site_settings[0]['company_tax']:0;
        $site_settings[0]['driver_commission'] = (isset($site_settings[0]['driver_commission']) && (trim($site_settings[0]['driver_commission']) != null)) ? $site_settings[0]['driver_commission']:0;
        $site_settings[0]['cancellation_fare'] = (isset($site_settings[0]['cancellation_fare']) && (trim($site_settings[0]['cancellation_fare']) != null)) ? $site_settings[0]['cancellation_fare']:0;
        $site_settings[0]['skip_credit_card'] = (isset($site_settings[0]['skip_credit_card']) && (trim($site_settings[0]['skip_credit_card']) != null)) ? $site_settings[0]['skip_credit_card']:0;
        $site_settings[0]['date_time_format'] = (isset($site_settings[0]['date_time_format']) && (trim($site_settings[0]['date_time_format']) != null)) ? $site_settings[0]['date_time_format']:'Y-m-d H:i:s';
        
        
        $package_details            = $manage_model->current_package_details( $cid );
        $company_timezone           = ( isset( $site_settings[0]['user_time_zone'] ) && $site_settings[0]['user_time_zone'] != "" ) ? $site_settings[0]['user_time_zone'] : TIMEZONE;
        $this->selected_page_title  = __( "company_setting" );
        $view                       = View::factory( 'admin/add_settings_company' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'site_settings', $site_settings )->bind( 'package_details', $package_details )->bind( 'company_timezone', $company_timezone )->bind( 'currency_symbol', $currencysymbol );
        $this->template->title      = SITENAME . " | " . __( 'company_setting' );
        $this->template->page_title = __( 'company_setting' );
        $this->template->content    = $view;
    }
    //List Passengers those are registered in Company
    /** passenger list **/
    public function action_passengers()
    {
        $this->is_login();
        //Page Title
        $this->page_title           = __( 'menu_manage_passengers' );
        $this->selected_page_title  = __( 'menu_manage_passengers' );
        $cid                        = $_SESSION['company_id'];
        $usertype                   = $_SESSION['user_type'];
        $id                         = $this->session->get( 'id' );
        $userid                     = $this->session->get( 'userid' );
        $usrid                      = isset( $userid ) ? $userid : $id;
        $this->template->title      = __( 'menu_user_list' );
        $this->template->page_title = __( 'menu_user_list' );
        //import model
        $company                    = Model::factory( 'company' );
        $manage_company             = Model::factory( 'manage' );
        $count_user_list            = $company->count_passenger_list_history( $cid );
        $all_list                   = $count_user_list;
        $count_user_list            = count( $count_user_list );
        //pagination loads here
        //-------------------------
        $page_no                    = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_user_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_user_list              = $company->all_passenger_list_history( $cid, $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/company_passengers_list' )->bind( 'title', $title )->bind( 'details', $details )->bind( 'all_user_list', $all_user_list )->bind( 'pag_data', $pag_data )->bind( 'UserList', $UserList )->bind( 'all_list', $all_list )->bind( 'srch', $_POST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | Passengers List";
        $this->template->page_title = "Passengers List";
        $this->template->content    = $view;
    }
    /** passengers list **/
    public function action_company_passenger_search()
    {
        $this->is_login();
        $this->session->get( 'userid' );
        //Page Title
        $this->page_title          = __( 'menu_manage_passengers' );
        $this->selected_page_title = __( 'menu_manage_passengers' );
        $cid                       = $_SESSION['company_id'];
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $company                   = Model::factory( 'company' );
        $count_user_list           = $company->count_passengersearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $cid );
        $all_list                  = $count_user_list;
        $count_user_list           = count( $count_user_list );
        if ( isset( $_SESSION['download_set'] ) ) {
            if ( count( $all_list ) > 0 ) {
                foreach ( $all_list as $key => $value ) {
                    if ( array_key_exists( 'created_date', $value ) ) {
                        $date = Commonfunction::getDateTimeFormat( $value['created_date'], 1 );
                        unset( $value['created_date'] );
                        $all_list[$key]['created_date'] = $date;
                    }
                }
            }
            $export_table_header       = array(
                 __( 'name' ),
                __( 'email_label' ),
                __( 'phone_label' ),
                __( 'address' ),
                __( 'referral_code' ),
                __( 'wallet_amount' ),
                __( 'created_date' ),
                __( 'Status' ) 
            );
            $export_table_field_select = array(
                 'name',
                'email',
                array(
                     'field' => array(
                         'country_code',
                        'phone' 
                    ),
                    'symbol' => '-' 
                ),
                'address',
                'referral_code',
                'wallet_amount',
                'created_date',
                'user_status' 
            );
            $heading                   = 'Passengerlist';
            $this->action_create_the_document( $all_list, $export_table_header, $export_table_field_select, $heading );
        }
        $manage_company = Model::factory( 'manage' );
        //pagination loads here
        //-------------------------
        $page_no        = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_user_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_user_list = $company->get_all_searchpassenger_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $cid, $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/company_passengers_list' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_user_list', $all_user_list )->bind( 'all_list', $all_list );
        $this->template->content = $view;
    }
    /**  delete the passengers **/
    /** block passenger list**/
    public function action_block_passenger_request()
    {
        $this->is_login();
        $site     = Model::factory( 'site' );
        $passDets = $site->block_passenger_request( $_REQUEST['uniqueId'] );
        if ( count( $passDets ) > 0 ) {
            //Function to send mail or sms to blocked passengers
            $this->sendMailSmspassengers( $passDets[0]['passEmails'], $passDets[0]['passMobiles'], 'blocked' );
        }
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    /** actvie passenger list**/
    public function action_active_passenger_request()
    {
        $this->is_login();
        $site     = Model::factory( 'site' );
        $passDets = $site->active_passenger_request( $_REQUEST['uniqueId'] );
        if ( count( $passDets ) > 0 ) {
            //Function to send mail or sms to blocked passengers
            $this->sendMailSmspassengers( $passDets[0]['passEmails'], $passDets[0]['passMobiles'], 'activated' );
        }
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    /** trash passenger list**/
    public function action_trash_passenger_request()
    {
        $this->is_login();
        $site     = Model::factory( 'site' );
        $passDets = $site->trash_passenger_request( $_REQUEST['uniqueId'] );
        if ( count( $passDets ) > 0 ) {
            //Function to send mail or sms to blocked passengers
            $this->sendMailSmspassengers( $passDets[0]['passEmails'], $passDets[0]['passMobiles'], 'deleted' );
        }
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been moved to the Trash..' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    /** delete passenger list**/
    public function action_delete_passenger_request()
    {
        $this->is_login();
        $site     = Model::factory( 'site' );
        $status   = $site->delete_passenger_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been deleted successfully' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "company/passengers" );
    }
    public function is_login()
    {
        $session = Session::instance();
        //get current url and set it into session
        //========================================
        $this->session->set( 'requested_url', Request::detect_uri() );
        /**To check Whether the user is logged in or not**/
        if ( !isset( $this->session ) || ( !$this->session->get( 'userid' ) ) ) // && !$this->session->get('id')        
            {
            Message::error( __( 'login_access' ) );
            $this->request->redirect( "/company/login/" );
        }
        return;
    }
    public function action_getcitylist()
    {
        $company    = Model::factory( 'company' );
        $country_id = arr::get( $_REQUEST, 'country_id' );
        $output     = '';
        $city       = $company->get_city( $country_id );
        $count      = count( $city );
        if ( $count > 0 ) {
            $output .= '<select name="city" id="city"  style="color:rgb(163, 159, 159) ! important;" title="' . __( 'select_the_city' ) . '" >
                       <option value="">--Select--</option>';
            foreach ( $city as $modellist ) {
                $output .= '<option value="' . $modellist["city_id"] . '"';
                $output .= '>' . $modellist["city_name"] . '</option>';
            }
            $output .= '</select>';
        } else {
            $output .= '<select name="city" id="city"  style="color:rgb(163, 159, 159) ! important;" title="' . __( 'select_the_city' ) . '">
                   <option value="">--Select--</option></select>';
        }
        echo $output;
        exit;
    }
    /*This function is used to create the Free Trial version for the new users. This will create the company, Company Drivers,
     *  Company Taxi and model fare details*/
    public function action_getfreetrial()
    {
        $company     = Model::factory( 'company' );
        $post_values = $_POST;
        if ( $post_values['time_zone'] ) {
            //This will convert the time to requested TIMEZONE
            $current_time = convert_timezone( 'now', $post_values['time_zone'] );
        } else {
            $current_time = date( 'Y-m-d H:i:s' );
        }
        $post               = Arr::map( 'trim', $this->request->post() );
        $post['createdate'] = $current_time;
        $companyemail       = $post['g_email'];
        // Check whether the company email exist or not. We need to pass email to this function
        $check_email_exist  = $company->checkemail( $companyemail );
        if ( $check_email_exist == 0 ) {
            echo '2';
            exit;
        }
        // Check whether the company domain exist or not. We need to pass domain name to this function. Domain name used as a Subdomain in the URL
        $add_model          = Model::factory( 'add' );
        $check_domain_exist = $add_model->checkcompanydomain( $post_values["domain_name"] );
        if ( $check_domain_exist == 1 ) {
            echo '3';
            exit;
        }
        /**********************************************/
        // Pass all values to model to save the free trial data. Once company created then all other information will be related to company will be created
        $budget          = isset( $_POST['budget'] ) ? $_POST['budget'] : '-';
        $ip              = $_SERVER['REMOTE_ADDR'];
        // Get city and country details
        $url             = "http://api.ipinfodb.com/v3/ip-country/?key=" . IPINFOAPI_KEY . "&ip=$ip";
        $data            = @file_get_contents( $url );
        $dat             = explode( ";", $data );
        $city_name       = isset( $dat[2] ) ? $dat[2] : "";
        $country_name    = isset( $dat[3] ) ? $dat[3] : "";
        $post['city']    = isset( $dat[2] ) ? $dat[2] : "";
        $post['country'] = isset( $dat[3] ) ? $dat[3] : "";
        $message1        = " (FREE TRAIL REQUEST)  Company : " . $_POST['company_name'] . "  |  Message : " . $_POST['message'] . "   |   No. of Taxi : " . $_POST['no_of_taxi'] . "   |   Country : " . $country_name . "   |   IP Address : " . $ip;
        $save_free_trial = $company->save_free_trial( $post );
        if ( count( $save_free_trial ) > 0 ) {
            $mail              = "";
            $replace_variables = array(
                 REPLACE_LOGO => EMAILTEMPLATELOGO,
                REPLACE_SITENAME => $this->app_name,
                REPLACE_EMAIL => $post['g_email'],
                REPLACE_NAME => $post['g_name'],
                REPLACE_PHONE => $post['g_phone'],
                REPLACE_COMPANY => $post['company_name'],
                REPLACE_NOOFTAXI => $post['no_of_taxi'],
                REPLACE_COUNTRY => $country_name,
                REPLACE_CITY => $city_name,
                MESSAGE => $message1,
                REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                REPLACE_SITEEMAIL => $this->siteemail,
                REPLACE_SITEURL => URL_BASE,
                REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
            );
            $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'get_free_quotes.html', $replace_variables );
            $to                = 'maheswaran.r@ndot.in,venkatesan@ndot.in';
            $from              = $this->siteemail;
            $subject           = __( 'get_free_quotes_details' ) . " - " . $this->app_name;
            $redirect          = 'users/index';
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
            /* CURL FUNCTION FOR Place the data to NDOT CRM */
            $_POST['name']        = $_POST['g_name'];
            $_POST['email']       = $_POST['g_email'];
            $_POST['telephone']   = $_POST['g_phone'];
            $_POST['category']    = "220";
            $_POST['site']        = "ndot";
            $_POST['success_url'] = "http://www.taximobility.com";
            $_POST['country']     = $country_name;
            $_POST['source_type'] = "22";
            $_POST['feedback']    = $message1;
            $data                 = $_POST;
            //url-ify the data for the POST
            $fields_string        = '';
            foreach ( $data as $key => $value ) {
                $fields_string .= $key . '=' . $value . '&';
            }
            $fields_string = rtrim( $fields_string, '&' );
            $url           = "http://ndot.engagedots.com/api/contactUs";
            $ch            = curl_init(); //open connection
            curl_setopt( $ch, CURLOPT_URL, $url ); //set the url, number of POST vars, POST data
            curl_setopt( $ch, CURLOPT_POST, count( $data ) );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            $result = curl_exec( $ch ); //execute post
            curl_close( $ch ); //close connection
            /* CURL FUNCTION FOR CONTACT CRM */
            /********** Auto company creation ***************/
            $signup_id = $company->addcompanydetails( $post_values );
            if ( $signup_id == 1 ) {
                $mail                    = "";
                $domain                  = trim( $post['domain_name'] );
                $passengerdriver_details = "";
                $passengerdriver_details .= "<p style='color: #333'>Passenger Details</p>";
                $passengerdriver_details .= '<table cellspacing="0" cellpadding="0" width="300" style="font-size: 14px; line-height: 20px; font-family: "Lucida Grande",Arial,sans-serif;font-color: #000; border="1" ">
                    <tr>
                        <td><b>#</b></td>
                        <td><b>Username(Mobile Number)</b></td>
                        <td><b>Password</b></td>
                    </tr>';
                // Dynmaic Passenger login details 
                for ( $i = 1; $i < 3; $i++ ) {
                    $passengerdriver_details .= '<tr>
                        <td>' . $i . '</td>
                        <td>' . $domain . $i . '</td>
                        <td>qwerty</td>
                    </tr>';
                }
                $passengerdriver_details .= '</table>';
                $passengerdriver_details .= '<img src="##SITEURL##public/common/images/email_temp_spacer-header.jpg" width="520px" height="20px" alt="##SITENAME##"/>';
                $passengerdriver_details .= '<p style="color: #333;">Driver Details</p>';
                $passengerdriver_details .= '<table  cellspacing="0" cellpadding="0" width="300" style="font-size: 14px; line-height: 20px; font-family: "Lucida Grande",Arial,sans-serif;font-color: #000; border="1" ">
                    <tr>
                        <td><b>#</b></td>
                        <td><b>Username(Mobile Number)</b></td>
                        <td><b>Password</b></td>
                    </tr>';
                // Dynmaic driver login details 
                for ( $i = 1; $i < 3; $i++ ) {
                    $passengerdriver_details .= '<tr>
                        <td>' . $i . '</td>
                        <td>' . $domain . $i . '</td>
                        <td>qwerty</td>
                    </tr>';
                }
                $passengerdriver_details .= '</table>';
                $passengerdriver_details .= '<img src="##SITEURL##public/common/images/email_temp_spacer-header.jpg" width="520px" height="20px" alt="##SITENAME##"/>';
                $replace_variables = array(
                     REPLACE_LOGO => EMAILTEMPLATELOGO,
                    REPLACE_SITENAME => $this->app_name,
                    REPLACE_USERNAME => $_POST['g_name'],
                    REPLACE_EMAIL => $_POST['g_email'],
                    REPLACE_PASSWORD => 'qwerty',
                    REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                    REPLACE_SITEEMAIL => $this->siteemail,
                    MESSAGE => $passengerdriver_details,
                    REPLACE_SITEURL => URL_BASE,
                    REPLACE_COMPANYDOMAIN => $domain,
                    REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                    REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
                );
                // Place the content to Email templete 
                $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'autotrail_company_registration.html', $replace_variables );
                $to                = $_POST['g_email'];
                $from              = $this->siteemail;
                $subject           = __( 'registration_success' ); //Language string for internationalization
                $redirect          = 'no';
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
                echo '1';
                exit;
            }
        }
    }
    public function action_sms_settings()
    {
        $this->is_login();
        $usertype = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "admin/login" );
        }
        $settings              = Model::factory( 'company' );
        $errors                = array();
        $socialsettings_submit = arr::get( $_REQUEST, 'editsms_settings_submit' );
        $errors                = array();
        $post_values           = array();
        $company_id            = $_SESSION['company_id'];
        $sms_id                = '';
        $smssettings           = $settings->sms_settings( $company_id );
        if ( empty( $smssettings ) ) {
            $smssettings[0]['sms_account_id']  = '';
            $smssettings[0]['sms_auth_token']  = '';
            $smssettings[0]['sms_from_number'] = '';
            $smssettings[0]['sms_id']          = '';
        }
        $sms_id = $smssettings[0]['sms_id'];
        if ( $socialsettings_submit && Validation::factory( $_POST ) ) {
            $post_values = $_POST;
            $validator   = $settings->validate_update_smssettings( arr::extract( $_POST, array(
                 'sms_account_id',
                'sms_auth_token',
                'sms_from_number' 
            ) ) );
            if ( $validator->check() ) {
                $status = $settings->update_sms_settings( $_POST, $company_id, $sms_id );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessful_settings_update' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "company/sms_settings" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $this->selected_page_title  = __( "sms_settings" );
        $view                       = View::factory( 'admin/sms_settings' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'smssettings', $smssettings );
        $this->template->title      = SITENAME . " | " . __( 'sms_settings' );
        $this->template->page_title = __( 'sms_settings' );
        $this->template->content    = $view;
    }
    public function action_checkemail()
    {
        $company_model      = Model::factory( 'company' );
        $check_domain_exist = $company_model->checkemail( $_REQUEST["type"] );
        if ( $check_domain_exist == 0 ) {
            echo '<span style="color:red;">' . __( 'emailexists' ) . '</span>';
            exit;
        } else {
            echo "";
            exit;
        }
    }
    public function action_checkdomain()
    {
        $add_model          = Model::factory( 'add' );
        $check_domain_exist = $add_model->checkcompanydomain( $_REQUEST["type"] );
        if ( $check_domain_exist == 0 ) {
            echo '<span style="color:green;">' . __( 'company_domain_is_avaliable' ) . '</span>';
            exit;
        } else {
            echo '<span style="color:red;">' . __( 'company_domain_is_exist' ) . '</span>';
            exit;
        }
    }
    /**************Dashboard Trip Details Chart***************/
    public function action_get_company_trip_count()
    {
        $this->auto_render = false;
        $model_dashboard   = Model::factory( 'company' );
        $year              = date( 'Y' );
        for ( $i = 1; $i <= 12; $i++ ) {
            $count    = $model_dashboard->get_company_trip_count( $i, $year );
            $revenues = $model_dashboard->get_company_trip_revenues( $i, $year );
            if ( $revenues == '' || $revenues == 'NULL' ) {
                $revenues = "0";
            } else {
                $revenues = $revenues;
            }
            if ( $count == '' || $count == 'NULL' ) {
                $count = "0";
            } else {
                $count = $count;
            }
            if ( $revenues != 0 && $count != 0 ) {
                $average = $count / $revenues;
            } else {
                $average = "0";
            }
            $data['trips'][] = array(
                 'trips' => $count,
                'revenues' => $revenues,
                'average' => round( $average, 3 ) 
            );
        }
        $json            = array();
        $json['success'] = $data;
        echo json_encode( $json );
    }
    public function action_total_trip_details_search()
    {
        $post_values     = $_POST;
        $startdate       = Commonfunction::ensureDatabaseFormat( $post_values['startdate'], 1 );
        $enddate         = Commonfunction::ensureDatabaseFormat( $post_values['enddate'], 2 );
        $model_dashboard = Model::factory( 'company' );
        $get_transaction = $model_dashboard->total_trip_details( $startdate, $enddate );
        $view            = View::factory( 'company/total_trip_revenue' )->bind( 'post_values', $post_values )->bind( 'get_transaction', $get_transaction );
        echo $view;
        exit;
    }
    /**************Dashboard Trip Details Chart***************/
    /** Account Report **/
    //Admin Transactions without Search action 
    public function action_account_reports()
    {
        $this->is_login();
        $find_url       = explode( '/', $_SERVER['REQUEST_URI'] );
        $split          = explode( '?', $find_url[2] );
        $list           = $split[0];
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $manage_transaction   = Model::factory( 'transaction' );
        $common_model         = Model::factory( 'commonmodel' );
        $page_title           = __( "account_report" );
        $list                 = 'all';
        $company              = $_SESSION['company_id'];
        $get_allcompany       = "";
        $taxilist             = $manage_transaction->gettaxidetails( '', '', array ());
        $passengerlist        = $manage_transaction->getpassengerdetails( '', '', array ());
        $driverlist           = $manage_transaction->getdriverdetails( '', '', array ());
        $managerlist          = $manage_transaction->getmanagerdetails( '' );
        $startdate            = date( 'Y-m-d 00:00:00', strtotime( '-7 days' ) );
        $enddate              = date( 'Y-m-d H:i:s' );
        $all_transaction_list = $manage_transaction->accountreport_details( $list, $company, $startdate, $enddate, 'All' );
        $grpahdata            = $manage_transaction->getaccountreportvalues( $list, $company, $startdate, $enddate, '' );
        $gateway_details      = $common_model->gateway_details();
        //****pagination ends here***//
        if ( ( $company != "" ) && ( $company != "All" ) && $company != 0 ) {
            $total_amount = $manage_transaction->get_company_commission_amount( $company, $startdate, $enddate, 'All' );
        } else {
            $total_amount = $manage_transaction->accountreport_details_payment( $company, $startdate, $enddate, 'All' );
        }
        //send data to view file 
        $view                       = View::factory( 'admin/company_account_report' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'taxilist', $taxilist )->bind( 'driverlist', $driverlist )->bind( 'managerlist', $managerlist )->bind( 'passengerlist', $passengerlist )->bind( 'get_allcompany', $get_allcompany )->bind( 'grpahdata', $grpahdata )->bind( 'gateway_details', $gateway_details )->bind( 'id', $id )->bind( 'total_amount', $total_amount );
        $this->page_title           = $page_title;
        $this->template->title      = $page_title . " | " . SITENAME;
        $this->template->page_title = $page_title;
        $this->template->content    = $view;
    }
    public function action_account_report_lists()
    {
        $this->is_login();
        $find_url       = explode( '/', $_SERVER['REQUEST_URI'] );
        $split          = explode( '?', $find_url[2] );
        $list           = $split[0];
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $page_title           = __( "account_report" );
        $list                 = 'all';
        $company              = trim( Html::chars( $_REQUEST['filter_company'] ) );
        $startdate            = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['startdate'] ) ), 1 );
        $enddate              = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['enddate'] ) ), 1 );
        $payment_type         = trim( Html::chars( $_REQUEST['payment_type'] ) );
        $manage_transaction   = Model::factory( 'transaction' );
        $common_model         = Model::factory( 'commonmodel' );
        $get_allcompany       = $manage_transaction->get_allcompany_tranaction();
        $managerlist          = $manage_transaction->getmanagerdetails( $company );
        $all_transaction_list = $manage_transaction->accountreport_details( $list, $company, $startdate, $enddate, $payment_type );
        $grpahdata            = $manage_transaction->getaccountreportvalues( $list, $company, $startdate, $enddate, $payment_type );
        $gateway_details      = $common_model->gateway_details();
        //****pagination ends here***//
        if ( ( $company != "" ) && ( $company != "All" ) && $company != 0 ) {
            $total_amount = $manage_transaction->get_company_commission_amount( $company, $startdate, $enddate, $payment_type );
        } else {
            $total_amount = $manage_transaction->accountreport_details_payment( $company, $startdate, $enddate, $payment_type );
        }
        //send data to view file 
        $view                       = View::factory( 'admin/company_account_report' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'taxilist', $taxilist )->bind( 'driverlist', $driverlist )->bind( 'managerlist', $managerlist )->bind( 'passengerlist', $passengerlist )->bind( 'get_allcompany', $get_allcompany )->bind( 'grpahdata', $grpahdata )->bind( 'gateway_details', $gateway_details )->bind( 'id', $id )->bind( 'total_amount', $total_amount );
        $this->page_title           = $page_title;
        $this->template->title      = $page_title . " | " . SITENAME;
        $this->template->page_title = $page_title;
        $this->template->content    = $view;
    }
    
    public function action_payment_gateway_module()
    {
        $this->is_login();
        $usertype    = $_SESSION['user_type'];
        $company     = Model::factory( 'company' );
        $update_post = arr::get( $_REQUEST, 'update' );
        $post        = array();
        if ( $update_post ) {
            $post = $_REQUEST;
            if ( isset( $post['default_payment'] ) ) {
                $id                     = $post['default_payment'];
                $update_default_country = $company->update_default_payment( $id );
                if ( $update_default_country == 1 ) {
                    Message::success( __( 'changed_default_payment' ) );
                    $this->request->redirect( "company/payment_gateway_module" );
                } else if ( $update_default_country == '-1' ) {
                    Message::error( __( 'select_the_activepayment' ) );
                    $this->request->redirect( "company/payment_gateway_module" );
                } else {
                    Message::error( __( 'select_the_defaultpayment' ) );
                    $this->request->redirect( "company/payment_gateway_module" );
                }
            } else {
                Message::error( __( 'not_updated' ) );
                $this->request->redirect( "admin/payment_gateway_module" );
            }
        }
        $payment_settings           = $company->get_payment_gateways();
        $this->selected_page_title  = __( "site_settings" );
        $view                       = View::factory( 'admin/manage_company_payment_module' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'pag_data', $pag_data )->bind( 'payment_settings', $payment_settings )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'payment_gateway_setting' );
        $this->template->page_title = __( 'payment_gateway_setting' );
        $this->template->content    = $view;
    }
}
