<?php defined('SYSPATH') OR die("No direct access allowed."); 

//For CSS class define in the table if the data's available
//===========================================================
$total_gateways=count($payment_gatway_details);

$table_css=$export_excel_button="";
$table_css="";
if($total_gateways > 0)
{  $table_css='class="table_border"';  }?>

<div class="container_content fl clr">
<div class="cont_container mt15 mt10">
    <div class="content_top"><div class="top_left"></div><div class="top_center"></div><div class="top_rgt"></div></div>
    <div class="content_middle">
        <form method="post" class="admin_form" name="frmpayment" id="frmpayment">
			<div class="clr">&nbsp;</div>
			<?php if($total_gateways > 0){ ?>
			<div class= "overflow-block">
			<?php }?>
			<div class="admin_new_tab">
			<table cellspacing="1" cellpadding="10" align="center" <?php echo $table_css; ?>>
			<?php if($total_gateways > 0){ ?>
			<!-- ** payment Listings Starts Here ** -->
				<tr class="rowhead"> 
						<th align="center"><?php echo __('sno_label'); ?></th>
						<th align="center"><?php echo __('action_label'); ?></th>
						<th align="center"><?php echo __('payment_name_label'); ?></th>
						<th align="center"><?php echo __('payment_description');?></th>
						<th align="center"><?php echo __('payment_method'); ?></th>
       			 </tr>    
				<?php 
				 
				 $sno=$Offset; /* For Serial No */
				 
				 foreach($payment_gatway_details as $payment_gatway_details){
				 
				 $sno++;
				 
				 $trcolor=($sno%2==0) ? 'oddtr' : 'eventr'; 
				 
				?>
				<tr class="<?php echo $trcolor; ?>">

                <td align="center">
                    <?php echo $sno; ?>
                </td>
                <td>
 							<?php echo '<a href='.URL_BASE.'sitemanagement/edit_payment_gateways/'.$payment_gatway_details['id'].' " title ='.__('edit').' class="editicon"></a>' ; ?>               
                </td>
                 <td>
                   <?php echo $payment_gatway_details['payment_gatway']; ?>
                </td>               
                <td>
                    <?php echo wordwrap($payment_gatway_details['description'],105,'<br/>',1); ?>
                </td>
                <td align="center">
                <?php echo ($payment_gatway_details['payment_method'] == 'T')?__('sandbox'):__('live'); ?>
                </td>

        </tr>
		<?php } 
		 }
	// ** Job favorites Listings Ends Here ** //
		 else { 
	// ** No Job favorites is Found Means ** //
		?>
       	<tr>
        	<td class="nodata"><?php echo __('no_data'); ?></td>
        </tr>
        <?php } ?>

</table></div>
<?php if($total_gateways > 0){ ?>
</div>
<?php }?>
</form>
	</div>
	<div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>	
<div class="clr">&nbsp;</div>
<div class="pagination">
	<?php if($total_gateways > 0): ?>
	 <p><?php echo $pag_data->render(); ?></p>  
	<?php endif; ?>
</div>
<div class="clr">&nbsp;</div>
</div>


