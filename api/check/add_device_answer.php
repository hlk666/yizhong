<?php
require PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}
if (false === Validate::checkRequired($_POST['question_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'question_id.']);
}
if (false === Validate::checkRequired($_POST['text'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'text.']);
}
if (false === Validate::checkRequired($_POST['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}
if (false === Validate::checkRequired($_POST['status'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'status.']);
}

$ret = DbiAdmin::getDbi()->addAnswer($_POST['device_id'], $_POST['question_id'], $_POST['text'], $_POST['user'], $_POST['status']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
