<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/10/12
 * Time: 11:15
 */

namespace Gps\Model;


use Think\Model;

class BaseGmsModel extends Model
{
    protected $connection =DB_CONFIG_NAME_GMS;
}