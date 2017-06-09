<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/scale.fix.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/jquery.formance.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/awesome_form.js"></script>
<div class="dash_details payment_option">
	<?php echo View::factory(USERVIEW.'website_user/left_menu'); ?>
	<section id="right_side_part">
		<div class="top_part">
			<div class="bread_com">
				<ul>
					<li><a href="<?php echo URL_BASE; ?>" title="<?php echo __("home_breadcrumb"); ?>"><?php echo __("home_breadcrumb"); ?></a><i class="fa fa-angle-double-right"></i></li>
					<li><p><?php echo __("payment_option"); ?></p></li>
				</ul>
			</div>
			<?php echo View::factory(USERVIEW.'website_user/upcoming_trip_alert'); ?>
		</div>
		<div class="white_bg">
			<div class="completed_trips cancelled_trip">
				<h2><?php echo __("payment_option"); ?></h2>
				<a class="add_card" href="<?php echo URL_BASE."add-card.html"; ?>" title="<?php echo __("add_card"); ?>"><?php echo __("add_card"); ?></a>
                                <div class="scroll_div">
                                <table>
					<tr>
						<th><?php echo __("sno"); ?></th>
						<th><?php echo __("credit_card_no"); ?></th>
						<th><?php echo __("card_verification_no"); ?></th>
						<th><?php echo __("credit_card_expirydate"); ?></th>
						<th><?php echo __("action"); ?></th>
						<th><?php echo __("default_card"); ?></th>
					</tr>
					<?php if(count($saved_card_details) > 0 ) {
						$i = 1;foreach($saved_card_details as $s) {
					?>
					
					<tr>
						<td><?php echo $i; ?></td>
						<td>
							<?php 
								$card_number = chunk_split(encrypt_decrypt('decrypt',$s["creditcard_no"]), 4, ' ');
								$restrict_numers = substr($card_number, 15, 4); 
								echo ("XXXX-XXXX-XXXX-$restrict_numers");
							?>
						</td>
						<td><?php echo "XXX"; ?></td>
						<td><?php echo  $s["expdatemonth"]."-".$s["expdateyear"]; ?></td>
						<td>
                                                    <a class="ico_edit" title="<?php echo __("edit"); ?>" href="<?php echo URL_BASE."users/update_card_details/".$s["passenger_cardid"]; ?>">&nbsp;</a>
							<?php if($s["default_card"] == 0) { ?>
                                                    <a class="ico_delete" title="<?php echo __("delete"); ?>" onclick="return confirm('Are you sure you want to delete card details?');" href="<?php echo URL_BASE."users/delete_card_details/".$s["passenger_cardid"]; ?>">&nbsp;</a>
							<?php } ?>
						</td>
						<td>
							<span id="default_loader_<?php echo $s["passenger_cardid"]; ?>"></span>
							<div id="default_button_<?php echo $s["passenger_cardid"]; ?>">
								<input type="radio" id="t-option" class="change-default-card" data-id="<?php echo $s["passenger_cardid"]; ?>" name="default_card" value="1" <?php if($s["default_card"] > 0) { ?>checked<?php } ?>/>
							</div>
						</td>
					</tr>
					<?php $i++; } } else { ?>
						<tr><td colspan="9"><?php echo __("no_data"); ?></td></tr>
					<?php } ?>
				</table>
                                </div>
				<div class="card_details" style="display:none;">
					<form name="payment_option_form" method="post" autocomplete="off" onsubmit="return validate_payment_option_form();">
						<ul>
							<li class="card_no">
								<input type="text" class="credit_card_number" x-autocompletetype="cc-number" placeholder="<?php echo __("credit_card_no"); ?>" name="creditcard_number" onpaste="return false;">
								<em id="credit_card_error"></em>
							</li>
							<li class="cal_month">
								<input type="text" class="credit_card_expiry" x-autocompletetype="cc-exp" placeholder="<?php echo __("mm_yyyy"); ?>" maxlength="9" onpaste="return false;" name="creditcard_expiry_date">
								<em id="credit_card_expiry_error"></em>
							</li>
							<li class="cvv">
								<input type="text" class="credit_card_cvc" x-autocompletetype="cc-csc" placeholder="<?php echo __("card_verification_no"); ?>" autocomplete="off" onpaste="return false;" name="creditcard_cvv">
								<em id="credit_card_cvc_error"></em>
							</li>
							<li class="submit_payment">
								<input type="submit" value="<?php echo __("btn_submit"); ?>" title="<?php echo __("btn_submit"); ?>" name="submit" id="	"/>
							</li>
						</ul>
						<div class="card_det_terms">
							<p><input type="checkbox" name="set_default_card" value="1"/><?php echo __("default_card"); ?></p>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</div>
<script>
$(document).ready( function () {
	$(".change-default-card").click( function () {
		var data = $(this).data("id");
		$.ajax({
			url:'<?php echo URL_BASE;?>users/change_default_card',
			type:'post',
			dataType:'json',
			data:{ passenger_cardid: data},
			beforeSend:function() {
				$("#default_button_"+data).hide();
				$("#default_loader_"+data).html('<img src="<?php echo URL_BASE."public/frontend/logged_in/images/loading.gif"; ?>" alt="Loading..."/><br>Plese Wait...');
			},
			success:function(res)
			{
				/* $("#default_loader_"+data).html("");
				$("#default_button_"+data).show(); */
				window.location = "<?php echo URL_BASE.'payment-option.html'; ?>";
			}
		});
	});
});
function validate_payment_option_form()
{
	var error = 1;
	var creditcard_number = document.payment_option_form.creditcard_number.value.trim();
	var creditcard_expiry_date = document.payment_option_form.creditcard_expiry_date.value.trim();
	var creditcard_cvv = document.payment_option_form.creditcard_cvv.value.trim();

	if(creditcard_number == "") {
		$("#credit_card_error").html("<?php echo  __('required'); ?>");
		error = 0;
	} else {
		$("#credit_card_error").html("");
	}

	if(creditcard_expiry_date == "") {
		$("#credit_card_expiry_error").html("<?php echo  __('required'); ?>");
		error = 0;
	} else {
		$("#credit_card_expiry_error").html("");
	}

	if(creditcard_cvv == "") {
		$("#credit_card_cvc_error").html("<?php echo  __('required'); ?>");
		error = 0;
	} else {
		$("#credit_card_cvc_error").html("");
		if(creditcard_number == "") {
			$("#credit_card_cvc_error").html("&nbsp;");
		}
	}
	if(error) {
		$('#payment_option_submit_button').val("<?php echo __("please_wait"); ?>").attr('disabled', 'disabled');
		document.payment_option_form.submit();
	} else {
		return false;
	}
}
</script>
