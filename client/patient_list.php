<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'function.php';

session_start();
if (false == checkLogin()) {
    echo "您尚未登录!";
    exit;
}

if (!isset($_GET["id"])) {
    //echo "错误的访问。";
    //exit;
}
$hospital = $_GET["id"];

$flag = isset($_GET['current_flag']) ? $_GET['current_flag'] : '0';
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户列表</title>
<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
    $(function(){
        var rgb;
        $("tr").dblclick(function sendURL(){
            var text = $(this).children('td').eq(0).text();
            var Psn = $(this).children('td').eq(1).text();
            var Pname = $(this).children('td').eq(2).text();
            var sex = $(this).children('td').eq(5).text();
            var age = $(this).children('td').eq(6).text();
            var hosNum = $(this).children('td').eq(7).text();
            var shebei = $(this).children('td').eq(8).text();
            var quyu = $(this).children('td').eq(12).text();
            text=$.trim(text);
            Psn=$.trim(Psn);
            Pname=$.trim(Pname);
            sex=$.trim(sex);
            age=$.trim(age);
            hosNum=$.trim(hosNum);
            shebei=$.trim(shebei);
            quyu=$.trim(quyu);
            
            window.lily.onCall(text,hosNum,Pname,sex,age,Psn,shebei,quyu,0);
        });
        $("tr").mouseover(function(){
            rgb = $(this).css('background-color');
            $(this).css({
            'backgroundColor':'#5fafcd',
            'color':'#fff'
            });
        });
       $("tr").mouseout(function(){
           $(this).css({
           'backgroundColor':rgb,
           'color':'#000'
           });
        });
    })
</script>
</head>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<?php
    $total = Dbi::getDbi()->getRecordCount('guardian', 'regist_hospital_id = ' .$hospital);
    $total = 21;
    if ($total == 0) {
        echo "当前无用户。";
        exit;
    }
    $rows = 10;
    $page = isset($_GET['page']) ? $_GET['page'] : null;
    $ret = getPaging($total, $rows, $_SERVER['REQUEST_URI'], $page);
    $offset = $ret['offset'];
    $navigation = $ret['navigation'];
    echo $navigation;
    
    $result=mysql_query("SELECT * FROM `patient_basic_info` WHERE hospitalNumber = '$hospital' order by P_id DESC limit $firstcount,$displaypg ");
    echo"<table style='font-size:14px;' border='0' cellpadding='0' bgcolor='#A3C7DF'>
    <tr bgcolor='#ECEADB' style='height:30px' align='center'>
      <td style='display:none;'>序号</td>
      <td>用户编码</td>
      <td>姓名</td>
      <td>监护设置</td>
      <td>监护状态</td>
      <td>性别</td>
      <td>年龄</td>
      <td>联系电话</td>
      <td>设备号</td>
      <td>开始时间</td>
      <td>结束时间</td>
      <td>申请医生</td>
      <td>病区</td>
    </tr>";
    $i = 1;
    $pname;
    while($row=mysql_fetch_array($result))
    {
       if ($i % 2 == 0){
         $color='#C7E5FF';
         }else{
         $color='#EBF5FF';
         } $i += 1;
       if($row[guardianship]==0)
        {
          $Pstate = '新注册';
        }
        else if($row[guardianship]==1)
        {
          $Pstate = '正在监护';
        }
        else
        {
          $Pstate = '结束监护';
        }
    echo "<tr bgcolor=$color style='height:25px'>
    <td style='display:none;'>
    ".$row[p_id]."
    </td>
    <td>
    <div align='center' style='width:150px'>".$row[p_sn]."</div>
    </td>
    <td>
    <div align='center' style='width:68px'>".$row[p_name]."</div>
    </td>
        <td>
    <div align='center' style='width:70px'><a href = './starttime.php?id=$row[p_id]'>".选择."</div>
    </td>
    <td>
    <div align='center' style='width:70px'>".$Pstate."</div>
    </td>
    <td>
    <div align='center' style='width:30px'>".$row[gender]."</div>
    </td>
    <td>
    <div align='center' style='width:30px'>".$row[birthYear]."</div>
    </td>
    <td>
    <div align='center' style='width:100px'>".$row[phone]."</div>
    </td>
    <td>
    <div align='center' style='width:40px'>".$row[healthState]."</div>
    </td>
    <td>
    <div align='center' style='width:200px'>".$row[starttime]."</div>
    </td>
    <td>
    <div align='center' style='width:200px'>".$row[endtime]."</div>
    </td>
    <td>
    <div align='center' style='width:68px'>".$row[Doc_name]."</div>
    </td>
    <td>
    <div align='center' style='width:150px'>".$row[peaceMaker]."</div>
    </td>
    </tr>";
    }
    echo"</table>";
    mysql_close($conn);
    ?>
</body>
</html>