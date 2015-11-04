<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';

$consultationId = $_GET['id'];
Dbi::getDbi()->endConsultation($consultationId);

echo "<script>location.href='".$_SERVER["HTTP_REFERER"]."';</script>";