<?php
require '../common.php';
include_head('监护列表');

session_start();
checkDoctorLogin();

$hospitalId = $_SESSION["hospital"];
$noticeConsultation = '';
if (true == Dbi::getDbi()->existedRequestConsultation($hospitalId)) {
    $noticeConsultation = '有新的会诊请求，请及时查看。<br />';
}
if (true == Dbi::getDbi()->existedResponseConsultation($hospitalId)) {
    $noticeConsultation .= '会诊请求已回复，请查看。<br />';
}
if ($noticeConsultation != '') {
    echo $noticeConsultation;
}

$guardians = Dbi::getDbi()->getGuardianList($hospitalId);
if (VALUE_DB_ERROR === $guardians) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if (empty($guardians)) {
    user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
}
?>
<body >
<table style="font-size:14px;border:0;background-color:#A3C7DF;">
  <tr bgcolor='#ECEADB' style='height:30px' align='center'>
    <td style='display:none;'>编号</td>
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
  </tr>
<?php
$noticeEndGuard = '';
$alarmFlag = false;
foreach ($guardians as $index => $guardian) {
    if ($index % 2 == 0) {
        $color = '#C7E5FF';
    } else {
        $color = '#EBF5FF';
    }
    
    if ($guardian['status'] == 2) {
        $noticeEndGuard .= '<p style="color:red">[' 
            . $guardian['patient_name'] . ']<p>已监护结束，请及时为其诊断并作出病情总结。<br />';
    }
    
    if (true == Dbi::getDbi()->existedEcgNotRead($guardian['guardian_id'])) {
        $alarmFlag = true;
        $color = '#FF0000';
    }
    $id = $guardian['guardian_id'];
    $age = date('Y') - $guardian['birth_year'];
    $sex = $guardian['sex'] == 1 ? '男' : '女';
    $lead = empty($guardian['lead']) ? '' : 'V' . $guardian['lead'];
    echo "<tr bgcolor='$color' style='height:25px'>
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
?>
</table>
<?php
if ($noticeEndGuard != '') {
    echo $noticeEndGuard;
}

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