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
$status = isset($_GET['status']) ? $_GET['status'] : '';

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
if (!empty($status)) {
    $deviceList = DbiAdmin::getDbi()->getDeviceByStatus($status);
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
    if ($value['hospital_id'] == '0') {
        $deviceUsed = '-';
    } else {
        $deviceUsed = '<a href="device_guardian.php?hospital=' . $value['hospital_id'] . '&device=' . $value['device_id'] . '">开单</a>';
    }
    if ($value['hospital_id'] == '9999') {
        $hospitalName = '已废弃';
    } else {
        $hospitalName = $value['hospital_name'];
    }
    $buttonTxt = $value['hospital_name'] == '羿中医疗生产部' ? '' 
            : '<button type="button" class="btn btn-xs btn-info" onclick="javascript:unbindDevice('. $value['device_id'] . ')">点击退回设备</button>';
    $htmlDevices .= '<tr><td>'
        . $hospitalName . '</td><td>'
        . $value['device_id'] . '</td><td>'
        . $deviceUsed . '</td><td>'
        . '<a href="device_question.php?id=' . $value['device_id'] . '">问题</a></td><td>'
        . '<a href="device_history.php?id=' . $value['device_id'] . '">历史</a></td><td>'
        . $value['ver_phone'] . '</td><td>'
        . $value['ver_embedded'] . '</td><td>'
        . $value['ver_app'] . '</td><td>'
        . $value['ver_pcb'] . '</td><td>'
        . $value['ver_box'] . '</td><td>'
        . $value['device_sale'] . '</td><td>'
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
</form>
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label for="start_time" class="control-label">设备问题处理进度</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <select class="form-control" name="status" id="status">
      <option value="0" selected>请选择</option>
      <option value="1" >未处理</option>
      <option value="2" >进度1</option>
      <option value="3" >进度2</option>
    </select>
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-sm btn-info"">查看设备</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge red;" />
  <table class="table table-striped">
    <thead>
      <tr>
        <th>医院名</th>
        <th>设备ID</th>
        <th>使用情况</th>
        <th>问题反馈</th>
        <th>生命周期</th>
        <th>手机</th>
        <th>嵌入式</th>
        <th>app</th>
        <th>电路板</th>
        <th>采集盒</th>
        <th>销售政策</th>
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
