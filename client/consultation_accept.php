<?php
require '../common.php';
include_head('接受会诊');

if (!isset($_GET['hospital']) || !isset($_GET['consultation']) || !isset($_GET['guardian'])) {
    user_goto(MESSAGE_PARAM, GOTO_FLAG_BACK);
}
$hospitalId = $_GET['hospital'];
$consultationId = $_GET['consultation'];
$guardianId = $_GET['guardian'];

$ret = Dbi::getDbi()->flowConsultationAccept($hospitalId, $consultationId, $guardianId);
if (VALUE_DB_ERROR === $ret) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
}
$patientName = Dbi::getDbi()->getPatientNameByGuardian($guardianId);
if (VALUE_DB_ERROR === $patientName) {
    $patientName = '';
}
echo $patientName . '已添加到监护列表，稍后请从监护列表查看。';
?>
</html>