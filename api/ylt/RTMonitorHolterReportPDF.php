<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

$xml = '<?xml version="1.0" encoding="GB2312"?>
<OriginalSummary>
<StudyInstanceUID>ea1c22e4-cd20-11e9-9621-0242ac110002</StudyInstanceUID>
</OriginalSummary>';
/*
if (false === Validate::checkRequired($_POST['InMessage'])) {
    api_exit(['ResultCode' => '0', 'ResultInfo' => MESSAGE_PARAM]);
}
$xml = $_POST['InMessage'];
*/
$param = XML::getXml($xml);

if (empty($param)) {
    $resultInfo = MESSAGE_PARAM;
} elseif (false === Validate::checkRequired($param['StudyInstanceUID'])) {
    $resultInfo = '参数不足：StudyInstanceUID.';
} elseif (!Dbi::getDbi()->existedStudyReported($param['StudyInstanceUID'])) {
    $resultInfo = "该病历数据不存在或未审核完毕。";
} else {
    $resultInfo = '';
}
if (!empty($resultInfo)) {
    api_exit(['ResultCode' => '0', 'OutData' => '', 'ResultInfo' => $resultInfo]);
}

$ftpUrl = FTP_URL . $param['StudyInstanceUID'] . '/';

api_exit_download_pdf('1', $ftpUrl, FTP_USER, FTP_PASSWORD, 'report.pdf', MESSAGE_SUCCESS);
