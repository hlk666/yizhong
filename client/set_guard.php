<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';
require PATH_LIB . 'Invigilator.php';

$guardianId = $_GET["id"];
$status = $_GET['status'];
session_start();
$hospitalId = $_SESSION['hospital'];

if ($status > 1) {
    echo '<script language=javascript>alert("监护状态错误！");history.back();</script>';
    exit;
}

$command = array();
if ($status == 0) {
    Dbi::getDbi()->startGuard($guardianId);
    $command = array('action' => 'start');
}
if ($status == 1) {
    Dbi::getDbi()->endGuard($guardianId);
    $command = array('action' => 'end');
}

$invigilator = new Invigilator($guardianId);
$invigilator->create($command);

header('myPatientss.php?id=' . $hospitalId);
exit;