<?php 
$no_data = 0;
if($company_name != "" || $taxi_count != "" || $assigned_taxi_count != "" ||  $total_driver_count != "" || $assigned_driver_count != "") {
	$no_data = 1;
}

?>
<div id="assigned_unassigned_graph" <?php if($no_data) { ?> style="min-width: 310px; height: auto; margin: 0 auto"<?php } ?>>
<div class="nodata_found"><?php echo __('no_data'); ?></div></div>
<?php if($no_data) { ?>
<?php if(COMPANY_CID > 0) { ?>
<script>
$(function () {
	$('#assigned_unassigned_graph').highcharts({
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
			pointFormat: '<b>{point.y} '+' | {point.x}</b>'
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
			data: [
				{ name: '<?php echo __('total_taxies'); ?>', y: <?php echo $taxi_count; ?>,color:"#0088cc"},
				{ name: '<?php echo __('assigned_taxies'); ?>', y: <?php echo $assigned_taxi_count; ?>,color:"#f39c12"},
				{ name: '<?php echo __('total_drivers'); ?>', y: <?php echo $total_driver_count; ?>,color:"#734ba9"},
				{ name: '<?php echo __('assigned_drivers'); ?>', y: <?php echo $assigned_driver_count; ?>,color:"#2baab1"},
			]
		}]
	});
});
</script>
<?php } else { ?>
<script>
$(function () {
    $('#assigned_unassigned_graph').highcharts({
        chart: {
            type: 'column'
        },
        credits: {
			enabled: false
		},
        exporting: { 
			enabled: false
		},
        title: {
            text: ''
        },
        xAxis: {
            categories: [<?php echo $company_name; ?>]
        },
        yAxis: [{
            min: 0,
            title: {
                text: 'Count'
            }
        }, {
            title: {
                text: ''
            },
            opposite: true
        }],
        legend: {
            shadow: false
        },
        tooltip: {
            shared: true
        },
        plotOptions: {
            column: {
                grouping: false,
                shadow: false,
                borderWidth: 0
            }
        },
        series: [{
            name: '<?php echo __('total_taxies'); ?>',
            color: 'rgba(165,170,217,1)',
            data: [<?php echo $taxi_count; ?>],
            pointPadding: 0.3,
            pointPlacement: -0.2
        }, {
            name: '<?php echo __('assigned_taxies'); ?>',
            color: 'rgba(126,86,134,.9)',
            data: [<?php echo $assigned_taxi_count; ?>],
            pointPadding: 0.4,
            pointPlacement: -0.2
        }, {
            name: '<?php echo __('total_drivers'); ?>',
            color: 'rgba(248,161,63,1)',
            data: [<?php echo $total_driver_count; ?>],
            pointPadding: 0.3,
            pointPlacement: 0.2,
            yAxis: 1
        }, {
            name: '<?php echo __('assigned_drivers'); ?>',
            color: 'rgba(186,60,61,.9)',
            data: [<?php echo $assigned_driver_count; ?>],
            pointPadding: 0.4,
            pointPlacement: 0.2,
            yAxis: 1
        }]
    });
});
</script>
<?php } ?>
<?php } ?>
