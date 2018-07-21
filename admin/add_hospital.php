<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';
require '../lib/ShortMessageService.php';

$title = '添加新的医院';
require 'header.php';

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。', 2000, 'add_hospital.php');
    }
    
    $hospitalName = !isset($_POST['hospital_name']) ? null : $_POST['hospital_name'];
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $level = isset($_POST['level']) ? $_POST['level'] : '';
    $hospitalTel = !isset($_POST['hospital_tel']) ? null : $_POST['hospital_tel'];
    $province = isset($_POST['province']) ? $_POST['province'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $county = isset($_POST['county']) ? $_POST['county'] : '';
    $hospitalAddress = !isset($_POST['hospital_address']) ? null : $_POST['hospital_address'];
    $parentFlag = !isset($_POST['parent_flag']) ? null : $_POST['parent_flag'];
    $hospitalParent = !isset($_POST['hospital_parent']) ? null : $_POST['hospital_parent'];
    $adminUser = !isset($_POST['admin']) ? null : $_POST['admin'];
    $messageTel = (isset($_POST['message_tel']) && !empty($_POST['message_tel'])) ? $_POST['message_tel'] : '0';
    $salesman = (isset($_POST['salesman']) && !empty($_POST['salesman'])) ? $_POST['salesman'] : '0';
    
    $invoiceName = (isset($_POST['invoice_name']) && !empty($_POST['invoice_name'])) ? $_POST['invoice_name'] : '';
    $invoiceId = (isset($_POST['invoice_id']) && !empty($_POST['invoice_id'])) ? $_POST['invoice_id'] : '';
    $invoiceAddressTel = (isset($_POST['invoice_addr_tel']) && !empty($_POST['invoice_addr_tel'])) ? $_POST['invoice_addr_tel'] : '';
    $invoiceBank = (isset($_POST['invoice_bank']) && !empty($_POST['invoice_bank'])) ? $_POST['invoice_bank'] : '';
    
    $comment = (isset($_POST['comment']) && !empty($_POST['comment'])) ? $_POST['comment'] : '';
    $analysisHospital = (isset($_POST['hospital_analysis']) && !empty($_POST['hospital_analysis'])) ? $_POST['hospital_analysis'] : '';
    $reportHospital = (isset($_POST['hospital_report']) && !empty($_POST['hospital_report'])) ? $_POST['hospital_report'] : '';
    $titleHospital = (isset($_POST['hospital_title']) && !empty($_POST['hospital_title'])) ? $_POST['hospital_title'] : '';
    $agency = (isset($_POST['agency']) && !empty($_POST['agency'])) ? $_POST['agency'] : '0';
    $contractFlag = isset($_POST['contract_flag']) ? $_POST['contract_flag'] : '0';
    $deviceSale = '2';//isset($_POST['device_sale']) ? $_POST['device_sale'] : '2';
    $displayCheck = isset($_POST['display_check']) ? $_POST['display_check'] : '0';
    $reportMustCheck = isset($_POST['report_must_check']) ? $_POST['report_must_check'] : '0';
    
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
    if (empty($adminUser)) {
        user_back_after_delay('请正确输入初始管理员登录用户。');
    }
    if (empty($analysisHospital) && empty($reportHospital) && empty($titleHospital)) {
        //no hospital => OK.
    } elseif (!empty($analysisHospital) && !empty($reportHospital) && !empty($titleHospital)) {
        //all hospitals => OK.
    } else {
        user_back_after_delay('请同时设置分析、出报告和抬头医院。');
    }
    
    $isExisted = DbiAdmin::getDbi()->existedLoginName($adminUser, 0);
    if (VALUE_DB_ERROR === $isExisted) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    if (true === $isExisted) {
        user_back_after_delay("登录用户名<font color='red'>$adminUser</font>已被他人使用。");
    }
    
    $ret = DbiAdmin::getDbi()->addHospital($hospitalName, $type, $level, $hospitalTel, 
            $province, $city, $county, $hospitalAddress, $parentFlag, $hospitalParent, $adminUser, 
            $messageTel, $salesman, $comment, $analysisHospital, $reportHospital, $titleHospital, 
            $agency, $contractFlag, $deviceSale, $displayCheck, $reportMustCheck, 
            $invoiceName, $invoiceId, $invoiceAddressTel, $invoiceBank, $_SESSION['user']);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    ShortMessageService::send('13465596133', '有新的医院。');
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
    
    $ret = DbiAdmin::getDbi()->getHospitalListHigh(0);
    if (VALUE_DB_ERROR === $ret) {
        $ret = array();
    }
    
    $analysisHospital = '0';
    $reportHospital = '0';
    $titleHospital = '0';
    
    $htmlAnalysisHospitals = '';
    foreach ($ret as $value) {
        if ($value['hospital_id'] == $analysisHospital) {
            $htmlAnalysisHospitals .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
        } else {
            $htmlAnalysisHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
        }
    }
    
    $htmlReportHospitals = '';
    foreach ($ret as $value) {
        if ($value['hospital_id'] == $reportHospital) {
            $htmlReportHospitals .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
        } else {
            $htmlReportHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
        }
    }
    
    $htmlTitleHospitals = '';
    foreach ($ret as $value) {
        if ($value['hospital_id'] == $titleHospital) {
            $htmlTitleHospitals .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
        } else {
            $htmlTitleHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
        }
    }
    
    $ret = DbiAdmin::getDbi()->getAgencyList();
    if (VALUE_DB_ERROR === $ret) {
        $ret = array();
    }
    $htmlAgency = '<option value="0">请选择代理商</option>';
    foreach ($ret as $value) {
        $htmlAgency .= '<option value="' . $value['agency_id'] . '">' . $value['name'] . '</option>';
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
    <label for="type" class="col-sm-2 control-label">定位/类型<font color="red">*</font></label>
    <div class="col-sm-10">
      <select class="form-control" name="type">
        <option value="0">请选择类型</option>
        <option value="1">云平台</option>
        <option value="2">分析中心</option>
        <option value="3">下级医院</option>
        <option value="4">独立医院</option>
    </select></div>
  </div>
  <div class="form-group">
    <label for="level" class="col-sm-2 control-label">医院级别<font color="red">*</font></label>
    <div class="col-sm-10">
      <select class="form-control" name="level">
        <option value="0">请选择级别</option>
        <option value="3">三级</option>
        <option value="2">二级</option>
        <option value="1">一级</option>
        <option value="99">零级</option>
    </select></div>
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
  <div class="form-group">
    <label for="parent_flag" class="col-sm-2 control-label">可否作为上级医院<font color="red">*</font></label>
    <div class="col-sm-2">
      <label class="checkbox-inline">
      <input type="radio" name="parent_flag" value="1">可</label>
      <label class="checkbox-inline">
      <input type="radio" name="parent_flag" value="0" checked>否</label>
    </div>
    <label for="hospital_parent" class="col-sm-2 control-label">本院的上级医院</label>
    <div class="col-sm-6">
      <select class="form-control" name="hospital_parent">
        <option value="0">请选择上级医院(非必须)</option>$htmlParentHospitals
    </select></div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">是否已签合同</label>
    <div class="col-sm-4">
      <label class="checkbox-inline">
      <input type="radio" name="contract_flag" value="1">已签</label>
      <label class="checkbox-inline">
      <input type="radio" name="contract_flag" value="0" checked>未签</label>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">是否显示审阅医生</label>
    <div class="col-sm-4">
      <label class="checkbox-inline">
      <input type="radio" name="display_check" value="1">是</label>
      <label class="checkbox-inline">
      <input type="radio" name="display_check" value="0" checked>否</label>
    </div>
    <label class="col-sm-2 control-label">是否报告必须审阅</label>
    <div class="col-sm-4">
      <label class="checkbox-inline">
      <input type="radio" name="report_must_check" value="1">是</label>
      <label class="checkbox-inline">
      <input type="radio" name="report_must_check" value="0" checked>否</label>
    </div>
  </div>
  <div class="form-group">
    <label for="comment" class="col-sm-2 control-label">报告底部文字</label>
    <div class="col-sm-10">
      <textarea class="form-control" rows="3" name="comment"></textarea>
    </div>
  </div>
  <hr style="border-top:1px ridge red;" />
  <div style="margin-top:10px;margin-bottom:10px;font-size:x-large;text-align:center;"><h2>长程分析(本院则无需配置)</h2></div>
  <div class="form-group">
    <label for="hospital_analysis" class="col-sm-2 control-label">分析医院</label>
    <div class="col-sm-10">
      <select class="form-control" name="hospital_analysis">
        <option value="0">请选择分析医院</option>$htmlAnalysisHospitals
    </select></div>
  </div>
  <div class="form-group">
    <label for="hospital_report" class="col-sm-2 control-label">出报告医院</label>
    <div class="col-sm-10">
      <select class="form-control" name="hospital_report">
        <option value="0">请选择出报告医院</option>$htmlReportHospitals
    </select></div>
  </div>
  <div class="form-group">
    <label for="hospital_title" class="col-sm-2 control-label">抬头医院</label>
    <div class="col-sm-10">
      <select class="form-control" name="hospital_title">
        <option value="0">请选择抬头医院</option>$htmlTitleHospitals
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
<script src="js/proCityCountry.js"></script>
<script src="js/yizhong.js"></script>
<script>
    var proS=document.getElementById("proS"),cityS=document.getElementById("cityS"),countyS=document.getElementById("countyS");
    loadProvince();
    loadCity();
    loadCounty();
</script>
EOF;
}
require 'tpl/footer.tpl';
