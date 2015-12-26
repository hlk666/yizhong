<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
$hospitalId = isset($_GET['hospital_id']) ? $_GET['hospital_id'] : null;
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$rows = isset($_GET['rows']) ? $_GET['rows'] : 20;
$patientName = isset($_GET['patient_name']) ? $_GET['patient_name'] : null;
$tel = isset($_GET['tel']) ? $_GET['tel'] : null;
$sTime = isset($_GET['start_time']) ? $_GET['start_time'] : null;
$eTime = isset($_GET['end_time']) ? $_GET['end_time'] : null;
$offset = $page * $rows;

$ret = Dbi::getDbi()->getDuardians($hospitalId, $offset, $rows, $patientName, $tel, $sTime, $eTime);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '3', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = '';
    
    foreach ($ret as $key => $row) {
        $ret[$key]['age'] = date('Y') - $row['birth_year'];
        $ret[$key]['sex'] = $row['sex'] == 1 ? '男' : '女';
        unset($ret[$key]['birth_year']);
    }
    $result['patients'] = $ret;
    api_exit($result);
}
