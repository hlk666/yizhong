<?php
require 'common.php';

$data = DbiAdmin::getDbi()->getRelationChild();
if (VALUE_DB_ERROR === $data) {
    echo 'db error';
    exit;
}

$path = PATH_DATA . 'hospital_child' . DIRECTORY_SEPARATOR;
clearFloder($path);

foreach ($data as $item) {
    $id = $item['parent_hospital_id'];
    $child = $item['child'];
    $childList = explode(',', $child);
    foreach ($childList as $childId) {
        $newChild = isParent($childId, $data);
        if (false !== $newChild) {
            $child .= ',' . $newChild;
        }
    }
    
    $ret = DbiAdmin::getDbi()->getRelationChildName($child);
    if (VALUE_DB_ERROR === $ret) {
        echo 'db error';
        exit;
    }
    $file = $path . $id . '.txt';
    $text = '';
    foreach ($ret as $hos) {
        $text .= $hos['hospital_id'] . ',' . $hos['hospital_name'] . ';';
    }
    $text = substr($text, 0, -1);
    file_put_contents($file, $text);
}

$ret = DbiAdmin::getDbi()->getRelation();
if (VALUE_DB_ERROR === $ret) {
    echo 'db error';
    exit;
}

$path = PATH_DATA . 'relation' . DIRECTORY_SEPARATOR;
clearFloder($path);

foreach ($ret as $item) {
    if (!empty($item)) {
        $file = $path . $item['h1'] . '.txt';
        if (!empty($item['h4'])) {
            $txt = $item['h4'] . '/' . $item['h3'] . '/' . $item['h2'] . '/' . $item['h1'];
        } elseif (!empty($item['h3'])) {
            $txt = $item['h3'] . '/' . $item['h3'] . '/' . $item['h2'] . '/' . $item['h1'];
        } else {
            $txt = $item['h2'] . '/' . $item['h2'] . '/' . $item['h2'] . '/' . $item['h1'];
        }
        file_put_contents($file, $txt);
    }
    
}

function isParent($id, array $data)
{
    foreach ($data as $item) {
        if ($id == $item['parent_hospital_id']) {
            return $item['child'];
        }
    }
    return false;
}
function clearFloder($path)
{
    $fileList = scandir($path);
    foreach ($fileList as $f) {
        if ($f != '.' && $f != '..') {
            @unlink($path . $f);
        }
    }
}
