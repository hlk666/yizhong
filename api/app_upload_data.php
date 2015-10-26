<?php
require '../config/path.php';
require_once PATH_LIB . 'AppUploadData.php';

$patientId = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
$mode = isset($_GET['mode']) ? $_GET['mode'] : null;
$alert = isset($_GET['alert']) ? $_GET['alert'] : null;
$data = file_get_contents('php://input');
echo strlen($data);exit;
$appUpload = new AppUploadData();
$ret = $appUpload->run($patientId, $mode, $alert, $data);

echo $ret;
