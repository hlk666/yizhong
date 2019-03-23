<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'GeTui.php';
require_once PATH_LIB . 'Logger.php';

if (false === Validate::checkRequired($_POST['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}
if (false === Validate::checkRequired($_POST['text'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'text.']);
}

$file = PATH_CACHE_CLIENT . $_POST['device_id'] . '.php';
if (!file_exists($file)) {
    api_exit(['code' => '1', 'message' => '设备ID对应的配置文件不存在。重启设备可以解决该问题。']);
}

include $file;

$gt = GeTui::pushToSingle($clientId, $_POST['text']);
if (false === $gt) {
    api_exit(['code' => '3', 'message' => '推送失败。请确认设备联网。']);
}

api_exit_success();

