<?php defined('SYSPATH') or die('No direct script access.');
// -- Environment setup --------------------------------------------------------
$split = explode('/',$_SERVER['REQUEST_URI']);

// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
//date_default_timezone_set('Asia/Calcutta');
date_default_timezone_set('Asia/Kolkata');
//mysql_query("SET `time_zone` = '".date('P')."'");
/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

ini_set('display_errors', 1);
ini_set ('gd.jpeg_ignore_warning', 1);
// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');
Cookie::$salt = 'Taxi';

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}
//to set kohana environment (four environments - PRODUCTION, DEVELOPMENT, STAGING, TESTING)
Kohana::$environment = isset($_SERVER['KOHANA_ENV'])
    ? constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']))
    : Kohana::DEVELOPMENT;
    
/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
    'base_url'      => '/',
    'index_file'    => FALSE,
    'errors'        => TRUE,
    'profile'       => (Kohana::$environment == Kohana::DEVELOPMENT),
    'caching'       => (Kohana::$environment == Kohana::PRODUCTION)
));
set_exception_handler(array('Kohana_Exception', 'handler'));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
		'image'      		=> MODPATH.'image',      // Image manipulation
		'taximobility'          => MODPATH.'taximobility',      
		'commonfunction'  	=> MODPATH.'commonfunction', //common function added as module		  
		'Message' 		=> MODPATH.'message',		  
		'Email' 		=> MODPATH.'email',
		'pagination' 		=> MODPATH.'pagination',// Pagination
		//'debugtoolbar'	=> MODPATH.'debugtoolbar',// debug toolbar
		'mongoDB' 		=> MODPATH.'mangodb', 	// mongoDB
		//'phpex' 		=> MODPATH.'phpex', 	// phpexcel
		'braintree' 		=> MODPATH.'braintree',// braintree
		'paypal' 		=> MODPATH.'paypal', 	// paypal
		'authorize' 		=> MODPATH.'authorize',// authorize
		'stripe' 		=> MODPATH.'stripe', 	// stripe
		'paymentgateway' 	=> MODPATH.'paymentgateway',//connect with application & payment gateway 		          
	));

/** 
 * Error router 
 */


/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

//Include defined Constants files


Route::set('error', 'error/<action>/<origuri>/<message>', array('action' => '[0-9]++', 'origuri' => '.+', 'message' => '.+'))
->defaults(array(
    'controller' => 'error',
    'action'     => 'index'
));

Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'users',
		'action'     => 'index',
	));

Route::set('admin', '(<controller>(/<action>(/<id>/<method>)))')
	->defaults(array(
		'controller' => 'admin',
		'action'     => 'index',
		'method'     => NULL,
	));
	
Route::set('edit', '(<controller>(/<action>(/<id>/<method>/<lparam>)))')
	->defaults(array(
		'controller' => 'admin',
		'action'     => 'index',
		'method'     => NULL,
		'lparam'     => NULL,
	));

Route::set('privacy--policy', 'privacypolicy.html')
    ->defaults(array(
        'controller' => 'page',
        'action'     => 'privacy_policy'
    )); 
    
Route::set('terms--conditions', 'termsconditions.html')
    ->defaults(array(
        'controller' => 'page',
        'action'     => 'terms_conditions'
    )); 
  
Route::set('cms', 'cms/(<pageurl>).html',array( 'pageurl' => '[a-zA-Z0-9\-]+'))
	->defaults(array(
		'controller' => 'page',
		'action'     => 'common_cms',
	));
	
Route::set('webbooking', 'booking.html')
    ->defaults(array(
        'controller' => 'find',
        'action'     => 'webBooking'
    ));

Route::set('online-booking', 'addmoney.html')
	->defaults(array(
		'controller' => 'users',
		'action'     => 'add_money'
));

Route::set('change-password', 'change-password.html')
	->defaults(array(
		'controller' => 'users',
		'action'     => 'change_password'
));

Route::set('edit-profile', 'edit-profile.html')
	->defaults(array(
		'controller' => 'users',
		'action'     => 'passenger_editprofile'
));

Route::set('cancelled-trips', 'cancelled-trips.html')
	->defaults(array(
		'controller' => 'users',
		'action'     => 'cancelled_trips'
));

Route::set('completed-trips', 'completed-trips.html')
	->defaults(array(
		'controller' => 'users',
		'action'     => 'completed_trips'
));

Route::set('dashboard', 'dashboard.html')
	->defaults(array(
		'controller' => 'users',
		'action'     => 'passenger_dashboard'
));

Route::set('payment-option', 'payment-option.html')
	->defaults(array(
		'controller' => 'users',
		'action'     => 'payment_option'
));

Route::set('add-card', 'add-card.html')
	->defaults(array(
		'controller' => 'users',
		'action'     => 'add_card_details'
));
//Include defined Constants files
require Kohana::find_file('classes','table_config');
$current_controller = (count($split) > 1 && $split[1])?$split[1]:"";
//$ctrl = substr($current_controller, 0, -3);
$pos = strpos($current_controller, 'mobileapi');
