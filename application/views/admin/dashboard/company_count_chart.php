<?php 
$no_data = 0;
if($result[0]["total_trip_count"] > 0 || $result[0]["card_payment_count"] > 0 || $result[0]["cash_payment_count"] > 0 ||  $result[0]["promotional_used_count"] > 0 || $result[0]["referral_payment_count"] > 0) {
$no_data = 1;
}
?>
<div id="company_count" <?php if($no_data) { ?> style="min-width: 310px; height: auto; margin: 0 auto"<?php } ?> ><div class="nodata_found"><?php echo __('no_data'); ?></div></div>
<?php if($no_data) { ?>
<script>
$(function () {
	$('#company_count').highcharts({
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
			pointFormat: '<b>{point.y}</b>'
		},
		plotOptions: {
			pie: {
				innerSize: 80,
				allowPointSelect: true,
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.y}',
				},
				showInLegend: true
			}
		},
		series: [{
			name: '',
			data: [
				['<?php echo __('trips'); ?>', <?php echo $result[0]["total_trip_count"]; ?>],
				['<?php echo __('card_payment'); ?>', <?php echo $result[0]["card_payment_count"]; ?>],
				['<?php echo __('cash_payment'); ?>', <?php echo $result[0]["cash_payment_count"]; ?>],
				['<?php echo __('promotional_used'); ?>', <?php echo $result[0]["promotional_used_count"]; ?>],
				['<?php echo __('referral'); ?>', <?php echo $result[0]["referral_payment_count"]; ?>]
			]
		}]
	});
});
</script>
<?php } ?>
