<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<?php $find_url = explode('/',$_SERVER['REQUEST_URI']);
$split = explode('?',$find_url[3]);  	
$list = $split[0];
?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />


<!--<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script>-->
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
$form_action = URL_BASE.'admin/calendarwise_report/';
$back_action = URL_BASE.'admin/calendarwise_report/';

$startdate = isset($srch["all_dates"]) ? $srch["all_dates"] :date('Y-m-d'); 	

//For CSS class deefine in the table if the data's available
//===========================================================
$total_transaction=count($all_transaction_list);

$table_css=$export_excel_button=$export_pdf_button="";
if($total_transaction>0)
{ 
	$table_css='class="table_border"'; 
	$startdate_export = isset($srch["startdate"]) ? $srch["startdate"] :'';
	$enddate_export = isset($srch["enddate"]) ? $srch["enddate"] :'';
	$export_excel_button='
        				<input type="button"  title="'.__('button_export').'" class="button" value="'.__('button_export').'" 
        				onclick="location.href=\''.URL_BASE.'transaction/export/'.$list.'/?filter_company='.$company_val.'&startdate='.$startdate_export.'&enddate='.$enddate_export.'&taxiid='.$taxiid.'&driver_id='.$driver_id.'&manager_id='.$manager_id.'&passengerid='.$passengerid.'&transaction_id='.$transaction_id.'\'" />
    				';
	$export_pdf_button='
        				<input type="button"  title="'.__('button_pdf').'" class="button" value="'.__('button_pdf').'" 
        				style="margin-left:20px;" onclick="location.href=\''.URL_BASE.'transaction/exportpdf/'.$list.'/?filter_company='.$company_val.'&startdate='.$startdate_export.'&enddate='.$enddate_export.'&taxiid='.$taxiid.'&driver_id='.$driver_id.'&manager_id='.$manager_id.'&passengerid='.$passengerid.'&transaction_id='.$transaction_id.'\'" />
    				';    				
}

?>




</script>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle"> 

        <form method="get" class="form" name="calendar_search" id="calendar_search" action="<?php echo $form_action; ?>" >

			<!-- Datepicker calender -->
			<div id="withAltField" class="box" style="margin-top:-40px;margin-left:50px;">
			<div id="with-altField"></div>
			<input type="hidden" id="altField" name="all_dates" value="<?php echo $startdate; ?>" >
			<label class="errors" id="errors_days"></label>
			</div>
			<!-- Datepicker calender -->

		<?php if($list !='rejected' ) { ?>		
                <div class="widget chartWrapper">
            <div class="title"><img src="<?php echo IMGPATH; ?>/icons/dark/stats.png" alt="" class="titleIcon" /><h6><?php echo __('chart'); ?></h6></div>
            <div class="body">
		<?php 
		if(count($grpahdata)>0)
		{
			foreach($grpahdata as $gdata)
			{
				$fare[] = $gdata['amount'];
				//echo date( "d",strtotime($gdata['createdate']))."<br>";
				$month[] = "'".date( "d",strtotime($gdata['createdate']))." ".date( "M",strtotime($gdata['createdate']))."'";
			}
				if($fare){
					$fare = implode(",",$fare);
				}
				if($month){
					$month = implode(",",$month);
				}
		?>
		
		 <div class="chart" id="transaction_chart"></div>
			</div>
<?php } else { echo "<div class='nodata' style='padding:0px 10px 8px 0;'>".__('no_data')."</div></div>"; } ?>
		
        </div>
		<?php } ?>
                		<div class="widget">
		<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo $page_title; ?></h6>
		<div style="width:auto; float:right; margin:0 3px;">
		<!--<div class="button greyishB"> <?php echo $export_excel_button; ?></div>  
		<div class="button greyishB"> <?php echo $export_pdf_button; ?></div>-->                       
		</div>
				
		</div>


<?php if($total_transaction > 0){ ?>
<div class= "overflow-block">
<?php } ?>		
<table cellspacing="1" cellpadding="11" width="100%" align="center" class="sTable responsive">
<?php if($total_transaction > 0){ ?>
<thead>
	<tr>
		<td align="left" width="5%"><?php echo __('sno_label'); ?></td>
		<?php if($list != 'rejected') { ?>
		<td align="left" width="15%"><?php echo __('transactionid_label'); ?></td>
		<td align="left" width="10%"><?php echo __('payment_type'); ?></td>
		<td align="left" width="10%"><?php echo __('track_id'); ?></td>
		<?php } ?>
		<td align="left" width="10%"><?php echo __('passenger_name'); ?></td>
		<td align="left" width="10%"><?php echo ucfirst(__('driver_name')); ?></td>
		<td align="left" width="15%"><?php echo __('companyname'); ?></td>
		<td align="left" width="10%"><?php echo __('journey_date'); ?></td>
		<td align="left" width="10%"><?php echo __('Current_Location'); ?></td>
		<td align="left" width="10%"><?php echo __('Drop_Location'); ?></td>
		<td align="left" width="10%"><?php echo __('No_Passengers'); ?></td>
		<?php if($list != 'rejected') { ?>
		<td align="left" width="10%"><?php echo __('admin_commision'); ?></td>
		<td align="left" width="10%"><?php echo __('company_commision'); ?></td>
		<?php /* <td align="left" width="10%"><?php echo __('package_type'); ?></td> */ ?>
		<?php } ?>
		<?php if($list != 'rejected') { ?>
		<?php if($list != 'cancelled') { ?>
		<td align="left" width="10%"><?php echo __('distance_km'); ?></td>
		<td align="left" width="10%"><?php echo __('nightfare'); ?></td>
		<?php } ?>

		<td align="left" width="10%"><?php if($list == 'cancelled') { echo __('cancel_fare'); } else { echo __('trip_total_fare'); }?></td>
		<td align="left" width="10%"><?php echo __('equivalent_to_usd').CURRENCY_FORMAT; ?></td>

		<?php if($list != 'success') { ?>
		<td align="left" width="10%"><?php echo __('travel_status'); ?></td>
		<?php } ?>
		<!--<td align="left" width="10%"><?php echo __('rating_points');?></td>
		<td align="left" width="10%"><?php echo __('comments');?></td>-->
		<?php } 
		else {	?>
		<td align="left" width="10%"><?php echo __('travel_status');?></td>
		<td align="left" width="10%"><?php echo __('reason');?></td>
		<?php } ?>

	</tr>
</thead>
<tbody>	
		<?php
		/* For Serial No */
		$sno=$Offset; 
		$totalfare="";
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
			<?php if($list != 'rejected') { ?>
			<td>
				<?php if($listings['transaction_id'] != "")
				{?>
				<a href="<?php echo URL_BASE.'transaction/transaction_details/'.$listings['passengers_log_id'];?>"><?php echo $listings['transaction_id']; ?></a>
				<?php } else { ?>
				- <?php } ?></td>
			<td><?php if($listings['payment_type'] == 2) { echo __('credit_card_paypal'); } else if($listings['payment_type'] == 1) { echo __('account'); } else if($listings['payment_type'] == 4){ echo __('new_credit_card'); } else { echo __('cash'); } ?></td>
			<td><a href="<?php echo URL_BASE.'transaction/transaction_details/'.$listings['passengers_log_id'];?>"><?php echo $listings['passengers_log_id']; ?></a></td>
			<?php } ?>

			
			<td><?php echo ucfirst($listings['passenger_name']); ?></td>
			<td><a href="<?php echo URL_BASE.'manage/driverinfo/'.$listings['driver_id'];?>"><?php echo wordwrap(ucfirst($listings['driver_name']),30,'<br/>',1); ?></a></td>
			<td>
				<a href="<?php echo URL_BASE.'manage/companydetails/'.$listings['userid'];?>">
			<?php echo ucfirst($listings['company_name']); ?></a></td>
			<td><?php echo $listings['createdate'];?></td>
			<td><?php echo $listings['current_location'];?></td>
			<td><?php echo $listings['drop_location'];?></td>
			<td><?php echo $listings['no_passengers'];?></td>
			<?php if($list != 'rejected') { ?>		
			<td><?php echo $listings['admin_amount'];?></td>
			<td><?php echo $listings['company_amount'];?></td>
			<?php } ?>
			<?php if($list != 'rejected') { ?>
			<?php if($list != 'cancelled') { ?>
			<td><?php if($listings['distance'] == 0) { echo '-'; } else { echo round($listings['actual_distance'],2);}?></td>
			<td><?php if($listings['nightfare'] == 0) { echo '-'; } else { echo round($listings['nightfare'],2);}?></td>
			<?php } ?>
			
			<td><?php 
			$company_currency = $listings['company_id'];
			$ccur = findcompany_currency($company_currency);
			if($listings['fare'] == 0) { echo '-'; } else { echo $ccur.round($listings['fare'],2);}?></td>
			<td><?php if($listings['fare'] == 0) { echo '-'; $convet_amt = 0; } else {
				$ccur_for = findcompany_currencyformat($company_currency);
			$convet_amt = currency_conversion($ccur_for,$listings['fare']);
			echo round($convet_amt,2);}?></td>

			<?php if($list != 'success') { ?>
			<td><?php if($listings['travel_status'] == 0) { echo __('not_completed'); } else if($listings['travel_status'] == 1) { echo __('completed'); } else if($listings['travel_status'] == 2) { echo __('inprogress'); } else if($listings['travel_status'] == 3) { echo __('start_to_pickup'); } else if($listings['travel_status'] == 4) { echo __('cancel_by_passenger'); } ?> </td>
			<?php } ?>

			<!--<td><?php if($listings['rating'] == 0) { echo '-'; } else { echo $listings['rating']; }?></td>
			<td><?php echo $listings['comments']; ?></td>-->
			<?php }
			else { ?>
			<td><?php if($listings['driver_reply'] == 'C') { echo __('cancelled_by_driver'); } else { echo __('rejected_by_driver'); }?></td>
			<td><?php if($listings['driver_comments'] == '') { echo '-'; } else { echo $listings['driver_comments']; }?></td>	
			<?php } ?>
		</tr>
		<?php
			if($list != 'rejected') { 
				$totalfare +=$convet_amt;
			}

			} ?>
		<?php if($list != 'rejected') { ?>
		<tr>
			<?php if($list != 'cancelled') { ?>	
			<td></td>
			<?php } ?>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
			<td></td><td></td><td></td>
			<?php if($list != 'success') { ?>
			<td></td>
			<?php } ?>

			<?php if($list == 'success') { ?>
			<td></td>
			<?php } ?>

			<?php if($list != 'all' || $list != 'success') { ?><td></td><?php } ?><td></td><td><?php echo __('total').'('.CURRENCY.')';?></td><td><?php echo $totalfare; ?></td>

		

		</tr>
		<?php } 
	
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
toggle(25);

/** calendar **/
$('#with-altField').datepicker({
dateFormat: "yy-mm-dd", 
altField: '#altField',
maxDate: new Date(),
onSelect: function (date) {  document.getElementById("calendar_search").submit(); }
});
/** calendar **/


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
		$('#startdate_error').html("<?php echo __('select_datetime');?>");
		$('#startdate').focus();
		return false;		
	}	
	else if(enddate =="")
	{
		$('#startdate_error').html('');
		$('#enddate_error').html("<?php echo __('select_datetime');?>");
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


$(function () {
        $('#transaction_chart').highcharts({
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
				text: 'Amount (Rs)'
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
			name: 'Transaction',
			data: [<?php echo $fare;?>]
		}]
	});
	
    });



</script>
		<?php }
} ?>
<script src="<?php echo SCRIPTPATH; ?>highcharts.js"></script>



