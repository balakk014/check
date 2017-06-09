<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 
         <form name="editcity_form" class="form" id="editcity_form" action="" method="post" enctype="multipart/form-data">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">                             

	<tr>
	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
		<?php 
			if(isset($model_details[0]['city_countryid']) && !array_key_exists('country_name',$postvalue)){
				$country_name = $model_details[0]['city_countryid'];
			}else{
				if(isset($postvalue['country_name'])){
					$country_name = $postvalue['country_name'];
				}else{
					$country_name = "";
				}
			}
		?> 
	<?php $field_type =  $country_name; ?>

	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
              <select name="country_name" id="country_name">
              <?php foreach($motor_details as $listings) { ?>
              <option value="<?php echo $listings['country_id']; ?>" <?php if($field_type == $listings['country_id'] ) {  echo 'selected=selected'; } ?> ><?php echo ucfirst($listings['country_name']); ?></option>
              <?php } ?>
              </select>
              </div>
              </div>
               <?php if(isset($errors) && array_key_exists('coutry_name',$errors)){ echo "<span class='error'>".ucfirst($errors['country_name'])."</span>";}?>
		 <input type="hidden" name="city_countryid" value="<?php echo $model_details[0]['country_id']; ?>" >
	</td>   	
	</tr>


	<tr>
	<?php $field_type =''; 
	if(isset($model_details[0]['city_stateid']) && !array_key_exists('state_name',$postvalue)){
		$city_stateid = $model_details[0]['city_stateid'];
		}else{
			if(isset($postvalue['state_name'])){
				$city_stateid = $postvalue['state_name'];
			}else{
				$city_stateid = "";
			}
		}
		$field_type =  $city_stateid;
	?>
	<td valign="top" width="20%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="state_list">
		<select name="state_name" id="state_name" >
		<option value=""><?php echo __('select_label'); ?></option>
		<?php
		foreach($state_details as $state_list) {  ?>
		<option value="<?php echo $state_list['state_id']; ?>" <?php if($field_type == $state_list['state_id']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($state_list["state_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
              <?php if(isset($errors) && array_key_exists('state_name',$errors)){ echo "<span class='error'>".ucfirst($errors['state_name'])."</span>"; }?>
	<input  type="hidden" value="<?php echo $field_type; ?>"  name="state_id"  id="state_id">         
        </td>     
         
	</tr>
	<?php
	if(isset($model_details[0]['city_name']) && !array_key_exists('city_name',$postvalue)){
		$city_name = $model_details[0]['city_name'];
	}else{
		if(isset($postvalue['city_name'])){
			$city_name = $postvalue['city_name'];
		}else{
			$city_name = "";
		}
	}
	?>
		
	<tr>
	<td valign="top" width="20%"><label><?php echo __('city_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="30" minlength="2"  title="<?php echo __('enterthecityname'); ?>" id="city_name" name="city_name" value="<?php echo $city_name; ?>" />
              <?php if(isset($errors) && array_key_exists('city_name',$errors)){ echo "<span class='error'>".ucfirst($errors['city_name'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	<?php
	if(isset($model_details[0]['zipcode']) && !array_key_exists('zipcode',$postvalue)){
		$zipcode = $model_details[0]['zipcode'];
	}else{
		if(isset($postvalue['zipcode'])){
			$zipcode = $postvalue['zipcode'];
		}else{
			$zipcode = "";
		}
	}
	?>
	<tr>
	<td valign="top" width="20%"><label><?php echo __('zipcode'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  maxlength="30" minlength="2"  title="<?php echo __('enterthezipcode'); ?>" id="zipcode" name="zipcode" value="<?php echo $zipcode; ?>" />
              <?php if(isset($errors) && array_key_exists('zipcode',$errors)){ echo "<span class='error'>".ucfirst($errors['zipcode'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	<?php
	if(isset($model_details[0]['city_model_fare']) && !array_key_exists('city_model_fare',$postvalue)){
		$city_model_fare = $model_details[0]['city_model_fare'];
	}else{
		if(isset($postvalue['city_model_fare'])){
			$city_model_fare = $postvalue['city_model_fare'];
		}else{
			$city_model_fare = "";
		}
	}
	?>
	<tr>
	<td valign="top" width="20%"><label><?php echo __('city_model_fare'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  minlength="2"  title="<?php echo __('enter_city_model_fare'); ?>" id="city_model_fare" name="city_model_fare" value="<?php echo $city_model_fare; ?>" maxlength="5" max="100"  oncopy="return false;" onpaste="return false;" oncut="return false;" onkeypress="return onlyDotsAndNumbers(this,event)" />
              <?php if(isset($errors) && array_key_exists('city_model_fare',$errors)){ echo "<span class='error'>".ucfirst($errors['city_model_fare'])."</span>";}?>
	</div>
	</td>   	
	</tr>
	
	<tr>
	<td class="empt_cel">&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
                    
                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
							<div class="new_button">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_editcity" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button">   <input type="reset" onclick="change_state('<?php echo isset($model_details[0]['city_countryid']) ? $model_details[0]['city_countryid'] : ''; ?>','<?php echo isset($model_details[0]['city_stateid']) ? $model_details[0]['city_stateid'] : ''; ?>');" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            
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

 $("#country_name").focus(); 

change_state('','');	

		
	
});

    $("#country_name").change(function() {

      		var countryid= $("#country_name").val();
      		var state_id= $("#state_id").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getstatelist",
			type:"get",
			data:"country_id="+countryid+"&state_id="+state_id,
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

function change_state(country_id, stateid)
{
	
	var countryid= $("#country_name").val();
	var state_id= $("#state_id").val();
	if(country_id != '' && stateid != '') {
			countryid = country_id;
			state_id= stateid;
		}

	  $.ajax({
		url:"<?php echo URL_BASE;?>add/getstatelist",
		type:"get",
		data:"country_id="+countryid+"&state_id="+state_id,
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

function onlyDotsAndNumbers(txt, event) {
    var charCode = (event.which) ? event.which : event.keyCode   

	if(txt.value.length == 0) {
		if (charCode == 46) {
			if (txt.value.indexOf(".") < -1)
			    return true;
			else
			    return false;
		}

	}

    /* if (charCode == 46) {
        if (txt.value.indexOf(".") < 0)
            return true;
        else
            return false;
    } */
	
    if (txt.value.indexOf(".") > 0) {
        var txtlen = txt.value.length;
        var dotpos = txt.value.indexOf(".");
        if (charCode != 46 && charCode != 8 && (txtlen - dotpos) > 2)
            return false;
    }

    if (charCode > 31 && charCode != 46 && (charCode < 48 || charCode > 57)) {
        return false;
	}

    return true;
}    
</script>
