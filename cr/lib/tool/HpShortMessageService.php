<?php
class HpShortMessageService
{
    private static $url = 'http://http.yunsms.cn/tx/';
    private static $log = 'sms.log';
    
    public static function send($mobile, $content)
    {
        HpLogger::writeCommonLog('tel number:' . $mobile, self::$log);
        $data = array(
            'uid' => SMS_USER,
            'pwd' => strtolower(md5(SMS_PASSWORD)),
            'mobile' => $mobile,
            'content' => mb_convert_encoding($content, 'gbk', 'UTF-8'),
            'time' => '',
            'mid' => ''
        );
        
        /*
         * --------------------------------
         * 状态:
         * 100 发送成功
         * 101 验证失败
         * 102 短信不足
         * 103 操作失败
         * 104 非法字符
         * 105 内容过多
         * 106 号码过多
         * 107 频率过快
         * 108 号码内容空
         * 109 账号冻结
         * 110 禁止频繁单条发送
         * 111 系统暂定发送
         * 112有错误号码
         * 113定时时间不对
         * 114账号被锁，10分钟后登录
         * 115连接失败
         * 116 禁止接口发送
         * 117绑定IP不正确
         * 120 系统升级
         * --------------------------------
         */
        if (self::postSMS($data) == '100') {
            return true;
        } else {
            echo false;
        }
    }
    
    /**
     * post data to url.
     * @param string $url
     * @param array $data
     * @return string
     */
    private static function postSMS($data)
    {
        $row = parse_url(self::$url);
        $host = $row['host'];
        $port = $row['port'] ? $row['port'] : 80;
        $file = $row['path'];
        while(list($k, $v)= each($data)) {
            $post .= rawurlencode($k). '=' . rawurlencode($v). '&'; //转URL标准码
        }
        $post = substr($post, 0, -1);
        $len = strlen($post);
        $fp = @fsockopen($host, $port, $errno, $errstr, 10);
        if (!$fp) {
            return "$errstr ($errno)\n";
        } else {
            $receive = '';
            $out = "POST $file HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n\r\n";
            $out .= $post;
            fwrite($fp, $out);
            while(!feof($fp)) {
                $receive .= fgets($fp, 128);
            }
            fclose($fp);
            HpLogger::writeCommonLog($receive, self::$log);
            $receive = explode("\r\n\r\n", $receive);
            unset($receive[0]);
        
            $result = isset($receive[1]) ? $receive[1] : '';
            $result = explode("\r\n", $result);
            if (isset($result[1])) {
                return $result[1];
            } else {
                return '';
            }
        }
    }
}
