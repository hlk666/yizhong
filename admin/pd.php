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
$optionVersionPhone = '<option value="0">请选择</option>' 
        . '<option value="红米5A">红米5A</option>'
        . '<option value="红米5A">红米7A</option>'
        . '<option value="联想A5">联想A5</option>';
$optionVersionEmbedded = '<option value="3.0(调整写卡看门狗)">调整写卡看门狗</option>';
//'<option value="0">请选择</option>' . '<option value="3.0(调整写卡看门狗)">3.0(调整写卡看门狗)</option>';
$optionVersionApp = '<option value="20.0">20.0</option>';
//'<option value="0">请选择</option>' . '<option value="0.1(test)">0.1(test)</option>';
$optionVersionPcb = '<option value="0">请选择</option>'
        . '<option value="20180801">20180801</option>'
        . '<option value="返修">返修</option>';
$optionVersionBox = '<option value="YZXD801-2">YZXD801-2</option>';
//'<option value="0">请选择</option>' . '<option value="白盒方形">白盒方形</option>';

foreach ($deviceList as $value) {
    $btnDel = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:pdFunc('. $value['device_id'] . ', \'delete\')">注销</button>';
    $btnAbandon = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:pdFunc('. $value['device_id'] . ', \'abandon\')">移入废品库</button>';
    $btnWarehouse = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:pdFunc('. $value['device_id'] . ', \'warehouse\')">移入成品库</button>';
    $selectVersionPhone = '<select class="form-control" id="version_phone' . $value['device_id'] . '">' . $optionVersionPhone . '</select>';
    $selectVersionEmbedded = '<select class="form-control" id="version_embedded' . $value['device_id'] . '">' . $optionVersionEmbedded . '</select>';
    $selectVersionApp = '<select class="form-control" id="version_app' . $value['device_id'] . '">' . $optionVersionApp . '</select>';
    $selectVersionPcb = '<select class="form-control" id="version_pcb' . $value['device_id'] . '">' . $optionVersionPcb . '</select>';
    $selectVersionBox = '<select class="form-control" id="version_box' . $value['device_id'] . '">' . $optionVersionBox . '</select>';
    $htmlDevices .= '<tr><td>' . $value['device_id'] . '</td><td>' . $btnDel . '</td><td>' . $btnAbandon 
                . '</td><td>' . $selectVersionPhone . '</td><td>' . $selectVersionEmbedded 
                . '</td><td>' . $selectVersionApp . '</td><td>' . $selectVersionPcb 
                . '</td><td>' . $selectVersionBox 
                . '</td><td>' . $btnWarehouse . '</td></tr>';
}

echo <<<EOF
<h4>将设备添加到生产部(新品)。</h4>
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
    <th>注销/删除设备ID</th>
    <th>移入废品库</th>
    <th>手机版本</th><th>嵌入式版本</th><th>APP版本</th><th>电路板批次</th><th>记录盒型号</th>
    <th>移入成品库</th>
  </tr>
</thead>
<tbody>$htmlDevices</tbody>
</table>
<script>
    function pdFunc(id, type)
    {
        var version_phone = document.getElementById("version_phone" + id).value;
        var version_embedded = document.getElementById("version_embedded" + id).value;
        var version_app = document.getElementById("version_app" + id).value;
        var version_pcb = document.getElementById("version_pcb" + id).value;
        var version_box = document.getElementById("version_box" + id).value;
        if (type == "warehouse" && (version_phone == "0" || version_embedded == "0"
            || version_app == "0" || version_pcb == "0" || version_box == "0")) {
            alert("请选择版本、型号后再移入成品库。");
            return;
        }
        //alert(version_phone + "," + version_embedded+ "," + version_app+ "," + version_pcb + "," + version_box);
        window.location = 'pd_js.php?id=' + id + '&type=' + type + '&ver_phone=' + version_phone 
        + '&ver_embedded=' + version_embedded + '&ver_app=' + version_app+ '&ver_pcb=' + version_pcb+ '&ver_box=' + version_box;
    }
</script>
EOF;
require 'tpl/footer.tpl';
