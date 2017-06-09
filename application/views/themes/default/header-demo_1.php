<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<?php $trial_enterprise_package = (defined('TRIAL_ENTER_PACK')) ? TRIAL_ENTER_PACK : array(0,3); #0-Trial, 3-Enterprise  ?>
<header class="header">
	<div class="inner">
		<div class="header_left">
			<img src="<?php echo URL_BASE . SITE_LOGO_IMGPATH . 'logo.png'; ?>" alt="" />
		</div>
		<div class="header_right">
            <a href="javascript:;" id="toggle_menu"></a>
			<nav class="main-nav">
				<ul>
					<li><a href="#home" title="Home">Home</a></li>
					<li><a href="#about-us" title="About us">About us</a></li>
					<li><a href="#join-us-driver" title="Join us driver">Join us driver</a></li>
					<!--<li><a href="#contact-us" title="Contact us">Contact us</a></li> -->
					<?php if(in_array(PACKAGE_TYPE,$trial_enterprise_package)){ ?>
						<li><a class="sign_in" href="javascript:;" title="Sign in">Sign in</a></li>
						<li><a class="sign_up" href="javascript:;" title="Sign Up">Sign Up</a></li>
					<?php } ?>
				</ul>
			</nav>
		</div>
	</div>
</header>
<?php /** Sign in popup Start **/ ?>
<?php echo View::factory(USERVIEW.'website_user/signin_popup'); ?>
<?php /** Sign in popup End **/ ?>

<?php /** Forgot password in popup Start **/ ?>
<?php echo View::factory(USERVIEW.'website_user/forgot_password'); ?>
<?php /** Forgot password in popup End **/ ?>

<?php /** Sign up popup Start **/ ?>
<?php echo View::factory(USERVIEW.'website_user/signup_popup'); ?>
<?php /** Sign up popup End **/ ?>

<div class="facebook_info_popup" style="display: none;">
      <h3><?php echo __('information'); ?></h3>
      <div class="cvv_details">
              <ul>
                      <li>
                              <div class="full_name">
                                      <?php echo __("passenger_fb_info_alert"); ?>
                              </div>
                      </li>
                      <li>
                              <div class="sub_butt">
                                      <input type="button" onclick="info_confirm(1);" value="<?php echo __('Yes'); ?>" title="<?php echo __('Yes'); ?>"/>
                                      <input type="button" onclick="info_confirm(0);" value="<?php echo __('No'); ?>" title="<?php echo __('No'); ?>"/>
                              </div>
                      </li>
              </ul>
      </div>
</div>

<script>
function tab_index(form_name)
{
	$("form[name='"+form_name+"'] :input:last").on('keydown', function (e) { 
		if ($("this:focus") && (e.which == 9)) {
			e.preventDefault();
		   $("form[name='"+form_name+"'] :input:first").focus();
		}
	});
}


	

$(window).scroll(function() {
	if ($(this).scrollTop() > 1){ 
		$('.header').addClass("sticky");
	}
	else{
		$('.header').removeClass("sticky");
	}
});


			
$("#toggle_menu").click(function(){
    $('.main-nav').slideToggle();
                });
                $(window).bind('resize orientationchange', function() {
    ww = document.body.clientWidth;
    adjustMenu();
});
function adjustMenu() {
        if (ww < 768) {
                $("#toggle_menu").show();
                $(".main-nav").hide();
        }
        else if (ww >= 769) {
                $("#toggle_menu").hide();
                $(".main-nav").show();
        }
}
		
        
</script>
