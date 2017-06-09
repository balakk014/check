<?php defined('SYSPATH') OR die("No direct access allowed."); ?>

 <div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 
         <form name="addcity_form" id="addmotor_form" class="form" action="" method="post" enctype="multipart/form-data" data-form="server-form">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">                                                  

	<tr>
	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label><span class="star">*</span></td>        
	<td>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('country_name',$postvalue)){ $field_type =  $postvalue['country_name']; } ?>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
              <select name="country_name" id="country_name">
              <?php foreach($motor_details as $listings) { ?>
              <option value="<?php echo $listings['country_id']; ?>" <?php if($field_type == $listings['country_id']) { echo 'selected=selected'; }elseif($field_type == '' && $listings['country_id'] == DEFAULT_COUNTRY) { echo 'selected=selected'; } ?>><?php echo ucfirst($listings['country_name']); ?></option>
              <?php } ?>
              </select>
        </div>
	</div>      
              <?php if(isset($errors) && array_key_exists('country_name',$errors)){ echo "<span class='error'>".ucfirst($errors['country_name'])."</span>";}?>

	</td>   	
	</tr> 
	
	<tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('state_name',$postvalue)){ $field_type =  $postvalue['state_name']; } ?>
	<td valign="top" width="20%"><label><?php echo __('state_label'); ?></label><span class="star">*</span></td>
	<td>
	<div class="formRight">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="state_list">
		<select name="state_name" id="state_name" >		
		<?php
		foreach($state_details as $state_list) {  ?>
		<option value="<?php echo $state_list['state_id']; ?>" <?php if($field_type == $state_list['state_id']) { echo 'selected=selected'; }elseif($field_type == '' && $state_list['state_id'] == DEFAULT_STATE) { echo 'selected=selected'; } ?> ><?php echo ucfirst($state_list["state_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
              <?php if(isset($errors) && array_key_exists('state_name',$errors)){ echo "<span class='error'>".ucfirst($errors['state_name'])."</span>"; }?>
        </td>      
	</tr>
		
	<tr>
	<td valign="top" width="20%"><label><?php echo __('city_label'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php echo __('enterthecityname'); ?>" name="city_name" id="city_name" value="<?php if(isset($postvalue) && array_key_exists('city_name',$postvalue)){ echo $postvalue['city_name']; }?>"  minlength ="2" maxlength="30" />
              <?php if(isset($errors) && array_key_exists('city_name',$errors)){ echo "<span class='error'>".ucfirst($errors['city_name'])."</span>";}?>
	</div>
	</td>   	
	</tr>

	<tr>
	<td valign="top" width="20%"><label><?php echo __('zipcode'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php echo __('enterthezipcode'); ?>" name="zipcode" id="zipcode" value="<?php if(isset($postvalue) && array_key_exists('zipcode',$postvalue)){ echo $postvalue['zipcode']; }?>"  minlength ="2" maxlength="30" />
              <?php if(isset($errors) && array_key_exists('zipcode',$errors)){ echo "<span class='error'>".ucfirst($errors['zipcode'])."</span>";}?>
	</div>
	</td>   	
	</tr> 

	<tr>
	<td valign="top" width="20%"><label><?php echo __('city_model_fare'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text" title="<?php echo __('enter_city_model_fare'); ?>" name="city_model_fare" id="city_model_fare" value="<?php if(isset($postvalue) && array_key_exists('city_model_fare',$postvalue)){ echo $postvalue['city_model_fare']; }?>"  minlength ="1" maxlength="5" max="100" oncopy="return false;" onpaste="return false;" oncut="return false;" onkeypress="return onlyDotsAndNumbers(this,event)" />
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
							<input type="text" name="submit_addcity" value="form" style="display:none;"/>
                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
							<div class="new_button">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addcity" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button">   <input type="reset" onclick="change_state('<?php echo DEFAULT_COUNTRY; ?>','<?php echo DEFAULT_STATE; ?>');" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            
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

	/*var stateid= $("#state_name").val();
	if(stateid == '')
	{ */
		change_state('','');	
	//}
		
	
});

    $("#country_name").change(function() {

      		var countryid= $("#country_name").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getstatelist",
			type:"get",
			data:"country_id="+countryid,
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

function change_state(country_id,state_id)
{

     		var countryid= $("#country_name").val();
     		var stateid= $("#state_name").val();
     		if(country_id != '' && state_id != '') {
				countryid = country_id;
				stateid= state_id;
			}

		  $.ajax({
			url:"<?php echo URL_BASE;?>add/getstatelist",
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

    if (charCode == 46) {
        if (txt.value.indexOf(".") < 0)
            return true;
        else
            return false;
    }
	
    if (txt.value.indexOf(".") > 0) {
        var txtlen = txt.value.length;
        var dotpos = txt.value.indexOf(".");
        if ((txtlen - dotpos) > 2)
            return false;
    }

    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}
    
</script>
