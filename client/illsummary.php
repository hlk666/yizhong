<?php
header("Content-type: text/html; charset=utf-8");
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';

$guardianId = $_GET['id'];
$illResult = isset($_GET['rx']) ? $_GET['rx'] : '';
$status = Dbi::getDbi()->getGuardianStatusByGuardian($guardianId);
if ('1' == $status) {
    $notice = '该用户尚未结束监护，如需下诊断总结，请先从【用户管理】中将该用户结束监护。';
    echo '<p style="font-size:18pt;color:red;text-align:center">' . $notice . '<p>';
    exit; 
}
Dbi::getDbi()->createIllResult($guardianId, $illResult);
sleep(1);
echo "<script>location.href='illsum.php?id=$guardianId';</script>";
