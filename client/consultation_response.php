<?php
require '../common.php';
include_head('会诊结果');

$hospitalId = $_GET['hospital'];
if (empty($hospitalId)) {
    user_goto(MESSAGE_PARAM, GOTO_FLAG_EXIT);
}
$consultations = Dbi::getDbi()->getConsultationResponse($hospitalId);
if (VALUE_DB_ERROR === $consultations) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if (empty($consultations)) {
    user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
}
?>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0" style='font-size:15px;'>
<div style='height:172px; overflow:auto;'>
<table style="height:50px;border:0;width:100%;margin:0;font-size:15px;">
  <tr bgcolor='#666666'>
    <td style='display:none;'>数据路径</td>
    <td style='display:none;'>会诊号</td>
    <td width='25%'>会诊医院</td>
    <td width='25%'>会诊时间</td>
    <td width='50%'>会诊结论</td>
  </tr>
<?php
foreach ($consultations as $index => $row) {
    if ($index % 2 == 0) {
        $color = '#E5E5E5';
    } else {
        $color = '#ADD8E6';
    }
echo "<tr bgcolor=$color>
    <td style='display:none;'>" . $row['data_path'] . "</td>
    <td style='display:none;'>" . $row['consultation_id'] . "</td>
    <td><div align='center'>" . $row['hospital_name'] . "</div></td>
    <td><div align='center'>" . $row['response_time'] . "</div></td>
    <td><div align='center'>" . $row['response_message'] . "</div></td>
    </tr>";
}
?>
</table>
</div>
<?php include_js_file();?>
<script type="text/javascript">
$(function(){
    $("tr").dblclick(function sendURL(){
        var ecgpath = $(this).children('td').eq(0).text();
        var cid = $(this).children('td').eq(1).text();
        ecgpath=$.trim(ecgpath);
        cid=$.trim(cid);
        //window.YZ.getpath(ecgpath,cid);
        window.location.href="./consultation_end.php?id="+cid;
    });
})
</script>
</body>
</html>