<?php defined('SYSPATH') OR die("No direct access allowed.");
	echo html::script('public/common/ckeditor/ckeditor.js');
?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle site_settingss">
		   <?php 
				/* echo TIMEZONE.'<br>';
				//date_default_timezone_set('America/Los_Angeles');
				date_default_timezone_set('Asia/Kolkata');
				echo date_default_timezone_get() . ' => ' . date('e') . ' => ' . date('T'); */
			?>

            <form method="POST" enctype="multipart/form-data" class="form" action="" name="settings" id="settings" >
                <table class="0" cellpadding="5" cellspacing="0" width="100%">
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('site_name_label'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field" ><input type="text" name="app_name" id="app_name" title="<?php echo __('enter_site_name'); ?>" maxlength="75" value="<?php echo isset($site_settings) &&!array_key_exists('app_name',$postvalue)? trim($site_settings[0]['app_name']):$postvalue['app_name']; ?>"></div>
                    <?php if(isset($errors) && array_key_exists('app_name',$errors)){ echo "<span class='error'>".ucfirst($errors['app_name'])."</span>";}?>
                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('site_description_label'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field" ><textarea type="text" name="app_description" id="app_description" rows="7" cols="35" style="resize:none;" title="<?php echo __('enter_site_description'); ?>" value=""><?php echo isset($site_settings) && (!array_key_exists('app_description', $errors)) ? trim($site_settings[0]['app_description']) : trim($validator['app_description']); ?></textarea></div>
                            <span class="error"><?php echo isset($errors['app_description']) ? $errors['app_description'] : ''; ?></span>
                        </td>
                    </tr> 
                    
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('site_tagline_label'); ?></label><span class="star">*</span></td>   
                        <td>
						<div class="new_input_field" >
							<input type="text" name="site_tagline" id="site_tagline" title="<?php echo __('enter_site_tagline'); ?>" maxlength="50" value="<?php echo isset($site_settings) &&!array_key_exists('site_tagline',$postvalue)? trim($site_settings[0]['site_tagline']):$postvalue['site_tagline']; ?>">
						</div>
						<?php if(isset($errors) && array_key_exists('site_tagline',$errors)){ echo "<span class='error'>".ucfirst($errors['site_tagline'])."</span>";}?>
					</td>
                    </tr>                              

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('contact_email_label'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field" ><input type="text" name="contact_email" id="contact_email" title="<?php echo __('enter_contact_email'); ?>" maxlength="75" value="<?php echo isset($site_settings) && (!array_key_exists('email_id', $errors)) ? $site_settings[0]['email_id'] : $validator['email_id']; ?>"></div>
                            <span class="error"><?php echo isset($errors['contact_email']) ? $errors['contact_email'] : ''; ?></span></td>
                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('contact_phone_label'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field" ><input type="text" name="phone_number" id="phone_number"  title="<?php echo __('enter_phone_number'); ?>" maxlength="30" value="<?php echo isset($site_settings) && (!array_key_exists('phone_number', $errors)) ? $site_settings[0]['phone_number'] : $validator['phone_number']; ?>"></div>
                            <span class="error"><?php echo isset($errors['contact_email']) ? $errors['contact_email'] : ''; ?></span></td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('pagination_settings'); ?></label><span class="star">*</span></td>   
                        <td>
							<div class="formRight">
					<div class="selector new_setin" id="uniform-user_type">
						<select name="pagination_settings" id="pagination_settings" title="<?php echo __('select_pagination'); ?>" >
							<option value=""><?php echo __('select_label'); ?></option>
							<option value="10" <?php if($site_settings[0]['pagination_settings'] == '10') { echo 'selected=selected'; } ?> ><?php echo __('10');?></option>
							<option value="20" <?php if($site_settings[0]['pagination_settings'] == '20') { echo 'selected=selected'; } ?> ><?php echo __('20');?></option>
							<option value="30" <?php if($site_settings[0]['pagination_settings'] == '30') { echo 'selected=selected'; } ?> ><?php echo __('30');?></option>
							<option value="40" <?php if($site_settings[0]['pagination_settings'] == '40') { echo 'selected=selected'; } ?> ><?php echo __('40');?></option>
							<option value="50" <?php if($site_settings[0]['pagination_settings'] == '50') { echo 'selected=selected'; } ?> ><?php echo __('50');?></option>
						<?php echo isset($site_settings) && (!array_key_exists('pagination_settings', $errors)) ? $site_settings[0]['pagination_settings'] : $validator['pagination_settings']; ?></select>
						</div>
						<span class="error"><?php echo isset($errors['pagination_settings']) ? $errors['pagination_settings'] : ''; ?></span>
					</div>
                        </td>
                    </tr>
                     <tr>
                        <td valign="top" width="20%"><label><?php echo __('notification_settings_label'); ?></label><span class="star">*</span></td>   
                        <td>
							<div class="new_input_field" >
								<input type="text" name="notification_settings" class="required chk onlynumbers onlyseconds" id="notification_settings"  maxlength="3" value="<?php echo isset($site_settings) && (!array_key_exists('notification_settings', $errors)) ? $site_settings[0]['notification_settings'] : $validator['notification_settings']; ?>"></div>
								<span class="error"><?php echo isset($errors['notification_settings']) ? $errors['notification_settings'] : ''; ?></span>
								<span class="textclass fl clr"><?php echo __('notification_settings_upto'); ?></span>
                           </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('tell_to_friend_message'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field" ><textarea rows="5" cols="35" style="resize:none;" name="tell_to_friend_message" id="tell_to_friend_message"  title="<?php echo __('enter_tell_to_friend_message'); ?>" maxlength="150"><?php echo isset($site_settings) && (!array_key_exists('tell_to_friend_message', $errors)) ? $site_settings[0]['tell_to_friend_message'] : $validator['tell_to_friend_message']; ?></textarea></div>
                            <span class="error"><?php echo isset($errors['tell_to_friend_message']) ? $errors['tell_to_friend_message'] : ''; ?></span>
                            </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('meta_key_label'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
			<textarea name="meta_keyword" id="meta_keyword" rows="7" cols="35" title="<?php echo __('enter_meta_keywords'); ?>" style="resize:none;"><?php echo isset($site_settings) && (!array_key_exists('meta_keyword', $errors)) ? trim($site_settings[0]['meta_keyword']) : trim($validator['meta_keyword']); ?></textarea>
			</div>
                            <span class="error"><?php echo isset($errors['meta_keyword']) ? $errors['meta_keyword'] : ''; ?></span></td>
                    </tr>
                    
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('meta_desc_label'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
                        <textarea name="meta_description" id="meta_description" rows="7" cols="35" title="<?php echo __('enter_meta_description'); ?>" style="resize:none;"><?php echo isset($site_settings) && (!array_key_exists('meta_description', $errors)) ? trim($site_settings[0]['meta_description']) : trim($validator['meta_description']); ?></textarea>
			</div>
                            <span class="error"><?php echo isset($errors['meta_description']) ? $errors['meta_description'] : ''; ?></span></td>
                    </tr>

                     <tr>
                        <td valign="top" width="20%"><label><?php echo __('sms_enable'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
				<div class="formRight">
					<div class="selector new_setin" id="uniform-user_type">
						<select name="sms_enable" id="sms_enable" title="<?php echo __('sms_enable'); ?>" >
							<option value=""><?php echo __('select_label'); ?></option>
							<option value="1" <?php if($site_settings[0]['sms_enable'] == '1') { echo 'selected=selected'; } ?> ><?php echo __('yes');?></option>
							<option value="0" <?php if($site_settings[0]['sms_enable'] == '0') { echo 'selected=selected'; } ?> ><?php echo __('no');?></option>
						<?php echo isset($site_settings) && (!array_key_exists('sms_enable', $errors)) ? $site_settings[0]['sms_enable'] : $validator['sms_enable']; ?></select>
						</div>
					</div>
				</div>
						    <span class="error"><?php echo isset($errors['sms_enable']) ? $errors['sms_enable'] : ''; ?></span></td>
                    </tr>
                    
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('site_language'); ?></label><span class="star">*</span></td>   
                        <td>
                            <div class="new_input_field">
                                <select class="required" name="site_language[]" multiple style="height:100px;">
                                    <option value=""><?php echo __('select_label'); ?></option>
                                        <?php foreach(STATIC_LANGUAGE_ARRAY as $key => $value){
                                           if(isset($validator['site_language'])){
						$selected=(is_array($validator['site_language']) && in_array($key,$validator['site_language']))? "selected='selected'" : "";
                                           }elseif(isset($site_settings[0]['site_default_language'])){
						$languages = explode(",",$site_settings[0]['site_default_language']);
						$selected=(is_array($languages) && in_array($key,$languages))? "selected='selected'" : "";
                                            }else{
                                            	$selected = '';
                                            }
                                        ?>
                                        <option value="<?php echo $key; ?>" <?php echo $selected; ?> ><?php echo ucfirst($value)?></option>
                                        <?php } ?>
                                </select>
                            </div>	
                            <span class="error"><?php echo isset($errors['site_language']) ? $errors['site_language'] : ''; ?></span></td>
                    </tr>
                    
                    
				<tr>
					<td valign="top" width="20%"><label><?php echo __('selected_timezone'); ?></label><span class="star">*</span></td>   
					<td>
						<div class="new_input_field">
							<div class="formRight">
								<div class="selector new_setin">
									<?php $format = isset($validator['user_time_zone']) ? $validator['user_time_zone'] : $company_timezone; ?>
									<select name="user_time_zone" title="<?php echo __('time_zone'); ?>" >
										<?php
											$timezone = unserialize(SELECT_TIMEZONE);
											foreach($timezone as $key => $value) { ?>
												<?php if($company_timezone == $value) { ?>
												<option value="<?php echo $value; ?>"  <?php echo 'selected=selected'; ?>><?php echo ucfirst($value); ?></option>
												<?php  } ?>
										<?php } ?>
										<?php echo isset($site_settings) && (!array_key_exists('user_time_zone', $errors)) ? $company_timezone : $validator['user_time_zone']; ?>
									</select>
								</div>
							</div>
						</div>
						<span class="error"><?php echo isset($errors['date_time_format']) ? $errors['date_time_format'] : ''; ?></span>
					</td>
				</tr>
				
				<tr>
					<td valign="top" width="20%"><label><?php echo __('date_time_format'); ?></label><span class="star">*</span></td>   
					<td>
						<div class="new_input_field">
							<div class="formRight">
								<div class="selector new_setin">
									<?php $format = isset($validator['date_time_format'])?$validator['date_time_format']:$site_settings[0]['date_time_format']; ?>
									<select name="date_time_format" title="<?php echo __('date_time_format'); ?>" >
										<option value=""><?php echo __('select_label'); ?></option>
										<option value="Y-m-d H:i:s" <?php if($format == "Y-m-d H:i:s") { echo 'selected=selected'; } ?> ><?php echo "Y-m-d H:i:s"; ?></option>
										<option value="Y/m/d H:i:s" <?php if($format == "Y/m/d H:i:s") { echo 'selected=selected'; } ?> ><?php echo "Y/m/d H:i:s"; ?></option>
										<option value="d-m-Y H:i:s" <?php if($format == "d-m-Y H:i:s") { echo 'selected=selected'; } ?> ><?php echo "d-m-Y H:i:s"; ?></option>
										<option value="d/m/Y H:i:s" <?php if($format == "d/m/Y H:i:s") { echo 'selected=selected'; } ?> ><?php echo "d/m/Y H:i:s"; ?></option>
										 <option value="D,dM-Y h:i:s A" <?php if($format == "D,dM-Y h:i:s A") { echo 'selected=selected'; } ?> ><?php echo "D,dM-Y h:i:s A"; ?></option> 
										<?php echo isset($site_settings) && (!array_key_exists('date_time_format', $errors)) ? $site_settings[0]['date_time_format'] : $validator['date_time_format']; ?>
									</select>
								</div>
							</div>
						</div>
						<span class="error"><?php echo isset($errors['date_time_format']) ? $errors['date_time_format'] : ''; ?></span>
					</td>
				</tr>
                    
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('default_unit'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
                        <?php $checked=isset($site_settings[0]['default_unit'])?$site_settings[0]['default_unit']:"1"; 
						//echo $checked;?>
                        <input type="radio" name="default_unit" id="default_unit" title="<?php echo __('enter_payment_method'); ?>"  value="0" <?php if($checked=='0'){ echo 'checked=checked';}?> ><?php echo 'Kilometer'; ?>
                        
                        <input type="radio" name="default_unit" id="default_unit" title="<?php echo __('enter_payment_method'); ?>"  value="1" <?php if($checked=='1'){ echo 'checked=checked';}?>><?php  echo 'Mile'; ?>
                        </div>
						<?php if(isset($errors) && array_key_exists('default_unit',$errors)){ echo "<span class='error'>".ucfirst($errors['default_unit'])."</span>";}?></td>
                    </tr>
                    <tr>
						<td valign="top" width="20%"><label><?php echo __('default_miles'); ?></label><span class="star">*</span></td>   
						<td>
							<div class="new_input_field">
								<input type="text" name="default_miles" title="<?php echo __('enter_default_miles'); ?>" value="<?php echo isset($site_settings) && (!array_key_exists('default_miles', $errors)) ? $site_settings[0]['default_miles'] : $validator['default_miles']; ?>" maxlength="5">
							</div>
							<span class="error"><?php echo isset($errors['default_miles']) ? ucfirst($errors['default_miles']) : ''; ?></span>
						</td>
					</tr>
					<tr>
                        <td valign="top" width="20%"><label><?php echo __('skip_credit_card'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
                        <?php $checked=isset($site_settings[0]['skip_credit_card'])?$site_settings[0]['skip_credit_card']:"0"; 
						//echo $checked;?>
                        <input type="radio" name="skip_credit_card" id="skip_card_enable" onclick="return skip_credit_cards('1');" title="<?php echo __('select_skip_credit_card'); ?>"  value="0" <?php if($checked=='0'){ echo 'checked=checked';}?> ><?php echo 'Enable'; ?>
                        
                        <input type="radio" name="skip_credit_card" id="skip_card_disable" onclick="return skip_credit_cards('2');" title="<?php echo __('select_skip_credit_card'); ?>"  value="1" <?php if($checked=='1'){ echo 'checked=checked';}?>><?php  echo 'Disable'; ?>
                        </div>
						<?php if(isset($errors) && array_key_exists('skip_credit_card',$errors)){ echo "<span class='error'>".ucfirst($errors['skip_credit_card'])."</span>";}?></td>
                    </tr>
                    
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('cancellation_fare'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
						<div class="formRight">
							<div class="selector new_setin" id="uniform-user_type">
								<?php $cancel_chk=isset($validator['cancellation_fare'])?$validator['cancellation_fare']:$site_settings[0]['cancellation_fare_setting']; 
								//echo $cancel_chk;?>
								<select name="cancellation_fare" id="cancellation_fare" onchange="return check_cancelation_skip_cards(this.value);"   class="required" title="<?php echo __('cancellation_fare'); ?>">
									<option value=""><?php echo __('select_label'); ?></option>
									<option value="1" <?php if($cancel_chk == 1) { echo 'selected=selected'; } ?> ><?php echo __('yes');?></option>
									<option value="0" <?php if($cancel_chk == 0) { echo 'selected=selected'; } ?> ><?php echo __('no');?></option>
								</select>
								</div>
							</div>
						</div> 
						<label for="cancellation_fare" generated="true" class="errorvalid" style="display:none"></label>
						<span class="error"><?php echo isset($errors['cancellation_fare']) ? $errors['cancellation_fare'] : ''; ?></span></td>
                    </tr>

			<?php /*
                     <tr>
                        <td valign="top" width="20%"><label><?php echo __('passenger_setting'); ?></label><span class="star">*</span></td>   
                        <td>
			<?php  $labelname_type = array( "1" => "Server will select the nearest taxi and dispatch","2" => "Passenger able to select the taxi"); ?>
<div class="new_input_field">
				<div class="formRight">
					<div class="selector" id="uniform-user_type">
<?php $passenger_chk = isset($validator['passenger_setting'])?$validator['passenger_setting']:$site_settings[0]['passenger_setting']; ?>

						<select name="passenger_setting" id="passenger_setting" title="<?php echo __('passenger_setting'); ?>" >
					<?php foreach($labelname_type as $labelname_key => $labelname_value) { ?>
					<option value="<?php echo $labelname_key; ?>" <?php if($passenger_chk == $labelname_key) echo "selected='selected'"; ?>><?php echo $labelname_value; ?></option>
					<?php } ?>
						<?php echo isset($site_settings) && (!array_key_exists('passenger_setting', $errors)) ? $site_settings[0]['passenger_setting'] : $validator['passenger_setting']; ?></select>
						</div>
					</div>
				</div>
						    <span class="error"><?php echo isset($errors['passenger_setting']) ? $errors['passenger_setting'] : ''; ?></span></td>
                    </tr>
			 */ ?>
                     <?php /*
                     <tr>
                        <td valign="top" width="20%"><label><?php echo __('site_country'); ?> </label><span class="star">*</span></td>   
                        <td>
				<div class="new_input_field">
					<div class="formRight">
						<div class="selector" id="uniform-user_type">
							<select name="site_country" id="site_country" title="<?php echo __('enter_site_country'); ?>" >
								<option value="">-- select country --</option>
								<?php foreach($site_country as $sitecountry){ ?>
								<option value='<?php echo $sitecountry['country_id'];?>' <?php if($site_settings[0]['site_country'] == $sitecountry['country_id'] ) {  echo 'selected=selected'; } ?> ><?php echo $sitecountry['country_name'];?></option>
								<?php } ?>
							<?php echo isset($site_settings) && (!array_key_exists('site_country', $errors)) ? $site_settings[0]['site_country'] : $validator['site_country']; ?></select>
							</div>
						</div>
					</div>
							<span class="error"><?php echo isset($errors['site_country']) ? $errors['site_country'] : ''; ?></span></td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('site_city'); ?> </label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
                        <select name="site_city" id="site_city" title="<?php echo __('enter_site_city'); ?>" >
				<option>-- select city --</option>
				<?php foreach($site_city as $sitecity){ ?>
				<option value='<?php echo $sitecity['city_id'];?>' <?php if($site_settings[0]['site_city'] == $sitecity['city_id']) { echo 'selected=selected'; } ?> ><?php echo $sitecity['city_name'];?></option>
				<?php } ?>
                        <?php echo isset($site_settings) && (!array_key_exists('site_city', $errors)) ? $site_settings[0]['site_city'] : $validator['site_city']; ?></select>
			</div>
                            <span class="error"><?php echo isset($errors['site_city']) ? $errors['site_city'] : ''; ?></span></td>
                    </tr> */ ?>
                    <tr>
	<td valign="top" width="20%"><label><?php echo __('fare_settings'); ?></label><span class="star">*</span></td>        
	<td>
		<div class="formRight">
		<div id="uniform-user_type">
		<div id="fare_settings">
			<input type="radio" name="price_settings" value="1" <?php if($site_settings[0]['price_settings'] == 1) { echo 'checked = checked'; } ?> /> <?php echo __('admin_fare'); ?>
			<input type="radio" name="price_settings" value="2" <?php if($site_settings[0]['price_settings'] == 2) { echo 'checked = checked'; } ?> /> <?php echo __('company_fare'); ?>
		</div>	
		</div>
		</div>
		<label for="fare_settings" generated="true" style="display:none" class="errorvalid"><?php echo __('select_fare_settings'); ?></label>	
		 <?php if(isset($errors) && array_key_exists('fare_settings',$errors)){ echo "<span class='error'>".ucfirst($errors['fare_settings'])."</span>"; }?>
	</td>   	
	</tr>
			<tr>
                        <td valign="top" width="20%"><label><?php echo __('fare_calculation'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
				<div class="formRight">
					<div class="selector new_setin" id="uniform-user_type">
						<select name="fare_calculation" id="fare_calculation" title="<?php echo __('fare_calculation'); ?>" >
							<option value=""><?php echo __('select_label'); ?></option>
							<option value="1" <?php if($site_settings[0]['fare_calculation_type'] == '1') { echo 'selected=selected'; } ?> ><?php echo __('distance');?></option>
							<option value="2" <?php if($site_settings[0]['fare_calculation_type'] == '2') { echo 'selected=selected'; } ?> ><?php echo __('time');?></option>
							<option value="3" <?php if($site_settings[0]['fare_calculation_type'] == '3') { echo 'selected=selected'; } ?> ><?php echo __('distance')." / ".__('time');?></option>
						<?php echo isset($site_settings) && (!array_key_exists('fare_calculation', $errors)) ? $site_settings[0]['fare_calculation_type'] : $validator['fare_calculation']; ?></select>
						</div>
					</div>
				</div>
						    <span class="error"><?php echo isset($errors['fare_calculation']) ? $errors['fare_calculation'] : ''; ?></span></td>
                    </tr>

			<tr>
				<td valign="top" width="20%"><label><?php echo __('referral_settings'); ?></label><span class="star">*</span></td>        
				<td>
					<div class="formRight">
					<div id="uniform-user_type">
					<div id="referral_settings">
						<input type="radio" name="referral_settings" value="1" <?php if($site_settings[0]['referral_settings'] == 1) { echo 'checked = checked'; } ?> /> <?php echo __('enable'); ?>
						<input type="radio" name="referral_settings" value="2" <?php if($site_settings[0]['referral_settings'] == 2) { echo 'checked = checked'; } ?> /> <?php echo __('disable'); ?>
					</div>	
					</div>
					</div>
					<label for="referral_settings" generated="true" style="display:none" class="errorvalid"><?php echo __('select_referral_settings'); ?></label>	
					 <?php if(isset($errors) && array_key_exists('referral_settings',$errors)){ echo "<span class='error'>".ucfirst($errors['referral_settings'])."</span>"; }?>
				</td>   	
			</tr>
			
			<tr>
                <td valign="top" width="20%"><label><?php echo __('referral_amount'); ?></label><span class="star">*</span></td>   
                <td>
					<div class="new_input_field">
						<input type="text" name="referral_amount" id="referral_amount"  title="<?php echo __('enter_referral_amount'); ?>" maxlength="5" value="<?php echo isset($site_settings) && (!array_key_exists('referral_amount', $errors)) ? $site_settings[0]['referral_amount'] : $validator['referral_amount']; ?>" >
					</div>
					<span class="error"><?php echo isset($errors['referral_amount']) ? ucfirst($errors['referral_amount']) : ''; ?></span>
				</td>
				<input type="hidden" name="prev_referral_amount" value="<?php echo (isset($site_settings[0]['referral_amount'])) ? $site_settings[0]['referral_amount'] : 0; ?>">
            </tr>
            
            <tr>
                <td valign="top" width="20%"><label><?php echo __('wallet_amount1'); ?></label><span class="star">*</span></td>   
                <td>
					<div class="new_input_field">
						<input type="text" name="wallet_amount1" id="wallet_amount1"  title="<?php echo __('enter_wallet_amount'); ?>" maxlength="5" value="<?php echo isset($site_settings) && (!array_key_exists('wallet_amount1', $errors)) ? $site_settings[0]['wallet_amount1'] : $validator['wallet_amount1']; ?>">
					</div>
					<span class="error"><?php echo isset($errors['wallet_amount1']) ? ucfirst($errors['wallet_amount1']) : ''; ?></span>
				</td>
            </tr>
            
            <tr>
                <td valign="top" width="20%"><label><?php echo __('wallet_amount2'); ?></label><span class="star">*</span></td>   
                <td>
					<div class="new_input_field">
						<input type="text" name="wallet_amount2" id="wallet_amount2"  title="<?php echo __('enter_wallet_amount'); ?>" maxlength="5" value="<?php echo isset($site_settings) && (!array_key_exists('wallet_amount2', $errors)) ? $site_settings[0]['wallet_amount2'] : $validator['wallet_amount2']; ?>">
					</div>
					<span class="error"><?php echo isset($errors['wallet_amount2']) ? ucfirst($errors['wallet_amount2']) : ''; ?></span>
				</td>
            </tr>
            
            <tr>
                <td valign="top" width="20%"><label><?php echo __('wallet_amount3'); ?></label><span class="star">*</span></td>   
                <td>
					<div class="new_input_field">
						<input type="text" name="wallet_amount3" id="wallet_amount3"  title="<?php echo __('enter_wallet_amount'); ?>" maxlength="5" value="<?php echo isset($site_settings) && (!array_key_exists('wallet_amount3', $errors)) ? $site_settings[0]['wallet_amount3'] : $validator['wallet_amount3']; ?>">
					</div>
					<span class="error"><?php echo isset($errors['wallet_amount3']) ? ucfirst($errors['wallet_amount3']) : ''; ?></span>
				</td>
            </tr>
            
           <?php /* <tr>
                <td valign="top" width="20%"><label><?php echo __('wallet_amount_range'); ?></label><span class="star">*</span></td>   
                <td>
					<div class="new_input_field" style="width:400px;">
						<input type="text" name="wallet_amount_range" id="wallet_amount_range"  title="<?php echo __('enter_wallet_amount_range'); ?>" maxlength="20" value="<?php echo isset($site_settings) && (!array_key_exists('wallet_amount_range', $errors)) ? $site_settings[0]['wallet_amount_range'] : $validator['wallet_amount_range']; ?>">
					</div>
					<span class="error"><?php echo isset($errors['wallet_amount_range']) ? ucfirst($errors['wallet_amount_range']) : ''; ?></span>
				</td>
            </tr> */ ?>
            <input type="hidden" name="wallet_amount_range" id="wallet_amount_range"  title="<?php echo __('enter_wallet_amount_range'); ?>" maxlength="20" value="<?php echo isset($site_settings) && (!array_key_exists('wallet_amount_range', $errors)) ? $site_settings[0]['wallet_amount_range'] : $validator['wallet_amount_range']; ?>">
            
            <tr>
				<td valign="top" width="20%"><label><?php echo __('driver_referral_setting'); ?></label><span class="star">*</span></td>        
				<td>
					<div class="formRight">
					<div id="uniform-user_type">
					<div id="referral_settings">
						<input type="radio" name="driver_referral_setting" value="1" <?php if($site_settings[0]['driver_referral_setting'] == 1) { echo 'checked = checked'; } ?> /> <?php echo __('enable'); ?>
						<input type="radio" name="driver_referral_setting" value="2" <?php if($site_settings[0]['driver_referral_setting'] == 2) { echo 'checked = checked'; } ?> /> <?php echo __('disable'); ?>
					</div>	
					</div>
					</div>
					<label for="driver_referral_setting" generated="true" style="display:none" class="errorvalid"><?php echo __('select_referral_settings'); ?></label>	
					 <?php if(isset($errors) && array_key_exists('driver_referral_setting',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_referral_setting'])."</span>"; }?>
				</td>   	
			</tr>
			<tr>
                <td valign="top" width="20%"><label><?php echo __('driver_referral_amount'); ?></label><span class="star">*</span></td>   
                <td>
					<div class="new_input_field">
						<input type="text" name="driver_referral_amount" id="driver_referral_amount"  title="<?php echo __('enter_referral_amount'); ?>" maxlength="5" value="<?php echo isset($site_settings) && (!array_key_exists('driver_referral_amount', $errors)) ? $site_settings[0]['driver_referral_amount'] : $validator['driver_referral_amount']; ?>">
					</div>
					<span class="error"><?php echo isset($errors['driver_referral_amount']) ? ucfirst($errors['driver_referral_amount']) : ''; ?></span>
				</td>
				<input type="hidden" name="prev_driver_referral_amount" value="<?php echo (isset($site_settings[0]['driver_referral_amount'])) ? $site_settings[0]['driver_referral_amount'] : 0; ?>">
            </tr>
                  <?php /*  <tr>
	<td valign="top" width="20%"><label><?php echo __('currency_code'); ?></label><span class="star">*</span></td>        
	<td>
		<div class="formRight">
		<div class="selector" id="uniform-user_type">
		<span><?php echo __('currency_code'); ?></span>
		<div id="currency_code">
			<select name="currency_code" id="currency_code"  title="<?php echo __('select_currency_code'); ?>" >
				<option value="">-- Select Currency Code--</option>
				<?php foreach($currency_code as $key=>$currencycode){ ?>
				<option value='<?php echo $currencycode;?>' <?php if($site_settings[0]['currency_format'] == $currencycode) { echo 'selected=selected'; } ?> ><?php echo $currencycode;?></option>
				<?php } ?>  
			</select>
		</div>	
		</div>
		</div>
		<label for="currency_code" generated="true" style="display:none" class="errorvalid"><?php echo __('select_currency_code'); ?></label>	
		 <?php if(isset($errors) && array_key_exists('currency_code',$errors)){ echo "<span class='error'>".ucfirst($errors['currency_code'])."</span>"; }?>
	</td>   	
	</tr>
                     <tr>
                        <td valign="top" width="20%"><label><?php echo __('site_currency'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
				<div class="formRight">
					<div class="selector" id="uniform-user_type">
						<select name="site_currency" id="site_currency" title="<?php echo __('enter_site_currency'); ?>" >
							<option value="">-- Select Currency --</option>
							<?php foreach($currency_symbol as $key=>$currencysymbol){ ?>
							<option value="<?php echo $currencysymbol;?>" <?php if($site_settings[0]['site_currency'] == $currencysymbol) { echo 'selected=selected'; } ?> ><?php echo $currencysymbol;?></option>
							<?php } ?>
						<?php echo isset($site_settings) && (!array_key_exists('site_currency', $errors)) ? $site_settings[0]['site_currency'] : $validator['site_currency']; ?></select>
						</div>
					</div>
				</div>
						    <span class="error"><?php echo isset($errors['site_currency']) ? $errors['site_currency'] : ''; ?></span></td>
                    </tr> */ ?>
                    
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('site_map'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
				<div class="formRight">
					<div class="selector new_setin" id="uniform-user_type">
						<select name="show_map" id="show_map" title="<?php echo __('enter_site_map'); ?>" >
							<option value=""><?php echo __('select_label'); ?></option>
							<option value="1" <?php if($site_settings[0]['show_map'] == '1') { echo 'selected=selected'; } ?>><?php echo __('front_end'); ?></option>
							<option value="2" <?php if($site_settings[0]['show_map'] == '2') { echo 'selected=selected'; } ?>><?php echo __('admin_end'); ?></option>
							<option value="3" <?php if($site_settings[0]['show_map'] == '3') { echo 'selected=selected'; } ?>><?php echo __('both_end'); ?></option>
						<?php  echo isset($site_settings) && (!array_key_exists('show_map', $errors)) ? $site_settings[0]['show_map'] : $validator['show_map']; ?>
						</select>
						</div>
					</div>
				</div>
						    <span class="error"><?php echo isset($errors['show_map']) ? $errors['show_map'] : ''; ?></span></td>
                    </tr>
					<tr>
						<td valign="top" width="20%"><label><?php echo __('commision_setting'); ?></label><span class="star">*</span></td>   
						<td>
							<div class="new_input_field">
								<input type="checkbox" name="admin_commision_setting" <?php if($site_settings[0]['admin_commision_setting'] == 1) { echo 'checked'; } ?> value="1"><?php echo __('admin'); ?>

								<input type="checkbox" name="company_commision_setting" <?php if($site_settings[0]['company_commision_setting'] == 1) { echo 'checked'; } ?> value="1"><?php echo __('company'); ?>

								<input type="checkbox" name="driver_commision_setting" <?php if($site_settings[0]['driver_commision_setting'] == 1) { echo 'checked'; } ?> value="1"><?php echo __('driver'); ?>
							</div>
						</td>
					</tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('admin_commission'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
<input type="text" name="admin_commission" id="admin_commission"  title="<?php echo __('enteradmincommission'); ?>" max="100" maxlength="5" value="<?php echo isset($site_settings) && (!array_key_exists('admin_commission', $errors)) ? $site_settings[0]['admin_commission'] : $validator['admin_commission']; ?>">
			</div>
                            <span class="error"><?php echo isset($errors['admin_commission']) ? ucfirst($errors['admin_commission']) : ''; ?></span>
			    <small class="sub_note"><?php echo __('updateadmincommision'); ?></small>
			</td>
                    </tr>
<!--<tr>
                       <td valign="top" width="20%"><label><?php echo __('admin_taxi_charge').' ('.CURRENCY.')'; ?></label><span class="star">*</span></td>   
                       <td><div class="new_input_field" style="width:400px;">
<input type="text" name="taxi_charge" id="taxi_charge"  title="<?php echo __('enter_admin_taxi_charge'); ?>" maxlength="6" value="<?php echo isset($site_settings) && (!array_key_exists('taxi_charge', $errors)) ? $site_settings[0]['taxi_charge'] : $validator['taxi_charge']; ?>">
                       </div>
                           <span class="error"><?php echo isset($errors['taxi_charge']) ? ucfirst($errors['taxi_charge']) : ''; ?></span>
                          <?php /* <span class="textclass fl clr"><?php echo __('updateadmincommision'); ?></span> */ ?>
                       </td>
                   </tr>  -->                  
<tr>
                       <td valign="top" width="20%"><label><?php echo __('continuous_request_time'); ?></label><span class="star">*</span></td>   
                       <td><div class="new_input_field">
<input type="text" name="continuous_request_time" id="continuous_request_time" class="required greaternotification" maxlength="6" value="<?php echo isset($site_settings) && (!array_key_exists('continuous_request_time', $errors)) ? $site_settings[0]['continuous_request_time'] : $validator['continuous_request_time']; ?>">
                       </div>
                           <span class="error"><?php echo isset($errors['continuous_request_time']) ? ucfirst($errors['continuous_request_time']) : ''; ?></span>
                          <?php /* <span class="textclass fl clr"><?php echo __('updateadmincommision'); ?></span> */ ?>
                       </td>
                   </tr>                    

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('tax'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field">
<input type="text" name="tax" id="tax"  title="<?php echo __('enter_tax'); ?>" maxlength="5" value="<?php echo isset($site_settings) && (!array_key_exists('tax', $errors)) ? $site_settings[0]['tax'] : $validator['tax']; ?>">
			</div>
                            <span class="error"><?php echo isset($errors['tax']) ? ucfirst($errors['tax']) : ''; ?></span>
			    <small class="sub_note"><?php echo __('note_tax'); ?></small>
			</td>
                    </tr>
                    
			<input type="hidden" name="ios_google_map_key" value="<?php echo trim($site_settings[0]['ios_google_map_key']) ?>">
			<input type="hidden" name="ios_google_geo_key" value="<?php echo trim($site_settings[0]['ios_google_geo_key']) ?>">
			<input type="hidden" name="web_google_map_key" value="<?php echo trim($site_settings[0]['web_google_map_key']) ?>">
			<input type="hidden" name="google_timezone_api_key" value="<?php echo trim($site_settings[0]['google_timezone_api_key']) ?>">
			<input type="hidden" name="web_google_geo_key" value="<?php echo trim($site_settings[0]['web_google_geo_key']) ?>">
			<input type="hidden" name="android_google_api_key" value="<?php echo trim($site_settings[0]['android_google_key']) ?>">
				
				<tr>
					<td valign="top" width="20%"><label><?php echo __('passenger_app_android_store_link'); ?></label><span class="star">*</span></td>
					<td><div class="new_input_field"><input type="text" name="passenger_app_android_store_link" id="passenger_app_android_store_link" value="<?php echo isset($site_settings) && (!array_key_exists('passenger_app_android_store_link', $errors)) ? $site_settings[0]['passenger_app_android_store_link'] : $validator['passenger_app_android_store_link']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('passenger_app_android_store_link',$errors)){ echo "<span class='error'>".ucfirst($errors['passenger_app_android_store_link'])."</span>"; } ?></td>
				</tr>
				<tr>
					<td valign="top" width="20%"><label><?php echo __('passenger_app_ios_store_link'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="passenger_app_ios_store_link" id="passenger_app_ios_store_link" value="<?php echo isset($site_settings) && (!array_key_exists('passenger_app_ios_store_link', $errors)) ? $site_settings[0]['passenger_app_ios_store_link'] : $validator['passenger_app_ios_store_link']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('passenger_app_ios_store_link',$errors)){ echo "<span class='error'>".ucfirst($errors['passenger_app_ios_store_link'])."</span>";}?></td>
				</tr>

				<tr>
					<td valign="top" width="20%"><label><?php echo __('app_android_store_link'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="app_android_store_link" id="app_android_store_link" title="<?php echo __('enter_app_android_store_link'); ?>" value="<?php echo isset($site_settings) && (!array_key_exists('app_android_store_link', $errors)) ? $site_settings[0]['app_android_store_link'] : $validator['app_android_store_link']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('app_android_store_link',$errors)){ echo "<span class='error'>".ucfirst($errors['app_android_store_link'])."</span>"; } ?></td>
				</tr>
				
				<tr>
					<td valign="top" width="20%"><label><?php echo __('app_ios_store_link'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="app_ios_store_link" id="app_ios_store_link" title="<?php echo __('enter_app_ios_store_link'); ?>" value="<?php echo isset($site_settings) && (!array_key_exists('app_ios_store_link', $errors)) ? $site_settings[0]['app_ios_store_link'] : $validator['app_ios_store_link']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('app_ios_store_link',$errors)){ echo "<span class='error'>".ucfirst($errors['app_ios_store_link'])."</span>";}?></td>
				</tr>
				<tr>
				<td class="empt_cel">&nbsp;</td>
							<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
						</tr>						
						<tr>
							<td valign="top">&nbsp;</td>
							<td style="padding-left:0px;">
								 <div class="new_button">  <input type="submit" name="editsettings_submit" <?php if($email==SUPERADMIN_EMAIL) { ?> id="disable" <?php } ?> title ="<?php echo __('button_update'); ?>" value="<?php echo __('button_update'); ?>"></div>
								<div class="new_button"> <input type="reset" name="editsettings_reset" title="<?php echo __('button_reset'); ?>" value="<?php echo __('button_reset'); ?>"></div>
							</td></tr>
				</table>
			</form>
            <br/><br/>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt" ></div></div>
    </div>

</div>
<script src="<?php echo SCRIPTPATH; ?>jscolor.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {

	jQuery("#settings").validate();

	jQuery.validator.addMethod('chk', function(value) {
		return (parseInt(document.getElementById('notification_settings').value)  > 0); }, '<?php echo __("notificationtime_should_greater_thanzero"); ?>');

	jQuery.validator.addMethod('onlyseconds', function(value) {
		return (parseInt(document.getElementById('notification_settings').value)  < 60); }, '<?php echo __("notification_timeshouldbe_lessthan_60_seconds"); ?>.');
	
	jQuery.validator.addMethod('greaternotification', function(value) {		
		
		if(parseInt(document.getElementById('continuous_request_time').value)  > (4*parseInt(document.getElementById('notification_settings').value))){
			return (parseInt(document.getElementById('continuous_request_time').value) < (6*parseInt(document.getElementById('notification_settings').value))+1);
		}else{
			return false;
		}
		
	}, '<?php echo __("continuousrequest_timeshouldbegreaterthan_4_Notification_time_info"); ?>.');
	
	$.validator.addMethod( "imageonly", function(value,element){
	var pathLength = value.length; var lastDot = value.lastIndexOf( "."); var fileType = value.substring(lastDot,pathLength).toLowerCase(); return this.optional(element) || fileType.match(/(?:.jpg|.jpeg|.png)$/) }, "Please upload image file only");

	//For Field Focus
	//===============
	var field_val = $("#app_name").val();
	$("#app_name").focus().val("").val(field_val);

	var cityid= $("#site_city").val();

	if(cityid == '')
	{
		//change_city();
	}


     $("#site_country").change(function() {

      		var countryid= $("#site_country").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getcitylist",
			type:"get",
			data:"country_id="+countryid,
			success:function(data){

			$('#city_list').html();
			$('#city_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    });
    
	$( "input.theme").change(function(){
		var themeid = this.value;
		$("#theme"+themeid).show();
		if(themeid == 1)
			$("#theme2").hide();
		else
			$("#theme1").hide();
    });          
});

function change_city()
{

			var countryid= $("#site_country").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getcitylist",
			type:"get",
			data:"country_id="+countryid,
			success:function(data){

			$('#city_list').html();
			$('#city_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function skip_credit_cards(val)
{
	var cancel_fare_setting = $("#cancellation_fare").val();
	if(val == 1 && cancel_fare_setting == 1) {
		alert("<?php echo __('not_enable_skip_card_option'); ?>");
		$("#skip_card_enable").prop('checked', false);
		$("#skip_card_disable").prop('checked', true);
	}
}


function check_cancelation_skip_cards(val)
{
	var skip_credit_card_val = $('input[name="skip_credit_card"]:checked').val();
	if(val == 1 && skip_credit_card_val == 0) {
		alert("<?php echo __('not_enable_skip_card_option'); ?>");
		$("#cancellation_fare").val(0);
	}
}
</script>
