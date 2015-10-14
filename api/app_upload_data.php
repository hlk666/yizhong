<?php
require '../config/path.php';
require_once PATH_LIB . 'AppUploadData.php';

$appUpload = new AppUploadData();
$ret = $appUpload->run($_POST);
echo $ret;
