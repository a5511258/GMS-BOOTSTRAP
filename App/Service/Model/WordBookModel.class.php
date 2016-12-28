<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/7/15
 * Time: 下午3:06
 */
namespace Service\Model;


class WordBookModel extends BaseInfoModel
{
    static $cache=array();
    const TYPE_VEHICLE_TYPE=1; //常量车辆类型
    const TYPE_GROUP_TYPE = 2; //常量组织类型
    const TYPE_INDUSTRY_TYPE = 3; //常量组织类型用途

    const TYPE_YES_NO_TYPE = 16;

    const TYPE_CHANNEL_NUM_TYPE = 15; //通道数量

    const TYPE_STATES_TYPE = 9;  //状态类型

    const TYPE_DEVICE_TYPE = 22; //设备类型

    const TYPE_SIMCARD_NET_TYPE = 12;




    public function getMap($typeId)
    {
        if (!isset(self::$cache[$typeId]))
        {
            self::$cache[$typeId]=$this->where(array("type_id"=>$typeId))->getField('word_id as id,word as text');
        }
        return self::$cache[$typeId];
    }

    public function CreateData()
    {
        // TODO: Implement CreateData() method.
    }


}