<?php
/**
 * 科达GPS信息同步脚本,10秒钟同步一次.应当使用supervisor用来监控本脚本
 * User: daniel
 * Date: 2016/10/14 0014
 * Time: 16:58
 */
while(true)
{
//    $url="http://test.51zsqc.com/CourtGms/index.php?m=Gps&c=Sync&a=syncKeDaGPS";
    $url="http://127.0.0.1/CourtGms/index.php?m=Gps&c=Sync&a=syncKeDaGPS";
//    $url="http://133.3.0.149/CourtGms/index.php?m=Gps&c=Sync&a=syncKeDaGPS";
    try{
        $data=file_get_contents($url);
        DLog($data);
    }catch(Exception $e)
    {
       DLog($e);
    }
    sleep(10);
}
function DLog($data){
   $data= print_r($data,true);
    echo date("Y-m-d H:i:s ------------\n ")." ".$data."\n";
}
