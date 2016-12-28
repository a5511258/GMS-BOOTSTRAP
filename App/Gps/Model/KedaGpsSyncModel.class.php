<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/10/13
 * Time: 9:25
 */

namespace Gps\Model;


use Think\Exception;

/**
 * 用来做科达数据同步
 * @package Gps\Model
 */
class KedaGpsSyncModel
{
    protected $mpsDB;
    
    public function __construct()
    {
        $mpsDB=M("","",DB_CONFIG_NAME_KEDA_MPS);
        $this->mpsDB=$mpsDB;
    }

    public function startSync(){

        $vehicleModel=new VehicleDetailViewModel();
        $deviceIds=$vehicleModel->where(array("vehicle_from"=>2))->getField("device_id",true);
        $kedaExistsDevices= $this->getKeDaDBExistDevices();
        $this->syncTables(array_intersect($deviceIds,$kedaExistsDevices));
//        if ($kedaExistsDevices&&$deviceIds)
//        {
//            $needSyncDevice=array();
//            foreach ($kedaExistsDevices as $tableName) {
//                if (in_array($tableName,$deviceIds))
//                {
//                    $needSyncDevice[]=$tableName;
//                }
//            }
//            $this->syncTables($needSyncDevice);
//        }
    }

    function getKeDaDBExistDevices()
    {
        $mpsDB=$this->mpsDB;
        $tables=$mpsDB->query("SHOW TABLES LIKE 'tbl%';");
        $deviceIds=array();
        //获取存在gps的deviceId和本地库录入deviceId的交集
        foreach ($tables as $table) {
            $deviceIds[]=substr($table["tables_in_mps (tbl%)"],3);
        }
        return $deviceIds;
    }
    public function syncTables($deviceIds)
    {
        foreach ($deviceIds as $table) {
            $this->syncDeviceLastGps($table);
        }
    }
    public function syncDeviceLastGps($deviceId)
    {

        $mpsDB=$this->mpsDB;
        $mpsDB->table("tbl".$deviceId);
        $newData=$mpsDB->order("time desc")->find();
        if(!$newData)
        {
            throw  new Exception("设备 $deviceId 数据表为空");
        }
        $newData = $this->convertKedaGps2Ours($newData);
        $lastGpsModel=new LastGpsModel();
        $gmsVehicleModel=new VehicleDetailViewModel();
        $vehicle_id=$gmsVehicleModel->getVehicleIdByDeviceId($deviceId);
        //插入gps数据
        //        $gpsModel=new GpsModel($vehicle_id);
        //        try{
        //            $addResult=$gpsModel->add($newData);
        //        }catch(Exception $e)
        //        {
        //            echo $e->getMessage();
        //        }
        return $lastGpsModel->tryAdd($vehicle_id,$newData);
    }

    /**
     * 同步在线科达车辆的GPS
     */
    public function startSyncOnlineGps()
    {
        $onlineModel=new KeDaOnlineModel();
        $onlineDevices=$onlineModel->getValue();
        $kedaExistsDevices= $this->getKeDaDBExistDevices();
        $result=array();
        foreach ($onlineDevices as $deviceId=>$onlineDevice) {
            if (in_array($deviceId,$kedaExistsDevices))
            {
                try{
                    if ($this->syncDeviceLastGps($deviceId)){
                        $result[]="刷新$deviceId GPS成功\n";
                    }else{
                        $result[]="刷新$deviceId GPS失败,过期数据\n";
                    }
                }catch(Exception $e)
                {
                    $result[]=$e->getMessage();
                }
            }else{
                $result[]="$deviceId 未开启科达GPS数据\n";
            }
        }
        return $result;
    }

    /**
     * @param $newData
     * @return mixed
     */
    protected function convertKedaGps2Ours($newData)
    {
        return KeDaGpsModel::convertToTXGps($newData);
    }

    private function checkData($newData)
    {
        
    }
}