<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'DataFile.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$guardianId = $_GET['patient_id'];
$dataFile = DataFile::getDataFile('alert_sum', $guardianId);
if (false === $dataFile) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    include $dataFile;
    
    $alertData = array();
    foreach ($alertSum as $type => $count) {
         $alertData[] = ['type' => $type, 'count' => $count];
    }
    
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['alert'] = $alertData;
    api_exit($result);
}
