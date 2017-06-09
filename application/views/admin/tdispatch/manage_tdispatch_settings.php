<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<?php /*
<!--Map & Location Autocomplete JS-->
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery-1.6.3.min.js"></script>
<script src="http://maps.google.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&libraries=places,geometry&amp;sensor=false&callback=initialize" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/gmaps.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script> */ ?>
<!--Map & Location Autocomplete JS-->

<?php
$labelname_select = '';
$match_vehicletype = '';
$hide_customer = '';
$hide_droplocation = '';
$hide_fare = '';

if(count($tdispatch_settings)>0) { 

	$labelname_select = isset($tdispatch_settings[0]['labelname']) ? explode(',',$tdispatch_settings[0]['labelname']):''; 
	$hide_customer = isset($tdispatch_settings[0]['hide_customer']) ? $tdispatch_settings[0]['hide_customer']:''; 
	$hide_droplocation = isset($tdispatch_settings[0]['hide_droplocation']) ? $tdispatch_settings[0]['hide_droplocation']:'';
	$hide_fare = isset($tdispatch_settings[0]['hide_fare']) ? $tdispatch_settings[0]['hide_fare']:'';  

}
?>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">
       
	<form method="POST" class="form" action="" name = "tdispatch_setting" id="tdispatch_setting">
		<table class="0" cellpadding="5" cellspacing="0" width="99%">
		   <tr class="titlebold">
		   <td class="titlebold"><?php echo ucfirst(__('tdispatch_setting')); ?></td>
		   <td></td>	          
		   </tr>

		    <tr>
			<td valign="top" width="20%"><label><?php echo __('controller_setting');?></label><span class="star">*</span></td>   	
			<td> <?php  
				//$labelname_type = array( "1" => "Server will select the nearest taxi and dispatch","2" => "Operator able to select the taxi"); 
				$labelname_type = array( "1" => __('auto_label'),"2" => __('manual_label'));
				?>
				   <div id="uniform-user_type">
				   <?php /*<select name="labelname" title="<?php echo __('select_operating_settings'); ?>">
					<option value=""><?php echo __('select_label'); ?></option>
					<?php foreach($labelname_type as $labelname_key => $labelname_value) { ?>
					<option value="<?php echo $labelname_key; ?>" <?php if($labelname_select == $labelname_key){ echo "selected='selected'";} ?>><?php echo $labelname_value; ?></option>
					<?php } ?>
				    </select> */ ?>
				    <?php foreach($labelname_type as $labelname_key => $labelname_value) { ?>
				    <input type="checkbox" name="labelname[]" <?php if(!empty($labelname_select) && in_array($labelname_key,$labelname_select)){ echo "checked='checked'";} ?> value="<?php echo $labelname_key; ?>"> <?php echo $labelname_value; ?>
				    <?php } ?>
				    </div>
				    <?php				    
				     if(isset($errors) && array_key_exists('labelname',$errors)){ echo "<span class='error'>".ucfirst(__('select_operating_settings'))."</span>";}?>
				     <input type="hidden" name="hide_customer" id="hide_customer"  value="1"   />
				     <input type="hidden" name="hide_droplocation" id="hide_droplocation"  value="1"   />
			</td>
		    </tr>
		    
            <tr>
				<td>&nbsp;</td>
				<td><?php echo __('dispatch_setting_description'); ?></td>
			</tr>
		   <?php /*<tr>
		   <td valign="top" width="20%"><label><?php echo __('hide_customer_inform'); ?></label></td>        
				   <td>
				   <div class="new_input_field">
			   	  <input type="checkbox" name="hide_customer" id="hide_customer" <?php if($hide_customer == 1) { echo 'checked'; } ?> value="1"   />
			   </div>
		   </td>   	
		   </tr>

		   <tr>
		   <td valign="top" width="20%"><label><?php echo __('hide_customer_droplocation'); ?></label></td>        
				   <td>
				   <div class="new_input_field">
			   	  <input type="checkbox" name="hide_droplocation" id="hide_droplocation" <?php if($hide_droplocation == 1) { echo 'checked'; } ?> value="1"   />
			   </div>
		   </td>   	
		   </tr> */?>


		<tr>
			<td>&nbsp;</td>
			<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
		</tr>                                 
		<tr>
			<td>&nbsp;</td>
			<td colspan="">
			<br />
			
			<div class="new_button">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
			<div class="new_button">  <input type="submit" value="<?php echo __('submit' );?>" name="submit_settings" title="<?php echo __('submit' );?>" /></div>
			<div class="clr">&nbsp;</div>
		</td>
		</tr> 
                </table>
                           
                </form>
                <br/><br/>
              
        </div>

        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt" ></div></div>
</div>

</div>
<script language="javascript" type="text/javascript">

 $(document).ready(function(){
	 
var field_val = $("#smtp_host").val();
$("#smtp_host").focus().val("").val(field_val);
});

</script>
