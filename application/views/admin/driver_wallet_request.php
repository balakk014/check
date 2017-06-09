<?php defined('SYSPATH') OR die("No direct access allowed.");
$user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :''; 
$startdate = isset($srch["startdate"]) ? $srch["startdate"] :''; 	
$enddate = isset($srch["enddate"]) ? $srch["enddate"] :''; 	
$form_action = URL_BASE.'manage/walletrequests';
$back_action = URL_BASE.'manage/walletrequests';
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
                                <input type="text" name="keyword" maxlength="55" style="width:100%;" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
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
				<?php if($_SESSION['user_type'] =='A') { ?>
					<td valign="middle"><label><?php echo __('company'); ?></label></td>
                        <td width="20%" >
                                <select name="company" id="company" class="required widings ser_input_field">
									<option value=""><?php echo __('All'); ?></option>
									<?php 
									$field_type =''; if(isset($srch['company'])){ $field_type =  $srch['company']; } 
									if(count($taxicompany_details) > 0) { ?>
										<?php foreach($taxicompany_details as $company_list) {
											$companyName = (isset($company_list['company_brand_type']) && $company_list['company_brand_type'] == 'S') ? ucfirst($company_list["company_name"]).' - Admin' : ucfirst($company_list["company_name"]);
											 ?>
										<option value="<?php echo $company_list['cid']; ?>" <?php if($field_type == $company_list['cid']) { echo 'selected=selected'; } ?> ><?php echo $companyName; ?></option>
                                    
										<?php } ?>
									<?php } ?>
								 </select>
								 
							
								 
                        </td>
                   <?php } else { ?>
                   <td></td><td></td>
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
     		<div class="widget" style="margin-bottom:0px !important;">
		<div class="title"><h6><?php echo $page_title; ?></h6>
		<div style="width:auto; float:right; margin:0 0;"> <?php if($countDriverRequests > 0){ $export_table_count = $countDriverRequests; include_once(APPPATH.'views/admin/export_menu.php'); } ?><?php //echo $export_excel_button; ?></div>
		</div>
		</div>     
<?php if($countDriverRequests > 0){ ?>


	<div class= "overflow-block">
<?php } ?>
	<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
<?php if ($countDriverRequests > 0) { ?>
		<thead>
		    <tr >
				<td align="left" width="5%"><?php echo __('select'); ?></td>
				<td align="left" width="5%"><?php echo __('sno_label'); ?></td>
				<td align="left" width="15%"><?php echo __('name'); ?></td>
				<td align="left" width="15%"><?php echo __('phone'); ?></td>
				<?php if($_SESSION['user_type'] =='A') { ?>
				<td align="left" width="15%"><?php echo __('company_name'); ?></td>
				<?php } ?>
				<td align="left" style="text-align:left;" width="15%"><?php echo __('request_amount'); ?></td>
				<td align="left" style="text-align:left;" width="15%"><?php echo __('request_date'); ?></td>
				<td align="left" style="text-align:left;" width="15%"><?php echo __('approved_date'); ?></td>
				<td align="left" width="13%"><?php echo __('request_status'); ?></td>
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

		<td>
			<?php if($req_list['wallet_request_status'] == 1) { ?>
			<input type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $req_list['wallet_request_id'];?>" value="<?php echo $req_list['wallet_request_id'];?>" />
			<?php } else { echo '-'; } ?>
		</td>
		<td align="center"><?php echo $sno; ?></td>
		<td align="center"><a title="<?php echo ucfirst($req_list['driverName']); ?>" href="<?php echo URL_BASE.'manage/driverinfo/'.$req_list['driver_id'];?>"><?php echo $req_list['driverName']; ?></a></td>
		<td align="center"><?php echo $req_list['driverPhone']; ?></td>
		<?php if($_SESSION['user_type'] =='A') { ?>
		<td align="center"><?php echo ($req_list['company_name'] != '') ? $req_list['company_name'] : 'All'; ?></td>
		<?php } ?>
		<td style="text-align:left;" align="center"><?php echo $req_list['wallet_request_amount']; ?></td>
		<td style="text-align:left;" align="center"><?php echo Commonfunction::getDateTimeFormat($req_list['wallet_request_date'],1); ?></td>
		<td style="text-align:left;" align="center"><?php echo ($req_list['wallet_request_status'] == 2 && $req_list['approved_date'] != '0000-00-00 00:00:00') ? Commonfunction::getDateTimeFormat($req_list['approved_date'],1) : '-'; ?></td>
		<td align="center"><?php echo $req_list['reqstatuslbl']; ?></td>
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
<?php if ($countDriverRequests > 0) { ?>
	
<?php } ?>
                                </div>
</form>
                </div>
<div class="bottom_contenttot">

<!--** Multiple select starts Here ** -->
<?php if($countDriverRequests > 0)
       { ?>
          <div class="select_all manage_fag">
			<ul>
			<li>
                <b><a href="javascript:selectToggle(true, 'managerequests');"><?php echo __('all_label');?></a></b></li>
				
				<li><span class="pr5 pl5">|</span></li><li><b><a href="javascript:selectToggle(false, 'managerequests');"><?php echo __('select_none');?></a></b></li>
				</ul>

                <span class="more_selection">
                    <select name="more_action" id="more_action">
                        <option value=""><?php echo __('change_status'); ?></option>
                        <option value="approve" ><?php echo __('approve_label'); ?></option>
                        <option value="disapprove" ><?php echo __('Reject'); ?></option>
                    </select>
                 </span>
	        </div>
        <?php
        } ?>
<!--** Multiple select ends Here ** -->
<div class="pagination">
		<?php if($countDriverRequests > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>

</div>
</div>
</div>
<script type="text/javascript">
 $(document).ready(function(){
	$("input[type='text']:first", document.forms[0]).focus();
});
</script>


<script type="text/javascript">

	function selectToggle(toggle, form) {
		var myForm = document.forms[form];
		for( var i=0; i < myForm.length; i++ ) { 
		    if(toggle) {
		        myForm.elements[i].checked = "checked";
		    } 
		    else
		    { myForm.elements[i].checked = ""; }
		}
	}

	
	//for More action Drop Down
	//=========================
	$('#more_action').change(function() {

		//select drop down option value
		//======================================
		var selected_val= $('#more_action').val();
		
			//perform more action reject withdraw
			//===================================		
			switch (selected_val){
					//	Current Action "approve" 
					//===================================
					case "approve":
						var confirm_msg =  "<?php echo __('areyousure_wantto_approve_request');?>";
		
						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
							 var ans = confirm(confirm_msg)
							 if(ans){
								 document.managerequests.action="<?php echo URL_BASE;?>manage/approveWalletRequest";
								 document.managerequests.submit();
							 }else{
								$('#more_action').val('');
							 }
		
						}
						else{
								//alert for no record select
								//=============================
								alert("<?php echo __('please_select_atleast_oneormore_record_todo_thisaction');?>")	
								$('#more_action').val('');
						}					
					break;
					//	Current Action "disapprove"
					//=========================
					case "disapprove":
						var confirm_msg =  "<?php echo __('areyou_surewanttodisapprove_request');?>";
						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.managerequests.action="<?php echo URL_BASE;?>manage/disapproveWalletRequest";
								 document.managerequests.submit();
							 }else{
							 	$('#more_action').val('');
							 }		
						}
						else{
						        //alert for no record select
						        //=============================
							    alert("<?php echo __('please_select_atleast_oneormore_record_todo_thisaction');?>")	
							    $('#more_action').val('');
						}						

					break;

				}		
			return false;  
	});
	
$(document).ready(function(){
	$("#search_user_btn").click(function(){
		var stFrom = $("#startdate").val();
		var stTo = $("#enddate").val();
		var flag = true;
		//start date validation
		if(stFrom != '' && stTo != '' && to_timestamp(stTo) < to_timestamp(stFrom)) {
			$('#st_enddate_error').html("<?php echo __('enddate_shouldbegreaterthan_startdate'); ?>");
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
	} );

	$("#enddate").datetimepicker( {
		showTimepicker:DEFAULT_TIME_SHOW,
		showSecond: true,
		timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
		dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
		stepHour: 1,
		stepMinute: 1,
		stepSecond: 1,
	} );

} );


</script>
