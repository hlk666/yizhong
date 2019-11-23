<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '发货(调配设备)';
require 'header.php';

$repeatedAddHospital = !isset($_GET['hospital']) ? null : $_GET['hospital'];

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。');
    }
    
    $hospitalId = !isset($_POST['hospital']) ? 0 : $_POST['hospital'];
    $deviceList = !isset($_POST['device']) ? null : $_POST['device'];
    $deviceIdList = !isset($_POST['device_id']) ? null : $_POST['device_id'];
    $agency = !isset($_POST['agency']) ? '0' : $_POST['agency'];
    $salesman = !isset($_POST['salesman']) ? '0' : $_POST['salesman'];
    $content = !isset($_POST['content']) ? '' : $_POST['content'];
    $action = !isset($_POST['action']) ? '' : $_POST['action'];
    
    if ((empty($hospitalId) || '0' == $hospitalId) && (empty($agency)) && (empty($salesman))) {
        user_back_after_delay('请选择医院或代理商/业务员。');
    }
    if (!empty($agency) && !empty($salesman)) {
        user_back_after_delay('不能同时选择代理商和业务员。');
    }
    if (empty($deviceList) && empty($deviceIdList)) {
        user_back_after_delay('请选择(或输入)设备ID。');
    }
    if (empty($action)) {
        user_back_after_delay('请选择备注信息。');
    }
    if (empty($deviceList) && !empty($deviceIdList)) {
        $deviceList = explode(',', str_replace('，', ',', $deviceIdList));
    }
    /*
    foreach ($deviceList as $deviceId) {
        $isExisted = DbiAdmin::getDbi()->existedDevice1($deviceId);
        if (VALUE_DB_ERROR === $isExisted) {
            user_back_after_delay(MESSAGE_DB_ERROR);
        } elseif (true === $isExisted) {
            user_back_after_delay("设备【 $deviceId 】已绑定其他医院。");
        }  else {
            continue;
        }
    }
    */
    foreach ($deviceList as $deviceId) {
        $ret = DbiAdmin::getDbi()->checkDeviceDelivery($deviceId);
        if (VALUE_DB_ERROR === $ret) {
            user_back_after_delay(MESSAGE_DB_ERROR);
        } elseif ($ret == 1) {
            user_back_after_delay("设备【 $deviceId 】的ID不存在，不能发货/调配。");
        }  elseif ($ret == 2) {
            //user_back_after_delay("设备【 $deviceId 】在生产部，不能发货/调配。");
            //not check 20191025
        }  else {
            //do nothing.
        }
    }
    if (!empty($hospitalId)) {
        $agency = 0;
        $salesman = 0;
    }
    foreach ($deviceList as $deviceId) {
        if (empty($deviceId)) {
            user_back_after_delay('设备ID有误。');
        }
        $ret = DbiAdmin::getDbi()->delDevice($deviceId, $hospitalId, $agency, $salesman, $_SESSION['user'], $content, $action);
        if (VALUE_DB_ERROR === $ret) {
            user_back_after_delay(MESSAGE_DB_ERROR);
        }
    }
    $_SESSION['post'] = true;
    echo MESSAGE_SUCCESS;
} else {
    $_SESSION['post'] = false;
    
    $name = isset($_GET['name']) && !empty($_GET['name']) ? $_GET['name'] : '';
    $count = isset($_GET['count']) && !empty($_GET['count']) ? $_GET['count'] : '';
    
    echo <<<EOF
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">医院名(模糊匹配)</label>
  </div>
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="name" value="$name" >
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">设备列表数量</label>
  </div>
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="count" value="$count">
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-info">显示</button>
  </div>
</div>
</form>
EOF;
    if (empty($name)) {
        $htmlHospitals = '<option value="0">无医院</option>';
    } else {
        $ret = DbiAdmin::getDbi()->getHospitalList(null, null, null, $name);
        if (VALUE_DB_ERROR === $ret) {
            $ret = array();
        }
        $htmlHospitals = '<option value="0">请选择医院</option>';
        foreach ($ret as $value) {
            $htmlHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
        }
    }
        
    if (empty($count)) {
        $htmlDevices = '<input type="text" class="form-control" name="device_id" >';
    } else {
        $ret = DbiAdmin::getDbi()->getDeviceNotUsed($count);
        if (VALUE_DB_ERROR === $ret) {
            $ret = array();
        }
        $htmlDevices = '';
        foreach ($ret as $value) {
            $htmlDevices .= '<label class="checkbox-inline"><input type="checkbox" name="device[]" value="'
                    . $value['device_id'] . '">' . $value['device_id'] . '</label>';
        }
    }
    
    $ret = DbiAdmin::getDbi()->getAgencyList();
    if (VALUE_DB_ERROR === $ret) {
        $ret = array();
    }
    echo '<script>var agencyData = ' . json_encode($ret, JSON_UNESCAPED_UNICODE) . ';</script>';
    $htmlAgency = '<option value="0">请选择代理商</option>';
    foreach ($ret as $value) {
        $htmlAgency .= '<option value="' . $value['agency_id'] . '">' . $value['name'] . '</option>';
    }
    
    $ret = DbiAdmin::getDbi()->getSalesmanList();
    if (VALUE_DB_ERROR === $ret) {
        $ret = array();
    }
    echo '<script>var salesmanData = ' . json_encode($ret, JSON_UNESCAPED_UNICODE) . ';</script>';
    $htmlSalesman = '<option value="0">请选择业务员</option>';
    foreach ($ret as $value) {
        $htmlSalesman .= '<option value="' . $value['salesman_id'] . '">' . $value['name'] . '</option>';
    }
    
        
        echo <<<EOF
<hr style="border-top:1px ridge red;" />
<form class="form-horizontal" role="form" method="post">
  <div class="form-group">
    <label for="hospital" class="col-sm-2 control-label">注意：</label>
    <label for="hospital" class="col-sm-10 control-label" style="text-align:left;"><font color="red">医院和代理商，请只选择一项。</font></label>
  </div>
  <div class="form-group">
    <label for="hospital" class="col-sm-2 control-label">请选择医院：</label>
    <div class="col-sm-10"><select class="form-control" name="hospital">$htmlHospitals</select></div>
  </div>
  <div class="form-group">
    <label for="device_id" class="col-sm-2 control-label">设备ID(输入时用英文逗号分隔)<font color="red">*</font></label>
    <div class="col-sm-10">$htmlDevices</div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">代理商</label>
    <div class="col-sm-3">
    <input type="text" class="form-control" id="agency_input" onBlur="agencyFilter()"></div>
    <div class="col-sm-7">
      <select class="form-control" id="agency" name="agency">$htmlAgency</select>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">业务员</label>
    <div class="col-sm-3">
    <input type="text" class="form-control" id="salesman_input" onBlur="salesmanFilter()"></div>
    <div class="col-sm-7">
      <select class="form-control" id="salesman" name="salesman">$htmlSalesman</select>
    </div>
  </div>
  <div class="form-group">
    <label for="content" class="col-sm-2 control-label">操作<font color="red">*</font></label>
    <div class="col-sm-3">
      <select class="form-control" name="action">
        <option value="">请选择</option>
        <option value="新注册">新注册</option>
        <option value="追加设备">追加设备</option>
        <option value="更换设备">退换设备</option>
        <option value="撤回设备">收回设备</option>
        <option value="调拨">调拨</option>
        <option value="其他">其他</option>
      </select>
    </div>
    <label for="content" class="col-sm-2 control-label">备注</label>
    <div class="col-sm-5">
      <input type="text" class="form-control" name="content" >
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-5 col-sm-2">
      <button type="submit" class="btn btn-lg btn-success" name="submit">发货</button>
    </div>
  </div>
</form>
<script>
function agencyFilter()
{
    var agencyValue = document.getElementById('agency_input').value.trim();
    var agencySelect = document.getElementById('agency');
    agencySelect.options.length = 0;
    agencySelect.options.add(new Option('请选择', ''));
    for(let item in agencyData)
    {
        if (agencyValue == '') {
            agencySelect.options.add(new Option(agencyData[item].name, agencyData[item].agency_id));
        } else {
            var index = agencyData[item].name.indexOf(agencyValue);
            if (index != -1) {
                agencySelect.options.add(new Option(agencyData[item].name, agencyData[item].agency_id));
            }
        }
    }
}
function salesmanFilter()
{
    var salesmanValue = document.getElementById('salesman_input').value.trim();
    var salesmanSelect = document.getElementById('salesman');
    salesmanSelect.options.length = 0;
    salesmanSelect.options.add(new Option('请选择', ''));
    for(let item in salesmanData)
    {
        if (salesmanValue == '') {
            salesmanSelect.options.add(new Option(salesmanData[item].name, salesmanData[item].salesman_id));
        } else {
            var index = salesmanData[item].name.indexOf(salesmanValue);
            if (index != -1) {
                salesmanSelect.options.add(new Option(salesmanData[item].name, salesmanData[item].salesman_id));
            }
        }
    }
}
</script>
EOF;
}
require 'tpl/footer.tpl';
