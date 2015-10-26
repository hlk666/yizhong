<?php
require '../config/path.php';
require PATH_LIB . 'Dbi.php';

if (empty($_GET['hospital_id'])) {
    echo json_encode(['code' => '1', 'message' => 'hospital_id is empty.']);
    exit;
}

$hospitalId = $_GET['hospital_id'];

//@todo change to two tables
$sql = 'select patient_id, start_time, end_time, name, age, sex, tel, reported 
        from patient_history where hospital_id = "' . $hospitalId . '"';

if (isset($_GET['reported']) && trim($_GET['reported']) != '') {
    $sql .= ' and reported = "' . $_GET['reported'] . '"';
}

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