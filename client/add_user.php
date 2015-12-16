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
.STYLE2 {color: #EE0000;}
tr {height:24px;border: 1px solid #FFFFFF;background-color: #B0E2FF;}
td {border: 1px solid #FFFFFF;height:24px;}
</style>
<body style="font-size:12px;">
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
    $polycardia = isset($_SESSION['param']['polycardia']) ? $_SESSION['param']['polycardia'] : PARAM_POLYCARDIA;
    $bradycardia = isset($_SESSION['param']['bradycardia']) ? $_SESSION['param']['bradycardia'] : PARAM_BRADYCARDIA;
    $lead = isset($_SESSION['param']['lead']) ? $_SESSION['param']['lead'] : PARAM_LEAD;
    //special param
    $mode3_record_time = isset($_SESSION['param']['mode3_record_time']) ? $_SESSION['param']['mode3_record_time'] : PARAM_MODE3_RECORD_TIME;
    $mode2_record_time = isset($_SESSION['param']['mode2_record_time']) ? $_SESSION['param']['mode2_record_time'] : PARAM_MODE2_RECORD_TIME;
    $regular_time = isset($_SESSION['param']['regular_time']) ? $_SESSION['param']['regular_time'] : PARAM_REGULAR_TIME;
    $premature_beat = isset($_SESSION['param']['premature_beat']) ? $_SESSION['param']['premature_beat'] : PARAM_PREMATURE_BEAT;
    $combeatrhy = isset($_SESSION['param']['combeatrhy']) ? $_SESSION['param']['combeatrhy'] : PARAM_COMBEATRHY;
    $exminrate = isset($_SESSION['param']['exminrate']) ? $_SESSION['param']['exminrate'] : PARAM_EXMINRATE;
    $stopbeat = isset($_SESSION['param']['stopbeat']) ? $_SESSION['param']['stopbeat'] : PARAM_STOPBEAT;
    $sthigh = isset($_SESSION['param']['sthigh']) ? $_SESSION['param']['sthigh'] : PARAM_STHIGH;
    $stlow = isset($_SESSION['param']['stlow']) ? $_SESSION['param']['stlow'] : PARAM_STLOW;
    $twave = isset($_SESSION['param']['twave']) ? $_SESSION['param']['twave'] : PARAM_TWAVE;
    
    $guardian = Dbi::getDbi()->getGuardianByDevice($device);
    if (VALUE_DB_ERROR === $guardian) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_URL, 'add_user.php');
    }
    if (!empty($guardian)) {
        $patient = Dbi::getDbi()->getPatient($guardian['patient_id']);
        if (VALUE_DB_ERROR === $patient) {
            user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_URL, 'add_user.php');
        }
        if (empty($patient)) {
            $otherPatient = '其他用户(id:' . $guardian['patient_id'] . ')';
        } else {
            $otherPatient = $patient['patient_name'];
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
        $param['mode2_exminrate'] = $exminrate;
        $param['mode2_combeatrhy'] = $combeatrhy;
        $param['mode2_stopbeat'] = $stopbeat;
        $param['mode2_sthigh'] = $sthigh;
        $param['mode2_stlow'] = $stlow;
        $param['mode2_twave'] = $twave;
    }
    if ($mode == 3) {
        $param['mode3_polycardia'] = $polycardia;
        $param['mode3_bradycardia'] = $bradycardia;
        $param['mode3_lead'] = $lead;
        $param['mode3_record_time'] = $mode3_record_time;
    }
    $ret = $invigilator->create($param);
    if (VALUE_PARAM_ERROR === $ret) {
        user_goto(MESSAGE_PARAM, GOTO_FLAG_URL, 'add_user.php');
    }
    if (VALUE_DB_ERROR === $ret) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_URL, 'add_user.php');
    }
    if (VALUE_GT_ERROR === $ret) {
        user_goto('注册成功，但和设备通信失败，请手动操作设备开始监护。', GOTO_FLAG_URL, 'add_user.php');
    }
    unset($_SESSION['guardian']);
    unset($_SESSION['param']);
    user_goto(MESSAGE_SUCCESS, GOTO_FLAG_URL, 'patients.php?id=' . $registHospital);
}
$hospitals = Dbi::getDbi()->getHospitalParent($registHospital);
if (VALUE_DB_ERROR === $hospitals) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_URL, 'add_user.php');
}
?>
<form action="" method="post" id="formAddUser" onsubmit="return false;">
<input type="hidden" name="type" value="regist" />
<table style="width:100%;height:100%;border-collapse:collapse;border-color:#FFFFFF;">
  <tr>
    <td>姓名：<span class="STYLE2">*</span></td>
    <td width="90"><input name="name" type="text" style="width: 80px" id="name" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['name'] ?>" /></td>
    <td width="74">性别：<span class="STYLE2">*</span></td>
    <td width="83">
    <select name="sex" style="width: 80px" id="sex">
      <option value="1" <?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['sex'] == '1') echo 'selected="selected"'?>>男 </option>
      <option value="2" <?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['sex'] == '2') echo 'selected="selected"'?>>女 </option>
     </select></td>
  </tr>
  <tr>
    <td>年龄(岁)：<span class="STYLE2">*</span></td>
    <td><input name="age" type="text" style="width: 80px" id="age" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['age']?>" /> </td>
    <td>联系电话：<span class="STYLE2">*</span></td>
    <td><input name="tel" type="text" style="width: 80px" id="tel" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['tel']?>" /></td>
  </tr>
   <tr>
    <td>患者症状：<span class="STYLE2">*</span></td>
    <td colspan="3"><input name="tentative_diagnose" type="text" style="width: 250px" id="tentative_diagnose" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['tentative_diagnose']?>" /></td>
  </tr>
    <tr>
    <td>病史：<span class="STYLE2">*</span></td>
    <td colspan="3"><input name="medical_history" type="text" style="width: 250px" id="medical_history" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['medical_history']?>" /></td>
  </tr>
  <tr>
    <td>监护医院：<span class="STYLE2">*</span></td>
    <td colspan="3"><select name="guard_hospital" style="width: 252px" id="guard_hospital">
    <option value="<?php echo $registHospital;?>"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['guard_hospital'] == $registHospital) echo 'selected="selected"'?>>本院</option>
    <?php
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
  <tr>
    <td>设备编号：<span class="STYLE2">*</span></td>
    <td><input name="device" type="text" style="width: 80px" id="device" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['device']?>" /></td>
    <td>开单医生：<span class="STYLE2">*</span></td>
    <td><input name="doctor" type="text" style="width: 80px" id="doctor" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['doctor']?>" /></td>
  </tr>
  <tr>
    <td>监护模式：<span class="STYLE2">*</span></td>
    <td width="83">
    <select name="mode" style="width: 80px" id="mode">
      <option value="1" <?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['mode'] != '1') {echo '';} else {echo 'selected="selected"';}?>>实时监护模式 </option>
      <option value="2"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['mode'] == '2') echo 'selected="selected"'?>>异常监护模式 </option>
      <option value="3"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['mode'] == '3') echo 'selected="selected"'?>>单次测量模式 </option>
     </select></td>
    <td>监护时长：<span class="STYLE2">*</span></td>
    <td>
    <select name="hours" style="width: 80px" id="hours">
      <option value="24" <?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['hours'] != '24') {echo '';} else {echo 'selected="selected"';}?>>24小时</option>
      <option value="48"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['hours'] == '48') echo 'selected="selected"'?>>48小时</option>
      <option value="0"<?php if(isset($_SESSION['guardian']) && $_SESSION['guardian']['hours'] == '0') echo 'selected="selected"'?>>单次无时长 </option>
     </select>
    </td>
  </tr>
  <tr>
    <td>病区/住址：</td>
    <td colspan="3"><input name="sickroom" type="text" style="width: 250px" id="address" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['sickroom']?>" /></td>
  </tr>
  <tr>
    <td>身高(cm)：</td>
    <td><input name="height" type="text" style="width: 80px" id="height" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['height']?>" /></td>
    <td>体重(kg)：</td>
    <td><input name="weight" type="text" style="width: 80px" id="weight" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['weight']?>" /></td>
  </tr>
  <tr>
    <td>血压(?/?)：</td>
    <td><input name="blood_pressure" type="text" style="width: 80px" id="height" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['blood_pressure']?>" /></td>
    <td>亲属电话：</td>
    <td><input name="family_tel" type="text" style="width: 80px" id="family_tel" value="<?php if(isset($_SESSION['guardian'])) echo $_SESSION['guardian']['family_tel']?>" /></td>
   <tr>
    <td colspan="4"><div align="center">
        <input name="param" type="submit" value="设置监护参数" onclick="params()" />
        <input name="add" type="submit" value="注册" onclick="regist()">
        <input name="reset" type="submit" value="清空" onclick="clearAll()">
    </div></td>
    </tr>
</table>
</form> 
<?php include_js_file();?>
</body>
</html>