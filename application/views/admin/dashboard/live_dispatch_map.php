<div id="on_going_trip_map" >
    <div class="live_trip_right_box">
	<ul>
	    <li><span class="map_available"><?php echo __('available'); ?></span></li>
	    <li><span class="map_ontrip"><?php echo __('on_trip'); ?></span></li>
	    <li><span class="map_inactive"><?php echo __('inactive'); ?></span></li>
	     <li><span class="map_shiftout"><?php echo __('shift_out'); ?></span></li>
	</ul>
    </div>
	<div id="map-canvas" style="width:100%;height:400px;background-color:white;"></div>
</div>

<script>
    jQuery(function($) {
		// Asynchronously Load the map API
		if (typeof google === 'object' && typeof google.maps === 'object') {
			initialize();
		}else{
			var script = document.createElement('script');
			script.src = "http://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initialize";
			document.body.appendChild(script);
		}
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
			$a = 0; $b = 6;
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
		<?php } if ($b == 4) { if ($v['driver_status'] == 'F' && $v['shift_status'] == 'OUT') { ?>
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

					// Loop through our array of markers & place each one on the map  
					var position = new google.maps.LatLng(markers[i][0], markers[i][1]);
					bounds.extend(position);
					marker = new google.maps.Marker({
						position: position,
						map: map,
						animation: google.maps.Animation.DROP,
						icon: markers[i][4]
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


					// Allow each marker to have an info window
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
					// Automatically center the map fitting all markers on the screen
					map.fitBounds(bounds);
				}
			}
			else
			{
				$('#on_going_trip_map').html('<div class="nodata_found"><?php echo __('nodrivers_available'); ?></div>');
			}
	}
</script>
