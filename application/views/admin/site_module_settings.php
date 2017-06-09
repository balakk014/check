<?php defined('SYSPATH') OR die("No direct access allowed.");
$tagcount = count($site_info_settings);
$no_image=URL_BASE.'public/common/images/no-image.png';
?>
<?php /* <script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery-1.6.3.min.js"></script> */ ?>
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
			alert('The total no of tags not greater than 5');
		}
        }
</script>
<style type="text/css">
.inputfields .form input[type="text"]
{
width:350px;
}
</style>

<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle"> 
			<form method="post" enctype="multipart/form-data" class="form" name="managerating_form" id="managerating_form" action="module_settings" >
   
				
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
					<tr>
						<td><h2 class="tab_sub_tit"><?php echo __('module_settings_info'); ?></h2></td>
						<td></td>
					</tr>
					<?php /*if($tagcount=='5'){ 
						for($i=0;$i<$tagcount;$i++){
						?>
						<tr>
						<td valign="top" width="20%">
							<label><?php echo __('Enter site tags description').($i+1); ?></label><span class="star">*</span>
						</td>
						<td>
							<div class="new_input_field">
							<input type="text" id="member<?php echo $i; ?>" class="required" title="<?php echo __('Enter site tags description').($i+1); ?>" name="member<?php echo $i;?>" value="<?php echo isset($site_info_settings) &&!array_key_exists('content',$postvalue)? trim($site_info_settings[$i]['content']):$postvalue['content']; ?>" --onkeyup="addFields()">
							</div>
						</td>
						
						<tr>
					<?php } }else{ ?>
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('enter_fields'); ?></label><span class="star">*</span>
						</td>        
						<td>
							<div class="new_input_field">
							<input type="text" id="member" name="member" value="" onkeyup="addFields()">Number of tag descriptions: (max. 5)
							</div>
						</td>
					</tr>
					<?php } */?>
					<!-- member varible for manage site tag lines on the fron page -->
					<input type="hidden" id="member" name="member" value="<?php echo $tagcount;?>"/> 
					<input type="hidden" name="image_id" value="<?php if(count($site_settings) >0){ echo $site_settings[0]['id'];}else{ echo 0; } ?>">
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('alt_tags').'1'; ?></label><span class="star">*</span>
						</td>  
						<td>
						<div class="new_input_field">
						<input type="text" class="required" id="tags1" name="tags1" value="<?php echo isset($site_info_settings) &&!array_key_exists('tags1',$postvalue)? trim(isset($site_info_settings[0]['alt_tags'])?$site_info_settings[0]['alt_tags']:''):$postvalue['tags1']; ?>" title="<?php echo __('enter_alt_tags').'1'; ?> " >
						</div>						
						</td>      					
					</tr>	
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('banner_image').'1'; ?></label><span class="star">*</span>
						</td>        
						<td>
						
							<div class="new_input_field">
							<input type="file" class=" imageonly" name="banner_image1" title="<?php echo __('select_taxi_image'); ?>" value="<?php if(count($site_settings) >0){ echo isset($site_settings) &&!array_key_exists('banner_image1',$postvalue)? trim($site_settings[0]['banner_image1']):$postvalue['banner_image1'];} ?>">
							</div>
							<?php if(count($site_settings) >0){
								if(file_exists(DOCROOT.BANNER_IMGPATH.$site_settings[0]['banner_image1'])){
									$image=URL_BASE.BANNER_IMGPATH.$site_settings[0]['banner_image1'];
								}else{
									$image=$no_image;
								}
							?>
							<div class="site_logo" style="width:220px;">
								<img src="<?php echo $image;?>">
							</div>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('alt_tags').'2'; ?></label><span class="star">*</span>
						</td>  
						<td>
						<div class="new_input_field">
						<input type="text" class="required" id="tags2" name="tags2" value="<?php echo isset($site_info_settings) &&!array_key_exists('tags2',$postvalue)? trim(isset($site_info_settings[1]['alt_tags'])?$site_info_settings[1]['alt_tags']:''):$postvalue['tags2']; ?>" title="<?php echo __('enter_alt_tags').'2'; ?> " >
						</div>						
						</td>      					
					</tr>						
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('banner_image').'2'; ?></label><span class="star">*</span>
						</td>        
						<td>
							<div class="new_input_field">
							<input type="file" class=" imageonly" name="banner_image2" title="<?php echo __('select_taxi_image'); ?>" value="<?php if(count($site_settings) >0){ echo isset($site_settings) &&!array_key_exists('banner_image2',$postvalue)? trim($site_settings[0]['banner_image2']):$postvalue['banner_image2']; } ?>">
							</div>
							<?php if(count($site_settings) >0){
								if(file_exists(DOCROOT.BANNER_IMGPATH.$site_settings[0]['banner_image2'])){
									$image=URL_BASE.BANNER_IMGPATH.$site_settings[0]['banner_image2'];
								}else{
									$image=$no_image;
								}
							?>
							<div class="site_logo" style="width:220px;">
								<img src="<?php echo $image;?>">
							</div>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('alt_tags').'3'; ?></label><span class="star">*</span>
						</td>  
						<td>
						<div class="new_input_field">
						<input type="text" class="required" id="tags3" name="tags3" value="<?php echo isset($site_info_settings) &&!array_key_exists('tags3',$postvalue)? trim(isset($site_info_settings[2]['alt_tags'])?$site_info_settings[2]['alt_tags']:''):$postvalue['tags3']; ?>" title="<?php echo __('enter_alt_tags').'3'; ?> " >
						</div>						
						</td>      					
					</tr>					
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('banner_image').'3'; ?></label><span class="star">*</span>
						</td>        
						<td>
							<div class="new_input_field">
							<input type="file" class=" imageonly" name="banner_image3" title="<?php echo __('select_taxi_image'); ?>" value="<?php if(count($site_settings) >0){ echo isset($site_settings) &&!array_key_exists('banner_image3',$postvalue)? trim($site_settings[0]['banner_image3']):$postvalue['banner_image3']; } ?>">
							</div>
							<?php if(count($site_settings) >0){
								if(file_exists(DOCROOT.BANNER_IMGPATH.$site_settings[0]['banner_image3'])){
									$image=URL_BASE.BANNER_IMGPATH.$site_settings[0]['banner_image3'];
								}else{
									$image=$no_image;
								}
							?>
							<div class="site_logo" style="width:220px;">
								<img src="<?php echo $image;?>">
							</div>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('alt_tags').'4'; ?></label><span class="star">*</span>
						</td>  
						<td>
						<div class="new_input_field">
						<input type="text" class="required" id="tags4" name="tags4" value="<?php echo isset($site_info_settings) &&!array_key_exists('tags4',$postvalue)? trim(isset($site_info_settings[3]['alt_tags'])?$site_info_settings[3]['alt_tags']:''):$postvalue['tags4']; ?>" title="<?php echo __('enter_alt_tags').'4'; ?> " >
						</div>						
						</td>      					
					</tr>					
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('banner_image').'4'; ?></label><span class="star">*</span>
						</td>        
						<td>
							<div class="new_input_field">
							<input type="file" class=" imageonly" name="banner_image4" title="<?php echo __('select_taxi_image'); ?>" value="<?php if(count($site_settings) >0){ echo isset($site_settings) &&!array_key_exists('banner_image4',$postvalue)? trim($site_settings[0]['banner_image4']):$postvalue['banner_image4']; } ?>" >
							</div>
							<?php if(count($site_settings) >0){
								if(file_exists(DOCROOT.BANNER_IMGPATH.$site_settings[0]['banner_image4'])){
									$image=URL_BASE.BANNER_IMGPATH.$site_settings[0]['banner_image4'];
								}else{
									$image=$no_image;
								}
							?>
							<div class="site_logo" style="width:220px;">
								<img src="<?php echo $image;?>">
							</div>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('alt_tags').'5'; ?></label><span class="star">*</span>
						</td>  
						<td>
						<div class="new_input_field">
						<input type="text" class="required" id="tags5" name="tags5" value="<?php echo isset($site_info_settings) &&!array_key_exists('tags5',$postvalue)? trim(isset($site_info_settings[4]['alt_tags'])?$site_info_settings[4]['alt_tags']:''):$postvalue['tags5']; ?>" title="<?php echo __('enter_alt_tags').'5'; ?> " >
						</div>						
						</td>      					
					</tr>					
					<tr>
						<td valign="top" width="20%">
							<label><?php echo __('banner_image').'5'; ?></label><span class="star">*</span>
						</td>        
						<td>
							<div class="new_input_field">
							<input type="file" class=" imageonly" name="banner_image5" title="<?php echo __('select_taxi_image'); ?>" value="<?php if(count($site_settings) >0){ echo isset($site_settings) &&!array_key_exists('banner_image5',$postvalue)? trim($site_settings[0]['banner_image5']):$postvalue['banner_image5']; } ?>" >
							</div>
							<?php if(count($site_settings) >0){
								if(file_exists(DOCROOT.BANNER_IMGPATH.$site_settings[0]['banner_image5'])){
									$image=URL_BASE.BANNER_IMGPATH.$site_settings[0]['banner_image5'];
								}else{
									$image=$no_image;
								}
							?>
							<div class="site_logo" style="width:220px;">
								<img src="<?php echo $image;?>">
							</div>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td class="empt_cel" valign="top" width="20%">
							&nbsp;
						</td> 
						<td style="padding-bottom:15px;">
							<div class="new_button">  <input type="submit" value="<?php echo __('submit' );?>" name="submit_modules" title="<?php echo __('submit' );?>" /></div>
							<div class="new_button">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
							
						</td>
					</tr>
					</table>
					<div id="inputfields" class="new_input_field"></div>
				
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
 $(document).ready(function(){

var field_val = $("#member0").val();
$("#member0").focus().val("").val(field_val);

  	
	 jQuery("#managerating_form").validate();
	 
	
	$.validator.addMethod( "imageonly", function(value,element){
var pathLength = value.length; var lastDot = value.lastIndexOf( "."); var fileType = value.substring(lastDot,pathLength).toLowerCase(); return this.optional(element) || fileType.match(/(?:.jpg|.jpeg|.png)$/) }, "Please upload image(jpg,jpeg,png) files only");

});   

</script>
