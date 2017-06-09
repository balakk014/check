<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<link rel="stylesheet" href="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<?php /* <script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script> */ ?>
<script src="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<div class="new_inner_page_outer">
    <div class="widget">
	<div class="title">
	    <h6><?php echo __('withdraw_request'); ?></h6>
            <div style="float: right;margin: 0;"> 
		<?php if ($count_data > 0) {
		    $export_table_count = $count_data;
		    include_once(APPPATH . 'views/admin/export_menu.php');
		} ?>
	    </div>
        </div>
    </div>
    <div class="content_middle_out">
    <div class="content_middle_new">
	    <?php if ($company_id > 0) { ?>
        <div class="new_button" style="float: right;margin-top: 10px;margin-right: 0;"> 
        <input class="sendRequest" type="button" value="<?php echo __("withdraw_request"); ?>" />
    </div>
<?php } ?>
	
	 <form method="get" class="form" name="managerequests" id="manage_model" action="<?php echo URL_BASE . 'manage/withdrawrequest'; ?>" autocomplete="off">
	<div class="withdraw_seach">
	   
		<ul>
		    <li>
			<input type="text" placeholder="<?php echo __('keyword'); ?>" name="keyword" maxlength="45" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
		    </li>
		    <li>
			<div class="withdraw_select">
			<select name="brand_type">
			    <option value=""><?php echo __('brand_type'); ?></option>
			    <option <?php if (isset($srch['brand_type']) && $srch['brand_type'] == 1) { ?>selected<?php } ?> value="1"><?php echo __('Multy'); ?></option>
			    <option <?php if (isset($srch['brand_type']) && $srch['brand_type'] == 2) { ?>selected<?php } ?> value="2"><?php echo __('Single'); ?></option>
			</select>
			</div>
		    </li>
		    <li>
			<div class="withdraw_select">
			<select name="status">
			    <option value=""><?php echo __('status'); ?></option>
			    <option <?php if (isset($srch['status']) && $srch['status'] != '' && $srch['status'] == 0) { ?>selected<?php } ?> value="0"><?php echo __('not_yet_approved'); ?></option>
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
		    <li>
                        <div class="new_button"><input type="submit" value="<?php echo __('button_search'); ?>" id="search_user_btn" name="search_user" title="<?php echo __('button_search'); ?>" /></div>

			<!--			<div class="button blueB">
						    <input type="button" value="<?php //echo __('button_cancel');  ?>" title="<?php //echo __('button_cancel');  ?>" onclick="location.href = '<?php //echo URL_BASE . 'manage/withdrawrequest';  ?>'" />
						</div>-->
		    </li>
		</ul>
	 		    
	</div>
	<div class="withdraw_count_box">
	    <div id="quick-actions">
<?php if ($company_id == 0) { ?>
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
    			    <a href="javascript:;" title="<?php echo __('payment_to_single_brand'); ?>" class="green-square"></a>
    			    <h2><?php echo __('payment_to_single_brand'); ?></h2>    							
    			    <p><?php echo ($dashboard_data[0]["payment_transaction_single"] != '') ? CURRENCY . $dashboard_data[0]["payment_transaction_single"] : CURRENCY . "0"; ?></p>
    			</div>
    		    </li>
    		    <li class="color_code_with6">
    			<div class="dash_active_left">
    			    <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w6.png"  />
    			</div>
    			<div class="withdraw_detail_right">
    			    <a href="javascript:;" title="<?php echo __('payment_to_multi_brand'); ?>" class="sea-square"></a>
    			    <h2><?php echo __('payment_to_multi_brand'); ?></h2>    							
    			    <p><?php echo ($dashboard_data[0]["payment_transaction_multy"] != '') ? CURRENCY . $dashboard_data[0]["payment_transaction_multy"] : CURRENCY . "0"; ?></p>
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
<?php } else { ?>
    		<ul>
		    <li class="color_code_with4">
    			<div class="dash_active_left">
    			    <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w4.png"  />    				
    			</div>    
    			<div class="withdraw_detail_right">			    
    			    <h2><?php echo __('payment_transcaction'); ?></h2>
    			    <p><?php echo ($dashboard_data[0]["payment_transaction"] != '') ? CURRENCY . $dashboard_data[0]["payment_transaction"] : CURRENCY . "0"; ?></p>
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
		    <li class="color_code_with2">
    			<div class="dash_active_left">
    			    <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w2.png"  />    				
    			</div>  
    			<div class="withdraw_detail_right">	
    			    <a href="javascript:;" title="<?php echo __('payment_approved'); ?>" class="green-square"></a>
    			    <h2><?php echo __('payment_approved'); ?></h2>
    			    <p><?php echo ($dashboard_data[0]["approved_withdraw_request_count"] > 0) ? $dashboard_data[0]["approved_withdraw_request_count"] : 0; ?></p>
    			</div>
    		    </li>
    		    <li  class="color_code_with3">
			<div class="dash_active_left">
    			    <img class="image" src="<?php echo IMGPATH; ?>dashboard_icons/w3.png"  />    				
    			</div>  
			<div class="withdraw_detail_right">
			    <a href="javascript:;" title="<?php echo __('payment_deneid'); ?>" class="blue-square"></a>
			    <h2><?php echo __('payment_deneid'); ?></h2>
			    <p><?php echo ($dashboard_data[0]["deny_withdraw_request_count"] > 0) ? $dashboard_data[0]["deny_withdraw_request_count"] : 0; ?></p>
			</div>  
		    </li>
    		</ul>
<?php } ?>
	    </div>
	</div>

	<div class="withdraw_table">
            <div class="responsive_table">
	    <table cellspacing="0" cellpadding="10" width="100%" align="center">
		<thead>
		    <tr>
<?php if ($company_id == 0) { ?>
    			<th align="left" width="5%"><?php echo __('select'); ?></th>
			<?php } ?>
			<th align="left" width="5%"><?php echo __('sno_label'); ?></th>
			<th align="left" width="15%"><?php echo __('request_id'); ?></th>
			<?php if ($company_id == 0) { ?>
    			<th align="left" width="15%"><?php echo __('brand_type'); ?></th>
    			<th align="left" width="15%"><?php echo __('name_label'); ?></th>
<?php } ?>
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
				    <?php if ($company_id == 0) { ?>
	    			<td align="center">
					<?php if ($req_list['request_status'] == 0) { ?>
					    <input data-request-id="<?php echo '#' . $req_list['request_id']; ?>" type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $req_list['withdraw_request_id']; ?>" value="<?php echo $req_list['withdraw_request_id']; ?>" />
				    <?php
				    } else {
					echo '-';
				    }
				    ?>
	    			</td>
				<?php } ?>
				<td align="center"><?php echo $sno; ?></td>
				<td>#<?php echo $req_list['request_id']; ?></td>
	<?php if ($company_id == 0) { ?>
	    			<td><?php echo ($req_list['brand_type'] == 1) ? __('Multy') : __('single'); ?></td>
	    			<td><?php echo ucfirst($req_list['name'] . ' ' . $req_list['lastname']); ?></td>
				    <?php } ?>
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
				<td align="center"><a href="<?php echo URL_BASE . "manage/withdrawdeatil/" . $req_list['withdraw_request_id']; ?>" class="view_icon"></a></td>
			    </tr>
    <?php }
} else {
    ?>
    		    <tr><td colspan="9" class="" align="center"><?php echo __('no_data'); ?></td></tr>
	<?php } ?>
		</tbody>
	    </table>
            </div>
	</div>
    </div>
	<div class="table_bottom_control">
<?php if ($company_id == 0) { ?>
    	<!--** Multiple select starts Here ** -->
    <?php if ($count_data > 0) { ?>
	<ul class="select_all_part">
		    <li><a href="javascript:selectToggle(true, 'managerequests');"><?php echo __('all_label'); ?></a></li>
		    <li><a href="javascript:selectToggle(false, 'managerequests');"><?php echo __('select_none'); ?></a></li>
		    <li><div class="bottom_selection_select">
			<select name="more_action" id="more_action">
			    <option value=""><?php echo __('change_status'); ?></option>
			    <option value="approved" ><?php echo __('approved'); ?></option>
			    <option value="deny" ><?php echo __('deny'); ?></option>
			</select>
			</div>
		    </li>
		</ul>
		<?php } ?>
    	<!--** Multiple select ends Here ** -->
<?php } ?>
	
<?php if ($count_data > 0): ?>
    	    <?php echo $pag_data->render(); ?>
<?php endif; ?> 
	
	</div>
	        </form>
    </div>
</div>

<?php if ($company_id == 0) { ?>
    <?php /** List Page Status Change Using Popup Start * */ ?>
    <div id="myModal" class="modal_popup" style="display:none;">
        <div class="modal-content withdraw_request_change">
    	<div class="withdraw_popup_header">    	    
    	    <h2><?php echo __('withdraw_status_change'); ?></h2>
	    <span class="close">×</span>
    	</div>
    	<div class="withdraw_popup_form">
    	    <form method="post" name="list_status_update" onsubmit="return check_list_validation_form();" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="type" value="0"/>
    		<input type="hidden" name="withdraw_request_id" value=""/>
		<ul>
    		    <li><input type="text" readonly name="request_id" placeholder="Request ID" value=""/>
    			<em class='error' id="requestIdError"></em></li>
    		    <li><select name="status" onchange="changeStatus(this.value);" id="selectedstatus">
    			    <option value=""><?php echo __('status_label'); ?></option>
    			    <option value="1"><?php echo __('approved'); ?></option>
    			    <option value="2"><?php echo __('deny'); ?></option>
    			</select>
    			<em class='error' id="statusError"></em></li>
    		    <li id="paymodeModeSection"><select name="payment_mode">
    			    <option value=""><?php echo __('pay_mode'); ?></option>
				<?php if (count($payment_mode) > 0) {
				    foreach ($payment_mode as $pay) {
					?>
	    			    <option value="<?php echo $pay["withdraw_payment_mode_id"]; ?>"><?php
				echo ucfirst($pay["payment_mode_name"]
				);
					?></option>
	<?php }
    }
    ?>
    			</select>
    			<em class='error' id="amountError"></em></li>
    		    <li id="transactionIdSection"><input type="text" name="transaction_id" placeholder="<?php echo __('transaction_id'); ?>" value=""/>
    			<em class='error' id="transactionidError"></em></li>
    		    <li><textarea id="comments" name="comments" placeholder="<?php echo __('comments'); ?>"></textarea>
    			<em class='error' id="commentsError"></em></li>
    		    <li><input type="file" name="attachment" value=""/>
    			<em class='error' id="attachmentError"></em></li>
				<li><div class="new_button"> <input type="submit" value="<?php echo __('btn_submit'); ?>" name="status_form_submit" title="<?php echo __('btn_submit'); ?>"></div></li>		   
    		</ul>
    		<input type="hidden" name="status_form_submit" value="1">
    	    </form>
    	</div>    	
        </div>
    </div>
    <?php /** List Page Status Change Using Popup End * */ ?>
		    <?php } else { ?>
<div id="requestModal" class="withdrow_requestpop" style="display: none;">
	<div class="withdrow_reuestpopinner">
        <div class="modal-content withdraw_request_change">
    	<div class="withdraw_popup_header">
    	    <span class="close">×</span>
    	    <h2><?php echo __("withdraw_request"); ?></h2>
    	</div>
    <div class="withdraw_popup_form">
    	    <form method="post" name="company_request_amount" id="company_request_amount" onsubmit="return validate_request_form();" autocomplete="off">
		<ul>
		    <li><strong><?php echo __('payment_availed'); ?> :</strong> <?php echo CURRENCY; ?><span id="payment_availed"><?php echo COMPANY_AVAILABLE_AMOUNT; ?></span></li>
		    <?php
				$pending_amt = ($dashboard_data[0]["payment_transaction_pending"] != '') ? $dashboard_data[0]["payment_transaction_pending"] : 0;
		    ?>
		    <li><strong><?php echo __('payment_req_pending'); ?> :</strong> <span><?php echo CURRENCY . $pending_amt; ?></span></li>
		    <li><strong>  <?php echo __('request_amount'); ?> : </strong>
    		    <input type="text" class="numbersOnly" readonly onpaste="return false;" name="company_request_amount" placeholder="<?php echo __('trans_amt'); ?>" value="<?php echo COMPANY_AVAILABLE_AMOUNT - $pending_amt; ?>"/>
    		    <em class='error' id="requestAmountError"></em></li>
		    <li>
			<div class="new_button">
				<input type="submit" value="<?php echo __('btn_submit'); ?>" name="company_request_form" title="<?php echo __('btn_submit'); ?>">
			</div>
			<div class="new_button">
    		    <input type="button" value="<?php echo __('cancel'); ?>" id="closePopup" title="<?php echo __('cancel'); ?>" >
			</div>
		    </li>
		  
		</ul>    		
    	    </form>
    	</div>
    	
        </div>
    </div>
	</div>

<?php } ?>

<div id="fade"></div>

<script type="text/javascript">
    $(document).ready(function() {
	$("#startdate,#enddate").datetimepicker({
	    showTimepicker:DEFAULT_TIME_SHOW,
	    showSecond: true,
	    timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	    dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	    stepHour: 1,
	    stepMinute: 1,
	    stepSecond: 1
	});

<?php if ($company_id == 0) { ?>
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
    		    var confirm_msg =  "<?php echo __('areyousure_wantto_approve_request'); ?>";
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
    			$("#selectedstatus").val(1);
    			$("#paymodeModeSection,#transactionIdSection").show();
                       
    			modal.style.display = "block";
    		    } else {
    			alert("<?php echo __('please_select_atleast_oneormore_record_todo_thisaction'); ?>")	
    			$('#more_action').val('');
    			$('#fade').hide();
    		    }
    		    break;
    		case "deny":
    		    var confirm_msg =  "<?php echo __('areyousure_wantto_activate_request'); ?>";
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
    			$("#comments").attr("placeholder", "Reason");
    			$("#paymodeModeSection,#transactionIdSection").hide();
    			$("#selectedstatus").val(2);
    			modal.style.display = "block";
    		    } else {
    			//alert for no record select
    			//=============================
    			alert("<?php echo __('please_select_atleast_oneormore_record_todo_thisaction'); ?>")	
    			$('#more_action').val('');
    			$('#fade').hide();
    		    }
    		    break;
    		}
    		return false;  
    	    });
<?php } else { ?>
    		    // Get the modal
    		    var modal = document.getElementById('requestModal');

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
    		    }

    		    // When the user clicks anywhere outside of the modal, close it
    		    window.onclick = function(event) {
    			if (event.target == modal) {
    			    modal.style.display = "none";
                            $('#fade').hide();
    			}
    		    }
    	
    		    jQuery('.numbersOnly').keyup(function () { 
    			this.value = this.value.replace(/[^0-9\.]/g,'');
    		    });
    	
    		    $(".sendRequest").click( function () {
    			$("#requestAmountError").html("");
			modal.style.display = "block";
                        $('#fade').show();
                                        
    		    });
    		    $("#closePopup").click( function () {
    			$("#requestAmountError").html("");
    			modal.style.display = "none";
    			$('#fade').hide();
                        
    		    });
    		    $(".close").click( function () {
					$('#fade').hide();
    		    });
<?php } ?>
		});

<?php if ($company_id == 0) { ?>
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
    	    var s = document.list_status_update.status.value.trim();
    	    var r = document.list_status_update.request_id.value.trim();
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
				document.list_status_update.submit(); 
    	    }
    	    return false;
    	}
<?php } else { ?>
    	function validate_request_form()
    	{
    	    var validate = 1;
    	    var amount =  document.company_request_amount.company_request_amount.value.trim();
    	    var payment_availed = $("#payment_availed").text();
    	    $("#requestAmountError").html("");
    	    if (amount == "") { 
    		$("#requestAmountError").html("*Required"); 
    		validate = 0; 
    	    } else if (amount != "" && parseFloat(amount) == 0) {
    		$("#requestAmountError").html("*Amount should be greater than 0"); 
    		validate = 0; 
    	    } else if (amount != "" && parseFloat(amount) > 0 && parseFloat(amount) > parseFloat(payment_availed)) {
    		$("#requestAmountError").html("*Invalid Amount"); 
    		validate = 0; 
    	    }
    	    if(validate) {
    		document.company_request_amount.submit(); 
    		$("#company_request_amount").submit();
    	    }
    	    return false;
    	}
<?php } ?>
</script>
