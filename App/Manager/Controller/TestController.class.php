<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/7/27
 * Time: 上午8:18
 */

namespace Manager\Controller;
use Think\Controller;

class TestController extends Controller {
    public function index(){
        $a = Is_Auth('Admin/Index/index');
        $this->display();
    }

    public function test1($windowId = "setting"){
        $this->assign("windowId",$windowId);
        $this->display();
    }
    public function test2($windowId = "setting"){
        $this->assign("windowId",$windowId);
        $this->display();
    }

    public function test3(){
        $Auth = new \Common\Libs\Auth();
        $AUTH_KEY=session(C('AUTH_KEY'));
        echo "uid:";
        dump($AUTH_KEY);
        //当前权限表达式
        $Auth_Rule = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        $a = 'Admin/Index/index';
        echo "uid:";
        dump($a);
        if (! $Auth->check ($a,$AUTH_KEY)) {
            echo "ok";
        }else{
            echo "nok";

        }
    }

    public function readCSV(){
            $file = fopen('1.csv','r');
            $data = fgetcsv($file);
            $data = eval('return '.iconv('gbk','utf-8',var_export($data,true)).';');
            echo count($data);
            $num = intval(count($data) / 12);

//            echo $num;
//            $result = array_chunk($data, $num);
//            dump($result);
            $result = array();
            dump($data);
            $vehicle_id = 3;
            $status = 262147;
            $altitude = 1096;
            $directionDesc = ["正北","东北","正东","东南","正南","西南","正西","西北"];




//            for($i=0;$i<count($directionDesc);$i++){
//                $directionDesc[$i] = mb_convert_encoding($directionDesc[$i],'gb2312');
//            }
        $direction = array_search($data[2],$directionDesc) * 45;

        echo bin2hex($data[2]);
        dump($direction);
        echo bin2hex($directionDesc[3]);

            $dao = M("gps",null,"DB_CONFIG1");
            for($i=0;$i<count($data);$i+=12){
                $lat = $data[$i+7];
                $lng = $data[$i+6];
                $time = $data[$i+10];
                dump($data[$i+2]);
                $direction = array_search($data[$i+2],$directionDesc) * 45;
                dump($direction);
                $d["vehicle_id"] = $vehicle_id;
                $d["status"] = $status;
                $d["altitude"] = $altitude;
                $d["lat"] = $lat;
                $d["lng"] = $lng;
                $d["time"] = $time;
                $d["direction"] = $direction;
                echo $dao->add($d);

            }
            dump($result);

            //print_r($goods_list);

            /* foreach ($goods_list as $arr){
                if ($arr[0]!=""){
                    echo $arr[0]."<br>";
                }
            } */
//             dump($goods_list);

             fclose($file);
    }

}