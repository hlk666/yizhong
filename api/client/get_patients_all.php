<?php
require_once PATH_LIB . 'Dbi.php';

$hospitalId = 0;
$mode = isset($_GET['mode']) ? $_GET['mode'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$rows = isset($_GET['rows']) ? $_GET['rows'] : 10000;
$patientName = isset($_GET['patient_name']) ? $_GET['patient_name'] : null;
$tel = isset($_GET['tel']) ? $_GET['tel'] : null;
$sTime = isset($_GET['start_time']) ? $_GET['start_time'] : null;
$eTime = isset($_GET['end_time']) ? $_GET['end_time'] : null;
$device = isset($_GET['device_id']) ? $_GET['device_id'] : null;
$registHospitalId = isset($_GET['regist_hospital']) ? $_GET['regist_hospital'] : null;
$doctorName = isset($_GET['doctor_name']) ? $_GET['doctor_name'] : null;
$offset = $page * $rows;

$ret = Dbi::getDbi()->getGuardians($hospitalId, $offset, $rows, $mode, $status, 
        $patientName, $tel, $sTime, $eTime, $device, $registHospitalId, $doctorName);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    
    foreach ($ret as $key => $row) {
        $ret[$key]['age'] = date('Y') - $row['birth_year'];
        $ret[$key]['sex'] = $row['sex'] == 1 ? '男' : '女';
        unset($ret[$key]['birth_year']);
    }
    $result['patients'] = $ret;
    api_exit($result);
}
