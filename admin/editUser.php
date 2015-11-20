<?php
	session_start();
	if($_SESSION["isLogin"]!= true || $_SESSION["loginType"] != 1){
		header("location:../index.php");
		exit;
	}
	$jobNo = $_SESSION["loginId"];
	$hospital = $_SESSION["hospital"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>健康档案</title>
<style type="text/css">
<!--
.STYLE1 {
	font-family: "新宋体";
	font-weight: bold;
	font-size: 16px;
}
.STYLE2 {
	color: #EE0000;
	font-size: 16px;
}
.STYLE3 {
                border: 1px solid #808080;
				background-color: #B0E2FF;
}
.STYLE4 {font-size: 16px}
.STYLE6 {font-size: 16px; font-weight: bold; }
-->
</style>
</head>

<body>
<div >
<script language="javascript">
function CheckPost() {
	
	if (myform.devPhone.value == "") {
		alert("设备编号不能为空！");
		myform.devPhone.focus();
		return false;
	}
	if (myform.uid.value == "") {
		alert("居民信息编码不能为空！");
		myform.uid.focus();
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
	if (myform.name.value.length < 2) {
		alert("输入的姓名太短");
		myform.name.focus();
		return false;
	}
	if (myform.phone.value == "") {
		alert("电话号码不能为空！");
		myform.phone.focus();
		return false;
	}	
	if (myform.docNo.value == "") {
		alert("登记医生不能为空！");
		myform.docNo.focus();
		return false;
	}
	if (myform.birthYear.value == "") {
		alert("请选择出生年份！");
		myform.birthYear.focus();
		return false;
	}
}
</script>
<?php
$id = $_GET['id'];
include ("../libraries/conn.php");
$sql = "SELECT * FROM patient_basic_info LEFT JOIN patient_advanced_info ON
patient_basic_info.p_id = patient_advanced_info.p_id LEFT JOIN patient_health_info
ON patient_basic_info.p_id = patient_health_info.p_id LEFT JOIN monitor_record
ON patient_basic_info.p_id = monitor_record.p_id WHERE patient_basic_info.p_id = $id";
$result = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($result);
$oldLicNo = $row[idCard];					
$uid = $row[p_sn];
$o_guardianship = $row[guardianship];
$o_province = $row[province];
$o_city = $row[city];
$o_area = $row[area];
//$devPhone = $row[terminalPhone];
$sql1 = "SELECT * FROM relative WHERE p_id = '$id'";
$rs1 = mysql_query($sql1) or die(mysql_error());
$rowl = mysql_fetch_array($rs1);
$o_re1_id = $rowl['r_id'];
$o_re1 = $rowl['r_name'];
$o_rePhone1 = $rowl['r_phone'];
?>                
<?php
  if($_POST['Button1']){
	include ("../libraries/conn.php");
	$name = $_POST["name"];
	$birthYear = $_POST["birthYear"];
	$gender = $_POST["gender"];
	$licNo = $_POST["licNo"];
	$docNo = $_POST["docNo"];
	$phone = $_POST["phone"];
	$height = $_POST["height"];
	$weight = $_POST["weight"];
	$blood = $_POST["blood"];
	$pacemaker = $_POST["pacemaker"];
	$allergy = $_POST["allergy"];
	$province = $_POST["province"];
	$city = $_POST["city"];
	$area = $_POST["area"];
	$street = $_POST["street"];
	$relative1 = $_POST["relative1"];
	$rePhone1 = $_POST["rePhone1"];
	$surgery = $_POST["surgery"];
	$hospitalization = $_POST["hospitalization"];
	$chronic = $_POST["chronic"];
	$genetic = $_POST["genetic"];
	$status = $_POST["status"];
	$devPhone = $_POST["terminalPhone"];
	$career = $_POST["career"];
	$marriage = $_POST["marriage"];
	$pri_dia = $_POST['pri_dia'];
	$parea = $_POST['parea'];
	
	$sql = "REPLACE INTO `remote_ecg`.`patient_basic_info` (p_id, p_sn, guardianship,gender, birthYear, phone, peaceMaker, healthState, hospitalNumber, p_name)
	VALUES ('$id', '$uid', '$o_guardianship','$gender', '$birthYear', '$phone', '$parea','$status', '$hospital', '$name')";	
	mysql_query($sql) or die('用户基本信息添加错误: '.mysql_error());
	$sql = "REPLACE INTO `remote_ecg`.`patient_advanced_info` (p_id, marriage, idCard, career, province, city, area, street)
	VALUES ('$id', '$marriage', '$licNo', '$career', '$province', '$city', '$area', '$street')";
	mysql_query($sql) or die('用户详细信息添加错误: '.mysql_error());

	$sql = "REPLACE INTO `remote_ecg`.`patient_health_info` (p_id, bloodType, DBP, SBP, hypoxemia, height, weight, primartDiagnosis, allergyHistory, operationHistory, hospitalHistory, chronicDisease, geneticDisease, terminalPhone)
	VALUES ('$id', '$blood', '$rePhone2', '$bp1', '$pacemaker', '$height', '$weight', '$pri_dia', '$allergy', '$surgery', '$hospitalization', '$chronic', '$genetic', '$devPhone')";
	mysql_query($sql) or die('用户健康信息添加错误: '.mysql_error());
	
	$sql = "REPLACE INTO relative (r_id, p_id, r_name, r_phone) VALUES ('$o_re1_id', '$id', '$relative1', '$rePhone1')";
	mysql_query($sql) or die('亲属添加错误: '.mysql_error());
	mysql_close($conn);
  	echo "<script language='javascript'> 
			alert('用户修改成功！');
			this.location.href='./editUser.php?id=$id'
		</script>";
 }
?>
<script language="javascript" src="../libraries/PCASClass.js"></script>
<table width="627" height="609" border="0" align="center" cellspacing="1" bordercolor="#000000">
  <tr>
    <td height="40" colspan="4" bgcolor="#4F94CD"><span class="STYLE1">&nbsp;&nbsp;健康档案</span></td>
  </tr>
  <form action="" method="post" id="myform" onSubmit="return CheckPost()">
  <tr class="STYLE3">
    <td width="129" height="25"><span class="STYLE4">用户信息编码：<span class="STYLE2">*</span></span></td>
    <td width="202"><?php echo $row[p_sn];?></td>
    <td colspan="2"><span class="STYLE2">注意：用户信息编码一旦确定不可更改</span></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">姓名：<span class="STYLE2">*</span></span></td>
    <td><input name="name" type="text" style="width: 120px" id="name" value="<?php echo $row[p_name];?>"/></td>
    <td width="108"><span class="STYLE4">性别：<span class="STYLE2">*</span></span></td>
    <td width="170">
	<select name="gender" style="width: 50px" id="gender">
	                      <option value="<?php echo $row[gender];?>" selected="selected"><?php echo $row[gender];?> </option>
				          <option value="男">男 </option>
				          <option value="女">女 </option>
     </select>	</td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">出生年月：<span class="STYLE2">*</span></span></td>
    <td><select name="birthYear" style="width: 125px">
	                      <option value="<?php echo $row[birthYear];?>" selected="selected"><?php echo $row[birthYear];?></option>
				          <option value="1940">1940</option>
				          <option value="1941">1941</option>
				          <option value="1942">1942</option>
				          <option value="1943">1943</option>
				          <option value="1944">1944</option>
				          <option value="1945">1945</option>
				          <option value="1946">1946</option>
				          <option value="1947">1947</option>
				          <option value="1948">1948</option>
				          <option value="1949">1949</option>
				          <option value="1950">1950</option>
				          <option value="1951">1951</option>
				          <option value="1952">1952</option>
				          <option value="1953">1953</option>
				          <option value="1954">1954</option>
				          <option value="1955">1955</option>
				          <option value="1956">1956</option>
				          <option value="1957">1957</option>
				          <option value="1958">1958</option>
				          <option value="1959">1959</option>
				          <option value="1960">1960</option>
				          <option value="1961">1961</option>
				          <option value="1962">1962</option>
				          <option value="1963">1963</option>
				          <option value="1964">1964</option>
				          <option value="1965">1965</option>
				          <option value="1966">1966</option>
				          <option value="1967">1967</option>
				          <option value="1968">1968</option>
				          <option value="1969">1969</option>
				          <option value="1970">1970</option>
				          <option value="1971">1971</option>
				          <option value="1972">1972</option>
				          <option value="1973">1973</option>
				          <option value="1974">1974</option>
				          <option value="1975">1975</option>
				          <option value="1976">1976</option>
				          <option value="1977">1977</option>
				          <option value="1978">1978</option>
				          <option value="1979">1979</option>
				          <option value="1980">1980</option>
				          <option value="1981">1981</option>
				          <option value="1982">1982</option>
				          <option value="1983">1983</option>
				          <option value="1984">1984</option>
				          <option value="1985">1985</option>
				          <option value="1986">1986</option>
				          <option value="1987">1987</option>
				          <option value="1988">1988</option>
				          <option value="1989">1989</option>
				          <option value="1990">1990</option>
				          <option value="1991">1991</option>
				          <option value="1992">1992</option>
				          <option value="1993">1993</option>
				          <option value="1994">1994</option>
				          <option value="1995">1995</option>
				          <option value="1996">1996</option>
				          <option value="1997">1997</option>
				          <option value="1998">1998</option>
				          <option value="1999">1999</option>
				          <option value="2000">2000</option>
				          <option value="2001">2001</option>
				          <option value="2002">2002</option>
				          <option value="2003">2003</option>
				          <option value="2004">2004</option>
				          <option value="2005">2005</option>
				          <option value="2006">2006</option>
				          <option value="2007">2007</option>
				          <option value="2008">2008</option>
				          <option value="2009">2009</option>
				          <option value="2010">2010</option>
				          <option value="2011">2011</option>
						  <option value="2012">2003</option>
				          <option value="2013">2004</option>
				          <option value="2014">2005</option>
				          <option value="2015">2006</option>
				          <option value="2016">2007</option>
				          <option value="2017">2008</option>
				          <option value="2018">2009</option>
				          <option value="2019">2010</option>
				          <option value="2020">2011</option>
						  <option value="1901">1901</option>
				          <option value="1902">1902</option>
				          <option value="1903">1903</option>
				          <option value="1904">1904</option>
				          <option value="1905">1905</option>
				          <option value="1906">1906</option>
				          <option value="1907">1907</option>
				          <option value="1908">1908</option>
				          <option value="1909">1909</option>
				          <option value="1910">1910</option>
				          <option value="1911">1911</option>
				          <option value="1912">1912</option>
				          <option value="1913">1913</option>
				          <option value="1914">1914</option>
				          <option value="1915">1915</option>
				          <option value="1916">1916</option>
				          <option value="1917">1917</option>
				          <option value="1918">1918</option>
				          <option value="1919">1919</option>
				          <option value="1920">1920</option>
				          <option value="1921">1921</option>
				          <option value="1922">1922</option>
				          <option value="1923">1923</option>
				          <option value="1924">1924</option>
				          <option value="1925">1925</option>
				          <option value="1926">1926</option>
				          <option value="1927">1927</option>
				          <option value="1928">1928</option>
				          <option value="1929">1929</option>
				          <option value="1930">1930</option>
				          <option value="1931">1931</option>
				          <option value="1932">1932</option>
				          <option value="1933">1933</option>
				          <option value="1934">1934</option>
				          <option value="1935">1935</option>
				          <option value="1936">1936</option>
				          <option value="1937">1937</option>
				          <option value="1938">1938</option>
				          <option value="1939">1939</option>
                      </select>    </td>
    <td><span class="STYLE4">血型：</span></td>
    <td><select name="blood" style="width: 50px">
	                      <option value="<?php echo $row[bloodType];?>" selected="selected"><?php echo $row[bloodType];?></option>
                          <option value="A">A</option>
				          <option value="B">B</option>
				          <option value="AB">AB</option>
				          <option value="O">O</option>
				          <option value="其他">其他</option>
				          <option value="未知">未知</option>
                      </select>	</td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">身高(CM)：</span></td>
    <td><input name="height" type="text" style="width: 120px" id="height" value="<?php echo $row[height];?>"/></td>
    <td><span class="STYLE4">体重(Kg)：</span></td>
    <td><input name="weight" type="text" style="width: 50px" id="weight" value="<?php echo $row[weight];?>"/></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">职业：</span></td>
    <td><input name="career" type="text" style="width: 120px" id="career" value="<?php echo $row[career];?>"/></td>
    <td><span class="STYLE4">胸导联位置：<span class="STYLE2">*</span></span></td>
    <td><select name="marriage" style="width: 50px" id="marriage">
	    <option value="<?php echo $row[marriage];?>" selected="selected">
		<?php 
		      if ($row[marriage] == 1) echo "V1"; 
		      if ($row[marriage] == 2) echo "V2"; 
			  if ($row[marriage] == 3) echo "V3";
			  if ($row[marriage] == 4) echo "V4"; 
			  if ($row[marriage] == 5) echo "V5"; 
			  if ($row[marriage] == 6) echo "V6";
		?>
		</option>
						   <option value="1">V1</option>
				          <option value="2">V2</option>
				          <option value="3">V3</option>
						  <option value="4">V4</option>
						  <option value="5">V5</option>
						  <option value="6">V6</option>
    </select></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">联系电话：<span class="STYLE2">*</span></span></td>
    <td><input name="phone" type="text" style="width: 120px" id="phone" value="<?php echo $row[phone];?>"/></td>
    <td><span class="STYLE4">起搏器：</span></td>
    <td><select name="pacemaker" style="width: 65px" id="pacemaker">
                          <option value="<?php echo $row[hypoxemia];?>" selected="selected"><?php if($row[hypoxemia] == 0) echo "未安装"; if($row[hypoxemia] == 1) echo "已安装";?></option>
						  <option value="0">未安装</option>
				          <option value="1">已安装</option>
                      </select>	</td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">身份证号：</span></td>
    <td><input name="licNo" type="text" style="width: 150px" id="licNo" value="<?php echo $row[idCard];?>"/></td>
    <td colspan="2">&nbsp;</td>
    </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">住址：</span></td>
    <td colspan="3">
	  <select name="province" style="width: 50px">
          </select>
            <select name="city" style="width: 50px">
          </select>
            <select name="area" style="width: 50px">
          </select>
          <input name="street" type="text" style="width: 305px" id="address" value="<?php echo $row[street]; ?>" /></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">状态：</span></td>
    <td><select name="status" style="width: 50px" id="status">
	                      <option value="<?php echo $row[healthState];?>" selected="selected"><?php if ($row[healthState] == 1) echo "普通"; if ($row[healthState] == 2) echo "紧急"; ?></option>
				          <option value="1" selected="selected">普通</option>
				          <option value="2">紧急</option>
                      </select>	</td>
    <td><span class="STYLE4">病区：</span></td>
    <td><input name="parea" type="text" style="width: 150px" id="parea" value="<?php echo $row[peaceMaker];?>"/></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">亲属：</span></td>
    <td><input name="relative1" type="text" style="width: 120px" id="relative1" value="<?php echo $o_re1;?>"/></td>
    <td><span class="STYLE4">电话：</span></td>
    <td><input name="rePhone1" type="text" style="width: 120px" id="rePhone1" value="<?php echo $o_rePhone1;?>"/></td>
  </tr>
   <tr class="STYLE3">
    <td height="25"><span class="STYLE4">初步诊断：</span></td>
    <td colspan="3"><input name="pri_dia" type="text" style="width: 470px" id="pri_dia" value="<?php echo $row[primartDiagnosis];?>" /></td>
  </tr>
    <tr class="STYLE3">
    <td height="25"><span class="STYLE4">手术史：</span></td>
    <td colspan="3"><input name="surgery" type="text" style="width: 470px" id="surgery" value="<?php echo $row[operationHistory];?>"/></td>
  </tr>
    <tr class="STYLE3">
    <td height="25"><span class="STYLE4">住院史：</span></td>
    <td colspan="3"> <input name="hospitalization" type="text" style="width: 470px" id="hospitalization" value="<?php echo $row[hospitalHistory];?>"/></td>
  </tr>
    <tr class="STYLE3">
    <td height="25"><span class="STYLE4">慢性病史：</span></td>
    <td colspan="3"><input name="chronic" type="text" style="width: 470px" id="chronic" value="<?php echo $row[chronicDisease];?>"/></td>
   </tr>
    <tr class="STYLE3">
    <td height="25"><span class="STYLE4">遗传病史：</span></td>
    <td colspan="3"> <input name="genetic" type="text" style="width: 470px" id="genetic" value="<?php echo $row[geneticDisease];?>"/></td>
  </tr>
  <tr class="STYLE3">
    <td height="25"><span class="STYLE4">设备编号：<span class="STYLE2">*</span></span></td>
    <td><input name="terminalPhone" type="text" style="width: 150px" id="terminalPhone" value="<?php echo $row[terminalPhone];?>"/></td>
    <td><span class="STYLE4">开单医生：<span class="STYLE2">*</span></span></td>
    <td><?php echo $row[Doc_Na];?></td>
  </tr>
   <tr class="STYLE3">
    <td height="31" colspan="4"><div align="center"><span class="STYLE2">注：*部分请务必填写完整</span></div></td>
  </tr>
   <tr class="STYLE3">
    <td height="61" colspan="4"><div align="center">
        <input name="Button1" type="submit" value="提交" style="width: 120px"/>
    </div></td>
	</form> 
  </tr>
   <tr class="STYLE3">
    <td height="31" colspan="4"><div align="center"><span class="STYLE6">烟台羿中医疗科技有限公司</span></div></td>
  </tr>
</table>
</div>
                                     <script language="javascript" defer>
									  var hProvince, hCity, hArea;
									  hProvince = "<?php echo $o_province;?>";
									  hCity = "<?php echo $o_city;?>";
									  hArea = "<?php echo $o_area;?>";
									  new PCAS("province","city","area",hProvince, hCity, hArea);
                                      </script>
</body>
</html>