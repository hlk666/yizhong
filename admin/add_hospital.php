<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '添加新的医院';
require 'header.php';

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。', 2000, 'add_hospital.php');
    }
    
    $hospitalName = !isset($_POST['hospital_name']) ? null : $_POST['hospital_name'];
    $hospitalTel = !isset($_POST['hospital_tel']) ? null : $_POST['hospital_tel'];
    $province = isset($_POST['province']) ? $_POST['province'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $hospitalAddress = !isset($_POST['hospital_address']) ? null : $_POST['hospital_address'];
    $parentFlag = !isset($_POST['parent_flag']) ? null : $_POST['parent_flag'];
    $hospitalParent = !isset($_POST['hospital_parent']) ? null : $_POST['hospital_parent'];
    $adminUser = !isset($_POST['admin']) ? null : $_POST['admin'];
    $messageTel = (isset($_POST['message_tel']) && !empty($_POST['message_tel'])) ? $_POST['message_tel'] : '0';
    $salesman = (isset($_POST['salesman']) && !empty($_POST['salesman'])) ? $_POST['salesman'] : '';
    $comment = (isset($_POST['comment']) && !empty($_POST['comment'])) ? $_POST['comment'] : '';
    
    if (empty($hospitalName)) {
        user_back_after_delay('请正确输入医院名。');
    }
    if (empty($hospitalTel)) {
        user_back_after_delay('请正确输入医院电话。');
    }
    if (empty($province)) {
        user_back_after_delay('请选择省/市/自治区。');
    }
    if (empty($city)) {
        user_back_after_delay('请选择城市。');
    }
    if (empty($hospitalAddress)) {
        user_back_after_delay('请正确输入医院地址。');
    }
    if (empty($adminUser)) {
        user_back_after_delay('请正确输入初始管理员登录用户。');
    }
    
    $isExisted = DbiAdmin::getDbi()->existedLoginName($adminUser, 0);
    if (VALUE_DB_ERROR === $isExisted) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    if (true === $isExisted) {
        user_back_after_delay("登录用户名<font color='red'>$adminUser</font>已被他人使用。");
    }
    
    $ret = DbiAdmin::getDbi()->addHospital($hospitalName, $hospitalTel, $province, $city, $hospitalAddress, 
            $parentFlag, $hospitalParent, $adminUser, $messageTel, $salesman, $comment);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    echo MESSAGE_SUCCESS 
        . '<br /><button type="button" class="btn btn-lg btn-info" style="margin-top:50px;" ' 
        . ' onclick="javascript:location.href=\'hospital.php\';">查看医院列表</button>';
} else {
    $_SESSION['post'] = false;
    $ret = DbiAdmin::getDbi()->getHospitalParentList();
    if (VALUE_DB_ERROR === $ret) {
        $ret = array();
    }
    $htmlParentHospitals = '';
    foreach ($ret as $value) {
        $htmlParentHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
    }
    
    echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  <div class="form-group">
    <label for="hospital_name" class="col-sm-2 control-label">医院名<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="hospital_name" name="hospital_name" placeholder="请输入医院的名字" required>
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_tel" class="col-sm-2 control-label">电话<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="hospital_tel" name="hospital_tel" placeholder="请输入医院的联系电话" required>
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_address" class="col-sm-2 control-label">地址<font color="red">*</font></label>
    <div class="col-sm-2">
      <select class="form-control" name="province" id="proS" onchange="loadCity()"><option value="0">请选择</option></select>
    </div>
    <div class="col-sm-2">
      <select class="form-control" name="city" id="cityS"><option value="0">请选择</option></select>
    </div>
    <div class="col-sm-6">
      <input type="text" class="form-control" id="hospital_address" name="hospital_address" placeholder="请输入医院的地址" required>
    </div>
  </div>
  <div class="form-group">
    <label for="admin" class="col-sm-2 control-label">管理员<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="admin" name="admin" placeholder="请输入管理员登录用户名" required>
    </div>
  </div>
  <div class="form-group">
    <label for="message_tel" class="col-sm-2 control-label">接收短信手机号</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="message_tel" name="message_tel" placeholder="请输入值班医生手机号(接收短信)">
    </div>
  </div>
  <div class="form-group">
    <label for="salesman" class="col-sm-2 control-label">业务员</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="salesman" name="salesman" placeholder="请输入业务员姓名">
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
    <label for="comment" class="col-sm-2 control-label">报告底部文字</label>
    <div class="col-sm-10">
      <textarea class="form-control" rows="5" name="comment"></textarea>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-lg btn-success" name="submit">保存</button>
      <button type="button" class="btn btn-lg btn-primary" style="margin-left:50px" 
        onclick="javascript:history.back();">返回</button>
    </div>
  </div>
</form>
<script src="js/proCity.js"></script>
<script src="js/yizhong.js"></script>
<script>
    var proS=document.getElementById("proS"),cityS=document.getElementById("cityS");
    loadProvince();
    loadCity();
</script>
EOF;
}
require 'tpl/footer.tpl';
