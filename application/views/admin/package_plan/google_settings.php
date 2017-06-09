<?php defined('SYSPATH') OR die("No direct access allowed.");?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div class="account_outer">
    <form name="frm_google_settings" id="frm_google_settings" method="post" action="">
    <!-- Google settings -->
    <div class="account_det_list" style="border: none;">
        <div class="account_lft_det">
            <div class="acc_tit"><h2><?php echo __('google_settings');?></h2></div>
            <div class="acc_det">
                <p><?php echo __('google_settings_desc');?></p>
            </div>
        </div>
        <div class="account_rgt_det">
            <div class="rgt_lay add_crd_det">
                <div class="form_group">
                    <label><?php echo __('ios_google_map_key');?></label>
                    <input class="form_control" type="text" class="required" value="<?php if(isset($postvalue) && array_key_exists('ios_google_map_key',$postvalue)){ echo $postvalue['ios_google_map_key']; }else{echo $google_settings[0]['ios_google_map_key'];}?>" name="ios_google_map_key" id="ios_google_map_key" maxlength="100"/>
                    <?php if(isset($errors) && array_key_exists('ios_google_map_key',$errors)){ echo "<span class='error'>".ucfirst($errors['ios_google_map_key'])."</span>";}?>
                </div>
                <div class="form_group">
                    <label><?php echo __('ios_google_geo_key');?></label>
                    <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('ios_google_geo_key',$postvalue)){ echo $postvalue['ios_google_geo_key']; }else{echo $google_settings[0]['ios_google_geo_key'];}?>" name="ios_google_geo_key" id="ios_google_geo_key" maxlength="100"/>
                    <?php if(isset($errors) && array_key_exists('ios_google_geo_key',$errors)){ echo "<span class='error'>".ucfirst($errors['ios_google_geo_key'])."</span>";}?>
                </div>
                <div class="form_group">
                    <label><?php echo __('web_google_map_key');?></label>
                    <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('web_google_map_key',$postvalue)){ echo $postvalue['web_google_map_key']; }else{echo $google_settings[0]['web_google_map_key'];}?>" name="web_google_map_key" id="web_google_map_key" maxlength="100"/>
                    <?php if(isset($errors) && array_key_exists('web_google_map_key',$errors)){ echo "<span class='error'>".ucfirst($errors['web_google_map_key'])."</span>";}?>
                </div>
                <div class="form_group">
                    <label><?php echo __('enter_google_timezone_api_key');?></label>
                    <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('google_timezone_api_key',$postvalue)){ echo $postvalue['google_timezone_api_key']; }else{echo $google_settings[0]['google_timezone_api_key'];}?>" name="google_timezone_api_key" id="google_timezone_api_key" maxlength="100"/>
                    <?php if(isset($errors) && array_key_exists('google_timezone_api_key',$errors)){ echo "<span class='error'>".ucfirst($errors['google_timezone_api_key'])."</span>";}?>
                </div>
                <div class="form_group">
                    <label><?php echo __('web_google_geo_key');?></label>
                    <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('web_google_geo_key',$postvalue)){ echo $postvalue['web_google_geo_key']; }else{echo $google_settings[0]['web_google_geo_key'];}?>" name="web_google_geo_key" id="web_google_geo_key" maxlength="100"/>
                    <?php if(isset($errors) && array_key_exists('web_google_geo_key',$errors)){ echo "<span class='error'>".ucfirst($errors['web_google_geo_key'])."</span>";}?>
                </div>
                <div class="form_group">
                    <label><?php echo __('android_google_key');?></label>
                    <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('android_google_api_key',$postvalue)){ echo $postvalue['android_google_api_key']; }else{ echo $google_settings[0]['android_google_key'];}?>" name="android_google_api_key" id="android_google_api_key" maxlength="100"/>
                    <?php if(isset($errors) && array_key_exists('android_google_api_key',$errors)){ echo "<span class='error'>".ucfirst($errors['android_google_api_key'])."</span>";}?>
                </div>
            </div>
        </div>        
    </div>
    <!-- Four square settings -->
    <div class="account_det_list" style="border: none;">
        <div class="account_lft_det">
            <div class="acc_tit"><h2><?php echo __('foursquare_settings');?></h2></div>
            <div class="acc_det">
                <p><?php echo __('foursquare_settings_desc').'<br><br><b>'.__('note').': </b>'.__('foursquare_info');?></p>
            </div>
        </div>
		<div class="account_rgt_det">
            <div class="rgt_lay add_crd_det">
				<?php /*
                <div class="form_group enb_sett">
                    <label><?php echo __('web_foursquare_api_key');?></label>
                    <input class="form_control" type="text" class="required" value="<?php if(isset($postvalue) && array_key_exists('web_foursquare_api_key',$postvalue)){ echo $postvalue['web_foursquare_api_key']; }else{echo $google_settings[0]['web_foursquare_api_key'];}?>" name="web_foursquare_api_key" id="web_foursquare_api_key" maxlength="100"/>
                    <?php if(isset($errors) && array_key_exists('web_foursquare_api_key',$errors)){ echo "<span class='error'>".ucfirst($errors['web_foursquare_api_key'])."</span>";}?>
                    <div class="checkbox_custom"><input type="checkbox" name="web_foursquare_status" <?php echo (isset($google_settings[0]['web_foursquare_status']) && ($google_settings[0]['web_foursquare_status'] == 1)) ? 'checked' :''; ?> value="1">
						<label for="termsnew"><?php echo __('enable');?></label>
					</div>
                </div>
				*/ ?>
				<input type="hidden" value="webkey" name="web_foursquare_api_key">
                <div class="form_group enb_sett">
                    <label><?php echo __('android_foursquare_api_key');?></label>
                    <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('android_foursquare_api_key',$postvalue)){ echo $postvalue['android_foursquare_api_key']; }else{echo $google_settings[0]['android_foursquare_api_key'];}?>" name="android_foursquare_api_key" id="ios_google_geo_key" maxlength="100"/>
                    <?php if(isset($errors) && array_key_exists('android_foursquare_api_key',$errors)){ echo "<span class='error'>".ucfirst($errors['android_foursquare_api_key'])."</span>";}?>
                    <div class="checkbox_custom"><input type="checkbox" name="android_foursquare_status" <?php echo (isset($google_settings[0]['android_foursquare_status']) && ($google_settings[0]['android_foursquare_status'] == 1)) ? 'checked' :''; ?> value="1">
					<label><?php echo __('enable');?></label>
					</div> 
                </div>
                               
                <div class="form_group enb_sett">
                    <label><?php echo __('ios_foursquare_api_key');?></label>
                    <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('ios_foursquare_api_key',$postvalue)){ echo $postvalue['ios_foursquare_api_key']; }else{echo $google_settings[0]['ios_foursquare_api_key'];}?>" name="ios_foursquare_api_key" id="ios_google_geo_key" maxlength="100"/>
                    <?php if(isset($errors) && array_key_exists('ios_foursquare_api_key',$errors)){ echo "<span class='error'>".ucfirst($errors['ios_foursquare_api_key'])."</span>";}?>
                    <div class="checkbox_custom"><input type="checkbox" name="ios_foursquare_status" <?php echo (isset($google_settings[0]['ios_foursquare_status']) && ($google_settings[0]['ios_foursquare_status'] == 1)) ? 'checked' :''; ?> value="1">
					<label><?php echo __('enable');?></label>
					</div> 
                </div>  
                          
            </div>
        </div>
    </div>
    
    <div class="bottom_butt_sec">
        <div class="align_right">
<!--            <input class="btn_primary" type="reset" value="Cancel"/>-->
            <input class="common_butt" type="submit" value="<?php echo __('button_update'); ?>" name="btn_google" id="btn_google"/>
        </div>
    </div>
</form>
</div>
<script>
$(document).ready(function () {	
	$("#frm_google_settings").validate({
		rules: {			   
			ios_google_map_key: "required",
			ios_google_geo_key: "required",
			web_google_map_key: "required",
			google_timezone_api_key: "required",
			web_google_geo_key: "required",
			android_google_api_key: "required",
		},
		messages: {
			ios_google_map_key: "<?php echo __('plz_enter_ios_google_mapkey'); ?>",
			ios_google_geo_key: "<?php echo __('plz_enter_ios_google_geokey'); ?>",
			web_google_map_key: "<?php echo __('plz_enter_web_google_mapkey'); ?>",
			google_timezone_api_key: "<?php echo __('plz_enter_google_timezone_apikey'); ?>",
			web_google_geo_key: "<?php echo __('plz_enter_web_google_geokey'); ?>",
			android_google_api_key: "<?php echo __('plz_enter_android_google_apikey'); ?>",
		}
	});		
});
</script>
