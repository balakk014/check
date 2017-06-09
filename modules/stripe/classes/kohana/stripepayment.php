<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Kohana Stripe payment gateway module
 *
 * @uses       Stripe (v1.0)
 * @package    Custom
 * @author     NDOT Team 
 * @copyright  (c) 2017-2020 Ndot Team 
 */
class Kohana_Stripepayment {

    /**
     * Declare static variable
     * 
     * @var type 
     */
    protected static $_stripe_gateway_connection;

    public function __construct() {
        
    }

    /**
     * stripe payment gateway connection establishment
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
                // Stripe sandbox environment 
                if ($pay_type == "T") {


                    $merchantId = $connection_string[0]['payment_gateway_username'];
                    $publicKey = $connection_string[0]['payment_gateway_password'];
                    $privateKey = $connection_string[0]['payment_gateway_signature'];
                }
                // Stripe production environment 
                else if ($pay_type == "L") {

                    $merchantId = $connection_string[0]['live_payment_gateway_username'];
                    $publicKey = $connection_string[0]['live_payment_gateway_password'];
                    $privateKey = $connection_string[0]['live_payment_gateway_signature'];
                } else {
                    throw new Exception('payment method is not valid');
                }
                
                // Stripe configuration setup 
                $strip_connect = \Stripe\Stripe::setApiKey($merchantId);
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
     * Stripe payment sale transaction 
     * 
     * @param type $connection_string
     * @param type $transaction_amount
     * @param type $card_info
     * @param type $shipping_info
     * @param type $additional_parameters
     * @return boolean|int
     * @throws Exception
     */
    public static function stripe_sale($connection_string = [], $transaction_amount = null, $card_info = [], $shipping_info = [], $additional_parameters = []) {

        // Stripe payment gateway connection
        $stripe_connect = self::connect($connection_string);

        if ($stripe_connect == true) {
            $error = '';
            $payment_info = array_merge($card_info, $shipping_info);
            $validate = self::validate_sale_info($payment_info);            
            if ($validate->check()) {
                try {

                    $params = [];
                    $options = [];
                    // Amount multiply with 100 for exact amount transaction
                    $params['amount'] = $transaction_amount * 100;
                    $params['currency'] = $card_info['currency'];
                    
                    // For Caputre True -> Amount settled to merchant
                    $params['capture'] =TRUE;
                    $params['card']['number'] = str_replace(' ', '', $card_info['card_number']);
                    $params['card']['exp_month'] = $card_info['expirationMonth'];
                    $params['card']['exp_year'] = $card_info['expirationYear'];
                    $params['card']['cvc'] = str_replace(' ', '', $card_info['cvv']);
                    
                    // charge a credit card
                    $stripe_result = \Stripe\Charge::create($params, $options);
                    $stripe_result->jsonSerialize();
                    
                } catch (Stripe_CardError $e) {
                    $error = $e->getMessage();
                } catch (Stripe_InvalidRequestError $e) {
                    // Invalid parameters were supplied to Stripe's API
                    $error = $e->getMessage();
                } catch (Stripe_AuthenticationError $e) {
                    // Authentication with Stripe's API failed
                    $error = $e->getMessage();
                } catch (Stripe_ApiConnectionError $e) {
                    // Network communication with Stripe failed
                    $error = $e->getMessage();
                } catch (Stripe_Error $e) {
                    // Display a very generic error to the user, and maybe send
                    // yourself an email
                    $error = $e->getMessage();
                } catch (Exception $e) {
                    // Something else happened, completely unrelated to Stripe
                    $error = $e->getMessage();
                }


                $payment_response = [];
                $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
                $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';
                
                // Transaction success response
                if (isset($stripe_result['paid']) && $stripe_result['paid'] == 1 && $error == '') {                   
                    $payment_response['payment_response'] = $stripe_result['paid'];
                    $payment_response['TRANSACTIONID'] = $stripe_result['id'];
                    $payment_response['payment_status'] = 1;
                    $payment_response['cardType'] = $stripe_result['source']['brand'];
                }

                // Transaction failure response
                else {
                    $payment_response['payment_response'] = $error;
                    $payment_response['payment_status'] = 0;
                }
                return $payment_response;
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Stripe_payment_gateway_not_properly configured or Stripe payment gateway not active');
        }
    }

    /**
     * Stripe Preauthorization process
     * 
     * @param type $connection_string
     * @param type $transaction_amount
     * @param type $card_info
     * @param type $shipping_info
     * @param type $additional_parameters
     * @return type
     */
    public static function stripe_preauthorization($connection_string = [], $transaction_amount = null, $card_info = [], $shipping_info = [], $additional_parameters = []) {
            
        // Stripe payment gateway connection
        $stripe_connect = self::connect($connection_string);
        
        if ($stripe_connect == true) {
            $error = '';
            $payment_info = array_merge($card_info, $shipping_info);            
            $validate = self::validate_sale_info($payment_info);            
            if ($validate->check()) {
                try {

                    $params = [];
                    $options = [];
                    if($card_info['currency']=='INR' && $transaction_amount==1){
                        $transaction_amount= $transaction_amount *35;
                    }
                    // Amount multiply with 100 for exact amount transaction
                    $params['amount'] = $transaction_amount * 100;
                    $params['currency'] = $card_info['currency'];
                    
                    // For Caputre False -> Amount just authorized only not settled to merchant
                    $params['capture'] =FALSE;
                    $params['card']['number'] = str_replace(' ', '', $card_info['card_number']);
                    $params['card']['exp_month'] = $card_info['expirationMonth'];
                    $params['card']['exp_year'] = $card_info['expirationYear'];
                    $params['card']['cvc'] = str_replace(' ', '', $card_info['cvv']);
                    
                    // charge a credit card
                    $stripe_result = \Stripe\Charge::create($params, $options);                    
                    $failure_code=$stripe_result->failure_code;
                    $stripe_result->jsonSerialize();
                    
                } catch (Stripe_CardError $e) {
                    $error = $e->getMessage();
                } catch (Stripe_InvalidRequestError $e) {
                    // Invalid parameters were supplied to Stripe's API
                    $error = $e->getMessage();
                } catch (Stripe_AuthenticationError $e) {
                    // Authentication with Stripe's API failed
                    $error = $e->getMessage();
                } catch (Stripe_ApiConnectionError $e) {
                    // Network communication with Stripe failed
                    $error = $e->getMessage();
                } catch (Stripe_Error $e) {
                    // Display a very generic error to the user, and maybe send
                    // yourself an email
                    $error = $e->getMessage();
                } catch (Exception $e) {
                    // Something else happened, completely unrelated to Stripe
                    $error = $e->getMessage();
                }


                $payment_response = [];
                $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
                $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';
                
                // Transaction success response
                if (isset($stripe_result['paid']) && $stripe_result['paid'] == 1 && $error == '') {                   
                    $payment_response['payment_response'] = $stripe_result['paid'];
                    $payment_response['TRANSACTIONID'] = $stripe_result['id'];
                    $payment_response['payment_status'] = 1;
                    $payment_response['cardType'] = $stripe_result['source']['brand'];
                    $payment_response['preauthorize_amount']=$transaction_amount;
                }

                // Transaction failure response
                else {
                    $payment_response['payment_response'] = $error;
                    $payment_response['payment_status'] = 0;
                    $payment_response['preauthorize_amount']=$transaction_amount;
                }
                return $payment_response;
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Stripe_payment_gateway_not_properly configured or Stripe payment gateway not active');
        }
    }

    /**
     * Stripe void transaction
     * 
     * @param type $connection_string
     * @param type $transactionId
     * @return boolean|int
     * @throws Exception
     */
    public static function stripe_void($connection_string = [], $transactionId = '') {
        // Stripe payment gateway connection
        $stripe_connect = self::connect($connection_string);
        if ($stripe_connect == true) {
            $error = '';
            $validate = self::validate_void_settlement_info(['transactionId' => $transactionId]);

            if ($validate->check()) {
                try {                    
                    // Stripe payment void to customer use refund  method
                    $stripe_result = \Stripe\Refund::create(["charge"=>$transactionId]);                    
                    $stripe_result->jsonSerialize();
                    $stripe_result_status= isset($stripe_result->status)?$stripe_result->status:'';
                    
               } catch (Stripe_CardError $e) {
                    $error = $e->getMessage();
                } catch (Stripe_InvalidRequestError $e) {
                    // Invalid parameters were supplied to Stripe's API
                    $error = $e->getMessage();
                } catch (Stripe_AuthenticationError $e) {
                    // Authentication with Stripe's API failed
                    $error = $e->getMessage();
                } catch (Stripe_ApiConnectionError $e) {
                    // Network communication with Stripe failed
                    $error = $e->getMessage();
                } catch (Stripe_Error $e) {
                    // Display a very generic error to the user, and maybe send
                    // yourself an email
                    $error = $e->getMessage();
                } catch (Exception $e) {
                    // Something else happened, completely unrelated to Stripe
                    $error = $e->getMessage();
                }

                $payment_response = [];
                $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
                $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';
                
                // Transaction success response
                 //if (isset($stripe_result['paid']) && $stripe_result['paid'] == 1 && $error == '') {                   
                if($stripe_result_status=="succeeded"){
                    $payment_response['payment_response'] = $stripe_result_status;
                    $payment_response['TRANSACTIONID'] = $stripe_result->charge;
                    $payment_response['payment_status'] = 1;
                    $payment_response['cardType'] = '';
                }
                // Transaction failure response
                else{
                    $payment_response['payment_response'] = $error;
                    $payment_response['payment_status'] = 0;
                }
                return $payment_response;
            } else {
                $errors = $validate->errors('errors');
                throw new Exception($errors);
                return false;
            }
        } else {
            throw new Exception('Stripe_payment_gateway_not_properly configured or stripe payment gateway not active');
        }
    }

    /**
     * Stripe Settlement transaction
     * 
     * @param type $connection_string
     * @param type $transactionId
     * @param type $amount
     * @return boolean|int
     * @throws Exception
     */
    public static function stripe_settlement($connection_string = [], $transactionId = '', $amount = null) {
        // Stripe payment gateway connection
        $stripe_connect = self::connect($connection_string);

        if ($stripe_connect == true) {
            $error = '';
           // $payment_info = array_merge($card_info, $shipping_info);
            $validate = self::validate_void_settlement_info(['transactionId' => $transactionId]);
            
            if ($validate->check()) {
                try {

                    $params = [];
                    $options = [];
                    // Amount multiply with 100 for exact amount transaction                   
                    $transaction_amount = $amount * 100;
                    // Stripe payment settled to merchant use retrieve  method
                    $stripe_result = \Stripe\Charge::retrieve($transactionId);                    
                    $stripe_result->capture(array("amount"=>$transaction_amount));
                    $stripe_result->jsonSerialize();
                 
                } catch (Stripe_CardError $e) {
                    $error = $e->getMessage();
                } catch (Stripe_InvalidRequestError $e) {
                    // Invalid parameters were supplied to Stripe's API
                    $error = $e->getMessage();
                } catch (Stripe_AuthenticationError $e) {
                    // Authentication with Stripe's API failed
                    $error = $e->getMessage();
                } catch (Stripe_ApiConnectionError $e) {
                    // Network communication with Stripe failed
                    $error = $e->getMessage();
                } catch (Stripe_Error $e) {
                    // Display a very generic error to the user, and maybe send
                    // yourself an email
                    $error = $e->getMessage();
                } catch (Exception $e) {
                    // Something else happened, completely unrelated to Stripe
                    $error = $e->getMessage();
                }

                
                $payment_response = [];
                $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
                $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';
                
                // Transaction success response
                if (isset($stripe_result['paid']) && $stripe_result['paid'] == 1 && $error == '') {                                        
                    $payment_response['payment_response'] = $stripe_result['paid'];
                    $payment_response['TRANSACTIONID'] = $stripe_result['id'];
                    $payment_response['payment_status'] = 1;
                    $payment_response['transaction_status']='';
                    $payment_response['cardType'] = $stripe_result['source']['brand'];
                    
                }
                // Transaction failure response
                else {
                    $payment_response['payment_response'] = $error;
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
            throw new Exception('Stripe_payment_gateway_not_properly configured or Stripe payment gateway not active');
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
                        ->rule('currency', 'not_empty')
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
