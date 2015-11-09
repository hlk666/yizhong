<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';

$guardianId = $_GET["id"];
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>诊断记录</title>

</head>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<table width='100%' style='font-size:14px;' border='0' cellpadding='0' bgcolor='#A3C7DF' >
<tr bgcolor='#ECEADB' style='height:30px' align='center'>
<td width='30%'>报警时间</td>
<td width='60%'>诊断结论</td>
<td width='10%'>诊断医生</td>
</tr>
<?php
$result = Dbi::getDbi()->getDiagnosisByGuardian($guardianId);
foreach ($result as $index => $row) {
    if ($index % 2 == 0) {
        $color='#EBF5FF';
    } else {
        $color='#C7E5FF';
    }
    
    $eid = $row['ecg_id'];
    $content = trim($row['content']);
    $doctorName = $row['doctor_name'];
    $alertTime = $row['alert_time'];
    $dataPath = $row['data_path'];
    echo"<tr bgcolor=$color align='center' style='height:25px'>
    <td><div align='center' style='width:150px'>$alertTime</div></td>
    <td><div style='width:150px'>$content</div></td>
    <td><div style='width:80px'>$doctorName</div></td>
    <td style='display:none;'>$dataPath</div></td>
    </tr>";
}
?>
</table>
<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
$(function(){
    var rgb;
    $("tr").dblclick(function sendURL(){
    var url = $(this).children('td').eq(3).text();
        var dio = $(this).children('td').eq(1).text();
        var time = $(this).children('td').eq(0).text();
        url=$.trim(url);
        dio=$.trim(dio);
        time=$.trim(time);
        window.ecg.ShowECG(url,dio,time);
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