<?php
class Logger
{
    public static function write($fileName, $message)
    {
        $data = '(' . self::getIP() . ')' . date('Ymd H:i:s') . '----' . $message . "\r\n";
        $handle = fopen(PATH_LOG . date('Ymd') . $fileName, 'a');
        if ($handle == false) {
            return;
        }
        fwrite($handle, $data);
        fclose($handle);
    }
    
    public static function writeByHour($fileName, $message)
    {
        $data = '(' . self::getIP() . ')' . date('Ymd H:i:s') . '----' . $message . "\r\n";
        $handle = fopen(PATH_LOG . date('YmdH') . $fileName, 'a');
        if ($handle == false) {
            return;
        }
        fwrite($handle, $data);
        fclose($handle);
    }
    
    public static function writeBatch($fileName, $message)
    {
        $data = date('Ymd H:i:s') . '----' . $message . "\r\n";
        $handle = fopen(PATH_LOG . date('Ymd') . $fileName, 'a');
        if ($handle == false) {
            return;
        }
        fwrite($handle, $data);
        fclose($handle);
    }
    
    public static function writeCommonError($message)
    {
        $data = '(' . self::getIP() . ')' . date('Ymd H:i:s') . '----' . $message . "\r\n";
        $handle = fopen(PATH_LOG . date('Ymd') . 'error.txt', 'a');
        if ($handle == false) {
            return;
        }
        fwrite($handle, $data);
        fclose($handle);
    }
    
    public static function writeCommands($fileName, $id, array $cmd)
    {
        if (empty($cmd)) {
            return;
        }
        
        $ip = self::getIP();
        $clientInfo = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'known client';
        $command = "id=$id, client info : $clientInfo.\ncommand info :";
        foreach ($cmd as $key => $value) {
            $command .= $key . ' => ' . $value . ', ';
        }
        $command .= "*****";
        self::write($fileName, $command);
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