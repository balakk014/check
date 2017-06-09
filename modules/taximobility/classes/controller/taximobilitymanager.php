<?php defined( 'SYSPATH' ) OR die( 'No Direct Script Access' );
/******************************************
* Contains Manager Module details
* @Package: Taximobility
* @Author: Taxi Team
* @URL : taximobility.com
********************************************/
class Controller_TaximobilityManager extends Controller_Siteadmin
{
    public function action_index()
    {
        $this->urlredirect->redirect( 'manager/login' );
        $this->template->meta_desc     = $this->metadescription;
        $this->template->meta_keywords = $this->metakeywords;
    }
    public function action_login()
    {
        if ( $this->userid ) {
            $this->urlredirect->redirect( 'manager/dashboard' );
        }
        $userid      = "";
        $success_msg = "";
        $error_msg   = "";
        //condition checked to show package period expire message
        if ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] == "expire" ) {
            Message::error( __( 'check_company_owner' ) );
            $this->urlredirect->redirect( 'manager/login' );
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
                if ( ( $this->authorize->managerlogin_details( $form_values['email'], md5( $form_values['password'] ), TRUE, "" ) ) > 0 ) //,""
                    {
                    $select_result = $this->authorize->managerlogin_details( $form_values['email'], md5( $form_values['password'] ), FALSE );
                    if ( ( $select_result[0]['company_status'] == 'D' ) || ( $select_result[0]['company_status'] == 'T' ) ) {
                        Message::error( __( 'login_deactive' ) );
                    } else {
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
							setcookie( "manager_email",$select_result[0]['email'],time() + (86400 * 30) );
							setcookie( "manager_password",$form_values['password'],time() + (86400 * 30) );
						}
                        Message::success( __( 'succesful_login_flash' ) );
                        $this->urlredirect->redirect( 'manager/login' );
                    }
                } else {
                    Message::error( __( 'login_failure' ) );
                }
            } else {
                $errors = $validate->errors( 'errors' );
            }
        }
        $this->template->page_title = __( 'manager_login_title' );
        $view                       = View::factory( MANAGERVIEW . 'login' )->bind( 'validate', $validate )->bind( 'form_values', $form_values )->bind( 'errors', $errors );
        $this->template->content    = $view;
    }
    public function action_dashboard()
    {
        $this->is_login();
        $dashboard    = Model::factory( 'manager' );
        $dash         = Model::factory( 'admin' );
        $taxidispatch = Model::factory( 'taxidispatch' );
        $transaction  = Model::factory( 'transaction' );
        $company_id   = $_SESSION['company_id'];
        if ( $company_id ) {
            $company_timezone = $dashboard->get_company_timezone( $company_id );
        }
        if ( isset( $_SESSION['user_type'] ) ) {
            $usertype = $_SESSION['user_type'];
            if ( $usertype == 'A' ) {
                $this->urlredirect->redirect( 'admin/dashboard' );
            }
            if ( $usertype == 'C' ) {
                $this->urlredirect->redirect( 'company/dashboard' );
            }
            $manager_details_array = array();
            if ( $usertype == 'M' ) {
                $manager_details = $transaction->manager_details( $_SESSION['userid'] );
                if ( count( $manager_details ) > 0 ) {
                    $manager_details_array = $manager_details;
                }
            }
        } else {
            $this->urlredirect->redirect( 'manager/login' );
        }
        $post_values        = array();
        $post_values        = $_REQUEST;
        $availabletaxi_list = $dashboard->get_availabletaxi_list();
        $freedriver_list    = $dashboard->free_driver_list();
        $freetaxi_list      = $dashboard->free_taxi_list();
        $getdriverdetails   = $transaction->getdriverdetails( $_SESSION['company_id'], $_SESSION['userid'], $manager_details_array );
        $driver_id          =  $val = "";
        if ( count( $getdriverdetails ) > 0 ) {
            foreach ( $getdriverdetails as $res ) {
                $val .= '["' . $res['name'] . '",1],';
                $driver_id .= $res['id'] . ",";
            }
            $driver_id = rtrim( $driver_id, ',' );
        } else {
            $driver_id = "''";
        }
        $getUserbyCompany = rtrim( $val, ',' );
        $startdate        = date( 'Y-m-d 00:00:00' );
        $enddate          = date( 'Y-m-d H:i:s' );
        $gettransaction   = $dashboard->changegettransaction( $driver_id, $startdate, $enddate );
        $count            = "";
        $name             = "";
        foreach ( $gettransaction as $res ) {
            $name .= "'" . $res["driver_name"] . "',";
            $count .= $res["count"] . ",";
        }
        $name                 = rtrim( $name, ',' );
        $count                = rtrim( $count, ',' );
        $company_id           = $_SESSION['company_id'];
        $admin_dashboard_data = $dashboard->get_admin_dashboard_data( $company_id );
        $val                  = array();
        $all_company_map_list = $taxidispatch->driver_status_details( $val );
        if ( count( $all_company_map_list ) > 0 ) {
            foreach ( $all_company_map_list as $k => $v ) {
                $all_company_map_list[$k]["driver_info_close"] = "driver_info_close";
                if ( $v['driver_status'] == 'F' && $v['shift_status'] == 'OUT' ) {
                    $all_company_map_list[$k]["driver_status_class"] = "driver_shiftout";
                } elseif ( $v['driver_status'] == 'A' ) {
                    $all_company_map_list[$k]["driver_status_class"] = "driver_in_active";
                } else if ( $v['driver_status'] == 'B' ) {
                    $all_company_map_list[$k]["driver_status_class"] = "driver_on_trip";
                } else if ( $v['driver_status'] == 'F' && $v['shift_status'] == 'IN' ) {
                    $all_company_map_list[$k]["driver_status_class"] = "driver_available";
                }
            }
        }
        $this->selected_page_title  = __( "dashboard" );
        $view                       = View::factory( 'manager/dashboard' )->bind( 'postvalue', $post_values )->bind( 'availabletaxi_list', $availabletaxi_list )->bind( 'freetaxi_list', $freetaxi_list )->bind( 'admin_dashboard_data', $admin_dashboard_data )->bind( 'freedriver_list', $freedriver_list )->bind( 'name', $name )->bind( 'gettransaction', $gettransaction )->bind( 'count', $count )->bind( 'drivers', $driver_id )->bind( 'all_company_map_list', $all_company_map_list );
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
        if ( $usertype == 'C' ) {
            $this->urlredirect->redirect( 'company/dashboard' );
        }
        $userid = $_SESSION['userid'];
        $uid    = $this->request->param( 'id' );
        if ( $uid != $userid ) {
            Message::error( __( 'invalid_access' ) );
            $this->urlredirect->redirect( 'manager/dashboard' );
        }
        $edit_model          = Model::factory( 'edit' );
        $add_model           = Model::factory( 'add' );
        /**To get the form submit button name**/
        $signup_submit       = arr::get( $_REQUEST, 'submit_editmanager' );
        $errors              = array();
        $post_values         = array();
        $taxicompany_details = $add_model->taxicompany_details();
        $Company_details     = $edit_model->manager_details( $uid );
        $country_details     = $add_model->country_details();
        $state_details       = $add_model->state_details();
        $city_details        = $add_model->city_details();
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
                if ( $status == 1 ) {
                    Message::success( __( 'sucessfull_updated_manager' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "manager/dashboard" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                       = View::factory( 'admin/edit_manager' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'postvalue', $post_values )->bind( 'country_details', $country_details )->bind( 'city_details', $city_details )->bind( 'taxicompany_details', $taxicompany_details )->bind( 'state_details', $state_details )->bind( 'company_details', $Company_details );
        $this->template->content    = $view;
        $this->template->title      = __( 'Edit Profile' );
        $this->template->page_title = __( 'Edit Profile' );
        $this->template->content    = $view;
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
        if ( $usertype == 'C' ) {
            $this->urlredirect->redirect( 'company/dashboard' );
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
                $subject           = __( 'reset_password_label' );
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
                $this->request->redirect( "manager/changepassword" );
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
        $this->session->destroy();
        Cookie::delete( 'userid' );
        $this->request->redirect( "/manager/login" );
    }
    public function action_forgot_password()
    {
        $errors         = array();
        $forgotpassword = arr::get( $_REQUEST, 'submit_forgot_password_admin' );
        if ( isset( $forgotpassword ) && Validation::factory( $_POST ) ) {
            $postvalue = $_POST;
            $post      = Arr::map( 'trim', $this->request->post() );
            $validator = $this->authorize->forgotpassword_managervalidate( arr::extract( $post, array(
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
                $redirect          = "manage/login";
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
                $this->request->redirect( "manager/login" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                             = View::factory( MANAGERVIEW . 'forgot_password' )->bind( 'errors', $errors )->bind( 'postvalue', $postvalue );
        $this->template->content          = $view;
        $this->template->meta_description = SITENAME . " | Admin ";
        $this->template->meta_keywords    = SITENAME . " | Admin ";
        $this->template->title            = SITENAME . " | " . __( 'forgot_password' );
        $this->template->page_title       = __( 'forgot_password' );
    }
    /***********Dashboard Trip details chart************/
    public function action_get_company_trip_count()
    {
        $this->auto_render = false;
        $model_dashboard   = Model::factory( 'manager' );
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
        $model_dashboard = Model::factory( 'manager' );
        $get_transaction = $model_dashboard->total_trip_details( $startdate, $enddate );
        $view            = View::factory( 'company/total_trip_revenue' )->bind( 'post_values', $post_values )->bind( 'get_transaction', $get_transaction );
        echo $view;
        exit;
    }
    public function action_driver_trip_count()
    {
        $post_values     = $_POST;
        $startdate       = Commonfunction::ensureDatabaseFormat( $post_values['startdate'], 1 );
        $enddate         = Commonfunction::ensureDatabaseFormat( $post_values['enddate'], 2 );
        $driver_id       = (string) $post_values['drivers'];
        $model_dashboard = Model::factory( 'manager' );
        $gettransaction  = $model_dashboard->changegettransaction( $driver_id, $startdate, $enddate );
        $count           = "";
        $name            = "";
        if ( !empty( $gettransaction ) ) {
            foreach ( $gettransaction as $res ) {
                $name .= "'" . $res["driver_name"] . "',";
                $count .= $res["count"] . ",";
            }
            $name  = rtrim( $name, ',' );
            $count = rtrim( $count, ',' );
        }
        $view = View::factory( 'manager/driver_trip_count' )->bind( 'post_values', $post_values )->bind( 'name', $name )->bind( 'count', $count )->bind( 'get_transaction', $gettransaction );
        echo $view;
        exit;
    }
    /***********Dashboard Trip details chart************/
}
