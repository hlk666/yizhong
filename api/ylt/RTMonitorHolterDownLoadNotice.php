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
    api_exit(['ResultCode' => '0', 'OutData' => '', 'ResultInfo' => $resultInfo]);
}

if (!Dbi::getDbi()->existedStudyToDownload($param['StudyInstanceUID'])) {
    api_exit(['ResultCode' => '0', 'OutData' => '', 'ResultInfo' => '该病历数据未上传。']);
}

$dir = PATH_ROOT . 'data' . DIRECTORY_SEPARATOR . $param['StudyInstanceUID'] . DIRECTORY_SEPARATOR;
if (!file_exists($dir)) {
    api_exit(['ResultCode' => '0', 'OutData' => '', 'ResultInfo' => '下载数据一致性错误。']);
}
$tmp = scandir($dir);
$files = array();
foreach ($tmp as $file) {
    if ($file != '.' && $file != '..') {
        $files[] = $file;
    }
}

$ftpUrl = FTP_URL . $param['StudyInstanceUID'] . '/';
$localPath = LOCAL_FILE_PATH . $param['StudyInstanceUID'] . '\\';

api_exit_download('1', $localPath , $ftpUrl, FTP_USER, FTP_PASSWORD, $files, MESSAGE_SUCCESS);
