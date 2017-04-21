<?php
class HpLogger
{
    public static function write($message, $fileName = 'error.log', $time = LOG_TIME_HOUR)
    {
        self::writeLog($fileName, $message, $time);
    }
    
    public static function writeDebugTimeLog($action, $time)
    {
        $message = $action . ' took time : ' . $time;
        $file = 'time.log';
        self::writeLog($file, $message, LOG_TIME_DAY);
    }
    
    private static function writeLog($fileName, $message, $time)
    {
        if (LOG_TIME_HOUR == $time) {
            $file = PATH_ROOT . 'log/' . date('YmdH') . $fileName;
        } elseif (LOG_TIME_DAY == $time) {
            $file = PATH_ROOT . 'log/' . date('Ymd') . $fileName;
        } else {
            $file = PATH_ROOT . 'log/' . $fileName;
        }
        $handle = fopen($file, 'a');
        if ($handle == false) {
            return;
        }
        $data = '(' . self::getIP() . ')' . date('H:i:s') . "\r\n" . $message . "\r\n\r\n";
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