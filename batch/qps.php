<?php
require 'common.php';

$lastHour = strtotime('-1 hour');
$fileTime = isset($_GET['time']) ? $_GET['time'] : date('YmdH', $lastHour);
$file = dirname(PATH_ROOT) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $fileTime . '.access.log';
$logFile = 'qps.log';

if (!file_exists($file)) {
    //Logger::writeBatch($logFile, "file[$file] not exists." );
    
    $dataFile = DataFile::getDataFile('qps', date('Ym', strtotime('-1 hour')));
    if (false === $dataFile) {
        $requestMonth = array();
    } else {
        include $dataFile;
    }
    $requestMonth[$fileTime]['maxRequest'] = 0;
    $requestMonth[$fileTime]['maxRequestCount'] = 0;
    
    $retIO = DataFile::setDataFile('qps', date('Ym', strtotime('-1 hour')), ['requestMonth' => $requestMonth]);
    if (false === $retIO) {
        Logger::writeBatch($logFile, "failed to create file[$dataFile]." );
        exit(5);
    }
    exit(2);
}
$logTxt = file_get_contents($file);
if (empty($logTxt)) {
    Logger::writeBatch($logFile, "file[$file] is empty." );
    exit(3);
}
preg_match_all('/\[(.* \+0800)\]/', $logTxt, $out);
if (!isset($out[1]) || empty($out[1])) {
    Logger::writeBatch($logFile, "can not get data from file[$file]." );
    exit(4);
}

$time = array();
foreach ($out[1] as $value) {
    $time[] = strtotime($value);
}
$time = array_count_values($time);

$year = date('Y', $lastHour);
$month = date('m', $lastHour);
$day = date('d', $lastHour);
$hour = date('H', $lastHour);

$request = array();
$baseTime = mktime($hour, 0, 0, $month, $day, $year);
for ($i = 0; $i < 3600; $i++) {
    $second = $baseTime + $i;
    $request[$second] = 0;
}
foreach ($time as $key => $value) {
    $request[$key] = $value;
}

$dataFile = DataFile::getDataFile('qps', date('Ym', strtotime('-1 hour')));
if (false === $dataFile) {
    $requestMonth = array();
} else {
    include $dataFile;
}
$countRequest = array_count_values($request);
krsort($countRequest);
$maxRequest = key($countRequest);
$requestMonth[$fileTime]['maxRequest'] = $maxRequest;
$requestMonth[$fileTime]['maxRequestCount'] = $countRequest[$maxRequest];

$retIO = DataFile::setDataFile('qps', date('Ym', strtotime('-1 hour')), ['requestMonth' => $requestMonth]);
if (false === $retIO) {
    Logger::writeBatch($logFile, "failed to create file[$dataFile]." );
    exit(5);
}

$txtFile = PATH_DATA . 'qps' . DIRECTORY_SEPARATOR;
if (!file_exists($txtFile)) {
    mkdir($txtFile);
}
$txtFile .= $fileTime . '.txt';

$txtValue = "time,并发数\n";
foreach ($request as $key => $value) {
    $txtValue .= date('Y/m/d H:i:s', $key) . ",$value\n";
}
if (false === file_put_contents($txtFile, $txtValue)) {
    Logger::writeBatch($logFile, "failed to create file[$txtFile]." );
    exit(6);
}

echo 'ok';
exit(0);
