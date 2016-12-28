<?php

/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/7/18
 * Time: 下午5:37
 */

namespace Service\Utils;
class Test
{
    var $test = array(
        array('LimitNum' => null, 'ParentId' => 0, 'GroupType' => 1, 'Description' => '1212', 'GroupName' => 'Center', 'Address' => '111', 'IdLevel' => '/1/', 'GroupId' => 1, 'TelNo' => '', 'GroupFatherName' => null, 'Industry' => 6),
        array('LimitNum' => null, 'ParentId' => 1, 'GroupType' => 2, 'Description' => '', 'GroupName' => '赤峰烟草', 'Address' => '', 'IdLevel' => '/1/3/', 'GroupId' => 3, 'TelNo' => '', 'GroupFatherName' => 'Center', 'Industry' => 6),
        array('LimitNum' => null, 'ParentId' => 1, 'GroupType' => 1, 'Description' => '', 'GroupName' => 'Test1', 'Address' => '123', 'IdLevel' => '/1/4/', 'GroupId' => 4, 'TelNo' => '', 'GroupFatherName' => 'Center', 'Industry' => 0),
        array('LimitNum' => null, 'ParentId' => 3, 'GroupType' => 2, 'Description' => '', 'GroupName' => '烟草1', 'Address' => '', 'IdLevel' => '/1/3/8/', 'GroupId' => 8, 'TelNo' => '', 'GroupFatherName' => '赤峰烟草', 'Industry' => 0),
        array('LimitNum' => null, 'ParentId' => 4, 'GroupType' => 2, 'Description' => '', 'GroupName' => '烟草11', 'Address' => '', 'IdLevel' => '/1/4/9/', 'GroupId' => 9, 'TelNo' => '', 'GroupFatherName' => 'Test1', 'Industry' => 0),
        array('LimitNum' => null, 'ParentId' => 4, 'GroupType' => 1, 'Description' => '', 'GroupName' => 'test11', 'Address' => '', 'IdLevel' => '/1/4/10/', 'GroupId' => 10, 'TelNo' => '', 'GroupFatherName' => 'Test1', 'Industry' => 0),
        array('LimitNum' => null, 'ParentId' => 10, 'GroupType' => 1, 'Description' => '', 'GroupName' => 'test121', 'Address' => '', 'IdLevel' => '/1/4/10/11/', 'GroupId' => 11, 'TelNo' => '', 'GroupFatherName' => 'test11', 'Industry' => 0)
    );

    function formatarray($array)
    {
        $soucearray = array();
        for ($i = 0; $i < count($array); $i++) {
            $temp = array();
            $temp[id] = $array[$i][GroupId];
            $temp[text] = $array[$i][GroupName];
            $temp[ParentId] = $array[$i][ParentId];
            $temp[state] = "closed";
            $temp[children] = array();
            $temp[attributes][GroupType] = $array[$i][GroupType];;
            $temp[attributes][IdLevel] = $array[$i][IdLevel];;
            $temp[iconCls] = "icon-folder";
            array_push($soucearray, $temp);
        }
        return $soucearray;
    }


    function getTree($data, $pid, $key, $pKey, $childKey, $maxDepth = 0)
    {
        static $depth = 0;
        $depth++;
        if (intval($maxDepth) <= 0) {
            $maxDepth = count($data) * count($data);
        }
        if ($depth > $maxDepth) {
            exit("error recursion:max recursion depth {$maxDepth}");
        }
        $tree = array();
        foreach ($data as $rk => $rv) {
            if ($rv[$pKey] == $pid) {
                $rv[$childKey] = $this->getTree($data, $rv[$key], $key, $pKey, $childKey, $maxDepth);
                $tree[] = $rv;

            }

        }
        return $tree;
    }

    /**
     * 数组根据父id生成树
     * @staticvar int $depth 递归深度
     * @param array $data 数组数据
     * @param integer $pid 父id的值
     * @param string $key id在$data数组中的键值
     * @param string $chrildKey 要生成的子的键值
     * @param string $pKey 父id在$data数组中的键值
     * @param int $maxDepth 最大递归深度，防止无限递归
     * @return array 重组后的数组
     */

    function test1($data)
    {
        $sourcarray = $this->formatarray($data);
        //dump($sourcarray);
        $tree = $this->getTree($sourcarray, 0, 'id', 'ParentId', 'children');
        //dump($tree[children][1]);
        return $tree;
    }


    function t()
    {

//        $result = $this->getTree($this->rows, 0, 'id', 'parentId');
        $result = $this->test1($this->test);
        //dump($result[0][child]);
//        echo "<pre>";
//        print_r($result);
        echo json_encode($result);
    }
}