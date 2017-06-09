<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
         <form name="editadmin_form" class="form" id="editadmin_form" action="" method="post" enctype="multipart/form-data">
         <table border="0" cellpadding="5" cellspacing="0" width="100%">
          
	   <tr>
	   <td colspan="2"><h2 class="tab_sub_tit"><?php echo __('personalinform'); ?></h2></td>
	   </tr>   
	   <?php 
			if(isset($user_details[0]['name']) && !array_key_exists('firstname',$postvalue)){
				$name = $user_details[0]['name'];
			}else{
				if(isset($postvalue['firstname'])){
					$name = $postvalue['firstname'];
				}else{
					$name = "";
				}
			}
		?>       
           <tr>
           <td valign="top" width="20%"><label><?php echo __('firstname'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enterfirstname'); ?>" name="firstname" id="firstname" value="<?php echo $name; ?>"  minlength="4" maxlength="30" />
              <?php if(isset($errors) && array_key_exists('firstname',$errors)){ echo "<span class='error'>".ucfirst($errors['firstname'])."</span>";}?>
		   </div>
           </td>   	
           </tr>        
			<?php 
			if(isset($user_details[0]['lastname']) && !array_key_exists('lastname',$postvalue)){
				$lastname = $user_details[0]['lastname'];
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
              <input type="text" title="<?php echo __('enterlastname'); ?>" name="lastname" id="lastname" value="<?php echo $lastname; ?>" minlength="1"  maxlength="30" />
              <?php if(isset($errors) && array_key_exists('lastname',$errors)){ echo "<span class='error'>".ucfirst($errors['lastname'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
           <?php 
			if(isset($user_details[0]['email']) && !array_key_exists('email',$postvalue)){
				$email = $user_details[0]['email'];
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
              <input type="text" title="<?php echo __('enteremailaddress'); ?>" name="email" id="email" value="<?php echo $email; ?>"  maxlength="50" />
              <?php if(isset($errors) && array_key_exists('email',$errors)){ echo "<span class='error'>".ucfirst($errors['email'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
            <?php 
			if(isset($user_details[0]['phone']) && !array_key_exists('phone',$postvalue)){
				$phone = $user_details[0]['phone'];
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
              <input type="text" title="<?php echo __('entermobileno'); ?>" name="phone" id="phone" value="<?php echo $phone; ?>" minlength="7" maxlength="20" />
              <span class="unit_mobile_code" id="mobile_code"></span>
              <input type="hidden" name="telephone_code" id="hid_mobile_code" value="">
              <?php if(isset($errors) && array_key_exists('phone',$errors)){ echo "<span class='error'>".ucfirst($errors['phone'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
            <?php 
			if(isset($user_details[0]['address']) && !array_key_exists('address',$postvalue)){
				$address = $user_details[0]['address'];
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
           <?php 
			if(isset($user_details[0]['login_country']) && !array_key_exists('country',$postvalue)){
				$country = $user_details[0]['login_country'];
			}else{
				if(isset($postvalue['country'])){
					$country = $postvalue['country'];
				}else{
					$country = "";
				}
			}
			?> 
	<tr>
	<?php $field_type =$country; ?>
	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
              <select name="country" id="country">
              <option value=""><?php echo __("select_label"); ?></option>
              <?php foreach($country_details as $country_list) { ?>
              <option value="<?php echo $country_list['country_id']; ?>" <?php if($field_type == $country_list['country_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($country_list['country_name']); ?></option>
              <?php } ?>
              </select>
        </div>
	</div>
	  <?php if(isset($errors) && array_key_exists('country',$errors)){ echo "<span class='error'>".ucfirst($errors['country'])."</span>";}?>

	</td>   	
	</tr>
	
	<tr>
	<?php $field_type =''; 
	
			if(isset($user_details[0]['user_type']) && !array_key_exists('user_type',$postvalue)){
				$user_type = $user_details[0]['user_type'];
			}else{
				if(isset($postvalue['user_type'])){
					$user_type = $postvalue['user_type'];
				}else{
					$user_type = "";
				}
			}
	
	$field_type =  $user_type; ?>
	<td valign="top" width="20%"><label><?php echo __('usertype_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
              <select name="user_type" id="user_type">              
				  <option value="S" <?php if($field_type == 'S') { ?>Selected<?php } ?> ><?php echo __("moderator"); ?></option>
				  <option value="DA" <?php if($field_type == 'DA') { ?>Selected<?php } ?> ><?php echo __("demo_admin"); ?></option>
              </select>
        </div>
	</div>
              <?php if(isset($errors) && array_key_exists('user_type',$errors)){ echo "<span class='error'>".ucfirst($errors['user_type'])."</span>";}?>

	</td>   	
	</tr>
<!--
	<tr>
	<?php $field_type =''; 
	if(isset($user_details[0]['login_state']) && !array_key_exists('state',$postvalue)){
				$login_state = $user_details[0]['login_state'];
			}else{
				if(isset($postvalue['state'])){
					$login_state = $postvalue['state'];
				}else{
					$login_state = "";
				}
			}
	
	$field_type =  $login_state;
	?>
	<td valign="top" width="20%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="state_list">
		<select name="state" id="state" onchange="change_city();">
		<option value="">--Select--</option>
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
	
	
	<tr>
	<?php $field_type =''; 
	if(isset($user_details[0]['login_city']) && !array_key_exists('city',$postvalue)){
				$login_city = $user_details[0]['login_city'];
			}else{
				if(isset($postvalue['city'])){
					$login_city = $postvalue['city'];
				}else{
					$login_city = "";
				}
			}
		$field_type =  $login_city;
	 ?>
	<td valign="top" width="20%"><label><?php echo __('city_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="city_list">
		<select name="city" id="city" onchange="change_company();">
		<option value="">--Select--</option>
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
-->	
		
	<tr>
	<td>&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
                    
                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
							<div class="new_button">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_editadmin" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            
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
telephone_code();
var field_val = $("#firstname").val();
$("#firstname").focus().val("").val(field_val);

	change_state();	
	change_city();	
	
	$("#phone" ).keyup(function() {
		//to allow left and right arrow key move
		if(event.which>=37 && event.which<=40)
		{
			return false;

		}
		//this.value = this.value.replace(/[`~!@#$%^&*\s_|\=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
		this.value = this.value.replace(/[`~!@#$%^&*()\s_|+\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
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

</script>
