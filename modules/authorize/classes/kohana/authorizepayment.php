<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Kohana Authorize payment gateway module
 *
 * @uses       Authorize (v1.0)
 * @package    Custom
 * @author     NDOT Team 
 * @copyright  (c) 2017-2020 Ndot Team 
 */
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

//define("AUTHORIZENET_LOG_FILE", "authorize-net.log");
class Kohana_Authorizepayment {

    /**
     * Declare static variable
     * 
     * @var type 
     */
    protected static $_authorize_gateway_connection;

    public function __construct() {
        
    }

   /**
    * Authorize payment gateway connection establishment
    * 
    * @param type $connection_string
    * @param type $transactionRequestType
    * @return boolean
    * @throws Exception
    */
    public static function connect($connection_string = [], $transactionRequestType = '') {

        // Load default configuration
        // ($config === NULL) AND $config = Kohana::$config->load('emal');

        if (isset($connection_string[0]['payment_method'])) {
            $pay_type = $connection_string[0]['payment_method'];
            try {
                // Authorize sandbox environment 
                if ($pay_type == "T") {


                    $Merchant_loginId = $connection_string[0]['payment_gateway_username'];
                    $Merchant_transaction_key = $connection_string[0]['payment_gateway_password'];
                    $privateKey = $connection_string[0]['payment_gateway_signature'];

                    // Common setup for API credentials
                    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();

                    $merchantAuthentication->setName($Merchant_loginId);
                    $merchantAuthentication->setTransactionKey($Merchant_transaction_key);
                    $refId = 'ref' . time();
                    $request = new AnetAPI\CreateTransactionRequest();
                    $request->setMerchantAuthentication($merchantAuthentication);
                    $request->setRefId($refId);
                    $request->setTransactionRequest($transactionRequestType);

                    $controller = new AnetController\CreateTransactionController($request);
                    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
                }
                // Authorize production environment 
                else if ($pay_type == "L") {

                    $Merchant_loginId = $connection_string[0]['live_payment_gateway_username'];
                    $Merchant_transaction_key = $connection_string[0]['live_payment_gateway_password'];
                    $privateKey = $connection_string[0]['live_payment_gateway_signature'];

                    // Common setup for API credentials
                    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();

                    $merchantAuthentication->setName($Merchant_loginId);
                    $merchantAuthentication->setTransactionKey($Merchant_transaction_key);
                    $refId = 'ref' . time();
                    
                    $request = new AnetAPI\CreateTransactionRequest();
                    $request->setMerchantAuthentication($merchantAuthentication);
                    $request->setRefId($refId);
                    $request->setTransactionRequest($transactionRequestType);

                    $controller = new AnetController\CreateTransactionController($request);
                    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
                } else {
                    throw new Exception('payment method is not valid');
                }
                // Authorize configuration setup 
                return $response;
            } catch (Exception $message) {
                echo $message;
                exit;
            }
        } else {
            throw new Exception('Authorize_payment_gateway_not_properly configured or authorize payment gateway not active');
            return false;
        }
    }

    /**
     * Authorize payment sale transaction 
     * 
     * @param type $connection_string
     * @param type $transaction_amount
     * @param type $card_info
     * @param type $shipping_info
     * @param type $additional_parameters
     * @return boolean|int
     * @throws Exception
     */
    public static function authorize_sale($connection_string = [], $transaction_amount = null, $card_info = [], $shipping_info = [], $additional_parameters = []) {

        $payment_info = array_merge($card_info, $shipping_info);
        $validate = self::validate_sale_info($payment_info);
        $payment_response = [];
        if ($validate->check()) {
            try {
                // Create the payment data for a credit card
                $creditCard = new AnetAPI\CreditCardType();
                $creditCard->setCardNumber(str_replace(' ', '', $card_info['card_number']));
                $creditCard->setExpirationDate($card_info['expirationMonth'] . $card_info['expirationYear']);
                $creditCard->setCardCode(str_replace(' ', '', $card_info['cvv']));
                $paymentOne = new AnetAPI\PaymentType();
                $paymentOne->setCreditCard($creditCard);
                $order = new AnetAPI\OrderType();
                $order->setDescription("New Item");
                // Set the customer's Bill To address
                $customerAddress = new AnetAPI\CustomerAddressType();
                $customerAddress->setFirstName($shipping_info['firstName']);
                $customerAddress->setLastName(isset($shipping_info['lastName']) ? $shipping_info['lastName'] : '');
                $customerAddress->setCompany(isset($shipping_info['company']) ? $shipping_info['company'] : '');
                $customerAddress->setAddress(isset($shipping_info['street']) ? $shipping_info['street'] : '');
                $customerAddress->setCity(isset($shipping_info['city']) ? $shipping_info['city'] : '');
                $customerAddress->setState(isset($shipping_info['state']) ? $shipping_info['state'] : '');
                $customerAddress->setZip(isset($shipping_info['zipcode']) ? $shipping_info['zipcode'] : '');
                $customerAddress->setCountry(isset($shipping_info['country_code']) ? $shipping_info['country_code'] : '');
                // Set the customer's identifying information
                $CustomerData = new AnetAPI\CustomerDataType();
                $CustomerData->setType("individual");
                $CustomerData->setId(isset($shipping_info['phone']) ? $shipping_info['phone'] : '');
                $CustomerData->setEmail(isset($shipping_info['email']) ? $shipping_info['email'] : '');
                // Create a TransactionRequestType object
                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("authCaptureTransaction");
                $transactionRequestType->setAmount($transaction_amount);
                $transactionRequestType->setOrder($order);
                $transactionRequestType->setPayment($paymentOne);
                $transactionRequestType->setBillTo($customerAddress);
                $transactionRequestType->setCustomer($CustomerData);

                // Auhtorize payment gateway connection & response
                $response = self::connect($connection_string,$transactionRequestType);
            } catch (Exception $message) {
                echo $message;
                exit;
            }



            $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
            $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';


            if ($response != null) {
                // Transaction success response
                if ($response->getMessages()->getResultCode() == "Ok") {
                    $tresponse = $response->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        /*  echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
                          echo " Void transaction SUCCESS AUTH CODE: " . $tresponse->getAuthCode() . "\n";
                          echo " Void transaction SUCCESS TRANS ID  : " . $tresponse->getTransId() . "\n";
                          echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n";
                          echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n"; */
                        $payment_response['payment_response'] = $tresponse->getResponseCode();
                        $payment_response['CORRELATIONID'] = $tresponse->getAuthCode();
                        $payment_response['TRANSACTIONID'] = $tresponse->getTransId();
                        $payment_response['payment_status'] = 1;
                        $payment_response['cardType'] = $tresponse->getAccountType();
                    }
                    // Transaction failure response
                    else {
                        if ($tresponse->getErrors() != null) {
                            /*  echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                              echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n"; */
                            $payment_response['payment_response'] = $tresponse->getErrors()[0]->getErrorText();
                            $payment_response['payment_status'] = 0;
                        }
                    }
                }
                // Transaction failure response
                else {
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        /* echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                          echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n"; */
                        $payment_response['payment_response'] = $tresponse->getErrors()[0]->getErrorText();
                        $payment_response['payment_status'] = 0;
                    } else {
                        /* echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                          echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n"; */
                        $payment_response['payment_response'] = $response->getMessages()->getMessage()[0]->getText();
                        $payment_response['payment_status'] = 0;
                    }
                }
            } else {
                echo "No response returned \n";
                $payment_response['payment_status'] = 0;
            }
        } else {
            $errors = $validate->errors('errors');
            throw new Exception($errors);
            return false;
        }
        return $payment_response;
    }

    /**
     * Authorize Preauthorization process
     * 
     * @param type $connection_string
     * @param type $transaction_amount
     * @param type $card_info
     * @param type $shipping_info
     * @param type $additional_parameters
     * @return type
     */
    public static function authorize_preauthorization($connection_string = [], $transaction_amount = null, $card_info = [], $shipping_info = [], $additional_parameters = []) {
        $payment_info = array_merge($card_info, $shipping_info);
        $validate = self::validate_sale_info($payment_info);

        if ($validate->check()) {
            try {
                // Create the payment data for a credit card
                $creditCard = new AnetAPI\CreditCardType();
                $creditCard->setCardNumber(str_replace(' ', '', $card_info['card_number']));
                $creditCard->setExpirationDate($card_info['expirationMonth'] . $card_info['expirationYear']);
                $creditCard->setCardCode(str_replace(' ', '', $card_info['cvv']));
                $paymentOne = new AnetAPI\PaymentType();
                $paymentOne->setCreditCard($creditCard);
                $order = new AnetAPI\OrderType();
                $order->setDescription("New Item");
                // Set the customer's Bill To address
                $customerAddress = new AnetAPI\CustomerAddressType();
                $customerAddress->setFirstName($shipping_info['firstName']);
                $customerAddress->setLastName(isset($shipping_info['lastName']) ? $shipping_info['lastName'] : '');
                $customerAddress->setCompany(isset($shipping_info['company']) ? $shipping_info['company'] : '');
                $customerAddress->setAddress(isset($shipping_info['street']) ? $shipping_info['street'] : '');
                $customerAddress->setCity(isset($shipping_info['city']) ? $shipping_info['city'] : '');
                $customerAddress->setState(isset($shipping_info['state']) ? $shipping_info['state'] : '');
                $customerAddress->setZip(isset($shipping_info['zipcode']) ? $shipping_info['zipcode'] : '');
                $customerAddress->setCountry(isset($shipping_info['country_code']) ? $shipping_info['country_code'] : '');
                // Set the customer's identifying information
                $CustomerData = new AnetAPI\CustomerDataType();
                $CustomerData->setType("individual");
                $CustomerData->setId(isset($shipping_info['phone']) ? $shipping_info['phone'] : '');
                $CustomerData->setEmail(isset($shipping_info['email']) ? $shipping_info['email'] : '');
                // Create a TransactionRequestType object
                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("authOnlyTransaction");
                $transactionRequestType->setAmount($transaction_amount);
                $transactionRequestType->setOrder($order);
                $transactionRequestType->setPayment($paymentOne);
                $transactionRequestType->setBillTo($customerAddress);
                $transactionRequestType->setCustomer($CustomerData);

                // Auhtorize payment gateway connection With waiting for response
                $response = self::connect($connection_string,$transactionRequestType);
            } catch (Exception $message) {
                echo $message;
                exit;
            }
            $payment_response = [];
            $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
            $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';


            if ($response != null) {
                // Transaction success response
                if ($response->getMessages()->getResultCode() == "Ok") {
                    $tresponse = $response->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        /*  echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
                          echo " Void transaction SUCCESS AUTH CODE: " . $tresponse->getAuthCode() . "\n";
                          echo " Void transaction SUCCESS TRANS ID  : " . $tresponse->getTransId() . "\n";
                          echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n";
                          echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n"; */
                        $payment_response['payment_response'] = $tresponse->getResponseCode();
                        $payment_response['CORRELATIONID'] = $tresponse->getAuthCode();
                        $payment_response['TRANSACTIONID'] = $tresponse->getTransId();
                        $payment_response['payment_status'] = 1;
                        $payment_response['cardType'] = $tresponse->getAccountType();
                    }
                    // Transaction failure response
                    else {
                        if ($tresponse->getErrors() != null) {
                            /*  echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                              echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n"; */
                            $payment_response['payment_response'] = $tresponse->getErrors()[0]->getErrorText();
                            $payment_response['payment_status'] = 0;
                        }
                    }
                }
                // Transaction failure response
                else {
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        /* echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                          echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n"; */
                        $payment_response['payment_response'] = $tresponse->getErrors()[0]->getErrorText();
                        $payment_response['payment_status'] = 0;
                    } else {
                        /* echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                          echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n"; */
                        $payment_response['payment_response'] = $response->getMessages()->getMessage()[0]->getText();
                        $payment_response['payment_status'] = 0;
                    }
                }
            } else {
                echo "No response returned \n";
                $payment_response['payment_status'] = 0;
            }
        } else {
            $errors = $validate->errors('errors');
            throw new Exception($errors);
            return false;
        }
        return $payment_response;
    }

    /**
     * Authorize void transaction
     * 
     * @param type $connection_string
     * @param type $transactionId
     * @return boolean|int
     * @throws Exception
     */
    public static function authorize_void($connection_string = [], $transactionId = '') {

        $validate = self::validate_void_settlement_info(['transactionId' => $transactionId]);

        if ($validate->check()) {
            try {
                //create a transaction
                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("voidTransaction");
                $transactionRequestType->setRefTransId($transactionId);

                // Auhtorize payment gateway connection & response
                $response = self::connect($connection_string,$transactionRequestType);
            } catch (Exception $message) {
                echo $message;
                exit;
            }

            $payment_response = [];
            $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
            $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';

            if ($response != null) {
                // Transaction success response
                if ($response->getMessages()->getResultCode() == "Ok") {
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        $payment_response['payment_response'] = $tresponse->getResponseCode();
                        $payment_response['CORRELATIONID'] = $tresponse->getAuthCode();
                        $payment_response['TRANSACTIONID'] = $tresponse->getTransId();
                        $payment_response['payment_status'] = 1;
                        $payment_response['cardType'] = $tresponse->getAccountType();
                    }
                    // Transaction failure response
                    else {
                        $payment_response['payment_response'] = $tresponse->getErrors()[0]->getErrorText();
                        $payment_response['payment_status'] = 0;
                    }
                } else {
                    // Transaction failure response
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        $payment_response['payment_response'] = $tresponse->getErrors()[0]->getErrorText();
                        $payment_response['payment_status'] = 0;
                    } else {
                        $payment_response['payment_response'] = $response->getMessages()->getMessage()[0]->getText();
                        $payment_response['payment_status'] = 0;
                    }
                }
            } else {
                echo "No response returned \n";
                $payment_response['payment_status'] = 0;
            }
            return $payment_response;
        } else {
            $errors = $validate->errors('errors');
            throw new Exception($errors);
            return false;
        }
    }

    /**
     * Authorize Settlement transaction
     * 
     * @param type $connection_string
     * @param type $transactionId
     * @param type $amount
     * @return boolean|int
     * @throws Exception
     */
    public static function authorize_settlement($connection_string = [], $transactionId = '', $amount = null) {

        $validate = self::validate_void_settlement_info(['transactionId' => $transactionId]);

        if ($validate->check()) {
            try {
                // Now capture the previously authorized  amount

                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("priorAuthCaptureTransaction");
                $transactionRequestType->setRefTransId($transactionId); 
                if($amount!=null){
                $transactionRequestType->setAmount($amount);
                }
                // Auhtorize payment gateway connection & response
                $response = self::connect($connection_string,$transactionRequestType);
            } catch (Exception $message) {
                echo $message;
                exit;
            }

            $payment_response = [];
            $payment_response['payment_method'] = isset($connection_string[0]['payment_method']) ? $connection_string[0]['payment_method'] : '';
            $payment_response['payment_gateway_id'] = isset($connection_string[0]['payment_gateway_id']) ? $connection_string[0]['payment_gateway_id'] : '';

            if ($response != null) {
                if ($response->getMessages()->getResultCode() =="Ok") {
                    $tresponse = $response->getTransactionResponse();
                    // Transaction success response
                    if ($tresponse != null && $tresponse->getMessages() != null) {

                        $payment_response['payment_response'] = $tresponse->getResponseCode();
                        $payment_response['TRANSACTIONID'] = $tresponse->getRefTransId();
                        $payment_response['transaction_status'] = $tresponse->getMessages()[0]->getDescription();
                        $payment_response['payment_status'] = 1;
                    } else {
                        // Transaction failure response
                        $payment_response['payment_response'] = $tresponse->getErrors()[0]->getErrorText();
                        $payment_response['payment_status'] = 0;
                        $payment_response['transaction_status']='';
                    }
                } else {
                    // Transaction failure response
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getErrors() != null) {

                        $payment_response['payment_response'] = $tresponse->getErrors()[0]->getErrorText();
                        $payment_response['payment_status'] = 0;
                        $payment_response['transaction_status']='';
                    } else {

                        $payment_response['payment_response'] = $response->getMessages()->getMessage()[0]->getText();
                        $payment_response['payment_status'] = 0;
                        $payment_response['transaction_status']='';
                    }
                }
            } else {
                echo "No response returned \n";
                $payment_response['payment_status'] = 0;
            }

            return $payment_response;
        } else {
            $errors = $validate->errors('errors');
            throw new Exception($errors);
            return false;
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
