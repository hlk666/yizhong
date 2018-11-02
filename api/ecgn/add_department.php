<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}

$diagnosisDepartment = isset($_POST['diagnosis_department']) ? $_POST['diagnosis_department'] : '';
$manager = isset($_POST['manager']) ? $_POST['manager'] : '';

$id = DbiEcgn::getDbi()->addDepartment($_POST['name'], $diagnosisDepartment, $manager);
if (VALUE_DB_ERROR === $id) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
