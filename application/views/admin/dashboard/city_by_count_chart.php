<?php 
$no_data = 0;
if(count($result) > 0) {
	$no_data = 1;
}
?>
<div id="cityByCount" <?php if($no_data) { ?> style="min-width: 310px; height: auto; margin: 0 auto" <?php } ?> ><div class="nodata_found"><?php echo __('no_data'); ?></div></div>
<?php if($no_data) { ?>
<script type="text/javascript" language="javascript">
$(function () {
    $('#cityByCount').highcharts({
		colors: ['#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 65,
                beta: 0
            }
        },
        title: {
            text: ''
        },
        credits: {
			enabled: false
		},
        tooltip: {
			enabled: false,
            pointFormat: '{series.name}: <b>{point.y}</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 20,
                size: 550,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}<br> <b>{point.y}</b>'
                },
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            data: [
            <?php foreach($result as $t) { ?>
                ['<?php echo ucfirst($t["city_name"]); ?>', <?php echo $t["trip_top_cities_count"]; ?>],
            <?php } ?>
            ]
        }]
    });
});
</script>
<?php } ?>
