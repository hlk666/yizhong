<?php
require_once PATH_LIB . 'Logger.php';

class ShortMessageService
{
    private static $url = "http://dx.qxtsms.com/sms.aspx?action=send&userid=5031&account=yizhongyiliao&password=05356395321&sendTime=&checkcontent=0";
    private static $log = 'sms.log';
    
    public static function send($mobile, $content)
    {
        Logger::write(self::$log, 'tel number:' . $mobile);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$url . "&mobile=$mobile&content=【羿中医疗】$content");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            Logger::write(self::$log, curl_error($ch));
            return false;
        }
        curl_close($ch);
        
        $xml = simplexml_load_string($response);
        if ($xml->returnstatus == 'Faild') {
            Logger::write(self::$log, $xml->message);
            return false;
        } elseif ($xml->returnstatus == 'Success') {
            Logger::write(self::$log, $xml->message . '.remainpoint:' . $xml->remainpoint);
            return true;
        } else {
            Logger::write(self::$log, 'unexpected return value.');
            return false;
        }
    }
}
