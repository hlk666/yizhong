<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['doctor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id.']);
}

$id = $_POST['doctor_id'];
$ret = DbiEcgn::getDbi()->existedDoctor($id);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (false === $ret) {
    api_exit(['code' => '18', 'message' => '对象不存在。']);
}

$user = isset($_POST['user']) ? $_POST['user'] : null;
$password = isset($_POST['password']) ? md5($_POST['password']) : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$tel = isset($_POST['tel']) ? $_POST['tel'] : null;
$type = isset($_POST['type']) ? $_POST['type'] : null;
$hospitalId = isset($_POST['hospital_id']) ? $_POST['hospital_id'] : null;
$departmentId = isset($_POST['department_id']) ? $_POST['department_id'] : null;

$data = array();
if (null !== $user) {
    $data['login_name'] = $user;
}
if (null !== $password) {
    $data['password'] = $password;
}
if (null !== $name) {
    $data['real_name'] = $name;
}
if (null !== $tel) {
    $data['tel'] = $tel;
}
if (null !== $type) {
    $data['type'] = $type;
}
if (null !== $hospitalId) {
    $data['hospital_id'] = $hospitalId;
}
if (null !== $departmentId) {
    $data['department_id'] = $departmentId;
}

if (empty($data)) {
    api_exit(['code' => '1', 'message' => '没有修改任何信息。']);
}

$ret = DbiEcgn::getDbi()->editDoctor($id, $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
