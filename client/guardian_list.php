<?php
require '../common.php';
include_head('监护列表');

session_start();

checkDoctorLogin();
$consultationFlag = 0;
$consultationMsg = '';
$hospitalId = $_SESSION["hospital"];

if (true === Dbi::getDbi()->existedRequestConsultation($hospitalId)) {
    $consultationFlag = 1;
    $consultationMsg .= '有新的会诊请求';
    
}
if (true === Dbi::getDbi()->existedResponseConsultation($hospitalId)) {
    if ($consultationFlag == 1) {
        $consultationMsg .= '和会诊回复';
    } else {
        $consultationMsg .= '有新的会诊回复';
        $consultationFlag = 1;
    }
}
if ($consultationFlag == 1) {
    $consultationMsg .= '，请及时查看。';
    echo '<div style="height:20px;margin-top:2px;margin-bottom:2px;"><font color="red">' 
            . $consultationMsg . '</font></div>';
}

$guardians = Dbi::getDbi()->getGuardianList($hospitalId);
if (VALUE_DB_ERROR === $guardians) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if (empty($guardians)) {
    user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
}
$total = count($guardians);

$rows = get_rows_by_resolution($_SESSION['height'], 1, $consultationFlag);
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
    $guardians = Dbi::getDbi()->getGuardianList($hospitalId, $offset, $rows);
    if (VALUE_DB_ERROR === $result) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
    }
}
?>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<?php
$noticeEndGuard = '';
$alarmFlag = false;
$sum = 0;
$table = '<div style="height:20px;margin-top:3px;margin-bottom:3px;">' . $navigation . '</div>
<table style="font-size:14px;border:0;background-color:#A3C7DF;">
  <tr bgcolor="#ECEADB" style="height:23px" align="center">
    <td style="display:none;">编号</td>
    <td>姓名</td>
    <td>性别</td>
    <td>年龄</td>
    <td>远程查房</td>
    <td>联系电话</td>
    <td>血压</td>
    <td>初步诊断</td>
    <td>病史</td>
    <td>胸导联</td>
    <td>所属医院</td>
    <td>病区</td>
  </tr>';
foreach ($guardians as $index => $guardian) {
    if ($index % 2 == 0) {
        $color = '#C7E5FF';
    } else {
        $color = '#EBF5FF';
    }
    
    if ($guardian['status'] == 2) {
        $sum++;
        if ($sum < 4) {
            $noticeEndGuard .= '<font color="red">['
                    . $guardian['patient_name'] . ']</font>已监护结束，请为其诊断并作出总结。<br>';
        }
    }
    
    if (true === Dbi::getDbi()->existedEcgNotRead($guardian['guardian_id'])) {
        $alarmFlag = true;
        $color = '#FF0000';
    }
    $id = $guardian['guardian_id'];
    $age = date('Y') - $guardian['birth_year'];
    $sex = $guardian['sex'] == 1 ? '男' : '女';
    $lead = empty($guardian['lead']) ? '' : 'V' . $guardian['lead'];
    $table .= "<tr bgcolor='$color' style='height:22px'>
    <td style='display:none;'>$id</td>
    <td><div align='center' style='width:80px'>" . $guardian['patient_name'] . "</div></td>
    <td><div align='center' style='width:40px'>$sex</div></td>
    <td><div align='center' style='width:40px'>$age</div></td>
    <td><div align='center' style='width:80px'><a href='guardian_remote_check_info.php?id=$id'>发送命令</a></div></td>
    <td><div align='center' style='width:100px'>" . $guardian['tel'] . "</div></td>
    <td><div align='center' style='width:50px'>" . $guardian['blood_pressure'] . "</div></td>
    <td><div align='center' style='width:200px'>". $guardian['tentative_diagnose'] . "</div></td>
    <td><div align='center' style='width:200px'>". $guardian['medical_history'] . "</div></td>
    <td><div align='center' style='width:50px'>$lead</div></td>
    <td><div align='center' style='width:200px'>" . $guardian['hospital_name'] . "</div></td>
    <td><div align='center' style='width:200px'>". $guardian['sickroom'] . "</div></td>
    </tr>";
}
if ($noticeEndGuard != '') {
    echo $noticeEndGuard;
}
echo $table;
?>
</table>
<?php
if ($alarmFlag) {
    echo "<script language='javascript'>window.lily.playmusic(1);</script>";
}
?>
<?php include_js_file();?>
<script type="text/javascript">
$(function(){
    $("tr").dblclick(function sendURL(){
        var text = $(this).children('td').eq(0).text();
        var Pname = $(this).children('td').eq(1).text();
        var sex = $(this).children('td').eq(2).text();
        var age = $(this).children('td').eq(3).text();
        var hosNum = $(this).children('td').eq(5).text();
        var Psn = $(this).children('td').eq(0).text();
        var shebei = <?php echo $hospitalId; ?>;
        var quyu = 0;
        text=$.trim(text);
        Pname=$.trim(Pname);
        sex=$.trim(sex);
        age=$.trim(age);
        hosNum=$.trim(hosNum);
        
        window.lily.onCall(text,hosNum,Pname,sex,age,Psn,shebei,quyu,1);
    });
})
</script>
</body>
</html>