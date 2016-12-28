<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/9/23
 * Time: 14:56
 */
date_default_timezone_set('PRC');
$vehicle_id_arr=loadData();
$server="127.0.0.1";
//$server="test.51zsqc.com";
//$server="133.3.0.149";
$url="http://$server/CourtGms/index.php?m=Gps&c=GpsTest&a=generateKeTestData&";
//$url="http://test.51zsqc.com/CourtGms/index.php?m=Gps&c=GpsTest&a=generateKeTestData&";
//$url="http://test.51zsqc.com/CourtGms/index.php?m=Gps&c=Index&a=updateGps&";
$sleepTime=10;
while(true){
    foreach ($vehicle_id_arr as $id=>$vehicle_info) {
        if(is_array($vehicle_info)){
            $vehicle_info=updateData($vehicle_info);
            $req=$url.info2Str($vehicle_info);
            L($req);
//            $result=request($req);
            $result=file_get_contents($req);
            if ($result)
            {
                L($result);
            }else{
                L("更新失败");
            }
//            $result=file_get_contents($req);
            $vehicle_id_arr[$id]=$vehicle_info;
//            L($vehicle_info);
        }else{
            L($vehicle_info);
        }
    }
    sleep($sleepTime);
}
function info2Str($vehicle_info){
    $str="";
    foreach ($vehicle_info as $key => $value) {
        $str.="$key=$value&";
    }
    $str=substr($str,0,strlen($str)-1);
    return $str;
//    return sprintf("car_no=%s&",$vehicle_info["car_no"],$vehicle_info["lat"],$vehicle_info["lng"],$vehicle_info["time"],$vehicle_info["speed"],$vehicle_info["direction"]);
}

function updateData($vehicle_info){
    $distance=0.00001;
    $vehicle_info["pos_lat"]=$vehicle_info["pos_lat"]+rand(-5,150)*$distance;
    $vehicle_info["pos_lng"]= $vehicle_info["pos_lng"]+rand(-5,150)*$distance;
    $vehicle_info["time"]=time();
    $vehicle_info["speed"]=rand(0,3);
    $vehicle_info["direction"]=rand(1,360);
    return $vehicle_info;
}

function request($url,$is_get=true,$data="")
{
    $ch = curl_init();
    if (!$is_get)
    {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT,3);   //只需要设置一个秒的数量就可以
    curl_setopt($ch, CURLOPT_URL, $url);
    $r = curl_exec($ch);
    $statusCode=curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($statusCode==200)
    {
        return $r;
    }else{
        return false;
    }
}


function loadData()
{
    $initialInfo=array(
        "pos_lng"=>111.626125,
        "pos_lat"=>40.807445,
        "time"=>time(),
        "direction"=>0,
        "speed"=>0,
        "deviceId"=>0,
    );

    $deviceArr=array(
        "c779049aed924cfa8146128b17654ffb",
        "e97cbd5f9d06464191f57ba7da906ffe",
    );
    $vehicle_id_arr=array();
    foreach ($deviceArr as $device) {
        $initialInfo["deviceId"]=$device;
        $vehicle_id_arr[$device]=$initialInfo;
    }
    return $vehicle_id_arr;
}

function L($data,$title=null){
    echo print_r($data,true)."\n";
}
