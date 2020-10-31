<?php
require PATH_ROOT . 'lib/analysis/AnalysisUpload.php';
require_once PATH_ROOT . 'lib/DbiAnalytics.php';

$data = file_get_contents('php://input');

$upload = new AnalysisUpload();
$param = array_merge($_GET, $_POST);
$exec = $upload->run($param, $data, 'report');
/*
$guardianId = $param['patient_id'];
if (!empty($guardianId)) {
    $isQianyi = DbiAnalytics::getDbi()->isQianyi($guardianId);
    if ($isQianyi) {
        DbiAnalytics::getDbi()->saveQianyiData($guardianId);
    }
}
*/
echo $exec;
