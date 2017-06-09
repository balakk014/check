<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle">
			<div class="widget">
				<div class="title"><h6><?php echo $page_title; ?></h6>
				<div style="width:auto; float:right;"></div>
			</div>
			<form method="get" name="manage_payment" action="withdraw_payment_mode">
				<div class= "overflow-block">
					<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
						<thead>
							<tr>
								<td align="center" width="8%"><?php echo __('Select'); ?></td>
								<td align="center" width="8%"><?php echo __('sno_label'); ?></td>
								<td align="left" style="text-align:left;" width="15%"><?php echo __('payment').' '.__('name_label'); ?></td>
								<td align="center" width="8%"><?php echo __('status_label'); ?></td>
							</tr>
						</thead>
						<tbody>
							<?php
							if(count($withdraw_payment_mode) > 0) {
								$sno = $Offset;
								foreach($withdraw_payment_mode as $listings) {
								$sno++;
								$trcolor = ($sno%2==0) ? 'oddtr' : 'eventr';  
							?>
							<tr class="<?php echo $trcolor; ?>">
								<td align="center">
									<input type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $listings['withdraw_payment_mode_id'];?>" value="<?php echo $listings['withdraw_payment_mode_id'];?>" />
								</td>
								<td align="center"><?php echo $sno; ?></td>
								<td align="left"><?php echo $listings['payment_mode_name']; ?></td>
								<td align="center"> 
									<?php 
										if($listings['payment_mode_status'] == 'D') {
											$txt = "unsuspendicon"; $class ="blockicon";
										} else { 
											$txt = "Active"; $class ="unsuspendicon"; 
										}
										echo '<a  title ='.$txt.' class='.$class.'></a>' ;  
									?>
								</td>
							</tr>
							<?php } } else { ?>
							<tr>
								<td colspan="7" align="center"><?php echo __('no_data');?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
	<div class="bottom_contenttot">
	<!--** Multiple select starts Here ** -->
	<?php if(count($withdraw_payment_mode) > 0) { ?>
	 <div class="select_all manage_fag">
		<ul><li><b><a id="select-all"><?php echo __('all_label');?></a></b></li><li><span class="pr5 pl5">|</span></li><li><b><a id="select-none"><?php echo __('select_none');?></a></b></li></ul>
		 <span class="more_selection">
			<select name="more_action" id="more_action">
				<option value=""><?php echo __('Change Status'); ?></option>
				<option value="block_country_request" ><?php echo __('Block'); ?></option>
				<option value="active_country_request" ><?php echo __('Active'); ?></option>
			</select>
		</span>
	</div>
	<?php } ?>
	
	<!--** Multiple select ends Here ** -->
</div>
</div>
</div>
<script type="text/javascript">

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

	function selectToggle(toggle, form) {
		var myForm = document.forms[form];
		for( var i=0; i < myForm.length; i++ ) { 
			if(toggle) {
				myForm.elements[i].checked = "checked";
			} else { 
				myForm.elements[i].checked = ""; 
			}
		}
	}

	//for More action Drop Down
	//=========================
	$('#more_action').change(function() {
		//select drop down option value
		//======================================
		var selected_val= $('#more_action').val();
		//perform more action reject withdraw
		//===================================
		switch (selected_val) {
			//	Current Action "reject"//block 
			//===================================
			case "block_country_request":
			var confirm_msg =  "<?php echo __('Are you sure want to block Request(s)?');?>";

			//Find checkbox whether selected or not and do more action
			//============================================================
			if($('input[type="checkbox"]').is(':checked')) {
				var ans = confirm(confirm_msg)
				if(ans) {
					document.manage_payment.action="<?php echo URL_BASE;?>manage/block_unblock__withdraw_payment/D";
					document.manage_payment.submit();
				} else {
					$('#more_action').val('');
				}
			} else {
				//alert for no record select
				//=============================
				alert("<?php echo __('Please select atleast one or more Record(s) to do this action');?>")	
				$('#more_action').val('');
			}
			break;

			//	Current Action "approve"
			//=========================

			case "active_country_request":
				var confirm_msg =  "<?php echo __('Are you sure want to Activate Request(s)?');?>";
				//Find checkbox whether selected or not and do more action
				//============================================================
				if($('input[type="checkbox"]').is(':checked')) {
					var ans = confirm(confirm_msg)
					if(ans) {
						document.manage_payment.action="<?php echo URL_BASE;?>manage/block_unblock__withdraw_payment/A";
						document.manage_payment.submit();
					} else {
						$('#more_action').val('');
					}
				} else {
					//alert for no record select
					//=============================
					alert("<?php echo __('Please select atleast one or more Record(s) to do this action');?>")	
					$('#more_action').val('');
				}
			break;
		}
		return false;  
	});
});
</script>
