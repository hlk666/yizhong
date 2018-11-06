<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}
if (false === Validate::checkRequired($_POST['password'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'password.']);
}
if (false === Validate::checkRequired($_POST['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}
if (false === Validate::checkRequired($_POST['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}
if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}
if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['department_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'department_id.']);
}


$id = DbiEcgn::getDbi()->addDoctor($_POST['user'], md5($_POST['password']), 
        $_POST['name'], $_POST['tel'], $_POST['type'], $_POST['hospital_id'], $_POST['department_id']);
if (VALUE_DB_ERROR === $id) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
