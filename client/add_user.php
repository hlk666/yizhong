<?php
require '../common.php';
require PATH_LIB . 'Invigilator.php';
include_head('用户注册');

session_start();
checkDoctorLogin();

$registHospital = $_SESSION["hospital"];
$doctorId = $_SESSION["loginId"]
?>
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
<body>
<?php
if (isset($_POST['type']) && $_POST['type'] == 'save') {
    unset($_SESSION['param']);
    $_SESSION['param'] = $_POST;
}
if (isset($_POST['type']) && $_POST['type'] == 'regist'){
    unset($_SESSION['guardian']);
    $_SESSION['guardian'] = $_POST;
    $name = $_POST["name"];
    $age = $_POST["age"];
    $sex = $_POST["sex"];
    $tel = $_POST["tel"];
    $height = $_POST["height"];
    $weight = $_POST["weight"];
    $bloodPressure = $_POST["blood_pressure"];
    $sickRoom = $_POST["sickroom"];
    $familyTel = $_POST["family_tel"];
    $tentativeDiagnose = $_POST['tentative_diagnose'];
    $medicalHistory = $_POST["medical_history"];
    $guardHospital = $_POST["guard_hospital"];
    $registDoctorName = $_POST["doctor"];
    $device = $_POST['device'];
    $mode = $_POST['mode'];
    $hours = $_POST['hours'];
    
    //common param
    $polycardia = $_SESSION['param']['polycardia'];
    $bradycardia = $_SESSION['param']['bradycardia'];
    $lead = $_SESSION['param']['lead'];
    //special param
    $mode3_record_time = $_SESSION['param']['mode3_record_time'];
    $mode2_record_time = $_SESSION['param']['mode2_record_time'];
    $regular_time = $_SESSION['param']['regular_time'];
    $premature_beat = $_SESSION['param']['premature_beat'];
    $arrhythmia = $_SESSION['param']['arrhythmia'];
    
    $guardian = Dbi::getDbi()->getGuardianByDevice($device);
    if (!empty($guardian)) {
        $ret = Dbi::getDbi()->getPatient($guardian['patient_id']);
        if (empty($ret)) {
            $otherPatient = '其他用户(id:' . $guardian['patient_id'] . ')';
        } else {
            $otherPatient = $ret['patient_name'];
        }
        if (0 == $guardian['status']) {
            user_goto($otherPatient . '已注册该设备，请待该用户监护结束后使用此设备。', GOTO_FLAG_URL, 'add_user.php');
        }
        if (1 == $guardian['status']) {
            user_goto($otherPatient . '正在使用该设备，请待该用户监护结束后使用此设备。', GOTO_FLAG_URL, 'add_user.php');
        }
    }
    $guardianId = Dbi::getDbi()->flowGuardianAddUser($name, $sex, $age, $tel, $device, $registHospital, $guardHospital, 
            $mode, $hours, $lead, $doctorId, $sickRoom, $bloodPressure, $height, $weight, 
            $familyTel, $tentativeDiagnose, $medicalHistory, $registDoctorName);
    if (VALUE_DB_ERROR === $guardianId) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
    }
    $invigilator = new Invigilator($guardianId, $mode, $hours);
    $param = array();
    if ($mode == 1) {
        $param['mode1_polycardia'] = $polycardia;
        $param['mode1_bradycardia'] = $bradycardia;
        $param['mode1_lead'] = $lead;
    }
    if ($mode == 2) {
        $param['mode2_record_time'] = $mode2_record_time;
        $param['mode2_polycardia'] = $polycardia;
        $param['mode2_bradycardia'] = $bradycardia;
        $param['mode2_lead'] = $lead;
        $param['mode2_regular_time'] = $regular_time;
        $param['mode2_premature_beat'] = $premature_beat;
        $param['mode2_arrhythmia'] = $arrhythmia;
    }
    if ($mode == 3) {
        $param['mode3_polycardia'] = $polycardia;
        $param['mode3_bradycardia'] = $bradycardia;
        $param['mode3_lead'] = $lead;
        $param['mode3_record_time'] = $mode3_record_time;
    }
    $invigilator->create($param);
    unset($_SESSION['guardian']);
    unset($_SESSION['param']);
    user_goto(MESSAGE_SUCCESS, GOTO_FLAG_URL, 'patients.php?id=' . $registHospital);
}
?>
<table width="100%" height="100%" border="0" align="center" cellspacing="1" bordercolor="#000000">
  <form action="" method="post" id="formAddUser" onsubmit="return false;">
  <input type="hidden" name="type" value="regist" />
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
    <td height="25"><span class="STYLE4">联系电话：<span class="STYLE2">*</span></span></td>
    <td><input name="tel" type="text" style="width: 80px" id="tel" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['tel']?>" /></td>
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
    $hospitals = Dbi::getDbi()->getHospitalParent($registHospital);
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
    <td><span class="STYLE4">监护时长：<span class="STYLE2">*</span></span></td>
    <td>
    <select name="hours" style="width: 80px" id="hours">
      <option value="24" <?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['hours'] != '24') {echo '';} else {echo 'selected="selected"';}?>>24小时</option>
      <option value="48"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['hours'] == '48') echo 'selected="selected"'?>>48小时</option>
      <option value="0"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['hours'] == '0') echo 'selected="selected"'?>>单次无时长 </option>
     </select>
    </td>
  </tr>
   <tr class="STYLE3">
    <td height="25"><span class="STYLE4">身高(cm)：</span></td>
    <td><input name="height" type="text" style="width: 80px" id="height" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['height']?>" /></td>
    <td><span class="STYLE4">体重(kg)：</span></td>
    <td><input name="weight" type="text" style="width: 80px" id="weight" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['weight']?>" /></td>
  </tr>
  <tr class="STYLE3">
    <td><span class="STYLE4">血压(120/80)：</span></td>
    <td><input name="blood_pressure" type="text" style="width: 80px" id="height" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['blood_pressure']?>" /></td>
    <td><span class="STYLE4">亲属电话：</span></td>
    <td><input name="family_tel" type="text" style="width: 80px" id="family_tel" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['family_tel']?>" /></td>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">病区/住址：</span></td>
    <td colspan="3"><input name="sickroom" type="text" style="width: 250px" id="address" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['sickroom']?>" /></td>
  </tr>
   <tr class="STYLE3">
    <td height="30" colspan="4"><div align="center">
        <input name="param" type="submit" value="设置监护参数" onclick="params()" />
        <input name="add" type="submit" value="注册" onclick="regist()">
        <input name="reset" type="submit" value="清空" onclick="clearAll()">
    </div></td>
    </tr>
    </form> 
</table>
<?php include_js_file();?>
</body>
</html>
