<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';
require '../lib/ShortMessageService.php';

$title = '添加新的设备';
$isHideSider = true;
require 'header.php';

$repeatedAddHospital = !isset($_GET['hospital']) ? null : $_GET['hospital'];

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。', 2000, 'add.php');
    }
    
    $hospitalId = !isset($_POST['hospital']) ? null : $_POST['hospital'];
    $deviceId = !isset($_POST['device_id']) ? null : $_POST['device_id'];
    
    if (empty($hospitalId) || '0' == $hospitalId) {
        user_back_after_delay('请选择医院。');
    }
    if (empty($deviceId)) {
        user_back_after_delay('请正确输入设备ID。');
    }
    
    $isExisted = DbiAdmin::getDbi()->existedDevice($deviceId);
    if (VALUE_DB_ERROR === $isExisted) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    if (true === $isExisted) {
        user_back_after_delay('该设备ID已经和其他医院绑定，请解绑后再操作。');
    }
    
    $ret = DbiAdmin::getDbi()->addDevice($hospitalId, $deviceId);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    echo MESSAGE_SUCCESS 
        . '<button type="button" class="btn btn-lg btn-info" style="margin-top:50px;margin-left:50px;" ' 
        . ' onclick="javascript:location.href=\'add1.php?hospital=' . $hospitalId . '\';">继续给该医院添加设备</button>';
} else {
    $_SESSION['post'] = false;
    $ret = DbiAdmin::getDbi()->getHospitalWorder($_SESSION['user']);
    if (VALUE_DB_ERROR === $ret) {
        $ret = array();
    }
    $htmlHospitals = '<option value="0">请选择医院</option>';
    foreach ($ret as $value) {
        if ($repeatedAddHospital == $value['hospital_id']) {
            $htmlHospitals .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
        } else {
            $htmlHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
        }
    }
    
    echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  <div class="form-group">
    <label for="hospital" class="col-sm-2 control-label">请选择医院：</label>
    <div class="col-sm-10"><select class="form-control" name="hospital">$htmlHospitals</select></div>
  </div>
  <div class="form-group">
    <label for="device_id" class="col-sm-2 control-label">设备ID<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="device_id" name="device_id" placeholder="请输入设备ID" required>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-lg btn-success" name="submit">保存</button>
    </div>
  </div>
</form>

EOF;
}
require 'tpl/footer.tpl';
