<?php
namespace Gps\Model;
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/9/22
 * Time: 15:22
 */
class LastGpsModel extends BaseGpsModel
{
    static $cache=array();
    const LAST_GPS_CACHE = "LAST_GPS_CACHE";
    //30分钟的超时时间
    const TIME_LIMIT=1800;
    /**
     * 尝试更新last_gps表.
     * @param $vehicleId
     * @param $gpsData
     * @return bool true=更新成功,false=更新失败，超时的gps
     */
    public function tryAdd($vehicleId, $gpsData)
    {
        $lastestGpsTime=$this->getLastGpsTime($vehicleId);
        if($lastestGpsTime)//如果没有旧的gps，或者是新的gps
        {
            //本次GPS时间比上次大
            $timeDiff=strtotime($gpsData["time"])-strtotime($lastestGpsTime);
            if ($timeDiff>0)
            {
                $this->updateLastestGps($vehicleId,$gpsData);
                return true;
            }else
            {
                return false;
            }
        }
    }

    /**
     * @param $vehicleId
     * @param $gpsData
     */
    private function updateLastestGps($vehicleId, $gpsData)
    {
        //更新缓存
        self::$cache[$vehicleId]=$gpsData["time"];
        S(self::LAST_GPS_CACHE,self::$cache);
        $gpsData["vehicle_id"]=$vehicleId;
        //更新lastgps表
        $this->add($gpsData,"",true);
    }

    /**
     * 获取上次gps日期
     * @param $vehicleId
     * @return bool|mixed
     */
    private function getLastGpsTime($vehicleId)
    {
        if (!self::$cache)
        {
            $cache=S(self::LAST_GPS_CACHE);
            if (!$cache)
            {
                $cache=$this->getField("vehicle_id,time");
                S(self::LAST_GPS_CACHE,$cache);
            }
            self::$cache=$cache;
        }
        return isset(self::$cache[$vehicleId])? self::$cache[$vehicleId]:"1990-10-11 11:39:24";
    }
}