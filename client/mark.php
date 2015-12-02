<?php
require '../common.php';
if (!isset($_GET['id']) || !isset($_GET['type']) || !isset($_GET['check'])) {
    exit;
}
$id = $_GET['id'];
$type = $_GET['type'];
$mark = $_GET['check'];
if ($type == 'e') {
    Dbi::getDbi()->markEcg($id, $mark);
    file_put_contents('aaa.txt', $id . $mark);
}
if ($type == 'd') {
    Dbi::getDbi()->markDiagnosis($id, $mark);
}
