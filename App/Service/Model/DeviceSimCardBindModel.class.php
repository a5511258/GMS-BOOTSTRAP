<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/11/17
 * Time: 上午11:55
 */

namespace Service\Model;


class DeviceSimCardBindModel extends BaseBindInfoModel
{

    protected $tableName = 'device_simcard_bind';

    protected $_validate = array(

        array('device_id', 'require', '绑定参数设备ID传递错误！'),

        array('simcard_id', 'require', '绑定参数Sim卡ID传递错误！'),
    );

    protected function getTargetADbName()
    {
        return 'device_info';
    }

    protected function getTargetBDbName()
    {
        return 'simcard_info';
    }
}