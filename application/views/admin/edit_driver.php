<?php defined('SYSPATH') OR die("No direct access allowed.");?>

<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimepicker/jquery-ui.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimepicker/jquery-1.9.1.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimepicker/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">   
       <form name="editpackage_form" id="editpackage_form" class="form" action="" method="post" enctype="multipart/form-data">
       <table border="0" cellpadding="5" cellspacing="0" width="100%">            

	<tr>
            <td><h2 class="tab_sub_tit"><?php echo ucfirst(__('personalinform')); ?></h2></td>
	<td></td>	          
	</tr>	
	<?php 
		if(isset($company_details[0]['name']) && !array_key_exists('firstname',$postvalue)){
			$firstname = $company_details[0]['name'];
		}else{
			if(isset($postvalue['firstname'])){
				$firstname = $postvalue['firstname'];
			}else{
				$firstname = "";
			}
		}
		?>  
	<tr>
	<td valign="top" width="25%"><label><?php echo __('firstname'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45" minlength="4"  title="<?php echo __('enterfirstname'); ?>" id="firstname" name="firstname" value="<?php echo $firstname; ?>" />
              <?php if(isset($errors) && array_key_exists('firstname',$errors)){ echo "<span class='error'>".ucfirst($errors['firstname'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	<?php 
		if(isset($company_details[0]['lastname']) && !array_key_exists('lastname',$postvalue)){
			$lastname = $company_details[0]['lastname'];
		}else{
			if(isset($postvalue['lastname'])){
				$lastname = $postvalue['lastname'];
			}else{
				$lastname = "";
			}
		}
		?>  
	<tr>
	<td valign="top" width="25%"><label><?php echo __('lastname'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45" minlength="1" title="<?php echo __('enterlastname'); ?>" id="lastname" name="lastname" value="<?php echo $lastname; ?>" />
              <?php if(isset($errors) && array_key_exists('lastname',$errors)){ echo "<span class='error'>".ucfirst($errors['lastname'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	<?php 
		if(isset($company_details[0]['email']) && !array_key_exists('email',$postvalue)){
			$email = $company_details[0]['email'];
		}else{
			if(isset($postvalue['email'])){
				$email = $postvalue['email'];
			}else{
				$email = "";
			}
		}
		?>  
	<tr>
	<td valign="top" width="25%"><label><?php echo __('email'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  title="<?php echo __('enteremailaddress'); ?>" id="email" name="email" value="<?php echo $email; ?>" maxlength="100" /><br>
              <?php if(isset($errors) && array_key_exists('email',$errors)){ echo "<span class='error'>".ucfirst($errors['email'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	<?php 
		if(isset($company_details[0]['org_password']) && !array_key_exists('password',$postvalue)){
			$password = $company_details[0]['org_password'];
		}else{
			if(isset($postvalue['password'])){
				$password = $postvalue['password'];
			}else{
				$password = "";
			}
		}
		?>  
	<?php /*<tr>
	<td valign="top" width="25%"><label><?php echo __('password'); ?></label><span class="star">*</span></td>        
	<td>
	   <div class="new_input_field">
		  <input type="password" title="<?php echo __('enterpassword'); ?>" name="password" id="password" value="<?php echo $password; ?>"  minlength="4" maxlength="20" />
		  <?php if(isset($errors) && array_key_exists('password',$errors)){ echo "<span class='error'>".ucfirst($errors['password'])."</span>";}?>
	   </div>
	</td>
	</tr> */ ?>
	<?php 
		if(isset($company_details[0]['org_password']) && !array_key_exists('repassword',$postvalue)){
			$org_password = $company_details[0]['org_password'];
		}else{
			if(isset($postvalue['repassword'])){
				$org_password = $postvalue['repassword'];
			}else{
				$org_password = "";
			}
		}
		?>  
	<?php /*<tr>
	<td valign="top" width="25%"><label><?php echo __('confirm_password_label'); ?></label><span class="star">*</span></td>        
	<td>
		<div class="new_input_field">
		  <input type="password" title="<?php echo __('entertheconfirmpassword'); ?>" name="repassword" id="repassword" value="<?php echo $org_password; ?>"  minlength="2" maxlength="20" />
		  <?php if(isset($errors) && array_key_exists('repassword',$errors)){ echo "<span class='error'>".ucfirst($errors['repassword'])."</span>";}?>
	   </div>
	</td>   	
	</tr> */ ?>
<?php 
		if(isset($company_details[0]['gender']) && !array_key_exists('gender',$postvalue)){
			$gender = $company_details[0]['gender'];
		}else{
			if(isset($postvalue['gender'])){
				$gender = $postvalue['gender'];
			}else{
				$gender = "";
			}
		}
		?>  
	<tr>
	<td valign="top" width="25%"><label><?php echo __('gender'); ?></label><span class="star">*</span></td>        
	<td>
		<?php  $field_chkvalue =  $gender; ?>
	<div class="new_input_field">
              <input type="radio"   title="<?php echo __('selectthegender'); ?>" name="gender" value="Male" <?php if($field_chkvalue =='Male') { echo 'checked'; } ?> /><?php echo __('male'); ?>
              <input type="radio"   title="<?php echo __('selectthegender'); ?>" name="gender" value="Female" <?php if($field_chkvalue =='Female') { echo 'checked'; } ?>/><?php echo __('female'); ?>
              <?php if(isset($errors) && array_key_exists('gender',$errors)){ echo "<span class='error'>".ucfirst($errors['gender'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	<?php 
		if(isset($company_details[0]['dob']) && !array_key_exists('dob',$postvalue)){
			$dob = $company_details[0]['dob'];
		}else{
			if(isset($postvalue['dob'])){
				$dob = $postvalue['dob'];
			}else{
				$dob = "";
			}
		}
		?>  

	<tr>
	<td valign="top" width="25%"><label><?php echo __('date_of_birth'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  readonly title="<?php echo __('selectdate_of_birth'); ?>" id="dob" name="dob" value="<?php echo $dob; ?>"  />
              <?php if(isset($errors) && array_key_exists('dob',$errors)){ echo "<span class='error'>".ucfirst($errors['dob'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	           		
	<?php 
		if(isset($company_details[0]['driver_license_id']) && !array_key_exists('driver_license_id',$postvalue)){
			$driver_license_id = $company_details[0]['driver_license_id'];
		}else{
			if(isset($postvalue['driver_license_id'])){
				$driver_license_id = $postvalue['driver_license_id'];
			}else{
				$driver_license_id = "";
			}
		}
		?>  
	<tr>
	<td valign="top" width="25%"><label><?php echo __('driver_license_id'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45"  title="<?php echo __('enter_driver_license_id'); ?>" id="driver_license_id" name="driver_license_id" value="<?php echo $driver_license_id; ?>" />
              <?php if(isset($errors) && array_key_exists('driver_license_id',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_license_id'])."</span>";}?>
	</div>
	<?php 
		if(isset($company_details[0]['driver_license_expire_date']) && !array_key_exists('driver_license_expire_date',$postvalue)){
			$driver_license_expire_date = $company_details[0]['driver_license_expire_date'];
		}else{
			if(isset($postvalue['driver_license_expire_date'])){
				$driver_license_expire_date = $postvalue['driver_license_expire_date'];
			}else{
				$driver_license_expire_date = "";
			}
		}
		?>  
	</td>
        </tr>
        <tr>
	<td valign="top" width="25%"><label><?php echo __('driver_license_expire_date'); ?></label><span class="star">*</span></td>
	<?php /*echo isset($driver_info_details[0]['driver_license_expire_date']) &&!array_key_exists('driver_license_expire_date',$postvalue)? trim($driver_info_details[0]['driver_license_expire_date']):$postvalue['driver_license_expire_date']; */ 
	
	if(isset($driver_info_details[0]['driver_license_expire_date']) && !array_key_exists('driver_license_expire_date',$postvalue)){
		$driver_license_expire_date = $driver_info_details[0]['driver_license_expire_date'];
	}else{
		if(isset($postvalue['driver_license_expire_date'])){
			$driver_license_expire_date = $postvalue['driver_license_expire_date'];
		}else{
			$driver_license_expire_date = ""; 
		}
	}
	
	if(isset($driver_info_details[0]['driver_pco_license_number']) && !array_key_exists('driver_pco_license_number',$postvalue)){
		$driver_pco_license_number = $driver_info_details[0]['driver_pco_license_number'];
	}else{
		if(isset($postvalue['driver_pco_license_number'])){
			$driver_pco_license_number = $postvalue['driver_pco_license_number'];
		}else{
			$driver_pco_license_number = "";
		}
	}
	
	if(isset($driver_info_details[0]['driver_pco_license_expire_date']) && !array_key_exists('driver_pco_license_expire_date',$postvalue)){
		$driver_pco_license_expire_date = $driver_info_details[0]['driver_pco_license_expire_date'];
	}else{
		if(isset($postvalue['driver_pco_license_expire_date'])){
			$driver_pco_license_expire_date = $postvalue['driver_pco_license_expire_date'];
		}else{
			$driver_pco_license_expire_date = "";
		}
	}
	
	if(isset($driver_info_details[0]['driver_insurance_number']) && !array_key_exists('driver_insurance_number',$postvalue)){
		$driver_insurance_number = $driver_info_details[0]['driver_insurance_number'];
	}else{
		if(isset($postvalue['driver_insurance_number'])){
			$driver_insurance_number = $postvalue['driver_insurance_number'];
		}else{
			$driver_insurance_number = "";
		}
	}
	
	if(isset($driver_info_details[0]['driver_insurance_expire_date']) && !array_key_exists('driver_insurance_expire_date',$postvalue)){
		$driver_insurance_expire_date = $driver_info_details[0]['driver_insurance_expire_date'];
	}else{
		if(isset($postvalue['driver_insurance_expire_date'])){
			$driver_insurance_expire_date = $postvalue['driver_insurance_expire_date'];
		}else{
			$driver_insurance_expire_date = "";
		}
	}
	
	if(isset($driver_info_details[0]['driver_national_insurance_number']) && !array_key_exists('driver_national_insurance_number',$postvalue)){
		$driver_national_insurance_number = $driver_info_details[0]['driver_national_insurance_number'];
	}else{
		if(isset($postvalue['driver_national_insurance_number'])){
			$driver_national_insurance_number = $postvalue['driver_national_insurance_number'];
		}else{
			$driver_national_insurance_number = "";
		}
	}
	
	if(isset($driver_info_details[0]['driver_national_insurance_expire_date']) && !array_key_exists('driver_national_insurance_expire_date',$postvalue)){
		$driver_national_insurance_expire_date = $driver_info_details[0]['driver_national_insurance_expire_date'];
	}else{
		if(isset($postvalue['driver_national_insurance_expire_date'])){
			$driver_national_insurance_expire_date = $postvalue['driver_national_insurance_expire_date'];
		}else{
			$driver_national_insurance_expire_date = "";
		}
	}
	
	
	?>
	<td>
	<div class="new_input_field">
	  <input type="text"  maxlength="30" readonly title="<?php echo __('enter_driver_license_expire_date'); ?>" id="driver_license_expire_date" name="driver_license_expire_date" value="<?php echo $driver_license_expire_date; ?>" />
	  <?php if(isset($errors) && array_key_exists('driver_license_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_license_expire_date'])."</span>";}?>
	</div>
	</td>
	</tr>
	
	<tr>
	<td valign="top" width="25%"><label><?php echo __('driver_pco_license_number'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45"  title="<?php echo __('enter_driver_pco_license_no'); ?>" id="driver_pco_license_number" name="driver_pco_license_number" value="<?php echo $driver_pco_license_number; ?>" />
              <?php if(isset($errors) && array_key_exists('driver_pco_license_number',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_pco_license_number'])."</span>";}?>
	</div>
	</td>
        </tr>
        <tr>
	<td valign="top" width="25%"><label><?php echo __('driver_pco_license_expire_date'); ?></label><span class="star">*</span></td>
	<td>
	<div class="new_input_field">
	  <input type="text"  maxlength="30" readonly title="<?php echo __('enter_driver_pco_license_expire_date'); ?>" id="driver_pco_license_expire_date" name="driver_pco_license_expire_date" value="<?php echo $driver_pco_license_expire_date; ?>" />
	  <?php if(isset($errors) && array_key_exists('driver_pco_license_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_pco_license_expire_date'])."</span>";}?>
	</div>
	</td>	
	</tr>
	
	<tr>
	<td valign="top" width="25%"><label><?php echo __('driver_insurance_number'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45"  title="<?php echo __('enter_driver_insurance_no'); ?>" id="driver_insurance_number" name="driver_insurance_number" value="<?php echo $driver_insurance_number; ?>" />
              <?php if(isset($errors) && array_key_exists('driver_insurance_number',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_insurance_number'])."</span>";}?>
	</div>
	</td>
        </tr>
        <tr>
	<td valign="top" width="25%"><label><?php echo __('driver_insurance_expire_date'); ?></label><span class="star">*</span></td>
	<td>
	<div class="new_input_field">
	  <input type="text"  maxlength="30" readonly title="<?php echo __('enter_driver_insurance_expire_date'); ?>" id="driver_insurance_expire_date" name="driver_insurance_expire_date" value="<?php echo $driver_insurance_expire_date; ?>" />
	  <?php if(isset($errors) && array_key_exists('driver_insurance_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_insurance_expire_date'])."</span>";}?>
	</div>
	</td>	
	</tr>
	
	<tr>
	<td valign="top" width="25%"><label><?php echo __('driver_national_insurance_number'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45" title="<?php echo __('enter_driver_national_insurance_no'); ?>" id="driver_national_insurance_number" name="driver_national_insurance_number" value="<?php echo $driver_national_insurance_number; ?>" />
              <?php if(isset($errors) && array_key_exists('driver_national_insurance_number',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_national_insurance_number'])."</span>";}?>
	</div>
	</td>
        </tr>
        <tr>
	<td valign="top" width="25%"><label><?php echo __('driver_national_insurance_expire_date'); ?></label><span class="star">*</span></td>
	<td>
	<div class="new_input_field">
	  <input type="text"  maxlength="30" readonly title="<?php echo __('enter_driver_national_insurance_expire_date'); ?>" id="driver_national_insurance_expire_date" name="driver_national_insurance_expire_date" value="<?php echo $driver_national_insurance_expire_date; ?>" />
	  <?php if(isset($errors) && array_key_exists('driver_national_insurance_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_national_insurance_expire_date'])."</span>";}?>
	</div>
	</td>	
	</tr>
	<?php
	if(isset($company_details[0]['phone']) && !array_key_exists('phone',$postvalue)){
		$phone = $company_details[0]['phone'];
	}else{
		if(isset($postvalue['phone'])){
			$phone = $postvalue['phone'];
		}else{
			$phone = "";
		}
	}
	?>
	<tr>
	<td valign="top" width="25%"><label><?php echo __('mobile'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field input-mob-box mobile_codetxt">
              <input type="text" title="<?php echo __('entermobileno'); ?>" id="phone" name="phone" value="<?php echo $phone; ?>" minlength="7" maxlength="16" />
              <span class="unit_mobile_code" id="mobile_code"></span>
              <input type="hidden" name="telephone_code" id="hid_mobile_code" value="">
              <?php if(isset($errors) && array_key_exists('phone',$errors)){ echo "<span class='error'>".ucfirst($errors['phone'])."</span>";}?>
	</div>
	</td>	
	</tr>
	
	<?php 
	$brand_type = "";
	if($_SESSION['user_type'] =='A' || $_SESSION['user_type'] =='DA') {
		
   if(isset($company_details[0]['company_brand_type']) && !array_key_exists('brand_type',$postvalue)){
		$brand_type = $company_details[0]['company_brand_type'];
	}else{
		if(isset($postvalue['brand_type'])){
			$brand_type = $postvalue['brand_type'];
		}else{
			$brand_type = "";
		}
	}
	
		?>	
	<tr>
		<td valign="top" width="25%"><label><?php echo __('brand'); ?></label><span class="star">*</span></td>        
		<td>
			<div class="selector">
					<?php echo ($brand_type == "M") ? __('Multy') : __('Single'); ?>
					<input type="hidden" name="brand_type" value="<?php echo $brand_type; ?>" />
			</div>
		</td>
	</tr> 
 <?php
	if(isset($company_details[0]['company_id']) && !array_key_exists('company_name',$postvalue)){
		$company_id = $company_details[0]['company_id'];
	}else{
		if(isset($postvalue['company_name'])){
			$company_id = $postvalue['company_name'];
		}else{
			$company_id = "";
		}
	}

	?>	
		
	<tr id="companyRow">
	<?php  $field_type = $company_id; ?>
	<td valign="top" width="25%"><label><?php echo __('taxicompany'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="taxicompany_list">
		<select name="company_name" id="company_name" onchange="getcountry(this.value,'','')">
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($taxicompany_details as $company_list) {  ?>
			<?php if(isset($company_list['company_brand_type']) && $company_list['company_brand_type'] == 'M') { ?>
			<option value="<?php echo $company_list['cid']; ?>" <?php if($field_type == $company_list['cid']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($company_list["company_name"]); ?></option>
			<?php } ?>
		<?php } ?>
		</select>
	</div>	
		</div></div>
              <?php if(isset($errors) && array_key_exists('company_name',$errors)){ echo "<span class='error'>".ucfirst($errors['company_name'])."</span>"; }?>
        </td>      
	</tr>	
	<?php if($brand_type == "S") { ?>
			<input type="hidden" name="company_name" value="<?php echo $company_id; ?>" />
	<?php } ?>
	<?php } else { ?> 
	<tr>
	<td valign="top" width="25%"></td>
	<td>
	<div class="new_input_field">
	<input type="hidden" name="company_name" id="company_name" value="<?php echo $_SESSION['company_id']; ?>">
		<?php if(isset($errors) && array_key_exists('company_name',$errors)){ echo "<span class='error'>".ucfirst($errors['company_name'])."</span>"; } ?>
	 </div>
	 </td>
	 </tr>
	<?php } ?>
	
	<?php if($_SESSION['user_type'] !='M')
	{ ?>
	 <?php
	if(isset($company_details[0]['login_country']) && !array_key_exists('country',$postvalue)){
		$country = $company_details[0]['login_country'];
	}else{
		if(isset($postvalue['country'])){
			$country = $postvalue['country'];
		}else{
			$country = "";
		}
	}
	?>			
<tr>
	<?php  $field_type =  $country; ?>
	<td valign="top" width="25%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
             <select  <?php if($_SESSION['user_type']== 'C' || $_SESSION['user_type']== 'M' ) { ?> name="countrys" disabled <?php }else{ ?> name="country" id="country" <?php } ?>>
              <option value=""><?php echo __('select_label'); ?></option>
              <?php foreach($country_details as $country_list) { ?>
              <option value="<?php echo $country_list['country_id']; ?>" <?php if($field_type == $country_list['country_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($country_list['country_name']); ?></option>
              <?php } ?>
              </select>
		<?php if($_SESSION['user_type']== 'C' || $_SESSION['user_type']== 'M' ) { ?> <input type="hidden" name="country" id="country" value="<?php echo $field_type; ?>"> <?php } ?>
        </div>
	</div>
              <?php if(isset($errors) && array_key_exists('country',$errors)){ echo "<span class='error'>".ucfirst($errors['country'])."</span>";}?>

	</td>   	
	</tr>
	 <?php
	if(isset($company_details[0]['login_state']) && !array_key_exists('state',$postvalue)){
		$login_state = $company_details[0]['login_state'];
	}else{
		if(isset($postvalue['state'])){
			$login_state = $postvalue['state'];
		}else{
			$login_state = "";
		}
	}
	?>			

	<tr>
	<?php $field_type =  $login_state; ?>
	<td valign="top" width="25%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="state_list">
		<select name="state" id="state" onchange="change_city_drop();"><?php /*//change_city(); */ ?>
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($state_details as $state_list) {  ?>
		<option value="<?php echo $state_list['state_id']; ?>" <?php if($field_type == $state_list['state_id']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($state_list["state_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
              <?php if(isset($errors) && array_key_exists('state',$errors)){ echo "<span class='error'>".ucfirst($errors['state'])."</span>"; }?>
        </td>      
	</tr>
	 <?php
	if(isset($company_details[0]['login_city']) && !array_key_exists('city',$postvalue)){
		$city = $company_details[0]['login_city'];
	}else{
		if(isset($postvalue['city'])){
			$city = $postvalue['city'];
		}else{
			$city = "";
		}
	}
	?>		

	<tr>
	<?php $field_type =  $city; ?>
	<td valign="top" width="25%"><label><?php echo __('city_label'); ?></label><span class="star">*</span></td>        
	<td>
		<div class="formRight">
		<div class="selector" id="uniform-user_type">
		<span><?php echo __('select_label'); ?></span>
		<div id="city_list">
			<select name="city" id="city" --onchange="change_company();">
			<option value=""><?php echo __('select_label'); ?></option>
			<?php
			foreach($city_details as $city_list) {  ?>
			<option value="<?php echo $city_list['city_id']; ?>" <?php if($field_type == $city_list['city_id']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($city_list["city_name"]); ?></option>
			<?php	} ?>
			</select>
		</div>	
		</div>


		</div>
		 <?php if(isset($errors) && array_key_exists('city',$errors)){ echo "<span class='error'>".ucfirst($errors['city'])."</span>"; }?>
	</td>   	
	</tr>
	<?php } 
	else { ?>
		<input type="hidden" name="country" id="country" value="<?php echo $_SESSION['country_id']; ?>">
		<input type="hidden" name="state" id="state" value="<?php echo $_SESSION['state_id']; ?>">
		<input type="hidden" name="city" id="city" value="<?php echo $_SESSION['city_id']; ?>">
	
	<?php } 
	?>
	 <?php
	if(isset($company_details[0]['booking_limit']) && !array_key_exists('booking_limit',$postvalue)){
		$booking_limit = $company_details[0]['booking_limit'];
	}else{
		if(isset($postvalue['booking_limit'])){
			$booking_limit = $postvalue['booking_limit'];
		}else{
			$booking_limit = "";
		}
	}
	?>		
	<tr>
	<td valign="top" width="25%"><label><?php echo __('booking_limit_per_day'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php echo __('enter_booking_limit'); ?>" id="booking_limit" maxlength="5" name="booking_limit" value="<?php echo $booking_limit; ?>" />
              <?php if(isset($errors) && array_key_exists('booking_limit',$errors)){ echo "<span class='error'>".ucfirst($errors['booking_limit'])."</span>";}?>
	</div>
	</td>   	
	</tr>	
	<?php
	if(isset($company_details[0]['address']) && !array_key_exists('address',$postvalue)){
		$address = $company_details[0]['address'];
	}else{
		if(isset($postvalue['address'])){
			$address = $postvalue['address'];
		}else{
			$address = "";
		}
	}
	?>		
	<tr>
	<td valign="top" width="25%"><label><?php echo __('address'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <textarea name="address" id="address" class="required" title="<?php echo __('enteraddress'); ?>" rows="7" cols="35"><?php echo $address; ?></textarea>
              
              <?php if(isset($errors) && array_key_exists('address',$errors)){ echo "<span class='error'>".ucfirst($errors['address'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	<tr>
		<td><label><?php echo __('profile_picture'); ?></label></td>
		<td>
			<?php if(!empty($company_details[0]['profile_picture']) && file_exists(DOCROOT.'public/'.UPLOADS.'/driver_image/'.$company_details[0]['profile_picture'])){ ?>
					<img src="<?php echo URL_BASE.'public/'.UPLOADS.'/driver_image/'.$company_details[0]['profile_picture'];?>" height="140px" width="140px" class="img-polaroid">
				<?php }else{ ?>
					<img height="140px" width="140px" src="<?php echo URL_BASE;?>public/<?php echo UPLOADS; ?>/driver_image/no-image.jpg" class="img-polaroid">
			<?php } ?>
		</td>
	</tr>
	
	<tr>
		<td></td>
		<td>
		<input type="file" name="profile_picture"  class="required imageonly" id="profile_picture" value="<?php echo $company_details[0]['profile_picture'];?>"/>
			  <br><span style="color:red;" class="signup_error" id="profile_picture_error">
				  <?php echo array_key_exists("profile_picture",$errors)?($errors["profile_picture"]):"";?></span>
			  <input type="hidden" name="id" value="<?php echo $company_details[0]['id'] ?>" />
		</td>
	</tr>		
<tr>
	<td>&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
                            <div class="new_button"><input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                            <div class="new_button"><input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_driver" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button"><input type="reset" onclick="change_state('<?php echo isset($company_details[0]['login_country']) ? $company_details[0]['login_country'] : ''; ?>','<?php echo isset($company_details[0]['login_state']) ? $company_details[0]['login_state'] : ''; ?>');change_city('<?php echo isset($company_details[0]['login_country']) ? $company_details[0]['login_country'] : ''; ?>','<?php echo isset($company_details[0]['login_state']) ? $company_details[0]['login_state'] : ''; ?>','<?php echo isset($company_details[0]['login_city']) ? $company_details[0]['login_city'] : ''; ?>')" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                        </td>
                    </tr> 

                </table>
        </form>
        </div>
    </div>
</div>  
<script type="text/javascript">
$(document).ready(function(){

var field_val = $("#firstname").val();
$("#firstname").focus().val("").val(field_val);
	
//$("#dob").datepicker({maxDate:new Date(1997, 11,31)});
$("#dob").datepicker({dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
        changeMonth: true,
        changeYear: true,
        maxDate: "-8Y",
        minDate: "-100Y",
        yearRange: "-46:-8"});
$('#driver_license_expire_date,#driver_pco_license_expire_date,#driver_insurance_expire_date,#driver_national_insurance_expire_date').datepicker({dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,changeMonth: true, changeYear: true, yearRange: new Date().getFullYear()+':+100',minDate: 0,maxDate: new Date(2100, 1,18) });

change_state('','');	
change_city('','','');
telephone_code();
//phone number validation
$("#phone" ).keyup(function() {
	//to allow left and right arrow key move
	if(event.which>=37 && event.which<=40)
	{
		return false;

	}
	this.value = this.value.replace(/[`~!@#$%^&*()\s_|+\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
	//this.value = this.value.replace(/[`~!@#$%^&*\s_|\=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
});

//Brand Type change function
    var brand = '<?php echo $brand_type; ?>';
		if(brand == "S"){
			$('#companyRow').hide();
		} else {
			$('#companyRow').show();
		}
  $('#brand_type').on('change',function(){
		var brand = $(this).val();
		if(brand == "S"){
			$('#companyRow').hide();
		} else {
			$('#companyRow').show();
		}
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
			telephone_code();
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    });
    
    //function to get Telephone code while selecting the country
    function telephone_code()
    {
		var countryid= $("#country").val();
		$.ajax({
			url:"<?php echo URL_BASE;?>add/getTelephoneCode",
			type:"post",
			data:"country_id="+countryid,
			success:function(data){
				$("#mobile_code").html(data);
				$("#hid_mobile_code").val(data);
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

function getcountry(company_id,stateid,cityid)
{
	$.ajax({
		url:"<?php echo URL_BASE;?>add/getcountry",
		type:"get",
		data:"company_id="+company_id,
		success:function(data){
			var res = data.split("~");
			$('#country').html(res[0]);
			if(stateid != '' && cityid != '') {
				change_country(stateid,cityid);
			} else {
				change_country(res[1],res[2]);
			}
		},
		error:function(data)
		{
			//alert(cid);
		}
	});	
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
			telephone_code();
		},
		error:function(data)
		{
			//alert(cid);
		}
	});
}


    $.validator.addMethod( "imageonly", function(value,element){
var pathLength = value.length; var lastDot = value.lastIndexOf( "."); var fileType = value.substring(lastDot,pathLength).toLowerCase(); return this.optional(element) || fileType.match(/(?:.jpg|.jpeg|.png)$/) }, "<?php echo __('please_upload_image_fileonly'); ?>");
    
</script>

