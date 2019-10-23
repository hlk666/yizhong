<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '查询患者状态';
require 'header.php';

$name = isset($_GET['name']) ? $_GET['name'] : '';
if (empty($name)) {
    $name = isset($_GET['hidden_name']) ? $_GET['hidden_name'] : '';
}
$id = isset($_GET['id']) ? $_GET['id'] : '';
$guardian = isset($_GET['guardian']) ? $_GET['guardian'] : '';

$patientList = array();
$patient = array();
if (!empty($guardian)) {
    $patient = DbiAdmin::getDbi()->getPatientStatus('0', $guardian);
    if (VALUE_DB_ERROR === $patient) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
} else {
    if (!empty($name)) {
        $patientList = DbiAdmin::getDbi()->getPatientFuzy($name);
        if (VALUE_DB_ERROR === $patientList) {
            user_back_after_delay(MESSAGE_DB_ERROR);
        }
    }
    if (!empty($id)) {
        $patient = DbiAdmin::getDbi()->getPatientStatus($id);
        if (VALUE_DB_ERROR === $patient) {
            user_back_after_delay(MESSAGE_DB_ERROR);
        }
    }
}


$htmlPatients = '';
foreach ($patientList as $value) {
    if ($id == $value['patient_id']) {
        $htmlPatients .= '<option value="' . $value['patient_id'] . '" selected>' . $value['patient_name'] . '</option>';
    } else {
        $htmlPatients .= '<option value="' . $value['patient_id'] . '">' . $value['patient_name'] . '</option>';
    }
}
if (!empty($patient)) {
    $guardianId = $patient['guardian_id'];
    $patientName = $patient['patient_name'];
    $hospitalId = $patient['hospital_id'];
    $hospitalName = $patient['hospital_name'];
    $startTime = $patient['start_time'];
    $endTime = $patient['end_time'];
    $deviceId = $patient['device_id'];
    $mode = $patient['mode'] == 1 ? '实时模式' : '异常模式';
    if ($patient['upload_status'] == 2) {
        $uploadStatus = '已上传';
    } elseif ($patient['upload_status'] == 3) {
        $uploadStatus = '已下载';
    } elseif ($patient['upload_status'] == 4) {
        $uploadStatus = '已分析';
    } elseif ($patient['upload_status'] == 5) {
        $uploadStatus = '已审核';
    } elseif ($patient['upload_status'] == 6) {
        $uploadStatus = '已分配';
    } elseif ($patient['upload_status'] == 7) {
        $uploadStatus = '问题数据';
    } elseif ($patient['upload_status'] == 8) {
        $uploadStatus = '已打印';
    } elseif ($patient['upload_status'] == 9) {
        $uploadStatus = '等待内部审核';
    } elseif ($patient['upload_status'] == 10) {
        $uploadStatus = '内部审核分配';
    } else {
        $uploadStatus = '未上传';
    }
    if ($patient['moved_type'] == 1) {
        $movedType = '协助分析';
    } elseif ($patient['moved_type'] == 2) {
        $movedType = '协助出报告';
    } else {
        $movedType = '未发生转移';
    }
    $movedHospitalName = $patient['moved_hospital_name'];
    $reportTime = $patient['report_time'];
    $hbiDoctor = $patient['hbi_doctor'];
    $reportDoctor = $patient['report_doctor'];
    $downloadDoctor = $patient['download_doctor_name'];
    $url = $patient['url'];
    $htmlTable = "
<table class='table table-striped'>
  <thead>
    <tr>
      <th></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr><td>姓名</td><td>$patientName</td></tr>
    <tr><td>监护ID</td><td>$guardianId</td></tr>
    <tr><td>医院ID</td><td>$hospitalId</td></tr>
    <tr><td>开单医院</td><td>$hospitalName</td></tr>
    <tr><td>注册时间</td><td>$startTime</td></tr>
    <tr><td>结束时间</td><td>$endTime</td></tr>
    <tr><td>设备ID</td><td>$deviceId</td></tr>
    <tr><td>监护模式</td><td>$mode</td></tr>
    <tr><td>长程分析进度</td><td>$uploadStatus</td></tr>
    <tr><td>长程分析转移</td><td>$movedType</td></tr>
    <tr><td>分析医院</td><td>$movedHospitalName</td></tr>
    <tr><td>分析/审核时间</td><td>$reportTime</td></tr>
    <tr><td>分析医生</td><td>$hbiDoctor</td></tr>
    <tr><td>审核医生</td><td>$reportDoctor</td></tr>
    <tr><td>数据分配医生</td><td>$downloadDoctor</td></tr>
    <tr><td>上传URL(gz后缀是手机上传)</td><td>$url</td></tr>
  </tbody>
</table>";
} else {
    $htmlTable = '';
}

echo <<<EOF
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">姓名(可模糊查询)</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="name" value="$name" required>
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-sm btn-info">搜索</button>
  </div>
</div>
</form>
<form class="form-horizontal" role="form" method="get">
<input type="hidden" name="hidden_name" value="$name" />
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label for="start_time" class="control-label">选择患者</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <select class="form-control" name="id" id="id">$htmlPatients</select>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label for="guardian" class="control-label">监护ID</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="guardian" id="guardian" value="$guardian">
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-sm btn-info" onclick="return display();">查看</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge red;" />
$htmlTable
<script>
function display()
{
    var tmpId = document.getElementById("id").innerHTML;
    var tmpGuardian = document.getElementById("guardian").value;
    if (tmpId == "" && tmpGuardian == "") {
        alert("请搜索并且选择1个患者，或者直接输入监护ID后再查看。");
        return false;
    }
    return true;
}
</script>
EOF;
require 'tpl/footer.tpl';
