<?php
defined('SYSPATH') OR die("No direct access allowed.");
$company_timezone = TIMEZONE;
?>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/validation/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/jstz.min.js"></script>


<div class="page_loader" id="loading" style="display: none"><div class="load_img"><img src="<?php echo URL_BASE;?>public/cloud_package/loader.gif"></div></div>
<div class="profile_out">
    <div class="profile_inner">
        <div class="profile_bg">   
            <form method="post" id="editprofile" name="editprofile" action ="">
                <?php // echo ucfirst(__('personalinform')); ?>
                <div class="profile_header"><h1><?php echo __('force_profile_title'); ?></h1></div>
                <div class="profile">

                    <div class="profile_form">
                        <div class="form_group">
                            <?php
                            if (isset($login_detail[0]['name']) && !array_key_exists('firstname', $postvalue)) {
                                $name = $login_detail[0]['name'];
                            } else {
                                if (isset($postvalue['firstname'])) {
                                    $name = $postvalue['firstname'];
                                } else {
                                    $name = "";
                                }
                            }
                            ?>     
                            <div class="div_lft">
                                <label class="control_label"><?php echo __('firstname'); ?><span class="star">*</span></label>
                                <input type="text" title="<?php echo __('enterfirstname'); ?>" name="firstname" id="firstname" class="required form_control" value="<?php
                                if (isset($postvalue) && array_key_exists('firstname', $postvalue)) {
                                    echo $postvalue['firstname'];
                                } else {
                                    echo $name;
                                }
                                ?>"  minlength="2" maxlength="50" />
                                       <?php
                                       if (isset($errors) && array_key_exists('firstname', $errors)) {
                                           echo "<span class='error'>" . ucfirst($errors['firstname']) . "</span>";
                                       }
                                       ?>
                            </div>
                            <?php
                            if (isset($login_detail[0]['lastname']) && !array_key_exists('lastname', $postvalue)) {
                                $lastname = $login_detail[0]['lastname'];
                            } else {
                                if (isset($postvalue['lastname'])) {
                                    $lastname = $postvalue['lastname'];
                                } else {
                                    $lastname = "";
                                }
                            }
                            ?> 
                            <div class="div_rgt">
                                <label class="control_label"><?php echo __('lastname'); ?><span class="star">*</span></label>
                                <input type="text" title="<?php echo __('enterlastname'); ?>" name="lastname" class="required form_control" id="lastname" value="<?php
                                if (isset($postvalue) && array_key_exists('lastname', $postvalue)) {
                                    echo $postvalue['lastname'];
                                } else {
                                    echo $lastname;
                                }
                                ?>" minlength="2"  maxlength="50" />
                                       <?php
                                       if (isset($errors) && array_key_exists('lastname', $errors)) {
                                           echo "<span class='error'>" . ucfirst($errors['lastname']) . "</span>";
                                       }
                                       ?>
                            </div>
                        </div>
                        <div class="form_group">
                            <?php
                            if (isset($login_detail[0]['email']) && !array_key_exists('email', $postvalue)) {
                                $email = $login_detail[0]['email'];
                            } else {
                                if (isset($postvalue['email'])) {
                                    $email = $postvalue['email'];
                                } else {
                                    $email = "";
                                }
                            }
                            ?>  
                            <div class="div_lft">
                                <label class="control_label"><?php echo __('email'); ?><span class="star">*</span></label>
                                <input type="text"  maxlength="30" minlength="3"  title="<?php echo __('enteremailaddress'); ?>" class="required email form_control" id="email" name="email" value="<?php
                                       if (isset($postvalue) && array_key_exists('email', $postvalue)) {
                                           echo $postvalue['email'];
                                       } else {
                                           echo $email;
                                       }
                                       ?>" />
                            <?php
                            if (isset($errors) && array_key_exists('email', $errors)) {
                                echo "<span class='error'>" . ucfirst($errors['email']) . "</span>";
                            }
                            ?>
                            </div>
                            <?php
                            if (isset($login_detail[0]['phone']) && !array_key_exists('phone', $postvalue)) {
                                $phone = $login_detail[0]['phone'];
                            } else {
                                if (isset($postvalue['phone'])) {
                                    $phone = $postvalue['phone'];
                                } else {
                                    $phone = "";
                                }
                            }
                            ?>  
                            <div class="div_rgt">
                                <label class="control_label"><?php echo __('mobile'); ?><span class="star">*</span></label>
                                <div class="input-mob-box mobile_no">	
                                    <input type="text"  maxlength="30" minlength="7" class="required form_control" title="<?php echo __('entermobileno'); ?>" id="phone" name="phone" value="<?php
                                    if (isset($postvalue) && array_key_exists('phone', $postvalue)) {
                                        echo $postvalue['phone'];
                                    } else {
                                        echo $phone;
                                    }
                                    ?>" />   

                        <?php
                        if (isset($errors) && array_key_exists('phone', $errors)) {
                            echo "<span class='error'>" . ucfirst($errors['phone']) . "</span>";
                        }
                        ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        if (isset($login_detail[0]['address']) && !array_key_exists('address', $postvalue)) {
                            $address = $login_detail[0]['address'];
                        } else {
                            if (isset($postvalue['address'])) {
                                $address = $postvalue['address'];
                            } else {
                                $address = "";
                            }
                        }
                        ?>  
                        <div class="form_group">
                            <label class="control_label"><?php echo __('address'); ?><span class="star">*</span></label>
                            <textarea name="address" id="address" class="required form_control" title="<?php echo __('enteraddress'); ?>" rows="7" cols="35"><?php
                            if (isset($postvalue) && array_key_exists('address', $postvalue)) {
                                echo $postvalue['address'];
                            }
                        ?></textarea>
                                <?php
                                       if (isset($errors) && array_key_exists('address', $errors)) {
                                           echo "<span class='error'>" . $errors['address'] . "</span>";
                                       }
                                       ?>
                        </div>
                        <div class="form_group">
                            <div class="div_lft">
                                <label class="control_label"><?php echo __('iso_code'); ?><span class="star">*</span></label>
                                <input class="required form_control" type="text"  maxlength="5" minlength="2"  title="<?php echo __('entertheisocountrycode'); ?>" id="iso_country_code" name="iso_country_code" value="<?php
                                       if (isset($postvalue) && array_key_exists('iso_country_code', $postvalue)) {
                                           echo $postvalue['iso_country_code'];
                                       }
                                       ?>" />
<?php
if (isset($errors) && array_key_exists('iso_country_code', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['iso_country_code']) . "</span>";
}
?>
                            </div>
                            <div class="div_rgt">
                                <label class="control_label"><?php echo __('telephone_code'); ?><span class="star">*</span></label>
                                <input class="form_control" type="text"  maxlength="7" minlength="2"  title="<?php echo __('enterthetelecode'); ?>" id="telephone_code" name="telephone_code" value="<?php
                                       if (isset($postvalue) && array_key_exists('telephone_code', $postvalue)) {
                                           echo $postvalue['telephone_code'];
                                       }
                                       ?>" />
<?php
if (isset($errors) && array_key_exists('telephone_code', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['telephone_code']) . "</span>";
}
?>
                            </div>
                        </div>
                        <div class="form_group">
                            <div class="div_lft">
                                <label class="control_label"><?php echo __('currency_code'); ?><span class="star">*</span></label>
                                <input class="required form_control" type="text"  maxlength="5" minlength="2"  title="<?php echo __('enter_currency_code'); ?>" id="currency_code" name="currency_code" value="<?php
                            if (isset($postvalue) && array_key_exists('currency_code', $postvalue)) {
                                echo $postvalue['currency_code'];
                            }
                            ?>" />
                            <?php
                            if (isset($errors) && array_key_exists('currency_code', $errors)) {
                                echo "<span class='error'>" . ucfirst($errors['currency_code']) . "</span>";
                            }
                            ?>
                            </div>
                            <div class="div_rgt">
                                <label class="control_label"><?php echo __('currency_symbol'); ?><span class="star">*</span></label>
                                <input class="required form_control" type="text"  maxlength="5"  title="<?php echo __('enter_currency_symbol'); ?>" id="currency_symbol" name="currency_symbol" value="<?php
                                if (isset($postvalue) && array_key_exists('currency_symbol', $postvalue)) {
                                    echo $postvalue['currency_symbol'];
                                }
                                ?>" />
                                <?php
                                       if (isset($errors) && array_key_exists('currency_symbol', $errors)) {
                                           echo "<span class='error'>" . ucfirst($errors['currency_symbol']) . "</span>";
                                       }
                                       ?>
                            </div>
                        </div>
                        <div class="form_group">
<?php
if (isset($login_detail[0]['login_city']) && !array_key_exists('city', $postvalue)) {
    $login_city = $login_detail[0]['login_city'];
} else {
    if (isset($postvalue['city'])) {
        $login_city = $postvalue['city'];
    } else {
        $login_city = "";
    }
}
?>    
                            <div class="div_lft">
<?php $field_type_city = $login_city; ?>
                                <label class="control_label"><?php echo __('city_label'); ?><span class="star">*</span></label>
                            <?php
                            $city_name = '';
                            ?>
                                <input type="text" class="required form_control" maxlength="50" minlength="2"  name="city" id="city" value="<?php
                            if (isset($postvalue) && array_key_exists('city', $postvalue)) {
                                echo $postvalue['city'];
                            }
                            ?>" title="<?php echo __('entercity'); ?>">
                            <?php
                            if (isset($errors) && array_key_exists('city', $errors)) {
                                echo "<span class='error'>" . ucfirst($errors['city']) . "</span>";
                            }
                            ?>
                            </div>
                            <div class="div_rgt">
                                <label class="control_label"><?php echo __('zip_postal_code'); ?><span class="star">*</span></label>
                                <input class="required form_control" type="text"  maxlength="15" minlength="2"   value="<?php
                            if (isset($postvalue) && array_key_exists('postal_code', $postvalue)) {
                                echo $postvalue['postal_code'];
                            }
                            ?>" name="postal_code" id="postal_code" title="<?php echo __('enterpostalcode'); ?>">
                                        <?php
                                        if (isset($errors) && array_key_exists('postal_code', $errors)) {
                                            echo "<span class='error'>" . ucfirst($errors['postal_code']) . "</span>";
                                        }
                                        ?>
                            </div>


                        </div>

                        <div class="form_group">
                                <?php
                                if (isset($login_detail[0]['login_country']) && !array_key_exists('country', $postvalue)) {
                                    $country = $login_detail[0]['login_country'];
                                } else {
                                    if (isset($postvalue['country'])) {
                                        $country = $postvalue['country'];
                                    } else {
                                        $country = "";
                                    }
                                }
                                ?>  
                            <div class="div_lft">
                            <?php $field_type = $country; ?>
                                <label class="control_label"><?php echo __('country_label'); ?><span class="star">*</span></label>
                                <div class="sel_box">
                                    <select name="country" id="country" class="required form_control" title="<?php echo __('select_the_country'); ?>">
                                        <option value=""><?php echo __('select_label'); ?></option>
                                <?php foreach ($all_country_list as $key => $country_list) {
                                    ?>
                                            <option value="<?php echo $country_list; ?>" <?php
                                    if ($field_type == $country_list) {
                                        echo 'selected=selected';
                                    }
                                    ?>><?php echo ucfirst($country_list); ?></option>
                                       <?php } ?>
                                    </select>
                                </div>

                                       <?php
                                       if (isset($errors) && array_key_exists('country', $errors)) {
                                           echo "<span class='error'>" . ucfirst($errors['country']) . "</span>";
                                       }
                                       ?>

                            </div>
                                        <?php
                                        if (isset($login_detail[0]['login_state']) && !array_key_exists('state', $postvalue)) {
                                            $state = $login_detail[0]['login_state'];
                                        } else {
                                            if (isset($postvalue['state'])) {
                                                $state = $postvalue['state'];
                                            } else {
                                                $state = "";
                                            }
                                        }
                                        ?>    
                            <div class="div_rgt">
                                        <?php $field_type = $state; ?>
                                <label class="control_label"><?php echo __('state_label'); ?><span class="star">*</span></label>
                                        <?php
                                        $state_name = '';
                                        ?>
                                <input type="text" class="required form_control" maxlength="50" minlength="2" name="state" id="state" value="<?php
                                        if (isset($postvalue) && array_key_exists('state', $postvalue)) {
                                            echo $postvalue['state'];
                                        }
                                        ?>" title="<?php echo __('enterstate'); ?>">
<?php
if (isset($errors) && array_key_exists('state', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['state']) . "</span>";
}
?>
                            </div>
                        </div>
                        <div class="form_group">
                            <div class="div_lft">
                                <label class="control_label"><?php echo __('selected_timezone'); ?><span class="star">*</span></label>
                                <div class="sel_box">
                                    <select class="required form_control" name="user_time_zone" id="user_time_zone" title="<?php echo __('select_time_zone'); ?>" >
<?php
$timezone = unserialize(SELECT_TIMEZONE);

if (isset($user_timezone) && !array_key_exists('user_time_zone', $postvalue)) {
    $timezone_user = '';
} else {
    if (isset($postvalue['user_time_zone'])) {
        $timezone_user = $postvalue['user_time_zone'];
    } else {
        $timezone_user = "";
    }
}
echo '<option value="" >--Select--</option>';
foreach ($timezone as $key => $value) {
    ?>
                                            <option value="<?php echo $value; ?>" <?php
    if ($timezone_user == $value) {
        echo 'selected=selected';
    }
    ?> ><?php echo ucfirst($value); ?></option>
<?php } ?>
                                    </select>
                                </div>
                                <span class="error"><?php echo isset($errors['user_time_zone']) ? $errors['user_time_zone'] : ''; ?></span>
                            </div>
                            <div class="div_rgt">
                                <label class="control_label"><?php echo __('website_info'); ?></label>
                                <input class="form_control required" type="text" minlength="1" maxlength="60" value="<?php echo $business_name; ?>" name="website_info" id="website_info" readonly="readonly">
                            </div>
                        </div>
                        <label class="star">*<?php echo __('required_label'); ?></label>
                    </div>

                </div>
                <div class="profile_bot_det">       
                    <div class="enter_store"><input class="common_butt" type="submit" <?php if ($email == SUPERADMIN_EMAIL) { ?> id="disable" <?php } ?> value="<?php echo __('submit'); ?>" name="submit_editprofile" title="<?php echo __('submit'); ?>" onclick="return check_profile_confirmation();"/></div>
                </div>
                
                
            </form>
        </div>
        <?php 
        $telephone_code= isset($login_credentials->telephone_code)?$login_credentials->telephone_code:'';
        ?>
        <div class="website_details">
                    <div class="website_det">
                        <h3><?php echo __('website_details'); ?></h3>
                        <ul>
                            <li><label><?php echo __('website_url'); ?></label><p><?php echo URL_BASE;?></p></li>
                            <li><label><?php echo __('admin_url'); ?></label><p><?php echo URL_BASE.'admin/login';?></p></li>
                            <li><label><?php echo __('admin_email'); ?></label><p><?php echo isset($login_credentials->email)?$login_credentials->email:''?></p></li>
                            <li><label><?php echo __('admin_password'); ?></label><p><?php echo isset($login_credentials->password)?$login_credentials->password:'';?></p></li>
                        </ul>
                    </div>
                    <div class="website_det">
                        <h3><?php echo __('passenger_driver_info'); ?></h3>
                        <ul>
                            <li><label><?php echo __('passenger_mobileno'); ?></label><p><?php echo isset($login_credentials->passengerphonenumber)?$telephone_code.'-'.$login_credentials->passengerphonenumber:'';?></p></li>
                           
                            <li><label><?php echo __('driver_mobileno'); ?></label><p><?php echo isset($login_credentials->driverphonenumber)?$login_credentials->driverphonenumber:'';?></p></li>
                             <li><label><?php echo __('passenger_password'); ?></label><p><?php echo isset($login_credentials->passengerpassword)?$login_credentials->passengerpassword:'';?></p></li>
                            <li><label><?php echo __('driver_password'); ?></label><p><?php echo isset($login_credentials->driverpassword)?$login_credentials->driverpassword:'';?></p></li>
                        </ul>
                    </div>
            <div class="enter_store resend_email"><input class="common_butt" type="button" value="<?php echo __('resend_email'); ?>" title="<?php echo __('resend_email'); ?>" onclick="resend_email();"/></div>
                </div>
        
    </div>    
</div>
<script type="text/javascript">
    $(document).ready(function () {
        //Time zone detection and selected 
<?php if (!isset($postvalue['user_time_zone'])) { ?>
            var timezone = jstz.determine();
            var timezone_name = timezone.name();

            $('#user_time_zone option').each(function () {
                var user_timezone = $(this).attr('value');
                if (user_timezone == timezone_name) {
                    $(this).attr('selected', true);
                }
            });
<?php } ?>


        $.validator.addMethod(
                "regex",
                function (value, element, regexp) {
                    if (regexp.constructor != RegExp)
                        regexp = new RegExp(regexp);
                    else if (regexp.global)
                        regexp.lastIndex = 0;
                    return this.optional(element) || regexp.test(value);
                },
                "<?php echo __('please_check_your_input'); ?>"
                );

        $('#editprofile').validate(
                {
                    rules:
                            {

                                telephone_code:
                                        {
                                            required: true,
                                            regex: /^[0-9\s]+$/
                                        },
                            }
                });
        $("#phone").keyup(function () {
            //to allow left and right arrow key move
            if (event.which >= 37 && event.which <= 40)
            {
                return false;

            }
            this.value = this.value.replace(/[`~!@#$%^&*()\s_|\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
        });

        $("#telephone_code").keyup(function () {
            //to allow left and right arrow key move
            if (event.which >= 37 && event.which <= 40)
            {
                return false;

            }
            this.value = this.value.replace(/[`~!@#$%^&*()\s_|\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');

        });

    });

    function check_profile_confirmation() {
        var strconfirm = confirm("<?php echo __('confirm_update'); ?>");
        if (strconfirm == true) {
            return true;
        } else {
            return false;
        }
    }

function resend_email(){
    var email_id=$('#email').val();
    if(email_id==''){
        alert('<?php echo __("enter_the_email"); ?>');
        return false;
    }
     var strconfirm = confirm("<?php echo __('are_yousure_resendmail_login_detailfor'); ?>"+email_id);
      if (strconfirm == true) {
          $('#loading').show();
          $.ajax({
			url:"<?php echo URL_BASE;?>package/resendmail",
			type:"post",
			data:"email="+email_id,
			success:function(data){
                            
                         $('#loading').hide();                                                       
                         if(data==1){
                            alert('<?php echo __("mail_sent_successfully_to"); ?> '+email_id);
                            return true;
                         }
                         alert('<?php echo __("mailnotsent_please_tryagain_after_sometime"); ?>')
                             return false;
			
			},
			error:function(data)
			{
				alert(data);
			}
		});
            return true;
        } else {
            return false;
        }
         
}
</script>
