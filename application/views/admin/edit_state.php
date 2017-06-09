<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">
         <form name="editstate_form" class="form" id="editstate_form" action="" method="post" enctype="multipart/form-data">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
	<?php 
		if(isset($state_details[0]['state_countryid']) && !array_key_exists('country_name',$postvalue)){
			$country_name = $state_details[0]['state_countryid'];
		}else{
			if(isset($postvalue['country_name'])){
				$country_name = $postvalue['country_name'];
			}else{
				$country_name = "";
			}
		}
		?> 
	<?php $field_type = $country_name; ?>
	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
		<select name="country_name" id="country_name">
			<?php foreach($country_details as $listings) { ?>
				<option value="<?php echo $listings['country_id']; ?>" <?php if($field_type == $listings['country_id'] ) {  echo 'selected=selected'; }elseif($field_type == '' && $listings['country_id'] == DEFAULT_COUNTRY) { echo 'selected=selected'; } ?> ><?php echo ucfirst($listings['country_name']); ?></option>
			<?php } ?>
		</select>
		</div>
	</div>
	
              <?php //print_r($errors);?>
		<input type="hidden" name="state_countryid" value="<?php echo $state_details[0]['state_countryid']; ?>" >
		<?php if(isset($errors) && array_key_exists('country_name',$errors)){ echo "<span class='error'>".ucfirst($errors['country_name'])."</span>";}?> 
	</td>   	
	</tr>
	<?php 
		if(isset($state_details[0]['state_name']) && !array_key_exists('state_name',$postvalue)){
			$state_name = $state_details[0]['state_name'];
		}else{
			if(isset($postvalue['state_name'])){
				$state_name = $postvalue['state_name'];
			}else{
				$state_name = "";
			}
		}
		?> 
	<tr>
	<td valign="top" width="20%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="30" minlength="2"  title="<?php echo __('enterthestatename'); ?>" id="state_name" name="state_name" value="<?php echo $state_name; ?>" />
              <?php if(isset($errors) && array_key_exists('state_name',$errors)){ echo "<span class='error'>".ucfirst($errors['state_name'])."</span>";}?>
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
                            <div class="new_button"><input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
							<div class="new_button"><input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_editstate" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button"><input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            <div class="clr">&nbsp;</div>
                        </td>
                    </tr> 

                </table>

        </form>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
 $("#country_name").focus(); 
});
</script>
