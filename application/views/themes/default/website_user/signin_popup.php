<div class="signin_form" style="display: none;">
<?php $session = Session::instance(); ?>
	<h3><?php echo __('button_signin'); ?></h3>
<!--	<a href="javascript:;" class="close_butt">&nbsp;</a>-->
	<form name="signin_form" method="post" autocomplete="off">
		<input type="hidden" name="fb_id" value="<?php echo $session->get("fb_id"); ?>"/>
		<input type="hidden" name="fb_access_token" value="<?php echo $session->get("fb_access_token"); ?>"/>
		<ul>
			<li>
				<div class="full_name_mobile">
					<span>
						<input type="text" class="country_code_restrict" tabindex="1" onpaste="return false;" title="Country Code" name="country_code" value="<?php if(isset($_COOKIE["country_code"])) { echo $_COOKIE["country_code"]; } ?>" placeholder="<?php echo TELEPHONECODE ?>*" maxlength="5"/>
					</span>
					<div class="mob_no">
						<input type="text" name="mobile_number" tabindex="2" class="txtboxToFilter" value="<?php if(isset($_COOKIE["mobile_number"])) { echo $_COOKIE["mobile_number"]; } ?>" onpaste="return false;" maxlength="15" placeholder="9876543210*" title="<?php echo __('mobile_number'); ?>"/>
					</div>
				</div>
				<em id="signin_code_phone_error"></em>
			</li>
			<li>
				<div class="full_name">
					<input type="password" name="password" tabindex="3" onpaste="return false;" maxlength="24" placeholder="<?php echo __('password_label'); ?>*" title="<?php echo __('password_label'); ?>" value="<?php if(isset($_COOKIE["passenger_password"])) { echo $_COOKIE["passenger_password"]; } ?>"/>
				</div>
			</li>
			<li style="margin-top: 5px;">
				<div class="rember">
					<input type="checkbox" name="remember_me" value="1" tabindex="4"/><span><?php echo __('remember_me'); ?></span>
				</div>
				<p><a class="forgot_password" href="javascript:;" title="<?php echo __('forgot_password'); ?>"><?php echo __('forgot_password'); ?></a></p>
			</li>
			<li>
				<div class="sub_butt">
					<input type="button" name="signin_submit" tabindex="5" onclick="signinSubmit();" value="<?php echo __('button_signin'); ?>" title="<?php echo __('button_signin'); ?>"/>
				</div>
			</li>
		</ul>
	</form>
	<div class="new_acc_create">
		<h2><?php echo __('dont_have_account'); ?></h2>
		<a href="javascript:;" class="sign_up_home" title="<?php echo __('button_signup'); ?>"><?php echo __('button_signup'); ?></a>
		<label>&nbsp;</label>
		<a onclick="facebookconnect_login();" href="javascript:;"><img alt="facebook" src="<?php echo URL_BASE;?>public/frontend/logged_in/images/facebook.png"/></a>
	</div>
</div>

<script type="text/javascript">
/** Signin form tab focus within a form **/
$(document).ready( function () {
	tab_index("signin_form");
});

$("form[name='signin_form'] :input").keypress(function (e) {
	if (e.which == 13) {
		signinSubmit();
		return false;
	}
});

/** Signin form validate and logged in **/
function signinSubmit()
{
	$.ajax({
		url:'<?php echo URL_BASE; ?>users/signin_passenger',
		type:'post',
		dataType:'json',
		data:$("form[name='signin_form']").serialize(),
		beforeSend:function() {
			$("input[name='signin_submit']").val("<?php echo __('please_wait'); ?>").attr("disabled","disabled");
			$(".response_signin_error").remove();
		},
		success:function(json)
		{
			if(json.error)
			{
				$("form[name='signin_form'] em#signin_code_phone_error").html("");
				$("form[name='signin_form'] em.response_signin_error").html("");
				$(".response_signin_error").remove();
				$("input[name='signin_submit']").val("<?php echo __('button_signin'); ?>").removeAttr("disabled");
				$.each(json.error,function(k,v){
					if(k == "password") {
						$("form[name='signin_form'] :input[name='"+k+"']").after('<em class="response_signin_error '+k+'">'+ucFirst(v)+'</em>');
					}
					if(k == "country_code" || k == "mobile_number") {
						$("form[name='signin_form'] em#signin_code_phone_error").html(ucFirst(v));
					}
				}); 
			}
			if(json.redirect)
			{
				window.location = json.redirect;
			}
		}
	});
	tab_index("signin_form");
}
function facebookconnect_login()
{
	location.href;
	var path = "<?php echo URL_BASE; ?>";
	win2 = window.open(path+"users/fconnect_login/",null,"width=750,location=0,status=0,height=500"),checkChild()
}
</script>
