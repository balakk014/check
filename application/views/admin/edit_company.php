<?php defined('SYSPATH') OR die("No direct access allowed.");  ?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<!-- time picker start-->

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
         <form name="editcompany_form" id="editcompany_form" class="form" action="" method="post" enctype="multipart/form-data">
          <table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
            <td colspan="2"><h2 class="tab_sub_tit"><?php echo __('personalinform'); ?></h2></td>
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
			   <input type="hidden" name="company_id" value="<?php echo $company_details[0]['cid']; ?>"/>
           <td valign="top" width="20%"><label><?php echo __('firstname'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">	
              <input type="text"  maxlength="45" minlength="4"  class="required" title="<?php echo __('enterfirstname_msg'); ?>" id="firstname" name="firstname" value="<?php echo $firstname; ?>" />
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
           <td valign="top" width="20%"><label><?php echo __('lastname'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">	
              <input type="text"  maxlength="45" minlength="1" class="required" title="<?php echo __('enterlastname_msg'); ?>" id="lastname" name="lastname" value="<?php echo $lastname; ?>" />
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
           <td valign="top" width="20%"><label><?php echo __('email'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">	
              <input type="text"  title="<?php echo __('enteremailaddress'); ?>" class="required" id="email" name="email" value="<?php echo $email; ?>" maxlength="75" /><br>
              <?php if(isset($errors) && array_key_exists('email',$errors)){ echo "<span class='error'>".ucfirst($errors['email'])."</span>";}?>
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
           <td valign="top" width="20%"><label><?php echo __('mobile'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field input-mob-box mobile_codetxt">	
              <input type="text" title="<?php echo __('entermobileno'); ?>" id="phone" name="phone" value="<?php echo $phone; ?>" minlength="7" maxlength="16" />
              <span class="unit_mobile_code" id="mobile_code"></span>
              <input type="hidden" name="telephone_code" id="hid_mobile_code" value="">
              <?php if(isset($errors) && array_key_exists('phone',$errors)){ echo "<span class='error'>".ucfirst($errors['phone'])."</span>";}?>
		   </div>
	   </td>   	
           </tr>                       		   

        <?php /*   <tr>
           <td valign="top" width="20%"><label><?php echo __('paypal_api_username'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_paypal_api_username'); ?>" name="paypal_api_username" id="paypal_api_username" class="required" value="<?php echo isset($company_details[0]['company_paypal_username']) &&!array_key_exists('paypal_api_username',$postvalue)? trim($company_details[0]['company_paypal_username']):$postvalue['paypal_api_username']; ?>" maxlength="150" />
              <?php if(isset($errors) && array_key_exists('paypal_api_username',$errors)){ echo "<span class='error'>".ucfirst($errors['paypal_api_username'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('paypal_api_password'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_paypal_api_password'); ?>" name="paypal_api_password" id="paypal_api_password" class="required" value="<?php echo isset($company_details[0]['company_paypal_password']) &&!array_key_exists('paypal_api_password',$postvalue)? trim($company_details[0]['company_paypal_password']):$postvalue['paypal_api_password']; ?>" maxlength="150" />
              <?php if(isset($errors) && array_key_exists('paypal_api_password',$errors)){ echo "<span class='error'>".ucfirst($errors['paypal_api_password'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
           
            <tr>
           <td valign="top" width="20%"><label><?php echo __('paypal_api_signature'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_paypal_api_signature'); ?>" name="paypal_api_signature" id="paypal_api_signature" class="required" value="<?php echo isset($company_details[0]['company_paypal_signature']) &&!array_key_exists('paypal_api_signature',$postvalue)? trim($company_details[0]['company_paypal_signature']):$postvalue['paypal_api_signature']; ?>" maxlength="150" />

              <?php if(isset($errors) && array_key_exists('paypal_api_signature',$errors)){ echo "<span class='error'>".ucfirst($errors['paypal_api_signature'])."</span>";}?>
		   </div>
           </td>   	
           </tr>

			<tr>
				<td valign="top" width="20%"><label><?php echo __('payment_method'); ?></label><span class="star">*</span></td>   
				<td><div class="new_input_field">
				
				<input type="radio" name="payment_method" id="payment_method" title="<?php echo __('enter_payment_method'); ?>"  value="T" <?php if($company_details[0]['payment_method']=='T'){ echo 'checked=checked';}?> ><?php echo 'Test Mode'; ?>
				
				<input type="radio" name="payment_method" id="payment_method" title="<?php echo __('enter_payment_method'); ?>"  value="L" <?php if($company_details[0]['payment_method']=='L'){ echo 'checked=checked';}?>><?php  echo 'Live Mode'; ?>
				</div>
				<?php if(isset($errors) && array_key_exists('payment_method',$errors)){ echo "<span class='error'>".ucfirst($errors['payment_method'])."</span>";}?></td>
			</tr>
		   
           <tr> */ ?>
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
           <td valign="top" width="20%"><label><?php echo __('address'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">	
              <textarea name="address" id="address" class="required" title="<?php echo __('enteraddress'); ?>" rows="7" cols="35"><?php echo $address; ?></textarea>
              <?php if(isset($errors) && array_key_exists('address',$errors)){ echo "<span class='error'>".$errors['address']."</span>";}?>
		   </div>
	   </td>   	
           </tr>  
           
	   <tr>
		<td colspan="2"><h2 class="tab_sub_tit"><?php echo ucfirst(__('companyinformation')); ?></h2></td>
		<td></td>	          
	   </tr>       
    <?php
	if(isset($company_details[0]['company_name']) && !array_key_exists('company_name',$postvalue)){
		$company_name = $company_details[0]['company_name'];
	}else{
		if(isset($postvalue['company_name'])){
			$company_name = $postvalue['company_name'];
		}else{
			$company_name = "";
		}
	}
	?>	 
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('companyname'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">	
              <input type="text"  maxlength="45" minlength="4"  class="required" title="<?php echo __('enterthecompanyname_msg'); ?>" id="company_name" name="company_name" value="<?php echo $company_name; ?>" />
              <?php if(isset($errors) && array_key_exists('company_name',$errors)){ echo "<span class='error'>".ucfirst($errors['company_name'])."</span>";}?>
		   </div>
	   </td>   	
           </tr>  
           </tr>
           <input type="hidden" name="domain_name" value=""/>
           <input type="hidden" name="company_address" value=""/>
			<?php /* <tr>
           <td valign="top" width="20%"><label><?php echo __('company_domain'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_company_domain'); ?>"  name="domain_name" id="domain_name" value="<?php if(isset($company_details[0]['company_domain'])){ echo $company_details[0]['company_domain']; }?>"  minlength="4" maxlength="15"  style="display:inline" readonly="readonly"/><span><?php echo SUB_DOMAIN_NAME; ?></span>
              <?php if(isset($errors) && array_key_exists('domain_name',$errors)){ echo "<span class='error'>".ucfirst($errors['domain_name'])."</span>";}?>
		   </div>
           </td>   	
           </tr>                        		   
           <tr>
           <td valign="top" width="20%"><label><?php echo __('companyaddress'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">	
              <textarea name="company_address" id="company_address"  class="required" title="<?php echo __('enterthecompanyaddress'); ?>" rows="7" cols="35"><?php echo isset($company_details[0]['company_address']) &&!array_key_exists('company_address',$postvalue)? trim($company_details[0]['company_address']):$postvalue['company_address']; ?></textarea>
              <?php if(isset($errors) && array_key_exists('company_address',$errors)){ echo "<span class='error'>".ucfirst($errors['company_address'])."</span>";}?>
		   </div>
	   </td>   	
   </tr> */ ?> 
   
    <?php
	if(isset($company_details[0]['company_country']) && !array_key_exists('country',$postvalue)){
		$company_country = $company_details[0]['company_country'];
		}else{
			if(isset($postvalue['country'])){
				$company_country = $postvalue['country'];
			}else{
				$company_country = "";
			}
		}
		$field_type =  $company_country;
	?>
	 
<tr>
	
	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	
               <select  title="<?php echo __('select_the_country'); ?>" class="required" <?php if($_SESSION['user_type']== 'C' || $_SESSION['user_type']== 'M' ) { ?> name="countrys" disabled <?php }else{ ?> name="country" id="country" <?php } ?>>
				  <?php foreach($country_details as $country_list) { ?>
				  <option value="<?php echo $country_list['country_id']; ?>" <?php if($field_type == $country_list['country_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($country_list['country_name']); ?></option>
				  <?php } ?>
              </select>
              
              
			<?php if($_SESSION['user_type']== 'C' || $_SESSION['user_type']== 'M' ) { ?> <input type="hidden" name="country" id="country" value="<?php echo $field_type; ?>"> <?php } ?>
        </div>
	</div>
		<label for="country" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_country'); ?></label>	
              <?php if(isset($errors) && array_key_exists('country',$errors)){ echo "<span class='error'>".ucfirst($errors['country'])."</span>";}?>

	</td>   	
	</tr>

	<tr>
	<?php $field_type =''; 

	if(isset($company_details[0]['company_state']) && !array_key_exists('state',$postvalue)){
		$company_state = $company_details[0]['company_state'];
		}else{
			if(isset($postvalue['state'])){
				$company_state = $postvalue['state'];
			}else{
				$company_state = "";
			}
		}
		$field_type =  $company_state;
	?>

	<td valign="top" width="20%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="state_list">
		<select name="state" id="state" onchange="change_city_drop('','','');" class="required" title="<?php echo __('select_the_state'); ?>">
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($state_details as $state_list) {  ?>
		<option value="<?php echo $state_list['state_id']; ?>" <?php if($field_type == $state_list['state_id']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($state_list["state_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
		<label for="state" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_state'); ?></label>	
              <?php if(isset($errors) && array_key_exists('state',$errors)){ echo "<span class='error'>".ucfirst($errors['state'])."</span>"; }?>
        </td>      
	</tr>
	
	<tr>
	<?php $field_type =''; 
	
	if(isset($company_details[0]['company_city']) && !array_key_exists('city',$postvalue)){
		$company_city = $company_details[0]['company_city'];
		}else{
			if(isset($postvalue['city'])){
				$company_city = $postvalue['city'];
			}else{
				$company_city = "";
			}
		}
		$field_type =  $company_city;
		
	?>
	<td valign="top" width="20%"><label><?php echo __('city_label'); ?></label><span class="star">*</span></td>        
	<td>
		<div class="formRight">
		<div class="selector" id="uniform-user_type">
		<span><?php echo __('select_label'); ?></span>
		<div id="city_list">
			<select name="city" id="city"  class="required" title="<?php echo __('select_the_city'); ?>">
			<option value=""><?php echo __('select_label'); ?></option>
			<?php
			foreach($city_details as $city_list) {  ?>
			<option value="<?php echo $city_list['city_id']; ?>" <?php if($field_type == $city_list['city_id']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($city_list["city_name"]); ?></option>
			<?php	} ?>
			</select>
		</div>	
		</div>
		</div>
		<label for="city" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_city'); ?></label>	
		 <?php if(isset($errors) && array_key_exists('city',$errors)){ echo "<span class='error'>".ucfirst($errors['city'])."</span>"; }?>
	</td>   	
	</tr>
		<input type="hidden" name="currency_symbol" id="currency_symbol" value="<?php echo CURRENCY; ?>"/>
		<input type="hidden" name="currency_code" id="currency_code" value="<?php echo CURRENCY_FORMAT; ?>"/>
	<?php /*
<tr>
	<td valign="top" width="20%"><label><?php echo __('currency_code'); ?></label><span class="star">*</span></td>        
	<td>
		<div class="formRight">
		<div class="selector" id="uniform-user_type">
		<span><?php echo __('currency_code'); ?></span>
		<div id="currency_code">
			<select name="currency_code" id="currency_code" class="required" title="<?php echo __('select_currency_code'); ?>" >
				<option value=""><?php echo __('select_label'); ?></option>
				<?php foreach($currency_code as $key=>$currencycode){ ?>
				<option value='<?php echo $currencycode;?>' <?php if($company_details[0]['company_currency_format'] == $currencycode) { echo 'selected=selected'; } ?> ><?php echo $currencycode;?></option>
				<?php } ?>  
			</select>
		</div>	
		</div>
		</div>
		<label for="currency_code" generated="true" style="display:none" class="errorvalid"><?php echo __('select_currency_code'); ?></label>	
		 <?php if(isset($errors) && array_key_exists('currency_code',$errors)){ echo "<span class='error'>".ucfirst($errors['currency_code'])."</span>"; }?>
	</td>   	
	</tr>
		
		<tr>
	<td valign="top" width="20%"><label><?php echo __('currency_symbol'); ?></label><span class="star">*</span></td>        
	<td>
		<div class="formRight">
		<div class="selector" id="uniform-user_type">
		<span><?php echo __('currency_symbol'); ?></span>
		<div id="currency_symbol">
			<select name="currency_symbol" id="currency_symbol" class="required" title="<?php echo __('select_currency_symbol'); ?>" >
				<option value=""><?php echo __('select_label'); ?></option>
				<?php foreach($currency_symbol as $key=>$currency_symbol){ ?>
				<option value='<?php echo $currency_symbol;?>' <?php if($company_details[0]['company_currency'] == $currency_symbol) { echo 'selected=selected'; } ?> ><?php echo $currency_symbol;?></option>
				<?php } ?>  
			</select>
		</div>	
		</div>
		</div>
		<label for="currency_symbol" generated="true" style="display:none" class="errorvalid"><?php echo __('select_currency_symbol'); ?></label>	
		 <?php if(isset($errors) && array_key_exists('currency_symbol',$errors)){ echo "<span class='error'>".ucfirst($errors['currency_symbol'])."</span>"; }?>
	</td>   	
	</tr> */ ?>
	<?php /* if($controller == "edit" && $action == "company") { ?>
		 <?php 
			if(isset($company_details[0]['upgrade_expirydate'])){
				$upgrade_expirydate = $company_details[0]['upgrade_expirydate'];
			}else{
				if(isset($postvalue['expire_date'])){
					$upgrade_expirydate = $postvalue['expire_date'];
				}else{
					$upgrade_expirydate = "";
				}
			}
			?>         
		<tr>
           <td valign="top" width="20%"><label><?php echo __('expire_date'); ?></label><span class="star">*</span></td>        
		   <td>
			   <div class="new_input_field">	
				  <input type="text" readonly class="required" title="<?php echo __('Enter Company Expiry Date'); ?>" id="expire_date" name="expire_date" value="<?php echo $upgrade_expirydate; ?>" />
				  <?php if(isset($errors) && array_key_exists('expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['expire_date'])."</span>";}?>
			   </div>
		   </td>   	
		   <input type="hidden" name="prev_expiry_date" value="<?php echo isset($company_details[0]['upgrade_expirydate']) ? trim($company_details[0]['upgrade_expirydate']):''; ?>">
        </tr>
     <?php } */ ?>
		<?php /* ?>
		<tr>
		<?php $field_type =TIMEZONE; ?>

		<td valign="top" width="20%"><label><?php echo __('time_zone'); ?></label><span class="star">*</span></td>
		<td>
		<div class="formRight">
		<div class="new_input_field" >
		
		<div id="timezone_list">
			<select  title="<?php echo __('select_time_zone'); ?>"  <?php if($_SESSION['user_type']== 'C' || $_SESSION['user_type']== 'M' ) { ?> disabled name="time_zones" id="time_zone" <?php }else{?> name="time_zone" id="time_zone" class="form-control js-example-basic-single required" <?php } ?>>
			<option value=""><?php echo __('select_label'); ?></option>
			<?php

			$timezone = unserialize(SELECT_TIMEZONE);

			foreach($timezone as $key => $value) {  ?>
				<option value="<?php echo $value; ?>" <?php if($field_type == $value) { echo 'selected=selected'; } ?> ><?php echo ucfirst($value); ?></option>
			<?php	} ?>
			</select>
			<?php if($_SESSION['user_type']== 'C' || $_SESSION['user_type']== 'M' ) { ?> <input type="hidden" name="time_zone" id="time_zone" value="<?php echo $field_type; ?>">  <?php } ?> 
		</div>	
			</div></div>
			<label for="time_zone" generated="true" style="display:none" class="errorvalid"><?php echo __('select_time_zone'); ?></label>	
		      <?php if(isset($errors) && array_key_exists('time_zone',$errors)){ echo "<span class='error'>".ucfirst($errors['time_zone'])."</span>"; }?>
		  </td>    
		</tr>
		<?php */ ?>
		<tr>
			<td valign="top" width="20%"><label><?php echo __('company_image_label'); ?> </label></td>   			
			<td> 
				<div class="new_input_field">
					<input type="file" class="imageonly" name="company_image" id="taxi_image" title="<?php echo __('select_taxi_image'); ?>" value="">				
				</div>
				<div class="site_logo" >
					<?php  if(file_exists($_SERVER["DOCUMENT_ROOT"].'/public/'.UPLOADS.'/company/'.$company_details[0]['userid'].'.png')){  ?> 
						<img width="75" height="75" src="<?php echo URL_BASE.COMPANY_IMG_IMGPATH.$company_details[0]['userid'].'.png?q='.time();?>"/>
					<?php }else{ ?>
						<img width="75" height="75"  src="<?php echo URL_BASE;?>public/common/images/no_image.png?q="<?php time();?>/>
					<?php } ?>
				</div><br />
				<?php if(isset($errors) && array_key_exists('company_image',$errors)){ echo "<span class='error'>".ucfirst($errors['company_image'])."</span>";}?>				
			</td>
		</tr>
	<?php /*if($_SESSION['user_type']=="A" || $_SESSION['user_type']=="DA"){ ?>
	<!-----Company Payment settings----->
		
		<?php if(count($get_company_payment_settings)>0){ ?>
			<table class="0 sTable responsive" style="border-top:1px solid #cdcdcd;" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td colspan="3" valign="top" width="20%"><label><b><?php echo __('payment_module_settings'); ?><span class="star">*</span></b></label></td>   
				</tr>
				<tr>
					<td align="center" valign="top" width="10%"><label><?php echo __('payment_module_status'); ?></label></td>
					<td align="center" valign="top" width="10%"><label><?php echo __('payment_module_name'); ?></label></td>
					<td align="center" valign="top" width="10%"><?php echo __('default_gateway'); ?></td>
				</tr>				
				<?php $i=0;  foreach($get_company_payment_settings as $resultset) { ?>				
				<tr>
					<td align="center"><input class="pay_mod"  type="checkbox" name="paymodstatus[]" value="<?php echo $resultset['compay_payment_id']; ?>" <?php if($resultset['pay_active']==1){echo 'checked="checked"'; }  ?> /></td> 
					<td align="center"><img src="<?php echo URL_BASE;?>public/common/images/<?php echo $resultset['pay_mod_image'] ?>"><div class="new_input_field"><label><?php echo $resultset['pay_mod_name'];?></label></div></td>
					<td align="center"><input type="radio" name="default[]" value="<?php echo $resultset['compay_payment_id'];?>" <?php if($resultset['pay_mod_default']==1){echo 'checked="checked"'; }  ?>  <?php if($i=='0'){ echo 'checked="checked"'; } ?> /></td>
					<input type="hidden" name="payid[]" value="<?php echo $resultset['compay_payment_id'];?>"  />
				</tr>
				<?php $i++; } ?>				
				<tr>
					<td colspan="3"><b><a href="javascript:selectToggle(true, 'editcompany_form');"><?php echo __('all_label');?></a></b><span class="pr2 pl2">|</span><b><a href="javascript:selectToggle(false, 'editcompany_form');"><?php echo __('select_none');?></a></b></td>
				</td>
				<tr>					
					<td colspan="3"><?php if(isset($errors) && array_key_exists('paymodstatus',$errors)){ echo "<span class='error'>".ucfirst($errors['paymodstatus'])."</span>";}?></td>
				</tr>
			</table>			
		<?php } else { ?>
    
			<table  class="0" cellpadding="5" cellspacing="0" width="85%">
				<tr>
					<td colspan="3" valign="top" width="20%"><label><b><?php echo __('payment_module_settings'); ?><span class="star">*</span></b></label></td>   
				</tr>
				<tr>
					<td valign="top" width="10%" ><label><?php echo __('payment_module_status'); ?></label></td><td valign="top" width="10%" ><label><?php echo __('payment_module_name'); ?></label></td><td valigin="top" width="10%"><?php echo __('default_gateway'); ?></td></tr>	

						<?php
						$query2 = "SELECT * FROM ".PAYMENT_MODULES." order by pay_mod_id asc";

						$result = Db::query(Database::SELECT, $query2)
								->execute()
								->as_array();
						//print_r($result);exit;
						$i=0;
						foreach($result as $resultset) { ?>				
						<tr>
						<td><input class="pay_mod"  type="checkbox" name="paymodstatus[]" value="<?php echo $resultset['pay_mod_id']; ?>"/></td> <?php if(!array_key_exists('paymodstatus',$errors) && isset($post_values['paymodstatus'])){ if($post_values['paymodstatus'][$i]==$resultset['pay_mod_id']){echo 'checked="checked"'; }  }  ?>
						<td><img src="<?php echo URL_BASE;?>public/common/images/<?php echo $resultset['pay_mod_image'] ?>"><div class="new_input_field"><label><?php echo $resultset['pay_mod_name'];?></label></div></td>
						<td><input type="radio" name="default[]" value="<?php echo $resultset['pay_mod_id'];?>"  <?php if($i=='0'){ echo 'checked="checked"'; } ?> /></td>
						<input type="hidden" name="payid_add[]" value="<?php echo $resultset['pay_mod_id'];?>"  />
						<input type="hidden" name="paymodname[]" value="<?php echo $resultset['pay_mod_name'];?>"  />
						<input type="hidden" name="paymodimage[]" value="<?php echo $resultset['pay_mod_image'];?>"  />
						</tr>
						<?php $i++; } ?>
						<tr>
						<td><b><a href="javascript:selectToggle(true, 'addcompany_form');"><?php echo __('all_label');?></a></b><span class="pr2 pl2">|</span><b><a href="javascript:selectToggle(false, 'addcompany_form');"><?php echo __('select_none');?></a></b></td>
						</td>
					<tr>
					<td>&nbsp;</td>
					<td><?php if(isset($errors) && array_key_exists('paymodstatus',$errors)){ echo "<span class='error'>".ucfirst($errors['paymodstatus'])."</span>";}?></td>
				</tr>
			</table>
		<?php } ?>
    <!-----Company Payment settings----->
    <?php } */ ?>
			<tr><td width="20%" class="empt_cel"> &nbsp;</td>		
			<td class="star">*<?php echo __('required_label'); ?></td>
			</tr>                         
			<tr><td width="20%"></td>					 
				<td>					
					<div class="new_button"><input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
					<div class="new_button"><input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addcompany" title="<?php echo __('btn_submit' );?>" /></div>
					<div class="new_button"><input type="reset" onclick="change_state('<?php echo isset($company_details[0]['company_country']) ? $company_details[0]['company_country'] : ''; ?>','<?php echo isset($company_details[0]['company_state']) ? $company_details[0]['company_state'] : ''; ?>');change_city_drop('<?php echo isset($company_details[0]['company_country']) ? $company_details[0]['company_country'] : ''; ?>','<?php echo isset($company_details[0]['company_state']) ? $company_details[0]['company_state'] : ''; ?>','<?php echo isset($company_details[0]['company_city']) ? $company_details[0]['company_city'] : ''; ?>')" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
					
				</td>
			</tr> 
		</table>
		<td>&nbsp;</td>
                </table>

        </form>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>  
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/select2.js"></script>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/css/select2.css"/>
<script type="text/javascript">
$(document).ready(function(){
	telephone_code();
$("#time_zone").select2();
$("#phone" ).keyup(function() {
	//to allow left and right arrow key move
	if(event.which>=37 && event.which<=40)
	{
		return false;

	}
	this.value = this.value.replace(/[`~!@#$%^&*()\s_|+\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
	//this.value = this.value.replace(/[`~!@#$%^&*\s_|\=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
});
	
var field_val = $("#firstname").val();

$("#firstname").focus().val("").val(field_val);

var user_type = "<?php echo $_SESSION['user_type']; ?>";

	 jQuery("#editcompany_form").valid();

	$.validator.addMethod( "imageonly", function(value,element){
var pathLength = value.length; var lastDot = value.lastIndexOf( "."); var fileType = value.substring(lastDot,pathLength).toLowerCase(); return this.optional(element) || fileType.match(/(?:.jpg|.jpeg|.png)$/) }, "Please upload image(jpg,jpeg,png) files only");

	change_state('','');	
	//change_city();	
	change_city_drop('','','');	
	$("#company_name" ).keyup(function() {
		this.value = this.value.replace(/[`~!@#$%^*_|\=?;:+",.<>\{\}\[\]\\\/]/gi, '');
	});
	
	//Date time picker
	$("#expire_date").datetimepicker( {
		showTimepicker:true,
		showSecond: true,
		timeFormat: 'hh:mm:ss',
		dateFormat: 'yy-mm-dd',
		stepHour: 1,
		stepMinute: 1,
		minDateTime : new Date(),
		stepSecond: 1
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
			change_city_drop('','','');
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
    
function change_city_drop(country_id,state_id,city_id){
	var countryid= $("#country").val();
	var stateid= $("#state").val();
	var cityid= $("#city").val();
	if(country_id != '' && state_id != '' && city_id != '') {
		countryid = country_id;
		stateid = state_id;
		cityid = city_id;
	}
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

/* function change_city()
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
    
} */ 

function selectToggle(toggle, form) {
	var myForm = document.forms[form];

	if(toggle) {
		 $('.pay_mod').each(function() { //loop through each checkbox
			this.checked = true;  //select all checkboxes with class "checkbox1"              
			});
	} 
	else
	{  
		$('.pay_mod').each(function() { //loop through each checkbox
			this.checked = false;  //select all checkboxes with class "checkbox1"              
		}); 
	}
}

</script>
