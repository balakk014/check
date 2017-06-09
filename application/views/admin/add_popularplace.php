<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<script src="<?php echo URL_BASE; ?>public/common/js/jquery.dd.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/validation/jquery.validate.js"></script>
<div class="container_content fl clr">
   <div class="cont_container mt15 mt10">
      <div class="content_middle edit_popularplaces">
         <form name="add_popular" id="add_popular" class="form" action="" method="post" enctype="multipart/form-data" onreset="resetHandler();">
            <table border="0" cellpadding="5" cellspacing="0" width="100%">
               <tr>
                  <td valign="top" width="20%"><label><?php echo __('city_label'); ?></label><span class="star">*</span></td>
                  <td>
                     <?php $field_type =''; if(isset($postvalue) && array_key_exists('city_name',$postvalue)){ $field_type =  $postvalue['city_name']; } ?>
                     <div class="formRight">
                        <div class="selector new_select" id="uniform-user_type">
                           <span><?php echo __('select_label'); ?></span>
                           <select name="city_name" id="city_name">
                              <option value=""><?php echo __('select_label'); ?></option>
                              <?php foreach($city_details as $listings) { ?>
                              <option value="<?php echo $listings['city_id'].'|'.$listings['city_name']; ?>" <?php if($field_type == $listings['city_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($listings['city_name']); ?></option>
                              <?php } ?>
                           </select>
                        </div>
                        <em id="cityname_err"></em>
                     </div>
                     <?php if(isset($errors) && array_key_exists('city_name',$errors)){ echo "<span class='error'>".ucfirst($errors['city_name'])."</span>";}?>
                  </td>
               </tr>
               <tr>
                  <td valign="top" width="20%"><label><?php echo __('label_name'); ?></label><span class="star">*</span></td>
                  <td>
                     <div class="new_input_field">
                        <input type="text" placeholder="<?php echo __('enter_labelname'); ?>" title="<?php echo __('enter_labelname'); ?>" name="label_name[]" id="label_name1" value="<?php if(isset($postvalue) && array_key_exists('label_name',$postvalue)){ echo $postvalue['label_name']; }?>" class="labelname"  maxlength="60"/>
                        <?php if(isset($errors) && array_key_exists('location_name',$errors)){ echo "<span class='error'>".ucfirst($errors['location_name'])."</span>";}?>
                     </div>
                  </td>
               </tr>
               <tr>
                  <td valign="top" width="20%"><label><?php echo __('locationicon_label'); ?></label><span class="star">*</span></td>
                  <td>
                     <div class="formRight">
                        <div class="new_input_field">
							<select name="location_icon[]" id="location_icon1" class="location_icon">
							<option value=""><?php echo __('select_label'); ?></option>
							<?php foreach($location_icon as $key=>$value){ ?>
								<option value='<?php echo $key ?>' data-image="<?php echo URL_BASE ?>public/admin/images/<?php echo $key ?>.png" data-title="<?php echo $value; ?>"><?php echo $value; ?></option>
							<?php } ?>
							</select>                               
                        </div>                        
                        <em id="locationicon_err"></em>
                     </div>
                  </td>
               </tr>
               <tr>
                  <td valign="top" width="20%"><label><?php echo __('location_address'); ?></label><span class="star">*</span></td>
                  <td>
                     <div class="new_input_field">
                        <input class="location_name" type="text" placeholder="<?php echo __('enter_locationname'); ?>" title="<?php echo __('enter_locationname'); ?>" name="location_name[]" id="location_name1" value="<?php if(isset($postvalue) && array_key_exists('location_name',$postvalue)){ echo $postvalue['location_name']; }?>"  maxlength="125" autocomplete="off"/>
                        <?php if(isset($errors) && array_key_exists('location_name',$errors)){ echo "<span class='error'>".ucfirst($errors['location_name'])."</span>";}?>
                     </div>
                     <div class="location_editbtm">
                        <p class="latlong_cont" id="latlong1"></p>
                     </div>
                     <input type="hidden" name="latitude[]" id="latitude1" value="">
                     <input type="hidden" name="longitude[]" id="longitude1" value="">
                  </td>
               </tr>
               <tbody id="more_locations">
                  <!-- Loads more locations-->
               </tbody>
               <input type="hidden" name="count" id="count" value="1">
               <tr>
                  <td class="empt_cel">&nbsp;</td>
                  <td colspan="" class="star">*<?php echo __('required_label'); ?></td>
               </tr>
               <tr>
                  <td>&nbsp;</td>
                  <td colspan="">
                     <input type="hidden" name="submit_addpopularplace" value="1">
                     <div class="new_button"><input type="button" value="<?php echo __('add_more'); ?>" title="<?php echo __('add_more'); ?>" onclick="add_fields()" /></div>
                     <div class="new_button"><input type="button" value="<?php echo __('button_back'); ?>" title="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                     <div class="new_button"><input type="submit" name="submit_addpopularplace" value="<?php echo __('btn_submit' );?>" title="<?php echo __('btn_submit' );?>" /></div>
                     <div class="new_button"><input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                     <div class="clr">&nbsp;</div>
                  </td>
               </tr>
            </table>
         </form>
      </div>
      <div class="content_bottom">
         <div class="bot_left"></div>
         <div class="bot_center"></div>
         <div class="bot_rgt"></div>
      </div>
   </div>
</div>
<script src="https://maps.google.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&libraries=places,geometry" type="text/javascript"></script>
<script type="text/javascript">

var autocomplete;
initialize('location_name1');
function initialize(Id) {
    autocomplete = new google.maps.places.Autocomplete((document.getElementById(Id)), {
        types: ['geocode']
    });
    google.maps.event.addDomListener(document.getElementById(Id), 'focus', geolocate);
}

$(document.body).on('keyup', '.location_name' ,function(){	
	var location_name = $(this).attr('id');
	var id = location_name.substr(13);
	var text = document.getElementById(location_name);	
	google.maps.event.addListener(autocomplete, 'place_changed', function () {
		var pickup = autocomplete.getPlace();//Get a place lat&long
		document.getElementById('latitude'+id).value = pickup.geometry.location.lat();
		document.getElementById('longitude'+id).value = pickup.geometry.location.lng();
		var latlong = pickup.geometry.location.lat()+', '+pickup.geometry.location.lng();
		$("#latlong"+id).html(latlong);
	}); 
});

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

function resetHandler(){
	$(".location_icon").each(function() {
		$("#"+this.id).msDropDown().data("dd").set("selectedIndex", 0);
	});
}
	  
$(document).ready(function(){	
	
	// icon image load
	$(".location_icon").msDropdown();

	$(window).keydown(function(event){
		if(event.keyCode == 13) {
			event.preventDefault();
			return false;
		}
	});
     
   	$("#add_popular").validate({
   		// Error placement
   		errorPlacement: function(error, element) {
   			if (element.attr("name") == "city_name" ){
   				error.insertAfter("#cityname_err");
   			}else if (element.attr("name") == "location_icon[]" ){
   				error.insertAfter("#locationicon_err");
   			}else{
   				error.insertAfter(element);
   			}
   		},
   	    rules: {
   			city_name: "required",
   			'label_name[]': {
   				required: true,
   				minlength: 4,
   			},	
   			'location_name[]': {
   				required: true
   			},					
   			'location_icon[]': {
   				required: true
   			}					
   	    },
   	    messages: {
   			city_name: "<?php echo __('please_select_anycity'); ?>",
   			'label_name[]': {
   				required: "<?php echo __('location_name_must_notbeempty'); ?>",
   				minlength:"<?php echo __('location_name_must_consistsofatleast_four_characters'); ?>",
   			},
   			'location_name[]': {
   				required: "<?php echo __('location_address_mustnotbe_empty'); ?>",
   			},			
   			'location_icon[]': {
   				required: "<?php echo __('please_select_location_icon'); ?>",
   			}			
   	    },
   		submitHandler: function(form) {
   			additional_validation(form);
   		}
   	});	
   });
      
   function additional_validation(form){   	
		
		// check for duplicate text
		var $elems = $('.location_name');
		var $elemslabel = $('.labelname');
		var values = [];
		var valueslabel = [];
		var duplicates = 1;
		$elems.each(function () {
			if(!this.value) return true;
			if(values.indexOf(this.value) !== -1) {
				$( "#"+this.id ).after("<label for='location_name' class='errorvalid'><?php echo __('this_location_address_already_added'); ?></label>"); 
				duplicates = 2;
			}
			values.push(this.value);
		});  
		 
		$elemslabel.each(function () {
			if(!this.value) return true;
			if(valueslabel.indexOf(this.value) !== -1) {
				$( "#"+this.id ).after("<label for='label_name' class='errorvalid'><?php echo __('this_location_name_already_added'); ?></label>"); 
				duplicates = 2;
			}
			valueslabel.push(this.value);
		});   
		
		if(duplicates != 1){
			return false;
		}
	
		// form validation
		$.ajax({
			type: "POST",
			url: "<?php echo URL_BASE.'add/check_locationdetails'; ?>",
			data: $(form).serialize(),
			timeout: 3000,
			success: function(result) {
				var response = JSON.parse(result);
				var isEmpty = (response).length === 0;
				if(isEmpty == false){
					// check city name exists 
					if(response.hasOwnProperty('city')){
						$("#cityname_err").html("<label for='city_name' class='errorvalid'>"+response['city']+"</label>");
					}
					
					// check label name exists 
					if(response.hasOwnProperty('label_name')){
						var label_response = response.label_name;
						var splited = label_response.split(',');
						for(var i=0;i<splited.length;i++){
							var elementDet = splited[i].split('|');
							$( "#label_name"+elementDet[0] ).after("<label for='label_name' class='errorvalid'>"+elementDet[1]+"</label>");
						}
					}	
					
					// check location name exists 					
					if(response.hasOwnProperty('location_name')){
						var location_response = response.location_name;
						var splited = location_response.split(',');
						for(var i=0;i<splited.length;i++){
							var elementDet = splited[i].split('|');
							$( "#location_name"+elementDet[0] ).after("<label for='location_name' class='errorvalid'>"+elementDet[1]+"</label>");
						}
					}	
					return false;					
				}else{
					form.submit();
				}				
			},
			error: function() {
				alert('<?php echo __("network_connection_failed"); ?>');
				return false;
			}
		});	
   }
   
	function add_fields()
	{
		var count = $("#count").val();
		var err=0;
		var id = count;
		for(var i=1;i<= count;i++){
			var location_name = $("#location_name"+i).val(); 
			var label_name = $("#label_name"+i).val(); 
			var location_icon = $("#location_icon"+i).val(); 
			if(location_name =='' || label_name == ''|| location_icon == ''){
				err++;
			}
		} 
		if(err != 0){
			alert("<?php echo __('required_location_details_are_missing_please_check'); ?>");
			return false;
		}		
		count++; 
		$("#count").val(count);
		var field = '<tr id="rowa'+count+'"><td valign="top" width="20%"><label><?php echo __("label_name"); ?></label><span class="star">*</span></td><td><div class="new_input_field"><input type="text" placeholder="<?php echo __('enter_labelname'); ?>" title="<?php echo __('enter_labelname'); ?>" class="labelname" name="label_name[]" id="label_name'+count+'" value=""  maxlength="60"/></div></td></tr><tr id="rowc'+count+'"><td valign="top" width="20%"><label><?php echo __("locationicon_label"); ?></label><span class="star">*</span></td><td><div class="formRight"><div class="new_input_field"><select name="location_icon[]" id="location_icon'+count+'" class="location_icon"><option value=""><?php echo __("select_label"); ?></option><?php foreach($location_icon as $key=>$value){ ?><option value="<?php echo $key ?>" data-image="<?php echo URL_BASE ?>public/admin/images/<?php echo $key ?>.png" data-title="<?php echo $value; ?>"><?php echo $value; ?></option><?php } ?></select></div><em id="locationicon_err"></em></div></td></tr><tr id="rowb'+count+'"><td valign="top" width="20%"><label><?php echo __('location_address'); ?></label><span class="star">*</span></td><td><div class="new_input_field"><input class="location_name" type="text" placeholder="<?php echo __('enter_locationname'); ?>" title="<?php echo __('enter_locationname'); ?>" name="location_name[]" id="location_name'+count+'" value=""  maxlength="125" autocomplete="off"/></div><div class="location_remove"><input type="button" onclick="remove_field('+count+')" value="Remove"></div><div class="location_editbtm"><p class="latlong_cont" id="latlong'+count+'"></p></div><input type="hidden" name="latitude[]" id="latitude'+count+'" value=""><input type="hidden" name="longitude[]" id="longitude'+count+'" value=""></td></tr>';   
		$("#more_locations").append(field);
		$(".location_icon").msDropdown();  
		initialize('location_name'+count);
		// add places autocomplete process to all inputs
		//~ var input = document.getElementsByClassName('location_name');
		//~ var options = {
			//~ componentRestrictions: {country: "ind"}
		//~ };
		//~ for (i = 0; i < input.length; i++) {
			//~ initialize('location_name'+count);
			//~ autocomplete = new google.maps.places.Autocomplete(input[i], options);
		//~ }	
	}
   function remove_field(id){
   	$('#rowa'+id).remove();
   	$('#rowb'+id).remove();
   	$('#rowc'+id).remove();
   }
</script>
