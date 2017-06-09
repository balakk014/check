<?php defined('SYSPATH') OR die("No direct access allowed.");
	echo html::script('public/common/ckeditor/ckeditor.js');
?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle site_settingss">

            <form method="POST" enctype="multipart/form-data" class="form" action="" name="settings" id="settings" >
                <table class="0" cellpadding="5" cellspacing="0" width="100%">                    
				<tr>
					<td valign="top" width="20%"><label><?php echo __('site_logo_label'); ?></label><span class="star">*</span></td>   
					<td>
					<div class="new_input_field">
					<input type="file" name="site_logo" id="site_logo" class="imageonly" title="<?php echo __('select_taxi_image'); ?>" value="">
					
				</div>
				<div class="site_logo">
				<img src="<?php echo URL_BASE.SITE_LOGO_IMGPATH.'logo.png';?>" width="160">
				</div>
				<small class="sub_note"><?php echo __('logo_desc'); ?></small>
				<?php if(isset($errors) && array_key_exists('site_logo',$errors)){ echo "<span class='error'>".ucfirst($errors['site_logo'])."</span>";}?>
				
			</td>
             </tr>
             <tr>
                <td valign="top" width="20%"><label><?php echo __('site_email_logo'); ?></label><span class="star">*</span></td>   
                <td>
					<div class="new_input_field">
						<input type="file" name="email_site_logo" id="email_site_logo" class="imageonly" title="<?php echo __('select_taxi_image'); ?>" value="<?php /*echo isset($site_settings) &&!array_key_exists('email_site_logo',$postvalue)? trim($site_settings[0]['email_site_logo']):$postvalue['email_site_logo']; */?>">
					</div>
					<?php 
						$email_logo = $_SERVER['DOCUMENT_ROOT'] . '/' . SITE_LOGO_IMGPATH . 'site_email_logo.png';
						
						if (file_exists($email_logo)) {
					?>
					<div class="email_site_logo" style="width:160px;">
						<img src="<?php echo URL_BASE.'public/'.UPLOADS.'/site_logo/site_email_logo.png';?>" width="160">
					</div>
					<?php } ?>
				<small class="sub_note"><?php echo __('email_logo_desc'); ?></small>
				<?php if(isset($errors) && array_key_exists('email_site_logo',$errors)){ echo "<span class='error'>".ucfirst($errors['email_site_logo'])."</span>";}?>
				
			</td>
                    </tr>
                    
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('site_favicon_label'); ?></label><span class="star">*</span></td>   
                        <td>
				<div class="new_input_field">
					<input type="file" name="site_favicon" id="site_favicon" class="imageonly" title="<?php echo __('select_taxi_image'); ?>" value="<?php /*echo isset($site_settings) &&!array_key_exists('site_favicon',$postvalue)? trim($site_settings[0]['site_favicon']):$postvalue['site_favicon'];*/ ?>">
					
				</div>
				<div class="site_logo" style="width:220px;"> <input type="hidden" name="favicon_old" id="favicon_old" value="<?php echo $site_settings[0]['site_favicon']; ?>" />
				<?php 
					$img_favicon = URL_BASE.SITE_FAVICON_IMGPATH.$site_settings[0]['site_favicon'];
					$img_favicon_docroot = DOCROOT.SITE_FAVICON_IMGPATH.$site_settings[0]['site_favicon'];
					if (file_exists($img_favicon_docroot)) { ?>
					<img src="<?php echo $img_favicon; ?>" >
				<?php } ?>
				</div>
				<small class="sub_note"><?php echo __('fav_desc'); ?></small>
				<?php if(isset($errors) && array_key_exists('site_favicon',$errors)){ echo "<span class='error'>".ucfirst($errors['site_favicon'])."</span>";}?>
			</td>
                    </tr>
                    <tr>
                        <td valign="top" width="20%"><label><?php echo __('site_copyrights_label'); ?></label><span class="star">*</span></td>   
                        <td>
						<div class="new_input_field">
							<input type="text" name="site_copyrights" id="site_copyrights" title="<?php echo __('enter_site_copyrights'); ?>" maxlength="100" value="<?php echo isset($site_settings) &&!array_key_exists('site_copyrights',$postvalue)? trim($site_settings[0]['site_copyrights']):$postvalue['site_copyrights']; ?>">
						</div>
						<?php if(isset($errors) && array_key_exists('site_copyrights',$errors)){ echo "<span class='error'>".ucfirst($errors['site_copyrights'])."</span>";}?>
						</td>
				</tr>
              
              <tr>
				<td valign="top" width="20%"><label><?php echo __('flash_screen_logo_label'); ?></label><span class="star">*</span></td>   
				<td>
					<?php 
						$mobile_header_logo_req="";
						if(isset($site_settings[0]['mobile_header_logo'])){
							$mobile_header_logo = $mobile_header_logo_req = $site_settings[0]['mobile_header_logo'];
						}else{
							if(isset($postvalue['mobile_header_logo'])){
								$mobile_header_logo = $postvalue['mobile_header_logo'];
							}else{
								$mobile_header_logo = "";
							}
						}
					?>   
					<div class="new_input_field">
						<input type="file" name="mobile_header_logo" class="mobile_header_logo_validate imageonly" title="<?php echo __('mobile_header_logo_label'); ?>" value="<?php //	echo $mobile_header_logo; ?>">
					</div>
					<?php if($mobile_header_logo_req != '') { ?>
					<div class="site_logo" style="width:220px;">
						<img src="<?php echo URL_BASE.MOBILE_LOGO_PATH.$mobile_header_logo_req;?>">
					</div>
					<?php } ?>
					<input type="hidden" name="mobile_header_logo" id="mobile_header_logo" value="<?php echo $mobile_header_logo_req; ?>" />
					<small class="sub_note"><?php echo __('mobile_header_logo_desc'); ?></small>
					<span id="mobile_header_logo_error"><?php if(isset($errors) && array_key_exists('mobile_header_logo',$errors)){ echo "<span class='error'>".ucfirst($errors['mobile_header_logo'])."</span>"; } ?></span>
				</td>
            </tr>
			
			
			<tr>
				<td valign="top" width="20%"><label><?php echo __('mobile_header_logo_label'); ?></label><span class="star">*</span></td>   
				<td>
					<?php 
						$flash_screen_logo_req="";
						if(isset($site_settings[0]['flash_screen_logo'])){
							$flash_screen_logo = $flash_screen_logo_req = $site_settings[0]['flash_screen_logo'];
						}else{
							if(isset($postvalue['flash_screen_logo'])){
								$flash_screen_logo = $postvalue['flash_screen_logo'];
							}else{
								$flash_screen_logo = "";
							}
						}
					?>   
					<div class="new_input_field">
						<input type="file" name="flash_screen_logo" class="flash_screen_logo_validate" title="<?php echo __('flash_screen_logo_label'); ?>" value="<?php //echo $flash_screen_logo; ?>">
					</div>
					<div class="site_logo" style="width:220px;"> <input type="hidden" name="flash_screen_logo" id="flash_screen_logo" value="<?php echo $flash_screen_logo_req; ?>" />
						<img src="<?php echo URL_BASE.MOBILE_LOGO_PATH.$flash_screen_logo_req;?>">
					</div>
					<small class="sub_note"><?php echo __('flash_screen_logo_desc'); ?></small>
					<span id="flash_screen_logo_error"><?php if(isset($errors) && array_key_exists('flash_screen_logo',$errors)){ echo "<span class='error'>".ucfirst($errors['flash_screen_logo'])."</span>";}?></span>
				</td>
			</tr>
			
			<tr>
				<td valign="top" width="20%"><label><?php echo __('select_theme'); ?></label></td>
				<td>
					<div class="new_input_field1">
					<div class="theme_list">
					<span><?php echo __('default'); ?> theme</span>
						<input type="radio" class="theme" name="theme_id" value="1" <?php if($site_settings[0]['theme_id'] == 1){ echo 'checked'; } ?>>
						
						<img src="<?php echo URL_BASE.'public/admin/images/default_theme.png';?>" width="100">
						</div>
						
						<div class="theme_list">
						<span><?php echo __('new'); ?> theme</span>
						<input type="radio" class="theme" name="theme_id" value="2" <?php if($site_settings[0]['theme_id'] == 2){ echo 'checked'; } ?>>
						<img src="<?php echo URL_BASE.'public/admin/images/new_theme.png';?>" width="100">
						</div>
					</div>
			   </td>
			</tr>
			
			<!-- Theme 1 contents -->
            <tbody id="theme1" <?php if($site_settings[0]['theme_id'] == 2 ){ ?> style="display:none" <?php } ?>>
				<tr>
					<td valign="top" width="20%"><label><?php echo __('front_banner_image'); ?></label></td>   
					<td>
						<div class="new_input_field">
							<input type="file" name="banner_image" id="banner_image" class="imageonly" title="<?php echo __('select_taxi_image'); ?>" value="<?php //echo isset($site_settings) &&!array_key_exists('banner_image',$postvalue)? trim($site_settings[0]['banner_image']):$postvalue['banner_image']; ?>">
						</div>
						<?php 
							$banner_logo = $_SERVER['DOCUMENT_ROOT'] . '/' . PUBLIC_UPLOADS_LANDING_FOLDER . $site_settings[0]['banner_image'];
							if (($site_settings[0]['banner_image'] != '') && file_exists($banner_logo)) {
						?>
						<div class="site_logo" style="width:220px;">
							<img src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER.$site_settings[0]['banner_image'];?>">
						</div>
						<?php } ?>
						<input type="hidden" name="banner_img_old" id="banner_img_old" value="<?php echo $site_settings[0]['banner_image']; ?>" />
						<small class="sub_note"><?php echo __('front_banner_image_info'); ?></small>
						<?php if(isset($errors) && array_key_exists('banner_image',$errors)){ echo "<span class='error'>".ucfirst($errors['banner_image'])."</span>"; } ?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="20%"><label><?php echo __('app_background_colour'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="app_bg_color" id="app_bg_color" class="jscolor" title="<?php echo __('enter_app_bg_color'); ?>" value="<?php echo isset($site_settings) && (!array_key_exists('app_bg_color', $errors)) ? $site_settings[0]['app_bg_color'] : $validator['app_bg_color']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('app_bg_color',$errors)){ echo "<span class='error'>".ucfirst($errors['app_bg_color'])."</span>"; } ?></td>
				</tr>				
				<tr>
					<td valign="top" width="20%"><label><?php echo __('about_us_background_colour'); ?></label><span class="star">*</span></td>
					<td><div class="new_input_field"><input type="text" name="about_bg_color" id="about_bg_color" class="jscolor" title="<?php echo __('enter_about_bg_color'); ?>" value="<?php echo isset($site_settings) && (!array_key_exists('about_bg_color', $errors)) ? $site_settings[0]['about_bg_color'] : $validator['about_bg_color']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('about_bg_color',$errors)){ echo "<span class='error'>".ucfirst($errors['about_bg_color'])."</span>"; } ?></td>
				</tr>				
				<tr>
					<td valign="top" width="20%"><label><?php echo __('footer_background_colour'); ?></label><span class="star">*</span></td>
					<td><div class="new_input_field"><input type="text" name="footer_bg_color" id="footer_bg_color" class="jscolor" title="<?php echo __('enter_footer_bg_color'); ?>" value="<?php echo isset($site_settings) && (!array_key_exists('footer_bg_color', $errors)) ? $site_settings[0]['footer_bg_color'] : $validator['footer_bg_color']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('footer_bg_color',$errors)){ echo "<span class='error'>".ucfirst($errors['footer_bg_color'])."</span>"; } ?></td>
				</tr>
			</tbody>
			<!-- Theme 1 contents end-->
			<!-- Theme 2 contents -->
			<tbody id="theme2" <?php if($site_settings[0]['theme_id'] == 1){ ?> style="display:none" <?php } ?>>
				<tr>
					<td valign="top" width="20%"><label><?php echo __('front_banner_image'); ?></label></td>   
					<td>
						<div class="new_input_field">
							<input type="file" name="banner_image_1" id="banner_image_1" class="imageonly" title="<?php echo __('select_taxi_image'); ?>" value="">
						</div>
						<?php 
							$banner_logo = $_SERVER['DOCUMENT_ROOT'] . '/' . PUBLIC_UPLOADS_LANDING_FOLDER . $site_settings[0]['banner_image_1'];
							if (($site_settings[0]['banner_image_1'] != '') && file_exists($banner_logo)) {
						?>
						<div class="site_logo" style="width:220px;">
							<img src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER.$site_settings[0]['banner_image_1'];?>">
						</div>
						<?php } ?>
						<input type="hidden" name="banner_img_1_old" id="banner_img_1_old" value="<?php echo $site_settings[0]['banner_image_1']; ?>" />
						<small class="sub_note"><?php echo __('front_banner_image_info'); ?></small>
						<?php if(isset($errors) && array_key_exists('banner_image_1',$errors)){ echo "<span class='error'>".ucfirst($errors['banner_image_1'])."</span>"; } ?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="20%"><label><?php echo __('frontend_image'); ?></label></td>   
					<td>
						<div class="new_input_field">
							<input type="file" name="frontend_mobile" id="frontend_mobile" class="imageonly" title="<?php echo __('select_taxi_image'); ?>" value="">
						</div>
						<?php 
							$frontend_mobile = $_SERVER['DOCUMENT_ROOT'] . '/' . PUBLIC_UPLOADS_LANDING_FOLDER . $site_settings[0]['frontend_mobile'];
							if (($site_settings[0]['frontend_mobile'] != '') && file_exists($frontend_mobile)) { ?>
							<div class="site_logo" style="width:220px;">
								<img src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER.$site_settings[0]['frontend_mobile'];?>">
							</div>
						<?php } ?>
						<input type="hidden" name="frontend_mobile_old" id="frontend_mobile_old" value="<?php echo $site_settings[0]['frontend_mobile']; ?>" />
						<small class="sub_note"><?php echo __('front_mobile_image_info'); ?></small>
						<?php if(isset($errors) && array_key_exists('frontend_mobile',$errors)){ echo "<span class='error'>".ucfirst($errors['frontend_mobile'])."</span>"; } ?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="20%"><label><?php echo __('frontend_carimage'); ?></label></td>   
					<td>
						<div class="new_input_field">
							<input type="file" name="frontend_car" id="frontend_car" class="imageonly" title="<?php echo __('select_taxi_image'); ?>" value="">
						</div>
						<?php 
							$frontend_car = $_SERVER['DOCUMENT_ROOT'] . '/' . PUBLIC_UPLOADS_LANDING_FOLDER . $site_settings[0]['frontend_car'];
							if (($site_settings[0]['frontend_car'] != '') && file_exists($frontend_car)) {
								 ?>
							<div class="site_logo" style="width:220px;">
								<img src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER.$site_settings[0]['frontend_car'];?>">
							</div>
						<?php } ?>
						<input type="hidden" name="frontend_car_old" id="frontend_car_old" value="<?php echo $site_settings[0]['frontend_car']; ?>" />
						<small class="sub_note"><?php echo __('front_car_image_info'); ?></small>
						<?php if(isset($errors) && array_key_exists('frontend_car',$errors)){ echo "<span class='error'>".ucfirst($errors['frontend_car'])."</span>"; } ?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="20%"><label><?php echo __('app_background_colour'); ?></label><span class="star">*</span></td>   
					<td><div class="new_input_field"><input type="text" name="app_bg_color_1" id="app_bg_color_1" class="jscolor" title="<?php echo __('enter_app_bg_color'); ?>" value="<?php echo isset($site_settings[0]['app_bg_color_1']) && (!array_key_exists('app_bg_color_1', $errors)) ? $site_settings[0]['app_bg_color_1'] : $validator['app_bg_color_1']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('app_bg_color',$errors)){ echo "<span class='error'>".ucfirst($errors['app_bg_color'])."</span>"; } ?></td>
				</tr>
				
				<tr>
					<td valign="top" width="20%"><label><?php echo __('footer_background_colour'); ?></label><span class="star">*</span></td>
					<td><div class="new_input_field"><input type="text" name="footer_bg_color_1" id="footer_bg_color_1" class="jscolor" title="<?php echo __('enter_footer_bg_color'); ?>" value="<?php echo isset($site_settings[0]['footer_bg_color_1']) && (!array_key_exists('footer_bg_color', $errors)) ? $site_settings[0]['footer_bg_color_1'] : $validator['footer_bg_color']; ?>"></div>
					<?php if(isset($errors) && array_key_exists('footer_bg_color',$errors)){ echo "<span class='error'>".ucfirst($errors['footer_bg_color'])."</span>"; } ?></td>
				</tr>
			</tbody>			
			<!-- Theme 2 contents end-->
			<tr>
					<td valign="top" width="20%"><label><?php echo __('front_banner_content'); ?></label></td>
					<td>
						<div class="new_input_field1">
							<textarea name="banner_content" id="banner_content" class="ckeditor" title="<?php echo __('enter_banner_content'); ?>"><?php echo isset($site_settings) && (!array_key_exists('banner_content', $errors)) ? $site_settings[0]['banner_content'] : $validator['banner_content']; ?></textarea>
							<?php if(isset($errors) && array_key_exists('banner_content',$errors)){ echo "<span class='error'>".ucfirst($errors['banner_content'])."</span>";}?>
						</div>
				   </td>
				</tr>				
				<tr>
					<td valign="top" width="20%"><label><?php echo __('front_app_content'); ?></label></td>
					<td>
						<div class="new_input_field1">
							<textarea name="app_content" id="app_content" class="ckeditor" title="<?php echo __('enter_app_content'); ?>"><?php echo isset($site_settings) && (!array_key_exists('app_content', $errors)) ? $site_settings[0]['app_content'] : $validator['app_content']; ?></textarea>
							<?php if(isset($errors) && array_key_exists('app_content',$errors)){ echo "<span class='error'>".ucfirst($errors['app_content'])."</span>";}?>
						</div>
				   </td>
				</tr>				
				<tr>
					<td valign="top" width="20%"><label><?php echo __('front_about_us_content'); ?></label></td>
					<td>
						<div class="new_input_field1">
							<textarea name="about_us_content" id="about_us_content" class="ckeditor" title="<?php echo __('enter_about_us_content'); ?>"><?php echo isset($site_settings) && (!array_key_exists('about_us_content', $errors)) ? $site_settings[0]['about_us_content'] : $validator['about_us_content']; ?></textarea>
							<?php if(isset($errors) && array_key_exists('about_us_content',$errors)){ echo "<span class='error'>".ucfirst($errors['about_us_content'])."</span>";}?>
						</div>
				   </td>
				</tr>
				<tr>
					<td valign="top" width="20%"><label><?php echo __('contact_us_content'); ?></label></td>
					<td>
						<div class="new_input_field1">
							<textarea name="contact_us_content" id="contact_us_content" class="ckeditor" title="<?php echo __('enter_contact_us_content'); ?>"><?php echo isset($site_settings) && (!array_key_exists('contact_us_content', $errors)) ? $site_settings[0]['contact_us_content'] : $validator['contact_us_content']; ?></textarea>
							<?php if(isset($errors) && array_key_exists('contact_us_content',$errors)){ echo "<span class='error'>".ucfirst($errors['contact_us_content'])."</span>";}?>
						</div>
				   </td>
				</tr>
				
				<tr>
				<td class="empt_cel">&nbsp;</td>
							<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
						</tr>						
						<tr>
							<td valign="top">&nbsp;</td>
							<td style="padding-left:0px;">
								 <div class="new_button">  <input type="submit" name="template_submit" <?php if($email==SUPERADMIN_EMAIL) { ?> id="disable" <?php } ?> title ="<?php echo __('button_update'); ?>" value="<?php echo __('button_update'); ?>"></div>
								<div class="new_button"> <input type="reset" name="editsettings_reset" title="<?php echo __('button_reset'); ?>" value="<?php echo __('button_reset'); ?>"></div>
							</td></tr>
				</table>
			</form>
            <br/><br/>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt" ></div></div>
    </div>

</div>
<script src="<?php echo SCRIPTPATH; ?>jscolor.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {

	jQuery("#settings").validate();

	jQuery.validator.addMethod('chk', function(value) {
		return (parseInt(document.getElementById('notification_settings').value)  > 0); }, 'Notification time should greater than zero');

	jQuery.validator.addMethod('onlyseconds', function(value) {
		return (parseInt(document.getElementById('notification_settings').value)  < 60); }, 'Notification time should be less than 60 seconds.');
	
	jQuery.validator.addMethod('greaternotification', function(value) {		
		
		if(parseInt(document.getElementById('continuous_request_time').value)  > (4*parseInt(document.getElementById('notification_settings').value))){
			return (parseInt(document.getElementById('continuous_request_time').value) < (6*parseInt(document.getElementById('notification_settings').value))+1);
		}else{
			return false;
		}
		
	}, 'Continuous Request time should be greater than 4*Notification time & lesser or equal to 6*Notification time.');
	
	$.validator.addMethod( "imageonly", function(value,element){
	var pathLength = value.length; var lastDot = value.lastIndexOf( "."); var fileType = value.substring(lastDot,pathLength).toLowerCase(); return this.optional(element) || fileType.match(/(?:.jpg|.jpeg|.png)$/) }, "Please upload image file only");

	//For Field Focus
	//===============
	var field_val = $("#app_name").val();
	$("#app_name").focus().val("").val(field_val);

	var cityid= $("#site_city").val();

	if(cityid == '')
	{
		//change_city();
	}


     $("#site_country").change(function() {

      		var countryid= $("#site_country").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getcitylist",
			type:"get",
			data:"country_id="+countryid,
			success:function(data){

			$('#city_list').html();
			$('#city_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    });
    
	$( "input.theme").change(function(){
		var themeid = this.value;
		$("#theme"+themeid).show();
		if(themeid == 1)
			$("#theme2").hide();
		else
			$("#theme1").hide();
    });          
});

function change_city()
{

			var countryid= $("#site_country").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getcitylist",
			type:"get",
			data:"country_id="+countryid,
			success:function(data){

			$('#city_list').html();
			$('#city_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function skip_credit_cards(val)
{
	var cancel_fare_setting = $("#cancellation_fare").val();
	if(val == 1 && cancel_fare_setting == 1) {
		alert("<?php echo __('not_enable_skip_card_option'); ?>");
		$("#skip_card_enable").prop('checked', false);
		$("#skip_card_disable").prop('checked', true);
	}
}


function check_cancelation_skip_cards(val)
{
	var skip_credit_card_val = $('input[name="skip_credit_card"]:checked').val();
	if(val == 1 && skip_credit_card_val == 0) {
		alert("<?php echo __('not_enable_skip_card_option'); ?>");
		$("#cancellation_fare").val(0);
	}
}
</script>
