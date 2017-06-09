<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains Tdispatch Model details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Model_TaximobilityTdispatch extends Model
{
    public function __construct()
    {
        $this->session         = Session::instance();
        $this->username        = $this->session->get("username");
        $this->admin_username  = $this->session->get("username");
        $this->admin_userid    = $this->session->get("id");
        $this->admin_email     = $this->session->get("email");
        $this->user_admin_type = $this->session->get("user_type");
		$this->user_createdby = $this->userid = $this->session->get('userid');
        $this->company_id     = $this->session->get('company_id');
        $this->currentdate     = Commonfunction::getCurrentTimeStamp();
        $this->currentdate_bytimezone     = Commonfunction::createdateby_user_timezone();
		
		//MongoDB Instance
        $this->mongo_db    = MangoDB::instance('default');
    }
    //New
	public function validate_dispatchsetting($arr)
    {
        return Validation::factory($arr)->rule('labelname', 'not_empty');
    }
    public function update_dispatchsetting($post)
    {
        $user_createdby    = $this->userid;
        $company_id        = $this->company_id;
        $labelname         = isset($post['labelname']) ? implode(',', $post['labelname']) : '';
		if($company_id!=0){
			## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
			$options=[
				'projection'=>[
					'dispatch_algorithm'=>1                    
					],
				'sort'=>[
					'dispatch_algorithm.aid'=>-1
				],
				'limit'=>1
			];
			$dispatch_data = $this->mongo_db->find(MDB_COMPANY,['_id'=>(int)$company_id],$options);
			$companydispatch = (!empty($dispatch_data))?$dispatch_data:array();
			if (count($dispatch_data) > 0) {
				$update_array =array(
					'dispatch_algorithm.0.alg_created_by' => $user_createdby,
					'dispatch_algorithm.0.labelname' => $labelname
				);
				$result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>$company_id),array('$set'=>$update_array),array('upsert'=>true));
				return (empty($result->getwriteErrors())) ? 1 : 0;
			} else {
				$insert_array =array(
					'alg_created_by' => $user_createdby,
					'labelname' => $labelname
				);
				$insert_data = array('dispatch_algorithm' => $insert_array);
				$result = $this->mongo_db->updateOne(MDB_COMPANY,array('_id'=>$company_id),array('$push'=>$insert_data),array('upsert'=>true));
				return (empty($result->getwriteErrors())) ? 1 : 0;
			}
		} else {
			$result = $this->mongo_db->updateOne(MDB_SITEINFO,array('_id'=>1),array('$set'=>array('labelname'=>$labelname)),array('upsert'=>true));
			return (empty($result->getwriteErrors())) ? 1 : 0;
		}
    }
    
    public function tdispatch_settings()
    {
        $company_id     = $this->company_id;
		if($company_id!=0){
			## fields_projection code added to support LAMP 7.0 and its mongo version 3.4.0
			$options=[
				'projection'=>[
					'dispatch_algorithm'=>1
				],
				'limit'=>1
			];
			$dispatch_data = $this->mongo_db->find(MDB_COMPANY,['_id'=>(int)$company_id],$options);
			$companydispatch = (!empty($dispatch_data))?$dispatch_data:array();
			if (count($dispatch_data) > 0) {
				## LAMP 7.0 and Mongo DB 3.4 Conversion
                $company_dispatch  = $companydispatch[0]['dispatch_algorithm'];
                if(count($company_dispatch)==1){
                    $result    = $company_dispatch;
                } else {
                    $data = array_reverse($company_dispatch);
                    $result    = $data[0];
                }
            } else {
				$result = $companydispatch;
			}
		} else {
			$dispatch_data = $this->mongo_db->findOne(MDB_SITEINFO,array('_id'=>1),array('labelname'));
			$result[0] = (!empty($dispatch_data))? $dispatch_data :array();	
		}
        return $result;
    }
    
    public function get_citymodel_fare_details($model_id = "", $city_name = "", $city_id = '', $company_id = '')
	{
		if ($city_name != '') {
			$condition = array("stateinfo.cityinfo.city_name"=> Commonfunction::MongoRegex("/$city_name/i"));
		} elseif ($city_id != '') {
			$condition = array("stateinfo.cityinfo.city_id"=> (int)$city_id);
		} else {
			$condition = array("stateinfo.cityinfo.default"=> 1);
		}
		$arguments = array(
			array('$unwind' =>'$stateinfo'),
			array('$unwind' =>'$stateinfo.cityinfo'),
			array('$match' => $condition),
			array('$project' =>array(
					'_id' => 0,
					'city_id' => '$stateinfo.cityinfo.city_id',
					'city_model_fare' => '$stateinfo.cityinfo.city_model_fare',
				)
			),
			array('$limit' => 1)
		);
		$result = $this->mongo_db->aggregate(MDB_CSC, $arguments);
		$city_model_fare = 0;
		if(!empty($result['result']) && count($result['result'][0])>0){
			$city_model_fare = $result['result'][0]['city_model_fare'];
		}
		if (FARE_SETTINGS == 2 && !empty($company_id)) {
			$arguments = array(
				array('$unwind'=>'$model_fare'),
				array('$project' => array(
						"model_id"=>'$model_fare.model_id',
						"base_fare" => array('$add'=>array('$model_fare.base_fare',array('$multiply'=>array('$model_fare.base_fare',array('$divide'=>array($city_model_fare,100)))))),
						"min_fare" => array('$add'=>array('$model_fare.min_fare',array('$multiply'=>array('$model_fare.min_fare',array('$divide'=>array($city_model_fare,100)))))),
						"cancellation_fare" => array('$add'=>array('$model_fare.cancellation_fare',array('$multiply'=>array('$model_fare.cancellation_fare',array('$divide'=>array($city_model_fare,100)))))),
						"below_km" => array('$add'=>array('$model_fare.below_km',array('$multiply'=>array('$model_fare.below_km',array('$divide'=>array($city_model_fare,100)))))),
						"above_km" => array('$add'=>array('$model_fare.above_km',array('$multiply'=>array('$model_fare.above_km',array('$divide'=>array($city_model_fare,100)))))),
						"night_fare" => array('$add'=>array('$model_fare.night_fare',array('$multiply'=>array('$model_fare.night_fare',array('$divide'=>array($city_model_fare,100)))))),
						"minutes_fare" => array('$add'=>array('$model_fare.minutes_fare',array('$multiply'=>array('$model_fare.minutes_fare',array('$divide'=>array($city_model_fare,100)))))),
						"night_charge" => '$model_fare.night_charge',
						"night_timing_from" => '$model_fare.night_timing_from',
						"night_timing_to" => '$model_fare.night_timing_to',
						"waiting_time" => '$model_fare.waiting_time',
						"min_km" => '$model_fare.min_km',
						"below_above_km" => '$model_fare.below_above_km',
					)
				),
				array('$match' => array("_id"=>(int)$company_id,"model_id"=>(int)$model_id)),
			);
			$res = $this->mongo_db->aggregate(MDB_COMPANY,$arguments);
			if(!empty($res['result'])){
				$result[] = (object)$res['result'][0];
			}
			return $result;
		} else {
			$arguments = array(
				array('$project' => array(
						"_id"=>1,
						"base_fare"=>array('$add'=>array('$base_fare',array('$multiply'=>array('$base_fare',array('$divide'=>array($city_model_fare,100)))))),
						"min_fare" => array('$add'=>array('$min_fare',array('$multiply'=>array('$min_fare',array('$divide'=>array($city_model_fare,100)))))),
						"cancellation_fare" => array('$add'=>array('$cancellation_fare',array('$multiply'=>array('$cancellation_fare',array('$divide'=>array($city_model_fare,100)))))),
						"below_km" => array('$add'=>array('$below_km',array('$multiply'=>array('$below_km',array('$divide'=>array($city_model_fare,100)))))),
						"above_km" => array('$add'=>array('$above_km',array('$multiply'=>array('$above_km',array('$divide'=>array($city_model_fare,100)))))),
						"night_fare" => array('$add'=>array('$night_fare',array('$multiply'=>array('$night_fare',array('$divide'=>array($city_model_fare,100)))))),
						"minutes_fare" => array('$add'=>array('$minutes_fare',array('$multiply'=>array('$minutes_fare',array('$divide'=>array($city_model_fare,100)))))),
						"night_charge" => '$night_charge',
						"night_timing_from" => '$night_timing_from',
						"night_timing_to" => '$night_timing_to',
						"waiting_time" => '$waiting_time',
						"min_km" => '$min_km',
						"below_above_km" => '$below_above_km',
						"description" => '$description'
					)
				),
				array('$match' => array("_id"=>(int)$model_id)),
			);
			$res = $this->mongo_db->aggregate(MDB_MOTOR_MODEL,$arguments);
			if(!empty($res['result'])){
				$result[] = (object)$res['result'][0];
			}
			return $result;
		}
    }
}
?>
