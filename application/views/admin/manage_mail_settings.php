<?php defined('SYSPATH') OR die("No direct access allowed."); ?>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">  
	<form method="POST" class="form" action="" >
		<table class="0" cellpadding="5" cellspacing="0" width="99%">
			<tr class="titlebold">
                            <td colspan="2"><h2 class="tab_sub_tit"><?php echo ucfirst(__('mail_settings_label')); ?></h2></td>
			   <td></td>	          
			</tr>
			<tr>
				<td valign="top" width="20%"><label><?php echo __('smtp_host_label');?></label><span class="star">*</span></td>   
				<td>
				<div class="new_input_field">
				<input type="text" name="smtp_host" id="smtp_host" title="<?php echo __('enter_hostname'); ?>" maxlength="50" value="<?php echo isset($mail_settings[0]['smtp_host']) &&!array_key_exists('smtp_host',$postvalue)? trim($mail_settings[0]['smtp_host']):$postvalue['smtp_host']; ?>">
				<span class="error"><?php echo isset($errors['smtp_host'])?$errors['smtp_host']:''; ?></span>
				</div>
				</td>
			</tr>                                                            
			<tr>
				<td valign="top" width="20%"><label><?php echo __('smtp_port_label');?></label><span class="star">*</span></td>   
				<td>
				<div class="new_input_field">
				<input type="text" name="smtp_port" id="smtp_port" title="<?php echo __('enter_portnumber'); ?>" maxlength="4" value="<?php echo isset($mail_settings[0]['smtp_port']) &&!array_key_exists('smtp_port',$postvalue)? trim($mail_settings[0]['smtp_port']):$postvalue['smtp_port']; ?>">
				<span class="error"><?php echo isset($errors['smtp_port'])?$errors['smtp_port']:''; ?></span>
				</div>
				</td>
			</tr>                              
            <tr>
				<td valign="top" width="20%"><label><?php echo __('smtp_username_label');?></label><span class="star">*</span></td>   
				<td>
				<div class="new_input_field">
				<input type="text" name="smtp_username" id="smtp_username" title="<?php echo __('enter_smtp_name'); ?>" maxlength="75" value="<?php echo isset($mail_settings[0]['smtp_username']) &&!array_key_exists('smtp_username',$postvalue)? trim($mail_settings[0]['smtp_username']):$postvalue['smtp_username']; ?>">
				<span class="error"><?php echo isset($errors['smtp_username'])?$errors['smtp_username']:''; ?></span>
				</div>
				</td>
            </tr>
            <tr>
				<td valign="top" width="20%"><label><?php echo __('smtp_password_label');?></label><span class="star">*</span></td>   
				<td>
				<div class="new_input_field">
				<input type="password" name="smtp_password" id="smtp_password" title="<?php echo __('enter_smtp_password'); ?>" maxlength="50" value="<?php echo isset($mail_settings[0]['smtp_password']) &&!array_key_exists('smtp_password',$postvalue)? trim($mail_settings[0]['smtp_password']):$postvalue['smtp_password']; ?>">
				<span class="error"><?php echo isset($errors['smtp_password'])?$errors['smtp_password']:''; ?></span>
				</div>
				</td>
            </tr>
            <tr>
				<td valign="top" width="20%"><label><?php echo __('transport_layer_security');?></label><span class="star">*</span></td>   	
				<td> <?php  $transport_layer = array( "ssl" => "Ssl","tls" => "Tls","ssh" => "Ssh","ipsec" => "Ipsec"); ?>
				   <div class="selector" id="uniform-user_type">
				   <select name="transport_layer_security">
						<option><?php echo __('select_label'); ?></option>
					<?php foreach($transport_layer as $transport_layer_key => $transport_layer_value) { ?>
					<option value="<?php echo $transport_layer_key; ?>" <?php if($mail_settings[0]["transport_layer_security"]==$transport_layer_key) echo "selected='selected'"; ?>><?php echo $transport_layer_value; ?></option>
					<?php } ?>
						</select>
					</div>	
				</td>
			</tr>

            <tr>
		<td valign="top" width="20%"><label><?php echo __('smtp');?></label><span class="star">*</span></td>   	
		<td> <?php  $transport_layer = array( "1" => "Yes","0" => "No"); ?>
			   <div class="selector" id="uniform-user_type">
			   <select name="smtp">
			        <option><?php echo __('select_label'); ?></option>
				<?php foreach($transport_layer as $transport_layer_key => $transport_layer_value) { ?>
				<option value="<?php echo $transport_layer_key; ?>" <?php if($mail_settings[0]["smtp"]==$transport_layer_key) echo "selected='selected'"; ?>><?php echo $transport_layer_value; ?></option>
				<?php } ?>
	        	    </select>
			    </div>	
		</td>
	    </tr>

		<tr>
			<td class="empt_cel">&nbsp;</td>
			<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
		</tr>                                 
		<tr>
			<td>&nbsp;</td>
			<td colspan="">
                            <div class="new_button">  <input type="submit" value="<?php echo __('submit' );?>" name="submit_editmailsetings" title="<?php echo __('submit' );?>" /></div>
			<div class="new_button">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
			<div class="clr">&nbsp;</div>
		</td>
		</tr> 
                </table>
                           
                </form>
                <br/><br/>
              
        </div>

</div>

</div>

<script language="javascript" type="text/javascript">

 $(document).ready(function(){

 var field_val = $("#smtp_host").val();
$("#smtp_host").focus().val("").val(field_val);

});
</script>
