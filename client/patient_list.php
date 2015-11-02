<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'function.php';

session_start();
if (false == checkLogin()) {
    echo "您尚未登录!";
    exit;
}

if (!isset($_GET["id"])) {
    //echo "错误的访问。";
    //exit;
}
$hospitalId = $_GET["id"];
$flag = isset($_GET['current_flag']) ? $_GET['current_flag'] : '0';

$total = Dbi::getDbi()->getRecordCount('guardian', 'regist_hospital_id = ' .$hospitalId);
if ($total == 0) {
    echo "当前无用户。";
    exit;
}

$rows = 10;
$page = isset($_GET['page']) ? $_GET['page'] : null;
$ret = getPaging($total, $rows, $_SERVER['REQUEST_URI'], $page);
$offset = $ret['offset'];
$navigation = $ret['navigation'];

$result = Dbi::getDbi()->getPatientList($hospitalId, $offset, $rows);
//@todo add end_time field to guardian table.
var_dump($result);exit;
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户列表</title>
</head>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<?php echo $navigation; ?>
<table style='font-size:14px;' border='0' cellpadding='0' bgcolor='#A3C7DF'>
<tr bgcolor='#ECEADB' style='height:30px' align='center'>
  <td style='display:none;'>序号</td>
  <td>用户编码</td>
  <td>姓名</td>
  <?php if($flag == 1) echo '<td>监护设置</td>'; ?>
  <td>监护状态</td>
  <td>性别</td>
  <td>年龄</td>
  <td>联系电话</td>
  <td>设备号</td>
  <td>开始时间</td>
  <td>结束时间</td>
  <td>申请医生</td>
  <td>病区</td>
</tr>
<?php 
foreach ($result as $index => $row) {
    if ($index % 2 == 0) {
        $color = '#EBF5FF';
    } else {
        $color = '#C7E5FF';
    }
    if ($row['status'] == 0) {
        $statusValue = '新注册';
    } elseif ($row['status'] == 1) {
        $statusValue = '正在监护';
    } elseif ($row['status'] == 2) {
        $statusValue = '结束监护';
    } elseif ($row['status'] == 3) {
        $statusValue = '已诊断(未打印报告)';
    } else {
        $statusValue = '已打印报告';
    }
    $age = date('Y') - $row['birth_year'];
    echo "<tr bgcolor=$color style='height:25px'>
    <td style='display:none;'>".$row['guardian_id']."</td>
    <td><div align='center' style='width:150px'>".$row['patient_id']."</div></td>
    <td><div align='center' style='width:68px'>".$row['patient_name']."</div></td>";
    if ($flag == 1) {
        if ($row['status'] == 0) {
            echo "<td><div align='center' style='width:70px'><a href = './set_guard.php?status=0&id="
                    . $row['guardian_id'] . "'>开始监护</div></td>";
        }
        
        if ($row['status'] == 1) {
            echo "<td><div align='center' style='width:70px'><a href = './set_guard.phpstatus=1&?id="
                    . $row['guardian_id'] . "'>结束监护</div></td>";
        }
    }
    
    echo "<td><div align='center' style='width:70px'>$statusValue</div></td>
    <td><div align='center' style='width:30px'>" . $row['sex'] . "</div></td>
    <td><div align='center' style='width:30px'>$age</div></td>
    <td><div align='center' style='width:100px'>" . $row['tel'] . "</div></td>
    <td><div align='center' style='width:40px'>" . $row['device_id'] . "</div></td>
    <td><div align='center' style='width:200px'>" . $row['start_time'] . "</div></td>
    <td><div align='center' style='width:200px'>" . $row['end_time'] . "</div></td>
    <td><div align='center' style='width:68px'>" . $row['regist_doctor'] . "</div></td>
    <td><div align='center' style='width:150px'>" . $row['sickroom'] . "</div></td></tr>"; 
}
?>
</table>
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
</body>
</html>