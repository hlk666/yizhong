<?php
require '../config/config.php';
require '../lib/DbiAdmin.php';

if (isset($_POST['submit'])) {
    $sql = $_POST['sql'];
    if (!empty($sql)) {
        //only for get data.
        $ret = DbiAdmin::getDbi()->getData($sql);
        var_dump($ret);
    }
} else{
    echo <<<EOF
<html>
<body>
<form method="post">
<input type="text" name="sql" />
<input type="submit" name="submit" />
<form>
</body>
</html>
EOF;
}