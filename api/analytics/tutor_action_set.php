<?php
require PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['tutor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tutor_id.']);
}
if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['url'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'url.']);
}


$ret = DbiAnalytics::getDbi()->setTutorAction($_POST['tutor_id'], $_POST['patient_id'], $_POST['url']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
