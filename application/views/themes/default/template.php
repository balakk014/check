<?php defined('SYSPATH') OR die('No direct access allowed.'); 
$controller = Request::initial()->controller();
$action = Request::initial()->action(); 
$session = Session::instance();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">    
<meta name="robots" content="noindex,nofollow">
<link rel="shortcut icon" href="<?php echo URL_BASE."public/".UPLOADS."/favicon/".SITE_FAVICON; ?>" type="image/x-icon" />
<?php 
if(THEMEID == 2){ ?>
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo URL_BASE;?>public/frontend/landing_page/theme_1/style.css" />
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo URL_BASE;?>public/frontend/landing_page/theme_1/multi_style.css" />
<?php }else{ ?>
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo URL_BASE;?>public/frontend/landing_page/style.css" />
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo URL_BASE;?>public/frontend/landing_page/multi_style.css" />
<?php } ?>

<?php if($session->get("id") == "") { ?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/jquery-1.9.1.min.js"></script>
<?php } else { ?>
<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE;?>public/frontend/logged_in/css/font-awesome.css"/>
<?php
$lanfold = ($language != '' && $language != 'en') ? $language.'/':'';
?>
<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/frontend/logged_in/css/<?php echo $lanfold; ?>style.css"/>
<?php /*<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE;?>public/frontend/logged_in/css/<?php echo $lanfold; ?>dashboard_style.css"/> */ ?>
<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/frontend/logged_in/css/<?php echo $lanfold; ?>media_style.css"/>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/script.js"></script>  
<script type="text/javascript">
$(document).ready(function() {
	check_upcoming_trip_alert();
	$('.menu_icon').click(function() {
		$("#sidebar").toggleClass("open_sidebar");
		$('#right_side_part').toggleClass('open_sidebar');
	});
	$("#right_side_part").css({'min-height':($(".dash_details aside#sidebar").height()+'px')});
	$("aside#sidebar").css({'min-height':($("#right_side_part").height()+'px')});
});
</script>
<?php } ?>
<title><?php if($meta_title == '') { echo SITENAME;  } else {  echo $meta_title; } ?></title>        
<script>
    var language = '<?php echo $js_language; ?>';
    var SrcPath = '<?php echo URL_BASE; ?>';
</script>
<?php if($session->get("id") != "") { ?>
    <style>
        .header_outer,.bookingpage .header_outer{background:<?php echo WEBSITE_HEADER_BG; ?>}
        .footer{background:<?php echo WEBSITE_FOOTER_BG; ?>}
        aside{background:<?php echo WEBSITE_SIDEBAR_BG; ?>}
        #sidebar ul li.active{background:<?php echo WEBSITE_SIDEBAR_ACTIVE; ?>}
        .icon_18{fill:<?php echo WEBSITE_SIDEBAR_ICON; ?>}
        #sidebar ul li.active a .icon_18{fill:<?php echo WEBSITE_SIDEBAR_ICON_ACTIVE; ?>}
        .submit input[type=submit],.cancel_trp,.ride_now,.ride_later,.add_card,.promo_code input[type=button], .card_details ul li input[type=submit]{background:<?php echo WEBSITE_BUTTON_BG; ?>}
        .submit input[type=submit]:hover,.cancel_trp:hover,.ride_now:hover,.ride_later:hover,.add_card:hover,.promo_code input[type=button]:hover, .card_details ul li input[type=submit]:hover{background:<?php echo WEBSITE_BUTTON_HOVER_BG; ?>}
    </style>
<?php } ?>
<!-- End Google Tag Manager -->
 <?php if($session->get("id") == "") { ?>
<script>
function ucFirst(string) {
	return string.substring(0, 1).toUpperCase() + string.substring(1).toLowerCase();
}
</script>
<?php  } ?>

<script>
var $ = jQuery.noConflict();
jQuery.browser={};(function(){jQuery.browser.msie=false;
jQuery.browser.version=0;if(navigator.userAgent.match(/MSIE ([0-9]+)\./)){
jQuery.browser.msie=true;jQuery.browser.version=RegExp.$1;}})();
</script>
</head>
<body <?php if(isset($_SESSION['usertype'])) { $usertype = $_SESSION['usertype']; if(isset($_SESSION['id']) && ($usertype == 'passengers')) { if($user_det[0]['phone']==$user_det[0]['fb_user_id']) { ?> onload="return phone_popup();" <?php } } } ?> <?php if($_SERVER['REQUEST_URI'] !='/')  { ?>  class="body_non_bg" <?php } else { ?>class="header_bg_trans"<?php } ?>> 
<input type="hidden" name="baseurl" id="baseurl" value="<?php echo URL_BASE;?>">
<noscript>Your browser does not support JavaScript!</noscript>	
<div class="outer">
	<?php 
		
		#header
		$header_demo = USERVIEW . 'header-demo';
		if(THEMEID == 2){
			$header_demo = USERVIEW . 'header-demo_1';
		}
		
		#footer
		$footer = USERVIEW . 'footer';
		if(THEMEID == 2){
			$footer = USERVIEW . 'footer_1';
		}
		
		if($session->get("id") == "") {
			echo new View($header_demo); 
		} else {
			echo new View(USERVIEW."website_user/header_new"); 
		}
		echo $content;
		
		echo new View($footer); 
	?>
</div>

<?php 
if($session->get("id") == "") { ?>
<script type="text/javascript">
	function googleTranslateElementInit() {
		new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
	}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php } ?>
</body>
</html>
