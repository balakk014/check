<?php
defined('SYSPATH') OR die("No direct access allowed.");
?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 
            <form method="POST" enctype="multipart/form-data" class="form" action="" >
                <table class="0" cellpadding="5" cellspacing="0" width="85%">
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sms_account_id'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="sms_account_id" id="sms_account_id" title="<?php echo __('enter_sms_account_id'); ?>" maxlength="50" value="<?php echo isset($smssettings) &&!array_key_exists('sms_account_id',$postvalue)? trim($smssettings[0]['sms_account_id']):$postvalue['sms_account_id']; ?>"></div>
                    <?php if(isset($errors) && array_key_exists('sms_account_id',$errors)){ echo "<span class='error'>".ucfirst($errors['sms_account_id'])."</span>";}?></td>
                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sms_auth_token'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="sms_auth_token" id="sms_auth_token" title="<?php echo __('enter_sms_auth_token'); ?>" maxlength="100" value="<?php echo isset($smssettings) && (!array_key_exists('sms_auth_token', $errors)) ? $smssettings[0]['sms_auth_token'] : $validator['sms_auth_token']; ?>" > </div>
                            <?php if(isset($errors) && array_key_exists('sms_auth_token',$errors)){ echo "<span class='error'>".ucfirst($errors['sms_auth_token'])."</span>";}?></td>
                        </td>

                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sms_from_number'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="sms_from_number" id="sms_from_number" title="<?php echo __('enter_sms_from_number'); ?>" maxlength="50" value="<?php echo isset($smssettings) && (!array_key_exists('sms_from_number', $errors)) ? $smssettings[0]['sms_from_number'] : $validator['sms_from_number']; ?>"></div>
                            <?php if(isset($errors) && array_key_exists('sms_from_number',$errors)){ echo "<span class='error'>".ucfirst($errors['sms_from_number'])."</span>";}?></td>
                    </tr>

                    <tr>
                        <td valign="top">&nbsp;</td>
                        <td style="padding-left:0px;">
                            <div class="new_button">  <input type="submit" name="editsms_settings_submit" title ="<?php echo __('button_update'); ?>" value="<?php echo __('button_update'); ?>"></div>
                            <div class="new_button"> <input type="reset" name="editsms_settings_reset" title="<?php echo __('button_reset'); ?>" value="<?php echo __('button_reset'); ?>"></div>
                        </td></tr>
                </table>

            </form>
            <br/><br/>

        </div>

        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt" ></div></div>
    </div>

</div>
<script language="javascript" type="text/javascript">

 $(document).ready(function(){

var field_val = $("#facebook_key").val();
$("#facebook_key").focus().val("").val(field_val);
	
});
</script>
