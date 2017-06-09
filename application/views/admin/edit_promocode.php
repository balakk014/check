<?php defined('SYSPATH') OR die("No direct access allowed."); 
if(TIMEZONE)
{
	$current_time = convert_timezone('now',TIMEZONE);
	$current_date = explode(' ',$current_time);
	$start_time = $current_date[0].' 00:00:01';
	$end_time = $current_date[0].' 23:59:59';
	$date = $current_date[0].' %';
}
else
{
	$current_time =	date('Y-m-d H:i:s');
	$start_time = date('Y-m-d').' 00:00:01';
	$end_time = date('Y-m-d').' 23:59:59';
	$date = date('Y-m-d %');
}	
//print_r($promocode_details); exit;
?>

<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<!-- time picker start-->
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<!-- time picker start-->

 <div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 

         <form name="addmotor_form" id="addmotor_form" class="form" action="" method="post" enctype="multipart/form-data">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">                                                  

	

           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('promocode'); ?></label></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_promocode'); ?>" readonly name="promocode" id="promocode" value="<?php echo isset($promocode_details[0]['promocode']) &&!array_key_exists('promocode',$postvalue)? trim($promocode_details[0]['promocode']):$postvalue['promocode']; ?>"  />
              <?php if(isset($errors) && array_key_exists('promocode',$errors)){ echo "<span class='error'>".ucfirst($errors['promocode'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
           <?php 
			if(isset($promocode_details[0]['promo_discount']) && !array_key_exists('promo_discount',$postvalue)){
				$promo_discount = $promocode_details[0]['promo_discount'];
			}else{
				if(isset($postvalue['promo_discount'])){
					$promo_discount = $postvalue['promo_discount'];
				}else{
					$promo_discount = "";
				}
			}
			?> 		
           <tr>
           <td valign="top" width="20%"><label><?php echo __('promocode_discount'); ?></label></td>
	       <td>
		   <div class="new_input_field">
              <input type="text" title="<?php echo __('enter_promo_discount'); ?>" readonly class="required numbersdots" name="promo_discount" id="promo_discount" value="<?php echo $promo_discount; ?>" minlength="1" maxlength="8"  />
              <?php if(isset($errors) && array_key_exists('min_km',$errors)){ echo "<span class='error'>".ucfirst($errors['promo_discount'])."</span>";}?>
		   </div>
           </td>
           </tr>
            <?php 
            //print_r($promocode_details); exit; 
			if(isset($promocode_details[0]['start_date']) && !array_key_exists('start_date',$postvalue)){
				$start_date = $promocode_details[0]['start_date'];
			}else{
				if(isset($postvalue['start_date'])){
					$start_date = $postvalue['start_date'];
				}else{
					$start_date = "";
				}
			}
			?> 
            <tr>
           <td valign="top" width="20%"><label><?php echo __('start_date'); ?></label></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text"   name="start_date" id="start_date" value="<?php echo $start_date; ?>"    />
		   </div>
           </td>   	
           </tr>  
           <?php 
			if(isset($promocode_details[0]['expire_date']) && !array_key_exists('expire_date',$postvalue)){
				$expire_date = $promocode_details[0]['expire_date'];
			}else{
				if(isset($postvalue['expire_date'])){
					$expire_date = $postvalue['expire_date'];
				}else{
					$expire_date = "";
				}
			}
			?> 
             <tr>
           <td valign="top" width="20%"><label><?php echo __('expire_date'); ?></label></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text" class="start_exp_valid start_exp_timevalid" name="expire_date" id="expire_date" value="<?php echo $expire_date; ?>"  />
              <?php if(isset($errors) && array_key_exists('expire_date',$errors)){ echo "<span class='error'>".ucfirst($errors['expire_date'])."</span>";}?>
		   </div>
           </td>   	
           </tr> 
           <?php 
			if(isset($promocode_details[0]['promo_limit']) && !array_key_exists('promo_limit',$postvalue)){
				$promo_limit = $promocode_details[0]['promo_limit'];
			}else{
				if(isset($postvalue['promo_limit'])){
					$promo_limit = $postvalue['promo_limit'];
				}else{
					$promo_limit = "";
				}
			}
			?> 
           <tr>
           <td valign="top" width="20%"><label><?php echo __('promo_limit'); ?></label></td>        
	       <td>
		   <div class="new_input_field">
              <input type="text"  class="required onlynumbers"  name="promo_limit" id="promo_limit" value="<?php echo $promo_limit; ?>"  minlength="1" maxlength="3"  />
              <?php if(isset($errors) && array_key_exists('promo_limit',$errors)){ echo "<span class='error'>".ucfirst($errors['promo_limit'])."</span>";}?>
              <span class='error' id="limit_error"></span>
		   </div>
           </td>   	
           </tr>
			<?php
				$used_count = $unused_count = 0; $used_array = $unused_array = array();
				$unused_count = $promocode_details[0]['promo_limit'];
				if(isset($promocode_details[0]['promo_used_details']) && $promocode_details[0]['promo_used_details'] != "") {
					$data = unserialize($promocode_details[0]['promo_used_details']);
					foreach($data as $c) {
						if($c) {
							//$used_count++;
							$used_count = $used_count+$c;
							$unused_count = $unused_count - $c;
						} else {
							//$unused_count++;
						}
					}
				}
			?>
			<tr>
				<td valign="top" width="20%"><label><?php echo __('user_promo_used_count'); ?></label></td>        
				<td>
					<div class="new_input_field">
						<?php echo $used_count; ?>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top" width="20%"><label><?php echo __('user_promo_unused_count'); ?></label></td>        
				<td>
					<div class="new_input_field">
						<?php echo $unused_count; ?>
					</div>
				</td>
			</tr>
			<tr>
				<td></td>
			<td>
              <label><input type="checkbox" name="resend" id="resend"  />		<?php echo __('resend_promocode'); ?></label> 
           </td> 
       
           </tr> 
           
	</tr>                         
                    <tr>
			<td width="20%">&nbsp;</td>
                        <td colspan="">
                    
                            <div class="new_button"><input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
							<div class="new_button"><input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addmodel" onclick = "return check_limit(); " title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="new_button"><input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            
                            <div class="clr">&nbsp;</div>
                        </td>
                    </tr>            

           

		</table>



           

	   </div>

        </form>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>  
<script type="text/javascript">
$(document).ready(function(){
 $("#addmotor_form").validate();
$("#credits").focus();	
//jQuery("#addmotor_form").validate();	 
});


function check_limit()
{
	var expire_date = jQuery("#expire_date").val();
	var promo_discount = jQuery("#promo_limit").val();
	var current_discount = <?php echo isset($promocode_details[0]['promo_limit'])?$promocode_details[0]['promo_limit']:'0'; ?>;
	if(promo_discount < current_discount)
	{
		jQuery("#limit_error").html("<?php echo __('promo_limit_msg'); ?>");
		return false;
	}
	else
	{
		return true;
	}
	
	
}



	$( "#start_date" ).datetimepicker({
	minDate:0,
	showTimepicker:true,
	showSecond: true,
	timeFormat: 'hh:mm:ss',
	dateFormat: 'yy-mm-dd',
	stepHour: 1,
	stepMinute: 1,
	minDateTime : new Date(), 
	stepSecond: 1,
      onClose: function( selectedDate ) {
        $( "#end_date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#expire_date" ).datetimepicker({
		showTimepicker:true,
		showSecond: true,
		timeFormat: 'hh:mm:ss',
		dateFormat: 'yy-mm-dd',
		stepHour: 1,
		stepMinute: 1,
		minDateTime : new Date(),
		stepSecond: 1,
      onClose: function( selectedDate ) {
        $( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });	
    
  jQuery.validator.addMethod("start_exp_valid", function(value, element) {
	 
	var start_date = $('#start_date').val();
	//alert(start_date);
	//alert(value);
	if(start_date!=''&&value!='')
	{
		var match_start = start_date.match(/^(\d+)-(\d+)-(\d+) (\d+)\:(\d+)\:(\d+)$/);
		var d1 = new Date(match_start[1], match_start[2] - 1, match_start[3], match_start[4], match_start[5], match_start[6]);
		var match_end = value.match(/^(\d+)-(\d+)-(\d+) (\d+)\:(\d+)\:(\d+)$/);
		var d2 = new Date(match_end[1], match_end[2] - 1, match_end[3], match_end[4], match_end[5], match_end[6]);
		//console.log(date.getTime() / 1000);
		/*var start_data=start_date.split(' ');
		var st_date=start_data[0].split('-');
		var st_time=start_data[1].split(':');
		alert(start_data[0]);
		var expire_data=value.split(' ');
		var ex_date=expire_data[0].split('-');
		var ex_time=expire_data[1].split(':');		
		
		var d1 = new Date(st_date.pop(), st_date.pop() - 1, st_date.pop(),st_time.pop(),st_time.pop(),st_time.pop());
		alert(d1);
		var d2 = new Date(ex_date.pop(), ex_date.pop() - 1, ex_date.pop(),ex_time.pop(),ex_time.pop(),ex_time.pop());
		alert(d2); */
		
		if(d1.getTime() > d2.getTime())
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		return true;
	}
		 
 },"<?php echo __('expiredate_and_time_shouldbegreaterthan_startdate'); ?>");
 
   jQuery.validator.addMethod("start_exp_timevalid", function(value, element) {
	 
	var start_date = "<?php echo convert_timezone('now',TIMEZONE); ?>";
	//alert(start_date);
	//alert(value);
	var match_start = start_date.match(/^(\d+)-(\d+)-(\d+) (\d+)\:(\d+)\:(\d+)$/);
		var d1 = new Date(match_start[1], match_start[2] - 1, match_start[3], match_start[4], match_start[5], match_start[6]);
		var match_end = value.match(/^(\d+)-(\d+)-(\d+) (\d+)\:(\d+)\:(\d+)$/);
		var d2 = new Date(match_end[1], match_end[2] - 1, match_end[3], match_end[4], match_end[5], match_end[6]);
		/*var start_data=start_date.split(' ');
		var st_date=start_data[0].split('-');
		var st_time=start_data[1].split(':');
		
		var expire_data=value.split(' ');
		var ex_date=expire_data[0].split('-');
		var ex_time=expire_data[1].split(':');		
		
		var d1 = new Date(st_date.pop(), st_date.pop() - 1, st_date.pop(),st_time.pop(),st_time.pop(),st_time.pop());
		
		var d2 = new Date(ex_date.pop(), ex_date.pop() - 1, ex_date.pop(),ex_time.pop(),ex_time.pop(),ex_time.pop()); */
		
		if(d1.getTime() > d2.getTime())
		{
			return false;
		}
		else
		{
			return true;
		}
		 
 },"<?php echo __('expiretime_shouldbegreaterthan_currentdatetime'); ?>");
 
 
 
 
</script>
