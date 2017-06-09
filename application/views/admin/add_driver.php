<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimepicker/jquery-ui.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimepicker/jquery-1.9.1.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimepicker/jquery-ui.js"></script>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">   
       <form name="registration_form" id="registration_form" class="form" action="" method="post" enctype="multipart/form-data" data-form="server-form">
       <table border="0" cellpadding="5" cellspacing="0" width="100%">            

	<tr>
            <td><h2 class="tab_sub_tit"><?php echo ucfirst(__('personalinform')); ?></h2></td>
	<td></td>	          
	</tr>	

	<tr>
	<td valign="top" width="25%"><label><?php echo __('firstname'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45" minlength="4"  title="<?php echo __('enterfirstname'); ?>" id="firstname" name="firstname" value="<?php if(isset($postvalue) && array_key_exists('firstname',$postvalue)){ echo $postvalue['firstname']; }?>" />
              <?php if(isset($errors) && array_key_exists('firstname',$errors)){ echo "<span class='error'>".ucfirst($errors['firstname'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="25%"><label><?php echo __('lastname'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45" minlength="1" title="<?php echo __('enterlastname'); ?>" id="lastname" name="lastname" value="<?php if(isset($postvalue) && array_key_exists('lastname',$postvalue)){ echo $postvalue['lastname']; }?>" />
              <?php if(isset($errors) && array_key_exists('lastname',$errors)){ echo "<span class='error'>".ucfirst($errors['lastname'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	

	<tr>
	<td valign="top" width="25%"><label><?php echo __('email'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  title="<?php echo __('enteremailaddress'); ?>" id="email" name="email" value="<?php if(isset($postvalue) && array_key_exists('email',$postvalue)){ echo $postvalue['email']; }?>" maxlength="64" /><br>
              <?php if(isset($errors) && array_key_exists('email',$errors)){ echo "<span class='error'>".ucfirst($errors['email'])."</span>";}?>
	</div>
	</td>   	
	</tr>

           <tr>
           <td valign="top" width="25%"><label><?php echo __('password'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="password" title="<?php echo __('enterpassword'); ?>" name="password" id="password" value="<?php if(isset($postvalue) && array_key_exists('password',$postvalue)){ echo $postvalue['password']; }?>"  minlength="4" maxlength="20" />
              <?php if(isset($errors) && array_key_exists('password',$errors)){ echo "<span class='error'>".ucfirst($errors['password'])."</span>";}?>
		   </div>
           </td>   	
           </tr>

            <tr>
           <td valign="top" width="25%"><label><?php echo __('confirm_password_label'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
		   	              <input type="password" title="<?php echo __('entertheconfirmpassword'); ?>" name="repassword" id="repassword" value="<?php if(isset($postvalue) && array_key_exists('repassword',$postvalue)){ echo $postvalue['repassword']; }?>"  minlength="2" maxlength="20" />
		      <?php if(isset($errors) && array_key_exists('repassword',$errors)){ echo "<span class='error'>".ucfirst($errors['repassword'])."</span>";}?>
		   </div>
           </td>   	
           </tr>

	<tr>
	<td valign="top" width="25%"><label><?php echo __('gender'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="radio"   title="<?php echo __('selectthegender'); ?>" name="gender" value="Male" checked /><?php echo __('male'); ?>
              <input type="radio"   title="<?php echo __('selectthegender'); ?>" name="gender" value="Female" /><?php echo __('female'); ?>
              <?php if(isset($errors) && array_key_exists('gender',$errors)){ echo "<span class='error'>".ucfirst($errors['gender'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="25%"><label><?php echo __('date_of_birth'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  readonly title="<?php echo __('selectdate_of_birth'); ?>" id="dob" name="dob" value="<?php if(isset($postvalue) && array_key_exists('dob',$postvalue)){ echo $postvalue['dob']; }?>" checked />
              <?php if(isset($errors) && array_key_exists('dob',$errors)){ echo "<span class='error'>".ucfirst($errors['dob'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	           
	<tr>
	<td valign="top" width="25%"><label><?php echo __('driver_license_id'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45"  title="<?php echo __('enter_driver_license_id'); ?>" id="driver_license_id" name="driver_license_id" value="<?php if(isset($postvalue) && array_key_exists('driver_license_id',$postvalue)){ echo $postvalue['driver_license_id']; }?>" />
              <?php if(isset($errors) && array_key_exists('driver_license_id',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_license_id'])."</span>";}?>
	</div>
	</td>
        </tr>
        <tr>
	<td width="25%" ><label><?php echo __('driver_license_expire_date'); ?></label><span class="star">*</span></td>
	<td>
	<div class="new_input_field">
	  <input type="text"  maxlength="30" readonly title="<?php echo __('enter_driver_license_expire_date'); ?>" id="driver_license_expire_date" name="driver_license_expire_date" value="<?php if(isset($postvalue) && array_key_exists('driver_license_expire_date',$postvalue)){ echo $postvalue['driver_license_expire_date']; }?>" />
	  <?php if(isset($errors) && array_key_exists('driver_license_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_license_expire_date'])."</span>";}?>
	</div>
	</td>	
	</tr>
	
	<tr>
	<td valign="top" width="25%"><label><?php echo __('driver_pco_license_number'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45"  title="<?php echo __('enter_driver_pco_license_no'); ?>" id="driver_pco_license_number" name="driver_pco_license_number" value="<?php if(isset($postvalue) && array_key_exists('driver_pco_license_number',$postvalue)){ echo $postvalue['driver_pco_license_number']; }?>" />
              <?php if(isset($errors) && array_key_exists('driver_pco_license_number',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_pco_license_number'])."</span>";}?>
	</div>
	</td>
        </tr>
        <tr>
	<td width="25%"><label><?php echo __('driver_pco_license_expire_date'); ?></label><span class="star">*</span></td>
	<td>
	<div class="new_input_field">
	  <input type="text"  maxlength="30" readonly title="<?php echo __('enter_driver_pco_license_expire_date'); ?>" id="driver_pco_license_expire_date" name="driver_pco_license_expire_date" value="<?php if(isset($postvalue) && array_key_exists('driver_pco_license_expire_date',$postvalue)){ echo $postvalue['driver_pco_license_expire_date']; }?>" />
	  <?php if(isset($errors) && array_key_exists('driver_pco_license_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_pco_license_expire_date'])."</span>";}?>
	</div>
	</td>	
	</tr>
	
	<tr>
	<td valign="top" width="25%"><label><?php echo __('driver_insurance_number'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45"  title="<?php echo __('enter_driver_insurance_no'); ?>" id="driver_insurance_number" name="driver_insurance_number" value="<?php if(isset($postvalue) && array_key_exists('driver_insurance_number',$postvalue)){ echo $postvalue['driver_insurance_number']; }?>" />
              <?php if(isset($errors) && array_key_exists('driver_insurance_number',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_insurance_number'])."</span>";}?>
	</div>
	</td>
        </tr>
        <tr>
	<td width="25%"><label><?php echo __('driver_insurance_expire_date'); ?></label><span class="star">*</span></td>
	<td>
	<div class="new_input_field">
	  <input type="text"  maxlength="30" readonly title="<?php echo __('enter_driver_insurance_expire_date'); ?>" id="driver_insurance_expire_date" name="driver_insurance_expire_date" value="<?php if(isset($postvalue) && array_key_exists('driver_insurance_expire_date',$postvalue)){ echo $postvalue['driver_insurance_expire_date']; }?>" />
	  <?php if(isset($errors) && array_key_exists('driver_insurance_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_insurance_expire_date'])."</span>";}?>
	</div>
	</td>	
	</tr>
	
	<tr>
	<td valign="top" width="25%"><label><?php echo __('driver_national_insurance_number'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="45" title="<?php echo __('enter_driver_national_insurance_no'); ?>" id="driver_national_insurance_number" name="driver_national_insurance_number" value="<?php if(isset($postvalue) && array_key_exists('driver_national_insurance_number',$postvalue)){ echo $postvalue['driver_national_insurance_number']; }?>" />
              <?php if(isset($errors) && array_key_exists('driver_national_insurance_number',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_national_insurance_number'])."</span>";}?>
	</div>
	</td>
        </tr>
        <tr>
	<td width="25%"><label><?php echo __('driver_national_insurance_expire_date'); ?></label><span class="star">*</span></td>
	<td>
	<div class="new_input_field">
	  <input type="text"  maxlength="30" readonly title="<?php echo __('enter_driver_national_insurance_expire_date'); ?>" id="driver_national_insurance_expire_date" name="driver_national_insurance_expire_date" value="<?php if(isset($postvalue) && array_key_exists('driver_national_insurance_expire_date',$postvalue)){ echo $postvalue['driver_national_insurance_expire_date']; }?>" />
	  <?php if(isset($errors) && array_key_exists('driver_national_insurance_expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['driver_national_insurance_expire_date'])."</span>";}?>
	</div>
	</td>	
	</tr>
	<tr>
	<td valign="top" width="25%"><label><?php echo __('mobile'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field input-mob-box mobile_codetxt">
              <input type="text" title="<?php echo __('entermobileno'); ?>" width="80%" id="phone" name="phone" value="<?php if(isset($postvalue) && array_key_exists('phone',$postvalue)){ echo $postvalue['phone']; }?>" minlength="7" maxlength="20" />
              <span class="unit_mobile_code" id="mobile_code"></span>
              <input type="hidden" name="telephone_code" id="hid_mobile_code" value="">
              <?php if(isset($errors) && array_key_exists('phone',$errors)){ echo "<span class='error'>".ucfirst($errors['phone'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<?php if($_SESSION['user_type'] =='A' || $_SESSION['user_type'] =='DA') { ?>
	<tr>
		<td valign="top" width="25%"><label><?php echo __('brand'); ?></label><span class="star">*</span></td>        
		<td>
			<div class="selector">
				<select name="brand_type" id="brand_type">
					<option <?php if (isset($postvalue['brand_type']) && $postvalue['brand_type'] == "M") { ?>selected<?php } ?> value="M"><?php echo __('Multy'); ?></option>
					<option <?php if (isset($postvalue['brand_type']) && $postvalue['brand_type'] == "S") { ?>selected<?php } ?> value="S"><?php echo __('Single'); ?></option>
				</select>
			</div>
		</td>
	</tr> 
	<tr id="companyRow">
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('company_name',$postvalue) && empty($cid)){ $field_type =  $postvalue['company_name']; } else if(!empty($cid)) { $field_type = $cid; } ?>
	<td valign="top" width="25%"><label><?php echo __('taxicompany'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="taxicompany_list">
		<select name="company_name" id="company_name" onchange="getcountry(this.value);">
			<option value=""><?php echo __('select_label'); ?></option>
			<?php foreach($taxicompany_details as $company_list) {  ?>
			<option value="<?php echo $company_list['cid']; ?>" <?php if($field_type == $company_list['cid']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($company_list["company_name"]); ?></option>
			<?php } ?>
		</select>
	</div>	
		</div></div>
              <?php if(isset($errors) && array_key_exists('company_name',$errors)){ echo "<span class='error'>".ucfirst($errors['company_name'])."</span>"; }?>
        </td>      
	</tr>
	<?php } else { ?> 
	<tr>
	<td valign="top" width="25%"></td>
	<td>
	<div class="new_input_field">
	<input type="hidden" name="brand_type" id="brand_type" value="M">
	<input type="hidden" name="company_name" id="company_name" value="<?php echo $_SESSION['company_id']; ?>">
		<?php if(isset($errors) && array_key_exists('company_name',$errors)){ echo "<span class='error'>".ucfirst($errors['company_name'])."</span>"; } ?>

	 </div>
	 </td>
	</tr>
	<?php } ?>
	
	<?php 
	$field_type =''; if(isset($postvalue) && array_key_exists('state',$postvalue)){ $field_type =  $postvalue['state']; }else{ $field_type = $country_company;} 
	if($_SESSION['user_type'] !='M')
	{ 
		?>
	<tr>
	
	<td valign="top" width="25%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
              <select  <?php if($_SESSION['user_type']== 'C' || $_SESSION['user_type']== 'M' ) { ?> name="countrys" disabled id="country" <?php }else{ ?> name="country" id="country" <?php } ?> >
              <option value=""><?php echo __('select_label'); ?></option>
              <?php foreach($country_details as $country_list) { ?>
              <option value="<?php echo $country_list['country_id']; ?>" <?php if($field_type == $country_list['country_id']) { echo 'selected=selected'; }elseif($country_list['country_id'] == DEFAULT_COUNTRY) { echo 'selected=selected'; } ?>><?php echo ucfirst($country_list['country_name']); ?></option>
              <?php } ?>
              </select>
		<?php if($_SESSION['user_type']== 'C' || $_SESSION['user_type']== 'M' ) { ?> <input type="hidden" name="country" id="country" value="<?php echo $field_type; ?>"> <?php } ?>
        </div>
	</div>
    <?php if(isset($errors) && array_key_exists('country',$errors)){ echo "<span class='error'>".ucfirst($errors['country'])."</span>";} ?>
	</td>   	
	</tr>

	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('state',$postvalue)){ $field_type =  $postvalue['state']; }else{ $field_type = $state_company;} ?>
	<td valign="top" width="25%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="state_list">
		<select name="state" id="state" --onchange="change_city_drop();">
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($state_details as $state_list) {  ?>
		<option value="<?php echo $state_list['state_id']; ?>" <?php if($field_type == $state_list['state_id']) { echo 'selected=selected'; }elseif($state_list['state_id'] == DEFAULT_STATE) { echo 'selected=selected'; } ?> ><?php echo ucfirst($state_list["state_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
              <?php if(isset($errors) && array_key_exists('state',$errors)){ echo "<span class='error'>".ucfirst($errors['state'])."</span>"; }?>
        </td>      
	</tr>
	
	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('city',$postvalue)){ $field_type =  $postvalue['city']; }else{ $field_type = $city_company;} ?>
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
		<option value="<?php echo $city_list['city_id']; ?>" <?php if($field_type == $city_list['city_id']) { echo 'selected=selected'; } elseif($city_list['city_id'] == DEFAULT_CITY) { echo 'selected=selected'; } ?> ><?php echo ucfirst($city_list["city_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
              <?php if(isset($errors) && array_key_exists('city',$errors)){ echo "<span class='error'>".ucfirst($errors['city'])."</span>"; }?>
        </td>      
	</tr>
	<?php } 
	else { ?>
		<input type="hidden" name="country" id="country" value="<?php echo $_SESSION['country_id']; ?>">
		<input type="hidden" name="state" id="state" value="<?php echo $_SESSION['state_id']; ?>">
		<input type="hidden" name="city" id="city" value="<?php echo $_SESSION['city_id']; ?>">
	<?php } ?>
			
	<tr>
	<td valign="top" width="25%"><label><?php echo __('booking_limit_per_day'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php echo __('enter_booking_limit'); ?>" id="booking_limit" maxlength="5" name="booking_limit" value="<?php if(isset($postvalue) && array_key_exists('booking_limit',$postvalue)){ echo $postvalue['booking_limit']; }?>" />
              <?php if(isset($errors) && array_key_exists('booking_limit',$errors)){ echo "<span class='error'>".ucfirst($errors['booking_limit'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	<tr>
	<td valign="top" width="25%"><label><?php echo __('address'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <textarea name="address" id="address" class="required" title="<?php echo __('enteraddress'); ?>" rows="7" cols="35"><?php if(isset($postvalue) && array_key_exists('address',$postvalue)){ echo $postvalue['address']; }?></textarea>
              <?php if(isset($errors) && array_key_exists('address',$errors)){ echo "<span class='error'>".ucfirst($errors['address'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	<tr>
	<td valign="top" width="25%"><label><?php echo __('photo_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="file" title="<?php echo __('profile_image_content'); ?>" id="photo" name="photo" value="<?php if(isset($postvalue) && array_key_exists('photo',$postvalue)){ echo $postvalue['photo']; }?>"  />
              <?php if(isset($errors) && array_key_exists('photo',$errors)){  echo "<span class='error'>".ucfirst($errors['photo'])."</span>";}?>
	</div>
	</td>   	
	</tr>

							
<tr>
	<td  class="empt_cel">&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
							<input type="text" name="submit_driver" value="form" style="display:none;"/>
                           <div class="new_button"><input type="button" value="<?php echo __('button_back'); ?>" onclick="window.location='<?php echo URL_BASE."manage/driver"; ?>'" /></div>
                            <div class="new_button">  <input type="submit" value="<?php echo __('submit' );?>" name="submit_driver" title="<?php echo __('submit' );?>" /></div>
                            <div class="new_button">   <input type="reset" onclick="change_state('<?php echo DEFAULT_COUNTRY; ?>','<?php echo DEFAULT_STATE; ?>');change_city('<?php echo DEFAULT_COUNTRY; ?>','<?php echo DEFAULT_STATE; ?>','<?php echo DEFAULT_CITY; ?>')" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
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

$("#firstname").focus();
	
//$("#dob").datepicker({maxDate:new Date(1997, 11,31)});
$("#dob").datepicker({ dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
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
    
    //Brand Type change function
    var brand = $('#brand_type').val();
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

function getcountry(company_id)
{
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
    
    
</script>
