<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '统计信息(不计内部测试数据)';
require 'header.php';

$dataFile = PATH_DATA . date('Ymd', strtotime('-1 day')) . '.php';
if (!file_exists($dataFile)) {
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
    $noticeGuardianDay = '<tr><td>' . $guardianCountDay
        . '</td><td>' . $guardianCountDayRealtime
        . '</td><td>' . $guardianCountDayAbnormal
        . '</td><td>' . $guardianCountDayOnetime . '</td></tr>';
    
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
        . '</td><td>' . $value['guard_hospital_name']
        . '</td><td>' . $value['device_id']
        . '</td><td>' . $value['guardian_id']
        . '</td><td>' . $value['patient_name']
        . '</td><td>' . $modeText . '</td></tr>';
}

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

$deviceUsedRate = ($deviceTotal == 0) ? 0 : ($deviceUsedNotRepeated / $deviceTotal) * 100;
$deviceUsedRate = round($deviceUsedRate, 2);
$noticeDevice = '总设备数：<font color="blue">' . $deviceTotal
    . '</font>。<br>使用设备数(包含重复使用)：<font color="blue">' . $deviceUsed
    . '</font>。<br>使用设备数(去掉重复使用)：<font color="blue">' . $deviceUsedNotRepeated
    . '</font>，<br>设备使用率(去重复后)：<font color="blue">' . $deviceUsedRate . '%</font>。';
$noticeDevice = '<tr><td>' . $deviceTotal
    . '</td><td>' . $deviceUsed
    . '</td><td>' . $deviceUsedNotRepeated
    . '</td><td>' . $deviceUsedRate . '%</td></tr>';
$htmlGuardianAll = '<tr><td>' . $guardianCountAll
    . '</td><td>' . $guardianCountAllRealtime
    . '</td><td>' . $guardianCountAllAbnormal
    . '</td><td>' . $guardianCountAllOnetime . '</td></tr>';
echo <<<EOF
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
<hr style="border-top:1px ridge red;" />
  <table class="table table-striped">
    <thead>
      <tr>
        <th>开单医院</th>
        <th>监护医院</th>
        <th>设备ID</th>
        <th>监护ID</th>
        <th>病人姓名</th>
        <th>监护模式</th>
      </tr>
    </thead>
    <tbody>$htmlGuardianDay</tbody>
  </table>
<hr style="border-top:1px ridge red;" />
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
<hr style="border-top:1px ridge red;" />
  <table class="table table-striped">
    <thead>
      <tr>
        <th>总设备数</th>
        <th>使用设备数(含重复)</th>
        <th>使用设备数(去重复)</th>
        <th>设备使用率(去重复)</th>
      </tr>
    </thead>
    <tbody>$noticeDevice</tbody>
  </table>
<div style="background-color:#428bca;"><h3>截止到昨日的合计数据：</h3></div>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>监护数</th>
        <th>实时模式</th>
        <th>异常模式</th>
        <th>单次模式</th>
      </tr>
    </thead>
    <tbody>$htmlGuardianAll</tbody>
  </table>
EOF;
require 'tpl/footer.tpl';

//$paging = getPaging($page, $lastPage); <div style="text-align:right;"><ul class="pagination">$paging</ul><div>