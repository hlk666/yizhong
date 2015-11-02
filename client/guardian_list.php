<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'function.php';

session_start();
if (false == checkLogin()) {
    echo "您尚未登录!";
    header('location:doctor.php');
    exit;
}
$hospitalId = $_SESSION["hospital"];

$requestConsultation = Dbi::getDbi()->getRecordCount('consultation', 'status = 1 and response_hospital_id = ' . $hospitalId);
if ($requestConsultation > 0) {
    $noticeConsultation = '有新的会诊请求，请及时查看。<br />';
}
$responseConsultation = Dbi::getDbi()->getRecordCount('consultation', 'status = 2 and request_hospital_id = ' . $hospitalId);
if ($responseConsultation > 0) {
    $noticeConsultation .= '会诊请求已回复，请查看。<br />';
}
if ($noticeConsultation != '') {
    echo $noticeConsultation;
}

$guardians = Dbi::getDbi()->getGuardianList($hospitalId);
if (VALUE_DB_ERROR == $guardians) {
    echo '系统错误，请重试或联系管理员。';
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">   
<html xmlns="http://www.w3.org/1999/xhtml">   
<head>   
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta http-equiv="refresh" content="60">   
<title>监护列表</title>   
</head>
<body >
<table style='font-size:14px;' border='0' cellpadding='0' bgcolor='#A3C7DF' >
  <tr bgcolor='#ECEADB' style='height:30px' align='center'>
    <td style='display:none;'>编号</td>
    <td>姓名</td>
    <td>性别</td>
    <td>年龄</td>
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
    
    $where = ' and read_status = 0 and guardian_id = ' . $guardian['guardian_id'] 
        . ' and create_time >= "' . $guardian['start_time'] . '"';
    if ($guardian['end_time'] != null) {
        $where .= ' and create_time <= "' . $guardian['end_time'] . '"';
    }
    if (true == Dbi::getDbi()->existData('ecg', $where)) {
        $alarmFlag = true;
        $color = '#FF0000';
    }
    $age = date('Y') - $guardian['birth_year'];
    $lead = empty($guardian['lead']) ? '' : 'V' . $guardian['lead'];
    echo "<tr bgcolor='$color' style='height:25px'>
    <td style='display:none;'>" . $guardian['guardian_id'] . "</td>
    <td><div align='center' style='width:80px'>" . $guardian['patient_name'] . "</div></td>
    <td><div align='center' style='width:50px'>" . $guardian['sex'] . "</div></td>
    <td><div align='center' style='width:50px'>$age</div></td>
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
<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
$(function(){
    var rgb;
    $("tr").dblclick(function sendURL(){
        var text = $(this).children('td').eq(0).text();
        var Pname = $(this).children('td').eq(1).text();
        var sex = $(this).children('td').eq(2).text();
        var age = $(this).children('td').eq(3).text();
        var hosNum = $(this).children('td').eq(4).text();
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