<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$ret = DbiEcgn::getDbi()->existedPatient($_POST['patient_id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (false === $ret) {
    api_exit(['code' => '18', 'message' => '对象不存在。']);
}

$name = isset($_POST['name']) ? $_POST['name'] : null;
$sex = isset($_POST['sex']) ? $_POST['sex'] : null;
$birthYear = isset($_POST['birth_year']) ? $_POST['birth_year'] : null;
$tel = isset($_POST['tel']) ? $_POST['tel'] : null;

$data = array();
if (null !== $name) {
    $data['patient_name'] = $name;
}
if (null !== $sex) {
    $data['sex'] = $sex;
}
if (null !== $birthYear) {
    $data['birth_year'] = $birthYear;
}
if (null !== $tel) {
    $data['tel'] = $tel;
}

if (empty($data)) {
    api_exit(['code' => '1', 'message' => '没有修改任何信息。']);
}

$ret = DbiEcgn::getDbi()->editPatient($_POST['patient_id'], $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
