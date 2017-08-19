<?php
class HpUpload
{
    public static function uploadImage($data, $name, $suffix)
    {
        if (empty($data)) {
            HpLogger::writeCommonLog('No data uploaded.');
            return false;
        }
        
        $category = 'image';
        
        $dir = PATH_ROOT . $category . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        
        if (empty($suffix)) {
            $suffix = 'jpg';
        }
        $fileName = $name . date('His') . '.' . $suffix;
        
        if (false === file_put_contents($dir . $fileName, $data)) {
            HpLogger::writeCommonLog('Failed to write file : ' . $dir . $fileName);
            return false;
        }
        
        return $category . '/' . date('Ymd') . '/' . $fileName;
    }
}