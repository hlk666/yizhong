<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['mode'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'mode.']);
}

$hospital = $_POST['hospital_id'];
$mode = $_POST['mode'];
$file = PATH_CONFIG . 'hospital_mode.txt';
if (!file_exists($file)) {
    api_exit(['code' => '6', 'message' => '配置信息错误，请联系管理员。']);
}

$oldArray = explode(',', file_get_contents($file));
if ($mode == 1) {
    $oldArray[] = $hospital;
}
if ($mode == 2) {
    foreach ($oldArray as $key => $item) {
        if ($item == $hospital) {
            unset($oldArray[$key]);
        }
    }
}

$newArray = array_unique($oldArray);
file_put_contents($file, implode(',', $newArray));

api_exit_success();
