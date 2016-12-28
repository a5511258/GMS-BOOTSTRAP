<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/11/11
 * Time: 上午8:58
 */

namespace Service\Model;

use Service\Utils\GroupInfoUtils;


class SimCardInfoModel extends BaseInfoModel
{

    protected $tableName = 'simcard_info';

    protected $_validate = array(
        array('group_id', 'require', '组织名称不能为空！'),
        array('simcard_no', 'require', 'SIM卡号码不能为空 或 SIM卡号码重复!', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
        array('simcard_no', '/^1[3-8]+\d{9}$/', 'SIM卡号码格式不正确！'),
        array('simcard_imei', 'require', 'IMEI编码不能为空 或 IMEI编码重复', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
        array('simcard_netword_type', 'require', 'SIM卡网络类型不能为空！'),
        array('status', 'require', '设备状态不能为空！'),
        array('status', 'require', '请选择正确的设备状态！', self::MUST_VALIDATE, '[1-2]', self::MODEL_BOTH),
        array('simcard_open_time', 'require', '开卡时间为必填内容!'),
        array('simcard_netword_type', '[0-7]', '请选择正确的SIM卡网络类型！'),
    );


    protected function formatDataContent($data)
    {

        $wordModel = new WordBookModel();

        $Utils = new GroupInfoUtils();

        $Bind = new DeviceSimCardBindModel();

        //格式化SIM卡网络类型
        $cacheSimCardNetType = $wordModel->getMap(WordBookModel::TYPE_SIMCARD_NET_TYPE);

        $cacheSimCardStates = $wordModel->getMap(WordBookModel::TYPE_STATES_TYPE);


        $result['total'] = $data['total'];

        $result['data'] = array();


        foreach ($data['data'] as $k => $simCardInfo) {

            $temp = array();
            $temp['id'] = $simCardInfo['id'];

            $temp['simcard_no'] = $simCardInfo['simcard_no'];

            $temp['group_id'] = $simCardInfo['group_id'];

            $temp['group_name'] = $Utils->getGroupName($simCardInfo['group_id']);

            $bindDevice = $Bind->getBindInfo($Bind::Type_DESC, $simCardInfo['id']);

            $temp['device_id'] = $bindDevice['id'];

            $temp['device_no'] = $bindDevice['device_no'];


            $temp['simcard_network_type'] = $cacheSimCardNetType[$simCardInfo['simcard_network_type']];

            $temp['simcard_flow'] = $simCardInfo['simcard_flow'];

            $temp['simcard_operator'] = $simCardInfo['simcard_operator'];

            $temp['status'] = $cacheSimCardStates[$simCardInfo['status']];

            $temp['simcard_imei'] = $simCardInfo['simcard_imei'];

            $temp['simcard_open_time'] = $simCardInfo['simcard_open_time'];

            $temp['remark'] = $simCardInfo['remark'];

            array_push($result['data'], $temp);

        }

        return $result;
    }

    protected function formatDataStructure($data)
    {
        return returnSearch($data['total'], $data['data']);
    }



    protected function getEffectMap($type)
    {

        if ('bind' == $type) {

            $bindModel = new DeviceSimCardBindModel();

            $bindInfo = $bindModel->getAllBindInfo();

            $usedSimCard = array();

            foreach ($bindInfo as $info) {

                array_push($usedSimCard, $info['simcard_id']);
            }

            $usedSimCard = implode(',', $usedSimCard);

            $map['id'] = array('not in', $usedSimCard);

            return $map;


        } else {
            return parent::getEffectMap($type); // TODO: Change the autogenerated stub
        }

    }

    protected function getEffectField()
    {
        return 'id,simcard_no as text';
    }

    protected function formatEffect($data)
    {

        $res = array();

        $temp['id'] = '';
        $temp['text'] = '解绑SIM卡';

        array_push($res, $temp);

        foreach ($data as $item) {
            array_push($res, $item);
        }

        return $res;


    }


    protected function search()
    {
        $map = array();

        $post_data = I('post.');

        if (isset($post_data['simcard_no']) && $post_data['simcard_no'] != '') {
            $map['simcard_no'] = array('LIKE', '%' . $post_data['simcard_no'] . '%');
        }

        if (isset($post_data['device_id']) && $post_data['device_id'] != '') {
            $map['device_id'] = $post_data['device_id'];
        }

        return $map;
    }


    /**
     * 实例化父类CreateData方法
     * 添加数据函数
     * 业务逻辑:
     * 1、SIM卡号码不能重名
     * 2、IMEI编码不能重名
     * 3、企业下无法添加SIM卡
     * 4、绑定设备编号
     */
    public function CreateData()
    {

        $post_data = I('post.');

        $Utils = new GroupInfoUtils();

        $group_id = $post_data['group_id'];

        if (2 == $Utils->getGroupType($group_id)) {

            $this->startTrans();

            $createData = $this->create($post_data);

            $bandDeviceID = $post_data['device_id'];

            unset($createData['id']);

            if ($createData) {

                $res = $this->add($createData);

                if (false !== $res) {

                    $Bind = new DeviceSimCardBindModel();

                    if ($bandDeviceID) {

                        //存在绑定数据 添加一条绑定数据
                        $res = $Bind->addBindInfo($Bind::Type_DESC, $bandDeviceID, $res);

                        //不存在绑定数据
                    } else {

                        //不存在绑定数据,删除可能存在的绑定数据
                        $res = $Bind->delBindInfo($Bind::Type_DESC, $res);
                    }

                    if ($res['Result']) {

                        $this->commit();

                        action_log('User_Add', MODULE_NAME, session('UserInfo')['service_group_id']);

                        return returnSuccess('添加成功!');
                    } else {

                        $this->rollback();

                        return returnError($res['Msg']);
                    }

                } else {

                    $this->rollback();

                    return returnError($this->getError());
                }

            } else {

                return returnError($this->getError());
            }
        } else {
            return returnError('非车队下无法添加SIM卡');
        }


    }


    /**
     * 复写父类 EditData 方法
     * 修改数据函数
     * 业务逻辑
     * 1、SIM卡号码不能重名
     * 2、IMEI编码不能重名
     * 3、企业下无法添加SIM卡
     * 3、绑定设备编号
     * @return mixed
     */
    public function EditData()
    {
        if (IS_POST) {

            $post_data = I('post.');

            $Utils = new GroupInfoUtils();

            $group_id = $post_data['group_id'];

            if (2 == $Utils->getGroupType($group_id)) {

                $this->startTrans();

                $createData = $this->create($post_data);

                $bandDeviceID = $post_data['device_id'];

                $id = $createData['id'];

                unset($createData['id']);

                if ($createData) {

                    $res = $this->where(array('id' => $id))->save($createData);

                    if (false !== $res) {

                        $Bind = new DeviceSimCardBindModel();
                        //存在待绑定数据
                        if ($bandDeviceID) {
                            $res = $Bind->addBindInfo($Bind::Type_DESC, $bandDeviceID, $id);

                            //不存在绑定数据
                        } else {
                            $res = $Bind->delBindInfo($Bind::Type_DESC, $id);
                        }
                        if ($res['Result']) {

                            $this->commit();

                            action_log('User_Add', MODULE_NAME, session('UserInfo')['service_group_id']);

                            return returnSuccess('更新成功!');
                        } else {

                            $this->rollback();

                            return returnError($res['Msg']);
                        }

                    } else {

                        $this->rollback();

                        return returnError($this->getError());
                    }

                } else {

                    return returnError($this->getError());
                }
            } else {
                return returnError('非车队下无法添加SIM卡');
            }
        } else {
            return parent::EditData(); // TODO: Change the autogenerated stub
        }
    }

    /**
     * @return string 返回导出文件名称
     */
    protected function getXlsName()
    {
        return 'SIM卡信息管理';
    }


}