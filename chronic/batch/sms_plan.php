<?php
require 'common.php';

$logFile = 'sms_plan.log';
$day = date('Y-m-d', strtotime('+1 day'));
$startTime = $day . ' 00:00:00';
$endTime = $day . ' 23:59:59';

HpLogger::writeCommonLog('starts', $logFile);

$plans = DbiBatch::getDbi()->getPlans($startTime, $endTime);
if (VALUE_DB_ERROR === $plans) {
    HpLogger::writeCommonLog('db error-failed to get plans.', $logFile);
    exit(-1);
}

foreach ($plans as $plan) {
    $textCase = HpErrorMessage::getTelMessagePlanCase($plan['follow_time'], $plan['child_hospital_name'], $plan['parent_hospital_name']);
    $sendCase = sendSMS($plan['tel'], $plan['case_name'], $textCase, $logFile);
    
    $telDoctor = DbiBatch::getDbi()->getTelList($plan['apply_hospital_id']);
    if (VALUE_DB_ERROR === $telDoctor) {
        HpLogger::writeCommonLog('db error-failed to get tel list.', $logFile);
        exit(-1);
    }
    
    $textDoctor = HpErrorMessage::getTelMessagePlanDoctor($plan['case_name'], $plan['follow_time'], $plan['follow_text']);
    $sendDoctor = false;
    foreach ($telDoctor as $row) {
        $send = sendSMS($row['tel'], $plan['child_hospital_name'] . '的医生', $textDoctor, $logFile);
        if ($send) {
            $sendDoctor = true;
        }
    }
    if (true == $sendCase && true == $sendDoctor) {
        $ret = DbiBatch::getDbi()->setMessageSend($plan['plan_id']);
        if (VALUE_DB_ERROR === $ret) {
            HpLogger::writeCommonLog('db error-failed to update message_time.', $logFile);
        }
    }
}
HpLogger::writeCommonLog('ends', $logFile);
exit(0);

function sendSMS($tel, $destination, $content, $logFile)
{
    if (true !== HpValidate::checkPhoneNo($tel)) {
        HpLogger::writeCommonLog('tel number error with tel : ' . $tel, $logFile);
        return false;
    }
    HpLogger::writeCommonLog("send message to $destination($tel) with content[$content].", $logFile);
    $ret = HpShortMessageService::send($tel, $content);
    if (false === $ret) {
        HpLogger::writeCommonLog('failed.', $logFile);
        return false;
    }
    
    HpLogger::writeCommonLog('succeed.', $logFile);
    return true;
}
