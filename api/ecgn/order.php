<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['examination_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'examination_id.']);
}
if (false === Validate::checkRequired($_POST['order_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'order_time.']);
}


$ret = DbiEcgn::getDbi()->order($_POST['examination_id'], $_POST['order_time']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();

