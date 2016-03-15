<?php
require 'common.php';

$logFile = 'day_sum.log';
$day = date('Y-m-d', strtotime('-1 day'));
$dataFile = date('Ymd', strtotime('-1 day'));
$startTime = $day . ' 00:00:00';
$endTime = $day . ' 23:59:59';

$guardiansDay = DbiAdmin::getDbi()->getGuardiansByRegistTime($startTime, $endTime, TEST_HOSPITALS);
if (VALUE_DB_ERROR === $guardiansDay) {
    Logger::writeBatch($logFile, 'Failed to get guardian data by regist time.');
    exit(1);
}

$deviceTotal = DbiAdmin::getDbi()->getDeviceSum(TEST_HOSPITALS);
if (VALUE_DB_ERROR === $deviceTotal) {
    Logger::writeBatch($logFile, 'Failed to get device total count.');
    exit(2);
}
$device = array();
$device['deviceTotal'] = $deviceTotal['total'];
$device['deviceUsed'] = count($guardiansDay);

$ecgsDay = DbiAdmin::getDbi()->getEcgs($startTime, $endTime, TEST_HOSPITALS);
if (VALUE_DB_ERROR === $ecgsDay) {
    Logger::writeBatch($logFile, 'Failed to get ecg data by regist time.');
    exit(3);
}

$retIO = DataFile::setDataFile('daysum', $dataFile,
        ['guardiansDay' => $guardiansDay], ['device' => $device], ['ecgsDay' => $ecgsDay]);
if (false === $retIO) {
    Logger::writeBatch($logFile, 'Failed to get device total count.');
    exit(4);
}

exit(0);
