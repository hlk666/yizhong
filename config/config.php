<?php
date_default_timezone_set('asia/shanghai');

define('PATH_ROOT', 'D:\hp\www\yizhong\\');

define('PATH_LOG', PATH_ROOT . 'log\\');
define('PATH_LIB', PATH_ROOT . 'lib\\');
define('PATH_CONFIG', PATH_ROOT . 'config\\');
define('PATH_UPDATE', PATH_ROOT . 'update\\');
define('PATH_DATA', PATH_ROOT . 'data\\');
define('PATH_UPLOAD', PATH_ROOT . 'upload\\');
define('PATH_ECG', PATH_ROOT . 'ECG\\');
define('PATH_REAL_TIME', PATH_ROOT . 'RealTime\\');
define('PATH_CACHE_CMD', PATH_ROOT . 'cache\cmd\\');
define('PATH_CACHE_CMD_BK', PATH_ROOT . 'cache\cmd_bk\\');
define('PATH_CACHE_CLIENT', PATH_ROOT . 'cache\client\\');
define('PATH_CACHE_ECGONLINE', PATH_ROOT . 'cache\ecgonline\\');
define('PATH_CACHE_ECG_NOTICE', PATH_ROOT . 'cache\ecg_notice\\');
define('PATH_CACHE_REGIST_NOTICE', PATH_ROOT . 'cache\regist_notice\\');
define('PATH_CACHE_CONSULTATION_APPLY_NOTICE', PATH_ROOT . 'cache\consultation_apply\\');
define('PATH_CACHE_CONSULTATION_REPLY_NOTICE', PATH_ROOT . 'cache\consultation_reply\\');
define('PATH_REPORT', PATH_ROOT . 'report\\');
define('PATH_LONG_RANGE', PATH_ROOT . 'LongRange\\');
define('PATH_HBI', PATH_ROOT . 'hbi\\');
define('PATH_GETUI', PATH_ROOT . 'vendor\\getui\\');

define('SUFFIX_REAL_TIME_FILE', '_real_time.bin');

define('URL_ROOT', 'http://101.200.174.235/');
define('TEST_HOSPITALS', '1,4,5');

define('VALUE_DB_ERROR', -1);
define('VALUE_PARAM_ERROR', -2);
define('VALUE_GT_ERROR', -3);

define('VALUE_DEFAULT_ROWS', 100);

define('GOTO_FLAG_EXIT', 1);
define('GOTO_FLAG_BACK', 2);
define('GOTO_FLAG_URL', 3);

define('MESSAGE_GT_ERROR', '操作成功，但设备未开机。');
define('MESSAGE_DB_ERROR', '数据库操作失败，请重试或联系管理员。');
define('MESSAGE_SUCCESS', '操作成功。');
define('MESSAGE_DB_NO_DATA', '当前没有数据。');
define('MESSAGE_PARAM', '参数错误，请重试或联系管理员。');
define('MESSAGE_OTHER_ERROR', '系统内部错误。');
define('MESSAGE_NOT_EDIT', '没有修改任何信息，请不要提交。');
define('MESSAGE_REQUIRED', '请提供参数： ');
define('MESSAGE_FORMAT', '该参数格式错误： ');

define('PARAM_POLYCARDIA', 120);
define('PARAM_BRADYCARDIA', 50);
define('PARAM_LEAD', '5');
define('PARAM_MODE3_RECORD_TIME', '30');
define('PARAM_MODE2_RECORD_TIME', '30');
define('PARAM_REGULAR_TIME', '0');
define('PARAM_PREMATURE_BEAT', '8');
define('PARAM_COMBEATRHY', 'on');
define('PARAM_EXMINRATE', '30');
define('PARAM_TWAVE', 'on');
define('PARAM_STOPBEAT', '10');
define('PARAM_STHIGH', '15');
define('PARAM_STLOW', '15');


