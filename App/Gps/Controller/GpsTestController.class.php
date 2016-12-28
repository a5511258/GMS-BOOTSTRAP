<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/10/12
 * Time: 8:48
 */

namespace Gps\Controller;


use Gps\Model\KeDaGpsTestModel;
use Gps\Model\KedaInfoModel;
use Service\Controller\VehicleMaintenanceReportController;
use Service\Model\VehicleInfoModel;
use Service\Model\VehicleMaintenanceInfoModel;
use Service\Model\VehicleRecordModel;
use Service\Model\VehicleRepairInfoModel;
use Service\Model\VehicleViolationInfoModel;
use Service\Model\VehicleRoadTollInfoModel;
use Service\Model\VehicleOilConsumptionInfoModel;
use Service\Model\VehicleViolationViewModel;
use Think\Controller;

class GpsTestController extends Controller
{
    function generateKeTestData($deviceId)
    {
        $data = getParam();
        $testModel = new KeDaGpsTestModel($deviceId);
        if ($testModel->isTableExist) {
            if ($testModel->add($data)) {
                workInterfaceEcho("更新成功");
            } else {
                workInterfaceEcho("更新失败");
            }
        } else {
            workInterfaceEcho("表不存在");
        }
    }

    function insertKeCarData()
    {
        $data = getParam();
        $testModel = new KedaInfoModel();
        $deviceName = $testModel->getField("device_name", true);
        $model = new VehicleInfoModel();
        foreach ($deviceName as $item) {
            $data["device_name"] = $item;
            $data["vehicle_from"] = 2;
            $data["vehicle_license"] = $item;
            $data["group_id"] = 1;
            $model->add($data);
        }
    }

    function insertTestRepairData()
    {
        $data = getParam();
        $testModel = new VehicleRepairInfoModel();
        $model = new VehicleInfoModel();
        $cars = $model->select();
        foreach ($cars as $car) {
            $data["damage_time"] = $this->getRandomTime();
            $data["repair_cost"] = $this->getRandomInt();
            $data["repair_state"] = 1;
            $data["vehicle_id"] = $car["vehicle_id"];
            $testModel->add($data);
        }
    }

    function insertTestMaintenanceData()
    {
        $data = getParam();
        $testModel = new VehicleMaintenanceInfoModel();
        $model = new VehicleInfoModel();
        $cars = $model->select();
        foreach ($cars as $car) {
            $data["maintenance_time"] = $this->getRandomTime();
            $data["maintenance_cost"] = $this->getRandomInt();
            $data["vehicle_id"] = $car["vehicle_id"];
            $testModel->add($data);
        }
    }

    function insertTestViolationData()
    {
        $data = getParam();
        $testModel = new VehicleViolationInfoModel();
        $model = new VehicleInfoModel();
        $cars = $model->select();
        foreach ($cars as $car) {
            $data["violation_time"] = $this->getRandomTime();
            $data["violation_cost"] = $this->getRandomInt();
            $data["vehicle_id"] = $car["vehicle_id"];
            $testModel->add($data);
        }
    }

    function insertTestRecordData()
    {
        $data = getParam();
        $testModel = new VehicleRecordModel();
        $model = new VehicleInfoModel();
        $cars = $model->select();
        foreach ($cars as $car) {
            $data["vehicle_id"] = $car["vehicle_id"];
            $data["cost"] = $this->getRandomInt();
            $testModel->add($data);
        }
    }

    function insertTestRoadTollData()
    {
        $data = getParam();
        $testModel = new VehicleRoadTollInfoModel();
        $model = new VehicleInfoModel();
        $cars = $model->select();
        foreach ($cars as $car) {
            $data["vehicle_id"] = $car["vehicle_id"];
            $data["cost"] = $this->getRandomInt();
            $testModel->add($data);
        }
    }

    function insertTestOilConsumptionData()
    {
        $data = getParam();
        $testModel = new VehicleOilConsumptionInfoModel();
        $model = new VehicleInfoModel();
        $cars = $model->select();
        foreach ($cars as $car) {
            $data["vehicle_id"] = $car["vehicle_id"];
            $data["rising_number_of_fuel"] = $this->getRandomInt();
            $data["cost"] = $this->getRandomInt();
            $data["now_mileage"] = $this->getRandomInt();
            $testModel->add($data);
        }
    }


    function getRandomTime()
    {
        return currentDate(strtotime("-" . rand(1, 100) . " day"), time());
    }

    function getRandomInt()
    {
        return rand(1, 1000);
    }
}