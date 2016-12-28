<?php
namespace Gps\Model;
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/9/22
 * Time: 15:22
 */
class BaseMpsModel extends \Think\Model
{
    protected $connection = DB_CONFIG_NAME_KEDA_MPS;
    protected $tablePrefix = '';
}