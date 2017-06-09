<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
         <form name="editmanager_form" class="form" id="editmanager_form" action="" method="post" enctype="multipart/form-data">
         <table border="0" cellpadding="5" cellspacing="0" width="100%">
          
	   <tr>
               <td><h2 class="tab_sub_tit"><?php echo __('personalinform'); ?></h2></td>
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
           <td valign="top" width="20%"><label><?php echo __('firstname'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterfirstname'); ?>" name="firstname" id="firstname" value="<?php echo $firstname; ?>"  minlength="4" maxlength="45" />
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
              <input type="text" title="<?php echo __('enterlastname'); ?>" name="lastname" id="lastname" value="<?php echo $lastname; ?>" minlength="1"  maxlength="45"/>
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
              <input type="text" title="<?php echo __('enteremailaddress'); ?>" name="email" id="email" value="<?php echo $email; ?>"  maxlength="75" />
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
              <input type="text" title="<?php echo __('entermobileno'); ?>" name="phone" id="phone" value="<?php echo $phone; ?>" minlength="7" maxlength="16" />
              <span class="unit_mobile_code" id="mobile_code"></span>
              <input type="hidden" name="telephone_code" id="hid_mobile_code" value="">
              <?php if(isset($errors) && array_key_exists('phone',$errors)){ echo "<span class='error'>".ucfirst($errors['phone'])."</span>";} ?>
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
           <td valign="top" width="20%"><label><?php echo __('address'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
		<textarea name="address" id="address"  title="<?php echo __('enteraddress'); ?>" rows="7" cols="35"><?php echo $address; ?></textarea>

              <?php if(isset($errors) && array_key_exists('address',$errors)){ echo "<span class='error'>".ucfirst($errors['address'])."</span>";}?>
		   </div>
           </td>   	
           </tr>             
        <?php /*   <tr>
	   <td><?php echo ucfirst(__('companyinformation')); ?></td>
	   <td></td>	          
           </tr>
           */ ?>
    <?php if($_SESSION['user_type'] =='A' || $_SESSION['user_type'] =='DA')
	{ ?>	
		
	  <?php 
			if(isset($company_details[0]['company_id']) && !array_key_exists('company_name',$postvalue)){
				$company_name = $company_details[0]['company_id'];
			}else{
				if(isset($postvalue['company_name'])){
					$company_name = $postvalue['company_name'];
				}else{
					$company_name = "";
				}
			}
			?>      
	<tr>
	<?php $field_type =  $company_name; ?>
	<td valign="top" width="20%"><label><?php echo __('taxicompany'); ?></label><span class="star">*</span></td><td>
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
              <?php if(isset($errors) && array_key_exists('company_name',$errors)){ echo "<span class='error'>".ucfirst($errors['company_name'])."</span>"; } ?>
        </td>      
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
     <?php 
			if(isset($company_details[0]['login_country']) && !array_key_exists('country',$postvalue)){
				$login_country = $company_details[0]['login_country'];
			}else{
				if(isset($postvalue['country'])){
					$login_country = $postvalue['country'];
				}else{
					$login_country = "";
				}
			}
			?>     
	<tr>
	<?php $field_type =  $login_country; ?>
	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
		<input type="hidden" name="country" value = "<?php echo $company_details[0]['login_country']; ?>" />
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
			$state = $company_details[0]['login_state'];
		}else{
			if(isset($postvalue['state'])){
				$state = $postvalue['state'];
			}else{
				$state = "";
			}
		}
		?>    
	<tr>
	<?php $field_type =  $state; ?>
	<td valign="top" width="20%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="state_list">
		<select name="state" id="state" onchange="change_city_drop();">
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
	<?php  $field_type =  $city; ?>
	<td valign="top" width="20%"><label><?php echo __('city_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="city_list">
		<select name="city" id="city">
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($city_details as $city_list) {  ?>
		<option value="<?php echo $city_list['city_id']; ?>" <?php if($field_type == $city_list['city_id']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($city_list["city_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
              <?php if(isset($errors) && array_key_exists('city',$errors)){ echo "<span class='error'>".ucfirst($errors['city'])."</span>"; }?>
        </td>      
	</tr>
	
	<?php if(($_SESSION['user_type'] != 'S') && (TDISPATCH_VIEW==1) && isset($_SESSION['vbx_show']) && $_SESSION['vbx_show']==1 ) { ?>
	
	  <tr>
           <td valign="top" width="20%">&nbsp;</td>        
	   <td>
		   <div class="new_input_field">
			   <input type="checkbox" name="add_as_executive" value=1 <?php if((isset($_POST['add_as_executive']) && $_POST['add_as_executive'] ==1) || (isset($is_executive) && $is_executive ==1)){?> checked="checked" <?php }?> >&nbsp;<?php echo __('Add this user as callcenter executive'); ?>
               <input type="hidden" name="callcenter_id" value="<?php if(isset($is_executive_id)){ echo $is_executive_id;}else{ echo '0';} ?>"
		   </div>
           </td>   	
      </tr> 
<?php }?>
		
	<tr>
	<td>&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
                                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                            <div class="new_button">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_editmanager" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button">   <input type="reset" onclick="change_state('<?php echo isset($company_details[0]['login_country']) ? $company_details[0]['login_country'] : ''; ?>','<?php echo isset($company_details[0]['login_state']) ? $company_details[0]['login_state'] : ''; ?>');change_city('<?php echo isset($company_details[0]['login_country']) ? $company_details[0]['login_country'] : ''; ?>','<?php echo isset($company_details[0]['login_state']) ? $company_details[0]['login_state'] : ''; ?>','<?php echo isset($company_details[0]['login_city']) ? $company_details[0]['login_city'] : ''; ?>')" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
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
<?php if(isset($company_details[0]['company_id'])) { ?>	
		getcountry('<?php echo $company_details[0]['company_id']; ?>','<?php echo $company_details[0]['login_state']; ?>','<?php echo $company_details[0]['login_city']; ?>');
<?php } ?>

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
	change_state('','');	
	change_city('','','');	
	
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

 

function change_city(country_id,state_id,city_id)
{

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

/*function change_company()
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
} */

</script>
