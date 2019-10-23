<?php
header("Content-Type:text/html;charset=utf-8");
require './config/config.php';
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'XML.php';

if (!isset($_GET['entry']) || empty($_GET['entry'])) {
    echo '';
    exit;
}

$file = PATH_ROOT . 'logic/' . $_GET['entry'] . '.php';
if (!file_exists($file)) {
    echo '';
    exit;
}

foreach ($_GET as $key => $value) {
    $_GET[$key] = trim($value);
}
foreach ($_POST as $key => $value) {
    $_POST[$key] = trim($value);
}

$params = array_merge($_GET, $_POST);
Logger::write('all_params.log', var_export($params, true));

include $file;

function api_exit(array $ret)
{
    $ret = XML::createXml($ret);
    echo $ret;
    exit;
}

function api_exit_download($resultCode, $originalFilePath, 
        $ftpFilePath, $ftpUser, $ftpPassword, array $ftpFiles, $resutInfo)
{
    $ret = XML::createXmlForDownload($resultCode, $originalFilePath, 
            $ftpFilePath, $ftpUser, $ftpPassword, $ftpFiles, $resutInfo);
    echo $ret;
    exit;
}

function api_exit_download_pdf($resultCode, $pdfFilePath, $ftpUser, $ftpPassword, $fileName, $resutInfo)
{
    $ret = XML::createXmlForDownloadPDF($resultCode, $pdfFilePath, $ftpUser, $ftpPassword, $fileName, $resutInfo);
    echo $ret;
    exit;
}
