<?php
defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
 * Contains Dashboard(Site Statistics - Count) details
 * @Package: Taximobility
 * @Author: taxi Team
 * @URL : taximobility.com
 ********************************************/
class Controller_TaximobilityDashboard extends Controller_Siteadmin
{
    /**
     * ****action_index()****
     * @return user listings  view with pagination
     */
    public function action_index()
    {
        //set page title
        $this->page_title          = __( 'menu_dashboard' );
        $this->selected_page_title = __( 'menu_dashboard' );
        //auth login check
        $this->is_login();
        //import model
        $model_dashboard         = Model::factory( 'dashboard' );
        //send data to view file 
        $view                    = View::factory( 'admin/dashboard' )->bind( 'title', $title )->bind( 'dashboard', $model_dashboard );
        $this->template->content = $view;
    }
    public function action_getUsers()
    {
        $this->auto_render = false;
        $model_dashboard   = Model::factory( 'dashboard' );
        $year              = date( 'Y' );
        if ( ( !isset( $_REQUEST['startdate'] ) ) && ( !isset( $_REQUEST['enddate'] ) ) ) {
            for ( $i = 1; $i <= 12; $i++ ) {
                $count = $model_dashboard->getUsers( $i, $year );
                if ( $count == '' || $count == 'NULL' ) {
                    $count = "0";
                }
                $data['users'][] = array(
                     'count' => $count 
                );
            }
        } else {
            $startdate = $_REQUEST['startdate'];
            $enddate   = $_REQUEST['enddate'];
            $count     = $model_dashboard->getUsersbydate( $startdate, $enddate );
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
    public function action_get_company_trip_count()
    {
        $this->auto_render = false;
        $model_dashboard   = Model::factory( 'dashboard' );
        $year              = date( 'Y' );
        $webtrips          = 0;
        $mobiletrips       = 0;
        for ( $i = 1; $i <= 12; $i++ ) {
            $appwise         = $model_dashboard->appwise_trips( $i, $year );
            $count           = 0;
            $revenues        = 0;
            $admincommission = 0;
            $average         = 0;
            if ( count( $appwise ) > 0 ) {
                $count           = $appwise[0]['total_trips'];
                $revenues        = $appwise[0]['revenues'];
                $admincommission = $appwise[0]['admincommission'];
                $webtrips        = $appwise[0]['webtrips'] + $webtrips;
                $mobiletrips     = $appwise[0]['mobiletrips'] + $mobiletrips;
                $average         = $count / $revenues;
            }
            $data['trips'][] = array(
                 'trips' => $count,
                'revenues' => round( $revenues, 2 ),
                'admincommission' => round( $admincommission, 2 ),
                'average' => round( $average, 3 ) 
            );
        }
        $data['webtrips']    = $webtrips;
        $data['mobiletrips'] = $mobiletrips;
        $json                = array();
        $json['success']     = $data;
        echo json_encode( $json );
    }
    //** Function to get total users count companywise **//
    public function action_getallUsersCompanywise()
    {
        $data = array();
        if ( isset( $_POST['company'] ) ) {
            $company_id      = $_POST['company'];
            $model_dashboard = Model::factory( 'dashboard' );
            $model_admin     = Model::factory( 'admin' );
            $company_name    = '';
            $drivers         = 0;
            $taxis           = 0;
            $passengers      = 0;
            $usersTaxies     = $model_dashboard->getCompanyUsersTaxi( $company_id ); //getting total drivers, taxi, passengers
            if ( count( $usersTaxies ) > 0 ) {
                $drivers      = $usersTaxies[0]['totaldrivers'];
                $taxis        = $usersTaxies[0]['totaltaxis'];
                $passengers   = $usersTaxies[0]['totalpassengers'];
                $company_name = $usersTaxies[0]['company_name'];
            }
            $data['totalusers'][] = array(
                 'drivers' => $drivers,
                'taxis' => $taxis,
                'passengers' => $passengers 
            );
            $data['companyName']  = strtoupper( $company_name );
        }
        $json            = array();
        $json['success'] = $data;
        echo json_encode( $json );
        exit;
    }
    public function action_total_trip_details_search()
    {
        $post_values     = $_POST;
        $startdate       = Commonfunction::ensureDatabaseFormat( $post_values['startdate'], 1 );
        $enddate         = Commonfunction::ensureDatabaseFormat( $post_values['enddate'], 2 );
        $company_id      = $post_values['company'];
        $model_dashboard = Model::factory( 'dashboard' );
        $dashboard       = Model::factory( 'admin' );
        $get_transaction = $model_dashboard->total_trip_details( $company_id, $startdate, $enddate );
        $view            = View::factory( 'admin/statistics/total_trip_revenue' )->bind( 'post_values', $post_values )->bind( 'get_transaction', $get_transaction );
        echo $view;
        exit;
    }
    public function action_driver_status_details_search()
    {
        $driver_status        = $_REQUEST['driver_status'];
        $manage_company       = Model::factory( 'taxidispatch' );
        $all_company_map_list = $manage_company->driver_status_details( $_REQUEST );
        $a                    = 0;
        $b                    = 5;
        $markers              = array();
        if ( count( $all_company_map_list ) > 0 ) {
            foreach ( $all_company_map_list as $v ) {
                for ( $b = 0; $b < 5; $b++ ) {
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
                        $driver_status   = ( $v['driver_status'] == 'F' && $v['shift_status'] == 'IN' ) ? __( 'free_in' ) : ( ( $v['driver_status'] == 'A' ) ? "<span>" . __( 'hired' ) . "</span>" : ( ( $v['driver_status'] == 'B' ) ? "<span>" . __( 'trip_assigned' ) . "</span>" : __( 'free_out' ) ) );
                        $txtcolor        = ( $v['driver_status'] == 'F' && $v['shift_status'] == 'IN' ) ? 'green' : ( ( $v['driver_status'] == 'A' ) ? '#07841E' : ( ( $v['driver_status'] == 'B' ) ? 'red' : '#0F9ED6' ) );
                        $markers[$a][$b] = '<div id="bodyContent"><p><b>' . __( 'driver_status' ) . '</b>: <b style="color:' . $txtcolor . ';">' . $driver_status . '</b></p></div></div>';
                    }
                    if ( $b == 4 ) {
                        if ( $v['driver_status'] == 'F' && $v['shift_status'] == 'OUT' ) {
                            $markers[$a][$b] = URL_BASE . 'public/admin/images/dashboard_icons/map_shiftout_icon.png';
                        } elseif ( $v['driver_status'] == 'A' ) {
                            $markers[$a][$b] = URL_BASE . 'public/admin/images/dashboard_icons/map_incactive_icon.png';
                        } elseif ( $v['driver_status'] == 'B' ) {
                            $markers[$a][$b] = URL_BASE . 'public/admin/images/dashboard_icons/map_waiting_icon.png';
                        } elseif ( $v['driver_status'] == 'F' && $v['shift_status'] == 'IN' ) {
                            $markers[$a][$b] = URL_BASE . 'public/admin/images/dashboard_icons/map_available_icon.png';
                        }
                    }
                }
                $a++;
            }
        }
        echo json_encode( $markers );
        exit;
    }
    public function action_driver_status_details_search_company()
    {
        $post_values          = $_POST;
        $driver_status        = $_REQUEST['driver_status'];
        $company              = $_REQUEST['company'];
        $manage_company       = Model::factory( 'dashboard' );
        $all_company_map_list = $manage_company->driver_status_details_company( $driver_status, $company );
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
    public function action_get_admin_total_details()
    {
        $this->auto_render       = false;
        $model_dashboard         = Model::factory( 'dashboard' );
        $dashboard               = Model::factory( 'admin' );
        $company_countlist       = $dashboard->get_comapny_countlist();
        $passenger_countlist     = $dashboard->count_passenger_list_history();
        $drivers_countlist       = $dashboard->get_drivers_countlist();
        $availabletaxi_countlist = $dashboard->get_taxi_countlist();
        if ( $company_countlist == '' || $company_countlist == 'NULL' ) {
            $company_countlist = "0";
        } else {
            $company_countlist = $company_countlist;
        }
        if ( $passenger_countlist == '' || $passenger_countlist == 'NULL' ) {
            $passenger_countlist = "0";
        } else {
            $passenger_countlist = $passenger_countlist;
        }
        if ( $drivers_countlist == '' || $drivers_countlist == 'NULL' ) {
            $drivers_countlist = "0";
        } else {
            $drivers_countlist = $drivers_countlist;
        }
        if ( $availabletaxi_countlist == '' || $availabletaxi_countlist == 'NULL' ) {
            $availabletaxi_countlist = "0";
        } else {
            $availabletaxi_countlist = $availabletaxi_countlist;
        }
        $data['latest_details'][] = array(
             'company_countlist' => "['Total Companies (" . $company_countlist . ")'," . $company_countlist . "]",
            'passenger_countlist' => "['Total passengers (" . $passenger_countlist . ")'," . $passenger_countlist . "]",
            'drivers_countlist' => "['Total Drivers (" . $drivers_countlist . ")'," . $drivers_countlist . "]",
            'availabletaxi_countlist' => "['Total Taxies (" . $availabletaxi_countlist . ")'," . $availabletaxi_countlist . "]" 
        );
        $json                     = array();
        $json['success']          = $data;
        echo json_encode( $json );
    }
    public function action_get_admin_latest_details()
    {
        $this->auto_render        = false;
        $model_dashboard          = Model::factory( 'dashboard' );
        $dashboard                = Model::factory( 'admin' );
        $activeusers_list_count   = $dashboard->get_activeusers_list_count();
        $availabletaxi_list_count = $dashboard->get_availabletaxi_list_count();
        $freedriver_list_count    = $dashboard->free_driver_list_count();
        $freetaxi_list_count      = $dashboard->free_taxi_list_count();
        if ( $activeusers_list_count == '' || $activeusers_list_count == 'NULL' ) {
            $activeusers_list_count = "0";
        } else {
            $activeusers_list_count = $activeusers_list_count;
        }
        if ( $availabletaxi_list_count == '' || $availabletaxi_list_count == 'NULL' ) {
            $availabletaxi_list_count = "0";
        } else {
            $availabletaxi_list_count = $availabletaxi_list_count;
        }
        if ( $freedriver_list_count == '' || $freedriver_list_count == 'NULL' ) {
            $freedriver_list_count = "0";
        } else {
            $freedriver_list_count = $freedriver_list_count;
        }
        if ( $freetaxi_list_count == '' || $freetaxi_list_count == 'NULL' ) {
            $freetaxi_list_count = "0";
        } else {
            $freetaxi_list_count = $freetaxi_list_count;
        }
        $data['latest_details'][] = array(
             'activeusers_list_count' => "['Live Passengers (" . $activeusers_list_count . ")'," . $activeusers_list_count . "]",
            'availabletaxi_list_count' => "['Today Unassigned Taxies (" . $freetaxi_list_count . ")'," . $freetaxi_list_count . "]",
            'freedriver_list_count' => "['Today Unassigned Drivers (" . $freedriver_list_count . ")'," . $freedriver_list_count . "]",
            'freetaxi_list_count' => "['Today Free Taxies (" . $availabletaxi_list_count . ")'," . $availabletaxi_list_count . "]" 
        );
        $json                     = array();
        $json['success']          = $data;
        echo json_encode( $json );
    }
    /**
     * Dashboard Total Details Chart
     **/
    public function action_dashboardTotalDetails()
    {
        $start_date = Commonfunction::ensureDatabaseFormat( $this->request->post( "startdate" ), 1 );
        $end_date   = Commonfunction::ensureDatabaseFormat( $this->request->post( "enddate" ), 2 );
        $usertype   = $_SESSION['user_type'];
        if ( $usertype == 'C' || $usertype == 'M' ) {
            $company_id = $this->session->get( 'company_id' );
        } else {
            $company_id = $this->request->post( "company" );
        }
        $brand_filter  = $this->request->post( "brand_filter" );
        $dashboard     = Model::factory( 'dashboard' );
        $dashboardData = $dashboard->getDashboardData( $company_id, $start_date, $end_date );
        $view          = View::factory( 'admin/dashboard/dashboard_total_details' )->bind( "dashboardData", $dashboardData )->bind( "driver_commission", $driver_commission )->bind( "company_id", $company_id );
        echo $view;
        exit;
    }
    /**
     * Assigned VS Unassigned Chart
     **/
    public function action_dashboardAssignUnassigenChart()
    {
        $start_date    = Commonfunction::ensureDatabaseFormat( date( 'Y-m-d 00:00:00' ), 1 );
        $end_date      = Commonfunction::ensureDatabaseFormat( date( 'Y-m-d 23:59:59' ), 2 );
        $company_id    = $this->request->post( "company" );
        $dashboard     = Model::factory( 'dashboard' );
        $dashboardData = $dashboard->getDashboardassignUnassignData( $company_id, $start_date, $end_date );
        $company_name  = $taxi_count = $total_driver_count = $assigned_driver_count = $assigned_taxi_count = "";
        if ( count( $dashboardData ) > 0 ) {
            foreach ( $dashboardData as $company ) {
                if ( isset( $company["cid"] ) && $company["cid"] != "" && isset( $company["company_name"] ) && $company["company_name"] != "" ) {
                    $taxi_count[]            = ( $company["taxi_count"] != "" ) ? $company["taxi_count"] : 0;
                    $total_driver_count[]    = ( $company["total_driver_count"] != "" ) ? $company["total_driver_count"] : 0;
                    $company_name[]          = $company["company_name"];
                    $assigned_driver_count[] = $company["assigned_driver_count"];
                    $assigned_taxi_count[]   = $company["assigned_taxi_count"];
                }
            }
            $company_name          = explode( ",", addslashes( implode( ",", array_map( 'ucfirst', $company_name ) ) ) );
            $company_name          = "'" . implode( "','", $company_name ) . "'";
            $taxi_count            = implode( ",", $taxi_count );
            $total_driver_count    = implode( ",", $total_driver_count );
            $assigned_driver_count = implode( ",", $assigned_driver_count );
            $assigned_taxi_count   = implode( ",", $assigned_taxi_count );
        }
        $view = View::factory( 'admin/dashboard/assigned_unassigned_chart' )->bind( "company_name", $company_name )->bind( "taxi_count", $taxi_count )->bind( "assigned_taxi_count", $assigned_taxi_count )->bind( "total_driver_count", $total_driver_count )->bind( "assigned_driver_count", $assigned_driver_count )->bind( "company_id", $company_id );
        echo $view;
        exit;
    }
    /**
     * Comapny Wise Trip Chart
     **/
    public function action_dashboardCompanyWiseTripChart()
    {
        $start_date   = Commonfunction::ensureDatabaseFormat( $this->request->post( "startdate" ), 1 );
        $end_date     = Commonfunction::ensureDatabaseFormat( $this->request->post( "enddate" ), 2 );
        $company_id   = ( COMPANY_CID > 0 ) ? COMPANY_CID : "";
        $dashboard    = Model::factory( 'dashboard' );
        $result       = $dashboard->getCompanyWiseTrip( $company_id, $start_date, $end_date );
        $company_name = $trip_completed = $trip_inprogress = $trip_cancelled = "";
        if ( count( $result ) > 0 ) {
            $company_name_array = $trip_completed_array = $trip_inprogress_array = $trip_cancelled_array = array();
            foreach ( $result as $trip ) {
                $company_name_array[]    = $trip["company_name"];
                $trip_completed_array[]  = $trip["trip_completed"];
                $trip_inprogress_array[] = $trip["trip_inprogress"];
                $trip_cancelled_array[]  = $trip["trip_cancelled"];
            }
            $company_name    = explode( ",", addslashes( implode( ",", array_map( 'ucfirst', $company_name_array ) ) ) );
            $company_name    = "'" . implode( "','", $company_name ) . "'";
            $trip_completed  = implode( ",", $trip_completed_array );
            $trip_inprogress = implode( ",", $trip_inprogress_array );
            $trip_cancelled  = implode( ",", $trip_cancelled_array );
        }
        $view = View::factory( 'admin/dashboard/company_wise_trip_chart' )->bind( "company_name", $company_name )->bind( "trip_completed", $trip_completed )->bind( "trip_inprogress", $trip_inprogress )->bind( "trip_cancelled", $trip_cancelled );
        echo $view;
        exit;
    }
    /**
     * Payment By Comapny Chart
     **/
    public function action_dashboardPaymentByCompanyChart()
    {
        $start_date         = Commonfunction::ensureDatabaseFormat( $this->request->post( "startdate" ), 1 );
        $end_date           = Commonfunction::ensureDatabaseFormat( $this->request->post( "enddate" ), 2 );
        $company_id         = $this->request->post( "company" );
        $brand_filter       = $this->request->post( "brand_filter" );
        $dashboard          = Model::factory( 'dashboard' );
        $payment_by_company = $dashboard->getPaymentByCompany( $company_id, $start_date, $end_date );
        $view               = View::factory( 'admin/dashboard/payment_by_company_chart' )->bind( "payment_by_company", $payment_by_company )->bind( "company_id", $company_id );
        echo $view;
        exit;
    }
    /**
     * Comapny Count Chart
     **/
    public function action_dashboardCompanyCountChart()
    {
        $start_date    = Commonfunction::ensureDatabaseFormat( $this->request->post( "startdate" ), 1 );
        $end_date      = Commonfunction::ensureDatabaseFormat( $this->request->post( "enddate" ), 2 );
        $company_id    = $this->request->post( "company" );
        $dashboard     = Model::factory( 'dashboard' );
        $company_count = $dashboard->getCompanyCountChart( $company_id, $start_date, $end_date );
        $view          = View::factory( 'admin/dashboard/company_count_chart' )->bind( "result", $company_count );
        echo $view;
        exit;
    }
    /**
     * Trip Request Top Cities
     **/
    public function action_dashboardTripReqTopCitiesChart()
    {
        $start_date = Commonfunction::ensureDatabaseFormat( $this->request->post( "startdate" ), 1 );
        $end_date   = Commonfunction::ensureDatabaseFormat( $this->request->post( "enddate" ), 2 );
        $company_id = $this->request->post( "company" );
        $dashboard  = Model::factory( 'dashboard' );
        $result     = $dashboard->getTripReqTopCitiesChart( $company_id, $start_date, $end_date );
        $view       = View::factory( 'admin/dashboard/trip_request_top_cities' )->bind( "result", $result );
        echo $view;
        exit;
    }
    /**
     * City By Count
     **/
    public function action_dashboardcityByCountChart()
    {
        $start_date   = Commonfunction::ensureDatabaseFormat( $this->request->post( "startdate" ), 1 );
        $end_date     = Commonfunction::ensureDatabaseFormat( $this->request->post( "enddate" ), 2 );
        $company_id   = $this->request->post( "company" );
        $brand_filter = $this->request->post( "brand_filter" );
        $dashboard    = Model::factory( 'dashboard' );
        $result       = $dashboard->getCityByCountChart( $company_id, $start_date, $end_date );
        $view         = View::factory( 'admin/dashboard/city_by_count_chart' )->bind( "result", $result );
        echo $view;
        exit;
    }
    /**
     * Company Revenue
     **/
    public function action_dashboardcompanyRevenueChart()
    {
        $start_date   = Commonfunction::ensureDatabaseFormat( $this->request->post( "startdate" ), 1 );
        $end_date     = Commonfunction::ensureDatabaseFormat( $this->request->post( "enddate" ), 2 );
        $company_id   = $this->request->post( "company" );
        $brand_filter = $this->request->post( "brand_filter" );
        $dashboard    = Model::factory( 'dashboard' );
        $grpahdata    = $dashboard->getCompanyRevenueChart( $company_id, $start_date, $end_date );
        $view         = View::factory( 'admin/dashboard/company_revenue_graph' )->bind( 'grpahdata', $grpahdata );
        echo $view;
        exit;
    }
    /**
     * Driver Revenue
     **/
    public function action_dashboardDriverRevenueChart()
    {
        $start_date = Commonfunction::ensureDatabaseFormat( $this->request->post( "startdate" ), 1 );
        $end_date   = Commonfunction::ensureDatabaseFormat( $this->request->post( "enddate" ), 2 );
        $driver_id  = $this->request->post( "driver_id" );
        $dashboard  = Model::factory( 'dashboard' );
        $grpahdata  = $dashboard->getDriverRevenueChart( $driver_id, $start_date, $end_date );
        $view       = View::factory( 'admin/dashboard/driver_revenue_graph' )->bind( 'grpahdata', $grpahdata );
        echo $view;
        exit;
    }
    /**
     * Live Dispatch Map
     **/
    public function action_dashboardLiveDispatchMap()
    {
        $taxidispatch         = Model::factory( 'taxidispatch' );
        $all_company_map_list = $taxidispatch->driver_status_details( array ());
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
        $view = View::factory( 'admin/dashboard/live_dispatch_map' )->bind( 'all_company_map_list', $all_company_map_list );
        echo $view;
        exit;
    }
} // End Welcome