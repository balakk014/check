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
					<li><p><?php echo __("update_card_details"); ?></p></li>
				</ul>
			</div>
			<?php echo View::factory(USERVIEW.'website_user/upcoming_trip_alert'); ?>
		</div>
		<div class="white_bg">
			<div class="completed_trips cancelled_trip">
				<h2><?php echo __("update_card_details"); ?></h2>
				<div class="card_details">
					<form name="payment_option_form" method="post" autocomplete="off" onsubmit="return validate_payment_option_form();">
						<ul>
							<li class="card_no">
							<div class="select_box">
								<select name="card_type">
									<option <?php if($card[0]['card_type'] == "P") { ?>selected<?php } ?> value="P"><?php echo __('personal'); ?></option>
									<option <?php if($card[0]['card_type'] == "B") { ?>selected<?php } ?> value="B"><?php echo __('business'); ?></option>
								</select>
                                                            </div>
							</li>
							<li class="card_no">
								<input type="text" id="creditcard_number" class="credit_card_number" x-autocompletetype="cc-number" placeholder="<?php echo __("credit_card_no"); ?>" name="creditcard_number" onpaste="return false;" value="<?php echo trim(chunk_split(encrypt_decrypt('decrypt',$card[0]["creditcard_no"]), 4, ' ')); ?>">
								<em id="credit_card_error"></em>
							</li>
							<?php
								$month = (strlen($card[0]['expdatemonth']) == 1) ? "0".$card[0]['expdatemonth'] : $card[0]['expdatemonth'];
								$year = $card[0]['expdateyear'];
								$month_year = $month." / ".$year;
							?>
							<li class="cal_month" style="margin-left: 0;">
								<input type="text" id="creditcard_expiry_date" class="credit_card_expiry" x-autocompletetype="cc-exp" placeholder="<?php echo __("mm_yyyy"); ?>" maxlength="9" onpaste="return false;" name="creditcard_expiry_date" value="<?php echo $month_year; ?>">
								<em id="credit_card_expiry_error"></em>
							</li>
							<li class="cvv">
								<input type="text" class="credit_card_cvc" maxlength="4" x-autocompletetype="cc-csc" placeholder="<?php echo __("card_verification_no"); ?>" autocomplete="off" onpaste="return false;" name="creditcard_cvv" id="creditcard_cvv" value="">
								<em id="credit_card_cvc_error"></em>
							</li>
							<li>
								<input type="submit" title="<?php echo __("btn_submit"); ?>" value="<?php echo __("btn_submit"); ?>" name="submit_card_details" id="payment_option_submit_button"/>
							</li>
						</ul>
						<div class="card_det_terms">
							<p><input type="checkbox" name="set_default_card" <?php if($card[0]['default_card']) { ?>checked<?php } ?> value="1"/><?php echo __('default_card'); ?></p>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</div>
<script>
function validate_payment_option_form()
{
	var error = 1;
	var creditcard_number = $('#creditcard_number').val().trim();
	var creditcard_expiry_date = $('#creditcard_expiry_date').val().trim();
	var creditcard_cvv = $('#creditcard_cvv').val().trim();

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
		if(creditcard_expiry_date != "") {
			var d = new Date();
			var current_month = d.getMonth() + 1;
			current_month = (current_month <= 12) ? current_month : 1;
			var current_year = d.getFullYear();
			var data = creditcard_expiry_date.replace(/\s/g, '').split("/");

			if (parseInt(data[1]) < current_year) {
				$("#credit_card_expiry_error").html("<?php echo  __('card_year_expired'); ?>");
				error = 0;
			} else if (parseInt(data[1]) > current_year) {
				if((parseInt(data[0]) < 1 || parseInt(data[0]) > 12)) {
					$("#credit_card_expiry_error").html("<?php echo  __('card_month_expired'); ?>");
					error = 0;
				} else {
					$("#credit_card_expiry_error").html("");
				}
			} else if (parseInt(data[1]) == current_year) {
				if((parseInt(data[0]) < current_month) || (parseInt(data[0]) < 1 || parseInt(data[0]) > 12)) {
					$("#credit_card_expiry_error").html("<?php echo  __('card_month_expired'); ?>");
					error = 0;
				} else {
					$("#credit_card_expiry_error").html("");
				}
			} else {
				$("#credit_card_expiry_error").html("");
			}
		}
	}

	if(creditcard_cvv == "") {
		$("#credit_card_cvc_error").html("aaa");
		alert("required fields missing");
		error = 0;
	} else {
		if(creditcard_cvv.length >= 3 && creditcard_cvv.length <= 4){
			$("#credit_card_cvc_error").html("");
			if(creditcard_number == "") {
				$("#credit_card_cvc_error").html("&nbsp;");
			}
		} else {
			$("#credit_card_cvc_error").html("<?php echo __("cvv_length_error"); ?>");
			alert("<?php echo __("cvv_length_error"); ?>");
			error = 0;
		}
	}
	if(error) {
		document.payment_option_form.submit();
	} else {
		return false;
	}
}
</script>
