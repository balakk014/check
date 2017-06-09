<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div class="dash_details">
	<?php echo View::factory(USERVIEW.'website_user/left_menu'); ?>
	<section id="right_side_part">
		<div class="top_part">
			<div class="bread_com">
				<ul>
					<li><a href="<?php echo URL_BASE; ?>" title="<?php echo __("home_breadcrumb"); ?>"><?php echo __("home_breadcrumb"); ?></a><i class="fa fa-angle-double-right"></i></li>
					<li><p><?php echo __('cancelledtrip_logs'); ?></p></li>
				</ul>
			</div>
			<?php echo View::factory(USERVIEW.'website_user/upcoming_trip_alert'); ?>
		</div>
		<div class="white_bg">
			<div class="completed_trips cancelled_trip">
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
						$sno = $Offset;
						foreach($cancelled_trips as $c) { 
						$sno++;
					?>
					<tr>
						<td><?php echo $sno; ?></td>
						<td><?php echo $c["passengers_log_id"]; ?></td>
						<td><?php echo ucfirst($c["model_name"]); ?></td>
						<td><?php echo ($c["actual_pickup_time"] != "0000-00-00 00:00:00") ? $c["actual_pickup_time"] : $c["pickup_time"]; ?></td>
						<td><?php echo ucfirst($c["name"]); ?></td>
						<td><?php echo ($c["distance"] != "") ? number_format($c["distance"],2,'.','') : 0; ?></td>
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
								if($c["payment_type"] == 2) {
									echo __("credit_card");
								} else if($c["payment_type"] == 3) {
									echo __("uncard");
								} else if($c["payment_type"] == 4) {
									echo __("account");
								} else if($c["payment_type"] == 5) {
									echo __("wallet");
								} else {
									echo __("cash");
								}
							?>
						</td>
						<?php /* <td><a class="more_det" href="<?php echo URL_BASE."users/transaction_details/".$c["passengers_log_id"]; ?>"><i class="fa fa-plus-square-o"></i></a></td> */ ?>
					</tr>
					<?php } } else { ?>
						<tr><td colspan="9"><?php echo __("no_data"); ?></td></tr>
					<?php } ?>
				</table>
                                </div>
			</div>
                    <?php if(count($cancelled_trips) > 0): ?>
                        <div class="pagination">
			    <?php echo $pag_data->render(); ?>
        		</div>
                    <?php endif; ?> 
		</div>
		
	</section>
</div>
