<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$startTime = isset($_POST['start_time']) ? $_POST['start_time'] : null;
$endTime = isset($_POST['end_time']) ? $_POST['end_time'] : null;
$days = date_diff(date_create($startTime), date_create($endTime))->days + 1;

if (empty($startTime) || empty($endTime)) {
        $title = '参数不足。';
        require 'header.php';
        user_back_after_delay('请同时输入查询开始日和结束日。');
}

$title = $startTime . ' 至 ' . $endTime . '为止的统计信息(不计内部测试数据)';

require 'header.php';

$startTime .= ' 00:00:00';
$endTime .= ' 23:59:59';

/* get all data from DB, if failed in any step, exit. */
$guardians = DbiAdmin::getDbi()->getGuardiansByRegistTime($startTime, $endTime, TEST_HOSPITALS);
if (VALUE_DB_ERROR === $guardians) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$ecgs = DbiAdmin::getDbi()->getEcgs($startTime, $endTime, TEST_HOSPITALS);
if (VALUE_DB_ERROR === $ecgs) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$deviceTotal = DbiAdmin::getDbi()->getDeviceSum(TEST_HOSPITALS);
if (VALUE_DB_ERROR === $deviceTotal) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

/* guardian and device information */
$guardianCount = 0;
$guardianCountRealtime = 0;
$guardianCountAbnormal = 0;
$guardianCountOnetime = 0;
foreach ($guardians as $key => $guardian) {
    $deviceList[] = $guardian['device_id'];
    $guardianCount++;
    if ('1' == $guardian['mode']) {
        $guardianCountRealtime++;
    } elseif ('2' == $guardian['mode']) {
        $guardianCountAbnormal++;
    } elseif ('3' == $guardian['mode']) {
        $guardianCountOnetime++;
    } else {
        //do nothing.
    }
}
$deviceTotal = $deviceTotal['total'];
$deviceUsed = count($guardians);
$deviceUsedRate = ($deviceTotal == 0) ? 0 : ($deviceUsed / ($deviceTotal * $days) * 100);
$deviceUsedRate = round($deviceUsedRate, 2);

/* ecg information */
$ecgCount = 0;
$ecgAlarmCount = 0;
$ecgRemoteCheckCount = 0;
$ecgRegularCount = 0;
$ecgSOSCount = 0;
foreach ($ecgs as $ecg) {
    $ecgCount++;
    if ('0' == $ecg['alert_flag']) {
        $ecgRegularCount++;
    } elseif ('1' == $ecg['alert_flag']) {
        $ecgSOSCount++;
    } elseif ('3' == $ecg['alert_flag']) {
        $ecgRemoteCheckCount++;
    } else {
        $ecgAlarmCount++;
    }
}

$noticeGuardian = '<tr><td>' . $guardianCount
    . '</td><td>' . $guardianCountRealtime
    . '</td><td>' . $guardianCountAbnormal
    . '</td><td>' . $guardianCountOnetime . '</td></tr>';
$noticeEcg = '<tr><td>' . $ecgCount
    . '</td><td>' . $ecgAlarmCount
    . '</td><td>' . $ecgRegularCount
    . '</td><td>' . $ecgSOSCount
    . '</td><td>' . $ecgRemoteCheckCount . '</td></tr>';
$noticeDevice = '<tr><td>' . $deviceTotal
    . '</td><td>' . $deviceUsed
    . '</td><td>' . $days
    . '</td><td>' . $deviceUsedRate . '%</td></tr>';
echo <<<EOF
<table class="table table-striped">
  <thead>
      <tr>
        <th>监护数</th>
        <th>实时模式</th>
        <th>异常模式</th>
        <th>单次模式</th>
      </tr>
    </thead>
    <tbody>$noticeGuardian</tbody>
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
    <tbody>$noticeEcg</tbody>
  </table>
<hr style="border-top:1px ridge red;" />
  <table class="table table-striped">
    <thead>
      <tr>
        <th>设备总数</th>
        <th>设备使用台次</th>
        <th>期间天数</th>
        <th>设备使用率</th>
      </tr>
    </thead>
    <tbody>$noticeDevice</tbody>
  </table>
EOF;
require 'tpl/footer.tpl';
