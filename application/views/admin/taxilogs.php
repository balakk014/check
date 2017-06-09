<?php defined('SYSPATH') OR die("No direct access allowed."); ?>

<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/transaction.js"></script>

<?php
$startdate = isset($srch["startdate"]) ? $srch["startdate"] :date('Y-m-d 00:00:00'); 	
$enddate = isset($srch["enddate"]) ? $srch["enddate"] :date('Y-m-d H:i:s'); 	
?>
<?php /*
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle">
			<form method="get" class="form" name="driverlogs" id="driverlogs" action="manage/driverlogssearch">
				<table class="list_table1" border="0" width="65%" cellpadding="5" cellspacing="0">
					<tr>
					<td valign="middle"><label><?php echo __('from_date'); ?></label></td>
					<td valign="top">
					<div class="new_input_field_transaction">
					<input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="startdate" name="startdate" value="<?php echo $startdate;?>"  />
					<span id="startdate_error" class="error"></span>		 
					</div>

					</td>       

					<td valign="middle"><label><?php echo __('end_date'); ?></label></td>
					<td valign="top">
					<div class="new_input_field_transaction">
					<input type="text"  readonly title="<?php echo __('select_datetime'); ?>" id="enddate" name="enddate" value="<?php echo $enddate;?>"  />
					<span id="enddate_error" class="error"></span>								

					</div>
					</td>     
					</tr>
					<tr>
						<td valign="top"><label>&nbsp;</label></td>
						<td>                            
						    <div class="button brownB">
							<input type="submit" value="<?php echo __('button_search'); ?>" name="search_user" title="<?php echo __('button_search'); ?>" />
						    </div>
						    <div class="button blueB">
							<input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo URL_BASE; ?>manage/country'" />
						    </div>
						</td>
					</tr>
				</table>
				</form>
                

*/ ?>

<!-- Service Time  Journey -->
<div class="widget margin-bottom" >
	<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" />
	<h6><?php echo __('service_time'); ?></h6>
		<div style="width:auto; float:right; margin: 4px 3px;">		
		<div class="button greyish"></div>
		</div>
	</div>
	<div>
		
			<?php if(count($taxi_logs_service)>0) { ?>
			<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
				
				<thead>
				<tr>
					<td>#</td>
					<td><?php echo __('driver_name'); ?></td>
					<td><b><?php echo __('interval_type'); ?></b></td>
					<td><b><?php echo __('interval_startdate');?></b></td>
					<td><b><?php echo __('interval_enddate');?></b></td>
					<td><b><?php echo __('interval_time');?></b></td>
					<td><b><?php echo __('Reason');?></b></td>
					</tr>
				</tr>
				</thead>					
				
				<?php 
				$i=1;
				($i%2 == 1)?$class="eventr":$class="oddtr";
					foreach($taxi_logs_service as $values)
					{ ?>
						<tr class="<?php echo $class; ?>">	
						<td><?php echo $i;?></td>
						<td><?php echo ucfirst($values->name); ?></td>
						<td><?php 
						if($values->interval_type =='B') 
						{ echo __('break'); }
						else if($values->interval_type =='S') 
						{ echo __('service'); } ?></td>
						<td><?php echo $values->interval_start;?></td>
						<td><?php echo $values->interval_end;?></td>
						<td>
						<?php $to_time = strtotime($values->interval_start);
							$from_time = strtotime($values->interval_end);
							
						if($from_time > $to_time)
						{
						$seconds = $from_time - $to_time;
						echo $days    = floor($seconds / 86400). " Day ";
						echo $hours   = floor(($seconds - ($days * 86400)) / 3600)." Hr ";
						echo $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60)." Min ";
						echo $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)))." Sec ";
						}
						 ?>	
						</td>
						<td><?php echo $values->reason;?></td>
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
		<?php if(count($taxi_logs_service)>0): ?>
		 <p><?php echo $pag_data->render(); ?></p>  
		<?php endif; ?> 
  </div>
  <div class="clr">&nbsp;</div>

<script>
$(document).ready(function(){
toggle(6);

/*	$("#startdate").datetimepicker( {
	showTimepicker:true,
	showSecond: true,
	timeFormat: 'hh:mm:ss',
	dateFormat: 'yy-mm-dd',
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(),
	stepSecond: 1
	});

	$("#enddate").datetimepicker( {
	showTimepicker:true,
	showSecond: true,
	timeFormat: 'hh:mm:ss',
	dateFormat: 'yy-mm-dd',
	stepHour: 1,
	stepMinute: 1,
	maxDateTime : new Date(),
	stepSecond: 1
	});
*/

} );

</script>

  
