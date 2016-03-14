<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '医院-设备列表';
require 'header.php';

$hospital = isset($_GET['hospital']) ? $_GET['hospital'] : null;
if (0 == $hospital) {
    $hospital = null;
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

$ret = DbiAdmin::getDbi()->getDeviceList($hospital);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}
$count = count($ret);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$rows = 10;
$offset = ($page - 1) * $rows;
$lastPage = ceil($count / $rows);

if (1 === $page) {
    $ret = array_slice($ret, 0, $rows);
} else {
    $ret = DbiAdmin::getDbi()->getDeviceList($hospital, $offset, $rows);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
}

$htmlDevices = '';
foreach ($ret as $value) {
    $htmlDevices .= '<tr><td>' 
        . $value['hospital_name'] . '</td><td>'
        . $value['device_id'] . '</td><td>'
        . $value['city'] . '</td><td>'
        . '<button type="button" class="btn btn-xs btn-info" onclick="javascript:unbindDevice(' 
            . $value['device_id'] . ')">点击解除</button></td></tr>';
}
$currentPage = null;
if (null !== $hospital) {
    $currentPage = 'device.php?hospital=' . $hospital;
}
$paging = getPaging($page, $lastPage, $currentPage);
echo <<<EOF
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label for="start_time" class="control-label"><font color="red">*</font>选择医院</label>
  </div>
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <select class="form-control" name="hospital">$htmlHospitals</select>
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-sm btn-info">查看该院设备</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge red;" />
  <table class="table table-striped">
    <thead>
      <tr>
        <th>医院名</th>
        <th>设备ID</th>
        <th>城市代码</th>
        <th>解除绑定</th>
      </tr>
    </thead>
    <tbody>$htmlDevices</tbody>
  </table>
<div style="text-align:right;">
<ul class="pagination">$paging</ul>
<div>
EOF;
require 'tpl/footer.tpl';
