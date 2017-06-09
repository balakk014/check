<?php defined('SYSPATH') OR die("No direct access allowed.");
$user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :''; 
$startdate = isset($srch["startdate"]) ? $srch["startdate"] :''; 	
$enddate = isset($srch["enddate"]) ? $srch["enddate"] :''; 	
$form_action = URL_BASE.'manageusers/passengerwalletlogs';
$back_action = URL_BASE.'manageusers/passengerwalletlogs';
?>
<style>
table tr td {
	vertical-align:top;
}
</style>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<?php /* <script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script> */ ?>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>

<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle">
			<form method="get" class="form" name="managerequests" id="manage_model" action="<?php $form_action ?>">
				<table class="list_table1" border="0" width="100%" cellpadding="5" cellspacing="0">
				 <tr>
						<td valign="middle"><label><?php echo __('keyword'); ?></label></td>
                        <td width="20%" >
                            <div class="ser_input_field">
                                <input type="text" name="keyword" maxlength="55" style="width:94%;" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
                            </div>
                            <span class="search_info_label"><?php echo __('search_by_name_phone'); ?></span>
                        </td>
                        <td width="8%"><label><?php echo __('from_date'); ?></label></td>
                        <td >
						<div class="ser_input_field">
								  <input type="text" readonly  title="<?php echo __('select_datetime'); ?>" id="startdate" name="startdate" value="<?php echo isset($srch['startdate']) ? trim($srch['startdate']) : ''; ?>"  />
						 <span id="st_startdate_error" class="error"></span>		 
						 </div>
						
                        </td>       
                        <td width="8%"><label><?php echo __('end_date'); ?></label></td>
                        <td>
						<div class="ser_input_field">
								  <input type="text"  readonly  title="<?php echo __('select_datetime'); ?>" id="enddate" name="enddate" value="<?php echo isset($srch['enddate']) ? trim($srch['enddate']) : ''; ?>"  />						
						</div>
						<span id="st_enddate_error" class="error"></span>	
                        </td>   
                        
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
     		<div class="widget" style="margin-bottom:0px !important;">
		<div class="title"><h6><?php echo $page_title; ?></h6>
		<div style="width:auto; float:right; "> <?php if($getLogsCount > 0){ $export_table_count = $getLogsCount; include_once(APPPATH.'views/admin/export_menu.php'); } ?></div>
		</div>
		</div>     
<?php if($getLogsCount > 0){ ?>


	<div class= "overflow-block">
<?php } ?>
	<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
<?php if ($getLogsCount > 0) { ?>
		<thead>
		    <tr >
				<td align="center" width="5%"><?php echo __('sno_label'); ?></td>
				<td align="center" width="5%"><?php echo __('transaction_id'); ?></td>
				<td align="left" width="15%"><?php echo __('booking_date'); ?></td>
				<td align="left" width="15%"><?php echo __('name'); ?></td>
				<td align="left" width="15%"><?php echo __('phone_label'); ?></td>
				<td align="left" width="15%"><?php echo __('amount'); ?></td>
				<td align="left" width="13%"><?php echo __('payment_type'); ?></td>
		    </tr>
		</thead>
        <tbody>
		<?php

		$sno=$Offset; /* For Serial No */
		//print_r($all_user_list);
		 foreach($requestLists as $req_list) {
		 
		 //S.No Increment
		 //==============
		 $sno++;
        
         //For Odd / Even Rows
         //===================
         $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
		 
        ?>     

	<tr class="<?php echo $trcolor; ?>">

		<td align="center"><?php echo $sno; ?></td>
		<td align="left">
			<a title="<?php echo ucfirst($req_list['transaction_id']); ?>" href="<?php echo URL_BASE.'manageusers/passengerwalletlogs_details/'.base64_encode($req_list['transaction_id']); ?>" >
				<?php echo $req_list['transaction_id']; ?>
			</a>
		</td>
		<td  align="left"><?php echo Commonfunction::getDateTimeFormat($req_list['createdate'],1); ?></td>
		<td align="left"><?php echo $req_list['name']; ?></td>
		<td align="left"><?php echo $req_list['mobile_number']; ?></td>
		<td align="left"><?php echo $req_list['amount']; ?></td>
		<td align="left"><?php echo $req_list['payment_type']; ?></td>
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
	</div>
<?php if ($getLogsCount > 0) { ?>
	</div>
<?php } ?>
</form>
</div>
<div class="bottom_contenttot">
<div class="pagination">
		<?php if($getLogsCount > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>
 </div>

</div>
</div>
 <div id="fade"></div>
<script type="text/javascript">
$(document).ready(function(){
	$("input[type='text']:first", document.forms[0]).focus();

	$("#search_user_btn").click(function(){
		var stFrom = $("#startdate").val();
		var stTo = $("#enddate").val();
		var flag = true;
		//start date validation
		if(stFrom != '' && stTo != '' && stTo < stFrom) {
			$('#st_enddate_error').html("End date should be greater than start date");
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
		stepSecond: 1,
	});

	$("#enddate").datetimepicker( {
		showTimepicker:DEFAULT_TIME_SHOW,
		showSecond: true,
		timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
		dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
		stepHour: 1,
		stepMinute: 1,
		stepSecond: 1,
	});
} );


</script>
