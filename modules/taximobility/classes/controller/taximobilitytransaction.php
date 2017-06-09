<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
* Contains Transaction details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************/
class Controller_TaximobilityTransaction extends Controller_Siteadmin
{
    /**
     ****__construct()****
     * Common Function in this controller
     */
    public function __construct( Request $request, Response $response )
    {
        parent::__construct( $request, $response );
        $this->is_login();
    }
    /**
     ****is login()****
     * function to check whether the user is logged in or not
     */
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
    /**
     ****Transaction List****
     * function to get overall transaction list
     */
    public function action_admintransaction()
    {
        $find_url       = explode( '/', $_SERVER['REQUEST_URI'] );
        $split          = explode( '?', $find_url[3] );
        $list           = $split[0];
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $manage_transaction = Model::factory( 'transaction' );
        $common_model       = Model::factory( 'commonmodel' );
        if ( $list == 'all' ) {
            $page_title = __( "all_transaction_log" );
        } elseif ( $list == 'success' ) {
            $page_title = __( "success_transaction_log" );
        } elseif ( $list == 'cancelled' ) {
            $page_title = __( "cancelled_transaction_log" );
        } elseif ( $list == 'rejected' ) {
            $page_title = __( "rejected_trip_log" );
        } elseif ( $list == 'pendingpayment' ) {
            $page_title = __( "pending_payment_details" );
        } else {
            $page_title = __( "all_transaction_log" );
            $list       = 'all';
        }
        $get_allcompany         = $manage_transaction->get_allcompany_tranaction( $usertype );
        $taxilist               = $manage_transaction->gettaxidetails( '', '', array ());
        $passengerlist          = $manage_transaction->getpassengerdetails( '', '', array ());
        $driverlist             = $manage_transaction->getdriverdetails( '', '', array ());
        $managerlist            = $manage_transaction->getmanagerdetails( '' );
        $startdate              = date( 'Y-m-01 00:00:00' );
        $enddate                = convert_timezone( 'now', $_SESSION['timezone'] );
        $count_transaction_list = $manage_transaction->count_admintransaction_list( $list, 'All', 'All', 'All', 'All', 'All', $startdate, $enddate, '', '', '' );
        $grpahdata              = $manage_transaction->getgraphvalues( $list, 'All', 'All', 'All', 'All', '', $startdate, $enddate, '', '' );
        $gateway_details        = $common_model->gateway_details();
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
        $all_transaction_list       = $manage_transaction->transaction_details( $list, 'All', 'All', 'All', 'All', 'All', $startdate, $enddate, $offset, REC_PER_PAGE, '', '', '' );
        $view                       = View::factory( 'admin/report/admintransaction' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'count_transaction_list', $count_transaction_list )->bind( 'taxilist', $taxilist )->bind( 'driverlist', $driverlist )->bind( 'managerlist', $managerlist )->bind( 'passengerlist', $passengerlist )->bind( 'get_allcompany', $get_allcompany )->bind( 'gateway_details', $gateway_details )->bind( 'grpahdata', $grpahdata )->bind( 'grpahstartdate', $startdate )->bind( 'transaction_type', $list )->bind( 'id', $id );
        $this->page_title           = $page_title;
        $this->template->title      = $page_title . " | " . SITENAME;
        $this->template->page_title = $page_title;
        $this->template->content    = $view;
    }
    /**
     ****Transaction List()****
     * function to get transaction list with search option
     */
    public function action_admintransaction_list()
    {
        $find_url       = explode( '/', $_SERVER['REQUEST_URI'] );
        $split          = explode( '?', $find_url[3] );
        $list           = $split[0];
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $list = ( isset( $_REQUEST['travelSts'] ) ) ? $_REQUEST['travelSts'] : $list;
        if ( $list == 'all' ) {
            $page_title = __( "all_transaction_log" );
        } elseif ( $list == 'success' ) {
            $page_title = __( "success_transaction_log" );
        } elseif ( $list == 'cancelled' ) {
            $page_title = __( "cancelled_transaction_log" );
        } elseif ( $list == 'rejected' ) {
            $page_title = __( "rejected_trip_log" );
        } elseif ( $list == 'pendingpayment' ) {
            $page_title = __( "pending_payment_details" );
        } else {
            $page_title = __( "all_transaction_log" );
            $list       = 'all';
        }
        $company               = trim( Html::chars( $_REQUEST['filter_company'] ) );
        $startdate             = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['startdate'] ) ), 1 );
        $enddate               = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['enddate'] ) ), 2 );
        $taxiid                = isset( $_REQUEST['taxiid'] ) ? trim( Html::chars( $_REQUEST['taxiid'] ) ) : '';
        $driver_id             = trim( Html::chars( $_REQUEST['driver_id'] ) );
        $manager_id            = trim( Html::chars( $_REQUEST['manager_id'] ) );
        $passengerid           = trim( Html::chars( $_REQUEST['passengerid'] ) );
        $transaction_id        = trim( Html::chars( $_REQUEST['transaction_id'] ) );
        $payment_type          = trim( Html::chars( $_REQUEST['payment_type'] ) );
        $payment_mode          = trim( Html::chars( $_REQUEST['payment_mode'] ) );
        $trip_id               = trim( Html::chars( $_REQUEST['trip_id'] ) );
        $manage_transaction    = Model::factory( 'transaction' );
        $common_model          = Model::factory( 'commonmodel' );
        $manager_details_array = array();
        if ( ( $manager_id != "" ) && ( $manager_id != "All" ) || ( $usertype == 'M' ) ) {
            $manager_details = $manage_transaction->manager_details( $manager_id );
            if ( count( $manager_details ) > 0 ) {
                $manager_details_array = $manager_details;
            }
        }
        $get_allcompany = $manage_transaction->get_allcompany_tranaction();
        $taxilist       = $manage_transaction->gettaxidetails( $company, $manager_id, $manager_details_array );
        $passengerlist  = $manage_transaction->getpassengerdetails( $company, '', $manager_details_array );
        $driverlist     = $manage_transaction->getdriverdetails( $company, $manager_id, $manager_details_array );
        $managerlist    = $manage_transaction->getmanagerdetails( $company );
        $taxi_ids       = $driver_ids = "";
        if ( ( $manager_id != "" ) && ( $manager_id != "All" ) || ( $usertype == 'M' ) ) {
            if ( count( $taxilist ) > 0 ) {
                foreach ( $taxilist as $taxis ) {
                    $taxi_ids .= $taxis["taxi_id"] . ',';
                }
                $taxi_ids = substr( $taxi_ids, 0, strlen( $taxi_ids ) - 1 );
            }
            if ( count( $driverlist ) > 0 ) {
                foreach ( $driverlist as $drivers ) {
                    $driver_ids .= $drivers["id"] . ',';
                }
                $driver_ids = substr( $driver_ids, 0, strlen( $driver_ids ) - 1 );
            }
        }
        $count_transaction_list = $manage_transaction->count_admintransaction_list( $list, $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id, $payment_type, $payment_mode, '', $taxi_ids, $driver_ids, $trip_id );
        //pagination loads here
        $page_no                = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset               = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data             = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_transaction_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_transaction_list = $manage_transaction->transaction_details( $list, $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $offset, REC_PER_PAGE, $transaction_id, $payment_type, $payment_mode, '', $taxi_ids, $driver_ids, $trip_id );
        //calculation to restrict 60 days report shown in graph
        $noDays               = round( ( strtotime( $enddate ) - strtotime( $startdate ) ) / ( 3600 * 24 ) );
        if ( $noDays > 60 ) {
            $enddate = date( "Y-m-d H:i:s", strtotime( "+60 day", strtotime( $startdate ) ) );
        }
        $grpahdata       = $manage_transaction->getgraphvalues( $list, $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id, $payment_type, $payment_mode, '', $taxi_ids, $driver_ids );
        $graphStartDate  = Commonfunction::getDateTimeFormat( $startdate, 1 );
        $graphEndDate    = Commonfunction::getDateTimeFormat( $enddate, 1 );
        $gateway_details = $common_model->gateway_details();
        //****pagination ends here***//
        if ( isset( $_SESSION['download_set'] ) ) {
            $all_transaction_list = $manage_transaction->export_transaction_details( $list, $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $offset, REC_PER_PAGE, $transaction_id, $payment_type, $payment_mode, '', $taxi_ids, $driver_ids, $trip_id );
            $all_data             = $all_transaction_list;
            $ccur                 = CURRENCY_FORMAT;
            if ( count( $all_data ) > 0 ) {
                foreach ( $all_data as $key => $value ) {
                    if ( array_key_exists( 'payment_type', $value ) ) {
                        if ( $value['payment_type'] == 2 ) {
                            $payment_type = "Credit Card";
                        } else if ( $value['payment_type'] == 3 ) {
                            $payment_type = __( 'new_credit_card' );
                        } else if ( $value['payment_type'] == 4 ) {
                            $payment_type = __( 'account' );
                        } else {
                            $payment_type = "Cash";
                        }
                        unset( $value['payment_type'] );
                        $all_data[$key]['payment_type'] = $payment_type;
                    }
                    if ( $value['actual_pickup_time'] != '0000-00-00 00:00:00' && $value['actual_pickup_time'] != '' && isset( $value['actual_pickup_time'] ) ) {
                        $journeyDate = Commonfunction::getDateTimeFormat( $value['actual_pickup_time'], 1 );
                    } else {
                        $journeyDate = Commonfunction::getDateTimeFormat( $value['pickup_time'], 1 );
                    }
                    unset( $value['journey_date'] );
                    $all_data[$key]['journey_date'] = $journeyDate;
                    $waitingTime                    = '-';
                    if ( !empty( $value['waiting_time'] ) ) {
                        $waitingTimeArr    = explode( " ", $value['waiting_time'] );
                        $waitingTimeFormat = explode( ":", $waitingTimeArr[0] );
                        $waitingTime       = ( !isset( $waitingTimeFormat[2] ) ) ? '00:' . $waitingTimeArr[0] : $waitingTimeArr[0];
                    }
                    unset( $value['waiting_time'] );
                    $all_data[$key]['waiting_time'] = $waitingTime;
                    if ( !empty( $value['distance'] ) ) {
                        $distance = round( $value['distance'], 2 );
                    } else {
                        $distance = '-';
                    }
                    unset( $value['distance'] );
                    $all_data[$key]['distance'] = $distance;
                    $admin_amount               = ( !empty( $value['admin_amount'] ) ) ? $ccur . ' ' . $value['admin_amount'] : '-';
                    unset( $value['admin_amount'] );
                    $all_data[$key]['admin_amount'] = $admin_amount;
                    $company_amount                 = ( !empty( $value['company_amount'] ) ) ? $ccur . ' ' . $value['company_amount'] : '-';
                    unset( $value['company_amount'] );
                    $all_data[$key]['company_amount'] = $company_amount;
                    $nightfare                        = ( !empty( $value['nightfare'] ) ) ? $ccur . ' ' . $value['nightfare'] : '-';
                    unset( $value['nightfare'] );
                    $all_data[$key]['nightfare'] = $nightfare;
                    $eveningfare                 = ( !empty( $value['eveningfare'] ) ) ? $ccur . ' ' . $value['eveningfare'] : '-';
                    unset( $value['eveningfare'] );
                    $all_data[$key]['eveningfare'] = $eveningfare;
                    $fare                          = ( !empty( $value['fare'] ) ) ? $ccur . ' ' . round( $value['fare'], 2 ) : '-';
                    unset( $value['fare'] );
                    $all_data[$key]['fare'] = $fare;                    
                    
                    $travelstatus = '';
                    if($list == 'rejected'){	
						$travelstatus = __('rejected_by_driver');
					}else{
						if($value['travel_status'] == 0) {
							$travelstatus = __('not_completed'); 
						} else if($value['travel_status'] == 1) { 
							$travelstatus = __('completed'); 
						} else if($value['travel_status'] == 2) { 
							$travelstatus = __('inprogress'); 
						} else if($value['travel_status'] == 3) { 
							$travelstatus = __('start_to_pickup'); 
						} else if($value['travel_status'] == 4) { 
							$travelstatus = __('cancel_by_passenger'); 
						} else if($value['travel_status'] == 8) { 
							$travelstatus = __('cancelled_by_dispatcher'); 
						} else if($value['travel_status'] == 9 || $value['driver_reply'] == 'C') { 
							$travelstatus = __('cancelled_by_driver'); 
						} else { $travelstatus = __('not_completed'); } 
					}
					$all_data[$key]['travel_status'] = $travelstatus;
                }
            }
            $export_table_header = array(
                 __( 'trip_id' ),
                __( 'passenger_name' ),
                __( 'driver_name' ),
                __( 'journey_date' ),
                __( 'passenger_email' ),
                __( 'Current_Location' ),
                __( 'Drop_Location' ),
                __( 'companyname' ) 
            );
            if ( $list != 'rejected' ) {
                $export_table_header[] = __( 'cctransaction_id' );
                $export_table_header[] = __( 'payment_type' );
                $export_table_header[] = __( 'admin_commision' );
                $export_table_header[] = __( 'company_commision' );
            }
            if ( $list != 'rejected' && $list != 'cancelled' ) {
                $export_table_header[] = __( 'waiting_time_with_format' );
                $export_table_header[] = __( 'distance_km' );
                $export_table_header[] = __( 'trip_total_fare' );
                $export_table_header[] = __( 'nightfare' );
                $export_table_header[] = __( 'eveningfare' );
            } elseif ( $list == 'cancelled' ) {
                $export_table_header[] = __( 'cancel_fare' );
            }
            $export_table_header[] = __( 'travel_status' );
            $export_table_field_select = array(
                 'passengers_log_id',
                'passenger_name',
                'driver_name',
                'journey_date',
                'passenger_email',
                'current_location',
                'drop_location',
                'company_name' 
            );
            if ( $list != 'rejected' ) {
                $export_table_field_select[] = 'transaction_id';
                $export_table_field_select[] = 'payment_type';
                $export_table_field_select[] = 'admin_amount';
                $export_table_field_select[] = 'company_amount';
            }
            if ( $list != 'rejected' && $list != 'cancelled' ) {
                $export_table_field_select[] = 'waiting_time';
                $export_table_field_select[] = 'distance';
                $export_table_field_select[] = 'fare';
                $export_table_field_select[] = 'nightfare';
                $export_table_field_select[] = 'eveningfare';
            } elseif ( $list == 'cancelled' ) {
                $export_table_field_select[] = 'fare';
            }
            $export_table_field_select[] = 'travel_status';
            $heading = "Export";
            $this->action_create_the_document( $all_data, $export_table_header, $export_table_field_select, $heading );
        }
        //send data to view file 
        $view                       = View::factory( 'admin/report/admintransaction' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'count_transaction_list', $count_transaction_list )->bind( 'taxilist', $taxilist )->bind( 'driverlist', $driverlist )->bind( 'managerlist', $managerlist )->bind( 'passengerlist', $passengerlist )->bind( 'get_allcompany', $get_allcompany )->bind( 'grpahdata', $grpahdata )->bind( 'graphStartDate', $graphStartDate )->bind( 'graphEndDate', $graphEndDate )->bind( 'gateway_details', $gateway_details )->bind( 'grpahstartdate', $startdate )->bind( 'transaction_type', $list )->bind( 'id', $id );
        $this->page_title           = $page_title;
        $this->template->title      = $page_title . " | " . SITENAME;
        $this->template->page_title = $page_title;
        $this->template->content    = $view;
    }
    /**
     ****Transaction List****
     * function to get overall reaject/cancel trip list
     */
    public function action_adminrejcancel()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $manage_transaction     = Model::factory( 'transaction' );
        $get_allcompany         = $manage_transaction->get_allcompany_tranaction( $usertype );
        $taxilist               = $manage_transaction->gettaxidetails( '', '' );
        $passengerlist          = $manage_transaction->getpassengerdetails( '', '' );
        $driverlist             = $manage_transaction->getdriverdetails( '', '' );
        $managerlist            = $manage_transaction->getmanagerdetails( '' );
        $count_transaction_list = $manage_transaction->count_rejcancel_list( 'All', 'All', 'All', 'All', 'All', '', '' );
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
        $all_transaction_list       = $manage_transaction->rejcancel_details( 'All', 'All', 'All', 'All', 'All', '', '', $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/report/adminrejcancel' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'taxilist', $taxilist )->bind( 'driverlist', $driverlist )->bind( 'managerlist', $managerlist )->bind( 'passengerlist', $passengerlist )->bind( 'get_allcompany', $get_allcompany )->bind( 'id', $id );
        $this->page_title           = __( 'cancelledtrip_logs' );
        $this->template->title      = SITENAME . " | " . __( 'cancelledtrip_logs' );
        $this->template->page_title = __( 'cancelledtrip_logs' );
        $this->template->content    = $view;
    }
    public function action_adminrejcancel_list()
    {
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $company                = trim( Html::chars( $_REQUEST['filter_company'] ) );
        $startdate              = trim( Html::chars( $_REQUEST['startdate'] ) );
        $enddate                = trim( Html::chars( $_REQUEST['enddate'] ) );
        $taxiid                 = trim( Html::chars( $_REQUEST['taxiid'] ) );
        $driver_id              = trim( Html::chars( $_REQUEST['driver_id'] ) );
        $manager_id             = trim( Html::chars( $_REQUEST['manager_id'] ) );
        $passengerid            = trim( Html::chars( $_REQUEST['passengerid'] ) );
        $manage_transaction     = Model::factory( 'transaction' );
        $get_allcompany         = $manage_transaction->get_allcompany_tranaction();
        $taxilist               = $manage_transaction->gettaxidetails( $company, $manager_id );
        $passengerlist          = $manage_transaction->getpassengerdetails( $company, $manager_id );
        $driverlist             = $manage_transaction->getdriverdetails( $company, $manager_id );
        $managerlist            = $manage_transaction->getmanagerdetails( $company );
        $count_transaction_list = $manage_transaction->count_rejcancel_list( $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate );
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
        $all_transaction_list       = $manage_transaction->rejcancel_details( $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/report/adminrejcancel' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'taxilist', $taxilist )->bind( 'driverlist', $driverlist )->bind( 'managerlist', $managerlist )->bind( 'passengerlist', $passengerlist )->bind( 'get_allcompany', $get_allcompany )->bind( 'id', $id );
        $this->page_title           = __( 'cancelledtrip_logs' );
        $this->template->title      = SITENAME . " | " . __( 'cancelledtrip_logs' );
        $this->template->page_title = __( 'cancelledtrip_logs' );
        $this->template->content    = $view;
    }
    //Company Transactions without Search action 
    public function action_companytransaction()
    {
        $find_url       = explode( '/', $_SERVER['REQUEST_URI'] );
        $split          = explode( '?', $find_url[3] );
        $list           = $split[0];
        $passenger_ids  = $cpassenger_id = '';
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'A' ) {
            $split         = explode( '/', $_SERVER['REQUEST_URI'] );
            $get_companyid = explode( '?', $split[3] );
            $company_id    = $get_companyid[0];
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $manage_transaction = Model::factory( 'transaction' );
        $common_model       = Model::factory( 'commonmodel' );
        if ( $usertype != 'A' ) {
            $company_id = $_SESSION['company_id'];
        }
        if ( $list == 'all' ) {
            $page_title = __( "all_transaction_log" );
        } elseif ( $list == 'success' ) {
            $page_title = __( "success_transaction_log" );
        } elseif ( $list == 'cancelled' ) {
            $page_title = __( "cancelled_transaction_log" );
        } elseif ( $list == 'rejected' ) {
            $page_title = __( "rejected_trip_log" );
        } else {
            $page_title = __( "all_transaction_log" );
            $list       = 'all';
        }
        $taxilist    = $manage_transaction->gettaxidetails( $company_id, '', array ());
        $companyTime = '';
        if ( !empty( $taxilist ) ) {
            $companyTime = convert_timezone( 'now', $taxilist[0]['time_zone'] );
        }
        $passengerlist = $manage_transaction->getpassengerdetails( $company_id, '', array ());
        if ( count( $passengerlist ) > 0 ) {
            foreach ( $passengerlist as $passengers ) {
                $cpassenger_id .= $passengers["id"] . ',';
            }
            $passenger_ids = substr( $cpassenger_id, 0, strlen( $cpassenger_id ) - 1 );
        }
        $driverlist  = $manage_transaction->getdriverdetails( $company_id, '', array ());
        $managerlist = $manage_transaction->getmanagerdetails( $company_id );
        if ( $companyTime == '' ) {
            $companyTime = $common_model->getcompany_all_currenttimestamp( $company_id ); //to get current time for a particular company
        }
        $startdate              = Commonfunction::ensureDatabaseFormat( date( 'Y-m-01 00:00:00' ), 1 );
        $enddate                = Commonfunction::ensureDatabaseFormat( $companyTime, 2 );
        $count_transaction_list = $manage_transaction->count_admintransaction_list( $list, $company_id, 'All', 'All', 'All', 'All', $startdate, $enddate, '', '', '', $passenger_ids, array(), '', '' );
        $grpahdata              = $manage_transaction->getgraphvalues( $list, $company_id, 'All', 'All', 'All', 'All', $startdate, $enddate, '', '', '', $passenger_ids, array(), '', '' );
        $gateway_details        = $common_model->gateway_details();
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
        $all_transaction_list       = $manage_transaction->transaction_details( $list, $company_id, 'All', 'All', 'All', 'All', $startdate, $enddate, $offset, REC_PER_PAGE, '', '', '', $passenger_ids, array(), '', '' );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/report/companytransaction' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'count_transaction_list', $count_transaction_list )->bind( 'taxilist', $taxilist )->bind( 'driverlist', $driverlist )->bind( 'managerlist', $managerlist )->bind( 'grpahdata', $grpahdata )->bind( 'passengerlist', $passengerlist )->bind( 'get_allcompany', $get_allcompany )->bind( 'gateway_details', $gateway_details )->bind( 'enddate', $enddate )->bind( 'grpahstartdate', $startdate )->bind( 'id', $id );
        $this->page_title           = $page_title;
        $this->template->title      = $page_title . " | " . SITENAME;
        $this->template->page_title = $page_title;
        $this->template->content    = $view;
    }
    public function action_companytransaction_list()
    {
        $find_url       = explode( '/', $_SERVER['REQUEST_URI'] );
        $split          = explode( '?', $find_url[3] );
        $list           = $split[0];
        $companyTime    = $passenger_ids = $cpassenger_id = '';
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'A' ) {
            $split         = explode( '/', $_SERVER['REQUEST_URI'] );
            $get_companyid = explode( '?', $split[3] );
            $company_id    = $get_companyid[0];
        }
        if ( $usertype == 'M' ) {
            $this->request->redirect( "manager/login" );
        }
        $list = ( isset( $_REQUEST['travelSts'] ) ) ? $_REQUEST['travelSts'] : $list;
        if ( $list == 'all' ) {
            $page_title = __( "all_transaction_log" );
        } elseif ( $list == 'success' ) {
            $page_title = __( "success_transaction_log" );
        } elseif ( $list == 'cancelled' ) {
            $page_title = __( "cancelled_transaction_log" );
        } elseif ( $list == 'rejected' ) {
            $page_title = __( "rejected_trip_log" );
        } else {
            $page_title = __( "all_transaction_log" );
            $list       = 'all';
        }
        $startdate          = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['startdate'] ) ), 1 );
        $enddate            = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['enddate'] ) ), 2 );
        $taxiid             = trim( Html::chars( $_REQUEST['taxiid'] ) );
        $driver_id          = trim( Html::chars( $_REQUEST['driver_id'] ) );
        $manager_id         = trim( Html::chars( $_REQUEST['manager_id'] ) );
        $passengerid        = trim( Html::chars( $_REQUEST['passengerid'] ) );
        $transaction_id     = trim( Html::chars( $_REQUEST['transaction_id'] ) );
        $payment_type       = trim( Html::chars( $_REQUEST['payment_type'] ) );
        $manage_transaction = Model::factory( 'transaction' );
        $common_model       = Model::factory( 'commonmodel' );
        if ( $usertype != 'A' ) {
            $company_id = $_SESSION['company_id'];
        }
        $manager_details_array = array();
        if ( ( $manager_id != "" ) && ( $manager_id != "All" ) || ( $usertype == 'M' ) ) {
            $manager_details = $manage_transaction->manager_details( $manager_id );
            if ( count( $manager_details ) > 0 ) {
                $manager_details_array = $manager_details;
            }
        }
        $taxilist = $manage_transaction->gettaxidetails( $company_id, $manager_id, $manager_details_array );
        if ( !empty( $taxilist ) ) {
            $companyTime = convert_timezone( 'now', $taxilist[0]['time_zone'] );
        }
        $passengerlist = $manage_transaction->getpassengerdetails( $company_id, $manager_id, $manager_details_array );
        if ( count( $passengerlist ) > 0 ) {
            foreach ( $passengerlist as $passengers ) {
                $cpassenger_id .= $passengers["id"] . ',';
            }
            $passenger_ids = substr( $cpassenger_id, 0, strlen( $cpassenger_id ) - 1 );
        }
        $driverlist  = $manage_transaction->getdriverdetails( $company_id, $manager_id, $manager_details_array );
        $managerlist = $manage_transaction->getmanagerdetails( $company_id );
        if ( $companyTime == '' ) {
            $companyTime = $common_model->getcompany_all_currenttimestamp( $company_id ); //to get current time for a particular company
        }
        $taxi_ids = $driver_ids = "";
        if ( ( $manager_id != "" ) && ( $manager_id != "All" ) || ( $usertype == 'M' ) ) {
            if ( count( $taxilist ) > 0 ) {
                foreach ( $taxilist as $taxis ) {
                    $taxi_ids .= $taxis["taxi_id"] . ',';
                }
                $taxi_ids = substr( $taxi_ids, 0, strlen( $taxi_ids ) - 1 );
            }
            if ( count( $driverlist ) > 0 ) {
                foreach ( $driverlist as $drivers ) {
                    $driver_ids .= $drivers["id"] . ',';
                }
                $driver_ids = substr( $driver_ids, 0, strlen( $driver_ids ) - 1 );
            }
        }
        $count_transaction_list = $manage_transaction->count_admintransaction_list( $list, $company_id, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id, $payment_type, '', $passenger_ids, $taxi_ids, $driver_ids );
        //echo $count_transaction_list;exit;
        $gateway_details        = $common_model->gateway_details();
        //pagination loads here
        $page_no                = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset               = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data             = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_transaction_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_transaction_list = $manage_transaction->transaction_details( $list, $company_id, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $offset, REC_PER_PAGE, $transaction_id, $payment_type, '', $passenger_ids, $taxi_ids, $driver_ids );
        //calculation to restrict 60 days report shown in graph
        $noDays               = round( ( strtotime( $enddate ) - strtotime( $startdate ) ) / ( 3600 * 24 ) );
        if ( $noDays > 60 ) {
            $enddate = date( "Y-m-d H:i:s", strtotime( "+60 day", strtotime( $startdate ) ) );
        }
        $grpahdata      = $manage_transaction->getgraphvalues( $list, $company_id, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id, $payment_type, '', $passenger_ids, $taxi_ids, $driver_ids );
        $graphStartDate = Commonfunction::getDateTimeFormat( $startdate, 1 );
        $graphEndDate   = Commonfunction::getDateTimeFormat( $enddate, 1 );
        //****pagination ends here***//
        if ( isset( $_SESSION['download_set'] ) ) {
            $all_transaction_list = $manage_transaction->export_transaction_details( $list, $company_id, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $offset, REC_PER_PAGE, $transaction_id, $payment_type, '', $passenger_ids, $taxi_ids, $driver_ids );
            $all_data             = $all_transaction_list;
            $ccur                 = CURRENCY_FORMAT;
            if ( count( $all_data ) > 0 ) {
                foreach ( $all_data as $key => $value ) {
                    if ( array_key_exists( 'payment_type', $value ) ) {
                        if ( $value['payment_type'] == 2 ) {
                            $payment_type = "Credit Card";
                        } else if ( $value['payment_type'] == 3 ) {
                            $payment_type = __( 'new_credit_card' );
                        } else if ( $value['payment_type'] == 4 ) {
                            $payment_type = __( 'account' );
                        } else {
                            $payment_type = "Cash";
                        }
                        unset( $value['payment_type'] );
                        $all_data[$key]['payment_type'] = $payment_type;
                    }
                    if ( $value['actual_pickup_time'] != '0000-00-00 00:00:00' && $value['actual_pickup_time'] != '' && isset( $value['actual_pickup_time'] ) ) {
                        $journeyDate = Commonfunction::getDateTimeFormat( $value['actual_pickup_time'], 1 );
                    } else {
                        $journeyDate = Commonfunction::getDateTimeFormat( $value['pickup_time'], 1 );
                    }
                    unset( $value['journey_date'] );
                    $all_data[$key]['journey_date'] = $journeyDate;
                    $waitingTime                    = '-';
                    if ( !empty( $value['waiting_time'] ) ) {
                        $waitingTimeArr    = explode( " ", $value['waiting_time'] );
                        $waitingTimeFormat = explode( ":", $waitingTimeArr[0] );
                        $waitingTime       = ( !isset( $waitingTimeFormat[2] ) ) ? '00:' . $waitingTimeArr[0] : $waitingTimeArr[0];
                    }
                    unset( $value['waiting_time'] );
                    $all_data[$key]['waiting_time'] = $waitingTime;
                    if ( !empty( $value['distance'] ) != 0 ) {
                        $distance = round( $value['distance'], 2 );
                    } else {
                        $distance = '-';
                    }
                    unset( $value['distance'] );
                    $all_data[$key]['distance'] = $distance;
                    $admin_amount               = ( !empty( $value['admin_amount'] ) ) ? $ccur . ' ' . $value['admin_amount'] : '-';
                    unset( $value['admin_amount'] );
                    $all_data[$key]['admin_amount'] = $admin_amount;
                    $company_amount                 = ( !empty( $value['company_amount'] ) ) ? $ccur . ' ' . $value['company_amount'] : '-';
                    unset( $value['company_amount'] );
                    $all_data[$key]['company_amount'] = $company_amount;
                    $nightfare                        = ( !empty( $value['nightfare'] ) ) ? $ccur . ' ' . $value['nightfare'] : '-';
                    unset( $value['nightfare'] );
                    $all_data[$key]['nightfare'] = $nightfare;
                    $eveningfare                 = ( !empty( $value['eveningfare'] ) ) ? $ccur . ' ' . $value['eveningfare'] : '-';
                    unset( $value['eveningfare'] );
                    $all_data[$key]['eveningfare'] = $eveningfare;
                    $fare                          = ( !empty( $value['fare'] ) ) ? $ccur . ' ' . round( $value['fare'], 2 ) : '-';
                    unset( $value['fare'] );
                    $all_data[$key]['fare'] = $fare;
                }
            }
            $export_table_header = array(
                 __( 'trip_id' ),
                __( 'passenger_name' ),
                __( 'driver_name' ),
                __( 'journey_date' ),
                __( 'passenger_email' ),
                __( 'Current_Location' ),
                __( 'Drop_Location' ),
                __( 'companyname' ) 
            );
            if ( $list != 'rejected' ) {
                $export_table_header[] = __( 'cctransaction_id' );
                $export_table_header[] = __( 'payment_type' );
                $export_table_header[] = __( 'admin_commision' );
                $export_table_header[] = __( 'company_commision' );
            }
            if ( $list != 'rejected' && $list != 'cancelled' ) {
                $export_table_header[] = __( 'waiting_time_with_format' );
                $export_table_header[] = __( 'distance_km' );
                $export_table_header[] = __( 'trip_total_fare' );
                $export_table_header[] = __( 'nightfare' );
                $export_table_header[] = __( 'eveningfare' );
            } elseif ( $list == 'cancelled' ) {
                $export_table_header[] = __( 'cancel_fare' );
            }
            $export_table_field_select = array(
                 'passengers_log_id',
                'passenger_name',
                'driver_name',
                'journey_date',
                'passenger_email',
                'current_location',
                'drop_location',
                'company_name' 
            );
            if ( $list != 'rejected' ) {
                $export_table_field_select[] = 'transaction_id';
                $export_table_field_select[] = 'payment_type';
                $export_table_field_select[] = 'admin_amount';
                $export_table_field_select[] = 'company_amount';
            }
            if ( $list != 'rejected' && $list != 'cancelled' ) {
                $export_table_field_select[] = 'waiting_time';
                $export_table_field_select[] = 'distance';
                $export_table_field_select[] = 'fare';
                $export_table_field_select[] = 'nightfare';
                $export_table_field_select[] = 'eveningfare';
            } elseif ( $list == 'cancelled' ) {
                $export_table_field_select[] = 'fare';
            }
            $heading = "Export";
            $this->action_create_the_document( $all_data, $export_table_header, $export_table_field_select, $heading );
        }
        //send data to view file 
        $view                       = View::factory( 'admin/report/companytransaction' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'count_transaction_list', $count_transaction_list )->bind( 'taxilist', $taxilist )->bind( 'driverlist', $driverlist )->bind( 'managerlist', $managerlist )->bind( 'grpahdata', $grpahdata )->bind( 'graphStartDate', $graphStartDate )->bind( 'graphEndDate', $graphEndDate )->bind( 'passengerlist', $passengerlist )->bind( 'get_allcompany', $get_allcompany )->bind( 'gateway_details', $gateway_details )->bind( 'grpahstartdate', $startdate )->bind( 'id', $id );
        $this->page_title           = $page_title;
        $this->template->title      = $page_title . " | " . SITENAME;
        $this->template->page_title = $page_title;
        $this->template->content    = $view;
    }
    //Manager Transactions without Search action 
    public function action_managertransaction()
    {
        $find_url       = explode( '/', $_SERVER['REQUEST_URI'] );
        $split          = explode( '?', $find_url[3] );
        $list           = $split[0];
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        $company_id     = $_SESSION['company_id'];
        if ( $company_id ) {
            $dashboard        = Model::factory( 'manager' );
            $company_timezone = $dashboard->get_company_timezone( $company_id );
        }
        if ( $usertype == 'A' ) {
            $this->request->redirect( "admin/login" );
        }
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        $manage_transaction    = Model::factory( 'transaction' );
        $common_model          = Model::factory( 'commonmodel' );
        $manager_details_array = array();
        if ( $usertype == 'M' ) {
            $manager_details = $manage_transaction->manager_details( $_SESSION['userid'] );
            if ( count( $manager_details ) > 0 ) {
                $manager_details_array = $manager_details;
            }
        }
        if ( $list == 'all' ) {
            $page_title = __( "all_transaction_log" );
        } elseif ( $list == 'success' ) {
            $page_title = __( "success_transaction_log" );
        } elseif ( $list == 'cancelled' ) {
            $page_title = __( "cancelled_transaction_log" );
        } elseif ( $list == 'rejected' ) {
            $page_title = __( "rejected_trip_log" );
        } else {
            $page_title = __( "all_transaction_log" );
            $list       = 'all';
        }
        $company_id             = $_SESSION['company_id'];
        $get_allcompany         = $manage_transaction->get_allcompany_tranaction();
        $taxilist               = $manage_transaction->gettaxidetails( $company_id, $user_createdby, $manager_details_array );
        $driverlist             = $manage_transaction->getdriverdetails( $company_id, $user_createdby, $manager_details_array );
        $passengerlist          = $manage_transaction->getpassengerdetails( $company_id, $user_createdby, $manager_details_array );
        $companyTime            = $common_model->getcompany_all_currenttimestamp( $company_id ); //to get current time for a particular company
        $startdate              = Commonfunction::ensureDatabaseFormat( date( 'Y-m-01 00:00:00' ), 1 );
        $enddate                = Commonfunction::ensureDatabaseFormat( $companyTime, 2 );
        $count_transaction_list = $manage_transaction->count_admintransaction_list( $list, $company_id, $user_createdby, 'All', 'All', 'All', $startdate, $enddate, '', '' );
        $grpahdata              = $manage_transaction->getgraphvalues( $list, $company_id, $user_createdby, 'All', 'All', 'All', $startdate, $enddate, '', '' );
        $gateway_details        = $common_model->gateway_details();
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
        $all_transaction_list       = $manage_transaction->transaction_details( $list, $company_id, $user_createdby, 'All', 'All', 'All', $startdate, $enddate, $offset, REC_PER_PAGE, '', '' );
        //****pagination ends here***//
        //send data to view file 
        $view                       = View::factory( 'admin/report/managertransaction' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'count_transaction_list', $count_transaction_list )->bind( 'taxilist', $taxilist )->bind( 'driverlist', $driverlist )->bind( 'grpahdata', $grpahdata )->bind( 'passengerlist', $passengerlist )->bind( 'get_allcompany', $get_allcompany )->bind( 'gateway_details', $gateway_details )->bind( 'enddate', $enddate )->bind( 'grpahstartdate', $startdate )->bind( 'id', $id );
        $this->page_title           = $page_title;
        $this->template->title      = $page_title . " | " . SITENAME;
        $this->template->page_title = $page_title;
        $this->template->content    = $view;
    }
    public function action_managertransaction_list()
    {
        $find_url       = explode( '/', $_SERVER['REQUEST_URI'] );
        $split          = explode( '?', $find_url[3] );
        $list           = $split[0];
        $user_createdby = $_SESSION['userid'];
        $usertype       = $_SESSION['user_type'];
        if ( $usertype == 'A' ) {
            $this->request->redirect( "admin/login" );
        }
        if ( $usertype == 'C' ) {
            $this->request->redirect( "company/login" );
        }
        $list = ( isset( $_REQUEST['travelSts'] ) ) ? $_REQUEST['travelSts'] : $list;
        if ( $list == 'all' ) {
            $page_title = __( "all_transaction_log" );
        } elseif ( $list == 'success' ) {
            $page_title = __( "success_transaction_log" );
        } elseif ( $list == 'cancelled' ) {
            $page_title = __( "cancelled_transaction_log" );
        } elseif ( $list == 'rejected' ) {
            $page_title = __( "rejected_trip_log" );
        } else {
            $page_title = __( "all_transaction_log" );
            $list       = 'all';
        }
        $startdate   = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['startdate'] ) ), 1 );
        $enddate     = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['enddate'] ) ), 2 );
        $taxiid      = trim( Html::chars( $_REQUEST['taxiid'] ) );
        $driver_id   = trim( Html::chars( $_REQUEST['driver_id'] ) );
        $passengerid = trim( Html::chars( $_REQUEST['passengerid'] ) );
        if ( isset( $_REQUEST['transaction_id'] ) ) {
            $transaction_id = trim( Html::chars( $_REQUEST['transaction_id'] ) );
        } else {
            $transaction_id = "";
        }
        if ( isset( $_REQUEST['payment_type'] ) ) {
            $payment_type = trim( Html::chars( $_REQUEST['payment_type'] ) );
        } else {
            $payment_type = "";
        }
        $manage_transaction    = Model::factory( 'transaction' );
        $common_model          = Model::factory( 'commonmodel' );
        $manager_details_array = array();
        if ( $usertype == 'M' ) {
            $manager_details = $manage_transaction->manager_details( $_SESSION['userid'] );
            if ( count( $manager_details ) > 0 ) {
                $manager_details_array = $manager_details;
            }
        }
        $company_id     = $_SESSION['company_id'];
        $get_allcompany = $manage_transaction->get_allcompany_tranaction();
        $taxilist       = $manage_transaction->gettaxidetails( $company_id, $user_createdby, $manager_details_array );
        $passengerlist  = $manage_transaction->getpassengerdetails( $company_id, $user_createdby, $manager_details_array );
        $driverlist     = $manage_transaction->getdriverdetails( $company_id, $user_createdby, $manager_details_array );
        $taxi_ids       = $driver_ids = "";
        if ( $usertype == 'M' ) {
            if ( count( $taxilist ) > 0 ) {
                foreach ( $taxilist as $taxis ) {
                    $taxi_ids .= $taxis["taxi_id"] . ',';
                }
                $taxi_ids = substr( $taxi_ids, 0, strlen( $taxi_ids ) - 1 );
            }
            if ( count( $driverlist ) > 0 ) {
                foreach ( $driverlist as $drivers ) {
                    $driver_ids .= $drivers["id"] . ',';
                }
                $driver_ids = substr( $driver_ids, 0, strlen( $driver_ids ) - 1 );
            }
        }
        $count_transaction_list = $manage_transaction->count_admintransaction_list( $list, $company_id, $user_createdby, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id, $payment_type, '', '', $taxi_ids, $driver_ids );
        $gateway_details        = $common_model->gateway_details();
        //pagination loads here
        $page_no                = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset               = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data             = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_transaction_list,
            'view' => 'pagination/punbb' 
        ) );
        $all_transaction_list = $manage_transaction->transaction_details( $list, $company_id, $user_createdby, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $offset, REC_PER_PAGE, $transaction_id, $payment_type, '', '', $taxi_ids, $driver_ids );
        //****pagination ends here***//
        //calculation to restrict 60 days report shown in graph
        $noDays               = round( ( strtotime( $enddate ) - strtotime( $startdate ) ) / ( 3600 * 24 ) );
        if ( $noDays > 60 ) {
            $enddate = date( "Y-m-d H:i:s", strtotime( "+60 day", strtotime( $startdate ) ) );
        }
        $grpahdata      = $manage_transaction->getgraphvalues( $list, $company_id, $user_createdby, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id, $payment_type, '', '', $taxi_ids, $driver_ids );
        $graphStartDate = Commonfunction::getDateTimeFormat( $startdate, 1 );
        $graphEndDate   = Commonfunction::getDateTimeFormat( $enddate, 1 );
        //send data to view file 
        if ( isset( $_SESSION['download_set'] ) ) {
            $all_transaction_list = $manage_transaction->export_transaction_details( $list, $company_id, $user_createdby, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $offset, REC_PER_PAGE, $transaction_id, $payment_type, '', '', $taxi_ids, $driver_ids );
            $all_data             = $all_transaction_list;
            $ccur                 = CURRENCY_FORMAT;
            if ( count( $all_data ) > 0 ) {
                foreach ( $all_data as $key => $value ) {
                    if ( array_key_exists( 'payment_type', $value ) ) {
                        if ( $value['payment_type'] == 2 ) {
                            $payment_type = "Credit Card";
                        } else if ( $value['payment_type'] == 3 ) {
                            $payment_type = __( 'new_credit_card' );
                        } else if ( $value['payment_type'] == 4 ) {
                            $payment_type = __( 'account' );
                        } else {
                            $payment_type = "Cash";
                        }
                        unset( $value['payment_type'] );
                        $all_data[$key]['payment_type'] = $payment_type;
                    }
                    if ( $value['actual_pickup_time'] != '0000-00-00 00:00:00' && $value['actual_pickup_time'] != '' && isset( $value['actual_pickup_time'] ) ) {
                        $journeyDate = Commonfunction::getDateTimeFormat( $value['actual_pickup_time'], 1 );
                    } else {
                        $journeyDate = Commonfunction::getDateTimeFormat( $value['pickup_time'], 1 );
                    }
                    unset( $value['journey_date'] );
                    $all_data[$key]['journey_date'] = $journeyDate;
                    $waitingTime                    = '-';
                    if ( !empty( $value['waiting_time'] ) ) {
                        $waitingTimeArr    = explode( " ", $value['waiting_time'] );
                        $waitingTimeFormat = explode( ":", $waitingTimeArr[0] );
                        $waitingTime       = ( !isset( $waitingTimeFormat[2] ) ) ? '00:' . $waitingTimeArr[0] : $waitingTimeArr[0];
                    }
                    unset( $value['waiting_time'] );
                    $all_data[$key]['waiting_time'] = $waitingTime;
                    if ( !empty( $value['distance'] ) ) {
                        $distance = round( $value['distance'], 2 );
                    } else {
                        $distance = '-';
                    }
                    unset( $value['distance'] );
                    $all_data[$key]['distance'] = $distance;
                    $admin_amount               = ( !empty( $value['admin_amount'] ) ) ? $ccur . ' ' . $value['admin_amount'] : '-';
                    unset( $value['admin_amount'] );
                    $all_data[$key]['admin_amount'] = $admin_amount;
                    $company_amount                 = ( !empty( $value['company_amount'] ) ) ? $ccur . ' ' . $value['company_amount'] : '-';
                    unset( $value['company_amount'] );
                    $all_data[$key]['company_amount'] = $company_amount;
                    $nightfare                        = ( !empty( $value['nightfare'] ) ) ? $ccur . ' ' . $value['nightfare'] : '-';
                    unset( $value['nightfare'] );
                    $all_data[$key]['nightfare'] = $nightfare;
                    $eveningfare                 = ( !empty( $value['eveningfare'] ) ) ? $ccur . ' ' . $value['eveningfare'] : '-';
                    unset( $value['eveningfare'] );
                    $all_data[$key]['eveningfare'] = $eveningfare;
                    $fare                          = ( !empty( $value['fare'] ) ) ? $ccur . ' ' . round( $value['fare'], 2 ) : '-';
                    unset( $value['fare'] );
                    $all_data[$key]['fare'] = $fare;
                }
            }
            $export_table_header = array(
                 __( 'trip_id' ),
                __( 'passenger_name' ),
                __( 'driver_name' ),
                __( 'journey_date' ),
                __( 'passenger_email' ),
                __( 'Current_Location' ),
                __( 'Drop_Location' ),
                __( 'companyname' ) 
            );
            if ( $list != 'rejected' ) {
                $export_table_header[] = __( 'cctransaction_id' );
                $export_table_header[] = __( 'payment_type' );
                $export_table_header[] = __( 'admin_commision' );
                $export_table_header[] = __( 'company_commision' );
            }
            if ( $list != 'rejected' && $list != 'cancelled' ) {
                $export_table_header[] = __( 'waiting_time_with_format' );
                $export_table_header[] = __( 'distance_km' );
                $export_table_header[] = __( 'trip_total_fare' );
                $export_table_header[] = __( 'nightfare' );
                $export_table_header[] = __( 'eveningfare' );
            } elseif ( $list == 'cancelled' ) {
                $export_table_header[] = __( 'cancel_fare' );
            }
            $export_table_field_select = array(
                 'passengers_log_id',
                'passenger_name',
                'driver_name',
                'journey_date',
                'passenger_email',
                'current_location',
                'drop_location',
                'company_name' 
            );
            if ( $list != 'rejected' ) {
                $export_table_field_select[] = 'transaction_id';
                $export_table_field_select[] = 'payment_type';
                $export_table_field_select[] = 'admin_amount';
                $export_table_field_select[] = 'company_amount';
            }
            if ( $list != 'rejected' && $list != 'cancelled' ) {
                $export_table_field_select[] = 'waiting_time';
                $export_table_field_select[] = 'distance';
                $export_table_field_select[] = 'fare';
                $export_table_field_select[] = 'nightfare';
                $export_table_field_select[] = 'eveningfare';
            } elseif ( $list == 'cancelled' ) {
                $export_table_field_select[] = 'fare';
            }
            $heading = "Export";
            $this->action_create_the_document( $all_data, $export_table_header, $export_table_field_select, $heading );
        }
        $view                       = View::factory( 'admin/report/managertransaction' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'srch', $_REQUEST )->bind( 'pag_data', $pag_data )->bind( 'all_transaction_list', $all_transaction_list )->bind( 'count_transaction_list', $count_transaction_list )->bind( 'taxilist', $taxilist )->bind( 'driverlist', $driverlist )->bind( 'grpahdata', $grpahdata )->bind( 'graphStartDate', $graphStartDate )->bind( 'graphEndDate', $graphEndDate )->bind( 'passengerlist', $passengerlist )->bind( 'get_allcompany', $get_allcompany )->bind( 'gateway_details', $gateway_details )->bind( 'grpahstartdate', $startdate )->bind( 'id', $id );
        $this->page_title           = __( 'transaction_list' );
        $this->template->title      = SITENAME . " | " . __( 'transaction_list' );
        $this->template->page_title = __( 'transaction_list' );
        $this->template->content    = $view;
    }
    //Function used to get company taxi by company id
    public function action_gettaxilist()
    {
        $add_model          = Model::factory( 'transaction' );
        $output             = '';
        $company_id         = arr::get( $_REQUEST, 'company_id' );
        $manage_transaction = Model::factory( 'transaction' );
        $gettaxi_details    = $manage_transaction->gettaxidetails( $company_id, '' );
        if ( count( $gettaxi_details ) > 0 ) {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                            <select name="taxiid" id="taxiid" class="select2">
                           <option value="All">' . __( 'all_label' ) . '</option>';
            foreach ( $gettaxi_details as $modellist ) {
                $output .= '<option value="' . $modellist["taxi_id"] . '"';
                $output .= '>' . $modellist["taxi_no"] . '</option>';
            }
            $output .= '</select></div>';
        } else {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                    <select name="taxiid" id="taxiid">
                       <option value="">' . __( 'select_label' ) . '</option></select></div>';
        }
        echo $output;
        exit;
    }
    //Function used to get company driver by company id
    public function action_getdriverlist()
    {
        $add_model          = Model::factory( 'transaction' );
        $output             = '';
        $company_id         = arr::get( $_REQUEST, 'company_id' );
        $manage_transaction = Model::factory( 'transaction' );
        $getdriver_details  = $manage_transaction->getdriverdetails( $company_id, '' );
        if ( count( $getdriver_details ) > 0 ) {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                            <select name="driver_id" id="driver_id" class="select2">
                           <option value="All">' . __( 'all_label' ) . '</option>';
            foreach ( $getdriver_details as $modellist ) {
                $drivername = $modellist["name"] . ' ' . $modellist["lastname"];
                $output .= '<option value="' . $modellist["id"] . '"';
                $output .= '>' . ucfirst( $drivername ) . '</option>';
            }
            $output .= '</select></div>';
        } else {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                    <select name="driver_id" id="driver_id">
                        <option value="">' . __( 'select_label' ) . '</option></select></div>';
        }
        echo $output;
        exit;
    }
    //Function used to get company passenger by company id
    public function action_getpassengerlist()
    {
        $add_model            = Model::factory( 'transaction' );
        $output               = '';
        $company_id           = arr::get( $_REQUEST, 'company_id' );
        $manage_transaction   = Model::factory( 'transaction' );
        $getpassenger_details = $manage_transaction->getpassengerdetails( $company_id, '' );
        if ( count( $getpassenger_details ) > 0 ) {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                            <select name="passengerid" id="passengerid" class="select2">
                           <option value="All">' . __( 'all_label' ) . '</option>';
            foreach ( $getpassenger_details as $modellist ) {
                $passengername = ucfirst( $modellist["name"] );
                $output .= '<option value="' . $modellist["id"] . '"';
                $output .= '>' . $passengername . '</option>';
            }
            $output .= '</select></div>';
        } else {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                    <select name="passengerid" id="passengerid">
                        <option value="">' . __( 'select_label' ) . '</option></select></div>';
        }
        echo $output;
        exit;
    }
    //Function used to get company Manager by company id
    public function action_getmanagerlist()
    {
        $add_model          = Model::factory( 'transaction' );
        $output             = '';
        $company_id         = arr::get( $_REQUEST, 'company_id' );
        $manage_transaction = Model::factory( 'transaction' );
        $getmanager_details = $manage_transaction->getmanagerdetails( $company_id );
        if ( count( $getmanager_details ) > 0 ) {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type" onchange="getmanagertaxi(this.value),getmanagerdriver(this.value),getcompanypassengers(filter_company.value)">
                            <select name="manager_id" id="manager_id">
                           <option value="All">' . __( 'all_label' ) . '</option>';
            foreach ( $getmanager_details as $modellist ) {
                $managername = $modellist["name"] . ' ' . $modellist["lastname"];
                $output .= '<option value="' . $modellist["id"] . '"';
                $output .= '>' . ucfirst( $managername ) . '</option>';
            }
            $output .= '</select></div>';
        } else {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                    <select name="manager_id" id="manager_id">
                        <option value="">' . __( 'select_label' ) . '</option></select></div>';
        }
        echo $output;
        exit;
    }
    //Function used to get manger taxi by company id
    public function action_getmanager_taxilist()
    {
        $add_model          = Model::factory( 'transaction' );
        $output             = '';
        $company_id         = arr::get( $_REQUEST, 'company_id' );
        $manager_id         = arr::get( $_REQUEST, 'manager_id' );
        $manage_transaction = Model::factory( 'transaction' );
        $gettaxi_details    = $manage_transaction->getmanager_taxidetails( $company_id, $manager_id );
        if ( count( $gettaxi_details ) > 0 ) {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                            <select name="taxiid" id="taxiid" class="select2">
                           <option value="All">' . __( 'all_label' ) . '</option>';
            foreach ( $gettaxi_details as $modellist ) {
                $output .= '<option value="' . $modellist["taxi_id"] . '"';
                $output .= '>' . $modellist["taxi_no"] . '</option>';
            }
            $output .= '</select></div>';
        } else {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                    <select name="taxiid" id="taxiid">
                       <option value="">' . __( 'select_label' ) . '</option></select></div>';
        }
        echo $output;
        exit;
    }
    //Function used to get company driver by company id
    public function action_getmanager_driverlist()
    {
        $add_model          = Model::factory( 'transaction' );
        $output             = '';
        $company_id         = arr::get( $_REQUEST, 'company_id' );
        $manager_id         = arr::get( $_REQUEST, 'manager_id' );
        $manage_transaction = Model::factory( 'transaction' );
        $getdriver_details  = $manage_transaction->getmanager_driverdetails( $company_id, $manager_id );
        if ( count( $getdriver_details ) > 0 ) {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                            <select name="driver_id" id="driver_id" class="select2">
                           <option value="All">' . __( 'all_label' ) . '</option>';
            foreach ( $getdriver_details as $modellist ) {
                $drivername = $modellist["name"] . ' ' . $modellist["lastname"];
                $output .= '<option value="' . $modellist["id"] . '"';
                $output .= '>' . ucfirst( $drivername ) . '</option>';
            }
            $output .= '</select></div>';
        } else {
            $output .= '<div class="selector ser_input_field" id="uniform-user_type">
                    <select name="driver_id" id="driver_id">
                        <option value="">' . __( 'select_label' ) . '</option></select></div>';
        }
        echo $output;
        exit;
    }
    /**
     * ****action_export()****
     * @param 
     * @return functionality for csv export
     */
    public function action_export()
    {
        $company           = trim( Html::chars( $_REQUEST['filter_company'] ) );
        $startdate         = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['startdate'] ) ), 1 );
        $enddate           = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['enddate'] ) ), 2 );
        $taxiid            = trim( Html::chars( $_REQUEST['taxiid'] ) );
        $driver_id         = trim( Html::chars( $_REQUEST['driver_id'] ) );
        $manager_id        = trim( Html::chars( $_REQUEST['manager_id'] ) );
        $passengerid       = trim( Html::chars( $_REQUEST['passengerid'] ) );
        $transaction_id    = trim( Html::chars( $_REQUEST['transaction_id'] ) );
        $payment_type      = trim( Html::chars( $_REQUEST['payment_type'] ) );
        $payment_mode      = ( isset( $_REQUEST['payment_mode'] ) ) ? trim( Html::chars( $_REQUEST['payment_mode'] ) ) : '';
        $usertype          = $_SESSION['user_type'];
        //import admin model
        $transaction_model = Model::factory( 'transaction' );
        $find_url          = explode( '/', $_SERVER['REQUEST_URI'] );
        $split             = explode( '?', $find_url[3] );
        $list              = $split[0];
        if ( $list == '' ) {
            //$condition = "WHERE pl.driver_reply = 'A' ";
        } else if ( $list == 'success' ) {
            //$condition = "WHERE pl.travel_status = '1' and pl.driver_reply = 'A' ";
        } else if ( $list == 'cancelled' ) {
            //$condition = "WHERE pl.travel_status = '4' and pl.driver_reply = 'A' ";
        } else if ( $list == 'rejected' ) {
            //$condition = "WHERE pl.driver_reply != 'A'";
        } else if ( $list == 'pendingpayment' ) {
            //$condition = "WHERE pl.driver_reply != 'A'";
        } else {
            $company = $list;
            $list    = 'all';
        }
        $manager_details_array = array();
        if ( ( $manager_id != "" ) && ( $manager_id != "All" ) || ( $usertype == 'M' ) ) {
            $manager_id      = ( $manager_id ) ? $manager_id : $_SESSION['userid'];
            $manager_details = $transaction_model->manager_details( $manager_id );
            if ( count( $manager_details ) > 0 ) {
                $manager_details_array = $manager_details;
            }
        }
        //export csv data retrieved here
        $list = $transaction_model->export_data( $list, $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id, $payment_type, $payment_mode, $manager_details_array );
    }
    /**
     * ****action_export()****
     * @param 
     * @return functionality for csv export
     */
    public function action_exportpdf()
    {
        $company           = trim( Html::chars( $_REQUEST['filter_company'] ) );
        $startdate         = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['startdate'] ) ), 1 );
        $enddate           = Commonfunction::ensureDatabaseFormat( trim( Html::chars( $_REQUEST['enddate'] ) ), 2 );
        $taxiid            = trim( Html::chars( $_REQUEST['taxiid'] ) );
        $driver_id         = trim( Html::chars( $_REQUEST['driver_id'] ) );
        $manager_id        = trim( Html::chars( $_REQUEST['manager_id'] ) );
        $passengerid       = trim( Html::chars( $_REQUEST['passengerid'] ) );
        $transaction_id    = trim( Html::chars( $_REQUEST['transaction_id'] ) );
        $payment_type      = trim( Html::chars( $_REQUEST['payment_type'] ) );
        $payment_mode      = ( isset( $_REQUEST['payment_mode'] ) ) ? trim( Html::chars( $_REQUEST['payment_mode'] ) ) : '';
        $usertype          = $_SESSION['user_type'];
        //import admin model
        $transaction_model = Model::factory( 'transaction' );
        $manage            = Model::factory( 'manage' );
        $find_url          = explode( '/', $_SERVER['REQUEST_URI'] );
        $split             = explode( '?', $find_url[3] );
        $list              = $split[0];
        if ( $list == 'all' ) {
            //$condition = "WHERE pl.driver_reply = 'A' ";
        } else if ( $list == 'success' ) {
            //$condition = "WHERE pl.travel_status = '1' and pl.driver_reply = 'A' ";
        } else if ( $list == 'cancelled' ) {
            //$condition = "WHERE pl.travel_status = '4' and pl.driver_reply = 'A' ";
        } else if ( $list == 'rejected' ) {
            //$condition = "WHERE pl.driver_reply != 'A'";
        } else if ( $list == 'pendingpayment' ) {
            //$condition = "WHERE pl.driver_reply != 'A'";
        } else {
            $company = $list;
            $list    = 'all';
        }
        $manager_details_array = array();
        if ( ( $manager_id != "" ) && ( $manager_id != "All" ) || ( $usertype == 'M' ) ) {
            $manager_id      = ( $manager_id ) ? $manager_id : $_SESSION['userid'];
            $manager_details = $transaction_model->manager_details( $manager_id );
            if ( count( $manager_details ) > 0 ) {
                $manager_details_array = $manager_details;
            }
        }
        //export csv data retrieved here
        $list1     = $transaction_model->export_data_pdf( $list, $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id, $payment_type, $payment_mode, $manager_details_array );
        $headlable = __( 'report_head' );
        if ( $startdate != "" ) {
            $headlable = __( 'report_head' ) . ' ' . __( 'from' ) . ' ' . $startdate . ' ' . __( 'to' ) . ' ' . $enddate;
        } else {
            $headlable = __( 'report_head' );
        }
        $xls_output = '<style>
    h1 {
        color: navy;
        font-family: times;
        font-size: 24pt;
    }    
    td {
        font-weight:bold;
        font:bold 12pt arial; color:#000000;        
    }
    .tr_border{border-bottom:1px solid #2c2c2c;}
    .invoice_head{text-align: center;color:#000000;}
    .head_border{border-bottom:1px solid #2c2c2c;margin-top:5px;}
    .totalstyle{font-weight:bold; font:bold 12pt arial; color:#ffffff; background-color:#2c2c2c; text-align:right;  }
    </style>
    <table border="0" cellpadding="1" cellspacing="1">
     <tr>
       <td style="text-align:center;">' . $headlable . '</td>
     </tr>
       <tr>
      <td class="head_border">' . date( "F j, Y" ) . '</td>
     </tr>
     </table>
 <table border="0" cellpadding="0" cellspacing="0">';
        $xls_output .= "<tr>";
        if ( $list != 'rejected' ) {
            $xls_output .= '<td class="head_border">' . __( 'cctransaction_id' ) . '</td>';
            $xls_output .= '<td class="head_border">' . __( 'payment_type' ) . '</td>';
        }
        $xls_output .= '<td class="head_border">' . __( 'trip_id' ) . '</td>';
        $xls_output .= '<td class="head_border">' . __( 'passenger_name' ) . '</td>';
        $xls_output .= '<td class="head_border">' . ucfirst( __( 'driver_name' ) ) . '</td>';
        $xls_output .= '<td class="head_border">' . __( 'journey_date' ) . '</td>';
        $xls_output .= '<td class="head_border">' . __( 'Current_Location' ) . '</td>';
        $xls_output .= '<td class="head_border">' . __( 'Drop_Location' ) . '</td>';
        if ( $list != 'rejected' && $list != 'cancelled' ) {
            $xls_output .= '<td class="head_border">' . __( 'distance_km' ) . '</td>';
            $xls_output .= '<td class="head_border">' . __( 'fare' ) . '</td>';
            $xls_output .= '<td class="head_border">' . __( 'tax%' ) . '</td>';
            $xls_output .= '<td class="head_border">' . __( 'trip_total_fare' ) . '</td>';
        } elseif ( $list == 'cancelled' ) {
            $xls_output .= '<td class="head_border">' . __( 'fare' ) . '</td>';
            $xls_output .= '<td class="head_border">' . __( 'tax' ) . '</td>';
            $xls_output .= '<td class="head_border">' . __( 'cancel_fare' ) . '(' . CURRENCY . ')' . '</td>';
        } else {
            $xls_output .= '<td class="head_border">' . __( 'travel_status' ) . '</td>';
        }
        $xls_output .= "</tr>";
        $file       = 'Export';
        $total_fare = $tax_total = $percenttotal = $subtotal = "";
        foreach ( $list1 as $result ) {
            if ( $list != 'rejected' ) {
                $paymentMod = ( $result['payment_method'] == 'L' ) ? 'Live' : 'Test';
                if ( $result['distance'] == 0 ) {
                    $distance = '-';
                } else {
                    $distance = round( $result['distance'], 2 );
                }
                if ( $result['fare'] == 0 ) {
                    $fare = '-';
                } else {
                    $fare = round( $result['fare'], 2 );
                }
                if ( $result['comments'] == '' ) {
                    $comments = 'No Comments';
                } else {
                    $comments = $result['comments'];
                }
            } else {
                if ( $result['driver_reply'] == 'C' ) {
                    $driver_reply = __( 'cancelled_by_driver' );
                } else {
                    $driver_reply = __( 'rejected_by_driver' );
                }
                if ( $result['driver_comments'] == '' ) {
                    $driver_comments = '';
                } else {
                    $driver_comments = $result['driver_comments'];
                }
            }
            if ( $result['rating'] == 0 ) {
                $ratings = '-';
            } else {
                $ratings = $result['rating'];
            }
            if ( $list != 'rejected' ) {
                if ( $result['transaction_id'] == "" ) {
                    $trans_id = '-';
                } else {
                    $trans_id = ucfirst( $result['transaction_id'] );
                }
            }
            $xls_output .= "<tr>";
            if ( $list != 'rejected' ) {
                $xls_output .= '<td class="head_border">' . $trans_id . '</td>';
                if ( $result['payment_type'] == 2 ) {
                    $xls_output .= '<td class="head_border">Credit Card Using ' . $result['payment_gateway_name'] . ' ( ' . $paymentMod . ' )</td>';
                } else if ( $result['payment_type'] == 3 ) {
                    $xls_output .= '<td class="head_border">' . __( 'new_credit_card' ) . ' ( ' . $paymentMod . ' )</td>';
                } else if ( $result['payment_type'] == 4 ) {
                    $xls_output .= '<td class="head_border">' . __( 'account' ) . '</td>';
                } else {
                    $xls_output .= '<td class="head_border">Cash</td>';
                }
            }
            if ( $list != 'rejected' ) {
                $tripfare     = round( $result['fare'], 2 );
                $company_tax  = round( $result['company_tax'], 2 );
                $percentvalue = ( $company_tax / 100 ) * $tripfare;
                $fare         = round( $tripfare - $percentvalue, 2 );
            }
            $journeyDate = ( $result['actual_pickup_time'] != '0000-00-00 00:00:00' && $result['actual_pickup_time'] != '' && isset( $result['actual_pickup_time'] ) ) ? Commonfunction::getDateTimeFormat( $result['actual_pickup_time'], 1 ) : Commonfunction::getDateTimeFormat( $result['pickup_time'], 1 );
            $xls_output .= '<td class="head_border">' . $result['passengers_log_id'] . '</td>';
            $xls_output .= '<td class="head_border">' . ucfirst( $result['passenger_name'] ) . '</td>';
            $xls_output .= '<td class="head_border">' . wordwrap( ucfirst( $result['driver_name'] ), 30, '<br/>', 1 ) . '</td>';
            $xls_output .= '<td class="head_border">' . $journeyDate . '</td>';
            $xls_output .= '<td class="head_border">' . strip_tags( htmlentities( $result['current_location'] ) ) . '</td>';
            $xls_output .= '<td class="head_border">' . strip_tags( htmlentities( $result['drop_location'] ) ) . '</td>';
            if ( $list != 'rejected' && $list != 'cancelled' ) {
                $xls_output .= '<td class="head_border">' . $distance . '</td>';
                $xls_output .= '<td class="head_border">' . $fare . '</td>';
                $xls_output .= '<td class="head_border">' . $company_tax . '</td>';
                $company_currency = $result['company_id'];
                $ccur             = CURRENCY;
                $xls_output .= '<td class="head_border">' . $ccur . ' ' . $tripfare . '</td>';
                $ccur_for   = findcompany_currencyformat( $company_currency );
                $convet_amt = currency_conversion( $ccur_for, $tripfare );
            } elseif ( $list == 'cancelled' ) {
                $xls_output .= '<td class="head_border">' . $fare . '</td>';
                $xls_output .= '<td class="head_border">' . $company_tax . '</td>';
                $company_currency = $result['company_id'];
                $ccur             = CURRENCY;
                $xls_output .= '<td class="head_border">' . $ccur . ' ' . $tripfare . '</td>';
                $ccur_for   = findcompany_currencyformat( $company_currency );
                $convet_amt = currency_conversion( $ccur_for, $tripfare );
            } else {
                $xls_output .= '<td class="head_border">' . $driver_reply . '</td>';
            }
            $xls_output .= "</tr>";
            if ( $list != 'rejected' ) {
                $total_fare += $convet_amt;
                $tax_total += $company_tax;
                $percenttotal += $percentvalue;
                $subtotal += $convet_amt;
            }
        }
        $xls_output .= "</table>";
        $xls_output .= '<table border="0" cellpadding="1" cellspacing="1"><tr>';
        if ( $list != 'rejected' ) {
            $xls_output .= "<td></td>";
            $xls_output .= "<td></td>";
        }
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        $xls_output .= "</tr>";
        $xls_output .= "</table>";
        $xls_output .= '<table border="0" cellpadding="1" cellspacing="1"><tr>';
        if ( $list != 'rejected' ) {
            $xls_output .= "<td></td>";
            $xls_output .= "<td></td>";
        }
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        if ( $list != 'rejected' && $list != 'cancelled' ) {
            $xls_output .= "<td>" . __( 'tax' ) . "</td>";
            $xls_output .= "<td>" . CURRENCY . ' ' . round( $percenttotal, 2 ) . "</td>";
        } elseif ( $list == 'cancelled' ) {
            $xls_output .= "<td>" . __( 'tax' ) . "</td>";
            $xls_output .= "<td>" . CURRENCY . ' ' . round( $percenttotal, 2 ) . "</td>";
        } else {
            $xls_output .= "<td></td>";
            $xls_output .= "<td></td>";
        }
        $xls_output .= "</tr>";
        $xls_output .= "</table>";
        $xls_output .= '<table border="0" cellpadding="1" cellspacing="1"><tr>';
        if ( $list != 'rejected' ) {
            $xls_output .= "<td></td>";
            $xls_output .= "<td></td>";
        }
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        $xls_output .= "<td></td>";
        if ( $list != 'rejected' && $list != 'cancelled' ) {
            $xls_output .= "<td>" . __( 'trip_total_fare' ) . "</td>";
            $xls_output .= "<td>" . CURRENCY . ' ' . round( $total_fare, 2 ) . "</td>";
        } elseif ( $list == 'cancelled' ) {
            $xls_output .= "<td>" . __( 'trip_total_fare' ) . "</td>";
            $xls_output .= "<td>" . CURRENCY . ' ' . round( $total_fare, 2 ) . "</td>";
        } else {
            $xls_output .= "<td></td>";
            $xls_output .= "<td></td>";
        }
        $xls_output .= "</tr>";
        $xls_output .= "</table>";
        $filename = $file . "_" . date( "Y-m-d_H-i", time() );
        $html     = preg_replace( "<tbody>", " ", $xls_output );
        $html     = preg_replace( "</tbody>", " ", $html );
        ob_clean();
        $generate_pdf = $manage->generate_pdf( $html, $filename );
    }
    public function action_accountexport()
    {
        $company           = trim( Html::chars( $_REQUEST['filter_company'] ) );
        $startdate         = trim( Html::chars( $_REQUEST['startdate'] ) );
        $enddate           = trim( Html::chars( $_REQUEST['enddate'] ) );
        $taxiid            = trim( Html::chars( $_REQUEST['taxiid'] ) );
        $driver_id         = trim( Html::chars( $_REQUEST['driver_id'] ) );
        $manager_id        = trim( Html::chars( $_REQUEST['manager_id'] ) );
        $passengerid       = trim( Html::chars( $_REQUEST['passengerid'] ) );
        $transaction_id    = trim( Html::chars( $_REQUEST['transaction_id'] ) );
        //import admin model
        $transaction_model = Model::factory( 'transaction' );
        $find_url          = explode( '/', $_SERVER['REQUEST_URI'] );
        $split             = explode( '?', $find_url[2] );
        $list              = $split[0];
        $list              = 'all';
        //export csv data retrieved here
        $list              = $transaction_model->accountexport_data( $list, $company, $manager_id, $taxiid, $driver_id, $passengerid, $startdate, $enddate, $transaction_id );
    }
    /**** For Graph **************/
    public function action_gettriptotals()
    {
        $this->auto_render = false;
        $transaction_model = Model::factory( 'transaction' );
        $company           = trim( Html::chars( $_REQUEST['filter_company'] ) );
        $startdate         = trim( Html::chars( $_REQUEST['startdate'] ) );
        $enddate           = trim( Html::chars( $_REQUEST['enddate'] ) );
        $taxiid            = trim( Html::chars( $_REQUEST['taxiid'] ) );
        $driver_id         = trim( Html::chars( $_REQUEST['driver_id'] ) );
        $split_time        = explode( ":", $waiting_time );
        $year              = date( 'Y' );
        for ( $i = 1; $i <= 12; $i++ ) {
            $count = $model_dashboard->getUsers( $i, $year );
            if ( $count == '' || $count == 'NULL' ) {
                $count = "0";
            }
            $data['users'][] = array(
                 'count' => $count 
            );
        }
        $json            = array();
        $json['success'] = $data;
        echo json_encode( $json );
    }
    //Transactions Details
    public function action_transaction_details()
    {
        $user_createdby      = $_SESSION['userid'];
        $usertype            = $_SESSION['user_type'];
        $manage_transaction  = Model::factory( 'transaction' );
        $company_id          = $_SESSION['company_id'];
        $log_id              = explode( '/', $_SERVER['REQUEST_URI'] );
        $transaction_details = $manage_transaction->viewtransaction_details( $log_id[3] );
        if ( count( $transaction_details ) == 0 ) {
            if ( $usertype == 'A' ) {
                $this->request->redirect( "transaction/admintransaction/all" );
            } else if ( $usertype == 'C' ) {
                $this->request->redirect( "transaction/companytransaction/all" );
            } else {
                $this->request->redirect( "transaction/managertransaction/all" );
            }
        }
        //send data to view file 
        $view                       = View::factory( 'admin/transaction_details' )->bind( 'transaction_details', $transaction_details )->bind( 'manage_transaction', $manage_transaction );
        $this->page_title           = __( 'transaction_list' );
        $this->template->title      = SITENAME . " | " . __( 'transaction_list' );
        $this->template->page_title = __( 'transaction_list' );
        $this->template->content    = $view;
    }
    //Braintree Settlement
    public function action_settlement_list()
    {
        $user_createdby            = $_SESSION['userid'];
        $usertype                  = $_SESSION['user_type'];
        $manage_transaction        = Model::factory( 'transaction' );
        $commonmodel               = Model::factory( 'commonmodel' );
        $company_id                = $_SESSION['company_id'];
        $log_id                    = explode( '/', $_SERVER['REQUEST_URI'] );
        $company_id                = ( $company_id != '0' ) ? $company_id : '';
        $current_date              = $commonmodel->getcompany_all_currenttimestamp( $company_id );
        $default_start             = date( 'Y-m-d', strtotime( '-1 month', strtotime( $current_date ) ) );
        $default_end               = $enddate = convert_timezone( 'now', $_SESSION['timezone'] ); //date('Y-m-d', strtotime($current_date));
        $keyword                   = trim( Html::chars( isset( $_GET['keyword'] ) ? $_GET['keyword'] : '' ) );
        $filter_company            = ( isset( $_GET['filter_company'] ) ? $_GET['filter_company'] : '' );
        $start_date                = isset( $_GET['startdate'] ) ? Commonfunction::ensureDatabaseFormat( $_GET['startdate'], 1 ) : $default_start;
        $end_date                  = isset( $_GET['enddate'] ) ? Commonfunction::ensureDatabaseFormat( $_GET['enddate'], 2 ) : $default_end;
        $get_allcompany            = $manage_transaction->get_allcompany_tranaction( $usertype );
        $count_transaction_details = $manage_transaction->total_braintree_transaction_details( $keyword, $start_date, $end_date, $company_id, $filter_company );
        //pagination loads here
        $page_no                   = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset              = REC_PER_PAGE * ( $page_no - 1 );
        $pag_data            = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_transaction_details,
            'view' => 'pagination/punbb' 
        ) );
        $transactionid_array = array();
        $transaction_details = $manage_transaction->braintree_transaction_details( $keyword, $start_date, $end_date, $company_id, $filter_company, REC_PER_PAGE, $offset );
        foreach ( $transaction_details as $key => $val ) {
            if ( isset($val['payment_status']) != 'settled' ) {
                $transactionid_array[$key] = $val['transaction_id'] . ":" . $val['trip_id'];
            }
        }
        if ( !empty( $transactionid_array ) && $usertype != 'A' ) {
            $update_settlement_status = $manage_transaction->update_settlement_status( $transactionid_array, $company_id );
        }
        if ( isset( $_POST['update'] ) ) {
            $transaction_array        = isset( $_POST['uniqueId'] ) ? $_POST['uniqueId'] : '';
    
            foreach ( $transaction_array as $key => $val ) {
                $trans          = explode( ":", $val );
                $transaction_id = $trans[0];
                $trip_id        = $trans[1];
                
                

                    // Payment gateway settlement transaction
                $additional_parameters=['payment_types'=>2];
                    if (class_exists('Paymentgateway')) {
                        $paymentresponse = Paymentgateway::payment_gateway_connect('settlement',$transaction_id,[],[],$additional_parameters);
                        $payment_status=$paymentresponse['payment_status'];
                        $transaction_status=$paymentresponse['transaction_status'];
                    } else {
                        trigger_error("Unable to load class: Paymentgateway", E_USER_WARNING);
                    }
                    if($payment_status==1){
                         
                    $manage_transaction = $manage_transaction->update_transaction( array(
                         'payment_status' => $transaction_status
                    ), $trip_id );
                    if ( $transaction_status == 'Settled' ) {
                        Message::success( __( 'Settlement Success for Selected Request' ) );
                        $this->request->redirect( "/transaction/settlement_list" );
                    } else {
                        Message::success( __( 'Selected Request is' ) . " " .  ucwords( $transaction_status )  );
                        $this->request->redirect( "/transaction/settlement_list" );
                    }
                    }else{
                        
                    $braintree_message = $paymentresponse['payment_response'];
                    $manage_transaction = $manage_transaction->update_transaction( array(
                         'payment_status' => $transaction_status 
                    ), $trip_id );
                    Message::error( $braintree_message );
                    $this->request->redirect( "/transaction/settlement_list" );
                    }                
            }
        }
        //send data to view file 
        $view                       = View::factory( 'admin/manage_settlement' )->bind( 'Offset', $offset )->bind( 'action', $action )->bind( 'current_date', $current_date )->bind( 'srch', $_REQUEST )->bind( 'company_id', $company_id )->bind( 'pag_data', $pag_data )->bind( 'get_allcompany', $get_allcompany )->bind( 'transaction_details', $transaction_details );
        $this->page_title           = __( 'braintree_settlemant' );
        $this->template->title      = SITENAME . " | " . __( 'braintree_settlemant' );
        $this->template->page_title = __( 'braintree_settlemant' );
        $this->template->content    = $view;
    }// End Braintree Settlement
} // End Tranaction
?>
