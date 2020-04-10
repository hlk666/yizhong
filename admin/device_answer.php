<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '设备问题处理意见';
require 'header.php';

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。', 2000, 'device_answer.php');
    }

    $id = isset($_POST['id']) ?  trim($_POST['id']) : '0';
    $answer = isset($_POST['answer']) ?  trim($_POST['answer']) : '';
    $status = isset($_POST['status']) ?  trim($_POST['status']) : '2';
    $deviceId = isset($_POST['device_id']) ?  trim($_POST['device_id']) : '0';
    $userId = isset($_SESSION['user_id']) ?  trim($_SESSION['user_id']) : '0';
    if (empty($answer)) {
        user_back_after_delay('请正确输入处理意见。');
    }
    
    $ret = DbiAdmin::getDbi()->addAnswer($deviceId, $id, $answer, $userId, $status);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    
    echo MESSAGE_SUCCESS
    . '<br /><button type="button" class="btn btn-lg btn-info" style="margin-top:50px;" '
            . ' onclick="javascript:location.href=\'device_answer.php?id=' . $id . '\';">刷新查看</button>';

} else {
    $_SESSION['post'] = false;
    
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    if (empty($id)) {
        user_back_after_delay(MESSAGE_PARAM);
    }
    $ret = DbiAdmin::getDbi()->getDeviceAnswer($id);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    
    
    $htmlData = '';
    $count = count($ret);
    $deviceId = ($count == 0) ? '0' : $ret[0]['device_id'];
    for ($i = 0; $i < $count; $i++) {
        if ($i == 0) {
            $htmlData .= '<tr><th rowspan="' . $count . '">' . $ret[$i]['question'] . '</th>';
        }
        $htmlData .= '<td>' . $ret[$i]['answer'] . '</td><td>'
                . $ret[$i]['answer_time'] . '</td><td>'
                        . $ret[$i]['real_name'] . '</td><td>'
                                . $ret[$i]['time_diff'] . '</td></tr>';
    }
    echo <<<EOF
  <table class="table table-striped">
    <thead>
      <tr>
        <th>问题</th>
        <th>处理意见</th>
        <th>处理时间</th>
        <th>处理人员</th>
        <th>时间间隔(小时)</th>
      </tr>
    </thead>
    <tbody>$htmlData</tbody>
  </table>
<hr style="border-top:1px ridge red;" />
<form class="form-horizontal" role="form" method="post">
<input type="hidden" name="id" value="$id">
<input type="hidden" name="device_id" value="$deviceId">
<div class="row">
  
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">处理进度：</label>
  </div>
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <select class="form-control" name="status" id="status">
      <option value="0" selected>请选择</option>
      <option value="2" >进度1</option>
      <option value="3" >进度2</option>
    </select>
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">处理意见：</label>
  </div>
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="answer" required>
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-sm btn-info" name="submit">登录</button>
    <button type="button" class="btn btn-sm btn-primary" onclick="javascript:history.back();">返回</button>
  </div>
</div>
</form>
EOF;
}
require 'tpl/footer.tpl';
