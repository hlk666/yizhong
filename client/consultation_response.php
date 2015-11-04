<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';

$hospitalId = $_GET['hospital'];
if (empty($hospitalId)) {
    echo '医院参数没有传输，请联系管理员。';
    exit;
}
$consultations = Dbi::getDbi()->getConsultationResponse($hospitalId);
if (VALUE_DB_ERROR == $consultations) {
    echo '读取数据失败，请重试或联系管理员。';
    exit;
}
if (empty($consultations)) {
    echo '没有会诊回复。';
    exit;
}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>会诊记录</title>
</head>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0" style='font-size:15px;'>
<div style='height:172px; overflow:auto;'>
<table height='50' border='0' style='width:100%; margin:0;font-size:15px;' >
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
<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
$(function(){
    var rgb;
    $("tr").dblclick(function sendURL(){
        var ecgpath = $(this).children('td').eq(0).text();
        var cid = $(this).children('td').eq(1).text();
        ecgpath=$.trim(ecgpath);
        cid=$.trim(cid);
        //window.YZ.getpath(ecgpath,cid);
        window.location.href="./end_consultation.php?id="+cid;
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