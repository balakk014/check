<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle site_settingss">
            <form method="POST" enctype="multipart/form-data" class="form" action="" name="settings" id="settings" >
                <table class="0" cellpadding="5" cellspacing="0" width="100%">
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('header_background'); ?></label><span class="star">*</span></td>   
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="header_background" id="header_background" class="jscolor" value="<?php echo isset($site_settings[0]['website_header_background']) && (!array_key_exists('header_background', $errors)) ? $site_settings[0]['website_header_background'] : $validator['header_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('header_background',$errors)){ echo "<span class='error'>".ucfirst($errors['header_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '000')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('footer_background'); ?></label><span class="star">*</span></td>   
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="footer_background" id="footer_background" class="jscolor" value="<?php echo isset($site_settings[0]['website_footer_background']) && (!array_key_exists('footer_background', $errors)) ? $site_settings[0]['website_footer_background'] : $validator['footer_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('footer_background',$errors)){ echo "<span class='error'>".ucfirst($errors['footer_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '000')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sidebar_background'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="sidebar_background" id="sidebar_background" class="jscolor" value="<?php echo isset($site_settings[0]['website_sidebar_background']) && (!array_key_exists('sidebar_background', $errors)) ? $site_settings[0]['website_sidebar_background'] : $validator['sidebar_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('sidebar_background',$errors)){ echo "<span class='error'>".ucfirst($errors['sidebar_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => 'fff')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sidebar_icon'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="sidebar_icon" id="sidebar_icon" class="jscolor" value="<?php echo isset($site_settings[0]['website_sidebar_icon']) && (!array_key_exists('sidebar_icon', $errors)) ? $site_settings[0]['website_sidebar_icon'] : $validator['sidebar_icon']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('sidebar_icon',$errors)){ echo "<span class='error'>".ucfirst($errors['sidebar_icon'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => 'e71818')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sidebar_icon_active'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="sidebar_icon_active" id="sidebar_icon_active" class="jscolor" value="<?php echo isset($site_settings[0]['website_sidebar_icon_active']) && (!array_key_exists('sidebar_icon_active', $errors)) ? $site_settings[0]['website_sidebar_icon_active'] : $validator['sidebar_icon_active']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('sidebar_icon_active',$errors)){ echo "<span class='error'>".ucfirst($errors['sidebar_icon_active'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => 'fff')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sidebar_active'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="sidebar_active" id="sidebar_active" class="jscolor" value="<?php echo isset($site_settings[0]['website_sidebar_active']) && (!array_key_exists('sidebar_active', $errors)) ? $site_settings[0]['website_sidebar_active'] : $validator['sidebar_active']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('sidebar_active',$errors)){ echo "<span class='error'>".ucfirst($errors['sidebar_active'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => 'e71818')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('button_background'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="button_background" id="button_background" class="jscolor" value="<?php echo isset($site_settings[0]['website_button_background']) && (!array_key_exists('button_background', $errors)) ? $site_settings[0]['website_button_background'] : $validator['button_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('button_background',$errors)){ echo "<span class='error'>".ucfirst($errors['button_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => 'ee3324')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('button_hover_background'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="button_hover_background" id="button_hover_background" class="jscolor" value="<?php echo isset($site_settings[0]['website_button_hover_background']) && (!array_key_exists('button_hover_background', $errors)) ? $site_settings[0]['website_button_hover_background'] : $validator['button_hover_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('button_hover_background',$errors)){ echo "<span class='error'>".ucfirst($errors['button_hover_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '000')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="empt_cel">&nbsp;</td>
                        <td colspan="" class="star">*<?php echo __('required_label'); ?></td>
                    </tr>
                    <tr>
                        <td valign="top">&nbsp;</td>
                        <td style="padding-left:0px;">
                            <div class="new_button">  <input type="submit" name="website_theme_settings_submit" title ="<?php echo __('button_update'); ?>" value="<?php echo __('button_update'); ?>"></div>
                            <div class="new_button"> <input type="reset" name="website_theme_settings_reset" title="<?php echo __('button_reset'); ?>" value="<?php echo __('button_reset'); ?>"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt" ></div></div>
    </div>

</div>
<script src="<?php echo SCRIPTPATH; ?>jscolor.js" type="text/javascript"></script>