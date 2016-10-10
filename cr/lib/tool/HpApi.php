<?php
define('API_TIMEOUT', 20);

class HpApi
{
    private $_baseUrl;
    public function __construct($baseUrl = URL_ROOT . '/api/')
    {
        $this->_baseUrl = $baseUrl;
    }
    
    public function getString($api, $params = array(), $timeout = API_TIMEOUT)
    {
        return $this->request($api, $params, 'GET', $timeout);
    }
    public function getJson($api, $params = array(), $timeout = API_TIMEOUT)
    {
        $response = $this->request($api, $params, 'GET', $timeout);
        return json_decode($response);
    }
    
    public function postString($api, array $params, $timeout = API_TIMEOUT)
    {
        return $this->request($api, $param, 'POST', $timeout);
    }
    public function postJson($api, array $params, $timeout = API_TIMEOUT)
    {
        $response = $this->request($api, $params, 'POST', $timeout);
        return json_decode($response);
    }
    
    private function request($api, array $params = array(), $method = 'GET', $timeout = API_TIMEOUT) {
        if (empty($api)) {
            HpLogger::writeCommonLog('Api name is empty.');
            return false;
        }
        
        if (DEBUG_MODE) {
            $startTime = microtime_float();
        }
        
        $url = $this->_baseUrl . $api;
        
        if ('GET' == $method && !empty($params)) {
            $queryString = '';
            foreach ($params as $name => $value) {
                //$name = urlencode($name);
                //$value = urlencode($value);
                $queryString .= "$name=$value&";
            
            }
            $url .= '?' . substr($queryString, 0, -1);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        
        if ('POST' == $method) {
            $queryString = $params;
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        }
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        if (DEBUG_MODE) {
            $time = microtime_float() - $startTime;
            HpLogger::writeDebugLog('Calling api of ' . $api, $time);
        }
        
        return $response;
    }
}
