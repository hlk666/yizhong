<?php
require '../config/path.php';
require PATH_LIB . 'Dbi.php';

$guardianId = $_GET['id'];
Dbi::getDbi()->endThisMedical($guardianId);
echo "<script>location.href='" . $_SERVER["HTTP_REFERER"] . "';</script>";
exit;
