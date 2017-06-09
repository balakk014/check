<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
* Contains Tdispatch details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************/
class Controller_TaximobilityTdispatch extends Controller_Siteadmin
{
    /**
     ****__construct()****
     * 
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
            $this->request->redirect( "/company/login/" );
        }
        return;
    }
    public function action_getDistanceandtime()
    {
        $output           = '';
        $current_location = arr::get( $_REQUEST, 'current_location' );
        $drop_location    = arr::get( $_REQUEST, 'drop_location' );
        $current_location = urlencode( $current_location );
        $drop_location    = urlencode( $drop_location );
        $json             = file_get_contents( 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $current_location . '&destination=' . $drop_location . '&waypoints=&sensor=false&key=' . GOOGLE_GEO_API_KEY );
        $details          = json_decode( $json, TRUE );
        $distance         = $details['routes'][0]['legs']['0']['distance']['text'];
        $duration         = $details['routes'][0]['legs']['0']['duration']['text'];
        $output           = $distance . ',' . $duration;
        echo $output;
        exit;
    }
    /** Manage Tdispatch settings **/
    public function action_tdispatch_settings()
    {
        $this->is_login();
        $usertype = $_SESSION['user_type'];
        if ( $usertype == 'A' && $usertype == 'S' ) {
            $this->request->redirect( "admin/login" );
        }
        $settings        = Model::factory( 'tdispatch' );
        $submit_settings = arr::get( $_REQUEST, 'submit_settings' );
        $post_values     = $errors = array();
        if ( $submit_settings && Validation::factory( $this->request->post() ) ) {
            $post      = Arr::map( 'trim', $this->request->post() );
            $validator = $settings->validate_dispatchsetting( arr::extract( $post, array(
                 'labelname' 
            ) ) );
            if ( $validator->check() ) {
                $status = $settings->update_dispatchsetting( $post );
                if ( $status == 1 ) {
                    Message::success( __( 'sucessful_settings_update' ) );
                } else {
                    Message::error( __( 'not_updated' ) );
                }
                $this->request->redirect( "tdispatch/tdispatch_settings" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $tdispatch_settings         = $settings->tdispatch_settings();
        $this->selected_page_title  = __( "site_settings" );
        $view                       = View::factory( 'admin/tdispatch/manage_tdispatch_settings' )->bind( 'postvalue', $post_values )->bind( 'errors', $errors )->bind( 'tdispatch_settings', $tdispatch_settings );
        $this->template->title      = SITENAME . " | " . __( 'tdispatch_setting' );
        $this->template->page_title = __( 'tdispatch_setting' );
        $this->template->content    = $view;
    }
    public function action_get_citymodel_fare_details()
    {
        $company_id           = $_SESSION['company_id'];
        $tdispatch_model      = Model::factory( 'tdispatch' );
        $common_model         = Model::factory( 'commonmodel' );
        $total_min            = $_REQUEST['total_min'];
        $taxi_fare_details    = $tdispatch_model->get_citymodel_fare_details( $_REQUEST['model_id'], $_REQUEST['city_name'], $_REQUEST['city_id'], $company_id );
        $company_tax          = $common_model->company_tax( $company_id );
        $base_fare            = $taxi_fare_details[0]->base_fare;
        $min_km_range         = $taxi_fare_details[0]->min_km;
        $min_fare             = $taxi_fare_details[0]->min_fare;
        $cancellation_fare    = $taxi_fare_details[0]->cancellation_fare;
        $below_above_km_range = $taxi_fare_details[0]->below_above_km;
        $below_km             = $taxi_fare_details[0]->below_km;
        $above_km             = $taxi_fare_details[0]->above_km;
        $night_charge         = $taxi_fare_details[0]->night_charge;
        $night_timing_from    = $taxi_fare_details[0]->night_timing_from;
        $night_timing_to      = $taxi_fare_details[0]->night_timing_to;
        $night_fare           = $taxi_fare_details[0]->night_fare;
        $waiting__per_hour    = $taxi_fare_details[0]->waiting_time;
        $minutes_fare         = $taxi_fare_details[0]->minutes_fare;
        //Waiting Time Charge for an company
        $total_fare           = $distance = $total = 0;
        $distance             = $_REQUEST['distance_km'];
        $total_fare           = $base_fare;
        if ( FARE_CALCULATION_TYPE == 1 || FARE_CALCULATION_TYPE == 3 ) {
            if ( $distance < $min_km_range ) {
                $total_fare = $min_fare;
            } else if ( $distance <= $below_above_km_range ) {
                $fare       = $distance * $below_km;
                $total_fare = $fare + $base_fare;
            } else if ( $distance > $below_above_km_range ) {
                $fare       = $distance * $above_km;
                $total_fare = $fare + $base_fare;
            }
        }
        if ( FARE_CALCULATION_TYPE == 2 || FARE_CALCULATION_TYPE == 3 ) {
            /********** Minutes fare calculation ************/
            $minutes = round( $total_min / 60 );
            if ( $minutes_fare > 0 ) {
                $minutes_cost = $minutes * $minutes_fare;
                $total_fare   = $total_fare + $minutes_cost;
            }
            /************************************************/
        }
        /** Edited By Logeswaran
         *  TAX Added Here Removed in script.js
         */
        $total_fare = number_format( ( ( $total_fare * TAX / 100 ) + $total_fare ), 2, '.', ' ' );
        echo $total_fare;
        exit;
    }
}
?>