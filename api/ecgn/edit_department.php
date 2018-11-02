<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['department_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'department_id.']);
}

$id = $_POST['department_id'];
$ret = DbiEcgn::getDbi()->existedDepartment($id);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (false === $ret) {
    api_exit(['code' => '18', 'message' => '对象不存在。']);
}

$name = isset($_POST['name']) ? $_POST['name'] : null;
$diagnosisDepartment = isset($_POST['diagnosis_department']) ? $_POST['diagnosis_department'] : null;
$manager = isset($_POST['manager']) ? $_POST['manager'] : '';

$data = array();
if (null !== $name) {
    $data['department_name'] = $name;
}
if (null !== $diagnosisDepartment) {
    $data['diagnosis_department_id'] = $diagnosisDepartment;
}
if (null !== $manager) {
    $data['manager_id'] = $manager;
}

if (empty($data)) {
    api_exit(['code' => '1', 'message' => '没有修改任何信息。']);
}

$ret = DbiEcgn::getDbi()->editDepartment($id, $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
