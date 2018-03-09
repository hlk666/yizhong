<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['county'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'county.']);
}
if (false === Validate::checkRequired($_POST['count'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'count.']);
}

$ret = DbiAdmin::getDbi()->addCountyHospital($_POST['county'], $_POST['count']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
