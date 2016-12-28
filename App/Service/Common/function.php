<?php

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}


/**
 * 数组转xls格式的excel文件
 * @param  array  $data      需要生成excel文件的数组
 * @param  string $filename  生成的excel文件名
 * auth  bjy
 *      示例数据：
$data = array(
array(NULL, 2010, 2011, 2012),
array('Q1',   12,   15,   21),
array('Q2',   56,   73,   86),
array('Q3',   52,   61,   69),
array('Q4',   30,   32,    0),
);
 */
function create_xls($data,$filename='simple.xls'){
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename=str_replace('.xls', '', $filename).'.xls';
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
        ->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
    $phpexcel->getActiveSheet()->fromArray($data,null,'A1');
    $phpexcel->getActiveSheet()->setTitle('Sheet1');
    $phpexcel->setActiveSheetIndex(0);
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/download");
//    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=$filename");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $filePath = './Export/';
    $objwriter->save($filePath.$filename);
}



function create_table_xls($type,$data,$titlename='报表',$startTime="1",$endTime="1",$filename='simple.xls'){
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename=str_replace('.xls', '', $filename).'.xls';
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
        ->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");


    $length = count($data[0]);

    $ColumnLength = ord("".$length);

    $ColumnLength = $ColumnLength + 16;


    $ColumnLength = chr($ColumnLength);

    $lineNumber = count($data) + 6;


    //填写 报表 抬头


    $phpexcel->getActiveSheet()->mergeCells('A1:'.$ColumnLength.'3');

    $phpexcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
    $phpexcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);


    $phpexcel->getActiveSheet()->setCellValue('A1', $titlename);

    $phpexcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //垂直居中
    $phpexcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



    //填写 报表日期

    $phpexcel->getActiveSheet()->mergeCells('A5:'.$ColumnLength.'5');

    $time = $startTime." 至 ".$endTime;

    $phpexcel->getActiveSheet()->setCellValue('A5', $time);

    $phpexcel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


    if('usual' == $type){




        //批量设置 1- n 列 为 文字居中显示
        for($i = 1; $i < $length;$i++){

            $Column = ord("".$i);

            $Column = $Column + 16;

            $Column = chr($Column);

            $phpexcel->getActiveSheet()->getStyle($Column.'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpexcel->getActiveSheet()->duplicateStyle( $phpexcel->getActiveSheet()->getStyle($Column.'6'), $Column.'7:'.$Column.$lineNumber );

        }



        //设置  行7 到 最后一行 列的格式化

        $phpexcel->getActiveSheet()->getStyle($ColumnLength.'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $phpexcel->getActiveSheet()->getStyle($ColumnLength.'7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_RMB_SIMPLE);
        $phpexcel->getActiveSheet()->duplicateStyle( $phpexcel->getActiveSheet()->getStyle($ColumnLength.'7'), $ColumnLength.'8:'.$ColumnLength.$lineNumber );


    }
    else if('list' == $type ){

        //批量设置 1- n 列 为 文字居中显示
        for($i = 1; $i <= $length;$i++){

            $Column = ord("".$i);

            $Column = $Column + 16;

            $Column = chr($Column);

            $phpexcel->getActiveSheet()->getStyle($Column.'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpexcel->getActiveSheet()->duplicateStyle( $phpexcel->getActiveSheet()->getStyle($Column.'6'), $Column.'7:'.$Column.$lineNumber );

        }

    }
    else{


        for ($i = 1; $i <= 2;$i++){

            $Column = ord("".$i);

            $Column = $Column + 16;

            $Column = chr($Column);

            $phpexcel->getActiveSheet()->getStyle($Column.'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpexcel->getActiveSheet()->duplicateStyle( $phpexcel->getActiveSheet()->getStyle($Column.'6'), $Column.'7:'.$Column.$lineNumber );
        }

        //批量设置 3-7列 为货币格式
        for($i = 3; $i < ($length + 1);$i++){


            $Column = ord("".$i);

            $Column = $Column + 16;

            $Column = chr($Column);

            $phpexcel->getActiveSheet()->getStyle($Column.'7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_RMB_SIMPLE);
            $phpexcel->getActiveSheet()->duplicateStyle( $phpexcel->getActiveSheet()->getStyle($Column.'7'), $Column.'8:'.$Column.$lineNumber );


            $phpexcel->getActiveSheet()->setCellValue($Column.$lineNumber, '=SUM('.$Column.'7:'.$Column.($lineNumber - 1).')');


        }



    }

    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框
            ),
        ),
    );

    $phpexcel->getActiveSheet()->fromArray($data,null,'A6'); //填充数据

    //标题行文字居中

    $phpexcel->getActiveSheet()->duplicateStyle( $phpexcel->getActiveSheet()->getStyle('A6'), 'A6:'.$ColumnLength.'6');

    $phpexcel->getActiveSheet()->getStyle('A6:'.$ColumnLength.($lineNumber-1))->applyFromArray($styleArray);


    //填写 合计 行

    $phpexcel->getActiveSheet()->setCellValue('A'.$lineNumber, '合计');

    $phpexcel->getActiveSheet()->setCellValue($ColumnLength.$lineNumber, '=SUM('.$ColumnLength.'7:'.$ColumnLength.($lineNumber - 1).')');


    $phpexcel->getActiveSheet()->setTitle('Sheet1');
    $phpexcel->setActiveSheetIndex(0);
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/download");
//    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=$filename");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
//    $objwriter->save('php://output');
    $filePath = './Export/';
//    $filePath = EXPORT_PATH;
    $objwriter->save($filePath.$filename);
//    exit;
}

/**
 * 导入excel文件
 * @param  string $file excel文件路径
 * @return array        excel文件内容数组
 * auth  bjy
 */
function import_excel($file){
    // 判断文件是什么格式
    $type = pathinfo($file);
    $type = strtolower($type["extension"]);
    $type=$type==='csv' ? $type : 'Excel5';
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    // 判断使用哪种格式
    $objReader = PHPExcel_IOFactory::createReader($type);
    $objPHPExcel = $objReader->load($file);
    $sheet = $objPHPExcel->getSheet(0);
    // 取得总行数
    $highestRow = $sheet->getHighestRow();
    // 取得总列数
    $highestColumn = $sheet->getHighestColumn();
    //循环读取excel文件,读取一条,插入一条
    $data=array();
    //从第一行开始读取数据
    for($j=1;$j<=$highestRow;$j++){
        //从A列读取数据
        for($k='A';$k<=$highestColumn;$k++){
            // 读取单元格
            $data[$j][]=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
        }
    }
    return $data;
}


function returnSuccess($msg,$data="")
{
    $result['Msg'] = $msg;
    $result['Result'] = true;
    $result['Data'] = $data;
    return $result;
}
function returnError($msg)
{
    $result['Msg'] = $msg;
    $result['Result'] = false;
    return $result;
}

function returnSearch($total,$rows=array())
{
    $result["total"]=$total;
    $result["rows"]=$rows;
    return $result;
}

function getCurrentGroupId()
{
    return session('UserInfo')['service_group_id'];
}



function parseParentIdLevel($level,$containSelf=true)
{
    $parentIds=explode("/",$level);
    $count=count($parentIds);
    if($count>=3)
    {
        unset($parentIds[0]);
        if (!$containSelf)//如果不包含自己，则清除
        {
            unset($parentIds[$count-2]);
        }
        unset($parentIds[$count-1]);
    }
    return $parentIds;
}

