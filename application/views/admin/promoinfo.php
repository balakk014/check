<?php defined('SYSPATH') OR die("No direct access allowed.");?>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle">
			<div class="driverinfo_common">
					<h2 class="tab_sub_tit"><?php echo ucfirst(__('promoinform_label')); ?></h2>
                                        <ul>	
                                            <li>
                                                <label><?php echo ucfirst(__('promocode')); ?></label>
                                                <p><?php if (isset($promo_data[0]['promocode'])) {
    echo $promo_data[0]['promocode'];
} else {
    echo '';
} ?></p>
                                            </li>
                                            <li><label><?php echo __('promocode_discount'); ?></label>
                                                <p><?php if (isset($promo_data[0]['promo_discount'])) {
    echo $promo_data[0]['promo_discount'];
} ?></p>
                                            </li>
                                            <li><label><?php echo __('start_date'); ?></label>
                                                <p><?php if (isset($promo_data[0]['start_date'])) {
    echo Commonfunction::getDateTimeFormat($promo_data[0]['start_date'], 1);
} ?></p>
                                            </li>
                                            <li><label><?php echo __('expiry_date'); ?></label>
                                                <p><?php if (isset($promo_data[0]['expire_date'])) {
    echo Commonfunction::getDateTimeFormat($promo_data[0]['expire_date'], 1);
} ?></p>
                                            </li>
                                            <li><label><?php echo __('promo_limit'); ?></label>
                                                <p><?php if (isset($promo_data[0]['promo_limit'])) {
    echo $promo_data[0]['promo_limit'];
} else {
    echo '-';
} ?></p>
                                            </li>
                                            <li><label><?php echo __('passengers_count'); ?></label>
                                                <?php echo count($promo_data[0]['passenger_id']); ?>
                                            </li>
				<?php
					$commonmodel = Model::factory('commonmodel');
					$used_count = $unused_count = 0; $used_array = $unused_array = array();
					$unused_count = $promo_data[0]['promo_limit'];
					if(isset($promo_data[0]['promo_used_details']) && $promo_data[0]['promo_used_details'] != "") {
						$data = unserialize($promo_data[0]['promo_used_details']);
						foreach($data as $k => $c) {
							if($c > 0) {
								$name = $commonmodel->get_passenger_name($k);
								$used_array[] = array("id" => $k,"name" => $name,"count" => $c);
								$used_count = $used_count+$c;
								$unused_count = $unused_count - $c;
							} else {
								$name = $commonmodel->get_passenger_name($k);
								$unused_array[] = array("id" => $k,"name" => $name,"count" => $c);
								//$unused_count++;
							}
						}
					}
				?>
				<li><label><?php echo __('user_promo_used_count'); ?></label>
                                    <p><?php echo $used_count; ?></p>
						</li>
				<li><label><?php echo __('user_promo_unused_count'); ?></label>
                                    <p><?php echo $unused_count; ?></p>
						</li>
			</ul>
                        </div>
                    <div class="over_all">
			<div class="widget" >
				<div class="title"><h6><?php echo __('promocode_used_list'); ?></h6>
					<div style="width:auto; float:right; margin: 0px;">
						<div class="button greyish"></div>
					</div>
				</div>
				<div>
					<?php if(count($used_array) > 0) { ?>
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						<thead>
							<tr>
								<td align="center">#</td>
								<td><?php echo __('passenger_name'); ?></td>
								<td><?php echo __('promocode_used_count');?></td>
							</tr>
						</thead>
						<?php $i=1; $class = ($i%2 == 1) ? "eventr" : "oddtr";
							foreach($used_array as $us) { ?>
								<tr class="<?php echo $class; ?>">
									<td align="center"><?php echo $i;?></td>
									<td><a href="<?php echo URL_BASE."manage/passengerinfo/".$us["id"]; ?>"><?php echo ucfirst($us["name"]); ?></a></td>
									<td align="left"><?php echo $us["count"]; ?></td>
								</tr>
						<?php $i++; } ?>
						<?php } else { echo "<div class='no_data'>".__('no_data')."</div>"; } ?>
					</table>
				</div>
			</div>
                    </div>
             <div class="over_all">        
			<div class="widget mb10">
				<div class="title"><h6><?php echo __('promocode_unused_list'); ?></h6>
					<div style="width:auto; float:right; margin: 0px;">
						<div class="button greyish"></div>
					</div>
				</div>
				<div>
					<?php if(count($unused_array) > 0) { ?>
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						<thead>
							<tr>
								<td align="center">#</td>
								<td><?php echo __('passenger_name'); ?></td>
								<td><?php echo __('promocode_used_count');?></td>
							</tr>
						</thead>
						<?php $i=1; $class = ($i%2 == 1) ? "eventr" : "oddtr";
							foreach($unused_array as $values) { ?>
								<tr class="<?php echo $class; ?>">
									<td align="center"><?php echo $i;?></td>
									<td><a href="<?php echo URL_BASE."manage/passengerinfo/".$values["id"]; ?>"><?php echo ucfirst($values["name"]); ?></a></td>
									<td align="left"><?php echo $values["count"]; ?></td>
								</tr>
						<?php $i++; } ?>
						<?php } else { echo "<div class='no_data'>".__('no_data')."</div>"; } ?>
					</table>
				</div>
			</div>
			</div>
		</div>
	</div>
</div> 
