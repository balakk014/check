<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="new_inner_page_outer">
    <div class="new_inner_page_box">
	<div class="new_page_title">
	    <h2><?php echo ucfirst(__('withdraw_request_view')); ?></h2>
	</div>
	<div class="withdraw_detail_table">
            <div class="responsive_table">
	    <table border="0" cellpadding="10" cellspacing="0" width="100%">				
		<tr>
		    <td valign="top" width="35%">
			<label><?php echo __('request_id'); ?></label>
		    </td>        
		    <td>
			<p><?php if (isset($details[0]['request_id'])) {
    echo "#" . $details[0]['request_id'];
} else {
    echo '';
} ?></p>

		    </td>
		</tr>
			    <?php if ($company_id == 0) { ?>
    		<tr>
    		    <td valign="top" width="35%"><label><?php echo __('brand_type'); ?></label></td>        
    		    <td>
    			<p>
    <?php if (isset($details[0]['brand_type'])) {
	echo ($details[0]['brand_type'] == 1) ? __('Multy') : __('single');
    } else {
	echo '';
    } ?>
    			</p>
    		    </td>
    		</tr>
    		<tr>
    		    <td valign="top" width="35%"><label><?php echo __('name_label'); ?></label></td>        
    		    <td>
    			<p>
				<?php if (isset($details[0]['name'])) {
				    echo $details[0]['name'] . " " . $details[0]['lastname'];
				} else {
				    echo '';
				} ?>
    			</p>
    		    </td>
    		</tr>
			    <?php } ?>
		<tr>
		    <td valign="top" width="35%"><label><?php echo __('withdraw_amount'); ?></label></td>        
		    <td>
			<p>
		<?php if (isset($details[0]['withdraw_amount'])) {
		    echo CURRENCY . $details[0]['withdraw_amount'];
		} else {
		    echo '';
		} ?>
			</p>
		    </td>
		</tr>
		<tr>
		    <td valign="top" width="35%"><label><?php echo __('request_date'); ?></label></td>        
		    <td>
			<p>
<?php if (isset($details[0]['request_date'])) {
    echo Commonfunction::getDateTimeFormat($details[0]['request_date'], 1);
} else {
    echo '';
} ?>
			</p>
		    </td>
		</tr>
		<?php if ($company_id == 0 || $type) { ?>
    		<tr>
    		    <td valign="top" width="35%"><label><?php echo __('status'); ?></label></td>        
    		    <td>
    			<div class="withdraw_input_fields">
						<?php if (isset($details[0]['request_status']) && $details[0]['request_status'] == 0) { ?>
				    <form method="post" name="status_update" id="status_update" action="<?php echo URL_BASE . 'manage/withdrawdeatil/' . $id; ?>" onsubmit="return check_validation_form();" enctype="multipart/form-data" autocomplete="off">
					<input type="hidden" name="type" value="<?php echo $type; ?>"/>
					<ul>
					    <li><select name="status" onchange="changeStatus(this.value);">
						    <option value=""><?php echo __('status_label'); ?></option>
						    <option value="1"><?php echo __('approved'); ?></option>
						    <option value="2"><?php echo __('deny'); ?></option>
						</select>
						<em class='error' id="statusError"></em>
					    </li>
					    <li id="paymodeModeSection">
						<select name="payment_mode">
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
						<em class='error' id="amountError"></em>
					    </li>
					    <li id="transactionIdSection"><input type="text" name="transaction_id" placeholder="<?php echo __('transaction_id'); ?>" value=""/>
						<em class='error' id="transactionidError"></em></li>
					    <li><textarea id="comments" name="comments" placeholder="<?php echo __('comments'); ?>"></textarea>
						<em class='error' id="commentsError"></em></li>
					    <li><input type="file" name="attachment" value=""/>
						<em class='error' id="attachmentError"></em></li>
                                            <li><div class="new_button"><input type="submit" value="<?php echo __('btn_submit'); ?>" id="status_form_submit" name="status_form_submit" title="<?php echo __('btn_submit'); ?>"></div></li>
					</ul>
				    </form>
				    <?php
				} else {
				    $status = __('not_yet_approved');
				    if (isset($details[0]['request_status']) && $details[0]['request_status'] == 1) {
					$status = __('approved');
				    } else if (isset($details[0]['request_status']) && $details[0]['request_status'] == 2) {
					$status = __('deny');
				    }
				    echo "<p>".$status."</p>";
				}
				?>
    			</div>
    		    </td>
    		</tr>
<?php } else { ?>
    		<tr>
    		    <td valign="top" width="35%"><label><?php echo __('status'); ?></label></td>        
    		    <td>
    			<div class="new_input_field">
    <?php
    $status = __('not_yet_approved');
    if (isset($details[0]['request_status']) && $details[0]['request_status'] == 1) {
	$status = __('approved');
    } else if (isset($details[0]['request_status']) && $details[0]['request_status'] == 2) {
	$status = __('deny');
    }
    echo "<p>".$status."</p>";
    ?>
    			</div>
    		    </td>
    		</tr>
					<?php } ?>
	    </table>
            </div>
	</div>
	<div class="withdraw_activity_log">
	    <div class="activity_log_box">
		<div class="active_title">
		    <h2><?php echo ucfirst(__('activity_logs')); ?></h2>
		</div>
		<div class="withdraw_activity_log_list">
			<?php if (count($log) > 0) {
					    foreach ($log as $l) { ?>
		    <ul>
				<li>
				    <div class="withdraw_activity_log_box">
					<div class="withdraw_activity_log_title">
					    <span> <?php echo $l["created_date"]; ?></span>
					    <p> <?php
				    $status = $l["status"];
				    $status_label = __('not_yet_approved');
				    if ($status == 1) {
					$status_label = __('approved');
				    } else if ($status == 2) {
					$status_label = __('rejected');
				    }
				    echo _('status_changedto')." " . $status_label . ".";
				    $data = "";
				    if ($status == 1) {
					$data .= "<p>".__('pay_mode')." : " . $l["payment_mode_name"] . "</p>";
					$data .= "<p>".__('transactionid_label')." : " . $l["transaction_id"] . "</p>";
					$data .= "<b>".__('comments')." </b> : " . $l["comments"];
				    } else if ($status == 2) {
					$data .= "<p><b>".__('reason')." </b> : " . $l["comments"]."</p>";
				    }
				    echo $data;
				    ?>
					</p>
					<p>
					<?php 
						if ($l['file_name'] != "" && file_exists(DOCROOT . WITHDRAW_IMG_PATH . $l['file_name'])) { 
							$allowed_extensions = array("jpg", "jpeg", "gif", "bmp");
							$extension = pathinfo($l['file_name'], PATHINFO_EXTENSION);
							if($extension != "" && in_array($extension,$allowed_extensions)) { ?>
								<img src="<?php echo URL_BASE . WITHDRAW_IMG_PATH . $l['file_name']; ?>" width="100" height="100"/>
							<?php } else { ?>
							<a download href="<?php echo URL_BASE . WITHDRAW_IMG_PATH . $l['file_name']; ?>"><?php echo $l['file_name']; ?></a>
					<?php } } ?>
					</p>
					</div>
				    </div>
				</li>
		    </ul>
		    <?php }
} else { ?>
    			<div class="nodata"><?php echo __('no_data'); ?></div>
<?php } ?>
		</div>
	    </div>
	</div>
 
<script type="text/javascript" language="javascript">
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
function check_validation_form()
{
	var validate = 1;
	var s = document.status_update.status.value.trim();
	if(s != 2) {
		var p = document.status_update.payment_mode.value.trim();
		var t = document.status_update.transaction_id.value.trim();
	}
	var c = document.status_update.comments.value.trim();
	$("#statusError, #amountError, #transactionidError, #commentsError").html("");
	if (s == "") { $("#statusError").html("*Required"); validate = 0; }
	if(s != 2) {
		if (p == "") { $("#amountError").html("*Required"); validate = 0; }
		if (t == "") { $("#transactionidError").html("*Required"); validate = 0; }
	}
	if (c == "") { $("#commentsError").html("*Required"); validate = 0; }
	if(validate) { $("#status_update").submit(); }
	return false;
}
</script>
