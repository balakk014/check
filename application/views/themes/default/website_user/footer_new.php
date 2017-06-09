<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<footer>
	<div class="footer_top_out">
		<div class="inner">
			<div class="mob_support">
				<strong><?php echo __("Support"); ?></strong>
				<ul>
					<li><a href="<?php echo COMMON_IOS_PASSENGER_APP; ?>" target="_blank"><img alt="ios" src="<?php echo URL_BASE; ?>public/frontend/logged_in/images/ios.png"/></a></li>
					<li><a href="<?php echo COMMON_ANDROID_PASSENGER_APP; ?>" target="_blank"><img alt="android" src="<?php echo URL_BASE; ?>public/frontend/logged_in/images/android.png"/></a></li>
				</ul>
			</div>
			<div class="social_share">
				<strong><?php echo __("Connect_with_us"); ?></strong>
				<ul>
					<li><a class="facebook2" href="<?php echo FB_SHARE; ?>" title="<?php echo __("facebook"); ?>" rel="nofollow" target="_blank">&nbsp;</a></li>
					<li><a class="twitter" href="<?php echo TW_SHARE; ?>" title="<?php echo __("twitter"); ?>" rel="nofollow" target="_blank">&nbsp;</a></li>
					<li><a class="google" href="<?php echo GOOGLE_SHARE; ?>" title="<?php echo __("google_plus"); ?>" rel="nofollow" target="_blank">&nbsp;</a></li>
					<li><a class="linked_in" href="<?php echo LINKEDIN_SHARE; ?>" title="<?php echo __("linkedin"); ?>" rel="nofollow" target="_blank">&nbsp;</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="footer_outer">
		<div class="inner">
			<div class="footer">
				<div class="footer_products">
					<h6><?php echo __("learn_more"); ?></h6>
					<ul>
						<li><a href="<?php echo URL_BASE;?>about-us.html" title="<?php echo __('aboutus');?>"><?php echo __('aboutus');?></a></li>
						<li><a href="<?php echo URL_BASE;?>termsconditions.html" rel="nofollow" title="<?php echo __('terms_conditions');?>"><?php echo __('terms_conditions');?></a></li>
						<li><a href="<?php echo URL_BASE;?>privacypolicy.html" rel="nofollow" title="<?php echo __('privacy_policy');?>"><?php echo __('privacy_policy');?></a></li>
						<li><a href="<?php echo URL_BASE;?>tutorial.html" title="<?php echo __('tutorial');?>"><?php echo __('tutorial');?></a></li>
						<li><a target="_blank" href="<?php echo URL_BASE;?>blog" title="Blog" >Blog</a></li>
					</ul>
				</div>
				<div class="footer_products">
					<h6><?php echo __("company"); ?></h6>
					<ul>
						<li><a href="<?php echo URL_BASE;?>features.html" title="<?php echo __('features_menu');?>"><?php echo __('features_menu');?></a></li>  
						<li><a href="<?php echo URL_BASE; ?>portfolios.html" title="<?php echo __('Portfolios');?>"><?php echo __('Portfolios');?></a></li> 
						<li ><a href="<?php echo URL_BASE; ?>case-studies.html" title="<?php echo __('casestudies');?>"><?php echo __('casestudies');?></a></li> 
						<li><a href="<?php echo URL_BASE;?>faq.html" title="<?php echo __('FAQ');?>"><?php echo __('FAQ');?></a></li>
						<li><a href="<?php echo URL_BASE;?>downloads.html?filename=Corporate_Document.pdf" title="<?php echo __("corporate_document"); ?>"><?php echo __("corporate_document"); ?></a></li>
					</ul>
				</div>
				<div class="footer_products">                       
					<h6 class="footer_title"><?php echo __("solutions"); ?></h6>
					<ul class="footer_menu">
						<li><a href="<?php echo URL_BASE;?>taxi-booking-and-dispatching.html" title="<?php echo __("taxi_transportation"); ?>"><?php echo __("taxi_transportation"); ?></a></li>
						<li><a href="<?php echo URL_BASE;?>delivery-assistance.html" title="<?php echo __("delivery_logistics"); ?>"><?php echo __("delivery_logistics"); ?></a></li>
					</ul>
				</div>
				<div class="footer_products">
					<h6><?php echo ucwords(__("contactus_label")); ?></h6>                        
					<p>1159 Sonora Court,</p>
					<p>Suite 204, Sunnyvale,</p>
					<p>CA 94086, United States.</p>
					<p>Phone: +1 (202) 657 -6901</p>                      
				</div>  
			</div>
			<div class="foot_bottom">
				<div class="subscribe">
					<strong><?php echo __("subscribe"); ?></strong>
					 <form method="post" action="<?php echo URL_BASE.'users/subscribe'; ?>" name="subscribe_form" onsubmit="return subscribe_validation();" autocomplete="off">
						<input placeholder="<?php echo __("enter_your_email"); ?>" type="text" name="subscribe_email" />
						<input type="submit" value="<?php echo __("submit"); ?>"/>
						<span class="sub_err" id="subscribe_email_error"></span>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="copy_r_outer">
		<div class="inner">
			<p><?php echo $footer_contents[0]['site_copyrights']; ?></p>
		</div>
	</div>
</footer>
<script>
function subscribe_validation()
{
	var email = document.subscribe_form.subscribe_email.value.trim();
	var pattern = /^(([^<>()\[\]\\.%!#$^&*(),;:\s@"]+(\.[^<>()\[\]\\.%!#$^&*(),;:\s@"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	var validate_result = pattern.test(email);
	if (email == "") {
		$("#subscribe_email_error").html("Enter Your Email-ID")
	} else if (!validate_result) {
		$("#subscribe_email_error").html("Invalid Email")
	} else {
		$("#subscribe_email_error").html("");
		document.subscribe_form.submit();
		return true;
	}
	return false;
}
</script>
