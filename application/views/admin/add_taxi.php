<?php defined('SYSPATH') OR die("No direct access allowed."); 
$field_count = count($additional_fields);
?>
<?php /* <script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery-1.6.3.min.js"></script> */ ?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<?php /* <script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script> */ ?>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">
         <form name="addtaxi_form" id="addtaxi_form" class="form" action="" method="post" enctype="multipart/form-data">
         <table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
            <td><h2 class="tab_sub_tit"><?php echo ucfirst(__('taxi_inform'));//print_r($errors); ?></h2></td>
	<td></td>	          
	</tr>          

	<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_no'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php echo __('enter_taxi_no'); ?>" id="taxi_no" class="required" name="taxi_no" value="<?php if(isset($postvalue) && array_key_exists('taxi_no',$postvalue)){ echo $postvalue['taxi_no']; }?>" minlength="4" maxlength="30" />
              <?php if(isset($errors) && array_key_exists('taxi_no',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_no'])."</span>";}?>
	</div>
	</td>   	
	</tr> 
	<?php 
	//echo $_SESSION['user_type'];
	if(($_SESSION['user_type'] =='A') || ($_SESSION['user_type'] =='DA') || ($_SESSION['user_type'] =='S'))
	{ 
	
	$field_type =''; if(isset($postvalue) && array_key_exists('company_name',$postvalue) && empty($cid)){ $field_type =  $postvalue['company_name']; } else if(!empty($cid)) { $field_type = $cid; } ?>
	<td valign="top" width="20%"><label><?php echo __('taxicompany'); ?></label><span class="star">*</span></td><td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="taxicompany_list">
		<select name="company_name" id="company_name" class="required" onchange="getcountry(this.value);">
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($taxicompany_details as $company_list) {  ?>
		<option value="<?php echo $company_list['cid']; ?>" <?php if($field_type == $company_list['cid']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($company_list["company_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
		<label for="company_name" style="display:none" class="errorvalid companyname_css" id="companyname_css"><?php echo __('select_the_company'); ?></label>	
              <?php if(isset($errors) && array_key_exists('company_name',$errors)){ echo "<span class='error'>".ucfirst($errors['company_name'])."</span>"; }?>
	</tr>
	<?php } 
	else { ?> 
	<tr>
	<td valign="top" width="20%"></td>
	<td>
	<div class="new_input_field">	
	<input type="hidden" name="company_name" id="company_name" value="<?php echo $_SESSION['company_id']; ?>">
	<?php if(isset($errors) && array_key_exists('company_name',$errors)){ echo "<span class='error'>".ucfirst($errors['company_name'])."</span>"; } ?>	
	</div>
	</td>
	</tr>
	<?php }
	?>
	<?php /*
	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('taxi_type',$postvalue)){ $field_type =  $postvalue['taxi_type']; } ?>
	
	<td valign="top" width="20%"><label><?php echo __('taxi_type'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
              <select name="taxi_type" id="taxi_type" class="required" title="<?php echo __('select_the_taxitype'); ?>">
              <option value="">--Select--</option>

              <?php foreach($motor_details as $listings) { ?>
              <option value="<?php echo $listings['motor_id']; ?>" <?php if($field_type == $listings['motor_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($listings['motor_name']); ?></option>
              <?php } ?>
              </select>
        </div>
	</div>
	<label for="taxi_type" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_taxitype'); ?></label>
              <?php if(isset($errors) && array_key_exists('taxi_type',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_type'])."</span>";}?>

	</td>   	
	</tr>
	*/ ?>
	<input type="hidden" name="taxi_type" id="taxi_type" value="1">
	
	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('taxi_model',$postvalue)){ $field_type =  $postvalue['taxi_model']; } ?>
	<td valign="top" width="20%"><label><?php echo __('taxi_model'); ?></label><span class="star">*</span></td>        
	<td>
		<div class="formRight">
		<div class="selector" id="uniform-user_type">
		<span><?php echo __('select_label'); ?></span>
		<div id="taximodels">
			<select name="taxi_model" id="taxi_model" onchange="getTaxiSpeed(this.value)" class="required" title="<?php echo __('select_the_taximodel'); ?>">	
				<?php if(($_SESSION['user_type'] =='A') || ($_SESSION['user_type'] =='DA') || ($_SESSION['user_type'] =='S')){ ?>	      		      

					<option value=""><?php echo __('select_company'); ?></option>
					<?php
					if(!empty($model_details_new)){ ?>
					<option value=""><?php echo __('select_label'); ?></option>
					<?php foreach($model_details_new as $list) { ?>				
						<option value="<?php echo $list['model_id']; ?>" <?php if($field_type == $list['model_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($list['model_name']); ?></option> 
					<?php }
					}else{ ?><option value=""><?php echo __('nomodels_found'); ?></option><?php }
					?>

				<?php }else{

					if(!empty($model_details_new)){ ?>
					<option value=""><?php echo __('select_label'); ?></option>
					<?php foreach($model_details_new as $list) { ?>				
						<option value="<?php echo $list['model_id']; ?>" <?php if($field_type == $list['model_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($list['model_name']); ?></option> 
					<?php }
					}else{ ?><option value=""><?php echo __('nomodels_found'); ?></option><?php }
					
				}  ?>
			</select>		      
		</div>      		
		</div>
		</div>
		<label for="taxi_model" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_taximodel'); ?></label>
		<?php if(isset($errors) && array_key_exists('taxi_model',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_model'])."</span>";}?>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_owner_name'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
        <input type="text" title="<?php echo __('enter_taxi_owner_name'); ?>" class="required" id="taxi_owner_name" name="taxi_owner_name"  minlength="4" maxlength="30" value="<?php if(isset($postvalue) && array_key_exists('taxi_owner_name',$postvalue)){ echo $postvalue['taxi_owner_name']; }?>" />
        <?php if(isset($errors) && array_key_exists('taxi_owner_name',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_owner_name'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_manufacturer'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
        <input type="text" title="<?php echo __('enter_taxi_manufacturer'); ?>" class="required" id="taxi_manufacturer" name="taxi_manufacturer" minlength="4" maxlength="30"  value="<?php if(isset($postvalue) && array_key_exists('taxi_manufacturer',$postvalue)){ echo $postvalue['taxi_manufacturer']; }?>" />
        <?php if(isset($errors) && array_key_exists('taxi_manufacturer',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_manufacturer'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_colour'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
        <input type="text" title="<?php echo __('enter_taxi_colour'); ?>" minlength="3" maxlength="20" class="required" id="taxi_colour" name="taxi_colour" value="<?php if(isset($postvalue) && array_key_exists('taxi_colour',$postvalue)){ echo $postvalue['taxi_colour']; }?>" />
        <?php if(isset($errors) && array_key_exists('taxi_colour',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_colour'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_motor_expire_date'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
        <input type="text" title="<?php echo __('enter_taxi_motor_expire_date'); ?>" class="required" readonly id="taxi_motor_expire_date" name="taxi_motor_expire_date" value="<?php if(isset($postvalue) && array_key_exists('taxi_motor_expire_date',$postvalue)){ echo $postvalue['taxi_motor_expire_date']; }?>" />
        <?php if(isset($errors) && array_key_exists('taxi_motor_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_motor_expire_date'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_insurance_number'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
        <input type="text" title="<?php echo __('enter_taxi_insurance_number'); ?>" maxlength="45" class="required" id="taxi_insurance_number" name="taxi_insurance_number" value="<?php if(isset($postvalue) && array_key_exists('taxi_insurance_number',$postvalue)){ echo $postvalue['taxi_insurance_number']; }?>" />
        <?php if(isset($errors) && array_key_exists('taxi_insurance_number',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_insurance_number'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_insurance_expire_date'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
        <input type="text" title="<?php echo __('enter_taxi_insurance_expire_date'); ?>" class="required" readonly id="taxi_insurance_expire_date" name="taxi_insurance_expire_date" value="<?php if(isset($postvalue) && array_key_exists('taxi_insurance_expire_date',$postvalue)){ echo $postvalue['taxi_insurance_expire_date']; }?>" />
        <?php if(isset($errors) && array_key_exists('taxi_insurance_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_insurance_expire_date'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_pco_licence_number'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
        <input type="text" title="<?php echo __('enter_taxi_pco_licence_number'); ?>" maxlength="45" class="required" id="taxi_pco_licence_number" name="taxi_pco_licence_number" value="<?php if(isset($postvalue) && array_key_exists('taxi_pco_licence_number',$postvalue)){ echo $postvalue['taxi_pco_licence_number']; }?>" />
        <?php if(isset($errors) && array_key_exists('taxi_pco_licence_number',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_pco_licence_number'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_pco_licence_expire_date'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
        <input type="text" title="<?php echo __('enter_taxi_pco_licence_expire_date'); ?>" class="required" readonly id="taxi_pco_licence_expire_date" name="taxi_pco_licence_expire_date" value="<?php if(isset($postvalue) && array_key_exists('taxi_pco_licence_expire_date',$postvalue)){ echo $postvalue['taxi_pco_licence_expire_date']; }?>" />
        <?php if(isset($errors) && array_key_exists('taxi_pco_licence_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_pco_licence_expire_date'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<?php /*<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_capacity').'('.__('total_passenger').')'; ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php echo __('enter_taxi_capacity'); ?>" class="required onlynumbers" id="taxi_capacity" name="taxi_capacity" value="<?php if(isset($postvalue) && array_key_exists('taxi_capacity',$postvalue)){ echo $postvalue['taxi_capacity']; }?>" minlength="1" maxlength="20" />
              <?php if(isset($errors) && array_key_exists('taxi_capacity',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_capacity'])."</span>";}?>
	</div>
	</td>   	
	</tr> */ ?>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_speed'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php echo __('enter_taxi_speed'); ?>" class="required numbersdots" id="taxi_speed" name="taxi_speed" value="<?php if(isset($postvalue) && array_key_exists('taxi_speed',$postvalue)){ echo $postvalue['taxi_speed']; }?>" minlength="1" maxlength="10" />
              <?php if(isset($errors) && array_key_exists('taxi_speed',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_speed'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	  <tr>
           <td valign="top" width="20%"><label><?php echo __('taxi_min_speed'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('entertaxi_min_speed'); ?>" class="required numbersdots" name="taxi_min_speed" id="taxi_min_speed" value="<?php if(isset($postvalue) && array_key_exists('taxi_min_speed',$postvalue)){ echo $postvalue['taxi_min_speed']; }?>"    minlength="1" maxlength="10" />
              <?php if(isset($errors) && array_key_exists('taxi_min_speed',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_min_speed'])."</span>";}?>

		   </div>
           </td>   	
           </tr> 
	<tr>
           <td valign="top" width="20%"><label><?php echo __('maximum_luggage'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_minimum_luggage'); ?>" class="required onlynumbers" name="minimum_luggage" id="minimum_luggage" value="<?php if(isset($postvalue) && array_key_exists('minimum_luggage',$postvalue)){ echo $postvalue['minimum_luggage']; }?>" maxlength="6"  />
              <?php if(isset($errors) && array_key_exists('minimum_luggage',$errors)){ echo "<span class='error'>".ucfirst($errors['minimum_luggage'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
		
	<!--<tr>
	<td valign="top" width="20%"><label><?php //echo __('taxi_fare_km'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php //echo __('enter_taxi_fare_km'); ?>" id="taxi_fare_km" class="required digits" name="taxi_fare_km" value="<?php //if(isset($postvalue) && array_key_exists('taxi_fare_km',$postvalue)){ echo $postvalue['taxi_fare_km']; }?>" minlength="1" maxlength="20" />
              <?php //if(isset($errors) && array_key_exists('taxi_fare_km',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_fare_km'])."</span>"; } ?>
	</div>
	</td>   	
	</tr>-->
        <input type="hidden" title="<?php echo __('enter_taxi_fare_km'); ?>" id="taxi_fare_km" class="required digits" name="taxi_fare_km" value="1" minlength="1" maxlength="20" />  
            <?php
            if($field_count > 0)
            {
		    for($i=0; $i<$field_count; $i++)
		    { 
		    	$field_name = $additional_fields[$i]['field_name']; ?>
			<tr>
		<td valign="top" width="20%"><label><?php echo $additional_fields[$i]['field_labelname']; ?></label><span class="star">*</span></td>        
		<td>
		<div class="new_input_field">
			<?php if($additional_fields[$i]['field_type'] == 'Textbox')
			{ ?>
	                <input type="text" title="<?php echo __('enter_the').$additional_fields[$i]['field_labelname']; ?>" class="required" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" value="<?php if(isset($postvalue) && array_key_exists($field_name,$postvalue)){ echo $postvalue[$field_name]; }?>" maxlength="20" />
	                <?php }
	                else if($additional_fields[$i]['field_type'] == 'Checkbox')
	                { 
	                
	                        $field_chkvalue = ''; 
	            	 	if(isset($postvalue) && array_key_exists($field_name,$postvalue)){ $field_chkvalue =  $postvalue[$field_name]; }
	            	 	
	                 	$field_value = explode(',',$additional_fields[$i]['field_value']);
	                 	
	                 	foreach($field_value as $key => $value)
	                 	{ ?>
	                <input type="checkbox" title="<?php echo __('enter_the'); ?>"  name="<?php echo $field_name; ?>" value="<?php echo $value; ?>" <?php if($field_chkvalue == $value) { echo 'checked'; } ?> /><?php echo $value; ?>	                 	
	                 	
	                 	<?php  }
	                ?>

	                <?php }
		        else if($additional_fields[$i]['field_type'] == 'Radio')
	                { 
             	            	$field_radvalue = ''; 
	            	 	if(isset($postvalue) && array_key_exists($field_name,$postvalue)){ $field_radvalue =  $postvalue[$field_name]; }
	            	 
	                 	$field_value = explode(',',$additional_fields[$i]['field_value']);
	                 	
	                 	foreach($field_value as $key => $value)
	                 	{ ?>
	                <input type="radio" title="<?php echo __('select_the').$field_name; ?>" class="required" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="<?php echo $value; ?>" <?php if($field_radvalue == $value) { echo 'checked'; } ?> /><?php echo $value; ?>	                 	

	                 	
	                 	<?php }
	                ?>

	                <?php }
	                else if($additional_fields[$i]['field_type'] == 'Select')
	                { 
	            	 $field_selvalue =''; 

	            	 if(isset($postvalue) && array_key_exists($field_name,$postvalue)){ $field_selvalue =  $postvalue[$field_name]; }

	                 	$field_value = explode(',',$additional_fields[$i]['field_value']);
	                 	?>
				<div class="formRight">
				<div class="selector" id="uniform-user_type">
				<span><?php echo __('select_label'); ?></span>
	                 	<select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="required" title="<?php echo __('select_the').$field_name; ?>">
				<option value=""><?php echo __('select_label'); ?></option>
	                 	<?php
	                 	foreach($field_value as $key => $value)
	                 	{ ?>
				<option value="<?php echo $value; ?>" <?php  if($field_selvalue == $value) { echo 'selected=selected'; } ?> ><?php echo ucfirst($value); ?></option>
	                 	<?php }
	                 	?>
	                 	</select>
	                 	</div>
	                 	</div>
	                <?php 	
			}
	                ?>
	                <label for="<?php echo $field_name; ?>" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the').$field_name; ?></label>		
	                <?php
	               	 
		        if(isset($errors) && array_key_exists($field_name,$errors)){ echo "<span class='error'>".ucfirst($errors[$field_name])."</span>";}?>
	                </div>
	                </td>
	                </tr>
		   <?php }
            }
            ?>    

	<?php if(($_SESSION['user_type'] !='M') ||($_SESSION['user_type'] !='C'))
	{ ?>
	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('country',$postvalue)){ $field_type =  $postvalue['country']; } ?>
	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
              <select  title="<?php echo __('select_the_country'); ?>" class="required" <?php if($_SESSION['user_type']== 'C' || $_SESSION['user_type']== 'M' ) { ?> name="country" id="country" disabled<?php }else{ ?> name="country" id="country" <?php } ?>>
              <option value=""><?php echo __('select_label'); ?></option>
              <?php foreach($country_details as $country_list) { ?>
              <option value="<?php echo $country_list['country_id']; ?>" <?php if($field_type == $country_list['country_id']) { echo 'selected=selected'; }elseif($field_type =='' && $country_list['country_id'] == DEFAULT_COUNTRY) { echo 'selected=selected'; } ?>><?php echo ucfirst($country_list['country_name']); ?></option>
              <?php } ?>
              </select>
			<?php if($_SESSION['user_type']== 'C' || $_SESSION['user_type']== 'M' ) { ?> <input type="hidden" name="country" id="country" value="<?php echo ($field_type!="")?$field_type:DEFAULT_COUNTRY; ?>" > <?php } ?>
        </div>
	</div>
	     <label for="country" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_country'); ?></label>	
              <?php if(isset($errors) && array_key_exists('country',$errors)){ echo "<span class='error'>".ucfirst($errors['country'])."</span>";}?>

	</td>   	
	</tr>

	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('state',$postvalue)){ $field_type =  $postvalue['state']; }
	//else{ $field_type = $state_company;} 
	else{ $field_type = DEFAULT_STATE;} ?>
	<td valign="top" width="20%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="state_list">
		<select name="state" id="state" onchange="change_city_drop();" class="required" title="<?php echo __('select_the_state'); ?>">
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($state_details as $state_list) {  ?>
		<option value="<?php echo $state_list['state_id']; ?>" <?php if($field_type == $state_list['state_id']) { ?> selected <?php } ?> ><?php echo ucfirst($state_list["state_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
		<label for="state" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_state'); ?></label>	
              <?php if(isset($errors) && array_key_exists('state',$errors)){ echo "<span class='error'>".ucfirst($errors['state'])."</span>"; }?>
        </td>      
	</tr>
	
	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('city',$postvalue)){ $field_type =  $postvalue['city']; }
	//else{ $field_type = $city_company;}
	else{ $field_type = DEFAULT_CITY; } ?>
	<td valign="top" width="20%"><label><?php echo __('city_label'); ?></label><span class="star">*</span></td><td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="city_list">
		<select name="city" id="city" --onchange="change_company();" class="required" title="<?php echo __('select_the_city'); ?>">
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($city_details as $city_list) {  ?>
		<option value="<?php echo $city_list['city_id']; ?>" <?php if($field_type == $city_list['city_id']) { echo 'selected=selected'; }  ?> ><?php echo ucfirst($city_list["city_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
		<label for="city" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_city'); ?></label>	
              <?php if(isset($errors) && array_key_exists('city',$errors)){ echo "<span class='error'>".ucfirst($errors['city'])."</span>"; }?>
	</tr>
	<?php } else { ?>
		<input type="hidden" name="country" id="country" value="<?php echo $_SESSION['country_id']; ?>">
		<input type="hidden" name="state" id="state" value="<?php echo $_SESSION['state_id']; ?>">

		<input type="hidden" name="city" id="city" value="<?php echo $_SESSION['city_id']; ?>">
	
	<?php } 
	?>
	<tr>
		<td valign="top" width="20%"><label><?php echo __('taxi_image_label'); ?></label><span class="star">*</span></td>   
		<td>
			<div class="new_input_field">
				<input type="file"  class="required imageonly" name="taxi_image" id="taxi_image" title="<?php echo __('select_taxi_image'); ?>" value="<?php if(isset($postvalue) && array_key_exists('taxi_image',$postvalue)){ echo $postvalue['taxi_image']; }?>">
				
			</div>
			<?php if(isset($errors) && array_key_exists('taxi_image',$errors)){ echo "<span class='error'>".ucfirst($errors['taxi_image'])."</span>";}?>
		</td>
	    </tr>
	 
        <table border="0" cellpadding="0" cellspacing="0"  class="form" id="sub_add" width="100%">
        <tr><td valign="top" width="20%"></td><td>&nbsp;&nbsp;&nbsp;<a id="add_moreimage" href="#">Add More Image</a></td></tr>   
        </table>
        <table border="0" cellpadding="5" cellspacing="0" width="100%">		
			<tr>
				<td class="empt_cel">&nbsp;</td>
				<td  class="star">*<?php echo __('required_label'); ?></td>
			</tr> 
			<tr>
				<td style="width:20%;">&nbsp;</td>
				<td colspan="2">
					<div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.location='<?php echo URL_BASE."manage/taxi"; ?>'" /></div>
                                        <div class="new_button">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addtaxi" id="submit_addtaxi" title="<?php echo __('btn_submit' );?>" /></div>
					<div class="new_button">   <input type="reset" onclick="change_state('<?php echo DEFAULT_COUNTRY; ?>','<?php echo DEFAULT_STATE; ?>');change_city('<?php echo DEFAULT_COUNTRY; ?>','<?php echo DEFAULT_STATE; ?>','<?php echo DEFAULT_CITY; ?>')" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
				</td>
			</tr> 
		</table>	                         
	</table>	                         
		</table>
        </form>
        </div>
    </div>
</div> 


<script type="text/javascript">
	$(document).ready(function(){
		$("#taxi_no").focus();
			change_type();	
			change_state('','');	
			change_city('','','');
		//date time picker
		$("#taxi_insurance_expire_date").datetimepicker( {
		showTimepicker:true,
		showSecond: true,
		timeFormat: 'hh:mm:ss',
		dateFormat: 'yy-mm-dd',
		stepHour: 1,
		stepMinute: 1,
		minDateTime : new Date(),
		stepSecond: 1
		} );

		$('#taxi_motor_expire_date, #taxi_pco_licence_expire_date').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true, changeYear: true, yearRange: new Date().getFullYear()+':+100',minDate: 0,maxDate: new Date(2100, 1,18) });
		
	});


	jQuery(document).ready(function () {
		jQuery('#addtaxi_form').submit(function(){

			//var validator=jQuery("#addtaxi_form").validate({});
		});

		$.validator.addMethod( "imageonly", function(value,element){
		var pathLength = value.length; var lastDot = value.lastIndexOf( "."); var fileType = value.substring(lastDot,pathLength).toLowerCase(); return this.optional(element) || fileType.match(/(?:.jpg|.jpeg|.png)$/) }, "<?php echo __('please_upload_image_fileonly'); ?>");

			$("#submit_addtaxi").click(function(){
			var flag = true;
					var validator=jQuery("#addtaxi_form").valid({});
					
							$("input:file").each(function(){ 
								   var fld=$(this).val();
								   var get_id= $(this).attr('id').replace('cpicture','');
								   if(fld!=''){
								   if(!/(\.png|\.jpg|\.jpeg)$/i.test(fld)) {
							   $("#error"+get_id).show();
							   
							   flag=false;
					   }else{
				 $("#error"+get_id).hide();
				 
				flag=true;
			} 
			}
			});
		   return flag;   
		});
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
			change_city_drop();
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    });
    
	/** Function to get taxi speed and minimum speed details **/
	function getTaxiSpeed(modelId)
	{
		$.ajax({
			url:"<?php echo URL_BASE;?>add/getTaxiSpeed",
			type:"post",
			data:"modelId="+modelId,
			success:function(data){
				var res = $.parseJSON(data);
				$.each(res, function(i, resval) {
					$('#taxi_speed').val(resval.taxi_speed);
					$('#taxi_speed').attr('readonly','readonly');
					$('#taxi_min_speed').val(resval.taxi_min_speed);
					$('#taxi_min_speed').attr('readonly','readonly');
				});
				
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
	}

	function getcountry(company_id)
	{
		// get taxi models
		get_models(company_id);	
		$.ajax({
			url:"<?php echo URL_BASE;?>add/getcountry",
			type:"get",
			data:"company_id="+company_id,
			success:function(data){
				var res = data.split("~");
				$('#country').html(res[0]);
				change_country(res[1],res[2]);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
	}

	function get_models(company_id)
	{
		if(company_id == ''){		
			$("#taximodels").html("<select><option value=''><?php echo __('select_company'); ?></option></select>");
			return true;
		}else{
		
			$.ajax({
				url:"<?php echo URL_BASE;?>add/getmodelfare",
				type:"get",
				data:"company_id="+company_id,
				success:function(data){
					$("#taximodels").html(data);
					return true;
				},
				error:function(data)
				{
					//alert(cid);
				}
			});
		}
	}

	function change_country(state_id,city_id)
	{
		var countryid= $("#country").val();

		$.ajax({
			url:"<?php echo URL_BASE;?>add/getlist_state",
			type:"get",
			data:"country_id="+countryid+"&state_id="+state_id,
			success:function(data){
				$('#state_list').html(data);
				change_city(countryid,state_id,city_id);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});
	}

	
 
	function change_city_drop(){
		
      		var countryid= $("#country").val();
      		var stateid= $("#state").val();
      		var cityid= $("#city").val();
		$.ajax({
			url:"<?php echo URL_BASE;?>add/getassigntaxilist",
			type:"get",
			data:"country_id="+countryid+"&state_id="+stateid+"&city_id="+cityid,
			success:function(data){

			$('#city_list').html();
			$('#city_list').html(data);
			//change_driverinfo();
			//change_taxiinfo();
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    } 
   

	/*function change_state(country_id,state_id)
	{

		var countryid= $("#country").val();
		var stateid= $("#state").val();
		if(country_id != '' && state_id != '') {
			countryid = country_id;
			stateid= state_id;
		}

		$.ajax({
			url:"<?php echo URL_BASE;?>add/getlist_state",
			type:"get",
			data:"country_id="+countryid+"&state_id="+stateid,
			success:function(data){

				$('#state_list').html();
				$('#state_list').html(data);
				change_city_drop();
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
		
	}
	function change_city(country_id,state_id,city_id)
	{
		var stateid= $("#state").val();
		var countryid= $("#country").val();
		var cityid= $("#city").val();
		if(country_id != '' && state_id != '' && city_id != '') {
			countryid = country_id;
			stateid = state_id;
			cityid = city_id;
		}
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
    
	}*/

function change_state(country_id,state_id)
{

	var countryid= $("#country").val();
	var stateid= $("#state").val();
	if(country_id != '' && state_id != '') {
		countryid = country_id;
		stateid= state_id;
	}

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
    

function change_city(country_id,state_id,city_id)
{

	var stateid= $("#state").val();
	var countryid= $("#country").val();
	var cityid= $("#city").val();
	if(country_id != '' && state_id != '' && city_id != '') {
		countryid = country_id;
		stateid = state_id;
		cityid = city_id;
	}
		
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

	function change_company()
	{

      		var countryid= $("#country").val();
      		var stateid= $("#state").val();
      		var city_id= $("#city").val();
      		var company_name = $("#company_name").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getcompanylist",
			type:"get",
			data:"country_id="+countryid+"&state_id="+stateid+"&city_id="+city_id+"&company_name="+company_name,
			success:function(data){

			$('#taxicompany_list').html();
			$('#taxicompany_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}	
    		});
	}

	function change_type()
	{

		var motorid= $("#taxi_type").val();
		var modelid= $("#taxi_model").val();

		if(modelid ==null)
		{
			modelid="";
		}

		$.ajax({
			url:"<?php echo URL_BASE;?>add/getmodellist",
			type:"get",
			data:"motor_id="+motorid+"&model_id="+modelid,
			success:function(data){
			$('#model_list').html();
			$('#model_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
		
	}

	$("#taxi_type").change(function() {

      		var motorid= $("#taxi_type").val();
      		var modelid= $("#model_id").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getmodellist",
			type:"get",
			data:"motor_id="+motorid+"&model_id="+modelid,
			success:function(data){
			$('#model_list').html();
			$('#model_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
	});    


	$(function(){ 
		$('#add_moreimage').click(function(){  
		var newRow = $("#sub_add tr").length+1; 
		if(newRow <= 11) {
			 $("#sub_add").append('<tr id="row_'+newRow+'"><td width="20%"></td><td width="20%"><input type="file" style="margin-bottom:5px;" name="size[]" id="cpicture'+newRow+'"  class="required" title="Select the image"><br><span id="error'+newRow+'" style="display:none;color:red;font-size:11px;">*Only jpeg, jpg or png images</span><td><a href="javascript:;" onClick="return removetr_contact('+newRow+');">Delete</a></td></tr>');     
		}
		return false;	
		});
	});  

	function removetr_contact(rowid) {
		var r1 = "row_"+rowid;
		$("#sub_add tr").each(function () {    
		if(r1==$(this).attr('id')) {
		$(this).remove();
		}   
		});
		return false;
	}
	
	function getid(rowid) {
		var r2 = "row_"+rowid;
	}     
</script>
