<?php
$result = array();

$folders = scandir(PATH_UPDATE . 'client_without_phone');
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
$dir = PATH_UPDATE . 'client_without_phone' . DIRECTORY_SEPARATOR . $versionDir;
$result['version'] = $versionDir;

if (!file_exists($dir)) {
    $result['files'] = [];
} else {
    $files = scandir($dir);
    $tmp = array();
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                $subFiles = scandir($dir . DIRECTORY_SEPARATOR . $file);
                $subTmp = array();
                foreach ($subFiles as $subFile) {
                    if ($subFile != '.' && $subFile != '..') {
                        $time = filemtime($dir . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . $subFile);
                        $subTmp['file'] = URL_ROOT . 'update/client_without_phone/' . $versionDir . '/' . $file . '/' . $subFile;
                        $subTmp['time'] = date('Y-m-d H:i:s', $time);
                        $result['files'][] = $subTmp;
                    }
                }
            } else {
                $time = filemtime($dir . DIRECTORY_SEPARATOR . $file);
                $tmp['file'] = URL_ROOT . 'update/client_without_phone/' . $versionDir . '/' . $file;
                $tmp['time'] = date('Y-m-d H:i:s', $time);
                $result['files'][] = $tmp;
            }
        }
    }
}

api_exit($result);
