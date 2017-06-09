<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Kohana Paypal payment gateway module
 *
 * @uses       Paypal -Do Direct (v1.0)
 * @package    Custom
 * @author     NDOT Team 
 * @copyright  (c) 2017-2020 Ndot Team 
 */
class Kohana_Paypalpayment {

    /**
     * Declare static variable
     * 
     * @var type 
     */
    protected static $_paypal_gateway_connection;

    public function __construct() {
        
    }

    /**
     * Paypal payment gateway connection establishment   
     * 
     * @param type $connection_string
     * @return boolean|string
     * @throws Exception
     */
    public static function connect($connection_string = []) {

        // Load default configuration
        // ($config === NULL) AND $config = Kohana::$config->load('emal');


        if (isset($connection_string[0]['payment_method'])) {
            $pay_type = $connection_string[0]['payment_method'];
            try {



                // Paypal sandbox environment 
                if ($pay_type == "T") {
                    $merchantId = $connection_string[0]['payment_gateway_username'];
                    $publicKey = $connection_string[0]['payment_gateway_password'];
                    $privateKey = $connection_string[0]['payment_gateway_signature'];
                    $curl_url = 'https://api-3t.sandbox.paypal.com/nvp';
                }
                // Paypal production environment 
                else if ($pay_type == "L") {
                    $merchantId = $connection_string[0]['live_payment_gateway_username'];
                    $publicKey = $connection_string[0]['live_payment_gateway_password'];
                    $privateKey = $connection_string[0]['live_payment_gateway_signature'];
                    $curl_url = 'https://api-3t.paypal.com/nvp';

                    /*                     * ************************************ */
                } else {
                    throw new Exception('payment method is not valid');
                }
                $paypal_params['USER'] = urlencode($merchantId);
                $paypal_params['PWD'] = urlencode($publicKey);
                $paypal_params['SIGNATURE'] = urlencode($privateKey);
                $paypal_params['CURRENCYCODE'] = str_replace(' ', '', CURRENCY_FORMAT);
                $paypal_params['CURL_URL'] = $curl_url;
                return $paypal_params;
            } catch (Exception $message) {
                echo $message;
                exit;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Paypal payment sale transaction 
     * 
     * @param type $connection_string
     * @param type $transaction_amount
     * @param type $card_info
     * @param type $shipping_info
     * @param type $additional_parameters
     * @return boolean|int
     * @throws Exception
     */
    public static function paypal_sale($connection_string = [], $transaction_amount = null, $card_info = [], $shipping_info = [], $additional_parameters = []) {

        // Paypal payment gateway connection
        $paypal_authenticate_params = self::connect($connection_string);

        
        if (is_array($paypal_authenticate_params)) {


            $payment_info = array_merge_recursive($card_info, $shipping_info, $paypal_authenticate_params);

            $validate = self::validate_sale_info($payment_info);

            if ($validate->check()) {
                try {
                    //$shipping_info = array_change_key_case($shipping_info, CASE_UPPER);
                    $cust_ref_id=1;
                    if(isset($additional_parameters['trip_id'])){
                        $cust_ref_id=$additional_parameters['trip_id'];
                    }elseif (isset ($additional_parameters['passenger_id'])) {
                        $cust_ref_id=$additional_parameters['passenger_id'];
                    }
                    $payment_action = 'sale';
                    $request['METHOD'] = 'DoDirectPayment';
                    $request['VERSION'] = 65.1; //  $this->version='65.1';     51.0  
                    //$request['CUSTREF']=(int) $values['passenger_log_id'];
                    $request['CUSTREF'] = $cust_ref_id;
                    $request['PAYMENTACTION'] = $payment_action; //type
                    $request['AMT'] = urlencode($transaction_amount); //   $amount = urlencode($data['amount']);
                    $request['ACCT'] = urlencode(str_replace(' ', '', $card_info['card_number']));
                    $request['EXPDATE'] = urlencode($card_info['expirationMonth'] . $card_info['expirationYear']);
                    $request['CVV2'] = urlencode(str_replace(' ', '', $card_info['cvv']));
                    

                    $request['FIRSTNAME'] = urlencode($shipping_info['firstName']);
                    $request['LASTNAME'] = isset($shipping_info['lastName']) ? urlencode($shipping_info['lastName']) : '';
                    $request['EMAIL'] = isset($shipping_info['email']) ? $shipping_info['email'] : '';
                    $request['IPADDRESS'] = urlencode($_SERVER['REMOTE_ADDR']);
                    $request['STREET'] =  isset($shipping_info['street']) ? urlencode($shipping_info['street']) : '';
                    $request['CITY'] =  isset($shipping_info['city']) ? urlencode($shipping_info['city']) : '';
                    $request['STATE'] =  isset($shipping_info['state']) ? urlencode($shipping_info['state']) : '';
                    $request['ZIP'] = isset($shipping_info['zipcode']) ? urlencode($shipping_info['zipcode']) : '';
                    $request['COUNTRYCODE'] =  isset($shipping_info['country_code']) ? urlencode($shipping_info['country_code']) : '';
                    
                    
                    
                    $curl=curl_init($paypal_authenticate_params['CURL_URL']); 
                    unset($paypal_authenticate_params['CURL_URL']);
                    $request= array_merge($request,$paypal_authenticate_params);
                    $payment_gateway_params= http_build_query($request);
                    
                    // Paypal configuration setup 
                    curl_setopt($curl, CURLOPT_PORT, 443);
                    curl_setopt($curl, CURLOPT_HEADER, 0);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
                    curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $payment_gateway_params);
                    $response = curl_exec($curl);
                    $nvpstr = $response;
                    curl_close($curl);
                    $intial = 0;
                    $nvpArray = array();
                    while (strlen($nvpstr)) {
                        //postion of Key
                        $keypos = strpos($nvpstr, '=');
                        //position of value
                        $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);
                        /* getting the Key and Value values and storing in a Associative Array */
                        $keyval = substr($nvpstr, $intial, $keypos);
                        $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
                        //decoding the respose
                        $nvpArray[urldecode($keyval)] = urldecode($valval);
                        $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
                    }
                     
                   
                } catch (Exception $message) {
                    echo $message;
                    exit;
                }


                $payment_response = [];
                $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
                $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';
                
                // Transaction success response
                if ($nvpArray['ACK']=="SuccessWithWarning" || $nvpArray['ACK'] == "Success" || $nvpArray['ACK'] == "SUCCESS" || $nvpArray['ACK'] == "SUCCESSWITHWARNING") {
                    $payment_response['payment_response'] = $nvpArray['ACK'];
                    $payment_response['CORRELATIONID']=$nvpArray['CORRELATIONID'];
                    $payment_response['TRANSACTIONID'] = $nvpArray['TRANSACTIONID'];
                    $payment_response['payment_status'] = 1;
                    $payment_response['cardType'] = '';
                }

                // Transaction failure response
                else{
                    $shortmessage= isset($nvpArray['L_SHORTMESSAGE0'])?' - '.$nvpArray['L_SHORTMESSAGE0']:'';
                    $payment_response['payment_response'] = $nvpArray['ACK'].$shortmessage;
                    $payment_response['payment_status'] = 0;
                    $payment_response['CORRELATIONID']=$nvpArray['CORRELATIONID'];
                }
                return $payment_response;
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Paypal_payment_gateway_not_properly configured or paypal payment gateway not active');
        }
    }
    /**
     * paypal Preauthorization process
     * 
     * @param type $connection_string
     * @param type $transaction_amount
     * @param type $card_info
     * @param type $shipping_info
     * @param type $additional_parameters
     * @return type
     */

    public static function paypal_preauthorization($connection_string = [], $transaction_amount = null, $card_info = [], $shipping_info = [], $additional_parameters = []) {
        // Paypal payment gateway connection
        $paypal_authenticate_params = self::connect($connection_string);
        
        if (is_array($paypal_authenticate_params)) {


            $payment_info = array_merge_recursive($card_info, $shipping_info, $paypal_authenticate_params);

            $validate = self::validate_sale_info($payment_info);

            if ($validate->check()) {
                try {
                    //$shipping_info = array_change_key_case($shipping_info, CASE_UPPER);
                    $payment_action = 'Authorization';
                    $request['METHOD'] = 'DoDirectPayment';
                    $request['VERSION'] = 65.1; //  $this->version='65.1';     51.0  
                    //$request['CUSTREF']=(int) $values['passenger_log_id'];
                    $request['CUSTREF'] = 1;
                    $request['PAYMENTACTION'] = $payment_action; //type
                    $request['AMT'] = urlencode($transaction_amount); //   $amount = urlencode($data['amount']);
                    $request['ACCT'] = urlencode(str_replace(' ', '', $card_info['card_number']));
                    $request['EXPDATE'] = urlencode($card_info['expirationMonth'] . $card_info['expirationYear']);
                    $request['CVV2'] = urlencode(str_replace(' ', '', $card_info['cvv']));
                    

                    $request['FIRSTNAME'] = urlencode($shipping_info['firstName']);
                    $request['LASTNAME'] = isset($shipping_info['lastName']) ? urlencode($shipping_info['lastName']) : '';
                    $request['EMAIL'] = isset($shipping_info['email']) ? $shipping_info['email'] : '';
                    $request['IPADDRESS'] = urlencode($_SERVER['REMOTE_ADDR']);
                    $request['STREET'] =  isset($shipping_info['street']) ? urlencode($shipping_info['street']) : '';
                    $request['CITY'] =  isset($shipping_info['city']) ? urlencode($shipping_info['city']) : '';
                    $request['STATE'] =  isset($shipping_info['state']) ? urlencode($shipping_info['state']) : '';
                    $request['ZIP'] = isset($shipping_info['zipcode']) ? urlencode($shipping_info['zipcode']) : '';
                    $request['COUNTRYCODE'] =  isset($shipping_info['country_code']) ? urlencode($shipping_info['country_code']) : '';
                    
                    
                    
                    $curl=curl_init($paypal_authenticate_params['CURL_URL']); 
                    unset($paypal_authenticate_params['CURL_URL']);
                    $request= array_merge($request,$paypal_authenticate_params);
                    $payment_gateway_params= http_build_query($request);
                    // Paypal configuration setup 
                    curl_setopt($curl, CURLOPT_PORT, 443);
                    curl_setopt($curl, CURLOPT_HEADER, 0);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
                    curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $payment_gateway_params);
                    $response = curl_exec($curl);
                    $nvpstr = $response;
                    curl_close($curl);
                    $intial = 0;
                    $nvpArray = array();
                    while (strlen($nvpstr)) {
                        //postion of Key
                        $keypos = strpos($nvpstr, '=');
                        //position of value
                        $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);
                        /* getting the Key and Value values and storing in a Associative Array */
                        $keyval = substr($nvpstr, $intial, $keypos);
                        $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
                        //decoding the respose
                        $nvpArray[urldecode($keyval)] = urldecode($valval);
                        $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
                    }
                     
                   
                } catch (Exception $message) {
                    echo $message;
                    exit;
                }


                $payment_response = [];
                $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
                $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';
                
                // Transaction success response
                if ($nvpArray['ACK']=="SuccessWithWarning" || $nvpArray['ACK'] == "Success" || $nvpArray['ACK'] == "SUCCESS" || $nvpArray['ACK'] == "SUCCESSWITHWARNING") {
                    $payment_response['payment_response'] = $nvpArray['ACK'];
                    $payment_response['CORRELATIONID']=$nvpArray['CORRELATIONID'];
                    $payment_response['TRANSACTIONID'] = $nvpArray['TRANSACTIONID'];
                    $payment_response['payment_status'] = 1;
                    $payment_response['cardType'] = '';
                }

                // Transaction failure response
                else{
                    $shortmessage= isset($nvpArray['L_SHORTMESSAGE0'])?' - '.$nvpArray['L_SHORTMESSAGE0']:'';
                    $payment_response['payment_response'] = $nvpArray['ACK'].$shortmessage;
                    $payment_response['payment_status'] = 0;
                    $payment_response['CORRELATIONID']=$nvpArray['CORRELATIONID'];
                    $payment_response['TRANSACTIONID'] ='';
                }
                return $payment_response;
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Paypal_payment_gateway_not_properly configured or paypal payment gateway not active');
        }
    }

    /**
     * Paypal void transaction
     * 
     * @param type $connection_string
     * @param type $transactionId
     * @return boolean|int
     * @throws Exception
     */
    public static function paypal_void($connection_string = [], $transactionId = '') {
        // Paypal payment gateway connection
       $paypal_authenticate_params = self::connect($connection_string);
        
        if (is_array($paypal_authenticate_params)) {
            $validate = self::validate_void_settlement_info(['transactionId' => $transactionId]);

            if ($validate->check()) {
                try {
                    
             
                    $request['METHOD'] = 'RefundTransaction';
                    $request['VERSION'] = 65.1; //  $this->version='65.1';     51.0              
             
                    $request['TRANSACTIONID']= urlencode($transactionId);
                    $curl=curl_init($paypal_authenticate_params['CURL_URL']); 
                    unset($paypal_authenticate_params['CURL_URL']);
                    $request= array_merge($request,$paypal_authenticate_params);
                    $payment_gateway_params= http_build_query($request);
			//	print_r($request);
			curl_setopt($curl, CURLOPT_PORT, 443);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			$nvpstr = curl_exec($curl);
			curl_close($curl);
			$nvpArray = array();
			$intial=0;
			while(strlen($nvpstr))
			{
				//postion of Key
				$keypos= strpos($nvpstr,'=');
				//position of value
				$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
				/*getting the Key and Value values and storing in a Associative Array*/
				$keyval=substr($nvpstr,$intial,$keypos);
				$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
				//decoding the respose
				$nvpArray[urldecode($keyval)] =urldecode( $valval);
				$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
			}
                    
                } catch (Exception $message) {
                    echo $message;
                    exit;
                }

                $payment_response = [];
                $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
                $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';
                // Transaction success response
                if ($nvpArray['ACK']=="SuccessWithWarning" || $nvpArray['ACK'] == "Success" || $nvpArray['ACK'] == "SUCCESS" || $nvpArray['ACK'] == "SUCCESSWITHWARNING") {
                    $payment_response['payment_response'] = $nvpArray['ACK'];
                    $payment_response['CORRELATIONID']=$nvpArray['CORRELATIONID'];
                    $payment_response['TRANSACTIONID'] = $nvpArray['TRANSACTIONID'];
                    $payment_response['payment_status'] = 1;
                    $payment_response['cardType'] = '';
                }
                // Transaction failure response
                else {
                    $shortmessage= isset($nvpArray['L_SHORTMESSAGE0'])?' - '.$nvpArray['L_SHORTMESSAGE0']:'';
                    $payment_response['payment_response'] = $nvpArray['ACK'].$shortmessage;
                    $payment_response['payment_status'] = 0;
                }
                return $payment_response;
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Paypal_payment_gateway_not_properly configured or paypal payment gateway not active');
        }
    }

    /**
     * Paypal Settlement transaction
     * 
     * @param type $connection_string
     * @param type $transactionId
     * @param type $amount
     * @return boolean|int
     * @throws Exception
     */
    public static function paypal_settlement($connection_string = [], $transactionId = '', $amount = null) {
        // Paypal payment gateway connection
        $paypal_authenticate_params = self::connect($connection_string);
        if (is_array($paypal_authenticate_params)) {
            $validate = self::validate_void_settlement_info(['transactionId' => $transactionId]);

            if ($validate->check()) {
                try {
                    
                    $request['METHOD'] = 'DoCapture';
                    $request['VERSION'] = 65.1; //  $this->version='65.1';     51.0              
             
                    $request['TRANSACTIONID']= urlencode($transactionId);
                    $request['AMT']=$amount;
                    $curl=curl_init($paypal_authenticate_params['CURL_URL']); 
                    unset($paypal_authenticate_params['CURL_URL']);
                    $request= array_merge($request,$paypal_authenticate_params);
                    $payment_gateway_params= http_build_query($request);
			//	print_r($request);
			curl_setopt($curl, CURLOPT_PORT, 443);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			$nvpstr = curl_exec($curl);
			curl_close($curl);
			$nvpArray = array();
			$intial=0;
			while(strlen($nvpstr))
			{
				//postion of Key
				$keypos= strpos($nvpstr,'=');
				//position of value
				$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
				/*getting the Key and Value values and storing in a Associative Array*/
				$keyval=substr($nvpstr,$intial,$keypos);
				$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
				//decoding the respose
				$nvpArray[urldecode($keyval)] =urldecode( $valval);
				$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
			}
                    
                    
                    /*if ($amount != null) {
                        $paypal_result = Braintree_Transaction::submitForSettlement($transactionId, $amount);
                    } else {
                        $paypal_result = Braintree_Transaction::submitForSettlement($transactionId);
                    }*/
                } catch (Exception $message) {
                    echo $message;
                    exit;
                }

                $payment_response = [];
                $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
                $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';
                // Transaction success response
                if ($nvpArray['ACK']=="SuccessWithWarning" || $nvpArray['ACK'] == "Success" || $nvpArray['ACK'] == "SUCCESS" || $nvpArray['ACK'] == "SUCCESSWITHWARNING") {
                    $payment_response['payment_response'] = $nvpArray['ACK'];
                    //$payment_response['transaction'] =  $nvpArray['ACK'];
                    $payment_response['TRANSACTIONID'] = $nvpArray['TRANSACTIONID'];
                    $payment_response['transaction_status'] = str_replace('_', ' ',  $nvpArray['ACK']);
                    $payment_response['payment_status'] = 1;
                }
                // Transaction failure response
                else {
                    //$payment_response['transaction'] ==  $nvpArray['ACK'];
                    $shortmessage= isset($nvpArray['L_SHORTMESSAGE0'])?' - '.$nvpArray['L_SHORTMESSAGE0']:'';
                    $payment_response['payment_response'] =  $nvpArray['ACK'].$shortmessage;
                    $payment_response['payment_status'] = 0;
                    $payment_response['transaction_status']='';
                    $payment_response['TRANSACTIONID'] ='';
                }
                return $payment_response;
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Paypal_payment_gateway_not_properly configured or paypal payment gateway not active');
        }
    }

    /**
     * validate payment transaction parameters
     * 
     * @param type $payment_info
     * @return type
     */
    private static function validate_sale_info($payment_info) {

        return Validation::factory($payment_info)
                        ->rule('card_number', 'not_empty')
                        ->rule('expirationMonth', 'not_empty')
                        ->rule('expirationYear', 'not_empty')
                        ->rule('cvv', 'not_empty')
                        ->rule('firstName', 'not_empty');
    }

    /**
     * validate payment transaction parameters
     * 
     * @param type $payment_info
     * @return type
     */
    private static function validate_void_settlement_info($payment_info) {

        return Validation::factory($payment_info)
                        ->rule('transactionId', 'not_empty');
    }

}
