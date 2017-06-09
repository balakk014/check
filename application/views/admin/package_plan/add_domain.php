<?php
defined('SYSPATH') OR die("No direct access allowed.");

$sms_account_id = '';
$sms_auth_token = '';
$sms_from_number = '';
?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div style="display: none;">
    <svg version="1.1" id="question_mart" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
        viewBox="0 0 92 92" style="enable-background:new 0 0 92 92;" xml:space="preserve">
        <g>
            <path d="M45.386,0.004C19.983,0.344-0.333,21.215,0.005,46.619c0.34,25.393,21.209,45.715,46.611,45.377
                c25.398-0.342,45.718-21.213,45.38-46.615C91.656,19.986,70.786-0.335,45.386,0.004z M45.25,74l-0.254-0.004
                c-3.912-0.116-6.67-2.998-6.559-6.852c0.109-3.788,2.934-6.538,6.717-6.538l0.227,0.004c4.021,0.119,6.748,2.972,6.635,6.937
                C51.904,71.346,49.123,74,45.25,74z M61.705,41.341c-0.92,1.307-2.943,2.93-5.492,4.916l-2.807,1.938
                c-1.541,1.198-2.471,2.325-2.82,3.434c-0.275,0.873-0.41,1.104-0.434,2.88l-0.004,0.451H39.43l0.031-0.907
                c0.131-3.728,0.223-5.921,1.768-7.733c2.424-2.846,7.771-6.289,7.998-6.435c0.766-0.577,1.412-1.234,1.893-1.936
                c1.125-1.551,1.623-2.772,1.623-3.972c0-1.665-0.494-3.205-1.471-4.576c-0.939-1.323-2.723-1.993-5.303-1.993
                c-2.559,0-4.311,0.812-5.359,2.478c-1.078,1.713-1.623,3.512-1.623,5.35v0.457H27.936l0.02-0.477
                c0.285-6.769,2.701-11.643,7.178-14.487C37.947,18.918,41.447,18,45.531,18c5.346,0,9.859,1.299,13.412,3.861
                c3.6,2.596,5.426,6.484,5.426,11.556C64.369,36.254,63.473,38.919,61.705,41.341z"/>
        </g>
    </svg>
</div>

<form action="" method="post" name="frm_add_daomain" id="frm_add_domain">
<div class="domains_card_width_limit">
    <div class="rgt_lay">
        <h2 class="comm_tit">Connect existing domain</h2>
        <div class="pay_card_det">
            <input type="text" value="<?php echo $live_domain_name;?>" class="form_control" name="domain_name" id="domain_name" placeholder="e.g.example.com"/>
            <p class="input_help_text">Enter the domain name you want to connect</p>
            <?php if (isset($errors) && array_key_exists('domain_name', $errors)) { echo "<span class='error'>" . ucfirst($errors['domain_name']) . "</span>";} ?>   
              <?php if (isset($errors) && array_key_exists('replace_domain_name', $errors)) { echo "<span class='error'>" . ucfirst($errors['replace_domain_name']) . "</span>";} ?>   
        </div>
        <div class="bottom_butt_sec_1">
            <div class="align_right">
                <input type="submit" value="Next" class="common_butt" name="btn_next" id="btn_next" />
            </div>
        </div>
    </div>
</div>
</form>
<div class="ui_footer_help">
    <div class="ui_footer_help_content">
        <div class="help_icon">
            <svg role="img" viewBox="0 0 53 53" class="icon_24"><g><use xlink:href="#question_mart" class="icon_24"></use></g></svg>
        </div>
        <div><p>Learn more about <a href="#">domains</a> at the Taximobility Help Center.</p></div>
    </div>
</div>



<script>
function sms_settings(){


}
$(document).ready(function () {	
	
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


