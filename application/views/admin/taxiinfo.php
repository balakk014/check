<?php defined('SYSPATH') OR die("No direct access allowed."); 
$field_count = count($additional_fields);

?>

<?php if(SHOW_MAP !=1 ) { ?>
	<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&sensor=false&v=3.25"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/easyslider/bjqs-1.3.min.js"></script>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<link href="<?php echo URL_BASE;?>public/common/js/easyslider/bjqs.css" rel="stylesheet" type="text/css" media="screen" />	
<style>
ul.bjqs-controls.v-centered li a:hover {
background: #000;
color: #fff;
}
ul.bjqs-controls.v-centered li a {
display: block;
padding: 5px;
background: #fff;
color: #000;
text-decoration: none;
}
</style>	

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
          $('#on_going_place').html("My Current Location : "+results[1].formatted_address);
         }
      } else {
        //alert("Geocoder failed due to: " + status);
      } 
   });
   
   
        
}

</script>
<?php } ?>
<?php 
if($taxi_driver){
	
?>
<script type="text/javascript">
push_notification('<?php echo $user_details1[0]['id']; ?>');
setInterval(function() 
{
	push_notification('<?php echo $user_details1[0]['id']; ?>');
	   
}, 10000);


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
						$('#on_going_place').html("My Current Location : "+results[1].formatted_address);
						}
						}			
						else if (status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
						setTimeout(function() {
						codeLatLng(lat,lng,id);
						}, 200); 
					}
					else {
					//alert('Geocoder failed due to: ' + status);
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
<?php 
}
?>
<div class="container_content fl clr">
    <div class="cont_container mt15">
       <div class="content_middle">
        <div class="taxi_info_common">
            <h2 class="tab_sub_tit"><?php echo $taxi_details[0]['taxi_no'].'   -  '.ucfirst(__('taxi_inform')); ?></h2>
                 <ul>       
                      

	<li><label><?php echo __('taxi_no'); ?></label>
            <p><?php if(isset($taxi_details[0]['taxi_no'])) { echo $taxi_details[0]['taxi_no']; } else { echo ''; } ?></p>
	</li> 
	
	<li><label><?php echo __('taxi_type'); ?></label>
            <p><?php if(isset($taxi_details[0]['motor_name'])) { echo $taxi_details[0]['motor_name']; } else { echo ''; } ?></p>
	</li>

	<li><label><?php echo __('taxi_model'); ?></label>
            <p><?php if(isset($taxi_details[0]['model_name'])) { echo $taxi_details[0]['model_name']; } else { echo ''; } ?></p>
	</li>
	
	<?php /*<tr>
	<td valign="top" width="20%"><label><?php echo __('taxi_capacity'); ?></label></td>        
	<td>
	<div class="new_input_field">
	<?php if(isset($taxi_details[0]['taxi_capacity'])) { echo $taxi_details[0]['taxi_capacity']; } else { echo ''; } ?>
	</div>
	</td>   	
	</tr> */ ?>
		
	<li><label><?php echo __('taxi_speed'); ?></label>
            <p><?php if(isset($taxi_details[0]['taxi_speed'])) { echo $taxi_details[0]['taxi_speed']; } else { echo ''; } ?></p>
	</li>
 	<li><label><?php echo __('maximum_luggage'); ?></label>
            <p><?php if(isset($taxi_details[0]['max_luggage'])) { echo $taxi_details[0]['max_luggage']; } else { echo ''; } ?></p>
	</li>          
            <?php
            if($field_count > 0)
            {
		    for($i=0; $i<$field_count; $i++)
		    { 
		    	$field_name = $additional_fields[$i]['field_name']; ?>
			<li><label><?php echo $additional_fields[$i]['field_labelname']; ?></label>
                        <p><?php if(isset($taxi_details[0][$field_name]) && $taxi_details[0][$field_name]!='') { echo $taxi_details[0][$field_name]; } else { echo 'Not Specified'; } ?></p>
		 <?php }
	                ?>
	                </li>
	<?php	
            }
            ?>    
                 </ul>
		</div>
                <div class="taxi_info_rgt">
	<?php $result = $taxi_details;
	if(count($result) > 0)
			{
				if(isset($result[0]) && count($result[0]) > 0)
				{
					$output ='<ul class="bjqs">';
					
					$taxi_image = $_SERVER['DOCUMENT_ROOT'].'/'.TAXI_IMG_IMGPATH.$result[0]['taxi_image'];
					if(file_exists($taxi_image) && $result[0]['taxi_image'] !='')
					{
					$taxi_image = URL_BASE.TAXI_IMG_IMGPATH.$result[0]['taxi_image'];
					}else{
					$taxi_image = URL_BASE."/public/".UPLOADS."/taxi_image/no-image.jpg";
					}
					$output .='<li><img src="'.$taxi_image.'" ></li>';	
		
					$count = isset($result[0]['taxi_sliderimage'])?$result[0]['taxi_sliderimage']:0;
					$serialize_count = isset($result[0]['taxi_serializeimage']) ? unserialize($result[0]['taxi_serializeimage']):'';
					$taxi_id = $result[0]['taxi_id'];
					$j = 0;
					if(is_array($serialize_count))
					{
						foreach($serialize_count as $value)
						{
							if(file_exists($_SERVER["DOCUMENT_ROOT"].'/public/'.UPLOADS.'/taxi_image/'.$taxi_id.'_'.$value.'.png'))
							{ 
							$image_path = URL_BASE.'/public/'.UPLOADS.'/taxi_image/'.$taxi_id.'_'.$value.'.png';
							$output .='<li><img src="'.$image_path.'" ></li>';	
							}						
						
						}

					}
					$output .='</ul>';

					
					echo "<div id='banner-fade'>".$output."</div>";
				}  
			} 	?>
			</div> 

		<!--- Transaction Chart--->
                <div class="over_all">
			<div class="widget margin-bottom comp_journy" >
			<div class="title ">
			<h6><?php echo __('transactions'); ?></h6>
				<?php $startdate = date('Y-m-01 00:00:00');
		$enddate  = date('Y-m-t 12:59:59'); $display="";?>
			 <form  action="" method="post" name="" id="trans_search_form" >
			 <div class="rgt_field_det" align="right">
				<div class="small_field_det">
                                    <label><?php echo __('startdate');?></label>
				 <input type="hidden" name="user_id" id="user_id" value="<?php echo $taxi_details[0]['taxi_id']; ?>">
				 <input type="hidden" name="user_name" id="user_name" value="<?php  echo $taxi_details[0]['motor_name']; ?>">
				  <input type="hidden" name="user_type" id="user_type" value="D">
				 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="transstartdate" name="transstartdate" value="<?php echo $startdate; //Commonfunction::getDateTimeFormat($startdate,1); ?>"  />
				 <span id="tstartdate_error" class="errors" style="display:none;"></span>
                                </div>
                             <div class="small_field_det">
                                 <label><?php echo __('enddate');?></label>
				 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="transenddate" name="transenddate" value="<?php echo $enddate; //Commonfunction::getDateTimeFormat($enddate,1); ?>"  />
				 <span id="tenddate_error" class="errors" style="display:none;"></span>
                             </div>
				 <div class="small_butt"> 
				 <input type="hidden" name="type_export" id="type_export" value="">
				 <input type="button" name="search_transaction" id="search_transaction" value="GO" title="Go" >
				 
				 </div>
				 </div>				
			</div>
			</form>
			
		<div id="driver_transactions">
			<?php  if($display == 'display:none;'){ echo "<div class='no_data'>".__('no_data')."</div>"; } ?>
		<div id="transaction_chart" style="min-width: 400px; height: 400px; margin: 0 auto;<?php echo $display;?>"></div>
		</div>
		
		</div>
                </div>
		
		<!--- Transaction Chart--->

		<!--Current Status of The Taxi -->
		<div class="widget margin-bottom" >
			<div class="title"><h6><?php echo __('Current Status of Taxi'); ?></h6>
				<div style="width:auto; float:right; margin: 0;">		
				          
					
				</div>
			</div>
			<div id="on_going_trip_map" >
				<?php 
				if($taxi_driver){
				?>
				<div class="ongoing">
					<div id="on_going_trip"></div>
					<div id="on_going_place"></div>
				</div>
				<?php if(SHOW_MAP !=1) { ?>
					<div id="map-canvas" style="width:100%;height:250px;"></div>
					<?php } ?>
				<?php }else{
					echo "<div class='no_data'>".__('no_data')."</div>";
				} ?>
			</div>
		</div>
		<!--Current Status of The Driver -->
		
			<!-- Last Three Trips taxi Map -->
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
	$driver_latlog = '['.$driver_tracking[0]['active_record'].']';
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
	$driver_latlog1 = '['.$driver_tracking[1]['active_record'].']';
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
	$driver_latlog2 = '['.$driver_tracking[2]['active_record'].']';
	$driver_default2 = explode(',',$driver_tracking[2]['active_record']);
	$count =  count($driver_default2);

	$driver_endlat2 = "";
	$driver_endlng2 = "";
	$driver_centerlat2 = str_replace('[','',$driver_default2[0]);
	$driver_centerlng2 = str_replace(']','',$driver_default2[1]);
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
          console.log(e);
        }
      });

	 var iconBase = '<?php echo PUBLIC_IMGPATH.'/' ; ?>';
   /** Make Marker for three paths **/

	<?php if(isset($driver_tracking[0]['active_record']) && !empty($driver_tracking[0]['active_record'])){ ?>

      map.addMarker({
        lat: <?php echo $driver_centerlat; ?>,
        lng: <?php echo $driver_centerlng; ?>,
       // title: 'Start Point-1',
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
        //title: 'End Point-1',
        title: '<?php echo $driver_tracking[0]['drop_location']; ?>',
	icon: iconBase + 'driver_one.png',
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
        title: '<?php echo $driver_tracking[1]['current_location']; ?>',
	icon: iconBase + 'driver_two.png',
        details: {
          database_id: 421,
          author: 'HPNeo1'
        },

      });
      <?php if(!empty($driver_endlat1) && !empty($driver_endlng1)) { ?>
      map.addMarker({
        lat: <?php echo $driver_endlat1; ?>,
        lng: <?php echo $driver_endlng1; ?>,
        title: '<?php echo $driver_tracking[1]['drop_location']; ?>',
	icon: iconBase + 'driver_two.png',
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
        title: '<?php echo $driver_tracking[2]['drop_location']; ?>',
	icon: iconBase + 'driver_three.png',
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
	/** Create Path and its colour's for three paths ends **/

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
	<!-- Last Three Trips Taxi Map ends -->

		

		<!-- Completed  Journey -->
		<div class="widget margin-bottom">
			<div class="title">
			<h6><?php echo __('Completed Journey'); ?></h6>
				<div style="width:auto; float:right; margin: 4px 3px;">		
				<?php if($count_taxi_logs_completed_transaction > REC_PER_PAGE) {  ?>
					<?php if($_SESSION['user_type'] == 'A') { ?> 
					<div class="small_butt">
                                            <a class="export_me_menu" href="<?php echo URL_BASE ?>transaction/admintransaction_list/all?filter_company=All&manager_id=All&taxiid=<?php echo $taxi_details[0]['taxi_id']; ?>&startdate=&enddate=&transaction_id=&payment_type=All&payment_mode=All&passengerid=All&driver_id=All"><?php echo __('view_all'); ?></a>
					</div>
					<?php } 
					else if($_SESSION['user_type'] == 'C')
					{ 
						//<?php echo date('Y-m-d'); 23:59:59?>
					<div class="small_butt">
					<a class="export_me_menu" href="<?php echo URL_BASE ?>transaction/companytransaction_list/all/?manager_id=All&taxiid=<?php echo $taxi_details[0]['taxi_id']; ?>&driver_id=All&passengerid=All&startdate=&enddate=&transaction_id=&payment_type=All&payment_mode=All&search_user="><?php echo __('view_all'); ?></a>
					</div>
					<?php }
					else if($_SESSION['user_type'] == 'M')
					{ 
					?>
					<div class="small_butt"><a class="export_me_menu" href="<?php echo URL_BASE; ?>transaction/managertransaction_list/all/?taxiid=<?php echo $taxi_details[0]['taxi_id']; ?>&driver_id=All&passengerid=All&transaction_id=&startdate=&enddate=&payment_type=All&payment_mode=All&search_user="><?php echo __('view_all'); ?></a></div>
					<?php }
				}
				else
				{ ?>            
				<div class="button greyish"></div>
				<?php } ?>
				</div>
			</div>
                    <div class="overflow-block" style="float:none;display:block;">
				
					<?php if(count($taxi_logs_completed_transaction)>0) { ?>
					<table cellspacing="1" cellpadding="11" width="100%" align="center" class="sTable responsive">
						
						<thead>
						<tr>
							<td>#</td>
							<td><?php echo __('passenger_name'); ?></td>
							<td><b><?php echo __('Current_Location'); ?></b></td>
							<td><b><?php echo __('Drop_Location'); ?></b></td>
							<!--<td><b><?php echo __('No_Passengers'); ?></b></td>-->
							<td><b><?php echo __('pictup_date');?></b></td>
							<?php /* <td><b><?php echo __('pictup_time');?></b></td> */ ?>
							<td><b><?php echo __('distance');?></b></td>
							<td><b><?php echo __('trip_total_fare');?></b></td>
							<?php if($_SESSION['user_type'] == 'A') { ?> <td><b><?php echo __('equivalent_to_usd').CURRENCY_FORMAT;?></b></td> <?php } ?>
							</tr>
						</tr>
						</thead>
						<?php 
						$i=1;
						($i%2 == 1)?$class="eventr":$class="oddtr";
					
							foreach($taxi_logs_completed_transaction as $values)
							{
								$distance = round($values->distance,2).' '.$values->distance_unit;
								$current_fare = round($values->fare,2);
								$fare_km = 0;

								if(isset($values->fare) && ($current_fare > 0) && ($distance > 0 ))
								{
									$fare_km = $current_fare/$distance;
								}									
								?>
								<tr class="<?php echo $class; ?>">	
									<td><?php echo $i;?></td>
								<td><?php echo ucfirst($values->name); ?></td>
								<td><?php echo $values->current_location;?></td>
								<td><?php echo $values->drop_location;?></td>
								<!--<td><?php echo $values->no_passengers;?></td>-->
								<td><?php echo Commonfunction::getDateTimeFormat($values->pickup_time,1);?></td>
								<?php /* <td><?php echo date('h:i:s', strtotime($values->pickup_time));?></td> */ ?>
								<td><?php echo $distance;?></td>
								<td><?php echo $company_currency.$current_fare;?></td>
								<?php if($_SESSION['user_type'] == 'A') { ?>
								 <td>
									<?php $convet_amt = currency_conversion($company_currency_format,$current_fare);
									echo round($convet_amt,2); ?>
								</td>
								 <?php } ?>
								
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
		
		<!-- Completed Journey -->
				
		<!-- Service Time  Journey -->
               
		<div class="widget pb10">
			<div class="title">
			<h6><?php echo __('service_time'); ?></h6>
				<div style="width:auto; float:right; margin: 4px 3px;">		
				<?php if($count_taxi_logs_service > REC_PER_PAGE) {  ?>
				<div class="button greyish"><a href="<?php echo URL_BASE; ?>manage/taxilogs/<?php echo $taxi_details[0]['taxi_id']; ?>"><?php echo __('view_all'); ?></a></div>
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
				
					<?php if(count($taxi_logs_service)>0) { ?>
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						
						<thead>
						<tr>
							<td>#</td>
							<td><?php echo __('driver_name'); ?></td>
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
							foreach($taxi_logs_service as $values)
							{ ?>
								<tr class="<?php echo $class; ?>">	
								<td><?php echo $i;?></td>
								<td><?php echo ucfirst($values->name); ?></td>
								<td><?php 
								if($values->interval_type =='B') 
								{ echo __('break'); }
								else if($values->interval_type =='S') 
								{ echo __('service'); } ?></td>
								<td><?php echo Commonfunction::getDateTimeFormat($values->interval_start,1); ?></td>
								<td><?php echo Commonfunction::getDateTimeFormat($values->interval_end,1); ?></td>
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
		
        </div>
    </div>
</div>
<script src="<?php echo SCRIPTPATH; ?>highcharts.js"></script>
<script>
	jQuery(document).ready(function($) {

$("#transstartdate").datetimepicker( {
	showTimepicker:DEFAULT_TIME_SHOW,
	showSecond: true,
	timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(),
	stepSecond: 1
});

$("#transenddate").datetimepicker( {
	showTimepicker:DEFAULT_TIME_SHOW,
	showSecond: true,
	timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
	dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(), 
	stepSecond: 1
});

	  $('#banner-fade').bjqs({
		height      : 250,
		width       : 400,
			left	: 20,	
		responsive  : true
	  });

	onload_chart();	

	});

	function onload_chart()
	{ 
		var startdate = $("#transstartdate").val();
		var enddate = $("#transenddate").val();
		var driver_id = $("#user_id").val();
		var taxi_name = $("#user_name").val();
		var dataS = "startdate="+startdate+"&enddate="+enddate+"&driver_id="+driver_id+"&taxi_name="+taxi_name;
		$.ajax({
			type: "POST",
			url: SrcPath+"manage/taxi_transaction_search", 
			data: dataS, 
			cache: false, 
			success: function(response) 
			{
				var datArr = response.split('~');
				var transCount = datArr[1];
				$('#driver_transactions').html(datArr[0]);
				if(transCount == 0) {
					$("#trans_search_form").hide();
				}
			}
		});
	}

	 $("#search_transaction").click(function(){

 	var startdate = $("#transstartdate").val();
	var enddate = $("#transenddate").val();
	var driver_id = $("#user_id").val();
	var taxi_name = $("#user_name").val();
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
		$('#driver_transactions').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');	
		if(to_timestamp(startdate) > to_timestamp(enddate)) 
		{
			$("#tstartdate_error").html("<?php echo __('startdate_greater'); ?>");
			$("#tstartdate_error").show();
		}
		else
		{
			$("#tstartdate_error").html("");
			$("#tstartdate_error").hide();
		var dataS = "startdate="+startdate+"&enddate="+enddate+"&driver_id="+driver_id+"&taxi_name="+taxi_name;
		//var dataS = "{'startdate':'"+startdate+"','enddate':'"+enddate+"','driver_id':'"+driver_id+"'}";alert(dataS);
		$.ajax
		({ 			
			type: "POST",
			url: SrcPath+"manage/taxi_transaction_search", 
			data: dataS, 
			cache: false, 
			dataType: 'html',
			success: function(response) 
			{ 	
				var datArr = response.split("~");
				$('#driver_transactions').html(datArr[0]);			
			} 
			 
		});	
			
		}
	}
	 
 });
</script>
