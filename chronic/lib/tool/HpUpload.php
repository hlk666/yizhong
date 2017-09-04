<?php
class HpUpload
{
    public static function uploadImage($data, $name, $suffix)
    {
        if (empty($data)) {
            HpLogger::write('No data uploaded.', 'upload.log');
            return false;
        }
        
        $category = 'upload';
        
        $dir = PATH_ROOT . $category . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        
        if (empty($suffix)) {
            $suffix = 'jpg';
        }
        $fileName = $name . '_' . date('His') . '.' . $suffix;
        
        if (false === file_put_contents($dir . $fileName, $data)) {
            HpLogger::write('Failed to write file : ' . $dir . $fileName, 'upload.log');
            return false;
        }
        
        return $category . '/' . date('Ymd') . '/' . $fileName;
    }
}