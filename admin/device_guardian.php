<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '设备开单明细';
require 'header.php';

$hospital = isset($_GET['hospital']) ? $_GET['hospital'] : '';
$device = isset($_GET['device']) ? $_GET['device'] : '';
if (empty($device)) {
    user_back_after_delay(MESSAGE_PARAM);
}
$startTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$endTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] . ' 23:59:59' : null;

$ret = DbiAdmin::getDbi()->getHospitalGuardian($hospital, $device, $startTime, $endTime);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$htmlData = '';
foreach ($ret as $value) {
    $htmlData .= '<tr><td>' 
        . $value['hospital_name'] . '</td><td>'
        . $value['device_id'] . '</td><td>'
        . $value['guardian_id'] . '</td><td>'
        . $value['regist_time'] . '</td><td>'
        . $value['patient_name'] . '</td><td>'
        . $value['regist_doctor_name'] . '</td></tr>';
}
echo <<<EOF
  <table class="table table-striped">
    <thead>
      <tr>
        <th>开单医院</th>
        <th>设备ID</th>
        <th>监护ID</th>
        <th>开单时间</th>
        <th>病人姓名</th>
        <th>开单医生</th>
      </tr>
    </thead>
    <tbody>$htmlData</tbody>
  </table>
  <div class="col-sm-offset-4 col-sm-4">
      <button type="button" class="btn btn-lg btn-primary" style="margin-left:50px" 
        onclick="javascript:history.back();">返回</button>
    </div>
EOF;
require 'tpl/footer.tpl';
