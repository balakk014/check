<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="widget margin-bottom" >
	<div class="title">
	<h6><?php echo __('shift_history'); ?></h6>
		<div style="width:auto; float:right; margin: 4px 3px;">
		<div class="button greyish"></div>
		</div>
	</div>
	<div>
		
			<?php if(count($driverReferralList)>0) { ?>
			<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
				<thead>
						<tr>
							<td>#</td>
							<td><b><?php echo __('name_label');?></b></td>
							<td><b><?php echo __('status_label');?></b></td>
							<td><b><?php echo __('wallet_amount');?></b></td>
							<td><b><?php echo __('created_date');?></b></td>
						</tr>
				</thead>
				<?php 
						$i=1;
						($i%2 == 1)?$class="eventr":$class="oddtr";
							foreach($driverReferralList as $ref) { ?>
								<tr class="<?php echo $class; ?>">
								<td><?php echo $i; ?></td>
								<td><a href="<?php echo URL_BASE.'manage/driverinfo/'.$ref->registered_driver_id; ?>"><?php echo ucfirst($ref->name); ?></a></td>
								<td><?php echo ($ref->referral_status > 0) ? __('used') : __("not_used"); ?></td>
								<td><?php echo $ref->registered_driver_wallet; ?></td>
								<td><?php echo Commonfunction::getDateTimeFormat($ref->createdate,1); ?></td>
							</tr>
								
								<?php $i++;
							} 
						 ?>
				</table>
			<?php }else {
					echo "<div class='no_data'>".__('no_data')."</div>"; 
					
				}?>					
		
		</div>
</div>

<!-- Service Time Journey -->

<div class="clr">&nbsp;</div>
<div class="pagination">
		<?php if(count($driverReferralList)>0): ?>
		 <p><?php echo $pag_data->render(); ?></p>  
		<?php endif; ?> 
  </div>
  <div class="clr">&nbsp;</div>

<script>
$(document).ready(function(){
toggle(7);
} );

</script>

  
