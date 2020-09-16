<?php
require_once PATH . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

/*
if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['agency_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'agency_id.']);
}*/
if (false === Validate::checkRequired($_POST['user_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user_id.']);
}
if (false === Validate::checkRequired($_POST['record_text'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'record_text.']);
}

$agencyId = isset($_POST['agency_id']) && !empty($_POST['agency_id']) ? $_POST['agency_id'] : '0';
$hospitalId = isset($_POST['hospital_id']) && !empty($_POST['hospital_id']) ? $_POST['hospital_id'] : '0';

$ret = DbiSale::getDbi()->addRecord($_POST['user_id'], $_POST['record_text'], $hospitalId, $agencyId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
