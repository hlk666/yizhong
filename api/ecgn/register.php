<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['examination_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'examination_id.']);
}
if (false === Validate::checkRequired($_POST['exam_department_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'exam_department_id.']);
}
if (false === Validate::checkRequired($_POST['room_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'room_id.']);
}

$ret = DbiEcgn::getDbi()->register($_POST['examination_id'], $_POST['exam_department_id'], $_POST['room_id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

setRegistNotice($guardHospital, $mode);
api_exit_success();





