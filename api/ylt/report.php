<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['id'])) {
    exit_json('1', '参数不足：id.');
}
if (false === Validate::checkRequired($_POST['doctor'])) {
    exit_json('1', '参数不足：doctor.');
}
if (false === Validate::checkRequired($_POST['zhenduan'])) {
    exit_json('1', '参数不足：zhenduan.');
}
if (false === Validate::checkRequired($_POST['jielun'])) {
    exit_json('1', '参数不足：jielun.');
}
/*
$data = file_get_contents('php://input');
if (empty($data)) {
    exit_json('1', '参数不足：pdf报告的二进制流。');
}
*/
$ret = Dbi::getDbi()->report($_POST['id'], $_POST['doctor'], $_POST['zhenduan'], $_POST['jielun']);
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
