<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<link href="<?php echo URL_BASE; ?>public/frontend/logged_in/css/jRating.jquery.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/frontend/logged_in/js/jRating.jquery.js"></script>
<div class="dash_details">
	<?php echo View::factory(USERVIEW.'website_user/left_menu'); ?>
	<?php $unit = (DEFAULT_UNIT == 1) ? __("miles_label") : __("kms"); ?>
	<section id="right_side_part">
		<div class="top_part">
			<div class="bread_com">
				<ul>
					<li><a href="#" title="<?php echo __("home_breadcrumb"); ?>"><?php echo __("home_breadcrumb"); ?></a><i class="fa fa-angle-double-right"></i></li>
					<li><p><?php echo __("button_home"); ?></p></li>
				</ul>
			</div>
			<?php echo View::factory(USERVIEW.'website_user/upcoming_trip_alert'); ?>
		</div>
		<div class="white_bg">
			<div class="recent_upcoming">
				<div class="recent_trip">
					<h1><?php echo __("recent_trips"); ?></h1>
					<ul>
						<?php if(count($recent_trips) > 0 ) {
							$rt = 1; foreach($recent_trips as $r) {
								$current_location = isset($r["current_location"]) ? $r["current_location"] : "";
								$drop_location = isset($r["drop_location"]) ? $r["drop_location"] : "";
							?>
						<li>
                                                    <div class="trip_lst">
							<?php if($current_location != "") { ?>
								<div class="passenger_current_location"><p><?php echo $current_location; ?></p></div>
							<?php } ?>
							<?php if($drop_location != "") { ?>
								<div class="passenger_drop_location"><p><?php echo $drop_location; ?> <br> <?php echo $r["pickup_time"]; ?></p></div>
							<?php } ?>
							
                                                            
							</div>
							<div class="view_trip_det">
								<a href="javascript:;" class="view_details pluse_icon" data-condition="1" data-id="<?php echo $rt; ?>-recent" title="View more detail"></a>
							</div>
						</li>
						<div id="recent-trip-<?php echo $rt; ?>" class="trip_mor_det" style="display:none;">
                                                   <?php
								if(file_exists(DOCROOT.SITE_DRIVER_IMGPATH.$r['profile_picture']) && ($r['profile_picture'] != "")) {
									$profile_image = URL_BASE.SITE_DRIVER_IMGPATH.$r['profile_picture'];
								} else {
									$profile_image = URL_BASE."public/frontend/logged_in/images/profile_noimage.png";
								}
							?>
								<div class="user"><img width="38" height="38" alt="user image" src="<?php echo $profile_image; ?>"/></div>
								<div class="trip_user_det">
							<?php 
								//$siteusers = Model::factory('siteusers');
								//$rating = $siteusers->get_driver_rating($r["driver_id"]);
								$rating = array_key_exists($r["driver_id"], $alldriver_ratings) ? $alldriver_ratings[$r["driver_id"]] : 0;
							?>
							<p><label><?php echo __("driver_name"); ?> </label><span> <?php echo ucfirst($r["name"]); ?></span></p>
							<p><label><?php echo __("phone_label"); ?> </label><span> <?php echo $r["country_code"]." ".$r["phone"]; ?></span></p>
								<div class="rate"> <label><?php echo __("rating"); ?></label>
									<script type="text/javascript">
										$(document).ready(function(){
											$(".basic<?php echo $r['passengers_log_id']; ?>").jRating({
												bigStarsPath : '<?php echo URL_BASE; ?>public/frontend/logged_in/images/detail_star.png', // path of the icon stars.png
												smallStarsPath : '<?php echo URL_BASE; ?>public/frontend/logged_in/images/small.png', // path of the icon small.png
												length : 5,
												rateMax : 5,
												step:true,
												showRateInfo: false,
												canRateAgain : true,
												nbRates : 10,
												onError : function() {
													
												}
											});
										});
									</script>
									<div class="basic<?php echo $r['passengers_log_id']; ?> jDisabled star_small_rat" id="<?php echo $rating; ?>"></div>
									<?php /* <div class="rating">
										<div class="half" <?php if(($rating > 0.2 && $rating == 0.5) || ($rating > 0.5)){ ?> style="color: #5eba2c;" <?php } ?> title="0.5 stars"></div>
										<div class = "full" <?php if(($rating > 0.5 && $rating == 1) || ($rating > 1)){ ?> style="color: #5eba2c;" <?php } ?> title="1 star"></div>
										<div class="half" <?php if(($rating > 1 && $rating == 1.5) || ($rating > 1.5)){ ?> style="color: #5eba2c;" <?php } ?> title="1.5 stars"></div>
										<div class = "full" <?php if(($rating > 1.5 && $rating == 2) || ($rating > 2)){ ?> style="color: #5eba2c;" <?php } ?> title="2 stars"></div>
										<div class = "full" <?php if(($rating > 2.5 && $rating == 3) || ($rating > 3)){ ?> style="color: #5eba2c;" <?php } ?> title="3 stars"></div>
										<div class="half" <?php if(($rating > 3 && $rating == 3.5) || ($rating > 3.5)){ ?> style="color: #5eba2c;" <?php } ?> title="3.5 stars"></div>
										<div class = "full" <?php if(($rating > 3.5 && $rating == 4) || ($rating > 4)){ ?> style="color: #5eba2c;" <?php } ?> title="4 stars"></div>
										<div class="half" <?php if(($rating > 4 && $rating == 4.5) || ($rating > 4.5)){ ?> style="color: #5eba2c;" <?php } ?>  title="4.5 stars"></div>
										<div class = "full" <?php if(($rating > 4.5 && $rating == 5) || ($rating > 5)){ ?> style="color: #5eba2c;" <?php } ?>  title="5 stars"></div>
									</div> */ ?>
								</div>
							</div>
						</div>
						<?php $rt++; } } else { ?>
							<div class="no_data"><?php echo __('no_data'); ?></div>
						<?php } ?>
					</ul>
				</div>
				<div class="recent_trip upcoming_trip_det">
					<?php $status = 0; if(count($upcomming_trips) > 0 ) {
						$status = (isset($upcomming_trips[0]["travel_status"])) ? $upcomming_trips[0]["travel_status"] : 0;
					} ?>
					<h1><?php 
					echo ($status == 2 || $status == 5) ? __("trip_in_progress") : __("upcomming_trips"); ?></h1>
					<ul>
						<?php if(count($upcomming_trips) > 0 ) {
							$up = 1; foreach($upcomming_trips as $u) {
								$cancellationFree = (FARE_SETTINGS == 2) ? $u['cancellation_fare'] : CANCELLATION_FARE;
								//echo '--'.$cancellationFree;exit;
								$askCvv = 0;
								if($cancellationFree != 0) {
									$askCvv = ($u['creditcardCnt'] > 0) ? 1 : 2;
								}
								$current_location = isset($u["current_location"]) ? $u["current_location"] : "";
								$drop_location = isset($u["drop_location"]) ? $u["drop_location"] : "";
						?>
						<li>
							<div class="trip_lst">
								<?php if($current_location != "") { ?>
									<div class="passenger_current_location"><p><?php echo $current_location; ?></p></div>
								<?php } ?>
								<?php if($drop_location != "") { ?>
									<div class="passenger_drop_location"><p><?php echo $drop_location; ?></div>
								<?php } ?>
                                  <p class="book_dat_time">
									<?php echo $u["pickup_time"]; ?>
									<br>
									<?php echo __("status_label"); ?> :
									<?php
									/** Edited by Logeswaran 02-12-2016
									 *  Ride Later Activitites
									 */
										$status_value = "";
										
										if($u["travel_status"] == 9 && $u["driver_reply"] == "A") {
											$status_value = __("confirmed");
										} else if($u["travel_status"] == 3) {
											$status_value = __("driver_arrived");
										} else if($u["travel_status"] == 2) {
											$status_value = __("trip_started");
										} else if($u["travel_status"] == 5) {
											$status_value = __("waiting_payment");
										}
										else if($u["travel_status"] == 0 && $u["driver_reply"]=="") {
											$status_value = __("trip_not_confirmed");
										}
										echo $status_value;
									?>
								</p>
								<?php 
								$in_trip = array(2,5);
								if(!in_array($u["travel_status"],$in_trip)){ ?>
								<a class="cancel_trp" href="javascript:;" onclick="passengerCancel('<?php echo $u['passengers_log_id'] ?>','<?php echo $askCvv; ?>','<?php echo $u['driver_id']; ?>');"><?php echo __('cancel'); ?> trip</a> <?php } ?>
							</div>
							<?php if($u["travel_status"] != 0 && $u["driver_reply"]!="")
							{
							?>
							<div class="view_trip_det">
								<a href="javascript:;" class="view_details pluse_icon" data-condition="1" data-id="<?php echo $up; ?>-upcomming" title="View more detail"></a>
							</div>
							<?php
							}
							?>
						</li>
						<div id="upcomming-trip-<?php echo $up; ?>" class="trip_mor_det" style="display:none;">
						<?php
							if($u["driver_id"]!="" && $u["driver_id"] > 0) {
								if(file_exists(DOCROOT.SITE_DRIVER_IMGPATH.$u['profile_picture']) && ($u['profile_picture'] != "")) {
									$profile_image = URL_BASE.SITE_DRIVER_IMGPATH.$u['profile_picture'];
								} else {
									$profile_image = URL_BASE."public/frontend/logged_in/images/profile_noimage.png";
								} 
								
							?>
							<div class="user"><img width="38" height="38" alt="user image" src="<?php echo $profile_image; ?>"/></div>
							<div class="trip_user_det">
							<?php
								//$siteusers = Model::factory('siteusers');
								//$rating = $siteusers->get_driver_rating($u["driver_id"]);
								
								$rating = array_key_exists($u["driver_id"], $alldriver_ratings) ? $alldriver_ratings[$u["driver_id"]] : 0;
							?>
							<p><label><?php echo __("driver_name"); ?> </label><span> <?php echo ucfirst($u["name"]); ?></span></p>
							<p><label><?php echo __("phone_label"); ?> </label><span> <?php echo $u["country_code"]." ".$u["phone"]; ?></span></p>
							<div class="rate"> <label><th><?php echo __("rating"); ?></th></label>
								<script type="text/javascript">
									$(document).ready(function(){
										$(".basic<?php echo $u['passengers_log_id']; ?>").jRating({
											bigStarsPath : '<?php echo URL_BASE; ?>public/frontend/logged_in/images/detail_star.png', // path of the icon stars.png
											smallStarsPath : '<?php echo URL_BASE; ?>public/frontend/logged_in/images/small.png', // path of the icon small.png
											length : 5,
											rateMax : 5,
											step:true,
											showRateInfo: false,
											canRateAgain : true,
											nbRates : 10,
											onError : function() {
												
											}
										});
									});
								</script>
								<div class="basic<?php echo $u['passengers_log_id']; ?> jDisabled star_small_rat" id="<?php echo $rating; ?>"></div>
								<?php /* <div class="rating">
									<div class="half" <?php if(($rating > 0.2 && $rating == 0.5) || ($rating > 0.5)){ ?> style="color: #5eba2c;" <?php } ?> title="0.5 stars"></div>
									<div class = "full" <?php if(($rating > 0.5 && $rating == 1) || ($rating > 1)){ ?> style="color: #5eba2c;" <?php } ?> title="1 star"></div>
									<div class="half" <?php if(($rating > 1 && $rating == 1.5) || ($rating > 1.5)){ ?> style="color: #5eba2c;" <?php } ?> title="1.5 stars"></div>
									<div class = "full" <?php if(($rating > 1.5 && $rating == 2) || ($rating > 2)){ ?> style="color: #5eba2c;" <?php } ?> title="2 stars"></div>
									<div class="half" <?php if(($rating > 2 && $rating == 2.5) || ($rating > 2.5)){ ?> style="color: #5eba2c;" <?php } ?> title="2.5 stars"></div>
									<div class = "full" <?php if(($rating > 2.5 && $rating == 3) || ($rating > 3)){ ?> style="color: #5eba2c;" <?php } ?> title="3 stars"></div>
									<div class="half" <?php if(($rating > 3 && $rating == 3.5) || ($rating > 3.5)){ ?> style="color: #5eba2c;" <?php } ?> title="3.5 stars"></div>
									<div class = "full" <?php if(($rating > 3.5 && $rating == 4) || ($rating > 4)){ ?> style="color: #5eba2c;" <?php } ?> title="4 stars"></div>
									<div class="half" <?php if(($rating > 4 && $rating == 4.5) || ($rating > 4.5)){ ?> style="color: #5eba2c;" <?php } ?>  title="4.5 stars"></div>
									<div class = "full" <?php if(($rating > 4.5 && $rating == 5) || ($rating > 5)){ ?> style="color: #5eba2c;" <?php } ?>  title="5 stars"></div>
								</div> */ ?>
							</div>
							</div><?php } ?>
						</div>
						<?php $up++; } } else { ?>
							<div class="no_data"><?php echo __('no_data'); ?></div>
						<?php } ?>
					</ul>
				</div>
			</div>
			<!--completed trip start-->
			<div class="completed_trips">
				<h2><?php echo __("completed_trips"); ?></h2>
				<div class="scroll_div">
                                <table>
					<tr>
						<th><?php echo __("sno"); ?></th>
						<th><?php echo __("trip_id"); ?></th>
						<th><?php echo __("model"); ?></th>
						<th><?php echo __("pickup_time"); ?></th>
						<th><?php echo ucwords(__("driver_name")); ?></th>
						<th><?php echo __("distance"); ?></th>
						<th><?php echo __("trip_total_fare"); ?> (<?php echo CURRENCY; ?>)</th>
						<th><?php echo __("payment_type"); ?></th>
						<th><?php echo __("details"); ?></th>
					</tr>
					<?php if(count($completed_trips) > 0 ) {
							$i = 1; foreach($completed_trips as $c) { ?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $c["passengers_log_id"]; ?></td>
						<td><?php echo ucfirst($c["model_name"]); ?></td>
						<td><?php echo $c["pickup_time"]; ?></td>
						<td><?php echo ucfirst($c["name"]); ?></td>
						<td>
							<?php 
								if($c["distance"] != "") { 
									echo $c["distance"]." ".$unit;
								} else {
									echo "0 ".$unit;
								} ?>
						</td>
						<td>
							<?php 
								if($c["used_wallet_amount"] > 0) {
									echo $c["fare"] + $c["used_wallet_amount"];
								} else {
									echo $c["fare"];
								}
								?>
						</td>
						<td>
							<?php
								if($c["payment_type"] == 1) {
									echo __("cash");
								}else if($c["payment_type"] == 2) {
									echo __("credit_card");
								} else if($c["payment_type"] == 3) {
									echo __("uncard");
								} else if($c["payment_type"] == 4) {
									echo __("account");
								} else if($c["payment_type"] == 5) {
									echo __("wallet");
								} else {
									echo "-";
								}
							?>
						</td>
						<td><a class="more_det" href="<?php echo URL_BASE."users/transaction_details/".$c["passengers_log_id"]; ?>"><i class="plus_square"></i></a></td>
					</tr>
					<?php $i++; } } else { ?>
						<tr><td colspan="9"><?php echo __("no_data"); ?></td></tr>
					<?php } ?>
				</table>
                                </div>
			</div>
			<!--cancled trip start-->
			<div class="completed_trips">
				<h2><?php echo __("cancelledtrip_logs"); ?></h2>
				<div class="scroll_div">
                                <table>
					<tr>
						<th><?php echo __("sno"); ?></th>
						<th><?php echo __("trip_id"); ?></th>
						<th><?php echo __("model"); ?></th>
						<th><?php echo __("pickup_time"); ?></th>
						<th><?php echo ucwords(__("driver_name")); ?></th>
						<th><?php echo __("distance"); ?></th>
						<th><?php echo __("cancel_fare"); ?> (<?php echo CURRENCY; ?>)</th>
						<th><?php echo __("payment_type"); ?></th>
						<?php /* <th><?php echo __("details"); ?></th> */ ?>
					</tr>
					<?php if(count($cancelled_trips) > 0 ) {
						$j = 1;foreach($cancelled_trips as $c) { 
					?>
					<tr>
						<td><?php echo $j; ?></td>
						<td><?php echo $c["passengers_log_id"]; ?></td>
						<td><?php echo ucfirst($c["model_name"]); ?></td>
						<td><?php echo ($c["actual_pickup_time"] != "0000-00-00 00:00:00" && $c["actual_pickup_time"]!="") ? $c["actual_pickup_time"] : $c["pickup_time"]; ?></td>
						<td><?php echo ucfirst($c["name"]); ?></td>
						<td>
							<?php 
								if($c["distance"] != "") { 
									echo $c["distance"]." ".$unit;
								} else {
									echo "0 ".$unit;
								} ?>
						</td>
						<td>
							<?php 
								if($c["used_wallet_amount"] > 0) {
									echo $c["fare"] + $c["used_wallet_amount"];
								} else {
									echo ($c["fare"]) ? $c["fare"] : 0;
								}
								?>
						</td>
						<td>
							<?php
								if($c["payment_type"] == 1) {
									echo __("cash");
								} else if($c["payment_type"] == 2) {
									echo __("credit_card");
								} else if($c["payment_type"] == 3) {
									echo __("uncard");
								} else if($c["payment_type"] == 4) {
									echo __("account");
								} else if($c["payment_type"] == 5) {
									echo __("wallet");
								} else {
									echo '-';
								}
							?>
						</td>
						<?php /* <td><a class="more_det" href="<?php echo URL_BASE."users/transaction_details/".$c["passengers_log_id"]; ?>"><i class="fa fa-plus-square-o"></i></a></td> */ ?>
					</tr>
					<?php $j++; } } else { ?>
						<tr><td colspan="9"><?php echo __("no_data"); ?></td></tr>
					<?php } ?>
				</table>
                                </div>
			</div>
		</div>
	</section>
</div>
<div id="fade"></div>
<div id="show_user_message">
    <div class="message_for_user_purpose">
        <h1>Please wait Do not refresh while cancelling the trip</h1>
        <img width="50" height="50" alt="loader" src="<?php echo URL_BASE;?>public/common/images/loading.gif"/>
    </div>
</div>
<div class="signin_form ask_cvv_popup" style="display: none;">
	<h3><?php echo __('cvv'); ?></h3>
	<a href="javascript:;" class="close_butt">&nbsp;</a>
	<div class="cvv_details">
		<ul>
			<li>
				<div class="full_name">
					<input type="text" name="card_cvv" id="card_cvv" class="txtboxToFilter" tabindex="1" maxlength="4" onpaste="return false;" placeholder="<?php echo __('cvv'); ?>*" title="<?php echo __('cvv'); ?>" value=""/>
					<input type="hidden" name="tripId" id="tripId" value="">
					<input type="hidden" name="driverId" id="driverId" value="">
					<em id="card_cvv_error"></em>
				</div>
			</li>
			<li>
				<div class="sub_butt">
					<input type="button" name="trip_cancel" id="trip_cancel_save" tabindex="2" value="<?php echo __('Submit'); ?>" title="<?php echo __('Submit'); ?>"/>
					<input type="button" name="cancel_btn" id="cancel_btn" tabindex="3" value="<?php echo __('Cancel'); ?>" title="<?php echo __('Cancel'); ?>"/>
				</div>
			</li>
		</ul>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.close_butt,#cancel_btn').click(function(){
			$('.ask_cvv_popup, #fade').hide();
			$("#card_cvv_error").html("");
		});
		
		$('.txtboxToFilter').keydown(function (event) {  
			// Allow: backspace, delete, tab, escape, and enter
			if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
				// Allow: Ctrl+A
			(event.keyCode == 65 && event.ctrlKey === true) || 
				// Allow: home, end, left, right
			(event.keyCode >= 35 && event.keyCode <= 39)) {
				// let it happen, don't do anything
				return;
			} else {
				// Ensure that it is a number and stop the keypress
				if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
					event.preventDefault(); 
				}   
			}
		});
		
		$('#trip_cancel_save').click(function(){
			var tripId = $("#tripId").val();
			var card_cvv = $("#card_cvv").val();
			var driver_id = $("#driverId").val();
			$("#card_cvv_error").html("");
			if(card_cvv == '') {
				$("#card_cvv_error").html("CVV should not be empty");
				return false;
			} else if(card_cvv.length < 3){
				$("#card_cvv_error").html("<?php echo __("cvv_length_error"); ?>");
				return false;
			} else {
				passengerCancel(tripId,0,driver_id,card_cvv);
			}
		});
	});
function passengerCancel(tripId,askCVV,driver_id,cvvVal)
{
	var cvvNo;
	if(askCVV == 2) {
		alert("You don't have credit card to cancel the trip");
		return false;
	} else {
		if(driver_id > 0) {
			if(askCVV == 1){
				$("#fade, .ask_cvv_popup").show();
				$("#tripId").val(tripId);
				$("#driverId").val(driver_id);
				return false;
			} 
			var sendInfo = {
			   passenger_log_id: tripId,
			   travel_status : "4",
			   remarks : "",
			   pay_mod_id : "2",
			   creditcard_cvv : cvvVal,
			};
	   } else {
		   var sendInfo = {
			   passenger_log_id: tripId,
			   travel_status : "4",
			   remarks : "",
			   pay_mod_id : "2",
			   driver_id : driver_id,
			   creditcard_cvv : "",
			};
	   }

       $.ajax({
           type: "POST",
           //url: "<?php echo URL_BASE;?>mobileapi117/index/dGF4aV9hbGw/?type=cancel_trip",
           url: "<?php echo URL_BASE;?>users/cancel_trip",
           
           dataType: "json",
           beforeSend:function() {
				$('#show_user_message').show();
				$('.ask_cvv_popup').hide();
			},
           success: function (msg) {
               if (msg) {
				   $('.ask_cvv_popup, #fade').hide();
				   $('#show_user_message').hide();
				   alert(msg.message);
				  // if(msg.status == 1 || msg.status == 2) {
					   location.reload();
				  // }
               } else {
                   alert("You cannot cancel the trip !");
               }
           },

           data: sendInfo
       });
	}
}
</script>
<?php /*
$distance = 0;
$from = urlencode("Vadavalli, Coimbatore, Tamil Nadu, India");
$to = urlencode("Gandhipuram, Coimbatore, Tamil Nadu, India");
$data = @file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=$from&destinations=$to&language=en-EN");
$data = json_decode($data);
print_r($data);

if(isset($data->status) && $data->status == "OK") {
	foreach($data->rows[0]->elements as $road) {
		$distance += $road->distance->text;
		$a = $road->duration->text;
	}
	  $a = str_replace(" mins", "", $a);
	echo $a;
} */
?>
<?php /* <script>
var geocodingAPI = "https://maps.googleapis.com/maps/api/geocode/json?latlng=11.020983,76.96633440000005&key=AIzaSyByFlLjgMjTCIoGEy_Et02p5aMV_XGl5jM";

 $.getJSON(geocodingAPI, function (json) {
	 
     if (json.status == "OK") {
         //Check result 0
         var result = json.results[0];
         console.log(result);
         //look for locality tag and administrative_area_level_1
         var city = "";
         var state = "";
         for (var i = 0, len = result.address_components.length; i < len; i++) {
             var ac = result.address_components[i];
             
            if (ac.types.indexOf("administrative_area_level_1") >= 0) state = ac.short_name;
            if (ac.types.indexOf("administrative_area_level_2") >= 0) city = ac.short_name;
         }
         if (state != '') {
             console.log("Hello to you out there in " + city + ", " + state + "!");
         }
     }

 });
</script>
*/ ?>
