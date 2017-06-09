<?php

defined('SYSPATH') OR die('No Direct Script Access');
/* * **************************************************************
 * Contains Package Model details
 * @Package: Taximobility
 * @Author: taxi Team
 * @URL : taximobility.com
 * ****************************************************************** */

Class Model_TaximobilityPackage extends Model {

    public function __construct() {
        $this->session = Session::instance();
        $this->company_id = $this->session->get('company_id');
        $this->currentdate = Commonfunction::getCurrentTimeStamp();
        $this->currentdate_bytimezone = Commonfunction::createdateby_user_timezone();
        //MongoDB Instance
        $this->mongo_db = MangoDB::instance('default');
    }

    /**
     * Available driver count Functionalities 
     * 
     * @return type
     */
    public function checkdrivercount() {
        $options = [
            'projection' => [
                'driver_count' => 1,
            ]
        ];
        $query_result = $this->mongo_db->find(MDB_SITEINFO, ['_id' => 1], $options);
        $result = !empty($query_result) ? $query_result : array();
        return $result;
    }

    /**
     * To check added driver count Functionalities
     * 
     * @return type
     */
    public function check_current_driver_count() {
        $options = [
            'projection' => [
                '_id' => 1,
                'name' => 1
            ]
        ];
        $query_result = $this->mongo_db->find(MDB_PEOPLE, ['user_type' => 'D'], $options);

        $result = !empty($query_result) ? $query_result : array();
        return $result;
    }
    /**
     * 
     * @param type $post
     * @return type
     */
    public function payment_data($post) {

        if ($post) {
            $product_id = 1;           
            
            $subscription_cost = $post['subscription_cost'];
            $setup_cost = $post['setup_cost'];
            $total_amount = floatval($post['total_amount']);

            $email = $post['email'];
            $subject = 'Direct payment user';            
            $phone = $post['phone'];
            $package_type = $post['package_type'];
            $name = $post['name'];
            $payment_terms = $post['payment_terms'];
            $createdate = $post['createdate'];
            $startdate = $post['startdate'];
            $expirydate = $post['expirydate'];

            $invoice_id = Commonfunction::get_auto_id(MDB_PACKAGE_INFO);
            $package_data = array('_id' => $invoice_id,
                    'purchase_inv_id' => "NDOT-P-" . $invoice_id,
                    'package_type' => (int) $package_type,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'subject' => $subject,
                    'product_id' => 1,
                    'subscription_cost' => (double) $subscription_cost,
                    'setup_cost' => (double) $setup_cost,
                    'amount' => (double) $total_amount,
                    'payment_terms' => (int) $payment_terms,
                    'createddate' => Commonfunction::MongoDate(strtotime($createdate)),
                    'startdate' => Commonfunction::MongoDate(strtotime($startdate)),
                    'expirydate' => Commonfunction::MongoDate(strtotime($expirydate)),
                    'paid_status' => 0
                );
            if (isset($post['service_tax'])) {
                $service_tax = $post['service_tax'];
                $service_tax_cost = $post['service_tax_cost'];
                $package_additional_data = array(
                    'service_tax' => $service_tax,
                    'service_tax_cost' => $service_tax_cost
                );
                $package_data= array_merge($package_data,$package_additional_data);
            }
            
            $package_result = $this->mongo_db->insertOne(MDB_PACKAGE_INFO, $package_data);
            return $invoice_id;
        }
    }

    /**
     * Shipping and Card Details
     * 
     * @return string
     */
    public function billing_card_info_details() {

        $option = [
            'projection' => [
                '_id' => 1,
                'cardnumber' => 1,
                'cvv' => 1,
                'expiry_month' => 1,
                'expiry_year' => 1,
                'firstname' => 1,
                'lastname' => 1,
                'email'=>1,
                'customer_id'=>1,
                'address' => 1,
                'city' => 1,
                'state' => 1,
                'country' => 1,
                'postal_code' => 1,
                'subscription_id'=>1,
                'customer_id'=>1
            ]
        ];
        $result = $this->mongo_db->find(MDB_BILLING_REG_INFO, array('_id' => 1), $option);
        return (!empty($result)) ? $result : '';
    }

    public function billing_registration($post, $billing_info_id) {

        if ($post) {

            $cardnumber = $post['card_number'];
            $cardnumber = encrypt_decrypt('encrypt', $cardnumber);
            
            $expiry_month = $post['expirationMonth'];
            $expiry_year = $post['expirationYear'];
            $cvv = $post['cvv'];


            $firstname = $post['firstName'];
            $lastname = $post['lastname'];
            //$email = $post['email'];
            $address = $post['address'];
            $city = $post['city'];
            $postal_code = $post['postal_code'];


            $country = $post['country'];
            $state = $post['state'];
            $createdate = $post['createdate'];

            $id = Commonfunction::get_auto_id(MDB_BILLING_REG_INFO);
            $billing_data =[                           
                            'firstname' => $firstname,
                            'lastname' => $lastname,                            
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'country' => $country,
                            'postal_code' => $postal_code,
                            'cardnumber' => $cardnumber,
                            'expiry_month' => $expiry_month,
                            'expiry_year' => $expiry_year,
                            'cvv' => $cvv,
                            'updated_date' => Commonfunction::MongoDate(strtotime($createdate))
                        ];
            if ($billing_info_id == '') {
                $billing_data_id = ['_id' => $id,                 
                                    'createddate' => Commonfunction::MongoDate(strtotime($createdate))
                                   ];
                $billing_merge_data= array_merge($billing_data,$billing_data_id);
                $package_result = $this->mongo_db->insertOne(MDB_BILLING_REG_INFO, $billing_merge_data);
            }             
            else{               
                $package_result = $this->mongo_db->updateOne(MDB_BILLING_REG_INFO, ['_id' => 1], array('$set' => $billing_data), array('upsert' => false));
            }
            return $package_result;
        }
    }

    public function billing_info_update($post, $invoice_id, $shipping_id) {

        if ($post) {

            $cardnumber = $post['card_number'];
            $cardnumber = encrypt_decrypt('encrypt', $cardnumber);

            
            $expiry_month = $post['expirationMonth'];
            $expiry_year = $post['expirationYear'];
            $cvv = $post['cvv'];

            $firstname = $post['firstName'];
            $lastname = $post['lastname'];
            $email=$post['email'];
            $address = $post['address'];
            $city = $post['city'];
            $postal_code = $post['postal_code'];

            $country = $post['country'];
            $state = $post['state'];
            $createdate = $post['createdate'];

            $id = Commonfunction::get_auto_id(MDB_BILLING_INFO);
            $card_id = Commonfunction::get_auto_id(MDB_BILLING_REG_INFO);
            $billing_data = array(
                    'purchase_inv_id' => "NDOT-P-" . $invoice_id,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email'=>$email,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'postal_code' => $postal_code,
                    'cardnumber' => $cardnumber,
                    'expiry_month' => $expiry_month,
                    'expiry_year' => $expiry_year,
                    'cvv' => $cvv,
                    'updated_date' => Commonfunction::MongoDate(strtotime($createdate))
                );
            if ($card_id == 1) {
                $billing_date_data = array('_id' => $card_id,                                        
                    'createddate' => Commonfunction::MongoDate(strtotime($createdate)),                   
                );
                $billing_data= array_merge($billing_data,$billing_date_data);
                $package_result = $this->mongo_db->insertOne(MDB_BILLING_REG_INFO, $billing_data);
            } else {              
                $package_result = $this->mongo_db->updateOne(MDB_BILLING_REG_INFO, ['_id' => 1], array('$set' => $billing_data), array('upsert' => false));
            }
            $package_data = array('_id' => $id,               
                'createddate' => Commonfunction::MongoDate(strtotime($createdate))
            );
            $package_data= array_merge($billing_data,$package_data);

            $package_result = $this->mongo_db->insertOne(MDB_BILLING_INFO, $package_data);
            return $package_result;
        }
    }

    public function getadmin_profile_info() {
        $option = [
            'projection' => [
                'name' => 1,
                'lastname' => 1,
                'email' => 1,
                'password'=>1,
                'phone' => 1,
                'address' => 1,
                'timezone' => 1,
                'created_date' => 1,
                'last_login' => 1,
                'postal_code' => 1,
                'login_country' => 1,
                'login_state' => 1,
                'login_city' => 1
            ]
        ];
        $result = $this->mongo_db->find(MDB_PEOPLE, array('user_type' => 'A'), $option);
        if (!empty($result)) {
            foreach ($result as $r) {
                $temp_arr['name'] = isset($r['name']) ? $r['name'] : '';
                $temp_arr['lastname'] = isset($r['lastname']) ? $r['lastname'] : '';
                $temp_arr['email'] = isset($r['email']) ? $r['email'] : '';
                $temp_arr['password'] = isset($r['password']) ? $r['password'] : '';
                $temp_arr['phone'] = isset($r['phone']) ? $r['phone'] : '';
                $temp_arr['address'] = isset($r['address']) ? $r['address'] : '';
                $temp_arr['timezone'] = isset($r['timezone']) ? $r['timezone'] : '';
                $temp_arr['created_date'] = isset($r['created_date']) ? commonfunction::convertphpdate('Y-m-d H:i:s', $r['created_date']) : '';
                $temp_arr['last_login'] = isset($r['last_login']) ? commonfunction::convertphpdate('Y-m-d H:i:s', $r['last_login']) : '';
                $temp_arr['postal_code'] = isset($r['postal_code']) ? $r['postal_code'] : '';
                $temp_arr['login_country'] = isset($r['login_country']) ? $r['login_country'] : '';
                $temp_arr['login_state'] = isset($r['login_state']) ? $r['login_state'] : '';
                $temp_arr['login_city'] = isset($r['login_city']) ? $r['login_city'] : '';
            }
            return $temp_arr;
        } else {
            return '';
        }
        return $result;
    }

    public function update_package_info($package_data = []) {

        
            $update_package_info = [
                /*'txnID' => $package_data['TxnID'],
                'ePGTxnID' => $package_data['ePGTxnID'],
                'currency' => $package_data['currency'],
                'responsecode' => $package_data['ResponseCode'],*/
                
                'paid_status' => (int) $package_data['paid_status'],
                //'response_msg' => $package_data['response_msg'],
                'pay_mode' => $package_data['pay_mode'],
                'business_name' => $package_data['business_name'],
                'expirydate' => Commonfunction::MongoDate(strtotime($package_data['expirydate']))
            ];
        
      if($package_data['subscription_id']!=''){
          $update_paid_info=['subscription_id'=>$package_data['subscription_id'],
                'customer_id'=>$package_data['customer_id']];
          $result_billing = $this->mongo_db->updateOne(MDB_BILLING_REG_INFO, ['_id' => 1], array('$set' => [ 'subscription_id'=>$package_data['subscription_id'],
                'customer_id'=>$package_data['customer_id']]), array('upsert' => false));
          $update_package_info= array_merge($update_package_info,$update_paid_info);
      }

        $result = $this->mongo_db->updateOne(MDB_PACKAGE_INFO, ['_id' => (int) $package_data['invoice_id']], array('$set' => $update_package_info), array('upsert' => true));
        
        $dbname = ALTER_DB_NAME;
        $manager = new MongoDB\Driver\Manager('mongodb://' . ALTER_DB_USER . ':' . ALTER_DB_PWD . '@' . ALTER_DB_HOST . '/' . $dbname . '?authsource=admin');
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $siteinfo = new MongoDB\Driver\BulkWrite();
        $options = [
            'projection' => [
                '_id' => 1,
            ],
            'sort' => [
                '_id' => -1
            ],
            'limit' => 1
        ];
        $filter = [];
        $query = new MongoDB\Driver\Query($filter, $options);
        $cursor = $manager->executeQuery($dbname . '.' . MDB_PACKAGE_ACCT_TRANS, $query);
        $id = '';
        foreach ($cursor as $r) {
            $id = $r->_id;
        }
        if ($id == '') {
            $id = 1;
        } else {
            $id = $id + 1;
        }

        $update_package_info = [
                '_id' => $id,
                'invoice_ref_id' => $package_data['invoice_id'],
                'business_name' => $package_data['business_name'],
                'name' => $package_data['name'],
                'phone' => $package_data['phone'],
                'email' => $package_data['email'],
                'country' => $package_data['country'],
                'package_type' => (int) $package_data['package_type'],
                'payment_terms' => (int) $package_data['payment_terms'],
                'subscription_id'=>$package_data['subscription_id'],
                'customer_id'=>$package_data['customer_id'],
                /*'txnID' => $package_data['TxnID'],
                'ePGTxnID' => $package_data['ePGTxnID'],
                'currency' => $package_data['currency'],
                'responsecode' => $package_data['ResponseCode'],*/
                'amount' => (double) $package_data['amount'],
                'paid_status' => (int) $package_data['paid_status'],
                //'response_msg' => $package_data['response_msg'],
                'pay_mode' => $package_data['pay_mode'],
                'createddate' => Commonfunction::MongoDate(strtotime(PACKAGE_UPGRADE_TIME)),
                'expirydate' => Commonfunction::MongoDate(strtotime($package_data['expirydate']))
            ];
        if ($package_data['service_tax_cost'] != 0) {                   
            $update_package_info_additional = [                
                'service_tax' => (int) $package_data['service_tax'],
                'service_tax_cost' => (double) $package_data['service_tax_cost']
            ];
            $update_package_info= array_merge($update_package_info,$update_package_info_additional);
        }
        $siteinfo->insert($update_package_info);
        $result = $manager->executeBulkWrite($dbname . "." . MDB_PACKAGE_ACCT_TRANS, $siteinfo, $writeConcern);
    }

    /**
     * Force profile server post value validation
     * 
     * @param type $arr
     * @param type $uid
     * @return type
     */
    public function editprofile_validate($arr, $uid) {
        return Validation::factory($arr)->rule('firstname', 'not_empty')                        
                                        ->rule('firstname', 'min_length', array(':value','4'))
                                        ->rule('firstname', 'max_length', array(':value','30'))
                                        ->rule('lastname', 'not_empty')                      
                                        ->rule('email', 'not_empty')
                                        ->rule('email', 'email')
                                        ->rule('email', 'max_length', array( ':value','100'))
                                        ->rule('email', 'Model_Edit::checkemail', array(':value',$uid))
                                        ->rule('phone', 'not_empty')
                                        ->rule('phone', 'min_length', array( ':value', '7'))
                                        ->rule('phone', 'max_length', array(':value', '20'))
                                        ->rule('phone', 'contact_phone', array(':value'))
                                        ->rule('phone', 'Model_Edit::checkphone', array(':value',$uid))
                                        ->rule('address', 'not_empty')               
                                        ->rule('country', 'not_empty')
                                        ->rule('state', 'not_empty')
                                        ->rule('city', 'not_empty')
                                        ->rule('user_time_zone', 'not_empty')
                                        ->rule('iso_country_code', 'not_empty')
                                        ->rule('telephone_code', 'not_empty')
                                        ->rule('currency_code', 'not_empty')
                                        ->rule('postal_code', 'not_empty')
                                        ->rule('currency_symbol', 'not_empty');
    }
    /**
     * Force profile updation
     * 
     * @param type $uid
     * @param type $post
     * @return type
     */
    public function edit_people($uid, $post) {

        //MongoDB
        $data_set = array(
            'name' => $post['firstname'],
            'address' => $post['address'],
            'lastname' => $post['lastname'],
            'email' => $post['email'],
            'phone' => $post['phone'],
            'timezone' => $post['user_time_zone'],
            'postal_code' => $post['postal_code'],
            'website_info' => $post['website_info'],            
        );
        $result = $this->mongo_db->updateOne(MDB_PEOPLE, array('_id' => (int) $uid), array('$set' => $data_set), array('upsert' => true));

        $dataset_siteinfo = [
            'user_time_zone' => $post['user_time_zone'],
            'profile_status' => 1
        ];
        $result_siteinfo = $this->mongo_db->updateOne(MDB_SITEINFO, array('_id' => 1), array('$set' => $dataset_siteinfo), array('upsert' => false));
        
        if(PACKAGE_TYPE==3){
        $dataset_domain=['mobile_api_key'=>$post['mobile_api_key']];
        $result_domain = $this->mongo_db->updateOne(MDB_COMPANY_DOMAIN, array('_id' => 1), array('$set' => $dataset_domain), array('upsert' => false));
        }
        
        $dataset_companyinfo = [
            'companydetails.time_zone' => $post['user_time_zone'],
            'companydetails.user_time_zone' => $post['user_time_zone'],
        ];
        $result_companyinfo = $this->mongo_db->updateOne(MDB_COMPANY, array('_id' => 1), array('$set' => $dataset_companyinfo), array('upsert' => false));

        $city_array = array('city_id' => 1,
            'city_name' => $post['city'],
            'zipcode' => $post['postal_code'],
            'city_status' => 'A',
            'city_countryid' => 1,
            'city_stateid' => 1,
            'city_model_fare' => 10,
            'default' => 1
        );

        $state_array = array("state_id" => 1,
            "state_name" => $post['state'],
            "state_countryid" => 1,
            "state_status" => 'A',
            "default" => 1,
            "cityinfo" => array($city_array)
        );

        $country_array = array("_id" => 1,
            "country_name" => $post['country'],
            "iso_country_code" => $post['iso_country_code'],
            "telephone_code" => '+' . $post['telephone_code'],
            "currency_code" => $post['currency_code'],
            "currency_symbol" => $post['currency_symbol'],
            "country_status" => "A",
            "default" => 1,
            "stateinfo" => array($state_array)
        );
        $result_countryinfo = $this->mongo_db->updateOne(MDB_CSC, array('_id' => 1), array('$set' => $country_array), array('upsert' => false));

        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }

    public function get_site_info() {
        $option = [
            'projection' => [
                'profile_status' => 1,
                'package_type' => 1,
                'bill_payment_terms' => 1,
                'expiry_date' => 1,
                'driver_count' => 1,
                'domain_name' => 1,
                'user_time_zone' => 1,
                'create_date' => 1
            ]
        ];
        $result = $this->mongo_db->find(MDB_SITEINFO, [], $option);
        return (!empty($result)) ? $result : '';
    }

    public function upgrade_plan_validate($arr, $userid) {

        return Validation::factory($arr)->rule('cardnumber', 'not_empty')
                        ->rule('expirydate', 'not_empty')
                        ->rule('cvv', 'not_empty')
                        ->rule('firstName', 'not_empty')
                        ->rule('lastname', 'not_empty')
                        ->rule('address', 'not_empty')
                        ->rule('expirydate', 'not_empty')
                        ->rule('postal_code', 'not_empty')
                        ->rule('city', 'not_empty')
                        ->rule('state', 'not_empty')
                        ->rule('country', 'not_empty')
                        ->rule('terms', 'not_empty');
    }

    public function billing_data_validate($arr, $userid) {

        return Validation::factory($arr)->rule('billing-options', 'not_empty');
    }

    public function country_details() {
        ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options = [
            'projection' => [
                '_id' => 1,
                'country_name' => 1,
                'iso_country_code' => 1,
                'telephone_code' => 1,
                'currency_code' => 1,
                'currency_symbol' => 1
            ],
            'sort' => [
                'country_name' => 1
            ]
        ];
        $res = $this->mongo_db->find(MDB_CSC, ['country_status' => 'A'], $options);

        return (!empty($res)) ? $res : array();
    }

    public function update_siteinfo($package_type, $driver_count, $payment_terms, $expiry_date = '') {
        $dataset_siteinfo = [
            'package_type' => $package_type,
            'driver_count' => $driver_count,
            'bill_payment_terms' => $payment_terms,
            'expiry_date' => Commonfunction::MongoDate(strtotime($expiry_date))
        ];
        $result_siteinfo = $this->mongo_db->updateOne(MDB_SITEINFO, array('_id' => 1), array('$set' => $dataset_siteinfo), array('upsert' => false));
    }

    public function get_package_paid_info() {
        $option = [
            'projection' => [
                '_id' => 1,
                'purchase_inv_id' => 1,
                'package_type' => 1,
                'name' => 1,
                'email' => 1,
                'phone' => 1,
                'product_id' => 1,
                'subscription_cost' => 1,
                'setup_cost' => 1,
                'amount' => 1,
                'createddate' => 1,
                'expirydate' => 1
            ],
            'sort'=>[
                '_id'=>-1
            ]
        ];
        $result = $this->mongo_db->find(MDB_PACKAGE_INFO, array('paid_status' => 1), $option);
        if (!empty($result)) {
            return $result;
        } else {
            return array();
        }
        return $result;
    }
     public function get_recent_package_paid_info() {
        $option = [
            'projection' => [
                '_id' => 1,
                'purchase_inv_id' => 1,
                'package_type' => 1,
                'name' => 1,
                'email' => 1,
                'phone' => 1,
                'product_id' => 1,
                'subscription_cost' => 1,
                'setup_cost' => 1,
                'service_tax'=>1,
                'service_tax_cost'=>1,
                'amount' => 1,
                'createddate' => 1,
                'expirydate' => 1,
                'subscription_id'=>1,
                'customer_id'=>1,
                'payment_terms'=>1,
            ],
             'sort'=>[
                '_id'=>-1
                ],
            'limit'=>1
        ];
        
        $result = $this->mongo_db->find(MDB_PACKAGE_INFO, array('paid_status' => 1), $option);
        if (!empty($result)) {            
            return $result;
        } else {
            return array();
        }
        return $result;
    }
    public function get_package_invoice_info($id) {

        $option = [
            'projection' => [
                '_id' => 1,
                'purchase_inv_id' => 1,
                'package_type' => 1,
                'name' => 1,
                'email' => 1,
                'phone' => 1,
                'product_id' => 1,
                'subscription_cost' => 1,
                'setup_cost' => 1,
                'amount' => 1,
                'createddate' => 1,
                'expirydate' => 1,
                'city' => 1,
                'state' => 1,
                'country' => 1,
                'payment_terms' => 1
            ]
        ];

        $filter = array('_id' => (int) $id);
        $result = $this->mongo_db->find(MDB_PACKAGE_INFO, $filter, $option);

        if (!empty($result)) {
            return $result;
        } else {
            return array();
        }

        return $result;
    } 

    public function city_details() {
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$unwind' => '$stateinfo.cityinfo'),
            //array('$match' => array('stateinfo.cityinfo.city_status'=>'A','stateinfo.state_id'=>(int)DEFAULT_STATE,'_id'=>(int)DEFAULT_COUNTRY)),
            array('$match' => array('stateinfo.cityinfo.city_status' => 'A')),
            array('$project' => array('_id' => 0,
                    'city_id' => '$stateinfo.cityinfo.city_id',
                    'city_name' => '$stateinfo.cityinfo.city_name',
                )
            ),
            array(
                '$sort' => array(
                    'country_name' => 1
                ),
            )
        );
        $result = $this->mongo_db->aggregate(MDB_CSC, $ops);
        return (!empty($result['result'])) ? $result['result'] : array();
    }

    public function state_details() {
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$match' => array('stateinfo.state_status' => 'A')),
            //array('$match' => array('stateinfo.state_status'=>'A','_id'=>(int)DEFAULT_COUNTRY)),
            array('$project' => array('_id' => 0,
                    'state_id' => '$stateinfo.state_id',
                    'state_name' => '$stateinfo.state_name',
                )
            ),
            array(
                '$sort' => array(
                    'country_name' => 1
                ),
            )
        );
        $result = $this->mongo_db->aggregate(MDB_CSC, $ops);

        return (!empty($result['result'])) ? $result['result'] : array();
    }

    public static function getstate_details($country_id) {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$match' => array('stateinfo.state_status' => 'A', 'stateinfo.state_countryid' => (int) $country_id)),
            array('$project' => array('_id' => 0,
                    'state_id' => '$stateinfo.state_id',
                    'state_name' => '$stateinfo.state_name',
                    'state_default' => '$stateinfo.default',
                )
            ),
            array(
                '$sort' => array(
                    'country_name' => 1
                ),
            )
        );
        $result = $mongodb->aggregate(MDB_CSC, $ops);
        return (!empty($result['result'])) ? $result['result'] : array();
    }

    public static function getcity_details($country_id, $state_id) {
        //MongoDB
        $mongodb = MangoDB::instance('default');
        $ops = array(
            array('$unwind' => '$stateinfo'),
            array('$unwind' => '$stateinfo.cityinfo'),
            array('$match' => array('stateinfo.cityinfo.city_status' => 'A', 'stateinfo.state_id' => (int) $state_id, '_id' => (int) $country_id)),
            array('$project' => array('_id' => 0,
                    'city_id' => '$stateinfo.cityinfo.city_id',
                    'city_name' => '$stateinfo.cityinfo.city_name',
                )
            ),
            array(
                '$sort' => array(
                    'country_name' => 1
                ),
            )
        );
        $result = $mongodb->aggregate(MDB_CSC, $ops);
        return (!empty($result['result'])) ? $result['result'] : array();
    }

    /* public function sms_settings() {

        $option = [
            'projection' => [
                '_id' => 1,
                'sms_account_id' => 1,
                'sms_auth_token' => 1,
                'sms_from_number' => 1,
                'sms_from_number1' => 1,
                'http_link' => 1,
                'username' => 1,
                'password' => 1,
                'default' => 1,
                'default1' => 1
            ],
        ];
        $res = $this->mongo_db->find(MDB_SMS_SETTINGS, array(), $option);
        $result = !empty($res) ? $res[0] : array();        
        return $result;
    } */
    
    public function sms_settings()
    {      
		$option = [
			'projection' => [
				'_id' => 1,
				'sms_account_id' => 1,
				'sms_auth_token' => 1,
				'sms_from_number' => 1               
			]
		];
        $result = $this->mongo_db->find(MDB_SMS_SETTINGS, array('_id' => 1), $option);
        if (!empty($result)) {
            return $result;
        } else {
            return '';
        }
        return $result;
    }

    /*public function update_sms_settings($post_values, $sms_id) {
        
        $update_array['sms_account_id'] = isset($post_values['sms_account_id']) ? $post_values['sms_account_id']:'';
        $update_array['sms_auth_token'] = isset($post_values['sms_auth_token']) ? $post_values['sms_auth_token']:'';
        $update_array['sms_from_number'] = isset($post_values['sms_from_number']) ? $post_values['sms_from_number']:'';
        $update_array['http_link'] = isset($post_values['http_link']) ? $post_values['http_link']:'';
        $update_array['sms_from_number1'] = isset($post_values['sms_from_number1']) ? $post_values['sms_from_number1']:'';
        $update_array['username'] = isset($post_values['username']) ? $post_values['username']:'';
        $update_array['password'] = isset($post_values['password']) ? $post_values['password']:'';
        $update_array['default'] = isset($post_values['default']) ? $post_values['default']:'';
        $update_array['default1'] = isset($post_values['default1']) ? $post_values['default1']:'';
        # set default
        $default_sms = isset($post_values['default_sms']) ? $post_values['default_sms']:'';
        $update_array['default'] = $update_array['default1'] = 0;
        if($default_sms == 2)
			$update_array['default1'] = 1;
		else
			$update_array['default'] = 1;
				
        $update_array['updatedate'] = Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone));        
        
        if ($sms_id != '') {
            $result = $this->mongo_db->updateOne(MDB_SMS_SETTINGS, ['_id' => (int) $sms_id], array('$set' => $update_array), array('upsert' => false));
        } else {
			$update_array['_id'] = 1;
			$update_array['createddate'] = Commonfunction::MongoDate(strtotime($this->currentdate_bytimezone));
            $result = $this->mongo_db->insertOne(MDB_SMS_SETTINGS, $update_array);
        }
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    } */
    
    public function update_sms_settings($post_value_array, $id, $sms_id)
    {
       $createdate=date('d-m-Y H:i:s');
        if ($sms_id != '') {
             $dataset_smsinfo = [            
            'sms_account_id' => $post_value_array['sms_account_id'],
            'sms_auth_token' => $post_value_array['sms_auth_token'],
            'sms_from_number' => $post_value_array['sms_from_number'],
            'updatedate' => Commonfunction::MongoDate(strtotime($createdate))
        ];
              $result = $this->mongo_db->updateOne(MDB_SMS_SETTINGS, ['_id' => (int) $sms_id], array('$set' => $dataset_smsinfo), array('upsert' => false));
            
        } else {
            
             $id = Commonfunction::get_auto_id(MDB_SMS_SETTINGS);
            $sms_settings_data = array('_id' => $id,
                                       'sms_account_id' => $post_value_array['sms_account_id'],
                                       'sms_auth_token' => $post_value_array['sms_auth_token'],
                                       'sms_from_number' => $post_value_array['sms_from_number'],      
                                       'company_id'=>$id,
                                        'createddate' => Commonfunction::MongoDate(strtotime($createdate)),
                                        'updatedate' => Commonfunction::MongoDate(strtotime($createdate))
                                        );

            $result = $this->mongo_db->insertOne(MDB_SMS_SETTINGS, $sms_settings_data);
         
        }
        //return $package_result;
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }

    public function validate_update_smssettings($arr = "") {
        return Validation::factory($arr)->rule('sms_account_id', 'not_empty')
                        ->rule('sms_auth_token', 'not_empty')
                        ->rule('sms_from_number', 'numeric')
                        ->rule('sms_from_number', 'not_empty');
    }

    public function google_settings() {

        $option = [
            'projection' => [
                '_id' => 1,
                'ios_google_map_key' => 1,
                'ios_google_geo_key' => 1,
                'google_timezone_api_key' => 1,
                'web_google_map_key' => 1,
                'web_google_geo_key' => 1,
                'android_google_key' => 1,
                'web_foursquare_api_key' => 1,
                'android_foursquare_api_key' => 1,
                'ios_foursquare_api_key' => 1,
                'web_foursquare_status' => 1,
                'android_foursquare_status' => 1,
                'ios_foursquare_status' => 1
            ]
        ];
        $result = $this->mongo_db->find(MDB_SITEINFO, array('_id' => 1), $option);
        return (!empty($result)) ? $result : '';
    }

    public function update_google_settings($post_value_array, $google_id) {
        $createdate = date('d-m-Y H:i:s');
        $web_foursquare_status = isset($post_value_array['web_foursquare_status']) ? $post_value_array['web_foursquare_status'] : '';
        $android_foursquare_status = isset($post_value_array['android_foursquare_status']) ? $post_value_array['android_foursquare_status'] : '';
        $ios_foursquare_status = isset($post_value_array['ios_foursquare_status']) ? $post_value_array['ios_foursquare_status'] : '';

        $dataset_smsinfo = [
            'ios_google_map_key' => $post_value_array['ios_google_map_key'],
            'ios_google_geo_key' => $post_value_array['ios_google_geo_key'],
            'google_timezone_api_key' => $post_value_array['google_timezone_api_key'],
            'web_google_map_key' => $post_value_array['web_google_map_key'],
            'web_google_geo_key' => $post_value_array['web_google_geo_key'],
            'android_google_key' => $post_value_array['android_google_api_key'],
            'web_foursquare_api_key' => $post_value_array['web_foursquare_api_key'],
            'android_foursquare_api_key' => $post_value_array['android_foursquare_api_key'],
            'ios_foursquare_api_key' => $post_value_array['ios_foursquare_api_key'],
            'web_foursquare_status' => $web_foursquare_status,
            'android_foursquare_status' => $android_foursquare_status,
            'ios_foursquare_status' => $ios_foursquare_status
        ];

        $result = $this->mongo_db->updateOne(MDB_SITEINFO, ['_id' => (int) $google_id], array('$set' => $dataset_smsinfo), array('upsert' => false));

        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }

    public function validate_update_google_settings($arr = "") {
        return Validation::factory($arr)->rule('ios_google_map_key', 'not_empty')
                        ->rule('ios_google_geo_key', 'not_empty')
                        ->rule('web_google_map_key', 'not_empty')
                        ->rule('google_timezone_api_key', 'not_empty')
                        ->rule('web_google_geo_key', 'not_empty')
                        ->rule('android_google_api_key', 'not_empty')
                        ->rule('web_foursquare_api_key', 'not_empty')
                        ->rule('android_foursquare_api_key', 'not_empty')
                        ->rule('ios_foursquare_api_key', 'not_empty');
    }

    /**
     * validating the web language 
     * 
     * @param type $file
     * @return type
     */
    public function validate_web_language($file) {
        $validator = Validation::factory($file)->rule('dynamic_lang', 'not_empty');
        if (isset($file['web_language_file']) && !empty($file['web_language_file'])) {
            $validator = $validator->rule('web_language_file', 'Upload::type', array(':value', array('xml')));
        }
        return $validator;
    }

    /** 
     * validating the ios driver language
     * 
     * @param type $file
     * @return type
     */
    public function validate_ios_driver_language($file) {
        $validator = Validation::factory($file)->rule('dynamic_lang', 'not_empty');;
        if (isset($file['ios_driver_language_file']) && !empty($file['ios_driver_language_file'])) {
            $validator = $validator->rule('ios_driver_language_file', 'Upload::type', array(':value', array('strings')));
        }
        if (isset($file['ios_passenger_language_file']) && !empty($file['ios_passenger_language_file'])) {
            $validator = $validator->rule('ios_passenger_language_file', 'Upload::type', array(':value', array('strings')));
        }
        if (isset($file['ios_driver_colorcode_file']) && !empty($file['ios_driver_colorcode_file'])) {
            $validator = $validator->rule('ios_driver_colorcode_file', 'Upload::type', array(':value', array('xml')));
        }
        if (isset($file['ios_passenger_colorcode_file']) && !empty($file['ios_passenger_colorcode_file'])) {
            $validator = $validator->rule('ios_passenger_colorcode_file', 'Upload::type', array(':value', array('xml')));
        }
        return $validator;
    }

    /**
     * validating the android driver language 
     * 
     * @param type $file
     * @return type
     */
    public function validate_android_driver_language($file) {
        $validator = Validation::factory($file)->rule('dynamic_lang', 'not_empty');;
        if (isset($file['android_driver_language_file']) && !empty($file['android_driver_language_file'])) {
            $validator = $validator->rule('android_driver_language_file', 'Upload::type', array(':value', array('xml')));
        }
        if (isset($file['android_passenger_language_file']) && !empty($file['android_passenger_language_file'])) {
            $validator = $validator->rule('android_passenger_language_file', 'Upload::type', array(':value', array('xml')));
        }
        if (isset($file['android_driver_colorcode_file']) && !empty($file['android_driver_colorcode_file'])) {
            $validator = $validator->rule('android_driver_colorcode_file', 'Upload::type', array(':value', array('xml')));
        }
        if (isset($file['android_passenger_colorcode_file']) && !empty($file['android_passenger_colorcode_file'])) {
            $validator = $validator->rule('android_passenger_colorcode_file', 'Upload::type', array(':value', array('xml')));
        }
        return $validator;
    }

    /**
     * Get the total no of package transaction information
     * 
     * @return type
     */
    public function count_package_info() {
        $result = $this->mongo_db->count(MDB_PACKAGE_INFO, array('_id' => array('$ne' => null)));
        return $result;
    }

    public function all_package_info_list($offset, $val) {

        $package_array = array(
            array('$match' => array('_id' => array('$ne' => null))),
            array(
                '$project' => array(
                    '_id' => '$_id',
                    'package_type' => '$package_type',
                    'amount' => '$amount',
                    'payment_terms' => '$payment_terms',
                    'paid_status' => '$paid_status',
                    'txnID' => '$txnID',
                    'ePGTxnID' => '$ePGTxnID',
                    'purchase_inv_id' => '$purchase_inv_id',
                    'createddate'=>'$createddate'
                )
            ),
            array(
                '$sort' => array("_id" => -1)
            ),
            array(
                '$skip' => (int) $offset
            ),
            array(
                '$limit' => (int) $val
            )
        );
        $result = $this->mongo_db->aggregate(MDB_PACKAGE_INFO, $package_array);

        return (!empty($result['result'])) ? $result['result'] : array();
    }

    public function total_paid_amount() {
        $arguments = [array(
        '$match' => array('paid_status' => 1)
            ),
            array(
                '$group' => array(
                    '_id' => NULL,
                    'total_amount' => array('$sum' => '$amount')
                )
        )];
        $result = $this->mongo_db->aggregate(MDB_PACKAGE_INFO, $arguments);
        return (!empty($result['result']) && isset($result['result'])) ? $result['result'] : array();
    }

    /** Updating language and color code values for Web,Android and IOS. * */
    public function update_language_colorcode($arraydata) {

        # updated time
        $time = date('YmdHis');
        $key_status = str_replace("_settings", "_status", $arraydata[0]);
        $update_info = [
            $arraydata[0] => $arraydata[1],
            $key_status => $time
        ];
        $result = $this->mongo_db->updateOne(MDB_SITEINFO, ['_id' => 1], array('$set' => $update_info), array('upsert' => true));
    }

    /** Fetching language and color code values for Web,Android and IOS. * */
    public function get_langcolor_info() {
        $options = [
            'projection' => [
                '_id' => 0,
                'website_language_settings' => 1,
                'ios_driver_language_settings' => 1,
                'ios_passenger_language_settings' => 1,
                'ios_driver_colorcode_settings' => 1,
                'ios_passenger_colorcode_settings' => 1,
                'android_driver_language_settings' => 1,
                'android_passenger_language_settings' => 1,
                'android_driver_colorcode_settings' => 1,
                'android_passenger_colorcode_settings' => 1
            ]
        ];
        $query_result = $this->mongo_db->find(MDB_SITEINFO, ['_id' => 1], $options);
        $resultset = array();
        if(isset($query_result[0]) && count($query_result[0]) > 0){
            foreach($query_result[0] as $key => $value){
                $resultset[$key] = isset($value[SELECTED_LANGUAGE])?$value[SELECTED_LANGUAGE]:1;
            }
        }
        return $resultset;
    }

    /** Fetching payment gateway details. * */
    public function get_payment_details($payment_gateway_id = '') {
        $result = [];

        if ($payment_gateway_id != 0 && $payment_gateway_id != '') {
            $result = $this->mongo_db->find(MDB_PAYMENT_GATEWAYS, array('company_id' => 0, 'payment_gateway_id' => (int) $payment_gateway_id));
        } else if ($payment_gateway_id != 0) {
            $result = $this->mongo_db->find(MDB_PAYMENT_GATEWAYS, array('company_id' => 0));
        } else {
            $result = $this->mongo_db->find(MDB_PAYMENT_GATEWAYS, array('company_id' => 0, 'default_payment_gateway' => 1));
        }

        return (!empty($result) && count($result) > 0) ? $result : array();
    }

    public function get_paypal_payment_details($payment_gateway_id = 0) {


        $result = $this->mongo_db->find(MDB_PAYMENT_GATEWAYS, array('company_id' => 0, 'payment_gateway_id' => 1));

        return (!empty($result) && count($result) > 0) ? $result : array();
    }

    /** Checking validation for fields. * */
    public function validate_editcompanypayment($arr) {

        $object = Validation::factory($arr);
        foreach ($arr as $key => $value) {
            $object->rule($key, 'not_empty');
        }
        return $object;
    }

    public function editcompanypayment($post) {
        $company_id = $this->company_id;
        /* $payment_id = array_values($post['payment_gateway_provider_id']);

          $payment_id = (isset($payment_id[0]) && $payment_id[0]!="")?$payment_id[0]:0; */
        if ($post['payment_method'] == "T") {
            $payment_gateway_username_field = "payment_gateway_username";
            $payment_gateway_password_field = "payment_gateway_password";
            $payment_gateway_signature_field = "payment_gateway_signature";
            $payment_gateway_username = $post['payment_gateway_username'];
            $payment_gateway_password = $post['payment_gateway_password'];
            $payment_gateway_signature = isset($post['payment_gateway_signature']) ? $post['payment_gateway_signature'] : '';
        } else {
            $payment_gateway_username_field = "live_payment_gateway_username";
            $payment_gateway_password_field = "live_payment_gateway_password";
            $payment_gateway_signature_field = "live_payment_gateway_signature";
            $payment_gateway_username = $post['live_payment_gateway_username'];
            $payment_gateway_password = $post['live_payment_gateway_password'];
            $payment_gateway_signature = isset($post['live_payment_gateway_signature']) ? $post['live_payment_gateway_signature'] : '';
        }
        $check_box_default = isset($post['check_default']) ? (int) $post['check_default'] : 0;

        $query = array(
            'payment_gatway' => $post['payment_gateway_name'],
            'description' => $post['description'],
            'payment_method' => $post['payment_method'],
            'payment_status' => $post['payment_gateway_status'],
            $payment_gateway_username_field => $payment_gateway_username,
            $payment_gateway_password_field => $payment_gateway_password,
            $payment_gateway_signature_field => $payment_gateway_signature,
        );

        if (isset($post['payment_gateway_type'])) {
            $payment_gateway_id = $post['payment_gateway_type'];
        } else {
            $payment_gateway_id = $post['payment_gateway_id'];
        }
        $company_id = 0;
        $avialed_payment_gateway = $this->mongo_db->count(MDB_PAYMENT_GATEWAYS, array('payment_gateway_id' => array('$eq' => (int) $payment_gateway_id), 'company_id' => 0), array('payment_status'));

        if ($check_box_default == 1) {
            $query = array_merge($query, array('default_payment_gateway' => (int) $check_box_default));
            $result_update = $this->mongo_db->updateOne(MDB_PAYMENT_GATEWAYS, array('default_payment_gateway' => 1, 'company_id' => (int) $company_id), array('$set' => ['default_payment_gateway' => 0]), array('upsert' => true));
        }
        if ($avialed_payment_gateway > 0) {
            $result = $this->mongo_db->updateOne(MDB_PAYMENT_GATEWAYS, array('payment_gateway_id' => (int) $payment_gateway_id, 'company_id' => (int) $company_id), array('$set' => $query), array('upsert' => true));
        } else {
            $id = Commonfunction::get_auto_id(MDB_PAYMENT_GATEWAYS);
            $query = array(
                '_id' => $id,
                'company_id' => 0,
                'payment_gateway_id' => (int) $payment_gateway_id,
                'payment_gatway' => $post['payment_gateway_name'],
                'description' => $post['description'],
                'payment_method' => $post['payment_method'],
                'payment_status' => $post['payment_gateway_status'],
                'default_payment_gateway' => (int) $check_box_default,
                $payment_gateway_username_field => $payment_gateway_username,
                $payment_gateway_password_field => $payment_gateway_password,
                $payment_gateway_signature_field => $payment_gateway_signature,
            );
            $result = $this->mongo_db->insertOne(MDB_PAYMENT_GATEWAYS, $query);
        }
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }

    /**
     * get detailed invoice transaction
     * @param int $id
     */
    public function account_transaction($id) {
        $options = [
            'projection' => [
                '_id' => 1,
                'purchase_inv_id' => 1,
                'subscription_cost' => 1,
                'setup_cost' => 1,
                'amount' => 1,
                'package_type' => 1,
                'payment_terms' => 1,
                'createddate' => 1,
                'expirydate' => 1,
                'paid_status' => 1,
                'txnID' => 1,
                'ePGTxnID' => 1,
                'currency' => 1,
                'responsecode' => 1,
                'response_msg' => 1,
                'pay_mode' => 1,
                'service_tax' => 1,
                'service_tax_cost' => 1
            ]
        ];

        $query_result = $this->mongo_db->find(MDB_PACKAGE_INFO, ['_id' => (int) $id], $options);
        $result = (!empty($query_result) && isset($query_result[0])) ? $query_result[0] : array();
        return $result;
    }

    public function get_payment_gateway_info() {
        $options = [
            'projection' => [
                '_id' => 1,
                'payment_gateway_id' => 1,
                'payment_gatway' => 1
            ]
        ];
        $query_result = $this->mongo_db->find(MDB_PAYMENT_GATEWAYS, ['default_payment_gateway' => 1], $options);
        $result = (!empty($query_result) && isset($query_result[0])) ? $query_result[0] : array();
        return $result;
    }

    public function get_country_from_ip() {

        $ipaddress = $_SERVER['REMOTE_ADDR'];
        $result = array('country' => '', 'city' => '');
        $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ipaddress));
        if ($ip_data && $ip_data->geoplugin_countryName != null) {
            $result['country'] = $ip_data->geoplugin_countryCode;
            $result['city'] = $ip_data->geoplugin_city;
        }
        return $result;
    }

    /*     * * Common Function for generating PDF ************ */

    public function generate_pdf($html, $filename) {
        require_once(APPPATH . 'vendor/pdf/config/lang/eng.php');
        require_once(APPPATH . 'vendor/pdf/tcpdf.php');
        // create new PDF document
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        // set header and footer fonts
        $pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //set some language-dependent strings
        $pdf->setLanguageArray($l);
        // ---------------------------------------------------------
        // set font
        //$pdf->SetFont('helvetica', '', 10);        
        $pdf->AddPage();
        //$pdf->SetFont('helvetica', '', 8);
        # To support arabic fonts
        $pdf->SetFont('ufontscom_aealarabiya', '', 10);

        $pdf->writeHTML($html, true, false, false, false, '');
        // add a page
        // output the HTML content
        // reset pointer to the last page
        //Close and output PDF document
        ob_end_clean();
        $pdf->Output($filename . '.pdf', 'D');
        exit;
    }

    /*     * * Common Function for Send PDF ************ */

    public function send_pdf($html, $filepath) {
        require_once(APPPATH . 'vendor/pdf/config/lang/eng.php');
        require_once(APPPATH . 'vendor/pdf/tcpdf.php');
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        // set header and footer fonts
        $pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //set some language-dependent strings
        $pdf->setLanguageArray($l);
        // ---------------------------------------------------------
        // set font
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 8);
        $pdf->writeHTML($html, true, false, false, false, '');
        // add a page
        // output the HTML content
        // reset pointer to the last page
        //Close and output PDF document
        ob_end_clean();
        $pdf->Output($filepath . '.pdf', 'F');
        if (file_exists($filepath . '.pdf')) {
            return 1;
        } else {
            return 0;
        }
    }
    public function get_passenger_login_info(){
        $options = [
            'projection' => [
                '_id' => 1,
                'country_code'=>1,
                'phone'=>1,
                'password'=>1
            ],
            'sort'=>[
                '_id'=>1
            ],
            'limit'=>3
        ];

        $query_result = $this->mongo_db->find(MDB_PASSENGERS,['user_status'=>'A'], $options);
        $result = (!empty($query_result) && isset($query_result)) ? $query_result : array();
        
        return $result;
    }
    public function get_driver_login_info(){
        $options = [
            'projection' => [
                '_id' => 1,
                'country_code'=>1,
                'phone'=>1,
                'password'=>1
            ],
            'sort'=>[
                '_id'=>1
            ],
            'limit'=>3
        ];

        $query_result = $this->mongo_db->find(MDB_PEOPLE,['user_type'=>'D'], $options);
        $result = (!empty($query_result) && isset($query_result)) ? $query_result : array();
        return $result;
    }          

     public function login_details_byid($userid)
    {
		$result = array();
        $res = $this->mongo_db->findOne(MDB_PEOPLE,array('_id'=>(int)$userid),array('_id','name','lastname','email','address','country_code','login_country','login_state','login_city','status','phone','profile_picture'));
		if(!empty($res)){
			$result[] = $res;
		}
        return $result;    
    }
    /**
     * 
     * @param type $arr
     * @return type
     */
     public function add_domain_validate($arr) {         
        return Validation::factory($arr)->rule('domain_name', 'not_empty')
                                        ->rule('replace_domain_name', 'Model_Package::check_is_domain_name',array(':value'));                
     }                                      
     
     public function add_domain($access_key='',$live_domain='',$post_domain=''){ 
                  
          $domain_info = [
           'live_domain'=>$live_domain,
           'post_domain'=>$post_domain
        ];
        $domain_update_result = $this->mongo_db->updateOne(MDB_COMPANY_DOMAIN,['company_domain'=>$access_key],['$set'=>$domain_info]);
        
        return empty($domain_update_result->getwriteErrors())?$domain_update_result->getModifiedCount():0;
     }
     
     public function get_live_domain_info($access_key=''){
        $res = $this->mongo_db->findOne(MDB_COMPANY_DOMAIN,array('company_domain'=>$access_key),array('live_domain','post_domain','redirect_enable'));
		
        return (!empty($res))?$res: array();    
     }
     
     public function update_redirectdomain($access_key='',$redirect_status=0){ 
                  
          $domain_info = [
           'redirect_enable'=>$redirect_status           
        ];
        $domain_update_result = $this->mongo_db->updateOne(MDB_COMPANY_DOMAIN,['company_domain'=>$access_key],['$set'=>$domain_info]);
        
        return empty($domain_update_result->getwriteErrors())?$domain_update_result->getModifiedCount():0;
     }
     
     public function delete_domain($access_key=''){
          $domain_info = [
           'live_domain'=>'',
           'post_domain'   =>'',
           'redirect_enable'   =>0
        ];
        $res = $this->mongo_db->UpdateOne(MDB_COMPANY_DOMAIN,['company_domain'=>$access_key],['$set'=>$domain_info]);
	return empty($res->getwriteErrors())?$res->getModifiedCount():0;
     }
     public static function check_is_domain_name($domain_name){
   if (preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $domain_name) &&
   !preg_match('/-$/', $domain_name))
{
    return false;
}
return true;
     }
     public function get_all_domain_info($access_key){
         /*unset($this->mongo_db);
         $mongo_db= MangoDB::instance('additional_db');*/
         $options=['projection'=>[
                                    '_id'=>0,
                                    'live_domain'=>1
                            ]
                  ];
        $res =$this->mongo_db->find(MDB_COMPANY_DOMAIN,['company_domain'=>['$ne'=>$access_key]],$options);
		
        return (!empty($res))?$res: array();
     }
}
