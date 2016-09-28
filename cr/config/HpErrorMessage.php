<?php
define('ERROR_SUCCESS', 0);
define('ERROR_NO_DATA', 1);
define('ERROR_NOT_EXIST_ID', 2);
define('ERROR_DB', 3);
define('ERROR_DATA_CONSISTENCY', 4);
define('ERROR_PARAM_REQUIRED', 10);
define('ERROR_PARAM_NUMERIC', 11);
define('ERROR_PARAM_PHONE', 12);
define('ERROR_USER_TYPE', 13);
define('ERROR_USER_NOT_EXISTED', 20);
define('ERROR_PASSWORD', 21);
define('ERROR_USER_NAME_USED', 22);
define('ERROR_LOGIN_NO', 23);
define('ERROR_NO_PERMISSON', 24);
define('ERROR_LOGIN_TIMEOUT', 25);
define('ERROR_CREATE_SESSION', 26);
define('ERROR_OTHER', 99);

class HpErrorMessage
{
    private static $error = [
                    ERROR_SUCCESS => ['code' => ERROR_SUCCESS, 'message' => '操作成功。'], 
                    ERROR_NO_DATA => ['code' => ERROR_NO_DATA, 'message' => '没有符合条件的数据。'],
                    ERROR_NOT_EXIST_ID => ['code' => ERROR_NOT_EXIST_ID, 'message' => '操作对象不存在，请确认输入是否正确。'],
                    ERROR_DB => ['code' => ERROR_DB, 'message' => '数据库操作发生错误，请联系管理员。'],
                    ERROR_DATA_CONSISTENCY => ['code' => ERROR_DATA_CONSISTENCY, 'message' => '数据一致性错误，请联系管理员。'],
                    ERROR_PARAM_REQUIRED => ['code' => ERROR_PARAM_REQUIRED, 'message' => '参数未提供：'],
                    ERROR_PARAM_NUMERIC => ['code' => ERROR_PARAM_NUMERIC, 'message' => '参数类型应该是数字：'],
                    ERROR_PARAM_PHONE => ['code' => ERROR_PARAM_PHONE, 'message' => '请输入正确的手机号码。'],
                    ERROR_USER_TYPE => ['code' => ERROR_USER_TYPE, 'message' => '用户类型错误。'],
                    
                    ERROR_USER_NOT_EXISTED => ['code' => ERROR_USER_NOT_EXISTED, 'message' => '用户不存在。'],
                    ERROR_PASSWORD => ['code' => ERROR_PASSWORD, 'message' => '密码错误。'],
                    ERROR_USER_NAME_USED => ['code' => ERROR_USER_NAME_USED, 'message' => '用户名已被他人使用。'],
                    ERROR_LOGIN_NO => ['code' => ERROR_LOGIN_NO, 'message' => '用户未登录。'],
                    ERROR_NO_PERMISSON => ['code' => ERROR_NO_PERMISSON, 'message' => '权限不足，请用更高权限用户登录。'],
                    ERROR_LOGIN_TIMEOUT => ['code' => ERROR_LOGIN_TIMEOUT, 'message' => '登录已经过期，请重新登录。'],
                    ERROR_CREATE_SESSION => ['code' => ERROR_CREATE_SESSION, 'message' => '登录成功，但是获取session失败。请重试或者联系管理员。'],
                    
                    ERROR_OTHER => ['code' => ERROR_OTHER, 'message' => '未知错误，请联系管理员。'],
    ];
    
    public static function getError($errorName, $otherInfo = '')
    {
        if (array_key_exists($errorName, self::$error)) {
            if (!empty($otherInfo)) {
                self::$error[$errorName]['message'] = self::$error[$errorName]['message'] . $otherInfo;
            }
            return self::$error[$errorName];
        } else {
            HpLogger::writeCommonLog('Error type does not exist : ' . $errorName);
            return self::$error[ERROR_OTHER];
        }
    }
}