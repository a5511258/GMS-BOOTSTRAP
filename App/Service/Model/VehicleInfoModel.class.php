<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16/7/20
 * Time: 上午10:24
 */

namespace Service\Model;
use Think\Model\RelationModel;


class VehicleInfoModel extends RelationModel
{
    protected $tablePrefix = 'service_';

    protected $_link = array(
        'GroupName' => array(
            'mapping_type'    => self::BELONGS_TO,
            'mapping_name'    => 'group_name',
            'class_name'    =>  'group_info',
            'foreign_key'   =>  'group_id',// 当前表中的字段
            'mapping_fields'=>'group_name',// 被关联表中的字段
            'as_fields' => 'group_name:group_name',
        ),

    );

}