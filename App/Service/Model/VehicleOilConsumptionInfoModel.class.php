<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/11/1
 * Time: 下午5:10
 */

namespace Service\Model;



class VehicleOilConsumptionInfoModel extends BaseServiceModel
{


    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('vehicle_id', 'require', '车牌号码不能为空！'),
        array('cost', 'require', '费用不能为空！'),
        array('rising_number_of_fuel', 'require', '加油数量不能为空！')
    );
}