<?php
require '../common.php';
include_head('病情总结');
$guardianId = $_GET['id'];
?>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<div align="center">
<table width='100%' style='font-size:14px;' border='0' cellpadding='0' bgcolor='#A3C7DF' >
<tr bgcolor='#ECEADB' style='height:30px' align='center'><td>病情总结</td></tr>
<?php
$ret = Dbi::getDbi()->getGuardianById($guardianId);
if (VALUE_DB_ERROR === $ret) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
$result = empty($ret) ? '' : $ret['guardian_result'];
// foreach ($result as $row) {
//     echo"<tr align='center' style='height:25px'><td>$row</td></tr>";
// }
echo"<tr align='center' style='height:25px'><td>$result</td></tr>";
echo "</table><input type='button' name='edit' value='修改病情总结' 
    onclick=\"javascript:location.href='guardian_edit_result.php?id=$guardianId'\" />";
?>
</div>
<?php include_js_file();?>
<script type="text/javascript">
$(function(){
    $("tr").dblclick(function sendURL(){
        var pid = <?php echo $guardianId; ?>;
        var text = $(this).children('td').eq(0).text();
        text=$.trim(text);
        window.ill.showHomepage(text);
        window.location= "print_result.php?id="+ pid;
    })
})
</script>
</body>
</html>