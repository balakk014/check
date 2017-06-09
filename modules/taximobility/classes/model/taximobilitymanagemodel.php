<?php defined('SYSPATH') or die('No direct script access.');
/****************************************************************
* Contains Manage model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Model_TaximobilityManagemodel extends Model
{
    public function __construct()
    {
        $this->session = Session::instance();
        $this->mongo_db = MangoDB::instance('default');
    }
    public function select_site_settings()
    {
         ## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
        $options=[];
        $res = $this->mongo_db->find(MDB_SITEINFO,[],$options);
        $result = $res;
        return (isset($result)) ? Commonfunction::change_key($result) : array();
    }
    public function site_settings_validate($arr)
    {
        $validate = Validation::factory($arr)->rule('appname', 'not_empty', array(
            ':value',
            'App Name'
        ))->rule('appdescription', 'not_empty', array(
            ':value',
            'App Description'
        ))->rule('currencyformat', 'not_empty', array(
            ':value',
            'Currency Format'
        ))->rule('currency_symbol', 'not_empty', array(
            ':value',
            'Currency Symbol'
        ))->rule('metakeyword', 'not_empty', array(
            ':value',
            'Meta Keyword'
        ))->rule('metadescription', 'not_empty', array(
            ':value',
            'Meta Description'
        ))->rule('paypalusername', 'not_empty', array(
            ':value',
            'Paypal Username'
        ))->rule('twitterurl', 'url', array(
            ':value',
            'Field'
        ))->rule('facebookurl', 'url', array(
            ':value',
            'Field'
        ))->rule('email', 'not_empty', array(
            ':value',
            'Email'
        ))->rule('email', 'email_domain', array(
            ':value',
            'Email'
        ));
        return $validate;
    }
}
