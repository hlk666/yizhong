<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '解除设备绑定';
require 'header.php';

$deviceId = isset($_GET['id']) ? $_GET['id'] : null;

if (empty($deviceId)) {
    user_back_after_delay('非法访问。');
}

$ret = DbiAdmin::getDbi()->delDevice($deviceId);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

user_back_after_delay('已删除。');

require 'tpl/footer.tpl';