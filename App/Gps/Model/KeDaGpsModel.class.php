<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 22:03
 */

namespace Gps\Model;


class KeDaGpsModel extends BaseMpsModel
{

    const CREATE_SQL="CREATE TABLE`%s`(`id`int(11)NOT NULL AUTO_INCREMENT,`deviceId`varchar(128)COLLATE utf8_bin DEFAULT NULL,`pos_lng`double DEFAULT'0',`pos_lat`double DEFAULT'0',`speed`double DEFAULT'0',`time`bigint(20)NOT NULL,`pos_earthlng`double DEFAULT'0',`pos_earthlat`double DEFAULT'0',KEY`id`(`id`),KEY`index_time`(`time`))ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
    protected $Name;
    protected $isAutoCreate=false;
    public $isTableExist=false;
    const TABLE_PRE="tbl";
    public function __construct($name)
    {
        $this->Name=$name;//先复制
        $this->isTableExist=$this->checkTableExist();//检查表存在
        $this->tableName=$this->getRealTableName();
        if ($this->isTableExist)
        {
            parent::__construct();
        }
    }

    function checkTableExist()
    {
        $tableName=$this->getRealTableName();
        $tempModel=M("","",$this->connection);
        $result=$tempModel->execute("SHOW TABLES LIKE '$tableName';");
        if (!$result)
        {
            $createSql=sprintf(self::CREATE_SQL,$tableName,$tableName);
            try{
                $result=$tempModel->execute($createSql);
                return true;
            }catch (\Exception $e)
            {
                return false;
            }
        }else{
            return true;
        }
    }
    function getRealTableName()
    {
        return self::TABLE_PRE.$this->Name;
    }

    public function getHistoryGps($startTime, $endTime)
    {
        $where["time"]=array(
            array('gt',strtotime($startTime)),array('lt',strtotime($endTime))
        );
        $data= $this->where($where)->field("id",true)->order("time")->select();
        foreach ( $data as $key=> $item) {
            $data[$key]=$this->convertToTXGps($item);
        }
        return $data;
    }

    static function convertToTXGps($kedaGps)
    {
        $item=array();
        $item["lat"]=$kedaGps["pos_earthlat"];
        $item["lng"]=$kedaGps["pos_earthlng"];
        $item["time"]=currentDate($kedaGps["time"]);
        $item["speed"]=$kedaGps["speed"];
        $item["direction"]=0;
        return $item;
    }
}