<?php defined('SYSPATH') OR die("No direct access allowed."); ?>

<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/transaction.js"></script>
<?php
//For search values
//=================
$user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :''; 
$company_val = isset($srch["filter_company"]) ? $srch["filter_company"] :''; 
$startdate = isset($srch["startdate"]) ? $srch["startdate"] :date('Y-m-d 00:00:00'); 	
$enddate = isset($srch["enddate"]) ? $srch["enddate"] :date('Y-m-d H:i:s'); 	
$taxiid = isset($srch["taxiid"]) ? $srch["taxiid"] :'';
$passengerid = isset($srch["passengerid"]) ? $srch["passengerid"] :'';
$driver_id = isset($srch["driver_id"]) ? $srch["driver_id"] :'';
$manager_id = isset($srch["manager_id"]) ? $srch["manager_id"] :'';
$status_val = isset($srch["status"]) ? $srch["status"] :''; 							
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :''; 
$s_date = isset($srch["startdate"]) ? 2:1; 	
$e_date = isset($srch["enddate"]) ? 2:1; 
$payment_type = isset($srch["payment_type"]) ? $srch["payment_type"] :''; 
$transaction_id = isset($srch["transaction_id"]) ? $srch["transaction_id"] :''; 
$form_action = '';
$form_action = URL_BASE.'admin/active_driver_search/';
$back_action = URL_BASE.'admin/active_driver_search/';



//For CSS class deefine in the table if the data's available
//===========================================================
$total_list=count($active_driverlist);

$table_css=$export_pdf_button=$export_pdf_send_button="";
if($total_list>0)
{ 
	$table_css='class="table_border"'; 
	$startdate_export = isset($srch["startdate"]) ? $srch["startdate"] :'';
	$enddate_export = isset($srch["enddate"]) ? $srch["enddate"] :'';
	
	//http://192.168.1.88:1010/admin/active_driver_search/?filter_company=All&taxiid=All&driver_id=All&startdate=2014-01-01+00%3A00%3A00&enddate=2014-04-05+11%3A47%3A12&search_user=Search
	$export_pdf_button='
        				<input type="button"  title="'.__('Gen_invoice').'" class="button" value="'.__('Gen_invoice').'" 
        				style="margin-left:20px;" onclick="location.href=\''.URL_BASE.'admin/exportpdf/?filter_company='.$company_val.'&startdate='.$startdate_export.'&enddate='.$enddate_export.'&taxiid='.$taxiid.'&driver_id='.$driver_id.'&type_export=0'.'\'" />
    				';    
	$export_pdf_send_button='
        				<input type="button"  title="'.__('Gen_invoice_send').'" class="button" value="'.__('Gen_invoice_send').'" 
        				onclick="location.href=\''.URL_BASE.'admin/exportpdf/?filter_company='.$company_val.'&startdate='.$startdate_export.'&enddate='.$enddate_export.'&taxiid='.$taxiid.'&driver_id='.$driver_id.'&type_export=1'.'\'" />
    				';    		
}

?>


</script>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle"> 
        <form method="get" class="form" name="managedriver" id="managedriver" action="<?php echo $form_action; ?>" onsubmit="return validatetranaction_form();">
		<table class="list_table1" border="0" width="100%" cellspacing="0" cellpadding="5" >
				<tr>				
					<td valign="middlle"><label><?php echo __('company'); ?></label></td>
					<td valign="top">
						<div class="selector" id="uniform-user_type">
							<select class="select2" name="filter_company" id="filter_company" onchange="getcompanymanager(this.value),getcompanytaxi(this.value),getcompanydriver(this.value),getcompanypassengers(this.value)">
								
									<option value="All"><?php echo __('all_label'); ?></option>    
								<?php 
									foreach ($get_allcompany as $comapany_list) {
									$selected_status = ($company_val == $comapany_list['cid']) ? ' selected="selected" ' : " ";
								?>  
									<option value="<?php echo $comapany_list['cid']; ?>"  <?php echo $selected_status; ?> ><?php echo ucfirst($comapany_list['company_name']); ?></option>
									<?php }?>
							</select>
						</div>
						<div id="filter_company_error" class="error"></div>
					</td>  
				<?php /*	
				<td valign="middlle"><label><?php echo __('manager_name'); ?></label></td>
				<td id="manager_list">
					<div class="selector" id="uniform-user_type">
						<select name="manager_id" id="manager_id" onchange="getmanagertaxi(this.value),getmanagerdriver(this.value),getcompanypassengers(filter_company.value)">
							<?php if(count($managerlist) > 0) { ?>
							<option value="All"><?php echo __('all_label');?></option>
							<?php
							foreach($managerlist as $values) { 
								$managername = $values["name"].' '.$values["lastname"];
								$selected_status = ($manager_id == $values['id']) ? ' selected="selected" ' : " ";
							echo '<option value="'.$values["id"].'"'.$selected_status.'>'.ucfirst($managername).'</option>';
						 } } else { 
									  echo '<option value="">'.__('select_label').'</option>';
								  }?>
						</select>
					</div>
				</td>
				*/ ?>					   
				<td valign="middlle"><label><?php echo __('taxi'); ?></label></td>
				<td id="taxi_list">
					<div class="selector" id="uniform-user_type">
						<select name="taxiid" id="taxiid" class="select2">
						<?php if(count($taxilist) > 0) { ?>
						<option value="All"><?php echo __('all_label');?></option>
						<?php
						foreach($taxilist as $values) { 
							$selected_status = ($taxiid == $values['taxi_id']) ? ' selected="selected" ' : " ";
						echo '<option value="'.$values["taxi_id"].'"'.$selected_status.'>'.$values["taxi_no"].'</option>';
						 } } else { 
									  echo '<option value="">'.__('select_label').'</option>';
								  }?>
						</select>
					</div>
				</td>
				
				<td valign="middlle"><label><?php echo __('driver_name'); ?></label></td>
				<td id="driver_list">
					<div class="selector" id="uniform-user_type">
						<select name="driver_id" id="driver_id">
							<?php if(count($driverlist) > 0) { ?>
							<option value="All"><?php echo __('all_label');?></option>
							<?php
							foreach($driverlist as $values) { 
								$drivername = $values["name"].' '.$values["lastname"];
								$selected_status = ($driver_id == $values['id']) ? ' selected="selected" ' : " ";
							echo '<option value="'.$values["id"].'"'.$selected_status.'>'.ucfirst($drivername).'</option>';
						 } } else { 
									  echo '<option value="">'.__('select_label').'</option>';
								  }?>
						</select>
					</div>
				</td>	
							
				</tr>
				<tr>

				 <?php 
				 /*
					<td valign="middlle"><label><?php echo __('passenger_name'); ?></label></td>
					<td id="passenger_list">
						<div class="selector" id="uniform-user_type">
							<select name="passengerid" id="passengerid" class="select2">
							<?php if(count($passengerlist) > 0) { ?>
							<option value="All"><?php echo __('all_label');?></option>
							<?php
							foreach($passengerlist as $values) { 
								if(is_null($values["company_name"]))
								{
									$cname='';
								}
								else
								{
									$cname='-'.ucfirst($values["company_name"]);
								}
								$passengername = ucfirst($values["name"]).$cname;
								$selected_status = ($passengerid == $values['id']) ? ' selected="selected" ' : " ";
							echo '<option value="'.$values["id"].'"'.$selected_status.'>'.$passengername.'</option>';
							 } } else { 
										  echo '<option value="">'.__('select_label').'</option>';
									  }?>
							</select>
						</div>
					</td>
                      */ ?>
					</tr>  
				<tr>  
					<td valign="middle"><label><?php echo __('from_date'); ?></label></td>
                        <td valign="top">
						<div class="new_input_field_transaction">
								  <input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="startdate" name="startdate" value="<?php echo $startdate;?>"  />
						 <span id="startdate_error" class="error"></span>		 
						 </div>
						
                        </td>       

                        <td valign="middle"><label><?php echo __('end_date'); ?></label></td>
                        <td valign="top">
						<div class="new_input_field_transaction">
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
                                <input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo $back_action; ?>'" />
                            </div>
                        </td>
                 </tr>
                </table>
	


                		<div class="widget">
		<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo $page_title; ?></h6>
		<div style="width:auto; float:right; margin:0 3px;">
		<div class="button greyishB"> <?php echo $export_pdf_button; ?></div>  
		<div class="button greyishB"> <?php echo $export_pdf_send_button; ?></div>                       
		</div>
				
		</div>


<?php if($total_list > 0){ ?>
<div class= "overflow-block">
<?php } ?>		
<table cellspacing="1" cellpadding="11" width="100%" align="center" class="sTable responsive">
<?php if($total_list > 0){ ?>
<thead>
	<tr>
		<td align="left" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" width="20%"><?php echo __('taxi_no'); ?></td>
		<td align="left" width="20%"><?php echo ucfirst(__('driver_name')); ?></td>
		<td align="left" width="20%"><?php echo __('companyname'); ?></td>
		<td align="left" width="20%"><?php echo __('trip_count'); ?></td>
	</tr>
</thead>
<tbody>	
		<?php
		/* For Serial No */
		$sno=$Offset; 
		$totalfare="";
		 foreach($active_driverlist as $listings) { 

		 //S.No Increment
		 //==============
		 $sno++;
        
         //For Odd / Even Rows
         //===================
         $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
		 
        ?>     

		<tr class="<?php echo $trcolor; ?>">
			<td><?php echo $sno; ?></td>
			<td align='middle'><?php echo $listings['taxi_no']; ?></td>
			<td align='middle'><a href="<?php echo URL_BASE.'manage/driverinfo/'.$listings['driver_id'];?>"><?php echo wordwrap(ucfirst($listings['driver_name']),30,'<br/>',1); ?></a></td>
			<td align='middle'>
				<a href="<?php echo URL_BASE.'manage/companydetails/'.$listings['userid'];?>">		
				<?php echo ucfirst($listings['company_name']); ?></a></td>

			<td align='middle'><?php echo $listings['trip_count']; ?></td>
		</tr>
		<?php

			} ?>
        <tr><td colspan="4" align='right'>
			<?php echo __('admin_taxi_charge').'('.CURRENCY.') '.'<br/>'; ?>
			<?php echo __('admin_total_number_of_taxi').' '.'<br/>'; ?>
			<?php echo __('total').'('.CURRENCY.')'.''; ?>
			</td>
			<td align="center"><?php echo TAXI_CHARGE.' * <br/>'.$total_list.'<br/>'.TAXI_CHARGE*$total_list; ?>
		    </td></tr>
		<?php 
	
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
<?php if ($total_list > 0) { ?>
</div>
<?php } ?>
</form>
</div>
</div>

<div class="pagination">
		<?php if($total_list > 0): ?>
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
toggle(29);

$("#startdate").datetimepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
maxDateTime : new Date("<?php echo date('Y m d,H:i:s'); ?>"),
stepSecond: 1
} );

$("#enddate").datetimepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
maxDateTime : new Date("<?php echo date('Y m d,H:i:s'); ?>"),
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
<?php 
//echo $fare;
$milliseconds = strtotime($startdate) * 1000;
//echo $startdate;
if(isset($_GET['startdate']) && isset($_GET['startdate'])){

	if($_GET['startdate'] !='' && $_GET['startdate'] !='')
	{
		$text = __('transactions').' '.__('from').' '.$startdate.' '.__('to').' '.$enddate;
	}
	else
	{
		$text = __('transactions');	
	}

}else{
	$text = __('transactions');
}
if(count($grpahdata)>0)
{

	if($list != 'rejected') { ?>
<script type="text/javascript">
	var startdate = $('#startdate').val();
	if(startdate != '')
	{
	var temp = new Array();
	temp = startdate.split("-");
	var year = temp[0];
	var month = temp[1];
	var dates = temp[2].substring(0,2);
	var month = month-1;
	}

$(function () {
        $('#container').highcharts({
		title: {
			text: '<?php echo $text;?>',
			x: -20 //center
		},
		subtitle: {
			text: '',
			x: -20 //center
		},
		xAxis: {
			categories: [<?php echo $month;?>]
		},
		yAxis: {
			title: {
				text: '<?php echo __("amount_label")."(".CURRENCY.")"; ?>'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			valueSuffix: ''
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'middle',
			borderWidth: 0
		},
		series: [{
			name: '<?php echo __("transaction"); ?>',
			data: [<?php echo $fare;?>]
		}]
	});
	
    });
</script>
		<?php }
} ?>
<script src="<?php echo SCRIPTPATH; ?>highcharts.js"></script>
