<?php
require '../config/config.php';
require '../lib/function.php';

$title = '查看远程查房记录';
require 'header.php';

$patientId = isset($_GET['patient']) && !empty($_GET['patient']) ? $_GET['patient'] : '';
$hour = isset($_GET['hour']) && !empty($_GET['hour']) ? $_GET['hour'] : '';
if (isset($_GET['submit'])){    
    if (empty($hour)) {
        user_back_after_delay('请输入时间(格式为2位数字。例如08,13)。');
    }
    if (empty($patientId)) {
        user_back_after_delay('请输入病人ID。');
    }
    $ymd = date("Ymd");
    $logFile = PATH_LOG . $ymd . $hour . 'all_params.log';
    $pattern = "/$ymd (\d{2}:\d{2}:\d{2})----array \(\s+  'entry' => '(client_remote_check" 
            . "|app_get_command|app_upload_data)',\s+'patient_id' => '$patientId'/U";
    preg_match_all($pattern, file_get_contents($logFile), $out);
    if (empty($out[0])) {
        $table = '没有符合条件的记录。';
    } else {
        $data = '';
        $count = count($out[1]);
        for ($i = 0; $i < $count; $i++) {
            if ($out[2][$i] == 'client_remote_check') {
                $action = '发出远程查房命令';
            } elseif ($out[2][$i] == 'app_get_command') {
                $action = 'App响应';
            } elseif ($out[2][$i] == 'app_upload_data') {
                $action = '上传数据';
            } else {
                $action = '其他';
            }
            $data .= '<tr><td>'
                . $out[1][$i] . '</td><td>'
                . $out[2][$i] . '</td><td>'
                . $action . '</td></tr>';
        }
        $table = "<table class='table table-striped'>
        <thead><tr><th>时间</th><th>接口名</th><th>操作</th></tr></thead>
        <tbody>$data</tbody>
        </table>";
    }
} else {
    $table = '没有符合条件的记录。';
}
echo <<<EOF
<form class="form-horizontal" role="form" method="get">
  <div class="form-group">
    <label for="hospital_name" class="col-sm-3 control-label">时间段(如9时,请输入09)<font color="red">*</font></label>
    <div class="col-sm-3">
      <input type="text" class="form-control" name="hour" value="$hour" required>
    </div>
    <label for="hospital_name" class="col-sm-1 control-label">病人ID<font color="red">*</font></label>
    <div class="col-sm-3">
      <input type="text" class="form-control" name="patient" value="$patientId" required>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-lg btn-success" name="submit">查看</button>
    </div>
  </div>
</form>
<hr style="border-top:1px ridge red;" />
$table
EOF;
require 'tpl/footer.tpl';
