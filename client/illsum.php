<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';
$guardianId = $_GET['id'];
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>病情总结</title>
</head>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<div align="center">
<table width='100%' style='font-size:14px;' border='0' cellpadding='0' bgcolor='#A3C7DF' >
<tr bgcolor='#ECEADB' style='height:30px' align='center'><td>病情总结</td></tr>
<?php
$result = Dbi::getDbi()->getGuardianResult($guardianId);
// foreach ($result as $row) {
//     echo"<tr align='center' style='height:25px'><td>$row</td></tr>";
// }
echo"<tr align='center' style='height:25px'><td>$result</td></tr>";
echo "</table><input type='button' name='edit' value='修改病情总结' 
    onclick=\"javascript:location.href='edit_result.php?id=$guardianId'\" />";
?>
</div>
<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
$(function(){
    $("tr").dblclick(function sendURL(){
        var pid = <?php echo $guardianId; ?>;
        var text = $(this).children('td').eq(0).text();
        text=$.trim(text);
        window.ill.showHomepage(text);
        window.location= "end_medical.php?id="+ pid;
    })
})
</script>
</body>
</html>