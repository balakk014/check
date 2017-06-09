<?php
defined('SYSPATH') OR die("No direct access allowed.");
?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/permalink.js"></script>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 
            <form method="POST" enctype="multipart/form-data" class="form" action="" >
			<table class="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<?php 
						if(isset($model_details[0]['menu_name']) && !array_key_exists('menu_name',$postvalue)){
							$menu_name = $model_details[0]['menu_name'];
						}else{
							if(isset($postvalue['menu_name'])){
								$menu_name = $postvalue['menu_name'];
							}else{
								$menu_name = "";
							}
						}
					?> 
					<td valign="top" width="20%"><label><?php echo __('menu_name_label'); ?>:<span class="star">*</span></label></td>
					<td>
						<div class="new_input_field">
						<input type="text" autocomplete="off" maxlength="100" class="required" title="<?php echo __('enter_menu_name_label'); ?>" name="menu_name" id="menu_name" value="<?php echo $menu_name; ?>" >					
						<?php if(isset($errors) && array_key_exists('menu_name',$errors)){ echo "<span class='error'>".ucfirst($errors['menu_name'])."</span>";}?>
						</div>
					</td>
				</tr>
			<tr>
				<?php 
						if(isset($model_details[0]['menu_link']) && !array_key_exists('slug',$postvalue)){
							$menu_link = $model_details[0]['menu_link'];
						}else{
							if(isset($postvalue['slug'])){
								$menu_link = $postvalue['slug'];
							}else{
								$menu_link = "";
							}
						}
					?> 
				<td valign="top" width="20%"><label><?php echo __('permalink'); ?>:<span class="star">*</span></label></td>
				<td>
					<div class="new_input_field">
					<input type="text" readonly maxlength="100" title="<?php echo __('enter_menu_name_label'); ?>" name="slug" id="slug" value="<?php echo $menu_link; ?>" class="slug">
					<?php if(isset($errors) && array_key_exists('slug',$errors)){ echo "<span class='error'>".ucfirst($errors['slug'])."</span>";}?>		
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top" width="20%"><label><?php echo __('publish_status_label'); ?>:<span class="star">*</span></label> </td>
				<td> 
				<div>
					<input type="radio" name="status_posts" <?php if($model_details[0]['status_post'] == 'P') echo 'checked'; ?> id="status_post" value="Publish">Publish
					<input type="radio" <?php if($model_details[0]['status_post'] == 'U') echo 'checked'; ?> name="status_posts" id="status_post" value="Unpublish">Unpublish
				</div>			
				</td>
			<tr>
			
			<tr>	
				<td width="20%">&nbsp;</td>
				<td>
					<div class="new_button">  <input type="submit" name="submit_editmenu" title ="<?php echo __('btn_submit'); ?>" value="<?php echo __('btn_submit'); ?>"></div>
					<div class="new_button"> <input type="reset" name="reset_editmenu" title="<?php echo __('button_reset'); ?>" value="<?php echo __('button_reset'); ?>"></div>
				</td></tr>

			</table>
            </form>

        </div>

        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt" ></div></div>
    </div>

</div>

<script type="text/javascript">
 $(document).ready(function(){

 var field_val = $("#menu_name").val();
$("#menu_name").focus().val("").val(field_val);   
	
	$("#menu_name").keyup(function() {
		//this.value = this.value.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/0-9]/gi, '');
		this.value = this.value.replace(/[`~!@#$%^&*_+\-=?;:",.<>\{\}\[\]\\\/0-9]/gi, '');
	})
	$("#menu_name").slug();

		
});
</script>
