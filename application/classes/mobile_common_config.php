<?php defined( 'SYSPATH' ) or die( "No direct script access." );
//--------------------------------------------------

$expiry = 0;

$uploads='uploads';


//DEFINE BASE URL(URL_BASE)
DEFINE('UPLOADS',$uploads);

$session     = Session::instance();
$commonmodel = Model::factory( 'commonmodel' );
# specify projection fields and check with model
$site_info_projection_array= ['app_name','email_id','app_description','notification_settings','pre_authorized_amount','web_google_map_key','web_google_geo_key','google_timezone_api_key','pagination_settings','price_settings','continuous_request_time','facebook_key','facebook_secretkey','site_country','site_state','site_city','admin_commission','tax','site_copyrights','facebook_share','twitter_share','google_share','linkedin_share','sms_enable','driver_tell_to_friend_message','referral_discount','show_map','taxi_charge','fare_calculation_type','driver_referral_setting','referral_settings','default_miles','admin_commision_setting','company_commision_setting','driver_commision_setting','user_time_zone','date_time_format','tell_to_friend_message','default_unit','skip_credit_card','cancellation_fare_setting','referral_amount','ios_google_map_key','ios_google_geo_key','android_google_key','site_logo','google_business_key','customer_android_key','expiry_date','website_language_settings','selected_language','site_default_language'];		
$result      = $commonmodel->common_site_info($site_info_projection_array);
DEFINE( 'APPLICATION_NAME', '' );
if ( $_SERVER['SERVER_PORT'] == "443" ) {
    DEFINE( 'PROTOCOL', 'https' );
} else {
    DEFINE( 'PROTOCOL', 'http' );
}

##Dynamic Language Process Starts
$db_language = explode(',', $result[0]["site_default_language"]);
$staticLanguArr = array("en"=>"english");
if(count($db_language) > 0){
    foreach($db_language as $langval){
        $DynamicLanguageArr[$langval] = $staticLanguArr[$langval];
    }
}else{
    $DynamicLanguageArr = array("en"=>"english");
}
DEFINE( "STATIC_LANGUAGE_ARRAY", $staticLanguArr );
DEFINE( "DYNAMIC_LANGUAGE_ARRAY", $DynamicLanguageArr );
##Dynamic Language Process End

DEFINE( 'URL_BASE', url::base( PROTOCOL, TRUE ) );
DEFINE( 'TEST_MODE', "T" );
DEFINE( 'LIVE_MODE', "L" );
DEFINE( 'IN_REVIEW', "R" );
DEFINE( "SELECTED_LANGUAGE", $result[0]["selected_language"] );
DEFINE( 'PRE_AUTHORIZATION_AMOUNT', $result[0]['pre_authorized_amount'] );
DEFINE( 'PRE_AUTHORIZATION_REG_AMOUNT', 0 );
DEFINE( 'PRE_AUTHORIZATION_RETRY_REG_AMOUNT', 1 );
DEFINE( 'GOOGLE_MAP_API_KEY', $result[0]['web_google_map_key'] );
DEFINE( 'GOOGLE_GEO_API_KEY', $result[0]['web_google_geo_key'] );
DEFINE( 'GOOGLE_TIMEZONE_API_KEY', "" );
DEFINE( 'IOS_GOOGLE_MAP_API_KEY', $result[0]['ios_google_map_key'] );
DEFINE( 'IOS_GOOGLE_GEO_API_KEY', $result[0]['ios_google_geo_key'] );
DEFINE( 'ANDROID_GOOGLE_GEO_API_KEY', $result[0]['android_google_key'] );
DEFINE( 'GOOGLE_BUSINESS_KEY_USED_STATUS', $result[0]['google_business_key'] );
DEFINE( 'DEFAULT_DATE_TIME_FORMAT', $result[0]['date_time_format'] );
DEFINE( 'REFERRAL_SETTINGS', $result[0]['referral_settings'] );
DEFINE( 'DRIVER_REFERRAL_SETTINGS', $result[0]['driver_referral_setting'] );
DEFINE( 'CUSTOMER_ANDROID_KEY', $result[0]['customer_android_key'] );
DEFINE( 'EXPIRY_DATE', $result[0]['expiry_date'] );
DEFINE( 'SITE_CURRENCY', '');
DEFINE( 'SITE_LOGO', $result[0]['site_logo'] );
DEFINE( 'IPINFOAPI_KEY', "" );
DEFINE( 'ENCRYPT_KEY', "ndotencript_" );
//set the headers here
header( 'Cache-Control: no-cache, no-store, must-revalidate' ); // HTTP 1.1.
header( 'Pragma: no-cache' ); // HTTP 1.0.
header( 'Expires: 0' ); // Proxies.
//View path
DEFINE( "ADMINVIEW", "admin/" );
DEFINE( "COMPANYVIEW", "company/" );
DEFINE( "MANAGERVIEW", "manager/" );
DEFINE( "VIEW_PATH", "pages/" );
DEFINE( "SCRIPTPATH", URL_BASE . "public/common/js/" );
//Status
DEFINE( "ACTIVE", "A" );
DEFINE( "INACTIVE", "I" );
DEFINE( "DELETED", "D" );
DEFINE( "SUCCESS", "S" );
DEFINE( "PENDING", "P" );
DEFINE( "SUCCESSWITHWARNING", "SW" );
DEFINE( "FAILURE", "F" );
DEFINE( "FAILUREWITHWARNING", "FW" );
DEFINE( "DELIVERED", "D" );
DEFINE( "UNDELIVERED", "U" );
DEFINE( "ADMIN", "A" );
DEFINE( "STOREADMIN", "S" );
DEFINE( "NORMALUSER", "N" );
DEFINE( "PAGE_NO", 1 );
DEFINE( "SUCESS", 1 );
DEFINE( "FAIL", 0 );
DEFINE( "PAID_PACKAGE", 6 );
DEFINE( "COMMON_ANDROID_PASSENGER_APP", '' );
DEFINE( "COMMON_IOS_PASSENGER_APP", '' );
DEFINE( "COMMON_ANDROID_DRIVER_APP", '' );
DEFINE( "LOCATION_LATI", "34.0500" );
DEFINE( "LOCATION_LONG", "-118.2500" );
DEFINE( "LOCATION_ADDR", "San Francisco" );
DEFINE( "IPADDRESS", "" );
DEFINE( "RANDOM_KEY_LENGTH", "15" );
DEFINE( "REPLACE_LOGO", "##LOGO##" );
DEFINE( "REPLACE_NAME", "##NAME##" );
DEFINE( "REPLACE_USERNAME", "##USERNAME##" );
DEFINE( "REPLACE_SHAREDUSER", "##SHAREDUSER##" );
DEFINE( "REPLACE_CONTROLLERNAME", "##CONTROLLERNAME##" );
DEFINE( "REPLACE_PASSWORD", "##PASSWORD##" );
DEFINE( "REPLACE_SUBJECT", "##SUBJECT##" );
DEFINE( "RESET_LINK", "##RESET_LINK##" );
DEFINE( "REPLACE_PHONE", "##PHONE##" );
DEFINE( "REPLACE_MESSAGE", "##MESSAGE##" );
DEFINE( "REPLACE_PROMOCODE", "##PROMOCODE##" );
DEFINE( "REPLACE_OTP", "##OTP##" );
DEFINE( "REPLACE_EMAIL", "##EMAIL##" );
DEFINE( "REPLACE_SALES_PERSON_EMAIL", "##SALES_PERSONEMAIL##" );
DEFINE( "REPLACE_MOBILE", "##MOBILE##" );
DEFINE( "REPLACE_SITENAME", "##SITENAME##" );
DEFINE( "REPLACE_DOMAINNAME", "##DOMAINNAME##" );
DEFINE( "REPLACE_EMAIL_LOGO", "##EMAILLOGO##" );
DEFINE( "REPLACE_SITELINK", "##SITELINK##" );
DEFINE( "REPLACE_SITEURL", "##SITEURL##" ); //
DEFINE( "REPLACE_ACTLINK", "##ACTLINK##" ); //
DEFINE( "REPLACE_REQMESSAGE", "##REQMESSAGE##" );
DEFINE( "REPLACE_ORDERLIST", "##ORDERS_LIST##" );
DEFINE( "REPLACE_DELIVERYADDRESS", "##DELIVERY_ADDRESS##" );
DEFINE( "REPLACE_ORDERID", "##ORDERID##" );
DEFINE( "REPLACE_SITEEMAIL", "##SITEEMAIL##" );
DEFINE( "REPLACE_NOOFTAXI", "##NOOFTAXI##" );
DEFINE( "REPLACE_COMPANY", "##COMPANY##" );
DEFINE( "REPLACE_CLIENT_NAME", "##CLIENT_NAME##" );
DEFINE( "REPLACE_COUNTRY", "##COUNTRY##" );
DEFINE( "REPLACE_CITY", "##CITY##" );
DEFINE( "CONTACT_NO", "##CONTACT_NO##" );
DEFINE( "REPLACE_PICKUP", "##PICKUP##" );
DEFINE( "REPLACE_MAPURl", "##MAPURL##" );
DEFINE( "REPLACE_DROP", "##DROP##" );
DEFINE( "REPLACE_CURRENCY", "##CURRENCY##" );
DEFINE( "REPLACE_PACKAGE", "##PACKAGE_NAME##" );
DEFINE( "REPLACE_TAXIS", "##NO_OF_TAXIS##" );
DEFINE( "REPLACE_CHARGE", "##CHARGE_PER_TAXI##" );
DEFINE( "REPLACE_AMOUNT", "##TOTAL_AMOUNT##" );
DEFINE( "REPLACE_COPYRIGHTS", "##COPYRIGHTS##" );
DEFINE( "REPLACE_COPYRIGHTYEAR", "##COPYYEAR##" );
DEFINE( "REPLACE_ANDROID_PASSENGER_APP", "##ANDROID_PASSENGER_APP##" );
DEFINE( "REPLACE_IOS_PASSENGER_APP", "##IOS_PASSENGER_APP##" );
DEFINE( "REPLACE_ANDROID_DRIVER_APP", "##ANDROID_DRIVER_APP##" );
DEFINE( 'MESSAGE', '##MESSAGE##' );
DEFINE( 'TEMPLATE_CONTENT', '##TEMPLATE_CONTENT##' );
DEFINE( 'SITE_DESCRIPTION', '##SITE_DESCRIPTION##' );
DEFINE( "REPLACE_VERIFYLINK", "##VERIFYLINK##" );
DEFINE( "REPLACE_STARTDATE", "##STARTDATE##" );
DEFINE( "REPLACE_EXPIREDATE", "##EXPIREDATE##" );
DEFINE( "REPLACE_USAGELIMIT", "##USAGELIMIT##" );
DEFINE( "IMGPATH", URL_BASE . "public/admin/images/" );
DEFINE( 'PUBLIC_FOLDER', "public/admin" );
DEFINE( 'PUBLIC_UPLOADS_FOLDER', "public/".$uploads );
DEFINE( 'PUBLIC_IMAGES_FOLDER', "public/common/images/" );
DEFINE( 'PUBLIC_UPLOAD_BANNER_FOLDER', PUBLIC_UPLOADS_FOLDER."/banners/" );
DEFINE( 'UPLOADED_FILES', "uploadfiles" );
DEFINE( 'USERS_IMGFOLDER', "users/" );
DEFINE( 'USER_IMGPATH', PUBLIC_FOLDER . '/' . UPLOADED_FILES . '/' . USERS_IMGFOLDER );
DEFINE( 'USER_IMGPATH_THUMB', USER_IMGPATH . 'thumb/' );
DEFINE( 'USER_IMGPATH_PROFILE', USER_IMGPATH . 'profile/' );
DEFINE( 'PUBLIC_FOLDER_IMGPATH', PUBLIC_FOLDER . '/' . 'images' );
DEFINE( 'PUBLIC_IMGPATH', URL_BASE . 'public/' . 'images' );
DEFINE( "TEMPLATEPATH", "public/common/emailtemplate/" );
DEFINE( "REPLACE_TAXINO", "##TAXINO##" );
DEFINE( 'SITE_LOGOIMG', "site_logo" );
DEFINE( 'BANNER_IMG', "banners/" );
DEFINE( 'LOGOPATH', PUBLIC_FOLDER . '/images/' );
DEFINE( 'SITE_LOGO_IMGPATH', PUBLIC_UPLOADS_FOLDER . '/' . SITE_LOGOIMG );
DEFINE( 'BANNER_IMGPATH', PUBLIC_UPLOADS_FOLDER . '/' . BANNER_IMG );
DEFINE( 'TAXI_IMG', "taxi_image/" );
DEFINE( 'COMPANY_IMG', "company/" );
DEFINE( 'TAXI_IMG_IMGPATH', PUBLIC_UPLOADS_FOLDER . '/' . TAXI_IMG );
DEFINE( 'COMPANY_IMG_IMGPATH', PUBLIC_UPLOADS_FOLDER . '/' . COMPANY_IMG );
DEFINE( 'PASS_IMG', "passenger/" );
DEFINE( 'PASS_IMG_IMGPATH', PUBLIC_UPLOADS_FOLDER . '/' . PASS_IMG );
DEFINE( 'TAXI_IMG_WIDTH', 340 );
DEFINE( 'TAXI_IMG_HEIGHT', 260 ); //
DEFINE( 'TAXI_APP_THMB32_IMG_WIDTH', 32 );
DEFINE( 'TAXI_APP_THMB32_IMG_HEIGHT', 32 );
DEFINE( 'TAXI_APP_THMB100_IMG_WIDTH', 100 );
DEFINE( 'TAXI_APP_THMB100_IMG_HEIGHT', 100 );
DEFINE( 'COMPANY_IMG_WIDTH', 32 );
DEFINE( 'COMPANY_IMG_HEIGHT', 32 );
DEFINE( 'PASS_IMG_WIDTH', 140 );
DEFINE( 'PASS_IMG_HEIGHT', 140 );
DEFINE( 'DRIVER_DOC_IMG_WIDTH', 500 );
DEFINE( 'DRIVER_DOC_IMG_HEIGHT', 500 );
DEFINE( 'PASS_THUMBIMG_WIDTH', 80 );
DEFINE( 'PASS_THUMBIMG_HEIGHT', 80 );
DEFINE( 'PASS_THUMBIMG_WIDTH1', 100 );
DEFINE( 'PASS_THUMBIMG_HEIGHT1', 100 );
DEFINE( 'SITE_LOGO_WIDTH', 155 );
DEFINE( 'SITE_LOGO_HEIGHT', 35 );
DEFINE( 'BANNER_SLIDER_WIDTH', 1600 );
DEFINE( 'BANNER_SLIDER_HEIGHT', 557 );
DEFINE( 'FAVICON_IMG', "favicon/" );
DEFINE( 'SITE_FAVICON_IMGPATH', PUBLIC_UPLOADS_FOLDER . '/' . FAVICON_IMG );
DEFINE( 'FAVICON_WIDTH', 16 );
DEFINE( 'FAVICON_HEIGHT', 16 );
DEFINE( 'DRIVER_IMG', "driver_image/" );
DEFINE( 'SITE_DRIVER_IMGPATH', PUBLIC_UPLOADS_FOLDER . '/' . DRIVER_IMG );
DEFINE( 'WITHDRAW_IMG_PATH', PUBLIC_UPLOADS_FOLDER . '/withdraw_request_attachements/' );
DEFINE( 'MOBILE_COMPLETE_TRIP_MAP_IMG_PATH', PUBLIC_UPLOADS_FOLDER . '/complete_trip_map/' );
DEFINE( 'MOBILE_TRIP_DETAIL_MAP_IMG_PATH', PUBLIC_UPLOADS_FOLDER . '/trip_detail_map/' );
DEFINE( 'MOBILE_PENDING_TRIP_MAP_IMG_PATH', PUBLIC_UPLOADS_FOLDER . '/pending_trip_map/' );
DEFINE( 'MOBILE_FAV_LOC_MAP_IMG_PATH', PUBLIC_UPLOADS_FOLDER . '/favourite_location_map/' );
DEFINE( 'MOBILE_iOS_IMAGES_FILES', PUBLIC_UPLOADS_FOLDER . '/iOS/' );
DEFINE( 'MOBILE_ANDROID_IMAGES_FILES', PUBLIC_UPLOADS_FOLDER . '/android/' );
//Taxi Dispatch
DEFINE( 'BOOTSTRAP_IMGPATH', URL_BASE . 'public/dispatch/vendor/bootstrap/images' );
DEFINE( 'TAXI_DISPATCH', "admin/taxi_dispatch/" );
DEFINE( 'DATATABLE_CSSPATH', URL_BASE . "public/dispatch/vendor/Datatable/css/" );
DEFINE( 'DATATABLE_JSPATH', URL_BASE . "public/dispatch/vendor/Datatable/js/" );
DEFINE( 'TDISPATCH_VIEW', 1 ); //1-Show , 0-Hide
DEFINE( 'MAP_COUNTRY', 'IND' );
DEFINE( 'REC_PER_PAGE', $result[0]['pagination_settings'] );
DEFINE( 'FARE_SETTINGS', $result[0]['price_settings'] );
DEFINE( 'ADMIN_NOTIFICATION_TIME', $result[0]['notification_settings'] );
DEFINE( 'CONTINOUS_REQUEST_TIME', $result[0]['continuous_request_time'] );
DEFINE( 'FB_KEY', $result[0]['facebook_key'] );
DEFINE( 'DEFAULT_COUNTRY', $result[0]['site_country'] );
DEFINE( 'DEFAULT_STATE', $result[0]['site_state'] );
DEFINE( 'DEFAULT_CITY', $result[0]['site_city'] );
DEFINE( 'APP_DESCRIPTION', $result[0]['app_description'] );
DEFINE( 'TOTAL_RATING', 5 );
DEFINE( 'REMINDER_TIME', 1 );
DEFINE( 'ADMIN_COMMISSON', $result[0]['admin_commission'] );
DEFINE( 'TAX', $result[0]['tax'] );
DEFINE( 'MIN_FUND', "" );
DEFINE( 'MAX_FUND', "" );
DEFINE( 'SITE_NAME', $result[0]['app_name'] );
DEFINE( 'SITE_COPYRIGHT', $result[0]['site_copyrights'] );
DEFINE( 'FB_SHARE', $result[0]['facebook_share'] );
DEFINE( 'FB_SECRET_KEY', $result[0]['facebook_secretkey'] );
DEFINE( 'TW_SHARE', $result[0]['twitter_share'] );
DEFINE( 'GOOGLE_SHARE', $result[0]['google_share'] );
DEFINE( 'LINKEDIN_SHARE', $result[0]['linkedin_share'] );
DEFINE( 'SMS', $result[0]['sms_enable'] );
DEFINE( 'DRIVER_TELL_TO_FRIEND_MESSAGE', $result[0]['driver_tell_to_friend_message'] );
DEFINE( 'REFERRAL_DISCOUNT', $result[0]['referral_discount'] );
DEFINE( 'SITE_EMAIL_CONTACT', $result[0]['email_id'] );
DEFINE( 'SHOW_MAP', $result[0]['show_map'] );
DEFINE( 'TAXI_CHARGE', $result[0]['taxi_charge'] );
DEFINE( 'SITE_FARE_CALCULATION_TYPE', $result[0]['fare_calculation_type'] );
DEFINE( 'BOOK_BY_PASSENGER', 1 );
DEFINE( 'BOOK_BY_CONTROLLER', 2 );
DEFINE( 'REPLACE_COMPANYNAME', '##COMPANYNAME##' );
DEFINE( 'REPLACE_COMPANYDOMAIN', '##COMPANYDOMAIN##' );
DEFINE( 'LOCATIONUPDATESECONDS', 15 );
DEFINE( 'DEFAULTMILE', $result[0]['default_miles'] );
DEFINE( 'DEFAULT_DRIVER_MILE', 3 );
DEFINE( 'TRAILEXPIRY', 10 );
DEFINE( 'DEFAULT_CONNECTION', 'default' );
DEFINE( 'ADMIN_COMMISION_SETTING', $result[0]['admin_commision_setting'] );
DEFINE( 'COMPANY_COMMISION_SETTING', $result[0]['company_commision_setting'] );
DEFINE( 'DRIVER_COMMISION_SETTING', $result[0]['driver_commision_setting'] );
//DEFINE("EMAILTEMPLATELOGO",URL_BASE.PUBLIC_FOLDER_IMGPATH.'/logo.png');
//print_r($smtp_result[0]);
DEFINE( "SMTP", 1 );
DEFINE( "NIGHT_FROM", "20:00:00" );
DEFINE( "NIGHT_TO", "05:59:59" );
DEFINE( "EVENING_FROM", "16:00:00" );
DEFINE( "EVENING_TO", "19:59:59" );
DEFINE( "SAR_EQUAL_USD", "0.27" );
DEFINE( 'REPLACE_TRIPDETAILS', '##TRIP_DETAILS##' );

DEFINE( "SUB_DOMAIN_NAME", '' );
DEFINE( "DOMAIN_NAME", '' );
DEFINE( "DOMAIN_URL_NAME", '' );
$dynamicTimeZone = isset( $result[0]["user_time_zone"] ) ? $result[0]["user_time_zone"] : 'Asia/Kolkata';
DEFINE( "COMPANY_CID", 0 );
DEFINE( "TIMEZONE", $dynamicTimeZone );
DEFINE( "USER_SELECTED_TIMEZONE", $dynamicTimeZone );
DEFINE( 'CHECK_EXPAIRY', $expiry );
$currentTime = convert_timezone( 'now', $dynamicTimeZone );
$cTsatmp     = strtotime( $currentTime );
$expiryTime  = isset( $result[0]["expiry_date"] ) ? strtotime( Commonfunction::convertphpdate( 'Y-m-d H:i:s', $result[0]["expiry_date"] ) ) : '';

if ( !empty( $expiryTime ) && $expiryTime < $cTsatmp && CHECK_EXPAIRY != 0 ) {
    require Kohana::find_file('classes/controller', 'ndotcrypt');
    $headers = apache_request_headers();  
    $mobile_data_ndot_crypt=new NDOT_MCrypt();
    $mobile_decryptdata='';
    $additional_param=[];
    
    $message = array(
         "message" => "Your host has been expired",
        "status" => 2 
    );
    if(isset($headers['Authorization'])){  
         $additional_param['header_authorization']=$headers['Authorization'];
        
        
    }
    $mobile_data_ndot_crypt->encrypt_encode_json($message, $additional_param);
    exit;
}
/*if ( $session->get( 'userid' ) != "" || $session->get( 'id' ) != "" ) {
    $default_currency = $commonmodel->common_currency_details(  );
} else {*/
    $default_currency = $commonmodel->common_currency_details();
//}
DEFINE( 'CURRENCY', $default_currency[0]['currency_symbol'] );
DEFINE( 'CURRENCY_SYMB', $default_currency[0]['currency_symbol'] );
DEFINE( 'CURRENCY_FORMAT', $default_currency[0]['currency_code'] );
$email_logo = URL_BASE . SITE_LOGO_IMGPATH . '/site_email_logo.png';
DEFINE( "EMAIL_TEMPLATE_LOGO", $email_logo );
DEFINE( "EMAILTEMPLATELOGO", $email_logo );
DEFINE( "COMPANY_SITENAME", SITE_NAME );
DEFINE( "COMPANY_NOTIFICATION_TIME", 60 );
DEFINE( "COMPANY_CUSTOMER_APP_URL", '' );
DEFINE( "COMPANY_DRIVER_APP_URL", '' );
DEFINE( 'TELL_TO_FRIEND_MESSAGE', $result[0]['tell_to_friend_message'] );
DEFINE( "DEFAULT_UNIT", $result[0]['default_unit'] );
DEFINE( "DEFAULT_SKIP_CREDIT_CARD", $result[0]['skip_credit_card'] );
DEFINE( "COMPANY_CURRENCY", CURRENCY );
DEFINE( "CANCELLATION_FARE", $result[0]['cancellation_fare_setting'] );
DEFINE( "COMPANY_COPYRIGHT", SITE_COPYRIGHT );
DEFINE( "CONTACT_EMAIL", SITE_EMAIL_CONTACT );
DEFINE( "COPYRIGHT_YEAR", "2016" );
/**************** Convert the Timezone *********/
function convert_timezone( $time, $timezone = '' )
{
    $date      = new DateTime( $time, new DateTimeZone( $timezone ) );
    $localtime = $date->format( 'Y-m-d H:i:s' );
    return $localtime;
}
// FUNCTION FOR CURRENCY CONVERSION
function currency_conversion( $company_currency, $amt )
{
    try {
        return $amt;
    }
    catch ( Kohana_Exception $e ) {
        Message::error( __( 'currency_converstion_not_applicable' ) );
        $location = URL_BASE . '/admin/dashboard';
        header( "Location: $location" );
    }
}
// FUNCTION FOR CURRENCY CONVERSION to USD
function currency_conversion_usd( $company_currency, $amt )
{
    try {
        return $amt;
    }
    catch ( Kohana_Exception $e ) {
        $converted_amt = SAR_EQUAL_USD * $amt;
        return $converted_amt;
    }
}
//ENCRIPTION AND DECRIPTION FUNCTION
function encrypt_decrypt( $action, $string )
{
    $output = false;
    $key    = 'Taxi Application';
    // initialization vector 
    $iv     = md5( md5( $key ) );
    if ( $action == 'encrypt' ) {
        $output = base64_encode( ENCRYPT_KEY . $string );
    } else if ( $action == 'decrypt' ) {
        $decrypt_val = base64_decode( $string );
        $split       = explode( '_', $decrypt_val );
        if ( count( $split ) > 1 ) {
            $output = trim( $split[1] );
        } else {
            $output = "";
        }
    }
    return $output;
}
// Repeat X function
function repeatx( $data, $repeatstring, $repeatcount )
{
    if ( $data != "" ) {
        if ( $repeatcount == 'All' ) {
            return str_repeat( $repeatstring, ( strlen( $data ) ) );
        } else {
            return str_repeat( $repeatstring, ( strlen( $data ) - $repeatcount ) ) . substr( $data, -$repeatcount, $repeatcount );
        }
    } else {
        return 0;
    }
}
DEFINE( "SENDGRID_HOST", '' );
DEFINE( "SENDGRID_PORT", '' );
DEFINE( "SENDGRID_USERNAME", '' );
DEFINE( "SENDGRID_PASSWORD", '' );
/*** Language setting for mobile application*****/
$lang = ( isset( $_GET ) ) ? ( Arr::get( $_GET, 'lang' ) ) : 'en';
/******** Language Selection ************/

        
$web_language = $result[0]['website_language_settings'];
DEFINE( 'WEB_DB_LANGUAGE',$web_language);
$domain = defined('SUB_DOMAIN_TEMP') ? SUB_DOMAIN_TEMP : UPLOADS;
$session_lang = $this->session->get( $domain.'lang' );
$session_lang = ($session_lang!="")?$session_lang:SELECTED_LANGUAGE;
$default_customize = isset($web_language[$session_lang])?$web_language[$session_lang]:1;
DEFINE( 'LANG_INFO',$default_customize);
DEFINE( "LANG", $session_lang.'def' );
/***********************************************/
DEFINE( "SUPERADMIN_EMAIL", "" );
DEFINE( 'UNIT', DEFAULT_UNIT );
if ( UNIT == 1 ) {
    DEFINE( 'UNIT_NAME', 'MILES' );
} else {
    DEFINE( 'UNIT_NAME', 'KM' );
}
if ( COMPANY_CID != 0 ) {
    DEFINE( 'FARE_CALCULATION_TYPE', COMPANY_FARE_CALCULATION_TYPE ); //1 => Distance, 2 => Time, 3=> Distance / Time
} else {
    DEFINE( 'FARE_CALCULATION_TYPE', SITE_FARE_CALCULATION_TYPE ); //1 => Distance, 2 => Time, 3=> Distance / Time
}
DEFINE( 'SKIP_CREDIT_CARD', DEFAULT_SKIP_CREDIT_CARD ); // 1 as Skip , 0 as No-Skip
/****Tell to friend-App URL Display by PAID VERSION Start****/
DEFINE( "ANDROID_PASSENGER_APP", COMMON_ANDROID_PASSENGER_APP );
DEFINE( "IOS_PASSENGER_APP", COMMON_IOS_PASSENGER_APP );
DEFINE( "ANDROID_DRIVER_APP", COMMON_ANDROID_DRIVER_APP );
/**** Driver tracking DB initializing****/
DEFINE( 'DRIVER_TRACK_DB', 'driver_tracking' ); //Access Database 2

/****** payment Gateway CUrl Process url *******/
DEFINE( 'CRM_PAYMENT_AUTHENTICATION_ACCESSKEY','');
DEFINE( 'CRM_UPDATE_ENABLE',0);
?>
