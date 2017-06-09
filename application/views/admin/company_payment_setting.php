<?php defined('SYSPATH') OR die("No direct access allowed.");  ?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery-1.6.3.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>

<!-- time picker start-->
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<!-- time picker start-->

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
         <form name="editcompany_form" id="editcompany_form" class="form" action="" method="post" enctype="multipart/form-data">
          <table border="0" cellpadding="5" cellspacing="0" width="100%">

<?php if($_SESSION['user_type']=="C"){ ?>
	<!-----Company Payment settings----->
		<?php if(count($get_company_payment_settings)>0){ ?>
			<table cellpadding="5" cellspacing="0" width="100%">
			<tr>
				<td valign="top" width="20%"><label><b><?php echo __('payment_module_settings'); ?><span class="star">*</span></b></label></td>   
			</tr>
			</table>
			<table style="border-top:1px solid #cdcdcd;" class="sTable responsive" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td align="center" valign="top" width="10%" ><label><?php echo __('payment_module_status'); ?></label></td>
					<td align="center" valign="top" width="10%" ><label><?php echo __('payment_module_name'); ?></label></td>
					<td align="center" valign="top" width="10%"><label><?php echo __('default_gateway'); ?></label></td>
					<!--<td align="center" valign="top" width="10%"><label><?php echo __('edit_gateway'); ?></label></td> -->
				</tr>
				
				<?php $i=0;  foreach($get_company_payment_settings as $resultset) { ?>				
				<tr>
					<td align="center"><input class="pay_mod"  type="checkbox" name="paymodstatus[]" value="<?php echo $resultset['compay_payment_id']; ?>" <?php if($resultset['pay_active']==1){echo 'checked="checked"'; }  ?> /></td> 
					<td align="center"><img src="<?php echo URL_BASE;?>public/common/images/<?php echo $resultset['pay_mod_image'] ?>"><div class="new_input_field"><label><?php echo $resultset['pay_mod_name'];?></label></div></td>
					<td align="center"><input type="radio" name="default[]" value="<?php echo $resultset['compay_payment_id'];?>" <?php if($resultset['pay_mod_default']==1){echo 'checked="checked"'; }  ?>  <?php if($i=='0'){ echo 'checked="checked"'; } ?> /></td>
					<input type="hidden" name="payid[]" value="<?php echo $resultset['compay_payment_id'];?>"  /></td>
					<!--<td align="center"><?php //if($resultset['pay_mod_id']== 4 || $resultset['pay_mod_id']== 5){?> 
					<?php //echo '<a href='.URL_BASE.'edit/paymentgateway/'.$resultset['compay_payment_id'].' " title ="Edit" class="editicon"></a>' ; ?>
					<?php //} ?></td> -->
				</tr>
				<?php $i++; } ?>
				
				<tr>
					<td colspan="4"><b><a href="javascript:selectToggle(true, 'editcompany_form');"><?php echo __('all_label');?></a></b><span class="pr2 pl2">|</span><b><a href="javascript:selectToggle(false, 'editcompany_form');"><?php echo __('select_none');?></a></b></td>
				</td>

				<tr>					
					<td colspan="4"  style="padding:0;"><?php if(isset($errors) && array_key_exists('paymodstatus',$errors)){ echo "<span class='error'>".ucfirst($errors['paymodstatus'])."</span>";}?></td>
				</tr>
			</table>
			
		<?php } else { ?>
    
			<table class="sTable responsive" cellpadding="5" cellspacing="0" width="85%">
				<tr>
					<td valign="top" width="20%"><label><b><?php echo __('payment_module_settings'); ?><span class="star">*</span></b></label></td>   
				</tr>
				<tr>
					<td align="center" valign="top" width="10%" ><label><?php echo __('payment_module_status'); ?></label></td><td valign="top" width="10%" ><label><?php echo __('payment_module_name'); ?></label></td><td valigin="top" width="10%"><?php echo __('default_gateway'); ?></td></tr>	

						<?php
						$query2 = "SELECT * FROM ".PAYMENT_MODULES." order by pay_mod_id asc";

						$result = Db::query(Database::SELECT, $query2)
								->execute()
								->as_array();
						//print_r($result);exit;
						$i=0;
						foreach($result as $resultset) { ?>				
						<tr>
						<td align="center"><input class="pay_mod"  type="checkbox" name="paymodstatus[]" value="<?php echo $resultset['pay_mod_id']; ?>" /></td> <?php /*  if(!array_key_exists('paymodstatus',$errors) && isset($post_values['paymodstatus'])){ if($post_values['paymodstatus'][$i]==$resultset['pay_mod_id']){echo 'checked="checked"'; }  } */ ?>
						<td align="center"><img src="<?php echo URL_BASE;?>public/common/images/<?php echo $resultset['pay_mod_image'] ?>"><div class="new_input_field"><label><?php echo $resultset['pay_mod_name'];?></label></div></td>
						<td align="center"><input type="radio" name="default[]" value="<?php echo $resultset['pay_mod_id'];?>"  <?php if($i=='0'){ echo 'checked="checked"'; } ?> /></td>
						<input type="hidden" name="payid_add[]" value="<?php echo $resultset['pay_mod_id'];?>"  />
						<input type="hidden" name="paymodname[]" value="<?php echo $resultset['pay_mod_name'];?>"  />
						<input type="hidden" name="paymodimage[]" value="<?php echo $resultset['pay_mod_image'];?>"  />
						</tr>
						<?php $i++; } ?>
						<tr>
						<td colspan="4">
						<b>
						<a href="javascript:selectToggle(true, 'addcompany_form');"><?php echo __('all_label');?></a>
						</b><span class="pr2 pl2">|</span><b>
						<a href="javascript:selectToggle(false, 'addcompany_form');"><?php echo __('select_none');?></a></b></td>
						</tr>
						<tr>						
						<td colspan="4" style="padding:0;"><?php if(isset($errors) && array_key_exists('paymodstatus',$errors)){ echo "<span class='error'>".ucfirst($errors['paymodstatus'])."</span>";}?></td>
						</tr>
			</table>
		<?php } ?>
    <!-----Company Payment settings----->
    <?php } ?>
<table cellpadding="5" cellspacing="0">
		<tr>	
			<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
		</tr>                         
		<tr>			
			<td colspan="">                          
				<div class="button blackB"><input type="button" value="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
				<div class="button dredB"><input type="button" onclick="selectToggle(false, 'editcompany_form')" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
				<div class="button greenB"><input type="submit" value="<?php echo __('btn_submit' );?>" name="submit_addcompany" title="<?php echo __('btn_submit' );?>" /></div>
				<div class="clr">&nbsp;</div>
			</td>
		</tr> 
</table>
		</table>

        </form>
        </div>
	</div>
	</div>
<script type="text/javascript">
function selectToggle(toggle, form) {
	var myForm = document.forms[form];

	if(toggle) {
		 $('.pay_mod').each(function() { //loop through each checkbox
			this.checked = true;  //select all checkboxes with class "checkbox1"              
			});
	} 
	else
	{  
		$('.pay_mod').each(function() { //loop through each checkbox
			this.checked = false;  //select all checkboxes with class "checkbox1"              
		}); 
	}
}

</script>
