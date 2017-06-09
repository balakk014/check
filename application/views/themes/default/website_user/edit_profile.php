<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div class="dash_details">
	<?php echo View::factory(USERVIEW.'website_user/left_menu'); ?>
	<section id="right_side_part">
		<div class="top_part">
			<div class="bread_com">
				<ul>
					<li><a href="<?php echo URL_BASE; ?>" title="<?php echo __("home_breadcrumb"); ?>"><?php echo __("home_breadcrumb"); ?></a><i class="fa fa-angle-double-right"></i></li>
					<li><p><?php echo __('editprofile_label'); ?></p></li>
				</ul>
			</div>
			<?php echo View::factory(USERVIEW.'website_user/upcoming_trip_alert'); ?>
		</div>
		<div class="white_bg">
			<div class="change_password">
				<h1><?php echo __('editprofile_label'); ?></h1>
				<form class="form-horizontal" name="edit_profile" method="post" action="" enctype="multipart/form-data" autocomplete="off">
					<ul>
						<li>
							<div class="full_name">
								<input type="text" name="name" on-paste="return false;" id="firstname" maxlength="35" placeholder="<?php echo __('firstname_label'); ?>" value="<?php echo isset($user[0]['name']) &&!array_key_exists('name',$data)? trim($user[0]['name']):$data['name'];  ?>" />
								<em><?php echo array_key_exists("name",$errors)?$errors["name"]:"";?></em>
							</div>
						</li>
						<li>
							<div class="full_name">
								<input type="text" name="lastname" on-paste="return false;" id="last_name" maxlength="35" placeholder="<?php echo __('lastname_label'); ?>" value="<?php echo isset($user[0]['lastname']) &&!array_key_exists('lastname',$data)? trim($user[0]['lastname']):$data['lastname'];  ?>" />
								<em><?php echo array_key_exists("lastname",$errors)?$errors["lastname"]:"";?></em>
							</div>
						</li>
						<li>
							<div class="full_name">
								<input type="text" name="email" on-paste="return false;" maxlength="64" placeholder="<?php echo __('emaillabel'); ?>" value="<?php echo isset($user[0]['email']) &&!array_key_exists('email',$data)? trim($user[0]['email']):$data['email'];  ?>" />
								<em><?php echo array_key_exists("email",$errors)?$errors["email"]:"";?></em>
							</div>
						</li>
						<li>
                                                    <div class="mobile_field">
                                                        <div class="country_code">
								<input type="text" class="country_code_restrict" onpaste="return false;" name="country_code" placeholder="+91*" value="<?php echo isset($user[0]['country_code']) &&!array_key_exists('country_code',$data)? trim($user[0]['country_code']):$data['country_code'];  ?>" maxlength="5"/>
							</div>
							<div class="mobile_no">
								<input type="text" onpaste="return false;" id="txtboxToFilter" maxlength="15" name="phone" placeholder="<?php echo __('mobile'); ?>" value="<?php echo isset($user[0]['phone']) &&!array_key_exists('phone',$data)? trim($user[0]['phone']):$data['phone'];  ?>" />
							</div>
                                                        <em><?php echo array_key_exists("country_code",$errors)?$errors["country_code"]:"";?></em>
                                                        <em><?php echo array_key_exists("phone",$errors)?$errors["phone"]:"";?></em>
                                                    </div>
						</li>
						<li>
							<div class="full_name">
								<textarea name="address" maxlength="255" placeholder="<?php echo __('address'); ?>"><?php echo isset($user[0]['address']) && !array_key_exists('address',$data) ? trim($user[0]['address']):$data['address'];  ?></textarea>
								<em><?php echo array_key_exists("address",$errors)?$errors["address"]:"";?></em>
							</div>
						</li>
						<li>
							<div class="full_name">
								<input type="file" name="profile_picture" id="profile_picture" title="<?php echo __('choose_file'); ?>" onchange="return Checkfiles();"/>
								<em id="profile_picture_error">
									<?php if(isset($errors) && array_key_exists('profile_picture',$errors)){ echo "<span class='error'>".__('Errors.photo.Upload::type')."</span>";}?>
								</em>
								<div class="choose_profile_img">
									<?php if(file_exists(DOCROOT.PASS_IMG_IMGPATH.$user[0]['id'].".png")) { ?>
										<img src="<?php echo URL_BASE.PASS_IMG_IMGPATH.$user[0]['id'].".png"; ?>">
									<?php } else { ?>
										<img alt="user image" src="<?php echo URL_BASE;?>public/frontend/logged_in/images/profile_noimage.png"/>
									<?php } ?>
								</div>
							</div>
						</li>
						<li>
							<div class="submit">
								<input type="submit" name="submit_user_profile" value="<?php echo __('button_save'); ?>" title="<?php echo __('button_save'); ?>" />
							</div>
						</li>
					</ul>
				</form>
			</div>
		</div>
	</section>
</div>
<script>
$(document).ready( function() {
	$('#txtboxToFilter').keydown(function (event) {  
		// Allow: backspace, delete, tab, escape, and enter
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
			// Allow: Ctrl+A
		(event.keyCode == 65 && event.ctrlKey === true) || 
			// Allow: home, end, left, right
		(event.keyCode >= 35 && event.keyCode <= 39)) {
			// let it happen, don't do anything
			return;
		} else {
			// Ensure that it is a number and stop the keypress
			if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
				event.preventDefault(); 
			}   
		}
	});
/** Country code allowed +91 **/

	$(".country_code_restrict" ).keyup(function() {
		//to allow left and right arrow key move
		if(event.which>=37 && event.which<=40) {
			return false;
		}
		this.value = this.value.replace(/[`~!@#$%^&*()\s_|\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
	});
	//first name, lastname
	$("#firstname,#last_name").keyup(function(event) {
		//to allow left and right arrow key move and backspace, delete buttons
		if((event.which>=37 && event.which<=40) || event.which==8 || event.which==46)
		{
			return false;
		}
		this.value = this.value.replace(/[`~!0-9@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
	});
});
function Checkfiles()
{
	var fup = document.getElementById('profile_picture');
	var fileName = fup.value;
	var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
	if(ext == "gif" || ext == "GIF" || ext == "JPEG" || ext == "jpeg" || ext == "jpg" || ext == "JPG" || ext == "png" || ext == "PNG" || ext == "bmp" || ext == "BMP") {
		return true;
	} else {
		$("#profile_picture_error").html("<?php echo __('upload_image_error'); ?>");
		fup.focus();
		return false;
	}
}
</script>
