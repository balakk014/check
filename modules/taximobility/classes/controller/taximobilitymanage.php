<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
* Contains User Management(Users)details
* @Package: Taximobility
* @Author: Taxi Team
* @URL : taximobility.com
********************************************/
class Controller_TaximobilityManage extends Controller_Siteadmin
{
    /**
     ****__construct()****
     */
    public function __construct( Request $request, Response $response )
    {
        parent::__construct( $request, $response );
        $this->is_login();
        $company_id     = $this->session->get( 'company_id' );
        $this->crntTime = $this->commonmodel->getcompany_all_currenttimestamp( $company_id );
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
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_company' );
        $this->selected_page_title = __( 'manage_company' );
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_searchcompany_list();
        $company_count             = !empty( $count_company_list ) ? $count_company_list[0]['count'] : 0;
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $company_count,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_company_searchlist( "", "", "", $offset, REC_PER_PAGE );
        $get_allcompany             = $manage_company->get_allcompany();
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_company' )->bind( 'all_company_list', $all_company_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset )->bind( 'all_list', $company_count );
        $this->template->title      = SITENAME . " | " . __( 'manage_company' );
        $this->template->page_title = __( 'manage_company' );
        $this->template->content    = $view;
    }
    public function action_companysearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_company' );
        $this->selected_page_title = __( 'manage_company' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_searchcompany_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        $company_count             = !empty( $count_company_list ) ? $count_company_list[0]['count'] : 0;
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $company_count,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'companysearch' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_company_list = $manage_company->all_company_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), '', $offset, REC_PER_PAGE );
        }
        if ( isset( $_SESSION['download_set'] ) ) {
            $export_table_header       = array(
                 __( 'name' ),
                __( 'companyname' ),
                __( 'email_label' ),
                __( 'no_of_taxi' ),
                __( 'no_of_driver' ),
                __( 'no_of_manager' ),
                __( 'Status' ) 
            );
            $export_table_field_select = array(
                 'name',
                'company_name',
                'email',
                'no_of_taxi',
                'no_of_driver',
                'no_of_manager',
                'company_status' 
            );
            $heading                   = 'ManageCompanies';
            $this->action_create_the_document( $all_company_list, $export_table_header, $export_table_field_select, $heading );
        }
        $get_allcompany          = $manage_company->get_allcompany();
        //set data to view file    
        $view                    = View::factory( 'admin/manage_company' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'get_allcompany', $get_allcompany )->bind( 'all_company_list', $all_company_list )->bind( 'all_list', $company_count );
        $this->template->content = $view;
    }
    public function action_active_company_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage  = Model::factory( 'manage' );
        $res_com = $manage->get_front_company_request( $_REQUEST['uniqueId'] );
        foreach ( $res_com as $rc ) {
            $manage->company_login_update( $rc['id'] );
            $mail              = "";
            $replace_variables = array(
                 REPLACE_LOGO => EMAILTEMPLATELOGO,
                REPLACE_SITENAME => $this->app_name,
                REPLACE_USERNAME => ucfirst( $rc['name'] ),
                REPLACE_EMAIL => $rc['email'],
                REPLACE_PASSWORD => $rc['org_password'],
                REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                REPLACE_SITEEMAIL => $this->siteemail,
                REPLACE_SITEURL => URL_BASE,
                REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
            );
            $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'registertemp.html', $replace_variables );
            $to                = $rc['email'];
            $from              = $this->siteemail;
            $subject           = __( 'registration_success' );
            $redirect          = "manage/company";
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
        $status   = $manage->active_company_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_company_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_company_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_motor()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_motor_company' );
        $this->selected_page_title = __( 'manage_motor_company' );
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_motor_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_motor_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_motorcompany' )->bind( 'all_company_list', $all_company_list )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_motor_company' );
        $this->template->page_title = __( 'manage_motor_company' );
        $this->template->content    = $view;
    }
    public function action_motorsearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_motor_company' );
        $this->selected_page_title = __( 'manage_motor_company' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_motorsearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_company_list = $manage_company->get_all_motor_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_motorcompany' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_active_motor_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_motor_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/motor" ); //transaction/index
    }
    public function action_block_motor_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_motor_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/motor" );
    }
    public function action_model()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_model' );
        $this->selected_page_title = __( 'manage_model' );
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_model_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_model_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_motormodel' )->bind( 'all_company_list', $all_company_list )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_model' );
        $this->template->page_title = __( 'manage_model' );
        $this->template->content    = $view;
    }
    public function action_fare()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_fare' );
        $this->selected_page_title = __( 'manage_fare' );
        $manage_fare               = Model::factory( 'manage' );
        $company_id                = $_SESSION['company_id'];
        $count_fare_list           = $manage_fare->count_fare_list( $company_id );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_fare_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_fare_list              = $manage_fare->all_fare_list( $company_id, $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_fare' )->bind( 'all_fare_list', $all_fare_list )->bind( 'pag_data', $pag_data )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_fare' );
        $this->template->page_title = __( 'manage_fare' );
        $this->template->content    = $view;
    }
    public function action_companymodelsearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_fare' );
        $this->selected_page_title = __( 'manage_fare' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_fare               = Model::factory( 'manage' );
        $count_fare_list           = $manage_fare->count_company_searchmodel_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_fare_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_fare_list = $manage_fare->get_company_all_model_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_fare' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'all_fare_list', $all_fare_list );
        $this->template->content = $view;
    }
    public function action_modelsearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_model' );
        $this->selected_page_title = __( 'manage_model' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_searchmodel_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_company_list = $manage_company->get_all_model_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_motormodel' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_modelinfo()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $edit_model    = Model::factory( 'edit' );
        $uid           = $this->request->param( 'id' );
        $motor_details = $edit_model->motordetails();
        $model_details = $edit_model->model_motordetails( $uid );
        //if invalid id is given redirect to manage page
        if ( count( $model_details ) == 0 ) {
            $this->request->redirect( "manage/model" );
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'manage_motormodelinfo' )->bind( 'motor_details', $motor_details )->bind( 'model_details', $model_details );
        $this->template->title      = SITENAME . " | " . __( 'MODEL_INFORMATION' );
        $this->template->page_title = SITENAME . " | " . __( 'MODEL_INFORMATION' );
        $this->template->content    = $view;
    }
    public function action_fareinfo()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $edit_model    = Model::factory( 'edit' );
        $manage        = Model::factory( 'manage' );
        $uid           = $this->request->param( 'id' );
        $motor_details = $edit_model->motordetails();
        $model_details = $manage->model_faredetails( $uid );
        if ( count( $model_details ) > 0 ) {
            $modelid    = $model_details[0]['model_id'];
            $model_name = $edit_model->model_motordetails( $modelid );
        } else {
            $this->request->redirect( "manage/fare" );
        }
        //send data to view file 
        $view                       = View::factory( ADMINVIEW . 'manage_motormodelinfo' )->bind( 'motor_details', $motor_details )->bind( 'model_details', $model_details )->bind( 'model_name', $model_name );
        $this->template->title      = SITENAME . " | " . __( 'MODEL_INFORMATION' );
        $this->template->page_title = SITENAME . " | " . __( 'MODEL_INFORMATION' );
        $this->template->content    = $view;
    }
    public function action_active_model_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_model_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_active_fare_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_fare_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/fare" );
    }
    public function action_block_model_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage = Model::factory( 'manage' );        
        $status = $manage->block_model_request( $_REQUEST['uniqueId'] );
        if($status == 1){
			# send mail to company admins
			$this->send_companymail($_REQUEST['uniqueId']);
			Message::success(__( 'Checked requests have been changed to blocked status.' ));
		}
		else if($status == -1){
			Message::error(__('atleast_onemodel'));
		}else{
			Message::error(__('driver_has_trips'));
		}
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    
    public function send_companymail($modelId){
		
		$modelname = array('1' => 'Taxi','3' => 'LUX','10' => 'LIMO');
		$manage = Model::factory( 'manage' );  
		foreach($modelId as $id){
			$models[] = $modelname[$id];
		}
		$models = implode($models,',');
		
		$list = $manage->getcompany_emails( $modelId );
		if(!empty($list)){
			foreach($list as $email){
				
				$replace_variables = array(
					REPLACE_LOGO => EMAILTEMPLATELOGO,
					REPLACE_SITENAME => COMPANY_SITENAME,
					REPLACE_DOMAINNAME => $models,
					REPLACE_COPYRIGHTS => SITE_COPYRIGHT
				);
				$message           = $this->emailtemplate->emailtemplate(DOCROOT . TEMPLATEPATH . 'model_block.html', $replace_variables);
				//~ $message           = 'model blocked';
				$to                = $email;
				$from              = $this->siteemail;
				$subject           = __( 'taximodels_blocked' );
				$mail_model        = Model::factory( 'add' );
				$smtp_result       = $mail_model->smtp_settings();
				if($to == 'venkatachalam.r@ndot.in'){
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
		}
	}
	
    public function action_block_fare_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_fare_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/fare" );
    }
    public function action_driver()
    {
        //Page Title
        $this->page_title          = __( 'manage_driver' );
        $this->selected_page_title = __( 'manage_driver' );
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $cid                       = $_SESSION['company_id'];
        $availabilitycount         = 1;
        $count_company_list        = $manage_company->count_driver_list();
        $all_list                  = $count_company_list;
        $count_company_list        = count( $count_company_list );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_driver_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $get_allcompany             = $manage_company->get_allcompany( 'A' );
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_driver' )->bind( 'all_company_list', $all_company_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset )->bind( 'all_list', $all_list );
        $this->template->title      = SITENAME . " | " . __( 'manage_driver' );
        $this->template->page_title = __( 'manage_driver' );
        $this->template->content    = $view;
    }
    public function action_driversearch()
    {
        $user_createdby            = $_SESSION['userid'];
        $company_id                = $_SESSION['company_id'];
        $usertype                  = $_SESSION['user_type'];
        //Page Title
        $this->page_title          = __( 'manage_driver' );
        $this->selected_page_title = __( 'manage_driver' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $cid                       = $_SESSION['company_id'];
        $peoples                   = $manage_company->get_people_list();
        $driver_list               = "";
        if ( $_REQUEST['status'] == "U" ) {
            $company          = ( isset( $_REQUEST['filter_company'] ) ) ? $_REQUEST['filter_company'] : $cid;
            $free_driver_list = $manage_company->unassign_taxi_list( $cid );
            if ( count( $free_driver_list ) > 0 ) {
                foreach ( $free_driver_list as $key => $value ) {
                    $driver_list .= $value['id'] . ",";
                }
                $driver_list = rtrim( $driver_list, ',' );
            }
        }
        $availabilitycount = 1;
        if ( $usertype != 'A' ) {
            $count_company_list = $manage_company->count_searchdriver_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ), $peoples, $driver_list );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        } else {
            $count_company_list = $manage_company->count_searchdriver_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ), $peoples, $driver_list );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        }
        if ( isset( $_SESSION['download_set'] ) ) {
            if ( $usertype == 'A' ) {
                $export_table_header       = array(
                     __( 'name' ),
                    __( 'email_label' ),
                    __( 'companyname' ),
                    __( 'phone_label' ),
                    __( 'country_label' ),
                    __( 'state_label' ),
                    __( 'city_label' ),
                    __( 'driver_license_id' ),
                    __( 'driver_status' ) 
                );
                $export_table_field_select = array(
                     'name',
                    'email',
                    'company_name',
                    'phone',
                    'country_name',
                    'state_name',
                    'city_name',
                    'driver_license_id',
                    'shift_status' 
                );
            } else {
                $export_table_header       = array(
                     __( 'name' ),
                    __( 'email_label' ),
                    __( 'phone_label' ),
                    __( 'country_label' ),
                    __( 'state_label' ),
                    __( 'city_label' ),
                    __( 'driver_license_id' ),
                    __( 'driver_status' ) 
                );
                $export_table_field_select = array(
                     'name',
                    'email',
                    'phone',
                    'country_name',
                    'state_name',
                    'city_name',
                    'driver_license_id',
                    'shift_status' 
                );
            }
            $heading = 'taxiexport';
            $this->action_create_the_document( $all_list, $export_table_header, $export_table_field_select, $heading );
        }
        //pagination loads here
        //-------------------------
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            if ( $usertype != 'A' ) {
                $all_company_list = $manage_company->get_all_driver_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ), $offset, REC_PER_PAGE, false, $peoples, $driver_list );
            } else {
                $all_company_list = $manage_company->get_all_driver_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ), $offset, REC_PER_PAGE, false, $peoples, $driver_list );
            }
        }
        $get_allcompany          = $manage_company->get_allcompany();
        //set data to view file    
        $view                    = View::factory( 'admin/manage_driver' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'get_allcompany', $get_allcompany )->bind( 'all_company_list', $all_company_list )->bind( 'all_list', $all_list );
        $this->template->content = $view;
    }
    public function action_active_driver_request()
    {
        $this->is_login();
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $company_id     = $_SESSION['company_id'];
        $manage         = Model::factory( 'manage' );
        $add_model      = Model::factory( 'add' );
        if ( $usertype != 'A' ) {
            $check_result = 1;
            if ( $check_result < 0 ) {
                if ( $usertype == 'C' ) {
                    $this->request->redirect( "manage/availabilitydriver" );
                }
                if ( $usertype == 'M' ) {
                    $this->request->redirect( "manage/availabilitydriver" );
                }
            }
        }
        $status   = $manage->active_driver_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_driver_request()
    {
        $this->is_login();
        $manage           = Model::factory( 'manage' );
        $pagedata         = explode( "/", $_SERVER["REQUEST_URI"] );
        $page             = isset( $pagedata[3] ) ? $pagedata[3] : '';
        $isDriverAssigned = $manage->isdriverassigned( $_REQUEST['uniqueId'] );
        if ( count( $isDriverAssigned ) == 0 ) {
            $status = $manage->block_driver_request( $_REQUEST['uniqueId'] );
            //Flash message for Reject
            //==========================
            Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        } else {
            Message::error( __( 'assigned_driver_not_block' ) );
        }
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_field()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_field' );
        $this->selected_page_title = __( 'manage_field' );
        $manage_company            = Model::factory( 'manage' );
        $CompanyList               = $manage_company->field_list();
        $count_company_list        = $manage_company->count_field_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_field_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_field' )->bind( 'all_company_list', $all_company_list )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_field' );
        $this->template->page_title = __( 'manage_field' );
        $this->template->content    = $view;
    }
    public function action_fieldsearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_field' );
        $this->selected_page_title = __( 'manage_field' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_fieldsearch_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_company_list = $manage_company->get_all_field_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_field' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_active_field_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_field_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/field" ); //transaction/index
    }
    public function action_block_field_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_field_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/field" ); //transaction/index
    }
    public function action_order_field_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->change_order_request( $_REQUEST['change_value'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/field" ); //transaction/index
    }
    public function action_taxi()
    {
        //Page Title
        $this->page_title          = __( 'manage_taxi' );
        $this->selected_page_title = __( 'manage_taxi' );
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $cid                       = $_SESSION['company_id'];
        $user_type                 = $_SESSION['user_type'];
        $availabilitycount         = 1;
        $count_company_list        = $manage_company->all_taxi_list( '', '', true );
        $peoples                   = $manage_company->get_people_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset           = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list = $manage_company->all_taxi_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        if ( $user_type == "A" ) {
            $get_allcompany = $manage_company->get_allcompany( 'A' );
        }
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_taxi' )->bind( 'all_company_list', $all_company_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset )->bind( 'all_list', $count_company_list );
        $this->template->title      = SITENAME . " | " . __( 'manage_taxi' );
        $this->template->page_title = __( 'manage_taxi' );
        $this->template->content    = $view;
    }
    public function action_taxisearch()
    {
        $user_createdby            = $_SESSION['userid'];
        $cid                       = $company_id = $_SESSION['company_id'];
        $usertype                  = $_SESSION['user_type'];
        //Page Title
        $this->page_title          = __( 'manage_taxi' );
        $this->selected_page_title = __( 'manage_taxi' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $taxi_list                 = "";
        if ( $_REQUEST['status'] == "U" ) {
            $company       = ( isset( $_REQUEST['filter_company'] ) ) ? $_REQUEST['filter_company'] : $company_id;
            $freetaxi_list = $manage_company->unassign_taxi_list( $company );
            if ( count( $freetaxi_list ) > 0 ) {
                foreach ( $freetaxi_list as $key => $value ) {
                    $taxi_list .= $value['taxi_id'] . ",";
                }
                $taxi_list = rtrim( $taxi_list, ',' );
            }
        }
        $availabilitycount = 1;
        if ( $usertype != 'A' ) {
            $count_company_list = $manage_company->count_searchtaxi_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ), $taxi_list );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        } else {
            $count_company_list = $manage_company->count_searchtaxi_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ), $taxi_list );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        }
        //pagination loads here
        //-------------------------
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            if ( $usertype != 'A' ) {
                $all_company_list = $manage_company->get_all_taxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ), $offset, REC_PER_PAGE, $taxi_list );
            } else {
                $all_company_list = $manage_company->get_all_taxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ), $offset, REC_PER_PAGE, $taxi_list );
            }
        }
        if ( $usertype == "A" ) {
            $get_allcompany = $manage_company->get_allcompany();
        }
        if ( isset( $_SESSION['download_set'] ) ) {
            if ( $usertype == 'A' ) {
                $export_table_header       = array(
                     __( 'taxi_no' ),
                    __( 'companyname' ),
                    __( 'taxi_type' ),
                    __( 'taxi_model' ),
                    __( 'Status' ) 
                );
                $export_table_field_select = array(
                     'taxi_no',
                    'company_name',
                    'motor_name',
                    'model_name',
                    'taxi_status' 
                );
            } else {
                $export_table_header       = array(
                     __( 'taxi_no' ),
                    __( 'taxi_type' ),
                    __( 'taxi_model' ),
                    __( 'Status' ) 
                );
                $export_table_field_select = array(
                     'taxi_no',
                    'motor_name',
                    'model_name',
                    'taxi_status' 
                );
            }
            $heading = 'ManageTaxi';
            $this->action_create_the_document( $all_list, $export_table_header, $export_table_field_select, $heading );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_taxi' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'get_allcompany', $get_allcompany )->bind( 'all_company_list', $all_company_list )->bind( 'all_list', $count_company_list );
        $this->template->content = $view;
    }
    public function action_active_taxi_request()
    {
        $this->is_login();
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $company_id     = $_SESSION['company_id'];
        $manage         = Model::factory( 'manage' );
        $add_model      = Model::factory( 'add' );
        if ( $usertype != 'A' ) {
            $check_result = 1;
            if ( $check_result < 0 ) {
                if ( $usertype == 'C' ) {
                    $this->request->redirect( "manage/availabilitytaxi" );
                }
                if ( $usertype == 'M' ) {
                    $this->request->redirect( "manage/availabilitytaxi" );
                }
            }
        }
        $status   = $manage->active_taxi_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_taxi_request()
    {
        $this->is_login();
        $manage         = Model::factory( 'manage' );
        $pagedata       = explode( "/", $_SERVER["REQUEST_URI"] );
        $page           = isset( $pagedata[3] ) ? $pagedata[3] : '';
        $isTaxiAssigned = $manage->istaxiassigned( $_REQUEST['uniqueId'] );
        if ( count( $isTaxiAssigned ) == 0 ) {
            $status = $manage->block_taxi_request( $_REQUEST['uniqueId'] );
            //Flash message for Reject
            //==========================
            Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        } else {
            Message::error( __( 'assigned_taxi_not_blocked' ) );
        }
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_package()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_package' );
        $this->selected_page_title = __( 'manage_package' );
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_package_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_package_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/manage_package' )->bind( 'all_company_list', $all_company_list )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_package' );
        $this->template->page_title = __( 'manage_package' );
        $this->template->content    = $view;
    }
    public function action_packagesearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_package' );
        $this->selected_page_title = __( 'manage_package' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_packagesearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_company_list = $manage_company->get_all_package_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_package' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_active_package_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_package_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_package_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_package_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_country()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_country' );
        $this->selected_page_title = __( 'manage_mountry' );
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_country_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset           = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list = $manage_company->all_country_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //Find page action in view
        $action           = $this->request->action();
        //get form submit request
        $update_post      = arr::get( $_REQUEST, 'update' );
        $post             = array();
        if ( $update_post ) {
            $post = $_REQUEST;
            if ( isset( $post['default_country'] ) ) {
                $id                     = $post['default_country'];
                $update_default_country = $manage_company->update_default_country( $id );
                if ( $update_default_country == 1 ) {
                    Message::success( __( 'changed_default_country' ) );
                    $this->request->redirect( "manage/country" );
                } else if ( $update_default_country == '-2' ) {
                    Message::error( __( 'not_updated' ) );
                    $this->request->redirect( "manage/country" );
                } else if ( $update_default_country == '-1' ) {
                    Message::error( __( 'select_the_activecountry' ) );
                    $this->request->redirect( "manage/country" );
                } else {
                    Message::error( __( 'select_the_defaultcountry' ) );
                    $this->request->redirect( "manage/country" );
                }
            } else {
                Message::error( __( 'not_updated' ) );
                $this->request->redirect( "manage/country" );
            }
        }
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_country' )->bind( 'all_company_list', $all_company_list )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_country' );
        $this->template->page_title = __( 'manage_country' );
        $this->template->content    = $view;
    }
    public function action_countrysearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_country' );
        $this->selected_page_title = __( 'manage_country' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->get_all_country_countlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_company_list = $manage_company->get_all_country_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_country' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_active_country_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_country_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_country_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_country_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        if ( $status == 1 ) {
            Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        } else {
            Message::error( __( 'country_not_delete' ) );
        }
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_gateway()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' && $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        if ( in_array( $_REQUEST['default_payment'], $_REQUEST['uniqueId'] ) ) {
            $default = 0;
        } else {
            $default = 1;
        }
        $manage = Model::factory( 'manage' );
        if ( $default > 0 ) {
            $status   = $manage->block_gateway( $_REQUEST['uniqueId'], $_REQUEST['default_payment'] );
            $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
            $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
            //Flash message for Reject
            //==========================
            if ( $status == 1 ) {
                Message::success( __( 'Checked requests have been changed to blocked status.' ) );
            } else {
                Message::error( __( 'block_default_gateway' ) );
            }
        } else {
            Message::error( __( 'block_default_gateway' ) );
        }
        if ( $usertype == 'A' ) {
            $this->request->redirect( "admin/payment_gateway_module" );
        } else {
            $this->request->redirect( "company/payment_gateway_module" );
        }
    }
    public function action_active_gateway()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'C' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_gateway( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        if ( $usertype == 'A' ) {
            $this->request->redirect( "admin/payment_gateway_module" );
        } else {
            $this->request->redirect( "company/payment_gateway_module" );
        }
    }
    public function action_trash_gateway()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'C' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        if ( in_array( $_REQUEST['default_payment'], $_REQUEST['uniqueId'] ) ) {
            $default = 0;
        } else {
            $default = 1;
        }
        $manage = Model::factory( 'manage' );
        if ( $default > 0 ) {
            $status = $manage->trash_gateway( $_REQUEST['uniqueId'] );
            if ( $status == 1 ) {
                Message::success( __( 'Checked requests has been deleted' ) );
            } else {
                Message::error( __( 'country_not_delete' ) );
            }
        } else {
            Message::error( __( 'country_not_delete' ) );
        }
        if ( $usertype == 'A' ) {
            $this->request->redirect( "admin/payment_gateway_module" );
        } else {
            $this->request->redirect( "company/payment_gateway_module" );
        }
    }
    public function action_trash_country_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage = Model::factory( 'manage' );
        $status = $manage->trash_country_request( $_REQUEST['uniqueId'] );
        if ( $status == 1 ) {
            Message::success( __( 'Checked requests has been deleted' ) );
        } else {
            Message::error( __( 'country_not_delete' ) );
        }
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_city()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_city' );
        $this->selected_page_title = __( 'manage_city' );
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_city_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset           = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list = $manage_company->all_city_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //Find page action in view
        $action           = $this->request->action();
        //get form submit request
        $update_post      = arr::get( $_REQUEST, 'update' );
        $post             = array();
        if ( $update_post ) {
            if ( isset( $_REQUEST['default_city'] ) ) {
                $post                   = $_REQUEST;
                $id                     = $post['default_city'];
                $update_default_country = $manage_company->update_default_city( $id );
                if ( $update_default_country == 1 ) {
                    Message::success( __( 'changed_default_city' ) );
                    $this->request->redirect( "manage/city" );
                } else if ( $update_default_country == '-1' ) {
                    Message::error( __( 'select_the_activecity' ) );
                    $this->request->redirect( "manage/city" );
                } else if ( $update_default_country == '-2' ) {
                    Message::error( __( 'not_updated' ) );
                    $this->request->redirect( "manage/city" );
                } else {
                    Message::error( __( 'select_the_defaultcity' ) );
                    $this->request->redirect( "manage/city" );
                }
            } else {
                Message::error( __( 'not_updated' ) );
                $this->request->redirect( "manage/city" );
            }
        }
        //send data to view file 
        $view                       = View::factory( 'admin/manage_city' )->bind( 'all_company_list', $all_company_list )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_city' );
        $this->template->page_title = __( 'manage_city' );
        $this->template->content    = $view;
    }
    public function action_citysearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_city' );
        $this->selected_page_title = __( 'manage_city' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_searchcity_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb',
            'uri_segment' => 'page' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_company_list = $manage_company->get_all_city_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_city' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_state()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_state' );
        $this->selected_page_title = __( 'manage_state' );
        $manage_state              = Model::factory( 'manage' );
        $count_state_list          = $manage_state->count_state_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset         = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data       = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_state_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_state_list = $manage_state->all_state_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //Find page action in view
        $action         = $this->request->action();
        //get form submit request
        $update_post    = arr::get( $_REQUEST, 'update' );
        $post           = array();
        if ( $update_post ) {
            if ( isset( $_REQUEST['default_state'] ) ) {
                $post                   = $_REQUEST;
                $id                     = $post['default_state'];
                $update_default_country = $manage_state->update_default_state( $id );
                if ( $update_default_country == 1 ) {
                    Message::success( __( 'changed_default_state' ) );
                    $this->request->redirect( "manage/state" );
                } else if ( $update_default_country == '-1' ) {
                    Message::error( __( 'select_the_activestate' ) );
                    $this->request->redirect( "manage/state" );
                } else if ( $update_default_country == '-2' ) {
                    Message::error( __( 'not_updated' ) );
                    $this->request->redirect( "manage/state" );
                } else {
                    Message::error( __( 'select_the_defaultstate' ) );
                    $this->request->redirect( "manage/state" );
                }
            } else {
                Message::error( __( 'not_updated' ) );
                $this->request->redirect( "manage/state" );
            }
        }
        //send data to view file 
        $view                       = View::factory( 'admin/manage_state' )->bind( 'all_state_list', $all_state_list )->bind( 'pag_data', $pag_data )->bind( 'StateList', $StateList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_state' );
        $this->template->page_title = __( 'manage_state' );
        $this->template->content    = $view;
    }
    public function action_statesearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_state' );
        $this->selected_page_title = __( 'manage_state' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_state              = Model::factory( 'manage' );
        $count_state_list          = $manage_state->count_searchstate_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_state_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_state' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_state_list = $manage_state->get_all_state_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_state' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'StateList', $StateList )->bind( 'pag_data', $pag_data )->bind( 'all_state_list', $all_state_list );
        $this->template->content = $view;
    }
    public function action_active_city_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_city_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_city_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_city_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        if ( $status == 1 ) {
            Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        } else {
            Message::error( __( 'city_not_block' ) );
        }
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_trash_city_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->trash_city_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        if ( $status == 1 ) {
            Message::success( __( 'Checked requests has been deleted' ) );
        } else {
            Message::error( __( 'city_not_delete' ) );
        }
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_active_state_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_state_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_state_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_state_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        if ( $status == 1 ) {
            Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        } else {
            Message::error( __( 'state_not_block' ) );
        }
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_trash_state_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->trash_state_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        if ( $status == 1 ) {
            Message::success( __( 'Checked requests has been deleted' ) );
        } else {
            Message::error( __( 'state_not_delete' ) );
        }
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_manager()
    {
        //Page Title
        $this->page_title          = __( 'manage_manager' );
        $this->selected_page_title = __( 'manage_manager' );
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_manager_list();
        $all_list                  = $count_company_list;
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_manager_list( $offset, REC_PER_PAGE );
        $get_allcompany             = $manage_company->get_allcompany();
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_manager' )->bind( 'all_company_list', $all_company_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset )->bind( 'all_list', $all_list );
        $this->template->title      = SITENAME . " | " . __( 'manage_manager' );
        $this->template->page_title = __( 'manage_manager' );
        $this->template->content    = $view;
    }
    public function action_managersearch()
    {
        $user_createdby            = $_SESSION['userid'];
        $company_id                = $_SESSION['company_id'];
        $usertype                  = $_SESSION['user_type'];
        //Page Title
        $this->page_title          = __( 'manage_manager' );
        $this->selected_page_title = __( 'manage_manager' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        if ( $usertype != 'A' ) {
            $count_company_list = $manage_company->count_searchmanager_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ) );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        } else {
            $count_company_list = $manage_company->count_searchmanager_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ) );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        }
        if ( isset( $_SESSION['download_set'] ) ) {
            if ( $usertype == 'A' ) {
                $export_table_header       = array(
                     __( 'name' ),
                    __( 'companyname' ),
                    __( 'email_label' ),
                    __( 'address' ),
                    __( 'country_label' ),
                    __( 'state_label' ),
                    __( 'city_label' ),
                    __( 'Status' ) 
                );
                $export_table_field_select = array(
                     'name',
                    'company_name',
                    'email',
                    'address',
                    'country_name',
                    'state_name',
                    'city_name',
                    'status' 
                );
            } else {
                $export_table_header       = array(
                     __( 'name' ),
                    __( 'email_label' ),
                    __( 'address' ),
                    __( 'country_label' ),
                    __( 'state_label' ),
                    __( 'city_label' ),
                    __( 'Status' ) 
                );
                $export_table_field_select = array(
                     'name',
                    'email',
                    'address',
                    'country_name',
                    'state_name',
                    'city_name',
                    'status' 
                );
            }
            $heading = 'ManageDispatcher';
            $this->action_create_the_document( $all_list, $export_table_header, $export_table_field_select, $heading );
        }
        //pagination loads here
        //-------------------------
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            if ( $usertype != 'A' ) {
                $all_company_list = $manage_company->all_manager_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ), $offset, REC_PER_PAGE );
            } else {
                $all_company_list = $manage_company->all_manager_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ), $offset, REC_PER_PAGE );
            }
        }
        $get_allcompany          = $manage_company->get_allcompany();
        //set data to view file    
        $view                    = View::factory( 'admin/manage_manager' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'get_allcompany', $get_allcompany )->bind( 'all_company_list', $all_company_list )->bind( 'all_list', $all_list );
        $this->template->content = $view;
    }
    public function action_active_manager_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_manager_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] ); //transaction/index
    }
    public function action_block_manager_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_manager_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] ); //transaction/index
    }
    public function action_admin()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/login" );
        }
        //Page Title
        $this->page_title          = __( 'manage_superadmin' );
        $this->selected_page_title = __( 'manage_superadmin' );
        $manage_company            = Model::factory( 'manage' );
        $count_admin_list          = $manage_company->count_admin_list();
        $all_list                  = $count_admin_list;
        $count_admin_list          = count( $count_admin_list );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_admin_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_admin_list             = $manage_company->all_admin_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_admin' )->bind( 'all_admin_list', $all_admin_list )->bind( 'pag_data', $pag_data )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset )->bind( 'all_list', $all_list );
        $this->template->title      = SITENAME . " | " . __( 'manage_superadmin' );
        $this->template->page_title = __( 'manage_superadmin' );
        $this->template->content    = $view;
    }
    public function action_adminsearch()
    {
        //Page Title
        $this->page_title          = __( 'manage_superadmin' );
        $this->selected_page_title = __( 'manage_superadmin' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_model              = Model::factory( 'manage' );
        $admin_list                = $manage_model->all_admin_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        $all_list                  = $admin_list;
        $count_admin_list          = count( $admin_list );
        if ( isset( $_SESSION['download_set'] ) ) {
            $export_table_header       = array(
                 __( 'name' ),
                __( 'email_label' ),
                __( 'address' ),
                __( 'country_label' ),
                __( 'state_label' ),
                __( 'city_label' ),
                __( 'Status' ) 
            );
            $export_table_field_select = array(
                 'name',
                'email',
                'address',
                'country_name',
                'state_name',
                'city_name',
                'status' 
            );
            $heading                   = 'ManageModerator';
            $this->action_create_the_document( $all_list, $export_table_header, $export_table_field_select, $heading );
        }
        //pagination loads here
        //-------------------------
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                  = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_admin_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post             = arr::get( $_REQUEST, 'search_user' );
        $all_admin_list          = $manage_model->all_admin_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        //set data to view file    
        $view                    = View::factory( 'admin/manage_admin' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_admin_list', $all_admin_list )->bind( 'all_list', $all_list );
        $this->template->content = $view;
    }
    public function action_active_admin_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_manager_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_admin_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_admin_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_assigntaxi()
    {
        //Page Title
        $this->page_title          = __( 'manage_assigned_taxi' );
        $this->selected_page_title = __( 'manage_assigned_taxi' );
        $manage_company            = Model::factory( 'manage' );
        $add_model                 = Model::factory( 'add' );
        $usertype                  = $_SESSION['user_type'];
        $cid                       = $_SESSION['company_id'];
        if ( $usertype != 'A' ) {
            $check_result = 1;
            if ( $check_result < 0 ) {
                if ( $usertype == 'C' ) {
                    $this->request->redirect( "manage/availabilitytaxi" );
                }
                if ( $usertype == 'M' ) {
                    $this->request->redirect( "manage/availabilitytaxi" );
                }
            }
            $check_result = 1;
            if ( $check_result < 0 ) {
                if ( $usertype == 'C' ) {
                    $this->request->redirect( "manage/availabilitydriver" );
                }
                if ( $usertype == 'M' ) {
                    $this->request->redirect( "manage/availabilitydriver" );
                }
            }
        }
        $count_company_list = $manage_company->count_assigntaxi_list();
        $all_list           = $count_company_list;
        $count_company_list = count( $count_company_list );
        $peoples            = $manage_company->get_people_list();
        //pagination loads here
        //-------------------------
        $page_no            = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset           = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list = $manage_company->all_assigntaxi_list( $offset, REC_PER_PAGE, $peoples );
        //****pagination ends here***//
        if ( $usertype != "M" ) {
            $get_allcompany = $manage_company->get_allcompany( 'A' );
        }
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_assigntaxi' )->bind( 'all_company_list', $all_company_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'pag_data', $pag_data )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset )->bind( 'all_list', $all_list );
        $this->template->title      = SITENAME . " | " . __( 'manage_assigned_taxi' );
        $this->template->page_title = __( 'manage_assigned_taxi' );
        $this->template->content    = $view;
    }
    public function action_set_ajax_session()
    {
        $var = $_GET['set'];
        $this->session->set( 'download_set', $var );
        echo 'true';
        exit;
    }
    public function action_assigntaxisearch()
    {
        $user_createdby            = $_SESSION['userid'];
        $company_id                = $_SESSION['company_id'];
        $usertype                  = $_SESSION['user_type'];
        //Page Title
        $this->page_title          = __( 'manage_assigned_taxi' );
        $this->selected_page_title = __( 'manage_assigned_taxi' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $cid                       = $_SESSION['company_id'];
        if ( $usertype != 'A' ) {
            $check_result = 1;
            if ( $check_result < 0 ) {
                if ( $usertype == 'C' ) {
                    Message::success( __( 'limited_taxi' ) );
                    $this->request->redirect( "manage/availabilitytaxi" );
                }
                if ( $usertype == 'M' ) {
                    Message::success( __( 'limited_taxi' ) );
                    $this->request->redirect( "manage/availabilitytaxi" );
                }
            }
            $check_result = 1;
            if ( $check_result < 0 ) {
                if ( $usertype == 'C' ) {
                    Message::success( __( 'limited_driver' ) );
                    $this->request->redirect( "manage/availabilitydriver" );
                }
                if ( $usertype == 'M' ) {
                    Message::success( __( 'limited_driver' ) );
                    $this->request->redirect( "manage/availabilitydriver" );
                }
            }
        }
        if ( $usertype != 'A' ) {
            $count_company_list = $manage_company->count_assigntaxisearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ) );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        } else {
            $count_company_list = $manage_company->count_assigntaxisearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ) );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        }
        $peoples = $manage_company->get_people_list();
        if ( isset( $_SESSION['download_set'] ) ) {
            if ( count( $all_list ) > 0 ) {
                foreach ( $all_list as $key => $value ) {
                    if ( array_key_exists( 'mapping_startdate', $value ) ) {
                        $from_date = Commonfunction::getDateTimeFormat( $value['mapping_startdate'], 1 );
                        unset( $value['mapping_startdate'] );
                        $all_list[$key]['mapping_startdate'] = $from_date;
                    }
                    if ( array_key_exists( 'mapping_enddate', $value ) ) {
                        $to_date = Commonfunction::getDateTimeFormat( $value['mapping_enddate'], 1 );
                        unset( $value['mapping_enddate'] );
                        $all_list[$key]['mapping_enddate'] = $to_date;
                    }
                }
            }
            if ( $usertype == 'A' ) {
                $export_table_header       = array(
                     __( 'driver_name' ),
                    __( 'taxi_no' ),
                    __( 'companyname' ),
                    __( 'country_label' ),
                    __( 'state_label' ),
                    __( 'city_label' ),
                    __( 'from_date' ),
                    __( 'end_date' ),
                    __( 'Status' ) 
                );
                $export_table_field_select = array(
                     'name',
                    'taxi_no',
                    'company_name',
                    'country_name',
                    'state_name',
                    'city_name',
                    'mapping_startdate',
                    'mapping_enddate',
                    'mapping_status' 
                );
            } else {
                $export_table_header       = array(
                     __( 'driver_name' ),
                    __( 'taxi_no' ),
                    __( 'country_label' ),
                    __( 'state_label' ),
                    __( 'city_label' ),
                    __( 'from_date' ),
                    __( 'end_date' ),
                    __( 'Status' ) 
                );
                $export_table_field_select = array(
                     'name',
                    'taxi_no',
                    'country_name',
                    'state_name',
                    'city_name',
                    'mapping_startdate',
                    'mapping_enddate',
                    'mapping_status' 
                );
            }
            $heading = 'ManageAssignedTaxi';
            $data    = array();
            if ( count( $all_list ) > 0 ) {
                foreach ( $all_list as $val ) {
                    if ( $val['mapping_status'] == 'A' ) {
                        $val['mapping_status'] = "Active";
                    } elseif ( $val['mapping_status'] == 'T' ) {
                        $val['mapping_status'] = "Trash";
                    } elseif ( $val['mapping_status'] == 'U' ) {
                        $val['mapping_status'] = "Unassign";
                    } else {
                        $val['mapping_status'] = "Deactive";
                    }
                    $data[] = $val;
                }
            }
            $this->action_create_the_document( $data, $export_table_header, $export_table_field_select, $heading );
        }
        //-------------------------
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            if ( $usertype != 'A' ) {
                $all_company_list = $manage_company->get_all_assigntaxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ), $offset, REC_PER_PAGE, $peoples );
            } else {
                $all_company_list = $manage_company->get_all_assigntaxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ), $offset, REC_PER_PAGE, $peoples );
            }
        }
        $get_allcompany          = $manage_company->get_allcompany();
        //set data to view file    
        $view                    = View::factory( 'admin/manage_assigntaxi' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'get_allcompany', $get_allcompany )->bind( 'all_company_list', $all_company_list )->bind( 'all_list', $all_list );
        $this->template->content = $view;
    }
    public function action_active_assigntaxi_request()
    {
        $this->is_login();
        $manage      = Model::factory( 'manage' );
        $assignCnt   = 0;
        $drhastaxi   = 0;
        $driveridArr = array();
        foreach ( $_REQUEST['uniqueId'] as $key => $assignId ) {
            $assignedDets = $manage->get_assigned_details( $assignId );
            if ( count( $assignedDets ) > 0 ) {
                $alreadyAssigned = $manage->check_already_assigned( $assignedDets[0]['mapping_driverid'], $assignedDets[0]['mapping_taxiid'], $assignedDets[0]['mapping_startdate'], $assignedDets[0]['mapping_enddate'] );
                if ( in_array( $assignedDets[0]['mapping_driverid'], $driveridArr ) ) {
                    $drhastaxi = $manage->check_driver_have_taxi( $assignId, $assignedDets[0]['mapping_driverid'], $assignedDets[0]['mapping_startdate'], $assignedDets[0]['mapping_enddate'] );
                }
                if ( $alreadyAssigned > 0 || $drhastaxi > 0 ) {
                    $assignCnt++;
                }
                $driveridArr[] = $assignedDets[0]['mapping_driverid'];
            }
        }
        if ( $assignCnt == 0 ) {
            $status   = $manage->active_assigntaxi_request( $_REQUEST['uniqueId'] );
            $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
            $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
            //Flash message for Reject
            //==========================
            Message::success( __( 'Checked requests have been changed to activated status.' ) );
        } else {
            Message::error( __( 'selected_taxi_already_assign' ) );
        }
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_assigntaxi_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_assigntaxi_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    /**
     * Unassign Taxi
     **/
    public function action_unassign_taxi()
    {
        $this->is_login();
        $manage       = Model::factory( 'manage' );
        $commMdl      = Model::factory( 'commonmodel' );
        $driver_model = Model::factory( 'driver' );
        $date         = $commMdl->getcompany_all_currenttimestamp( $_REQUEST['mapping_companyid'] );
        $status       = $manage->unassign_taxi_request( $_REQUEST['uniqueId'], $date );
        if ( count( $status ) > 0 ) {
            //to update driver status and shift status
            $customer_google_api = $commMdl->select_site_settings( 'driver_android_key', SITEINFO );
            foreach ( $status as $mapId => $driverId ) {
                $driver_shift     = $driver_model->get_shift_status( $driverId );
                $driverShiftId    = isset( $driver_shift[0]['driver_shift_id'] ) ? $driver_shift[0]['driver_shift_id'] : 0;
                //send push notification to driver
                $driverDeviceDets = $manage->getDriverDeviceToken( $driverId );
                if ( count( $driverDeviceDets ) > 0 && !empty( $driverDeviceDets[0]['device_token'] ) ) {
                    $pushMessage = array(
                         "message" => __( 'taxi_unassigned_by_admin' ),
                        "status" => 15,
                        "display" => 1 
                    );
                    $commMdl->send_pushnotification( $driverDeviceDets[0]['device_token'], $driverDeviceDets[0]['device_type'], $pushMessage, $customer_google_api );
                }
                $signout = $manage->signoutDriver( $driverId, $driverShiftId, $date );
            }
        }
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been unassigned.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_trash_assigntaxi_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->trash_assigntaxi_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been deleted.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_ratingdrivers()
    {
        $user_createdby            = $_SESSION['userid'];
        $usertype                  = $_SESSION['user_type'];
        $this->page_title          = __( 'manage_rating_taxi' );
        $this->selected_page_title = __( 'manage_rating_taxi' );
        $manage_rating_drivers     = Model::factory( 'manage' );
        $count_rating_drivers      = $manage_rating_drivers->countall_rating_drivers();
        //pagination loads here
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset             = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data           = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_rating_drivers,
            'view' => 'pagination/punbb' 
        ) );
        $all_rating_drivers = $manage_rating_drivers->all_rating_drivers( $offset, REC_PER_PAGE );
        if ( $usertype == "A" ) {
            $get_rate_company = $manage_rating_drivers->get_rating_company();
        }
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/managerating_drivers' )->bind( 'all_rating_drivers', $all_rating_drivers )->bind( 'pag_data', $pag_data )->bind( 'RatingdriversList', $RatingdriversList )->bind( 'get_rate_company', $get_rate_company )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_rating_drivers' );
        $this->template->page_title = __( 'manage_rating_drivers' );
        $this->template->content    = $view;
    }
    public function action_managerating_driversview()
    {
        $user_createdby            = $_SESSION['userid'];
        $usertype                  = $_SESSION['user_type'];
        $uid                       = $this->request->param( 'id' );
        $this->page_title          = __( 'rating_drivers' );
        $this->selected_page_title = __( 'rating_drivers' );
        $manage_rating_drivers     = Model::factory( 'driver' );
        $count_rating_drivers      = count( $manage_rating_drivers->get_driver_logs1( $uid, 'R', 'A', '1', '', '', TRUE ) );
        $driver_profile            = $manage_rating_drivers->get_my_profile_details( $uid );
        if ( count( $driver_profile ) == 0 ) {
            $this->request->redirect( "manage/ratingdrivers" );
        }
        //pagination loads here
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_rating_drivers,
            'view' => 'pagination/punbb' 
        ) );
        $all_rating_drivers         = $manage_rating_drivers->get_driver_logs1( $uid, 'R', 'A', '1', REC_PER_PAGE, $offset, FALSE );
        //send data to view file 
        $view                       = View::factory( 'admin/managerating_driversview' )->bind( 'all_rating_drivers', $all_rating_drivers )->bind( 'pag_data', $pag_data )->bind( 'driver_profile', $driver_profile )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'rating_drivers' );
        $this->template->page_title = __( 'rating_drivers' );
        $this->template->content    = $view;
    }
    public function action_ratingcompanies()
    {
        $user_createdby            = $_SESSION['userid'];
        $usertype                  = $_SESSION['user_type'];
        $this->page_title          = __( 'manage_rating_company' );
        $this->selected_page_title = __( 'manage_rating_company' );
        $manage_rating_companies   = Model::factory( 'manage' );
        $count_rating_companies    = $manage_rating_companies->count_rating_companies_list();
        //pagination loads here
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_rating_companies,
            'view' => 'pagination/punbb' 
        ) );
        $all_rating_companies       = $manage_rating_companies->all_rating_companies( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/managerating_companies' )->bind( 'all_rating_companies', $all_rating_companies )->bind( 'pag_data', $pag_data )->bind( 'RatingcompanyList', $RatingcompanyList )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_rating_comapany' );
        $this->template->page_title = __( 'manage_rating_company' );
        $this->template->content    = $view;
    }
    public function action_delete_ratingdrivers()
    {
        $id                   = $this->request->param( 'id' );
        $delete_ratingdrivers = Model::factory( 'manage' );
        $ratingdrivers        = $delete_ratingdrivers->delete_ratingdrivers( $id );
        if ( $ratingdrivers ) {
            Message::success( __( 'Rating was deleted.' ) );
            $this->request->redirect( "manage/ratingdrivers" );
        }
    }
    public function action_delete_ratingcompanies()
    {
        $id                     = $this->request->param( 'id' );
        $delete_ratingcompanies = Model::factory( 'manage' );
        $ratingcompanies        = $delete_ratingcompanies->delete_ratingcompanies( $id );
        if ( $ratingcompanies ) {
            Message::success( __( 'Rating was deleted.' ) );
            $this->request->redirect( "manage/ratingcompanies" );
        }
    }
    public function action_ratingdriver_search()
    {
        $user_createdby            = $_SESSION['userid'];
        $user_type                 = $_SESSION['user_type'];
        //Page Title
        $this->page_title          = __( 'manage_rating_taxi' );
        $this->selected_page_title = __( 'manage_rating_taxi' );
        //default empty list and offset
        $search_list               = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_rating_drivers     = Model::factory( 'manage' );
        $keyword                   = trim( Html::chars( $_REQUEST['keyword'] ) );
        $company_id                = 0;
        if ( isset( $_REQUEST['filter_company'] ) ) {
            $company_id = trim( Html::chars( $_REQUEST['filter_company'] ) );
        }
        $count_rating_drivers = $manage_rating_drivers->count_rating_drivers_list( $keyword, $company_id );
        //pagination loads here
        $page_no              = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_rating_drivers,
            'view' => 'pagination/punbb' 
        ) );
        //Post results for search 
        if ( isset( $search_post ) && $_REQUEST ) {
            $all_rating_drivers = $manage_rating_drivers->get_all_ratingdrivers_searchlist( $keyword, $company_id, $offset, REC_PER_PAGE );
        }
        if ( $user_type == "A" ) {
            $get_rate_company = $manage_rating_drivers->get_rating_company();
        }
        //set data to view file    
        $view                       = View::factory( 'admin/managerating_drivers' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'pag_data', $pag_data )->bind( 'srch', $_REQUEST )->bind( 'RatingdriversList', $RatingdriversList )->bind( 'get_rate_company', $get_rate_company )->bind( 'all_rating_drivers', $all_rating_drivers );
        $this->template->page_title = __( 'manage_rating_drivers' );
        $this->template->content    = $view;
    }
    public function action_ratingcompanies_search()
    {
        $user_createdby            = $_SESSION['userid'];
        //Page Title
        $this->page_title          = __( 'manage_rating_company' );
        $this->selected_page_title = __( 'manage_rating_company' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_rating_companies   = Model::factory( 'manage' );
        $count_rating_companies    = $manage_rating_companies->count_rating_companies_list();
        //get form submit request
        $search_post               = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( isset( $search_post ) && $_REQUEST ) {
            $all_rating_companies = $manage_rating_companies->get_all_ratingcompanies_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ) );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/managerating_companies' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'RatingcompanyList', $RatingcompanyList )->bind( 'all_rating_companies', $all_rating_companies );
        $this->template->content = $view;
    }
    public function action_packagereport()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        //Page Title
        $this->page_title          = __( 'upgrade_reports' );
        $this->selected_page_title = __( 'upgrade_reports' );
        $company_val               = isset( $_GET["filter_company"] ) ? $_GET["filter_company"] : '';
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_packagereport_list( $company_val );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $rcompany_list              = $manage_company->get_all_company_list();
        $all_company_list           = $manage_company->all_packagereport_list( $company_val, $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/package_report' )->bind( 'all_company_list', $all_company_list )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset )->bind( 'rcompany_list', $rcompany_list );
        $this->template->title      = SITENAME . " | " . __( 'upgrade_reports' );
        $this->template->page_title = __( 'upgrade_reports' );
        $this->template->content    = $view;
    }
    public function action_packagereports()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        //Page Title
        $this->page_title          = __( 'upgrade_reports' );
        $this->selected_page_title = __( 'upgrade_reports' );
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_packagereport_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_packagereport_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/package_reports' )->bind( 'all_company_list', $all_company_list )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'upgrade_reports' );
        $this->template->page_title = __( 'upgrade_reports' );
        $this->template->content    = $view;
    }
    public function action_packagereportsearch()
    {
        //Page Title
        $this->page_title          = __( 'upgrade_reports' );
        $this->selected_page_title = __( 'upgrade_reports' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_assigntaxi_list( trim( Html::chars( $_REQUEST['keyword'] ) ) );
        $count_company_list        = count( $count_company_list );
        //get form submit request
        $search_post               = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( isset( $search_post ) && $_REQUEST ) {
            $all_company_list = $manage_company->get_all_assigntaxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_assigntaxi' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'CompanyList', $CompanyList )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_active_packagereport_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_assigntaxi_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/assigntaxi" ); //transaction/index
    }
    public function action_block_packagereport_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_assigntaxi_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/assigntaxi" ); //transaction/index
    }
    public function action_trash_motor_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        //default empty list and offset
        $search_list = '';
        $offset      = '';
        $manage      = Model::factory( 'manage' );
        $status      = $manage->trash_motor_request( $_REQUEST['uniqueId'] );
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests has been deleted' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/motor" );
    }
    public function action_trash_company_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage = Model::factory( 'manage' );
        $status = $manage->trash_company_request( $_REQUEST['uniqueId'] );
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests has been deleted' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_trash_field_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage = Model::factory( 'manage' );
        $status = $manage->trash_field_request( $_REQUEST['uniqueId'] );
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests has been deleted' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/field" );
    }
    public function action_trash_package_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage = Model::factory( 'manage' );
        $status = $manage->trash_package_request( $_REQUEST['uniqueId'] );
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests has been deleted' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_trash_model_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'DA' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $this->page_title          = __( 'manage_contactus' );
        $this->selected_page_title = __( 'manage_contactus' );
        $manage_contacts           = Model::factory( 'manage' );
        $manage                    = Model::factory( 'manage' );
		
		$status = $manage->trash_model_request( $_REQUEST['uniqueId'] );
        if($status == 1){
			# send mail to company admins
			$this->send_companymail($_REQUEST['uniqueId']);
			Message::success(__( 'Checked requests has been deleted' ));
		}
		else if($status == -1){
			Message::error(__('atleast_onemodel'));
		}else{
			Message::error(__('driver_has_trips'));
		}
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_trash_manager_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $this->is_login();
        $manage = Model::factory( 'manage' );
        $status = $manage->trash_manager_request( $_REQUEST['uniqueId'] );
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests has been deleted' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_trash_admin_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $this->is_login();
        $manage = Model::factory( 'manage' );
        $status = $manage->trash_admin_request( $_REQUEST['uniqueId'] );
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests has been deleted' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_trash_taxi_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $this->is_login();
        $manage         = Model::factory( 'manage' );
        $isTaxiAssigned = $manage->istaxiassigned( $_REQUEST['uniqueId'] );
        if ( count( $isTaxiAssigned ) == 0 ) {
            $status = $manage->trash_taxi_request( $_REQUEST['uniqueId'] );
            Message::success( __( 'Checked requests has been deleted' ) );
        } else {
            Message::error( __( 'assigned_taxi_not_delete' ) );
        }
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_trash_driver_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $this->is_login();
        $manage           = Model::factory( 'manage' );
        $isDriverAssigned = $manage->isdriverassigned( $_REQUEST['uniqueId'] );
        if ( count( $isDriverAssigned ) == 0 ) {
            $status = $manage->trash_driver_request( $_REQUEST['uniqueId'] );
            Message::success( __( 'Checked requests has been deleted' ) );
        } else {
            Message::error( __( 'assigned_driver_not_delete' ) );
        }
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_contacts_search()
    {
        $user_createdby            = $_SESSION['userid'];
        //Page Title
        $this->page_title          = __( 'manage_contactus_search' );
        $this->selected_page_title = __( 'manage_contactus_search' );
        $cid                       = $_SESSION['company_id'];
        $usertype                  = $_SESSION['user_type'];
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //import model
        $manage_contacts           = Model::factory( 'manage' );
        //get form submit request
        $search_post               = arr::get( $_REQUEST, 'search_user' );
        $post                      = Securityvalid::sanitize_inputs( Arr::map( 'trim', $this->request->query() ) );
        $keyword                   = $post["keyword"];
        if ( $usertype != 'C' )
            $count_contacts_list = $manage_contacts->get_all_contacts_searchlist_count( trim( Html::chars( $keyword ) ) );
        else
            $count_contacts_list = $manage_contacts->get_all_contacts_searchlist_count( trim( Html::chars( $keyword ) ), $cid );
        //pagination loads here
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset   = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_contacts_list,
            'view' => 'pagination/punbb' 
        ) );
        //Post results for search 
        if ( isset( $search_post ) && $_REQUEST ) {
            if ( $usertype != 'C' )
                $all_contacts_list = $manage_contacts->get_all_contacts_searchlist( trim( Html::chars( $keyword ) ) );
            else
                $all_contacts_list = $manage_contacts->get_all_contacts_searchlist( trim( Html::chars( $keyword ) ), $cid );
        }
        //set data to view file    
        $view                       = View::factory( 'admin/manage_contacts' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'ContactsList', $ContactsList )->bind( 'all_contacts_list', $all_contacts_list );
        $this->template->title      = SITENAME . " | " . __( 'manage_contactus_search' );
        $this->template->page_title = __( 'manage_contactus_search' );
        $this->template->content    = $view;
    }
    public function action_contacts()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            //$this->request->redirect("company/login");
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $this->page_title          = __( 'manage_contactus' );
        $this->selected_page_title = __( 'manage_contactus' );
        $cid                       = $_SESSION['company_id'];
        $manage_contacts           = Model::factory( 'manage' );
        if ( $usertype != 'C' )
            $count_contacts_list = $manage_contacts->count_contacts_list();
        else
            $count_contacts_list = $manage_contacts->count_contacts_list( $cid );
        //pagination loads here
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset   = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_contacts_list,
            'view' => 'pagination/punbb' 
        ) );
        if ( $usertype != 'C' )
            $all_contacts_list = $manage_contacts->all_contacts_list( $offset, REC_PER_PAGE );
        else
            $all_contacts_list = $manage_contacts->all_contacts_list( $offset, REC_PER_PAGE, $cid );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/manage_contacts' )->bind( 'all_contacts_list', $all_contacts_list )->bind( 'pag_data', $pag_data )->bind( 'Offset', $offset )->bind( 'ContactsList', $ContactsList )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_contactus' );
        $this->template->page_title = __( 'manage_contactus' );
        $this->template->content    = $view;
    }
    public function action_contact_view()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $cid            = $_SESSION['company_id'];
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $this->page_title          = __( 'manage_contacts_view' );
        $this->selected_page_title = __( 'manage_contacts_view' );
        $id                        = $this->request->param( 'id' );
        $manage_contacts           = Model::factory( 'manage' );
        $ContactsList              = $manage_contacts->contacts_list_view( $id );
        if ( empty( $ContactsList ) || ( $usertype == 'C' && $cid != $ContactsList[0]['contact_cid'] ) ) {
            $this->request->redirect( "manage/contacts" );
        }
        $view                       = View::factory( 'admin/manage_contact_view' )->bind( 'pag_data', $pag_data )->bind( 'ContactsList', $ContactsList )->bind( 'Offset', $offset )->bind( 'usertype', $usertype );
        $this->template->title      = SITENAME . " | " . __( 'manage_contacts_view' );
        $this->template->page_title = __( 'manage_contacts_view' );
        $this->template->content    = $view;
    }
    public function action_delete_contacts()
    {
        $id              = $this->request->param( 'id' );
        $delete_contacts = Model::factory( 'manage' );
        $contacts        = $delete_contacts->delete_contacts( $id );
        if ( $contacts ) {
            Message::success( __( 'Contact was deleted.' ) );
            $this->request->redirect( $_SERVER['HTTP_REFERER'] );
        }
    }
    /** user info details **/
    public function action_userinfo()
    {
        $user_createdby  = $_SESSION['userid'];
        $usertype        = $_SESSION['user_type'];
        $id              = $this->request->param( 'id' );
        $view_controller = Model::factory( 'manage' );
        $user_details    = $view_controller->details_userinfo( $id );
        if ( empty( $user_details ) ) {
            $this->request->redirect( "manageusers/index" );
        }
        $view                       = View::factory( 'admin/userinfo' )->bind( 'pag_data', $pag_data )->bind( 'user_details', $user_details )->bind( 'Offset', $offset );
        $this->page_title           = __( 'user_inform' );
        $this->template->title      = SITENAME . " | " . __( 'user_inform' );
        $this->template->page_title = __( 'user_inform' );
        $this->template->content    = $view;
    }
    public function action_admin_userinfo()
    {
        $user_createdby             = $_SESSION['userid'];
        $usertype                   = $_SESSION['user_type'];
        $id                         = $this->request->param( 'id' );
        $view_controller            = Model::factory( 'manage' );
        $user_details               = $view_controller->details_userinfo( $id );
        $view                       = View::factory( 'admin/admin_userinfo' )->bind( 'pag_data', $pag_data )->bind( 'user_details', $user_details )->bind( 'Offset', $offset );
        $this->page_title           = __( 'user_inform' );
        $this->template->title      = SITENAME . " | " . __( 'user_inform' );
        $this->template->page_title = __( 'user_inform' );
        $this->template->content    = $view;
    }
    /** passenger info details **/
    public function action_passengerinfo()
    {
        $user_createdby  = $_SESSION['userid'];
        $usertype        = $_SESSION['user_type'];
        $id              = $this->request->param( 'id' );
        $view_controller = Model::factory( 'manage' );
        $user_details    = $view_controller->details_passengerinfo( $id );
        if ( count( $user_details ) == 0 ) {
            $this->request->redirect( "manageusers/passengers" );
        }
        /***** Completed Transaction *********************/
        $passenger_logs_completed_transaction = $view_controller->get_passenger_completed_transaction( $id, 'R', 'A', '1', REC_PER_PAGE, 0, 1, '', '' );
        /**************************************************/
        $view                                 = View::factory( 'admin/passengerinfo' )->bind( 'pag_data', $pag_data )->bind( 'user_details', $user_details )->bind( 'user_account_details', $user_account_details )->bind( 'passenger_logs_completed_transaction', $passenger_logs_completed_transaction )->bind( 'Offset', $offset );
        $this->page_title                     = __( 'user_inform' );
        $this->template->title                = SITENAME . " | " . __( 'user_inform' );
        $this->template->page_title           = __( 'user_inform' );
        $this->template->content              = $view;
    }
    /** company info details **/
    public function action_companyinfo()
    {
        $user_createdby             = $_SESSION['userid'];
        $usertype                   = $_SESSION['user_type'];
        $id                         = $this->request->param( 'id' );
        $view_controller            = Model::factory( 'manage' );
        $user_details               = $view_controller->details_userinfo( $id );
        $view                       = View::factory( 'admin/companyinfo' )->bind( 'pag_data', $pag_data )->bind( 'user_details', $user_details )->bind( 'Offset', $offset );
        $this->page_title           = __( 'companyinformation' );
        $this->template->title      = SITENAME . " | " . __( 'companyinformation' );
        $this->template->page_title = __( 'companyinformation' );
        $this->template->content    = $view;
    }
    /** driver info details **/
    public function action_driverinfo()
    {
        $user_createdby  = $_SESSION['userid'];
        $usertype        = $_SESSION['user_type'];
        $company_id      = $_SESSION['company_id'];
        $country_id      = $_SESSION['country_id'];
        $state_id        = $_SESSION['state_id'];
        $city_id         = $_SESSION['city_id'];
        $id              = $this->request->param( 'id' );
        $view_controller = Model::factory( 'driver' );
        $manage          = Model::factory( 'manage' );
        $user_details1   = $manage->details_userinfo( $id, '1' );
        if ( empty( $user_details1 ) ) {
            $this->urlredirect->redirect( 'manage/driver' );
        }
        $dashstyles  = array(
            'public/admin/css/dashboard/bootstrap-responsive.css' => 'screen',
            'public/admin/css/dashboard/charisma-app.css' => 'screen',
            'public/admin/css/dashboard/jquery-ui-1.8.21.custom.css' => 'screen',
            'public/admin/css/dashboard/chosen.css' => 'screen',
            'public/admin/css/dashboard/uniform.default.css' => 'screen',
            'public/admin/css/dashboard/jquery.noty.css' => 'screen',
            'public/admin/css/dashboard/noty_theme_default.css' => 'screen',
            'public/admin/css/dashboard/jquery.iphone.toggle.css' => 'screen',
            'public/admin/css/dashboard/opa-icons.css' => 'screen',
            'public/admin/css/datepicker.css' => 'screen' 
        );
        $dashscripts = array(
             SCRIPTPATH . 'dashboard/jquery-ui-1.8.21.custom.min.js',
            SCRIPTPATH . 'dashboard/bootstrap-transition.js',
            SCRIPTPATH . 'dashboard/bootstrap-alert.js',
            SCRIPTPATH . 'dashboard/bootstrap-modal.js',
            SCRIPTPATH . 'dashboard/bootstrap-dropdown.js',
            SCRIPTPATH . 'dashboard/bootstrap-scrollspy.js',
            SCRIPTPATH . 'dashboard/bootstrap-tab.js',
            SCRIPTPATH . 'dashboard/bootstrap-tooltip.js',
            SCRIPTPATH . 'dashboard/bootstrap-popover.js',
            SCRIPTPATH . 'dashboard/bootstrap-button.js',
            SCRIPTPATH . 'dashboard/bootstrap-collapse.js',
            SCRIPTPATH . 'dashboard/bootstrap-tour.js',
            SCRIPTPATH . 'dashboard/jquery.cookie.js',
            SCRIPTPATH . 'dashboard/jquery.dataTables.min.js',
            SCRIPTPATH . 'dashboard/jquery.chosen.min.js',
            SCRIPTPATH . 'dashboard/jquery.noty.js',
            SCRIPTPATH . 'dashboard/jquery.iphone.toggle.js',
            SCRIPTPATH . 'dashboard/jquery.history.js',
            SCRIPTPATH . 'dashboard/charisma.js',
            SCRIPTPATH . 'highcharts.js',
            SCRIPTPATH . 'bootstrap-datepicker.js',
            SCRIPTPATH . 'kkcountdown.js',
            SCRIPTPATH . 'jquery-countdown.js' 
        );
        $driver_cmid = $user_details1[0]['company_id'];
        if ( $usertype == 'C' ) {
            if ( ( $company_id !== $user_details1[0]['company_id'] ) || ( $user_details1[0]['user_type'] != 'D' ) ) {
                $this->urlredirect->redirect( 'manage/driver' );
            }
        } else if ( $usertype == 'M' ) {
            if ( ( $company_id !== $user_details1[0]['company_id'] ) || ( $state_id !== $user_details1[0]['login_state'] ) || ( $city_id !== $user_details1[0]['login_city'] ) || ( $country_id !== $user_details1[0]['login_country'] ) || ( $user_details1[0]['user_type'] != 'D' ) ) {
                $this->urlredirect->redirect( 'manage/driver' );
            }
        }
        $user_details                  = $view_controller->get_driver_logs1( $id, 'R', 'A', '1', REC_PER_PAGE, '0' );
        $driver_profile                = $view_controller->get_my_profile_details( $id );
        $get_transaction               = $view_controller->get_trans_of_driver( $id, REC_PER_PAGE );
        $get_tot_trans_driver          = $view_controller->get_total_trans_driver( $id );
        $get_tot_ratings_driver        = $view_controller->get_total_ratings_driver( $id );
        $driver_logs_progress          = $driver_logs_upcoming = array();
        $driver_logs_progress_upcoming = $view_controller->driver_logs_progress_upcoming( $id, 'R', 'A', $driver_cmid );
        if ( !empty( $driver_logs_progress_upcoming ) ) {
            foreach ( $driver_logs_progress_upcoming as $up ) {
                if ( $up->travel_status == 0 ) {
                    $driver_logs_upcoming[] = $up;
                }
                if ( $up->travel_status == 2 ) {
                    $driver_logs_progress[] = $up;
                }
            }
        }
        # Getting all Completed driver logs
        $driver_logs_completed_transaction = $manage->get_driver_completed_transaction( $id, 'R', 'A', '1', REC_PER_PAGE, 0, 1, '', '' );
        $get_trip_statitics                = $view_controller->get_trip_statitics( $id );
        $total_trip_statitics              = $view_controller->getoverall_trip_statitics_count( $id );
        $count_get_driver_shift_logs       = $view_controller->count_get_driver_shift_logs( $id );
        $get_driver_shift_logs             = $view_controller->get_driver_shift_logs( $id, REC_PER_PAGE, '0' );
        $driver_tracking                   = $view_controller->get_my_trips( $id );
        //function to get withdraw requests
        $driverWithdrawReqs                = $manage->getDriverWithdrawRequests( $id );
        /** Driver referral list **/
        $get_driver_referral_list          = $view_controller->get_driver_referral_list( $id, REC_PER_PAGE, 0, true );
        $view                              = View::factory( 'admin/driverinfo' )->bind( 'pag_data', $pag_data )->bind( 'user_details', $user_details )->bind( 'user_details1', $user_details1 )->bind( 'driver_profile', $driver_profile )->bind( 'get_transaction', $get_transaction )->bind( 'get_tot_trans_driver', $get_tot_trans_driver )->bind( 'get_tot_ratings_driver', $get_tot_ratings_driver )->bind( 'get_trip_statitics', $get_trip_statitics )->bind( 'total_trip_statitics', $total_trip_statitics )->bind( 'driver_logs_progress', $driver_logs_progress )->bind( 'driver_logs_completed_transaction', $driver_logs_completed_transaction )->bind( 'driver_logs_upcoming', $driver_logs_upcoming )->bind( 'count_get_driver_shift_logs', $count_get_driver_shift_logs )->bind( 'get_driver_shift_logs', $get_driver_shift_logs )->bind( 'dashstyles', $dashstyles )->bind( 'dashscripts', $dashscripts )->bind( 'driver_tracking', $driver_tracking )->bind( 'driverWithdrawReqs', $driverWithdrawReqs )->bind( 'driverReferralList', $get_driver_referral_list )->bind( 'Offset', $offset );
        $this->page_title                  = __( 'rating_drivers' );
        $this->template->title             = SITENAME . " | " . __( 'driver_info' );
        $this->template->page_title        = __( 'driver_info' );
        $this->template->content           = $view;
    }
    public function action_driver_completed_logs()
    {
        $startdate                         = Commonfunction::ensureDatabaseFormat( $_POST['startdate'], 1 );
        $enddate                           = Commonfunction::ensureDatabaseFormat( $_POST['enddate'], 2 );
        $driver_id                         = $_POST['driver_id'];
        $manage                            = Model::factory( 'manage' );
        $driver_logs_completed_transaction = $manage->get_driver_completed_transaction( $driver_id, 'R', 'A', '1', REC_PER_PAGE, 0, 0, $startdate, $enddate );
        $html                              = "";
        if ( count( $driver_logs_completed_transaction ) > 0 ) {
            $html .= '<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
                        <thead>
                        <tr>
                            <td>#</td>
                            <td>' . __( 'trip_id' ) . '</td>
                            <td>' . __( 'passenger_name' ) . '</td>
                            <td><b>' . __( 'pickuploc_droploc' ) . '</b></td>
                            <td><b>' . __( 'pictup_date' ) . '</b></td>
                            <td><b>' . __( 'distance' ) . '</b></td>
                            <td><b>' . __( 'tax' ) . '</b></td>
                            <td><b>' . __( 'trip_total_fare' ) . '</b></td>
                            </tr>
                        </tr>
                        </thead>';
            $i = 1;
            foreach ( $driver_logs_completed_transaction as $values ) {
                $class            = ( $i % 2 == 1 ) ? "eventr" : "oddtr";
                $distance         = round( $values->distance, 2 ) . ' ' . $values->distance_unit;
                $current_fare     = round( $values->fare, 2 );
                $company_tax      = $values->company_tax;
                $currtotal        = $current_fare - $company_tax;
                $travel_status    = $values->travel_status;
                $company_currency = CURRENCY;
                if ( $travel_status == 0 ) {
                    $status    = __( 'critical' );
                    $row_solor = 'style="color:#00FF00;"';
                } elseif ( $travel_status == 1 ) {
                    $status    = __( 'completed' );
                    $row_solor = 'style="color:#00FF00;"';
                } elseif ( $travel_status == 2 ) {
                    $status    = __( 'inprogress' );
                    $row_solor = 'style="color:#0000FF;"';
                }
                if ( $travel_status == 3 ) {
                    $status    = __( 'start_to_pickup' );
                    $row_solor = 'style="color:#FFFF00;"';
                } elseif ( $travel_status == 4 ) {
                    $status    = __( 'cancel_by_passenger' );
                    $row_solor = 'style="color:#990066;"';
                } elseif ( $travel_status == 5 ) {
                    $status    = __( 'waiting_payment' );
                    $row_solor = 'style="color:#00FFFF;"';
                } elseif ( $travel_status == 6 ) {
                    $status    = __( 'missed' );
                    $row_solor = 'style="color:#FF6633;"';
                } elseif ( $travel_status == 7 ) {
                    $status    = __( 'dispatched' );
                    $row_solor = 'style="color:#003333;"';
                } elseif ( $travel_status == 8 ) {
                    $status    = __( 'cancelled' );
                    $row_solor = 'style="color:#990000;"';
                }
                $html .= '<tr class=' . $class . '>    
                    <td>' . $i . '</td>
                    <td>' . $values->passengers_log_id . '</td>
                    <td>' . ucfirst( $values->name ) . '</td>
                    <td><p ' . $row_solor . '>' . $values->current_location . '</p>
                    <p>' . $values->drop_location . '</p></td>
                    <td>' . Commonfunction::getDateTimeFormat( $values->pickup_time, 1 ) . '</td>
                    <td>' . $distance . '</td>
                    <td>' . $company_currency . $company_tax . '</td>
                    <td>' . $company_currency . $current_fare . '</td>
                    </tr>';
                $i++;
            }
            $html .= '</table><div align="left" class="new_button"> 
                <input type="button" name="gen_pdf" id="gen_pdf" value="' . __( 'gen_pdf' ) . '" title="' . __( 'gen_pdf' ) . '" onclick="gen_pdf(this.value)">                
                </div><div align="left" class="new_button"> 
                <input type="button" name="sendmail" id="sendmail" value="' . __( 'send_mail' ) . '" title="' . __( 'send_mail' ) . '" onclick="gen_pdf(this.value)"></div>    ';
        } else {
            $html .= "<div class='no_data'>" . __( 'no_data' ) . "</div>";
        }
        echo $html;
        exit;
    }
    public function action_passenger_completed_logs()
    {
        $startdate                         = Commonfunction::ensureDatabaseFormat( $_POST['startdate'], 1 );
        $enddate                           = Commonfunction::ensureDatabaseFormat( $_POST['enddate'], 2 );
        $passenger_id                      = $_POST['passenger_id'];
        $manage                            = Model::factory( 'manage' );
        $driver_logs_completed_transaction = $manage->get_passenger_completed_transaction( $passenger_id, 'R', 'A', '1', REC_PER_PAGE, 0, 0, $startdate, $enddate );
        $html                              = "";
        if ( count( $driver_logs_completed_transaction ) > 0 ) {
            $html .= '<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
                        <thead>
                        <tr>
                            <td>#</td>
                            <td>' . __( 'passenger_name' ) . '</td>
                            <td><b>' . __( 'pickuploc_droploc' ) . '</b></td>
                            <td><b>' . __( 'pictup_date' ) . '</b></td>
                            <td><b>' . __( 'distance' ) . '</b></td>
                            <td><b>' . __( 'tax' ) . '</b></td>
                            <td><b>' . __( 'trip_total_fare' ) . '</b></td>
                            </tr>
                        </tr>
                        </thead>';
            $i = 1;
            ( $i % 2 == 1 ) ? $class = "eventr" : $class = "oddtr";
            foreach ( $driver_logs_completed_transaction as $values ) {
                $distance         = round( $values->distance, 2 ) . ' ' . $values->distance_unit;
                $current_fare     = round( $values->fare, 2 );
                $company_tax      = $values->Taxamt;
                $travel_status    = $values->travel_status;
                $company_currency = CURRENCY;
                if ( $travel_status == 0 ) {
                    $status    = __( 'critical' );
                    $row_solor = 'style="color:#00FF00;"';
                } elseif ( $travel_status == 1 ) {
                    $status    = __( 'completed' );
                    $row_solor = 'style="color:#00FF00;"';
                } elseif ( $travel_status == 2 ) {
                    $status    = __( 'inprogress' );
                    $row_solor = 'style="color:#0000FF;"';
                }
                if ( $travel_status == 3 ) {
                    $status    = __( 'start_to_pickup' );
                    $row_solor = 'style="color:#FFFF00;"';
                } elseif ( $travel_status == 4 ) {
                    $status    = __( 'cancel_by_passenger' );
                    $row_solor = 'style="color:#990066;"';
                } elseif ( $travel_status == 5 ) {
                    $status    = __( 'waiting_payment' );
                    $row_solor = 'style="color:#00FFFF;"';
                } elseif ( $travel_status == 6 ) {
                    $status    = __( 'missed' );
                    $row_solor = 'style="color:#FF6633;"';
                } elseif ( $travel_status == 7 ) {
                    $status    = __( 'dispatched' );
                    $row_solor = 'style="color:#003333;"';
                } elseif ( $travel_status == 8 ) {
                    $status    = __( 'cancelled' );
                    $row_solor = 'style="color:#990000;"';
                }
                $html .= '<tr class=' . $class . '>    
                    <td>' . $i . '</td>
                    <td>' . ucfirst( $values->name ) . '</td>
                    <td><p ' . $row_solor . '>' . $values->current_location . '</p>
                    <p>' . $values->drop_location . '</p></td>
                    <td>' . Commonfunction::getDateTimeFormat( $values->pickup_time, 1 ) . '</td>
                    <td>' . $distance . '</td>                                
                    <td>' . $company_currency . $company_tax . '</td>
                    <td>' . $company_currency . $current_fare . '</td>                                                            
                    </tr>';
                $i++;
            }
            $html .= '</table><div align="left" class="new_button"> 
                <input type="button" name="gen_pdf" id="gen_pdf" value="' . __( 'gen_pdf' ) . '" title="' . __( 'gen_pdf' ) . '" onclick="gen_pdf(this.value)">                
                </div><div align="left" class="new_button"> 
                <input type="button" name="sendmail" id="sendmail" value="' . __( 'send_mail' ) . '" title="' . __( 'send_mail' ) . '" onclick="gen_pdf(this.value)"></div>    ';
        } else {
            $html .= "<div class='no_data'>" . __( 'no_data' ) . "</div>";
        }
        echo $html;
        exit;
    }
    // Generate PDF *******************/
    public function action_genpdf()
    {
        $url          = $_SERVER['HTTP_REFERER'];
        $split        = explode( '/', $url );
        $request_page = $split[4];
        $startdate    = Commonfunction::ensureDatabaseFormat( $_POST['userstartdate'], 1 );
        $enddate      = Commonfunction::ensureDatabaseFormat( $_POST['userenddate'], 2 );
        $driver_id    = $_POST['user_id'];
        $driver_name  = $_POST['user_name'];
        $type         = $_POST['type_export'];
        $user_type    = $_POST['user_type'];
        $manage       = Model::factory( 'manage' );
        $driver       = Model::factory( 'driver' );
        $passengers   = Model::factory( 'passengers' );
        if ( $request_page != 'passengerinfo' ) {
            $user_details1 = $manage->details_userinfo( $driver_id );
        }
        if ( $user_type == 'D' ) {
            $driver_details                    = $driver->get_driver_profile_details( $driver_id );
            $email                             = $driver_details['email'];
            $driver_logs_completed_transaction = $manage->get_driver_completed_transaction( $driver_id, 'R', 'A', '1', REC_PER_PAGE, 0, 0, $startdate, $enddate );
        } else {
            $passengers_details                = $passengers->get_passenger_profile_details( $driver_id );
            $email                             = $passengers_details[0]['email'];
            $driver_logs_completed_transaction = $manage->get_passenger_completed_transaction( $driver_id, 'R', 'A', '1', REC_PER_PAGE, 0, 0, $startdate, $enddate );
        }
        $Middle_html = "";
        $Endhtml     = "";
        $Tophtml     = '
            <style>
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
    </style>
    <table border="0" cellpadding="1" cellspacing="1">
 <tr>
   <td></td>
   <td><div class="invoice_head">' . date( "F j, Y" ) . '<h1>INVOICE</h1></div></td>
 </tr>
  <tr>';
        if ( $request_page != 'passengerinfo' ) {
            $Tophtml .= '<td align="left">' . $user_details1[0]['company_name'] . '</td>
  <td align="right">' . $user_details1[0]['name'] . '</td>';
        } else {
            $Tophtml .= '<td align="left"></td>
  <td align="right">' . $passengers_details[0]['name'] . '</td>';
        }
        $Tophtml .= '</tr>';
        if ( $request_page != 'passengerinfo' ) {
            $Tophtml .= '
  <tr>
   <td align="left" height="20" valign="middle">Tax Number</td>
      <td></td>
 </tr>';
        }
        $Tophtml .= '</table>';
        $trip_id_lable         = __( 'trip_id' );
        $passenger_name_lable  = __( 'passenger_name' );
        $pickuploc_lable       = __( 'Current_Location' );
        $droploc_lable         = __( 'Drop_Location' );
        $pictup_date_lable     = __( 'pictup_date' );
        $distance_km_lable     = __( 'distance_km' );
        $fare_lable            = __( 'fare' );
        $tax_lable             = __( 'tax' );
        $trip_total_fare_lable = __( 'trip_total_fare' );
        $at_lable              = __( 'at' );
        $Middle_html .= '<table border="0" cellpadding="1" cellspacing="1" width="700">
                        <tr>
                            <td class="head_border" width=10>#</td>
                            <td class="head_border" width=130><b>' . $trip_id_lable . '</b></td>
                            <td class="head_border" width=130><b>' . $passenger_name_lable . '</b></td>
                            <td class="head_border" width=350><b>' . $pickuploc_lable . '</b></td>
                            <td class="head_border" width=350><b>' . $droploc_lable . '</b></td>
                            <td class="head_border" width=130><b>' . $pictup_date_lable . '</b></td>
                            <td class="head_border" width=30><b>' . $distance_km_lable . '</b></td>        
                            <td class="head_border" width=30><b>' . $fare_lable . '</b></td>    
                            <td class="head_border" width=10><b>' . $tax_lable . '</b></td>    
                            <td class="head_border" width=10><b>' . $trip_total_fare_lable . '</b></td>                                
                        </tr>';
        $i = 1;
        ( $i % 2 == 1 ) ? $class = "eventr" : $class = "oddtr";
        $rowdatas         = "";
        $tax_total        = "";
        $fare_total       = "";
        $company_currency = CURRENCY;
        foreach ( $driver_logs_completed_transaction as $values ) {
            $distance         = round( $values->actual_distance, 2 );
            $current_fare     = round( $values->fare, 2 );
            $company_tax      = $values->Taxamt;
            $currtotal        = ( $current_fare == 0 ) ? 0 : $current_fare - $company_tax;
            $travel_status    = $values->travel_status;
            $current_location = $values->current_location;
            $drop_location    = $values->drop_location;
            $passenger_name   = $values->name;
            $trip_id          = $values->passengers_log_id;
            $pickup_time      = Commonfunction::getDateTimeFormat( $values->pickup_time, 1 );
            $company_currency = CURRENCY;
            if ( $travel_status == 0 ) {
                $status    = __( 'critical' );
                $row_solor = 'style="color:#00FF00;"';
            } elseif ( $travel_status == 1 ) {
                $status    = __( 'completed' );
                $row_solor = 'style="color:#00FF00;"';
            } elseif ( $travel_status == 2 ) {
                $status    = __( 'inprogress' );
                $row_solor = 'style="color:#0000FF;"';
            }
            if ( $travel_status == 3 ) {
                $status    = __( 'start_to_pickup' );
                $row_solor = 'style="color:#FFFF00;"';
            } elseif ( $travel_status == 4 ) {
                $status    = __( 'cancel_by_passenger' );
                $row_solor = 'style="color:#990066;"';
            } elseif ( $travel_status == 5 ) {
                $status    = __( 'waiting_payment' );
                $row_solor = 'style="color:#00FFFF;"';
            } elseif ( $travel_status == 6 ) {
                $status    = __( 'missed' );
                $row_solor = 'style="color:#FF6633;"';
            } elseif ( $travel_status == 7 ) {
                $status    = __( 'dispatched' );
                $row_solor = 'style="color:#003333;"';
            } elseif ( $travel_status == 8 ) {
                $status    = __( 'cancelled' );
                $row_solor = 'style="color:#990000;"';
            }
            $Middle_html .= '<tr>    
                                <td width=10>' . $i . '</td>
                                <td width=130>' . $trip_id . '</td>
                                <td width=130>' . $passenger_name . '</td>
                                <td width=350>' . $current_location . '</td>
                                <td width=350>' . $drop_location . '</td>
                                <td width=130>' . $pickup_time . '</td>
                                <td width=30>' . $distance . '</td>
                                <td width=30>' . $currtotal . '</td>
                                <td width=10>' . $company_currency . ' ' . $company_tax . '</td>
                                <td width=10>' . $company_currency . ' ' . $current_fare . '</td>
                                </tr>';
            $fare_total += $current_fare;
            $tax_total += $company_tax;
            $i = $i + 1;
        }
        $Middle_html .= '';
        $Middle_html .= "</table>";
        $Endhtml .= '<table border="0" cellpadding="1" cellspacing="1" width="700"><tr>    
                                <td width=10></td>
                                <td width=130></td>
                                <td width=350></td>
                                <td width=130></td>
                                <td width=30></td>
                                <td width=30>Total</td>
                                <td width=10 class="taxstyle" height="25">' . $company_currency . ' ' . $tax_total . '</td>
                                <td width=10 class="totalstyle" height="25">' . $company_currency . ' ' . $fare_total . '</td>                                                            
                                </tr></table>';
        $html = $Tophtml . $Middle_html . $Endhtml; //
        $html = preg_replace( "<tbody>", " ", $html );
        $html = preg_replace( "</tbody>", " ", $html );
        ob_clean();
        $filename = __( 'INVOICE' ) . '-' . $driver_name . '-' . date( 'm-d-y-s' );
        if ( $type == __( 'gen_pdf' ) ) {
            $generate_pdf = $manage->generate_pdf( $html, $filename );
        } else {
            $filepath     = $_SERVER['DOCUMENT_ROOT'] . "/public/" . UPLOADS . "/driver_invoice/" . $filename;
            $generate_pdf = $manage->send_pdf( $html, $driver_name, $email, $filepath );
            if ( $generate_pdf == 1 ) {
                $mail              = "";
                $replace_variables = array(
                     REPLACE_LOGO => EMAILTEMPLATELOGO,
                    REPLACE_SITENAME => $this->app_name,
                    REPLACE_USERNAME => $driver_name,
                    REPLACE_SITELINK => URL_BASE . 'users/contactinfo/',
                    REPLACE_SITEEMAIL => $this->siteemail,
                    REPLACE_SITEURL => URL_BASE,
                    REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                    REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
                );
                $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'driver_invoice.html', $replace_variables );
                $to                = $email; //'senthilkumar.a@ndot.in';//
                $from              = $this->siteemail;
                $subject           = __( 'driver_invoice' ) . ' ' . $this->app_name;
                $redirect          = ( $user_type == 'D' ) ? "manage/driverinfo/" . $driver_id : "manage/passengerinfo/" . $driver_id;
                $attachment        = $filepath . '.pdf';
                $mail_model        = Model::factory( 'add' );
                $smtp_result       = $mail_model->smtp_settings();
                if ( !empty( $smtp_result ) && ( $smtp_result[0]['smtp'] == 1 ) ) {
                    include( $_SERVER['DOCUMENT_ROOT'] . "/modules/SMTP/smtp.php" );
                } else {
                    $email_from    = 'maheswaran.r@ndot.in'; // Who the email is from
                    $email_subject = 'driver invoice'; // The Subject of the email
                    $email_message = 'driver invoice';
                    $email_to      = 'senthilkumar.a@ndot.in'; // Who the email is to
                    $headers       = "From: " . $email_from;
                    $semi_rand     = md5( time() );
                    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
                    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
                    $email_message .= "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type:text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $email_message .= "\n\n";
                    $fileatt      = $filename; // Path to the file
                    $fileatt_type = "application/pdf"; // File Type
                    $file         = fopen( $fileatt, 'rb' );
                    $data         = fread( $file, filesize( $fileatt ) );
                    fclose( $file );
                    $data         = chunk_split( base64_encode( $data ) );
                    $fileatt_name = 'voucher' . md5( time() ) . ".pdf";
                    $email_message .= "--{$mime_boundary}\n" . "Content-Type: {$fileatt_type};\n" . " name=\"{$fileatt_name}\"\n" . 
                        "Content-Transfer-Encoding: base64\n\n" . $data .= "\n\n";
                    $email_message .= "--{$mime_boundary}\n" . $ok = @mail( $email_to, $email_subject, $email_message, $headers );
                    // To send HTML mail, the Content-type header must be set      
                }
                Message::success( __( 'invoice_send' ) );
                $this->request->redirect( URL_BASE . $redirect );
            } else {
                $this->request->redirect( "manage/driver/" . $driver_id . "" );
            }
        }
    }
    public function action_driverlogs()
    {
        $id                        = $this->request->param( 'id' );
        $view_model                = Model::factory( 'driver' );
        $this->selected_page_title = __( 'manage_content' );
        //pagination loads here
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        $count_driver_logs         = $view_model->count_get_driver_logs_service( $id );
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_driver_logs,
            'view' => 'pagination/punbb' 
        ) );
        $driver_logs_service        = $view_model->get_driver_logs_service( $id, REC_PER_PAGE, $offset );
        $view                       = View::factory( 'admin/driverlogs' )->bind( 'pag_data', $pag_data )->bind( 'driver_logs_service', $driver_logs_service );
        $this->page_title           = __( 'service_time' );
        $this->template->title      = SITENAME . " | " . __( 'service_time' );
        $this->template->page_title = __( 'service_time' );
        $this->template->content    = $view;
    }
    public function action_drivershifthistory()
    {
        $id                          = $this->request->param( 'id' );
        $view_model                  = Model::factory( 'driver' );
        $this->selected_page_title   = __( 'manage_content' );
        //pagination loads here
        $page_no                     = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        $count_get_driver_shift_logs = $view_model->count_get_driver_shift_logs( $id );
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_get_driver_shift_logs,
            'view' => 'pagination/punbb' 
        ) );
        $driver_shift_logs          = $view_model->get_driver_shift_logs( $id, REC_PER_PAGE, $offset );
        $view                       = View::factory( 'admin/drivershifts' )->bind( 'pag_data', $pag_data )->bind( 'Offset', $offset )->bind( 'driver_shift_logs', $driver_shift_logs );
        $this->page_title           = __( 'shift_history' );
        $this->template->title      = SITENAME . " | " . __( 'shift_history' );
        $this->template->page_title = __( 'shift_history' );
        $this->template->content    = $view;
    }
    public function action_taxilogs()
    {
        $id                        = $this->request->param( 'id' );
        $view_model                = Model::factory( 'driver' );
        $this->selected_page_title = __( 'manage_content' );
        //pagination loads here
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        $count_driver_logs         = $view_model->count_get_taxi_logs_service( $id );
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_driver_logs,
            'view' => 'pagination/punbb' 
        ) );
        $taxi_logs_service          = $view_model->get_taxi_logs_service( $id, REC_PER_PAGE, $offset );
        $view                       = View::factory( 'admin/taxilogs' )->bind( 'pag_data', $pag_data )->bind( 'taxi_logs_service', $taxi_logs_service );
        $this->page_title           = __( 'service_time' );
        $this->template->title      = SITENAME . " | " . __( 'service_time' );
        $this->template->page_title = __( 'service_time' );
        $this->template->content    = $view;
    }
    public function action_taxiinfo()
    {
        $user_createdby  = $_SESSION['userid'];
        $usertype        = $_SESSION['user_type'];
        $company_id      = $_SESSION['company_id'];
        $country_id      = $_SESSION['country_id'];
        $state_id        = $_SESSION['state_id'];
        $city_id         = $_SESSION['city_id'];
        $id              = $this->request->param( 'id' );
        $view_controller = Model::factory( 'manage' );
        $view_model      = Model::factory( 'driver' );
        $taxi_details    = $view_controller->details_taxiinfo( $id );
        //redirect to list page
        if ( count( $taxi_details ) == 0 ) {
            $this->request->redirect( "manage/taxi" );
        }
        $tmid = $view_controller->check_taxicompanyid( $id );
        if ( $usertype == 'C' ) {
            if ( $company_id != $tmid[0]['taxi_company'] ) {
                $this->urlredirect->redirect( 'company/dashboard' );
            }
        } else if ( $usertype == 'M' ) {
            if ( ( $company_id != $tmid[0]['taxi_company'] ) || ( $state_id != $tmid[0]['taxi_state'] ) || ( $city_id != $tmid[0]['taxi_city'] ) || ( $country_id != $tmid[0]['taxi_country'] ) ) {
                $this->urlredirect->redirect( 'manager/dashboard' );
            }
        }
        $taxi_driver = $view_controller->mapping_driver_details( $taxi_details[0]['taxi_id'] );
        if ( $taxi_driver ) {
            $user_details1 = $taxi_driver;
        }
        $count_taxi_logs_completed_transaction = count( $view_model->get_taxi_logs_completed_transaction( $id, 'R', 'A', '1' ) );
        $taxi_logs_completed_transaction       = $view_model->get_taxi_logs_completed_transaction( $id, 'R', 'A', '1', REC_PER_PAGE, 0 );
        $driver_tracking                       = $view_model->get_taxi_trips( $id );
        $companyid_curr                        = ( $_SESSION['company_id'] != 0 ) ? $_SESSION['company_id'] : $taxi_details[0]['taxi_company'];
        $company_curr                          = $view_controller->findcompany_currency( $companyid_curr );
        $company_currency                      = !empty( $company_curr ) ? $company_curr[0]['currency_symbol'] : '';
        $company_currency_format               = !empty( $company_curr ) ? $company_curr[0]['currency_code'] : '';
        $view                                  = View::factory( 'admin/taxiinfo' )->bind( 'pag_data', $pag_data )->bind( 'additional_fields', $additional_fields )->bind( 'taxi_details', $taxi_details )->bind( 'taxi_driver', $taxi_driver )->bind( 'user_details1', $user_details1 )->bind( 'count_taxi_logs_service', $count_taxi_logs_service )->bind( 'taxi_logs_service', $taxi_logs_service )->bind( 'count_taxi_logs_completed_transaction', $count_taxi_logs_completed_transaction )->bind( 'taxi_logs_completed_transaction', $taxi_logs_completed_transaction )->bind( 'Offset', $offset )->bind( 'company_currency', $company_currency )->bind( 'company_currency_format', $company_currency_format )->bind( 'driver_tracking', $driver_tracking );
        $this->page_title                      = __( 'taxi_inform' );
        $this->template->title                 = SITENAME . " | " . __( 'taxi_inform' );
        $this->template->page_title            = __( 'taxi_inform' );
        $this->template->content               = $view;
    }
    public function action_contents()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $this->page_title          = __( 'manage_content' );
        $this->selected_page_title = __( 'manage_content' );
        $manage_content            = Model::factory( 'manage' );
        $count_content_list        = $manage_content->count_content_list();
        //pagination loads here
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_content_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_content_list           = $manage_content->all_content_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/manage_content' )->bind( 'all_content_list', $all_content_list )->bind( 'pag_data', $pag_data )->bind( 'Offset', $offset )->bind( 'ContentList', $ContentList )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_content' );
        $this->template->page_title = __( 'manage_content' );
        $this->template->content    = $view;
    }
    public function action_company_contents()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'A' || $usertype == 'C' ) {
            //$this->request->redirect("company/login");
        } else {
            $this->request->redirect( "admin/login" );
        }
        $cid                       = $_SESSION['company_id'];
        $this->page_title          = __( 'manage_content' );
        $this->selected_page_title = __( 'manage_content' );
        $manage_content            = Model::factory( 'manage' );
        $count_content_list        = $manage_content->count_company_content_list( $cid );
        //pagination loads here
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_content_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_content_list           = $manage_content->all_company_content_list( $offset, REC_PER_PAGE, $cid );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/manage_company_content' )->bind( 'all_content_list', $all_content_list )->bind( 'pag_data', $pag_data )->bind( 'Offset', $offset )->bind( 'ContentList', $ContentList )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_content' );
        $this->template->page_title = __( 'manage_content' );
        $this->template->content    = $view;
    }
    public function action_content_view()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $this->page_title           = __( 'manage_content_view' );
        $this->selected_page_title  = __( 'manage_content_view' );
        $id                         = $this->request->param( 'id' );
        $manage_content             = Model::factory( 'manage' );
        $ContactsList               = $manage_content->content_list_view( $id );
        $view                       = View::factory( 'admin/manage_content_view' )->bind( 'pag_data', $pag_data )->bind( 'ContactsList', $ContactsList )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_content_view' );
        $this->template->page_title = __( 'manage_content_view' );
        $this->template->content    = $view;
    }
    //for deleting contents
    public function action_delete_content()
    {
        $id                   = $this->request->param( 'id' );
        $delete_ratingdrivers = Model::factory( 'manage' );
        $ratingdrivers        = $delete_ratingdrivers->delete_content( $id );
        if ( $ratingdrivers ) {
            Message::success( __( 'Content was deleted.' ) );
            //$this->request->redirect("manage/contents");
            $this->request->redirect( $_SERVER['HTTP_REFERER'] );
        }
    }
    public function action_status_company_content()
    {
        $status = $this->request->param( 'id' );
        $manage = Model::factory( 'manage' );
        $manage->company_content_request_change( $_REQUEST['uniqueId'], $status );
        //Flash message for Reject
        //==========================
        if ( $status == 1 )
            Message::success( __( 'Checked requests have been changed to activated status.' ) );
        else if ( $status == 0 )
            Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        $this->request->redirect( "manage/company_contents" );
    }
    //for edit content view 
    public function action_content_edit_view()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $this->page_title          = __( 'edit_content_view' );
        $this->selected_page_title = __( 'edit_content_view' );
        $id                        = $this->request->param( 'id' );
        $manage_content            = Model::factory( 'manage' );
        /** Select menus **/
        $menu_details              = $manage_content->get_menus();
        $signup_submit             = arr::get( $_REQUEST, 'submit_addcompany' );
        $ContactsList              = $manage_content->content_list_view( $id );
        //redirect to list page if there is no content
        if ( count( $ContactsList ) == 0 ) {
            $this->request->redirect( "manage/contents" );
        }
        $errors     = array();
        $postvalues = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = Arr::map( 'trim', $this->request->post() );
            $validator   = $manage_content->validate_editview( arr::extract( $post_values, array(
                 'menu_name',
                'meta_title',
                'meta_keyword',
                'meta_description' 
            ) ) );
            if ( $validator->check() ) {
                $menu_name_exits = $manage_content->menu_name_exits( $post_values, $id );
                if ( $menu_name_exits == 1 ) {
                    Message::error( __( 'content_already_exits' ) );
                    $this->request->redirect( "manage/contents" );
                }
                $signup_id = $manage_content->update_editview_content( $post_values, $id );
                if ( $signup_id == 1 ) {
                    Message::success( __( 'profile_updated_successfully' ) );
                    $this->request->redirect( "manage/contents" );
                } else {
                    Message::error( __( 'not_updated' ) );
                    $this->request->redirect( "manage/contents" );
                }
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                       = View::factory( 'admin/edit_content_view' )->bind( 'errors', $errors )->bind( 'pag_data', $pag_data )->bind( 'postvalue', $postvalues )->bind( 'company_details', $ContactsList )->bind( 'Offset', $offset )->bind( 'menu_details', $menu_details );
        $this->template->title      = SITENAME . " | " . __( 'edit_content_view' );
        $this->template->page_title = __( 'edit_content_view' );
        $this->template->content    = $view;
    }
    //for edit content view 
    public function action_company_content_edit()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'A' || $usertype == 'C' ) {
        } else {
            $this->request->redirect( "manager/login" );
        }
        $this->page_title          = __( 'edit_content_view' );
        $this->selected_page_title = __( 'edit_content_view' );
        $id                        = $this->request->param( 'id' );
        $manage_content            = Model::factory( 'manage' );
        $ContactsList              = $manage_content->company_content_list_view( $id );
        $cid                       = $_SESSION['company_id'];
        if ( $usertype == 'C' && $ContactsList[0]['company_id'] != $cid ) {
            Message::error( __( 'company_content_error_msg' ) );
            $this->request->redirect( "manage/company_contents" );
        }
        /** Select menus **/
        $menu_details  = $manage_content->get_menus();
        $signup_submit = arr::get( $_REQUEST, 'submit_addcompany' );
        $errors        = array();
        $postvalues    = array();
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $postvalues = $_POST;
            $post       = Arr::map( 'trim', $this->request->post() );
            $validator  = $manage_content->validate_companyeditview( arr::extract( $post, array(
                 'menu_name',
                'page_title',
                'page_url' 
            ) ), $cid, $id );
            if ( $validator->check() ) {
                $signup_id = $manage_content->update_edit_company_content( $post, $id );
                Message::success( __( 'profile_updated_successfully' ) );
                $this->request->redirect( "manage/company_contents" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $ContactsList               = $manage_content->company_content_list_view( $id );
        $view                       = View::factory( 'admin/edit_content_company' )->bind( 'errors', $errors )->bind( 'pag_data', $pag_data )->bind( 'postvalue', $postvalues )->bind( 'company_details', $ContactsList )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'edit_content_view' );
        $this->template->page_title = __( 'edit_content_view' );
        $this->template->content    = $view;
    }
    public function action_companydetails()
    {
        $user_createdby  = $_SESSION['userid'];
        $usertype        = $_SESSION['user_type'];
        $company_id      = $_SESSION['company_id'];
        $view_controller = Model::factory( 'manage' );
        $id              = $this->request->param( 'id' );
        $cmid            = $view_controller->check_companyid( $id );
        if ( count( $cmid ) > 0 ) {
            if ( $usertype == 'C' ) {
                if ( $company_id != $cmid[0]['cid'] ) {
                    $this->urlredirect->redirect( 'company/dashboard' );
                }
            } else if ( $usertype == 'M' ) {
                if ( $company_id != $cmid[0]['cid'] ) {
                    $this->urlredirect->redirect( 'manager/dashboard' );
                }
            }
        } else {
            if ( ( $usertype == 'C' ) ) {
                $this->urlredirect->redirect( 'company/dashboard' );
            } else if ( ( $usertype == 'M' ) ) {
                $this->urlredirect->redirect( 'manager/dashboard' );
            }
        }
        $user_details = $view_controller->details_userinfo( $id );
        //if invalid id is given redirect to manage page
        if ( count( $user_details ) == 0 ) {
            $this->request->redirect( "manage/company" );
        }
        $company_info    = array();
        $package_details = $view_controller->current_package_details( $cmid[0]['cid'] );
        if ( !empty( $package_details ) ) {
            foreach ( $package_details as $p ) {
                $company_info[] = array(
                     'company_domain' => $p['company_domain'],
                    'company_currency' => $p['company_currency'] 
                );
            }
        }
        $view                       = View::factory( 'admin/companydetails' )->bind( 'pag_data', $pag_data )->bind( 'user_details', $user_details )->bind( 'id', $id )->bind( 'package_details', $package_details )->bind( 'Offset', $offset )->bind( 'company_info', $company_info );
        $this->page_title           = __( 'companyinformation' );
        $this->template->title      = SITENAME . " | " . __( 'companyinformation' );
        $this->template->page_title = __( 'companyinformation' );
        $this->template->content    = $view;
    }
    /** getting manager details **/
    public function action_managerdetails()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $company_id     = $_SESSION['company_id'];
        if ( ( $usertype == 'M' ) ) {
            $this->urlredirect->redirect( 'manager/dashboard' );
        }
        $id              = $this->request->param( 'id' );
        $view_controller = Model::factory( 'manage' );
        $user_details    = $view_controller->details_userinfo( $id );
        if ( $usertype == 'C' ) {
            if ( isset( $user_details[0]['company_id'] ) ) {
                if ( ( $company_id != $user_details[0]['company_id'] ) || ( $user_details[0]['user_type'] != 'M' ) ) {
                    $this->urlredirect->redirect( 'company/dashboard' );
                }
            }
        }
        //if invalid id is given redirect to manage page
        if ( count( $user_details ) == 0 ) {
            $this->request->redirect( "manage/manager" );
        }
        $view                       = View::factory( 'admin/managerdetails' )->bind( 'pag_data', $pag_data )->bind( 'user_details', $user_details )->bind( 'id', $id )->bind( 'Offset', $offset );
        $this->page_title           = __( 'managerinformation' );
        $this->template->title      = SITENAME . " | " . __( 'managerinformation' );
        $this->template->page_title = __( 'managerinformation' );
        $this->template->content    = $view;
    }
    public function action_getcompanymanagerlist()
    {
        $manage_model  = Model::factory( 'manage' );
        $output        = '';
        $company_id    = arr::get( $_REQUEST, 'company_id' );
        $page_title    = __( 'manager_management' );
        $page_no       = arr::get( $_REQUEST, 'page' );
        $count_details = $manage_model->getcompanymanagerlist( $company_id );
        if ( $page_no )
            $offset = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/puncm' 
        ) );
        $getmodel_details = $manage_model->get_companymanagerlist( $company_id, $offset, REC_PER_PAGE );
        $count            = count( $getmodel_details );
        $output .= '<div class="widget">
            <div class="title"><h6>' . $page_title . '</h6>
            <div style="width:auto; float:right; margin:0;">';
        if ( $count > 5 ) {
            $output .= '<a class="export_me_menu" href="' . URL_BASE . 'manage/managersearch?keyword=&status=&filter_company=' . $company_id . '">View All</a>                       
            </div>';
        } else {
            $output .= '<div class="button greyish"></div></div>';
        }
        $output .= '</div>';
        if ( $count > 0 ) {
            $output .= '<div class= "overflow-block">';
        }
        $output .= '<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">';
        if ( $count > 0 ) {
            $output .= '<thead>
            <tr>
            <td align="left" width="5%" style="min-width: 22px !important;" >Status</td>
            <td align="left" width="5%">' . __( 'sno_label' ) . '</td>
            <td align="left" width="10%">' . ucfirst( __( 'manager_management' ) ) . '</td>
            <td align="left" width="10%">' . __( 'companyname' ) . '</td>
            <td align="left" width="10%">' . __( 'country_label' ) . '</td>
            <td align="left" width="10%">' . __( 'state_label' ) . '</td>
            <td align="left" width="10%">' . __( 'city_label' ) . '</td>
            </tr>
            </thead>
            <tbody>    ';
            $sno = $offset;
            /* For Serial No */
            foreach ( $getmodel_details as $listings ) {
                //S.No Increment
                //==============
                $sno++;
                //For Odd / Even Rows
                //===================
                $trcolor = ( $sno % 2 == 0 ) ? 'oddtr' : 'eventr';
                $output .= '<tr class="' . $trcolor . '">
            <td align="middle">';
                if ( $listings['status'] == 'A' ) {
                    $txt   = "Deactivate";
                    $class = "unsuspendicon";
                } else {
                    $txt   = "Activate";
                    $class = "blockicon";
                }
                $output .= '<a href="javascript:void(0);" title =' . $txt . ' class=' . $class . '></a>';
                $output .= '</td> 
            <td>' . $sno . '</td>
            <td><a href=' . URL_BASE . 'manage/managerdetails/' . $listings['id'] . '>' . wordwrap( ucfirst( $listings['name'] ), 30, '<br/>', 1 ) . '</a></td>
            <td>' . wordwrap( ucfirst( $listings['company_name'] ), 30, '<br/>', 1 ) . '</td>
            <td>' . wordwrap( $listings['country_name'], 25, '<br />', 1 ) . '</td>    
            <td>' . wordwrap( $listings['state_name'], 25, '<br />', 1 ) . '</td>                        
            <td>' . wordwrap( $listings['city_name'], 25, '<br />', 1 ) . '</td>
            </tr>';
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
        if ( $count > REC_PER_PAGE ) {
            $output .= '</div>';
            $output .= '</div><div class="clr">&nbsp;</div>';
            $output .= '<div class="pagination">';
            $output .= '<p>' . $pag_data->render() . '</p>';
            $output .= '</div><div class="clr">&nbsp;</div>';
        }
        echo $output;
        exit;
    }
    public function action_getcompanydriverlist()
    {
        $manage_model  = Model::factory( 'manage' );
        $output        = '';
        $company_id    = arr::get( $_REQUEST, 'company_id' );
        $page_title    = __( 'company_driver' );
        $page_no       = arr::get( $_REQUEST, 'page' );
        $count_details = $manage_model->getcompanydriverlist( $company_id );
        if ( $page_no )
            $offset = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/puncd' 
        ) );
        $getmodel_details = $manage_model->get_companydriverlist( $company_id, $offset, REC_PER_PAGE );
        $count            = count( $getmodel_details );
        $output .= '<div class="widget">
        <div class="title"><h6>' . $page_title . '</h6>
        <div style="width:auto; float:right; margin: 0;">';
        if ( $count > 5 ) {
            $output .= '<a class="export_me_menu" href="' . URL_BASE . 'manage/driversearch?keyword=&status=&filter_company=' . $company_id . '">View All</a>
        </div>';
        } else {
            $output .= '<div class="button greyish"></div></div>';
        }
        $output .= '</div>';
        if ( $count > 0 ) {
            $output .= '<div class= "overflow-block">';
        }
        $output .= '<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">';
        if ( $count > 0 ) {
            $output .= '<thead>
        <tr>
        <td align="left" width="5%" style="min-width: 22px !important;" >Status</td>
        <td align="left" width="5%">' . __( 'sno_label' ) . '</td>
        <td align="left" width="10%">' . ucfirst( __( 'driver_name' ) ) . '</td>
        <td align="left" width="10%">' . __( 'companyname' ) . '</td>
        <td align="left" width="10%">' . __( 'country_label' ) . '</td>
        <td align="left" width="10%">' . __( 'state_label' ) . '</td>
        <td align="left" width="10%">' . __( 'city_label' ) . '</td>
        </tr>
        </thead>
        <tbody>    ';
            $sno = $offset;
            /* For Serial No */
            foreach ( $getmodel_details as $listings ) {
                //S.No Increment
                //==============
                $sno++;
                //For Odd / Even Rows
                //===================
                $trcolor = ( $sno % 2 == 0 ) ? 'oddtr' : 'eventr';
                $output .= '<tr class="' . $trcolor . '">
            <td align="middle">';
                if ( $listings['status'] == 'A' ) {
                    $txt   = "Deactivate";
                    $class = "unsuspendicon";
                } else {
                    $txt   = "Activate";
                    $class = "blockicon";
                }
                $output .= '<a href="javascript:void(0);" title =' . $txt . ' class=' . $class . '></a>';
                $output .= '</td> 
            <td>' . $sno . '</td>
            <td><a href=' . URL_BASE . 'manage/driverinfo/' . $listings['id'] . '>' . wordwrap( ucfirst( $listings['name'] ), 30, '<br/>', 1 ) . '</a></td>
            <td>' . wordwrap( ucfirst( $listings['company_name'] ), 30, '<br/>', 1 ) . '</td>
            <td>' . wordwrap( $listings['country_name'], 25, '<br />', 1 ) . '</td>    
            <td>' . wordwrap( $listings['state_name'], 25, '<br />', 1 ) . '</td>                        
            <td>' . wordwrap( $listings['city_name'], 25, '<br />', 1 ) . '</td>

            </tr>';
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
        if ( $count > REC_PER_PAGE ) {
            $output .= '</div>';
            $output .= '</div><div class="clr">&nbsp;</div>';
            $output .= '<div class="pagination">';
            $output .= '<p>' . $pag_data->render() . '</p>';
            $output .= '</div><div class="clr">&nbsp;</div>';
        }
        echo $output;
        exit;
    }
    public function action_getcompanytaxilist()
    {
        $manage_model  = Model::factory( 'manage' );
        $output        = '';
        $company_id    = arr::get( $_REQUEST, 'company_id' );
        $page_title    = __( 'company_taxi' );
        $page_no       = arr::get( $_REQUEST, 'page' );
        $count_details = $manage_model->getcompanytaxilist( $company_id );
        if ( $page_no )
            $offset = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/punct' 
        ) );
        $getmodel_details = $manage_model->get_companytaxilist( $company_id, $offset, REC_PER_PAGE );
        $count            = count( $getmodel_details );
        $output .= '<div class="widget">
                <div class="title"><h6>' . $page_title . '</h6>
                <div style="width:auto; float:right; margin: 0;">';
        if ( $count > 5 ) {
            $output .= '<a class="export_me_menu" href="' . URL_BASE . 'manage/taxisearch?keyword=&status=&filter_company=' . $company_id . '">View All</a></div>';
        } else {
            $output .= '<div class="button greyish"></div> </div>';
        }
        $output .= '</div>';
        if ( $count > 0 ) {
            $output .= '<div class= "overflow-block">';
        }
        $output .= '<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">';
        if ( $count > 0 ) {
            $output .= '<thead>
                <tr>
                <td align="left" width="5%" style="min-width: 22px !important;" >Status</td>
                <td align="left" width="5%">' . __( 'sno_label' ) . '</td>
                <td align="left" width="10%">' . ucfirst( __( 'taxi_no' ) ) . '</td>
                <td align="left" width="10%">' . __( 'companyname' ) . '</td>
                <td align="left" width="10%">' . __( 'country_label' ) . '</td>
                <td align="left" width="10%">' . __( 'state_label' ) . '</td>
                <td align="left" width="10%">' . __( 'city_label' ) . '</td>
                </tr>
                </thead>
                <tbody>    ';
            $sno = $offset;
            /* For Serial No */
            foreach ( $getmodel_details as $listings ) {
                //S.No Increment
                //==============
                $sno++;
                //For Odd / Even Rows
                //===================
                $trcolor = ( $sno % 2 == 0 ) ? 'oddtr' : 'eventr';
                $output .= '<tr class="' . $trcolor . '">
                <td align="middle">';
                if ( $listings['taxi_status'] == 'A' ) {
                    $txt   = "Deactivate";
                    $class = "unsuspendicon";
                } else {
                    $txt   = "Activate";
                    $class = "blockicon";
                }
                $output .= '<a href="javascript:void(0);" title =' . $txt . ' class=' . $class . '></a>';
                $output .= '</td> 
                <td>' . $sno . '</td>
                <td><a href=' . URL_BASE . 'manage/taxiinfo/' . $listings['taxi_id'] . '>' . wordwrap( ucfirst( $listings['taxi_no'] ), 30, '<br/>', 1 ) . '</a></td>
                <td>' . wordwrap( ucfirst( $listings['company_name'] ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( $listings['country_name'], 25, '<br />', 1 ) . '</td>    
                <td>' . wordwrap( $listings['state_name'], 25, '<br />', 1 ) . '</td>                        
                <td>' . wordwrap( $listings['city_name'], 25, '<br />', 1 ) . '</td>

                </tr>';
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
        if ( $count > REC_PER_PAGE ) {
            $output .= '</div>';
            $output .= '</div><div class="clr">&nbsp;</div>';
            $output .= '<div class="pagination">';
            $output .= '<p>' . $pag_data->render() . '</p>';
            $output .= '</div><div class="clr">&nbsp;</div>';
        }
        echo $output;
        exit;
    }
    /** getting driver rating list **/
    public function action_getuserratinglist()
    {
        $manage_model  = Model::factory( 'manage' );
        $output        = '';
        $driver_id     = arr::get( $_REQUEST, 'user_id' );
        $page_title    = __( 'manage_rating_taxi' );
        $page_no       = arr::get( $_REQUEST, 'page' );
        $count_details = $manage_model->getuserratinglist( $driver_id );
        if ( $page_no )
            $offset = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/punrtu' 
        ) );
        $getmodel_details = $manage_model->get_userratinglist( $driver_id, $offset, REC_PER_PAGE );
        $count            = count( $getmodel_details );
        $output .= '<div class="widget">
                <div class="title"><h6>' . $page_title . '</h6>
                <div style="width:auto; float:right; margin: 0;">';
        if ( $count > 0 ) {
            $output .= '</div>';
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
                <td align="left" width="10%">' . __( 'Current_Location' ) . '</td>
                <td align="left" width="10%">' . __( 'Drop_Location' ) . '</td>
                <td align="left" width="10%">' . __( 'No_Passengers' ) . '</td>
                <td align="left" width="10%">' . __( 'pictup_date' ) . '</td>
                <td align="left" width="10%">' . __( 'pictup_time' ) . '</td>
                <td align="left" width="10%">' . __( 'rating_points' ) . '</td>
                <td align="left" width="10%">' . __( 'comments' ) . '</td>
                </tr>
                </thead>
                <tbody>    ';
            $sno = $offset;
            /* For Serial No */
            foreach ( $getmodel_details as $listings ) {
                //S.No Increment
                //==============
                $sno++;
                //For Odd / Even Rows
                //===================
                $trcolor = ( $sno % 2 == 0 ) ? 'oddtr' : 'eventr';
                $output .= '<tr class="' . $trcolor . '">';
                if ( $listings['rating'] == 0 ) {
                    $txt = "Not rated yet";
                } else {
                    $txt = $listings['rating'] . ' / 5';
                }
                if ( empty( $listings['comments'] ) ) {
                    $txt1 = "No comments";
                } else {
                    $txt1 = $listings['comments'];
                }
                $drop = "-";
                if ( $listings['drop_location'] )
                    $drop = wordwrap( ucfirst( $listings['drop_location'] ), 30, '<br/>', 1 );
                $no_passengers = "-";
                if ( $listings['no_passengers'] )
                    $no_passengers = wordwrap( ucfirst( $listings['no_passengers'] ), 30, '<br/>', 1 );
                $output .= '<td>' . $sno . '</td>
                <td>' . wordwrap( ucfirst( $listings['name'] ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( ucfirst( $listings['current_location'] ), 30, '<br/>', 1 ) . '</td>
                <td>' . $drop . '</td>
                <td>' . $no_passengers . '</td>
                <td>' . wordwrap( date( 'd/m/Y', strtotime( $listings['pickup_time'] ) ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( ucfirst( $listings['waitingtime'] ), 30, '<br/>', 1 ) . ' Mins</td>
                <td>' . $txt . '</td>
                <td>' . wordwrap( ucfirst( $txt1 ), 30, '<br/>', 1 ) . '</td>
                </tr>';
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
    /** getting driver rating list **/
    public function action_getdriverratinglist()
    {
        $manage_model  = Model::factory( 'manage' );
        $output        = '';
        $driver_id     = arr::get( $_REQUEST, 'driver_id' );
        $page_title    = __( 'manage_rating_taxi' );
        $page_no       = arr::get( $_REQUEST, 'page' );
        $count_details = $manage_model->getdriverratinglist( $driver_id );
        if ( $page_no )
            $offset = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/punrtd' 
        ) );
        $getmodel_details = $manage_model->get_driverratinglist( $driver_id, $offset, REC_PER_PAGE );
        $count            = count( $getmodel_details );
        $output .= '<div class="widget">
                <div class="title"><h6>' . $page_title . '</h6>
                <div style="width:auto; float:right; margin: 0;">';
        if ( $count > 0 ) {
            $output .= '<div class="button greyish"></div></div>';
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
                <td align="left" width="10%">' . ucfirst( __( 'passenger_name' ) ) . '</td>
                <td align="left" width="10%">' . __( 'Current_Location' ) . '</td>
                <td align="left" width="10%">' . __( 'Drop_Location' ) . '</td>
                <td align="left" width="10%">' . __( 'No_Passengers' ) . '</td>
                <td align="left" width="10%">' . __( 'pictup_date' ) . '</td>
                <td align="left" width="10%">' . __( 'pictup_time' ) . '</td>
                <td align="left" width="10%">' . __( 'waiting_time' ) . '</td>
                <td align="left" width="10%">' . __( 'rating_points' ) . '</td>
                <td align="left" width="10%">' . __( 'comments' ) . '</td>
                </tr>
                </thead>
                <tbody>    ';
            $sno = $offset;
            /* For Serial No */
            foreach ( $getmodel_details as $listings ) {
                //S.No Increment
                //==============
                $sno++;
                //For Odd / Even Rows
                //===================
                $trcolor = ( $sno % 2 == 0 ) ? 'oddtr' : 'eventr';
                $output .= '<tr class="' . $trcolor . '">';
                if ( $listings['rating'] == 0 ) {
                    $txt = "Not rated yet";
                } else {
                    $txt = $listings['rating'] . ' / 5';
                }
                if ( empty( $listings['comments'] ) ) {
                    $txt1 = "No comments";
                } else {
                    $txt1 = $listings['comments'];
                }
                $output .= '<td>' . $sno . '</td>
                <td>' . wordwrap( ucfirst( $listings['name'] ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( ucfirst( $listings['current_location'] ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( ucfirst( $listings['drop_location'] ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( ucfirst( $listings['no_passengers'] ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( date( 'd/m/Y', strtotime( $listings['pickup_time'] ) ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( date( 'H:i:s', strtotime( $listings['pickup_time'] ) ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( ucfirst( $listings['waitingtime'] ), 30, '<br/>', 1 ) . ' Mins</td>
                <td>' . $txt . '</td>
                <td>' . wordwrap( ucfirst( $txt1 ), 30, '<br/>', 1 ) . '</td>
                </tr>';
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
    /** getting manager driver list **/
    public function action_getmanagerdriverlist()
    {
        $manage_model  = Model::factory( 'manage' );
        $output        = '';
        $company_id    = arr::get( $_REQUEST, 'manager_id' );
        $page_title    = __( 'company_driver' );
        $page_no       = arr::get( $_REQUEST, 'page' );
        $count_details = $manage_model->getmanagerdriverlist( $company_id );
        if ( $page_no )
            $offset = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/punmd' 
        ) );
        $getmodel_details = $manage_model->get_managerdriverlist( $company_id, $offset, REC_PER_PAGE );
        $count            = count( $getmodel_details );
        $param_id         = $getmodel_details[0]['cid'];
        $output .= '<div class="widget">
                <div class="title"><h6>' . $page_title . '</h6>
                <div style="width:auto; float:right; margin:0;">';
        if ( $count > 5 ) {
            $output .= '<a class="export_me_menu" href="' . URL_BASE . 'manage/driversearch?keyword=&status=&filter_company=' . $param_id . '">View All</a>                       
                </div>';
        } else {
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
                <td align="left" width="5%" style="min-width: 22px !important;" >Status</td>
                <td align="left" width="5%">' . __( 'sno_label' ) . '</td>
                <td align="left" width="10%">' . ucfirst( __( 'driver_name' ) ) . '</td>
                <td align="left" width="10%">' . __( 'companyname' ) . '</td>
                <td align="left" width="10%">' . __( 'country_label' ) . '</td>
                <td align="left" width="10%">' . __( 'state_label' ) . '</td>
                <td align="left" width="10%">' . __( 'city_label' ) . '</td>
                </tr>
                </thead>
                <tbody>    ';
            /* For Serial No */
            $sno = $offset;
            foreach ( $getmodel_details as $listings ) {
                //S.No Increment
                //==============
                $sno++;
                //For Odd / Even Rows
                //===================
                $trcolor = ( $sno % 2 == 0 ) ? 'oddtr' : 'eventr';
                $output .= '<tr class="' . $trcolor . '">
                <td>';
                if ( $listings['status'] == 'A' ) {
                    $txt   = "Deactivate";
                    $class = "unsuspendicon";
                } else {
                    $txt   = "Activate";
                    $class = "blockicon";
                }
                $output .= '<a href="javascript:void(0);" title =' . $txt . ' class=' . $class . '></a>';
                $output .= '</td> 
                <td>' . $sno . '</td>
                <td><a href=' . URL_BASE . 'manage/driverinfo/' . $listings['id'] . '>' . wordwrap( ucfirst( $listings['name'] ), 30, '<br/>', 1 ) . '</a></td>
                <td>' . wordwrap( ucfirst( $listings['company_name'] ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( $listings['country_name'], 25, '<br />', 1 ) . '</td>    
                <td>' . wordwrap( $listings['state_name'], 25, '<br />', 1 ) . '</td>                        
                <td>' . wordwrap( $listings['city_name'], 25, '<br />', 1 ) . '</td>

                </tr>';
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
        if ( $count > REC_PER_PAGE ) {
            $output .= '</div>';
            $output .= '</div><div class="clr">&nbsp;</div>';
            $output .= '<div class="pagination">';
            $output .= '<p>' . $pag_data->render() . '</p>';
            $output .= '</div><div class="clr">&nbsp;</div>';
        }
        echo $output;
        exit;
    }
    /** getting manger taxi list**/
    public function action_getmanagertaxilist()
    {
        $manage_model  = Model::factory( 'manage' );
        $output        = '';
        $company_id    = arr::get( $_REQUEST, 'manager_id' );
        $page_title    = __( 'company_taxi' );
        $page_no       = arr::get( $_REQUEST, 'page' );
        $count_details = $manage_model->getmanagertaxilist( $company_id );
        if ( $page_no )
            $offset = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/punmt' 
        ) );
        $getmodel_details = $manage_model->get_managertaxilist( $company_id, $offset, REC_PER_PAGE );
        $count            = count( $getmodel_details );
        $param_id         = $getmodel_details[0]['cid'];
        $output .= '<div class="widget">
                <div class="title"><h6>' . $page_title . '</h6>
                <div style="width:auto; float:right; margin:0;">';
        if ( $count > 5 ) {
            $output .= '<a class="export_me_menu" href="' . URL_BASE . 'manage/taxisearch?keyword=&status=&filter_company=' . $param_id . '">View All</a>                       
                </div>';
        } else {
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
                <td align="left" width="5%" style="min-width: 22px !important;" >Status</td>
                <td align="left" width="5%">' . __( 'sno_label' ) . '</td>
                <td align="left" width="10%">' . ucfirst( __( 'taxi_no' ) ) . '</td>
                <td align="left" width="10%">' . __( 'companyname' ) . '</td>
                <td align="left" width="10%">' . __( 'country_label' ) . '</td>
                <td align="left" width="10%">' . __( 'state_label' ) . '</td>
                <td align="left" width="10%">' . __( 'city_label' ) . '</td>
                </tr>
                </thead>
                <tbody>    ';
            /* For Serial No */
            $sno = $offset;
            foreach ( $getmodel_details as $listings ) {
                //S.No Increment
                //==============
                $sno++;
                //For Odd / Even Rows
                //===================
                $trcolor = ( $sno % 2 == 0 ) ? 'oddtr' : 'eventr';
                $output .= '<tr class="' . $trcolor . '">
                <td>';
                if ( $listings['taxi_status'] == 'A' ) {
                    $txt   = "Deactivate";
                    $class = "unsuspendicon";
                } else {
                    $txt   = "Activate";
                    $class = "blockicon";
                }
                $output .= '<a href="javascript:void(0);" title =' . $txt . ' class=' . $class . '></a>';
                $output .= '</td> 
                <td>' . $sno . '</td>
                <td><a href=' . URL_BASE . 'manage/taxiinfo/' . $listings['taxi_id'] . '>' . wordwrap( ucfirst( $listings['taxi_no'] ), 30, '<br/>', 1 ) . '</a></td>
                <td>' . wordwrap( ucfirst( $listings['company_name'] ), 30, '<br/>', 1 ) . '</td>
                <td>' . wordwrap( $listings['country_name'], 25, '<br />', 1 ) . '</td>    
                <td>' . wordwrap( $listings['state_name'], 25, '<br />', 1 ) . '</td>
                <td>' . wordwrap( $listings['city_name'], 25, '<br />', 1 ) . '</td>

                </tr>';
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
        if ( $count > REC_PER_PAGE ) {
            $output .= '</div>';
            $output .= '</div><div class="clr">&nbsp;</div>';
            $output .= '<div class="pagination">';
            $output .= '<p>' . $pag_data->render() . '</p>';
            $output .= '</div><div class="clr">&nbsp;</div>';
        }
        echo $output;
        exit;
    }
    //Transactions without Search action 
    public function action_transaction()
    {
        $user_createdby         = $_SESSION['userid'];
        $usertype               = $_SESSION['user_type'];
        $manage_transaction     = Model::factory( 'manage' );
        $get_allcompany         = $manage_transaction->get_allcompany_tranaction( $usertype );
        $count_transaction_list = $manage_transaction->count_transaction_list( '', '', '' );
        //pagination loads here
        $page_no                = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_transaction_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_transaction_list       = $manage_transaction->transaction_details( '', '', '', $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/transactiondetails' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'id', $id );
        $this->page_title           = __( 'transaction_list' );
        $this->template->title      = SITENAME . " | " . __( 'transaction_list' );
        $this->template->page_title = __( 'transaction_list' );
        $this->template->content    = $view;
    }
    public function action_transaction_list()
    {
        $user_createdby         = $_SESSION['userid'];
        $usertype               = $_SESSION['user_type'];
        $company                = trim( Html::chars( $_REQUEST['filter_company'] ) );
        $startdate              = trim( Html::chars( $_REQUEST['startdate'] ) );
        $enddate                = trim( Html::chars( $_REQUEST['enddate'] ) );
        $manage_transaction     = Model::factory( 'manage' );
        $get_allcompany         = $manage_transaction->get_allcompany_tranaction();
        $count_transaction_list = $manage_transaction->count_transaction_list( $company, $startdate, $enddate );
        //pagination loads here
        $page_no                = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_transaction_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_transaction_list       = $manage_transaction->transaction_details( $company, $startdate, $enddate, $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/transactiondetails' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'id', $id );
        $this->page_title           = __( 'transaction_list' );
        $this->template->title      = SITENAME . " | " . __( 'transaction_list' );
        $this->template->page_title = __( 'transaction_list' );
        $this->template->content    = $view;
    }
    public function action_get_translist()
    {
        $manage_model  = Model::factory( 'manage' );
        $output        = '';
        $company_id    = arr::get( $_REQUEST, 'company_id' );
        $page_title    = __( 'transaction_list' );
        $page_no       = arr::get( $_REQUEST, 'page' );
        $count_details = $manage_model->getcompanytranslist( $company_id );
        if ( $page_no )
            $offset = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/punctl' 
        ) );
        $getmodel_details = $manage_model->get_translist( $company_id, $offset, REC_PER_PAGE );
        $count            = count( $getmodel_details );
        $output .= '<div class="widget">
                <div class="title"><h6>' . $page_title . '</h6>
                <div style="width:auto; float:right; margin: 0;">';
        if ( $count > 5 ) {
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
                <td align="left" width="10%">' . __( 'passenger_name' ) . '</td>
                <td align="left" width="10%">' . ucfirst( __( 'driver_name' ) ) . '</td>
                <td align="left" width="10%">' . __( 'companyname' ) . '</td>
                <td align="left" width="10%">' . __( 'email_label' ) . '</td>
                <td align="left" width="10%">' . __( 'Current_Location' ) . '</td>
                <td align="left" width="10%">' . __( 'Drop_Location' ) . '</td>
                <td align="left" width="10%">' . __( 'No_Passengers' ) . '</td>
                <td align="left" width="10%">' . __( 'pictup_date' ) . '</td>
                <td align="left" width="10%">' . __( 'pictup_time' ) . '</td>
                <td align="left" width="10%">' . __( 'waiting_time' ) . '</td>    
                <td align="left" width="10%">' . __( 'dropdate_time' ) . '</td>                                
                <td align="left" width="10%">' . __( 'rating_points' ) . '</td>
                <td align="left" width="10%">' . __( 'comments' ) . '</td>
                </tr>
                </thead>
                <tbody>    ';
            /* For Serial No */
            $sno = $offset;
            foreach ( $getmodel_details as $listings ) {
                //S.No Increment
                //==============
                $sno++;
                //For Odd / Even Rows
                //===================
                $trcolor = ( $sno % 2 == 0 ) ? 'oddtr' : 'eventr';
                $output .= '<tr class="' . $trcolor . '">';
                $output .= '<td>' . $sno . '</td>
                <td>' . $listings['passenger_name'] . '</td>
                <td><a href=' . URL_BASE . 'manage/driverinfo/' . $listings['id'] . '>' . wordwrap( $listings['driver_name'], 30, '<br/>', 1 ) . '</a></td>
                <td><a href=' . URL_BASE . 'manage/companydetails/' . $listings['id'] . '>' . wordwrap( $listings['company_name'], 25, '<br />', 1 ) . '</a></td>
                <td>' . wordwrap( $listings['email'], 25, '<br />', 1 ) . '</td>
                <td>' . $listings['current_location'] . '</td>
                <td>' . $listings['drop_location'] . '</td>
                <td>' . $listings['no_passengers'] . '</td>
                <td>' . date( 'd/m/Y', strtotime( $listings['pickup_time'] ) ) . '</td>
                <td>' . date( 'h:i:s', strtotime( $listings['pickup_time'] ) ) . '</td>
                <td>' . $listings['waitingtime'] . ' Mins</td>
                <td>' . $listings['drop_time'] . '</td>';
                if ( $listings['rating'] == 0 ) {
                    $output .= '<td>' . '' . '</td>';
                } else {
                    $output .= '<td>' . $listings['rating'] . '</td>';
                }
                $output .= '<td>' . $listings['comments'] . '</td></tr>';
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
    /** driver info details **/
    public function action_driver_unavailable()
    {
        $user_createdby      = $_SESSION['userid'];
        $usertype            = $_SESSION['user_type'];
        $driver_id           = $this->request->param( 'id' );
        $view_controller     = Model::factory( 'manage' );
        $unavailable_details = $view_controller->driver_unavailable( $driver_id );
        $signup_submit       = arr::get( $_REQUEST, 'submit_addleave' );
        if ( $signup_submit && Validation::factory( $_POST ) ) {
            $post_values = $_POST;
            $post        = Arr::map( 'trim', $this->request->post() );
            $validator   = $view_controller->validate_unavailabledriver( arr::extract( $post, array(
                 'driver_id',
                'reason',
                'startdate',
                'enddate' 
            ) ) );
            if ( $validator->check() ) {
                $update = $view_controller->add_unavailabledriver( $post );
                Message::success( __( 'profile_updated_successfully' ) );
                $this->request->redirect( "manage/unavailability" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                       = View::factory( 'admin/driver_unavailable' )->bind( 'pag_data', $pag_data )->bind( 'validator', $validator )->bind( 'driver_id', $driver_id )->bind( 'errors', $errors )->bind( 'unavailable_details', $unavailable_details )->bind( 'Offset', $offset )->bind( 'postvalue', $post_values );
        $this->page_title           = __( 'mark_unavailable' );
        $this->template->title      = SITENAME . " | " . __( 'mark_unavailable' );
        $this->template->page_title = __( 'mark_unavailable' );
        $this->template->content    = $view;
    }
    public function action_getunavilabledriverlist()
    {
        $manage_model  = Model::factory( 'manage' );
        $output        = '';
        $driver_id     = arr::get( $_REQUEST, 'driver_id' );
        $page_title    = __( 'unavailability' );
        $page_no       = arr::get( $_REQUEST, 'page' );
        $count_details = $manage_model->getunavailabledriverlist( $driver_id );
        if ( $page_no )
            $offset = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data          = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_details,
            'view' => 'pagination/puncmal' 
        ) );
        $getdriver_details = $manage_model->get_unavailabledriverlist( $driver_id, $offset, REC_PER_PAGE );
        $count             = count( $getdriver_details );
        $output .= '<div class="widget">
                <div class="title"><h6>' . $page_title . '</h6>
                <div style="width:auto; float:right; margin:0;">';
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
    public function action_unavailability()
    {
        $user_createdby         = $_SESSION['userid'];
        $usertype               = $_SESSION['user_type'];
        $manage_transaction     = Model::factory( 'manage' );
        $count_transaction_list = $manage_transaction->count_unavailability_list();
        //pagination loads here
        $page_no                = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_transaction_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_unavailablity_list     = $manage_transaction->unavailability_details( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/manage_unavailability' )->bind( 'pag_data', $pag_data )->bind( 'all_unavailablity_list', $all_unavailablity_list )->bind( 'id', $id )->bind( 'Offset', $offset );
        $this->page_title           = __( 'unavailability_driver_taxi' );
        $this->template->title      = SITENAME . " | " . __( 'unavailability_driver_taxi' );
        $this->template->page_title = __( 'unavailability_driver_taxi' );
        $this->template->content    = $view;
    }
    public function action_unavailabilitysearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'unavailability_driver_taxi' );
        $this->selected_page_title = __( 'unavailability_driver_taxi' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_unavailabilitysearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_unavailablity_list = $manage_company->get_unavailabilitysearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_unavailability' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_unavailablity_list', $all_unavailablity_list );
        $this->template->content = $view;
    }
    public function action_availabilitytaxi()
    {
        //Page Title
        $this->page_title          = __( 'manage_availability_taxi' );
        $this->selected_page_title = __( 'manage_availability_taxi' );
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $count_company_list        = $manage_company->count_taxi_list();
        $all_list                  = $count_company_list;
        $count_company_list        = count( $count_company_list );
        $cid                       = $_SESSION['company_id'];
        $availabilitycount         = 1;
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_taxi_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $get_allcompany             = $manage_company->get_allcompany();
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_availabilitytaxi' )->bind( 'all_company_list', $all_company_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_availability_taxi' );
        $this->template->page_title = __( 'manage_availability_taxi' );
        $this->template->content    = $view;
    }
    public function action_availabilitytaxisearch()
    {
        $user_createdby            = $_SESSION['userid'];
        $company_id                = $_SESSION['company_id'];
        $usertype                  = $_SESSION['user_type'];
        //Page Title
        $this->page_title          = __( 'manage_availability_taxi' );
        $this->selected_page_title = __( 'manage_availability_taxi' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $cid                       = $_SESSION['company_id'];
        if ( $usertype != 'A' ) {
            $count_company_list = $manage_company->count_searchtaxi_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ) );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        } else {
            $count_company_list = $manage_company->count_searchtaxi_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ) );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        }
        //pagination loads here
        //-------------------------
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            if ( $usertype != 'A' ) {
                $all_company_list = $manage_company->get_all_untaxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ), $offset, REC_PER_PAGE );
            } else {
                $all_company_list = $manage_company->get_all_untaxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ), $offset, REC_PER_PAGE );
            }
        }
        $get_allcompany          = $manage_company->get_allcompany();
        $availabilitycount       = 1;
        //set data to view file    
        $view                    = View::factory( 'admin/manage_availabilitytaxi' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'get_allcompany', $get_allcompany )->bind( 'availabilitycount', $availabilitycount )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_active_availabilitytaxi_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_availabilitytaxi_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_availabilitytaxi_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_availabilitytaxi_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_availabilitydriver()
    {
        //Page Title
        $this->page_title          = __( 'manage_availability_driver' );
        $this->selected_page_title = __( 'manage_availability_driver' );
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $count_company_list        = $manage_company->count_driver_list();
        $all_list                  = $count_company_list;
        $count_company_list        = count( $count_company_list );
        $cid                       = $_SESSION['company_id'];
        $availabilitycount         = 1;
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_driver_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $get_allcompany             = $manage_company->get_allcompany();
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_availabilitydriver' )->bind( 'all_company_list', $all_company_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_availability_driver' );
        $this->template->page_title = __( 'manage_availability_driver' );
        $this->template->content    = $view;
    }
    public function action_availabilitydriversearch()
    {
        $user_createdby            = $_SESSION['userid'];
        $company_id                = $_SESSION['company_id'];
        $usertype                  = $_SESSION['user_type'];
        //Page Title
        $this->page_title          = __( 'manage_availability_driver' );
        $this->selected_page_title = __( 'manage_availability_driver' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $cid                       = $_SESSION['company_id'];
        $availabilitycount         = 1;
        if ( $usertype != 'A' ) {
            $count_company_list = $manage_company->count_searchdriver_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ) );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        } else {
            $count_company_list = $manage_company->count_searchdriver_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ) );
            $all_list           = $count_company_list;
            $count_company_list = count( $count_company_list );
        }
        //pagination loads here
        //-------------------------
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            if ( $usertype != 'A' ) {
                $all_company_list = $manage_company->get_all_undriver_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $company_id ) ), $offset, REC_PER_PAGE );
            } else {
                $all_company_list = $manage_company->get_all_undriver_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ), $offset, REC_PER_PAGE );
            }
        }
        $get_allcompany          = $manage_company->get_allcompany();
        //set data to view file    
        $view                    = View::factory( 'admin/manage_availabilitydriver' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'get_allcompany', $get_allcompany )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_active_availabilitydriver_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_availabilitydriver_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_block_availabilitydriver_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_availabilitydriver_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    //Listed the menus
    public function action_menu()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_menu' );
        $this->selected_page_title = __( 'manage_menu' );
        $manage_menu               = Model::factory( 'manage' );
        $count_menu_list           = $manage_menu->count_menu_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_menu_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_menu_list              = $manage_menu->all_menu_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //Find page action in view
        $action                     = $this->request->action();
        //send data to view file 
        $view                       = View::factory( 'admin/manage_menu' )->bind( 'all_menu_list', $all_menu_list )->bind( 'pag_data', $pag_data )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_menu' );
        $this->template->page_title = __( 'manage_menu' );
        $this->template->content    = $view;
    }
    //For deleting contents
    public function action_delete_menu()
    {
        $id          = $this->request->param( 'id' );
        $delete_menu = Model::factory( 'manage' );
        $status      = $delete_menu->delete_menu( $id );
        if ( $status ) {
            Message::success( __( 'menu_was_delete' ) );
            $this->request->redirect( $_SERVER['HTTP_REFERER'] );
        }
    }
    //Listed the miles
    public function action_mile()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( ( $usertype != 'A' ) && ( $usertype != 'S' ) ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_mile' );
        $this->selected_page_title = __( 'manage_mile' );
        $manage_mile               = Model::factory( 'manage' );
        $count_mile_list           = $manage_mile->count_mile_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_mile_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_mile_list              = $manage_mile->all_mile_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //Find page action in view
        $action                     = $this->request->action();
        //send data to view file 
        $view                       = View::factory( 'admin/manage_mile' )->bind( 'all_mile_list', $all_mile_list )->bind( 'pag_data', $pag_data )->bind( 'MileList', $MileList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_mile' );
        $this->template->page_title = __( 'manage_mile' );
        $this->template->content    = $view;
    }
    //Active miles request function
    public function action_active_mile_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_mile_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/mile" );
    }
    //Block miles request function
    public function action_block_mile_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_mile_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/mile" );
    }
    //Trash miles request function
    public function action_trash_mile_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->trash_mile_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests has been deleted' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/mile" );
    }
    //For deleting miles
    public function action_delete_mile()
    {
        $id          = $this->request->param( 'id' );
        $delete_menu = Model::factory( 'manage' );
        $status      = $delete_menu->delete_mile( $id );
        if ( $status ) {
            Message::success( __( 'mile_was_delete' ) );
            $this->request->redirect( "manage/mile" );
        }
    }
    //For deleting miles
    public function action_update_comments()
    {
        $passengers_log_id = $this->request->param( 'id' );
        $delete_menu       = Model::factory( 'manage' );
        $status            = $delete_menu->update_comments( $passengers_log_id );
        if ( $status ) {
            Message::success( __( 'comments_del' ) );
            $this->request->redirect( "manage/ratingdrivers" );
        } else {
            Message::error( __( 'not_updated' ) );
        }
    }
    public function action_today_unassigned_taxi()
    {
        //Page Title
        $this->page_title          = __( 'unassigned_taxy' );
        $this->selected_page_title = __( 'unassigned_taxy' );
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $admin                     = Model::factory( 'admin' );
        $cid                       = $_SESSION['company_id'];
        $availabilitycount         = $admin->free_taxi_list( $cid );
        $count_company_list        = $admin->free_taxi_list_count( $cid );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $admin->free_taxi_list_all_pag( $offset, REC_PER_PAGE, $cid );
        //****pagination ends here***//
        $get_allcompany             = $manage_company->get_allcompany();
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_today_unassigned_taxi' )->bind( 'all_company_list', $all_company_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'unassigned_taxy' );
        $this->template->page_title = __( 'unassigned_taxy' );
        $this->template->content    = $view;
    }
    //unassigned taxi search
    public function action_unassigned_taxisearch()
    {
        $user_createdby            = $_SESSION['userid'];
        $company_id                = $_SESSION['company_id'];
        $usertype                  = $_SESSION['user_type'];
        //Page Title
        $this->page_title          = __( 'unassigned_taxy' );
        $this->selected_page_title = __( 'unassigned_taxy' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $cid                       = $_SESSION['company_id'];
        $availabilitycount         = 1;
        $count_company_list        = $manage_company->count_unassign_searchtaxi_list( trim( Html::chars( $_REQUEST['keyword'] ) ) );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            if ( $usertype != 'A' ) {
                $all_company_list = $manage_company->get_unassign_taxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), $offset, REC_PER_PAGE );
            } else {
                $all_company_list = $manage_company->get_unassign_taxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), $offset, REC_PER_PAGE );
            }
        }
        $get_allcompany          = $manage_company->get_allcompany();
        //set data to view file    
        $view                    = View::factory( 'admin/manage_today_unassigned_taxi' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'get_allcompany', $get_allcompany )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_freetaxi()
    {
        //Page Title
        $this->page_title          = __( 'free_taxy' );
        $this->selected_page_title = __( 'free_taxy' );
        $manage_company            = Model::factory( 'manage' );
        $add_model                 = Model::factory( 'add' );
        $usertype                  = $_SESSION['user_type'];
        $cid                       = $_SESSION['company_id'];
        if ( $usertype != 'A' ) {
            $check_result = 1;
            if ( $check_result < 0 ) {
                if ( $usertype == 'C' ) {
                    $this->request->redirect( "manage/availabilitytaxi" );
                }
                if ( $usertype == 'M' ) {
                    $this->request->redirect( "manage/availabilitytaxi" );
                }
            }
            $check_result = 1;
            if ( $check_result < 0 ) {
                if ( $usertype == 'C' ) {
                    $this->request->redirect( "manage/availabilitydriver" );
                }
                if ( $usertype == 'M' ) {
                    $this->request->redirect( "manage/availabilitydriver" );
                }
            }
        }
        $count_company_list = $manage_company->count_freetaxi_list( $cid );
        //pagination loads here
        //-------------------------
        $page_no            = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_freetaxi_list( $offset, REC_PER_PAGE, $cid );
        //****pagination ends here***//
        $get_allcompany             = $manage_company->get_allcompany();
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_freetaxi' )->bind( 'all_company_list', $all_company_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'free_taxy' );
        $this->template->page_title = __( 'free_taxy' );
        $this->template->content    = $view;
    }
    public function action_freetaxisearch()
    {
        $user_createdby            = $_SESSION['userid'];
        $company_id                = $_SESSION['company_id'];
        $usertype                  = $_SESSION['user_type'];
        //Page Title
        $this->page_title          = __( 'free_taxy' );
        $this->selected_page_title = __( 'free_taxy' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $cid                       = $_SESSION['company_id'];
        if ( $usertype != 'A' ) {
            $check_result = 1;
            if ( $check_result < 0 ) {
                if ( $usertype == 'C' ) {
                    Message::success( __( 'limited_taxi' ) );
                    $this->request->redirect( "manage/availabilitytaxi" );
                }
                if ( $usertype == 'M' ) {
                    Message::success( __( 'limited_taxi' ) );
                    $this->request->redirect( "manage/availabilitytaxi" );
                }
            }
            $check_result = 1;
            if ( $check_result < 0 ) {
                if ( $usertype == 'C' ) {
                    Message::success( __( 'limited_driver' ) );
                    $this->request->redirect( "manage/availabilitydriver" );
                }
                if ( $usertype == 'M' ) {
                    Message::success( __( 'limited_driver' ) );
                    $this->request->redirect( "manage/availabilitydriver" );
                }
            }
        }
        if ( $usertype != 'A' ) {
            $count_company_list = $manage_company->count_freetaxisearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ) );
        } else {
            $count_company_list = $manage_company->count_freetaxisearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ) );
        }
        //-------------------------
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            if ( $usertype != 'A' ) {
                $all_company_list = $manage_company->get_all_freetaxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), $offset, REC_PER_PAGE );
            } else {
                $all_company_list = $manage_company->get_all_freetaxi_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), $offset, REC_PER_PAGE );
            }
        }
        $get_allcompany          = $manage_company->get_allcompany();
        //set data to view file    
        $view                    = View::factory( 'admin/manage_freetaxi' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'get_allcompany', $get_allcompany )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    public function action_unassign_driver()
    {
        //Page Title
        $this->page_title          = __( 'unass_drivers' );
        $this->selected_page_title = __( 'unass_drivers' );
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $cid                       = $_SESSION['company_id'];
        $availabilitycount         = 1;
        $count_company_list        = $manage_company->free_driver_list_count( $cid );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_company_list           = $manage_company->all_free_driver_list( $offset, REC_PER_PAGE, $cid );
        //****pagination ends here***//
        $get_allcompany             = $manage_company->get_allcompany();
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_unassign_driver' )->bind( 'all_company_list', $all_company_list )->bind( 'get_allcompany', $get_allcompany )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'unass_drivers' );
        $this->template->page_title = __( 'unass_drivers' );
        $this->template->content    = $view;
    }
    public function action_unassign_driversearch()
    {
        $user_createdby            = $_SESSION['userid'];
        $company_id                = $_SESSION['company_id'];
        $usertype                  = $_SESSION['user_type'];
        //Page Title
        $this->page_title          = __( 'unass_drivers' );
        $this->selected_page_title = __( 'unass_drivers' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $add_company               = Model::factory( 'add' );
        $cid                       = $_SESSION['company_id'];
        $availabilitycount         = 1;
        if ( $usertype != 'A' ) {
            $count_company_list = $manage_company->count_unassign_searchdriver_list( trim( Html::chars( $_REQUEST['keyword'] ) ) );
        } else {
            $count_company_list = $manage_company->count_unassign_searchdriver_list( trim( Html::chars( $_REQUEST['keyword'] ) ) );
        }
        //pagination loads here
        //-------------------------
        $page_no = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            if ( $usertype != 'A' ) {
                $all_company_list = $manage_company->get_unassign_driver_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), $offset, REC_PER_PAGE );
            } else {
                $all_company_list = $manage_company->get_unassign_driver_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), $offset, REC_PER_PAGE );
            }
        }
        $get_allcompany          = $manage_company->get_allcompany();
        //set data to view file    
        $view                    = View::factory( 'admin/manage_unassign_driver' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'availabilitycount', $availabilitycount )->bind( 'get_allcompany', $get_allcompany )->bind( 'all_company_list', $all_company_list );
        $this->template->content = $view;
    }
    // Mute the Driver Those are not displayed in Search
    public function action_mute_driver_request()
    {
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->mute_driver_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to Mute status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_banner()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "company/login" );
        }
        //Page Title
        $this->page_title          = __( 'manage_banner' );
        $this->selected_page_title = __( 'manage_banner' );
        $manage_banner             = Model::factory( 'manage' );
        $company_id                = $_SESSION['company_id'];
        $count_banner_list         = $manage_banner->count_banner_list( $company_id );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_banner_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_banner_list            = $manage_banner->all_banner_list( $company_id, $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_banner' )->bind( 'all_banner_list', $all_banner_list )->bind( 'pag_data', $pag_data )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_banner' );
        $this->template->page_title = __( 'manage_banner' );
        $this->template->content    = $view;
    }
    public function action_bannersearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "company/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_banner' );
        $this->selected_page_title = __( 'manage_banner' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_banner             = Model::factory( 'manage' );
        $count_banner_list         = $manage_banner->count_company_searchbanner_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_banner_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_banner_list = $manage_banner->get_company_all_banner_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_banner' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'all_banner_list', $all_banner_list );
        $this->template->content = $view;
    }
    public function action_block_banner_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "company/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_banner_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/banner" ); //transaction/index
    }
    public function action_active_banner_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' ) {
            $this->request->redirect( "company/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_banner_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manage/banner" ); //transaction/index
    }
    public function action_faq()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_faq' );
        $this->selected_page_title = __( 'manage_faq' );
        $manage_company            = Model::factory( 'manage' );
        $count_faq_list            = $manage_company->count_faq_list();
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_faq_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_faq_list               = $manage_company->all_faq_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/manage_faq' )->bind( 'all_faq_list', $all_faq_list )->bind( 'pag_data', $pag_data )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_faq' );
        $this->template->page_title = __( 'manage_faq' );
        $this->template->content    = $view;
    }
    public function action_block_faq_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->block_faq_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_active_faq_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $manage   = Model::factory( 'manage' );
        $status   = $manage->active_faq_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_trash_faq_request()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $this->page_title          = __( 'manage_faq' );
        $this->selected_page_title = __( 'manage_faq' );
        $manage_contacts           = Model::factory( 'manage' );
        $manage                    = Model::factory( 'manage' );
        $status                    = $manage->trash_faq_request( $_REQUEST['uniqueId'] );
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests has been deleted' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    public function action_faqsearch()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        //Page Title
        $this->page_title          = __( 'manage_faq' );
        $this->selected_page_title = __( 'manage_faq' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $manage_company            = Model::factory( 'manage' );
        $count_company_list        = $manage_company->count_searchfaq_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
        //echo $count_company_list;
        //pagination loads here
        //-------------------------
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset      = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data    = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_company_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_faq_list = $manage_company->get_all_faq_searchlist( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/manage_faq' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'CompanyList', $CompanyList )->bind( 'all_faq_list', $all_faq_list );
        $this->template->content = $view;
    }
    public function action_driver_transaction_search()
    {
        $driver_model = Model::factory( 'driver' );
        if ( isset( $_POST ) ) {
            $start_date      = $_POST['startdate'];
            $end_date        = $_POST['enddate'];
            $driver_id       = $_POST['driver_id'];
            $output          = "";
            $get_transaction = $driver_model->get_trans_of_driver( $driver_id, REC_PER_PAGE, Commonfunction::ensureDatabaseFormat( $start_date, 1 ), Commonfunction::ensureDatabaseFormat( $end_date, 2 ) );
            if ( !empty( $get_transaction ) ) {
                $fare  = array();
                $month = array();
                foreach ( $get_transaction as $vl ) {
                    if ( $vl['fare'] != NULL ) {
                        $fare[]  = $vl['fare'];
                        $month[] = "'" . $vl['date'] . " " . $vl['month'] . "'";
                    }
                }
                if ( $fare != NULL ) {
                    $fare = implode( ",", $fare );
                }
                if ( $month != NULL ) {
                    $month = implode( ",", $month );
                }
                $display = "display:block;";
                $output  = '<div id="transaction_chart" style="min-width: 400px; height: 400px; margin: 0 auto;' . $display . '"></div>';
?>
                   <script>
                    $('#transaction_chart').highcharts({
                    title: {
                        text: 'Transaction from <?php
                echo Commonfunction::getDateTimeFormat( $start_date, 1 );
?> to <?php
                echo Commonfunction::getDateTimeFormat( $end_date, 1 );
?>',
                        x: -20 //center
                    },
                    subtitle: {
                        text: '',
                        x: -20
                    },
                    xAxis: {
                        categories: [<?php
                echo $month;
?>]
                    },
                    yAxis: {
                        title: {
                            text: 'Amount (Rs)'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    tooltip: {
                        valueSuffix: ''
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series: [{
                        name: 'Transaction',
                        data: [<?php
                echo $fare;
?>]
                    }]
                });
                    </script>
                <?php
            } else {
                $output = "<div class='no_data'>" . __( 'no_data' ) . "</div>";
            }
        } else {
            $output = "<div class='no_data'>" . __( 'no_data' ) . "</div>";
        }
        echo $output;
        exit;
    }
    public function action_driver_statistics_search()
    {
        $driver_model = Model::factory( 'driver' );
        if ( isset( $_POST ) ) {
            $start_date         = Commonfunction::ensureDatabaseFormat( $_POST['startdate'], 1 );
            $end_date           = Commonfunction::ensureDatabaseFormat( $_POST['enddate'], 2 );
            $driver_id          = $_POST['driver_id'];
            $output             = "";
            $get_trip_statitics = $driver_model->get_trip_statitics( $driver_id, REC_PER_PAGE, $start_date, $end_date );
            if ( ( !empty( $get_trip_statitics['completed_trips'] ) ) || ( !empty( $get_trip_statitics['rejected_trips'] ) ) || ( !empty( $get_trip_statitics['cancelled_trips'] ) ) ) {
                $createdate      = array();
                $resdate         = array();
                $reject_trips    = array();
                $cancelled_trips = array();
                $completed_trips = array();
                $display_trip    = '';
                $a               = 0;
                $b               = 0;
                $date_conv       = '';
                $end             = ( date( 'M-d' ) );
                //to get number of days between to datetimes
                $start_ts        = strtotime( $start_date );
                $end_ts          = strtotime( $end_date );
                $diff            = $end_ts - $start_ts;
                $no_of_days      = ( round( $diff / 86400 ) );
                while ( $a <= $no_of_days ) {
                    $end          = date( 'M-d', mktime( 0, 0, 0, date( "m", strtotime( $end_date ) ), date( "d", strtotime( $end_date ) ) - $a, date( "Y", strtotime( $end_date ) ) ) );
                    $createdate[] = "'$end'";
                    $a++;
                }
                while ( $b < count( $get_trip_statitics['cancelled_trips'] ) ) {
                    if ( isset( $get_trip_statitics['cancelled_trips'][$b]['cancelled_count'] ) ) {
                        foreach ( $createdate as $ct ) {
                            $date_conv = date( 'M-d', strtotime( $get_trip_statitics['cancelled_trips'][$b]['createdate'] ) );
                            $ct        = str_replace( "'", "", $ct );
                            if ( $ct == $date_conv ) {
                                $resdate[]         = "'$date_conv'";
                                $cancelled_trips[] = $get_trip_statitics['cancelled_trips'][$b]['cancelled_count'];
                            } else {
                                //$cancelled_trips[]=0;
                            }
                        }
                    } else {
                        //$cancelled_trips[]=0;
                    }
                    $b++;
                }
                $b = 0;
                while ( $b < count( $get_trip_statitics['rejected_trips'] ) ) {
                    if ( isset( $get_trip_statitics['rejected_trips'][$b]['rejected_count'] ) ) {
                        foreach ( $createdate as $ct ) {
                            $date_conv = date( 'M-d', strtotime( $get_trip_statitics['rejected_trips'][$b]['createdate'] ) );
                            $ct        = str_replace( "'", "", $ct );
                            if ( $ct == $date_conv ) {
                                $resdate[]      = "'$date_conv'";
                                $reject_trips[] = $get_trip_statitics['rejected_trips'][$b]['rejected_count'];
                            } else {
                                //$reject_trips[]=0;
                            }
                        }
                    } else {
                        //$reject_trips[]=0;
                    }
                    $b++;
                }
                $b = 0;
                while ( $b < count( $get_trip_statitics['completed_trips'] ) ) {
                    if ( isset( $get_trip_statitics['completed_trips'][$b]['completed_count'] ) ) {
                        foreach ( $createdate as $ct ) {
                            $date_conv = date( 'M-d', strtotime( $get_trip_statitics['completed_trips'][$b]['createdate'] ) );
                            $ct        = str_replace( "'", "", $ct );
                            if ( $ct == $date_conv ) {
                                $resdate[]         = "'$date_conv'";
                                $completed_trips[] = $get_trip_statitics['completed_trips'][$b]['completed_count'];
                            } else {
                                //$completed_trips[]=0;
                            }
                        }
                    } else {
                        //$completed_trips[]=0;
                    }
                    $b++;
                }
                $redateArr       = array_unique( $resdate );
                $reject_trips    = implode( ",", $reject_trips );
                $cancelled_trips = implode( ",", $cancelled_trips );
                $completed_trips = implode( ",", $completed_trips );
                $resdate         = implode( ",", $redateArr );
                $display_trip    = "display:block;";
                $output          = '<div id="trip_statitics" style="min-width: 400px; height: 400px; margin: 0 auto;' . $display_trip . '"></div>';
?>
                   <script>
                    $('#trip_statitics').highcharts({
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: '<?php
                echo __( 'trip_statitics' );
?>'
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: {
                            categories: [<?php
                echo $resdate;
?>]
                            //categories: ['Jun-11','Jun-10']
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Trip Counts'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0"><b>{point.y} Trips</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [{
                            name: 'Rejected Trips',
                            data: [<?php
                echo $reject_trips;
?>]
                
                        }, {
                            name: 'Cancelled Trips',
                            data: [<?php
                echo $cancelled_trips;
?>]
                
                        }, {
                            name: 'Completed Trips',
                            data: [<?php
                echo $completed_trips;
?>]
                
                        }]
                    });
                    </script>
                <?php
            } else {
                $output = "<div class='no_data'>" . __( 'no_data' ) . "</div>";
            }
        } else {
            $output = "<div class='no_data'>" . __( 'no_data' ) . "</div>";
        }
        echo $output;
        exit;
    }
    public function action_taxi_transaction_search()
    {
        $driver_model = Model::factory( 'driver' );
        if ( isset( $_POST ) ) {
            $start_date      = $_POST['startdate'];
            $end_date        = $_POST['enddate'];
            $taxi_name       = $_POST['taxi_name'];
            $driver_id       = $_POST['driver_id'];
            $output          = "";
            $for_date        = $start_date . " to " . $end_date;
            $get_transaction = $driver_model->get_trans_of_taxi( $driver_id, REC_PER_PAGE, Commonfunction::ensureDatabaseFormat( $start_date, 1 ), Commonfunction::ensureDatabaseFormat( $end_date, 2 ) );
            //function to get total count of taxi transaction
            $totTransCount   = $driver_model->get_total_trans_taxi( $driver_id );
            if ( !empty( $get_transaction ) ) {
                $fare  = '';
                $month = '';
                $trips = '';
                foreach ( $get_transaction as $vl ) {
                    if ( $vl['fare'] != NULL ) {
                        $trips[] = $vl['trips'];
                        $fare[]  = $vl['fare'];
                        $month[] = "'" . $vl['date'] . " " . $vl['month'] . "'";
                    }
                }
                if ( $trips != NULL ) {
                    $trips = implode( ",", $trips );
                }
                if ( $fare != NULL ) {
                    $fare = implode( ",", $fare );
                }
                if ( $month != NULL ) {
                    $month = implode( ",", $month );
                }
                $display = "display:block;";
                //echo $fare;
                //echo '<br>';
                //echo $month;
                $output  = '<div id="transaction_chart" style="min-width: 400px; height: 400px; margin: 0 auto;' . $display . '"></div>';
?>
                   <script>
        $('#transaction_chart').highcharts({            
            chart: {
                shortMonths:true,
                zoomType: 'xy'
            },
            title: {
                    text: 'Total Trip Details [<?php
                echo $taxi_name;
?>]'
                },
                subtitle: {
                    text: "<?php
                echo __( 'for_label' ) . ' ' . $for_date;
?>",
                },
                xAxis: [{
                    shortMonths:true,
                    categories: [<?php
                echo $month;
?>]
                }],
                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '{value} Trips',
                        style: {
                            color: Highcharts.getOptions().colors[2]
                        }
                    },
                    title: {
                        text: 'Trip Counts',
                        style: {
                            color: Highcharts.getOptions().colors[2]
                        }
                    },
                    opposite: true

                }, { // Secondary yAxis
                    gridLineWidth: 0,
                    title: {
                        text: 'Trip Revenues',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    labels: {
                        format: '{value} $',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    }

                }, ],
                tooltip: {
                    shared: true
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    x: 120,
                    verticalAlign: 'top',
                    y: 80,
                    floating: true,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                },
                series: [{
                    name: 'Trip Revenues',
                    type: 'column',
                    yAxis: 1,
                    data : [<?php
                echo $fare;
?>],
                    tooltip: {
                        valueSuffix: ' $'
                    }

                },
                 {
                    name: 'Trip Counts',
                    type: 'spline',
                    data : [<?php
                echo $trips;
?>],
                    tooltip: {
                        valueSuffix: ' Trips'
                    }
                }]
            });
    </script>
                    
                <?php
            } else {
                $output = "<div class='no_data'>" . __( 'no_data' ) . "</div>";
            }
        } else {
            $output = "<div class='no_data'>" . __( 'no_data' ) . "</div>";
        }
        echo $output . '~' . $totTransCount;
        exit;
    }
    /************* Dashboard All Driver status***************/
    public function action_view_all_drivers()
    {
        $manage_company       = Model::factory( 'manage' );
        $all_company_map_list = $manage_company->all_driver_map_list();
        $a                    = 0;
        $b                    = 5;
        $markers              = array();
        if ( count( $all_company_map_list ) > 0 ) {
            foreach ( $all_company_map_list as $v ) {
                for ( $b = 0; $b < 6; $b++ ) {
                    if ( $b == 0 ) {
                        $markers[$a][$b] = $v['latitude'];
                    }
                    if ( $b == 1 ) {
                        $markers[$a][$b] = $v['longitude'];
                    }
                    if ( $b == 2 ) {
                        $markers[$a][$b] = '<div class="info_content"><b>' . __( 'driver_name' ) . '</b> : ' . $v['name'];
                    }
                    if ( $b == 3 ) {
                        $driver_status   = ( $v['driver_status'] == 'F' ) ? __( 'Free' ) : ( ( $v['driver_status'] == 'A' ) ? "<span>" . __( 'Hired' ) . "</span>" : __( 'Free' ) );
                        $markers[$a][$b] = '<div id="bodyContent"><p><b>' . __( 'driver_status' ) . '</b>: <b style="color:green;">' . $driver_status . '</b>';
                    }
                    if ( $b == 4 ) {
                        $shift_status    = ( $v['shift_status'] == 'IN' ) ? __( 'in' ) : __( 'out' );
                        $markers[$a][$b] = '<b style="color:#0F9ED6;">' . $shift_status . '</b></p></div></div>';
                    }
                    if ( $b == 5 ) {
                        if ( $v['driver_status'] == 'F' && $v['shift_status'] == 'OUT' ) {
                            $markers[$a][$b] = PUBLIC_IMGPATH . '/driver_four.png';
                        } elseif ( $v['driver_status'] == 'A' ) {
                            $markers[$a][$b] = PUBLIC_IMGPATH . '/driver_one.png';
                        } else {
                            $markers[$a][$b] = PUBLIC_IMGPATH . '/driver_two.png';
                        }
                    }
                }
                $a++;
            }
        }
        echo json_encode( $markers );
        exit;
    }
    public function action_view_all_drivers_company()
    {
        $company              = $_REQUEST['company'];
        $manage_company       = Model::factory( 'manage' );
        $all_company_map_list = $manage_company->all_driver_map_list_company( $company );
        $a                    = 0;
        $b                    = 5;
        $markers              = array();
        if ( count( $all_company_map_list ) > 0 ) {
            foreach ( $all_company_map_list as $v ) {
                for ( $b = 0; $b < 6; $b++ ) {
                    if ( $b == 0 ) {
                        $markers[$a][$b] = $v['latitude'];
                    }
                    if ( $b == 1 ) {
                        $markers[$a][$b] = $v['longitude'];
                    }
                    if ( $b == 2 ) {
                        $markers[$a][$b] = '<div class="info_content"><b>' . __( 'driver_name' ) . '</b> : ' . $v['name'];
                    }
                    if ( $b == 3 ) {
                        $driver_status   = ( $v['driver_status'] == 'F' ) ? __( 'Free' ) : ( ( $v['driver_status'] == 'A' ) ? "<span>" . __( 'Hired' ) . "</span>" : __( 'Free' ) );
                        $markers[$a][$b] = '<div id="bodyContent"><p><b>' . __( 'driver_status' ) . '</b>: <b style="color:green;">' . $driver_status . '</b>';
                    }
                    if ( $b == 4 ) {
                        $shift_status    = ( $v['shift_status'] == 'IN' ) ? __( 'in' ) : __( 'out' );
                        $markers[$a][$b] = '<b style="color:#0F9ED6;">' . $shift_status . '</b></p></div></div>';
                    }
                    if ( $b == 5 ) {
                        if ( $v['driver_status'] == 'F' && $v['shift_status'] == 'OUT' ) {
                            $markers[$a][$b] = PUBLIC_IMGPATH . '/driver_four.png';
                        } elseif ( $v['driver_status'] == 'A' ) {
                            $markers[$a][$b] = PUBLIC_IMGPATH . '/driver_one.png';
                        } else {
                            $markers[$a][$b] = PUBLIC_IMGPATH . '/driver_two.png';
                        }
                    }
                }
                $a++;
            }
        } else {
            $markers = "";
        }
        echo json_encode( $markers );
        exit;
    }
    /************* Dashboard All Driver status***************/
    public function action_promocode()
    {
        $user_createdby = $_SESSION['userid'];
        $company_id     = $_SESSION['company_id'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'C' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        if ( isset( $_GET['search_user'] ) ) {
            $search = $_GET;
        } else {
            $search = '';
        }
        //Page Title
        $this->page_title          = __( 'manage_promocode' );
        $this->selected_page_title = __( 'manage_promocode' );
        $manage                    = Model::factory( 'manage' );
        $add_model                 = Model::factory( 'add' );
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $count_promocode_list       = $manage->count_promocode_list( $search, $company_id );
        $total_items                = count( $count_promocode_list );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $total_items,
            'view' => 'pagination/punbb' 
        ) );
        $promocode_list             = $manage->promocode_list( $offset, REC_PER_PAGE, $search, $company_id );
        $total_users                = count( $promocode_list );
        //send data to view file 
        $view                       = View::factory( 'admin/manage_promocode' )->bind( 'promocode_list', $promocode_list )->bind( 'pag_data', $pag_data )->bind( 'total_users', $total_users )->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'promocode_list' );
        $this->template->page_title = __( 'manage_promocode' );
        $this->template->content    = $view;
    }
    /** promo info details **/
    public function action_promoinfo()
    {
        $id              = $this->request->param( 'id' );
        $company_id      = $_SESSION['company_id'];
        $view_controller = Model::factory( 'manage' );
        $details         = $view_controller->details_promoinfo( $id, $company_id );
        if ( count( $details ) == 0 ) {
            $this->request->redirect( "manage/promocode" );
        }
        $view                       = View::factory( 'admin/promoinfo' )->bind( 'promo_data', $details );
        $this->template->title      = SITENAME . " | " . __( 'promocode_list' );
        $this->template->page_title = __( 'detail_promocode' );
        $this->template->content    = $view;
    }
    public function action_driver_logout()
    {
        $loginfo                = $_GET;
        $driver_id              = $loginfo['driver_id'];
        $company_id             = isset( $loginfo['company_id'] ) ? $loginfo['company_id'] : "";
        $manage_company         = Model::factory( 'manage' );
        $Commonmodel            = Model::factory( 'Commonmodel' );
        $driver_model           = Model::factory( 'driver' );
        $customer_google_api    = $Commonmodel->select_site_settings( 'driver_android_key', MDB_SITEINFO );
        $get_driver_log_details = $manage_company->get_driver_current_status( $driver_id, $company_id );
        if ( count( $get_driver_log_details ) == 0 ) {
            $api_ext          = Model::factory( 'mobileapi117extended' );
            //send push notification to driver
            $driverDeviceDets = $manage_company->getDriverDeviceToken( $driver_id );
            if ( count( $driverDeviceDets ) > 0 && !empty( $driverDeviceDets[0]['device_token'] ) ) {
                $pushMessage = array(
                     "message" => __( 'driver_logout_via_admin' ),
                    "status" => 15,
                    "display" => 1 
                );
                $Commonmodel->send_pushnotification( $driverDeviceDets[0]['device_token'], $driverDeviceDets[0]['device_type'], $pushMessage, $customer_google_api );
            }
            $update_array        = array(
                 "login_from" => "",
                "login_status" => "N",
                "device_id" => "",
                "device_token" => "",
                "device_type" => "",
                "notification_setting" => "0" 
            );
            $login_status_update = $api_ext->update_driver_people( $update_array, $driver_id );
            /** GET Shift ID **/
            $driver_shift        = $driver_model->get_shift_status( $driver_id );
            if ( count( $driver_shift ) > 0 ) {
                $driver_shift_id = isset( $driver_shift[0]['driver_shift_id'] ) ? $driver_shift[0]['driver_shift_id'] : '';
                $transaction     = $api_ext->update_drivershiftend( $driver_shift_id );
            }
            /*** Update in Driver table **/
            $driver_reply = $driver_model->update_driver_shift_status( $driver_id, '0' );
            $result       = 1;
        } else {
            $result = 0;
        }
        echo $result;
        exit;
    }
    /** Function to get driver wallet request lists **/
    public function action_walletrequests()
    {
        $company_id = $_SESSION['company_id'];
        $usertype   = $_SESSION['user_type'];
        $manage     = Model::factory( 'manage' );
        $add_model  = Model::factory( 'add' );
        $page_no    = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset              = REC_PER_PAGE * ( $page_no - 1 );
        $allRequests         = $manage->getWalletAmtRequests( $_GET, $company_id, $getCnt = 1 );
        $countDriverRequests = count( $allRequests );
        //export section
        if ( isset( $_SESSION['download_set'] ) ) {
            if ( count( $allRequests ) > 0 ) {
                foreach ( $allRequests as $key => $value ) {
                    if ( array_key_exists( 'wallet_request_date', $value ) ) {
                        $date = Commonfunction::getDateTimeFormat( $value['wallet_request_date'], 1 );
                        unset( $value['wallet_request_date'] );
                        $allRequests[$key]['wallet_request_date'] = $date;
                    }
                }
            }
            if ( $usertype == 'A' ) {
                $export_table_header       = array(
                     __( 'name' ),
                    __( 'phone_label' ),
                    __( 'companyname' ),
                    __( 'request_amount' ),
                    __( 'request_date' ),
                    __( 'request_status' ) 
                );
                $export_table_field_select = array(
                     'driverName',
                    'driverPhone',
                    'company_name',
                    'wallet_request_amount',
                    'wallet_request_date',
                    'reqstatus' 
                );
            } else {
                $export_table_header       = array(
                     __( 'name' ),
                    __( 'phone_label' ),
                    __( 'request_amount' ),
                    __( 'request_date' ),
                    __( 'request_status' ) 
                );
                $export_table_field_select = array(
                     'driverName',
                    'driverPhone',
                    'wallet_request_amount',
                    'wallet_request_date',
                    'reqstatus' 
                );
            }
            $heading = 'taxiexport';
            $data    = array();
            if ( count( $allRequests ) > 0 ) {
                foreach ( $allRequests as $key => $val ) {
                    if ( $val['wallet_request_status'] == 1 ) {
                        $wallet_request_status = "Pending";
                    } elseif ( $val['wallet_request_status'] == 2 ) {
                        $wallet_request_status = "Approved";
                    } else {
                        $wallet_request_status = "Disapproved";
                    }
                    unset( $val['request_date'] );
                    $allRequests[$key]['reqstatus'] = $wallet_request_status;
                }
            }
            $this->action_create_the_document( $allRequests, $export_table_header, $export_table_field_select, $heading );
        }
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $countDriverRequests,
            'view' => 'pagination/punbb' 
        ) );
        $getDriverWalletRequests    = $manage->getWalletAmtRequests( $_GET, $company_id, $getCnt = 0, $offset, REC_PER_PAGE );
        $taxicompany_details        = $add_model->taxicompany_details();
        $view                       = View::factory( 'admin/driver_wallet_request' )->bind( 'requestLists', $getDriverWalletRequests )->bind( 'countDriverRequests', $countDriverRequests )->bind( 'taxicompany_details', $taxicompany_details )->bind( 'pag_data', $pag_data )->bind( 'srch', $_GET )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'request_list' );
        $this->template->page_title = __( 'manage_driver_request' );
        $this->page_title           = __( 'manage_driver_request' );
        $this->template->content    = $view;
    }
    /** approve the driver wallet requests **/
    public function action_approveWalletRequest()
    {
        $manage = Model::factory( 'manage' );
        $result = $manage->updateRequestStatus( $_REQUEST['uniqueId'], '2', $this->crntTime, $this->session->get( 'userid' ) );
        //Flash message for Approve
        //==========================
        Message::success( __( 'driver_wallet_request_approved' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] ); //transaction/index
    }
    /** disapprove the driver wallet requests **/
    public function action_disapproveWalletRequest()
    {
        $manage = Model::factory( 'manage' );
        $result = $manage->updateRequestStatus( $_REQUEST['uniqueId'], '3', $this->crntTime, $this->session->get( 'userid' ) );
        //Flash message for Approve
        //==========================
        Message::success( __( 'driver_wallet_request_disapproved' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( $_SERVER['HTTP_REFERER'] ); //transaction/index
    }
    public function action_driverreferralhistory()
    {
        $id                        = $this->request->param( 'id' );
        $view_model                = Model::factory( 'driver' );
        $this->selected_page_title = __( 'manage_content' );
        //pagination loads here
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        $get_driver_referral_list  = $view_model->get_driver_referral_list( $id, 0, 0, false );
        //echo count($get_driver_referral_list); exit;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => count( $get_driver_referral_list ),
            'view' => 'pagination/punbb' 
        ) );
        $get_driver_referral_list   = $view_model->get_driver_referral_list( $id, REC_PER_PAGE, $offset, true );
        $view                       = View::factory( 'admin/driver_referral_list' )->bind( 'pag_data', $pag_data )->bind( 'driverReferralList', $get_driver_referral_list );
        $this->page_title           = __( 'driver_referral_history' );
        $this->template->title      = SITENAME . " | " . __( 'driver_referral_history' );
        $this->template->page_title = __( 'driver_referral_history' );
        $this->template->content    = $view;
    }
    /** 
     * Get Withdraw Request lists 
     **/
    public function action_withdrawrequest()
    {
        $company_id = $_SESSION['company_id'];
        $usertype   = $_SESSION['user_type'];
        $user_id    = $_SESSION['userid'];
        $manage     = Model::factory( 'manage' );
        $page_no    = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                 = 2 * ( $page_no - 1 );
        $submit                 = arr::get( $_REQUEST, 'status_form_submit' );
        $company_request_submit = arr::get( $_REQUEST, 'company_request_form' );
        if ( $submit && Validation::factory( $_REQUEST ) ) {
            $withdraw_request_id_array = explode( ",", $_POST["withdraw_request_id"] );
            $log_ids                   = array();
            if ( count( $withdraw_request_id_array ) > 0 ) {
                $attachment_name = "";
                foreach ( $withdraw_request_id_array as $l ) {
                    $log_ids[] = $manage->insert_withdraw_status_log( $_POST, $l );
                }
                if ( isset( $_FILES ) && $_FILES['attachment']['name'] != "" ) {
                    $re_file_name    = implode( "_", $log_ids );
                    $update_ids      = implode( ",", $log_ids );
                    $attachment_name = $re_file_name . "_" . $_FILES['attachment']['name'];
                    $filename        = Upload::save( $_FILES['attachment'], $attachment_name, DOCROOT . WITHDRAW_IMG_PATH );
                    $manage->update_withdraw_file_name( $log_ids, $attachment_name );
                }
                $message = ( $_POST['status'] == 1 ) ? __( 'withdraw_approved' ) : __( 'withdraw_denied' );
            }
            Message::success( $message );
            $this->request->redirect( "manage/withdrawrequest" );
        } else if ( $company_request_submit && Validation::factory( $_POST ) ) {
            $result = $manage->insert_company_withdraw_request( $_POST, $company_id, $user_id );
            if ( $result > 0 ) {
                Message::success( __( 'withdraw_req_sent_success' ) );
                $this->urlredirect->redirect( 'manage/withdrawrequest' );
            }
        }
        $all_data   = $manage->getWithdrawRequests( $_GET, $company_id, $getCnt = 1, 0 );
        $count_data = count( $all_data );
        //export section
        if ( isset( $_SESSION['download_set'] ) ) {
            if ( count( $all_data ) > 0 ) {
                foreach ( $all_data as $key => $value ) {
                    if ( array_key_exists( 'brand_type', $value ) ) {
                        $brand_type = ( $value['brand_type'] == 1 ) ? __( 'Multy' ) : __( 'single' );
                        unset( $value['brand_type'] );
                        $all_data[$key]['brand_type'] = $brand_type;
                    }
                    if ( array_key_exists( 'request_status', $value ) ) {
                        $status_label = "Not yet Approved";
                        if ( $value['request_status'] == 1 ) {
                            $status_label = "Approved";
                        } else if ( $value['request_status'] == 2 ) {
                            $status_label = "Deny";
                        }
                        unset( $value['request_status'] );
                        $all_data[$key]['request_status'] = $status_label;
                    }
                    if ( array_key_exists( 'request_date', $value ) ) {
                        $date = Commonfunction::getDateTimeFormat( $value['request_date'], 1 );
                        unset( $value['request_date'] );
                        $all_data[$key]['request_date'] = $date;
                    }
                }
            }
            if ( $company_id == 0 ) {
                $export_table_header       = array(
                     __( 'request_id' ),
                    __( 'brand_type' ),
                    __( 'name' ),
                    __( 'withdraw_amount' ),
                    __( 'request_date' ),
                    __( 'request_status' ) 
                );
                $export_table_field_select = array(
                     'request_id',
                    'brand_type',
                    'name',
                    'withdraw_amount',
                    'request_date',
                    'request_status' 
                );
            } else {
                $export_table_header       = array(
                     __( 'request_id' ),
                    __( 'withdraw_amount' ),
                    __( 'request_date' ),
                    __( 'request_status' ) 
                );
                $export_table_field_select = array(
                     'request_id',
                    'withdraw_amount',
                    'request_date',
                    'request_status' 
                );
            }
            $heading = 'Withdraw_Request';
            $this->action_create_the_document( $all_data, $export_table_header, $export_table_field_select, $heading );
        }
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => 10,
            'total_items' => $count_data,
            'view' => 'pagination/punbb' 
        ) );
        $get_withdraw_request       = $manage->getWithdrawRequests( $_GET, $company_id, $getCnt = 0, 0, $offset, 10 );
        $dashboard_withdraw_request = $manage->getDashboardWithdrawRequests( $company_id, 0 );
        $payment_mode               = $manage->get_withdraw_payment_mode();
        $view                       = View::factory( 'admin/withdraw/admin_withdraw_request' )->bind( 'dashboard_data', $dashboard_withdraw_request )->bind( 'data', $get_withdraw_request )->bind( 'count_data', $count_data )->bind( 'pag_data', $pag_data )->bind( 'srch', $_GET )->bind( 'company_id', $company_id )->bind( 'payment_mode', $payment_mode )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'request_list' );
        $this->template->page_title = ( $company_id ) ? __( 'company_withdraw' ) : __( 'withdraw_request' );
        $this->template->content    = $view;
    }
    /** 
     * Get Withdraw Request detail 
     **/
    public function action_withdrawdeatil()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $company_id     = $_SESSION['company_id'];
        if ( ( $usertype == 'M' ) ) {
            $this->urlredirect->redirect( 'manager/dashboard' );
        }
        $id     = $this->request->param( 'id' );
        $manage = Model::factory( 'manage' );
        $submit = arr::get( $_REQUEST, 'status_form_submit' );
        if ( $submit && Validation::factory( $_POST ) ) {
            $log_ids = $manage->insert_withdraw_status_log( $_POST, $id );
            if ( isset( $_FILES ) && $_FILES['attachment']['name'] != "" ) {
                $attachment_name = "";
                $attachment_name = $log_ids . "_" . $_FILES['attachment']['name'];
                $log_ids         = explode( ",", $log_ids );
                $filename        = Upload::save( $_FILES['attachment'], $attachment_name, DOCROOT . WITHDRAW_IMG_PATH );
                $manage->update_withdraw_file_name( $log_ids, $attachment_name );
            }
            Message::success( __( 'profile_updated_successfully' ) );
            $this->request->redirect( "manage/withdrawdeatil/" . $id );
        }
        $details = $manage->get_withdraw_deatil( $id );
        if ( count( $details ) > 0 ) {
            $type = ( isset( $details[0]["type"] ) && $details[0]["type"] ) ? 1 : 0;
        }
        $log          = $manage->get_withdraw_log( $id );
        $payment_mode = $manage->get_withdraw_payment_mode();
        if ( count( $details ) == 0 ) {
            $this->request->redirect( "manage/withdrawrequest" );
        }
        $view                       = View::factory( 'admin/withdraw/withdraw_details' )->bind( 'details', $details )->bind( 'log', $log )->bind( 'payment_mode', $payment_mode )->bind( 'company_id', $company_id )->bind( 'type', $type )->bind( 'id', $id );
        $this->page_title           = __( 'withdraw_request_details' );
        $this->template->title      = SITENAME . " | " . __( 'withdraw_request' );
        $this->template->page_title = __( 'withdraw_request' );
        $this->template->content    = $view;
    }
    public function action_block_unblock__withdraw_payment()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype != 'C' && $usertype != 'A' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $this->is_login();
        $status = $this->request->param( 'id' );
        $manage = Model::factory( 'manage' );
        $result = $manage->block_unblock_withdraw_payment( $_REQUEST['uniqueId'], $status );
        if ( $result == 1 ) {
            $text = ( $status == 1 ) ? "blocked" : "unblocked";
            Message::success( __( 'Checked requests have been changed to ' . $text . ' status.' ) );
        } else if($result == -1) {
            Message::error(__('atleast_onepaymode'));
        }else {
            Message::success( __( 'problem_in_update' ) );
        }
        $this->request->redirect( "admin/withdraw_payment_mode" );
    }
    /** 
     * Get Withdraw Request lists 
     **/
    public function action_driverwithdraw()
    {
        $company_id = $_SESSION['company_id'];
        $usertype   = $_SESSION['user_type'];
        $user_id    = $_SESSION['userid'];
        $manage     = Model::factory( 'manage' );
        $page_no    = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset = REC_PER_PAGE * ( $page_no - 1 );
        $submit = arr::get( $_REQUEST, 'status_form_submit' );
        if ( $submit && Validation::factory( $_POST ) ) {
            $withdraw_request_id_array = explode( ",", $_POST["withdraw_request_id"] );
            $log_ids                   = array();
            if ( count( $withdraw_request_id_array ) > 0 ) {
                $attachment_name = "";
                foreach ( $withdraw_request_id_array as $l ) {
                    $log_ids[] = $manage->insert_withdraw_status_log( $_POST, $l );
                }
                if ( isset( $_FILES ) && $_FILES['attachment']['name'] != "" ) {
                    $re_file_name    = implode( "_", $log_ids );
                    $update_ids      = implode( ",", $log_ids );
                    $attachment_name = $re_file_name . "_" . $_FILES['attachment']['name'];
                    $filename        = Upload::save( $_FILES['attachment'], $attachment_name, DOCROOT . WITHDRAW_IMG_PATH );
                    $manage->update_withdraw_file_name( $log_ids, $attachment_name );
                }
            }
            Message::success( __( 'profile_updated_successfully' ) );
            $this->request->redirect( "manage/driverwithdraw" );
        }
        $all_data   = $manage->getWithdrawRequests( $_GET, $company_id, $getCnt = 1, 1 );
        $count_data = count( $all_data );
        //export section
        if ( isset( $_SESSION['download_set'] ) ) {
            if ( count( $all_data ) > 0 ) {
                foreach ( $all_data as $key => $value ) {
                    if ( array_key_exists( 'brand_type', $value ) ) {
                        $brand_type = ( $value['brand_type'] == 1 ) ? __( 'Multy' ) : __( 'single' );
                        unset( $value['brand_type'] );
                        $all_data[$key]['brand_type'] = $brand_type;
                    }
                    if ( array_key_exists( 'request_status', $value ) ) {
                        $status_label = "Not yet Approved";
                        if ( $value['request_status'] == 1 ) {
                            $status_label = "Approved";
                        } else if ( $value['request_status'] == 2 ) {
                            $status_label = "Deny";
                        }
                        unset( $value['request_status'] );
                        $all_data[$key]['request_status'] = $status_label;
                    }
                    if ( array_key_exists( 'request_date', $value ) ) {
                        $date = Commonfunction::getDateTimeFormat( $value['request_date'], 1 );
                        unset( $value['request_date'] );
                        $all_data[$key]['request_date'] = $date;
                    }
                }
            }
            if ( $company_id == 0 ) {
                $export_table_header       = array(
                     __( 'request_id' ),
                    __( 'brand_type' ),
                    __( 'name' ),
                    __( 'withdraw_amount' ),
                    __( 'request_date' ),
                    __( 'request_status' ) 
                );
                $export_table_field_select = array(
                     'request_id',
                    'brand_type',
                    'name',
                    'withdraw_amount',
                    'request_date',
                    'request_status' 
                );
            } else {
                $export_table_header       = array(
                     __( 'request_id' ),
                    __( 'withdraw_amount' ),
                    __( 'request_date' ),
                    __( 'request_status' ) 
                );
                $export_table_field_select = array(
                     'request_id',
                    'withdraw_amount',
                    'request_date',
                    'request_status' 
                );
            }
            $heading = 'Withdraw Request';
            $this->action_create_the_document( $all_data, $export_table_header, $export_table_field_select, $heading );
        }
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_data,
            'view' => 'pagination/punbb' 
        ) );
        $get_withdraw_request       = $manage->getWithdrawRequests( $_GET, $company_id, $getCnt = 0, 1, $offset, REC_PER_PAGE );
        $dashboard_withdraw_request = $manage->getDashboardWithdrawRequests( $company_id, 1 );
        $payment_mode               = $manage->get_withdraw_payment_mode();
        $view                       = View::factory( 'admin/withdraw/driver_withdraw_request' )->bind( 'dashboard_data', $dashboard_withdraw_request )->bind( 'data', $get_withdraw_request )->bind( 'count_data', $count_data )->bind( 'pag_data', $pag_data )->bind( 'srch', $_GET )->bind( 'company_id', $company_id )->bind( 'payment_mode', $payment_mode )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'request_list' );
        $this->template->page_title = __( 'driver_withdraw' ) . " - Withdraw Request";
        $this->template->content    = $view;
    }
    //Admin Push Notification for Tracking Driver        
    public function action_adminpush_notification()
    {
        if ( isset( $_POST["driver_id"] ) ) {
            $driverid     = $_POST["driver_id"];
            $Commonmodel  = Model::factory( 'Commonmodel' );
            $driver_model = Model::factory( 'driver' );
            if ( isset( $_POST["datetime"] ) ) {
                $getcurrenttime = $_POST["datetime"];
                $availablity    = $driver_model->check_driver_travel_availability( $driverid, $getcurrenttime );
            } else {
                $availablity = $driver_model->check_driver_travel_availability( $driverid, date( 'Y-m-d H:i:s' ) );
            }
            $get_current_trip_logs = $driver_model->get_current_trip_logs( $driverid );
            $html                  = "";
            $val                   = "";
            if ( isset( $_POST["type"] ) ) {
                $dri = $Commonmodel->get_driver_currentstatus( $driverid );
                if ( !empty( $dri ) ) {
                    if ( $dri == "IN" ) {
                        $val = ' -- ' . __( 'online' );
                    } elseif ( $dri == "OUT" ) {
                        $val = ' -- ' . __( 'offline' );
                    }
                }
            }
            $driver_current_status = $driver_model->get_driver_current_status( $driverid );
            $update_time           = isset( $driver_current_status[0]->update_date ) ? "  Last Updated : " . Commonfunction::getDateTimeFormat( $driver_current_status[0]->update_date, 1 ) : "";
            //Driver Status Need to Change as Active
            if ( count( $availablity ) > 0 ) {
                $current_location = $availablity[0]['current_location'];
                $drop_location    = $availablity[0]['drop_location'];
                $passlog_id       = $availablity[0]['passengers_log_id'];
                $msg_status       = $availablity[0]['msg_status'];
                $driver_reply     = $availablity[0]['driver_reply'];
                echo "<span class='btn btn-mini btn-danger'>I AM HIRED $val</span>$update_time";
                if ( count( $get_current_trip_logs ) > 0 ) {
                    $distance = $get_current_trip_logs[0]['distance'];
                    echo "<a id='complete_button' class='btn btn-mini ' style='margin-left:15px;background:#3C85BA !important;color:white;cursor:default !important;' onclick='show_compopup();'>COMPLETE TRIP</a>";
                    echo "<span class='btn btn-mini btn-primary'>Distance so far - $distance</span>";
                }
                echo "<br/>From : " . $availablity[0]['current_location'];
                echo "<br/>To : " . $availablity[0]['drop_location'];
                $driver_current_status = $driver_model->get_driver_current_status( $driverid );
                echo "<input type='hidden' id='latitude' value=" . $driver_current_status[0]->latitude . ">";
                echo "<input type='hidden' id='longitude' value=" . $driver_current_status[0]->longitude . ">";
            } else {
                $driver_current_status = $driver_model->get_driver_current_status( $driverid );
                if ( $driver_current_status[0]->status == 'B' ) {
                    echo "<span class='btn btn-mini btn-primary'>" . __( 'i_am_busy' ) . $val . "</span>$update_time<br><br><div id='breakstatus'>
                        <span class='btn btn-mini btn-warning' >" . __( 'break_in' ) . "</span></div>";
                    echo "<input type='hidden' id='latitude' value=" . $driver_current_status[0]->latitude . ">";
                    echo "<input type='hidden' id='longitude' value=" . $driver_current_status[0]->longitude . ">";
                } else if ( $driver_current_status[0]->status == 'A' ) {
                    echo "<span class='btn btn-mini btn-primary'>" . __( 'i_am_active' ) . $val . "</span><br><br>";
                    echo "<input type='hidden' id='latitude' value=" . $driver_current_status[0]->latitude . ">";
                    echo "<input type='hidden' id='longitude' value=" . $driver_current_status[0]->longitude . ">";
                } else if ( $driver_current_status[0]->status == 'S' ) {
                    echo "<span class='btn btn-mini btn-primary'>" . __( 'i_am_service' ) . $val . "</span>$update_time<br><br><div id='breakstatus'>
                        <span class='btn btn-mini btn-warning' onclick='driverbreak(0)'>SERVICE IN</span><br>
                        </div>";
                    echo "<input type='hidden' id='latitude' value=" . $driver_current_status[0]->latitude . ">";
                    echo "<input type='hidden' id='longitude' value=" . $driver_current_status[0]->longitude . ">";
                } else {
                    echo "<span class='btn btn-mini btn-warning'>" . __( 'i_am_free' ) . $val . "</span>$update_time";
                    echo "<input type='hidden' id='latitude' value=" . $driver_current_status[0]->latitude . ">";
                    echo "<input type='hidden' id='longitude' value=" . $driver_current_status[0]->longitude . ">";
                }
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
        $this->page_title          = __( 'manage_popular' );
        $this->selected_page_title = __( 'manage_popular' );
        $manage_company            = Model::factory( 'manage' );  
        $search_post = $_REQUEST;
        $keyword = '';
        if(isset($search_post['search_popular'])){
			$keyword = trim($search_post['keyword']); 
		}     
        $total_records        = $manage_company->get_popularplaces($count=true,$offset='',$page_records='',$keyword);
        $page_records = REC_PER_PAGE;
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset           = $page_records * ( $page_no - 1 );
        $pag_data         = Pagination::factory( array(
             'current_page' => array(
				'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => $page_records,
            'total_items' => $total_records,
            'view' => 'pagination/punbb' 
        ));
        $popular_places = $manage_company->get_popularplaces( $count=false, $offset, $page_records,$keyword);
        $view                       = View::factory( 'admin/manage_popularplaces' )
									->bind( 'popular_places', $popular_places )
									->bind( 'pag_data', $pag_data )
									->bind( 'srch', $_REQUEST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_popular' );
        $this->template->page_title = __( 'manage_popular' );
        $this->template->content    = $view;
    }
} // End Welcome
?>
