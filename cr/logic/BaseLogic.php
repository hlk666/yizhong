<?php
require_once PATH_ROOT . 'lib/tool/HpSession.php';
require_once PATH_ROOT . 'lib/util/HpValidate.php';

class BaseLogic
{
    protected $retSuccess;
    protected $param = array();
    
    protected function validate($class)
    {
        foreach ($_GET as $key => $value) {
            $this->param[$key] = trim($value);
        }
        
        $data = file_get_contents('php://input');
        if (!empty($data)) {
            $this->param['data'] = $data;
        } else {
            foreach ($_POST as $key => $value) {
                $this->param[$key] = trim($value);
            }
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
        return array();
    }
    
    public function run()
    {
        //$startTime = microtime_float();
        
        $noError = $this->validate();
        if (true === $noError)
        {
            $this->retSuccess = HpErrorMessage::getError(ERROR_SUCCESS);
            $ret = $this->execute();
        } else {
            $ret = $noError;
        }
        
        //$endTime = microtime_float();
        //HpLogger::writeDebugLog($this->param['entry'], $endTime - $startTime);
        
        return $ret;
    }
}