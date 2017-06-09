<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title><?php echo "Your Current plan has expired. please pick a plan";//echo __('plan_expired_message'); ?></title>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="title" content="About Us | Taxi Mobility" />
<meta name="keywords" content="taxi app, about us." />
<meta name="description" content="Taxi Mobility dispatch system is a sincere effort to reduce public inconvenience in booking taxi and helping taxi companies with an intelligent taxi dispatch software." />

<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

<link rel="shortcut icon" href="<?php echo URL_BASE; ?>public/uploads/favicon/<?php echo SITE_FAVICON;?>" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/media_style.css" />   
</head>

<body class="body_non_bg">
    <div class="expire_page">
        <div class="expire_inner">
            <div class="expire_page_img_block">
                <div class="expire_page_img"><img src="<?php echo URL_BASE; ?>public/common/images/coming_soon.png"/></div>
            </div>
            <div class="expire_page_info_block">
                <p class="currently_unavailable"><i><svg version="1.1" id="warning" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 529.337 529.337" style="enable-background:new 0 0 529.337 529.337;" xml:space="preserve">
	<g>
		<path d="M279.438,41.827c-3.117-5.383-8.783-8.5-14.733-8.5c-5.95,0-11.617,3.117-14.733,8.5L2.338,470.51
			c-3.117,5.383-3.117,11.617,0,17c3.117,5.383,8.783,8.5,14.733,8.5h495.267l0,0c9.35,0,17-7.65,17-17
			c0-3.683-1.133-6.8-2.833-9.633L279.438,41.827z M46.538,462.01L264.704,84.327L482.871,462.01H46.538z"/>
		<path d="M247.421,201.344v130.333c0,9.35,7.65,17,17,17s17-7.65,17-17V201.344c0-9.35-7.65-17-17-17
			S247.421,191.994,247.421,201.344z"/>
		<path d="M264.421,371.344c-9.35,0-17,7.65-17,17v11.333c0,9.35,7.65,17,17,17s17-7.65,17-17v-11.333
			C281.421,378.994,273.771,371.344,264.421,371.344z"/>
	</g>

</svg></i><a href="#"><?php echo $_SERVER['SERVER_NAME'];?> </a>is currently unavailable</p>
                <h1 class="what_do">What can I do?</h1>
<!--                <div class="visitor_store">
                    <p>If you're a visitor to this store</p>
                    <span>Please try again later</span>
                </div>-->
                <div class="owner_store">
                    <i><svg version="1.1" id="taxi_img" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 214.355 214.355" style="enable-background:new 0 0 214.355 214.355;" xml:space="preserve">
<g>
	<path d="M185.536,93.624l-8.534-42.235c-2.969-14.698-16.007-25.367-31.003-25.367h-14.338V11.678c0-3.313-2.687-6-6-6H88.694
		c-3.313,0-6,2.687-6,6v14.344H68.356c-14.996,0-28.034,10.668-31.003,25.366l-8.534,42.236C12.592,95.685,0,109.569,0,126.348
		v39.209c0,3.313,2.687,6,6,6h9.902v19.723c0,9.593,7.805,17.397,17.397,17.397h2.146c9.593,0,17.396-7.805,17.396-17.397v-19.723
		h108.67v19.723c0,9.593,7.804,17.397,17.396,17.397h2.146c9.593,0,17.397-7.805,17.397-17.397v-19.723h9.902c3.313,0,6-2.687,6-6
		v-39.209C214.355,109.569,201.764,95.685,185.536,93.624z M48.481,56.9h117.392l7.365,36.45H41.117L48.481,56.9z M94.694,17.678
		h24.967v8.344H94.694V17.678z M68.356,38.021h77.643c5.873,0,11.257,2.641,14.898,6.878H53.458
		C57.099,40.662,62.483,38.021,68.356,38.021z M40.843,191.28c0,2.976-2.421,5.397-5.396,5.397H33.3
		c-2.977,0-5.397-2.421-5.397-5.397v-19.723h12.94V191.28z M186.453,191.28c0,2.976-2.421,5.397-5.397,5.397h-2.146
		c-2.976,0-5.396-2.421-5.396-5.397v-19.723h12.94V191.28z M202.355,159.558H12v-33.209c0-11.579,9.42-20.999,20.999-20.999h148.357
		c11.579,0,20.999,9.42,20.999,20.999V159.558z"/>
	<path d="M44.339,116.599c-8.743,0-15.855,7.112-15.855,15.855s7.112,15.855,15.855,15.855c8.742,0,15.854-7.112,15.854-15.855
		S53.081,116.599,44.339,116.599z M44.339,136.309c-2.126,0-3.855-1.729-3.855-3.855s1.729-3.855,3.855-3.855
		c2.125,0,3.854,1.729,3.854,3.855S46.464,136.309,44.339,136.309z"/>
	<path d="M170.017,116.599c-8.742,0-15.854,7.112-15.854,15.855s7.112,15.855,15.854,15.855c8.743,0,15.855-7.112,15.855-15.855
		S178.76,116.599,170.017,116.599z M170.017,136.309c-2.125,0-3.854-1.729-3.854-3.855s1.729-3.855,3.854-3.855
		c2.126,0,3.855,1.729,3.855,3.855S172.143,136.309,170.017,136.309z"/>
</g>

</svg></i>
                    <p><?php echo "If you're the owner of this taxi company";//__('if_owner');?></p>
                    <span>Please <a href="<?php echo URL_BASE;?>admin/login" title="sign">sign</a> in to upgrade your account, or <a href="<?php echo CONTACT_URL;?>" title="contact support" target="_blank">contact support.</a></span>
                </div>
            </div>
        </div>
    </div>
</body>
