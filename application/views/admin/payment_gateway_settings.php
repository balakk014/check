<?php
defined('SYSPATH') OR die("No direct access allowed.");
?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 
            <form method="POST" enctype="multipart/form-data" class="form" action="" >
               <?php /* <table class="0" cellpadding="5" cellspacing="0" width="100%">
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('payment_gatway_name'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="payment_gatway_name" id="payment_gatway_name" title="<?php echo __('enter_payment_gatway_name'); ?>" maxlength="250" value="<?php echo isset($payment_settings) && (!array_key_exists('payment_gatway', $errors)) ? $payment_settings[0]['payment_gatway'] : $validator['payment_gatway_name']; ?>"></div>
                    <?php if(isset($errors) && array_key_exists('payment_gatway_name',$errors)){ echo "<span class='error'>".ucfirst($errors['payment_gatway_name'])."</span>";}?></td>
                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('payment_description'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="description" id="description1" title="<?php echo __('enter_payment_description'); ?>" maxlength="250" value="<?php echo isset($payment_settings) && (!array_key_exists('description', $errors)) ? $payment_settings[0]['description'] : $validator['description']; ?>" > </div>
                            <?php if(isset($errors) && array_key_exists('description',$errors)){ echo "<span class='error'>".ucfirst($errors['description'])."</span>";}?></td>
                        </td>

                    </tr>                               
			<input type="hidden" name="currency_code" value="USD" >
			<input type="hidden" name="currency_symbol" value="$" >

                    <?php /* <tr>
			
                        <td valign="top" width="20%"><label><?php echo __('currency_code'); ?></label><span class="star">*</span></td>   
                        <td>
				<div class="formRight">
				<div class="selector" id="uniform-user_type">
				<span><?php echo __('select_label'); ?></span>
				<select name="currency_code" id="currency_code" title="<?php echo __('enter_currency_code'); ?>" >
				<option value="">-- Select Currency Code--</option>
				<?php foreach($currency_code as $key=>$currencycode){ ?>
				<option value='<?php echo $currencycode;?>' <?php if($payment_settings[0]['currency_code'] == $currencycode) { echo 'selected=selected'; } ?> ><?php echo $currencycode;?></option>
				<?php } ?>                        
                            <?php echo isset($payment_settings) && (!array_key_exists('currency_code', $errors)) ? $payment_settings[0]['currency_code'] : $validator['currency_code']; ?></select>
                            </div>
                            </div>
                            <span class="error"><?php echo isset($errors['currency_code']) ? $errors['currency_code'] : ''; ?></span>
                            </td>
                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('currency_symbol'); ?></label><span class="star">*</span></td>   
                        <td>
			<div class="formRight">
			<div class="selector" id="uniform-user_type">
			<span><?php echo __('select_label'); ?></span>
				<select name="currency_symbol" id="currency_symbol" title="<?php echo __('enter_currency_symbol'); ?>" >
				<option value="">-- Select Currency Symbol--</option>
				<?php foreach($currency_symbol as $key=>$currencysymbol){ ?>
				<option value='<?php echo $currencysymbol;?>' <?php if($payment_settings[0]['currency_symbol'] == $currencysymbol) { echo 'selected=selected'; } ?> ><?php echo $currencysymbol;?></option>
				<?php } ?>
				<?php echo isset($payment_settings) && (!array_key_exists('currency_symbol', $errors)) ? $payment_settings[0]['currency_symbol'] : $validator['currency_symbol']; ?>
				</select>
			</div>
			</div>      
                        <span class="error"><?php echo isset($errors['currency_symbol']) ? $errors['currency_symbol'] : ''; ?></span>
                        </td>
                    </tr> * ?>
                    <tr>
			<tr>
                        <td valign="top" width="20%"><label><?php echo __('payment_method'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
                        
                        <input type="radio" name="payment_method" id="payment_method" title="<?php echo __('enter_payment_method'); ?>"  value="T" <?php if($payment_settings[0]['payment_method']=='T'){ echo 'checked=checked';}?> ><?php echo 'Test Mode'; ?>
                        
                        <input type="radio" name="payment_method" id="payment_method" title="<?php echo __('enter_payment_method'); ?>"  value="L" <?php if($payment_settings[0]['payment_method']=='L'){ echo 'checked=checked';}?>><?php  echo 'Live Mode'; ?>
                        </div>
                    <?php if(isset($errors) && array_key_exists('payment_method',$errors)){ echo "<span class='error'>".ucfirst($errors['payment_method'])."</span>";}?></td>
                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('paypal_api_username'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="paypal_api_username" id="paypal_api_username" title="<?php echo __('enter_paypal_api_username'); ?>" maxlength="250" value="<?php echo isset($payment_settings) && (!array_key_exists('paypal_api_username', $errors)) ? $payment_settings[0]['paypal_api_username'] : $validator['paypal_api_username']; ?>" > </div>
                            <?php if(isset($errors) && array_key_exists('paypal_api_username',$errors)){ echo "<span class='error'>".ucfirst($errors['paypal_api_username'])."</span>";}?></td>
                        </td>

                    </tr> 
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('paypal_api_password'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="paypal_api_password" id="paypal_api_password" title="<?php echo __('enter_paypal_api_password'); ?>" maxlength="250" value="<?php echo isset($payment_settings) && (!array_key_exists('paypal_api_password', $errors)) ? $payment_settings[0]['paypal_api_password'] : $validator['paypal_api_password']; ?>"></div>
                            <?php if(isset($errors) && array_key_exists('paypal_api_password',$errors)){ echo "<span class='error'>".ucfirst($errors['paypal_api_password'])."</span>";}?></td>
                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('paypal_api_signature'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="paypal_api_signature" id="paypal_api_signature"  title="<?php echo __('enter_paypal_api_signature'); ?>" maxlength="250" value="<?php echo isset($payment_settings) && (!array_key_exists('paypal_api_signature', $errors)) ? $payment_settings[0]['paypal_api_signature'] : $validator['paypal_api_signature']; ?>"></div>
                            <?php if(isset($errors) && array_key_exists('paypal_api_signature',$errors)){ echo "<span class='error'>".ucfirst($errors['paypal_api_signature'])."</span>";}?></td>
                    </tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td></tr>	
		<tr>
			<td valign="top" width="20%"><label><?php echo __('payment_module_settings'); ?></label></td>   
		</tr>
 		</table> */ ?>
		<div class="overflow-block">
		<table class="0 sTable responsive payment_gtes" cellpadding="5" cellspacing="0" width="100%" style="border-top:1px solid #cdcdcd;">
			<tr>
				<td align="center" valign="top" width="10%"><label><?php echo __('payment_module_status'); ?></label></td>
				<td align="center" valign="top" width="10%"><label><?php echo __('payment_module_name'); ?></label></td>
				<td align="center" valigin="top" width="10%"><?php echo __('default_gateway'); ?></td>
			</tr>	

			    <?php foreach($gatway_list as $resultset) { ?>
				<tr>
				<td align="center"><input type="checkbox" name="paymodstatus[]" value="<?php echo $resultset['pay_mod_id']; ?>" <?php if($resultset['pay_mod_active']=='1'){ echo 'checked="checked"'; } ?>/></td>
				<td align="center"><img src="<?php echo URL_BASE;?>public/common/images/<?php echo $resultset['pay_mod_image'] ?>"><div class="new_input_field"><label><?php echo $resultset['pay_mod_name'];?></label></div></td>
				<td align="center"><input type="radio" name="default[]" value="<?php echo $resultset['pay_mod_id'];?>"  <?php if($resultset['pay_mod_default']=='1'){ echo 'checked="checked"'; } ?> /></td>
				<input type="hidden" name="payid[]" value="<?php echo $resultset['pay_mod_id'];?>"  /></td>
				</tr>
				<?php } ?>
                    <tr>			
                        <td colspan="3"><?php if(isset($errors) && array_key_exists('paymodstatus',$errors)){ echo "<span class='error'>".ucfirst($errors['paymodstatus'])."</span>";}?></td>
                    </tr>
                </table>
				</div>
                <div class="bottom_part">
                   <p class="star">*<?php echo __('required_label'); ?></p>
                   <div style="float:left;width:100%;">
			<div class="new_button">  <input type="submit" name="editpaymentsettings_submit" title ="<?php echo __('button_update'); ?>" value="<?php echo __('button_update'); ?>"></div>
                        <div class="new_button"> <input type="reset" name="editpaymentsettings_reset" title="<?php echo __('button_reset'); ?>" value="<?php echo __('button_reset'); ?>"></div>
                </div>
                </div>
            </form>
        </div>
    </div>

</div>
<script language="javascript" type="text/javascript">

$(document).ready(function(){


	var field_val = $("#payment_gatway_name").val();
	$("#payment_gatway_name").focus().val("").val(field_val);

});

</script>
