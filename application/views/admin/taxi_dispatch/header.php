<link rel="stylesheet" href="<?php echo URL_BASE;?>public/dispatch/vendor/bootstrap/css/bootstrap.css"/>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/dispatch/vendor/bootstrap/css/style.css"/>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/dispatch/vendor/bootstrap/css/simple-sidebar.css"/>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/dispatch/dist/css/formValidation.css"/>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/dispatch/vendor/bootstrap/css/bootstrap-datetimepicker.css"/>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/dispatch/vendor/bootstrap/css/media_style.css"/>
<style>
    .taxi_dispatcher_inner .top_row{background:<?php echo DISPATCH_FOOTER_BG; ?>}
    .sticky_message{background:<?php echo ADMIN_FOOTER_BG; ?>}
    .main-sidebar, .main_header .logo{background:<?php echo ADMIN_SIDEBAR_BG; ?>}
    .menu_drop_down,ul.menu_drop_down li ul.menu_drop_down{background:<?php echo ADMIN_SIDEBAR_SUBTAB; ?>}
    .icon_24,.icon_22,.icon_20{fill:<?php echo ADMIN_SIDEBAR_ICON; ?>}
    .menu > ul > li:hover > a .icon_24, .menu > ul > li.active > a .icon_24,.head_leftmenu a.head_dash.active .icon_22, 
    .head_leftmenu a.head_msgbook.active .icon_22,.head_leftmenu a.head_setting.active .icon_22{fill:<?php echo ADMIN_SIDEBAR_ICON_ACTIVE; ?>}
    .menu > ul > li.active > a i, .menu > ul > li:hover > a i{background:<?php echo ADMIN_SIDEBAR_ICON_CIRCLE; ?>}
    .menu > ul > li.active{background:<?php echo ADMIN_SIDEBAR_ACTIVE; ?>}
    #submit_filter,#reset_date,.container.taxi_dispatcher .form-group .btn-primary,#add_booking_tab{background:<?php echo DISPATCH_BUTTON_BG; ?>}
    #submit_filter:hover, #reset_date:hover,.container.taxi_dispatcher .form-group .btn-primary:hover,#add_booking_tab:hover{color:#fff;background:<?php echo DISPATCH_BUTTON_HOVER_BG; ?>}
    
</style>
<div class="svg_icons" style="display: none;">
    
    <svg version="1.1" id="dashboard" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 24 24" enable-background="new 0 0 24 23" xml:space="preserve">
<g><path d="M12.417,14.516c-0.576,0-1.047-0.465-1.084-1.055L7.511,8.522l4.906,3.746c0.597,0,1.081,0.502,1.081,1.123
			C13.498,14.01,13.014,14.516,12.417,14.516L12.417,14.516z M21.973,19.391h-1.631c0.008-0.014,0.021-0.029,0.026-0.041
			l-2.022-2.104l1.076-1.121l1.82,1.889c0.914-1.633,1.446-3.523,1.446-5.553c0-0.098-0.014-0.186-0.017-0.283H20.2v-1.583h2.322
			c-0.306-1.862-1.047-3.562-2.125-4.982l-1.687,1.749l-1.078-1.121l1.736-1.804c-1.805-1.789-4.209-2.919-6.871-3.046v2.873
			l-1.526,0.002V1.417C8.59,1.654,6.443,2.708,4.776,4.3l1.699,1.762L5.396,7.183L3.721,5.447c-1.144,1.452-1.928,3.214-2.244,5.145
			H3.71v1.586H1.328c-0.003,0.098-0.013,0.186-0.013,0.283c0,2.037,0.537,3.938,1.457,5.576l1.938-1.76l1.005,1.195l-2.081,1.887
			c0.008,0.008,0.016,0.02,0.024,0.031H3.6l-0.042,0.035l-0.032-0.035H2.027c-1.278-1.982-2.025-4.365-2.025-6.93
			c0-6.883,5.371-12.459,11.997-12.459c6.628,0,12.001,5.576,12,12.459C23.998,15.025,23.251,17.408,21.973,19.391L21.973,19.391z
			 M6.546,22.998v-2.922h10.811v2.922H6.546L6.546,22.998z"/>

</g>
</svg>

<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="booking" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve" >
<g>
    <path d="M397.736,78.378c6.824,0,12.358-5.533,12.358-12.358V27.027C410.094,12.125,397.977,0,383.08,0H121.641    c-3.277,0-6.42,1.303-8.739,3.62L10.527,105.995c-2.317,2.317-3.62,5.461-3.62,8.738v370.239C6.908,499.875,19.032,512,33.935,512    h349.144c14.897,0,27.014-12.125,27.014-27.027V296.289c0.001-6.824-5.532-12.358-12.357-12.358    c-6.824,0-12.358,5.533-12.358,12.358v188.684c0,1.274-1.031,2.311-2.297,2.311H33.936c-1.274,0-2.311-1.037-2.311-2.311v-357.88    h75.36c14.898,0,27.016-12.12,27.016-27.017V24.716H383.08c1.267,0,2.297,1.037,2.297,2.311V66.02    C385.377,72.845,390.911,78.378,397.736,78.378z M109.285,100.075c0,1.269-1.032,2.301-2.3,2.301H49.107l60.178-60.18V100.075z"/>
    <path d="M492.865,100.396l-14.541-14.539c-16.304-16.304-42.832-16.302-59.138,0L303.763,201.28H103.559    c-6.825,0-12.358,5.533-12.358,12.358c0,6.825,5.533,12.358,12.358,12.358h175.488l-74.379,74.379H103.559    c-6.825,0-12.358,5.533-12.358,12.358s5.533,12.358,12.358,12.358h76.392l-0.199,0.199c-1.508,1.508-2.598,3.379-3.169,5.433    l-19.088,68.747h-53.936c-6.825,0-12.358,5.533-12.358,12.358s5.533,12.358,12.358,12.358h63.332c0.001,0,2.709-0.306,3.107-0.41    c0.065-0.017,77.997-21.642,77.997-21.642c2.054-0.57,3.926-1.662,5.433-3.169l239.438-239.435    C509.168,143.228,509.168,116.7,492.865,100.396z M184.644,394.073l10.087-36.326l26.24,26.24L184.644,394.073z M244.69,372.752    l-38.721-38.721l197.648-197.648l38.722,38.721L244.69,372.752z M475.387,142.054l-15.571,15.571l-38.722-38.722l15.571-15.571    c6.669-6.668,17.517-6.667,24.181,0l14.541,14.541C482.054,124.54,482.054,135.388,475.387,142.054z" />
</g>
</svg>

<svg version="1.1" id="setting" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
<g>
		<path d="M23.622,13.994l-1.461,0.227c-0.822,0.125-1.512,0.656-1.846,1.422c-0.334,0.764-0.252,1.631,0.213,2.32
			l0.795,1.174c0.121,0.176,0.098,0.412-0.053,0.563l-1.887,1.885c-0.154,0.156-0.398,0.178-0.576,0.045l-0.838-0.611
			c-0.688-0.506-1.568-0.611-2.355-0.285c-0.785,0.326-1.332,1.021-1.461,1.865l-0.158,1.025C13.96,23.842,13.774,24,13.556,24
			h-2.667c-0.213,0-0.397-0.15-0.437-0.361l-0.269-1.389c-0.158-0.818-0.713-1.488-1.491-1.793c-0.773-0.305-1.639-0.193-2.31,0.299
			l-1.191,0.873c-0.179,0.133-0.423,0.111-0.576-0.045l-1.886-1.883c-0.153-0.152-0.174-0.389-0.055-0.564l0.794-1.172
			c0.469-0.691,0.547-1.559,0.216-2.322c-0.334-0.766-1.025-1.297-1.849-1.422l-1.459-0.227c-0.216-0.031-0.376-0.219-0.376-0.438
			V10.89c0-0.213,0.153-0.397,0.36-0.437l1.389-0.268c0.82-0.155,1.491-0.713,1.794-1.489c0.305-0.779,0.192-1.642-0.3-2.313
			L2.369,5.193C2.24,5.017,2.259,4.769,2.414,4.614L4.3,2.73c0.15-0.152,0.387-0.173,0.563-0.055l1.515,1.026
			c0.674,0.458,1.528,0.544,2.281,0.231c0.752-0.311,1.294-0.976,1.449-1.775l0.345-1.796C10.491,0.15,10.675,0,10.889,0h2.667
			c0.219,0,0.404,0.16,0.439,0.376l0.223,1.46c0.127,0.823,0.658,1.515,1.424,1.846c0.762,0.334,1.631,0.255,2.322-0.213l1.17-0.794
			c0.178-0.118,0.414-0.097,0.566,0.053l1.883,1.886c0.156,0.155,0.176,0.403,0.045,0.579l-0.873,1.188
			c-0.492,0.673-0.605,1.536-0.301,2.312c0.307,0.779,0.977,1.333,1.795,1.492l1.389,0.268c0.211,0.04,0.363,0.224,0.363,0.437
			v2.667C24.001,13.775,23.841,13.963,23.622,13.994L23.622,13.994z M23.112,11.255l-1.031-0.197
			c-1.119-0.215-2.037-0.976-2.451-2.039c-0.42-1.06-0.264-2.244,0.41-3.162l0.646-0.884l-1.357-1.357l-0.867,0.589
			c-0.945,0.639-2.131,0.75-3.176,0.292c-1.045-0.455-1.773-1.402-1.947-2.528L13.175,0.89h-1.92l-0.273,1.434
			c-0.213,1.097-0.953,2.004-1.983,2.43c-1.028,0.429-2.196,0.308-3.12-0.315l-1.21-0.821L3.313,4.974L3.96,5.858
			C4.634,6.776,4.789,7.96,4.371,9.02c-0.416,1.063-1.333,1.823-2.451,2.039l-1.031,0.2v1.918l1.081,0.166
			c1.128,0.174,2.073,0.898,2.528,1.945c0.458,1.045,0.347,2.23-0.292,3.176L3.616,19.33l1.357,1.357l0.884-0.646
			c0.918-0.674,2.102-0.828,3.162-0.412c1.06,0.418,1.823,1.332,2.039,2.453l0.197,1.029h1.92l0.1-0.648
			c0.176-1.152,0.926-2.105,2.002-2.551c1.074-0.445,2.279-0.303,3.219,0.387l0.531,0.389l1.355-1.357l-0.588-0.867
			c-0.639-0.945-0.748-2.131-0.295-3.176c0.459-1.047,1.402-1.771,2.529-1.945l1.084-0.166V11.255L23.112,11.255z M12.222,16.891
			c-2.575,0-4.666-2.094-4.666-4.667c0-2.575,2.091-4.669,4.666-4.669c2.572,0,4.666,2.094,4.666,4.669
			C16.888,14.797,14.794,16.891,12.222,16.891L12.222,16.891z M12.222,8.443c-2.083,0-3.777,1.697-3.777,3.78
			c0,2.083,1.694,3.776,3.777,3.776c2.083,0,3.777-1.693,3.777-3.776C15.999,10.14,14.306,8.443,12.222,8.443L12.222,8.443z
			 M12.222,14.668c-1.35,0-2.446-1.098-2.446-2.444c0-1.35,1.097-2.446,2.446-2.446c1.347,0,2.443,1.097,2.443,2.446
			C14.665,13.57,13.569,14.668,12.222,14.668L12.222,14.668z M12.222,10.666c-0.857,0-1.557,0.697-1.557,1.558
			c0,0.858,0.7,1.554,1.557,1.554c0.857,0,1.554-0.695,1.554-1.554C13.776,11.363,13.079,10.666,12.222,10.666L12.222,10.666z"/>
</g>
</svg>

   
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="home" x="0px" y="0px" viewBox="0 0 611.997 611.998" xml:space="preserve">

	<g>
		<path d="M511.114,300.251c-9.94,0-17.638,7.663-17.638,17.651v241.105H368.401v-98.453c0-9.236-7.697-17.31-17.002-17.31h-90.435    c-9.948,0-17.96,8.073-17.96,17.31v98.453h-124.76v-233.1c0-9.306-7.69-17.036-17.638-17.036c-9.298,0-16.995,7.73-16.995,17.036    v250.752c0,9.305,7.697,17.036,16.995,17.036h160.358c9.298,0,16.995-7.731,16.995-17.036v-98.454h55.801v98.454    c0,9.305,7.697,17.036,17.639,17.036h159.715c9.299,0,16.995-7.731,16.995-17.036V317.903    C528.109,307.915,520.413,300.251,511.114,300.251z" />
		<path d="M607.003,314.003L467.819,174.225V78.919c0-9.921-8.019-17.583-17.96-17.583c-9.305,0-17.001,7.663-17.001,17.583v60.345    L318.046,23.774c-3.518-3.558-7.697-5.474-11.864-5.474c-4.81,0-8.983,1.984-12.507,5.474L5.361,312.087    c-6.917,6.91-7.375,17.994,0,24.357c6.411,7.389,17.454,6.91,24.371,0l276.45-275.793l275.807,278.393    c2.873,2.874,7.054,4.516,12.507,4.516c4.81,0,8.976-1.642,12.507-4.516C613.42,332.613,613.899,320.982,607.003,314.003z" />
	</g>

</svg>



<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="log_out" x="0px" y="0px" viewBox="0 0 612 612"  xml:space="preserve">
<g>
		<polygon points="222.545,319.909 577.228,319.909 500.728,445.091 528.546,445.091 612,306 528.546,166.909 500.728,166.909     577.228,292.146 222.545,292.146   " />
		<polygon points="0,612 417.272,612 417.272,431.182 389.454,431.182 389.454,584.182 27.818,584.182 27.818,27.818     389.454,27.818 389.454,180.818 417.272,180.818 417.272,0 0,0   " />
</g>
</svg>
    
    
</div>
<?php $session = Session::instance(); $companyId = ($session->get('company_id') > 0) ? $session->get('company_id') : 0; ?>
<?php /*
<div class="loader">
	<div class="loader_inner">
		<div class="clearfix" style="margin-bottom: 10px;text-align: center;">
			<?php 
				$company_logo = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_LOGO_IMGPATH.$companyId.'_logo.png';
				if(file_exists($company_logo)) { ?>
					<img src="<?php echo URL_BASE.SITE_LOGO_IMGPATH.$companyId.'_logo.png'; ?>" alt="Logo" style="width:154px;">
			<?php } else { ?>
				<img src="<?php echo URL_BASE.SITE_LOGO_IMGPATH; ?>logo.png" alt="Logo" style="width:154px;">
			<?php } ?>
		</div>
		<div class="clearfix"><img src='<?php echo URL_BASE; ?>public/common/css/img/ajax-loaders/294.gif'/></div>
	</div>
</div> */ ?>
<div class="taxi_dispatcher_inner">
    <div class="row top_row"> 
        <div class="col-lg-5">
<div id="wrapper" class="toggled">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a class="sidebar_menu" href="javascript:;">
                        <span class="close_side_bar">&nbsp;</span>
                    </a>
                </li>
			<?php if(isset($_SESSION['vbx_show']) && $_SESSION['vbx_show']==1){?>
			
				 <li>
                    <a href="<?php echo URL_BASE; ?>callcenter/dispatch"><?php echo __('dashboard'); ?></a>
                </li>

			<?php }else{?>
				 <li>
                    <a href="<?php echo URL_BASE; ?>taxidispatch/dashboard"><?php echo __('dashboard'); ?></a>
                </li>

			<?php } ?>
               
                <li>
                    <a href="<?php echo URL_BASE; ?>taxidispatch/manage_booking"><?php echo __('manage_booking'); ?></a>
                </li>
               <?php /* <li>
                    <a href="<?php echo URL_BASE;?>tdispatch/recurrentbooking/"><?php echo __('recurrent_booking'); ?></a>
                </li>
                <li>
                    <a href="<?php echo URL_BASE;?>tdispatch/frequent_location/"><?php echo __('frequent_location'); ?></a>
                </li>
                <li>
                    <a href="<?php echo URL_BASE;?>tdispatch/frequent_journey/"><?php echo __('frequent_journey'); ?></a>
                </li> */ ?>
                <?php if($_SESSION['user_type'] =='C' || $_SESSION['user_type'] =='A') { ?>
                <li>
                    <a target="_top" href="<?php echo URL_BASE;?>tdispatch/tdispatch_settings/"><?php echo __('tdispatch_setting'); ?></a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-80 head_leftmenu">
                         <?php
							$dash_active = $booking_active = $settings_active='';
							if($action == 'dashboard')
								$dash_active = 'active';
							
							if($action == 'booking' || $action == 'manage_booking')
								$booking_active = 'active';
								
							if($action == 'tdispatch_settings')
								$settings_active = 'active';
						?>
					 <?php if(isset($_SESSION['vbx_show']) && $_SESSION['vbx_show']==1){?>
                        <a class="head_dash <?php echo $dash_active ?>" title="<?php echo __('dashboard'); ?>" href="<?php echo URL_BASE; ?>callcenter/dispatch">
                        <i><svg viewBox="0 0 22 22" class="icon_22"><g><use xlink:href="#dashboard" class="icon_22"></use></g></svg></i>   
                        <span class="show_menu"><?php echo __('dashboard'); ?></span>
                        </a>
					<?php }else{?>
                        <a class="head_dash <?php echo $dash_active ?>" title="<?php echo __('dashboard'); ?>" href="<?php echo URL_BASE; ?>taxidispatch/dashboard">
                            <i><svg viewBox="0 0 22 22" class="icon_22"><g><use xlink:href="#dashboard" class="icon_22"></use></g></svg></i>  
                            <span class="show_menu"><?php echo __('dashboard'); ?></span>
                        </a>
					<?php } ?>
                        <a class="head_msgbook <?php echo $booking_active ?>" title="<?php echo __('booking'); ?>" href="<?php echo URL_BASE; ?>taxidispatch/manage_booking">
							<i><svg viewBox="0 0 22 22" class="icon_22"><g><use xlink:href="#booking" class="icon_22"></use></g></svg></i>
                            <span class="show_menu"><?php echo __('booking'); ?></span>
                            </a>
                        <?php if($_SESSION['user_type'] =='C' || $_SESSION['user_type'] =='A') { ?>
                        <a class="head_setting <?php echo $settings_active ?>" title="<?php echo __('settings'); ?>" href="<?php echo URL_BASE;?>tdispatch/tdispatch_settings/">
                            <i><svg viewBox="0 0 24 24" class="icon_22"><g><use xlink:href="#setting" class="icon_22"></use></g></svg></i>  
                            <span class="show_menu"><?php echo __('settings'); ?></span>
                            </a>
                <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
     </div>
        <div class="col-md-6">
            <a class="logo" href="<?php echo URL_BASE; ?>taxidispatch/dashboard" title="<?php echo __('logo'); ?>">
            <?php 
				$company_logo = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_LOGO_IMGPATH.$companyId.'_logo.png';
				if(file_exists($company_logo)) { ?>
					<img src="<?php echo URL_BASE.SITE_LOGO_IMGPATH.$companyId.'_logo.png'; ?>" alt="<?php echo __('logo'); ?>">
				<?php } else { ?>
					<img src="<?php echo URL_BASE.SITE_LOGO_IMGPATH; ?>logo.png" alt="<?php echo __('logo'); ?>">
				<?php } ?>
            </a>
        </div>
        <div class="col-lg-5 rgt_menu">            
            <ul>
				<?php if($_SESSION['user_type']=="A" || $_SESSION['user_type']=="DA") { ?>
                                        <li>
                                            <a class="goto_admin" href="<?php echo URL_BASE; ?>admin/dashboard"  title="<?php echo __('go_to'); ?>">
                                            <i><svg viewBox="0 0 122.775 122.775" class="icon_16"><g><use xlink:href="#home" class="icon_20"></use></g></svg></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="logout" href="<?php echo URL_BASE; ?>admin/logout" title="<?php echo __('logout_label'); ?>">
                                                <i><svg viewBox="0 0 122.775 122.775" class="icon_16"><g><use xlink:href="#log_out" class="icon_20"></use></g></svg></i>
                                            </a>
                                        </li>
				<?php } else if($_SESSION['user_type']=="C") { ?>
					<li><a class="goto_company" href="<?php echo URL_BASE; ?>company/dashboard" title="<?php echo __('go_to'); ?>">
                                                <i><svg viewBox="0 0 122.775 122.775" class="icon_16"><g><use xlink:href="#log_out" class="icon_20"></use></g></svg></i>
                                            </a>
                                        </li>
					<li><a class="logout" href="<?php echo URL_BASE; ?>company/logout" title="<?php echo __('logout_label'); ?>">
                                            <i><svg viewBox="0 0 122.775 122.775" class="icon_16"><g><use xlink:href="#log_out" class="icon_20"></use></g></svg></i>
                                            </a>
                                        </li>
				<?php }	else if($_SESSION['user_type']=="M") { ?>
					<li><a class="goto_dispatch" href="<?php echo URL_BASE; ?>manager/dashboard" title="<?php echo __('go_to'); ?>"></a></li>
					<li><a class="logout" href="<?php echo URL_BASE; ?>manager/logout" title="<?php echo __('logout_label'); ?>">
                                                <i><svg viewBox="0 0 122.775 122.775" class="icon_16"><g><use xlink:href="#log_out" class="icon_20"></use></g></svg></i>
                                            </a>
                                        </li>
				<?php }  ?>
            </ul>
        </div>
    </div>
</div>


