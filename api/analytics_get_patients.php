<?php
require PATH_LIB . 'Dbi.php';

if (empty($_GET['hospital_id'])) {
    echo json_encode(['code' => '1', 'message' => MESSAGE_REQUIRED .'hospital_id']);
    exit;
}

$hospitalId = $_GET['hospital_id'];
if (!is_numeric($hospitalId)) {
    echo json_encode(['code' => '2', 'message' => MESSAGE_FORMAT . 'hospital_id']);
    exit;
}

$reported = null;
$startTime = null;
$endTime = null;
if (isset($_GET['reported']) && trim($_GET['reported']) != '') {
    $reported = $_GET['reported'];
}
if (isset($_GET['start_time']) && trim($_GET['start_time']) != '') {
    $startTime = $_GET['start_time'];
}
if (isset($_GET['end_time']) && trim($_GET['end_time']) != '') {
    $endTime = $_GET['end_time'];
}
$patients = Dbi::getDbi()->getPatientsForAnalytics($hospitalId, $reported, $startTime, $endTime);
if (VALUE_DB_ERROR === $patients) {
    echo json_encode(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
    exit;
}
foreach ($patients as $key => $row) {
    $patients[$key]['age'] = date('Y') - $row['birth_year'];
    $patients[$key]['sex'] = $row['sex'] == 1 ? '男' : '女';
    unset($patients[$key]['birth_year']);
}

$ret = array();
$ret['code'] = 0;
$ret['patients'] = $patients;
echo json_encode($ret);
