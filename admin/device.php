<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '医院-设备列表';
require 'header.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$hospital = isset($_GET['hospital']) ? $_GET['hospital'] : '';
$agency = isset($_GET['agency']) ? $_GET['agency'] : '';

$hospitalList = array();
$deviceList = array();

if (!empty($id)) {
    $deviceList = DbiAdmin::getDbi()->getDeviceById($id);
    if (VALUE_DB_ERROR === $deviceList) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
}
if (!empty($name)) {
    $hospitalList = DbiAdmin::getDbi()->getHospitalList('', '', '', $name);
    if (VALUE_DB_ERROR === $hospitalList) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
}
if (!empty($hospital)) {
    $deviceList = DbiAdmin::getDbi()->getDeviceList($hospital);
    if (VALUE_DB_ERROR === $deviceList) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
}

if (!empty($agency)) {
    $deviceList = DbiAdmin::getDbi()->getDeviceListAgency($agency);
    if (VALUE_DB_ERROR === $deviceList) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
}

$htmlHospitals = '';
foreach ($hospitalList as $value) {
    if ($hospital == $value['hospital_id']) {
        $htmlHospitals .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
    } else {
        $htmlHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
    }
}

$htmlDevices = '';
foreach ($deviceList as $value) {
    $buttonTxt = $value['hospital_name'] == '羿中医疗生产部' ? '' 
            : '<button type="button" class="btn btn-xs btn-info" onclick="javascript:unbindDevice('. $value['device_id'] . ')">点击退回设备</button>';
    $htmlDevices .= '<tr><td>'
        . $value['hospital_name'] . '</td><td>'
        . $value['device_id'] . '</td><td>'
        . $value['agency'] . '</td><td>'
        . $value['salesman'] . '</td></tr>';
        //. $buttonTxt . '</td></tr>';
}


echo <<<EOF
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <label class="control-label">输入设备ID(支持输入后n位)：</label>
  </div>
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="id" value="$id" required>
  </div>
  <div class="col-xs-12 col-sm-3">
    <button type="submit" class="btn btn-sm btn-info">根据设备ID搜索</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge red;" />
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">医院名</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="name" value="$name" required>
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-sm btn-info">搜索医院</button>
  </div>
</div>
</form>
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label for="start_time" class="control-label">选择医院</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <select class="form-control" name="hospital" id="hospitals">$htmlHospitals</select>
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-sm btn-info" onclick="return display();">查看设备</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge red;" />
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <label class="control-label">输入代理商/业务员姓名：</label>
  </div>
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="agency" value="$agency" required>
  </div>
  <div class="col-xs-12 col-sm-3">
    <button type="submit" class="btn btn-sm btn-info">搜索</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge red;" />
  <table class="table table-striped">
    <thead>
      <tr>
        <th>医院名</th>
        <th>设备ID</th>
        <th>代理商</th>
        <th>业务员</th>
      </tr>
    </thead>
    <tbody>$htmlDevices</tbody>
  </table>
<script>
function display()
{
    var tmp = document.getElementById("hospitals").innerHTML;
    if (tmp == "") {
        alert("请搜索并且选择1个医院。");
        return false;
    }
    return true;
}
</script>
EOF;
require 'tpl/footer.tpl';
