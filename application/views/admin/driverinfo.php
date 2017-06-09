<?php defined('SYSPATH') OR die("No direct access allowed.");?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>"></script> 
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<?php
$notification_setting = "";
foreach($driver_profile as $result){
	$driver_name = $result['name'];
	$notification_setting = $result['notification_setting'];
	$company_id = $result['company_id'];
}
$company_currency = CURRENCY_SYMB;//CURRENCY;//findcompany_currency($company_id);
if($notification_setting == '1')
{
	$not_setting = "ON";
}
else
{
	$not_setting = "OFF";
}

foreach($driver_profile as $result){
	$driver_name = $result['name'];
}

if($get_transaction){
	  $fare = array();
	  $month = array();
	foreach($get_transaction as $vl)
	{
		if($vl['fare'] != NULL){
			$fare[] = $vl['fare'];
			$month[] = "'".$vl['date']." ".$vl['month']."'";
		}
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
	$display ="display:none;";
}
?>
<?php 
if((!empty($get_trip_statitics['completed_trips']))||(!empty($get_trip_statitics['rejected_trips']))|| (!empty($get_trip_statitics['cancelled_trips']))){
	  $createdate = array();
	  $reject_trips = array();
	  $cancelled_trips = array();
	  $completed_trips = array();
	  $display_trip ='';
	  $a=0;
	  $b=0;
	  $date_conv='';
	$end=(date('M-d'));
	while($a<=7)
	{
		$end=date('M-d', mktime(0, 0, 0, date("m") , date("d")-$a, date("Y")));
		$createdate[]= "'$end'";
		$a++;
	}
	while($b < count($get_trip_statitics['cancelled_trips']))
	{
	if(isset($get_trip_statitics['cancelled_trips'][$b]['cancelled_count']))
	{
		foreach($createdate as $ct)
		{
			$date_conv=date('M-d',strtotime($get_trip_statitics['cancelled_trips'][$b]['createdate']));
			$ct = str_replace("'","",$ct);
			if($ct == $date_conv)
			{
				$cancelled_trips[]=$get_trip_statitics['cancelled_trips'][$b]['cancelled_count'];
			}
			else
			{
				//$cancelled_trips[]=0;
			}
		}
	}
	else
	{
		//$cancelled_trips[]=0;
	}
	$b++;
	}
	$b=0;
	while($b < count($get_trip_statitics['rejected_trips']))
	{
	if(isset($get_trip_statitics['rejected_trips'][$b]['rejected_count']))
	{
		foreach($createdate as $ct)
		{
			$date_conv=date('M-d',strtotime($get_trip_statitics['rejected_trips'][$b]['createdate']));
			$ct = str_replace("'","",$ct);
			if($ct == $date_conv)
			{
				$reject_trips[]=$get_trip_statitics['rejected_trips'][$b]['rejected_count'];
			}
			else
			{
				//$reject_trips[]=0;
			}
		}
	}
	else
	{
		//$reject_trips[]=0;
	}
	$b++;
	}
	$b=0;
	while($b < count($get_trip_statitics['completed_trips']))
	{
	if(isset($get_trip_statitics['completed_trips'][$b]['completed_count']))
	{
		foreach($createdate as $ct)
		{
			$date_conv=date('M-d',strtotime($get_trip_statitics['completed_trips'][$b]['createdate']));
			$ct = str_replace("'","",$ct);
			if($ct == $date_conv)
			{
				$completed_trips[]=$get_trip_statitics['completed_trips'][$b]['completed_count'];
			}
			else
			{
				//$completed_trips[]=0;
			}
		}
	}
	else
	{
		//$completed_trips[]=0;
	}
	$b++;
	}
	  $reject_trips = implode(",",$reject_trips);
	  $cancelled_trips = implode(",",$cancelled_trips);
	  $completed_trips = implode(",",$completed_trips);
	  $createdate=implode(",",$createdate);

	$display_trip ="display:block;";
}else{
	$createdate = array();
	$reject_trips = array();
	$cancelled_trips = array();
	$completed_trips = array();
	$display_trip ="display:none;";
}
?>
<?php if(SHOW_MAP !=1 ) { ?>
<script>	
// Enable the visual refresh
google.maps.visualRefresh = true;

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
          $('#on_going_place').html("<?php echo __('my_current_location'); ?> : "+results[1].formatted_address);
         }
      }			
      else if (status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
				  setTimeout(function() {
						codeLatLng(lat,lng,id);
				  }, 200); 
	  }
	else {
			  alert('<?php echo __("gecoder_failed"); ?> ' + status);
			  attempts = 0;
	}
   });
   
   
        
}
</script>
<?php } ?>
<script type="text/javascript">
push_notification('<?php echo $user_details1[0]['id']; ?>');
setInterval(function() 
{
	push_notification('<?php echo $user_details1[0]['id']; ?>');
	   
}, 60000);


function push_notification(driver_id)
{
	
	var dataS = "&type=1&driver_id="+driver_id;
	var SrcPath = "<?php echo URL_BASE; ?>";
	var response;
	$.ajax
	({ 			
		type: "POST",
		//url: SrcPath+"driver/adminpush_notification/1", 
                url: SrcPath+"manage/adminpush_notification/1", 
		data: dataS, 
		cache: false, 
		dataType: 'html',
		success: function(response) 
		{ 	
			var test_str = response;
			var start_pos = test_str.indexOf('|') + 1;
			var end_pos = test_str.indexOf('|',start_pos);
			var text_to_get = test_str.substring(start_pos,end_pos)
			
			if(text_to_get.length > 1){				
				response = response.substr(text_to_get.length+4);
				$('#on_going_trip_btn').html(text_to_get);
				loadPage();
				console.log(text_to_get);
			}
		
			$('#on_going_trip').html(response);
			var lat = $('#latitude').val();
			var lng = $('#longitude').val();
			
			var show_map = "<?php echo SHOW_MAP; ?>";

			if(show_map !=1)
			{
				 
				showPosition(lat,lng);
				var latlng = new google.maps.LatLng(lat,lng);
				geocoder = new google.maps.Geocoder();
				geocoder.geocode({'latLng': latlng}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
					if (results[1]) {         
						$('#on_going_place').html("<?php echo __('my_current_location'); ?> : "+results[1].formatted_address);
						}
						}			
						else if (status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
						setTimeout(function() {
						codeLatLng(lat,lng,id);
						}, 200);
					}
					else {
					alert('<?php echo __("gecoder_failed"); ?> ' + status);
					attempts = 0;
					}
				});
			}
			else
			{
				
			}
		} 
		 
	});	
}


</script>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">
           <div class="driverinfo_common">
			<h2 class="tab_sub_tit"><?php echo ucfirst(__('personalinform')); ?></h2>
                        <ul>	
			   <li><label><?php echo __('firstname'); ?></label>
                               <p><?php if(isset($user_details1[0]['name'])) { echo ucfirst($user_details1[0]['name']); } else { echo ''; } ?></p>
				   </li> 	
			<?php if(isset($user_details1[0]['lastname']) && !empty($user_details1[0]['lastname'])) { ?>		   
			   <li><label><?php echo __('lastname'); ?></label>
                               <p><?php if(isset($user_details1[0]['lastname'])) { echo $user_details1[0]['lastname']; } ?></p>
				   </li> 
			   <?php } ?>
			   
			   <?php if(isset($user_details1[0]['email']) && !empty($user_details1[0]['email'])) { ?>
			   <li><label><?php echo __('email'); ?></label>
                               <p><?php if(isset($user_details1[0]['email'])) { echo $user_details1[0]['email']; } ?></p>
				   </li> 
			    <?php } ?>
			    
			   <?php if(isset($user_details1[0]['gender']) && !empty($user_details1[0]['gender'])) { ?>
			   <li><label><?php echo __('gender'); ?></label>
                               <p><?php if(isset($user_details1[0]['gender'])) { echo $user_details1[0]['gender']; } ?></p>	
				   </li>
			   <?php } ?>
			   
			   <?php if(isset($user_details1[0]['phone']) && !empty($user_details1[0]['phone'])) { ?>
			   <li><label><?php echo __('mobile'); ?></label>
                               <p><?php if(isset($user_details1[0]['phone'])) { echo $user_details1[0]['phone']; } ?></p>	
				   </li>  
			   <?php } ?>                     		   
			 
			 <?php if(isset($user_details1[0]['address']) && !empty($user_details1[0]['address'])) { ?>  
			   <li><label><?php echo __('address'); ?></label>
                               <p><?php if(isset($user_details1[0]['address'])) { echo $user_details1[0]['address']; } ?></p>
                            </li>
			  <?php } ?>
			  
			 <?php if(isset($user_details1[0]['dob']) && $user_details1[0]['dob'] != '0000-00-00' ) { ?>             
			<li><label><?php echo __('date_of_birth'); ?></label>
                            <p><?php if(isset($user_details1[0]['dob'])) { echo Commonfunction::getDateTimeFormat($user_details1[0]['dob'],2); } ?></p>
				   </li> 
			<?php } ?>
			
			 <?php if($user_details1[0]['user_type'] != 'N') { ?>  
			 <?php if(isset($user_details1[0]['driver_license_id']) && !empty($user_details1[0]['driver_license_id'])) { ?> 
			<li><label><?php echo __('driver_license_id'); ?></label>
                            <p><?php if(isset($user_details1[0]['driver_license_id'])) { echo $user_details1[0]['driver_license_id']; } ?></p>
				   </li> 
			<?php } ?>
			
			 <?php if(isset($user_details1[0]['driver_license_expire_date']) && !empty($user_details1[0]['driver_license_expire_date'])) { ?> 
			<li><label><?php echo __('driver_license_expire_date'); ?></label>
                            <p><?php if(isset($user_details1[0]['driver_license_expire_date'])) { echo Commonfunction::getDateTimeFormat($user_details1[0]['driver_license_expire_date'],2); } ?></p>
				   </li> 
			<?php } ?>
			
			 <?php if(isset($user_details1[0]['driver_pco_license_number']) && !empty($user_details1[0]['driver_pco_license_number'])) { ?> 			
			<li><label><?php echo __('driver_pco_license_number'); ?></label>
                            <p><?php if(isset($user_details1[0]['driver_pco_license_number'])) { echo $user_details1[0]['driver_pco_license_number']; } ?></p>
				   </li> 
			<?php } ?>
			
			 <?php if(isset($user_details1[0]['driver_pco_license_expire_date']) && !empty($user_details1[0]['driver_pco_license_expire_date'])) { ?> 
			<li><label><?php echo __('driver_pco_license_expire_date'); ?></label>
                            <p><?php if(isset($user_details1[0]['driver_pco_license_expire_date'])) { echo Commonfunction::getDateTimeFormat($user_details1[0]['driver_pco_license_expire_date'],2); } ?></p>
				   </li> 
			<?php } ?>
			
			<?php if(isset($user_details1[0]['driver_insurance_number']) && !empty($user_details1[0]['driver_insurance_number'])) { ?> 
			<li><label><?php echo __('driver_insurance_number'); ?></label>
                            <p><?php if(isset($user_details1[0]['driver_insurance_number'])) { echo $user_details1[0]['driver_insurance_number']; } ?></p>
				   </li> 
			<?php } ?>
			
			<?php if(isset($user_details1[0]['driver_insurance_expire_date']) && !empty($user_details1[0]['driver_insurance_expire_date'])) { ?> 
			<li><label><?php echo __('driver_insurance_expire_date'); ?></label>
                            <p><?php if(isset($user_details1[0]['driver_insurance_expire_date'])) { echo Commonfunction::getDateTimeFormat($user_details1[0]['driver_insurance_expire_date'],2); } ?></p>
				   </li> 
			<?php }  ?>
			
			<?php if(isset($user_details1[0]['driver_national_insurance_number']) && !empty($user_details1[0]['driver_national_insurance_number'])) { ?> 
			<li><label><?php echo __('driver_national_insurance_number'); ?></label>
                            <p><?php if(isset($user_details1[0]['driver_national_insurance_number'])) { echo $user_details1[0]['driver_national_insurance_number']; } ?></p>
				   </li> 
			<?php } ?>
			
			<?php if(isset($user_details1[0]['driver_national_insurance_expire_date']) && !empty($user_details1[0]['driver_national_insurance_expire_date'])) { ?>
			<li><label><?php echo __('driver_national_insurance_expire_date'); ?></label>
                            <p><?php if(isset($user_details1[0]['driver_national_insurance_expire_date'])) { echo Commonfunction::getDateTimeFormat($user_details1[0]['driver_national_insurance_expire_date'],2); } ?></p>
				   </li> 
			<?php } ?>
			
				<?php } ?> 
			   			<li><label><?php echo __('booking_limit_per_day'); ?></label>
                                                    <p><?php if(isset($user_details1[0]['booking_limit'])) { echo $user_details1[0]['booking_limit']; } ?></p>
				   </li> 
			
			<li><label><?php echo __('rating_points'); ?></label>
				   <p><?php if(count($get_tot_ratings_driver) > 0) { 
					   $rate = ($get_tot_ratings_driver[0]['total_ratings'] )/($get_tot_ratings_driver[0]['trip_cnt']);					   
					   echo number_format($rate,1)." Out of 5";
					 } else {
						 echo "0 Out of 5";
					 }
					 
                                         ?></p>
				   </li>
			   <li><label><?php echo __('referral_code'); ?></label>
                               <p><?php if(isset($user_details1[0]['registered_driver_code'])) { echo $user_details1[0]['registered_driver_code']; } ?></p>
				   </li> 
			</ul>
			
			<?php if($usertype == 'A') { ?>
			
		       <?php if($user_details1[0]['user_type'] != 'N') { ?>   
				  
			  
                        <h2 class="tab_sub_tit"><?php echo ucfirst(__('companyinformation')); ?></h2>
                                <ul>
		 
			   <li><label><?php echo __('companyname'); ?></label>
                               <p><?php if(isset($user_details1[0]['company_name'])) { echo ucfirst($user_details1[0]['company_name']); } ?></p>
				   </li>  
						   
			   <li><label><?php echo __('companyaddress'); ?></label>
                               <p><?php if(isset($user_details1[0]['company_address'])) { echo $user_details1[0]['company_address']; } ?></p>				
				   </li>  	          

			<li><label><?php echo __('country_label'); ?></label>
                            <p><?php if(isset($user_details1[0]['country_name'])) { echo $user_details1[0]['country_name']; } ?></p>			
				   </li>

			<li><label><?php echo __('state_label'); ?></label>
                            <p><?php if(isset($user_details1[0]['state_name'])) { echo $user_details1[0]['state_name']; } ?></p>				
				   </li>
			
			<li><label><?php echo __('city_label'); ?></label>
                            <p><?php if(isset($user_details1[0]['city_name'])) { echo $user_details1[0]['city_name']; } ?></p>				
				   </li>
                                </ul>
			<?php } ?>
			
		
		<?php } ?>
           </div>
		<!--- Transaction Chart--->
                <div class="over_all">
			<div class="widget margin-bottom comp_journy" >
			<div class="title">
			<h6><?php echo __('transactions'); ?></h6>
			<?php if($get_tot_trans_driver > 0) { ?> 
			 <form  action="" method="post" name="" id="" >
			 <div class="rgt_field_det" align="right">
                             <div class="small_field_det">
                             <label><?php echo __('startdate');?></label>
				 <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_details1[0]['id']; ?>">
				 <input type="hidden" name="user_name" id="user_name" value="<?php echo $driver_name; ?>">
				  <input type="hidden" name="user_type" id="user_type" value="D">
				 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="transstartdate" name="transstartdate" value=""  />
				 <span id="tstartdate_error" class="errors" style="display:none;"></span>
                             </div>
                             <div class="small_field_det">
                                 <label><?php echo __('enddate');?></label>
				 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="transenddate" name="transenddate" value=""  />
				 <span id="tenddate_error" class="errors" style="display:none;"></span>
                             </div>
                                 <div class="small_butt"> 
                                     <input  type="button" name="search_transaction" id="search_transaction" value="GO" title="Go" >
				 
				 </div>
				 </div>	
			</form>
			<?php } ?>
			</div>
		<div id="driver_transactions">
			<?php if($display == 'display:none;'){ echo "<div class='no_data'>".__('no_data')."</div>"; } ?>
		<div id="transaction_chart" style="min-width: 400px; height: 400px; margin: 0 auto;<?php echo $display;?>"></div>
		</div>
		
		</div>
                </div>
		<!--- Transaction Chart--->
		
		<!--Current Status of The Driver -->
		<div class="widget margin-bottom" >
			<div class="title"><h6><?php echo __('current_statusof_driver'); ?></h6>
				<?php if(($_SESSION["user_type"]=='C' || $_SESSION["user_type"]=='M' || $_SESSION["user_type"]=='A') && $user_details1[0]['login_status']=='S') {  ?>
                                    <div class="small_butt">
					<input type="button" name="logout" id="driver_logout"  onclick="d_logout('<?php echo $user_details1[0]['id']; ?>','<?php echo $user_details1[0]['company_id']; ?>')" value="<?php echo __('logout_label'); ?>" title="<?php echo __('logout_label'); ?>" >
					</div>
                                <?php } ?>
				<div style="width:auto; float:right; margin: 0px -1px;">		
				<!--<div class="title"><?php echo __('notification_setting')." : ";?><span class='btn btn-mini btn-primary'><?php echo $not_setting;?></span></div>-->
				</div>
			</div>
			<div id="on_going_trip_map" >
				<div class="ongoing">
					<div id="on_going_trip"></div>
					<div id="on_going_place"></div>
				</div>
				<?php if(SHOW_MAP !=1) { ?>
					<div id="map-canvas" style="width:100%;height:250px;"></div>
				<?php } ?>
			</div>
		</div>

		<!--Current Status of The Driver -->

	<!-- Last Three Trips Driver Map -->
<script type="text/javascript" src="/public/common/js/gmaps.js"></script>
	<div class="widget margin-bottom" >
			<div class="title"><h6><?php echo __('trip_route_map'); ?></h6>
				<div style="width:auto; float:right; margin: 0;">		
				<div class="button greyish"></div>
				</div>
			</div>
			<div id="on_going_trip_map" >
				
					  <?php if(count($driver_tracking) > 0) { ?> <div id="map"></div> <?php }else{ echo "<div class='no_data'>".__('no_data')."</div>";} ?>
			</div>
		</div>

	<?php
 if(count($driver_tracking) > 0) { 
/** Take the starting and ending points **/
if(isset($driver_tracking[0]['active_record']) && !empty($driver_tracking[0]['active_record'])){
	$driver_latlog = "[".$driver_tracking[0]['active_record'].']';
	$driver_default = explode(',',$driver_tracking[0]['active_record']);
	$count =  count($driver_default);
	$driver_centerlat = str_replace('[','',$driver_default[0]);
	$driver_centerlng = str_replace(']','',$driver_default[1]);
	$driver_endlat = "";
	$driver_endlng = "";
	if($count > 3) {
		$driver_endlat = str_replace('[','',$driver_default[$count-2]);
		$driver_endlng = str_replace(']','',$driver_default[$count-1]);
	}
}

if(isset($driver_tracking[1]['active_record']) && !empty($driver_tracking[1]['active_record'])){
	$driver_latlog1 = "[".$driver_tracking[1]['active_record'].']';
	$driver_default1 = explode(',',$driver_tracking[1]['active_record']);
	$count =  count($driver_default1);
	$driver_centerlat1 = str_replace('[','',$driver_default1[0]);
	$driver_centerlng1 = str_replace(']','',$driver_default1[1]);
	$driver_endlat1 = "";
	$driver_endlng1 = "";
	if($count > 3) {
		$driver_endlat1 = str_replace('[','',$driver_default1[$count-2]);
		$driver_endlng1 = str_replace(']','',$driver_default1[$count-1]);
	}
}

if(isset($driver_tracking[2]['active_record']) && !empty($driver_tracking[2]['active_record'])){
	$driver_latlog2 = "[".$driver_tracking[2]['active_record'].']';
	$driver_default2 = explode(',',$driver_tracking[2]['active_record']);
	$count =  count($driver_default2);
	$driver_centerlat2 = str_replace('[','',$driver_default2[0]);
	$driver_centerlng2 = str_replace(']','',$driver_default2[1]);
	$driver_endlat2 = "";
	$driver_endlng2 = "";
	if($count > 3) {
		$driver_endlat2 = str_replace('[','',$driver_default2[$count-2]);
		$driver_endlng2 = str_replace(']','',$driver_default2[$count-1]);
	}
}

/** Take the starting and ending points ends **/
?>			
		


  <script type="text/javascript">
    var map;
    $(document).ready(function(){

      map = new GMaps({
        el: '#map',
        lat: <?php echo $driver_centerlat; ?>,
        lng: <?php echo $driver_centerlng; ?>,
        click: function(e){
          //console.log(e);
        }
      });
var iconBase = '<?php echo PUBLIC_IMGPATH.'/' ; ?>';
   /** Make Marker for three paths **/

	<?php if(isset($driver_tracking[0]['active_record']) && !empty($driver_tracking[0]['active_record'])){ ?>

      map.addMarker({
        lat: <?php echo $driver_centerlat; ?>,
        lng: <?php echo $driver_centerlng; ?>,
        title: '<?php echo $driver_tracking[0]['current_location']; ?>',
	icon: iconBase + 'driver_one.png',
        details: {
          database_id: 42,
          author: 'HPNeo'
       
        },
      });
	<?php if(!empty($driver_endlat) && !empty($driver_endlng)) { ?>
      map.addMarker({
        lat: <?php echo $driver_endlat; ?>,
        lng: <?php echo $driver_endlng; ?>,
	icon: iconBase + 'driver_one.png',
        title: '<?php echo $driver_tracking[0]['drop_location']; ?>',
      });
    <?php } ?>
	path = <?php echo $driver_latlog; ?>;
	map.drawPolyline({
        path: path,
        strokeColor: 'green',
        strokeOpacity: 0.6,
        strokeWeight: 6
        });
	
	


	<?php } ?>

	<?php if(isset($driver_tracking[1]['active_record']) && !empty($driver_tracking[1]['active_record'])){ ?>

	map.addMarker({
        lat: <?php echo $driver_centerlat1; ?>,
        lng: <?php echo $driver_centerlng1; ?>,
	icon: iconBase + 'driver_two.png',
        title: '<?php echo $driver_tracking[1]['current_location']; ?>',
        details: {
          database_id: 421,
          author: 'HPNeo1'
        },

      });
      <?php if(!empty($driver_endlat1) && !empty($driver_endlng1)) { ?>
      map.addMarker({
        lat: <?php echo $driver_endlat1; ?>,
        lng: <?php echo $driver_endlng1; ?>,
	icon: iconBase + 'driver_two.png',
        title: '<?php echo $driver_tracking[1]['drop_location']; ?>',
      });
	<?php } ?>
		path1 = <?php echo $driver_latlog1; ?>;

	map.drawPolyline({
        path: path1,
        strokeColor: 'red',
        strokeOpacity: 0.6,
        strokeWeight: 6
      	});
	<?php } ?>


	<?php if(isset($driver_tracking[2]['active_record']) && !empty($driver_tracking[2]['active_record'])){ ?>

	map.addMarker({
        lat: <?php echo $driver_centerlat2; ?>,
        lng: <?php echo $driver_centerlng2; ?>,
        title: '<?php echo $driver_tracking[2]['current_location']; ?>',
	icon: iconBase + 'driver_three.png',
        details: {
          database_id: 43,
          author: 'HPNeo2'
        },
      });
      <?php if(!empty($driver_endlat2) && !empty($driver_endlng2)) { ?>
      map.addMarker({
        lat: <?php echo $driver_endlat2; ?>,
        lng: <?php echo $driver_endlng2; ?>,
	icon: iconBase + 'driver_three.png',
        title: '<?php echo $driver_tracking[2]['drop_location']; ?>',
        /*infoWindow: {
          content: '<p>HTML Content</p>'
        }*/
      });
      <?php } ?>

	path2 = <?php echo $driver_latlog2; ?>;

	map.drawPolyline({
        path: path2,
        strokeColor: 'blue',
        strokeOpacity: 0.6,
        strokeWeight: 6
      	});

   <?php } ?>



	/** Make Marker for three paths **/

    });
  </script>


<style>
#map{
  display: block;
  width: 100%;
  height: 500px;
  margin-top:20px;	
  margin: 0 auto;
 
}
#map.large{
  height:500px;
}
</style>
<?php } ?>

	<!-- Last Three Trips Driver Map ends -->
		<!---Trip Charts-->
		<div class="widget margin-bottom comp_journy" >
			<div class="title">
			<h6><?php echo __('trip_statitics'); ?></h6>
			<?php if($total_trip_statitics > 0) { ?>
			 <form  action="#" method="post" name="" id="" >
                             <div class="rgt_field_det" align="right">
				<?php
					$trip_startdate = date('Y-m-d', strtotime('-7 days'))." 00:00:00";
					$trip_enddate = date('Y-m-d 23:59:59');
				?>
                                 <div class="small_field_det">
                                     <label><?php echo __('startdate');?></label>
				 <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_details1[0]['id']; ?>">
				 <input type="hidden" name="user_name" id="user_name" value="<?php echo $driver_name; ?>">
				  <input type="hidden" name="user_type" id="user_type" value="D">
				 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="tripstartdate" name="transstartdate" value="<?php echo $trip_startdate;//Commonfunction::getDateTimeFormat($trip_startdate,1); ?>"  />
				 <span id="tripstartdate_error" class="errors" style="display:none;"></span>
                                 </div>
                                 <div class="small_field_det">
                                     <label><?php echo __('enddate');?></label>
				 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="tripsenddate" name="transenddate" value="<?php echo $trip_enddate;//Commonfunction::getDateTimeFormat($trip_enddate,1); ?>"  />
				 <span id="tripenddate_error" class="errors" style="display:none;"></span>
                                 </div>
				 <div class="small_butt">
				 <input type="button" name="search_transaction" id="search_statistics" value="GO" title="Go" >
				 </div>
				 </div>	
			</form>
			<?php } ?>
			</div>
		<div id="driver_statistics">
		<?php if($display_trip == 'display:none;'){ echo "<div class='no_data'>".__('no_data')."</div>"; } ?>
		<div id="trip_statitics" style="min-width: 400px; height: 400px; margin: 0 auto;<?php echo $display_trip;?>"></div>
		</div>
		
		</div>
		<!---Trip Charts-->
		<!-- Upcoming Journey -->
		<?php /*<div class="widget margin-bottom" >
			<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo __('Upcoming Journey'); ?></h6>
				<div style="width:auto; float:right; margin: 4px 3px;">		
					<div class="button greyishB"> </div>              

				</div>
			</div>
			<div>
				
					<?php if(count($driver_logs_upcoming)>0) { ?>
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						
						<thead>
						<tr>
							<td>#</td>
							<td><?php echo __('passenger_name'); ?></td>
							<td><b><?php echo __('Current_Location'); ?></b></td>
							<td><b><?php echo __('Drop_Location'); ?></b></td>
							<td><b><?php echo __('No_Passengers'); ?></b></td>
							<td><b><?php echo __('pictup_date');?></b></td>
							<td><b><?php echo __('pictup_time');?></b></td>
							
							</tr>
						</tr>
						</thead>					
						
						<?php 
						$i=1;
						($i%2 == 1)?$class="eventr":$class="oddtr";
					
							foreach($driver_logs_upcoming as $values)
							{
									
								?>
								<tr class="<?php echo $class; ?>">	
									<td><?php echo $i;?></td>
									<td><?php echo ucfirst($values->name); ?></td>
									<td><?php echo $values->current_location;?></td>
									<td><?php echo $values->drop_location;?></td>
									<td><?php echo $values->no_passengers;?></td>
									<td><?php echo date('d/m/Y',strtotime($values->pickup_time));?></td>
									<td><?php echo date('h:i:s', strtotime($values->pickup_time));?></td>
									
								
								</tr>
								
								<?php $i++;
							}
						 ?>
						</table>
					<?php }else {
							echo "<div class='no_data'>".__('no_data')."</div>"; 
							
						}?>					
				
				</div>
		</div>*/?>
		<!-- Upcoming Journey -->
		
		<!-- Ongoing Journey -->
		<div class="widget margin-bottom" >
			<div class="title"><h6><?php echo __('ongoing_journey'); ?></h6>
				<div style="width:auto; float:right; margin: 0px;">		
				<div class="button greyish"></div>

				</div>
			</div>
			<div>
				
					<?php if(count($driver_logs_progress)>0) { ?>
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						
						<thead>
						<tr>
							<td>#</td>
							<td><?php echo __('passenger_name'); ?></td>
							<td><b><?php echo __('Current_Location'); ?></b></td>
							<td><b><?php echo __('Drop_Location'); ?></b></td>
							<td><b><?php echo __('No_Passengers'); ?></b></td>
							<td><b><?php echo __('pictup_date');?></b></td>
							<td><b><?php echo __('pictup_time');?></b></td>							
							
							</tr>
						</tr>
						</thead>					
						
						<?php 
						$i=1;
						($i%2 == 1)?$class="eventr":$class="oddtr";
					
							foreach($driver_logs_progress as $values)
							{
									
								?>
								<tr class="<?php echo $class; ?>">	
									<td><?php echo $i;?></td>
									<td><?php echo ucfirst($values->name); ?></td>
									<td><?php echo $values->current_location;?></td>
									<td><?php echo $values->drop_location;?></td>
									<td><?php echo $values->no_passengers;?></td>
                                                                        <td><?php echo Commonfunction::getDateTimeFormat($values->pickup_time,2);?></td>
                                                                        <td><?php echo Commonfunction::getDateTimeFormat($values->pickup_time,4);?></td>
								</tr>
								
								<?php $i++;
							}
						 ?>
						</table>
					<?php }else {
							echo "<div class='no_data'>".__('no_data')."</div>"; 
							
						}?>					
				
				</div>
		</div>
		
		<!-- Ongoing Journey -->
		
		<!-- Completed  Journey -->
		<div class="widget margin-bottom comp_journy" >
			<div class="title">
			<h6><?php echo __('completed_journey'); ?></h6>
			<?php if($get_tot_trans_driver > 0) { ?>
			 <form  action="<?php echo URL_BASE;?>manage/genpdf" method="post" name="drivermgmt" id="drivermgmt" >
			 <div class="rgt_field_det" align="right">
				 <?php
					$users_startdate = date('Y-m-d')." 00:00:01";
					$users_enddate = date('Y-m-d 23:59:59');
				?>
                                <div class="small_field_det">
                                <label><?php echo __('startdate');?></label>
				 <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_details1[0]['id']; ?>">
				 <input type="hidden" name="user_name" id="user_name" value="<?php echo $driver_name; ?>">
				  <input type="hidden" name="user_type" id="user_type" value="D">
				 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="userstartdate" name="userstartdate" value="<?php echo $users_startdate; //Commonfunction::getDateTimeFormat($users_startdate,1); ?>"  />
				 <span id="startdate_error" class="errors" style="display:none;"></span>
                                </div>
                                <div class="small_field_det">
                                 <label><?php echo __('enddate'); ?></label>
				 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="userenddate" name="userenddate" value="<?php echo $users_enddate; //Commonfunction::getDateTimeFormat($users_enddate,1); ?>" />
				 <span id="enddate_error" class="errors" style="display:none;"></span>
                                </div>
				 <div class="small_butt"> 
				 <input type="hidden" name="type_export" id="type_export" value="">
				 <input type="button" name="change_usercompany" id="change_usercompany" value="<?php echo __('go'); ?>" title="<?php echo __('go'); ?>" >
				 
				 </div>				
			</div>
			</form>
			<?php } ?>
        	<?php /*?><div style="width:auto; float:right; margin: 0px 0px;">		
				<?php if($count_driver_logs_completed_transaction > REC_PER_PAGE) {  ?>
					<?php if($_SESSION['user_type'] == 'A') { ?> 
					<div class="button greyish">
					<a href="<?php echo URL_BASE ?>transaction/admintransaction_list?filter_company=All&taxiid=All&startdate=&enddate=&passengerid=All&driver_id=<?php echo $user_details1[0]['id']; ?>"><?php echo __('view_all'); ?></a>
					</div>
					<?php } 
					else if($_SESSION['user_type'] == 'C')
					{ ?>
					<div class="button greyish">
					<a href="<?php echo URL_BASE ?>transaction/companytransaction_list?taxiid=All&startdate=&enddate=&passengerid=All&driver_id=<?php echo $user_details1[0]['id']; ?>"><?php echo __('view_all'); ?></a>
					</div>
					<?php }
					else if($_SESSION['user_type'] == 'M')
					{ 
					?>
					<div class="button greyish"><a href="<?php echo URL_BASE; ?>transaction/managertransaction_list?taxiid=All&startdate=&enddate=&passengerid=All&driver_id=<?php echo $user_details1[0]['id']; ?>"><?php echo __('view_all'); ?></a></div>
					<?php }
				}
				else
				{ ?>            
				<div class="button greyish"></div>
				<?php } ?>
				</div> <?php */ ?>
			</div>
			<div id="drivercompleted_logs">
				
					<?php //print_r($driver_logs_completed_transaction);
					if(count($driver_logs_completed_transaction)>0) { ?>
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						
						<thead>
						<tr>
							<td>#</td>
							<td><?php echo __('trip_id'); ?></td>
							<td><?php echo __('passenger_name'); ?></td>
							<td><b><?php echo __('pickuploc_droploc'); ?></b></td>
							<td><b><?php echo __('pictup_date');?></b></td>
							<td><b><?php echo __('distance');?></b></td>		
							<td><b><?php echo str_replace('%currency%',CURRENCY,__('fare'));?></b></td>	
							<td><b><?php echo __('tax');?></b></td>	
							<td><b><?php echo __('trip_total_fare');?></b></td>	
							<?php /*<td><b><?php echo __('equivalent_to_usd').CURRENCY_FORMAT; ?></b></td> */ ?>
							</tr>
						</tr>
						</thead>					
						
						<?php 
						$i=1;
						
						
							foreach($driver_logs_completed_transaction as $values)
							{
								($i%2 == 1)?$class="eventr":$class="oddtr";
								$distance = round($values->distance,2).' '.$values->distance_unit;
								$current_fare = round($values->fare,2);
								$company_tax = $values->company_tax;
								$percentvalue = ($company_tax/100)*$current_fare;
								$currtotal = $current_fare - $percentvalue;
								$travel_status = $values->travel_status;
								/*if($_SESSION['company_id'] != 0)
								{
									$company_currency = findcompany_currency($_SESSION['company_id']);
								}
								else
								{
									$company_currency = findcompany_currency($values->company_id);
								} */
								$company_currency = CURRENCY;
								//$company_currency_format = findcompany_currencyformat($values->company_id);
								//$convet_amt = currency_conversion($company_currency_format,$current_fare);
								//$convet_amt = round($convet_amt,2);
								
							if($travel_status == 0) { $status = __('critical'); $row_solor = 'style="color:#00FF00;"';  } elseif($travel_status == 1) { $status = __('completed'); $row_solor = 'style="color:#00FF00;"'; }  elseif($travel_status == 2) { $status = __('inprogress'); $row_solor = 'style="color:#0000FF;"'; }  if($travel_status == 3) { $status = __('start_to_pickup'); $row_solor = 'style="color:#FFFF00;"'; } elseif($travel_status == 4) { $status = __('cancel_by_passenger'); $row_solor = 'style="color:#990066;"';} elseif($travel_status== 5) { $status = __('waiting_payment'); $row_solor = 'style="color:#00FFFF;"';} elseif($travel_status == 6) { $status = __('missed'); $row_solor = 'style="color:#FF6633;"';} elseif($travel_status == 7) { $status = __('dispatched'); $row_solor = 'style="color:#003333;"'; }  elseif($travel_status == 8) { $status = __('cancelled'); $row_solor = 'style="color:#990000;"';} 		//echo $row_solor;
								?>
								<tr class="<?php echo $class; ?>">	
								<td><?php echo $i;?></td>
								<td><?php echo $values->passengers_log_id; ?></td>
								<td><?php echo ucfirst($values->name); ?></td>
								<td><p <?php echo $row_solor;?>><?php echo $values->current_location;?></p>
								<p><?php echo $values->drop_location;?></p></td>
								<td><?php echo Commonfunction::getDateTimeFormat($values->pickup_time,1);?></td>
								<td><?php echo $distance;?></td>
								<td><?php echo $currtotal;?></td>
								<td><?php echo $company_currency.$company_tax;?></td>
								<td><?php echo $company_currency.$current_fare;?></td>
								<?php /*<td><?php echo $convet_amt; ?></td>	*/ ?>													
								</tr>								
								<?php $i++;
							}
						 ?>
						</table>
				<div align="left" class="new_button"> 
				<input type="button" name="gen_pdf" id="gen_pdf" value="<?php echo __('gen_pdf');?>" title="<?php echo __('gen_pdf');?>" onclick="gen_pdf(this.value)">
				</div>	
					<?php }else {
							echo "<div class='no_data'>".__('no_data')."</div>"; 							
						}?>									
				</div>
 
		</div>
		<!-- Completed Journey -->
		<!-- Driver Withdraw Requests -->
		<div class="widget margin-bottom" >
			<div class="title">
				<h6><?php echo __('manage_driver_request'); ?></h6>
			</div>
			<div id="driverWithdrawReqs">
				<?php if(count($driverWithdrawReqs) > 0) { ?>
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						<thead>
							<tr>
								<td>#</td>
								<td><?php echo __('request_date'); ?></td>
								<td><?php echo __('request_amount'); ?></td>
								<td><?php echo __('approved_date'); ?></td>
								<td><?php echo __('request_status'); ?></td>
							</tr>
						</thead>
						<tbody>
							<?php 
							$sNo = 1;
							foreach($driverWithdrawReqs as $reqs) {
								 $trcolor=($sNo%2==0) ? 'oddtr' : 'eventr'; 
								 ?>
							<tr class="<?php echo $trcolor; ?>">
								<td><?php echo $sNo; ?></td>
								<td><?php  echo Commonfunction::getDateTimeFormat($reqs['request_date'],1);  ?></td>
								<td><?php echo $reqs['wallet_request_amount'];  ?></td>
								<td><?php echo ($reqs['request_status'] == 2 && $reqs['approved_date'] != '0000-00-00 00:00:00') ? $reqs['approved_date'] : '-';  ?></td>
								<td><?php echo $reqs['statuslbl']; ?></td>
							</tr>
							<?php 
								$sNo++;
							} ?>
						</tbody>
					</table>
				<?php } else {
							echo "<div class='no_data'>".__('no_data')."</div>"; 							
						} 
				?>
			</div>
		</div>
		<!-- Driver Withdraw Requests -->
		<!-- Service Time  Journey -->
		<?php /*
		<div class="widget margin-bottom" >
			<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" />
			<h6><?php echo __('service_time'); ?></h6>
				<div style="width:auto; float:right; margin: 4px 3px;">		
				<?php if($count_driver_logs_service > 1) {  ?>
				<div class="button greyish"><a href="<?php echo URL_BASE; ?>manage/driverlogs/<?php echo $user_details1[0]['id']; ?>"><?php echo __('view_all'); ?></a></div>
				<?php	
				}
				else
				{ 
				?>
				<div class="button greyish"></div>
				<?php } ?>        

				</div>
			</div>
			<div>
				
					<?php if(count($driver_logs_service)>0) { ?>
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						
						<thead>
						<tr>
							<td>#</td>
							<td><?php echo __('taxi_no'); ?></td>
							<td><b><?php echo __('interval_type'); ?></b></td>
							<td><b><?php echo __('interval_startdate');?></b></td>
							<td><b><?php echo __('interval_enddate');?></b></td>
							<td><b><?php echo __('interval_time');?></b></td>
							<td><b><?php echo __('Reason');?></b></td>
							</tr>
						</tr>
						</thead>					
						
						<?php 
						$i=1;
						($i%2 == 1)?$class="eventr":$class="oddtr";
							foreach($driver_logs_service as $values)
							{ ?>
								<tr class="<?php echo $class; ?>">	
								<td><?php echo $i;?></td>
								<td><?php echo ucfirst($values->taxi_no); ?></td>
								<td><?php 
								if($values->interval_type =='B') 
								{ echo __('break'); }
								else if($values->interval_type =='S') 
								{ echo __('service'); } ?></td>
								<td><?php echo $values->interval_start;?></td>
								<td><?php echo $values->interval_end;?></td>
								<td>
								<?php $to_time = strtotime($values->interval_start);
									$from_time = strtotime($values->interval_end);
									
								if($from_time > $to_time)
								{
								$seconds = $from_time - $to_time;
								echo $days    = floor($seconds / 86400). " Day ";
								echo $hours   = floor(($seconds - ($days * 86400)) / 3600)." Hr ";
								echo $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60)." Min ";
								echo $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)))." Sec ";
								}
								 ?>	
								</td>
								<td><?php echo $values->reason;?></td>
								</tr>
								
								<?php $i++;
							} 
						 ?>
						</table>
					<?php }else {
							echo "<div class='no_data'>".__('no_data')."</div>"; 
							
						}?>					
				
				</div>
		</div>
		
		<!-- Service Time Journey -->
		*/ ?>
		
		<!-- Shift Time Journey Start -->
                <div class="scroll_inner">
		<div class="widget margin-bottom  shift_history" >
			<div class="title">
			<h6><?php echo __('shift_history'); ?></h6>
                        <div style="float: right;">		
				<?php if($count_get_driver_shift_logs > 1) {  ?>
                            <div class="small_butt"><a class="export_me_menu" href="<?php echo URL_BASE; ?>manage/drivershifthistory/<?php echo $user_details1[0]['id']; ?>"><?php echo __('view_all'); ?></a></div>
				<?php	
				}
				else
				{ 
				?>
				<div class="button greyish"></div>
				<?php } ?>        

				</div>
			</div>
			<div>
				
					<?php if(count($get_driver_shift_logs)>0) { ?>
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						
						<thead>
						<tr>
							<td>#</td>
							<td><?php echo __('taxi_no'); ?></td>							
							<td><b><?php echo __('shift_startdate');?></b></td>
							<td><b><?php echo __('shift_endtime');?></b></td>
							<td><b><?php echo __('shift_time');?></b></td>
							</tr>
						</tr>
						</thead>					
						
						<?php 
						$i=1;
						($i%2 == 1)?$class="eventr":$class="oddtr";
							foreach($get_driver_shift_logs as $values)
							{ ?>
								<tr class="<?php echo $class; ?>">	
								<td><?php echo $i;?></td>
								<td><a href="<?php echo URL_BASE.'manage/taxiinfo/'.$values->taxi_id;?>">
								<?php echo ucfirst($values->taxi_no); ?></a></td>

                                                                <td><?php if(isset($values->shift_start) && ($values->shift_start!=null)){ echo Commonfunction::getDateTimeFormat($values->shift_start,1); } ?></td>
                                                                <td><?php if(isset($values->shift_end) && ($values->shift_end!=null)){ echo Commonfunction::getDateTimeFormat($values->shift_end,1); } ?></td>
								<td>
								<?php $to_time = strtotime($values->shift_start);
									$from_time = strtotime($values->shift_end);
								
									if($from_time > $to_time)
									{
									$seconds = $from_time - $to_time;
									echo $days    = floor($seconds / 86400). " Day ";
									echo $hours   = floor(($seconds - ($days * 86400)) / 3600)." Hr ";
									echo $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60)." Min ";
									echo $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)))." Sec ";		
									}else { echo __('shift_in');}	
								 ?>	
								</td>
								</tr>
								
								<?php $i++;
							} 
						 ?>
						</table>
					<?php }else {
							echo "<div class='no_data'>".__('no_data')."</div>"; 
							
						}?>					
				
				</div>
		</div>
                </div>
		<!-- Service Time Journey -->
			<div class="widget margin-bottom">
				<div class="title">
					<h6><?php echo $page_title." - ".ucfirst($driver_name); ?></h6>
				</div>
				
			<?php
			if(count($user_details)>0){
				$user_id = $user_details[0]->id;
			 foreach($user_details as $res){
				 if($res->rating != 0) {
				//echo "<pre>";print_r($res);echo "</pre>";
				if($res->profile_image)
				{
					$img1 = 'thumb_'.$res->profile_image;
					$img = URL_BASE.'public/uploads/passenger/'.$img1;
				}else{
					$img = URL_BASE."public/common/images/noimages.jpg";
				}
				switch($res->rating){
					case 1: $star = "one";
							break;
					case 2: $star = "two";
							break;
					case 3: $star = "three";
							break;
					case 4: $star = "four";
							break;
					case 5: $star = "five";
							break;
					default: $star = "";
							break;
				}
				 ?>
			<div class="review" />
				<div class="review-head">
					<div style="float:left;" class="review-title"><?php echo ucfirst($res->name);?></div>
					<div style="float:right;"><?php echo Commonfunction::getDateTimeFormat($res->createdate,2);?></div>
				</div>
				<div class="review-text">
					<div class="reviewerprofile" style="float:left;">
						<div id="revimg">
							<img src="<?php echo $img;?>" width="50" height="50"/>
						</div>
						<div id="reviewer">
							<span class="review-owner" style="text-align:center;"><?php //echo $res->name;?></span>
						</div>
						<div id="revdate"></div>
					</div>
					<div style="float:right;width:92%;">
					<p class="rating <?php echo $star;?>"></p>
						<?php //if($res->comments){echo $res->comments;}else{echo __('no_comments');}?>
					</div>
				</div>
			</div>
			<?php }
			}
			}else{
				$user_id = "";
				echo "<div class='no_data'>".__('no_data')."</div>";
			}
			 ?>
		</div>

		<?php /** Referral Driver List Start **/ ?>
		<div class="widget margin-bottom" >
			<div class="title">
				<h6><?php echo __('driver_referral'); ?></h6>
				<div style="width:auto; float:right; margin:0;">
					<?php if(count($driverReferralList) > 0) { ?>
                                    <div class="small_butt"><a class="export_me_menu" href="<?php echo URL_BASE; ?>manage/driverreferralhistory/<?php echo $user_details1[0]['id']; ?>"><?php echo __('view_all'); ?></a></div>
					<?php } else { ?>
						<div class="button greyish"></div>
					<?php } ?>
				</div>
			</div>
			<div>
				<?php if(count($driverReferralList) > 0) { ?>
				<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
					<thead>
					<tr>
						<td>#</td>
						<td><b><?php echo __('name_label');?></b></td>
						<td><b><?php echo __('status_label');?></b></td>
						<td><b><?php echo __('wallet_amount');?></b></td>
						<td><b><?php echo __('created_date');?></b></td>
					</tr>
					</thead>
					<?php $i=1; ($i%2 == 1) ? $class="eventr" : $class="oddtr";
						foreach($driverReferralList as $ref) { ?>
							<tr class="<?php echo $class; ?>">
								<td><?php echo $i; ?></td>
								<td><a href="<?php echo URL_BASE.'manage/driverinfo/'.$ref->registered_driver_id; ?>"><?php echo ucfirst($ref->name); ?></a></td>
								<td><?php echo ($ref->referral_status > 0) ? __('used') : __("not_used"); ?></td>
								<td><?php echo $ref->registered_driver_wallet; ?></td>
								<td><?php echo Commonfunction::getDateTimeFormat($ref->createdate,1); ?></td>
							</tr>
					<?php $i++; } ?>
				</table>
				<?php } else {
					echo "<div class='no_data'>".__('no_data')."</div>";
				} ?>
			</div>
		</div>
		<?php /** Referral Driver List End **/ ?>

        </div>
    </div>
</div>  
<input type="hidden" name="driver_id" id="driver_id" value="<?php echo $user_id; ?>">

<script src="<?php echo SCRIPTPATH; ?>highcharts.js"></script>
<script type="text/javascript" language="javascript">
	//function to delete driver
	function d_logout(driver_id,company_id)
	{
		
	   var answer = confirm("<?php echo __('are_you_surewanttologout');?>");
	    
		if (answer){
		 $.ajax({
			url:"<?php echo URL_BASE;?>manage/driver_logout",
			type:"get",
			data:"driver_id="+driver_id+"&company_id="+company_id,
			success:function(data){
				//console.log(data);return false;
			if(data==1)
			{
                          alert('<?php echo __('logout_success'); ?>');
			  window.location=SrcPath+'manage/driverinfo/'+driver_id;
			}else{
			alert('<?php echo __('driver_in_trip'); ?>');
			}
			
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
	    }
	    
	    return false;  
	}  
 $(document).ready(function() {
change_driver_rating();
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
});

$("#transstartdate,#tripstartdate").datetimepicker( {
	showTimepicker:DEFAULT_TIME_SHOW,
	showSecond: true,
	timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(),
	stepSecond: 1
});

$("#userenddate").datetimepicker( {
	showTimepicker:DEFAULT_TIME_SHOW,
	showSecond: true,
	timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(), 
	stepSecond: 1
});
$("#transenddate,#tripsenddate").datetimepicker( {
	showTimepicker:DEFAULT_TIME_SHOW,
	showSecond: true,
	timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(), 
	stepSecond: 1
});


});

function change_driver_rating()
{
      		var driver_id = $("#driver_id").val();

		var page_no = '1';
		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getdriverratinglist",
			type:"get",
			data:"driver_id="+driver_id+"&page="+page_no,
			success:function(data){
			$('#driver_ratings').html();
			$('#driver_ratings').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function pagin_driver_rating(page_no)
{
		var driver_id = $("#driver_id").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getdriverratinglist",
			type:"get",
			data:"driver_id="+driver_id+"&page="+page_no,
			success:function(data){
			$('#driver_ratings').html();
			$('#driver_ratings').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

//

 $("#change_usercompany").click(function(){

 	var startdate = $("#userstartdate").val();
	var enddate = $("#userenddate").val();
	var driver_id = $("#user_id").val();
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
			$('#drivercompleted_logs').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
			$("#startdate_error").html("");
			$("#startdate_error").hide();
			var dataS = "startdate="+startdate+"&enddate="+enddate+"&driver_id="+driver_id;
			$.ajax({
				type: "POST",
				url: SrcPath+"manage/driver_completed_logs", 
				data: dataS, 
				cache: false, 
				dataType: 'html',
				success: function(response) 
				{
					$('#drivercompleted_logs').html(response);
				}
			});
		}
	}
 });
 
 
 $("#search_transaction").click(function(){

 	var startdate = $("#transstartdate").val();
	var enddate = $("#transenddate").val();
	var driver_id = $("#user_id").val();
	if(startdate =='')
	{
		$("#tstartdate_error").html("<?php echo __('select_startdate'); ?>");
		$("#tstartdate_error").show();
	}
	else
	{
		$("#tstartdate_error").html("");
		$("#tstartdate_error").hide();
	}
	if(enddate =='')
	{
		$("#tenddate_error").html("<?php echo __('select_enddate'); ?>");
		$("#tenddate_error").show();
	}
	else
	{
		$("#tenddate_error").hide("");
		$("#tenddate_error").hide();
	}
	if(startdate !='' && enddate!='')
	{
		if(to_timestamp(startdate) > to_timestamp(enddate))
		{
			$("#tstartdate_error").html("<?php echo __('startdate_greater'); ?>");
			$("#tstartdate_error").show();
		}
		else
		{
			$('#driver_transactions').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');
			$("#tstartdate_error").html("");
			$("#tstartdate_error").hide();
			var dataS = "startdate="+startdate+"&enddate="+enddate+"&driver_id="+driver_id;
			//var dataS = "{'startdate':'"+startdate+"','enddate':'"+enddate+"','driver_id':'"+driver_id+"'}";alert(dataS);
			$.ajax({
				type: "POST",
				url: SrcPath+"manage/driver_transaction_search", 
				data: dataS, 
				cache: false, 
				dataType: 'html',
				success: function(response) 
				{
					$('#driver_transactions').html(response);
				}
			});
		}
	}
	 
 });
 
 $("#search_statistics").click(function(){

 	var startdate = $("#tripstartdate").val();
	var enddate = $("#tripsenddate").val();
	var driver_id = $("#user_id").val();
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
		if(to_timestamp(startdate) > to_timestamp(enddate))
		{
			$("#tripstartdate_error").html("<?php echo __('startdate_greater'); ?>");
			$("#tripstartdate_error").show();
		}
		else
		{
			$('#driver_statistics').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');
			$("#tripstartdate_error").html("");
			$("#tripstartdate_error").hide();
			var dataS = "startdate="+startdate+"&enddate="+enddate+"&driver_id="+driver_id;
			//var dataS = "{'startdate':'"+startdate+"','enddate':'"+enddate+"','driver_id':'"+driver_id+"'}";alert(dataS);
			$.ajax({ 
				type: "POST",
				url: SrcPath+"manage/driver_statistics_search", 
				data: dataS, 
				cache: false, 
				dataType: 'html',
				success: function(response) 
				{
					$('#driver_statistics').html(response);
				}
			});
		}
	}
 });
 
function gen_pdf(type)
{
	//alert(type);
 	var startdate = $("#userstartdate").val();
	var enddate = $("#userenddate").val();
	var driver_id = $("#drivers_id").val();
	var driver_name = $('#driver_name').val();
	$('#type_export').val(type);
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
			document.forms['drivermgmt'].submit();
		}
	}
	 
 }
<?php if(count($month) > 0 && count($fare) > 0) { ?>
$('#transaction_chart').highcharts({
	title: {
		text: '<?php echo __("last7days_transaction"); ?>',
		x: -20 //center
	},
	subtitle: {
		text: '',
		x: -20
	},
	xAxis: {
		categories: [<?php echo $month;?>]
	},
	yAxis: {
		title: {
			text: '<?php echo __("amount_rs"); ?>'
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
		verticalAlign: 'middle',
		borderWidth: 0
	},
	series: [{
		name: '<?php echo __("transaction"); ?>',
		data: [<?php echo $fare;?>]
	}]
});
<?php } ?>
</script>
<?php if(count($createdate) > 0 && count($reject_trips) > 0 && count($cancelled_trips) > 0 && count($completed_trips) > 0) { ?>
<script>
$(function () {
        $('#trip_statitics').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: '<?php echo __('trip_statitics'); ?>'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: [<?php echo $createdate; ?>]
            },
            yAxis: {
                min: 0,
                title: {
                    text: '<?php echo __("trip_counts"); ?>'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y} <?php echo __("trips"); ?></b></td></tr>',
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
                name: '<?php echo __("rejected_trips"); ?>',
                data: [<?php echo $reject_trips; ?>]
    
            }, {
                name: '<?php echo __("cancelledtrip_logs"); ?>',
                data: [<?php echo $cancelled_trips; ?>]
    
            }, {
                name: '<?php echo __("completed_trips"); ?>',
                data: [<?php echo $completed_trips; ?>]
    
            }]
        });
    });
</script>
<?php } ?>
