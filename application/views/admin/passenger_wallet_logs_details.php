<?php defined('SYSPATH') OR die("No direct access allowed."); 
if($_SESSION['user_type'] =='C') {
	$company_currency = findcompany_currency($_SESSION['company_id']);
	$company_currency = $company_currency[0]['currency_symbol'];
} else{
	$company_currency = CURRENCY;
}
?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
        <div class="content_middle">
            <div class="driverinfo_common">
                <ul>
                    <li>
                        <label><?php echo __('transactionid_label'); ?></label>
                        <p><?php if(isset($log_details[0]['transaction_id']) && $log_details[0]['transaction_id'] != '') { echo $log_details[0]['transaction_id']; } else { echo '-'; } ?></p>
                    </li>
                    <li>
                        <label><?php echo __('name'); ?></label>
                        <p><?php if(isset($log_details[0]['name']) && $log_details[0]['name'] != '') { echo $log_details[0]['name']; } else { echo '--'; } ?></p>
                    </li>
                    <li>
                        <label><?php echo __('mobile_number'); ?></label>
                        <p><?php echo (isset($log_details[0]['mobile_number']) && $log_details[0]['mobile_number']!='')?$log_details[0]['mobile_number'] : '--'; ?></p>
                    </li>
                    <li>
                        <label><?php echo __('amount'); ?></label>
                        <p><?php if(isset($log_details[0]['amount']) && $log_details[0]['amount'] > 0) { echo $company_currency.$log_details[0]['amount']; } else { echo '-'; } ?></p>
                    </li>
                    <li>
                        <label><?php echo __('payment_type'); ?></label>
                        <p><?php echo isset($log_details[0]['payment_type'])?$log_details[0]['payment_type']:"--"; ?></p>
                    </li>
                    <li>
                        <label><?php echo __('payment_status'); ?></label>
                        <p><?php echo isset($log_details[0]['payment_status'])?$log_details[0]['payment_status']:"--"; ?></p>
                    </li>
                    <li>
                        <label><?php echo __('transaction_date'); ?></label>
                        <p><?php                   
                            $createdate=(isset($log_details[0]['createdate']) && $log_details[0]['createdate'] != '0000-00-00 00:00:00')?$log_details[0]['createdate']:"0000-00-00 00:00:00"; 
                            if($createdate !="0000-00-00 00:00:00"){
                            	$createdate=Commonfunction::getDateTimeFormat($createdate,1);
                            }else{
                            	$createdate="--";
                            }
                            ?>
                            <?php echo $createdate; ?>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
