<?php
require PATH_LIB . 'Dbi.php';

if (empty($_GET['device_id'])) {
    echo json_encode(['code' => '1', 'message' => MESSAGE_REQUIRED .'device_id']);
    exit;
}

$deviceId = $_GET['device_id'];
$result = array();
$patient = Dbi::getDbi()->getPatientByDevice($deviceId);
if (VALUE_DB_ERROR === $patient) {
    $result['code'] = 1;
    $result['message'] = MESSAGE_DB_ERROR;
} elseif (empty($patient)) {
    $result['code'] = 2;
    $result['message'] = MESSAGE_DB_NO_DATA;
} else {
    $file = PATH_CACHE_CMD . $patient['guardian_id'] . '.php';
    if (file_exists($file)) {
        include $file;
        if (isset($info) && !empty($info) && !empty($info['end_time'])) {
            $result['code'] = 0;
            $result['patient_id'] = $patient['guardian_id'];
            $result['name'] = $patient['patient_name'];
            $result['mode'] = $patient['mode'];
            
            $result['seconds_left'] = $info['end_time'] - time();
        } else {
            $result['code'] = 2;
            $result['message'] = '剩余时间信息不存在。';
        }
    } else {
        $result['code'] = 2;
        $result['message'] = '剩余时间信息不存在。';
    }
}
echo json_encode($result);
