<?php
require_once PATH_ROOT . 'lib/tool/HpSession.php';
require_once PATH_ROOT . 'lib/util/HpValidate.php';

class BaseLogicApi
{
    protected $retSuccess;
    protected $param = array();
    
    protected function validate($class)
    {
        $queryString = "access class[$class] with GET : ";
        foreach ($_GET as $key => $value) {
            $this->param[$key] = trim($value);
            $queryString .= "$key => $value, ";
        }
        $queryString = substr($queryString, 0, -2) . "\r\n";
        $queryString .= "access class[$class] with POST : ";
        
        foreach ($_POST as $key => $value) {
            $this->param[$key] = trim($value);
            $queryString .= "$key => $value, ";
        }
        $queryString = substr($queryString, 0, -2);
        
        if (DEBUG_MODE) {
            HpLogger::writeCommonLog($queryString, 'debug.log');
        }
        
        $data = file_get_contents('php://input');
        if (!empty($data)) {
            $this->param['data'] = $data;
        }
        
        return $this->authorize($class);
    }
    
    private function authorize($class)
    {
        $currentAuthorityLevel = HpAuthority::getClassAuthority($class);
        if (AUTHORITY_OTHER === $currentAuthorityLevel) {
            return true;
        }
        
        if (!isset($this->param['sid']) || empty($this->param['sid'])) {
            return HpErrorMessage::getError(ERROR_LOGIN_NO);
        }
        
        $session = new HpSession($this->param['sid']);
        return $session->checkSession($currentAuthorityLevel);
    }
    
    protected function execute()
    {
        return $this->retSuccess;
    }
    
    public function run()
    {
        //$startTime = microtime_float();
        
        $noError = $this->validate();
        if (true === $noError)
        {
            $this->retSuccess = HpErrorMessage::getError(ERROR_SUCCESS);
            $model = $this->execute();
        } else {
            $model = $noError;
        }
        
        echo json_encode($model, JSON_UNESCAPED_UNICODE);
    }
}