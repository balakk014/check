<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
 * Contains User Management(Users)details
* @Package: Taximobility
* @Author: Taxi Team
* @URL : taximobility.com
********************************************/
class Controller_TaximobilityManageusers extends Controller_Siteadmin
{
    /**
     ****__construct()****
     */
    public function __construct( Request $request, Response $response )
    {
        parent::__construct( $request, $response );
        $this->is_login();
    }
    /**
     * ****action_index()****
     * @return people listings  view with pagination
     */
    public function action_index()
    {
        //Page Title
        $this->page_title          = __( 'menu_user_list' );
        $this->selected_page_title = __( 'menu_user_list' );
        $id                        = $this->session->get( 'id' );
        $userid                    = $this->session->get( 'userid' );
        $usrid                     = isset( $userid ) ? $userid : $id;
        $usertype                  = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $this->template->title      = __( 'menu_user_list' );
        $this->template->page_title = __( 'menu_user_list' );
        //import model
        $admin_user                 = Model::factory( 'authorize' );
        $count_user_list            = $admin_user->count_user_list();
        //pagination loads here
        //-------------------------
        $page_no                    = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - PAGE_NO );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_user_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_user_list              = $admin_user->all_user_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/admin_user_list' )->bind( 'title', $title )->bind( 'details', $details )->bind( 'all_user_list', $all_user_list )->bind( 'pag_data', $pag_data )->bind( 'srch', $_POST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'menu_user_list' );
        $this->template->page_title = __( 'menu_user_list' );
        $this->template->content    = $view;
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
    public function action_history()
    {
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $id                         = $this->session->get( 'id' );
        $userid                     = $this->session->get( 'userid' );
        $usrid                      = isset( $userid ) ? $userid : $id;
        $this->template->title      = __( 'menu_user_list' );
        $this->template->page_title = __( 'menu_user_list' );
        //import model
        $admin_user                 = Model::factory( 'authorize' );
        $UserList                   = $admin_user->user_list();
        $count_user_list            = $admin_user->count_user_list_history();
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
        $all_user_list              = $admin_user->all_user_list_history( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/admin_user_list_history' )->bind( 'title', $title )->bind( 'details', $details )->bind( 'all_user_list', $all_user_list )->bind( 'pag_data', $pag_data )->bind( 'UserList', $UserList )->bind( 'srch', $_POST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | Users History";
        $this->template->page_title = "Users History";
        $this->template->content    = $view;
    }
    /** passenger list **/
    public function action_passengers()
    {
        //Page Title
        $this->page_title          = __( 'menu_manage_passengers' );
        $this->selected_page_title = __( 'menu_manage_passengers' );
        $usertype                  = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $id                         = $this->session->get( 'id' );
        $userid                     = $this->session->get( 'userid' );
        $usrid                      = isset( $userid ) ? $userid : $id;
        $this->template->title      = __( 'menu_user_list' );
        $this->template->page_title = __( 'menu_user_list' );
        //import model
        $admin_user                 = Model::factory( 'authorize' );
        $manage_company             = Model::factory( 'manage' );
        $count_user_list            = $admin_user->count_passenger_list_history();
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
        $all_user_list              = $admin_user->all_passenger_list_history( $offset, REC_PER_PAGE );
        $get_allcompany             = $manage_company->get_allcompany( 'A' );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/passengers_list' )->bind( 'title', $title )->bind( 'details', $details )->bind( 'all_user_list', $all_user_list )->bind( 'pag_data', $pag_data )->bind( 'get_allcompany', $get_allcompany )->bind( 'srch', $_POST )->bind( 'Offset', $offset )->bind( 'all_list', $all_list );
        $this->template->title      = SITENAME . " | Passengers List";
        $this->template->page_title = "Passengers List";
        $this->template->content    = $view;
    }
    /** block passenger list**/
    public function action_block_passenger_request()
    {
		
        $this->is_login();
        $site     = Model::factory( 'site' );
        $passDets = $site->block_passenger_request( $_REQUEST['uniqueId'] );
       
        /*if ( count( $passDets ) > 0 ) {
            //Function to send mail or sms to blocked passengers
            $this->sendMailSmspassengers( $passDets[0]['passEmails'], $passDets[0]['passMobiles'], 'blocked' );
        }*/
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    /** actvie passenger list**/
    public function action_active_passenger_request()
    {
        $this->is_login();
        $site     = Model::factory( 'site' );
        $passDets = $site->active_passenger_request( $_REQUEST['uniqueId'] );
        /*if ( count( $passDets ) > 0 ) {
            //Function to send mail or sms to blocked passengers
            $this->sendMailSmspassengers( $passDets[0]['passEmails'], $passDets[0]['passMobiles'], 'activated' );
        }*/
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        $this->request->redirect( $_SERVER['HTTP_REFERER'] );
    }
    /** trash passenger list**/
    public function action_trash_passenger_request()
    {
        $this->is_login();
        $site     = Model::factory( 'site' );
        $passDets = $site->trash_passenger_request( $_REQUEST['uniqueId'] );
        /*if ( count( $passDets ) > 0 ) {
            //Function to send mail or sms to blocked passengers
            $this->sendMailSmspassengers( $passDets[0]['passEmails'], $passDets[0]['passMobiles'], 'deleted' );
        } */
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        Message::success( __( 'Checked requests have been moved to the Trash..' ) );
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
        $this->request->redirect( "manageusers/passengers" );
    }
    public function action_block_users_request()
    {
        $this->is_login();
        $site             = Model::factory( 'site' );
        $status           = $site->block_users_request( $_REQUEST['uniqueId'] );
        $isDriverAssigned = $site->isdriverassigned( $_REQUEST['uniqueId'] );
        if ( count( $isDriverAssigned ) == 0 ) {
            $status = $site->block_users_request( $_REQUEST['uniqueId'], 2 );
            Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        } else {
            Message::error( __( 'assigned_driver_not_block' ) );
        }
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        $this->request->redirect( "manageusers/" . $page );
    }
    public function action_active_users_request()
    {
        $this->is_login();
        $site     = Model::factory( 'site' );
        $status   = $site->active_users_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been changed to activated status.' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manageusers/" . $page ); //transaction/index
    }
    public function action_trash_users_request()
    {
        $this->is_login();
        $site             = Model::factory( 'site' );
        $status           = $site->trash_users_request( $_REQUEST['uniqueId'] );
        $isDriverAssigned = $site->isdriverassigned( $_REQUEST['uniqueId'] );
        if ( count( $isDriverAssigned ) == 0 ) {
            $status = $site->block_users_request( $_REQUEST['uniqueId'], 2 );
            Message::success( __( 'Checked requests have been changed to blocked status.' ) );
        } else {
            Message::error( __( 'assigned_driver_not_block' ) );
        }
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manageusers/" . $page );
    }
    public function action_delete_users_request()
    {
        $this->is_login();
        $site     = Model::factory( 'site' );
        $status   = $site->delete_users_request( $_REQUEST['uniqueId'] );
        $pagedata = explode( "/", $_SERVER["REQUEST_URI"] );
        $page     = isset( $pagedata[3] ) ? $pagedata[3] : '';
        //Flash message for Reject
        //==========================
        Message::success( __( 'Checked requests have been deleted successfully' ) );
        //redirects to job_feedback details page after deletion
        $this->request->redirect( "manageusers/" . $page );
    }
    /**
     * ****action_search()****
     * @param 
     * @return search user listings
     */
    public function action_search()
    {
        //Page Title
        $this->page_title          = __( 'menu_user_list' );
        $this->selected_page_title = __( 'menu_user_list' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $admin                     = Model::factory( 'admin' );
        $count_user_list           = $admin->count_usersearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['user_type'] ) ), trim( Html::chars( $_REQUEST['status'] ) ) );
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
            'total_items' => $count_user_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_user_list = $admin->get_all_search_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['user_type'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/admin_user_list' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_user_list', $all_user_list );
        $this->template->content = $view;
    }
    /** passengers list **/
    public function action_passenger_search()
    {
        //Page Title
        $this->page_title          = __( 'menu_manage_passengers' );
        $this->selected_page_title = __( 'menu_manage_passengers' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $admin                     = Model::factory( 'admin' );
        $authorize                 = Model::factory( 'authorize' );
        $count_user_list           = $admin->get_all_searchpassenger_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ) );
        $all_list                  = $count_user_list;
        $count_user_list           = count( $count_user_list );
        $manage_company            = Model::factory( 'manage' );
        $get_allcompany            = $manage_company->get_allcompany();
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
            'total_items' => $count_user_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_user_list = $admin->get_all_searchpassenger_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ), $offset, REC_PER_PAGE );
        }
        if ( isset( $_SESSION['download_set'] ) ) {
            $export = $admin->get_all_searchpassenger_list( trim( Html::chars( $_REQUEST['keyword'] ) ), trim( Html::chars( $_REQUEST['status'] ) ), trim( Html::chars( $_REQUEST['filter_company'] ) ), $offset, $all_list );
            if ( count( $export ) > 0 ) {
                foreach ( $export as $key => $value ) {
                    if ( array_key_exists( 'created_date', $value ) ) {
                        $date = Commonfunction::getDateTimeFormat( $value['created_date'], 1 );
                        unset( $value['created_date'] );
                        $export[$key]['created_date'] = $date;
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
            $this->action_create_the_document( $export, $export_table_header, $export_table_field_select, $heading );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/passengers_list' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'get_allcompany', $get_allcompany )->bind( 'all_user_list', $all_user_list )->bind( 'all_list', $all_list );
        $this->template->content = $view;
    }
    //live users list
    public function action_live_users()
    {
        //Page Title
        $this->page_title           = __( 'live_passengers' );
        $this->selected_page_title  = __( 'live_passengers' );
        $id                         = $this->session->get( 'id' );
        $userid                     = $this->session->get( 'userid' );
        $usrid                      = isset( $userid ) ? $userid : $id;
        $usertype                   = $_SESSION['user_type'];
        $this->template->title      = __( 'menu_user_list' );
        $this->template->page_title = __( 'menu_user_list' );
        //import model
        $admin_user                 = Model::factory( 'authorize' );
        $cid                        = $_SESSION['company_id'];
        $dashboard                  = Model::factory( 'admin' );
        $activeusers_list           = $dashboard->get_all_activeusers_list( $cid );
        $activeusers_list_count     = $dashboard->get_activeusers_list_count( $cid );
        //pagination loads here
        //-------------------------
        $page_no                    = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                     = REC_PER_PAGE * ( $page_no - PAGE_NO );
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $activeusers_list_count,
            'view' => 'pagination/punbb' 
        ) );
        $all_user_list              = $dashboard->all_users_list( $offset, REC_PER_PAGE, $cid );
        //****pagination ends here***//
        $details                    = '';
        //send data to view file 
        $view                       = View::factory( 'admin/admin_live_user_list' )->bind( 'title', $title )->bind( 'details', $details )->bind( 'all_user_list', $all_user_list )->bind( 'pag_data', $pag_data )->bind( 'UserList', $UserList )->bind( 'srch', $_POST )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'live_passengers' );
        $this->template->page_title = __( 'live_passengers' );
        $this->template->content    = $view;
    }
    //live users search
    public function action_live_users_search()
    {
        //Page Title
        $this->page_title          = __( 'live_users' );
        $this->selected_page_title = __( 'live_users' );
        //default empty list and offset
        $search_list               = '';
        $offset                    = '';
        //Find page action in view
        $action                    = $this->request->action();
        //import model
        $admin                     = Model::factory( 'admin' );
        $UserList                  = $admin->live_usersearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ) );
        $count_user_list           = $admin->count_live_usersearch_list( trim( Html::chars( $_REQUEST['keyword'] ) ) );
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
            'total_items' => $count_user_list,
            'view' => 'pagination/punbb' 
        ) );
        //get form submit request
        $search_post = arr::get( $_REQUEST, 'search_user' );
        //Post results for search 
        if ( $_REQUEST ) {
            $all_user_list = $admin->get_all_live_search_list( trim( Html::chars( $_REQUEST['keyword'] ) ), $offset, REC_PER_PAGE );
        }
        //set data to view file    
        $view                    = View::factory( 'admin/admin_live_user_list' )->bind( 'title', $title )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_user_list', $all_user_list );
        $this->template->content = $view;
    }
    /** passenger list **/
    public function action_passengerspromo()
    {
        //Page Title
        $this->page_title          = __( 'passengerspromo' );
        $this->selected_page_title = __( 'passengerspromo' );
        $usertype                  = $_SESSION['user_type'];
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $id                         = $this->session->get( 'id' );
        $userid                     = $this->session->get( 'userid' );
        $usrid                      = isset( $userid ) ? $userid : $id;
        //company id from session
        $company_id                 = $this->session->get( 'company_id' );
        $this->template->title      = __( 'menu_user_list' );
        $this->template->page_title = __( 'menu_user_list' );
        //import model
        $admin_user                 = Model::factory( 'authorize' );
        $manage_company             = Model::factory( 'manage' );
        $add_model                  = Model::factory( 'add' );
        $getpromocode               = $manage_company->getpromocode();
        $count_user_list            = $admin_user->count_passenger_list_history();
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
        $all_user_list              = $admin_user->all_passenger_list_history( $offset, REC_PER_PAGE );
        //send data to view file 
        $view                       = View::factory( 'admin/passengers_promo' )->bind( 'title', $title )->bind( 'details', $details )->bind( 'all_user_list', $all_user_list )->bind( 'pag_data', $pag_data )->bind( 'promocode', $getpromocode )->bind( 'srch', $_POST )->bind( 'company_id', $company_id )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . ' ' . __( 'passengerspromo' );
        $this->template->page_title = __( 'passengerspromo' );
        $this->template->content    = $view;
    }
    public function action_getuserslist()
    {
        $manage = Model::factory( 'manage' );
        echo $manage->getuserslist( $_GET['company_id'] );
        exit;
    }
    public function action_sendpromocode()
    {
        $manage      = Model::factory( 'manage' );
        $Commonmodel = Model::factory( 'Commonmodel' );
        $id          = $this->session->get( 'id' );
        $userid      = $this->session->get( 'userid' );
        $usrid       = isset( $userid ) ? $userid : $id;
        //company id from session
        $company_id  = $this->session->get( 'company_id' );
        $usertype    = $_SESSION['user_type'];
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $this->template->title      = __( 'menu_user_list' );
        $this->template->page_title = __( 'menu_user_list' );
        $search_post                = $_POST;
        $mail_type                  = $_POST['mail_type']; //this function used to remove unwanted quotes
        $promo_code                 = $_POST['promo_code'];
        $promo_discount             = $_POST['promo_discount'];
        $subjects                   = $_POST['subject'];
        $content                    = $_POST['content'];
        $to_user                    = ( $mail_type != 1 ) ? $_POST['to_user'] : '';
        $start_date                 = $_POST['start_date'];
        $expire_date                = $_POST['expire_date'];
        $promo_limit                = $_POST['limit'];
        $promocode_exit             = $manage->check_promo_exit( $promo_code, $company_id );
        $cid                        = ( $company_id == 0 ) ? $_POST['company'] : $company_id;
        if ( !empty( $cid ) ) {
            $email_logo = URL_BASE . SITE_LOGO_IMGPATH . '/' . $company_id . '_email_logo.png';
        } else {
            $email_logo = EMAIL_TEMPLATE_LOGO;
        }
        if ( $promocode_exit == 0 ) {
            if ( $mail_type == 1 ) {
                $passenger_list = $manage->getactive_users( $cid );
            } else {
                $passenger_list = $to_user;
            }
            if ( count( $passenger_list ) > 0 ) {
                $passengers_details = array();
                $promo_used_details = "";
                foreach ( $passenger_list as $values ) {
                    $pdetail              = explode( '~', $values );
                    $passengers_details[] = (int) $pdetail[0];
                }
                if ( count( $passengers_details ) > 0 ) {
                    $array              = array_fill_keys( array_keys( array_flip( $passengers_details ) ), 0 );
                    $promo_used_details = serialize( $array );
                    $currenttime        = $Commonmodel->getcompany_all_currenttimestamp( COMPANY_CID );
                    $insert_array       = array(
                         "passenger_id" => $passengers_details,
                        "company_id" => $cid,
                        "promocode" => $promo_code,
                        "promo_discount" => $promo_discount,
                        "promo_used" => "0",
                        "amount_earned" => "0",
                        "start_date" => $start_date,
                        "expire_date" => $expire_date,
                        "promo_limit" => $promo_limit,
                        "createdate" => $currenttime,
                        "promo_used_details" => $promo_used_details 
                    );
                    $promo_insert       = $Commonmodel->insert_promocode( $insert_array );
                    foreach ( $passenger_list as $values ) {
                        $pdetail           = explode( '~', $values );
                        $id                = $pdetail[0];
                        $email             = $pdetail[1];
                        $name              = $pdetail[2];
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
                            REPLACE_SITEEMAIL => CONTACT_EMAIL,
                            REPLACE_SITEURL => URL_BASE,
                            REPLACE_COPYRIGHTS => SITE_COPYRIGHT,
                            REPLACE_COPYRIGHTYEAR => COPYRIGHT_YEAR 
                        );
                        $message           = $this->emailtemplate->emailtemplate( DOCROOT . TEMPLATEPATH . 'promocode_message.html', $replace_variables );
                        $to                = $email;
                        $from              = CONTACT_EMAIL;
                        $subject           = $subjects;
                        $redirect          = "";
                        $mail_model        = Model::factory( 'add' );
                        $smtp_result       = $mail_model->smtp_settings();
                        if ( !empty( $smtp_result ) && ( $smtp_result[0]['smtp'] == 1 ) ) {
                            $redirect = "manage/promocode";
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
                    Message::success( __( 'promo_success' ) );
                    $this->request->redirect( "manage/promocode" );
                }
            } else {
                Message::error( __( 'no_user_to_send_promocode' ) );
                $this->request->redirect( "manageusers/passengerspromo" );
            }
        } else {
            Message::success( __( 'promo_already' ) );
            $this->request->redirect( "manage/promocode" );
        }
        $this->request->redirect( "manageusers/passengerspromo" );
    }
    public function action_checkpromocode()
    {
        $manage            = Model::factory( 'manage' );
        $companyId         = isset( $_REQUEST["company_id"] ) ? $_REQUEST["company_id"] : 0;
        $check_promo_exist = $manage->check_promo_exit( $_REQUEST["promo"], $companyId );
        if ( $check_promo_exist == 0 ) {
            echo '<span style="color:green;">' . __( 'promo_is_avaliable' ) . '</span>';
            exit;
        } else {
            echo '<span style="color:red;">' . __( 'promo_already' ) . '</span>';
            exit;
        }
    }
    public function action_getcompanypromo()
    {
        $company_id     = $_GET['company_id'];
        $manage_company = Model::factory( 'manage' );
        $getpromocode   = $manage_company->getpromocode();
        if ( $company_id != 0 ) {
            $company_dets = $manage_company->getcompanydomainName( $company_id );
            $getpromocode = $company_dets['company_domain'] ? '' : "_" . $getpromocode;
        }
        echo $getpromocode;
        exit;
    }
    /** Function to get Passenger wallet money add logs **/
    public function action_passengerwalletlogs()
    {
        $usertype = $this->session->get( 'user_type' );
        if ( $usertype != 'A' && $usertype != 'S' && $usertype != 'C' ) {
            $this->request->redirect( "admin/dashboard" );
        }
        $manageMdl = Model::factory( 'manage' );
        $page_no   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset       = REC_PER_PAGE * ( $page_no - 1 );
        $getTotalLogs = $manageMdl->passengerWalletLogs( $_GET, $setPagination = 0 );
        //~ print_r($getTotalLogs);exit;
        $getLogsCount = count( $getTotalLogs );
        //export section
        if ( isset( $_SESSION['download_set'] ) ) {
            if ( count( $getTotalLogs ) > 0 ) {
                foreach ( $getTotalLogs as $key => $value ) {
                    if ( array_key_exists( 'createdate', $value ) ) {
                        $date = Commonfunction::getDateTimeFormat( $value['createdate'], 1 );
                        unset( $value['createdate'] );
                        $getTotalLogs[$key]['createdate'] = $date;
                    }
                }
            }
            $export_table_header       = array(
                 __( 'booking_date' ),
                 __( 'transaction_id' ),
                __( 'name' ),
                __( 'phone_label' ),
                __( 'amount' ),
                __( 'payment_type' ) 
            );
            $export_table_field_select = array(
                'createdate',
				'transaction_id',
                'name',
                'mobile_number',
                'amount',
                'payment_type' 
            );
            $heading                   = 'passengerwalletlog';
            $this->action_create_the_document( $getTotalLogs, $export_table_header, $export_table_field_select, $heading );
        }
        $pag_data                   = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $getLogsCount,
            'view' => 'pagination/punbb' 
        ) );
        $getPassengerWalletLogs     = $manageMdl->passengerWalletLogs( $_GET, $setPagination = 1, $offset, REC_PER_PAGE );
        //send data to view file 
        $view                       = View::factory( 'admin/passenger_wallet_logs' )->bind( 'requestLists', $getPassengerWalletLogs )->bind( 'getLogsCount', $getLogsCount )->bind( 'pag_data', $pag_data )->bind( 'srch', $_GET )->bind( 'Offset', $offset );
        $this->template->title      = SITENAME . " | " . __( 'manage_passenger_wallet' );
        $this->template->page_title = __( 'manage_passenger_wallet' );
        $this->template->content    = $view;
    }
    
    //Transactions Details
    public function action_passengerwalletlogs_details()
    {
        $manageMdl = Model::factory( 'manage' );
        $transaction_id              = explode( '/', $_SERVER['REQUEST_URI'] );
        if(isset($transaction_id[3])){
			$transaction_id = base64_decode($transaction_id[3]);
			$log_details = $manageMdl->viewtransaction_details( $transaction_id );
			if ( count( $log_details ) <= 0 ) {
				Message::error( __( 'invalid_request' ) );
				$this->request->redirect( "manageusers/passengerwalletlogs" );
			}
		}else{
			Message::error( __( 'invalid_request' ) );
			$this->request->redirect( "manageusers/passengerwalletlogs" );
		}
        //send data to view file
        $view                       = View::factory( 'admin/passenger_wallet_logs_details' )->bind( 'log_details', $log_details );
        $this->page_title           = __( 'passenger_wallet_log_details' );
        $this->template->title      = SITENAME . " | " . __( 'passenger_wallet_log_details' );
        $this->template->page_title = __( 'passenger_wallet_log_details' );
        $this->template->content    = $view;
    }
    
} // End Welcome
