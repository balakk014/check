<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<?php /* <script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script> */ ?>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<div class="new_inner_page_outer">

    
   
   <form method="get" class="form" name="managerequests" id="manage_model" action="<?php echo URL_BASE . 'manage/driverwithdraw'; ?>" autocomplete="off">
       
       
       <div class="withdraw_seach">
         <ul>
            <li><input type="text" placeholder="<?php echo __('keyword'); ?>" name="keyword" maxlength="256" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" /></li>
            <li><input type="text" placeholder="<?php echo __('driver_name_label'); ?>" name="driver_name" maxlength="256" value="<?php echo isset($srch['driver_name']) ? trim($srch['driver_name']) : ''; ?>" /></li>
            <li>
               <div class="withdraw_select">
                  <select name="status">
                     <option value=""><?php echo __('status'); ?></option>
                     <option <?php if (isset($srch['status']) && $srch['status'] == 0) { ?>selected<?php } ?> value="0"><?php echo __('not_yet_approved'); ?></option>
                     <option <?php if (isset($srch['status']) && $srch['status'] == 1) { ?>selected<?php } ?> value="1"><?php echo __('approved'); ?></option>
                     <option <?php if (isset($srch['status']) && $srch['status'] == 2) { ?>selected<?php } ?> value="2"><?php echo __('deny'); ?></option>
                  </select>
               </div>
            </li>
            <li class="date_picker_icon">
               <input type="text" placeholder="<?php echo __('from_date_label'); ?>" readonly  title="<?php echo __('select_datetime'); ?>" id="startdate" name="startdate" value="<?php echo isset($srch['startdate']) ? trim($srch['startdate']) : date('Y-m-d 00:00:00', strtotime('-7 days')); ?>" />
               <span id="st_startdate_error" class="error"></span>
            </li>
            <li class="date_picker_icon">
               <input type="text" placeholder="<?php echo __('end_date_label'); ?>" readonly  title="<?php echo __('select_datetime'); ?>" id="enddate" name="enddate" value="<?php echo isset($srch['enddate']) ? trim($srch['enddate']) : convert_timezone('now',$_SESSION['timezone']); ?>"/>
               <span id="st_enddate_error" class="error"></span>
            </li>
            <li><div class="new_button"><input type="submit" value="<?php echo __('button_search'); ?>" id="search_user_btn" name="search_user" title="<?php echo __('button_search'); ?>" /></div></li>
            <!--<input type="button" value="<?php //echo __('button_cancel');  ?>" title="<?php //echo __('button_cancel');  ?>" onclick="location.href = '<?php //echo URL_BASE.'manage/driverwithdraw';  ?>'" /> -->
         </ul>
      </div>
      <div class="withdraw_count_box">
         <div id="quick-actions">
            <ul>
               <li class="color_code_with1">
                  <div class="dash_active_left">
                     <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w1.png"  />
                  </div>
                  <div class="withdraw_detail_right">
                     <a href="javascript:;" title="<?php echo __('no_of_withdraw_resquest'); ?>" class="blue-square"></a>
                     <h2><?php echo __('no_of_withdraw_resquest'); ?></h2>
                     <p><?php echo $dashboard_data[0]["total_withdraw_request_count"]; ?></p>
                  </div>
               </li>
               <li class="color_code_with2">
                  <div class="dash_active_left">
                     <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w2.png"  />
                  </div>
                  <div class="withdraw_detail_right">
                     <a href="javascript:;" title="<?php echo __('no_of_resquest_approved'); ?>" class="red-square"></a>
                     <h2><?php echo __('no_of_resquest_approved'); ?></h2>
                     <p><?php echo ($dashboard_data[0]["approved_withdraw_request_count"] > 0) ? $dashboard_data[0]["approved_withdraw_request_count"] : 0; ?></p>
                  </div>
               </li>
               <li class="color_code_with3">
                  <div class="dash_active_left">
                     <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w3.png"  />
                  </div>
                  <div class="withdraw_detail_right">
                     <a href="javascript:;" title="<?php echo __('no_of_resquest_deneid'); ?>" class="blue-square"></a>
                     <h2><?php echo __('no_of_resquest_deneid'); ?></h2>
                     <p><?php echo ($dashboard_data[0]["deny_withdraw_request_count"] > 0) ? $dashboard_data[0]["deny_withdraw_request_count"] : 0; ?></p>
                  </div>
               </li>
               <li class="color_code_with4">
                  <div class="dash_active_left">
                     <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w4.png"  />
                  </div>
                  <div class="withdraw_detail_right">
                     <a href="javascript:;" title="<?php echo __('payment_transcaction'); ?>" class="sea-square"></a>
                     <h2><?php echo __('payment_transcaction'); ?></h2>
                     <p><?php echo ($dashboard_data[0]["payment_transaction"] != '') ? CURRENCY . $dashboard_data[0]["payment_transaction"] : CURRENCY . "0"; ?></p>
                  </div>
               </li>
               <li class="color_code_with5">
                  <div class="dash_active_left">
                     <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w5.png"  />
                  </div>
                  <div class="withdraw_detail_right">
                     <a href="javascript:;" title="<?php echo __('payment_to_driver'); ?>" class="green-square"></a>
                     <h2><?php echo __('payment_to_driver'); ?></h2>
                     <p><?php echo ($dashboard_data[0]["payment_transaction_single"] != '') ? CURRENCY . $dashboard_data[0]["payment_transaction_single"] : CURRENCY . "0"; ?></p>
                  </div>
               </li>
               <li class="color_code_with7">
                  <div class="dash_active_left">
                     <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w7.png"  />
                  </div>
                  <div class="withdraw_detail_right">
                     <a href="javascript:;" title="<?php echo __('payment_deneid'); ?>" class="green-square"></a>
                     <h2><?php echo __('payment_deneid'); ?></h2>
                     <p><?php echo ($dashboard_data[0]["payment_transaction_deneid"] != '') ? CURRENCY . $dashboard_data[0]["payment_transaction_deneid"] : CURRENCY . "0"; ?></p>
                  </div>
               </li>
               <li class="color_code_with8">
                  <div class="dash_active_left">
                     <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w8.png"  />
                  </div>
                  <div class="withdraw_detail_right">
                     <a href="javascript:;" title="<?php echo __('payment_request_pending'); ?>" class="red-square"></a>
                     <h2><?php echo __('payment_request_pending'); ?></h2>
                     <p><?php echo ($dashboard_data[0]["payment_transaction_pending"] != '') ? CURRENCY . $dashboard_data[0]["payment_transaction_pending"] : CURRENCY . "0"; ?></p>
                  </div>
               </li>
            </ul>
         </div>
      </div>
      <div class="withdraw_table responsive_table">
         <table cellspacing="0" cellpadding="10" width="100%" align="center">
            <thead>
               <tr>
                  <th align="left" width="5%"><?php echo __('select'); ?></th>
                  <th align="left" width="5%"><?php echo __('sno_label'); ?></th>
                  <th align="left" width="15%"><?php echo __('request_id'); ?></th>
                  <th align="left" width="15%"><?php echo __('driver_name_label'); ?></th>
                  <th align="left" width="15%"><?php echo __('withdraw_amount'); ?></th>
                  <th align="left" width="15%"><?php echo __('request_date'); ?></th>
                  <th align="left" width="15%"><?php echo __('status'); ?></th>
                  <th align="left" width="13%"><?php echo __('view'); ?></th>
               </tr>
            </thead>
            <tbody>
               <?php
                  if ($count_data > 0) {
                  $sno = $Offset;
                  foreach ($data as $req_list) {
                   $sno++;
                   $trcolor = ($sno % 2 == 0) ? 'oddtr' : 'eventr';
                   ?>
               <tr class="<?php echo $trcolor; ?>">
                  <td>
                     <?php if ($req_list['request_status'] == 0) { ?>
                     <input data-request-id="<?php echo '#' . $req_list['request_id']; ?>" type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $req_list['withdraw_request_id']; ?>" value="<?php echo $req_list['withdraw_request_id']; ?>" />
                     <?php } else {
                        echo '-';
                        } ?>
                  </td>
                  <td align="left"><?php echo $sno; ?></td>
                  <td>#<?php echo $req_list['request_id']; ?></td>
                  <td><?php echo ucfirst($req_list['name'] . ' ' . $req_list['lastname']); ?></td>
                  <td><?php echo CURRENCY . $req_list['withdraw_amount']; ?></td>
                  <td><?php echo Commonfunction::getDateTimeFormat($req_list['request_date'], 1); ?></td>
                  <td>
                     <?php
                        $status = __('not_yet_approved');
                        $color = "#FC921A";
                        if ($req_list['request_status'] == 1) {
                        $status = __('approved');
                        $color = "#089910";
                        } else if ($req_list['request_status'] == 2) {
                        $status = __('deny');
                        $color = "#F92E12";
                        }
                        ?>
                     <span style="color: <?php echo $color; ?>;"><?php echo $status; ?></span>
                  <td><a href="<?php echo URL_BASE . "manage/withdrawdeatil/" . $req_list['withdraw_request_id']; ?>" class="view_icon"></a></td>
               </tr>
               <?php }
                  } else { ?>
               <tr>
                  <td colspan="9" align="center"><?php echo __('no_data'); ?></td>
               </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
       </div>
      <div class="bottom_contenttot">
         <?php if ($count_data > 0) { ?>
         <div class="select_all manage_fag">
            <ul>
               <li>
                  <b><a href="javascript:selectToggle(true, 'managerequests');"><?php echo __('all_label'); ?></a></b>
               </li>
			   <li>
                  <span class="pr5 pl5">|</span>
               </li>
               <li><b><a href="javascript:selectToggle(false, 'managerequests');"><?php echo __('select_none'); ?></a></b></li>
               
            </ul>
                  <span class="more_selection">
                     <select name="more_action" id="more_action">
                        <option value=""><?php echo __('change_status'); ?></option>
                        <option value="approved" ><?php echo __('approved'); ?></option>
                        <option value="deny" ><?php echo __('deny'); ?></option>
                     </select>
                  </span>
              
         
         <?php } ?>	
         <div class="pagination">  
            <?php if ($count_data > 0): ?>
            <?php echo $pag_data->render(); ?> 
            <?php endif; ?> 			
         </div>
      </div>
   </form>

<?php /** List Page Status Change Using Popup Start **/ ?>
<div id="myModal" class="modal_popup" style="display:none;">
        <div class="modal-content withdraw_request_change">
    	<div class="withdraw_popup_header">  
         <span class="close">×</span>
         <h2><?php echo __('withdraw_status_change'); ?></h2>
      </div>
      <div class="withdraw_popup_form">
         <form method="post" name="list_status_update" id="list_status_update" onsubmit="return check_list_validation_form();" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="type" value="1"/>
            <input type="hidden" name="withdraw_request_id" value=""/>
            <ul>
               <li><input type="text" name="request_id" placeholder="Request ID" value=""/>
                  <em class='error' id="requestIdError"></em>
               </li>
               <li>
                  <select name="status" onchange="changeStatus(this.value);">
                     <option value=""><?php echo __('status_label'); ?></option>
                     <option value="1"><?php echo __('approved'); ?></option>
                     <option value="2"><?php echo __('deny'); ?></option>
                  </select>
                  <em class='error' id="statusError"></em>
               </li>
               <li id="paymodeModeSection">
                  <select name="payment_mode">
                     <option value=""><?php echo "Payment Mode"; ?></option>
                     <?php if(count($payment_mode) > 0) { foreach($payment_mode as $pay) { ?>
                     <option value="<?php echo $pay["withdraw_payment_mode_id"]; ?>"><?php echo ucfirst($pay["payment_mode_name"]
                        ); ?></option>
                     <?php } } ?>
                  </select>
                  <em class='error' id="amountError"></em>
               </li>
               <li id="transactionIdSection"><input type="text" name="transaction_id" placeholder="<?php echo __('transaction_id'); ?>" value=""/>
                  <em class='error' id="transactionidError"></em>
               </li>
               <li><textarea id="comments" name="comments" placeholder="<?php echo __('comments'); ?>"></textarea>
                  <em class='error' id="commentsError"></em>
               </li>
               <li><input type="file" name="attachment" value=""/>
                  <em class='error' id="attachmentError"></em>
               </li>
               <li><div class="new_button"><input type="submit" value="<?php echo __('btn_submit'); ?>" name="status_form_submit" title="<?php echo __('btn_submit'); ?>"></div></li>
            </ul>
         </form>
      </div>
   </div>
</div>
<div id="fade"></div>
<?php /** List Page Status Change Using Popup End **/ ?>
<script type="text/javascript">
   $(document).ready(function() {
   	
   $("#startdate,#enddate").datetimepicker({
   	showTimepicker:DEFAULT_TIME_SHOW,
   	showSecond: true,
   	timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
   	dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
   	stepHour: 1,
   	stepMinute: 1,
   	stepSecond: 1,
   });
   
   
   	// Get the modal
   	var modal = document.getElementById('myModal');
   	modal.style.display = "none";
   
   	// Get the button that opens the modal
   	//var btn = document.getElementById("myBtn");
   
   	// Get the <span> element that closes the modal
   	var span = document.getElementsByClassName("close")[0];
   
   	// When the user clicks the button, open the modal
   	/* btn.onclick = function() {
   		modal.style.display = "block";
   	} */
   
   	// When the user clicks on <span> (x), close the modal
   	span.onclick = function() {
   		$('#more_action').val('');
   		$('input:checkbox').removeAttr('checked');
   		modal.style.display = "none";
   		$('#fade').hide();
   	}
   
   	// When the user clicks anywhere outside of the modal, close it
   	window.onclick = function(event) {
   		if (event.target == modal) {
   			modal.style.display = "none";
   		}
   	}
   
   	//for More action Drop Down
   	//=========================
   	$('#more_action').change(function() {
   		$("input[name=request_id],input[name=transaction_id],select[name=status],#comments").val('');
   		$("#statusError, #amountError, #transactionidError, #commentsError").html("");
   		//select drop down option value
   		//======================================
   		var selected_val= $('#more_action').val();
   		//perform more action reject withdraw
   		//===================================
   		switch (selected_val) {
   			//	Current Action "reject"//block 
   			//===================================
   			case "approved":
   			var confirm_msg =  "<?php echo __('areyousure_wantto_approve_request');?>";
   			 $('#fade').show();
   
   			//Find checkbox whether selected or not and do more action
   			//============================================================
   			if($('input[type="checkbox"]').is(':checked')) {
   				var request_ids = $("input[type=checkbox]:checked").map(function() {
   					return $(this).data("request-id");
   				}).get().join(",");
   				var withdraw_request_id = $("input[type=checkbox]:checked").map(function() {
   					return this.value;
   				}).get().join(",");
   				$("input[name=withdraw_request_id]").val(withdraw_request_id);
   				$("input[name=request_id]").val(request_ids);
   				$("select[name=status]").val(1);
   				$("#paymodeModeSection,#transactionIdSection").show();
   				modal.style.display = "block";
   			} else {
   				alert("<?php echo __('please_select_atleast_oneormore_record_todo_thisaction');?>")	
   				$('#more_action').val('');
   				$('#fade').hide();
   			}
   			break;
   			case "deny":
   				var confirm_msg =  "<?php echo __('areyousure_wantto_activate_request');?>";
   				 $('#fade').show();
   				//Find checkbox whether selected or not and do more action
   				//============================================================
   				if($('input[type="checkbox"]').is(':checked')) {
   					var request_ids = $("input[type=checkbox]:checked").map(function() {
   						return $(this).data("request-id");
   					}).get().join(",");
   					var withdraw_request_id = $("input[type=checkbox]:checked").map(function() {
   						return this.value;
   					}).get().join(",");
   					$("input[name=withdraw_request_id]").val(withdraw_request_id);
   					$("input[name=request_id]").val(request_ids);
   					$("select[name=status]").val(2);
   					$("#comments").attr("placeholder", "Reason");
   					$("#paymodeModeSection,#transactionIdSection").hide();
   					modal.style.display = "block";
   				} else {
   					//alert for no record select
   					//=============================
   					alert("<?php echo __('please_select_atleast_oneormore_record_todo_thisaction');?>")	
   					$('#more_action').val('');
   					$('#fade').hide();
   				}
   			break;
   		}
   		return false;  
   	});
   });
   
   function changeStatus(id)
   {
   	if(id == 2) {
   		$("#paymodeModeSection,#transactionIdSection").hide();
   		$("#comments").attr("placeholder", "Reason");
   	} else {
   		$("#paymodeModeSection,#transactionIdSection").show();
   		$("#comments").attr("placeholder", "Comments");
   	}
   }
   
   function selectToggle(toggle, form) {
   	var myForm = document.forms[form];
   	for( var i=0; i < myForm.length; i++ ) { 
   		if(toggle) {
   			myForm.elements[i].checked = "checked";
   		} else { 
   			myForm.elements[i].checked = ""; 
   		}
   	}
   }
   
   function check_list_validation_form()
   {
   	var validate = 1;
   	var r = document.list_status_update.request_id.value.trim();
   	var s = document.list_status_update.status.value.trim();
   	if(s != 2) {
   		var p = document.list_status_update.payment_mode.value.trim();
   		var t = document.list_status_update.transaction_id.value.trim();
   	}
   	var c = document.list_status_update.comments.value.trim();
   	$("#requestIdError,#amountError, #statusError, #transactionidError, #commentsError").html("");
   	if (r == "") { $("#requestIdError").html("*Required"); validate = 0; }
   	if (s == "") { $("#statusError").html("*Required"); validate = 0; }
   	if(s != 2) {
   		if (p == "") { $("#amountError").html("*Required"); validate = 0; }
   		if (t == "") { $("#transactionidError").html("*Required"); validate = 0; }
   	}
   	if (c == "") { $("#commentsError").html("*Required"); validate = 0; }
   	if(validate) { 
   		//document.list_status_update.submit(); 
   		$("#list_status_update").submit(); 
   		
   	}
   	return false;
   }
</script>
