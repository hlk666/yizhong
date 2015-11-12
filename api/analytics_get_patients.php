<?php
require '../config/path.php';
require PATH_CONFIG . 'value.php';
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

$sql = 'select guardian_id as patient_id, start_time, end_time, patient_name as name, birth_year, sex, tel, reported
        from guardian as g inner join patient as p on g.patient_id = p.patient_id
        where regist_hospital_id = ' . $hospitalId;

if (isset($_GET['reported']) && trim($_GET['reported']) != '') {
    $sql .= ' and reported = "' . $_GET['reported'] . '"';
}
//start_time is the condition from query while 'end_time' is field name.
if (isset($_GET['start_time']) && trim($_GET['start_time']) != '') {
    $sql .= ' and end_time >= "' . $_GET['start_time'] . '"';
}

if (isset($_GET['end_time']) && trim($_GET['end_time']) != '') {
    $sql .= ' and end_time <= "' . $_GET['end_time'] . '"';
}
$data = Dbi::getDbi()->getAllData($sql);
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
