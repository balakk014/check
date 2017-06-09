<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<link rel="stylesheet" href="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/validation/jquery.validate.js"></script>

<?php
$startdate = date('Y-m-d 00:00:00');
$enddate = date('Y-m-d H:i:s');
$not_setting = "ON";
?>
<?php if (SHOW_MAP != 1) { ?>
    <script>	
        // Enable the visual refresh
        //google.maps.visualRefresh = true;

        var map;

        function showPosition(lat,lng)
        {
    	var latlng = new google.maps.LatLng(lat,lng);
    	var mapOptions = {
    	    zoom: 15,
    	    center: latlng,
    	    mapTypeId: google.maps.MapTypeId.ROADMAP
    	};
    	map = new google.maps.Map(document.getElementById('map-canvas'),
    	mapOptions);
          
    	var iconBase = '<?php echo PUBLIC_IMGPATH . '/'; ?>';
    	var latlng=new google.maps.LatLng(lat,lng);
    	var marker = new google.maps.Marker({
                position: latlng,
                map: map,
               
                animation: google.maps.Animation.DROP,
                icon: iconBase + 'car.png'
                
    	});  
        
    	geocoder = new google.maps.Geocoder();
    	geocoder.geocode({'latLng': latlng}, function(results, status) {
    	    if (status == google.maps.GeocoderStatus.OK) {
    		if (results[1]) {         
    		    $('#on_going_place').html("My Current Location : "+results[1].formatted_address);
    		}
    	    }			
    	    else if (status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
    		setTimeout(function() {
    		    codeLatLng(lat,lng,id);
    		}, 200); 
    	    }
    	    else {
    		alert('Geocoder failed due to: ' + status);
    		attempts = 0;
    	    }
    	});
       
       
            
        }

    </script>
<?php } ?>
<script type="text/javascript">
    //initialize();
    setInterval(function() 
    {
	dashboard_map();
			   
    }, 60000);
</script>
<div class="main_dashboard_page">
        <div class="dashboard_widget_row">	
	    <div class="dashboard_page_top_list actions" style="padding-bottom: 0">
		<div id="quick-actions">
			<ul>
				<li class="color_code6">
					<div class="dash_active_left">
						<img src="<?php echo IMGPATH ?>dashboard_icons/6.png" class="image" alt="<?php echo __('payment_label'); ?>"  />
					</div>
					<div class="dashboard_detail_right">
						<h2><?php echo __('drivers'); ?></h2>
						<p><?php echo $admin_dashboard_data["driver"]; ?></p>
					</div>
				</li>
				<li class="color_code7">
					<div class="dash_active_left">
						<img src="<?php echo IMGPATH ?>dashboard_icons/7.png" class="image" alt="<?php echo __('commission'); ?>"  />
					</div>
					<div class="dashboard_detail_right">
						<h2><?php echo __('taxis'); ?></h2>
						<p><?php echo $admin_dashboard_data["taxi"]; ?></p>
					</div>
				</li>
				<li class="color_code8">
					<div class="dash_active_left">
						<img src="<?php echo IMGPATH ?>dashboard_icons/8.png"  alt="<?php echo __('payment_label'); ?>"  />
					</div>
					<div class="dashboard_detail_right">
						<h2> <?php echo __('unassigned_taxies'); ?></h2>
						<p><?php if (count($freetaxi_list) > 0) {
							echo $free_taxi = count($freetaxi_list);
						} else {
							echo $free_taxi = 0;
						} ?></p>
					</div>
				</li>
			</ul>
		</div>
		<div id="map" class="manager_status">
			<ul>
				
				<li class="color_code7">
					<div class="dash_active_left">
						<img src="<?php echo IMGPATH ?>dashboard_icons/7.png" class="image" alt="<?php echo __('commission'); ?>"  />
					</div>
					<div class="dashboard_detail_right">	   
						<h2><?php echo __('free_taxies_today'); ?></h2>
						<p><?php if (count($availabletaxi_list) > 0) {
								echo count($availabletaxi_list);
							} else {
								echo '0';
						} ?></p>
					</div>
				</li>
			<li class="color_code3">
			    <div class="dash_active_left">
				    <img src="<?php echo IMGPATH ?>dashboard_icons/w7.png" class="image" alt="<?php echo __('commission'); ?>"  />
			    </div>
			    <div class="dashboard_detail_right">	
				<h2><?php echo __('unass_drivers_today'); ?></h2>
				<p><?php
				    if (count($freedriver_list) > 0) {
					echo $freedriver = count($freedriver_list);
				    } else {
					echo $freedriver = 0;
				    }
?></p>
			    </div>
			</li>
			<li class="color_code4">
			    <div class="dash_active_left">
				    <img src="<?php echo IMGPATH ?>dashboard_icons/2.png" class="image" alt="<?php echo __('commission'); ?>"  />
			    </div>
			    <div class="dashboard_detail_right">	
				<h2><?php echo __('live_passengers'); ?></h2>
				<p><?php echo $admin_dashboard_data["general_users"]; ?></p>
			    </div>
			</li>
		    </ul>
		</div>
		
		<?php /* <ul class=" manager_action_tabs">
		<li><a href="#quick-actions" title=""><?php echo __('Statistics'); ?></a></li>
		<!--<li><a href="#user-stats" title=""><?php // echo __('quick_access'); ?></a></li>-->
		<li><a href="#map" title="" id="map-link"><?php echo __('reports'); ?></a></li>
	    </ul> */ ?>
	    </div>
	</div>
    <!-- action tabs -->
    <div class="actions-wrapper">
	<div class="">

	    <!--<div id="user-stats">

		  <ul class="round-buttons">

		       <li><div class="depth"><a href="<?php echo URL_BASE; ?>transaction/admintransaction/all/" title="<?php echo __('transaction_details'); ?>" class="tip"><i class="icon-money"></i></a></div>
		       <span class="rapid_title"><?php echo __('transaction'); ?></span>
		      </li>
		       <li><div class="depth"><a href="<?php echo URL_BASE; ?>add/company" title="<?php echo __('taxicompany'); ?>" class="tip"><i class="icon-trello"></i></a></div>
			   <span class="rapid_title"><?php echo __('company'); ?></span>
			   </li>
		       <li><div class="depth"><a href="<?php echo URL_BASE; ?>add/admin" title="<?php echo __('superadmin_management'); ?>" class="tip"><i class="icon-user"></i></a></div>

		       <span class="rapid_title"><?php echo __('super_admin'); ?></span></li>

		       <li><div class="depth"><a href="<?php echo URL_BASE; ?>admin/manage_site" title="<?php echo __('settings'); ?>" class="tip"><i class="icon-wrench"></i></a></div>
			       <span class="rapid_title"><?php echo __('settings'); ?></span>
</li>
		   </ul>
	   </div>-->

	    

	    

	    
	</div>
    </div>
    <!-- /action tabs -->
    <div class="dashboard_widget_row">
	<div class="dashborad_widget_box lg-12">
	    <form  action="<?php echo URL_BASE; ?>manager/dashboard" method="post" name="dashboard" id="dashboard" >  
		<div class="dashboard_widget_title with_calender">
		    <h2><?php echo __('Current Status of Driver'); ?></h2>
		   <?php /* <div class="dashboard_calender_in_header with_select_box">
			<ul>
			    <li>
				<div class="new_dash_calender_select">
				    <select name="select_driver_status" id="select_driver_status">
					<option value=""><?php echo __('all_label'); ?></option>
					<option value="A">Active</option>
					<option value="F">Free</option>
					<option value="B">Busy</option>
					<option value="OUT">Free and Shiftout</option>
				    </select>
				</div>
			    </li>
			    <li>
				<input type="button" name="search_driver_status" id="search_driver_status" onclick="dashboard_map()" value="GO" title="Go" >
			    </li>
			</ul>
		    </div> */ ?>
		</div>
		<div class="dashboard_map_outer" id="on_going_trip_map"  style="padding: 3px;">					
		    <div id="on_going_trip"></div>
		    <div id="on_going_place"></div>
<?php if (SHOW_MAP != 1) { ?>
	<div class="live_trip_right_box">
				<ul>
					<li><span class="map_available">Available</span></li>
					<li><span class="map_ontrip">On Trip</span></li>
					<li><span class="map_inactive">In Active</span></li>
					 <li><span class="map_shiftout">Shift out</span></li>
				</ul>
			</div>
    		    <div id="map-canvas" style="width:100%;height:500px;"></div>
<?php } ?>
		</div>


	    </form>
	</div>
    </div>


    <!-- Company Total Trip Details Start-->
    <div class="dashboard_widget_row">
	<div class="dashborad_widget_box lg-12">
	    <div class="dashboard_widget_title with_calender">
		<h2><?php echo __('total_trip_details'); ?></h2>
		<div class="dashboard_calender_in_header with_select_box">
		    <ul>
			<li>
			    <div class="new_dash_calender_input">
				<input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="company_trips_startdate" name="company_trips_startdate" value="<?php echo $startdate; ?>"  />
				<span id="company_trips_startdate_error" class="errors" style="display:none;"></span>
			    </div>
			</li>
			<li>
			    <div class="new_dash_calender_input">
				<input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="company_trips_enddate" name="company_trips_enddate" value="<?php echo convert_timezone('now',$_SESSION['timezone']); ?>"  />
				<span id="company_trips_enddate_error" class="errors" style="display:none;"></span>
			    </div>
			</li>
			<li>
			    <input type="button" name="company_search_total_trips" id="company_search_total_trips" value="GO" title="Go" >
			</li>
		    </ul>
		</div>
		<div class="dashboard_map_outer" id="company_total_trips_details">
		    <div id="company_trips_details">
		    </div>
		</div>
	    </div>
	</div>
    </div>

    <!-- Company Total Trip Details End-->
    <div class="dashboard_widget_row">
	<div class="dashborad_widget_box lg-12">
	    <div class="dashboard_widget_title with_calender">
		<h2><?php echo __('transaction'); ?></h2>
			<div class="dashboard_calender_in_header with_select_box">
				<ul>
				<li>
					<div class="new_dash_calender_input">
					<input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="userstartdate" name="userstartdate" value="<?php echo $startdate; ?>"  />
					<span id="startdate_error" class="errors" style="display:none;"></span>
					</div>
				</li>
				<li>
					<div class="new_dash_calender_input">
					<input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="userenddate" name="userenddate" value="<?php echo convert_timezone('now',$_SESSION['timezone']); ?>"  />
					<span id="enddate_error" class="errors" style="display:none;"></span>
					</div>
				</li>
				<li><input type="button" name="change_usercompany" id="change_usercompany" value="GO" title="Go" ></li>
				</ul>
			</div>
			<div class="dashboard_map_outer" id="tripcount">
					<?php if ($count != '') { ?> 
							<div class="chart" id="transaction" ></div>
					<?php
					} else {
						echo "<div class='no_data'>No Data Found!</div>";
					}
					?>
			</div>
	    </div>
	</div>
    </div>
	<input type="hidden" id="drivers" value="<?php echo $drivers ?>">

    <!-- Available Taxies widget -->
    <div class="dashboard_widget_row">
	<div class="dashborad_widget_box lg-12">
	    <div class="dashboard_widget_title with_calender">
		<h2><?php echo __('free_taxi'); ?></h2>
	    </div>
	    <div class="dashboard_map_outer withdraw_table">
                <div class="content_middle">
		<table cellpadding="0" cellspacing="0" width="100%" class="sTable">
		    <thead>
			<tr>
			    <td align="center" width="80"><?php echo __('taxi_no'); ?></td>
			    <td align="center" width="80"><?php echo __('driver_name'); ?></td>
			    <td align="center" width="80"><?php echo __('driver_phone_number'); ?></td>
			</tr>
		    </thead>
		    <tbody>
<?php if (isset($availabletaxi_list) && count($availabletaxi_list) > 0) { ?>
    <?php foreach ($availabletaxi_list as $availabletaxilist) { ?>
				<tr>
				    <td align="center"><a href="<?php echo URL_BASE; ?>manage/taxiinfo/<?php echo $availabletaxilist['taxi_id']; ?>" title="" ><?php echo $availabletaxilist['taxi_no']; ?></a></td>
				    
				    <td align="center"><a href="<?php echo URL_BASE; ?>manage/driverinfo/<?php echo $availabletaxilist['driver_id']; ?>" title="" ><?php echo $availabletaxilist['name']; ?></a></td>
				    <td align="center"><?php echo $availabletaxilist['phone']; ?></td>
				</tr>
					<?php
					}
				    } else {
					?>
    			<tr><td colspan="4" align="center"> <?php echo __('no_data'); ?></td> </tr>
				<?php } ?>

		    </tbody>
		</table> 
                </div>
	    </div>
	</div>
    </div>

    <!-- Free Taxies widget -->
	<?php /* <div class="dashboard_widget_row">
		<div class="dashborad_widget_box lg-12">
			<div class="dashboard_widget_title with_calender">
			<h2><?php echo __('free_taxies'); ?></h2>
			</div>
			<div class="dashboard_map_outer withdraw_table">
			<table cellpadding="0" cellspacing="0" width="100%" class="sTable">
				<thead>
				<tr>
					<td width="80"><?php echo __('taxi_no'); ?></td>
					<td width="80"><?php echo __('company_name'); ?></td>
				</tr>
				</thead>
				<tbody>
					<?php if (isset($freetaxi_list) && count($freetaxi_list) > 0) { ?>
					<?php foreach ($freetaxi_list as $freetaxilist) { ?>
					<tr>
						<td align="center"><a href="<?php echo URL_BASE; ?>manage/taxiinfo/<?php echo $freetaxilist['taxi_id']; ?>" title="" ><?php echo $freetaxilist['taxi_no']; ?></a></td>
						<td align="center"><a href="<?php echo URL_BASE; ?>manage/companydetails/<?php echo $freetaxilist['userid']; ?>" title
						<?php echo $freetaxilist['company_name']; ?></a></td>
					</tr>
					<?php } } else { ?>
					<tr><td colspan="4" align="center"> <?php echo __('no_data'); ?></td> </tr>
					<?php } ?>
				</tbody>
			</table> 
			</div>
		</div>
    </div> */ ?>


    <!-- Free Drivers widget -->
  <?php /* <div class="dashboard_widget_row">
		<div class="dashborad_widget_box lg-12">
			<div class="dashboard_widget_title with_calender">
			<h2><?php echo __('free_drivers'); ?></h2>
			</div>
			<div class="dashboard_map_outer withdraw_table">
			<table cellpadding="0" cellspacing="0" width="100%" class="sTable">
				<thead>
				<tr>
					<td width="80"><?php echo __('driver_name'); ?></td>
					<td width="80"><?php echo __('company_name'); ?></td>
				</tr>
				</thead>
				<tbody>
					<?php if (isset($freedriver_list) && count($freedriver_list) > 0) { ?>
					<?php foreach ($freedriver_list as $freedriverlist) { ?>
					<tr>
						<td align="center"><a href="<?php echo URL_BASE; ?>manage/driverinfo/<?php echo $freedriverlist['id']; ?>" title="" ><?php echo $freedriverlist['name']; ?></a></td>
						<td align="center"><a href="<?php echo URL_BASE; ?>manage/companydetails/<?php echo $freedriverlist['userid']; ?>" title="" >
						<?php echo $freedriverlist['company_name']; ?></a></td>
					</tr>
					<?php } } else { ?>
					<tr><td colspan="4" align="center"> <?php echo __('no_data'); ?></td> </tr>
					<?php } ?>
				</tbody>
			</table>   
			<input type="hidden" name="trans_name" id="trans_name" value="<?php echo $name; ?>" />
			<input type="hidden" name="trans_count" id="trans_count" value="<?php echo $count; ?>" />
			</div>
		</div>
    </div> */ ?>
    <input type="hidden" name="trans_name" id="trans_name" value="<?php echo $name; ?>" />
	<input type="hidden" name="trans_count" id="trans_count" value="<?php echo $count; ?>" />
</div>
<script type="text/javascript">
    $(document).ready(function () {
	var name = $('#trans_name').val();
	var count = $('#trans_count').val();

	if(count != '' )
	{ 
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
		
	}		
    });
	
    $(document).ready(function () {

	$("#userstartdate").datetimepicker( {
	    showTimepicker:DEFAULT_TIME_SHOW,
	    showSecond: true,
	    timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	    dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	    stepHour: 1,
	    stepMinute: 1,
	    maxDateTime : new Date(),
	    stepSecond: 1
	} );

	$("#userenddate").datetimepicker( {
	    showTimepicker:DEFAULT_TIME_SHOW,
	    showSecond: true,
	    timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	    dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	    stepHour: 1,
	    stepMinute: 1,
	    maxDateTime : new Date(), 
	    stepSecond: 1
	} );


    });

    $("#change_usercompany").click(function(){

		var startdate = $("#userstartdate").val();
		var enddate = $("#userenddate").val();
		if(startdate =='')
		{
			$("#startdate_error").html("<?php echo __('select_startdate'); ?>");
			$("#startdate_error").show();
		}
		else
		{
			$("#startdate_error").html("");
			$("#startdate_error").hide();
		}
		if(enddate =='')
		{
			$("#enddate_error").html("<?php echo __('select_enddate'); ?>");
			$("#enddate_error").show();
		}
		else
		{
			$("#enddate_error").hide("");
			$("#enddate_error").hide();
		}
		if(startdate !='' && enddate!='')
		{
			if(to_timestamp(startdate) > to_timestamp(enddate)) 
			{
				$("#startdate_error").html("<?php echo __('startdate_greater'); ?>");
				$("#startdate_error").show();
			}
			else
			{
				$("#startdate_error").html("");
				$("#startdate_error").hide();
				//document.forms['dashboard'].submit();
				$('#tripcount').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
				var drivers = $("#drivers").val();
				var dataS = "startdate="+startdate+"&enddate="+enddate+"&drivers="+drivers;
				$.ajax({ 			
					type: "POST",
					url: SrcPath+"manager/driver_trip_count", 
					data: dataS, 
					cache: false, 
					dataType: 'html',
					success: function(response) 
					{ 	
						$('#tripcount').html(response);			
					} 
				});	
			}
		}	 
    });	
</script>
<script src="<?php echo SCRIPTPATH; ?>highcharts.js"></script>
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY0kkJiTPVd2U7aTOAwhc9ySH6oHxOIYM&amp;sensor=false"></script> -->
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/plugins/jquery.easytabs.min.js"></script>

<script type="text/javascript">
    //===== Hide/show action tabs =====//

    $('.showmenu').click(function () {
	$('.actions-wrapper').slideToggle(100);
    });
	
    //===== Easy tabs =====//

    $('.actions').easytabs({
	animationSpeed: 300,
	collapsible: false,
	tabActiveClass: "current"
    });
</script>

<script>
	$(document).ready(function(){		
		$("#company_search_total_trips").trigger('click');
	});
	
    $("#company_trips_startdate").datetimepicker( {
	showTimepicker:DEFAULT_TIME_SHOW,
	showSecond: true,
	timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(),
	stepSecond: 1
    } );

    $("#company_trips_enddate").datetimepicker( {
	showTimepicker:DEFAULT_TIME_SHOW,
	showSecond: true,
	timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(), 
	stepSecond: 1
    } );

    $(function () {
	/*chart2 = new Highcharts.Chart({
	    chart: {
		renderTo: 'company_trips_details',
		type: 'line',
		marginRight: 130,
		marginBottom: 25,
		events: {
		    load: requestData_trip_counts
		},
	    },
	    title: {
		text: '<?php echo __('total_trip_details'); ?>'
	    },
	    subtitle: {
		text: "<?php echo __('for_label') . ' ' . date('Y'); ?>",
	    },
		credits: {
			enabled: false
		},
	    xAxis: [{
		    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
			'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
		}],
	    yAxis: [{ // Primary yAxis
		    labels: {
			format: '{value} <?php echo __('Trips'); ?>',
			style: {
			    color: Highcharts.getOptions().colors[2]
			}
		    },
		    title: {
			text: '<?php echo __('trip_counts'); ?>',
			style: {
			    color: Highcharts.getOptions().colors[2]
			}
		    },
		    opposite: true

		}, { // Secondary yAxis
		    gridLineWidth: 0,
		    title: {
			text: '<?php echo __('trip_revenues'); ?>',
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
		    name: '<?php echo __('trip_revenues'); ?>',
		    type: 'column',
		    yAxis: 1,
		    data : [],
		    tooltip: {
			valueSuffix: ' $'
		    }

		},
		{
		    name: '<?php echo __('trip_counts'); ?>',
		    type: 'spline',
		    data : [],
		    tooltip: {
			valueSuffix: ' Trips'
		    }
		}]
	});

	function requestData_trip_counts() {
	    $.ajax({
		url: '<?php echo URL_BASE; ?>manager/get_company_trip_count',
		type: 'post',
		dataType: 'json',		
		beforeSend: function() {			
		},
		complete: function() {			
		},				
		success: function(json) {
		    if (json['error']) {
			alert(json['error']);
		    }
				
		    if (json['success']) {				
			var series = chart2.series[0],
			shift = series.data.length > 30; // shift if the series is longer than 300 (drop oldest point)
			$.each( json['success']['trips'], function( key, value ) {
			    chart2.series[0].addPoint(eval(value['revenues']), true, shift);
			    chart2.series[1].addPoint(eval(value['trips']), true, shift);
			});
		    }
		}
	    });
	}*/
});

    /*** Total Trips and Toal Revenue **********/
    $("#company_search_total_trips").click(function(){

		var startdate = $("#company_trips_startdate").val();
		var enddate = $("#company_trips_enddate").val();

		if(startdate =='')
		{
			$("#company_trips_startdate_error").html("<?php echo __('select_startdate'); ?>");
			$("#company_trips_startdate_error").show();
		}
		else
		{
			$("#company_trips_startdate_error").html("");
			$("#company_trips_startdate_error").hide();
		}
		if(enddate =='')
		{
			$("#company_trips_enddate_error").html("<?php echo __('select_enddate'); ?>");
			$("#company_trips_enddate_error").show();
		}
		else
		{
			$("#company_trips_enddate_error").hide("");
			$("#company_trips_enddate_error").hide();
		}
		if(startdate !='' && enddate!='')
		{
			$('#company_total_trips_details').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
			if(to_timestamp(startdate) > to_timestamp(enddate)) 
			{
			$("#company_trips_startdate_error").html("<?php echo __('startdate_greater'); ?>");
			$("#company_trips_startdate_error").show();
			}
			else
			{
			$("#company_trips_startdate_error").html("");
			$("#company_trips_startdate_error").hide();
			var dataS = "startdate="+startdate+"&enddate="+enddate;
			$.ajax
			({ 			
				type: "POST",
				url: SrcPath+"manager/total_trip_details_search", 
				data: dataS, 
				cache: false, 
				dataType: 'html',
				success: function(response) 
				{ 	
					$('#company_total_trips_details').html(response);			
				} 
			});	
			}
		}
    });
 
</script>




<script>
    jQuery(function($) {
	// Asynchronously Load the map API 
	var script = document.createElement('script');  
	script.src = "https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initialize";
	document.body.appendChild(script);
    });

    function initialize() 
    { 
	var map;
	var bounds = new google.maps.LatLngBounds();
	var mapOptions = {
	    mapTypeId: 'roadmap'
	};
	var markers=[];
<?php
$a = 0;
$b = 6;
if (count($all_company_map_list) > 0) {
	foreach ($all_company_map_list as $v) {
		for ($b = 0; $b < 6; $b++) {
			if ($b == 0) {
			?>
				markers [<?php echo $a; ?>] = new Array(5);
				markers[<?php echo $a; ?>][<?php echo $b; ?>]=<?php echo $v['latitude']; ?>;
			<?php } if ($b == 1) { ?>
				markers[<?php echo $a; ?>][<?php echo $b; ?>]=<?php echo $v['longitude']; ?>;
			<?php } if ($b == 2) { ?>
				markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo '<div class="marker-info-win"><div class="marker-inner-win">' ?>';
			<?php } if ($b == 3) {
				$driver_status = ($v['driver_status'] == 'F' && $v['shift_status'] == 'IN') ? "<span>" . __('available') . "</span>" : (($v['driver_status'] == 'A') ? "<span>" . 'In Active' . "</span>" : (($v['driver_status'] == 'B') ? "<span>" . 'New Trip Assigned' . "</span>" : 'In Active'));
				$txtcolor = '';
			?>
				markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo '<div id="bodyContent"><p>' . $driver_status . '</p><span class="info-content">'.$v['name'].'</span></div></div></div>'; ?>';
			<?php } if ($b == 4) {
				if ($v['driver_status'] == 'F' && $v['shift_status'] == 'OUT') { ?>
					markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo URL_BASE.'public/admin/images/dashboard_icons/map_shiftout_icon.png'; ?>';
			<?php } elseif ($v['driver_status'] == 'A') { ?>
					markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo URL_BASE.'public/admin/images/dashboard_icons/map_incactive_icon.png'; ?>';
			<?php } else if ($v['driver_status'] == 'B') { ?>
					markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo URL_BASE.'public/admin/images/dashboard_icons/map_waiting_icon.png'; ?>';
			<?php } else if ($v['driver_status'] == 'F' && $v['shift_status'] == 'IN') { ?>
					markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo URL_BASE.'public/admin/images/dashboard_icons/map_available_icon.png'; ?>';
			<?php } } if($b==5) { ?>
			markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo $v['driver_status_class']; ?>';
		<?php } } $a++; } } ?>   
		    
			// Display a map on the page
			map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
			map.setTilt(45);
			if(markers != "")
			{
			    for( i = 0; i < markers.length; i++ ) { 
				// Display multiple markers on a map
				var infoWindow = new google.maps.InfoWindow(), marker, i;
				var iconBase = '<?php echo PUBLIC_IMGPATH . '/'; ?>';
				   
				// Loop through our array of markers & place each one on the map  
		   
				var position = new google.maps.LatLng(markers[i][0], markers[i][1]);
				bounds.extend(position);
				marker = new google.maps.Marker({
				    position: position,
				    map: map,
				    animation: google.maps.Animation.DROP,
				    icon: markers[i][4],
				});
				google.maps.event.addListener(infoWindow, 'domready', function() {

						// Reference to the DIV which receives the contents of the infowindow using jQuery
						var iwOuter = $('.gm-style-iw');

						/* The DIV we want to change is above the .gm-style-iw DIV.
						* So, we use jQuery and create a iwBackground variable,
						* and took advantage of the existing reference to .gm-style-iw for the previous DIV with .prev().
						*/
						var iwBackground = iwOuter.prev();

						// Remove the background shadow DIV
						iwBackground.children(':nth-child(2)').css({'display' : 'none'});

						// Remove the white background DIV
						iwBackground.children(':nth-child(4)').css({'display' : 'none'});
						
						 // Apply the desired effect to the close button
						 
						// iwCloseBtn.css({opacity: '1', right: '38px', top: '3px', border: '7px solid #48b5e9', 'border-radius': '13px', 'box-shadow': '0 0 5px #3990B9'});
						 
						// Moves the shadow of the arrow 76px to the left margin.
						iwBackground.children(':nth-child(1)').attr('style', function(i,s){ return s + 'left: 76px !important;'});

						// Moves the arrow 76px to the left margin.
						iwBackground.children(':nth-child(3)').attr('style', function(i,s){ return s + 'left: 76px !important;'});

						// Changes the desired tail shadow color.
						iwBackground.children(':nth-child(3)').find('div').children().css({'box-shadow': 'rgba(72, 181, 233, 0.6) 0px 1px 6px', 'z-index' : '1'});

						if($('.iw-content').height() < 140){
							$('.iw-bottom-gradient').css({display: 'none'});
						}
					});
				// Allow each marker  to have an info window
				google.maps.event.addListener(marker, 'click', (function(marker, i) {
				    return function() {
					infoWindow.setContent(markers[i][2]+markers[i][3]);
					infoWindow.open(map, marker);
					$('.gm-style-iw').parent().removeClass('').addClass(markers[i][5]);
					$('.gm-style-iw').next('div').removeClass('').addClass('driver_info_close');
					$('.gm-style-iw').prev('div').children(':nth-child(3)').children(':nth-child(1)').removeClass('').addClass('diver_info_left_arrow');
					$('.gm-style-iw').prev('div').children(':nth-child(3)').children(':nth-child(2)').removeClass('').addClass('diver_info_right_arrow');
				    }
				})(marker, i));
				;
				// Automatically center the map fitting all markers on the screen
				map.fitBounds(bounds);
				
				
			    }
			}
			else
			{
			    //$('#on_going_trip_map').html('No drivers found');
			    $('#map-canvas').html('<div class="no_data">No drivers found</div>');	
			}
		
			// Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
			var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
			    //this.setZoom(12);
			    google.maps.event.removeListener(boundsListener);
			});
		
		    }
</script>


<script>
/*** Map of taxis **********/
var Path = "<?php echo URL_BASE; ?>";
function dashboard_map()
{
	var taxi_company = '';
	var driver_status=$("#select_driver_status").val();
	var dataS = "driver_status="+driver_status+"&taxi_company="+taxi_company;
	var url_path = Path+"dashboard/driver_status_details_search";
	var markers=new Array();
	$.ajax({
	    type: "GET",
	    url:url_path,
	    data: dataS, 
	    async: true,
	    contentType: "application/json; charset=utf-8",
	    dataType: "json",
	    success:function(data){
		if(data != "")
		{
		    markers=data;
		    var map;
		    var bounds = new google.maps.LatLngBounds();
		    var mapOptions = {
			mapTypeId: 'roadmap'
		    };
		    // Display a map on the page
		    map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
		    map.setTilt(45);

		    for( i = 0; i < markers.length; i++ ) { 
			// Display multiple markers on a map
			var infoWindow = new google.maps.InfoWindow(), marker, i;
			var iconBase = '<?php echo PUBLIC_IMGPATH . '/'; ?>';
						
			// Loop through our array of markers & place each one on the map  
					   
			var position = new google.maps.LatLng(markers[i][0], markers[i][1]);
			bounds.extend(position);
			marker = new google.maps.Marker({
			    position: position,
			    map: map,
			    animation: google.maps.Animation.DROP,
			    //icon: iconBase + 'car.png',
			    icon: markers[i][4],
			    //title: markers[i][2]
			});
			// Allow each marker  to have an info window
			google.maps.event.addListener(marker, 'click', (function(marker, i) {
			    return function() {
				infoWindow.setContent(markers[i][2]+markers[i][3]);
				infoWindow.open(map, marker);
			    }
			})(marker, i));
			;
			// Automatically center the map fitting all markers on the screen
			map.fitBounds(bounds);
							
							
		    }
		    // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
		    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
			//this.setZoom(4);
			google.maps.event.removeListener(boundsListener);
		    });
		}
		else
		{ 
		    $('#map-canvas').html('<div class="no_data">No drivers found</div>');
		}
	    },
	    error:function()
	    {
		//alert('failed');
	    }
	});
}
</script>

