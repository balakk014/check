<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
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
	
	<div>
		
			<?php if(count($driver_shift_logs)>0) { ?>
			<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
				
				<thead>
						<tr>
							<td>#</td>
							<td><?php echo __('taxi_no'); ?></td>							
							<td><b><?php echo __('shift_startdate');?></b></td>
							<td><b><?php echo __('shift_endtime');?></b></td>
							<td><b><?php echo __('shift_time');?></b></td>
							</tr>
						</tr>
				</thead>					
				
				<?php 
						$i=1; $sno=$Offset;
						($i%2 == 1)?$class="eventr":$class="oddtr";
							foreach($driver_shift_logs as $values)
							{  $sno++; ?>
								<tr class="<?php echo $class; ?>">	
								<td><?php echo $sno;?></td>
								<td><a href="<?php echo URL_BASE.'manage/taxiinfo/'.$values->taxi_id;?>">
								<?php echo ucfirst($values->taxi_no); ?></a></td>

                                                                <td><?php echo (isset($values->shift_start) && $values->shift_start!='' && $values->shift_start!='0000-00-00 00:00:00')?  Commonfunction::getDateTimeFormat($values->shift_start,1):'';?></td>
								<td><?php echo (isset($values->shift_end)&& $values->shift_end!='' && $values->shift_end!='0000-00-00 00:00:00')? Commonfunction::getDateTimeFormat($values->shift_end,1):'';?></td>
								<td>
								<?php $to_time = strtotime($values->shift_start);
									$from_time = strtotime($values->shift_end);
								
									if($from_time > $to_time)
									{
									$seconds = $from_time - $to_time;
									echo $days    = floor($seconds / 86400). " Day ";
									echo $hours   = floor(($seconds - ($days * 86400)) / 3600)." Hr ";
									echo $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60)." Min ";
									echo $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)))." Sec ";		
									}else { echo __('shift_in');}	
								 ?>	
								</td>
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

<div class="pagination">
		<?php if(count($driver_shift_logs)>0): ?>
		<?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>
  <div class="clr">&nbsp;</div>

  
