<?php defined('SYSPATH') OR die("No direct access allowed.");
echo html::script('public/common/ckeditor/ckeditor.js'); 

?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
         <form name="registration_form" class="form" id="registration_form" action="" method="post" enctype="multipart/form-data" data-form="server-form">
         <div class="responsive_table">
             <table border="0" cellpadding="5" cellspacing="0" width="100%">
          <tr>
		<td valign="top" width="20%"><label><?php echo __('menu_label'); ?></label><span class="star">*</span></td>        
		<td>
		<?php $field_type =''; if(isset($postvalue) && array_key_exists('menu_name',$postvalue)){ $field_type =  $postvalue['menu_name']; } ?>
		<div class="formRight new_input_field">
		<div class="selector fullwidthsel" id="uniform-user_type">
		<span><?php echo __('select_label'); ?></span>
		      <select name="menu_name" id="menu_name">
		      <option value=""><?php echo __('select_label'); ?></option>
		      <?php foreach($menu_details as $listings) { ?>
		      <option value="<?php echo $listings['menu_id']; ?>" <?php if($field_type == $listings['menu_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($listings['menu_name']); ?></option>
		      <?php } ?>
		      </select>
		</div>
		</div>      
		      <?php if(isset($errors) && array_key_exists('menu_name',$errors)){ echo "<span class='error'>".ucfirst($errors['menu_name'])."</span>";}?>

		</td>   	
	   </tr>   


           <tr>
           <td valign="top" width="20%"><label><?php echo __('meta_title_label'); ?></label><span class="star">*</span></td>        
	   <td>
		<div class="new_input_field">
			<textarea name="meta_title" id="meta_title" title="<?php echo __('enter_meta_title'); ?>" rows="7" cols="35"><?php if(isset($postvalue) && array_key_exists('meta_title',$postvalue)){ echo $postvalue['meta_title']; }?></textarea>
			<?php if(isset($errors) && array_key_exists('meta_title',$errors)){ echo "<span class='error'>".ucfirst($errors['meta_title'])."</span>";}?>
		   </div>
           </td>
           </tr>
           	   
           <tr>
           <td valign="top" width="20%"><label><?php echo __('meta_key_label'); ?></label><span class="star">*</span></td>        
	   <td>
		<div class="new_input_field">
			<textarea name="meta_keyword" id="meta_keyword" title="<?php echo __('enter_meta_keywords'); ?>" rows="7" cols="35"><?php if(isset($postvalue) && array_key_exists('meta_keyword',$postvalue)){ echo $postvalue['meta_keyword']; }?></textarea>
			<?php if(isset($errors) && array_key_exists('meta_keyword',$errors)){ echo "<span class='error'>".ucfirst($errors['meta_keyword'])."</span>";}?>
		   </div>
           </td>
           </tr> 

           <tr>
           <td valign="top" width="20%"><label><?php echo __('meta_desc_label'); ?></label><span class="star">*</span></td>        
	   <td>
		<div class="new_input_field">
			<textarea name="meta_description" id="meta_description" title="<?php echo __('enter_meta_description'); ?>" rows="7" cols="35"><?php if(isset($postvalue) && array_key_exists('meta_description',$postvalue)){ echo $postvalue['meta_description']; }?></textarea>
			<?php if(isset($errors) && array_key_exists('meta_description',$errors)){ echo "<span class='error'>".ucfirst($errors['meta_description'])."</span>";}?>
		   </div>
           </td>
           </tr> 
                      	           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('content'); ?></label></td>        
	   <td>
		<div class="new_input_field1">
			<textarea name="content" id="content" class="ckeditor" title="<?php echo __('entercontent'); ?>" rows="7" cols="35"><?php if(isset($postvalue) && array_key_exists('content',$postvalue)){ echo $postvalue['content']; }?></textarea>
			<?php if(isset($errors) && array_key_exists('content',$errors)){ echo "<span class='error'>".ucfirst($errors['content'])."</span>";}?>
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
							<input type="text" name="submit_addmanager" value="form" style="display:none;"/>
                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                             <div class="new_button">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addmanager" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button">   <input type="button" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>"  onclick="resetvalues()" /></div>
                            <div class="clr">&nbsp;</div>
                        </td>
                    </tr> 

                </table>
         </div>
        </form>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>  


<script type="text/javascript">
 $(document).ready(function(){
  $("#menu_name").focus(); 
});
function resetvalues()
{
	document.getElementById("registration_form").reset();
	CKEDITOR.instances.content.setData("");
}
</script>
