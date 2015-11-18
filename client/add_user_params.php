<?php
require '../common.php';
include_head('设置监护参数');

session_start();
checkDoctorLogin();

if(isset($_POST['type']) && $_POST['type'] == 'regist'){
    unset($_SESSION['guardian']);
    $_SESSION['guardian'] = $_POST;
}
?>
<style type="text/css">
<!--
.STYLE1 {
    font-family: "新宋体";
    font-weight: bold;
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
<table width="100%" height="100%" border="0" align="center" cellspacing="1" bordercolor="#000000">
<form action="add_user.php" method="post" id="formParam">
  <input type="hidden" name="type" value="save" />
  <tr class="STYLE3">
    <td colspan="2"><span style="color:red;">若删除某参数，系统会按默认值监护。</span></td>
  </tr>
  <tr class="STYLE3">
    <td height="25" colspan="2"><span class="STYLE4"><strong>共通参数(所有监护模式都须设置)：</strong></span></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">心动过速阀值(数字)：</span></td>
    <td width="90"><input name="polycardia" type="text" style="width: 100px" 
    value="<?php if(isset($_SESSION['param'])) {echo $_SESSION['param']['polycardia'];} else {echo 120;}?>" /></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">心动过缓阀值(数字)：</span></td>
    <td width="90"><input name="bradycardia" type="text" style="width: 100px" 
    value="<?php if(isset($_SESSION['param'])) {echo $_SESSION['param']['bradycardia'];} else {echo 50;}?>" /></td>
  </tr>
  <tr class="STYLE3">
    <td><span class="STYLE4">胸导位置：</span></td>
    <td><select name="lead" style="width: 105px" id="lead">
      <option value="1" 
      <?php 
      if(isset($_SESSION['param']) && $_SESSION['param']['lead'] != '1') {
          echo '';
      } else {
          echo 'selected="selected"';
      }?>
      >V1</option>
      <option value="2" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['lead'] == '2') echo 'selected="selected"'?>
      >V2</option>
      <option value="3" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['lead'] == '3') echo 'selected="selected"'?>
      >V3</option>
      <option value="4" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['lead'] == '4') echo 'selected="selected"'?>
      >V4</option>
      <option value="5" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['lead'] == '5') echo 'selected="selected"'?>
      >V5</option>
      <option value="6" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['lead'] == '6') echo 'selected="selected"'?>
      >V6</option>
    </select></td>
  </tr>
  <tr class="STYLE3">
    <td height="25" colspan="2"><span class="STYLE4"><strong>仅单次测量模式的参数：</strong></span></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">单次心电录制时长：</span></td>
    <td width="90"><select name="mode3_record_time" style="width: 105px" id="lead">
      <option value="10" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['mode3_record_time'] == '10') echo 'selected="selected"'?>
      >10秒</option>
      <option value="20" 
      <?php 
      if(isset($_SESSION['param']) && $_SESSION['param']['mode3_record_time'] != '20') {
          echo '';
      } else {
          echo 'selected="selected"';
      }?>
      >20秒</option>
    </select></td>
  </tr>
  <tr class="STYLE3">
    <td height="25" colspan="2"><span class="STYLE4"><strong>仅异常监护模式的参数：</strong></span></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">报警心电录制时长：</span></td>
    <td width="90"><select name="mode2_record_time" style="width: 105px" id="lead">
      <option value="10" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['mode2_record_time'] == '10') echo 'selected="selected"'?>
      >10秒</option>
      <option value="20" 
      <?php 
      if(isset($_SESSION['param']) && $_SESSION['param']['mode2_record_time'] != '20') {
          echo '';
      } else {
          echo 'selected="selected"';
      }?>
      >20秒</option>
    </select></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">定时监护(按时固定发送一次<span style="color:#FF0000;">正常</span>心电数据)：</span></td>
    <td width="90"><select name="regular_time" style="width: 105px" id="lead">
      <option value="0" 
      <?php 
      if(isset($_SESSION['param']) && $_SESSION['param']['regular_time'] != '0') {
          echo '';
      } else {
          echo 'selected="selected"';
      }?>
      >只发报警心电</option>
      <option value="1" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['regular_time'] == '1') echo 'selected="selected"'?>
      >间隔1小时</option>
      <option value="2" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['regular_time'] == '2') echo 'selected="selected"'?>
      >间隔2小时</option>
      <option value="3" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['regular_time'] == '3') echo 'selected="selected"'?>
      >间隔3小时</option>
      <option value="4" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['regular_time'] == '4') echo 'selected="selected"'?>
      >间隔4小时</option>
    </select></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">早搏(个/分钟，超过该值则报警)：</span></td>
    <td width="90"><select name="premature_beat" style="width: 105px" id="lead">
      <option value="1" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['premature_beat'] == '1') echo 'selected="selected"'?>
      >1个/分钟</option>
      <option value="2" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['premature_beat'] == '2') echo 'selected="selected"'?>
      >2个/分钟</option>
      <option value="3" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['premature_beat'] == '3') echo 'selected="selected"'?>
      >3个/分钟</option>
      <option value="4" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['premature_beat'] == '4') echo 'selected="selected"'?>
      >4个/分钟</option>
      <option value="5" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['premature_beat'] == '5') echo 'selected="selected"'?>
      >5个/分钟</option>
      <option value="6" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['premature_beat'] == '6') echo 'selected="selected"'?>
      >6个/分钟</option>
      <option value="7" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['premature_beat'] == '7') echo 'selected="selected"'?>
      >7个/分钟</option>
      <option value="8" 
      <?php 
      if(isset($_SESSION['param']) && $_SESSION['param']['premature_beat'] != '8') {
          echo '';
      } else {
          echo 'selected="selected"';
      }?>
      >8个/分钟</option>
      <option value="9" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['premature_beat'] == '9') echo 'selected="selected"'?>
      >9个/分钟</option>
      <option value="10" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['premature_beat'] == '10') echo 'selected="selected"'?>
      >10个/分钟</option>
    </select></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">其他异常监护(关闭则只监护过速和过缓)：</span></td>
    <td width="90"><select name="arrhythmia" style="width: 100px" id="lead">
      <option value="1" 
      <?php 
      if(isset($_SESSION['param']) && $_SESSION['param']['arrhythmia'] != '1') {
          echo '';
      } else {
          echo 'selected="selected"';
      }?>
      >打开</option>
      <option value="2" 
      <?php if(isset($_SESSION['param']) && $_SESSION['param']['arrhythmia'] == '2') echo 'selected="selected"'?>
      >关闭</option>
    </select></td>
  </tr>
  <tr class="STYLE3">
    <td height="30" colspan="4"><div align="center"><input name="param" type="submit" value="返回注册页面" /></div></td>
  </tr>
</form> 
</table>
</body>
</html>