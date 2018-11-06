<?php
require_once PATH_LIB . 'db/DbiEcgn.php';

$user = isset($_POST['user']) ? $_POST['user'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$tel = isset($_POST['tel']) ? $_POST['tel'] : null;
$type = isset($_POST['type']) ? $_POST['type'] : null;
$hospitalId = isset($_POST['hospital_id']) ? $_POST['hospital_id'] : null;
$departmentId = isset($_POST['department_id']) ? $_POST['department_id'] : null;

$ret = DbiEcgn::getDbi()->getDoctor($user, $name, $tel, $type, $hospitalId, $departmentId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['list'] = $ret;
    api_exit($result);
}
