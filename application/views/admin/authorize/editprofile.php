<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
	 <form method="post" enctype="multipart/form-data" class="form" name="editprofile" id="editprofile" action ="">
	 <table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
	<td><?php echo ucfirst(__('personalinform')); ?></td>
	<td></td>	          
	</tr>
     	<?php 
		if(isset($login_detail[0]['name']) && !array_key_exists('firstname',$postvalue)){
			$name = $login_detail[0]['name'];
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
              <input type="text" title="<?php echo __('enterfirstname'); ?>" name="firstname" id="firstname" class="required" value="<?php echo $name; ?>"  minlength="4" maxlength="30" />
              <?php if(isset($errors) && array_key_exists('firstname',$errors)){ echo "<span class='error'>".ucfirst($errors['firstname'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
			<?php 
			if(isset($login_detail[0]['lastname']) && !array_key_exists('lastname',$postvalue)){
				$lastname = $login_detail[0]['lastname'];
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
              <input type="text" title="<?php echo __('enterlastname'); ?>" name="lastname" class="required" id="lastname" value="<?php echo $lastname; ?>" minlength="4"  maxlength="50" />
              <?php if(isset($errors) && array_key_exists('lastname',$errors)){ echo "<span class='error'>".ucfirst($errors['lastname'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
           <?php 
			if(isset($login_detail[0]['email']) && !array_key_exists('email',$postvalue)){
				$email = $login_detail[0]['email'];
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
              <input type="text"  maxlength="30" minlength="3"  title="<?php echo __('enteremailaddress'); ?>" class="required email" id="email" name="email" value="<?php echo $email; ?>" />
              <?php if(isset($errors) && array_key_exists('email',$errors)){ echo "<span class='error'>".ucfirst($errors['email'])."</span>";}?>
		   </div>
	   </td>   	
       </tr> 	
       
	<?php 
		if(isset($login_detail[0]['phone']) && !array_key_exists('phone',$postvalue)){
			$phone = $login_detail[0]['phone'];
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
              <input type="text"  maxlength="30" minlength="7" class="required" title="<?php echo __('entermobileno'); ?>" id="phone" name="phone" value="<?php echo $phone; ?>" />
              <span class="unit_mobile_code" id="mobile_code"></span>
              <input type="hidden" name="telephone_code" id="hid_mobile_code" value="">
              <?php if(isset($errors) && array_key_exists('phone',$errors)){ echo "<span class='error'>".ucfirst($errors['phone'])."</span>";}?>
		   </div>
	   </td>   	
       </tr> 	
            <?php 
			if(isset($login_detail[0]['address']) && !array_key_exists('address',$postvalue)){
				$address = $login_detail[0]['address'];
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
              <textarea name="address" id="address" class="required" title="<?php echo __('enteraddress'); ?>" rows="7" cols="35"><?php echo $address; ?></textarea>
              <?php if(isset($errors) && array_key_exists('address',$errors)){ echo "<span class='error'>".$errors['address']."</span>";}?>
		   </div>
	   </td>   	
           </tr>  
			<?php 
			if(isset($login_detail[0]['login_country']) && !array_key_exists('country',$postvalue)){
				$country = $login_detail[0]['login_country'];
			}else{
				if(isset($postvalue['country'])){
					$country = $postvalue['country'];
				}else{
					$country = "";
				}
			}
			?>    
	<tr>
	<?php $field_type =  $country; ?>
	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="formRight">
	<div class="new_input_field">
              <select name="country" id="country" class="required" title="<?php echo __('select_the_country'); ?>">
              <option value="">--Select--</option>
              <?php foreach($country_details as $country_list) { ?>
              <option value="<?php echo $country_list['country_id']; ?>" <?php if($field_type == $country_list['country_id']) { echo 'selected=selected'; } ?>><?php echo $country_list['country_name']; ?></option>
              <?php } ?>
              </select>
        </div>
	</div>
              <?php if(isset($errors) && array_key_exists('country',$errors)){ echo "<span class='error'>".ucfirst($errors['country'])."</span>";}?>

	</td>   	
	</tr>
		<?php 
			if(isset($login_detail[0]['login_state']) && !array_key_exists('state',$postvalue)){
				$state = $login_detail[0]['login_state'];
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
	<!--<div class="selector" id="uniform-user_type"> -->
	<div class="new_input_field" id="state_list">
		<select name="state" id="state" class="required" onchange="change_city_drop()" title="<?php echo __('select_the_state'); ?>" >
		<option value="">--Select--</option>
		<?php
		foreach($state_details as $state_list) {  ?>
		<option value="<?php echo $state_list['state_id']; ?>" <?php if($field_type == $state_list['state_id']) { echo 'selected=selected'; } ?> ><?php echo $state_list["state_name"]; ?></option>
		<?php	} ?>
		</select>
	</div>	
		<!--</div>--></div>
              <?php if(isset($errors) && array_key_exists('state',$errors)){ echo "<span class='error'>".ucfirst($errors['state'])."</span>"; }?>
        </td>      
	</tr>
	<?php 
		if(isset($login_detail[0]['login_city']) && !array_key_exists('city',$postvalue)){
			$login_city = $login_detail[0]['login_city'];
		}else{
			if(isset($postvalue['city'])){
				$login_city = $postvalue['city'];
			}else{
				$login_city = "";
			}
		}
		?>    
	
	<tr>
	<?php  $field_type =  $login_city; ?>
	<td valign="top" width="20%"><label><?php echo __('city_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<!--<div class="selector" id="uniform-user_type">
	<span><?php //echo __('select_label'); ?></span> -->
	<div class="new_input_field" id="city_list">
		<select name="city" id="city" class="required" title="<?php echo __('select_the_city'); ?>">
		<option value="">--Select--</option>
		<?php
		foreach($city_details as $city_list) {  ?>
		<option value="<?php echo $city_list['city_id']; ?>" <?php if($field_type == $city_list['city_id']) { echo 'selected=selected'; } ?> ><?php echo $city_list["city_name"]; ?></option>
		<?php	} ?>
		</select>
	</div>	
		<!--</div>--></div>
              <?php if(isset($errors) && array_key_exists('city',$errors)){ echo "<span class='error'>".ucfirst($errors['city'])."</span>"; }?>
        </td>      
	</tr>
		<tr>
			<td valign="top" width="20%"><label><?php echo __('image'); ?> </label></td>   			
			<td> 
				<div class="new_input_field">
					<input type="file" class="imageonly" name="image" id="photo" title="<?php echo __('select_taxi_image'); ?>" value="">				
				</div>
				<div class="site_logo" >
					<?php  if(file_exists($_SERVER["DOCUMENT_ROOT"].'/public/'.UPLOADS.'/company/'.$login_detail[0]['_id'].'.png')){  ?> 
						<img width="75" height="75" src="<?php echo URL_BASE.COMPANY_IMG_IMGPATH.$login_detail[0]['_id'].'.png?q='.time();?>"/>
					<?php }else{ ?>
						<img width="75" height="75"  src="<?php echo URL_BASE;?>public/common/images/no_image.png?q="<?php time();?>/>
					<?php } ?>
				</div><br />
				<?php if(isset($errors) && array_key_exists('image',$errors)){ echo "<span class='error'>".ucfirst($errors['image'])."</span>";}?>				
			</td>
		</tr>


		<td class="empt_cel">&nbsp;</td>
		<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
		</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
                            <br />
                    
                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                             <div class="new_button">  <input type="submit" <?php if($email==SUPERADMIN_EMAIL) { ?> id="disable" <?php } ?> value="<?php echo __('submit' );?>" name="submit_editprofile" title="<?php echo __('submit' );?>" /></div>
                            <div class="new_button">   <input type="reset" onclick="change_state('<?php echo isset($login_detail[0]['login_country']) ? $login_detail[0]['login_country'] : ''; ?>','<?php echo isset($login_detail[0]['login_state']) ? $login_detail[0]['login_state'] : ''; ?>');change_city('<?php echo isset($login_detail[0]['login_country']) ? $login_detail[0]['login_country'] : ''; ?>','<?php echo isset($login_detail[0]['login_state']) ? $login_detail[0]['login_state'] : ''; ?>','<?php echo isset($login_detail[0]['login_city']) ? $login_detail[0]['login_city'] : ''; ?>')" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                           
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
	jQuery("#editprofile").validate();
var field_val = $("#old_password").val();
$("#old_password").focus().val("").val(field_val);
	change_state('','');	
	change_city('','','');	
	
	$("#phone" ).keyup(function() {
		//to allow left and right arrow key move
		if(event.which>=37 && event.which<=40)
		{
			return false;

		}
		this.value = this.value.replace(/[`~!@#$%^&*()\s_|+\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
		//this.value = this.value.replace(/[`~!@#$%^&*\s_|\=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
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
			telephone_code()
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

	var stateid= $("#state").val();
	var countryid= $("#country").val();
	var cityid= $("#city").val();
	if(country_id != '' && state_id != '' && city_id != '') {
		countryid = country_id;
		stateid= state_id;
		cityid= city_id;
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


</script>

