<?php defined('SYSPATH') OR die("No direct access allowed.");?>
<?php
$startdate = date('Y-m-d 00:00:00'); 	
$enddate = date('Y-m-d H:i:s'); 	
?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<?php
$startdate = date('Y-m-d 00:00:00'); 	
$enddate = date('Y-m-d H:i:s');
$not_setting = "ON";

?>
<?php if(SHOW_MAP !=1 ) { ?>
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
      
    var iconBase = '<?php echo PUBLIC_IMGPATH.'/' ; ?>';
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

<!-- action tabs -->
 <div class="actions-wrapper">
				    <div class="actions">

				    	 <!--<div id="user-stats">

					       <ul class="round-buttons">

					            <li><div class="depth"><a href="<?php echo URL_BASE;?>transaction/admintransaction/all/" title="<?php echo __('transaction_details');?>" class="tip"><i class="icon-money"></i></a></div>
					            <span class="rapid_title"><?php echo __('transaction'); ?></span>
					           </li>
					            <li><div class="depth"><a href="<?php echo URL_BASE;?>add/company" title="<?php echo __('taxicompany');?>" class="tip"><i class="icon-trello"></i></a></div>
							<span class="rapid_title"><?php echo __('company'); ?></span>
							</li>
					            <li><div class="depth"><a href="<?php echo URL_BASE;?>add/admin" title="<?php echo __('superadmin_management');?>" class="tip"><i class="icon-user"></i></a></div>

					            <span class="rapid_title"><?php echo __('super_admin'); ?></span></li>

					            <li><div class="depth"><a href="<?php echo URL_BASE;?>admin/manage_site" title="<?php echo __('settings');?>" class="tip"><i class="icon-wrench"></i></a></div>
          					            <span class="rapid_title"><?php echo __('settings'); ?></span>
</li>
					        </ul>
				    	</div>-->

				    	<div id="quick-actions">
				    		<ul class="statistics">
				    			<li>
									<div class="blue_bg">
										<div class="top-info">
											<a href="<?php echo URL_BASE?>manage/managersearch?keyword=&status=A" title="<?php echo __(strtoupper('company managers'));?>" class="purple-square"></a>
											<strong><?php echo $admin_dashboard_data["manager"]; ?></strong>
										</div>
										<div class="progress progress-micro"><div class="bar" style="width: 90%;"></div></div>
									</div>
									<span class="rapid_title"><?php echo __(strtoupper('company managers'));?></span>
				    			</li>
				    			<li>
									<div class="green_bg">
										<div class="top-info">
											<a href="<?php echo URL_BASE?>manage/driversearch?keyword=&status=A" title="<?php echo __(strtoupper('Drivers'));?>" class="green-square"></a>
											<strong><?php echo $admin_dashboard_data["driver"]; ?></strong>
										</div>
										<div class="progress progress-micro"><div class="bar" style="width: 70%;"></div></div>
									</div>
									<span class="rapid_title"><?php echo __(strtoupper('Drivers'));?></span>
				    			</li>
				    			<li>
									<div class="orange_bg">
										<div class="top-info">
											<a href="<?php echo URL_BASE?>manage/taxisearch?keyword=&status=A" title="<?php echo __(strtoupper('taxis'));?>" class="sea-square"></a>
											<strong><?php echo $admin_dashboard_data["taxi"]; ?></strong>
										</div>
										<div class="progress progress-micro"><div class="bar" style="width: 50%;"></div></div>
									</div>
									<span class="rapid_title"><?php echo __(strtoupper('taxis'));?></span>
				    			</li>
				    		</ul>
				    	</div>

				    	<div id="map" class="comp_status">
							<ul class="dashboard_report_tab">
								<li>
				    				<div class="top-info rad b_red">
										<a href="<?php echo URL_BASE; ?>manage/today_unassigned_taxi" title="<?php echo __('unassigned_taxies'); ?>">
											<img src="<?php echo IMGPATH ?>/taxi.png" class="image" alt="<?php echo __('unassigned_taxies'); ?>"  />
										</a>
									</div>
											<span class="rapid_title">
											<?php
											if($freetaxi_list_count > 0)
											{
												echo $free_taxi = $freetaxi_list_count;
											}
											else
											{
												echo $free_taxi = 0;
											}
											?>
											<?php echo __('unassigned_taxies'); ?></span>

									
								</li>
							<li>
				    				<div class="top-info rad b_blue">
									<a href="<?php echo URL_BASE; ?>manage/freetaxi" title="<?php echo __('free_taxies_today'); ?>">
										<img src="<?php echo IMGPATH ?>/taxi.png" class="image" alt="<?php echo __('free_taxies_today'); ?>"  />
									</a>
									</div>
										<span class="rapid_title">
										<?php
										if($availabletaxi_list_count > 0)
										{
											echo $avai_taxi_list = $availabletaxi_list_count;
										}
										else
										{
											echo $avai_taxi_list = 0;
										}
										?>
										<?php echo __('free_taxies_today'); ?>
										</span>
									
								</li>
								<li>
				    				<div class="top-info rad b_green">
										<a href="<?php echo URL_BASE; ?>manage/unassign_driver" title="<?php echo __('unass_drivers_today'); ?>">
											<img src="<?php echo IMGPATH ?>/driver.png" class="image" alt="<?php echo __('unass_drivers_today'); ?>"  />

									</a>
									</div>
											<span class="rapid_title">
											<?php
											if($freedriver_list_count > 0)
											{
												echo $freedriver = $freedriver_list_count;
											}
											else
											{
												echo $freedriver = 0;
											}
											?>
											<?php echo __('unass_drivers_today'); ?>
											</span>
										
								</li>
								<li>
				    				<div class="top-info rad b_orange">
									<a href="<?php echo URL_BASE; ?>manageusers/live_users" title="<?php echo __('live_passengers'); ?>">
										<img src="<?php echo IMGPATH ?>/manager.png" class="image" alt="<?php echo __('live_passengers'); ?>"  />
									</a>
									</div>
										<span class="rapid_title">
										<?php
										if($activeusers_list_count > 0)
										{
											echo $activeusers = $activeusers_list_count;
										}
										else
										{
											echo $activeusers = 0;
										}
										?>
										<?php echo __('live_passengers'); ?>
										</span>
									
								</li>
							</ul>
				    	</div>

				    	<ul class="action-tabs comp_action_tabs">
						<li><a href="#quick-actions" title=""><?php echo __('Statistics');?></a></li>
						<!--<li><a href="#user-stats" title=""><?php echo __('quick_access');?></a></li>-->
				    	<li><a href="#map" title="" id="map-link"><?php echo __('reports');?></a></li>
				    	</ul>
				    </div>
				</div>
			    <!-- /action tabs -->
			    



<form  action="<?php echo URL_BASE;?>company/dashboard" method="post" name="dashboard" id="dashboard" >
<!-- Company Total Trip Details Start-->
	<div class="widget chartWrapper">
		<div class="title">
			<img src="<?php echo IMGPATH; ?>/icons/dark/stats.png" alt="" class="titleIcon" />
			<h6><?php echo __('total_trip_details'); ?></h6>
			<div class="title" align="right">
				<div class="one">
					 <span><?php echo __('startdate');?></span>
					 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="company_trips_startdate" name="company_trips_startdate" value="<?php echo $startdate; //Commonfunction::getDateTimeFormat($startdate,1); ?>"  />
					 <span id="company_trips_startdate_error" class="errors" style="display:none;"></span>
					 <span><?php echo __('enddate');?></span>
					 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="company_trips_enddate" name="company_trips_enddate" value="<?php echo $enddate; //echo Commonfunction::getDateTimeFormat($enddate,1); ?>"  />
					 <span id="company_trips_enddate_error" class="errors" style="display:none;"></span>
					 <div class="button blackB"> 
						<input type="button" name="company_search_total_trips" id="company_search_total_trips" value="GO" title="Go" >
					 </div>
				</div>
			</div>
		</div>
		<div class="body" id="company_total_trips_details">
			<?php //if($display == 'display:none;'){ echo "<div class='no_data'>".__('no_data')."</div>"; } ?>
			<div id="company_trips_details" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
		</div>
	</div>
<!-- Company Total Trip Details End-->

<!--Dashboard Current Status of The Driver -->
	<div class="widget margin-bottom" >
		<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo __('Current Status of Driver'); ?></h6>
			<div class="title" align="right">
				<div class="one">
					<select name="select_driver_status" id="select_driver_status" style="width:150px;height:25px;">
						<option value=""><?php echo __('all_label'); ?></option>
							<option value="A">Active</option>
							<option value="F">Free</option>
							<option value="B">Busy</option>
							<option value="OUT">Free and Shiftout</option>
					</select>

					 <div class="button blackB"> 
						<input type="button" name="search_driver_status" id="search_driver_status" onclick="dashboard_map()" value="GO" title="Go" >
					 </div>
				</div>
			</div>
			<!--<div style="width:auto; float:right; margin: 2px 2px;">		
			<div class="title"><?php echo __('notification_setting')." : ";?><span class='btn btn-mini btn-primary'><?php echo $not_setting;?></span></div>
			</div>-->
		</div>
		<div id="on_going_trip_map" >
			<div class="ongoing">
				<div id="on_going_trip"></div>
				<div id="on_going_place"></div>
			</div>
			<?php if(SHOW_MAP !=1) { ?>
				<div id="map-canvas" style="width:100%;height:500px;"></div>
			<?php } ?>
		</div>
	</div>
<!--Dashboard Current Status of The Driver -->

<?php /*
        <div class="widget chartWrapper">
            <div class="title"><img src="<?php echo IMGPATH; ?>/icons/dark/stats.png" alt="" class="titleIcon" /><h6><?php echo __('companychart'); ?></h6>
            <div class="title" align="right">Startdate<input type="text" style="margin-top:4px;" readonly title="<?php echo __('select_datetime'); ?>"  id="userstartdate" name="userstartdate" value="<?php if(isset($postvalue) && array_key_exists('userstartdate',$postvalue)){ echo $postvalue['userstartdate']; }?>"  />
            <span id="startdate_error" class="errors" style="display:none;"></span>
        Enddate<input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="userenddate" name="userenddate" value="<?php if(isset($postvalue) && array_key_exists('userenddate',$postvalue)){ echo $postvalue['userenddate']; }?>"  />
        
        <span id="enddate_error" class="errors" style="display:none;"></span>
        <div class="button blackB"> 
        <input type="button" name="change_usercompany" id="change_usercompany" value="GO" title="Go" >
        </div></div>
        
        </div>
            <div class="body">
		<?php if(($dashboard_driverdetails !='') || ($dashboard_managerdetails !='') || ($dashboard_taxidetails !='')){ ?> 
			<div class="chart" id="company_details" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
			<?php }else{ 
					echo "No Data Found!";
			} ?>		
		
			</div>
        </div>
*/ ?>
<!-- Manager Taxies widget -->
<div class="widget" >
    <div class="title"><img src="<?php echo IMGPATH; ?>/icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo __('manager_management'); ?></h6></div>
    <table cellpadding="0" cellspacing="0" width="100%" class="sTable">
        <thead>
            <tr>
                <td width="10%"><?php echo __('manager_name'); ?></td>
                <td width="10%"><?php echo __('company_name'); ?></td>
                <td width="10%"><?php echo __('driver_phone_number'); ?></td>
                <td width="10%"><?php echo __('country_label'); ?></td>
                <td width="10%"><?php echo __('state_label'); ?></td>
                <td width="10%"><?php echo __('city_label'); ?></td>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($manager_list) && count($manager_list) > 0) { ?>
	<?php foreach($manager_list as $managerlist){ ?>
            <tr>
                <td align="center"><a href="<?php echo URL_BASE;?>manage/managerdetails/<?php echo $managerlist['id']; ?>" title="" ><?php echo $managerlist['name'];?></a></td>
                <td align="center"><a href="<?php echo URL_BASE;?>manage/companydetails/<?php echo $managerlist['userid']; ?>" title="" >
                <?php /* if(file_exists($_SERVER["DOCUMENT_ROOT"].'/public/uploads/company/'.$managerlist['userid'].'.png')){  ?> 
		<img   width="32" height="32" src="<?php echo URL_BASE.COMPANY_IMG_IMGPATH.$managerlist['userid'].'.png';?>"/>
		<?php }else{ ?>
		<img width="32" height="32"  src="<?php echo URL_BASE;?>public/common/images/company_noimage.png"/>
		<?php }  */ ?>
		
                <?php echo $managerlist['company_name'];?></a></td>
                <td align="center"><?php echo $managerlist['phone'];?></td>
                <td align="center"><?php echo $managerlist['country_name'];?></td>
                <td align="center"><?php echo $managerlist['state_name'];?></td>
                <td align="center"><?php echo $managerlist['city_name'];?></td>
            </tr>
        <?php } 
        }
        else
        { ?>
        <tr><td colspan="4" align="center"> <?php echo __('no_data'); ?></td> </tr>
        <?php } ?>
            
        </tbody>
    </table>   
</div>

<!-- Available Taxies widget -->
<div class="widget" >
    <div class="title"><img src="<?php echo IMGPATH; ?>/icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo __('free_taxi'); ?></h6></div>
    <table cellpadding="0" cellspacing="0" width="100%" class="sTable">
        <thead>
            <tr>
                <td width="80"><?php echo __('taxi_no'); ?></td>
                <td width="80"><?php echo __('company_name'); ?></td>
                <td width="80"><?php echo __('driver_name'); ?></td>
                <td width="80"><?php echo __('driver_phone_number'); ?></td>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($availabletaxi_list) && count($availabletaxi_list) > 0) { ?>
	<?php foreach($availabletaxi_list as $availabletaxilist){ ?>
            <tr>
                <td align="center"><a href="<?php echo URL_BASE;?>manage/taxiinfo/<?php echo $availabletaxilist['taxi_id']; ?>" title="" ><?php echo $availabletaxilist['taxi_no'];?></a></td>
                <td align="center"><a href="<?php echo URL_BASE;?>manage/companydetails/<?php echo $availabletaxilist['userid']; ?>" title="" >
                <?php /* if(file_exists($_SERVER["DOCUMENT_ROOT"].'/public/uploads/company/'.$availabletaxilist['userid'].'.png')){  ?> 
		<img   width="32" height="32" src="<?php echo URL_BASE.COMPANY_IMG_IMGPATH.$availabletaxilist['userid'].'.png';?>"/>
		<?php }else{ ?>
		<img width="32" height="32"  src="<?php echo URL_BASE;?>public/common/images/company_noimage.png"/>
		<?php }  */ ?>                
                <?php echo $availabletaxilist['company_name'];?></a></td>
                <td align="center"><a href="<?php echo URL_BASE;?>manage/driverinfo/<?php echo $availabletaxilist['driver_id']; ?>" title="" ><?php echo $availabletaxilist['name'];?></a></td>
                <td align="center"><?php echo $availabletaxilist['phone'];?></td>
            </tr>
        <?php } 
        }
        else
        { ?>
        <tr><td colspan="4" align="center"> <?php echo __('no_data'); ?></td> </tr>
        <?php } ?>
            
        </tbody>
    </table>   
</div>

<!-- Free Taxies widget -->
<div class="widget widget_company_dashboard">
    <div class="title"><img src="<?php echo IMGPATH; ?>/icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo __('free_taxies'); ?></h6></div>
    <table cellpadding="0" cellspacing="0" width="100%" class="sTable">
        <thead>
            <tr>
                <td width="80"><?php echo __('taxi_no'); ?></td>
                <td width="80"><?php echo __('company_name'); ?></td>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($freetaxi_list) && count($freetaxi_list) > 0) { ?>
	<?php foreach($freetaxi_list as $freetaxilist){ ?>
            <tr>
                <td align="center"><a href="<?php echo URL_BASE;?>manage/taxiinfo/<?php echo $freetaxilist['taxi_id']; ?>" title="" ><?php echo $freetaxilist['taxi_no'];?></a></td>
                <td align="center"><a href="<?php echo URL_BASE;?>manage/companydetails/<?php echo $freetaxilist['userid']; ?>" title="" >
                <?php /* if(file_exists($_SERVER["DOCUMENT_ROOT"].'/public/uploads/company/'.$freetaxilist['userid'].'.png')){  ?> 
		<img   width="32" height="32" src="<?php echo URL_BASE.COMPANY_IMG_IMGPATH.$freetaxilist['userid'].'.png';?>"/>
		<?php }else{ ?>
		<img width="32" height="32"  src="<?php echo URL_BASE;?>public/common/images/company_noimage.png"/>
		<?php } */ ?>                
                <?php echo $freetaxilist['company_name'];?></a></td>
            </tr>
        <?php } 
        }
        else
        { ?>
        <tr><td colspan="4" align="center"> <?php echo __('no_data'); ?></td> </tr>
        <?php } ?>
            
        </tbody>
    </table>   
</div>
<?php if($freetaxi_list_count>10){ ?>
 <div style="float:right;margin-top:5px;"><a href="<?php echo URL_BASE; ?>manage/today_unassigned_taxi"><b>View More</b></a></div> 
 <?php } ?>
 
<!-- Free Drivers widget -->
<div class="widget widget_company_dashboard widget_company_rgt">
    <div class="title"><img src="<?php echo IMGPATH; ?>/icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo __('free_drivers'); ?></h6></div>
    <table cellpadding="0" cellspacing="0" width="100%" class="sTable">
        <thead>
            <tr>
                <td width="80"><?php echo __('driver_name'); ?></td>
                <td width="80"><?php echo __('company_name'); ?></td>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($freedriver_list) && count($freedriver_list) > 0) { ?>
	<?php foreach($freedriver_list as $freedriverlist){ ?>
            <tr>
                <td align="center"><a href="<?php echo URL_BASE;?>manage/driverinfo/<?php echo $freedriverlist['id']; ?>" title="" ><?php echo $freedriverlist['name'];?></a></td>
                <td align="center"><a href="<?php echo URL_BASE;?>manage/companydetails/<?php echo $freedriverlist['userid']; ?>" title="" >
                <?php /* if(file_exists($_SERVER["DOCUMENT_ROOT"].'/public/uploads/company/'.$freedriverlist['userid'].'.png')){  ?> 
		<img   width="32" height="32" src="<?php echo URL_BASE.COMPANY_IMG_IMGPATH.$freedriverlist['userid'].'.png';?>"/>
		<?php }else{ ?>
		<img width="32" height="32"  src="<?php echo URL_BASE;?>public/common/images/company_noimage.png"/>
		<?php } */ ?>                       
                <?php echo $freedriverlist['company_name'];?></a></td>
            </tr>
        <?php } 
        }
        else
        { ?>
        <tr><td colspan="4" align="center"> <?php echo __('no_data'); ?></td> </tr>
        <?php } ?>
            
        </tbody>
    </table>   
</div>
<?php if($freedriver_list_count>10){ ?>
<div style="float:right;margin-top:5px;"><a href="<?php echo URL_BASE; ?>manage/unassign_driver"><b>View More</b></a></div> 
 <?php } ?>

<!-- Latest Updates -->
 <!-- <div class="widget" >
<div class="title"><img src="<?php echo IMGPATH; ?>/icons/dark/add.png" alt="" class="titleIcon" /><h6><?php echo __('Latest Updates'); ?></h6></div>
    <table cellpadding="0" cellspacing="0" width="100%" class="sTable">
        <thead>
        </thead>
        <tbody>
           <tr>
                <td align="center"><?php //echo __('comp_tot_bal'); ?></td>
                <td align="center"><?php //echo __(CURRENCY); ?><?php //echo $company_tot_current_bal[0]['account_balance'];?></td>
            </tr> 
             <tr>
                <td align="center"><?php //echo __('admin_tot_bal'); ?></td>
                <td align="center"><?php //echo __(CURRENCY); ?><?php //echo $admin_tot_current_bal[0]['account_balance'];?></td>
            </tr>  
        </tbody>
    </table>   
</div> -->
</form>
<script>
$(document).ready(function () {

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


});
$(function () {
  <?php  if(($dashboard_driverdetails !='') || ($dashboard_managerdetails !='') || ($dashboard_taxidetails !='')){ 
  	$result ='';
  	if($dashboard_driverdetails !='')
  	{
  		$result .= $dashboard_driverdetails.',';
  	}
  	if($dashboard_managerdetails !='')
  	{
  		$result .= $dashboard_managerdetails.',';
  	}
  	if($dashboard_taxidetails !='')
  	{
  		$result .= $dashboard_taxidetails;
  	}
  
  ?>   	
		// Build the chart
		/*
        $('#company_details').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Company Details'
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
                name: 'Count',
                data: [
			<?php echo $result; ?>
                ]
            }]
        });
        */
 <?php } ?>       
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
 
</script>
<script src="<?php echo SCRIPTPATH; ?>highcharts.js"></script>
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
 <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY0kkJiTPVd2U7aTOAwhc9ySH6oHxOIYM&amp;sensor=false"></script> -->
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/plugins/jquery.easytabs.min.js"></script>

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
		chart2 = new Highcharts.Chart({
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
				text: "<?php echo __('for_label') . ' ' . date ('Y'); ?>",
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
					format: '{value} <?php echo COMPANY_CURRENCY; ?>',
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
					valueSuffix: ' <?php echo COMPANY_CURRENCY; ?>'
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
				url: '<?php echo URL_BASE; ?>company/get_company_trip_count',
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
							
							//console.log(key+":"+value['trips']);
							//console.log(key+":"+value['revenues']);
						});
					}
				}
			});
		}
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
				$.ajax({
					type: "POST",
					url: SrcPath+"company/total_trip_details_search", 
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
		$a=0;
		$b=5 ;
		if(count($all_company_map_list) > 0)
		{		
			 foreach($all_company_map_list as $v)
			{ 
				for($b=0;$b<5;$b++)
				{
					if($b==0)
					{  ?>
						markers [<?php echo $a; ?>] = new Array(4);
						markers[<?php echo $a; ?>][<?php echo $b; ?>]=<?php echo $v['latitude']; ?>;
					<?php }
					if($b==1)
					{ ?>
						markers[<?php echo $a; ?>][<?php echo $b; ?>]=<?php echo $v['longitude']; ?>;
					<?php }
					if($b==2)
					{ ?>
						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo '<div class="info_content"><b>'.__('driver_name').'</b> : '.$v['name']; ?>';
					<?php }
					if($b==3)
					{ $driver_status =($v['driver_status']=='F' && $v['shift_status']=='IN')?__('free_in'):(($v['driver_status']=='A')?"<span>".__('hired')."</span>":(($v['driver_status']=='B')?"<span>".__('trip_assigned')."</span>":__('free_out')));
					$txtcolor =($v['driver_status']=='F' && $v['shift_status']=='IN')? 'green':(($v['driver_status']=='A')? '#07841E':(($v['driver_status']=='B')? 'red':'#0F9ED6')); ?>
						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo '<div id="bodyContent"><p><b>'.__('driver_status').'</b>: <b style="color:'.$txtcolor.';">'.$driver_status.'</b></p></div></div>'; ?>';
					<?php }
					/*if($b==4)
					{ $shift_status=($v['shift_status']=='IN')?__('in'):__('out');?>
						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo '<b style="color:#0F9ED6;">'.$shift_status.'</b></p></div></div>'; ?>';
					<?php } */
					if($b==4){
					if($v['driver_status']=='F' && $v['shift_status']=='OUT'){ ?>
						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo PUBLIC_IMGPATH.'/driver_four.png'; ?>';
					<?php }elseif($v['driver_status']=='A'){ ?>
						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo PUBLIC_IMGPATH.'/driver_two.png'; ?>';
					<?php }else if($v['driver_status']=='B'){ ?>
						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo PUBLIC_IMGPATH.'/driver_one.png'; ?>';
					<?php }else if($v['driver_status']=='F' && $v['shift_status']=='IN'){ ?>
						markers[<?php echo $a; ?>][<?php echo $b; ?>]='<?php echo PUBLIC_IMGPATH.'/driver_three.png'; ?>';
					<?php }
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
			var iconBase = '<?php echo PUBLIC_IMGPATH.'/' ; ?>';
				   
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
				;
				// Automatically center the map fitting all markers on the screen
				map.fitBounds(bounds);
				
				
			}
		}
		else
		{
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
							var iconBase = '<?php echo PUBLIC_IMGPATH.'/' ; ?>';
							
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

