<?php
require 'E:\wamp\www\config\config.php';
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'function.php';
require_once PATH_LIB . 'DbiAdmin.php';

$logFile = 'day_sum.log';
$day = date('Y-m-d', strtotime('-1 day'));
$dataFile = PATH_DATA . date('Ymd', strtotime('-1 day')) . '.php';
$startTime = $day . ' 00:00:00';
$endTime = $day . ' 23:59:59';

$template = "<?php\n";

Logger::writeBatch($logFile, 'Start to get guardian data by regist time.');
$guardiansDay = DbiAdmin::getDbi()->getGuardiansByRegistTime($startTime, $endTime, TEST_HOSPITALS);
if (VALUE_DB_ERROR === $guardiansDay) {
    Logger::writeBatch($logFile, 'Failed to get guardian data by regist time.');
    exit(2);
}
Logger::writeBatch($logFile, 'Finish to get guardian data by regist time.');

Logger::writeBatch($logFile, 'Start to get ecg data by regist time.');
$ecgsDay = DbiAdmin::getDbi()->getEcgs($startTime, $endTime, TEST_HOSPITALS);
if (VALUE_DB_ERROR === $ecgsDay) {
    Logger::writeBatch($logFile, 'Failed to get ecg data by regist time.');
    exit(3);
}
Logger::writeBatch($logFile, 'Finish to get ecg data by regist time.');

Logger::writeBatch($logFile, 'Start to create guardian cache data.');
$template .= '$guardiansDay = array();' . "\n";
foreach ($guardiansDay as $key => $guardianDay) {
    foreach ($guardianDay as $subKey => $subValue) {
        $template .= '$guardiansDay[' . $key . '][\'' . $subKey . '\'] = \'' . $subValue . "';\n";
    }
}
$template .= "\n";
Logger::writeBatch($logFile, 'Finish to create guardian cache data.');

Logger::writeBatch($logFile, 'Start to get device total count.');
$deviceTotal = DbiAdmin::getDbi()->getDeviceSum(TEST_HOSPITALS);
if (VALUE_DB_ERROR === $deviceTotal) {
    Logger::writeBatch($logFile, 'Failed to get device total count.');
    exit(4);
}
$template .= '$deviceTotal = ' . $deviceTotal['total'] . ";\n";
$template .= '$deviceUsed = ' . count($guardiansDay) . ";\n";
$template .= "\n";
Logger::writeBatch($logFile, 'Finish to get device total count.');

Logger::writeBatch($logFile, 'Start to create ecg cache data.');
$template .= '$ecgsDay = array();' . "\n";
foreach ($ecgsDay as $key => $ecgDay) {
    foreach ($ecgDay as $subKey => $subValue) {
        $template .= '$ecgsDay[' . $key . '][\'' . $subKey . '\'] = \'' . $subValue . "';\n";
    }
}
$template .= "\n";
Logger::writeBatch($logFile, 'Finish to create ecg cache data.');

Logger::writeBatch($logFile, 'Start to create file.');
$handle = fopen($dataFile, 'w');
fwrite($handle, $template);
fclose($handle);
Logger::writeBatch($logFile, 'Finish to create file.');

exit(0);
