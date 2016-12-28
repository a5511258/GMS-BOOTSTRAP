<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16/11/17
 * Time: 上午11:55
 */

namespace Service\Model;


class VehicleDeviceBindModel extends BaseBindInfoModel
{

    protected $tableName = 'vehicle_device_bind';

    protected $_validate = array(

        array('vehicle_id', 'require', '绑定参数车辆ID传递错误！'),

        array('device_id', 'require', '绑定参数设备ID传递错误！'),
    );

    protected function getTargetADbName()
    {
        return 'vehicle_base_info';
    }

    protected function getTargetBDbName()
    {
        return 'device_info';
    }
}