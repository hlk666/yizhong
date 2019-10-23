<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

$xml = '<?xml version="1.0" encoding="GB2312"?>
<OriginalSummary>
<HospitalName>创建监控站的医院</HospitalName >
<Department>创建监控站的部门</Department>
<HospitalAreaName> 创 建 监 控 站 的 医 院 区 域 </HospitalAreaName>
<StudyInstanceUID>0fb310f4-a3c1-11e9-8573-54bf643dd2d2</StudyInstanceUID>
<!--如果盒子停止监控时间没有，则用当前时间-->
<StopMonitorTime>停止监控时间</StopMonitorTime>
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
} elseif (!Dbi::getDbi()->existedStudyToEnd($param['StudyInstanceUID'])) {
    $resultInfo = "不存在已开始的病历数据。";
} else {
    $resultInfo = '';
}
if (!empty($resultInfo)) {
    api_exit(['ResultCode' => '0', 
        'OutData' => ['StudyInstanceUID' => $param['StudyInstanceUID'], 'MonitorDuration' => '0', 'MonitorValidDuration' => '0'], 
        'ResultInfo' => $resultInfo]);
}

$ret = Dbi::getDbi()->stop($param['StudyInstanceUID'], $param['StopMonitorTime']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['ResultCode' => '0', 
        'OutData' => ['StudyInstanceUID' => $param['StudyInstanceUID'], 'MonitorDuration' => '0', 'MonitorValidDuration' => '0'],
        'ResultInfo' => MESSAGE_DB_ERROR]);
}

api_exit(['ResultCode' => '1', 
        'OutData' => ['StudyInstanceUID' => $param['StudyInstanceUID'], 'MonitorDuration' => '0', 'MonitorValidDuration' => '0'],
        'ResultInfo' => MESSAGE_SUCCESS]);
