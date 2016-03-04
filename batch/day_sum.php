<?php
require '../config/config.php';
require_once PATH_LIB . 'Logger.php';
//require '../lib/function.php';
require '../lib/DbiAdmin.php';

$logFile = 'day_sum.log';
$exceptHospitals = array();
$day = date('Y-m-d', strtotime('-1 day'));
$dataFile = PATH_DATA . $day . '.php';
$startTime = $day . ' 00:00:00';
$endTime = $day . ' 23:59:59';

$guardianCountDay = 0;
$guardianCountDayRealtime = 0;
$guardianCountDayAbnormal = 0;
$guardianCountDayOnetime = 0;

$ecgCount = 0;
$ecgAlarmCount = 0;
$ecgRemoteCheckCount = 0;
$ecgRegularCount = 0;
$ecgSOSCount = 0;

$guardianCountAll = 0;
$guardianCountAllRealtime = 0;
$guardianCountAllAbnormal = 0;
$guardianCountAllOnetime = 0;

$consultationApplyCount = 0;
$consultationReplyCount = 0;

$deviceUsed = 0;
$deviceAll = 0;

$guardiansDay = DbiAdmin::getDbi()->getGuardiansByRegistTime($startTime, $endTime, $exceptHospitals);
if (VALUE_DB_ERROR === $guardiansDay) {
    Logger::write($logFile, 'DB error at' . date('Y-m-d H:i:s'));
    exit(2);
}
var_dump($guardiansDay);

//@todo 昨天开了多少单子，医院别，其中实时单子多少个，异常单子多少个，单次单子多少个
//异常报警的条数多少，远程查房多少，定时报警多少，SOS多少
//@todo 迄今为止多少单子，医院别，其中实时单子多少个，异常单子多少个，单次单子多少个
//上面两个有"点击查看详细"的入口按钮
//@todo 昨天发生了多少次会诊，多少回复了
//@todo 有多少设备正在被使用，使用率多少
//@todo 上面的都要去掉测试数据