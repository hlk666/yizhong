<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddUser extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['login_name', 'real_name', 'password', 'type', 'tel', 'hospital_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkRange = HpValidate::checkRange(['type'], $this->param, ['0', '1', '2']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        $ret = isset($this->param['tel']) ? HpValidate::checkPhoneNo($this->param['tel']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_PHONE);
        }
        
        $ret = Dbi::getDbi()->existedUser($this->param['login_name']);
        if (true === $ret) {
            return HpErrorMessage::getError(ERROR_USER_NAME_USED);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $session = new HpSession($this->param['sid']);
        if ($session->getSessionType() > $this->param['type']) {
            return HpErrorMessage::getError(ERROR_NO_PERMISSON);
        }
        $ret = Dbi::getDbi()->addUser($this->param['login_name'], $this->param['real_name'], md5($this->param['password']), 
                $this->param['type'], $this->param['tel'], $this->param['hospital_id']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
