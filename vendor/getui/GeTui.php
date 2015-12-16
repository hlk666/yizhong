<?php
require_once dirname(dirname(__DIR__)) . '\\common.php';
require_once PATH_LIB . 'Logger.php';
require 'IGt.Push.php';

define('HOST','http://sdk.open.api.igexin.com/apiex.htm');
define('APPKEY','6UomKSEmgKAa54D8qtJzi3');
define('APPID','DXJRc5JbUK7phUtkz0L9f3');
define('MASTERSECRET','J3XGK1l7a97soPajddmq7A');

define('CID','be2d5d60027913b07d5657eeaaddc4cb');
define('CID_ANOTHER','56bd0c337542dac972a946139adaa684');
define('TEST_MESSAGE','发出的测试信息');
//date('Y-m-d H:i:s') . TEST_MESSAGE

class GeTui
{
    private static $logFile = 'logGeTui.txt';
    private static $expireTime = 43200000;
    
    private static function createTemplate($message)
    {
        $template = new IGtTransmissionTemplate();
        $template->set_appId(APPID);
        $template->set_appkey(APPKEY);
        $template->set_transmissionType(1);
        $template->set_transmissionContent($message);
    
        return $template;
    }
    private static function createMessage($flag, $template)
    {
        if ($flag == 'single') {
            $message = new IGtSingleMessage();
        } elseif ($flag == 'list') {
            $message = new IGtListMessage();
        } else {
            $message = new IGtMessage();
        }
        $message->set_isOffline(true);
        $message->set_offlineExpireTime(self::$expireTime);
        $message->set_data($template);
        //0:only wifi. 1:no limited
        $message->set_PushNetWorkType(0);
        return $message;
    }
    /*
     response of pushToSingle:
     array(3) {
     ["taskId"]=> string(41) "OSS-1212_fcfbdad65edb5218eae299180f60104a"
     ["result"]=> string(2) "ok"
     ["status"]=> string(16) "successed_online" }
     
     response of pushToList:
     array(3) {
     ["result"]=> string(2) "ok"
     ["details"]=> array(2) {
     ["56bd0c337542dac972a946139adaa684"]=> string(16) "successed_online"
     ["be2d5d60027913b07d5657eeaaddc4cb"]=> string(16) "successed_online" }
     ["contentId"]=> string(31) "OSL-1212_DFn9d9VoCg5oaeiaaYIAN7" }
     */
    private static function checkResponse($rep)
    {
        if (isset($rep['result']) && $rep['result'] == 'ok') {
            return true;
        } else {
            if (is_array($rep) && !empty($rep)) {
                $error = '';
                foreach ($rep as $key => $value) {
                    $error .= $key . ' => ' . $value . ',';
                }
                $error = substr($error, 0, -1);
            } else {
                $error = $rep;
            }
            Logger::write(self::$logFile, $error);
            return false;
        }
    }
    
    public static function pushToSingle($clientId, $text)
    {
        if (empty($clientId)) {
            Logger::write(self::$logFile, 'clientID is empty.');
            return false;
        }
        $igt = new IGeTui(HOST, APPKEY, MASTERSECRET, false);
        $message = self::createMessage('single', self::createTemplate($text));
        
        $target = new IGtTarget();
        $target->set_appId(APPID);
        $target->set_clientId($clientId);
        
        try {
            $rep = $igt->pushMessageToSingle($message, $target);
            return self::checkResponse($rep);
        } catch(RequestException $e) {
            Logger::write(self::$logFile, $e->getMessage());
            return false;
        }
    }
    
    public static function pushToList(array $list, $text)
    {
        if (empty($list)) {
            Logger::write(self::$logFile, 'list is empty.');
            return false;
        }
        foreach ($list as $clientId) {
            if (empty($clientId)) {
                Logger::write(self::$logFile, 'clientID is empty.');
                return false;
            }
        }
        putenv("gexin_pushList_needDetails=true");
        putenv("gexin_pushList_needAsync=true");
        
        $igt = new IGeTui(HOST, APPKEY, MASTERSECRET, false);
        $message = self::createMessage('list', self::createTemplate($text));
        $contentId = $igt->getContentId($message);
        
        $targetList = array();
        foreach ($list as $clientId) {
            $target = new IGtTarget();
            $target->set_appId(APPID);
            $target->set_clientId($clientId);
            $targetList[] = $target;
        }
        
        try {
            $rep = $igt->pushMessageToList($contentId, $targetList);
            return self::checkResponse($rep);
        } catch(RequestException $e) {
            Logger::write(self::$logFile, $e->getMessage());
            return false;
        }
    }
}
