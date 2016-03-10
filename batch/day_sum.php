<?php
require '../config/config.php';
require_once PATH_LIB . 'Logger.php';
//require '../lib/function.php';
require '../lib/DbiAdmin.php';

$logFile = 'day_sum.log';
$exceptHospitalList = '1,4,5';
$day = date('Y-m-d', strtotime('-1 day'));
$dataFile = PATH_DATA . date('Ymd', strtotime('-1 day')) . '.php';
$startTime = $day . ' 00:00:00';
$endTime = $day . ' 23:59:59';

$template = "<?php\n";

$guardiansDay = DbiAdmin::getDbi()->getGuardiansByRegistTime($startTime, $endTime, $exceptHospitalList);
if (VALUE_DB_ERROR === $guardiansDay) {
    Logger::write($logFile, 'try to get guardian data at' . date('Y-m-d H:i:s'));
    exit(2);
}

$ecgsDay = DbiAdmin::getDbi()->getEcgs($startTime, $endTime, $exceptHospitalList);
if (VALUE_DB_ERROR === $ecgsDay) {
    Logger::write($logFile, 'try to get ecg data at' . date('Y-m-d H:i:s'));
    exit(3);
}

$deviceList = array();
$template .= '$guardiansDay = array();' . "\n";
foreach ($guardiansDay as $key => $guardianDay) {
    foreach ($guardianDay as $subKey => $subValue) {
        $template .= '$guardiansDay[' . $key . '][\'' . $subKey . '\'] = \'' . $subValue . "';\n";
    }
    $deviceList[] = $guardianDay['device_id'];
}
$template .= "\n";

$deviceTotal = DbiAdmin::getDbi()->getDeviceSum($exceptHospitalList);
if (VALUE_DB_ERROR === $deviceTotal) {
    Logger::write($logFile, 'try to get device id at' . date('Y-m-d H:i:s'));
    exit(4);
}
$template .= '$deviceTotal = ' . $deviceTotal['total'] . ";\n";
$template .= '$deviceUsed = ' . count($deviceList) . ";\n";
$deviceList = array_unique($deviceList);
$template .= '$deviceUsedNotRepeated = ' . count($deviceList) . ";\n";
$template .= "\n";

$template .= '$ecgsDay = array();' . "\n";
foreach ($ecgsDay as $key => $ecgDay) {
    foreach ($ecgDay as $subKey => $subValue) {
        $template .= '$ecgsDay[' . $key . '][\'' . $subKey . '\'] = \'' . $subValue . "';\n";
    }
}
$template .= "\n";

$guardiansAll = DbiAdmin::getDbi()->getGuardiansByRegistTime(null, $endTime, $exceptHospitalList);
if (VALUE_DB_ERROR === $guardiansAll) {
    Logger::write($logFile, 'try to get all guardian data at' . date('Y-m-d H:i:s'));
    exit(5);
}
$guardianCountAll = 0;
$guardianCountAllRealtime = 0;
$guardianCountAllAbnormal = 0;
$guardianCountAllOnetime = 0;
foreach ($guardiansAll as $value) {
    $guardianCountAll++;
    if ('1' == $value['mode']) {
        $guardianCountAllRealtime++;
    } elseif ('2' == $value['mode']) {
        $guardianCountAllAbnormal++;
    } elseif ('3' == $value['mode']) {
        $guardianCountAllOnetime++;
    } else {
        //do nothing.
    }
}
$template .= '$guardianCountAll = ' . $guardianCountAll . ";\n";
$template .= '$guardianCountAllRealtime = ' . $guardianCountAllRealtime . ";\n";
$template .= '$guardianCountAllAbnormal = ' . $guardianCountAllAbnormal . ";\n";
$template .= '$guardianCountAllOnetime = ' . $guardianCountAllOnetime . ";\n";
$template .= "\n";

$handle = fopen($dataFile, 'w');
fwrite($handle, $template);
fclose($handle);

echo 'success.';
exit(0);
