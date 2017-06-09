<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">  
			<div class="widget">
			<?php /*	<div class="title"><h6><?php echo __('sms_template'); ?></h6>
				<div style="width:auto; float:right;">
					<div class="button greyishB"> <?php //echo $export_excel_button; ?></div>                       
				</div>
			</div> */?>
			<div class= "overflow-block">
				<form method="get" class="form" name="manage_sms_templet" id="manage_sms_templet" action="">
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						<thead>
							<tr>
								<td align="center" ><?php echo __('status_label'); ?></td>
								<td align="center" ><?php echo __('sno_label'); ?></td>
								<td align="left" ><?php echo __('SMS_TEMPLETE_TITLE'); ?></td>
								<td align="left" ><?php echo __('sms_template_status'); ?></td>
							</tr>
						</thead>
						<tbody>
							<?php $i=1; foreach($sms_template as $result) { $trcolor=($i%2==0) ? 'oddtr' : 'eventr'; ?>
							<tr class="<?php echo $trcolor; ?>">
								<td align="center">
									<input type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $result['sms_id'];?>" value="<?php echo $result['sms_id'];?>" />
								</td>
								<td align="center"><?php echo $i; ?></td>
								<td align="left"><a href="<?php echo URL_BASE.'edit/sms_templates/'.$result['sms_id'].'/'; ?> "><?php echo $result['sms_info']; ?></a></td>
								<td align="left"><?php echo ($result["status"] == 0) ? __("enable") : __("disable"); ?></td>
							</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
					<input type="hidden" name="status" value=""/>
				</form>
			</div>
		</div>
	</div>
</div>
</div>
<!--** Multiple select starts Here ** -->
<?php if(count($sms_template) > 0) { ?>
<div class="bottom_contenttot">
<div class="select_all manage_fag">
<ul>
<li>
	<b><a id="select-all"><?php echo __('all_label');?></a></b></li><li><span class="pr5 pl5">|</span></li><li><b><a id="select-none"><?php echo __('select_none');?></a></b></li>
	</ul>
	 <span class="more_selection">
		<select name="more_action" id="more_action">
			<option value=""><?php echo __('Change Status'); ?></option>
			<option value="status-enable"><?php echo __('enable'); ?></option>
			<option value="status-disable"><?php echo __('disable'); ?></option>
		</select>
	 </span>
</div>
</div>
<?php } ?>
<!--** Multiple select ends Here ** -->

<script language="javascript" type="text/javascript">
$(document).ready(function() {

	$('#select-all').click(function(event) {   
		// Iterate each checkbox
		$(':checkbox').each(function() {
			this.checked = true;                        
		});
	});

	$('#select-none').click(function(event) {   
		// Iterate each checkbox
		$(':checkbox').each(function() {
			this.checked = false;                        
		});
	});
	
	//for More action Drop Down
	//=========================
	$('#more_action').change(function() {
		var selected_val= $('#more_action').val();
		switch (selected_val) {
			case "status-enable":
				var confirm_msg =  "<?php echo __('Are you sure want to enable SMS Template(s)?');?>";
				if($('input[type="checkbox"]').is(':checked'))
				{
					var ans = confirm(confirm_msg)
					if(ans) {
						$("input[name=status]").val(0);
						document.manage_sms_templet.action="<?php echo URL_BASE;?>admin/enable_template";
						document.manage_sms_templet.submit();
					} else {
						$('#more_action').val('');
					}
				} else {
					alert("<?php echo __('Please select atleast one or more Record(s) to do this action');?>")
					$('#more_action').val('');
				}
			break;

			case "status-disable":
				var confirm_msg =  "<?php echo __('Are you sure want to disable SMS Template(s)?');?>";
				if($('input[type="checkbox"]').is(':checked'))
				{
					var ans = confirm(confirm_msg)
					if(ans) {
						$("input[name=status]").val(1);
						document.manage_sms_templet.action="<?php echo URL_BASE;?>admin/enable_template";
						document.manage_sms_templet.submit();
					} else {
						$('#more_action').val('');
					}
				} else {
					alert("<?php echo __('Please select atleast one or more Record(s) to do this action');?>")	
					$('#more_action').val('');
				}
			break;
		}
		return false;  
	});
});
</script>
