<?php 
$no_data = 0;
if(count($result) > 0) {
	$no_data = 1;
}
?>
<div id="trip_request_to_cities" <?php if($no_data) { ?> style="min-width: 310px; height: auto; margin: 0 auto"<?php } ?>><div class="nodata_found"><?php echo __('no_data'); ?></div></div>
<?php if($no_data) { ?>
<script>
$(function () {
    $('#trip_request_to_cities').highcharts({
        chart: {
            type: 'column'
        },
        credits: {
			enabled: false
		},
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
             title: {
                text: 'Cities'
            },
        },
        yAxis: {
            title: {
                text: 'Request Count'
            }

        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: false,
                    format: '{point.y}'
                }
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b>'
        },

        series: [{
            name: 'City',
            colorByPoint: true,
            data: [
            <?php foreach($result as $t) { ?>
            {
                name: '<?php echo ucfirst($t["city_name"]); ?>',
                y: <?php echo $t["trip_top_cities_count"]; ?>
            },
            <?php } ?>
            ]
        }]
    });
});
</script>
<?php } ?>
