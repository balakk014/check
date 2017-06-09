<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">
         <form name="addmotor_form" class="form" id="addmotor_form" action="" method="post" enctype="multipart/form-data">
           <table border="0" cellpadding="5" cellspacing="0" width="100%">
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('reason'); ?></label><span class="star">*</span></td>        
	   <td>
		   <div class="new_input_field">
		  <?php 
			if(isset($unavailable_details[0]['u_reason']) && !array_key_exists('reason',$postvalue)){
				$reason = $unavailable_details[0]['u_reason'];
			}else{
				if(isset($postvalue['reason'])){
					$reason = $postvalue['reason'];
				}else{
					$reason = "";
				}
			}
			?>       
		<textarea name="reason" id="reason"  title="<?php echo __('enterthereason'); ?>" rows="7" cols="35"><?php echo $reason; ?></textarea>

              <?php if(isset($errors) && array_key_exists('reason',$errors)){ echo "<span class='error'>".ucfirst($errors['reason'])."</span>";}?>
		   </div>
           </td>   	
           </tr>  
           <?php 
			if(isset($unavailable_details[0]['u_startdate']) && !array_key_exists('startdate',$postvalue)){
				$startdate = $unavailable_details[0]['u_startdate'];
			}else{
				if(isset($postvalue['startdate'])){
					$startdate = $postvalue['startdate'];
				}else{
					$startdate = "";
				}
			}
			?>  
	<tr>
	<td valign="top" width="20%"><label><?php echo __('from_date'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="startdate" name="startdate" value="<?php echo $startdate; ?>"  />
              <?php if(isset($errors) && array_key_exists('startdate',$errors)){ echo "<span class='error'>".ucfirst($errors['startdate'])."</span>";}?>
              <input type="hidden" name="driver_id" id="driver_id" value="<?php if(isset($unavailable_details[0]['u_driverid'])) { echo trim($unavailable_details[0]['u_driverid']); } ?>">
              <input type="hidden" name="taxi_id" id="taxi_id" value="<?php if(isset($unavailable_details[0]['u_taxiid'])) { echo trim($unavailable_details[0]['u_taxiid']); } ?>">
	</div>
	</td>   	
	</tr>
	   <?php 
		if(isset($unavailable_details[0]['u_enddate']) && !array_key_exists('enddate',$postvalue)){
			$enddate = $unavailable_details[0]['u_enddate'];
		}else{
			if(isset($postvalue['enddate'])){
				$enddate = $postvalue['enddate'];
			}else{
				$enddate = "";
			}
		}
		?>  
	<tr>
	<td valign="top" width="20%"><label><?php echo __('end_date'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="enddate" name="enddate" value="<?php echo $enddate; ?>"  />
              <?php if(isset($errors) && array_key_exists('enddate',$errors)){ echo "<span class='error'>".ucfirst($errors['enddate'])."</span>";}?>
              
        </div>
	</td>   	
	</tr>

          
	<tr>
	<td>&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
                            <br />
                            <div class="button blackB">     <input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                            <div class="button dredB">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            <div class="button greenB">  <input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addleave" title="<?php echo __('btn_submit' );?>" /></div>
                            <div class="clr">&nbsp;</div>
                        </td>
                    </tr> 

                </table>
                

        </form>
         <div id="show_unavailabledriver"></div>
         
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>    
</div>
<script type="text/javascript">
$(document).ready(function(){

change_info();

});

$("#startdate").datetimepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
minDateTime : new Date(),
stepSecond: 1
} );

$("#enddate").datetimepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
minDateTime : new Date(), 
stepSecond: 1
} );


 $("#startdate").change(function() {
 	change_info();
 });
 $("#enddate").change(function() {
 	change_info(); 
 });
 
function change_info()
{

      		var driver_id = $("#driver_id").val();
      		var taxi_id = $("#taxi_id").val();

     		var startdate = $("#startdate").val();
     		var enddate = $("#enddate").val();
		var page_no = '1';
		  $.ajax({
			url:"<?php echo URL_BASE;?>edit/getunavilabledriverlist",
			type:"get",
			data:"driver_id="+driver_id+"&taxi_id="+taxi_id+"&startdate="+startdate+"&enddate="+enddate+"&page="+page_no,
			success:function(data){
			$('#show_unavailabledriver').html();
			$('#show_unavailabledriver').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}
 
function page_unavailinfo(page_no)
{

      		var driver_id = $("#driver_id").val();
		var taxi_id = $("#taxi_id").val();
		
     		var startdate = $("#startdate").val();
     		var enddate = $("#enddate").val();
		  $.ajax({
			url:"<?php echo URL_BASE;?>edit/getunavilabledriverlist",
			type:"get",
			data:"driver_id="+driver_id+"&taxi_id="+taxi_id+"&startdate="+startdate+"&enddate="+enddate+"&page="+page_no,
			success:function(data){
			$('#show_unavailabledriver').html();
			$('#show_unavailabledriver').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
} 
 
</script>
