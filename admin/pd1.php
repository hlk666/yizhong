<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '生产部管理';
$isHideSider = true;
require 'header.php';

if (isset($_POST['submit'])){
    $ret = DbiAdmin::getDbi()->addDevicePD($_POST['device']);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
}

$deviceList = DbiAdmin::getDbi()->getDeviceListPD();
if (VALUE_DB_ERROR === $deviceList) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$htmlDevices = '';
$optionVersion = '<option value="0">请选择版本</option>' 
        . '<option value="app:1.0,采集盒:2.0,嵌入式3.0">app:1.0,采集盒:2.0,嵌入式3.0</option>'
        . '<option value="app:1.1,采集盒:2.1,嵌入式3.1">app:1.1,采集盒:2.1,嵌入式3.1</option>';
foreach ($deviceList as $value) {
    $btnDel = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:pdFunc('. $value['device_id'] . ', \'delete\')">注销</button>';
    $btnAbandon = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:pdFunc('. $value['device_id'] . ', \'abandon\')">移入废品库</button>';
    $btnWarehouse = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:pdFunc('. $value['device_id'] . ', \'warehouse\')">移入成品库</button>';
    $selectVersion = '<select class="form-control" id="version' . $value['device_id'] . '">' . $optionVersion . '</select>'; 
    $htmlDevices .= '<tr><td>' . $value['device_id'] . '</td><td>' . $btnDel . '</td><td>' . 
        $btnAbandon . '</td><td>' . $selectVersion . '</td><td>' . $btnWarehouse . '</td></tr>';
}

echo <<<EOF
<h4>将设备添加到生产部(新品、退货等)。</h4>
<form class="form-horizontal" role="form" method="post">
<div class="row">
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <label class="control-label">输入设备ID：</label>
  </div>
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="device" required>
  </div>
  <div class="col-xs-12 col-sm-3">
    <button type="submit" class="btn btn-sm btn-info" name="submit">添加到生产部</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge red;" />
<table class="table table-striped">
<thead>
  <tr>
    <th>设备ID</th>
    <th>注销/删除设备ID(不移入废品库)</th>
    <th>移入废品库</th>
    <th>版本</th>
    <th>移入成品库(选择版本后再操作)</th>
  </tr>
</thead>
<tbody>$htmlDevices</tbody>
</table>
<script>
    function pdFunc(id, type)
    {
        var version = document.getElementById("version" + id).value;
        if (type == "warehouse" && version == "0") {
            alert("请选择版本后再移入成品库。");
            return;
        }
        alert(version);
        //window.location = 'pd_js.php?id=' + id + '&type=' + type + '&version=' + version;
    }
</script>
EOF;
require 'tpl/footer.tpl';
