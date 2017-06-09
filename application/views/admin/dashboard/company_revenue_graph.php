<?php 
$no_data = 0;
if(count($grpahdata) > 0) {
	$no_data = 1;
}
?>
<div id="company_revenue_chart" <?php if($no_data) { ?> style="min-width: 310px; height: auto; margin: 0 auto"<?php } ?>> <div class="nodata_found"><?php echo __('no_data'); ?></div></div>
<?php if($no_data) { ?>
<?php
if(count($grpahdata) > 0) {
	foreach($grpahdata as $gdata) {
		$trips[] = $gdata['trips'];
		$fare[] = $gdata['amount'];
		$user_count[] = $gdata['user_count'];
		$month[] = "'".date( "d",strtotime($gdata['createdate']))." ".date( "M",strtotime($gdata['createdate']))." ".date( "Y",strtotime($gdata['createdate']))."'";
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
	if($user_count){
		$user_count = implode(",",$user_count);
	}
?>
<script>
$(function () {
	$('#company_revenue_chart').highcharts({
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
				text: '<?php echo __('trip_count'); ?>',
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
				text: '<?php echo __('trip_revenues'); ?>',
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
			name: '<?php echo __('trip_revenues'); ?>',
			type: 'spline',
			yAxis: 1,
			data : [<?php echo $fare;?>],
			tooltip: {
				valueSuffix: ' <?php echo CURRENCY; ?>',
			}
		},{
			name: '<?php echo __('no_of_trips'); ?>',
			type: 'spline',
			data : [<?php echo $trips;?>],
			color : '#f39c12',
			tooltip: {
				valueSuffix: ' Trips'
			}
		},{
			name: '<?php echo __('no_of_new_users_signed'); ?>',
			type: 'spline',
			data : [<?php echo $user_count;?>],
			color: '#31cc01'
		}]
	});
});
</script>
<?php } } ?>
