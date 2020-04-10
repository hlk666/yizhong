<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '设备问题记录';
require 'header.php';

$device = isset($_GET['id']) ? $_GET['id'] : '';
if (empty($device)) {
    user_back_after_delay(MESSAGE_PARAM);
}
$ret = DbiAdmin::getDbi()->getDeviceQuestion($device);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$htmlData = '';
foreach ($ret as $value) {
    $htmlData .= '<tr><td>' 
        . $device . '</td><td>'
        . $value['question_id'] . '</td><td>'
        . $value['hospital_name'] . '</td><td>'
        . $value['text'] . '</td><td>'
        . $value['question_time'] . '</td><td>'
        . $value['real_name'] . '</td><td>'
        . '<a href="device_answer.php?id=' . $value['question_id'] . '">查看</a></td></tr>';
}
echo <<<EOF
  <table class="table table-striped">
    <thead>
      <tr>
        <th>设备ID</th>
        <th>问题ID</th>
        <th>医院</th>
        <th>问题描述</th>
        <th>时间</th>
        <th>人员</th>
        <th>处理意见</th>
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
