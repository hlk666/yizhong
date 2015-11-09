﻿<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'function.php';
require PATH_LIB . 'Invigilator.php';

session_start();
checkDoctorLogin();

$registHospital = $_SESSION["hospital"];
$doctorId = $_SESSION["loginId"]
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>用户注册</title>
<style type="text/css">
<!--
.STYLE1 {
    font-family: "新宋体";
    font-weight: bold;
    font-size: 12px;
}
.STYLE2 {
    color: #EE0000;
    font-size: 12px;
}
.STYLE3 {
    border: 1px solid #808080;
    background-color: #B0E2FF;
}
.STYLE4 {font-size: 12px}
.STYLE6 {font-size: 12px; font-weight: bold; }
-->
</style>
</head>
<body>
<script language="javascript">
function CheckPost() {
    if (myform.device.value == "") {
        alert("设备编号不能为空！");
        myform.device.focus();
        return false;
    }
    if (myform.name.value == "") {
        alert("姓名不能为空！");
        myform.name.focus();
        return false;
    }
    var set =/[^\u4e00-\u9fa5]/;
    if (set.test(myform.name.value)) {
        alert("请输入中文姓名！");
        myform.name.focus();
        return false;
    }
    if (myform.name.value.length > 50) {
        alert("输入的姓名过长");
        myform.name.focus();
        return false;
    }
    if (myform.tel.value == "") {
        alert("电话号码不能为空！");
        myform.tel.focus();
        return false;
    }
    if (myform.lead.value == "0") {
        alert("胸导联未选择！");
        myform.lead.focus();
        return false;
    }    
    if (myform.tentative_diagnose.value == "") {
        alert("请填写患者症状！");
        myform.tentative_diagnose.focus();
        return false;
    }    
    if (myform.medical_history.value == "") {
        alert("请填写病史！");
        myform.medical_history.focus();
        return false;
    }    
    if (myform.doctor.value == "") {
        alert("登记医生不能为空！");
        myform.doctor.focus();
        return false;
    }
    if (myform.age.value == "") {
        alert("年龄不能为空！");
        myform.age.focus();
        return false;
    }
    if (myform.hours.value == "" && myform.mode.value != "3") {
        alert("监护时长不能为空！");
        myform.hours.focus();
        return false;
    }
}
</script>
<?php
  if(isset($_POST['add']) && $_POST['add']){
    $_SESSION['guardian'] = $_POST;
    $name = $_POST["name"];
    $age = $_POST["age"];
    $sex = $_POST["sex"];
    $tel = $_POST["tel"];
    $height = $_POST["height"];
    $weight = $_POST["weight"];
    $bloodPressure = $_POST["blood_pressure"];
    $lead = $_POST["lead"];
    $sickRoom = $_POST["sickroom"];
    $familyName = $_POST["family_name"];
    $familyTel = $_POST["family_tel"];
    $tentativeDiagnose = $_POST['tentative_diagnose'];
    $medicalHistory = $_POST["medical_history"];
    $guardHospital = $_POST["guard_hospital"];
    $registDoctorName = $_POST["doctor"];
    $device = $_POST['device'];
    $mode = $_POST['mode'];
    $hours = $_POST['hours'];
    
    $guardian = Dbi::getDbi()->getGuardianStatusByDevice($device);
    if (!empty($guardian)) {
        $ret = Dbi::getDbi()->getPatient($guardian['patient_id']);
        if (empty($ret)) {
            $otherPatient = '其他用户(id:' . $guardian['patient_id'] . ')';
        } else {
            $otherPatient = $ret['patient_name'];
        }
        if (0 == $guardian['status']) {
            echo "<script language=javascript>alert(\"'$otherPatient'已注册该设备，请待该用户监护结束后使用此设备！\");history.back();</script>";
            exit;
        }
        if (1 == $guardian['status']) {
            echo "<script language=javascript>alert(\"'$otherPatient'正在使用该设备，请从监护列表结束其监护！\");history.back();</script>";
            exit;
        }
    }
    $guardianId = Dbi::getDbi()->registUser($name, $sex, $age, $tel, $device, $registHospital, $guardHospital, 
            $mode, $hours, $lead, $doctorId, $sickRoom, $bloodPressure, $height, $weight, $familyName, 
            $familyTel, $tentativeDiagnose, $medicalHistory, $registDoctorName);
    if (VALUE_DB_ERROR === $guardianId) {
        echo "<script language=javascript>alert(\"用户注册失败，请重试或联系系统管理员。\");history.back();</script>";
        exit;
    }
    $invigilator = new Invigilator($guardianId, $mode, $hours);
    //@todo add other params here.
    $command = array();
    $invigilator->create($command);
    unset($_SESSION['guardian']);
    echo "<script language='javascript'> alert('用户添加成功！');window.location.href='patients.php?id= $registHospital'</script>";
}
?>
<script language="javascript" src="../libraries/PCASClass.js"></script>
<table width="100%" height="100%" border="0" align="center" cellspacing="1" bordercolor="#000000">
  <form action="" method="post" id="myform" onsubmit="return CheckPost()">
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">姓名：<span class="STYLE2">*</span></span></td>
    <td width="90"><input name="name" type="text" style="width: 80px" id="name" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['name'] ?>" /></td>
    
    <td width="74"><span class="STYLE4">性别：<span class="STYLE2">*</span></span></td>
    <td width="83">
    <select name="sex" style="width: 80px" id="sex">
      <option value="1" <?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['sex'] == '1') echo 'selected="selected"'?>>男 </option>
      <option value="2"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['sex'] == '2') echo 'selected="selected"'?>>女 </option>
     </select></td>
  </tr>
  
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">年龄(数字)：<span class="STYLE2">*</span></span></td>
    <td><input name="age" type="text" style="width: 80px" id="age" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['age']?>" /> </td>
    
    <td><span class="STYLE4">血压(高压/低压)：</span></td>
    <td><input name="blood_pressure" type="text" style="width: 80px" id="height" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['blood_pressure']?>" /></td>
  </tr>
  
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">身高(cm)：</span></td>
    <td><input name="height" type="text" style="width: 80px" id="height" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['height']?>" /></td>
    <td><span class="STYLE4">体重(kg)：</span></td>
    <td><input name="weight" type="text" style="width: 80px" id="weight" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['weight']?>" /></td>
  </tr>
  
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">联系电话：<span class="STYLE2">*</span></span></td>
    <td><input name="tel" type="text" style="width: 80px" id="tel" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['tel']?>" /></td>
    <td><span class="STYLE4">胸导位置：<span class="STYLE2">*</span></span></td>
    <td><select name="lead" style="width: 80px" id="lead">
      <option value="0"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['lead'] == '0') echo 'selected="selected"'?>></option>
      <option value="1"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['lead'] == '1') echo 'selected="selected"'?>>V1</option>
      <option value="2"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['lead'] == '2') echo 'selected="selected"'?>>V2</option>
      <option value="3"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['lead'] == '3') echo 'selected="selected"'?>>V3</option>
      <option value="4"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['lead'] == '4') echo 'selected="selected"'?>>V4</option>
      <option value="5"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['lead'] == '5') echo 'selected="selected"'?>>V5</option>
      <option value="6"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['lead'] == '6') echo 'selected="selected"'?>>V6</option>
    </select></td>
  </tr>
  
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">病区/住址：</span></td>
    <td colspan="3"><input name="sickroom" type="text" style="width: 220px" id="address" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['sickroom']?>" /></td>
  </tr>
  
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">亲属：</span></td>
    <td><input name="family_name" type="text" style="width: 80px" id="family_name" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['family_name']?>" /></td>
    <td><span class="STYLE4">联系电话：</span></td>
    <td><input name="family_tel" type="text" style="width: 80px" id="family_tel" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['family_tel']?>" /></td>
  </tr>
  
   <tr class="STYLE3">
    <td height="25"><span class="STYLE4">患者症状：<span class="STYLE2">*</span></span></td>
    <td colspan="3"><input name="tentative_diagnose" type="text" style="width: 250px" id="tentative_diagnose" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['tentative_diagnose']?>" /></td>
  </tr>
  
    <tr class="STYLE3">
    <td height="25"><span class="STYLE4">病史：<span class="STYLE2">*</span></span></td>
    <td colspan="3"><input name="medical_history" type="text" style="width: 250px" id="medical_history" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['medical_history']?>" /></td>
  </tr>
  
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">监护医院：<span class="STYLE2">*</span></span></td>
    <td colspan="3"><select name="guard_hospital" style="width: 252px" id="guard_hospital">
    <option value="<?php echo $registHospital;?>"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['guard_hospital'] == $registHospital) echo 'selected="selected"'?>>本院</option>
    <?php 
    $hospitals = Dbi::getDbi()->getParentHospitals($registHospital);
    foreach ($hospitals as $value) {
        echo '<option value="' . $value['hospital_id'] . '" ';
        if (isset($_SESSION['guardian']) && $_SESSION['guardian']['guard_hospital'] == $value['hospital_id']) {
            echo 'selected="selected" ';
        }
        echo '>' . $value['hospital_name'] . '</option>';
    }
    ?>
    </select></td>
  </tr>
  
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">设备编号：<span class="STYLE2">*</span></span></td>
    <td><input name="device" type="text" style="width: 80px" id="device" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['device']?>" /></td>
    <td><span class="STYLE4">开单医生：<span class="STYLE2">*</span></span></td>
    <td><input name="doctor" type="text" style="width: 80px" id="doctor" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['doctor']?>" /></td>
  </tr>
  
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">监护模式：<span class="STYLE2">*</span></span></td>
    <td width="83">
    <select name="mode" style="width: 80px" id="mode">
      <option value="1" <?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['mode'] != '1') {echo '';} else {echo 'selected="selected"';}?>>实时监护模式 </option>
      <option value="2"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['mode'] == '2') echo 'selected="selected"'?>>异常监护模式 </option>
      <option value="3"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['mode'] == '3') echo 'selected="selected"'?>>单次测量模式 </option>
     </select></td>
    
    <td><span class="STYLE4">监护时长(小时。单次测量模式可不输入)：<span class="STYLE2">*</span></span></td>
    <td><input name="hours" type="text" style="width: 80px" id=""hours"" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['hours']?>" /></td>
  </tr>
  
   <tr class="STYLE3">
    <td height="30" colspan="4"><div align="center">
        <input name="add" type="submit" value="注册">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="reset" type="reset" value="清空">
    </div></td>
    </tr>
    </form> 
</table>
</body>
</html>
