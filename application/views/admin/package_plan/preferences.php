<?php defined('SYSPATH') OR die("No direct access allowed.");
    $default_check = $ios_driver_lang_default_check = $ios_passenger_lang_default_check = $ios_driver_color_default_check = $ios_passenger_color_default_check =$android_driver_lang_default_check = $android_passenger_lang_default_check = $android_driver_color_default_check = $android_passenger_color_default_check = 'checked="checked"';
    $custom_check = $ios_driver_lang_custom_check = $ios_passenger_lang_custom_check = $ios_driver_color_custom_check = $ios_passenger_color_custom_check =$android_driver_lang_custom_check = $android_passenger_lang_custom_check = $android_driver_color_custom_check = $android_passenger_color_custom_check = '';
    $display_val = $ios_driver_lang_display_val = $ios_passenger_lang_display_val = $ios_driver_color_display_val = $ios_passenger_color_display_val = $android_driver_lang_display_val = $android_passenger_lang_display_val = $android_driver_color_display_val = $android_passenger_color_display_val = 'style="display:none"';
    $display_href_default = $ios_driver_lang_display_href_default = $ios_passenger_lang_display_href_default = $ios_driver_color_display_href_default = $ios_passenger_color_display_href_default = $android_driver_lang_display_href_default = $android_passenger_lang_display_href_default = $android_driver_color_display_href_default = $android_passenger_color_display_href_default = 'style="display:block"';
    $display_href_custom = $ios_driver_lang_display_href_custom = $ios_passenger_lang_display_href_custom = $ios_driver_color_display_href_custom = $ios_passenger_color_display_href_custom = $android_driver_lang_display_href_custom = $android_passenger_lang_display_href_custom = $android_driver_color_display_href_custom = $android_passenger_color_display_href_custom = 'style="display:none"';
    $web_custome_href = $ios_driver_lang_custome_href = $ios_passenger_lang_custome_href = $ios_driver_color_custome_href =  $ios_passenger_color_custome_href = $android_driver_lang_custome_href = $android_passenger_lang_custome_href = $android_driver_color_custome_href =  $android_passenger_color_custome_href = "";
    $li_web = $li_ios = $li_android = "";
    if($action == "web_language"){
    	$li_web = "active";
    }elseif($action == "ios_language_colorcode"){
    	$li_ios = "active";
    }elseif($action == "android_language_colorcode"){
    	$li_android = "active";
    }else{
    	$li_web = "active";
    }
    $dynamic_language_array = DYNAMIC_LANGUAGE_ARRAY;
    if(isset($postvalue['dynamic_lang']) && $postvalue['dynamic_lang']!=""){
        $selected_language = $postvalue['dynamic_lang'];
    }else{
        $selected_language = SELECTED_LANGUAGE;
    }
    $selected_language_name = ucfirst($dynamic_language_array[$selected_language]);
    $dynamin_lang_input = '<input type="hidden" class="dynamic_lang" name="dynamic_lang" value="'.$selected_language.'">';
?>

   <div class="select_language">
<label><?php echo __('select_language'); ?><span class="star">*</span></label>
                <div class="formRight">
                <div class="selector" id="uniform-user_type">
                <div id="taxicompany_list">
                    <select class="required" id="selected_language">
                        <option value=""><?php echo __('select_label'); ?></option>
                        <?php foreach($dynamic_language_array as $key => $value){ 
                            
                           $selected = ($selected_language == $key)?"selected='selected'":"";
                        ?>
                            <option value="<?php echo $key; ?>" <?php echo $selected; ?> ><?php echo ucfirst($value)?></option>
                        <?php } ?>
                    </select>
                </div>
                </div>
                <?php if(isset($errors) && array_key_exists('dynamic_lang',$errors)){  $dynamic_style = "style='display:block'"; }else{ $dynamic_style = "style='display:none'"; } ?>
                <em <?php echo $dynamic_style; ?> class="errorvalid language_error" ><?php echo __('select_language'); ?></em>
                </div>
                
</div>   
<input type="hidden" value="<?php echo $selected_language; ?>" id="old_value_set">
<input type="hidden" value="<?php echo ucfirst($dynamic_language_array[$selected_language]); ?>" id="old_value_set_name">           
<div class="next_grid_cell">
    <div class="next_card">
        <div class="has_bulk_actions">
            
            <div class="tabs">
                <ul class="nav nav-tabs">
                    <li class="<?php echo $li_web; ?>"><a data-toggle="tab" aria-expanded="true" href="#Web" title="<?php echo __('web'); ?>"><?php echo __('web'); ?></a></li>
                    <li class="<?php echo $li_ios; ?>"><a data-toggle="tab" aria-expanded="true" href="#IOS" title="<?php echo __('ios'); ?>"><?php echo __('ios'); ?></a></li>
                    <li class="<?php echo $li_android; ?>"><a data-toggle="tab" aria-expanded="true" href="#Android" title="<?php echo __('android'); ?>"><?php echo __('android'); ?></a></li>
                </ul>
                <div class="tab-content" id="preferenceID">
                    <span class="language_note"><?php echo __('file_upload_note_info'); ?></span>
                    <div id="Web" class="tab-pane <?php echo $li_web; ?>">
                        <div class="account_det_list" style="border: none;">
                            <div class="account_lft_det">
                                <div class="acc_tit">
                                    <h2><?php echo __('web_lang'); ?></h2>
                                </div>
                                <div class="acc_det">
                                    <p><?php echo __('web_lang_info'); ?></p>
                                </div>
                            </div>
                            <div class="account_rgt_det">
                                
                                <div class="rgt_lay">
                                    <form name="web_language" class="form" id="web_language" action="web_language" method="post" enctype="multipart/form-data">
                                        <?php
                                            echo $dynamin_lang_input;
                                            if(isset($langcolor_info['website_language_settings']) && $langcolor_info['website_language_settings'] == 2){
                                                $default_check = '';
                                                $display_href_default = 'style="display:none"';
                                                $custom_check = 'checked="checked"';
                                                $display_val = $display_href_custom = 'style="display:block"';
                                                /*if(file_exists(APPPATH.'i18n/'.$selected_language.'_default.xml')){
                                                    $web_custome_href =  URL_BASE.'download/downloadfiles?file_path='.$selected_language.'_default.xml';
                                                }
                                                if(file_exists(CUSTOMLANGPATH.'i18n/'.$selected_language.'_customize.xml')) {
                                                    $web_custome_href =  URL_BASE.'download/downloadfiles?file_path='.$selected_language.'_customize.xml';
                                                }*/
                                            }elseif(isset($postvalue['web_lang_radio']) && $postvalue['web_lang_radio'] == 2){
                                                $default_check = '';
                                                $display_href_default = 'style="display:none"';
                                                $custom_check = 'checked="checked"';
                                                $display_val = 'style="display:block"';
                                            }
                                            
                                            $web_default_href='';
                                            $web_custome_href='';
                                        if(file_exists(CUSTOMLANGPATH.'i18n/'.$selected_language.'_customize.xml')) {
                                            $web_custome_href =  URL_BASE.'download/downloadfiles?file_path='.$selected_language.'_customize.xml';
                                            }
                                            
                                           if(file_exists(APPPATH.'i18n/'.$selected_language.'_default.xml')){
                                                $web_default_href =  URL_BASE.'download/downloadfiles?file_path='.$selected_language.'_default.xml';
                                            }
                                        ?>
                                        <?php if($web_default_href!=""){ ?>
                                        <a id="web_href_default" href="<?php echo $web_default_href.'&file_name=Web_lang_default-'.$selected_language.'.xml&mime_type=text/plain'; ?>" class="download_lang_custom" title="<?php echo __('download_web_lang',array(':param' => 'default')); ?>" <?php echo $display_href_default; ?> >&nbsp;</a>
                                        <?php } if($web_custome_href!=""){ ?>
                                            <a id="web_href_custom" href="<?php echo $web_custome_href.'&file_name=Web_lang_customize-'.$selected_language.'.xml&mime_type=text/plain'; ?>" class="download_lang_custom" title="<?php echo __('download_web_lang',array(':param' => 'customize')); ?>" <?php echo $display_href_custom; ?> >&nbsp;</a>
                                        <?php } ?>
                                        <div class="choose_lang_opt">
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="1" name="web_lang_radio" class="class_radio" <?php echo $default_check; ?> id="web_language_default" onclick="display_button('1','web_language_file','web_href_default','web_href_custom')" />
                                                <label for="default"><?php echo __('default'); ?></label>
                                            </div>
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="2" name="web_lang_radio" class="class_radio" <?php echo $custom_check; ?> id="web_language_customize" onclick="display_button('2','web_language_file','web_href_default','web_href_custom')"/>
                                                <label for="customize"><?php echo __('customize'); ?></label>
                                            </div>
                                        </div>
                                        <div class="form_group inputfile" id="web_language_file" <?php echo $display_val; ?> >
                                            <input type="file" name="web_language_file" value=""/>
                                            <span class="textclass fl clr"><?php echo __('file_format_info',array(':param' => '.xml')); ?></span>
                                            <?php if(isset($errors) && array_key_exists('web_language_file',$errors)){ echo "<span class='error'>".ucfirst($errors['web_language_file'])."</span>";}?>
                                        </div>
                                        <div class="form_group" style="margin: 0;">
                                            <input class="common_butt" type="button" name="submit_web_language" value="<?php echo __('button_update'); ?>" onclick="formsubmit('web_language')" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="IOS" class="tab-pane <?php echo $li_ios; ?>">
                        <div class="account_det_list">
                            <div class="account_lft_det">
                                <div class="acc_tit">
                                    <h2><?php echo __('driver_app_lang'); ?></h2>
                                </div>
                                <div class="acc_det">
                                    <p><?php echo __('driver_app_lang_info'); ?></p>
                                </div>
                            </div>
                            <div class="account_rgt_det">
                                <div class="rgt_lay">
                                    <form name="ios_driver_language" class="form" id="ios_driver_language" action="ios_language_colorcode" method="post" enctype="multipart/form-data">
                                        <?php
                                            echo $dynamin_lang_input;
                                            $default_target_path = DOCROOT.IOS_DEFAULT_CUSTOMIZE_FILES.'driver/';
                                            if(file_exists($default_target_path.'Localizable_'.$selected_language_name.'_customize.strings')){
                                                $ios_driver_lang_custome_href =  URL_BASE.'download/downloadfiles?file_path=Localizable_'.$selected_language_name.'_customize.strings';
                                            }
                                            if(isset($langcolor_info['ios_driver_language_settings']) && $langcolor_info['ios_driver_language_settings'] == 2){
                                                $ios_driver_lang_display_href_default = 'style="display:none"';
                                                $ios_driver_lang_default_check = '';
                                            	$ios_driver_lang_custom_check = 'checked="checked"';
                                                $ios_driver_lang_display_val = $ios_driver_lang_display_href_custom = 'style="display:block"';
                                                if(file_exists($default_target_path.'Localizable_'.$selected_language_name.'_default.strings')){
                                                    $ios_driver_lang_custome_href =  URL_BASE.'download/downloadfiles?file_path=Localizable_'.$selected_language_name.'.strings';
                                                }
                                            }elseif(isset($postvalue['ios_driver_lang_radio']) && $postvalue['ios_driver_lang_radio'] == 2){
                                                $ios_driver_lang_display_href_default = 'style="display:none"';
                                                $ios_driver_lang_default_check = '';
                                            	$ios_driver_lang_custom_check = 'checked="checked"';
                                                $ios_driver_lang_display_val = $ios_driver_lang_display_href_custom = 'style="display:block"';
                                            }
                                            if(file_exists($default_target_path.'Localizable_'.$selected_language_name.'_default.strings')){
                                                $ios_driver_lang_default_href =  URL_BASE.'download/downloadfiles?file_path=Localizable_'.$selected_language_name.'_default.strings';
                                            }else{
                                                $ios_driver_lang_default_href =  URL_BASE.'download/downloadfiles?file_path=Localizable_'.$selected_language_name.'.strings';
                                            }
                                        ?>
                                        <a id="ios_driver_lang_href_default" href="<?php echo $ios_driver_lang_default_href.'&file_name=Driver_IOS-Default_Language.strings&mime_type=text/plain'; ?>" class="download_lang_custom" title="<?php echo __('download_ios_driver_app_lang',array(':param' => 'default')); ?>" <?php echo $ios_driver_lang_display_href_default; ?> >&nbsp;</a>
                                        <?php if($ios_driver_lang_custome_href!=""){ ?>
                                            <a id="ios_driver_lang_href_custom" href="<?php echo $ios_driver_lang_custome_href.'&file_name=Driver_IOS-Customize_Language.strings&mime_type=text/plain'; ?>" class="download_lang_custom" title="<?php echo __('download_ios_driver_app_lang',array(':param' => 'customize')); ?>" <?php echo $ios_driver_lang_display_href_custom; ?> >&nbsp;</a>
                                        <?php } ?>
                                        <div class="choose_lang_opt">
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="1" name="ios_driver_lang_radio" class="class_radio" <?php echo $ios_driver_lang_default_check; ?> id="ios_driver_language_default" onclick="display_button('1','ios_driver_language_file','ios_driver_lang_href_default','ios_driver_lang_href_custom')" />
                                                <label for="default"><?php echo __('default'); ?></label>
                                            </div>
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="2" name="ios_driver_lang_radio" class="class_radio" <?php echo $ios_driver_lang_custom_check; ?> id="ios_driver_language_customize" onclick="display_button('2','ios_driver_language_file','ios_driver_lang_href_default','ios_driver_lang_href_custom')"/>
                                                <label for="customize"><?php echo __('customize'); ?></label>
                                            </div>
                                        </div>
                                        <div class="form_group inputfile" id="ios_driver_language_file" <?php echo $ios_driver_lang_display_val; ?> >
                                            <input type="file" name="ios_driver_language_file" value=""/>
                                            <span class="textclass fl clr"><?php echo __('file_format_info',array(':param' => '.strings')); ?></span>
                                            <?php if(isset($errors) && array_key_exists('ios_driver_language_file',$errors)){ echo "<span class='error'>".ucfirst($errors['ios_driver_language_file'])."</span>";}?>
                                        </div>
                                        <div class="form_group" style="margin: 0;">
                                            <input class="common_butt" type="button" name="submit_ios_driver_language" value="<?php echo __('button_update'); ?>" onclick="formsubmit('ios_driver_language')" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="account_det_list">
                            <div class="account_lft_det">
                                <div class="acc_tit">
                                    <h2><?php echo __('passenger_app_lang'); ?></h2>
                                </div>
                                <div class="acc_det">
                                    <p><?php echo __('passenger_app_lang_info'); ?></p>
                                </div>
                            </div>
                            <div class="account_rgt_det">
                                <div class="rgt_lay">
                                    <form name="ios_passenger_language" class="form" id="ios_passenger_language" action="ios_language_colorcode" method="post" enctype="multipart/form-data">                                        
                                        <?php
                                            echo $dynamin_lang_input;
                                            $default_target_path = DOCROOT.IOS_DEFAULT_CUSTOMIZE_FILES.'passenger/';
                                            if(file_exists($default_target_path.'Localizable_'.$selected_language_name.'_customize.strings')){
                                                $ios_passenger_lang_custome_href =  URL_BASE.'download/downloadfiles?file_path=Localizable_'.$selected_language_name.'_customize.strings';
                                            }
                                            if(isset($langcolor_info['ios_passenger_language_settings']) && $langcolor_info['ios_passenger_language_settings'] == 2){
                                                $ios_passenger_lang_display_href_default = 'style="display:none"';
                                                $ios_passenger_lang_default_check = '';
                                            	$ios_passenger_lang_custom_check = 'checked="checked"';
                                                $ios_passenger_lang_display_val = $ios_passenger_lang_display_href_custom = 'style="display:block"';
                                                if(file_exists($default_target_path.'Localizable_'.$selected_language_name.'_default.strings')){
                                                    $ios_passenger_lang_custome_href =  URL_BASE.'download/downloadfiles?file_path=Localizable_'.$selected_language_name.'.strings';
                                                }
                                            }elseif(isset($postvalue['ios_passenger_lang_radio']) && $postvalue['ios_passenger_lang_radio'] == 2){
                                                $ios_passenger_lang_display_href_default = 'style="display:none"';
                                                $ios_passenger_lang_default_check = '';
                                            	$ios_passenger_lang_custom_check = 'checked="checked"';
                                                $ios_passenger_lang_display_val = $ios_passenger_lang_display_href_custom = 'style="display:block"';
                                            }
                                            if(file_exists($default_target_path.'Localizable_'.$selected_language_name.'_default.strings')){
                                                $ios_passenger_lang_default_href =  URL_BASE.'download/downloadfiles?file_path=Localizable_'.$selected_language_name.'_default.strings';
                                            }else{
                                                $ios_passenger_lang_default_href =  URL_BASE.'download/downloadfiles?file_path=Localizable_'.$selected_language_name.'.strings';
                                            }
                                        ?>
                                        <a id="ios_passenger_lang_href_default" href="<?php echo $ios_passenger_lang_default_href.'&file_name=Passenger_IOS-Default_Language.strings&mime_type=text/plain'; ?>" class="download_lang_custom" title="<?php echo __('download_ios_passenger_app_lang',array(':param' => 'default')); ?>" <?php echo $ios_passenger_lang_display_href_default; ?> >&nbsp;</a>
                                        <?php if($ios_passenger_lang_custome_href!=""){ ?>
                                            <a id="ios_passenger_lang_href_custom" href="<?php echo $ios_passenger_lang_custome_href.'&file_name=Passenger_IOS-Customize_Language.strings&mime_type=text/plain'; ?>" class="download_lang_custom" title="<?php echo __('download_ios_passenger_app_lang',array(':param' => 'customize')); ?>" <?php echo $ios_passenger_lang_display_href_custom; ?> >&nbsp;</a>
                                        <?php } ?>
                                        <div class="choose_lang_opt">
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="1" name="ios_passenger_lang_radio" class="class_radio" <?php echo $ios_passenger_lang_default_check; ?> id="ios_passenger_language_default" onclick="display_button('1','ios_passenger_language_file','ios_passenger_lang_href_default','ios_passenger_lang_href_custom')"/>
                                                <label for="default"><?php echo __('default'); ?></label>
                                            </div>
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="2" name="ios_passenger_lang_radio" class="class_radio" <?php echo $ios_passenger_lang_custom_check; ?> id="ios_passenger_language_customize" onclick="display_button('2','ios_passenger_language_file','ios_passenger_lang_href_default','ios_passenger_lang_href_custom')"/>
                                                <label for="customize"><?php echo __('customize'); ?></label>
                                            </div>
                                        </div>
                                        <div class="form_group inputfile" id="ios_passenger_language_file" <?php echo $ios_passenger_lang_display_val; ?> >
                                            <input type="file" name="ios_passenger_language_file" value=""/>
                                            <span class="textclass fl clr"><?php echo __('file_format_info',array(':param' => '.strings')); ?></span>
                                            <?php if(isset($errors) && array_key_exists('ios_passenger_language_file',$errors)){ echo "<span class='error'>".ucfirst($errors['ios_passenger_language_file'])."</span>";}?>
                                        </div>
                                        <div class="form_group" style="margin: 0;">
                                            <input class="common_butt" type="button" name="submit_ios_passenger_language" value="<?php echo __('button_update'); ?>" onclick="formsubmit('ios_passenger_language')"/>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="account_det_list">
                            <div class="account_lft_det">
                                <div class="acc_tit">
                                    <h2><?php echo __('driver_app_color_code'); ?></h2>
                                </div>
                                <div class="acc_det">
                                    <p><?php echo __('driver_app_color_code_info'); ?></p>
                                </div>
                            </div>
                            <div class="account_rgt_det">
                                <div class="rgt_lay">
                                    <form name="ios_driver_colorcode" class="form" id="ios_driver_colorcode" action="ios_language_colorcode" method="post" enctype="multipart/form-data">
                                        <?php
                                            echo $dynamin_lang_input;
                                            $default_target_path = DOCROOT.IOS_DEFAULT_CUSTOMIZE_FILES;
                                            if(file_exists($default_target_path.'DriverAppColor_customize.xml')){
                                                $ios_driver_color_custome_href =  URL_BASE.'download/downloadfiles?file_path=DriverAppColor_customize.xml';
                                            }
                                            if(isset($langcolor_info['ios_driver_colorcode_settings']) && $langcolor_info['ios_driver_colorcode_settings'] == 2){
                                                $ios_driver_color_display_href_default = 'style="display:none"';
                                                $ios_driver_color_default_check = '';
                                            	$ios_driver_color_custom_check = 'checked="checked"';
                                                $ios_driver_color_display_val = $ios_driver_color_display_href_custom = 'style="display:block"';
                                                if(file_exists($default_target_path.'DriverAppColor_default.xml')){
                                                    $ios_driver_color_custome_href =  URL_BASE.'download/downloadfiles?file_path=DriverAppColor.xml';
                                                }
                                            }elseif(isset($postvalue['ios_driver_colorcode_radio']) && $postvalue['ios_driver_colorcode_radio'] == 2){
                                                $ios_driver_color_display_href_default = 'style="display:none"';
                                                $ios_driver_color_default_check = '';
                                            	$ios_driver_color_custom_check = 'checked="checked"';
                                                $ios_driver_color_display_val = $ios_driver_color_display_href_custom = 'style="display:block"';
                                            }
                                            if(file_exists($default_target_path.'DriverAppColor_default.xml')){
                                                $ios_driver_color_default_href =  URL_BASE.'download/downloadfiles?file_path=DriverAppColor_default.xml';
                                            }else{
                                                $ios_driver_color_default_href =  URL_BASE.'download/downloadfiles?file_path=DriverAppColor.xml';
                                            }
                                        ?>
                                         <a id="ios_driver_color_href_default" href="<?php echo $ios_driver_color_default_href.'&file_name=IOS_Driver_colorcode-Default.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_ios_driver_app_colorcode',array(':param' => 'default')); ?>" <?php echo $ios_driver_color_display_href_default; ?> >&nbsp;</a>
                                        <?php if($ios_driver_color_custome_href!=""){ ?>
                                            <a id="ios_driver_color_href_custom" href="<?php echo $ios_driver_color_custome_href.'&file_name=IOS_Driver_colorcode-Customize.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_ios_driver_app_colorcode',array(':param' => 'customize')); ?>" <?php echo $ios_driver_color_display_href_custom; ?> >&nbsp;</a>
                                        <?php } ?>
                                        <div class="choose_lang_opt">
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="1" name="ios_driver_colorcode_radio" class="class_radio" <?php echo $ios_driver_color_default_check; ?> id="ios_driver_colorcode_default" onclick="display_button('1','ios_driver_colorcode_file','ios_driver_color_href_default','ios_driver_color_href_custom')" />
                                                <label for="default"><?php echo __('default'); ?></label>
                                            </div>
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="2" name="ios_driver_colorcode_radio" class="class_radio" <?php echo $ios_driver_color_custom_check; ?> id="ios_driver_colorcode_customize" onclick="display_button('2','ios_driver_colorcode_file','ios_driver_color_href_default','ios_driver_color_href_custom')"/>
                                                <label for="customize"><?php echo __('customize'); ?></label>
                                            </div>
                                        </div>
                                        <div class="form_group inputfile" id="ios_driver_colorcode_file" <?php echo $ios_driver_color_display_val; ?> >
                                            <input type="file" name="ios_driver_colorcode_file" value=""/>
                                            <span class="textclass fl clr"><?php echo __('file_format_info',array(':param' => '.xml')); ?></span>
                                            <?php if(isset($errors) && array_key_exists('ios_driver_colorcode_file',$errors)){ echo "<span class='error'>".ucfirst($errors['ios_driver_colorcode_file'])."</span>";}?>
                                        </div>
                                        <div class="form_group" style="margin: 0;">
                                            <input class="common_butt" type="submit" name="submit_ios_driver_colorcode" value="<?php echo __('button_update'); ?>" onclick="formsubmit('ios_driver_colorcode')"/>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="account_det_list" style="border: none;">
                            <div class="account_lft_det">
                                <div class="acc_tit">
                                    <h2><?php echo __('passenger_app_color_code'); ?></h2>
                                </div>
                                <div class="acc_det">
                                    <p><?php echo __('passenger_app_color_code_info'); ?></p>
                                </div>
                            </div>
                            <div class="account_rgt_det">
                                <div class="rgt_lay">
                                    <form name="ios_passenger_colorcode" class="form" id="ios_passenger_colorcode" action="ios_language_colorcode" method="post" enctype="multipart/form-data">    
                                        <?php
                                            echo $dynamin_lang_input;
                                            $default_target_path = DOCROOT.IOS_DEFAULT_CUSTOMIZE_FILES;
                                            if(file_exists($default_target_path.'PassengerAppColor_customize.xml')){
                                                $ios_passenger_color_custome_href =  URL_BASE.'download/downloadfiles?file_path=PassengerAppColor_customize.xml';
                                            }
                                            if(isset($langcolor_info['ios_passenger_colorcode_settings']) && $langcolor_info['ios_passenger_colorcode_settings'] == 2){
                                                $ios_passenger_color_display_href_default = 'style="display:none"';
                                                $ios_passenger_color_default_check = '';
                                            	$ios_passenger_color_custom_check = 'checked="checked"';
                                                $ios_passenger_color_display_val = $ios_passenger_color_display_href_custom = 'style="display:block"';
                                                if(file_exists($default_target_path.'PassengerAppColor_default.xml')){
                                                    $ios_passenger_color_custome_href =  URL_BASE.'download/downloadfiles?file_path=PassengerAppColor.xml';
                                                }
                                            }elseif(isset($postvalue['ios_passenger_colorcode_radio']) && $postvalue['ios_passenger_colorcode_radio'] == 2){
                                                $ios_passenger_color_display_href_default = 'style="display:none"';
                                                $ios_passenger_color_default_check = '';
                                            	$ios_passenger_color_custom_check = 'checked="checked"';
                                                $ios_passenger_color_display_val = $ios_passenger_color_display_href_custom = 'style="display:block"';
                                            }
                                            if(file_exists($default_target_path.'PassengerAppColor_default.xml')){
                                                $ios_passenger_color_default_href =  URL_BASE.'download/downloadfiles?file_path=PassengerAppColor_default.xml';
                                            }else{
                                                $ios_passenger_color_default_href =  URL_BASE.'download/downloadfiles?file_path=PassengerAppColor.xml';
                                            }
                                        ?>
                                         <a id="ios_passenger_color_href_default" href="<?php echo $ios_passenger_color_default_href.'&file_name=IOS_Passenger_colorcode-Default.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_ios_passenger_app_colorcode',array(':param' => 'default')); ?>" <?php echo $ios_passenger_color_display_href_default; ?> >&nbsp;</a>
                                        <?php if($ios_passenger_color_custome_href!=""){ ?>
                                            <a id="ios_passenger_color_href_custom" href="<?php echo $ios_passenger_color_custome_href.'&file_name=IOS_Passenger_colorcode-Customize.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_ios_passenger_app_colorcode',array(':param' => 'customize')); ?>" <?php echo $ios_passenger_color_display_href_custom; ?> >&nbsp;</a>
                                        <?php } ?>
                                        <div class="choose_lang_opt">
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="1" name="ios_passenger_colorcode_radio" class="class_radio" <?php echo $ios_passenger_color_default_check; ?> id="ios_passenger_colorcode_default" onclick="display_button('1','ios_passenger_colorcode_file','ios_passenger_color_href_default','ios_passenger_color_href_custom')"/>
                                                <label for="default"><?php echo __('default'); ?></label>
                                            </div>
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="2" name="ios_passenger_colorcode_radio" class="class_radio" <?php echo $ios_passenger_color_custom_check; ?> id="ios_passenger_colorcode_customize" onclick="display_button('2','ios_passenger_colorcode_file','ios_passenger_color_href_default','ios_passenger_color_href_custom')"/>
                                                <label for="customize"><?php echo __('customize'); ?></label>
                                            </div>
                                        </div>
                                        <div class="form_group inputfile" id="ios_passenger_colorcode_file" <?php echo $ios_passenger_color_display_val; ?> >
                                            <input type="file" name="ios_passenger_colorcode_file" value="" />
                                            <span class="textclass fl clr"><?php echo __('file_format_info',array(':param' => '.xml')); ?></span>
                                            <?php if(isset($errors) && array_key_exists('ios_passenger_colorcode_file',$errors)){ echo "<span class='error'>".ucfirst($errors['ios_passenger_colorcode_file'])."</span>";}?>
                                        </div>
                                        <div class="form_group" style="margin: 0;">
                                            <input class="common_butt" type="button" name="submit_ios_passenger_colorcode" value="<?php echo __('button_update'); ?>" onclick="formsubmit('ios_passenger_colorcode')"/>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="Android" class="tab-pane <?php echo $li_android; ?>">
                        <div class="account_det_list">
                            <div class="account_lft_det">
                                <div class="acc_tit">
                                    <h2><?php echo __('driver_app_lang'); ?></h2>
                                </div>
                                <div class="acc_det">
                                    <p><?php echo __('driver_app_lang_info'); ?></p>
                                </div>
                            </div>
                            <div class="account_rgt_det">
                                <div class="rgt_lay">
                                    <form name="android_driver_language" class="form" id="android_driver_language" action="android_language_colorcode" method="post" enctype="multipart/form-data">
                                        <?php
                                            echo $dynamin_lang_input;
                                            $default_target_path = DOCROOT.ANDROID_DEFAULT_CUSTOMIZE_FILES.'driver/';
                                            if(file_exists($default_target_path.'strings_'.$selected_language_name.'_customize.xml')){
                                                $android_driver_lang_custome_href =  URL_BASE.'download/downloadfiles?file_path=strings_'.$selected_language_name.'_customize.xml';
                                            }
                                            if(isset($langcolor_info['android_driver_language_settings']) && $langcolor_info['android_driver_language_settings'] == 2){
                                                $android_driver_lang_display_href_default = 'style="display:none"';
                                                $android_driver_lang_default_check = '';
                                            	$android_driver_lang_custom_check = 'checked="checked"';
                                                $android_driver_lang_display_val = $android_driver_lang_display_href_custom = 'style="display:block"';
                                                if(file_exists($default_target_path.'strings_'.$selected_language_name.'_default.xml')){
                                                    $android_driver_lang_custome_href =  URL_BASE.'download/downloadfiles?file_path=strings_'.$selected_language_name.'.xml';
                                                }
                                            }elseif(isset($postvalue['android_driver_lang_radio']) && $postvalue['android_driver_lang_radio'] == 2){
                                                $android_driver_lang_display_href_default = 'style="display:none"';
                                                $android_driver_lang_default_check = '';
                                            	$android_driver_lang_custom_check = 'checked="checked"';
                                                $android_driver_lang_display_val = $android_driver_lang_display_href_custom = 'style="display:block"';
                                            }
                                            if(file_exists($default_target_path.'strings_'.$selected_language_name.'_default.xml')){
                                                $android_driver_lang_default_href =  URL_BASE.'download/downloadfiles?file_path=strings_'.$selected_language_name.'_default.xml';
                                            }else{
                                                $android_driver_lang_default_href =  URL_BASE.'download/downloadfiles?file_path=strings_'.$selected_language_name.'.xml';
                                            }
                                        ?>
                                        <a id="android_driver_lang_href_default" href="<?php echo $android_driver_lang_default_href.'&file_name=Driver_Android-Default_Language.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_android_driver_app_lang',array(':param' => 'default')); ?>" <?php echo $android_driver_lang_display_href_default; ?> >&nbsp;</a>
                                        <?php if($android_driver_lang_custome_href!=""){ ?>
                                            <a id="android_driver_lang_href_custom" href="<?php echo $android_driver_lang_custome_href.'&file_name=Driver_Android-Customize_Language.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_android_driver_app_lang',array(':param' => 'customize')); ?>" <?php echo $android_driver_lang_display_href_custom; ?> >&nbsp;</a>
                                        <?php } ?>
                                        <div class="choose_lang_opt">
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="1" name="android_driver_lang_radio" class="class_radio" <?php echo $android_driver_lang_default_check; ?> id="android_driver_language_default" onclick="display_button('1','android_driver_language_file','android_driver_lang_href_default','android_driver_lang_href_custom')"/>
                                                <label for="default"><?php echo __('default'); ?></label>
                                            </div>
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="2" name="android_driver_lang_radio" class="class_radio" <?php echo $android_driver_lang_custom_check; ?> id="android_driver_language_customize" onclick="display_button('2','android_driver_language_file','android_driver_lang_href_default','android_driver_lang_href_custom')"/>
                                                <label for="customize"><?php echo __('customize'); ?></label>
                                            </div>
                                        </div>
                                        <div class="form_group inputfile" id="android_driver_language_file" <?php echo $android_driver_lang_display_val; ?> >
                                            <input type="file" name="android_driver_language_file" value=""/>
                                            <span class="textclass fl clr"><?php echo __('file_format_info',array(':param' => '.xml')); ?></span>
                                            <?php if(isset($errors) && array_key_exists('android_driver_language_file',$errors)){ echo "<span class='error'>".ucfirst($errors['android_driver_language_file'])."</span>";}?>
                                        </div>
                                        <div class="form_group" style="margin: 0;">
                                            <input class="common_butt" type="button" name="submit_android_driver_language" value="<?php echo __('button_update'); ?>" onclick="formsubmit('android_driver_language')"/>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="account_det_list">
                            <div class="account_lft_det">
                                <div class="acc_tit">
                                    <h2><?php echo __('passenger_app_lang'); ?></h2>
                                </div>
                                <div class="acc_det">
                                    <p><?php echo __('passenger_app_lang_info'); ?></p>
                                </div>
                            </div>
                            <div class="account_rgt_det">
                                <div class="rgt_lay">
                                    <form name="android_passenger_language" class="form" id="android_passenger_language" action="android_language_colorcode" method="post" enctype="multipart/form-data">
                                        <?php
                                            echo $dynamin_lang_input;
                                            $default_target_path = DOCROOT.ANDROID_DEFAULT_CUSTOMIZE_FILES.'passenger/';
                                            if(file_exists($default_target_path.'strings_'.$selected_language_name.'_customize.xml')){
                                                $android_passenger_lang_custome_href =  URL_BASE.'download/downloadfiles?file_path=strings_'.$selected_language_name.'_customize.xml';
                                            }
                                            if(isset($langcolor_info['android_passenger_language_settings']) && $langcolor_info['android_passenger_language_settings'] == 2){
                                                $android_passenger_lang_display_href_default = 'style="display:none"';
                                                $android_passenger_lang_default_check = '';
                                            	$android_passenger_lang_custom_check = 'checked="checked"';
                                                $android_passenger_lang_display_val = $android_passenger_lang_display_href_custom = 'style="display:block"';
                                                if(file_exists($default_target_path.'strings_'.$selected_language_name.'_default.xml')){
                                                    $android_passenger_lang_custome_href =  URL_BASE.'download/downloadfiles?file_path=strings_'.$selected_language_name.'.xml';
                                                }
                                            }elseif(isset($postvalue['android_passenger_lang_radio']) && $postvalue['android_passenger_lang_radio'] == 2){
                                                $android_passenger_lang_display_href_default = 'style="display:none"';
                                                $android_passenger_lang_default_check = '';
                                            	$android_passenger_lang_custom_check = 'checked="checked"';
                                                $android_passenger_lang_display_val = $android_passenger_lang_display_href_custom = 'style="display:block"';
                                            }
                                            if(file_exists($default_target_path.'strings_'.$selected_language_name.'_default.xml')){
                                                $android_passenger_lang_default_href =  URL_BASE.'download/downloadfiles?file_path=strings_'.$selected_language_name.'_default.xml';
                                            }else{
                                                $android_passenger_lang_default_href =  URL_BASE.'download/downloadfiles?file_path=strings_'.$selected_language_name.'.xml';
                                            }
                                        ?>
                                        <a id="android_passenger_lang_href_default" href="<?php echo $android_passenger_lang_default_href.'&file_name=Passenger_Android-Default_Language.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_android_passenger_app_lang',array(':param' => 'default')); ?>" <?php echo $android_passenger_lang_display_href_default; ?> >&nbsp;</a>
                                        <?php if($android_passenger_lang_custome_href!=""){ ?>
                                            <a id="android_passenger_lang_href_custom" href="<?php echo $android_passenger_lang_custome_href.'&file_name=Passenger_Android-Customize_Language.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_android_passenger_app_lang',array(':param' => 'customize')); ?>" <?php echo $android_passenger_lang_display_href_custom; ?> >&nbsp;</a>
                                        <?php } ?>
                                        <div class="choose_lang_opt">
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="1" name="android_passenger_lang_radio" class="class_radio" <?php echo $android_passenger_lang_default_check; ?> id="android_passenger_language_default" onclick="display_button('1','android_passenger_language_file','android_passenger_lang_href_default','android_passenger_lang_href_custom')"/>
                                                <label for="default"><?php echo __('default'); ?></label>
                                            </div>
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="2" name="android_passenger_lang_radio" class="class_radio" <?php echo $android_passenger_lang_custom_check; ?> id="android_passenger_language_customize" onclick="display_button('2','android_passenger_language_file','android_passenger_lang_href_default','android_passenger_lang_href_custom')"/>
                                                <label for="customize"><?php echo __('customize'); ?></label>
                                            </div>
                                        </div>
                                        <div class="form_group inputfile" id="android_passenger_language_file" <?php echo $android_passenger_lang_display_val; ?>>
                                            <input type="file" name="android_passenger_language_file" value=""/>
                                            <span class="textclass fl clr"><?php echo __('file_format_info',array(':param' => '.xml')); ?></span>
                                            <?php if(isset($errors) && array_key_exists('android_passenger_language_file',$errors)){ echo "<span class='error'>".ucfirst($errors['android_passenger_language_file'])."</span>";}?>
                                        </div>
                                        <div class="form_group" style="margin: 0;">
                                            <input class="common_butt" type="button" name="submit_android_passenger_language" value="<?php echo __('button_update'); ?>" onclick="formsubmit('android_passenger_language')"/>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="account_det_list">
                            <div class="account_lft_det">
                                <div class="acc_tit">
                                    <h2><?php echo __('driver_app_color_code'); ?></h2>
                                </div>
                                <div class="acc_det">
                                    <p><?php echo __('driver_app_color_code_info'); ?></p>
                                </div>
                            </div>
                            <div class="account_rgt_det">
                                <div class="rgt_lay">
                                    <form name="android_driver_colorcode" class="form" id="android_driver_colorcode" action="android_language_colorcode" method="post" enctype="multipart/form-data">
                                         <?php
                                            echo $dynamin_lang_input;
                                            $default_target_path = DOCROOT.ANDROID_DEFAULT_CUSTOMIZE_FILES;
                                            if(file_exists($default_target_path.'driverAppColors_customize.xml')){
                                                $android_driver_color_custome_href =  URL_BASE.'download/downloadfiles?file_path=driverAppColors_customize.xml';
                                            }
                                            if(isset($langcolor_info['android_driver_colorcode_settings']) && $langcolor_info['android_driver_colorcode_settings'] == 2){
                                                $android_driver_color_display_href_default = 'style="display:none"';
                                                $android_driver_color_default_check = '';
                                            	$android_driver_color_custom_check = 'checked="checked"';
                                                $android_driver_color_display_val = $android_driver_color_display_href_custom = 'style="display:block"';
                                                if(file_exists($default_target_path.'driverAppColors_default.xml')){
                                                    $android_driver_color_custome_href =  URL_BASE.'download/downloadfiles?file_path=driverAppColors.xml';
                                                }
                                            }elseif(isset($postvalue['android_driver_colorcode_radio']) && $postvalue['android_driver_colorcode_radio'] == 2){
                                                $android_driver_color_display_href_default = 'style="display:none"';
                                                $android_driver_color_default_check = '';
                                            	$android_driver_color_custom_check = 'checked="checked"';
                                                $android_driver_color_display_val = $android_driver_color_display_href_custom = 'style="display:block"';
                                            }
                                            if(file_exists($default_target_path.'driverAppColors_default.xml')){
                                                $android_driver_color_default_href =  URL_BASE.'download/downloadfiles?file_path=driverAppColors_default.xml';
                                            }else{
                                                $android_driver_color_default_href =  URL_BASE.'download/downloadfiles?file_path=driverAppColors.xml';
                                            }
                                        ?>
                                         <a id="android_driver_color_href_default" href="<?php echo $android_driver_color_default_href.'&file_name=Android_Driver_colorcode-Default.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_android_driver_app_colorcode',array(':param' => 'default')); ?>" <?php echo $android_driver_color_display_href_default; ?> >&nbsp;</a>
                                        <?php if($android_driver_color_custome_href!=""){ ?>
                                            <a id="android_driver_color_href_custom" href="<?php echo $android_driver_color_custome_href.'&file_name=Android_Driver_colorcode-Customize.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_android_driver_app_colorcode',array(':param' => 'customize')); ?>" <?php echo $android_driver_color_display_href_custom; ?> >&nbsp;</a>
                                        <?php } ?>
                                        <div class="choose_lang_opt">
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="1" name="android_driver_colorcode_radio" class="class_radio" <?php echo $android_driver_color_default_check; ?> id="android_driver_colorcode_default" onclick="display_button('1','android_driver_colorcode_file','android_driver_color_href_default','android_driver_color_href_custom')"/>
                                                <label for="default"><?php echo __('default'); ?></label>
                                            </div>
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="2" name="android_driver_colorcode_radio" class="class_radio" <?php echo $android_driver_color_custom_check; ?> id="android_driver_colorcode_customize" onclick="display_button('2','android_driver_colorcode_file','android_driver_color_href_default','android_driver_color_href_custom')"/>
                                                <label for="customize"><?php echo __('customize'); ?></label>
                                            </div>
                                        </div>
                                        <div class="form_group inputfile" id="android_driver_colorcode_file" <?php echo $android_driver_color_display_val; ?>>
                                            <input type="file" name="android_driver_colorcode_file" value=""/>
                                            <span class="textclass fl clr"><?php echo __('file_format_info',array(':param' => '.xml')); ?></span>
                                            <?php if(isset($errors) && array_key_exists('android_driver_colorcode_file',$errors)){ echo "<span class='error'>".ucfirst($errors['android_driver_colorcode_file'])."</span>";}?>
                                        </div>
                                        <div class="form_group" style="margin: 0;">
                                            <input class="common_butt" type="button" name="submit_android_driver_colorcode" value="<?php echo __('button_update'); ?>" onclick="formsubmit('android_driver_colorcode')"/>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="account_det_list" style="border: none;">
                            <div class="account_lft_det">
                                <div class="acc_tit">
                                    <h2><?php echo __('passenger_app_color_code'); ?></h2>
                                </div>
                                <div class="acc_det">
                                    <p><?php echo __('passenger_app_color_code_info'); ?></p>
                                </div>
                            </div>
                            <div class="account_rgt_det">
                                <div class="rgt_lay">
                                    <form name="android_passenger_colorcode" class="form" id="android_passenger_colorcode" action="android_language_colorcode" method="post" enctype="multipart/form-data">
                                        <?php
                                            echo $dynamin_lang_input;
                                            $default_target_path = DOCROOT.ANDROID_DEFAULT_CUSTOMIZE_FILES;
                                            if(file_exists($default_target_path.'passengerAppColors_customize.xml')){
                                                $android_passenger_color_custome_href =  URL_BASE.'download/downloadfiles?file_path=passengerAppColors_customize.xml';
                                            }
                                            if(isset($langcolor_info['android_passenger_colorcode_settings']) && $langcolor_info['android_passenger_colorcode_settings'] == 2){
                                                $android_passenger_color_display_href_default = 'style="display:none"';
                                                $android_passenger_color_default_check = '';
                                            	$android_passenger_color_custom_check = 'checked="checked"';
                                                $android_passenger_color_display_val = $android_passenger_color_display_href_custom = 'style="display:block"';
                                                if(file_exists($default_target_path.'passengerAppColors_default.xml')){
                                                    $android_passenger_color_custome_href =  URL_BASE.'download/downloadfiles?file_path=passengerAppColors.xml';
                                                }
                                            }elseif(isset($postvalue['android_passenger_colorcode_radio']) && $postvalue['android_passenger_colorcode_radio'] == 2){
                                                $android_passenger_color_display_href_default = 'style="display:none"';
                                                $android_passenger_color_default_check = '';
                                            	$android_passenger_color_custom_check = 'checked="checked"';
                                                $android_passenger_color_display_val = $android_passenger_color_display_href_custom = 'style="display:block"';
                                            }
                                            if(file_exists($default_target_path.'passengerAppColors_default.xml')){
                                                $android_passenger_color_default_href =  URL_BASE.'download/downloadfiles?file_path=passengerAppColors_default.xml';
                                            }else{
                                                $android_passenger_color_default_href =  URL_BASE.'download/downloadfiles?file_path=passengerAppColors.xml';
                                            }
                                        ?>
                                         <a id="android_passenger_color_href_default" href="<?php echo $android_passenger_color_default_href.'&file_name=Android_Passenger_colorcode-Default.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_android_passenger_app_colorcode',array(':param' => 'default')); ?>" <?php echo $android_passenger_color_display_href_default; ?> >&nbsp;</a>
                                        <?php if($android_passenger_color_custome_href!=""){ ?>
                                            <a id="android_passenger_color_href_custom" href="<?php echo $android_passenger_color_custome_href.'&file_name=Android_Passenger_colorcode-Customize.xml&mime_type=application/atom+xml'; ?>" class="download_lang_custom" title="<?php echo __('download_android_passenger_app_colorcode',array(':param' => 'customize')); ?>" <?php echo $android_passenger_color_display_href_custom; ?> >&nbsp;</a>
                                        <?php } ?>
                                        <div class="choose_lang_opt">
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="1" name="android_passenger_colorcode_radio" class="class_radio" <?php echo $android_passenger_color_default_check; ?> id="android_passenger_colorcode_default" onclick="display_button('1','android_passenger_colorcode_file','android_passenger_color_href_default','android_passenger_color_href_custom')"/>
                                                <label for="default"><?php echo __('default'); ?></label>
                                            </div>
                                            <div class="radio_primary lang_sett">
                                                <input type="radio" value="2" name="android_passenger_colorcode_radio" class="class_radio" <?php echo $android_passenger_color_custom_check; ?> id="android_passenger_colorcode_customize" onclick="display_button('2','android_passenger_colorcode_file','android_passenger_color_href_default','android_passenger_color_href_custom')"/>
                                                <label for="customize"><?php echo __('customize'); ?></label>
                                            </div>
                                        </div>
                                        <div class="form_group inputfile" id="android_passenger_colorcode_file" <?php echo $android_passenger_color_display_val; ?> >
                                            <input type="file" name="android_passenger_colorcode_file" value=""/>
                                            <span class="textclass fl clr"><?php echo __('file_format_info',array(':param' => '.xml')); ?></span>
                                            <?php if(isset($errors) && array_key_exists('android_passenger_colorcode_file',$errors)){ echo "<span class='error'>".ucfirst($errors['android_passenger_colorcode_file'])."</span>";}?>
                                        </div>
                                        <div class="form_group" style="margin: 0;">
                                            <input class="common_butt" type="button" name="submit_android_passenger_colorcode" value="<?php echo __('button_update'); ?>" onclick="formsubmit('android_passenger_colorcode')"/>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function display_button(param,fileid,hrefid_default,hrefid_custom){
    	if(param==1){
            $("#"+fileid+", #"+hrefid_custom).css('display','none');
            $("#"+hrefid_default).css('display','block');
    	}else{
            $("#"+fileid+", #"+hrefid_custom).css('display','block');
            $("#"+hrefid_default).css('display','none');
    	}
    }
    
    function formsubmit(formid){
        var selecte_val = $('input[type=radio]:checked', '#'+formid).val();
        if(selecte_val==1){
            if(confirm("Are you sure you want to set default language?"))
                document.getElementById(formid).submit();
            else
                return false;
        }else if(selecte_val==2){			
            // get inputs by tags name from form
            var form_elem = document.forms[formid].getElementsByTagName("input");
            for(var i = 0; i < form_elem.length; i++) {
                var node = form_elem[i];
                if(node.getAttribute('type') == 'file' && ($(node).val() == '')){
                    alert('Please choose file to upload');
                    return false;
                }
            }					
            if(confirm("Are you sure you are ready to upload the file?"))
                document.getElementById(formid).submit();
            else
                return false;
        }else{
            alert('invalid Request');
        }        
    }
    
    $(document).ready(function(){
        var selected_language = $("#selected_language option:selected").val();
        //alert(selected_language);
    });    
    (function () {
        var previous;
        var inputradio_id = [], inputfile_id = [];//, atagids=[];
        $("#selected_language").change(function() { 
            previous_value=$('#old_value_set').val();
            previous_text=$('#old_value_set_name').val();
            langkey = this.value;
            if(langkey !="" && langkey!="undefine"){
                var langvalue = $('#selected_language option:selected').text();
                $('#old_value_set').val(this.value);
                $('#old_value_set_name').val(langvalue);
                append_input(langkey,'none');
                //console.log($('#preferenceID a').attr('id'));
                //$("input[type='radio']").each(function() { inputradio_id.push($(this).attr('id')); });
                //$(".inputfile").each(function() { inputfile_id.push($(this).attr('id')); });
                $(".download_lang_custom").each(function() {
                    var link = this.href;
                    var text1 = new RegExp(previous_value+'_', 'g');
                    var text2 = new RegExp('-'+previous_value, 'g');
                    var text3 = new RegExp('_'+previous_text, 'g');
                    var href_link = link.replace(text1,langkey+'_').replace(text2,'-'+langkey).replace(text3,'_'+langvalue);
                    $(this).attr('href',href_link);
                    //atagids.push($(this).attr('id'));
                });
                /*$.ajax({
                    url: '<?php echo URL_BASE."package/ajax_preference_settings"; ?>',
                    cache: false,
                    type: "POST",
                    data: {languageID : langkey, inputradio_id : inputradio_id, inputfile_id : inputfile_id},
                }).success(function(result) {
                    console.log(result);
                });*/
            }else{
                append_input('','block');
            }
        });
    })();
    
    function append_input(value,style_display){
        var input = $("<input>").attr("type", "hidden").attr("class", "dynamic_lang").attr("name", "dynamic_lang").val(value);
        $('.dynamic_lang').remove();
        $('.form').append(input);
        $('.language_error').css('display',style_display);
    }
</script>
