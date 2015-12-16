<?php
error_reporting(E_ALL);
require '../common.php';
require PATH_LIB . 'Invigilator.php';
include_head('远程查房');

session_start();
checkDoctorLogin();

$guardianId = isset($_GET['id']) ? $_GET['id'] : null;
$data = array('check_info' => 'on');

$invigilator = new Invigilator($guardianId);
$ret = $invigilator->create($data);
if (VALUE_PARAM_ERROR === $ret) {
    user_back_after_delay(MESSAGE_PARAM, 1500);
} elseif (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR, 1500);
} elseif (VALUE_GT_ERROR === $ret) {
    user_back_after_delay(MESSAGE_GT_ERROR, 1500);
} else {
    user_back_after_delay('已经发送远程查房命令，请等待数据上传后查看(约1分钟)。', 1500);
}
?>
</html>