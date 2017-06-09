<?php 
defined('SYSPATH') OR die("No direct access allowed.");


//For currency code drop down value set
//===================================
$code = isset($data['currency_code']) ? $data['currency_code'] :'';

 /* payment method radio button Checked */
//----------------------------------------
$payment_method_live_checked =$payment_method_test_checked="";
 if(isset($payment_gateway_details['payment_method'])	&& $payment_gateway_details['payment_method']==LIVE_MODE)
 { 
$payment_method_live_checked="checked='checked'"; }

 if(isset($payment_gateway_details['payment_method'])	&& $payment_gateway_details['payment_method']==TEST_MODE)
 { $payment_method_test_checked="checked='checked'"; }
 ?>

<div class="container_content fl clr">
<div class="cont_container mt15 mt10">
<div class="content_top"><div class="top_left"></div><div class="top_center"></div><div class="top_rgt"></div></div>
        <div class="content_middle">
                <form method="post" class="admin_form" name="frmpayment" id="frmpayment" action ="<?php echo URL_BASE;?>sitemanagement/<?php echo $action;?>">
                <table border="0"   cellpadding="10" cellspacing="0" width="100%">
                        <tr>
                                <td valign = "top" width="20%"><label><?php echo __('payment_gatway_name'); ?></label><span class="star">*</span></td>
                                <td>
                                		<input type="text" name="payment_gatway" maxlength="256" id="payment_gatway" readonly ="readonly" value="<?php echo isset($payment_gateway_details['payment_gatway']) && !array_key_exists('payment_gatway',$errors)? trim($payment_gateway_details['payment_gatway']):$validator['payment_gatway'];?>"/>
                                   <span class="error">
                                        <?php echo isset($errors['payment_gatway'])?$errors['payment_gatway']:""; ?>
                                    </span>
                                </td>
                        </tr>

                         <tr>
                                <td valign="top" width="20%"><label><?php echo __('payment_description'); ?></label><span class="star">*</span></td>
                                <td>
                                	<?php $max_len = '255';?>
                                	<textarea  name="description" id="description" class="payment_description" onkeyup="return limitlength(this, <?php echo $max_len;?>,'info_desc_label')" value="" maxlength="255" ><?php echo isset($payment_gateway_details['description']) && !array_key_exists('description',$errors)? trim($payment_gateway_details['description']):$validator['description'];?></textarea>
                                   <span class="error">
                                        <?php echo isset($errors["description"])? $errors["description"]:""; ?>
                                   </span>
                                   <span class="info_label" id="info_desc_label">
                                   <?php echo __('info_maxlength_label',array(':param'=>$max_len));?> 
                                   </span>  
                                </td>
                        </tr>
                         
	                         <tr>      
									        <td class="big_text"><?php echo __('paypal_settings_label');?></td>
									</tr>
									<tr>
											 <td valign="top"> <label><?php echo __('paypal_account_type');?></label></td>
												<td>
												  <input type="radio" name="payment_method" value="L" <?php echo $payment_method_live_checked;?> /> <?php echo __('live_account_label');?>
												  <input type="radio" name="payment_method" value="T" <?php echo $payment_method_test_checked;?> /> <?php echo __('sandbox account_label');?>
									         </td>
									</tr>
									<tr>
											  <td valign="top"> <label><?php echo __('paypal currency code');?></label></td>
												<td> 
												<select name="currency_code">
												  <?php
														// Code to display all currency  format
				                              // ====================================
 																 $selected_currency_code="";

			                                        foreach($all_currency_code as $currency_key) 
			                                        {                                        
			                                       if(isset($payment_gateway_details['currency_code'])){?>
                                                <option  value="<?php echo $currency_key; ?>"
                                                <?php echo ($payment_gateway_details['currency_code'] == $currency_key)?"selected='selected'":"";?> ><?php echo $currency_key;?></option>
                                                
                                                <?php  } }?> 												
											  </select>  
									
											</td>
								  </tr>
								  
								  
						<tr>
                                <td valign="top" ><label>Currency Symbol</label>
                                <span class="star">*</span>
                                        </td>
                                        <td>
                                        <div class="width400">
										<select name="currency_symbol" id="currency_symbol" >
										<?php foreach($currency_symbol as $key => $value) { ?>
										<option value="<?php echo $key; ?>"  <?php echo (isset($currencysymbol) && ($value==$payment_gateway_details['currency_symbol']))?"selected=selected":"";?>><?php echo $value;?></option>
										<?php } ?>
										</select>
                                       </div>
                                        <?php if(isset($error['currency_symbol'])){?>
                                        <span class="error">
                                        <?php if(isset($error) && array_key_exists('currency_symbol',$error)){ echo $error['currency_symbol'];}?>
                                        </span>
                                         <?php }?>
                                       
                                </td>      
                        </tr>								  
								  
								  
								  <tr>
									      <td valign="top"><label><?php echo __('payment_gateway_username_label');?></label><span class="star">*</span></td>
									      <td>
											  <input type="text" class="required"  name="payment_gateway_username" id="payment_gateway_username" maxlength="256" value="<?php echo isset($payment_gateway_details['payment_gateway_username']) && !array_key_exists('payment_gateway_username',$errors)? trim($payment_gateway_details['payment_gateway_username']):$validator['payment_gateway_username'];?>" maxlength="500"/> 
                                   <span class="error">
                                        <?php echo isset($errors["payment_gateway_username"])? $errors["payment_gateway_username"]:""; ?>
                                   </span> 									
											</td>
								  </tr>
								  <tr>								
									       <td valign="top"> <label><?php echo __('paypal api password_label');?></label><span class="star">*</span></td>
											<td>
											  <input type="password" class="required"  name="payment_gateway_password" maxlength="500" id="payment_gateway_password" value="<?php echo isset($payment_gateway_details['payment_gateway_password']) && !array_key_exists('payment_gateway_password',$errors)? trim($payment_gateway_details['payment_gateway_password']):$validator['payment_gateway_password'];?>" /> 
                                   <span class="error">
                                        <?php echo isset($errors["payment_gateway_password"])? $errors["payment_gateway_password"]:""; ?>
                                   </span> 									
											</td>
								 </tr>
									<tr>
									       <td valign="top"> <label><?php echo __('paypal api signature_label');?></label><span class="star">*</span></td>
									       <td>
											  <input type="text" class="required" name="payment_gateway_signature" maxlength="500" id="payment_gateway_signature" value="<?php echo isset($payment_gateway_details['payment_gateway_signature']) && !array_key_exists('payment_gateway_signature',$errors)? trim($payment_gateway_details['payment_gateway_signature']):$validator['payment_gateway_signature'];?>" /> 
                                   <span class="error">
                                        <?php echo isset($errors["payment_gateway_signature"])? $errors["payment_gateway_signature"]:""; ?>
                                   </span> 									
											</td>
									</tr>

                         <tr>
                        	<td colspan="2" class="star">*<?php echo __('required_label'); ?></td>
                        </tr>  	                        

                        <tr>
                                <td colspan="3" style="padding-left:110px;">
                                  <br />
                                  <input type="button" value="<?php echo __("button_back"); ?>" onclick="location.href='<?php echo URL_BASE;?>sitemanagement/payment_gatways'" />
                                  <input type="reset" value="<?php echo __("button_reset"); ?>" />
                                  <input type="submit" value="<?php echo ($action == 'edit_payment_gateways' )?''.__("button_update").'':''.__("button_update").'';?>" name="<?php echo ($action == 'edit_payment_gateways' )?'edit_gateway':'edit_gateway';?>" />
                                  <div class="clr">&nbsp;</div>
                                </td>
                        </tr> 
                        
                 </table>
                </form> 
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
</div>	
</div>

<script language="javascript" type="text/javascript">

$(document).ready(function(){

});
</script>
<script type="text/javascript" language="javascript">

//code for checking message field maxlength
//============================
function limitlength(obj, maxlength, label_id){
        //var maxlength=length
        if (obj.value.length>maxlength){
                obj.value=obj.value.substring(0, maxlength);
                // max reach
                //$("span.info_label").html("<?php echo __('ddfdsfdsf');?>");
                document.getElementById(label_id).innerHTML="<?php echo __('entered_max_text');?>"+maxlength;
        }else{
                var charleft = maxlength - obj.value.length;
                //alert(charleft);
                //inner html
                document.getElementById(label_id).innerHTML= charleft+"<?php echo __('entered_char_left_text');?>";
        }     
} 

$(document).ready(function(){

	//For Field Focus
	//===============
	//field_focus('payment_gatway');

}); 

</script>




