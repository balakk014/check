
<?php if($name == ''){ echo "<div class='no_data'>".__('no_data')."</div>"; } else{ ?> 
<div id="transaction">
	<script>
    $(document).ready(function () {
		
			$('#transaction').highcharts({
				
			chart: {
				type: 'column'
			},
			credits: {
				enabled: false
			},
			title: {
				text: 'Trip Meter by Drivers'
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories: [<?php echo $name ?>]
			},
			yAxis: {
				min: 0,
				title: {
				text: 'Number of Trips'
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
				pointPadding: 0.2,
				borderWidth: 0
				}
			},
			series: [{
				name: 'Trips',
				data: [<?php echo $count; ?>]
		
				}]
			});
			
			
    });
	</script>
</div>
<?php } ?>


