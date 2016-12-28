<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/11/11
 * Time: 上午9:02
 */

namespace Service\Model;

use Service\Utils\GroupInfoUtils;

abstract class BaseInfoModel extends ServiceCoreModel
{

    /**
     *
     * 默认检索条件
     * @return array
     */

    protected function search()
    {
        return array();
    }

    /**
     * 格式化indexData函数返回数据的结构
     * @param $data
     * @return mixed
     */
    protected function formatDataStructure($data)
    {
        return $data;
    }


    /**
     *
     * 格式化indexData函数返回数据的内容
     *
     * @param $data
     * @return mixed
     */
    protected function formatDataContent($data)
    {
        return $data;
    }


    /**
     * @return array 返回表格列名称
     */
    protected function getColumnName()
    {
        return array();
    }

    /**
     * @return array 返回表格列字段名称
     */
    protected function getTableFieldName()
    {
        return array();
    }

    /**
     * @return string 返回导出文件名称
     */
    protected function getXlsName()
    {
        return '管理';
    }


    /**
     * 基础信息默认查询方式
     *
     * @return array|mixed
     */
    public function IndexData()
    {


        $Utils = new GroupInfoUtils();

        $groupId = session('UserInfo')['service_group_id'];

        $groupIds = $Utils->getAllGroupID($groupId);


        $map = $this->search();

        $map['group_id'] = array('in', $groupIds);
        $map['_logic'] = 'and';


        if (CONTROLLER_NAME == "GroupInfo") {
            $data = $this->where($map)->order('parent_id')->select();
        } else {
            $post_data = I('post.');
            $post_data ['first'] = $post_data ['rows'] * ($post_data ['page'] - 1);
            $data['total'] = $this->where($map)->count();
            if ($data['total'] == 0) {
                $data['data'] = array();
            } else {
                $data['data'] = $this->where($map)
                    ->order($post_data ['sort'] . ' ' . $post_data ['order'])
                    ->limit($post_data ['first'] . ',' . $post_data ['rows'])
                    ->select();
            }
        }

        $data = $this->formatDataContent($data);

        $data = $this->formatDataStructure($data);

        return $data;
    }


    public function isExist()
    {

    }


    /**
     *
     * 添加数据(应为各个界面业务逻辑不同抽象为虚函数)
     * @return mixed
     */
    abstract public function CreateData();


    /**
     * post请求为修改数据库get请求为查询数据库数据
     * 根据提供的group_id ,id 修改数据
     * 修改数据(根据不同界面业务逻辑需覆盖父函数)
     */
    public function EditData()
    {
        if (IS_GET) {


            $Utils = new GroupInfoUtils();


            //获取检索条件
            $groupId = I('get.ID');

            $data = $this->where(array('id' => $groupId,))->find(); //查找语句

            if (isset($data['group_id'])) {

                $data['group_name'] = $Utils->getGroupName($data['group_id']);

            }

            if ($data) {
                return returnSuccess('查询成功', $data);
            } else {
                return returnError($this->getError());
            }
        }
    }


    /**
     * 移动目标组织单位位置
     * post请求 id 目标group_id
     * 根据业务需求 需复写此方法
     */
    public function ChangeGroup()
    {

    }


    /**
     * 基础信息删除函数
     * 前台需传递参数  id  group_id
     */
    public function DeleteData()
    {


        $postData = I('post.');

        $this->startTrans();

        $res = $this->where(
            array(
                'group_id' => $postData['group_id'],
                'id' => $postData['id'])
        )->delete();

        if ($res) {

            $this->commit();

            action_log('User_Del', MODULE_NAME, session('UserInfo')['service_group_id']);

            return returnSuccess('删除成功!');


        } else {

            $this->rollback();

            return returnError($this->getError());

        }


    }


    /**
     *
     * 返回有效有效条件
     * @return array
     */
    protected function getEffectMap($type)
    {
        return array();
    }

    /**
     * 返回获取的字段名称
     * @return string
     */
    protected function getEffectField()
    {
        return '';
    }

    protected function formatEffect($data)
    {
        return $data;
    }


    /**
     *
     * 获取有效 SIM卡 设备  车牌号码
     * @param string $type 类型 全部  部分
     * $type = all              获取全部
     * $type = bind             依据groupID 获取可以绑定的设备
     * @param string $groupID 依据的groupID
     * @return mixed
     */
    public function getEffectiveInfo($type = 'all', $groupID = '')
    {

        $Utils = new GroupInfoUtils();

        $map = $this->getEffectMap($type);

        if ('' != $groupID) {

            $groupIds = $groupID;
        } else {

            $groupIds = $Utils->getAllGroupID(session('UserInfo')['service_group_id']);

        }
        $map['group_id'] = array('in', $groupIds);
        $map['status'] = '1';

        $strField = $this->getEffectField();

        $res = $this->where($map)->field($strField)->select();

        if ('bind' == $type) {

            $res = $this->formatEffect($res);

            return $res;
        } else {
            return $res;
        }
    }


    /**
     * 基础信息导出函数
     */
    public function ExportData()
    {

        $res = $this->getColumnName();

        $tableFieldName = $this->getTableFieldName();

        $postData = I('post.');

        if (!empty($postData['rows'])) {

            foreach ($postData['rows'] as $dataTable) {
                $content = array();

                foreach ($tableFieldName as $field) {

                    array_push($content, $dataTable[$field]);

                }
                array_push($res, $content);

            }

        }

        $time = time();
        $fileName = $this->getXlsName() . $time . ".xls";

        create_xls($res, $fileName);


        return returnSuccess('导出成功', "/CourtGms/Export/" . $fileName);
    }
}