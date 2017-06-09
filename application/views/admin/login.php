<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!--login start-->

<div class="login_outer">
    <div class="inner_login">
    <?php  echo new View("admin/header"); ?>
    
        
        <div class="loginWrapper">
    <div class="loginWrapper_inner">
	    <div class="login_box">
                <?php if($controller == 'company') { ?>
      <div class="log_form_menu_sett">
        <div style="float:left"><a href="<?php echo URL_BASE; ?>company/login" target = "_self"><?php echo __('company_login'); ?></a></div>
        <div style="float:right"><a href="<?php echo URL_BASE; ?>manager/login" target = "_self"><?php echo __('manager_login'); ?></a> </div>
     </div>
	<?php } ?>
        <div class="title"><h6>
        <?php if($controller == 'company') { echo __('companyad_login'); } elseif($controller == 'manager') { echo __('managerad_login');   } if($controller == 'admin') { echo __('page_login_title'); }
        	?>
        </h6>
        </div>
        <?php
        //For Notice Messages
        //===================
        //echo $message->message;exit;
        $sucessful_message = Message::display();

        if ($sucessful_message) {
            ?>

            <div id="messagedisplay" class="padding_150">
                <div class="notice_message">
            <?php echo $sucessful_message; ?>
                </div>
            </div>
<?php } ?> 
<?php if (isset($error_login)) { ?><span class="login_error"><?php echo $error_login; ?></span><?php } ?>
        <form class="form_login" method="post" name="frmlogin" id="frmlogin">
            <fieldset>
                <div class="log_form_control">
                    <input class="form_control" placeholder="Email address" tabindex="1" type="text" id="email" name="email" value="<?php if(isset($_POST["email"])) { echo $_POST["email"]; }elseif(isset($_COOKIE["admin_email"])) { echo $_COOKIE["admin_email"]; } ?>"  maxlength="50" />
<?php if(isset($errors) && array_key_exists('email',$errors)){ echo "<span class='error'>".__('please_provide_email')."</span>";}?>
                </div>

                <div class="log_form_control pass_field">
                        <input class="form_control" placeholder="Password" tabindex="2" type="password" name="password" value="<?php if(isset($_COOKIE["admin_password"])) { echo $_COOKIE["admin_password"]; } ?>" maxlength="15" />
<?php if(isset($errors) && array_key_exists('password',$errors)){ echo "<span class='error'>".ucfirst($errors['password'])."</span>";}?>
                        <div class="rememberMe"><a href="<?php echo URL_BASE; ?>admin/forgot_password" tabindex="3" class="frgtpsd">Forgot?<?php // echo __('forgot_password'); ?></a></div> 
                </div>
				
				<div class="log_form_control remember_text">
					<p><input type="checkbox" name="remember_me" value="1" tabindex="4"/><span><?php echo __('remember_me'); ?></span></p>
				</div>
				
                <div class="loginControl">
                    <input type="submit" value="Log me in"  name="admin_login" tabindex="5" title="<?php echo __('admin_login'); ?>" />
                </div>
            </fieldset>
        </form>
    </div>
   </div>
 </div>   
    </div>
</div>
    <!--login_end-->
		    
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/plugins/bootstrap.min.js"></script>
<script>
$(document).ready( function () {
	$(".close_message").click( function() {
		$("#messagedisplay").hide();
	});
});
</script>
