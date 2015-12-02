<?php
require '../common.php';
include_head('心电诊断');

$guardianId = $_GET["id"];
$ecgId = $_GET["eid"];
$content = $_GET["rx"];
$doctorName = $_GET["docNo"];

$doctorId = Dbi::getDbi()->getDoctorByName($doctorName);
if (VALUE_DB_ERROR === $doctorId) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if (empty($doctorId)) {
    user_goto('该医生尚未在系统中注册，请通过医院管理注册该医生信息。', GOTO_FLAG_EXIT);
}
$ret = Dbi::getDbi()->flowGuardianAddDiagnosis($ecgId, $doctorId, $content);
if (VALUE_DB_ERROR === $ret) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}

user_goto(MESSAGE_SUCCESS, GOTO_FLAG_URL, 'guardian_diagnosis.php?id=' . $guardianId);
?>
</html>