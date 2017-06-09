<?php defined('SYSPATH') OR die("No direct access allowed."); 
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :''; 
$total_places=count($popular_places); ?>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle">
			
			<form method="get" class="form" action="">
				<table class="list_table1" border="0" width="65%" cellpadding="5" cellspacing="0">
					<tr>
						<td valign="top"><label><?php echo __('keyword_label'); ?></label></td>
						<td >
						    <div class="ser_input_field">
							<input type="text" name="keyword"  maxlength="55" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
						    </div>
						    <span class="search_info_label"><?php echo __('search_by_popularname'); ?></span>
						</td>
					</tr>
					<tr>
						<td valign="top"><label>&nbsp;</label></td>
						<td>                            
						    <div class="new_button">
							<input type="submit" value="<?php echo __('button_search'); ?>" name="search_popular" title="<?php echo __('button_search'); ?>" />
						    </div>
						    <div class="new_button">
							<input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo URL_BASE; ?>manage/popularplace'" />
						    </div>
						</td>
					</tr>
				</table>
				</form>
		  <div class="over_all">  
			<div class="widget">
				<div class="title"><h6><?php echo $page_title; ?></h6>
				<div style="width:auto; float:right; margin: 4px 3px;">
				<div class="button greyishB"> <?php //echo $export_excel_button; ?></div>                       
			</div>
		</div>
<form method="get" name="manage_country" id="manage_country" action="country">
<?php if($total_places > 0){ ?>
<div class= "overflow-block">
<?php } ?>
	<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
	<?php if($total_places > 0){ ?>
	<thead>
		<tr>
		<td align="center" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" style="text-align:left;" width="11%"><?php echo __('city_name'); ?></td>
		<td align="left" style="text-align:left;" width="10%"><?php echo __('places_count'); ?></td>
		<td align="center" width="10%" ><?php echo __('action_label'); ?></td>
		</tr>
		</thead>
		<tbody>
	<?php
		$sno=$Offset;
		foreach($popular_places as $listings) { 
		$sno++;        
		$trcolor=($sno%2==0) ? 'oddtr' : 'eventr';?>     
		<tr class="<?php echo $trcolor; ?>">
			<td align="center"><?php echo $sno; ?></td>
			<td align="left"><?php echo ucfirst($listings['city_name']); ?></td>
			<td align="left"><?php echo $listings['count']; ?></td>
			<td align="center"><?php echo '<a href='.URL_BASE.'edit/popularplace/'.$listings['city_id'].' " title ="Edit" class="editicon"></a>' ; ?></td>			
		</tr>
		<?php }
		}else{ ?>
			<tr>
				<td class="nodata"><?php echo __('no_data'); ?></td>
			</tr>
		<?php } ?>	
		</tbody>
	</table>
	<?php if ($total_places > 0) { ?>
	</div>
	<?php } ?>
	</form>
</div></div></div>
<div class="bottom_contenttot">
<div class="pagination">
		<?php if($total_places > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>
</div>
</div>
</div>
