<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '医院设备开单';
require 'header.php';

$hospital = isset($_GET['hospital']) ? $_GET['hospital'] : '';
$startTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$endTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] . ' 23:59:59' : null;
$startTimeDisplay = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : '';
$endTimeDisplay = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] : '';

if (empty($startTimeDisplay)) {
    $days = 0;
} else {
    $tmpEndDay = empty($endTimeDisplay) ? date('Ymd') : $endTimeDisplay;
    $days = date_diff(date_create($startTimeDisplay), date_create($tmpEndDay))->days + 1;
}

$ret = DbiAdmin::getDbi()->getHospitalList();
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}
$htmlHospitals = '<option value="0">请选择医院</option>';
foreach ($ret as $value) {
    if ($hospital == $value['hospital_id']) {
        $htmlHospitals .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
    } else {
        $htmlHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
    }
}

$ret = DbiAdmin::getDbi()->getDeviceGuardianCount($hospital, $startTime, $endTime);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$countDevice = 0;
$countGuardian = 0;
if (empty($ret)) {
    $htmlData = '没有数据。';
} else {
    $htmlData = '<table class="table table-striped">
    <thead>
      <tr>
        <th>设备ID</th>
        <th>开单数(点击看明细)</th>
        <th>使用率</th>
      </tr>
    </thead>
    <tbody>';
    foreach ($ret as $value) {
        $countDevice++;
        $countGuardian += $value['quantity'];
        if ($value['quantity'] == 0) {
            $link = '0';
        } else {
            $link = '<a href="device_guardian.php?hospital=' . $hospital . '&device=' . $value['device_id'];
            if (!empty($startTime)) {
                $link .= '&start_time=' . $startTimeDisplay;
            }
            if (!empty($endTime)) {
                $link .= '&end_time=' . $endTimeDisplay;
            }
            $link .= '">' . $value['quantity'] . '</a>';
        }
        if ($days == 0 || $value['quantity'] == 0) {
            $tmpRate = 0;
        } else {
            $tmpRate = round($value['quantity'] * 100 / $days, 0);
        }
        $htmlData .= '<tr><td>' . $value['device_id'] . '</td><td>' . $link . '</td><td>' . $tmpRate . '%</td></tr>';
    }
    $htmlData .= '</tbody></table>';
}

$rate = round(($countDevice == 0 || $days == 0) ? 0 : $countGuardian * 100 / ($countDevice * $days),2); 
$rateHtml = "设备数：<font color='red'>$countDevice</font>。 设备使用率：<font color='red'>$rate%</font>(如果未选择开始时间，则不计算设备使用率)。";

echo <<<EOF
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-1" style="margin-bottom:3px;">
    <label for="salesman" class="control-label"><font color="red">*</font>医院</label>
  </div>
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <select class="form-control" name="hospital">$htmlHospitals</select>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <label for="start_time" class="control-label">开始日：</label>
    <input type="text" name="start_time" value="$startTimeDisplay" onclick="SelectDate(this,'yyyy-MM-dd')" />
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <label for="end_time" class="control-label">结束日：</label>
    <input type="text" name="end_time" value="$endTimeDisplay" onclick="SelectDate(this,'yyyy-MM-dd')" />
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-lg btn-info" style="margin-top:10px;">查看</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge blue;" />
$rateHtml
<hr style="border-top:1px ridge blue;" />
$htmlData
<script type="text/javascript" src="js/adddate.js"></script>
EOF;
require 'tpl/footer.tpl';
