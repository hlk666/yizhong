<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';
require '../lib/ShortMessageService.php';

$title = '添加新医院';
$isHideSider = true;
require 'header.php';

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。', 2000, 'add.php');
    }
    
    if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
        echo '请登录后再操作。';
        header('location:index.php');
        exit;
    }
    $creator = $_SESSION['user'];
    $hospitalName = !isset($_POST['hospital_name']) ? null : $_POST['hospital_name'];
    $level = isset($_POST['level']) ? $_POST['level'] : '';
    $hospitalTel = !isset($_POST['hospital_tel']) ? null : $_POST['hospital_tel'];
    $emergencyTel = !isset($_POST['emergency_tel']) ? '' : $_POST['emergency_tel'];
    $province = isset($_POST['province']) ? $_POST['province'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $county = isset($_POST['county']) ? $_POST['county'] : '';
    $hospitalAddress = !isset($_POST['hospital_address']) ? null : $_POST['hospital_address'];
    $adminUser = !isset($_POST['admin']) ? null : $_POST['admin'];
    $password = !isset($_POST['password']) ? null : $_POST['password'];
    $passwordRepeat = !isset($_POST['password_repeat']) ? null : $_POST['password_repeat'];
    $contact = !isset($_POST['contact']) ? null : $_POST['contact'];
    $salesman = (isset($_POST['salesman']) && !empty($_POST['salesman'])) ? $_POST['salesman'] : '0';
    
    $invoiceName = (isset($_POST['invoice_name']) && !empty($_POST['invoice_name'])) ? $_POST['invoice_name'] : '';
    $invoiceId = (isset($_POST['invoice_id']) && !empty($_POST['invoice_id'])) ? $_POST['invoice_id'] : '';
    $invoiceAddressTel = (isset($_POST['invoice_addr_tel']) && !empty($_POST['invoice_addr_tel'])) ? $_POST['invoice_addr_tel'] : '';
    $invoiceBank = (isset($_POST['invoice_bank']) && !empty($_POST['invoice_bank'])) ? $_POST['invoice_bank'] : '';
    
    $titleHospital = (isset($_POST['hospital_title']) && !empty($_POST['hospital_title'])) ? $_POST['hospital_title'] : '';
    $agency = (isset($_POST['agency']) && !empty($_POST['agency'])) ? $_POST['agency'] : '0';
    
    $double = (isset($_POST['double']) && !empty($_POST['double'])) ? $_POST['double'] : '';
    $strDevice = (isset($_POST['device_list']) && !empty($_POST['device_list'])) ? str_replace('，', ',', $_POST['device_list']) : '';
    $deviceList = empty($strDevice) ? array() : explode(',', str_replace('，', ',', $strDevice));
    
    if (empty($hospitalName)) {
        user_back_after_delay('请正确输入医院名。');
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
    if (empty($county)) {
        user_back_after_delay('请选择区县。');
    }
    if (empty($hospitalAddress)) {
        user_back_after_delay('请正确输入医院地址。');
    }
    if (empty($adminUser)) {
        user_back_after_delay('请正确输入初始管理员登录用户。');
    }
    if (empty($password)) {
        user_back_after_delay('请正确输入初始管理员登录密码。');
    }
    if ($password != $passwordRepeat) {
        user_back_after_delay('初始管理员用户的两次密码不一致。');
    }
    foreach ($deviceList as $deviceId) {
        $isExisted = DbiAdmin::getDbi()->existedDevice1($deviceId);
        if (VALUE_DB_ERROR === $isExisted) {
            user_back_after_delay(MESSAGE_DB_ERROR);
        } elseif (true === $isExisted) {
            user_back_after_delay("设备【 $deviceId 】已绑定其他医院。");
        }  else {
            $ret = DbiAdmin::getDbi()->checkDeviceDelivery($deviceId);
            if (VALUE_DB_ERROR === $ret) {
                user_back_after_delay(MESSAGE_DB_ERROR);
            } elseif ($ret == 1) {
                user_back_after_delay("设备【 $deviceId 】的ID不存在。");
            }  elseif ($ret == 2) {
                user_back_after_delay("设备【 $deviceId 】在生产部。");
            }  else {
                continue;
            }
        }
    }
    
    if (empty($titleHospital)) {
        $analysisHospital = 0;
        $reportHospital = 0;
    } else {
        if ($titleHospital == 313) {
            $analysisHospital = 417;
            $reportHospital = 313;
        } else {
            $analysisHospital = 119;
            $reportHospital = 119;
        }
    }
    
    $isExisted = DbiAdmin::getDbi()->existedLoginName($adminUser, 0);
    if (VALUE_DB_ERROR === $isExisted) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    if (true === $isExisted) {
        user_back_after_delay("登录用户名<font color='red'>$adminUser</font>已被他人使用。");
    }
    
    $ret = DbiAdmin::getDbi()->addHospital($hospitalName, '0', $level, $hospitalTel, 
            $province, $city, $county, $hospitalAddress, '0', '0', $adminUser, '', 
            $salesman, '', $analysisHospital, $reportHospital, $titleHospital, $agency, 
            '0', '2', '0', '0', $invoiceName, $invoiceId, $invoiceAddressTel, $invoiceBank, 
            $creator, $double, $deviceList, $contact, $password, $emergencyTel);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    ShortMessageService::send('13465596133', '有新的医院。');
    echo '<h1>添加成功。</h1><a href="add.php">继续新建医院</a>';
} else {
    $_SESSION['post'] = false;
    
    $ret = DbiAdmin::getDbi()->getAgencyList();
    if (VALUE_DB_ERROR === $ret) {
        $ret = array();
    }
    $htmlAgency = '<option value="0">请选择代理商</option>';
    foreach ($ret as $value) {
        $htmlAgency .= '<option value="' . $value['agency_id'] . '">' . $value['name'] . '(' . $value['agency_tel'] . ')</option>';
    }
    $ret = DbiAdmin::getDbi()->getSalesmanList();
    if (VALUE_DB_ERROR === $ret) {
        $ret = array();
    }
    $htmlSalesman = '<option value="0">请选择业务员</option>';
    foreach ($ret as $value) {
        $htmlSalesman .= '<option value="' . $value['salesman_id'] . '">' . $value['name'] . '</option>';
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
    <label for="level" class="col-sm-2 control-label">医院级别<font color="red">*</font></label>
    <div class="col-sm-10">
      <select class="form-control" name="level">
        <option value="0">请选择级别</option>
        <option value="3">三级</option>
        <option value="2">二级</option>
        <option value="1">一级</option>
    </select></div>
  </div>
  <div class="form-group">
    <label for="hospital_tel" class="col-sm-2 control-label">电话<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="hospital_tel" name="hospital_tel" placeholder="请输入医院的联系电话" required>
    </div>
  </div>
  <div class="form-group">
    <label for="emergency_tel" class="col-sm-2 control-label"><font color="red">报警联系(值班)电话</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="emergency_tel" name="emergency_tel" placeholder="请输入医院的值班电话">
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_address" class="col-sm-2 control-label">地址<font color="red">*</font></label>
    <div class="col-sm-2">
      <select class="form-control" name="province" id="proS" onchange="loadCity()"><option value="0">请选择</option></select>
    </div>
    <div class="col-sm-2">
      <select class="form-control" name="city" id="cityS" onchange="loadCounty()"><option value="0">请选择</option></select>
    </div>
    <div class="col-sm-2">
      <select class="form-control" name="county" id="countyS"><option value="0">请选择</option></select>
    </div>
    <div class="col-sm-4">
      <input type="text" class="form-control" id="hospital_address" name="hospital_address" placeholder="请输入医院的地址" required>
    </div>
  </div>
  <div class="form-group">
    <label for="admin" class="col-sm-2 control-label">管理员登录账号<font color="red">*</font></label>
    <div class="col-sm-4">
      <input type="text" class="form-control" id="admin" name="admin" placeholder="请输入管理员登录用户名" required onchange="checkUser(this.value)">
    </div>
    <label class="col-sm-6 control-label" id="check_user" style="text-align:left;color:red;"></label>
  </div>
  
  <div class="form-group">
    <label class="col-sm-2 control-label">管理员登录密码<font color="red">*</font></label>
    <div class="col-sm-4">
      <input type="password" class="form-control" name="password" id="password" placeholder="管理员密码" onchange="checkRepeatPwd()" required>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">确认密码<font color="red">*</font></label>
    <div class="col-sm-4">
      <input type="password" class="form-control" name="password_repeat" id="password_repeat" placeholder="管理员密码" onchange="checkRepeatPwd()" required>
    </div>
    <label class="col-sm-6 control-label" id="check_pwd" style="text-align:left;color:red;"></label>
  </div>
  
  <div class="form-group">
    <label for="admin" class="col-sm-2 control-label">联系人<font color="red">*</font></label>
    <div class="col-sm-4">
      <input type="text" class="form-control" name="contact" required">
    </div>
    <label class="col-sm-6 control-label" id="check_user" style="text-align:left;">例：王主任，张医生</label>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">审核医院(本院请填0)<font color="red">*</font></label>
    <div class="col-sm-4">
      <input type="text" class="form-control" name="hospital_title" placeholder="无需审核(本院直接出报告)请填0" onchange="getHosName(this.value)" required>
    </div>
    <label class="col-sm-6 control-label" id="title" style="text-align:left;color:red;"></label>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">是否双抬头</label>
    <div class="col-sm-4">
      <label class="checkbox-inline">
      <input type="radio" name="double" value="1">是</label>
      <label class="checkbox-inline">
      <input type="radio" name="double" value="0" checked>否</label>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">代理商</label>
    <div class="col-sm-10">
      <select class="form-control" name="agency">$htmlAgency</select>
    </div>
  </div>
  <div class="form-group">
    <label for="salesman" class="col-sm-2 control-label">业务员</label>
    <div class="col-sm-10">
      <select class="form-control" name="salesman">$htmlSalesman</select>
    </div>
  </div>
  <div class="form-group">
    <label for="device_list" class="col-sm-2 control-label">设备ID(多个设备用英文逗号分隔)</label>
    <div class="col-sm-4">
      <input type="text" class="form-control" name="device_list" placeholder="请输入设备ID(多个设备用英文逗号分隔)" onchange="checkDevice(this.value)">
    </div>
    <label class="col-sm-6 control-label" id="device" style="text-align:left;color:red;"></label>
  </div>
  <!--
  <div class="form-group">
    <label class="col-sm-2 control-label">发票名称</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="invoice_name" placeholder="请输入发票名称">
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">纳税人识别号</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="invoice_id" placeholder="请输入纳税人识别号">
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">(发票)地址电话</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="invoice_addr_tel" placeholder="请输入(发票)地址电话">
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">开户行及账号</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="invoice_bank" placeholder="请输入开户行及账号">
    </div>
  </div>
  -->
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10" style="text-align:center;">
      <button type="submit" class="btn btn-lg btn-success" name="submit">保存</button>
    </div>
  </div>
</form>
<script src="js/proCityCountry.js"></script>
<script src="js/yizhong.js"></script>
<script>
    var proS=document.getElementById("proS"),cityS=document.getElementById("cityS"),countyS=document.getElementById("countyS");
    loadProvince();
    loadCity();
    loadCounty();
    function checkRepeatPwd()
    {
      if ($("#password").val() == "") {
          return;
      }
      if ($("#password").val() != $("#password_repeat").val()) {
          $("#check_pwd").html("两次输入的密码不一致");
      } else {
          $("#check_pwd").html("");
      }
    }
</script>
EOF;
}
require 'tpl/footer.tpl';
