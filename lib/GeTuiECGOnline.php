<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_GETUI . 'IGt.Push.php';

class GeTuiECGOnline
{
    private static $logFile = 'logGeTuiECGOnline.txt';
    private static $expireTime = 43200000;
    private static $host = 'http://sdk.open.api.igexin.com/apiex.htm';
    private static $appKey = 'd4cGFKHHH88D4rvqGO8WH1';
    private static $appID = 'PBUdydkzC57Os240hj2RP3';
    private static $masterSecret = '5EKS2XnlJw5RKegYcyKBJ6';
    
    private static function createTemplate($message)
    {
        $template = new IGtNotificationTemplate();
        $template->set_appId(self::$appID);
        $template->set_appkey(self::$appKey);
        $template->set_transmissionType(1);
        $template->set_transmissionContent('deault message.');
        $template->set_title('烟台羿中医疗');
        $template->set_text($message);
        $template->set_logo('http://wwww.igetui.com/logo.png');
        $template->set_isRing(true);
        $template->set_isVibrate(true);
        $template->set_isClearable(true);
    
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
        $message->set_PushNetWorkType(0);
        return $message;
    }
    
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
            Logger::write(self::$logFile, 'clientID is required.');
            return false;
        }
        $igt = new IGeTui(self::$host, self::$appKey, self::$masterSecret, false);
        $message = self::createMessage('single', self::createTemplate($text));
        
        $target = new IGtTarget();
        $target->set_appId(self::$appID);
        $target->set_clientId($clientId);
        
        try {
            $rep = $igt->pushMessageToSingle($message, $target);
            $ret = self::checkResponse($rep);
            if (true === $ret) {
                Logger::write(self::$logFile, date('Y-m-d H:i:s') . ' success.');
                return true;
            } else {
                Logger::write(self::$logFile, date('Y-m-d H:i:s') . ' fail.');
                return false;
            }
        } catch(RequestException $e) {
            Logger::write(self::$logFile, $e->getMessage());
            return false;
        }
    }
    
    public static function pushToList(array $list, $text)
    {
        if (empty($list)) {
            Logger::write(self::$logFile, 'list of clientID is required.');
            return false;
        }
        foreach ($list as $clientId) {
            if (empty($clientId)) {
                Logger::write(self::$logFile, 'clientID is required.');
                return false;
            }
        }
        putenv("gexin_pushList_needDetails=true");
        putenv("gexin_pushList_needAsync=true");
        
        $igt = new IGeTui(self::$host, self::$appKey, self::$masterSecret, false);
        $message = self::createMessage('list', self::createTemplate($text));
        $contentId = $igt->getContentId($message);
        
        $targetList = array();
        foreach ($list as $clientId) {
            $target = new IGtTarget();
            $target->set_appId(self::$appID);
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
