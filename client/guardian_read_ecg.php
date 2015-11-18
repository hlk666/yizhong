<?php
require '../common.php';
include_head('心电已读');

Dbi::getDbi()->flowGuardianReadEcg($_GET['id']);

user_goto(null, GOTO_FLAG_URL, $_SERVER["HTTP_REFERER"]);
?>
</html>