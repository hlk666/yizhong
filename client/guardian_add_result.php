<?php
require '../common.php';
include_head('诊断总结');

$guardianId = $_GET['id'];
$illResult = isset($_GET['rx']) ? $_GET['rx'] : '';
$ret = Dbi::getDbi()->getGuardianById($guardianId);
if (VALUE_DB_ERROR === $ret) {
    $message =  '<p style="font-size:18pt;color:red;text-align:center">读取数据失败，请重试。<p>';
    user_goto($message, GOTO_FLAG_EXIT);
}
if (empty($ret)) {
    $message =  '<p style="font-size:18pt;color:red;text-align:center">该监护没有数据。<p>';
    user_goto($message, GOTO_FLAG_EXIT);
}

if ($ret['status'] < 2) {
    $notice = '该用户尚未结束监护，如需下诊断总结，请先从【用户管理】中将该用户结束监护。';
    $message =  '<p style="font-size:18pt;color:red;text-align:center">' . $notice . '<p>';
    user_goto($message, GOTO_FLAG_EXIT);
}
$ret = Dbi::getDbi()->flowGuardianAddResult($guardianId, $illResult);
if (VALUE_DB_ERROR === $ret) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
sleep(1);
user_goto(null, GOTO_FLAG_URL, 'guardian_result.php?id=' .$guardianId);
?>
</html>