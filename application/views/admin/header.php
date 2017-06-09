<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!-- Top fixed navigation -->
<div class="login_header">    
    <div class="loginLogo">	
        <a href="<?php echo URL_BASE; ?>" target = "_blank">
                <img src="<?php echo URL_BASE . SITE_LOGO_IMGPATH . 'logo.png'; ?>" />
        </a>
    </div> 
    <div class="header_login_rgt">
        <ul>
            <li><a href="<?php echo URL_BASE; ?>" title="<?php echo __('main_website'); ?>"><i class="icon-reply"></i><span><?php echo __('main_website'); ?></span></a></li>
            <li><a href="<?php echo URL_BASE; ?>#contact-us" title="<?php echo __('contact_admin'); ?>"><i class="icon-user"></i><span><?php echo __('contact_admin'); ?></span></a></li>
            <li><a href="http://www.ndottech.com" target="_blank" title="<?php echo __('Support'); ?>"><i class="icon-comments"></i><span><?php echo __('Support'); ?></span></a></li>
<!--            <li id="login_top_lang_select"></li>-->
        </ul>
    </div> 
</div>
  <?php  if(($action != 'login' && $action != 'forgot_password') ) { ?>
        <div class="head_in">
        <div class="head_rgt">
	    <?php if ($action != 'login'): ?>
		<p class="fl"> <?php echo __("welcome_label"); ?></p><p class="fl"> <?php echo $_SESSION['name'] . ' | '; ?>  </p>
		<p class="fl"><a href = "<?php echo URL_BASE; ?>admin/edifprofile/<?php echo $adminid; ?>" class='fl'><?php echo __("menu_myinfo") . ' | '; ?></a></p>
		<p class="fl"><a href = "<?php echo URL_BASE; ?>admin/changepassword" class='fl'><?php echo __("menu_change_password") . ' | '; ?></a></p>
		<p class="fl"><a href ="<?php echo URL_BASE; ?>admin/logout" class='fl' title="<?php echo __('logout_label') ?>"> <?php echo __('logout_label') ?></a></p>
        </div>	
        </div>
 <?php endif; } ?>	
