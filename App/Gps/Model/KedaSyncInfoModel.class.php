<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/10/19
 * Time: 18:49
 */

namespace Gps\Model;


class KedaSyncInfoModel extends BaseGpsModel
{

    static $syncInfo;

    /**
     * 初始同步时限
     * @return int
     */
    public static function getInitialSyncLength()
    {
        return 60*15;
    }

    /**
     * 大同步时限
     * @return int
     */
    public static function getBigSyncLength()
    {
        return 60*10;
    }

    public function getSyncInfo()
    {
        if (!self::$syncInfo)
        {
            self::$syncInfo=$this->select("device_id,vehicle_id,sync_time");
        }
        return self::$syncInfo;
    }

    public function getLastSyncTime($device_id)
    {
        $syncInfo=$this->getSyncInfo();
        if ($syncInfo&&isset($syncInfo[$device_id]))
        {
            return $syncInfo[$device_id]["sync_time"];
        }
        return "1970-01-01 00:00:00";
    }

}