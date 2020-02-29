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

$guardians = DbiAdmin::getDbi()->getGuardiansOn();
if (VALUE_DB_ERROR === $guardians) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($guardians)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}
$path = PATH_DATA . 'guardian_on' . DIRECTORY_SEPARATOR;
$fileList = scandir($path);
foreach ($fileList as $f) {
    if ($f != '.' && $f != '..') {
        unlink($path . $f);
    }
}

$countPool = count($data);
Logger::write('shift.log', 'count of data is ' . $countPool);
$poolId = '0';
for ($i = 0; $i < $countPool; $i++) {
    $tmp = explode(',', $data[$i]);
    if (isset($tmp[0]) && !empty($tmp[0])) {
        $poolId = $tmp[0];
    } else {
        Logger::write('shift.log', 'format error.');
        api_exit(['code' => '1', 'message' => '格式错误。']);
    }
    $poolTxt = '';
    foreach ($guardians as $guardian) {
        if ($guardian['guardian_id'] % $countPool == $i) {
            $poolTxt .= $guardian['guardian_id'] . ',';
        }
    }
    $poolTxt = substr($poolTxt, 0, -1);
    file_put_contents($path . $poolId . '.txt', $poolTxt);
    Logger::write('shift.log', 'file: ' . $poolId . '.txt, content :' . $poolTxt);
}

api_exit_success();
