<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}
if (false === Validate::checkRequired($_POST['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}

//$caseId = isset($_POST['case_id']) ? $_POST['case_id'] : '';

$id = DbiEcgn::getDbi()->addHospital($_POST['name'], $_POST['tel']);
if (VALUE_DB_ERROR === $id) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
