<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';

$hospitalId = $_GET['hospital'];
if (empty($hospitalId)) {
    echo '医院参数没有传输，请联系管理员。';
    exit;
}
$consultations = Dbi::getDbi()->getConsultationRequest($hospitalId);
if (VALUE_DB_ERROR === $consultations) {
    echo '读取数据失败，请重试或联系管理员。';
    exit;
}
if (empty($consultations)) {
    echo '没有会诊请求。';
    exit;
}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>会诊请求</title>
</head>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0" style='font-size:15px;'>
<div style='height:172px; overflow:auto;'>
<table height='50' border='0' style='width:100%; margin:0;font-size:15px;' >
  <tr bgcolor='#666666'>
    <td style='display:none;'>医院号</td>
    <td style='display:none;'>会诊号</td>
    <td style='display:none;'>监护号</td>
    <td width='100px'>请求医院</td>
    <td width='100px'>请求时间</td>
    <td width='200px'>会诊原因</td>
  </tr>
<?php
foreach ($consultations as $index => $row) {
    if ($index % 2 == 0) {
        $color = '#E5E5E5';
    } else {
        $color = '#ADD8E6';
    }
    echo "<tr bgcolor=$color>
        <td style='display:none;'>$hospitalId</td>
        <td style='display:none;'>" . $row['consultation_id'] . "</td>
        <td style='display:none;'>" . $row['guardian_id'] . "</td>
        <td><div align='center'>" . $row['hospital_name'] . "</div></td>
        <td><div align='center'>" . $row['request_time'] . "</div></td>
        <td><div align='center'>" . $row['request_message'] . "</div></td>
        </tr>";
}
?>
</table>
</div>
<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
$(function(){
var rgb;
    $("tr").dblclick(function sendURL(){
        var hosnumber = $(this).children('td').eq(0).text();
        var cid = $(this).children('td').eq(1).text();
        var pid = $(this).children('td').eq(2).text();
        hosnumber=$.trim(hosnumber);
        cid=$.trim(cid);
        pid=$.trim(pid);
        window.location= "handle_consultation.php?hospital="+ hosnumber+"&consultation="+cid+"&guardian="+pid;
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