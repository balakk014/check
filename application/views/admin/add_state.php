<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
 <div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 
         <form name="addstate_form" id="addstate_form" class="form" action="" method="post" enctype="multipart/form-data" data-form="server-form">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">                                                  

	<tr>
	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('country_name',$postvalue)){ $field_type =  $postvalue['country_name']; } ?>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
              <select name="country_name" id="country_name">
              <?php foreach($state_details as $listings) { ?>
              <option value="<?php echo $listings['country_id']; ?>"  <?php if($field_type == $listings['country_id']) { echo 'selected=selected'; } elseif($field_type == '' && $listings['country_id'] == DEFAULT_COUNTRY) { echo 'selected=selected'; } ?>><?php echo ucfirst($listings['country_name']); ?></option>
              <?php } ?>
              </select>
        </div>
	</div>      
              <?php if(isset($errors) && array_key_exists('country_name',$errors)){ echo "<span class='error'>".ucfirst($errors['country_name'])."</span>";}?>

	</td>   	
	</tr> 
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php echo __('enterthestatename'); ?>" name="state_name" id="state_name" value="<?php if(isset($postvalue) && array_key_exists('state_name',$postvalue)){ echo $postvalue['state_name']; }?>"  maxlength="30" minlength="2" />
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
							<input type="text" name="submit_addstate" value="form" style="display:none;"/>
                            <div class="new_button"><input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
							<div class="new_button"><input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addstate" title="<?php echo __('btn_submit' );?>" /></div>
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
