<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '设备调配历史';
require 'header.php';

$device = isset($_GET['device']) ? $_GET['device'] : '';
if (empty($device)) {
    user_back_after_delay(MESSAGE_PARAM);
}
$ret = DbiAdmin::getDbi()->getDeviceHistory($device);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$htmlData = '';
foreach ($ret as $value) {
    $htmlData .= '<tr><td>' 
        . $value['device_id'] . '</td><td>'
        . $value['regist_time'] . '</td><td>'
        . $value['patient_name'] . '</td><td>'
        . $value['regist_doctor_name'] . '</td></tr>';
}
echo <<<EOF
  <table class="table table-striped">
    <thead>
      <tr>
        <th>设备ID</th>
        <th>时间</th>
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
