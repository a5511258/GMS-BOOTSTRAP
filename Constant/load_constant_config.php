<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/9/28
 * Time: 10:37
 */

require_once __DIR__.DIRECTORY_SEPARATOR."/common_config.php";
if (defined('APP_ENV'))
{
    $config_file=__DIR__.DIRECTORY_SEPARATOR."/".APP_ENV.".php";
    require_once $config_file;
}