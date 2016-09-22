<?php
class BaseLogic
{
    protected $retSuccess;
    protected $param = array();
    
    protected function validate($class)
    {
        foreach ($_GET as $key => $value) {
            $_GET[$key] = trim($value);
        }
        foreach ($_POST as $key => $value) {
            $_POST[$key] = trim($value);
        }
        $this->param = array_merge($_GET, $_POST);
        
        $data = file_get_contents('php://input');
        if (!empty($data)) {
            $this->param['data'] = $data;
        }
        
        return $this->authorize($class);
    }
    
    private function authorize($class)
    {
        $currentAuthorityLevel = HpAuthority::getClassAuthority($class);
        if ($currentAuthorityLevel === AUTHORITY_OTHER) {
            return true;
        }
        
        if (!isset($this->param['sid']) || empty($this->param['sid'])) {
            return HpErrorMessage::getError(ERROR_LOGIN_NO);
        }
        $sessFile = PATH_ROOT . 'session' . DIRECTORY_SEPARATOR . $this->param['sid'] . '.php';
        if (!file_exists($sessFile)) {
            return HpErrorMessage::getError(ERROR_LOGIN_NO);
        }
        
        include $sessFile;
        if ($currentAuthorityLevel < $sessType) {
            return HpErrorMessage::getError(ERROR_NO_PERMISSON);
        }
        if (time() > $sessTime + SESSION_TIME) {
            return HpErrorMessage::getError(ERROR_LOGIN_TIMEOUT);
        }
        
        return true;
    }
    
    protected function execute()
    {
        return array();
    }
    
    public function run()
    {
        $noError = $this->validate();
        if (true === $noError)
        {
            $this->retSuccess = HpErrorMessage::getError(ERROR_SUCCESS);
            return $this->execute();
        }
        return $noError;
    }
}