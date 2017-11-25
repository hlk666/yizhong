<?php
define('ERROR_SUCCESS', 0);
define('ERROR_NO_DATA', 1);
define('ERROR_NOT_EXIST_ID', 2);
define('ERROR_DB', 3);
define('ERROR_DATA_CONSISTENCY', 4);
define('ERROR_SHORT_MESSAGE', 5);
define('ERROR_TEL_EMPTY', 6);
define('ERROR_VC', 7);

define('ERROR_PARAM_REQUIRED', 10);
define('ERROR_PARAM_NUMERIC', 11);
define('ERROR_PARAM_PHONE', 12);
define('ERROR_PARAM_RANGE', 13);
define('ERROR_PARAM_SPACE', 14);
define('ERROR_PARAM_TIME', 15);
define('ERROR_PARAM_FORMAT', 16);
define('ERROR_PARAM_SIZE', 17);

define('ERROR_USER_NOT_EXISTED', 20);
define('ERROR_PASSWORD', 21);
define('ERROR_USER_NAME_USED', 22);
define('ERROR_LOGIN_NO', 23);
define('ERROR_NO_PERMISSON', 24);
define('ERROR_LOGIN_TIMEOUT', 25);
define('ERROR_CREATE_SESSION', 26);
define('ERROR_USER_COUNT', 27);

define('ERROR_UPLOAD_NO_DATA', 30);
define('ERROR_UPLOAD_NAME', 31);
define('ERROR_UPLOAD_SUFFIX', 32);
define('ERROR_UPLOAD_SUCCESS', 33);
define('ERROR_UPLOAD_FAIL', 34);

define('ERROR_SEARCH', 40);
define('ERROR_NOT_IN_DEPARTMENT', 41);
define('ERROR_DATA_EXISTED', 42);
define('ERROR_TIME_ERROR', 43);
define('ERROR_DATA_DELETE_DENY', 44);
define('ERROR_FOLLOW_RECORD_NOTICE', 45);

define('ERROR_OTHER', 99);

class HpErrorMessage
{
    private static $error = [
                    ERROR_SUCCESS => ['code' => ERROR_SUCCESS, 'message' => '操作成功。'], 
                    ERROR_NO_DATA => ['code' => ERROR_NO_DATA, 'message' => '没有符合条件的数据。'],
                    ERROR_NOT_EXIST_ID => ['code' => ERROR_NOT_EXIST_ID, 'message' => '操作对象不存在，请确认输入是否正确。'],
                    ERROR_DB => ['code' => ERROR_DB, 'message' => '数据库操作发生错误，请联系管理员。'],
                    ERROR_DATA_CONSISTENCY => ['code' => ERROR_DATA_CONSISTENCY, 'message' => '数据一致性错误，请联系管理员。'],
                    ERROR_SHORT_MESSAGE => ['code' => ERROR_SHORT_MESSAGE, 'message' => '操作成功，但是发送短信失败。'],
                    ERROR_TEL_EMPTY => ['code' => ERROR_TEL_EMPTY, 'message' => '操作成功，手机号码空。'],
                    ERROR_VC => ['code' => ERROR_VC, 'message' => '验证码错误。'],
                    
                    ERROR_PARAM_REQUIRED => ['code' => ERROR_PARAM_REQUIRED, 'message' => '参数未提供：'],
                    ERROR_PARAM_NUMERIC => ['code' => ERROR_PARAM_NUMERIC, 'message' => '参数类型应该是数字：'],
                    ERROR_PARAM_PHONE => ['code' => ERROR_PARAM_PHONE, 'message' => '请输入正确的手机号码。'],
                    ERROR_PARAM_RANGE => ['code' => ERROR_PARAM_RANGE, 'message' => '参数值不在范围内：'],
                    ERROR_PARAM_SPACE => ['code' => ERROR_PARAM_SPACE, 'message' => '该参数不能为空白：'],
                    ERROR_PARAM_TIME => ['code' => ERROR_PARAM_TIME, 'message' => '时间格式错误。'],
                    ERROR_PARAM_FORMAT => ['code' => ERROR_PARAM_FORMAT, 'message' => '参数格式错误。'],
                    ERROR_PARAM_SIZE => ['code' => ERROR_PARAM_SIZE, 'message' => '参数大小错误。'],
                    
                    ERROR_USER_NOT_EXISTED => ['code' => ERROR_USER_NOT_EXISTED, 'message' => '用户不存在。'],
                    ERROR_PASSWORD => ['code' => ERROR_PASSWORD, 'message' => '密码错误。'],
                    ERROR_USER_NAME_USED => ['code' => ERROR_USER_NAME_USED, 'message' => '用户名已被他人使用。'],
                    ERROR_LOGIN_NO => ['code' => ERROR_LOGIN_NO, 'message' => '用户未登录。'],
                    ERROR_NO_PERMISSON => ['code' => ERROR_NO_PERMISSON, 'message' => '权限不足，请用更高权限用户登录。'],
                    ERROR_LOGIN_TIMEOUT => ['code' => ERROR_LOGIN_TIMEOUT, 'message' => '登录已经过期，请重新登录。'],
                    ERROR_CREATE_SESSION => ['code' => ERROR_CREATE_SESSION, 'message' => '登录成功，但是获取session失败。请重试或者联系管理员。'],
                    ERROR_USER_COUNT => ['code' => ERROR_USER_COUNT, 'message' => '符合条件的数据不唯一。'],
                    
                    ERROR_UPLOAD_NO_DATA => ['code' => ERROR_UPLOAD_NO_DATA, 'message' => '没有上传任何文件。'],
                    ERROR_UPLOAD_NAME => ['code' => ERROR_UPLOAD_NAME, 'message' => '实验室检查结果的类型错误。'],
                    ERROR_UPLOAD_SUFFIX => ['code' => ERROR_UPLOAD_SUFFIX, 'message' => '请上传以下类型文件：'],
                    ERROR_UPLOAD_SUCCESS => ['code' => ERROR_UPLOAD_SUCCESS, 'message' => '上传成功。'],
                    ERROR_UPLOAD_FAIL => ['code' => ERROR_UPLOAD_FAIL, 'message' => '上传失败，请重试或联系管理员。'],
                    
                    ERROR_SEARCH => ['code' => ERROR_SEARCH, 'message' => '搜索条件错误，请确认搜索关键字和值。'],
                    ERROR_NOT_IN_DEPARTMENT => ['code' => ERROR_NOT_IN_DEPARTMENT, 'message' => '该信息不属于本科室管理。'],
                    ERROR_DATA_EXISTED => ['code' => ERROR_DATA_EXISTED, 'message' => '该数据已存在，请勿重复操作。'],
                    ERROR_TIME_ERROR => ['code' => ERROR_TIME_ERROR, 'message' => '时间错误。'],
                    ERROR_DATA_DELETE_DENY => ['code' => ERROR_DATA_DELETE_DENY, 'message' => '该数据无法删除。'],
                    ERROR_FOLLOW_RECORD_NOTICE => ['code' => ERROR_FOLLOW_RECORD_NOTICE, 'message' => '随访记录不存在或者不是术后随访。'],
                    
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
    
    public static function getTelMessageApplyConsultationDoctor($applyHospital, $applyDoctor)
    {
        return "{$applyHospital}的{$applyDoctor}医生请求会诊，请确认。";
    }
    public static function getTelMessageApplyConsultationCase($applyHospital, $applyDoctor, $replyHospital)
    {
        return "{$applyHospital}的{$applyDoctor}医生已经将您的病历发给{$replyHospital}的医生，请求会诊。";
    }
    
    public static function getTelMessageReplyConsultationDoctor($replyHospital, $replyDoctor)
    {
        return "{$replyHospital}的{$replyDoctor}医生回复会诊，请确认。";
    }
    public static function getTelMessageReplyConsultationCase($replyHospital, $replyDoctor, $applyHospital)
    {
        return "{$replyHospital}的{$replyDoctor}医生已经为您会诊，并给出诊疗意见。请到{$applyHospital}查询。";
    }
    
    public static function getTelMessageApplyReferralDoctor($applyHospital, $applyDoctor)
    {
        return "{$applyHospital}的{$applyDoctor}医生请求转诊，请确认。";
    }
    public static function getTelMessageApplyReferralCase($applyHospital, $applyDoctor, $replyHospital)
    {
        return "{$applyHospital}的{$applyDoctor}医生已经向{$replyHospital}发出您的转诊申请。";
    }
    
    public static function getTelMessageReplyReferralDoctor($replyHospital, $replyDoctor)
    {
        return "{$replyHospital}的{$replyDoctor}医生回复转诊，请确认。";
    }
    public static function getTelMessageReplyReferralCase($replyHospital, $replyDoctor, $applyHospital)
    {
        return "{$replyHospital}的{$replyDoctor}医生已经回复转诊。请到{$applyHospital}查询。";
    }
    
    public static function getTelMessageConfirmDoctor($replyHospital, $replyDoctor, $caseName)
    {
        return "{$replyHospital}的{$replyDoctor}医生已经确认{$caseName}病人到院。";
    }
    public static function getTelMessageConfirmCase($replyHospital, $replyDoctor)
    {
        return "{$replyHospital}的{$replyDoctor}医生已经确认您到达医院。";
    }
    
    public static function getTelMessageDischargeDoctor($replyHospital, $replyDoctor, $caseName)
    {
        return "{$replyHospital}的{$replyDoctor}医生已经为{$caseName}病人办理出院，请从病历信息中查看具体信息。";
    }
    public static function getTelMessageDischargeCase($replyHospital, $replyDoctor, $applyHospital)
    {
        return "{$replyHospital}的{$replyDoctor}医生已经为您办理出院，并将复查计划发送给{$applyHospital}，请及时进行复查。";
    }
    
    public static function getTelMessagePlanDoctor($caseName, $followTime, $followText)
    {
        $date = substr($followTime, 0, 4) . '年' . substr($followTime, 5, 2) . '月' . substr($followTime, 8, 2) . '日';
        return "{$caseName}的复查时间({$date})快到了，复查内容是{$followText}。请及时联系患者。";
    }
    public static function getTelMessagePlanCase($followTime, $followHospital, $dischargeHospital)
    {
        $date = substr($followTime, 0, 4) . '年' . substr($followTime, 5, 2) . '月' . substr($followTime, 8, 2) . '日';
        return "您的复查时间({$date})到了，请您按时到{$followHospital}复查。结果会发送到{$dischargeHospital}，并得到诊疗建议。";
    }
    
    public static function getTelMessageFollowDoctor($followHospital, $followDoctor, $caseName)
    {
        return "{$followHospital}的{$followDoctor}医生为患者{$caseName}添加随访记录，请确认。";
    }
    public static function getTelMessageFollowCase($followHospital, $followDoctor, $dischargeHospital)
    {
        return "{$followHospital}的{$followDoctor}医生为您添加随访记录，{$dischargeHospital}的医生会进行确认并回复。";
    }
    
    public static function getTelMessageFollowReplyDoctor($replyHospital, $replyDoctor, $caseName)
    {
        return "{$replyHospital}的{$replyDoctor}医生回复{$caseName}的随访记录，请确认。";
    }
    public static function getTelMessageFollowReplyCase($replyHospital, $replyDoctor, $applyHospital)
    {
        return "{$replyHospital}的{$replyDoctor}医生回复您的随访记录，请到{$applyHospital}确认。";
    }
}