<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['problem_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'problem_id.']);
}
if (false === Validate::checkRequired($_POST['user_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user_id.']);
}
if (false === Validate::checkRequired($_POST['status'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'status.']);
}

$ret = DbiAdmin::getDbi()->updateProblem($_POST['problem_id'], $_POST['user_id'], $_POST['status']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
