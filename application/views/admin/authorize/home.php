<?php defined('SYSPATH') OR die("No direct access allowed."); ?>

<div class="container_content fl clr">
 <div class="cont_container mt15 mt10">

    <div class="content_top"><div class="top_left"></div><div class="top_center"></div><div class="top_rgt"></div></div>
    <div class="content_middle">
		
            <table width="100%" border="0" cellpadding="5" cellspacing="" > 
                    <tr>
                        <td width="40%" valign="top" ><label><?php echo __('name_label'); ?></label></td>
                        <td> <?php echo isset($login_detail[0]['name'])?$login_detail[0]['name']:'--'; ?></td>      
                    </tr>
              
                    <tr>
                        <td valign="top" ><label><?php echo __('email_label');?></label></td>
                        <td><?php echo isset($login_detail[0]['email'])?$login_detail[0]['email']:'--'; ?></td>      
                    </tr>   

                    <tr>
                        <td valign="top" ><label><?php echo __('total_transactions_amount'); ?></label></td>
                        <td>$ <?php echo isset($total_transactions_amount)?$total_transactions_amount:'0'; ?> USD</td>      
                    </tr>
<?php /*
                    <tr>
                            <td valign="top" ><label><?php echo 'Caregivers Pay Amount (USD)';?></label></td>
                            <td><?php echo isset($caregivers_pay_amount)?$caregivers_pay_amount:'0'; ?></td>      
                    </tr>



                     <tr>
                            <td valign="top" ><label><?php echo 'Transactions Commision Amount (USD)';?></label></td>
                            <td><?php echo isset($transactions_commision_amount)?$transactions_commision_amount:'0'; ?></td>      
                    </tr>

                     <tr>
                            <td valign="top" ><label><?php echo 'Withdraw Commision Amount (USD)';?></label></td>
                            <td><?php echo isset($withdraw_commision_amount)?$withdraw_commision_amount:'0'; ?></td>      
                    </tr>



	                <tr>
                            <td valign="top" ><label><?php echo 'Admin Total Commision Received (USD)';?></label></td>
                            <td><?php echo isset($admin_total_commision)?$admin_total_commision:'0'; ?></td>      
                    </tr>
*/ ?>               
            </table>


    </div>
    <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
 </div>
</div>


