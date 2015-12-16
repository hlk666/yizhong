<?php
require '../common.php';

include_head('用户列表');
session_start();
checkDoctorLogin();

if (!isset($_GET['id']) && !isset($_GET['name'])) {
    user_goto(MESSAGE_PARAM, GOTO_FLAG_EXIT);
}
$flag = isset($_GET['current_flag']) ? $_GET['current_flag'] : '1';
if (isset($_GET['id'])) {
    $hospitalId = $_GET['id'];
    $result = Dbi::getDbi()->getPatientList($hospitalId);
    if (VALUE_DB_ERROR === $result) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
    }
    if (empty($result)) {
        user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
    }
    $total = count($result);
    
    $rows = get_rows_by_resolution($_SESSION['height'], 1);
    if (null == $rows) {
        $rows = 7;
        include_once PATH_LIB . 'Logger.php';
        Logger::write('otherLog.txt', 'new resolution : ' . $_SESSION['height']);
    }
    $page = isset($_GET['page']) ? $_GET['page'] : null;
    $ret = getPaging($total, $rows, $_SERVER['REQUEST_URI'], $page);
    $offset = $ret['offset'];
    $navigation = $ret['navigation'];
    
    if ($total > $rows) {
        $result = Dbi::getDbi()->getPatientList($hospitalId, $offset, $rows);
        if (VALUE_DB_ERROR === $result) {
            user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
        }
    }
} else {
    $hospitalId = $_SESSION['hospital'];
    $name = $_GET['name'];
    $result = Dbi::getDbi()->getPatientListCondition($flag, $hospitalId, $name);
    if (VALUE_DB_ERROR === $result) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
    }
    if (empty($result)) {
        user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
    }
    $navigation = '';
}
?>
<style>
tr td {bordercolor:#FFFFFF;}
</style>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<div style="height:20px;margin-top:2px;margin-bottom:2px;"><?php echo $navigation; ?></div>
<table style="font-size:14px;border:1;background-color:#A3C7DF">
<tr bgcolor='#ECEADB' style='height:23px' align='center'>
  <td style='display:none;'>序号</td>
  <td>ID</td>
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
        $statusValue = '已结束监护';
    } elseif ($row['status'] == 3) {
        $statusValue = '已诊断';
    } else {
        $statusValue = '已打印报告';
    }
    $age = date('Y') - $row['birth_year'];
    $sex = $row['sex'] == 1 ? '男' : '女';
    $status = $row['status'];
    echo "<tr bgcolor=$color style='height:22px'>
    <td style='display:none;'>".$row['guardian_id']."</td>
    <td><div align='center' style='width:20px'>".$row['patient_id']."</div></td>
    <td><div align='center' style='width:68px'>".$row['patient_name']."</div></td>";
    if ($flag == 1) {
        if ($status == 0 || $status == 1) {
            echo "<td><div align='center' style='width:70px'><a href = './guardian_action.php?status=$status&id="
                    . $row['guardian_id'] . "'>监护设置</div></td>";
        } else {
            echo "<td><div align='center' style='width:70px'>-</div></td>";
        }
    }
    
    echo "<td><div align='center' style='width:70px'>$statusValue</div></td>
    <td><div align='center' style='width:30px'>$sex</div></td>
    <td><div align='center' style='width:30px'>$age</div></td>
    <td><div align='center' style='width:100px'>" . $row['tel'] . "</div></td>
    <td><div align='center' style='width:45px'>" . $row['device_id'] . "</div></td>
    <td><div align='center' style='width:200px'>" . $row['start_time'] . "</div></td>
    <td><div align='center' style='width:200px'>" . $row['end_time'] . "</div></td>
    <td><div align='center' style='width:68px'>" . $row['regist_doctor_name'] . "</div></td>
    <td><div align='center' style='width:150px'>" . $row['sickroom'] . "</div></td></tr>"; 
}
?>
</table>
<?php include_js_file();?>
<script type="text/javascript">
$(function(){
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
})
</script>
</body>
</html>