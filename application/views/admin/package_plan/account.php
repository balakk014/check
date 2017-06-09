<?php
defined('SYSPATH') OR die("No direct access allowed.");

$package_type = $get_site_info[0]['package_type'];
$selected_plan = '';

$bill_payment_terms = $get_site_info[0]['bill_payment_terms'];
if ($package_type == 1) {
    $selected_plan = __('basic');
} else if ($package_type == 2) {
    $selected_plan = __('plantinum');
} else if ($package_type == 3) {
    $selected_plan = __('enterprise');
} else {
    $selected_plan = __('trial');
}
$payment_terms='';
if ($bill_payment_terms == 1) {
    $payment_terms = __('monthly');
} else if ($bill_payment_terms == 2) {
    $payment_terms = __('yearly');
} else if ($bill_payment_terms == 3) {
    $payment_terms = __('biennial');
} else if ($bill_payment_terms == 4) {
    $payment_terms = __('triennial');
}

$account_current_status = ACCOUNT_STATUS;
if ($account_current_status == 0) {
    $account_status = __('inactive');
} else {
    $account_status = __('active');
}

$account_name = $admin_info['name'] . " " . $admin_info['lastname'];
$char_name = ucwords(substr($admin_info['name'], 0, 1) . substr($admin_info['lastname'], 0, 1));
$dateformate = '';
$expirydate_format = date(' F j,Y', strtotime(Commonfunction::convertphpdate($dateformate, $get_site_info[0]["expiry_date"])));

?>


<div class="account_outer">
    <div class="account_det_list">
        <div class="account_lft_det">
            <div class="acc_tit"><h2><?php echo __('account_overview');?></h2></div>
            <div class="acc_det">
                <p><?php echo __('view_our'); ?> <a title="<?php echo __('terms_of_service');?>" href="<?php echo TERMS_URL;?>" target="_blank"><?php echo __('terms_of_service');?></a> and <a title="<?php echo __('privacy_policy');?>" href="<?php echo PRIVACY_URL;?>" target="_blank"><?php echo __('privacy_policy');?></a></p>
                <p><a title="<?php echo __('compare_plans'); ?>" href="<?php echo URL_BASE . 'package/account_plan'; ?>"><?php echo __('compare_plans'); ?></a> <?php echo __('with_different_features_rates'); ?></p>
            </div>
        </div>
        <div class="account_rgt_det">
            <div class="rgt_lay">
                <div class="member_list">
                    <h3><?php echo __('member_since');?></h3>
                    <p><?php echo Commonfunction::convertphpdate('Y-m-d H:i:s',$get_site_info[0]['create_date']); ?></p>
                </div>
                <div class="member_list">
                    <h3><?php echo __('current_plan');?></h3>
                    <p><?php echo $selected_plan . " / " . $payment_terms; ?></p>
                </div>
                <div class="member_list">
                    <h3><?php echo __('account_status');?></h3>
                    <p><?php echo $account_status; ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="account_det_list">
        <div class="account_lft_det">
            <div class="acc_tit"><h2><?php echo __('billing_info');?></h2></div>
            <div class="acc_det">
                <p><?php echo __('summary_account');?></p>
            </div>
        </div>
        <div class="account_rgt_det">
            <div class="rgt_lay">
                <div class="billing_lft">
                    <h2 class="comm_tit"><?php echo __('add_your_credit_card'); ?></h2>
                    <p class="comm_dec"><?php $add_card_plan=str_replace('##CLOUD_SITENAME##',CLOUD_SITENAME,__('add_card_plan')); echo $add_card_plan?></p>
                    <a class="btn_primary" href="<?php echo URL_BASE . 'package/addcreditcard'; ?>" title="<?php echo __('add_credit_card'); ?>"><?php echo __('add_credit_card'); ?></a>
                </div>
                <div class="billing_rgt"><img src="<?php echo URL_BASE; ?>public/cloud_package/credit_card.png"/></div>
            </div>
        </div>
    </div>
    <div class="account_det_list">
        <div class="account_lft_det">
            <div class="acc_tit"><h2><?php echo __('account_permission'); ?></h2></div>
            <div class="acc_det">
                <p><?php echo __('summary_current_payment');?></p>
            </div>
        </div>
        <div class="account_rgt_det">
            <div class="rgt_lay">
                <div class="account_owner">
                    <h2 class="comm_tit"><?php echo __('account_owner'); ?></h2>
                    <div class="owner_det">
                        <div class="owner_name">
                            <span><?php echo $char_name; ?></span>
                        </div>
                        <div class="owner_lastlogin">
                            <p><a href="<?php echo URL_BASE . 'admin/editprofile/1'; ?>"><?php echo $account_name; date_default_timezone_set(TIMEZONE);?></a></p>
                            <p><?php echo __('last_login_was'); ?> <?php echo date('l, F j,Y, g:i A', strtotime($admin_info['last_login'])) . "  " . date('T'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rgt_lay mt20">
                <div class="staff_detail">
                    <h2 class="comm_tit"><?php echo __('driver_account');if(PACKAGE_TYPE!=3){ echo ' ('. $total_driver_count.' of '.$get_site_info[0]['driver_count'].')'; }?></h2>
                    <p class="comm_dec"><?php if(PACKAGE_TYPE!=3){echo str_replace('##driver_count##', $get_site_info[0]['driver_count'], __('customize_plan_msg'));}else{echo __('unlimited_plan_msg');} ?> <a title="<?php echo __('compare_plans'); ?>" href="<?php echo URL_BASE . 'package/account_plan'; ?>"><?php echo __('compare_plans'); ?>.</a></p>
                    <a class="common_butt" href="<?php echo URL_BASE;?>add/driver" title="<?php echo __('add_driver_account'); ?>"><?php echo __('add_driver_account'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="account_det_list" style="border: none;">
        <div class="account_lft_det">
            <div class="acc_tit"><h2><?php echo __('invoice_fees');?></h2></div>
            <div class="acc_det">
                <p><?php echo __('summary_invoice');?></p>
                <p><?php echo __('your_next_invoice_will_be_sentto'); ?> <a href="#"><?php echo $admin_info['email']; ?></a> <?php echo __('on'); ?> <?php echo $expirydate_format; ?>.</p>
<!--                <p>If you reach $100.00 in charges in a single day, or $200.00 in charges in total, an invoice will be issued immediately. <a href="#">View outstanding charges.</a></p>-->
            </div>
        </div>
        <div class="account_rgt_det">
            <div class="rgt_lay">
                <div class="staff_detail">
                    <h2 class="comm_tit"><?php echo __('account_summary');?></h2>
                    <p class="comm_dec"><?php $summary_view= str_replace('##CLOUD_SITENAME##',CLOUD_SITENAME,__('summary_view')); echo $summary_view;?></p>
                    <a class="btn_primary" href="<?php echo URL_BASE . 'package/account_summary'; ?>" title="<?php echo __('view_account_summary'); ?>"><?php echo __('view_account_summary'); ?></a>
                </div>
            </div>
            <div class="rgt_lay mt20">              
                    <?php                     
                    //if(TRIAL_EXPIRY_DAYS<=CLOUD_INVOICE_DUE_DAY){
                    $total_paid_info=count($get_paid_info);
                    if($total_paid_info>0){
                        
                     echo '<div class="invoice_lst"><h2 class="comm_tit">'.__('invoice').'</h2>
                             <div class="table_wrapper">
                             <table>
                             <thead><tr><th>'.__('invoice_id').'</th>
                             <th>'.__('purchase_date').'</th>                            
                             <th style="text-align:center">'.__('invoice_view').'</th>
                             </tr></thead><tbody>';
                     foreach($get_paid_info as $get_paid_invoice){
                         
                          echo '<tr><td>'.$get_paid_invoice['purchase_inv_id'].'</td>
                              <td>'.Commonfunction::convertphpdate('Y-m-d H:i:s', $get_paid_invoice['createddate']).'</td>                             
                              <td style="text-align:center"><form action="'.URL_BASE.'package/genpdf" method="post" name=frmgenpdf" id ="frmgenpdf">
                              <input type="submit" name="gen_pdf" id="package_gen_pdf" value="" title="Generate PDF">
                              <input type="hidden" id="pdf-id" name="pdf-id" value='.$get_paid_invoice['_id'].'>
                              </form></td></tr>';
                     }
                             '</tbody>
                              </table></div></div>';   
                    } else{
                    ?>
               
                    <div class="invoice_comm_det">
                    <h2><?php echo __('you_dont_have_invoices_yet'); ?></h2>
                    <p><?php echo __('your_invoices_will_be_shown_here_info'); ?></p>
                    </div>
                    <?php  } ?>
                
            </div>
        </div>
    </div>

</div>

<script>

 //$("#gen_pdf").click(function() {
function gen_pdf(id){
      		

		  $.ajax({
			url:"<?php echo URL_BASE;?>package/genpdf",
			type:"post",
			data:"id="+id,
			success:function(data){
                                alert('got response');
                            window.open(data);
			
			},
			error:function(data)
			{
				//alert(cid);
			}
		});
                }
    //});
    
</script>
