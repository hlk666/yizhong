<?php
require_once '../config/path.php';
require_once PATH_LIB . 'Dbi.php';

$guardianId = $_GET['id'];
Dbi::getDbi()->endMedical($guardianId);
echo "<script>location.href='" . $_SERVER["HTTP_REFERER"] . "';</script>";
exit;
