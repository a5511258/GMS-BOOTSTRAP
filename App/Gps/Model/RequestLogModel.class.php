<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 22:03
 */

namespace Gps\Model;


class RequestLogModel extends BaseServiceModel
{
    const TYPE_VEHICLE_RECORD_SYNC=1;

    public function getLastSyncRecordTime()
    {
        $data=$this->find(self::TYPE_VEHICLE_RECORD_SYNC);
        return $data["time"];
    }

    public function setLastSyncRecordTime($currDate)
    {
        $where["id"]=self::TYPE_VEHICLE_RECORD_SYNC;
        $where["time"]=$currDate;
        return $this->save($where);
    }
}