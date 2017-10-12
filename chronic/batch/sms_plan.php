<?php
require 'common.php';

$logFile = 'sms_plan.log';
$day = date('Y-m-d', strtotime('+2 day'));
$startTime = $day . ' 00:00:00';
$endTime = $day . ' 23:59:59';

HpLogger::write('start.', $logFile, LOG_TIME_DAY);

$plans = DbiBatch::getDbi()->getPlans($startTime, $endTime);
if (VALUE_DB_ERROR === $plans) {
    HpLogger::write('db error.', $logFile, LOG_TIME_DAY);
    exit(-1);
}

foreach ($plans as $plan) {
    $date = substr($plan['plan_time'], 0, 4) . '年' . substr($plan['plan_time'], 5, 2) . '月' . substr($plan['plan_time'], 8, 2) . '日';
    $value = $plan['plan_value'];
    $message = "您的复查时间({$date})到了，请您按时复查(项目:$value)。";
    $sendCase = sendSMS($plan['tel'], $plan['name'], $message, $logFile);
    
    if (true == $sendCase) {
        $ret = DbiBatch::getDbi()->setMessageSend($plan['follow_plan_id']);
        if (VALUE_DB_ERROR === $ret) {
            HpLogger::write('db error-failed to update message_time.', $logFile, LOG_TIME_DAY);
        }
    }
}
HpLogger::write('ends', $logFile, LOG_TIME_DAY);
exit(0);

function sendSMS($tel, $destination, $content, $logFile)
{
    if (true !== HpValidate::checkPhoneNo($tel)) {
        HpLogger::write('tel number error with tel : ' . $tel, $logFile, LOG_TIME_DAY);
        return false;
    }
    HpLogger::write("send message to $destination($tel) with content[$content].", $logFile, LOG_TIME_DAY);
    $ret = HpShortMessageService::send($tel, $content);
    if (false === $ret) {
        HpLogger::write('failed.', $logFile, LOG_TIME_DAY);
        return false;
    }
    
    HpLogger::write('succeed.', $logFile, LOG_TIME_DAY);
    return true;
}
