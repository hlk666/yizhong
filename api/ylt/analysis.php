<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['id'])) {
    exit_json('1', '参数不足：id.');
}

$ret = Dbi::getDbi()->analysis($_POST['id']);
if (VALUE_DB_ERROR === $ret) {
    exit_json('2', MESSAGE_DB_ERROR);
}

exit_json('0', MESSAGE_SUCCESS);

function exit_json($code, $message)
{
    $ret = array('code' => $code, 'message' => $message);
    echo json_encode($ret);
    exit;
}
