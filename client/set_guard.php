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
    $command = array('action' => 'start');
}
if ($status == 1) {
    $command = array('action' => 'end');
}

$invigilator = new Invigilator($guardianId);
$invigilator->create($command);

echo '<script language=javascript>history.back();</script>';
exit;