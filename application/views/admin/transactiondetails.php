<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<?php
//For search values
//=================
$user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :''; 
$company_val = isset($srch["filter_company"]) ? $srch["filter_company"] :''; 
$startdate = isset($srch["startdate"]) ? $srch["startdate"] :date('Y-m-d 00:00:00'); 	
$enddate = isset($srch["enddate"]) ? $srch["enddate"] :date('Y-m-d H:i:s'); 	
$status_val = isset($srch["status"]) ? $srch["status"] :''; 							
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :''; 

//For CSS class deefine in the table if the data's available
//===========================================================
$total_transaction=count($all_transaction_list);

$table_css=$export_excel_button="";
if($total_transaction>0)
{ 
	$table_css='class="table_border"'; 

	$export_excel_button='
        				<input type="button"  title="'.__('button_export').'" class="button" value="'.__('button_export').'" 
        				onclick="location.href=\''.URL_BASE.'manage/export?keyword='.$keyword.'&status='.$status_val.'&type='.$user_type_val.'\'" />
    				';
}?>

<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle"> 
        <form method="get" class="form" name="managedriver" id="managedriver" action="transaction_list" onsubmit="return validatetranaction_form();">
		<table class="list_table1" border="0" width="100%" cellpadding="5" cellspacing="0">
				<tr>
				<?php if($_SESSION['user_type'] == 'A') { ?>
					<td valign="top"><label><?php echo __('company'); ?></label></td>
					<td valign="top">
						<div class="selector" id="uniform-user_type">
							<select class="select2" name="filter_company" id="filter_company" --onchange="this.form.submit()">
								<option value=""><?php echo __('select_label'); ?></option>    
								<?php 
									foreach ($get_allcompany as $comapany_list) {
									$selected_status = ($company_val == $comapany_list['cid']) ? ' selected="selected" ' : " ";
								?>  
									<option value="<?php echo $comapany_list['cid']; ?>"  <?php echo $selected_status; ?> ><?php echo $comapany_list['company_name']; ?></option>
									<?php }  ?>
							</select>
						</div>
						<span id="filter_company_error" class="error"></span>

					</td>     
				<?php } ?>
				
				  

                        <td valign="middle"><label><?php echo __('from_date'); ?></label></td>
                        <td valign="top">
						<div class="new_input_field">
								  <input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="startdate" name="startdate" value="<?php echo $startdate;?>"  />
						 <span id="startdate_error" class="error"></span>		 
						 </div>
						
                        </td>       

                        <td valign="middle"><label><?php echo __('end_date'); ?></label></td>
                        <td valign="top">
						<div class="new_input_field">
								  <input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="enddate" name="enddate" value="<?php echo $enddate;?>"  />
								  						 <span id="enddate_error" class="error"></span>								

						</div>
                        </td>       
                 </tr>
                 <tr>
                        <td valign="top"><label>&nbsp;</label></td>
                        <td>
                            <div class="button brownB">
                                <input type="submit" value="<?php echo __('button_search'); ?>" name="search_user" title="<?php echo __('button_search'); ?>" />
                            </div>
                            <div class="button blueB">
                                <input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo URL_BASE; ?>manage/transaction'" />
                            </div>
                        </td>
                 </tr>
                </table>
                		<div class="widget">
		<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo $page_title; ?></h6>
		<div style="width:auto; float:right; margin: 4px 3px;">
		<div class="button greyishB"> <?php //echo $export_excel_button; ?></div>                       

		</div>
		</div>
<?php if($total_transaction > 0){ ?>
<div class= "overflow-block">
<?php } ?>		
<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
<?php if($total_transaction > 0){ ?>
<thead>
	<tr>
		<td align="left" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" width="10%"><?php echo __('passenger_name'); ?></td>
		<td align="left" width="10%"><?php echo ucfirst(__('driver_name')); ?></td>
		<td align="left" width="10%"><?php echo __('companyname'); ?></td>
		<td align="left" width="10%"><?php echo __('journey_date'); ?></td>
		<td align="left" width="10%"><?php echo __('passenger_email'); ?></td>
		<td align="left" width="10%"><?php echo __('Current_Location'); ?></td>
		<td align="left" width="10%"><?php echo __('Drop_Location'); ?></td>
		<td align="left" width="10%"><?php echo __('No_Passengers'); ?></td>
		<td align="left" width="10%"><?php echo __('waiting_time');?></td>
		<td align="left" width="10%"><?php echo __('distance_km'); ?></td>
		<td align="left" width="10%"><?php echo __('trip_total_fare'); ?></td>
		<td align="left" width="10%"><?php echo __('rating_points');?></td>
		<td align="left" width="10%"><?php echo __('comments');?></td>

	</tr>
</thead>
<tbody>	
		<?php
		/* For Serial No */
		$sno=$Offset; 
		
		 foreach($all_transaction_list as $listings) {

		 //S.No Increment
		 //==============
		 $sno++;
        
         //For Odd / Even Rows
         //===================
         $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
		 
        ?>     

		<tr class="<?php echo $trcolor; ?>">
			<td><?php echo $sno; ?></td>
			<td><?php echo ucfirst($listings['passenger_name']); ?></td>
			<td><a href="<?php echo URL_BASE.'manage/driverinfo/'.$listings['driver_id'];?>"><?php echo wordwrap(ucfirst($listings['driver_name']),30,'<br/>',1); ?></a></td>
			<td><a href="<?php echo URL_BASE.'manage/companydetails/'.$listings['userid'];?>"><?php echo wordwrap(ucfirst($listings['company_name']),25,'<br />',1); ?></a></td>
			<td><?php echo $listings['createdate'];?></td>
			<td><?php echo wordwrap($listings['email'],25,'<br />',1); ?></td>
			<td><?php echo $listings['current_location'];?></td>
			<td><?php echo $listings['drop_location'];?></td>
			<td><?php echo $listings['no_passengers'];?></td>
			<td><?php echo $listings['waitingtime'].' Mins';$comments = htmlentities($listings['comments']);?></td>
			<td><?php if($listings['distance'] == 0) { echo '-'; } else { echo round($listings['distance'],2);}?></td>
			<td><?php if($listings['fare'] == 0) { echo '-'; } else { echo round($listings['fare'],2);}?></td>
			<td><?php if($listings['rating'] == 0) { echo '-'; } else { echo $listings['rating']; }?></td>
			<td><?php echo $listings['comments']; ?></td>
		</tr>
		<?php
		if($sno >=$total_transaction){
		?>
		<tr>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
			<td><?php echo __('trip_total_fare');?></td>
			<td><?php echo $listings['totalfare']; ?></td><td></td><td></td><td></td>
		</tr>
		<?php }
			}
		
 		 } 
		 
		//For No Records
		//==============
	     else{ ?>
       	<tr>
        	<td class="nodata"><?php echo __('no_data'); ?></td>
        </tr>
		<?php } ?>
		</tbody>
</table>
<?php if ($total_transaction > 0) { ?>
</div>
<?php } ?>
</form>
</div>
</div>
<div class="clr">&nbsp;</div>
<div class="pagination">
		<?php if($total_transaction > 0): ?>
		 <p><?php echo $pag_data->render(); ?></p>  
		<?php endif; ?> 
  </div>
  <div class="clr">&nbsp;</div>

</div>
</div>

<script type="text/javascript" language="javascript">

//For Delete the users
//=====================
function frmdel_user(userid)
{
   var answer = confirm("<?php echo __('delete_alert2');?>");
    
	if (answer){
        window.location="<?php echo URL_BASE;?>admin/delete/"+userid;
    }
    
    return false;  
}  
function frmblk_user(userid,status)
{   
    window.location="<?php echo URL_BASE;?>admin/blkunblk/"+userid+"/"+status;    
    return false;  
}  


$(document).ready(function(){

$("#startdate").datetimepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
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
} );
function validatetranaction_form()
{
	valid = true;
	var filter_company = $('#filter_company').val();
	var startdate = $('#startdate').val();
	var enddate = $('#enddate').val();
	if(filter_company =="")
	{
		$('#filter_company_error').html("<?php echo __('select_company');?>");
		$('#filter_company').focus();
		return false;		
	}	
	else if(startdate =="")
	{
		$('#filter_company_error').html('');
		$('#startdate_error').html("<?php echo __('select_company');?>");
		$('#startdate').focus();
		return false;		
	}	
	else if(enddate =="")
	{
		$('#startdate_error').html('');
		$('#enddate_error').html("<?php echo __('select_company');?>");
		$('#enddate').focus();
		return false;		
	}
		return true;	
}
</script>
