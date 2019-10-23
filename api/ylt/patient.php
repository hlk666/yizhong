<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['id'])) {
    exit_json('1', '参数不足：id.', array());
}

$ret = Dbi::getDbi()->getPatient($_GET['id']);
if (VALUE_DB_ERROR === $ret) {
    exit_json('2', MESSAGE_DB_ERROR, array());
}

if (empty($ret)) {
    exit_json('3', MESSAGE_DB_NO_DATA, $ret);
}

exit_json('0', MESSAGE_SUCCESS, $ret);

function exit_json($code, $message, array $data)
{
    $ret = $data;
    $ret['code'] = $code;
    $ret['message'] = $message;
    echo json_encode($ret);
    exit;
}
