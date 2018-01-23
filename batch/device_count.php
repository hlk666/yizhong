<?php
require 'common.php';
require_once PATH_LIB . 'db/DbiBatch.php';

$logFile = 'batch_device_count.log';
Logger::writeBatch($logFile, 'start.');

$tableFrom = 'ecg';

$deviceCount = DbiBatch::getDbi()->getDeviceCount();
if (VALUE_DB_ERROR === $deviceCount) {
    Logger::writeBatch($logFile, 'db error.');
    exit;
}

$file = PATH_DATA . 'device_count' . DIRECTORY_SEPARATOR . date('Ymd') . '.php';
if (file_exists($file)) {
    unlink($file);
}


$data = "<?php\n";
$data .= '$deviceInfo = array();' . "\n";
foreach ($deviceCount as $row) {
    $data .= '$deviceInfo[\'' . $row['name'] . '\'] = ' . $row['quantity'] . ";\n";
}
file_put_contents($file, $data);

Logger::writeBatch($logFile, 'succeed.');
exit;
