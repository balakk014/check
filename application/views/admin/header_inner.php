<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<style>
    .header {background:<?php echo ADMIN_HEADER_BG; ?>}
    .sticky_message{background:<?php echo ADMIN_FOOTER_BG; ?>}
    .main-sidebar, .main_header .logo{background:<?php echo ADMIN_SIDEBAR_BG; ?>}
    .menu_drop_down,ul.menu_drop_down li ul.menu_drop_down{background:<?php echo ADMIN_SIDEBAR_SUBTAB; ?>}
    .icon_24{fill:<?php echo ADMIN_SIDEBAR_ICON; ?>}
    .menu > ul > li:hover > a .icon_24, .menu > ul > li.active > a .icon_24{fill:<?php echo ADMIN_SIDEBAR_ICON_ACTIVE; ?>}
    .menu > ul > li.active > a i, .menu > ul > li:hover > a i{background:<?php echo ADMIN_SIDEBAR_ICON_CIRCLE; ?>}
    .menu > ul > li.active{background:<?php echo ADMIN_SIDEBAR_ACTIVE; ?>}
    .new_button input[type=submit], .new_button input[type=button], .new_button input[type=reset],.dashboard_calender ul li a, .dashboard_calender_in_header ul li a, .dashboard_calender_in_header ul li input[type="button"]{background:<?php echo ADMIN_BUTTON_BG; ?>}
    .export_me_menu{color:#000;background:<?php echo ADMIN_BUTTON_BG; ?>}
    .new_button input[type=submit]:hover, .new_button input[type=button]:hover, .new_button input[type=reset]:hover,.dashboard_calender ul li a, .dashboard_calender_in_header ul li a, .dashboard_calender_in_header ul li input[type="button"]:hover{background:<?php echo ADMIN_BUTTON_HOVER_BG; ?>}
    .export_me_menu:hover{color:#fff;background:<?php echo ADMIN_BUTTON_HOVER_BG; ?>}
</style>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/script.js"></script> 
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/plugins/bootstrap.min.js"></script>
<?php
if ($_SESSION['user_type'] != 'M' && $_SESSION['user_type'] == 'A') {
	$admin_available_balance = 0;
}

if ($_SESSION['user_type'] != 'M' && $_SESSION['user_type'] == 'C') {
	$get_accountbalance1 = 0;
}
?>
<?php
if ($action != 'login'):
    if ($_SESSION['user_type'] == 'A' || $_SESSION['user_type'] == 'S') {
	$url = "admin/editprofile/";
    } else if ($_SESSION['user_type'] == 'C') {
	$url = "company/editprofile/";
    } else if ($_SESSION['user_type'] == 'M') {
	$url = "manager/editprofile/";
    }

    if ($_SESSION['user_type'] != 'M' && $_SESSION['user_type'] != 'S') {
	/*if ($_SESSION['user_type'] == 'C') {
	    $company_currency = findcompany_currency($_SESSION['company_id']);
	} else {
	    $company_currency = CURRENCY;
	}*/
        $company_currency = CURRENCY;
    }
    ?>
<div id="global-icon-symbols" data-tg-refresh="global-icon-symbols" data-tg-refresh-always="true" style="display: none;">
 <svg  id="edit_profile" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 297.068 297.068" style="enable-background:new 0 0 297.068 297.068;">
<g>
<path d="M288.758,46.999l-38.69-38.69c-5.347-5.354-12.455-8.303-20.02-8.303s-14.672,2.943-20.02,8.297L28.632,190.266L0,297.061
l107.547-28.805L288.745,87.045c5.36-5.354,8.323-12.462,8.323-20.026S294.105,52.347,288.758,46.999z M43.478,193.583
L180.71,55.823l60.554,60.541L103.761,253.866L43.478,193.583z M37.719,206.006l53.368,53.362l-42.404,11.35L26.35,248.384
L37.719,206.006z M279.657,77.951l-19.493,19.505l-60.579-60.541l19.544-19.525c5.823-5.848,16.016-5.842,21.851,0l38.69,38.696
c2.924,2.918,4.544,6.8,4.544,10.926C284.214,71.139,282.594,75.027,279.657,77.951z"/>
</g>

</svg>

<svg id="change_password" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;">
<g>
		<path d="M334.974,0c-95.419,0-173.049,77.63-173.049,173.049c0,21.213,3.769,41.827,11.211,61.403L7.672,399.928
			c-2.365,2.366-3.694,5.573-3.694,8.917v90.544c0,6.965,5.646,12.611,12.611,12.611h74.616c3.341,0,6.545-1.325,8.91-3.686
			l25.145-25.107c2.37-2.366,3.701-5.577,3.701-8.925v-30.876h30.837c6.965,0,12.611-5.646,12.611-12.611v-12.36h12.361
			c6.964,0,12.611-5.646,12.611-12.611v-27.136h27.136c3.344,0,6.551-1.329,8.917-3.694l40.121-40.121
			c19.579,7.449,40.196,11.223,61.417,11.223c95.419,0,173.049-77.63,173.049-173.049C508.022,77.63,430.393,0,334.974,0z
			 M334.974,320.874c-20.642,0-40.606-4.169-59.339-12.393c-4.844-2.126-10.299-0.956-13.871,2.525
			c-0.039,0.037-0.077,0.067-0.115,0.106l-42.354,42.354h-34.523c-6.965,0-12.611,5.646-12.611,12.611v27.136H159.8
			c-6.964,0-12.611,5.646-12.611,12.611v12.36h-30.838c-6.964,0-12.611,5.646-12.611,12.611v38.257l-17.753,17.725H29.202v-17.821
			l154.141-154.14c4.433-4.433,4.433-11.619,0-16.051s-11.617-4.434-16.053,0L29.202,436.854V414.07l167.696-167.708
			c0.038-0.038,0.067-0.073,0.102-0.11c3.482-3.569,4.656-9.024,2.53-13.872c-8.216-18.732-12.38-38.695-12.38-59.33
			c0-81.512,66.315-147.827,147.827-147.827S482.802,91.537,482.802,173.05C482.8,254.56,416.484,320.874,334.974,320.874z"/>
		<path d="M387.638,73.144c-26.047,0-47.237,21.19-47.237,47.237s21.19,47.237,47.237,47.237s47.237-21.19,47.237-47.237
			S413.686,73.144,387.638,73.144z M387.638,142.396c-12.139,0-22.015-9.876-22.015-22.015s9.876-22.015,22.015-22.015
			s22.015,9.876,22.015,22.015S399.777,142.396,387.638,142.396z"/>
	</g>

</svg>


<svg id="log_out" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384.971 384.971" style="enable-background:new 0 0 384.971 384.971;">
<g>
<path d="M180.455,360.91H24.061V24.061h156.394c6.641,0,12.03-5.39,12.03-12.03s-5.39-12.03-12.03-12.03H12.03
        C5.39,0.001,0,5.39,0,12.031V372.94c0,6.641,5.39,12.03,12.03,12.03h168.424c6.641,0,12.03-5.39,12.03-12.03
        C192.485,366.299,187.095,360.91,180.455,360.91z"/>
<path d="M381.481,184.088l-83.009-84.2c-4.704-4.752-12.319-4.74-17.011,0c-4.704,4.74-4.704,12.439,0,17.179l62.558,63.46H96.279
        c-6.641,0-12.03,5.438-12.03,12.151c0,6.713,5.39,12.151,12.03,12.151h247.74l-62.558,63.46c-4.704,4.752-4.704,12.439,0,17.179
        c4.704,4.752,12.319,4.752,17.011,0l82.997-84.2C386.113,196.588,386.161,188.756,381.481,184.088z"/>
	</g>
</svg>

            </div>



    <?php if ($_SESSION['user_type'] == 'A' || $_SESSION['user_type'] == 'S' || $_SESSION['user_type'] == 'DA') { ?>

	<a href="<?php echo URL_BASE; ?>admin/manage_site" target = "_blank" title="" class="logo">
	    <span class="logo-mini">
		<img src="<?php echo URL_BASE . SITE_LOGO_IMGPATH . 'logo-small.png'; ?>" alt="" />
	    </span>
	    <span class="logo-lg minus">
		<img src="<?php echo URL_BASE . SITE_LOGO_IMGPATH . 'logo.png'; ?>" alt="" />
	    </span>
	</a>
	<nav class="navbar-static-top">
	    <a class="sidebar-toggle" href="javascript:;"></a>
	    <div class="header_rgt">
		<ul>

		    <li>
			<a class="all_balance"><?php
	if ($_SESSION['user_type'] != 'S' && $_SESSION['user_type'] != 'DA') {
	    echo __('available') . ' ';
	} else {
	    echo "";
	}
	if ($_SESSION['user_type'] != 'M' && $_SESSION['user_type'] == 'A') {
		echo CURRENCY.$admin_available_balance;
	}
	if ($_SESSION['user_type'] != 'M' && $_SESSION['user_type'] == 'C') {
		$company_currency = findcompany_currency($_SESSION['company_id']);                
	}
	?></a>
		    </li>
		    <li>
				<a href="<?php echo URL_BASE."company/editprofile/".$_SESSION['userid']; ?>" title="Profile">
					<?php if (file_exists(DOCROOT.COMPANY_IMG_IMGPATH . $_SESSION['userid'].".png")) {
						$user_path_img = URL_BASE.COMPANY_IMG_IMGPATH . $_SESSION['userid'].".png";
					} else {
						$user_path_img = URL_BASE.COMPANY_IMG_IMGPATH."no_image.png";
					} ?>
					<img class="img-circle" alt="profile images" src="<?php echo $user_path_img; ?>" width="33" height="33">
				</a>
		    </li>
		    <li class="rgt_down_1">
			<div class="middle">
			    <a class="user-menu" data-toggle="dropdown">
				<?php
				if ((isset($user_data) && isset($user_data[0]['photo']) ) && $user_data[0]['photo'] != '' && file_exists(DOCROOT . USER_IMGPATH_HEADER_THUMB . $user_data[0]['photo'])) {
				    $user_path_img = URL_BASE . USER_IMGPATH_HEADER_THUMB . $user_data[0]['photo'];
				} else {
				    $user_path_img = IMGPATH . 'icons/userPic.png';
				}
				?>

				<span class="log_user"><?php echo __("administrator"); ?></span>
                                <span class="down_arr minus">&nbsp;</span>
			    </a>
			</div>
			<div class="header_profile_drop_down">			 
				<ul>
				    <li><a href="<?php echo URL_BASE; ?>admin/editprofile/<?php echo $adminid; ?>" title="">                                            
                                            <i><svg role="img" viewBox="0 0 16 16" class="icon_14"><g><use xlink:href="#edit_profile" class="icon_14"></use></g></svg></i>
                                            <span>Edit <?php echo __('profile'); ?></span></a></li>
				    <?php //if (COMPANY_CID > 1) { ?>
	    			    <li><a href="<?php echo URL_BASE; ?>admin/changepassword/" title="">
                                            <i><svg role="img" viewBox="0 0 16 16" class="icon_14"><g><use xlink:href="#change_password" class="icon_14"></use></g></svg></i>
                                            <span><?php echo __("menu_change_password"); ?></span></a></li>
				    <?php //} ?>
				    <li><a href="<?php echo URL_BASE; ?>admin/logout/" title="">
                                            <i><svg role="img" viewBox="0 0 16 16" class="icon_14"><g><use xlink:href="#log_out" class="icon_14"></use></g></svg></i>
                                            <span><?php echo __("logout_label"); ?></span></a></li>
				</ul>
			  
			</div>
		    </li>			    
		</ul>
	    </div>
	</nav>

    <?php } else if ($_SESSION['user_type'] == 'C') { ?>
	<a href="<?php echo URL_BASE; ?>" target = "_blank" title="" class="logo">
	<?php
		$company_logo = $_SERVER['DOCUMENT_ROOT'] . '/' . SITE_LOGO_IMGPATH . $_SESSION['company_id'] . '_logo.png';
		if (file_exists($company_logo)) { ?>
		<span class="logo-mini">
			<img src="<?php echo URL_BASE . SITE_LOGO_IMGPATH . $_SESSION['company_id'].'.png'; ?>" alt="" />
		</span>
		<span class="logo-lg minus">
			<img src="<?php echo URL_BASE . SITE_LOGO_IMGPATH . $_SESSION['company_id']. '_logo.png'; ?>" alt="" />
		</span>
	<?php } else { ?>
		<span class="logo-mini">
			<img src="<?php echo URL_BASE . SITE_LOGO_IMGPATH . 'logo-small.png'; ?>" alt="" />
		</span>
		<span class="logo-lg minus">
			<img src="<?php echo URL_BASE . SITE_LOGO_IMGPATH . 'logo.png'; ?>" alt="" />
		</span>
	<?php } ?>
	</a>
	<nav class="navbar-static-top">
	    <a class="sidebar-toggle" href="javascript:;"></a>
	    
	    <div class="header_rgt">
		<ul>

		    <li>
			<a class="all_balance"><?php
	echo __('available') . ' ';
	if ($_SESSION['user_type'] != 'M' && $_SESSION['user_type'] == 'A') {
		echo CURRENCY.$admin_available_balance;
	}
	if ($_SESSION['user_type'] != 'M' && $_SESSION['user_type'] == 'C') {
	    $company_currency = findcompany_currency($_SESSION['company_id']);
	}
	    ?></a></li>

		    <li>
			<a href="<?php echo URL_BASE."company/editprofile/".$_SESSION['userid']; ?>" title="Profile">
			   <?php if (file_exists(DOCROOT.COMPANY_IMG_IMGPATH . $_SESSION['userid'].".png")) {
					$user_path_img = URL_BASE.COMPANY_IMG_IMGPATH . $_SESSION['userid'].".png";
				} else {
					$user_path_img = URL_BASE.COMPANY_IMG_IMGPATH."no_image.png";
				} ?>
				<img class="img-circle" alt="profile images" src="<?php echo $user_path_img; ?>" width="33" height="33">
			</a>
		    </li>
		    <li class="rgt_down_1">
			<div class="middle">
			    <a class="user-menu" data-toggle="dropdown">
				<?php
				if ((isset($user_data) && isset($user_data[0]['photo']) ) && $user_data[0]['photo'] != '' && file_exists(DOCROOT . USER_IMGPATH_HEADER_THUMB . $user_data[0]['photo'])) {
				    $user_path_img = URL_BASE . USER_IMGPATH_HEADER_THUMB . $user_data[0]['photo'];
				} else {
				    $user_path_img = URL_BASE.COMPANY_IMG_IMGPATH."no_image.png";
				}
				?>

				<span class="log_user"><?php echo 'Welcome' . ' ' . $_SESSION['name']; ?></span>
				<span class="down_arr minus">
				    <span><b class="caret"></b></span>
				</span>
			    </a>
			</div>
			<div class="menu_drop_down_2">
			    <div class="menu_mid">
				<ul class="drop_down_1">
				    <li><a href="<?php echo URL_BASE; ?>company/editprofile/<?php echo $adminid; ?>" title="">
                                            <i><svg viewBox="0 0 16 16" class="icon_14"><g><use xlink:href="#edit_profile" class="icon_14"></use></g></svg></i>
                                            <span>Edit <?php echo __('profile'); ?></span></a></li>
				    <?php if (COMPANY_CID > 1) {
					?>
	    			    <li><a href="<?php echo URL_BASE; ?>company/changepassword/" title="">
                                            <i><svg viewBox="0 0 16 16" class="icon_14"><g><use xlink:href="#change_password" class="icon_14"></use></g></svg></i>
                                            <span><?php echo __("menu_change_password"); ?></span></a></li>
				    <?php } ?>
				    <li><a href="<?php echo URL_BASE; ?>company/logout/" title="">
                                            <i><svg viewBox="0 0 16 16" class="icon_14"><g><use xlink:href="#log_out" class="icon_14"></use></g></svg></i>
                                            <span><?php echo __("logout_label"); ?></span></a></li>
				</ul>
			    </div>
			</div>
		    </li>
		</ul>
	    </div>
	</nav>
	    <!-- /fixed top -->
	<?php } else if ($_SESSION['user_type'] == 'M') { ?>	
	    <!-- Fixed top -->
	    
	    
	<a href="<?php echo URL_BASE; ?>" target = "_blank" title="" class="logo">
	    <span class="logo-mini">
		<img src="<?php echo URL_BASE . SITE_LOGO_IMGPATH . 'logo-small.png'; ?>" alt="" />
	    </span>
	    <span class="logo-lg minus">
		<img src="<?php echo URL_BASE . SITE_LOGO_IMGPATH . 'logo.png'; ?>" alt="" />
	    </span>
	</a>
	    
	    
		    
	    <nav class="navbar-static-top">
	    <a class="sidebar-toggle" href="javascript:;"></a>

	    <div class="header_rgt">
		    <ul>
			 <li>
			<a href="<?php echo URL_BASE."company/editprofile/".$_SESSION['userid']; ?>" title="<?php echo __('profile'); ?>">
			  <?php
				if ((isset($user_data) && isset($user_data[0]['photo']) ) && $user_data[0]['photo'] != '' && file_exists(DOCROOT . USER_IMGPATH_HEADER_THUMB . $user_data[0]['photo'])) {
				    $user_path_img = URL_BASE . USER_IMGPATH_HEADER_THUMB . $user_data[0]['photo'];
				} else {
				    $user_path_img = URL_BASE.COMPANY_IMG_IMGPATH."no_image.png";
				}
				?>

				<img src="<?php echo $user_path_img; ?>" alt="" />
			</a>
		    </li>
			<li class="rgt_down_1">
			<div class="middle">
			    <a class="user-menu" data-toggle="dropdown">
				
				<span class="log_user"><?php echo 'Welcome' . ' ' . $_SESSION['name']; ?></span>
				<span class="down_arr minus">
				    <span><b class="caret"></b></span>
				</span>
			    </a>
			</div>
			<div class="menu_drop_down_2">
			    <div class="menu_mid">
				<ul class="drop_down_1">
				    <li><a href="<?php echo URL_BASE; ?>manager/editprofile/<?php echo $adminid; ?>" title="">
                                            <i><svg viewBox="0 0 19.333 17.832" class="icon_14"><g><use xlink:href="#edit_profile" class="icon_14"></use></g></svg></i>
                                            <span>Edit <?php echo __('profile'); ?></span></a></li>
				<?php if (COMPANY_CID > 1) {
				    ?>
	    			<li><a href="<?php echo URL_BASE; ?>manager/changepassword/" title="">
                                        <i><svg viewBox="0 0 12.563 18" class="icon_14"><g><use xlink:href="#change_password" class="icon_14"></use></g></svg></i>
                                        <span><?php echo __("menu_change_password"); ?></span></a></li>
				<?php } ?>
				<li><a href="<?php echo URL_BASE; ?>manager/logout/" title="">
                                        <i><svg viewBox="0 0 122.775 122.775" class="icon_14"><g><use xlink:href="#log_out" class="icon_14"></use></g></svg></i>
                                        <span><?php echo __("logout_label"); ?></span></a></li>
				</ul>
			    </div>
			</div>
		    </li>	    
		    
			
		    </ul>
		</div>
	    </nav>
	    <!-- /fixed top -->
	<?php } ?>
    <?php endif; ?>


