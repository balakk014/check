<?php 
$no_data = 0;
if(count($grpahdata) > 0) {
	$no_data = 1;
}
?>
<div id="driver_revenue_chart" <?php if($no_data) { ?> style="min-width: 300px; height: auto; margin: 0 auto"<?php } ?>><div class="nodata_found"><?php echo __('no_data'); ?></div></div>
<?php 
	if(count($grpahdata) > 0) {
		foreach($grpahdata as $gdata) {
			$trips[] = $gdata['trip_count'];
			$fare[] = $gdata['amount'];
			$month[] = "'".date( "d",strtotime($gdata['current_date']))." ".date( "M",strtotime($gdata['current_date']))." ".date( "Y",strtotime($gdata['current_date']))."'";
		}
		if($trips != NULL){
			$trips = implode(",",$trips);
		}
		if($fare){
			$fare = implode(",",$fare);
		}
		if($month){
			$month = implode(",",$month);
		}
?>
<script>
$(function () {
	$('#driver_revenue_chart').highcharts({
		chart: {
			shortMonths:true,
			zoomType: 'xy'
		},
		title: {
			text: '',
		},
		subtitle: {
			text: "",
		},
		xAxis: [{
			shortMonths:true,
			categories: [<?php echo $month;?>]
		}],
		yAxis: [{
			title: {
				text: 'Trip Count',
				style: {
					color: Highcharts.getOptions().colors[0]
				}
			},
			labels: {
				format: '{value}',
				style: {
					color: Highcharts.getOptions().colors[0]
				}
			},
			opposite: true

		}, {
		gridLineWidth: 0,
			labels: {
				format: '{value} <?php echo CURRENCY; ?>',
				style: {
					color: Highcharts.getOptions().colors[1]
				}
			},
			title: {
				text: 'Amount',
				style: {
					color: Highcharts.getOptions().colors[1]
				}
			},
		}, ],
		tooltip: {
			shared: true
		},
		credits: {
			enabled: false
		},
		legend: {
			x: 0,
			verticalAlign: 'bottom',
			y: 0,
			backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
		},
		series: [{
			name: '<?php echo __('trip_count'); ?>',
			type: 'spline',
			data : [<?php echo $trips;?>],
			color: '#0088cc'
		},{
			name: '<?php echo __('revenue_label'); ?>',
			type: 'spline',
			yAxis: 1,
			data : [<?php echo $fare;?>],
			color: '#ee3324',
			tooltip: {
				valueSuffix: ' <?php echo CURRENCY; ?>'
			}
		}]
	});
});
</script>
<?php } ?>
