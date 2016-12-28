<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/7/13
 * Time: 下午4:14
 */

namespace Service\Controller;


use Service\Model\WordBookModel;


class GroupInfoController extends BaseInfoController
{

    //系统默认模型
    private $Model = null;

    protected function _initialize()
    {
        //继承初始化方法
        parent::_initialize();
        //设置控制器默认模型
        $this->Model = D('group_info');
    }


    /**
     *
     * 获取数据
     */
    public function getGroupInfoToList()
    {

        $res = $this->Model->IndexData();

        echo json_encode($res);
    }


    public function add_edit()
    {

        $wordBookModel = new WordBookModel();

        $type = I('get.type');

        if ('add' == $type) {
            $type = "添加";
        } else {
            $type = "编辑";

            $_info = $this->Model->EditData();

            $this->assign('_info', $_info['Data']);

        }

        $this->assign('_type', $type);

        $temp = $wordBookModel->getMap($wordBookModel::TYPE_GROUP_TYPE);

        $dataType = array();

        foreach ($temp as $key => $val){

            $temp1['id'] = $key;
            $temp1['text'] = $val;

            array_push($dataType,$temp1);
        }

        $this->assign('_dataType', json_encode($dataType));

        $temp = $wordBookModel->getMap($wordBookModel::TYPE_INDUSTRY_TYPE);

        $dataIndustry = array();

        foreach ($temp as $key => $val){

            $temp1['id'] = $key;
            $temp1['text'] = $val;

            array_push($dataIndustry,$temp1);
        }


        $this->assign('_dataIndustry', json_encode($dataIndustry));


        $this->display();
    }



    /**
     * 添加
     */
    public function Create()
    {
        $res = $this->Model->CreateData();

        echo json_encode($res);
    }

    /**
     *  删除一组数据
     *  数据库得加触发器，在删除之前，移除所有相关数据
     */
//    function del()
//    {
//        if (IS_POST) {
//            $post_data = I('post.data');
//
//            $post_data = JsonUtils::formatBeforeDecode($post_data);
//
//            $info = json_decode($post_data,true);
//            if ($info){
//                $arr = array();
////                进行预处理，处理完成后再查询数据库
//                for ($i = 0;$i< count($info);$i++){
//
//                    if (count($arr)<=0){
//                        array_push($arr,$info[$i]['id_level']);
//                    }else {
//                        $currData = $info[$i]['id_level'];
//                        $n = count($arr);
//                        for ($j = 0;$j< $n;$j++){
//
//
//                            if (JsonUtils::isBeginWith($currData,$arr[$j])){
//                                break;
//                            }else if (JsonUtils::isBeginWith($arr[$j],$currData)){
//                                $arr[$j] = $currData;
//                                break;
//                            }else {
//                                array_push($arr,$currData);
//                                break;
//                            }
//                        }
//                    }
//
//                }
//                $count = 0;
//                for ($i = 0; $i< count($arr);$i++){
//                    $dao = D('group_info');
//                    $map['id_level'] = array('LIKE', $arr[$i] . '%');
//                    if($n = $dao->where($map)->delete()){
//                        $count += $n;
//                    }
//                }
//
//
//                if ($count>0){
//                    $result['Msg'] = "共删除了".$count."条数据";
//                }else {
//                    $result['Msg'] = "删除失败";
//                }
//                action_log('User_Del', MODULE_NAME, session('UserInfo')['service_group_id']);
//                echo json_encode($result);
//
//            }else{
//                action_log('User_Del', MODULE_NAME, session('UserInfo')['service_group_id']);
//                $result['Msg'] = "参数错误";
//                echo json_encode($result);
//            }
//        } else {
//
//        }
//    }

    /**
     * 通过groupID删除某条，并同时移除对应的子节点
     */
    public function Delete()
    {
        $res = $this->Model->DeleteData();

        echo json_encode($res);
    }

    /**
     * 编辑
     */
    public function Edit()
    {
        $res = $this->Model->EditData();

        echo json_encode($res);

    }

    public function exportData(){


        $res = $this->Model->ExportData();

        echo json_encode($res);
    }





}