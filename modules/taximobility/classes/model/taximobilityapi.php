<?php defined('SYSPATH') OR die('No Direct Script Access');
/****************************************************************
* Contains API model details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
Class Model_TaximobilityApi extends Model
{
    public function __construct()
    {
        $this->currentdate = Commonfunction::getCurrentTimeStamp();
		//MongoDB Instance
        $this->mongo_db    = MangoDB::instance('default');
    }
    
	/******** Get Taxi Speed *****************/
    public function get_taxi_speed($taxi_id = "")
    {
		//MongoDB
		$result = $this->mongo_db->findOne(MDB_TAXI,array('_id'=>(int)$taxi_id),array('taxi_speed'));
		return (!empty($result))?$result['taxi_speed']:0;
    }
    
	public function estimated_time($distance, $taxi_speed)
    {
        $ttime = "";
        if ($distance != 0 && $taxi_speed != 0) {
            $time = $distance / $taxi_speed;
            //Titanium.API.info("Response ETA" + distance + "-" + taxi_speed);					                                                                          
            $time = $time * 3600; // time duration in seconds
            $days = floor($time / (60 * 60 * 24));
            $time -= $days * (60 * 60 * 24);
            $hours = floor($time / (60 * 60));
            $time -= $hours * (60 * 60);
            $minutes = floor($time / 60);
            $time -= $minutes * 60;
            $seconds = floor($time);
            $time -= $seconds;
            if ($minutes > 0) {
                $ttime .= $minutes . __('Min') + ":";
            }
            if ($seconds > 0) {
                $ttime .= $seconds . __('Sec');
            }
        } else {
            $ttime = 1;
        }
        return $ttime;
    }
    
}
