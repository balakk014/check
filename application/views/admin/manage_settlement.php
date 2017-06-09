<?php defined('SYSPATH') OR die("No direct access allowed."); 
   //For search values
   //=================
   $user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :''; 
   $status_val = isset($srch["status"]) ? $srch["status"] :'';
   $keyword = isset($srch["keyword"]) ? $srch["keyword"] :'';
   $startdate = isset($srch["startdate"]) ? $srch["startdate"] : date('Y-m-01 00:00:00');
   $enddate = isset($srch["enddate"]) ? $srch["enddate"] : convert_timezone('now',$_SESSION['timezone']);
   $company_val = isset($srch["filter_company"]) ? $srch["filter_company"] : "All";
   
   //For CSS class deefine in the table if the data's available
   //===========================================================
   $total_company=count($transaction_details);
   
   
   $table_css=$export_excel_button="";
   if($total_company>0)
   { 
   	$table_css='class="table_border"'; 
   
   	$export_excel_button='
   	<input type="button"  title="'.__('button_export').'" class="button" value="'.__('button_export').'" 
   	onclick="location.href=\''.URL_BASE.'manage/export?keyword='.$keyword.'&status='.$status_val.'&type='.$user_type_val.'\'" />
   	';
   }?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<?php /* <script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script> */ ?>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<div class="container_content fl clr">
<div class="cont_container mt15 mt10">
   <div class="content_middle">
      <form method="get" class="form" name="manage_city1" id="manage_city1" action="settlement_list">
         <table class="list_table1" border="0" width="100%" cellpadding="5" cellspacing="0">
            <tr>
               <td valign="top"><label><?php echo __('keyword_label'); ?></label></td>
               <td >
                  <div class="ser_input_field">
                     <input type="text" name="keyword"  maxlength="256" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
                  </div>
                  <span class="search_info_label"><?php echo __('search_by_trip'); ?></span>
               </td>
               <?php if($_SESSION['user_type'] =='A') { ?>
               <td valign="top"><label><?php echo __('company'); ?></label></td>
               <td valign="top">
                  <div class="selector ser_input_field" id="uniform-user_type">
                     <select class="select2" name="filter_company" id="filter_company" >
                        <option value="All"><?php echo __('all_label'); ?></option>
                        <?php 
                           foreach ($get_allcompany as $comapany_list) {
                           $selected_status = ($company_val == $comapany_list['cid']) ? ' selected="selected" ' : " ";
                           $companyName = (isset($comapany_list['company_brand_type']) && $comapany_list['company_brand_type'] == 'S') ? ucfirst($comapany_list["company_name"]).' - Admin' : ucfirst($comapany_list["company_name"]);
                           ?>  
                        <option value="<?php echo $comapany_list['cid']; ?>"  <?php echo $selected_status; ?> ><?php echo  $companyName; ?></option>
                        <?php }?>
                     </select>
                  </div>
               </td>
               <?php } ?>
            </tr>
            <tr>
               <?php //$startdate=date('Y-m-d' ,$current_date); ?>
               <td valign="top"  ><label><?php echo __('start_date'); ?></label></td>
               <td valign="top" >
                  <div class="ser_input_field">
                     <input type="text" readonly  title="<?php echo __('select_datetime'); ?>" id="startdate" name="startdate" value="<?php echo isset($srch['startdate']) ? trim($srch['startdate']) : $startdate; ?>"  />
                     <span id="startdate_error" class="error"></span>		 
                  </div>
               </td>
               <td valign="top"><label><?php echo __('end_date'); ?></label></td>
               <td valign="top" >
                  <div class="ser_input_field">
                     <input type="text"  readonly  title="<?php echo __('select_datetime'); ?>" id="enddate" name="enddate" value="<?php echo isset($srch['enddate']) ? trim($srch['enddate']) :$enddate ; ?>"  />
                     <span id="enddate_error" class="error"></span>							
                  </div>
               </td>
               <!--<td valign="top"><label><?php echo __('status_label'); ?></label></td>
                  <td valign="top">
                  <div class="formRight">
                  <div class="selector" id="uniform-user_type">
                  <span><?php echo __('status_label'); ?></span>
                      <select class="select2" name="status" id="status" onchange="this.form.submit()">
                  	<option value=""><?php echo __('status_label'); ?></option>    
                  	<?php
                     foreach ($status as $status_key => $allstatus) {
                     
                         $selected_status = ($status_val == $status_key) ? ' selected="selected" ' : " ";
                         ?>  
                  	    <option value="<?php echo $status_key; ?>"  <?php echo $selected_status; ?> ><?php echo ucfirst($allstatus); ?></option>
                  	<?php }  ?>
                      </select>
                  </div>
                  </div>    
                  </td>-->
            </tr>
            <tr>
               <td valign="top"><label>&nbsp;</label></td>
               <td>
                  <!--[if IE]>
                  <input type="text" style="display: none;" disabled="disabled" size="1" />
                  <![endif]-->
                  <div class="new_button">
                     <input type="submit" value="<?php echo __('button_search'); ?>" name="search_user" title="<?php echo __('button_search'); ?>" />
                  </div>
                  <div class="new_button">
                     <input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo URL_BASE; ?>transaction/settlement_list'" />
                  </div>
               </td>
            </tr>
         </table>
      </form>
       <div class="over_all">
      <div class="widget">
         <div class="title">
            <h6><?php echo $page_title; ?></h6>
            <div style="width:auto; float:right; margin: 4px 3px;">
               <div class="button greyishB"> <?php //echo $export_excel_button; ?></div>
            </div>
         </div>
         <form method="post" name="manage_city" id="manage_city" action="settlement_list">
         <?php if($total_company > 0){ ?>
         <?php } ?>
         <div class= "overflow-block">
            <table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
               <?php if($total_company > 0){ ?>
               <thead>
                  <tr>
					  <?php
					  /*if($company_id != '' &&  $company_id != '0') {
						  ?>
                     <td align="left" width="8%"><?php echo __('Select'); ?></td>  
                     <?php
				 }*/
				 ?>
                     <td align="center" width="8%"><?php echo __('sno_label'); ?></td>
                     <td align="left"  width="15%"><?php echo __('trip_id'); ?></td>
                     <td align="left"  width="15%"><?php echo __('transaction_id'); ?></td>
                     <td align="left"  width="15%"><?php echo __('company_name'); ?></td>
                     <td align="left"  width="15%"><?php echo __('settlement_status'); ?></td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $sno=$Offset; /* For Serial No */
                     
                     foreach($transaction_details as $listings) {
                     
                     //S.No Increment
                     //==============
                     $sno++;
                     
                     //For Odd / Even Rows
                     //===================
                     $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
                      /*if($company_id != '' &&  $company_id != '0') {
                     ?>     
                  <tr class="<?php echo $trcolor; ?>">
                      <td align="center"><input type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $listings['trip_id'];?>" value="<?php echo $listings['transaction_id'].":".$listings['trip_id'];?>" />
                     </td> 
                     <?php } */ /* </form>*/ ?>
                     <td align="center" width="5%"> <?php echo $sno; ?> </td>
                     <td align="left"  width="15%"><?php echo $listings['trip_id']; ?></td>
                     <td align="left"  width="15%"><?php echo $listings['transaction_id']; ?></td>
                     <td align="left"  width="15%"><?php echo ucwords($listings['company_name']); ?></td>
                     <td align="left"  width="20%"><?php
                        if(isset($listings['payment_status'])=='submitted for settlement')
                        {
                        	echo ucwords($listings['payment_status'])."  - Check in Merchant Backend";
                        }
                        else
                        {
                        	echo isset($listings['payment_status']) ?  ucwords($listings['payment_status']) : '';
                        }?></td>
                  </tr>
                  <?php  }
                     if($sno >=1){
                     ?>
                  <?php /*if($company_id != '' &&  $company_id != '0') { ?>
                  <tr>
                     <td colspan="5"></td>
                     <td><?php echo ($company_id != '' &&  $company_id != '0')?"<div class='new_button'><input  type='submit' name='update' value='Submit Settlement' onclick='return checkCheckBoxes()' /></div>":''; ?></td>
                  </tr>
                  <?php }*/ ?>
                  <?php }else { ?>
                  <tr>
                     <td colspan="4" align="center"><?php echo __('no_data');?></td>
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
      </div>
       </div>
   </div>
      <div class="bottom_contenttot">
      <div class="pagination">
         <?php if($total_company > 0): ?>
         <?php echo $pag_data->render(); ?>
         <?php endif; ?> 
      </div>
      </div>
       </div>
      <!--** Multiple select starts Here ** -->
      <?php /*  if(count($transaction_details) > 0)
         { ?>
      <div class="select_all">
         <b><a id="select-all"><?php echo __('all_label');?></a></b><span class="pr2 pl2">|</span><b><a id="select-none"><?php echo __('select_none');?></a></b>
         <!-- <span style="padding-left:10px;">
            <select name="more_action" id="more_action">
                <option value=""><?php echo __('Change Status'); ?></option>
                <option value="block_city_request" ><?php echo __('Block'); ?></option>
                <option value="active_city_request" ><?php echo __('Active'); ?></option>
                <option value="trash_city_request" ><?php echo __('Trash'); ?></option>
            </select>
            </span>-->
      </div>
      <?php
         } */ ?>
      <!--** Multiple select ends Here ** -->
   </div>

<script type="text/javascript">
   $(document).ready(function(){
   
    $("#keyword").focus(); 
   });
</script>
<script type="text/javascript">
   $('#select-all').click(function(event) {   
           // Iterate each checkbox
           $(':checkbox').each(function() {
               this.checked = true;                        
           });
   });
   
   $('#select-none').click(function(event) {   
           // Iterate each checkbox
           $(':checkbox').each(function() {
               this.checked = false;                        
           });
   });
   
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
   
   					case "block_city_request":
   					var confirm_msg =  "<?php echo __('Are you sure want to block Request(s)?');?>";
   	
   					//Find checkbox whether selected or not and do more action
   					//============================================================
   					if($('input[type="checkbox"]').is(':checked'))
   					{
   				   		 var ans = confirm(confirm_msg)
   				   		 if(ans){
   							 document.manage_city.action="<?php echo URL_BASE;?>manage/block_city_request/index";
   							 document.manage_city.submit();
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
   
   					case "active_city_request":
   					var confirm_msg =  "<?php echo __('Are you sure want to Activate Request(s)?');?>";
   
   
   						//Find checkbox whether selected or not and do more action
   						//============================================================
   						if($('input[type="checkbox"]').is(':checked'))
   						{
   					   		 var ans = confirm(confirm_msg)
   					   		 if(ans){
   								 document.manage_city.action="<?php echo URL_BASE;?>manage/active_city_request/index";
   								 document.manage_city.submit();
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
   
   					case "trash_city_request":
   					var confirm_msg =  "<?php echo __('Are you sure want to move Request(s) to Trash ?');?>";
   
   
   						//Find checkbox whether selected or not and do more action
   						//============================================================
   						if($('input[type="checkbox"]').is(':checked'))
   						{
   					   		 var ans = confirm(confirm_msg)
   					   		 if(ans){
   								 document.manage_city.action="<?php echo URL_BASE;?>manage/trash_city_request/index";
   								 document.manage_city.submit();
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
   	
   $("#startdate").datetimepicker( {
   	showTimepicker:DEFAULT_TIME_SHOW,
   	showSecond: true,
   	timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
   	dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
   	stepHour: 1,
   	stepMinute: 1,
   	stepSecond: 1
   });
   
   $("#enddate").datetimepicker( {
   	showTimepicker:DEFAULT_TIME_SHOW,
   	showSecond: true,
   	timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
   	dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
   	stepHour: 1,
   	stepMinute: 1,
   	stepSecond: 1
   });
   
   function checkCheckBoxes() {
   	var chk = document.getElementsByName('uniqueId[]');
   	var len = chk.length;
   	var result = new Array();
   	for (i=0; i<len; i++) {
   		result[i] = chk[i].checked;
   	}
   	var find_value =result.indexOf(true);
   	if(find_value == '-1')
   	{
   		alert("You must select atleast one checkbox");
   		return false;
   	}
   	else
   	{
   		return true;
   	}
   }
</script> 
</script>
