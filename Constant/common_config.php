<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/9/28
 * Time: 10:52
 */
define('APP_NAME','CourtGms');
define('RUNTIME_PATH','./Runtime/');
define('EXPORT_PATH','./Export/'); //add by li 2016年09月28日11:14:13
define('UPLOAD_PATH','./Uploads/'); //add by li 2016年09月29日10:14:13
//define('WEB_SITE','http://127.0.0.1/ThinkGms/');
define('C_SERVER_ROOT','http://'.$_SERVER["HTTP_HOST"]);
define('C_WEB_ROOT','http://'.$_SERVER["HTTP_HOST"]."/".APP_NAME);

//配置默认数据库
define("DB_CONFIG_NAME_GMS","DB_GMS");
define('C_DB_HOST','localhost');
define('C_DB_GMS','TobaccoGms1');
define('C_DB_GMS_USER','root');
define('C_DB_GMS_PWD','tianxunceshi');



