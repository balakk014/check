<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<script src="<?php echo URL_BASE; ?>public/common/js/noty.js"></script>
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

        /*var map;

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
       
       
            
    } */

    </script>
<?php } ?>

<!-- Action tabs -->
<div class="actions-wrapper">
    <div class="actions">

	<div id="user-stats" class="user_status">

	    <ul class="round-buttons">

		<li><div class="depth tran_1"><a href="<?php echo URL_BASE; ?>transaction/admintransaction/all/" title="<?php echo __('transaction_details'); ?>" class="tip"></i></a></div>
		    <span class="rapid_title"><?php echo __('transaction'); ?></span>
		</li>
		<li><div class="depth tran_2"><a href="<?php echo URL_BASE; ?>manage/company" title="<?php echo __('taxicompany'); ?>" class="tip"></a></div>
		    <span class="rapid_title"><?php echo __('company'); ?></span>
		</li>
		<li><div class="depth tran_3"><a href="<?php echo URL_BASE; ?>manage/admin" title="<?php echo __('superadmin_management'); ?>" class="tip"></a></div>

		    <span class="rapid_title"><?php echo __('super_admin'); ?></span></li>

		<li><div class="depth tran_4"><a href="<?php echo URL_BASE; ?>admin/manage_site" title="<?php echo __('settings'); ?>" class="tip"></a></div>
		    <span class="rapid_title"><?php echo __('settings'); ?></span>
		</li>
	    </ul>
	</div>

	<div id="quick-actions">
	    <ul class="statistics">
		<li>
		    <div class="sky_blue">
			<div class="top-info">
			    <a href="<?php echo URL_BASE ?>manageusers/passenger_search?keyword=&status=A&filter_company=" title="<?php echo __(strtoupper('active users')); ?>" class="blue-square"></a>
			    <strong><?php echo $admin_dashboard_data["general_users"]; ?></strong>
			</div>
			<div class="progress progress-micro"><div class="bar" style="width: 60%;"></div></div>
		    </div>	
		    <span class="rapid_title"><?php echo __(strtoupper('active_users')); ?></span>
		</li>
		<li>
		    <div class="pink_bg">
			<div class="top-info">
			    <a href="<?php echo URL_BASE ?>manage/companysearch?keyword=&status=A&search_user=Search" title="<?php echo __(strtoupper('active Companies')); ?>" class="red-square"></a>
			    <strong><?php echo $admin_dashboard_data["company"]; ?></strong>
			</div>
			<div class="progress progress-micro"><div class="bar" style="width: 20%;"></div></div>
		    </div>
		    <span class="rapid_title"><?php echo __(strtoupper('active_companies')); ?></span>
		</li>
		<li>
		    <div class="blue_bg">
			<div class="top-info">
			    <a href="<?php echo URL_BASE ?>manage/managersearch?keyword=&status=A&filter_company=" title="<?php echo __(strtoupper('active company managers')); ?>" class="purple-square"></a>
			    <strong><?php echo $admin_dashboard_data["manager"]; ?></strong>
			</div>
			<div class="progress progress-micro"><div class="bar" style="width: 90%;"></div></div>
		    </div>
		    <span class="rapid_title"><?php echo __(strtoupper('active_company_managers')); ?></span>
		</li>
		<li>
		    <div class="green_bg">
			<div class="top-info">
			    <a href="<?php echo URL_BASE ?>manage/driversearch?keyword=&status=A&filter_company=" title="<?php echo __(strtoupper('active Drivers')); ?>" class="green-square"></a>
			    <strong><?php echo $admin_dashboard_data["driver"]; ?></strong>
			</div>
			<div class="progress progress-micro"><div class="bar" style="width: 70%;"></div></div>
		    </div>
		    <span class="rapid_title"><?php echo __(strtoupper('active Drivers')); ?></span>
		</li>
		<li>
		    <div class="orange_bg">
			<div class="top-info">
			    <a href="<?php echo URL_BASE ?>manage/taxisearch?keyword=&status=A&filter_company=" title="<?php echo __(strtoupper('active taxies')); ?>" class="sea-square"></a>
			    <strong><?php echo $admin_dashboard_data["taxi"]; ?></strong>
			</div>
			<div class="progress progress-micro"><div class="bar" style="width: 50%;"></div></div>
		    </div>
		    <span class="rapid_title"><?php echo __(strtoupper('active taxies')); ?></span>
		</li>
	    </ul>
	</div>

	<div id="map" class="user_status">
	    <ul class="dashboard_report_tab">
		<li>
		    <div class="depth tran_5 img_marg_1">
			<?php /* <a href="#free_taxies" title="<?php echo __('unassigned_taxies'); ?>"> */ ?>
			<a href="<?php echo URL_BASE; ?>manage/today_unassigned_taxi" title="<?php echo __('unassigned_taxies'); ?>">
			    <img src="<?php echo IMGPATH ?>/taxi.png" class="image" alt="<?php echo __('unassigned_taxies'); ?>"  />
			</a>
		    </div>
		    <span class="rapid_title">
			<?php
			if ($freetaxi_list_count > 0) {
			    echo $free_taxi = $freetaxi_list_count;
			} else {
			    echo $free_taxi = 0;
			}
			?>
			<?php echo __('unassigned_taxies'); ?></span>
		</li>
		<li>
		    <div class="depth tran_6 img_marg_1">
			<?php /* <a href="#free_taxi" title="<?php echo __('free_taxies_today'); ?>"> */ ?>
			<a href="<?php echo URL_BASE; ?>manage/freetaxi" title="<?php echo __('free_taxies_today'); ?>">
			    <img src="<?php echo IMGPATH ?>/taxi.png" class="image" alt="<?php echo __('free_taxies_today'); ?>"  />

			</a>
		    </div>
		    <span class="rapid_title">
<?php
if ($availabletaxi_list_count > 0) {
    echo $avai_taxi_list = $availabletaxi_list_count;
} else {
    echo $avai_taxi_list = 0;
}
?>
			<?php echo __('free_taxies_today'); ?>
		    </span>
		</li>
		<li>
		    <div class="depth tran_7 img_marg">
			<?php /* <a href="#free_drivers" title="<?php echo __('unass_drivers_today'); ?>"> */ ?>
			<a href="<?php echo URL_BASE; ?>manage/unassign_driver" title="<?php echo __('unass_drivers_today'); ?>">
			    <img src="<?php echo IMGPATH ?>/driver.png" class="image" alt="<?php echo __('unass_drivers_today'); ?>"  />
			</a>
		    </div>
		    <span class="rapid_title">
			<?php
			if ($freedriver_list_count > 0) {
			    echo $freedriver = $freedriver_list_count;
			} else {
			    echo $freedriver = 0;
			}
			?>
			<?php echo __('unass_drivers_today'); ?>
		    </span>
		</li>
		<li>
		    <div class="depth tran_8 img_marg">
			<?php /* <a href="#liveuser" title="<?php echo __('live_passengers'); ?>"> */ ?>
			<a href="<?php echo URL_BASE; ?>manageusers/live_users" title="<?php echo __('live_passengers'); ?>">
			    <img src="<?php echo IMGPATH ?>/manager.png" class="image" alt="<?php echo __('live_passengers'); ?>"  />
			</a>
		    </div>
		    <span class="rapid_title">
<?php
if ($activeusers_list_count > 0) {
    echo $activeusers = $activeusers_list_count;
} else {
    echo $activeusers = 0;
}
?>
<?php echo __('live_passengers'); ?>
		    </span>
		</li>
	    </ul>
	</div>

	<ul class="action-tabs">
	    <li><a href="#quick-actions" title=""><?php echo __('Statistics'); ?></a></li>
	    <li><a href="#user-stats" title=""><?php echo __('quick_access'); ?></a></li>
	    <li><a href="#map" title="" id="map-link"><?php echo __('reports'); ?></a></li>
	</ul>
    </div>
</div>
<!-- /action tabs -->
<!-- Chart -->
<form  action="<?php echo URL_BASE; ?>admin/dashboard" method="post" name="dashboard" id="dashboard" >
    <div class="widget1 chartWrapper">  
	<div class="admin_menu con_boxmm">
<?php /* <div id="notify_alert"></div> */ ?>
	    <ul>
		<!-- Company Details -->	
		<li class="color_code1">
		    <div class="dash_active_left"> </div> 
		    <div class="dash_active_mid">

			<div class="dash_act_img">

			    <a href="<?php echo URL_BASE ?>manage/companysearch?keyword=&status=A&search_user=Search" title="<?php echo __('companies'); ?>">
				<img src="<?php echo IMGPATH ?>/company.png" class="image" alt="<?php echo __('companies'); ?>"  />
			    </a>
			</div>

			<a href="<?php echo URL_BASE ?>manage/companysearch?keyword=&status=A&search_user=Search" title="<?php echo __('companies'); ?>" class="bb_texr">
<?php echo __('companies'); ?>
			    <span>(<?php echo $admin_dashboard_data["company"]; ?>)</span>
			</a> 

		    </div> <div class="dash_active_right">  </div> 

		</li>
		<!-- Company Details -->
		<!-- Manager Details -->
		<li class="color_code2">
		    <div class="dash_active_left"> </div> 
		    <div class="dash_active_mid">

			<div class="dash_act_img">

			    <a href="<?php echo URL_BASE ?>manage/managersearch?keyword=&status=A&filter_company=" title="<?php echo __('dispatchers'); ?>">
				<img src="<?php echo IMGPATH ?>/manager2.png" class="image" alt="<?php echo __('dispatchers'); ?>"  />
			    </a>
			</div>

			<a href="<?php echo URL_BASE ?>manage/managersearch?keyword=&status=A&filter_company=" title="<?php echo __('dispatchers'); ?>" class="bb_texr">
<?php echo __('dispatchers'); ?>
			    <span>(<?php echo $admin_dashboard_data["manager"]; ?>)</span>
			</a> 

		    </div> <div class="dash_active_right">  </div> 

		</li>
		<!-- Manager Details -->
		<!-- Driver Details -->
		<li class="color_code3">
		    <div class="dash_active_left"> </div> 
		    <div class="dash_active_mid">

			<div class="dash_act_img">

			    <a href="<?php echo URL_BASE ?>manage/driversearch?keyword=&status=A&filter_company=" title="<?php echo __('drivers'); ?>" >
				<img src="<?php echo IMGPATH ?>/driver2.png" class="image" alt="<?php echo __('drivers'); ?>" />
			    </a>
			</div>

			<a href="<?php echo URL_BASE ?>manage/driversearch?keyword=&status=A&filter_company=" title="<?php echo __('drivers'); ?>" class="bb_texr">
<?php echo __('drivers'); ?>
			    <span>(<?php echo $admin_dashboard_data["driver"]; ?>)</span>
			</a> 
		    </div> <div class="dash_active_right">  </div> 
		</li>
		<!-- Driver Details -->
		<!-- Driver Details -->
		<li class="color_code4">
		    <div class="dash_active_left"> </div> 
		    <div class="dash_active_mid">

			<div class="dash_act_img">

			    <a href="<?php echo URL_BASE ?>manage/taxisearch?keyword=&status=A&filter_company=" title="<?php echo __('taxis'); ?>">
				<img src="<?php echo IMGPATH ?>/taxi2.png" class="image" alt="<?php echo __('taxis'); ?>"  />
			    </a>
			</div>

			<a href="<?php echo URL_BASE ?>manage/taxisearch?keyword=&status=A&filter_company=" title="<?php echo __('taxis'); ?>" class="bb_texr">
<?php echo __('taxis'); ?>
			    <span>(<?php echo $admin_dashboard_data["taxi"]; ?>)</span>
			</a> 
		    </div> <div class="dash_active_right">  </div> 
		</li>
		<!-- Driver Details -->
		<!-- Customer Details -->
		<li class="color_code5">
		    <div class="dash_active_left"> </div> 
		    <div class="dash_active_mid">
			<div class="dash_act_img">
			    <a href="<?php echo URL_BASE ?>manageusers/passenger_search?keyword=&status=A&filter_company=" title="<?php echo __('customers'); ?>">
				<img src="<?php echo IMGPATH ?>/customer2.png" class="image" alt="<?php echo __('customers'); ?>"  />
			    </a>
			</div>
			<a href="<?php echo URL_BASE ?>manageusers/passenger_search?keyword=&status=A&filter_company=" title="<?php echo __('customers'); ?>" class="bb_texr">
<?php echo __('customers'); ?>
			    <span>(<?php echo $admin_dashboard_data["general_users"]; ?>)</span>
			</a> 
		    </div> <div class="dash_active_right">  </div> 
		</li>
		<!-- Customer Details -->
		<!-- Country Details -->
		<li class="color_code6">
		    <div class="dash_active_left"> </div> 
		    <div class="dash_active_mid">
			<div class="dash_act_img">
			    <a href="<?php echo URL_BASE ?>manage/countrysearch?keyword=&status=A" title="<?php echo __('countries'); ?>">
				<img src="<?php echo IMGPATH ?>/country.png" class="image" alt="<?php echo __('countries'); ?>"  />
			    </a>
			</div>
			<a href="<?php echo URL_BASE ?>manage/countrysearch?keyword=&status=A" title="<?php echo __('countries'); ?>" class="bb_texr">
<?php echo __('countries'); ?>
			    <span>(<?php echo $admin_dashboard_data["country"]; ?>)</span>
			</a> 
		    </div> <div class="dash_active_right">  </div> 
		</li>
		<!-- Country Details -->
		<!-- State Details -->
		<li class="color_code7">
		    <div class="dash_active_left"> </div> 
		    <div class="dash_active_mid">
			<div class="dash_act_img">
			    <a href="<?php echo URL_BASE ?>manage/statesearch?keyword=&status=A" title="<?php echo __('states'); ?>">
				<img src="<?php echo IMGPATH ?>/state2.png" class="image" alt="<?php echo __('states'); ?>"  />
			    </a>
			</div>
			<a href="<?php echo URL_BASE ?>manage/statesearch?keyword=&status=A" title="<?php echo __('states'); ?>" class="bb_texr">
<?php echo __('states'); ?>
			    <span>(<?php echo $admin_dashboard_data["state"]; ?>)</span>
			</a> 
		    </div> <div class="dash_active_right">  </div> 
		</li>
		<!-- State Details -->
		<!-- City Details -->
		<li class="color_code8">
		    <div class="dash_active_left"> </div> 
		    <div class="dash_active_mid">
			<div class="dash_act_img">
			    <a href="<?php echo URL_BASE ?>manage/citysearch?keyword=&status=A" title="<?php echo __('cities'); ?>">
				<img src="<?php echo IMGPATH ?>/city2.png" class="image" alt="<?php echo __('cities'); ?>"  />
			    </a>
			</div>
			<a href="<?php echo URL_BASE ?>manage/citysearch?keyword=&status=A" title="<?php echo __('cities'); ?>" class="bb_texr">
<?php echo __('cities'); ?>
			    <span>(<?php echo $admin_dashboard_data["city"]; ?>)</span>
			</a> 
		    </div> <div class="dash_active_right">  </div> 
		</li>
		<!-- City Details -->
	    </ul>
	</div>
    </div>
    <!-- Company Total Trip Details Start-->
    <div class="widget chartWrapper">
	<div class="title">
	    <img src="<?php echo IMGPATH; ?>/icons/dark/stats.png" alt="" class="titleIcon" />
	    <h6><?php echo __('total_trip_details'); ?></h6>

	    <div class="title" align="right">
		<div class="one home_trip_rgt">

		    <select name="company" id="select_company" style="width:150px;height:25px;">
			<option value=""><?php echo __('all_label'); ?></option>
<?php
if ($get_company_details > 0) {
    foreach ($get_company_details as $gcd) {
	?> 
				<option value="<?php echo $gcd['cid'] ?>"><?php echo ucfirst($gcd['company_name']); ?></option>
    <?php
    }
}
?>
		    </select>

		    <span><?php echo __('startdate'); ?></span>
		    <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="total_trips_startdate" name="total_trips_startdate" value="<?php echo Commonfunction::getDateTimeFormat($startdate, 1); ?>"  />
		    <span id="ttstartdate_error" class="errors" style="display:none;"></span>
		    <span><?php echo __('enddate'); ?></span>
		    <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="total_trips_enddate" name="total_trips_enddate" value="<?php echo Commonfunction::getDateTimeFormat($enddate, 1); ?>"  />
		    <span id="ttenddate_error" class="errors" style="display:none;"></span>
		    <div class="button_new"> 
			<input type="button" name="search_total_trips" id="search_total_trips" value="GO" title="Go" >
		    </div>
		</div>
	    </div>

	</div>
	<div class="body" id="admin_total_trips_details">
<?php //if($display == 'display:none;'){ echo "<div class='no_data'>".__('no_data')."</div>"; }  ?>
	    <div id="total_trips_details" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
	</div>
    </div>
    <!-- Company Total Trip Details End-->

    <!--Dashboard Current Status of The Driver -->
    <div class="widget margin-bottom" >
	<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo __('current_statusof_driver'); ?></h6>
	    <div class="title" align="right">
		<div class="one home_trip_rgt">
		    <select name="company" id="select_company_map" style="width:150px;height:25px;">
			<option value=""><?php echo __('all_label'); ?></option>
<?php
if ($get_company_details > 0) {
    foreach ($get_company_details as $gcd) {
	?> 
				<option value="<?php echo $gcd['cid'] ?>"><?php echo ucfirst($gcd['company_name']); ?></option>
    <?php }
}
?>
		    </select>
		    <select name="select_driver_status" id="select_driver_status" style="width:150px;height:25px;">
			<option value=""><?php echo __('all_label') . __('drivers'); ?></option>
			<option value="A"><?php echo __('active'); ?></option>
			<option value="F"><?php echo __('free_in'); ?></option>
			<option value="B"><?php echo __('busy'); ?></option>
			<option value="OUT"><?php echo __('free_and_shiftout'); ?></option>
		    </select>

		    <div class="button_new"> 
			<input type="button" name="search_driver_status" id="search_driver_status" value="GO" title="Go" onclick=dashboard_map() >
		    </div>
		</div>
	    </div>
	    <!--<div style="width:auto; float:right; margin: 2px 2px;">		
	    <div class="title"><?php echo __('notification_setting') . " : "; ?><span class='btn btn-mini btn-primary'><?php echo $not_setting; ?></span></div>
	    </div>-->
	</div>
	<div id="on_going_trip_map" >
	    <!--div class="ongoing">
		    <div id="on_going_trip"></div>
		    <div id="on_going_place"></div>
	    </div-->
<?php if (SHOW_MAP != 1) { ?>
    	    <div id="map-canvas" style="width:100%;height:500px;background-color:white;"></div>
<?php } ?>
	</div>
    </div>
    <!--Dashboard Current Status of The Driver -->

    <!--Dashboard Current Location of Passengers -->
    <div class="widget margin-bottom" >
	<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo __('current_locationofpassenger'); ?></h6>
	</div>
	<div id="passenger_map" >
	    <?php if (SHOW_MAP != 1) { ?>
    	    <div id="pass-map-canvas" style="width:100%;height:500px;background-color:white;"></div>
<?php } ?>
	</div>
    </div>
    <!--Dashboard Current Location of Passengers-->

    <div class="widget chartWrapper">
	<div class="title">
	    <img src="<?php echo IMGPATH; ?>/icons/dark/stats.png" alt="" class="titleIcon" />
	    <h6><?php echo __('total_company_details'); ?></h6>

	    <div class="title" align="right">
		<div class="one home_trip_rgt">

		    <select name="company" id="select_user_company" style="width:150px;height:25px;">
<?php /* <option value=""><?php echo __('all_label'); ?></option> */ ?>
<?php
if ($get_company_details > 0) {
    foreach ($get_company_details as $gcd) {
	?> 
				<option value="<?php echo $gcd['cid'] ?>"><?php echo ucfirst($gcd['company_name']); ?></option>
    <?php
    }
}
?>
		    </select>

		    <div class="button_new"> 
			<input type="button" name="search_total_count" id="search_total_count" onclick="getallUsersCompanywise('1');" value="GO" title="Go" >
		    </div>
		</div>
	    </div>

	</div>
	<div class="body" id="company_total_user_counts">
<?php //if($display == 'display:none;'){ echo "<div class='no_data'>".__('no_data')."</div>"; }  ?>
	    <div id="total_user_counts" style="min-width: 400px; height: 400px; margin: 0 auto"></div>

	</div>
    </div>
</form>
</div>
<script type="text/javascript">
    $(document).ready(function () {
	$("#transtartdate").datetimepicker( {
	    showTimepicker:true,
	    showSecond: true,
	    timeFormat: 'hh:mm:ss',
	    dateFormat: 'yy-mm-dd',
	    stepHour: 1,
	    stepMinute: 1,
	    maxDateTime : new Date(),
	    stepSecond: 1
	} );

	$("#tranenddate").datetimepicker( {
	    showTimepicker:true,
	    showSecond: true,
	    timeFormat: 'hh:mm:ss',
	    dateFormat: 'yy-mm-dd',
	    stepHour: 1,
	    stepMinute: 1,
	    maxDateTime : new Date(), 
	    stepSecond: 1
	} );

	$("#userstartdate").datetimepicker( {
	    showTimepicker:true,
	    showSecond: true,
	    timeFormat: 'hh:mm:ss',
	    dateFormat: 'yy-mm-dd',
	    stepHour: 1,
	    stepMinute: 1,
	    maxDateTime : new Date(),
	    stepSecond: 1
	} );

	$("#userenddate").datetimepicker( {
	    showTimepicker:true,
	    showSecond: true,
	    timeFormat: 'hh:mm:ss',
	    dateFormat: 'yy-mm-dd',
	    stepHour: 1,
	    stepMinute: 1,
	    maxDateTime : new Date(), 
	    stepSecond: 1
	} );

	$("#total_trips_startdate").datetimepicker( {
	    showTimepicker:DEFAULT_TIME_SHOW,
	    showSecond: true,
	    timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	    dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	    stepHour: 1,
	    stepMinute: 1,
	    maxDateTime : new Date(),
	    stepSecond: 1
	} );

	$("#total_trips_enddate").datetimepicker( {
	    showTimepicker:DEFAULT_TIME_SHOW,
	    showSecond: true,
	    timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	    dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	    stepHour: 1,
	    stepMinute: 1,
	    maxDateTime : new Date(), 
	    stepSecond: 1
	} );


	/*chart = new Highcharts.Chart({
		    chart: {
			renderTo: 'container_userchart',
			type: 'line',
			marginRight: 130,
			marginBottom: 25,
			events: {
			    load: requestData
			}
		    },
		    title: {
			text: "<?php echo __('users_month'); ?>",
			x: -20 //center
		    },
		    subtitle: {
			text: "<?php echo __('for_label') . ' ' . date('Y'); ?>",
			x: -20
		    },
		    xAxis: {
			categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
			    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
		    },
		    yAxis: {
			title: {
			    text: "<?php echo __('users_count'); ?>"
			},
			plotLines: [{
			    value: 0,
			    width: 1,
			    color: '#808080'
			}]
		    },
		    tooltip: {
			valueSuffix: ''
		    },
		    legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: -10,
			y: 100,
			borderWidth: 0
		    },
		    series: [{
			name: "<?php echo __('users_count'); ?>",					
			data : []
		    }]
	}); */


	/*function requestData() {
		$.ajax({
			url: '<?php echo URL_BASE; ?>dashboard/getUsers',
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
					var series = chart.series[0],
					shift = series.data.length > 30; // shift if the series is longer than 300 (drop oldest point)
					$.each( json['success']['users'], function( key, value ) {
						// add the point
						chart.series[0].addPoint(eval(value['count']), true, shift);
					});
				}
			}
		});
	} */

    	
    });

<?php /*
  if($getUserbyCompany !='') { ?>

  $(function () {

  // Radialize the colors
  Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
  return {
  radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
  stops: [
  [0, color],
  [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
  ]
  };
  });

  // Build the chart
  $('#user_company').highcharts({
  chart: {
  plotBackgroundColor: null,
  plotBorderWidth: null,
  plotShadow: false
  },
  title: {
  text: 'Drivers by Company'
  },
  tooltip: {
  pointFormat: '{series.name}: <b>{point.y}</b>'
  },
  plotOptions: {
  pie: {
  allowPointSelect: true,
  cursor: 'pointer',
  dataLabels: {
  enabled: true,
  color: '#000000',
  connectorColor: '#000000',
  formatter: function() {
  return '<b>'+ this.point.name +'</b>: '+ this.y;
  }
  }
  }
  },
  series: [{
  type: 'pie',
  name: 'Total Users',
  data: [
  <?php echo $getUserbyCompany; ?>
  ]
  }]
  });
  });
  <?php } ?>

  <?php if($transactionbyCompany !='') { ?>

  $(function () {

  // Build the chart
  $('#transaction_company').highcharts({
  chart: {
  plotBackgroundColor: null,
  plotBorderWidth: null,
  plotShadow: false
  },
  title: {
  text: 'Company Transaction'
  },
  tooltip: {
  pointFormat: '{series.name}: <b>{point.percentage}%</b>',
  //pointFormat: '{series.name}: <b>{point.y}</b>'
  percentageDecimals: 1
  },
  plotOptions: {
  pie: {
  allowPointSelect: true,
  cursor: 'pointer',
  dataLabels: {
  enabled: true,
  color: '#000000',
  connectorColor: '#000000',
  formatter: function() {
  //$nightfare = ($night_fare/100)*$total_fare;//night_charge%100;
  //var new_val = (Math.round(this.percentage*100)/100);
  var new_val = (Math.round(this.percentage*100));
  return '<b>'+ this.point.name +'</b>:'+ new_val +' %';
  //return '<b>'+ this.point.name +'</b>: '+ this.y;
  }
  }
  }
  },
  series: [{
  type: 'pie',
  name: 'Company Transaction',
  data: [
  <?php echo $transactionbyCompany; ?>
  ]
  }]
  });
  });

  <?php } ?>


  <?php if($company_accountbalance !='') { ?>

  $(function () {

  // Build the chart
  $('#company_accountbalance').highcharts({


  chart: {
  plotBackgroundColor: null,
  plotBorderWidth: null,
  plotShadow: false
  },
  title: {
  text: 'Company Account Balance'
  },
  tooltip: {
  pointFormat: '{series.name}: <b><?php echo CURRENCY; ?>{point.y}</b>',
  percentageDecimals: 100
  },
  plotOptions: {
  pie: {
  allowPointSelect: true,
  cursor: 'pointer',
  dataLabels: {
  enabled: true,
  color: '#000000',
  connectorColor: '#000000',
  formatter: function() {

  return '<b>'+ this.point.name +'</b>: <?php echo CURRENCY; ?>'+ this.y;
  }
  }
  }
  },
  series: [{
  type: 'pie',
  name: 'Company Account Balance',
  /*point:{
  events:{
  click: function (event) {
  location.href = '<?php URL_BASE;?>/admin/manage_fund_request/all/';
  }
  }
  },
  data: [
  <?php echo $company_accountbalance; ?>

  ]
  }]
  });
  });

  <?php } ?>

 */ ?>

    //ajax_call_notify(0);
    //Function used for the Push notifications
    /*setInterval(function() 
{
	if ($("#notify_alert").length > 0)
	{
		//ajax_call_notify(1);
	}   
	
	get_liveuser();
	
}, 500000);
/*
function ajax_call_notify(flag)
{

	if ($("#notify_alert"))
	{
		
		var value = $('#driver_logs').val();
		
		var dataS = "value="+value+"&flag="+flag;			
		var response;
		$.ajax
		({ 			
			type: "POST",
			url: "<?php echo URL_BASE; ?>admin/get_admin_notifications", 
			data: dataS, 
			cache: false, 
			dataType: 'html',
			success: function(response) 
			{ 		
				//alert(response);		
				if(flag==0){
					//alert(response);
					//$('#notify_alert').html(response);	
					var ars =  ["Afghanistan","Albania","Algeria","American Samoa","Andorra","Angola"];
					//$.each(ars, function(i, val) {
							//alert(val)
							var n = noty({
							text: response,//'<strong>Hi!</strong>  You have new booking request!',
							type: 'confirm',
							layout: 'bottomRight',
							closeWith: ['click'],
							});
						//});
}
				else{
					//$('#notify_alert').html(response);	
					}
			} 
			 
		});	
	}
	else
		return false;
}
     */

    function get_liveuser()
    {
	
	var dataS = "1=1";
	var SrcPath = "<?php echo URL_BASE; ?>";
	var response;
	$.ajax
	({ 			
	    type: "POST",
	    url: SrcPath+"admin/activeusers_list", 
	    data: dataS, 
	    success: function(response) 
	    { 	
		$('#liveuser').html();
		$('#liveuser').html(response);
	    } 
		 
	});	
    }



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
	    if(startdate > enddate)
	    {
		$("#startdate_error").html("<?php echo __('startdate_greater'); ?>");
		$("#startdate_error").show();
	    }
	    else
	    {
		$("#startdate_error").html("");
		$("#startdate_error").hide();
		document.forms['dashboard'].submit();
			
	    }
	}
	 
    });

    $("#change_transcompany").click(function(){


	var startdate = $("#transtartdate").val();
	var enddate = $("#tranenddate").val();
	if(startdate =='')
	{
	    $("#transtartdate_error").html("<?php echo __('select_startdate'); ?>");
	    $("#transtartdate_error").show();
	}
	else
	{
	    $("#transtartdate_error").html("");
	    $("#transtartdate_error").hide();
	}
	if(enddate =='')
	{
	    $("#tranenddate_error").html("<?php echo __('select_enddate'); ?>");
	    $("#tranenddate_error").show();
	}
	else
	{
	    $("#tranenddate_error").hide("");
	    $("#tranenddate_error").hide();
	}
	if(startdate !='' && enddate!='')
	{
	    if(startdate > enddate)
	    {
		$("#transtartdate_error").html("<?php echo __('startdate_greater'); ?>");
		$("#transtartdate_error").show();
	    }
	    else
	    {
		$("#transtartdate_error").html("");
		$("#transtartdate_error").hide();
		document.forms['dashboard'].submit();
			
	    }
	}
	 
    });

    $("#change_company_trip_count").click(function(){


	var startdate = $("#tripstartdate").val();
	var enddate = $("#tripenddate").val();
	if(startdate =='')
	{
	    $("#tripstartdate_error").html("<?php echo __('select_startdate'); ?>");
	    $("#tripstartdate_error").show();
	}
	else
	{
	    $("#tripstartdate_error").html("");
	    $("#tripstartdate_error").hide();
	}
	if(enddate =='')
	{
	    $("#tripenddate_error").html("<?php echo __('select_enddate'); ?>");
	    $("#tripenddate_error").show();
	}
	else
	{
	    $("#tripenddate_error").hide("");
	    $("#tripenddate_error").hide();
	}
	if(startdate !='' && enddate!='')
	{
	    if(startdate > enddate)
	    {
		$("#tripstartdate_error").html("<?php echo __('startdate_greater'); ?>");
		$("#tripstartdate_error").show();
	    }
	    else
	    {
		$("#tripstartdate_error").html("");
		$("#tripstartdate_error").hide();
		document.forms['dashboard'].submit();
			
	    }
	}
	 
    });
</script>


<script src="<?php echo SCRIPTPATH; ?>highcharts.js"></script>
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
    $("#tripstartdate").datetimepicker( {
	showTimepicker:true,
	showSecond: true,
	timeFormat: 'hh:mm:ss',
	dateFormat: 'yy-mm-dd',
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(),
	stepSecond: 1
    } );

    $("#tripenddate").datetimepicker( {
	showTimepicker:true,
	showSecond: true,
	timeFormat: 'hh:mm:ss',
	dateFormat: 'yy-mm-dd',
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(), 
	stepSecond: 1
    } );
	

</script>

<script>
    $(function () {
	chart2 = new Highcharts.Chart({
	    chart: {
		renderTo: 'total_trips_details',
		type: 'line',
		marginRight: 130,
		marginBottom: 25,
		shortMonths:true,
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
			format: '{value} <?php echo CURRENCY; ?>',
			style: {
			    color: Highcharts.getOptions().colors[0]
			}
		    }

		} ],
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
			valueSuffix: ' <?php echo CURRENCY; ?>'
		    }

		},
		{
		    name: '<?php echo __('admin_commision'); ?>',
		    type: 'column',
		    yAxis: 1,
		    data : [],
		    tooltip: {
			valueSuffix: ' <?php echo CURRENCY; ?>'
		    }

		},
		{
		    name: '<?php echo __('trip_counts'); ?>',
		    type: 'spline',
		    yAxis: 0,
		    data : [],
		    tooltip: {
			valueSuffix: ' Trips'
		    }
		}, {
		    type: 'pie',
		    name: '<?php echo __('trip_counts'); ?>',
		    data: [],
		    center: [600, 60],
		    size: 100,
		    showInLegend: false,
		    dataLabels: {
			enabled: false
		    }
		}]
	});

	function requestData_trip_counts() {
	    $.ajax({
		url: '<?php echo URL_BASE; ?>dashboard/get_company_trip_count',
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
			//console.log("Trip Details:"+json['success']['trips']);
			var series = chart2.series[0],
			shift = series.data.length > 15; // shift if the series is longer than 300 (drop oldest point)
			$.each( json['success']['trips'], function( key, value ) {
			    chart2.series[0].addPoint(eval(value['revenues']), true, shift);
			    chart2.series[1].addPoint(eval(value['admincommission']), true, shift);
			    chart2.series[2].addPoint(eval(value['trips']), true, shift);
			    //console.log(key+":"+value['trips']);
			    //console.log(key+":"+value['revenues']);
			});
							
			chart2.series[3].addPoint({name: 'Mobile App',y: json['success']['mobiletrips'],color: Highcharts.getOptions().colors[0] },true,shift);
			chart2.series[3].addPoint({name: 'Web App',y: json['success']['webtrips'],color: Highcharts.getOptions().colors[1] },true,shift);
						
		    }
		}
	    });
	}
		
		
	//chart to get total count of passengers, Drivers, and Taxi 
	chartCompanyUser = new Highcharts.Chart({
	    chart: {
		renderTo: 'total_user_counts',
		type: 'column',
		events: {
		    load: getallUsersCompanywise
		}
	    },

	    title: {
		text: '<?php echo __("total_users_groupedbycompany"); ?>'
	    },

	    xAxis: {
		categories: []
	    },

	    yAxis: {
		allowDecimals: false,
		min: 0,
		title: {
		    text: '<?php echo __("numberofusers"); ?>'
		}
	    },

	    series: [{
		    name: '<?php echo __("drivers"); ?>',
		    data: []
		}, {
		    name: '<?php echo __("taxi"); ?>',
		    data: []
		}, {
		    name: '<?php echo __("passengers"); ?>',
		    data: []
		}]
	});		
});
	
    //to get all users count(driver, taxi and passengers) by companywise
    function getallUsersCompanywise(temp)
    {
	var company = $("#select_user_company").val();
	$.ajax({
	    url: '<?php echo URL_BASE; ?>dashboard/getallUsersCompanywise',
	    type: 'post',
	    data: 'company='+company,
	    dataType: 'json',				
	    success: function(json) {
		if (json['error']) {
		    alert(json['error']);
		}
				
		if (json['success']) {
		    var series = chartCompanyUser.series[0],
		    shift = series.data.length > 15; // shift if the series is longer than 300 (drop oldest point)
		    //console.log(json['success']['totalusers']);
		    chartCompanyUser.xAxis[0].setCategories([json['success']['companyName']]);
		    //to set series in chart(data)
		    $.each( json['success']['totalusers'], function( key, value ) {
			if(temp == 1) {
			    chartCompanyUser.series[0].setData([eval(value['drivers'])], true, shift);
			    chartCompanyUser.series[1].setData([eval(value['taxis'])], true, shift);
			    chartCompanyUser.series[2].setData([eval(value['passengers'])], true, shift);
			} else {
			    chartCompanyUser.series[0].addPoint(eval(value['drivers']), true, shift);
			    chartCompanyUser.series[1].addPoint(eval(value['taxis']), true, shift);
			    chartCompanyUser.series[2].addPoint(eval(value['passengers']), true, shift);
			}
							
		    });
		}
	    }
	});
    }
	

    /*** Get Total updates Chart Start ***/
    /*$(function () {
		chart3 = new Highcharts.Chart({
			chart: {
                renderTo: 'container_total_updates',
				plotBackgroundColor: null,
				plotBorderWidth: 0,
				plotShadow: false,
				events: {
					load: get_admin_total_details,
				},
            },
			legend: {
				layout: 'vertical',
				align: 'right',
				verticalAlign: 'top',
				x: 10,
				y: 0,
				borderWidth: 0,
				backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
			},
            title: {
                text: '<?php echo __('total_updates'); ?>'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}</b>: {point.percentage:.2f} %',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
						}
					},
                    showInLegend: true,
                    events:{
						click: function (event, i) {
							var en=event.point.name;
							var res = en.split("(");
							var ename=res[0];
							if(ename=='TOTAL TAXIES '){
								window.open('<?php echo URL_BASE; ?>manage/taxi','_newtab');
								//window.open('<?php echo URL_BASE; ?>manage/taxi');
							}else if(ename=='TOTAL PASSENGERS '){
								window.open('<?php echo URL_BASE; ?>manageusers/passengers','_newtab');
								//window.open('<?php echo URL_BASE; ?>manageusers/passengers');
							}else if(ename=='TOTAL DRIVERS '){
								window.open('<?php echo URL_BASE; ?>manage/driver','_newtab');
								//window.open('<?php echo URL_BASE; ?>manage/driver');
							}else if(ename=='TOTAL COMPANIES '){
								window.open('<?php echo URL_BASE; ?>manage/company','_newtab');
								//window.open('<?php echo URL_BASE; ?>manage/company');
							}
						}
					}
                }
            },
            series: [{
                type: 'pie',
                name: 'Latest updates',
                data: []
            }]
		});

		function get_admin_total_details() {
			$.ajax({
				url: '<?php echo URL_BASE; ?>dashboard/get_admin_total_details',
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
						//console.log("Latest Details:"+json['success']['latest_details']);
						var series = chart3.series[0],
						shift = series.data.length > 15; // shift if the series is longer than 300 (drop oldest point)
						$.each( json['success']['latest_details'], function( key, value ) {
							chart3.series[0].addPoint(eval(value['company_countlist']), true, shift);
							chart3.series[0].addPoint(eval(value['passenger_countlist']), true, shift);
							chart3.series[0].addPoint(eval(value['drivers_countlist']), true, shift);
							chart3.series[0].addPoint(eval(value['availabletaxi_countlist']), true, shift);
							/*
							console.log(key+":"+value['company_countlist']);
							console.log(key+":"+value['passenger_countlist']);
							console.log(key+":"+value['drivers_countlist']);
							console.log(key+":"+value['availabletaxi_countlist']);
							
						});
					}
				}
			});
		}
	});
	/*** Get Total updates Chart End ***/

    /*** Get Latest updates Chart Start ***/
    /*$(function () {
			
		chart4 = new Highcharts.Chart({
			colors: ['#04B4AE', '#6A0888', '#0040FF', '#000000'],
			chart: {
                renderTo: 'container_latest_updates',
				plotBackgroundColor: null,
				plotBorderWidth: 0,
				plotShadow: false,
				events: {
					load: get_admin_latest_details,
				},
            },
			legend: {
				layout: 'vertical',
				align: 'right',
				verticalAlign: 'top',
				x: 10,
				y: 0,
				borderWidth: 0,
				backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
			},
            title: {
                text: '<?php echo __('latest_updates'); ?>'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}</b>: {point.percentage:.2f} %',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
						}
					},
                    showInLegend: true,
                    events:{
						click: function (event, i) {
							var en=event.point.name;
							var res = en.split("(");
							var ename=res[0];
							if(ename=='LIVE PASSENGERS '){
								window.open('<?php echo URL_BASE; ?>manageusers/live_users','_newtab2');
								//window.open('<?php echo URL_BASE; ?>manageusers/live_users');
							}else if(ename=='TODAY UNASSIGNED TAXIES '){
								window.open('<?php echo URL_BASE; ?>manage/today_unassigned_taxi','_newtab2');
								//window.open('<?php echo URL_BASE; ?>manage/today_unassigned_taxi');
							}else if(ename=='TODAY UNASSIGNED DRIVERS '){
								window.open('<?php echo URL_BASE; ?>manage/unassign_driver','_newtab2');
								//window.open('<?php echo URL_BASE; ?>manage/unassign_driver');
							}else if(ename=='TODAY FREE TAXIES '){
								window.open('<?php echo URL_BASE; ?>manage/freetaxi','_newtab2');
								//window.open('<?php echo URL_BASE; ?>manage/freetaxi');
							}
						}
					}
                }
            },
            series: [{
                type: 'pie',
                name: 'Latest updates',
                data: []
            }]
		});

		function get_admin_latest_details() {
			$.ajax({
				url: '<?php echo URL_BASE; ?>dashboard/get_admin_latest_details',
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
						//console.log("Latest Details:"+json['success']['latest_details']);
						var series = chart4.series[0],
						shift = series.data.length > 15; // shift if the series is longer than 300 (drop oldest point)
						$.each( json['success']['latest_details'], function( key, value ) {
							chart4.series[0].addPoint(eval(value['activeusers_list_count']), true, shift);
							chart4.series[0].addPoint(eval(value['availabletaxi_list_count']), true, shift);
							chart4.series[0].addPoint(eval(value['freedriver_list_count']), true, shift);
							chart4.series[0].addPoint(eval(value['freetaxi_list_count']), true, shift);
							/*
							console.log(key+":"+value['company_countlist']);
							console.log(key+":"+value['passenger_countlist']);
							console.log(key+":"+value['drivers_countlist']);
							console.log(key+":"+value['availabletaxi_countlist']);
							
						});
					}
				}
			});
		}
	});
	/*** Get Latest updates Chart End ***/

    /*** Total Trips and Toal Revenue **********/
    $("#search_total_trips").click(function() {
	var startdate = $("#total_trips_startdate").val();
	var enddate = $("#total_trips_enddate").val();
	var select_company = $("#select_company").val();

	if(select_company !="") {
	    var company = $("#select_company").val();
	} else {
	    var company="";
	}

	if(startdate =='') {
	    $("#ttstartdate_error").html("<?php echo __('select_startdate'); ?>");
	    $("#ttstartdate_error").show();
	} else {
	    $("#ttstartdate_error").html("");
	    $("#ttstartdate_error").hide();
	}

	if(enddate =='') {
	    $("#ttenddate_error").html("<?php echo __('select_enddate'); ?>");
	    $("#ttenddate_error").show();
	} else {
	    $("#tenddate_error").hide("");
	    $("#tenddate_error").hide();
	}

	if(startdate !='' && enddate!='') {
	    $('#admin_total_trips_details').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
	    if(to_timestamp(startdate) > to_timestamp(enddate)) {
		$("#ttstartdate_error").html("<?php echo __('startdate_greater'); ?>");
		$("#ttstartdate_error").show();
	    } else {
		$("#ttstartdate_error").html("");
		$("#ttstartdate_error").hide();
		var dataS = "startdate="+startdate+"&enddate="+enddate+"&company="+company;
		$.ajax ({
		    type: "POST",
		    url: SrcPath+"dashboard/total_trip_details_search", 
		    data: dataS, 
		    cache: false, 
		    dataType: 'html',
		    success: function(response) 
		    {
			$('#admin_total_trips_details').html(response);
		    } 
		});
	    }
	}
    });
</script>

<script>
    //~ jQuery(function($) {
		//~ // Asynchronously Load the map API 
		//~ var script = document.createElement('script');  
		//~ script.src = "https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&amp;sensor=false&callback=initialize";
		//~ document.body.appendChild(script);
    //~ });

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
$b = 5;
if (count($all_company_map_list) > 0) {
    foreach ($all_company_map_list as $v) {
	for ($b = 0; $b < 5; $b++) {
	    if ($b == 0) {
		?>
								markers [<?php echo $a; ?>] = new Array(4);
								markers[<?php echo $a; ?>][<?php echo $b; ?>]=<?php echo $v['latitude']; ?>;
	    <?php
	    }
	    if ($b == 1) {
		?>
								markers[<?php echo $a; ?>][<?php echo $b; ?>]=<?php echo $v['longitude']; ?>;
	    <?php
	    }
	    if ($b == 2) {
		?>
								markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo '<div class="marker-info-win"><div class="marker-inner-win"><span class="info-content"><b>' . __('driver_name') . '</b> : ' . $v['name']; ?>';
	    <?php
	    }
	    if ($b == 3) {
		$driver_status = ($v['driver_status'] == 'F' && $v['shift_status'] == 'IN') ? __('free_in') : (($v['driver_status'] == 'A') ? "<span>" . __('hired') . "</span>" : (($v['driver_status'] == 'B') ? "<span>" . __('trip_assigned') . "</span>" : __('free_out')));
		$txtcolor = ($v['driver_status'] == 'F' && $v['shift_status'] == 'IN') ? 'green' : (($v['driver_status'] == 'A') ? '#07841E' : (($v['driver_status'] == 'B') ? 'red' : '#0F9ED6'));
		?>
								markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo '<div id="bodyContent"><p><b>' . __('driver_status') . '</b>: <b style="color:' . $txtcolor . ';">' . $driver_status . '</b></p></div></span></div></div>'; ?>';
	    <?php
	    }
	    /* if($b==4)
	      { $shift_status=($v['shift_status']=='IN')?__('in'):__('out');?>
	      markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo '<b style="color:#0F9ED6;">'.$shift_status.'</b></p></div></span></div></div>'; ?>';
	      <?php } */
	    if ($b == 4) {
		if ($v['driver_status'] == 'F' && $v['shift_status'] == 'OUT') {
		    ?>
		    						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo PUBLIC_IMGPATH . '/driver_four.png'; ?>';
		<?php } elseif ($v['driver_status'] == 'A') { ?>
		    						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo PUBLIC_IMGPATH . '/driver_two.png'; ?>';
		<?php } else if ($v['driver_status'] == 'B') { ?>
		    						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo PUBLIC_IMGPATH . '/driver_one.png'; ?>';
		<?php } else if ($v['driver_status'] == 'F' && $v['shift_status'] == 'IN') { ?>
		    						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo PUBLIC_IMGPATH . '/driver_three.png'; ?>';
		<?php
		}
	    }
	}
	$a++;
    }
}
?>   
		    
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
				    //icon: iconBase + 'car.png',
				    icon: markers[i][4],
				    //closeclick:true,
				    //title: markers[i][2]
				});
				// Allow each marker  to have an info window
				google.maps.event.addListener(marker, 'click', (function(marker, i) {
				    return function() {
					infoWindow.setContent(markers[i][2]+markers[i][3]);
					infoWindow.open(map, marker);
				    }
				})(marker, i));
				
				// Automatically center the map fitting all markers on the screen
				map.fitBounds(bounds);
				
				
			    }
			}
			else
			{
			    $('#on_going_trip_map').html('<?php echo __("no_drivers_found"); ?>');	
			}
		
			// Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
			var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
			    //this.setZoom(12);
			    google.maps.event.removeListener(boundsListener);
			});
		
			//** Passenger Map Section **//
			var passMarkers = [];
<?php
if (count($activePassengers) > 0) {
    $inc = 0;
    foreach ($activePassengers as $passval) {
	if (!empty($passval['latitude']) && !empty($passval['longitude'])) {
	    ?>
	    				 passMarkers[<?php echo $inc; ?>] = new Array(3);
	    				 passMarkers[<?php echo $inc; ?>][0] = <?php echo $passval['latitude']; ?>;
	    				 passMarkers[<?php echo $inc; ?>][1] = <?php echo $passval['longitude']; ?>;
	    				 passMarkers[<?php echo $inc; ?>][2] = '<?php echo '<div class="marker-info-win"><div class="marker-inner-win"><span class="info-content"><b>' . __('passenger_name') . '</b> : ' . $passval['name']; ?></span></div></div>';
	    <?php
	    $inc++;
	}
    }
}
?>
			 var passengerMap = new google.maps.Map(document.getElementById("pass-map-canvas"), mapOptions);
			 passengerMap.setTilt(45);
			 if(passMarkers != "")
			 {
			     for( p = 0; p < passMarkers.length; p++ ) { 
				 // Display multiple markers on a map
				 var pinfoWindow = new google.maps.InfoWindow(), passmarker, p;
				 var iconBase = '<?php echo PUBLIC_IMGPATH . '/'; ?>';
				   
				 // Loop through our array of markers & place each one on the map 
				 var position = new google.maps.LatLng(passMarkers[p][0], passMarkers[p][1]);
				 bounds.extend(position);
				 passmarker = new google.maps.Marker({
				     position: position,
				     map: passengerMap,
				     animation: google.maps.Animation.DROP,
				     icon: iconBase + 'driver_one.png',
				 });
				 // Allow each marker  to have an info window
				 google.maps.event.addListener(passmarker, 'click', (function(passmarker, p) {
				     return function() {
					 pinfoWindow.setContent(passMarkers[p][2]);
					 pinfoWindow.open(passengerMap, passmarker);
				     }
				 })(passmarker, p));
				 // Automatically center the map fitting all markers on the screen
				 passengerMap.fitBounds(bounds);
			     }
			 }
		
		
        
		     }
</script>

<script>
    /*** Map of taxis **********/
    /*$("#search_driver_status").click(function(){ */
    var Path = "<?php echo URL_BASE; ?>";
    function dashboard_map()
    {
	var taxi_company = $("#select_company_map").val();
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
		    $('#map-canvas').html('<div class="no_data"><?php echo __("no_drivers_found"); ?></div>');
		}
	    },
	    error:function()
	    {
		//alert('failed');
	    }
	});
    }
</script>

<script type="text/javascript">
    //initialize();
    setInterval(function() 
    {
	dashboard_map();	
			   
    }, 60000);
</script>


<?php /* Latest update chart referenece

  $(function () {
  $('#container').highcharts({
  chart: {
  plotBackgroundColor: null,
  plotBorderWidth: 0,
  plotShadow: false
  },
  title: {
  text: 'Browser<br>shares',
  align: 'center',
  verticalAlign: 'middle',
  y: 50
  },
  tooltip: {
  pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
  },
  plotOptions: {
  pie: {
  dataLabels: {
  enabled: true,
  distance: -50,
  style: {
  fontWeight: 'bold',
  color: 'white',
  textShadow: '0px 1px 2px black'
  }
  },
  startAngle: -90,
  endAngle: 90,
  center: ['50%', '75%'],
  events:{
  click: function (event, i) {
  //alert(event.point.name);
  window.location.href="www.google.com";
  }
  }
  }
  },
  series: [{
  type: 'pie',
  name: 'Browser share',
  innerSize: '50%',
  data: [
  ['Firefox',   45.0],
  ['IE',       26.8],
  ['Chrome', 12.8],
  ['Safari',    8.5],
  ['Opera',     6.2],
  {
  name: 'Others',
  y: 0.7,
  dataLabels: {
  enabled: false
  }
  }
  ]
  }]
  });
  });


 */ ?>
