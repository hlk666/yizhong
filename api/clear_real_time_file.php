<?php
require_once '../config/path.php';

if (!isset($_GET['id'])) {
    exit;
}

$id = trim($_GET['id']);
if (empty($id)) {
    exit;
}

$file = PATH_REAL_TIME . $id . '\\' . $id . SUFFIX_REAL_TIME_FILE;

$handle = fopen($file, 'w');
if (!$handle) {
    exit;
}
fclose($handle);
