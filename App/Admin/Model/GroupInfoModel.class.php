<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/7/14
 * Time: 下午4:26
 */

namespace Service\Model;

use Service\Utils\GroupInfoUtils;

class GroupInfoModel extends BaseInfoModel
{
    static $groupIds = array();

    public static function getSubGroupId()
    {
        if (!self::$groupIds) {
            $group_id = session('UserInfo')['service_group_id'];
            $Utils = new GroupInfoUtils();
            self::$groupIds = $Utils->getAllGroupID($group_id);
        }
        return self::$groupIds;
    }

    public function getGroupInfos($group_ids)
    {
        $groupWhere["group_id"] = array("in", $group_ids);
        return $this->where($groupWhere)->getField("group_id,parent_id,group_name,counties,id_level");
    }

    public function searchGroupName($groupName)
    {
        $group_ids = GroupInfoModel::getSubGroupId();
        $searchGroupWhere["group_name"] = array('like', "%$groupName%");
        $searchGroupWhere["group_id"] = array('in', $group_ids);
        $groupLevels = $this->where($searchGroupWhere)->getField("id_level", true);
        if ($groupLevels) {
            $formatGroupLevels = array();
            $needUnset = array();
            foreach ($groupLevels as $position => $groupLevel) {
                foreach ($groupLevels as $testPosition => $testLevel) {
                    if (strpos($groupLevel, $testLevel)) {
                        $needUnset[] = $testPosition;
                    }
                }
            }
            foreach ($needUnset as $unsetIndex) {
                unset($groupLevels[$unsetIndex]);
            }

            foreach ($groupLevels as $groupLevel) {

            }
        } else {
            return array();
        }
    }


    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('group_name', 'require', '组织名称不能为空！'),
        array('parent_id', 'require', '上级名称不能为空！'),
        array('limit_num', 'require', '限制车辆数不能为空！'),
        array('group_type', 'require', '组织类型不能为空！'),
        array('industry', 'require', '所属行业不能为空！', self::MUST_VALIDATE, '[0-9]', self::MODEL_BOTH),
        array('group_type', array(1, 2), '请选择正确的组织类型！', self::MUST_VALIDATE, 'in', self::MODEL_BOTH),
    );


    protected function search()
    {
        $map = array();

        $post_data = I('post.');

        if (isset($post_data['group_name']) && $post_data['group_name'] != '') {
            $map['group_name'] = array('LIKE', '%' . $post_data['group_name'] . '%');
        }

        if (isset($post_data['responsibility_people']) && $post_data['responsibility_people'] != '') {
            $map['responsibility_people'] = array('like', '%' . $post_data['responsibility_people'] . '%');
        }

        if (isset($post_data['tel_no']) && $post_data['tel_no'] != '') {
            $map['tel_no'] = array('like', '%' . $post_data['tel_no'] . '%');
        }

        return $map;
    }


    protected function formatDataContent($data)
    {

        $wordModel = new WordBookModel();
        //格式化组织类型
        $cacheGroupType = $wordModel->getMap(WordBookModel::TYPE_GROUP_TYPE);


        //格式化所属行业
        $cacheGroupIndustry = $wordModel->getMap(WordBookModel::TYPE_INDUSTRY_TYPE);


        $result = array();

        foreach ($data as $k => $groupInfo) {

            $temp = array();

            $temp['id'] = $groupInfo['group_id'];

            $temp['text'] = $groupInfo['group_name'];

            $temp['responsibility_people'] = $groupInfo['responsibility_people'];

            $temp['limit_num'] = $groupInfo['limit_num'];


            $data[$k]['group_type'] = $cacheGroupType[$groupInfo["group_type"]];

            $temp['group_type'] = $data[$k]['group_type'];


            $data[$k]['industry'] = $cacheGroupIndustry[$groupInfo["industry"]];

            $temp['industry'] = $data[$k]['industry'];

            $temp['tel_no'] = $groupInfo['tel_no'];

            $temp['address'] = $groupInfo['address'];

            $temp['description'] = $groupInfo['description'];

            //获取父节点名称
            $data[$k]['parent_name'] = $this->where(array(
                'group_id' => $groupInfo['parent_id']
            ))->find()['group_name'];


            $temp['parent_name'] = $data[$k]['parent_name'];

            $temp['ParentId'] = $groupInfo['parent_id'];

            $temp['iconCls'] = "icon-organization";//此处添加图标后会影响用户界面

            array_push($result, $temp);

        }

        return $result;
    }

    protected function formatDataStructure($data)
    {

        $temp_parent_id = $data[0]['ParentId'];


        $result = list_to_tree($data, 'id', 'ParentId', 'children', $temp_parent_id);


        return $result;


    }


    /**
     * @return array 返回表格列名称
     */
    protected function getColumnName()
    {
        return array(
            array('名称', '上级名称', '限制车辆数', '类型', '所属行业', '负责人', '联系电话', '地址', '描述'),
        );
    }

    /**
     * @return array 返回表格列字段名称
     */
    protected function getTableFieldName()
    {
        return array(
            'group_name', 'parent_name', 'limit_num', 'group_type', 'industry', 'responsibility_people', 'tel_no', 'address', 'description'
        );
    }

    /**
     * @return string 返回导出文件名称
     */
    protected function getXlsName()
    {
        return '组织架构管理';
    }

    /**
     * 实例化父类CreateData方法
     * 添加数据函数
     * 业务逻辑:
     * 1、父节点信息下不能存在同名
     * 2、车队下无法添加企业
     */
    public function CreateData()
    {
        // TODO: Implement CreateData() method.


        $post_data = I('post.');

        $parent_id = $post_data['parent_id'];

        if ($parent_id) {
            //查询父节点信息
            $parentInfo = $this->where(array(
                'group_id' => $post_data['parent_id']
            ))->find();

            if (1 == $parentInfo['group_type']) {
                unset($post_data['group_id']);

                $data_name = $this->where(array(
                    'group_name' => $post_data['group_name'],
                    'parent_id' => $parent_id
                ))->find();

                if (!$data_name) {

                    $this->startTrans();

                    $datas = $this->create($post_data);

                    if ($datas) {


                        if ($n = $this->add()) {

                            $datas['id_level'] = $parentInfo['id_level'] . $n . '/';

                            $res = $this->where(array(
                                'group_id' => $n
                            ))->save($datas);

                            if (false !== $res) {


                                $this->commit();

                                action_log('User_Add', MODULE_NAME, session('UserInfo')['service_group_id']);

                                return returnSuccess('添加成功');

                            } else {

                                $this->rollback();

                                return returnError($this->getError());
                            }

                        } else {

                            $this->rollback();

                            return returnError($this->getError());
                        }

                    } else {
                        return returnError($this->getError());
                    }


                } else {
                    return returnError('同组中已经存在相同组织名称,请修改后重试!');
                }
            } else {

                return returnError('非企业无法添加下级');
            }
        } else {
            return returnError('父节点信息不存在!');
        }


    }

    /**
     * 复写父类EditData 方法
     * 添加数据函数
     * 业务逻辑:
     * 1、父节点信息下不能存在同名
     * 2、车队下无法添加企业
     */
    public function EditData()
    {


        if (IS_POST) {


            $post_data = I('post.');


            if ('根节点' != $post_data['parent_id']) {
                //查询父节点信息
                $parent = $this->where(array(
                    'group_id' => $post_data['parent_id']
                ))->find();
            } else {
                $parent['group_type'] = 1;
            }

            if (1 == $parent['group_type']) {
                //保存节点group_id信息
                $id = $post_data['group_id'];
                unset($post_data['group_id']);

                if ('根节点' != $post_data['parent_id']) {
                    //更新节点id_level信息
                    $post_data['id_level'] = $parent['id_level'] . $id . '/';
                } else {
                    $post_data['id_level'] = '/';

                    $post_data['parent_id'] = 0;
                }


                $map['group_name'] = $post_data['group_name'];

                $map['parent_id'] = $post_data['parent_id'];

                $map['group_id'] = array('not in', $id);

                $map['_logic'] = 'and';

                //查询父节点下同名信息
                $data_name = $this->where($map)->find();

                if (!$data_name) {


                    //自动校验数据信息
                    $data = $this->create($post_data);

                    if ($data) {

                        $this->startTrans();

                        $res = $this->where(array('group_id' => $id))->save($data);

                        if (false !== $res) {
                            $this->commit();

                            action_log('User_Add', MODULE_NAME, session('UserInfo')['service_group_id']);

                            return returnSuccess('更新成功');

                        } else {
                            $this->rollback();

                            return returnError('创建失败，请稍后再试!');

                        }

                    } else {

                        return returnError($this->getError());

                    }

                } else {

                    return returnError('同组中已经存在相同组织名称,请修改后重试!');

                }
            } else {
                return returnError('非企业无法添加下级');

            }

        } else {

            //获取检索条件
            $groupId = I('get.groupID');

            $data = $this->where(array('group_id' => $groupId,))->find(); //查找语句

            if ($data) {
                return returnSuccess('查询成功', $data);
            } else {
                return returnError($this->getError());
            }

        }

    }


}