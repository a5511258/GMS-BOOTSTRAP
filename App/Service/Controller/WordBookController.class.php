<?php
/**
 * 数据字典
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/7/15
 * Time: 下午3:04
 */
namespace Service\Controller;
use Think\Controller;

class WordBookController extends Controller {

    /**
     * 依据数据字典类别，返回对应的数据字典
     * @param int $typeId 数据字典类别，
     */
    function getWordBook($typeId=-1){
//        echo $typeId;
        $dao = D('word_book');

        if ($typeId == -1){
            $datas = $dao->field('word_id,word')->select();
            echo json_encode($datas);
            return;
        }else{
//            $datas = $dao->where(array('type_id'=>$typeId))->field('word_id,word')->select();
            $datas = $dao->where(array('type_id'=>$typeId))->select();
            echo json_encode($datas);
            return;
        }
    }

    /**
     * user:li
     * 获取报警信息
     * @param int $AlarmSource 报警来源
     */
    function GetAllAlarm($AlarmSource=-1){
        $dao = M("alarm_info",null,"DB_CONFIG1");

        if ($AlarmSource == -1){
            $datas = $dao->field('id,alarm_define as text')->select();
            echo json_encode($datas);
            return;
        }else{
            $datas = $dao->field('id,alarm_define as text')->where(array('alarm_source'=>$AlarmSource))->select();
            echo json_encode($datas);
            return;
        }
    }

    function importTest(){
        header("Content-type: text/html; charset=utf-8");
        $url = 'http://test.51zsqc.com:6006/Ferry/Report/VehicleLog/Default.do?Action=GetOperateType&Type=post';

//        $d = file_get_contents($url);
        $d = $this->request($url);
//        $a = mb_convert_encoding($d,"gbk","utf-8");
//        echo '123';
//        dump($d);
        $datas = json_decode($d,true); ;
//        dump($datas);
//            echo $datas[0]['text'];
//        $dao = D('operater');
//        for($i=0;$i<count($datas);$i++){
//            echo $datas[$i]['text'];
//            $x['operate_name'] = $datas[$i]['text'];
//            echo $x['operate_name'];
//            $dao->add($x);
//        }

    }

    function request($url){


        $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36';
        $ua1 = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.158888800.95 Safari/537.36 SE 2.X MetaSr 1.0';
        $proxy = '116.114.18.195:6006';

        $header = array(
            'Host: test.51zsqc.com:6006',
            'Cache-Control: max-age=0',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.76 Mobile Safari/537.36',
            'Accept: */*',
            'Accept-Encoding: gzip, deflate, sdch',
            'Accept-Language: zh-CN,zh;q=0.8,en;q=0.6',
            'Cookie: PHPSESSID=hn8bvgg46a8q40o9d4vshl8ac4; MVSP.U=VUlEPTEmVU49YWRtaW4mR0lEPTE0Njg0MDEyMzczODk1JlJJRD0x; MVSP.C=Vj0yLjAuMC4wJkw9emgtQ04mVD1ncmF5; JSESSIONID=1CF59D858CADB4B008B5AA9641A9D593'    );


//        Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_USERAGENT,$ua);
        $r = curl_exec($ch);
        $code =curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result['r'] = $r;
        $result['c'] = $code;

        return $r;

    }

}