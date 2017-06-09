<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery-1.6.3.min.js"></script>
<!-- time picker start-->
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<!-- time picker start-->

 <div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 
         <form name="addmotor_form" id="addmotor_form" class="form" action="" method="post" enctype="multipart/form-data">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">                                                  
	<td valign="top" width="20%"><label><?php echo __('motorcompanyname'); ?></label><span class="star">*</span></td>        
	<td>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('companyname',$postvalue)){ $field_type =  $postvalue['companyname']; } ?>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
              <select name="companyname" id="companyname" class="required" title="<?php echo __('selectthemotorcompanyname'); ?>">
              <option value=""><?php echo __('select_label'); ?></option>
              <?php foreach($motor_details as $listings) { ?>
              <option value="<?php echo $listings['motor_id']; ?>"  <?php if($field_type == $listings['motor_id']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($listings['motor_name']); ?></option>
              <?php } ?>
              </select>
        </div>
        <label for="companyname" generated="true" style="display:none;" class="errorvalid"><?php echo __('selectthemotorcompanyname'); ?></label>	
	</div>      
              <?php if(isset($errors) && array_key_exists('companyname',$errors)){ echo "<span class='error'>".ucfirst($errors['companyname'])."</span>";}?>

	</td>   	
	</tr> 
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('model_name'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php echo __('entermodelname'); ?>" class="required" name="model_name" id="model_name" value="<?php if(isset($postvalue) && array_key_exists('model_name',$postvalue)){ echo $postvalue['model_name']; }?>" minlength="2" maxlength="30" />
              <?php if(isset($errors) && array_key_exists('model_name',$errors)){ echo "<span class='error'>".ucfirst($errors['model_name'])."</span>";}?>
	</div>
	</td>   	
	</tr> 
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('model_size'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
        <input type="text" title="<?php echo __('entermodelsize'); ?>" class="required" name="model_size" id="model_size" value="<?php if(isset($postvalue) && array_key_exists('model_size',$postvalue)){ echo $postvalue['model_size']; }?>" maxlength="3" />
        <?php if(isset($errors) && array_key_exists('model_size',$errors)){ echo "<span class='error'>".ucfirst($errors['model_size'])."</span>";}?>
	</div>
	</td>   	
	</tr> 

	  <!--Fare Details start-->
	  <tr>
	   <td><?php echo ucfirst(__('fare_details')); ?></td>
	   <td></td>	          
           </tr>
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('base_fare'); ?>(<?php echo CURRENCY; ?>)</label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterthebase_fare'); ?>" class="required numbersdots" name="base_fare" id="base_fare" value="<?php if(isset($postvalue) && array_key_exists('base_fare',$postvalue)){ echo $postvalue['base_fare']; }?>"  />
              <?php if(isset($errors) && array_key_exists('base_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['base_fare'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('taxi_min_km'); ?></label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_min_km'); ?>" class="required numbersdots" name="min_km" id="min_km" value="<?php if(isset($postvalue) && array_key_exists('min_km',$postvalue)){ echo $postvalue['min_km']; }?>" minlength="1" maxlength="7"  />
              <?php if(isset($errors) && array_key_exists('min_km',$errors)){ echo "<span class='error'>".ucfirst($errors['min_km'])."</span>";}?>
		   </div>
           </td>   	
           </tr>   
           
            <tr>
           <td valign="top" width="20%"><label><?php echo __('min_fare'); ?>(<?php echo CURRENCY; ?>)</label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterthemin_fare'); ?>" class="required numbersdots" name="min_fare" id="min_fare" value="<?php if(isset($postvalue) && array_key_exists('min_fare',$postvalue)){ echo $postvalue['min_fare']; }?>"  minlength="1" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('min_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['min_fare'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
           
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('cancellation_fare'); ?>(<?php echo CURRENCY; ?>)</label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterthecancellation_fare'); ?>" class="required numbersdots" name="cancellation_fare" id="cancellation_fare" value="<?php if(isset($postvalue) && array_key_exists('cancellation_fare',$postvalue)){ echo $postvalue['cancellation_fare']; }?>"  minlength="1" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('cancellation_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['cancellation_fare'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('below_and_above_km'); ?></label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_below_and_above_km'); ?>" class="required onlynumbers" name="below_and_above_km" id="below_and_above_km" value="<?php if(isset($postvalue) && array_key_exists('below_and_above_km',$postvalue)){ echo $postvalue['below_and_above_km']; }?>" minlength="1" maxlength="7"  />
              <?php if(isset($errors) && array_key_exists('below_and_above_km',$errors)){ echo "<span class='error'>".ucfirst($errors['below_and_above_km'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
           
           <tr>
           <td valign="top" width="20%"><label><?php echo sprintf(__('below_km'),''); ?></label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterthebelow_km'); ?>" class="required numbersdots" name="below_km" id="below_km" value="<?php if(isset($postvalue) && array_key_exists('below_km',$postvalue)){ echo $postvalue['below_km']; }?>"  minlength="1" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('below_km',$errors)){ echo "<span class='error'>".ucfirst($errors['below_km'])."</span>";}?>
		   </div>
           </td>   	
           </tr>   
           
           <tr>
           <td valign="top" width="20%"><label><?php echo sprintf(__('above_km'),''); ?></label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('entertheabove_km'); ?>" class="required numbersdots" name="above_km" id="above_km" value="<?php if(isset($postvalue) && array_key_exists('above_km',$postvalue)){ echo $postvalue['above_km']; }?>"  minlength="1" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('above_km',$errors)){ echo "<span class='error'>".ucfirst($errors['above_km'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
           
            <tr>
           <td valign="top" width="20%"><label><?php echo __('waiting_charge_ph'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterwaiting_charge'); ?>" class="required numbersdots" name="waiting_time" id="waiting_time" value="<?php if(isset($postvalue) && array_key_exists('waiting_time',$postvalue)){ echo $postvalue['waiting_time']; }?>"    minlength="1" maxlength="20" />
              <?php if(isset($errors) && array_key_exists('waiting_time',$errors)){ echo "<span class='error'>".ucfirst($errors['waiting_time'])."</span>";}?>

		   </div>
           </td>   	
           </tr> 
           
           <tr>
			<td valign="top" width="20%"><label><?php echo __('fare_per_minute'); ?></label><span class="star">*</span></td>        
			<td>
			<div class="new_input_field">
				<input type="text" title="<?php echo __('enter_fare_per_minute'); ?>" class="required" name="minutes_fare" id="minutes_fare" value="<?php if(isset($postvalue) && array_key_exists('minutes_fare',$postvalue)){ echo $postvalue['minutes_fare']; }?>" maxlength="3" />
				<?php if(isset($errors) && array_key_exists('minutes_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['minutes_fare'])."</span>";}?>
			</div>
			</td>
		</tr>
            	 
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('night_charge'); ?></label><span class="star">*</span></td>        
	       <td>
			   <?php $nfield_type =''; if(isset($postvalue) && array_key_exists('night_charge',$postvalue)){ $nfield_type =  $postvalue['night_charge']; } ?>
		   <div class="formRight">
			<div class="selector" id="uniform-user_type">
		      <select name="night_charge" id="night_charge" class="required" title="<?php echo __('enterthenight_charge'); ?>">
		      <option value=""><?php echo __('select_label'); ?></option>
              <option value="1" <?php if($nfield_type == '1') { echo 'selected=selected'; } ?>><?php echo __('yes'); ?></option>
              <option value="0" <?php if($nfield_type == '0') { echo 'selected=selected'; } ?>><?php echo __('no'); ?></option>
              </select>
              
		   </div>
		   </div>
		    <div class="new_input_field">
              <label for="night_charge" generated="true" style="display:none;" class="errorvalid"><?php echo __('enterthenight_charge'); ?></label>	
              <?php if(isset($errors) && array_key_exists('night_charge',$errors)){ echo "<span class='error'>".ucfirst($errors['night_charge'])."</span>";}?>
              </div>
           </td>   	
           </tr> 
		</table>
		<div id="charge_det" <?php if($nfield_type==1){ ?> style="display:block;padding-left:5px;" <?php }else if($nfield_type==0){ ?> style="display:none;padding-left:5px;" <?php } ?>>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
           <td valign="top" width="20%"><label><?php echo __('night_timing_from'); ?></label><span class="star">*</span></td>        
	       <td style="padding:5px 0;">
		   <div class="new_input_field_charge">
              <input type="text" title="<?php echo __('enterthenight_timing_from'); ?>"  class="required"  readonly name="night_timing_from" id="night_timing_from" value="<?php if(isset($postvalue) && array_key_exists('night_timing_from',$postvalue)){ echo $postvalue['night_timing_from']; }?>"  minlength="7" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('night_timing_from',$errors)){ echo "<span class='error'>".ucfirst($errors['night_timing_from'])."</span>";}?>
		   </div>
           </td>   	
           </tr>            
            <tr>
           <td></td>        
	       <td id="valid_from" style="display:none">
			    <div class="new_input_field_charge errorvalid">
			    <?php echo __('kindly_select_thetimegreater_thanorequal_to'); ?> <?php echo NIGHT_FROM;?>
			   </div>
			   </td>
	       </tr>
           <tr>
           <td valign="top" width="20%"><label><?php echo __('night_timing_to'); ?></label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field_charge">
              <input type="text" readonly title="<?php echo __('enterthenight_timing_to'); ?>" class="required" name="night_timing_to" id="night_timing_to" value="<?php if(isset($postvalue) && array_key_exists('night_timing_to',$postvalue)){ echo $postvalue['night_timing_to']; }?>"  minlength="7" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('night_timing_to',$errors)){ echo "<span class='error'>".ucfirst($errors['night_timing_to'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
           
           <tr>
           <td></td>        
	       <td id="valid_to" style="display:none">
			    <div class="new_input_field_charge errorvalid">
			    <?php echo __('kindly_select_thetimeless_thanorequal_to'); ?> <?php echo NIGHT_TO;?>
			   </div>
			   </td>
	       </tr>
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('night_fare'); ?></label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field_charge">
              <input type="text" title="<?php echo __('enterthenight_fare'); ?>" class="required numbersdots" name="night_fare" id="night_fare" value="<?php if(isset($postvalue) && array_key_exists('night_fare',$postvalue)){ echo $postvalue['night_fare']; }?>"  minlength="1" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('night_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['night_fare'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
             <!--Fare Details end-->
	   </table>
	   </div>
	   
	   <!-- evening fare details start -->
	   <table border="0" cellpadding="5" cellspacing="0" width="100%">
		   <tr>
			   <td valign="top" width="20%"><label><?php echo __('evening_charge'); ?></label><span class="star">*</span></td>        
			   <td style="padding:5px;">
				   <?php $efield_type =''; if(isset($postvalue) && array_key_exists('evening_charge',$postvalue)){ $efield_type =  $postvalue['evening_charge']; } ?>
			   <div class="formRight">
				<div class="selector" id="uniform-user_type">
				  <select name="evening_charge" id="evening_charge" class="required" title="<?php echo __('entertheevening_charge'); ?>">
				  <option value=""><?php echo __('select_label'); ?></option>
				  <option value="1" <?php if($efield_type == '1') { echo 'selected=selected'; } ?>><?php echo __('yes'); ?></option>
				  <option value="0" <?php if($efield_type == '0') { echo 'selected=selected'; } ?>><?php echo __('no'); ?></option>
				  </select>
				  
			   </div>
			   </div>
				<div class="new_input_field">
				  <label for="evening_charge" generated="true" style="display:none;" class="errorvalid"><?php echo __('entertheevening_charge'); ?></label>	
				  <?php if(isset($errors) && array_key_exists('evening_charge',$errors)){ echo "<span class='error'>".ucfirst($errors['evening_charge'])."</span>";}?>
				  </div>
			   </td>   	
		   </tr>
       </table>
       
       <div id="evening_charge_det" <?php if($efield_type==1){ ?> style="display:block;padding-left:5px;" <?php }else if($efield_type==0){ ?> style="display:none;padding-left:5px;" <?php } ?>>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
			   <td valign="top" width="20%"><label><?php echo __('evening_timing_from'); ?></label></td>        
			   <td style="padding:5px 0;">
			   <div class="new_input_field_charge">
				  
				  <input type="text" title="<?php echo __('entertheevening_timing_from'); ?>"  readonly name="evening_timing_from" id="evening_timing_from" value="<?php if(isset($postvalue) && array_key_exists('evening_timing_from',$postvalue)){ echo $postvalue['evening_timing_from']; }?>"  minlength="7" maxlength="30"  />
				  <?php if(isset($errors) && array_key_exists('evening_timing_from',$errors)){ echo "<span class='error'>".ucfirst($errors['evening_timing_from'])."</span>";}?>
			   </div>
			   </td>   	
			   </tr>            
				<tr>
			   <td></td>        
			   <td id="evening_valid_from" style="display:none">
					<div class="new_input_field_charge errorvalid">
					<?php echo __('kindly_select_thetimegreater_thanorequal_to'); ?> <?php echo EVENING_FROM;?>
				   </div>
				   </td>
			   </tr>
			   <tr>
			   <td valign="top" width="20%"><label><?php echo __('evening_timing_to'); ?></label></td>        
			   <td>
			   <div class="new_input_field_charge">
				  <input type="text" readonly title="<?php echo __('entertheevening_timing_to'); ?>" name="evening_timing_to" id="evening_timing_to" value="<?php if(isset($postvalue) && array_key_exists('evening_timing_to',$postvalue)){ echo $postvalue['evening_timing_to']; }?>"  minlength="7" maxlength="30"  />
				  <?php if(isset($errors) && array_key_exists('evening_timing_to',$errors)){ echo "<span class='error'>".ucfirst($errors['evening_timing_to'])."</span>";}?>
			   </div>
			   </td>   	
			   </tr> 
			   
			   <tr>
			   <td></td>        
			   <td id="evening_valid_to" style="display:none">
					<div class="new_input_field_charge errorvalid">
					<?php echo __('kindly_select_thetimeless_thanorequal_to'); ?> <?php echo EVENING_TO; ?>
				   </div>
				   </td>
			   </tr>
			   
			   <tr>
			   <td valign="top" width="20%"><label><?php echo __('evening_fare'); ?></label></td>        
			   <td>
			   <div class="new_input_field_charge">
				  <input type="text" title="<?php echo __('entertheevening_fare'); ?>" class="numbersdots" name="evening_fare" id="evening_fare" value="<?php if(isset($postvalue) && array_key_exists('evening_fare',$postvalue)){ echo $postvalue['evening_fare']; }?>"  minlength="1" maxlength="30"  />
				  <?php if(isset($errors) && array_key_exists('evening_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['evening_fare'])."</span>";}?>
			   </div>
			   </td>   	
			   </tr>
				 <!--Fare Details end-->
		   </table>
	   </div>
       <!-- evening fare details end -->
	   
	   <table border="0" cellpadding="0" cellspacing="0" width="100%">
	   <tr>
	<td width="19%">&nbsp;</td>
	<td>&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
			<td>&nbsp;</td><td>&nbsp;</td>
                        <td colspan="">
                            <br />
                    
                            <div class="button blackB">     <input type="button" value="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                            <div class="button dredB">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            <div class="button greenB">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addmodel" title="<?php echo __('btn_submit' );?>" /></div>
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
$(document).ready(function(){

$("#companyname").focus();	
 
$("#night_timing_from, #evening_timing_from").timepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
stepHour: 1,
stepMinute: 1,
minDateTime : new Date("<?php echo date('H:i:s'); ?>"),
stepSecond: 1
} );

$("#night_timing_to, #evening_timing_to").timepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
stepHour: 1,
stepMinute: 1,
minDateTime : new Date("<?php echo date('H:i:s'); ?>"),
stepSecond: 1
} );



	 jQuery("#addmotor_form").validate();
	 
	$.validator.addMethod( "imageonly", function(value,element){
var pathLength = value.length; var lastDot = value.lastIndexOf( "."); var fileType = value.substring(lastDot,pathLength).toLowerCase(); return this.optional(element) || fileType.match(/(?:.jpg|.jpeg|.png)$/) }, "Please upload image(jpg,jpeg,png) files only");

	change_state();	
	change_city();	
	
});

    $('#night_timing_from').change(function(){
        //var st = parseInt($('#night_timing_from').val().replace(':', ''), 10); 
        //var et = parseInt($('#night_timing_to').val().replace(':', ''), 10);
        var st = $('#night_timing_from').val();
        var et = $('#night_timing_to').val();
        
        var base = "Wed, 09 Aug 1995 ";
		var pickuptime_d = Date.parse(base+st.substr(0, 5)+":00"+st.substr(5));
		var currentTime_d = Date.parse(base+et.substr(0, 5)+":00"+et.substr(5));
		if ((st >= '<?php echo NIGHT_FROM;?>') )
		{
			$("#valid_from").css('display','none');			
               return true;
		}
		else
		{
			 $("#valid_from").css('display','block');
			 $("#night_timing_from").val("");
               return false;
		}
    });
    

    $('#night_timing_to').change(function(){
        //var st = parseInt($('#night_timing_from').val().replace(':', ''), 10); 
        //var et = parseInt($('#night_timing_to').val().replace(':', ''), 10);
        var st = $('#night_timing_from').val();
        var et = $('#night_timing_to').val();
        var base = "Wed, 09 Aug 1995 ";
		var pickuptime_d = Date.parse(base+st.substr(0, 5)+":00"+st.substr(5));
		var currentTime_d = Date.parse(base+et.substr(0, 5)+":00"+et.substr(5));
		if ((et <= '<?php echo NIGHT_TO;?>') || (et >= '22:00:00'))
		{
			$("#valid_to").css('display','none');
               return true;
		}
		else
		{
			 $("#valid_to").css('display','block');
			 $("#night_timing_to").val("");
               return false;
		}
    });


 $("#night_charge").change(function() {

      		var night_charge = $("#night_charge").val();
      		if(night_charge==1)
      		{
				$("#charge_det").css('display','block');
				$("#night_timing_from").val("");
				$("#night_timing_to").val("");				
				$("#night_fare").val("");
			}
			else if(night_charge==0)
      		{
				$("#charge_det").css('display','none');
				$("#night_timing_from").val("00:00:00");
				$("#night_timing_to").val("00:00:00");				
				$("#night_fare").val("0");
				/*$("#night_timing_from").removeClass("required hasDatepicker");				
				$("#night_timing_to").removeClass("required hasDatepicker");				
				$("#night_fare").removeClass("required numbersdots");*/
				/*$("#night_timing_from").removeClass("required hasDatepicker").addClass("hasDatepicker");				
				$("#night_timing_to").removeClass("required hasDatepicker").addClass("hasDatepicker");				
				$("#night_fare").removeClass("required numbersdots");*/
			}
			else 
			{
				$("#charge_det").css('display','none');
				$("#night_timing_from").val("");
				$("#night_timing_to").val("");				
				$("#night_fare").val("");
			}
    });
    
    $('#evening_timing_from').change(function(){
        var st = $('#evening_timing_from').val();
        var et = $('#evening_timing_to').val();
        
        var base = "Wed, 09 Aug 1995 ";
		var pickuptime_d = Date.parse(base+st.substr(0, 5)+":00"+st.substr(5));
		var currentTime_d = Date.parse(base+et.substr(0, 5)+":00"+et.substr(5));
		if ((st >= '<?php echo EVENING_FROM;?>') )
		{
			$("#evening_valid_from").css('display','none');			
               return true;
		}
		else
		{
			 $("#evening_valid_from").css('display','block');
			 $("#evening_timing_from").val("");
               return false;
		}
    });
    
     $('#evening_timing_to').change(function(){
        var st = $('#evening_timing_from').val();
        var et = $('#evening_timing_to').val();
        var base = "Wed, 09 Aug 1995 ";
		var pickuptime_d = Date.parse(base+st.substr(0, 5)+":00"+st.substr(5));
		var currentTime_d = Date.parse(base+et.substr(0, 5)+":00"+et.substr(5));
		if ((et <= '<?php echo EVENING_TO;?>') || (et >= '22:00:00'))
		{
			$("#evening_valid_to").css('display','none');
               return true;
		}
		else
		{
			 $("#evening_valid_to").css('display','block');
			 $("#evening_timing_to").val("");
               return false;
		}
    });
    
	$("#evening_charge").change(function() {
      		var evening_charge = $("#evening_charge").val();
      		if(evening_charge==1)
      		{
				$("#evening_charge_det").css('display','block');
				$("#evening_timing_from").val("");
				$("#evening_timing_to").val("");				
				$("#evening_fare").val("");
			}
			else if(evening_charge==0)
      		{
				$("#evening_charge_det").css('display','none');
				$("#evening_timing_from").val("00:00:00");
				$("#evening_timing_to").val("00:00:00");				
				$("#evening_fare").val("0");
			}
			else 
			{
				$("#evening_charge_det").css('display','none');
				$("#evening_timing_from").val("");
				$("#evening_timing_to").val("");				
				$("#evening_fare").val("");
			}
    });
    
    $("#country").change(function() {

      		var countryid= $("#country").val();
      		var stateid= $("#state").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getlist_state",
			type:"get",
			data:"country_id="+countryid+"&state_id="+stateid,
			success:function(data){

			$('#state_list').html();
			$('#state_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    });

function change_state()
{

     		var countryid= $("#country").val();
     		var stateid= $("#state").val();
     		

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getlist_state",
			type:"get",
			data:"country_id="+countryid+"&state_id="+stateid,
			success:function(data){

			$('#state_list').html();
			$('#state_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}
    

function change_city()
{

      		var stateid= $("#state").val();
		var countryid= $("#country").val();
		var cityid= $("#city").val();
		
		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getcitylist",
			type:"get",
			data:"country_id="+countryid+"&state_id="+stateid+"&city_id="+cityid,
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

</script>
