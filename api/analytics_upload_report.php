<?php
require '../config/path.php';
require PATH_LIB . 'AnalyticsUpload.php';

$data = file_get_contents('php://input');

$upload = new AnalyticsUpload();
echo $upload->run($_GET, $data);
