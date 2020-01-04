<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['user_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user_id.']);
}
if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}
$userId = $_POST['user_id'];
$type = $_POST['type'];

$ret = DbiAdmin::getDbi()->addShift($userId, $type);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$file = PATH_DATA . 'shift.txt';
$userList = explode(';', file_get_contents($file));
$data = array();
foreach ($userList as $user) {
    if (empty($user)) {
        continue;
    }
    $tmp = explode(',', $user);
    if (isset($tmp[0]) && $tmp[0] != $userId) {
        $data[] = $user;
    }
}

if ($type == '1') {
    $ret = DbiAdmin::getDbi()->getAccountInfo($userId);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    $data[] = $userId . ',' . $ret['tel'] . ',' . date('Y-m-d H:i:s');
} elseif ($type == '2') {
    //do nothing.
} else {
    api_exit(['code' => '1', 'message' => 'type错误。']);
}
$dataString = implode(';', $data);
file_put_contents($file, $dataString);

api_exit_success();
