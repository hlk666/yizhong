<?php
require '../common.php';
$title = '修改医院信息';
require 'header.php';

session_start();

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_goto('请不要刷新页面。', GOTO_FLAG_URL, 'add_hospital.php');
    }
    
    $hospitalName = !isset($_POST['hospital_name']) ? null : $_POST['hospital_name'];
    $hospitalTel = !isset($_POST['hospital_tel']) ? null : $_POST['hospital_tel'];
    $hospitalAddress = !isset($_POST['hospital_address']) ? null : $_POST['hospital_address'];
    $parentFlag = !isset($_POST['parent_flag']) ? null : $_POST['parent_flag'];
    $hospitalParent = !isset($_POST['hospital_parent']) ? null : $_POST['hospital_parent'];
    
    if (empty($hospitalName)) {
        user_goto('请正确输入医院名。', GOTO_FLAG_BACK);
    }
    if (empty($hospitalTel)) {
        user_goto('请正确输入医院电话。', GOTO_FLAG_BACK);
    }
    if (empty($hospitalAddress)) {
        user_goto('请正确输入医院地址。', GOTO_FLAG_BACK);
    }
    
//     $ret = Dbi::getDbi()->addHospital($hospitalName, $hospitalTel, $hospitalAddress, $parentFlag, $hospitalParent);
//     if (VALUE_DB_ERROR === $ret) {
//         user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
//     }
    $_SESSION['post'] = true;
    echo MESSAGE_SUCCESS 
        . '<br /><button type="submit" class="btn btn-lg btn-info" style="margin-top:50px;" ' 
        . ' onclick="javascript:location.href=\'hospital.php\';">查看医院列表</button>';
} else {
    $_SESSION['post'] = false;
    $action = isset($_GET['action']) ? $_GET['action'] : null;
    $hospitalId = isset($_GET['id']) ? $_GET['id'] : null;
    if (empty($action) || empty($hospitalId)) {
        user_goto(MESSAGE_PARAM, GOTO_FLAG_BACK);
    }
    
    $ret = Dbi::getDbi()->getHospitalInfo($hospitalId);
    if (VALUE_DB_ERROR === $ret) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
    }
    $hospitalName = $ret['hospital_name'];
    
    echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  <div class="form-group">
    <label for="hospital_name" class="col-sm-2 control-label">医院名<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="hospital_name" name="hospital_name" value="请输入医院的名字">
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_tel" class="col-sm-2 control-label">电话<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="hospital_tel" name="hospital_tel" value="请输入医院的联系电话">
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_address" class="col-sm-2 control-label">地址<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="hospital_address" name="hospital_address" value="请输入医院的地址">
    </div>
  </div>
  <div class="form-group">
    <label for="parent_flag" class="col-sm-2 control-label">可否作为上级医院<font color="red">*</font></label>
    <div class="col-sm-10">
      <label class="checkbox-inline">
      <input type="radio" name="parent_flag" value="1">可</label>
      <label class="checkbox-inline">
      <input type="radio" name="parent_flag" value="0" checked>否</label>
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_parent" class="col-sm-2 control-label">本院的上级医院</label>
    <div class="col-sm-10">
      <select class="form-control" name="hospital_parent">
        <option value="0">请选择上级医院(非必须)</option>$htmlParentHospitals
    </select></div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-lg btn-success" name="submit">保存</button>
      <button type="button" class="btn btn-lg btn-primary" style="margin-left:50px" 
        onclick="javascript:history.back();">返回</button>
    </div>
  </div>
</form>
EOF;
}
require 'tpl/footer.tpl';
