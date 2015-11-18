<?php
require '../config/config.php';

require PATH_LIB . 'Dbi.php';

if (empty($_GET['hospital_id'])) {
    echo json_encode(['code' => '1', 'message' => 'hospital_id is empty.']);
    exit;
}

$hospitalId = $_GET['hospital_id'];
if (!is_numeric($hospitalId)) {
    echo json_encode(['code' => '2', 'message' => 'hospital_id is not number.']);
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
$data = Dbi::getDbi()->getPatientsForAnalytics($hospitalId, $reported, $startTime, $endTime);
if (VALUE_DB_ERROR == $data) {
    if (!is_numeric($hospitalId)) {
        echo json_encode(['code' => '3', 'message' => 'db error.']);
        exit;
    }
}
foreach ($data as $key => $row) {
    $data[$key]['age'] = date('Y') - $row['birth_year'];
    $data[$key]['sex'] = $row['sex'] == 1 ? '男' : '女';
    unset($data[$key]['birth_year']);
}

$ret = array();
$ret['code'] = 0;
$ret['patients'] = $data;
echo json_encode($ret);
