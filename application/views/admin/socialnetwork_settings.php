<?php
defined('SYSPATH') OR die("No direct access allowed.");
?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 
            <form method="POST" enctype="multipart/form-data" class="form" action="" >
                <table class="0" cellpadding="5" cellspacing="0" width="100%">
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('facebook_key'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="facebook_key" id="facebook_key" title="<?php echo __('enter_facebook_key'); ?>" maxlength="50" value="<?php echo isset($socialsettings) &&!array_key_exists('facebook_key',$postvalue)? trim($socialsettings[0]['facebook_key']):$postvalue['facebook_key']; ?>"></div>
                    <?php if(isset($errors) && array_key_exists('facebook_key',$errors)){ echo "<span class='error'>".ucfirst($errors['facebook_key'])."</span>";}?></td>
                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('facebook_secretkey'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="facebook_secretkey" id="facebook_secretkey" title="<?php echo __('enter_facebook_secretkey'); ?>" maxlength="100" value="<?php echo isset($socialsettings) && (!array_key_exists('facebook_secretkey', $errors)) ? $socialsettings[0]['facebook_secretkey'] : $validator['facebook_secretkey']; ?>" > </div>
                            <?php if(isset($errors) && array_key_exists('facebook_secretkey',$errors)){ echo "<span class='error'>".ucfirst($errors['facebook_secretkey'])."</span>";}?></td>
                        </td>

                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('facebook_share'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="facebook_share" id="facebook_share" title="<?php echo __('enter_facebook_share'); ?>" maxlength="100" value="<?php echo isset($socialsettings) && (!array_key_exists('facebook_share', $errors)) ? $socialsettings[0]['facebook_share'] : $validator['facebook_share']; ?>"></div>
                            <?php if(isset($errors) && array_key_exists('facebook_share',$errors)){ echo "<span class='error'>".ucfirst($errors['facebook_share'])."</span>";}?></td>
                    </tr>

                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('twitter_share'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="twitter_share" id="twitter_share"  title="<?php echo __('enter_twitter_share'); ?>" maxlength="100" value="<?php echo isset($socialsettings) && (!array_key_exists('twitter_share', $errors)) ? $socialsettings[0]['twitter_share'] : $validator['twitter_share']; ?>"></div>
                            <?php if(isset($errors) && array_key_exists('twitter_share',$errors)){ echo "<span class='error'>".ucfirst($errors['twitter_share'])."</span>";}?></td>
                    </tr>
                    
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('google_share'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="google_share" id="google_share"  title="<?php echo __('enter_google_share'); ?>" maxlength="100" value="<?php echo isset($socialsettings) && (!array_key_exists('google_share', $errors)) ? $socialsettings[0]['google_share'] : $validator['google_share']; ?>"></div>
                            <?php if(isset($errors) && array_key_exists('google_share',$errors)){ echo "<span class='error'>".ucfirst($errors['google_share'])."</span>";}?></td>
                    </tr>
                    
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('linkedin_share'); ?></label><span class="star">*</span></td>   
                        <td><div class="new_input_field"><input type="text" name="linkedin_share" id="linkedin_share"  title="<?php echo __('enter_linkedin_share'); ?>" maxlength="100" value="<?php echo isset($socialsettings) && (!array_key_exists('linkedin_share', $errors)) ? $socialsettings[0]['linkedin_share'] : $validator['linkedin_share']; ?>"></div>
                            <?php if(isset($errors) && array_key_exists('linkedin_share',$errors)){ echo "<span class='error'>".ucfirst($errors['linkedin_share'])."</span>";}?></td>
                    </tr>
				<tr>
					<td valign="top" width="20%"><label><?php echo __('facebook_follow_link'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="facebook_follow_link" id="facebook_follow_link" title="<?php echo __('enter_facebook_follow_link'); ?>" value="<?php echo isset($socialsettings) && (!array_key_exists('facebook_follow_link', $errors)) ? $socialsettings[0]['facebook_follow_link'] : $validator['facebook_follow_link']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('facebook_follow_link',$errors)){ echo "<span class='error'>".ucfirst($errors['facebook_follow_link'])."</span>";}?></td>
				</tr>

				<tr>
					<td valign="top" width="20%"><label><?php echo __('twitter_follow_link'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="twitter_follow_link" id="twitter_follow_link"  title="<?php echo __('enter_twitter_follow_link'); ?>" value="<?php echo isset($socialsettings) && (!array_key_exists('twitter_follow_link', $errors)) ? $socialsettings[0]['twitter_follow_link'] : $validator['twitter_follow_link']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('twitter_follow_link',$errors)){ echo "<span class='error'>".ucfirst($errors['twitter_follow_link'])."</span>";}?></td>
				</tr>
				
				<tr>
					<td valign="top" width="20%"><label><?php echo __('google_follow_link'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="google_follow_link" id="google_follow_link"  title="<?php echo __('enter_google_follow_link'); ?>" value="<?php echo isset($socialsettings) && (!array_key_exists('google_follow_link', $errors)) ? $socialsettings[0]['google_follow_link'] : $validator['google_follow_link']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('google_follow_link',$errors)){ echo "<span class='error'>".ucfirst($errors['google_follow_link'])."</span>";}?></td>
				</tr>
				<tr>
					<td valign="top" width="20%"><label><?php echo __('itune_passenger'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="itune_passenger" id="itune_passenger" title="<?php echo __('enter_itune_passenger'); ?>" value="<?php echo isset($socialsettings) && (!array_key_exists('itune_passenger', $errors)) ? $socialsettings[0]['itune_passenger'] : $validator['itune_passenger']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('itune_passenger',$errors)){ echo "<span class='error'>".ucfirst($errors['itune_passenger'])."</span>";}?></td>
				</tr>

				<tr>
					<td valign="top" width="20%"><label><?php echo __('itune_driver'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="itune_driver" id="itune_driver"  title="<?php echo __('enter_itune_driver'); ?>" value="<?php echo isset($socialsettings) && (!array_key_exists('itune_driver', $errors)) ? $socialsettings[0]['itune_driver'] : $validator['itune_driver']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('itune_driver',$errors)){ echo "<span class='error'>".ucfirst($errors['itune_driver'])."</span>";}?></td>
				</tr>
				
				<tr>
					<td valign="top" width="20%"><label><?php echo __('fb_profile'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="fb_profile" id="fb_profile"  title="<?php echo __('enter_fb_profile'); ?>" value="<?php echo isset($socialsettings) && (!array_key_exists('fb_profile', $errors)) ? $socialsettings[0]['fb_profile'] : $validator['fb_profile']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('fb_profile',$errors)){ echo "<span class='error'>".ucfirst($errors['fb_profile'])."</span>";}?></td>
				</tr>
                    
                    <tr>
			<td class="empt_cel">&nbsp;</td>
                        <td colspan="" class="star">*<?php echo __('required_label'); ?></td>
                    </tr>                                
                    <tr>
                        <td valign="top">&nbsp;</td>
                        <td style="padding-left:0px;">
							<div class="new_button">  <input type="submit" name="editsocialsettings_submit" title ="<?php echo __('button_update'); ?>" value="<?php echo __('button_update'); ?>"></div>
                            <div class="new_button"> <input type="reset" name="editsocialsettings_reset" title="<?php echo __('button_reset'); ?>" value="<?php echo __('button_reset'); ?>"></div>
                            

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
