<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
* Contains Settings(Including All Over Site)details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************/
class Controller_TaximobilitySettings extends Controller_Website
{
    public function __construct( Request $request, Response $response )
    {
        parent::__construct( $request, $response );
        $this->settings = Model::factory( 'Settings' );
    }
    /** Add Site Settings **/
    public function action_add_settings_site()
    {
        $msg    = "";
        //get current page segment id 
        $id     = $this->request->param( 'id' );
        $action = $this->request->action();
        $action .= "/" . $id;
        $settings                  = Model::factory( 'Settings' );
        $this->selected_page_title = __( "add settings site" );
        $this->page_title          = __( 'site_settings' );
        $settings_post             = arr::get( $_REQUEST, 'addsettings_submit' );
        $errors                    = array();
        if ( isset( $settings_post ) ) {
            $validator = $settings->validate_add_settings_site( arr::extract( $_POST, array(
                 'site_name',
                'site_slogan',
                'site_version',
                'site_language',
                'site_paypal_currency_code',
                'site_paypal_currency',
                'contact_email',
                'comman_email_from',
                'comman_email_to',
                'date_format_display',
                'time_format_display',
                'date_time_format_display',
                'date_format_tooltip',
                'time_format_tooltip',
                'date_time_tooltip',
                'date_time_highlight_tooltip',
                'site_tracker' 
            ) ) );
            if ( $validator->check() ) {
                $status = $settings->add_settings_site( $_POST, $validator );
                if ( $status == 1 ) {
                    //Flash message 
                    Message::success( __( 'add_site_settings_flash' ) );
                    $this->request->redirect( "settings/add_settings_site" );
                } else if ( $status == 0 ) {
                    $site_name_exists = __( "site_name_exists" );
                }
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $view                    = View::factory( 'admin/add_settings_site' )->bind( 'errors', $errors )->bind( 'validator', $validator )->bind( 'site_name_exists', $site_name_exists )->bind( 'site_settings', $site_settings )->bind( 'action', $action );
        $this->template->content = $view;
    }
    /** Manage Site Settings **/
    public function action_manage_site()
    {
        $settings                  = Model::factory( 'settings' );
        $signup_submit             = arr::get( $_REQUEST, 'submit_addmodel' );
        $errors                    = array();
        $post_values               = array();
        $this->selected_page_title = __( "site_settings" );
        $view                      = View::factory( 'admin/add_settings_site' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'site_settings', $site_settings );
        $id                        = $this->request->param( 'id' );
        $site_settings             = $settings->site_settings( $id );
        $this->template->content   = $view;
    }
    /** Site Settings**/
    public function action_site_settings()
    {
        $msg    = "";
        $id     = $this->request->param( 'id' );
        $action = $this->request->action();
        $action .= "/" . $id;
        $settings      = Model::factory( 'Settings' );
        $settings_post = arr::get( $_REQUEST, 'editsettings_submit' );
        $errors        = array();
        //check if site logo exist
        $img_exist     = $settings->check_logo_exist();
        //check if favicon exist
        $img_exist1    = $settings->check_favicon_exist();
        if ( isset( $settings_post ) && ( Validation::factory( $_POST, $_FILES ) ) ) {
            $validator = $settings->validate_edit_settings_site( array_merge( $_POST, array(
                 'site_name',
                'site_slogan',
                'site_description',
                'site_version',
                'site_language',
                'site_paypal_currency_code',
                'site_paypal_currency',
                'contact_email',
                'comman_email_from',
                'comman_email_to',
                'date_format_display',
                'time_format_display',
                'date_time_format_display',
                'date_format_tooltip',
                'time_format_tooltip',
                'date_time_tooltip',
                'date_time_highlight_tooltip',
                'site_tracker',
                'site_logo' 
            ) ), $_FILES );
            if ( $validator->check() ) {
                //code for site logo image update
                //===============================
                $image_name = "";
                if ( $_FILES['site_logo']['name'] != '' ) {
                    $image_name = uniqid() . str_replace( " ", "_", $_FILES['site_logo']['name'] );
                    $filename   = Upload::save( $_FILES['site_logo'], $image_name, DOCROOT . LOGO_IMGPATH, '0777' );
                    //Image resize and crop for thumb image
                    $image      = Image::factory( $filename );
                    $path1      = DOCROOT . LOGO_IMGPATH_THUMB;
                    Commonfunction::imageresize( $image, LOGO_WIDTH, LOGO_HEIGHT, $path1, $image_name, 90 );
                }
                //code for site favicon update
                //=============================
                $image_name1 = "";
                if ( $_FILES['site_favicon']['name'] != '' ) {
                    $image_name1 = uniqid() . str_replace( " ", "_", $_FILES['site_favicon']['name'] );
                    $filename1   = Upload::save( $_FILES['site_favicon'], $image_name1, DOCROOT . FAVICON_IMGPATH, '0777' );
                    //Image resize and crop for thumb image
                    $image1      = Image::factory( $filename1 );
                    $path2       = DOCROOT . FAVICON_IMGPATH_THUMB;
                    Commonfunction::imageresize( $image1, FAVICON_SMALL_IMAGE_WIDTH, FAVICON_SMALL_IMAGE_HEIGHT, $path2, $image_name1, 90 );
                }
                if ( $img_exist != '' && $image_name != '' ) {
                    if ( file_exists( DOCROOT . LOGO_IMGPATH . $img_exist ) ) {
                        unlink( DOCROOT . LOGO_IMGPATH . $img_exist );
                    }
                    /**If image exists unlink the image from that location**/
                    if ( file_exists( $path1 . $img_exist ) ) {
                        unlink( $path1 . $img_exist );
                    }
                }
                $status = $settings->edit_site_settings( $validator, $_POST, $image_name, $image_name1 );
                //Flash message 
                Message::success( __( 'update_settings_flash' ) );
                $this->request->redirect( "settings/manage_site" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $site_settings             = $settings->site_settings( $id );
        $this->selected_page_title = __( "Manage site" );
        $this->page_title          = __( 'menu_master' );
        $view                      = View::factory( 'admin/add_settings_site' )->bind( 'current_uri', $id )->bind( 'action', $action )->bind( 'site_settings', $site_settings )->bind( 'errors', $errors )->bind( 'validator', $validator );
        $this->template->content   = $view;
    }
    public function action_delete_site_logo()
    {
        //get current page segment id 
        $id          = $this->request->param( 'id' );
        $settings    = Model::factory( 'Settings' );
        $logo_delete = $settings->check_logo_exist();
        if ( file_exists( DOCROOT . LOGO_IMGPATH . $logo_delete ) && $logo_delete != '' ) {
            unlink( DOCROOT . LOGO_IMGPATH . $logo_delete );
        }
        if ( file_exists( DOCROOT . LOGO_IMGPATH_THUMB . $logo_delete ) && $logo_delete != '' ) {
            unlink( DOCROOT . LOGO_IMGPATH_THUMB . $logo_delete );
        }
        $status        = $settings->update_logo_image( $id );
        //send data to view file 
        $site_settings = $settings->site_settings( $id );
        //Flash message 
        Message::success( __( 'delete_site_logo_flash' ) );
        $this->request->redirect( "settings/site_settings/" );
    }
    public function action_delete_site_favicon()
    {
        //get current page segment id 
        $id          = $this->request->param( 'id' );
        $settings    = Model::factory( 'Settings' );
        $icon_delete = $settings->check_favicon_exist();
        if ( file_exists( DOCROOT . FAVICON_IMGPATH . $icon_delete ) && $icon_delete != '' ) {
            unlink( DOCROOT . FAVICON_IMGPATH . $icon_delete );
        }
        if ( file_exists( DOCROOT . FAVICON_IMGPATH_THUMB . $icon_delete ) && $icon_delete != '' ) {
            unlink( DOCROOT . FAVICON_IMGPATH_THUMB . $icon_delete );
        }
        $status        = $settings->update_favicon_image( $id );
        //send data to view file 
        $site_settings = $settings->site_settings( $id );
        //Flash message 
        Message::success( __( 'delete_site_favicon_flash' ) );
        $this->request->redirect( "settings/site_settings/" );
    }
    /** Meta Site Settings**/
    public function action_site_settings_meta()
    {
        $msg    = "";
        $id     = $this->request->param( 'meta_id' );
        $action = $this->request->action();
        $action .= "/" . $id;
        $settings      = Model::factory( 'Settings' );
        $settings_post = arr::get( $_REQUEST, 'metasettings_submit' );
        $errors        = array();
        if ( isset( $settings_post ) ) {
            $validator = $settings->validate_site_settings_meta( arr::extract( $_POST, array(
                 'meta_keywords',
                'meta_description' 
            ) ) );
            if ( $validator->check() ) {
                $status = $settings->edit_site_settings_meta( $id, $_POST );
                //Flash message 
                Message::success( __( 'update_metasettings_flash' ) );
                $validator = null;
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $site_meta                 = $settings->get_site_settings_meta();
        $this->selected_page_title = __( "menu_meta_settings" );
        $this->page_title          = __( 'menu_meta_settings' );
        $view                      = View::factory( 'admin/site_settings_meta' )->bind( 'current_uri', $id )->bind( 'action', $action )->bind( 'site_meta', $site_meta )->bind( 'errors', $errors )->bind( 'validator', $validator );
        $this->template->content   = $view;
    }
    /** Manage Site Settings Meta **/
    public function action_manage_site_meta()
    {
        $settings                  = Model::factory( 'Settings' );
        $errors                    = array();
        $this->selected_page_title = __( "Manage Site Settings Meta" );
        $view                      = View::factory( 'admin/site_settings_meta' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'site_meta ', $site_meta );
        $id                        = $this->request->param( 'meta_id' );
        $site_meta                 = $settings->get_site_settings_meta();
        $this->page_title          = __( "menu_master" );
        $this->template->content   = $view;
    }
    /** Site Settings Users**/
    public function action_site_settings_user()
    {
        $msg                = "";
        $error              = array();
        $user_settings_post = arr::get( $_POST, 'usersettings_submit' );
        if ( isset( $user_settings_post ) ) {
            $validator = $this->settings->validate_site_settings_user( arr::extract( $_POST, array(
                 'inactive_users' 
            ) ) );
            if ( $validator->check() ) {
                $status = $this->settings->edit_site_settings_user( $_POST );
                if ( $status == 1 ) {
                    //Flash message 
                    Message::success( __( 'update_usersettings_flash' ) );
                } else {
                    $msg = __( 'meta not exists' );
                }
                $validator = null;
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $this->selected_page_title = __( "menu_user_settings" );
        $this->page_title          = __( 'menu_user_settings' );
        $view                      = View::factory( 'admin/site_settings_user' )->bind( 'user_setting_data', $site_settings_user )->bind( 'errors', $errors )->bind( 'validator', $validator );
        $site_settings_user        = $this->settings->get_site_settings_user();
        $this->template->content   = $view;
    }
    /** Site Settings Gigs**/
    public function action_site_settings_gig()
    {
        //echo "fdsfdfd";exit;
        $msg    = "";
        $id     = $this->request->param( 'id' );
        $action = $this->request->action();
        $action .= "/" . $id;
        $settings      = Model::factory( 'Settings' );
        $settings_post = arr::get( $_REQUEST, 'gigsettings_submit' );
        $errors        = array();
        if ( isset( $settings_post ) ) {
            $validator          = $settings->validate_site_settings_gig( arr::extract( $_POST, array(
                 'min_days',
                'max_days',
                'prices',
                'commission_amount',
                'commission_type_id',
                'alternate_name',
                'sidebar_cat_limit',
                'rating_type_id',
                'min_title_length',
                'max_title_length',
                'max_desc_length',
                'amount_cleared_days',
                'inprogress_overtime',
                'auto_review_complete' 
            ) ) );
            $price_explode      = explode( ',', $_POST['prices'] );
            $commission_explode = explode( ',', $_POST['commission_amount'] );
            //count for price amount
            //======================
            $price_count        = array_unique( $price_explode );
            $commission_count   = array_unique( $commission_explode );
            if ( $validator->check() ) {
                $status = $settings->edit_site_settings_gig( $_POST );
                //Flash message 
                Message::success( __( 'update_gigsettings_flash', array(
                     ':param' => ( $this->job_settings[0]['alternate_name'] ) 
                ) ) );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $site_settings_gig         = $settings->get_site_settings_gig();
        $this->selected_page_title = __( "menu_gig_settings", array(
             ':param' => ( $this->job_settings[0]['alternate_name'] ) 
        ) );
        $this->page_title          = __( "menu_gig_settings", array(
             ':param' => ( $this->job_settings[0]['alternate_name'] ) 
        ) );
        $view                      = View::factory( 'admin/site_settings_gig' )->bind( 'current_uri', $id )->bind( 'action', $action )->bind( 'site_settings_gig', $site_settings_gig )->bind( 'commission_errors', $commission_errors )->bind( 'errors', $errors )->bind( 'validator', $validator );
        $this->template->content   = $view;
    }
    /** Site Settings social_network **/
    public function action_social_network()
    {
        $msg    = "";
        $id     = $this->request->param( 'id' );
        $action = $this->request->action();
        $action .= "/" . $id;
        $settings      = Model::factory( 'Settings' );
        $settings_post = arr::get( $_REQUEST, 'socialnetwork_submit' );
        $errors        = array();
        if ( isset( $settings_post ) ) {
            $validator = $settings->validate_site_socialnetwork( arr::extract( $_POST, array(
                 'facebook_api',
                'facebook_access_token',
                'facebook_secret_key',
                'facebook_userid',
                'facebook_application_id',
                'facebook_page_id',
                'twitter_username',
                'tiwtter_consumer_key',
                'twitter_consumer_secret',
                'twitter_access_key',
                'twitter_access_token' 
            ) ) );
            if ( $validator->check() ) {
                $status = $settings->edit_site_socialnetwork( $_POST );
                //Flash message 
                Message::success( __( 'update_socialsettings_flash' ) );
                $validator = null;
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $site_socialnetwork        = $settings->get_site_socialnetwork();
        $this->selected_page_title = __( "menu_social_network_settings" );
        $this->page_title          = __( 'menu_social_network_settings' );
        $view                      = View::factory( 'admin/social_network' )->bind( 'action', $action )->bind( 'site_socialnetwork', $site_socialnetwork )->bind( 'errors', $errors )->bind( 'validator', $validator );
        $this->template->content   = $view;
    }
    /*
     *@Social Network Settings
     *Updated Product in Facebook
     */
    public function action_social_network_account_settings()
    {
        $this->page_title          = __( 'menu_social_network_account' );
        $this->selected_page_title = __( 'menu_social_network_account' );
        $settings                  = Model::factory( 'Settings' );
        $site_socialnetwork        = $settings->get_site_socialnetwork_account( 1 );
        //splitting created_date to display proper format
        //===================================================
        $i                         = 0;
        foreach ( $site_socialnetwork as $site_socialnetwork_account ) {
            $site_socialnetwork[$i]['cdate']      = $this->DisplayDateTimeFormat( $site_socialnetwork_account["cdate"] );
            $site_socialnetwork[$i]['image_url']  = $site_socialnetwork_account["image_url"];
            $site_socialnetwork[$i]['first_name'] = $site_socialnetwork_account["first_name"];
            $site_socialnetwork[$i]['email_id']   = $site_socialnetwork_account["email_id"];
            $site_socialnetwork[$i]['id']         = $site_socialnetwork_account["id"];
            $i++;
        }
        $view                    = View::factory( 'admin/social_network_account' )->bind( 'site_socialnetwork', $site_socialnetwork )->bind( 'errors', $errors )->bind( 'validator', $validator );
        $this->template->content = $view;
    }
    /** Site Settings cashwithdraw**/
    public function action_site_cashwithdraw()
    {
        $msg    = "";
        $id     = $this->request->param( 'id' );
        $action = $this->request->action();
        $action .= "/" . $id;
        $settings      = Model::factory( 'Settings' );
        $settings_post = arr::get( $_REQUEST, 'cashwithdraw_submit' );
        $errors        = array();
        if ( isset( $settings_post ) ) {
            $validator = $settings->validate_site_cashwithdraw( arr::extract( $_POST, array(
                 'min_amount',
                'max_amount',
                'transaction_fee',
                'transaction_fee_type' 
            ) ) );
            if ( $validator->check() ) {
                $status = $settings->edit_site_cashwithdraw( $_POST );
                if ( $status == 1 ) {
                    //Flash message 
                    Message::success( __( 'update_cashsettings_flash' ) );
                }
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $site_cashwithdraw         = $settings->get_site_cashwithdraw();
        $this->selected_page_title = __( "menu_cash_withdraw_settings" );
        $this->page_title          = __( 'menu_cash_withdraw_settings' );
        $view                      = View::factory( 'admin/site_cashwithdraw' )->bind( 'current_uri', $id )->bind( 'action', $action )->bind( 'site_cashwithdraw', $site_cashwithdraw )->bind( 'errors', $errors )->bind( 'validator', $validator );
        $this->template->content   = $view;
    }
    public function action_addfacebook()
    {
        $settings    = Model::factory( 'Settings' );
        $commonmodel = Model::factory( 'commonfunctions' );
        if ( $settings->check_totalsocial_account() ) {
            $arr = $this->facebook->get_details_for_social_account();
            if ( is_array( $arr ) && count( $arr ) > 0 ) {
                if ( $settings->check_fb_account( $arr['id'] ) ) {
                    $insert_fb = commonfunction::add_fb_details( $arr );
                    Message::success( __( 'fb_account_added_success' ) );
                    $this->request->redirect( 'settings/social_network_account_settings' );
                } else {
                    Message::error( __( 'fb_account_already_exists' ) );
                    $this->request->redirect( 'settings/social_network_account_settings' );
                }
            } else {
                $this->facebook->auth();
            }
        } else {
            $this->request->redirect( 'settings/social_network_account_settings' );
        }
    }
    //delete facebook account details 
    //==================================  
    public function action_deletefacebook()
    {
        $get      = Arr::get( $_GET, 'acc_id' );
        $get      = isset( $get ) ? $get : 1;
        $settings = Model::factory( 'Settings' );
        $delete   = $settings->delete_fb_account( $get );
        if ( $delete ) {
            Message::success( __( 'fb_account_delete_success' ) );
            $this->request->redirect( 'settings/social_network_account_settings' );
        }
    }
    /**
     * ****manage_static_pages()****
     * manage static pages
     */
    public function action_manage_static_pages()
    {
        //page and menu title
        //====================
        $this->selected_page_title = __( "page_title_static" );
        $this->page_title          = __( 'page_title_static' );
        //auth login check
        $this->is_login();
        //creating object for model    
        $settings_static_pages = Model::factory( 'settings' );
        $count_pages_list      = $settings_static_pages->count_pages_list();
        //pagination loads here
        //-------------------------
        $page_no               = isset( $_GET['page'] ) ? $_GET['page'] : 0;
        if ( $page_no == 0 || $page_no == 'index' )
            $page_no = PAGE_NO;
        $offset                  = REC_PER_PAGE * ( $page_no - PAGE_NO );
        $pag_data                = Pagination::factory( array(
             'current_page' => array(
                 'source' => 'query_string',
                'key' => 'page' 
            ),
            'items_per_page' => REC_PER_PAGE,
            'total_items' => $count_pages_list,
            'view' => 'pagination/basic' 
        ) );
        $all_pages_list          = $settings_static_pages->all_pages_list( $offset, REC_PER_PAGE );
        //****pagination ends here***//
        //send data to view file 
        $view                    = View::factory( 'admin/site_static_pages_details' )->bind( 'current_uri', $id )->bind( 'action', $action )->bind( 'offset', $offset )->bind( 'pag_data', $pag_data )->bind( 'all_pages_list', $all_pages_list )->bind( 'validator', $validator )->bind( 'errors', $errors );
        $this->template->content = $view;
    }
    /**
     * ****edit_static_pages()****
     * edit static pages
     */
    public function action_edit_static_pages()
    {
        //page title
        //============
        $this->selected_page_title = __( "page_title_static" );
        $this->page_title          = __( 'page_title_static' );
        //get current page segment id 
        $page_id                   = $this->request->param( 'id' );
        //check current action
        $action                    = $this->request->action();
        $action .= "/" . $page_id;
        //creating object for model    
        $settings_static_pages = Model::factory( 'settings' );
        //getting request for form submit
        $page_edit             = arr::get( $_REQUEST, 'edit_pages_submit' );
        $errors                = array();
        //validation starts here    
        if ( isset( $page_edit ) && Validation::factory( $_POST ) ) {
            //****send validation fields into model for checking rules***//
            //===================================================================
            $validator = $settings_static_pages->edit_static_page_form( arr::extract( $_POST, array(
                 'page_title',
                'page_url',
                'page_description',
                'meta_title',
                'meta_keyword' 
            ) ) );
            //validation starts here                     
            if ( $validator->check() ) {
                //*********page edit process starts here*************//
                //===========================================================
                $status = $settings_static_pages->edit_page_data( $page_id, $_POST );
                //Flash message 
                Message::success( __( 'static_page_update_flash' ) );
                //page redirection after success
                $this->request->redirect( "settings/manage_static_pages" );
            } else {
                //validation failed, get errors
                $errors = $validator->errors( 'errors' );
            }
        }
        //send data to view file 
        $page_data               = $settings_static_pages->get_all_static_page_details( $page_id );
        //send data to view file 
        $view                    = View::factory( 'admin/site_manage_static_pages' )->bind( 'current_uri', $id )->bind( 'action', $action )->bind( 'page_data', $page_data[0] )->bind( 'validator', $validator )->bind( 'errors', $errors );
        $this->template->content = $view;
    }
    /**
     * ****show_static_pages()****
     * show static pages
     */
    public function action_show_static_pages()
    {
        //page title
        //============
        $this->selected_page_title = __( "page_title_static" );
        $this->page_title          = __( 'page_title_static' );
        //get current page segment id 
        $page_id                   = $this->request->param( 'id' );
        //creating object for model    
        $settings_static_pages     = Model::factory( 'settings' );
        //send data to view file 
        $page_data                 = $settings_static_pages->show_static_page_content( $page_id );
        $view                      = View::factory( 'admin/show_static_pages' )->bind( 'current_uri', $id )->bind( 'action', $action )->bind( 'page_data', $page_data[0] );
        $this->template->content   = $view;
    }
    /** Manage Smtp Settings **/
    public function action_mail_settings()
    {
        $mail_settings             = Model::factory( 'Settings' );
        $errors                    = array();
        $this->selected_page_title = __( "mail_settings" );
        $view                      = View::factory( 'admin/manage_mail_settings' )->bind( 'validator', $validator )->bind( 'errors', $errors )->bind( 'mail_settings', $mail_settings );
        $id                        = $this->request->param( 'id' );
        $mail_settings             = $mail_settings->mail_settings( $id );
        $this->page_title          = __( "mail_settings" );
        $this->template->content   = $view;
    }
    /** Site Settings**/
    public function action_manage_mail_settings()
    {
        $msg    = "";
        $id     = $this->request->param( 'id' );
        $action = $this->request->action();
        $action .= "/" . $id;
        $settings      = Model::factory( 'Settings' );
        $settings_post = arr::get( $_REQUEST, 'editmailsettings_submit' );
        $errors        = array();
        if ( isset( $settings_post ) && Validation::factory( $_POST ) ) {
            $validator = $settings->validate_edit_mail_settings( arr::extract( $_POST, array(
                 'smtp_host',
                'smtp_port',
                'smtp_username',
                'smtp_password' 
            ) ) );
            if ( $validator->check() ) {
                $status = $settings->edit_mail_settings( $_POST );
                //Flash message 
                Message::success( __( 'update_mail_settings_flash' ) );
                $this->request->redirect( "settings/mail_settings" );
            } else {
                $errors = $validator->errors( 'errors' );
            }
        }
        $mail_settings             = $settings->mail_settings( $id );
        $this->selected_page_title = __( "mail_settings" );
        $this->page_title          = __( 'mail_settings' );
        $view                      = View::factory( 'admin/manage_mail_settings' )->bind( 'current_uri', $id )->bind( 'action', $action )->bind( 'mail_settings', $mail_settings )->bind( 'errors', $errors )->bind( 'validator', $validator );
        $this->template->content   = $view;
    }
    /*** TWITTER LOGIN *****/
    public function action_twitterlogin()
    {
        if ( $this->session->get( 'userid' ) ) {
            echo "gfdgdfg";
            exit;
            $this->request->redirect( 'userjobs/index' );
        } else { //exit;
            $this->twitter->logged_in();
            $this->request->redirect( $this->twitter->get_auth_link() );
        }
    }
    public function action_twitter()
    {
        //Get tokens from the url oauth_token 
        $twit_tokens = arr::extract( $_GET, array(
             'oauth_token',
            'oauth_verifier',
            'denied' 
        ) );
        if ( isset( $twit_tokens['denied'] ) ) {
            echo "inside twitter";
            exit;
            $this->request->redirect( "users/login" );
        } else {
            try {
                $this->twitter->force_login( $twit_tokens['oauth_token'] );
                $this->request->redirect( 'socialnetwork/twitter_confirm?oauth_token=' . $twit_tokens['oauth_token'] . '&oauth_verifier=' . $twit_tokens['oauth_verifier'] );
            }
            catch ( Throwable $error ) {
                //throw new Exception("Twitter Error - Failed to connect");
                Message::error( __( 'twitter_error_failed_to_connect' ) );
                $this->request->redirect( '/' );
            }
        }
    }
    public function action_twitter_confirm()
    {
        $this->twitter->logged_in();
        $twit_tokens = arr::extract( $_GET, array(
             'oauth_token',
            'oauth_verifier',
            'denied' 
        ) );
        if ( isset( $twit_tokens['oauth_token'] ) && isset( $twit_tokens['oauth_verifier'] ) ) {
            $this->user   = $this->twitter->get_user();
            $name         = $this->user['name'];
            $user_details = array(
                 'username' => $this->user['screen_name'],
                'firstname' => $name 
            );
            $result       = DB::select()->from( USERS )->where( 'username', '=', $this->user['screen_name'] )->execute()->as_array();
            if ( count( $result ) == 0 ) {
                $this->session->set( 'twitter_details', array(
                     'username' => $user_details['username'],
                    'firstname' => $user_details['firstname'] 
                ) );
                $this->request->redirect( "socialnetwork/twittersignin" );
            } else {
                $this->commonmodel->insert( USER_LOGIN_DETAILS, array(
                     'userid' => $result[0]['id'],
                    'login_ip' => Request::$client_ip,
                    'user_agent' => Request::$user_agent,
                    'last_login' => $this->getCurrentTimeStamp 
                ) );
                $this->session->set( 'auction_userid', $result[0]['id'] );
                $this->session->set( 'auction_username', $this->user['screen_name'] );
                Message::success( __( "logged_in_successfully" ) );
                $this->request->redirect( "users/" );
            }
        } else {
            $this->request->redirect( "users/login" );
        }
        exit;
    }
    public function action_twittersignin()
    {
        $user_details = $this->session->get( 'twitter_details' );
        if ( empty( $user_details ) ) {
            $this->request->redirect( "/" );
        }
        $view     = View::factory( THEME_FOLDER . 'twitter_emailstep' )->bind( 'errors', $errors );
        $email    = arr::extract( $_POST, array(
             'email' 
        ) );
        $validate = $this->socialnetworkmodel->validate_email( $email );
        $submit   = $this->request->post( 'signin' );
        if ( isset( $submit ) ) {
            if ( $validate->check() ) {
                $password = Commonfunction::randomkey_generator();
                if ( isset( $user_details ) ) {
                    $insert = $this->commonmodel->insert( USERS, array(
                         'username' => $user_details['username'],
                        'firstname' => $user_details['firstname'],
                        'email' => $email['email'],
                        'password' => md5( $password ),
                        'login_type' => "T",
                        'referral_id' => Text::random(),
                        'status' => ACTIVE,
                        'created_date' => $this->getCurrentTimeStamp 
                    ) );
                    if ( $insert ) {
                        $this->session->set( 'auction_userid', $insert[0] );
                        $this->session->set( 'auction_username', $user_details['username'] );
                        $this->session->delete( 'twitter_details' );
                        $this->username         = array(
                             TO_MAIL => $email['email'],
                            USERNAME => $user_details['username'],
                            PASSWORD => $password 
                        );
                        $this->replace_variable = array_merge( $this->replace_variables, $this->username );
                        //send mail to user by defining common function variables from here
                        $mail                   = Commonfunction::get_email_template_details( TWITTER_SIGNUP, $this->replace_variable, SEND_MAIL_TRUE );
                        if ( $mail == MAIL_SUCCESS ) {
                            Message::success( __( 'registration_success_via_social', array(
                                 ':param' => 'Via Twitter',
                                ':param1' => __( 'please_check_mail' ) 
                            ) ) );
                            $this->request->redirect( 'users/' );
                        }
                        Message::success( __( 'registration_success_via_social', array(
                             ':param' => 'Via Twitter',
                            ":param1" => "" 
                        ) ) );
                        $this->request->redirect( "users/" );
                    }
                } else {
                    Message::error( __( 'error_in_registration_via_social', array(
                         ':param' => 'Via Twitter' 
                    ) ) );
                    $this->request->redirect( "users/signup" );
                }
            } else {
                $errors = $validate->errors( 'errors' );
            }
        }
        $this->template->content          = $view;
        $this->template->title            = $this->title;
        $this->template->meta_description = $this->metadescription;
        $this->template->meta_keywords    = $this->metakeywords;
    }
} // End Settings