<?php defined('SYSPATH') OR die("No direct access allowed."); $select_pack_id ='';  ?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
         <form name="addcompany_form" class="form" id="addcompany_form" action="" method="post" enctype="multipart/form-data" data-form="script-form">
         <table border="0" cellpadding="5" cellspacing="0" width="100%">
	   <tr>
               <td colspan="2"><h2 class="tab_sub_tit"><?php echo __('personalinform'); ?></h2></td>
	   </tr>          
	   <tr>
           <td valign="top" width="20%"><label><?php echo __('firstname'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterfirstname_msg'); ?>" class="required" name="firstname" id="firstname" value="<?php if(isset($postvalue) && array_key_exists('firstname',$postvalue)){ echo $postvalue['firstname']; }?>"  minlength="4" maxlength="45" />
              <?php if(isset($errors) && array_key_exists('firstname',$errors)){ echo "<span class='error'>".ucfirst($errors['firstname'])."</span>";}?>
		   </div>
           </td>   	
           </tr>        

           <tr>
           <td valign="top" width="20%"><label><?php echo __('lastname'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterlastname_min1_msg'); ?>" class="required" name="lastname" id="lastname" value="<?php if(isset($postvalue) && array_key_exists('lastname',$postvalue)){ echo $postvalue['lastname']; }?>"  minlength="1" maxlength="45" />
              <?php if(isset($errors) && array_key_exists('lastname',$errors)){ echo "<span class='error'>".ucfirst($errors['lastname'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('email'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enteremailaddress'); ?>" class="required" name="email" id="email" value="<?php if(isset($postvalue) && array_key_exists('email',$postvalue)){ echo $postvalue['email']; }?>"  maxlength="64" />
              <?php if(isset($errors) && array_key_exists('email',$errors)){ echo "<span class='error'>".ucfirst($errors['email'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  

           <tr>
           <td valign="top" width="20%"><label><?php echo __('password'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="password" title="<?php echo __('enterpassword_msg'); ?>" class="required" name="password" id="password" value="<?php if(isset($postvalue) && array_key_exists('password',$postvalue)){ echo $postvalue['password']; }?>"  minlength="4" maxlength="20" />
              <?php if(isset($errors) && array_key_exists('password',$errors)){ echo "<span class='error'>".ucfirst($errors['password'])."</span>";}?>
		   </div>
           </td>   	
           </tr>

            <tr>
           <td valign="top" width="20%"><label><?php echo __('confirm_password_label'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
		   <input type="password" title="<?php echo __('entertheconfirmpassword_msg'); ?>" class="required" name="repassword" id="repassword" value="<?php if(isset($postvalue) && array_key_exists('repassword',$postvalue)){ echo $postvalue['repassword']; }?>"  minlength="4" maxlength="20" />
		      <?php if(isset($errors) && array_key_exists('repassword',$errors)){ echo "<span class='error'>".ucfirst($errors['repassword'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
                                            
           <tr>
           <td valign="top" width="20%"><label><?php echo __('mobile'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field input-mob-box mobile_codetxt">
              <input type="text" title="<?php echo __('entermobileno'); ?>" name="phone" id="phone" class="required" value="<?php if(isset($postvalue) && array_key_exists('phone',$postvalue)){ echo $postvalue['phone']; }?>" minlength="7" maxlength="16" />
              <span class="unit_mobile_code" id="mobile_code"></span>
              <input type="hidden" name="telephone_code" id="hid_mobile_code" value="">
              <?php if(isset($errors) && array_key_exists('phone',$errors)){ echo "<span class='error'>".ucfirst($errors['phone'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  

			
           
           <tr> 
           <td valign="top" width="20%"><label><?php echo __('address'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
		<textarea name="address" id="address"  title="<?php echo __('enteraddress'); ?>" class="required" rows="7" cols="35"><?php if(isset($postvalue) && array_key_exists('address',$postvalue)){ echo $postvalue['address']; }?></textarea>

              <?php if(isset($errors) && array_key_exists('address',$errors)){ echo "<span class='error'>".ucfirst($errors['address'])."</span>";}?>
		   </div>
           </td>   	
           </tr>             
           <tr>
               <td colspan="2"><h2 class="tab_sub_tit"><?php echo ucfirst(__('companyinformation')); ?></h2></td>
           </tr>
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('companyname'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterthecompanyname_msg'); ?>" class="required" name="company_name" id="company_name" value="<?php if(isset($postvalue) && array_key_exists('company_name',$postvalue)){ echo $postvalue['company_name']; }?>"  minlength="4" maxlength="45"  />
              <?php if(isset($errors) && array_key_exists('company_name',$errors)){ echo "<span class='error'>".ucfirst($errors['company_name'])."</span>";}?>
		   </div>
           </td>   	
           </tr>   
           <input type="hidden" name="domain_name" value=""/>
           <input type="hidden" name="company_address" value=""/>
			<?php /* <tr>
           <td valign="top" width="20%"><label><?php echo __('company_domain'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_company_domain'); ?>" class="required" onblur="checkdomainname(this.value)" name="domain_name" id="domain_name" value="<?php if(isset($postvalue) && array_key_exists('domain_name',$postvalue)){ echo $postvalue['domain_name']; }?>"  minlength="4" maxlength="15" onkeyup="nospaces(this)" style="display:inline"/> 
              <span><?php echo SUB_DOMAIN_NAME; ?></span><br /><span id="unameavilable" class="validerror"> </span>
              
		   </div>
           </td>   	
           </tr>  
                      
           <tr>
           <td valign="top" width="20%"><label><?php echo __('companyaddress'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
			<textarea name="company_address" id="company_address"  class="required" title="<?php echo __('enterthecompanyaddress'); ?>" rows="7" cols="35"><?php if(isset($postvalue) && array_key_exists('company_address',$postvalue)){ echo $postvalue['company_address']; }?></textarea>

              <?php if(isset($errors) && array_key_exists('company_address',$errors)){ echo "<span class='error'>".ucfirst($errors['company_address'])."</span>";}?>
		   </div>
           </td>   	
           </tr>   */ ?>
           
	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('country',$postvalue)){ $field_type =  $postvalue['country']; } ?>
	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
              <select name="country" id="country" class="required" title="<?php echo __('select_the_country'); ?>">
              <option value=""><?php echo __('select_label'); ?></option>
              <?php foreach($country_details as $country_list) { ?>
						<option value="<?php echo $country_list['country_id']; ?>" <?php if($field_type == $country_list['country_id']) { echo 'selected=selected'; }elseif($country_list['country_id'] == DEFAULT_COUNTRY) { echo 'selected=selected'; } ?>>
							<?php echo ucfirst($country_list['country_name']); ?>
						</option>
              <?php } ?>
              </select>
        </div>
	</div>
		<label for="country" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_country'); ?></label>	
              <?php if(isset($errors) && array_key_exists('country',$errors)){ echo "<span class='error'>".ucfirst($errors['country'])."</span>";}?>

	</td>   	
	</tr>

	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('state',$postvalue)){ $field_type =  $postvalue['state']; } ?>
	<td valign="top" width="20%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="state_list">
		<select name="state" id="state" class="required" onchange="change_city();" title="<?php echo __('select_the_state'); ?>">
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($state_details as $state_list) {  ?>
			<option value="<?php echo $state_list['state_id']; ?>" <?php if($field_type == $state_list['state_id']) { echo 'selected=selected'; }elseif($state_list['state_id'] == DEFAULT_STATE) { echo 'selected=selected'; } ?> ><?php echo ucfirst($state_list["state_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
		<label for="state" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_state'); ?></label>	
              <?php if(isset($errors) && array_key_exists('state',$errors)){ echo "<span class='error'>".ucfirst($errors['state'])."</span>"; }?>
        </td>      
	</tr>


	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('city',$postvalue)){ $field_type =  $postvalue['city']; } ?>
	<td valign="top" width="20%"><label><?php echo __('city_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="city_list">
		<select name="city" id="city" class="required" title="<?php echo __('select_the_city'); ?>">
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($city_details as $city_list) {  ?>
		<option value="<?php echo $city_list['city_id']; ?>" <?php if($field_type == $city_list['city_id']) { echo 'selected=selected'; }elseif($city_list['city_id'] == DEFAULT_CITY) { echo 'selected=selected'; } ?> ><?php echo ucfirst($city_list["city_name"]); ?></option>
		<?php } ?>
		</select>
	</div>	
		</div></div>
		<label for="city" generated="true" style="display:none" class="errorvalid"><?php echo __('select_the_city'); ?></label>	
              <?php if(isset($errors) && array_key_exists('city',$errors)){ echo "<span class='error'>".ucfirst($errors['city'])."</span>"; }?>
          </td>    
	</tr>
	<?php /*
	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('currency_code',$postvalue)){ $field_type =  $postvalue['currency_code']; } ?>
	<td valign="top" width="20%"><label><?php echo __('currency_code'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" >
	<span><?php echo __('currency_code'); ?></span>
	<div id="currency_code">
		<select name="currency_code" id="currency_code" class="required" title="<?php echo __('select_currency_code'); ?>">
		<option value=""><?php echo __('select_label'); ?></option>
				<?php foreach($currency_code as $key=>$currencycode){ ?>
				<option value="<?php echo $currencycode;?>" <?php if($field_type == $currencycode['currency_code']) { echo 'selected=selected'; } ?>  ><?php echo $currencycode;?></option>
				<?php } ?>
		</select>
	</div>	
		</div></div>
		<label for="currency_code" generated="true" style="display:none" class="errorvalid"><?php echo __('select_currency_code'); ?></label>	
              <?php if(isset($errors) && array_key_exists('currency_code',$errors)){ echo "<span class='error'>".ucfirst($errors['currency_code'])."</span>"; }?>
          </td>    
	</tr>
	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('currency_symbol',$postvalue)){ $field_type =  $postvalue['currency_symbol']; } ?>
	<td valign="top" width="20%"><label><?php echo __('currency_symbol'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" >
	<span><?php echo __('currency_symbol'); ?></span>
	<div id="currency_symbol">
		<select name="currency_symbol" id="currency_symbol" class="required" title="<?php echo __('select_currency_symbol'); ?>">
		<option value=""><?php echo __('select_label'); ?></option>
				<?php foreach($currency_symbol as $key=>$currencysymbol){ ?>
				<option value="<?php echo $currencysymbol;?>" <?php if($field_type == $currencysymbol['currency_symbol']) { echo 'selected=selected'; } ?> ><?php echo $currencysymbol;?></option>
				<?php } ?>
				<?php echo isset($payment_settings) && (!array_key_exists('currency_symbol', $errors)) ? $payment_settings[0]['currency_symbol'] : $validator['currency_symbol']; ?>
		</select>
	</div>	
		</div></div>
		<label for="currency_symbol" generated="true" style="display:none" class="errorvalid"><?php echo __('select_currency_symbol'); ?></label>	
              <?php if(isset($errors) && array_key_exists('currency_symbol',$errors)){ echo "<span class='error'>".ucfirst($errors['currency_symbol'])."</span>"; }?>
          </td>    
	</tr>
*/ ?>
	<input type="hidden" name="currency_symbol" id="currency_symbol" value="<?php echo CURRENCY; ?>"/>
	<input type="hidden" name="currency_code" id="currency_code" value="<?php echo CURRENCY_FORMAT; ?>"/>
	<tr style="display:none">
	<?php $field_type =TIMEZONE; /*if(isset($postvalue) && array_key_exists('time_zone',$postvalue)){ $field_type =  $postvalue['time_zone']; } */ ?>
	<td valign="top" width="20%"><label><?php echo __('time_zone'); ?></label><span class="star">*</span></td>
	<td>
	
	<div class="col-lg-12" >
	<!-- <span><?php echo __('select_label'); ?></span> !-->
            <div class="select_box">
		<select name="time_zone" id="time_zone" class="form-control js-example-basic-single required" title="<?php echo __('select_time_zone'); ?>">
		<option value=""><?php echo __('select_label'); ?></option>
		<?php

		$timezone = unserialize(SELECT_TIMEZONE);

		foreach($timezone as $key => $value) {  ?>
		<option value="<?php echo $value; ?>" <?php if($field_type == $value) { echo 'selected=selected'; } ?> ><?php echo ucfirst($value); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div>
		<label for="time_zone" generated="true" style="display:none" class="errorvalid"><?php echo __('select_time_zone'); ?></label>	
              <?php if(isset($errors) && array_key_exists('time_zone',$errors)){ echo "<span class='error'>".ucfirst($errors['time_zone'])."</span>"; }?>
          </td>    
	</tr>

	<tr>
		<td valign="top" width="20%"><label><?php echo __('company_image_label'); ?><span class="star">*</span> </label></td>   
		<td>
			<div class="new_input_field">
				<input type="file"  class="required imageonly" name="company_image" id="taxi_image" title="<?php echo __('select_taxi_image'); ?>" value="<?php //if(isset($postvalue) && array_key_exists('taxi_image',$postvalue)){ echo $postvalue['taxi_image']; }?>">
				
			</div>
			<?php if(isset($errors) && array_key_exists('company_image',$errors)){ echo "<span class='error'>".ucfirst($errors['company_image'])."</span>";}?>
		</td>
	    </tr>
	  <input type="hidden" name="pack" value="1"  />
	<tr style="display:none">
	<td><?php echo ucfirst(__('package_upgrade')); ?></td>
	<td></td>	          
	</tr>

	<tr style="display:none">
	<td><div class="new_input_field">
	<input type="radio" name="upgrade_package" id="upgrade_package1" title="<?php echo __('enter_payment_method'); ?>"  value="D" checked ><?php echo __('Yes'); ?>
	
	<input type="radio" name="upgrade_package" id="upgrade_package2" title="<?php echo __('enter_payment_method'); ?>"  value="N"><?php echo __('No'); ?>
	</div>
	<?php if(isset($errors) && array_key_exists('upgrade_package',$errors)){ echo "<span class='error'>".ucfirst($errors['upgrade_package'])."</span>";}?></td>
	</tr>


	</table>
	
	<div id="show_package" class="admin_show_package" style="display:none">      
        <p ><?php echo __('select_package'); ?></p>
			<?php 
			$i = 1;
			$package_count = count($package_details);
			foreach($package_details as $package_list) { ?>

                                <p>
                                <input type="radio" name="pack" id="pack_<?php echo $package_list['package_id']; ?>" value="<?php echo $package_list['package_id']; ?>" <?php if($select_pack_id){ echo 'CHECKED'; } elseif ($i == 1) { echo 'CHECKED'; }?>/>
                                <strong><?php echo wordwrap(ucfirst($package_list['package_name']),30,'<br/>',1); ?></strong>
                                <a href="javascript:;" id="view_<?php echo $package_list['package_id']; ?>" onclick="show_detail('<?php echo $i; ?>','<?php echo $package_count; ?>')" title="<?php echo __('details'); ?>"><?php echo '('.__('details').')'; ?></a>
                                </p>
                                <div class="clr mt10" id="pack_det_<?php echo $i; ?>" style="display:none;">
                                <table border="0" cellpadding="5" cellspacing="0" width="100%">      
				<tr>
                                <td width="20%" valign="top">
                                <?php echo '<h1 style="font-size:bold !important;">'.__('package_name').' :'.'</h1>'; ?></td>
                                <td><?php echo wordwrap(ucfirst($package_list['package_name']),30,'<br/>',1); ?></td>
                                </tr>
                                <tr>
                                <td width="20%" valign="top">
                                <?php echo '<h1 style="font-size:bold !important;">'.__('package_description').' :'.'</h1>'; ?></td>
                                <td><?php echo wordwrap(ucfirst($package_list['package_description']),30,'<br/>',1); ?></td>
                                </tr>
                                <tr>
                                <td width="20%" valign="top">
                                <?php echo '<h1 style="font-size:bold !important;">'.__('package_price').' :'.'</h1>'; ?></td>
                                <td><?php echo CURRENCY.' '.$package_list["package_price"];?></td>
                                </tr>
                                <tr>
                                <td width="20%" valign="top">
                                <?php echo '<h1 style="font-size:bold !important;">'.__('no_of_taxi').' :'.'</h1>'; ?></td>
                                <td><?php echo $package_list["no_of_taxi"];?></td>
                                </tr>
                                <tr>
                                <td width="20%" valign="top">
                                <?php echo '<h1 style="font-size:bold !important;">'.__('no_of_driver').' :'.'</h1>'; ?></td>
                                <td><?php echo $package_list["no_of_driver"];?></td>
                                </tr>                                
                                <tr>
                                <td width="20%" valign="top">
                                <?php echo '<h1 style="font-size:bold !important;">'.__('no_of_days_expire').' :'.'</h1>'; ?></td>
                                <td><?php echo $package_list["days_expire"];?></td>
                                </tr>
                                <tr>
                                <td width="20%" valign="top">
                                <?php echo '<h1 style="font-size:bold !important;">'.__('package_type').' :'.'</h1>'; ?></td>
                                <td><?php if($package_list['package_type'] == 'T' ) { echo __('transaction_based_commission'); } else if($package_list['package_type'] == 'P' ) { echo __('package_based_commission'); } else if($package_list['package_type'] == 'N' ) { echo __('package_based_no_commission'); } ?></td>
                                </tr>
                                <tr>
                                <td width="20%" valign="top">
                                <?php echo '<h1 style="font-size:bold !important;">'.__('driver_tracking').' :'.'</h1>'; ?></td>
                                <td><?php if($package_list['driver_tracking'] == 'N' ) { echo __('no'); } else { echo __('yes'); } ?></td>
                                </tr>
                                </table>
                                </div>
                               <?php 
                               $i++;
                               } ?> 

	</div>
	<table border="0" cellpadding="5" cellspacing="0" width="100%" class="no_hide">                     
 	<tr>
	<td width="20%" class="empt_cel"> &nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>   
		<tr>
		<td width="20%" class=""> &nbsp;</td>
			<td colspan="">
				<div class="new_button"><input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.location='<?php echo URL_BASE."manage/company"; ?>'" /></div>
				<div class="new_button"><input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addcompany" title="<?php echo __('btn_submit' );?>" /></div>
				<div class="new_button"><input type="reset" onclick="change_state('<?php echo DEFAULT_COUNTRY; ?>','<?php echo DEFAULT_STATE; ?>');change_city('<?php echo DEFAULT_COUNTRY; ?>','<?php echo DEFAULT_STATE; ?>','<?php echo DEFAULT_CITY; ?>')" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
				
				<div class="clr">&nbsp;</div>
			</td>
		</tr> 
	</table>
        </form>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>  

<?php

$date = new DateTime('now', new DateTimeZone(TIMEZONE));
$current_time = $date->format('H:i:s');

?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/select2.js"></script>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/css/select2.css"/>
<script type="text/javascript">

$(document).ready(function(){

telephone_code();
$.validator.addMethod( "imageonly", function(value,element){
var pathLength = value.length; var lastDot = value.lastIndexOf( "."); var fileType = value.substring(lastDot,pathLength).toLowerCase(); return this.optional(element) || fileType.match(/(?:.jpg|.jpeg|.png)$/) }, "Please upload image file only");

	$("#phone" ).keyup(function() {
		//to allow left and right arrow key move
		if(event.which>=37 && event.which<=40)
		{
			return false;

		}
		this.value = this.value.replace(/[`~!@#$%^&*()\s_|+\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
			//this.value = this.value.replace(/[`~!@#$%^&*\s_|\=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
	});
	$("#firstname").focus();
	jQuery("#addcompany_form").validate();
	 
	$.validator.addMethod( "imageonly", function(value,element){
var pathLength = value.length; var lastDot = value.lastIndexOf( "."); var fileType = value.substring(lastDot,pathLength).toLowerCase(); return this.optional(element) || fileType.match(/(?:.jpg|.jpeg|.png)$/) }, "Please upload image(jpg,jpeg,png) files only");

	change_state('','');	
	change_city('','','');	
	 $("#time_zone").select2();
	 //company name validation expression
	 $("#company_name" ).keyup(function() {
		this.value = this.value.replace(/[`~!@#$%^*_|\=?;:+",.<>\{\}\[\]\\\/]/gi, '');
	});
	
	$("#domain_name" ).keyup(function() {
		this.value = this.value.replace(/[`~!@#$&%^*()\s_|\=?;:+-.'",<>\{\}\[\]\\\/]/gi, '');
		if($(this).val().length < 4) {
			$("#unameavilable").html("");
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
			change_city_drop();
			telephone_code();
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    });
	
	$('#upgrade_package1').click(function(){
		$('#show_package').show();
	});
	$('#upgrade_package2').click(function(){
		$('#show_package').hide();
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

function show_detail(id,tot)
{
	for(var i=1; i <= tot; i++)
	{  
		var isVisible = $("#pack_det_"+i).is(":visible");
		
		if(id==i)
		{
			$("#pack_det_"+i).slideToggle("show");
		}
		else
		{	
			if(isVisible)
			{
				$("#pack_det_"+i).slideToggle("hide");
			}
		}
	}
} 





function nospaces(t){
  if(t.value.match(/\s/g)){
    alert("Do Not Allow Space");
    t.value=t.value.replace(/\s/g,'');
  }
}

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
</script>
