<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<script src="https://maps.google.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&libraries=places,geometry" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo SCRIPTPATH ?>gmaps.js"></script>
<script type="text/javascript">	
    $(document).ready(function(){
		
	   $('#fade').hide();
	   var licount = $(".general_tab_outer ul li").length;
		//to reset the pickup location and drop location fields if there is no local storage
		var locVals = localStorage.getItem("book_again_form");
		if(locVals == null){
			  $('#pickup_location').val('');
			  $('#drop_location').val('');
		}
	   if(licount > 4){
		   var minLiWidth = 550 + (parseInt(licount - 4) * 116);
		   $(".general_tab_outer ul").css('width',minLiWidth+'px');
		   $(".banner_tab").css('margin-top','25px');
	   }
	   $('.general_tab_outer ul li a').removeClass('active');
	   $(".general_tab_outer ul li:first a").addClass("active");
	   var modelAll = $("#modelids").val();
	   var allMdlArr = modelAll.split(",");
	   
	   $.each(allMdlArr, function( index, value ) {
			var thumb_image = "<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/model_image/thumb_"+value+".png";
			var thumb_act_image = "<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/model_image/thumb_act_"+value+".png";
		  if(index != 0) {
			  $('.taxi_ico_'+value).html('<img src="'+thumb_image+'">');
		  } else {
			   $('.taxi_ico_'+value).html('<img src="'+thumb_act_image+'">');
		  }
		});
		
	   $('.general_tab_outer ul li a').click(function(){
			var car_img = $(this).attr('id');
			$('.car_img div').hide();
			$('.'+car_img).show();
			$('.general_tab_outer ul li a').removeClass('active');
			$(this).addClass('active');
			//to show the model thumb image
			var mdlArr = car_img.split('_');
			var orderId = mdlArr[1];
			var modelId = $("#modelId_"+orderId).val();
			$.each(allMdlArr, function( index, value ) {
				var thumb_image = "<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/model_image/thumb_"+value+".png";
				var thumb_act_image = "<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/model_image/thumb_act_"+value+".png";
			  if(modelId != value) {
				 // $('.taxi_ico_'+value).css('background-image', 'url(' + thumb_image + ')');
				   $('.taxi_ico_'+value).html('<img src="'+thumb_image+'">');
			  } else {
				 // $('.taxi_ico_'+value).css('background-image', 'url(' + thumb_act_image + ')');
				  $('.taxi_ico_'+value).html('<img src="'+thumb_act_image+'">');
			  }
			});
			
	   });
	   	   
	   $(".general_tab_outer ul li a").hover(function(){
				var car_img = $(this).attr('id');
				var mdlArr = car_img.split('_');
				var orderId = mdlArr[1];
				var modelId = $("#modelId_"+orderId).val();
				var thumb_act_image = "<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/model_image/thumb_act_"+modelId+".png";
				$('.taxi_ico_'+modelId).html('<img src="'+thumb_act_image+'">');
			}, function(){
				var car_img = $(this).attr('id');
				var mdlArr = car_img.split('_');
				var orderId = mdlArr[1];
				var modelId = $("#modelId_"+orderId).val();
				var thumb_image = "<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/model_image/thumb_"+modelId+".png";
				var thumb_act_image = "<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/model_image/thumb_act_"+modelId+".png";
				$('.taxi_ico_'+modelId).html('<img src="'+thumb_image+'">');
				$('.active .taxi_ico_'+modelId).html('<img src="'+thumb_act_image+'">');
		});
                
        $("#country_code" ).keyup(function(event) {
			//to allow left and right arrow key move
			if(event.which>=37 && event.which<=40)
			{
				return false;
			}
			this.value = this.value.replace(/[`~!@#$%^&*()\s_|\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
		});
         
        //time picker for pickuptime
        $("#pickup_time").datetimepicker({
			showTimepicker:true,
			showSecond: true,
			timeFormat: 'hh:mm:ss tt',
			dateFormat: 'yy-mm-dd',
			stepHour: 1,
			stepMinute: 1,
			minDateTime : new Date(),
			ampm: true,
			stepSecond: 1
		});
		
		//first name, lastname
		$("#pickup_location,#drop_location,#landmark").keyup(function(event) {
			//to allow left and right arrow key move and backspace, delete buttons
			if((event.which>=37 && event.which<=40) || event.which==8 || event.which==46)
			{
				return false;
			}
			this.value = this.value.replace(/[`~!@#$%^&*()_|+\=?;:'"<>\{\}\[\]\\]/gi, '');
		});
    });
</script>
 <script type="text/javascript">
   $(document).ready(function(){
      $('body').addClass('bookingpage');
   });
</script>
<!-- banner start -->
<div class="banner_outer">
	<div class="inner">
		<h1><?php echo __('you_have_choice_msg'); ?></h1>
		<h2><?php echo __('and_any_occasion'); ?></h2>
		<div class="banner_tab general_tab_outer">
                    <div class="tab_scroll">
			<ul>
				<?php /*<li><a id="tab_1" class="active" href="javascript:;" title="Taxi"><i class="taxi_ico"></i><span>Taxi</span></a></li>
				<li><a id="tab_2" href="javascript:;" title="SUV"><i class="taxi_ico_1"></i><span>SUV</span></a></li>
				<li><a id="tab_3" href="javascript:;" title="LUX"><i class="taxi_ico_2"></i><span>LUX</span></a></li> */ ?> 
				<?php 
				if(count($getmodel_details) > 0) {
					$tabId = 1;
					$modelIdsArr = array();
					foreach($getmodel_details as $models){ 
						$modelIdsArr[] = $models['model_id'];
						?>
						<li><a id="tab_<?php echo $tabId; ?>" href="javascript:;" title="<?php echo $models['model_name']; ?>" onclick="return getFareDets('<?php echo $models['model_id']; ?>','','');"><i class="taxi_ico_<?php echo $models['model_id']; ?>"></i><span><?php echo $models['model_name']; ?></span><input type="hidden" name="model_id" id="modelId_<?php echo $tabId; ?>" value="<?php echo $models['model_id']; ?>"></a></li>
				<?php
						$tabId++;
					}
					$modelIds = implode(",",$modelIdsArr); ?>
					<input type="hidden" name="modelids" id="modelids" value="<?php echo $modelIds; ?>">
				<?php }
				?>
			</ul>
                    </div>
			<div id="availDrivers"></div>
			<div class="taxi_tab_images car_img">
				<?php /*<div class="tab_1"><img alt="taxi image" src="<?php echo URL_BASE; ?>public/frontend/logged_in/images/taxi_big.png"/></div>
				<div class="tab_2" style="display: none;"><img alt="SUV image" src="<?php echo URL_BASE; ?>public/frontend/logged_in/images/suv_big.png"/></div>
				<div class="tab_3" style="display: none;"><img alt="LUX image" src="<?php echo URL_BASE; ?>public/frontend/logged_in/images/lux_big.png"/></div> */ ?>
				<?php 
				if(count($getmodel_details) > 0) {
					$tabId = 1;
					foreach($getmodel_details as $models){
						$style = '';
						 if($tabId != 1) {
							 $style = 'style="display:none"';
						 }
						?>
						<div class="tab_<?php echo $tabId; ?>" <?php echo $style; ?> ><img alt="<?php echo $models['model_name']; ?> image" src="<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/model_image/large_<?php echo $models['model_id']; ?>.png"/></div>
				<?php
						$tabId++;
					}
				}
				?>
				
			</div>
		</div>
	</div>
</div>
<!-- banner end -->
    
<div class="taxi_costdet">
	<div class="inner">
            <div class="scroll_tab">
            <table>
			<tr>
				<th><?php echo __('base_fare'); ?></th>
				<th><?php echo __('min_fare'); ?> <span id="min_km"></span></th>
				<th><?php echo __('below'); ?> <span id="below_km"></span></th>
				<th><?php echo __('above'); ?> <span id="above_km"></span></th>
				<th><?php echo __('night_charge'); ?> <span id="night_chargeapp"></span></th>
				<th><?php echo __('evening_charge'); ?> <span id="eve_chargeapp"></span></th>
				<th><?php echo __('cancel_fare'); ?></th>
			</tr>
			<tr>
				<td id="base_fare"></td>
				<td id="min_fare"></td>
				<td id="below_fare"></td>
				<td id="above_fare"></td>
				<td id="night_fare_percent"></td>
				<td id="eve_fare_percent"></td>
				<td id="cancel_fare"></td>
			</tr>
		</table>
            </div>
	</div>
</div>
    
    
<!-- taxi booking -->
<div class="book_taxi">
	<div class="inner">
		<h2><?php echo __('book_a_taxi'); ?></h2>
		<h3><?php echo __('and_any_occasion'); ?></h3>
		<form name="bookingForm" id="bookingForm" method="post" enctype="multipart/form-data">
			<ul>
				<li><div class="full_name"><input type="text" name="name" id="name" readonly placeholder="Name" value="<?php echo $passengerName; ?>"/></div></li>
				<li><div class="mobile_num"><span><input type="text" name="country_code" id="country_code" readonly maxlength="5" placeholder="+1" value="<?php echo $passengerPhCode; ?>" /></span><input type="text" placeholder="<?php echo __('mobile_number'); ?>" name="mobile_number" id="mobile_number" readonly maxlength="15" value="<?php echo $passengerPhone; ?>"/></div></li>
				<li><div class="full_name"><input type="text" placeholder="Email" name="email" readonly id="email" value="<?php echo $passengerEmail; ?>"/></div></li>
				<li><div class="full_name"><input type="text" placeholder="<?php echo __('Current_Location'); ?>" maxlength="100" name="pickup_location" id="pickup_location" value=""/></div></li>
				<li><div class="full_name"><input type="text" placeholder="<?php echo __('Drop_Location'); ?>" maxlength="100" name="drop_location" id="drop_location" value=""/></div></li>
				<li id="pickup_time_fld" style="display:none"><div class="full_name"><input type="text" placeholder="<?php echo __('pickup_time'); ?>"  name="pickup_time" id="pickup_time" value=""/></div></li>
                                <!-- <li><div class="full_name"><input type="text" placeholder="<?php echo __('land_mark'); ?>" maxlength="100" name="landmark" id="landmark" value=""/></div></li> Before modification sureshkumar.M in pack 6.0 -->
                                <li style="display:none !important;"><div class="full_name"><input type="text" placeholder="<?php echo __('land_mark'); ?>" maxlength="100" name="landmark" id="landmark" value=""/></div></li> <!-- Sureshkumar.M modified code, this landmark is unwanted this is convey by QA Sathishkumar sakthivel -->
                                <li><div class="full_name"><input type="text" placeholder="<?php echo __('promocode'); ?>" maxlength="6" name="promocode" id="promocode" value=""/></div></li>
				<input type="hidden" name="model" id="model" value="" />
				<input type="hidden" name="passengerId" id="passengerId" value="<?php echo $passengerId; ?>" />
				<input type="hidden" name="pass_latitude" id="pass_latitude" value="" />
				<input type="hidden" name="pass_longitude" id="pass_longitude" value="" />
				<input type="hidden" name="pickup_latitude" id="pickup_latitude" value="" />
				<input type="hidden" name="pickup_longitude" id="pickup_longitude" value="" />
				<input type="hidden" name="drop_latitude" id="drop_latitude" value="" />
				<input type="hidden" name="drop_longitude" id="drop_longitude" value="" />
				<input type="hidden" name="appx_distance" id="appx_distance" value=""/>
				<input type="hidden" name="appx_amount" id="appx_amount" value=""/>
			</ul>
                    <div class="booking_buttons">
                        <div class="ride_now"><input type="button" onclick="return bookingSub('0');" id="ride_now" value="<?php echo __("ride_now"); ?>"/></div>
                        <div class="ride_later"><input type="button" onclick="return bookingSub('1')" id="ride_later" value="<?php echo __("ride_later"); ?>"/></div>
                        <div class="ride_now"><input type="button" onclick="fare_estimate();" value="<?php echo __("fare_estimate"); ?>"/></div>
                    </div>
		</form>
		
		<!-- foursquare -->
		<link rel="stylesheet" href="<?php echo URL_BASE ?>public/common/css/jquery-ui.css">
		<link rel="stylesheet" href="<?php echo URL_BASE ?>public/common/css/jquery-ui.theme.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="<?php echo SCRIPTPATH ?>jquery-ui.js"></script>
	</div>
</div>
<!-- taxi booking  end-->

<!-- map start -->
	<?php if(SHOW_MAP !=2) { ?>
	<div class="google_map" id="map-canvas" style="width:100%;height:503px;margin:0;">
		<?php /*<img alt="google map" src="<?php echo URL_BASE; ?>public/web_booking/images/map_img.jpg"/> */ ?>
	</div>
	<?php } ?>
	<!-- map end -->
	<div id="fade"></div>
	<div id="show_user_message">
		<div class="message_for_user_purpose">
			<h1><?php echo __('dont_refresh_message'); ?></h1>
			<img width="32" height="32" alt="loader" src="<?php echo URL_BASE;?>public/common/images/loading.gif"/>
		</div>
	</div>

<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
	//web booking Form Submit	
	function bookingSub(bookType)
	{
		$("#pickup_time_fld").hide();
		if(bookType == 1) {
			$("#pickup_time_fld").show();
		}
		var today = new Date();
		var Y = today.getFullYear(),
			month = today.getMonth()+1,
			dateVal = today.getDate(),
			h = today.getHours(),
			m = today.getMinutes(),
			s = today.getSeconds();
			
			month = (month < 10) ? '0'+month : month;
			dateVal = (dateVal < 10) ? '0'+dateVal : dateVal;
		var currentTime = Y + "-" + month + "-" + dateVal + " " + h + ":" + m + ":" + s;
		var form = (window.location.hash) ? localStorage.getItem("book_again_form") : $("form[name='bookingForm']").serialize();
		localStorage.setItem("book_again_form_details", form); // This is sureshkumar.m Worked code because want to get pickup and drop location
		//alert(localStorage.getItem("book_again_form"));
		if(form != null) {  
			$.ajax({
				url:'<?php echo URL_BASE;?>find/bookingSubmit',
				type:'post',
				dataType:'json',
				data:form + "&bookType="+bookType+"&currentTime="+currentTime,
				beforeSend:function() {
					//$('#ride_now, #ride_later').attr('disabled', 'disabled');
					$("input").removeClass('req-field');
					$('#show_user_message').show();
					$(".response_signup_error,.empty_tag").remove();
					if((window.location.hash)) {
						localStorage.removeItem("book_again_form");
					}
				},
				success:function(res)
				{  
					if((window.location.hash)) {
						localStorage.removeItem("book_again_form");
					}
					$("input").removeClass('req-field');
					if(res.error)
					{ 
						$('#show_user_message').hide();
						$.each(res.error,function(k,v){ 
							$("input[name='"+k+"']").addClass('req-field');
							$("form#bookingForm input[name='"+k+"']").after('<em class="response_signup_error error_valids">'+ucFirst(v)+'</em>');
							if(k == "pickup_location" && bookType == 0) {
								$("form#bookingForm input[name='drop_location'],form#bookingForm input[name='landmark']").after('<em class="empty_tag">&nbsp;</em>');
							}
						}); 
						
					}
					//redirect
					if(res.redirect)
					{
						window.location = res.redirect;
					}
					//request screen
					if(res.driverCnt)
					{
						$('#show_user_message').show();
						setTimeout(function(){
						  $('#show_user_message').hide();
						}, res.driverCnt);
					}
				}
			});
		}
	}
	
	
	//to get driver markers
	var bounds = new google.maps.LatLngBounds();
	var markers = [];
	var map; 
	var start;
	var end;
	var autocomplete, toAutocomplete;
	var directionsService = new google.maps.DirectionsService;
	var directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
	
	function initMap()
	{		
		var iconBase = '<?php echo PUBLIC_IMGPATH.'/' ; ?>';
		var modelID = $(".active input").val();
		
		//to show the passenger's current location with marker image
		/* if ("geolocation" in navigator){
				navigator.geolocation.getCurrentPosition(function(position){
					infoWindow = new google.maps.InfoWindow();
					var pos = {lat: position.coords.latitude, lng: position.coords.longitude};
					getFareDets(modelID,position.coords.latitude,position.coords.longitude);
					$("#pass_latitude").val(position.coords.latitude);
					$("#pass_longitude").val(position.coords.longitude);
					map = new google.maps.Map(document.getElementById('map-canvas'), {
						center: pos,
						zoom: 12
					  });
					  
					  marker = new google.maps.Marker({
						position: pos,
						map: map,
						animation: google.maps.Animation.DROP,
						icon: iconBase + 'location_icon.png',
					});
					
				});
		} else {
			console.log("Browser doesn't support geolocation!");
		} */
		  
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				do_something(position.coords);
			},
			function(failure) {
				<?php $global_session->set('set_map_marker','1'); ?>
				var local_path = '<?php echo URL_BASE; ?>map.txt';
				$.getJSON(local_path, function(response) {
					var loc = response.loc.split(',');
					var coords = {
						latitude: loc[0],
						longitude: loc[1]
					};
					do_something(coords);
				});
			});
		}
        
		function  do_something(coords)
		{
			infoWindow = new google.maps.InfoWindow();
			var defaultLatLng = new google.maps.LatLng(coords.latitude, coords.longitude);
			getFareDets(modelID,coords.latitude,coords.longitude);
			$("#pass_latitude").val(coords.latitude);
			$("#pass_longitude").val(coords.longitude);
			map = new google.maps.Map(document.getElementById('map-canvas'), {
				center: defaultLatLng,
				zoom: 12
			});
			directionsDisplay.setMap(map);
				
			marker = new google.maps.Marker({
				position: defaultLatLng,
				map: map,
				title:'some',
				animation: google.maps.Animation.DROP,
				icon: iconBase +  'location_icon.png',
			});			
			markers.push(marker);
        }     
		
		//Auto Suggest for pickup and drop locations
		/** option variable to search the particular country *
		var options = {
			componentRestrictions: {country: MAP_COUNTRY}
		};
		/** google autocomplete functionality in add booking **/
		var options = {types: ['geocode'] };
		/* Restrictions to load only particular country */
		
		autocomplete = new google.maps.places.Autocomplete(document.getElementById('pickup_location'), options);
		toAutocomplete = new google.maps.places.Autocomplete(document.getElementById('drop_location'), options);
		
		google.maps.event.addDomListener(document.getElementById('pickup_location'), 'focus', geolocate);
		google.maps.event.addDomListener(document.getElementById('drop_location'), 'focus', geolocate);
		
		/* get the latitude and longitude of pickup and drop location and saved them in hidden elements */
		google.maps.event.addListener(autocomplete, 'place_changed', function () {
			var pickup = autocomplete.getPlace();//Get a place lat&long
			/***************Get a locationA Latitude and Longitude  ***********/
			document.getElementById('pickup_latitude').value = pickup.geometry.location.lat();//initialized latitude
			document.getElementById('pickup_longitude').value = pickup.geometry.location.lng();//initialized longitude
			/***************End of Get a locationA Latitude and Longitude ***********/
			/* Route draw function */			
			//calculateAndDisplayRoute(directionsService, directionsDisplay);	
		});
		//drop location
		google.maps.event.addListener(toAutocomplete, 'place_changed', function () {
			var droploc = toAutocomplete.getPlace();//Get a place lat&long
			/***************Get a locationA Latitude and Longitude  ***********/
			document.getElementById('drop_latitude').value = droploc.geometry.location.lat();//initialized latitude
			document.getElementById('drop_longitude').value = droploc.geometry.location.lng();//initialized longitude
			/***************End of Get a locationA Latitude and Longitude ***********/
			var pickup_location = $("#pickup_location").val();
			var drop_location = $("#drop_location").val();
			show_fare('',pickup_location,drop_location,modelID,'');
			/* Route draw function */
			//calculateAndDisplayRoute(directionsService, directionsDisplay);
		});	
	}	
	
	function geolocate() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function (position) {
				var geolocation = new google.maps.LatLng(
				position.coords.latitude, position.coords.longitude);
				var circle = new google.maps.Circle({
					center: geolocation,
					radius: position.coords.accuracy
				});
				autocomplete.setBounds(circle.getBounds());
				// Log autocomplete bounds here
				console.log(autocomplete.getBounds());
			});
		}
	}
		
	//function to remove markers from the marker
	function removeMarkers() { 
		for (var i = 0; i < markers.length; i++ ) {  
			markers[i].setMap(null);  
		}  
		markers.length = 0;  
	}
	
	function calculateAndDisplayRoute(directionsService, directionsDisplay) {

		var iconBase = '<?php echo PUBLIC_IMGPATH.'/' ; ?>';
		var icons = {
			start: new google.maps.MarkerImage(
				// URL
				iconBase + 'pickuplocation.png',
				// (width,height)
				new google.maps.Size( 25, 25 ),
				// The origin point (x,y)
				new google.maps.Point( 0, 0 ),
				// The anchor point (x,y)
				new google.maps.Point( 0, 25 )
			),
			end: new google.maps.MarkerImage(
				// URL
				iconBase + 'droplocation.png',
				// (width,height)
				new google.maps.Size( 25, 45 ),
				// The origin point (x,y)
				new google.maps.Point( 0, 0 ),
				// The anchor point (x,y)
				new google.maps.Point( 10, 45 )
			)
		};
		
		var pickup = document.getElementById('pickup_location').value;
		var drop = document.getElementById('drop_location').value;
				
		directionsService.route({
			origin: pickup,
			destination: drop,
			travelMode: 'DRIVING'
		}, function(response, status) {
			if (status === 'OK') {
				removeMarkers();
				directionsDisplay.setDirections(response);
				var leg = response.routes[ 0 ].legs[ 0 ];
				makeMarker( leg.start_location, icons.start, "title" );
				makeMarker( leg.end_location, icons.end, 'title' );
				
			} else {
			//~ window.alert('Directions request failed due to ' + status);
			}
		});
	}
	// Custome marker for pickup & drop locations
	function makeMarker( position, icon, title ) {
		var newmarker = new google.maps.Marker({
			position: position,
			map: map,
			icon: icon,
			title: title
		});
		markers.push(newmarker);
		newmarker;
	}	
	
	function getFareDets(modelID,passLat,passLong)
    {
		if(passLat == '' && passLong == '') {
			var pass_latitude = $("#pass_latitude").val();
			var pass_longitude = $("#pass_longitude").val();
		} else {
			var pass_latitude = passLat;
			var pass_longitude = passLong;
		}
		
		//assign the selected model in the hidden element
		$("#model").val(modelID);
		$.ajax({
			url:"<?php echo URL_BASE;?>find/getModelFareDets",
			type:"post",
			data:"modelID="+modelID+"&passlat="+pass_latitude+"&passlong="+pass_longitude,
			success:function(data){
				var res = $.parseJSON(data);
				$.each(res, function(i, val) {
					if(i==0) {
						$("#min_km").html(val.min_km);
						$("#below_km").html(val.below_above_km);
						$("#above_km").html(val.below_above_km);
						var nighChApp = (val.night_charge == 1) ? '(Yes)' : '(No)';
						var eveChApp = (val.evening_charge == 1) ? '(Yes)' : '(No)';
						$("#night_chargeapp").html(nighChApp);
						$("#eve_chargeapp").html(eveChApp);
						$("#base_fare").html(val.base_fare);
						$("#min_fare").html(val.min_fare);
						$("#below_fare").html(val.below_km);
						$("#above_fare").html(val.above_km);
						$("#night_fare_percent").html(val.night_fare+"%");
						$("#eve_fare_percent").html(val.evening_fare+"%");
						$("#cancel_fare").html(val.cancellation_fare);
					}
				});
				if(res.drivers_count > 0) {
					$("#availDrivers").html(res.drivers_count+" <?php echo __('car_avail'); ?>");
				} else {
					$("#availDrivers").html("<?php echo __('no_cars_avail'); ?>");
				}
				//set free status driver markers in map
				driverMarkers = res.driver_list[0];
				if(driverMarkers.length > 0)
				{
					for( i = 0; i < driverMarkers.length; i++ ) { 
						// Display multiple markers on a map
						var infoWindow = new google.maps.InfoWindow(), marker, i;
						// Loop through our array of markers & place each one on the map
						var position = new google.maps.LatLng(driverMarkers[i][0], driverMarkers[i][1]);
						bounds.extend(position);
						marker = new google.maps.Marker({
							position: position,
							map: map,
							animation: google.maps.Animation.DROP,
							icon: driverMarkers[i][3],
						});
						markers.push(marker);
						// Allow each marker  to have an info window
						google.maps.event.addListener(marker, 'click', (function(marker, i) {
							return function() {
								infoWindow.setContent(driverMarkers[i][2]);
								infoWindow.open(map, marker);
							}
						})(marker, i));
						// Automatically center the map fitting all markers on the screen
						//map.fitBounds(bounds);
					}
					
				} else {
					//~ removeMarkers();
				} 
			},
			error:function(data)
			{
				
			}
		});
	}
 
	google.maps.event.addDomListener(window, "load", initMap);
	
	/** Function to get first character in upper case **/
	function ucFirst(string) {
		return string.substring(0, 1).toUpperCase() + string.substring(1).toLowerCase();
	}
	
	/** Get fare estimate start **/

	$(document).ready( function () {
		$('.close_butt, #fade, #closebutton').click(function(){
			$('.fare_estimate_popup, #fade').hide();
		});		
		if(window.location.hash) {
			bookingSub(0);
		}
	});

	function fare_estimate()
	{
		var modelID = $(".active input").val();
		var modelName = $(".active span").text();
		var pickup_location = $("#pickup_location").val();
		var drop_location = $("#drop_location").val();

		var drop_latitude = $("#drop_latitude").val();
		var drop_longitude = $("#drop_longitude").val();
		if(pickup_location != "" && drop_location != "") {
			$("#couldnot_find_address").hide();
			$("#fade, .fare_estimate_popup").show();
			var geocodingAPI = "https://maps.googleapis.com/maps/api/geocode/json?latlng="+drop_latitude+","+drop_longitude+"&key=<?php echo GOOGLE_MAP_API_KEY; ?>";
			var city;
			$.getJSON(geocodingAPI, function (json) {
				if (json.status == "OK") {
					//Check result 0
					var result = json.results[0];
					//look for locality tag and administrative_area_level_1
					var state = "";
					for (var i = 0, len = result.address_components.length; i < len; i++) {
						var ac = result.address_components[i];
						if (ac.types.indexOf("administrative_area_level_1") >= 0) state = ac.short_name;
						if (ac.types.indexOf("administrative_area_level_2") >= 0) city = ac.short_name;
					}
					if (city != '') {
						city = city;
					}
				}
				show_fare(city,pickup_location,drop_location,modelID,modelName);
			});
		} else {
			alert("<?php echo __('pickup_drop_location_required'); ?>");
			return false;
		}
	}
	
	function show_fare(city,pickup_location,drop_location,modelID,modelName)
	{
		$("#distanceinfo").hide();
		$.ajax({
			url: "<?php echo URL_BASE;?>users/fare_estimate",
			type: "post",
			data: "city_name="+ city +"&pickup_location="+ pickup_location +"&drop_location="+ drop_location +"&modelID="+ modelID,
			beforeSend:function() {
				$("#trip_estimate_distance, #trip_estimate_fare,#modelImg,#model_name_lbl").html('<img src="<?php echo URL_BASE."public/frontend/logged_in/images/loading.gif"; ?>" alt="Loading..."/>');
			},
			success:function(data) {
				var json = $.parseJSON(data);
				
				$("#modelImg").html('<a href="javascript:;"><img src="<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/model_image/thumb_act_'+modelID+'.png"><span>'+modelName+'</span></a>');
				if(json.address_not_find) {
					$("#distanceinfo").hide();
					$("#modelImg").hide();
					$("#couldnot_find_address").show();
					$("#address_not_find").html(json.description);
				}else{
					$("#distanceinfo").show();
					$("#modelImg").show();
					$("#trip_estimate_distance").html(json.distance);
					$("#trip_estimate_fare").html('<?php echo CURRENCY; ?>'+json.total_fare);
					$("#model_name_lbl").html(modelName);
					$("#appx_distance").val(json.distance);
					$("#appx_amount").val(json.total_fare);
					$("#model_description").html(json.description);
				}
			},
			error:function(data) {
				
			}
		});
	}

	/** Get fare estimate End **/

</script> 
<?php
if($global_session->get('set_map_marker') != "") { 
	//$ipAddress = "103.244.10.56";
	//$ipAddress = "203.223.189.94";
	$ipAddress = $_SERVER['REMOTE_ADDR']; 
	$query = "http://api.ipinfodb.com/v3/ip-city/?key=" . IPINFOAPI_KEY . "&ip=" . $ipAddress . "&format=json"; 
	$json = @file_get_contents($query);
	$data = json_decode($json, true);
	if ($data['statusCode'] == "OK") {
		$lat_long = $data['latitude'].",".$data['longitude'];
		$array = array("ip" => $ipAddress,"loc" => $lat_long);
		$map_result = json_encode($array);
	}  
	$my_file = 'map.txt';  
	$handle = fopen($my_file, 'wb') or die('Cannot open file:  '.$my_file); 

	 // here is issue by sureshkumar.m
	if(isset($map_result)) {
		fwrite($handle, $map_result);
		fclose($handle);
	} 
	$global_session->delete('set_map_marker');
}
?> 
<div class="fare_estimate_popup" style="display: none;">
	<h3><?php echo __('fare_estimate'); ?></h3>
	<a href="javascript:;" class="close_butt">&nbsp;</a>
	<div class="fare_estimation_details">
		<ul id="distanceinfo">
			<li><p><?php echo __("model"); ?> : <span id="model_name_lbl"></span></p></li>
			<li><p><?php echo __("approx_distance"); ?> : <span id="trip_estimate_distance">0</span><span> <?php echo (DEFAULT_UNIT == 1) ? __("miles_label"): __("kms"); ?></span></p></li>
			
			<li><p><?php echo __("approx_fare"); ?> : <span id="trip_estimate_fare"><?php echo CURRENCY."0"; ?></span></p></li>
			<li><p><?php echo __("description"); ?> : <span id="model_description"></span></p></li>
		</ul>
		<ul id="couldnot_find_address" class="couldnot_find" style="display:none;">
			<li>
				<p id="address_not_find"></p>
				<div class="ride_now" id="closebutton"><input type="button" value="<?php echo __('ok'); ?>"></div>
			</li>
		</ul>
		<div id="modelImg" class="taxi_icons"></div>
	</div>
</div>
<!-- *************************************** start sureshkumar.m Modified code for assign form text box values *********** -->
<script type="text/javascript">
	//alert(localStorage.getItem("book_again_form_details"));
	if(localStorage.getItem("book_again_form_details")!=null)
	{
		var form_datas = localStorage.getItem("book_again_form_details");
		var split_dds = form_datas.split('&');
		if(split_dds[4]!=null && split_dds[4]!='')  
		{ 
			var pickup_location_splt=split_dds[4].split('='); 			
			if(pickup_location_splt[0]=='pickup_location')
			{
			removed_spl_pic_lct = decodeURIComponent(pickup_location_splt[1]).replace(/[`~!@#$%^&*()_|+\-=?;:'"<>\{\}\[\]\\\/]/gi, ' ');
			document.bookingForm.pickup_location.value=removed_spl_pic_lct; 
			}			
			var drp_location = split_dds[5].split('=');			
			if(drp_location[0]=='drop_location')
			{
			removed_spl_drp_lct = decodeURIComponent(drp_location[1]).replace(/[`~!@#$%^&*()_|+\-=?;:'"<>\{\}\[\]\\\/]/gi, ' ');		
			//document.getElementById('Current_Location').innerHTML = pickup_location_splt[1];			
			document.bookingForm.drop_location.value=removed_spl_drp_lct; 	
			}					
		}
	}
</script>
<!-- *************************************** end sureshkumar.m Modified code for assign form text box values *********** -->




