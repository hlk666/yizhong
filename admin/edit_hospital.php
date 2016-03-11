<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '修改/删除医院信息';
require 'header.php';

if (isset($_POST['edit']) || isset($_POST['del'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要重复刷新页面。', 2000, 'edit_hospital.php');
    }
    
    $hospitalId = !isset($_POST['hospital_id']) ? null : $_POST['hospital_id'];
    $hospitalName = !isset($_POST['hospital_name']) ? null : $_POST['hospital_name'];
    $hospitalTel = !isset($_POST['hospital_tel']) ? null : $_POST['hospital_tel'];
    $hospitalAddress = !isset($_POST['hospital_address']) ? null : $_POST['hospital_address'];
    $parentFlag = !isset($_POST['parent_flag']) ? null : $_POST['parent_flag'];
    $loginUser = !isset($_POST['login_user']) ? null : $_POST['login_user'];
    
    if (empty($hospitalId)) {
        user_back_after_delay('非法访问');
    }
    
    if (isset($_POST['edit'])) {
        if (empty($hospitalName)) {
            user_back_after_delay('请正确输入医院名。');
        }
        if (empty($hospitalTel)) {
            user_back_after_delay('请正确输入医院电话。');
        }
        if (empty($hospitalAddress)) {
            user_back_after_delay('请正确输入医院地址。');
        }
        if (empty($parentFlag)) {
            user_back_after_delay('请设置是否可以作为上级医院。');
        }
        if (empty($loginUser)) {
            user_back_after_delay('请正确输入管理员登录用户。');
        }
        
        $isExisted = DbiAdmin::getDbi()->existedLoginName($loginUser, $hospitalId);
        if (VALUE_DB_ERROR === $isExisted) {
            user_back_after_delay(MESSAGE_DB_ERROR);
        }
        if (true === $isExisted) {
            user_back_after_delay("登录用户名<font color='red'>$loginUser</font>已被他人使用。");
        }
        
        $ret = DbiAdmin::getDbi()->editHospital($hospitalId,
            $hospitalName, $hospitalTel, $hospitalAddress, $parentFlag, $loginUser);
    }
    
    if (isset($_POST['del'])) {
        $ret = DbiAdmin::getDbi()->delHospital($hospitalId);
    }
    
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    echo MESSAGE_SUCCESS
    . '<br /><button type="button" class="btn btn-lg btn-info" style="margin-top:50px;" '
            . ' onclick="javascript:location.href=\'hospital.php\';">查看医院列表</button>';
} else {
    $action = isset($_GET['action']) ? $_GET['action'] : null;
    $hospitalId = isset($_GET['id']) ? $_GET['id'] : null;
    $_SESSION['post'] = false;
    
    if (empty($action) || empty($hospitalId)) {
        user_back_after_delay('非法访问');
    }
    $hospitalInfo = DbiAdmin::getDbi()->getHospitalInfo($hospitalId);
    if (VALUE_DB_ERROR === $hospitalInfo) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    
    $hospitalName = $hospitalInfo['hospital_name'];
    $address = $hospitalInfo['address'];
    $tel = $hospitalInfo['tel'];
    $loginUser = $hospitalInfo['login_name'];
    
    if ('1' == $hospitalInfo['parent_flag']) {
        $htmlParentFlagYes = '<input type="radio" name="parent_flag" value="1" checked>可</label>';
        $htmlParentFlagNo = '<input type="radio" name="parent_flag" value="0">否</label>';
    } else {
        $htmlParentFlagYes = '<input type="radio" name="parent_flag" value="1">可</label>';
        $htmlParentFlagNo = '<input type="radio" name="parent_flag" value="0" checked>否</label>';
    }
    $button = '';
    if ('del' === $action) {
        $button = '确定删除';
        $style = 'danger';
    }
    if ('edit' === $action) {
        $button = '确定修改';
        $style = 'info';
    }
    
    echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  <input type="hidden" name="hospital_id" value="$hospitalId">
  <div class="form-group">
    <label for="hospital_name" class="col-sm-2 control-label">医院名<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="hospital_name" name="hospital_name" value="$hospitalName">
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_tel" class="col-sm-2 control-label">电话<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="hospital_tel" name="hospital_tel" value="$tel">
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_address" class="col-sm-2 control-label">地址<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="hospital_address" name="hospital_address" value="$address">
    </div>
  </div>
  <div class="form-group">
    <label for="parent_flag" class="col-sm-2 control-label">可否作为上级医院<font color="red">*</font></label>
    <div class="col-sm-10">
      <label class="checkbox-inline">$htmlParentFlagYes
      <label class="checkbox-inline">$htmlParentFlagNo
    </div>
  </div>
  <div class="form-group">
    <label for="login_user" class="col-sm-2 control-label">管理员登录用户<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="login_user" name="login_user" value="$loginUser">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-lg btn-$style" name="$action">$button</button>
      <button type="button" class="btn btn-lg btn-primary" style="margin-left:50px"
        onclick="javascript:history.back();">返回</button>
    </div>
  </div>
</form>
EOF;
}

require 'tpl/footer.tpl';
