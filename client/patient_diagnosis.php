<?php
require '../common.php';
include_head('诊断记录');
$guardianId = $_GET["id"];
?>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<table style="width:100%;font-size:14px;border:0;background-color:#A3C7DF">
<tr bgcolor='#ECEADB' style='height:30px' align='center'>
<td width='20%'>标记</td>
<td width='20%'>报警时间</td>
<td width='50%'>诊断结论</td>
<td width='10%'>医生</td>
</tr>
<?php
$result = Dbi::getDbi()->getDiagnosisByGuardian($guardianId);
if (VALUE_DB_ERROR === $result) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
foreach ($result as $index => $row) {
    if ($index % 2 == 0) {
        $color='#EBF5FF';
    } else {
        $color='#C7E5FF';
    }
    
    $diagnosisId = $row['diagnosis_id'];
    $checked = $row['mark'] == 1 ? "checked='checked'" : '';
    $eid = $row['ecg_id'];
    $content = trim($row['content']);
    $doctorName = $row['doctor_name'];
    $alertTime = $row['alert_time'];
    $dataPath = $row['data_path'];
    echo"<tr bgcolor=$color align='center' style='height:25px'>
    <td><input type='checkbox' name='chk$diagnosisId' onclick='mark(this, $diagnosisId)' $checked /></td>
    <td><div align='center' style='width:150px'>$alertTime</div></td>
    <td><div style='width:150px'>$content</div></td>
    <td><div style='width:80px'>$doctorName</div></td>
    <td style='display:none;'>$dataPath</div></td>
    </tr>";
}
?>
</table>
<?php include_js_file();?>
<script type="text/javascript">
$(function(){
    $("tr").dblclick(function sendURL(){
    var url = $(this).children('td').eq(4).text();
        var dio = $(this).children('td').eq(2).text();
        var time = $(this).children('td').eq(1).text();
        url=$.trim(url);
        dio=$.trim(dio);
        time=$.trim(time);
        window.ecg.ShowECG(url,dio,time);
    });
})
function mark(chk, id) {
    var request = new XMLHttpRequest();
    var url = "<?php echo URL_ROOT . 'client/mark.php?type=d&id='; ?>" + id;
    if (chk.checked) {
        url += "&check=1";
    } else {
        url += "&check=0";
    }
    request.open("GET", url);
    request.send(null);
}
</script>  
</body>
</html>