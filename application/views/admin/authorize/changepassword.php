<?php defined('SYSPATH') OR die("No direct access allowed."); ?>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">
         <form name="addmotor_form" class="form" id="addmotor_form" action="" method="post" enctype="multipart/form-data">
           <table border="0" cellpadding="5" cellspacing="0" width="100%">
           <tr>
           <td valign="top" width="20%"><label><?php echo __('oldpassword_label'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
		   	              <input type="password" title="<?php echo __('enter_oldpassword_label'); ?>" name="oldpassword" id="oldpassword" value="<?php if(isset($postvalue) && array_key_exists('oldpassword',$postvalue)){ echo $postvalue['oldpassword']; }?>"  minlength="2" maxlength="30" />
		      <?php if(isset($errors) && array_key_exists('oldpassword',$errors)){ echo "<span class='error'>".ucfirst($errors['oldpassword'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
			
            <tr>
           <td valign="top" width="20%"><label><?php echo __('newpassword_label'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
		   	              <input type="password" title="<?php echo __('enterpassword'); ?>" name="password" id="password" value="<?php if(isset($postvalue) && array_key_exists('password',$postvalue)){ echo $postvalue['password']; }?>"  minlength="2" maxlength="30" />
		      <?php if(isset($errors) && array_key_exists('password',$errors)){ echo "<span class='error'>".ucfirst($errors['password'])."</span>";}?>
		   </div>
           </td>   	
           </tr>

            <tr>
           <td valign="top" width="20%"><label><?php echo __('confirm_password_label'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
		   	              <input type="password" title="<?php echo __('entertheconfirmpassword'); ?>" name="repassword" id="repassword" value="<?php if(isset($postvalue) && array_key_exists('repassword',$postvalue)){ echo $postvalue['repassword']; }?>"  minlength="2" maxlength="30" />
		      <?php if(isset($errors) && array_key_exists('repassword',$errors)){ echo "<span class='error'>".ucfirst($errors['repassword'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
                               
	<tr>
	<td>&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
                            <br />
                    
                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                            <div class="new_button">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            <div class="new_button">  <input type="submit" value="<?php echo __('submit' );?>" name="submit_changepassword" title="<?php echo __('submit' );?>" /></div>
                            <div class="clr">&nbsp;</div>
                        </td>
                    </tr> 

                </table>
                

        </form>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>    
</div>
<script>
 $(document).ready(function(){
	
var field_val = $("#oldpassword").val();
$("#oldpassword").focus().val("").val(field_val);

		
});
</script>
