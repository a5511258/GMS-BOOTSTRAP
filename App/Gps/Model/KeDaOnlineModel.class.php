<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/10/19
 * Time: 19:10
 */

namespace Gps\Model;


use Common\Model\BaseCacheModel;
use Think\Exception;

class KeDaOnlineModel extends  BaseCacheModel
{
    /**
     * 生成缓存数据
     * @return mixed
     */
    public function generateData()
    {
        $xml=$this->getXmlData();
        $data=$this->parseXmlData($xml);
        return $data;

    }

    public function getMainCacheKey()
    {
        return "KeDaOnlineData";
    }


    function getXmlData()
    {
        if (!USE_SIMULATE_DATA)
        {
            $url="http://133.1.0.160/playweb/bin/getdev_fcgi?random=0.7016537697929264";
            $return=$this->request($url,"getdevlist=1,ip=133.1.0.160,user=admin@kedacom,pass=admin123");
        }else{
            $return=file_get_contents("./Test/keda_online.xml");
        }
        return $return;
    }
    function parseXmlData($xmlData)
    {
        $xml = simplexml_load_string($xmlData);
        if($xml)
        {
            $onLineList=array();
            foreach ($xml->children() as $devgroup) {
                foreach ($devgroup->devinfo as $devItem) {
                    $devItem=(array)$devItem;
                    if($devItem["DvcOnlineState"])
                    {
                        $device=array();
                        $device["id"]=$devItem["puid"];
                        $device["state"]=$devItem["DvcOnlineState"];
                        $device["devname"]=$devItem["devname"];
                        $channelCount=$devItem["channum"];
                        for($i = 0;$i < $channelCount;$i++) {
                            if(isset($devItem["chanstate$i"])&&$devItem["chanstate$i"]!=0){
                                $channel=array();
                                $channel["state"]=$devItem["chanstate$i"];
                                $channel["index"]=$i;
                                $device["channel"][]=$channel;
                            }
                        }
                        $onLineList[]=$device;
                    }
                }
            }
            //对车辆信息进行转换
            $gmsVehicleModel=new VehicleDetailViewModel();
            $where["vehicle_from"]=2;
            $maps=$gmsVehicleModel->where($where)->getField("device_id,vehicle_id");
            $onLineCars=array();
            foreach ($onLineList as $onLineDevice) {
                $deviceId=explode("@",$onLineDevice["id"])[0];
                if (isset($maps[$deviceId]))
                {
                    $onLineDevice["id"]=$maps[$deviceId];
                    $onLineCars[$deviceId]=$onLineDevice;
                }
            }
            return $onLineCars;
        }else{
            $msg="接口返回数据异常";
            throw new Exception($msg);
        }
    }
    function request($url,$data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,3);   //只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_URL, $url);
        $r = curl_exec($ch);
        $statusCode=curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $r;
    }

    public function getOnlineCars($useIdIndex=false)
    {
        $onlineValues=$this->getValue();
        $returnData=array();
        foreach ($onlineValues as $onlineValue) {
            if ($useIdIndex)
            {
                $returnData[$onlineValue["id"]]=$onlineValue;
            }else{
                $returnData[]=$onlineValue;
            }
        }
        return $returnData;
    }

    public function getCacheRemain()
    {
        return 10;
    }

//    function object2array($object) {
//        if (is_object($object)) {
//            foreach ($object as $key => $value) {
//                $array[$key] = $value;
//            }
//        }
//        else {
//            $array = $object;
//        }
//        return $array;
//    }


}