<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div class="dash_details">
	<?php echo View::factory(USERVIEW.'website_user/left_menu'); ?>
	<section id="right_side_part">
		<div class="top_part">
			<div class="bread_com">
				<ul>
					<li><a href="<?php echo URL_BASE; ?>" title="<?php echo __("home_breadcrumb"); ?>"><?php echo __("home_breadcrumb"); ?></a><i class="fa fa-angle-double-right"></i></li>
					<li><p><?php echo __("changepassword_label"); ?></p></li>
				</ul>
			</div>
			<?php echo View::factory(USERVIEW.'website_user/upcoming_trip_alert'); ?>
		</div>
		<div class="white_bg">
			<div class="change_password">
				<h1><?php echo __("changepassword_label"); ?></h1>
				<form method="post" name="change_password">
					<ul>
						<li>
							<div class="full_name">
								<input type="password" name="old_password" onpaste="return false;" placeholder="<?php echo ucwords(__("oldpassword_label")); ?>" value="<?php echo isset($validator['old_password'])?$validator['old_password']:'';?>" maxlength="24"/>
								<label class="error"> <?php echo isset($oldpass_error)?$oldpass_error:'';?><?php echo array_key_exists("old_password",$errors)?$errors["old_password"]:"";?></label>
							</div>
						</li>
						<li>
							<div class="full_name">
								<input type="password" onpaste="return false;" placeholder="<?php echo ucwords(__("newpassword_label")); ?>" name="new_password" value="<?php echo isset($validator['new_password'])?$validator['new_password']:'';?>" maxlength="24"/>
								<label class="error"> <?php echo array_key_exists("new_password",$errors)?$errors["new_password"]:"";?><?php echo $same_pw;?></label>
							</div>
						</li>
						<li>
							<div class="full_name">
								<input type="password" onpaste="return false;" placeholder="<?php echo ucwords(__("confirm_password_label")); ?>" name="confirm_password" value="<?php echo isset($validator['confirm_password'])?$validator['confirm_password']:'';?>" maxlength="24"/>
								<label class="error" > <?php echo array_key_exists("confirm_password",$errors)?$errors["confirm_password"]:"";?></label>
							</div>
						</li>
						<li>
							<div class="submit">
								<input type="submit" name="submit_change_pass" title="<?php echo __('changepassword_label');?>" value="<?php echo __('changepassword_label');?>"/>
							</div>
						</li>
					</ul>
				</form>
			</div>
		</div>
	</section>
</div>
