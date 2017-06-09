<?php
	$current_year=date ('Y');
	if(isset($post_values)){
		$sdate=$post_values['startdate'];
		$edate=$post_values['enddate'];
		$for_date=$sdate." to ".$edate;
	}else{
		$for_date=$current_year;
	}
	
	if($get_transaction){
		
		$fare = array();
		$month = array();
		$trips = array();
		foreach($get_transaction as $vl)
		{
			if($vl['fare'] != NULL){
				$trips[] = $vl['trips'];
				$fare[] = $vl['fare'];
				$month[] = "'".$vl['date']." ".$vl['month']."'";
			}
		}
		
		if($trips != NULL){
			$trips = implode(",",$trips);
		}
		if($fare != NULL){
			$fare = implode(",",$fare);
		}
		if($month != NULL){
			$month = implode(",",$month);
		}
		$display ="display:block;";
	}else{
		$fare = array();
		$month = array();
		$trips = array();
		$display ="display:none;";
	}
?>
<?php if($display == 'display:none;'){ echo "<div class='no_data'>".__('no_data')."</div>"; } else{ ?> 
<div id="total_trips_details" style="min-width: 400px; height: 400px; margin: 0 auto<?php echo $display;?>">
	<script>
		$('#total_trips_details').highcharts({
			chart: {
				zoomType: 'xy'
			},
			title: {
					text: 'Total Trip Details '
				},
				subtitle: {
					text: "<?php echo __('for_label') . ' ' . $for_date; ?>",
				},
				xAxis: [{
					categories: [<?php echo $month;?>]
				}],
				yAxis: [{ // Primary yAxis
					labels: {
						format: '{value} Trips',
						style: {
							color: Highcharts.getOptions().colors[2]
						}
					},
					title: {
						text: 'Trip Counts',
						style: {
							color: Highcharts.getOptions().colors[2]
						}
					},
					opposite: true

				}, { // Secondary yAxis
					gridLineWidth: 0,
					title: {
						text: 'Trip Revenues',
						style: {
							color: Highcharts.getOptions().colors[0]
						}
					},
					labels: {
						format: '{value} $',
						style: {
							color: Highcharts.getOptions().colors[0]
						}
					}

				}, ],
				tooltip: {
					shared: true
				},
				legend: {
					layout: 'vertical',
					align: 'left',
					x: 120,
					verticalAlign: 'top',
					y: 80,
					floating: true,
					backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
				},
				series: [{
					name: 'Trip Revenues',
					type: 'column',
					yAxis: 1,
					data : [<?php echo $fare;?>],
					tooltip: {
						valueSuffix: ' $'
					}

				},
				 {
					name: 'Trip Counts',
					type: 'spline',
					data : [<?php echo $trips;?>],
					tooltip: {
						valueSuffix: ' Trips'
					}
				}]
			});
	</script>
</div>
<?php } ?>


