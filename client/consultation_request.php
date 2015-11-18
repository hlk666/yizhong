<?php
require '../common.php';
include_head('会诊请求列表');

$hospitalId = $_GET['hospital'];
if (empty($hospitalId)) {
    user_goto(MESSAGE_PARAM, GOTO_FLAG_EXIT);
}
$consultations = Dbi::getDbi()->getConsultationRequest($hospitalId);
if (VALUE_DB_ERROR === $consultations) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if (empty($consultations)) {
    user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
}
?>
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
<?php include_js_file();?>
<script type="text/javascript">
$(function(){
    $("tr").dblclick(function sendURL(){
        var hosnumber = $(this).children('td').eq(0).text();
        var cid = $(this).children('td').eq(1).text();
        var pid = $(this).children('td').eq(2).text();
        hosnumber=$.trim(hosnumber);
        cid=$.trim(cid);
        pid=$.trim(pid);
        window.location= "consultation_accept.php?hospital="+ hosnumber+"&consultation="+cid+"&guardian="+pid;
    });
})
</script> 
</body>
</html>