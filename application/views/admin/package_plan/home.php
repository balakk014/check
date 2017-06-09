<?php
defined('SYSPATH') OR die("No direct access allowed.");
$account_name = $admin_info['name'];
?>
<div class="new_home_det">
    <div class="home_top_banner">
        <div class="home_greeting_content">
            <p class="home_greeting_heading"><?php echo __('Welcome_to') . ' ' . CLOUD_SITENAME . ',' . $account_name; ?>.</p>
        </div>
    </div>
    <div class="page_content">
        <div class="inner">
            <?php
            $panel_add_class = "";
            if (PACKAGE_TYPE != 0) {
                $panel_add_class = "";
            }
            ?>
            <div class="panel_head <?php echo $panel_add_class; ?>">                
                <div class="small_dot"><p class="blue_dot">&nbsp;</p></div>
                <div class="notification_title"><p class="home_notification_title">
                        <?php                        
                        if (PACKAGE_TYPE == 0) {
                            $str_message = __('free_trial');
                        } else if (PACKAGE_TYPE == 1) {
                            $str_message = __('basic') .   __("plan_label"); 
                        } else if (PACKAGE_TYPE == 2) {
                            $str_message = __('plantinum') . __("plan_label"); 
                        } else if (PACKAGE_TYPE == 3) {
                            $str_message = __('enterprise') . __("plan_label"); 
                        }
                        if (!empty(EXPIRY_TIME) && EXPIRY_TIME < strtotime(CURRENT_TIMEZONE_DATE)) {
                            $expiry_day_message = str_replace('##plan_name##', $str_message, __('expired_message'));
                        } else {
                            $str_trail_expiry_days = str_replace('+', '', TRIAL_EXPIRY_DAYS);
                            if (TRIAL_EXPIRY_DAYS == 0) {
                                $expiry_day_message = str_replace('##plan_name##', $str_message, __('expired_message_today'));
                            } else {                                
                                $expiry_days = str_replace('##expiry_days##', $str_trail_expiry_days, __('trial_expiry_days'));
                                $expiry_day_message = str_replace('##plan_name##', $str_message, $expiry_days);
                            }
                        }
                        echo $expiry_day_message;
                        ?>
                    </p></div>

                <div class="action_butt"><div class="action_butt"><?php if (PACKAGE_TYPE != 3) { ?><a class="small_butt" href="<?php echo URL_BASE . 'package/account_plan'; ?>" title="<?php echo __('select_aplan'); ?>"><?php echo __('select_aplan'); ?></a><?php } ?></div>
            </div>
        </div>
        <div class="skeleton_feed">
            <div class="inner">
                <div class="feed_item">
                    <div class="home_card_content">
                        <div class="home_card_content_title"><h3 class="comm_tit"><?php echo __('add_product'); ?></h3></div>
                        <div class="home_card_content_wrapper">
                            <p class="home_card_content_message"><?php echo __('home_card_content_msg'); ?></p>
                            <div class="home_card_content_button_wrapper"><a href="<?php echo URL_BASE . 'add/driver'; ?>" class="btn_primary" title="<?php echo __('add_driver'); ?>"><?php echo __('add_driver'); ?></a></div>
                        </div>
                    </div>
                </div>
                <!--                <div class="feed_item">
                                    <div class="home_card_content">
                                        <div class="home_card_content_title"><h3 class="comm_tit"><?php echo __('home_card_content_title'); ?></h3></div>
                                        <div class="home_card_content_wrapper">
                                            <p class="home_card_content_message"><?php echo __('home_card_content_message'); ?></p>
                                            <div class="home_card_content_button_wrapper"><a href="#" class="btn_primary" title="<?php echo __('home_customize_theme'); ?>"><?php echo __('home_customize_theme'); ?></a></div>
                                        </div>
                                    </div>
                                </div>-->
                <div class="feed_item">
                    <div class="home_card_content">
                        <div class="home_card_content_title"><h3 class="comm_tit"><?php echo __('add_domain_brand'); ?></h3></div>
                        <div class="home_card_content_wrapper">
                            <p class="home_card_content_message"><?php echo __('current_domain'); ?> <strong><?php echo URL_BASE; ?></strong> . <?php echo __('current_domain_desc'); ?></p>
                            <div class="home_card_content_button_wrapper"><a href="<?php echo URL_BASE;?>package/add_domain" class="btn_primary" title="<?php echo __('add_domain'); ?>"><?php echo __('add_domain'); ?></a></div>
                        </div>
                    </div>
                </div>
                <div class="feed_item">
                    <div class="home_card_content">
                        <div class="home_card_content_title"><h3 class="comm_tit"><?php echo __('add_own_lang'); ?></h3></div>
                        <div class="home_card_content_wrapper">
                            <p class="home_card_content_message"><?php echo __('current_lang'); ?> <strong><?php echo __('english_label'); ?></strong>.</p>
                            <div class="home_card_content_button_wrapper"><a href="<?php echo URL_BASE; ?>package/preferences" class="btn_primary" title="<?php echo __('choose_lang'); ?>"><?php echo __('choose_lang'); ?></a></div>
                        </div>
                    </div>
                </div>
                <div class="feed_item">
                    <div class="home_card_content">
                        <div class="home_card_content_title"><h3 class="comm_tit"><?php echo __('select_your_payment_gateway'); ?></h3></div>
                        <div class="home_card_content_wrapper">
                            <p class="home_card_content_message"><?php echo __('current_payment_gateway'); ?> <strong><?php echo $get_payment_gateway_info; ?></strong>.</p>
                            <div class="home_card_content_button_wrapper"><a href="<?php echo URL_BASE; ?>package/payments" class="btn_primary" title="<?php echo __('choose_payment'); ?>"><?php echo __('choose_payment'); ?></a></div>
                        </div>
                    </div>
                </div>
                <!--            <div class="feed_item">
                                <div class="home_card_content">
                                    <div class="home_card_content_title sales_chanel"><h3 class="comm_tit"><?php echo __('find_more_way_tosell'); ?></h3></div>
                                    <div class="ui_stack">
                                        <div class="ui_stack_list">
                                            <div class="ui_stack_img"><img height="25" src="<?php echo URL_BASE; ?>public/admin/images/facebook.png"/></div>
                                            <div class="ui_stack_det"><p><?php echo __('sell_on_facebook'); ?></p></div>
                                            <div class="action_butt"><a href="#" class="btn_primary" title="<?php echo __('add_facebook'); ?>"><?php echo __('add_facebook'); ?></a></div>
                                        </div>
                                        <div class="ui_stack_list">
                                            <div class="ui_stack_img"><img height="25" src="<?php echo URL_BASE; ?>public/admin/images/buy-button.png"/></div>
                                            <div class="ui_stack_det"><p><?php echo __('add_products_toan_existing_website'); ?></p></div>
                                            <div class="action_butt"><a href="#" class="btn_primary" title="<?php echo __('add_buy_button'); ?>"><?php echo __('add_buy_button'); ?></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                <div class="feed_item">
                    <div class="home_card_content">
                        <div class="home_card_content_title"><h3 class="comm_tit"><?php echo __('bookmark_store'); ?></h3></div>
                        <div class="home_card_content_wrapper book_mark">
                            <input  type="text" class="form_control" value="<?php echo URL_BASE; ?>package/home" readonly />
                            <p class="home_card_content_message"><?php echo __('press'); ?> <span class="keyboard_key s_none"><?php echo __('Ctrl'); ?></span> <?php echo __('plus_symbol'); ?> <span class="keyboard_key s_none"><?php echo __('D'); ?></span> <?php echo __('tobookmark_your'); ?> <?php echo CLOUD_SITENAME; ?> <?php echo __('admin'); ?>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
