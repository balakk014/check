<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains CMS model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityCms extends Model
{
    public function __construct()
    {
        //MongoDB Instance
        $this->mongo_db = MangoDB::instance('default');
    }
    /*Get the CMS Content*/
    public function getcmscontent($content, $default_companyid = "")
    {		
        $default_companyid = COMPANY_CID;
        if ($default_companyid != 0) {
            $args = array(array('$unwind' => '$company_cms'),
                          array('$match' => array('_id' => (int)$default_companyid,
                                                'company_cms.type' => 1,
                                                'company_cms.page_url' => Commonfunction::MongoRegex("/$content/i")
                                            )),
                          array('$project' => array('page_url' => '$company_cms.page_url',
                                                    'content' => '$company_cms.content',
                                                    'menu' => '$company_cms.menu_name'))
                        );
            $cms_result = $this->mongo_db->aggregate(MDB_COMPANY,$args);
            return (isset($cms_result['result'])) ? $cms_result['result'] : array();
        } else {
            $cms_result = $this->mongo_db->findOne(MDB_CMS, array(
                'status_post' => 'P',
                'menu_link' => $content
            ), array(
                'content',
                'meta_keyword',
                'meta_title',
                'meta_description',
                'menu'
            ));
            $res        = array();
            
            if(count($cms_result) >0){
                foreach ($cms_result as $keys => $values) {
                    $res[0][$keys] = $values;
                }
            }
            return (isset($res)) ? $res : array();
        }
    }
    /*Get the CMS Content*/
    public function getcompanycontent($pagename, $cid)
    {
        $contentcom = $this->mongo_db->findOne(MDB_COMPANY, array(
            'cid' => $cid,
            'cms.page_url' => $pagename,
            'cms.status' => 1
        ));
        $res        = array();
        foreach ($contentcom as $keys => $values) {
            $res[0][$keys] = $values;
        }
        return $res;
    }
    public function get_company_addr($cid)
    {
        $res = array();
        if ($cid != 0) {
            $contentcom = $this->mongo_db->findOne(MDB_COMPANY, array(
                'cid' => $cid
            ), array(
                'company_address'
            ));
            foreach ($contentcom as $keys => $values) {
                $res[0][$keys] = $values;
            }
        }
        return $res;
    }
}
