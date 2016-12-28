<?php
namespace Gps\Model;
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/9/22
 * Time: 15:22
 */
class BaseGpsModel extends \Think\Model
{
    protected $connection = 'DB_GPS';
    protected $tablePrefix = '';
}