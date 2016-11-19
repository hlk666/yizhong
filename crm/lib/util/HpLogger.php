<?php
class HpLogger
{
    public static function writeCommonLog($message, $fileName = 'error.log')
    {
        self::write($fileName, $message);
    }
    
    public static function writeDebugTimeLog($action, $time)
    {
        $message = $action . ' took time : ' . $time;
        $file = 'time.log';
        self::write($file, $message);
    }
    
    public static function writeDebugLog($message, $fileName = 'debug.log')
    {
        self::write($fileName, $message);
    }
    
    private static function write($fileName, $message)
    {
        $handle = fopen(PATH_ROOT . 'log/' . date('Ymd') . $fileName, 'a');
        if ($handle == false) {
            return;
        }
        $data = '(' . self::getIP() . ')' . date('H:i:s') . '--' . $message . "\r\n";
        fwrite($handle, $data);
        fclose($handle);
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