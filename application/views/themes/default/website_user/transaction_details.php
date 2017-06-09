<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<link href="<?php echo URL_BASE; ?>public/frontend/logged_in/css/jRating.jquery.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/frontend/logged_in/js/jRating.jquery.js"></script>
<div class="dash_details">
	<?php echo View::factory(USERVIEW.'website_user/left_menu'); ?>
	<section id="right_side_part">
		<div class="top_part">
			<div class="bread_com">
				<ul>
					<li><a href="<?php echo URL_BASE; ?>" title="<?php echo __("home_breadcrumb"); ?>"><?php echo __("home_breadcrumb"); ?></a><i class="fa fa-angle-double-right"></i></li>
					<li><p><?php echo __('trip_details'); ?></p></li>
				</ul>
			</div>
			<?php echo View::factory(USERVIEW.'website_user/upcoming_trip_alert'); ?>
		</div>
		<?php 
			$rating = 0;
			$siteusers = Model::factory('siteusers');
			if(count($transaction_details) > 0) {
				foreach($transaction_details as $d) {
		?>
		<?php
			$pickup_time = $trip_date = "";
			$pickup_time = $d['actual_pickup_time'];
			$drop_time = isset($d['drop_time'])?$d['drop_time']:'';
			if($pickup_time != "0000-00-00 00:00:00") {
				$pickup_time = $pickup_time;
				$datetime = explode(" ",$pickup_time);
				$trip_date = (isset($datetime[0])) ? $datetime[0] : $pickup_time;
			}
			
			
		?>
		<div class="white_bg">
			<div class="trip_detail">
				<h1><?php echo __("trip_on")." ".$trip_date; ?></h1>
				<div class="trip_det_lft">
					<div class="det_lft_top">
						<div class="trip_det_lst">
							<?php
								$drop_location = ""; $pickup_time = "";
								$current_location = isset($d["current_location"]) ? $d["current_location"] : "";
								$drop_location = isset($d["drop_location"]) ? $d["drop_location"] : "";
								$drop_latitude = (isset($d["drop_latitude"])) ? $d["drop_latitude"] : "";
								$drop_longitude = (isset($d["drop_longitude"])) ? $d["drop_longitude"] : "";
								$drop_location = ($drop_location != "") ? $drop_location : $manage_transaction->getaddress($drop_latitude,$drop_longitude);
								$pickup_time = $d['actual_pickup_time'];
								if($pickup_time != "0000-00-00 00:00:00") {
									$pickup_time = $pickup_time;
								}
							?>
							<?php if($current_location != "") { ?>
								<div class="passenger_current_location">
									<p><?php echo $current_location." ".$pickup_time; ?></p>
								</div>
							<?php } ?>
							<?php if($drop_location != "") { ?>
								<div class="passenger_drop_location">
									<p><?php echo $drop_location." ".$drop_time; ?></p>
								</div>
							<?php } ?>
						</div>
						<a href="<?php echo URL_BASE."users/download_transaction_detail/".$d["passengers_log_id"]; ?>" title="<?php echo __("download_fare_detail"); ?>" class="download_trp_det">&nbsp;</a>
					</div>
					<div class="det_lft_bot">
						<div class="taxi_icons">
							<a href="javascript:;">
								<?php if(file_exists(DOCROOT.MODEL_IMGPATH."thumb_act_".$d['model_id'].".png")) { ?>
									<i><img src="<?php echo URL_BASE.MODEL_IMGPATH; ?>thumb_act_<?php echo $d['model_id'] ?>.png"></i>
								<?php } else { ?>
									<i class="icon_taxi"></i>
								<?php } ?>
								<span><?php echo $d["model_name"]; ?></span>
							</a>
						</div>
						<ul>
							<li>
								<p><?php echo __("car_type"); ?></p>
								<span><?php echo $d["model_name"]; ?></span>
							</li>
							<li>
								<p><?php echo __("distance"); ?></p>
								<span>
									<?php 
										if($d['distance'] == 0) { 
											echo '0';
										} else { 
											echo $d['distance'].' '.$d['distance_unit'];
										} 
									?>
								</span>
							</li>
							<li>
								<p><?php echo __("duration"); ?></p>
								<span>
									<?php
										$drop_time = "";
										$drop_time = $d['drop_time'];
										if($drop_time != "0000-00-00 00:00:00") {
											$drop_time = $drop_time;
										}

										if($pickup_time != "" && $drop_time != "") {
											echo $manage_transaction->dateDiff($drop_time,$pickup_time)."<br>";
										} else {
											echo "0";
										}
									?>
								</span>
							</li>
						</ul>
					</div>
				</div>
				<div class="trip_det_rgt"><img alt="map" src="<?php echo $mapurl; ?>"/></div>
			</div>
			<div class="fare_details">
				<div class="fare_det_lft">
					<h2><?php echo __("fare_details"); ?></h2>
					<ul>
						<li>
							<label><?php echo __("base_fare"); ?></label>
							<p>
								<?php 
									echo CURRENCY.round(($d['tripfare']-$d['minutes_fare']),2);
								?>
							</p>
						</li>
						<li>
							<label><?php echo __("waiting_fare"); ?></label>
							<p>
								<?php 
									echo CURRENCY.round($d['taxi_waiting_cost'],2);
								?>
							</p>
						</li>
						<li>
							<label><?php echo __("minutes_fare"); ?></label>
							<p>
								<?php 
									echo CURRENCY.round($d['minutes_fare'],2);
								?>
							</p>
						</li>
						<?php if($d['nightfare_applicable'] == 1) {  ?>
						<li>
							<label><?php echo __("nightfare"); ?></label>
							<p>
								<?php 
									echo CURRENCY.round($d['nightfare'],2);
								?>
							</p>
						</li>
						<?php } ?>
						<?php if($d['eveningfare_applicable'] == 1) { ?>
						<li>
							<label><?php echo __("eveningfare"); ?></label>
							<p>
								<?php 
									echo CURRENCY.round($d['eveningfare'],2);
								?>
							</p>
						</li>
						<?php } ?>
						<li>
							<label><?php echo ucwords(__("sub_total")); ?></label>
							<?php $subtot = $d['tripfare']+$d['taxi_waiting_cost']+$d['nightfare']+$d['eveningfare']; $promotion = 0; ?>
							<p>
								<?php 
									$subtotAmt = $subtot - $promotion;
									echo CURRENCY.round($subtotAmt,2);
								?>
							</p>
						</li>
						<?php if($d['passenger_discount'] != 0) { ?>
						<li>
							<label><?php echo __("promo_discount").' ('.$d['passenger_discount'].'%)'; ?></label>
							<p>
								<?php 
									echo CURRENCY.round($d['discount_fare'],2);
								?>
							</p>
						</li>
						<?php } ?>
						<li>
							<label><?php echo __("tax"); ?></label>
							<p>
								<?php echo CURRENCY.round($d['company_tax'],2); ?>
							</p>
						</li>
						<li>
							<label><?php echo __("wallet_amount"); ?></label>
							<p>
								<?php 
									echo CURRENCY.$d['used_wallet_amount'];
								?>
							</p>
						</li>						
						<li>
							<label><?php echo __("payment_type"); ?></label>
							<p>
								<?php
									switch($d["payment_type"])
									{
										case 1:
											echo __("cash");
										break;
										case 2:
											echo __("credit_card");
										break;
										case 3:
											echo __("new_card");
										break;
										case 5:
											echo __("wallet");
										break;
										default:
											echo __("uncard");
										break;
									}
								?>
							</p>
						</li>
						<li class="tot_amt">
							<label><?php echo __("total_amount"); ?></label>
							<p>
								<?php
									$subtotal = round($d['fare']+$d['used_wallet_amount'],3);
									echo CURRENCY.round($subtotal,2);
								?>
							</p>
						</li>
					</ul>
				</div>
				<div class="fare_det_rgt">
					<div class="driver_img">
						<?php
							if(file_exists(DOCROOT.SITE_DRIVER_IMGPATH.$d['profile_picture']) && ($d['profile_picture'] != "")) {
								$profile_image = URL_BASE.SITE_DRIVER_IMGPATH.$d['profile_picture'];
							} else {
								$profile_image = URL_BASE."public/frontend/logged_in/images/profile_noimage.png";
							}
						?>
						<img src="<?php echo $profile_image; ?>"/>
					</div>
					<?php
						$rating = $siteusers->get_driver_rating($d["driver_id"]);
					?>
					<div class="triver_det">
						<h3><?php echo ucfirst($d["driver_name"]); ?></h3>
						<p><?php echo __("taxi_no"); ?>: <?php echo $d["taxi_no"]; ?></p>
						<script type="text/javascript">
							$(document).ready(function(){
								$(".basic14").jRating({
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
						<label class="basic14 jDisabled" id="<?php echo $rating; ?>"></label>					
					
					<form method="post" name="book_again_form">
						<?php $session = Session::instance(); ?>
						<input type="hidden" name="name" value="<?php echo $session->get("passenger_name"); ?>"/>
						<input type="hidden" name="country_code" value="<?php echo $session->get("passenger_phone_code"); ?>"/>
						<input type="hidden" name="mobile_number" value="<?php echo $session->get("passenger_phone"); ?>"/>
						<input type="hidden" name="email" value="<?php echo $session->get("passenger_email"); ?>"/>
						<input type="hidden" name="pickup_location" value="<?php echo $d["current_location"]; ?>"/>
						<input type="hidden" name="drop_location" value="<?php echo $d['drop_location']; ?>"/>
						<input type="hidden" name="landmark" value=""/>
						<input type="hidden" name="pickup_time" value=""/>
						<input type="hidden" name="promocode" value=""/>
						<input type="hidden" name="model" value="<?php echo $d['model_id']; ?>"/>
						<input type="hidden" name="passengerId" value="<?php echo $session->get("id");; ?>"/>
						<input type="hidden" name="pass_latitude" value="<?php echo $d['drop_latitude']; ?>"/>
						<input type="hidden" name="pass_longitude" value="<?php echo $d['drop_longitude']; ?>"/>
						<input type="hidden" name="pickup_latitude" value="<?php echo $d['pickup_latitude']; ?>"/>
						<input type="hidden" name="pickup_longitude" value="<?php echo $d['pickup_longitude']; ?>"/>
						<input type="hidden" name="drop_latitude" value="<?php echo $d['drop_latitude']; ?>"/>
						<input type="hidden" name="drop_longitude" value="<?php echo $d['drop_longitude']; ?>"/>
						<input type="button" onclick="return book_agains();" value="<?php echo __("book_again"); ?>" name="book_again" class="add_card"/>
					</form>
                                                </div>
				</div>
			</div>
		</div>
		<?php } } ?>
	</section>
</div>
<script>
function book_agains()
{
	var form = $("form[name='book_again_form']").serialize();
	localStorage.setItem("book_again_form", form);
	window.location = "<?php echo URL_BASE; ?>booking.html#book-again";
}
</script>

