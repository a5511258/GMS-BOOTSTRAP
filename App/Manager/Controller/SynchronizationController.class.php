<?php
/**
 * 用于与红树林同步数据
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/9/12
 * Time: 上午9:25
 */

namespace Manager\Controller;
use Think\Controller;
class SynchronizationController extends Controller
{
    public function SynchronizationVehicleDeviceInfos(){
        header("Content-type: text/html; charset=utf-8");
        $dao = M();
        $dao_vehicle = M("vehicleinfo",null,"DB_CONFIG2");
        $dao_device = M("deviceinfo",null,"DB_CONFIG2");


        $sql = "SELECT * FROM service_device_info,service_vehicle_info,service_vehicle_device WHERE
	              service_vehicle_info.vehicle_id = service_vehicle_device.vehicle_id
                  AND service_device_info.device_id = service_vehicle_device.device_id
                  ;";
        $datas = $dao->query($sql);

        for ($i=0;$i<count($datas);$i++){
            $data = $datas[$i];

            $car_license = $data['car_license'];
            $vehicleInfo = array();
            $vehicleInfo['CarLicense']  = $car_license;
            $vehicleInfo['VehicleId']   = $data['vehicle_id'];
            $vehicleInfo['PlateColor']  = $data['plate_color'];
            $vehicleInfo['GroupId']     = $data['group_id'];
            $vehicleInfo['Brand']       = $data['brand'];
            $vehicleInfo['Manufacturer'] = $data['manufacturer'];
            $vehicleInfo['ChannelNum']  = $data['channel_num'];
            $vehicleInfo['DeviceType']   = $data['device_type'];
            $vehicleInfo['DeviceNo']    = $data['device_no'];
            $vehicleInfo['Is_Video']    = $data['is_video'];
            $vehicleInfo['BarCode']     = $data['bar_code'];
            $vehicleInfo['AccountStatus'] = $data['account_status'];
            $vehicleInfo['ChassisNo']   = $data['chassis_no'];
            $vehicleInfo['CityId']      = $data['city_id'];
            $vehicleInfo['EngineNo']    = $data['engine_no'];
            $vehicleInfo['FuelType']    = $data['fuel_type'];
            $vehicleInfo['LimitNo']     = $data['limit_no'];
            $vehicleInfo['LimitUnit']   = $data['limit_unit'];
            $vehicleInfo['ProvinceId']  = $data['province_id'];
            $vehicleInfo['RoadTransportNo'] = $data['road_transport_no'];
            $vehicleInfo['RechnicalLevel'] = $data['technical_level'];

            $vehicleInfo['VehicleType'] = $data['vehicle_type'];
            $vehicleInfo['Vin']         = $data['vin'];
            $vehicleInfo['WorkState']   = $data['work_state'];
            $vehicleInfo['IsNetwork']   = $data['is_network'];
            $vehicleInfo['DeviceNo']    = $data['device_no'];


            $c = $dao_vehicle->where(array(
                'CarLicense' => $car_license
            ))->count();
            echo "<br>".$car_license.":".$c."个<br>";
            if ($c == 1){
                if($n = $dao_vehicle->where(array(
                    'CarLicense' => $car_license
                ))->save($vehicleInfo)){
                    dump($n);
                    echo $i." 更新\n";
                }else{
                    echo $i." 更新没成功";
                }
            }else{
                if ($n = $dao_vehicle->add($vehicleInfo)){
                    dump($n);
                    echo $i." 创建\n";
                }else {
                    echo $i." 创建没成功";
                }
            }



        }
    }
}
