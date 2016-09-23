<?php
define('SESSION_TIME', 14400);

class HpSession
{
    private $_file = null;
    private $_type = null;
    private $_lastTime = null;
    
    public function __construct($sid = '')
    {
        $sessFile = PATH_ROOT . 'session' . DIRECTORY_SEPARATOR . $sid . '.php';
        
        if (empty($sid) || !file_exists($sessFile)) {
            return;
        }
        
        $this->_file = $sessFile;
        include $sessFile;
        
        if (isset($sessType) && isset($sessTime)) {
            $this->_type = $sessType;
            $this->_lastTime = $sessTime;
        }
    }
    
    public function checkSession($currentAuthority)
    {
        if (null === $this->_type) {
            return HpErrorMessage::getError(ERROR_LOGIN_NO);
        }
        
        if ($currentAuthority < $this->_type) {
            return HpErrorMessage::getError(ERROR_NO_PERMISSON);
        }
        
        if (time() > $this->_lastTime + SESSION_TIME) {
            return HpErrorMessage::getError(ERROR_LOGIN_TIMEOUT);
        }
        
        $this->updateSession();
        return true;
    }
    
    public function createSession($user, $type)
    {
        $sid = md5($user);
        $file = PATH_ROOT . 'session' . DIRECTORY_SEPARATOR . $sid . '.php';
        
        if (false === $this->writeSession($file, $type)) {
            return false;
        } else {
            return $sid;
        }
    }
    
    public function updateSession()
    {
        if (null === $this->_file || null === $this->_type) {
            throw new Exception('Session info not enough when updating session file : ' . $this->_file);
            return;
        }
        
        $this->writeSession($this->_file, $this->_type);
    }
    
    private function writeSession($file, $type)
    {
        $time = time();
        
        $data = "<?php\n";
        $data .= '$sessType = ' . $type. ";\n";
        $data .= '$sessTime = ' . $time. ";\n";
        
        if (false === file_put_contents($file, $data)) {
            HpLogger::writeCommonLog('Failed to update session with file : ' . $file);
            return false;
        } else {
            $this->_file = $file;
            $this->_type = $type;
            $this->_lastTime = $time;
        }
    }
}