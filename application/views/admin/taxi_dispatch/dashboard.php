<script>
// Change the selector if needed
/*var $table = $('table.scroll'),
    $bodyCells = $table.find('tbody tr:first').children(),
    colWidth;

 
    
// Adjust the width of thead cells when window resizes
$(window).resize(function() {
    // Get the tbody columns width array
    colWidth = $bodyCells.map(function() {
        return $(this).width();
    }).get();
    
    // Set the width of thead columns
    $table.find('thead tr').children().each(function(i, v) {
        $(v).width(colWidth[i]);
    });    
}).resize(); // Trigger resize handler*/

</script>

<script>
$(document).ready(function() {
	$('.jumbotron').click(function() {
		// $('body').toggleClass('map_fluied');		
	});
	
	$("#reset_date").click(function(){
		$("#search_txt, #search_location, #select_taxi_model").val("");
		$("#filter_date").val("<?php echo date('Y-m-d 00:00');?>");
		$("#to_date").val("<?php echo date('Y-m-d 23:59');?>");
		all_booking_manage_list_search();
	});
	
	$("#clearFromDate").on('click',function(){
		$("#filter_date").val("<?php echo date('Y-m-d 00:00');?>");
	});
	
	$("#clearToDate").on('click',function(){
		$("#to_date").val("<?php echo date('Y-m-d 23:59');?>");
	});

	var today = new Date();
	var timeFormat = "hh:ii";
	//var dateFormat = DEFAULT_DATE_FORMAT_SCRIPT;
	var dateFormat = "yyyy-mm-dd";
	var dateFormat = dateFormat+' '+timeFormat;
	$("#filter_date").datetimepicker({
		autoclose:true,
		endDate: today,
		showTimepicker:true,
		showSecond: true,
		format : dateFormat,
		stepHour: 1,
		stepMinute: 1,
		stepSecond: 1
	});

	$("#to_date").datetimepicker( {
		autoclose:true,
		//endDate: today,
		showTimepicker:true,
		showSecond: true,
		format : dateFormat,
		stepHour: 1,
		stepMinute: 1,
		stepSecond: 1
	});
});

google.maps.event.addDomListener(window, 'load', function () {
	var places = new google.maps.places.Autocomplete(document.getElementById('search_location'));
	google.maps.event.addListener(places, 'place_changed', function () {
		var place = places.getPlace();
		var address = place.formatted_address;
		var latitude = place.geometry.location.lat();
		var longitude = place.geometry.location.lng();
	});
});
</script>


<?php 

//$company_currency = findcompany_currency($_SESSION['company_id']); 
$company_currency = CURRENCY_SYMB; 
?>

<div class="container taxi_dispatcher">
    <div class="row">
        <!-- form: -->
        <div class="lft_outer" id="container_tot">
            <div class="manage_booking_bottom_outer">
                
                <div class="manage_booking_bottom  push_left" id="left_slide_panel">
                    <div class="taxi_scroll_one_top">
                        
                        <div class="col-md-4 col-md-4-inner">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                    <li id="ab_tab"><a href="#add_booking" style="cursor:pointer;" id="add_booking_tab" role="tab" title="<?php echo __('add_booking'); ?>" data-toggle="tab"><?php echo __('add_booking'); ?></a></li>
                                    <li id="eb_tab">
                                        <!--a href="#edit_booking" role="tab" id="edit_booking_tab" data-toggle="tab">Edit Booking</a-->
                                    </li>
                                    <?php /* if($_SESSION['user_type']=="A"){ ?> 
                                    <li>
                                        <div class="all_company">
                                        <select   name="select_company" id="select_company" onchange="all_booking_manage_list();driver_list_with_status();">
                                                    <option value="0">All Company</option>
                                                    <?php foreach($get_active_company_details as $company){ ?>
                                                                    <option value="<?php echo $company['cid']; ?>"><?php echo ucfirst($company['company_name']); ?></option>
                                                    <?php } ?>
                                            </select>
                                        </div>
                                    </li>
                                    <?php }else{ ?>
                                            <input name="select_company" id="select_company" type="hidden" value="<?php echo $_SESSION['company_id']; ?>" >                        
                                    <?php } ?>
                                    <li>	
                                            <div class="all_company">
                                            <select   name="select_taxi_model" id="select_taxi_model" onchange="driver_list_with_status();">
                                                    <option value="">All Vehicle</option>
                                                    <?php foreach($model_details as $model){ ?>
                                                                    <option value="<?php echo $model['model_id']; ?>"><?php echo ucfirst($model['model_name']); ?></option>
                                                    <?php } ?>
                                            </select>
                                            </div>
                                    </li> */?>

                            </ul>
                           
                            <div id="add_book_tab" class="" style="display:none">
                                <div class="add_book_tabinner"> 
								<a href="javascript:;" title="close" id="close_button" class="popup_close_button close_side_bar">&nbsp;</a>
                                    <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo URL_BASE; ?>taxidispatch/dashboard" enctype="multipart/form-data" --onSubmit="check_passengerexit()">
                            <div class="row">
                                <h4><?php echo strtoupper(__('passengers_information')); ?></h4>
                                <div class="form-group">                                                             
                                    <input type="text" class="form-control" name="firstname" id="firstname" placeholder="<?php echo __('name_label'); ?>"  autocomplete="off" maxlength="55" />                              
                                </div>
                                <input name="passenger_id" id="passenger_id" type="hidden" >                        
                                <div class="form-group">                                                            
                                    <input type="text" class="form-control" name="email" id="email" placeholder="<?php echo __('email_id'); ?>" autocomplete="off" maxlength="85"/>                                 
                                </div>
                                <div class="form-group">
									<input type="text" class="form-control" name="country_code" id="country_code"  maxlength="8" placeholder="<?php echo TELEPHONECODE; ?>" autocomplete="off" />			                               
                                    <input type="text" class="form-control" name="phone" id="phone"  maxlength="15" placeholder="<?php echo __('mobile'); ?>" autocomplete="off" />                               
                                </div>                                            
                                 <?php /** booking details **/ ?>
                                <h4><?php echo strtoupper(__('booking_details')); ?></h4>
                                <div class="form-group col-lg-5_taxi_dispatcher">
                                    <div class="col-lg-12">
                                        <input type="text" class="form-control" id="current_location" name="current_location" autocomplete="off"  placeholder="<?php echo __('enter_currentlocation'); ?>" maxlength="150"/>
                                    </div>
                                    <div class="col-lg-12" style="margin-right: 0;">
                                        <input type="text" class="form-control" id="drop_location" name="drop_location" autocomplete="off" placeholder="<?php echo __('enter_droplocation'); ?>" maxlength="150" />
                                    </div>
                                    <input type="hidden" value="" class="form-control" id="notes" maxlength="128" name="notes" autocomplete="off"/>
                                    <div class="col-lg-12">
                                        <input class="form-control"  data-format="yyyy-mm-dd hh:mm" type="text" readonly name="pickup_date" id="pickup_date" autocomplete="off" placeholder="<?php echo __('pickup_time'); ?>"></input>
                                        <label class="error" id="timeError"></label>
                                    </div>
                                            <input type="hidden" id="dispatch_id" name="dispatch" value="" />
                                            <input type="hidden" id="create_id" name="create" value="" />
											<div class="col-lg-12">
													<?php $field_type =''; if(isset($postvalue) && array_key_exists('taxi_model',$postvalue)){ $field_type =  $postvalue['taxi_model']; } ?>
													<select style="padding:0;" name="taxi_model" id="taxi_model" class="form-control" title="<?php echo __('select_the_taximodel'); ?>" OnChange="change_minfare(this.value,'');">
															<option value=""><?php echo __('select_vehicle_label'); ?></option>
															<?php 
																	foreach($model_details as $list) { ?>
															<option value="<?php echo $list['model_id']; ?>" <?php if($field_type == $list['model_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($list['model_name']); ?></option>
															<?php } ?>
													</select>
											</div>                                               
                                </div>

                            </div>
                            <div class="row">    

                            <?php /**  Payment **/ ?>
                            <div class="row row_payment" style="padding-left: 0;">                                            
                                <div class="col-lg-5">
                                    <h4><?php echo strtoupper(__('payment')); ?></h4>
                                    <ul class="payment_inner">
                                        <li>
                                            <label><?php echo __('journey'); ?>:&nbsp;</label><span id="find_duration"><?php echo __('zero_mins'); ?></span>
                                        </li>
                                        <li>
                                            <label><?php echo __('distance'); ?>:&nbsp;</label><span id="find_km"><?php //echo __('zero_distance'); ?></span>
                                        </li>
                                        <li>
                                            <label><?php echo __('tax'); ?>:&nbsp;</label><span id="vat_tax"><?php echo $company_tax; ?></span><span>%</span>
                                        </li>
                                        <li>
                                            <label><?php echo __('approx_fare'); ?>:&nbsp;</label><span id="min_fare" class=""><?php echo '0'; ?></span><span><?php echo $company_currency; ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="map_addbooking_outer">
                                <div id="map_addbooking"></div>
                            </div>
                            </div>
                            <?php /** booking details **/ ?>
                            <?php /**  VEHICLE details **/ ?>                                        
                            <!-- Booking type-->

                            <div class="form-group">
                                <!--div id="directions"></div-->
                                <?php /** hidden fields **/ ?>
                                <div style="display:none;">
                                    <table>
                                        <tr>
                                            <td><?php echo __('start_altitude'); ?>:</td>
                                            <td id="start"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo __('end_altitude'); ?>:</td>
                                            <td id="end"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo __('maximum_latitude'); ?>:</td>
                                            <td id="max"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo __('minimum_latitude'); ?>:</td>
                                            <td id="min"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo __('distance'); ?>:</td>
                                            <td id="distance"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo __('total_ascent'); ?>:</td>
                                            <td id="ascent"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo __('total_descent'); ?>:</td>
                                            <td id="descent"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>                                       
                            <ul id="acc" style="display:none;">
                                <li><label><?php echo __('description'); ?> :</label><span id="desc"><?php echo __('rate_kilometer'); ?></span></li>
                                <li><label><?php echo __('value'); ?> :</label><span><?php echo $company_currency; ?></span><span id="min_value"></span></li>
                                <li><label><?php echo __('subtotal'); ?> :</label><span><?php echo $company_currency; ?></span><span id="sub_total"></span></li>
                            </ul>
                            <input type="hidden" name="payment_type" value=""/>
                            <input type="hidden" name="fixedprice" value=""/>
                            <input type="hidden" name="pickup_time" value="23"/>
                            <input type="hidden" name="pickup_lat" id="pickup_lat" value="">
                            <input type="hidden" name="pickup_lng" id="pickup_lng" value="">
                            <input type="hidden" name="drop_lat" id="drop_lat" value="">
                            <input type="hidden" name="drop_lng" id="drop_lng" value="">
                            <input type="hidden" name="info" id="info" value="">
                            <input type="hidden" name="model_minfare" id="model_minfare" value="0" >
                            <input type="hidden" name="distance_km" id="distance_km" value="0" >
                            <input type="hidden" name="total_fare" id="total_fare" value="0" >
                            <input type="hidden" name="total_duration" id="total_duration" value="0" >
                            <input type="hidden" name="total_duration_secs" id="total_duration_secs" value="0" >
                            <input type="hidden" name="city_id" id="city_id" value="" >
                            <input type="hidden" name="cityname" id="cityname" value="" >
                            <input type="hidden" name="payment_sec" id="payment_sec" value="" >
                            <input type="hidden" name="company_tax" id="company_tax" value="<?php echo $company_tax; ?>" >
                            <input type="hidden" name="default_company_unit" id="default_company_unit" value="<?php echo UNIT_NAME; ?>" >
                            <input type="hidden" name="recurrent" value="1"/>
                            <input type="hidden" name="luggage" value=""/>
                            <input type="hidden" name="no_passengers" value=""/>
                            <input type="hidden" name="driver_id" id="driver_id" value=""/>
                            <input type="hidden" name="admin_company_id" id="admin_company_id" value=""/>

                            <?php /*  <div class="form-group">
                                <div class="col-lg-9 col-lg-offset-3">
                                <div id="errors"></div>
                                </div>
                                </div> */ ?>
                            <div class="form-group">
                                <?php /*
                                    <div class="col-lg-9">
                                            <button type="submit" class="btn btn-primary" name="signup" value="Add Booking">Add Booking</button>
                                    </div>
                                    */ ?>
                                <div class="col-lg-9">
                                    <button type="submit" class="btn btn-primary" name="create" id="create" value="<?php echo __('create'); ?>" ><?php echo __('create'); ?></button>
                                </div>
                                <div class="col-lg-9">
                                    <button type="submit" class="btn btn-primary" name="dispatch" id="dispatch" value="<?php echo __('dispatch'); ?>" ><?php echo __('dispatch'); ?></button>
                                </div>
                                <div class="col-lg-9">
                                    <button type="button" class="btn btn-primary" name="reset" id="reset" value="<?php echo __('button_reset'); ?>" ><?php echo __('button_reset'); ?></button>
                                </div>
                            </div>
                        </form>
                                </div>
                            </div>
                            
                            <div id="edit_book_tab" class="" style="display:none">
                                <div class="edit_book_tabinner">
                                <a href="javascript:;" title="close" class="popup_close_button close_side_bar">&nbsp;</a>
                                                    <form id="defaultForm_edit" method="post" class="form-horizontal" action="<?php echo URL_BASE; ?>taxidispatch/dashboard" enctype="multipart/form-data" --onSubmit="change_email_phone_exit()">
                                                            <div class="row">
                                                                    <h4><?php echo strtoupper(__('passengers_information')); ?></h4>
                                                                    <div class="form-group">                                                             
                                                                            <input type="text" class="form-control" name="edit_firstname" id="edit_firstname" placeholder="<?php echo __('name_label'); ?>"  autocomplete="off" maxlength="55" />                              
                                                                    </div>
                                                                    <input name="edit_passenger_id" id="edit_passenger_id" type="hidden" >                        
                                                                    <div class="form-group">                                                            
                                                                            <input type="text" class="form-control" name="edit_email" id="edit_email" placeholder="<?php echo __('email_id'); ?>" autocomplete="off" maxlength="85" />                                 
                                                                    </div>
                                                                    <div class="form-group">
                                                                            <input type="text" class="form-control" name="edit_country_code" id="edit_country_code"  maxlength="8" placeholder="<?php echo TELEPHONECODE; ?>" autocomplete="off" />				                               
                                                                            <input type="text" class="form-control" name="edit_phone" id="edit_phone" maxlength="15" placeholder="<?php echo __('mobile'); ?>" />                               
                                                                    </div>
                            <?php /** booking details **/ ?>
                                                                    <h4><?php echo strtoupper(__('booking_details')); ?></h4>
                                                                    <div class="form-group col-lg-5_taxi_dispatcher">
                                                                            <div class="col-lg-12">
                                                                                    <input type="text" class="form-control" id="edit_current_location" name="edit_current_location" autocomplete="off"  placeholder="<?php echo __('enter_currentlocation'); ?>" maxlength="150"/>
                                                                            </div>
                                                                            <div class="col-lg-12" style="margin-right: 0;">
                                                                                    <input type="text" class="form-control" id="edit_drop_location" name="edit_drop_location" autocomplete="off" placeholder="<?php echo __('enter_droplocation'); ?>" maxlength="150"/>
                                                                            </div>
                                                                            <input type="hidden" class="form-control" name="edit_notes" id="edit_notes" maxlength="128" autocomplete="off"  placeholder="<?php echo __('note_driver'); ?>"  />
                                                                            <div class="col-lg-12">
                                                                                    <input type="hidden" name="edit_pickup_date_db" id="edit_pickup_date_db" value=""/>
                                                                                    <input class="form-control"  data-format="yyyy-mm-dd hh:mm" type="text" readonly name="edit_pickup_date" id="edit_pickup_date" autocomplete="off" placeholder="<?php echo __('pickup_time'); ?>"></input>
                                                                                    <label class="error" id="timeEditError"></label>
                                                                            </div>
                                                                            <input type="hidden" id="update_dispatch_id" name="update_dispatch" value="" />

                                                                            <div class="col-lg-12">
                                                                                    <?php $field_type =''; if(isset($postvalue) && array_key_exists('taxi_model',$postvalue)){ $field_type =  $postvalue['taxi_model']; } ?>
                                                                                    <select style="padding:0;" name="edit_taxi_model" id="edit_taxi_model" class="form-control" title="<?php echo __('select_the_taximodel'); ?>" OnChange="change_minfare(this.value,'edit');">
                                                                                            <option value=""><?php echo __('select_vehicle_label'); ?></option>
                                                                                            <?php 
                                                                                                    foreach($model_details as $list) { ?>
                                                                                            <option value="<?php echo $list['model_id']; ?>" <?php if($field_type == $list['model_id']) { echo 'selected=selected'; } ?>><?php echo ucfirst($list['model_name']); ?></option>
                                                                                            <?php } ?>
                                                                                    </select>
                                                                            </div>
                                                                    </div>                                                                                
                                                            </div>
                                                            <div class="row">
                                                                <div class="row row_payment" style="padding-left: 0;">
                                                                    <div class="col-lg-5">
                                                                            <h4><?php echo strtoupper(__('payment')); ?></h4>
                                                                            <ul class="payment_inner">
                                                                                    <li>
                                                                                            <label><?php echo __('journey'); ?>:&nbsp;</label><span id="edit_find_duration"><?php echo __('zero_mins'); ?></span>
                                                                                    </li>
                                                                                    <li>
                                                                                            <label><?php echo __('distance'); ?>:&nbsp;</label><span id="edit_find_km"></span>
                                                                                    </li>
                                                                                    <li>
                                                                                            <label><?php echo __('tax'); ?>:&nbsp;</label><span id="edit_vat_tax"><?php echo $company_tax; ?></span><span><?php echo __('percentage_symbole'); ?></span>
                                                                                    </li>
                                                                                    <li>
                                                                                            <label><?php echo __('approx_fare'); ?>:&nbsp;</label><span id="edit_min_fare" class=""><?php echo '0'; ?></span><span><?php echo $company_currency; ?></span>
                                                                                    </li>
                                                                            </ul>
                                                                    </div>
                                                                    <div class="map_addbooking_outer">
                                <div id="map_editbooking"></div>
                            </div>
                                                            </div>                                                                                
                                                            </div>
                                                            <?php /** booking details **/ ?>
                                                            <?php /**  VEHICLE details **/ ?>									
                                                            <!--Booking type-->

                                                            <?php /**  Payment **/ ?>									
                                                            <div class="form-group">
                                                                    <!--div id="directions"></div-->
                                                                    <?php /** hidden fields **/ ?>
                                                                    <div style="display:none;">
                                                                            <table>                                                                                    
                                                                                    <tr>
																						<td><?php echo __('start_altitude'); ?>:</td>
																						<td id="start"></td>
																					</tr>
																					<tr>
																						<td><?php echo __('end_altitude'); ?>:</td>
																						<td id="end"></td>
																					</tr>
																					<tr>
																						<td><?php echo __('maximum_latitude'); ?>:</td>
																						<td id="max"></td>
																					</tr>
																					<tr>
																						<td><?php echo __('minimum_latitude'); ?>:</td>
																						<td id="min"></td>
																					</tr>
																					<tr>
																						<td><?php echo __('distance'); ?>:</td>
																						<td id="distance"></td>
																					</tr>
																					<tr>
																						<td><?php echo __('total_ascent'); ?>:</td>
																						<td id="ascent"></td>
																					</tr>
																					<tr>
																						<td><?php echo __('total_descent'); ?>:</td>
																						<td id="descent"></td>
																					</tr>
                                                                            </table>
                                                                    </div>
                                                            </div>
                                                            <ul id="acc" style="display:none;">
                                                                    <li><label><?php echo __('description'); ?> :</label><span id="desc"><?php echo __('rate_kilometer'); ?></span></li>
                                                                    <li><label><?php echo __('value'); ?> :</label><span><?php echo $company_currency; ?></span><span id="edit_min_value"></span></li>
                                                                    <li><label><?php echo __('subtotal'); ?> :</label><span><?php echo $company_currency; ?></span><span id="edit_sub_total"></span></li>
                                                            </ul>
                                                            <input type="hidden" name="edit_payment_type" value=""/>
                                                            <input type="hidden" name="edit_fixedprice" value=""/>
                                                            <input type="hidden" name="edit_pickup_time" value=""/>
                                                            <input type="hidden" name="edit_pickup_lat" id="edit_pickup_lat" value="">
                                                            <input type="hidden" name="edit_pickup_lng" id="edit_pickup_lng" value="">
                                                            <input type="hidden" name="edit_drop_lat" id="edit_drop_lat" value="">
                                                            <input type="hidden" name="edit_drop_lng" id="edit_drop_lng" value="">
                                                            <input type="hidden" name="edit_info" id="info" value="">
                                                            <input type="hidden" name="edit_model_minfare" id="edit_model_minfare" value="" >
                                                            <input type="hidden" name="edit_distance_km" id="edit_distance_km" value="" >
                                                            <input type="hidden" name="edit_total_fare" id="edit_total_fare" value="" >
                                                            <input type="hidden" name="edit_total_duration" id="edit_total_duration" value="" >
                                                            <input type="hidden" name="edit_total_duration_secs" id="edit_total_duration_secs" value="" >
                                                            <input type="hidden" name="edit_city_id" id="edit_city_id" value="" >
                                                            <input type="hidden" name="edit_cityname" id="edit_cityname" value="" >
                                                            <input type="hidden" name="edit_payment_sec" id="edit_payment_sec" value="" >
                                                            <input type="hidden" name="edit_company_tax" id="edit_company_tax" value="<?php echo $company_tax; ?>" >
                                                            <input type="hidden" name="edit_default_company_unit" id="edit_default_company_unit" value="<?php echo UNIT_NAME; ?>" >
                                                            <input type="hidden" name="edit_recurrent" value="1"/>
                                                            <input type="hidden" name="edit_luggage" value=""/>
                        <input type="hidden" name="edit_no_passengers" value=""/>
                        <input type="hidden" name="edit_pass_logid" id="edit_pass_logid" value=""/>
                        <input type="hidden" name="edit_admin_company_id" id="edit_admin_company_id" value=""/>

                                                            <div class="form-group">
                                                                    <div class="col-lg-9">
                                                                            <button type="submit" class="btn btn-primary" id="update_submit" name="update" value="<?php echo __('button_update'); ?>" ><?php echo __('button_update'); ?></button>
                                                                    </div>
                                                                    <div class="col-lg-9">
                                                                            <button type="submit" class="btn btn-primary" name="update_dispatch" id="update_dispatch" value="<?php echo __('dispatch'); ?>" ><?php echo __('dispatch'); ?></button>
                                                                    </div>
                                                                    <div class="col-lg-9">
                                                                            <button type="submit" class="btn btn-primary" name="cancel_button" id="cancel_button" value="<?php echo __('cancel'); ?>" ><?php echo __('cancel'); ?></button>
                                                                    </div>
                                                                    <div class="col-lg-9">
                                                                            <button type="button" class="btn btn-primary edit_reset_btn" name="reset" value="<?php echo __('button_reset'); ?>" ><?php echo __('button_reset'); ?></button>
                                                                    </div>
                                                            </div>
                                                            </form>
                            </div>
                            </div>
                            
                        </div>
			<form method="get" class="form manage_book_dispatch form_manage_booking" name="frmcompany" id="frmcompany" action="companysearch">
				<table class="list_table1 list_table1_manage_booking" border="0" width="100%" cellpadding="6" cellspacing="0">
					<tbody>
						<tr>
							<td width="10%">
								<div class="new_input_field">
									<label><?php echo __('button_search'); ?></label>
									<input type="text" name="search_txt" maxlength="55" id="search_txt" value="" title="<?php echo __('search_manage_booking'); ?>" placeholder="<?php echo __('search_manage_booking'); ?>">
								</div>
							</td>
							<td width="10%">
								<div class="new_input_field">
									<label><?php echo __('location_label'); ?></label>
									<input type="text" name="search_location" maxlength="150" id="search_location" value="" title="<?php echo __('location_label'); ?>" placeholder="<?php echo __('location_label'); ?>">
								</div>
							</td>
							<td width="12%">
								<div class="input-append date reset-part">
									<label><?php echo __('from_date_label'); ?></label>
									<input type="text" name="filter_date" id="filter_date" readonly value="<?php echo date('Y-m-d 00:00');?>" placeholder="<?php echo __('from_date_label'); ?>"/>
									<span class="add-on"><i class="glyphicon glyphicon-remove" id="clearFromDate"></i></span>
								</div>
							</td>
							<td width="12%">
								<div class="input-append date to-reset-part">
									<label><?php echo __('end_date_label'); ?></label>
									<input type="text" name="to_date" id="to_date" readonly="" value="<?php echo date('Y-m-d 23:59');?>" placeholder="<?php echo __('end_date_label'); ?>"/>
									<span class="add-on"><i class="glyphicon glyphicon-remove" id="clearToDate"></i></span>
								</div>
							</td>
							<?php if($companyId == 0) { ?>
							<td width="10%" align="center">
								<label><?php echo __('company'); ?></label>
								<select name="select_company" id="select_company">
									<option value="0"><?php echo __('all_company_label'); ?></option>
									<?php foreach($get_active_company_details as $company){
											$companyName = (isset($company['company_brand_type']) && $company['company_brand_type'] == 'S') ? ucfirst($company["company_name"]).' - Admin' : ucfirst($company["company_name"]);
										 ?>
									<option value="<?php echo $company['cid']; ?>"><?php echo $companyName; ?></option>
									<?php } ?>
								</select>
							</td>
							<?php } else { ?>
								<input type="hidden" name="select_company" id="select_company" value="<?php echo $companyId; ?>"/>
							<?php } ?>
							<td width="10%" align="center">
								<label><?php echo __('model'); ?></label>
								<select name="select_taxi_model" id="select_taxi_model">
									<option value=""><?php echo __('all_vehicle_label'); ?></option>
									<?php foreach($model_details as $model){ ?>
									<option value="<?php echo $model['model_id']; ?>"><?php echo ucfirst($model['model_name']); ?></option>
									<?php } ?>
								</select>
							</td>
							<td width="5%" align="center">
								<div class="new_input_field">
									<label style="width:100%;">&nbsp;</label>
									<input type="button" name="submit_filter" id="submit_filter" title="<?php echo __('filter'); ?>" onclick="all_booking_manage_list_search()">
									<input type="button" name="reset_date" id="reset_date" title="<?php echo __('button_reset'); ?>">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
                        
                        <table cellspacing="0" cellpadding="0" width="100%" align="center" class="dispatch_icons">
                    <tr>
                        <td>
                                <div class="css_checkbox heading_icon_one">
                                    <input type="checkbox" name="status_color" checked value="0" class="click_on_disable" onchange="all_booking_manage_list_search()" >
                                </div>
                                <label><?php echo __('assign'); ?></label>
                        </td>
                        <td>
                                <div class="css_checkbox heading_icon_two">
                                    <input type="checkbox" name="status_color" checked value="6, 7, 10" class="click_on_disable" onchange="all_booking_manage_list_search()" >
                                </div>
                                <label><?php echo __('reassign'); ?></label>
                        </td>
                        <?php /*
                        <td>
                                <div class="heading_icon_three"><input type="checkbox" name="status_color" value="7" onchange="all_booking_manage_list()"></div>
                                <label>Waiting for response</label>
                        </td>
                        */ ?>
                        <td>
                                <div class="css_checkbox heading_icon_four">
                                    <input type="checkbox" name="status_color" checked value="9" class="click_on_disable" onchange="all_booking_manage_list_search()">
                                </div>
                                <label><?php echo __('confirmed'); ?></label>
                        </td>
                        <td>
                                <div class="css_checkbox heading_icon_five">
                                    <input type="checkbox" name="status_color" checked value="3" class="click_on_disable" onchange="all_booking_manage_list_search()">
                                </div>
                                <label><?php echo __('start_to_pickup'); ?></label>
                        </td>
                        <td>
                                <div class="css_checkbox heading_icon_six">
                                    <input type="checkbox" name="status_color" checked value="2" class="click_on_disable" onchange="all_booking_manage_list_search()">
                                </div>
                                <label><?php echo __('inprogress'); ?></label>
                        </td>
                        <td>
                                <div class="css_checkbox heading_icon_sevan">
                                    <input type="checkbox" name="status_color" value="1" class="click_on_disable" onchange="all_booking_manage_list_search()">
                                </div>
                                <label><?php echo __('completed'); ?></label>
                        </td>
                        <td>
                                <div class="css_checkbox heading_icon_eight">
                                    <input type="checkbox" name="status_color" checked value="5" class="click_on_disable" onchange="all_booking_manage_list_search()">
                                </div>
                                <label><?php echo __('waiting_payment'); ?></label>
                        </td>
                        <td>
                                <input type="hidden" name="status_color_cancel" id="status_color_cancel" value="8">
                                <div class="css_checkbox heading_icon_nine">
                                    <input type="checkbox" checked name="status_cancel" id="status_cancel" value="C,R" class="click_on_disable" onchange="all_booking_manage_list_search()">
                                </div>
                                <label><?php echo __('trip_cancelled'); ?></label>
                        </td>
                </tr>
            </table>
                        
                    </div>
              
                <!--Manage Booking-->
	<div class="manage_booking_bottom_outer">
            <div class="manage_booking_bottom manage_booking_bottom_scroll">
            <div class="manage_booking_outer col-md-12 map_manage_booking">
                <div class="form-control_bott">
                    <div id="change_result">
                        <div class="widget">
                            <?php /*<div class="title">
                                <div style="width:auto; float:right; margin: 4px 3px;">
                                	<div class="button greyishB"></div>
                                </div>
                                </div>         */ ?>   
                           
<!--                            <div id="scrollbox3">
    <h1>New Scrolling Window</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a justo erat, volutpat hendrerit dolor. Sed urna nibh, dapibus at egestas non, vulputate ut quam. Morbi a erat tristique tellus varius venenatis. Aenean lacinia sem eget turpis fringilla commodo. Sed lorem nisi, viverra a interdum nec, varius eu enim. Donec ornare, nunc quis eleifend iaculis, nulla eros mollis tellus, quis faucibus risus odio non lectus. Maecenas ac velit non metus rhoncus commodo. Nunc ligula est, ultricies sed mattis sed, dapibus at arcu. Maecenas lacinia nisl ut sem bibendum ac condimentum purus facilisis. Curabitur ut nibh lobortis libero interdum vehicula vel quis nulla.</p>
    
    <p>Suspendisse et massa urna. Donec eu lorem nec felis dapibus aliquam viverra in quam. Suspendisse ultrices, nisi ac venenatis porttitor, erat turpis dapibus augue, sed rutrum nunc ante sed enim. Aliquam et tempus mi. Nullam malesuada, nunc a eleifend pretium, justo lorem tempus justo, id adipiscing dolor ipsum sed velit. Maecenas odio massa, feugiat vel sodales ut, placerat at quam. Cras viverra diam vitae diam elementum vitae aliquet erat tincidunt. Quisque fringilla neque in lacus tempor cursus. Curabitur eget nulla et nisi dignissim tempor vel non risus. Mauris ac ipsum metus, a auctor massa. Nunc eros ante, ullamcorper a mollis nec, aliquam sed est. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>
</div>-->
 <div class="overflow-block overflow-block_outer dispatcher_details overall_scroll"> 
     <div class="dispatch_table">
         <div class="table_head">
             <div class="tbl_book_time"><label><?php echo __('booking_time'); ?></label></div>
             <div class="tbl_pickup_time"><label><?php echo __('booking_pickup_time'); ?></label></div>
             <div class="tbl_act_pickup_time"><label><?php echo __('act_pickup_time'); ?></label></div>
             <div class="tbl_book_type"><label><?php echo __('booking_type'); ?></label></div>
             <div class="tbl_trip_id"><label><?php echo __('trip_id'); ?></label></div>
             <div class="tbl_passenger"><label><?php echo __('passenger'); ?></label></div>
             <?php if ($_SESSION['user_type'] == "A") { ?>
             <div class="tbl_company_name"><label><?php echo __('company_name'); ?></label></div>
             <?php } ?>
             <div class="tbl_driver"><label><?php echo __('driver'); ?></label></div>
             <div class="tbl_vehicle"><label><?php echo __('vehicle'); ?></label></div>
             <div class="tbl_cur_Loc"><label><?php echo __('Current_Location'); ?></label></div>
             <div class="tbl_drop_Loc"><label><?php echo __('Drop_Location'); ?></label></div>
             <div class="tbl_distance"><label><?php echo __('distance'); ?></label></div>
             <div class="tbl_fare"><label><?php echo __('fare') . '(' . $company_currency . ')'; ?></label></div>
             <div class="tbl_status"><label><?php echo __('status'); ?></label></div>
             <div class="tbl_action"><label><?php echo __('action_label'); ?></label></div>
         </div>
         
         <div class="all_booking_manage_list table_body" id="all_booking_manage_list_all"></div>
     </div>
     
     <?php /*
        <table cellspacing="0" cellpadding="0" class="" width="100%" align="center" >
            <thead>
                <tr>
                    <td align="center"><?php echo __('booking_time'); ?></td>
                    <td align="center"><?php echo __('booking_pickup_time'); ?></td>
                    <td align="center"><?php echo __('act_pickup_time'); ?></td>
                    <td align="center"><?php echo __('booking_type'); ?></td>
                    <td align="center"><?php echo __('trip_id'); ?></td>
                    <td align="center"><?php echo __('passenger'); ?></td>
                    <?php if ($_SESSION['user_type'] == "A") { ?>
                        <td align="center"><?php echo __('company_name'); ?></td>
                    <?php } ?>
                    <td align="center"><?php echo __('driver'); ?></td>
                    <td align="center"><?php echo __('vehicle'); ?></td>
                    <td align="center"><?php echo __('Current_Location'); ?></td>
                    <td align="center"><?php echo __('Drop_Location'); ?></td>
                    <td align="center"><?php echo __('distance'); ?></td>
                    <td align="center"><?php echo __('fare') . '(' . $company_currency . ')'; ?></td>
                    <td align="center"><?php echo __('status'); ?></td>
                    <?php /* <td align="center"><?php echo __('notes'); ?></td> */ ?>
                 <?php /*   <td align="center" align="center"><?php echo __('action_label'); ?></td>
<!--                                            <td align="center">&nbsp;</td>-->
                </tr>
            </thead>
            <tbody class="all_booking_manage_list" id="scrollbox3">
                
            </tbody>
        </table> */?>
    </div>
<input type="hidden" id="scroll_enabled" name="scroll_enabled" value="0">

                            <?php /*<div class="overflow-block overflow-block_outer dispatcher_details">                                
                                <table class="scroll" cellspacing="0" cellpadding="0" class="" width="100%" align="center" id="changetr">
                                    <thead id="list_thead">
                                        
                                    </thead>
                                    <tbody id="" class="all_booking_manage_scroll_one" >
					<!---Manage Booking datas append here-->
                                    </tbody>
                                </table>
                                
                            </div> */ ?>
                        </div>
                    </div>
                </div>

            </div>
			  </div>
        </div>        
            <!--Manage Tab-->
            </div>
      
    
    
            <!-- /.panel -->    
                   <div class="col-md-8 col-md-8_scroll map_manage_booking driver_status_height_outer_top"  id="map-section">
                       <div class="jumbotron">
                           <div class="icon-menu">
                               <i class="fa fa-bars"></i>
                           </div>
                       </div>
               <div class="widget margin-bottom">
                   <input type="hidden" name="select_driver_status" id="select_driver_status" value="" />
                    <div id="on_going_trip_map" >
                        <div class="ongoing">
                            <div id="on_going_trip"></div>
                            <div id="on_going_place"></div>
                        </div>
                        <?php if(SHOW_MAP !=1) { ?>
                        <div id="map-canvas" style="width:100%;height:100%;margin:0;"></div>
                        <?php } ?>
                    </div>
            
                </div>
                       
                   </div>
             
           
             <div id="taxi_scroll_one" class="driver_status driver_status_height">
                <!-- Nav tabs
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#free_in_driver" id="free_in_driver_tab" role="tab" data-toggle="tab">Free IN</a></li>
                    <li><a href="#free_out_driver" role="tab" id="free_out_driver_tab" data-toggle="tab">Free OUT</a></li>
                    <li><a href="#active_driver" role="tab" id="active_driver_tab" data-toggle="tab">Active</a></li>
                </ul> -->
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="all_drivers">                        
                    </div>
                    <div class="tab-pane" id="free_in_driver">
                        <!--ul>
                            <li>
                            	<span>Driver Name</span>
                            	<span>Status</span>
                            </li>
                            <li>
                            	<span>Abu</span>
                            	<span>Free</span>
                            </li>                                
                            </ul-->
                    </div>
                    <div class="tab-pane" id="free_out_driver"></div>
                    <div class="tab-pane" id="active_driver"></div>
                    
                    
                </div>
            </div>
            <!--<div id="taxi_scroll_one" class="driver_status driver_status_height">
                <div class="caller_id">
                    <h4>Caller Id</h4>    
                    <ul>
                        <li>
                            <span class="pro_pic">&nbsp;</span>
                            <span class="number">8675045094</span>
                            <span class="phone_icon">&nbsp;</span>
                        </li>
                    </ul>
                </div>
            </div>-->
            

        <!--Manage Tab-->
			</div>
            <!--Manage Booking-->
		<?php /* <div class="rgt_outer">           
            <div class="friends-blog driver_status_bottom">
                <div class="recent_activity">
                    <h4>Recent Activity</h4>
                    <ul class="driver_status_height driver_status_height_re_act" id="recent_activity_content">
                        <!--Recent Activity Content Load Here-->
                        <!--li><span>Test</span></li-->
                    </ul>
                </div>
            </div>
        </div> */?>
		</div>
        </div>
    </div>
    <!-- :form -->
</div>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/script.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/tdispatch_addbooking_new.js"></script>


<script>
	$(document).ready(function () {
		
		<?php if(isset($show_popup['trip_id'])) { ?>
		edit_booking_from_manage('<?php echo $show_popup['trip_id']; ?>');
		<?php } ?>
		/*$( "#taxi_scroll_manage" ).scroll(function() {
		  //$(this).addClass( "sample" );
                      if ($(this).scrollTop() > 1){ 	 
                            $('.fixed_header').addClass("sticky");
                        }
                        else{
                            $('.fixed_header').removeClass("sticky");
                        }
		}); */
		//map_recur();//For intial load
		/*For load initial functions start*/
		//driver_status_dets();
		//recent_activity()
		//driver_list_with_status();
		all_booking_manage_list();
		/*For load initial functions end*/

		//to prevent enter 
		$(window).keydown(function(event) {
            if (event.keyCode == 13) {
				return false;
                //event.preventDefault();
            }
        }); 
        
        $('#myModal').modal('show');
        
         $('#defaultForm').validate({ // initialize the plugin
         //alert('as');
			rules: {
				firstname: {
					required: true,
					minlength: 3
				},
				email: {
					//required: true,
					email: true
				},
				phone: {
					required: true,
				},
				country_code: {
					required: true,
				},
				current_location: {
					required: true,
				},
				taxi_model: {
					required: true,
				},
				pickup_date: {
					required: true,
				},
			},
			messages: {
				firstname: {
					required: "<?php echo __('name_cannot_beempty'); ?>",
					minlength: jQuery.validator.format("<?php echo __('atleast_characters_required'); ?>")
				},
				email: {
					//required: "The email cannot be empty",
					//email: "Your email address must be in the format of name@domain.com"
					email: "<?php echo __('please_enter_valid_email'); ?>"
				},
				country_code: {
					required: "<?php echo __('countrycode_cannot_beempty'); ?>",
				},
				phone: {
					required: "<?php echo __('mobilenumber_cannot_beempty'); ?>",
				},
				current_location: {
					required: "<?php echo __('enter_currentlocation'); ?>",
				},
				taxi_model: {
					required: "<?php echo __('select_the_vehicle'); ?>",
				},
				pickup_date: {
					required: "<?php echo __('select_the_pickup_date'); ?>",
				},
			}
		});
		
		$('#defaultForm_edit').validate({ // initialize the plugin
			rules: {
				edit_firstname: {
					required: true,
					minlength: 3
				},
				edit_email: {
					//required: true,
					email: true
				},
				edit_country_code: {
					required: true,
				},
				edit_phone: {
					required: true,
				},
				edit_current_location: {
					required: true,
				},
				edit_taxi_model: {
					required: true,
				},
				edit_pickup_date: {
					required: true,
				},
			},
			messages: {
				edit_firstname: {
					required: "<?php echo __('name_cannot_beempty'); ?>",
					minlength: jQuery.validator.format("<?php echo __('atleast_characters_required'); ?>")
				},
				edit_email: {
					//required: "The email cannot be empty",
					//email: "Your email address must be in the format of name@domain.com"
					email: "<?php echo __('please_enter_valid_email'); ?>"
				},
				edit_country_code: {
					required: "<?php echo __('countrycode_cannot_beempty'); ?>",
				},
				edit_phone: {
					required: "<?php echo __('mobilenumber_cannot_beempty'); ?>",
				},
				edit_current_location: {
					required: "<?php echo __('enter_currentlocation'); ?>",
				},
				edit_taxi_model: {
					required: "<?php echo __('select_the_vehicle'); ?>",
				},
				edit_pickup_date: {
					required: "<?php echo __('select_the_pickup_date'); ?>",
				},
			}
		});
        
        $("#close_button,#reset").on('click',function(){
			//to reset the form fields
			$("#firstname").val("");
			$("#email").val("");
			$("#country_code").val("");
			$("#phone").val("");
			$("#current_location").val("");
			$("#drop_location").val("");
			$("#notes").val("");
			var today = new Date();
			var Y = today.getFullYear(),
			    month = today.getMonth()+1,
			    dateVal = today.getDate(),
				h = today.getHours(),
				m = today.getMinutes(),
				s = today.getSeconds();
				month = (month < 10) ? "0" + month : month;
				dateVal = (dateVal < 10) ? "0" + dateVal : dateVal;
				h = (h < 10) ? "0" + h : h;
				m = (m < 10) ? "0" + m : m;
				s = (s < 10) ? "0" + s : s;
			var pickupTime = Y + "-" + month + "-" + dateVal + " " + h + ":" + m + ":" + s;
			$("#pickup_date").val(pickupTime);
			$("#taxi_model").val("");
			 $("#email").removeAttr("readonly");
			 $("#firstname").removeAttr("readonly");
			 $("#phone").removeAttr("readonly");
			 $("#country_code").removeAttr("readonly");
			//to reset the distance and fare texts
			$("#find_duration").html("<?php echo __('zero_mins'); ?>");
			$("#find_km").html("<?php echo __('zero_distance'); ?>");
			$("#min_fare").html("0");
			//to hide the error messages
			$("label.error").html("");
			initialize();
		});
		
		$(".edit_reset_btn").on('click',function(){
			var findid = $('#edit_pass_logid').val();
			var default_unit = $('#edit_default_company_unit').val();
			var dataS = "passenger_logid="+trim(findid);		
			$.ajax
			({ 			
				type: "GET",
				url: "<?php echo URL_BASE;?>taxidispatch/edit_booking", 
				data: dataS, 
				cache: false, 
				async: true,
				contentType: "application/json; charset=utf-8",
				dataType: "json",			
				success: function(response) 
				{
					var data=response;
					var details=data[0];
					//console.log(details);
					$("#add_booking").removeClass("in");
					$("#edit_booking").addClass("in");
					//to add id for reset button in edit
					$('#edit_passenger_id').val(details.passengers_id);
					$('#edit_pass_logid').val(details.pass_logid);
					$('#edit_total_fare').val(details.approx_fare);
					var appDistance = (default_unit == "MILES") ? (details.approx_distance*0.621371) : details.approx_distance;
					appDistance = parseFloat(appDistance).toFixed(2);
					$('#edit_distance_km').val(appDistance);
					
					$('#edit_firstname').val(details.passenger_name);
					$('#edit_email').val(details.passenger_email);
					$('#edit_phone').val(details.passenger_phone);
					$('#edit_country_code').val(details.country_code);

					$('#edit_current_location').val(details.current_location);
					$('#edit_pickup_lat').val(details.pickup_latitude);
					$('#edit_pickup_lng').val(details.pickup_longitude);
					
					$('#edit_drop_location').val(details.drop_location);
					$('#edit_drop_lat').val(details.drop_latitude);
					$('#edit_drop_lng').val(details.drop_longitude);
					$('#edit_pickup_date').val(details.pickup_time);
					$('#edit_pickup_date_db').val(details.pickup_time);
					$('#edit_luggage').val(details.luggage);
					$('#edit_no_passengers').val(details.no_passengers);
					$('#edit_notes').val(details.notes_driver);
					$('#edit_taxi_model').val(details.taxi_modelid);

					$('#edit_city_id').val(details.search_city);
					var minStrExist = details.approx_duration.indexOf("mins")
					if(details.approx_duration != '') {	
						if(minStrExist < 0) {	//condition to check "mins"	string already exist
							$('#edit_find_duration').html(details.approx_duration+" mins");
							$('#edit_total_duration').val(details.approx_duration+" mins");
						} 
						else {
							$('#edit_find_duration').html(details.approx_duration);
							$('#edit_total_duration').val(details.approx_duration);
						}
					} else {
						$('#edit_find_duration').html('0 mins');
						$('#edit_total_duration').val('0 mins');
					}
					$('#edit_find_km').html(appDistance+" "+default_unit);
					$('#edit_min_fare').html(details.approx_fare);
					var durationSecs = details.approx_duration * 60;
					//to get the approximate fare
					if(minStrExist < 0) { //this calculation should be done only for later booking from app
						 calculate_totalfare_flag(details.approx_distance, details.taxi_modelid, '', details.search_city, durationSecs);
					}
				} 
			});
		});
        
        /* //script to hide dispatch button if future time is selected as pickuptime
        $("#pickup_date").on('change',function(){
			var pickupDate = $(this).val();//datetime is in yyyy-mm-dd hh:ii:ss format
			var dateString = pickupDate,
			dateParts = dateString.split(' '),
			timeParts = dateParts[1].split(':'),
			date;
			dateParts = dateParts[0].split('-');

			date = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2], timeParts[0], timeParts[1], timeParts[2]);
			var today = new Date();
			if(date.getTime() > today.getTime()){
				$('#dispatch').attr('disabled','disabled');
			} else {
				$('#dispatch').removeAttr('disabled');
			}
		}); */

       $("#dispatch").on('click',function(){
		   $("#timeError").hide().html("");
			var addValid = $("#defaultForm").valid({});
			if(addValid) {
				var current_date="<?php echo date('Y-m-d');?>";
					     current_date=current_date.replace("-","");
					     current_date=current_date.replace("-","");
						 pick_date=logid[4];
					     pick_date=pick_date.replace("-","");
					     pick_date=pick_date.replace("-","");
					     
					 var x=0;
					if(pick_date < current_date){
						if (confirm("<?php echo __('areyousuredo_youwantto_dispatch_previousdatetrip'); ?>") == true) {
						 x=1;
						}
					}else if(pick_date > current_date){
						if (confirm("<?php echo __('areyousuredo_youwantto_dispatch_futuredatetrip'); ?>") == true) {
						 x=1;
						}
					}else if(pick_date == current_date){
						 x=1;
					}
				
				$('#dispatch').attr('disabled','disabled');
				$('#dispatch_id').val("Dispatch");
				document.getElementById('defaultForm').submit();
			}
			return addValid;
		});
        $("#update_submit").on('click',function() {
			$("#timeEditError").hide().html("");
			editValid = 1;
			var edit_pickup_date_db = $('#edit_pickup_date_db').val();
			var edit_pickup_date = $('#edit_pickup_date').val();
			if(edit_pickup_date_db != edit_pickup_date) {
				var currentdate = new Date(); 
				pickup_date = new Date(edit_pickup_date);

				var diff = pickup_date.getTime() - currentdate.getTime();
				var msec = diff;
				var hh = Math.floor(msec / 1000 / 60 / 60);
				msec -= hh * 1000 * 60 * 60;
				var mm = Math.floor(msec / 1000 / 60);
				msec -= mm * 1000 * 60;
				if(hh < 1) {
					editValid = 0;
					$("#timeEditError").show().html("<?php echo __('later_booking_need_miniumonehour'); ?>");
					return false;
				}
			}
			if(editValid) {
				$('#update_dispatch').attr('disabled','disabled');
				$('#update_dispatch_id').val("Dispatch");
				document.getElementById('defaultForm_edit').submit();
			}
			return editValid;
		});
		
		$("#update_dispatch").on('click',function() {
			$("#timeEditError").hide().html("");
			var editValid = $("#defaultForm_edit").valid({});
			if(editValid) {
				$('#update_dispatch').attr('disabled','disabled');
				$('#update_dispatch_id').val("Dispatch");
				document.getElementById('defaultForm_edit').submit();
			}
			return editValid;
		});

		$("#create").on('click',function(){
			$("#timeError").hide().html("");
			var addValid = $("#defaultForm").valid({});
			var pickup_date = $("#defaultForm input[name='pickup_date']").val();
			var currentdate = new Date(); 
			pickup_date = new Date(pickup_date);

			var diff = pickup_date.getTime() - currentdate.getTime();
			var msec = diff;
			var hh = Math.floor(msec / 1000 / 60 / 60);
			msec -= hh * 1000 * 60 * 60;
			var mm = Math.floor(msec / 1000 / 60);
			msec -= mm * 1000 * 60;
			//console.log(hh + ":" + mm);
			if(hh < 1) {
				addValid = 0;
				$("#timeError").show().html("<?php echo __('later_booking_need_miniumonehour'); ?>");
				return false;
			}
			if(addValid) {
				$('#create').attr('disabled','disabled');
				$('#dispatch').attr('disabled','disabled');
				$('#create_id').val("Dispatch");
				document.getElementById('defaultForm').submit();
			}
			return addValid;
		});
	
		/* //script to hide dispatch button if future time is selected as pickuptime
		$("#edit_pickup_date").on('change',function(){
			var pickupDate = $(this).val();//datetime is in yyyy-mm-dd hh:ii:ss format
			var dateString = pickupDate,
			dateParts = dateString.split(' '),
			timeParts = dateParts[1].split(':'),
			date;
			dateParts = dateParts[0].split('-');

			date = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2], timeParts[0], timeParts[1], timeParts[2]);
			var today = new Date();
			if(date.getTime() > today.getTime()){
				$('#update_dispatch').attr('disabled','disabled');
			} else {
				$('#update_dispatch').removeAttr('disabled');
			}
		});*/
		
		initMap();
                
                window.timer_resize = setInterval(function()
                {
                    //alert("here");
                    refresh_map();
                },2000);
                
                 $('.icon-menu').click(function(){
                    window.timer_resize = setInterval(function()
                    {
                    //alert("here");
                    refresh_map();
                    },2000);
                    if($(this).hasClass('active_page') == false)
                    {

						$('.driver_status_height').fadeIn();
						$('.manage_booking_bottom').hide();                
						$('#map-section').removeClass('col-md-8');
						$('#map-section').addClass('col-md-12');
						$(this).addClass('active_page');
                    }
                    else
                    {
                    $('.driver_status_height').fadeIn();
                    $(this).removeClass('active_page');
                    $('#map-section').removeClass('col-md-12');
                    $('#map-section').addClass('col-md-8');
                    $('.manage_booking_bottom').show();
                    }
                });
                
		
		// For 10 seconds interval for without refresh
		setInterval(function()
		{
			//driver_list_with_status(),
			all_booking_manage_list();
		},5000); // For 5 seconds interval   */
	});
	var locations = {}; //A repository for markers (and the data from which they were contructed).

	$('#drop_location').on('change', function() {
		var a=$('#drop_location').val();
		if(a == ""){
			$('#drop_lat').val('');
			$('#drop_lng').val('');
			$('#distance_km').val('0');
			$('#total_fare').val('0');
			$('#min_fare').html('0');
			$('#find_km').html('0');
			$('#find_duration').html('0');
		}
	});

	$('#edit_drop_location').on('change', function() {
		var a=$('#edit_drop_location').val();
		if(a == ""){
			$('#edit_drop_lat').val('');
			$('#edit_drop_lng').val('');
			$('#edit_distance_km').val('0');
			$('#edit_total_fare').val('0');
			$('#edit_min_fare').html('0');
			$('#edit_find_km').html('0');
			$('#edit_find_duration').html('0');
			$('#edit_total_duration').val('0');
		}
	});

	$('#taxi_model').on('change', function() {
		var a=$('#taxi_model').val();
		if(a == ""){
			$('#total_fare').val('0');
			$('#min_fare').html('0');
		}
	});

	$('#edit_taxi_model').on('change', function() {
		var a=$('#edit_taxi_model').val();
		if(a == ""){
			$('#edit_total_fare').val('0');
			$('#edit_min_fare').html('0');
		}
	});
	
	
	$('#select_taxi_model').on('change', function() {
		var taxi_model=$(this).val();
		//to get the filtered model in taxi model dropdown
		$("#taxi_model").val(taxi_model);
		$("#edit_taxi_model").val(taxi_model);
	});

	//initial dataset for markers
	var locs = {
		<?php 	$b=1; 
			$a=count($all_company_map_list);
			if(count($all_company_map_list) > 0) { 
			for($i=0;$i<$a;$i++){ ?>
			    <?php echo $b; ?>: {
					<?php
					$book_now="";
					if($all_company_map_list[$i]['driver_status']=="F" && $all_company_map_list[$i]['shift_status']=="IN"){
						$driver_info='<span style="color:green">'.__('free_in').'</span>';
						//$book_now='<button type="button" class="btn btn-outline btn-primary btn-xs" name="bookingnow" onclick="bookingnow_click(this.id);" id="driverid_'.$all_company_map_list[$i]['driver_id'].'" >'.__('booknow').'</button>';
					}elseif($all_company_map_list[$i]['driver_status']=="F" && $all_company_map_list[$i]['shift_status']=="OUT"){
						$driver_info='<span style="color:blue">'.__('free_out').'</span>';
					}elseif($all_company_map_list[$i]['driver_status']=="B"){
						$driver_info='<span style="color:#07841E">'.__('trip_assigned').'</span>';
					}elseif($all_company_map_list[$i]['driver_status']=="A"){
						$driver_info='<span style="color:red">'.__('hired').'</span>';
					}
					$update_date=$all_company_map_list[$i]['update_date'];
					$drv_info='<span class="info-content">'.ucfirst($all_company_map_list[$i]['name']).'</span>';
					$drv_info.='</br>';
					$drv_info.='<span class="info-content">'.$driver_info.'</span>';
					$drv_info.='</br>';
					$drv_info.='<span class="info-content">'.$update_date.'</span>';
					if($book_now !=""){
						$drv_info.='</br>';
						//$drv_info.='<span class="info-content">'.$book_now.'</span>';
					}
					?>
					//info: '<?php echo $all_company_map_list[$i]['name'] ; ?>',
					info: '<?php echo $drv_info; ?>',
					lat: <?php echo $all_company_map_list[$i]['latitude'] ; ?>,
					lng: <?php echo $all_company_map_list[$i]['longitude'] ; ?>,
					status: '<?php echo $all_company_map_list[$i]['driver_status'] ; ?>',
					shift_status: '<?php echo $all_company_map_list[$i]['shift_status'] ; ?>'
			    },
			<?php $b++; } } ?>
	};
	
	var mainMap;
	function initMap(){
		mainMap = new google.maps.Map(document.getElementById('map-canvas'), {
			zoom: 12,
			maxZoom: 18,
			minZoom: 1,
			streetViewControl: false,
			center: new google.maps.LatLng(<?php echo $current_latitude;?>,<?php echo $current_longitude;?>),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});
	}
        
	function refresh_map()
	{
		var resize_map = google.maps.event.trigger(mainMap, 'resize');
		clearInterval(window.timer_resize);
	}
	
	function to_timestamp(date)
	{
		return (new Date(date.split(".").join("-")).getTime())/1000;
	}
	
	//google.maps.event.trigger(initMap, 'resize');

	function change_email_phone_exit()
	{
		
		//alert("sdf");
		event.preventDefault();
		/*alert("asddf");return false;
		var dataS = "pass_email="+pass_email+"&pass_phone="+pass_phone+"&pass_id="+pass_id;
		var url_path = "<?php echo URL_BASE; ?>taxidispatch/check_pass_phone_email_exist";
		$.ajax({
			type: "GET",
			url:url_path,
			data: dataS,
			async: true,
			success:function(data){
				alert(data);return false;
				if(data != 0){
					alert("Email/Phone already exist");
					return false;
				}
			},
			error:function() {
				//alert('failed'); 
			}
		}); */
		
		
	
	}

	var infowindow = new google.maps.InfoWindow();
	
	setMarkers(locs,1); // 1 as-Deafult Search Drivers
	
	function map_recur()
	{
		//alert('map_recur');
		var status = $("#select_driver_status").val();
		if(status !=""){
			var driver_status=$("#select_driver_status").val();
		}else{
			var driver_status="";
		}

		var model = $("#select_taxi_model").val();
		if(model !=""){
			var taxi_model=$("#select_taxi_model").val();
		}else{
			var taxi_model="";
		}

		var company = $("#select_company").val();
		if(company !=""){
			var taxi_company=$("#select_company").val();
		}else{
			var taxi_company="";
		}

		//driver_status_dets();
		//all_booking_manage_list();
		
		$('#admin_company_id').val(taxi_company);
		$('#edit_admin_company_id').val(taxi_company);

		if(driver_status!='')
		{
			//$('#map-canvas').html('<img src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" >');
			var Path = "<?php echo URL_BASE; ?>";
			
			if(driver_status!=""){
				var dataS = "driver_status="+driver_status+"&taxi_company="+taxi_company;
				var url_path = Path+"taxidispatch/driver_status_details_search_new";
			}
			
			var markers=new Array();
			$.ajax({
				type: "GET",
				url:url_path,
				data: dataS, 
				async: true,
				contentType: "application/json; charset=utf-8",
				dataType: "json",
				success:function(data){
					//For remove old markers
					removeMarkers(locations);
					setMarkers(data); // 2-As Driver status Search
					if(data != "")
					{
						$('#on_going_trip').html('');
					}
					else
					{
						$('#on_going_trip').html('<?php echo __('no_driver_found'); ?>');
					}
				},
				error:function() {
					//alert('failed'); 
				}
			});
		}else{
			//alert('map_recur else');
			var Path = "<?php echo URL_BASE; ?>";
			var url_path = Path+"taxidispatch/view_all_driverss";
			var dataS = "taxi_model="+taxi_model+"&taxi_company="+taxi_company;
			var markers;
			
			$.ajax({
				url:url_path,
				type: "GET",
				data: dataS, 
				async: true,
				contentType: "application/json; charset=utf-8",
				dataType: "json",			
				success:function(response){
					//For remove old markers
					removeMarkers(locations);
					setMarkers(response); // 2-As Driver status Search
					if(response != "")
					{
						$('#on_going_trip').html('');	
					}
					else
					{
						//$('#on_going_trip').html('<?php echo __('no_login_drivers'); ?>');	
						$('#on_going_trip').html('');	
					}
				},
				error:function() { //alert('failed'); 
				},
			});
		}
	}

	function driver_status_dets()
	{
		var company = $("#select_company").val();
		if(company !=""){
			var taxi_company=$("#select_company").val();
		}else{
			var taxi_company="";
		}
		
		var taxi_model = $("#select_taxi_model").val();
		
		var Path = "<?php echo URL_BASE; ?>";
		var all_drivers = "";
		var dataS = "driver_status="+all_drivers+"&taxi_company="+taxi_company+"&taxi_model="+taxi_model;
		var url_path = Path+"taxidispatch/driver_status_search_details";
		$.ajax({
			type: "GET",
			url:url_path,
			data: dataS, 
			async: true,
			success:function(data){
				
				if(data != ""){
					var response = data.split("#");
					$('#all_drivers').html(response[0]);	
					$('#driver_dets_count').html(response[1]);	
				}
			},
			error:function() {
				//alert('failed'); 
			}
		});
	}
	
	function driver_list_with_status()
	{
		var taxi_company=$("#select_company").val();
		var taxi_model = $("#select_taxi_model").val();
		var driver_status = $("#select_driver_status").val();
		
		var Path = "<?php echo URL_BASE; ?>";
		
		var dataS = "driver_status="+driver_status+"&taxi_company="+taxi_company+"&taxi_model="+taxi_model;
		var url_path = Path+"taxidispatch/driver_list_with_status";
		$.ajax({
			type: "GET",
			url:url_path,
			data: dataS, 
			async: true,
			success:function(data){
				if(data != ""){
					var response = data.split("#");
					$('#all_drivers').html(response[1]);	
					$('#driver_dets_count').html(response[2]);	
					var locations_val = $.parseJSON(response[0]);
					//For remove old markers
					removeMarkers(locations);
					setMarkers(locations_val); // 2-As Driver status Search
					if(locations_val != "")
					{
						$('#on_going_trip').html('');	
					}
					else
					{
						//$('#on_going_trip').html('<?php echo __('no_login_drivers'); ?>');	
						$('#on_going_trip').html('');
					}
				}
			},
			error:function() {
				//alert('failed'); 
			}
		});
	}

	function recent_activity()
	{
		var Path = "<?php echo URL_BASE; ?>";
		var dataS = "";
		var url_path = Path+"taxidispatch/get_recent_activity";
		var response;
		$.ajax({
			type: "GET",
			url: url_path, 
			data: dataS, 
			cache: false, 
			dataType: 'html',
			success: function(response){
				$('#recent_activity_content').html(response);
			}		 
		});	
	}
	
	function all_booking_manage_list_search()
	{
        $('.all_booking_manage_list').html("<div class='nodata'><p>Loading data.Please wait...</p></div>");
        $('.click_on_disable').attr("disabled","disabled");
            var company = $("#select_company").val();
            var scrollEnable = $("#scroll_enabled").val();
          
		//alert(company);
		if(company !=""){
			var taxi_company=$("#select_company").val();
		}else{
			var taxi_company="";
		}
		
		var favorite = [];
		$.each($("input[name='status_color']:checked"), function(){            
			favorite.push($(this).val());
                        $("#scroll_enabled").val("0");
		});
		var status_color_cancel = $('#status_color_cancel').val();
		favorite.push(status_color_cancel);
		
		var status_color=favorite.join(", ");

		var status_cancel = [];
		$.each($("input[name='status_cancel']:checked"), function(){            
			status_cancel.push($(this).val());
		});
		
		var search_txt = $('#search_txt').val();
		var search_location = $('#search_location').val();
		var filter_date = $('#filter_date').val();
		var to_date = $('#to_date').val();
		var booking_filter = $('#booking_filter').val();
			
		var taxi_model = $("#select_taxi_model").val();
		var driver_status = $("#select_driver_status").val();
		
		//alert(status_cancel);
		var Path = "<?php echo URL_BASE; ?>";
		var dataS = "travel_status="+status_color+"&status_cancel="+status_cancel+"&driver_status="+driver_status+"&taxi_company="+taxi_company+"&taxi_model="+taxi_model+"&search_txt="+search_txt+"&search_location="+search_location+"&filter_date="+filter_date+"&to_date="+to_date+"&booking_filter="+booking_filter;
		var url_path = Path+"taxidispatch/all_booking_list_manage";
		var response;
		$.ajax({
			type: "GET",
			url: url_path, 
			data: dataS, 
			cache: false, 
			dataType: 'html',
			success: function(response){
				$('.click_on_disable').removeAttr("disabled");
				var data = response.split("@");
				
				
					$('#all_drivers').html(data[1]);	
					$('#driver_dets_count').html(data[2]);	
					var locations_val = $.parseJSON(data[0]);
					//For remove old markers
					removeMarkers(locations);
					setMarkers(locations_val); // 2-As Driver status Search
					if(locations_val != "")
					{
						$('#on_going_trip').html('');	
					}
					else
					{
						//$('#on_going_trip').html('<?php echo __('no_login_drivers'); ?>');	
						$('#on_going_trip').html('');
					}
				
				
				/*if(data[3] == 0) {
					$("#list_thead").hide();
				} else {
					$("#list_thead").show();
				}*/
           
				$('.all_booking_manage_list').html(data[4]);
                                
                                //console.log(data[1]);
                              
                                 if(scrollEnable == 0){
                                       
                                         $("#scroll_enabled").val("1");
                                    } 

				//edit booking in dashboard
				$('.oddtr').bind('click', function(){
					var isrdata = this.id;
					var findid = isrdata.split('_').pop();
					var default_unit = $('#edit_default_company_unit').val();
					var editbook=$("#edit_book_tab").attr("class");
					if(editbook=="edit_book_active"){
						$("#edit_book_tab").removeClass('edit_book_active');
						$("#edit_book_tab").removeClass('edit_booking_'+findid);
						$("#edit_book_tab").hide();
						$("#eb_tab").removeClass('active');
						$("#add_booking_tab").html('Add Booking');
					}else{
						$("#add_book_tab").hide();                                
						$("#edit_book_tab").addClass('edit_book_active');
						$("#edit_book_tab").addClass('edit_booking_'+findid);
						$("#edit_book_tab").show();
						//
						$("#eb_tab").addClass('active');
						$("#add_booking_tab").html('Edit Booking');
					}

					var dataS = "passenger_logid="+trim(findid);		
					$.ajax
					({ 			
						type: "GET",
						url: "<?php echo URL_BASE;?>taxidispatch/edit_booking", 
						data: dataS, 
						cache: false, 
						async: true,
						contentType: "application/json; charset=utf-8",
						dataType: "json",			
						success: function(response) 
						{
							var data=response;
							var details=data[0];
							$("#timeEditError").hide().html("");
							$("#add_booking").removeClass("in");
							$("#edit_booking").addClass("in");
							//to add id for reset button in edit
							$(".edit_reset_btn").attr('id','reset_'+findid);
							$('#edit_passenger_id').val(details.passengers_id);
							$('#edit_pass_logid').val(details.pass_logid);
							$('#edit_total_fare').val(details.approx_fare);
							var appDistance = (default_unit == "MILES") ? (details.approx_distance*0.621371) : details.approx_distance;
							appDistance = parseFloat(appDistance).toFixed(2);
							$('#edit_distance_km').val(appDistance);
							
							$('#edit_firstname').val(details.passenger_name);
							$('#edit_email').val(details.passenger_email);
							$('#edit_phone').val(details.passenger_phone);
							$('#edit_country_code').val(details.country_code);

							$('#edit_current_location').val(details.current_location);
							$('#edit_pickup_lat').val(details.pickup_latitude);
							$('#edit_pickup_lng').val(details.pickup_longitude);
							
							$('#edit_drop_location').val(details.drop_location);
							$('#edit_drop_lat').val(details.drop_latitude);
							$('#edit_drop_lng').val(details.drop_longitude);
							$('#edit_pickup_date').val(details.pickup_time);
							$('#edit_pickup_date_db').val(details.pickup_time);
							$('#edit_luggage').val(details.luggage);
							$('#edit_no_passengers').val(details.no_passengers);
							$('#edit_notes').val(details.notes_driver);
							$('#edit_taxi_model').val(details.taxi_modelid);
							$('#edit_city_id').val(details.search_city);
							$('#edit_cityname').val(details.city_name);
							
							var minStrExist = details.approx_duration.indexOf("mins")
							if(details.approx_duration != '') {	
								if(minStrExist < 0) {	//condition to check "mins"	string already exist
									$('#edit_find_duration').html(details.approx_duration+" mins");
									$('#edit_total_duration').val(details.approx_duration+" mins");
								} 
								else {
									$('#edit_find_duration').html(details.approx_duration);
									$('#edit_total_duration').val(details.approx_duration);
								}
							} else {
								$('#edit_find_duration').html('0 mins');
								$('#edit_total_duration').val('0 mins');
							}
							
							
							if(appDistance != '') {
								$('#edit_find_km').html(appDistance+" "+default_unit);
							} else {
								$('#edit_find_km').html("0 "+default_unit);
							}
							
							$('#edit_min_fare').html(details.approx_fare);
							var durationSecs = details.approx_duration * 60;
							//to get the approximate fare
							if(minStrExist < 0) { //this calculation should be done only for later booking from app
								 calculate_totalfare_flag(details.approx_distance, details.taxi_modelid, '', details.search_city, durationSecs);
							 }
							
							//to get the company value as selected in company drop down
							if(details.company_id != 0) {
								$("#select_company").val(details.company_id);
								/*map_recur();
								driver_status_dets();*/
								//driver_list_with_status();
								all_booking_manage_list();
							}

							var travel_status=details.travel_status;
							if(travel_status == 0 || travel_status == 7 || travel_status == 10){
								//$("#cancel_button").hide();
								$('#update_dispatch').removeAttr('disabled');
								var dateString = details.pickup_time,
								dateParts = dateString.split(' '),
								timeParts = dateParts[1].split(':'),
								date;
								dateParts = dateParts[0].split('-');

								/* //script to hide dispatch button if future time is selected as pickuptime
								date = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2], timeParts[0], timeParts[1], timeParts[2]);
								var today = new Date();
								if(date.getTime() > today.getTime()){
									$('#update_dispatch').attr('disabled','disabled');
								} else {
									$('#update_dispatch').removeAttr('disabled');
								} */
							}else{
								/*if(travel_status == 9) {
									$("#cancel_button").show();
								} */
								$('#update_dispatch').attr('disabled','disabled');
							}							
							//to hide the dispatch button if pickup time is future														
						} 
					});
					
				});
				//edit booking in dashboard - end
				//dispatch button click function
				$('.update_dispatch').click(function() {
					var thisid = this.id;
					//var pass_logid = thisid.split('_').pop();
					var logid = thisid.split('_');
					
					var current_date="<?php echo date('Y-m-d');?>";
					     current_date=current_date.replace("-","");
					     current_date=current_date.replace("-","");
						 pick_date=logid[4];
					     pick_date=pick_date.replace("-","");
					     pick_date=pick_date.replace("-","");
					     
					 var x=0;
					if(pick_date < current_date){
						if (confirm("Are you sure do you want to dispatch previous date trip") == true) {
						 x=1;
						}
					}else if(pick_date > current_date){
						if (confirm("Are you sure do you want to dispatch future date trip") == true) {
						 x=1;
						}
					}else if(pick_date == current_date){
						 x=1;
					}
					
					
					if(x==1){
					checkPassengerStatus(logid[2],logid[3],thisid);
				    }
					/* var data = "company_id="+logid[3];
					var url_path = "<?php echo URL_BASE;?>taxidispatch/checkdispatchsettings";
					$.ajax({
						type: "POST",
						url:url_path,
						data: data, 
						async: true,
						success:function(res){
							var setArr = res.split(',');
							if(setArr.length > 1) {
								$("#dispatchSetting").modal({show:true});
								$(".dispatch_sel").on('click',function(){
									var seleVal = $(this).val();
									window.location.href="<?php echo URL_BASE;?>taxidispatch/dashboard?splid="+logid[2]+"&taxi_company="+logid[3]+"&dispatch_type="+seleVal;
								});
								//
							} else {
								window.location.href="<?php echo URL_BASE;?>taxidispatch/dashboard?splid="+logid[2]+"&taxi_company="+logid[3]+"&dispatch_type="+setArr[0];
							}
							//console.log(setArr.length);return false;
						},
						error:function() {
							//alert('failed'); 
						}
					}); */
				});
				//cancel trip
				$(".cancelBtn").on('click',function(){
					var cancel_Submit = confirm('<?php echo __('sure_want_cancel'); ?>');
					if(cancel_Submit == true)
					{
						var cancelArr = $(this).attr('id').split("_");
						var pass_logid = cancelArr[1];
						var url= URL_BASE+"taxidispatch/cancel_booking/?pass_logid="+pass_logid;
						$.post(url, {
						}, function(response){
						document.location.href=URL_BASE+"taxidispatch/dashboard";
						});
					} else {
						<?php if($_SESSION['user_type'] == 'A') { ?>
						//to deselect the selected company
						$("#select_company").val("0");
						//to get the default data - start
						//driver_list_with_status();
						all_booking_manage_list();
						<?php } ?>
						$("#edit_book_tab").removeClass('edit_book_active');
						$("#eb_tab").removeClass('active');
						//to get the default data - end
						$("#edit_book_tab").hide();
						$("#add_booking_tab").html('Add Booking');
						return false;
					}
				});
				
				var $table = $('table.scroll'),
				$bodyCells = $table.find('tbody tr:first').children(),
				colWidth;

			 // Get the tbody columns width array
				colWidth = $bodyCells.map(function() {
					return $(this).width();
				}).get();
				
				// Set the width of thead columns
				$table.find('thead tr').children().each(function(i, v) {
					$(v).width(colWidth[i]);
				}); 
			}		 
		});	
	}
	
	
	function all_booking_manage_list()
	{
        //  scrolify($('#tblNeedsScrolling'));		
//                                         $('.overall_scroll .newtable').enscroll({
//                                             showOnHover: false,
//                                             verticalTrackClass: 'track3',
//                                             verticalHandleClass: 'handle3'            
//                                         });
            var company = $("#select_company").val();
            var scrollEnable = $("#scroll_enabled").val();
          
		//alert(company);
		if(company !=""){
			var taxi_company=$("#select_company").val();
		}else{
			var taxi_company="";
		}
		
		var favorite = [];
		$.each($("input[name='status_color']:checked"), function(){            
			favorite.push($(this).val());
                        $("#scroll_enabled").val("0");
		});
		var status_color_cancel = $('#status_color_cancel').val();
		favorite.push(status_color_cancel);
		
		var status_color=favorite.join(", ");

		var status_cancel = [];
		$.each($("input[name='status_cancel']:checked"), function(){            
			status_cancel.push($(this).val());
		});
		
		var search_txt = $('#search_txt').val();
		var search_location = $('#search_location').val();
		var filter_date = $('#filter_date').val();
		var to_date = $('#to_date').val();
		var booking_filter = $('#booking_filter').val();
			
		var taxi_model = $("#select_taxi_model").val();
		var driver_status = $("#select_driver_status").val();
		
		//alert(status_cancel);
		var Path = "<?php echo URL_BASE; ?>";
		var dataS = "travel_status="+status_color+"&status_cancel="+status_cancel+"&driver_status="+driver_status+"&taxi_company="+taxi_company+"&taxi_model="+taxi_model+"&search_txt="+search_txt+"&search_location="+search_location+"&filter_date="+filter_date+"&to_date="+to_date+"&booking_filter="+booking_filter;
		var url_path = Path+"taxidispatch/all_booking_list_manage";
		var response;
		$.ajax({
			type: "GET",
			url: url_path, 
			data: dataS, 
			cache: false, 
			dataType: 'html',
			success: function(response){
				
				var data = response.split("@");
				
				
					$('#all_drivers').html(data[1]);	
					$('#driver_dets_count').html(data[2]);	
					var locations_val = $.parseJSON(data[0]);
					//For remove old markers
					removeMarkers(locations);
					setMarkers(locations_val); // 2-As Driver status Search
					if(locations_val != "")
					{
						$('#on_going_trip').html('');	
					}
					else
					{
						//$('#on_going_trip').html('<?php echo __('no_login_drivers'); ?>');	
						$('#on_going_trip').html('');
					}
				
				
				/*if(data[3] == 0) {
					$("#list_thead").hide();
				} else {
					$("#list_thead").show();
				}*/
           
				$('.all_booking_manage_list').html(data[4]);
                                
                                //console.log(data[1]);
                              
                                 if(scrollEnable == 0){
                                       
                                         $("#scroll_enabled").val("1");
                                    } 

				//edit booking in dashboard
				$('.oddtr').bind('click', function(){
					var isrdata = this.id;
					var findid = isrdata.split('_').pop();
					var default_unit = $('#edit_default_company_unit').val();
					var editbook=$("#edit_book_tab").attr("class");
					if(editbook=="edit_book_active"){
						$("#edit_book_tab").removeClass('edit_book_active');
						$("#edit_book_tab").removeClass('edit_booking_'+findid);
						$("#edit_book_tab").hide();
						$("#eb_tab").removeClass('active');
						$("#add_booking_tab").html('Add Booking');
					}else{
						$("#add_book_tab").hide();                                
						$("#edit_book_tab").addClass('edit_book_active');
						$("#edit_book_tab").addClass('edit_booking_'+findid);
						$("#edit_book_tab").show();
						//
						$("#eb_tab").addClass('active');
						$("#add_booking_tab").html('Edit Booking');
					}

					var dataS = "passenger_logid="+trim(findid);		
					$.ajax
					({ 			
						type: "GET",
						url: "<?php echo URL_BASE;?>taxidispatch/edit_booking", 
						data: dataS, 
						cache: false, 
						async: true,
						contentType: "application/json; charset=utf-8",
						dataType: "json",			
						success: function(response) 
						{
							var data=response;
							var details=data[0];
							$("#timeEditError").hide().html("");
							$("#add_booking").removeClass("in");
							$("#edit_booking").addClass("in");
							//to add id for reset button in edit
							$(".edit_reset_btn").attr('id','reset_'+findid);
							$('#edit_passenger_id').val(details.passengers_id);
							$('#edit_pass_logid').val(details.pass_logid);
							$('#edit_total_fare').val(details.approx_fare);
							var appDistance = (default_unit == "MILES") ? (details.approx_distance*0.621371) : details.approx_distance;
							appDistance = parseFloat(appDistance).toFixed(2);
							$('#edit_distance_km').val(appDistance);
							
							$('#edit_firstname').val(details.passenger_name);
							$('#edit_email').val(details.passenger_email);
							$('#edit_phone').val(details.passenger_phone);
							$('#edit_country_code').val(details.country_code);

							$('#edit_current_location').val(details.current_location);
							$('#edit_pickup_lat').val(details.pickup_latitude);
							$('#edit_pickup_lng').val(details.pickup_longitude);
							
							$('#edit_drop_location').val(details.drop_location);
							$('#edit_drop_lat').val(details.drop_latitude);
							$('#edit_drop_lng').val(details.drop_longitude);
							$('#edit_pickup_date').val(details.pickup_time);
							$('#edit_pickup_date_db').val(details.pickup_time);
							$('#edit_luggage').val(details.luggage);
							$('#edit_no_passengers').val(details.no_passengers);
							$('#edit_notes').val(details.notes_driver);
							$('#edit_taxi_model').val(details.taxi_modelid);
							$('#edit_city_id').val(details.search_city);
							$('#edit_cityname').val(details.city_name);
							
							var minStrExist = details.approx_duration.indexOf("mins")
							if(details.approx_duration != '') {	
								if(minStrExist < 0) {	//condition to check "mins"	string already exist
									$('#edit_find_duration').html(details.approx_duration+" mins");
									$('#edit_total_duration').val(details.approx_duration+" mins");
								} 
								else {
									$('#edit_find_duration').html(details.approx_duration);
									$('#edit_total_duration').val(details.approx_duration);
								}
							} else {
								$('#edit_find_duration').html('0 mins');
								$('#edit_total_duration').val('0 mins');
							}
							
							
							if(appDistance != '') {
								$('#edit_find_km').html(appDistance+" "+default_unit);
							} else {
								$('#edit_find_km').html("0 "+default_unit);
							}
							
							$('#edit_min_fare').html(details.approx_fare);
							var durationSecs = details.approx_duration * 60;
							//to get the approximate fare
							if(minStrExist < 0) { //this calculation should be done only for later booking from app
								 calculate_totalfare_flag(details.approx_distance, details.taxi_modelid, '', details.search_city, durationSecs);
							 }
							
							//to get the company value as selected in company drop down
							if(details.company_id != 0) {
								$("#select_company").val(details.company_id);
								/*map_recur();
								driver_status_dets();*/
								//driver_list_with_status();
								all_booking_manage_list();
							}

							var travel_status=details.travel_status;
							if(travel_status == 0 || travel_status == 7 || travel_status == 10){
								//$("#cancel_button").hide();
								$('#update_dispatch').removeAttr('disabled');
								var dateString = details.pickup_time,
								dateParts = dateString.split(' '),
								timeParts = dateParts[1].split(':'),
								date;
								dateParts = dateParts[0].split('-');

								/* //script to hide dispatch button if future time is selected as pickuptime
								date = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2], timeParts[0], timeParts[1], timeParts[2]);
								var today = new Date();
								if(date.getTime() > today.getTime()){
									$('#update_dispatch').attr('disabled','disabled');
								} else {
									$('#update_dispatch').removeAttr('disabled');
								} */
							}else{
								/*if(travel_status == 9) {
									$("#cancel_button").show();
								} */
								$('#update_dispatch').attr('disabled','disabled');
							}							
							//to hide the dispatch button if pickup time is future														
						} 
					});
					
				});
				//edit booking in dashboard - end
				//dispatch button click function
				$('.update_dispatch').click(function() {
					
					
					var thisid = this.id;
					//var pass_logid = thisid.split('_').pop();
					var logid = thisid.split('_');
					
					var current_date="<?php echo date('Y-m-d');?>";
					     current_date=current_date.replace("-","");
					     current_date=current_date.replace("-","");
						 pick_date=logid[4];
					     pick_date=pick_date.replace("-","");
					     pick_date=pick_date.replace("-","");
					 var x=0;
					if(pick_date < current_date){
						if (confirm("Are you sure do you want to dispatch previous date trip") == true) {
						 x=1;
						}
					}else if(pick_date > current_date){
						if (confirm("Are you sure do you want to dispatch future date trip") == true) {
						 x=1;
						}
					}else if(pick_date == current_date){
						 x=1;
					}
					
					if(x==1){
					checkPassengerStatus(logid[2],logid[3],thisid);
				    }
					/* var data = "company_id="+logid[3];
					var url_path = "<?php echo URL_BASE;?>taxidispatch/checkdispatchsettings";
					$.ajax({
						type: "POST",
						url:url_path,
						data: data, 
						async: true,
						success:function(res){
							var setArr = res.split(',');
							if(setArr.length > 1) {
								$("#dispatchSetting").modal({show:true});
								$(".dispatch_sel").on('click',function(){
									var seleVal = $(this).val();
									window.location.href="<?php echo URL_BASE;?>taxidispatch/dashboard?splid="+logid[2]+"&taxi_company="+logid[3]+"&dispatch_type="+seleVal;
								});
								//
							} else {
								window.location.href="<?php echo URL_BASE;?>taxidispatch/dashboard?splid="+logid[2]+"&taxi_company="+logid[3]+"&dispatch_type="+setArr[0];
							}
							//console.log(setArr.length);return false;
						},
						error:function() {
							//alert('failed'); 
						}
					}); */
				});
				//cancel trip
				$(".cancelBtn").on('click',function(){
					var cancel_Submit = confirm('<?php echo __('sure_want_cancel'); ?>');
					if(cancel_Submit == true)
					{
						var cancelArr = $(this).attr('id').split("_");
						var pass_logid = cancelArr[1];
						var url= URL_BASE+"taxidispatch/cancel_booking/?pass_logid="+pass_logid;
						$.post(url, {
						}, function(response){
						document.location.href=URL_BASE+"taxidispatch/dashboard";
						});
					} else {
						<?php if($_SESSION['user_type'] == 'A') { ?>
						//to deselect the selected company
						$("#select_company").val("0");
						//to get the default data - start
						//driver_list_with_status();
						all_booking_manage_list();
						<?php } ?>
						$("#edit_book_tab").removeClass('edit_book_active');
						$("#eb_tab").removeClass('active');
						//to get the default data - end
						$("#edit_book_tab").hide();
						$("#add_booking_tab").html('Add Booking');
						return false;
					}
				});
				
				var $table = $('table.scroll'),
				$bodyCells = $table.find('tbody tr:first').children(),
				colWidth;

			 // Get the tbody columns width array
				colWidth = $bodyCells.map(function() {
					return $(this).width();
				}).get();
				
				// Set the width of thead columns
				$table.find('thead tr').children().each(function(i, v) {
					$(v).width(colWidth[i]);
				}); 
			}		 
		});	
	}
	
	function checkPassengerStatus(trip_id, company_id,all_data)
	{
		var data = "trip_id="+trip_id+"&company_id="+company_id;
		var url_path = "<?php echo URL_BASE; ?>taxidispatch/checkPassengerStatus";
		$.ajax({
			type: "POST",
			url:url_path,
			data: data, 
			async: true,
			beforeSend : function() {
				$(".pendingCount, .inTrip, .button_1, .button_2, .processLabel").hide();
			},
			success:function(jsonData) {
				$(".logId").val(all_data);
				var data = JSON.parse(jsonData);
				var pending_count = data.pending_payment_count;
				var in_trip = data.in_trip;
				if(data != "") {
					if(pending_count > 0 && in_trip > 0) {
						$("#passengerPendingPayment").modal('show');
						$(".pendingCount, .inTrip, .button_2").show();
						$("#trip_count").text(pending_count);
					} else if(pending_count == 0 && in_trip > 0) {
						$("#passengerPendingPayment").modal('show');
						$(".pendingCount, .button_1").hide(); $(".inTrip, .button_2").show();
						$("#trip_count").text(pending_count);
					} else if(pending_count > 0 && in_trip == 0) {
						$("#passengerPendingPayment").modal('show');
						$(".pendingCount, .processLabel, .button_1").show(); $(".inTrip, .button_2").hide();
						$("#trip_count").text(pending_count);
					} else {
						pendingPayYes();
					}
				} else {
					pendingPayYes();
				}
			},
			error:function() {
				//alert('failed'); 
			}
		});
	}
	
	function pendingPayYes()
	{
		$("#passengerPendingPayment").modal('hide');
			var logid = $(".logId").val().split('_');
			var data = "company_id="+logid[3];
			var url_path = "<?php echo URL_BASE;?>taxidispatch/checkdispatchsettings";
			$.ajax({
				type: "POST",
				url:url_path,
				data: data, 
				async: true,
				success:function(res){
					var setArr = res.split(',');
					if(setArr.length > 1) {
						$("#dispatchSetting").modal('show');
						//$("#dispatchSetting").modal({show:true});
						$(".dispatch_sel").on('click',function(){
							var seleVal = $(this).val();
							window.location.href="<?php echo URL_BASE;?>taxidispatch/dashboard?splid="+logid[2]+"&taxi_company="+logid[3]+"&dispatch_type="+seleVal;
						});
						//
					} else {
						window.location.href="<?php echo URL_BASE;?>taxidispatch/dashboard?splid="+logid[2]+"&taxi_company="+logid[3]+"&dispatch_type="+setArr[0];
					}
					//console.log(setArr.length);return false;
				},
				error:function() {
					//alert('failed'); 
				}
			});
	}
	
	function pendingPayNo()
	{
		$("#passengerPendingPayment").modal('hide');
	}
	
	function setMarkers(locObj) {
	    $.each(locObj, function (key, loc) {
		//console.log(loc);
	        if (!locations[key] && loc.lat !== undefined && loc.lng !== undefined) {
	            //Marker has not yet been made (and there's enough data to create one).
				
				//Driver Status icon change when(Active,Free,Busy)
				if(loc.status=="A"){
					var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/orange.png'; ?>"; //RED
					//var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/red_car.png'; ?>"; //RED
				}else if(loc.status=="F" && loc.shift_status == 'OUT'){
					var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/blue.png'; ?>"; //BLUE
					//var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/blu_car.png'; ?>"; //BLUE
				}else if(loc.status=="B"){
					var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/red.png'; ?>"; // GREEN
					//var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/green_car.png'; ?>"; // GREEN
				}else if(loc.status=="F" && loc.shift_status == 'IN'){
					var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/green.png'; ?>"; // YELLOW
					//var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/orange_car.png'; ?>"; // YELLOW
				}else{
					var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/orange.png'; ?>"; // YELLOW
					//var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/orange_car.png'; ?>"; // YELLOW
				}
				
	            //Create marker 
	            loc.marker = new google.maps.Marker({
					//zoom: 11,
	                position: new google.maps.LatLng(loc.lat, loc.lng),
	                map: mainMap,
	                icon: status_icon,
	            }); 
				
	            //Attach click listener to marker
	            google.maps.event.addListener(loc.marker, 'mouseover', (function (key) {
	                return function () {
	                    infowindow.setContent(locations[key].info);
	                    infowindow.open(mainMap, locations[key].marker);
	                }
	            })(key));

	            //Remember loc in the `locations` so its info can be displayed and so its marker can be deleted.
	            locations[key] = loc;
	        } else if (locations[key] && loc.remove) {
	            //Remove marker from map
	            if (locations[key].marker) {
	                locations[key].marker.setMap(null);
	            }
	            //Remove element from `locations`
	            delete locations[key];
	        } else if (locations[key]) {
	            //Update the previous data object with the latest data.
	            $.extend(locations[key], loc);
	            if (loc.lat !== undefined && loc.lng !== undefined) {
	                //Update marker position (maybe not necessary but doesn't hurt).
	                locations[key].marker.setPosition(
	                new google.maps.LatLng(loc.lat, loc.lng));
	            }
	            if(loc.status !== undefined) {
					//Driver Status icon change when(Active,Free,Busy)
					if(loc.status=="A"){
						var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/red.png'; ?>"; //RED
						//var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/red_car.png'; ?>"; //RED
					}else if(loc.status=="F" && loc.shift_status == 'OUT'){
						var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/blue.png'; ?>"; //BLUE
						//var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/blu_car.png'; ?>"; //BLUE
					}else if(loc.status=="B"){
						var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/green.png'; ?>"; // GREEN
						//var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/green_car.png'; ?>"; // GREEN
					}else if(loc.status=="F" && loc.shift_status == 'IN'){
						var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/orange.png'; ?>"; // YELLOW
						//var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/orange_car.png'; ?>"; // YELLOW
					}else{
						var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/orange.png'; ?>"; // YELLOW
						//var status_icon="<?php echo BOOTSTRAP_IMGPATH.'/orange_car.png'; ?>"; // YELLOW
					}
	                locations[key].marker.setIcon(status_icon);
	            }
	            //locations[key].info looks after itself.
	        }
	    });
	}
	function removeMarkers(locObj)
	{
		$.each(locObj, function (key, loc) {
			if (locations[key].marker) {
				locations[key].marker.setMap(null);
			}
			//Remove element from `locations`
			delete locations[key];
		});
	}

	function bookingnow_click(drv_id)
	{
		var driver_id = drv_id.split('_').pop();
		$('#driver_id').val(driver_id);
		
		var addbook=$("#add_book_tab").attr("class");
		if(addbook=="add_book_active"){
			$("#add_book_tab").removeClass('add_book_active');
			$("#add_book_tab").hide();
			$("#ab_tab").removeClass('active');
		}else{
			$("#edit_book_tab").hide();
			$("#edit_book_tab").removeClass('edit_book_active');                                
			$("#add_book_tab").addClass('add_book_active');
			$("#add_book_tab").show();
			$("#ab_tab").addClass('active');
		}
	}

	$('#cancel_button').click(function() {
		var cancel_Submit = confirm('<?php echo __('sure_want_cancel'); ?>');
		if(cancel_Submit == true)
		{
			var pass_logid = $('#edit_pass_logid').val();
			var url= URL_BASE+"taxidispatch/cancel_booking/?pass_logid="+pass_logid;
			$.post(url, {
			}, function(response){
			document.location.href=URL_BASE+"taxidispatch/dashboard";
			});
		} else {
			<?php if($_SESSION['user_type'] == 'A') { ?>
			//to deselect the selected company
			$("#select_company").val("0");
			//to get the default data - start
			//driver_list_with_status();
			all_booking_manage_list();
			<?php } ?>
			$("#edit_book_tab").removeClass('edit_book_active');
			$("#eb_tab").removeClass('active');
			//to get the default data - end
			$("#edit_book_tab").hide();
			$("#add_booking_tab").html('Add Booking');
			return false;
		}
	});
	
	

	google.maps.event.addListener(mainMap, "click", function(event) 
	{
		//alert('ok');
		var lat = event.latLng.lat();
		var lng = event.latLng.lng();
		$('#current_location').blur();
		codeLatLng(lat,lng,'current_location');	
		
		//set_hidden(lat,lng);
	});
	google.maps.event.addListener(mainMap, "rightclick", function(event) 
	{
		var lat = event.latLng.lat();
		var lng = event.latLng.lng();	 
		$('#drop_location').blur();
		codeLatLng(lat,lng,'drop_location');
		clearMarkers();	
			
	});

	function codeLatLng(lat,lng,id) 
	{	 
		 var latlng = new google.maps.LatLng(lat, lng);
		  geocoder.geocode({'latLng': latlng}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				//alert(google.maps.GeocoderStatus);
			  if (results[1]) 
			  {		  
				 $('#'+id).val(results[1].formatted_address); 
				 pickup_drop_location_marker(results[1].formatted_address,id,latlng)
				 $('#'+id+'_lat').val(lat); 
				 $('#'+id+'_lng').val(lng); 
							
			  } else {
				alert('<?php echo __("no_result_found"); ?>');
			  }
			  attempts = 0;
			}
			else if (status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
				  setTimeout(function() {
						codeLatLng(lat,lng,id);
				  }, 200); 
			}
			 else {
			  alert('<?php echo __("gecoder_failed"); ?>' + status);
			  attempts = 0;
			}
		  });
	}

	function pickup_drop_location_marker(place, id, latlng) {
		
		var iconBase = '<?php echo PUBLIC_IMGPATH.' / ' ; ?>';
		if (id == 'drop_location') {
			end = latlng;
		}
		if (id == 'current_location') {
			start = latlng;
		}
		// First, remove any existing markers from the map.
		for (var i = 0; i < markerArray.length; i++) {
			markerArray[i].setMap(null);
		}
		markerArray = [];
		var request = {
			origin: start,
			destination: end,
			travelMode: google.maps.TravelMode.DRIVING
		};
		clearMarkers();
		directionsService.route(request, function(response, status) {
			if (status == google.maps.DirectionsStatus.OK) {
				//var warnings = document.getElementById('warnings_panel');
				//warnings.innerHTML = '<b>' + response.routes[0].warnings + '</b>';
				directionsDisplay.setDirections(response);
				showSteps(response);
			}
		});
	}

	function showSteps(directionResult) {
	  markerArray = [];
	  var myRoute = directionResult.routes[0].legs[0];
	  for (var i = 0; i < myRoute.steps.length; i++) {
		var marker = new google.maps.Marker({
		  position: myRoute.steps[i].start_location,
		  map: mainMap
		});
		clearMarkers();
		attachInstructionText(marker, myRoute.steps[i].instructions);
		markerArray[i] = marker;
	  }
	}

	function attachInstructionText(marker, text) {
	  google.maps.event.addListener(marker, 'click', function() {
		// Open an info window when the marker is clicked on,
		// containing the text of the step.
		stepDisplay.setContent(text);
		stepDisplay.open(mainMap, marker);
	  });
	}
	
	
	
	//function to get edit booking tab open while edit booking from manage booking page
	function edit_booking_from_manage(findid)
	{	
		var default_unit = $('#edit_default_company_unit').val();	
		var dataS = "passenger_logid="+trim(findid);
		$("#eb_tab").addClass('active');
		$("#add_booking_tab").html('Edit Booking');	
		$.ajax
		({ 			
			type: "GET",
			url: "<?php echo URL_BASE;?>taxidispatch/edit_booking", 
			data: dataS, 
			cache: false, 
			async: true,
			contentType: "application/json; charset=utf-8",
			dataType: "json",			
			success: function(response) 
			{
				if(response == '') {
					//redirect to dashboard if unknown trip id passed through url
					window.location.href = 'dashboard';
				}
				$("#edit_book_tab").show();
				var data=response;
				var details=data[0];
				//console.log(details);
				$("#add_booking").removeClass("in");
				$("#edit_booking").addClass("in");
				
				$('#edit_passenger_id').val(details.passengers_id);
				$('#edit_pass_logid').val(details.pass_logid);
				$('#edit_total_fare').val(details.approx_fare);
				var appDistance = (default_unit == "MILES") ? (details.approx_distance*0.621371) : details.approx_distance;
				appDistance = parseFloat(appDistance).toFixed(2);
				$('#edit_distance_km').val(appDistance);
				
				$('#edit_firstname').val(details.passenger_name);
				$('#edit_email').val(details.passenger_email);
				$('#edit_phone').val(details.passenger_phone);
				$('#edit_country_code').val(details.country_code);

				$('#edit_current_location').val(details.current_location);
				$('#edit_pickup_lat').val(details.pickup_latitude);
				$('#edit_pickup_lng').val(details.pickup_longitude);
				
				$('#edit_drop_location').val(details.drop_location);
				$('#edit_drop_lat').val(details.drop_latitude);
				$('#edit_drop_lng').val(details.drop_longitude);
				$('#edit_pickup_date').val(details.pickup_time);
				$('#edit_pickup_date_db').val(details.pickup_time);
				$('#edit_luggage').val(details.luggage);
				$('#edit_no_passengers').val(details.no_passengers);
				$('#edit_notes').val(details.notes_driver);
				$('#edit_taxi_model').val(details.taxi_modelid);

				$('#edit_city_id').val(details.search_city);
				var minStrExist = details.approx_duration.indexOf("mins")
				if(details.approx_duration != '') {	
					if(minStrExist < 0) {	//condition to check "mins"	string already exist
						$('#edit_find_duration').html(details.approx_duration+" mins");
						$('#edit_total_duration').val(details.approx_duration+" mins");
					} 
					else {
						$('#edit_find_duration').html(details.approx_duration);
						$('#edit_total_duration').val(details.approx_duration);
					}
				} else {
					$('#edit_find_duration').html('0 mins');
					$('#edit_total_duration').val('0 mins');
				}
				
				if(appDistance != '') {
					$('#edit_find_km').html(appDistance+" "+default_unit);
				} else {
					$('#edit_find_km').html("0 "+default_unit);
				}
				$('#edit_min_fare').html(details.approx_fare);
				
				var durationSecs = details.approx_duration * 60;
				//to get the approximate fare
				if(minStrExist < 0) { //this calculation should be done only for later booking from app
					 calculate_totalfare_flag(details.approx_distance, details.taxi_modelid, '', details.search_city, durationSecs);
				}
				
				//to get the company value as selected in company drop down
				if(details.company_id != 0) {
					$("#select_company").val(details.company_id);
					map_recur();
				}
				
				var travel_status=details.travel_status;
				if(travel_status == 0 || travel_status == 7 || travel_status == 10){
					//$("#cancel_button").hide();
					$('#update_dispatch').removeAttr('disabled');
				}else{
					/*if(travel_status == 9) {
						$("#cancel_button").show();
					} */
					$('#update_dispatch').attr('disabled','disabled');
				}
				
				//to hide the dispatch button if pickup time is future
				var dateString = details.pickup_time,
				dateParts = dateString.split(' '),
				timeParts = dateParts[1].split(':'),
				date;
				dateParts = dateParts[0].split('-');
				/* //script to hide dispatch button if future time is selected as pickuptime
				date = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2], timeParts[0], timeParts[1], timeParts[2]);
				var today = new Date();
				if(date.getTime() > today.getTime()){
					$('#update_dispatch').attr('disabled','disabled');
				} else {
					$('#update_dispatch').removeAttr('disabled');
				} */
			} 
		});
	}
	
</script>

<?php /** Display passenger pending trip alert start **/ ?>
<input type="hidden" name="logId" class="logId" value=""/>
<div class="modal fade" id="passengerPendingPayment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" ><?php echo __('pending_payment_details_popup'); ?></h4>
			</div>
			<div class="modal-body">
				<p class="inTrip" style="display:none;color:#fc5446"><?php echo __('passenger_already_in_trip'); ?></p>
				<p class="pendingCount" style="display:none;"><?php echo __("not_paid_trip"); ?> <span id="trip_count"></span> <?php echo __("trips_popup"); ?><span class="processLabel"><?php echo __("sure_want_to_process"); ?></span></p>
			</div>
			<div class="modal-footer">
				<p class="button_1" style="display:none;">
					<button type="button" onclick="pendingPayYes();" class="btn btn-default">Yes</button>
					<button type="button" onclick="pendingPayNo();" class="btn btn-default" data-dismiss="modal">No</button>
				</p>
				<p class="button_2" style="display:none;">
					<button type="button" onclick="pendingPayNo();" class="btn btn-default" data-dismiss="modal">Ok</button>
				</p>
			</div>
		</div>
	</div>
</div>

<?php /** Display passenger pending trip alert end **/ ?>

<!---Popup on Driver Search--->

<div class="modal fade" id="dispatchSetting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" ><?php echo __('tdispatch_setting'); ?></h4>
      </div>
      <div class="modal-body">
		  <input type="radio" name="dispatch_setting" class="dispatch_sel" value="1"> Auto
		  <input type="radio" name="dispatch_setting" class="dispatch_sel" value="2"> Manual
		  
      </div>
      <div class="modal-footer">
       <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
      </div>
    </div>
  </div>
</div>

<?php //echo $show_popup['show_pass_logid'];exit;
if(isset($show_popup['show_pass_logid'])) { ?>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo __('choose_driver_from_list'); ?></h4>
      </div>
      <div class="modal-body">
			<div class="controls">
				<div class="new_input_field">
				  <span class="add-on"></span>
				  <input type="text" name="search_driver" id="search_driver" value="" onKeyUp="driver_details_new()">
				</div>
				<input type="hidden" name="passenger_log_id" id="passenger_log_id" value="<?php echo $show_popup['show_pass_logid']; ?>">
			</div>
			<div id="show_process">
			<div id="driver_details"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">    
    $(document).ready(function(){
		driver_details_new();
    });    
 </script>
<?php } ?>


<?php 
if(isset($show_popup['splid'])) { ?>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id="model_close_one"  aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo __('choose_driver_from_list'); ?></h4>
      </div>
      <div class="modal-body">
			<div class="controls">
				<div class="new_input_field">
				  <span class="add-on"></span>
				  <input type="text" name="search_driver" id="search_driver" value="" onKeyUp="driver_details_new()">
				</div>
				<input type="hidden" name="passenger_log_id" id="passenger_log_id" value="<?php echo $show_popup['splid']; ?>">
				<input type="hidden" name="admin_companyid" id="admin_companyid" value="<?php echo $show_popup['taxi_company']; ?>">
			</div>
			<div id="show_process">
			<div id="driver_details"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default"  id="model_close_two">Close</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">    
    $(document).ready(function(){       
		driver_details_new();   
    });    

/**************************** Search Driver when the dispatcher going to select the driver *******************************/

$('#driver_details p').click(function() {
	var detailsid = this.id;
	var findimg = detailsid.split('_');

	var pass_logid = $('#passenger_log_id').val();	
	
	var dataS = "pass_logid="+pass_logid+"&driver_id="+findimg[0]+"&taxi_id="+findimg[1]+"&driver_away_in_km="+findimg[2];	
	
	$("#show_process").html('<img src="<?php echo IMGPATH; ?>loader.gif">');
	$.ajax
	({ 			
		type: "GET",
		url: "<?php echo URL_BASE;?>taxidispatch/updatebooking", 
		data: dataS, 
		cache: false, 
		dataType: 'html',
		success: function(response) 
		{ 		
			$("#show_process").html('');
			//console.log(response);
			//document.location.href="<?php echo URL_BASE;?>tdispatch/managebooking/#stuff";
			 window.location="<?php echo URL_BASE;?>taxidispatch/dashboard";
		} 
		 
	});	
});
                                                
                                      
            

/***************************************************************************/				    
 </script> 
 
<?php } ?>
