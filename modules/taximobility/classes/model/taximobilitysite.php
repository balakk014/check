<?php defined('SYSPATH') or die('No direct script access.');
/****************************************************************
* Contains Site Model details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Model_TaximobilitySite extends Model
{
    public function __construct()
    {
        $this->session     = Session::instance();
        $this->currentdate = Commonfunction::getCurrentTimeStamp();
        //MongoDB Instance
        $this->mongo_db    = MangoDB::instance('default');
    } 
    //Users block/active/delete 
    //=================================
    public function block_users_request($activeids, $type =1)
    {
        $blockids = $active_ids = Commonfunction::mongo_format_array($activeids);
        if($type == 1){
			$options=[
				'projection'=>[
					'_id'=>1,                               
					'user_type'=>1,                               
				]
			];
			$match = array('_id' => array('$in' => $active_ids));
			$get_type = $this->mongo_db->Find(MDB_PEOPLE, $match,$options);
			
			if(!empty($get_type)){				
				unset($blockids);
				foreach($get_type as $g){
					if($g['user_type'] != 'D'){
						# add user ids except driver id
						$blockids[] = $g['_id']; 
					}
				}
			}
		}
		
		if(!empty($blockids)){
			$result = $this->mongo_db->updateMany(MDB_PEOPLE, array(
						'_id' => array(
							'$in' => $blockids
						)
					), array(
						'$set' => array(
							'status' => 'D'
						)
					));
		}
        return true;
    }
   
    public function active_users_request($activeids)
    {
        $active_ids = Commonfunction::mongo_format_array($activeids);
        $result     = $this->mongo_db->updateMany(MDB_PEOPLE, array(
            '_id' => array(
                '$in' => $active_ids
            )
        ), array(
            '$set' => array(
                'status' => 'A'
            )
        ));
        //echo '<pre>';print_r($result);exit;
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();
    }
    
    public function trash_users_request($activeids, $type =1)
    {        
        $blockids = $active_ids = Commonfunction::mongo_format_array($activeids);
        if($type == 1){
			$options=[
				'projection'=>[
					'_id'=>1,                               
					'user_type'=>1,                               
				]
			];
			$match = array('_id' => array('$in' => $active_ids));
			$get_type = $this->mongo_db->Find(MDB_PEOPLE, $match,$options);
			
			if(!empty($get_type)){				
				unset($blockids);
				foreach($get_type as $g){
					if($g['user_type'] != 'D'){
						# add user ids except driver id
						$blockids[] = $g['_id']; 
					}
				}
			}
		}
		
		if(!empty($blockids)){
			$result = $this->mongo_db->updateMany(MDB_PEOPLE, array(
						'_id' => array(
							'$in' => $blockids
						)
					), array(
						'$set' => array(
							'status' => 'T'
						)
					));
		}
        return true;
    }
    public function delete_users_request($activeids)
    {
		//MongoDB
        //Here changing array values with string to integers values
        $active_ids = Commonfunction::mongo_format_array($activeids);
        $result     = $this->mongo_db->deleteOne(MDB_PEOPLE, array(
            '_id' => array(
                '$in' => $active_ids
            )
        ));
        //echo '<pre>';print_r($result);exit;
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    public function block_passenger_request($blockids)
    {
        //MongoDB
        //Here changing array values with string to integers values
        $ids = Commonfunction::mongo_format_array($blockids);
        $set_array = array(
            'user_status' => 'D'
        );
        $result    = $this->mongo_db->updateMany(MDB_PASSENGERS, array(
            '_id' => array(
                '$in' => $ids
            )
        ), array(
            '$set' => $set_array
        ));
        //print_r($result); exit;
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    public function active_passenger_request($activeids)
    {
        $ids = Commonfunction::mongo_format_array($activeids);
        $set_array = array(
            'user_status' => 'A'
        );
        $result    = $this->mongo_db->updateMany(MDB_PASSENGERS, array(
            '_id' => array(
                '$in' => $ids
            )
        ), array(
            '$set' => $set_array
        ));
        //print_r($result); exit;
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    public function trash_passenger_request($trashids)
    {
        $ids = Commonfunction::mongo_format_array($trashids);
        $set_array = array(
            'user_status' => 'T'
        );
        $result    = $this->mongo_db->updateMany(MDB_PASSENGERS, array(
            '_id' => array(
                '$in' => $ids
            )
        ), array(
            '$set' => $set_array
        ));
        //print_r($result); exit;
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    public function delete_passenger_request($deleteids)
    {
		//MongoDB
        //Here changing array values with string to integers values
        $active_ids = Commonfunction::mongo_format_array($deleteids);
        $result     = $this->mongo_db->deleteOne(MDB_PASSENGERS, array(
            '_id' => array(
                '$in' => $active_ids
            )
        ));
        //echo '<pre>';print_r($result);exit;
        return (empty($result->getwriteErrors())) ? 1 : $result->getwriteErrors();;
    }
    
    public function isdriverassigned($driverIds)
    {
		$driverIds = Commonfunction::mongo_format_array($driverIds);
		$match_query = array('mapping_driverid'=>array('$in'=>$driverIds),'mapping_status'=>'A');
		$ops = array(
			array(
					'$lookup' => array(
					'from'=>MDB_PEOPLE,
					'localField'=> "mapping_driverid",
					'foreignField' => "_id",
					'as'=> "people"
					)
				),
			array('$unwind'=>'$people'),
			array('$match' => $match_query),
			array('$project' => array('_id' => '$_id')),
		);
		$result = $this->mongo_db->aggregate(MDB_TAXI_DRIVER_MAPPING,$ops);
		return !empty($result['result']) ? $result['result'] :array();
    }

}
