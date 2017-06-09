<?php defined('SYSPATH') OR die("No direct access allowed.");
   
  if(isset($paid_amount[0]['total_amount'])) 
     {
  
$paid_amount=$paid_amount[0]['total_amount'];
     }else{
         $paid_amount="0.00";
     }
    
   ?>


<div class="account_summary_out">
    <div class="account_summary_inn">
        <div class="rgt_lay">
            <div class="summary_top_det">
                <h2><?php echo __('taximobility_fees'); ?></h2>
                <a title="View full statement" href="<?php echo URL_BASE;?>package/account_summary_details"><?php echo __('view_full_statement'); ?></a>
            </div>
            <div class="taxi_fees_det">
                <div class="fees_list">
                    <p><?php echo __('total_fees'); ?></p>
                    <span>$ <?php echo number_format($paid_amount,2);?> USD</span>
                </div>
            </div>
        </div>
    </div>
</div>
