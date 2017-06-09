<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<script src="<?php echo SCRIPTPATH; ?>jscolor.js" type="text/javascript"></script>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle site_settingss">
            <form method="POST" enctype="multipart/form-data" class="form" action="" name="settings" id="settings" >
                <table class="0" cellpadding="5" cellspacing="0" width="100%">
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('header_background'); ?></label><span class="star">*</span></td>   
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="header_background" id="header_background" class="jscolor {onFineChange:'headerbackground(this)'}" value="<?php echo isset($site_settings[0]['admin_header_background']) && (!array_key_exists('header_background', $errors)) ? $site_settings[0]['admin_header_background'] : $validator['header_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('header_background',$errors)){ echo "<span class='error'>".ucfirst($errors['header_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => 'fff')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('dispatch_header_background'); ?></label><span class="star">*</span></td>   
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="dispatch_header_background" id="dispatch_header_background" class="jscolor" value="<?php echo isset($site_settings[0]['dispatch_header_background']) && (!array_key_exists('dispatch_header_background', $errors)) ? $site_settings[0]['dispatch_header_background'] : $validator['dispatch_header_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('dispatch_header_background',$errors)){ echo "<span class='error'>".ucfirst($errors['dispatch_header_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '171717')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('package_footer_background'); ?></label><span class="star">*</span></td>   
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="footer_background" id="footer_background" class="jscolor {onFineChange:'footerbackground(this)'}" value="<?php echo isset($site_settings[0]['admin_footer_background']) && (!array_key_exists('footer_background', $errors)) ? $site_settings[0]['admin_footer_background'] : $validator['footer_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('footer_background',$errors)){ echo "<span class='error'>".ucfirst($errors['footer_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '171717')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sidebar_background'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="sidebar_background" id="sidebar_background" class="jscolor {onFineChange:'sidebarbackground(this)'}" value="<?php echo isset($site_settings[0]['admin_sidebar_background']) && (!array_key_exists('sidebar_background', $errors)) ? $site_settings[0]['admin_sidebar_background'] : $validator['sidebar_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('sidebar_background',$errors)){ echo "<span class='error'>".ucfirst($errors['sidebar_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '171717')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sidebar_sub_tab'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="sidebar_sub_tab" id="sidebar_sub_tab" class="jscolor {onFineChange:'sidebarsubtab(this)'}" value="<?php echo isset($site_settings[0]['admin_sidebar_sub_tab']) && (!array_key_exists('sidebar_sub_tab', $errors)) ? $site_settings[0]['admin_sidebar_sub_tab'] : $validator['sidebar_sub_tab']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('sidebar_sub_tab',$errors)){ echo "<span class='error'>".ucfirst($errors['sidebar_sub_tab'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '111')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sidebar_icon'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="sidebar_icon" id="sidebar_icon" class="jscolor {onFineChange:'sidebaricon(this)'}" value="<?php echo isset($site_settings[0]['admin_sidebar_icon']) && (!array_key_exists('sidebar_icon', $errors)) ? $site_settings[0]['admin_sidebar_icon'] : $validator['sidebar_icon']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('sidebar_icon',$errors)){ echo "<span class='error'>".ucfirst($errors['sidebar_icon'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '95a7b7')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sidebar_icon_active'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="sidebar_icon_active" id="sidebar_icon_active" class="jscolor {onFineChange:'sidebariconactive(this)'}" value="<?php echo isset($site_settings[0]['admin_sidebar_icon_active']) && (!array_key_exists('sidebar_icon_active', $errors)) ? $site_settings[0]['admin_sidebar_icon_active'] : $validator['sidebar_icon_active']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('sidebar_icon_active',$errors)){ echo "<span class='error'>".ucfirst($errors['sidebar_icon_active'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => 'ee3324')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sidebar_icon_circle'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="sidebar_icon_circle" id="sidebar_icon_circle" class="jscolor {onFineChange:'sidebariconcircle(this)'}" value="<?php echo isset($site_settings[0]['admin_sidebar_icon_circle']) && (!array_key_exists('sidebar_icon_circle', $errors)) ? $site_settings[0]['admin_sidebar_icon_circle'] : $validator['sidebar_icon_circle']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('sidebar_icon_circle',$errors)){ echo "<span class='error'>".ucfirst($errors['sidebar_icon_circle'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '303030')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('sidebar_active'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="sidebar_active" id="sidebar_active" class="jscolor {onFineChange:'sidebaractive(this)'}" value="<?php echo isset($site_settings[0]['admin_sidebar_active']) && (!array_key_exists('sidebar_active', $errors)) ? $site_settings[0]['admin_sidebar_active'] : $validator['sidebar_active']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('sidebar_active',$errors)){ echo "<span class='error'>".ucfirst($errors['sidebar_active'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '1d1d1d')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('button_background'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="button_background" id="button_background" class="jscolor {onFineChange:'buttonbackground(this)'}" value="<?php echo isset($site_settings[0]['admin_button_background']) && (!array_key_exists('button_background', $errors)) ? $site_settings[0]['admin_button_background'] : $validator['button_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('button_background',$errors)){ echo "<span class='error'>".ucfirst($errors['button_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => 'fff')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('button_hover_background'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="button_hover_background" id="button_hover_background" class="jscolor {onFineChange:'buttonhoverbackground(this)'}" value="<?php echo isset($site_settings[0]['admin_button_hover_background']) && (!array_key_exists('button_hover_background', $errors)) ? $site_settings[0]['admin_button_hover_background'] : $validator['button_hover_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('button_hover_background',$errors)){ echo "<span class='error'>".ucfirst($errors['button_hover_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '0088cc')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('dispatch_button_background'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="dispatch_button_background" id="dispatch_button_background" class="jscolor {onFineChange:'buttonbackground(this)'}" value="<?php echo isset($site_settings[0]['dispatch_button_background']) && (!array_key_exists('dispatch_button_background', $errors)) ? $site_settings[0]['dispatch_button_background'] : $validator['dispatch_button_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('dispatch_button_background',$errors)){ echo "<span class='error'>".ucfirst($errors['dispatch_button_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => 'ddd')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('dispatch_button_hover_background'); ?></label><span class="star">*</span></td>
                        <td>
                            <div class="new_input_field">
                                <input type="text" name="dispatch_button_hover_background" id="dispatch_button_hover_background" class="jscolor {onFineChange:'buttonhoverbackground(this)'}" value="<?php echo isset($site_settings[0]['dispatch_button_hover_background']) && (!array_key_exists('dispatch_button_hover_background', $errors)) ? $site_settings[0]['dispatch_button_hover_background'] : $validator['dispatch_button_hover_background']; ?>">
                            </div>
                            <?php if(isset($errors) && array_key_exists('dispatch_button_hover_background',$errors)){ echo "<span class='error'>".ucfirst($errors['dispatch_button_hover_background'])."</span>"; } ?>
                            <span class="textclass fl clr"><?php echo __('reset_info',array(':param' => '222')); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="empt_cel">&nbsp;</td>
                        <td colspan="" class="star">*<?php echo __('required_label'); ?></td>
                    </tr>
                    <tr>
                        <td valign="top">&nbsp;</td>
                        <td style="padding-left:0px;">
                            <div class="new_button">  <input type="submit" name="admin_theme_settings_submit" title ="<?php echo __('button_update'); ?>" value="<?php echo __('button_update'); ?>"></div>
                            <div class="new_button"> <input type="reset" name="admin_theme_settings_reset" title="<?php echo __('button_reset'); ?>" value="<?php echo __('button_reset'); ?>"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt" ></div></div>
    </div>
</div>
<script>
    function headerbackground(jscolor) { $('.header').css("background-color", '#' + jscolor); }
    function footerbackground(jscolor) { $('.sticky_message').css("background-color", '#' + jscolor); }
    function sidebarbackground(jscolor) { $('.main-sidebar, .logo').css("background-color", '#' + jscolor); }
    function sidebarsubtab(jscolor) { $('.menu_drop_down').css("background-color", '#' + jscolor); }
    function sidebaricon(jscolor) { $('.icon_24').css("fill", '#' + jscolor); }
    function sidebariconactive(jscolor) { $('.menu > ul > li:hover > a .icon_24, .menu > ul > li.active > a .icon_24').css("fill", '#' + jscolor); }
    function sidebariconcircle(jscolor) { $('.menu > ul > li.active > a i, .menu > ul > li:hover > a i').css("background-color", '#' + jscolor); }
    function sidebaractive(jscolor) { $('.menu > ul > li.active').css("background-color", '#' + jscolor); }
    function buttonbackground(jscolor) { $('.new_button input[type=submit], .new_button input[type=button], .new_button input[type=reset]').css("background-color", '#' + jscolor); }
    function buttonhoverbackground(jscolor) { $('.new_button input[type=submit]:hover, .new_button input[type=button]:hover, .new_button input[type=reset]:hover').css("background-color", '#' + jscolor); }
</script>
