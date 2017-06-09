<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php $session = Session::instance(); ?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/scale.fix.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/jquery.formance.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/awesome_form.js"></script>
<div class="signup_form" style="display:none">
	<h3>Sign Up</h3>
<!--	<a href="javascript:;" class="close_butt">&nbsp;</a>-->
	<input type="hidden" name="facebook_alert" value="<?php echo $session->get("signup_data"); ?>"/>
	<form name="signupForm" method="post" enctype="multipart/form-data">
		<input type="hidden" name="fb_id" value="<?php echo $session->get("fb_id"); ?>"/>
		<input type="hidden" name="fb_access_token" value="<?php echo $session->get("fb_access_token"); ?>"/>
		<ul>
			<li><label>Account</label></li>
			<li><div class="full_name"><input type="text" tabindex="1" on-paste="return false;" name="email" maxlength="64" value="<?php if($session->get("signup_data") == 2) { echo $session->get("fb_email"); } ?>" placeholder="<?php echo __('email'); ?>*"/></div></li>
			<li><div class="full_name"><input type="password" tabindex="2" name="password" maxlength="24" value="" placeholder="<?php echo __('password'); ?>*"/></div></li>
			<li><div class="full_name"><input type="password" tabindex="3" name="confirm_password" maxlength="24" value="" placeholder="<?php echo __('confirm_password_label'); ?>*"/></div></li>
			 <li><div class="full_name_mobile"><span><input type="text" tabindex="4" class="country_code_restrict" maxlength="5" name="country_code" value="" placeholder="<?php echo TELEPHONECODE ?>*"/></span><div class="mob_no"><input type="text" name="mobile_number" tabindex="5" class="txtboxToFilter" maxlength="15" value="" placeholder="<?php echo __('mobile_number'); ?>*"/></div></div><em id="signup_code_phone_error"></em></li>
			 <?php if(REFERRAL_SETTINGS == 1) { ?>
			<li><div class="full_name"><input type="text" name="referral_code" tabindex="6" maxlength="6" value="" placeholder="Referral Code"/></div></li>
			<?php } ?>
			<li><label>You</label></li>
			<li>
				<div class="full_name">
					<select name="salutation" tabindex="7">
						<option value="1">Mr</option>
						<option value="2">Mrs</option>
						<option value="3">Miss</option>
					</select>
				</div>
			</li>
			<li><div class="full_name"><input type="text" on-paste="return false;" name="first_name" id="firstname" tabindex="8" maxlength="35" value="<?php if($session->get("signup_data") == 2) { echo $session->get("fb_name"); } ?>" placeholder="<?php echo __('firstname_label'); ?>*"/></div></li>
			<li><div class="full_name"><input type="text" on-paste="return false;" name="last_name" id="last_name" tabindex="9" maxlength="35" value="" placeholder="<?php echo __('lastname_label'); ?>*"/></div></li>
			<?php if(DEFAULT_SKIP_CREDIT_CARD) { ?>
			<li><label>Card</label></li>
			<li><div class="full_name"><input type="text" name="credit_card_number" class="credit_card_number" x-autocompletetype="cc-number" id="credit_card_number" maxlength="20" tabindex="10" value="" placeholder="<?php echo __('credit_card_no'); ?>*"/></div></li>
			<li><div class="month"><input type="text" name="month" id="month" maxlength="2" tabindex="11" value="" placeholder="<?php echo __('month'); ?>*"/></div>
				<div class="year"><input type="text" name="year" id="year" maxlength="4" tabindex="12" value="" placeholder="<?php echo __('year'); ?>*"/></div>
			</li>
			<li><div class="full_name"><input type="text" name="cvv" id="cvv" maxlength="4" tabindex="13" value="" placeholder="<?php echo __('CVV'); ?>*"/></div></li>
			<?php } ?>
			<li><div class="sub_butt"><input type="button" name="signup_submit" tabindex="14" onclick="signupSubmit();" value="CREATE ACCOUNT"/></div></li>
			<li><p>By clicking "Create Account", you agree to <?php echo SITENAME;?>'s<br>
				<a href="<?php echo URL_BASE; ?>termsconditions.html" target="_blank">Terms and Conditions</a> and <a target="_blank" href="<?php echo URL_BASE; ?>privacypolicy.html">Privacy Policy.</a></p></li>
		</ul>
	</form>
</div>
<script type="text/javascript">
$(document).ready( function () {
	tab_index("signupForm");
	//card number, month, year
	$("#month,#year,#cvv" ).keyup(function(event) {
		//to allow left and right arrow key move and backspace, delete buttons
		if((event.which>=37 && event.which<=40) || event.which==8 || event.which==46)
		{
			return false;
		}
		this.value = this.value.replace(/[`~!@#$%^&*()\s_|+\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
	});
	//first name, lastname
	$("#firstname,#last_name").keyup(function(event) {
		//to allow left and right arrow key move and backspace, delete buttons
		if((event.which>=37 && event.which<=40) || event.which==8 || event.which==46)
		{
			return false;
		}
		this.value = this.value.replace(/[`~!0-9@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
	});

	$("form[name='signupForm'] :input").keypress(function (e) {
		if (e.which == 13) {
			signupSubmit();
			return false;
		}
	});

	<?php if($session->get("signup_data") != "" && $session->get("signup_data") == 2) { ?>
		deleteSetDataSession("<?php echo $session->get("signup_data"); ?>");
	<?php } ?>
});
/** Signup Form validate **/
function signupSubmit()
{
	$.ajax({
		url:'<?php echo URL_BASE;?>find/signup',
		type:'post',
		dataType:'json',
		data:$("form[name='signupForm']").serialize(),
		beforeSend:function() {
			$("input[name='signup_submit']").val("<?php echo __('please_wait'); ?>").attr("disabled","disabled");
			$(".response_signup_error").remove();
		},
		success:function(res)
		{
			if(res.error)
			{
				$("form[name='signupForm'] em#signup_code_phone_error").html("");
				$("form[name='signupForm'] em.response_signup_error").html("");
				$(".response_signup_error").remove();
				$("input[name='signup_submit']").val("<?php echo __('create_account'); ?>").removeAttr("disabled");
				$.each(res.error,function(k,v){
					if(k == "country_code" || k == "mobile_number") {
						$("form[name='signupForm'] em#signup_code_phone_error").html(ucFirst(v));
					} else {
						$("form[name='signupForm'] :input[name='"+k+"']").after('<em class="response_signup_error error_valids">'+ucFirst(v)+'</em>');
					}
				}); 
				
			}
			if(res.redirect)
			{
				window.location = res.redirect;
			}
		}
	});
	tab_index("signupForm");
}

function deleteSetDataSession(type)
{
	$.ajax({
		url:'<?php echo URL_BASE;?>users/deleteSetDataSession',
		type:'POST',
		dataType:'json',
		data:"type="+type,
		success:function()
		{
			$("input[name='facebook_alert']").val("1");
		}
	});
}
</script>
