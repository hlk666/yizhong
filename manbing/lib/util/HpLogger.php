<?php
class HpLogger
{
    public static function write($message, $fileName = 'error.log')
    {
        self::writeLog($fileName, $message);
    }
    
    public static function writeDebugTime($action, $time)
    {
        $message = $action . ' took time : ' . $time;
        $file = 'debug_time.log';
        self::write($file, $message);
    }
    
    private static function writeLog($fileName, $message)
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