<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">
         <form name="addmotor_form" class="form" id="addmotor_form" action="" method="post" enctype="multipart/form-data" data-form="server-form">
           <table border="0" cellpadding="5" cellspacing="0" width="100%">
           <tr>
           <td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
			   <td>
				   <?php //print_r($errors);?>
				   <div class="new_input_field">
					  <input type="text" title="<?php echo __('enterthecountryname'); ?>" name="country_name" id="country_name" value="<?php if(isset($postvalue) && array_key_exists('country_name',$postvalue)){ echo $postvalue['country_name']; }?>"  maxlength="30" minlength="2" />
					  <?php if(isset($errors) && array_key_exists('country_name',$errors)){ echo "<span class='error'>".ucfirst($errors['country_name'])."</span>";}?>
				   </div>
				</td>
		  </tr>
           <tr>
           <td valign="top" width="20%"><label><?php echo __('iso_code'); ?></label><span class="star">*</span></td>        
			   <td>
				   <div class="new_input_field">
		   	  <input type="text" title="<?php echo __('entertheisocountrycode'); ?>" name="iso_country_code" id="iso_country_code" value="<?php if(isset($postvalue) && array_key_exists('iso_country_code',$postvalue)){ echo $postvalue['iso_country_code']; }?>"  maxlength="5" minlength="2" />
		      <?php if(isset($errors) && array_key_exists('iso_country_code',$errors)){ echo "<span class='error'>".ucfirst($errors['iso_country_code'])."</span>";}?>
		   </div>
				</td>
		  </tr>
           <tr>
           <td valign="top" width="20%"><label><?php echo __('telephone_code'); ?></label><span class="star">*</span></td>        
			   <td>
			   <div class="new_input_field">
		   	  <input type="text" title="<?php echo __('enterthetelecode'); ?>" name="telephone_code" id="telephone_code" value="<?php if(isset($postvalue) && array_key_exists('telephone_code',$postvalue)){ echo $postvalue['telephone_code']; }?>"  maxlength="5" minlength="2" />
		      <?php if(isset($errors) && array_key_exists('telephone_code',$errors)){ echo "<span class='error'>".ucfirst($errors['telephone_code'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
           <tr>
           <td valign="top" width="20%"><label><?php echo __('currency_code'); ?></label><span class="star">*</span></td>        
			   <td>
			   <div class="new_input_field">
		   	  <input type="text" title="<?php echo __('enter_currency_code'); ?>" name="currency_code" id="currency_code" value="<?php if(isset($postvalue) && array_key_exists('currency_code',$postvalue)){ echo $postvalue['currency_code']; }?>"  maxlength="5" minlength="2" />
		      <?php if(isset($errors) && array_key_exists('currency_code',$errors)){ echo "<span class='error'>".ucfirst($errors['currency_code'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
            <tr>
           <td valign="top" width="20%"><label><?php echo __('currency_symbol'); ?></label><span class="star">*</span></td>        
			   <td>
			   <div class="new_input_field">
		   	  <input type="text" title="<?php echo __('enter_currency_symbol'); ?>" name="currency_symbol" id="currency_symbol" value="<?php if(isset($postvalue) && array_key_exists('currency_symbol',$postvalue)){ echo $postvalue['currency_symbol']; }?>"  maxlength="5"/>
		      <?php if(isset($errors) && array_key_exists('currency_symbol',$errors)){ echo "<span class='error'>".ucfirst($errors['currency_symbol'])."</span>";}?>
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
							<input type="text" name="submit_addcountry" value="form" style="display:none;"/>
                    
                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
							<div class="new_button">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addcountry" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            
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
