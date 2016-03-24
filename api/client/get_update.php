<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['current_version'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'current_version.']);
}
$currentVersion = $_GET['current_version'];
$result = array();

$versionDir = get_real_file_folder(PATH_UPDATE . 'client');
if (empty($versionDir)) {
    $result['version'] = $currentVersion;
    api_exit($result);
}

$versionDir = $versionDir[0];
if (version_compare($versionDir, $currentVersion) > 0) {
    $dir = PATH_UPDATE . 'client' . DIRECTORY_SEPARATOR . $versionDir;
    
    $result['version'] = $versionDir;
    $result['files'] = get_real_file_folder($dir);
    api_exit($result);
} else {
    $result['version'] = $currentVersion;
    api_exit($result);
}

function get_real_file_folder($dir)
{
    if (!file_exists($dir)) {
        return array();
    }
    $files = scandir($dir);
    $ret = array();
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $ret[] = $file;
        }
    }
    return $ret;
}