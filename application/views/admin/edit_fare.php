<?php defined('SYSPATH') OR die("No direct access allowed.");
	//$company_currency = findcompany_currency($_SESSION['company_id']);
	//$company_currency = $company_currency[0]['currency_symbol'];
        $company_currency=CURRENCY;
?>
<!-- time picker start-->
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">
         <form name="editmotor_form" class="form" id="editfare_form" action="" method="post" enctype="multipart/form-data">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">                             
	<?php 
		if(isset($model_details[0]['model_name']) && !array_key_exists('model_name',$postvalue)){
			$model_name = $model_details[0]['model_name'];
		}else{
			if(isset($postvalue['model_name'])){
				$model_name = $postvalue['model_name'];
			}else{
				$model_name = "";
			}
		}
		?>  
	<td valign="top" width="20%"><label><?php echo __('model_name');  ?></label><span class="star">*</span></td>        
	       <td>			   
		   <div class="new_input_field">
			   <?php echo isset($model_details[0]['model_name']) ? trim($model_details[0]['model_name']):""; ?>
              <input type="hidden" title="<?php echo __('model_name'); ?>" class="required" name="model_name" id="model_name" value="<?php echo $model_name; ?>"   />
              <?php if(isset($errors) && array_key_exists('model_name',$errors)){ echo "<span class='error'>".ucfirst($errors['model_name'])."</span>";}?>
			
		   </div>
           </td>   	
           </tr>   
           
      <?php 
		if(isset($model_details[0]['model_size']) && !array_key_exists('model_size',$postvalue)){
			$model_size = $model_details[0]['model_size'];
		}else{
			if(isset($postvalue['model_size'])){
				$model_size = $postvalue['model_size'];
			}else{
				$model_size = "";
			}
		}
		?>  
    <tr>
	<td valign="top" width="20%"><label><?php echo __('model_size'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  title="<?php echo __('entermodelsize'); ?>" class="required NotZero" id="model_size" name="model_size" value="<?php echo $model_size; ?>" />
              <?php if(isset($errors) && array_key_exists('model_size',$errors)){ echo "<span class='error'>".ucfirst($errors['model_size'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	<!--Fare Details start-->
	  <?php 
		if(isset($model_details[0]['base_fare']) && !array_key_exists('base_fare',$postvalue)){
			$base_fare = $model_details[0]['base_fare'];
		}else{
			if(isset($postvalue['base_fare'])){
				$base_fare = $postvalue['base_fare'];
			}else{
				$base_fare = "";
			}
		}
		?>  
	  <tr>
              <td><h2 class="tab_sub_tit"><?php echo ucfirst(__('fare_details')); ?></h2></td>
	   <td><input type="hidden" name="company_model_fare_id" id="company_model_fare_id" value="<?php echo $model_details[0]['company_model_fare_id']; ?>" ></td>	          
           </tr>
           <tr>
           <td valign="top" width="20%"><label><?php echo __('base_fare'); ?>(<?php echo $company_currency; ?>)</label><span class="star">*</span></td>        
	       <td>			   
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterthebase_fare'); ?>" class="required numbersdots" name="base_fare" id="base_fare" value="<?php echo $base_fare; ?>"  minlength="1" maxlength="7" onkeypress="return onlyDotsAndNumbers(this,event)" />
              <?php if(isset($errors) && array_key_exists('base_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['base_fare'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
             <?php 
				if(isset($model_details[0]['min_km']) && !array_key_exists('min_km',$postvalue)){
					$min_km = $model_details[0]['min_km'];
				}else{
					if(isset($postvalue['min_km'])){
						$min_km = $postvalue['min_km'];
					}else{
						$min_km = "";
					}
				}
				?>   
            <tr>
           <td valign="top" width="20%"><label><?php echo __('taxi_min_km'); ?></label><span class="star">*</span></td>        
	       <td>			   
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_min_km'); ?>" class="required numbersdots" name="min_km" id="min_km" value="<?php echo $min_km; ?>"  minlength="1" maxlength="7"  />
              <?php if(isset($errors) && array_key_exists('min_km',$errors)){ echo "<span class='error'>".ucfirst($errors['min_km'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
            <?php 
				if(isset($model_details[0]['min_fare']) && !array_key_exists('min_fare',$postvalue)){
					$min_fare = $model_details[0]['min_fare'];
				}else{
					if(isset($postvalue['min_fare'])){
						$min_fare = $postvalue['min_fare'];
					}else{
						$min_fare = "";
					}
				}
				?>   
            <tr>
           <td valign="top" width="20%"><label><?php echo __('min_fare'); ?>(<?php echo $company_currency; ?>)</label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterthemin_fare'); ?>" class="required numbersdots" name="min_fare" id="min_fare" value="<?php echo $min_fare; ?>"  minlength="1" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('min_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['min_fare'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
             <?php 
				if(isset($model_details[0]['cancellation_fare']) && !array_key_exists('cancellation_fare',$postvalue)){
					$cancellation_fare = $model_details[0]['cancellation_fare'];
				}else{
					if(isset($postvalue['cancellation_fare'])){
						$cancellation_fare = $postvalue['cancellation_fare'];
					}else{
						$cancellation_fare = "";
					}
				}
				?>   
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('cancellation_fare'); ?>(<?php echo $company_currency; ?>)</label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterthecancellation_fare'); ?>" class="required numbersdots" name="cancellation_fare" id="cancellation_fare" value="<?php echo $cancellation_fare; ?>"  minlength="1" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('cancellation_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['cancellation_fare'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
           <?php 
				if(isset($model_details[0]['below_above_km']) && !array_key_exists('below_above_km',$postvalue)){
					$below_above_km = $model_details[0]['below_above_km'];
				}else{
					if(isset($postvalue['below_above_km'])){
						$below_above_km = $postvalue['below_above_km'];
					}else{
						$below_above_km = "";
					}
				}
				?>   
           <tr>
           <td valign="top" width="20%"><label><?php echo __('below_and_above_km'); ?></label><span class="star">*</span></td>        
	       <td>			   
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_below_and_above_km'); ?>" class="required" name="below_and_above_km" id="below_and_above_km" value="<?php echo $below_above_km; ?>"  minlength="1" maxlength="7" onkeypress="return onlyDotsAndNumbers(this,event)"   />
              <?php if(isset($errors) && array_key_exists('below_and_above_km',$errors)){ echo "<span class='error'>".ucfirst($errors['below_and_above_km'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
             <?php 
				if(isset($model_details[0]['below_km']) && !array_key_exists('below_km',$postvalue)){
					$below_km = $model_details[0]['below_km'];
				}else{
					if(isset($postvalue['below_km'])){
						$below_km = $postvalue['below_km'];
					}else{
						$below_km = "";
					}
				}
				?>   
           <tr>
           <td valign="top" width="20%"><label><?php echo sprintf(__('below_km'),''); ?>(<?php echo $company_currency; ?>)</label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterthebelow_km'); ?>" class="required numbersdots" name="below_km" id="below_km" value="<?php echo $below_km; ?>"  minlength="1" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('below_km',$errors)){ echo "<span class='error'>".ucfirst($errors['below_km'])."</span>";}?>
		   </div>
           </td>   	
           </tr>   
             <?php 
				if(isset($model_details[0]['above_km']) && !array_key_exists('above_km',$postvalue)){
					$above_km = $model_details[0]['above_km'];
				}else{
					if(isset($postvalue['above_km'])){
						$above_km = $postvalue['above_km'];
					}else{
						$above_km = "";
					}
				}
				?>   
           <tr>
           <td valign="top" width="20%"><label><?php echo sprintf(__('above_km'),''); ?>(<?php echo $company_currency; ?>)</label><span class="star">*</span></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('entertheabove_km'); ?>" class="required numbersdots" name="above_km" id="above_km" value="<?php echo $above_km; ?>"  minlength="1" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('above_km',$errors)){ echo "<span class='error'>".ucfirst($errors['above_km'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
            <?php 
				if(isset($model_details[0]['waiting_time']) && !array_key_exists('waiting_time',$postvalue)){
					$waiting_time = $model_details[0]['waiting_time'];
				}else{
					if(isset($postvalue['waiting_time'])){
						$waiting_time = $postvalue['waiting_time'];
					}else{
						$waiting_time = "";
					}
				}
				?>   
           <tr>
           <td valign="top" width="20%"><label><?php echo __('waiting_charge_ph'); ?>(<?php echo $company_currency; ?>)</label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterwaiting_charge'); ?>" class="required numbersdots" name="waiting_time" id="waiting_time" value="<?php echo $waiting_time; ?>"    minlength="1" maxlength="20" />
              <?php if(isset($errors) && array_key_exists('waiting_time',$errors)){ echo "<span class='error'>".ucfirst($errors['waiting_time'])."</span>";}?>

		   </div>
           </td>   	
	   </tr> 
	       <?php 
				if(isset($model_details[0]['minutes_fare']) && !array_key_exists('minutes_fare',$postvalue)){
					$minutes_fare = $model_details[0]['minutes_fare'];
				}else{
					if(isset($postvalue['minutes_fare'])){
						$minutes_fare = $postvalue['minutes_fare'];
					}else{
						$minutes_fare = "";
					}
				}
				?>  
           <tr>
           <td valign="top" width="20%"><label><?php echo __('fare_per_minute'); ?>(<?php echo $company_currency; ?>)</label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">	
              <input type="text"  class="required numbersdots" title="<?php echo __('enter_fare_per_minute'); ?>" id="minutes_fare" name="minutes_fare" value="<?php echo $minutes_fare; ?>" minlength="1" maxlength="20" />
              <?php if(isset($errors) && array_key_exists('minutes_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['minutes_fare'])."</span>";}?>
		   </div>
	   </td>   	
           </tr> 
           <?php 
				if(isset($model_details[0]['night_charge']) && !array_key_exists('night_charge',$postvalue)){
					$night_charge = $model_details[0]['night_charge'];
				}else{
					if(isset($postvalue['night_charge'])){
						$night_charge = $postvalue['night_charge'];
					}else{
						$night_charge = "";
					}
				}
				?>   
   
           
            <tr>
           <td valign="top" width="20%"><label><?php echo __('night_charge'); ?></label><span class="star">*</span></td>        
	       <td>
			   <?php $nfield_type =  $night_charge; ?>
		    <div class="formRight">
			<div class="selector new_input_field" id="uniform-user_type">
		      <select name="night_charge" id="night_charge" class="required" title="<?php echo __('enterthenight_charge'); ?>">
		       <option value=""><?php echo __("select_label"); ?></option>
              <option value="1" <?php if($nfield_type == '1') { echo 'selected=selected'; } ?>><?php echo __("yes"); ?></option>
              <option value="0" <?php if($nfield_type == '0') { echo 'selected=selected'; } ?>><?php echo __("no"); ?></option>
              </select>
            </div>
		   </div>
		   <div class="new_input_field">
              <label for="night_charge" generated="true" style="display:none;" class="errorvalid"><?php echo __('enterthenight_charge'); ?></label>	
              <?php if(isset($errors) && array_key_exists('night_charge',$errors)){ echo "<span class='error'>".ucfirst($errors['night_charge'])."</span>";}?>
              </div>
              <?php if(isset($postvalue['night_charge'])) { $nyt_charge = $postvalue['night_charge']; }else{ $nyt_charge=""; } ?>
              <?php  $nc = $model_details[0]['night_charge']; ?>
           </td>   	
           </tr> 
           </table>
           <div id="charge_det" <?php if($nc==1 || $nyt_charge==1){ ?> style="display:block;padding-left:5px;" <?php }else if($nc==0 || $nyt_charge==0 || $nyt_charge==""){ ?> style="display:none;padding-left:5px;" <?php } ?>>
           <table border="0" cellpadding="5" cellspacing="0" width="100%">
           <?php 
				if(isset($model_details[0]['night_timing_from']) && !array_key_exists('night_timing_from',$postvalue)){
					$night_timing_from = $model_details[0]['night_timing_from'];
				}else{
					if(isset($postvalue['night_timing_from'])){
						$night_timing_from = $postvalue['night_timing_from'];
					}else{
						$night_timing_from = "";
					}
				}
				?>   
   
            <tr>
           <td valign="top" width="20%"><label><?php echo __('night_timing_from'); ?></label><span class="star">*</span></td>        
	       <td style="padding:5px 0;">
		   <div class="new_input_field">
              <input type="text" readonly title="<?php echo __('enterthenight_timing_from'); ?>" class="required" name="night_timing_from" id="night_timing_from" value="<?php echo $night_timing_from; ?>"  minlength="7" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('night_timing_from',$errors)){ echo "<span class='error'>".ucfirst($errors['night_timing_from'])."</span>";}?>
		   </div>
           </td>   	
           </tr>            
           <tr>
           <td></td>        
	       <td id="valid_from" style="display:none">
			    <div class="new_input_field errorvalid">
			    <?php echo __('kindly_select_thetimegreater_thanorequal_to'); ?> <?php echo NIGHT_FROM;?>
			   </div>
			   </td>
	       </tr>
	          <?php 
				if(isset($model_details[0]['night_timing_to']) && !array_key_exists('night_timing_to',$postvalue)){
					$night_timing_to = $model_details[0]['night_timing_to'];
				}else{
					if(isset($postvalue['night_timing_to'])){
						$night_timing_to = $postvalue['night_timing_to'];
					}else{
						$night_timing_to = "";
					}
				}
				?>   
   
           <tr>
           <td valign="top" width="20%"><label><?php echo __('night_timing_to'); ?></label><span class="star">*</span></td>        
			<td style="padding: 5px 0;">
		   <div class="new_input_field">
              <input type="text" readonly title="<?php echo __('enterthenight_timing_to'); ?>" class="required" name="night_timing_to" id="night_timing_to" value="<?php echo $night_timing_to; ?>"  minlength="7" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('night_timing_to',$errors)){ echo "<span class='error'>".ucfirst($errors['night_timing_to'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
           
           <tr>
           <td></td>        
	       <td id="valid_to" style="display:none">
			    <div class="new_input_field errorvalid">
			    <?php echo __("kindly_select_thetimeless_thanorequal_to"); ?> <?php echo NIGHT_TO;?>
			   </div>
			   </td>
	       </tr>
             <?php 
				if(isset($model_details[0]['night_fare']) && !array_key_exists('night_fare',$postvalue)){
					$night_fare = $model_details[0]['night_fare'];
				}else{
					if(isset($postvalue['night_fare'])){
						$night_fare = $postvalue['night_fare'];
					}else{
						$night_fare = "";
					}
				}
				?>   
           <tr>
           <td valign="top" width="20%"><label><?php echo __('night_fare'); ?></label><span class="star">*</span></td>        
	       <td style="padding: 5px 0;">
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterthenight_fare'); ?>" class="required numbersdots" name="night_fare" id="night_fare" value="<?php echo $night_fare; ?>"  minlength="1" maxlength="30"  />
              <?php if(isset($errors) && array_key_exists('night_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['night_fare'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
	  </table>
	  </div>
	  
	  <!--Fare Details end-->
		<?php 
			if(isset($model_details[0]['evening_charge']) && !array_key_exists('evening_charge',$postvalue)){
				$evening_charge = $model_details[0]['evening_charge'];
			}else{
				if(isset($postvalue['evening_charge'])){
					$evening_charge = $postvalue['evening_charge'];
				}else{
					$evening_charge = "";
				}
			}
			?>   
	  <!-- evening fare details start -->
	   <table border="0" cellpadding="5" cellspacing="0" width="100%">
		   <tr>
			   <td valign="top" width="20%"><label><?php echo __('evening_charge'); ?></label><span class="star">*</span></td>        
			   <td style="padding:5px;">
				   <?php $efield_type =  $evening_charge; ?>
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
		<?php 
			if(isset($model_details[0]['evening_timing_from']) && !array_key_exists('evening_timing_from',$postvalue)){
				$evening_timing_from = $model_details[0]['evening_timing_from'];
			}else{
				if(isset($postvalue['evening_timing_from'])){
					$evening_timing_from = $postvalue['evening_timing_from'];
				}else{
					$evening_timing_from = "";
				}
			}
			?>   
       <div id="evening_charge_det" <?php if($efield_type==1){ ?> style="display:block;padding-left:5px;" <?php }else if($efield_type==0){ ?> style="display:none;padding-left:5px;" <?php } ?>>
           <table cellpadding="5" cellspacing="0" width="100%">
				<tr>
			   <td valign="top" width="20%"><label><?php echo __('evening_timing_from'); ?></label><span class="star">*</span></td>        
			   <td style="padding:5px 0;">
			   <div class="new_input_field">
				  
				  <input type="text" title="<?php echo __('entertheevening_timing_from'); ?>"  readonly class="required" name="evening_timing_from" id="evening_timing_from" value="<?php echo $evening_timing_from; ?>"  minlength="7" maxlength="30"  />
				  <?php if(isset($errors) && array_key_exists('evening_timing_from',$errors)){ echo "<span class='error'>".ucfirst($errors['evening_timing_from'])."</span>";}?>
			   </div>
			   </td>   	
			   </tr>            
				<tr>
			   <td></td>        
			   <td id="evening_valid_from" style="display:none">
					<div class="new_input_field errorvalid">
					<!--Kindly select the time greater than or equal to <?php //echo EVENING_FROM;?> -->
					<?php echo __('kindly_select_thetime_between'); ?> <?php echo EVENING_FROM;?> <?php echo __('and'); ?> <?php echo EVENING_TO;?>
				   </div>
				   </td>
			   </tr>
			   <tr>
			   <td valign="top" width="20%"><label><?php echo __('evening_timing_to'); ?></label><span class="star">*</span></td> 
			  	 <td style="padding: 5px 0;">
				<?php 
				if(isset($model_details[0]['evening_timing_to']) && !array_key_exists('evening_timing_to',$postvalue)){
					$evening_timing_to = $model_details[0]['evening_timing_to'];
				}else{
					if(isset($postvalue['evening_timing_to'])){
						$evening_timing_to = $postvalue['evening_timing_to'];
					}else{
						$evening_timing_to = "";
					}
				}
				?> 
			   <div class="new_input_field">
				  <input type="text" readonly title="<?php echo __('entertheevening_timing_to'); ?>" class="required" name="evening_timing_to" id="evening_timing_to" value="<?php echo $evening_timing_to; ?>"  minlength="7" maxlength="30"  />
				  <?php if(isset($errors) && array_key_exists('evening_timing_to',$errors)){ echo "<span class='error'>".ucfirst($errors['evening_timing_to'])."</span>";}?>
			   </div>
			   </td>   	
			   </tr> 
			   
			   <tr>
			   <td></td>        
			   <td id="evening_valid_to" style="display:none">
					<div class="new_input_field errorvalid">
					<!--Kindly select the time less than or equal to <?php //echo EVENING_TO;?> -->
					<?php echo __("kindly_select_thetime_between"); ?> <?php echo EVENING_FROM;?> <?php echo __('and'); ?> <?php echo EVENING_TO;?>
				   </div>
				   </td>
				   
				   <td id="evening_valid_to_greater" style="display:none">
					<div class="new_input_field errorvalid">
					<?php echo __('eveningtimingto_shouldbe_greaterthan_eveningtiming_from'); ?>
				   </div>
				   </td>
			   </tr>
			   <?php 
				if(isset($model_details[0]['evening_fare']) && !array_key_exists('evening_fare',$postvalue)){
					$evening_fare = $model_details[0]['evening_fare'];
				}else{
					if(isset($postvalue['evening_fare'])){
						$evening_fare = $postvalue['evening_fare'];
					}else{
						$evening_fare = "";
					}
				}
				?>        
			   <tr>
			   <td valign="top" width="20%"><label><?php echo __('evening_fare'); ?></label><span class="star">*</span></td>
				<td style="padding: 5px 0;">
			   <div class="new_input_field">
				  <input type="text" title="<?php echo __('entertheevening_fare'); ?>" class="required numbersdots" name="evening_fare" id="evening_fare" value="<?php echo $evening_fare; ?>"  minlength="1" maxlength="30"  />
				  <?php if(isset($errors) && array_key_exists('evening_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['evening_fare'])."</span>";}?>
			   </div>
			   </td>   	
			   </tr>
				 <!--Fare Details end-->
		   </table>
	   </div>
		   <!-- evening fare details end -->
	  
	<table width="100%">
	<tr>
		<td class="empt_cel" width="20%">&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
				<td class="empt_cel" width="20%">&nbsp;</td>
                        <td colspan="">
                    
                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                            <div class="new_button">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_editmodel" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
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
//to allow numbers only
$("#model_size" ).keyup(function() {
	this.value = this.value.replace(/[`~!@#$%^&*()\s_|+\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
});

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
				/*$("#night_timing_from").removeClass("required hasDatepicker").addClass("hasDatepicker");				
				$("#night_timing_to").removeClass("required hasDatepicker").addClass("hasDatepicker");				
				$("#night_fare").removeClass("required numbersdots");*/
			}
			else if(night_charge=="")
			{
				$("#charge_det").css('display','none');
				$("#night_timing_from").val("");
				$("#night_timing_to").val("");				
				$("#night_fare").val("");
			}
			
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
    });
    
    $('#evening_timing_from').change(function(){
        var st = $('#evening_timing_from').val();
        var et = $('#evening_timing_to').val();
        
        var base = "Wed, 09 Aug 1995 ";
		var pickuptime_d = Date.parse(base+st.substr(0, 5)+":00"+st.substr(5));
		var currentTime_d = Date.parse(base+et.substr(0, 5)+":00"+et.substr(5));
		if ((st >= '<?php echo EVENING_FROM;?>') && (st <= '<?php echo EVENING_TO;?>'))
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
		if ((et >= '<?php echo EVENING_FROM;?>') && (et <= '<?php echo EVENING_TO;?>') && et >= st)
		{
			$("#evening_valid_to").css('display','none');
			$("#evening_valid_to_greater").css('display','none');
               return true;
		}
		else
		{
			 if(et < st) {
				$("#evening_valid_to").css('display','none');
				$("#evening_valid_to_greater").css('display','block');
			} else {
				$("#evening_valid_to").css('display','block');
				$("#evening_valid_to_greater").css('display','none');
			}
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
    
    jQuery("#editfare_form").validate();
    //for model size field zero validation
	$.validator.addMethod('NotZero', function(value, element) {
		return value > 0
	}, '<?php echo __("please_enter_morethan_zero"); ?>');
});
</script>
