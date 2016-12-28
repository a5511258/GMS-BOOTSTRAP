<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/11/17
 * Time: 下午5:28
 */

namespace Service\Model;


abstract class BaseBindInfoModel extends ServiceCoreModel
{


    const Type_ASC = 'ASC';
    const Type_DESC = 'DESC';


    /**
     * 获取基本绑定关系字段名称
     * @return array
     */
    public function getBaseBindField()
    {
        $res = $this->getDbFields();
        unset($res[0]); //去除主键ID

        $res = array_values($res);
        return $res;
    }

    /**
     * 获取所有绑定信息
     * @return mixed
     */
    public function getAllBindInfo()
    {
        $fieldArray = $this->getBaseBindField();

        $str = implode(',', $fieldArray);

        $res = $this->field($str)->select();

        return $res;
    }

    abstract protected function getTargetADbName();

    abstract protected function getTargetBDbName();


    /**
     * 获取绑定关系数据  一对一 关系绑定
     * @param $type
     * $type = asc   A->B
     * $type = desc  B->A
     * @param $value
     * @return mixed
     */
    public function getBindInfo($type, $value)
    {

        $fieldArray = $this->getBaseBindField();
        $dao1 = D($this->getTargetADbName());
        $dao2 = D($this->getTargetBDbName());

        if (self::Type_ASC == $type) {


            $id = $this->where(array($fieldArray['0'] => $value))->find()[$fieldArray['1']];

            $Info = $dao2->where(array('id' => $id))->find();

        } else {
            $id = $this->where(array($fieldArray['1'] => $value))->find()[$fieldArray['0']];

            $Info = $dao1->where(array('id' => $id))->find();
        }


        return $Info;
    }


    /**
     * 添加基本绑定关系函数   一对一 关系绑定
     * @param $type
     * $type = asc   A->B
     * $type = desc  B->A
     * @param $param1     ID1
     * @param $param2     ID2
     * @return
     * returnSuccess        绑定成功返回
     * returnreturnError    绑定失败返回
     *
     * 业务逻辑:
     * 1、绑定关系表中存在绑定关系:修改现有绑定关系
     * 2、绑定关系表中不存在绑定关系:增加新的绑定关系
     *
     * @return mixed     返回操作结果
     */
    public function addBindInfo($type, $param1, $param2)
    {
        if ($param1 && $param2) {

            $fieldArray = $this->getBaseBindField();

            $this->startTrans();

            if (self::Type_ASC == $type) {

                $searchField = $fieldArray['0'];
                $searchValue = $param1;
                $setField = $fieldArray['1'];
                $setValue = $param2;
            } else {

                $searchField = $fieldArray['1'];
                $searchValue = $param2;
                $setField = $fieldArray['0'];
                $setValue = $param1;
            }

            $search = $this->where(array($searchField => $searchValue))->find();

            if ($search) {
                $res = $this->where(array('id' => $search['id']))->setField($setField, $setValue);

                if (false !== $res) {
                    $this->commit();
                    return returnSuccess('操作成功!');
                } else {
                    $this->rollback();
                    return returnError($this->getError());
                }
            } else {

                $temp[$fieldArray['0']] = $param1;
                $temp[$fieldArray['1']] = $param2;

                $createData = $this->create($temp);

                if ($createData) {

                    $res = $this->add($createData);

                    if (false !== $res) {

                        $this->commit();

                        return returnSuccess('操作成功!');
                    } else {

                        $this->rollback();
                        return returnError($this->getError());
                    }

                } else {
                    $this->rollback();
                    return returnError($this->getError());
                }
            }


        } else {
            return returnError('参数错误!');
        }
    }


    /**
     * 删除基本绑定关系函数  一对一 关系删除
     * @param $type
     * $type = asc      A->B
     * $type = desc     B-A
     * @param $ID       ID A/B
     * @return mixed
     * returnSuccess        删除绑定成功返回
     * returnreturnError    删除绑定失败返回
     */
    public function delBindInfo($type, $ID)
    {

        if ($ID) {

            $fieldArray = $this->getBaseBindField();

            $this->startTrans();

            if (self::Type_ASC == $type) {

                $res = $this->where(array($fieldArray['0'] => $ID))->delete();
            } else {

                $res = $this->where(array($fieldArray['1'] => $ID))->delete();
            }

            if (false !== $res) {

                $this->commit();
                return returnSuccess('操作成功!');

            } else {

                $this->rollback();
                return returnError('操作失败!');

            }

        } else {
            return returnError('参数错误!');
        }


    }


}