<?php defined('SYSPATH') OR die('No direct access allowed.'); 

 /* Status Box Checked */
//------------------------
$status_checked ="";
 if(isset($user_data['status'])	&& $user_data['status']== ACTIVE)
 { $status_checked="checked='checked'"; }
 
 //For based on the user type, field defined
 //=========================================
 $status_field=$back_button=$admin_status_field="";
 if(isset($user_data['usertype']) && $user_data['usertype']!=ADMIN) { 
  
 $status_field="<tr>
                  	<td><label>".__('status_label')."</label></td>
                    <td>
                   	   	<input type='checkbox' name='status[]' value='A' ".$status_checked." />";   
                         if(isset($errors['status'])) {
						 	$status_field.="<span style='padding-left: 5px;' class='label_error'>".$errors['status']."</span>";
						} 
                    $status_field.="</td>
                 </tr> ";
                        
  //$back_button="<input type='button' value=".__('button_back')." title=".__('button_back')." onclick=\'location.href='/manageusers/index'\' /></a>";
}else{
	 $admin_status_field="
	<input type='hidden' name='status[]' value='A' >";
	}
 
 
?>


<div class="bread_crumb" >
	<!-- common config  home link -->
	<?php 
	$atag_start='<a href="'.URL_BASE.'admin/login" title="Home">'; 
	$atag_end='</a>';?>	
	<?php echo $atag_start. "Home".$atag_end;?>
	<span class="fwn"><img src="<?php echo URL_BASE;?>public/admin/images/list_arrow_medium.png" width="14px" height="14px" _class="mt5"/></span>
	
	<div style="float: left;"><?php echo "Edit People"; ?></div>
	</div>
<div class="container_content fl clr">
<div class="cont_container mt15 mt10">
    <div class="content_top"><div class="top_left"></div><div class="top_center"></div><div class="top_rgt"></div></div>
    <div class="content_middle">
		 <form method="post" enctype="multipart/form-data" class="admin_form" name="frmuser" id="frmuser" action ="<?php echo URL_BASE;?>manageusers/<?php echo $action;?>">
		 <div class="admin_new_tab">
                <table cellpadding="10" cellspacing="1" <?php echo $table_css; ?>>

                        <tr>
                                <td valign="top" width="20%"><label><?php echo  __('name_label'); ?></label><span class="star">*</span></td>
                                <td>
                                		<input type="text" name="name" maxlength="32" id="name" value="<?php echo isset($user_data['name']) &&!array_key_exists('name',$errors)? trim($user_data['name']):$validator['name']; ?>"/>
                                   <span class="error">
                                        <?php echo isset($errors['name'])?$errors['name']:""; ?>
                                    </span>
                                </td>
                        </tr>

                         <tr>
                                <td valign="top"><label><?php echo  __('email_label'); ?></label><span class="star">*</span></td>
                                <td>
                                	 <input type="text" name="email" maxlength="50" value="<?php echo isset($user_data['email']) && !array_key_exists("email",$errors)? trim($user_data['email']):$validator['email']; ?>"/>
                                     <span class="error">
                                        <?php echo isset($emailid_exist)?$emailid_exist:""; echo isset($errors['email'])?$errors['email']:""; ?>
                                        <?php echo isset($email_exists)?$email_exists:""; ?>
                                     </span>                          
                                </td>
                        </tr> 
                         <tr>
                                <td valign="top"><label><?php echo __('password_label'); ?></label><span class="star">*</span></td>
                                <td>
                                	 <input type="password" name="password" maxlength="50" value="<?php echo isset($user_data['password']) && !array_key_exists('password',$errors)? trim($user_data['password']):''; ?>"/>
                                     <span class="error">
                                        <?php echo isset($errors['password'])?$errors['password']:""; ?>
                                     </span>                          
                                </td>
                        </tr> 
                         <tr>
                                <td valign="top"><label><?php echo  __('location_label'); ?></label><span class="star">*</span></td>
                                <td>
                                	 <input type="text" name="location" maxlength="50" value="<?php echo isset($user_data['location']) && !array_key_exists('location',$errors)? trim($user_data['location']):$validator['location']; ?>"/>
                                     <span class="error">
                                        <?php echo isset($errors['location'])?$errors['location']:""; ?>
                                     </span>                          
                                </td>
                        </tr>                         
                         <tr>
                                <td valign="top"><label><?php echo  __('industry_label'); ?></label><span class="star">*</span></td>
                                <td>
                                	 <input type="text" name="industry" maxlength="50" value="<?php echo isset($user_data['industry']) && !array_key_exists('industry',$errors)? trim($user_data['industry']):$validator['industry']; ?>"/>
                                     <span class="error">
                                        <?php echo isset($errors['industry'])?$errors['industry']:""; ?>
                                     </span>                          
                                </td>
                        </tr>                         
                          <tr>
                                <td valign="top"><label><?php echo  __('smart_tags_label'); ?></label><span class="star">*</span></td>
                                <td>
                                	 <input type="text" name="smart_tags" maxlength="50" value="<?php echo isset($user_data['smart_tags']) && !array_key_exists('smart_tags',$errors)? trim($user_data['smart_tags']):$validator['smart_tags']; ?>"/>
                                     <span class="error">
                                        <?php echo isset($errors['smart_tags'])?$errors['smart_tags']:""; ?>
                                     </span>                          
                                </td>
                        </tr>                       
                         <tr>                       
                         <td valign="top"><label><?php echo __('photo_label'); ?></label></td>
                      	<td>
							      
								     <?php 
										//code to remove or delete photo link
										$user_image_path=IMGPATH.NO_IMAGE;
										$light_box_class=$delete_link=$atag_start=$atag_end="";
										$image_title=__('no_photo');
										$image_title=$user_data['username'];
										//check if file exists or not
										if(((isset($user_data)) && $user_data['photo']) && (file_exists(DOCROOT.USER_IMGPATH.$user_data['photo'])))
								        { 
								           $user_image_path = URL_BASE.USER_IMGPATH.$user_data['photo'];
								           $image_title=$user_data['username'];
										   $light_box_class="class='lightbox'";
										   $delete_title = __('delete'); 
										   $delete_link="<a onclick='frmdel_photo(".$user_data['id'].");' class='deleteicon' title='$delete_title' id='photo_delete'></a>"; 
										   $atag_start='<a href='.$user_image_path.' title='.$image_title.'>'; 
										   $atag_end='</a>';										   
										 }
										  
										?>
                                        
                                        <?php echo $delete_link; ?>
                                        <span style="margin-right:30px;" id="gallery">
										<?php echo $atag_start; ?>                                        
                                        <img src="<?php echo $user_image_path; ?>" title="<?php echo ucfirst($image_title); ?>" class="fl" width="<?php echo USER_SMALL_IMAGE_WIDTH;?>" height="<?php echo USER_SMALL_IMAGE_HEIGHT;?>" >
										<?php echo $atag_end; ?>
                                        </span>

						    <div style="width:300px;float:left;">
						          <span class="text_bg4 fl" style="margin:0 0 0 5px;">
						               <p class="fl clr" style="width:140px;text-align:center;font-weight:bold;font-size:11px;"><?php echo __('profile_image_content');?> <?php echo __('profile_image_resolution');?></p>
						          </span>
						        <div class="order_button fl clr" style="width:auto;margin:0 0 0 5px;">
						            <!-- <div class="order_but_left fl"></div> -->
						           <div class="orderr_but_mid fl">
						              <input name="photo" type="file" style="height:30px;width:auto; font:bold 12px Arial, Helvetica, sans-serif;color:#333;"/>  
						            </div>

						         </div>
                                  <span class="error-userphoto" id="userphoto">
                                        <?php echo isset($errors['file'])?$errors['file']:""; ?>
                                    </span>		
						        </div>
						        
					        
                             </td>

                        </tr>   
                             
                 
                        <tr>
                        	<td colspan="2" class="star">*<?php echo __('required_label'); ?></td>
                        </tr>                         
                        <tr>
                                <td colspan="2" style="padding-left:110px;">
                                  
                                  	
                                  	<input type="reset" value="<?php echo __("button_reset"); ?>" title="<?php echo __("button_reset"); ?>" />
								  	<input type="submit" value="<?php echo ($action == 'add' )?''.__("button_add").'':''.__("button_update").'';?>" name="<?php echo ($action == 'add' )?'admin_add':'admin_edit';?>" title="<?php echo ($action == 'add' )?''.__("button_add").'':''.__("button_update").'';?>" />
								  	
                                  <div class="clr">&nbsp;</div>
                                </td>
                        </tr> 

                </table></div>
        </form>
    </div>
    <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
</div>
</div>

<script type="text/javascript">
//code for checking message field maxlength
//============================
function limitlength(obj, maxlength, label_id){
        //var maxlength=length
        if (obj.value.length>maxlength){
                obj.value=obj.value.substring(0, maxlength);
                // max reach
                //$("span.info_label").html("<?php echo __('ddfdsfdsf');?>");
                document.getElementById(label_id).innerHTML="<?php echo __('entered_max_text');?>"+maxlength;
        }else{
                var charleft = maxlength - obj.value.length;
                //alert(charleft);
                //inner html
                document.getElementById(label_id).innerHTML= charleft+"<?php echo __('entered_char_left_text');?>";
        }     
}

//For Delete the users photo
//=====================
function frmdel_photo(userid)
{
    var answer = confirm("<?php echo __('delete_alert_image');?>")
    if (answer){
        window.location="<?php echo URL_BASE;?>manageusers/delete_userphoto/"+userid;
    }

    return false;  
} 

$(document).ready(function(){


});
</script>
