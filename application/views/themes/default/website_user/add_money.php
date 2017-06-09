<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/scale.fix.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/jquery.formance.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/awesome_form.js"></script>
<div class="dash_details">
	<?php echo View::factory(USERVIEW.'website_user/left_menu'); ?>
	<section id="right_side_part">
		<div class="top_part">
			<div class="bread_com">
				<ul>
					<li><a href="<?php echo URL_BASE; ?>" title="<?php echo __("home_breadcrumb"); ?>"><?php echo __("home_breadcrumb"); ?></a><i class="fa fa-angle-double-right"></i></li>
					<li><p><?php echo __("wallet"); ?></p></li>
				</ul>
			</div>
			<?php echo View::factory(USERVIEW.'website_user/upcoming_trip_alert'); ?>
		</div>
		<div class="white_bg">
			<div class="wallet_top_det">
				<h1><?php echo CURRENCY.$wallet_amounts; ?></h1>
				<b><?php echo __("wallet_amount"); ?></b>
				<p><?php echo __("wallet_desc"); ?></p>
			</div>
			
                    <div class="wallet_bot_det" style="padding:20px 0 0 0;">
				<div class="recharge_amount_det">
					<h2><?php echo __("recharge_money"); ?></h2>
					<div class="recharge_amounts">
						<ul>
							<li><a class="define_wallet_amount" data-amount="<?php echo WALLET_AMOUNT_1; ?>" href="javascript:;"><?php echo CURRENCY.WALLET_AMOUNT_1; ?></a></li>
							<li><a class="define_wallet_amount" data-amount="<?php echo WALLET_AMOUNT_2; ?>" href="javascript:;"><?php echo CURRENCY.WALLET_AMOUNT_2; ?></a></li>
							<li><a class="define_wallet_amount" data-amount="<?php echo WALLET_AMOUNT_3; ?>" href="javascript:;"><?php echo CURRENCY.WALLET_AMOUNT_3; ?></a></li>
						</ul>
						<div class="enter_amount">
							<input type="text" id="txtboxToFilter" name="wallet_amount" placeholder="<?php echo __('enter_amount_between').' '.CURRENCY.WALLET_AMOUNT_1."-".CURRENCY.WALLET_AMOUNT_3; ?>" onpaste="return false;" maxlength="6" />
							<em id="wallet_amount_error"><?php echo array_key_exists("wallet_amount",$errors)?$errors["wallet_amount"]:"";?></em>
						</div>
					</div>
					<div class="promo_code">
						<p class="have_promocode"><?php echo __("have_a_promocode"); ?></p>
						<div class="promocode_section" style="display:none;">
							<form name="promocode_form" method="post">
								<div class="promo_box">
									<input type="text" name="promocode" placeholder="<?php echo __("promocode"); ?>"/>
									<em id="promocode_error"></em>
								</div>
							</form>
						</div>
						<input type="hidden" id="promocode_validate" value="0"/>
						<input type="button" title="<?php echo __("add_money"); ?>" value="<?php echo __("add_money"); ?>" id="add_money"/>
					</div>
				</div>
				<div class="payment_detail" style="display:none;">
					<div class="pay_tab">
						<ul class="paymentgateway_name">
						<?php /*	<li class="paymentgateway <?php if(DEFAULT_PAYMENT_GATEWAY_ID == 1) { ?>active<?php } ?>" id="paypal"><a class="paypal" href="javascript:;" title="<?php echo __("paypal"); ?>"><?php echo __("paypal"); ?></a></li>
							<li class="paymentgateway <?php if(DEFAULT_PAYMENT_GATEWAY_ID == 2) { ?>active<?php } ?>" id="brain_tree"><a class="brain_tree" href="javascript:;" title="<?php echo __("brain_tree"); ?>"><?php echo __("brain_tree"); ?></a></li> */?>
                                                    <li><a class="credit_crd" href="javascript:;" title="Credit Card">Credit Card</a></li>
						</ul>
					</div>
					<div class="card_details">
						<form id="walletForm" name="payment_details_form" onsubmit="return validate_details();" method="post" autocomplete="off">
							<input type="hidden" name="paymentgateway_type" value="<?php echo DEFAULT_PAYMENT_GATEWAY_ID; ?>"/>
							<input type="hidden" name="user_wallet_amount"/>
							<input type="hidden" name="user_promo_code"/>
							<ul class="paymentgateway_fields">
								<li class="card_no">
									<input type="text" class="credit_card_number" x-autocompletetype="cc-number" placeholder="<?php echo __("credit_card_no"); ?>" name="creditcard_number" onpaste="return false;">
									<em id="credit_card_error"></em>
								</li>
								<li class="cal_month">
									<input type="text" class="credit_card_expiry" x-autocompletetype="cc-exp" placeholder="<?php echo __("mm_yyyy"); ?>" maxlength="9" onpaste="return false;" name="creditcard_expiry_date">
									<em id="credit_card_expiry_error"></em>
								</li>
								<li class="cvv">
									<input type="text" maxlength="4" class="credit_card_cvc" x-autocompletetype="cc-csc" placeholder="<?php echo __("card_verification_no"); ?>" autocomplete="off" onpaste="return false;" name="creditcard_cvv">
									<em id="credit_card_cvc_error"></em>
								</li>
								<li class="name_on_card">
									<input type="text" maxlength="55" class="user_name" name="creditcard_user_name" placeholder="<?php echo __("name_of_the_card"); ?>" onpaste="return false;"/>
									<em id="user_name_error"></em>
								</li>
								<li>
									<input type="submit" title="<?php echo __("btn_submit"); ?>" value="<?php echo __("btn_submit"); ?>" name="btnSubmit" id="payment_submit_form"/>
								</li>
							</ul>
							<div class="card_det_terms">
								<p><input type="checkbox" id="save_card_details" name="save_card_details" value="1"/><?php echo __("save_card_details"); ?></p>
								<p>
									<input type="checkbox" class="terms_condition" name="terms_condition" value="1"/>
									<a target="_blank" href="<?php echo URL_BASE."termsconditions.html"; ?>" title="<?php echo __("terms_and_conditions"); ?>"><?php echo __("terms_and_conditions"); ?></a>
									<em id="terms_condition_error"></em>
								</p>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<script>

$(document).ready( function() {

	$('#txtboxToFilter').keydown(function (event) {  
		// Allow: backspace, delete, tab, escape, and enter
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
			// Allow: Ctrl+A
		(event.keyCode == 65 && event.ctrlKey === true) || 
			// Allow: home, end, left, right
		(event.keyCode >= 35 && event.keyCode <= 39)) {
			// let it happen, don't do anything
			return;
		} else {
			// Ensure that it is a number and stop the keypress
			if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
				event.preventDefault(); 
			}   
		}
	});

	$(".define_wallet_amount").click( function () {
		$("input[name='wallet_amount']").val($(this).data("amount"));
	});

	$(".have_promocode").click(function() {
		$(".promocode_section").fadeToggle(100, function() {
			$(".promocode_section").find('em').text('');
			$(".promocode_section").find('input:text').val('');
			$("input[name='user_promo_code']").val('');
		});
		
		if($("#promocode_validate").val() == 0) {
			$("#promocode_validate").val(1);
		} else {
			$("#promocode_validate").val(0);
		}
	});

	$("#add_money").click(function() {
		validate_wallet_amount();
	});

	$("input[name='promocode']").bind("keyup change", function(e) {
		validate_promocode();
	});
	
	$(".paymentgateway").click(function () {
		$("ul.paymentgateway_name li").removeClass("active");
		$("#"+$(this).attr("id")).addClass("active");
		$("ul.paymentgateway_fields li").find('input:text').val('');
		$("ul.paymentgateway_fields, .card_det_terms").find('em').html('');
		$("form[name='payment_details_form'] input:checkbox").prop('checked', false);
		$("input[name='paymentgateway_type']").val((($(this).attr("id") == "paypal") ? 1 : 2));
	});
});

function validate_wallet_amount()
{
	var min_amount = "<?php echo WALLET_AMOUNT_1; ?>";
	var max_amount = "<?php echo WALLET_AMOUNT_3; ?>";
	var user_wallet_amount = $("input[name='wallet_amount']").val().trim();
	if(user_wallet_amount == "") {
		$("#wallet_amount_error").html("<?php echo  __('required'); ?>");
		$(".payment_detail").hide();
		return false;
	} else if(user_wallet_amount != "" && ((parseInt(user_wallet_amount) < parseInt(min_amount)) || (parseInt(user_wallet_amount) > parseInt(max_amount)))) {
		$("#wallet_amount_error").html("<?php echo __("enter_amount_between"); ?> <?php echo CURRENCY; ?>"+min_amount+"-<?php echo CURRENCY; ?>"+max_amount);
		error = 0;
	} else {
		$("#wallet_amount_error").html("");
		$(".payment_detail").show();
	}
	var promocode_box_validate = $("#promocode_validate").val();
	var promocode = $("input[name='promocode']").val().trim();

	if(promocode_box_validate == 1 && promocode == "") {
		$("#promocode_error").html("<?php echo __('required'); ?>");
	} else if(promocode_box_validate == 2 && promocode != "") {
		$("#promocode_error").html("<?php echo __('invalid_promocode_wallet'); ?>");
	} else if(promocode_box_validate == 3 && promocode != "") {
		$("#promocode_error").html("<?php echo __('promo_code_limit_exceed'); ?>");
	} else if(promocode_box_validate == 4 && promocode != "") {
		$("#promocode_error").html("<?php echo __('promo_code_startdate'); ?>");
	} else if(promocode_box_validate == 5 && promocode != "") {
		$("#promocode_error").html("<?php echo __('promo_code_expired'); ?>");
	} else {
		$("#promocode_error").html('');
		validate_promocode();
	}
}

function validate_promocode()
{
	var promocode = $("input[name='promocode']").val().trim();
	$("#promocode_error").html("");
	var promocode_box_validate = $("#promocode_validate").val();
	if(promocode_box_validate > 0) {
		$("#promocode_validate").val(promocode_box_validate);
	}
	if(promocode != "") {
		$.ajax({
			url:'<?php echo URL_BASE;?>users/validate_promocode',
			type:'post',
			dataType:'json',
			data:$("form[name='promocode_form']").serialize(),
			beforeSend:function() {
				$("#promocode_error").html("<?php echo __("please_wait_promocode_validating"); ?>");
				$('#add_money, #payment_submit_form').attr('disabled', 'disabled');
			},
			success:function(data)
			{
				var res = "";
				$("input[name='user_promo_code']").val("");
				if(data == 0) {
					res = "<?php echo  __('invalid_promocode_wallet'); ?>";
					$("#promocode_validate").val(2);
				} else if(data == 2) {
					res = "<?php echo  __('promo_code_limit_exceed'); ?>";
					$("#promocode_validate").val(3);
				} else if(data == 3) {
					res = "<?php echo  __('promo_code_startdate'); ?>";
					$("#promocode_validate").val(4);
				} else if(data == 4) {
					res = "<?php echo  __('promo_code_expired'); ?>";
					$("#promocode_validate").val(5);
				} else {
					res = "<?php echo  __('promocode_valid'); ?>";
					$("input[name='user_promo_code']").val(promocode);
					$("#promocode_validate").val(6);
				}
				$("#promocode_error").html(res);
				$('#add_money, #payment_submit_form').removeAttr("disabled");
			}
		});
	}
}

function validate_details()
{
	var error = 1;
	var min_amount = "<?php echo WALLET_AMOUNT_1; ?>";
	var max_amount = "<?php echo WALLET_AMOUNT_3; ?>";
	var user_wallet_amount = $("input[name='wallet_amount']").val().trim();
	var promocode_box_validate = $("#promocode_validate").val();
	var promocode = $("input[name='promocode']").val().trim();
	var creditcard_number = document.payment_details_form.creditcard_number.value.trim();
	var creditcard_expiry_date = document.payment_details_form.creditcard_expiry_date.value.trim();
	var creditcard_cvv = document.payment_details_form.creditcard_cvv.value.trim();
	var creditcard_user_name = document.payment_details_form.creditcard_user_name.value.trim();

	if(user_wallet_amount == "") {
		$("#wallet_amount_error").html("<?php echo  __('required'); ?>");
		error = 0;
	} else if(user_wallet_amount != "" && ((parseInt(user_wallet_amount) < parseInt(min_amount)) || (parseInt(user_wallet_amount) > parseInt(max_amount)))) {
		$("#wallet_amount_error").html("<?php echo __("enter_amount_between"); ?> "+min_amount+"-"+max_amount);
		error = 0;
	} else {
		$("#wallet_amount_error").html("");
	}

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
		$("#credit_card_cvc_error").html("<?php echo  __('required'); ?>");
		error = 0;
	} else {
		if(creditcard_cvv.length >= 3 && creditcard_cvv.length <= 4){
			$("#credit_card_cvc_error").html("");
			if(creditcard_number == "") {
				$("#credit_card_cvc_error").html("&nbsp;");
			}
		} else {
			$("#credit_card_cvc_error").html("<?php echo __("cvv_length_error"); ?>");
			error = 0;
		}
	}

	if(creditcard_user_name == "") {
		$("#user_name_error").html("<?php echo  __('required'); ?>");
		error = 0;
	} else {
		$("#user_name_error").html("");
	}

	if($(".terms_condition").is(':checked')) {
		$("#terms_condition_error").html("");
	} else {
		$("#terms_condition_error").html("<?php echo  __('required'); ?>");
		error = 0;
	}
	
	if(promocode_box_validate == 1 && promocode == '') {
		$("#promocode_error").html('<?php echo __('required'); ?>');
		error = 0;
	} else if(promocode_box_validate == 2 && promocode != "") {
		$("#promocode_error").html("<?php echo __('invalid_promocode_wallet'); ?>");
		error = 0;
	} else if(promocode_box_validate == 3 && promocode != "") {
		$("#promocode_error").html("<?php echo __('promo_code_limit_exceed'); ?>");
		error = 0;
	} else if(promocode_box_validate == 4 && promocode != "") {
		$("#promocode_error").html("<?php echo __('promo_code_startdate'); ?>");
		error = 0;
	} else if(promocode_box_validate == 5 && promocode != "") {
		$("#promocode_error").html("<?php echo __('promo_code_expired'); ?>");
		error = 0;
	} else if(promocode_box_validate == 6 && promocode != "") {
		$("#promocode_error").html("<?php echo __('promocode_valid'); ?>");
	} else {
		$("#promocode_error").html('');
		validate_promocode();
	}

	$("input[name='user_wallet_amount']").val($("input[name='wallet_amount']").val());
	if(document.getElementById('save_card_details').checked && error) {
		check_wallet_form();
	} else if(error) {
		$('#payment_submit_form').attr('disabled', 'disabled');
		document.payment_details_form.submit();
	}
	return false;
}

function check_wallet_form()
{
	var creditcard_number = document.payment_details_form.creditcard_number.value.trim();
	$.ajax({
		url:'<?php echo URL_BASE;?>users/check_creditcard_exist',
		type:'post',
		dataType:'json',
		data:{creditcard_number : creditcard_number},
		beforeSend:function() {
			$('#payment_submit_form').val("<?php echo __("please_wait"); ?>").attr('disabled', 'disabled');
		},
		success:function(data)
		{
			if(data == 1) {
				$("#credit_card_error").html("<?php echo __("card_exist"); ?>");
				$("#credit_card_cvc_error").html("&nbsp;");
				$('#payment_submit_form').val("<?php echo __("Submit"); ?>").removeAttr("disabled");
				return false;
			} else {
				document.payment_details_form.submit();
			}
		}
	});
}
</script>
