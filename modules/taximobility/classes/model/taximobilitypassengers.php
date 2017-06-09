<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Passengers Model details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityPassengers extends Model
{
    public function __construct()
    {
        $this->session         = Session::instance();
        $this->name            = $this->session->get("name");
        $this->admin_userid    = $this->session->get("passenger_id");
        $this->admin_email     = $this->session->get("email");
        $this->user_admin_type = $this->session->get("user_type");
        $Commonmodel           = Model::factory('Commonmodel');
        $this->currentdate     = $Commonmodel->getcompany_all_currenttimestamp(COMPANY_CID);
        $this->lat             = '';
        $this->lon             = '';
        if (isset($_SESSION['id']) && ($_SESSION['id'] != '')) {
            $this->lat = isset($_SESSION['ip_lati']) ? $_SESSION['ip_lati'] : LOCATION_LATI;
            $this->lon = isset($_SESSION['ip_lng']) ? $_SESSION['ip_lng'] : LOCATION_LONG;
        } else {
            $this->lat = isset($_COOKIE['c_lati']) ? $_COOKIE['c_lati'] : LOCATION_LATI;
            $this->lon = isset($_COOKIE['c_lng']) ? $_COOKIE['c_lng'] : LOCATION_LONG;
        }
        //MongoDB Instance
        $this->mongo_db = MangoDB::instance('default');
    }
    /**Validating User SignUP details**/
    /**Validating User SignUP details**/
    public function validate_signup($arr)
    {
        return Validation::factory($arr)->rule('name', 'not_empty')->rule('name', 'min_length', array(
            ':value',
            '4'
        ))->rule('name', 'max_length', array(
            ':value',
            '32'
        ))->rule('lastname', 'not_empty')->rule('lastname', 'min_length', array(
            ':value',
            '1'
        ))->rule('lastname', 'max_length', array(
            ':value',
            '32'
        ))->rule('email', 'not_empty')->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('password', 'not_empty')->rule('password', 'min_length', array(
            ':value',
            '5'
        ))->rule('password', 'max_length', array(
            ':value',
            '50'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        )) /*->rule('repassword', 'not_empty')
        ->rule('repassword', 'min_length', array(':value', '5'))
        ->rule('repassword', 'max_length', array(':value', '50'))
        ->rule('repassword',  'matches', array(':validation', 'password', 'repassword'))*/ ;
    }
    /**Validating Login Datas**/
    public function validate_login($arr)
    {
        if ($arr['password'] == 'Password') {
            $arr['password'] = "";
        }
        return Validation::factory($arr)->rule('email', 'not_empty')->rule('email', 'email')->rule('password', 'not_empty')->rule('password', 'min_length', array(
            ':value',
            '5'
        ))->rule('password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ));
    }
    public function get_passenger_profile_details($id = "")
    {
		$result = array();
        $res  = $this->mongo_db->findOne(MDB_PASSENGERS, array(
            '_id' => (int) $id
        ), array('email','name'));
        $result[] = $res;
        return $result;
    }
    // Validating User Details while Updating User Details
    public function validate_user_settings($arr, $files_value_array)
    {
        return Validation::factory($arr, $files_value_array)->rule('file', 'Upload::type', array(
            $files_value_array['photo'],
            array(
                'jpg',
                'jpeg',
                'png',
                'gif'
            )
        ))->rule('file', 'Upload::size', array(
            $files_value_array['photo'],
            '2M'
        ));
    }
    public static function validatemonth($month, $year)
    {
        $current_year  = date("Y");
        $current_month = date("m");
        if (($current_year == $year) && ($month < $current_month)) {
            return false;
        } else {
            return true;
        }
    }
    public static function validatecreditcard($creditcard_no)
    {
        $all_cardtype = '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/';
        if (preg_match($all_cardtype, $creditcard_no)) {
            return true;
        } else {
            return false;
        }
    }
    public function validate_user_profilesettings($arr)
    {
        return Validation::factory($arr)->rule('name', 'not_empty')->rule('name', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('name', 'min_length', array(
            ':value',
            '4'
        ))->rule('name', 'max_length', array(
            ':value',
            '32'
        ))->rule('lastname', 'not_empty')->rule('lastname', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('lastname', 'min_length', array(
            ':value',
            '1'
        ))->rule('lastname', 'max_length', array(
            ':value',
            '32'
        ))->rule('email', 'not_empty')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('email', 'email_domain')->rule('description', 'not_empty')->rule('description', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('description', 'min_length', array(
            ':value',
            '5'
        ))->rule('school', 'not_empty')->rule('education', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ))->rule('education', 'not_empty')->rule('education', 'illegal_chars', array(
            ':value',
            '/^[\p{L}-.,_; \'0-9]*$/u'
        ));
    }
    // Validating Forgot Password Details
    public function validate_forgotpwd($arr)
    {
        return Validation::factory($arr)->rule('email', 'email')->rule('email', 'max_length', array(
            ':value',
            '100'
        ))->rule('email', 'not_empty');
    }
    // Check Whether Passenger phone is Already Exist or Not
    public function check_phone_passengers($phone = "", $company_id = "")
    {
        $match_query=array();
		$match_query['phone'] = $phone;
        if ($company_id != '' & $company_id!=0) {
			//$match_query['passenger_cid'] = (int)$company_id;
        }
		$result = $this->mongo_db->count(MDB_PASSENGERS,$match_query);
        return (!empty($result)) ? $result : 0 ;
    }
    
    // Reset User Password if User Forgot Password 
    public function forgot_password_phone($array_data, $value, $random_key, $company_id = null)
    {
        $mdate  = Commonfunction::MongoDate(strtotime($this->currentdate));
        $pass   = md5($random_key);
        $match =array();
        $match['phone'] = $value['phone_no'];
        if ($company_id != '' && $company_id != 0) {
            //$match['passenger_cid'] = (int)$company_id;
        }
        $pwd_arr = array('password' => $pass, 
						//'org_password' => $random_key,
						'updated_date'=>$mdate);
        $update = $this->mongo_db->updateOne(MDB_PASSENGERS,$match,array('$set'=>$pwd_arr),array('upsert'=>true));
        $result = (empty($update->getwriteErrors())) ? 1 :0;
        if ($result) {
            $match['activation_status'] = 1;
            $project = array('name', 'email', 'password','country_code', 'phone');
            $res = $this->mongo_db->findOne(MDB_PASSENGERS,$match,$project);
            return (isset($res)) ? $res : array();
        } else {
            return 0;
        }
    }

    // Validating Change Password Details
    public function validate_changepwd($arr)
    {
        return Validation::factory($arr)->rule('old_password', 'not_empty')->rule('old_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('old_password', 'max_length', array(
            ':value',
            '16'
        ))->rule('new_password', 'not_empty')->rule('new_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('new_password', 'min_length', array(
            ':value',
            '5'
        ))->rule('new_password', 'max_length', array(
            ':value',
            '16'
        ))->rule('confirm_password', 'not_empty')->rule('confirm_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('confirm_password', 'matches', array(
            ':validation',
            'new_password',
            'confirm_password'
        ))->rule('confirm_password', 'min_length', array(
            ':value',
            '5'
        ))->rule('confirm_password', 'max_length', array(
            ':value',
            '16'
        ));
    }
    /**Validating Reset Password Details **/
    public function validate_resetpwd($arr)
    {
        return Validation::factory($arr)->rule('new_password', 'not_empty')
        //->rule('new_password','alpha_dash')
            ->rule('new_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('new_password', 'max_length', array(
            ':value',
            '16'
        ))->rule('conf_password', 'not_empty')
        //->rule('conf_password','alpha_dash')
            
        //->rule('conf_password', array(':equals','new_password'))
            ->rule('conf_password', 'valid_password', array(
            ':value',
            '/^[A-Za-z0-9@#$%!^&*(){}?-_<>=+|~`\'".,:;[]+]*$/u'
        ))->rule('conf_password', 'max_length', array(
            ':value',
            '16'
        ));
    }
    /**
     * Validation rule for fields in email
     */
    public function validate_email($arr)
    {
        return Validation::factory($arr)->rule('email', 'not_empty')->rule('email', 'max_length', array(
            ':value',
            '50'
        ))->rule('email', 'Model_Authorize::check_label_not_empty', array(
            ":value",
            __('enter_email')
        ))->rule('email', 'email_domain')->rule('email', 'Model_Authorize::unique_email');
    }
    //Getting the latitude and Longitude	
    public function getLatLong($address)
    {
        $address = str_replace(' ', '+', $address);
        $url     = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key=' . GOOGLE_GEO_API_KEY;
        $ch      = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $geoloc = curl_exec($ch);
        //print_r($geoloc);
        $json   = json_decode($geoloc);
        //print_r($json);exit;
        if ($json->status == 'OK') {
            return array(
                $json->results[0]->geometry->location->lat,
                $json->results[0]->geometry->location->lng
            );
        } else {
            return array(
                11.621354,
                76.14253698
            );
        }
    }
    //Applying the Haversine Function to get the Distance
    function Haversine($start, $finish)
    {
        $theta    = $start[1] - $finish[1];
        $distance = (sin(deg2rad($start[0])) * sin(deg2rad($finish[0]))) + (cos(deg2rad($start[0])) * cos(deg2rad($finish[0])) * cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        return round($distance, 2);
    }
    /** Get driver status **/
    public function select_current_user($id = "")
    {
        //MongoDB
        $result = $this->mongo_db->findOne(MDB_PASSENGERS,array('_id'=>(int)$id),array('passengerdetails.login_from','passengerdetails.phone'));
        return (!empty($result))?$result:array();
    }
    public static function authorize_creditcard($values)
    {
        //echo "values:";print_r($values); exit;
        $api_model      = Model::factory('api');
        $paypal_details = $api_model->paypal_details();
        $amount         = '0';
        $product_title  = Html::chars('Authorize Creditcard');
        $payment_action = 'Authorization';
        //$payment_action='sale';
        $request        = 'METHOD=DoDirectPayment';
        $request .= '&VERSION=65.1'; //  $this->version='65.1';     51.0  
        $request .= '&USER=' . urlencode($paypal_details[0]['payment_gateway_username']);
        $request .= '&PWD=' . urlencode($paypal_details[0]['payment_gateway_password']);
        $request .= '&SIGNATURE=' . urlencode($paypal_details[0]['payment_gateway_signature']);
        $request .= '&PAYMENTACTION=' . $payment_action; //type
        $request .= '&AMT=' . urlencode($amount); //   
        $request .= '&ACCT=' . urlencode(str_replace(' ', '', $values['creditcard_no']));
        $request .= '&EXPDATE=' . urlencode($values['expdatemonth'] . $values['expdateyear']);
        $request .= '&CVV2=' . urlencode($values['creditcard_cvv']);
        $request .= '&CURRENCYCODE=' . $paypal_details[0]['currency_code'];
        $paypal_type = ($paypal_details[0]['payment_method'] == "L") ? "live" : "sandbox";
        if ($paypal_type == "live") {
            $curl = curl_init('https://api-3t.paypal.com/nvp');
        } else {
            $curl = curl_init('https://api-3t.sandbox.paypal.com/nvp');
        }
        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        $response = curl_exec($curl);
        $nvpstr   = $response;
        curl_close($curl);
        $intial   = 0;
        $nvpArray = array();
        while (strlen($nvpstr)) {
            //postion of Key
            $keypos                       = strpos($nvpstr, '=');
            //position of value
            $valuepos                     = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);
            //getting the Key and Value values and storing in a Associative Array
            $keyval                       = substr($nvpstr, $intial, $keypos);
            $valval                       = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr                       = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }
        $ack = isset($nvpArray['ACK']) ? strtoupper($nvpArray['ACK']) : '';
        if ($ack == 'SUCCESSWITHWARNING' || $ack == 'SUCCESS') {
            return 0;
        } else {
            return -1;
        }
    }
    /*********** Function Used for validate credit cards ***************/
    function _checkSum($ccnum)
    {
        $checksum = 0;
        for ($i = (2 - (strlen($ccnum) % 2)); $i <= strlen($ccnum); $i += 2) {
            $checksum += (int) ($ccnum{$i - 1});
        }
        // Analyze odd digits in even length strings or even digits in odd length strings.
        for ($i = (strlen($ccnum) % 2) + 1; $i < strlen($ccnum); $i += 2) {
            $digit = (int) ($ccnum{$i - 1}) * 2;
            if ($digit < 10) {
                $checksum += $digit;
            } else {
                $checksum += ($digit - 9);
            }
        }
        if (($checksum % 10) == 0)
            return true;
        else
            return false;
    }
    function isVAlidCreditCard($ccnum, $type = "", $returnobj = false)
    {
        $creditcard = array(
            "visa" => "/^4\d{3}-?\d{4}-?\d{4}-?\d{4}$/",
            "mastercard" => "/^5[1-5]\d{2}-?\d{4}-?\d{4}-?\d{4}$/",
            "discover" => "/^6011-?\d{4}-?\d{4}-?\d{4}$/",
            "amex" => "/^3[4,7]\d{13}$/",
            "diners" => "/^3[0,6,8]\d{12}$/",
            "bankcard" => "/^5610-?\d{4}-?\d{4}-?\d{4}$/",
            "jcb" => "/^[3088|3096|3112|3158|3337|3528|3530]\d{12}$/",
            "enroute" => "/^[2014|2149]\d{11}$/",
            "switch" => "/^[4903|4911|4936|5641|6333|6759|6334|6767]\d{12}$/"
        );
        if (empty($type)) {
            $match = false;
            foreach ($creditcard as $type => $pattern)
                if (preg_match($pattern, $ccnum) == 1) {
                    $match = true;
                    break;
                }
            if (!$match)
                return 0;
            else {
                if ($returnobj) {
                    $return        = new stdclass;
                    $return->valid = $this->_checkSum($ccnum);
                    $return->ccnum = $ccnum;
                    $return->type  = $type;
                    return 1;
                } else
                    return 0;
            }
        } else {
            if (@preg_match($creditcard[strtolower(trim($type))], $ccnum) == 0) {
                return false;
            } else {
                if ($returnobj) {
                    //print_r($returnobj);
                    $return        = new stdclass;
                    $return->valid = $this->_checkSum($ccnum);
                    $return->ccnum = $ccnum;
                    $return->type  = $type;
                    return 1;
                } else
                    return 1;
            }
        }
    }
    /**Validating Card Details **/
    public function validate_card_details($arr)
    {
        return Validation::factory($arr)->rule('card_type', 'not_empty')->rule('org_creditcard_no', 'max_length', array(
            ':value',
            '16'
        ))->rule('org_creditcard_cvv', 'not_empty')->rule('org_creditcard_cvv', 'numeric')->rule('expdatemonth', 'numeric')->rule('expdateyear', 'numeric');
    }
}
