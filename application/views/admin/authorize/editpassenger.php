<?php defined('SYSPATH') OR die("No direct access allowed."); ?>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
	 <form method="post" enctype="multipart/form-data" class="form" name="editprofile" id="editprofile" action ="">
	 <table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
	<td colspan="2"><h2 class="tab_sub_tit"><?php echo ucfirst(__('personalinform')); ?></h2></td>          
	</tr>
     	<?php 
		if(isset($login_detail[0]['name']) && !array_key_exists('name',$postvalue)){
			$name = $login_detail[0]['name'];
		}else{
			if(isset($postvalue['name'])){
				$name = $postvalue['name'];
			}else{
				$name = "";
			}
		}
		?>  
           <tr>
           <td valign="top" width="20%"><label><?php echo ucfirst(__('name')); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_name'); ?>" name="name" id="name" value="<?php echo $name; ?>"  minlength="4" maxlength="30" />
              <?php if(isset($errors) && array_key_exists('name',$errors)){ echo "<span class='error'>".ucfirst($errors['name'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
           <?php 
			if(isset($login_detail[0]['email']) && !array_key_exists('email',$postvalue) ){
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
			   <input type="text"  maxlength="30" minlength="3"  title="<?php echo __('enterfirstname'); ?>" id="email" name="email" value="<?php echo $email; ?>" />
			   <?php if(isset($errors) && array_key_exists('email',$errors)){ echo "<span class='error'>".ucfirst($errors['email'])."</span>";}?>
			</div>
	   </td>   	
       </tr>
<?php
$country_code = (!empty($login_detail[0]['country_code'])) ? $login_detail[0]['country_code'].'-' : '';
?>
			<?php 
			if(isset($login_detail[0]['phone']) && !array_key_exists('phone',$postvalue) ){
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
              <input type="text"  maxlength="20" minlength="7" title="<?php echo __('entermobileno'); ?>" placeholder="<?php echo __('phone_placeholder_info'); ?>" id="phone" name="phone" value="<?php echo $phone; ?>" />
              <?php 
				if(isset($login_detail[0]['country_code']) && !array_key_exists('country_code',$postvalue)){
					$country_code = $login_detail[0]['country_code'];
				}else{
					$country_code = "";
				}
			?>  
              <span class="unit_mobile_code" id="mobile_code"><?php echo $country_code; ?></span>
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
           <td valign="top" width="20%"><label><?php echo __('address'); ?></label></td>        
	   <td>
		   <div class="new_input_field">	
              <textarea name="address" id="address" class="required" title="<?php echo __('enteraddress'); ?>" rows="7" cols="35"><?php echo $address; ?></textarea>
              <?php if(isset($errors) && array_key_exists('address',$errors)){ echo "<span class='error'>".$errors['address']."</span>";}?>
		   </div>
		   <input type="hidden" title="<?php echo __('enter_discounts_passenger'); ?>" class="required numbersdots numbersonly" name="discount" id="discount" value="0" maxlength="4"  />
	   </td>   	
           </tr>  
            <?php 
			if(isset($login_detail[0]['discount']) && !array_key_exists('discount',$postvalue)){
				$discount = $login_detail[0]['discount'];
			}else{
				if(isset($postvalue['discount'])){
					$discount = $postvalue['discount'];
				}else{
					$discount = "";
				}
			}
			?>     
		<!-- <tr>
           <td valign="top" width="20%"><label><?php //echo __('discounts_passenger'); ?></label></td>        
	   <td>
		   <div class="new_input_field">
              <input type="text" title="<?php //echo __('enter_discounts_passenger'); ?>" class="required numbersdots numbersonly" name="discount" id="discount" value="<?php echo $discount; ?>" maxlength="4"  />
              <?php //if(isset($errors) && array_key_exists('discount',$errors)){ echo "<span class='error'>".ucfirst($errors['discount'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 	-->
<?php if($_SESSION['user_type'] =='A' || $_SESSION['user_type'] =='DA')
	{ ?>	
	<?php 
		if(isset($login_detail[0]['passenger_cid']) && !array_key_exists('passenger_cid',$postvalue)){
			$company_name = $login_detail[0]['passenger_cid'];
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
	<td valign="top" width="20%"><label><?php echo __('taxicompany'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight new_input_field">
	<div class="selector fullwidthsel" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="taxicompany_list">
		<select name="company_id" id="company_id">
		<option value="">All</option>
		<?php
		foreach($taxicompany_details as $company_list) {  ?>
		<option value="<?php echo $company_list['cid']; ?>" <?php if($field_type == $company_list['cid']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($company_list["company_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
              <?php if(isset($errors) && array_key_exists('company_name',$errors)){ echo "<span class='error'>".ucfirst($errors['company_name'])."</span>"; }?>
        </td>      
	</tr>	
	<?php } ?>
		<td class="empt_cel">&nbsp;</td>
		<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
		</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
                    
                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
							<div class="new_button">  <input type="submit" value="<?php echo __('submit' );?>" name="submit_editprofile" title="<?php echo __('submit' );?>" /></div>
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
	 
	$("#phone" ).keyup(function() {
		//to allow left and right arrow key move
		if(event.which>=37 && event.which<=40)
		{
			return false;

		}
		this.value = this.value.replace(/[`~!@#$%^&*\s_|\=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
	});
	 

	 var field_val = $("#name").val();
	$("#name").focus().val("").val(field_val);
	change_state();	
	change_city();	
		
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

