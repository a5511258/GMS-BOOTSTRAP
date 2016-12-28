<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2015/12/29
 * Time: 18:00
 */

namespace Gps\Model;


use Think\Page;

class BaiduConvertModel
{
    /**
     * 将一组gps坐标转换为百度坐标
     * @param $gpsArray
     * @param int $type 1=net  2=php
     * @return array|bool
     */
    public static function convertAllToBaidu($gpsArray,$type=2)
    {
        if (!$gpsArray)
        {
            return array();
        }
        if (1==$type)
        {
            $return=array();
            if (count($gpsArray)>100)
            {
                $convertArr= array_chunk($gpsArray,100);
                foreach ($convertArr as $item) {
                    $item=self::convertMutilByNet($item);
                    $return=array_merge($return,$item);
                }
                return $return;
            }else{
                $item=self::convertMutilByNet($gpsArray);
                return $item;
            }
        }else{
            $gpsConvert=new GpsConverterModel();
            foreach ($gpsArray as $key=>$item) {
                $gpsArray[$key]=$gpsConvert->wgs2bd($item);
            }
            return $gpsArray;
        }
    }

    /**
     * @param $lng
     * @param $lat
     * @return array|bool
     */
    public static function convertPointToBaidu($lng, $lat)
    {
        $baiduUrl = "http://api.map.baidu.com/geoconv/v1/?coords=";
        $tailUrl = "&from1&to=5&ak=6f6332348e20 c2795d4bc3f28e436501";
        $baseUrl = $baiduUrl;
        $search = $lng . "," . $lat;
        $searchUrl = $baseUrl . $search . $tailUrl;
        $content = file_get_contents($searchUrl);
        $result = json_decode($content, true);
        if ($result['status'] == 0) {
            foreach ($result['result'] as $value) {
                return array("lng" => $value['x'], "lat" => $value['y']);
            }
        }
        return false;
    }

    /**
     * @param $gpsArray
     * @param $convertResult
     * @return bool
     */
    private static function convertMutilByNet($gpsArray)
    {
        $baiduUrl = "http://api.map.baidu.com/geoconv/v1/?coords=";
        $tailUrl = "&from1&to=5&ak=6f6332348e20c2795d4bc3f28e436501";
        $baseUrl = $baiduUrl;
        $search = "";
        $positionMap = array();
        foreach ($gpsArray as $key => $value) {
            if ($value['lng'] && $value['lat']) {
                $positionMap[] = $key;
                $search = $search . $value['lng'] . "," . $value['lat'] . ";";
            }
        }
        $search = substr($search, 0, -1);
        $searchUrl = $baseUrl . $search . $tailUrl;
        $content = file_get_contents($searchUrl);
        $result = json_decode($content, true);
        if ($result['status'] == 0) {
            foreach ($result['result'] as $value) {
                $convertResult[] = array("lng" => $value['x'], "lat" => $value['y']);
            }
            foreach ($positionMap as $key => $value) {
                $gpsArray[$value] = array_merge($gpsArray[$value], $convertResult[$key]);
            }
            return $gpsArray;
        }
        return false;
    }
}