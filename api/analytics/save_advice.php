<?php
require PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

if (false === Validate::checkRequired($_POST['advice'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'advice.']);
}

$ret = DbiAnalytics::getDbi()->addAdvice($_POST['patient_id'], $_POST['advice']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
