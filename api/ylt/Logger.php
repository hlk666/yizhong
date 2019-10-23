<?php
class Logger
{
    public static function write($fileName, $message)
    {
        $data = '(' . self::getIP() . ')' . date('Ymd H:i:s') . '----' . $message . "\r\n";
        $file = PATH_LOG . date('Ymd') . $fileName;
        file_put_contents($file, $data, FILE_APPEND);
    }
    
    public static function writeByHour($fileName, $message)
    {
        $data = '(' . self::getIP() . ')' . date('Ymd H:i:s') . '----' . $message . "\r\n";
        $file = PATH_LOG . date('YmdH') . $fileName;
        file_put_contents($file, $data, FILE_APPEND);
    }
    
    private static function getIP()
    {
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        if(!empty($_SERVER["REMOTE_ADDR"])){
            return $_SERVER["REMOTE_ADDR"];
        }
        return 'unknown IP';
    }
}