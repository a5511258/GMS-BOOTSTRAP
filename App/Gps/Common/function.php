<?php

/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/8/5
 * Time: 下午2:37
 */


function ajaxSuccess($msg,$paramData=null)
{
    $data["statusCode"]=200;
    $data["message"]=$msg;
    $data["data"]=$paramData;
    echo json_encode($data);
}
function ajaxError($msg)
{
    $data["statusCode"]=400;
    $data["message"]=$msg;
    echo json_encode($data);
}

function checkParam()
{
    $paramNames=func_get_args();
    $data=array();
    foreach ($paramNames as $key=>$paramName) {
        $paramValue=I($paramName);
        if($paramValue!=='')
        {
            $data[$paramName]=$paramValue;
        }else{
            return "缺少必要的参数 $paramName";
        }
    }
    return $data;
}




function delFile($dirName){
    if(file_exists($dirName) && $handle=opendir($dirName)){
        while(false!==($item = readdir($handle))){
            if($item!= "." && $item != ".."){
                if(file_exists($dirName.'/'.$item) && is_dir($dirName.'/'.$item)){
                    delFile($dirName.'/'.$item);
                }else{
                    if(unlink($dirName.'/'.$item)){
                        return true;
                    }
                }
            }
        }
        closedir( $handle);
    }
}
function utf8Header()
{
    header("Content-Type:text/html;charset=utf-8");
}
function workInterfaceEcho($msg)
{
    utf8Header();
    echo currentDate()."----".print_r($msg,true);
}

