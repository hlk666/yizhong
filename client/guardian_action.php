<?php
require '../common.php';
require PATH_LIB . 'Invigilator.php';
include_head('监护处理');

$guardianId = $_GET["id"];
$status = $_GET['status'];
session_start();
$hospitalId = $_SESSION['hospital'];

if ($status > 1) {
    followingAcction(MESSAGE_PARAM, GOTO_FLAG_BACK);
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

user_goto(null, GOTO_FLAG_BACK);
?>
</html>