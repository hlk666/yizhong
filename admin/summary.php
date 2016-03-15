<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';
require '../lib/DataFile.php';

$title = '昨日信息统计';
require 'header.php';


$dataFile = DataFile::getDataFile('daysum', date('Ymd', strtotime('-1 day')));
if (false === $dataFile) {
    echo '夜间更新文件不存在，请联系系统管理员。';
    require 'tpl/footer.tpl';
    exit;
}
include $dataFile;

$guardianCountDay = 0;
$guardianCountDayRealtime = 0;
$guardianCountDayAbnormal = 0;
$guardianCountDayOnetime = 0;

$ecgCount = 0;
$ecgAlarmCount = 0;
$ecgRemoteCheckCount = 0;
$ecgRegularCount = 0;
$ecgSOSCount = 0;

$htmlGuardianDay = '';
foreach ($guardiansDay as $value) {
    $guardianCountDay++;
    if ('1' == $value['mode']) {
        $guardianCountDayRealtime++;
    } elseif ('2' == $value['mode']) {
        $guardianCountDayAbnormal++;
    } elseif ('3' == $value['mode']) {
        $guardianCountDayOnetime++;
    } else {
        //do nothing.
    }
    
    $modeText = '';
    if ($value['mode'] == '1') {
        $modeText = '实时模式';
    }
    if ($value['mode'] == '2') {
        $modeText = '异常模式';
    }
    if ($value['mode'] == '3') {
        $modeText = '单次模式';
    }
    
    $htmlGuardianDay .= '<tr><td>' . $value['regist_hospital_name'] 
        . '</td><td>' . $value['device_id']
        . '</td><td>' . $value['guardian_id']
        . '</td><td>' . $value['patient_name']
        . '</td><td>' . $modeText . '</td></tr>';
}
if (empty($guardiansDay)) {
    $htmlGuardianDay = '<tr><td colspan="5"><font color="red">无数据。</font></td></tr>';
}
$noticeGuardianDay = '<tr><td>' . $guardianCountDay
    . '</td><td>' . $guardianCountDayRealtime
    . '</td><td>' . $guardianCountDayAbnormal
    . '</td><td>' . $guardianCountDayOnetime . '</td></tr>';

$htmlEcgDay = '';
foreach ($ecgsDay as $value) {
    $ecgCount++;
    if ('0' == $value['alert_flag']) {
        $ecgRegularCount++;
    } elseif ('1' == $value['alert_flag']) {
        $ecgSOSCount++;
    } elseif ('3' == $value['alert_flag']) {
        $ecgRemoteCheckCount++;
    } else {
        $ecgAlarmCount++;
    }
}
$noticeEcgDay = '<tr><td>' . $ecgCount
    . '</td><td>' . $ecgAlarmCount
    . '</td><td>' . $ecgRegularCount
    . '</td><td>' . $ecgSOSCount
    . '</td><td>' . $ecgRemoteCheckCount . '</td></tr>';

$deviceUsedRate = ($device['deviceTotal'] == 0) ? 0 : ($device['deviceUsed'] / $device['deviceTotal']) * 100;
$deviceUsedRate = round($deviceUsedRate, 2);
$noticeDevice = '<tr><td>' . $device['deviceTotal']
    . '</td><td>' . $device['deviceUsed']
    . '</td><td>' . $deviceUsedRate . '%</td></tr>';
$hr = '<hr style="border-top:1px ridge #428bca;" />';
echo <<<EOF
<div style="background-color:#428bca;"><h3>期间范围数据(勿频繁查询):</h3></div>
<form class="form-horizontal" role="form" method="post" action="summary_condition.php">
<div class="row">
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <label for="start_time" class="control-label"><font color="red">*</font>开始日：</label>
    <input type="text" name="start_time" onclick="SelectDate(this,'yyyy-MM-dd')" />
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <label for="end_time" class="control-label"><font color="red">*</font>结束日：</label>
    <input type="text" name="end_time" onclick="SelectDate(this,'yyyy-MM-dd')" />
  </div>
  <div class="col-xs-12 col-sm-3">
    <button type="submit" class="btn btn-sm btn-info" name="query">日期范围内查询</button>
  </div>
</div>
</form>
<div style="background-color:#428bca;"><h3>昨日数据：</h3></div>
<table class="table table-striped">
  <thead>
      <tr>
        <th>监护数</th>
        <th>实时模式</th>
        <th>异常模式</th>
        <th>单次模式</th>
      </tr>
    </thead>
    <tbody>$noticeGuardianDay</tbody>
  </table>
$hr
  <table class="table table-striped">
    <thead>
      <tr>
        <th>开单医院</th>
        <th>设备ID</th>
        <th>监护ID</th>
        <th>病人姓名</th>
        <th>监护模式</th>
      </tr>
    </thead>
    <tbody>$htmlGuardianDay</tbody>
  </table>
$hr
  <table class="table table-striped">
    <thead>
      <tr>
        <th>报警总数</th>
        <th>异常报警</th>
        <th>定时报警</th>
        <th>SOS报警</th>
        <th>手动查房</th>
      </tr>
    </thead>
    <tbody>$noticeEcgDay</tbody>
  </table>
$hr
  <table class="table table-striped">
    <thead>
      <tr>
        <th>总设备数</th>
        <th>设备使用台次</th>
        <th>设备使用率</th>
      </tr>
    </thead>
    <tbody>$noticeDevice</tbody>
  </table>
EOF;
require 'tpl/footer.tpl';

//$paging = getPaging($page, $lastPage); <div style="text-align:right;"><ul class="pagination">$paging</ul><div>