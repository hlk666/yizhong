<?php
date_default_timezone_set('PRC');
	$hospital = $_GET["hospital"];
	$eid= $_GET['eid'] ;
	$p_id = $_GET["id"];
if ($_POST['submit1']){
		$hx = $_POST['hx'];
		$hospitalId = $_POST['hospitalId'];
		include ("../libraries/conn.php");
		$sql = "INSERT INTO `remote_ecg`.`consultation` (c_id, askhos, answerhos, station, e_id, time, contents, p_id)VALUES ('', '$hospital', '$hospitalId', '1', '$eid', CURRENT_TIMESTAMP, '$hx', '$p_id')";	
		mysql_query($sql) or die ("Query Failed No.4:".mysql_error());
		mysql_close($conn);
		echo "会诊请求发送成功！";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>诊断报告</title>
<style type="text/css">
<!--
.STYLE3 {
       border: 1px solid #808080;
       background-color: #B0E2FF;
}
.STYLE4 {
	font-family: "宋体";
	font-weight: bold;
}
.STYLE5 {font-size: 14px}
.Tab{ border-collapse:collapse; width:300px; height:300px;}
.Tab td{ border:solid 1px #0000EE}
-->
</style>
</head>
<body>
<form name="" action="" method="post">
<table  cellspacing="0" class="Tab"  align="center">
  <tr bgcolor="#4F94CD">
    <td height="30" colspan="2"><strong>&nbsp;&nbsp;医院列表</strong></td>
  </tr>
  <tr>
    <td height="178" colspan="2">
	<?php
function _PAGEFT($total, $displaypg = 20, $url = '') {
   global $page, $firstcount, $pagenav, $_SERVER;
   $GLOBALS["displaypg"] = $displaypg;
   if (!$page)
		{
		$page = 1;
		}
	if($page<=1)    //防止为负数的情况下引起的错误。
		{
		$page = 1;
		}
    if (!$url) {
		$url = $_SERVER["REQUEST_URL"];
		}
	$id = $_GET["id"];
		//URL分析：
	$parse_url = parse_url($url);
	$url_query = $parse_url["query"]; //单独取出URL的查询字串
		if ($url_query) {
			$url_query = preg_replace("(^|&)page=$page", "", $url_query);
			$url = str_replace($parse_url["query"], $url_query, $url);
			if ($url_query)
				$url .= "id=$id&page";
			else
				$url .= "id=$id&page";
		} else {
			$url .= "?id=$id&page";
		}
		$lastpg = ceil($total / $displaypg); //最后页，也是总页数
		$page = $_GET["page"];
		
		$page = min($lastpg, $page);
		if($page<=1)    //防止为负数的情况下引起的错误。
			$page = 1;
		$prepg = $page -1; //上一页
		$nextpg = ($page == $lastpg ? 0 : $page +1); //下一页
		$firstcount = ($page -1) * $displaypg;

		//开始分页导航条代码：
		$pagenav = "共 $total 条记录";

		//如果只有一页则跳出函数：
		if ($lastpg <= 1)
			return false;

		$pagenav .= " <a href='$url=1'>首页</a> ";
		if ($prepg)
			$pagenav .= " <a href='$url=$prepg'>前页</a> ";
		else
			$pagenav .= " 前页 ";
		if ($nextpg)
			$pagenav .= " <a href='$url=$nextpg'>后页</a> ";
		else
			$pagenav .= " 后页 ";
		$pagenav .= " <a href='$url=$lastpg'>尾页</a> ";

		//下拉跳转列表，循环列出所有页码：
		
	}
	include ("../libraries/conn.php");
	include ("../libraries/connUsersData.php");
	include ("../libraries/htmtocode.php");						
	$result=mysql_query("SELECT * FROM friend WHERE wo = '$hospital' ");
	$total=mysql_num_rows($result);							
   //调用pageft()，每页显示10条信息（使用默认的20时，可以省略此参数），使用本页URL（默认，所以省略掉）。
	_PAGEFT($total,6);
	echo $pagenav;
								
	$result=mysql_query("SELECT * FROM friend WHERE wo = '$hospital' order by f_id  ");
   echo" <div style='height:185px; overflow:auto;'>";
    echo"<table width='100%' border='0' style='width:100%; margin:0;font-size:12px;' >
	 <tr bgcolor='#666666'><th width='20'>&nbsp;&nbsp;</th>
	 <th width='210'>医院名称</th>
	 </tr>";
	$i = 1;
	 while($row=mysql_fetch_array($result)){
	  if ($i % 2 == 0){
       $color='#E5E5E5';
		 }
	else{
		 $color='';
		 } 
		 $i += 1;
		 $ni = $row[ni];
		 $rs = mysql_query("SELECT * FROM hospital WHERE hospitalId = '$ni'");
		 $rowl = mysql_fetch_array($rs);
	echo"<tr bgcolor=$color>
    <td><div align='center' ><input type='radio' name='hospitalId' value=$row[ni]></div></td>
    <td><div align='center' >$rowl[hospitalName]</div></td>
    </tr>";	
	}
	echo"</table>";
	echo"</div>";
	mysql_close($conn);				
 ?>  
	</td>
  </tr>
  <tr bgcolor="#4F94CD">
    <td height="30" colspan="2"><span class="STYLE4">&nbsp;&nbsp;会诊请求说明</span></td>
  </tr>
  <tr class="STYLE3">
    <td height="50" colspan="2" align="center"><textarea name="hx" id="hx" cols="50" rows="5"></textarea></td>
  </tr>
  <tr class="STYLE3">
    <td colspan="2"  align="center" >
      <input type="submit" name="submit1" value="提交会诊"  onclick='checkPost()' style="width:100px"/> 
	  &nbsp;&nbsp; <input type="reset" name="submit2" value="重  置"  style="width:100px"/> </td>
  </tr>
</table>
 </form>
</body>
</html>
