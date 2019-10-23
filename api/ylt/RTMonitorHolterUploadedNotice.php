<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

$xml = '<?xml version="1.0" encoding="GB2312"?>
<OriginalSummary>
<StudyInstanceUID>0fb310f4-a3c1-11e9-8573-54bf643dd2d2</StudyInstanceUID>
</OriginalSummary>';

if (false === Validate::checkRequired($_POST['InMessage'])) {
    api_exit(['ResultCode' => '0', 'ResultInfo' => MESSAGE_PARAM]);
}
$xml = $_POST['InMessage'];
$param = XML::getXml($xml);

if (empty($param)) {
    $resultInfo = MESSAGE_PARAM;
} elseif (false === Validate::checkRequired($param['StudyInstanceUID'])) {
    $resultInfo = '参数不足：StudyInstanceUID.';
} elseif (!Dbi::getDbi()->existedStudy($param['StudyInstanceUID'])) {
    $resultInfo = "不存在该病历数据。";
} else {
    $resultInfo = '';
}
if (!empty($resultInfo)) {
    api_exit(['ResultCode' => '0', 'ResultInfo' => $resultInfo]);
}

$ret = Dbi::getDbi()->upload($param['StudyInstanceUID']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['ResultCode' => '0', 'ResultInfo' => MESSAGE_DB_ERROR]);
}

api_exit(['ResultCode' => '1', 'ResultInfo' => MESSAGE_SUCCESS]);
