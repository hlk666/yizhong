<?php
date_default_timezone_set('asia/shanghai');

define('PATH_ROOT', 'D:\hp\www\yizhong\\');

define('PATH_LOG', PATH_ROOT . 'log\\');
define('PATH_LIB', PATH_ROOT . 'lib\\');
define('PATH_CONFIG', PATH_ROOT . 'config\\');
define('PATH_DATA', PATH_ROOT . 'data\\');

define('FTP_URL', 'ftp://192.168.0.1/');
define('FTP_USER', 'test');
define('FTP_PASSWORD', '123456');
define('LOCAL_FILE_PATH', 'D:\ECGDATA\\');

define('DB_SERVER', 'localhost');
define('DB_DB', 'tianbo');
define('DB_USER', 'yizhong');
define('DB_PASSWORD', '123456');

define('VALUE_DB_ERROR', -1);
define('VALUE_PARAM_ERROR', -2);

define('MESSAGE_DB_ERROR', '数据库操作失败，请重试或联系管理员。');
define('MESSAGE_SUCCESS', '操作成功.');
define('MESSAGE_DB_NO_DATA', '当前没有数据。');
define('MESSAGE_PARAM', '参数错误，请重试或联系管理员。');

define('STATUS_BIND', '0');
define('STATUS_START', '1');
define('STATUS_END', '2');
define('STATUS_UNBIND', '3');
define('STATUS_UPLOAD', '4');
define('STATUS_ANALYSIS', '5');
define('STATUS_REPORT', '6');
define('STATUS_BACK_OUT', '7');


