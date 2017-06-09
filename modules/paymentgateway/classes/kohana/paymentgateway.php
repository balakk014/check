<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Kohana Payment gateway module
 *
 * @uses       Payment gateway integration (v1.0)
 * @package    Custom
 * @author     NDOT Team 
 * @copyright  (c) 2017-2020 Ndot Team
 */
class Kohana_Paymentgateway {

    /**
     * Declare static variable
     *
     * @var type
     */
    protected static $_payment_gateway_class_name;
    protected static $_payment_gateway_response;
    protected static $_payment_gateway_method_name;

    public function __construct() {
        
    }

    /**
     * Payment gateway connection establishment
     *
     * @param type $payment_type
     * @param type $card_info
     * @param type $shipping_info
     * @param type $transaction_amount
     * @param type $action
     * @param type $additional_parameters
     * @return type
     */
    public static function payment_gateway_connect($action = '', $transaction_amount = '', $card_info = [], $shipping_info = [], $additional_parameters = []) {

        // Get the default payment gateway connecting strings
        $connection_string = self::get_default_payment_connection_string_with_status();

        if (isset($connection_string[0]['payment_method'])) {
            //$payment_type = array(1 => 'Paypal', 2 => 'Braintree', 3 => 'Authorize', 4 => 'Stripe');
            
            // Get the payment gateway type id
            $payment_type_id = $connection_string[0]['payment_gateway_id'];
            try {
                // Declare Payment gateway list 
                $xml_system = simplexml_load_file(DOCROOT . "modules/paymentgateway/views/system.xml") or die("Error: Cannot create object");
                
                //Set the Paypal Paymen tgateway Here
                $paypal_type = ['0' => 'Paypal'];

                //Get the Alternate Paymentgateway types from xml           
                $payment_type = [];
                $payment_type = (array) $xml_system->children()->system->section->field->option;
                unset($payment_type['@attributes']);
                
                if ($payment_type[0] == 'Select Provider') {
                    $payment_type = array_replace($payment_type, $paypal_type);
                    $payment_type = array_combine(range(1, count($payment_type)), array_values($payment_type));
                } else {
                    throw new exception('In system.xml need to set first option  "Select Provider"');
                }
                
                // Get the payment gateway class name
                $payment_gateway_name = $payment_type[$payment_type_id];

                // Declaring payment gateway class & method name
                $_payment_gateway_class_name = $payment_gateway_name . 'payment';
                $_payment_gateway_method_name = $payment_gateway_name . '_' . $action;

                // Find the payment gateway class exists or not
                if (class_exists($_payment_gateway_class_name)) {
                    if (!method_exists($_payment_gateway_class_name, $_payment_gateway_method_name)) {
                        throw new Exception($_payment_gateway_class_name . '::' . $_payment_gateway_method_name . '() implementation not loaded');
                    }
                    if (is_array($card_info)) {
                        $card_info = array_merge($card_info, ["currency" => CURRENCY_FORMAT]);
                    }
                    $_payment_gateway_response = $_payment_gateway_class_name::$_payment_gateway_method_name($connection_string, $transaction_amount, $card_info, $shipping_info, $additional_parameters);

                    return $_payment_gateway_response;
                } else {
                    $x = 1;
                    throw new Exception("Unable to load class: $_payment_gateway_class_name");
                    return false;
                }
            } catch (Exception $ex) {

                if (isset($x) && $x == 1) {
                    throw new Exception("Unable to load class: $_payment_gateway_class_name");
                }
                throw new Exception($ex);
            }
        } else {
            throw new Exception('payment_gateway_not_properly configured or payment gateway not active');
        }
    }

    /**
     * Get Payment gateway connection parameters from application database
     *
     * @return type
     */
    private static function get_default_payment_connection_string_with_status() {

        $mongo_db = MangoDB::instance('default');
        $options = [
            'projection' => [
                '_id' => 1,
                'payment_gateway_id' => 1,
                'payment_method' => 1,
                'payment_gateway_username' => 1,
                'payment_gateway_password' => 1,
                'payment_gateway_signature' => 1,
                'live_payment_gateway_username' => 1,
                'live_payment_gateway_password' => 1,
                'live_payment_gateway_signature' => 1
            ],
            'skip' => 0,
            'limit' => 1
        ];
        $result = $mongo_db->find(MDB_PAYMENT_GATEWAYS, array('payment_status' => array('$ne' => 'T'), 'default_payment_gateway' => 1, 'company_id' => 0), $options);
        return isset($result) ? $result : array();
    }

    /**
     *
     * @param type $paymentType
     * @return type
     */
    private static function payment_gateway_bytype($paymentType = "") {
        $mongo_db = MangoDB::instance('default');
        $match = ['payment_gateway_id' => (int) $paymentType, 'company_id' => 0];
        $project = ['payment_gateway_id' => '$payment_gateway_id',
            'payment_method' => '$payment_method',
            'payment_gateway_username' => '$payment_gateway_username',
            'payment_gateway_password' => '$payment_gateway_password',
            'payment_gateway_signature' => '$payment_gateway_signature', 'live_payment_gateway_username' => '$live_payment_gateway_username',
            'live_payment_gateway_password' => '$live_payment_gateway_password',
            'live_payment_gateway_signature' => '$live_payment_gateway_signature',
            'default_payment_gateway' => '$default_payment_gateway',
            'payment_status' => '$payment_status',
            'payment_gatway' => '$payment_gatway',
            'description' => '$description'
        ];
        $args = [['$match' => $match], ['$project' => $project]];
        $res = $mongo_db->Aggregate(MDB_PAYMENT_GATEWAYS, $args);
        return (!empty($res) ? $res['result'] : array());
    }

    /**
     * Read XML To Load Controls use this function
     * 
     */
    public static function payment_auth_credentials_view($payment_gateway_details = []) {

        // paymentgateway drop down and common fields load this file
        try {
            $xml_system = simplexml_load_file(DOCROOT . "modules/paymentgateway/views/system.xml") or die("Error: Cannot create object");
        } catch (Exception $ex) {
            throw new Exception($ex);
        }

        $top_field_array = [];
        $payment_gateway_details = [];
        $checkbox_checked = '';
        $radio_checked = '';
        $checkbox_value = 0;
        $form_top_fields = [];
        $form_fields = [];
        $form_live_fields = [];
        $form_bottom_fields = [];

        foreach ($xml_system->children() as $child) {
            foreach ($child->section->field->option as $options) {
                $i = (int) $options['value'];
                $top_field_array[$i] = $options;
            }
        }
        
        $payment_type_id = Kohana_Request::current()->post('payment_gateway_type');
        $payment_type_button = Kohana_Request::current()->post('submit_edit_alternate_payment');
        if ($payment_type_button != '') {
            $payment_type_id = Kohana_Request::current()->post('payment_gateway_id');
        }
        
        if($payment_type_id==''){
            $connection_string = self::get_default_payment_connection_string_with_status();
            $payment_type_id= isset($connection_string[0]['payment_gateway_id'])?$connection_string[0]['payment_gateway_id']:'';
        }

        if ($payment_type_id != 0 && $payment_type_id != 1 && $payment_type_id != '') {
            $payment_type = [];
            //Get the Paymentgateway types from xml
            foreach ($xml_system->children()->system->section->field->option as $payment_types) {
                $val = (int) $payment_types['value'];
                $payment_type[$val] = $payment_types[0];
            }

            // Declaring payment gateway class & method name     
            $payment_gateway_name = strtolower($payment_type[$payment_type_id]);
            try {
                $payment_settings = self::payment_gateway_bytype($payment_type_id);
            } catch (Exception $ex) {
                throw new Exception($ex);
            }

            //Selected payment gateway data not in db
            if (count($payment_settings) == 0) {
                $payment_gateway_details = ['payment_gateway_username' => '', 'payment_gateway_password' => '', 'payment_gateway_signature' => '', 'live_payment_gateway_username' => '', 'live_payment_gateway_password' => '', 'live_payment_gateway_signature' => ''
                    , 'Payment gateway name' => '', 'description' => '', 'currency_code' => CURRENCY_FORMAT, 'currency_symbol' => CURRENCY, 'payment_gateway_id' => $payment_type_id, 'payment_status' => 'A', 'payment_method' => 'T', 'A' => TRUE, 'T' => TRUE];
            }

            //Selected Payment gateway data  setup with array
            foreach ($payment_settings as $payment_gateway_settings) {
                if ($payment_gateway_settings['default_payment_gateway'] == 1 && $payment_gateway_settings['payment_gateway_id'] != 1) {
                    $checkbox_checked = TRUE;
                    $payment_gateway_details['check_default'] = 1;
                } else {
                    $checkbox_checked = FALSE;
                    $payment_gateway_details['check_default'] = 0;
                }

                $payment_gateway_details['payment_gateway_username'] = isset($payment_gateway_settings['payment_gateway_username']) ? $payment_gateway_settings['payment_gateway_username'] : '';
                $payment_gateway_details['payment_gateway_password'] = isset($payment_gateway_settings['payment_gateway_password']) ? $payment_gateway_settings['payment_gateway_password'] : '';
                $payment_gateway_details['payment_gateway_signature'] = isset($payment_gateway_settings['payment_gateway_signature']) ? $payment_gateway_settings['payment_gateway_signature'] : '';

                $payment_gateway_details['live_payment_gateway_username'] = isset($payment_gateway_settings['live_payment_gateway_username']) ? $payment_gateway_settings['live_payment_gateway_username'] : '';
                $payment_gateway_details['live_payment_gateway_password'] = isset($payment_gateway_settings['live_payment_gateway_password']) ? $payment_gateway_settings['live_payment_gateway_password'] : '';
                $payment_gateway_details['live_payment_gateway_signature'] = isset($payment_gateway_settings['live_payment_gateway_signature']) ? $payment_gateway_settings['live_payment_gateway_signature'] : '';

                $payment_gateway_details['payment_gateway_name'] = isset($payment_gateway_settings['payment_gatway']) ? $payment_gateway_settings['payment_gatway'] : '';
                $payment_gateway_details['description'] = isset($payment_gateway_settings['description']) ? $payment_gateway_settings['description'] : '';
                $payment_gateway_details['currency_code'] = CURRENCY_FORMAT;
                $payment_gateway_details['currency_symbol'] = CURRENCY;
                $payment_gateway_details['payment_gateway_id'] = $payment_gateway_settings['payment_gateway_id'];

                if ($payment_gateway_settings['payment_status'] == 'A' && $payment_gateway_settings['payment_gateway_id'] != 1) {
                    $payment_gateway_details['A'] = TRUE;
                    $payment_gateway_details['I'] = FALSE;
                } else {
                    $payment_gateway_details['I'] = TRUE;
                    $payment_gateway_details['A'] = FALSE;
                }
                if ($payment_gateway_settings['payment_method'] == 'T' && $payment_gateway_settings['payment_gateway_id'] != 1) {
                    $payment_gateway_details['T'] = TRUE;
                    $payment_gateway_details['L'] = FALSE;
                } else {
                    $payment_gateway_details['L'] = TRUE;
                    $payment_gateway_details['T'] = FALSE;
                }
            }

            // choosed payment gateway types xml load here
            try {
                $xml = simplexml_load_file(DOCROOT . "modules/" . $payment_gateway_name . "/views/" . $payment_gateway_name . ".xml") or die("Error: Cannot create object");
            } catch (Exception $ex) {
                throw new Exception($ex);
            }
            $field_controls = [];
            $parent_field_array = (array) $xml_system->children()->system->section->group;

            $child_field_array = (array) $xml->children()->system->section->group;
            unset($parent_field_array['@attributes']);
            unset($child_field_array['@attributes']);

            $field_controls = array_merge((array) $parent_field_array['field'], (array) $child_field_array['field']);

            foreach ($field_controls as $field) {

                $field_type = (string) $field['type'];
                $span = isset($field->span) ? '<span class=' . $field->span['class'] . '>' . (string) $field->span . '</span>' : '';

                switch ($field_type) {
                    //Load controls based on XML types
                    case "text":
                        $field_id = (string) $field['id'];
                        $textvalue = isset($payment_gateway_details[$field_id]) ? $payment_gateway_details[$field_id] : '';
                        if ($payment_type_button != '') {
                            $textvalue = Kohana_Request::$current->post($field_id);
                        }
                        $showInPosition = isset($field['showInPosition']) ? (int) $field['showInPosition'] : '';
                        $readonly = isset($field['readonly']) ? (string) $field['readonly'] : '';

                        if (isset($field['readonly'])) {
                            $options = ['class' => 'small_form_control', $readonly => $readonly];
                        } else {
                            $options = ['class' => 'small_form_control'];
                        }

                        if ($showInPosition == 0) {
                            $form_top_fields[] = "<div class='form_group'>";
                            $form_top_fields[] = Form::label('label', $field->label, ['class' => 'small_control_label']);
                            $form_top_fields[] = $span;
                            $form_top_fields[] = Form::input($field['id'], $textvalue, $options);
                            $form_top_fields[] = "</div>";
                        } else if ($showInPosition == 1) {
                            $form_fields[] = "<div class='form_group'>";
                            $form_fields[] = Form::label('label', $field->label, ['class' => 'small_control_label']);
                            $form_fields[] = $span;
                            $form_fields[] = Form::input($field['id'], $textvalue, $options);
                            $form_fields[] = "</div>";
                        } else if ($showInPosition == 2) {
                            $form_live_fields[] = "<div class='form_group'>";
                            $form_live_fields[] = Form::label('label', $field->label, ['class' => 'small_control_label']);
                            $form_live_fields[] = $span;
                            $form_live_fields[] = Form::input($field['id'], $textvalue, $options);
                            $form_live_fields[] = "</div>";
                        } else if ($showInPosition == 3) {
                            $form_bottom_fields[] = "<div class='form_group'>";
                            $form_bottom_fields[] = Form::label('label', $field->label, []);
                            $form_bottom_fields[] = $span;
                            $form_bottom_fields[] = Form::input($field['id'], $textvalue, $options);
                            $form_bottom_fields[] = "</div>";
                        }
                        break;

                    case "select":
                        $form_fields[] = Form::label($field['id'], '', []);
                        $form_fields[] = Form::select($field['id'], [], '');
                        break;

                    case "button":
                        $form_fields[] = Form::button('Activate', 'Activate', array('type' => 'submit'));
                        break;

                    case "radio":
                        $field_value = (string) $field['value'];
                        $field_id = (string) $field['id'];
                        $radio_checked = isset($payment_gateway_details[$field_value]) ? $payment_gateway_details[$field_value] : FALSE;
                        if ($payment_type_button != '') {
                            $radio_value_array = ['A', 'I', 'T', 'L'];
                            $radio_postvalue = Kohana_Request::$current->post($field_id);
                            if ($radio_postvalue == $field_value) {
                                $radio_checked = TRUE;
                            } else {
                                $radio_checked = FALSE;
                            }
                        }
                        $showInPosition = isset($field['showInPosition']) ? (int) $field['showInPosition'] : '';
                        $showlabel = isset($field->label[0]) ? (string) $field->label[0] : '';
                        $class = isset($field->label['class']) ? (string) $field->label['class'] : '';
                        if ($class != '') {
                            $class = '<div class=' . $class . '>';
                        }

                        $id = isset($field->label['id']) ? (string) $field->label['id'] : '';
                        if ($id != '') {
                            $id = '</div>';
                        }

                        $events_param = [];
                        $pay_method_start = '';
                        $pay_method_end = '';
                        $live_mode_start = '';
                        $live_mode_end = '';
                        $payment_method = isset($field['value']) ? (string) $field['value'] : '';

                        if ($payment_method == 'T') {
                            $pay_method_start = "<div class='pay_method'><div class='radio_primary_small'>";

                            $events_param = ["onclick" => "change_payment_option('T')", 'class' => 'radio_primary_small'];
                        } else if ($payment_method == 'L') {
                            $live_mode_start = '</div><div class="radio_primary_small">';
                            $events_param = ["onclick" => "change_payment_option('L')", 'class' => 'radio_primary_small'];
                            $live_mode_end = '</div>';
                            $pay_method_end = "</div>";
                        } else if ($payment_method == 'A') {
                            $pay_method_start = "<div class='pay_method'><div class='radio_primary_small'>";
                            $events_param = ['class' => 'radio_primary_small'];
                        } else if ($payment_method == 'I') {
                            $live_mode_start = '</div><div class="radio_primary_small">';
                            $live_mode_end = '</div>';
                            $pay_method_end = "</div>";
                            $events_param = ['class' => 'radio_primary_small'];
                        } else {
                            $events_param = ['class' => 'radio_primary_small'];
                        }

                        if ($showInPosition == 0) {
                            if ($class != '') {
                                $form_top_fields[] = $class . Form::label($field['id'], $showlabel, ['class' => 'small_control_label']);
                            }
                            $form_top_fields[] = $span;
                            $form_top_fields[] = $pay_method_start . $live_mode_start . Form::radio($field['id'], $field_value, $radio_checked, $events_param) . Form::label($field['id'], $field['translate'], []) . $live_mode_end . $pay_method_end . $id;
                        } else if ($showInPosition == 1) {
                            if ($class != '') {
                                $form_fields[] = $class . Form::label('', $showlabel, ['class' => 'small_control_label']);
                            }
                            $form_fields[] = $span;
                            $form_fields[] = $pay_method_start . $live_mode_start . Form::radio($field['id'], $field_value, $radio_checked, $events_param) . Form::label($field['id'], $field['translate'], []) . $live_mode_end . $pay_method_end . $id;
                        } else if ($showInPosition == 2) {
                            if ($class != '') {
                                $form_live_fields[] = $class . Form::label('', $showlabel, ['class' => 'small_control_label']);
                            }
                            $form_live_fields[] = $span;
                            $form_live_fields[] = $pay_method_start . $live_mode_start . Form::radio($field['id'], $field_value, $radio_checked, $events_param) . Form::label($field['id'], $field['translate'], []) . $live_mode_end . $pay_method_end . $id;
                        } else if ($showInPosition == 3) {
                            if ($class != '') {
                                $form_bottom_fields[] = $class . Form::label('ss', $showlabel, ['class' => 'small_control_label']);
                            }
                            $form_bottom_fields[] = $span;
                            $form_bottom_fields[] = $pay_method_start . $live_mode_start . Form::radio($field['id'], $field_value, $radio_checked, $events_param) . Form::label($field['id'], $field['translate'], []) . $live_mode_end . $pay_method_end . $id;
                        }

                        break;

                    case "checkbox":
                        $field_id = (string) $field['id'];
                        $checkbox_field_value = 1;
                        $check_box_div_start = '<div class="checkbox_custom">';
                        $check_box_div_end = '</div>';
                        $checkbox_postvalue = Kohana_Request::$current->post($field_id);
                        if ($payment_type_button != '' && $checkbox_postvalue != '') {
                            $checkbox_checked = TRUE;
                        }
                        $showInPosition = isset($field['showInPosition']) ? (int) $field['showInPosition'] : '';
                        if ($showInPosition == 0) {
                            $form_top_fields[] = $check_box_div_start . Form::checkbox($field['id'], $checkbox_field_value, $checkbox_checked, []) . Form::label('label', $field->label[0], []) . $check_box_div_end;
                        } else if ($showInPosition == 1) {
                            $form_fields[] = $check_box_div_start . Form::checkbox($field['id'], $checkbox_field_value, $checkbox_checked, []) . Form::label('label', $field->label[0], []) . $check_box_div_end;
                        } else if ($showInPosition == 2) {
                            $form_live_fields[] = $check_box_div_start . Form::checkbox($field['id'], $checkbox_field_value, $checkbox_checked, []) . Form::label('label', $field->label[0], []) . $check_box_div_end;
                        } else if ($showInPosition == 3) {
                            $form_bottom_fields[] = $check_box_div_start . Form::checkbox($field['id'], $checkbox_field_value, $checkbox_checked, []) . Form::label('label', $field->label[0], []) . $check_box_div_end;
                        }
                        break;

                    case "hidden":
                        $field_id = (string) $field['id'];
                        $hidden_field_value = $payment_gateway_details[$field_id];
                        $showInPosition = isset($field['showInPosition']) ? (int) $field['showInPosition'] : '';

                        $form_bottom_fields[] = Form::hidden($field['id'], $hidden_field_value);
                }
            }
        }


        $payment_gateway_list[1] = $form_top_fields;
        $payment_gateway_list[2] = $form_fields;
        $payment_gateway_list[3] = $form_live_fields;
        $payment_gateway_list[4] = $form_bottom_fields;

        $payment_gateway_list[0] = Form::select('payment_gateway_type', $top_field_array, $payment_type_id, ['class' => 'form_control payment_gateway_type', 'onchange' => 'document.forms["gateways_details"].submit();']);
        return $payment_gateway_list;
    }

    /**
     * Required field only send to application
     * 
     * @return type
     * @throws Exception
     */
    public static function get_payment_gateway_required_fields() {

        // paymentgateway drop down and common fields load this file
        try {
            $xml_system = simplexml_load_file(DOCROOT . "modules/paymentgateway/views/system.xml") or die("Error: Cannot create object");
        } catch (Exception $ex) {
            throw new Exception($ex);
        }

        $payment_type_id = Kohana_Request::current()->post('payment_gateway_id');
        $field_array = [];
        if ($payment_type_id != 0 && $payment_type_id != '') {
            $payment_type = [];

            //Get the Paymentgateway types from xml
            foreach ($xml_system->children()->system->section->field->option as $payment_types) {
                $val = (int) $payment_types['value'];
                $payment_type[$val] = $payment_types[0];
            }
            $payment_gateway_name = strtolower($payment_type[$payment_type_id]);

            // choosed payment gateway types xml load here
            try {
                $xml = simplexml_load_file(DOCROOT . "modules/" . $payment_gateway_name . "/views/" . $payment_gateway_name . ".xml") or die("Error: Cannot create object");
            } catch (Exception $ex) {
                throw new Exception($ex);
            }
            $field_controls = [];

            $parent_field_array = (array) $xml_system->children()->system->section->group;
            $child_field_array = (array) $xml->children()->system->section->group;

            $field_controls = array_merge((array) $parent_field_array['field'], (array) $child_field_array['field']);
            $payment_method = Kohana_Request::current()->post('payment_method');

            foreach ($field_controls as $field) {
                if ($field['required'] == TRUE && ($field['showInMode'] == '' || $field['showInMode'] == (string) $payment_method)) {
                    $field_array[] = (string) $field['id'];
                }
            }
        }
        return array_unique($field_array);
    }
}

