<?php
require '../config/path.php';
require PATH_LIB . 'Dbi.php';

Dbi::getDbi()->setEcgReadStatus($_GET['id']);
echo "<script>location.href='" . $_SERVER["HTTP_REFERER"] . "';</script>";
exit;
