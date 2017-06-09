	$(document).ready(function ()
	{		
		/*For Add Booking*/
		$("#firstname").typeahead(
		{
			ajax: URL_BASE + "taxidispatch/firstname_load_new",
		});
		$("#email").typeahead(
		{
			ajax: URL_BASE + "taxidispatch/email_load_new",
		});
		$("#phone").typeahead(
		{
			ajax: URL_BASE + "taxidispatch/phone_load_new",
		});
		
		// Get the current typeahead instance
	/*	var typeaheadInstance = $("#firstname").data("typeahead");
		// Save the reference to the original implementation of the render() function
		var origRenderFunc = typeaheadInstance.render;
		// Overwrite the render() function
		typeaheadInstance.render = function() {
			// Execute the original implementation
			var result = origRenderFunc.apply(this, arguments);
			// Remove the 'active' class from the first item
			result.$menu.children().first().removeClass("active");
			return result;
		} */

		$("#firstname").change(function ()
		{
			var a = $("#firstname").val();
			if(a !=""){
				$("label[for='firstname']").html("");
				$("#passenger_id").val("");
				//$("#email").removeAttr("readonly");
				//$("#phone").removeAttr("readonly");
				$("#show_group").hide();
				$("#usergroup_list").html("");
				change_userdetails_new("firstname", a)
			}else{
				$("#email").val("");
				$("#phone").val("");
				$("#country_code").val("");
				$("#email").removeAttr("readonly");
				$("#phone").removeAttr("readonly");
				$("#country_code").removeAttr("readonly");
			}

		});

		$('#email').change(function(){
			var b = $("#email").val();
			if(b !=""){
				$("label[for='firstname']").html("");
				$("label[for='email']").html("");
				$('#passenger_id').val('');
				//$('#firstname').val('');
				$('#firstname').removeAttr('readonly');
				//$('#phone').val('');
				$('#phone').removeAttr('readonly');
				$("#country_code").removeAttr("readonly");
				$('#show_group').hide();
				$('#usergroup_list').html('');
				change_userdetails_new("email", b)
			}else{
				$("#firstname").val("");
				$("#phone").val("");
				$("#country_code").val("");
				$("#firstname").removeAttr("readonly");
				$("#phone").removeAttr("readonly");
				$("#country_code").removeAttr("readonly");
			}
			
		});

		$('#phone').change(function(){
			var c = $("#phone").val();
			if(c !=""){
				$('#passenger_id').val('');
				//$('#email').val('');
				$('#email').removeAttr('readonly');
				//$('#firstname').val('');
				$('#firstname').removeAttr('readonly');
				$('#show_group').hide();
				$('#usergroup_list').html('');
				change_userdetails_new("phone", c)
			}else{
				$("#email").val("");
				$("#firstname").val("");
				$("#country_code").val("");
				$("#email").removeAttr("readonly");
				$("#firstname").removeAttr("readonly");
				$("#country_code").removeAttr("readonly");
			}
			
		});

		
		/*For Edit Booking*/
		$("#edit_firstname").typeahead(
		{
			ajax: URL_BASE + "taxidispatch/firstname_load_new",
		});
		$("#edit_email").typeahead(
		{
			ajax: URL_BASE + "taxidispatch/email_load_new",
		});
		$("#edit_phone").typeahead(
		{
			ajax: URL_BASE + "taxidispatch/phone_load_new",
		});
		$("#edit_firstname").change(function ()
		{
			var a = $("#edit_firstname").val();
			if(a != "") {
				$("label[for='edit_firstname']").html("");
				$("#edit_passenger_id").val("");
				$("#edit_email").removeAttr("readonly");
				$("#edit_phone").removeAttr("readonly");
				$("#show_group").hide();
				$("#usergroup_list").html("");
				change_userdetails_new_edit("firstname", a)
			} else {
				$("#edit_email").val("");
				$("#edit_phone").val("");
				$("#edit_country_code").val("");
				$("#edit_email").removeAttr("readonly");
				$("#edit_phone").removeAttr("readonly");
				$("#edit_country_code").removeAttr("readonly");
			}
		});
		
		$("#edit_email").change(function ()
		{
			var b = $("#edit_email").val();
			if(b !=""){
				$("label[for='edit_firstname']").html("");
				$("label[for='edit_email']").html("");
				$("#edit_passenger_id").val("");
				$("#edit_firstname").removeAttr("readonly");
				$("#edit_phone").removeAttr("readonly");
				$("#show_group").hide();
				$("#usergroup_list").html("");
				change_userdetails_new_edit("email", b)
			} else {
				$("#edit_firstname").val("");
				$("#edit_phone").val("");
				$("#edit_country_code").val("");
				$("#edit_firstname").removeAttr("readonly");
				$("#edit_phone").removeAttr("readonly");
				$("#edit_country_code").removeAttr("readonly");
			}
		});
		
		$("#edit_phone").change(function ()
		{
			var c = $("#edit_phone").val();
			if(c != "") {
				$("#edit_passenger_id").val("");
				$("#edit_email").removeAttr("readonly");
				$("#edit_firstname").removeAttr("readonly");
				$("#show_group").hide();
				$("#usergroup_list").html("");
				change_userdetails_new_edit("phone", c)
			} else {
				$("#edit_email").val("");
				$("#edit_firstname").val("");
				$("#edit_country_code").val("");
				$("#edit_email").removeAttr("readonly");
				$("#edit_firstname").removeAttr("readonly");
				$("#edit_country_code").removeAttr("readonly");
			}
		});
		
		
	});

	function check_passengerexit()
	{
		var e = $("#passenger_id").val();
		var p = $("#email").val();
		var n = $("#phone").val();
		var k = true;
		var l = URL_BASE;
		var j = l + "tdispatch/checkpassenger_email_phone/";
		var h = "email=" + p + "&phone=" + n + "&passenger_id=" + e;
		console.log(h);
		$("#dispatch_loading").html('<div class="media-body"><h4 style="text-align:center" class="media-heading">Loading ......... </h4></div>');
		$.ajax(
		{
			type: "POST",
			url: j,
			data: h,
			async: false,
			cache: false,
			dataType: "html",
			success: function (u)
			{
				var t = JSON.parse(ALERT_MESSAGES);
				var q = u.split("~");
				var s = 0;
				var r = 0;
				console.log(q[0]);
				if (q[0] == 1)
				{
					$("#uemailavilable").html("");
					$("#dispatch_loading").html("");
					$("#uemailavilable").html(language.email_exists)
				}
				else
				{
					$("#uemailavilable").html("");
					$("#dispatch_loading").html("");
					$("#uphoneavilable").html("")
				}
				if (q[1] == 1)
				{
					$("#uphoneavilable").html("");
					$("#uphoneavilable").html(language.phone_exists)
				}
				else
				{
					$("#uemailavilable").html("");
					$("#dispatch_loading").html("");
					$("#uphoneavilable").html("")
				}
			}
		});
		var j = l + "tdispatch/check_dates/";
		var c = $("#frmdate").val();
		var b = $("#todate").val();
		var m = $("[name='recurrent']:checked").val();
		var f = [];
		$("[name='daysofweek[]']:checked").map(function ()
		{
			f.push($(this).val())
		});
		var g = $('[name="daysofweek[]"]:checked').val();
		var h = "frmdate=" + c + "&todate=" + b + "&days=" + f;
		var d = true;
		if (m == 2 && g != "")
		{
			$.ajax(
			{
				type: "POST",
				url: j,
				data: h,
				cache: false,
				async: false,
				dataType: "html",
				success: function (q)
				{
					if (trim(q) == 1)
					{
						$("#errors_days").html("");
						d = true
					}
					else
					{
						$("#errors_days").html("");
						$("#errors_days").html("check_days_between");
						d = false
					}
				}
			})
		}
		var a = $("#uemailavilable").html();
		var o = $("#uphoneavilable").html();
		if (o == "" && a == "" && k == true && d == true)
		{
			return true
		}
		else
		{
			event.preventDefault();
			return false
		}
	}
	function change_fromtolocation(a, b)
	{
		$("#current_location").val(urldecode(a));
		$("#drop_location").val(urldecode(b));
		GeocodeFromAddress()
	}
	function urldecode(a)
	{
		return decodeURIComponent((a + "").replace(/\+/g, "%20"))
	}
	function add_booking_pickup(a)
	{
		var b = $("#fid_" + a).text();
		$("#current_location").val(urldecode(b));
		$("#add_pick_drop").remove();
		GeocodeFromAddress();
		$("#add_pick_drop").remove()
	}
	function add_booking_dropoff(a)
	{
		var b = $("#fid_" + a).text();
		$("#drop_location").val(urldecode(b));
		$("#add_pick_drop").remove();
		GeocodeFromAddress()
	}
	function updateStatus(a)
	{
		$("#info").html(a)
	}
	function clearMarker()
	{
		if (marker != null)
		{
			marker.setMap(null)
		}
	}
	/*var marker = null;
	var working = false;
	var marker;
	var latlng = new google.maps.LatLng(LOCATION_LATI, LOCATION_LONG);
	var options =
	{
		zoom: 4,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById("map_addbooking"), options);

	
    
	var rendererOptions =
	{
		draggable: false
	};
	var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
	directionsDisplay.setMap(map);
	directionsDisplay.setPanel(document.getElementById("directions"));
	google.maps.event.addListener(directionsDisplay, "directions_changed", function ()
	{
		updateStatus("Route changed");
		if (!working)
		{
			working = true;
			GetElevation(directionsDisplay.directions.routes[0])
		}
	});
	google.maps.event.addListener(directionsDisplay, "routeindex_changed", function ()
	{
		updateStatus("Route index changed");
		GetElevation(directionsDisplay.directions.routes[directionsDisplay.getRouteIndex()])
	});
	/* Restrictions to load only particular country 
	var options = {
		componentRestrictions: {country: MAP_COUNTRY}
	};
	/* Restrictions to load only particular country 
	var autocomplete = new google.maps.places.Autocomplete(document.getElementById('current_location'), options);
	var toAutocomplete = new google.maps.places.Autocomplete(document.getElementById('drop_location'), options);
	//var autocomplete = new google.maps.places.Autocomplete(document.getElementById('current_location'), {});
	//var toAutocomplete = new google.maps.places.Autocomplete(document.getElementById('drop_location'), {});

	google.maps.event.addListener(autocomplete, "place_changed", function ()
	{
		var a = autocomplete.getPlace();
		GeocodeFromAddress();
		GotoLocation(a.geometry.location, 1)
	});
	google.maps.event.addListener(toAutocomplete, "place_changed", function ()
	{
		var a = toAutocomplete.getPlace();
		GeocodeFromAddress();
		GotoLocation(a.geometry.location, 2)
	});


	// autocomplete
	var edit_autocomplete = new google.maps.places.Autocomplete(document.getElementById('edit_current_location'), options);
	var edit_toAutocomplete = new google.maps.places.Autocomplete(document.getElementById('edit_drop_location'), options);

	
	google.maps.event.addListener(edit_autocomplete, 'place_changed', function() {
		var place = autocomplete.getPlace();
		edit_GeocodeFromAddress();
		GotoLocation(place.geometry.location,1);
	});

	google.maps.event.addListener(edit_toAutocomplete, 'place_changed', function() {
		var place = toAutocomplete.getPlace();
		edit_GeocodeFromAddress();
		GotoLocation(place.geometry.location,2);
	}); */
	//to initialize map while page refresing
	google.maps.event.addDomListener(window, 'load', initialize);
	var marker = null;
	var working = false;
	var marker;
	var directionsDisplay;
	var directionsService = new google.maps.DirectionsService();
	var map;
	var latitude_book = LOCATION_LATI;
	var longitude_book = LOCATION_LONG;	
	
	// Get current lat long
	if ("geolocation" in navigator){
		navigator.geolocation.getCurrentPosition(function(position){ 
			console.log(position.coords.latitude+" </br>Lang :"+ position.coords.longitude);
			latitude_book = position.coords.latitude;
			longitude_book = position.coords.longitude;
		});
	}else{
		console.log("Browser doesn't support geolocation!");
	}
	
	function initialize() {
		
	  directionsDisplay = new google.maps.DirectionsRenderer();
	  var latlng = new google.maps.LatLng(latitude_book, longitude_book);
	  var options =
		{
			zoom: 4,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
	  map = new google.maps.Map(document.getElementById('map_addbooking'), options);
	  directionsDisplay.setMap(map);
	  google.maps.event.addListener(directionsDisplay, "directions_changed", function ()
		{
			updateStatus("Route changed");
			if (!working)
			{
				working = true;
				GetElevation(directionsDisplay.directions.routes[0])
			}
		});
		google.maps.event.addListener(directionsDisplay, "routeindex_changed", function ()
		{
			updateStatus("Route index changed");
			GetElevation(directionsDisplay.directions.routes[directionsDisplay.getRouteIndex()])
		});
		/** option variable to search the particular country *
		var options = {
			componentRestrictions: {country: MAP_COUNTRY}
		};
		/** google autocomplete functionality in add booking **/
		var options = {};
		/* Restrictions to load only particular country */
		var autocomplete = new google.maps.places.Autocomplete(document.getElementById('current_location'), options);
		var toAutocomplete = new google.maps.places.Autocomplete(document.getElementById('drop_location'), options);
		//var autocomplete = new google.maps.places.Autocomplete(document.getElementById('current_location'), {});
		//var toAutocomplete = new google.maps.places.Autocomplete(document.getElementById('drop_location'), {});
		var origin_place_id = null;
        var destination_place_id = null;
        var travel_mode = google.maps.TravelMode.DRIVING;

		google.maps.event.addListener(autocomplete, "place_changed", function ()
		{
			/* var a = autocomplete.getPlace();
			GeocodeFromAddress();
			GotoLocation(a.geometry.location, 1) */

			var a = autocomplete.getPlace();
			origin_place_id = a.place_id;
			route(origin_place_id, destination_place_id, travel_mode, directionsService, directionsDisplay);
			GeocodeFromAddress(origin_place_id);
			GotoLocation(a.geometry.location, 1)
		});
		google.maps.event.addListener(toAutocomplete, "place_changed", function ()
		{
			/* var a = toAutocomplete.getPlace();
			GeocodeFromAddress();
			GotoLocation(a.geometry.location, 2) */

			var a = toAutocomplete.getPlace();
			destination_place_id = a.place_id;
			route(origin_place_id, destination_place_id, travel_mode, directionsService, directionsDisplay);
			//GeocodeFromAddress(a);
			GeocodeToAddress(destination_place_id);
			GotoLocation(a.geometry.location, 2)
		});
		/** google autocomplete functionality in edit booking **/
		var edit_autocomplete = new google.maps.places.Autocomplete(document.getElementById('edit_current_location'), options);
		var edit_toAutocomplete = new google.maps.places.Autocomplete(document.getElementById('edit_drop_location'), options);
		//var edit_autocomplete = new google.maps.places.Autocomplete(document.getElementById('edit_current_location'), {});
		//var edit_toAutocomplete = new google.maps.places.Autocomplete(document.getElementById('edit_drop_location'), {});

		google.maps.event.addListener(edit_autocomplete, 'place_changed', function() {
			/* var place = edit_autocomplete.getPlace();
			edit_GeocodeFromAddress();
			edit_GotoLocation(place.geometry.location,1); */

			var place = edit_autocomplete.getPlace();
			origin_place_id = place.place_id;
			route1(origin_place_id, destination_place_id, travel_mode,
			directionsService, directionsDisplay);
			edit_GeocodeFromAddress(origin_place_id);
			edit_GotoLocation(place.geometry.location,1);
		});

		google.maps.event.addListener(edit_toAutocomplete, 'place_changed', function() {
			/* var place = edit_toAutocomplete.getPlace();
			edit_GeocodeFromAddress();
			edit_GotoLocation(place.geometry.location,2); */

			var places = edit_toAutocomplete.getPlace();
			destination_place_id = places.place_id;
			route1(origin_place_id, destination_place_id, travel_mode,
			directionsService, directionsDisplay);
			//console.log("lat->"+places.geometry.location.lat()+"long->"+places.geometry.location.lng());
			edit_GeocodeToAddress(destination_place_id);
			edit_GotoLocation(places.geometry.location,2);
		});
	
	  
	}
	
	function GotoLocation(a, b)
	{
		GetLocationInfo(a, b);
		map.setCenter(a)
	}
	function GetLocationInfo(a, b)
	{
		if (a != null)
		{
			ShowLatLong(a, b)
		}
	}
	function ShowLatLong(a, b)
	{
		if (marker != null)
		{
			marker.setMap(null)
		}
		marker = new google.maps.Marker(
		{
			position: a,
			map: map
		});
		if (b == 1)
		{
			$("#pickup_lat").val(a.lat());
			$("#pickup_lng").val(a.lng());
			$("#payment_sec").show()
		}
		else
		{
			$("#drop_lat").val(a.lat());
			$("#drop_lng").val(a.lng());
			$("#payment_sec").show()
		}
	}
	
	function edit_GotoLocation(a, b)
	{
		edit_GetLocationInfo(a, b);
		map.setCenter(a)
	}
	function edit_GetLocationInfo(a, b)
	{
		if (a != null)
		{
			edit_ShowLatLong(a, b)
		}
	}
	function edit_ShowLatLong(a, b)
	{
		if (marker != null)
		{
			marker.setMap(null)
		}
		marker = new google.maps.Marker(
		{
			position: a,
			map: map
		});
		if (b == 1)
		{
			$("#edit_pickup_lat").val(a.lat());
			$("#edit_pickup_lng").val(a.lng());
			$("#edit_payment_sec").show()
		}
		else
		{
			$("#edit_drop_lat").val(a.lat());
			$("#edit_drop_lng").val(a.lng());
			$("#edit_payment_sec").show()
		}
	}
	
	function GeocodeFromAddress(loc)
	{
		working = true;
		$("#info").html("");
		$("#distance").html("");
		$("#start").html("");
		$("#end").html("");
		$("#min").html("");
		$("#max").html("");
		$("#ascent").html("");
		$("#descent").html("");
		clearMarker();
		updateStatus("Locating from address...");
		var b = new google.maps.Geocoder();
		var a = $("#current_location").val();
		b.geocode(
		{
			//address: loc
			placeId: loc
		}, function (f, e)
		{
			if (f != null && f[0])
			{
				var d = f[0];
				var c = loc.geometry.location;
				$("#pickup_lat").val(loc.geometry.location.lat());
				$("#pickup_lng").val(loc.geometry.location.lng());
				//GeocodeToAddress(c)
			}
			else
			{
				updateStatus("From address not found");
				working = false
			}
		})
	}

	function GeocodeToAddress(loc)
	{
		updateStatus("Locating to address...");
		var c = new google.maps.Geocoder();
		var b = $("#drop_location").val();		
		c.geocode(
		{
			//address: b
			placeId: loc
		}, function (f, e)
		{
			if (f != null && f[0])
			{
				var d = f[0];
				var g = d.geometry.location;
				$("#drop_lat").val(d.geometry.location.lat());
				$("#drop_lng").val(d.geometry.location.lng());
				//CalculateRoute(a, g)
			} else if(f == null){
				//while you select pickup and drop location using enter key
				var plat = $("#pickup_lat").val();
				var plong = $("#pickup_lng").val();
				var dlat = $("#drop_lat").val();
				var dlong = $("#drop_lng").val();
				a = "("+plat+","+plong+")";
				g = "("+dlat+","+dlong+")";
				//CalculateRoute(a, g)
			}
			else
			{
				updateStatus("To address not found");
				working = false
			}
		})
	}

	function edit_GeocodeFromAddress(dloc) 
	{
		working = true;
		// clear all fields
		$("#info").html("");
		$("#distance").html("");
		$("#start").html("");
		$("#end").html("");
		$("#min").html("");
		$("#max").html("");
		$("#ascent").html("");
		$("#descent").html("");
		clearMarker();

        // geocode from address
        updateStatus("Locating from address...");
        var geocoder = new google.maps.Geocoder();
        var from = $("#edit_current_location").val();
        geocoder.geocode({ 
			//'address': from 
			placeId: dloc 
		}, function(results, status) {
			if (results[0]) {
				 var result = results[0];
				//var result = dloc;
				var fromLatLng = result.geometry.location;
				$('#edit_pickup_lat').val(result.geometry.location.lat());
				$('#edit_pickup_lng').val(result.geometry.location.lng());
				//edit_GeocodeToAddress(fromLatLng);
				//edit_GeocodeToAddress(dloc);
			}
			/* if (results[0]) {
				var result = results[0];
				var fromLatLng = result.geometry.location;
				$('#edit_pickup_lat').val(result.geometry.location.lat());
				$('#edit_pickup_lng').val(result.geometry.location.lng());
				edit_GeocodeToAddress(fromLatLng);
            } */
			else {
				updateStatus("From address not found");
				working = false;
			}
		});
	}

    function edit_GeocodeToAddress(dloc)
	{
		updateStatus("Locating to address...");
		var c = new google.maps.Geocoder();
		var b = $("#edit_drop_location").val();
		c.geocode(
		{
			//address: b
			placeId: dloc
		}, function (f, e)
		{
			if (f[0])
			{
				var d = f[0];
				var g = d.geometry.location;
				//console.log("g"+g);
				$("#edit_drop_lat").val(d.geometry.location.lat());
				$("#edit_drop_lng").val(d.geometry.location.lng());
				//edit_CalculateRoute(a, g)
				var taxi_model = $('#edit_taxi_model').val();
				change_minfare(taxi_model,'edit');
			}
			else
			{
				updateStatus("To address not found");
				working = false
			}
		})
	}
      
	
	function CalculateRoute(b, e)
	{
		updateStatus("Calculating route...");
		var d = new google.maps.DirectionsService();
		var f = $("#routeType").val();
		var a = google.maps.DirectionsTravelMode.DRIVING;
		if (f == "Walking")
		{
			a = google.maps.DirectionsTravelMode.WALKING
		}
		else
		{
			if (f == "Public transport")
			{
				a = google.maps.DirectionsTravelMode.TRANSIT
			}
			else
			{
				if (f == "Cycling")
				{
					a = google.maps.DirectionsTravelMode.BICYCLING
				}
			}
		}
		var c =
		{
			origin: b,
			destination: e,
			travelMode: a,
			provideRouteAlternatives: true
		};
		d.route(c, function (h, g)
		{
			if (g == google.maps.DirectionsStatus.OK)
			{
				directionsDisplay.setDirections(h);
				GetElevation(h.routes[0])
			}
			else
			{
				var j = getDirectionStatusText(g);
				updateStatus("An error occurred calculating the route - " + j);
				working = false
			}
		})
	}
	
	function edit_CalculateRoute(b, e)
	{
		updateStatus("Calculating route...");
		var d = new google.maps.DirectionsService();
		var f = $("#routeType").val();
		var a = google.maps.DirectionsTravelMode.DRIVING;
		if (f == "Walking")
		{
			a = google.maps.DirectionsTravelMode.WALKING
		}
		else
		{
			if (f == "Public transport")
			{
				a = google.maps.DirectionsTravelMode.TRANSIT
			}
			else
			{
				if (f == "Cycling")
				{
					a = google.maps.DirectionsTravelMode.BICYCLING
				}
			}
		}
		var c =
		{
			origin: b,
			destination: e,
			travelMode: a,
			provideRouteAlternatives: true
		};
		d.route(c, function (h, g)
		{
			if (g == google.maps.DirectionsStatus.OK)
			{
				directionsDisplay.setDirections(h);
				edit_GetElevation(h.routes[0])
			}
			else
			{
				var j = getDirectionStatusText(g);
				updateStatus("An error occurred calculating the route - " + j);
				working = false
			}
		})
	}
	
	var locations;

	function GetElevation(a)
	{
		clearMarker();
		var c = 0;
		var u = 0;
		/*for (var w = 0; w < a.legs.length; w++)
		{
			var p = a.legs[w];
			c += p.distance.value;
			u += p.duration.value
		}
		hm_hours = ("0" + Math.round(u / 3600) % 24).slice(-2) + " hours";
		hm_secs = ("0" + Math.round(u / 60) % 60).slice(-2) + " mins";
		if (hm_hours != "00 hours")
		{
			show_time = hm_hours + hm_secs
		}
		else
		{
			show_time = hm_secs
		}*/
		show_time = a.legs[0].duration.text;
		show_time_secs = a.legs[0].duration.value;
		c = a.legs[0].distance.value;
		if(c == 0) {
			show_time = 0;
		}
		$("#distance").html(showDistance(c));
		var t = $("#default_company_unit").val();
		if (t == "MILES")
		{
			var e = (c / 1609.34).toFixed(1)
		}
		else
		{
			if (t == "KM")
			{
				var e = ((c / 100) / 10).toFixed(1)
			}
			else
			{
				var e = (c / 1609.34).toFixed(1)
			}
		}
		$("#find_km").html(e + " " + t);
		$("#distance_km").val(e);
		$("#desc").html("Rate Kilometer " + e);
		$("#find_duration").html(show_time);
		$("#total_duration").val(show_time);
		$("#total_duration_secs").val(show_time_secs);
		var r = $("#model_minfare").val();
		$("#min_value").html(r);
		var h = $("#taxi_model").val();
		var b = $("#current_location").val();
		var o = $("#pickup_lat").val();
		var v = $("#pickup_lng").val();
		var f = o + "," + v;
		var s = "";
		var l = "";
		geocoder = new google.maps.Geocoder();
		var n = new google.maps.LatLng(o, v);
		geocoder.geocode(
		{
			latLng: n
		}, function (y, z)
		{
			if (z == google.maps.GeocoderStatus.OK)
			{
				var B = y[0];
				for (var A = 0, x = B.address_components.length; A < x; A++)
				{
					var j = B.address_components[A];
					if (j.types.indexOf("locality") >= 0)
					{
						s = j.long_name
					}
					if (j.types.indexOf("administrative_area_level_1") >= 0)
					{
						l = j.long_name
					}
				}
				if (s != "")
				{
					$("#cityname").val(s);
					var k = $("#city_id").val();
					calculate_totalfare(e, h, s, k,show_time_secs)
				}
			}
		});
		locations = [];
		for (var w = 0; w < a.legs.length; w++)
		{
			var d = a.legs[w];
			for (var m = 0; m < d.steps.length; m++)
			{
				var q = d.steps[m];
				for (var g = 0; g < q.lat_lngs.length; g++)
				{
					locations.push(q.lat_lngs[g])
				}
			}
		}
		updateStatus("Calculating elevation for " + locations.length + " locations...");
		elevations = [];
		currentPos = 0;
		getElevation()
	}

	function edit_GetElevation(a)
	{
		clearMarker();
		var c = 0;
		var u = 0;
		/*for (var w = 0; w < a.legs.length; w++)
		{
			var p = a.legs[w];
			c += p.distance.value;
			u += p.duration.value
		}
		hm_hours = ("0" + Math.round(u / 3600) % 24).slice(-2) + " hours";
		hm_secs = ("0" + Math.round(u / 60) % 60).slice(-2) + " mins";
		if (hm_hours != "00 hours")
		{
			show_time = hm_hours + hm_secs
		}
		else
		{
			show_time = hm_secs
		}*/
		show_time = a.legs[0].duration.text;
		show_time_secs = a.legs[0].duration.value;
		c = a.legs[0].distance.value;
		if(c == 0) {
			show_time = 0;
		}
		$("#distance").html(showDistance(c));
		var t = $("#default_company_unit").val();
		if (t == "MILES")
		{
			var e = (c / 1609.34).toFixed(1)
		}
		else
		{
			if (t == "KM")
			{
				var e = ((c / 100) / 10).toFixed(1)
			}
			else
			{
				var e = (c / 1609.34).toFixed(1)
			}
		}
		$("#edit_find_km").html(e + " " + t);
		$("#edit_distance_km").val(e);
		$("#desc").html("Rate Kilometer " + e);
		$("#edit_find_duration").html(show_time);
		$("#edit_total_duration").val(show_time);
		$("#edit_total_duration_secs").val(show_time_secs);
		var r = $("#edit_model_minfare").val();
		$("#edit_min_value").html(r);
		var h = $("#edit_taxi_model").val();
		var b = $("#edit_current_location").val();
		var o = $("#edit_pickup_lat").val();
		var v = $("#edit_pickup_lng").val();
		var f = o + "," + v;
		var s = "";
		var l = "";
		geocoder = new google.maps.Geocoder();
		var n = new google.maps.LatLng(o, v);
		geocoder.geocode(
		{
			latLng: n
		}, function (y, z)
		{
			if (z == google.maps.GeocoderStatus.OK)
			{
				var B = y[0];
				for (var A = 0, x = B.address_components.length; A < x; A++)
				{
					var j = B.address_components[A];
					if (j.types.indexOf("locality") >= 0)
					{
						s = j.long_name
					}
					if (j.types.indexOf("administrative_area_level_1") >= 0)
					{
						l = j.long_name
					}
				}
				if (s != "")
				{
					$("#edit_cityname").val(s);
					var k = $("#edit_city_id").val();
					calculate_totalfare_flag(e, h, s, k,show_time_secs)
				}
			}/* else if(z == "OVER_QUERY_LIMIT") {
				alert(z);
			} */
		});
		locations = [];
		for (var w = 0; w < a.legs.length; w++)
		{
			var d = a.legs[w];
			for (var m = 0; m < d.steps.length; m++)
			{
				var q = d.steps[m];
				for (var g = 0; g < q.lat_lngs.length; g++)
				{
					locations.push(q.lat_lngs[g])
				}
			}
		}
		updateStatus("Calculating elevation for " + locations.length + " locations...");
		elevations = [];
		currentPos = 0;
		getElevation()
	}
	
	function showResults(h)
	{
		var m = 0;
		var f = 0;
		ShowElevation("#start", h[0]);
		ShowElevation("#end", h[h.length - 1]);
		var g = h[0];
		var l = h[0];
		var b = [];
		var e = 0;
		for (var c = 0; c < h.length; c++)
		{
			g = Math.min(h[c], g);
			l = Math.max(h[c], l);
			if (c > 0)
			{
				var a = google.maps.geometry.spherical.computeDistanceBetween(locations[c - 1], locations[c]);
				e += a
			}
			b.push([e / 1000, h[c]]);
			if (c > 0)
			{
				var k = h[c] - h[c - 1];
				if (k > 0)
				{
					m += k
				}
				else
				{
					f -= k
				}
			}
		}
		ShowElevation("#ascent", m);
		ShowElevation("#descent", f);
		ShowElevation("#min", g);
		ShowElevation("#max", l);

		function n(d, o, p)
		{
			$('<div id="tooltip">' + p + "</div>").css(
			{
				position: "absolute",
				display: "none",
				top: o,
				left: d + 20,
				border: "1px solid #fdd",
				padding: "2px",
				"background-color": "#fee",
				opacity: 0.8
			}).appendTo("body").fadeIn(200)
		}
		var j = null
	}
	var currentPos = 0;
	var partLength = 100;
	var elevations = [];

	function getElevation()
	{
		var c = [];
		var d = Math.min(locations.length, currentPos + 100);
		for (i = currentPos; i < d; i++)
		{
			c.push(locations[i])
		}
		updateStatus("Calculating elevation for " + currentPos + " to " + d + " (of " + locations.length + ")...");
		var a =
		{
			locations: c
		};
		var b = new google.maps.ElevationService();
		b.getElevationForLocations(a, function (f, e)
		{
			if (e == google.maps.ElevationStatus.OK)
			{
				for (var g = 0; g < f.length; g++)
				{
					elevations.push(f[g].elevation)
				}
				currentPos += partLength;
				if (currentPos > locations.length)
				{
					showResults(elevations);
					updateStatus("Elevation calculated using " + locations.length + " locations");
					working = false
				}
				else
				{
					getElevation()
				}
			}
			else
			{
				if (e == google.maps.ElevationStatus.OVER_QUERY_LIMIT)
				{
					updateStatus("Over query limit calculating the elevation for " + currentPos + " to " + (currentPos + partLength) + " (of " + locations.length + "), waiting 1 second before retrying");
					setTimeout("getElevation()", 1000)
				}
				else
				{
					updateStatus("An error occurred calculating the elevation - " + elevationStatusDescription(e));
					working = false
				}
			}
		})
	}
	function elevationStatusDescription(a)
	{
		switch (a)
		{
		case "OVER_QUERY_LIMIT":
			return "Over query limit";
		case "UNKNOWN_ERROR":
			return "Unknown error";
		default:
			return a
		}
	}
	function ShowElevation(a, b)
	{
		$(a).html(Math.round(b) + " metres (" + Math.round(b * 3.2808399) + " feet)")
	}
	function showDistance(a)
	{
		return Math.round(a / 100) / 10 + " km (" + Math.round((a * 0.621371192) / 100) / 10 + " miles)"
	}
	function driver_details_new()
	{
		var b = $("#passenger_log_id").val();
		var a = $("#search_driver").val();
		var company_id = $("#admin_companyid").val();
		var c = "pass_logid=" + b + "&search_driver=" + a + "&admin_companyid=" + company_id;
		$.ajax(
		{
			type: "GET",
			url: URL_BASE + "taxidispatch/search_driver_location",
			data: c,
			cache: false,
			dataType: "html",
			success: function (d)
			{
				$("#driver_details").html(d)
			}
		})
	};

	


function changetableresult_new()
{
	var search_txt = $('#search_txt').val();
	var filter_date = $('#filter_date').val();
	var to_date = $('#to_date').val();
	var sort_type = $('#sort_type').val();
	var sort_by = $('#sort_by').val();
	var booking_filter = $('#booking_filter').val();

	var dataS = "search_txt="+trim(search_txt)+"&filter_date="+filter_date+"&to_date="+to_date+"&sort_type="+trim(sort_type)+"&sort_by="+sort_by+"&booking_filter="+booking_filter+"&page=1";	
	$('#change_result').html('<img alt="ajax-loader" src="'+URL_BASE+'"/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');
	$.ajax
	({ 			
		type: "GET",
		url: URL_BASE+"tdispatch/managebookingsearch", 
		data: dataS, 
		cache: false, 
		dataType: 'html',
		success: function(response) 
		{ 	
			$('#hide_pagi').hide();
			$('#change_result').html('');			
			$('#change_result').html(response);			
		} 
	});	
}

/****** For Dispatcher design **********/
function route(origin_place_id, destination_place_id, travel_mode,directionsService, directionsDisplay) 
{
	if (!origin_place_id || !destination_place_id) {
		return;
	}
	directionsService.route({
		origin: {'placeId': origin_place_id},
		destination: {'placeId': destination_place_id},
		travelMode: travel_mode
	}, function(response, status) {
		if (status === google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(response);
			GetElevation(response.routes[0])
		} else {
			//var j = getDirectionStatusText(g);
			updateStatus("An error occurred calculating the route - " + status);
			working = false
			// window.alert('Directions request failed due to ' + status);
		}
	});
}

function route1(origin_place_id, destination_place_id, travel_mode,directionsService, directionsDisplay) 
{
	if (!origin_place_id || !destination_place_id) {
		return;
	}
	directionsService.route({
		origin: {'placeId': origin_place_id},
		destination: {'placeId': destination_place_id},
		travelMode: travel_mode
	}, function(response, status) {
		if (status === google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(response);
			//GetElevation(response.routes[0])
			edit_GetElevation(response.routes[0])
		} else {
			//var j = getDirectionStatusText(g);
			updateStatus("An error occurred calculating the route - " + status);
			working = false
			// window.alert('Directions request failed due to ' + status);
		}
	});
}


function change_accountuserdetails(name,field_name)
{

	var url= SrcPath+"tdispatch/get_passengerDetails/?field_value="+name+"&field_name="+field_name;
	$.post(url, {
	}, function(response){			
		var myArray = response.split(',');
		$('#passenger_id').val(myArray[0]);
		
		if(field_name == 'firstname')
		{
			$('#account_firstname').removeAttr('readonly');
			$('#account_email').val(myArray[2]);
			$('#account_email').attr('readonly','readonly');
			$('#account_phone').val(myArray[3]);	
			$('#account_phone').attr('readonly','readonly');
		}
		else if(field_name == 'email')
		{
			$('#account_email').removeAttr('readonly');
			$('#account_firstname').val(myArray[1]);
			$('#account_firstname').attr('readonly','readonly');
			$('#account_phone').val(myArray[3]);	
			$('#account_phone').attr('readonly','readonly');
		}
		else if(field_name == 'phone')
		{
			$('#account_phone').removeAttr('readonly');
			$('#account_firstname').val(myArray[1]);
			$('#account_firstname').attr('readonly','readonly');
			$('#account_email').val(myArray[2]);
			$('#account_email').attr('readonly','readonly');

		}

		

	});

}
/***************************************/
