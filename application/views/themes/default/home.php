<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div id="home">
	<?php if($home_array['banner_image'] != '') {
		if(file_exists(DOCROOT.PUBLIC_UPLOADS_LANDING_FOLDER.$home_array['banner_image'])) { ?>
			<img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?><?php echo $home_array['banner_image']; ?>" />
		<?php } else { ?>
			<img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>default_banner.png" />
		<?php } } else { ?>
			<img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>default_banner.png" />
		<?php } ?>
	<div class="inner">
		<?php echo $home_array['banner_content']; ?>
            <div class="passenger_app">
                <a target="_blank" href="<?php echo $home_array['passenger_app_ios_store_link']; ?>" title="app store"><img alt="app_store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>app_store.png" /></a>
		<a target="_blank" href="<?php echo $home_array['passenger_app_android_store_link']; ?>" title="play store"><img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>play_store.png" /></a>
            </div>
	</div>
</div>
<div id="join-us-driver" style="<?php echo "background:#".$home_array['app_bg_color']; ?>">
	<div class="inner">
		<?php echo $home_array['app_content']; ?>
		<a target="_blank" href="<?php echo $home_array['app_ios_store_link']; ?>" title="app store"><img alt="app_store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>app_store.png" /></a>
		<a target="_blank" href="<?php echo $home_array['app_android_store_link']; ?>" title="play store"><img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>play_store.png" /></a>
	</div> 
</div>
<div id="about-us" style="<?php echo "background:#".$home_array['about_bg_color']; ?>">
    <div class="inner">
		<div class="about_lft">
			<?php echo $home_array['about_us_content']; ?>
		</div>
		<div class="about_rgt">
			<img alt="Taxi" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>about_rgt.jpg" />
		</div>
	</div>
</div>
<div id="contact-us" style="<?php echo "background:#".$home_array['footer_bg_color']; ?>">
    <div class="inner">
		<div class="address_det">
			<h3>Contact Us</h3>
			<?php echo $home_array['contact_us_content']; ?>
		</div>
		<div class="contact_det" id="contact_det">
			<h3>Mail Us</h3>
			<form name="company_form" method="post" onsubmit="return companyFormSubmit();">
                            <ul>
                                <li>
				<input type="text" value="" name="name" placeholder="Name" maxlength="50" />
				<em id="name_error"></em>
                                </li>
                                <li>
				<input type="text" value="" name="phone" placeholder="Mobile Number" maxlength="16"/>
				<em id="phone_error"></em>
                                </li>
                                <li>
				<input type="text" value="" name="email" placeholder="Email"  maxlength="85"/>
				<em id="email_error"></em>
                                </li>
                                <li>
				<select name="type">
					<option value=""><?php echo __('select_type'); ?></option>
					<option value="1"><?php echo __('driver'); ?></option>
					<option value="2"><?php echo __('passenger'); ?></option>
				</select>
				<em id="type_error"></em>
                                </li>
                                <li>
				<textarea name="message" placeholder="Drop your Message here..." maxlength="250" minlength="10" ></textarea>
				<em id="message_error"></em>
                                </li>
                                <li>
				<input type="submit" id="form_submit_btn" value="Submit" name="company_form_submit"/>
				<input type="submit" id="loading_btn" value="Please Wait..." disabled="disabled" style="display:none;"/>
                                </li>
                            </ul>
			</form>
		</div>
		<div class="follow_det">
			<h3>Follow Us</h3>
			<a target="_blank" href="<?php echo $home_array['facebook_follow_link']; ?>" title="Facebook"><img alt="Facebook" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>facebook.png" /></a>
			<a target="_blank" href="<?php echo $home_array['google_follow_link']; ?>" title="google pluse"><img alt="google pluse" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>google_pluse.png" /></a>
			<a target="_blank" href="<?php echo $home_array['twitter_follow_link']; ?>" title="twitter"><img alt="twitter" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>twitter.png" /></a>
		</div>
	</div>
</div>
<div id="fade"></div>
<script>
function companyFormSubmit()
{
	var validate = 1;
	var name = document.company_form.name.value.trim();
	var email = document.company_form.email.value.trim();
	var phone = document.company_form.phone.value.trim();
	var type = document.company_form.type.value.trim();
	var message = document.company_form.message.value.trim();
	var l = email.indexOf("@");
	var h = email.lastIndexOf(".");
	var p = "!#$%^&*()+=[]\\';,/{}|\":<>?";
	for (var v = 0; v < document.company_form.email.value.length; v++) {
		if (p.indexOf(document.company_form.email.value.charAt(v)) != -1) {
			$("#email_error").html("Remove special characters");
			validate = 0;
			return false;
		}
	}
	
	if (name == "") {
		$("#name_error").html("Enter your name");
		validate = 0;
	} else {
		$("#name_error").html("");
	}
	
	if (email == "") {
		$("#email_error").html("Enter your email ID");
		validate = 0;
	} else if (l < 1 || h < l + 2 || h + 2 >= email.length) {
		$("#email_error").html("Invalid email");
		validate = 0;
	} else {
		$("#email_error").html("");
	}
	
	if (phone == "") {
		$("#phone_error").html("Enter your mobile number");
		validate = 0;
	} else {
		$("#phone_error").html("");
	}

	if (type == "") {
		$("#type_error").html("Choose your type");
		validate = 0;
	} else {
		$("#type_error").html("");
	}
	
	if (message == "") {
		$("#message_error").html("Enter your message");
		validate = 0;
	} else {
		$("#message_error").html("");
	}
	
	if(validate == 1) {
		$('#form_submit_btn').hide();
		$('#loading_btn').show();
		document.company_form.submit();
	} else {
		return false;
	}
}
</script>
