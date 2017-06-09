<?php defined('SYSPATH') OR die("No direct access allowed."); ?>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
        <div class="content_middle">    
            <div class="driverinfo_common">
                <h2 class="tab_sub_tit"><?php echo ucfirst(__('personalinform')); ?></h2>
                <ul>
                    <li><label><?php echo __('firstname'); ?></label>
                        <?php if (isset($user_details[0]['name'])) {
                            echo $user_details[0]['name'];
                        } else {
                            echo '';
                        } ?>
                    </li> 	

                    <li><label><?php echo __('lastname'); ?></label>
                        <?php if (isset($user_details[0]['lastname'])) {
                            echo $user_details[0]['lastname'];
                        } ?>
                    </li> 

                    <li><label><?php echo __('email'); ?></label>
<?php if (isset($user_details[0]['email'])) {
    echo $user_details[0]['email'];
} ?>
                    </li> 

                    <li><label><?php echo __('mobile'); ?></label>
                        <?php if (isset($user_details[0]['phone'])) {
                            echo $user_details[0]['country_code']." ".$user_details[0]['phone'];
                        } ?>
                    </li>                       		   

                    <li><label><?php echo __('address'); ?></label>
<?php if (isset($user_details[0]['address'])) {
    echo $user_details[0]['address'];
} ?>
                    </li>
<?php if ($user_details[0]['user_type'] != 'N' && $user_details[0]['user_type'] != 'S') { ?>        
                        <li><label><?php echo __('date_of_birth'); ?></label>
                            <?php if (isset($user_details[0]['dob']) && $user_details[0]['dob']!='0000-00-00') {
                                echo $user_details[0]['dob']; //Commonfunction::getDateTimeFormat($user_details[0]['dob'], 2);
                            } ?>
                        </li> 
                        <?php } ?>
                        <li><label><?php echo __('country_label'); ?></label>
<?php if (isset($user_details[0]['country_name'])) {
    echo $user_details[0]['country_name'];
} ?>
                    </li>

<?php if ($user_details[0]['user_type'] != 'S') { ?>
                        <li><label><?php echo __('state_label'); ?></label>
    <?php if (isset($user_details[0]['state_name'])) {
        echo $user_details[0]['state_name'];
    } ?>
                        </li>

                        <li><label><?php echo __('city_label'); ?></label>
    <?php if (isset($user_details[0]['city_name'])) {
        echo $user_details[0]['city_name'];
    } ?>
                        </li>
                </ul>

<?php if ($user_details[0]['user_type'] != 'N' && $user_details[0]['user_type'] != 'S') { ?>   

                    <h2 class="tab_sub_tit"><?php echo ucfirst(__('companyinformation')); ?></h2>

                    <ul>

                        <li><label><?php echo __('companyname'); ?></label>
                            <?php if (isset($user_details[0]['company_name'])) {
                                echo $user_details[0]['company_name'];
                            } ?>
                        </li>  

                        <li><label><?php echo __('companyaddress'); ?></label>
                        <?php if (isset($user_details[0]['company_address'])) {
                            echo $user_details[0]['company_address'];
                        } ?>
                        </li>  	          
<?php } ?>

                    
<?php } ?>

                </ul>

            </div>
        </div>
    </div>  
