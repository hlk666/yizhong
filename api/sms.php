<?php
require PATH_LIB . 'ShortMessageService.php';

$mobile = $_GET['id'];
$content = $_GET['rx'];
$ret = ShortMessageService::send($mobile, $content);
if (true === $ret) {
    echo 'success.';
} else {
    echo 'failed.';
}
