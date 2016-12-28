<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/10/10
 * Time: 9:50
 */

namespace Gps\Model;


use Service\Model\BaseServiceModel;
use Think\Model;

class VehicleDetailViewModel extends BaseServiceModel
{


    function getVehicleIdByName($deviceName)
    {
        $where["device_name"]=$deviceName;
        $maps= $this->where($where)->getField("device_name,vehicle_id");
        if (($maps))
        {
            return $maps[$deviceName];
        }else{
            return false;
        }
    }
    function getVehicleIdByDeviceId($deviceId)
    {
        $where["device_id"]=$deviceId;
        $maps= $this->where($where)->getField("device_id,vehicle_id");
        if (($maps))
        {
            return $maps[$deviceId];
        }else{
            return false;
        }
    }
}