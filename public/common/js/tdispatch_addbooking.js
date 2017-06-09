$(document).ready(function(){
	
$("#firstname").autocomplete(URL_BASE+"tdispatch/firstname_load", {
});


$("#email").autocomplete(URL_BASE+"tdispatch/email_load", {
});

$("#phone").autocomplete(URL_BASE+"tdispatch/phone_load", {
});


/** calendar **/
$('#with-altField').multiDatesPicker({
dateFormat: "yy-mm-dd", 
altField: '#altField',
disabled:true,
});
/** calendar **/

$("#pickup_date").datepicker( {
showTimepicker:true,
showSecond: true,
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
minDate : new Date(),
stepSecond: 1
} );

$("#pickup_time").timepicker( {
showTimepicker:true,
showSecond: false,
timeFormat: 'hh:mm:ss',
stepHour: 1,
stepMinute: 1,
minTime : new Date(current_date),
stepSecond: 1
} );


$("#frmdate").datepicker( {
showTimepicker:true,
showSecond: true,
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
minDate : new Date(),
stepSecond: 1,

onSelect: function (date) { change_frmtodate(); }
});

$("#todate").datepicker( {
showTimepicker:true,
showSecond: true,
dateFormat: 'yy-mm-dd',
stepHour: 1,
stepMinute: 1,
minDate : new Date(),
stepSecond: 1,

onSelect: function (date) { change_frmtodate(); }
});

$("#firstname").focus();	

toggle(31);

$("#addcompany_form").validate();

jQuery.validator.addMethod('cmpenddt', function(value) {
var cstartdate=document.getElementById('frmdate').value; 
var cenddate=document.getElementById('todate').value; 
return (cenddate > cstartdate); }, '...');

$('#recurrent_call').click(function(){
$('#recurrent2').attr('checked','checked');
$('#recurrent_info').show();
$('#labelname').addClass('required');
$('#frmdate').addClass('required');
$('#todate').addClass('required');
$('#cmpenddt').addClass('cmpenddt');
});

$('#recurrent_uncall').click(function(){
$('#recurrent1').attr('checked','checked');
$('#recurrent_info').hide();
$('#labelname').removeClass('required');
$('#frmdate').removeClass('required');
$('#todate').removeClass('cmpenddt');
$('#cmpenddt').removeClass('cmpenddt');
});

$('#fixedprice').blur(function(){
var price = $(this).val();	
if(price == '')
{
	//$('#show_suggestion').hide();	
	$('#min_fare').removeClass('strike');
	var tot_price = $('#total_fare').val();	
	$('#total_price').html(tot_price);
	
}
else
{	//$('#show_suggestion').hide();
	$('#total_price').html(price);
	$('#min_fare').addClass('strike');
}
});

$('#firstname').change(function(){
	$('#passenger_id').val('');
	//$('#email').val('');
	$('#email').removeAttr('readonly');
	//$('#phone').val('');
	$('#phone').removeAttr('readonly');
	$('#show_group').hide();
	$('#usergroup_list').html('');
	//$('#load_location').html('');
	//$('#load_journey').html('');
	//$('#show_suggestion').hide();
});

$('#email').change(function(){
	$('#passenger_id').val('');
	//$('#firstname').val('');
	$('#firstname').removeAttr('readonly');
	//$('#phone').val('');
	$('#phone').removeAttr('readonly');
	$('#show_group').hide();
	$('#usergroup_list').html('');
	//$('#load_location').html('');
	//$('#load_journey').html('');
	//$('#show_suggestion').hide();
});

$('#phone').change(function(){
	$('#passenger_id').val('');
	//$('#email').val('');
	$('#email').removeAttr('readonly');
	//$('#firstname').val('');
	$('#firstname').removeAttr('readonly');
	$('#show_group').hide();
	$('#usergroup_list').html('');
	//$('#load_location').html('');
	//$('#load_journey').html('');
	//$('#show_suggestion').hide();
});


});


function change_frmtodate()
{
	var frmdate = $('#frmdate').val();
	var todate = $("#todate").val();

	if(frmdate !='' && todate !='')
	{

		//$('#with-altField').multiDatesPicker('resetDates','');
		/*
		var start_date = new Date(frmdate);
		var end_date = new Date(todate);

		var all_date = $('#altField').val();
		alert(all_date);
		var date_array = String(all_date).split(',');
		
		var array_index1 = $.inArray(frmdate,date_array);
		var array_index2 = $.inArray(todate,date_array)+1;
		
		alert('index1'+array_index1);
		alert('index2'+array_index2);

		if(array_index1 > 0)
		{
			var array1 = date_array.slice(0,array_index1);
		}
		else
		{
			var array1 = '';
		}

		if(array_index2 > 0 )
		{
			var array2 = date_array.slice(array_index2,date_array.length);	
		}
		else
		{
			var array2 = '';	
		}



		var remove_dates1;
		var remove_dates2;

		if(array1 !='')			
		{
			for (var i=0;i<array1.length;i++)
			{ 	
				//remove_dates1 += new Date(array2[i]);
				$('#with-altField').multiDatesPicker('removeDates', [new Date(array1[i])]);
			}
		}

		if(array2 !='')			
		{
			for (var i=0;i<array2.length;i++)
			{ 	
				//remove_dates2 += new Date(array2[i]);
				$('#with-altField').multiDatesPicker('removeDates', [new Date(array2[i])]);
			}
		}
		

		/*
		if(remove_dates !='')
		{
			$('#with-altField').multiDatesPicker('removeDates', [remove_dates,remove_dates]);
		}
		*/


		$('#with-altField').datepicker('setDate',null);
		$('#with-altField').datepicker("option", "minDate", frmdate);
		$('#with-altField').datepicker("option", "maxDate", todate);
		$('#with-altField').datepicker("option", "disabled",false);


	}
	else
	{

		$('#with-altField').datepicker('setDate',null);
		$('#with-altField').datepicker("option", "minDate", '');
		$('#with-altField').datepicker("option", "maxDate", '');
		$('#with-altField').datepicker("option", "disabled",true);

	}

}


function check_passengerexit()
{
	var passenger_id = $('#passenger_id').val();
	var email = $('#email').val();
	var phone = $('#phone').val();
	var payment_valid = true;

	var SrcPath = URL_BASE;
	var url= SrcPath+"tdispatch/checkpassenger_email_phone/";
	var dataS = "email="+email+"&phone="+phone+"&passenger_id="+passenger_id;		
	console.log(dataS);
	$('#dispatch_loading').html('<div class="media-body"><h4 style="text-align:center" class="media-heading">Loading ......... </h4></div>');
	
	$.ajax
	({ 			
		type: "POST",
		url: url, 
		data: dataS, 
		async:false,
		cache: false, 
		dataType: 'html',
		success: function(response) 
		{ 	
			var resdata = JSON.parse(ALERT_MESSAGES);
			var chk_result = response.split('~');
			var chk_email=0;
			var chk_phone=0;
			console.log(chk_result[0]);
			if(chk_result[0]== 1)
			{		
				$('#uemailavilable').html('');
				$('#dispatch_loading').html('');
				$('#uemailavilable').html(language.email_exists);
			}
			else
			{	
				$('#uemailavilable').html('');
				$('#dispatch_loading').html('');
				$('#uphoneavilable').html('');				
			}
			
			if(chk_result[1]== 1)
			{
				$('#uphoneavilable').html('');
				$('#uphoneavilable').html(language.phone_exists);
			}	
			else
			{	
				$('#uemailavilable').html('');
				$('#dispatch_loading').html('');
				$('#uphoneavilable').html('');				
			}
		} 
		 
	});
	
/*

	if($('#group_id'))
	{
		var group_id = $('#group_id').val();
		var total_fare = $('#total_fare').val();
		var payment_type = $('[name="payment_type"]:checked').val(); 
		var recurrent = $('[name="recurrent"]:checked').val(); 

		if(payment_type ==4 && recurrent ==1 && group_id !='' )
		{	
			var url= SrcPath+"tdispatch/checkgrouplimit/";
			var dataS = "group_id="+group_id+"&total_fare="+total_fare;	

			$.ajax
			({ 			
				type: "POST",
				url: url, 
				data: dataS, 
				cache: false, 
				async:false,
				dataType: 'html',
				success: function(response) 
				{ 	
					if(trim(response) == 'N')
					{		
						$("#payment_error").html("<?php echo __('account_no_sufficient'); ?>");
						$('#payment_error').show();
						payment_valid = false;
					}
					else
					{
						$("#payment_error").html("");
						$('#payment_error').hide();
						payment_valid = true;
					}	
				} 
				 
			});
		}

	}	
	else
	{
		if(passenger_id !='')
		{
			var payment_type = $('[name="payment_type"]:checked').val(); 
			if(payment_type ==4)
			{
				$("#payment_error").html("<?php echo __('donot_have_account'); ?>");
				$('#payment_error').show();
				payment_valid = false;
			}
			else
			{
				$("#payment_error").html("");
				$('#payment_error').hide();
				payment_valid = true;
			}
		}

	}	
*/

	var url= SrcPath+"tdispatch/check_dates/";

	var frmdate = $('#frmdate').val();
	var todate = $('#todate').val();
	var recurrent = $("[name='recurrent']:checked").val(); 
	var array_days = [];

	$("[name='daysofweek[]']:checked").map(function(){
	array_days.push($(this).val());
	});
	
	var daysofweek = $('[name="daysofweek[]"]:checked').val(); 

	var dataS = "frmdate="+frmdate+"&todate="+todate+"&days="+array_days;	
	var valid_days = true;
	if(recurrent == 2 && daysofweek !='')
	{
		$.ajax
		({ 			
			type: "POST",
			url: url, 
			data: dataS, 
			cache: false, 
			async:false,
			dataType: 'html',
			success: function(response) 
			{ 	

				if(trim(response) == 1)
				{	
					$("#errors_days").html('');
					valid_days = true;
				}
				else
				{
					$("#errors_days").html('');
					$("#errors_days").html("check_days_between");
					valid_days = false;
				}	
			} 
			 
		});
	}

	var uemailavilable = $('#uemailavilable').html();
	var uphoneavilable = $('#uphoneavilable').html();

	if(uphoneavilable == "" && uemailavilable == "" && payment_valid == true && valid_days == true )
	{
		return true;
	}	
	else
	{
		event.preventDefault();	
		return false;
	}


}


function change_fromtolocation(fromlocation,tolocation)
{
 $('#current_location').val(urldecode(fromlocation));
 $('#drop_location').val(urldecode(tolocation));
GeocodeFromAddress();
}

function urldecode(str) {
   return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}

function popup_location(fid)
{
	$('#add_pick_drop').remove();
	$('#load_location li').removeClass('location_selected');
	var p = $("#fid_"+fid);
	var position = p.position();
	var pheight = (position.top)-20;

	$("<div id='add_pick_drop' style='top:"+pheight+"px; display: block;'><div id='close_location' style='color:black;float:right;padding:5px 5px 5px 5px ! important;'>X</div><div class='button dredB add_pic_but'><input type='button' value='Add To Pick Up' title='Add To Pick Up' id='add_to_pickup' onClick='add_booking_pickup("+fid+");'></div><div class='button greenB add_pic_but'><input type='button' value='Add To Drop off' title='Add To Drop off'  id='add_to_dropoff' onClick='add_booking_dropoff("+fid+");'></div></div>").insertAfter('#fid_'+fid);
	$('#selectclass_'+fid).addClass('location_selected');

$('#close_location').click(function(){
	$('#add_pick_drop').remove();
});


}

function add_booking_pickup(fid)
{

	 var pickuplocation = $('#fid_'+fid).text();
	 $('#current_location').val(urldecode(pickuplocation));
	$('#add_pick_drop').remove();
	GeocodeFromAddress();
	$('#add_pick_drop').remove();
}

function add_booking_dropoff(fid)
{
	 var droplocation = $('#fid_'+fid).text();
	 $('#drop_location').val(urldecode(droplocation));
	$('#add_pick_drop').remove();
	GeocodeFromAddress();
}

$(function(){ 

	$('#add_exclusion').click(function(){  
	var newRow = $("#sub_add tr").length+1; 
	 $("#sub_add").append('<tr id="row_'+newRow+'"><td><input type="text" placeholder="Exclus.start" name="exclus_start[]" id="exclus_start'+newRow+'"  class="mt10" style="width:120px;"  title=""><br><span id="error'+newRow+'" style="display:none;color:red;font-size:11px;"></span><td><input type="text" placeholder="Exclus.end" name="exclus_end[]" id="exclus_end'+newRow+'"  class="mt10 " style="width:120px;margin-left:12px;" title=""></td><td><button type="button" class="remove_icon" onClick="return removetr_contact('+newRow+');"></td></tr>');     
	
	var date = new Date();
	var currentMonth = date.getMonth(); // current month
	var currentDate = date.getDate(); // current date
	var currentYear = date.getFullYear(); //this year

	$("#exclus_start"+newRow).datepicker( {
	showTimepicker:true,
	showSecond: true,
	dateFormat: 'yy-mm-dd',
	stepHour: 1,
	stepMinute: 1,
	minDate: new Date(currentYear, currentMonth, currentDate),
	stepSecond: 1
	});


	$("#exclus_end"+newRow).datepicker( {
	showTimepicker:true,
	showSecond: true,
	dateFormat: 'yy-mm-dd',
	stepHour: 1,
	stepMinute: 1,
	minDate: new Date(currentYear, currentMonth, currentDate),
	stepSecond: 1
	});


	return false;	
	});

});  

  function removetr_contact(rowid) {
   var r1 = "row_"+rowid;
   $("#sub_add tr").each(function () {    
    if(r1==$(this).attr('id')) {
     $(this).remove();
    }   
   });
   return false;
  }
   function getid(rowid) {
   var r2 = "row_"+rowid;
   }     

$('.cal_label').click(function (e) {
var id = $(this).text();

 if ($('#monthDays_'+id).is(':checked'))
 {	
       $('#monthDays_'+id).attr('checked',false);
       $('#calactive_'+id).removeClass('green_active');	
 }
 else
 {	
       $('#monthDays_'+id).attr('checked',true);
       $('#calactive_'+id).addClass('green_active');	
 }			

});

$('.cale_label').click(function (e) {
var id = $(this).text();

 if ($('#daysofweek_'+id).is(':checked'))
 {	
       $('#daysofweek_'+id).attr('checked',false);
       $('#calactive_'+id).removeClass('green_active');	
 }
 else
 {	
       $('#daysofweek_'+id).attr('checked',true);
       $('#calactive_'+id).addClass('green_active');	
 }			

});

/* Map */

	function updateStatus(status) {
		$("#info").html(status);

	}

	function clearMarker() {
		if (marker != null)
			marker.setMap(null);
	}
		
			var marker = null;
			var working = false;


      var marker;
      var latlng = new google.maps.LatLng(LOCATION_LATI,LOCATION_LONG);
      var options = {
        zoom: 6,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      var map = new google.maps.Map(document.getElementById("map_addbooking"), options);

	
      //map.controls[google.maps.ControlPosition.TOP_RIGHT].push(new FullScreenControl(map));

      // set up directions renderer
      var rendererOptions = { draggable: false};
      var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
      directionsDisplay.setMap(map);
			directionsDisplay.setPanel(document.getElementById("directions"));
      google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
				updateStatus("Route changed");
				if (!working) {
					working = true;
					GetElevation(directionsDisplay.directions.routes[0]);
				}
      });
			google.maps.event.addListener(directionsDisplay, 'routeindex_changed', function() {
				updateStatus("Route index changed");
				/*if (!working) {
					working = true;*/
					GetElevation(directionsDisplay.directions.routes[directionsDisplay.getRouteIndex()]);
				/*}*/
			});

			// autocomplete
			var autocomplete = new google.maps.places.Autocomplete(document.getElementById('current_location'), {});
			var toAutocomplete = new google.maps.places.Autocomplete(document.getElementById('drop_location'), {});


			google.maps.event.addListener(autocomplete, 'place_changed', function() {
				var place = autocomplete.getPlace();
				 GeocodeFromAddress();
				 GotoLocation(place.geometry.location,1);
			});

			google.maps.event.addListener(toAutocomplete, 'place_changed', function() {
				var place = toAutocomplete.getPlace();
				GeocodeFromAddress();
				GotoLocation(place.geometry.location,2);
			});


			function GotoLocation(location,field) {
				GetLocationInfo(location,field);
				map.setCenter(location);
			}


		      function GetLocationInfo(latlng,field)
		      {
			if (latlng != null)
			{
			  ShowLatLong(latlng,field);
			}
		      }

		      function ShowLatLong(latLong,field)
		      {
			// show the lat/long
			if (marker != null) {
			  marker.setMap(null);
			}
			marker = new google.maps.Marker({
			  position: latLong,
			  map: map});
			if(field == 1)
			{
				$('#pickup_lat').val(latLong.lat());
				$('#pickup_lng').val(latLong.lng());
				$('#payment_sec').show();	
			}
			else
			{
				$('#drop_lat').val(latLong.lat());
				$('#drop_lng').val(latLong.lng());
				$('#payment_sec').show();	
			}

		      }

			
      function GeocodeFromAddress() {
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
        var from = $("#current_location").val();
	
        geocoder.geocode({ 'address': from },
          function(results, status) {
            if (results[0]) {
              var result = results[0];
              var fromLatLng = result.geometry.location;
		$('#pickup_lat').val(result.geometry.location.lat());
		$('#pickup_lng').val(result.geometry.location.lng());
              GeocodeToAddress(fromLatLng);

            }
            else {
              updateStatus("From address not found");
		working = false;
            }
          }
        );
      }

      function GeocodeToAddress(fromLatLng) {
        // geocode to address

        updateStatus("Locating to address...");
        var geocoder = new google.maps.Geocoder();
        var to = $("#drop_location").val();
        geocoder.geocode({ 'address': to },
          function(results, status) {
            if (results[0]) {
              var result = results[0];
              var toLatLng = result.geometry.location;
		$('#drop_lat').val(result.geometry.location.lat());
		$('#drop_lng').val(result.geometry.location.lng());
              CalculateRoute(fromLatLng, toLatLng);
            }
            else {
              updateStatus("To address not found");
							working = false;
            }
          }
        );
      }

      function CalculateRoute(fromLatLng, toLatLng) {
        // calculate the route
        updateStatus("Calculating route...");
        var directions = new google.maps.DirectionsService();

				var routeType = $('#routeType').val();
				var travelMode = google.maps.DirectionsTravelMode.DRIVING;
				if (routeType == "Walking")
					travelMode = google.maps.DirectionsTravelMode.WALKING;
				else if (routeType == "Public transport")
					travelMode = google.maps.DirectionsTravelMode.TRANSIT;
				else if (routeType == "Cycling")
					travelMode = google.maps.DirectionsTravelMode.BICYCLING;
				
        var request = {
          origin: fromLatLng,
          destination: toLatLng,
          travelMode: travelMode,
	  provideRouteAlternatives: true
        };
        directions.route(request, function(result, status) {
          if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(result);
            GetElevation(result.routes[0]);
          }
          else {
            var statusText = getDirectionStatusText(status);
            updateStatus("An error occurred calculating the route - " + statusText);
						working = false;
          }
        });
      }

			var locations;
      function GetElevation(route) {
				clearMarker();
        			// show distance
				var distance = 0;
				var time = 0;
				for (var i=0; i<route.legs.length; i++) {
					var theLeg = route.legs[i];
					distance += theLeg.distance.value;
					time += theLeg.duration.value;
				}

				hm_hours = ('0'+Math.round(time/3600) % 24).slice(-2)+' hours';
				hm_secs = ('0'+Math.round(time/60)%60).slice(-2)+' mins';
				
				if(hm_hours != '00 hours')
				{
					show_time = hm_hours+hm_secs;
				}
				else
				{
					show_time = hm_secs;					
				}
				
				
				$("#distance").html(showDistance(distance));
				
				var default_company_unit = $('#default_company_unit').val();
				if(default_company_unit=='MILES'){
					var km =(distance/1609.34).toFixed(1);
				}else if(default_company_unit=='KM'){
					var km =((distance/100) / 10).toFixed(1);
				}else{
					var km =(distance/1609.34).toFixed(1);
				}
				
				$('#find_km').html(km+" "+default_company_unit);
				$('#distance_km').val(km);
				$('#desc').html('Rate Kilometer '+km); 
	
				$('#find_duration').html(show_time);	
				$('#total_duration').val(show_time);	
				var model_minfare = $('#model_minfare').val();
				$('#min_value').html(model_minfare);

				var model_id = $('#taxi_model').val();
				var pickup_location = $('#current_location').val();

				var pickup_lat = $('#pickup_lat').val();
				var pickup_lng = $('#pickup_lng').val();

				var pickup_latlng = pickup_lat+','+pickup_lng;

				var city = "";
				var state = "";
				//11.0104033,76.94990280000002
				geocoder = new google.maps.Geocoder();
				var latlng = new google.maps.LatLng(pickup_lat,pickup_lng);
				geocoder.geocode({'latLng': latlng}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						//Check result 0
						var result = results[0];
						//look for locality tag and administrative_area_level_1

						for(var i=0, len=result.address_components.length; i<len; i++) {
							var ac = result.address_components[i];
							if(ac.types.indexOf("locality") >= 0) city = ac.long_name;
							if(ac.types.indexOf("administrative_area_level_1") >= 0) state = ac.long_name;
						}
						//only report if we got Good Stuff
						if(city != '') {
							//alert("Hello to you out there in "+city);
							$('#cityname').val(city);
							var city_id = $('#city_id').val();
							calculate_totalfare(km,model_id,city,city_id);

						}
					} 
				});


				

				/*var total_fare = Math.round(parseFloat(km)*parseFloat(model_minfare));
				var vat_tax = $('#vat_tax').html();
				var tot_tax =  (total_fare*vat_tax/100).toFixed(2);
				var tot_amt = parseFloat(total_fare)+parseFloat(tot_tax);
				$("#min_fare").html(tot_amt);
				$('#sub_total').html(tot_amt);
				$('#total_price').html(tot_amt);
				$('#total_fare').val(tot_amt);
				$('#min_fare').removeClass('strike');

				var price = $('#fixedprice').val();
				if(price == '')
				{
				//$('#show_suggestion').hide();	
				$('#min_fare').removeClass('strike');
				var tot_price = $('#total_fare').val();	
				$('#total_price').html(tot_price);

				}
				else
				{	//$('#show_suggestion').hide();
				$('#total_price').html(price);
				$('#min_fare').addClass('strike');
				}
				*/

				
        // get all the lat/longs
        locations = [];
        for (var i=0; i<route.legs.length; i++) {
          var thisLeg = route.legs[i];
          for (var j=0; j<thisLeg.steps.length; j++) {
            var thisStep = thisLeg.steps[j];
            for (var k=0; k<thisStep.lat_lngs.length; k++) {
              locations.push(thisStep.lat_lngs[k]);
            }
          }
        }
				
				updateStatus("Calculating elevation for " + locations.length + " locations...");
				elevations = [];
				currentPos = 0;
				getElevation();
			}
			
			function showResults(results) {
				// display the results
				var ascent = 0;
				var descent = 0;
				ShowElevation("#start", results[0]);
				ShowElevation("#end", results[results.length-1]);
				var minElevation = results[0];
				var maxElevation = results[0];
				var chartData = [];
				var distance = 0;
				
				for (var i=0; i<results.length; i++) {
					minElevation = Math.min(results[i], minElevation);
					maxElevation = Math.max(results[i], maxElevation);
					
					// calculate distance
					if (i > 0) {
						var d = google.maps.geometry.spherical.computeDistanceBetween (locations[i-1], locations[i]);
						distance += d;
					}
					
					chartData.push([distance/1000, results[i]]);
					
					if (i>0) {
						var thisAscent = results[i] - results[i-1];
						if (thisAscent > 0)
							ascent += thisAscent;
						else
							descent -= thisAscent;
					}
				}
				ShowElevation("#ascent", ascent);
				ShowElevation("#descent", descent);
				ShowElevation("#min", minElevation);
				ShowElevation("#max", maxElevation);
				// chart
				
				
				function showTooltip(x, y, contents) {
					$('<div id="tooltip">' + contents + '</div>').css( {
							position: 'absolute',
							display: 'none',
							top: y,
							left: x + 20,
							border: '1px solid #fdd',
							padding: '2px',
							'background-color': '#fee',
							opacity: 0.80
					}).appendTo("body").fadeIn(200);
				}

				var previousPoint = null;
			}
			
			var currentPos = 0;
			var partLength = 100;
			var elevations = [];
			function getElevation() {
        // calculate the elevation of the route
				var locationsPart = [];
				var end = Math.min(locations.length, currentPos+100);
				for (i=currentPos; i<end; i++) {
					locationsPart.push(locations[i]);
				}
        updateStatus("Calculating elevation for " + currentPos + " to " + end + " (of " + locations.length + ")...");
				
				var positionalRequest = {
          'locations': locationsPart
        };

        var elevator = new google.maps.ElevationService();
        // Initiate the location request
        elevator.getElevationForLocations(positionalRequest,
          function(results, status) {
            if (status == google.maps.ElevationStatus.OK) {
							for (var i=0; i<results.length; i++) {
								elevations.push(results[i].elevation);
							}
							currentPos += partLength;
							if (currentPos > locations.length) {
								showResults(elevations);
								updateStatus("Elevation calculated using " + locations.length + " locations");
								working = false;
							}
							else {
								getElevation();
							}
            }
            else {
							if (status == google.maps.ElevationStatus.OVER_QUERY_LIMIT) {
								updateStatus("Over query limit calculating the elevation for " + currentPos + " to " + (currentPos + partLength) + " (of " + locations.length + "), waiting 1 second before retrying");
								setTimeout("getElevation()", 1000);		
							} 
							else {
								updateStatus("An error occurred calculating the elevation - " + elevationStatusDescription(status));
								working = false;
							}
            }
          }
        );
      }

			function elevationStatusDescription(status) {
				switch (status) {
					case "OVER_QUERY_LIMIT": return "Over query limit";
					case "UNKNOWN_ERROR": return "Unknown error";
					default: return status;
				}
			}
			
      function ShowElevation(selector, elevation) {
        $(selector).html(Math.round(elevation) + " metres (" + Math.round(elevation*3.2808399) + " feet)");
      }
function showDistance(distance) {
	return Math.round(distance/100) / 10 + " km (" +
				Math.round((distance*0.621371192)/100) / 10 + " miles)";

}
/* Map */
	
