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
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $level = isset($_POST['level']) ? $_POST['level'] : '';
    $hospitalTel = !isset($_POST['hospital_tel']) ? null : $_POST['hospital_tel'];
    $province = isset($_POST['province']) ? $_POST['province'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $hospitalAddress = !isset($_POST['hospital_address']) ? null : $_POST['hospital_address'];
    $parentFlag = !isset($_POST['parent_flag']) ? null : $_POST['parent_flag'];
    $loginUser = !isset($_POST['login_user']) ? null : $_POST['login_user'];
    $messageTel = isset($_POST['message_tel']) ? $_POST['message_tel'] : '0';
    $agency = isset($_POST['agency']) ? $_POST['agency'] : '';
    $salesman = isset($_POST['salesman']) ? $_POST['salesman'] : '';
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
    $contractFlag = isset($_POST['contract_flag']) ? $_POST['contract_flag'] : '0';
    $deviceSale = isset($_POST['device_sale']) ? $_POST['device_sale'] : '0';
    $displayCheck = isset($_POST['display_check']) ? $_POST['display_check'] : '0';
    
    if (empty($hospitalId)) {
        user_back_after_delay('非法访问');
    }
    
    if (isset($_POST['edit'])) {
        if (empty($hospitalName)) {
            user_back_after_delay('请正确输入医院名。');
        }
        if (empty($type)) {
            user_back_after_delay('请选择类型。');
        }
        if (empty($level)) {
            user_back_after_delay('请选择级别。');
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
        if (null === $parentFlag) {
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

        $ret = DbiAdmin::getDbi()->editHospital($hospitalId, $hospitalName, $type, $level, $hospitalTel, $province, $city, 
                $hospitalAddress, $parentFlag, $loginUser, $messageTel, $agency, $salesman, $comment, 
                $contractFlag, $deviceSale, $displayCheck);
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
    
    $type = $hospitalInfo['type'];
    $typeSelected = '<option value="0">请选择</option> 
        <option value="1"' . ($type == '1' ? ' selected ' : '') . '>云平台</option>
        <option value="2"' . ($type == '2' ? ' selected ' : '') . '>分析中心</option>
        <option value="3"' . ($type == '3' ? ' selected ' : '') . '>下级医院</option>
        <option value="4"' . ($type == '4' ? ' selected ' : '') . '>独立医院</option>';
    $level = $hospitalInfo['level'];
    $levelSelected = '<option value="0">请选择</option>  
        <option value="3"' . ($level == '3' ? ' selected ' : '') . '>三级</option>
        <option value="2"' . ($level == '2' ? ' selected ' : '') . '>二级</option>
        <option value="1"' . ($level == '1' ? ' selected ' : '') . '>一级</option>';
    $hospitalName = $hospitalInfo['hospital_name'];
    $province = $hospitalInfo['province'];
    $city = $hospitalInfo['city'];
    $address = $hospitalInfo['address'];
    $tel = $hospitalInfo['tel'];
    $loginUser = $hospitalInfo['login_name'];
    $messageTel = $hospitalInfo['sms_tel'];
    $agency = $hospitalInfo['agency'];
    $salesman = $hospitalInfo['salesman'];
    $comment = $hospitalInfo['comment'];
    
    if ('1' == $hospitalInfo['parent_flag']) {
        $htmlParentFlagYes = '<input type="radio" name="parent_flag" value="1" checked>可</label>';
        $htmlParentFlagNo = '<input type="radio" name="parent_flag" value="0">否</label>';
    } else {
        $htmlParentFlagYes = '<input type="radio" name="parent_flag" value="1">可</label>';
        $htmlParentFlagNo = '<input type="radio" name="parent_flag" value="0" checked>否</label>';
    }
    
    if ('1' == $hospitalInfo['contract_flag']) {
        $htmlContract = '<div class="col-sm-10">
            <label class="checkbox-inline"><input type="radio" name="contract_flag" value="1" checked>已签</label>
            <label class="checkbox-inline"><input type="radio" name="contract_flag" value="0">未签</label></div>';
    } else {
        $htmlContract = '<div class="col-sm-10">
            <label class="checkbox-inline"><input type="radio" name="contract_flag" value="1">已签</label>
            <label class="checkbox-inline"><input type="radio" name="contract_flag" value="0" checked>未签</label></div>';
    }
    if ('1' == $hospitalInfo['device_sale']) {
        $htmlDeviceSale = '<div class="col-sm-10">
            <label class="checkbox-inline"><input type="radio" name="device_sale" value="1" checked>出售</label>
            <label class="checkbox-inline"><input type="radio" name="device_sale" value="0">免费</label></div>';
    } else {
        $htmlDeviceSale = '<div class="col-sm-10">
            <label class="checkbox-inline"><input type="radio" name="device_sale" value="1">出售</label>
            <label class="checkbox-inline"><input type="radio" name="device_sale" value="0" checked>免费</label></div>';
    }
    if ('1' == $hospitalInfo['display_check']) {
        $htmlDisplayCheck = '<div class="col-sm-10">
            <label class="checkbox-inline"><input type="radio" name="display_check" value="1" checked>是</label>
            <label class="checkbox-inline"><input type="radio" name="display_check" value="0">否</label></div>';
    } else {
        $htmlDisplayCheck = '<div class="col-sm-10">
            <label class="checkbox-inline"><input type="radio" name="display_check" value="1">是</label>
            <label class="checkbox-inline"><input type="radio" name="display_check" value="0" checked>否</label></div>';
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
    <label for="type" class="col-sm-2 control-label">定位/类型<font color="red">*</font></label>
    <div class="col-sm-10">
      <select class="form-control" name="type">
        $typeSelected
    </select></div>
  </div>
  <div class="form-group">
    <label for="level" class="col-sm-2 control-label">医院级别<font color="red">*</font></label>
    <div class="col-sm-10">
      <select class="form-control" name="level">
        $levelSelected
    </select></div>
  </div>
  <div class="form-group">
    <label for="hospital_tel" class="col-sm-2 control-label">电话<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="hospital_tel" name="hospital_tel" value="$tel">
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
      <input type="text" class="form-control" id="hospital_address1" name="hospital_address" value="$address">
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
    <label class="col-sm-2 control-label">是否已签合同<font color="red">*</font></label>
    $htmlContract
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">设备投放类型<font color="red">*</font></label>
    $htmlDeviceSale
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">是否显示审阅医生<font color="red">*</font></label>
    $htmlDisplayCheck
  </div>
  <div class="form-group">
    <label for="login_user" class="col-sm-2 control-label">管理员登录用户<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="login_user" name="login_user" value="$loginUser">
    </div>
  </div>
  <div class="form-group">
    <label for="message_tel" class="col-sm-2 control-label">接收短信手机号<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="message_tel" name="message_tel" value="$messageTel">
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">代理商<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="agency" value="$agency">
    </div>
  </div>
  <div class="form-group">
    <label for="salesman" class="col-sm-2 control-label">业务员<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="salesman" name="salesman" value="$salesman">
    </div>
  </div>
  <div class="form-group">
    <label for="comment" class="col-sm-2 control-label">报告底部文字</label>
    <div class="col-sm-10">
      <textarea class="form-control" rows="3" name="comment">$comment</textarea>
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
<script src="js/proCity.js"></script>
<script src="js/yizhong.js"></script>
<script>
    var proS=document.getElementById("proS"),cityS=document.getElementById("cityS");
    loadProvince($province);
    loadCity($city);
</script>
EOF;
}

require 'tpl/footer.tpl';
