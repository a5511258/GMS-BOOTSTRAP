<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/7/20
 * Time: 下午3:34
 */

namespace Service\Utils;
class JsonUtils
{

    /**
     * 在将 json字符串转成数组对象前，先转换html特殊字符，
     * 防止因特殊字符导致转换失败。
     * @param $str
     * @return mixed
     */
    function formatBeforeDecode($str){
        $str = str_replace("&gt;",">",$str);
        $str = str_replace("&nbsp;"," ",$str);

        $str = str_replace("&quot;","\"",$str);
        $str = str_replace("&#39;","\'",$str);
        $str = str_replace("\\\\;","\\",$str);
        $str = str_replace("\\n;","\n",$str);
        $str = str_replace("\\r;","\r",$str);
        return $str;
    }

    function isBeginWith($mainStr,$preStr){
        if (strlen($mainStr) < strlen($preStr)){
            return false;
        }else {
            $pre = substr($mainStr,0,strlen($preStr));
            return ($preStr == $pre);
        }
    }
}