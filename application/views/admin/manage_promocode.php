<?php defined('SYSPATH') OR die("No direct access allowed.");
$user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :'';
$startdate = isset($srch["startdate"]) ? $srch["startdate"] :'';
$enddate = isset($srch["enddate"]) ? $srch["enddate"] :'';
$form_action = URL_BASE.'manage/promocode/';
$back_action = URL_BASE.'manage/promocode/';
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
		<div class="content_middle promos_code">
			<form method="get" class="form" name="manage_model" id="manage_model" action="<?php $form_action ?>">
<table class="list_table1" border="0" width="100%" cellpadding="5" cellspacing="0">
 <tr>

						
                         <td width="8%" class="wid100" ><label><b><?php echo __('start_date'); ?></b></label></td>
                        <td width="8%"><label><?php echo __('from'); ?></label></td>
                        <td >
						<div class="ser_input_field">
								  <input type="text" readonly  title="<?php echo __('select_datetime'); ?>" id="startdate" name="startdate" value="<?php echo isset($srch['startdate']) ? trim($srch['startdate']) : ''; ?>"  />
						 <span id="st_startdate_error" class="error"></span>		 
						 </div>
						
                        </td>       
                        <td width="8%"><label><?php echo __('to'); ?></label></td>
                        <td>
						<div class="ser_input_field">
								  <input type="text"  readonly  title="<?php echo __('select_datetime'); ?>" id="enddate" name="enddate" value="<?php echo isset($srch['enddate']) ? trim($srch['enddate']) : ''; ?>"  />						
						</div>
						<span id="st_enddate_error" class="error"></span>	
                        </td>   
                        
			</tr>
                        
			<tr>
				<?php /* if($_SESSION['user_type'] =='A') { ?>
					<td valign="middle"><label><?php echo __('company'); ?></label></td>
                        <td width="20%" >
                            <div class="new_input_field">
                                <select name="company" id="company" style="width:190px;" class="required">
									<option value=""><?php echo __('All'); ?></option>
									<?php 
									$field_type =''; if(isset($srch['company'])){ $field_type =  $srch['company']; } 
									if(count($taxicompany_details) > 0) { ?>
										<?php foreach($taxicompany_details as $company_list) { ?>
										<option value="<?php echo $company_list['cid']; ?>" <?php if($field_type == $company_list['cid']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($company_list["company_name"]); ?></option>
										<?php } ?>
									<?php } ?>
								 </select>
                            </div>
                        </td>
                   <?php } else { ?>
                   <td></td><td></td>
                   <?php } */ ?>
					<td valign="middle" class="wid100" width="8%"><label><b><?php echo __('expire_date'); ?></b></label></td>
					<td valign="middle" width="8%"><label><?php echo __('from'); ?></label></td>
					<td >
					<div class="ser_input_field">
							  <input type="text" readonly  title="<?php echo __('select_datetime'); ?>" id="e_startdate" name="e_startdate" value="<?php echo isset($srch['e_startdate']) ? trim($srch['e_startdate']) : ''; ?>"  />
					<span id="exp_startdate_error" class="error"></span>		 
					</div>
					
					</td>       
					<td valign="middle" width="8%"><label><?php echo __('to'); ?></label></td>
					<td>
					<div class="ser_input_field">
							  <input type="text" readonly  title="<?php echo __('select_datetime'); ?>" id="e_enddate" name="e_enddate" value="<?php echo isset($srch['e_enddate']) ? trim($srch['e_enddate']) : ''; ?>"  />
					</div>
					<span id="exp_enddate_error" class="error"></span>		
					</td>   
			</tr>
                        <tr>
                            <td colspan="2" valign="middle" class="promo_rgt"><label><?php echo __('promocode'); ?></label></td>
                            <td colspan="2" width="20%" >
                            <div class="ser_input_field">
                                <input type="text" name="keyword"  class="wids_100" maxlength="10" style="width:100%;" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
                            </div>
                        </td>
                        </tr>
			 <tr>
                                        <td colspan="2" valign="top"><label>&nbsp;</label></td>
                                        <td colspan="2">
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
     		<div class="widget">
		<div class="title"><h6><?php echo $page_title; ?></h6>
		<?php /*<div style="width:auto; float:right; margin: 4px 3px;">
		<div class="button greyishB"> <?php echo $export_excel_button; ?></div>
		</div>*/ ?>
		</div>        
<?php if($total_users > 0){ ?>


	<div class= "overflow-block">
<?php } ?>
	<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
<?php if ($total_users > 0) { ?>
		<thead>
		    <tr >
		<!--<td align="left" width="5%"></td>-->
		<td align="center" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" width="15%"><?php echo ucfirst(__('promocode')); ?></td>
		<td align="center" width="15%"><?php echo __('promocode_discount'); ?></td>
		<?php if($_SESSION['user_type'] =='A') { ?>
		<?php /* <td align="left" width="15%"><?php echo __('company_name'); ?></td> */ ?>
		<?php } ?>
		<td align="left" style="text-align:left;" width="15%"><?php echo __('start_date'); ?></td>
		<td align="left" style="text-align:left;" width="15%"><?php echo __('expiry_date'); ?></td>
		<td align="center" width="13%"><?php echo __('promo_limit'); ?></td>
		<td align="center" width="13%"><?php echo __('passengers_count'); ?></td>
		<td align="center" width="50%"><?php echo __('action_label'); ?></td>
		    </tr>
		</thead>
                  <tbody>               
                         
		<?php

		$sno=$Offset; /* For Serial No */
		//print_r($all_user_list);
		 foreach($promocode_list as $promocode_list) {
		 
		 //S.No Increment
		 //==============
		 $sno++;
        
         //For Odd / Even Rows
         //===================
         $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
		 
        ?>     

	<tr class="<?php echo $trcolor; ?>">

		<!--<td><input type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $promocode_list['passenger_promoid'];?>" value="<?php echo $promocode_list['passenger_promoid'];?>" /></td>-->
		<td align="center"><?php echo $sno; ?></td>
		<td align="left"><a href="<?php echo URL_BASE.'manage/promoinfo/'.$promocode_list['passenger_promoid'];?>"><?php echo $promocode_list['promocode']; ?></a></td>
		<td align="center"><?php echo $promocode_list['promo_discount']; ?></td>
		<?php if($_SESSION['user_type'] =='A') { ?>
		<?php /* <td align="center"><?php echo ($promocode_list['company_name'] != '') ? $promocode_list['company_name'] : 'All'; ?></td> */ ?>
		<?php } ?>
		<td  align="left"><?php echo Commonfunction::getDateTimeFormat($promocode_list['start_date'],1); ?></td>
		<td  align="left"><?php echo Commonfunction::getDateTimeFormat($promocode_list['expire_date'],1); ?></td>
		<td align="center"><?php echo $promocode_list['promo_limit']; ?></td>
		<td align="center"><?php echo count(explode(",",$promocode_list['passenger_id'])); ?></td>

		<!--<td> 
		<?php 
		/*if($listings['user_status']=='A')
		{  $txt = "Activate"; $class ="unsuspendicon";    }
		elseif($listings['user_status']=='T')
		{$txt = "Trash"; $class ="trashicon";}
		else{  $txt = "Deactivate"; $class ="blockicon";      }


		echo '<a href="javascript:void(0);" title ='.$txt.' class='.$class.'></a>' ;  
		*/?>
		</td> -->
		
		<td align="center" width="20" colspan='3' ><?php echo '<a href='.URL_BASE.'edit/promocode/'.$promocode_list['passenger_promoid'].' " title ="Edit" class="editicon"></a>' ; ?></td>

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

                    <?php if ($total_users > 0) { ?>
                        </div>
                            <?php } ?>
</form>
</div>
</div>
</div>
<div class="bottom_contenttot">
<div class="pagination">
		<?php if($total_users > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>
  </div>

<!--** Multiple select starts Here ** -->
<?php if(count($promocode_list) > 0)
       { ?>
          <!--<div class="select_all">
                <b><a href="javascript:selectToggle(true, 'frmusers');"><?php echo __('all_label');?></a></b><span class="pr2 pl2">|</span><b><a href="javascript:selectToggle(false, 'frmusers');"><?php echo __('select_none');?></a></b>

                 <span class="more_selection">
                    <select name="more_action" id="more_action">
                        <option value=""><?php echo __('Change Status'); ?></option>
                        <option value="block_passenger_request" ><?php echo __('Block'); ?></option>
                        <option value="active_passenger_request" ><?php echo __('Active'); ?></option>
                        <option value="trash_passenger_request" ><?php echo __('Trash'); ?></option>
                    </select>
                 </span>
	        </div>
        <?php
        } ?>
<!--** Multiple select ends Here ** -->



</div>

<script type="text/javascript" language="javascript">
//For Delete the users
//=====================

function frmdel_user(userid)
{
   var answer = confirm("<?php echo __('delete_alert2');?>");
    
	if (answer){
        window.location="<?php echo URL_BASE;?>admin/delete_passenger/"+userid;
    }
    
    return false;  
}  
function frmblk_user(userid,status)
{   
    window.location="<?php echo URL_BASE;?>admin/blkunblk_passenger/"+userid+"/"+status;    
    return false;  
}  

</script>
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


					//	Current Action "reject"//block 
					//===================================

					case "block_passenger_request":
					var confirm_msg =  "<?php echo __('Are you sure want to block Request(s)?');?>";
	
					//Find checkbox whether selected or not and do more action
					//============================================================
					if($('input[type="checkbox"]').is(':checked'))
					{
				   		 var ans = confirm(confirm_msg)
				   		 if(ans){
							 document.frmusers.action="<?php echo URL_BASE;?>manageusers/block_passenger_request/index";
							 document.frmusers.submit();
						 }else{
						 	$('#more_action').val('');
						 }
	
					}
					else{
					        //alert for no record select
					        //=============================
						    alert("<?php echo __('Please select atleast one or more Record(s) to do this action');?>")	
						    $('#more_action').val('');
					}					
					break;



					//	Current Action "approve"
					//=========================

					case "active_passenger_request":
					var confirm_msg =  "<?php echo __('Are you sure want to Activate Request(s)?');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.frmusers.action="<?php echo URL_BASE;?>manageusers/active_passenger_request/index";
								 document.frmusers.submit();
							 }else{
							 	$('#more_action').val('');
							 }		
						}
						else{
						        //alert for no record select
						        //=============================
							    alert("<?php echo __('Please select atleast one or more Record(s) to do this action');?>")	
							    $('#more_action').val('');
						}						

					break;


                	//	Current Action "trash"
					//==========================

					case "trash_passenger_request":
					var confirm_msg =  "<?php echo __('Are you sure want to move Request(s) to Trash ?');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.frmusers.action="<?php echo URL_BASE;?>manageusers/trash_passenger_request/index";
								 document.frmusers.submit();
							 }else{
							 	$('#more_action').val('');
							 }		
						}
						else{
						        //alert for no record select
						        //=============================
							    alert("<?php echo __('Please select atleast one or more Record(s) to do this action');?>")	
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
	var expFrom = $("#e_startdate").val();
	var expTo = $("#e_enddate").val();
	var flag = true;
	//start date validation
	if(stFrom != '' && stTo != '' && stTo < stFrom) {
		$('#st_enddate_error').html("End date should be greater than start date");
		flag = false;
	}
	//expire date validation
	if(expFrom != '' && expTo != '' && expTo < expFrom) {
		$('#exp_enddate_error').html("End date should be greater than start date");
		flag = false;
	}
	return flag;
});


$("#startdate").datetimepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
stepSecond: 1,
 /*onSelect: function (selected) {
	/*var dt = new Date(selected);
	console.log(dt);
	dt.setDate(dt.getDate() + 1);
	$("#enddate").datepicker("option", "minDate", dt);*/
	/*var timev = new Date($(this).datetimepicker('getDate').getTime());
	//$('#enddate').datetimepicker('setDate',timev);
    $( "#enddate" ).datetimepicker( "option", "minDate", timev );
}*/
} );

$("#enddate").datetimepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
stepSecond: 1,
/*onSelect: function (selected) {
		/*var edt = new Date(selected);
		edt.setDate(edt.getDate() - 1);
		$("#startdate").datepicker("option", "maxDate", edt);*/
		/*var timev = new Date($(this).datetimepicker('getDate').getTime());
		//$('#startdate').datetimepicker('setDate',timev);
		$( "#startdate" ).datetimepicker( "option", "maxDate", timev );
	}*/
} );

$("#e_startdate").datetimepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
stepSecond: 1
} );

$("#e_enddate").datetimepicker( {
showTimepicker:true,
showSecond: true,
timeFormat: 'hh:mm:ss',
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
stepSecond: 1
} );
} );


</script>
