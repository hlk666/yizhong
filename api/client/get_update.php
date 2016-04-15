<?php
$result = array();

$folders = scandir(PATH_UPDATE . 'client');
$versionDir = '';
foreach ($folders as $folder) {
    if ($folder != '.' && $folder != '..') {
        $versionDir = $folder;
        break;
    }
}
if ('' == $versionDir) {
    $result['version'] = '0.0.0.0';
    api_exit($result);
}

//if (version_compare($versionDir, $currentVersion) > 0) {
$dir = PATH_UPDATE . 'client' . DIRECTORY_SEPARATOR . $versionDir;
$result['version'] = $versionDir;

if (!file_exists($dir)) {
    $result['files'] = [];
} else {
    $files = scandir($dir);
    $tmp = array();
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $time = filemtime($dir . DIRECTORY_SEPARATOR . $file);
            $tmp['file'] = URL_ROOT . 'update/client/' . $versionDir . '/' . $file;
            $tmp['time'] = date('Y-m-d H:i:s', $time);
            $result['files'][] = $tmp;
        }
    }
}

api_exit($result);