<?php
require_once PATH_LIB . 'AppUploadData.php';

$patientId = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
$mode = isset($_GET['mode']) ? $_GET['mode'] : null;
$alert = isset($_GET['alert']) ? $_GET['alert'] : 0;
$time = isset($_GET['time']) ? date('YmdHis', $_GET['time']/1000) : date('YmdHis');
$data = file_get_contents('php://input');

$appUpload = new AppUploadData();
echo $appUpload->run($patientId, $mode, $alert, $time, $data);
