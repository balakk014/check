<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Kohana Braintree payment gateway module
 *
 * @uses       Braintree (v1.0)
 * @package    Custom
 * @author     NDOT Team 
 * @copyright  (c) 2017-2020 Ndot Team 
 */

class Kohana_Braintreepayment {

    /**
     * Declare static variable
     * 
     * @var type 
     */
    protected static $_braintree_gateway_connection;

    public function __construct() {
        
    }

   /**
    * Braintree payment gateway connection establishment
    * 
    * @param type $connection_string
    * @return boolean
    * @throws Exception
    */
    public static function connect($connection_string = []) {

        // Load default configuration
        // ($config === NULL) AND $config = Kohana::$config->load('emal');


        if (isset($connection_string[0]['payment_method'])) {
            $pay_type = $connection_string[0]['payment_method'];
            try {
                // Braintree sandbox environment 
                if ($pay_type == "T") {

                    Braintree_Configuration::environment('sandbox');
                    $merchantId = $connection_string[0]['payment_gateway_username'];
                    $publicKey = $connection_string[0]['payment_gateway_password'];
                    $privateKey = $connection_string[0]['payment_gateway_signature'];
                }
                // Braintree production environment 
                else if ($pay_type == "L") {
                    Braintree_Configuration::environment('production');
                    $merchantId = $connection_string[0]['live_payment_gateway_username'];
                    $publicKey = $connection_string[0]['live_payment_gateway_password'];
                    $privateKey = $connection_string[0]['live_payment_gateway_signature'];
                } else {
                    throw new Exception('payment method is not valid');
                }
                // Braintree configuration setup 
                Braintree_Configuration::merchantId($merchantId); //your_merchant_id
                Braintree_Configuration::publicKey($publicKey); //your_public_key
                Braintree_Configuration::privateKey($privateKey); //your_private_key
            } catch (Braintree_Exception $message) {
                echo $message;
                exit;
            }            
            return true;
        } else {
            return false;
        }
    }

   /**
    * Braintree payment sale transaction 
    * 
    * @param type $connection_string
    * @param type $transaction_amount
    * @param type $card_info
    * @param type $shipping_info
    * @param type $additional_parameters
    * @return boolean|int
    * @throws Exception
    */
    public static function braintree_sale($connection_string = [], $transaction_amount = null, $card_info = [], $shipping_info = [], $additional_parameters = []) {

        // Braintree payment gateway connection
        $braintree_connect = self::connect($connection_string);

        if ($braintree_connect == true) {
            $payment_info = array_merge($card_info, $shipping_info);
            $validate = self::validate_sale_info($payment_info);

            if ($validate->check()) {
                try {
                    $braintree_result = Braintree_Transaction::sale([
                                'amount' => $transaction_amount,
                                'creditCard' => [
                                    'cardholderName' => $shipping_info['firstName'],
                                    'number' => str_replace(' ', '', $card_info['card_number']), //$_POST['creditCard']
                                    'expirationMonth' => $card_info['expirationMonth'], //$_POST['month']
                                    'expirationYear' => $card_info['expirationYear'], //$_POST['year']
                                    'cvv' => str_replace(' ', '', $card_info['cvv']),
                                ],
                                'customer' => [
                                    'firstName' => $shipping_info['firstName'],
                                    'lastName' => isset($shipping_info['lastName'])? $shipping_info['lastName'] : '',
                                    'company' => isset($shipping_info['company']) ? $shipping_info['company'] : '',
                                    'phone' => isset($shipping_info['phone']) ? $shipping_info['phone'] : '',
                                    'fax' => isset($shipping_info['fax']) ? $shipping_info['fax'] : '',
                                    'website' => isset($shipping_info['website']) ? $shipping_info['website'] : '',
                                    'email' => isset($shipping_info['email']) ? $shipping_info['email'] : ''
                                ],
                                'shipping' => [
                                    'firstName' => $shipping_info['firstName'],
                                    'lastName' => isset($shipping_info['lastName'])? $shipping_info['lastName'] : '',
                                    'company' => isset($shipping_info['company']) ? $shipping_info['company'] : '',
                                    'streetAddress' => isset($shipping_info['street']) ? $shipping_info['street'] : '',
                                    'extendedAddress' => '',
                                    'locality' => isset($shipping_info['state']) ? $shipping_info['state'] : '',
                                    'region' => isset($shipping_info['country_code']) ? $shipping_info['country_code'] : '',
                                    'postalCode' => isset($shipping_info['zipcode']) ? $shipping_info['zipcode'] : '',
                                    'countryCodeAlpha2' => isset($shipping_info['country_code']) ? $shipping_info['country_code'] : '',
                                ],                   
                      'options' => ['submitForSettlement' => True]
  ]);
                } catch (Braintree_Exception $message) {
                    $class_name=get_class($message);
                    if($class_name='Braintree_Exception_Authentication'){
                        $response='Invalid Merchant configuration';
                    }else{
                        $response=$class_name;
                    }
                    $payment_response['payment_response']=$response;
                    $payment_response['payment_status']=0;
                    return $payment_response; 
                }


                $payment_response=[];
                $payment_response['payment_method']=isset($connection_string[0]['payment_method'])?$connection_string[0]['payment_method']:'';
                $payment_response['payment_gateway_id']=isset($connection_string[0]['payment_gateway_id'])?$connection_string[0]['payment_gateway_id']:'';
            
                // Transaction success response
                if ($braintree_result->success) {                    
                    $payment_response['payment_response']=$braintree_result->success;
                    $payment_response['TRANSACTIONID']=$braintree_result->transaction->id;
                    $payment_response['payment_status']=1;
                    $payment_response['cardType'] =$braintree_result->transaction->creditCardDetails->cardType;	                    
                }
                // Transaction failure response
                else if ($braintree_result->transaction) {
                    $payment_response['payment_status']=0;                    
                }
                // Transaction failure response
                else if ($braintree_result->message) {
                    $payment_response['payment_response']=$braintree_result->message;
                    $payment_response['payment_status']=0;
                    
                }
                return $payment_response;
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Braintree_payment_gateway_not_properly configured or braintree payment gateway not active');
        }
    }
    /**
     * Braintree Preauthorization process
     * 
     * @param type $connection_string
     * @param type $transaction_amount
     * @param type $card_info
     * @param type $shipping_info
     * @param type $additional_parameters
     * @return type
     */

    public static function braintree_preauthorization($connection_string = [], $transaction_amount = null, $card_info = [], $shipping_info = [], $additional_parameters = []) {
         // Braintree payment gateway connection
        $braintree_connect = self::connect($connection_string);

        if ($braintree_connect == true) {
            $payment_info = array_merge($card_info, $shipping_info);
            $validate = self::validate_sale_info($payment_info);

            if ($validate->check()) {
                try {
                    $braintree_result = Braintree_Transaction::sale([
                                'amount' => $transaction_amount,
                                'creditCard' => [
                                    'cardholderName' => $shipping_info['firstName'],
                                    'number' => str_replace(' ', '', $card_info['card_number']), //$_POST['creditCard']
                                    'expirationMonth' => $card_info['expirationMonth'], //$_POST['month']
                                    'expirationYear' => $card_info['expirationYear'], //$_POST['year']
                                    'cvv' => str_replace(' ', '', $card_info['cvv']),
                                ],
                                'customer' => [
                                    'firstName' => $shipping_info['firstName'],
                                    'lastName' => isset($shipping_info['lastName'])? $shipping_info['lastName'] : '',
                                    'company' => isset($shipping_info['company']) ? $shipping_info['company'] : '',
                                    'phone' => isset($shipping_info['phone']) ? $shipping_info['phone'] : '',
                                    'fax' => isset($shipping_info['fax']) ? $shipping_info['fax'] : '',
                                    'website' => isset($shipping_info['website']) ? $shipping_info['website'] : '',
                                    'email' => isset($shipping_info['email']) ? $shipping_info['email'] : ''
                                ],
                                'shipping' => [
                                    'firstName' => $shipping_info['firstName'],
                                    'lastName' => isset($shipping_info['lastName'])? $shipping_info['lastName'] : '',
                                    'company' => isset($shipping_info['company']) ? $shipping_info['company'] : '',
                                    'streetAddress' => isset($shipping_info['street']) ? $shipping_info['street'] : '',
                                    'extendedAddress' => '',
                                    'locality' => isset($shipping_info['state']) ? $shipping_info['state'] : '',
                                    'region' => isset($shipping_info['country_code']) ? $shipping_info['country_code'] : '',
                                    'postalCode' => isset($shipping_info['zipcode']) ? $shipping_info['zipcode'] : '',
                                    'countryCodeAlpha2' => isset($shipping_info['country_code']) ? $shipping_info['country_code'] : '',
                                ],                   
                      
  ]);
                } catch (Braintree_Exception $message) {                    
                    $class_name=get_class($message);
                    if($class_name='Braintree_Exception_Authentication'){
                        $response='Invalid Merchant configuration';
                    }else{
                        $response=$class_name;
                    }
                    $payment_response['payment_response']=$response;
                    $payment_response['payment_status']=0;
                    return $payment_response;                    
                }


                $payment_response=[];
                $payment_response['payment_method']=isset($connection_string[0]['payment_method'])?$connection_string[0]['payment_method']:'';
                $payment_response['payment_gateway_id']=isset($connection_string[0]['payment_gateway_id'])?$connection_string[0]['payment_gateway_id']:'';
            
                // Transaction success response
                if ($braintree_result->success) {                    
                    $payment_response['payment_response']=$braintree_result->success;
                    $payment_response['TRANSACTIONID']=$braintree_result->transaction->id;
                    $payment_response['payment_status']=1;
                    $payment_response['cardType'] =$braintree_result->transaction->creditCardDetails->cardType;	                    
                }
                // Transaction failure response
                else if ($braintree_result->transaction) {
                    $payment_response['payment_status']=0;                    
                }
                // Transaction failure response
                else if ($braintree_result->message) {
                    $payment_response['payment_response']=$braintree_result->message;
                    $payment_response['payment_status']=0;
                    
                }
                return $payment_response;
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Braintree_payment_gateway_not_properly configured or braintree payment gateway not active');
        }
    }
    /**
     * Braintree void transaction
     * 
     * @param type $connection_string
     * @param type $transactionId
     * @return boolean|int
     * @throws Exception
     */
    public static function braintree_void($connection_string = [], $transactionId = '') {
        // Braintree payment gateway connection
        $braintree_connect = self::connect($connection_string);
        if ($braintree_connect == true) {
            $validate = self::validate_void_settlement_info(['transactionId'=>$transactionId]);

            if ($validate->check()) {
                try {
                    $braintree_result = Braintree_Transaction::void($transactionId);
                    
                } catch (Braintree_Exception $message) {
                     $class_name=get_class($message);
                    if($class_name='Braintree_Exception_Authentication'){
                        $response='Invalid Merchant configuration';
                    }else{
                        $response=$class_name;
                    }
                    $payment_response['payment_response']=$response;
                    $payment_response['payment_status']=0;
                    return $payment_response; 
                }
                
                $payment_response=[];
                $payment_response['payment_method']=isset($connection_string[0]['payment_method'])?$connection_string[0]['payment_method']:'';
                $payment_response['payment_gateway_id']=isset($connection_string[0]['payment_gateway_id'])?$connection_string[0]['payment_gateway_id']:'';
                // Transaction success response
                if ($braintree_result->success) {                    
                    $payment_response['payment_response']=$braintree_result->success;
                    $payment_response['TRANSACTIONID']=$braintree_result->transaction->id;
                    $payment_response['payment_status']=1;
                    $payment_response['cardType'] =$braintree_result->transaction->creditCardDetails->cardType;	                    
                }                
                // Transaction failure response
                else if ($braintree_result->message) {
                    $payment_response['payment_response']=$braintree_result->message;
                    $payment_response['payment_status']=0;                    
                }
               return $payment_response;
                
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Braintree_payment_gateway_not_properly configured or braintree payment gateway not active');
        }
    }

    /**
     * Braintree Settlement transaction
     * 
     * @param type $connection_string
     * @param type $transactionId
     * @param type $amount
     * @return boolean|int
     * @throws Exception
     */
    public static function braintree_settlement($connection_string = [], $transactionId = '', $amount = null) {
        // Braintree payment gateway connection
        $braintree_connect = self::connect($connection_string);
        if ($braintree_connect == true) {
            $validate = self::validate_void_settlement_info(['transactionId'=>$transactionId]);

            if ($validate->check()) {
                try {
                    if ($amount != null) {
                        $braintree_result = Braintree_Transaction::submitForSettlement($transactionId, $amount);
                    } else {
                        $braintree_result = Braintree_Transaction::submitForSettlement($transactionId);
                    }
                    
                } catch (Braintree_Exception $message) {
                   $class_name=get_class($message);
                    if($class_name='Braintree_Exception_Authentication'){
                        $response='Invalid Merchant configuration';
                    }else{
                        $response=$class_name;
                    }
                    $payment_response['payment_response']=$response;
                    $payment_response['payment_status']=0;
                    return $payment_response; 
                }
                
                $payment_response=[];
                $payment_response['payment_method']=isset($connection_string[0]['payment_method'])?$connection_string[0]['payment_method']:'';
                $payment_response['payment_gateway_id']=isset($connection_string[0]['payment_gateway_id'])?$connection_string[0]['payment_gateway_id']:'';
                  // Transaction success response
                if ($braintree_result->success) {  
                    $payment_response['payment_response']=$braintree_result->success;
                    //$payment_response['transaction']=$braintree_result->transaction; 
                    $payment_response['TRANSACTIONID']=$braintree_result->transaction->id;
                    $payment_response['transaction_status']=str_replace('_', ' ', $braintree_result->transaction->status);
                    $payment_response['payment_status']=1;                    
                }                
                // Transaction failure response
                else{
                   // $payment_response['transaction']==$braintree_result->transaction;
                    $payment_response['payment_response']=$braintree_result->message;
                    $payment_response['payment_status']=0;                    
                    $payment_response['transaction_status']='';
                    $payment_response['TRANSACTIONID']='';
                }
                return $payment_response;
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Braintree_payment_gateway_not_properly configured or braintree payment gateway not active');
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
