<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<?php $find_url = explode('/',$_SERVER['REQUEST_URI']);
$split = explode('?',$find_url[3]);
$currentPage = $split[0];  	
$list = isset($srch["travelSts"]) ? $srch["travelSts"] : $split[0];
$list = intval($list) ? "all" : $list;

?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/transaction.js"></script>
<?php
//For search values
//=================
$user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :'';
$company_val = isset($srch["filter_company"]) ? $srch["filter_company"] :'';
//$startdate = isset($srch["startdate"]) ? $srch["startdate"] : Commonfunction::getDateTimeFormat(date('Y-m-01 00:00:00'),1);
$startdate = isset($srch["startdate"])?$srch["startdate"]:date('Y-m-01 00:00:00');
$enddate = isset($srch["enddate"])?$srch["enddate"]:convert_timezone('now',$_SESSION['timezone']);
$taxiid = isset($srch["taxiid"])?$srch["taxiid"]:'';
$passengerid = isset($srch["passengerid"]) ? $srch["passengerid"] :'';
$driver_id = isset($srch["driver_id"]) ? $srch["driver_id"] :'';
$manager_id = isset($srch["manager_id"]) ? $srch["manager_id"] :'';
$status_val = isset($srch["status"]) ? $srch["status"] :'';
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :'';
$s_date = isset($srch["startdate"]) ? 2:1;
$e_date = isset($srch["enddate"]) ? 2:1;
$payment_type = isset($srch["payment_type"]) ? $srch["payment_type"] :'';
$payment_mode = isset($srch["payment_mode"]) ? $srch["payment_mode"] :'';
$transaction_id = isset($srch["transaction_id"]) ? $srch["transaction_id"] :'';
$trip_id = isset($srch["trip_id"]) ? $srch["trip_id"] :'';
$form_action = '';
$form_action = URL_BASE.'transaction/admintransaction_list/'.$currentPage.'/';
$back_action = URL_BASE.'transaction/admintransaction/'.$currentPage.'/';

$chart_currency=findcompany_currency($company_val);
$chart_currency=$chart_currency[0]["currency_symbol"];

//For CSS class deefine in the table if the data's available
//===========================================================
$total_transaction=count($all_transaction_list);
$table_css=$export_excel_button=$export_pdf_button="";
if($total_transaction>0)
{
	$table_css='class="table_border"';
	$startdate_export = isset($srch["startdate"]) ? $srch["startdate"] : Commonfunction::getDateTimeFormat(date('Y-m-01 00:00:00'),1);
	$enddate_export = isset($srch["enddate"]) ? $srch["enddate"] : Commonfunction::getDateTimeFormat(date('Y-m-d H:i:s'),2);
	$export_excel_button='<input type="button"  title="'.__('button_export').'" class="button" value="'.__('button_export').'" onclick="location.href=\''.URL_BASE.'transaction/export/'.$list.'/?filter_company='.$company_val.'&startdate='.$startdate_export.'&enddate='.$enddate_export.'&taxiid='.$taxiid.'&driver_id='.$driver_id.'&manager_id='.$manager_id.'&passengerid='.$passengerid.'&transaction_id='.$transaction_id.'&payment_type='.$payment_type.'&payment_mode='.$payment_mode.'\'" />';

	$export_pdf_button='<input type="button"  title="'.__('button_pdf').'" class="button" value="'.__('button_pdf').'" style="margin-left:20px;" onclick="location.href=\''.URL_BASE.'transaction/exportpdf/'.$list.'/?filter_company='.$company_val.'&startdate='.$startdate_export.'&enddate='.$enddate_export.'&taxiid='.$taxiid.'&driver_id='.$driver_id.'&manager_id='.$manager_id.'&passengerid='.$passengerid.'&transaction_id='.$transaction_id.'&payment_type='.$payment_type.'&payment_mode='.$payment_mode.'\'" />';
 } ?>
</script>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle"> 
        <form method="get" class="form" name="managedriver" id="managedriver" action="<?php echo $form_action; ?>" onsubmit="return validatetranaction_form();">
		<table class="list_table1" border="0" width="100%" cellspacing="0" cellpadding="5" >
				<tr>				
					<td valign="middlle"><label><?php echo __('company'); ?></label></td>
					<td valign="top">
						<div class="selector ser_input_field" id="uniform-user_type">
							<select class="select2" name="filter_company" id="filter_company" onchange="getcompanymanager(this.value),getcompanytaxi(this.value),getcompanydriver(this.value),getcompanypassengers(this.value)">
								
									<option value="All"><?php echo __('all_label'); ?></option>    
								<?php 
									foreach ($get_allcompany as $comapany_list) {
									$selected_status = ($company_val == $comapany_list['cid']) ? ' selected="selected" ' : " ";
									$companyName = (isset($comapany_list['company_brand_type']) && $comapany_list['company_brand_type'] == 'S') ? ucfirst($comapany_list["company_name"]).' - Admin' : ucfirst($comapany_list["company_name"]);
								?>  
									<option value="<?php echo $comapany_list['cid']; ?>"  <?php echo $selected_status; ?> ><?php echo $companyName; ?></option>
									<?php }?>
							</select>
						</div>
						<div id="filter_company_error" class="error"></div>
					</td>  
					
				<td valign="middlle"><label><?php echo __('manager_name'); ?></label></td>
				<td id="manager_list">
					<div class="selector ser_input_field" id="uniform-user_type">
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
				<?php if($transaction_type != 'rejected'){ ?>
				<td valign="middlle"><label><?php echo __('taxi'); ?></label></td>
				<td id="taxi_list">
					<div class="selector ser_input_field" id="uniform-user_type">
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
				<?php } ?>
				</tr>
				<tr>
				<td valign="middlle"><label><?php echo __('driver_name'); ?></label></td>
				<td id="driver_list">
					<div class="selector ser_input_field" id="uniform-user_type">
						<select name="driver_id" id="driver_id">
							<?php if(count($driverlist) > 0) { ?>
							<option value="All"><?php echo __('all_label');?></option>
							<?php
							foreach($driverlist as $values) { 
								$name = (isset($values["name"]))?$values["name"]:"";
								$lastname = (isset($values["lastname"]))?$values["lastname"]:"";
								$drivername = $name.' '.$lastname;
								$selected_status = ($driver_id == $values['id']) ? ' selected="selected" ' : " ";
							echo '<option value="'.$values["id"].'"'.$selected_status.'>'.ucfirst($drivername).'</option>';
						 } } else { 
									  echo '<option value="">'.__('select_label').'</option>';
								  }?>
						</select>
					</div>
				</td>
				 
					<td valign="middlle"><label><?php echo __('passenger_name'); ?></label></td>
					<td id="passenger_list">
						<div class="selector ser_input_field" id="uniform-user_type">
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
									$cname=' - ('.ucfirst($values["company_name"]).")";
								}
								$passengername = ucfirst($values["name"]).$cname;
								$selected_status = ($passengerid == $values['id']) ? ' selected="selected" ' : " ";
							if(!empty($values["name"]))
								echo '<option value="'.$values["id"].'"'.$selected_status.'>'.$passengername.'</option>';
							 } } else { 
										  echo '<option value="">'.__('select_label').'</option>';
									  }?>
							</select>
						</div>
					</td>
					
					<?php if($currentPage == 'all' && $list == 'rejected') { ?>
						<td valign="middlle"><label><?php echo __('status'); ?></label></td>
						<td>
							<div class="selector ser_input_field" id="uniform-user_type">
								<select name="travelSts" id="travelSts" class="select2">
									<option value="success"><?php echo __('completed');?></option>
									<option value="cancelled"><?php echo __('cancelled');?></option>
									<option value="rejected" selected><?php echo __('rejected');?></option>
								</select>
							</div>
						</td>
					<?php } ?>
						 <?php if($list !='rejected' ) { ?>
					<td valign="middle"><label><?php echo __('transactionid_label'); ?></label></td>
                        		<td valign="top">

						<div class="ser_input_field">

								  <input type="text"  title="<?php echo __('enter_the_transaction_id'); ?>" id="transaction_id" name="transaction_id" value="<?php echo $transaction_id;?>"  />
						 <span id="transaction_error" class="error"></span>		 
	
						 </div>
						
                        </td> 
						 <?php }
						else
						{ ?>
						  <input type="hidden"  title="<?php echo __('enter_the_transaction_id'); ?>" id="transaction_id" name="transaction_id" value=""  />
						<?php } ?>
					</tr>  
				<tr>  
					<td valign="middle"><label><?php echo __('from_date'); ?></label></td>
                        <td valign="top">
						<div class="ser_input_field">
								  <input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="startdate" name="startdate" value="<?php echo $startdate; ?>"  />
						<?php /* echo date('Y-m-01 00:00:00'); */ ?>
						 <span id="startdate_error" class="error"></span>		 
						 </div>
						
                        </td>       

                        <td valign="middle"><label><?php echo __('end_date'); ?></label></td>
                        <td valign="top">
						<div class="ser_input_field">
								  <input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="enddate" name="enddate" value="<?php echo $enddate; ?>"  />		
						</div>
						<span id="enddate_error" class="error"></span>
                        </td>   
  
			  
                 
                 <?php if($list !='rejected' ) { ?>
				
					<td valign="middlle"><label><?php echo __('payment_type'); ?></label></td>
					<td id="payment_list">
						<div class="selector ser_input_field" id="uniform-user_type">
							<select name="payment_type" id="payment_type" class="select2">
							<?php if(count($gateway_details) > 0) { ?>
							<option value="All"><?php echo __('all_label');?></option>
							<?php
							foreach($gateway_details as $values) { 
								$pay_mod_name = $values["pay_mod_name"];
								$selected_status = ($payment_type == $values['pay_mod_id']) ? ' selected="selected" ' : " ";
							echo '<option value="'.$values["pay_mod_id"].'"'.$selected_status.'>'.ucfirst($pay_mod_name).'</option>';
							 } } else { 
										  echo '<option value="">'.__('select_label').'</option>';
									  }?>
							</select>
						</div>
					</td>  
                 
				<?php }else { ?>
						<input type="hidden" name="payment_type" value="" />
					<?php } ?>
				</tr>
				<tr>
					<?php if($list !='rejected' && $list !='cancelled') { ?>

					<td valign="middlle"><label><?php echo __('payment_mode'); ?></label></td>
					<td id="payment_list">
						<div class="selector ser_input_field" id="uniform-user_type">
							<select name="payment_mode" id="payment_mode" class="select2">
								<option value="All"><?php echo __('all_label');?></option>
							<?php
							foreach($paymentModeArr as $payK=>$payV) {
								$selected_status = ($payment_mode == $payK) ? ' selected="selected" ' : " ";
							echo '<option value="'.$payK.'"'.$selected_status.'>'.ucfirst($payV).'</option>';
							 } ?>
							</select>
						</div>
					</td>
				<?php }else { ?>
						<input type="hidden" name="payment_mode" value="" />
					<?php } ?>
					<?php if($currentPage == 'all' && $list != 'rejected') { ?>
						<td valign="middlle"><label><?php echo __('status'); ?></label></td>
						<td>
							<div class="selector ser_input_field" id="uniform-user_type">
								<select name="travelSts" id="travelSts" class="select2">
									<option value="all" <?php if($list == 'all') { ?>selected<?php } ?> ><?php echo __('all_label');?></option>
									<option value="success" <?php if($list == 'success') { ?>selected<?php } ?> ><?php echo __('completed');?></option>
									<option value="cancelled" <?php if($list == 'cancelled') { ?>selected<?php } ?> ><?php echo __('cancelled');?></option>
									<option value="rejected"><?php echo __('rejected');?></option>
								</select>
							</div>
						</td>
					<?php } ?>
					<?php if($list !='rejected' ) { ?>	 
						<td valign="middle"><label><?php echo __('trip_id'); ?></label></td>
						<td valign="top">
							<div class="ser_input_field">
								<input type="text" maxlength="20"  title="<?php echo __('entry_trip_id'); ?>" id="trip_id" name="trip_id" value="<?php echo $trip_id;?>" />
							</div>
						</td>
					<?php }else{ ?>
						<input type="hidden"  title="<?php echo __('entry_trip_id'); ?>" id="trip_id" name="trip_id" value=""  />
					<?php } ?>
				</tr>
                 <tr>
                        <td valign="top"><label>&nbsp;</label></td>
                        <td>
                            <div class="new_button">
                                <input type="submit" value="<?php echo __('button_search'); ?>" id="search_user_btn" name="search_user" title="<?php echo __('button_search'); ?>" />
                            </div>
                            <div class="new_button">
                                <input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo $back_action; ?>'" />
                            </div>
                        </td>
                 </tr>
                </table>
            <div class="over_all">
		<?php if($list !='rejected' ) { ?>		
                <div class="widget chartWrapper">
            <div class="title"><h6><?php echo __('chart'); ?></h6></div>
            <div class="body">
		<?php 
		if(count($grpahdata)>0)
		{
			foreach($grpahdata as $gdata)
			{
				$fare[] = ($gdata['amount'] != "") ? $gdata['amount'] : 0;
				$completed_amount[] = ($gdata['completed_amount'] != "") ? $gdata['completed_amount'] : 0;
				$cancelled_amount[] = ($gdata['cancelled_amount'] != "") ? $gdata['cancelled_amount'] : 0;
				$pending_amount[] = ($gdata['pending_amount'] != "") ? $gdata['pending_amount'] : 0;
				//echo date( "d",strtotime($gdata['createdate']))."<br>";
				$month[] = "'".date( "d",strtotime($gdata['createdate']))." ".date( "M",strtotime($gdata['createdate']))."'";
				//$month[] = "'".date( "d",strtotime($gdata['pickup_time']))." ".date( "M",strtotime($gdata['pickup_time']))."'";
			}
			
				if($fare){
					$fare = implode(",",$fare);
				}
				if($completed_amount){
					$completed_amount = implode(",",$completed_amount);
				}
				if($cancelled_amount){
					$cancelled_amount = implode(",",$cancelled_amount);
				}
				if($pending_amount){
					$pending_amount = implode(",",$pending_amount);
				}
				if($month){
					$month = implode(",",$month);
				}
		?>
		
		 <div class="chart" id="transaction_chart"></div>
		 <span class="grp_lable"><?php echo __('last_two_months_view_graph') ?></span>
			</div>
<?php } else { echo "<div class='nodata' style='padding:0px 10px 8px 0;'>".__('no_data')."</div></div>"; } ?>
		
        </div>
		<?php } ?>
            </div>
            
		<?php /* <div class="widget">
			<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo $page_title; ?></h6>
			<div style="width:auto; float:right; margin: 0 3px;">
				<div class="button greyishB" > <?php echo $export_excel_button; ?></div>  
				<div class="button greyishB"> <?php echo $export_pdf_button; ?></div>                       
			</div>
		</div> */ ?>
            <div class="over_all">
		<div class="widget" style="margin-bottom:0px !important;">
			<div class="title">
				<h6><?php echo $page_title; ?></h6>
				<div style="width:auto; float:right; margin: 0;"><?php if($count_transaction_list > 0){ $export_table_count = $count_transaction_list; include_once(APPPATH.'views/admin/transaction_export_menu.php'); } ?></div>
			</div>
		</div>
<?php if($total_transaction > 0){ ?>
	<div class= "overflow-block">
<?php } ?>
<table cellspacing="1" cellpadding="11" width="100%" align="center" class="sTable responsive">
<?php if($total_transaction > 0){ ?>
<thead>
	<tr>
		<td align="center" width="5%"><?php echo __('sno_label'); ?></td>
		<?php if($list != 'rejected') { ?>
		<td align="center" width="15%"><?php echo __('cctransaction_id'); ?></td>
		<td align="left" width="10%"><?php echo __('payment_type'); ?></td>
		<?php } ?>
		<td align="center" width="10%"><?php echo __('trip_id'); ?></td>
		<td align="left" width="10%"><?php echo __('passenger_name'); ?></td>
		<td align="left" width="10%"><?php echo ucfirst(__('driver_name')); ?></td>
		<td align="left" width="10%"><?php echo __('companyname'); ?></td>
		<td align="left" width="10%"><?php echo __('journey_date'); ?></td>
		<td align="left" width="10%"><?php echo __('pickuploc_droploc'); ?></td>
		<!--<td align="left" width="10%"><?php echo __('Drop_Location'); ?></td>-->
		<!--<td align="left" width="10%"><?php echo __('No_Passengers'); ?></td>-->
		<?php if($list != 'rejected') { ?>
		<td align="left" width="10%"><?php echo __('admin_commision'); ?></td>
		<td align="left" width="10%"><?php echo __('company_commision'); ?></td>
		<?php /* <td align="left" width="10%"><?php echo __('package_type'); ?></td> */ ?>
		<?php } ?>
		<?php if($list != 'rejected') { ?>
		<?php if($list != 'cancelled') { ?>
		<td align="left" width="10%"><?php echo __('distance'); ?></td>
		<td align="left" width="10%"><?php echo __('nightfare'); ?></td>
		<td align="left" width="10%"><?php echo __('eveningfare'); ?></td>
		<?php } ?>
		<td align="left" width="10%"><?php echo __('wallet_amount'); ?></td>
		<td align="left" width="10%"><?php if($list == 'cancelled') { echo __('cancel_fare'); } else { echo __('trip_total_fare'); }?></td>
		<?php /*<td align="left" width="10%"><?php echo __('equivalent_to_usd').CURRENCY_FORMAT; ?></td> */ ?>

		<?php if($list != 'success') { ?>
		<td align="left" width="10%"><?php echo __('travel_status'); ?></td>
		<?php } ?>
		<!--<td align="left" width="10%"><?php echo __('rating_points');?></td>
		<td align="left" width="10%"><?php echo __('comments');?></td>-->
		<?php } 
		else {	?>
		<td align="left" width="10%"><?php echo __('travel_status');?></td>
		<?php /* <td align="left" width="10%"><?php echo __('reason');?></td> */ ?>
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
<?php /*$company_currency = $listings['company_id'];
			$ccur = findcompany_currency($company_currency); */
			$ccur = CURRENCY; ?>
		<tr class="<?php echo $trcolor; ?>">
			<td align="center"><?php echo $sno; ?></td>
			<?php if($list != 'rejected') {
					$paymentMod = ($listings['payment_method'] == 'L') ? 'Live' : 'Test';
				 ?>
			<td align="center">
				<?php if($listings['transaction_id'] != "")
				{?>
				<a title="<?php echo ucfirst($listings['transaction_id']); ?>" href="<?php echo URL_BASE.'transaction/transaction_details/'.$listings['passengers_log_id'];?>"><?php echo $listings['transaction_id']; ?></a>
				<?php } else { ?>
				- <?php } ?></td>
			<td><?php if($listings['payment_type'] == 2) { echo __('credit_card_using').$listings['payment_gateway_name'].' ('.$paymentMod.')'; } else if($listings['payment_type'] == 4) { echo __('account'); } else if($listings['payment_type'] == 3){ echo __('new_credit_card').' ('.$paymentMod.')'; } else { echo __('cash'); } ?></td>
			<?php } ?>
			<td>
				<?php if($list != 'rejected') { ?>
				<a title="<?php echo ucfirst($listings['passengers_log_id']); ?>" href="<?php echo URL_BASE.'transaction/transaction_details/'.$listings['passengers_log_id'];?>"><?php echo $listings['passengers_log_id']; ?></a>
				<?php } else { ?>
				<?php echo $listings['passengers_log_id']; ?>
				<?php } ?>
			</td>
			
			<td><?php echo ucfirst($listings['passenger_name']); ?></td>
			<td><a title="<?php echo ucfirst($listings['driver_name']); ?>" href="<?php echo URL_BASE.'manage/driverinfo/'.$listings['driver_id'];?>"><?php echo wordwrap(ucfirst($listings['driver_name']),30,'<br/>',1); ?></a></td>
			<td>
				<a title="<?php echo ucfirst($listings['company_name']); ?>" href="<?php echo URL_BASE.'manage/companydetails/'.$listings['userid'];?>">
				<?php  /* if(file_exists($_SERVER["DOCUMENT_ROOT"].'/public/uploads/company/'.$listings['userid'].'.png')){  ?> 
					<img width="32" height="32" src="<?php echo URL_BASE.COMPANY_IMG_IMGPATH.$listings['userid'].'.png';?>"/>
				<?php }else{ ?>
					<img width="32" height="32"  src="<?php echo URL_BASE;?>public/common/images/company_noimage.png"/>
				<?php } */  ?>				
			<?php echo ucfirst($listings['company_name']); ?></a></td>
			<td><?php echo ($listings['actual_pickup_time'] != '0000-00-00 00:00:00' && isset($listings['actual_pickup_time']) && $listings['actual_pickup_time']!='') ? Commonfunction::getDateTimeFormat($listings['actual_pickup_time'],1) : Commonfunction::getDateTimeFormat($listings['pickup_time'],1); ?></td>
			<?php 
			$pic_loc = $listings['current_location'];
			$drop_loc = $listings['drop_location'];
			?>
			<td><b>Pickup</b><br/><?php echo ($pic_loc != 'No address found')?(substr($pic_loc,0,25).'...'):(substr($pic_loc,0,25));?><br/><b>Drop</b><br/><?php echo ($drop_loc != 'No address found')?(substr($drop_loc,0,25).'...'):(substr($drop_loc,0,25));?></td>
			<!--<td><?php //echo $listings['drop_location'];?></td>-->
			<!--<td><?php //echo $listings['no_passengers'];?></td>-->
			<?php if($list != 'rejected') { ?>		
			<td><?php if($listings['admin_amount']) { echo $ccur.$listings['admin_amount']; } else { echo '-'; } ?></td>
			<td><?php if($listings['company_amount']) { echo $ccur.$listings['company_amount']; } else { echo '-'; } ?></td>
			<?php /*
			<td><?php if($listings['trans_packtype'] == 'T' ) { echo __('transaction_based_commission'); } else if($listings['trans_packtype'] == 'P' ) { echo __('package_based_commission'); } else if($listings['trans_packtype'] == 'N' ) { echo __('package_based_no_commission'); } ?> </td>
			*/ ?>
			<?php } ?>
			<?php if($list != 'rejected') { ?>
			<?php if($list != 'cancelled') { ?>
			<td><?php if($listings['distance'] == 0) { echo '-'; } else { echo round($listings['distance'],2).' '.$listings['distance_unit'];}?></td>
			<td><?php if($listings['nightfare'] == 0) { echo '-'; } else { echo $ccur.round($listings['nightfare'],2);}?></td>
			<td><?php if($listings['eveningfare'] == 0) { echo '-'; } else { echo $ccur.round($listings['eveningfare'],2);}?></td>
			<?php } ?>
			
			
			<td><?php 
			if($listings['used_wallet_amount'] == 0) { echo '-'; } else { echo $ccur.round($listings['used_wallet_amount'],2);}?></td>
			<td><?php 
			if($listings['fare'] == 0) { echo '-';$convet_amt = 0; } else { echo $ccur.round($listings['fare'],2); 
			$convet_amt = round($listings['fare'],2);
			}?></td>
			<?php /*<td><?php if($listings['fare'] == 0) { echo '-'; $convet_amt = 0; } else {
				$ccur_for = findcompany_currencyformat($company_currency);
			$convet_amt = currency_conversion($ccur_for,$listings['fare']);
			echo round($convet_amt,2);}?></td> */ ?>

			<?php if($list != 'success') { ?>
			<td><?php if($listings['travel_status'] == 0) { echo __('not_completed'); } else if($listings['travel_status'] == 1) { echo __('completed'); } else if($listings['travel_status'] == 2) { echo __('inprogress'); } else if($listings['travel_status'] == 3) { echo __('start_to_pickup'); } else if($listings['travel_status'] == 4) { echo __('cancel_by_passenger'); } else if($listings['travel_status'] == 8) { echo __('cancelled_by_dispatcher'); } else if($listings['travel_status'] == 9 || $listings['driver_reply'] == 'C') { echo __('cancelled_by_driver'); } else { echo __('not_completed'); } ?> </td>
			<?php } ?>

			<!--<td><?php if($listings['rating'] == 0) { echo '-'; } else { echo $listings['rating']; }?></td>
			<td><?php echo $listings['comments']; ?></td>-->
			<?php }
			else { ?>
			<td><?php if($listings['driver_reply'] == 'C') { echo __('cancelled_by_driver'); } else { echo __('rejected_by_driver'); }?></td>
			<?php /* <td><?php if($listings['driver_comments'] == '') { echo '-'; } else { echo $listings['driver_comments']; }?></td>	*/ ?>
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
			<td></td><td></td>
			<?php } ?>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
			<td></td><td></td>
			<?php if($list != 'success') { ?>
			<td></td>
			<?php } ?>

			<?php if($list == 'success') { ?>
			<td></td>
			<?php } ?>

			<?php if($list != 'all' || $list != 'success') { ?>
				<td></td>
			<?php } ?>
			<?php if($list != 'cancelled') { ?>
				<td></td>
			<?php } ?>
			<td><?php echo __('total').'('.CURRENCY.')';?></td><td><?php echo $totalfare; ?></td><?php if($list != 'cancelled') { ?><td></td><?php } ?>

		

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
            </div>
</form>
</div>
<div class="bottom_contenttot">
<div class="pagination">
		<?php if($total_transaction > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>
  </div>

</div>
</div>
<div id="fade"></div>
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
$("#search_user_btn").click(function(){
	var stFrom = $("#startdate").val();
	var stTo = $("#enddate").val();
	var flag = true;
	//start date and end date validation
	if(stFrom != '' && stTo != '' && to_timestamp(stTo) < to_timestamp(stFrom)) {
		$('#enddate_error').html("End date should be greater than start date");
		flag = false;
	}
	return flag;
});

$("#startdate").datetimepicker( {
showTimepicker:DEFAULT_TIME_SHOW,
showSecond: true,
timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
stepHour: 1,
stepMinute: 1,
maxDateTime : new Date("<?php echo date('Y m d,H:i:s'); ?>"),
stepSecond: 1
} );

$("#enddate").datetimepicker( {
showTimepicker:DEFAULT_TIME_SHOW,
showSecond: true,
timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
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
if(isset($_GET['startdate']) && isset($_GET['enddate'])){

	if($_GET['startdate'] !='' && $_GET['enddate'] !='')
	{
		$text = __('transactions').' '.__('from').' '.$graphStartDate.' '.__('to').' '.$graphEndDate;
	}
	else
	{
		$text = __('transactions');	
	}

}else{
	$text = __('transactions').' '.__('from').' '.$startdate.' '.__('to').' '.$enddate;
}
if(count($grpahdata)>0)
{

	if($list != 'rejected') { ?>
<script type="text/javascript">
	//var startdate = $('#startdate').val();
	var startdate = '<?php echo $grpahstartdate; ?>';
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
        $('#transaction_chart').highcharts({
		title: {
			text: '<?php echo $text;?>',
			x: -20 //center
		},
		credits: {
			enabled: false
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
				text: 'Amount (<?php echo $chart_currency; ?>)'
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
		series: [
		<?php /*if($list == 'all') { ?>
		{
			name: 'Total Amount',
			data: [<?php echo $fare;?>]
		},{
			name: 'Completed Amount',
			data: [<?php echo $completed_amount;?>]
		},{
			name: 'Cancelled Amount',
			data: [<?php echo $cancelled_amount;?>]
		}
		<?php } */ ?>
		<?php if($list == 'all' || $list == 'success') { ?>
		{
			name: '<?php echo __("completed_amount"); ?>',
			data: [<?php echo $completed_amount;?>]
		}
		<?php } ?>
		<?php if($list == 'cancelled') { ?>
		{
			name: '<?php echo __("cancelled_amount"); ?>',
			data: [<?php echo $cancelled_amount;?>]
		}
		<?php } ?>
		<?php if($list == 'pendingpayment') { ?>
		{
			name: '<?php echo __("pending_amount"); ?>',
			data: [<?php echo $pending_amount;?>]
		}
		<?php } ?>
		]
	});
	
    });
</script>
		<?php }
} ?>
<script src="<?php echo SCRIPTPATH; ?>highcharts.js"></script>
