<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

$xml = '<?xml version="1.0" encoding="GB2312"?>
<OriginalSummary>
<MonitorData StationID="123" ECGrealtimemonitorbuffer=" 120秒" COUNT="10" 
MinimalECGdisplaytime="30" HospitalName="创建监控站的医院" Department="创建监控站的部门" 
HospitalAreaName="创建监控站的医院区域"> 
<!--病历检查号-->
<StudyInstanceUID>0fb310f4-a3c1-11e9-8573-54bf643dd2d2</StudyInstanceUID>
<RecordNo>记录器编号</RecordNo>
<!--如果盒子开始监控时间没有，则用当前时间-->
<StartMonitorTime>开始监控时间</StartMonitorTime> 
</MonitorData>
</OriginalSummary>';

if (false === Validate::checkRequired($_POST['InMessage'])) {
    api_exit(['ResultCode' => '0', 'ResultInfo' => MESSAGE_PARAM]);
}
$xml = $_POST['InMessage'];
$param = XML::getXml($xml);

if (empty($param) || !isset($param['MonitorData'])) {
    $resultInfo = MESSAGE_PARAM;
} elseif (false === Validate::checkRequired($param['MonitorData']['StudyInstanceUID'])) {
    $resultInfo = '参数不足：StudyInstanceUID.';
} elseif (!Dbi::getDbi()->existedStudyToStart($param['MonitorData']['StudyInstanceUID'])) {
    $resultInfo = "不存在未开始且已绑定的病历数据。";
} else {
    $resultInfo = '';
}
if (!empty($resultInfo)) {
    api_exit(['ResultCode' => '0', 'ResultInfo' => $resultInfo]);
}

$ret = Dbi::getDbi()->start($param['MonitorData']['StudyInstanceUID'], $param['MonitorData']['StartMonitorTime']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['ResultCode' => '0', 'ResultInfo' => MESSAGE_DB_ERROR]);
}

api_exit(['ResultCode' => '1', 'ResultInfo' => MESSAGE_SUCCESS]);
