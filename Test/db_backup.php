<?php

/**
 * 数据库备份脚本.请注意用户名和密码
 */
$saveDir="/txkj/dbbackup";
$db=array(
    "CourtGms",
);

$cfg_dbhost="127.0.0.1";
$cfg_dbport=3306;
$cfg_dbuser="root";
$cfg_dbpwd="txcourt";
foreach ($db as $cfg_dbname) {
    // 设置SQL文件保存文件名
    $filename =       $cfg_dbname .date("Y-m-d_H-i-s"). ".sql";
    // 所保存的文件名
    // 获取当前页面文件路径，SQL文件就导出到此文件夹内
    $tmpFile = $saveDir. DIRECTORY_SEPARATOR . $filename;
    // 用MySQLDump命令导出数据库
//    $cmd="mysqldump -h$cfg_dbhost -P$cfg_dbport  -u$cfg_dbuser -p$cfg_dbpwd --default-character-set=utf8 $cfg_dbname > " . $tmpFile;
    $cmd="mysqldump -h$cfg_dbhost -P$cfg_dbport  -u$cfg_dbuser -p$cfg_dbpwd --default-character-set=utf8 $cfg_dbname > " .$tmpFile;
    echo($cmd);
    exec($cmd);
}
//$file = fopen($tmpFile, "r"); // 打开文件
//echo fread($file, filesize($tmpFile));
//fclose($file);
exit;
