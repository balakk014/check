<?php 
$no_data = 0;
if($payment_by_company[0]["total_amount"] > 0 || $payment_by_company[0]["cash_payment"] > 0 || $payment_by_company[0]["card_payment"] > 0 ||  $payment_by_company[0]["referral_payment"] > 0 || $payment_by_company[0]["promotional_amount"] > 0) {
	$no_data = 1;
}
if ($company_id > 0) { 
	$total_amount = round($payment_by_company[0]["commision_amount"],2);
	$cash_payment = round($payment_by_company[0]["company_cash_amt"],2);
	$card_payment = round($payment_by_company[0]["company_card_payment"],2);
} else { 
	$total_amount = round($payment_by_company[0]['total_amount'],2);
	$cash_payment = round($payment_by_company[0]["cash_payment"],2);
	$card_payment = round($payment_by_company[0]["card_payment"],2);
}

?>
<div id="payment_by_company" <?php if($no_data) { ?> style="min-width: 310px; height: auto; margin: 0 auto"<?php } ?>><div class="nodata_found"><?php echo __('no_data'); ?></div></div>
<?php if($no_data) { ?>
<script>
$(function () {
	var currency = '<?php echo CURRENCY; ?>';
	$('#payment_by_company').highcharts({
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie'
		},
		credits: {
			enabled: false
		},
		title: {
			text: ''
		},
		tooltip: {
			pointFormat: '<b>'+currency+'{point.y} '+' | {point.x}</b>'
		},
		plotOptions: {
			pie: {
				innerSize: 40,
				allowPointSelect: true,
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: '+currency+'{point.y}'+' | {point.x}',
				},
				showInLegend: true
			}
		},
		series: [{
			data: [
				{ name: '<?php echo __("Trip Payment"); ?>', y: <?php echo $total_amount; ?> ,x: <?php echo $payment_by_company[0]['total_amount_count']; ?>,color:'#0088cc' },
				{ name: '<?php echo __("card_payment"); ?>', y: <?php echo $card_payment; ?> ,x: <?php echo $payment_by_company[0]['card_payment_count']; ?>,color:'#f39c12' },
				{ name: '<?php echo __("cash_payment"); ?>', y: <?php echo $cash_payment; ?> ,x: <?php echo $payment_by_company[0]['cash_payment_count']; ?>,color:'#734ba9' },
				{ name: '<?php echo __("promotional_payment"); ?>', y: <?php echo round($payment_by_company[0]['promotional_amount'],2); ?> ,x: <?php echo $payment_by_company[0]['promotional_amount_count']; ?>,color:'#2baab1' },
				{ name: '<?php echo __("referral_payments"); ?>', y: <?php echo round($payment_by_company[0]['referral_payment'],2); ?> ,x: <?php echo $payment_by_company[0]['referral_payment_count']; ?>,color:'#cc0095' },
			]
		}]
	});
});
</script>
<div class="dashboardpayment">Note : <?php echo __('trip_count_get_additionally_accountable_passenger_paid_both'); ?></div>
<?php } ?>

