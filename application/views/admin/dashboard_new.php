<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<link rel="stylesheet" href="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE; ?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script src="<?php echo URL_BASE; ?>public/common/js/highcharts.js"></script>
<script src="<?php echo URL_BASE; ?>public/common/js/highcharts-3d.js"></script>
<script src="<?php echo URL_BASE; ?>public/common/js/exporting.js"></script>
<?php /*<script src="http://maps.google.com/maps/api/js"></script> */ ?>
<div class="main_dashboard_page">
    <div class="dashboard_top_search_part">
		<div class="dashboard_calender_in_header with_select_box">
	<ul>
	    <li>
		  <div class="new_dash_calender_select" <?php if ($_SESSION['company_id'] > 0) { ?>style="display:none;"<?php } ?>>
		<select name="company" >
		   <optgroup label="<?php echo __('Multy'); ?>">
			<option value=""><?php echo __('select_brand_label'); ?></option>
			<?php
				if ($getAllCompany > 0) {
				foreach ($getAllCompany as $pc) { if($pc['company_brand_type'] == "M") { ?>
					<option value="<?php echo $pc['cid']; ?>"><?php echo ucfirst($pc['company_name']); ?></option>
				<?php } } ?>
			</optgroup>
			<optgroup label="Single">
				<?php foreach ($getAllCompany as $pc) { if($pc['company_brand_type'] == "S") { ?>
					<option value="<?php echo $pc['cid']; ?>"><?php echo ucfirst($pc['company_name']); ?></option>
				<?php } } ?>
			</optgroup>
			<?php } ?>
		</select>
		  </div>
		  <input type="hidden" name="ses_company" id="ses_company" value="<?php echo $_SESSION['company_id']; ?>">
	    </li>
	    <li>
		 <div class="new_dash_calender_input">
		<input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="total_data_startdate" name="total_data_startdate" value="<?php echo date('Y-m-d 00:00:00', strtotime('-7 days')); ?>"  />
		<span id="ttstartdate_error" class="errors" style="display:none;"></span>
		 </div>
	    </li>
	    <li>
		 <div class="new_dash_calender_input">
		<input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="total_data_enddate" name="total_data_enddate" value="<?php echo convert_timezone('now',$_SESSION['timezone']); ?>"  />
		<span id="ttenddate_error" class="errors" style="display:none;"></span>
		 </div>
	    </li>
	    <li>
		<input type="button" name="search_total_details" onclick="dashboardTotalDetails();" value="<?php echo __('go'); ?>" title="<?php echo __('go'); ?>" >
	    </li>
	</ul>
	</div>
    </div>
<?php /** Dashboard total details count Start * */ ?>
    <div class="dashboard_widget_row">
	
	    <div class="dashboard_page_top_list">	
		<div class="dashboardTotalDetailsAjax"></div>
	    </div>
	
    </div>
    <div class="dashboard_widget_row">
	<div class="dashborad_widget_box lg-12">
	    <div class="dashboard_widget_title with_calender">
		<h2><?php echo __('live_dispatch'); ?></h2>
	    </div>
	    <div class="dashboard_map_outer" id="livedispatchAjax">
			<?php /* <img src="<?php echo IMGPATH; ?>dashboard_icons/dummy_map.jpg"/> */ ?>
	    </div>
	</div>    
    </div>
    <div class="dashboard_widget_row">
	<div class="dashborad_widget_box lg-12">
	    <div class="dashboard_widget_title with_calender">
		<h2><?php echo __('assigned_unassigned_asset_label'); ?></h2>
			<?php if ($_SESSION['company_id'] == 0) { ?>
				<div class="dashboard_calender_in_header with_select_box trip_req_from_city">
					<ul>
						<li>
							<div class="new_dash_calender_select">
								<select name="assign_unassign_company" id="assign_unassign_company">
									<?php
										if ($getAllCompany > 0) { ?>
										<optgroup label="<?php echo __('Multy'); ?>">
										<?php foreach ($getAllCompany as $au) { if($au['company_brand_type'] == "M") { ?>
											<option value="<?php echo $au['cid']; ?>"><?php echo ucfirst($au['company_name']); ?></option>
										<?php } } ?>
									</optgroup>
									<optgroup label="Single">
										<?php foreach ($getAllCompany as $au) { if($au['company_brand_type'] == "S") { ?>
											<option value="<?php echo $au['cid']; ?>"><?php echo ucfirst($au['company_name']); ?></option>
										<?php } } ?>
									</optgroup>
									<?php } else { ?>
									<option value=""><?php echo __('select_brand_label'); ?></option>
									<?php } ?>
								</select>
							</div>
						</li>
						<li>
							<input type="button" name="search_assign_unassign" onclick="assignUnassignedChart();" value="<?php echo __('go'); ?>" title="<?php echo __('go'); ?>" >
						</li>
					</ul>
				</div>
				<?php } else { ?>
					<input type="hidden" id="assign_unassign_company" name="assign_unassign_company" value="<?php echo $_SESSION['company_id']; ?>">
				<?php } ?>
	    </div>
	    <div class="dashboard_map_outer">
		<div class="assignedUnassignedChartAjax"></div>
	    </div>
	</div>	    
    </div>
    <div class="dashboard_widget_row company_wise_trip">
	   <?php if ($_SESSION['company_id'] == 0) { ?>
	<div class="dashborad_widget_box lg-6 company_trip">
	 
	    <div class="dashboard_widget_title with_calender">
    		<h2><?php echo __('company_wise_trip_label'); ?></h2>
    		<div class="dashboard_calender_in_header with_select_box">
    		    <ul>
    			
    			<li>
    			    <div class="new_dash_calender_input">
    				<input type="text" readonly title="<?php echo __('select_datetime'); ?>"  id="comapny_wise_startdate" name="comapny_wise_startdate" value="<?php echo date('Y-m-d 00:00:00', strtotime('-7 days')); ?>" />
    				<span id="comapny_wise_startdate_error" class="errors" style="display:none;"></span>
    			    </div>
    			</li>
    			<li>
    			    <div class="new_dash_calender_input">
    				<input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="company_wise_enddate" name="company_wise_enddate" value="<?php echo convert_timezone('now',$_SESSION['timezone']); ?>" />
    				<span id="company_wise_enddate_error" class="errors" style="display:none;"></span>
    			    </div>
    			</li>
    			<li>
    			    <input type="button" name="search_company_wise_trip" onclick="companyWiseTripChart();" value="<?php echo __('go'); ?>" title="<?php echo __('go'); ?>" >
    			</li>
    		    </ul>
    		</div>    		
    	    </div>
    	    <div class="dashboard_map_outer">
    		<div class="companyWiseTripChartAjax"></div>
    	    </div>
	    
	</div>	
	<?php } ?>
	<div class="dashborad_widget_box <?php if ($_SESSION['company_id'] == 0) { ?> lg-6 <?php } else { ?> lg-12 <?php } ?>">
	    <div class="dashboard_widget_title with_calender">
		<h2><?php echo ($_SESSION['company_id'] == 0) ? __('payment_by_company_label') : __('payment_label'); ?></h2>
		<div class="new_dash_calender_select pay_by_company" style="<?php if ($_SESSION['company_id'] > 0) { ?>display:none;<?php } ?> float:right;margin-top:-10px;">
				<select name="payment_company" >
				     <optgroup label="<?php echo __('Multy'); ?>">
					<option value=""><?php echo __('select_brand_label'); ?></option>
					<?php
						if ($getAllCompany > 0) {
						foreach ($getAllCompany as $pc) { if($pc['company_brand_type'] == "M") { ?>
							<option value="<?php echo $pc['cid']; ?>"><?php echo ucfirst($pc['company_name']); ?></option>
						<?php } } ?>
					</optgroup>
					<optgroup label="Single">
						<?php foreach ($getAllCompany as $pc) { if($pc['company_brand_type'] == "S") { ?>
							<option value="<?php echo $pc['cid']; ?>"><?php echo ucfirst($pc['company_name']); ?></option>
						<?php } } ?>
					</optgroup>
					<?php } ?>
				</select>
			    </div>
		<div class="dashboard_calender_in_header with_select_box payment_box payment_by_company" style="margin-top:11px;">
		    <ul>
			
			<li> 
			    <div class="new_dash_calender_input">
				<input type="text" readonly title="<?php echo __('select_datetime'); ?>"  id="payment_by_company_startdate" name="payment_by_company_startdate" value="<?php echo date('Y-m-d 00:00:00', strtotime('-7 days')); ?>" />
				<span id="payment_by_company_startdate_error" class="errors" style="display:none;"></span>
			    </div>
			</li>
			<li> 
			    <div class="new_dash_calender_input">
				<input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="payment_by_company_enddate" name="payment_by_company_enddate" value="<?php echo convert_timezone('now',$_SESSION['timezone']); ?>" />
				<span id="payment_by_company_enddate_error" class="errors" style="display:none;"></span>
			    </div>
			</li>
			<li>
			    <input type="button" name="search_payment_by_company" onclick="paymentByCompanyChart();" value="<?php echo __('go'); ?>" title="<?php echo __('go'); ?>" >
			</li>
		    </ul>
		</div>
		</div>
	    <div class="dashboard_map_outer">
		<div class="paymentByCompanyChartAjax"></div>
		
	    </div>
	</div>	
    </div>

 <?php /** City By Count Start * */ ?>
    <div class="dashboard_widget_row">
	<div class="dashborad_widget_box lg-12">
	    <div class="dashboard_widget_title with_calender">
		<h2><?php echo __('trip_request_from_city_label'); ?></h2>
		<div class="dashboard_calender_in_header with_select_box trip_req_from_city">
		    <ul>
			<li <?php if ($_SESSION['company_id'] > 0) { ?>style="display:none;"<?php } ?>><div class="new_dash_calender_select">
				<select name="city_by_company" >
			<optgroup label="<?php echo __('Multy'); ?>">
			    <option value=""><?php echo __('select_brand_label'); ?></option>
			    <?php
				if ($getAllCompany > 0) {
				foreach ($getAllCompany as $pc) { if($pc['company_brand_type'] == "M") { ?>
					<option value="<?php echo $pc['cid']; ?>"><?php echo ucfirst($pc['company_name']); ?></option>
				<?php } } ?>
			</optgroup>
			<optgroup label="Single">
				<?php foreach ($getAllCompany as $pc) { if($pc['company_brand_type'] == "S") { ?>
					<option value="<?php echo $pc['cid']; ?>"><?php echo ucfirst($pc['company_name']); ?></option>
				<?php } } ?>
			</optgroup>
			<?php } ?>
		    </select></div></li>
			<li><div class="new_dash_calender_input">
				 <input type="text" readonly title="<?php echo __('select_datetime'); ?>"  id="city_by_count_startdate" name="city_by_count_startdate" value="<?php echo date('Y-m-d 00:00:00', strtotime('-7 days')); ?>" />
		    <span id="city_by_count_startdate_error" class="errors" style="display:none;"></span>
			    </div> </li>
			<li><div class="new_dash_calender_input">
				  <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="city_by_count_enddate" name="city_by_count_enddate" value="<?php echo convert_timezone('now',$_SESSION['timezone']); ?>" />
		    <span id="city_by_count_enddate_error" class="errors" style="display:none;"></span>
			    </div> </li>
			    <li>
				<input type="button" name="search_city_by_count" onclick="cityByCountChart();" value="<?php echo __('go'); ?>" title="<?php echo __('go'); ?>" >
			    </li>
		    </ul>
		</div>
		
	    </div>
	    <div class="dashboard_map_outer">
		<div class="cityByCountChartAjax"></div>
	    </div>
	</div>	    
    </div>
 <?php /** City By Count End * */ ?>
<?php /** Company Revenue Graph Start * */ ?>
    <div class="dashboard_widget_row">
	<div class="dashborad_widget_box lg-12">
	    <div class="dashboard_widget_title with_calender">
		<h2><?php echo __('company_revenue_graph_label'); ?></h2>
		<div class="dashboard_calender_in_header with_select_box trip_req_from_city">
		    <ul>
			<li <?php if ($_SESSION['company_id'] > 0) { ?>style="display:none;"<?php } ?>><div class="new_dash_calender_select">
				<select name="company_revenue" >
				     <optgroup label="<?php echo __('Multy'); ?>">
					<option value=""><?php echo __('select_brand_label'); ?></option>
					<?php
						if ($getAllCompany > 0) {
						foreach ($getAllCompany as $pc) { if($pc['company_brand_type'] == "M") { ?>
							<option value="<?php echo $pc['cid']; ?>"><?php echo ucfirst($pc['company_name']); ?></option>
						<?php } } ?>
					</optgroup>
					<optgroup label="Single">
						<?php foreach ($getAllCompany as $pc) { if($pc['company_brand_type'] == "S") { ?>
							<option value="<?php echo $pc['cid']; ?>"><?php echo ucfirst($pc['company_name']); ?></option>
						<?php } } ?>
					</optgroup>
					<?php } ?>
				</select>
			    </div>
			</li>
			<li><div class="new_dash_calender_input"> <input type="text" readonly title="<?php echo __('select_datetime'); ?>"  id="company_revenue_startdate" name="company_revenue_startdate" value="<?php echo date('Y-m-d 00:00:00', strtotime('-7 days')); ?>" />
				<span id="company_revenue_startdate_error" class="errors" style="display:none;"></span></div></li>
			<li><div class="new_dash_calender_input"> <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="company_revenue_enddate" name="company_revenue_enddate" value="<?php echo convert_timezone('now',$_SESSION['timezone']); ?>" />
				<span id="company_revenue_enddate_error" class="errors" style="display:none;"></span></div></li>
			<li><input type="button" name="search_company_revenue" onclick="companyRevenueChart();" value="GO" title="Go" ></li>			    
		    </ul>
			
		</div>
	    <div class="dashboard_map_outer">
		<div class="companyRevenueChartAjax"></div>
	    </div>
	</div>	    
    </div>
<?php /** Company Revenue Graph End * */ ?>
<?php /** Driver Revenue Graph Start * */ ?>
    <div class="dashboard_widget_row">
	<div class="dashborad_widget_box lg-12">
	    <div class="dashboard_widget_title with_calender">
		<h2><?php echo __('driver_revenue_graph_label'); ?></h2>
		<div class="dashboard_calender_in_header with_select_box driver_revenue">
		    <ul>
			<li>
			    <div class="new_dash_calender_select">
				<select name="company_driver_revenue">
			<option value=""><?php echo __('select_driver_label'); ?></option>
			<?php if (count($driver_list) > 0) {
				
			    foreach ($driver_list as $dr) {
					if(isset($dr['name'])){
				?>
				<option value="<?php echo $dr['id']; ?>"><?php echo ucfirst($dr['name']); ?></option>
			    <?php } }
			}
			?>
		    </select>
			    </div>
			</li>
			<li><div class="new_dash_calender_input">
				 <input type="text" readonly title="<?php echo __('select_datetime'); ?>"  id="driver_revenue_startdate" name="driver_revenue_startdate" value="<?php echo date('Y-m-d 00:00:00', strtotime('-7 days')); ?>" />
		    <span id="driver_revenue_startdate_error" class="errors" style="display:none;"></span>
			    </div></li>
			<li><div class="new_dash_calender_input">
				   <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="driver_revenue_enddate" name="driver_revenue_enddate" value="<?php echo convert_timezone('now',$_SESSION['timezone']); ?>" />
		    <span id="driver_revenue_enddate_error" class="errors" style="display:none;"></span>
			    </div></li>
			<li><input type="button" name="search_driver_revenue" onclick="driverRevenueChart();" value="<?php echo __('go'); ?>" title="<?php echo __('go'); ?>" ></li>
		    </ul>
		</div>		
	    </div>
	    <div class="dashboard_map_outer">
		<div class="driverRevenueChartAjax"></div>
	    </div>
	</div>	    
    </div>



    <?php /** Driver Revenue Graph End * */ ?>
    
    <?php /** Company Count Start * */ ?>
    
    <?php /* <div class="widget chartWrapper">
      <div class="title">
      <img src="<?php echo IMGPATH; ?>/icons/dark/stats.png" alt="" class="titleIcon" />
      <h6><?php echo __('company_count_label'); ?></h6>
      <div class="title" align="right">
      <div class="one home_trip_rgt">
      <select name="company_count" style="width:150px;height:25px;">
      <optgroup label="Multy">
      <option value=""><?php echo __('select_brand_label'); ?></option>
      <?php if($getAllCompany > 0) { foreach($getAllCompany as $cc) { ?>
      <?php if(COMPANY_CID == 0) { ?>
      <option value="<?php echo $cc['cid']; ?>"><?php echo ucfirst($cc['company_name']); ?></option>
      <?php } else {
      if(COMPANY_CID == $cc['cid']) { ?>
      <option <?php if(COMPANY_CID == $cc['cid']) { ?>selected<?php } ?> value="<?php echo $cc['cid']; ?>"><?php echo ucfirst($cc['company_name']); ?></option>
      <?php } } ?>
      <?php } } ?>
      </optgroup>
      <optgroup label="Single">

      </optgroup>
      </select>
      <span><?php echo __('startdate');?></span>
      <input type="text" readonly title="<?php echo __('select_datetime'); ?>"  id="company_count_startdate" name="company_count_startdate" value="<?php echo Commonfunction::getDateTimeFormat(date('Y-m-01 00:00:00'),1); ?>" />
      <span id="company_count_startdate_error" class="errors" style="display:none;"></span>
      <span><?php echo __('enddate');?></span>
      <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="company_count_enddate" name="company_count_enddate" value="<?php echo Commonfunction::getDateTimeFormat(date('Y-m-d H:i:s'),1); ?>" />
      <span id="company_count_enddate_error" class="errors" style="display:none;"></span>
      <div class="button_new">
      <input type="button" name="search_company_count" onclick="companyCountChart();" value="GO" title="Go" >
      </div>
      </div>
      </div>
      </div>
      <div class="companyCountChartAjax"></div>
      </div> */ ?>
    <?php /** Company Count End * */ ?>

    <?php /** Trip Request From Top Cities Start * */ ?>
    <?php /* <div class="widget chartWrapper">
      <div class="title">
      <img src="<?php echo IMGPATH; ?>/icons/dark/stats.png" alt="" class="titleIcon" />
      <h6><?php echo __('trip_request_from_top_cities_label'); ?></h6>
      <div class="title" align="right">
      <div class="one home_trip_rgt">
      <select name="company_top_cities" style="width:150px;height:25px;">
      <optgroup label="Multy">
      <option value=""><?php echo __('select_brand_label'); ?></option>
      <?php if($getAllCompany > 0) { foreach($getAllCompany as $tc) { ?>
      <?php if(COMPANY_CID == 0) { ?>
      <option value="<?php echo $tc['cid']; ?>"><?php echo ucfirst($tc['company_name']); ?></option>
      <?php } else {
      if(COMPANY_CID == $tc['cid']) { ?>
      <option <?php if(COMPANY_CID == $tc['cid']) { ?>selected<?php } ?> value="<?php echo $tc['cid']; ?>"><?php echo ucfirst($tc['company_name']); ?></option>
      <?php } } ?>
      <?php } } ?>
      </optgroup>
      <optgroup label="Single">

      </optgroup>
      </select>
      <span><?php echo __('startdate');?></span>
      <input type="text" readonly title="<?php echo __('select_datetime'); ?>"  id="trip_req_top_cities_startdate" name="trip_req_top_cities_startdate" value="<?php echo Commonfunction::getDateTimeFormat(date('Y-m-01 00:00:00'),1); ?>" />
      <span id="trip_req_top_cities_startdate_error" class="errors" style="display:none;"></span>
      <span><?php echo __('enddate');?></span>
      <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="trip_req_top_cities_enddate" name="trip_req_top_cities_enddate" value="<?php echo Commonfunction::getDateTimeFormat(date('Y-m-d H:i:s'),1); ?>" />
      <span id="trip_req_top_cities_enddate_error" class="errors" style="display:none;"></span>
      <div class="button_new">
      <input type="button" name="search_trip_request_top_cities" onclick="tripReqTopCitiesChart();" value="GO" title="Go" >
      </div>
      </div>
      </div>
      </div>
      <div class="tripReqTopCitiesChartAjax"></div>
      </div> */ ?>
<?php /** Trip Request From Top Cities End * */ ?>
</div>
</div>
<script type="text/javascript" language="javascript">
    $(document).ready(function () {
		$("#total_data_startdate, #total_data_enddate, #assign_unassign_startdate, #assign_unassign_enddate, #comapny_wise_startdate, #company_wise_enddate, #payment_by_company_startdate,#payment_by_company_enddate,#company_count_startdate,#company_count_enddate,#trip_req_top_cities_startdate,#trip_req_top_cities_enddate,#city_by_count_startdate,#city_by_count_enddate,#company_revenue_startdate,#company_revenue_enddate,#driver_revenue_startdate,#driver_revenue_enddate").datetimepicker( {
			showTimepicker:DEFAULT_TIME_SHOW,
			showSecond: true,
			timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
			dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
			stepHour: 1,
			stepMinute: 1,
			maxDateTime : new Date(),
			stepSecond: 1
		});

		var COMPANY_CID = '<?php echo COMPANY_CID; ?>'
		dashboardTotalDetails();
		assignUnassignedChart();
		if(COMPANY_CID == 0) {
			companyWiseTripChart();
		}
		paymentByCompanyChart();
		//companyCountChart();
		//tripReqTopCitiesChart();
		cityByCountChart();
		companyRevenueChart();
		driverRevenueChart();
		liveDispatchMap();
		setInterval(function() {
			liveDispatchMap();
		}, 40000);
    });

    function dashboardTotalDetails()
    {
	var startdate = $("#total_data_startdate").val();
	var enddate = $("#total_data_enddate").val();
	var ses_company = $("#ses_company").val();
	var select_company = $("select[name=company]").val();
	var company = (select_company !="") ? select_company : ses_company;
	var label = $('select[name=company] :selected').parent().attr('label');
	if(company == "") { brand_filter = 0; } else if(label == "<?php echo __('Multy'); ?>") { brand_filter = 1; } else if(label == "Single") { brand_filter = 2; }

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
	    $("#ttenddate_error").hide("");
	    $("#ttenddate_error").hide();
	}

	if(startdate !='' && enddate!='') {
	    $('.dashboardTotalDetailsAjax').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
	    if(to_timestamp(startdate) > to_timestamp(enddate)) {
		$("#ttstartdate_error").html("<?php echo __('startdate_greater'); ?>");
		$("#ttstartdate_error").show();
	    } else {
		$("#ttstartdate_error").html("");
		$("#ttstartdate_error").hide();
		var dataS = "startdate="+startdate+"&enddate="+enddate+"&company="+company+"&brand_filter="+brand_filter;
		$.ajax ({
		    type: "POST",
		    url: SrcPath+"dashboard/dashboardTotalDetails",
		    data: dataS, 
		    cache: false, 
		    dataType: 'html',
		    success: function(response) 
		    {
			$('.dashboardTotalDetailsAjax').html(response);
		    } 
		});
	    }
	}
    }

	function assignUnassignedChart()
	{
		var select_company = $("#assign_unassign_company").val();
		$('.assignedUnassignedChartAjax').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');
		var data = "company="+select_company;
		$.ajax ({
			type: "POST",
			url: SrcPath+"dashboard/dashboardAssignUnassigenChart",
			cache: false, 
			dataType: 'html',
			data: data,
			success: function(response) 
			{
			$('.assignedUnassignedChartAjax').html(response);
			} 
		});
	}

    function companyWiseTripChart()
    {
	var startdate = $("#comapny_wise_startdate").val();
	var enddate = $("#company_wise_enddate").val();
	
	if(startdate =='') {
	    $("#comapny_wise_startdate_error").html("<?php echo __('select_startdate'); ?>");
	    $("#comapny_wise_startdate_error").show();
	} else {
	    $("#comapny_wise_startdate_error").html("");
	    $("#comapny_wise_startdate_error").hide();
	}

	if(enddate =='') {
	    $("#company_wise_enddate_error").html("<?php echo __('select_enddate'); ?>");
	    $("#company_wise_enddate_error").show();
	} else {
	    $("#company_wise_enddate_error").hide("");
	    $("#company_wise_enddate_error").hide();
	}

	if(startdate !='' && enddate!='') {
	    $('.companyWiseTripChartAjax').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
	    if(to_timestamp(startdate) > to_timestamp(enddate)) {
		$("#comapny_wise_startdate_error").html("<?php echo __('startdate_greater'); ?>");
		$("#comapny_wise_startdate_error").show();
	    } else {
		$("#company_wise_enddate_error").html("");
		$("#company_wise_enddate_error").hide();
		var dataS = "startdate="+startdate+"&enddate="+enddate;
		$.ajax ({
		    type: "POST",
		    url: SrcPath+"dashboard/dashboardCompanyWiseTripChart",
		    data: dataS, 
		    cache: false, 
		    dataType: 'html',
		    success: function(response) 
		    {
			$('.companyWiseTripChartAjax').html(response);
		    } 
		});
	    }
	}
    }

    function paymentByCompanyChart()
    {
	var startdate = $("#payment_by_company_startdate").val();
	var enddate = $("#payment_by_company_enddate").val();
	var ses_company = $("#ses_company").val();
	var select_company = $("select[name=payment_company]").val();
	var company = (select_company !="") ? select_company : ses_company;
	var label = $('select[name=payment_company] :selected').parent().attr('label');
	if(company == "") { brand_filter = 0; } else if(label == "<?php echo __('Multy'); ?>") { brand_filter = 1; } else if(label == "Single") { brand_filter = 2; }
	
	if(startdate =='') {
	    $("#payment_by_company_startdate_error").html("<?php echo __('select_startdate'); ?>");
	    $("#payment_by_company_startdate_error").show();
	} else {
	    $("#payment_by_company_startdate_error").html("");
	    $("#payment_by_company_startdate_error").hide();
	}

	if(enddate =='') {
	    $("#payment_by_company_enddate_error").html("<?php echo __('select_enddate'); ?>");
	    $("#payment_by_company_enddate_error").show();
	} else {
	    $("#payment_by_company_enddate_error").hide("");
	    $("#payment_by_company_enddate_error").hide();
	}

	if(startdate !='' && enddate!='') {
	    $('.paymentByCompanyChartAjax').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
	    if(to_timestamp(startdate) > to_timestamp(enddate)) {
		$(".dashboardpayment").hide();
		$("#payment_by_company_startdate_error").html("<?php echo __('startdate_greater'); ?>");
		$("#payment_by_company_startdate_error").show();
	    } else {
		$(".dashboardpayment").show();
		$("#payment_by_company_enddate_error").html("");
		$("#payment_by_company_enddate_error").hide();
		var dataS = "startdate="+startdate+"&enddate="+enddate+"&company="+company+"&brand_filter="+brand_filter;
		$.ajax ({
		    type: "POST",
		    url: SrcPath+"dashboard/dashboardPaymentByCompanyChart",
		    data: dataS, 
		    cache: false, 
		    dataType: 'html',
		    success: function(response) 
		    {
			$('.paymentByCompanyChartAjax').html(response);
		    } 
		});
	    }
	}
    }

    /* function companyCountChart()
{
	var startdate = $("#company_count_startdate").val();
	var enddate = $("#company_count_enddate").val();
	var select_company = $("select[name=company_count]").val();
	var company = (select_company !="") ? select_company : "";
	
	if(startdate =='') {
		$("#company_count_startdate_error").html("<?php echo __('select_startdate'); ?>");
		$("#company_count_startdate_error").show();
	} else {
		$("#company_count_startdate_error").html("");
		$("#company_count_startdate_error").hide();
	}

	if(enddate =='') {
		$("#company_count_enddate_error").html("<?php echo __('select_enddate'); ?>");
		$("#company_count_enddate_error").show();
	} else {
		$("#company_count_enddate_error").hide("");
		$("#company_count_enddate_error").hide();
	}

	if(startdate !='' && enddate!='') {
		$('.companyCountChartAjax').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
		if(to_timestamp(startdate) > to_timestamp(enddate)) {
			$("#company_count_startdate_error").html("<?php echo __('startdate_greater'); ?>");
			$("#company_count_startdate_error").show();
		} else {
			$("#company_count_enddate_error").html("");
			$("#company_count_enddate_error").hide();
			var dataS = "startdate="+startdate+"&enddate="+enddate+"&company="+company;
			$.ajax ({
				type: "POST",
				url: SrcPath+"dashboard/dashboardCompanyCountChart",
				data: dataS, 
				cache: false, 
				dataType: 'html',
				success: function(response) 
				{
					$('.companyCountChartAjax').html(response);
				} 
			});
		}
	}
} */

    /* function tripReqTopCitiesChart()
{
	var startdate = $("#trip_req_top_cities_startdate").val();
	var enddate = $("#trip_req_top_cities_enddate").val();
	var select_company = $("select[name=company_top_cities]").val();
	var company = (select_company !="") ? select_company : "";
	
	if(startdate =='') {
		$("#trip_req_top_cities_startdate_error").html("<?php echo __('select_startdate'); ?>");
		$("#trip_req_top_cities_startdate_error").show();
	} else {
		$("#trip_req_top_cities_startdate_error").html("");
		$("#trip_req_top_cities_startdate_error").hide();
	}

	if(enddate =='') {
		$("#trip_req_top_cities_enddate_error").html("<?php echo __('select_enddate'); ?>");
		$("#trip_req_top_cities_enddate_error").show();
	} else {
		$("#trip_req_top_cities_enddate_error").hide("");
		$("#trip_req_top_cities_enddate_error").hide();
	}

	if(startdate !='' && enddate!='') {
		$('.tripReqTopCitiesChartAjax').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
		if(to_timestamp(startdate) > to_timestamp(enddate)) {
			$("#trip_req_top_cities_startdate_error").html("<?php echo __('startdate_greater'); ?>");
			$("#trip_req_top_cities_startdate_error").show();
		} else {
			$("#trip_req_top_cities_enddate_error").html("");
			$("#trip_req_top_cities_enddate_error").hide();
			var dataS = "startdate="+startdate+"&enddate="+enddate+"&company="+company;
			$.ajax ({
				type: "POST",
				url: SrcPath+"dashboard/dashboardTripReqTopCitiesChart",
				data: dataS, 
				cache: false, 
				dataType: 'html',
				success: function(response) 
				{
					$('.tripReqTopCitiesChartAjax').html(response);
				} 
			});
		}
	}
} */

    function cityByCountChart()
    {
	var startdate = $("#city_by_count_startdate").val();
	var enddate = $("#city_by_count_enddate").val();
	var ses_company = $("#ses_company").val();
	var select_company = $("select[name=city_by_company]").val();
	var company = (select_company !="") ? select_company : ses_company;
	var label = $('select[name=city_by_company] :selected').parent().attr('label');
	if(company == "") { brand_filter = 0; } else if(label == "<?php echo __('Multy'); ?>") { brand_filter = 1; } else if(label == "Single") { brand_filter = 2; }
	
	if(startdate =='') {
	    $("#city_by_count_startdate_error").html("<?php echo __('select_startdate'); ?>");
	    $("#city_by_count_startdate_error").show();
	} else {
	    $("#city_by_count_startdate_error").html("");
	    $("#city_by_count_startdate_error").hide();
	}

	if(enddate =='') {
	    $("#city_by_count_enddate_error").html("<?php echo __('select_enddate'); ?>");
	    $("#city_by_count_enddate_error").show();
	} else {
	    $("#city_by_count_enddate_error").hide("");
	    $("#city_by_count_enddate_error").hide();
	}

	if(startdate !='' && enddate!='') {
	    $('.cityByCountChartAjax').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
	    if(to_timestamp(startdate) > to_timestamp(enddate)) {
		$("#city_by_count_startdate_error").html("<?php echo __('startdate_greater'); ?>");
		$("#city_by_count_startdate_error").show();
	    } else {
		$("#city_by_count_enddate_error").html("");
		$("#city_by_count_enddate_error").hide();
		var dataS = "startdate="+startdate+"&enddate="+enddate+"&company="+company+"&brand_filter="+brand_filter;
		$.ajax ({
		    type: "POST",
		    url: SrcPath+"dashboard/dashboardcityByCountChart",
		    data: dataS, 
		    cache: false, 
		    dataType: 'html',
		    success: function(response) 
		    {
			$('.cityByCountChartAjax').html(response);
		    } 
		});
	    }
	}
    }

	function companyRevenueChart()
	{
		var startdate = $("#company_revenue_startdate").val();
		var enddate = $("#company_revenue_enddate").val();
		var ses_company = $("#ses_company").val();
		var select_company = $("select[name=company_revenue]").val();
		var company = (select_company !="") ? select_company : ses_company;
		var label = $('select[name=company_revenue] :selected').parent().attr('label');
		if(company == "") { brand_filter = 0; } else if(label == "<?php echo __('Multy'); ?>") { brand_filter = 1; } else if(label == "Single") { brand_filter = 2; }
		
		if(startdate =='') {
			$("#company_revenue_startdate_error").html("<?php echo __('select_startdate'); ?>");
			$("#company_revenue_startdate_error").show();
		} else {
			$("#company_revenue_startdate_error").html("");
			$("#company_revenue_startdate_error").hide();
		}

		if(enddate =='') {
			$("#company_revenue_enddate_error").html("<?php echo __('select_enddate'); ?>");
			$("#company_revenue_enddate_error").show();
		} else {
			$("#company_revenue_enddate_error").hide("");
			$("#company_revenue_enddate_error").hide();
		}

		if(startdate !='' && enddate!='') {
			$('.companyRevenueChartAjax').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
			if(to_timestamp(startdate) > to_timestamp(enddate)) {
				$("#company_revenue_startdate_error").html("<?php echo __('startdate_greater'); ?>");
				$("#company_revenue_startdate_error").show();
			} else {
				$("#company_revenue_enddate_error").html("");
				$("#company_revenue_enddate_error").hide();
				var dataS = "startdate="+startdate+"&enddate="+enddate+"&company="+company+"&brand_filter="+brand_filter;
				$.ajax ({
					type: "POST",
					url: SrcPath+"dashboard/dashboardcompanyRevenueChart",
					data: dataS, 
					cache: false, 
					dataType: 'html',
					success: function(response) 
					{
						$('.companyRevenueChartAjax').html(response);
					} 
				});
			}
		}
    }

	function driverRevenueChart()
	{
		var startdate = $("#driver_revenue_startdate").val();
		var enddate = $("#driver_revenue_enddate").val();
		var ses_company = $("#ses_company").val();
		var select_company = $("select[name=company_driver_revenue]").val();
		var company = (select_company !="") ? select_company : ses_company;
		
		if(startdate =='') {
			$("#driver_revenue_startdate_error").html("<?php echo __('select_startdate'); ?>");
			$("#driver_revenue_startdate_error").show();
		} else {
			$("#driver_revenue_startdate_error").html("");
			$("#driver_revenue_startdate_error").hide();
		}

		if(enddate =='') {
			$("#driver_revenue_enddate_error").html("<?php echo __('select_enddate'); ?>");
			$("#driver_revenue_enddate_error").show();
		} else {
			$("#driver_revenue_enddate_error").hide("");
			$("#driver_revenue_enddate_error").hide();
		}

		if(startdate !='' && enddate!='') {
			$('.driverRevenueChartAjax').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
			if(to_timestamp(startdate) > to_timestamp(enddate)) {
			$("#driver_revenue_startdate_error").html("<?php echo __('startdate_greater'); ?>");
			$("#driver_revenue_startdate_error").show();
			} else {
			$("#driver_revenue_enddate_error").html("");
			$("#driver_revenue_enddate_error").hide();
			var dataS = "startdate="+startdate+"&enddate="+enddate+"&driver_id="+company;
			$.ajax ({
				type: "POST",
				url: SrcPath+"dashboard/dashboardDriverRevenueChart",
				data: dataS, 
				cache: false, 
				dataType: 'html',
				success: function(response) 
				{
				$('.driverRevenueChartAjax').html(response);
				} 
			});
			}
		}
    }
    
	function liveDispatchMap()
	{        
		$.ajax ({
			url: SrcPath+"dashboard/dashboardLiveDispatchMap",
			cache: false, 
			dataType: 'html',
			success: function(response) 
			{
				$('#livedispatchAjax').html(response);
			}
		});
	}
</script>

