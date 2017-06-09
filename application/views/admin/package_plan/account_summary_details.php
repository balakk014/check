<?php defined('SYSPATH') OR die("No direct access allowed.");

$total_package_plan=count($all_package_info_list);


if(isset($all_package_info_list)){
$table_css=$export_excel_button="";
?>


<div class="next_grid_cell">
<div class="next_card">
    <div class="has_bulk_actions">
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" aria-expanded="true" href="#Transaction list" title="<?php echo __('transaction_list'); ?>"><?php echo __('transaction_list'); ?></a></li>
            </ul>
            <div class="tab-content acc_summary_det">
                <div id="Transaction list" class="tab-pane active">
                    <div class="table_wrapper">
                        <table cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><?php echo __('s_no'); ?></th>
                                    <th><?php echo __('epg_transaction_id');?></th>
                                    <th><?php echo __('purchase_inv_id');?></th>
                                    <th><?php echo __('plan_type');?></th>
                                    <th><?php echo __('total_amount');?></th>
                                    <th><?php echo __('request_date');?></th>
                                    <th><?php echo __('payment_status');?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sno=1;
                                $total_package_info=count($all_package_info_list);
                                if($total_package_info>0){
                                foreach($all_package_info_list as $all_package_info_details) {                                    
                                        $id=$all_package_info_details['_id'];
                                        $package_type=$all_package_info_details['package_type'];
                                        $payment_terms=$all_package_info_details['payment_terms'];
                                        $total_amount=$all_package_info_details['amount'];
                                        $paid_status=$all_package_info_details['paid_status'];
                                        $transaction_id='';
                                        $ePGTxnID='';
                                        if(isset($all_package_info_details['txnID'])){
                                        $transaction_id=$all_package_info_details['txnID'];
                                        $ePGTxnID=$all_package_info_details['ePGTxnID'];
                                        }
                                        $requested_date='';
                                        if(isset($all_package_info_details['createddate'])){
                                            $requested_date=Commonfunction::convertphpdate('Y-m-d H:i:s',$all_package_info_details['createddate']);
                                        }
                                        $invoice_id=$all_package_info_details['purchase_inv_id'];
                                        $plan_type='';
                                        $payment_terms_desc='';
                                        if($package_type==0){
                                            $plan_type=__('trial');
                                        }else if ($package_type==1){
                                            $plan_type=__('basic');
                                        }else if($package_type==2){
                                            $plan_type=__('plantinum');
                                        }else{
                                            $plan_type=__('enterprise');
                                        }
                                        
                                        if($payment_terms==1)
                                        {
                                            $payment_terms_desc=__('monthly');
                                        }elseif ($payment_terms==2) {
                                            $payment_terms_desc=__('yearly');
                                        } elseif ($payment_terms==3) {
                                            $payment_terms_desc=__('bi');
                                        } elseif ($payment_terms==4) {
                                            $payment_terms_desc=__('monthly');
                                        }
                                        
                                        $package_plan=$plan_type.'-'.$payment_terms_desc;
                                        if($paid_status==0)
                                        {
                                            $payment_status=__('payment_failure');
                                        
                                        }else{
                                            $payment_status=__('paid');   
                                        
                                        }
                                            
                                        
                                        
                                            if($ePGTxnID==''){
                                                $transaction_id='-';
                                                $class='style=padding-left:30px;';
                                                
                                            }else{
                                            
                                            $transaction_id='<a href='.URL_BASE.'package/account_transaction/'.$id.'>'.$ePGTxnID.'</a>';
                                            $class='';
                                            }
                                        
                                        echo '<tr>
                                                <td>'.$sno.'</td>
                                                <td '.$class.'>'.$transaction_id.'</a></td>
                                                <td><a href='.URL_BASE.'package/account_transaction/'.$id.'>'.$invoice_id.'</a></td>
                                                <td>'.$package_plan.'</td>
                                                <td>'.CLOUD_CURRENCY_SYMB.' '.number_format($total_amount,2).'</td>
                                                <td>'.$requested_date.'</td>
                                                <td>'.$payment_status.'</td>
                                                </tr>';
                                        $sno++;
                                }
                                 }else{
                                    echo '<tr><td colspan="6" align="center">'.__('no_data').'</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                 <?php if($total_package_plan > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
            </div>
            
        </div>
    </div>
   
</div>  
		
</div>






<?php } ?>

