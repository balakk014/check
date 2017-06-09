<?php defined('SYSPATH') OR die("No direct access allowed."); 
echo html::script('public/common/ckeditor/ckeditor.js'); 
?>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
         <form name="registration_form" id="registration_form" class="form" action="" method="post" enctype="multipart/form-data">
          <table border="0" cellpadding="5" cellspacing="0" width="100%">
          
          
	<tr>
            <td><h2 class="tab_sub_tit"><?php echo ucfirst(__('edit_content_view')); ?></h2></td>
	<td></td>	          
	</tr>
	
	<tr>
		<td valign="top" width="20%"><label><?php echo __('menu_label'); ?></label><span class="star">*</span></td>        
		<td>
		<?php $field_type = $company_details[0]['menu_id']; 
		?>
		<div class="formRight">
		<div class="selector" id="uniform-user_type">
		<span><?php echo __('select_label'); ?></span>
		      <select name="menu_name" id="menu_name">
		      <option value=""><?php echo __("select_label"); ?></option>
		      <?php foreach($menu_details as $listings) { ?>
		      <option value="<?php echo $listings['menu_id']; ?>" <?php if($field_type == $listings['menu_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($listings['menu_name']); ?></option>
		      <?php } ?>
		      </select>
		</div>
		</div>      
		      <?php if(isset($errors) && array_key_exists('menu_name',$errors)){ echo "<span class='error'>".ucfirst($errors['menu_name'])."</span>";}?>

		</td>   	
	   </tr> 
	    <?php 
		if(isset($company_details[0]['meta_title']) && !array_key_exists('meta_title',$postvalue)){
			$meta_title = $company_details[0]['meta_title'];
		}else{
			if(isset($postvalue['meta_title'])){
				$meta_title = $postvalue['meta_title'];
			}else{
				$meta_title = "";
			}
		}
		?>  
	   
           <tr>
           <td valign="top" width="20%"><label><?php echo __('meta_title_label'); ?></label><span class="star">*</span></td>        
	   <td>
		<div class="new_input_field2">
			<textarea name="meta_title" style="width:50%;" id="meta_title" title="<?php echo __('enter_meta_title'); ?>" rows="7" cols="35"><?php echo $meta_title;  ?></textarea>
			<?php if(isset($errors) && array_key_exists('meta_title',$errors)){ echo "<span class='error'>".ucfirst($errors['meta_title'])."</span>";}?>
		   </div>
           </td>
           </tr>
        <?php 
		if(isset($company_details[0]['meta_keyword']) && !array_key_exists('meta_keyword',$postvalue)){
			$meta_keyword = $company_details[0]['meta_keyword'];
		}else{
			if(isset($postvalue['meta_keyword'])){
				$meta_keyword = $postvalue['meta_keyword'];
			}else{
				$meta_keyword = "";
			}
		}
		?>  
	    
           <tr>
           <td valign="top" width="20%"><label><?php echo __('meta_key_label'); ?></label><span class="star">*</span></td>        
	   <td>
		<div class="new_input_field2">
			<textarea name="meta_keyword" style="width:50%;" id="meta_keyword" title="<?php echo __('enter_meta_keywords'); ?>" rows="7" cols="35"><?php echo $meta_keyword;  ?></textarea>
			<?php if(isset($errors) && array_key_exists('meta_keyword',$errors)){ echo "<span class='error'>".ucfirst($errors['meta_keyword'])."</span>";}?>
		   </div>
           </td>
           </tr> 
		 <?php 
		if(isset($company_details[0]['meta_description']) && !array_key_exists('meta_description',$postvalue)){
			$meta_description = $company_details[0]['meta_description'];
		}else{
			if(isset($postvalue['meta_description'])){
				$meta_description = $postvalue['meta_description'];
			}else{
				$meta_description = "";
			}
		}
		?>  
           <tr>
           <td valign="top" width="20%"><label><?php echo __('meta_desc_label'); ?></label><span class="star">*</span></td>        
	   <td>
		<div class="new_input_field2">
			<textarea name="meta_description" style="width:50%;" id="meta_description" title="<?php echo __('enter_meta_description'); ?>" rows="7" cols="35"><?php echo $meta_description;  ?></textarea>
			<?php if(isset($errors) && array_key_exists('meta_description',$errors)){ echo "<span class='error'>".ucfirst($errors['meta_description'])."</span>";}?>
		   </div>
           </td>
           </tr> 
            <?php 
		if(isset($company_details[0]['content']) && !array_key_exists('content',$postvalue)){
			$content = $company_details[0]['content'];
		}else{
			if(isset($postvalue['content'])){
				$content = $postvalue['content'];
			}else{
				$content = "";
			}
		}
		?>  
	<tr>	 
	   <td valign="top" width="20%"><label><?php echo __('content'); ?></label></td>        
	   <td>
		   <div class="new_input_field1">	
              <textarea name="content" style="width:50%;" id="content" class="ckeditor required" title="<?php echo __('entercontent'); ?>" rows="7" cols="35"><?php echo $content;  ?></textarea>
              <?php if(isset($errors) && array_key_exists('content',$errors)){ echo "<span class='error'>".$errors['content']."</span>";}?>
		   </div>
	   </td>   	
           </tr>  
           
	           
		<tr>
		<td class="empt_cel">&nbsp;</td>
		<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
		</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
                            <div class="new_button"><input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                            <div class="new_button"><input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addcompany" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button"><input type="button" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" onclick="resetvalues()" /></div>
                            
                            <div class="clr">&nbsp;</div>
                        </td>
                    </tr> 

                </table>
        </form>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>  
<script type="text/javascript">
function resetvalues()
{
	$('#menu_name').val('');
	$('#meta_title').val('');
	$('#meta_keyword').val('');
	$('#meta_description').val('');
	CKEDITOR.instances.content.setData("");
}
 $(document).ready(function(){
  $("#menu_name").focus(); 
	
});   
</script>

