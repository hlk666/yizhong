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

$guardiansDay = DbiAdmin::getDbi()->getGuardiansByRegistTime($startTime, $endTime, TEST_HOSPITALS);
if (VALUE_DB_ERROR === $guardiansDay) {
    Logger::write($logFile, 'try to get guardian data at' . date('Y-m-d H:i:s'));
    exit(2);
}

$ecgsDay = DbiAdmin::getDbi()->getEcgs($startTime, $endTime, TEST_HOSPITALS);
if (VALUE_DB_ERROR === $ecgsDay) {
    Logger::write($logFile, 'try to get ecg data at' . date('Y-m-d H:i:s'));
    exit(3);
}

$template .= '$guardiansDay = array();' . "\n";
foreach ($guardiansDay as $key => $guardianDay) {
    foreach ($guardianDay as $subKey => $subValue) {
        $template .= '$guardiansDay[' . $key . '][\'' . $subKey . '\'] = \'' . $subValue . "';\n";
    }
}
$template .= "\n";

$deviceTotal = DbiAdmin::getDbi()->getDeviceSum(TEST_HOSPITALS);
if (VALUE_DB_ERROR === $deviceTotal) {
    Logger::write($logFile, 'try to get device id at' . date('Y-m-d H:i:s'));
    exit(4);
}
$template .= '$deviceTotal = ' . $deviceTotal['total'] . ";\n";
$template .= '$deviceUsed = ' . count($guardiansDay) . ";\n";
$template .= "\n";

$template .= '$ecgsDay = array();' . "\n";
foreach ($ecgsDay as $key => $ecgDay) {
    foreach ($ecgDay as $subKey => $subValue) {
        $template .= '$ecgsDay[' . $key . '][\'' . $subKey . '\'] = \'' . $subValue . "';\n";
    }
}
$template .= "\n";

$handle = fopen($dataFile, 'w');
fwrite($handle, $template);
fclose($handle);

exit(0);
