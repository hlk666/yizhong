<?php
require '../config/path.php';
require PATH_LIB . 'Dbi.php';

if (empty($_GET['device_id'])) {
    echo json_encode(['code' => '1', 'message' => 'device_id is empty.']);
    exit;
}

$deviceId = $_GET['device_id'];

$result = array();
if ($deviceId == '1') {
    $result['code'] = 0;
    $result['patient_id'] = 123;
    $result['name'] = '张三';
    $result['mode'] = 1;
} elseif ($deviceId == '2') {
    $result['code'] = 0;
    $result['patient_id'] = 234;
    $result['name'] = '李四';
    $result['mode'] = 2;
} elseif ($deviceId == '3') {
    $result['code'] = 0;
    $result['patient_id'] = 345;
    $result['name'] = '王五';
    $result['mode'] = 3;
} else {
    $result['code'] = 1;
    $result['message'] = 'some error message';
}
echo json_encode($result);


//@todo change to two tables
$sql = 'select patient_id, start_time, end_time, name, age, sex, tel, reported
        from guardian_history where hospital_id = "' . $hospitalId . '"';

if (isset($_GET['reported']) && trim($_GET['reported']) != '') {
    $sql .= ' and reported = "' . $_GET['reported'] . '"';
}

//@todo if I need to change start_time to end_time here?
if (isset($_GET['start_time']) && trim($_GET['start_time']) != '') {
    $sql .= ' and start_time >= "' . $_GET['start_time'] . '"';
}

if (isset($_GET['end_time']) && trim($_GET['end_time']) != '') {
    $sql .= ' and end_time <= "' . $_GET['end_time'] . '"';
}

$data = array();
$data['code'] = 0;
$data['patients'] = Dbi::getDbi()->getAllData($sql);
echo json_encode($data);