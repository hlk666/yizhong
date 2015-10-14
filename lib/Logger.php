<?php
class Logger
{
    public static function write($fileName, $message)
    {
        $data = '(' . self::getIP() . ')' . date('Ymd H:i:s') . '----' . $message . "\r\n";
        
        $handle = fopen(PATH_LOG . $fileName, 'a');
        if ($handle == false) {
            return;
        }
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