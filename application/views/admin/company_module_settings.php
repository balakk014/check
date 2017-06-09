<?php defined('SYSPATH') OR die("No direct access allowed.");
//$tagcount = count($site_info_settings);
?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery-1.6.3.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<script type='text/javascript'>
        function addFields(){
            // Number of inputs to create
            var number = document.getElementById("member").value;
            // Container <div> where dynamic content will be placed
            var container = document.getElementById("inputfields");
            // Clear previous contents of the container
            while (container.hasChildNodes()) {
                container.removeChild(container.lastChild);
            }
            if(number<=5){
		    for (i=0;i<number;i++){
			// Append a node with a random text
			container.appendChild(document.createTextNode("Enter site tags description " + (i+1)));
			// Create an <input> element, set its type and name attributes
			var input = document.createElement("input");
			input.type = "text";
			input.name = "member" + i;
			container.appendChild(input);
			// Append a line break 
			container.appendChild(document.createElement("br"));
		    }
		}else
		{
			alert('<?php echo __("thetotal_nooftagsnot_greater_than5"); ?>');
		}
        }
</script>
<style ="text/css">
.inputfields .form input[type="text"]
{
width:350px;
}
.errorvalid{padding:0px;}
</style>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle mods_sten"> 
			<form method="post" enctype="multipart/form-data" class="form" name="managerating_form" id="managerating_form" action="module_settings" >
   
				<div class="widget">
					<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" />
						<h6><?php echo $page_title; ?></h6>
					</div>
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
					<tr>
						<td><?php echo __('module_settings_info'); ?></td>
						<td></td>
					</tr>
					<?php $i=1; foreach($site_settings as $site_info){  ?>

					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('image_tag').$i; ?></label><span class="star">*</span>
						</td>  
						<td>
						<div class="new_input_field">
						<input type="text" class="required" id="image_tag<?php echo $i; ?>" name="image_tag<?php echo $i; ?>" value="<?php echo isset($site_info) &&!array_key_exists("image_tag$i",$postvalue)? trim($site_info['image_tag']):$postvalue['image_tag'.$i]; ?>" title="<?php echo __('enter_image_tag').$i; ?> " >
						</div>						
						</td>      					
					</tr>
					
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('alt_tags').$i; ?></label><span class="star">*</span>
						</td>  
						<td>
						<div class="new_input_field">
						<input type="text" class="required" id="tags<?php echo $i; ?>" name="tags<?php echo $i; ?>" value="<?php echo isset($site_info) &&!array_key_exists("tags$i",$postvalue)? trim($site_info['alt_tags']):$postvalue['tags'.$i]; ?>" title="<?php echo __('enter_alt_tags').$i; ?> " >
						</div>						
						</td>      					
					</tr>	
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('banner_image').$i; ?></label><span class="star">*</span>
						</td>        
						<td>
							<div class="new_input_field">
							<?php if(empty($site_info['banner_image'])) { $cl="required"; } else{ $cl=""; }  ?>	
							<input type="file" class=" imageonly <?php echo $cl; ?>" name="banner_image<?php echo $i; ?>" title="<?php echo __('select_taxi_image'); ?>" value="<?php if(count($site_settings) >0){ echo isset($site_info) &&!array_key_exists("banner_image$i",$postvalue)? trim($site_info['banner_image']):$postvalue['banner_image'.$i];} ?>">
							<input type="hidden" name="image_id<?php echo $i; ?>" value="<?php  echo $site_info['id']; ?>">
							</div>
							<br/><p><?php echo __('banner_image_width_height'); ?></p>
							<?php if(!empty($site_info['banner_image'])&&file_exists(DOCROOT.BANNER_IMGPATH.$site_info['banner_image'])){ ?>
							<div class="site_logo" style="width:220px;height:200px;">
								<img height="200" src="<?php echo URL_BASE.BANNER_IMGPATH.$site_info['banner_image'];?>">
							</div>
							<?php  } ?>
						</td>
					</tr>
					<?php $i++; } ?>
					</table>
					<div id="inputfields" class="new_input_field"></div>
					<div class="button dredB" style="margin-left:210px;">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
					<div class="button greenB">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_modules" title="<?php echo __('btn_submit' );?>" />
					
					</div>
					<p>&nbsp;</p>
				</div>
				
			</form>
		</div>
	</div>
</div>
<div class="clr">&nbsp;</div>
<script type="text/javascript">
 $(document).ready(function(){

var field_val = $("#member0").val();
$("#member0").focus().val("").val(field_val);

  	
	 jQuery("#managerating_form").validate();
	 
	$.validator.addMethod( "imageonly", function(value,element){
var pathLength = value.length; var lastDot = value.lastIndexOf( "."); var fileType = value.substring(lastDot,pathLength).toLowerCase(); return this.optional(element) || fileType.match(/(?:.jpg|.jpeg|.png)$/) }, "<?php echo __('please_upload_image_files_only'); ?>");

});   

</script>
