<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';

if (!isset($_GET['hospital']) || !isset($_GET['consultation']) || !isset($_GET['guardian'])) {
    echo "<script language=javascript>alert(\"参数不足。\");history.back();</script>";
    exit;
}
$hospitalId = $_GET['hospital'];
$consultationId = $_GET['consultation'];
$guardianId = $_GET['guardian'];

$ret = Dbi::getDbi()->handleConsultation($hospitalId, $consultationId, $guardianId);
if (VALUE_DB_ERROR === $ret) {
    echo "<script language=javascript>alert(\"操作数据库失败，请重试或联系管理员。\");history.back();</script>";
    exit;
}
$patientName = Dbi::getDbi()->getPatientNameByGuardian($guardianId);
echo $patientName . '已添加到监护列表，稍后请从监护列表查看';
