<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<footer class="footer">
	<div class="inner">
		<p><?php echo $footer_contents['site_copyrights']; //__('footer_label'); ?></p>
	</div>
</footer>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/landing_page/jquery.scroller.js"></script>
<script type="text/javascript">$('.main-nav').scroller();</script>
<script type="text/javascript">
	$(".start_now_free").click(function() {
    $('html,body').animate({
        scrollTop: $("#contact_det").offset().top},
        'slow');
});
</script>
<?php $session = Session::instance(); if($session->get("id") == "") { ?>
<script type="text/javascript">
$(document).ready(function(){
	/** Home Page Signin & Signup popup start **/
	$('#fade').on('click', function(ev) {
		clearFrom('signin_form');
		clearFrom('signupForm');
		clearFrom('forgot_form');
		$('.signin_form').hide();
		$('.signup_form').hide();
		$('.forgot_form').hide();
		$('#fade').hide();
	});
	
	$('.sign_up, .sign_up_home').click(function(){
		clearFrom('signupForm');
		$('.signin_form').hide();
		$('.forgot_form').hide();
		$('.facebook_info_popup').hide();
		$('#fade').show();
		$('.signup_form').show();
		if($("input[name='facebook_alert']").val() == 1) {
			$("form[name='signupForm']").find(":input[name=fb_id], :input[name=fb_access_token], :input[name=first_name], :input[name=email]").val('');
		}
		$("form[name='signupForm']").find(":input[name=email]").focus();
	});
	$('.sign_in,#booking_menu').click(function(){
		clearFrom('signin_form');
		$('.signup_form').hide();
		$('.forgot_form').hide();
		$('.facebook_info_popup').hide();
		$('#fade').show();
		$('.signin_form').show();
		$("form[name='signin_form']").find(":input[name=country_code]").focus();
	});
	$('.forgot_password').click(function(){
		clearFrom('forgot_form');
		$('.signup_form').hide();
		$('.signin_form').hide();
		$('.facebook_info_popup').hide();
		$('#fade').show();
		$('.forgot_form').show();
		$("form[name='forgot_form']").find(":input[name=country_code]").focus();
	});
	$('.close_butt').click(function(){
		$('.signup_form').hide();
		$('#fade').hide();
		$('.signin_form').hide();
		$('.forgot_form').hide();
		$('.facebook_info_popup').hide();
	});
	/** Home Page Signin & Signup popup end **/
	
	/** Trigger signin or signup form using facebook returns start **/

	var facebook_user = localStorage.getItem("registered_user");
	if(facebook_user != null) {
		if(facebook_user == 1) {
			$(".sign_in").trigger('click');
		}
		if(facebook_user == 2) {
			$(".sign_up").trigger('click');
		}
		localStorage.removeItem("registered_user");
	}

	/** Facebook information popup **/

	var open_info_confirm = localStorage.getItem("open_info_confirm");
	if(open_info_confirm != null && open_info_confirm == 1) {
		$("#fade, .facebook_info_popup").show();
		localStorage.removeItem("open_info_confirm");
	}

	/** Trigger signin or signup form using facebook returns end **/
});

function clearFrom(form_name)
{
	var button = "<?php echo __('btn_submit'); ?>";
	if(form_name == "signin_form") {
		button = "<?php echo __('button_signin'); ?>";
	} else if(form_name == "signup_form") {
		button = "<?php echo __('create_account'); ?>";
	}
	$("form[name='"+ form_name +"']").trigger("reset");
	$("form[name='"+ form_name +"']").find('em').text('');
	$("form[name='"+ form_name +"']").find(":input[type=button]").removeAttr("disabled").val(button);
}

function info_confirm(val)
{
	localStorage.removeItem("open_info_confirm");
	if(val == 1) {
		setData(1);
	} else {
		setData(2);
	}
}

function setData(type)
{
	$.ajax({
		url:"<?php echo URL_BASE;?>users/setData",
		type:"POST",
		data:"type="+type,
		success:function() {
			localStorage.setItem("registered_user", type);
			window.location.reload(true);
		},
		error:function()
		{
			//window.location.reload(true);
		}
	});
}
</script>
<?php } ?>
<div id="fade"></div>
