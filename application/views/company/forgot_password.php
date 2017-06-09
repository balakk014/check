<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!--login start-->
<div class="login_outer">
    <div class="inner_login">
        <?php  echo new View("admin/header"); ?>

<div class="loginWrapper">
    <div class="loginWrapper_inner"> 
    <div class="login_box">
        <div class="title"><h6><?php echo __('forgot_password'); ?></h6>
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
                <div class="log_form_control">
				<input class="form_control" placeholder="Email address" type="text" name="email" id="email" value="<?php if (isset($_POST['email'])) { echo $_POST['email'];} ?>"  maxlength="50" />
			<?php if(isset($errors) && array_key_exists('email',$errors)){ echo "<span class='error'>".__('please_provide_email')."</span>";}?>
                </div>
                <div class="loginControl">
			<?php /*<div class="rememberMe"><a href="<?php echo URL_BASE; ?>company/login" title="<?php echo ucfirst(__('login')); ?>" class="frgtpsd"><?php echo ucfirst(__('login')); ?></a></div> */?>
                    <input type="submit" value="<?php echo __('button_send'); ?>"  name="submit_forgot_password_admin" title="<?php echo __('button_send'); ?>" />
                </div>

        </form>
	</div>
</div>
</div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#email").focus();	
});  
</script>
