<?php
defined('SYSPATH') OR die("No direct access allowed.");

$sms_account_id = '';
$sms_auth_token = '';
$sms_from_number = '';
?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div style="display: none">
    <svg id="close_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 212.982 212.982" style="enable-background:new 0 0 212.982 212.982;">
        <g>
            <path d="M131.804,106.491l75.936-75.936c6.99-6.99,6.99-18.323,0-25.312
                  c-6.99-6.99-18.322-6.99-25.312,0l-75.937,75.937L30.554,5.242c-6.99-6.99-18.322-6.99-25.312,0c-6.989,6.99-6.989,18.323,0,25.312
                  l75.937,75.936L5.242,182.427c-6.989,6.99-6.989,18.323,0,25.312c6.99,6.99,18.322,6.99,25.312,0l75.937-75.937l75.937,75.937
                  c6.989,6.99,18.322,6.99,25.312,0c6.99-6.99,6.99-18.322,0-25.312L131.804,106.491z"/>
        </g>
    </svg>
    <svg id="next-marketing" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" enable-background="new 0 0 24 24">
        <g>
            <path d="M10 17.7c-2-.4-3.3-.7-5-.7 0 2 .2 4 .9 5.9.2.7.7 1.1 1.4 1.1h1.9c1 0 1.7-.9 1.5-1.9-.5-1.4-.7-3-.7-4.4zM19 0c-1 0-1.9.5-2.7 1.4C13.7 3.7 8.8 5 5 5c-2.8 0-5 2.2-5 5v.1c0 2.8 2.2 5 5 5 4 0 9 1.3 11.6 3.8.7.7 1.5 1.1 2.4 1.1 3.3 0 5-5 5-10S22.3 0 19 0zm0 17c-.4-.1-1.3-1.5-1.8-4 1.5-.1 2.8-1.4 2.8-3s-1.2-2.8-2.8-3c.4-2.5 1.3-4 1.8-4 .6.1 2 2.7 2 7s-1.4 6.9-2 7z"/>
        </g>
    </svg>
</div>

<div class="domain_det_list">
     <div class="account_lft_det">
            <div class="acc_tit"><h2>Domain Details</h2></div>
            <div class="acc_det">
                <p>Everything you need to know about your domain</p>
            </div>
        </div>
        <div class="account_rgt_det domain_det_sec">
            <div class="rgt_lay">
                <div class="domain_top_det"><p>You added jallicart.com on April 20, 2017.</p></div>
                <div class="domain_mid_det">
                    <h3>DOMAIN STATUS</h3>
                    <span class="domain_sta">Setup required</span>
                    <p>Setup is required.</p>
                    <a class="setup_instructions" href="#">View instructions</a>
                </div>
            </div>
        </div>
</div>

<div class="domain_setup_popup_out">
    <div class="domain_setup_popup">
        <div class="domain_popup_top">
            <h1>Set up your domain</h1>
            <a class="close_ico"><svg role="img" viewBox="0 0 16 16" class="icon_13"><g><use xlink:href="#close_icon" class="icon_13"></use></g></svg></a>
        </div>
        <div class="domain_popup_middle">
            <div class="chanel_popup_scroll">
                <div class="recomamded_store">
                    <div class="ui_banner">
                        <div class="ui_banner_ribbon">
                            <svg role="img" viewBox="0 0 16 16" class="size_24"><g><use xlink:href="#next-marketing" class="size_24"></use></g></svg>
                        </div>
                        <div class="ui_banner_content"><p>Domain changes can take up to 24 hours to take effect.</p></div>
                    </div>
                    <div class="domain_bot_det">
                    <p><span>Your domain is set up with <strong> GoDaddy </strong></span></p>
                    <ol>
                        <li>Log in to your domain provider account.</li>
                        <li>Set your <strong>A record</strong> to our IP address: <strong>23.227.38.32</strong></li>
                    </ol>
                    <p class="type--subdued">Find out how to <a href="#">set up domains</a> at the Taximobility Help Center.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="domain_popup_bot">
            <div class="popup_bot_inn">
                <input type="button" class="btn_primary" value="Done"/>
            </div>
        </div>
    </div>
</div>
<div id="fade"></div>
<script>
function sms_settings(){


}
$(document).ready(function () {	
    $('.setup_instructions').click(function(){
        $('.domain_setup_popup_out').show();
        $('#fade').show();
        $('.domain_setup_popup_out').addClass('popup_open');
    });
    $('.close_ico').click(function(){
        $('.domain_setup_popup_out').hide();
        $('#fade').hide();
        $('.domain_setup_popup_out').removeClass('popup_open');
    });
	
	$("#frm_sms_settings").validate({
		rules: {			   
			sms_account_id: "required",
			sms_auth_token: "required",
			sms_from_number: {
				required:true,
				number:true
			}
		},
		messages: {
			sms_account_id: "<?php echo __('enter_smsacc'); ?>",
			sms_auth_token: "<?php echo __('enter_smsauth'); ?>",
			sms_from_number:{
				required :"<?php echo __('enter_smsfrom'); ?>",
				number :"<?php echo __('valid_smsfrom'); ?>",
			}
		}
	});		
});
</script>


