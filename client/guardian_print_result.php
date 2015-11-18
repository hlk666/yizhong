<?php
require '../common.php';
include_head('结束诊疗');

$guardianId = $_GET['id'];
$ret = Dbi::getDbi()->flowGuardianEndAll($guardianId);
if (VALUE_DB_ERROR === $ret) {
    user_error(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
}
user_goto(null, GOTO_FLAG_URL, $_SERVER["HTTP_REFERER"]);
?>
</html>