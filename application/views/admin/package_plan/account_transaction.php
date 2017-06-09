<?php defined('SYSPATH') OR die("No direct access allowed.");
   
/*$transaction_id=$transaction_info[0]['txnID'];
$epg_transaction_id=$transaction_info[0]['ePGtxnID'];*/
    $package='';
if($transaction_info['package_type']==1){
    $package=__('basic');
}else if($transaction_info['package_type']==2){
    $package=__('plantinum');
}else if($transaction_info['package_type']==3){
    $package=__('enterprise');
}
$payment_terms_desc='';
if($transaction_info['payment_terms']==1){
    $payment_terms_desc=__('monthly');
}elseif($transaction_info['payment_terms']==2){
    $payment_terms_desc=__('yearly');
}elseif($transaction_info['payment_terms']==3){
    $payment_terms_desc=__('biennial');
}elseif($transaction_info['payment_terms']==4){
    $payment_terms_desc=__('triennial');
}
if($payment_terms_desc==''){
    $package_type_terms=$package;
}else{
    $package_type_terms=$package.'-'.$payment_terms_desc;
}

if($transaction_info['paid_status']==0){
    $payment_status=__('payment_failure');
}else{
    $payment_status=__('payment_success');
}

$pay_mode_name='-';
$txnID='-';
$ePGTxnID='-';
$responsecode='-';
$response_msg='-';
if(isset($transaction_info['pay_mode'])){
    if($transaction_info['pay_mode']==1){
        $pay_mode_name=__('credit_card');
    }else{
        $pay_mode_name=__('cash');
    }
}

if(isset($transaction_info['service_tax'])){
    $service_tax=$transaction_info['service_tax'];
    $service_tax_cost=$transaction_info['service_tax'];
}

if(isset($transaction_info['txnID'])){
    if($transaction_info['txnID']!=''){    
        $txnID = $transaction_info['txnID'];        
    }    
}

if(isset($transaction_info['ePGTxnID'])){
    if($transaction_info['ePGTxnID']!=''){    
        $ePGTxnID = $transaction_info['ePGTxnID'];        
    }    
}

if(isset($transaction_info['responsecode'])){
    if($transaction_info['responsecode']!=''){    
        $responsecode = $transaction_info['responsecode'];        
    }    
}
if(isset($transaction_info['response_msg'])){
    if($transaction_info['response_msg']!=''){    
        $response_msg = $transaction_info['response_msg'];        
    }    
}
?>


<div class="next_grid_cell">
<div class="next_card">
    <div class="has_bulk_actions">
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" aria-expanded="true" href="#Transaction_details" title="<?php echo __('transaction_details'); ?>"><?php echo __('transaction_details'); ?></a></li>
            </ul>
            <div class="tab-content acc_summary_det">
                <div id="Transaction_details" class="tab-pane active">
                    <div class="confirm_ord">
                        <ul>
                            <li><label><?php echo __('purchase_inv_id');?></label><p><?php echo $transaction_info['purchase_inv_id'];?></p></li>                            
                            <li><label><?php echo __('transaction_id');?></label><p><?php echo $txnID; ?></p></li>
                            <li><label><?php echo __('epg_transaction_id');?></label><p><?php echo $ePGTxnID;?></p></li>                            
                            <li><label><?php echo __('plan_type');?></label><p><?php echo $package_type_terms;?></p></li>
                            <li><label><?php echo __('subscription_cost');?></label><p><?php echo CLOUD_CURRENCY_SYMB.' '.number_format($transaction_info['subscription_cost'],2);?></p></li>
                            <li><label><?php echo __('setup_cost');?></label><p><?php echo CLOUD_CURRENCY_SYMB.' '.number_format($transaction_info['setup_cost'],2);?></p></li>
                            <?php if(isset($transaction_info['service_tax'])){
                            echo  "<li><label>". __('service_tax_cost') ."(".$transaction_info['service_tax']."%)</label><p>".CLOUD_CURRENCY_SYMB.' '.number_format($transaction_info['service_tax_cost'],2)."</p></li>";
                            } ?>
                            <li><label><?php echo __('total_amount');?></label><p><?php echo CLOUD_CURRENCY_SYMB.' '.number_format($transaction_info['amount'],2);?></p></li>
                        </ul>
                        <ul>                         
                            <li><label><?php echo __('payment_status');?></label><p><?php echo $payment_status;?></p></li>
                            <li><label><?php echo __('response_code');?></label><p><?php echo $responsecode;?></p></li>
                            <li><label><?php echo __('request_date');?></label><p><?php echo Commonfunction::convertphpdate('Y-m-d H:i:s',$transaction_info['createddate']);?></p></li>                           
                            <li><label><?php echo __('response_msg');?></label><p><?php echo $response_msg;?></p></li>
                             <li><label><?php echo __('pay_mode');?></label><p><?php echo $pay_mode_name;?></p></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
