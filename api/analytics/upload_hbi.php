<?php
require PATH_LIB . 'AnalyticsUploadHbi.php';

$data = file_get_contents('php://input');

$upload = new AnalyticsUploadHbi();
echo $upload->run($_GET, $data);
