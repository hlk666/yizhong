<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '生产部管理';
$isHideSider = true;
require 'header.php';

if (isset($_POST['submit'])){
    $deviceId = $_POST['device'];
    $isExisted = DbiAdmin::getDbi()->existedDevice2($deviceId);
    if (VALUE_DB_ERROR === $isExisted) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    } elseif (true === $isExisted) {
        user_back_after_delay("设备【 $deviceId 】已绑定其他医院或代理商/业务员，请从【发货/调配】页面操作。");
    } else {
        $ret = DbiAdmin::getDbi()->addDevicePD($deviceId);
        if (VALUE_DB_ERROR === $ret) {
            user_back_after_delay(MESSAGE_DB_ERROR);
        }
    }
}

$deviceList = DbiAdmin::getDbi()->getDeviceListPD();
if (VALUE_DB_ERROR === $deviceList) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$htmlDevices = '';
foreach ($deviceList as $value) {
    $btnDel = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:pdFunc('. $value['device_id'] . ', \'delete\')">注销</button>';
    $btnAbandon = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:pdFunc('. $value['device_id'] . ', \'abandon\')">移入废品库</button>';
    $btnWarehouse = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:pdFunc('. $value['device_id'] . ', \'warehouse\')">移入成品库</button>';
    $inputIccid = '<input type="text" class="form-control" id="iccid' . $value['device_id'] . '" value="'. $value['iccid'] .'">'; 
    $htmlDevices .= '<tr><td>' . $value['device_id'] . '</td><td>' . $btnDel . '</td><td>' . 
        $btnAbandon . '</td><td>' . $inputIccid . '</td><td>' . $btnWarehouse . '</td></tr>';
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
    <th>联通平台iccid</th>
    <th>移入成品库</th>
  </tr>
</thead>
<tbody>$htmlDevices</tbody>
</table>
<script>
    function pdFunc(id, type)
    {
        var iccid = document.getElementById("iccid" + id).value;
        window.location = 'pd_js.php?id=' + id + '&type=' + type + '&iccid=' + iccid;
    }
</script>
EOF;
require 'tpl/footer.tpl';
