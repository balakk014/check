<?php
defined('SYSPATH') OR die("No direct access allowed.");

$sms_account_id = '';
$sms_auth_token = '';
$sms_from_number = '';
?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div class="account_outer">
	<form action="" name="frm_sms_settings" id="frm_sms_settings" method="post" onsubmit="return sms_settings()">
    <div class="account_det_list" style="border: none;">
        <div class="account_lft_det">
            <div class="acc_tit"><h2><?php echo __('sms_settings'); ?></h2></div>
            <div class="acc_det">
                <p><?php echo __('sms_settings_desc'); ?></p>
            </div>
        </div>
        <div class="account_rgt_det">
            <div class="rgt_lay sms_lay">
                <div class="sms_top_sec">
                    <div class="payment_logo">
                        <img width="100" height="25" src="<?php echo URL_BASE; ?>public/cloud_package/twillo_logo.svg"/>
                    </div>
                    <div class="pay_card_det">
                        <div class="pay_crd_lst">
                            <p><a href="https://www.twilio.com/" title="Twilio sms" target="_blank"><?php echo __('twilio_sms'); ?></a></p>
                            <p><?php echo __('twilio_sms_desc'); ?></p>
                        </div>
                    </div>
                    <div class="div_lft">
                        <div class="small_sel">
                            <select class="form_control" name="sms_gateway" id="sms_gateway">
                                <option value='twilio' selected="selected">Twilio</option>
                            </select>
                        </div>
						
                    </div>
                </div>
                    <div class="sms_sett_opt">
                        <div class="sms_mid_sec">
                            <div class="sms_mid_lft">
                                <div class="form_group">
                                    <label class="small_control_label">Account ID</label>
                                    <input type="text" class="small_form_control required" value="<?php if(isset($postvalue) && array_key_exists('sms_account_id',$postvalue)){ echo $postvalue['sms_account_id']; }else{ echo $sms_account_id; } ?>" name="sms_account_id" id="sms_account_id" maxlength="50"/>
                                    <?php
                                    if (isset($errors) && array_key_exists('sms_account_id', $errors)) {
                                        echo "<span class='error'>" . ucfirst($errors['sms_account_id']) . "</span>";
                                 }
                                    ?>
                                </div>
                                <div class="form_group">
                                    <label class="small_control_label">Auth Token</label>
                                    <input type="text" class="small_form_control" value="<?php if(isset($postvalue) && array_key_exists('sms_auth_token',$postvalue)){ echo $postvalue['sms_auth_token']; }else{ echo $sms_auth_token;} ?>" name="sms_auth_token" id="sms_auth_token" maxlength="50"/>
                                    <!--<div class="next_input_wrapper"><p><input type="checkbox" value=""/> Show</p></div>!-->
                                    <?php
                                    if (isset($errors) && array_key_exists('sms_auth_token', $errors)) {
                                        echo "<span class='error'>" . ucfirst($errors['sms_auth_token']) . "</span>";
                                    }
                                    ?>
                                </div>
                                <div class="form_group">
                                    <label class="small_control_label">SMS from number</label>
                                    <input type="text" class="small_form_control" value="<?php if(isset($postvalue) && array_key_exists('sms_from_number',$postvalue)){ echo $postvalue['sms_from_number']; }else{ echo $sms_from_number; } ?>" name="sms_from_number" id="sms_from_number" maxlength="50"/>
                                    <?php
                                    if (isset($errors) && array_key_exists('sms_from_number', $errors)) {
                                        echo "<span class='error'>" . ucfirst($errors['sms_from_number']) . "</span>";
                                    }
                                    ?>
                                </div>
                                <?php /*<div class="checkbox_custom">
                                    <input id="check_default" <?php if($default == 1){ echo 'checked';} ?> name="default_sms" type="checkbox" name="check_default" value="1"/>
                                    <label for="check_default"><?php echo __('default_sms'); ?></label>
                                </div>
                                * */ ?>
                            </div>
                        </div>
                    </div>
                    <div class="sms_bot_sec">
						<div class="align_right">
							<input class="common_butt" type="submit" value="Activate" name="btn_sms_activate" id="btn_sms_activate"/>
						</div>
					</div> 
            </div>
        </div>
    </div>
 </form>
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


