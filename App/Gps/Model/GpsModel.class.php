<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 22:03
 */

namespace Gps\Model;


class GpsModel extends BaseGpsModel
{

    const CREATE_SQL="CREATE TABLE`%s`(`lat`double DEFAULT NULL,`lng`double DEFAULT NULL,`time`datetime DEFAULT NULL,`speed`int(11)DEFAULT'0',`direction`int(11)DEFAULT'0',UNIQUE KEY`uk_%s`(`time`)USING HASH)ENGINE=InnoDB DEFAULT CHARSET=utf8";
    const TABLE_PRE="gps_";
    protected $Name;

    public function __construct($name)
    {
        $this->Name=$name;//先复制
        $this->checkTableExist();//检查表存在
        $this->tableName=$this->getRealTableName();
        parent::__construct();
    }

    function checkTableExist()
    {
        $tableName=$this->getRealTableName();
        $tempModel=M("","","DB_GPS");
        $result=$tempModel->execute("SHOW TABLES LIKE '$tableName';");
        if (!$result)
        {
            $createSql=sprintf(self::CREATE_SQL,$tableName,$tableName);
            $result=$tempModel->execute($createSql);
        }else{
            return true;
        }
    }
    function getRealTableName()
    {
        return self::TABLE_PRE.$this->Name;
    }

    public function getHistoryGps($startTime,$endTime)
    {
        $where["time"]=array(
            array('gt',$startTime),array('lt',$endTime)
        );
        return $this->where($where)->field("id",true)->order("time")->select();
    }
}