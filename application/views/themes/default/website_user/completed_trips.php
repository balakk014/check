<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div class="dash_details">
	<?php echo View::factory(USERVIEW.'website_user/left_menu'); ?>
	<section id="right_side_part">
		<div class="top_part">
			<div class="bread_com">
				<ul>
					<li><a href="<?php echo URL_BASE; ?>" title="<?php echo __("home_breadcrumb"); ?>"><?php echo __("home_breadcrumb"); ?></a><i class="fa fa-angle-double-right"></i></li>
					<li><p><?php echo __("completed_trip"); ?></p></li>
				</ul>
			</div>
			<?php echo View::factory(USERVIEW.'website_user/upcoming_trip_alert'); ?>
		</div>
		<div class="white_bg">
			<div class="completed_trips cancelled_trip">
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
						$sno = $Offset;
						foreach($completed_trips as $c) { 
						$sno++;
					?>
					<tr>
						<td><?php echo $sno; ?></td>
						<td><?php echo $c["passengers_log_id"]; ?></td>
						<td><?php echo ucfirst($c["model_name"]); ?></td>
                                                <td><?php echo Commonfunction::getDateTimeFormat($c["pickup_time"],1); ?></td>
						<td><?php echo ucfirst($c["name"]); ?></td>
						<td><?php echo number_format($c["distance"],2,'.',''); ?></td>
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
						<td><a class="more_det" href="<?php echo URL_BASE."users/transaction_details/".$c["passengers_log_id"]; ?>"><i class="fa fa-plus-square-o"></i></a></td>
					</tr>
					<?php } } else { ?>
						<tr><td colspan="9"><?php echo __("no_data"); ?></td></tr>
					<?php } ?>
				</table>
                                </div>
			</div>
                        <?php if(count($completed_trips) > 0): ?>
                        <div class="pagination">
				<?php echo $pag_data->render(); ?>
        		</div>
                        <?php endif; ?> 
		</div>
	</section>
</div>
