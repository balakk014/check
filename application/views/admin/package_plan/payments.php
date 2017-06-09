<?php
defined('SYSPATH') OR die("No direct access allowed.");
$count_payment_settings = count($payment_settings);

?>
<div class="account_outer">
    <div class="account_det_list">
        <div class="account_lft_det">
            <div class="acc_tit">
                <h2><?php echo __('accept_payments'); ?></h2>
            </div>
            <div class="acc_det">
                <p><?php echo __('enable'); ?> <a><?php echo __('payment_gateways'); ?></a><?php echo __('payment_gateways_info'); ?></p>
                <p><?php echo __('payment_gateways_accept_info'); ?></p>
            </div>
        </div>
        <div class="account_rgt_det">
            <form method="POST" enctype="multipart/form-data" class="form" id="direct_payment_gateway" action="direct_payment_gateway" >
                <div class="rgt_lay sms_lay mt20">
                    <div class="sms_top_sec">
                        <div class="account_owner">
                            <div class="payment_logo">
                                <img width="100" height="25" src="<?php echo URL_BASE; ?>public/admin/images/logo_paypal.svg"/>
                            </div>
                            <div class="pay_card_det">
                                <div class="pay_crd_lst">
                                    <p><a href="#" title="<?php echo __('express_checkout'); ?>"><?php echo __('express_checkout'); ?></a></p>
                                    <p><?php echo __('accept_paypal_info'); ?> <a class="learn_more" href="#"><?php echo __('learn_more'); ?></a></p>
                                </div>
                            </div>
                            <div class="div_lft">
                                <div class="small_sel">
                                    <?php
                                    if (isset($postvalue['payment_gateway_type']) && $postvalue['payment_gateway_type'] == 1) {
                                        $selected = "selected='selected'";
                                    } else {
                                        $selected = "selected='selected'";
                                    }
                                    ?>
                                    <select class="form_control payment_gateway_type" name="payment_gateway_type" onchange="gatewaySelected(this.value)">
                                        <option value="0"><?php echo __('select_paypal_method'); ?></option>
                                        <option value="1" <?php echo $selected; ?> ><?php echo __('paypal_dodirect_payment'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sms_sett_opt" id="default_acc" style="display:none">
                        <div class="sms_mid_sec">
                            <div class="sms_mid_lft">
                                <input type="hidden" value="1" name="payment_gateway_provider_id[1]" id="payment_gateway_provider_id" />
                                <div class="form_group">
                                    <?php
                                    if (isset($paypal_payment_settings[0]['payment_gatway']) && !array_key_exists('payment_gateway_name', $postvalue)) {
                                        $payment_gateway_name = $paypal_payment_settings[0]['payment_gatway'];
                                    } else {
                                        if (isset($postvalue['payment_gateway_name']) && isset($_POST['submit_editpayment'])) {
                                            $payment_gateway_name = $postvalue['payment_gateway_name'];
                                        }else if(isset($paypal_payment_settings[0]['payment_gatway'])){
                                            $payment_gateway_name = $paypal_payment_settings[0]['payment_gatway'];
                                        }
                                        else {
                                        
                                            $payment_gateway_name = "";
                                        }
                                    }
                                    ?>
                                    <label class="small_control_label"><?php echo __('payment_gateway_name'); ?></label><span class="star">*</span>
                                    <input type="text" name="payment_gateway_name" id="payment_gateway_name" class="small_form_control" value="<?php echo $payment_gateway_name; ?>"/>
                                </div>
                                <div class="form_group">
                                    <?php
                                    if (isset($paypal_payment_settings[0]['description']) && !array_key_exists('description', $postvalue)) {
                                        $description = $paypal_payment_settings[0]['description'];
                                    } else {
                                        if (isset($postvalue['description']) && isset($_POST['submit_editpayment'])) {
                                            $description = $postvalue['description'];
                                        }else if(isset($paypal_payment_settings[0]['description'])){
                                            $description = $paypal_payment_settings[0]['description'];
                                        }
                                        else {
                                        
                                            $description = "";
                                        }
                                    }
                                    ?>
                                    <label class="small_control_label"><?php echo __('payment_gateway_description'); ?></label><span class="star">*</span>
                                    <input type="text" class="small_form_control" name="description" id="description1" title="<?php echo __('enter_payment_description'); ?>" maxlength="250" value="<?php echo $description; ?>" >
                                    <?php if (isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('description', $errors)) {
                                        echo "<span class='error'>" . ucfirst($errors['description']) . "</span>";
                                    } ?>
                                </div>
                                <div class="form_group">
                                    <label class="small_control_label"><?php echo __('pay_currency_code'); ?></label><span class="star">*</span>

<input type="text" name="currency_code" id= "currency_code" class="small_form_control" value="<?php echo CURRENCY_FORMAT; ?>" readonly="readonly"/>
<?php if (isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('currency_code', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['currency_code']) . "</span>";
} ?>
                                </div>
                                <div class="form_group">
                                    <label class="small_control_label"><?php echo __('pay_currency_symbol'); ?></label><span class="star">*</span>

<input type="text" name="currency_symbol" id= "currency_symbol" class="small_form_control" value="<?php echo CURRENCY; ?>" readonly="readonly"/>
                                <?php if (isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('currency_symbol', $errors)) {
                                    echo "<span class='error'>" . ucfirst($errors['currency_symbol']) . "</span>";
                                } ?>
                                </div>
                            </div>
                            <div class="sms_mid_rgt">
                                
                                <div class="test_mode">
                                    <div class="form_group">
                                        <?php
                                        if (isset($paypal_payment_settings[0]['payment_gateway_username']) && !array_key_exists('payment_gateway_username', $postvalue)) {
                                            $payment_gateway_username = $paypal_payment_settings[0]['payment_gateway_username'];
                                        } else {
                                            if (isset($postvalue['payment_gateway_username']) && isset($_POST['submit_editpayment'])) {
                                                $payment_gateway_username = $postvalue['payment_gateway_username'];
                                            }else if(isset($paypal_payment_settings[0]['payment_gateway_username'])){
                                                $payment_gateway_username = $paypal_payment_settings[0]['payment_gateway_username'];
                                        } else {
                                                $payment_gateway_username = "";
                                            }
                                        }
                                        ?> 
                                        <label class="small_control_label"><?php echo __('payment_gateway_username'); ?></label><span class="star">*</span>
                                        <input type="text" class="small_form_control" name="payment_gateway_username" id="payment_gateway_username" title="<?php echo __('enter_payment_gateway_username'); ?>" maxlength="250" value="<?php echo $payment_gateway_username; ?>" >
                                        <?php if (isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('payment_gateway_username', $errors)) {
                                            echo "<span class='error'>" . ucfirst($errors['payment_gateway_username']) . "</span>";
                                        } ?>
                                    </div>

                                    <div class="form_group">
                                        <?php
                                        if (isset($paypal_payment_settings[0]['payment_gateway_password']) && !array_key_exists('payment_gateway_password', $postvalue)) {
                                            $payment_gateway_password = $paypal_payment_settings[0]['payment_gateway_password'];
                                        } else {
                                            if (isset($postvalue['payment_gateway_password']) && isset($_POST['submit_editpayment'])) {
                                                $payment_gateway_password = $postvalue['payment_gateway_password'];
                                            } else if(isset($paypal_payment_settings[0]['payment_gateway_password'])){
                                                $payment_gateway_password = $paypal_payment_settings[0]['payment_gateway_password'];
                                        }else {
                                                $payment_gateway_password = "";
                                            }
                                        }
                                        ?>
                                        <label class="small_control_label"><?php echo __('payment_gateway_password'); ?></label><span class="star">*</span>
                                        <input type="text" class="small_form_control" name="payment_gateway_password" id="payment_gateway_password" title="<?php echo __('enter_payment_gateway_password'); ?>" maxlength="250" value="<?php echo $payment_gateway_password; ?>">
                                        <?php if (isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('payment_gateway_password', $errors)) {
                                            echo "<span class='error'>" . ucfirst($errors['payment_gateway_password']) . "</span>";
                                        } ?>
                                    </div>

                                    <div class="form_group">
                                        <?php
                                        if (isset($paypal_payment_settings[0]['payment_gateway_signature']) && !array_key_exists('payment_gateway_signature', $postvalue)) {
                                            $paypal_api = $paypal_payment_settings[0]['payment_gateway_signature'];
                                        } else {
                                            if (isset($postvalue['payment_gateway_signature'])) {
                                                $paypal_api = $postvalue['payment_gateway_signature'];
                                            } else {
                                                $paypal_api = "";
                                            }
                                        }
                                        ?>
                                        <label class="small_control_label"><?php echo __('payment_gateway_signature'); ?></label><span class="star">*</span>
                                        <input type="text" class="small_form_control" name="payment_gateway_signature" id="payment_gateway_signature"  title="<?php echo __('enter_payment_gateway_signature'); ?>" maxlength="250" value="<?php echo $paypal_api; ?>">
                                        <?php if (isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('payment_gateway_signature', $errors)) {
                                            echo "<span class='error'>" . ucfirst($errors['payment_gateway_signature']) . "</span>";
                                        } ?>
                                    </div>
                                </div>

                                <div class="live_mode">
                                    <div class="form_group">
                                        <?php
                                        if (isset($paypal_payment_settings[0]['live_payment_gateway_username']) && !array_key_exists('live_payment_gateway_username', $postvalue)) {
                                            $live_payment_gateway_username = $paypal_payment_settings[0]['live_payment_gateway_username'];
                                        } else {
                                            if (isset($postvalue['live_payment_gateway_username'])) {
                                                $live_payment_gateway_username = $postvalue['live_payment_gateway_username'];
                                            } else {
                                                $live_payment_gateway_username = "";
                                            }
                                        }
                                        ?> 
                                        <label class="small_control_label"><?php echo __('payment_gateway_username'); ?></label><span class="star">*</span>
                                        <input type="text" class="small_form_control" name="live_payment_gateway_username" id="live_payment_gateway_username" title="<?php echo __('enter_payment_gateway_username'); ?>" maxlength="250" value="<?php echo $live_payment_gateway_username; ?>" >
                                        <?php if (isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('live_payment_gateway_username', $errors)) {
                                            echo "<span class='error'>" . ucfirst($errors['live_payment_gateway_username']) . "</span>";
                                        } ?>
                                    </div>

                                    <div class="form_group">
<?php
if (isset($paypal_payment_settings[0]['live_payment_gateway_password']) && !array_key_exists('live_payment_gateway_password', $postvalue)) {
    $live_payment_gateway_password = $paypal_payment_settings[0]['live_payment_gateway_password'];
} else {
    if (isset($postvalue['live_payment_gateway_password'])) {
        $live_payment_gateway_password = $postvalue['live_payment_gateway_password'];
    } else {
        $live_payment_gateway_password = "";
    }
}
?>
                                        <label class="small_control_label"><?php echo __('payment_gateway_password'); ?></label><span class="star">*</span>
                                        <input type="text" class="small_form_control" name="live_payment_gateway_password" id="live_payment_gateway_password" title="<?php echo __('enter_payment_gateway_password'); ?>" maxlength="250" value="<?php echo $live_payment_gateway_password; ?>">
                                        <?php if (isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('live_payment_gateway_password', $errors)) {
                                            echo "<span class='error'>" . ucfirst($errors['live_payment_gateway_password']) . "</span>";
                                        } ?>
                                    </div>

                                    <div class="form_group">
<?php
if (isset($paypal_payment_settings[0]['live_payment_gateway_signature']) && !array_key_exists('live_payment_gateway_signature', $postvalue)) {
    $live_payment_gateway_signature = $paypal_payment_settings[0]['live_payment_gateway_signature'];
} else {
    if (isset($_POST['submit_editpayment']) && isset($postvalue['live_payment_gateway_signature'])) {
        $live_payment_gateway_signature = $postvalue['live_payment_gateway_signature'];
    } else if(isset($paypal_payment_settings[0]['live_payment_gateway_signature'])){
        $live_payment_gateway_signature = $paypal_payment_settings[0]['live_payment_gateway_signature'];
    }else {
        $live_payment_gateway_signature = "";
    }
}
?>                                
                                        <label class="small_control_label"><?php echo __('payment_gateway_signature'); ?></label><span class="star">*</span>
                                        <input type="text" class="small_form_control" name="live_payment_gateway_signature" id="live_payment_gateway_signature" title="<?php echo __('live_payment_gateway_signature'); ?>" maxlength="250" value="<?php echo $live_payment_gateway_signature; ?>">
<?php if ( isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('live_payment_gateway_signature', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['live_payment_gateway_signature']) . "</span>";
} ?>
                                    </div>
                                </div>
                                <div class="form_group">
                                    <label class="small_control_label"><?php echo __('payment_method'); ?></label><span class="star">*</span>
                                    <div class="pay_method">
                                        <div class="radio_primary_small">
                                            <input type="radio" name="payment_method" onclick="change_payment_option('T');" id="payment_method" value="T" <?php if (isset($postvalue['payment_method'])) {
    if ($postvalue['payment_method'] == 'T') {
        echo 'checked=checked';
    }
} else {
    if (isset($paypal_payment_settings[0]['payment_method']) && $paypal_payment_settings[0]['payment_method'] == 'T') {
        echo 'checked=checked';
    }
} ?> >
                                            <label for="payment_method"><?php echo 'Test Mode'; ?></label>
                                        </div>
                                        <div class="radio_primary_small">
                                            <input type="radio" name="payment_method" onclick="change_payment_option('L');" id="payment_method" value="L" <?php if (isset($postvalue['payment_method'])) {
                                            if ($postvalue['payment_method'] == 'L') {
                                                echo 'checked=checked';
                                            }
                                        } else {
                                            if (isset($paypal_payment_settings[0]['payment_method']) && $paypal_payment_settings[0]['payment_method'] == 'L') {
                                                echo 'checked=checked';
                                            }
                                        } ?>>
                                            <label for="payment_method"><?php echo 'Live Mode'; ?></label>
                                        </div>
                                        <?php if (isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('payment_method', $errors)) {
                                            echo "<span class='error'>" . ucfirst($errors['payment_method']) . "</span>";
                                        } ?>
                                    </div>
                                </div>
                                <div class="form_group">
                                    <label class="small_control_label"><?php echo __('payment_gateway_status'); ?></label><span class="star">*</span>
                                    <div class="pay_method">
                                        <div class="radio_primary_small">
                                            <input type="radio" name="payment_gateway_status" id="payment_gateway_status" value="A" <?php if (isset($postvalue['payment_gateway_status'])) {
                                        if (isset($_POST['submit_editpayment']) && $postvalue['payment_gateway_status'] == 'A') {
                                            echo 'checked=checked';
                                        }
                                    } else {
                                        if (isset($paypal_payment_settings[0]['payment_status']) && $paypal_payment_settings[0]['payment_status'] == 'A') {
                                            echo 'checked=checked';
                                        }
                                    } ?> >
                                            <label for="payment_gateway_status"><?php echo __('active'); ?></label>
                                        </div>
                                        <div class="radio_primary_small">
                                            <input type="radio" name="payment_gateway_status" id="payment_gateway_status" value="I" <?php if (isset($postvalue['payment_gateway_status'])) {
                                        if (isset($_POST['submit_editpayment']) && $postvalue['payment_gateway_status'] == 'I') {
                                            echo 'checked=checked';
                                        }
                                    } else {
                                        if (isset($paypal_payment_settings[0]['payment_status']) && $paypal_payment_settings[0]['payment_status'] == 'I') {
                                            echo 'checked=checked';
                                        }
                                    } ?>>
                                            <label for="payment_gateway_status"><?php echo __('inactive'); ?></label>
                                        </div>
                                <?php if (isset($_POST['submit_editpayment']) && isset($errors) && array_key_exists('payment_gateway_status', $errors)) {
                                    echo "<span class='error'>" . ucfirst($errors['payment_gateway_status']) . "</span>";
                                } ?>
                                    </div>
                                </div>
                                <?php
$checked = '';
if (isset($paypal_payment_settings[0]['default_payment_gateway']) && $paypal_payment_settings[0]['default_payment_gateway'] == 1) {
    $checked = 'checked';
}
?>

                                <div class="checkbox_custom">
                                    <input id="check_default" name="check_default" type="checkbox" name="check_default" <?php echo $checked; ?> value="1"/>
                                    <label for="check_default"><?php echo __('default_payment_gateway'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="sms_bot_sec">
                            <div class="align_right">
                                <input class="common_butt" type="submit" name="submit_editpayment" title ="<?php echo __('activate'); ?>" value="<?php echo __('activate'); ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="rgt_lay sms_lay mt20" id="alternate_payment">
                <div class="sms_top_sec">
                    <div class="staff_detail">
                        <h2 class="comm_tit"><?php echo __('alternative_payments'); ?></h2>
                        <p class="comm_dec"><?php echo __('alternative_payments_info'); ?></p>
                        <div class="div_lft">
                            <div class="small_sel">
                                <form method="POST" enctype="multipart/form-data" class="form" id="gateways_details" name="gateways_details" action=" #alternate_payment">
                                    <?php
                                    //if(isset($postvalue['alternative_gateways_type']) && $postvalue['alternative_gateways_type']==2){
                                    $payment_gateway_id = '';
                                    if (isset($postvalue['payment_gateway_type'])) {
                                        $payment_gateway_id = $postvalue['payment_gateway_type'];
                                        $selected = "selected='selected'";
                                    } else {
                                        $selected = "";
                                    }
                                    echo $payment_gateway_list;
                                    ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                 <?php
                 
                 if(isset($errors)){foreach ($errors as $error){ echo "<p class='error payment_error'>".ucfirst($error)."</p>";}}
                 
                 ?>
                <form method="POST" enctype="multipart/form-data" class="form" id="alternative_gateways_details" action="alternative_gateways_details#alternate_payment" >
                    <div class="sms_sett_opt"  id="alternate_acc">
                        <div class="sms_mid_sec">
                            <div class="sms_mid_lft">                            
                                <?php
                                
                                $count_form_top_fields = count($form_top_fields);
                                if ($count_form_top_fields > 0) {
                                    foreach ($form_top_fields as $fields) {

                                        echo $fields;
                                    }
                                }
                               
                                ?>
                            </div>
                            <div class="sms_mid_rgt">    
                                <div class="alternate_test_mode">


                    <?php
                   
                    $count_form_fields = count($form_fields);
                    if ($count_form_fields > 0) {
                        foreach ($form_fields as $fields) {

                            echo $fields;
                        }
                    }
                    
                    ?>

                                </div>
                                <div class="alternate_live_mode">


                    <?php
                    
                    $count_form_live_fields = count($form_live_fields);
                    if ($count_form_live_fields > 0) {
                        foreach ($form_live_fields as $fields) {

                            echo $fields;
                        }
                    }
                    
                    ?>

                                </div>

                    <?php
                    
                    $count_form_bottom_fields = count($form_bottom_fields);
                    if ($count_form_bottom_fields > 0) {
                        foreach ($form_bottom_fields as $fields) {
                            echo $fields;                    
                            
                        }
                    }
                     
                    
                    ?>
                            </div>
                        </div>
                        <?php if($count_form_bottom_fields>0){ ?>
                        <div class="sms_bot_sec">
                            <div class="align_right">                           
                                <input class="common_butt" type="submit" name="submit_edit_alternate_payment" title ="<?php echo __('button_save'); ?>" value="<?php echo __('button_save'); ?>" />
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    
                </form>

            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var hash = window.location.hash.substr(1);
<?php if (!isset($payment_settings[0]['payment_method'])||$payment_settings[0]['payment_method'] == 'T') { ?>
            $(".live_mode").hide();
            $(".test_mode").show();
<?php } else { ?>
            $(".test_mode").hide();
            $(".live_mode").show();
<?php } ?>
<?php

if(!isset($_POST['submit_edit_alternate_payment'])){
            
if (!isset($payment_settings[0]['payment_method'])||$payment_settings[0]['payment_method'] == 'T') { ?>
            $(".alternate_live_mode").hide();
            $(".alternate_test_mode").show();
<?php } else { ?>
            $(".alternate_test_mode").hide();
            $(".alternate_live_mode").show();
<?php }
}else{
    if(isset($_POST['payment_method'])&& $_POST['payment_method']=='T'){?>
            $(".alternate_live_mode").hide();
                   $(".alternate_test_mode").show();
<?php } else { ?>
            $(".alternate_test_mode").hide();
            $(".alternate_live_mode").show();
<?php }
 } ?>
<?php if (isset($errors) && (array_key_exists('live_payment_gateway_username', $errors) || array_key_exists('live_payment_gateway_password', $errors) || array_key_exists('live_payment_gateway_signature', $errors))) { ?>
            $(".test_mode").hide();
            $(".live_mode").show();
            $(".alternate_test_mode").hide();
            $(".alternate_live_mode").show();
<?php } ?>
        var payment_gateway_type = $(".payment_gateway_type option:selected").val();
        if (payment_gateway_type == 1) {
            $("#default_acc").show();
        }
        var alternative_gateways_type = $(".alternative_gateways_type option:selected").val();
        if (alternative_gateways_type == 2) {
            $("#alternate_acc").show();
        }
<?php ?>
    });

    function gatewaySelected(param) {
        if (param == 1) {
            $("#default_acc").show();
        } else {
            $("#default_acc").hide();
        }
    }

    function alternative_gateways_types(param) {
        if (param == 2) {
            $("#alternate_acc").show();
        } else {
            $("#alternate_acc").hide();
        }
    }

    function change_payment_option(val)
    {
        if (val == "T") {
            $(".alternate_live_mode").hide();
            $(".alternate_test_mode").show();
            $(".live_mode").hide();
            $(".test_mode").show();
        } else {
            $(".alternate_test_mode").hide();
            $(".alternate_live_mode").show();
            $(".test_mode").hide();
            $(".live_mode").show();
        }
    }
</script>