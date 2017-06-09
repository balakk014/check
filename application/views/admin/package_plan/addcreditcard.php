<?php
defined('SYSPATH') OR die("No direct access allowed.");
$company_timezone = TIMEZONE;
//$firstname = $getadmin_profile_info['name'];
//$lastname = $getadmin_profile_info['lastname'];
$email= $getadmin_profile_info['email'];
//$address = $getadmin_profile_info['address'];
//$postal_code = $getadmin_profile_info['postal_code'];
if(!empty($billing_card_info_details)){
    $cardnumber=$billing_card_info_details[0]['cardnumber'];
    $cardnumber = encrypt_decrypt('decrypt',$cardnumber);
    $cvv=$billing_card_info_details[0]['cvv'];
    $expiry_month=$billing_card_info_details[0]['expiry_month'];
    $expiry_year=$billing_card_info_details[0]['expiry_year'];
    $expirydate=$expiry_month.'/'.$expiry_year;
    $firstname = $billing_card_info_details[0]['firstname'];
    $lastname = $billing_card_info_details[0]['lastname'];
   // $email= $billing_card_info_details[0]['email'];
    $address = $billing_card_info_details[0]['address'];    
    if(isset($billing_card_info_details[0]['postal_code']))
    {    
        $postal_code = $billing_card_info_details[0]['postal_code'];
    }else{
        $postal_code ='';
    }
    $country = $billing_card_info_details[0]['country'];
    $state = $billing_card_info_details[0]['state'];
    $city = $billing_card_info_details[0]['city'];
    
}else{
	$cardnumber='';
    $cvv='';
    $expirydate='';
    $firstname='';
    $lastname = '';   
    $address = '';
    $postal_code='';
    $country = '';
    $state = '';
    $city = '';
}
?>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/validation/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/jquery.formance.min.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/frontend/logged_in/js/awesome_form.js"></script>


<div class="account_outer">
    <form action="" method="post" name="frmaddcard" id="frmaddcard" onsubmit="return validate_billinginfo();">
    <div class="account_det_list" style="border: none;">
        <div class="account_lft_det">
            <div class="acc_tit"><h2><?php echo __('billing_info');?></h2></div>
            <div class="acc_det">
                <p><?php echo __('add_credit_card_desc');?></p>
            </div>
        </div>        
                                        
			<div class="account_rgt_det">
                <div class="rgt_lay add_crd_det">
                    <div class="form_group card_no">
                        <label><?php echo __('card_number'); ?></label>
                        <div class="crd">
                            <input class="form_control numbersonly" maxlength="20" x-autocompletetype="cc-number" type="text"  value="<?php if(isset($postvalue) && array_key_exists('cardnumber',$postvalue)){ echo $postvalue['cardnumber']; }else{ echo $cardnumber;}?>" name="cardnumber" id="cardnumber" maxlength="30"/>
                            <span class="lock"></span>
                            <em id="cardnumber_error">
                                  <?php if (isset($errors) && array_key_exists('cardnumber', $errors)) {echo "<span class='error'>" . ucfirst($errors['cardnumber']) . "</span>";}?>
							</em>
                        </div>
                    </div>
                    <div class="form_group">
                        <div class="div_lft">
                            <label><?php echo __('expires'); ?></label>
                            <input class="form_control credit_card_expiry" x-autocompletetype="cc-exp" placeholder="MM / YYYY " type="text" value="<?php if(isset($postvalue) && array_key_exists('expirydate',$postvalue)){ echo $postvalue['expirydate']; }else{ echo $expirydate;}?>" maxlength="9" onpaste="return false;" name="expirydate" id="expirydate"/>
							<em id="expirydate_error">
                            <?php if (isset($errors) && array_key_exists('expirydate', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['expirydate']) . "</span>";
}?></em>
   
                        </div>
                        <div class="div_rgt">
                            <label><?php echo __('card_verification_no'); ?><span class="info"><div class="tool_tip"><p><?php echo __('digit_security_code_info'); ?></p></div></span></label>
                            <input class="form_control numbersonly" type="text" value="<?php if(isset($postvalue) && array_key_exists('cvv',$postvalue)){ echo $postvalue['cvv']; }else{ echo $cvv;}?>" name="cvv" id="cvv" maxlength="5"/>
                            <em id="cvv_error">
                            <?php if (isset($errors) && array_key_exists('cvv', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['cvv']) . "</span>";
}?></em>
                        </div>
                    </div>
                    <div class="billing_info">
                        <h2><?php echo __('billing_address');?></h2>
                        <div class="form_group">
                            <div class="div_lft">
                                <label><?php echo __('firstname_label');?></label>
                                <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('firstName',$postvalue)){echo $postvalue['firstName'];}else{echo $firstname;} ?>" name="firstName" id="firstName" minlength="2" maxlength="50"/>
                                 <em id="firstname_error">
								 <?php if (isset($errors) && array_key_exists('firstname', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['firstName']) . "</span>";
}?></em>
                            </div>
                            <div class="div_rgt">
                                <label><?php echo __('lastname_label');?></label>
                                <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('lastname',$postvalue)){echo $postvalue['lastname'];}else{echo $lastname;} ?>" name="lastname" id="lastname" minlength="2" maxlength="50"/>
                                <em id="lastname_error">
                                 <?php if (isset($errors) && array_key_exists('lastname', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['lastname']) . "</span>";
}?></em>
                            </div>
                        </div>
                        <div class="form_group card_no">
                            <label><?php echo __('street_address');?></label>
                            <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('address',$postvalue)){echo $postvalue['address'];}else{echo $address;} ?>" name="address" id="address" minlength="2"/>
                            <em id="address_error">
								<?php if (isset($errors) && array_key_exists('address', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['address']) . "</span>";
}?></em>
                        </div>
                        <div class="form_group">
                            <div class="div_lft" id="city_list">
                                <label><?php echo __('city_label');?></label>

<input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('city',$postvalue)){echo $postvalue['city'];}else{echo $city;} ?>" name="city" id="city" minlength="2" maxlength="50"/>
   <em id="city_error"><?php if (isset($errors) && array_key_exists('city', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['city']) . "</span>";
}?></em>
                            </div>
                            <div class="div_rgt">
                                <label><?php echo __('postal_code');?></label>
                                <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('postal_code',$postvalue)){echo $postvalue['postal_code'];}else{echo $postal_code;} ?>" name="postal_code" id="postal_code" maxlength="8" minlength="2"/>
                                   <em id="postal_code_error"><?php if (isset($errors) && array_key_exists('postal_code', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['postal_code']) . "</span>";
}?></em>
   
                            </div>
                        </div>
                        <div class="form_group">
                            <div class="div_lft">
                                <label><?php echo __('country_label');?></label>
                                <div class="small_sel">
                                <select class="form_control" name="country" id="country"><option value=""><?php echo __('select_country'); ?></option>
                                    <?php
                                    if (isset($country) && !array_key_exists('country', $postvalue)) {
                                       
                                    } else {
                                        if (isset($postvalue['country'])) {
                                            $country = $postvalue['country'];
                                        } else {
                                            $country = "";
                                        }
                                    }
                                      foreach ($all_country_list as $key=>$country_list) {                         
                            ?>
                 
                                        ?>
                                        <option value="<?php echo $country_list; ?>" <?php if ($country == $country_list) {
                                        echo 'selected=selected';
                                    } ?>><?php echo ucfirst($country_list); ?></option>
                                        <?php } ?></select>
                                 <?php if (isset($errors) && array_key_exists('country', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['country']) . "</span>";
}?></div>
                            </div>
                            <div class="div_rgt" id="state_list">
                                <label><?php echo __('state_label');?></label>
                    <input class="form_control" type="text" value="<?php if(isset($postvalue) && array_key_exists('state',$postvalue)){echo $postvalue['state'];}else{echo $state;} ?>" name="state" id="state" minlength="2" maxlength="50"/>
                     <?php if (isset($errors) && array_key_exists('state', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['state']) . "</span>";
}?>
                            </div>
                        </div>

                    </div>
                     <div class="order_bot_det">
                        <p class="note_checkout"><span class="required"><?php echo __('note_label'); ?></span> : <?php echo __('business_entity_des'); ?></p>
                    </div>
                    <div class="agree_term">
                        <div class="checkbox_custom"><input type="checkbox" id="termsnew" name="terms" value="1"/>
                        <label for="termsnew"><?php echo __('iagree_with'); ?> <a href="<?php echo TERMS_URL;?>" target="_blank"><?php echo strtolower(__('terms_and_conditions')); ?></a></label>
                        </div>
                        <em id="terms_error">
                         <?php if (isset($errors) && array_key_exists('terms', $errors)) {
    echo "<span class='error'>" . ucfirst($errors['terms']) . "</span>";
}?>
</em>
                    </div>
                </div>
            </div>
                    
    </div>
    <div class="bottom_butt_sec">
        <div class="align_right">
<!--            <a href="<?php echo URL_BASE;?>package/account" class="btn_primary">Cancel</a>-->
            <input class="common_butt" type="submit" value="Confirm" name="btn_card_confirm" id="btn_card_confirm" onsubmit="return validate_payment_option_form();"/>
        </div>
    </div>
</form>
</div>
<script>
$(document).ready(function () { 
	$(".numbersonly").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
			}
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
});
	
function validate_billinginfo()
{	
	var error = 1;
	var cardnumber = $("#cardnumber").val();
		cardnumber = cardnumber.replace(/\s/g, '');		
	var cardnumber_length = cardnumber.length;
	var expirydate =  $("#expirydate").val();
	var cvv =  $("#cvv").val();
	var firstname =  $("#firstname").val();
	var lastname =  $("#lastname").val();
	var address =  $("#address").val();
	var city =  $("#city").val();
	var postal_code =  $("#postal_code").val();
	var country =  $("#country").val();
	var state =  $("#state").val();
	var terms = $('#termsnew:checkbox:checked').length;	

	if(cardnumber == "") {
		$("#cardnumber_error").html("Please enter credit card number");
		error = 0;
	} else {
		if(cardnumber_length < 16 || cardnumber_length > 20 ){
			$("#cardnumber_error").html("Card number length should be 16 to 20");
		}else{
			$("#cardnumber_error").html("");
		}
	}		
	
	if(expirydate == "") {
		$("#expirydate_error").html("Please enter expiry month & year");
		error = 0;
	} else {		
		var exp_error = expiry_validation(expirydate);
		error = (exp_error == 0) ? exp_error : error;
	}
	
	if(cvv == "") {
		$("#cvv_error").html("Please enter cvv");
		error = 0;
	} else {
		var cvv_error = cvv_validation(cvv);
		error = (cvv_error == 0) ? cvv_error : error;
	}
	
	if(firstname == "") {
		$("#firstname_error").html("Please enter first name");
		error = 0;
	} else {
		$("#firstname_error").html("");
	}
	
	if(lastname == "") {
		$("#lastname_error").html("Please enter last name");
		error = 0;
	} else {
		$("#lastname_error").html("");
	}
	
	if(address == "") {
		$("#address_error").html("Please enter address");
		error = 0;
	} else {
		$("#address_error").html("");
	}
	
	if(city == "") {
		$("#city_error").html("Please enter city");
		error = 0;
	} else {
		$("#city_error").html("");
	}
	
	if(state == "") {
		$("#state_error").html("Please enter state");
		error = 0;
	} else {
		$("#state_error").html("");
	}
	
	if(country == "") {
		$("#country_error").html("Please select country");
		error = 0;
	} else {
		$("#country_error").html("");
	}		
	
	if(postal_code == "") {
		$("#postal_code_error").html("Please enter postal code");
		error = 0;
	} else {
		var numbers = postal_code.match(/\d+/);
		if (numbers == null) {
			$("#postal_code_error").html("Please includes number in postal code");
			error = 0;
		}else{ 
			$("#postal_code_error").html("");
		}
	}	
	
	if(!terms) {
		$("#terms_error").html("Please select terms");
		error = 0;
	} else {
		$("#terms_error").html("");
	}	
	
	if(error) {
		document.frmcheckout.submit();
	} else {
		return false;
	}
}

function expiry_validation(creditcard_expiry_date){
	
	var error = 1;
	var d = new Date();
	var current_month = d.getMonth() + 1;
	current_month = (current_month <= 12) ? current_month : 1;
	var current_year = d.getFullYear();
	var data = creditcard_expiry_date.replace(/\s/g, '').split("/");
	if (parseInt(data[1]) < current_year) {
		$("#expirydate_error").html("<?php echo  __('card_year_expired'); ?>");
		error = 0;
	} else if (parseInt(data[1]) > current_year) {
		if((parseInt(data[0]) < 1 || parseInt(data[0]) > 12)) {
			$("#expirydate_error").html("<?php echo  __('card_month_expired'); ?>");
			error = 0;
		} else {
			$("#expirydate_error").html("");
		}
	} else if (parseInt(data[1]) == current_year) {
		if((parseInt(data[0]) < current_month) || (parseInt(data[0]) < 1 || parseInt(data[0]) > 12)) {
			$("#expirydate_error").html("<?php echo  __('card_month_expired'); ?>");
			error = 0;
		} else {
			$("#expirydate_error").html("");
		}
	} else {
		$("#expirydate_error").html("");
	}
	return error;
}

function cvv_validation(cvv){
	
	var error = 1;
	if(cvv.length >= 3 && cvv.length <= 4){
		$("#cvv_error").html("");
	} else {
		$("#cvv_error").html("<?php echo __("cvv_length_error"); ?>");
		error = 0;
	}
	return error;
}
</script>
